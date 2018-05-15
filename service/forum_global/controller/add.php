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
        require_once(str_repeat("../",1).'config/config.php');

        //外掛函式檔
        $funcs=array(
            APP_ROOT.'inc/code',
            APP_ROOT.'service/forum_global/inc/code',

            APP_ROOT.'lib/php/fso/code',
            APP_ROOT.'lib/php/upload/file_upload_save/code',
            APP_ROOT.'lib/php/image/phpthumb/thumb_imagebysize',
            APP_ROOT.'lib/php/db/code',
            APP_ROOT.'lib/php/net/code',
            APP_ROOT.'lib/php/array/code',
            APP_ROOT.'lib/php/date/code'
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
        //函式: add_blacklist_group_school()
        //用途: 小組加入學校的黑名單
        //-----------------------------------------------

            function add_blacklist_group_school($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_blacklist_group_school()
            //用途: 小組加入學校的黑名單
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
                            'user_id  ',
                            'group_id '
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
                    $user_id  =trim($_POST[trim('user_id  ')]);
                    $group_id =trim($_POST[trim('group_id ')]);

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

                        $user_id         =(int)$user_id;
                        $group_id        =(int)$group_id;
                        $sess_school_code=mysql_prep($sess_school_code);

                    //-----------------------------------
                    //檢核書籍ISBN
                    //-----------------------------------

                        $sql="
                            SELECT `mssr_forum_global`.`mssr_forum_blacklist_group_school`.`group_id`
                            FROM  `mssr_forum_global`.`mssr_forum_blacklist_group_school`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_blacklist_group_school`.`school_code`= '{$sess_school_code}'
                                AND `mssr_forum_global`.`mssr_forum_blacklist_group_school`.`group_id`   =  {$group_id        }
                        ";
                        $blacklist_group_school_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $user_id         =(int)$user_id;
                    $group_id        =(int)$group_id;
                    $sess_school_code=mysql_prep(strip_tags($sess_school_code));

                //---------------------------------------
                //處理
                //---------------------------------------

                    if(count($blacklist_group_school_results)>=1){
                        $sql="
                            # for mssr_forum_blacklist_group_school
                            DELETE FROM `mssr_forum_global`.`mssr_forum_blacklist_group_school`
                            WHERE 1=1
                                AND `group_id`   = {$group_id        }
                                AND `school_code`='{$sess_school_code}'
                            LIMIT 1;
                        ";
                    }else{
                        $sql="
                            # for mssr_forum_blacklist_group_school
                            INSERT IGNORE INTO `mssr_forum_global`.`mssr_forum_blacklist_group_school` SET
                                `school_code`   = '{$sess_school_code}',
                                `group_id`      =  {$group_id        } ,
                                `blacklist_id`  = NULL                 ,
                                `keyin_mdate`   = NOW()                ;
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

                    $msg="";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_article_draft()
        //用途: 暫存草稿
        //-----------------------------------------------

            function add_article_draft($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_article_draft()
            //用途: 暫存草稿
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
                //book_sid
                //eagle_code
                //article_category
                //article_title
                //article_content
                //group_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'book_sid        ',
                            'eagle_code      ',
                            'article_category',
                            'article_title   ',
                            'article_content ',
                            'group_id        '
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
                    $book_sid           =trim($_POST[trim('book_sid         ')]);
                    $article_title      =trim($_POST[trim('article_title    ')]);
                    $tmp_eagle_code     =trim($_POST[trim('eagle_code       ')]);
                    $group_id           =trim($_POST[trim('group_id         ')]);
                    $article_content    =trim($_POST[trim('article_content  ')]);
                    $article_category   =(isset($_POST['article_category']))?(int)$_POST['article_category']:0;

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

                    if($book_sid===''){
                       $arry_err[]='書本識別碼,未輸入!';
                    }
                    if($article_title===''){
                       $arry_err[]='文章標題,未輸入!';
                    }
                    if($tmp_eagle_code===''){
                       //$tmp_eagle_code='0';
                    }
                    if($group_id===''){
                       $arry_err[]='小組主索引,未輸入!';
                    }
                    if($article_content===''){
                        $arry_err[]='文章內容,未輸入!';
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

                        $book_sid=mysql_prep($book_sid);

                    //-----------------------------------
                    //檢核書籍是否存在
                    //-----------------------------------

                        $arry_book_infos=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_isbn_10','book_isbn_13'),$arry_conn_mssr);
                        if(empty($arry_book_infos)){
                            $msg="書籍不存在";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    location.href='{$send_url}';
                                </script>
                            ";
                            die($msg);
                        }else{
                            $book_isbn_10='';
                            if(trim($arry_book_infos[0]['book_isbn_10'])!=='')$book_isbn_10=trim($arry_book_infos[0]['book_isbn_10']);

                            $book_isbn_13='';
                            if(trim($arry_book_infos[0]['book_isbn_13'])!=='')$book_isbn_13=trim($arry_book_infos[0]['book_isbn_13']);
                        }

                    //-----------------------------------
                    //檢核草稿是否存在
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum_global`.`mssr_forum_article_draft`.`draft_id`
                            FROM `mssr_forum_global`.`mssr_forum_article_draft`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_article_draft`.`user_id` = {$sess_user_id}
                                AND `mssr_forum_global`.`mssr_forum_article_draft`.`book_sid`='{$book_sid    }'
                                AND `mssr_forum_global`.`mssr_forum_article_draft`.`group_id`= {$group_id    }
                        ";
                        $draft_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------
                //book_sid
                //eagle_code
                //article_category
                //article_title
                //article_content
                //group_id

                    $book_isbn_10       =mysql_prep(strip_tags($book_isbn_10));
                    $book_isbn_13       =mysql_prep(strip_tags($book_isbn_13));
                    $book_sid           =mysql_prep(strip_tags($book_sid));
                    $article_title      =bad_content_filter(mysql_prep(strip_tags($article_title)));
                    $group_id           =(int)$group_id;
                    $article_content    =bad_content_filter(mysql_prep(strip_tags($article_content)));
                    $keyin_ip           =get_ip();
                    $tmp_eagle_code     =mysql_prep(strip_tags(trim($tmp_eagle_code)));
                    $article_category   =(int)$article_category;

                    $get_from           =1;
                    if((int)$group_id!==0)$get_from=2;

                //---------------------------------------
                //處理
                //---------------------------------------

                    switch(count($draft_results)){

                        case 0:
                            $sql="
                                # for mssr_forum_article_draft
                                INSERT INTO `mssr_forum_global`.`mssr_forum_article_draft` SET
                                    `user_id`           =  {$sess_user_id    } ,
                                    `book_sid`          = '{$book_sid        }',
                                    `group_id`          =  {$group_id        } ,
                                    `draft_id`          = NULL                 ,
                                    `eagle_code`        = '{$tmp_eagle_code  }',
                                    `article_category`  =  {$article_category} ,
                                    `article_title`     = '{$article_title   }',
                                    `article_content`   = '{$article_content }';
                            ";
                        break;

                        default:
                            $sql="
                                # for mssr_forum_article_draft
                                UPDATE `mssr_forum_global`.`mssr_forum_article_draft` SET
                                    `eagle_code`        = '{$tmp_eagle_code  }',
                                    `article_category`  =  {$article_category} ,
                                    `article_title`     = '{$article_title   }',
                                    `article_content`   = '{$article_content }'
                                WHERE 1=1
                                    AND `mssr_forum_global`.`mssr_forum_article_draft`.`user_id` = {$sess_user_id}
                                    AND `mssr_forum_global`.`mssr_forum_article_draft`.`book_sid`='{$book_sid    }'
                                    AND `mssr_forum_global`.`mssr_forum_article_draft`.`group_id`= {$group_id    }
                                LIMIT 1;
                            ";
                        break;
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

                    $msg="儲存草稿成功";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            location.href='../view/article.php?get_from={$get_from}&book_sid={$book_sid}&group_id={$group_id}';
                        </script>
                    ";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_hot_booklist()
        //用途: 熱門書單
        //-----------------------------------------------

            function add_hot_booklist($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_hot_booklist()
            //用途: 熱門書單
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
                //book_sid

                    if(!empty($_POST)){
                        $post_chk=array(
                            'user_id  ',
                            'book_sid '
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
                    $user_id  =trim($_POST[trim('user_id  ')]);
                    $book_sid =trim($_POST[trim('book_sid ')]);

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

                        $user_id       =(int)$user_id;
                        $book_sid      =mysql_prep(trim($book_sid));
                        //$year          =date("Y");
                        //$month         =date("m");
                        //$date_now      =(int)date('j');
                        //$week_cno      =(int)(ceil($date_now/7)-1);
                        //$arry_date_week=date_week_array($year,$month);
                        //$week_sdate    =trim($arry_date_week[$week_cno]['sdate']);
                        //$week_edate    =trim($arry_date_week[$week_cno]['edate']);
                        //$week_sdate    =trim(date('Y-m-d', time()-86400*date('w')+(date('w')>0?86400:-6*86400)));
                        //$week_edate    =trim(date("Y-m-d",strtotime($week_sdate)+(86400*6)));
                        $week_sdate    =trim(date('Y-m-d'));
                        $week_edate    =trim(date('Y-m-d'));

                    //-----------------------------------
                    //檢核追蹤狀態
                    //-----------------------------------

                        $sql="
                            SELECT `mssr_forum_global`.`mssr_forum_hot_booklist`.`create_by`
                            FROM  `mssr_forum_global`.`mssr_forum_hot_booklist`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_hot_booklist`.`create_by` = {$sess_user_id}
                                AND `mssr_forum_global`.`mssr_forum_hot_booklist`.`book_sid`  ='{$book_sid    }'
                                AND `mssr_forum_global`.`mssr_forum_hot_booklist`.`keyin_cdate` BETWEEN '{$week_sdate} 00:00:00' AND '{$week_edate} 23:59:59'
                        ";
                        $hot_booklist_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

                    //-----------------------------------
                    //檢核投票次數
                    //-----------------------------------

                        $sql="
                            SELECT `mssr_forum_global`.`mssr_forum_hot_booklist`.`create_by`
                            FROM  `mssr_forum_global`.`mssr_forum_hot_booklist`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_hot_booklist`.`create_by` = {$sess_user_id}
                                AND `mssr_forum_global`.`mssr_forum_hot_booklist`.`keyin_cdate` BETWEEN '{$week_sdate} 00:00:00' AND '{$week_edate} 23:59:59'
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,10),$arry_conn_mssr);
                        if(count($db_results)>=5){
                            $msg="每天只能投5次票";
                            die($msg);
                        }

                    //-----------------------------------
                    //檢核書籍ISBN
                    //-----------------------------------

                        $arrys_book_info=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_isbn_10','book_isbn_13'),$arry_conn_mssr);
                        if(!empty($arrys_book_info)){
                            $book_isbn_10=trim($arrys_book_info[0]['book_isbn_10']);
                            $book_isbn_13=trim($arrys_book_info[0]['book_isbn_13']);
                        }else{
                            $book_isbn_10='';
                            $book_isbn_13='';
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $user_id        =(int)$user_id;
                    $book_sid       =mysql_prep(strip_tags($book_sid));
                    $book_isbn_10   =mysql_prep(strip_tags($book_isbn_10));
                    $book_isbn_13   =mysql_prep(strip_tags($book_isbn_13));
                    $week_sdate     =mysql_prep(trim($week_sdate));
                    $week_edate     =mysql_prep(trim($week_edate));

                //---------------------------------------
                //處理
                //---------------------------------------

                    switch(count($hot_booklist_results)){

                        case 1:
                            $sql="
                                # for mssr_forum_hot_booklist
                                DELETE FROM `mssr_forum_global`.`mssr_forum_hot_booklist`
                                WHERE 1=1
                                    AND `create_by` = {$sess_user_id    }
                                    AND `book_sid`  ='{$book_sid        }'
                                    AND `mssr_forum_global`.`mssr_forum_hot_booklist`.`keyin_cdate` BETWEEN '{$week_sdate} 00:00:00' AND '{$week_edate} 23:59:59'
                                LIMIT 1;
                            ";
                        break;

                        default:
                            $sql="
                                # for mssr_forum_hot_booklist
                                INSERT INTO `mssr_forum_global`.`mssr_forum_hot_booklist` SET
                                    `create_by`     = {$sess_user_id    } ,
                                    `book_sid`      ='{$book_sid        }',
                                    `book_isbn_10`  ='{$book_isbn_10    }',
                                    `book_isbn_13`  ='{$book_isbn_13    }',
                                    `keyin_cdate`   = NOW()               ;
                            ";
                        break;

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

                    $msg="";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_best_article()
        //用途: 加入精華文
        //-----------------------------------------------

            function add_best_article($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_best_article()
            //用途: 加入精華文
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
                //cat_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'article_id ',
                            'cat_id     '
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
                    $article_id =trim($_POST[trim('article_id ')]);
                    $cat_id     =trim($_POST[trim('cat_id     ')]);

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
                    if($cat_id===''){
                       $arry_err[]='精華文章類別主索引,未輸入!';
                    }else{
                        $cat_id=(int)$cat_id;
                        if($cat_id===0){
                            $arry_err[]='精華文章類別主索引,錯誤!';
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
                        $cat_id     =(int)$cat_id;

                    //-----------------------------------
                    //檢核小組主索引
                    //-----------------------------------

                        $sql="
                            SELECT `mssr_forum_global`.`mssr_forum_article`.`group_id`
                            FROM `mssr_forum_global`.`mssr_forum_article`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_article`.`article_id`={$article_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(empty($db_results)){
                            $msg="發生嚴重錯誤";
                            die($msg);
                        }else{
                            $group_id=(int)$db_results[0]['group_id'];
                        }

                    //-----------------------------------
                    //檢核是否已加入
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum_global`.`mssr_forum_best_article_category_rev`.`cat_id`,
                                `mssr_forum_global`.`mssr_forum_best_article_category`.`cat_name`
                            FROM `mssr_forum_global`.`mssr_forum_best_article_category_rev`
                                INNER JOIN `mssr_forum_global`.`mssr_forum_best_article_category` ON
                                `mssr_forum_global`.`mssr_forum_best_article_category_rev`.`cat_id`=`mssr_forum_global`.`mssr_forum_best_article_category`.`cat_id`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_best_article_category_rev`.`article_id`={$article_id}
                                AND `mssr_forum_global`.`mssr_forum_best_article_category_rev`.`cat_id`    ={$cat_id}
                                AND `mssr_forum_global`.`mssr_forum_best_article_category`.`cat_state`=1
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(!empty($db_results)){
                            $msg="加入成功";
                            die($msg);
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $article_id =(int)$article_id;
                    $cat_id     =(int)$cat_id;
                    $group_id   =(int)$group_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_article
                        UPDATE `mssr_forum_global`.`mssr_forum_article` SET
                            `article_type`   = 2
                        WHERE 1=1
                            AND `article_id` = {$article_id}
                        LIMIT 1;

                        # for mssr_forum_best_article_category_rev
                        INSERT INTO `mssr_forum_global`.`mssr_forum_best_article_category_rev` SET
                            `create_by`     = {$sess_user_id    } ,
                            `group_id`      = {$group_id        } ,
                            `article_id`    = {$article_id      } ,
                            `cat_id`        = {$cat_id          } ,
                            `keyin_cdate`   = NOW()               ;
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

                    $msg="加入成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_request_rec_us_book()
        //用途: 新增邀請(請求好友推薦一本書籍給你)
        //-----------------------------------------------

            function add_request_rec_us_book($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_request_rec_us_book()
            //用途: 新增邀請(請求好友推薦一本書籍給你)
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

                    if(!empty($_POST)){
                        $post_chk=array(
                            'user_id'
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
                    $user_id=trim($_POST[trim('user_id')]);

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
                       $arry_err[]='好友主索引,未輸入!';
                    }else{
                        $user_id=(int)$user_id;
                        if($user_id===0){
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

                        $user_id=(int)$user_id;

                    //-----------------------------------
                    //檢核邀請狀態
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_id`,
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_state`
                            FROM `mssr_forum_global`.`mssr_forum_user_request`
                                INNER JOIN `mssr_forum_global`.`mssr_forum_user_request_rec_us_book_rev` ON
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_id`=`mssr_forum_user_request_rec_us_book_rev`.`request_id`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_user_request`.`request_from`={$sess_user_id}
                                AND `mssr_forum_global`.`mssr_forum_user_request`.`request_to`={$user_id}
                            ORDER BY `mssr_forum_global`.`mssr_forum_user_request`.`keyin_cdate` DESC
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                        if(!empty($db_results)){
                            $rs_request_id   =(int)$db_results[0]['request_id'];
                            $rs_request_state=(int)$db_results[0]['request_state'];
                            if($rs_request_state===3){
                                $msg="好友還在考慮喔";
                                $jscript_back="
                                    <script>
                                        alert('{$msg}');
                                        location.href='{$send_url}';
                                    </script>
                                ";
                                die($msg);
                            }
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $user_id=(int)$user_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_user_request
                        INSERT INTO `mssr_forum_global`.`mssr_forum_user_request` SET
                            `request_from`      = {$sess_user_id    } ,
                            `request_to`        = {$user_id         } ,
                            `request_id`        = NULL                ,
                            `request_state`     = 3                   ,
                            `request_read`      = 2                   ,
                            `keyin_cdate`       = NOW()               ,
                            `keyin_mdate`       = NULL                ;
                    ";
                    //送出
                    $err ='DB QUERY FAIL(1)';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                    //lastInsertId
                    $last_request_id=(int)$conn_mssr->lastInsertId();

                    $sql="
                        # for mssr_forum_user_request_rec_us_book_rev
                        INSERT INTO `mssr_forum_global`.`mssr_forum_user_request_rec_us_book_rev` SET
                            `request_id`     = {$last_request_id } ,
                            `book_sid`       = ''                  ,
                            `rev_id`         = NULL                ,
                            `request_content`= ''                  ;
                    ";
                    //送出
                    $err ='DB QUERY FAIL(2)';
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

                    $msg="請求成功，請耐心等待好友回應";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            location.href='{$send_url}';
                        </script>
                    ";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_track_article()
        //用途: 追蹤文章
        //-----------------------------------------------

            function add_track_article($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_track_article()
            //用途: 追蹤文章
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
                //article_id
                //group_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'user_id   ',
                            'article_id',
                            'group_id  '
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
                    $user_id   =trim($_POST[trim('user_id   ')]);
                    $article_id=trim($_POST[trim('article_id')]);
                    $group_id  =trim($_POST[trim('group_id  ')]);

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
                    if($article_id===''){
                       $arry_err[]='文章主索引,未輸入!';
                    }else{
                        $article_id=(int)$article_id;
                        if($article_id===0){
                            $arry_err[]='文章主索引,錯誤!';
                        }
                    }
                    if($group_id===''){
                       $arry_err[]='小組主索引,未輸入!';
                    }else{
                        $group_id=(int)$group_id;
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

                        $user_id   =(int)$user_id   ;
                        $article_id=(int)$article_id;
                        $group_id  =(int)$group_id  ;

                    //-----------------------------------
                    //檢核追蹤狀態
                    //-----------------------------------

                        $sql="
                            SELECT `mssr_forum_global`.`mssr_forum_track_article`.`user_id`
                            FROM  `mssr_forum_global`.`mssr_forum_track_article`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_track_article`.`user_id`   = {$sess_user_id}
                                AND `mssr_forum_global`.`mssr_forum_track_article`.`article_id`= {$article_id  }
                                AND `mssr_forum_global`.`mssr_forum_track_article`.`group_id`  = {$group_id    }
                        ";
                        $track_track_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $user_id   =(int)$user_id   ;
                    $article_id=(int)$article_id;
                    $group_id  =(int)$group_id  ;

                //---------------------------------------
                //處理
                //---------------------------------------

                    switch(count($track_track_results)){

                        case 1:
                            $sql="
                                # for mssr_forum_track_article
                                DELETE FROM `mssr_forum_global`.`mssr_forum_track_article`
                                WHERE 1=1
                                    AND `mssr_forum_global`.`mssr_forum_track_article`.`user_id`   = {$sess_user_id}
                                    AND `mssr_forum_global`.`mssr_forum_track_article`.`article_id`= {$article_id  }
                                    AND `mssr_forum_global`.`mssr_forum_track_article`.`group_id`  = {$group_id    }
                                LIMIT 1;
                            ";
                        break;

                        default:
                            $sql="
                                # for mssr_forum_track_article
                                INSERT INTO `mssr_forum_global`.`mssr_forum_track_article` SET
                                    `user_id`       = {$sess_user_id} ,
                                    `group_id`      = {$group_id    } ,
                                    `article_id`    = {$article_id  } ,
                                    `keyin_cdate`   = NOW()           ;
                            ";
                        break;

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

                    $msg="追蹤文章成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_request_more_create_group()
        //用途: 新增邀請(更多好友聯署建立小組)
        //-----------------------------------------------

            function add_request_more_create_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_request_more_create_group()
            //用途: 新增邀請(更多好友聯署建立小組)
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
                //group_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'friend_id  ',
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
                    $friend_id  =trim($_POST[trim('friend_id    ')][0]);
                    $group_id   =trim($_POST[trim('group_id     ')]);

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

                        $friend_id =(int)$friend_id;
                        $group_id  =(int)$group_id;

                    //-----------------------------------
                    //檢核版主是誰
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_id`,
                                `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_type`
                            FROM `mssr_forum_global`.`mssr_forum_group`
                                INNER JOIN `mssr_forum_global`.`mssr_forum_group_user_rev` ON
                                `mssr_forum_global`.`mssr_forum_group`.`group_id`=`mssr_forum_global`.`mssr_forum_group_user_rev`.`group_id`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_group_user_rev`.`group_id`  ={$group_id}
                                AND `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_type` IN (2,3)
                                AND `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_state`IN (1)
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        if(empty($db_results)){
                            $msg="發生嚴重錯誤";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    history.back(-1);
                                </script>
                            ";
                            die($jscript_back);
                        }else{
                            foreach($db_results as $db_result){
                                $group_user_type_admin[]=(int)$db_result['user_id'];
                                if((int)$db_result['user_type']===2){
                                    $group_user_type_2_user_id=(int)$db_result['user_id'];
                                }
                            }
                        }

                    //-----------------------------------
                    //檢核是否已聯署過
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_id`,
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_state`
                            FROM `mssr_forum_global`.`mssr_forum_user_request`
                                INNER JOIN `mssr_forum_global`.`mssr_forum_user_request_create_group_rev` ON
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_id`=`mssr_forum_user_request_create_group_rev`.`request_id`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_user_request`.`request_from`={$group_user_type_2_user_id}
                                AND `mssr_forum_global`.`mssr_forum_user_request`.`request_to`={$friend_id}
                                AND `mssr_forum_global`.`mssr_forum_user_request_create_group_rev`.`group_id`={$group_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                        if(!empty($db_results)){
                            $rs_request_id   =(int)$db_results[0]['request_id'];
                            $rs_request_state=(int)$db_results[0]['request_state'];

                            if($rs_request_state===1){
                                $msg="好友已經聯署過";
                                $jscript_back="
                                    <script>
                                        alert('{$msg}');
                                        history.back(-1);
                                    </script>
                                ";
                                die($jscript_back);
                            }
                            if($rs_request_state===2){
                                $msg="好友已經拒絕聯署了";
                                $jscript_back="
                                    <script>
                                        alert('{$msg}');
                                        history.back(-1);
                                    </script>
                                ";
                                die($jscript_back);
                            }
                            if($rs_request_state===3){
                                $msg="好友還在考慮喔";
                                $jscript_back="
                                    <script>
                                        alert('{$msg}');
                                        history.back(-1);
                                    </script>
                                ";
                                die($jscript_back);
                            }
                        }

                        if(in_array($friend_id,$group_user_type_admin)){
                            $msg="好友已經聯署過";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    history.back(-1);
                                </script>
                            ";
                            die($jscript_back);
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $friend_id  =(int)$friend_id;
                    $group_id   =(int)$group_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_user_request
                        INSERT INTO `mssr_forum_global`.`mssr_forum_user_request` SET
                            `request_from`      = {$group_user_type_2_user_id   }   ,
                            `request_to`        = {$friend_id                   }   ,
                            `request_id`        =NULL                               ,
                            `request_state`     =3                                  ,
                            `request_read`      =2                                  ,
                            `keyin_cdate`       =NOW()                              ,
                            `keyin_mdate`       = NULL                              ;
                    ";
                    //送出
                    $err ='DB QUERY FAIL(1)';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                    //lastInsertId
                    $last_request_id=(int)$conn_mssr->lastInsertId();

                    $sql="
                        # for mssr_forum_user_request_create_group_rev
                        INSERT INTO `mssr_forum_global`.`mssr_forum_user_request_create_group_rev` SET
                            `request_id`    = {$last_request_id } ,
                            `group_id`      = {$group_id        } ,
                            `rev_id`        =NULL                 ;
                    ";
                    //送出
                    $err ='DB QUERY FAIL(2)';
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

                    $msg="邀請成功，請耐心等待好友聯署";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
            }

        //-----------------------------------------------
        //函式: add_request_create_group()
        //用途: 新增邀請(聯署建立小組)
        //-----------------------------------------------

            function add_request_create_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_request_create_group()
            //用途: 新增邀請(聯署建立小組)
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
                            'user_id    ',
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
                    $user_id    =trim($_POST[trim('user_id    ')]);
                    $group_id   =trim($_POST[trim('group_id   ')]);

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

                        $user_id =(int)$user_id;
                        $group_id=(int)$group_id;

                    //-----------------------------------
                    //檢核版主是誰
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_id`,
                                `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_type`
                            FROM `mssr_forum_global`.`mssr_forum_group`
                                INNER JOIN `mssr_forum_global`.`mssr_forum_group_user_rev` ON
                                `mssr_forum_global`.`mssr_forum_group`.`group_id`=`mssr_forum_global`.`mssr_forum_group_user_rev`.`group_id`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_group_user_rev`.`group_id`  ={$group_id}
                                AND `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_type` IN (2,3)
                                AND `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_state`IN (1)
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(empty($db_results)){
                            $msg="發生嚴重錯誤";
                            die($msg);
                        }else{
                            foreach($db_results as $db_result){
                                $group_user_type_admin[]=(int)$db_result['user_id'];
                                if((int)$db_result['user_type']===2){
                                    $group_user_type_2_user_id=(int)$db_result['user_id'];
                                }
                            }
                        }

                    //-----------------------------------
                    //檢核小組是否已越過聯署門檻
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_id`,
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_state`
                            FROM `mssr_forum_global`.`mssr_forum_user_request`
                                INNER JOIN `mssr_forum_global`.`mssr_forum_user_request_create_group_rev` ON
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_id`=`mssr_forum_user_request_create_group_rev`.`request_id`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_user_request`.`request_from`={$group_user_type_2_user_id}
                                AND `mssr_forum_global`.`mssr_forum_user_request_create_group_rev`.`group_id`={$group_id}
                                AND `mssr_forum_global`.`mssr_forum_user_request`.`request_state`=1
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        $group_state=false;
                        if(count($db_results)>=1){
                            $group_state=true;
                        }

                    //-----------------------------------
                    //檢核是否已聯署過
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_id`,
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_state`
                            FROM `mssr_forum_global`.`mssr_forum_user_request`
                                INNER JOIN `mssr_forum_global`.`mssr_forum_user_request_create_group_rev` ON
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_id`=`mssr_forum_user_request_create_group_rev`.`request_id`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_user_request`.`request_from`={$group_user_type_2_user_id}
                                AND `mssr_forum_global`.`mssr_forum_user_request`.`request_to`={$sess_user_id}
                                AND `mssr_forum_global`.`mssr_forum_user_request_create_group_rev`.`group_id`={$group_id}
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

                        if(in_array($sess_user_id,$group_user_type_admin)){
                            $msg="你已經聯署過";
                            die($msg);
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $user_id =(int)$user_id;
                    $group_id=(int)$group_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    switch(count($db_results)){

                        case 1:

                            $sql="
                                # for mssr_forum_user_request
                                UPDATE `mssr_forum_global`.`mssr_forum_user_request` SET
                                    `request_state`  = 1,
                                    `request_read`   = 1
                                WHERE 1=1
                                    AND `request_id` = {$rs_request_id}
                                LIMIT 1;

                                # for mssr_forum_group_user_rev
                                INSERT INTO `mssr_forum_global`.`mssr_forum_group_user_rev` SET
                                    `edit_by`           = {$sess_user_id    } ,
                                    `group_id`          = {$group_id        } ,
                                    `user_id`           = {$sess_user_id    } ,
                                    `user_type`         =1                    ,
                                    `user_state`        =3                    ,
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
                                INSERT INTO `mssr_forum_global`.`mssr_forum_user_request` SET
                                    `request_from`      = {$group_user_type_2_user_id   },
                                    `request_to`        = {$sess_user_id                },
                                    `request_id`        = NULL                           ,
                                    `request_state`     = 1                              ,
                                    `request_read`      = 1                              ,
                                    `keyin_cdate`       = NOW()                          ,
                                    `keyin_mdate`       = NULL                           ;

                                # for mssr_forum_group_user_rev
                                INSERT INTO `mssr_forum_global`.`mssr_forum_group_user_rev` SET
                                    `edit_by`           = {$sess_user_id    } ,
                                    `group_id`          = {$group_id        } ,
                                    `user_id`           = {$sess_user_id    } ,
                                    `user_type`         =1                    ,
                                    `user_state`        =3                    ,
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
                                INSERT INTO `mssr_forum_global`.`mssr_forum_user_request_create_group_rev` SET
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

                    if($group_state){

                        $sql="
                            # for mssr_forum_group
                            UPDATE `mssr_forum_global`.`mssr_forum_group` SET
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

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="聯屬成功，請耐心等待小組建立";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                        </script>
                    ";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_request_join_to_group()
        //用途: 新增邀請(申請加入小組)
        //-----------------------------------------------

            function add_request_join_to_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_request_join_to_group()
            //用途: 新增邀請(申請加入小組)
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
                            'user_id    ',
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
                    $user_id    =trim($_POST[trim('user_id    ')]);
                    $group_id   =trim($_POST[trim('group_id   ')]);

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

                        $user_id =(int)$user_id;
                        $group_id=(int)$group_id;

                    //-----------------------------------
                    //檢核版主是誰
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_id`,
                                `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_type`
                            FROM `mssr_forum_global`.`mssr_forum_group`
                                INNER JOIN `mssr_forum_global`.`mssr_forum_group_user_rev` ON
                                `mssr_forum_global`.`mssr_forum_group`.`group_id`=`mssr_forum_global`.`mssr_forum_group_user_rev`.`group_id`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_group_user_rev`.`group_id`  ={$group_id}
                                AND `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_type` IN (2,3)
                                AND `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_state`IN (1)
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        if(empty($db_results)){
                            $msg="發生嚴重錯誤";
                            die($msg);
                        }else{
                            $arry_group_user_type_2_user_id=array();
                            foreach($db_results as $db_result){
                                $group_user_type_admin[]=(int)$db_result['user_id'];
                                if((int)$db_result['user_type']===3){
                                    $group_user_type_2_user_id=(int)$db_result['user_id'];
                                    $arry_group_user_type_2_user_id[]=$group_user_type_2_user_id;
                                }
                                if((int)$db_result['user_type']===2){
                                    $group_user_type_2_user_id=(int)$db_result['user_id'];
                                    $arry_group_user_type_2_user_id[]=$group_user_type_2_user_id;
                                }
                            }
                        }

                    //-----------------------------------
                    //檢核是否已申請過
                    //-----------------------------------

                        $arry_request_id=array();
                        foreach($arry_group_user_type_2_user_id as $arry_val){
                            $group_user_type_2_user_id=(int)$arry_val;

                            $sql="
                                SELECT
                                    `mssr_forum_global`.`mssr_forum_user_request`.`request_id`,
                                    `mssr_forum_global`.`mssr_forum_user_request`.`request_state`
                                FROM `mssr_forum_global`.`mssr_forum_user_request`
                                    INNER JOIN `mssr_forum_global`.`mssr_forum_user_request_join_to_group_rev` ON
                                    `mssr_forum_global`.`mssr_forum_user_request`.`request_id`=`mssr_forum_user_request_join_to_group_rev`.`request_id`
                                WHERE 1=1
                                    AND `mssr_forum_global`.`mssr_forum_user_request`.`request_from`={$user_id}
                                    AND `mssr_forum_global`.`mssr_forum_user_request`.`request_to`={$group_user_type_2_user_id}
                                    AND `mssr_forum_global`.`mssr_forum_user_request_join_to_group_rev`.`group_id`={$group_id}
                            ";
                            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                            if(!empty($db_results)){
                                $rs_request_id   =(int)$db_results[0]['request_id'];
                                $rs_request_state=(int)$db_results[0]['request_state'];
                                if($rs_request_state===1){
                                    $msg="你已經申請過了";
                                    $jscript_back="
                                        <script>
                                            alert('{$msg}');
                                        </script>
                                    ";
                                    die($msg);
                                }
                                if($rs_request_state===3){
                                    $msg="版主還在考慮喔";
                                    $jscript_back="
                                        <script>
                                            alert('{$msg}');
                                        </script>
                                    ";
                                    die($msg);
                                }
                                $arry_request_id[]=$rs_request_id;
                            }
                        }

                        if(in_array($user_id,$group_user_type_admin)){
                            $msg="你已經加入此小組";
                            die($msg);
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $user_id =(int)$user_id;
                    $group_id=(int)$group_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    switch(count($db_results)){

                        case 0:

                            foreach($arry_group_user_type_2_user_id as $arry_val){
                                $group_user_type_2_user_id=(int)$arry_val;

                                $sql="
                                    # for mssr_forum_user_request
                                    INSERT INTO `mssr_forum_global`.`mssr_forum_user_request` SET
                                        `request_from`      = {$sess_user_id                },
                                        `request_to`        = {$group_user_type_2_user_id   },
                                        `request_id`        = NULL                           ,
                                        `request_state`     = 3                              ,
                                        `request_read`      = 2                              ,
                                        `keyin_cdate`       = NOW()                          ,
                                        `keyin_mdate`       = NULL                           ;
                                ";
                                //送出
                                $err ='DB QUERY FAIL(2)';
                                $sth=$conn_mssr->prepare($sql);
                                $sth->execute()or die($err);

                                //lastInsertId
                                $last_request_id=(int)$conn_mssr->lastInsertId();

                                $sql="
                                    # for mssr_forum_user_request_join_to_group_rev
                                    INSERT INTO `mssr_forum_global`.`mssr_forum_user_request_join_to_group_rev` SET
                                        `request_id`    = {$last_request_id } ,
                                        `group_id`      = {$group_id        } ,
                                        `rev_id`        = NULL                ;
                                ";
                                //送出
                                $err ='DB QUERY FAIL(3)';
                                $sth=$conn_mssr->prepare($sql);
                                $sth->execute()or die($err);
                            }

                        break;

                        default:

                            foreach($arry_request_id as $rs_request_id){
                                $rs_request_id=(int)$rs_request_id;

                                $sql="
                                    # for mssr_forum_user_request
                                    UPDATE `mssr_forum_global`.`mssr_forum_user_request` SET
                                        `request_state`  = 3,
                                        `request_read`   = 2
                                    WHERE 1=1
                                        AND `request_id` = {$rs_request_id}
                                    LIMIT 1;
                                ";
                                //送出
                                $err ='DB QUERY FAIL(1)';
                                $sth=$conn_mssr->prepare($sql);
                                $sth->execute()or die($err);
                            }

                        break;

                    }

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="申請成功，請耐心等待版主回應";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                        </script>
                    ";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_join_us_group()
        //用途: 新增人員加入小組
        //-----------------------------------------------

            function add_join_us_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_join_us_group()
            //用途: 新增人員加入小組
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;
                    global $file_server_enable;
                    global $arry_ftp1_info;
                    global $fso_enc;
                    global $page_enc;
                    global $conn_host_country_code;
                    global $arry_conn_mssr_tw;
                    global $arry_conn_mssr_hk;
                    global $arry_conn_mssr_sg;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //friend_id
                //group_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'friend_id      ',
                            'group_id       ',
                            'friend_name    '
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
                    $friend_id   =trim($_POST[trim('friend_id    ')]);
                    $group_id    =trim($_POST[trim('group_id     ')]);
                    $friend_name =trim($_POST[trim('friend_name  ')]);

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

                        $friend_id          =(int)$friend_id;
                        $group_id           =(int)$group_id;
                        $friend_name        =trim($friend_name);
                        $friend_type        =1;
                        $friend_country_code='';

                    //-----------------------------------
                    //檢核國籍身分
                    //-----------------------------------

                        $sql="
                            SELECT
                                `user`.`member`.`name`,
                                `user`.`personnel`.`responsibilities`,
                                `user`.`school`.`country_code`
                            FROM `user`.`member`
                                LEFT JOIN `user`.`personnel` ON
                                `user`.`member`.`uid`=`user`.`personnel`.`uid`

                                LEFT JOIN `user`.`member_school` ON
                                `user`.`member`.`uid`=`user`.`member_school`.`uid`

                                LEFT JOIN `user`.`school` ON
                                `user`.`member_school`.`school_code`=`user`.`school`.`school_code`
                            WHERE 1=1
                                AND `user`.`member`.`uid` IN ({$friend_id})
                                AND `user`.`member`.`name` = '{$friend_name}'
                        ";
                        $db_results_hk=db_result($conn_type='pdo','',$sql,array(),$arry_conn_mssr_hk);
                        if(!empty($db_results_hk)){
                            if(trim($db_results_hk[0]['responsibilities'])!=='')$friend_type=2;
                            $friend_country_code=trim($db_results_hk[0]['country_code']);
                        }

                        $sql="
                            SELECT
                                `user`.`member`.`name`,
                                `user`.`personnel`.`responsibilities`,
                                `user`.`school`.`country_code`
                            FROM `user`.`member`
                                LEFT JOIN `user`.`personnel` ON
                                `user`.`member`.`uid`=`user`.`personnel`.`uid`

                                LEFT JOIN `user`.`member_school` ON
                                `user`.`member`.`uid`=`user`.`member_school`.`uid`

                                LEFT JOIN `user`.`school` ON
                                `user`.`member_school`.`school_code`=`user`.`school`.`school_code`
                            WHERE 1=1
                                AND `user`.`member`.`uid` IN ({$friend_id})
                                AND `user`.`member`.`name` = '{$friend_name}'
                        ";
                        $db_results_tw=db_result($conn_type='pdo','',$sql,array(),$arry_conn_mssr_tw);
                        if(!empty($db_results_tw)){
                            if(trim($db_results_tw[0]['responsibilities'])!=='')$friend_type=2;
                            $friend_country_code=trim($db_results_tw[0]['country_code']);
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $friend_id          =(int)$friend_id;
                    $group_id           =(int)$group_id;
                    $friend_type        =(int)$friend_type;
                    $friend_country_code=trim($friend_country_code);

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_group_user_rev
                        INSERT IGNORE INTO `mssr_forum_global`.`mssr_forum_group_user_rev` SET
                            `edit_by`           = {$friend_id           }   ,
                            `group_id`          = {$group_id            }   ,
                            `user_id`           = {$friend_id           }   ,
                            `user_country_code` ='{$friend_country_code }'  ,
                            `user_type`         = {$friend_type         }   ,
                            `user_state`        =1                          ,
                            `user_intro`        =''                         ,
                            `keyin_cdate`       =NOW()                      ,
                            `keyin_mdate`       =NOW()                      ;
                    ";
                    //送出
                    $err ='DB QUERY FAIL(1)';
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

                    $msg="加入成功";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                        </script>
                    ";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_request_join_us_group()
        //用途: 新增邀請(邀請好友加入小組)
        //-----------------------------------------------

            function add_request_join_us_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_request_join_us_group()
            //用途: 新增邀請(邀請好友加入小組)
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
                //group_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'friend_id    ',
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
                    $friend_id  =trim($_POST[trim('friend_id    ')]);
                    $group_id   =trim($_POST[trim('group_id   ')]);

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

                        $friend_id  =(int)$friend_id;
                        $group_id   =(int)$group_id;

                    //-----------------------------------
                    //檢核是否已經是成員
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_state`
                            FROM `mssr_forum_global`.`mssr_forum_group`
                                INNER JOIN `mssr_forum_global`.`mssr_forum_group_user_rev` ON
                                `mssr_forum_global`.`mssr_forum_group`.`group_id`=`mssr_forum_global`.`mssr_forum_group_user_rev`.`group_id`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_group_user_rev`.`group_id`   ={$group_id}
                                AND `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_id`    ={$friend_id}
                                AND `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_state` IN (1,2,3)
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(!empty($db_results)){
                            $rs_user_state=(int)$db_results[0]['user_state'];
                            if($rs_user_state===1){
                                $msg="好友已經是成員";
                            }
                            if($rs_user_state===2){
                                $msg="好友已經是成員，但被停用中";
                            }
                            if($rs_user_state===3){
                                $msg="好友正在申請加入中，請盡快前往審核";
                            }
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                </script>
                            ";
                            die($msg);
                        }

                    //-----------------------------------
                    //檢核是否已邀請過
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_id`,
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_state`
                            FROM `mssr_forum_global`.`mssr_forum_user_request`
                                INNER JOIN `mssr_forum_global`.`mssr_forum_user_request_join_us_group_rev` ON
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_id`=`mssr_forum_user_request_join_us_group_rev`.`request_id`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_user_request`.`request_from`={$sess_user_id}
                                AND `mssr_forum_global`.`mssr_forum_user_request`.`request_to`={$friend_id}
                                AND `mssr_forum_global`.`mssr_forum_user_request_join_us_group_rev`.`group_id`={$group_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                        if(!empty($db_results)){
                            $rs_request_id   =(int)$db_results[0]['request_id'];
                            $rs_request_state=(int)$db_results[0]['request_state'];
                            if($rs_request_state===1){
                                $msg="你已經邀請過了";
                                $jscript_back="
                                    <script>
                                        alert('{$msg}');
                                    </script>
                                ";
                                die($msg);
                            }
                            if($rs_request_state===3){
                                $msg="好友還在考慮喔";
                                $jscript_back="
                                    <script>
                                        alert('{$msg}');
                                    </script>
                                ";
                                die($msg);
                            }
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $friend_id  =(int)$friend_id;
                    $group_id   =(int)$group_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    switch(count($db_results)){

                        case 1:

                            $sql="
                                # for mssr_forum_user_request
                                UPDATE `mssr_forum_global`.`mssr_forum_user_request` SET
                                    `request_state`  = 3,
                                    `request_read`   = 2
                                WHERE 1=1
                                    AND `request_id` = {$rs_request_id}
                                LIMIT 1;
                            ";
                            //送出
                            $err ='DB QUERY FAIL(1)';
                            $sth=$conn_mssr->prepare($sql);
                            $sth->execute()or die($err);

                        break;

                        default:

                            $sql="
                                # for mssr_forum_user_request
                                INSERT INTO `mssr_forum_global`.`mssr_forum_user_request` SET
                                    `request_from`      = {$sess_user_id    } ,
                                    `request_to`        = {$friend_id       } ,
                                    `request_id`        = NULL                ,
                                    `request_state`     = 3                   ,
                                    `request_read`      = 2                   ,
                                    `keyin_cdate`       = NOW()               ,
                                    `keyin_mdate`       = NULL                ;
                            ";
                            //送出
                            $err ='DB QUERY FAIL(2)';
                            $sth=$conn_mssr->prepare($sql);
                            $sth->execute()or die($err);

                            //lastInsertId
                            $last_request_id=(int)$conn_mssr->lastInsertId();

                            $sql="
                                # for mssr_forum_user_request_join_us_group_rev
                                INSERT INTO `mssr_forum_global`.`mssr_forum_user_request_join_us_group_rev` SET
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

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="邀請成功，請耐心等待好友回應";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                        </script>
                    ";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_track_group()
        //用途: 追蹤小組
        //-----------------------------------------------

            function add_track_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_track_group()
            //用途: 追蹤小組
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
                            'user_id  ',
                            'group_id '
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
                    $user_id  =trim($_POST[trim('user_id  ')]);
                    $group_id =trim($_POST[trim('group_id ')]);

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

                        $user_id =(int)$user_id;
                        $group_id=(int)$group_id;

                    //-----------------------------------
                    //檢核追蹤狀態
                    //-----------------------------------

                        $sql="
                            SELECT `mssr_forum_global`.`mssr_forum_track_group`.`user_id`
                            FROM  `mssr_forum_global`.`mssr_forum_track_group`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_track_group`.`user_id` ={$sess_user_id}
                                AND `mssr_forum_global`.`mssr_forum_track_group`.`group_id`={$group_id    }
                        ";
                        $track_group_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $user_id =(int)$user_id;
                    $group_id=(int)$group_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    switch(count($track_group_results)){

                        case 1:
                            $sql="
                                # for mssr_forum_track_group
                                DELETE FROM `mssr_forum_global`.`mssr_forum_track_group`
                                WHERE 1=1
                                    AND `user_id`   ={$sess_user_id }
                                    AND `group_id`  ={$group_id     }
                                LIMIT 1;
                            ";
                        break;

                        default:
                            $sql="
                                # for mssr_forum_track_group
                                INSERT INTO `mssr_forum_global`.`mssr_forum_track_group` SET
                                    `user_id`       = {$sess_user_id    } ,
                                    `group_id`      = {$group_id        } ,
                                    `keyin_cdate`   = NOW()               ;
                            ";
                        break;

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

                    $msg="追蹤小組成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_best_article_category()
        //用途: 建立精華區類別
        //-----------------------------------------------

            function add_best_article_category($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_best_article_category()
            //用途: 建立精華區類別
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
                //cat_name

                    if(!empty($_POST)){
                        $post_chk=array(
                            'group_id',
                            'cat_name'
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
                    $cat_name=trim($_POST[trim('cat_name')]);

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
                    if($cat_name===''){
                       $arry_err[]='精華區文章類別名稱,未輸入!';
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
                        $cat_name =mysql_prep(trim($cat_name));
                        $arry_msg =[];

                    //-----------------------------------
                    //檢核是否重複建立
                    //-----------------------------------

                        $sql="
                            SELECT `mssr_forum_global`.`mssr_forum_best_article_category`.`cat_name`
                            FROM `mssr_forum_global`.`mssr_forum_best_article_category`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_best_article_category`.`group_id`= {$group_id}
                                AND `mssr_forum_global`.`mssr_forum_best_article_category`.`cat_name`='{$cat_name}'
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                        if(!empty($db_results)){
                            $arry_msg['msg']="類別名稱重複";
                            die(json_encode($arry_msg,true));
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $group_id=(int)$group_id;
                    $cat_name=mysql_prep(strip_tags(trim($cat_name)));

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_best_article_category
                        INSERT INTO `mssr_forum_global`.`mssr_forum_best_article_category` SET
                            `create_by`     = {$sess_user_id    } ,
                            `group_id`      = {$group_id        } ,
                            `cat_id`        = NULL                ,
                            `cat_name`      ='{$cat_name        }',
                            `cat_state`     = 1                   ,
                            `keyin_cdate`   = NOW()               ;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                    //lastInsertId
                    $last_cat_id=(int)$conn_mssr->lastInsertId();

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $arry_msg['msg']   ="建立成功";
                    $arry_msg['cat_id']=(int)$last_cat_id;
                    die(json_encode($arry_msg,true));
            }

        //-----------------------------------------------
        //函式: add_group_booklist()
        //用途: 建立小組書單
        //-----------------------------------------------

            function add_group_booklist($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_group_booklist()
            //用途: 建立小組書單
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;
                    global $arry_conn_mssr_tw;
                    global $arry_conn_mssr_hk;

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
                    $sess_country_code=trim($_SESSION['mssr_forum_global']['country_code']);

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

                        $arry_conn_mssr_country =get_conn_country($sess_user_id,$sess_account);
                        $conn_mssr_country      =conn($db_type='mysql',$arry_conn_mssr_country);
                        $conn_mssr              =conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $group_id =(int)$group_id;
                        $book_sid=mysql_prep(trim($book_sid));

                    //-----------------------------------
                    //檢核是否重複建立
                    //-----------------------------------

                        $sql="
                            SELECT `mssr_forum_global`.`mssr_forum_group_booklist`.`book_sid`
                            FROM `mssr_forum_global`.`mssr_forum_group_booklist`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_group_booklist`.`group_id`     = {$group_id}
                                AND `mssr_forum_global`.`mssr_forum_group_booklist`.`book_sid`     ='{$book_sid}'
                                AND `mssr_forum_global`.`mssr_forum_group_booklist`.`country_code` ='{$sess_country_code}'
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                        if(!empty($db_results)){
                            $msg="書籍已在清單中";
                            die($msg);
                        }

                    //-----------------------------------
                    //檢核書籍資訊
                    //-----------------------------------

                        $arry_book_infos=get_book_info($conn_mssr_country,$book_sid,$array_filter=array('book_isbn_10','book_isbn_13'),$arry_conn_mssr_country);
                        if(empty($arry_book_infos)){
                            $msg="書籍不存在";
                            die($msg);
                        }else{
                            $book_isbn_10=trim($arry_book_infos[0]['book_isbn_10']);
                            $book_isbn_13=trim($arry_book_infos[0]['book_isbn_13']);
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $group_id    =(int)$group_id;
                    $book_sid    =mysql_prep(strip_tags(trim($book_sid)));
                    $book_isbn_10=mysql_prep(strip_tags(trim($book_isbn_10)));
                    $book_isbn_13=mysql_prep(strip_tags(trim($book_isbn_13)));

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_group_booklist
                        INSERT INTO `mssr_forum_global`.`mssr_forum_group_booklist` SET
                            `create_by`     = {$sess_user_id        } ,
                            `group_id`      = {$group_id            } ,
                            `book_sid`      ='{$book_sid            }',
                            `book_isbn_10`  ='{$book_isbn_10        }',
                            `book_isbn_13`  ='{$book_isbn_13        }',
                            `country_code`  ='{$sess_country_code   }',
                            `keyin_cdate`   = NOW()                   ;
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

                    $msg="建立成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_track_book()
        //用途: 追蹤書籍
        //-----------------------------------------------

            function add_track_book($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_track_book()
            //用途: 追蹤書籍
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
                //book_sid

                    if(!empty($_POST)){
                        $post_chk=array(
                            'user_id  ',
                            'book_sid '
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
                    $user_id  =trim($_POST[trim('user_id  ')]);
                    $book_sid =trim($_POST[trim('book_sid ')]);

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

                        $user_id =(int)$user_id;
                        $book_sid=mysql_prep(trim($book_sid));

                    //-----------------------------------
                    //檢核追蹤狀態
                    //-----------------------------------

                        $sql="
                            SELECT `mssr_forum_global`.`mssr_forum_track_book`.`user_id`
                            FROM  `mssr_forum_global`.`mssr_forum_track_book`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_track_book`.`user_id`   = {$sess_user_id}
                                AND `mssr_forum_global`.`mssr_forum_track_book`.`book_sid`  ='{$book_sid    }'
                        ";
                        $track_book_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $user_id =(int)$user_id;
                    $book_sid=mysql_prep(trim($book_sid));

                //---------------------------------------
                //處理
                //---------------------------------------

                    switch(count($track_book_results)){

                        case 1:
                            $sql="
                                # for mssr_forum_track_book
                                DELETE FROM `mssr_forum_global`.`mssr_forum_track_book`
                                WHERE 1=1
                                    AND `user_id`   = {$sess_user_id    }
                                    AND `book_sid`  ='{$book_sid        }'
                                LIMIT 1;
                            ";
                        break;

                        default:
                            $sql="
                                # for mssr_forum_track_book
                                INSERT INTO `mssr_forum_global`.`mssr_forum_track_book` SET
                                    `user_id`       = {$sess_user_id    } ,
                                    `book_sid`      ='{$book_sid        }',
                                    `keyin_cdate`   = NOW()               ;
                            ";
                        break;

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

                    $msg="追蹤書籍成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_request_article()
        //用途: 新增邀請(討論文章)
        //-----------------------------------------------

            function add_request_article($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_request_article()
            //用途: 新增邀請(討論文章)
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
                //request_article_friend_name
                //article_id
                //group_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'request_article_friend_name',
                            'article_id                 ',
                            'group_id                   '
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
                    $arry_friend_name =array_map("trim",$_POST[trim('request_article_friend_name')]);
                    $article_id       =trim($_POST[trim('article_id               ')]);
                    $group_id         =trim($_POST[trim('group_id                 ')]);

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

                    if(empty($arry_friend_name)){
                       $arry_err[]='好友,未輸入!';
                    }
                    if($article_id===''){
                       $arry_err[]='文章主索引,未輸入!';
                    }else{
                        $article_id=(int)$article_id;
                        if($article_id===0){
                            $arry_err[]='文章主索引,錯誤!';
                        }
                    }
                    if($group_id===''){
                       $arry_err[]='小組主索引,未輸入!';
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

                        $arry_friend_id  =array();
                        $arry_friend_name=$arry_friend_name;
                        $article_id      =(int)$article_id;
                        $group_id        =(int)$group_id;

                    //-----------------------------------
                    //檢核使用者主索引
                    //-----------------------------------

                        $tmp_list_friend_name=implode("','",$arry_friend_name);

                        $sql="
                            SELECT `user`.`member`.`uid`
                            FROM `user`.`member`
                            WHERE 1=1
                                AND `user`.`member`.`name` IN ('{$tmp_list_friend_name}')
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        if(!empty($db_results)){
                            $tmp_arry_friend_id=array();
                            foreach($db_results as $db_result){
                                $rs_uid=(int)$db_result['uid'];
                                $tmp_arry_friend_id[]=$rs_uid;
                            }
                            $tmp_list_friend_id=implode(",",$tmp_arry_friend_id);
                            $sql="
                                SELECT
                                    `mssr_forum_global`.`mssr_forum_friend`.`user_id`,
                                    `mssr_forum_global`.`mssr_forum_friend`.`friend_id`
                                FROM `mssr_forum_global`.`mssr_forum_friend`
                                WHERE 1=1
                                    AND (
                                        `mssr_forum_global`.`mssr_forum_friend`.`user_id`  ={$sess_user_id}
                                        OR
                                        `mssr_forum_global`.`mssr_forum_friend`.`friend_id`={$sess_user_id}
                                    )
                                    AND (
                                        `mssr_forum_global`.`mssr_forum_friend`.`user_id`  IN({$tmp_list_friend_id})
                                        OR
                                        `mssr_forum_global`.`mssr_forum_friend`.`friend_id`IN({$tmp_list_friend_id})
                                    )
                                    AND `mssr_forum_global`.`mssr_forum_friend`.`friend_state`=1
                            ";
                            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                            if(!empty($db_results)){
                                foreach($db_results as $db_result){
                                    $rs_user_id  =(int)$db_result['user_id'];
                                    $rs_friend_id=(int)$db_result['friend_id'];
                                    if($rs_user_id!==$sess_user_id)$arry_friend_id[]=$rs_user_id;
                                    if($rs_friend_id!==$sess_user_id)$arry_friend_id[]=$rs_friend_id;
                                }
                            }else{
                                $msg="發生嚴重錯誤";
                                $jscript_back="
                                    <script>
                                        alert('{$msg}');
                                        location.href='{$send_url}';
                                    </script>
                                ";
                                die($jscript_back);
                            }
                        }else{
                            $msg="發生嚴重錯誤";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    location.href='{$send_url}';
                                </script>
                            ";
                            die($jscript_back);
                        }
                        if(empty($arry_friend_id)){
                            $msg="發生嚴重錯誤";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    location.href='{$send_url}';
                                </script>
                            ";
                            die($jscript_back);
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $arry_friend_id =$arry_friend_id;
                    $article_id     =(int)$article_id;
                    $group_id       =(int)$group_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    foreach($arry_friend_id as $friend_id){

                        $friend_id=(int)$friend_id;

                        //檢核是否已邀請過
                        $sql="
                            SELECT
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_id`,
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_state`
                            FROM `mssr_forum_global`.`mssr_forum_user_request`
                                INNER JOIN `mssr_forum_global`.`mssr_forum_user_request_article_rev` ON
                                `mssr_forum_global`.`mssr_forum_user_request`.`request_id`=`mssr_forum_user_request_article_rev`.`request_id`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_user_request`.`request_from`={$sess_user_id}
                                AND `mssr_forum_global`.`mssr_forum_user_request`.`request_to`={$friend_id}
                                AND `mssr_forum_global`.`mssr_forum_user_request_article_rev`.`article_id`={$article_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                        if(!empty($db_results)){
                            $rs_request_id   =(int)$db_results[0]['request_id'];
                            $rs_request_state=(int)$db_results[0]['request_state'];
                            if($rs_request_state===1 || $rs_request_state===3){
                                continue;
                            }else{
                                $sql="
                                    # for mssr_forum_user_request
                                    UPDATE `mssr_forum_global`.`mssr_forum_user_request` SET
                                        `request_state`  = 3,
                                        `request_read`   = 2
                                    WHERE 1=1
                                        AND `request_id` = {$rs_request_id}
                                    LIMIT 1;
                                ";
                                //送出
                                $err ='DB QUERY FAIL(1)';
                                $sth=$conn_mssr->prepare($sql);
                                $sth->execute()or die($err);
                            }
                        }else{
                            $sql="
                                # for mssr_forum_user_request
                                INSERT INTO `mssr_forum_global`.`mssr_forum_user_request` SET
                                    `request_from`      = {$sess_user_id    } ,
                                    `request_to`        = {$friend_id       } ,
                                    `request_id`        = NULL                ,
                                    `request_state`     = 3                   ,
                                    `request_read`      = 2                   ,
                                    `keyin_cdate`       = NOW()               ,
                                    `keyin_mdate`       = NULL                ;
                            ";
                            //送出
                            $err ='DB QUERY FAIL(2)';
                            $sth=$conn_mssr->prepare($sql);
                            $sth->execute()or die($err);

                            //lastInsertId
                            $last_request_id=(int)$conn_mssr->lastInsertId();

                            $sql="
                                # for mssr_forum_user_request_article_rev
                                INSERT INTO `mssr_forum_global`.`mssr_forum_user_request_article_rev` SET
                                    `request_id`    = {$last_request_id } ,
                                    `group_id`      = {$group_id        } ,
                                    `article_id`    = {$article_id      } ,
                                    `rev_id`        = NULL                ;
                            ";
                            //送出
                            $err ='DB QUERY FAIL(3)';
                            $sth=$conn_mssr->prepare($sql);
                            $sth->execute()or die($err);
                        }
                    }

                    //switch(count($db_results)){
                    //
                    //    case 1:
                    //
                    //        $sql="
                    //            # for mssr_forum_user_request
                    //            UPDATE `mssr_forum_global`.`mssr_forum_user_request` SET
                    //                `request_state`  = 3,
                    //                `request_read`   = 2
                    //            WHERE 1=1
                    //                AND `request_id` = {$rs_request_id}
                    //            LIMIT 1;
                    //        ";
                    //        //送出
                    //        $err ='DB QUERY FAIL(1)';
                    //        $sth=$conn_mssr->prepare($sql);
                    //        $sth->execute()or die($err);
                    //
                    //    break;
                    //
                    //    default:
                    //
                    //        $sql="
                    //            # for mssr_forum_user_request
                    //            INSERT INTO `mssr_forum_global`.`mssr_forum_user_request` SET
                    //                `request_from`      = {$sess_user_id    } ,
                    //                `request_to`        = {$friend_id       } ,
                    //                `request_id`        = NULL                ,
                    //                `request_state`     = 3                   ,
                    //                `request_read`      = 2                   ,
                    //                `keyin_cdate`       = NOW()               ,
                    //                `keyin_mdate`       = NULL                ;
                    //        ";
                    //        //送出
                    //        $err ='DB QUERY FAIL(2)';
                    //        $sth=$conn_mssr->prepare($sql);
                    //        $sth->execute()or die($err);
                    //
                    //        //lastInsertId
                    //        $last_request_id=(int)$conn_mssr->lastInsertId();
                    //
                    //        $sql="
                    //            # for mssr_forum_user_request_article_rev
                    //            INSERT INTO `mssr_forum_global`.`mssr_forum_user_request_article_rev` SET
                    //                `request_id`    = {$last_request_id } ,
                    //                `group_id`      = {$group_id        } ,
                    //                `article_id`    = {$article_id      } ,
                    //                `rev_id`        = NULL                ;
                    //        ";
                    //        //送出
                    //        $err ='DB QUERY FAIL(3)';
                    //        $sth=$conn_mssr->prepare($sql);
                    //        $sth->execute()or die($err);
                    //
                    //    break;
                    //
                    //}

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="邀請成功，請耐心等待好友回應";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            location.href='{$send_url}';
                        </script>
                    ";
                    die($jscript_back);
            }

        //-----------------------------------------------
        //函式: add_group()
        //用途: 建立小組
        //-----------------------------------------------

            function add_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_group()
            //用途: 建立小組
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
                //group_name
                //group_content
                //group_rule
                //friend_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'group_type   ',
                            'group_name   ',
                            'group_content',
                            'group_rule   ',
                            'friend_id    '
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
                    $group_name   =trim($_POST[trim('group_name   ')]);
                    $group_content=trim($_POST[trim('group_content')]);
                    $group_rule   =trim($_POST[trim('group_rule   ')]);
                    $friend_ids   =array_map("trim",$_POST[trim('friend_id')]);

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
                    foreach($friend_ids as $friend_id){
                        if($friend_id===''){
                            $arry_err[]='好友主索引,未輸入!';
                        }else{
                            $friend_id=(int)$friend_id;
                            if($friend_id===0){
                                $arry_err[]='好友主索引,錯誤!';
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

                        $friend_ids=array_map("trim",$_POST[trim('friend_id')]);
                        $group_name=mysql_prep(($group_name));

                    //-----------------------------------
                    //檢核小組名稱是否重複
                    //-----------------------------------

                        $sql="
                            SELECT `mssr_forum_global`.`mssr_forum_group`.`group_name`
                            FROM `mssr_forum_global`.`mssr_forum_group`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_group`.`group_name`='{$group_name}'
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                        if(!empty($db_results)){
                            $msg="小組名稱重複";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    history.back(-1);
                                </script>
                            ";
                            die($jscript_back);
                        }

                    //-----------------------------------
                    //檢核好友是否存在
                    //-----------------------------------

                        foreach($friend_ids as $friend_id){
                            $friend_id=(int)$friend_id;
                            $sql="
                                SELECT
                                    `user`.`member`.`uid`
                                FROM `user`.`member`
                                WHERE 1=1
                                    AND `user`.`member`.`uid`={$friend_id}
                                    AND `user`.`member`.`permission`<>'x'
                            ";
                            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                            if(empty($db_results)){
                                $msg="好友不存在";
                                $jscript_back="
                                    <script>
                                        alert('{$msg}');
                                        history.back(-1);
                                    </script>
                                ";
                                die($jscript_back);
                            }
                        }

                    //-----------------------------------
                    //檢核帶班老師是否存在
                    //-----------------------------------

                        $sql="
                            SELECT
                                `user`.`teacher`.`uid`
                            FROM `user`.`teacher`
                                INNER JOIN `user`.`student` ON
                                `user`.`teacher`.`class_code`=`user`.`student`.`class_code`
                            WHERE 1=1
                                AND `user`.`student`.`uid`={$sess_user_id}
                                AND CURDATE() BETWEEN `user`.`student`.`start` AND `user`.`student`.`end`
                                AND CURDATE() BETWEEN `user`.`teacher`.`start` AND `user`.`teacher`.`end`
                            GROUP BY `user`.`teacher`.`uid`
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        $arry_teacher_uid=array();
                        if(!empty($db_results)){
                            foreach($db_results as $db_result){
                                $arry_teacher_uid[]=(int)$db_result['uid'];
                            }
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $group_type         =(int)$group_type;
                    $group_name         =mysql_prep(strip_tags($group_name));
                    $group_content      =mysql_prep(strip_tags($group_content));
                    $group_rule         =mysql_prep(strip_tags($group_rule));
                    $friend_ids         =array_map("trim",$_POST[trim('friend_id')]);
                    $arry_teacher_uid   =array_map("trim",$arry_teacher_uid);

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_group
                        INSERT INTO `mssr_forum_global`.`mssr_forum_group` SET
                            `create_by`         = {$sess_user_id    } ,
                            `edit_by`           = {$sess_user_id    } ,
                            `group_id`          = NULL                ,
                            `group_name`        ='{$group_name      }',
                            `group_content`     ='{$group_content   }',
                            `group_rule`        ='{$group_rule      }',
                            `group_type`        = {$group_type      } ,
                            `group_state`       =3                    ,
                            `keyin_cdate`       =NOW()                ,
                            `keyin_mdate`       = NULL                ;
                    ";
                    //送出
                    $err ='DB QUERY FAIL(1)';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                    //lastInsertId
                    $last_group_id=(int)$conn_mssr->lastInsertId();

                    $sql="";
                    if(!in_array($sess_user_id,$arry_teacher_uid)){
                        $sql.="
                            # for mssr_forum_group_user_rev
                            INSERT INTO `mssr_forum_global`.`mssr_forum_group_user_rev` SET
                                `edit_by`           = {$sess_user_id    } ,
                                `group_id`          = {$last_group_id   } ,
                                `user_id`           = {$sess_user_id    } ,
                                `user_type`         =2                    ,
                                `user_state`        =1                    ,
                                `user_intro`        =''                   ,
                                `keyin_cdate`       =NOW()                ,
                                `keyin_mdate`       = NULL                ;
                        ";
                    }
                    foreach($arry_teacher_uid as $teacher_uid){
                        $teacher_uid=(int)$teacher_uid;
                        $sql.="
                            # for mssr_forum_group_user_rev
                            INSERT INTO `mssr_forum_global`.`mssr_forum_group_user_rev` SET
                                `edit_by`           = {$teacher_uid     } ,
                                `group_id`          = {$last_group_id   } ,
                                `user_id`           = {$teacher_uid     } ,
                                `user_type`         =3                    ,
                                `user_state`        =1                    ,
                                `user_intro`        =''                   ,
                                `keyin_cdate`       =NOW()                ,
                                `keyin_mdate`       = NULL                ;
                        ";
                    }
                    //foreach($friend_ids as $friend_id){
                    //    $friend_id=(int)$friend_id;
                    //    $sql.="
                    //        # for mssr_forum_group_user_rev
                    //        INSERT INTO `mssr_forum_global`.`mssr_forum_group_user_rev` SET
                    //            `edit_by`           = {$friend_id       } ,
                    //            `group_id`          = {$last_group_id   } ,
                    //            `user_id`           = {$friend_id       } ,
                    //            `user_type`         =1                    ,
                    //            `user_state`        =1                    ,
                    //            `user_intro`        =''                   ,
                    //            `keyin_cdate`       =NOW()                ,
                    //            `keyin_mdate`       = NULL                ;
                    //    ";
                    //}
                    //送出
                    $err ='DB QUERY FAIL(2)';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                    foreach($friend_ids as $friend_id){
                        $friend_id=(int)$friend_id;
                        $sql="
                            # for mssr_forum_user_request
                            INSERT INTO `mssr_forum_global`.`mssr_forum_user_request` SET
                                `request_from`      = {$sess_user_id    } ,
                                `request_to`        = {$friend_id       } ,
                                `request_id`        =NULL                 ,
                                `request_state`     =3                    ,
                                `request_read`      =2                    ,
                                `keyin_cdate`       =NOW()                ,
                                `keyin_mdate`       = NULL                ;
                        ";
                        //送出
                        $err ='DB QUERY FAIL(3)';
                        $sth=$conn_mssr->prepare($sql);
                        $sth->execute()or die($err);

                        //lastInsertId
                        $last_request_id=(int)$conn_mssr->lastInsertId();

                        $sql="
                            # for mssr_forum_user_request_create_group_rev
                            INSERT INTO `mssr_forum_global`.`mssr_forum_user_request_create_group_rev` SET
                                `request_id`    = {$last_request_id } ,
                                `group_id`      = {$last_group_id   } ,
                                `rev_id`        =NULL                 ;
                        ";
                        //送出
                        $err ='DB QUERY FAIL(4)';
                        $sth=$conn_mssr->prepare($sql);
                        $sth->execute()or die($err);
                    }

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="建立小組成功，請耐心等待好友聯署";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            location.href='../view/user.php?user_id={$sess_user_id}&tab=4';
                        </script>
                    ";
                    die($jscript_back);
            }

        //-----------------------------------------------
        //函式: add_report_article()
        //用途: 按文章檢舉
        //-----------------------------------------------

            function add_report_article($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_report_article()
            //用途: 按文章檢舉
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
                //article_id
                //reply_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'user_id   ',
                            'article_id',
                            'reply_id  '
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
                    $user_id   =trim($_POST[trim('user_id   ')]);
                    $article_id=trim($_POST[trim('article_id')]);
                    $reply_id  =trim($_POST[trim('reply_id  ')]);

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
                    if($article_id===''){
                       $arry_err[]='文章主索引,未輸入!';
                    }else{
                        $article_id=(int)$article_id;
                        if($article_id===0){
                            $arry_err[]='文章主索引,錯誤!';
                        }
                    }
                    if($reply_id===''){
                       $arry_err[]='回覆主索引,未輸入!';
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

                        $user_id        =(int)$user_id;
                        $article_id     =(int)$article_id;
                        $reply_id       =(int)$reply_id;
                        $has_report_cno =0;

                    //-----------------------------------
                    //檢核按檢舉狀態
                    //-----------------------------------

                        switch($reply_id){
                            case 0:
                                $sql="
                                    SELECT
                                        `mssr_forum_global`.`mssr_forum_article_report_log`.`log_id`
                                    FROM `mssr_forum_global`.`mssr_forum_article_report_log`
                                    WHERE 1=1
                                        AND `mssr_forum_global`.`mssr_forum_article_report_log`.`user_id`   ={$user_id   }
                                        AND `mssr_forum_global`.`mssr_forum_article_report_log`.`article_id`={$article_id}
                                ";
                                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                                if(empty($db_results)){$flag='add';}else{
                                    $msg="你已經檢舉過了";
                                    die($msg);
                                }

                                $sql="
                                    SELECT
                                        `mssr_forum_global`.`mssr_forum_article_report_log`.`log_id`
                                    FROM `mssr_forum_global`.`mssr_forum_article_report_log`
                                    WHERE 1=1
                                        AND `mssr_forum_global`.`mssr_forum_article_report_log`.`article_id`={$article_id}
                                ";
                                $has_report_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                                $has_report_cno=count($has_report_results);

                                $sql="
                                    SELECT
                                        `mssr_forum_global`.`mssr_forum_article_like_log`.`log_id`
                                    FROM `mssr_forum_global`.`mssr_forum_article_like_log`
                                    WHERE 1=1
                                        AND `mssr_forum_global`.`mssr_forum_article_like_log`.`article_id`={$article_id}
                                ";
                                $has_like_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                                $has_like_cno=count($has_like_results);
                                if($has_like_cno<=0)$has_like_cno=1;
                            break;

                            default:
                                $sql="
                                    SELECT
                                        `mssr_forum_global`.`mssr_forum_reply_report_log`.`user_id`
                                    FROM  `mssr_forum_global`.`mssr_forum_reply_report_log`
                                    WHERE 1=1
                                        AND `mssr_forum_global`.`mssr_forum_reply_report_log`.`user_id` ={$user_id }
                                        AND `mssr_forum_global`.`mssr_forum_reply_report_log`.`reply_id`={$reply_id}
                                ";
                                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                                if(empty($db_results)){$flag='add';}else{
                                    $msg="你已經檢舉過了";
                                    die($msg);
                                }

                                $sql="
                                    SELECT
                                        `mssr_forum_global`.`mssr_forum_reply_report_log`.`user_id`
                                    FROM  `mssr_forum_global`.`mssr_forum_reply_report_log`
                                    WHERE 1=1
                                        AND `mssr_forum_global`.`mssr_forum_reply_report_log`.`reply_id`={$reply_id}
                                ";
                                $has_report_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                                $has_report_cno=count($has_report_results);

                                $sql="
                                    SELECT
                                        `mssr_forum_global`.`mssr_forum_reply_like_log`.`user_id`
                                    FROM  `mssr_forum_global`.`mssr_forum_reply_like_log`
                                    WHERE 1=1
                                        AND `mssr_forum_global`.`mssr_forum_reply_like_log`.`reply_id`={$reply_id}
                                ";
                                $has_like_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                                $has_like_cno=count($has_like_results);
                                if($has_like_cno<=0)$has_like_cno=1;
                            break;
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $user_id        =(int)$user_id;
                    $article_id     =(int)$article_id;
                    $reply_id       =(int)$reply_id;
                    $has_report_cno =(int)$has_report_cno;

                //---------------------------------------
                //處理
                //---------------------------------------

                    switch($reply_id){
                        case 0:
                            if($flag==='add'){
                                $sql="
                                    # for mssr_forum_article_report_log
                                    INSERT INTO `mssr_forum_global`.`mssr_forum_article_report_log` SET
                                        `user_id`       = {$user_id         },
                                        `article_id`    = {$article_id      },
                                        `log_id`        = NULL               ,
                                        `keyin_mdate`   = NULL               ;

                                    # for mssr_forum_article
                                    UPDATE `mssr_forum_global`.`mssr_forum_article` SET
                                        `article_report_cno`  = `article_report_cno`+1
                                    WHERE 1=1
                                        AND `article_id`= {$article_id      }
                                    LIMIT 1;
                                ";
                                if($has_report_cno>=($has_like_cno*6)){
                                    $sql.="
                                        # for mssr_forum_article
                                        UPDATE `mssr_forum_global`.`mssr_forum_article` SET
                                            `article_state`  = 2
                                        WHERE 1=1
                                            AND `article_id`= {$article_id      }
                                        LIMIT 1;
                                    ";
                                }
                            }else{
                                $sql="
                                    # for mssr_forum_article_report_log
                                    DELETE FROM `mssr_forum_global`.`mssr_forum_article_report_log`
                                    WHERE 1=1
                                        AND `user_id`   = {$user_id         }
                                        AND `article_id`= {$article_id      };

                                    # for mssr_forum_article
                                    UPDATE `mssr_forum_global`.`mssr_forum_article` SET
                                        `article_report_cno`  = `article_report_cno`-1
                                    WHERE 1=1
                                        AND `article_id`= {$article_id      }
                                    LIMIT 1;
                                ";
                            }
                        break;

                        default:
                            if($flag==='add'){
                                $sql="
                                    # for mssr_forum_reply_report_log
                                    INSERT INTO `mssr_forum_global`.`mssr_forum_reply_report_log` SET
                                        `user_id`       = {$user_id         },
                                        `article_id`    = {$article_id      },
                                        `reply_id`      = {$reply_id        },
                                        `log_id`        = NULL               ,
                                        `keyin_mdate`   = NULL               ;

                                    # for mssr_forum_reply
                                    UPDATE `mssr_forum_global`.`mssr_forum_reply` SET
                                        `reply_report_cno`  = `reply_report_cno`+1
                                    WHERE 1=1
                                        AND `reply_id`= {$reply_id          }
                                    LIMIT 1;
                                ";
                                if($has_report_cno>=($has_like_cno*6)){
                                    $sql.="
                                        # for mssr_forum_reply
                                        UPDATE `mssr_forum_global`.`mssr_forum_reply` SET
                                            `reply_state`  = 2
                                        WHERE 1=1
                                            AND `reply_id`= {$reply_id          }
                                        LIMIT 1;
                                    ";
                                }
                            }else{
                                $sql="
                                    # for mssr_forum_reply_report_log
                                    DELETE FROM `mssr_forum_global`.`mssr_forum_reply_report_log`
                                    WHERE 1=1
                                        AND `user_id`   = {$user_id         }
                                        AND `reply_id`  = {$reply_id        };

                                    # for mssr_forum_reply
                                    UPDATE `mssr_forum_global`.`mssr_forum_reply` SET
                                        `reply_report_cno`  = `reply_report_cno`-1
                                    WHERE 1=1
                                        AND `reply_id`  = {$reply_id        }
                                    LIMIT 1;
                                ";
                            }
                        break;
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

                    $msg="檢舉成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_like_article()
        //用途: 按文章讚
        //-----------------------------------------------

            function add_like_article($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_like_article()
            //用途: 按文章讚
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
                //article_id
                //reply_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'user_id   ',
                            'article_id',
                            'reply_id  '
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
                    $user_id   =trim($_POST[trim('user_id   ')]);
                    $article_id=trim($_POST[trim('article_id')]);
                    $reply_id  =trim($_POST[trim('reply_id  ')]);

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
                    if($article_id===''){
                       $arry_err[]='文章主索引,未輸入!';
                    }else{
                        $article_id=(int)$article_id;
                        if($article_id===0){
                            $arry_err[]='文章主索引,錯誤!';
                        }
                    }
                    if($reply_id===''){
                       $arry_err[]='回覆主索引,未輸入!';
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

                        $user_id   =(int)$user_id;
                        $article_id=(int)$article_id;
                        $reply_id  =(int)$reply_id;
                        $sess_country_code=trim($sess_country_code);

                    //-----------------------------------
                    //檢核按讚狀態
                    //-----------------------------------

                        switch($reply_id){
                            case 0:
                                $sql="
                                    SELECT
                                        `mssr_forum_global`.`mssr_forum_article_like_log`.`log_id`
                                    FROM `mssr_forum_global`.`mssr_forum_article_like_log`
                                    WHERE 1=1
                                        AND `mssr_forum_global`.`mssr_forum_article_like_log`.`user_id`   ={$user_id   }
                                        AND `mssr_forum_global`.`mssr_forum_article_like_log`.`article_id`={$article_id}
                                        AND `mssr_forum_global`.`mssr_forum_article_like_log`.`user_country_code`='{$sess_country_code}'
                                ";
                                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                                if(empty($db_results)){$flag='add';}else{$flag='del';}
                            break;

                            default:
                                $sql="
                                    SELECT
                                        `mssr_forum_global`.`mssr_forum_reply_like_log`.`log_id`
                                    FROM `mssr_forum_global`.`mssr_forum_reply_like_log`
                                    WHERE 1=1
                                        AND `mssr_forum_global`.`mssr_forum_reply_like_log`.`user_id`  ={$user_id  }
                                        AND `mssr_forum_global`.`mssr_forum_reply_like_log`.`reply_id` ={$reply_id }
                                        AND `mssr_forum_global`.`mssr_forum_reply_like_log`.`user_country_code`='{$sess_country_code}'
                                ";
                                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                                if(empty($db_results)){$flag='add';}else{$flag='del';}
                            break;
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $user_id   =(int)$user_id;
                    $article_id=(int)$article_id;
                    $reply_id  =(int)$reply_id;
                    $sess_country_code=trim($sess_country_code);

                //---------------------------------------
                //處理
                //---------------------------------------

                    switch($reply_id){
                        case 0:
                            if($flag==='add'){
                                $sql="
                                    # for mssr_forum_article_like_log
                                    INSERT INTO `mssr_forum_global`.`mssr_forum_article_like_log` SET
                                        `user_id`       = {$user_id         },
                                        `user_country_code`= '{$sess_country_code}',
                                        `article_id`    = {$article_id      },
                                        `log_id`        = NULL               ,
                                        `keyin_mdate`   = NULL               ;

                                    # for mssr_forum_article
                                    UPDATE `mssr_forum_global`.`mssr_forum_article` SET
                                        `article_like_cno`  = `article_like_cno`+1
                                    WHERE 1=1
                                        AND `article_id`= {$article_id      }
                                    LIMIT 1;
                                ";
                            }else{
                                $sql="
                                    # for mssr_forum_article_like_log
                                    DELETE FROM `mssr_forum_global`.`mssr_forum_article_like_log`
                                    WHERE 1=1
                                        AND `user_id`   = {$user_id         }
                                        AND `user_country_code`='{$sess_country_code}'
                                        AND `article_id`= {$article_id      };

                                    # for mssr_forum_article
                                    UPDATE `mssr_forum_global`.`mssr_forum_article` SET
                                        `article_like_cno`  = `article_like_cno`-1
                                    WHERE 1=1
                                        AND `article_id`= {$article_id      }
                                    LIMIT 1;
                                ";
                            }
                        break;

                        default:
                            if($flag==='add'){
                                $sql="
                                    # for mssr_forum_reply_like_log
                                    INSERT INTO `mssr_forum_global`.`mssr_forum_reply_like_log` SET
                                        `user_id`       = {$user_id         },
                                        `user_country_code`= '{$sess_country_code}',
                                        `article_id`    = {$article_id      },
                                        `reply_id`      = {$reply_id        },
                                        `log_id`        = NULL               ,
                                        `keyin_mdate`   = NULL               ;

                                    # for mssr_forum_reply
                                    UPDATE `mssr_forum_global`.`mssr_forum_reply` SET
                                        `reply_like_cno`= `reply_like_cno`+1
                                    WHERE 1=1
                                        AND `reply_id`  = {$reply_id        }
                                    LIMIT 1;
                                ";
                            }else{
                                $sql="
                                    # for mssr_forum_reply_like_log
                                    DELETE FROM `mssr_forum_global`.`mssr_forum_reply_like_log`
                                    WHERE 1=1
                                        AND `user_id`   = {$user_id         }
                                        AND `user_country_code`='{$sess_country_code}'
                                        AND `reply_id`  = {$reply_id        };

                                    # for mssr_forum_reply
                                    UPDATE `mssr_forum_global`.`mssr_forum_reply` SET
                                        `reply_like_cno`= `reply_like_cno`-1
                                    WHERE 1=1
                                        AND `reply_id`  = {$reply_id        }
                                    LIMIT 1;
                                ";
                            }
                        break;
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

                    $msg="按文章讚成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_friend()
        //用途: 加為好友
        //-----------------------------------------------

            function add_friend($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_friend()
            //用途: 加為好友
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
                //friend_id
                //content

                    if(!empty($_POST)){
                        $post_chk=array(
                            'user_id  ',
                            'friend_id'
                            //'content  '
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
                    $user_id  =trim($_POST[trim('user_id  ')]);
                    $friend_id=trim($_POST[trim('friend_id')]);
                    $content  =(isset($_POST['content']))?trim($_POST['content']):'';

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

                        $user_id  =(int)$user_id;
                        $friend_id=(int)$friend_id;
                        $content  =trim($content);

                    //-----------------------------------
                    //檢核好友狀態
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum_global`.`mssr_forum_friend`.`create_by`,
                                `mssr_forum_global`.`mssr_forum_friend`.`user_id`,
                                `mssr_forum_global`.`mssr_forum_friend`.`friend_id`,
                                `mssr_forum_global`.`mssr_forum_friend`.`friend_state`
                            FROM `mssr_forum_global`.`mssr_forum_friend`
                            WHERE 1=1
                                AND (
                                    `mssr_forum_global`.`mssr_forum_friend`.`user_id`  ={$user_id}
                                    OR
                                    `mssr_forum_global`.`mssr_forum_friend`.`friend_id`={$user_id}
                                )
                                AND (
                                    `mssr_forum_global`.`mssr_forum_friend`.`user_id`  ={$friend_id}
                                    OR
                                    `mssr_forum_global`.`mssr_forum_friend`.`friend_id`={$friend_id}
                                )
                        ";
                        $friend_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                        if(!empty($friend_results)){
                            if(in_array((int)$friend_results[0]['friend_state'],array(1,3))){
                                $msg="已是好友或好友確認中";
                                die($msg);
                            }
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $user_id  =(int)$user_id;
                    $friend_id=(int)$friend_id;
                    $content  =bad_content_filter(mysql_prep(strip_tags(trim($content))));

                //---------------------------------------
                //處理
                //---------------------------------------

                    switch(count($friend_results)){
                        case 0:
                            $sql="
                                # for mssr_forum_friend
                                INSERT INTO `mssr_forum_global`.`mssr_forum_friend` SET
                                    `create_by`     = {$sess_user_id    } ,
                                    `user_id`       = {$sess_user_id    } ,
                                    `friend_id`     = {$friend_id       } ,
                                    `content`       ='{$content         }',
                                    `friend_state`  = 3                   ,
                                    `keyin_mdate`   = NULL                ;
                            ";
                        break;

                        case 1:
                            $sql="
                                # for mssr_forum_friend
                                UPDATE `mssr_forum_global`.`mssr_forum_friend` SET
                                    `create_by`     = {$sess_user_id} ,
                                    `user_id`       = {$sess_user_id} ,
                                    `friend_id`     = {$friend_id   } ,
                                    `content`       ='{$content     }',
                                    `friend_state`  = 3
                                WHERE 1=1
                                    AND (
                                        `mssr_forum_global`.`mssr_forum_friend`.`user_id`  ={$sess_user_id}
                                        OR
                                        `mssr_forum_global`.`mssr_forum_friend`.`friend_id`={$sess_user_id}
                                    )
                                    AND (
                                        `mssr_forum_global`.`mssr_forum_friend`.`user_id`  ={$friend_id}
                                        OR
                                        `mssr_forum_global`.`mssr_forum_friend`.`friend_id`={$friend_id}
                                    )
                                LIMIT 1;
                            ";
                        break;

                        default:
                            $msg="發生嚴重錯誤";
                            die($msg);
                        break;
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

                    $msg="加入好友成功，請等待對方回覆";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: reply_article()
        //用途: 回覆文章
        //-----------------------------------------------

            function reply_article($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: reply_article()
            //用途: 回覆文章
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;
                    global $file_server_enable;
                    global $arry_ftp1_info;
                    global $fso_enc;
                    global $page_enc;
                    global $conn_host_country_code;
                    global $arry_conn_mssr_tw;
                    global $arry_conn_mssr_hk;
                    global $arry_conn_mssr_sg;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //book_sid
                //reply_content
                //article_id
                //eagle_code
                //reply_from
                //group_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'book_sid     ',
                            'reply_content',
                            'article_id   ',
                            'eagle_code   ',
                            'reply_from   ',
                            'group_id     '
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
                    $book_sid       =trim($_POST[trim('book_sid                 ')]);
                    $article_id     =trim($_POST[trim('article_id               ')]);
                    $eagle_code     =trim($_POST[trim('eagle_code               ')]);
                    $reply_from     =trim($_POST[trim('reply_from               ')]);
                    $group_id       =trim($_POST[trim('group_id                 ')]);
                    $reply_contents =array_map("trim",$_POST[trim('reply_content')]);

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

                    //if(isset($_SESSION['mssr_forum_global']['uid'])&&isset($_SESSION['mssr_forum_global']['country_code'])){
                    //    $sess_country_code    =trim($_SESSION['mssr_forum_global']['country_code']);
                    //}

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($group_id===''){
                       $arry_err[]='小組主索引,未輸入!';
                    }
                    if($book_sid===''){
                       $arry_err[]='書本識別碼,未輸入!';
                    }
                    if($article_id===''){
                       $arry_err[]='文章主索引,未輸入!';
                    }else{
                        $article_id=(int)$article_id;
                        if($article_id===0){
                            $arry_err[]='文章主索引,錯誤!';
                        }
                    }
                    if($eagle_code===''){
                       $arry_err[]='鷹架代號,未輸入!';
                    }
                    if($reply_from===''){
                       $arry_err[]='回文來源,未輸入!';
                    }else{
                        $reply_from=(int)$reply_from;
                        if($reply_from===0){
                            $arry_err[]='回文來源,錯誤!';
                        }
                    }
                    foreach($reply_contents as $reply_content){
                        if($reply_content===''){
                            $arry_err[]='回文內容,未輸入!';
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

                        $arry_conn_mssr_country =get_conn_country($sess_user_id,$sess_account);
                        $conn_mssr_country      =conn($db_type='mysql',$arry_conn_mssr_country);
                        $conn_country_code      =trim($conn_host_country_code[$arry_conn_mssr_country['db_host']]);

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $book_sid=mysql_prep($book_sid);
                        $group_id=(int)$group_id;

                    //-----------------------------------
                    //檢核書籍是否存在
                    //-----------------------------------

                        $sql="
                            SELECT `mssr_forum_global`.`mssr_forum_group_booklist`.`country_code`
                            FROM `mssr_forum_global`.`mssr_forum_group_booklist`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_group_booklist`.`book_sid`='{$book_sid}'
                                AND `mssr_forum_global`.`mssr_forum_group_booklist`.`group_id`= {$group_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        if(empty($db_results)){
                            $msg="書籍不存在";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    location.href='{$send_url}';
                                </script>
                            ";
                            die($jscript_back);
                        }else{
                            $rs_country_code=trim($db_results[0]['country_code']);
                            $arry_conn="arry_conn_mssr_{$rs_country_code}";
                            $arry_conn=$$arry_conn;
                        }

                        $arry_book_infos=get_book_info('',$book_sid,$array_filter=array('book_isbn_10','book_isbn_13'),$arry_conn);
                        if(empty($arry_book_infos)){
                            $msg="書籍不存在";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    location.href='{$send_url}';
                                </script>
                            ";
                            die($jscript_back);
                        }else{
                            $book_isbn_10='';
                            if(trim($arry_book_infos[0]['book_isbn_10'])!=='')$book_isbn_10=trim($arry_book_infos[0]['book_isbn_10']);

                            $book_isbn_13='';
                            if(trim($arry_book_infos[0]['book_isbn_13'])!=='')$book_isbn_13=trim($arry_book_infos[0]['book_isbn_13']);

                            $book_country_code=$rs_country_code;
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $book_isbn_10       =mysql_prep(strip_tags($book_isbn_10));
                    $book_isbn_13       =mysql_prep(strip_tags($book_isbn_13));
                    $book_country_code  =mysql_prep(strip_tags($book_country_code));
                    $book_sid           =mysql_prep(strip_tags($book_sid));
                    $article_id         =(int)$article_id;
                    $eagle_code         =(int)$eagle_code;
                    $reply_from         =(int)$reply_from;
                    $group_id           =(int)$group_id;
                    $reply_content      =bad_content_filter(mysql_prep(strip_tags(implode("",$reply_contents))));
                    $keyin_ip           =get_ip();

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_reply
                        INSERT INTO `mssr_forum_global`.`mssr_forum_reply` SET
                            `edit_by`           = {$sess_user_id    },
                            `user_id`           = {$sess_user_id    },
                            `user_country_code` ='{$sess_country_code   }',
                            `group_id`          = {$group_id        },
                            `eagle_code`        = {$eagle_code      },
                            `article_id`        = {$article_id      },
                            `reply_id`          = NULL               ,
                            `reply_from`        = {$reply_from      },
                            `reply_state`       = 1                  ,
                            `reply_like_cno`    = 0                  ,
                            `reply_report_cno`  = 0                  ,
                            `keyin_cdate`       = NOW()              ,
                            `keyin_mdate`       = NULL               ;
                    ";
                    //送出
                    $err ='DB QUERY FAIL(1)';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                    //lastInsertId
                    $last_reply_id=(int)$conn_mssr->lastInsertId();

                    $sql="
                        # for mssr_forum_reply_book_rev
                        INSERT INTO `mssr_forum_global`.`mssr_forum_reply_book_rev` SET
                            `book_sid`      = '{$book_sid       }',
                            `book_isbn_10`  = '{$book_isbn_10   }',
                            `book_isbn_13`  = '{$book_isbn_13   }',
                            `country_code`  = '{$book_country_code  }',
                            `article_id`    =  {$article_id     } ,
                            `reply_id`      =  {$last_reply_id  } ;
                    ";
                    //送出
                    $err ='DB QUERY FAIL(2)';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                    $sql="
                        # for mssr_forum_reply_detail
                        INSERT INTO `mssr_forum_global`.`mssr_forum_reply_detail` SET
                            `article_id`        =  {$article_id     } ,
                            `reply_id`          =  {$last_reply_id  } ,
                            `reply_content`     = '{$reply_content  }',
                            `keyin_ip`          = '{$keyin_ip       }';
                    ";
                    //送出
                    $err ='DB QUERY FAIL(3)';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                    $sql="
                        # for mssr_forum_reply_detail_log
                        INSERT INTO `mssr_forum_global`.`mssr_forum_reply_detail_log` SET
                            `article_id`        =  {$article_id     } ,
                            `reply_id`          =  {$last_reply_id  } ,
                            `log_id`            = NULL                ,
                            `reply_content`     = '{$reply_content  }',
                            `keyin_ip`          = '{$keyin_ip       }';
                    ";
                    //送出
                    $err ='DB QUERY FAIL(4)';
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

                    $msg="回文成功";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            location.href='{$send_url}';
                        </script>
                    ";
                    die($jscript_back);
            }

        //-----------------------------------------------
        //函式: add_article()
        //用途: 新增文章
        //-----------------------------------------------

            function add_article($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_article()
            //用途: 新增文章
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;
                    global $file_server_enable;
                    global $arry_ftp1_info;
                    global $fso_enc;
                    global $page_enc;
                    global $conn_host_country_code;
                    global $arry_conn_mssr_tw;
                    global $arry_conn_mssr_hk;
                    global $arry_conn_mssr_sg;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //book_sid
                //article_title
                //article_content
                //eagle_code
                //article_from
                //group_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'book_sid       ',
                            'article_title  ',
                            'article_content',
                            'eagle_code     ',
                            'article_from   ',
                            'group_id       '
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
                    $book_sid           =trim($_POST[trim('book_sid                   ')]);
                    $article_title      =trim($_POST[trim('article_title              ')]);
                    $tmp_eagle_code     =trim($_POST[trim('eagle_code                 ')]);
                    $article_from       =trim($_POST[trim('article_from               ')]);
                    $group_id           =trim($_POST[trim('group_id                   ')]);
                    $article_contents   =array_map("trim",$_POST[trim('article_content')]);
                    $article_category   =(isset($_POST['article_category']))?(int)$_POST['article_category']:0;

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

                    //if(isset($_SESSION['mssr_forum_global']['uid'])&&isset($_SESSION['mssr_forum_global']['country_code'])){
                    //    $sess_country_code    =trim($_SESSION['mssr_forum_global']['country_code']);
                    //}

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($book_sid===''){
                       $arry_err[]='書本識別碼,未輸入!';
                    }
                    if($article_title===''){
                       $arry_err[]='文章標題,未輸入!';
                    }
                    if($tmp_eagle_code===''){
                       $arry_err[]='鷹架代號,未輸入!';
                    }
                    if($article_from===''){
                       $arry_err[]='文章來源,未輸入!';
                    }else{
                        $article_from=(int)$article_from;
                        if($article_from===0){
                            $arry_err[]='文章來源,錯誤!';
                        }
                    }
                    if($group_id===''){
                       $arry_err[]='小組主索引,未輸入!';
                    }
                    foreach($article_contents as $article_content){
                        if($article_content===''){
                            $arry_err[]='文章內容,未輸入!';
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

                        $arry_conn_mssr_country =get_conn_country($sess_user_id,$sess_account);
                        $conn_mssr_country      =conn($db_type='mysql',$arry_conn_mssr_country);
                        $conn_country_code      =trim($conn_host_country_code[$arry_conn_mssr_country['db_host']]);

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $book_sid=mysql_prep($book_sid);

                    //-----------------------------------
                    //檢核書籍是否存在
                    //-----------------------------------

                        $sql="
                            SELECT `mssr_forum_global`.`mssr_forum_group_booklist`.`country_code`
                            FROM `mssr_forum_global`.`mssr_forum_group_booklist`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_group_booklist`.`book_sid`='{$book_sid}'
                                AND `mssr_forum_global`.`mssr_forum_group_booklist`.`group_id`= {$group_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        if(empty($db_results)){
                            $msg="書籍不存在";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    location.href='{$send_url}';
                                </script>
                            ";
                            die($jscript_back);
                        }else{
                            $rs_country_code=trim($db_results[0]['country_code']);
                            $arry_conn="arry_conn_mssr_{$rs_country_code}";
                            $arry_conn=$$arry_conn;
                        }

                        $arry_book_infos=get_book_info('',$book_sid,$array_filter=array('book_isbn_10','book_isbn_13'),$arry_conn);
                        if(empty($arry_book_infos)){
                            $msg="書籍不存在";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    location.href='{$send_url}';
                                </script>
                            ";
                            die($jscript_back);
                        }else{
                            $book_isbn_10='';
                            if(trim($arry_book_infos[0]['book_isbn_10'])!=='')$book_isbn_10=trim($arry_book_infos[0]['book_isbn_10']);

                            $book_isbn_13='';
                            if(trim($arry_book_infos[0]['book_isbn_13'])!=='')$book_isbn_13=trim($arry_book_infos[0]['book_isbn_13']);

                            $book_country_code=$rs_country_code;
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $book_isbn_10       =mysql_prep(strip_tags($book_isbn_10));
                    $book_isbn_13       =mysql_prep(strip_tags($book_isbn_13));
                    $book_country_code  =mysql_prep(strip_tags($book_country_code));
                    $book_sid           =mysql_prep(strip_tags($book_sid));
                    $article_title      =bad_content_filter(mysql_prep(strip_tags($article_title)));
                    $article_from       =(int)$article_from;
                    $group_id           =(int)$group_id;
                    $article_content    =bad_content_filter(mysql_prep(strip_tags(implode("",$article_contents))));
                    $article_content    =str_replace("\\r\\n","\n",$article_content);
                    $keyin_ip           =get_ip();

                    $tmp_eagle_code     =mysql_prep(strip_tags(trim($tmp_eagle_code)));
                    $arry_eagle_code    =explode(",",$tmp_eagle_code);
                    $arry_eagle_code    =array_diff($arry_eagle_code, array(null,'null','',' '));

                    $article_category   =(int)$article_category;
                    if($article_category===0){
                        $arry_lv=array();
                        foreach($arry_eagle_code as $eagle_code){
                            $eagle_code=(int)$eagle_code;
                            if($eagle_code===0){
                                $article_category=1;
                                break;
                            }
                            if(in_array($eagle_code,array(1,2,3,4,60,61,62,7,8,9,63,64,65,66,67,68,69,19,20,47,25,26,27,120,121))){
                                $arry_lv[]=(int)1;
                            }
                            if(in_array($eagle_code,array(13,14,15,5,6,70,71,72,73,74,10,11,12,75,76,77,78,79,122,123,124,125,126,127,128,28,29,30,45,55,56,42,43,22))){
                                $arry_lv[]=(int)3;
                            }
                            if(in_array($eagle_code,array(80,81,82,83,84,85,86,87,88,89,90,16,17,102,103,104,105,106,107,108,109,110,111,112,91,92,93,94,95,96,97,98,99,100,101,21,51,52,38,39,132,133,31,32,33,134,135,136,137,138,34,35,49,50,36,37,53,59,129,130,131))){
                                $arry_lv[]=(int)5;
                            }
                            if(in_array($eagle_code,array(48,139,140,141,142,143,57,113,114,115,116,117,118,119))){
                                $arry_lv[]=(int)7;
                            }
                        }
                        $arry_lv=array_unique($arry_lv);
                        $lv=0;
                        foreach($arry_lv as $val){
                            $lv=$lv+(int)$val;
                        }
                        if($lv===1){
                            $article_category=4;
                        }
                        if($lv===3){
                            $article_category=5;
                        }
                        if($lv===5){
                            $article_category=6;
                        }
                        if($lv===7){
                            $article_category=7;
                        }
                        if($lv!==1 && $lv!==3 && $lv!==5 && $lv!==7){
                            $article_category=1;
                        }
                    }

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_article
                        INSERT INTO `mssr_forum_global`.`mssr_forum_article` SET
                            `edit_by`           = {$sess_user_id        } ,
                            `user_id`           = {$sess_user_id        } ,
                            `user_country_code` ='{$sess_country_code   }',
                            `group_id`          = {$group_id            } ,
                            `article_id`        = NULL                    ,
                            `article_from`      = {$article_from        } ,
                            `article_category`  = {$article_category    } ,
                            `article_type`      = 1                       ,
                            `article_state`     = 1                       ,
                            `article_like_cno`  = 0                       ,
                            `article_report_cno`= 0                       ,
                            `keyin_cdate`       = NOW()                   ,
                            `keyin_mdate`       = NULL                    ;
                    ";
                    //送出
                    $err ='DB QUERY FAIL(1)';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                    //lastInsertId
                    $last_article_id=(int)$conn_mssr->lastInsertId();

                    foreach($arry_eagle_code as $eagle_code){
                        $eagle_code=(int)$eagle_code;
                        $sql="
                            # for mssr_forum_article_eagle_rev
                            INSERT IGNORE INTO `mssr_forum_global`.`mssr_forum_article_eagle_rev` SET
                                `eagle_code`        = {$eagle_code      },
                                `article_id`        = {$last_article_id },
                                `keyin_mdate`       = NULL               ;
                        ";
                        $conn_mssr->exec($sql);
                    }

                    $sql="
                        # for mssr_forum_article_book_rev
                        INSERT INTO `mssr_forum_global`.`mssr_forum_article_book_rev` SET
                            `book_sid`      = '{$book_sid           }',
                            `book_isbn_10`  = '{$book_isbn_10       }',
                            `book_isbn_13`  = '{$book_isbn_13       }',
                            `country_code`  = '{$book_country_code  }',
                            `article_id`    =  {$last_article_id    } ;
                    ";
                    //送出
                    $err ='DB QUERY FAIL(2)';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                    $sql="
                        # for mssr_forum_article_detail
                        INSERT INTO `mssr_forum_global`.`mssr_forum_article_detail` SET
                            `article_id`        =  {$last_article_id} ,
                            `article_title`     = '{$article_title  }',
                            `article_content`   = '{$article_content}',
                            `keyin_ip`          = '{$keyin_ip       }';
                    ";
                    //送出
                    $err ='DB QUERY FAIL(3)';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                    $sql="
                        # for mssr_forum_article_detail_log
                        INSERT INTO `mssr_forum_global`.`mssr_forum_article_detail_log` SET
                            `article_id`        =  {$last_article_id} ,
                            `log_id`            = NULL                ,
                            `article_title`     = '{$article_title  }',
                            `article_content`   = '{$article_content}',
                            `keyin_ip`          = '{$keyin_ip       }';
                    ";
                    //送出
                    $err ='DB QUERY FAIL(4)';
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

                    $msg="發文成功";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            location.href='../view/reply.php?get_from={$article_from}&article_id={$last_article_id}';
                        </script>
                    ";
                    die($jscript_back);
            }

        //-----------------------------------------------
        //函式: add_group_teacher()
        //用途: 建立小組(老師直接建立)
        //-----------------------------------------------

            function add_group_teacher($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_group_teacher()
            //用途: 建立小組(老師直接建立)
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
                //group_name
                //group_content
                //group_rule
                //friend_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'group_type   ',
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

                        $group_name=mysql_prep(($group_name));

                    //-----------------------------------
                    //檢核小組名稱是否重複
                    //-----------------------------------

                        $sql="
                            SELECT `mssr_forum_global`.`mssr_forum_group`.`group_name`
                            FROM `mssr_forum_global`.`mssr_forum_group`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_group`.`group_name`='{$group_name}'
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                        if(!empty($db_results)){
                            $msg="小組名稱重複";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    history.back(-1);
                                </script>
                            ";
                            die($jscript_back);
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $sess_country_code  =trim($sess_country_code);
                    $group_type         =(int)$group_type;
                    $group_name         =mysql_prep(strip_tags($group_name));
                    $group_content      =mysql_prep(strip_tags($group_content));
                    $group_rule         =mysql_prep(strip_tags($group_rule));

                    $arry_teacher_uid[] =(int)$sess_user_id;
                    $arry_teacher_uid   =array_map("trim",$arry_teacher_uid);

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_group
                        INSERT INTO `mssr_forum_global`.`mssr_forum_group` SET
                            `create_by`         = {$sess_user_id    } ,
                            `edit_by`           = {$sess_user_id    } ,
                            `group_id`          = NULL                ,
                            `group_name`        ='{$group_name      }',
                            `group_content`     ='{$group_content   }',
                            `group_rule`        ='{$group_rule      }',
                            `group_type`        = {$group_type      } ,
                            `group_state`       =1                    ,
                            `keyin_cdate`       =NOW()                ,
                            `keyin_mdate`       =NOW()                ;
                    ";
                    //送出
                    $err ='DB QUERY FAIL(1)';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                    //lastInsertId
                    $last_group_id=(int)$conn_mssr->lastInsertId();

                    $sql="
                        # for mssr_forum_group_user_rev
                        INSERT INTO `mssr_forum_global`.`mssr_forum_group_user_rev` SET
                            `edit_by`           = {$sess_user_id        } ,
                            `group_id`          = {$last_group_id       } ,
                            `user_id`           = {$sess_user_id        } ,
                            `user_country_code` ='{$sess_country_code   }',
                            `user_type`         =2                        ,
                            `user_state`        =1                        ,
                            `user_intro`        =''                       ,
                            `keyin_cdate`       =NOW()                    ,
                            `keyin_mdate`       =NOW()                    ;
                    ";
                    //送出
                    $err ='DB QUERY FAIL(2)';
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

                    $msg="建立小組成功";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            location.href='../view/user.php?user_id={$sess_user_id}&tab=4';
                        </script>
                    ";
                    die($jscript_back);
            }
?>

