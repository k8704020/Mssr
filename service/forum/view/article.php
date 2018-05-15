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

        //外掛頁面檔
        require_once(str_repeat("../",1).'pages/code.php');

        //外掛函式檔
        $funcs=array(
            APP_ROOT.'inc/code',
            APP_ROOT.'service/forum/inc/code',

            APP_ROOT.'lib/php/db/code',
            APP_ROOT.'lib/php/date/code'
        );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

    //---------------------------------------------------
    //有無登入,SESSION
    //---------------------------------------------------

        $arrys_sess_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
        //if(empty($arrys_sess_login_info)){
        //    $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
        //    $jscript_back="
        //        <script>
        //            alert('{$msg}');
        //            location.href='/ac/index.php';
        //        </script>
        //    ";
        //    die($jscript_back);
        //}

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

        //特殊處理
        if(!isset($sess_country_code)){
            $sess_country_code='tw';
        }
        if($sess_country_code!=='tw'){
            die();
        }

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------

        $get_from    =(isset($_GET['get_from']))?(int)$_GET['get_from']:0;
        $get_book_sid=(isset($_GET['book_sid']))?trim($_GET['book_sid']):'';
        $get_group_id=(isset($_GET['group_id']))?(int)$_GET['group_id']:0;
        $tab         =(isset($_GET['tab']))?(int)$_GET['tab']:1;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

        $arry_err=array();

        if($get_from===0){
            $arry_err[]='組態,錯誤!';
        }else{
            if($get_from===1){
                if($get_book_sid===''){
                    $arry_err[]='書本識別碼,錯誤!';
                }
            }elseif($get_from===2){
                if($get_group_id===0){
                    $arry_err[]='小組主索引,錯誤!';
                }
            }else{
                $arry_err[]='組態,錯誤!';
            }
        }
        if($tab===0){
            $tab=1;
        }
        if(count($arry_err)!==0){
            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
            die();
        }

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //連線物件
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,明日聊書";

        //標籤
        $meta=meta($rd=1);

        //導覽列
        $navbar=navbar($rd=1);

        //廣告牆
        $carousel=carousel($rd=1);

        //側邊欄


        //註腳列
        $footbar=footbar($rd=1);

        //載入內容
        switch($get_from){
            case 1:
            //書籍內容
                //載入模態框
                $modal_dialog_1=modal_dialog($rd=1,$type=1);
                $modal_bookstore_rec=modal_bookstore_rec($rd=1);
                page_book($title);
            break;

            case 2:
            //小組內容
                $modal_bookstore_rec=modal_bookstore_rec($rd=1);
                page_group($title);
            break;

            default:
                die('組態,錯誤!');
            break;
        }
?>
<script type="text/javascript">
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    //OBJ
    var notification=new notification();

    //ONLOAD
    $(function(){
        setTimeout(function(){
            if(window.EventSource){
                var source = new EventSource('../pages/require/msg_cno/code.php');
                source.onmessage = function(e) {
                    var request_cno=e.data;
                    for(var i=0; i<$('.request_cno').length;i++){
                        var $request_cno=$('.request_cno').eq(i);
                        if($request_cno.html()!=request_cno){
                            if(parseInt($request_cno.html())<parseInt(request_cno)){
                                var notification_tag    =1;
                                var notification_title  ="明日聊書系統: "+ new Date().toLocaleString();
                                var notification_icon   ="http://www.cot.org.tw/mssr/service/forum/img/logo.png";
                                var notification_content="您有一條新的訊息，請進入聊書系統觀看！";
                                notification.show_notification(notification_tag, notification_title, notification_icon, notification_content);
                            }
                            $request_cno.empty().append(request_cno).animate({opacity:'0'},250).animate({opacity:'1'},500);
                        }
                    }
                };
            }
        }, 100);
    })

</script>


