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
            APP_ROOT.'service/_dev_forum_eric_mission/inc/code',
            APP_ROOT.'service/_dev_forum_eric_mission/view/Eric/fun_ex_sentence',

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
    //接收,設定參數
    //---------------------------------------------------

        $get_from         =(isset($_GET['get_from']))?(int)$_GET['get_from']:0;
        $get_group_task_id=(isset($_GET['group_task_id']))?(int)($_GET['group_task_id']):0;
        $get_article_id   =(isset($_GET['article_id']))?(int)($_GET['article_id']):0;
        $tab              =(isset($_GET['tab']))?(int)$_GET['tab']:1;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

        $arry_err=array();

        if($get_from===0){
            $arry_err[]='組態,錯誤!';
        }else{
            if($get_from===3){
                if($get_group_task_id===0){
                    $arry_err[]='推播任務編號,錯誤!';
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

        //註腳列
        $footbar=footbar($rd=1);

        //modal
        $modal_bookstore_rec=modal_bookstore_rec($rd=1);

        //載入內容
        if($get_article_id===0){
            //任務主頁
            page_article($title);
        }else{
            //任務回覆頁
            page_reply($title);
        }
?>


<?php function page_reply($title="") {?>
<?php
//-------------------------------------------------------
//page_reply 區塊 -- 開始
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
        global $get_group_task_id;
        global $get_article_id;
        global $tab;
        global $modal_bookstore_rec;

        global $conn_mssr;
        global $arry_conn_mssr;

        global $meta;
        global $navbar;
        global $carousel;
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

        $get_group_id=0;
        $request_article=(isset($_GET['request_article']))?(int)$_GET['request_article']:0;

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
        //任務資訊 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_forum`.`dev_group_mission`.`group_task_id`,
                    `mssr_forum`.`dev_group_mission`.`gask_topic`,
                    `mssr_forum`.`dev_group_mission`.`create_time`
                FROM `mssr_forum`.`dev_group_mission`
                WHERE 1=1
                    AND `dev_group_mission`.`group_task_id`={$get_group_task_id}
            ";
            $group_mission_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(empty($group_mission_results)){
                die('推播任務編號,錯誤!');
            }else{
                $gask_topic=trim($group_mission_results[0]['gask_topic']);
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
                    `mssr_forum`.`mssr_forum_article_detail`.`article_content`,

                    IFNULL((
                        SELECT `user`.`school`.`school_name`
                        FROM `user`.`member_school`
                            INNER JOIN `user`.`school` ON
                            `user`.`member_school`.`school_code`=`user`.`school`.`school_code`
                        WHERE 1=1
                            AND `user`.`member_school`.`end`='0000-00-00'
                            AND `user`.`member_school`.`uid`=`mssr_forum`.`mssr_forum_article`.`user_id`
                        LIMIT 1
                    ),'') AS `school_name`
                FROM `mssr_forum`.`mssr_forum_article_book_rev`
                    INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                    `mssr_forum`.`mssr_forum_article_book_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`

                    INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                    `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                    INNER JOIN `user`.`member` ON
                    `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_article`.`article_from` =3 -- 文章來源
                    AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                    AND `mssr_forum`.`mssr_forum_article`.`article_id`   ={$get_article_id}
            ";
            $article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(empty($article_results)){
                @header("Location:user.php?user_id={$sess_user_id}&tab=1");
                die();
            }else{
                $rs_article_school_name =trim($article_results[0]['school_name']);
                $rs_article_book_sid    =trim($article_results[0]['book_sid']);
                $rs_article_title       =trim($article_results[0]['article_title']);
                $rs_article_content     =trim($article_results[0]['article_content']);
                $rs_article_user_name   =trim($article_results[0]['name']);
                $rs_article_keyin_mdate =trim($article_results[0]['keyin_mdate']);
                $rs_article_like_cno    =(int)($article_results[0]['article_like_cno']);
                $rs_article_id          =(int)($article_results[0]['article_id']);
                $rs_article_user_id     =(int)($article_results[0]['user_id']);
                $rs_article_user_sex    =(int)($article_results[0]['sex']);
                $arry_article_user_title=get_member_title($rs_article_user_id,$arry_conn_mssr);
                $rs_article_user_title  =(isset($arry_article_user_title[0]['title_name']))?trim("- ".$arry_article_user_title[0]['title_name']):'';

                $arry_book_infos=get_book_info($conn_mssr,$rs_article_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                if(!empty($arry_book_infos)){
                    $rs_book_name=trim($arry_book_infos[0]['book_name']);
                    $arry_my_borrow[$rs_article_book_sid]=$rs_book_name;
                }

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

                    `mssr_forum`.`mssr_forum_reply_detail`.`reply_content`,

                    IFNULL((
                        SELECT `user`.`school`.`school_name`
                        FROM `user`.`member_school`
                            INNER JOIN `user`.`school` ON
                            `user`.`member_school`.`school_code`=`user`.`school`.`school_code`
                        WHERE 1=1
                            AND `user`.`member_school`.`end`='0000-00-00'
                            AND `user`.`member_school`.`uid`=`mssr_forum`.`mssr_forum_reply`.`user_id`
                        LIMIT 1
                    ),'') AS `school_name`
                FROM `mssr_forum`.`mssr_forum_reply_book_rev`
                    INNER JOIN `mssr_forum`.`mssr_forum_reply` ON
                    `mssr_forum`.`mssr_forum_reply_book_rev`.`reply_id`=`mssr_forum`.`mssr_forum_reply`.`reply_id`

                    INNER JOIN `mssr_forum`.`mssr_forum_reply_detail` ON
                    `mssr_forum`.`mssr_forum_reply`.`reply_id`=`mssr_forum`.`mssr_forum_reply_detail`.`reply_id`

                    INNER JOIN `user`.`member` ON
                    `mssr_forum`.`mssr_forum_reply`.`user_id`=`user`.`member`.`uid`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_reply`.`reply_from` =3 -- 回文來源
                    AND `mssr_forum`.`mssr_forum_reply`.`reply_state`=1 -- 回文狀態
                    AND `mssr_forum`.`mssr_forum_reply`.`article_id` ={$get_article_id}
                ORDER BY `mssr_forum`.`mssr_forum_reply`.`keyin_cdate` ASC
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $db_results_cno=count($db_results);
            if($db_results_cno!==0){
                foreach($db_results as $db_result){
                    $rs_book_sid=trim($db_result['book_sid']);
                    $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                    if(!empty($arry_book_infos)){
                        $rs_book_name=trim($arry_book_infos[0]['book_name']);
                        $arry_my_borrow[$rs_book_sid]=$rs_book_name;
                    }
                }
            }
            $arry_my_borrow['']=str_repeat("-",150);
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
        //我的書櫃 SQL
        //-----------------------------------------------

            //$arry_my_borrow=array();
            if(isset($sess_user_id)){
                $sql="
                    SELECT `mssr`.`mssr_book_borrow_log`.`book_sid`
                    FROM `mssr`.`mssr_book_borrow_log`
                    WHERE 1=1
                        AND `mssr`.`mssr_book_borrow_log`.`user_id`={$sess_user_id}
                    GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`,
                             `mssr`.`mssr_book_borrow_log`.`book_sid`
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $db_result){
                        $rs_book_sid=trim($db_result['book_sid']);
                        $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                        if(!empty($arry_book_infos)){
                            $rs_book_name=trim($arry_book_infos[0]['book_name']);
                            $arry_my_borrow[$rs_book_sid]=$rs_book_name;
                        }
                    }
                }
            }

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
                    FROM `mssr_forum`.`mssr_forum_article_like_log`
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
        //回文鷹架
        //-----------------------------------------------

            $reply_eagle_content=reply_eagle(1);
            $reply_eagle_code   =reply_eagle(2);

        //-----------------------------------------------
        //其他
        //-----------------------------------------------

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

            //模態框
            if(isset($sess_user_id))$modal_dialog_2=modal_dialog($rd=1,$type=2);
            //if(isset($sess_user_id))$modal_dialog_1=modal_dialog($rd=1,$type=1);

            $send_url=trim('http://').trim($_SERVER['HTTP_HOST']).trim($_SERVER['REQUEST_URI']);
            $sentence='2.請輸入文章內容';
            $sentence=fun_ex_sentence($get_group_task_id);
            if(empty($sentence))$sentence='2.請輸入文章內容';
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
<style>
    .jumbotron{
        background-image: url('../img/default/front_cover_group_mission.jpg');
        background-color: #ebe1d4;
    }
    .jumbotron .jumbotron_name, .jumbotron .jumbotron-xs_name{
        max-width: 500px;
        background-color: #000000;
        color: #ffcccc;
        font-size: 16pt;
    }
    .jumbotron .jumbotron-xs_name{
        margin-right: 20px;
        font-size: 12pt;
    }
    #article{
        padding-top: 25px;
    }
    .media-heading{
        margin: 5px 0;
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
                src="../img/default/group_mission.jpg"
                width="160" height="160" border="0" alt="user_img"
                onclick="location.href=''"
                onmouseover='this.style.cursor="pointer";'/>
                <!-- 大頭貼,大解析度,end -->

                <!-- jumbotron_name,start -->
                <span class="jumbotron_name">
                    任務主題：<br>
                    <?php echo htmlspecialchars($gask_topic);?>
                </span>
                <!-- jumbotron_name,end -->

                <!-- jumbotron_note,start -->

                <!-- jumbotron_note,end -->

            </div>
            <!-- jumbotron,end -->

            <!-- jumbotron-xs,start -->
            <div class="jumbotron jumbotron-xs hidden-sm hidden-md hidden-lg">

                <!-- 大頭貼,小解析度,start -->
                <img class="jumbotron-xs_img hidden-sm hidden-md hidden-lg"
                src="../img/default/group_mission.jpg"
                width="100" height="100" border="0" alt="user_img"
                onclick="location.href=''"
                onmouseover='this.style.cursor="pointer";'/>
                <!-- 大頭貼,小解析度,end -->

                <!-- jumbotron-xs_name,start -->
                <span class="jumbotron-xs_name">任務主題：<br><?php echo htmlspecialchars($gask_topic);?></span>
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
                            <!-- 按鈕區域 -->
                            <!-- 小解析度 -->
                            <!-- 按鈕區域 -->
                        </td>
                        <td class="hidden-xs" align="center"></td>
                        <td class="hidden-xs" align="center"></td>
                        <td class="hidden-xs" align="center"></td>
                    </tr></tbody>
                </table>
            </div>
            <!-- page_info,end -->

            <!-- lefe_side,start -->
            <div class="book_lefe_side col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <ul id="myTab" class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#view_article" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">
                        討論串
                    </a></li>
                </ul>
                <div id="myTabContent" class="tab-content">

                    <!-- 觀看文章 -->
                    <div role="tabpanel" class="tab-pane fade in active" id="view_article" aria-labelledBy="home-tab">

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
                                        <?php echo htmlspecialchars($rs_article_school_name);?>
                                        <?php echo htmlspecialchars($rs_article_user_name);?>
                                        <a href="user.php?user_id=<?php echo $sess_user_id;?>&tab=10">
                                            <?php echo htmlspecialchars($rs_article_user_title);?>
                                        </a>

                                        <!-- 功能鈕,大解析度,start -->
                                        <button type="button" class="btn_request_article btn btn-default btn-xs pull-right hidden-xs"
                                        data-toggle="modal" data-target=".modal_request_article">邀請好友一同聊書</button>
                                        <button type="button" class="btn_like_article btn btn-default btn-xs pull-right hidden-xs"
                                        user_id=<?php echo $sess_user_id;?>
                                        article_id=<?php echo $rs_article_id;?>
                                        reply_id=0><?php echo $btn_like_article_html;?></button>
                                        <!-- 功能鈕,大解析度,end -->

                                        <!-- 功能鈕,小解析度,start -->
                                        <?php if(isset($sess_user_id)):?>
                                            <div class="btn-group pull-right hidden-sm hidden-md hidden-lg">
                                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    功能&nbsp;<span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="#" class="btn_request_article"
                                                    data-toggle="modal" data-target=".modal_request_article">邀請好友一同聊書</a></li>

                                                    <li><a href="javascript:void(0);" class="btn_like_article"
                                                    user_id=<?php echo $sess_user_id;?>
                                                    article_id=<?php echo $rs_article_id;?>
                                                    reply_id=0><?php echo $btn_like_article_html;?></a></li>
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
                                $rs_reply_school_name   =trim($reply_result['school_name']);
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

                                //各樓層,讚
                                $btn_like_reply_html=trim('讚');
                                if(isset($sess_user_id)){
                                    $sql="
                                        SELECT `mssr_forum`.`mssr_forum_reply_like_log`.`user_id`
                                        FROM  `mssr_forum`.`mssr_forum_reply_like_log`
                                        WHERE 1=1
                                            #AND `mssr_forum`.`mssr_forum_reply_like_log`.`user_id` ={$sess_user_id }
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

                                $arry_reply_user_title=get_member_title($rs_reply_user_id,$arry_conn_mssr);
                                $rs_reply_user_title  =(isset($arry_reply_user_title[0]['title_name']))?trim("- ".$arry_reply_user_title[0]['title_name']):'';
                        ?>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="media">
                                    <a class="pull-left" href="user.php?user_id=<?php echo $rs_reply_user_id;?>&tab=1">
                                        <img class="media-object" src="<?php echo $rs_reply_img;?>" alt="Media">
                                    </a>
                                    <h4 class="media-heading">
                                        <?php echo htmlspecialchars($rs_reply_school_name);?>
                                        <?php echo htmlspecialchars($rs_reply_user_name);?>
                                        <a href="user.php?user_id=<?php echo $sess_user_id;?>&tab=10">
                                            <?php echo htmlspecialchars($rs_reply_user_title);?>
                                        </a>
                                        <!-- 功能鈕,大解析度,start -->
                                        <button type="button" class="btn_like_article btn btn-default btn-xs pull-right hidden-xs"
                                        user_id=<?php echo $sess_user_id;?>
                                        article_id=<?php echo $rs_article_id;?>
                                        reply_id=<?php echo $rs_reply_id;?>><?php echo $btn_like_reply_html;?></button>
                                        <!-- 功能鈕,大解析度,end -->

                                        <!-- 功能鈕,小解析度,start -->
                                        <?php if(isset($sess_user_id)):?>
                                            <div class="btn-group pull-right hidden-sm hidden-md hidden-lg">
                                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    功能&nbsp;<span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="javascript:void(0);" class="btn_like_article"
                                                    user_id=<?php echo $sess_user_id;?>
                                                    article_id=<?php echo $rs_article_id;?>
                                                    reply_id=<?php echo $rs_reply_id;?>><?php echo $btn_like_reply_html;?></a></li>
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
                                <h4 style="border-bottom:1px solid #428bca;margin-top:10px;padding:10px 0;color:#428bca;">發表回覆</h4>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
                                        <a href="javascript:void(0);" class="btn btn-primary btn-block"
                                        role="button" style="color:#ffffff;" onclick="reply_eagle(eagle_lv=1);void(0);"
                                        >使用回文輔助</a>

                                        <div class="row reply_eagle" style="position:relative;margin-top:0px;">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
                                                <form id="Form1" name="Form1" method="post" onsubmit="return false;">
                                                    <div class="div_textarea form-group" style="margin-top:15px;">
                                                        <textarea class="reply_content form-control" id="reply_content[]" name="reply_content[]" rows="6" placeholder=""
                                                        ><?php echo $sentence;?></textarea>
                                                    </div>
                                                    <div class="row chosen" style="position:relative;margin-bottom:15px;">
                                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
                                                            <select class="form-control book_sid" id="book_sid" name="book_sid" style="margin-bottom:0px;">
                                                                <option value="" disabled="disabled" selected>請選擇一本書來發文......</option>
                                                                <?php foreach($arry_my_borrow as $key=>$val):?>
                                                                    <option value="<?php echo trim($key);?>"><?php echo trim($val);?></option>
                                                                <?php endforeach;?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" id="send_chk">我已閱讀過並同意遵守討論區規則
                                                        </label>
                                                        , <a target="_blank" href="forum.php?method=view_mssr_forum_article_reply_rule" style="color:#428bca;">按這裡檢視討論區規則</a>
                                                    </div>
                                                    <hr></hr>
                                                    <button style='margin-bottom:20px;' type="button" class="btn btn-default pull-right" onclick="Btn_reply_article();void(0);">送出</button>
                                                    <div class="form-group hidden">
                                                        <input type="text" class="form-control" name="article_id" value="<?php echo (int)$get_article_id;?>">
                                                        <input type="text" class="form-control" name="eagle_code" id="eagle_code" value="0">
                                                        <input type="text" class="form-control" name="reply_from" value="3">
                                                        <input type="text" class="form-control" name="group_id" value="0">
                                                        <input type="text" class="form-control" name="send_url" value="<?php echo trim($send_url);?>">
                                                        <input type="text" class="form-control" name="method" value="reply_article">
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- <select class="form-control" onchange="reply_eagle();void(0);">
                                            <option disabled="disabled" selected>請選擇類型來開始回覆文章 </option>
                                            <option value="1">我覺得你說的很好，但我還想補充……            </option>
                                            <option value="2">在……的部分，我跟你的想法不一樣，因為……      </option>
                                            <option value="0">其他                                        </option>
                                        </select> -->
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <!-- lefe_side,end -->

            <!-- right_side,start -->

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
    <?php //echo $modal_dialog_1;?>
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
    var nl               ='\r\n';
    var get_from         =parseInt(<?php echo (int)$get_from;?>);
    var get_article_id   =parseInt(<?php echo $get_article_id;?>);
    var get_group_id     =parseInt(<?php echo (int)$get_group_id;?>);
    var get_group_task_id=parseInt(<?php echo (int)$get_group_task_id;?>);
    var request_article  =parseInt(<?php echo (int)$request_article;?>);
    var send_url         =document.URL;
    var reply_content_default=$.trim($.trim($('.reply_content')[0].value));


    //OBJ
    var reply_eagle_content=<?php echo json_encode($reply_eagle_content,true);?>;
    var reply_eagle_code   =<?php echo json_encode($reply_eagle_code,true);?>;
    var arry_my_borrow     =<?php echo json_encode($arry_my_borrow,true);?>;
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


    //FUNCTION
    $('.reply_content').focus(function(){
        if($.trim($(this)[0].value)===reply_content_default){
            $(this)[0].value='';
        }
    });
    $('.reply_content').focusout(function(){
        if($.trim($(this)[0].value)===''){
            $(this)[0].value=reply_content_default;
        }
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
    });
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
        var pushstate=send_url.replace("&request_article=1","");
        window.history.pushState("", "",pushstate);
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
            $(oForm1).append('<input type="hidden" class="form-control" name="group_task_id" value="'+get_group_task_id+'">');
            if(confirm('你確定要送出嗎 ?')){
                oForm1.action='../controller/add.php'
                oForm1.submit();
                return true;
            }else{
                return false;
            }
        }
    }


    //ONLOAD
    $(function(){
        if(request_article===1){
            $('.btn_request_article')[0].click();
        }
    })


    //將滑鼠右鍵|貼上事件取消
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
    //user_page_log(rd=3);
</script>
</html>
<?php
//-------------------------------------------------------
//page_reply 區塊 -- 結束
//-------------------------------------------------------
    $conn_user=NULL;
    $conn_mssr=NULL;
?>
<?php };?>


<?php function page_article($title="") {?>
<?php
//-------------------------------------------------------
//page_article 區塊 -- 開始
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
        global $get_group_task_id;
        global $get_article_id;
        global $tab;
        global $modal_bookstore_rec;

        global $conn_mssr;
        global $arry_conn_mssr;

        global $meta;
        global $navbar;
        global $carousel;
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

        $get_from         =(int)$get_from;
        $get_group_task_id=(int)$get_group_task_id;
        $get_article_id   =(int)$get_article_id;

        //-----------------------------------------------
        //任務資訊 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_forum`.`dev_group_mission`.`group_task_id`,
                    `mssr_forum`.`dev_group_mission`.`gask_topic`,
                    `mssr_forum`.`dev_group_mission`.`create_time`
                FROM `mssr_forum`.`dev_group_mission`
                WHERE 1=1
                    AND `dev_group_mission`.`group_task_id`={$get_group_task_id}
            ";
            $group_mission_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(empty($group_mission_results)){
                die('推播任務編號,錯誤!');
            }else{
                $gask_topic=trim($group_mission_results[0]['gask_topic']);
            }

        //-----------------------------------------------
        //文章資訊 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `user`.`member`.`name`,

                    `mssr_forum`.`dev_article_group_mission_rev`.`group_task_id`,

                    `mssr_forum`.`mssr_forum_article`.`user_id`,
                    `mssr_forum`.`mssr_forum_article`.`article_id`,
                    `mssr_forum`.`mssr_forum_article`.`group_id`,
                    `mssr_forum`.`mssr_forum_article`.`article_like_cno` AS `like_cno`,
                    `mssr_forum`.`mssr_forum_article`.`article_report_cno`,
                    `mssr_forum`.`mssr_forum_article`.`keyin_mdate`,

                    `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                    `mssr_forum`.`mssr_forum_article_detail`.`article_content`,
                    '' AS `reply_content`,
                    'article' AS `type`
                FROM `mssr_forum`.`dev_article_group_mission_rev`
                    INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                    `mssr_forum`.`dev_article_group_mission_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`

                    INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                    `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                    INNER JOIN `user`.`member` ON
                    `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                WHERE 1=1
                    AND `mssr_forum`.`dev_article_group_mission_rev`.`group_task_id`={$get_group_task_id}
                    AND `mssr_forum`.`mssr_forum_article`.`article_from`            =3 -- 文章來源
                    AND `mssr_forum`.`mssr_forum_article`.`article_type`            =1 -- 文章類型
                    AND `mssr_forum`.`mssr_forum_article`.`article_state`           =1 -- 文章狀態
                ORDER BY `mssr_forum`.`mssr_forum_article`.`keyin_mdate` DESC
            ";
            $article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

        //-----------------------------------------------
        //我的書櫃 SQL
        //-----------------------------------------------

            $sess_user_book_results=array();
            $arry_my_borrow=array();
            if(isset($sess_user_id)){
                $sql="
                    SELECT
                        `mssr`.`mssr_book_borrow_log`.`book_sid`
                    FROM `mssr`.`mssr_book_borrow_log`
                    WHERE 1=1
                        AND `mssr`.`mssr_book_borrow_log`.`user_id`={$sess_user_id}
                    GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`,
                             `mssr`.`mssr_book_borrow_log`.`book_sid`
                    ORDER BY `mssr`.`mssr_book_borrow_log`.`borrow_sdate` DESC
                ";
                $sess_user_book_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($sess_user_book_results)){
                    foreach($sess_user_book_results as $sess_user_book_result){
                        $rs_book_sid=trim($sess_user_book_result['book_sid']);
                        $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                        if(!empty($arry_book_infos)){
                            $rs_book_name=trim($arry_book_infos[0]['book_name']);
                            $arry_my_borrow[$rs_book_sid]=$rs_book_name;
                        }
                    }
                }
            }

        //-----------------------------------------------
        //發文鷹架
        //-----------------------------------------------

            $article_eagle_content=article_eagle(1);
            $article_eagle_code   =article_eagle(2);

        //-----------------------------------------------
        //是否已接受任務且尚未發表文章 SQL
        //-----------------------------------------------

            $auth_add_article=false;
            //$sql="
            //    SELECT
            //        `mssr_forum`.`dev_complete_mission_log`.`accept_uid`,
            //        `mssr_forum`.`dev_complete_mission_log`.`group_task_id`
            //    FROM `mssr_forum`.`dev_complete_mission_log`
            //    WHERE 1=1
            //        AND `mssr_forum`.`dev_complete_mission_log`.`accept_uid`   ={$sess_user_id}
            //        AND `mssr_forum`.`dev_complete_mission_log`.`mission_state`=2
            //";
            //$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            //if(!empty($db_results)){
            //    $rs_accept_uid   =(int)$db_results[0]['accept_uid'];
            //    $rs_group_task_id=(int)$db_results[0]['group_task_id'];
                $sql="
                    SELECT
                        `mssr_forum`.`mssr_forum_article`.`user_id`
                    FROM `mssr_forum`.`dev_article_group_mission_rev`
                        INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                        `mssr_forum`.`dev_article_group_mission_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`
                    WHERE 1=1
                        AND `mssr_forum`.`dev_article_group_mission_rev`.`group_task_id`={$get_group_task_id}
                        AND `mssr_forum`.`mssr_forum_article`.`article_from`    =3 -- 文章來源
                        AND `mssr_forum`.`mssr_forum_article`.`article_type`    =1 -- 文章類型
                        AND `mssr_forum`.`mssr_forum_article`.`article_state`   =1 -- 文章狀態
                        AND `mssr_forum`.`mssr_forum_article`.`user_id`         ={$sess_user_id}
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(empty($db_results)){
                    $auth_add_article=true;
                }
            //}

        //-----------------------------------------------
        //其他
        //-----------------------------------------------

            $send_url=trim('http://').trim($_SERVER['HTTP_HOST']).trim($_SERVER['REQUEST_URI']);
            $sentence='2.請輸入文章內容';
            $sentence=fun_ex_sentence($get_group_task_id);
            if(empty($sentence))$sentence='2.請輸入文章內容';
            //echo "<Pre>";
            //print_r($sentence);
            //echo "</Pre>";
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
    <link href="../css/site.css" rel="stylesheet" type="text/css">

    <!--[if (gte IE 6)&(lte IE 8)]>
        <script>self.location.href='../pages/browser_update/index.php'</script>
    <![endif]-->

    <!--[if lt IE 9]>
        <script src="../../../lib/js/html5/code.js"></script>
        <script src="../../../lib/js/css/code.js"></script>
    <![endif]-->
</head>
<style>
    .jumbotron{
        background-image: url('../img/default/front_cover_group_mission.jpg');
        background-color: #ebe1d4;
    }
    .jumbotron .jumbotron_name, .jumbotron .jumbotron-xs_name{
        max-width: 500px;
        background-color: #000000;
        color: #ffcccc;
        font-size: 16pt;
    }
    .jumbotron .jumbotron-xs_name{
        margin-right: 20px;
        font-size: 12pt;
    }
    #article{
        padding-top: 25px;
    }
    .media-heading{
        margin: 5px 0;
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
                src="../img/default/group_mission.jpg"
                width="160" height="160" border="0" alt="user_img"
                onclick="location.href=''"
                onmouseover='this.style.cursor="pointer";'/>
                <!-- 大頭貼,大解析度,end -->

                <!-- jumbotron_name,start -->
                <span class="jumbotron_name">
                    任務主題：<br>
                    <?php echo htmlspecialchars($gask_topic);?>
                </span>
                <!-- jumbotron_name,end -->

                <!-- jumbotron_note,start -->

                <!-- jumbotron_note,end -->

            </div>
            <!-- jumbotron,end -->

            <!-- jumbotron-xs,start -->
            <div class="jumbotron jumbotron-xs hidden-sm hidden-md hidden-lg">

                <!-- 大頭貼,小解析度,start -->
                <img class="jumbotron-xs_img hidden-sm hidden-md hidden-lg"
                src="../img/default/group_mission.jpg"
                width="100" height="100" border="0" alt="user_img"
                onclick="location.href=''"
                onmouseover='this.style.cursor="pointer";'/>
                <!-- 大頭貼,小解析度,end -->

                <!-- jumbotron-xs_name,start -->
                <span class="jumbotron-xs_name">任務主題：<br><?php echo htmlspecialchars($gask_topic);?></span>
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
                            <!-- 按鈕區域 -->
                            <!-- 小解析度 -->
                            <!-- 按鈕區域 -->
                        </td>
                        <td class="hidden-xs" align="center"></td>
                        <td class="hidden-xs" align="center"></td>
                        <td class="hidden-xs" align="center"></td>
                    </tr></tbody>
                </table>
            </div>
            <!-- page_info,end -->

            <!-- lefe_side,start -->
            <div class="book_lefe_side col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <ul id="myTab" class="nav nav-tabs" role="tablist">
                    <li role="presentation" <?php if($tab===1)echo ' class="active"';?>><a href="#article" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">
                        討論
                    </a></li>
                    <?php if(isset($sess_user_id)&&($auth_add_article)):?>
                        <li role="presentation" <?php if($tab===2)echo ' class="active"';?>><a href="#add_article" id="profile-tab" role="tab" data-toggle="tab" aria-controls="profile">
                            發文
                        </a></li>
                    <?php endif;?>
                </ul>
                <div id="myTabContent" class="tab-content">
                    <!-- 討論 -->
                    <div role="tabpanel" class="tab-pane fade <?php if($tab===1)echo 'in active';?>" id="article" aria-labelledBy="home-tab">
                        <?php
                        if(!empty($article_results)){
                            foreach($article_results as $article_result):
                                extract($article_result, EXTR_PREFIX_ALL, "rs");
                                $rs_user_id         =(int)$rs_user_id;
                                $rs_group_id        =(int)$rs_group_id;
                                $rs_article_id      =(int)$rs_article_id;
                                $rs_like_cno        =(int)$rs_like_cno;
                                $rs_name            =trim($rs_name);
                                $rs_book_sid        =trim($rs_book_sid);
                                $rs_keyin_mdate     =date("Y-m-d H:i",strtotime(trim($rs_keyin_mdate)));
                                $rs_article_title   =trim($rs_article_title);
                                $rs_article_content =trim($rs_article_content);
                                $rs_reply_content   =trim($rs_reply_content);
                                $rs_type            =trim($rs_type);

                                if($rs_group_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
                                    $arry_blacklist_group_school=get_blacklist_group_school($sess_school_code,$rs_group_id,$arry_conn_mssr);
                                    if(!empty($arry_blacklist_group_school))continue;
                                }

                                if($rs_group_id===0)$get_from=1;
                                if($rs_group_id!==0)$get_from=2;

                                $rs_arry_content_img=[];
                                if($rs_type==='article'){
                                    $rs_content=$rs_article_content;
                                }else{
                                    $rs_content=$rs_reply_content;
                                }

                                //特殊處理
                                $arry_2=array(
                                    'gcp_2015_2_5_2_1','gcp_2015_2_5_4_1','gcp_2015_2_5_7_1',
                                    'gcp_2015_2_5_9_1','tst_2015_2_4_2_1','tst_2015_2_4_3_1',
                                    'tst_2015_2_4_6_1','tst_2015_2_4_9_1','tbn_2015_2_4_1_1',
                                    'tbn_2015_2_4_4_1','apa_2015_2_4_3_1','apa_2015_2_4_4_1'
                                );
                                $arry_3=array(
                                    trim('dsg'),trim('jzm'),trim('uxo'),trim('sgc'),
                                    trim('zpi'),trim('irx'),trim('gsz'),trim('vqk'),
                                    trim('gid'),trim('gth'),trim('bts'),trim('pce'),
                                    trim('ctc'),trim('gnk'),trim('gpe'),trim('nhe'),
                                    trim('gdc'),trim('csp'),trim('gps'),trim('cyc'),
                                    trim('jdy'),trim('smb'),trim('bnr'),trim('nep'),
                                    trim('dru'),trim('nsa'),trim('zbq'),trim('pqr'),
                                    trim('wbp'),trim('cjh  ')
                                );
                                $arry_4=array(
                                    trim('ged '),trim('ghf '),trim('ghl '),trim('zla '),
                                    trim('glh '),trim('zsk '),trim('star'),trim('bjd '),
                                    trim('pyd '),trim('cte '),trim('gsl '),trim('gfd '),
                                    trim('nif '),trim('pnr '),trim('wof '),trim('gzj '),
                                    trim('yre '),trim('api '),trim('smps'),trim('nam '),
                                    trim('uwn '),trim('ivw '),trim('did '),trim('lrb '),
                                    trim('chi '),trim('edl '),trim('won '),trim('dxu ')
                                );
                                $sql="
                                    SELECT
                                        user.student.class_code,
                                        user.semester.school_code
                                    FROM user.student

                                        INNER JOIN user.class ON
                                        user.class.class_code=user.student.class_code

                                        INNER JOIN user.semester ON
                                        user.class.semester_code=user.semester.semester_code
                                    WHERE 1=1
                                        AND `user`.`student`.`uid`={$rs_user_id}
                                    GROUP BY user.class.class_code

                                        UNION

                                    SELECT
                                        user.teacher.class_code,
                                        user.semester.school_code
                                    FROM user.teacher

                                        INNER JOIN user.class ON
                                        user.class.class_code=user.teacher.class_code

                                        INNER JOIN user.semester ON
                                        user.class.semester_code=user.semester.semester_code
                                    WHERE 1=1
                                        AND `user`.`teacher`.`uid`={$rs_user_id}
                                    GROUP BY user.class.class_code
                                ";
                                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                $continue=true;
                                foreach($db_results as $db_result){
                                    $rs_class_code =trim($db_result['class_code']);
                                    $rs_school_code=trim($db_result['school_code']);
                                    if(in_array($rs_class_code, $arry_2) || in_array($rs_school_code, $arry_3) || in_array($rs_school_code, $arry_4)){
                                        $continue=false;
                                        break;
                                    }
                                }
                                if($continue){
                                    continue;
                                }

                                if($get_from===1){
                                    $rs_book_name='';
                                    $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                    if(!empty($arry_book_infos)){$rs_book_name=trim($arry_book_infos[0]['book_name']);}else{continue;}
                                }else{
                                    $rs_group_name='';
                                    $sql="
                                        SELECT
                                            `mssr_forum`.`mssr_forum_group`.`group_name`
                                        FROM `mssr_forum`.`mssr_forum_group`
                                        WHERE 1=1
                                            AND `mssr_forum`.`mssr_forum_group`.`group_id`={$rs_group_id}
                                    ";
                                    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                    $rs_group_name=trim(($db_results[0]['group_name']));
                                }

                                //回文次數
                                $sql="
                                    SELECT COUNT(*) AS `cno`
                                    FROM `mssr_forum`.`mssr_forum_reply`
                                    WHERE 1=1
                                        AND `mssr_forum`.`mssr_forum_reply`.`article_id`= {$rs_article_id}
                                ";
                                $reply_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                $reply_article_cno    =(int)($reply_article_results[0]['cno']);

                                $rs_content=htmlspecialchars($rs_content);
                                preg_match_all('/src.*/',$rs_content,$arrys_preg_content);
                                if(!empty($arrys_preg_content)&&isset($arrys_preg_content[0])&&!empty($arrys_preg_content[0])){
                                    $arry_preg_article_content=$arrys_preg_content[0];
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
                                                    $replace="<img class='img-responsive' src='{$file_path}' width='100%' border='0' alt='rec_draw' style='border:0px solid red;margin:0 auto;'>";
                                                    $rs_arry_content_img[]=$replace;
                                                    $rs_content=str_replace("[img src=&quot;{$file_path}&quot; img]","",$rs_content);
                                                }elseif($extension==='mp3'){
                                                    $replace="
                                                        <audio controls>
                                                            <source src='{$file_path}' type='audio/mpeg'>
                                                        </audio>
                                                    ";
                                                    $rs_content=str_replace("[audio src=&quot;{$file_path}&quot; audio]","",$rs_content);
                                                }else{continue;}
                                            }
                                        }else{
                                            if(file_exists($file_path)){
                                                $file_info=pathinfo($file_path);
                                                $extension=(isset($file_info['extension']))?$file_info['extension']:'';
                                                if($extension==='jpg'){
                                                    $replace="<img class='img-responsive' src='{$file_path}' width='100%' border='0' alt='rec_draw' style='border:0px solid red;margin:0 auto;'>";
                                                    $rs_arry_content_img[]=$replace;
                                                    $rs_content=str_replace("[img src=&quot;{$file_path}&quot; img]","",$rs_content);
                                                }elseif($extension==='mp3'){
                                                    $replace="
                                                        <audio controls>
                                                            <source src='{$file_path}' type='audio/mpeg'>
                                                        </audio>
                                                    ";
                                                    $rs_content=str_replace("[audio src=&quot;{$file_path}&quot; audio]","",$rs_content);
                                                }else{continue;}
                                            }
                                        }
                                    }
                                }
                                if(mb_strlen($rs_content)>100){
                                    $rs_content=mb_substr($rs_content,0,100)."...";
                                }
                        ?>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="float:none;margin:0 auto;width:92%;border-bottom:1px dashed #000000;">
                                    <br><div class="triangle_15" style="position:relative;margin:4px 5px 0 0;float:left;"></div>
                                    <p><b><?php echo htmlspecialchars($rs_keyin_mdate);?></b></p>
                                    <div class="triangle_15" style="position:relative;margin:4px 5px 0 0;float:left;"></div>
                                    <p><b><?php echo htmlspecialchars($rs_article_title);?></b></p>
                                    <p><?php echo htmlspecialchars($rs_like_cno);?>人說這讚。</p><br>
                                    <?php if(!empty($rs_arry_content_img)):?>
                                        <div class="text-center"><?php echo $rs_arry_content_img[0];?></div><br>
                                        <p class="text-center"><?php echo htmlspecialchars($rs_content);?></p>
                                    <?php else:?>
                                        <p class="text-center"><?php echo htmlspecialchars($rs_content);?></p>
                                    <?php endif;?><br>
                                    <p>
                                        <a target="_blank" href="_dev_group_mission.php?get_from=3&group_task_id=<?php echo $get_group_task_id;?>&article_id=<?php echo $rs_article_id;?>">
                                            <span style='color:#428bca;'>(前往觀看...)</span>
                                        </a>
                                    </p>
                                    <p><?php echo htmlspecialchars($rs_name);?>發表 留言(<?php echo $reply_article_cno;?>)</p>
                                </div>
                            </div>
                        <?php endforeach;}else{?>
                            <div class="row media-row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="media">
                                        <a class="pull-left" href="javascript:void(0);"></a>
                                        <div class="media-body">
                                            <h4 class="media-heading"><b>目前無任何相關文章...</b></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }?>
                    </div>

                    <!-- 發文 -->
                    <div role="tabpanel" class="tab-pane fade <?php if($tab===2)echo 'in active';?>" id="add_article" aria-labelledBy="profile-tab">
                        <div class="row">
                            <div class="col-xs-12 col-sm-3 col-md-3 col-lg3 text-center visible-xs add_article_help-visible-xs" style="margin-bottom:15px;">
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
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="row eagle_lv_5" style="border-right:1px solid #eeeeee;">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg12">
                                        <form id="Form1"  name="Form1" method="post" onsubmit="return false;">
                                            <select class="form-control book_sid" id="book_sid" name="book_sid" style="margin-bottom:10px;">
                                                <option value="" disabled="disabled" selected>請選擇一本書來發文......</option>
                                                <?php foreach($arry_my_borrow as $key=>$val):?>
                                                    <option value="<?php echo trim($key);?>"><?php echo trim($val);?></option>
                                                <?php endforeach;?>
                                            </select>
                                            <div class="form-group">
                                                <input type="text" id="article_title" name="article_title" class="form-control" placeholder="1.請輸入文章標題">
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
                                                    <div class="form-group">
                                                        <textarea class="form-control article_content" id="article_content[]" name="article_content[]" rows="10" placeholder="<?php //echo $sentence;?>"
                                                        ><?php echo $sentence;?></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center hidden-xs add_article_help-hidden-xs">
                                                    <div>
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
                                                    </div>
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
                                                   <input type="checkbox" id="send_chk">我已閱讀過並同意遵守討論區規則
                                               </label>
                                                <a target="_blank" href="forum.php?method=view_mssr_forum_article_reply_rule" style="color:#428bca;">按這裡檢視討論區規則</a>
                                            </div>
                                            <hr></hr>
                                            <button type="button" class="btn btn-default pull-left btn_add_article" onclick="Btn_add_article();void(0);" style="margin:0 3px;">送出</button>
                                            <div class="form-group hidden">
                                                <input type="text" class="form-control" name="eagle_code" value="" id="eagle_code">
                                                <input type="text" class="form-control" name="article_from" value="3">
                                                <input type="text" class="form-control" name="group_id" value="0">
                                                <input type="text" class="form-control" name="group_task_id" value="<?php echo (int)$get_group_task_id;?>">
                                                <input type="text" class="form-control" name="send_url" value="<?php echo trim($send_url);?>">
                                                <input type="text" class="form-control" name="method" value="add_article">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- lefe_side,end -->

            <!-- right_side,start -->

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
<script type="text/javascript" src="../inc/code.js"></script>

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl               ='\r\n';
    var get_from         =parseInt(<?php echo (int)$get_from;?>);
    var get_group_task_id=parseInt(<?php echo (int)$get_group_task_id;?>);
    var get_article_id   =parseInt(<?php echo (int)$get_article_id;?>);
    var send_url         =document.URL;
    var article_content_default=$.trim($.trim($('.article_content')[0].value));


    //OBJ
    var article_eagle_content=<?php echo json_encode($article_eagle_content,true);?>;
    var article_eagle_code=<?php echo json_encode($article_eagle_code,true);?>;
    var arry_my_borrow=<?php echo json_encode($arry_my_borrow,true);?>;
    var json_sess_user_book_results ='<?php echo json_encode($sess_user_book_results,true);?>';


    //FUNCTION
    $('.article_content').focus(function(){
        if($.trim($(this)[0].value)===article_content_default){
            $(this)[0].value='';
        }
    });
    $('.article_content').focusout(function(){
        if($.trim($(this)[0].value)===''){
            $(this)[0].value=article_content_default;
        }
    });
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
        if(trim(oeagle_code.value)===''){
            oeagle_code.value=0;
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
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


    //ONLOAD
    $(function(){
        //發文輔助顯示
        if(/Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
            $('.add_article_help-hidden-xs').remove();
        }else{
            $('.add_article_help-visible-xs').remove();
        }
    })


    //將滑鼠右鍵|貼上事件取消
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
    //user_page_log(rd=3);
</script>
</html>
<?php
//-------------------------------------------------------
//page_article 區塊 -- 結束
//-------------------------------------------------------
    $conn_user=NULL;
    $conn_mssr=NULL;
?>
<?php };?>

<?php
    $conn_user=NULL;
    $conn_mssr=NULL;
?>