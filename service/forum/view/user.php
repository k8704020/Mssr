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

        //特殊處理
        if(!isset($sess_country_code)){
            $sess_country_code='tw';
        }
        if($sess_country_code!=='tw'){
            die();
        }

//分組觀察用程式碼
$use_new_system = true;

$isolated_school_array = array(
	'hop','dat','tqa','zbq','tap','mid','tbn','stp','cle','osl',
	'uwn','ifx','lqd','dzu','lum','dxu','bts','gwh','vsa','wte',
	'xql','gdc','ctc','glh','gcp','don','lrb','sua','pmc','smps',
	'lhes','cpe','chk','chc','bjd','cte','cwl','okr','shps','ybs'
);

if (isset($sess_school_code) && in_array($sess_school_code, $isolated_school_array)) {
	$use_new_system = false;
}

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------

        $user_id=(isset($_GET['user_id']))?(int)$_GET['user_id']:0;
        $tab    =(isset($_GET['tab']))?(int)$_GET['tab']:1;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

        $arry_err=array();

        if($user_id===0){
            $arry_err[]='使用者主索引,錯誤!';
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

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

        //-----------------------------------------------
        //page_info SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `name`,`sex`
                FROM `user`.`member`
                WHERE 1=1
                    AND `user`.`member`.`uid`={$user_id}
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            $user_img  ='';
            $user_name ='';
            $user_sex  =1;
            if(!empty($db_results)){
                $user_name=trim($db_results[0]['name']);
                $user_sex =(int)$db_results[0]['sex'];
                if($user_sex===1)$user_img='../img/default/user_boy.png';
                if($user_sex===2)$user_img='../img/default/user_girl.png';
            }else{die();}


            $sql="
                SELECT
                    COUNT(*) AS `cno`
                FROM `mssr_forum`.`mssr_forum_article`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_article`.`user_id`={$user_id}
            ";
            $add_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $add_article_cno=(int)($add_article_results[0]['cno']);


            $sql="
                SELECT
                    COUNT(*) AS `cno`
                FROM `mssr_forum`.`mssr_forum_reply`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_reply`.`user_id`={$user_id}
            ";
            $reply_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $reply_article_cno=(int)($reply_article_results[0]['cno']);

            $sql="
                    SELECT `user`.`school`.`school_code`, `user`.`school`.`school_name`, `user`.`class`.`grade`, `user`.`class_name`.`class_name`
                    FROM `user`.`class`
                        INNER JOIN `user`.`class_name` ON
                        `user`.`class`.`class_category`=`user`.`class_name`.`class_category`
                        INNER JOIN `user`.`student` ON
                        `user`.`class`.`class_code`=`user`.`student`.`class_code`
                        INNER JOIN `user`.`semester` ON
                        `user`.`class`.`semester_code`=`user`.`semester`.`semester_code`
                        INNER JOIN `user`.`school` ON
                        `user`.`semester`.`school_code`=`user`.`school`.`school_code`
                    WHERE 1=1
                        AND `user`.`student`.`uid`={$user_id}
                        AND `user`.`class`.`classroom` =`user`.`class_name`.`classroom`
                        AND `user`.`student`.`start`<=NOW()
                        AND `user`.`student`.`end`  >=NOW()
                    GROUP BY `user`.`class`.`class_code`

                UNION

                    SELECT `user`.`school`.`school_code`, `user`.`school`.`school_name`, `user`.`class`.`grade`, `user`.`class_name`.`class_name`
                    FROM `user`.`class`
                        INNER JOIN `user`.`class_name` ON
                        `user`.`class`.`class_category`=`user`.`class_name`.`class_category`
                        INNER JOIN `user`.`teacher` ON
                        `user`.`class`.`class_code`=`user`.`teacher`.`class_code`
                        INNER JOIN `user`.`semester` ON
                        `user`.`class`.`semester_code`=`user`.`semester`.`semester_code`
                        INNER JOIN `user`.`school` ON
                        `user`.`semester`.`school_code`=`user`.`school`.`school_code`
                    WHERE 1=1
                        AND `user`.`teacher`.`uid`={$user_id}
                        AND `user`.`class`.`classroom` =`user`.`class_name`.`classroom`
                        AND `user`.`teacher`.`start`<=NOW()
                        AND `user`.`teacher`.`end`  >=NOW()
                    GROUP BY `user`.`class`.`class_code`
            ";
            $arry_user_school_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $user_school_code='';
            if(!empty($arry_user_school_results))$user_school_code=trim($arry_user_school_results[0]['school_code']);

        //-----------------------------------------------
        //書櫃 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr`.`mssr_book_borrow_log`.`book_sid`
                FROM `mssr`.`mssr_book_borrow_log`
                WHERE 1=1
                    AND `mssr`.`mssr_book_borrow_log`.`user_id`={$user_id}
                GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`,
                         `mssr`.`mssr_book_borrow_log`.`book_sid`
                ORDER BY `mssr`.`mssr_book_borrow_log`.`borrow_sdate` DESC
            ";
            $book_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

        //-----------------------------------------------
        //書櫃類型 SQL
        //-----------------------------------------------

            $arry_book_category_rev_cno=array();
            $book_category_rev_results =array();
            $lists_book_category_rev='';
            if(!empty($book_results)&&(isset($user_school_code))&&(trim($user_school_code)!=='')){
                $sql="
                    SELECT `mssr`.`mssr_book_category`.`cat_name`
                    FROM `mssr`.`mssr_book_category`
                    WHERE 1=1
                        AND `mssr`.`mssr_book_category`.`cat2_id`    =1
                        AND `mssr`.`mssr_book_category`.`cat3_id`    =1
                        AND `mssr`.`mssr_book_category`.`cat_state`  ='啟用'
                        AND `mssr`.`mssr_book_category`.`school_code`='{$user_school_code}'
                ";
                $book_category_rev_cno_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($book_category_rev_cno_results)){
                    foreach($book_category_rev_cno_results as $arry_val){
                        $arry_book_category_rev_cno[trim($arry_val['cat_name'])]=0;
                    }
                }

                $arry_book_category_rev=array();
                foreach($book_results as $book_result){
                    $rs_book_sid=trim($book_result['book_sid']);
                    $arry_book_category_rev[]=$rs_book_sid;
                    $lists_book_category_rev=implode("','",$arry_book_category_rev);
                }

                $sql="
                    SELECT `mssr`.`mssr_book_category`.`cat_name`
                    FROM `mssr`.`mssr_book_category`
                        INNER JOIN `mssr`.`mssr_book_category_rev` ON
                        `mssr`.`mssr_book_category`.`cat_code`=`mssr`.`mssr_book_category_rev`.`cat_code`
                    WHERE 1=1
                        AND `mssr`.`mssr_book_category`.`cat2_id`    =1
                        AND `mssr`.`mssr_book_category`.`cat3_id`    =1
                        AND `mssr`.`mssr_book_category`.`cat_state`  ='啟用'
                        AND `mssr`.`mssr_book_category`.`school_code`='{$user_school_code}'
                        AND `mssr`.`mssr_book_category_rev`.`book_sid` IN ('{$lists_book_category_rev}')
                ";
                $book_category_rev_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($book_category_rev_results)){
                    foreach($book_category_rev_results as $book_category_rev_result){
                        $rs_cat_name=trim($book_category_rev_result['cat_name']);
                        if(array_key_exists($rs_cat_name,$arry_book_category_rev_cno)){
                            $arry_book_category_rev_cno[$rs_cat_name]=(int)($arry_book_category_rev_cno[$rs_cat_name]+1);
                        }
                    }
                    $arry_book_category_rev_cno['未分類']=(int)(count($book_results)-count($book_category_rev_results));
                }

                $category_rev_cno_val_0=0;
                foreach($arry_book_category_rev_cno as $val){
                    if((int)$val===0)$category_rev_cno_val_0++;
                }
                if($category_rev_cno_val_0===count($arry_book_category_rev_cno))$arry_book_category_rev_cno=array();
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
                        AND `mssr_forum`.`mssr_forum_article`.`article_from` IN (1,2) -- 文章來源
                        AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                        AND `mssr_forum`.`mssr_forum_article`.`user_id`      ={$user_id}

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

                    $rs_name            =trim($rs_name);
                    $rs_book_sid        =trim($rs_book_sid);
                    $rs_keyin_cdate     =trim($rs_keyin_cdate);
                    $rs_article_title   =trim($rs_article_title);
                    $rs_article_content =trim($rs_article_content);
                    $rs_reply_content   =trim($rs_reply_content);
                    $rs_type            =trim($rs_type);
                    $rs_keyin_time      =strtotime($rs_keyin_cdate);

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
                }
                //時間排序
                krsort($article_reply_results);
            }

        //-----------------------------------------------
        //小組 SQL
        //-----------------------------------------------

        //-----------------------------------------------
        //書友 SQL
        //-----------------------------------------------

            $friend_results=get_forum_friend($user_id,$friend_id=0,$arry_conn_mssr);

        //-----------------------------------------------
        //推薦書友 SQL
        //-----------------------------------------------

            //取得書友名單
            $arry_forum_friend=array();
            $forum_friend_list='';
            if(!empty($friend_results)){
                foreach($friend_results as $friend_result){
                    if((int)$friend_result['friend_state']===1){
                        if((int)$friend_result['user_id']!==$sess_user_id)$arry_forum_friend[]=$friend_result['user_id'];
                        if((int)$friend_result['friend_id']!==$sess_user_id)$arry_forum_friend[]=$friend_result['friend_id'];
                    }
                }
                $forum_friend_list=implode(',',$arry_forum_friend);
            }

            $rec_friend_results=array();
            $json_rec_friend_results=json_encode(array(),true);
            if(!empty($arrys_sess_login_info)){
                if(isset($sess_arry_class_info)&&!empty($sess_arry_class_info)&&count($arry_forum_friend)<10){
                    $sess_class_code='';
                    $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                    foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                        $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        $sess_class_code.="'".addslashes(trim($sess_arry_class_info['class_code']))."'";
                        if($inx!==count($sess_arrys_class_info)-1)$sess_class_code.=",";
                    }
                    $sql="
                        SELECT
                            `user`.`member`.`uid`,
                            `user`.`member`.`name`,
                            `user`.`member`.`sex`
                        FROM `user`.`member`
                            INNER JOIN `user`.`student` ON
                            `user`.`member`.`uid`=`user`.`student`.`uid`
                        WHERE 1=1
                            AND `user`.`student`.`class_code` IN ({$sess_class_code})
                            AND CURDATE() BETWEEN `user`.`student`.`start` AND `user`.`student`.`end`
                    ";
                    $rec_friend_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    if(!empty($rec_friend_results)){
                        foreach($rec_friend_results as $inx=>$rec_friend_result){
                            if(in_array((int)$rec_friend_result['uid'],$arry_forum_friend))unset($rec_friend_results[$inx]);
                            if((int)$rec_friend_result['uid']===$sess_user_id)unset($rec_friend_results[$inx]);
                        }
                        Shuffle($rec_friend_results);
                        $json_rec_friend_results=json_encode($rec_friend_results,true);
                    }
                }
            }

        //-----------------------------------------------
        //邀請 SQL
        //-----------------------------------------------

            $request_results=get_request_info($sess_user_id,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);

        //-----------------------------------------------
        //追蹤的書籍 SQL
        //-----------------------------------------------

        //-----------------------------------------------
        //追蹤的小組 SQL
        //-----------------------------------------------

        //-----------------------------------------------
        //追蹤的文章 SQL
        //-----------------------------------------------

        //-----------------------------------------------
        //書友推薦給我的書 SQL
        //-----------------------------------------------

        //-----------------------------------------------
        //用戶樣式 SQL
        //-----------------------------------------------

            $style_id=1;
            $style_from=1;
            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_style_user_rev`.`style_id`,
                    `mssr_forum`.`mssr_forum_style_user_rev`.`style_from`
                FROM `mssr_forum`.`mssr_forum_style_user_rev`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_style_user_rev`.`user_id`={$user_id}
            ";
            $style_user_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(!empty($style_user_results)){
                $style_id  =(int)$style_user_results[0]['style_id'];
                $style_from=(int)$style_user_results[0]['style_from'];
            }

        //-----------------------------------------------
        //班級設置 SQL
        //-----------------------------------------------

            $setting_class_user_upload=1;
            if(isset($sess_arrys_class_info[0]['class_code'])&&trim($sess_arrys_class_info[0]['class_code'])!==''){
                $sess_class_code=addslashes(trim($sess_arrys_class_info[0]['class_code']));
                $sql="
                    SELECT `mssr_forum`.`mssr_forum_setting_class`.`setting`
                    FROM `mssr_forum`.`mssr_forum_setting_class`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_setting_class`.`class_code`='{$sess_class_code}'
                ";
                $setting_class_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                $setting_class_user_upload=(int)json_decode($setting_class_results[0]['setting'],true)['user_upload'];
            }

        //-----------------------------------------------
        //個人資訊 SQL
        //-----------------------------------------------

            $user_content='無';
            $sql="
                SELECT *
                FROM `mssr_forum`.`mssr_forum_user_info`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_user_info`.`user_id`={$user_id}
            ";
            $user_info_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(!empty($user_info_results)){
                $user_content=trim($user_info_results[0]['user_content']);
            }

		//-----------------------------------------------
		//請求推薦書籍限制 SQL
		//-----------------------------------------------
			$today_date = date("Y-m-d");
			$deadline = date("Y-m-d", strtotime('-6 day'));

			$sql = "
				SELECT `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev`.`request_id`
				FROM `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev`
					INNER JOIN `mssr_forum`.`mssr_forum_user_request`
					ON `mssr_forum`.`mssr_forum_user_request`.`request_id` = `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev`.`request_id`
				WHERE 1=1
					AND `mssr_forum`.`mssr_forum_user_request`.`request_from` = {$user_id}
					AND `mssr_forum`.`mssr_forum_user_request`.`keyin_cdate` BETWEEN '{$deadline} 00:00:00' AND '{$today_date} 23:59:59'
			";
			$request_book_results = db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

		//-----------------------------------------------
		//取得積分 SQL
		//-----------------------------------------------
			$rank_and_point = get_rank_and_point($user_id,'','',$arry_conn_user,$arry_conn_mssr);
			$rank = $rank_and_point["total_rank"];

		//-----------------------------------------------
		//取得頭銜 SQL
		//-----------------------------------------------
			$appellation = get_appellation($rank,'','',$arry_conn_user,$arry_conn_mssr);
			$appellation_name = $appellation["appellation_name"];
			$appellation_mark = $appellation["appellation_mark"];
			$appellation_color_id = $appellation["appellation_color_id"];

		//-----------------------------------------------
		//取得下一個頭銜所需的積分 SQL
		//-----------------------------------------------
			$sql="
				SELECT `mssr_forum`.`mssr_forum_appellation`.`required_rank`
				FROM `mssr_forum`.`mssr_forum_appellation`
				WHERE 1=1
					AND `mssr_forum`.`mssr_forum_appellation`.`required_rank` > $rank
				ORDER BY `mssr_forum`.`mssr_forum_appellation`.`required_rank` ASC
				LIMIT 0, 1
			";

			$results_required_rank = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

			if (!empty($results_required_rank)) {
				$required_rank = $results_required_rank[0]['required_rank'];
			}

			if ($required_rank == 0) {
				$required_rank = 700;
			}

			$next_appellation_need_rank = $required_rank - $rank;

		//-----------------------------------------------
		//取得親密度 SQL
		//-----------------------------------------------
			$friend_intimate = get_friend_intimate($sess_user_id,$user_id,'','',$arry_conn_user,$arry_conn_mssr);

		//-----------------------------------------------
        //頭銜顏色
        //-----------------------------------------------
			$sql="
				SELECT 
					`mssr_forum`.`mssr_forum_appellation_color`.`font_color`,
					`mssr_forum`.`mssr_forum_appellation_color`.`background_color`
				FROM `mssr_forum`.`mssr_forum_appellation_color`
				WHERE 1=1
					AND `mssr_forum`.`mssr_forum_appellation_color`.`appellation_color_id` = $appellation_color_id
				LIMIT 0, 1
			";

			$results_appellation_color = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

			if (!empty($results_appellation_color)) {
				$font_color = $results_appellation_color[0]['font_color'];
				$background_color = $results_appellation_color[0]['background_color'];
			}

        //-----------------------------------------------
        //個人大頭貼
        //-----------------------------------------------

            //FTP 路徑
            if(isset($file_server_enable)&&($file_server_enable)){
                //$ftp_root="public_html/mssr/info/user";
                //$ftp_path="{$ftp_root}/{$user_id}/forum/user_sticker";
                //
                ////連接 | 登入 FTP
                //$ftp_conn  =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                //$ftp_login =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                //
                ////設定被動模式
                //ftp_pasv($ftp_conn,TRUE);
                //
                ////獲取檔案目錄
                //$arry_ftp_file=ftp_nlist($ftp_conn,$ftp_path);
                //
                //if(!empty($arry_ftp_file)){
                //    $user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$user_id}/forum/user_sticker/1.jpg";
                //    $user_img_size=getimagesize($user_img);
                //}
                if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$user_id}/forum/user_sticker/1.jpg")){
                    $user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$user_id}/forum/user_sticker/1.jpg";
                    $user_img_size=getimagesize($user_img);
                }
            }else{
                if(file_exists("../../../info/user/{$user_id}/forum/user_sticker/1.jpg")){
                    $user_img="../../../info/user/{$user_id}/forum/user_sticker/1.jpg";
                    $user_img_size=getimagesize($user_img);
                }
            }

        //-----------------------------------------------
        //載入模態框
        //-----------------------------------------------

            $modal_dialog_add_friend=modal_dialog($rd=1,$type=3);

            $new_modal_dialog=modal_dialog($rd=1,$type=4);

        //-----------------------------------------------
        //書籍類別
        //-----------------------------------------------

            $book_category_results=array();

        //-----------------------------------------------
        //書籍類別關聯
        //-----------------------------------------------

            $arrys_book_category_rev=array();
            $json_book_category_rev=json_encode($arrys_book_category_rev,true);

        //-----------------------------------------------
        //按鈕設置
        //-----------------------------------------------

            //加為書友
            $btn_add_friend_show=true;
            $btn_add_friend_disabled=false;
            $btn_add_friend_html=trim('加為書友');
            if($sess_user_id!==$user_id){
                $get_forum_friend=get_forum_friend($sess_user_id,$user_id,$arry_conn_mssr);
                if(empty($get_forum_friend)){
                    $btn_add_friend_show=true;
                    $btn_add_friend_disabled=false;
                    $btn_add_friend_html=trim('加為書友');
                }else{
                    if((int)$get_forum_friend[0]['friend_state']===1){$btn_add_friend_show=true;$btn_add_friend_disabled=true;$btn_add_friend_html=trim('已是書友');}
                    if((int)$get_forum_friend[0]['friend_state']===2){$btn_add_friend_show=true;$btn_add_friend_disabled=false;$btn_add_friend_html=trim('加為書友');}
                    if((int)$get_forum_friend[0]['friend_state']===3){$btn_add_friend_show=true;$btn_add_friend_disabled=true;$btn_add_friend_html=trim('書友確認中');}
                }
            }else{
                $btn_add_friend_show=false;
                $btn_add_friend_disabled=true;
            }

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

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-116055812-1"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'UA-116055812-1');
</script>

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
            <div class="jumbotron hidden-xs" style="background-image:url('../img/default/front_cover_user.jpg');background-position:center top;background-size:100% auto;">

                <!-- 大頭貼,大解析度,start -->
                <img class="jumbotron_img hidden-xs"
                src="<?php echo $user_img;?>"
                width="160" height="160" border="0" alt="user_img"
                onclick="location.href='user.php?user_id=<?php echo $user_id;?>&tab=1'"
                onmouseover='this.style.cursor="pointer";'/>
                <!-- 大頭貼,大解析度,end -->

                <!-- jumbotron_name,start -->
                <span class="jumbotron_name">
                    <?php 
                    	$mark = "<span style='font-weight: normal;'>" . $appellation_mark . "</span>";
                    	echo htmlspecialchars($user_name); ?>
                    <?php 
if ($use_new_system) {
    echo "（頭銜：<span style='color:$font_color;background-color:$background_color;'>" . $mark . $appellation_name . $mark . "</span>）";
}
                     ?>
<?php if ($use_new_system) { ?>
                    <?php if($sess_user_id===$user_id):?>
                    	<?php if($appellation_name!="精緻遠古石刻"):?>
							<div width="300" style="font-size:16px;">			
								距離下一個頭銜還差 <font color="#ffff00"><?php echo $next_appellation_need_rank;?></font> 積分
							</div>
						<?php endif;?>
                    <?php endif;?>
<?php } ?>
                    <?php if(!empty($arry_user_school_results)):?>
                        <div style='font-size:12px;'>
                        <?php foreach($arry_user_school_results as $arry_user_school_result):?>
                        <?php
                            $cno             =(int)0;
                            $user_school_name=trim($arry_user_school_result['school_name']);
                            $user_grade      =(int)$arry_user_school_result['grade'];
                            $user_class_name =trim($arry_user_school_result['class_name']);
                        ?>
                            <?php echo $user_school_name.$user_grade.'年'.htmlspecialchars($user_class_name).'班';?>
                            <?php if($cno!==(count($arry_user_school_results)-1))echo '，';?>
                        <?php $cno++;endforeach;?>
                        </div>
                    <?php endif;?>
                    <div style='font-size:12px;'>已經讀了 <font color="#ffff00"><?php echo count($book_results);?></font> 本書</div>
                    <div style='font-size:12px;'>發表了 <font color="#ffff00"><?php echo $add_article_cno;?></font> 篇文章    </div>
                    <div style='font-size:12px;'>已回覆 <font color="#ffff00"><?php echo $reply_article_cno;?></font> 篇文章  </div>
<?php if ($use_new_system) { ?>
                    <?php if($sess_user_id!==$user_id && isset($get_forum_friend[0]) && (int)$get_forum_friend[0]['friend_state']===1):?>
                    	<div style='font-size:12px;'>
                    		你與<?php echo htmlspecialchars($user_name); ?>之間的親密度為 <font color="#ffff00"><?php echo $friend_intimate; ?></font> 點	
                    	</div>
                    <?php endif;?>
<?php } ?>
                </span>
                <!-- jumbotron_name,end -->

            </div>
            <!-- jumbotron,end -->

            <!-- jumbotron-xs,start -->
            <div class="jumbotron jumbotron-xs hidden-sm hidden-md hidden-lg" style="background-image:url('../img/default/front_cover_user.jpg');background-position:center top;background-size:100% auto;">

                <!-- 大頭貼,小解析度,start -->
                <img class="jumbotron-xs_img hidden-sm hidden-md hidden-lg"
                src="<?php echo $user_img;?>"
                width="100" height="100" border="0" alt="user_img"
                onclick="location.href='user.php?user_id=<?php echo $user_id;?>&tab=1'"
                onmouseover='this.style.cursor="pointer";'/>
                <!-- 大頭貼,小解析度,end -->

                <!-- jumbotron-xs_name,start -->
                <span class="jumbotron-xs_name"><?php echo htmlspecialchars($user_name);?></span>
                <!-- jumbotron-xs_name,end -->

            </div>
            <!-- jumbotron-xs,end -->

            <!-- page_info,大解析度,start -->
            <div class="page_info hidden-xs">
                <table class="table hidden-xs" border="1">
                    <tbody><tr>
                        <td width="215px">&nbsp;</td>
                        <td width="250px" align="center">
                            <?php if($btn_add_friend_show):?>
                                <!-- <button type="button" class="btn_add_friend btn btn-default btn-xs"
                                <?php if($btn_add_friend_disabled)echo 'disabled="disabled"';?>
                                user_id=<?php echo $sess_user_id;?>
                                friend_id=<?php echo $user_id;?>><?php echo $btn_add_friend_html;?></button> -->
                                <button class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal_dialog_add_friend"
                                onclick="
                                $('#add_friend_title').text('邀請<?php echo htmlspecialchars($user_name);?>當你的書友');
                                $('.btn_add_friend').attr('friend_id',<?php echo (int)$user_id;?>);
                                void(0);"
                                <?php if($btn_add_friend_disabled)echo 'disabled="disabled"';?>>
                                    <?php echo $btn_add_friend_html;?>
                                </button>
                            <?php endif;?>

                            <?php if(isset($get_forum_friend[0])&&(int)$get_forum_friend[0]['friend_state']===1):?>
                                <button type="button" class="btn_del_friend btn btn-default btn-xs"
                                friend_id=<?php echo $user_id;?>>取消書友</button>
                            <?php endif;?>

                            <!-- <?php if($sess_user_id!==$user_id&&isset($get_forum_friend[0])&&(int)$get_forum_friend[0]['friend_state']===1):?>
                                <button type="button" class="btn btn-default btn-xs"
                                onclick="add_request_rec_us_book(this,<?php echo (int)$user_id;?>);void(0);">請求推薦書籍</button>
                            <?php endif;?> -->
                        </td>
                        <td align="center"><!-- <span>已經讀了 <?php echo count($book_results);?> 本書</span> --></td>
                        <td align="center"><!-- <span>發表了 <?php echo $add_article_cno;?> 篇文章    </span> --></td>
                        <td align="center"><!-- <span>已回覆 <?php echo $reply_article_cno;?> 篇文章  </span> --></td>
                    </tr></tbody>
                </table>
            </div>
            <!-- page_info,大解析度,end -->

            <!-- page_info,小解析度,start -->
            <?php if($sess_user_id!==$user_id):?>
            <div class="page_info hidden-sm hidden-md hidden-lg">
                <table class="table hidden-sm hidden-md hidden-lg" border="1">
                    <tbody><tr>
                        <td align="center">
                            <?php if(isset($get_forum_friend)&&(int)$get_forum_friend[0]['friend_state']===1):?>
                                <button type="button" class="btn_del_friend btn btn-default btn-xs pull-right"
                                style="position:relative;top:3px;"
                                friend_id=<?php echo $user_id;?>>取消書友</button>
                            <?php endif;?>

                            <?php if($btn_add_friend_show):?>
                                <!-- <button type="button" class="btn_add_friend btn btn-default btn-xs pull-right"
                                style="position:relative;top:3px;"
                                <?php if($btn_add_friend_disabled)echo 'disabled="disabled"';?>
                                user_id=<?php echo $sess_user_id;?>
                                friend_id=<?php echo $user_id;?>><?php echo $btn_add_friend_html;?></button> -->

                                <button class="btn btn-default btn-xs pull-right" data-toggle="modal" data-target="#modal_dialog_add_friend"
                                style="position:relative;top:3px;"
                                onclick="
                                $('#add_friend_title').text('邀請<?php echo htmlspecialchars($user_name);?>當你的書友');
                                $('.btn_add_friend').attr('friend_id',<?php echo (int)$user_id;?>);
                                void(0);"
                                <?php if($btn_add_friend_disabled)echo 'disabled="disabled"';?>>
                                    <?php echo $btn_add_friend_html;?>
                                </button>
                            <?php endif;?>
                        </td>
                    </tr></tbody>
                </table>
            </div>
            <?php endif;?>
            <!-- page_info,小解析度,end -->

            <!-- user_lefe_side,start -->
            <div class="user_lefe_side col-xs-12 col-sm-10 col-md-10 col-lg-10">
                <ul id="myTab" class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="<?php if($tab===1)echo 'active';?>"><a href="#home" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">
                        首頁
                    </a></li>
                    <li role="presentation" class="<?php if($tab===2)echo 'active';?>"><a href="#book" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile"
                    onclick="user_blade(2);">
                        書櫃
                    </a></li>
                    <li role="presentation" class="<?php if($tab===3)echo 'active';?>"><a href="#article" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile"
                    onclick="user_blade(3);">
                        討論
                    </a></li>
                    <li role="presentation" class="<?php if($tab===5)echo 'active';?> hidden-xs"><a href="#friend" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile"
                    onclick="user_blade(5);">
                        書友
                    </a></li>
                    <li role="presentation" class="<?php if($tab===4)echo 'active';?> hidden-xs"><a href="#group" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile"
                    onclick="user_blade(4);">
                        小組
                    </a></li>
                    <?php if($sess_user_id===$user_id):?>
                        <li role="presentation" class="<?php if($tab===6)echo 'active';?> hidden-xs"><a href="#request" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile">
                            訊息及邀請
                        </a></li>
                        <li role="presentation" class="<?php if($tab===7)echo 'active';?> hidden-xs"><a href="#track" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile"
                        onclick="user_blade(7);">
                            追蹤及收藏
                        </a></li>
                        <?php if($setting_class_user_upload===1):?>
                            <li role="presentation" class="<?php if($tab===8)echo 'active';?> hidden-xs"><a href="#user_info" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile">
                                編輯我的頁面
                            </a></li>
                        <?php endif;?>
<?php if ($use_new_system) { ?>
						<li role="presentation" class="<?php if($tab===9)echo 'active';?> hidden-xs"><a href="#message" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile"
						onclick="user_blade(9);">
							紀錄
						</a></li>
<?php } ?>
                    <?php endif;?>
                    <li role="presentation" class="dropdown hidden-sm hidden-md hidden-lg">
                        <a href="javascript:void(0);" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown" aria-controls="myTabDrop1-contents">更多&nbsp;<span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="myTabDrop1" id="myTabDrop1-contents">
                            <li role="presentation" class="<?php if($tab===5)echo 'active';?>"><a href="#friend" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile"
                            onclick="user_blade(5);">
                                書友
                            </a></li>
                            <li role="presentation" class="<?php if($tab===4)echo 'active';?>"><a href="#group" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile"
                            onclick="user_blade(4);">
                                小組
                            </a></li>
                            <?php if($sess_user_id===$user_id):?>
                                <li role="presentation" class="<?php if($tab===6)echo 'active';?>"><a href="#request" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile">
                                    訊息及邀請
                                </a></li>
                                <li role="presentation" class="<?php if($tab===7)echo 'active';?>"><a href="#track" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile"
                                onclick="user_blade(7);">
                                    追蹤及收藏
                                </a></li>
                                <?php if($setting_class_user_upload===1):?>
                                    <li role="presentation" class="<?php if($tab===8)echo 'active';?>"><a href="#user_info" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile">
                                        編輯我的頁面
                                    </a></li>
                                <?php endif;?>
                            <?php endif;?>
                        </ul>
                    </li>
                </ul>
                <div id="myTabContent" class="tab-content">

                    <!-- 首頁 -->
                    <div role="tabpanel" class="tab-pane fade <?php if($tab===1)echo 'in active';?>" id="home" aria-labelledBy="home-tab">

                        <!-- 推薦書友,start -->
                        <?php if($sess_user_id===$user_id):?>
                            <?php if(isset($rec_friend_results)&&!empty($rec_friend_results)):?>
                            <div class="row rec_friend" style="position:relative;margin-top:-10px;margin-bottom:-20px;">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 rec_friend_title">
                                    <div class="tab_title" style="margin-top:15px;margin-bottom:15px;">
                                        你可能認識的人......
                                        <a href="javascript:void(0);" class="btn btn-xs btn-primary"
                                        role="button" style="color:#ffffff;"
                                        onclick="refresh_rec_friend(this);void(0);"
                                        >手動刷新</a>
                                    </div>
                                </div>
                                <?php
                                    $cno=0;
                                    foreach($rec_friend_results as $rec_friend_result):
                                        if($cno<6){
                                            $rs_user_id  =(int)$rec_friend_result['uid'];
                                            $rs_user_sex =(int)$rec_friend_result['sex'];
                                            $rs_user_name=trim($rec_friend_result['name']);
                                            if($rs_user_sex===1)$rs_user_img='../img/default/user_boy.png';
                                            if($rs_user_sex===2)$rs_user_img='../img/default/user_girl.png';
                                ?>
                                    <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 rec_friend_content">
                                        <div class="thumbnail">
                                            <img class='img-responsive' src="<?php echo $rs_user_img;?>" alt="thumbnail" style="height:70px;">
                                            <div class="caption" style="text-align:center;">
                                                <h3><?php echo htmlspecialchars($rs_user_name);?></h3>
                                                <p><a href="javascript:void(0);" class="btn_add_friend btn btn-primary btn-xs" role="button" style="color:#ffffff;"
                                                user_id=<?php echo $sess_user_id;?>
                                                friend_id=<?php echo $rs_user_id;?>
                                                >加為書友</a></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php $cno++;}endforeach;?>
                            </div>
                            <?php endif;?>
                        <?php endif;?>
                        <!-- 推薦書友,end -->

                        <!-- 個人資訊 -->
                        <div class="tab_title"><?php echo htmlspecialchars($user_name);?>&nbsp;的個人資訊<!-- &nbsp;<?php for($i=0;$i<15;$i++):?>‧<?php endfor;?> --></div>
                        <div class="dashed_ccc"></div>
                        <div class="media">
                            <img class="media-object pull-left hidden" src="<?php echo $user_img;?>" alt="media-object" width="64" height="64" border="0">
                            <div class="media-body">
                                <h4 class="media-heading hidden">
                                    <?php if(!empty($arry_user_school_results)):?>
                                        <?php foreach($arry_user_school_results as $arry_user_school_result):?>
                                        <?php
                                            $cno             =(int)0;
                                            $user_school_name=trim($arry_user_school_result['school_name']);
                                            $user_grade      =(int)$arry_user_school_result['grade'];
                                            $user_class_name =trim($arry_user_school_result['class_name']);
                                        ?>
                                            <?php echo $user_school_name.$user_grade.'年'.htmlspecialchars($user_class_name).'班';?>
                                            <?php if($cno!==(count($arry_user_school_results)-1))echo '，';?>
                                        <?php $cno++;endforeach;?>
                                    <?php endif;?>
                                    <?php echo '，'.htmlspecialchars($user_name);?>
                                </h4>
                                <?php if($sess_user_id===$user_id):?>
                                    <form id="form_user_info" name="form_user_info" method="post" onsubmit="return false;">
                                        <div class="input-group" style="position:relative;margin:10px 0;">
                                            <span class="input-group-addon">自我介紹</span>
                                            <input class="form-control" type="text" id="user_content" name="user_content" maxlength="100"
                                            value="<?php echo htmlspecialchars($user_content);?>" placeholder="請輸入自我介紹" required>
                                        </div>
                                        <hr></hr>
                                        <div class="form-group pull-right" style="position:relative;">
                                            <button type="button" class="btn btn-default btn-xs" onclick="edit_user_info();void(0);">更新</button>
                                        </div>
                                        <div class="form-group hidden">
                                            <input type="text" class="form-control" name="method" value="edit_user_info">
                                            <input type="text" class="form-control" name="send_url" value="<?php echo trim($send_url);?>">
                                        </div>
                                    </form>
                                <?php else:?>
                                    <p style="position:relative;margin:10px 0;">自我介紹：<?php echo htmlspecialchars($user_content);?></p>
                                <?php endif;?>
                            </div>
                        </div>

                        <!-- 近期發文 -->
                        <div class="tab_title"><?php echo htmlspecialchars($user_name);?>&nbsp;最近討論發文、回文<!-- &nbsp;<?php for($i=0;$i<15;$i++):?>‧<?php endfor;?> --></div>
                        <div class="dashed_ccc"></div>
                        <table class="user_lefe_side_tab1 table table-striped">
                            <thead><tr class="second_tr" align="left">
                                <td width=""><span>標題</span>                      </td>
                                <td width="100px"><span>發表時間</span>             </td>
                                <td width="20px"class="hidden-xs"><span>讚</span>   </td>
                            </tr></thead>
                            <tbody>
                                <?php
                                if(!empty($article_reply_results)){
                                    $cno=0;
                                    foreach($article_reply_results as $article_reply_result):
                                        extract($article_reply_result, EXTR_PREFIX_ALL, "rs");
                                    //筆數控制
                                    if($cno<5){
                                        $rs_user_id         =(int)$rs_user_id;
                                        $rs_group_id        =(int)$rs_group_id;
                                        $rs_article_id      =(int)$rs_article_id;
                                        $rs_reply_id        =(int)$rs_reply_id;
                                        $rs_like_cno        =(int)$rs_like_cno;
                                        $rs_book_sid        =trim($rs_book_sid);
                                        $rs_keyin_mdate     =trim($rs_keyin_mdate);
                                        $rs_article_title   =trim($rs_article_title);
                                        $rs_article_content =trim($rs_article_content);
                                        $rs_reply_content   =trim($rs_reply_content);
                                        $rs_type            =trim($rs_type);

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

                                        if(mb_strlen($rs_article_content)>100){
                                            $rs_article_content=mb_substr($rs_article_content,0,100)."..";
                                        }

                                        if(mb_strlen($rs_reply_content)>100){
                                            $rs_reply_content=mb_substr($rs_reply_content,0,100)."..";
                                        }

                                        //特殊處理
                                        $rs_book_name='';
                                        if($get_from===1){
                                            $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                            if(!empty($arry_book_infos)){
                                                $rs_book_name=trim($arry_book_infos[0]['book_name']);
                                                $all_book_name = $rs_book_name;
                                            }else{
                                                continue;
                                            }
                                            if(mb_strlen($rs_book_name)>10){
                                                $all_book_name = $rs_book_name;
                                                $rs_book_name=mb_substr($rs_book_name,0,10)."..";
                                            }
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
                                            if(mb_strlen($rs_group_name)>10){
                                                $rs_group_name=mb_substr($rs_group_name,0,10)."..";
                                            }
                                        }
                                        if($rs_book_name==='')continue;
                                ?>
                                <tr align="left">
                                    <td style="border:0px;word-break:break-all;overflow:hidden;">
                                        <a target="_blank" href="reply.php?get_from=<?php echo $get_from;?>&article_id=<?php echo $rs_article_id;?>">
                                            <span style="font-size:12pt;">● <?php echo htmlspecialchars($rs_article_title);?></span><br>
                                            <span style="font-size:10pt;float:right;margin-top:5px;" style="text-decoration:none;" data-toggle="tooltip" title="<?php echo $all_book_name;?>"> <!-- 新增完整書名 -->
                                                <img width="20" height="20" style="weight:20px;height:20px;" src="../img/default/book.png" alt="book.png">
                                                <?php echo htmlspecialchars($rs_book_name);?>
                                                <?php if($rs_group_id!==0):?>
                                                    | <img width="20" height="20" style="weight:20px;height:20px;" src="../img/default/group.jpg" alt="group.jpg">
                                                    <?php echo htmlspecialchars($rs_group_name);?>
                                                <?php endif;?>
                                            </span>
                                        </a>
                                    </td>
                                    <td style="border:0px;"><?php echo htmlspecialchars($rs_keyin_mdate);?></td>
                                    <td class="hidden-xs" style="border:0px;"><?php echo htmlspecialchars($rs_like_cno);?></td>
                                </tr>
                                <?php $cno++;}endforeach;}else{?>
                                    <tr align="center"><td colspan="4" style="border:0px;font-size:16px;">查無文章資訊。</td></tr>
                                <?php }?>
                            </tbody>
                        </table>

                        <!-- 近期書籍 -->
                        <div class="tab_title"><?php echo htmlspecialchars($user_name);?>&nbsp;最近閱讀書籍<!-- &nbsp;<?php for($i=0;$i<15;$i++):?>‧<?php endfor;?> --></div>
                        <div class="dashed_ccc"></div>
                        <?php if(!empty($book_results)){ ?>
                            <div class="user_lefe_side_tab2 row">
                                <?php
                                    $cno=0;
                                    foreach($book_results as $inx=>$book_result):
                                    //本數控制
                                    if($cno<6){
                                        $rs_book_sid=trim($book_result['book_sid']);
                                        if($rs_book_sid!==''){
                                            $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                            if(empty($arry_book_infos))continue;
                                            $rs_book_name=trim($arry_book_infos[0]['book_name']);
                                            $all_book_name = $rs_book_name;
                                            if(mb_strlen($rs_book_name)>20){
                                                $all_book_name = $rs_book_name;
                                                $rs_book_name=mb_substr($rs_book_name,0,20)."..";
                                            }
                                            if(trim($rs_book_name)==='')continue;
                                            $rs_book_img    ='../img/default/book.png';
                                            if(file_exists("../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg")){
                                                $rs_book_img="../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg";
                                            }
                                            if(preg_match("/^mbu/i",$rs_book_sid)){
                                                $get_book_info=get_book_info($conn_mssr,trim($rs_book_sid),$array_filter=array('book_verified'),$arry_conn_mssr);
                                                if(!empty($get_book_info)){
                                                    $rs_book_verified=(int)$get_book_info[0]['book_verified'];
                                                    if($rs_book_verified===2)continue;
                                                }else{continue;}
                                            }
                                        }
                                ?>
                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                                    <div class="thumbnail">
                                        <a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_book_sid);?>">
                                            <img width="80" height="80" style="weight:80px;height:80px;" src="<?php echo $rs_book_img;?>" alt="Generic placeholder thumbnail">
                                            <div class="caption" style="text-decoration:none;" data-toggle="tooltip" title="<?php echo $all_book_name;?>"><?php echo htmlspecialchars($rs_book_name);?></div>   <!-- 新增完整書名 -->
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

                        <!-- 近期書友 -->
                        <div class="tab_title"><?php echo htmlspecialchars($user_name);?>&nbsp;最近新增書友<!-- &nbsp;<?php for($i=0;$i<15;$i++):?>‧<?php endfor;?> --></div>
                        <div class="dashed_ccc"></div>
                        <?php if(!empty($friend_results)){ ?>
                            <div class="user_lefe_side_tab3 row">
                                <?php
                                    $cno=0;
                                    foreach($friend_results as $inx=>$friend_result):
                                        $rs_friend_state=(int)$friend_result['friend_state'];
                                    //人數控制
                                    if($cno<6&&$rs_friend_state===1){
                                        $rs_user_id     =(int)$friend_result['user_id'];
                                        $rs_friend_id   =(int)$friend_result['friend_id'];
                                        if($rs_user_id!==$user_id || $rs_friend_id!==$user_id){
                                            $tmp_user_id=0;
                                            if($rs_user_id!==$user_id)$tmp_user_id=$rs_user_id;
                                            if($rs_friend_id!==$user_id)$tmp_user_id=$rs_friend_id;
                                            $sql="
                                                SELECT
                                                    `name`,`sex`
                                                FROM `user`.`member`
                                                WHERE 1=1
                                                    AND `user`.`member`.`uid`={$tmp_user_id}
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
                                        }
                                        if(isset($file_server_enable)&&($file_server_enable)){
                                            //$rs_user_img_ftp_path     ="{$ftp_root}/{$tmp_user_id}/forum/user_sticker";
                                            //$arry_rs_user_img_ftp_file=ftp_nlist($ftp_conn,$rs_user_img_ftp_path);
                                            //if(isset($arry_rs_user_img_ftp_file[0])){
                                            //    $rs_user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$tmp_user_id}/forum/user_sticker/1.jpg";
                                            //}
                                            if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$tmp_user_id}/forum/user_sticker/1.jpg")){
                                                $rs_user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$tmp_user_id}/forum/user_sticker/1.jpg";
                                            }
                                        }else{
                                            if(file_exists("../../../info/user/{$tmp_user_id}/forum/user_sticker/1.jpg")){
                                                $rs_user_img="../../../info/user/{$tmp_user_id}/forum/user_sticker/1.jpg";
                                            }
                                        }
                                ?>
                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                                    <div class="thumbnail">
                                        <a href="user.php?user_id=<?php echo $tmp_user_id;?>&tab=1">
                                            <img width="80" height="80" style="weight:80px;height:80px;" src="<?php echo $rs_user_img;?>" alt="Generic placeholder thumbnail">
                                            <div class="caption"><?php echo htmlspecialchars($rs_user_name);?></div>
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
                                    <tr align="center"><td style="border:0px;font-size:16px;">查無書友資訊。</td></tr>
                                </tbody>
                            </table>
                        <?php }?>
                    </div>

                    <!-- 編輯我的頁面 -->
                    <div role="tabpanel" class="tab-pane fade <?php if($tab===8)echo 'in active';?>" id="user_info" aria-labelledBy="profile-tab">
                        <div class="user_lefe_side_tab3 row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="panel-group" id="panel-user_info">

                                    <div class="panel panel-default hidden-xs">
                                        <div class="panel-heading">
                                            <a data-toggle="collapse" data-parent="#panel-user_info" href="#collapseFive">
                                                <h4 class="panel-title">
                                                    個人大頭貼設定
                                                </h4>
                                            </a>
                                        </div>
                                        <div id="collapseFive" class="panel-collapse collapse in">
                                            <div class="panel-body">
                                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                                    <pre style="background-color:#ffffdd;">使用中大頭貼</pre>
                                                    <img src="<?php echo $user_img;?>" style="border:1px solid #e1e1e1;"
                                                    width="160" height="160" border="0" alt="user_img"/>
                                                    <hr></hr>
                                                    <form action="../controller/img.php" method="post" class="ajax_user_sticker_form">
                                                        <!-- <span class="btn btn-default btn-xs btn_file">
                                                            重新選擇<input type="file" name="user_sticker_file" class="user_sticker_file">
                                                        </span> -->
                                                        <input type="file" name="user_sticker_file" class="user_sticker_file btn btn-default btn-xs"
                                                        style="float:left;position:relative;top:-2px;width:75%;">
                                                        <button type="button" class="btn btn-default btn-xs"
                                                        onclick="ajax_user_sticker_upload(this);void(0);"
                                                        >上傳</button>
                                                        <input type="hidden" class="form-control" name="method" value="add_user_sticker_img">
                                                        <input type="hidden" class="form-control" name="send_url" value="#">
                                                    </form>
                                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="color:red;">
                                                        <br>
                                                        <p class="text-left">1.檔案類型限定為.jpg檔案</p>
                                                        <p class="text-left">2.檔案大小不得超過100KB</p>
                                                    </div>
                                                </div>
                                                <?php if(isset($user_img_size)):?>
                                                    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                                                        <pre style="background-color:#ffffdd;">裁切大頭貼</pre>
                                                        <div class="row">
                                                            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                                                                <img id="old_user_sticker" src="<?php echo $user_img;?>"
                                                                style="border:1px solid #e1e1e1;" border="0" alt="user_img"
                                                                width="<?php echo $user_img_size[0];?>" height="<?php echo $user_img_size[1];?>"/>
                                                            </div>
                                                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                                                <div style="overflow:hidden;width:160px;height:160px;">
                                                                    <img id="new_user_sticker" src="<?php echo $user_img;?>"
                                                                    style="border:1px solid #e1e1e1;" border="0" alt="user_img"
                                                                    width="<?php echo $user_img_size[0];?>" height="<?php echo $user_img_size[1];?>"/>
                                                                </div>
                                                                <hr></hr>
                                                                <form action="../controller/img.php" method="post" class="edit_user_sticker_form">
                                                                    <input type="hidden" name="user_sticker_x1" value="0"   id="user_sticker_x1">
                                                                    <input type="hidden" name="user_sticker_y1" value="0"   id="user_sticker_y1">
                                                                    <input type="hidden" name="user_sticker_x2" value="0"   id="user_sticker_x2">
                                                                    <input type="hidden" name="user_sticker_y2" value="0"   id="user_sticker_y2">
                                                                    <input type="hidden" name="user_sticker_w"  value="160" id="user_sticker_w">
                                                                    <input type="hidden" name="user_sticker_h"  value="160" id="user_sticker_h">
                                                                    <input type="hidden" class="form-control" name="method" value="edit_user_sticker_img">
                                                                    <input type="hidden" class="form-control" name="send_url" value="#">
                                                                    <button type="button" class="btn btn-default btn-xs"
                                                                    onclick="edit_user_sticker_form(this);void(0);"
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
                                        <div class="panel-heading">
                                            <a data-toggle="collapse" data-parent="#panel-user_info" href="#collapseFour">
                                                <h4 class="panel-title">
                                                    選擇個人頁面樣式
                                                </h4>
                                            </a>
                                        </div>
                                        <div id="collapseFour" class="panel-collapse collapse">
                                            <div class="panel-body">
                                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                                                    <div class="thumbnail">
                                                        <img height="80" style="height:80px;" src="../img/bg.jpg" alt="預設樣式">
                                                        <div class="caption">預設</div>
                                                        <div class="caption">
                                                            <button type="button" class="btn btn-default btn-xs"
                                                            onclick="edit_style_user(1,1);void(0);"
                                                            >預覽</button>
                                                            <button type="button" class="btn btn-default btn-xs"
                                                            onclick="edit_style_user(2,1);void(0);"
                                                            >套用</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php for($i=2;$i<=6;$i++):?>
                                                    <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                                                        <div class="thumbnail">
                                                            <img height="80" style="height:80px;" src="../img/default/style_user/bg_<?php echo $i;?>.jpg" alt="樣式<?php echo $i;?>">
                                                            <div class="caption">樣式<?php echo $i-1;?></div>
                                                            <div class="caption">
                                                                <button type="button" class="btn btn-default btn-xs"
                                                                onclick="edit_style_user(1,<?php echo $i;?>);void(0);"
                                                                >預覽</button>
                                                                <button type="button" class="btn btn-default btn-xs"
                                                                onclick="edit_style_user(2,<?php echo $i;?>);void(0);"
                                                                >套用</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endfor;?>
                                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 hidden-xs">
                                                    <div class="thumbnail">
                                                        <img height="80" style="height:80px;" src="../img/default/style_user/bg_upload.jpg" alt="自行上傳">
                                                        <div class="caption style_user_file_name"></div>
                                                        <div class="caption">
                                                            <form action="../controller/img.php" method="post" class="ajax_style_user_form">
                                                                <span class="btn btn-default btn-xs btn_file">
                                                                    選擇<input type="file" name="style_user_file" class="style_user_file">
                                                                </span>
                                                                <button type="button" class="btn btn-default btn-xs"
                                                                onclick="ajax_style_user_upload(this);void(0);"
                                                                >上傳</button>
                                                                <input type="hidden" class="form-control" name="method" value="add_style_user_img">
                                                                <input type="hidden" class="form-control" name="send_url" value="#">
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 書櫃 -->
                    <div role="tabpanel" class="tab-pane fade <?php if($tab===2)echo 'in active';?>" id="book" aria-labelledBy="profile-tab">

                    </div>

                    <!-- 討論 -->
                    <div role="tabpanel" class="tab-pane fade <?php if($tab===3)echo 'in active';?>" id="article" aria-labelledBy="profile-tab">

                    </div>

                    <!-- 小組 -->
                    <div role="tabpanel" class="tab-pane fade <?php if($tab===4)echo 'in active';?>" id="group" aria-labelledBy="profile-tab">

                    </div>

                    <!-- 書友 -->
                    <div role="tabpanel" class="tab-pane fade <?php if($tab===5)echo 'in active';?>" id="friend" aria-labelledBy="profile-tab">

                    </div>

                    <!-- 追蹤 -->
                    <div role="tabpanel" class="tab-pane fade <?php if($tab===7)echo 'in active';?>" id="track" aria-labelledBy="profile-tab">

                    </div>

                    <!-- 邀請 -->
                    <div role="tabpanel" class="tab-pane fade <?php if($tab===6)echo 'in active';?>" id="request" aria-labelledBy="profile-tab">
                        <div class="row" style="position:relative;">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="position:relative;margin-top:25px;margin-bottom:10px;">
                                <?php if(empty($friend_results)&&$sess_user_id===$user_id):?>
                                    <div class="user_lefe_side_tab3 row" style="position:relative;margin-top:-5px;margin-bottom:10px;">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:center;">
                                            <pre style="background-color:#ffffdd;">目前共有【<?php echo count($friend_results);?>】個訊息及邀請</pre>
                                        </div>
                                    </div>
                                <?php endif;?>
								<?php if(empty($request_book_results)): ?>
									<div class="text-center">
										請求書友推薦一本書籍：
										<button class="btn btn-primary" data-toggle="modal" data-target="#request_book">請求推薦</button>
									</div>
								<?php else: ?>
									<div class="text-center">
										請求書友推薦一本書籍：
										<font color="red">※你在最近七天內已送出過請求了。</font>
									</div>
								<?php endif; ?>
                                <!-- <div class="input-group" style="position:relative;">
                                    <input type="text" class="user_name form-control" name="user_name" placeholder="請選擇或輸入一位書友來請求推薦一本書籍給你">
                                    <input type="hidden" class="user_id form-control" name="user_id" value="0">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            選擇書友 <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            <?php
                                            if(!empty($friend_results)&&$sess_user_id===$user_id){
                                                foreach($friend_results as $inx=>$friend_result):
                                                    $rs_friend_state=(int)$friend_result['friend_state'];
                                                    $rs_user_id     =(int)$friend_result['user_id'];
                                                    $rs_friend_id   =(int)$friend_result['friend_id'];
                                                    if($rs_user_id!==$user_id || $rs_friend_id!==$user_id){
                                                        $tmp_user_id=0;
                                                        if($rs_user_id!==$user_id)$tmp_user_id=$rs_user_id;
                                                        if($rs_friend_id!==$user_id)$tmp_user_id=$rs_friend_id;
                                                        $sql="
                                                            SELECT `name`
                                                            FROM `user`.`member`
                                                            WHERE 1=1
                                                                AND `user`.`member`.`uid`={$tmp_user_id}
                                                        ";
                                                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                                        $rs_user_name ='';
                                                        if(!empty($db_results)){$rs_user_name=trim($db_results[0]['name']);}else{continue;}
                                                    }
                                                if($rs_friend_state===1){
                                            ?>
                                            <li><a href="javascript:void(0);" onclick="auto_user(this);void(0);"><?php echo htmlspecialchars($rs_user_name);?></a></li>
                                            <?php }endforeach;}?>
                                        </ul>
                                    </div>
                                    <div class="input-group-btn">
                                        <button type="button" class="form-control btn btn-default btn-xs" style="position:relative;margin:0 5px;"
                                        onclick="add_request_rec_us_book(this);void(0);">請求</button>
                                    </div>
                                </div> -->
                            </div>
                            <?php
                                if(!empty($request_results)){
                                    foreach($request_results as $time=>$request_result):
                                        foreach($request_result as $request_type=>$arry_request):
                                            extract($arry_request, EXTR_PREFIX_ALL, "rs");
                                            if(!in_array(trim($request_type),array('request_friend','article_get_like','article_get_reply','request_friend_success'))){
                                                $rs_request_from_sex =(int)$rs_request_from_sex;
                                                $rs_request_to_sex   =(int)$rs_request_to_sex;
                                                $rs_request_from_name=trim($rs_request_from_name);
                                                $rs_request_to_name  =trim($rs_request_to_name);
                                                $rs_request_from     =(int)$rs_request_from;
                                                $rs_request_to       =(int)$rs_request_to;
                                                $rs_request_id       =(int)$rs_request_id;
                                                $rs_request_state    =(int)$rs_request_state;
                                                $rs_request_read     =(int)$rs_request_read;
                                                $rs_keyin_cdate      =trim($rs_keyin_cdate);
                                                $rs_rev_id           =(int)$rs_rev_id;

                                                $rs_request_from_img ='../img/default/user_boy.png';
                                                $rs_request_to_img   ='../img/default/user_boy.png';

                                                if($rs_request_from_sex===2)$rs_request_from_img ='../img/default/user_girl.png';
                                                if($rs_request_to_sex===2)$rs_request_to_img ='../img/default/user_girl.png';

                                                if($rs_request_from!==$sess_user_id){
			                                        if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_request_from}/forum/user_sticker/1.jpg")){
			                                            $rs_request_from_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_request_from}/forum/user_sticker/1.jpg";
			                                        }
			                                    }
			                                    if($rs_request_to!==$sess_user_id){
			                                        if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_request_to}/forum/user_sticker/1.jpg")){
			                                            $rs_request_to_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_request_to}/forum/user_sticker/1.jpg";
			                                        }
			                                    }
                                            }
                            ?>
                                <!-- <?php if(trim($request_type)==='ok_request_rec_us_book_rev'):?>
                                <?php
                                    $rs_book_sid=trim($arry_request['book_sid']);
                                    if($rs_book_sid!==''){
                                        $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                        if(empty($arry_book_infos))continue;
                                        $rs_book_name=trim($arry_book_infos[0]['book_name']);
                                    }else{continue;}
                                    $rs_content="
                                        已回應 <a href='user.php?user_id={$rs_request_from}&tab=1'>{$rs_request_from_name}</a> 的請求，
                                        推薦一本書籍給 <a href='user.php?user_id={$rs_request_from}&tab=1'>{$rs_request_from_name}</a>。
                                    ";
                                ?>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="position:relative;margin-top:15px;">
                                        <div class="media">
                                            <a class="pull-left" href="user.php?user_id=<?php echo $rs_request_to;?>&tab=1">
                                                <img class="media-object" src="<?php echo htmlspecialchars($rs_request_to_img);?>" width="64" height="64" alt="Media">
                                            </a>
                                            <div class="media-body">
                                                <a href="user.php?user_id=<?php echo $rs_request_to;?>&tab=1">
                                                    <h4 class="media-heading"><?php echo htmlspecialchars($rs_request_to_name);?></h4>
                                                </a>
                                                <h4><?php echo ($rs_content);?></h4>
                                                <h4>
                                                    書名：<a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_book_sid);?>">【
                                                    <?php echo htmlspecialchars($rs_book_name);?>】</a>
                                                </h4>
                                                <h4><button type="button" class="btn_edit_ok_request_rec_us_book btn btn-default btn-xs" style="position:relative;top:5px;"
                                                request_id='<?php echo $rs_request_id;?>'>確定</button></h4>
                                                <h4 class="pull-right"><?php echo ($rs_keyin_cdate);?></h4>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;?> -->

                                <?php if(trim($request_type)==='request_rec_us_book_rev'):?>
                                <?php
                                    $rs_content="
                                        向【<a href='user.php?user_id={$rs_request_to}&tab=1'>{$rs_request_to_name}</a>】提出邀請，
                                        希望【<a href='user.php?user_id={$rs_request_to}&tab=1'>{$rs_request_to_name}</a>】能推薦一本書籍給{$rs_request_from_name}。
                                    ";
                                ?>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="position:relative;margin-top:15px;">
                                        <div class="media" style="overflow:visible;">
                                            <a class="pull-left" href="user.php?user_id=<?php echo $rs_request_from;?>&tab=1">
                                                <img class="media-object" src="<?php echo htmlspecialchars($rs_request_from_img);?>" width="64" height="64" alt="Media">
                                            </a>
                                            <div class="media-body" style="overflow:visible;">
                                                <a href="user.php?user_id=<?php echo $rs_request_from;?>&tab=1">
                                                    <h4 class="media-heading"><?php echo htmlspecialchars($rs_request_from_name);?></h4>
                                                </a>
                                                <h4><?php echo ($rs_content);?></h4>
                                                <?php if($rs_request_to===$sess_user_id):?>
                                                    <div class="input-group" style="position:relative;padding:5px;">
                                                        <input type="text" class="form-control request_rec_us_book_name" name="request_rec_us_book_name" placeholder="請選擇或輸入一本書籍來回應書友的請求" request_id='<?php echo $rs_request_id;?>'>
                                                        <input type="hidden" class="form-control request_rec_us_book_sid" name="request_rec_us_book_sid" value="" request_id='<?php echo $rs_request_id;?>'>
                                                        <div class="input-group-btn">
                                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                                選擇書籍 <span class="caret"></span>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                                <?php
                                                                if(!empty($book_results)&&$sess_user_id===$user_id){
                                                                    foreach($book_results as $inx=>$book_result):
                                                                        $rs_book_sid=trim($book_result['book_sid']);
                                                                        if($rs_book_sid!==''){
                                                                            $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                                                            if(empty($arry_book_infos))continue;
                                                                            $rs_book_name=trim($arry_book_infos[0]['book_name']);
                                                                            if(mb_strlen($rs_book_name)>25){
                                                                                $rs_book_name=mb_substr($rs_book_name,0,25)."..";
                                                                            }
                                                                        }
                                                                ?>
                                                                <li><a href="javascript:void(0);" onclick="auto_request_rec_us_book(this,<?php echo $rs_request_id;?>);void(0);"><?php echo htmlspecialchars($rs_book_name);?></a></li>
                                                                <?php endforeach;}?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <h4 class="col-md-offset-1">
                                                        <div class="form-group">
                                                            <textarea class="form-control request_content" name="request_content" rows="1" placeholder="請輸入推薦這本書籍的理由"
                                                            request_id='<?php echo $rs_request_id;?>'></textarea>
                                                        </div>
                                                        <button type="button" class="btn btn-default btn-sm pull-left"
                                                        onclick="edit_request_rec_us_book(this,<?php echo $rs_request_id;?>);void(0);">送出</button>
                                                    </h4>
                                                    <h4 style='clear:left;'></h4>
                                                <?php endif;?>
                                                <h4 class="pull-right"><?php echo ($rs_keyin_cdate);?></h4>
                                                <h4 style='clear:right;'></h4>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;?>

                                <!-- <?php if(trim($request_type)==='request_article_rev'):?>
                                <?php
                                    $rs_group_id     =(int)$rs_group_id;
                                    $rs_article_id   =(int)$rs_article_id;
                                    $rs_article_title=trim($rs_article_title);

                                    if($rs_group_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
                                        $arry_blacklist_group_school=get_blacklist_group_school($sess_school_code,$rs_group_id,$arry_conn_mssr);
                                        if(!empty($arry_blacklist_group_school))continue;
                                    }

                                    if($rs_article_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
                                        $arry_blacklist_article_school=get_blacklist_article_school($sess_school_code,$rs_article_id,$arry_conn_mssr);
                                        if(!empty($arry_blacklist_article_school))continue;
                                    }

                                    $rs_content      ="
                                        向【<a href='user.php?user_id={$rs_request_to}&tab=1'>{$rs_request_to_name}</a>】提出邀請，
                                        希望【<a href='user.php?user_id={$rs_request_to}&tab=1'>{$rs_request_to_name}</a>】能一起參與討論文章：
                                    ";
                                    if($rs_group_id===0){
                                        $rs_content.="<a target='_blank' href='reply.php?get_from=1&article_id={$rs_article_id}'>【{$rs_article_title}】。</a>";
                                    }
                                    if($rs_group_id!==0){
                                        $rs_content.="<a target='_blank' href='reply.php?get_from=2&article_id={$rs_article_id}'>【{$rs_article_title}】。</a>";
                                    }
                                ?>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="position:relative;margin-top:15px;">
                                        <div class="media">
                                            <a class="pull-left" href="user.php?user_id=<?php echo $rs_request_from;?>&tab=1">
                                                <img class="media-object" src="<?php echo htmlspecialchars($rs_request_from_img);?>" width="64" height="64" alt="Media">
                                            </a>
                                            <div class="media-body">
                                                <a href="user.php?user_id=<?php echo $rs_request_from;?>&tab=1">
                                                    <h4 class="media-heading"><?php echo htmlspecialchars($rs_request_from_name);?></h4>
                                                </a>
                                                <h4><?php echo ($rs_content);?></h4>
                                                <?php if($rs_request_to===$sess_user_id):?>
                                                    <h4><button type="button" class="btn_request_article_rev btn btn-default btn-xs" style="position:relative;top:5px;"
                                                    request_id='<?php echo $rs_request_id;?>'>確定</button></h4>
                                                <?php endif;?>
                                                <h4 class="pull-right"><?php echo ($rs_keyin_cdate);?></h4>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;?> -->

                                <?php if(trim($request_type)==='request_create_group_rev'):?>
                                <?php
                                    $rs_group_id  =(int)$rs_group_id;
                                    $rs_group_name=trim($rs_group_name);
                                    $rs_content ="
                                        向【<a href='user.php?user_id={$rs_request_to}&tab=1'>{$rs_request_to_name}</a>】提出邀請，
                                        希望【<a href='user.php?user_id={$rs_request_to}&tab=1'>{$rs_request_to_name}</a>】能一同聯署建立小組：
                                        【<a target='_blank' href='article.php?get_from=2&group_id={$rs_group_id}'>{$rs_group_name}</a>】。
                                    ";
                                ?>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="position:relative;margin-top:15px;">
                                        <div class="media">
                                            <a class="pull-left" href="user.php?user_id=<?php echo $rs_request_from;?>&tab=1">
                                                <img class="media-object" src="<?php echo htmlspecialchars($rs_request_from_img);?>" width="64" height="64" alt="Media">
                                            </a>
                                            <div class="media-body">
                                                <a href="user.php?user_id=<?php echo $rs_request_from;?>&tab=1">
                                                    <h4 class="media-heading"><?php echo htmlspecialchars($rs_request_from_name);?></h4>
                                                </a>
                                                <h4><?php echo ($rs_content);?></h4>
                                                <?php if($rs_request_to===$sess_user_id):?>
                                                    <h4>
                                                        <button type="button" class="btn btn-default btn-xs" style="position:relative;top:5px;"
                                                        request_id='<?php echo $rs_request_id;?>' onclick="request_create_group(this,1);void(0);">我要聯署
                                                        </button>
                                                        <button type="button" class="btn btn-default btn-xs" style="position:relative;top:5px;"
                                                        request_id='<?php echo $rs_request_id;?>' onclick="request_create_group(this,2);void(0);">我不要聯署
                                                        </button>
                                                    </h4>
                                                <?php endif;?>
                                                <h4 class="pull-right"><?php echo ($rs_keyin_cdate);?></h4>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;?>

                                <!-- <?php if(trim($request_type)==='request_join_to_group_rev'):?>
                                <?php
                                    $rs_group_id  =(int)$rs_group_id;
                                    $rs_group_name=trim($rs_group_name);
                                    $rs_content ="
                                        向【<a href='user.php?user_id={$rs_request_to}&tab=1'>{$rs_request_to_name}</a>】提出申請，
                                        希望能加入你的小組：
                                        【<a target='_blank' href='article.php?get_from=2&group_id={$rs_group_id}'>{$rs_group_name}</a>】。
                                    ";

                                    if($rs_group_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
                                        $arry_blacklist_group_school=get_blacklist_group_school($sess_school_code,$rs_group_id,$arry_conn_mssr);
                                        if(!empty($arry_blacklist_group_school))continue;
                                    }
                                ?>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="position:relative;margin-top:15px;">
                                        <div class="media">
                                            <a class="pull-left" href="user.php?user_id=<?php echo $rs_request_from;?>&tab=1">
                                                <img class="media-object" src="<?php echo htmlspecialchars($rs_request_from_img);?>" width="64" height="64" alt="Media">
                                            </a>
                                            <div class="media-body">
                                                <a href="user.php?user_id=<?php echo $rs_request_from;?>&tab=1">
                                                    <h4 class="media-heading"><?php echo htmlspecialchars($rs_request_from_name);?></h4>
                                                </a>
                                                <h4><?php echo ($rs_content);?></h4>
                                                <?php if($rs_request_to===$sess_user_id):?>
                                                    <h4>
                                                        <button type="button" class="btn btn-default btn-xs" style="position:relative;top:5px;"
                                                        request_id='<?php echo $rs_request_id;?>' onclick="request_join_to_group(this,1);void(0);">允許
                                                        </button>
                                                        <button type="button" class="btn btn-default btn-xs" style="position:relative;top:5px;"
                                                        request_id='<?php echo $rs_request_id;?>' onclick="request_join_to_group(this,2);void(0);">拒絕
                                                        </button>
                                                    </h4>
                                                <?php endif;?>
                                                <h4 class="pull-right"><?php echo ($rs_keyin_cdate);?></h4>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;?> -->

                                <!-- <?php if(trim($request_type)==='request_join_us_group_rev'):?>
                                <?php
                                    $rs_group_id  =(int)$rs_group_id;
                                    $rs_group_name=trim($rs_group_name);
                                    $rs_content ="
                                        向【<a href='user.php?user_id={$rs_request_to}&tab=1'>{$rs_request_to_name}</a>】提出邀請，
                                        希望【<a href='user.php?user_id={$rs_request_to}&tab=1'>{$rs_request_to_name}</a>】能加入他的小組：
                                        【<a target='_blank' href='article.php?get_from=2&group_id={$rs_group_id}'>{$rs_group_name}</a>】。
                                    ";

                                    if($rs_group_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
                                        $arry_blacklist_group_school=get_blacklist_group_school($sess_school_code,$rs_group_id,$arry_conn_mssr);
                                        if(!empty($arry_blacklist_group_school))continue;
                                    }
                                ?>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="position:relative;margin-top:15px;">
                                        <div class="media">
                                            <a class="pull-left" href="user.php?user_id=<?php echo $rs_request_from;?>&tab=1">
                                                <img class="media-object" src="<?php echo htmlspecialchars($rs_request_from_img);?>" width="64" height="64" alt="Media">
                                            </a>
                                            <div class="media-body">
                                                <a href="user.php?user_id=<?php echo $rs_request_from;?>&tab=1">
                                                    <h4 class="media-heading"><?php echo htmlspecialchars($rs_request_from_name);?></h4>
                                                </a>
                                                <h4><?php echo ($rs_content);?></h4>
                                                <?php if($rs_request_to===$sess_user_id):?>
                                                    <h4>
                                                        <button type="button" class="btn btn-default btn-xs" style="position:relative;top:5px;"
                                                        request_id='<?php echo $rs_request_id;?>' onclick="request_join_us_group(this,1);void(0);">接受
                                                        </button>
                                                        <button type="button" class="btn btn-default btn-xs" style="position:relative;top:5px;"
                                                        request_id='<?php echo $rs_request_id;?>' onclick="request_join_us_group(this,2);void(0);">拒絕
                                                        </button>
                                                    </h4>
                                                <?php endif;?>
                                                <h4 class="pull-right"><?php echo ($rs_keyin_cdate);?></h4>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;?> -->

                                <?php if(trim($request_type)==='request_friend'):?>
                                <?php
                                    $rs_user_name       =trim($rs_user_name);
                                    $rs_friend_name     =trim($rs_friend_name);
                                    $rs_create_by       =(int)$rs_create_by;
                                    $rs_user_id         =(int)$rs_user_id;
                                    $rs_friend_id       =(int)$rs_friend_id;
                                    $rs_friend_content  =trim($rs_content);
                                    $rs_friend_state    =(int)$rs_friend_state;
                                    $rs_keyin_mdate     =trim($rs_keyin_mdate);

                                    if($rs_friend_state===1){
                                        $rs_friend_state_html='成功';
                                    }elseif($rs_friend_state===2){
                                        $rs_friend_state_html='失敗';
                                    }

                                    $rs_user_img    ='../img/default/user_boy.png';
                                    $rs_friend_img  ='../img/default/user_boy.png';

                                    if($rs_user_sex===2)$rs_user_img ='../img/default/user_girl.png';
                                    if($rs_friend_sex===2)$rs_friend_img ='../img/default/user_girl.png';

                                    if($rs_user_id!==$sess_user_id){
                                        if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg")){
                                            $rs_friend_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                                        }
                                    }
                                    if($rs_friend_id!==$sess_user_id){
                                        if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_friend_id}/forum/user_sticker/1.jpg")){
                                            $rs_friend_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_friend_id}/forum/user_sticker/1.jpg";
                                        }
                                    }

                                    if($rs_friend_state===3){
                                        $rs_content ="
                                            【<a href='user.php?user_id={$rs_user_id}&tab=1'>{$rs_user_name}</a>】
                                            已經提出要與
                                            【<a href='user.php?user_id={$rs_friend_id}&tab=1'>{$rs_friend_name}</a>】
                                            成為書友，
                                            請問你是否要跟他成為書友?
                                        ";
                                        if(trim($rs_friend_content)!==''){
                                            $rs_content.="
                                                <br>
                                                【<a href='user.php?user_id={$rs_user_id}&tab=1'>{$rs_user_name}</a>】 說：{$rs_friend_content}
                                            ";
                                        }
                                    }else{
                                        $rs_content ="
                                            【<a href='user.php?user_id={$rs_user_id}&tab=1'>{$rs_user_name}</a>】
                                            提出與
                                            【<a href='user.php?user_id={$rs_friend_id}&tab=1'>{$rs_friend_name}</a>】
                                            的
                                            交友申請結果為 : {$rs_friend_state_html}
                                        ";
                                    }
                                ?>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="position:relative;margin-top:15px;">
                                        <div class="media">
                                            <!-- <a class="pull-left" href="javascript:void(0);">
                                                <img class="media-object" src="<?php echo htmlspecialchars($rs_user_img);?>" width="64" height="64" alt="Media">
                                            </a> -->
                                            <a class="pull-left" href="javascript:void(0);">
                                                <img class="media-object" src="<?php echo htmlspecialchars($rs_friend_img);?>" width="64" height="64" alt="Media">
                                            </a>
                                            <div class="media-body">
                                                <h4><?php echo ($rs_content);?></h4>
                                                <?php if($rs_friend_state===3&&$rs_create_by!==$sess_user_id):?>
                                                    <h4>
                                                        <button type="button" class="btn btn-default btn-xs" style="position:relative;top:5px;"
                                                        create_by='<?php echo $rs_create_by;?>'
                                                        user_id='<?php echo $rs_user_id;?>'
                                                        friend_id='<?php echo $rs_friend_id;?>'
                                                        onclick="request_friend(this,1);void(0);">接受
                                                        </button>
                                                        <button type="button" class="btn btn-default btn-xs" style="position:relative;top:5px;"
                                                        create_by='<?php echo $rs_create_by;?>'
                                                        user_id='<?php echo $rs_user_id;?>'
                                                        friend_id='<?php echo $rs_friend_id;?>'
                                                        onclick="request_friend(this,2);void(0);">拒絕
                                                        </button>
                                                    </h4>
                                                <?php endif;?>
                                                <h4 class="pull-right"><?php echo ($rs_keyin_mdate);?></h4>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;?>

                            <?php endforeach;endforeach;}?>
							<?php
							if(!empty($request_results)){
							    foreach($request_results as $time=>$request_result):
							        foreach($request_result as $request_type=>$arry_request):
							            extract($arry_request, EXTR_PREFIX_ALL, "rs");
							            if(!in_array(trim($request_type),array('request_friend','article_get_like','article_get_reply','request_friend_success'))){
							                $rs_request_from_sex =(int)$rs_request_from_sex;
							                $rs_request_to_sex   =(int)$rs_request_to_sex;
							                $rs_request_from_name=trim($rs_request_from_name);
							                $rs_request_to_name  =trim($rs_request_to_name);
							                $rs_request_from     =(int)$rs_request_from;
							                $rs_request_to       =(int)$rs_request_to;
							                $rs_request_id       =(int)$rs_request_id;
							                $rs_request_state    =(int)$rs_request_state;
							                $rs_request_read     =(int)$rs_request_read;
							                $rs_keyin_cdate      =trim($rs_keyin_cdate);
							                $rs_rev_id           =(int)$rs_rev_id;

							                $rs_request_from_img ='../img/default/user_boy.png';
							                $rs_request_to_img   ='../img/default/user_boy.png';

							                if($rs_request_from_sex===2)$rs_request_from_img ='../img/default/user_girl.png';
							                if($rs_request_to_sex===2)$rs_request_to_img ='../img/default/user_girl.png';
							            }
										
										if (trim($request_type) === 'report_message'):
											$rs_message_id = (int)$rs_message_id;
											$rs_article_id = trim($rs_article_id);
											$rs_article_title = trim($rs_article_title);
											$rs_article_type = (int)$rs_article_type;

											if ($rs_article_type == 1) {
												$rs_content = "
													你所發表的文章【{$rs_article_title}】被多數人檢舉，已將此文章移除並扣除發文所獲得的50點積分。
												";
											}

											if ($rs_article_type == 2) {
												$rs_content = "
													你在文章<a target='_blank' href='reply.php?get_from=1&article_id={$rs_article_id}'>【{$rs_article_title}】</a>中的回文被多數人檢舉，已將此回文移除並扣除50點發文點數作為懲罰。
												";
											}
							 ?>
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="position:relative;margin-top:15px;">
												<div class="media">
													<a class="pull-left">
														<img class="media-object" src="<?php echo htmlspecialchars($rs_request_from_img);?>" width="64" height="64" alt="Media">
													</a>
													<div class="media-body">
														<div>
															<h4 class="media-heading"><?php echo htmlspecialchars($rs_request_from_name);?></h4>
														</div>
														<h4><?php echo ($rs_content);?></h4>
														<?php if ($rs_request_to === $sess_user_id):?>
															<h4><button type="button" class="btn_report_message btn btn-default btn-sm" style="position:relative;top:5px;" message_id='<?php echo $rs_message_id;?>'>確定</button></h4>
														<?php endif;?>
														<h4 class="pull-right"><?php echo ($rs_keyin_cdate);?></h4>
													</div>
												</div>
											</div>
							<?php 
										endif;
									endforeach;
								endforeach;
							}
							 ?>
                        </div>
                    </div>

                    <!-- 紀錄 -->
                    <div role="tabpanel" class="tab-pane fade <?php if($tab===9)echo 'in active';?>" id="message" aria-labelledBy="profile-tab">

                    </div>

                </div>
            </div>
            <!-- user_lefe_side,end -->

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

    <!-- modal_add_friend,start -->
    <?php echo $modal_dialog_add_friend;?>
    <!-- modal_add_friend,end -->

    <!-- modal_request_article,start -->
	<?php echo $new_modal_dialog;?>
	<!-- modal_request_article,end -->

	<!-- 新增跳窗用遮罩 -->
	<div class="mask" id="mask"></div>

</body>

<!-- 通用 -->
<script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/func/block_ui/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/ui/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/func/flot/excanvas.min.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/func/flot/jquery.flot.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/func/flot/jquery.flot.pie.js"></script>
<script type="text/javascript" src="../../../lib/js/string/code.js"></script>
<script type="text/javascript" src="../../../lib/js/array/code.js"></script>
<script type="text/javascript" src="../../../lib/js/fso/code.js"></script>
<script type="text/javascript" src="../../../lib/js/form/code.js"></script>
<script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/3.51/jquery.form.min.js"></script>

<!-- 專屬 -->
<script type="text/javascript" src="../inc/code.js"></script>

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var send_url=document.URL;
    var tab=<?php echo $tab;?>;
    var sess_user_id=parseInt(<?php echo $sess_user_id;?>);
    var user_id=parseInt(<?php echo $user_id;?>);
    var style_id=parseInt(<?php echo $style_id;?>);
    var style_from=parseInt(<?php echo $style_from;?>);
    var user_article_cno=parseInt(<?php echo count($article_reply_results);?>);
    var json_book_category_rev=<?php echo $json_book_category_rev;?>

    var user_img_size ={};
    var arry_user_info={};
    var arry_user_name=[];
    var arry_book_info={};
    var arry_book_name=[];
    var arry_friend_name=[];

    user_img_size[0]=0;
    user_img_size[1]=0;
    <?php if(isset($user_img_size)):?>
        user_img_size[0]=parseInt(<?php echo $user_img_size[0];?>);
        user_img_size[1]=parseInt(<?php echo $user_img_size[1];?>);
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
		arry_friend_name.push('<?php echo $rs_user_name;?>');
	<?php endforeach;}?>

    <?php
    if(!empty($friend_results)&&$sess_user_id===$user_id){
        foreach($friend_results as $inx=>$friend_result):
            $rs_friend_state=(int)$friend_result['friend_state'];
            $rs_user_id     =(int)$friend_result['user_id'];
            $rs_friend_id   =(int)$friend_result['friend_id'];
            if($rs_user_id!==$user_id || $rs_friend_id!==$user_id){
                $tmp_user_id=0;
                if($rs_user_id!==$user_id)$tmp_user_id=$rs_user_id;
                if($rs_friend_id!==$user_id)$tmp_user_id=$rs_friend_id;
                $sql="
                    SELECT `name`
                    FROM `user`.`member`
                    WHERE 1=1
                        AND `user`.`member`.`uid`={$tmp_user_id}
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                $rs_user_name ='';
                if(!empty($db_results)){$rs_user_name=trim($db_results[0]['name']);}else{continue;}
            }
        if($rs_friend_state===1){
    ?>
        arry_user_info['<?php echo $rs_user_name;?>']='<?php echo $tmp_user_id;?>';
        arry_user_name.push('<?php echo $rs_user_name;?>');
    <?php }endforeach;}?>

    <?php
    if(!empty($book_results)&&$sess_user_id===$user_id){
        foreach($book_results as $inx=>$book_result):
            $rs_book_sid=trim($book_result['book_sid']);
            if($rs_book_sid!==''){
                $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                if(empty($arry_book_infos))continue;
                $rs_book_name=addslashes(trim($arry_book_infos[0]['book_name']));
                if(mb_strlen($rs_book_name)>25){
                    $rs_book_name=mb_substr($rs_book_name,0,25)."..";
                }
            }
    ?>
        arry_book_info['<?php echo $rs_book_name;?>']='<?php echo $rs_book_sid;?>';
        arry_book_name.push('<?php echo $rs_book_name;?>');
    <?php endforeach;}?>


    //OBJ
    var notification=new notification();
    var category_dataset=[];
    <?php if(!empty($arry_book_category_rev_cno)):?>
        <?php foreach($arry_book_category_rev_cno as $key=>$val):?>
            <?php if(trim($key)!=='未分類'):?>
                var tmp_category_dataset={label:"<?php echo trim($key);?>",data:<?php echo (int)($val);?>};
                category_dataset.push(tmp_category_dataset);
            <?php endif;?>
        <?php endforeach;?>
    <?php endif;?>


    //FUNCTION
    $(".request_book_friend_name").autocomplete({
        source: arry_user_name
    });
    $(".request_rec_us_book_name").autocomplete({
        source: arry_book_name
    });

    function refresh_rec_friend(obj){
    //刷新推薦的書友

        var cno=0;
        var json_rec_friend_results=<?php echo $json_rec_friend_results;?>;
        shuffle(json_rec_friend_results);
        $('.rec_friend').find('.rec_friend_content').remove();

        for(key1 in json_rec_friend_results){
            var html='';
            var uid =parseInt(json_rec_friend_results[key1]['uid']);
            var name=$.trim(json_rec_friend_results[key1]['name']);
            var sex =parseInt(json_rec_friend_results[key1]['sex']);
            var user_img='';
            if(sex===1)user_img='../img/default/user_boy.png';
            if(sex===2)user_img='../img/default/user_girl.png';

            html+='<div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 rec_friend_content">';
            html+=  '<div class="thumbnail">';
            html+=      '<img class="img-responsive" src="'+user_img+'" alt="thumbnail" style="height:70px;">';
            html+=      '<div class="caption" style="text-align:center;">';
            html+=          '<h3>'+name+'</h3>';
            html+=          '<p><a href="javascript:void(0);" class="btn_add_friend btn btn-primary btn-xs" role="button" style="color:#ffffff;"';
            html+=          ' user_id='+sess_user_id+'';
            html+=          ' friend_id='+uid+'';
            html+=          '>加為書友</a></p>';
            html+=      '</div>';
            html+=  '</div>';
            html+='</div>';

            if(cno<6){
                $('.rec_friend').append(html);
                cno++;
            }
        }
        function shuffle(sourceArray) {
            for (var n = 0; n < sourceArray.length - 1; n++) {
                var k = n + Math.floor(Math.random() * (sourceArray.length - n));

                var temp = sourceArray[k];
                sourceArray[k] = sourceArray[n];
                sourceArray[n] = temp;
            }
        }
    }

    function book_categroy_rev_filter(cat_name){
    //書籍條件顯示

        var cat_name=$.trim(cat_name);
        if(cat_name==='全部'){
            $('.book_thumbnail_col').show();
        }else{
            $('.book_thumbnail_col').hide();
            $('.book_thumbnail_col').each(function(inx) {
                var book_sid=$.trim($('.book_thumbnail_col').eq(inx).attr('book_sid'));
                if(json_book_category_rev[book_sid]!==undefined){
                    for(key1 in json_book_category_rev[book_sid]){
                        for(key2 in json_book_category_rev[book_sid][key1]){
                            if($.trim(json_book_category_rev[book_sid][key1][key2])===cat_name){
                                $('.book_thumbnail_col').eq(inx).show();
                            }
                        }
                    }
                }
            });
        }
    }

    function edit_book_category_user_rev(obj,book_sid){
    //設定書籍類別(軟標籤)

        var cat_code=$.trim($(obj).val());
        var book_sid=$.trim(book_sid);

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
                cat_code:encodeURI(trim(cat_code                     )),
                book_sid:encodeURI(trim(book_sid                     )),
                method  :encodeURI(trim('edit_book_category_user_rev')),
                send_url:encodeURI(trim(send_url                     ))
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

    function edit_user_sticker_form(obj){
    //裁切個人大頭貼

        var oForm          =$('.edit_user_sticker_form')[0];
        var user_sticker_x1=parseInt($('#user_sticker_x1').val());
        var user_sticker_y1=parseInt($('#user_sticker_y1').val());
        var user_sticker_x2=parseInt($('#user_sticker_x2').val());
        var user_sticker_y2=parseInt($('#user_sticker_y2').val());
        var user_sticker_w =parseInt($('#user_sticker_w').val());
        var user_sticker_h =parseInt($('#user_sticker_h').val());

		if(user_sticker_w==0||user_sticker_h==0){
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

    function ajax_user_sticker_upload(obj){
    //ajax 個人大頭貼上傳

        var obj=obj;
        var arry_type=[
            'jpg',
            'jpeg'
        ]
        var file_val=trim($('.user_sticker_file').val());
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

        $('.ajax_user_sticker_form').ajaxSubmit({
            beforeSubmit: function(){
            },
            success: function(respone,st,xhr,$form){
                alert(respone);
                $(obj)[0].disabled=false;
                location.href='user.php?user_id='+user_id+'&tab=8';
                return true;
            },
            error: function(){
                alert('上傳失敗');
                $(obj)[0].disabled=false;
                return false;
            }
        });
    }

    function edit_user_info(){
    //更新個人資訊

        var oform_user_info=document.getElementById('form_user_info');
        if(confirm('你確定要更新個人資訊嗎 ?')){
            oform_user_info.action='../controller/edit.php'
            oform_user_info.submit();
            return true;
        }else{
            return false;
        }
    }

    function ajax_style_user_upload(obj){
    //ajax 個人頁面上傳

        var obj=obj;
        var arry_type=[
            'jpg',
            'jpeg'
        ]
        var file_val=trim($('.style_user_file').val());
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
        $('.style_user_file_name').text(filename+'.'+extension);

        //上傳進度
        $(obj)[0].disabled=true;

        $('.ajax_style_user_form').ajaxSubmit({
            beforeSubmit: function(){
            },
            success: function(respone,st,xhr,$form){
                alert(respone);
                $(obj)[0].disabled=false;
                location.href='user.php?user_id='+user_id+'&tab=8';
                return true;
            },
            error: function(){
                alert('上傳失敗');
                $(obj)[0].disabled=false;
                return false;
            }
        });
    }

    function edit_style_user(type,style_id){
    //更換個人頁面樣式

        var type    =parseInt(type);
        var style_id=parseInt(style_id);

        if(type===1){
        //套用
            $('body').css("background-image","url(../img/default/style_user/bg_"+style_id+".jpg)");
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
                    style_id    :encodeURI(trim(style_id            )),
                    method      :encodeURI(trim('edit_style_user'   )),
                    send_url    :encodeURI(trim(send_url            ))
                },

            //事件
                beforeSend  :function(){
                //傳送前處理
                },
                success     :function(respones){
                //成功處理
                    location.href='user.php?user_id='+user_id+'&tab=8';
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

    $('.btn_edit_ok_request_rec_us_book').click(function(){
    //回覆書友推薦一本書籍給你

        var request_id=parseInt($(this).attr('request_id'));
        var obj       =$(this);

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
                request_id  :encodeURI(trim(request_id                      )),
                method      :encodeURI(trim('edit_ok_request_rec_us_book'   )),
                send_url    :encodeURI(trim(send_url                        ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                obj.parent().parent().parent().remove();
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

    function auto_request_rec_us_book(obj,request_id){
    //選擇書籍回應邀請

        try{
            var request_id=parseInt(request_id);
            var book_name=trim($(obj).text());
            var book_sid =trim(arry_book_info[book_name]);
            $('.request_rec_us_book_name[request_id='+request_id+']').val(book_name);
            $('.request_rec_us_book_sid[request_id='+request_id+']').val(book_sid);
        }catch(e){}
    }

    function edit_request_rec_us_book(obj,request_id){
    //回應書友請求推薦一本書籍給他

        var request_id      =parseInt(request_id);
        var $book_name      =trim($('.request_rec_us_book_name[request_id='+request_id+']').val());
        var $book_sid       =trim(trim($('.request_rec_us_book_sid[request_id='+request_id+']').val()));
        var $request_content=trim(trim($('.request_content[request_id='+request_id+']').val()));
        var arry_err=[];

        if(trim($book_name)===''){
            arry_err.push('請選擇或輸入一本書籍來回應書友的請求');
        }else{
            if(in_array($book_name,arry_book_name)){
                $book_sid=trim(trim(arry_book_info[trim($book_name)]));
            }else{
                arry_err.push('請選擇或輸入一本書籍來回應書友的請求');
            }
        }
        if(trim($request_content)==='' || trim($request_content)==='請輸入推薦這本書籍的理由'){
            arry_err.push('請輸入推薦這本書籍的理由');
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(!confirm('你確定要送出嗎?')){
                return false;
            }else{
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
                        request_id      :encodeURI(trim(request_id                  )),
                        book_sid        :encodeURI(trim($book_sid                   )),
                        request_content :(trim($request_content                     )),
                        method          :encodeURI(trim('edit_request_rec_us_book'  )),
                        send_url        :encodeURI(trim(send_url                    ))
                    },

                //事件
                    beforeSend  :function(){
                    //傳送前處理
                    },
                    success     :function(respones){
                    //成功處理
                    	newAlert();
						alert(respones);
						alert("HIHI");
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
        }
    }

    $('#Btn_add_request_book').click(function(){
	//請求書友推薦一本書籍給你

		var oForm2=$('.modal_request_book').find('#Form2')[0];
		var orequest_book_friend_names=document.getElementsByName('request_book_friend_name[]');
		var success_flag=true;
		var arry_has_sel=[];

		$(orequest_book_friend_names).each(function(){
			var orequest_book_friend_name=$(this)[0];
			if(trim(orequest_book_friend_name.value)===''){
				alert('請選擇一位書友');
				orequest_book_friend_name.focus();
				success_flag=false;
				return false;
			}else{
				if(!in_array(trim(orequest_book_friend_name.value),arry_friend_name)){
					alert('請選擇或輸入正確的書友');
					orequest_book_friend_name.focus();
					success_flag=false;
					return false;
				}
				if(!in_array(trim(orequest_book_friend_name.value),arry_has_sel)){
					arry_has_sel.push(trim(orequest_book_friend_name.value));
				}else{
					alert('請選擇不同的書友');
					orequest_book_friend_name.focus();
					success_flag=false;
					return false;
				}
			}
		});
		if(!success_flag)return false;
		if(confirm('你確定要送出嗎 ?')){
			oForm2.action='../controller/add.php'
			oForm2.submit();
			return true;
		}else{
			return false;
		}
	});

	function request_book_all_friends() {
	//請求所有書友推薦一本書籍給你
	    
		if (arry_friend_name.length != 0) {
			if (!confirm('你確定要一次邀請全部的書友推薦一本書給你嗎？')) {
				return false;
			} else {
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
						request_book_friend_name : arry_friend_name,
						method : encodeURI(trim('add_request_rec_us_book')),
						send_url : encodeURI(trim(send_url))
					},

				//事件
					beforeSend  :function(){
					//傳送前處理
					},
					success     :function(){
					//成功處理
						alert("邀請成功，請耐心等待書友回應");
						location.href="user.php?user_id=" + <?php echo $sess_user_id; ?> + "&tab=6";
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
		} else {
			alert("你目前沒有任何書友喔！\n趕快去邀請其他人成為你的書友吧！");
			return false;
		}
	}

    // function add_request_rec_us_book(obj,user_id){
    // //請求書友推薦一本書籍給你

    //     var user_id=parseInt(user_id);
    //     var $user_name=trim($('.user_name').val());
    //     var $user_id  =parseInt(trim($('.user_id').val()));
    //     var arry_err=[];

    //     if(isNaN(user_id)){
    //         if(trim($user_name)===''){
    //             arry_err.push('請選擇或輸入一位書友來請求推薦一本書籍給你');
    //         }else{
    //             if(in_array($user_name,arry_user_name)){
    //                 $user_id=parseInt(trim(arry_user_info[trim($user_name)]));
    //             }else{
    //                 arry_err.push('請選擇或輸入一位書友來請求推薦一本書籍給你');
    //             }
    //         }
    //     }else{
    //         $user_id=user_id;
    //     }

    //     if(arry_err.length!=0){
    //         alert(arry_err.join(nl));
    //         return false;
    //     }else{
    //         if(!confirm('你確定要請求嗎?')){
    //             return false;
    //         }else{
    //             $.ajax({
    //             //參數設置
    //                 async      :true,
    //                 cache      :false,
    //                 global     :true,
    //                 timeout    :50000,
    //                 contentType:"application/x-www-form-urlencoded; charset=UTF-8",
    //                 url        :"../controller/add.php",
    //                 type       :"POST",
    //                 datatype   :"json",
    //                 data       :{
    //                     user_id :encodeURI(trim($user_id                 )),
    //                     method  :encodeURI(trim('add_request_rec_us_book')),
    //                     send_url:encodeURI(trim(send_url                 ))
    //                 },

    //             //事件
    //                 beforeSend  :function(){
    //                 //傳送前處理
    //                 },
    //                 success     :function(respones){
    //                 //成功處理
    //                     alert(respones);
    //                     location.href="user.php?user_id=" + <?php echo $sess_user_id; ?> + "&tab=6";
    //                     return true;
    //                 },
    //                 error       :function(xhr, ajaxoptions, thrownerror){
    //                 //失敗處理
    //                 },
    //                 complete    :function(){
    //                 //傳送後處理
    //                 }
    //             });
    //         }
    //     }
    // }

    // function auto_user(obj){
    // //選擇書友邀請

    //     try{
    //         var user_name=trim($(obj).text());
    //         var user_id  =trim(arry_user_info[user_name]);
    //         $('.user_name').val(user_name);
    //         $('.user_id').val(user_id);
    //     }catch(e){}
    // }

	function auto_user(obj,no){
	//選擇書友邀請
		var request_book_friend_name   =trim($(obj).text());
		var no                         =parseInt(no);
		var orequest_book_friend_name  =document.getElementsByName('request_book_friend_name[]')[no];
		orequest_book_friend_name.value=trim(request_book_friend_name);
	}

    function request_friend(obj,friend_state){
    //回覆交友邀請

        var create_by   =parseInt($(obj).attr('create_by'));
        var user_id     =parseInt($(obj).attr('user_id'));
        var friend_id   =parseInt($(obj).attr('friend_id'));
        var friend_state=parseInt(friend_state);

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
                create_by       :encodeURI(trim(create_by               )),
                user_id         :encodeURI(trim(user_id                 )),
                friend_id       :encodeURI(trim(friend_id               )),
                friend_state    :encodeURI(trim(friend_state            )),
                method          :encodeURI(trim('edit_request_friend'   )),
                send_url        :encodeURI(trim(send_url                ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                $(obj).parent().parent().parent().find('button').remove();
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

    function request_join_us_group(obj,request_state){
    //回覆邀請加入小組

        var request_id   =parseInt($(obj).attr('request_id'));
        var request_state=parseInt(request_state);

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
                request_id      :encodeURI(trim(request_id                  )),
                request_state   :encodeURI(trim(request_state               )),
                method          :encodeURI(trim('edit_request_join_us_group')),
                send_url        :encodeURI(trim(send_url                    ))
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
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function request_join_to_group(obj,request_state){
    //回覆申請加入小組

        var request_id   =parseInt($(obj).attr('request_id'));
        var request_state=parseInt(request_state);

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
                request_id      :encodeURI(trim(request_id                  )),
                request_state   :encodeURI(trim(request_state               )),
                method          :encodeURI(trim('edit_request_join_to_group')),
                send_url        :encodeURI(trim(send_url                    ))
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
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function request_create_group(obj,request_state){
    //回覆聯署建立小組

        var request_id   =parseInt($(obj).attr('request_id'));
        var request_state=parseInt(request_state);

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
                request_id      :encodeURI(trim(request_id                 )),
                request_state   :encodeURI(trim(request_state              )),
                method          :encodeURI(trim('edit_request_create_group')),
                send_url        :encodeURI(trim(send_url                   ))
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
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    $('.btn_request_article_rev').click(function(){
    //回覆文章邀請

        var request_id=parseInt($(this).attr('request_id'));
        var obj       =$(this);

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
                request_id  :encodeURI(trim(request_id              )),
                method      :encodeURI(trim('edit_request_article'  )),
                send_url    :encodeURI(trim(send_url                ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                obj.parent().parent().parent().remove();
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

    $('.btn_del_friend').click(function(){
    //取消書友

        var friend_id=parseInt($(this).attr('friend_id'));

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
                friend_id   :encodeURI(trim(friend_id       )),
                method      :encodeURI(trim('del_friend'    )),
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
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    });

    $('.btn_add_friend').click(function(){
    //加為書友

        var user_id     =parseInt($(this).attr('user_id'));
        var friend_id   =parseInt($(this).attr('friend_id'));
        var content     =trim($('#add_friend_content').val());

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
                content     :(trim(content                  )),
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
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    });

	$('.btn_report_message').click(function(){
	//確認檢舉訊息

		var message_id = parseInt($(this).attr('message_id'));

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
				message_id     :encodeURI(trim(message_id)),
				method      :encodeURI(trim('check_report_message')),
				send_url    :encodeURI(trim(send_url))
			},

			//事件
			beforeSend  :function(){
			//傳送前處理
			},
			success     :function(respones){
			//成功處理
				location.href = respones;
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

    function load_right_side(fun){
    //讀取側邊欄

        var fun=trim(fun);

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
                user_id     :encodeURI(trim(user_id             )),
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

    function user_sticker_area(img, selection) {
    //個人大頭貼裁切

        var scaleX=160/selection.width;
        var scaleY=160/selection.height;

        $('#new_user_sticker').css({
            width:Math.round(scaleX*user_img_size[0]) + 'px',
            height:Math.round(scaleY*user_img_size[1]) + 'px',
            marginLeft:'-' + Math.round(scaleX * selection.x1) + 'px',
            marginTop:'-' + Math.round(scaleY * selection.y1) + 'px'
        });

        $('#user_sticker_x1').val(selection.x1);
        $('#user_sticker_y1').val(selection.y1);
        $('#user_sticker_x2').val(selection.x2);
        $('#user_sticker_y2').val(selection.y2);
        $('#user_sticker_w').val(selection.width);
        $('#user_sticker_h').val(selection.height);
    }

    $(document).on("click",function(e){
        //console.log(e.target.id);
        //$('#old_user_sticker').imgAreaSelect({hide:true});
    });

    function load_user_article(){
    //讀取使用者文章

        var page_article_cno=parseInt(parseInt($('#article').find('.row').length));
        var user_id=parseInt(<?php echo $user_id;?>);

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
                page_article_cno:encodeURI(trim(page_article_cno    )),
                user_id         :encodeURI(trim(user_id             )),
                method          :encodeURI(trim('load_user_article' ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
//console.log(respones);
//return false;
                var respones=jQuery.parseJSON(respones);
                if(parseInt(respones.length)!==0){
                    for(key in respones){
                        var json_html=respones[key];
                        //附加
                        $('#article').append(json_html);
                    }
                }
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

    function user_blade(tab){
        switch(parseInt(tab)){
            case 2:
                var user_blade_path='blade/user.book.php';
                $.ajax({
                //參數設置
                    async      :true,
                    cache      :false,
                    global     :true,
                    timeout    :50000,
                    contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                    url        :user_blade_path,
                    type       :"GET",
                    data       :{
                        user_id:user_id
                    },

                //事件
                    success     :function(respones){
                    //成功處理
                        $('#book').empty();
                        $('#book').append(respones);
                    }
                });
            break;

            case 3:
                var user_blade_path='blade/user.article.php';
                $.ajax({
                //參數設置
                    async      :true,
                    cache      :false,
                    global     :true,
                    timeout    :50000,
                    contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                    url        :user_blade_path,
                    type       :"GET",
                    data       :{
                        user_id:user_id
                    },

                //事件
                    success     :function(respones){
                    //成功處理
                        $('#article').empty();
                        $('#article').append(respones);
                    }
                });
            break;

            case 4:
                var user_blade_path='blade/user.group.php';
                $.ajax({
                //參數設置
                    async      :true,
                    cache      :false,
                    global     :true,
                    timeout    :50000,
                    contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                    url        :user_blade_path,
                    type       :"GET",
                    data       :{
                        user_id:user_id
                    },

                //事件
                    success     :function(respones){
                    //成功處理
                        $('#group').empty();
                        $('#group').append(respones);
                    }
                });
            break;

            case 5:
                var user_blade_path='blade/user.friend.php';
                $.ajax({
                //參數設置
                    async      :true,
                    cache      :false,
                    global     :true,
                    timeout    :50000,
                    contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                    url        :user_blade_path,
                    type       :"GET",
                    data       :{
                        user_id:user_id
                    },

                //事件
                    success     :function(respones){
                    //成功處理
                        $('#friend').empty();
                        $('#friend').append(respones);
                    }
                });
            break;

            case 7:
                var user_blade_path='blade/user.track.php';
                $.ajax({
                //參數設置
                    async      :true,
                    cache      :false,
                    global     :true,
                    timeout    :50000,
                    contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                    url        :user_blade_path,
                    type       :"GET",
                    data       :{
                        user_id:user_id
                    },

                //事件
                    success     :function(respones){
                    //成功處理
                        $('#track').empty();
                        $('#track').append(respones);
                    }
                });
            break;

			case 9:
				var user_blade_path='blade/user.message.php';
				$.ajax({
					//參數設置
					async      :true,
					cache      :false,
					global     :true,
					timeout    :50000,
					contentType:"application/x-www-form-urlencoded; charset=UTF-8",
					url        :user_blade_path,
					type       :"GET",
					data       :{
						user_id:user_id
					},

					//事件
					success     :function(respones){
						//成功處理
						$('#message').empty();
						$('#message').append(respones);
					}
				});
			break;
        }
    }


    //ONLOAD
    $(function(){
        //讀取側邊欄
        if(sess_user_id===user_id){
            load_right_side(trim('member_self'));
        }else{
            load_right_side(trim('member_other'));
        }
        //滾動監聽
        $(window).scroll(function(){
            if(user_article_cno>0){
                //偵測行動裝置
                if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
                    if($(window).scrollTop()>=($(document).height()-$(window).height())%2){
                        //讀取使用者文章
                        load_user_article();
                    }
                }else{
                    if($(window).scrollTop()==$(document).height()-$(window).height()){
                        //讀取使用者文章
                        load_user_article();
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
        //載入個人頁面樣式
        if(style_from===1){
            $('body').css("background-image","url(../img/default/style_user/bg_"+style_id+".jpg)");
        }else{
            <?php if(isset($file_server_enable)&&($file_server_enable)):?>
                $('body').css("background-image","url(http://<?php echo $arry_ftp1_info['host'];?>/mssr/info/user/"+user_id+"/forum/style_user/bg_"+style_id+".jpg)");
            <?php else:?>
                $('body').css("background-image","url(../../../info/user/"+user_id+"/forum/style_user/bg_"+style_id+".jpg)");
            <?php endif;?>
        }
        //個人大頭貼裁切
        try{
            $('#old_user_sticker').imgAreaSelect({aspectRatio:'1:1', onSelectChange:user_sticker_area});
            $('#old_user_sticker').imgAreaSelect({hide:true});
        }catch(e){}

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

        user_blade(tab);
    })

    //顯示完整書名提示
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });

    //增加請求推薦書籍人數欄位
    function clone_request_book_tag(){
		$('.request_book_friend_name').autocomplete('destroy');
		$('.request_book_group:last').after($('.request_book_group').eq(0).clone(true));
		$('.request_book_group').find("A").each(function(){
			$(this).replaceWith("<a href='javascript:void(0);'>"+trim($(this).text())+"</a>");
		});
		$('.request_book_group').each(function(){
			var $request_book_group=$(this);
			$request_book_group.find("A").click(function(){
				// auto_user($(this)[0],parseInt($request_book_group.index()-1));
				auto_user($(this)[0],parseInt($request_book_group.index()));
			});
		});
		$('.request_book_group').eq($('.request_book_group').length-1).find("INPUT").val('').focus();
		$('.request_book_friend_name').autocomplete({
			source: arry_friend_name
		});
	}

	//減少請求推薦書籍人數欄位
	function del_request_book_tag(){
		if(parseInt($('.request_book_group').length)>1){
			$('.request_book_group').eq($('.request_book_group').length-1).remove();
		}
		$('.request_book_group').eq($('.request_book_group').length-1).find("INPUT").focus();
	}

	//新增跳窗
	function newAlert() {
		//使用遮罩
		var mask = document.getElementById("mask");
		mask.style.display = "block";

		(function() {
			window.alert = function(text) {
				//自訂div樣式
				var alertDiv = document.createElement('div');
				alertDiv.id = 'alertDiv';
				alertDiv.style.position = 'fixed';
				alertDiv.style.display = 'none';
				alertDiv.style.overflow = 'hidden';
				alertDiv.style.width = '700px';
				alertDiv.style.padding = '20px 0px';
				alertDiv.style.top = '20%';
				alertDiv.style.left = '50%';
				alertDiv.style.marginLeft = '-350px';
				alertDiv.style.textAlign = 'center';
				alertDiv.style.lineHeight = '22px';
				alertDiv.style.border = '2px gray solid';
				alertDiv.style.boxShadow = '5px 5px 10px black';
				alertDiv.style.borderRadius = '20px';
				alertDiv.style.zIndex = '100';
				alertDiv.style.backgroundColor = 'white';

				str = '<font size=5>' + text + '</font>';
				str += '<br><br><button id=alertbtn style=background-color:#006dcc;color:white;border:none;font-size:20px;padding:10px;border-radius:8px;width:80px; onclick=location.reload();>確定</button>';
				alertDiv.innerHTML = str;

				$(document.body).append(alertDiv);

				//顯示
				$('#alertDiv').slideDown(500);
			};
		})();
	}

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
    $conn_user=NULL;
    $conn_mssr=NULL;
    //ftp_close($ftp_conn);
?>