<?php function page_book($title="") {?>
<?php
//-------------------------------------------------------
//page_book 區塊 -- 開始
//-------------------------------------------------------

    //---------------------------------------------------
    //外部變數
    //---------------------------------------------------

        //config.php
        global $PAGE_SELF;
        global $FOLDER_SELF;
        global $nl;
        global $tab;
        global $fso_enc;
        global $page_enc;
        global $file_server_enable;
        global $arry_ftp1_info;

        //local
        global $arrys_sess_login_info;
        global $get_from;
        global $get_book_sid;
        global $get_group_id;
        global $tab;

        global $conn_mssr;
        global $arry_conn_mssr;

        //global $meta;
        global $navbar;
        global $carousel;
        global $modal_dialog_1;
        global $modal_bookstore_rec;
        global $footbar;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

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

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        $get_book_sid=addslashes($get_book_sid);
        $get_group_id=(int)$get_group_id;

        if(preg_match("/^mbu/i",$get_book_sid)){
            $get_book_info=get_book_info($conn_mssr,trim($get_book_sid),$array_filter=array('book_verified'),$arry_conn_mssr);
            if(!empty($get_book_info)){
                $rs_book_verified=(int)$get_book_info[0]['book_verified'];
                if($rs_book_verified===2){
                    $jscript_back="
                        <script>
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }
            }else{
                $jscript_back="
                    <script>
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }
        }

        //-----------------------------------------------
        //書籍資訊 SQL
        //-----------------------------------------------

            $arry_book_infos=get_book_info($conn_mssr,$get_book_sid,$array_filter=array('book_name','book_author','book_publisher','book_note','book_isbn_10','book_isbn_13'),$arry_conn_mssr);
            if(empty($arry_book_infos)){
                die('書本識別碼,錯誤!');
            }

            $book_name     =trim($arry_book_infos[0]['book_name']);
            $book_author   =trim($arry_book_infos[0]['book_author']);
            $book_publisher=trim($arry_book_infos[0]['book_publisher']);

            $book_note='暫無簡介';
            if(trim($arry_book_infos[0]['book_note'])!=='')$book_note=trim($arry_book_infos[0]['book_note']);

            $book_isbn_10='';
            if(trim($arry_book_infos[0]['book_isbn_10'])!=='')$book_isbn_10=trim($arry_book_infos[0]['book_isbn_10']);

            $book_isbn_13='';
            if(trim($arry_book_infos[0]['book_isbn_13'])!=='')$book_isbn_13=trim($arry_book_infos[0]['book_isbn_13']);

            $book_img    ='../img/default/book.png';
            if(file_exists("../../../info/book/{$get_book_sid}/img/front/simg/1.jpg")){
                $book_img="../../../info/book/{$get_book_sid}/img/front/simg/1.jpg";
            }

        //-----------------------------------------------
        //內容簡介 SQL
        //-----------------------------------------------

            $book_note.="......";
            $book_note =str_replace(" ","",$book_note);

        //-----------------------------------------------
        //page_info SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    COUNT(*) AS `cno`
                FROM `mssr_forum`.`mssr_forum_article_book_rev`
                    INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                    `mssr_forum`.`mssr_forum_article_book_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`='{$get_book_sid}'
                    AND `mssr_forum`.`mssr_forum_article`.`article_from`     =1 -- 文章來源
                    AND `mssr_forum`.`mssr_forum_article`.`article_state`    =1 -- 文章狀態
            ";
            $add_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $add_article_cno=(int)($add_article_results[0]['cno']);


            $sql="
                SELECT
                    COUNT(*) AS `cno`
                FROM `mssr_forum`.`mssr_forum_reply_book_rev`
                    INNER JOIN `mssr_forum`.`mssr_forum_reply` ON
                    `mssr_forum`.`mssr_forum_reply_book_rev`.`reply_id`=`mssr_forum`.`mssr_forum_reply`.`reply_id`

                    INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                    `mssr_forum`.`mssr_forum_reply`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_reply_book_rev`.`book_sid`='{$get_book_sid}'
                    AND `mssr_forum`.`mssr_forum_reply`.`reply_from`       =1 -- 回文來源
                    AND `mssr_forum`.`mssr_forum_reply`.`reply_state`      =1 -- 回文狀態
                    AND `mssr_forum`.`mssr_forum_article`.`article_state`  =1 -- 文章狀態
            ";
            $reply_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $reply_article_cno=(int)($reply_article_results[0]['cno']);


            $sql="
                SELECT
                    `mssr`.`mssr_book_borrow_log`.`user_id`,
                    `user`.`member`.`name`,
                    `user`.`member`.`sex`
                FROM `mssr`.`mssr_book_borrow_log`
                    INNER JOIN `user`.`member` ON
                    `mssr`.`mssr_book_borrow_log`.`user_id`=`user`.`member`.`uid`
                WHERE 1=1
                    AND `mssr`.`mssr_book_borrow_log`.`book_sid`='{$get_book_sid}'
                GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`,
                         `mssr`.`mssr_book_borrow_log`.`book_sid`
            ";
            $user_borrow_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $user_borrow_cno=count($user_borrow_results);


            //提取聊書書友資訊
            $friend_borrow_cno =0;
            $arry_forum_friend =array();
            if(isset($sess_user_id)){
                $arry_forum_friends=get_forum_friend($sess_user_id,$friend_id=0,$arry_conn_mssr);
                if(!empty($arry_forum_friends)){
                    foreach($arry_forum_friends as $arry_val){
                        if((int)$arry_val['friend_state']===1){
                            if((int)$arry_val['user_id']!==$sess_user_id)$arry_forum_friend[]=$arry_val['user_id'];
                            if((int)$arry_val['friend_id']!==$sess_user_id)$arry_forum_friend[]=$arry_val['friend_id'];
                        }
                    }
                }
                if(!empty($user_borrow_results)){
                    foreach($user_borrow_results as $inx=>$user_borrow_result){
                        $rs_user_id=(int)$user_borrow_result['user_id'];
                        if($rs_user_id===$sess_user_id || !in_array($rs_user_id,$arry_forum_friend)){
                            unset($user_borrow_results[$inx]);
                            continue;
                        }
                        $sql="
                            SELECT
                                `mssr`.`mssr_book_borrow_log`.`user_id`
                            FROM `mssr`.`mssr_book_borrow_log`
                            WHERE 1=1
                                AND `mssr`.`mssr_book_borrow_log`.`book_sid`='{$get_book_sid}'
                                AND `mssr`.`mssr_book_borrow_log`.`user_id` = {$rs_user_id  }
                            GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`,
                                     `mssr`.`mssr_book_borrow_log`.`book_sid`
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(!empty($db_results))$friend_borrow_cno++;
                    }
                }
            }

        //-----------------------------------------------
        //文章資訊 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `user`.`member`.`name`,

                    `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,

                    `mssr_forum`.`mssr_forum_article`.`user_id`,
                    `mssr_forum`.`mssr_forum_article`.`article_id`,
                    `mssr_forum`.`mssr_forum_article`.`article_like_cno`,
                    `mssr_forum`.`mssr_forum_article`.`article_report_cno`,
                    `mssr_forum`.`mssr_forum_article`.`keyin_cdate`,

                    `mssr_forum`.`mssr_forum_article_detail`.`article_title`
                FROM `mssr_forum`.`mssr_forum_article_book_rev`
                    INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                    `mssr_forum`.`mssr_forum_article_book_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`

                    INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                    `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                    INNER JOIN `user`.`member` ON
                    `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_article`.`article_from` =1 -- 文章來源
                    AND `mssr_forum`.`mssr_forum_article`.`article_type` =1 -- 文章類型
                    AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
            ";
            if($book_isbn_10!=='')$sql.="AND `mssr_forum`.`mssr_forum_article_book_rev`.`book_isbn_10`='{$book_isbn_10}'";
            if($book_isbn_13!=='')$sql.="AND `mssr_forum`.`mssr_forum_article_book_rev`.`book_isbn_13`='{$book_isbn_13}'";
            if($book_isbn_10===''&&$book_isbn_13==='')$sql.="AND `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`='{$get_book_sid}'";
            $sql.="ORDER BY `mssr_forum`.`mssr_forum_article`.`keyin_cdate` DESC";
            $article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,20),$arry_conn_mssr);

        //-----------------------------------------------
        //相關書籍 SQL
        //-----------------------------------------------

            $arry_about_books=array();
            if($book_isbn_10!=='' || $book_isbn_13!==''){
                $sql="
                    SELECT `book_sid`, `book_name`
                    FROM
                    (
                        SELECT
                            `mssr`.`mssr_book_class`.`book_sid`,
                            `mssr`.`mssr_book_class`.`book_name`
                        FROM `mssr`.`mssr_book_class`
                        WHERE 1=1
                    ";
                        if($book_isbn_10!=='')$sql.="AND `mssr`.`mssr_book_class`.`book_isbn_10`='{$book_isbn_10}'";
                        if($book_isbn_13!=='')$sql.="AND `mssr`.`mssr_book_class`.`book_isbn_13`='{$book_isbn_13}'";
                        $sql.="UNION";
                        $sql.="
                            SELECT
                                `mssr`.`mssr_book_library`.`book_sid`,
                                `mssr`.`mssr_book_library`.`book_name`
                            FROM `mssr`.`mssr_book_library`
                            WHERE 1=1
                        ";
                        if($book_isbn_10!=='')$sql.="AND `mssr`.`mssr_book_library`.`book_isbn_10`='{$book_isbn_10}'";
                        if($book_isbn_13!=='')$sql.="AND `mssr`.`mssr_book_library`.`book_isbn_13`='{$book_isbn_13}'";
                        $sql.="UNION";
                        $sql.="
                            SELECT
                                `mssr`.`mssr_book_global`.`book_sid`,
                                `mssr`.`mssr_book_global`.`book_name`
                            FROM `mssr`.`mssr_book_global`
                            WHERE 1=1
                        ";
                        if($book_isbn_10!=='')$sql.="AND `mssr`.`mssr_book_global`.`book_isbn_10`='{$book_isbn_10}'";
                        if($book_isbn_13!=='')$sql.="AND `mssr`.`mssr_book_global`.`book_isbn_13`='{$book_isbn_13}'";
                        $sql.="UNION";
                        //mssr_book_unverified第一段搜尋，來源為網路搜尋的書籍
                        $sql.="
                            SELECT
                                `mssr`.`mssr_book_unverified`.`book_sid`,
                                `mssr`.`mssr_book_unverified`.`book_name`
                            FROM `mssr`.`mssr_book_unverified`
                            WHERE 1=1
                                AND `mssr`.`mssr_book_unverified`.`book_from` = '2'
                        ";
                        if($book_isbn_10!=='')$sql.="AND `mssr`.`mssr_book_unverified`.`book_isbn_10`='{$book_isbn_10}'";
                        if($book_isbn_13!=='')$sql.="AND `mssr`.`mssr_book_unverified`.`book_isbn_13`='{$book_isbn_13}'";
                        $sql.="UNION";
                        //mssr_book_unverified第二段搜尋，來源為自己輸入資料的書籍，並通過審核
                        $sql.="
                            SELECT
                                `mssr`.`mssr_book_unverified`.`book_sid`,
                                `mssr`.`mssr_book_unverified`.`book_name`
                            FROM `mssr`.`mssr_book_unverified`
                            WHERE 1=1
                                AND `mssr`.`mssr_book_unverified`.`book_from` = '1'
                                AND `mssr`.`mssr_book_unverified`.`book_verified` = '1'
                        ";
                        if($book_isbn_10!=='')$sql.="AND `mssr`.`mssr_book_unverified`.`book_isbn_10`='{$book_isbn_10}'";
                        if($book_isbn_13!=='')$sql.="AND `mssr`.`mssr_book_unverified`.`book_isbn_13`='{$book_isbn_13}'";     
                        //新增同作者不同ISBN的書
                        if ($book_author!=='') {
                            $sql.="UNION";
                            $sql.="
                                SELECT
                                    `mssr`.`mssr_book_class`.`book_sid`,
                                    `mssr`.`mssr_book_class`.`book_name`
                                FROM `mssr`.`mssr_book_class`
                                WHERE 1=1
                                    AND `mssr`.`mssr_book_class`.`book_author` LIKE '{$book_author}%'
                            ";
                            if($book_isbn_10!=='')$sql.="AND `mssr`.`mssr_book_class`.`book_isbn_10`!='{$book_isbn_10}'";
                            if($book_isbn_13!=='')$sql.="AND `mssr`.`mssr_book_class`.`book_isbn_13`!='{$book_isbn_13}'";
                            $sql.="UNION";
                            $sql.="
                                SELECT
                                    `mssr`.`mssr_book_library`.`book_sid`,
                                    `mssr`.`mssr_book_library`.`book_name`
                                FROM `mssr`.`mssr_book_library`
                                WHERE 1=1
                                    AND `mssr`.`mssr_book_library`.`book_author` LIKE '{$book_author}%'
                            ";
                            if($book_isbn_10!=='')$sql.="AND `mssr`.`mssr_book_library`.`book_isbn_10`!='{$book_isbn_10}'";
                            if($book_isbn_13!=='')$sql.="AND `mssr`.`mssr_book_library`.`book_isbn_13`!='{$book_isbn_13}'";
                            $sql.="UNION";
                            $sql.="
                                SELECT
                                    `mssr`.`mssr_book_global`.`book_sid`,
                                    `mssr`.`mssr_book_global`.`book_name`
                                FROM `mssr`.`mssr_book_global`
                                WHERE 1=1
                                    AND `mssr`.`mssr_book_global`.`book_author` LIKE '{$book_author}%'
                            ";
                            if($book_isbn_10!=='')$sql.="AND `mssr`.`mssr_book_global`.`book_isbn_10`!='{$book_isbn_10}'";
                            if($book_isbn_13!=='')$sql.="AND `mssr`.`mssr_book_global`.`book_isbn_13`!='{$book_isbn_13}'";
                            $sql.="UNION";
                            //mssr_book_unverified第一段搜尋，來源為網路搜尋的書籍
                            $sql.="
                                SELECT
                                    `mssr`.`mssr_book_unverified`.`book_sid`,
                                    `mssr`.`mssr_book_unverified`.`book_name`
                                FROM `mssr`.`mssr_book_unverified`
                                WHERE 1=1
                                    AND `mssr`.`mssr_book_unverified`.`book_from` = '2'
                                    AND `mssr`.`mssr_book_unverified`.`book_author` LIKE '{$book_author}%'
                            ";
                            if($book_isbn_10!=='')$sql.="AND `mssr`.`mssr_book_unverified`.`book_isbn_10`!='{$book_isbn_10}'";
                            if($book_isbn_13!=='')$sql.="AND `mssr`.`mssr_book_unverified`.`book_isbn_13`!='{$book_isbn_13}'";
                            $sql.="UNION";
                            //mssr_book_unverified第二段搜尋，來源為自己輸入資料的書籍，並通過審核
                            $sql.="
                                SELECT
                                    `mssr`.`mssr_book_unverified`.`book_sid`,
                                    `mssr`.`mssr_book_unverified`.`book_name`
                                FROM `mssr`.`mssr_book_unverified`
                                WHERE 1=1
                                    AND `mssr`.`mssr_book_unverified`.`book_from` = '1'
                                    AND `mssr`.`mssr_book_unverified`.`book_verified` = '1'
                                    AND `mssr`.`mssr_book_unverified`.`book_author` LIKE '{$book_author}%'
                            ";
                            if($book_isbn_10!=='')$sql.="AND `mssr`.`mssr_book_unverified`.`book_isbn_10`!='{$book_isbn_10}'";
                            if($book_isbn_13!=='')$sql.="AND `mssr`.`mssr_book_unverified`.`book_isbn_13`!='{$book_isbn_13}'";
                        }
                $sql.="
                    ) AS sumAllBookTable
                    GROUP BY `book_name`
                ";
                $about_book_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($about_book_results)){
                    foreach($about_book_results as $about_book_result){
                        $arry_about_books[trim($about_book_result['book_sid'])]=trim($about_book_result['book_name']);
                    }
                }
            }

        //-----------------------------------------------
        //我的書櫃
        //-----------------------------------------------

            $arry_my_borrow=array();
            if(isset($sess_user_id)){
                if(!empty($arry_about_books)){
                    foreach($arry_about_books as $key=>$val){
                        $rs_book_sid=addslashes(trim($key));
                        $rs_book_name=trim($val);
                        $sql="
                            SELECT
                                `mssr`.`mssr_book_borrow_log`.`user_id`
                            FROM `mssr`.`mssr_book_borrow_log`
                            WHERE 1=1
                                AND `mssr`.`mssr_book_borrow_log`.`book_sid`='{$rs_book_sid}'
                                AND `mssr`.`mssr_book_borrow_log`.`user_id` ='{$sess_user_id}'
                            GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`,
                                     `mssr`.`mssr_book_borrow_log`.`book_sid`
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(!empty($db_results)&&($rs_book_sid!==$get_book_sid)){
                            $arry_my_borrow[$rs_book_sid]=$rs_book_name;
                        }
                    }
                }
            }

        //-----------------------------------------------
        //個人書櫃 SQL
        //-----------------------------------------------

            $sess_user_book_results=array();
            if(isset($sess_user_id)){
                $sql="
                    SELECT `mssr`.`mssr_rec_book_cno`.`book_sid`
                    FROM `mssr`.`mssr_rec_book_cno`
                    WHERE 1=1
                        AND `mssr`.`mssr_rec_book_cno`.`user_id`={$sess_user_id}
                    GROUP BY `mssr`.`mssr_rec_book_cno`.`user_id`,
                             `mssr`.`mssr_rec_book_cno`.`book_sid`
                    ORDER BY `mssr`.`mssr_rec_book_cno`.`keyin_cdate` DESC
                ";
                $sess_user_book_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            }

        //-----------------------------------------------
        //發文鷹架
        //-----------------------------------------------

            $article_eagle_content=article_eagle(1);
            $article_eagle_code   =article_eagle(2);

        //-----------------------------------------------
        //發文權限
        //-----------------------------------------------

            $my_has_borrow_flag=false;

            if(isset($sess_user_id)){
                if(!empty($arry_my_borrow)){
                    foreach($arry_my_borrow as $key=>$val){
                        $rs_book_sid=trim($key);
                        if($rs_book_sid===$get_book_sid){$my_has_borrow_flag=true;break;}
                    }
                }

                if(!$my_has_borrow_flag){
                    $sql="
                        SELECT `mssr`.`mssr_book_borrow_log`.`user_id`
                        FROM `mssr`.`mssr_book_borrow_log`
                        WHERE 1=1
                            AND `mssr`.`mssr_book_borrow_log`.`book_sid`='{$get_book_sid}'
                            AND `mssr`.`mssr_book_borrow_log`.`user_id` ='{$sess_user_id}'
                    ";
                    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    if(!empty($db_results))$my_has_borrow_flag=true;
                }

                if(!$my_has_borrow_flag){
                    if(!empty($sess_user_book_results)){
                        foreach($sess_user_book_results as $sess_user_book_result){
                            $rs_book_sid=trim($sess_user_book_result['book_sid']);
                            $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                            if(!empty($arry_book_infos)){
                                $rs_book_name=trim($arry_book_infos[0]['book_name']);
                                $comparison=book_name_comparison($book_name,$rs_book_name);
                                if($comparison){$my_has_borrow_flag=true;break;}
                            }else{continue;}
                        }
                    }
                }

                //回填書櫃
                if($my_has_borrow_flag)$arry_my_borrow[$get_book_sid]=$book_name;

				//檢查使用者點數
				$sql = "
					SELECT `mssr_forum`.`mssr_forum_point`.`total_point`
					FROM `mssr_forum`.`mssr_forum_point`
					WHERE 1=1
					   AND `mssr_forum`.`mssr_forum_point`.`user_id` = $sess_user_id;
				";

				$return_point_data = db_result($conn_type='pdo', $conn_mssr, $sql, array(0, 10), $arry_conn_mssr);

				if (isset($return_point_data[0]['total_point'])) {
					$user_point = $return_point_data[0]['total_point'];
				} else {
					$default_point = 100;
					$user_point = $default_point;
				}

				//點數不足時無法發文
				$enough_point = false;

				if ($user_point >= 30) {
					$enough_point = true;
				}
            }

        //-----------------------------------------------
        //身分判斷
        //-----------------------------------------------

            $arry_user_status=array();
            if(isset($sess_user_id)){
                $sql="
                    SELECT `user`.`permissions`.`status`
                    FROM `user`.`member`
                        INNER JOIN `user`.`permissions` ON
                        `user`.`member`.`permission`=`user`.`permissions`.`permission`
                    WHERE 1=1
                        AND `user`.`member`.`uid` ={$sess_user_id}
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $db_result){
                        $rs_status=trim($db_result['status']);
                        $arry_user_status[]=$rs_status;
                    }
                }
            }

        //-----------------------------------------------
        //修正書籍資訊權限
        //-----------------------------------------------

            $auth_edit_book_info=false;
            //if((in_array('i_a',$arry_user_status)||in_array('i_t',$arry_user_status)||in_array('i_sa',$arry_user_status))&&($book_isbn_10===''||$book_isbn_13===''))$auth_edit_book_info=true;
            //if(in_array('i_s',$arry_user_status)&&($book_isbn_10===''||$book_isbn_13===''))$auth_edit_book_info=true;
            //if(!$my_has_borrow_flag&&!empty($sess_user_book_results))$auth_edit_book_info=true;

        //-----------------------------------------------
        //已加入的小組 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_group`.`group_id`,
                    `mssr_forum`.`mssr_forum_group`.`group_name`
                FROM `mssr_forum`.`mssr_forum_group`
                    INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                    `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`={$sess_user_id}
                    AND `mssr_forum`.`mssr_forum_group`.`group_state`<>2
            ";
            $has_join_group_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

        //-----------------------------------------------
        //按鈕設置
        //-----------------------------------------------

            //熱門書單
            //echo '本周第一天（星期日为一周开始）：'.date('Y-m-d', time()-86400*date('w')).'<br/>';
            //echo '本周第一天（星期一为一周开始）：'.date('Y-m-d', time()-86400*date('w')+(date('w')>0?86400:-6*86400)).'<br/>';
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
            $btn_add_hot_booklist_html=trim('投票');
            $btn_add_hot_booklist_style="btn-default";
            if(isset($sess_user_id)){
                $sql="
                    SELECT `mssr_forum`.`mssr_forum_hot_booklist`.`create_by`
                    FROM  `mssr_forum`.`mssr_forum_hot_booklist`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_hot_booklist`.`create_by` = {$sess_user_id}
                        AND `mssr_forum`.`mssr_forum_hot_booklist`.`book_sid`  ='{$get_book_sid}'
                        AND `mssr_forum`.`mssr_forum_hot_booklist`.`keyin_cdate` BETWEEN '{$week_sdate} 00:00:00' AND '{$week_edate} 23:59:59'
                ";
                $hot_booklist_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(!empty($hot_booklist_results)){
                    $btn_add_hot_booklist_html='✔ 今天已投票';
                    $btn_add_hot_booklist_style="btn-warning";
                }
            }

            //追蹤書籍
            $btn_add_track_book_html=trim('追蹤書籍');
            $btn_add_track_book_style="btn-default";
            if(isset($sess_user_id)){
                $sql="
                    SELECT `mssr_forum`.`mssr_forum_track_book`.`user_id`
                    FROM  `mssr_forum`.`mssr_forum_track_book`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_track_book`.`user_id`   = {$sess_user_id}
                        AND `mssr_forum`.`mssr_forum_track_book`.`book_sid`  ='{$get_book_sid}'
                ";
                $track_book_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(!empty($track_book_results)){
                    $btn_add_track_book_html='已追蹤書籍';
                    $btn_add_track_book_style="btn-warning";
                }
            }

        //-----------------------------------------------
        //載入草稿
        //-----------------------------------------------

            $has_draft=false;
            if(isset($sess_user_id)){
                $sql="
                    SELECT
                        `mssr_forum`.`mssr_forum_article_draft`.`draft_id`
                    FROM `mssr_forum`.`mssr_forum_article_draft`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_article_draft`.`user_id` = {$sess_user_id  }
                        AND `mssr_forum`.`mssr_forum_article_draft`.`book_sid`='{$get_book_sid  }'
                        AND `mssr_forum`.`mssr_forum_article_draft`.`group_id`= {$get_group_id  }
                ";
                $draft_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(!empty($draft_results))$has_draft=true;
            }

        ////-----------------------------------------------
        ////FTP 登入
        ////-----------------------------------------------
        //
        //    //FTP 路徑
        //    if(isset($file_server_enable)&&($file_server_enable)){
        //        $ftp_root="public_html/mssr/info/user";
        //
        //        //連接 | 登入 FTP
        //        $ftp_conn  =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
        //        $ftp_login =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
        //
        //        //設定被動模式
        //        ftp_pasv($ftp_conn,TRUE);
        //    }

        //-----------------------------------------------
        //其他
        //-----------------------------------------------

            //if(!isset($sess_user_id)){
                $title=trim(addslashes($book_name)).' - 明日星球,明日聊書';
            //}

            $meta=meta(
                $rd=1,
                //$keywords=trim(addslashes($book_name)).' - 明日星球,明日聊書',
                $keywords='',
                $description=trim(addslashes($book_name)).', '.trim(addslashes($book_note)).' - 明日星球,明日聊書。'
            );

            $send_url=trim('http://').trim($_SERVER['HTTP_HOST']).trim($_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html>
<html lang='zh-TW' prefix='og: http://ogp.me/ns#'>
<head>
    <title><?php echo $title;?></title>

    <!-- 標籤,start -->
    <?php echo $meta;?>
    <!-- 標籤,end -->

    <!-- icon -->
    <link rel="shortcut icon" href="">

    <!-- 通用 -->
    <link href="../../../lib/framework/bootstrap/css/code.css" rel="stylesheet" type="text/css">

    <!-- 專屬 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" />
    <link href="../css/site.css" rel="stylesheet" type="text/css">

    <!--[if (gte IE 6)&(lte IE 8)]>
        <script>self.location.href='../pages/browser_update/index.php'</script>
    <![endif]-->

    <!--[if lt IE 9]>
        <script src="../../../lib/js/html5/code.js"></script>
        <script src="../../../lib/js/css/code.js"></script>
    <![endif]-->

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-116055812-1"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'UA-116055812-1');
</script>

</head>
<?php
    if(mb_strlen($book_note)>95){
        $book_note_len=trim(mb_substr($book_note,0,95)."...");
    }else{
        $book_note_len=trim($book_note);
    }
?>
<style>
    .jumbotron{
        background-image: url('#');
        background-color: #ebe1d4;
    }
    .jumbotron .jumbotron_name, .jumbotron .jumbotron-xs_name{
        color: #4e4e4e;
    }
</style>
<body>

    <!-- 導覽列,容器,start -->
    <?php echo $navbar;?>
    <!-- 導覽列,容器,end -->

    <!-- 頁面,容器,start -->
    <div class="container">

        <!-- 內容,start -->
        <div class="row">

            <!-- jumbotron,start -->
            <div class="jumbotron hidden-xs">

                <!-- 大頭貼,大解析度,start -->
                <img class="jumbotron_img hidden-xs"
                src="<?php echo $book_img;?>"
                width="160" height="160" border="0" alt="user_img"
                onclick="location.href='article.php?get_from=1&book_sid=<?php echo addslashes($get_book_sid);?>'"
                onmouseover='this.style.cursor="pointer";'/>
                <!-- 大頭貼,大解析度,end -->

                <!-- jumbotron_name,start -->
                <span class="jumbotron_name">
                    <?php echo htmlspecialchars($book_name);?>
                    <div style='font-size:12px;'><?php echo $add_article_cno;?>篇發文   </div>
                    <div style='font-size:12px;'><?php echo $reply_article_cno;?>篇回覆 </div>
                    <div style='font-size:12px;' onclick="$('#myTab li:eq(4) a').tab('show');void(0);" onmouseover="this.style.cursor='pointer'">
                        <?php echo $user_borrow_cno;?>位看過這本書&nbsp;(包含<?php echo $friend_borrow_cno;?>位書友)
                    </div>
                </span>
                <!-- jumbotron_name,end -->

                <!-- jumbotron_note,start -->
                <div class="jumbotron_note hidden-xs">
                    <span>
                        <span class="jumbotron_note_title">內容簡介</span><hr></hr>

                        <?php echo htmlspecialchars($book_note_len);?>
                        <button type="button" class="btn" style="font-weight:bold;font-size:13px;background-color:#fdfdfd;position:relative;left:-5px;"
                        data-toggle="modal" data-target=".bs-example-modal-sm">more</button>
                    </span>
                </div>
                <!-- jumbotron_note,end -->

            </div>
            <!-- jumbotron,end -->

            <!-- jumbotron-xs,start -->
            <div class="jumbotron jumbotron-xs hidden-sm hidden-md hidden-lg">

                <!-- 大頭貼,小解析度,start -->
                <img class="jumbotron-xs_img hidden-sm hidden-md hidden-lg"
                src="<?php echo $book_img;?>"
                width="100" height="100" border="0" alt="user_img"
                onclick="location.href='article.php?get_from=1&book_sid=<?php echo addslashes($get_book_sid);?>'"
                onmouseover='this.style.cursor="pointer";'/>
                <!-- 大頭貼,小解析度,end -->

                <!-- jumbotron-xs_name,start -->
                <span class="jumbotron-xs_name"><?php echo htmlspecialchars($book_name);?></span>
                <!-- jumbotron-xs_name,end -->

            </div>
            <!-- jumbotron-xs,end -->

            <!-- page_info,start -->
            <div class="page_info">
                <table class="table" border="1">
                    <tbody><tr>
                        <td class="hidden-xs" width="215px">&nbsp;</td>
                        <td width="235px" align="center">
                            <!-- 大解析度 -->
                            <?php if(isset($sess_user_id)):?>
                                <button type="button" class="btn btn-default btn-xs hidden-xs"
                                style="position:relative;top:0px;" onclick="$('#myTab li:eq(1) a').tab('show');void(0);"
                                style="text-decoration:none;" data-toggle="tooltip" data-placement="bottom"
                                title="按下按鈕開始進行發文。"
                                >發文</button>
                                <button type="button" class="btn_add_hot_booklist btn <?php echo $btn_add_hot_booklist_style;?> btn-xs hidden-xs"
                                style="position:relative;top:0px;"
                                user_id=<?php echo $sess_user_id;?>
                                book_sid="<?php echo $get_book_sid;?>"
                                style="text-decoration:none;" data-toggle="tooltip" data-placement="bottom"
                                title="每天都能投一次票讓老師知道你想討論這本書。"
                                ><?php echo $btn_add_hot_booklist_html;?></button>
                                <button type="button" class="btn_add_track_book btn <?php echo $btn_add_track_book_style;?> btn-xs hidden-xs"
                                style="position:relative;top:0px;"
                                user_id=<?php echo $sess_user_id;?>
                                book_sid="<?php echo $get_book_sid;?>"
                                style="text-decoration:none;" data-toggle="tooltip" data-placement="bottom"
                                title="追蹤書籍並顯示在動態牆。"
                                ><?php echo $btn_add_track_book_html;?></button>
                                <?php if($auth_edit_book_info):?>
                                    <button type="button" class="btn btn-default btn-xs hidden-xs hidden"
                                    style="position:relative;top:0px;" onclick="$('#myTab li:eq(5) a').tab('show');void(0);"
                                    title="如果你讀過這本書了，請按下此按鈕。"
                                    >我讀過了</button>
                                <?php endif;?>
                            <?php endif;?>

                            <!-- 小解析度 -->
                            <?php if(isset($sess_user_id)):?>
                                <button type="button" class="btn_add_track_book btn <?php echo $btn_add_track_book_style;?> btn-xs pull-right hidden-sm hidden-md hidden-lg"
                                style="position:relative;top:3px;margin:0 1px;"
                                user_id=<?php echo $sess_user_id;?>
                                book_sid="<?php echo $get_book_sid;?>"
                                title="追蹤書籍並顯示在動態牆。"
                                ><?php echo $btn_add_track_book_html;?></button>
                                <button type="button" class="btn_add_hot_booklist btn <?php echo $btn_add_hot_booklist_style;?> btn-xs pull-right hidden-sm hidden-md hidden-lg"
                                style="position:relative;top:3px;margin:0 1px;"
                                user_id=<?php echo $sess_user_id;?>
                                book_sid="<?php echo $get_book_sid;?>"
                                title="每天都能投一次票讓老師知道你想討論這本書。"
                                ><?php echo $btn_add_hot_booklist_html;?></button>
                                <button type="button" class="btn btn-default btn-xs pull-right hidden-sm hidden-md hidden-lg"
                                style="position:relative;top:3px;margin:0 1px;" onclick="$('#myTab li:eq(1) a').tab('show');void(0);"
                                title="按下按鈕開始進行發文。"
                                >發文</button>
                                <?php if($auth_edit_book_info):?>
                                    <button type="button" class="btn btn-default btn-xs pull-right hidden-sm hidden-md hidden-lg hidden"
                                    style="position:relative;top:3px;margin:0 1px;" onclick="$('#myTab li:eq(5) a').tab('show');void(0);"
                                    title="如果你讀過這本書了，請按下此按鈕。"
                                    >我讀過了</button>
                                <?php endif;?>
                            <?php endif;?>
                        </td>
                        <td class="hidden-xs" align="center"><!-- <span><?php echo $add_article_cno;?>篇發文   </span> --></td>
                        <td class="hidden-xs" align="center"><!-- <span><?php echo $reply_article_cno;?>篇回覆 </span> --></td>
                        <td class="hidden-xs" align="center"><!-- <span>
                            <?php echo $user_borrow_cno;?>位看過這本書&nbsp;(包含<?php echo $friend_borrow_cno;?>位書友)
                        </span> --></td>
                    </tr></tbody>
                </table>
            </div>
            <!-- page_info,end -->

            <!-- book_lefe_side,start -->
            <div class="book_lefe_side col-xs-12 col-sm-10 col-md-10 col-lg-10">

                <ul id="myTab" class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#article" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">
                        討論
                    </a></li>
                    <?php if(isset($sess_user_id)):?>
                        <li role="presentation"><a href="#add_article" id="profile-tab" role="tab" data-toggle="tab" aria-controls="profile">
                            發文
                        </a></li>
                    <?php endif;?>
                    <li role="presentation" class="visible-xs">
                        <button type="button" class="btn_modal_jumbotron_note btn btn-xs visible-xs" style="font-weight:bold;font-size:13px;"
                        data-toggle="modal" data-target=".bs-example-modal-sm">內容簡介</button>
                    </li>
                    <li role="presentation" class="hidden-xs"><a href="#book" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile">
                        相關書籍
                    </a></li>
                    <?php if(isset($sess_user_id)):?>
                        <li role="presentation" class="hidden-xs"><a href="#user_borrow" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile">
                            看過這本書的書友
                        </a></li>
                    <?php endif;?>
                    <?php if($auth_edit_book_info):?>
                        <li role="presentation" class="hidden"><a href="#book_info" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile">
                            修正書本資訊
                        </a></li>
                    <?php endif;?>
                    <li role="presentation" class="dropdown visible-xs">
                        <a href="#" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown" aria-controls="myTabDrop1-contents">更多&nbsp;<span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="myTabDrop1" id="myTabDrop1-contents">
                            <li><a href="#book" tabindex="-1" role="tab" id="dropdown1-tab" data-toggle="tab" aria-controls="dropdown1">相關書籍</a></li>
                            <?php if(isset($sess_user_id)):?>
                                <li><a href="#user_borrow" tabindex="-1" role="tab" id="dropdown1-tab" data-toggle="tab" aria-controls="dropdown1">看過這本書的書友</a></li>
                            <?php endif;?>
                            <?php if($auth_edit_book_info):?>
                                <li class='hidden'><a href="#book_info" tabindex="-1" role="tab" id="dropdown1-tab" data-toggle="tab" aria-controls="dropdown1">修正書本資訊</a></li>
                            <?php endif;?>
                        </ul>
                    </li>
                </ul>
                <div id="myTabContent" class="tab-content">

                    <!-- 討論 -->
                    <div role="tabpanel" class="tab-pane fade in active" id="article" aria-labelledBy="home-tab">
                        <table class="table table-striped table_article">
                            <thead class="hidden-xs"><tr class="second_tr" align="left">
                                <!-- <td width="220"><span>討論書籍</span></td> -->
                                <td width="">   <span>文章標題</span></td>
                                <td width="120"><span>姓名    </span></td>
                                <td width="140"><span>時間    </span></td>
                                <td width="50"> <span>讚      </span></td>
                                <td width="50"> <span>回應    </span></td>
                            </tr></thead>
                            <tbody>
                            <?php if(!empty($article_results)){
                                foreach($article_results as $inx=>$article_result):
                                    $rs_book_sid        =trim($article_result['book_sid']);
                                    $rs_article_title   =trim($article_result['article_title']);
                                    $rs_user_name       =trim($article_result['name']);
                                    $rs_keyin_cdate     =trim($article_result['keyin_cdate']);
                                    $rs_article_like_cno=(int)($article_result['article_like_cno']);
                                    $rs_article_id      =(int)($article_result['article_id']);
                                    $rs_user_id         =(int)($article_result['user_id']);

                                    //特殊處理
                                    $rs_book_name='';
                                    $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                    if(!empty($arry_book_infos)){$rs_book_name=trim($arry_book_infos[0]['book_name']);}else{continue;}

                                    if($rs_article_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
                                        $arry_blacklist_article_school=get_blacklist_article_school($sess_school_code,$rs_article_id,$arry_conn_mssr);
                                        if(!empty($arry_blacklist_article_school))continue;
                                    }

                                    $a_href_1="article.php?get_from=1&book_sid={$rs_book_sid}";
                                    $a_href_2="reply.php?get_from=1&article_id={$rs_article_id}";
                                    $a_href_3="user.php?user_id={$rs_user_id}&tab=1";

                                    //回文次數
                                    $sql="
                                        SELECT COUNT(*) AS `cno`
                                        FROM `mssr_forum`.`mssr_forum_reply_book_rev`
                                            INNER JOIN `mssr_forum`.`mssr_forum_reply` ON
                                            `mssr_forum`.`mssr_forum_reply_book_rev`.`reply_id`=`mssr_forum`.`mssr_forum_reply`.`reply_id`
                                        WHERE 1=1
                                            AND `mssr_forum`.`mssr_forum_reply_book_rev`.`article_id`= {$rs_article_id}
                                            AND `mssr_forum`.`mssr_forum_reply`.`reply_state`=1
                                    ";
                                    $reply_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                    $reply_article_cno=(int)($reply_article_results[0]['cno']);
                            ?>
                            <tr align="left">

                                <!-- 討論,大解析度,start -->
                                <!-- <td class="hidden-xs" style="border:0px;"><a href="<?php echo $a_href_1;?>"><?php echo htmlspecialchars($rs_book_name);?></a></td> -->
                                <td class="hidden-xs" style="border:0px;"><a href="<?php echo $a_href_2;?>" target="_blank"><?php echo htmlspecialchars($rs_article_title);?></a></td>
                                <td class="hidden-xs" style="border:0px;"><a href="<?php echo $a_href_3;?>"><?php echo htmlspecialchars($rs_user_name);?></a></td>
                                <td class="hidden-xs" style="border:0px;"><?php echo htmlspecialchars($rs_keyin_cdate);?></td>
                                <td class="hidden-xs" style="border:0px;"><?php echo htmlspecialchars($rs_article_like_cno);?></td>
                                <td class="hidden-xs" style="border:0px;"><?php echo htmlspecialchars($reply_article_cno);?></td>
                                <!-- 討論,大解析度,end -->

                                <!-- 討論,小解析度,start -->
                                <td class="hidden-sm hidden-md hidden-lg" style="border:0px;<?php if(isset($inx)&&$inx===0)echo 'position:relative;top:-5px;';?>">
                                    <span style="position:relative;top:-5px;font-size:16px;">
                                        <a href="<?php echo $a_href_2;?>" target="_blank"><?php echo htmlspecialchars($rs_article_title);?></a>
                                    </span><br>
                                    <span style="position:relative;top:5px;">
                                        <!-- <a href="<?php echo $a_href_1;?>"><?php echo htmlspecialchars($rs_book_name);?></a>
                                        &nbsp;&nbsp;|&nbsp;&nbsp; -->
                                        <a href="<?php echo $a_href_3;?>"><?php echo htmlspecialchars($rs_user_name);?></a>
                                    </span>
                                </td>
                                <!-- 討論,小解析度,end -->

                            </tr>
                            <?php endforeach;}else{?>
                            <tr align="left" style="height:250px;">

                                <!-- 討論,大解析度,start -->
                                <td class="hidden-xs" style="border:0px;" colspan="6" align="center">
                                    <span style="position:relative;top:100px;font-size:16px;">查無文章資訊。</span>
                                </td>
                                <!-- 討論,大解析度,end -->

                                <!-- 討論,小解析度,start -->
                                <td class="hidden-sm hidden-md hidden-lg" style="border:0px;<?php if(isset($inx)&&$inx===0)echo 'position:relative;top:-5px;';?>" align="center">
                                    <span style="position:relative;top:100px;font-size:16px;">查無文章資訊。</span>
                                </td>
                                <!-- 討論,小解析度,end -->

                            </tr>
                            <?php }?>
                            </tbody>
                        </table>
                    </div>

                    <!-- 發文 -->
                    <div role="tabpanel" class="tab-pane fade" id="add_article" aria-labelledBy="profile-tab">
                        <div class="row">
                            <div class="col-xs-12 col-sm-3 col-md-3 col-lg3 text-center visible-xs add_article_help-visible-xs" style="margin-bottom:15px;">
                                <?php if($my_has_borrow_flag):?>
                                    <a href="javascript:void(0);" class="btn btn-primary btn-block"
                                    role="button" style="color:#ffffff;" onclick="show_bookstore_rec(0);void(0);"
                                    >引用書店推薦</a>

                                    <a href="javascript:void(0);" class="btn btn-primary btn-block"
                                    role="button" style="color:#ffffff;" onclick="$('div.eagle_lv_1').fadeIn();void(0);"
                                    >使用發文輔助</a>
                                    <div class="row eagle_lv_1" style="display:none;">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg12" style="margin-top:10px;">
                                            <select class="form-control eagle_lv_1 select_eagle_lv_1" onchange="article_eagle(eagle_lv=1);void(0);">
                                                <option disabled="disabled" selected>請選擇書本類型</option>
                                                <?php foreach($article_eagle_content as $key=>$arry_val):?>
                                                    <option>&nbsp;&nbsp;<?php echo trim($key);?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                <?php endif;?>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <?php if($my_has_borrow_flag):?>
									<?php if($enough_point): ?>
	                                    <div class="row eagle_lv_5" style="border-right:1px solid #eeeeee;">
	                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
	                                            <form id="Form1"  name="Form1" method="post" onsubmit="return false;">
	                                                <?php if(count($arry_my_borrow)>1):?>
	                                                    <select class="form-control book_sid" id="book_sid" name="book_sid" style="margin-bottom:10px;">
	                                                        <option value="" disabled="disabled" selected>請選擇一本書來發文......</option>
	                                                        <?php foreach($arry_my_borrow as $key=>$val):?>
	                                                            <option value="<?php echo trim($key);?>"><?php echo trim($val);?></option>
	                                                        <?php endforeach;?>
	                                                    </select>
	                                                <?php else:?>
	                                                    <select class="form-control" id="book_sid" name="book_sid" style="display:none;margin-bottom:10px;">
	                                                        <?php foreach($arry_my_borrow as $key=>$val):?>
	                                                            <option value="<?php echo trim($key);?>" selected><?php echo trim($val);?></option>
	                                                        <?php endforeach;?>
	                                                    </select>
	                                                <?php endif;?>
	                                                <div class="form-group">
	                                                    <input type="text" id="article_title" name="article_title" class="form-control" placeholder="1.請輸入文章標題">
	                                                </div>
	                                                <div class="row">
	                                                    <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
	                                                        <div class="form-group">
	                                                            <textarea class="form-control article_content" id="article_content[]" name="article_content[]" rows="10" placeholder="2.請輸入文章內容" style="resize:none;"></textarea>
	                                                        </div>
	                                                    </div>
	                                                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center hidden-xs add_article_help-hidden-xs">
	                                                        <?php if($my_has_borrow_flag):?>
	                                                            <a href="javascript:void(0);" class="btn btn-primary btn-block"
	                                                            role="button" style="color:#ffffff;" onclick="show_bookstore_rec(0);void(0);"
	                                                            >引用書店推薦</a>

	                                                            <a href="javascript:void(0);" class="btn btn-primary btn-block"
	                                                            role="button" style="color:#ffffff;" onclick="$('div.eagle_lv_1').fadeIn();void(0);"
	                                                            >使用發文輔助</a>
	                                                            <div class="row eagle_lv_1" style="display:none;">
	                                                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg12" style="margin-top:10px;">
	                                                                    <select class="form-control eagle_lv_1 select_eagle_lv_1" onchange="article_eagle(eagle_lv=1);void(0);">
	                                                                        <option disabled="disabled" selected>請選擇書本類型</option>
	                                                                        <?php foreach($article_eagle_content as $key=>$arry_val):?>
	                                                                            <option>&nbsp;&nbsp;<?php echo trim($key);?></option>
	                                                                        <?php endforeach;?>
	                                                                    </select>
	                                                                </div>
	                                                            </div>
	                                                        <?php endif;?>
	                                                    </div>
	                                                </div>
	                                                <select class="form-control" id="article_category" name="article_category" style="margin-bottom:10px;">
	                                                    <option value="" disabled="disabled" selected>3.請選擇發文類型</option>
	                                                    <option value="1">綜合討論</option>
	                                                    <option value="4">我想要描述或釐清書中的重要內容</option>
	                                                    <option value="5">我想要表達讀完書後的感受</option>
	                                                    <option value="6">我想要提出關於這本書的疑問與發現</option>
	                                                    <option value="7">我有一些新點子想要嘗試</option>
	                                                    <option value="99">其他</option>
	                                                </select>

	                                                <?php if(!empty($has_join_group_results)):?>
	                                                    <div class="row">
	                                                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 text-center"
	                                                        style="padding-top:5px;">同時發佈在小組 (可複選)：</div>
	                                                        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9 text-left">
	                                                            <select id="has_join_group" name="each_group_id[]" class="form-control has_join_group pull-left"
	                                                            multiple="multiple" style="width:100%;">
	                                                                <?php foreach($has_join_group_results as $has_join_group_result):?>
	                                                                    <option value="<?php echo htmlspecialchars($has_join_group_result['group_id']);?>"
	                                                                    ><?php echo htmlspecialchars($has_join_group_result['group_name']);?></option>
	                                                                <?php endforeach;?>
	                                                            </select>
	                                                        </div>
	                                                    </div>
	                                                <?php endif;?>

	                                                <div class="checkbox">
	                                                   <label>
	                                                       <input type="checkbox" id="send_chk">我已閱讀過並同意遵守討論區規則
	                                                   </label>
	                                                    <a target="_blank" href="forum.php?method=view_mssr_forum_article_reply_rule" style="color:#428bca;">按這裡檢視討論區規則</a>
	                                                </div>
	                                                <hr></hr>
	                                                <button type="button" class="btn btn-default pull-left btn_add_article" onclick="Btn_add_article();void(0);" style="margin:0 3px;">送出</button>
	                                                <?php if($has_draft):?>
	                                                    <button type="button" class="btn btn-default pull-right btn_load_article_draft hidden" onclick="Btn_load_article_draft(this);void(0);" style="">載入草稿</button>
	                                                <?php endif;?>
	                                                <button type="button" class="btn btn-default pull-right btn_add_article_draft hidden" onclick="Btn_add_article_draft();void(0);" style="margin:0 3px;">暫存草稿</button>
	                                                <button type="button" class="btn btn-default pull-right next_step hidden"
	                                                onclick="$('#article_category, .btn_add_article, .prev_step').show();
	                                                $('#article_title, .article_content, .book_sid, .btn_add_article_draft, .btn_load_article_draft').hide();
	                                                $(this).hide();
	                                                void(0);">下一步</button>
	                                                <button type="button" class="btn btn-default pull-right prev_step hidden" style="display:none;"
	                                                onclick="$('#article_category, .btn_add_article').hide();
	                                                $('#article_title, .article_content, .book_sid, .next_step, .btn_add_article_draft, .btn_load_article_draft').show();
	                                                $(this).hide();
	                                                void(0);">上一步</button>
	                                                <div class="form-group hidden">
	                                                    <input type="text" class="form-control" name="eagle_code" value="" id="eagle_code">
	                                                    <input type="text" class="form-control" name="article_from" value="<?php echo (int)$get_from;?>">
	                                                    <input type="text" class="form-control" name="group_id" value="<?php echo (int)$get_group_id;?>">
	                                                    <input type="text" class="form-control" name="send_url" value="<?php echo trim($send_url);?>">
	                                                    <input type="text" class="form-control" name="method" value="add_article">
	                                                </div>
	                                            </form>
	                                        </div>
	                                    </div>
									<?php endif;?>
                                <?php endif;?>
                            </div>
                            <?php if(!$my_has_borrow_flag):?>
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
                                    您尚未閱讀過這本書，
                                        <button type="button" class="btn btn-default btn-xs"
                                        style="position:relative;top:-2px;font-size:11pt;" onclick="window.open('/mssr/service/code.php?mode=read_the_registration');void(0);"
                                        title="閱讀登記書籍資訊。"
                                        >前往閱讀登記</button><br><br>
                                    <?php if($auth_edit_book_info):?>
                                        2.  <button type="button" class="btn btn-default btn-xs"
                                            style="position:relative;top:-2px;" onclick="$('#myTab li:eq(5) a').tab('show');void(0);"
                                            title="如果你讀過這本書了，請按下此按鈕。"
                                            >我讀過這本書了</button>
                                    <?php endif;?>
                                </div>
                            <?php endif;?>
							<?php if(!$enough_point): ?>
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
									<font color="red">※你目前的點數不足30點，無法發文，您可以多回覆其他人的文章來賺取點數。</font>
								</div>
							<?php endif;?>
                        </div>
                    </div>

                    <!-- 相關書籍 -->
                    <div role="tabpanel" class="tab-pane fade" id="book" aria-labelledBy="profile-tab">
                        <?php if(!empty($arry_about_books)&&count($arry_about_books)>1){?>
                            <div class="user_lefe_side_tab2 row">
                                <?php
                                    foreach($arry_about_books as $key=>$val):
                                        $rs_book_sid =trim($key);
                                        $rs_book_name=trim($val);
                                        if($rs_book_sid===$get_book_sid)continue;

                                        $book_img    ='../img/default/book.png';
                                        if(file_exists("../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg")){
                                            $book_img="../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg";
                                        }
                                ?>
                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                                    <div class="thumbnail">
                                        <a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_book_sid);?>">
                                            <img width="80" height="80" style="weight:80px;height:80px;"
                                            src="<?php echo $book_img;?>" alt="Generic placeholder thumbnail">
                                            <div class="caption"><?php echo htmlspecialchars($rs_book_name);?></div>
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach;?>
                            </div>
                        <?php }else{?>
                            <div class="user_lefe_side_tab2 row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:center;">
                                    <pre style="background-color:#ffffdd;">目前共有【<?php
                                        if (count($arry_about_books) == 0) {
                                            echo count($arry_about_books);
                                        }else{
                                            echo count($arry_about_books) - 1;
                                        }
                                    ?>】本相關書籍</pre>
                                </div>
                            </div>
                        <?php }?>
                    </div>

                    <!-- 看過這本書的書友 -->
                    <div role="tabpanel" class="tab-pane fade" id="user_borrow" aria-labelledBy="profile-tab">
                        <div class="user_lefe_side_tab2 row">
                            <?php
                            if(!empty($user_borrow_results)){
                                foreach($user_borrow_results as $user_borrow_result):
                                    $rs_user_id  =(int)($user_borrow_result['user_id']);
                                    $rs_user_sex =(int)($user_borrow_result['sex']);
                                    $rs_user_name=trim($user_borrow_result['name']);

                                    $rs_user_img='';
                                    if($rs_user_sex===1)$rs_user_img='../img/default/user_boy.png';
                                    if($rs_user_sex===2)$rs_user_img='../img/default/user_girl.png';

                                    if(isset($file_server_enable)&&($file_server_enable)){
                                        //$rs_user_img_ftp_path="{$ftp_root}/{$rs_user_id}/forum/user_sticker";
                                        //$arry_rs_user_img_ftp_file=ftp_nlist($ftp_conn,$rs_user_img_ftp_path);
                                        //if(isset($arry_rs_user_img_ftp_file[0])){
                                        //    $rs_user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                                        //}
                                        if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg")){
                                            $rs_user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                                        }
                                    }else{
                                        if(file_exists("../../../info/user/{$rs_user_id}/forum/user_sticker/1.jpg")){
                                            $rs_user_img="../../../info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                                        }
                                    }
                            ?>
                            <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                                <div class="thumbnail">
                                    <a href="user.php?user_id=<?php echo $rs_user_id;?>&tab=1">
                                        <img width="80" height="80" style="weight:80px;height:80px;"
                                        src="<?php echo $rs_user_img;?>" alt="Generic placeholder thumbnail">
                                        <div class="caption"><?php echo htmlspecialchars($rs_user_name);?></div>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach;}?>
                        </div>
                    </div>

                    <!-- 修正書本資訊 -->
                    <div role="tabpanel" class="tab-pane fade" id="book_info" aria-labelledBy="profile-tab">
                        <div class="user_lefe_side_tab2 row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <form id="Form_edit_book_info" name="Form_edit_book_info" method="post" onsubmit="return false;">
                                    <?php if(in_array('i_a',$arry_user_status)||in_array('i_t',$arry_user_status)||in_array('i_sa',$arry_user_status)):?>
                                        <!-- <div class="input-group" style="margin-bottom:15px">
                                            <span class="input-group-addon">書籍名稱</span>
                                            <input class="form-control" type="text" id="book_name" name="book_name" value="<?php echo htmlspecialchars($book_name);?>"
                                            placeholder="請輸入書籍名稱" maxlength="50" required autofocus>
                                        </div>
                                        <div class="input-group" style="margin-bottom:15px">
                                            <span class="input-group-addon">書籍作者</span>
                                            <input class="form-control" type="text" id="book_author" name="book_author" value="<?php echo htmlspecialchars($book_author);?>"
                                            placeholder="請輸入書籍作者" maxlength="50" required autofocus>
                                        </div>
                                        <div class="input-group" style="margin-bottom:15px">
                                            <span class="input-group-addon">書籍出版社</span>
                                            <input class="form-control" type="text" id="book_publisher" name="book_publisher" value="<?php echo htmlspecialchars($book_publisher);?>"
                                            placeholder="請輸入書籍出版社" maxlength="30" required autofocus>
                                        </div> -->
                                    <?php endif;?>

                                    <?php if(!empty($sess_user_book_results)):?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:center;">
                                                <pre style="background-color:#ffffdd;">很抱歉，請告訴我們你書櫃中的哪本書跟【<?php echo htmlspecialchars($book_name);?>】這本書是同一本書。</pre>
                                            </div>
                                        </div>
                                        <div class="row" style="max-height:200px;overflow:auto;">
                                            <?php foreach($sess_user_book_results as $sess_user_book_result):?>
                                            <?php
                                                $rs_book_sid=trim($sess_user_book_result['book_sid']);
                                                if($rs_book_sid!==''){
                                                    $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name','book_isbn_10','book_isbn_13'),$arry_conn_mssr);
                                                    if(empty($arry_book_infos))continue;
                                                    $rs_book_name   =trim($arry_book_infos[0]['book_name']);
                                                    $rs_book_isbn_10=trim($arry_book_infos[0]['book_isbn_10']);
                                                    $rs_book_isbn_13=trim($arry_book_infos[0]['book_isbn_13']);
                                                    if(mb_strlen($rs_book_name)>20){
                                                        $rs_book_name=mb_substr($rs_book_name,0,20)."..";
                                                    }
                                                    if(trim($rs_book_name)==='')continue;
                                                    if(($book_isbn_10!==''&&$book_isbn_13!=='')&&($rs_book_isbn_10!==''&&$rs_book_isbn_13!==''))continue;
                                                }else{continue;}
                                            ?>
                                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="text-align:left;">
                                                    <label style="position:relative;top:2px;"><input type="radio" id="book_sid_from" name="book_sid_from" class='book_sid_from'
                                                    value="<?php echo trim($rs_book_sid);?>"
                                                    onclick="load_sess_user_book('<?php echo trim($rs_book_sid);?>');void(0);"></label>
                                                    <?php echo htmlspecialchars($rs_book_name);?>
                                                </div>
                                            <?php endforeach;?>
                                        </div>
                                    </div>
                                    <?php else:?>
                                        <div class="row" style="margin-bottom:-20px;">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:center;">
                                                <div class='alert alert-danger'>很抱歉，書櫃中沒有符合條件的書，請點選下方返回按鈕，並進行閱讀登記。</div>
                                            </div>
                                        </div>
                                    <?php endif;?>

                                    <?php if(($book_isbn_10===''||$book_isbn_13==='')&&(!empty($sess_user_book_results))):?>
                                        <hr>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:center;">
                                                <pre style="background-color:#ffffdd;">請幫幫我們補齊書本資訊。</pre>
                                            </div>
                                        </div>
                                    <?php endif;?>

                                    <?php if($book_isbn_10===''&&(!empty($sess_user_book_results))):?>
                                        <div class="input-group" style="margin-bottom:15px">
                                            <span class="input-group-addon">ISBN10碼</span>
                                            <input class="form-control" type="text" id="book_isbn_10" name="book_isbn_10" value="<?php echo htmlspecialchars($book_isbn_10);?>"
                                            placeholder="請輸入ISBN10碼" maxlength="10" required autofocus>
                                        </div>
                                    <?php endif;?>
                                    <?php if($book_isbn_13===''&&(!empty($sess_user_book_results))):?>
                                        <div class="input-group" style="margin-bottom:15px">
                                            <span class="input-group-addon">ISBN13碼</span>
                                            <input class="form-control" type="text" id="book_isbn_13" name="book_isbn_13" value="<?php echo htmlspecialchars($book_isbn_13);?>"
                                            placeholder="請輸入ISBN13碼" maxlength="13" required autofocus>
                                        </div>
                                    <?php endif;?>

                                    <hr>
                                    <div class="form-group pull-right">
                                        <?php if(!empty($sess_user_book_results)):?>
                                            <button type="button" id="btn_edit_book_info" class="btn btn-default">確定並送出</button>
                                        <?php endif;?>
                                        <button type="button" class="btn btn-default" onclick="$('#myTab li:eq(1) a').tab('show');void(0);"
                                        title="返回"
                                        >返回</button>
                                    </div>

                                    <div class="form-group hidden">
                                        <input type="hidden" class="form-control" name="book_sid_to" value="<?php echo trim($get_book_sid);?>">
                                        <input type="hidden" class="form-control" name="method" value="edit_book_info">
                                        <input type="hidden" class="form-control" name="send_url" value="<?php echo trim($send_url);?>">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <!-- book_lefe_side,end -->

            <!-- right_side,start -->
            <div class="right_side col-xs-12 col-sm-2 col-md-2 col-lg-2"></div>
            <!-- right_side,end -->

        </div>
        <!-- 內容,end -->

    </div>
    <!-- 頁面,容器,end -->

    <!-- 註腳列,容器,start -->
    <?php echo $footbar;?>
    <!-- 註腳列,容器,end -->

    <!-- 頁面至頂,start -->
    <div class="scroll_to_top hidden-xs"></div>
    <!-- 頁面至頂,end -->

    <!-- modal_jumbotron_note,start -->
    <?php echo $modal_dialog_1;?>
    <!-- modal_jumbotron_note,end -->

    <!-- modal_bookstore_rec,start -->
    <?php echo $modal_bookstore_rec;?>
    <!-- modal_bookstore_rec,end -->

</body>

<!-- 通用 -->
<script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/func/block_ui/code.js"></script>
<script type="text/javascript" src="../../../lib/js/string/code.js"></script>
<script type="text/javascript" src="../../../lib/js/array/code.js"></script>
<script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>

<!-- 專屬 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script type="text/javascript" src="../inc/code.js"></script>

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl          ='\r\n';
    var get_from    =parseInt(<?php echo (int)$get_from;?>);
    var get_group_id=parseInt(<?php echo (int)$get_group_id;?>);
    var send_url    =document.URL;
    var article_cno =parseInt(<?php echo count($article_results);?>);
    var book_sid    =trim('<?php echo trim($get_book_sid);?>');
    var book_note   =trim('<?php echo trim($book_note);?>');

    <?php if(isset($file_server_enable)&&($file_server_enable)):?>
        var file_server_enable=true;
        var arry_ftp1_info_host=trim('<?php echo trim($arry_ftp1_info["host"]);?>');
    <?php else:?>
        var file_server_enable=false;
        var arry_ftp1_info_host=trim('');
    <?php endif;?>

    <?php if(isset($sess_user_id)):?>
        var sess_user_id=parseInt(<?php echo $sess_user_id;?>);
    <?php else:?>
        var sess_user_id=parseInt(0);
    <?php endif;?>


    //OBJ
    var article_eagle_content       =<?php echo json_encode($article_eagle_content,true);?>;
    var article_eagle_code          =<?php echo json_encode($article_eagle_code,true);?>;
    var arry_my_borrow              =<?php echo json_encode($arry_my_borrow,true);?>;
    var json_sess_user_book_results ='<?php echo json_encode($sess_user_book_results,true);?>';


    //FUNCTION
    function show_bookstore_rec(no){
    //引用書店推薦

        var no=parseInt(no);

        if(no===0){
            $('#modal_bookstore_rec').modal();
            show_bookstore_rec(1);
        }else{
            $.ajax({
            //參數設置
                async      :true,
                cache      :false,
                global     :true,
                timeout    :50000,
                contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                url        :"../controller/load.php",
                type       :"POST",
                data       :{
                    no      :encodeURI(trim(no)),
                    sess_user_book_results:(trim(json_sess_user_book_results)),
                    method  :encodeURI(trim('load_bookstore_rec')),
                    send_url:encodeURI(trim(send_url))
                },
            //事件
                beforeSend  :function(){
                //傳送前處理
                    $.blockUI({
                        message:'<h3>資料讀取中...</h3>',
                        baseZ: 2000,
                        css:{
                            border: 'none',
                            padding: '15px',
                            backgroundColor: '#000',
                            '-webkit-border-radius': '10px',
                            '-moz-border-radius': '10px',
                            opacity: .6,
                            color: '#fff'
                        }
                    });
                },
                success     :function(respones){
                //成功處理
                    var respones=jQuery.parseJSON(respones);
                    if(no===1){
                        if(parseInt(respones.length)>0){
                            $(".show_bookstore_rec_content").empty();
                            for(key in respones){
                                var _html="";
                                var file_path=trim(respones[key]);
                                var has_append=false;
                                for(key in file_path.split("/")){
                                    var val=$.trim(file_path.split("/")[key]);
                                    if(val===book_sid){
                                        _html+="<div class='col-xs-12 col-sm-4 col-md-3 col-lg-3' style='margin:5px 0;'>";
                                            _html+="<div class='thumbnail text-center' style='background-color:#ffeaea;'>";
                                                _html+="<img src='"+file_path+"' width='120' height='120' class='img-responsive' border='0' alt='bookstore_rec' style='width:120px;height:120px;'>";
                                                _html+="<div class='caption'>";
                                                    _html+="<p>";
                                                        _html+="<button file_path='"+file_path+"' type='button' class='btn btn-primary btn-xs' onclick='show_bookstore_rec_draw(this);' style='margin:0 2px;margin-top:2px;'>觀看</button>";
                                                        _html+="<button file_path='"+file_path+"' type='button' class='btn btn-primary btn-xs' onclick='use_bookstore_rec(this,"+no+");' style='margin:0 2px;margin-top:2px;'>使用</button>";
                                                    _html+="</p>";
                                                _html+="</div>";
                                            _html+="</div>";
                                        _html+="</div>";
                                        $(".show_bookstore_rec_content").prepend(_html);
                                        has_append=true;
                                        break;
                                    }
                                }
                                if(!has_append){
                                    _html+="<div class='col-xs-12 col-sm-4 col-md-3 col-lg-3' style='margin:5px 0;'>";
                                        _html+="<div class='thumbnail text-center'>";
                                            _html+="<img src='"+file_path+"' width='120' height='120' class='img-responsive' border='0' alt='bookstore_rec' style='width:120px;height:120px;'>";
                                            _html+="<div class='caption'>";
                                                _html+="<p>";
                                                    _html+="<button file_path='"+file_path+"' type='button' class='btn btn-primary btn-xs' onclick='show_bookstore_rec_draw(this);' style='margin:0 2px;margin-top:2px;'>觀看</button>";
                                                    _html+="<button file_path='"+file_path+"' type='button' class='btn btn-primary btn-xs' onclick='use_bookstore_rec(this,"+no+");' style='margin:0 2px;margin-top:2px;'>使用</button>";
                                                _html+="</p>";
                                            _html+="</div>";
                                        _html+="</div>";
                                    _html+="</div>";
                                    $(".show_bookstore_rec_content").append(_html);
                                }
                            }
                        }
                    }else if(no===2){
                        if(parseInt(respones.length)>0){
                            $(".show_bookstore_rec_content").empty();
                            for(key in respones){
                                var _html="";
                                var content=trim(respones[key]);
                                _html+="<div class='col-xs-12 col-sm-4 col-md-3 col-lg-3' style='margin:5px 0;'>";
                                    _html+="<div class='thumbnail text-center' style='word-break:break-all;height:120px;'>";
                                        _html+="<p class='text-left' style='font-size:8pt;height:60px;'>"+content+"</p>";
                                        _html+="<div class='caption'>";
                                            _html+="<p>";
                                                _html+="<button content='"+content+"' type='button' class='btn btn-primary btn-xs' onclick='use_bookstore_rec(this,"+no+");' style='margin:0 2px;margin-top:2px;'>使用</button>";
                                            _html+="</p>";
                                        _html+="</div>";
                                    _html+="</div>";
                                _html+="</div>";
                                $(".show_bookstore_rec_content").append(_html);
                            }
                        }
                    }else if(no===3){
                        if(parseInt(respones.length)>0){
                            $(".show_bookstore_rec_content").empty();
                            for(key in respones){
                                var _html="";
                                var file_path=trim(respones[key]);
                                _html+="<div class='col-xs-12 col-sm-6 col-md-3 col-lg-3' style='margin:5px 0;'>";
                                    _html+="<div class='thumbnail text-center' style='word-break:break-all;height:80px;'>";
                                        _html+="<audio controls style='position:relative;top:10px;width:140px;'>";
                                            _html+="<source src='"+file_path+"' type='audio/mpeg'>";
                                        _html+="</audio>";
                                        _html+="<div class='caption text-center'>";
                                            _html+="<p>";
                                                _html+="<button file_path='"+file_path+"' type='button' class='btn btn-primary btn-xs' onclick='use_bookstore_rec(this,"+no+");' style='margin:0 2px;margin-top:2px;'>使用</button>";
                                            _html+="</p>";
                                        _html+="</div>";
                                    _html+="</div>";
                                _html+="</div>";
                                $(".show_bookstore_rec_content").append(_html);
                            }
                        }
                    }else{return false;}
                },
                error       :function(xhr, ajaxoptions, thrownerror){
                //失敗處理
                    return false;
                },
                complete    :function(){
                //傳送後處理
                    $.unblockUI();
                }
            });
        }
    }

    function use_bookstore_rec(obj,no){
        var no              =parseInt(no);
        var oarticle_content=document.getElementById('article_content[]');

        if(no===1){
            var file_path=trim($(obj).attr('file_path'));
            var file_tag=nl+'[img src="'+trim(file_path)+'" img]'+nl;
            alert('圖片檔即將貼在文章內容，請勿更改格式！');
            $(oarticle_content).val($.trim($(oarticle_content).val())+file_tag);
        }else if(no===2){
            var content=nl+trim($(obj).attr('content'))+nl;
            alert('文字即將貼在文章內容！');
            $(oarticle_content).val($.trim($(oarticle_content).val())+content);
        }else if(no===3){
            var file_path=trim($(obj).attr('file_path'));
            var file_tag=nl+'[audio src="'+trim(file_path)+'" audio]'+nl;
            alert('錄音檔即將貼在文章內容，請勿更改格式！');
            $(oarticle_content).val($.trim($(oarticle_content).val())+file_tag);
        }else{return false;}

        $('#modal_bookstore_rec').modal('hide');
        oarticle_content.focus();
    }

    function show_bookstore_rec_draw(obj){
        var file_path=$(obj).attr('file_path');
        window.open(file_path);
    }

    function load_sess_user_book(book_sid){
    //讀取書櫃的書籍資訊

        var book_sid=trim(book_sid);

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/load.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                book_sid    :encodeURI(trim(book_sid             )),
                method      :encodeURI(trim('load_sess_user_book')),
                send_url    :encodeURI(trim(send_url             ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                var respones    =jQuery.parseJSON(respones);
                var book_isbn_10=trim(respones.book_isbn_10);
                var book_isbn_13=trim(respones.book_isbn_13);

                if(book_isbn_10!==''){
                    try{
                        $('#book_isbn_10').val(book_isbn_10);
                    }catch(e){}
                }
                if(book_isbn_13!==''){
                    try{
                        $('#book_isbn_13').val(book_isbn_13);
                    }catch(e){}
                }

                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    $('#btn_edit_book_info').click(function(){
    //修正書本資訊

        var oForm_edit_book_info=$('#Form_edit_book_info')[0];
        var obook_isbn_10       =$('#book_isbn_10')[0];
        var obook_isbn_13       =$('#book_isbn_13')[0];
        var $book_sid_froms     =$('.book_sid_from');
        var chk                 =false;
        var arry_err            =[];

        if(obook_isbn_10!==undefined&&trim(obook_isbn_10.value)==='')arry_err.push('請輸入ISBN10碼');

        if(obook_isbn_13!==undefined&&trim(obook_isbn_13.value)==='')arry_err.push('請輸入ISBN13碼');

        $book_sid_froms.each(function(){
            if($(this)[0].checked)chk=true;
        });
        if(!chk)arry_err.push('請選擇一本你讀過的書');

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要送出嗎 ?')){
                oForm_edit_book_info.action='../controller/edit.php'
                oForm_edit_book_info.submit();
                return true;
            }
        }
    });

    $('.btn_add_hot_booklist').click(function(){
    //熱門書單

        var user_id =parseInt($(this).attr('user_id'));
        var book_sid=trim(($(this).attr('book_sid')));

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/add.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                user_id     :encodeURI(trim(user_id             )),
                book_sid    :encodeURI(trim(book_sid            )),
                method      :encodeURI(trim('add_hot_booklist'  )),
                send_url    :encodeURI(trim(send_url            ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                if($.trim(respones)!==''){
                    alert(respones);
                }else{
                    location.reload();
                }
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    });

    $('.btn_add_track_book').click(function(){
    //追蹤書籍

        var user_id     =parseInt($(this).attr('user_id'));
        var book_sid    =trim(($(this).attr('book_sid')));

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/add.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                user_id     :encodeURI(trim(user_id             )),
                book_sid    :encodeURI(trim(book_sid            )),
                method      :encodeURI(trim('add_track_book'    )),
                send_url    :encodeURI(trim(send_url            ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                location.reload();
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                if(ajaxoptions==='timeout'){
                    return false;
                }else{
                    return false;
                }
            },
            complete    :function(){
            //傳送後處理
            }
        });
    });

    function Btn_load_article_draft(obj){
    //載入草稿

        var obook_sid   =$('#add_article').find('#book_sid')[0];
        var arry_err    =[];

        if(trim(obook_sid.value)===''){
            arry_err.push('請選擇一本書來載入草稿');
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要載入草稿嗎 ?')){
                $.ajax({
                //參數設置
                    async      :true,
                    cache      :false,
                    global     :true,
                    timeout    :50000,
                    contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                    url        :"../controller/load.php",
                    type       :"POST",
                    datatype   :"json",
                    data       :{
                        book_sid        :encodeURI(trim(obook_sid.value     )),
                        group_id        :encodeURI(trim(0                   )),
                        method          :encodeURI(trim('load_article_draft')),
                        send_url        :encodeURI(trim(send_url            ))
                    },

                //事件
                    beforeSend  :function(){
                    //傳送前處理
                    },
                    success     :function(respones){
                    //成功處理
                        var respones=jQuery.parseJSON(respones);
                        if(parseInt(respones.length)>=1){
                            for(key1 in respones){
                                var eagle_code       =$.trim(respones[key1]['eagle_code']);
                                var article_category =parseInt(respones[key1]['article_category']);
                                var article_title    =$.trim(respones[key1]['article_title']);
                                var article_content  =$.trim(respones[key1]['article_content']);
                                var oarticle_contents=document.getElementsByName('article_content[]');
                                $("#eagle_code").val($("#eagle_code").val()+eagle_code);
                                $('#add_article').find('#article_title').val($('#add_article').find('#article_title').val()+article_title);
                                if(oarticle_contents!==undefined && oarticle_contents.length!==0){
                                    for(var i=0;i<oarticle_contents.length;i++){
                                        oarticle_content=oarticle_contents[i];
                                        $(oarticle_content).val($(oarticle_content).val()+article_content);
                                    }
                                }
                            }
                        }
                        $(obj).remove();
                        return true;
                    },
                    error       :function(xhr, ajaxoptions, thrownerror){
                    //失敗處理
                        return false;
                    },
                    complete    :function(){
                    //傳送後處理
                    }
                });
            }else{
                return false;
            }
        }
    }

    function Btn_add_article_draft(){
    //暫存草稿

        var obook_sid           =$('#add_article').find('#book_sid')[0];
        var oarticle_title      =$('#add_article').find('#article_title')[0];
        var oeagle_code         =document.getElementById('eagle_code');
        var oarticle_contents   =document.getElementsByName('article_content[]');
        var article_category    =parseInt($('#article_category')[0].value);
        var article_content_err =0;
        var arry_err            =[];

        if(trim(obook_sid.value)===''){
            arry_err.push('請選擇一本書來發文');
        }
        if(trim(oarticle_title.value)===''){
            arry_err.push('請輸入文章標題');
        }
        if(oarticle_contents!==undefined && oarticle_contents.length!==0){
            for(var i=0;i<oarticle_contents.length;i++){
                oarticle_content=oarticle_contents[i];
                var placeholder=trim(oarticle_content.getAttribute('placeholder'));
                if(trim(oarticle_content.value)==='' || trim(oarticle_content.value)===trim(placeholder)){
                    //arry_err.push('請輸入文章內容 '+(i+1));
                    article_content_err++;
                }
            }
        }else{
            arry_err.push('文章內容框錯誤');
        }
        if(parseInt(oarticle_contents.length)===parseInt(article_content_err)){
            arry_err.push('請輸入文章內容');
        }
        if(trim(oeagle_code.value)===''){
            //oeagle_code.value=0;
        }
        if(isNaN(article_category)){
            article_category=0
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要存成草稿嗎 ?')){
                $.ajax({
                //參數設置
                    async      :true,
                    cache      :false,
                    global     :true,
                    timeout    :50000,
                    contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                    url        :"../controller/add.php",
                    type       :"POST",
                    datatype   :"json",
                    data       :{
                        book_sid        :encodeURI(trim(obook_sid.value         )),
                        group_id        :encodeURI(trim(0                       )),
                        eagle_code      :encodeURI(trim(oeagle_code.value       )),
                        article_category:encodeURI(trim(article_category        )),
                        article_title   :(trim(oarticle_title.value             )),
                        article_content :(trim(oarticle_content.value           )),
                        method          :encodeURI(trim('add_article_draft'     )),
                        send_url        :encodeURI(trim(send_url                ))
                    },

                //事件
                    beforeSend  :function(){
                    //傳送前處理
                    },
                    success     :function(respones){
                    //成功處理
                        alert(respones);
                        return true;
                    },
                    error       :function(xhr, ajaxoptions, thrownerror){
                    //失敗處理
                        return false;
                    },
                    complete    :function(){
                    //傳送後處理
                    }
                });
            }else{
                return false;
            }
        }
    }

    function Btn_add_article(){
    //發文

        var oForm1              =$('#add_article').find('#Form1')[0];
        var osend_chk           =$('#add_article').find('#send_chk')[0];
        var obook_sid           =$('#add_article').find('#book_sid')[0];
        var oarticle_title      =$('#add_article').find('#article_title')[0];
        var oeagle_code         =document.getElementById('eagle_code');
        var oarticle_contents   =document.getElementsByName('article_content[]');
        var article_content_err =0;
        var arry_err            =[];

        if(trim(obook_sid.value)===''){
            arry_err.push('請選擇一本書來發文');
        }
        if(trim(oarticle_title.value)===''){
            arry_err.push('請輸入文章標題');
        }
        if(oarticle_contents!==undefined && oarticle_contents.length!==0){
            for(var i=0;i<oarticle_contents.length;i++){
                oarticle_content=oarticle_contents[i];
                var placeholder=trim(oarticle_content.getAttribute('placeholder'));
                if(trim(oarticle_content.value)==='' || trim(oarticle_content.value)===trim(placeholder)){
                    //arry_err.push('請輸入文章內容 '+(i+1));
                    article_content_err++;
                }
                if(trim(oarticle_content.value).match(/…/gi)){
                    arry_err.push('請填補文章內容');
                }
            }
        }else{
            arry_err.push('文章內容框錯誤');
        }
        if(parseInt(oarticle_contents.length)===parseInt(article_content_err)){
            arry_err.push('請輸入文章內容');
        }
        if(!osend_chk.checked){
            arry_err.push('請閱讀並勾選同意討論區規則');
        }
        if(trim(oeagle_code.value)===''){
            oeagle_code.value=0;
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            // if(confirm('發表文章將會消耗 30 點發文點數，你確定要送出嗎 ?')){
            if(confirm('你確定要送出嗎 ?')){
                $.blockUI({
                    message:'<h3>發送文章中...</h3>',
                    baseZ: 2000,
                    css:{
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .6,
                        color: '#fff'
                    }
                });
                oForm1.action='../controller/add.php'
                oForm1.submit();
                return true;
            }else{
                return false;
            }
        }
    }

    //設置旗標
    var flag = false;

    function load_article(){
    //讀取文章

        if(!flag){

            var page_article_cno=parseInt(parseInt($('.table_article tr').length)-1);
            var book_isbn_10    =trim('<?php echo $book_isbn_10;?>');
            var book_isbn_13    =trim('<?php echo $book_isbn_13;?>');
            var get_book_sid    =trim('<?php echo $get_book_sid;?>');
            var rs_cat_id=0;

            $.ajax({
            //參數設置
                async      :true,
                cache      :false,
                global     :true,
                timeout    :50000,
                contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                url        :"../controller/load.php",
                type       :"POST",
                datatype   :"json",
                data       :{
                    page_article_cno:encodeURI(trim(page_article_cno)),
                    book_isbn_10    :encodeURI(trim(book_isbn_10    )),
                    book_isbn_13    :encodeURI(trim(book_isbn_13    )),
                    get_book_sid    :encodeURI(trim(get_book_sid    )),
                    get_group_id    :encodeURI(trim(0               )),
                    get_from        :encodeURI(trim(get_from        )),
                    rs_cat_id :encodeURI(trim(rs_cat_id)),
                    method          :encodeURI(trim('load_article'  ))
                },

            //事件
                beforeSend  :function(){
                //傳送前處理
                },
                success     :function(respones){
                //成功處理
                    var respones=jQuery.parseJSON(respones);
                    if(parseInt(respones.length)!==0){
                        for(key in respones){
                            var json_html=respones[key];
                            //附加
                            $('.table_article').append(json_html);
                        }
                    }
                },
                error       :function(xhr, ajaxoptions, thrownerror){
                //失敗處理
                    if(ajaxoptions==='timeout'){
                        return false;
                    }else{
                        return false;
                    }
                },
                complete    :function(){
                //傳送後處理
                }
            });

            //紀錄旗標點
            flag = true;
            //延遲function避免重複執行
            setTimeout(function(){flag = false;}, 3000);
        }

    }

    function load_right_side(fun){
    //讀取側邊欄

        var fun =trim(fun);
        book_sid=trim(book_sid);

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/load.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                sess_user_id:encodeURI(trim(sess_user_id        )),
                book_sid    :encodeURI(trim(book_sid            )),
                fun         :encodeURI(trim(fun                 )),
                method      :encodeURI(trim('load_right_side'   ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
                $.blockUI({
                    message:'<h3>網頁讀取中...</h3>',
                    css:{
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .6,
                        color: '#fff'
                    }
                });
            },
            success     :function(respones){
            //成功處理
                var respones=jQuery.parseJSON(respones);
                if($.trim(respones)!==''){
                    $('.right_side').append(respones);
                }
                $('[data-toggle="tooltip"]').tooltip(); //顯示完整書名提示
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                $.unblockUI();
                return false;
            },
            complete    :function(){
            //傳送後處理
                $.unblockUI();
            }
        });
    }

    function load_book_note(book_sid){
    //讀取內容簡介

        var book_sid=trim(book_sid);

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/load.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                book_sid    :encodeURI(trim(book_sid            )),
                method      :encodeURI(trim('load_book_note'    ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    //ONLOAD
    $(function(){
        //讀取側邊欄
        if(sess_user_id>0)load_right_side(trim('book'));
        //滾動監聽
        $(window).scroll(function(){
            //滾動卷軸值小數點無條件進位
            var scrollint = Math.ceil($(window).scrollTop());
            if(article_cno>0){
                //偵測行動裝置
                if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
                    if(scrollint>=($(document).height()-$(window).height())%2){
                        //讀取文章
                        load_article();
                    }
                }else{
                    if(scrollint>$(document).height()-$(window).height()-200){
                        //讀取文章
                        load_article();
                    }
                }
            }
            //偵測行動裝置
            if(/Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){

            }else{
                if(parseInt($(window).scrollTop())>0){
                    $('.scroll_to_top').show();
                }else{
                    $('.scroll_to_top').hide();
                }
            }
        });
        //讀取內容簡介
        if(book_note===trim('無......') || book_note===trim('暫無簡介......')){
            load_book_note(book_sid);
        }
        //發文輔助顯示
        if(/Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
            $('.add_article_help-hidden-xs').remove();
        }else{
            $('.add_article_help-visible-xs').remove();
        }
    })

    $(document).ready(function() {
        $(document).get(0).oncontextmenu = function() {
            return false;
        };
        $(".has_join_group").select2({
            maximumSelectionLength:0,
            placeholder:"請選擇小組",
            allowClear:true
        });
    });
    //將滑鼠右鍵事件取消
    document.oncontextmenu = function(){
        window.event.returnValue=false;
    }
    $(document).keydown(function(event) {
        if (event.ctrlKey==true && (event.which == '118' || event.which == '86')) {
            event.preventDefault();
         }
    });

    //顯示完整書名提示
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });

</script>
<script type="text/javascript" src="../../../inc/external/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//呼叫使用者頁面紀錄
//-------------------------------------------------------
    user_page_log(rd=3);
</script>
</html>
<?php
//-------------------------------------------------------
//page_book 區塊 -- 結束
//-------------------------------------------------------
    $conn_user=NULL;
    $conn_mssr=NULL;
?>
<?php };?>


<?php function page_group($title="") {?>
<?php
//-------------------------------------------------------
//page_group 區塊 -- 開始
//-------------------------------------------------------

    //---------------------------------------------------
    //外部變數
    //---------------------------------------------------

        //config.php
        global $PAGE_SELF;
        global $FOLDER_SELF;
        global $nl;
        global $tab;
        global $fso_enc;
        global $page_enc;

        //local
        global $arrys_sess_login_info;
        global $get_from;
        global $get_book_sid;
        global $get_group_id;
        global $tab;

        global $conn_mssr;
        global $arry_conn_mssr;
        global $file_server_enable;
        global $arry_ftp1_info;
        global $modal_bookstore_rec;

        global $meta;
        global $navbar;
        global $carousel;
        global $footbar;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        if(empty($arrys_sess_login_info)){
            $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
            $jscript_back="
                <script>
                    alert('{$msg}');
                    location.href='/ac/index.php';
                </script>
            ";
            die($jscript_back);
        }

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

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        $get_group_id=(int)($get_group_id);

        //-----------------------------------------------
        //小組資訊 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_group`.`group_name`,
                    `mssr_forum`.`mssr_forum_group`.`group_content`,
                    `mssr_forum`.`mssr_forum_group`.`group_rule`,
                    `mssr_forum`.`mssr_forum_group`.`group_type`,
                    `mssr_forum`.`mssr_forum_group`.`group_state`
                FROM `mssr_forum`.`mssr_forum_group`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_group`.`group_id`={$get_group_id}
            ";
            $group_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(!empty($group_results)){
                $group_name   =trim($group_results[0]['group_name']);
                $group_content=trim($group_results[0]['group_content']);
                $group_rule   =trim($group_results[0]['group_rule']);
                $group_type   =(int)$group_results[0]['group_type'];
                $group_state  =(int)$group_results[0]['group_state'];
            }else{die();}

        //-----------------------------------------------
        //身分資訊 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_group_user_rev`.`user_type`,
                    `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`
                FROM `mssr_forum`.`mssr_forum_group`
                    INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                    `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_group`.`group_id`={$get_group_id}
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`={$sess_user_id}
            ";
            $my_group_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(!empty($my_group_results)){
                $user_type =(int)$my_group_results[0]['user_type'];
                $user_state=(int)$my_group_results[0]['user_state'];
            }else{
                $user_type =(int)0;
                $user_state=(int)0;
            }

        //-----------------------------------------------
        //討論 SQL
        //-----------------------------------------------

            $sql="
                    SELECT
                        `user`.`member`.`name`,

                        `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,

                        `mssr_forum`.`mssr_forum_article`.`user_id`,
                        `mssr_forum`.`mssr_forum_article`.`group_id`,
                        `mssr_forum`.`mssr_forum_article`.`article_id`,
                        0 AS `reply_id`,
                        `mssr_forum`.`mssr_forum_article`.`keyin_cdate`,
                        `mssr_forum`.`mssr_forum_article`.`article_like_cno` AS `like_cno`,

                        `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                        `mssr_forum`.`mssr_forum_article_detail`.`article_content`,
                        '' AS `reply_content`,
                        'article' AS `type`
                    FROM `mssr_forum`.`mssr_forum_article_book_rev`
                        INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                        `mssr_forum`.`mssr_forum_article_book_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`

                        INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                        `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                        INNER JOIN `user`.`member` ON
                        `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_article`.`article_from` =2 -- 文章來源
                        AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                        AND `mssr_forum`.`mssr_forum_article`.`group_id`     ={$get_group_id}

                UNION ALL

                    SELECT
                        `user`.`member`.`name`,

                        `mssr_forum`.`mssr_forum_reply_book_rev`.`book_sid`,

                        `mssr_forum`.`mssr_forum_reply`.`user_id`,
                        `mssr_forum`.`mssr_forum_reply`.`group_id`,
                        `mssr_forum`.`mssr_forum_reply`.`article_id`,
                        `mssr_forum`.`mssr_forum_reply`.`reply_id`,
                        `mssr_forum`.`mssr_forum_reply`.`keyin_cdate`,
                        `mssr_forum`.`mssr_forum_reply`.`reply_like_cno` AS `like_cno`,

                        `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                        `mssr_forum`.`mssr_forum_article_detail`.`article_content`,
                        `mssr_forum`.`mssr_forum_reply_detail`.`reply_content`,

                        'reply' AS `type`
                    FROM `mssr_forum`.`mssr_forum_reply_book_rev`
                        INNER JOIN `mssr_forum`.`mssr_forum_reply` ON
                        `mssr_forum`.`mssr_forum_reply_book_rev`.`reply_id`=`mssr_forum`.`mssr_forum_reply`.`reply_id`

                        INNER JOIN `mssr_forum`.`mssr_forum_reply_detail` ON
                        `mssr_forum`.`mssr_forum_reply`.`reply_id`=`mssr_forum`.`mssr_forum_reply_detail`.`reply_id`

                        INNER JOIN `user`.`member` ON
                        `mssr_forum`.`mssr_forum_reply`.`user_id`=`user`.`member`.`uid`

                        INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                        `mssr_forum`.`mssr_forum_reply`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`

                        INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                        `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_reply`.`reply_from` =2 -- 文章來源
                        AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                        AND `mssr_forum`.`mssr_forum_reply`.`reply_state`=1 -- 回文狀態
                        AND `mssr_forum`.`mssr_forum_reply`.`group_id`   ={$get_group_id}
                    ORDER BY FIELD(`type`, 'article', 'reply'), `keyin_cdate` DESC
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,20),$arry_conn_mssr);
            $article_reply_results=array();
            if(!empty($db_results)){
                foreach($db_results as $db_result){

                    extract($db_result, EXTR_PREFIX_ALL, "rs");

                    $rs_user_id         =(int)$rs_user_id;
                    $rs_group_id        =(int)$rs_group_id;
                    $rs_article_id      =(int)$rs_article_id;
                    $rs_reply_id        =(int)$rs_reply_id;
                    $rs_like_cno        =(int)$rs_like_cno;

                    $rs_user_name       =trim($rs_name);
                    $rs_book_sid        =trim($rs_book_sid);
                    $rs_keyin_cdate     =trim($rs_keyin_cdate);
                    $rs_article_title   =trim($rs_article_title);
                    $rs_article_content =trim($rs_article_content);
                    $rs_reply_content   =trim($rs_reply_content);
                    $rs_type            =trim($rs_type);
                    $rs_keyin_time      =strtotime($rs_keyin_cdate);

                    $article_reply_results[$rs_keyin_time][trim('user_name      ')]=$rs_user_name;
                    $article_reply_results[$rs_keyin_time][trim('book_sid       ')]=$rs_book_sid;
                    $article_reply_results[$rs_keyin_time][trim('user_id        ')]=$rs_user_id;
                    $article_reply_results[$rs_keyin_time][trim('group_id       ')]=$rs_group_id;
                    $article_reply_results[$rs_keyin_time][trim('article_id     ')]=$rs_article_id;
                    $article_reply_results[$rs_keyin_time][trim('reply_id       ')]=$rs_reply_id;
                    $article_reply_results[$rs_keyin_time][trim('like_cno       ')]=$rs_like_cno;
                    $article_reply_results[$rs_keyin_time][trim('keyin_cdate    ')]=$rs_keyin_cdate;
                    $article_reply_results[$rs_keyin_time][trim('article_title  ')]=$rs_article_title;
                    $article_reply_results[$rs_keyin_time][trim('article_content')]=$rs_article_content;
                    $article_reply_results[$rs_keyin_time][trim('reply_content  ')]=$rs_reply_content;
                    $article_reply_results[$rs_keyin_time][trim('type           ')]=$rs_type;
                }
                //時間排序
                krsort($article_reply_results);
            }

        //-----------------------------------------------
        //page_info SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    COUNT(*) AS `cno`
                FROM `mssr_forum`.`mssr_forum_article`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_article`.`group_id`     ={$get_group_id}
                    AND `mssr_forum`.`mssr_forum_article`.`article_from` =2 -- 文章來源
                    AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
            ";
            $add_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $add_article_cno=(int)($add_article_results[0]['cno']);


            $sql="
                SELECT
                    COUNT(*) AS `cno`
                FROM `mssr_forum`.`mssr_forum_reply`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_reply`.`group_id`   ={$get_group_id}
                    AND `mssr_forum`.`mssr_forum_reply`.`reply_from` =2 -- 回文來源
                    AND `mssr_forum`.`mssr_forum_reply`.`reply_state`=1 -- 回文狀態
            ";
            $reply_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $reply_article_cno=(int)($reply_article_results[0]['cno']);


            //提取聊書書友資訊
            $arry_forum_friend =array();
            $arry_forum_friends=get_forum_friend($sess_user_id,$friend_id=0,$arry_conn_mssr);
            if(!empty($arry_forum_friends)){
                foreach($arry_forum_friends as $arry_val){
                    if((int)$arry_val['friend_state']===1){
                        if((int)$arry_val['user_id']!==$sess_user_id){
                            $sql="
                                SELECT `name`
                                FROM `user`.`member`
                                WHERE 1=1
                                    AND `user`.`member`.`uid`={$arry_val['user_id']}
                            ";
                            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                            if(!empty($db_results)){
                                $rs_user_name=trim($db_results[0]['name']);
                            }
                            $arry_forum_friend[(int)$arry_val['user_id']]=$rs_user_name;
                        }
                        if((int)$arry_val['friend_id']!==$sess_user_id){
                            $sql="
                                SELECT `name`
                                FROM `user`.`member`
                                WHERE 1=1
                                    AND `user`.`member`.`uid`={$arry_val['friend_id']}
                            ";
                            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                            if(!empty($db_results)){
                                $rs_user_name=trim($db_results[0]['name']);
                            }
                            $arry_forum_friend[(int)$arry_val['friend_id']]=$rs_user_name;
                        }
                    }
                }
            }

        //-----------------------------------------------
        //我的書櫃 SQL
        //-----------------------------------------------

            $arry_my_borrow=array();
            $sql="
                SELECT
                    `mssr`.`mssr_book_borrow_log`.`book_sid`
                FROM `mssr`.`mssr_book_borrow_log`
                WHERE 1=1
                    AND `mssr`.`mssr_book_borrow_log`.`user_id` ='{$sess_user_id}'
                GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`,
                         `mssr`.`mssr_book_borrow_log`.`book_sid`
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $db_result){
                    $rs_book_sid=trim($db_result['book_sid']);
                    $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                    if(!empty($arry_book_infos)){$rs_book_name=trim($arry_book_infos[0]['book_name']);}else{continue;}
                    $arry_my_borrow[$rs_book_sid]=$rs_book_name;
                }
            }

        //-----------------------------------------------
        //小組書櫃 SQL
        //-----------------------------------------------

            $arry_group_booklist=array();
            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_group_booklist`.`book_sid`
                FROM `mssr_forum`.`mssr_forum_group_booklist`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_group_booklist`.`group_id`={$get_group_id}
                ORDER BY `mssr_forum`.`mssr_forum_group_booklist`.`keyin_cdate` DESC
            ";
            $group_book_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($group_book_results)){
                foreach($group_book_results as $group_book_result){
                    $rs_book_sid=trim($group_book_result['book_sid']);
                    $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                    if(empty($arry_book_infos))continue;
                    $rs_book_name=trim($arry_book_infos[0]['book_name']);
                    $arry_group_booklist[$rs_book_sid]=$rs_book_name;
                }
            }

        //-----------------------------------------------
        //小組成員 SQL
        //-----------------------------------------------

            $group_user_cno=0;
            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_group`.`group_id`,
                    `mssr_forum`.`mssr_forum_group`.`group_name`,
                    `mssr_forum`.`mssr_forum_group`.`group_state`,

                    `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`,
                    `mssr_forum`.`mssr_forum_group_user_rev`.`user_type`,
                    `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`
                FROM `mssr_forum`.`mssr_forum_group`
                    INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                    `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`={$get_group_id}
                ORDER BY `mssr_forum`.`mssr_forum_group_user_rev`.`keyin_cdate` DESC
            ";
            $group_user_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            foreach($group_user_results as $group_user_result){
                if((int)$group_user_result['user_state']===1)$group_user_cno++;
            }

        //-----------------------------------------------
        //精華區類別 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_best_article_category`.`cat_id`,
                    `mssr_forum`.`mssr_forum_best_article_category`.`cat_name`
                FROM  `mssr_forum`.`mssr_forum_best_article_category`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_best_article_category`.`group_id` ={$get_group_id}
                    AND `mssr_forum`.`mssr_forum_best_article_category`.`cat_state`=1
            ";
            $best_article_category_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

        //-----------------------------------------------
        //發文鷹架
        //-----------------------------------------------

            $article_eagle_content=article_eagle(1);
            $article_eagle_code   =article_eagle(2);

        //-----------------------------------------------
        //身分判斷
        //-----------------------------------------------

            $arry_user_status=array();
            $sql="
                SELECT
                    `user`.`permissions`.`status`
                FROM `user`.`member`
                    INNER JOIN `user`.`permissions` ON
                    `user`.`member`.`permission`=`user`.`permissions`.`permission`
                WHERE 1=1
                    AND `user`.`member`.`uid` ={$sess_user_id}
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $db_result){
                    $rs_status=trim($db_result['status']);
                    $arry_user_status[]=$rs_status;
                }
            }

        //-----------------------------------------------
        //個人書櫃 SQL
        //-----------------------------------------------

            $sess_user_book_results=array();
            if(isset($sess_user_id)){
                $sql="
                    SELECT `mssr`.`mssr_rec_book_cno`.`book_sid`
                    FROM `mssr`.`mssr_rec_book_cno`
                    WHERE 1=1
                        AND `mssr`.`mssr_rec_book_cno`.`user_id`={$sess_user_id}
                    GROUP BY `mssr`.`mssr_rec_book_cno`.`user_id`,
                             `mssr`.`mssr_rec_book_cno`.`book_sid`
                    ORDER BY `mssr`.`mssr_rec_book_cno`.`keyin_cdate` DESC
                ";
                $sess_user_book_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            }

        //-----------------------------------------------
        //是否曾檢核小組
        //-----------------------------------------------

            $sql="
                SELECT `mssr_forum`.`mssr_forum_group_report_log`.`group_id`
                FROM `mssr_forum`.`mssr_forum_group_report_log`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_group_report_log`.`user_id` ={$sess_user_id}
                    AND `mssr_forum`.`mssr_forum_group_report_log`.`group_id`={$get_group_id}
                LIMIT 1;
            ";
            $auth_report_group_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

        //-----------------------------------------------
        //權限
        //-----------------------------------------------

            //小組關閉
            if($group_state===2){
                $msg="此小組已遭停用";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }

            //停用權限
            if($user_state===2){
                $msg="你遭到版主停用";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }

            //黑名單小組權限
            $auth_blacklist_group_school=false;

            //檢舉小組權限
            $auth_report_group=true;

            //聯署建立小組權限
            $auth_create_group=false;

            //申請加入小組權限
            $auth_join_group=false;

            //關閉加入小組權限
            $auth_del_group=false;

            //參與權限
            $auth_participation_group=false;

            //發文權限
            $auth_add_article=false;

			//檢查使用者點數
			$sql = "
				SELECT `mssr_forum`.`mssr_forum_point`.`total_point`
				FROM `mssr_forum`.`mssr_forum_point`
				WHERE 1=1
				  AND `mssr_forum`.`mssr_forum_point`.`user_id` = $sess_user_id;
			";

			$return_point_data = db_result($conn_type='pdo', $conn_mssr, $sql, array(0, 10), $arry_conn_mssr);
			
			if (isset($return_point_data[0]['total_point'])) {
				$user_point = $return_point_data[0]['total_point'];
			} else {
				$default_point = 100;
				$user_point = $default_point;
			}

			//點數不足時無法發文
			$enough_point = false;

			if ($user_point >= 30) {
				$enough_point = true;
			}

            //回文權限
            $auth_add_reply=false;

            //管理權限
            $auth_admin=false;

            if(!empty($auth_report_group_results)){
                $auth_report_group=false;
            }

            if(in_array($user_type,array(3))&&$user_state===1&&$group_state===1){
                $auth_del_group=true;
            }

            //小組類型 1:公開 | 2:私密，預設:公開
            switch($group_type){

                case 1:
                    if(in_array($user_type,array(2,3))&&$user_state===1&&$group_state===1){
                        $auth_admin=true;
                    }

                    if($user_type===0&&$user_state===0&&$group_state===3){
                        $auth_create_group=true;
                    }

                    if($user_type===0&&$user_state===0&&$group_state===1&&$auth_create_group===false){
                        $auth_join_group=true;
                    }

                    if(in_array($user_type,array(1,2,3))&&in_array($user_state,array(1,3))&&$group_state===1){

                    }

                    if(in_array($user_type,array(1,2,3))&&$user_state===1&&$group_state===1){
                        $auth_add_article=true;
                        $auth_add_reply=true;
                    }

                    $auth_participation_group=true;
                break;

                case 2:
                    if(in_array($user_type,array(2,3))&&$user_state===1&&$group_state===1){
                        $auth_admin=true;
                    }

                    if($user_type===0&&$user_state===0&&$group_state===3){
                        $auth_create_group=true;
                    }

                    if($user_type===0&&$user_state===0&&$group_state===1&&$auth_create_group===false){
                        $auth_join_group=true;
                    }

                    if(in_array($user_type,array(1,2,3))&&in_array($user_state,array(1,3))&&$group_state===1){

                    }

                    if(in_array($user_type,array(1,2,3))&&$user_state===1&&$group_state===1){
                        $auth_participation_group=true;
                        $auth_add_article=true;
                        $auth_add_reply=true;
                    }
                break;

                default:
                    $msg="發生嚴重錯誤";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                break;

            }

            if(in_array('i_a',$arry_user_status)){
                $auth_del_group=true;
            }
            if(in_array('i_t',$arry_user_status)||in_array('i_sa',$arry_user_status)){
                //$auth_blacklist_group_school=true;
                $auth_report_group=false;
            }

        //-----------------------------------------------
        //按鈕設置
        //-----------------------------------------------

            //收藏小組
            $btn_add_track_group_html=trim('收藏小組');
            $btn_add_track_group_style="btn-default";
            $sql="
                SELECT `mssr_forum`.`mssr_forum_track_group`.`user_id`
                FROM  `mssr_forum`.`mssr_forum_track_group`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_track_group`.`user_id` = {$sess_user_id}
                    AND `mssr_forum`.`mssr_forum_track_group`.`group_id`= {$get_group_id}
            ";
            $track_group_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(!empty($track_group_results)){
                $btn_add_track_group_style="btn-warning";
                $btn_add_track_group_html='已收藏小組';
            }

            //申請加入小組
            $btn_join_group_html=trim('申請加入小組');

            //聯署建立小組
            $btn_create_group_html=trim('聯署建立小組');

            //解散小組
            $btn_del_group_html=trim('解散小組');

            //黑名單小組
            $blacklist_group_school_results=array();
            if(isset($sess_school_code)&&trim($sess_school_code!=='')){
                $btn_blacklist_group_school_html=trim('黑名單');
                $sql="
                    SELECT `mssr_forum`.`mssr_forum_blacklist_group_school`.`group_id`
                    FROM  `mssr_forum`.`mssr_forum_blacklist_group_school`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_blacklist_group_school`.`school_code`= '{$sess_school_code}'
                        AND `mssr_forum`.`mssr_forum_blacklist_group_school`.`group_id`   =  {$get_group_id    }
                ";
                $blacklist_group_school_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            }

        //-----------------------------------------------
        //小組樣式 SQL
        //-----------------------------------------------

            $style_id=1;
            $style_from=1;
            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_style_group_rev`.`style_id`,
                    `mssr_forum`.`mssr_forum_style_group_rev`.`style_from`
                FROM `mssr_forum`.`mssr_forum_style_group_rev`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_style_group_rev`.`group_id`={$get_group_id}
            ";
            $style_group_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(!empty($style_group_results)){
                $style_id  =(int)$style_group_results[0]['style_id'];
                $style_from=(int)$style_group_results[0]['style_from'];
            }

        //-----------------------------------------------
        //小組大頭貼
        //-----------------------------------------------

            $group_img=trim('../img/default/group.jpg');

            //FTP 路徑
            if(isset($file_server_enable)&&($file_server_enable)){
                //$ftp_root="public_html/mssr/info/forum";
                //$ftp_path="{$ftp_root}/group/{$get_group_id}";
                //
                ////連接 | 登入 FTP
                //$ftp_conn  =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                //$ftp_login =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                //
                ////設定被動模式
                //ftp_pasv($ftp_conn,TRUE);
                //
                ////獲取檔案目錄
                //$arry_ftp_group_sticker_file=ftp_nlist($ftp_conn,$ftp_path."/group_sticker");
                //
                //if(!empty($arry_ftp_group_sticker_file)){
                //    $group_img="http://".$arry_ftp1_info['host']."/mssr/info/forum/group/{$get_group_id}/group_sticker/1.jpg";
                //    $group_img_size=getimagesize($group_img);
                //}
                if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/forum/group/{$get_group_id}/group_sticker/1.jpg")){
                    $group_img="http://".$arry_ftp1_info['host']."/mssr/info/forum/group/{$get_group_id}/group_sticker/1.jpg";
                    $group_img_size=getimagesize($group_img);
                }
            }else{
                if(file_exists("../../../info/forum/group/{$get_group_id}/group_sticker/1.jpg")){
                    $group_img="../../../info/forum/group/{$get_group_id}/group_sticker/1.jpg";
                    $group_img_size=getimagesize($group_img);
                }
            }

        //-----------------------------------------------
        //小組 jumbotron 圖片
        //-----------------------------------------------

            $front_cover_group_1_img=trim('../img/default/front_cover_group.jpg');

            //獲取檔案目錄
            if(isset($file_server_enable)&&($file_server_enable)){
                //$arry_ftp_background_group_file=ftp_nlist($ftp_conn,$ftp_path."/background_group");
                //if(!empty($arry_ftp_background_group_file)){
                //    $front_cover_group_1_img=trim("http://".$arry_ftp1_info['host']."/mssr/info/forum/group/{$get_group_id}/background_group/front_cover_group_1.jpg");
                //}
                if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/forum/group/{$get_group_id}/background_group/front_cover_group_1.jpg")){
                    $front_cover_group_1_img=trim("http://".$arry_ftp1_info['host']."/mssr/info/forum/group/{$get_group_id}/background_group/front_cover_group_1.jpg");
                }
            }else{
                if(file_exists("../../../info/forum/group/{$get_group_id}/background_group/front_cover_group_1.jpg")){
                    $front_cover_group_1_img=trim("../../../info/forum/group/{$get_group_id}/background_group/front_cover_group_1.jpg");
                }
            }

        //-----------------------------------------------
        //其他
        //-----------------------------------------------

            $send_url=trim('http://').trim($_SERVER['HTTP_HOST']).trim($_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html>
<html lang='zh-TW' prefix='og: http://ogp.me/ns#'>
<head>
    <title><?php echo $title;?></title>

    <!-- 標籤,start -->
    <?php echo $meta;?>
    <!-- 標籤,end -->

    <!-- icon -->
    <link rel="shortcut icon" href="">

    <!-- 通用 -->
    <link href="../../../lib/jquery/ui/code.css" rel="stylesheet" type="text/css">
    <link href="../../../lib/framework/bootstrap/css/code.css" rel="stylesheet" type="text/css">

    <!-- 專屬 -->
    <link href="../css/site.css" rel="stylesheet" type="text/css">

    <!--[if (gte IE 6)&(lte IE 8)]>
        <script>self.location.href='../pages/browser_update/index.php'</script>
    <![endif]-->

    <!--[if lt IE 9]>
        <script src="../../../lib/js/html5/code.js"></script>
        <script src="../../../lib/js/css/code.js"></script>
    <![endif]-->
</head>
<body>

    <!-- 導覽列,容器,start -->
    <?php echo $navbar;?>
    <!-- 導覽列,容器,end -->

    <!-- 頁面,容器,start -->
    <div class="container">

        <!-- 內容,start -->
        <div class="row">

            <!-- jumbotron,start -->
            <div class="jumbotron hidden-xs" style="background-image:url('<?php echo $front_cover_group_1_img;?>');background-position:center top;background-size:100% auto;">

                <!-- 大頭貼,大解析度,start -->
                <img class="jumbotron_img hidden-xs"
                src="<?php echo $group_img;?>"
                width="160" height="160" border="0" alt="user_img"
                onclick="location.href='article.php?get_from=2&group_id=<?php echo $get_group_id;?>'"
                onmouseover='this.style.cursor="pointer";'/>
                <!-- 大頭貼,大解析度,end -->

                <!-- jumbotron_name,start -->
                <span class="jumbotron_name">
                    <?php echo htmlspecialchars($group_name);?>
                    <br/>版主：
                    <?php
                    if(!empty($group_user_results)){
                        $rs_arry_user_name=array();
                        foreach($group_user_results as $group_user_result):
                            $rs_user_id     =(int)$group_user_result['user_id'];
                            $rs_user_type   =(int)$group_user_result['user_type'];
                            $rs_user_state  =(int)$group_user_result['user_state'];
                            if($rs_user_type===2&&$rs_user_state===1){
                                $sql="
                                    SELECT `name`
                                    FROM `user`.`member`
                                    WHERE 1=1
                                        AND `user`.`member`.`uid`={$rs_user_id}
                                ";
                                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                $rs_user_name ='';
                                if(!empty($db_results)){
                                    $rs_user_name=trim($db_results[0]['name']);
                                    $rs_arry_user_name[]=$rs_user_name;
                                }
                            }else{continue;}
                    ?>
                    <?php endforeach;echo implode("、",$rs_arry_user_name);}?>
                    <div style='font-size:12px;'><?php echo $add_article_cno;?> 篇發文    </div>
                    <div style='font-size:12px;'><?php echo $reply_article_cno;?> 篇回覆  </div>
                    <div style='font-size:12px;'><?php echo $group_user_cno;?> 位成員     </div>
                </span>
                <!-- jumbotron_name,end -->

                <!-- jumbotron_note,start -->
                <div class="jumbotron_note hidden-xs">
                    <span>
                        <span class="jumbotron_note_title">聊書小組簡介</span><hr></hr>

                        <?php echo htmlspecialchars($group_content);?>
                    </span>
                </div>
                <!-- jumbotron_note,end -->

            </div>
            <!-- jumbotron,end -->

            <!-- jumbotron-xs,start -->
            <div class="jumbotron jumbotron-xs hidden-sm hidden-md hidden-lg" style="background-image:url('<?php echo $front_cover_group_1_img;?>');background-position:center top;background-size:100% auto;">

                <!-- 大頭貼,小解析度,start -->
                <img class="jumbotron-xs_img hidden-sm hidden-md hidden-lg"
                src="<?php echo $group_img;?>"
                width="100" height="100" border="0" alt="user_img"
                onclick="location.href='article.php?get_from=2&group_id=<?php echo $get_group_id;?>'"
                onmouseover='this.style.cursor="pointer";'/>
                <!-- 大頭貼,小解析度,end -->

                <!-- jumbotron-xs_name,start -->
                <span class="jumbotron-xs_name"><?php echo htmlspecialchars($group_name);?></span>
                <!-- jumbotron-xs_name,end -->

            </div>
            <!-- jumbotron-xs,end -->

            <!-- page_info,start -->
            <div class="page_info">
                <table class="table" border="1">
                    <tbody><tr>
                        <td class="hidden-xs" width="215px">&nbsp;</td>
                        <td width="305px" align="center">
                            <?php if($auth_blacklist_group_school&&empty($blacklist_group_school_results)):?>
                                <!-- 大解析度 -->
                                <button type="button" class="btn_blacklist_group_school btn btn-default btn-xs hidden-xs"
                                style="position:relative;top:0px;"
                                user_id=<?php echo $sess_user_id;?>
                                group_id="<?php echo $get_group_id;?>"
                                style="text-decoration:none;" data-toggle="tooltip" data-placement="bottom"
                                title="將小組加入黑名單，學校裡的人員都會看不到該小組。"
                                ><?php echo $btn_blacklist_group_school_html;?></button>
                                <!-- 小解析度 -->
                                <!-- <button type="button" class="btn_blacklist_group_school btn btn-default btn-xs pull-right hidden-sm hidden-md hidden-lg"
                                style="position:relative;top:3px;"
                                user_id=<?php echo $sess_user_id;?>
                                group_id="<?php echo $get_group_id;?>"
                                title="將小組加入黑名單，學校裡的人員都會看不到該小組。
                                ><?php echo $btn_blacklist_group_school_html;?></button> -->
                            <?php endif;?>
                            <?php if($auth_report_group):?>
                                <!-- 大解析度 -->
                                <button type="button" class="btn_report_group btn btn-default btn-xs hidden-xs"
                                style="position:relative;top:0px;"
                                user_id=<?php echo $sess_user_id;?>
                                group_id="<?php echo $get_group_id;?>"
                                style="text-decoration:none;" data-toggle="tooltip" data-placement="bottom"
                                title="檢舉小組，當檢舉數量到達一定次數時將會關閉此小組。"
                                >檢舉小組</button>
                                <!-- 小解析度 -->
                                <button type="button" class="btn_report_group btn btn-default btn-xs pull-right hidden-sm hidden-md hidden-lg"
                                style="position:relative;top:3px;"
                                user_id=<?php echo $sess_user_id;?>
                                group_id="<?php echo $get_group_id;?>"
                                title="檢舉小組，當檢舉數量到達一定次數時將會關閉此小組。"
                                >檢舉小組</button>
                            <?php endif;?>
                            <?php if($auth_create_group):?>
                                <!-- 大解析度 -->
                                <button type="button" class="btn_create_group btn btn-default btn-xs hidden-xs"
                                style="position:relative;top:0px;"
                                user_id=<?php echo $sess_user_id;?>
                                group_id="<?php echo $get_group_id;?>"
                                ><?php echo $btn_create_group_html;?></button>
                                <!-- 小解析度 -->
                                <button type="button" class="btn_create_group btn btn-default btn-xs pull-right hidden-sm hidden-md hidden-lg"
                                style="position:relative;top:3px;"
                                user_id=<?php echo $sess_user_id;?>
                                group_id="<?php echo $get_group_id;?>"
                                ><?php echo $btn_create_group_html;?></button>
                            <?php endif;?>
                            <?php if($auth_join_group):?>
                                <!-- 大解析度 -->
                                <button type="button" class="btn_join_group btn btn-default btn-xs hidden-xs"
                                style="position:relative;top:0px;"
                                user_id=<?php echo $sess_user_id;?>
                                group_id="<?php echo $get_group_id;?>"
                                ><?php echo $btn_join_group_html;?></button>
                                <!-- 小解析度 -->
                                <button type="button" class="btn_join_group btn btn-default btn-xs pull-right hidden-sm hidden-md hidden-lg"
                                style="position:relative;top:3px;"
                                user_id=<?php echo $sess_user_id;?>
                                group_id="<?php echo $get_group_id;?>"
                                ><?php echo $btn_join_group_html;?></button>
                            <?php endif;?>

                            <!-- 大解析度 -->
                            <button type="button" class="btn_add_track_group btn <?php echo $btn_add_track_group_style;?> btn-xs hidden-xs"
                            style="position:relative;top:0px;"
                            user_id=<?php echo $sess_user_id;?>
                            group_id="<?php echo $get_group_id;?>"
                            style="text-decoration:none;" data-toggle="tooltip" data-placement="bottom"
                            title="收藏小組並顯示在個人頁上。"
                            ><?php echo $btn_add_track_group_html;?></button>
                            <!-- 小解析度 -->
                            <button type="button" class="btn_add_track_group btn <?php echo $btn_add_track_group_style;?> btn-xs pull-right hidden-sm hidden-md hidden-lg"
                            style="position:relative;top:3px;"
                            user_id=<?php echo $sess_user_id;?>
                            group_id="<?php echo $get_group_id;?>"
                            title="收藏小組並顯示在個人頁上。"
                            ><?php echo $btn_add_track_group_html;?></button>

                            <?php if($auth_del_group):?>
                                <!-- 大解析度 -->
                                <button type="button" class="btn_del_group btn btn-default btn-xs hidden-xs"
                                style="position:relative;top:0px;"
                                group_id="<?php echo $get_group_id;?>"
                                title="解散小組，小組內的成員、討論及書單都會消失不見。"
                                ><?php echo $btn_del_group_html;?></button>
                                <!-- 小解析度 -->
                                <button type="button" class="btn_del_group btn btn-default btn-xs pull-right hidden-sm hidden-md hidden-lg"
                                style="position:relative;top:3px;"
                                group_id="<?php echo $get_group_id;?>"
                                title="解散小組，小組內的成員、討論及書單都會消失不見。"
                                ><?php echo $btn_del_group_html;?></button>
                            <?php endif;?>
                        </td>
                        <td class="hidden-xs" align="center"><!-- <span><?php echo $add_article_cno;?> 篇發文    </span> --></td>
                        <td class="hidden-xs" align="center"><!-- <span><?php echo $reply_article_cno;?> 篇回覆  </span> --></td>
                        <td class="hidden-xs" align="center"><!-- <span><?php echo $group_user_cno;?> 位成員     </span> --></td>
                    </tr></tbody>
                </table>
            </div>
            <!-- page_info,end -->

            <?php if(!$auth_create_group):?>

                <!-- group_lefe_side,start -->
                <div class="group_lefe_side col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <ul id="myTab" class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#home" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">
                            首頁
                        </a></li>
                        <?php if($auth_participation_group):?>
                            <li role="presentation"><a href="#article" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile" onclick="load_cat_id()">
                                討論
                            </a></li>
                            <li role="presentation" class="dropdown">
                                <a href="#" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown" aria-controls="myTabDrop1-contents">精華區&nbsp;<span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="myTabDrop1" id="myTabDrop1-contents">
                                    <?php
                                    if(!empty($best_article_category_results)){
                                        foreach($best_article_category_results as $best_article_category_result):
                                            $rs_cat_id  =(int)($best_article_category_result['cat_id']);
                                            $rs_cat_name=trim($best_article_category_result['cat_name']);
                                    ?>
                                        <li value="<?php echo "$rs_cat_id"; ?>"><a href="#beat_article_<?php echo $rs_cat_id;?>" tabindex="-1" role="tab" id="dropdown1-tab" data-toggle="tab" aria-controls="dropdown1">
                                            <?php echo htmlspecialchars($rs_cat_name);?>
                                        </a></li>
                                    <?php endforeach;}?>
                                </ul>
                            </li>
                            <li role="presentation" class="hidden-xs"><a href="#add_article" id="profile-tab" role="tab" data-toggle="tab" aria-controls="profile">
                                發文
                            </a></li>
                            <li role="presentation" class="hidden-xs"><a href="#book" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile">
                                小組書單
                            </a></li>
                            <li role="presentation" class="hidden-xs"><a href="#friend" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile">
                                小組成員
                            </a></li>
                            <?php if($auth_admin):?>
                                <li role="presentation" class="hidden-xs"><a href="#config" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile">
                                    小組管理
                                </a></li>
                            <?php endif;?>
                            <li role="presentation" class="dropdown hidden-sm hidden-md hidden-lg">
                                <a href="#" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown" aria-controls="myTabDrop1-contents">更多&nbsp;<span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="myTabDrop1" id="myTabDrop1-contents">
                                    <li><a href="#add_article" tabindex="-1" role="tab" id="dropdown1-tab" data-toggle="tab" aria-controls="dropdown1">發文</a></li>
                                    <li><a href="#info" tabindex="-1" role="tab" id="dropdown1-tab" data-toggle="tab" aria-controls="dropdown1">小組簡介</a></li>
                                    <li><a href="#book" tabindex="-1" role="tab" id="dropdown1-tab" data-toggle="tab" aria-controls="dropdown1">小組書單</a></li>
                                    <li><a href="#friend" tabindex="-1" role="tab" id="dropdown1-tab" data-toggle="tab" aria-controls="dropdown1">小組成員</a></li>
                                    <?php if($auth_admin):?>
                                        <li><a href="#config" tabindex="-1" role="tab" id="dropdown1-tab" data-toggle="tab" aria-controls="dropdown1">小組管理</a></li>
                                    <?php endif;?>
                                </ul>
                            </li>
                        <?php endif;?>
                    </ul>
                    <div id="myTabContent" class="tab-content">

                        <!-- 精華區 -->
                        <?php
                        if(!empty($best_article_category_results)){
                            foreach($best_article_category_results as $best_article_category_result):
                                $rs_cat_id  =(int)($best_article_category_result['cat_id']);
                                $rs_cat_name=trim($best_article_category_result['cat_name']);

                                //精華區文章 SQL
                                $sql="
                                    SELECT
                                        `user`.`member`.`name`,

                                        `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,

                                        `mssr_forum`.`mssr_forum_article`.`user_id`,
                                        `mssr_forum`.`mssr_forum_article`.`group_id`,
                                        `mssr_forum`.`mssr_forum_article`.`article_id`,
                                        0 AS `reply_id`,
                                        `mssr_forum`.`mssr_forum_article`.`keyin_cdate`,
                                        `mssr_forum`.`mssr_forum_article`.`article_like_cno` AS `like_cno`,

                                        `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                                        `mssr_forum`.`mssr_forum_article_detail`.`article_content`,
                                        '' AS `reply_content`,
                                        'article' AS `type`
                                    FROM `mssr_forum`.`mssr_forum_article_book_rev`
                                        INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                                        `mssr_forum`.`mssr_forum_article_book_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`

                                        INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                                        `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                                        INNER JOIN `user`.`member` ON
                                        `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

                                        INNER JOIN `mssr_forum`.`mssr_forum_best_article_category_rev` ON
                                        `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_best_article_category_rev`.`article_id`
                                    WHERE 1=1
                                        AND `mssr_forum`.`mssr_forum_article`.`article_type` =2
                                        AND `mssr_forum`.`mssr_forum_article`.`article_from` =2 -- 文章來源
                                        AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                                        AND `mssr_forum`.`mssr_forum_article`.`group_id`     ={$get_group_id}
                                        AND `mssr_forum`.`mssr_forum_best_article_category_rev`.`cat_id`={$rs_cat_id}

                                    ORDER BY `mssr_forum`.`mssr_forum_article`.`keyin_cdate` DESC
                                ";
                                $best_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,20),$arry_conn_mssr);
                        ?>
                        <div role="tabpanel" class="tab-pane fade" id="beat_article_<?php echo $rs_cat_id;?>" aria-labelledBy="profile-tab">
                            <table class="group_lefe_side_tab1 table table-striped beat_article_<?php echo $rs_cat_id ?>">
                                <thead class="hidden-xs"><tr class="second_tr" align="left">
                                    <td width="220"><span>討論書籍</span></td>
                                    <td width="">   <span>文章標題</span></td>
                                    <td width="120"><span>姓名    </span></td>
                                    <td width="140"><span>時間    </span></td>
                                    <td width="50"> <span>讚      </span></td>
                                    <td width="50"> <span>回應    </span></td>
                                </tr></thead>
                                <tbody>
                                <?php if(!empty($best_article_results)){
                                    foreach($best_article_results as $best_article_result):
                                        extract($best_article_result, EXTR_PREFIX_ALL, "rs");
                                        $rs_user_id         =(int)$rs_user_id;
                                        $rs_group_id        =(int)$rs_group_id;
                                        $rs_article_id      =(int)$rs_article_id;
                                        $rs_reply_id        =(int)$rs_reply_id;
                                        $rs_like_cno        =(int)$rs_like_cno;
                                        $rs_user_name       =trim($rs_name);
                                        $rs_book_sid        =trim($rs_book_sid);
                                        $rs_keyin_cdate     =trim($rs_keyin_cdate);
                                        $rs_article_title   =trim($rs_article_title);
                                        $rs_article_content =trim($rs_article_content);
                                        $rs_reply_content   =trim($rs_reply_content);
                                        $rs_type            =trim($rs_type);

                                        if($rs_type!=='article')continue;

                                        if($rs_group_id===0)$get_from=1;
                                        if($rs_group_id!==0)$get_from=2;

                                        //特殊處理
                                        $rs_book_name='';
                                        $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                        if(!empty($arry_book_infos)){$rs_book_name=trim($arry_book_infos[0]['book_name']);}else{continue;}

                                        $a_href_1="article.php?get_from=1&book_sid={$rs_book_sid}";
                                        $a_href_2="reply.php?get_from=2&article_id={$rs_article_id}";
                                        $a_href_3="user.php?user_id={$rs_user_id}&tab=1";

                                        //回文次數
                                        $sql="
                                            SELECT COUNT(*) AS `cno`
                                            FROM `mssr_forum`.`mssr_forum_reply_book_rev`
                                                INNER JOIN `mssr_forum`.`mssr_forum_reply` ON
                                                `mssr_forum`.`mssr_forum_reply_book_rev`.`reply_id`=`mssr_forum`.`mssr_forum_reply`.`reply_id`
                                            WHERE 1=1
                                                AND `mssr_forum`.`mssr_forum_reply_book_rev`.`article_id`= {$rs_article_id}
                                                AND `mssr_forum`.`mssr_forum_reply`.`reply_state`=1
                                        ";
                                        $reply_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                        $reply_article_cno=(int)($reply_article_results[0]['cno']);
                                ?>
                                <tr align="left">

                                    <!-- 討論,大解析度,start -->
                                    <td class="hidden-xs" style="border:0px;"><a href="<?php echo $a_href_1;?>"><?php echo htmlspecialchars($rs_book_name);?></a></td>
                                    <td class="hidden-xs" style="border:0px;"><a href="<?php echo $a_href_2;?>" target="_blank"><?php echo htmlspecialchars($rs_article_title);?></a></td>
                                    <td class="hidden-xs" style="border:0px;"><a href="<?php echo $a_href_3;?>"><?php echo htmlspecialchars($rs_user_name);?></a></td>
                                    <td class="hidden-xs" style="border:0px;"><?php echo htmlspecialchars($rs_keyin_cdate);?></td>
                                    <td class="hidden-xs" style="border:0px;"><?php echo htmlspecialchars($rs_like_cno);?></td>
                                    <td class="hidden-xs" style="border:0px;"><?php echo htmlspecialchars($reply_article_cno);?></td>
                                    <!-- 討論,大解析度,end -->

                                    <!-- 討論,小解析度,start -->
                                    <td class="hidden-sm hidden-md hidden-lg" style="border:0px;<?php if(isset($inx)&&$inx===0)echo 'position:relative;top:-5px;';?>">
                                        <span style="position:relative;top:-5px;font-size:16px;">
                                            <a href="<?php echo $a_href_2;?>" target="_blank"><?php echo htmlspecialchars($rs_article_title);?></a>
                                        </span><br>
                                        <span style="position:relative;top:5px;">
                                            <a href="<?php echo $a_href_1;?>"><?php echo htmlspecialchars($rs_book_name);?></a>
                                            &nbsp;&nbsp;|&nbsp;&nbsp;
                                            <a href="<?php echo $a_href_3;?>"><?php echo htmlspecialchars($rs_user_name);?></a>
                                        </span>
                                    </td>
                                    <!-- 討論,小解析度,end -->

                                </tr>
                                <?php endforeach;}else{?>
                                <tr align="left" style="height:250px;">

                                    <!-- 討論,大解析度,start -->
                                    <td class="hidden-xs" style="border:0px;" colspan="6" align="center">
                                        <span style="position:relative;top:100px;font-size:16px;">查無文章資訊。</span>
                                    </td>
                                    <!-- 討論,大解析度,end -->

                                    <!-- 討論,小解析度,start -->
                                    <td class="hidden-sm hidden-md hidden-lg" style="border:0px;<?php if(isset($inx)&&$inx===0)echo 'position:relative;top:-5px;';?>" align="center">
                                        <span style="position:relative;top:100px;font-size:16px;">查無文章資訊。</span>
                                    </td>
                                    <!-- 討論,小解析度,end -->

                                </tr>
                                <?php }?>
                                </tbody>
                            </table>
                        </div>
                        <?php endforeach;}?>

                        <!-- 首頁 -->
                        <div role="tabpanel" class="tab-pane fade in active" id="home" aria-labelledBy="home-tab">
                            <div class="alert alert-danger" role="alert" style="margin-top:20px;">
                                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                <span class="sr-only">提醒:</span>
                                請勿在小組聊天或發表與書籍無關的文章，違者依情節嚴重將給予【刪除文章】或【解散小組】的懲罰。
                            </div>

                            <!-- 近期發文 -->
                            <div class="tab_title"><?php echo htmlspecialchars($group_name);?>&nbsp;最近討論發文、回文</div>
                            <div class="dashed_ccc"></div>
                            <table class="group_lefe_side_tab1 table table-striped">
                                <?php if(!empty($article_reply_results)):?>
                                    <thead class=""><tr class="second_tr" align="left">
                                        <td width=""><span>標題</span>                      </td>
                                        <td width="100px"><span>發表時間</span>             </td>
                                        <td width="20px"class="hidden-xs"><span>讚</span>   </td>
                                    </tr></thead>
                                    <tbody>
                                        <?php
                                            $cno=0;
                                            foreach($article_reply_results as $article_reply_result):
                                                extract($article_reply_result, EXTR_PREFIX_ALL, "rs");
                                                $rs_user_id         =(int)$rs_user_id;
                                                $rs_group_id        =(int)$rs_group_id;
                                                $rs_article_id      =(int)$rs_article_id;
                                                $rs_reply_id        =(int)$rs_reply_id;
                                                $rs_like_cno        =(int)$rs_like_cno;
                                                $rs_book_sid        =trim($rs_book_sid);
                                                $rs_keyin_cdate     =trim($rs_keyin_cdate);
                                                $rs_article_title   =trim($rs_article_title);
                                                $rs_article_content =trim($rs_article_content);
                                                $rs_reply_content   =trim($rs_reply_content);
                                                $rs_type            =trim($rs_type);

                                                if($rs_group_id===0)$get_from=1;
                                                if($rs_group_id!==0)$get_from=2;

                                                if(mb_strlen($rs_article_content)>100){
                                                    $rs_article_content=mb_substr($rs_article_content,0,100)."..";
                                                }

                                                if(mb_strlen($rs_reply_content)>100){
                                                    $rs_reply_content=mb_substr($rs_reply_content,0,100)."..";
                                                }

                                                //特殊處理
                                                $rs_book_name='';
                                                $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                                if(!empty($arry_book_infos)){$rs_book_name=trim($arry_book_infos[0]['book_name']);}else{continue;}
                                                $all_book_name = $rs_book_name;
                                                if(mb_strlen($rs_book_name)>10){
                                                    $rs_book_name=mb_substr($rs_book_name,0,10)."..";
                                                }

                                                $a_href_1="article.php?get_from=1&book_sid={$rs_book_sid}";

                                            //筆數控制
                                            if($cno<5){
                                        ?>
                                        <tr align="left">
                                            <td style="border:0px;word-break:break-all;overflow:hidden;">
                                                <a target="_blank" href="reply.php?get_from=<?php echo $get_from;?>&article_id=<?php echo $rs_article_id;?>">
                                                    <span style="font-size:12pt;">● <?php echo htmlspecialchars($rs_article_title);?></span><br>
                                                    <span style="font-size:10pt;float:right;" style="text-decoration:none;" data-toggle="tooltip" title="<?php echo $all_book_name;?>">
                                                        <img width="20" height="20" style="weight:20px;height:20px;" src="../img/default/book.png" alt="Generic placeholder thumbnail">
                                                        <?php echo htmlspecialchars($rs_book_name);?>
                                                    </span>
                                                </a>
                                            </td>
                                            <td class="" style="border:0px;">
                                                <?php echo htmlspecialchars($rs_keyin_cdate);?>
                                            </td>
                                            <td class="hidden-xs" style="border:0px;">
                                                <?php echo htmlspecialchars($rs_like_cno);?>
                                            </td>
                                        </tr>
                                        <?php $cno++;}endforeach;?>
                                    </tbody>
                                <?php else:?>
                                    <thead><tr class="second_tr" align="left">
                                        <td style="height:30px;"><span></span></td>
                                    </tr></thead>
                                    <tbody>
                                        <tr align="center"><td style="border:0px;font-size:16px;">查無文章資訊。</td></tr>
                                    </tbody>
                                <?php endif;?>
                            </table>

                            <!-- 近期書單 -->
                            <div class="tab_title"><?php echo htmlspecialchars($group_name);?>&nbsp;最近興趣書單</div>
                            <div class="dashed_ccc"></div>
                            <?php if(!empty($group_book_results)){?>
                                <div class="group_lefe_side_tab2 row">
                                    <?php
                                        $cno=0;
                                        foreach($group_book_results as $group_book_result):
                                            $rs_book_sid=trim($group_book_result['book_sid']);
                                            if($rs_book_sid!==''){
                                                $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                                if(empty($arry_book_infos))continue;
                                                $rs_book_name=trim($arry_book_infos[0]['book_name']);
                                                $all_book_name = $rs_book_name;
                                                if(mb_strlen($rs_book_name)>20){
                                                    $rs_book_name=mb_substr($rs_book_name,0,20)."..";
                                                }
                                                $rs_book_img    ='../img/default/book.png';
                                                if(file_exists("../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg")){
                                                    $rs_book_img="../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg";
                                                }
                                            }
                                        //本數控制
                                        if($cno<6){
                                    ?>
                                        <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                                            <div class="thumbnail">
                                                <a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_book_sid);?>">
                                                    <img width="80" height="80" style="weight:80px;height:80px;" src="<?php echo $rs_book_img;?>" alt="Generic placeholder thumbnail">
                                                    <div class="caption" style="text-decoration:none;" data-toggle="tooltip" title="<?php echo $all_book_name;?>"><?php echo ($rs_book_name);?></div>
                                                </a>
                                            </div>
                                        </div>
                                    <?php $cno++;}endforeach;?>
                                </div>
                            <?php }else{?>
                                <table class="group_lefe_side_tab1 table table-striped">
                                    <thead><tr class="second_tr" align="left">
                                        <td style="height:30px;"><span></span></td>
                                    </tr></thead>
                                    <tbody>
                                        <tr align="center"><td style="border:0px;font-size:16px;">查無書單資訊。</td></tr>
                                    </tbody>
                                </table>
                            <?php }?>

                            <!-- 近期成員 -->
                            <div class="tab_title"><?php echo htmlspecialchars($group_name);?>&nbsp;最近加入成員</div>
                            <div class="dashed_ccc"></div>
                            <div class="group_lefe_side_tab3 row">
                                <?php
                                if(!empty($group_user_results)){
                                    $cno=0;
                                    foreach($group_user_results as $group_user_result):
                                        $rs_user_id=(int)$group_user_result['user_id'];

                                        $sql="
                                            SELECT
                                                `name`,`sex`
                                            FROM `user`.`member`
                                            WHERE 1=1
                                                AND `user`.`member`.`uid`={$rs_user_id}
                                        ";
                                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                        $rs_user_img  ='';
                                        $rs_user_name ='';
                                        $rs_user_sex  =1;
                                        if(!empty($db_results)){
                                            $rs_user_name=trim($db_results[0]['name']);
                                            $rs_user_sex =(int)$db_results[0]['sex'];
                                            if($rs_user_sex===1)$rs_user_img='../img/default/user_boy.png';
                                            if($rs_user_sex===2)$rs_user_img='../img/default/user_girl.png';

                                            if(isset($file_server_enable)&&($file_server_enable)){
                                                //$rs_user_img_ftp_root="public_html/mssr/info/user";
                                                //$rs_user_img_ftp_path="{$rs_user_img_ftp_root}/{$rs_user_id}/forum/user_sticker";
                                                //$arry_rs_user_img_ftp_file=ftp_nlist($ftp_conn,$rs_user_img_ftp_path);
                                                //if(isset($arry_rs_user_img_ftp_file[0])){
                                                //    $rs_user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                                                //}
                                                if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg")){
                                                    $rs_user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                                                }
                                            }else{
                                                if(file_exists("../../../info/user/{$rs_user_id}/forum/user_sticker/1.jpg")){
                                                    $rs_user_img="../../../info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                                                }
                                            }
                                        }

                                        $rs_user_type   =(int)$group_user_result['user_type'];
                                        $rs_user_type_html='';
                                        switch($rs_user_type){
                                            case 1:
                                                $rs_user_type_html='一般組員';
                                            break;
                                            case 2:
                                                $rs_user_type_html='一般版主';
                                            break;
                                            case 3:
                                                $rs_user_type_html='高級版主';
                                            break;
                                            default:
                                                continue;
                                            break;
                                        }

                                        $rs_user_state  =(int)$group_user_result['user_state'];
                                        $rs_user_state_html='';
                                        switch($rs_user_state){
                                            case 1:
                                                $rs_user_state_html='啟用';
                                            break;
                                            case 2:
                                                $rs_user_state_html='停用';
                                            break;
                                            case 3:
                                                $rs_user_state_html='申請中';
                                            break;
                                            default:
                                                continue;
                                            break;
                                        }
                                    //筆數控制
                                    if($cno<6){
                                ?>
                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                                    <div class="thumbnail">
                                        <a href="user.php?user_id=<?php echo $rs_user_id;?>&tab=1">
                                            <img width="80" height="80" style="weight:80px;height:80px;" src="<?php echo $rs_user_img;?>" alt="Generic placeholder thumbnail">
                                            <div class="caption">
                                                <?php echo htmlspecialchars($rs_user_name);?>
                                                <br>
                                                (<?php echo htmlspecialchars($rs_user_type_html);?>,<?php echo htmlspecialchars($rs_user_state_html);?>)
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <?php $cno++;}endforeach;}?>
                            </div>

                        </div>

                        <!-- 討論 -->
                        <div role="tabpanel" class="tab-pane fade" id="article" aria-labelledBy="profile-tab">
                            <table class="group_lefe_side_tab1 table table-striped table_article">
                                <thead class="hidden-xs"><tr class="second_tr" align="left">
                                    <td width="220"><span>討論書籍</span></td>
                                    <td width="">   <span>文章標題</span></td>
                                    <td width="120"><span>姓名    </span></td>
                                    <td width="140"><span>時間    </span></td>
                                    <td width="50"> <span>讚      </span></td>
                                    <td width="50"> <span>回應    </span></td>
                                </tr></thead>
                                <tbody>
                                <?php if(!empty($article_reply_results)){
                                    foreach($article_reply_results as $article_reply_result):
                                        extract($article_reply_result, EXTR_PREFIX_ALL, "rs");
                                        $rs_user_id         =(int)$rs_user_id;
                                        $rs_group_id        =(int)$rs_group_id;
                                        $rs_article_id      =(int)$rs_article_id;
                                        $rs_reply_id        =(int)$rs_reply_id;
                                        $rs_like_cno        =(int)$rs_like_cno;
                                        $rs_user_name       =trim($rs_user_name);
                                        $rs_book_sid        =trim($rs_book_sid);
                                        $rs_keyin_cdate     =trim($rs_keyin_cdate);
                                        $rs_article_title   =trim($rs_article_title);
                                        $rs_article_content =trim($rs_article_content);
                                        $rs_reply_content   =trim($rs_reply_content);
                                        $rs_type            =trim($rs_type);

                                        if($rs_type!=='article')continue;

                                        if($rs_group_id===0)$get_from=1;
                                        if($rs_group_id!==0)$get_from=2;

                                        //特殊處理
                                        $rs_book_name='';
                                        $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                        if(!empty($arry_book_infos)){$rs_book_name=trim($arry_book_infos[0]['book_name']);}else{continue;}

                                        $a_href_1="article.php?get_from=1&book_sid={$rs_book_sid}";
                                        $a_href_2="reply.php?get_from=2&article_id={$rs_article_id}";
                                        $a_href_3="user.php?user_id={$rs_user_id}&tab=1";

                                        //回文次數
                                        $sql="
                                            SELECT COUNT(*) AS `cno`
                                            FROM `mssr_forum`.`mssr_forum_reply_book_rev`
                                                INNER JOIN `mssr_forum`.`mssr_forum_reply` ON
                                                `mssr_forum`.`mssr_forum_reply_book_rev`.`reply_id`=`mssr_forum`.`mssr_forum_reply`.`reply_id`
                                            WHERE 1=1
                                                AND `mssr_forum`.`mssr_forum_reply_book_rev`.`article_id`= {$rs_article_id}
                                                AND `mssr_forum`.`mssr_forum_reply`.`reply_state`=1
                                        ";
                                        $reply_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                        $reply_article_cno=(int)($reply_article_results[0]['cno']);
                                ?>
                                <tr align="left">

                                    <!-- 討論,大解析度,start -->
                                    <td class="hidden-xs" style="border:0px;"><a href="<?php echo $a_href_1;?>"><?php echo htmlspecialchars($rs_book_name);?></a></td>
                                    <td class="hidden-xs" style="border:0px;"><a href="<?php echo $a_href_2;?>" target="_blank"><?php echo htmlspecialchars($rs_article_title);?></a></td>
                                    <td class="hidden-xs" style="border:0px;"><a href="<?php echo $a_href_3;?>"><?php echo htmlspecialchars($rs_user_name);?></a></td>
                                    <td class="hidden-xs" style="border:0px;"><?php echo htmlspecialchars($rs_keyin_cdate);?></td>
                                    <td class="hidden-xs" style="border:0px;"><?php echo htmlspecialchars($rs_like_cno);?></td>
                                    <td class="hidden-xs" style="border:0px;"><?php echo htmlspecialchars($reply_article_cno);?></td>
                                    <!-- 討論,大解析度,end -->

                                    <!-- 討論,小解析度,start -->
                                    <td class="hidden-sm hidden-md hidden-lg" style="border:0px;<?php if(isset($inx)&&$inx===0)echo 'position:relative;top:-5px;';?>">
                                        <span style="position:relative;top:-5px;font-size:16px;">
                                            <a href="<?php echo $a_href_2;?>" target="_blank"><?php echo htmlspecialchars($rs_article_title);?></a>
                                        </span><br>
                                        <span style="position:relative;top:5px;">
                                            <a href="<?php echo $a_href_1;?>"><?php echo htmlspecialchars($rs_book_name);?></a>
                                            &nbsp;&nbsp;|&nbsp;&nbsp;
                                            <a href="<?php echo $a_href_3;?>"><?php echo htmlspecialchars($rs_user_name);?></a>
                                        </span>
                                    </td>
                                    <!-- 討論,小解析度,end -->

                                </tr>
                                <?php endforeach;}else{?>
                                <tr align="left" style="height:250px;">

                                    <!-- 討論,大解析度,start -->
                                    <td class="hidden-xs" style="border:0px;" colspan="6" align="center">
                                        <span style="position:relative;top:100px;font-size:16px;">查無文章資訊。</span>
                                    </td>
                                    <!-- 討論,大解析度,end -->

                                    <!-- 討論,小解析度,start -->
                                    <td class="hidden-sm hidden-md hidden-lg" style="border:0px;<?php if(isset($inx)&&$inx===0)echo 'position:relative;top:-5px;';?>" align="center">
                                        <span style="position:relative;top:100px;font-size:16px;">查無文章資訊。</span>
                                    </td>
                                    <!-- 討論,小解析度,end -->

                                </tr>
                                <?php }?>
                                </tbody>
                            </table>
                        </div>

                        <!-- 發文 -->
                        <div role="tabpanel" class="tab-pane fade" id="add_article" aria-labelledBy="profile-tab">
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-md-3 col-lg3 text-center visible-xs add_article_help-visible-xs" style="margin-bottom:15px;">
                                    <?php if($auth_add_article  && !empty($arry_group_booklist)):?>
                                        <a href="javascript:void(0);" class="btn btn-primary btn-block"
                                        role="button" style="color:#ffffff;" onclick="show_bookstore_rec(0);void(0);"
                                        >引用書店推薦</a>

                                        <a href="javascript:void(0);" class="btn btn-primary btn-block"
                                        role="button" style="color:#ffffff;" onclick="$('div.eagle_lv_1').fadeIn();void(0);"
                                        >使用發文輔助</a>
                                        <div class="row eagle_lv_1" style="display:none;">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg12" style="margin-top:10px;">
                                                <select class="form-control eagle_lv_1 select_eagle_lv_1" onchange="article_eagle(eagle_lv=1);void(0);">
                                                    <option disabled="disabled" selected>請選擇書本類型</option>
                                                    <?php foreach($article_eagle_content as $key=>$arry_val):?>
                                                        <option>&nbsp;&nbsp;<?php echo trim($key);?></option>
                                                    <?php endforeach;?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php endif;?>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <?php if($auth_add_article && !empty($arry_group_booklist)):?>
										<?php if($enough_point): ?>
	                                        <div class="row eagle_lv_5" style="border-right:1px solid #eeeeee;">
	                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
	                                                <form id="Form1"  name="Form1" method="post" onsubmit="return false;">
	                                                    <?php if(count($arry_group_booklist)>1):?>
	                                                        <select class="form-control book_sid" id="book_sid" name="book_sid" style="margin-bottom:10px;">
	                                                            <option value="" disabled="disabled" selected>請選擇一本書來發文......</option>
	                                                            <?php foreach($arry_group_booklist as $key=>$val):?>
	                                                                <option value="<?php echo trim($key);?>"><?php echo trim($val);?></option>
	                                                            <?php endforeach;?>
	                                                        </select>
	                                                    <?php else:?>
	                                                        <select class="form-control" id="book_sid" name="book_sid" style="display:none;margin-bottom:10px;">
	                                                            <?php foreach($arry_group_booklist as $key=>$val):?>
	                                                                <option value="<?php echo trim($key);?>" selected><?php echo trim($val);?></option>
	                                                            <?php endforeach;?>
	                                                        </select>
	                                                    <?php endif;?>
	                                                    <div class="form-group">
	                                                        <input type="text" id="article_title" name="article_title" class="form-control" placeholder="1.請輸入文章標題">
	                                                    </div>
	                                                    <div class="row">
	                                                        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
	                                                            <div class="form-group">
	                                                                <textarea class="form-control article_content" id="article_content[]" name="article_content[]" rows="10" placeholder="2.請輸入文章內容" style="resize:none;"></textarea>
	                                                            </div>
	                                                        </div>
	                                                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center hidden-xs add_article_help-hidden-xs">
	                                                            <?php if($auth_add_article  && !empty($arry_group_booklist)):?>
	                                                                <a href="javascript:void(0);" class="btn btn-primary btn-block"
	                                                                role="button" style="color:#ffffff;" onclick="show_bookstore_rec(0);void(0);"
	                                                                >引用書店推薦</a>

	                                                                <a href="javascript:void(0);" class="btn btn-primary btn-block"
	                                                                role="button" style="color:#ffffff;" onclick="$('div.eagle_lv_1').fadeIn();void(0);"
	                                                                >使用發文輔助</a>
	                                                                <div class="row eagle_lv_1" style="display:none;">
	                                                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg12" style="margin-top:10px;">
	                                                                        <select class="form-control eagle_lv_1 select_eagle_lv_1" onchange="article_eagle(eagle_lv=1);void(0);">
	                                                                            <option disabled="disabled" selected>請選擇書本類型</option>
	                                                                            <?php foreach($article_eagle_content as $key=>$arry_val):?>
	                                                                                <option>&nbsp;&nbsp;<?php echo trim($key);?></option>
	                                                                            <?php endforeach;?>
	                                                                        </select>
	                                                                    </div>
	                                                                </div>
	                                                            <?php endif;?>
	                                                        </div>
	                                                    </div>
	                                                    <select class="form-control" id="article_category" name="article_category" style="margin-bottom:10px;">
	                                                        <option value="" disabled="disabled" selected>3.請選擇發文類型</option>
	                                                        <option value="1">綜合討論</option>
	                                                        <option value="4">我想要描述或釐清書中的重要內容</option>
	                                                        <option value="5">我想要表達讀完書後的感受</option>
	                                                        <option value="6">我想要提出關於這本書的疑問與發現</option>
	                                                        <option value="7">我有一些新點子想要嘗試</option>
	                                                        <option value="99">其他</option>
	                                                    </select>

	                                                    <div class="checkbox">
	                                                       <label>
	                                                           <input type="checkbox" id="book_page_send_chk" name="book_page_send_chk">同時發佈在書頁
	                                                       </label>
	                                                    </div>

	                                                    <div class="checkbox">
	                                                       <label>
	                                                           <input type="checkbox" id="send_chk">我已閱讀過並同意遵守討論區規則
	                                                       </label>
	                                                        <a target="_blank" href="forum.php?method=view_mssr_forum_article_reply_rule" style="color:#428bca;">按這裡檢視討論區規則</a>
	                                                    </div>
	                                                    <hr></hr>
	                                                    <button type="button" class="btn btn-default pull-left btn_add_article" onclick="Btn_add_article();void(0);" style="">送出</button>
	                                                    <button type="button" class="btn btn-default pull-right next_step hidden"
	                                                    onclick="$('#article_category, .btn_add_article, .prev_step').show();
	                                                    $('#article_title, .article_content, .book_sid').hide();
	                                                    $(this).hide();
	                                                    void(0);">下一步</button>
	                                                    <button type="button" class="btn btn-default pull-right prev_step hidden" style="display:none;margin:0 3px;"
	                                                    onclick="$('#article_category, .btn_add_article').hide();
	                                                    $('#article_title, .article_content, .book_sid, .next_step').show();
	                                                    $(this).hide();
	                                                    void(0);">上一步</button>
	                                                    <div class="form-group hidden">
	                                                        <input type="text" class="form-control" name="eagle_code" value="" id="eagle_code">
	                                                        <input type="text" class="form-control" name="article_from" value="<?php echo (int)$get_from;?>">
	                                                        <input type="text" class="form-control" name="group_id" value="<?php echo (int)$get_group_id;?>">
	                                                        <input type="text" class="form-control" name="send_url" value="<?php echo trim($send_url);?>">
	                                                        <input type="text" class="form-control" name="method" value="add_article">
	                                                    </div>
	                                                </form>
	                                            </div>
	                                        </div>
										<?php endif;?>
                                    <?php endif;?>
                                </div>
                                <?php if(!$auth_add_article):?>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
                                        您尚未加入此小組...... 請按上方的<span style='color:#4298ce;'>【加入小組】</span>按鈕
                                    </div>
                                <?php else:?>
                                    <?php if(empty($arry_group_booklist)):?>
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
                                            發文前，請先建立<span style='color:#4298ce;'>【小組書單】</span>......
                                        </div>
                                    <?php endif;?>
                                <?php endif;?>
								<?php if(!$enough_point): ?>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
										<font color="red">※你目前的點數不足30點，無法發文，您可以多回覆其他人的文章來賺取點數。</font>
									</div>
								<?php endif;?>
                            </div>
                        </div>

                        <!-- 書單 -->
                        <div role="tabpanel" class="tab-pane fade" id="book" aria-labelledBy="profile-tab">
                            <div class="group_lefe_side_tab2 row">
                                <?php if($auth_add_article && $auth_add_reply):?>
                                    <div class="input-group" style="position:relative;padding:10px 20px 20px 15px;">
                                        <input type="text" class="group_booklist_book_name form-control" name="group_booklist_book_name" placeholder="請選擇或輸入一本讀過的書籍來建立小組書單">
                                        <input type="hidden" class="group_booklist_book_sid form-control" name="group_booklist_book_sid" value="">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                選擇書籍 <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                <?php
                                                if(!empty($arry_my_borrow)){
                                                    foreach($arry_my_borrow as $rs_book_sid=>$rs_book_name):
                                                        $rs_book_name=trim($rs_book_name);
                                                ?>
                                                <li><a href="javascript:void(0);" onclick="auto_group_booklist(this);void(0);"><?php echo htmlspecialchars($rs_book_name);?></a></li>
                                                <?php endforeach;}?>
                                            </ul>
                                        </div>
                                        <div class="input-group-btn">
                                            <button type="button" class="form-control btn btn-default btn-xs" style="position:relative;margin:0 5px;"
                                            onclick="add_group_booklist(this);void(0);">建立</button>
                                        </div>
                                    </div>
                                <?php endif;?>
                                <?php
                                if(!empty($group_book_results)){
                                    foreach($group_book_results as $group_book_result):
                                        $rs_book_sid=trim($group_book_result['book_sid']);
                                        if($rs_book_sid!==''){
                                            $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                            if(empty($arry_book_infos))continue;
                                            $rs_book_name=trim($arry_book_infos[0]['book_name']);
                                            if(mb_strlen($rs_book_name)>20){
                                                $rs_book_name=mb_substr($rs_book_name,0,20)."..";
                                            }
                                            $rs_book_img    ='../img/default/book.png';
                                            if(file_exists("../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg")){
                                                $rs_book_img="../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg";
                                            }
                                        }
                                ?>
                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                                    <div class="thumbnail">
                                        <a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_book_sid);?>">
                                            <img width="80" height="80" style="weight:80px;height:80px;" src="<?php echo $rs_book_img;?>" alt="Generic placeholder thumbnail">
                                            <div class="caption"><?php echo ($rs_book_name);?></div>
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach;}?>
                            </div>
                        </div>

                        <!-- 成員 -->
                        <div role="tabpanel" class="tab-pane fade" id="friend" aria-labelledBy="profile-tab">
                            <div class="group_lefe_side_tab3 row">
                                <?php if($auth_add_article && $auth_add_reply):?>
                                    <div class="input-group" style="position:relative;padding:10px 20px 20px 15px;">
                                        <input type="text" class="group_user_name form-control" name="group_user_name" placeholder="請選擇或輸入一位書友來邀請加入小組">
                                        <input type="hidden" class="group_user_id form-control" name="group_user_id" value="0">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                選擇書友 <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                <?php
                                                if(!empty($arry_forum_friend)){
                                                    foreach($arry_forum_friend as $friend_id=>$friend_name):
                                                        $friend_id=(int)($friend_id);
                                                        $friend_name=trim($friend_name);
                                                ?>
                                                <li><a href="javascript:void(0);" onclick="auto_group_user(this);void(0);"><?php echo htmlspecialchars($friend_name);?></a></li>
                                                <?php endforeach;}?>
                                            </ul>
                                        </div>
                                        <div class="input-group-btn">
                                            <button type="button" class="form-control btn btn-default btn-xs" style="position:relative;margin:0 5px;"
                                            onclick="add_request_join_us_group(this);void(0);">邀請</button>
                                        </div>
                                    </div>
                                <?php endif;?>
                                <?php
                                if(!empty($group_user_results)){
                                    foreach($group_user_results as $group_user_result):
                                        $rs_user_id=(int)$group_user_result['user_id'];

                                        $sql="
                                            SELECT
                                                `name`,`sex`
                                            FROM `user`.`member`
                                            WHERE 1=1
                                                AND `user`.`member`.`uid`={$rs_user_id}
                                        ";
                                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                        $rs_user_img  ='';
                                        $rs_user_name ='';
                                        $rs_user_sex  =1;
                                        if(!empty($db_results)){
                                            $rs_user_name=trim($db_results[0]['name']);
                                            $rs_user_sex =(int)$db_results[0]['sex'];
                                            if($rs_user_sex===1)$rs_user_img='../img/default/user_boy.png';
                                            if($rs_user_sex===2)$rs_user_img='../img/default/user_girl.png';

                                            if(isset($file_server_enable)&&($file_server_enable)){
                                                //$rs_user_img_ftp_root="public_html/mssr/info/user";
                                                //$rs_user_img_ftp_path="{$rs_user_img_ftp_root}/{$rs_user_id}/forum/user_sticker";
                                                //$arry_rs_user_img_ftp_file=ftp_nlist($ftp_conn,$rs_user_img_ftp_path);
                                                //if(isset($arry_rs_user_img_ftp_file[0])){
                                                //    $rs_user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                                                //}
                                                if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg")){
                                                    $rs_user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                                                }
                                            }else{
                                                if(file_exists("../../../info/user/{$rs_user_id}/forum/user_sticker/1.jpg")){
                                                    $rs_user_img="../../../info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                                                }
                                            }
                                        }

                                        $rs_user_type   =(int)$group_user_result['user_type'];
                                        $rs_user_type_html='';
                                        switch($rs_user_type){
                                            case 1:
                                                $rs_user_type_html='一般組員';
                                            break;
                                            case 2:
                                                $rs_user_type_html='一般版主';
                                            break;
                                            case 3:
                                                $rs_user_type_html='高級版主';
                                            break;
                                            default:
                                                continue;
                                            break;
                                        }

                                        $rs_user_state  =(int)$group_user_result['user_state'];
                                        $rs_user_state_html='';
                                        switch($rs_user_state){
                                            case 1:
                                                $rs_user_state_html='啟用';
                                            break;
                                            case 2:
                                                $rs_user_state_html='停用';
                                            break;
                                            case 3:
                                                $rs_user_state_html='申請中';
                                            break;
                                            default:
                                                continue;
                                            break;
                                        }
                                ?>
                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                                    <div class="thumbnail">
                                        <a href="user.php?user_id=<?php echo $rs_user_id;?>&tab=1">
                                            <img width="80" height="80" style="weight:80px;height:80px;" src="<?php echo $rs_user_img;?>" alt="Generic placeholder thumbnail">
                                            <div class="caption">
                                                <?php echo htmlspecialchars($rs_user_name);?>
                                                <br>
                                                (<?php echo htmlspecialchars($rs_user_type_html);?>,<?php echo htmlspecialchars($rs_user_state_html);?>)
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach;}?>
                            </div>
                        </div>

                        <!-- 管理 -->
                        <div role="tabpanel" class="tab-pane fade" id="config" aria-labelledBy="profile-tab">
                            <table class="table">
                                <tbody><tr align="center">
                                    <td style="border:0px;">
                                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="headingOne">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                        <h4 class="panel-title">
                                                            管理成員
                                                        </h4>
                                                    </a>
                                                </div>
                                                <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                                    <div class="panel-body">
                                                        <table class="group_lefe_side_tab1 table"
                                                        style="position:relative;margin-top:5px;margin-bottom:10px;border:1px solid #ebebeb;font-size:16px;">
                                                            <thead><tr class="second_tr" align="center">
                                                                <td width="" align="left"><span>姓名</span></td>
                                                                <td width="200"><span>身分</span></td>
                                                                <td width="200"><span>狀態</span></td>
                                                            </tr></thead>
                                                            <tbody>
                                                            <?php
                                                            if(!empty($group_user_results)){
                                                                foreach($group_user_results as $group_user_result):
                                                                    $rs_user_id=(int)$group_user_result['user_id'];

                                                                    $sql="
                                                                        SELECT
                                                                            `name`,`sex`
                                                                        FROM `user`.`member`
                                                                        WHERE 1=1
                                                                            AND `user`.`member`.`uid`={$rs_user_id}
                                                                    ";
                                                                    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                                                    $rs_user_img  ='';
                                                                    $rs_user_name ='';
                                                                    $rs_user_sex  =1;
                                                                    if(!empty($db_results)){
                                                                        $rs_user_name=trim($db_results[0]['name']);
                                                                        $rs_user_sex =(int)$db_results[0]['sex'];
                                                                        if($rs_user_sex===1)$rs_user_img='../img/default/user_boy.png';
                                                                        if($rs_user_sex===2)$rs_user_img='../img/default/user_girl.png';
                                                                    }

                                                                    $rs_user_type   =(int)$group_user_result['user_type'];
                                                                    $rs_user_type_html='';
                                                                    switch($rs_user_type){
                                                                        case 1:
                                                                            $rs_user_type_html='一般組員';
                                                                        break;
                                                                        case 2:
                                                                            $rs_user_type_html='一般版主';
                                                                        break;
                                                                        case 3:
                                                                            $rs_user_type_html='高級版主';
                                                                        break;
                                                                        default:
                                                                            continue;
                                                                        break;
                                                                    }

                                                                    $rs_user_state  =(int)$group_user_result['user_state'];
                                                                    $rs_user_state_html='';
                                                                    switch($rs_user_state){
                                                                        case 1:
                                                                            $rs_user_state_html='啟用';
                                                                        break;
                                                                        case 2:
                                                                            $rs_user_state_html='停用';
                                                                        break;
                                                                        case 3:
                                                                            $rs_user_state_html='申請中';
                                                                        break;
                                                                        default:
                                                                            continue;
                                                                        break;
                                                                    }
                                                            ?>
                                                            <tr align="center">
                                                                <td style="border:0px;" align="left"><?php echo htmlspecialchars($rs_user_name);?></td>
                                                                <td style="border:0px;">
                                                                    <div class="form-group">
                                                                        <select class="form-control input-sm" onchange="edit_group_user_type(this,<?php echo $rs_user_id;?>);void(0);">
                                                                            <?php for($i=1;$i<4;$i++):?>
                                                                            <?php
                                                                                $auth_option[1]=false;
                                                                                $auth_option[2]=false;
                                                                                $auth_option[3]=false;
                                                                                if($i===1){
                                                                                    $option_val=1;$option_txt='一般組員';
                                                                                    if(in_array($user_type,array(2,3))&&in_array($rs_user_type,array(1))&&$rs_user_id!==$sess_user_id){
                                                                                        $auth_option[$i]=true;
                                                                                    }
                                                                                    if(in_array($user_type,array(3))&&in_array($rs_user_type,array(1,2))&&$rs_user_id!==$sess_user_id){
                                                                                        $auth_option[$i]=true;
                                                                                    }
                                                                                }
                                                                                if($i===2){
                                                                                    $option_val=2;$option_txt='一般版主';
                                                                                    if(in_array($user_type,array(2,3))&&in_array($rs_user_type,array(1,2))&&$rs_user_id!==$sess_user_id){
                                                                                        $auth_option[$i]=true;
                                                                                    }
                                                                                }
                                                                                if($i===3){
                                                                                    $option_val=3;$option_txt='高級版主';
                                                                                    if(in_array($user_type,array(3))&&in_array($rs_user_type,array(2)))$auth_option[$i]=true;
                                                                                }
                                                                            ?>
                                                                            <option value="<?php echo $option_val;?>"
                                                                            <?php if((int)$rs_user_type===(int)$i)echo 'selected';?>
                                                                            <?php if(!$auth_option[$i])echo 'disabled';?>>
                                                                                <?php echo $option_txt;?>
                                                                            </option>
                                                                            <?php endfor;?>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <td style="border:0px;">
                                                                    <div class="form-group">
                                                                        <select class="form-control input-sm" onchange="edit_group_user_state(this,<?php echo $rs_user_id;?>);void(0);">
                                                                            <?php for($i=1;$i<4;$i++):?>
                                                                            <?php
                                                                                $auth_option[1]=false;
                                                                                $auth_option[2]=false;
                                                                                $auth_option[3]=false;
                                                                                if($i===1){
                                                                                    $option_val=1;$option_txt='啟用';
                                                                                    if(in_array($rs_user_state,array(2,3))&&$rs_user_id!==$sess_user_id){
                                                                                        $auth_option[$i]=true;
                                                                                    }
                                                                                }
                                                                                if($i===2){
                                                                                    $option_val=2;$option_txt='停用';
                                                                                    if(in_array($rs_user_state,array(1,3))&&$rs_user_id!==$sess_user_id&&!in_array($rs_user_type,array(2,3))){
                                                                                        $auth_option[$i]=true;
                                                                                    }
                                                                                    if(in_array($user_type,array(3))&&in_array($rs_user_state,array(1,3))&&$rs_user_id!==$sess_user_id&&!in_array($rs_user_type,array(3))){
                                                                                        $auth_option[$i]=true;
                                                                                    }
                                                                                }
                                                                                if($i===3){
                                                                                    $option_val=3;$option_txt='申請中';
                                                                                }
                                                                            ?>
                                                                            <option value="<?php echo $option_val;?>"
                                                                            <?php if((int)$rs_user_state===(int)$i)echo 'selected';?>
                                                                            <?php if(!$auth_option[$i])echo 'disabled';?>>
                                                                                <?php echo $option_txt;?>
                                                                            </option>
                                                                            <?php endfor;?>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <?php endforeach;}?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="headingTwo">
                                                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                        <h4 class="panel-title">
                                                            管理興趣書單
                                                        </h4>
                                                    </a>
                                                </div>
                                                <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                                    <div class="panel-body">
                                                        <table class="group_lefe_side_tab1 table"
                                                        style="position:relative;margin-top:5px;margin-bottom:10px;border:1px solid #ebebeb;font-size:16px;">
                                                            <thead><tr class="second_tr" align="center">
                                                                <td width="" align="left"><span>書名</span></td>
                                                                <td width="100" align="center"><span>設定</span></td>
                                                            </tr></thead>
                                                            <tbody>
                                                            <?php
                                                            if(!empty($group_book_results)){
                                                                foreach($group_book_results as $group_book_result):
                                                                    $rs_book_sid=trim($group_book_result['book_sid']);
                                                                    if($rs_book_sid!==''){
                                                                        $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                                                        if(empty($arry_book_infos))continue;
                                                                        $rs_book_name=trim($arry_book_infos[0]['book_name']);
                                                                        $rs_book_img    ='../img/default/book.png';
                                                                        if(file_exists("../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg")){
                                                                            $rs_book_img="../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg";
                                                                        }
                                                                    }
                                                            ?>
                                                            <tr align="center">
                                                                <td style="border:0px;" align="left"><?php echo htmlspecialchars($rs_book_name);?></td>
                                                                <td style="border:0px;" align="center">
                                                                    <div class="form-group" style="position:relative;">
                                                                        <button type="button" class="btn btn-default"
                                                                        onclick="del_group_booklist(this,<?php echo $get_group_id;?>,'<?php echo $rs_book_sid;?>');void(0);"
                                                                        >移出</button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <?php endforeach;}?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="headingThree">
                                                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                        <h4 class="panel-title">
                                                            管理精華區類別
                                                        </h4>
                                                    </a>
                                                </div>
                                                <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                                                    <div class="panel-body">
                                                        <div class="input-group" style="position:relative;padding:10px 5px 20px 0px;">
                                                            <input type="text" class="cat_name form-control" name="cat_name" placeholder="請輸入類別名稱，來建立新的精華區類別">
                                                            <div class="input-group-btn">
                                                                <button type="button" class="form-control btn btn-default btn-xs" style="position:relative;margin:0 5px;"
                                                                onclick="add_best_article_category(this);void(0);">建立</button>
                                                            </div>
                                                        </div>
                                                        <table class="group_lefe_side_tab1 table table_best_article_category"
                                                        style="position:relative;margin-top:5px;margin-bottom:10px;border:1px solid #ebebeb;font-size:16px;">
                                                            <thead><tr class="second_tr" align="center">
                                                                <td width="" align="left"><span>類別名稱</span></td>
                                                                <td width="150" align="center"><span>設定</span></td>
                                                            </tr></thead>
                                                            <tbody>
                                                            <?php
                                                            if(!empty($best_article_category_results)){
                                                                foreach($best_article_category_results as $best_article_category_result):
                                                                    $rs_cat_id  =(int)($best_article_category_result['cat_id']);
                                                                    $rs_cat_name=trim($best_article_category_result['cat_name']);
                                                            ?>
                                                            <tr align="center">
                                                                <td style="border:0px;" align="left"><?php echo htmlspecialchars($rs_cat_name);?></td>
                                                                <td style="border:0px;" align="center">
                                                                    <div class="form-group" style="position:relative;">
                                                                        <button type="button" class="btn btn-default"
                                                                        onclick="del_best_article_category(this,<?php echo $get_group_id;?>,<?php echo $rs_cat_id;?>);void(0);"
                                                                        >移除</button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <?php endforeach;}?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="headingFour">
                                                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                                        <h4 class="panel-title">
                                                            管理小組
                                                        </h4>
                                                    </a>
                                                </div>
                                                <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
                                                    <div class="panel-body">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                                <form id="Form2" name="Form2" method="post" onsubmit="return false;">
                                                                    <div style='font-size:15px;color:#999999;'>
                                                                        <span class='hidden-xs'>小組類型：</span>
                                                                       <label class="checkbox-inline">
                                                                          <input type="radio" name="group_type" value="1" <?php if($group_type===1)echo 'checked';?>>
                                                                          公開小組
                                                                       </label>
                                                                       <label class="checkbox-inline">
                                                                          <input type="radio" name="group_type" value="2" <?php if($group_type===2)echo 'checked';?>>
                                                                          私密小組
                                                                       </label>
                                                                    </div>
                                                                    <div class="form-group" style="position:relative;margin-top:25px;">
                                                                        <input class="form-control" type="text" id="group_name" name="group_name" placeholder="請輸入小組名稱"
                                                                        value='<?php echo htmlspecialchars($group_name);?>'>
                                                                    </div>
                                                                    <div class="form-group" style="position:relative;margin-top:25px;">
                                                                        <textarea class="form-control" id="group_content" name="group_content" rows="3" placeholder="請輸入小組介紹" style="resize:none;"><?php echo htmlspecialchars($group_content);?></textarea>
                                                                    </div>
                                                                    <div class="form-group" style="position:relative;margin-top:25px;">
                                                                        <textarea class="form-control" id="group_rule" name="group_rule" rows="3" placeholder="請輸入小組規範" style="resize:none;"><?php echo htmlspecialchars($group_rule);?></textarea>
                                                                    </div>
                                                                    <hr></hr>

                                                                    <div class="form-group pull-right" style="position:relative;margin-bottom:25px;">
                                                                        <button type="button" id="btn_save_group_info" class="btn btn-default">儲存</button>
                                                                    </div>

                                                                    <div class="form-group hidden">
                                                                        <input type="hidden" class="form-control" name="group_id" value="<?php echo (int)($get_group_id);?>">
                                                                        <input type="hidden" class="form-control" name="method" value="edit_group">
                                                                        <input type="hidden" class="form-control" name="send_url" value="#">
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel panel-default hidden-xs">
                                                <div class="panel-heading" role="tab" id="headingFive">
                                                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                                        <h4 class="panel-title">
                                                            小組大頭貼設定
                                                        </h4>
                                                    </a>
                                                </div>
                                                <div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFive">
                                                    <div class="panel-body">
                                                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                                            <pre style="background-color:#ffffdd;">使用中大頭貼</pre>
                                                            <img src="<?php echo $group_img;?>" style="border:1px solid #e1e1e1;"
                                                            width="160" height="160" border="0" alt="group_img"/>
                                                            <hr></hr>
                                                            <form action="../controller/img.php" method="post" class="ajax_group_sticker_form">
                                                                <span class="btn btn-default btn-xs btn_file">
                                                                    重新選擇<input type="file" name="group_sticker_file" class="group_sticker_file">
                                                                </span>
                                                                <button type="button" class="btn btn-default btn-xs"
                                                                onclick="ajax_group_sticker_upload(this);void(0);"
                                                                >上傳</button>
                                                                <input type="hidden" class="form-control" name="group_id" value="<?php echo (int)$get_group_id;?>">
                                                                <input type="hidden" class="form-control" name="method" value="add_group_sticker_img">
                                                                <input type="hidden" class="form-control" name="send_url" value="#">
                                                            </form>
                                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="color:red;">
                                                                <br>
                                                                <p class="text-left">1.檔案類型限定為.jpg檔案</p>
                                                                <p class="text-left">2.檔案大小不得超過100KB</p>
                                                            </div>
                                                        </div>
                                                        <?php if(isset($group_img_size)):?>
                                                            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                                                                <pre style="background-color:#ffffdd;">裁切大頭貼</pre>
                                                                <div class="row">
                                                                    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                                                                        <img id="old_group_sticker" src="<?php echo $group_img;?>"
                                                                        style="border:1px solid #e1e1e1;" border="0" alt="group_img"
                                                                        width="<?php echo $group_img_size[0];?>" height="<?php echo $group_img_size[1];?>"/>
                                                                    </div>
                                                                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                                                        <div style="overflow:hidden;width:160px;height:160px;">
                                                                            <img id="new_group_sticker" src="<?php echo $group_img;?>"
                                                                            style="border:1px solid #e1e1e1;" border="0" alt="group_img"
                                                                            width="<?php echo $group_img_size[0];?>" height="<?php echo $group_img_size[1];?>"/>
                                                                        </div>
                                                                        <hr></hr>
                                                                        <form action="../controller/img.php" method="post" class="edit_group_sticker_form">
                                                                            <input type="hidden" name="group_id" value="<?php echo (int)$get_group_id;?>">
                                                                            <input type="hidden" name="group_sticker_x1" value="0"   id="group_sticker_x1">
                                                                            <input type="hidden" name="group_sticker_y1" value="0"   id="group_sticker_y1">
                                                                            <input type="hidden" name="group_sticker_x2" value="0"   id="group_sticker_x2">
                                                                            <input type="hidden" name="group_sticker_y2" value="0"   id="group_sticker_y2">
                                                                            <input type="hidden" name="group_sticker_w"  value="160" id="group_sticker_w">
                                                                            <input type="hidden" name="group_sticker_h"  value="160" id="group_sticker_h">
                                                                            <input type="hidden" class="form-control" name="method" value="edit_group_sticker_img">
                                                                            <input type="hidden" class="form-control" name="send_url" value="#">
                                                                            <button type="button" class="btn btn-default btn-xs"
                                                                            onclick="edit_group_sticker_form(this);void(0);"
                                                                            >確認裁切</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="headingSix">
                                                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                                        <h4 class="panel-title">
                                                            選擇小組頁面樣式
                                                        </h4>
                                                    </a>
                                                </div>
                                                <div id="collapseSix" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingSix">
                                                    <div class="panel-body">
                                                        <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                                                            <div class="thumbnail" style="border:0px;">
                                                                <img height="80" style="height:80px;" src="../img/bg.jpg" alt="預設樣式">
                                                                <div class="caption">預設</div>
                                                                <div class="caption">
                                                                    <button type="button" class="btn btn-default btn-xs"
                                                                    onclick="edit_style_group(1,1);void(0);"
                                                                    >預覽</button>
                                                                    <button type="button" class="btn btn-default btn-xs"
                                                                    onclick="edit_style_group(2,1);void(0);"
                                                                    >套用</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php for($i=2;$i<=6;$i++):?>
                                                            <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                                                                <div class="thumbnail" style="border:0px;">
                                                                    <img height="80" style="height:80px;" src="../img/default/style_group/bg_<?php echo $i;?>.jpg" alt="樣式<?php echo $i;?>">
                                                                    <div class="caption">樣式<?php echo $i-1;?></div>
                                                                    <div class="caption">
                                                                        <button type="button" class="btn btn-default btn-xs"
                                                                        onclick="edit_style_group(1,<?php echo $i;?>);void(0);"
                                                                        >預覽</button>
                                                                        <button type="button" class="btn btn-default btn-xs"
                                                                        onclick="edit_style_group(2,<?php echo $i;?>);void(0);"
                                                                        >套用</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endfor;?>
                                                        <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 hidden-xs">
                                                            <div class="thumbnail" style="border:0px;">
                                                                <img height="80" style="height:80px;" src="../img/default/style_group/bg_upload.jpg" alt="自行上傳">
                                                                <div class="caption style_group_file_name"></div>
                                                                <div class="caption">
                                                                    <form action="../controller/img.php" method="post" class="ajax_style_group_form">
                                                                        <span class="btn btn-default btn-xs btn_file">
                                                                            選擇<input type="file" name="style_group_file" class="style_group_file">
                                                                        </span>
                                                                        <button type="button" class="btn btn-default btn-xs"
                                                                        onclick="ajax_style_group_upload(this);void(0);"
                                                                        >上傳</button>
                                                                        <input type="hidden" class="form-control" name="group_id" value="<?php echo (int)$get_group_id;?>">
                                                                        <input type="hidden" class="form-control" name="method" value="add_style_group_img">
                                                                        <input type="hidden" class="form-control" name="send_url" value="#">
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel panel-default hidden-xs">
                                                <div class="panel-heading" role="tab" id="headingSeven">
                                                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                                        <h4 class="panel-title">
                                                            上傳小組背景照片
                                                        </h4>
                                                    </a>
                                                </div>
                                                <div id="collapseSeven" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingSeven">
                                                    <div class="panel-body">
                                                        <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 hidden-xs">
                                                            <div class="thumbnail" style="border:0px;">
                                                                <img height="80" style="height:80px;" src="../img/default/background_group/bg_upload.jpg" alt="自行上傳">
                                                                <div class="caption background_group_file_name"></div>
                                                                <div class="caption">
                                                                    <form action="../controller/img.php" method="post" class="ajax_background_group_form">
                                                                        <span class="btn btn-default btn-xs btn_file">
                                                                            選擇<input type="file" name="background_group_file" class="background_group_file">
                                                                        </span>
                                                                        <button type="button" class="btn btn-default btn-xs"
                                                                        onclick="ajax_background_group_upload(this);void(0);"
                                                                        >上傳</button>
                                                                        <input type="hidden" class="form-control" name="group_id" value="<?php echo (int)$get_group_id;?>">
                                                                        <input type="hidden" class="form-control" name="method" value="add_background_group_img">
                                                                        <input type="hidden" class="form-control" name="send_url" value="#">
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr></tbody>
                            </table>
                        </div>

                        <!-- 簡介 -->
                        <div role="tabpanel" class="tab-pane fade" id="info" aria-labelledBy="profile-tab">
                            <div class="group_lefe_side_tab4 row">
                                <div class="modal_jumbotron_note">
                                    <?php echo htmlspecialchars($group_content);?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- group_lefe_side,end -->

                <!-- right_side,start -->
                <div class="right_side col-xs-12 col-sm-2 col-md-2 col-lg-2"></div>
                <!-- right_side,end -->

            <?php endif;?>

        </div>
        <!-- 內容,end -->

    </div>
    <!-- 頁面,容器,end -->

    <!-- 註腳列,容器,start -->
    <?php echo $footbar;?>
    <!-- 註腳列,容器,end -->

    <!-- 頁面至頂,start -->
    <div class="scroll_to_top hidden-xs"></div>
    <!-- 頁面至頂,end -->

    <!-- modal_bookstore_rec,start -->
    <?php echo $modal_bookstore_rec;?>
    <!-- modal_bookstore_rec,end -->

