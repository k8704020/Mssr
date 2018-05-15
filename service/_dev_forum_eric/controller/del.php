<?php
//-------------------------------------------------------
//明日聊書
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",3).'config/config.php');

        //外掛函式檔
        $funcs=array(
            APP_ROOT.'inc/code',
            APP_ROOT.'service/_dev_forum_eric/inc/code',

            APP_ROOT.'lib/php/db/code',
            APP_ROOT.'lib/php/net/code',
            APP_ROOT.'lib/php/array/code'
        );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        $arrys_sess_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //method    函式名稱
    //send_url  返回地址

        $method='';
        if(isset($_POST['method'])&&trim($_POST['method'])!=='')$method=trim($_POST['method']);
        if(isset($_GET['method'])&&trim($_GET['method'])!=='')$method=trim($_GET['method']);

        $send_url='';
        if(isset($_POST['send_url'])&&trim($_POST['send_url'])!=='')$send_url=trim($_POST['send_url']);
        if(isset($_GET['send_url'])&&trim($_GET['send_url'])!=='')$send_url=trim($_GET['send_url']);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //method    函式名稱
    //send_url  返回地址

        if($method==='' || !function_exists($method) || $send_url===''){
            $msg="發生嚴重錯誤";
            $jscript_back="
                <script>
                    alert('{$msg}');
                    location.href='{$send_url}';
                </script>
            ";
            die($jscript_back);
        }

    //---------------------------------------------------
    //呼叫函式
    //---------------------------------------------------

        call_user_func($method,$send_url,$arrys_sess_login_info);

    //---------------------------------------------------
    //函式列表
    //---------------------------------------------------

        //-----------------------------------------------
        //函式: report_group()
        //用途: 檢舉小組
        //-----------------------------------------------

            function report_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: report_group()
            //用途: 檢舉小組
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //user_id
                //group_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'user_id',
                            'group_id'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $user_id =trim($_POST[trim('user_id')]);
                    $group_id=trim($_POST[trim('group_id')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($user_id===''){
                       $arry_err[]='使用者主索引,未輸入!';
                    }else{
                        $user_id=(int)$user_id;
                        if($user_id===0){
                            $arry_err[]='使用者主索引,錯誤!';
                        }
                    }

                    if($group_id===''){
                       $arry_err[]='小組主索引,未輸入!';
                    }else{
                        $group_id=(int)$group_id;
                        if($group_id===0){
                            $arry_err[]='小組主索引,錯誤!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $user_id  =(int)$user_id;
                        $group_id =(int)$group_id;

                    ////-----------------------------------
                    ////擁有小組權限人數
                    ////-----------------------------------
                    //
                    //    $sql="
                    //        SELECT `user`.`member`.`uid`
                    //        FROM `user`.`member`
                    //            INNER JOIN `user`.`permissions` ON
                    //            `user`.`member`.`permission` = `user`.`permissions`.`permission`
                    //        WHERE 1=1
                    //            AND `user`.`permissions`.`status`='u_mssr_forum'
                    //        GROUP BY `user`.`member`.`uid`
                    //    ";
                    //    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    //    $auth_mssr_forum_user_cno=ceil(count($db_results)*0.5);

                    //-----------------------------------
                    //檢核小組檢舉次數
                    //-----------------------------------

                        $sql="
                            SELECT `mssr_forum`.`mssr_forum_group_report_log`.`group_id`
                            FROM `mssr_forum`.`mssr_forum_group_report_log`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_group_report_log`.`group_id`={$group_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $sess_user_id=(int)$sess_user_id;
                    $group_id    =(int)$group_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_group_report_log
                        INSERT IGNORE INTO `mssr_forum`.`mssr_forum_group_report_log` SET
                            `user_id`    = {$sess_user_id},
                            `group_id`   = {$group_id    },
                            `log_id`     = NULL           ,
                            `keyin_mdate`= NULL           ;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                    //if(count($db_results)>=$auth_mssr_forum_user_cno){
                    //    $sql="
                    //        # for mssr_forum_group
                    //        UPDATE `mssr_forum`.`mssr_forum_group` SET
                    //            `group_state` = 2
                    //        WHERE 1=1
                    //            AND `mssr_forum`.`mssr_forum_group`.`group_id`={$group_id}
                    //        LIMIT 1;
                    //    ";
                    //    $conn_mssr->exec($sql);
                    //}

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="檢舉成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: del_group()
        //用途: 關閉小組
        //-----------------------------------------------

            function del_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: del_group()
            //用途: 關閉小組
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //group_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'group_id'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $group_id=trim($_POST[trim('group_id')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($group_id===''){
                       $arry_err[]='小組主索引,未輸入!';
                    }else{
                        $group_id=(int)$group_id;
                        if($group_id===0){
                            $arry_err[]='小組主索引,錯誤!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $group_id =(int)$group_id;

                    //-----------------------------------
                    //檢核小組身分資訊
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_group_user_rev`.`user_type`,
                                `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`
                            FROM `mssr_forum`.`mssr_forum_group`
                                INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                                `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_group`.`group_id`={$group_id}
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`={$sess_user_id}
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_type`<>3
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        if(!empty($db_results)){
                            die('關閉小組失敗');
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $sess_user_id=(int)$sess_user_id;
                    $group_id    =(int)$group_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_group
                        UPDATE `mssr_forum`.`mssr_forum_group` SET
                            `group_state` = 2
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_group`.`group_id`={$group_id}
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="已關閉小組";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: del_friend()
        //用途: 取消好友
        //-----------------------------------------------

            function del_friend($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: del_friend()
            //用途: 取消好友
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //friend_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'friend_id'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $friend_id=trim($_POST[trim('friend_id')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($friend_id===''){
                       $arry_err[]='好友主索引,未輸入!';
                    }else{
                        $friend_id=(int)$friend_id;
                        if($friend_id===0){
                            $arry_err[]='好友主索引,錯誤!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $friend_id =(int)$friend_id;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $sess_user_id=(int)$sess_user_id;
                    $friend_id   =(int)$friend_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_friend
                        UPDATE `mssr_forum`.`mssr_forum_friend` SET
                            `friend_state` = 2
                        WHERE 1=1
                            AND (
                                `mssr_forum`.`mssr_forum_friend`.`user_id`  ={$sess_user_id }
                                OR
                                `mssr_forum`.`mssr_forum_friend`.`friend_id`={$sess_user_id }
                            )
                            AND (
                                `mssr_forum`.`mssr_forum_friend`.`user_id`  ={$friend_id    }
                                OR
                                `mssr_forum`.`mssr_forum_friend`.`friend_id`={$friend_id    }
                            )
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="已取消好友";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: del_reply()
        //用途: 移除回覆
        //-----------------------------------------------

            function del_reply($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: del_reply()
            //用途: 移除回覆
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //reply_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'reply_id'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $reply_id=trim($_POST[trim('reply_id')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($reply_id===''){
                       $arry_err[]='回文主索引,未輸入!';
                    }else{
                        $reply_id=(int)$reply_id;
                        if($reply_id===0){
                            $arry_err[]='回文主索引,錯誤!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $reply_id =(int)$reply_id;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $reply_id =(int)$reply_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_reply
                        UPDATE `mssr_forum`.`mssr_forum_reply` SET
                            `reply_state`   = 2
                        WHERE 1=1
                            AND `reply_id`  = {$reply_id}
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="移除成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: del_article()
        //用途: 移除文章
        //-----------------------------------------------

            function del_article($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: del_article()
            //用途: 移除文章
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //article_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'article_id'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $article_id=trim($_POST[trim('article_id')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($article_id===''){
                       $arry_err[]='文章主索引,未輸入!';
                    }else{
                        $article_id=(int)$article_id;
                        if($article_id===0){
                            $arry_err[]='文章主索引,錯誤!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $article_id =(int)$article_id;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $article_id =(int)$article_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_article
                        UPDATE `mssr_forum`.`mssr_forum_article` SET
                            `article_state`   = 2
                        WHERE 1=1
                            AND `article_id`  = {$article_id}
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="移除成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: del_best_article()
        //用途: 移出精華文
        //-----------------------------------------------

            function del_best_article($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: del_best_article()
            //用途: 移出精華文
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //article_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'article_id'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $article_id=trim($_POST[trim('article_id')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($article_id===''){
                       $arry_err[]='文章主索引,未輸入!';
                    }else{
                        $article_id=(int)$article_id;
                        if($article_id===0){
                            $arry_err[]='文章主索引,錯誤!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $article_id =(int)$article_id;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $article_id =(int)$article_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_best_article_category_rev
                        DELETE FROM `mssr_forum`.`mssr_forum_best_article_category_rev`
                        WHERE 1=1
                            AND `article_id`  = {$article_id};

                        # for mssr_forum_article
                        UPDATE `mssr_forum`.`mssr_forum_article` SET
                            `article_type`   = 1
                        WHERE 1=1
                            AND `article_id`  = {$article_id}
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="移出成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: del_best_article_category()
        //用途: 移除精華區類別
        //-----------------------------------------------

            function del_best_article_category($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: del_best_article_category()
            //用途: 移除精華區類別
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //group_id
                //cat_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'group_id',
                            'cat_id'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $group_id=trim($_POST[trim('group_id')]);
                    $cat_id  =trim($_POST[trim('cat_id  ')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($group_id===''){
                       $arry_err[]='小組主索引,未輸入!';
                    }else{
                        $group_id=(int)$group_id;
                        if($group_id===0){
                            $arry_err[]='小組主索引,錯誤!';
                        }
                    }
                    if($cat_id===''){
                       $arry_err[]='精華區文章類別主索引,未輸入!';
                    }else{
                        $cat_id=(int)$cat_id;
                        if($cat_id===0){
                            $arry_err[]='精華區文章類別主索引,錯誤!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $group_id =(int)$group_id;
                        $cat_id   =(int)$cat_id;

                    //-----------------------------------
                    //檢核所有該精華類別的文章
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_best_article_category_rev`.`article_id`
                            FROM `mssr_forum`.`mssr_forum_best_article_category_rev`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_best_article_category_rev`.`cat_id`={$cat_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        $article_id_ilst='';
                        if(!empty($db_results)){
                            foreach($db_results as $inx=>$db_result){
                                $rs_article_id=(int)$db_result['article_id'];
                                $article_id_ilst.=$rs_article_id;
                                if($inx!==count($db_results)-1)$article_id_ilst.=",";
                            }
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $group_id =(int)$group_id;
                    $cat_id   =(int)$cat_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_best_article_category
                        DELETE FROM `mssr_forum`.`mssr_forum_best_article_category`
                        WHERE 1=1
                            AND `group_id`= {$group_id}
                            AND `cat_id`  = {$cat_id  }
                        LIMIT 1;

                        # for mssr_forum_best_article_category_rev
                        DELETE FROM `mssr_forum`.`mssr_forum_best_article_category_rev`
                        WHERE 1=1
                            AND `cat_id`  = {$cat_id  };
                    ";
                    if($article_id_ilst!==''){
                        $sql.="
                            # for mssr_forum_article
                            UPDATE `mssr_forum`.`mssr_forum_article` SET
                                `article_type`   = 1
                            WHERE 1=1
                                AND `article_id` IN ({$article_id_ilst});
                        ";
                    }
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="移除成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: del_group_booklist()
        //用途: 移出小組書單
        //-----------------------------------------------

            function del_group_booklist($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: del_group_booklist()
            //用途: 移出小組書單
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //group_id
                //book_sid

                    if(!empty($_POST)){
                        $post_chk=array(
                            'group_id',
                            'book_sid'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $group_id=trim($_POST[trim('group_id')]);
                    $book_sid=trim($_POST[trim('book_sid')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($group_id===''){
                       $arry_err[]='小組主索引,未輸入!';
                    }else{
                        $group_id=(int)$group_id;
                        if($group_id===0){
                            $arry_err[]='小組主索引,錯誤!';
                        }
                    }
                    if($book_sid===''){
                       $arry_err[]='書籍識別碼,未輸入!';
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $group_id  =(int)$group_id;
                        $book_sid  =mysql_prep($book_sid);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $group_id  =(int)$group_id;
                    $book_sid  =mysql_prep($book_sid);

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_group_booklist
                        DELETE FROM `mssr_forum`.`mssr_forum_group_booklist`
                        WHERE 1=1
                            AND `group_id`=  {$group_id}
                            AND `book_sid`= '{$book_sid}'
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="移出成功";
                    die($msg);
            }
?>

