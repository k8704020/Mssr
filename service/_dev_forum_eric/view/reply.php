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
            APP_ROOT.'service/_dev_forum_eric/inc/code',

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

        $get_from       =(isset($_GET['get_from']))?(int)$_GET['get_from']:0;
        $get_article_id =(isset($_GET['article_id']))?(int)$_GET['article_id']:0;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

        $arry_err=array();

        if($get_from===0){
            $arry_err[]='組態,錯誤!';
        }
        if($get_article_id===0){
            $arry_err[]='文章主索引,錯誤!';
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


        //模態框
        if(isset($sess_user_id))$modal_dialog_2=modal_dialog($rd=1,$type=2);

        //註腳列
        $footbar=footbar($rd=1);

        //載入內容
        switch($get_from){
            case 1:
            //書籍內容
                //載入模態框
                $modal_dialog_1=modal_dialog($rd=1,$type=1);
                page_book($title);
            break;

            case 2:
            //小組內容
                page_group($title);
            break;

            default:
                die('組態,錯誤!');
            break;
        }
?>


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
        global $get_article_id;

        global $conn_mssr;
        global $arry_conn_mssr;

        //global $meta;
        global $navbar;
        global $carousel;
        global $modal_dialog_1;
        global $modal_dialog_2;
        global $footbar;

        global $_GET;
        global $_POST;

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

        $get_group_id=0;

        //-----------------------------------------------
        //FTP 登入
        //-----------------------------------------------

            if(isset($file_server_enable)&&($file_server_enable)){
                //FTP 路徑
                $ftp_root="public_html/mssr/info/user";

                //連接 | 登入 FTP
                $ftp_conn  =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                $ftp_login =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

                //設定被動模式
                ftp_pasv($ftp_conn,TRUE);
            }

        //-----------------------------------------------
        //文章資訊 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `user`.`member`.`name`,
                    `user`.`member`.`sex`,

                    `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,

                    `mssr_forum`.`mssr_forum_article`.`user_id`,
                    `mssr_forum`.`mssr_forum_article`.`article_id`,
                    `mssr_forum`.`mssr_forum_article`.`article_like_cno`,
                    `mssr_forum`.`mssr_forum_article`.`article_report_cno`,
                    `mssr_forum`.`mssr_forum_article`.`keyin_mdate`,

                    `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                    `mssr_forum`.`mssr_forum_article_detail`.`article_content`
                FROM `mssr_forum`.`mssr_forum_article_book_rev`
                    INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                    `mssr_forum`.`mssr_forum_article_book_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`

                    INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                    `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                    INNER JOIN `user`.`member` ON
                    `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_article`.`article_from` =1 -- 文章來源
                    AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                    AND `mssr_forum`.`mssr_forum_article`.`article_id`   ={$get_article_id}
            ";
            $article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(empty($article_results)){
                @header("Location:user.php?user_id={$sess_user_id}&tab=1");
                die();
            }else{
                $rs_article_book_sid    =trim($article_results[0]['book_sid']);
                $rs_article_title       =trim($article_results[0]['article_title']);
                $rs_article_content     =trim($article_results[0]['article_content']);
                $rs_article_user_name   =trim($article_results[0]['name']);
                $rs_article_keyin_mdate =trim($article_results[0]['keyin_mdate']);
                $rs_article_like_cno    =(int)($article_results[0]['article_like_cno']);
                $rs_article_id          =(int)($article_results[0]['article_id']);
                $rs_article_user_id     =(int)($article_results[0]['user_id']);
                $rs_article_user_sex    =(int)($article_results[0]['sex']);

                //特殊處理
                $rs_article_book_name='';
                $arry_book_infos=get_book_info($conn_mssr,$rs_article_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                if(!empty($arry_book_infos)){$rs_article_book_name=trim($arry_book_infos[0]['book_name']);}else{}
                $rs_article_book_img='../img/default/book.png';
                if(file_exists("../../../info/book/{$rs_article_book_sid}/img/front/simg/1.jpg")){
                    $rs_article_book_img="../../../info/book/{$rs_article_book_sid}/img/front/simg/1.jpg";
                }

                $get_book_sid=mysql_prep(trim($rs_article_book_sid));

                $rs_article_img='../img/default/user_boy.png';
                if($rs_article_user_sex===2)$rs_article_img='../img/default/user_girl.png';

                if(isset($file_server_enable)&&($file_server_enable)){
                    $rs_article_img_ftp_path="{$ftp_root}/{$rs_article_user_id}/forum/user_sticker";
                    $arry_rs_article_img_ftp_file=ftp_nlist($ftp_conn,$rs_article_img_ftp_path);
                    if(isset($arry_rs_article_img_ftp_file[0])){
                        $rs_article_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_article_user_id}/forum/user_sticker/1.jpg";
                    }
                }else{
                    if(file_exists("../../../info/user/{$rs_article_user_id}/forum/user_sticker/1.jpg")){
                        $rs_article_img="../../../info/user/{$rs_article_user_id}/forum/user_sticker/1.jpg";
                    }
                }

                $rs_article_content=htmlspecialchars($rs_article_content);
                preg_match_all('/src.*/',$rs_article_content,$arrys_preg_article_content);
                if(!empty($arrys_preg_article_content)&&isset($arrys_preg_article_content[0])&&!empty($arrys_preg_article_content[0])){
                    $arry_preg_article_content=$arrys_preg_article_content[0];
                    foreach($arry_preg_article_content as $preg_article_content){
                        $preg_article_content=trim($preg_article_content);
                        $preg_article_content=str_replace("src=","",$preg_article_content);
                        $preg_article_content=str_replace('&quot;',"",$preg_article_content);
                        $preg_article_content=str_replace('img]',"",$preg_article_content);
                        $preg_article_content=str_replace('audio]',"",$preg_article_content);
                        $file_path=trim($preg_article_content);

                        if(isset($file_server_enable)&&($file_server_enable)){
                            $tmp_ftp_root ="public_html/mssr/info/forum";
                            $tmp_ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                            $tmp_ftp_login=ftp_login($tmp_ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                            ftp_pasv($tmp_ftp_conn,TRUE);
                            $tmp_file_path=str_replace("http://{$arry_ftp1_info['host']}","public_html",$file_path);
                            $arry_ftp_file=ftp_nlist($tmp_ftp_conn,$tmp_file_path);
                            if(!empty($arry_ftp_file)){
                                $file_info=pathinfo($file_path);
                                $extension=(isset($file_info['extension']))?$file_info['extension']:'';
                                if($extension==='jpg'){
                                    $replace="<img src='{$file_path}' border='0' alt='rec_draw' style='max-width:90%;border:0px solid red;margin:0 auto;'>";
                                    $rs_article_content=str_replace("[img src=&quot;{$file_path}&quot; img]",$replace,$rs_article_content);
                                }elseif($extension==='mp3'){
                                    $replace="
                                        <audio controls>
                                            <source src='{$file_path}' type='audio/mpeg'>
                                        </audio>
                                    ";
                                    $rs_article_content=str_replace("[audio src=&quot;{$file_path}&quot; audio]",$replace,$rs_article_content);
                                }else{continue;}
                            }
                        }else{
                            if(file_exists($file_path)){
                                $file_info=pathinfo($file_path);
                                $extension=(isset($file_info['extension']))?$file_info['extension']:'';
                                if($extension==='jpg'){
                                    $replace="<img src='{$file_path}' border='0' alt='rec_draw' style='max-width:90%;border:0px solid red;margin:0 auto;'>";
                                    $rs_article_content=str_replace("[img src=&quot;{$file_path}&quot; img]",$replace,$rs_article_content);
                                }elseif($extension==='mp3'){
                                    $replace="
                                        <audio controls>
                                            <source src='{$file_path}' type='audio/mpeg'>
                                        </audio>
                                    ";
                                    $rs_article_content=str_replace("[audio src=&quot;{$file_path}&quot; audio]",$replace,$rs_article_content);
                                }else{continue;}
                            }
                        }
                    }
                }
            }

        //-----------------------------------------------
        //回覆資訊 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `user`.`member`.`name`,

                    `mssr_forum`.`mssr_forum_reply_book_rev`.`book_sid`,

                    `mssr_forum`.`mssr_forum_reply`.`user_id`,
                    `mssr_forum`.`mssr_forum_reply`.`article_id`,
                    `mssr_forum`.`mssr_forum_reply`.`reply_id`,
                    `mssr_forum`.`mssr_forum_reply`.`reply_like_cno`,
                    `mssr_forum`.`mssr_forum_reply`.`reply_report_cno`,
                    `mssr_forum`.`mssr_forum_reply`.`keyin_mdate`,

                    `mssr_forum`.`mssr_forum_reply_detail`.`reply_content`
                FROM `mssr_forum`.`mssr_forum_reply_book_rev`
                    INNER JOIN `mssr_forum`.`mssr_forum_reply` ON
                    `mssr_forum`.`mssr_forum_reply_book_rev`.`reply_id`=`mssr_forum`.`mssr_forum_reply`.`reply_id`

                    INNER JOIN `mssr_forum`.`mssr_forum_reply_detail` ON
                    `mssr_forum`.`mssr_forum_reply`.`reply_id`=`mssr_forum`.`mssr_forum_reply_detail`.`reply_id`

                    INNER JOIN `user`.`member` ON
                    `mssr_forum`.`mssr_forum_reply`.`user_id`=`user`.`member`.`uid`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_reply`.`reply_from` =1 -- 回文來源
                    AND `mssr_forum`.`mssr_forum_reply`.`reply_state`=1 -- 回文狀態
                    AND `mssr_forum`.`mssr_forum_reply`.`article_id` ={$get_article_id}
                ORDER BY `mssr_forum`.`mssr_forum_reply`.`keyin_cdate` ASC
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $db_results_cno=count($db_results);
            $reply_results=array();

            //-------------------------------------------
            //分頁處理
            //-------------------------------------------

                $numrow=$db_results_cno;    //資料總筆數
                $psize =20;                 //單頁筆數,預設20筆
                $pnos  =0;                  //分頁筆數
                $pinx  =1;                  //目前分頁索引,預設1
                $sinx  =0;                  //值域起始值
                $einx  =0;                  //值域終止值

                if(isset($_GET['psize'])){
                    $psize=(int)$_GET['psize'];
                    if($psize===0){
                        $psize=20;
                    }
                }
                if(isset($_GET['pinx'])){
                    $pinx=(int)$_GET['pinx'];
                    if($pinx===0){
                        $pinx=1;
                    }
                }

                $pnos  =ceil($numrow/$psize);
                $pinx  =($pinx>$pnos)?$pnos:$pinx;

                $sinx  =(($pinx-1)*$psize)+1;
                $einx  =(($pinx)*$psize);
                $einx  =($einx>$numrow)?$numrow:$einx;

                if($numrow!==0){
                    $arrys_chunk =array_chunk($db_results,$psize);
                    $reply_results=$arrys_chunk[$pinx-1];
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
                    `mssr`.`mssr_book_borrow_log`.`user_id`
                FROM `mssr`.`mssr_book_borrow_log`
                WHERE 1=1
                    AND `mssr`.`mssr_book_borrow_log`.`book_sid`='{$get_book_sid}'
                GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`,
                         `mssr`.`mssr_book_borrow_log`.`book_sid`
            ";
            $user_borrow_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $user_borrow_cno=count($user_borrow_results);


            //提取聊書好友資訊
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
                    foreach($user_borrow_results as $user_borrow_result){
                        $rs_user_id=(int)$user_borrow_result['user_id'];
                        if($rs_user_id===$sess_user_id || !in_array($rs_user_id,$arry_forum_friend))continue;
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
        //相關書籍 SQL
        //-----------------------------------------------

            $arry_about_books=array();
            if($book_isbn_10!=='' || $book_isbn_13!==''){
                $sql="
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
                $sql.="
                    SELECT
                        `mssr`.`mssr_book_unverified`.`book_sid`,
                        `mssr`.`mssr_book_unverified`.`book_name`
                    FROM `mssr`.`mssr_book_unverified`
                    WHERE 1=1
                ";
                if($book_isbn_10!=='')$sql.="AND `mssr`.`mssr_book_unverified`.`book_isbn_10`='{$book_isbn_10}'";
                if($book_isbn_13!=='')$sql.="AND `mssr`.`mssr_book_unverified`.`book_isbn_13`='{$book_isbn_13}'";
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
                        $rs_book_sid=mysql_prep(trim($key));
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
                    SELECT `mssr`.`mssr_book_borrow_log`.`book_sid`
                    FROM `mssr`.`mssr_book_borrow_log`
                    WHERE 1=1
                        AND `mssr`.`mssr_book_borrow_log`.`user_id`={$sess_user_id}
                    GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`,
                             `mssr`.`mssr_book_borrow_log`.`book_sid`
                    ORDER BY `mssr`.`mssr_book_borrow_log`.`borrow_sdate` DESC
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
            }

        //-----------------------------------------------
        //身分判斷
        //-----------------------------------------------

            $arry_user_status=array();
            if(isset($sess_user_id)){
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
            }

        //-----------------------------------------------
        //修正書籍資訊權限
        //-----------------------------------------------

            $auth_edit_book_info=false;
            //if((in_array('i_a',$arry_user_status)||in_array('i_t',$arry_user_status)||in_array('i_sa',$arry_user_status))&&($book_isbn_10===''||$book_isbn_13===''))$auth_edit_book_info=true;
            //if(in_array('i_s',$arry_user_status)&&($book_isbn_10===''||$book_isbn_13===''))$auth_edit_book_info=true;

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

            //收藏文章
            $btn_add_track_article_html=trim('收藏文章');
            if(isset($sess_user_id)){
                $sql="
                    SELECT `mssr_forum`.`mssr_forum_track_article`.`user_id`
                    FROM  `mssr_forum`.`mssr_forum_track_article`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_track_article`.`user_id`   ={$sess_user_id }
                        AND `mssr_forum`.`mssr_forum_track_article`.`article_id`={$rs_article_id}
                        AND `mssr_forum`.`mssr_forum_track_article`.`group_id`  ={$get_group_id }
                ";
                $track_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(!empty($track_article_results)){
                    $btn_add_track_article_html='已收藏文章';
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

            //樓主,加為好友
            $btn_add_friend_show=false;
            $btn_add_friend_html=trim('加為好友');
            if(isset($sess_user_id)){
                if($sess_user_id!==$rs_article_user_id){
                    $get_forum_friend=get_forum_friend($sess_user_id,$rs_article_user_id,$arry_conn_mssr);
                    if(empty($get_forum_friend)){
                        $btn_add_friend_show=true;
                    }else{
                        if((int)$get_forum_friend[0]['friend_state']===2)$btn_add_friend_show=true;
                        if((int)$get_forum_friend[0]['friend_state']===3){$btn_add_friend_show=true;$btn_add_friend_html=trim('好友確認中');}
                    }
                }
            }

            //樓主,檢舉
            $btn_report_article_html=trim('檢舉');
            if(isset($sess_user_id)){
                $sql="
                    SELECT `mssr_forum`.`mssr_forum_article_report_log`.`user_id`
                    FROM  `mssr_forum`.`mssr_forum_article_report_log`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_article_report_log`.`user_id`   ={$sess_user_id }
                        AND `mssr_forum`.`mssr_forum_article_report_log`.`article_id`={$rs_article_id}
                ";
                $report_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($report_article_results)){
                    foreach($report_article_results as $report_article_result){
                        //if((int)$report_article_result['user_id']===$sess_user_id)$btn_report_article_html=trim('已檢舉');
                    }
                }
            }

            //樓主,讚
            $btn_like_article_html=trim('讚');
            if(isset($sess_user_id)){
                $sql="
                    SELECT `mssr_forum`.`mssr_forum_article_like_log`.`user_id`
                    FROM  `mssr_forum`.`mssr_forum_article_like_log`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_article_like_log`.`user_id`   ={$sess_user_id }
                        AND `mssr_forum`.`mssr_forum_article_like_log`.`article_id`={$rs_article_id}
                ";
                $like_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                $like_article_cno    =count($like_article_results);
                if(!empty($like_article_results)){
                    foreach($like_article_results as $like_article_result){
                        if((int)$like_article_result['user_id']===$sess_user_id)$btn_like_article_html=trim('收回讚');
                    }
                }
                $btn_like_article_html.="({$like_article_cno})";
            }

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
    <link href="../../../lib/jquery/ui/code.css" rel="stylesheet" type="text/css">
    <link href="../../../lib/framework/bootstrap/css/code.css" rel="stylesheet" type="text/css">
    <link href="../../../inc/code.css" rel="stylesheet" type="text/css">

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
                    <div style='font-size:12px;'>
                        <?php echo $user_borrow_cno;?>位看過這本書&nbsp;(包含<?php echo $friend_borrow_cno;?>位好友)
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
                        <td width="215px" align="center">
                            <!-- 大解析度 -->
                            <?php if(isset($sess_user_id)):?>
                                <button type="button" class="btn_add_hot_booklist btn <?php echo $btn_add_hot_booklist_style;?> btn-xs hidden-xs"
                                style="position:relative;top:0px;"
                                user_id=<?php echo $sess_user_id;?>
                                book_sid="<?php echo $get_book_sid;?>"
                                title="每天都能投一次票讓老師知道你想討論這本書。"
                                ><?php echo $btn_add_hot_booklist_html;?></button>
                                <button type="button" class="btn_add_track_book btn <?php echo $btn_add_track_book_style;?> btn-xs hidden-xs"
                                style="position:relative;top:0px;"
                                user_id=<?php echo $sess_user_id;?>
                                book_sid="<?php echo $get_book_sid;?>"
                                title="追蹤書籍並顯示在動態牆。"
                                ><?php echo $btn_add_track_book_html;?></button>
                                <?php if($auth_edit_book_info):?>
                                    <button type="button" class="btn btn-default btn-xs hidden-xs"
                                    style="position:relative;top:0px;" onclick="$('#myTab li:eq(1) a').tab('show');void(0);"
                                    title="協助填寫書籍ISBN資訊"
                                    >填寫ISBN</button>
                                <?php endif;?>
                            <?php endif;?>

                            <!-- 小解析度 -->
                            <?php if(isset($sess_user_id)):?>
                                <button type="button" class="btn_add_track_book btn <?php echo $btn_add_track_book_style;?> btn-xs pull-right hidden-sm hidden-md hidden-lg"
                                style="position:relative;top:3px;"
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
                                <?php if($auth_edit_book_info):?>
                                    <button type="button" class="btn btn-default btn-xs pull-right hidden-sm hidden-md hidden-lg"
                                    style="position:relative;top:3px;margin:0 1px;" onclick="$('#myTab li:eq(1) a').tab('show');void(0);"
                                    title="協助填寫書籍ISBN資訊"
                                    >填ISBN</button>
                                <?php endif;?>
                            <?php endif;?>
                        </td>
                        <td class="hidden-xs" align="center"><!-- <span><?php echo $add_article_cno;?>篇發文   </span> --></td>
                        <td class="hidden-xs" align="center"><!-- <span><?php echo $reply_article_cno;?>篇回覆 </span> --></td>
                        <td class="hidden-xs" align="center"><!-- <span>
                            <?php echo $user_borrow_cno;?>位看過這本書&nbsp;(包含<?php echo $friend_borrow_cno;?>位好友)
                        </span> --></td>
                    </tr></tbody>
                </table>
            </div>
            <!-- page_info,end -->

            <!-- book_lefe_side,start -->
            <div class="book_lefe_side col-xs-12 col-sm-10 col-md-10 col-lg-10">

                <ul id="myTab" class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#view_article" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">
                        討論串
                    </a></li>
                    <?php if($auth_edit_book_info):?>
                        <li role="presentation" class="hidden"><a href="#book_info" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile">
                            修正書本資訊
                        </a></li>
                    <?php endif;?>
                    <!-- <li role="presentation"><a href="#reply_article" id="profile-tab" role="tab" data-toggle="tab" aria-controls="profile">
                        回文
                    </a></li> -->
                    <li role="presentation" class="hidden-sm hidden-md hidden-lg">
                        <button type="button" class="btn_modal_jumbotron_note btn btn-xs hidden-sm hidden-md hidden-lg"
                        data-toggle="modal" data-target=".bs-example-modal-sm"><b>內容簡介</b></button>
                    </li>
                </ul>
                <div id="myTabContent" class="tab-content">

                    <!-- 觀看文章 -->
                    <div role="tabpanel" class="tab-pane fade in active" id="view_article" aria-labelledBy="home-tab">

                        <?php if(!$my_has_borrow_flag):?>
                            <pre style="background-color:#ffffdd;position:relative;margin-top:15px;margin-bottom:-5px;">您尚未閱讀過這本書...... <a target="_blank" href="/mssr/service/code.php?mode=read_the_registration">前往登記</a></pre>
                        <?php endif;?>

                        <!-- 分頁 -->
                        <?php if($numrow!==0):?>
                            <?php echo pagination((int)$pinx,(int)$psize,(int)$pnos,$url="reply.php?get_from=1&article_id={$rs_article_id}");?>
                        <?php endif;?>

                        <!-- 標題 -->
                        <div class="article_title row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <?php echo htmlspecialchars($rs_article_title);?>
                            </div>
                        </div>

                        <!-- 樓主 -->
                        <?php if((int)$pinx===1 || (int)$pinx===0):?>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="media">
                                    <a class="pull-left" href="user.php?user_id=<?php echo $rs_article_user_id;?>&tab=1">
                                        <img class="media-object" src="<?php echo $rs_article_img;?>" alt="Media">
                                    </a>
                                    <h4 class="media-heading">
                                        <?php echo htmlspecialchars($rs_article_user_name);?>

                                        <!-- 功能鈕,大解析度,start -->
                                        <?php if(isset($sess_user_id)):?>
                                            <?php if($sess_user_id===$rs_article_user_id):?>
                                                <button type="button" class="btn_edit_article btn btn-default btn-xs pull-right hidden-xs"
                                                article_id=<?php echo $rs_article_id;?>
                                                onclick="location.href='forum.php?method=edit_article&article_id=<?php echo $rs_article_id;?>&pinx=<?php echo $pinx;?>&psize=<?php echo $psize;?>';"
                                                >編輯</button>
                                            <?php endif?>

                                            <?php if($my_has_borrow_flag):?>
                                                <button type="button" class="btn_request_article btn btn-default btn-xs pull-right hidden-xs"
                                                data-toggle="modal" data-target=".modal_request_article">邀請好友一同聊書</button>
                                            <?php endif;?>

                                            <?php if($btn_add_friend_show):?>
                                                <button type="button" class="btn_add_friend btn btn-default btn-xs pull-right hidden-xs"
                                                user_id=<?php echo $sess_user_id;?>
                                                friend_id=<?php echo $rs_article_user_id;?>><?php echo $btn_add_friend_html;?></button>
                                            <?php endif;?>

                                            <?php if(in_array('i_t',$arry_user_status)||in_array('i_a',$arry_user_status)):?>
                                                <button type="button" class="btn_del_article btn btn-default btn-xs pull-right hidden-xs"
                                                article_id=<?php echo $rs_article_id;?>>移除文章</button>
                                            <?php endif?>

                                            <button type="button" class="btn_add_track_article btn btn-default btn-xs pull-right hidden-xs"
                                            user_id=<?php echo $sess_user_id;?>
                                            article_id=<?php echo $rs_article_id;?>
                                            group_id=<?php echo $get_group_id;?>>
                                            <?php echo $btn_add_track_article_html;?></button>

                                            <?php if($my_has_borrow_flag):?>
                                                <button type="button" class="btn_report_article btn btn-default btn-xs pull-right hidden-xs"
                                                user_id=<?php echo $sess_user_id;?>
                                                article_id=<?php echo $rs_article_id;?>
                                                reply_id=0><?php echo $btn_report_article_html;?></button>
                                                <button type="button" class="btn_like_article btn btn-default btn-xs pull-right hidden-xs"
                                                user_id=<?php echo $sess_user_id;?>
                                                article_id=<?php echo $rs_article_id;?>
                                                reply_id=0><?php echo $btn_like_article_html;?></button>
                                            <?php endif;?>
                                        <?php endif;?>
                                        <!-- 功能鈕,大解析度,end -->

                                        <!-- 功能鈕,小解析度,start -->
                                        <?php if(isset($sess_user_id)):?>
                                            <div class="btn-group pull-right hidden-sm hidden-md hidden-lg">
                                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    功能&nbsp;<span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <?php if($my_has_borrow_flag):?>
                                                        <li><a href="#" class="btn_request_article"
                                                        data-toggle="modal" data-target=".modal_request_article">邀請好友一同聊書</a></li>
                                                    <?php endif;?>

                                                    <?php if(in_array('i_t',$arry_user_status)||in_array('i_a',$arry_user_status)):?>
                                                        <li><a href="javascript:void(0);" class="btn_del_article"
                                                        article_id=<?php echo $rs_article_id;?>>移除文章</a></li>
                                                    <?php endif?>

                                                    <?php if($btn_add_friend_show):?>
                                                        <li><a href="javascript:void(0);" class="btn_add_friend"
                                                        user_id=<?php echo $sess_user_id;?>
                                                        friend_id=<?php echo $rs_article_user_id;?>><?php echo $btn_add_friend_html;?></a></li>
                                                    <?php endif;?>

                                                    <li><a href="javascript:void(0);" class="btn_add_track_article"
                                                    user_id=<?php echo $sess_user_id;?>
                                                    article_id=<?php echo $rs_article_id;?>
                                                    group_id=<?php echo $get_group_id;?>><?php echo $btn_add_track_article_html;?></a></li>

                                                    <?php if($my_has_borrow_flag):?>
                                                        <li><a href="javascript:void(0);" class="btn_report_article"
                                                        user_id=<?php echo $sess_user_id;?>
                                                        article_id=<?php echo $rs_article_id;?>
                                                        reply_id=0><?php echo $btn_report_article_html;?></a></li>
                                                        <li><a href="javascript:void(0);" class="btn_like_article"
                                                        user_id=<?php echo $sess_user_id;?>
                                                        article_id=<?php echo $rs_article_id;?>
                                                        reply_id=0><?php echo $btn_like_article_html;?></a></li>
                                                    <?php endif;?>

                                                    <?php if($sess_user_id===$rs_article_user_id):?>
                                                        <li><a href="javascript:void(0);" class="btn_edit_article"
                                                        article_id=<?php echo $rs_article_id;?>
                                                        onclick="location.href='forum.php?method=edit_article&article_id=<?php echo $rs_article_id;?>&pinx=<?php echo $pinx;?>&psize=<?php echo $psize;?>';"
                                                        >編輯</a></li>
                                                    <?php endif?>
                                                </ul>
                                            </div>
                                        <?php endif?>
                                        <!-- 功能鈕,小解析度,end -->

                                        <div><?php echo htmlspecialchars($rs_article_keyin_mdate);?>&nbsp;#1</div>
                                        <div class="hidden-xs" style="position:relative;top:17px;">文章編號：<?php echo htmlspecialchars($rs_article_id);?></div>
                                    </h4>
                                    <div class="pull-right media-body">
                                        <a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_article_book_sid);?>" style='color:#4e4e4e;'>
                                            <img width="40" height="40" src="<?php echo $rs_article_book_img;?>" border="0">
                                            <?php echo htmlspecialchars($rs_article_book_name);?>
                                        </a>
                                    </div>
                                    <div class="pull-left media-body">
                                        <?php echo nl2br(($rs_article_content));?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif;?>

                        <!-- 各樓層 -->
                        <?php if(!empty($reply_results)){
                            foreach($reply_results as $inx=>$reply_result):
                                $rs_reply_book_sid      =trim($reply_result['book_sid']);
                                $rs_reply_user_name     =trim($reply_result['name']);
                                $rs_reply_keyin_mdate   =trim($reply_result['keyin_mdate']);
                                $rs_reply_like_cno      =(int)($reply_result['reply_like_cno']);
                                $rs_article_id          =(int)($reply_result['article_id']);
                                $rs_reply_id            =(int)($reply_result['reply_id']);
                                $rs_reply_user_id       =(int)($reply_result['user_id']);
                                $rs_reply_content       =trim($reply_result['reply_content']);
                                $rs_reply_img           ='../img/default/user_boy.png';

                                //特殊處理
                                $sql="
                                    SELECT `sex`
                                    FROM `user`.`member`
                                    WHERE 1=1
                                        AND `user`.`member`.`uid`={$rs_reply_user_id}
                                ";
                                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                $rs_reply_user_sex  =1;
                                if(!empty($db_results)){
                                    $rs_reply_user_sex =(int)$db_results[0]['sex'];
                                    if($rs_reply_user_sex===1)$rs_reply_img='../img/default/user_boy.png';
                                    if($rs_reply_user_sex===2)$rs_reply_img='../img/default/user_girl.png';

                                    if(isset($file_server_enable)&&($file_server_enable)){
                                        $rs_reply_img_ftp_path="{$ftp_root}/{$rs_reply_user_id}/forum/user_sticker";
                                        $arry_rs_reply_img_ftp_file=ftp_nlist($ftp_conn,$rs_reply_img_ftp_path);
                                        if(isset($arry_rs_reply_img_ftp_file[0])){
                                            $rs_reply_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_reply_user_id}/forum/user_sticker/1.jpg";
                                        }
                                    }else{
                                        if(file_exists("../../../info/user/{$rs_reply_user_id}/forum/user_sticker/1.jpg")){
                                            $rs_reply_img="../../../info/user/{$rs_reply_user_id}/forum/user_sticker/1.jpg";
                                        }
                                    }
                                }else{die();}

                                $rs_reply_book_name='';
                                $arry_book_infos=get_book_info($conn_mssr,$rs_reply_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                if(!empty($arry_book_infos)){$rs_reply_book_name=trim($arry_book_infos[0]['book_name']);}else{continue;}
                                $rs_reply_book_img='../img/default/book.png';
                                if(file_exists("../../../info/book/{$rs_reply_book_sid}/img/front/simg/1.jpg")){
                                    $rs_reply_book_img="../../../info/book/{$rs_reply_book_sid}/img/front/simg/1.jpg";
                                }

                                //各樓層,加為好友
                                $btn_add_friend_show=false;
                                $btn_add_friend_html=trim('加為好友');
                                if(isset($sess_user_id)){
                                    if($sess_user_id!==$rs_reply_user_id){
                                        $get_forum_friend=get_forum_friend($sess_user_id,$rs_reply_user_id,$arry_conn_mssr);
                                        if(empty($get_forum_friend)){
                                            $btn_add_friend_show=true;
                                        }else{
                                            if((int)$get_forum_friend[0]['friend_state']===2)$btn_add_friend_show=true;
                                            if((int)$get_forum_friend[0]['friend_state']===3){$btn_add_friend_show=true;$btn_add_friend_html=trim('好友確認中');}
                                        }
                                    }
                                }

                                //各樓層,檢舉
                                $btn_report_reply_html=trim('檢舉');
                                if(isset($sess_user_id)){
                                    $sql="
                                        SELECT `mssr_forum`.`mssr_forum_reply_report_log`.`user_id`
                                        FROM  `mssr_forum`.`mssr_forum_reply_report_log`
                                        WHERE 1=1
                                            AND `mssr_forum`.`mssr_forum_reply_report_log`.`user_id` ={$sess_user_id }
                                            AND `mssr_forum`.`mssr_forum_reply_report_log`.`reply_id`={$rs_reply_id  }
                                    ";
                                    $report_reply_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                    if(!empty($report_reply_results)){
                                        foreach($report_reply_results as $report_reply_result){
                                            //if((int)$report_reply_result['user_id']===$sess_user_id)$btn_report_reply_html=trim('已檢舉');
                                        }
                                    }
                                }

                                //各樓層,讚
                                $btn_like_reply_html=trim('讚');
                                if(isset($sess_user_id)){
                                    $sql="
                                        SELECT `mssr_forum`.`mssr_forum_reply_like_log`.`user_id`
                                        FROM  `mssr_forum`.`mssr_forum_reply_like_log`
                                        WHERE 1=1
                                            AND `mssr_forum`.`mssr_forum_reply_like_log`.`user_id` ={$sess_user_id }
                                            AND `mssr_forum`.`mssr_forum_reply_like_log`.`reply_id`={$rs_reply_id  }
                                    ";
                                    $like_reply_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                    $like_reply_cno    =count($like_reply_results);
                                    if(!empty($like_reply_results)){
                                        foreach($like_reply_results as $like_reply_result){
                                            if((int)$like_reply_result['user_id']===$sess_user_id)$btn_like_reply_html=trim('收回讚');
                                        }
                                    }
                                    $btn_like_reply_html.="({$like_reply_cno})";
                                }
                        ?>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="media">
                                    <a class="pull-left" href="user.php?user_id=<?php echo $rs_reply_user_id;?>&tab=1">
                                        <img class="media-object" src="<?php echo $rs_reply_img;?>" alt="Media">
                                    </a>
                                    <h4 class="media-heading">
                                        <?php echo htmlspecialchars($rs_reply_user_name);?>

                                        <!-- 功能鈕,大解析度,start -->
                                        <?php if(isset($sess_user_id)):?>
                                            <?php if($btn_add_friend_show):?>
                                                <button type="button" class="btn_add_friend btn btn-default btn-xs pull-right hidden-xs"
                                                user_id=<?php echo $sess_user_id;?>
                                                friend_id=<?php echo $rs_reply_user_id;?>><?php echo $btn_add_friend_html;?></button>
                                            <?php endif;?>

                                            <?php if($sess_user_id===$rs_reply_user_id):?>
                                                <button type="button" class="btn_edit_reply btn btn-default btn-xs pull-right hidden-xs"
                                                reply_id=<?php echo $rs_reply_id;?>
                                                onclick="location.href='forum.php?method=edit_reply&reply_id=<?php echo $rs_reply_id;?>&pinx=<?php echo $pinx;?>&psize=<?php echo $psize;?>';"
                                                >編輯</button>
                                            <?php endif?>

                                            <?php if(in_array('i_t',$arry_user_status)||in_array('i_a',$arry_user_status)):?>
                                                <button type="button" class="btn_del_reply btn btn-default btn-xs pull-right hidden-xs"
                                                reply_id=<?php echo $rs_reply_id;?>>移除回覆</button>
                                            <?php endif?>

                                            <?php if($my_has_borrow_flag):?>
                                                <button type="button" class="btn_report_article btn btn-default btn-xs pull-right hidden-xs"
                                                user_id=<?php echo $sess_user_id;?>
                                                article_id=<?php echo $rs_article_id;?>
                                                reply_id=<?php echo $rs_reply_id;?>><?php echo $btn_report_reply_html;?></button>
                                                <button type="button" class="btn_like_article btn btn-default btn-xs pull-right hidden-xs"
                                                user_id=<?php echo $sess_user_id;?>
                                                article_id=<?php echo $rs_article_id;?>
                                                reply_id=<?php echo $rs_reply_id;?>><?php echo $btn_like_reply_html;?></button>
                                            <?php endif;?>
                                        <?php endif;?>
                                        <!-- 功能鈕,大解析度,end -->

                                        <!-- 功能鈕,小解析度,start -->
                                        <?php if(isset($sess_user_id)):?>
                                            <div class="btn-group pull-right hidden-sm hidden-md hidden-lg">
                                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    功能&nbsp;<span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <?php if($btn_add_friend_show):?>
                                                        <li><a href="javascript:void(0);" class="btn_add_friend"
                                                        user_id=<?php echo $sess_user_id;?>
                                                        friend_id=<?php echo $rs_reply_user_id;?>><?php echo $btn_add_friend_html;?></a></li>
                                                    <?php endif;?>

                                                    <?php if(in_array('i_t',$arry_user_status)||in_array('i_a',$arry_user_status)):?>
                                                        <li><a href="javascript:void(0);" class="btn_del_reply"
                                                        reply_id=<?php echo $rs_reply_id;?>>移除回覆</a></li>
                                                    <?php endif?>

                                                    <?php if($my_has_borrow_flag):?>
                                                        <li><a href="javascript:void(0);" class="btn_report_article"
                                                        user_id=<?php echo $sess_user_id;?>
                                                        article_id=<?php echo $rs_article_id;?>
                                                        reply_id=<?php echo $rs_reply_id;?>><?php echo $btn_report_reply_html;?></a></li>
                                                        <li><a href="javascript:void(0);" class="btn_like_article"
                                                        user_id=<?php echo $sess_user_id;?>
                                                        article_id=<?php echo $rs_article_id;?>
                                                        reply_id=<?php echo $rs_reply_id;?>><?php echo $btn_like_reply_html;?></a></li>
                                                    <?php endif;?>

                                                    <?php if($sess_user_id===$rs_reply_user_id):?>
                                                        <li><a href="javascript:void(0);" class="btn_edit_reply"
                                                        reply_id=<?php echo $rs_reply_id;?>
                                                        onclick="location.href='forum.php?method=edit_reply&reply_id=<?php echo $rs_reply_id;?>&pinx=<?php echo $pinx;?>&psize=<?php echo $psize;?>';"
                                                        >編輯</a></li>
                                                    <?php endif?>
                                                </ul>
                                            </div>
                                        <?php endif;?>
                                        <!-- 功能鈕,小解析度,end -->

                                        <div><?php echo htmlspecialchars($rs_reply_keyin_mdate);?>&nbsp;#<?php echo ($pinx*$psize)-$psize+2;?></div>
                                    </h4>
                                    <div class="pull-right media-body">
                                        <a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_reply_book_sid);?>" style='color:#4e4e4e;'>
                                            <img width="40" height="40" src="<?php echo $rs_reply_book_img;?>" border="0">
                                            <?php echo htmlspecialchars($rs_reply_book_name);?>
                                        </a>
                                    </div>
                                    <div class="pull-left media-body">
                                        <?php echo nl2br(htmlspecialchars($rs_reply_content));?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach;}?>

                        <!-- 分頁 -->
                        <?php if($numrow!==0):?>
                            <?php echo pagination((int)$pinx,(int)$psize,(int)$pnos,$url="reply.php?get_from=1&article_id={$rs_article_id}");?>
                        <?php endif;?>

                        <!-- 回文 -->
                        <div id="reply_article_row" class="row">
                            <div id="reply_article" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <!-- <pre class="text-center" style="background-color:#428bca;color:#ffffff;">回覆文章</pre> -->
                                <h4 style="border-bottom:1px solid #428bca;margin-top:10px;padding:10px 0;color:#428bca;">發表回覆</h4>
                                <?php if($my_has_borrow_flag):?>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
                                            <select class="form-control" onchange="reply_eagle();void(0);">
                                                <option disabled="disabled" selected>請選擇類型來開始回覆文章 </option>
                                                <option value="1">我覺得你說的很好，但我還想補充……            </option>
                                                <option value="2">在……的部分，我跟你的想法不一樣，因為……      </option>
                                                <option value="0">其他                                        </option>
                                            </select>
                                        </div>
                                    </div>
                                <?php else:?>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
                                            您尚未閱讀過這本書，試試以下方法吧！<br><br>
                                            1.  <button type="button" class="btn btn-default btn-xs"
                                                style="position:relative;top:-2px;" onclick="window.open('/mssr/service/code.php?mode=read_the_registration');void(0);"
                                                title="閱讀登記書籍資訊"
                                                >前往閱讀登記</button><br><br>
                                            <?php if($auth_edit_book_info):?>
                                                2.  <button type="button" class="btn btn-default btn-xs"
                                                    style="position:relative;top:-2px;" onclick="$('#myTab li:eq(1) a').tab('show');void(0);"
                                                    title="協助填寫書籍ISBN資訊"
                                                    >填寫ISBN</button>
                                                    <div style="height:20px;"></div>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                <?php endif;?>
                            </div>
                        </div>

                    </div>

                    <!-- 回文 -->
                    <!-- <div role="tabpanel" class="tab-pane fade" id="reply_article" aria-labelledBy="home-tab">
                        <?php if($my_has_borrow_flag):?>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    主題：&nbsp;<?php echo htmlspecialchars($rs_article_title);?><hr></hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
                                    <select class="form-control" onchange="reply_eagle();void(0);">
                                        <option disabled="disabled" selected>請選擇類型來開始回文 </option>
                                        <option value="1">我覺得你說的很好，但我還想補充……            </option>
                                        <option value="2">在……的部分，我跟你的想法不一樣，因為……      </option>
                                        <option value="0">其他                                        </option>
                                    </select>
                                </div>
                            </div>
                        <?php else:?>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
                                    您尚未閱讀過這本書...... <a target="_blank" href="/mssr/service/code.php?mode=read_the_registration">前往登記</a>
                                </div>
                            </div>
                        <?php endif;?>
                    </div> -->

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
                                    <?php if((in_array('i_s',$arry_user_status)&&($book_isbn_10===''))||(in_array('i_a',$arry_user_status)||in_array('i_t',$arry_user_status)||in_array('i_sa',$arry_user_status))&&($book_isbn_10==='')):?>
                                        <div class="input-group" style="margin-bottom:15px">
                                            <span class="input-group-addon">ISBN10碼</span>
                                            <input class="form-control" type="text" id="book_isbn_10" name="book_isbn_10" value="<?php echo htmlspecialchars($book_isbn_10);?>"
                                            placeholder="請輸入ISBN10碼" maxlength="10" required autofocus>
                                        </div>
                                    <?php endif;?>
                                    <?php if((in_array('i_s',$arry_user_status)&&($book_isbn_13===''))||(in_array('i_a',$arry_user_status)||in_array('i_t',$arry_user_status)||in_array('i_sa',$arry_user_status))&&($book_isbn_13==='')):?>
                                        <div class="input-group" style="margin-bottom:15px">
                                            <span class="input-group-addon">ISBN13碼</span>
                                            <input class="form-control" type="text" id="book_isbn_13" name="book_isbn_13" value="<?php echo htmlspecialchars($book_isbn_13);?>"
                                            placeholder="請輸入ISBN13碼" maxlength="13" required autofocus>
                                        </div>
                                    <?php endif;?>

                                    <hr></hr>
                                    <div class="form-group pull-right">
                                        <button type="button" id="btn_edit_book_info" class="btn btn-default">儲存</button>
                                    </div>

                                    <div class="form-group hidden">
                                        <input type="hidden" class="form-control" name="book_sid" value="<?php echo trim($get_book_sid);?>">
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

    <!-- modal_request_article,start -->
    <?php echo $modal_dialog_2;?>
    <!-- modal_request_article,end -->

</body>

<!-- 通用 -->
<script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/ui/code.js"></script>
<script type="text/javascript" src="../../../lib/js/string/code.js"></script>
<script type="text/javascript" src="../../../lib/js/array/code.js"></script>
<script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>
<script type="text/javascript" src="../../../inc/code.js"></script>

<!-- 專屬 -->
<script type="text/javascript" src="../inc/code.js"></script>

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;
    var get_from=<?php echo $get_from;?>;
    var send_url=document.URL;
    var get_article_id=parseInt(<?php echo $get_article_id;?>);
    var get_group_id=parseInt(<?php echo (int)$get_group_id;?>);
    var arry_friend_info={};
    var arry_friend_name=[];
    var book_sid    =trim('<?php echo $get_book_sid;?>');

    <?php if(isset($sess_user_id)):?>
        var sess_user_id=parseInt(<?php echo $sess_user_id;?>);
    <?php else:?>
        var sess_user_id=parseInt(0);
    <?php endif;?>

    <?php
    if(!empty($arry_forum_friend)){
        foreach($arry_forum_friend as $arry_val):
            $rs_friend_id=(int)$arry_val;
            $sql="
                SELECT `name`
                FROM `user`.`member`
                WHERE 1=1
                    AND `user`.`member`.`uid`={$rs_friend_id}
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(!empty($db_results)){
                $rs_user_name=htmlspecialchars(trim($db_results[0]['name']));
            }else{continue;}
    ?>
        arry_friend_info['<?php echo $rs_user_name;?>']=<?php echo $rs_friend_id;?>;
        arry_friend_name.push('<?php echo $rs_user_name;?>');
    <?php endforeach;}?>


    //OBJ
    var article_eagle_content=<?php echo json_encode($article_eagle_content,true);?>;
    var article_eagle_code   =<?php echo json_encode($article_eagle_code,true);?>;
    var arry_my_borrow       =<?php echo json_encode($arry_my_borrow,true);?>;


    //FUNCTION
    $('#btn_edit_book_info').click(function(){
    //修正書本資訊

        var oForm_edit_book_info=$('#Form_edit_book_info')[0];
        var obook_isbn_10       =$('#book_isbn_10')[0];
        var obook_isbn_13       =$('#book_isbn_13')[0];
        var arry_err            =[];

        try{
            if(trim(obook_isbn_10.value)===''&&trim(obook_isbn_13.value)===''){
                arry_err.push('請輸入ISBN10碼或ISBN13碼');
            }
        }catch(e){}

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要儲存嗎 ?')){
                oForm_edit_book_info.action='../controller/edit.php'
                oForm_edit_book_info.submit();
                return true;
            }
        }
    });

    function chk_request_article(obj,friend_id){
    //邀請好友一同聊書選取特效

        var friend_id=parseInt(friend_id);
        if(obj.checked===true){
            $('.div_request_article_'+friend_id).css("background-color","#ffffdd");
        }else{
            $('.div_request_article_'+friend_id).css("background-color","#ffffff");
        }
    }

    //$('.request_article_friend_name').keypress(function(e){
    ////自動選取
    //
    //    if(e.which==13){
    //        if(in_array($.trim($(this).val()),arry_friend_name)){
    //            $('.chk_request_article_'+parseInt(arry_friend_info[$.trim($(this).val())]))[0].checked=true;
    //            $('.div_request_article_'+parseInt(arry_friend_info[$.trim($(this).val())])).css("background-color","#ffffdd");
    //            $(this).val('');
    //        }else{
    //            alert('請輸入正確的好友名稱');
    //        }
    //    }
    //});
    function auto(obj,no){
    //選單貼上

        var request_article_friend_name   =trim($(obj).text());
        var no                            =parseInt(no);
        //var orequest_article_friend_id    =document.getElementsByName('request_article_friend_id')[no];
        var orequest_article_friend_name  =document.getElementsByName('request_article_friend_name[]')[no];
        //orequest_article_friend_id.value  =parseInt(arry_friend_info[request_article_friend_name]);
        orequest_article_friend_name.value=trim(request_article_friend_name);
    }
    $('.btn_request_article').click(function(){
        $('.request_article_friend_name').bind("keydown.autocomplete",function(){
            source: arry_friend_name
        });
    });
    $('.request_article_friend_name').autocomplete({
        source: arry_friend_name
    });
    function clone_request_article_tag(){
        $('.request_article_friend_name').autocomplete('destroy');
        $('.request_article_group:last').after($('.request_article_group').eq(0).clone(true));
        $('.request_article_group').find("A").each(function(){
            $(this).replaceWith("<a href='javascript:void(0);'>"+trim($(this).text())+"</a>");
        });
        $('.request_article_group').each(function(){
            var $request_article_group=$(this);
            $request_article_group.find("A").click(function(){
                auto($(this)[0],parseInt($request_article_group.index()-3));
            });
        });
        $('.request_article_group').eq($('.request_article_group').length-1).find("INPUT").val('').focus();
        $('.request_article_friend_name').autocomplete({
            source: arry_friend_name
        });
    }
    function del_request_article_tag(){
        if(parseInt($('.request_article_group').length)>1){
            $('.request_article_group').eq($('.request_article_group').length-1).remove();
        }
        $('.request_article_group').eq($('.request_article_group').length-1).find("INPUT").focus();
    }

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

    $('.btn_del_reply').click(function(){
    //移除回覆

        if(!confirm('你確定要移除回覆嗎?')){
            return false;
        }

        var reply_id  =parseInt($(this).attr('reply_id'));

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
                reply_id  :encodeURI(trim(reply_id      )),
                method      :encodeURI(trim('del_reply' )),
                send_url    :encodeURI(trim(send_url    ))
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
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    });

    $('.btn_del_article').click(function(){
    //移除文章

        if(!confirm('你確定要移除文章嗎?')){
            return false;
        }

        var article_id  =parseInt($(this).attr('article_id'));

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
                article_id  :encodeURI(trim(article_id      )),
                method      :encodeURI(trim('del_article'   )),
                send_url    :encodeURI(trim(send_url        ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                location.href='article.php?get_from=1&book_sid='+book_sid;
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

    $('.btn_add_track_article').click(function(){
    //追蹤文章

        var user_id     =parseInt($(this).attr('user_id'));
        var article_id  =parseInt($(this).attr('article_id'));
        var group_id    =parseInt($(this).attr('group_id'));

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
                article_id  :encodeURI(trim(article_id          )),
                group_id    :encodeURI(trim(group_id            )),
                method      :encodeURI(trim('add_track_article' )),
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

    $('#Btn_add_request_article').click(function(){
    //邀請好友一同聊書

        var oForm1=$('.modal_request_article').find('#Form1')[0];
        var orequest_article_friend_id   =document.getElementById('request_article_friend_id');
        var orequest_article_friend_names=document.getElementsByName('request_article_friend_name[]');
        var success_flag=true;
        var arry_has_sel=[];

        $(orequest_article_friend_names).each(function(){
            var orequest_article_friend_name=$(this)[0];
            if(trim(orequest_article_friend_name.value)===''){
                alert('請選擇一位好友');
                orequest_article_friend_name.focus();
                success_flag=false;
                return false;
            }else{
                if(!in_array(trim(orequest_article_friend_name.value),arry_friend_name)){
                    alert('請選擇或輸入正確的好友');
                    orequest_article_friend_name.focus();
                    success_flag=false;
                    return false;
                }
                if(!in_array(trim(orequest_article_friend_name.value),arry_has_sel)){
                    arry_has_sel.push(trim(orequest_article_friend_name.value));
                }else{
                    alert('請選擇不同的好友');
                    orequest_article_friend_name.focus();
                    success_flag=false;
                    return false;
                }
            }
        });
        if(!success_flag)return false;
        if(confirm('你確定要送出嗎 ?')){
            oForm1.action='../controller/add.php'
            oForm1.submit();
            return true;
        }else{
            return false;
        }

        //var $request_article_friend_ids=$('.request_article_friend_id');
        //var cno=0;
        //for(var i=0;i<$request_article_friend_ids.length;i++){
        //    var $request_article_friend_id=$request_article_friend_ids.eq(i);
        //    if($request_article_friend_id[0].checked===true){
        //        cno++;
        //    }
        //}
        //if(cno===0){
        //    alert('請至少選擇一位好友');
        //    return false;
        //}else{
        //    for(var i=0;i<$request_article_friend_ids.length;i++){
        //        var $request_article_friend_id=$request_article_friend_ids.eq(i);
        //        if($request_article_friend_id[0].checked===true){
        //            if(confirm('你確定要送出嗎 ?')){
        //                oForm1.action='../controller/add.php'
        //                oForm1.submit();
        //                return true;
        //            }else{
        //                return false;
        //            }
        //        }
        //    }
        //}
    });

    $('.btn_report_article').click(function(){
    //按文章檢舉

        var user_id     =parseInt($(this).attr('user_id'));
        var article_id  =parseInt($(this).attr('article_id'));
        var reply_id    =parseInt($(this).attr('reply_id'));

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
                article_id  :encodeURI(trim(article_id          )),
                reply_id    :encodeURI(trim(reply_id            )),
                method      :encodeURI(trim('add_report_article')),
                send_url    :encodeURI(trim(send_url            ))
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

    $('.btn_like_article').click(function(){
    //按文章讚

        var user_id     =parseInt($(this).attr('user_id'));
        var article_id  =parseInt($(this).attr('article_id'));
        var reply_id    =parseInt($(this).attr('reply_id'));

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
                article_id  :encodeURI(trim(article_id          )),
                reply_id    :encodeURI(trim(reply_id            )),
                method      :encodeURI(trim('add_like_article'  )),
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

    $('.btn_add_friend').click(function(){
    //加為好友

        var user_id  =parseInt($(this).attr('user_id'));
        var friend_id=parseInt($(this).attr('friend_id'));

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
                user_id     :encodeURI(trim(user_id         )),
                friend_id   :encodeURI(trim(friend_id       )),
                method      :encodeURI(trim('add_friend'    )),
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

    function Btn_add_article(){
    //發文

        var oForm1              =$('#add_article').find('#Form1')[0];
        var osend_chk           =$('#add_article').find('#send_chk')[0];
        var obook_sid           =$('#add_article').find('#book_sid')[0];
        var oarticle_title      =$('#add_article').find('#article_title')[0];
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

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要送出嗎 ?')){
                oForm1.action='../controller/add.php'
                oForm1.submit();
                return true;
            }else{
                return false;
            }
        }
    }

    function Btn_reply_article(){
    //回文

        var oForm1              =$('#reply_article').find('#Form1')[0];
        var osend_chk           =$('#reply_article').find('#send_chk')[0];
        var obook_sid           =$('#reply_article').find('#book_sid')[0];
        var oreply_contents     =document.getElementsByName('reply_content[]');
        var reply_content_err   =0;
        var arry_err            =[];

        if(trim(obook_sid.value)===''){
            arry_err.push('請選擇一本書來發文');
        }
        if(oreply_contents!==undefined && oreply_contents.length!==0){
            for(var i=0;i<oreply_contents.length;i++){
                oreply_content=oreply_contents[i];
                var placeholder=trim(oreply_content.getAttribute('placeholder'));
                if(trim(oreply_content.value)==='' || trim(oreply_content.value)===trim(placeholder)){
                    //arry_err.push('請輸入文章內容 '+(i+1));
                    reply_content_err++;
                }
            }
        }else{
            arry_err.push('文章內容框錯誤');
        }
        if(parseInt(oreply_contents.length)===parseInt(reply_content_err)){
            arry_err.push('請輸入文章內容');
        }
        if(!osend_chk.checked){
            arry_err.push('請閱讀並勾選同意討論區規則');
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要送出嗎 ?')){
                oForm1.action='../controller/add.php'
                oForm1.submit();
                return true;
            }else{
                return false;
            }
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


    //ONLOAD
    $(function(){
        //讀取側邊欄
        if(sess_user_id>0)load_right_side(trim('book'));
        //滾動監聽
        $(window).scroll(function(){
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
    })

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
        global $get_article_id;

        global $conn_mssr;
        global $arry_conn_mssr;
        global $file_server_enable;
        global $arry_ftp1_info;

        global $meta;
        global $navbar;
        global $carousel;
        global $modal_dialog_2;
        global $footbar;

        global $_GET;
        global $_POST;

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

        $get_article_id=(int)($get_article_id);

        //-----------------------------------------------
        //小組資訊 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_group`.`group_id`,
                    `mssr_forum`.`mssr_forum_group`.`group_name`,
                    `mssr_forum`.`mssr_forum_group`.`group_content`,
                    `mssr_forum`.`mssr_forum_group`.`group_rule`,
                    `mssr_forum`.`mssr_forum_group`.`group_type`,
                    `mssr_forum`.`mssr_forum_group`.`group_state`
                FROM `mssr_forum`.`mssr_forum_group`
                    INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                    `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_article`.`group_id`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_article`.`article_id`  ={$get_article_id}
                    AND `mssr_forum`.`mssr_forum_article`.`article_from`={$get_from      }
            ";
            $group_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(!empty($group_results)){
                $get_group_id =(int)$group_results[0]['group_id'];
                $group_name   =trim($group_results[0]['group_name']);
                $group_content=trim($group_results[0]['group_content']);
                $group_rule   =trim($group_results[0]['group_rule']);
                $group_type   =(int)$group_results[0]['group_type'];
                $group_state  =(int)$group_results[0]['group_state'];
            }else{die();}

        //-----------------------------------------------
        //小組大頭貼
        //-----------------------------------------------

            $group_img=trim('../img/default/group.jpg');

            if(isset($file_server_enable)&&($file_server_enable)){
                //FTP 路徑
                $ftp_root="public_html/mssr/info/forum";
                $ftp_path="{$ftp_root}/group/{$get_group_id}";

                //連接 | 登入 FTP
                $ftp_conn  =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                $ftp_login =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

                //設定被動模式
                ftp_pasv($ftp_conn,TRUE);

                //獲取檔案目錄
                $arry_ftp_group_sticker_file=ftp_nlist($ftp_conn,$ftp_path."/group_sticker");

                if(!empty($arry_ftp_group_sticker_file)){
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

            if(isset($file_server_enable)&&($file_server_enable)){
                //獲取檔案目錄
                $arry_ftp_background_group_file=ftp_nlist($ftp_conn,$ftp_path."/background_group");
                if(!empty($arry_ftp_background_group_file)){
                    $front_cover_group_1_img=trim("http://".$arry_ftp1_info['host']."/mssr/info/forum/group/{$get_group_id}/background_group/front_cover_group_1.jpg");
                }
            }else{
                if(file_exists("../../../info/forum/group/{$get_group_id}/background_group/front_cover_group_1.jpg")){
                    $front_cover_group_1_img=trim("../../../info/forum/group/{$get_group_id}/background_group/front_cover_group_1.jpg");
                }
            }

        //-----------------------------------------------
        //文章資訊 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `user`.`member`.`name`,
                    `user`.`member`.`sex`,

                    `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,

                    `mssr_forum`.`mssr_forum_article`.`user_id`,
                    `mssr_forum`.`mssr_forum_article`.`article_id`,
                    `mssr_forum`.`mssr_forum_article`.`article_like_cno`,
                    `mssr_forum`.`mssr_forum_article`.`article_report_cno`,
                    `mssr_forum`.`mssr_forum_article`.`keyin_mdate`,

                    `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                    `mssr_forum`.`mssr_forum_article_detail`.`article_content`
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
                    AND `mssr_forum`.`mssr_forum_article`.`group_id`     ={$get_group_id  }
                    AND `mssr_forum`.`mssr_forum_article`.`article_id`   ={$get_article_id}
            ";
            $article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(empty($article_results)){
                @header("Location:user.php?user_id={$sess_user_id}&tab=1");
                die();
            }else{
                $rs_article_book_sid    =trim($article_results[0]['book_sid']);
                $rs_article_title       =trim($article_results[0]['article_title']);
                $rs_article_content     =trim($article_results[0]['article_content']);
                $rs_article_user_name   =trim($article_results[0]['name']);
                $rs_article_keyin_mdate =trim($article_results[0]['keyin_mdate']);
                $rs_article_like_cno    =(int)($article_results[0]['article_like_cno']);
                $rs_article_id          =(int)($article_results[0]['article_id']);
                $rs_article_user_id     =(int)($article_results[0]['user_id']);
                $rs_article_user_sex    =(int)($article_results[0]['sex']);

                //特殊處理
                $rs_article_book_name='';
                $arry_book_infos=get_book_info($conn_mssr,$rs_article_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                if(!empty($arry_book_infos)){$rs_article_book_name=trim($arry_book_infos[0]['book_name']);}else{}
                $rs_article_book_img='../img/default/book.png';
                if(file_exists("../../../info/book/{$rs_article_book_sid}/img/front/simg/1.jpg")){
                    $rs_article_book_img="../../../info/book/{$rs_article_book_sid}/img/front/simg/1.jpg";
                }

                $get_book_sid=mysql_prep(trim($rs_article_book_sid));

                $rs_article_img='../img/default/user_boy.png';
                if($rs_article_user_sex===2)$rs_article_img='../img/default/user_girl.png';

                if(isset($file_server_enable)&&($file_server_enable)){
                    $rs_article_img_ftp_root="public_html/mssr/info/user";
                    $rs_article_img_ftp_path="{$rs_article_img_ftp_root}/{$rs_article_user_id}/forum/user_sticker";
                    $arry_rs_article_img_ftp_file=ftp_nlist($ftp_conn,$rs_article_img_ftp_path);
                    if(isset($arry_rs_article_img_ftp_file[0])){
                        $rs_article_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_article_user_id}/forum/user_sticker/1.jpg";
                    }
                }else{
                    if(file_exists("../../../info/user/{$rs_article_user_id}/forum/user_sticker/1.jpg")){
                        $rs_article_img="../../../info/user/{$rs_article_user_id}/forum/user_sticker/1.jpg";
                    }
                }

                //是否為精華文章
                $sql="
                    SELECT
                        `mssr_forum`.`mssr_forum_best_article_category_rev`.`cat_id`,
                        `mssr_forum`.`mssr_forum_best_article_category`.`cat_name`
                    FROM `mssr_forum`.`mssr_forum_best_article_category_rev`
                        INNER JOIN `mssr_forum`.`mssr_forum_best_article_category` ON
                        `mssr_forum`.`mssr_forum_best_article_category_rev`.`cat_id`=`mssr_forum`.`mssr_forum_best_article_category`.`cat_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_best_article_category_rev`.`article_id`={$rs_article_id}
                        AND `mssr_forum`.`mssr_forum_best_article_category`.`cat_state`=1
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                $is_best_article=false;
                if(!empty($db_results)){
                    $is_best_article=true;
                    $rs_best_article_cat_id=(int)$db_results[0]['cat_id'];
                    $rs_best_article_cat_name=trim($db_results[0]['cat_name']);
                }

                $rs_article_content=htmlspecialchars($rs_article_content);
                preg_match_all('/src.*/',$rs_article_content,$arrys_preg_article_content);
                if(!empty($arrys_preg_article_content)&&isset($arrys_preg_article_content[0])&&!empty($arrys_preg_article_content[0])){
                    $arry_preg_article_content=$arrys_preg_article_content[0];
                    foreach($arry_preg_article_content as $preg_article_content){
                        $preg_article_content=trim($preg_article_content);
                        $preg_article_content=str_replace("src=","",$preg_article_content);
                        $preg_article_content=str_replace('&quot;',"",$preg_article_content);
                        $preg_article_content=str_replace('img]',"",$preg_article_content);
                        $preg_article_content=str_replace('audio]',"",$preg_article_content);
                        $file_path=trim($preg_article_content);

                        if(isset($file_server_enable)&&($file_server_enable)){
                            $tmp_ftp_root ="public_html/mssr/info/forum";
                            $tmp_ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                            $tmp_ftp_login=ftp_login($tmp_ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                            ftp_pasv($tmp_ftp_conn,TRUE);
                            $tmp_file_path=str_replace("http://{$arry_ftp1_info['host']}","public_html",$file_path);
                            $arry_ftp_file=ftp_nlist($tmp_ftp_conn,$tmp_file_path);
                            if(!empty($arry_ftp_file)){
                                $file_info=pathinfo($file_path);
                                $extension=(isset($file_info['extension']))?$file_info['extension']:'';
                                if($extension==='jpg'){
                                    $replace="<img src='{$file_path}' border='0' alt='rec_draw' style='max-width:90%;border:0px solid red;margin:0 auto;'>";
                                    $rs_article_content=str_replace("[img src=&quot;{$file_path}&quot; img]",$replace,$rs_article_content);
                                }elseif($extension==='mp3'){
                                    $replace="
                                        <audio controls>
                                            <source src='{$file_path}' type='audio/mpeg'>
                                        </audio>
                                    ";
                                    $rs_article_content=str_replace("[audio src=&quot;{$file_path}&quot; audio]",$replace,$rs_article_content);
                                }else{continue;}
                            }
                        }else{
                            if(file_exists($file_path)){
                                $file_info=pathinfo($file_path);
                                $extension=(isset($file_info['extension']))?$file_info['extension']:'';
                                if($extension==='jpg'){
                                    $replace="<img src='{$file_path}' border='0' alt='rec_draw' style='max-width:90%;border:0px solid red;margin:0 auto;'>";
                                    $rs_article_content=str_replace("[img src=&quot;{$file_path}&quot; img]",$replace,$rs_article_content);
                                }elseif($extension==='mp3'){
                                    $replace="
                                        <audio controls>
                                            <source src='{$file_path}' type='audio/mpeg'>
                                        </audio>
                                    ";
                                    $rs_article_content=str_replace("[audio src=&quot;{$file_path}&quot; audio]",$replace,$rs_article_content);
                                }else{continue;}
                            }
                        }
                    }
                }
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
        //回覆資訊 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `user`.`member`.`name`,

                    `mssr_forum`.`mssr_forum_reply_book_rev`.`book_sid`,

                    `mssr_forum`.`mssr_forum_reply`.`user_id`,
                    `mssr_forum`.`mssr_forum_reply`.`article_id`,
                    `mssr_forum`.`mssr_forum_reply`.`reply_id`,
                    `mssr_forum`.`mssr_forum_reply`.`reply_like_cno`,
                    `mssr_forum`.`mssr_forum_reply`.`reply_report_cno`,
                    `mssr_forum`.`mssr_forum_reply`.`keyin_mdate`,

                    `mssr_forum`.`mssr_forum_reply_detail`.`reply_content`
                FROM `mssr_forum`.`mssr_forum_reply_book_rev`
                    INNER JOIN `mssr_forum`.`mssr_forum_reply` ON
                    `mssr_forum`.`mssr_forum_reply_book_rev`.`reply_id`=`mssr_forum`.`mssr_forum_reply`.`reply_id`

                    INNER JOIN `mssr_forum`.`mssr_forum_reply_detail` ON
                    `mssr_forum`.`mssr_forum_reply`.`reply_id`=`mssr_forum`.`mssr_forum_reply_detail`.`reply_id`

                    INNER JOIN `user`.`member` ON
                    `mssr_forum`.`mssr_forum_reply`.`user_id`=`user`.`member`.`uid`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_reply`.`reply_from` =2 -- 回文來源
                    AND `mssr_forum`.`mssr_forum_reply`.`reply_state`=1 -- 回文狀態
                    AND `mssr_forum`.`mssr_forum_reply`.`group_id`   ={$get_group_id  }
                    AND `mssr_forum`.`mssr_forum_reply`.`article_id` ={$get_article_id}
                ORDER BY `mssr_forum`.`mssr_forum_reply`.`keyin_cdate` ASC
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $db_results_cno=count($db_results);
            $reply_results=array();

            //-------------------------------------------
            //分頁處理
            //-------------------------------------------

                $numrow=$db_results_cno;    //資料總筆數
                $psize =20;                 //單頁筆數,預設20筆
                $pnos  =0;                  //分頁筆數
                $pinx  =1;                  //目前分頁索引,預設1
                $sinx  =0;                  //值域起始值
                $einx  =0;                  //值域終止值

                if(isset($_GET['psize'])){
                    $psize=(int)$_GET['psize'];
                    if($psize===0){
                        $psize=20;
                    }
                }
                if(isset($_GET['pinx'])){
                    $pinx=(int)$_GET['pinx'];
                    if($pinx===0){
                        $pinx=1;
                    }
                }

                $pnos  =ceil($numrow/$psize);
                $pinx  =($pinx>$pnos)?$pnos:$pinx;

                $sinx  =(($pinx-1)*$psize)+1;
                $einx  =(($pinx)*$psize);
                $einx  =($einx>$numrow)?$numrow:$einx;

                if($numrow!==0){
                    $arrys_chunk =array_chunk($db_results,$psize);
                    $reply_results=$arrys_chunk[$pinx-1];
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


            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`,
                    `mssr_forum`.`mssr_forum_group_user_rev`.`user_type`,
                    `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`
                FROM `mssr_forum`.`mssr_forum_group`
                    INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                    `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`   ={$get_group_id}
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state` =1
                ORDER BY `mssr_forum`.`mssr_forum_group_user_rev`.`keyin_cdate` DESC
            ";
            $group_user_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            //提取聊書好友資訊
            $arry_forum_friend =array();
            $arry_forum_friends=get_forum_friend($sess_user_id,$friend_id=0,$arry_conn_mssr);
            if(!empty($arry_forum_friends)){
                foreach($arry_forum_friends as $arry_val){
                    if((int)$arry_val['friend_state']===1){
                        if((int)$arry_val['user_id']!==$sess_user_id)$arry_forum_friend[]=$arry_val['user_id'];
                        if((int)$arry_val['friend_id']!==$sess_user_id)$arry_forum_friend[]=$arry_val['friend_id'];
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
                        window.close();
                    </script>
                ";
                die($jscript_back);
            }

            //聯署建立小組權限
            $auth_create_group=false;

            //申請加入小組權限
            $auth_join_group=false;

            //參與權限
            $auth_participation_group=false;

            //發文權限
            $auth_add_article=false;

            //回文權限
            $auth_add_reply=false;

            //管理權限
            $auth_admin=false;

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
        //按鈕設置
        //-----------------------------------------------

            //收藏文章
            $btn_add_track_article_html=trim('收藏文章');
            $sql="
                SELECT `mssr_forum`.`mssr_forum_track_article`.`user_id`
                FROM  `mssr_forum`.`mssr_forum_track_article`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_track_article`.`user_id`   ={$sess_user_id }
                    AND `mssr_forum`.`mssr_forum_track_article`.`article_id`={$rs_article_id}
                    AND `mssr_forum`.`mssr_forum_track_article`.`group_id`  ={$get_group_id }
            ";
            $track_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(!empty($track_article_results)){
                $btn_add_track_article_html='已收藏文章';
            }

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

            //樓主,加為好友
            $btn_add_friend_show=false;
            $btn_add_friend_html=trim('加為好友');
            if($sess_user_id!==$rs_article_user_id){
                $get_forum_friend=get_forum_friend($sess_user_id,$rs_article_user_id,$arry_conn_mssr);
                if(empty($get_forum_friend)){
                    $btn_add_friend_show=true;
                }else{
                    if((int)$get_forum_friend[0]['friend_state']===2)$btn_add_friend_show=true;
                    if((int)$get_forum_friend[0]['friend_state']===3){$btn_add_friend_show=true;$btn_add_friend_html=trim('好友確認中');}
                }
            }

            //樓主,檢舉
            $btn_report_article_html=trim('檢舉');
            $sql="
                SELECT `mssr_forum`.`mssr_forum_article_report_log`.`user_id`
                FROM  `mssr_forum`.`mssr_forum_article_report_log`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_article_report_log`.`user_id`   ={$sess_user_id }
                    AND `mssr_forum`.`mssr_forum_article_report_log`.`article_id`={$rs_article_id}
            ";
            $report_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($report_article_results)){
                foreach($report_article_results as $report_article_result){
                    //if((int)$report_article_result['user_id']===$sess_user_id)$btn_report_article_html=trim('已檢舉');
                }
            }

            //樓主,讚
            $btn_like_article_html=trim('讚');
            $sql="
                SELECT `mssr_forum`.`mssr_forum_article_like_log`.`user_id`
                FROM  `mssr_forum`.`mssr_forum_article_like_log`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_article_like_log`.`user_id`   ={$sess_user_id }
                    AND `mssr_forum`.`mssr_forum_article_like_log`.`article_id`={$rs_article_id}
            ";
            $like_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $like_article_cno    =count($like_article_results);
            if(!empty($like_article_results)){
                foreach($like_article_results as $like_article_result){
                    if((int)$like_article_result['user_id']===$sess_user_id)$btn_like_article_html=trim('收回讚');
                }
            }
            $btn_like_article_html.="({$like_article_cno})";
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
    <link href="../../../inc/code.css" rel="stylesheet" type="text/css">

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
                    <div style='font-size:12px;'><?php echo $add_article_cno;?> 篇發文          </div>
                    <div style='font-size:12px;'><?php echo $reply_article_cno;?> 篇回覆        </div>
                    <div style='font-size:12px;'><?php echo count($group_user_results);?> 位成員</div>
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
                        <td width="215px" align="center">
                            <!-- 大解析度 -->
                            <button type="button" class="btn_add_track_group btn <?php echo $btn_add_track_group_style;?> btn-xs hidden-xs"
                            style="position:relative;top:0px;"
                            user_id=<?php echo $sess_user_id;?>
                            group_id="<?php echo $get_group_id;?>"
                            title="收藏小組並顯示在個人頁上。"
                            ><?php echo $btn_add_track_group_html;?></button>
                            <!-- 小解析度 -->
                            <button type="button" class="btn_add_track_group btn <?php echo $btn_add_track_group_style;?> btn-xs pull-right hidden-sm hidden-md hidden-lg"
                            style="position:relative;top:3px;"
                            user_id=<?php echo $sess_user_id;?>
                            group_id="<?php echo $get_group_id;?>"
                            title="收藏小組並顯示在個人頁上。"
                            ><?php echo $btn_add_track_group_html;?></button>
                        </td>
                        <td class="hidden-xs" align="center"><!-- <span><?php echo $add_article_cno;?> 篇發文    </span> --></td>
                        <td class="hidden-xs" align="center"><!-- <span><?php echo $reply_article_cno;?> 篇回覆  </span> --></td>
                        <td class="hidden-xs" align="center"><!-- <span><?php echo count($group_user_results);?> 位成員</span> --></td>
                    </tr></tbody>
                </table>
            </div>
            <!-- page_info,end -->

            <!-- group_lefe_side,start -->
            <div class="group_lefe_side col-xs-12 col-sm-10 col-md-10 col-lg-10">

                <ul id="myTab" class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#view_article" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">
                        討論串
                    </a></li>
                    <!-- <li role="presentation"><a href="#reply_article" id="profile-tab" role="tab" data-toggle="tab" aria-controls="profile">
                        回文
                    </a></li> -->
                    <li role="presentation" class="dropdown hidden-sm hidden-md hidden-lg">
                        <a href="#" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown" aria-controls="myTabDrop1-contents">更多&nbsp;<span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="myTabDrop1" id="myTabDrop1-contents">
                            <li><a href="#info" tabindex="-1" role="tab" id="dropdown1-tab" data-toggle="tab" aria-controls="dropdown1">小組簡介</a></li>
                        </ul>
                    </li>
                </ul>
                <div id="myTabContent" class="tab-content">

                    <!-- 觀看文章 -->
                    <div role="tabpanel" class="tab-pane fade in active" id="view_article" aria-labelledBy="home-tab">

                        <!-- 分頁 -->
                        <?php if($numrow!==0):?>
                            <?php echo pagination((int)$pinx,(int)$psize,(int)$pnos,$url="reply.php?get_from=2&article_id={$rs_article_id}");?>
                        <?php endif;?>

                        <!-- 標題 -->
                        <div class="article_title row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <?php if($is_best_article)echo '【精華】';?>
                                <?php echo htmlspecialchars($rs_article_title);?>
                            </div>
                        </div>

                        <!-- 樓主 -->
                        <?php if((int)$pinx===1 || (int)$pinx===0):?>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="media">
                                    <a class="pull-left" href="user.php?user_id=<?php echo $rs_article_user_id;?>&tab=1">
                                        <img class="media-object" src="<?php echo $rs_article_img;?>" alt="Media">
                                    </a>
                                    <h4 class="media-heading">
                                        <?php echo htmlspecialchars($rs_article_user_name);?>

                                        <!-- 功能鈕,大解析度,start -->
                                        <?php if($auth_admin):?>
                                            <div class="btn-group pull-right hidden-xs" style="position:relative;top:0px;margin-left:2px;">
                                                <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                                    data-toggle="dropdown">
                                                    加入精華文 <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <?php
                                                    if(!empty($best_article_category_results)){
                                                        foreach($best_article_category_results as $best_article_category_result):
                                                            $rs_cat_id  =(int)($best_article_category_result['cat_id']);
                                                            $rs_cat_name=trim($best_article_category_result['cat_name']);
                                                    ?>
                                                    <li><a href="javascript:add_best_article(<?php echo $rs_article_id;?>,<?php echo $rs_cat_id;?>);void(0);">
                                                        <?php echo htmlspecialchars($rs_cat_name);?>
                                                    </a></li>
                                                    <?php endforeach;}?>
                                                    <li class="divider"></li>
                                                    <li><a href="javascript:del_best_article(<?php echo $rs_article_id;?>);void(0);">
                                                        移出精華文
                                                    </a></li>
                                                </ul>
                                            </div>
                                        <?php endif;?>

                                        <?php if($sess_user_id===$rs_article_user_id):?>
                                            <button type="button" class="btn_edit_article btn btn-default btn-xs pull-right hidden-xs"
                                            article_id=<?php echo $rs_article_id;?>
                                            onclick="location.href='forum.php?method=edit_article&article_id=<?php echo $rs_article_id;?>&pinx=<?php echo $pinx;?>&psize=<?php echo $psize;?>';"
                                            >編輯</button>
                                        <?php endif?>

                                        <?php if($auth_add_article && $auth_add_reply):?>
                                            <button type="button" class="btn_request_article btn btn-default btn-xs pull-right hidden-xs"
                                            data-toggle="modal" data-target=".modal_request_article">邀請好友一同聊書</button>
                                        <?php endif;?>

                                        <?php if(in_array('i_t',$arry_user_status)||in_array('i_a',$arry_user_status)):?>
                                            <button type="button" class="btn_del_article btn btn-default btn-xs pull-right hidden-xs"
                                            article_id=<?php echo $rs_article_id;?>>移除文章</button>
                                        <?php endif?>

                                        <?php if($btn_add_friend_show):?>
                                            <button type="button" class="btn_add_friend btn btn-default btn-xs pull-right hidden-xs"
                                            user_id=<?php echo $sess_user_id;?>
                                            friend_id=<?php echo $rs_article_user_id;?>><?php echo $btn_add_friend_html;?></button>
                                        <?php endif;?>

                                        <?php if($auth_add_article && $auth_add_reply):?>
                                            <button type="button" class="btn_add_track_article btn btn-default btn-xs pull-right hidden-xs"
                                            user_id=<?php echo $sess_user_id;?>
                                            article_id=<?php echo $rs_article_id;?>
                                            group_id=<?php echo $get_group_id;?>>
                                            <?php echo $btn_add_track_article_html;?></button>
                                            <button type="button" class="btn_report_article btn btn-default btn-xs pull-right hidden-xs"
                                            user_id=<?php echo $sess_user_id;?>
                                            article_id=<?php echo $rs_article_id;?>
                                            reply_id=0><?php echo $btn_report_article_html;?></button>
                                            <button type="button" class="btn_like_article btn btn-default btn-xs pull-right hidden-xs"
                                            user_id=<?php echo $sess_user_id;?>
                                            article_id=<?php echo $rs_article_id;?>
                                            reply_id=0><?php echo $btn_like_article_html;?></button>
                                        <?php endif;?>
                                        <!-- 功能鈕,大解析度,end -->

                                        <!-- 功能鈕,小解析度,start -->
                                        <div class="btn-group pull-right hidden-sm hidden-md hidden-lg">
                                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                功能&nbsp;<span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <?php if($auth_add_article && $auth_add_reply):?>
                                                    <li><a href="#" class="btn_request_article"
                                                    data-toggle="modal" data-target=".modal_request_article">邀請好友一同聊書</a></li>
                                                <?php endif;?>

                                                <?php if(in_array('i_t',$arry_user_status)||in_array('i_a',$arry_user_status)):?>
                                                    <li><a href="javascript:void(0);" class="btn_del_article"
                                                    article_id=<?php echo $rs_article_id;?>>移除文章</a></li>
                                                <?php endif?>

                                                <?php if($btn_add_friend_show):?>
                                                    <li><a href="javascript:void(0);" class="btn_add_friend"
                                                    user_id=<?php echo $sess_user_id;?>
                                                    friend_id=<?php echo $rs_article_user_id;?>><?php echo $btn_add_friend_html;?></a></li>
                                                <?php endif;?>

                                                <?php if($auth_add_article && $auth_add_reply):?>
                                                    <li><a href="javascript:void(0);" class="btn_add_track_article"
                                                    user_id=<?php echo $sess_user_id;?>
                                                    article_id=<?php echo $rs_article_id;?>
                                                    group_id=<?php echo $get_group_id;?>><?php echo $btn_add_track_article_html;?></a></li>
                                                    <li><a href="javascript:void(0);" class="btn_report_article"
                                                    user_id=<?php echo $sess_user_id;?>
                                                    article_id=<?php echo $rs_article_id;?>
                                                    reply_id=0><?php echo $btn_report_article_html;?></a></li>
                                                    <li><a href="javascript:void(0);" class="btn_like_article"
                                                    user_id=<?php echo $sess_user_id;?>
                                                    article_id=<?php echo $rs_article_id;?>
                                                    reply_id=0><?php echo $btn_like_article_html;?></a></li>
                                                <?php endif;?>

                                                <?php if($sess_user_id===$rs_article_user_id):?>
                                                    <li><a href="javascript:void(0);" class="btn_edit_article"
                                                    article_id=<?php echo $rs_article_id;?>
                                                    onclick="location.href='forum.php?method=edit_article&article_id=<?php echo $rs_article_id;?>&pinx=<?php echo $pinx;?>&psize=<?php echo $psize;?>';"
                                                    >編輯</a></li>
                                                <?php endif?>
                                            </ul>
                                        </div>
                                        <!-- 功能鈕,小解析度,end -->

                                        <div><?php echo htmlspecialchars($rs_article_keyin_mdate);?>&nbsp;#1</div>
                                        <div class="hidden-xs" style="position:relative;top:17px;">文章編號：<?php echo htmlspecialchars($rs_article_id);?></div>
                                    </h4>
                                    <div class="pull-right media-body">
                                        <a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_article_book_sid);?>">
                                            <img width="40" height="40" src="<?php echo $rs_article_book_img;?>" border="0">
                                            <?php echo htmlspecialchars($rs_article_book_name);?>
                                        </a>
                                    </div>
                                    <div class="pull-left media-body">
                                        <?php echo nl2br(($rs_article_content));?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif;?>

                        <!-- 各樓層 -->
                        <?php if(!empty($reply_results)){
                            foreach($reply_results as $inx=>$reply_result):
                                $rs_reply_book_sid      =trim($reply_result['book_sid']);
                                $rs_reply_user_name     =trim($reply_result['name']);
                                $rs_reply_keyin_mdate   =trim($reply_result['keyin_mdate']);
                                $rs_reply_like_cno      =(int)($reply_result['reply_like_cno']);
                                $rs_article_id          =(int)($reply_result['article_id']);
                                $rs_reply_id            =(int)($reply_result['reply_id']);
                                $rs_reply_user_id       =(int)($reply_result['user_id']);
                                $rs_reply_content       =trim($reply_result['reply_content']);
                                $rs_reply_img           ='../img/default/user_boy.png';

                                //特殊處理
                                $sql="
                                    SELECT `sex`
                                    FROM `user`.`member`
                                    WHERE 1=1
                                        AND `user`.`member`.`uid`={$rs_reply_user_id}
                                ";
                                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                $rs_reply_user_sex  =1;
                                if(!empty($db_results)){
                                    $rs_reply_user_sex =(int)$db_results[0]['sex'];
                                    if($rs_reply_user_sex===1)$rs_reply_img='../img/default/user_boy.png';
                                    if($rs_reply_user_sex===2)$rs_reply_img='../img/default/user_girl.png';

                                    if(isset($file_server_enable)&&($file_server_enable)){
                                        $rs_reply_img_ftp_root="public_html/mssr/info/user";
                                        $rs_reply_img_ftp_path="{$rs_reply_img_ftp_root}/{$rs_reply_user_id}/forum/user_sticker";
                                        $arry_rs_reply_img_ftp_file=ftp_nlist($ftp_conn,$rs_reply_img_ftp_path);
                                        if(isset($arry_rs_reply_img_ftp_file[0])){
                                            $rs_reply_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_reply_user_id}/forum/user_sticker/1.jpg";
                                        }
                                    }else{
                                        if(file_exists("../../../info/user/{$rs_reply_user_id}/forum/user_sticker/1.jpg")){
                                            $rs_reply_img="../../../info/user/{$rs_reply_user_id}/forum/user_sticker/1.jpg";
                                        }
                                    }
                                }else{die();}

                                $rs_reply_book_name='';
                                $arry_book_infos=get_book_info($conn_mssr,$rs_reply_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                if(!empty($arry_book_infos)){$rs_reply_book_name=trim($arry_book_infos[0]['book_name']);}else{continue;}
                                $rs_reply_book_img='../img/default/book.png';
                                if(file_exists("../../../info/book/{$rs_reply_book_sid}/img/front/simg/1.jpg")){
                                    $rs_reply_book_img="../../../info/book/{$rs_reply_book_sid}/img/front/simg/1.jpg";
                                }

                                //各樓層,加為好友
                                $btn_add_friend_show=false;
                                $btn_add_friend_html=trim('加為好友');
                                if($sess_user_id!==$rs_reply_user_id){
                                    $get_forum_friend=get_forum_friend($sess_user_id,$rs_reply_user_id,$arry_conn_mssr);
                                    if(empty($get_forum_friend)){
                                        $btn_add_friend_show=true;
                                    }else{
                                        if((int)$get_forum_friend[0]['friend_state']===2)$btn_add_friend_show=true;
                                        if((int)$get_forum_friend[0]['friend_state']===3){$btn_add_friend_show=true;$btn_add_friend_html=trim('好友確認中');}
                                    }
                                }

                                //各樓層,檢舉
                                $btn_report_reply_html=trim('檢舉');
                                $sql="
                                    SELECT `mssr_forum`.`mssr_forum_reply_report_log`.`user_id`
                                    FROM  `mssr_forum`.`mssr_forum_reply_report_log`
                                    WHERE 1=1
                                        AND `mssr_forum`.`mssr_forum_reply_report_log`.`user_id` ={$sess_user_id }
                                        AND `mssr_forum`.`mssr_forum_reply_report_log`.`reply_id`={$rs_reply_id  }
                                ";
                                $report_reply_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                if(!empty($report_reply_results)){
                                    foreach($report_reply_results as $report_reply_result){
                                        //if((int)$report_reply_result['user_id']===$sess_user_id)$btn_report_reply_html=trim('已檢舉');
                                    }
                                }

                                //各樓層,讚
                                $btn_like_reply_html=trim('讚');
                                $sql="
                                    SELECT `mssr_forum`.`mssr_forum_reply_like_log`.`user_id`
                                    FROM  `mssr_forum`.`mssr_forum_reply_like_log`
                                    WHERE 1=1
                                        AND `mssr_forum`.`mssr_forum_reply_like_log`.`user_id` ={$sess_user_id }
                                        AND `mssr_forum`.`mssr_forum_reply_like_log`.`reply_id`={$rs_reply_id  }
                                ";
                                $like_reply_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                $like_reply_cno    =count($like_reply_results);
                                if(!empty($like_reply_results)){
                                    foreach($like_reply_results as $like_reply_result){
                                        if((int)$like_reply_result['user_id']===$sess_user_id)$btn_like_reply_html=trim('收回讚');
                                    }
                                }
                                $btn_like_reply_html.="({$like_reply_cno})";
                        ?>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="media">
                                    <a class="pull-left" href="user.php?user_id=<?php echo $rs_reply_user_id;?>&tab=1">
                                        <img class="media-object" src="<?php echo $rs_reply_img;?>" alt="Media">
                                    </a>
                                    <h4 class="media-heading">
                                        <?php echo htmlspecialchars($rs_reply_user_name);?>

                                        <!-- 功能鈕,大解析度,start -->
                                        <?php if($btn_add_friend_show):?>
                                            <button type="button" class="btn_add_friend btn btn-default btn-xs pull-right hidden-xs"
                                            user_id=<?php echo $sess_user_id;?>
                                            friend_id=<?php echo $rs_reply_user_id;?>><?php echo $btn_add_friend_html;?></button>
                                        <?php endif;?>

                                        <?php if($sess_user_id===$rs_reply_user_id):?>
                                            <button type="button" class="btn_edit_reply btn btn-default btn-xs pull-right hidden-xs"
                                            reply_id=<?php echo $rs_reply_id;?>
                                            onclick="location.href='forum.php?method=edit_reply&reply_id=<?php echo $rs_reply_id;?>&pinx=<?php echo $pinx;?>&psize=<?php echo $psize;?>';"
                                            >編輯</button>
                                        <?php endif?>

                                        <?php if(in_array('i_t',$arry_user_status)||in_array('i_a',$arry_user_status)):?>
                                            <button type="button" class="btn_del_reply btn btn-default btn-xs pull-right hidden-xs"
                                            reply_id=<?php echo $rs_reply_id;?>>移除回覆</button>
                                        <?php endif?>

                                        <?php if($auth_add_article && $auth_add_reply):?>
                                            <button type="button" class="btn_report_article btn btn-default btn-xs pull-right hidden-xs"
                                            user_id=<?php echo $sess_user_id;?>
                                            article_id=<?php echo $rs_article_id;?>
                                            reply_id=<?php echo $rs_reply_id;?>><?php echo $btn_report_reply_html;?></button>
                                            <button type="button" class="btn_like_article btn btn-default btn-xs pull-right hidden-xs"
                                            user_id=<?php echo $sess_user_id;?>
                                            article_id=<?php echo $rs_article_id;?>
                                            reply_id=<?php echo $rs_reply_id;?>><?php echo $btn_like_reply_html;?></button>
                                        <?php endif;?>
                                        <!-- 功能鈕,大解析度,end -->

                                        <!-- 功能鈕,小解析度,start -->
                                        <div class="btn-group pull-right hidden-sm hidden-md hidden-lg">
                                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                功能&nbsp;<span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <?php if($btn_add_friend_show):?>
                                                    <li><a href="javascript:void(0);" class="btn_add_friend"
                                                    user_id=<?php echo $sess_user_id;?>
                                                    friend_id=<?php echo $rs_reply_user_id;?>><?php echo $btn_add_friend_html;?></a></li>
                                                <?php endif;?>

                                                <?php if(in_array('i_t',$arry_user_status)||in_array('i_a',$arry_user_status)):?>
                                                    <li><a href="javascript:void(0);" class="btn_del_reply"
                                                    reply_id=<?php echo $rs_reply_id;?>>移除回覆</a></li>
                                                <?php endif?>

                                                <?php if($auth_add_article && $auth_add_reply):?>
                                                    <li><a href="javascript:void(0);" class="btn_report_article"
                                                    user_id=<?php echo $sess_user_id;?>
                                                    article_id=<?php echo $rs_article_id;?>
                                                    reply_id=<?php echo $rs_reply_id;?>><?php echo $btn_report_reply_html;?></a></li>
                                                    <li><a href="javascript:void(0);" class="btn_like_article"
                                                    user_id=<?php echo $sess_user_id;?>
                                                    article_id=<?php echo $rs_article_id;?>
                                                    reply_id=<?php echo $rs_reply_id;?>><?php echo $btn_like_reply_html;?></a></li>
                                                <?php endif;?>

                                                <?php if($sess_user_id===$rs_reply_user_id):?>
                                                    <li><a href="javascript:void(0);" class="btn_edit_reply"
                                                    reply_id=<?php echo $rs_reply_id;?>
                                                    onclick="location.href='forum.php?method=edit_reply&reply_id=<?php echo $rs_reply_id;?>&pinx=<?php echo $pinx;?>&psize=<?php echo $psize;?>';"
                                                    >編輯</a></li>
                                                <?php endif?>
                                            </ul>
                                        </div>
                                        <!-- 功能鈕,小解析度,end -->

                                        <div><?php echo htmlspecialchars($rs_reply_keyin_mdate);?>&nbsp;#<?php echo ($pinx*$psize)-$psize+2;?></div>
                                    </h4>
                                    <div class="pull-right media-body">
                                        <a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_reply_book_sid);?>">
                                            <img width="40" height="40" src="<?php echo $rs_reply_book_img;?>" border="0">
                                            <?php echo htmlspecialchars($rs_reply_book_name);?>
                                        </a>
                                    </div>
                                    <div class="pull-left media-body">
                                        <?php echo nl2br(htmlspecialchars($rs_reply_content));?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach;}?>

                        <!-- 分頁 -->
                        <?php if($numrow!==0):?>
                            <?php echo pagination((int)$pinx,(int)$psize,(int)$pnos,$url="reply.php?get_from=2&article_id={$rs_article_id}");?>
                        <?php endif;?>

                        <!-- 回文 -->
                        <div id="reply_article_row" class="row">
                            <div id="reply_article" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <!-- <pre class="text-center" style="background-color:#428bca;color:#ffffff;">回覆文章</pre> -->
                                <h4 style="border-bottom:1px solid #428bca;margin-top:10px;padding:10px 0;color:#428bca;">發表回覆</h4>
                                <?php if($auth_add_reply):?>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
                                            <select class="form-control" onchange="reply_eagle();void(0);">
                                                <option disabled="disabled" selected>請選擇類型來開始回覆文章 </option>
                                                <option value="1">我覺得你說的很好，但我還想補充……            </option>
                                                <option value="2">在……的部分，我跟你的想法不一樣，因為……      </option>
                                                <option value="0">其他                                        </option>
                                            </select>
                                        </div>
                                    </div>
                                <?php else:?>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
                                            您尚未加入此小組...... 請按上方的<span style='color:#4298ce;'>【加入小組】</span>按鈕
                                        </div>
                                    </div>
                                <?php endif;?>
                            </div>
                        </div>

                    </div>

                    <!-- 回文 -->
                    <!-- <div role="tabpanel" class="tab-pane fade" id="reply_article" aria-labelledBy="home-tab">
                        <?php if($auth_add_reply):?>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    主題：&nbsp;<?php echo htmlspecialchars($rs_article_title);?><hr></hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
                                    <select class="form-control" onchange="reply_eagle();void(0);">
                                        <option disabled="disabled" selected>請選擇類型來開始回文 </option>
                                        <option value="1">我覺得你說的很好，但我還想補充……            </option>
                                        <option value="2">在……的部分，我跟你的想法不一樣，因為……      </option>
                                        <option value="0">其他                                        </option>
                                    </select>
                                </div>
                            </div>
                        <?php else:?>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
                                    您尚未加入此小組...... 請按上方的<span style='color:#4298ce;'>【加入小組】</span>按鈕
                                </div>
                            </div>
                        <?php endif;?>
                    </div> -->

                    <!-- 簡介 -->
                    <div role="tabpanel" class="tab-pane fade" id="info" aria-labelledBy="profile-tab">
                        <div class="group_lefe_side_tab4 row">
                            <div class="modal_jumbotron_note">
                                <span>
                                    <?php echo htmlspecialchars($group_content);?>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <!-- group_lefe_side,end -->

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

    <!-- modal_request_article,start -->
    <?php echo $modal_dialog_2;?>
    <!-- modal_request_article,end -->

</body>

<!-- 通用 -->
<script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/ui/code.js"></script>
<script type="text/javascript" src="../../../lib/js/string/code.js"></script>
<script type="text/javascript" src="../../../lib/js/array/code.js"></script>
<script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>
<script type="text/javascript" src="../../../inc/code.js"></script>

<!-- 專屬 -->
<script type="text/javascript" src="../inc/code.js"></script>

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;
    var get_from=<?php echo $get_from;?>;
    var get_article_id=parseInt(<?php echo $get_article_id;?>);
    var get_group_id=parseInt(<?php echo (int)$get_group_id;?>);
    var sess_user_id=parseInt(<?php echo $sess_user_id;?>);
    var style_id=parseInt(<?php echo $style_id;?>);
    var style_from=parseInt(<?php echo $style_from;?>);
    var send_url=document.URL;
    var arry_friend_info={};
    var arry_friend_name=[];

    <?php
    if(!empty($arry_forum_friend)){
        foreach($arry_forum_friend as $arry_val):
            $rs_friend_id=(int)$arry_val;
            $sql="
                SELECT `name`
                FROM `user`.`member`
                WHERE 1=1
                    AND `user`.`member`.`uid`={$rs_friend_id}
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(!empty($db_results)){
                $rs_user_name=htmlspecialchars(trim($db_results[0]['name']));
            }else{continue;}
    ?>
        arry_friend_info['<?php echo $rs_user_name;?>']=<?php echo $rs_friend_id;?>;
        arry_friend_name.push('<?php echo $rs_user_name;?>');
    <?php endforeach;}?>


    //OBJ
    var article_eagle_content=<?php echo json_encode($article_eagle_content,true);?>;
    var article_eagle_code   =<?php echo json_encode($article_eagle_code,true);?>;
    var arry_my_borrow       =<?php echo json_encode($arry_group_booklist,true);?>;


    //FUNCTION
    function chk_request_article(obj,friend_id){
    //邀請好友一同聊書選取特效

        var friend_id=parseInt(friend_id);
        if(obj.checked===true){
            $('.div_request_article_'+friend_id).css("background-color","#ffffdd");
        }else{
            $('.div_request_article_'+friend_id).css("background-color","#ffffff");
        }
    }

    //$('.request_article_friend_name').keypress(function(e){
    ////自動選取
    //
    //    if(e.which==13){
    //        if(in_array($.trim($(this).val()),arry_friend_name)){
    //            $('.chk_request_article_'+parseInt(arry_friend_info[$.trim($(this).val())]))[0].checked=true;
    //            $('.div_request_article_'+parseInt(arry_friend_info[$.trim($(this).val())])).css("background-color","#ffffdd");
    //            $(this).val('');
    //        }else{
    //            alert('請輸入正確的好友名稱');
    //        }
    //    }
    //});
    function auto(obj,no){
    //選單貼上

        var request_article_friend_name   =trim($(obj).text());
        var no                            =parseInt(no);
        //var orequest_article_friend_id    =document.getElementsByName('request_article_friend_id')[no];
        var orequest_article_friend_name  =document.getElementsByName('request_article_friend_name[]')[no];
        //orequest_article_friend_id.value  =parseInt(arry_friend_info[request_article_friend_name]);
        orequest_article_friend_name.value=trim(request_article_friend_name);
    }
    $('.btn_request_article').click(function(){
        $('.request_article_friend_name').bind("keydown.autocomplete",function(){
            source: arry_friend_name
        });
    });
    $('.request_article_friend_name').autocomplete({
        source: arry_friend_name
    });
    function clone_request_article_tag(){
        $('.request_article_friend_name').autocomplete('destroy');
        $('.request_article_group:last').after($('.request_article_group').eq(0).clone(true));
        $('.request_article_group').find("A").each(function(){
            $(this).replaceWith("<a href='javascript:void(0);'>"+trim($(this).text())+"</a>");
        });
        $('.request_article_group').each(function(){
            var $request_article_group=$(this);
            $request_article_group.find("A").click(function(){
                auto($(this)[0],parseInt($request_article_group.index()-3));
            });
        });
        $('.request_article_group').eq($('.request_article_group').length-1).find("INPUT").val('').focus();
        $('.request_article_friend_name').autocomplete({
            source: arry_friend_name
        });
    }
    function del_request_article_tag(){
        if(parseInt($('.request_article_group').length)>1){
            $('.request_article_group').eq($('.request_article_group').length-1).remove();
        }
        $('.request_article_group').eq($('.request_article_group').length-1).find("INPUT").focus();
    }

    function del_best_article(article_id){
    //移出精華文

        var article_id=parseInt(article_id);

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
                article_id  :encodeURI(trim(article_id          )),
                method      :encodeURI(trim('del_best_article'  )),
                send_url    :encodeURI(trim(send_url            ))
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
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function add_best_article(article_id,cat_id){
    //加入精華文

        var article_id=parseInt(article_id);
        var cat_id    =parseInt(cat_id);

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
                article_id  :encodeURI(trim(article_id          )),
                cat_id      :encodeURI(trim(cat_id              )),
                method      :encodeURI(trim('add_best_article'  )),
                send_url    :encodeURI(trim(send_url            ))
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
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    $('.btn_del_reply').click(function(){
    //移除回覆

        if(!confirm('你確定要移除回覆嗎?')){
            return false;
        }

        var reply_id  =parseInt($(this).attr('reply_id'));

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
                reply_id  :encodeURI(trim(reply_id      )),
                method      :encodeURI(trim('del_reply' )),
                send_url    :encodeURI(trim(send_url    ))
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
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    });

    $('.btn_del_article').click(function(){
    //移除文章

        if(!confirm('你確定要移除文章嗎?')){
            return false;
        }

        var article_id  =parseInt($(this).attr('article_id'));

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
                article_id  :encodeURI(trim(article_id      )),
                method      :encodeURI(trim('del_article'   )),
                send_url    :encodeURI(trim(send_url        ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                location.href='article.php?get_from=2&group_id='+get_group_id;
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

    $('.btn_add_track_article').click(function(){
    //追蹤文章

        var user_id     =parseInt($(this).attr('user_id'));
        var article_id  =parseInt($(this).attr('article_id'));
        var group_id    =parseInt($(this).attr('group_id'));

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
                article_id  :encodeURI(trim(article_id          )),
                group_id    :encodeURI(trim(group_id            )),
                method      :encodeURI(trim('add_track_article' )),
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
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    });

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

    $('#Btn_add_request_article').click(function(){
    //邀請好友一同聊書

        var oForm1=$('.modal_request_article').find('#Form1')[0];
        var orequest_article_friend_id   =document.getElementById('request_article_friend_id');
        var orequest_article_friend_names=document.getElementsByName('request_article_friend_name[]');
        var success_flag=true;
        var arry_has_sel=[];

        $(orequest_article_friend_names).each(function(){
            var orequest_article_friend_name=$(this)[0];
            if(trim(orequest_article_friend_name.value)===''){
                alert('請選擇一位好友');
                orequest_article_friend_name.focus();
                success_flag=false;
                return false;
            }else{
                if(!in_array(trim(orequest_article_friend_name.value),arry_friend_name)){
                    alert('請選擇或輸入正確的好友');
                    orequest_article_friend_name.focus();
                    success_flag=false;
                    return false;
                }
                if(!in_array(trim(orequest_article_friend_name.value),arry_has_sel)){
                    arry_has_sel.push(trim(orequest_article_friend_name.value));
                }else{
                    alert('請選擇不同的好友');
                    orequest_article_friend_name.focus();
                    success_flag=false;
                    return false;
                }
            }
        });
        if(!success_flag)return false;
        if(confirm('你確定要送出嗎 ?')){
            oForm1.action='../controller/add.php'
            oForm1.submit();
            return true;
        }else{
            return false;
        }

        //var $request_article_friend_ids=$('.request_article_friend_id');
        //var cno=0;
        //for(var i=0;i<$request_article_friend_ids.length;i++){
        //    var $request_article_friend_id=$request_article_friend_ids.eq(i);
        //    if($request_article_friend_id[0].checked===true){
        //        cno++;
        //    }
        //}
        //if(cno===0){
        //    alert('請至少選擇一位好友');
        //    return false;
        //}else{
        //    for(var i=0;i<$request_article_friend_ids.length;i++){
        //        var $request_article_friend_id=$request_article_friend_ids.eq(i);
        //        if($request_article_friend_id[0].checked===true){
        //            if(confirm('你確定要送出嗎 ?')){
        //                oForm1.action='../controller/add.php'
        //                oForm1.submit();
        //                return true;
        //            }else{
        //                return false;
        //            }
        //        }
        //    }
        //}
    });

    $('.btn_report_article').click(function(){
    //按文章檢舉

        var user_id     =parseInt($(this).attr('user_id'));
        var article_id  =parseInt($(this).attr('article_id'));
        var reply_id    =parseInt($(this).attr('reply_id'));

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
                article_id  :encodeURI(trim(article_id          )),
                reply_id    :encodeURI(trim(reply_id            )),
                method      :encodeURI(trim('add_report_article')),
                send_url    :encodeURI(trim(send_url            ))
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

    $('.btn_like_article').click(function(){
    //按文章讚

        var user_id     =parseInt($(this).attr('user_id'));
        var article_id  =parseInt($(this).attr('article_id'));
        var reply_id    =parseInt($(this).attr('reply_id'));

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
                article_id  :encodeURI(trim(article_id          )),
                reply_id    :encodeURI(trim(reply_id            )),
                method      :encodeURI(trim('add_like_article'  )),
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

    $('.btn_add_friend').click(function(){
    //加為好友

        var user_id  =parseInt($(this).attr('user_id'));
        var friend_id=parseInt($(this).attr('friend_id'));

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
                user_id     :encodeURI(trim(user_id         )),
                friend_id   :encodeURI(trim(friend_id       )),
                method      :encodeURI(trim('add_friend'    )),
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

    function Btn_add_article(){
    //發文

        var oForm1              =$('#add_article').find('#Form1')[0];
        var osend_chk           =$('#add_article').find('#send_chk')[0];
        var obook_sid           =$('#add_article').find('#book_sid')[0];
        var oarticle_title      =$('#add_article').find('#article_title')[0];
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

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要送出嗎 ?')){
                oForm1.action='../controller/add.php'
                oForm1.submit();
                return true;
            }else{
                return false;
            }
        }
    }

    function Btn_reply_article(){
    //回文

        var oForm1              =$('#reply_article').find('#Form1')[0];
        var osend_chk           =$('#reply_article').find('#send_chk')[0];
        var obook_sid           =$('#reply_article').find('#book_sid')[0];
        var oreply_contents     =document.getElementsByName('reply_content[]');
        var reply_content_err   =0;
        var arry_err            =[];

        if(trim(obook_sid.value)===''){
            arry_err.push('請選擇一本書來發文');
        }
        if(oreply_contents!==undefined && oreply_contents.length!==0){
            for(var i=0;i<oreply_contents.length;i++){
                oreply_content=oreply_contents[i];
                var placeholder=trim(oreply_content.getAttribute('placeholder'));
                if(trim(oreply_content.value)==='' || trim(oreply_content.value)===trim(placeholder)){
                    //arry_err.push('請輸入文章內容 '+(i+1));
                    reply_content_err++;
                }
            }
        }else{
            arry_err.push('文章內容框錯誤');
        }
        if(parseInt(oreply_contents.length)===parseInt(reply_content_err)){
            arry_err.push('請輸入文章內容');
        }
        if(!osend_chk.checked){
            arry_err.push('請閱讀並勾選同意討論區規則');
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要送出嗎 ?')){
                oForm1.action='../controller/add.php'
                oForm1.submit();
                return true;
            }else{
                return false;
            }
        }
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


    //ONLOAD
    $(function(){
        //讀取側邊欄
        load_right_side(trim('group'));
        //滾動監聽
        $(window).scroll(function(){
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
    })

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
?>