</body>

<!-- 通用 -->
<script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/func/block_ui/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/ui/code.js"></script>
<script type="text/javascript" src="../../../lib/js/string/code.js"></script>
<script type="text/javascript" src="../../../lib/js/array/code.js"></script>
<script type="text/javascript" src="../../../lib/js/fso/code.js"></script>
<script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/3.51/jquery.form.min.js"></script>

<!-- 專屬 -->
<script type="text/javascript" src="../inc/code.js"></script>

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl                  ='\r\n';
    var get_from            =parseInt(<?php echo (int)$get_from;?>);
    var get_group_id        =parseInt(<?php echo (int)$get_group_id;?>);
    var send_url            =document.URL;
    var article_cno         =parseInt(<?php echo count($article_reply_results);?>);
    var sess_user_id        =parseInt(<?php echo $sess_user_id;?>);
    var style_id            =parseInt(<?php echo $style_id;?>);
    var style_from          =parseInt(<?php echo $style_from;?>);
    var group_img_size      ={};
    var arry_booklist_info  ={};
    var arry_booklist_name  =[];
    var arry_group_user_info={};
    var arry_group_user_name=[];

    group_img_size[0]=0;
    group_img_size[1]=0;
    <?php if(isset($group_img_size)):?>
        group_img_size[0]=parseInt(<?php echo $group_img_size[0];?>);
        group_img_size[1]=parseInt(<?php echo $group_img_size[1];?>);
    <?php endif;?>

    <?php
    if(!empty($arry_my_borrow)){
    foreach($arry_my_borrow as $rs_book_sid=>$rs_book_name):
        $rs_book_sid =trim($rs_book_sid);
        $rs_book_name=trim($rs_book_name);
    ?>
        arry_booklist_info['<?php echo addslashes($rs_book_name);?>']='<?php echo $rs_book_sid;?>';
        arry_booklist_name.push('<?php echo addslashes($rs_book_name);?>');
    <?php endforeach;}?>

    <?php
    if(!empty($arry_forum_friend)){
    foreach($arry_forum_friend as $friend_id=>$friend_name):
        $friend_id=(int)($friend_id);
        $friend_name=trim($friend_name);
    ?>
        arry_group_user_info['<?php echo $friend_name;?>']='<?php echo $friend_id;?>';
        arry_group_user_name.push('<?php echo $friend_name;?>');
    <?php endforeach;}?>


    //OBJ
    var article_eagle_content=<?php echo json_encode($article_eagle_content,true);?>;
    var article_eagle_code   =<?php echo json_encode($article_eagle_code,true);?>;
    var arry_my_borrow       =<?php echo json_encode($arry_group_booklist,true);?>;
    var json_sess_user_book_results ='<?php echo json_encode($sess_user_book_results,true);?>';
    var json_group_book_results='<?php echo json_encode($group_book_results,true);?>';


    //FUNCTION
    function show_bookstore_rec(no){
    //引用書店推薦

        var no=parseInt(no);

        if(no===0){
            $('#modal_bookstore_rec').modal();
            show_bookstore_rec(1);
        }else{
            $.ajax({
            //參數設置
                async      :true,
                cache      :false,
                global     :true,
                timeout    :50000,
                contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                url        :"../controller/load.php",
                type       :"POST",
                data       :{
                    no      :encodeURI(trim(no                  )),
                    sess_user_book_results:(trim(json_sess_user_book_results)),
                    method  :encodeURI(trim('load_bookstore_rec')),
                    send_url:encodeURI(trim(send_url            ))
                },
            //事件
                beforeSend  :function(){
                //傳送前處理
                    $.blockUI({
                        message:'<h3>資料讀取中...</h3>',
                        baseZ: 2000,
                        css:{
                            border: 'none',
                            padding: '15px',
                            backgroundColor: '#000',
                            '-webkit-border-radius': '10px',
                            '-moz-border-radius': '10px',
                            opacity: .6,
                            color: '#fff'
                        }
                    });
                },
                success     :function(respones){
                //成功處理
                    var respones=jQuery.parseJSON(respones);
                    if(no===1){
                        if(parseInt(respones.length)>0){
                            $(".show_bookstore_rec_content").empty();
                            for(key in respones){
                                var _html="";
                                var file_path=trim(respones[key]);
                                var arry_book_sid=[];
                                for(key1 in jQuery.parseJSON(json_group_book_results)){
                                    var book_sid=jQuery.parseJSON(json_group_book_results)[key1]['book_sid'];
                                    arry_book_sid.push(book_sid);
                                }
                                var has_append=false;
                                for(key2 in file_path.split("/")){
                                    var val=$.trim(file_path.split("/")[key2]);
                                    if(in_array(val,arry_book_sid)){
                                        _html+="<div class='col-xs-12 col-sm-4 col-md-3 col-lg-3' style='margin:5px 0;'>";
                                            _html+="<div class='thumbnail text-center' style='background-color:#ffeaea;'>";
                                                _html+="<img src='"+file_path+"' width='120' height='120' class='img-responsive' border='0' alt='bookstore_rec' style='width:120px;height:120px;'>";
                                                _html+="<div class='caption'>";
                                                    _html+="<p>";
                                                        _html+="<button file_path='"+file_path+"' type='button' class='btn btn-primary btn-xs' onclick='show_bookstore_rec_draw(this);' style='margin:0 2px;margin-top:2px;'>觀看</button>";
                                                        _html+="<button file_path='"+file_path+"' type='button' class='btn btn-primary btn-xs' onclick='use_bookstore_rec(this,"+no+");' style='margin:0 2px;margin-top:2px;'>使用</button>";
                                                    _html+="</p>";
                                                _html+="</div>";
                                            _html+="</div>";
                                        _html+="</div>";
                                        $(".show_bookstore_rec_content").prepend(_html);
                                        has_append=true;
                                        break;
                                    }
                                }
                                if(!has_append){
                                    _html+="<div class='col-xs-12 col-sm-4 col-md-3 col-lg-3' style='margin:5px 0;'>";
                                        _html+="<div class='thumbnail text-center'>";
                                            _html+="<img src='"+file_path+"' width='120' height='120' class='img-responsive' border='0' alt='bookstore_rec' style='width:120px;height:120px;'>";
                                            _html+="<div class='caption'>";
                                                _html+="<p>";
                                                    _html+="<button file_path='"+file_path+"' type='button' class='btn btn-primary btn-xs' onclick='show_bookstore_rec_draw(this);' style='margin:0 2px;margin-top:2px;'>觀看</button>";
                                                    _html+="<button file_path='"+file_path+"' type='button' class='btn btn-primary btn-xs' onclick='use_bookstore_rec(this,"+no+");' style='margin:0 2px;margin-top:2px;'>使用</button>";
                                                _html+="</p>";
                                            _html+="</div>";
                                        _html+="</div>";
                                    _html+="</div>";
                                    $(".show_bookstore_rec_content").append(_html);
                                }
                            }
                        }
                    }else if(no===2){
                        if(parseInt(respones.length)>0){
                            $(".show_bookstore_rec_content").empty();
                            for(key in respones){
                                var _html="";
                                var content=trim(respones[key]);
                                _html+="<div class='col-xs-12 col-sm-4 col-md-3 col-lg-3' style='margin:5px 0;'>";
                                    _html+="<div class='thumbnail text-center' style='word-break:break-all;height:120px;'>";
                                        _html+="<p class='text-left' style='font-size:8pt;height:60px;'>"+content+"</p>";
                                        _html+="<div class='caption'>";
                                            _html+="<p>";
                                                _html+="<button content='"+content+"' type='button' class='btn btn-primary btn-xs' onclick='use_bookstore_rec(this,"+no+");' style='margin:0 2px;margin-top:2px;'>使用</button>";
                                            _html+="</p>";
                                        _html+="</div>";
                                    _html+="</div>";
                                _html+="</div>";
                                $(".show_bookstore_rec_content").append(_html);
                            }
                        }
                    }else if(no===3){
                        if(parseInt(respones.length)>0){
                            $(".show_bookstore_rec_content").empty();
                            for(key in respones){
                                var _html="";
                                var file_path=trim(respones[key]);
                                _html+="<div class='col-xs-12 col-sm-6 col-md-3 col-lg-3' style='margin:5px 0;'>";
                                    _html+="<div class='thumbnail text-center' style='word-break:break-all;height:80px;'>";
                                        _html+="<audio controls style='position:relative;top:10px;width:140px;'>";
                                            _html+="<source src='"+file_path+"' type='audio/mpeg'>";
                                        _html+="</audio>";
                                        _html+="<div class='caption text-center'>";
                                            _html+="<p>";
                                                _html+="<button file_path='"+file_path+"' type='button' class='btn btn-primary btn-xs' onclick='use_bookstore_rec(this,"+no+");' style='margin:0 2px;margin-top:2px;'>使用</button>";
                                            _html+="</p>";
                                        _html+="</div>";
                                    _html+="</div>";
                                _html+="</div>";
                                $(".show_bookstore_rec_content").append(_html);
                            }
                        }
                    }else{return false;}
                },
                error       :function(xhr, ajaxoptions, thrownerror){
                //失敗處理
                    return false;
                },
                complete    :function(){
                //傳送後處理
                    $.unblockUI();
                }
            });
        }
    }

    function use_bookstore_rec(obj,no){
        var no              =parseInt(no);
        var oarticle_content=document.getElementById('article_content[]');

        if(no===1){
            var file_path=trim($(obj).attr('file_path'));
            var file_tag=nl+'[img src="'+trim(file_path)+'" img]'+nl;
            alert('圖片檔即將貼在文章內容，請勿更改格式！');
            $(oarticle_content).val($.trim($(oarticle_content).val())+file_tag);
        }else if(no===2){
            var content=nl+trim($(obj).attr('content'))+nl;
            alert('文字即將貼在文章內容！');
            $(oarticle_content).val($.trim($(oarticle_content).val())+content);
        }else if(no===3){
            var file_path=trim($(obj).attr('file_path'));
            var file_tag=nl+'[audio src="'+trim(file_path)+'" audio]'+nl;
            alert('錄音檔即將貼在文章內容，請勿更改格式！');
            $(oarticle_content).val($.trim($(oarticle_content).val())+file_tag);
        }else{return false;}

        $('#modal_bookstore_rec').modal('hide');
        oarticle_content.focus();
    }

    function show_bookstore_rec_draw(obj){
        var file_path=$(obj).attr('file_path');
        window.open(file_path);
    }

    $(".group_booklist_book_name").autocomplete({
        source: arry_booklist_name
    });
    $(".group_user_name").autocomplete({
        source: arry_group_user_name
    });

    $('#btn_save_group_info').click(function(){
    //修改小組資訊

        var oForm2          =document.getElementById('Form2');
        var ogroup_name     =document.getElementById('group_name');
        var ogroup_content  =document.getElementById('group_content');
        var oggroup_rule    =document.getElementById('group_rule');
        var arry_err        =[];

        if(trim(ogroup_name.value)===''){
            arry_err.push('請輸入小組名稱');
        }else{
            if(trim(ogroup_name.value).length>50){
                arry_err.push('小組名稱限制50字');
            }
        }
        if(trim(ogroup_content.value)===''){
            arry_err.push('請輸入小組介紹');
        }else{
            if(trim(ogroup_content.value).length>300){
                arry_err.push('小組介紹限制300字');
            }
        }
        if(trim(oggroup_rule.value)===''){
            arry_err.push('請輸入小組規範');
        }else{
            if(trim(oggroup_rule.value).length>300){
                arry_err.push('小組規範限制300字');
            }
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要儲存嗎 ?')){
                oForm2.action='../controller/edit.php'
                oForm2.submit();
                return true;
            }else{
                return false;
            }
        }
    });

    $('.btn_blacklist_group_school').click(function(){
    //黑名單小組

        var user_id =parseInt($(this).attr('user_id'));
        var group_id=parseInt(($(this).attr('group_id')));

        if(!confirm('你確定要將小組加入學校的黑名單嗎?')){
            return false;
        }

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/add.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                user_id     :encodeURI(trim(user_id                     )),
                group_id    :encodeURI(trim(group_id                    )),
                method      :encodeURI(trim('add_blacklist_group_school')),
                send_url    :encodeURI(trim(send_url                    ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                //alert(respones);
                location.href="user.php?user_id="+sess_user_id+"&tab=4";
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                if(ajaxoptions==='timeout'){
                    return false;
                }else{
                    return false;
                }
            },
            complete    :function(){
            //傳送後處理
            }
        });
    });

    $('.btn_report_group').click(function(){
    //檢舉小組

        var user_id =parseInt($(this).attr('user_id'));
        var group_id=parseInt(($(this).attr('group_id')));

        if(!confirm('你確定要檢舉小組嗎?')){
            return false;
        }

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/del.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                user_id     :encodeURI(trim(user_id         )),
                group_id    :encodeURI(trim(group_id        )),
                method      :encodeURI(trim('report_group'  )),
                send_url    :encodeURI(trim(send_url        ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                location.reload();
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                if(ajaxoptions==='timeout'){
                    return false;
                }else{
                    return false;
                }
            },
            complete    :function(){
            //傳送後處理
            }
        });
    });

    $('.btn_create_group').click(function(){
    //聯署建立小組

        var user_id =parseInt($(this).attr('user_id'));
        var group_id=parseInt(($(this).attr('group_id')));

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/add.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                user_id     :encodeURI(trim(user_id                     )),
                group_id    :encodeURI(trim(group_id                    )),
                method      :encodeURI(trim('add_request_create_group'  )),
                send_url    :encodeURI(trim(send_url                    ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                location.reload();
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                if(ajaxoptions==='timeout'){
                    return false;
                }else{
                    return false;
                }
            },
            complete    :function(){
            //傳送後處理
            }
        });
    });

    $('.btn_del_group').click(function(){
    //解散小組

        if(!confirm('你確定要解散小組嗎?小組內的成員、討論及書單都會消失不見喔!')){
            return false;
        }

        var group_id=parseInt(($(this).attr('group_id')));

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/del.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                group_id    :encodeURI(trim(group_id    )),
                method      :encodeURI(trim('del_group' )),
                send_url    :encodeURI(trim(send_url    ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                location.href="user.php?user_id="+sess_user_id+"&tab=4";
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    });

    $('.btn_join_group').click(function(){
    //申請加入小組

        var user_id =parseInt($(this).attr('user_id'));
        var group_id=parseInt(($(this).attr('group_id')));

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/add.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                user_id     :encodeURI(trim(user_id                     )),
                group_id    :encodeURI(trim(group_id                    )),
                method      :encodeURI(trim('add_request_join_to_group' )),
                send_url    :encodeURI(trim(send_url                    ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                location.reload();
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                if(ajaxoptions==='timeout'){
                    return false;
                }else{
                    return false;
                }
            },
            complete    :function(){
            //傳送後處理
            }
        });
    });

    function auto_group_user(obj){
    //選擇書友邀請

        try{
            var user_name=trim($(obj).text());
            var user_id  =trim(arry_group_user_info[user_name]);
            $('.group_user_name').val(user_name);
            $('.group_user_id').val(user_id);
        }catch(e){}
    }

    function add_request_join_us_group(obj){
    //邀請書友加入小組

        var $group_user_name=trim($('.group_user_name').val());
        var $group_user_id  =parseInt(trim($('.group_user_id').val()));
        var arry_err=[];

        if(trim($group_user_name)===''){
            arry_err.push('請選擇或輸入一位書友來邀請加入小組');
        }else{
            if(in_array($group_user_name,arry_group_user_name)){
                $group_user_id=parseInt(trim(arry_group_user_info[trim($group_user_name)]));
            }else{
                arry_err.push('請選擇或輸入一位書友來邀請加入小組');
            }
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(!confirm('你確定要邀請嗎?')){
                return false;
            }else{
                $.ajax({
                //參數設置
                    async      :true,
                    cache      :false,
                    global     :true,
                    timeout    :50000,
                    contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                    url        :"../controller/add.php",
                    type       :"POST",
                    datatype   :"json",
                    data       :{
                        group_id    :encodeURI(trim(get_group_id                )),
                        friend_id   :encodeURI(trim($group_user_id              )),
                        method      :encodeURI(trim('add_request_join_us_group' )),
                        send_url    :encodeURI(trim(send_url                    ))
                    },

                //事件
                    beforeSend  :function(){
                    //傳送前處理
                    },
                    success     :function(respones){
                    //成功處理
                        alert(respones);
                        return true;
                    },
                    error       :function(xhr, ajaxoptions, thrownerror){
                    //失敗處理
                    },
                    complete    :function(){
                    //傳送後處理
                    }
                });
            }
        }
    }

    $('.btn_add_track_group').click(function(){
    //追蹤小組

        var user_id =parseInt($(this).attr('user_id'));
        var group_id=parseInt(($(this).attr('group_id')));

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/add.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                user_id     :encodeURI(trim(user_id             )),
                group_id    :encodeURI(trim(group_id            )),
                method      :encodeURI(trim('add_track_group'   )),
                send_url    :encodeURI(trim(send_url            ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                location.reload();
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                if(ajaxoptions==='timeout'){
                    return false;
                }else{
                    return false;
                }
            },
            complete    :function(){
            //傳送後處理
            }
        });
    });

    function del_best_article_category(obj,group_id,cat_id){
    //移除精華區類別

        var group_id=parseInt(group_id);
        var cat_id  =parseInt(cat_id);

        if(!confirm('你確定要移出嗎?')){
            return false;
        }

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/del.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                group_id    :encodeURI(trim(group_id                   )),
                cat_id      :encodeURI(trim(cat_id                     )),
                method      :encodeURI(trim('del_best_article_category')),
                send_url    :encodeURI(trim(send_url                   ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                $(obj).parent().parent().parent().remove();
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function add_best_article_category(obj){
    //建立精華區類別

        var $cat_name=trim($('.cat_name').val());
        var arry_err =[];

        if(trim($cat_name)===''){
            arry_err.push('請輸入類別名稱，來建立新的精華區類別');
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(!confirm('你確定要建立嗎?')){
                return false;
            }else{
                $.ajax({
                //參數設置
                    async      :true,
                    cache      :false,
                    global     :true,
                    timeout    :50000,
                    contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                    url        :"../controller/add.php",
                    type       :"POST",
                    datatype   :"json",
                    data       :{
                        cat_name    :(trim($cat_name                            )),
                        group_id    :encodeURI(trim(get_group_id                )),
                        method      :encodeURI(trim('add_best_article_category' )),
                        send_url    :encodeURI(trim(send_url                    ))
                    },

                //事件
                    beforeSend  :function(){
                    //傳送前處理
                    },
                    success     :function(respones){
                    //成功處理
                        var respones=jQuery.parseJSON(respones);
                        alert(respones.msg);
                        try{
                            if(respones.cat_id!==undefined){
                                var html ='';
                                html+='<tr align="center">';
                                html+=  '<td style="border:0px;" align="left">'+$cat_name+'</td>';
                                html+=      '<td style="border:0px;" align="center">';
                                html+=          '<div class="form-group" style="position:relative;">';
                                html+=              '<button type="button" class="btn btn-default"';
                                html+=              'onclick="del_best_article_category(this,'+get_group_id+','+parseInt(respones.cat_id)+');void(0);"';
                                html+=              '>移除</button>';
                                html+=          '</div>';
                                html+=      '</td>';
                                html+='</tr>';
                                $('.table_best_article_category').append(html);
                            }
                        }catch(e){}
                        return true;
                    },
                    error       :function(xhr, ajaxoptions, thrownerror){
                    //失敗處理
                    },
                    complete    :function(){
                    //傳送後處理
                    }
                });
            }
        }
    }

    function add_group_booklist(obj){
    //建立小組書單

        var $group_booklist_book_name=trim($('.group_booklist_book_name').val());
        var $group_booklist_book_sid =trim($('.group_booklist_book_sid').val());
        var arry_err=[];

        if(trim($group_booklist_book_name)===''){
            arry_err.push('請選擇或輸入一本讀過的書籍來建立小組書單');
        }else{
            if(in_array($group_booklist_book_name,arry_booklist_name)){
                $group_booklist_book_sid=trim(arry_booklist_info[trim($group_booklist_book_name)]);
            }else{
                arry_err.push('請選擇或輸入一本讀過的書籍來建立小組書單');
            }
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(!confirm('你確定要建立嗎?')){
                return false;
            }else{
                $.ajax({
                //參數設置
                    async      :true,
                    cache      :false,
                    global     :true,
                    timeout    :50000,
                    contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                    url        :"../controller/add.php",
                    type       :"POST",
                    datatype   :"json",
                    data       :{
                        group_id    :encodeURI(trim(get_group_id            )),
                        book_sid    :encodeURI(trim($group_booklist_book_sid)),
                        method      :encodeURI(trim('add_group_booklist'    )),
                        send_url    :encodeURI(trim(send_url                ))
                    },

                //事件
                    beforeSend  :function(){
                    //傳送前處理
                    },
                    success     :function(respones){
                    //成功處理
                        alert(respones);
                        location.reload();
                        return true;
                    },
                    error       :function(xhr, ajaxoptions, thrownerror){
                    //失敗處理
                    },
                    complete    :function(){
                    //傳送後處理
                    }
                });
            }
        }
    }

    function del_group_booklist(obj,group_id,book_sid){
    //移出小組書單

        var obj     =obj;
        var group_id=parseInt(group_id);
        var book_sid=trim(book_sid);

        if(!confirm('你確定要移出嗎?')){
            return false;
        }

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/del.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                group_id    :encodeURI(trim(group_id            )),
                book_sid    :encodeURI(trim(book_sid            )),
                method      :encodeURI(trim('del_group_booklist')),
                send_url    :encodeURI(trim(send_url            ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                $(obj).parent().parent().parent().remove();
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function edit_group_user_state(obj,user_id){
    //切換小組使用者狀態

        var user_state=parseInt(obj.value);
        var user_id   =parseInt(user_id);

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/edit.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                group_id    :encodeURI(trim(get_group_id            )),
                user_state  :encodeURI(trim(user_state              )),
                user_id     :encodeURI(trim(user_id                 )),
                method      :encodeURI(trim('edit_group_user_state' )),
                send_url    :encodeURI(trim(send_url                ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function edit_group_user_type(obj,user_id){
    //切換小組使用者身分

        var user_type=parseInt(obj.value);
        var user_id  =parseInt(user_id);

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/edit.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                group_id    :encodeURI(trim(get_group_id          )),
                user_type   :encodeURI(trim(user_type             )),
                user_id     :encodeURI(trim(user_id               )),
                method      :encodeURI(trim('edit_group_user_type')),
                send_url    :encodeURI(trim(send_url              ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function Btn_add_article(){
    //發文

        var oForm1              =$('#add_article').find('#Form1')[0];
        var osend_chk           =$('#add_article').find('#send_chk')[0];
        var obook_sid           =$('#add_article').find('#book_sid')[0];
        var oarticle_title      =$('#add_article').find('#article_title')[0];
        var oeagle_code         =document.getElementById('eagle_code');
        var oarticle_contents   =document.getElementsByName('article_content[]');
        var article_content_err =0;
        var arry_err            =[];

        if(trim(obook_sid.value)===''){
            arry_err.push('請選擇一本書來發文');
        }
        if(trim(oarticle_title.value)===''){
            arry_err.push('請輸入文章標題');
        }
        if(oarticle_contents!==undefined && oarticle_contents.length!==0){
            for(var i=0;i<oarticle_contents.length;i++){
                oarticle_content=oarticle_contents[i];
                var placeholder=trim(oarticle_content.getAttribute('placeholder'));
                if(trim(oarticle_content.value)==='' || trim(oarticle_content.value)===trim(placeholder)){
                    //arry_err.push('請輸入文章內容 '+(i+1));
                    article_content_err++;
                }
                if(trim(oarticle_content.value).match(/…/gi)){
                    arry_err.push('請填補文章內容');
                }
            }
        }else{
            arry_err.push('文章內容框錯誤');
        }
        if(parseInt(oarticle_contents.length)===parseInt(article_content_err)){
            arry_err.push('請輸入文章內容');
        }
        if(!osend_chk.checked){
            arry_err.push('請閱讀並勾選同意討論區規則');
        }
        if(trim(oeagle_code.value)===''){
            oeagle_code.value=0;
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            <!-- if(confirm('發表文章將會消耗 30 點發文點數，你確定要送出嗎 ?')){ -->
            if(confirm('你確定要送出嗎 ?')){
                $.blockUI({
                    message:'<h3>發送文章中...</h3>',
                    baseZ: 2000,
                    css:{
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .6,
                        color: '#fff'
                    }
                });
                oForm1.action='../controller/add.php'
                oForm1.submit();
                return true;
            }else{
                return false;
            }
        }
    }

    function load_cat_id(){
        rs_cat_id = 0;
    }

    $('#myTabDrop1-contents li').click(function() {
        rs_cat_id = $(this).val();
        return rs_cat_id;
    });

    var flag = false;

    function load_article(){
    //讀取文章

        if(!flag){

            var page_article_cno=parseInt(parseInt($('.table_article tr').length)-1);
            var book_isbn_10    =trim('');
            var book_isbn_13    =trim('');
            var get_book_sid    =trim('');
            var get_group_id    =parseInt(<?php echo (int)$get_group_id;?>);

            $.ajax({
            //參數設置
                async      :true,
                cache      :false,
                global     :true,
                timeout    :50000,
                contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                url        :"../controller/load.php",
                type       :"POST",
                datatype   :"json",
                data       :{
                    page_article_cno:encodeURI(trim(page_article_cno)),
                    book_isbn_10    :encodeURI(trim(book_isbn_10    )),
                    book_isbn_13    :encodeURI(trim(book_isbn_13    )),
                    get_book_sid    :encodeURI(trim(get_book_sid    )),
                    get_group_id    :encodeURI(trim(get_group_id    )),
                    get_from        :encodeURI(trim(get_from        )),
                    rs_cat_id :encodeURI(trim(rs_cat_id)),
                    method          :encodeURI(trim('load_article'  ))
                },

            //事件
                beforeSend  :function(){
                //傳送前處理
                },
                success     :function(respones){
                //成功處理
                    var respones=jQuery.parseJSON(respones);
                    if(parseInt(respones.length)!==0){
                        for(key in respones){
                            var json_html=respones[key];
                            //附加
                            $('.table_article').append(json_html);
                        }
                    }
                },
                error       :function(xhr, ajaxoptions, thrownerror){
                //失敗處理
                    if(ajaxoptions==='timeout'){
                        return false;
                    }else{
                        return false;
                    }
                },
                complete    :function(){
                //傳送後處理
                }
            });

            flag = true;

            setTimeout(function(){flag = false;}, 3000);
        }
    }

    function load_best_article(){
    //讀取精華區文章

        if(!flag){

            var page_article_cno=parseInt(parseInt($('.beat_article_'+rs_cat_id+' tr').length)-1);
            var book_isbn_10    =trim('');
            var book_isbn_13    =trim('');
            var get_book_sid    =trim('');
            var get_group_id    =parseInt(<?php echo (int)$get_group_id;?>);

            $.ajax({
            //參數設置
                async      :true,
                cache      :false,
                global     :true,
                timeout    :50000,
                contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                url        :"../controller/load.php",
                type       :"POST",
                datatype   :"json",
                data       :{
                    page_article_cno:encodeURI(trim(page_article_cno)),
                    book_isbn_10    :encodeURI(trim(book_isbn_10    )),
                    book_isbn_13    :encodeURI(trim(book_isbn_13    )),
                    get_book_sid    :encodeURI(trim(get_book_sid    )),
                    get_group_id    :encodeURI(trim(get_group_id    )),
                    get_from        :encodeURI(trim(get_from        )),
                    rs_cat_id :encodeURI(trim(rs_cat_id)),
                    method          :encodeURI(trim('load_article'  ))
                },

            //事件
                beforeSend  :function(){
                //傳送前處理
                },
                success     :function(respones){
                //成功處理
                    var respones=jQuery.parseJSON(respones);
                    if(parseInt(respones.length)!==0){
                        for(key in respones){
                            var json_html=respones[key];
                            //附加
                            $('.beat_article_'+rs_cat_id).append(json_html);
                        }
                    }
                },
                error       :function(xhr, ajaxoptions, thrownerror){
                //失敗處理
                    if(ajaxoptions==='timeout'){
                        return false;
                    }else{
                        return false;
                    }
                },
                complete    :function(){
                //傳送後處理
                }
            });

            flag = true;

            setTimeout(function(){flag = false;}, 100);
        }

    }

    function auto_group_booklist(obj){
    //選擇書籍清單

        try{
            var book_name=trim($(obj).text());
            var book_sid =trim(arry_booklist_info[book_name]);
            $('.group_booklist_book_name').val(book_name);
            $('.group_booklist_book_sid').val(book_sid);
        }catch(e){}
    }

    function load_right_side(fun){
    //讀取側邊欄

        var fun     =trim(fun);
        get_group_id=parseInt(get_group_id);

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/load.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                sess_user_id:encodeURI(trim(sess_user_id        )),
                group_id    :encodeURI(trim(get_group_id        )),
                fun         :encodeURI(trim(fun                 )),
                method      :encodeURI(trim('load_right_side'   ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
                $.blockUI({
                    message:'<h3>網頁讀取中...</h3>',
                    css:{
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .6,
                        color: '#fff'
                    }
                });
            },
            success     :function(respones){
            //成功處理
                var respones=jQuery.parseJSON(respones);
                if($.trim(respones)!==''){
                    $('.right_side').append(respones);
                }
                $('[data-toggle="tooltip"]').tooltip(); //顯示完整書名提示
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                $.unblockUI();
                return false;
            },
            complete    :function(){
            //傳送後處理
                $.unblockUI();
            }
        });
    }

    function edit_style_group(type,style_id){
    //更換小組頁面樣式

        var type    =parseInt(type);
        var style_id=parseInt(style_id);
        var group_id=parseInt(<?php echo (int)$get_group_id;?>);

        if(type===1){
        //套用
            $('body').css("background-image","url(../img/default/style_group/bg_"+style_id+".jpg)");
        }else{
        //使用
            $.ajax({
            //參數設置
                async      :true,
                cache      :false,
                global     :true,
                timeout    :50000,
                contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                url        :"../controller/edit.php",
                type       :"POST",
                datatype   :"json",
                data       :{
                    style_id    :encodeURI(trim(style_id          )),
                    group_id    :encodeURI(trim(group_id          )),
                    method      :encodeURI(trim('edit_style_group')),
                    send_url    :encodeURI(trim(send_url          ))
                },

            //事件
                beforeSend  :function(){
                //傳送前處理
                },
                success     :function(respones){
                //成功處理
                    location.href='article.php?get_from=2&group_id='+group_id;
                    return true;
                },
                error       :function(xhr, ajaxoptions, thrownerror){
                //失敗處理
                    return false;
                },
                complete    :function(){
                //傳送後處理
                }
            });
        }
    }

    function ajax_background_group_upload(obj){
    //ajax 小組背景照片上傳

        var obj=obj;
        var arry_type=[
            'jpg',
            'jpeg'
        ]
        var file_val=trim($('.background_group_file').val());
        var info=pathinfo(file_val);
        var filename =info['filename'];
        var extension=info['extension'];

        if(file_val===''){
            alert('請選擇上傳的檔案!');
            return false;
        }
        if(!in_array(extension.toLowerCase(),arry_type,false)){
            alert('請選擇jpg檔案!');
            return false;
        }

        //顯示檔名
        $('.background_group_file_name').text(filename+'.'+extension);

        //上傳進度
        $(obj)[0].disabled=true;

        $('.ajax_background_group_form').ajaxSubmit({
            beforeSubmit: function(){
            },
            success: function(respone,st,xhr,$form){
                var group_id=parseInt(<?php echo (int)$get_group_id;?>);
                alert(respone);
                $(obj)[0].disabled=false;
                location.href='article.php?get_from=2&group_id='+group_id;
                return true;
            },
            error: function(){
                alert('上傳失敗');
                $(obj)[0].disabled=false;
                return false;
            }
        });
    }

    function ajax_style_group_upload(obj){
    //ajax 小組頁面上傳

        var obj=obj;
        var arry_type=[
            'jpg',
            'jpeg'
        ]
        var file_val=trim($('.style_group_file').val());
        var info=pathinfo(file_val);
        var filename =info['filename'];
        var extension=info['extension'];

        if(file_val===''){
            alert('請選擇上傳的檔案!');
            return false;
        }
        if(!in_array(extension.toLowerCase(),arry_type,false)){
            alert('請選擇jpg檔案!');
            return false;
        }

        //顯示檔名
        $('.style_group_file_name').text(filename+'.'+extension);

        //上傳進度
        $(obj)[0].disabled=true;

        $('.ajax_style_group_form').ajaxSubmit({
            beforeSubmit: function(){
            },
            success: function(respone,st,xhr,$form){
                var group_id=parseInt(<?php echo (int)$get_group_id;?>);
                alert(respone);
                $(obj)[0].disabled=false;
                location.href='article.php?get_from=2&group_id='+group_id;
                return true;
            },
            error: function(){
                alert('上傳失敗');
                $(obj)[0].disabled=false;
                return false;
            }
        });
    }

    function ajax_group_sticker_upload(obj){
    //ajax 小組大頭貼上傳

        var obj=obj;
        var arry_type=[
            'jpg',
            'jpeg'
        ]
        var file_val=trim($('.group_sticker_file').val());
        var info=pathinfo(file_val);
        var filename =info['filename'];
        var extension=info['extension'];

        if(file_val===''){
            alert('請選擇上傳的檔案!');
            return false;
        }
        if(!in_array(extension.toLowerCase(),arry_type,false)){
            alert('請選擇jpg檔案!');
            return false;
        }

        //上傳進度
        $(obj)[0].disabled=true;

        $('.ajax_group_sticker_form').ajaxSubmit({
            beforeSubmit: function(){
            },
            success: function(respone,st,xhr,$form){
                var group_id=parseInt(<?php echo (int)$get_group_id;?>);
                alert(respone);
                $(obj)[0].disabled=false;
                location.href='article.php?get_from=2&group_id='+group_id;
                return true;
            },
            error: function(){
                alert('上傳失敗');
                $(obj)[0].disabled=false;
                return false;
            }
        });
    }

    function group_sticker_area(img, selection) {
    //小組大頭貼裁切

        var scaleX=160/selection.width;
        var scaleY=160/selection.height;

        $('#new_group_sticker').css({
            width:Math.round(scaleX*group_img_size[0]) + 'px',
            height:Math.round(scaleY*group_img_size[1]) + 'px',
            marginLeft:'-' + Math.round(scaleX * selection.x1) + 'px',
            marginTop:'-' + Math.round(scaleY * selection.y1) + 'px'
        });

        $('#group_sticker_x1').val(selection.x1);
        $('#group_sticker_y1').val(selection.y1);
        $('#group_sticker_x2').val(selection.x2);
        $('#group_sticker_y2').val(selection.y2);
        $('#group_sticker_w').val(selection.width);
        $('#group_sticker_h').val(selection.height);
    }

    function edit_group_sticker_form(obj){
    //裁切小組大頭貼

        var oForm           =$('.edit_group_sticker_form')[0];
        var group_sticker_x1=parseInt($('#group_sticker_x1').val());
        var group_sticker_y1=parseInt($('#group_sticker_y1').val());
        var group_sticker_x2=parseInt($('#group_sticker_x2').val());
        var group_sticker_y2=parseInt($('#group_sticker_y2').val());
        var group_sticker_w =parseInt($('#group_sticker_w').val());
        var group_sticker_h =parseInt($('#group_sticker_h').val());

		if(group_sticker_w==0||group_sticker_h==0){
			alert("請進行裁切");
			return false;
		}
        if(!confirm('你確定要進行裁切嗎?')){
            return false;
        }

        oForm.action='../controller/img.php'
        oForm.submit();
        return true;
    }


    //ONLOAD
    $(function(){
        //讀取側邊欄
        load_right_side(trim('group'));
        //滾動監聽
        $(window).scroll(function(){
        var scrollint = Math.ceil($(window).scrollTop());
            if(article_cno>0){
                //偵測行動裝置
                if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
                    if($(window).scrollTop()>=($(document).height()-$(window).height())%2){
                        //讀取文章
                        if(rs_cat_id==0){
                            load_article();
                        }else{
                            load_best_article();
                        }
                    }
                }else{
                    if(scrollint>$(document).height()-$(window).height()-200){
                        //讀取文章
                        if(rs_cat_id==0){
                            load_article();
                        }else{
                            load_best_article();
                        }
                    }
                }
            }
            //偵測行動裝置
            if(/Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){

            }else{
                if(parseInt($(window).scrollTop())>0){
                    $('.scroll_to_top').show();
                }else{
                    $('.scroll_to_top').hide();
                }
            }
        });
        //載入小組頁面樣式
        if(style_from===1){
            $('body').css("background-image","url(../img/default/style_group/bg_"+style_id+".jpg)");
        }else{
            <?php if(isset($file_server_enable)&&($file_server_enable)):?>
                $('body').css("background-image","url(http://<?php echo $arry_ftp1_info['host'];?>/mssr/info/forum/group/"+get_group_id+"/style_group/bg_"+style_id+".jpg)");
            <?php else:?>
                $('body').css("background-image","url(../../../info/forum/group/"+get_group_id+"/style_group/bg_"+style_id+".jpg)");
            <?php endif;?>
        }
        //小組大頭貼裁切
        try{
            $('#old_group_sticker').imgAreaSelect({aspectRatio:'1:1', onSelectChange:group_sticker_area});
            $('#old_group_sticker').imgAreaSelect({hide:true});
        }catch(e){}
        //發文輔助顯示
        if(/Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
            $('.add_article_help-hidden-xs').remove();
        }else{
            $('.add_article_help-visible-xs').remove();
        }
    })

    $(document).ready(function() {
        $(document).get(0).oncontextmenu = function() {
            return false;
        };
    });
    //將滑鼠右鍵事件取消
    document.oncontextmenu = function(){
        window.event.returnValue=false;
    }
    $(document).keydown(function(event) {
        if (event.ctrlKey==true && (event.which == '118' || event.which == '86')) {
            event.preventDefault();
         }
    });

</script>
<script type="text/javascript" src="../../../inc/external/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//呼叫使用者頁面紀錄
//-------------------------------------------------------
    user_page_log(rd=3);
</script>
</html>
<?php
//-------------------------------------------------------
//page_group 區塊 -- 結束
//-------------------------------------------------------
    $conn_user=NULL;
    $conn_mssr=NULL;
?>
<?php };?>
<?php
    $conn_user=NULL;
    $conn_mssr=NULL;
    //ftp_close($ftp_conn);
?>
