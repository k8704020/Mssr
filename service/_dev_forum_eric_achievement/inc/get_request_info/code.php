<?php
//-------------------------------------------------------
//函式: get_request_info()
//用途: 取得邀請資訊
//-------------------------------------------------------

    ////---------------------------------------------------
    ////測試
    ////---------------------------------------------------
    //
    //    //外掛設定檔
    //    require_once(str_repeat("../",4).'config/config.php');
    //    require_once(str_repeat("../",4).'inc/code.php');
    //    require_once(str_repeat("../",4).'lib/php/db/code.php');
    //    $conn_user=conn($db_type='mysql',$arry_conn_user);
    //    $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
    //
    //    $get_request_info=get_request_info($user_id=5030,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
    //    echo "<Pre>";
    //    print_r($get_request_info);
    //    echo "</Pre>";

    function get_request_info($user_id,$conn_user=NULL,$conn_mssr=NULL,$arry_conn_user,$arry_conn_mssr){
    //---------------------------------------------------
    //函式: get_request_info()
    //用途: 取得邀請資訊
    //---------------------------------------------------
    //$user_id              使用者主索引
    //$conn_user            user 資料庫連線物件
    //$conn_mssr            mssr 資料庫連線物件
    //$arry_conn_user       user 資料庫連線資訊陣列
    //$arry_conn_mssr       mssr 資料庫連線資訊陣列
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($user_id)){
                return false;
            }else{
                $user_id=(int)$user_id;
                if($user_id===0){
                    return false;
                }
            }

            if(!isset($conn_user)||!isset($conn_mssr)||!isset($arry_conn_user)||!isset($arry_conn_mssr)){
                return false;
            }

        //-----------------------------------------------
        //訊息撈取
        //-----------------------------------------------

            $arrys_msg=array();

