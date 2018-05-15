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
            APP_ROOT.'service/dev_forum/inc/code',

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
        //函式: edit_style_group()
        //用途: 更換小組頁面樣式
        //-----------------------------------------------

            function edit_style_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_style_group()
            //用途: 更換小組頁面樣式
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
                //style_id
                //group_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'style_id   ',
                            'group_id   '
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
                    $style_id=trim($_POST[trim('style_id')]);
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

                    if($style_id===''){
                       $arry_err[]='樣式主索引,未輸入!';
                    }else{
                        $style_id=(int)$style_id;
                        if($style_id===0){
                            $arry_err[]='樣式主索引,錯誤!';
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

                        $style_id=(int)$style_id;
                        $group_id=(int)$group_id;

                    //-----------------------------------
                    //檢核樣式
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_style_group_rev`.`style_id`
                            FROM `mssr_forum`.`mssr_forum_style_group_rev`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_style_group_rev`.`group_id`={$group_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $sess_user_id=(int)$sess_user_id;
                    $style_id    =(int)$style_id;
                    $group_id    =(int)$group_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    switch(count($db_results)){

                        case 0:
                            $sql="
                                # for mssr_forum_style_group_rev
                                INSERT INTO `mssr_forum`.`mssr_forum_style_group_rev` SET
                                    `create_by`     ={$sess_user_id },
                                    `group_id`      ={$group_id     },
                                    `style_id`      ={$style_id     },
                                    `style_from`    =1               ,
                                    `keyin_mdate`   =NULL            ;
                            ";
                        break;

                        default:
                            $sql="
                                # for mssr_forum_style_group_rev
                                UPDATE `mssr_forum`.`mssr_forum_style_group_rev` SET
                                    `style_id`  = {$style_id},
                                    `style_from`=1
                                WHERE 1=1
                                    AND `mssr_forum`.`mssr_forum_style_group_rev`.`group_id`={$group_id}
                                LIMIT 1;
                            ";
                        break;

                    }
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="修改成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_style_user()
        //用途: 更換個人頁面樣式
        //-----------------------------------------------

            function edit_style_user($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_style_user()
            //用途: 更換個人頁面樣式
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
                //style_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'style_id   '
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
                    $style_id=trim($_POST[trim('style_id')]);

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

                    if($style_id===''){
                       $arry_err[]='樣式主索引,未輸入!';
                    }else{
                        $style_id=(int)$style_id;
                        if($style_id===0){
                            $arry_err[]='樣式主索引,錯誤!';
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

                        $style_id=(int)$style_id;

                    //-----------------------------------
                    //檢核樣式
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_style_user_rev`.`style_id`
                            FROM `mssr_forum`.`mssr_forum_style_user_rev`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_style_user_rev`.`user_id`={$sess_user_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $sess_user_id=(int)$sess_user_id;
                    $style_id    =(int)$style_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    switch(count($db_results)){

                        case 0:
                            $sql="
                                # for mssr_forum_style_user_rev
                                INSERT INTO `mssr_forum`.`mssr_forum_style_user_rev` SET
                                    `user_id`       ={$sess_user_id },
                                    `style_id`      ={$style_id     },
                                    `style_from`    =1               ,
                                    `keyin_mdate`   =NULL            ;
                            ";
                        break;

                        default:
                            $sql="
                                # for mssr_forum_style_user_rev
                                UPDATE `mssr_forum`.`mssr_forum_style_user_rev` SET
                                    `style_id`  = {$style_id},
                                    `style_from`=1
                                WHERE 1=1
                                    AND `mssr_forum`.`mssr_forum_style_user_rev`.`user_id`={$sess_user_id}
                                LIMIT 1;
                            ";
                        break;

                    }
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="修改成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_group()
        //用途: 修改小組資訊
        //-----------------------------------------------

            function edit_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_group()
            //用途: 修改小組資訊
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
                //group_type
                //group_id
                //group_name
                //group_content
                //group_rule

                    if(!empty($_POST)){
                        $post_chk=array(
                            'group_type   ',
                            'group_id     ',
                            'group_name   ',
                            'group_content',
                            'group_rule   '
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
                    $group_type   =trim($_POST[trim('group_type   ')]);
                    $group_id     =trim($_POST[trim('group_id     ')]);
                    $group_name   =trim($_POST[trim('group_name   ')]);
                    $group_content=trim($_POST[trim('group_content')]);
                    $group_rule   =trim($_POST[trim('group_rule   ')]);

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
                    if($group_type===''){
                        $arry_err[]='小組類型,未輸入!';
                    }else{
                        $group_type=(int)$group_type;
                        if($group_type===0){
                            $arry_err[]='小組類型,錯誤!';
                        }
                    }
                    if($group_name===''){
                       $arry_err[]='小組名稱,未輸入!';
                    }
                    if($group_content===''){
                       $arry_err[]='小組簡介,未輸入!';
                    }
                    if($group_rule===''){
                       $arry_err[]='小組規範,未輸入!';
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

                        $group_type     =(int)$group_type;
                        $group_id       =(int)$group_id  ;
                        $group_name     =mysql_prep($group_name);
                        $group_content  =mysql_prep($group_content);
                        $group_rule     =mysql_prep($group_rule);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $group_type     =(int)$group_type;
                    $group_id       =(int)$group_id  ;
                    $group_name     =mysql_prep(strip_tags($group_name));
                    $group_content  =mysql_prep(strip_tags($group_content));
                    $group_rule     =mysql_prep(strip_tags($group_rule));

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_group
                        UPDATE `mssr_forum`.`mssr_forum_group` SET
                            `group_type`   =  {$group_type   } ,
                            `group_name`   = '{$group_name   }',
                            `group_content`= '{$group_content}',
                            `group_rule`   = '{$group_rule   }'
                        WHERE 1=1
                            AND `group_id` =  {$group_id     }
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="修改成功";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
            }

        //-----------------------------------------------
        //函式: edit_ok_request_rec_us_book()
        //用途: 回覆好友推薦一本書籍給你
        //-----------------------------------------------

            function edit_ok_request_rec_us_book($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_ok_request_rec_us_book()
            //用途: 回覆好友推薦一本書籍給你
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
                //request_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'request_id '
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
                    $request_id=trim($_POST[trim('request_id ')]);

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

                    if($request_id===''){
                       $arry_err[]='邀請主索引,未輸入!';
                    }else{
                        $request_id=(int)$request_id;
                        if($request_id===0){
                            $arry_err[]='邀請主索引,錯誤!';
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

                        $request_id=(int)$request_id;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $request_id=(int)$request_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_user_request
                        UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                            `request_state`  = 1,
                            `request_read`   = 1
                        WHERE 1=1
                            AND `request_id` = {$request_id}
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="回覆成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_request_rec_us_book()
        //用途: 回應好友請求推薦一本書籍給他
        //-----------------------------------------------

            function edit_request_rec_us_book($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_request_rec_us_book()
            //用途: 回應好友請求推薦一本書籍給他
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
                //request_id
                //book_sid
                //request_content

                    if(!empty($_POST)){
                        $post_chk=array(
                            'request_id     ',
                            'book_sid       ',
                            'request_content'
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
                    $request_id     =trim($_POST[trim('request_id     ')]);
                    $book_sid       =trim($_POST[trim('book_sid       ')]);
                    $request_content=trim($_POST[trim('request_content')]);

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

                    if($request_id===''){
                       $arry_err[]='邀請主索引,未輸入!';
                    }else{
                        $request_id=(int)$request_id;
                        if($request_id===0){
                            $arry_err[]='邀請主索引,錯誤!';
                        }
                    }
                    if($book_sid===''){
                       $arry_err[]='書籍識別碼,未輸入!';
                    }
                    if($request_content===''){
                       $arry_err[]='回覆請求原因,未輸入!';
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

                        $request_id     =(int)$request_id;
                        $book_sid       =mysql_prep($book_sid);
                        $request_content=mysql_prep($request_content);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $request_id     =(int)$request_id;
                    $book_sid       =mysql_prep(strip_tags($book_sid));
                    $request_content=mysql_prep(strip_tags($request_content));

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_user_request
                        UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                            `request_state`  = 1,
                            `request_read`   = 2
                        WHERE 1=1
                            AND `request_id` = {$request_id}
                        LIMIT 1;

                        # for mssr_forum_user_request_rec_us_book_rev
                        UPDATE `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev` SET
                            `book_sid`          = '{$book_sid       }',
                            `request_content`   = '{$request_content}'
                        WHERE 1=1
                            AND `request_id` = {$request_id}
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="已回應請求";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_request_friend()
        //用途: 回覆交友邀請
        //-----------------------------------------------

            function edit_request_friend($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_request_friend()
            //用途: 回覆交友邀請
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
                //create_by
                //user_id
                //friend_id
                //friend_state

                    if(!empty($_POST)){
                        $post_chk=array(
                            'create_by   ',
                            'user_id     ',
                            'friend_id   ',
                            'friend_state'
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
                    $create_by   =trim($_POST[trim('create_by   ')]);
                    $user_id     =trim($_POST[trim('user_id     ')]);
                    $friend_id   =trim($_POST[trim('friend_id   ')]);
                    $friend_state=trim($_POST[trim('friend_state')]);

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

                    if($create_by===''){
                       $arry_err[]='建立者主索引,未輸入!';
                    }else{
                        $create_by=(int)$create_by;
                        if($create_by===0){
                            $arry_err[]='建立者主索引,錯誤!';
                        }
                    }
                    if($user_id===''){
                       $arry_err[]='使用者主索引,未輸入!';
                    }else{
                        $user_id=(int)$user_id;
                        if($user_id===0){
                            $arry_err[]='使用者主索引,錯誤!';
                        }
                    }
                    if($friend_id===''){
                       $arry_err[]='好友主索引,未輸入!';
                    }else{
                        $friend_id=(int)$friend_id;
                        if($friend_id===0){
                            $arry_err[]='好友主索引,錯誤!';
                        }
                    }
                    if($friend_state===''){
                       $arry_err[]='交友狀態,未輸入!';
                    }else{
                        $friend_state=(int)$friend_state;
                        if($friend_state===0){
                            $arry_err[]='交友狀態,錯誤!';
                        }else{
                            if(!in_array($friend_state,array(1,2))){
                                $arry_err[]='交友狀態,錯誤!';
                            }
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

                        $create_by   =(int)$create_by;
                        $user_id     =(int)$user_id;
                        $friend_id   =(int)$friend_id;
                        $friend_state=(int)$friend_state;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $create_by   =(int)$create_by;
                    $user_id     =(int)$user_id;
                    $friend_id   =(int)$friend_id;
                    $friend_state=(int)$friend_state;

                //---------------------------------------
                //處理
                //---------------------------------------

                    if($friend_state===1){
                        $sql="
                            # for mssr_forum_friend
                            UPDATE `mssr_forum`.`mssr_forum_friend` SET
                                `friend_state`  = 1
                            WHERE 1=1
                                AND `create_by` = {$create_by}
                                AND `user_id`   = {$user_id  }
                                AND `friend_id` = {$friend_id}
                            ;
                        ";
                        $msg="你們已成為朋友";
                    }else{
                        $sql="
                            # for mssr_forum_friend
                            UPDATE `mssr_forum`.`mssr_forum_friend` SET
                                `friend_state`  = 2
                            WHERE 1=1
                                AND `create_by` = {$create_by}
                                AND `user_id`   = {$user_id  }
                                AND `friend_id` = {$friend_id}
                            ;
                        ";
                        $msg="你已拒絕此交友邀請";
                    }
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_request_join_us_group()
        //用途: 回覆邀請加入小組
        //-----------------------------------------------

            function edit_request_join_us_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_request_join_us_group()
            //用途: 回覆邀請加入小組
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
                //request_id
                //request_state

                    if(!empty($_POST)){
                        $post_chk=array(
                            'request_id     ',
                            'request_state  '
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
                    $request_id   =trim($_POST[trim('request_id ')]);
                    $request_state=trim($_POST[trim('request_state ')]);

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

                    if($request_id===''){
                       $arry_err[]='邀請主索引,未輸入!';
                    }else{
                        $request_id=(int)$request_id;
                        if($request_id===0){
                            $arry_err[]='邀請主索引,錯誤!';
                        }
                    }
                    if($request_state===''){
                       $arry_err[]='邀請狀態,未輸入!';
                    }else{
                        $request_state=(int)$request_state;
                        if($request_state===0){
                            $arry_err[]='邀請狀態,錯誤!';
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

                        $request_id   =(int)$request_id;
                        $request_state=(int)$request_state;

                    //-----------------------------------
                    //檢核版主是誰
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`,
                                `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`,

                                `mssr_forum`.`mssr_forum_group`.`group_state`,

                                `mssr_forum`.`mssr_forum_user_request`.`request_from`
                            FROM `mssr_forum`.`mssr_forum_group`
                                INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                                `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                                INNER JOIN `mssr_forum`.`mssr_forum_user_request_join_us_group_rev` ON
                                `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_user_request_join_us_group_rev`.`group_id`
                                INNER JOIN `mssr_forum`.`mssr_forum_user_request` ON
                                `mssr_forum`.`mssr_forum_user_request_join_us_group_rev`.`request_id`=`mssr_forum`.`mssr_forum_user_request`.`request_id`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_type` IN (2)
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`IN (1)
                                AND `mssr_forum`.`mssr_forum_user_request_join_us_group_rev`.`request_id`={$request_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(empty($db_results)){
                            $msg="發生嚴重錯誤";
                            die($msg);
                        }else{
                            $group_user_type_2_user_id=(int)$db_results[0]['user_id'];
                            $group_id=(int)$db_results[0]['group_id'];
                            $group_state=(int)$db_results[0]['group_state'];
                            $request_from=(int)$db_results[0]['request_from'];
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $request_id   =(int)$request_id;
                    $request_state=(int)$request_state;

                //---------------------------------------
                //處理
                //---------------------------------------

                    if($request_state===2){
                        $sql="
                            # for mssr_forum_user_request
                            UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                `request_state`  = 2,
                                `request_read`   = 1
                            WHERE 1=1
                                AND `request_id` = {$request_id}
                            LIMIT 1;
                        ";
                        //送出
                        $err ='DB QUERY FAIL(1)';
                        $sth=$conn_mssr->prepare($sql);
                        $sth->execute()or die($err);

                        $msg="你已拒絕此邀請";
                    }else{
                        if($request_from===$group_user_type_2_user_id){
                            $sql="
                                # for mssr_forum_user_request
                                UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                    `request_state`  = 1,
                                    `request_read`   = 1
                                WHERE 1=1
                                    AND `request_id` = {$request_id}
                                LIMIT 1;

                                # for mssr_forum_group_user_rev
                                INSERT INTO `mssr_forum`.`mssr_forum_group_user_rev` SET
                                    `edit_by`           = {$sess_user_id    } ,
                                    `group_id`          = {$group_id        } ,
                                    `user_id`           = {$sess_user_id    } ,
                                    `user_type`         =1                    ,
                                    `user_state`        =1                    ,
                                    `user_intro`        =''                   ,
                                    `keyin_cdate`       =NOW()                ,
                                    `keyin_mdate`       =NULL                 ;
                            ";
                        }else{
                            $sql="
                                # for mssr_forum_user_request
                                UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                    `request_state`  = 1,
                                    `request_read`   = 1
                                WHERE 1=1
                                    AND `request_id` = {$request_id}
                                LIMIT 1;

                                # for mssr_forum_group_user_rev
                                INSERT INTO `mssr_forum`.`mssr_forum_group_user_rev` SET
                                    `edit_by`           = {$sess_user_id    } ,
                                    `group_id`          = {$group_id        } ,
                                    `user_id`           = {$sess_user_id    } ,
                                    `user_type`         =1                    ,
                                    `user_state`        =3                    ,
                                    `user_intro`        =''                   ,
                                    `keyin_cdate`       =NOW()                ,
                                    `keyin_mdate`       =NULL                 ;
                            ";
                        }
                        //送出
                        $err ='DB QUERY FAIL(2)';
                        $sth=$conn_mssr->prepare($sql);
                        $sth->execute()or die($err);

                        $msg="已加入小組";
                    }
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_request_join_to_group()
        //用途: 回覆申請加入小組
        //-----------------------------------------------

            function edit_request_join_to_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_request_join_to_group()
            //用途: 回覆申請加入小組
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
                //request_id
                //request_state

                    if(!empty($_POST)){
                        $post_chk=array(
                            'request_id     ',
                            'request_state  '
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
                    $request_id   =trim($_POST[trim('request_id ')]);
                    $request_state=trim($_POST[trim('request_state ')]);

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

                    if($request_id===''){
                       $arry_err[]='邀請主索引,未輸入!';
                    }else{
                        $request_id=(int)$request_id;
                        if($request_id===0){
                            $arry_err[]='邀請主索引,錯誤!';
                        }
                    }
                    if($request_state===''){
                       $arry_err[]='邀請狀態,未輸入!';
                    }else{
                        $request_state=(int)$request_state;
                        if($request_state===0){
                            $arry_err[]='邀請狀態,錯誤!';
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

                        $request_id   =(int)$request_id;
                        $request_state=(int)$request_state;

                    //-----------------------------------
                    //檢核版主是誰
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`,
                                `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`,

                                `mssr_forum`.`mssr_forum_group`.`group_state`,

                                `mssr_forum`.`mssr_forum_user_request`.`request_from`
                            FROM `mssr_forum`.`mssr_forum_group`
                                INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                                `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                                INNER JOIN `mssr_forum`.`mssr_forum_user_request_join_to_group_rev` ON
                                `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_user_request_join_to_group_rev`.`group_id`
                                INNER JOIN `mssr_forum`.`mssr_forum_user_request` ON
                                `mssr_forum`.`mssr_forum_user_request_join_to_group_rev`.`request_id`=`mssr_forum`.`mssr_forum_user_request`.`request_id`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_type` IN (2)
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`IN (1)
                                AND `mssr_forum`.`mssr_forum_user_request_join_to_group_rev`.`request_id`={$request_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(empty($db_results)){
                            $msg="發生嚴重錯誤";
                            die($msg);
                        }else{
                            $group_user_type_2_user_id=(int)$db_results[0]['user_id'];
                            $group_id=(int)$db_results[0]['group_id'];
                            $group_state=(int)$db_results[0]['group_state'];
                            $request_from=(int)$db_results[0]['request_from'];
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $request_id   =(int)$request_id;
                    $request_state=(int)$request_state;

                //---------------------------------------
                //處理
                //---------------------------------------

                    if($request_state===2){
                        $sql="
                            # for mssr_forum_user_request
                            UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                `request_state`  = 2,
                                `request_read`   = 1
                            WHERE 1=1
                                AND `request_id` = {$request_id}
                            LIMIT 1;
                        ";
                        //送出
                        $err ='DB QUERY FAIL(1)';
                        $sth=$conn_mssr->prepare($sql);
                        $sth->execute()or die($err);

                        $msg="你已拒絕此申請";
                    }else{
                        $sql="
                            # for mssr_forum_user_request
                            UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                `request_state`  = 1,
                                `request_read`   = 1
                            WHERE 1=1
                                AND `request_id` = {$request_id}
                            LIMIT 1;

                            # for mssr_forum_group_user_rev
                            INSERT INTO `mssr_forum`.`mssr_forum_group_user_rev` SET
                                `edit_by`           = {$request_from    } ,
                                `group_id`          = {$group_id        } ,
                                `user_id`           = {$request_from    } ,
                                `user_type`         =1                    ,
                                `user_state`        =1                    ,
                                `user_intro`        =''                   ,
                                `keyin_cdate`       =NOW()                ,
                                `keyin_mdate`       =NULL                 ;
                        ";
                        //送出
                        $err ='DB QUERY FAIL(2)';
                        $sth=$conn_mssr->prepare($sql);
                        $sth->execute()or die($err);

                        $msg="已允許加入";
                    }
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_request_create_group()
        //用途: 回覆聯署建立小組
        //-----------------------------------------------

            function edit_request_create_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_request_create_group()
            //用途: 回覆聯署建立小組
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
                //request_id
                //request_state

                    if(!empty($_POST)){
                        $post_chk=array(
                            'request_id     ',
                            'request_state  '
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
                    $request_id   =trim($_POST[trim('request_id ')]);
                    $request_state=trim($_POST[trim('request_state ')]);

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

                    if($request_id===''){
                       $arry_err[]='邀請主索引,未輸入!';
                    }else{
                        $request_id=(int)$request_id;
                        if($request_id===0){
                            $arry_err[]='邀請主索引,錯誤!';
                        }
                    }
                    if($request_state===''){
                       $arry_err[]='邀請狀態,未輸入!';
                    }else{
                        $request_state=(int)$request_state;
                        if($request_state===0){
                            $arry_err[]='邀請狀態,錯誤!';
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

                        $request_id   =(int)$request_id;
                        $request_state=(int)$request_state;

                    //-----------------------------------
                    //檢核版主是誰
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`,
                                `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`,

                                `mssr_forum`.`mssr_forum_group`.`group_state`
                            FROM `mssr_forum`.`mssr_forum_group`
                                INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                                `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                                INNER JOIN `mssr_forum`.`mssr_forum_user_request_create_group_rev` ON
                                `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_user_request_create_group_rev`.`group_id`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_type` IN (2)
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`IN (1)
                                AND `mssr_forum`.`mssr_forum_user_request_create_group_rev`.`request_id`={$request_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(empty($db_results)){
                            $msg="發生嚴重錯誤";
                            die($msg);
                        }else{
                            $group_user_type_2_user_id=(int)$db_results[0]['user_id'];
                            $group_id=(int)$db_results[0]['group_id'];
                            $group_state=(int)$db_results[0]['group_state'];
                        }

                    //-----------------------------------
                    //檢核小組是否已越過聯署門檻
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_user_request`.`request_id`,
                                `mssr_forum`.`mssr_forum_user_request`.`request_state`
                            FROM `mssr_forum`.`mssr_forum_user_request`
                                INNER JOIN `mssr_forum`.`mssr_forum_user_request_create_group_rev` ON
                                `mssr_forum`.`mssr_forum_user_request`.`request_id`=`mssr_forum_user_request_create_group_rev`.`request_id`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_user_request`.`request_from`={$group_user_type_2_user_id}
                                AND `mssr_forum`.`mssr_forum_user_request_create_group_rev`.`group_id`={$group_id}
                                AND `mssr_forum`.`mssr_forum_user_request`.`request_state`=1
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        $group_flag=false;
                        if(count($db_results)>=1){
                            $group_flag=true;
                        }

                    //-----------------------------------
                    //檢核是否已聯署過
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_user_request`.`request_id`,
                                `mssr_forum`.`mssr_forum_user_request`.`request_state`
                            FROM `mssr_forum`.`mssr_forum_user_request`
                                INNER JOIN `mssr_forum`.`mssr_forum_user_request_create_group_rev` ON
                                `mssr_forum`.`mssr_forum_user_request`.`request_id`=`mssr_forum_user_request_create_group_rev`.`request_id`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_user_request`.`request_from`={$group_user_type_2_user_id}
                                AND `mssr_forum`.`mssr_forum_user_request`.`request_to`={$sess_user_id}
                                AND `mssr_forum`.`mssr_forum_user_request_create_group_rev`.`group_id`={$group_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                        if(!empty($db_results)){
                            $rs_request_id   =(int)$db_results[0]['request_id'];
                            $rs_request_state=(int)$db_results[0]['request_state'];

                            if($rs_request_state===1){
                                $msg="你已經聯署過";
                                die($msg);
                            }

                            if($rs_request_state===2){
                                $msg="你已經拒絕聯署過";
                                die($msg);
                            }
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $request_id   =(int)$request_id;
                    $request_state=(int)$request_state;
                    $group_id     =(int)$group_id;
                    $group_user_type_2_user_id=(int)$group_user_type_2_user_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    if($group_state===1){
                        if($request_state===2){
                            $sql="
                                # for mssr_forum_user_request
                                UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                    `request_state`  = 2,
                                    `request_read`   = 1
                                WHERE 1=1
                                    AND `request_id` = {$request_id}
                                LIMIT 1;
                            ";
                            //送出
                            $err ='DB QUERY FAIL(6)';
                            $sth=$conn_mssr->prepare($sql);
                            $sth->execute()or die($err);

                            $msg="你已拒絕連署";
                        }else{
                            $sql="
                                # for mssr_forum_user_request
                                UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                    `request_state`  = 1,
                                    `request_read`   = 1
                                WHERE 1=1
                                    AND `request_id` = {$request_id}
                                LIMIT 1;

                                # for mssr_forum_group_user_rev
                                INSERT INTO `mssr_forum`.`mssr_forum_group_user_rev` SET
                                    `edit_by`           = {$sess_user_id    } ,
                                    `group_id`          = {$group_id        } ,
                                    `user_id`           = {$sess_user_id    } ,
                                    `user_type`         =1                    ,
                                    `user_state`        =1                    ,
                                    `user_intro`        =''                   ,
                                    `keyin_cdate`       =NOW()                ,
                                    `keyin_mdate`       =NULL                 ;
                            ";
                            //送出
                            $err ='DB QUERY FAIL(7)';
                            $sth=$conn_mssr->prepare($sql);
                            $sth->execute()or die($err);

                            $msg="聯屬成功，請耐心等待小組建立";
                        }
                    }else{
                        if($request_state===2){
                            $sql="
                                # for mssr_forum_user_request
                                UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                    `request_state`  = 2,
                                    `request_read`   = 1
                                WHERE 1=1
                                    AND `request_id` = {$request_id}
                                LIMIT 1;
                            ";
                            //送出
                            $err ='DB QUERY FAIL(5)';
                            $sth=$conn_mssr->prepare($sql);
                            $sth->execute()or die($err);

                            $msg="你已拒絕連署";
                        }else{
                            switch(count($db_results)){

                                case 1:

                                    $sql="
                                        # for mssr_forum_user_request
                                        UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                            `request_state`  = 1,
                                            `request_read`   = 1
                                        WHERE 1=1
                                            AND `request_id` = {$rs_request_id}
                                        LIMIT 1;

                                        # for mssr_forum_group_user_rev
                                        INSERT INTO `mssr_forum`.`mssr_forum_group_user_rev` SET
                                            `edit_by`           = {$sess_user_id    } ,
                                            `group_id`          = {$group_id        } ,
                                            `user_id`           = {$sess_user_id    } ,
                                            `user_type`         =1                    ,
                                            `user_state`        =1                    ,
                                            `user_intro`        =''                   ,
                                            `keyin_cdate`       =NOW()                ,
                                            `keyin_mdate`       =NULL                 ;
                                    ";
                                    //送出
                                    $err ='DB QUERY FAIL(1)';
                                    $sth=$conn_mssr->prepare($sql);
                                    $sth->execute()or die($err);

                                break;

                                default:

                                    $sql="
                                        # for mssr_forum_user_request
                                        INSERT INTO `mssr_forum`.`mssr_forum_user_request` SET
                                            `request_from`      = {$group_user_type_2_user_id   },
                                            `request_to`        = {$sess_user_id                },
                                            `request_id`        = NULL                           ,
                                            `request_state`     = 1                              ,
                                            `request_read`      = 1                              ,
                                            `keyin_cdate`       = NOW()                          ,
                                            `keyin_mdate`       = NULL                           ;

                                        # for mssr_forum_group_user_rev
                                        INSERT INTO `mssr_forum`.`mssr_forum_group_user_rev` SET
                                            `edit_by`           = {$sess_user_id    } ,
                                            `group_id`          = {$group_id        } ,
                                            `user_id`           = {$sess_user_id    } ,
                                            `user_type`         =1                    ,
                                            `user_state`        =1                    ,
                                            `user_intro`        =''                   ,
                                            `keyin_cdate`       =NOW()                ,
                                            `keyin_mdate`       =NULL                 ;
                                    ";
                                    //送出
                                    $err ='DB QUERY FAIL(2)';
                                    $sth=$conn_mssr->prepare($sql);
                                    $sth->execute()or die($err);

                                    //lastInsertId
                                    $last_request_id=(int)$conn_mssr->lastInsertId();

                                    $sql="
                                        # for mssr_forum_user_request_create_group_rev
                                        INSERT INTO `mssr_forum`.`mssr_forum_user_request_create_group_rev` SET
                                            `request_id`    = {$last_request_id } ,
                                            `group_id`      = {$group_id        } ,
                                            `rev_id`        = NULL                ;
                                    ";
                                    //送出
                                    $err ='DB QUERY FAIL(3)';
                                    $sth=$conn_mssr->prepare($sql);
                                    $sth->execute()or die($err);

                                break;

                            }

                            if($group_flag){

                                $sql="
                                    # for mssr_forum_group
                                    UPDATE `mssr_forum`.`mssr_forum_group` SET
                                        `group_state`  = 1
                                    WHERE 1=1
                                        AND `group_id`={$group_id}
                                    LIMIT 1;
                                ";
                                //送出
                                $err ='DB QUERY FAIL(4)';
                                $sth=$conn_mssr->prepare($sql);
                                $sth->execute()or die($err);

                            }
                            $msg="聯屬成功，請耐心等待小組建立";
                        }
                    }

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $jscript_back="
                        <script>
                            alert('{$msg}');
                        </script>
                    ";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_request_article()
        //用途: 回覆文章邀請
        //-----------------------------------------------

            function edit_request_article($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_request_article()
            //用途: 回覆文章邀請
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
                //request_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'request_id '
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
                    $request_id=trim($_POST[trim('request_id ')]);

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

                    if($request_id===''){
                       $arry_err[]='邀請主索引,未輸入!';
                    }else{
                        $request_id=(int)$request_id;
                        if($request_id===0){
                            $arry_err[]='邀請主索引,錯誤!';
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

                        $request_id=(int)$request_id;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $request_id=(int)$request_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_user_request
                        UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                            `request_state`  = 1,
                            `request_read`   = 1
                        WHERE 1=1
                            AND `request_id` = {$request_id}
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="回覆成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_group_user_state()
        //用途: 切換小組使用者狀態
        //-----------------------------------------------

            function edit_group_user_state($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_group_user_state()
            //用途: 切換小組使用者狀態
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
                //user_state
                //user_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'group_id ',
                            'user_state',
                            'user_id  '
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
                    $group_id  =trim($_POST[trim('group_id ')]);
                    $user_state=trim($_POST[trim('user_state')]);
                    $user_id   =trim($_POST[trim('user_id  ')]);

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
                    if($user_state===''){
                       $arry_err[]='使用者狀態,未輸入!';
                    }else{
                        $user_state=(int)$user_state;
                        if($user_state===0){
                            $arry_err[]='使用者狀態,錯誤!';
                        }
                    }
                    if($user_id===''){
                       $arry_err[]='使用者主索引,未輸入!';
                    }else{
                        $user_id=(int)$user_id;
                        if($user_id===0){
                            $arry_err[]='使用者主索引,錯誤!';
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

                        $group_id  =(int)$group_id;
                        $user_state=(int)$user_state;
                        $user_id   =(int)$user_id;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $group_id  =(int)$group_id;
                    $user_state=(int)$user_state;
                    $user_id   =(int)$user_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_group_user_rev
                        UPDATE `mssr_forum`.`mssr_forum_group_user_rev` SET
                            `user_state`  = {$user_state}
                        WHERE 1=1
                            AND `group_id`= {$group_id  }
                            AND `user_id` = {$user_id   }
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="修改成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_group_user_type()
        //用途: 切換小組使用者身分
        //-----------------------------------------------

            function edit_group_user_type($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_group_user_type()
            //用途: 切換小組使用者身分
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
                //user_type
                //user_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'group_id ',
                            'user_type',
                            'user_id  '
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
                    $group_id =trim($_POST[trim('group_id ')]);
                    $user_type=trim($_POST[trim('user_type')]);
                    $user_id  =trim($_POST[trim('user_id  ')]);

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
                    if($user_type===''){
                       $arry_err[]='使用者身分,未輸入!';
                    }else{
                        $user_type=(int)$user_type;
                        if($user_type===0){
                            $arry_err[]='使用者身分,錯誤!';
                        }
                    }
                    if($user_id===''){
                       $arry_err[]='使用者主索引,未輸入!';
                    }else{
                        $user_id=(int)$user_id;
                        if($user_id===0){
                            $arry_err[]='使用者主索引,錯誤!';
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
                        $user_type=(int)$user_type;
                        $user_id  =(int)$user_id;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $group_id =(int)$group_id;
                    $user_type=(int)$user_type;
                    $user_id  =(int)$user_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_group_user_rev
                        UPDATE `mssr_forum`.`mssr_forum_group_user_rev` SET
                            `user_type`   = {$user_type}
                        WHERE 1=1
                            AND `group_id`= {$group_id }
                            AND `user_id` = {$user_id  }
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="修改成功";
                    die($msg);
            }
?>

