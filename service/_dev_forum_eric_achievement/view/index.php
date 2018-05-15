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
            APP_ROOT.'lib/php/db/code',
            APP_ROOT.'service/_dev_forum_eric_achievement/inc/code'
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
    //$_SESSION['uid']=2029;
    //$_SESSION['uid']=5029;
    //$_SESSION['uid']=5030;
    //$_SESSION['uid']=5031;
    //$_SESSION['uid']=5032;
    //$_SESSION['uid']=5033;
    //$_SESSION['uid']=5034;
    //$_SESSION['uid']=5035;
    //$_SESSION['uid']=35526;

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

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //連線物件
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //動態牆 SQL
        //-----------------------------------------------
        //好友動態      SQL
        //追蹤書籍動態  SQL
        //加入小組動態  SQL

            if(!empty($arrys_sess_login_info)){
                //取得好友名單
                $arry_forum_friend=array();
                $forum_friend_list='';
                $friend_results   =get_forum_friend($sess_user_id,$friend_id=0,$arry_conn_mssr);
                if(!empty($friend_results)){
                    foreach($friend_results as $friend_result){
                        if((int)$friend_result['friend_state']===1){
                            if((int)$friend_result['user_id']!==$sess_user_id)$arry_forum_friend[]=$friend_result['user_id'];
                            if((int)$friend_result['friend_id']!==$sess_user_id)$arry_forum_friend[]=$friend_result['friend_id'];
                        }
                    }
                    $forum_friend_list=implode(',',$arry_forum_friend);
                }

                //取得追蹤書籍名單
                $arry_forum_track_book=array();
                $forum_track_book_list='';
                $sql="
                    SELECT `mssr_forum`.`mssr_forum_track_book`.`book_sid`
                    FROM `mssr_forum`.`mssr_forum_track_book`
                    WHERE `mssr_forum`.`mssr_forum_track_book`.`user_id`={$sess_user_id}
                    GROUP BY `mssr_forum`.`mssr_forum_track_book`.`user_id`, `mssr_forum`.`mssr_forum_track_book`.`book_sid`
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $db_result){
                        $rs_book_sid=trim($db_result['book_sid']);
                        $arry_forum_track_book[]=$rs_book_sid;
                    }
                    $forum_track_book_list="'".implode("','",$arry_forum_track_book)."'";
                }

                //取得加入小組名單
                $arry_forum_group_user=array();
                $forum_group_user_list='';
                $sql="
                    SELECT `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                    FROM `mssr_forum`.`mssr_forum_group_user_rev`
                        INNER JOIN `mssr_forum`.`mssr_forum_group` ON
                        `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                    WHERE `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`={$sess_user_id}
                        AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`=1
                        AND `mssr_forum`.`mssr_forum_group`.`group_state`=1
                    GROUP BY `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`, `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $db_result){
                        $rs_group_id=trim($db_result['group_id']);
                        $arry_forum_group_user[]=$rs_group_id;
                    }
                    $forum_group_user_list=implode(',',$arry_forum_group_user);
                }

                //動態牆SQL
                $wall_results=array();
                $wall_sql="";

                if(!empty($arry_forum_friend)){
                    $wall_sql.="
                        SELECT
                            `mssr_forum`.`mssr_forum_group`.`group_name`,
                            `mssr_forum`.`mssr_forum_group`.`group_content`,
                            `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,
                            `user`.`member`.`name`,
                            `user`.`member`.`sex`,
                            `mssr_forum`.`mssr_forum_article`.`user_id`,
                            `mssr_forum`.`mssr_forum_article`.`group_id`,
                            `mssr_forum`.`mssr_forum_article`.`article_id`,
                            `mssr_forum`.`mssr_forum_article`.`article_from`,
                            `mssr_forum`.`mssr_forum_article`.`keyin_cdate`,
                            `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                            `mssr_forum`.`mssr_forum_article_detail`.`article_content`
                        FROM `mssr_forum`.`mssr_forum_article`
                            INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                            `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                            INNER JOIN `user`.`member` ON
                            `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

                            LEFT JOIN `mssr_forum`.`mssr_forum_article_book_rev`  ON
                            `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_book_rev`.`article_id`

                            LEFT JOIN `mssr_forum`.`mssr_forum_group`  ON
                            `mssr_forum`.`mssr_forum_article`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_article`.`user_id` IN ($forum_friend_list)
                            AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                            AND `mssr_forum`.`mssr_forum_article`.`article_from` IN (1,2)
                    ";
                    if(!empty($arry_forum_track_book)||!empty($arry_forum_group_user)){
                        $wall_sql.="UNION";
                    }
                }
                if(!empty($arry_forum_track_book)){
                    $wall_sql.="
                        SELECT
                            `mssr_forum`.`mssr_forum_group`.`group_name`,
                            `mssr_forum`.`mssr_forum_group`.`group_content`,
                            `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,
                            `user`.`member`.`name`,
                            `user`.`member`.`sex`,
                            `mssr_forum`.`mssr_forum_article`.`user_id`,
                            `mssr_forum`.`mssr_forum_article`.`group_id`,
                            `mssr_forum`.`mssr_forum_article`.`article_id`,
                            `mssr_forum`.`mssr_forum_article`.`article_from`,
                            `mssr_forum`.`mssr_forum_article`.`keyin_cdate`,
                            `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                            `mssr_forum`.`mssr_forum_article_detail`.`article_content`
                        FROM `mssr_forum`.`mssr_forum_article`
                            INNER JOIN `mssr_forum`.`mssr_forum_article_book_rev` ON
                            `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_book_rev`.`article_id`

                            INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                            `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                            INNER JOIN `user`.`member` ON
                            `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

                            LEFT JOIN `mssr_forum`.`mssr_forum_group`  ON
                            `mssr_forum`.`mssr_forum_article`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_article`.`user_id` <> {$sess_user_id}
                            AND `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid` IN ($forum_track_book_list)
                            AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                            AND `mssr_forum`.`mssr_forum_article`.`article_from` IN (1,2)
                    ";
                    if(!empty($arry_forum_group_user)){
                        $wall_sql.="UNION";
                    }
                }
                if(!empty($arry_forum_group_user)){
                    $wall_sql.="
                        SELECT
                            `mssr_forum`.`mssr_forum_group`.`group_name`,
                            `mssr_forum`.`mssr_forum_group`.`group_content`,
                            `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,
                            `user`.`member`.`name`,
                            `user`.`member`.`sex`,
                            `mssr_forum`.`mssr_forum_article`.`user_id`,
                            `mssr_forum`.`mssr_forum_article`.`group_id`,
                            `mssr_forum`.`mssr_forum_article`.`article_id`,
                            `mssr_forum`.`mssr_forum_article`.`article_from`,
                            `mssr_forum`.`mssr_forum_article`.`keyin_cdate`,
                            `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                            `mssr_forum`.`mssr_forum_article_detail`.`article_content`
                        FROM `mssr_forum`.`mssr_forum_article`
                            INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                            `mssr_forum`.`mssr_forum_article`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`

                            INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                            `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                            INNER JOIN `user`.`member` ON
                            `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

                            LEFT JOIN `mssr_forum`.`mssr_forum_article_book_rev`  ON
                            `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_book_rev`.`article_id`

                            LEFT JOIN `mssr_forum`.`mssr_forum_group`  ON
                            `mssr_forum`.`mssr_forum_article`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_article`.`user_id` <> {$sess_user_id}
                            AND `mssr_forum`.`mssr_forum_article`.`group_id` IN ($forum_group_user_list)
                            AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                            AND `mssr_forum`.`mssr_forum_group`.`group_state`=1
                            AND `mssr_forum`.`mssr_forum_article`.`article_from` IN (1,2)
                    ";
                }

                //彙整
                if($wall_sql!==''){
                    $wall_sql.="ORDER BY `keyin_cdate` DESC";
                    $db_results=db_result($conn_type='pdo',$conn_mssr,$wall_sql,array(0,30),$arry_conn_mssr);
                    if(!empty($db_results)){

                        $tmp_arry_group_id=[];
                        $tmp_arry_book_sid=[];
                        $tmp_group_id     ='';
                        $tmp_book_sid     ='';

                        foreach($db_results as $db_result){
                            extract($db_result, EXTR_PREFIX_ALL, "rs");
                            $rs_group_name     =trim($rs_group_name);
                            $rs_group_content  =trim($rs_group_content);
                            $rs_book_sid       =trim($rs_book_sid);
                            $rs_user_sex       =(int)$rs_sex;
                            $rs_user_name      =trim($rs_name);
                            $rs_user_id        =(int)$rs_user_id;
                            $rs_group_id       =(int)$rs_group_id;
                            $rs_article_id     =(int)$rs_article_id;
                            $rs_article_from   =(int)$rs_article_from;
                            $rs_keyin_cdate    =trim($rs_keyin_cdate);
                            $rs_article_title  =trim($rs_article_title);
                            $rs_article_content=trim($rs_article_content);
                            $rs_time           =trim(strtotime($rs_keyin_cdate));
                            $arry_user_title   =get_member_title($rs_user_id,$arry_conn_mssr);
                            $rs_user_title     =(isset($arry_user_title[0]['title_name']))?trim("- ".$arry_user_title[0]['title_name']):'';

                            $wall_results[$rs_time][trim('group_name     ')]=$rs_group_name;
                            $wall_results[$rs_time][trim('group_content  ')]=$rs_group_content;
                            $wall_results[$rs_time][trim('book_sid       ')]=$rs_book_sid;
                            $wall_results[$rs_time][trim('user_sex       ')]=$rs_user_sex;
                            $wall_results[$rs_time][trim('user_name      ')]=$rs_user_name;
                            $wall_results[$rs_time][trim('user_title     ')]=$rs_user_title;
                            $wall_results[$rs_time][trim('user_id        ')]=$rs_user_id;
                            $wall_results[$rs_time][trim('group_id       ')]=$rs_group_id;
                            $wall_results[$rs_time][trim('article_id     ')]=$rs_article_id;
                            $wall_results[$rs_time][trim('article_from   ')]=$rs_article_from;
                            $wall_results[$rs_time][trim('keyin_cdate    ')]=$rs_keyin_cdate;
                            $wall_results[$rs_time][trim('article_title  ')]=$rs_article_title;
                            $wall_results[$rs_time][trim('article_content')]=$rs_article_content;
                            $wall_results[$rs_time][trim('time           ')]=$rs_time;

                            $tmp_arry_group_id[]=$rs_group_id;
                            $tmp_arry_book_sid[]=$rs_book_sid;
                        }
                        krsort($wall_results);

                        $tmp_arry_group_id=array_unique($tmp_arry_group_id);
                        $tmp_arry_book_sid=array_unique($tmp_arry_book_sid);
                        $tmp_group_id     =implode(",",$tmp_arry_group_id);
                        $tmp_book_sid     =implode("','",$tmp_arry_book_sid);

                        if(trim($tmp_group_id)!==''){
                            $sql="
                                SELECT `mssr_forum`.`mssr_forum_group`.`group_id`, `mssr_forum`.`mssr_forum_group`.`group_state`
                                FROM `mssr_forum`.`mssr_forum_group`
                                WHERE `mssr_forum`.`mssr_forum_group`.`group_id` IN ($tmp_group_id)
                            ";
                            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                            if(!empty($db_results)){
                                foreach($db_results as $db_result){
                                    extract($db_result, EXTR_PREFIX_ALL, "rs");
                                    $rs_group_id   =(int)$rs_group_id;
                                    $rs_group_state=(int)$rs_group_state;
                                    if($rs_group_state!==1){
                                        foreach($wall_results as $inx=>$wall_result){
                                            if($rs_group_id===(int)$wall_result['group_id']){
                                                unset($wall_results[$inx]);
                                            }
                                        }
                                    }
                                }
                            }
                            if(isset($sess_school_code)&&trim($sess_school_code)!==''){
                                $sql="
                                    SELECT `mssr_forum`.`mssr_forum_blacklist_group_school`.`group_id`
                                    FROM  `mssr_forum`.`mssr_forum_blacklist_group_school`
                                    WHERE 1=1
                                        AND `mssr_forum`.`mssr_forum_blacklist_group_school`.`school_code`= '{$sess_school_code  }'
                                        AND `mssr_forum`.`mssr_forum_blacklist_group_school`.`group_id`   IN ($tmp_group_id)
                                ";
                                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                if(!empty($db_results)){
                                    foreach($db_results as $db_result){
                                        extract($db_result, EXTR_PREFIX_ALL, "rs");
                                        $rs_group_id   =(int)$rs_group_id;
                                        foreach($wall_results as $inx=>$wall_result){
                                            if($rs_group_id===(int)$wall_result['group_id']){
                                                unset($wall_results[$inx]);
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if(trim($tmp_book_sid)!==''){
                            $sql="
                                SELECT
                                    `mssr`.`mssr_book_global`.`book_sid`,
                                    `mssr`.`mssr_book_global`.`book_name`,
                                    `mssr`.`mssr_book_global`.`book_author`,
                                    `mssr`.`mssr_book_global`.`book_publisher`
                                FROM `mssr`.`mssr_book_global`
                                WHERE `mssr`.`mssr_book_global`.`book_sid` IN ('{$tmp_book_sid}')
                                    UNION
                                SELECT
                                    `mssr`.`mssr_book_class`.`book_sid`,
                                    `mssr`.`mssr_book_class`.`book_name`,
                                    `mssr`.`mssr_book_class`.`book_author`,
                                    `mssr`.`mssr_book_class`.`book_publisher`
                                FROM `mssr`.`mssr_book_class`
                                WHERE `mssr`.`mssr_book_class`.`book_sid` IN ('{$tmp_book_sid}')
                                    UNION
                                SELECT
                                    `mssr`.`mssr_book_library`.`book_sid`,
                                    `mssr`.`mssr_book_library`.`book_name`,
                                    `mssr`.`mssr_book_library`.`book_author`,
                                    `mssr`.`mssr_book_library`.`book_publisher`
                                FROM `mssr`.`mssr_book_library`
                                WHERE `mssr`.`mssr_book_library`.`book_sid` IN ('{$tmp_book_sid}')
                                    UNION
                                SELECT
                                    `mssr`.`mssr_book_unverified`.`book_sid`,
                                    `mssr`.`mssr_book_unverified`.`book_name`,
                                    `mssr`.`mssr_book_unverified`.`book_author`,
                                    `mssr`.`mssr_book_unverified`.`book_publisher`
                                FROM `mssr`.`mssr_book_unverified`
                                WHERE `mssr`.`mssr_book_unverified`.`book_sid` IN ('{$tmp_book_sid}')

                            ";
                            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                            if(!empty($db_results)){
                                foreach($db_results as $db_result){
                                    extract($db_result, EXTR_PREFIX_ALL, "rs");
                                    $rs_book_sid      =trim($rs_book_sid);
                                    $rs_book_name     =trim($rs_book_name);
                                    $rs_book_author   =trim($rs_book_author);
                                    $rs_book_publisher=trim($rs_book_publisher);
                                    foreach($wall_results as $inx=>$wall_result){
                                        if($rs_book_sid===$wall_result['book_sid']){
                                            $wall_results[$inx][trim('book_name')]     =$rs_book_name;
                                            $wall_results[$inx][trim('book_author')]   =$rs_book_author;
                                            $wall_results[$inx][trim('book_publisher')]=$rs_book_publisher;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

        //-----------------------------------------------
        //熱門書籍 SQL
        //-----------------------------------------------

            $hot_book_results   =array();
            $curtime            =date("Y-m-d H:i:s");
            $query_start_time   =date('Y-m-d H:i:s',strtotime($curtime."-14 days"));
            $query_end_time     =$curtime;

            $sql="
                    SELECT
                        `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,
                        `mssr_forum`.`mssr_forum_article`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_article_book_rev`
                        INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                        `mssr_forum`.`mssr_forum_article_book_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_article`.`group_id`=0
                        AND `mssr_forum`.`mssr_forum_article`.`article_state`=1
                        AND `mssr_forum`.`mssr_forum_article`.`keyin_cdate` BETWEEN '{$query_start_time}' AND '{$query_end_time}'

                UNION ALL

                    SELECT
                        `mssr_forum`.`mssr_forum_reply_book_rev`.`book_sid`,
                        `mssr_forum`.`mssr_forum_reply`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_reply_book_rev`
                        INNER JOIN `mssr_forum`.`mssr_forum_reply` ON
                        `mssr_forum`.`mssr_forum_reply_book_rev`.`article_id`=`mssr_forum`.`mssr_forum_reply`.`article_id`

                        INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                        `mssr_forum`.`mssr_forum_reply`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_reply_book_rev`.`reply_id`=`mssr_forum`.`mssr_forum_reply`.`reply_id`
                        AND `mssr_forum`.`mssr_forum_reply`.`group_id`=0
                        AND `mssr_forum`.`mssr_forum_article`.`article_state`=1
                        AND `mssr_forum`.`mssr_forum_reply`.`reply_state`=1
                        AND `mssr_forum`.`mssr_forum_reply`.`keyin_cdate` BETWEEN '{$query_start_time}' AND '{$query_end_time}'

                LIMIT 100
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $db_result){
                    $rs_book_sid=trim($db_result['book_sid']);
                    $hot_book_results[]=$rs_book_sid;
                }
                $hot_book_results=array_count_values($hot_book_results);
                arsort($hot_book_results);
            }

        //-----------------------------------------------
        //熱門小組 SQL
        //-----------------------------------------------

            $hot_group_results=array();
            $sql="
                    SELECT
                        `mssr_forum`.`mssr_forum_group`.`group_id`,
                        `mssr_forum`.`mssr_forum_article`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_article`
                        INNER JOIN `mssr_forum`.`mssr_forum_group` ON
                        `mssr_forum`.`mssr_forum_article`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_article`.`group_id`<>0
                        AND `mssr_forum`.`mssr_forum_group`.`group_state`=1

                UNION ALL

                    SELECT
                        `mssr_forum`.`mssr_forum_group`.`group_id`,
                        `mssr_forum`.`mssr_forum_reply`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_reply`
                        INNER JOIN `mssr_forum`.`mssr_forum_group` ON
                        `mssr_forum`.`mssr_forum_reply`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_reply`.`group_id`<>0
                        AND `mssr_forum`.`mssr_forum_group`.`group_state`=1

                ORDER BY `keyin_cdate` DESC
                LIMIT 100;
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $db_result){
                    $rs_group_id=(int)$db_result['group_id'];
                    $hot_group_results[]=$rs_group_id;
                }
                $hot_group_results=array_count_values($hot_group_results);
                arsort($hot_group_results);
            }

        //-----------------------------------------------
        //名人區 SQL
        //-----------------------------------------------

            $cdate_filter=date("Y-m-d H:i:s",strtotime("-15 days"));
            $hot_member_results=array();
            $sql="
                    SELECT
                        `mssr_forum`.`mssr_forum_article`.`user_id`,
                        `mssr_forum`.`mssr_forum_article`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_article`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_article`.`keyin_cdate` > '{$cdate_filter}'

                UNION ALL

                    SELECT
                        `mssr_forum`.`mssr_forum_reply`.`user_id`,
                        `mssr_forum`.`mssr_forum_reply`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_reply`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_reply`.`keyin_cdate` > '{$cdate_filter}'

                ORDER BY `keyin_cdate` DESC
                LIMIT 100;
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $db_result){
                    $rs_user_id=(int)$db_result['user_id'];
                    $hot_member_results[]=$rs_user_id;
                }
                $hot_member_results=array_count_values($hot_member_results);
                arsort($hot_member_results);
            }

        //-----------------------------------------------
        //推薦好友 SQL
        //-----------------------------------------------

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
        //初始化書籍類別
        //-----------------------------------------------

            if((isset($sess_school_code))&&(trim($sess_school_code)!=='')&&!empty($arrys_sess_login_info)){
                default_book_category($sess_school_code,$sess_user_id,$conn_mssr,$arry_conn_mssr);
            }

        //-----------------------------------------------
        //初始化班級設置
        //-----------------------------------------------

            if(isset($sess_arrys_class_info[0]['class_code'])&&trim($sess_arrys_class_info[0]['class_code'])!==''&&!empty($arrys_sess_login_info)){
                $sess_class_code=trim($sess_arrys_class_info[0]['class_code']);
                @update_setting_class_user_upload($db_type='mysql',$arry_conn_mssr,$APP_ROOT,$sess_class_code,$sess_user_id);
            }

        //-----------------------------------------------
        //身分判斷
        //-----------------------------------------------

            $arry_user_status=array();
            if(!empty($arrys_sess_login_info)){
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
        //推播任務 SQL
        //-----------------------------------------------

            if(!empty($arrys_sess_login_info)){
                $sql="
                    SELECT
                        `mssr_forum`.`dev_group_mission`.`group_task_id`,
                        `mssr_forum`.`dev_group_mission`.`gask_topic`,
                        `mssr_forum`.`dev_group_mission`.`create_time`
                    FROM `mssr_forum`.`dev_group_mission`
                    WHERE 1=1
                        and `dev_group_mission`.`group_task_id` not in (2,3,4)
                    ORDER BY `create_time` DESC
                ";
                $group_mission_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            }

//        //-----------------------------------------------
//        //進行中任務 SQL
//        //-----------------------------------------------
//
//            if(!empty($arrys_sess_login_info)){
//                $sql="
//                    SELECT
//                        MAX(b.`step_number`) as step_number ,
//                        gask_topic,
//                        b.group_task_id
//                    FROM `mssr_forum`.`dev_mission_step_log` as a
//                        inner join `mssr_forum`.`dev_group_mission_master` as b
//                        on a.`master_task_id` = b.`master_task_id`
//
//                        inner join `mssr_forum`.dev_group_mission
//                        on b.group_task_id = dev_group_mission.group_task_id
//                    WHERE `accept_uid`={$sess_user_id}
//
//                        and a.start_step_time is not null
//                        and a.end_step_time is not  null
//
//                    GROUP BY b.group_task_id
//                    ORDER BY b.step_number DESC
//                ";
//                $dev_mission_step_log_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
//            }

//        //-----------------------------------------------
//        //FTP 登入
//        //-----------------------------------------------
//
//            //連接 | 登入 FTP
//            if(isset($file_server_enable)&&($file_server_enable)){
//                //FTP 路徑
//                $ftp_root="public_html/mssr/info/user";
//
//                $ftp_conn  =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
//                $ftp_login =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
//                //設定被動模式
//                ftp_pasv($ftp_conn,TRUE);
//            }

        //-----------------------------------------------
        //fb_api 外掛導入
        //-----------------------------------------------

            //fb 粉絲團
            $fb_api_page_plugin=new inc\fb_api\page_plugin;

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,明日聊書";

        //標籤
        $meta=meta($rd=1);

        //導覽列
        //$navbar=navbar($rd=1);

        //廣告牆
        $carousel=carousel($rd=1);

        //註腳列
        $footbar=footbar($rd=1);
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
    <link href="../../../lib/framework/smt-bootstrap/css/code.css" rel="stylesheet" type="text/css">

    <!-- 專屬 -->
    <link href="../css/site.css" rel="stylesheet" type="text/css">

    <!--[if (gte IE 6)&(lte IE 8)]>
        <script>self.location.href='../pages/browser_update/index.php'</script>
    <![endif]-->

    <!--[if lt IE 9]>
        <script src="../../../lib/js/html5/code.js"></script>
        <script src="../../../lib/js/css/code.js"></script>
    <![endif]-->

    <style>
        /* 頁面微調 */
        @media screen and (min-width: 992px) and (max-width: 1200px){
            .media-row .media-col-other{
                position: relative;
                left: 14px;
            }
            .media-row .media-col-other .media-other{
                display: inline-block;
                width: 380px;
                overflow: hidden;
            }
        }
        @media screen and (min-width: 1200px){
            .media-row .media-col-other{
                position: relative;
                left: 13px;
            }
            .media-row .media-col-other .media-other{
                display: inline-block;
                width: 465px;
                overflow: hidden;
            }
        }
    </style>
</head>
<body>

<!-- ************* waring: ************** -->
<!-- 請勿以非法方式探索進入本網站，當您看 -->
<!-- 到這個頁面時，您的現在以及後續的操作 -->
<!-- 動作會紀錄在本網站的紀錄裡。         -->
<!-- ************* waring: ************** -->

    <!-- fb 粉絲團初始化,start -->
    <?php echo $fb_api_page_plugin::page_plugin_init();?>
    <!-- fb 粉絲團初始化,end -->

    <!-- 導覽列,容器,start -->
    <?php //echo $navbar;?>
    <!-- 導覽列,容器,end -->

    <!-- 頁面,容器,start -->
    <div class="container">

        <!-- carousel,容器,start -->
        <?php //echo $carousel;?>
        <!-- carousel,容器,end -->

        <!-- 內容,start -->
        <div class="row">

            <div class="wall col-xs-12 col-sm-10 col-md-10 col-lg-10">

                <!-- 推薦好友,start -->
                <?php if(isset($rec_friend_results)&&!empty($rec_friend_results)):?>
                <div class="row rec_friend" style="position:relative;margin-top:-10px;">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 rec_friend_title">
                        <h3 style='color:#ff0000;'>
                            你可能認識的人......
                            <a href="javascript:void(0);" class="btn btn-xs btn-primary"
                            role="button" style="color:#ffffff;"
                            onclick="refresh_rec_friend(this);void(0);"
                            >手動刷新</a>
                        </h3>
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
                                    <p><a href="javascript:void(0);" class="btn_add_friend btn btn-primary" role="button" style="color:#ffffff;"
                                    user_id=<?php echo $sess_user_id;?>
                                    friend_id=<?php echo $rs_user_id;?>
                                    >加為好友</a></p>
                                </div>
                            </div>
                        </div>
                    <?php $cno++;}endforeach;?>
                </div>
                <?php endif;?>
                <!-- 推薦好友,容器,end -->

                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"
                    style="border:1px solid #e7e7e7;background-color:#ffeaea;padding:10px;">
                        <span class="label label-danger  label-ribbon-left">個人成就！！</span>
                        <img class="" src="../img/default/group_mission.jpg" width="100" height="100" border="0" alt="img" style="margin-right:5px;float:left;">
                        <p><strong>聊書系統新功能：個人成就</p>
                        <p><u><strong><a href='user.php?user_id=<?php echo $sess_user_id;?>&tab=9'>點我前往觀看</a></strong></u></p>
                    </div>
                </div>

                <?php if(isset($dev_mission_step_log_results)&&!empty($dev_mission_step_log_results)):?>
                    <div class="row">
                    <?php
                        foreach($dev_mission_step_log_results as $dev_mission_step_log_result):
                            $rs_step_number  =(int)$dev_mission_step_log_result['step_number'];
                            $rs_gask_topic   =trim($dev_mission_step_log_result['gask_topic']);
                            $rs_group_task_id=(int)$dev_mission_step_log_result['group_task_id'];

                            if($rs_step_number===3){
                                continue;
                                $rs_step_number="已完成任務";
                            }else{
                                $rs_step_number="第 {$rs_step_number} 步";
                            }
                    ?>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"
                        style="border:1px solid #e7e7e7;background-color:#ffff99;padding:10px;margin-bottom:10px;">
                            <img src="../img/default/group_mission.jpg" width="100" height="100" border="0" alt="img" style="margin-right:5px;float:left;">
                            <p><strong>【你目前進行中的任務】：<?php echo htmlspecialchars($rs_gask_topic);?></strong></p>
                            <p><strong>【任務進度】：<?php echo htmlspecialchars($rs_step_number);?></strong></p>
                            <p>&nbsp;&nbsp;<u><strong>
                                <a href='user.php?user_id=<?php echo $sess_user_id;?>&tab=10&group_task_id=<?php echo $rs_group_task_id;?>'
                                style="color:red;">點我前往任務主控台</a>
                            </strong></u></p>
                        </div>
                    <?php endforeach;?>
                    </div>
                <?php endif;?>

                <?php if(isset($group_mission_results)&&!empty($group_mission_results)):?>
                    <div class="row">
                    <?php
                        foreach($group_mission_results as $group_mission_result):
                            $rs_group_task_id=(int)$group_mission_result['group_task_id'];
                            $rs_gask_topic   =trim($group_mission_result['gask_topic']);
                            $rs_create_time  =trim($group_mission_result['create_time']);
                    ?>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"
                        style="border:1px solid #e7e7e7;background-color:#ffeaea;padding:10px;">
                            <span class="label label-danger  label-ribbon-left">推播任務！！</span>
                            <img class="" src="../img/default/group_mission.jpg" width="100" height="100" border="0" alt="img" style="margin-right:5px;float:left;">
                            <p><strong>任務題目：<?php echo htmlspecialchars($rs_gask_topic);?></strong></p>
                            <!-- <p><strong>截止時間：<?php echo htmlspecialchars($rs_create_time);?></strong></p> -->
                            <p><u><strong><a href='_dev_group_mission.php?get_from=3&group_task_id=<?php echo $rs_group_task_id;?>'>點我前往觀看</a></strong></u></p>
                        </div>
                    <?php endforeach;?>
                    </div>
                <?php endif;?>

                <!-- 動態牆,start -->
                <div class="row media-row">
                <?php if(isset($wall_results)&&!empty($wall_results)):?>
                    <?php
                    foreach($wall_results as $rs_time=>$wall_result):
                        $rs_group_name      =trim($wall_result[trim('group_name     ')]);
                        $rs_group_content   =trim($wall_result[trim('group_content  ')]);
                        $rs_book_sid        =trim($wall_result[trim('book_sid       ')]);
                        $rs_user_sex        =(int)$wall_result[trim('user_sex       ')];
                        $rs_user_name       =trim($wall_result[trim('user_name      ')]);
                        $rs_user_title      =trim($wall_result[trim('user_title     ')]);
                        $rs_user_id         =(int)$wall_result[trim('user_id        ')];
                        $rs_group_id        =(int)$wall_result[trim('group_id       ')];
                        $rs_article_id      =(int)$wall_result[trim('article_id     ')];
                        $rs_article_from    =(int)$wall_result[trim('article_from   ')];
                        $rs_keyin_cdate     =trim($wall_result[trim('keyin_cdate    ')]);
                        $rs_article_title   =trim($wall_result[trim('article_title  ')]);
                        $rs_article_content =trim($wall_result[trim('article_content')]);
                        $rs_book_name       =trim($wall_result[trim('book_name      ')]);
                        $rs_book_author     =trim($wall_result[trim('book_author    ')]);
                        $rs_book_publisher  =trim($wall_result[trim('book_publisher ')]);
                        $rs_user_img        ='../img/default/user_boy.png';

                        if($rs_user_sex===2)$rs_user_img ='../img/default/user_girl.png';

                        if(isset($file_server_enable)&&($file_server_enable)){
                            if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg")){
                                $rs_user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                            }
                        }else{
                            if(file_exists("../../../info/user/{$rs_user_id}/forum/user_sticker/1.jpg")){
                                $rs_user_img="../../../info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                            }
                        }

                        if($rs_group_id===0){
                            $get_from    =1;
                            $rs_img_1    =trim('../img/default/book.png');
                            if(file_exists("../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg")){
                                $rs_img_1="../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg";
                            }
                            $rs_href_1   =trim("article.php?get_from=1&book_sid={$rs_book_sid}");
                            $rs_href_2   =trim("article.php?get_from=1&book_sid={$rs_book_sid}");
                            $rs_content_1=trim("【<a href='article.php?get_from=1&book_sid={$rs_book_sid}' style='color:#324fe1;'>{$rs_book_name}</a>】 說");
                            $rs_content_2=trim("{$rs_book_name}");
                            $rs_content_3=trim("作者：{$rs_book_author}</br/>出版社：{$rs_book_publisher}");
                        }else{
                            $get_from    =2;
                            $rs_img_1    =trim('../img/default/group.jpg');
                            $rs_href_1   =trim("article.php?get_from=2&group_id={$rs_group_id}");
                            $rs_href_2   =trim("article.php?get_from=2&group_id={$rs_group_id}");
                            $rs_content_1=trim("【<a href='article.php?get_from=2&group_id={$rs_group_id}' style='color:#324fe1;'>{$rs_group_name}</a>】 說");
                            $rs_content_2=trim("{$rs_group_name}");
                            $rs_content_3=trim("小組簡介：{$rs_group_content}");
                        }

                        //動態消息是否為好友
                        $is_forum_friend    =false;
                        if($rs_user_id!==$sess_user_id){
                            $sql="
                                SELECT
                                    `mssr_forum`.`mssr_forum_friend`.`user_id`,
                                    `mssr_forum`.`mssr_forum_friend`.`friend_id`
                                FROM `mssr_forum`.`mssr_forum_friend`
                                WHERE 1=1
                                    AND (
                                        `mssr_forum`.`mssr_forum_friend`.`user_id`  ={$sess_user_id}
                                            OR
                                        `mssr_forum`.`mssr_forum_friend`.`friend_id`={$sess_user_id}
                                    )
                                    AND (
                                        `mssr_forum`.`mssr_forum_friend`.`user_id`  ={$rs_user_id}
                                            OR
                                        `mssr_forum`.`mssr_forum_friend`.`friend_id`={$rs_user_id}
                                    )
                                    AND `mssr_forum`.`mssr_forum_friend`.`friend_state`=1
                            ";
                            $friend_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                            if(!empty($friend_results))$is_forum_friend=true;
                        }

                        if(!$is_forum_friend){
                            if(mb_strlen($rs_article_content)>250){
                                $rs_article_content=mb_substr($rs_article_content,0,250)."..";
                            }
                        }else{
                            if(mb_strlen($rs_article_content)>100){
                                $rs_article_content=mb_substr($rs_article_content,0,100)."..";
                            }
                        }
                        if($rs_article_from!==3){
                            $rs_article_content="
                                <a target='_blank' href='reply.php?get_from={$get_from}&article_id={$rs_article_id}'>
                                    {$rs_article_content}
                                </a>
                            ";
                        }else{
                           $sql="
                                SELECT `group_task_id`
                                FROM `mssr_forum`.`dev_article_group_mission_rev`
                                WHERE `article_id`={$rs_article_id}
                            ";
                            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                            if(!empty($db_results)){
                                $rs_group_task_id=(int)($db_results[0]['group_task_id']);
                            }
                            $rs_article_content="
                                <a target='_blank' href='_dev_group_mission.php?get_from=3&group_task_id={$rs_group_task_id}&article_id={$rs_article_id}'>
                                    {$rs_article_content}
                                </a>
                            ";
                        }
                ?>
                    <?php echo wall();?>
                <?php endforeach;else:?>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="media" style="display:none;">
                            <a class="pull-left" href="javascript:void(0);"></a>
                            <div class="media-body">
                                <h4 class="media-heading">歡迎蒞臨明日聊書！ 您目前無任何動態消息...</h4>
                            </div>
                        </div>
                    </div>
                <?php endif;?>
                </div>
                <!-- 動態牆,end -->

            </div>

            <!-- right_side,start -->
            <div class="right_side col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <div class="row hidden-xs" style="border:0px solid #dddddd;border-radius:5px;margin-top:0px;">
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden" style="color:#9197a3;margin-top:10px;margin-left:-10px;">
                        <b>常用</b>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden">
                        <a href="user.php?user_id=<?php //echo $sess_user_id;?>&tab=6">
                            <em class="glyphicon glyphicon-comment"></em> 我的訊息 <span class='hidden'>Messages</span>
                        </a>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden">
                        <a href="user.php?user_id=<?php //echo $sess_user_id;?>&tab=2">
                            <em class="glyphicon glyphicon-book"></em> 我的書櫃 <span class='hidden'>Bookcases</span>
                        </a>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden">
                        <a href="user.php?user_id=<?php //echo $sess_user_id;?>&tab=3">
                            <em class="glyphicon glyphicon-pencil"></em> 我的討論 <span class='hidden'>Articles</span>
                        </a>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden">
                        <a href="user.php?user_id=<?php //echo $sess_user_id;?>&tab=5">
                            <em class="glyphicon glyphicon-user"></em> 我的好友 <span class='hidden'>Friends</span>
                        </a>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden">
                        <a href="user.php?user_id=<?php //echo $sess_user_id;?>&tab=4">
                            <em class="glyphicon glyphicon-star"></em> 我的小組 <span class='hidden'>Groups</span>
                        </a>
                    </div>

                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12" style="color:#9197a3;margin-left:-10px;">
                        <b>本週熱門</b>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <a href="forum.php?method=view_hot_booklist">
                            <em class="glyphicon glyphicon glyphicon-list-alt"></em> 熱門書單 <span class='hidden'>Hot Booklist</span>
                        </a>
                    </div>

                    <?php if(in_array('i_a',$arry_user_status)||in_array('i_t',$arry_user_status)||in_array('i_sa',$arry_user_status)):?>
                        <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12" style="color:#9197a3;margin-top:10px;margin-left:-10px;">
                            <b>快速搜尋</b>
                        </div>
                        <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <a href="forum.php?method=view_all_group">
                                <em class="glyphicon glyphicon-star"></em> 所有小組 <span class='hidden'>All Group</span>
                            </a>
                        </div>
                    <?php endif;?>
                </div>

                <table class="table" style="margin-top:25px;">
                    <thead>
                        <tr><td class="text-center">熱門書籍</td></tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($hot_book_results)){
                            $cno=0;
                            foreach($hot_book_results as $rs_book_sid=>$hot_book_cno){
                                $rs_book_sid=trim($rs_book_sid);
                                if($cno<5){
                                    if(preg_match("/^mbu/i",$rs_book_sid)){
                                        $get_book_info=get_book_info($conn_mssr,trim($rs_book_sid),$array_filter=array('book_verified'),$arry_conn_mssr);
                                        if(!empty($get_book_info)){
                                            $rs_book_verified=(int)$get_book_info[0]['book_verified'];
                                            if($rs_book_verified===2)continue;
                                        }else{continue;}
                                    }
                                    $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                    if(empty($arry_book_infos))continue;
                                    $rs_book_name=trim($arry_book_infos[0]['book_name']);

                                    if(mb_strlen($rs_book_name)>10){
                                        $rs_book_name=mb_substr($rs_book_name,0,10)."..";
                                    }
                        ?>
                        <tr><td><a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_book_sid);?>">
                            <?php echo htmlspecialchars($rs_book_name);?>
                        </a></td></tr>
                        <?php $cno++;}}}else{?>
                        <tr><td>
                            目前無任何書籍資訊...
                        </td></tr>
                        <?php }?>
                    </tbody>
                </table>

                <table class="table">
                    <thead>
                        <tr><td class="text-center">熱門小組</td></tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($hot_group_results)){
                            $cno=0;
                            foreach($hot_group_results as $rs_group_id=>$hot_group_cno){
                                $rs_group_id=(int)$rs_group_id;
                                if($cno<5){
                                    if($rs_group_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
                                        $arry_blacklist_group_school=get_blacklist_group_school($sess_school_code,$rs_group_id,$arry_conn_mssr);
                                        if(!empty($arry_blacklist_group_school))continue;
                                    }
                                    $sql="
                                        SELECT
                                            `mssr_forum`.`mssr_forum_group`.`group_name`
                                        FROM `mssr_forum`.`mssr_forum_group`
                                        WHERE 1=1
                                            AND `mssr_forum`.`mssr_forum_group`.`group_id`={$rs_group_id}
                                    ";
                                    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                    if(empty($db_results))continue;
                                    $rs_group_name=trim($db_results[0]['group_name']);

                                    if(mb_strlen($rs_group_name)>10){
                                        $rs_group_name=mb_substr($rs_group_name,0,10)."..";
                                    }
                        ?>
                        <tr><td><a href="article.php?get_from=2&group_id=<?php echo addslashes($rs_group_id);?>">
                            <?php echo htmlspecialchars($rs_group_name);?>
                        </a></td></tr>
                        <?php $cno++;}}}else{?>
                        <tr><td>
                            目前無任何小組資訊...
                        </td></tr>
                        <?php }?>
                    </tbody>
                </table>

                <table class="table">
                    <thead>
                        <tr><td class="text-center">名人區</td></tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($hot_member_results)){
                            $cno=0;
                            foreach($hot_member_results as $rs_user_id=>$hot_member_cno){
                                $rs_user_id=(int)$rs_user_id;
                                if($cno<5){
                                    $sql="
                                        SELECT
                                            `user`.`member`.`name`
                                        FROM `user`.`member`
                                        WHERE 1=1
                                            AND `user`.`member`.`uid`={$rs_user_id}
                                    ";
                                    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                    if(empty($db_results))continue;
                                    $rs_user_name=trim($db_results[0]['name']);
                        ?>
                        <tr><td><a href="user.php?user_id=<?php echo $rs_user_id;?>&tab=1">
                            <?php echo htmlspecialchars($rs_user_name);?>
                        </a></td></tr>
                        <?php $cno++;}}}else{?>
                        <tr><td>
                            目前無任何小組資訊...
                        </td></tr>
                        <?php }?>
                    </tbody>
                </table>

                <table class="table">
                    <thead>
                        <tr><td class="text-center">粉絲團</td></tr>
                    </thead>
                </table>
                <!-- fb 粉絲團崁入,start -->
                <?php echo $fb_api_page_plugin::page_plugin_show();?>
                <!-- fb 粉絲團崁入,end -->
            </div>
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

</body>

<!-- 通用 -->
<script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>
<script type="text/javascript" src="../../../lib/js/string/code.js"></script>

<!-- 專屬 -->
<script type="text/javascript" src="../inc/code.js"></script>

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var send_url=document.URL;
    <?php if(isset($sess_user_id)&&((int)$sess_user_id)!==0):?>
        var sess_user_id=parseInt(<?php echo $sess_user_id;?>);
    <?php else:?>
        var sess_user_id=parseInt(0);
    <?php endif;?>
    <?php if(isset($wall_results)&&((int)count($wall_results))!==0):?>
        var wall_cno =parseInt(<?php echo count($wall_results);?>);
    <?php else:?>
        var wall_cno =parseInt(0);
    <?php endif;?>


    //OBJ


    //FUNCTION
    function refresh_rec_friend(obj){
    //刷新推薦的好友

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
            html+=          '<p><a href="javascript:void(0);" class="btn_add_friend btn btn-primary" role="button" style="color:#ffffff;"';
            html+=          ' user_id='+sess_user_id+'';
            html+=          ' friend_id='+uid+'';
            html+=          '>加為好友</a></p>';
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

    $(document).on('click',".btn_add_friend",function(){
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
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    });

    function media_effect(){
    //動態牆效果

        var $medias=$('.media:hidden');
        var browserwidth=parseInt(getbrowserwidth());

        for(var i=0;i<$medias.length;i++){
            $media=$medias.eq(i);
            $media.delay(50+((i+1)*50)).fadeIn(500,function(){
            });
        }

        //解析度 md
        if(browserwidth>=992 && browserwidth<1200){
            $('.media-row').imagesLoaded(function(){
                $('.media-row').masonry({
                    itemSelector: '.media-other',
                    columnWidth: 400,
                    isAnimated: true
                });
            });
        }
        //解析度 lg
        if(browserwidth>=1200){
            $('.media-row').imagesLoaded(function(){
                $('.media-row').masonry({
                    itemSelector: '.media-other',
                    columnWidth: 485,
                    isAnimated: true
                });
            });
        }

        function getbrowserwidth(){
            if ($.browser.msie){
                return document.compatMode == "CSS1Compat" ? document.documentElement.clientWidth :
                document.body.clientWidth;
            }else{
                return self.innerWidth;
            }
        }
    }

    function load_wall(){
    //讀取動態牆

        var wall_cno=parseInt(parseInt($('div.media-row > div').length)/2);

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/load.php",
            type       :"POST",
            dataType   :"json",
            data       :{
                wall_cno:encodeURI(trim(wall_cno    )),
                method  :encodeURI(trim('load_wall' ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                //var respones=jQuery.parseJSON(respones);
                if(parseInt(respones.length)!==0){
                    for(key in respones){
                        var json_html=respones[key];
                        //附加
                        $('div.media-row').append(json_html);
                        var $medias=$('.media:hidden');
                        for(var i=0;i<$medias.length;i++){
                            $media=$medias.eq(i);
                            $media.delay(50+((i+1)*50)).fadeIn(1000,function(){
                            });
                        }
                        //$('div.media-row').masonry('reload');
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
    }


    //ONLOAD
    $(function(){
        //滾動監聽
        $(window).scroll(function(){
            if(wall_cno>0){
                //偵測行動裝置
                if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
                    //讀取動態牆
                    if($(window).scrollTop()>=($(document).height()-$(window).height())%2)load_wall();
                }else{
                    //讀取動態牆
                    if($(window).scrollTop()==$(document).height()-$(window).height())load_wall();
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
        ////動態牆效果
        //media_effect();

        setTimeout(function(){
            $.ajax({
            //參數設置
                async      :true,
                cache      :false,
                global     :true,
                timeout    :50000,
                contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                url        :"navbar.php",
                type       :"POST",
                data       :{
                },

            //事件
                beforeSend  :function(){
                //傳送前處理
                },
                success     :function(respones){
                //成功處理
                    $('.container').before(respones);
                },
                error       :function(xhr, ajaxoptions, thrownerror){
                //失敗處理
                },
                complete    :function(){
                //傳送後處理
                }
            });
        }, 500);
    })


</script>
<script type="text/javascript" src="../../../inc/external/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//呼叫使用者頁面紀錄
//-------------------------------------------------------
    //user_page_log(rd=3);
</script>
<!-- google analytics -->
<?php echo google_track();?>
</html>
<?php
    $conn_user=NULL;
    $conn_mssr=NULL;
?>