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
        require_once(str_repeat("../",4).'config/config.php');

        //外掛頁面檔
        require_once(str_repeat("../",2).'pages/code.php');

        //外掛函式檔
        $funcs=array(
            APP_ROOT.'inc/code',
            APP_ROOT.'service/forum/inc/code'
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
    //接收,設定參數
    //---------------------------------------------------

        $user_id=(isset($_GET['user_id']))?(int)$_GET['user_id']:0;
        $tab    =(isset($_GET['tab']))?(int)$_GET['tab']:1;

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //連線物件
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

        //-----------------------------------------------
        //討論 SQL
        //-----------------------------------------------

            $sql="
                    SELECT
                        `user`.`member`.`name`,

                        `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,

                        `mssr_forum`.`mssr_forum_article`.`user_id`,
                        `mssr_forum`.`mssr_forum_article`.`article_from` AS `from`,
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
                        AND `mssr_forum`.`mssr_forum_article`.`article_from` IN (1,2) -- 文章來源
                        AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                        AND `mssr_forum`.`mssr_forum_article`.`user_id`      ={$user_id}

                UNION ALL

                    SELECT
                        `user`.`member`.`name`,

                        `mssr_forum`.`mssr_forum_reply_book_rev`.`book_sid`,

                        `mssr_forum`.`mssr_forum_reply`.`user_id`,
                        `mssr_forum`.`mssr_forum_reply`.`reply_from` AS `from`,
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
                        AND `mssr_forum`.`mssr_forum_article`.`article_from` IN (1,2) -- 文章來源
                        AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                        AND `mssr_forum`.`mssr_forum_reply`.`reply_state`    =1 -- 回文狀態
                        AND `mssr_forum`.`mssr_forum_reply`.`user_id`        ={$user_id}
                    ORDER BY `keyin_cdate` DESC
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
                    $rs_from            =(int)$rs_from;

                    $rs_name            =trim($rs_name);
                    $rs_book_sid        =trim($rs_book_sid);
                    $rs_keyin_cdate     =trim($rs_keyin_cdate);
                    $rs_article_title   =trim($rs_article_title);
                    $rs_article_content =trim($rs_article_content);
                    $rs_reply_content   =trim($rs_reply_content);
                    $rs_type            =trim($rs_type);
                    $rs_keyin_time      =strtotime($rs_keyin_cdate);
                    $rs_from_html       =($rs_from==1)?trim("書頁"):trim("小組頁");

                    if($rs_group_id!==0){
                        $sql="
                            SELECT `mssr_forum`.`mssr_forum_group`.`group_id`
                            FROM `mssr_forum`.`mssr_forum_group`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_group`.`group_id`={$rs_group_id}
                                AND `mssr_forum`.`mssr_forum_group`.`group_state`=1
                        ";
                        $tmp_group_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(empty($tmp_group_results))continue;
                    }

                    $article_reply_results[$rs_keyin_time][trim('rs_name        ')]=$rs_name;
                    $article_reply_results[$rs_keyin_time][trim('book_sid       ')]=$rs_book_sid;
                    $article_reply_results[$rs_keyin_time][trim('user_id        ')]=$rs_user_id;
                    $article_reply_results[$rs_keyin_time][trim('group_id       ')]=$rs_group_id;
                    $article_reply_results[$rs_keyin_time][trim('article_id     ')]=$rs_article_id;
                    $article_reply_results[$rs_keyin_time][trim('reply_id       ')]=$rs_reply_id;
                    $article_reply_results[$rs_keyin_time][trim('like_cno       ')]=$rs_like_cno;
                    $article_reply_results[$rs_keyin_time][trim('keyin_mdate    ')]=$rs_keyin_cdate;
                    $article_reply_results[$rs_keyin_time][trim('article_title  ')]=$rs_article_title;
                    $article_reply_results[$rs_keyin_time][trim('article_content')]=$rs_article_content;
                    $article_reply_results[$rs_keyin_time][trim('reply_content  ')]=$rs_reply_content;
                    $article_reply_results[$rs_keyin_time][trim('type           ')]=$rs_type;
                    $article_reply_results[$rs_keyin_time][trim('from_html      ')]=$rs_from_html;
                }
                //時間排序
                krsort($article_reply_results);
            }

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------
?>
<?php
if(!empty($article_reply_results)){
    foreach($article_reply_results as $article_reply_result):
        extract($article_reply_result, EXTR_PREFIX_ALL, "rs");
        $rs_user_id         =(int)$rs_user_id;
        $rs_group_id        =(int)$rs_group_id;
        $rs_article_id      =(int)$rs_article_id;
        $rs_reply_id        =(int)$rs_reply_id;
        $rs_like_cno        =(int)$rs_like_cno;
        $rs_name            =trim($rs_name);
        $rs_book_sid        =trim($rs_book_sid);
        $rs_keyin_mdate     =date("Y-m-d H:i",strtotime(trim($rs_keyin_mdate)));
        $rs_article_title   =trim($rs_article_title);
        $rs_article_content =trim($rs_article_content);
        $rs_reply_content   =trim($rs_reply_content);
        $rs_type            =trim($rs_type);
        $rs_from_html       =trim($rs_from_html);

        if($rs_group_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
            $arry_blacklist_group_school=get_blacklist_group_school($sess_school_code,$rs_group_id,$arry_conn_mssr);
            if(!empty($arry_blacklist_group_school))continue;
        }

        if($rs_article_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
            $arry_blacklist_article_school=get_blacklist_article_school($sess_school_code,$rs_article_id,$arry_conn_mssr);
            if(!empty($arry_blacklist_article_school))continue;
        }

        if($rs_reply_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
            $arry_blacklist_reply_school=get_blacklist_reply_school($sess_school_code,$rs_reply_id,$arry_conn_mssr);
            if(!empty($arry_blacklist_reply_school))continue;
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
                    //$tmp_ftp_root ="public_html/mssr/info/forum";
                    //$tmp_ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                    //$tmp_ftp_login=ftp_login($tmp_ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                    //ftp_pasv($tmp_ftp_conn,TRUE);
                    //$tmp_file_path=str_replace("http://{$arry_ftp1_info['host']}","public_html",$file_path);
                    //$arry_ftp_file=ftp_nlist($tmp_ftp_conn,$tmp_file_path);
                    if(is_url_exist($file_path)){
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
        if(mb_strlen($rs_content)>200){
            $rs_content=mb_substr($rs_content,0,200)."...";
        }
?>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="float:none;margin:0 auto;width:92%;border-bottom:1px dashed #000000;">

            <div class='row' style='margin-top:15px;margin-bottom:30px;background-color:#e1e1e1;border-radius:3px;'>
                <div class='text-left col-xs-6 col-sm-9 col-md-9 col-lg-9' style='margin-top:15px;margin-bottom:15px;border:0px solid red;'>
                    <b>【<?php echo htmlspecialchars($rs_from_html);?>】</b>
                    <b><?php echo htmlspecialchars($rs_article_title);?></b>
                </div>
                <div class='text-right col-xs-6 col-sm-3 col-md-3 col-lg-3' style='margin-top:15px;margin-bottom:15px;border:0px solid red;'>
                    <b><?php echo htmlspecialchars($rs_keyin_mdate);?></b>
                </div>
            </div>

            <?php if(!empty($rs_arry_content_img)):?>
                <div class='text-center'><?php echo $rs_arry_content_img[0];?></div><br>
                <p class='text-left'><?php echo htmlspecialchars($rs_content);?></p>
            <?php else:?>
                <p class="text-left"><?php echo htmlspecialchars($rs_content);?></p>
            <?php endif;?><br>

            <p style='float:left;'>
                <a target='_blank' href='reply.php?get_from=<?php echo $get_from;?>&article_id=<?php echo $rs_article_id;?>'>
                    <span style='color:#428bca;'>(前往觀看...)</span>
                </a>
            </p>

            <p style='float:right;'>
                <?php echo htmlspecialchars($rs_like_cno);?>人說這讚&nbsp;&nbsp;|&nbsp;
                <?php echo htmlspecialchars($rs_name);?>發表 留言(<?php echo $reply_article_cno;?>)
            </p>
            <div style='clear:right;'></div>
        </div>
    </div>
<?php endforeach;}else{?>
    <div class="user_lefe_side_tab3 row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:center;">
            <pre style="background-color:#ffffdd;">目前共有【<?php echo count($article_reply_results);?>】個討論文章</pre>
        </div>
    </div>
<?php }?>
<?php
    $conn_user=NULL;
    $conn_mssr=NULL;
    //ftp_close($ftp_conn);
?>