//            //-------------------------------------------
//            //推播任務收到提醒
//            //-------------------------------------------
//
//                $sql="
//                    SELECT
//                        IFNULL((
//                            SELECT
//                                `user`.`member`.`sex`
//                            FROM `user`.`member`
//                            WHERE `mssr_forum`.`dev_complete_mission_log`.`deliver_uid`=`user`.`member`.`uid`
//                            LIMIT 1
//                        ),1) AS `request_from_sex`,
//
//                        IFNULL((
//                            SELECT
//                                `user`.`member`.`sex`
//                            FROM `user`.`member`
//                            WHERE `mssr_forum`.`dev_complete_mission_log`.`accept_uid`=`user`.`member`.`uid`
//                            LIMIT 1
//                        ),'') AS `request_to_sex`,
//
//                        IFNULL((
//                            SELECT
//                                `user`.`member`.`name`
//                            FROM `user`.`member`
//                            WHERE `mssr_forum`.`dev_complete_mission_log`.`deliver_uid`=`user`.`member`.`uid`
//                            LIMIT 1
//                        ),'') AS `request_from_name`,
//
//                        IFNULL((
//                            SELECT
//                                `user`.`member`.`name`
//                            FROM `user`.`member`
//                            WHERE `mssr_forum`.`dev_complete_mission_log`.`accept_uid`=`user`.`member`.`uid`
//                            LIMIT 1
//                        ),'') AS `request_to_name`,
//
//                        `mssr_forum`.`dev_group_mission`.`group_task_id`,
//                        `mssr_forum`.`dev_group_mission`.`gask_topic`,
//
//                        `mssr_forum`.`dev_complete_mission_log`.`accept_uid` AS `request_to`,
//                        `mssr_forum`.`dev_complete_mission_log`.`deliver_uid` AS `request_from`,
//                        `mssr_forum`.`dev_complete_mission_log`.`start_time` AS `keyin_cdate`
//                    FROM `mssr_forum`.`dev_complete_mission_log`
//                        INNER JOIN `mssr_forum`.`dev_group_mission` ON
//                        `mssr_forum`.`dev_complete_mission_log`.`group_task_id`=`mssr_forum`.`dev_group_mission`.`group_task_id`
//                    WHERE 1=1
//                        AND `mssr_forum`.`dev_complete_mission_log`.`accept_uid`   ={$user_id}
//                        AND `mssr_forum`.`dev_complete_mission_log`.`mission_state`=0
//                ";
//                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
//                if(!empty($db_results)){
//                    foreach($db_results as $inx=>$db_result){
//                        extract($db_result, EXTR_PREFIX_ALL, "rs");
//
//                        $rs_request_from_sex =(int)$rs_request_from_sex;
//                        $rs_request_from_name=trim($rs_request_from_name);
//                        $rs_request_from     =trim($rs_request_from);
//                        $rs_request_to_sex   =(int)$rs_request_to_sex;
//                        $rs_request_to_name  =trim($rs_request_to_name);
//                        $rs_request_to       =trim($rs_request_to);
//                        $rs_group_task_id    =(int)$rs_group_task_id;
//                        $rs_gask_topic       =trim($rs_gask_topic);
//                        $rs_time             =trim(strtotime($rs_keyin_cdate));
//                        $rs_request_to_name  ='你';
//
//                        $arrys_msg[$rs_time][trim('accept_group_mission')][trim('request_from_sex   ')]=$rs_request_from_sex;
//                        $arrys_msg[$rs_time][trim('accept_group_mission')][trim('request_from_name  ')]=$rs_request_from_name;
//                        $arrys_msg[$rs_time][trim('accept_group_mission')][trim('request_from       ')]=$rs_request_from;
//                        $arrys_msg[$rs_time][trim('accept_group_mission')][trim('gask_topic         ')]=$rs_gask_topic;
//                        $arrys_msg[$rs_time][trim('accept_group_mission')][trim('group_task_id      ')]=$rs_group_task_id;
//                        $arrys_msg[$rs_time][trim('accept_group_mission')][trim('request_to_sex     ')]=$rs_request_to_sex;
//                        $arrys_msg[$rs_time][trim('accept_group_mission')][trim('request_to_name    ')]=$rs_request_to_name;
//                        $arrys_msg[$rs_time][trim('accept_group_mission')][trim('request_to         ')]=$rs_request_to;
//                        $arrys_msg[$rs_time][trim('accept_group_mission')][trim('request_id         ')]=0;
//                        $arrys_msg[$rs_time][trim('accept_group_mission')][trim('request_state      ')]=0;
//                        $arrys_msg[$rs_time][trim('accept_group_mission')][trim('request_read       ')]=0;
//                        $arrys_msg[$rs_time][trim('accept_group_mission')][trim('keyin_cdate        ')]=$rs_keyin_cdate;
//                        $arrys_msg[$rs_time][trim('accept_group_mission')][trim('rev_id             ')]=0;
//                    }
//                }
//
//            //-------------------------------------------
//            //朋友接受推播任務
//            //-------------------------------------------
//
//                $curdate=date("Y-m-d H:i:s");
//                $predate=date("Y-m-d H:i:s",strtotime('-1 day'));
//
//                $sql="
//                    SELECT
//                        `mssr_forum`.`mssr_forum_friend`.`user_id`,
//                        `mssr_forum`.`mssr_forum_friend`.`friend_id`
//                    FROM `mssr_forum`.`mssr_forum_friend`
//                    WHERE 1=1
//                        AND (
//                            `mssr_forum`.`mssr_forum_friend`.`user_id`  ={$user_id}
//                            OR
//                            `mssr_forum`.`mssr_forum_friend`.`friend_id`={$user_id}
//                        )
//                        AND `mssr_forum`.`mssr_forum_friend`.`friend_state`=1
//                ";
//                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
//                $arry_friend_id=array();
//                foreach($db_results as $db_result){
//                    $rs_user_id  =(int)$db_result['user_id'];
//                    $rs_friend_id=(int)$db_result['friend_id'];
//                    if(!in_array($rs_user_id,$arry_friend_id)){
//                        $arry_friend_id[]=$rs_user_id;
//                    }
//                    if(!in_array($rs_friend_id,$arry_friend_id)){
//                        $arry_friend_id[]=$rs_friend_id;
//                    }
//                }
//                $arry_friend_id=array_diff($arry_friend_id,array($user_id));
//                $list_friend_id=implode(",",$arry_friend_id);
//
//                if(trim($list_friend_id)!==''){
//                    $sql="
//                        SELECT
//                            `user`.`member`.`name` AS `request_from_name`,
//                            `user`.`member`.`sex` AS `request_from_sex`,
//
//                            `mssr_forum`.`dev_group_mission`.`group_task_id`,
//                            `mssr_forum`.`dev_group_mission`.`gask_topic`,
//
//                            `mssr_forum`.`dev_complete_mission_log`.`accept_uid` AS `request_from`,
//                            `mssr_forum`.`dev_complete_mission_log`.`start_time` AS `keyin_cdate`
//                        FROM `mssr_forum`.`dev_complete_mission_log`
//                            INNER JOIN `mssr_forum`.`dev_group_mission` ON
//                            `mssr_forum`.`dev_complete_mission_log`.`group_task_id`=`mssr_forum`.`dev_group_mission`.`group_task_id`
//
//                            INNER JOIN `user`.`member` ON
//                            `mssr_forum`.`dev_complete_mission_log`.`accept_uid`=`user`.`member`.`uid`
//                        WHERE 1=1
//                            AND `mssr_forum`.`dev_complete_mission_log`.`accept_uid`   IN ({$list_friend_id})
//                            AND `mssr_forum`.`dev_complete_mission_log`.`mission_state`=2
//                            AND `mssr_forum`.`dev_complete_mission_log`.`start_time`   >'{$predate}'
//                    ";
//                    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
//                    if(!empty($db_results)){
//                        foreach($db_results as $inx=>$db_result){
//                            extract($db_result, EXTR_PREFIX_ALL, "rs");
//
//                            $rs_request_from_sex =(int)$rs_request_from_sex;
//                            $rs_request_from_name=trim($rs_request_from_name);
//                            $rs_request_from     =trim($rs_request_from);
//                            $rs_group_task_id    =(int)$rs_group_task_id;
//                            $rs_gask_topic       =trim($rs_gask_topic);
//                            $rs_time             =trim(strtotime($rs_keyin_cdate));
//
//                            $arrys_msg[$rs_time][trim('friends_accept_group_mission')][trim('request_from_sex   ')]=$rs_request_from_sex;
//                            $arrys_msg[$rs_time][trim('friends_accept_group_mission')][trim('request_from_name  ')]=$rs_request_from_name;
//                            $arrys_msg[$rs_time][trim('friends_accept_group_mission')][trim('request_from       ')]=$rs_request_from;
//                            $arrys_msg[$rs_time][trim('friends_accept_group_mission')][trim('gask_topic         ')]=$rs_gask_topic;
//                            $arrys_msg[$rs_time][trim('friends_accept_group_mission')][trim('group_task_id      ')]=$rs_group_task_id;
//                            $arrys_msg[$rs_time][trim('friends_accept_group_mission')][trim('request_to_sex     ')]=0;
//                            $arrys_msg[$rs_time][trim('friends_accept_group_mission')][trim('request_to_name    ')]='';
//                            $arrys_msg[$rs_time][trim('friends_accept_group_mission')][trim('request_to         ')]=0;
//                            $arrys_msg[$rs_time][trim('friends_accept_group_mission')][trim('request_id         ')]=0;
//                            $arrys_msg[$rs_time][trim('friends_accept_group_mission')][trim('request_state      ')]=0;
//                            $arrys_msg[$rs_time][trim('friends_accept_group_mission')][trim('request_read       ')]=0;
//                            $arrys_msg[$rs_time][trim('friends_accept_group_mission')][trim('keyin_cdate        ')]=$rs_keyin_cdate;
//                            $arrys_msg[$rs_time][trim('friends_accept_group_mission')][trim('rev_id             ')]=0;
//                        }
//                    }
//                }

            //-------------------------------------------
            //發出的文章 - 被按讚
            //-------------------------------------------

                $curdate=date("Y-m-d");
                $predate=date("Y-m-d",strtotime('-1 day'));

                $sql="
                    SELECT
                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_article_like_log`.`user_id`=`user`.`member`.`uid`
                            LIMIT 1
                        ),1) AS `request_from_sex`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_sex`,

                        IFNULL((
                            SELECT `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE 1=1
                                AND `user`.`member`.`uid`=`mssr_forum`.`mssr_forum_article`.`user_id`
                            LIMIT 1
                        ),'') AS `article_user_name`,

                        IFNULL((
                            SELECT `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE 1=1
                                AND `user`.`member`.`uid`=`mssr_forum`.`mssr_forum_article_like_log`.`user_id`
                            LIMIT 1
                        ),'') AS `like_user_name`,

                        `mssr_forum`.`mssr_forum_article`.`user_id` AS `article_user_id`,
                        `mssr_forum`.`mssr_forum_article`.`group_id`,
                        `mssr_forum`.`mssr_forum_article`.`article_id`,
                        `mssr_forum`.`mssr_forum_article_detail`.`article_title`,

                        `mssr_forum`.`mssr_forum_article_like_log`.`user_id` AS `like_user_id`,
                        `mssr_forum`.`mssr_forum_article_like_log`.`keyin_mdate` AS `keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_article`
                        INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                        `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                        INNER JOIN `mssr_forum`.`mssr_forum_article_like_log` ON
                        `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_like_log`.`article_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_article`.`article_state`   =1
                        AND `mssr_forum`.`mssr_forum_article`.`user_id`         ={$user_id}
                        AND `mssr_forum`.`mssr_forum_article_like_log`.`user_id`<>{$user_id}
                        AND `mssr_forum`.`mssr_forum_article_like_log`.`keyin_mdate` BETWEEN '{$predate} 00:00:00' AND '{$curdate} 23:59:59'
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $inx=>$db_result){
                        extract($db_result, EXTR_PREFIX_ALL, "rs");

                        $rs_request_from_sex    =(int)$rs_request_from_sex;
                        $rs_request_to_sex      =(int)$rs_request_to_sex;
                        $rs_request_from_name   =trim($rs_like_user_name);
                        $rs_request_to_name     =trim($rs_article_user_name);
                        $rs_request_from        =(int)$rs_like_user_id;
                        $rs_request_to          =(int)$rs_article_user_id;
                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_group_id            =(int)$rs_group_id;
                        $rs_article_id          =(int)$rs_article_id;
                        $rs_article_title       =trim($rs_article_title);
                        $rs_request_to_name     ='你';

                        $arrys_msg[$rs_time]['article_get_like'][trim('request_from_sex ')]=$rs_request_from_sex;
                        $arrys_msg[$rs_time]['article_get_like'][trim('request_to_sex   ')]=$rs_request_to_sex;
                        $arrys_msg[$rs_time]['article_get_like'][trim('request_from_name')]=$rs_request_from_name;
                        $arrys_msg[$rs_time]['article_get_like'][trim('request_to_name  ')]=$rs_request_to_name;
                        $arrys_msg[$rs_time]['article_get_like'][trim('request_from     ')]=$rs_request_from;
                        $arrys_msg[$rs_time]['article_get_like'][trim('request_to       ')]=$rs_request_to;
                        $arrys_msg[$rs_time]['article_get_like'][trim('keyin_cdate      ')]=$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['article_get_like'][trim('group_id         ')]=$rs_group_id;
                        $arrys_msg[$rs_time]['article_get_like'][trim('article_id       ')]=$rs_article_id;
                        $arrys_msg[$rs_time]['article_get_like'][trim('article_title    ')]=$rs_article_title;
                    }
                }

            //-------------------------------------------
            //發出的文章 - 得到回應
            //-------------------------------------------

                $curdate=date("Y-m-d");
                $predate=date("Y-m-d",strtotime('-1 day'));

                $sql="
                    SELECT
                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_reply`.`user_id`=`user`.`member`.`uid`
                            LIMIT 1
                        ),1) AS `request_from_sex`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_sex`,

                        IFNULL((
                            SELECT `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE 1=1
                                AND `user`.`member`.`uid`=`mssr_forum`.`mssr_forum_article`.`user_id`
                            LIMIT 1
                        ),'') AS `article_user_name`,

                        IFNULL((
                            SELECT `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE 1=1
                                AND `user`.`member`.`uid`=`mssr_forum`.`mssr_forum_reply`.`user_id`
                            LIMIT 1
                        ),'') AS `reply_user_name`,

                        `mssr_forum`.`mssr_forum_article`.`user_id` AS `article_user_id`,
                        `mssr_forum`.`mssr_forum_article`.`group_id`,
                        `mssr_forum`.`mssr_forum_article`.`article_id`,
                        `mssr_forum`.`mssr_forum_article_detail`.`article_title`,

                        `mssr_forum`.`mssr_forum_reply`.`user_id` AS `reply_user_id`,
                        `mssr_forum`.`mssr_forum_reply`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_article`
                        INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                        `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                        INNER JOIN `mssr_forum`.`mssr_forum_reply` ON
                        `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_reply`.`article_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_article`.`article_state`=1
                        AND `mssr_forum`.`mssr_forum_article`.`user_id`      ={$user_id}
                        AND `mssr_forum`.`mssr_forum_reply`.`user_id`        <>{$user_id}
                        AND `mssr_forum`.`mssr_forum_reply`.`reply_state`    =1
                        AND `mssr_forum`.`mssr_forum_reply`.`keyin_cdate` BETWEEN '{$predate} 00:00:00' AND '{$curdate} 23:59:59'
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $inx=>$db_result){
                        extract($db_result, EXTR_PREFIX_ALL, "rs");

                        $rs_request_from_sex    =(int)$rs_request_from_sex;
                        $rs_request_to_sex      =(int)$rs_request_to_sex;
                        $rs_request_from_name   =trim($rs_reply_user_name);
                        $rs_request_to_name     =trim($rs_article_user_name);
                        $rs_request_from        =(int)$rs_reply_user_id;
                        $rs_request_to          =(int)$rs_article_user_id;
                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_group_id            =(int)$rs_group_id;
                        $rs_article_id          =(int)$rs_article_id;
                        $rs_article_title       =trim($rs_article_title);
                        $rs_request_to_name     ='你';

                        $arrys_msg[$rs_time]['article_get_reply'][trim('request_from_sex ')]=$rs_request_from_sex;
                        $arrys_msg[$rs_time]['article_get_reply'][trim('request_to_sex   ')]=$rs_request_to_sex;
                        $arrys_msg[$rs_time]['article_get_reply'][trim('request_from_name')]=$rs_request_from_name;
                        $arrys_msg[$rs_time]['article_get_reply'][trim('request_to_name  ')]=$rs_request_to_name;
                        $arrys_msg[$rs_time]['article_get_reply'][trim('request_from     ')]=$rs_request_from;
                        $arrys_msg[$rs_time]['article_get_reply'][trim('request_to       ')]=$rs_request_to;
                        $arrys_msg[$rs_time]['article_get_reply'][trim('keyin_cdate      ')]=$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['article_get_reply'][trim('group_id         ')]=$rs_group_id;
                        $arrys_msg[$rs_time]['article_get_reply'][trim('article_id       ')]=$rs_article_id;
                        $arrys_msg[$rs_time]['article_get_reply'][trim('article_title    ')]=$rs_article_title;
                    }
                }

            //-------------------------------------------
            //請求推薦書籍 - 已得到回應
            //-------------------------------------------

                $sql="
                    SELECT
                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_from`=`user`.`member`.`uid`
                            LIMIT 1
                        ),1) AS `request_from_sex`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_to`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_sex`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_from`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_from_name`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_to`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_name`,

                        `mssr_forum`.`mssr_forum_user_request`.`request_from`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_to`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_id`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_state`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_read`,
                        `mssr_forum`.`mssr_forum_user_request`.`keyin_cdate`,
                        `mssr_forum`.`mssr_forum_user_request`.`keyin_mdate`,

                        `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev`.`rev_id`,
                        `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev`.`book_sid`
                    FROM `mssr_forum`.`mssr_forum_user_request`
                        INNER JOIN `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev` ON
                        `mssr_forum`.`mssr_forum_user_request`.`request_id`=`mssr_forum`.`mssr_forum_user_request_rec_us_book_rev`.`request_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_from` ={$user_id}
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_state` IN (1)
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_read` =2
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $inx=>$db_result){
                        extract($db_result, EXTR_PREFIX_ALL, "rs");

                        $rs_request_from_sex    =(int)$rs_request_from_sex;
                        $rs_request_to_sex      =(int)$rs_request_to_sex;
                        $rs_request_from_name   =trim($rs_request_from_name);
                        $rs_request_to_name     =trim($rs_request_to_name);
                        $rs_request_from        =(int)$rs_request_from;
                        $rs_request_to          =(int)$rs_request_to;
                        $rs_request_id          =(int)$rs_request_id;
                        $rs_request_state       =(int)$rs_request_state;
                        $rs_request_read        =(int)$rs_request_read;
                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_rev_id              =(int)$rs_rev_id;
                        $rs_book_sid            =trim($rs_book_sid);
                        if($user_id===$rs_request_from){
                            $rs_request_from_name='你';
                        }
                        if($user_id===$rs_request_to){
                            $rs_request_to_name='你';
                        }

                        $arrys_msg[$rs_time]['ok_request_rec_us_book_rev'][trim('request_from_sex  ')]=$rs_request_from_sex;
                        $arrys_msg[$rs_time]['ok_request_rec_us_book_rev'][trim('request_to_sex    ')]=$rs_request_to_sex;
                        $arrys_msg[$rs_time]['ok_request_rec_us_book_rev'][trim('request_from_name ')]=$rs_request_from_name;
                        $arrys_msg[$rs_time]['ok_request_rec_us_book_rev'][trim('request_to_name   ')]=$rs_request_to_name;
                        $arrys_msg[$rs_time]['ok_request_rec_us_book_rev'][trim('request_from      ')]=$rs_request_from;
                        $arrys_msg[$rs_time]['ok_request_rec_us_book_rev'][trim('request_to        ')]=$rs_request_to;
                        $arrys_msg[$rs_time]['ok_request_rec_us_book_rev'][trim('request_id        ')]=$rs_request_id;
                        $arrys_msg[$rs_time]['ok_request_rec_us_book_rev'][trim('request_state     ')]=$rs_request_state;
                        $arrys_msg[$rs_time]['ok_request_rec_us_book_rev'][trim('request_read      ')]=$rs_request_read;
                        $arrys_msg[$rs_time]['ok_request_rec_us_book_rev'][trim('keyin_cdate       ')]=$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['ok_request_rec_us_book_rev'][trim('rev_id            ')]=$rs_rev_id;
                        $arrys_msg[$rs_time]['ok_request_rec_us_book_rev'][trim('book_sid          ')]=$rs_book_sid;
                    }
                }

            //-------------------------------------------
            //請求推薦書籍
            //-------------------------------------------

                $sql="
                    SELECT
                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_from`=`user`.`member`.`uid`
                            LIMIT 1
                        ),1) AS `request_from_sex`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_to`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_sex`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_from`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_from_name`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_to`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_name`,

                        `mssr_forum`.`mssr_forum_user_request`.`request_from`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_to`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_id`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_state`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_read`,
                        `mssr_forum`.`mssr_forum_user_request`.`keyin_cdate`,
                        `mssr_forum`.`mssr_forum_user_request`.`keyin_mdate`,

                        `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev`.`rev_id`,
                        `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev`.`book_sid`
                    FROM `mssr_forum`.`mssr_forum_user_request`
                        INNER JOIN `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev` ON
                        `mssr_forum`.`mssr_forum_user_request`.`request_id`=`mssr_forum`.`mssr_forum_user_request_rec_us_book_rev`.`request_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_to`   ={$user_id}
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_state` IN (3)
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_read` =2
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $inx=>$db_result){
                        extract($db_result, EXTR_PREFIX_ALL, "rs");

                        $rs_request_from_sex    =(int)$rs_request_from_sex;
                        $rs_request_to_sex      =(int)$rs_request_to_sex;
                        $rs_request_from_name   =trim($rs_request_from_name);
                        $rs_request_to_name     =trim($rs_request_to_name);
                        $rs_request_from        =(int)$rs_request_from;
                        $rs_request_to          =(int)$rs_request_to;
                        $rs_request_id          =(int)$rs_request_id;
                        $rs_request_state       =(int)$rs_request_state;
                        $rs_request_read        =(int)$rs_request_read;
                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_rev_id              =(int)$rs_rev_id;
                        $rs_book_sid            =trim($rs_book_sid);
                        if($user_id===$rs_request_from){
                            $rs_request_from_name='你';
                        }
                        if($user_id===$rs_request_to){
                            $rs_request_to_name='你';
                        }

                        $arrys_msg[$rs_time]['request_rec_us_book_rev'][trim('request_from_sex  ')]=$rs_request_from_sex;
                        $arrys_msg[$rs_time]['request_rec_us_book_rev'][trim('request_to_sex    ')]=$rs_request_to_sex;
                        $arrys_msg[$rs_time]['request_rec_us_book_rev'][trim('request_from_name ')]=$rs_request_from_name;
                        $arrys_msg[$rs_time]['request_rec_us_book_rev'][trim('request_to_name   ')]=$rs_request_to_name;
                        $arrys_msg[$rs_time]['request_rec_us_book_rev'][trim('request_from      ')]=$rs_request_from;
                        $arrys_msg[$rs_time]['request_rec_us_book_rev'][trim('request_to        ')]=$rs_request_to;
                        $arrys_msg[$rs_time]['request_rec_us_book_rev'][trim('request_id        ')]=$rs_request_id;
                        $arrys_msg[$rs_time]['request_rec_us_book_rev'][trim('request_state     ')]=$rs_request_state;
                        $arrys_msg[$rs_time]['request_rec_us_book_rev'][trim('request_read      ')]=$rs_request_read;
                        $arrys_msg[$rs_time]['request_rec_us_book_rev'][trim('keyin_cdate       ')]=$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['request_rec_us_book_rev'][trim('rev_id            ')]=$rs_rev_id;
                        $arrys_msg[$rs_time]['request_rec_us_book_rev'][trim('book_sid          ')]=$rs_book_sid;
                    }
                }

            //-------------------------------------------
            //請求推薦文章
            //-------------------------------------------

                $sql="
                    SELECT
                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_from`=`user`.`member`.`uid`
                            LIMIT 1
                        ),1) AS `request_from_sex`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_to`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_sex`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_from`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_from_name`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_to`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_name`,

                        `mssr_forum`.`mssr_forum_user_request`.`request_from`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_to`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_id`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_state`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_read`,
                        `mssr_forum`.`mssr_forum_user_request`.`keyin_cdate`,
                        `mssr_forum`.`mssr_forum_user_request`.`keyin_mdate`,

                        `mssr_forum`.`mssr_forum_user_request_article_rev`.`rev_id`,
                        `mssr_forum`.`mssr_forum_user_request_article_rev`.`group_id`,
                        `mssr_forum`.`mssr_forum_user_request_article_rev`.`article_id`,

                        `mssr_forum`.`mssr_forum_article_detail`.`article_title`
                    FROM `mssr_forum`.`mssr_forum_user_request`
                        INNER JOIN `mssr_forum`.`mssr_forum_user_request_article_rev` ON
                        `mssr_forum`.`mssr_forum_user_request`.`request_id`=`mssr_forum`.`mssr_forum_user_request_article_rev`.`request_id`

                        INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                        `mssr_forum`.`mssr_forum_user_request_article_rev`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_to`   ={$user_id}
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_state` IN (3)
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_read` =2
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $inx=>$db_result){
                        extract($db_result, EXTR_PREFIX_ALL, "rs");

                        $rs_request_from_sex    =(int)$rs_request_from_sex;
                        $rs_request_to_sex      =(int)$rs_request_to_sex;
                        $rs_request_from_name   =trim($rs_request_from_name);
                        $rs_request_to_name     =trim($rs_request_to_name);
                        $rs_request_from        =(int)$rs_request_from;
                        $rs_request_to          =(int)$rs_request_to;
                        $rs_request_id          =(int)$rs_request_id;
                        $rs_request_state       =(int)$rs_request_state;
                        $rs_request_read        =(int)$rs_request_read;
                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_rev_id              =(int)$rs_rev_id;
                        $rs_group_id            =(int)$rs_group_id;
                        $rs_article_id          =(int)$rs_article_id;
                        $rs_article_title       =trim($rs_article_title);
                        if($user_id===$rs_request_from){
                            $rs_request_from_name='你';
                        }
                        if($user_id===$rs_request_to){
                            $rs_request_to_name='你';
                        }

                        $arrys_msg[$rs_time]['request_article_rev'][trim('request_from_sex  ')]=$rs_request_from_sex;
                        $arrys_msg[$rs_time]['request_article_rev'][trim('request_to_sex    ')]=$rs_request_to_sex;
                        $arrys_msg[$rs_time]['request_article_rev'][trim('request_from_name ')]=$rs_request_from_name;
                        $arrys_msg[$rs_time]['request_article_rev'][trim('request_to_name   ')]=$rs_request_to_name;
                        $arrys_msg[$rs_time]['request_article_rev'][trim('request_from      ')]=$rs_request_from;
                        $arrys_msg[$rs_time]['request_article_rev'][trim('request_to        ')]=$rs_request_to;
                        $arrys_msg[$rs_time]['request_article_rev'][trim('request_id        ')]=$rs_request_id;
                        $arrys_msg[$rs_time]['request_article_rev'][trim('request_state     ')]=$rs_request_state;
                        $arrys_msg[$rs_time]['request_article_rev'][trim('request_read      ')]=$rs_request_read;
                        $arrys_msg[$rs_time]['request_article_rev'][trim('keyin_cdate       ')]=$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['request_article_rev'][trim('rev_id            ')]=$rs_rev_id;
                        $arrys_msg[$rs_time]['request_article_rev'][trim('group_id          ')]=$rs_group_id;
                        $arrys_msg[$rs_time]['request_article_rev'][trim('article_id        ')]=$rs_article_id;
                        $arrys_msg[$rs_time]['request_article_rev'][trim('article_title     ')]=$rs_article_title;
                    }
                }

            //-------------------------------------------
            //請求聯署建立小組
            //-------------------------------------------

                $sql="
                    SELECT
                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_from`=`user`.`member`.`uid`
                            LIMIT 1
                        ),1) AS `request_from_sex`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_to`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_sex`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_from`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_from_name`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_to`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_name`,

                        IFNULL((
                            SELECT
                                `mssr_forum`.`mssr_forum_user_request_create_group_rev`.`rev_id`
                            FROM `mssr_forum`.`mssr_forum_user_request_create_group_rev`
                            WHERE `mssr_forum`.`mssr_forum_user_request_create_group_rev`.`request_id`=`mssr_forum`.`mssr_forum_user_request`.`request_id`
                            LIMIT 1
                        ),'') AS `rev_id`,

                        IFNULL((
                            SELECT
                                `mssr_forum`.`mssr_forum_group`.`group_name`
                            FROM `mssr_forum`.`mssr_forum_group`
                            WHERE `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_user_request_create_group_rev`.`group_id`
                            LIMIT 1
                        ),'') AS `group_name`,

                        `mssr_forum`.`mssr_forum_user_request`.`request_from`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_to`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_id`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_state`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_read`,
                        `mssr_forum`.`mssr_forum_user_request`.`keyin_cdate`,
                        `mssr_forum`.`mssr_forum_user_request`.`keyin_mdate`,

                        `mssr_forum`.`mssr_forum_user_request_create_group_rev`.`group_id`
                    FROM `mssr_forum`.`mssr_forum_user_request`
                        INNER JOIN `mssr_forum`.`mssr_forum_user_request_create_group_rev` ON
                        `mssr_forum`.`mssr_forum_user_request`.`request_id`=`mssr_forum`.`mssr_forum_user_request_create_group_rev`.`request_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_to`   ={$user_id}
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_state` IN (3)
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_read` =2
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $inx=>$db_result){
                        extract($db_result, EXTR_PREFIX_ALL, "rs");

                        $rs_request_from_sex    =(int)$rs_request_from_sex;
                        $rs_request_to_sex      =(int)$rs_request_to_sex;
                        $rs_request_from_name   =trim($rs_request_from_name);
                        $rs_request_to_name     =trim($rs_request_to_name);
                        $rs_request_from        =(int)$rs_request_from;
                        $rs_request_to          =(int)$rs_request_to;
                        $rs_request_id          =(int)$rs_request_id;
                        $rs_request_state       =(int)$rs_request_state;
                        $rs_request_read        =(int)$rs_request_read;
                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_rev_id              =(int)$rs_rev_id;
                        $rs_group_id            =(int)$rs_group_id;
                        $rs_group_name          =trim($rs_group_name);
                        if($user_id===$rs_request_from){
                            $rs_request_from_name='你';
                        }
                        if($user_id===$rs_request_to){
                            $rs_request_to_name='你';
                        }

                        $arrys_msg[$rs_time]['request_create_group_rev'][trim('request_from_sex  ')]=$rs_request_from_sex;
                        $arrys_msg[$rs_time]['request_create_group_rev'][trim('request_to_sex    ')]=$rs_request_to_sex;
                        $arrys_msg[$rs_time]['request_create_group_rev'][trim('request_from_name ')]=$rs_request_from_name;
                        $arrys_msg[$rs_time]['request_create_group_rev'][trim('request_to_name   ')]=$rs_request_to_name;
                        $arrys_msg[$rs_time]['request_create_group_rev'][trim('request_from      ')]=$rs_request_from;
                        $arrys_msg[$rs_time]['request_create_group_rev'][trim('request_to        ')]=$rs_request_to;
                        $arrys_msg[$rs_time]['request_create_group_rev'][trim('request_id        ')]=$rs_request_id;
                        $arrys_msg[$rs_time]['request_create_group_rev'][trim('request_state     ')]=$rs_request_state;
                        $arrys_msg[$rs_time]['request_create_group_rev'][trim('request_read      ')]=$rs_request_read;
                        $arrys_msg[$rs_time]['request_create_group_rev'][trim('keyin_cdate       ')]=$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['request_create_group_rev'][trim('rev_id            ')]=$rs_rev_id;
                        $arrys_msg[$rs_time]['request_create_group_rev'][trim('group_id          ')]=$rs_group_id;
                        $arrys_msg[$rs_time]['request_create_group_rev'][trim('group_name        ')]=$rs_group_name;
                    }
                }

            //-------------------------------------------
            //申請加入小組
            //-------------------------------------------

                $sql="
                    SELECT
                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_from`=`user`.`member`.`uid`
                            LIMIT 1
                        ),1) AS `request_from_sex`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_to`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_sex`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_from`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_from_name`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_to`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_name`,

                        `mssr_forum`.`mssr_forum_user_request`.`request_from`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_to`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_id`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_state`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_read`,
                        `mssr_forum`.`mssr_forum_user_request`.`keyin_cdate`,
                        `mssr_forum`.`mssr_forum_user_request`.`keyin_mdate`,

                        `mssr_forum`.`mssr_forum_user_request_join_to_group_rev`.`rev_id`,
                        `mssr_forum`.`mssr_forum_user_request_join_to_group_rev`.`group_id`,

                        `mssr_forum`.`mssr_forum_group`.`group_name`
                    FROM `mssr_forum`.`mssr_forum_user_request`
                        INNER JOIN `mssr_forum`.`mssr_forum_user_request_join_to_group_rev` ON
                        `mssr_forum`.`mssr_forum_user_request`.`request_id`=`mssr_forum`.`mssr_forum_user_request_join_to_group_rev`.`request_id`

                        INNER JOIN `mssr_forum`.`mssr_forum_group` ON
                        `mssr_forum`.`mssr_forum_user_request_join_to_group_rev`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_to`   ={$user_id}
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_state` IN (3)
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_read` =2
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $inx=>$db_result){
                        extract($db_result, EXTR_PREFIX_ALL, "rs");

                        $rs_request_from_sex    =(int)$rs_request_from_sex;
                        $rs_request_to_sex      =(int)$rs_request_to_sex;
                        $rs_request_from_name   =trim($rs_request_from_name);
                        $rs_request_to_name     =trim($rs_request_to_name);
                        $rs_request_from        =(int)$rs_request_from;
                        $rs_request_to          =(int)$rs_request_to;
                        $rs_request_id          =(int)$rs_request_id;
                        $rs_request_state       =(int)$rs_request_state;
                        $rs_request_read        =(int)$rs_request_read;
                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_rev_id              =(int)$rs_rev_id;
                        $rs_group_id            =(int)$rs_group_id;
                        $rs_group_name          =trim($rs_group_name);
                        if($user_id===$rs_request_from){
                            $rs_request_from_name='你';
                        }
                        if($user_id===$rs_request_to){
                            $rs_request_to_name='你';
                        }

                        $arrys_msg[$rs_time]['request_join_to_group_rev'][trim('request_from_sex  ')]=$rs_request_from_sex;
                        $arrys_msg[$rs_time]['request_join_to_group_rev'][trim('request_to_sex    ')]=$rs_request_to_sex;
                        $arrys_msg[$rs_time]['request_join_to_group_rev'][trim('request_from_name ')]=$rs_request_from_name;
                        $arrys_msg[$rs_time]['request_join_to_group_rev'][trim('request_to_name   ')]=$rs_request_to_name;
                        $arrys_msg[$rs_time]['request_join_to_group_rev'][trim('request_from      ')]=$rs_request_from;
                        $arrys_msg[$rs_time]['request_join_to_group_rev'][trim('request_to        ')]=$rs_request_to;
                        $arrys_msg[$rs_time]['request_join_to_group_rev'][trim('request_id        ')]=$rs_request_id;
                        $arrys_msg[$rs_time]['request_join_to_group_rev'][trim('request_state     ')]=$rs_request_state;
                        $arrys_msg[$rs_time]['request_join_to_group_rev'][trim('request_read      ')]=$rs_request_read;
                        $arrys_msg[$rs_time]['request_join_to_group_rev'][trim('keyin_cdate       ')]=$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['request_join_to_group_rev'][trim('rev_id            ')]=$rs_rev_id;
                        $arrys_msg[$rs_time]['request_join_to_group_rev'][trim('group_id          ')]=$rs_group_id;
                        $arrys_msg[$rs_time]['request_join_to_group_rev'][trim('group_name        ')]=$rs_group_name;
                    }
                }

            //-------------------------------------------
            //邀請加入小組
            //-------------------------------------------

                $sql="
                    SELECT
                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_from`=`user`.`member`.`uid`
                            LIMIT 1
                        ),1) AS `request_from_sex`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_to`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_sex`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_from`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_from_name`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_user_request`.`request_to`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_name`,

                        `mssr_forum`.`mssr_forum_user_request`.`request_from`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_to`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_id`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_state`,
                        `mssr_forum`.`mssr_forum_user_request`.`request_read`,
                        `mssr_forum`.`mssr_forum_user_request`.`keyin_cdate`,
                        `mssr_forum`.`mssr_forum_user_request`.`keyin_mdate`,

                        `mssr_forum`.`mssr_forum_user_request_join_us_group_rev`.`rev_id`,
                        `mssr_forum`.`mssr_forum_user_request_join_us_group_rev`.`group_id`,

                        `mssr_forum`.`mssr_forum_group`.`group_name`
                    FROM `mssr_forum`.`mssr_forum_user_request`
                        INNER JOIN `mssr_forum`.`mssr_forum_user_request_join_us_group_rev` ON
                        `mssr_forum`.`mssr_forum_user_request`.`request_id`=`mssr_forum`.`mssr_forum_user_request_join_us_group_rev`.`request_id`

                        INNER JOIN `mssr_forum`.`mssr_forum_group` ON
                        `mssr_forum`.`mssr_forum_user_request_join_us_group_rev`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_to`   ={$user_id}
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_state` IN (3)
                        AND `mssr_forum`.`mssr_forum_user_request`.`request_read` =2
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $inx=>$db_result){
                        extract($db_result, EXTR_PREFIX_ALL, "rs");

                        $rs_request_from_sex    =(int)$rs_request_from_sex;
                        $rs_request_to_sex      =(int)$rs_request_to_sex;
                        $rs_request_from_name   =trim($rs_request_from_name);
                        $rs_request_to_name     =trim($rs_request_to_name);
                        $rs_request_from        =(int)$rs_request_from;
                        $rs_request_to          =(int)$rs_request_to;
                        $rs_request_id          =(int)$rs_request_id;
                        $rs_request_state       =(int)$rs_request_state;
                        $rs_request_read        =(int)$rs_request_read;
                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_rev_id              =(int)$rs_rev_id;
                        $rs_group_id            =(int)$rs_group_id;
                        $rs_group_name          =trim($rs_group_name);
                        if($user_id===$rs_request_from){
                            $rs_request_from_name='你';
                        }
                        if($user_id===$rs_request_to){
                            $rs_request_to_name='你';
                        }

                        $arrys_msg[$rs_time]['request_join_us_group_rev'][trim('request_from_sex  ')]=$rs_request_from_sex;
                        $arrys_msg[$rs_time]['request_join_us_group_rev'][trim('request_to_sex    ')]=$rs_request_to_sex;
                        $arrys_msg[$rs_time]['request_join_us_group_rev'][trim('request_from_name ')]=$rs_request_from_name;
                        $arrys_msg[$rs_time]['request_join_us_group_rev'][trim('request_to_name   ')]=$rs_request_to_name;
                        $arrys_msg[$rs_time]['request_join_us_group_rev'][trim('request_from      ')]=$rs_request_from;
                        $arrys_msg[$rs_time]['request_join_us_group_rev'][trim('request_to        ')]=$rs_request_to;
                        $arrys_msg[$rs_time]['request_join_us_group_rev'][trim('request_id        ')]=$rs_request_id;
                        $arrys_msg[$rs_time]['request_join_us_group_rev'][trim('request_state     ')]=$rs_request_state;
                        $arrys_msg[$rs_time]['request_join_us_group_rev'][trim('request_read      ')]=$rs_request_read;
                        $arrys_msg[$rs_time]['request_join_us_group_rev'][trim('keyin_cdate       ')]=$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['request_join_us_group_rev'][trim('rev_id            ')]=$rs_rev_id;
                        $arrys_msg[$rs_time]['request_join_us_group_rev'][trim('group_id          ')]=$rs_group_id;
                        $arrys_msg[$rs_time]['request_join_us_group_rev'][trim('group_name        ')]=$rs_group_name;
                    }
                }

            //-------------------------------------------
            //交友訊息
            //-------------------------------------------

                $sql="
                    #SELECT
                    #    IFNULL((
                    #        SELECT
                    #            `user`.`member`.`sex`
                    #        FROM `user`.`member`
                    #        WHERE `mssr_forum`.`mssr_forum_friend`.`user_id`=`user`.`member`.`uid`
                    #        LIMIT 1
                    #    ),1) AS `user_sex`,
                    #
                    #    IFNULL((
                    #        SELECT
                    #            `user`.`member`.`sex`
                    #        FROM `user`.`member`
                    #        WHERE `mssr_forum`.`mssr_forum_friend`.`friend_id`=`user`.`member`.`uid`
                    #        LIMIT 1
                    #    ),'') AS `friend_sex`,
                    #
                    #    IFNULL((
                    #        SELECT
                    #            `user`.`member`.`name`
                    #        FROM `user`.`member`
                    #        WHERE `mssr_forum`.`mssr_forum_friend`.`user_id`=`user`.`member`.`uid`
                    #        LIMIT 1
                    #    ),'') AS `user_name`,
                    #
                    #    IFNULL((
                    #        SELECT
                    #            `user`.`member`.`name`
                    #        FROM `user`.`member`
                    #        WHERE `mssr_forum`.`mssr_forum_friend`.`friend_id`=`user`.`member`.`uid`
                    #        LIMIT 1
                    #    ),'') AS `friend_name`,
                    #
                    #    `mssr_forum`.`mssr_forum_friend`.`create_by`,
                    #    `mssr_forum`.`mssr_forum_friend`.`user_id`,
                    #    `mssr_forum`.`mssr_forum_friend`.`friend_id`,
                    #    `mssr_forum`.`mssr_forum_friend`.`friend_state`,
                    #    `mssr_forum`.`mssr_forum_friend`.`keyin_mdate`
                    #FROM `mssr_forum`.`mssr_forum_friend`
                    #WHERE 1=1
                    #    AND (
                    #        `mssr_forum`.`mssr_forum_friend`.`user_id`  ={$user_id}
                    #            OR
                    #        `mssr_forum`.`mssr_forum_friend`.`friend_id`={$user_id}
                    #    )
                    #    AND `mssr_forum`.`mssr_forum_friend`.`friend_state` IN (1,2)
                    #    AND DATE(`mssr_forum`.`mssr_forum_friend`.`keyin_mdate`) >= CURDATE() - INTERVAL 1 DAY
                    #--------------- UNION ---------------#
                    #                 UNION
                    #--------------- UNION ---------------#
                    SELECT
                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_friend`.`user_id`=`user`.`member`.`uid`
                            LIMIT 1
                        ),1) AS `user_sex`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`sex`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_friend`.`friend_id`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `friend_sex`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_friend`.`user_id`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `user_name`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr_forum`.`mssr_forum_friend`.`friend_id`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `friend_name`,

                        `mssr_forum`.`mssr_forum_friend`.`create_by`,
                        `mssr_forum`.`mssr_forum_friend`.`user_id`,
                        `mssr_forum`.`mssr_forum_friend`.`friend_id`,
                        `mssr_forum`.`mssr_forum_friend`.`content`,
                        `mssr_forum`.`mssr_forum_friend`.`friend_state`,
                        `mssr_forum`.`mssr_forum_friend`.`keyin_mdate`
                    FROM `mssr_forum`.`mssr_forum_friend`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_friend`.`user_id`  <>{$user_id}
                        AND `mssr_forum`.`mssr_forum_friend`.`friend_id`= {$user_id}
                        AND `mssr_forum`.`mssr_forum_friend`.`friend_state` IN (3)
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $inx=>$db_result){
                        extract($db_result, EXTR_PREFIX_ALL, "rs");

                        $rs_user_sex        =(int)$rs_user_sex;
                        $rs_friend_sex      =(int)$rs_friend_sex;
                        $rs_user_name       =trim($rs_user_name);
                        $rs_friend_name     =trim($rs_friend_name);
                        $rs_create_by       =(int)$rs_create_by;
                        $rs_user_id         =(int)$rs_user_id;
                        $rs_friend_id       =(int)$rs_friend_id;
                        $rs_content         =trim($rs_content);
                        $rs_friend_state    =(int)$rs_friend_state;
                        $rs_keyin_mdate     =trim($rs_keyin_mdate);
                        $rs_time            =trim(strtotime($rs_keyin_mdate));
                        if($user_id===$rs_user_id){
                            $rs_user_name='你';
                        }
                        if($user_id===$rs_friend_id){
                            $rs_friend_name='你';
                        }

                        $arrys_msg[$rs_time]['request_friend'][trim('user_sex       ')] =$rs_user_sex;
                        $arrys_msg[$rs_time]['request_friend'][trim('friend_sex     ')] =$rs_friend_sex;
                        $arrys_msg[$rs_time]['request_friend'][trim('user_name      ')] =$rs_user_name;
                        $arrys_msg[$rs_time]['request_friend'][trim('friend_name    ')] =$rs_friend_name;
                        $arrys_msg[$rs_time]['request_friend'][trim('create_by      ')] =$rs_create_by;
                        $arrys_msg[$rs_time]['request_friend'][trim('user_id        ')] =$rs_user_id;
                        $arrys_msg[$rs_time]['request_friend'][trim('friend_id      ')] =$rs_friend_id;
                        $arrys_msg[$rs_time]['request_friend'][trim('content        ')] =$rs_content;
                        $arrys_msg[$rs_time]['request_friend'][trim('friend_state   ')] =$rs_friend_state;
                        $arrys_msg[$rs_time]['request_friend'][trim('keyin_mdate    ')] =$rs_keyin_mdate;
                    }
                }

        //-----------------------------------------------
        //整理回傳
        //-----------------------------------------------

            //訊息整理
            krsort($arrys_msg);

            //回傳
            return $arrys_msg;
    }
?>