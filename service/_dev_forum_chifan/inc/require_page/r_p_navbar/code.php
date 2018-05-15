<?php
//-------------------------------------------------------
//函式: r_p_navbar()
//用途: 外掛網站導覽列
//-------------------------------------------------------

    //---------------------------------------------------
    //測試
    //---------------------------------------------------

        ////外掛設定檔
        //require_once(str_repeat("../",5).'config/config.php');
        //require_once(str_repeat("../",5).'inc/code.php');
        //$conn_user=conn($db_type='mysql',$arry_conn_user);
        //$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
        //
        //r_p_navbar($user_id=5030,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);

    function r_p_navbar($user_id,$conn_user=NULL,$conn_mssr=NULL,$arry_conn_user,$arry_conn_mssr){
    //---------------------------------------------------
    //函式: r_p_navbar()
    //用途: 外掛網站導覽列
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

            //-------------------------------------------
            //申請小組
            //-------------------------------------------

                $arrys_join_my_forum=array();

                $sql="
                    SELECT
                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr`.`mssr_user_forum`.`user_id`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_from_name`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE {$user_id}=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_name`,

                        `mssr`.`mssr_user_forum`.`user_id` AS `request_from`,
                        {$user_id} AS `request_to`,
                        0 AS `request_id`,
                        1 AS `request_state`,
                        `mssr`.`mssr_user_forum`.`keyin_cdate`,
                        `mssr`.`mssr_user_forum`.`keyin_mdate`,
                        0 AS `rev_id`,
                        `mssr`.`mssr_forum`.`forum_id`,
                        `mssr`.`mssr_forum`.`forum_name`

                    FROM `mssr`.`mssr_user_forum`
                        INNER JOIN `mssr_forum` ON
                        `mssr`.`mssr_user_forum`.`forum_id`=`mssr`.`mssr_forum`.`forum_id`
                    WHERE 1=1
                        AND `mssr`.`mssr_forum`.`create_by`={$user_id}
                        AND `mssr`.`mssr_user_forum`.`user_id`<>{$user_id}
                        AND `mssr`.`mssr_user_forum`.`user_state`='申請中'
                ";
                $arrys_join_my_forum=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($arrys_join_my_forum)){
                    foreach($arrys_join_my_forum as $inx=>$arry_join_my_forum){
                        extract($arry_join_my_forum, EXTR_PREFIX_ALL, "rs");

                        $rs_request_from_name   =trim($rs_request_from_name);
                        $rs_request_to_name     =trim($rs_request_to_name);
                        $rs_request_state       =trim($rs_request_state);
                        $rs_request_from        =(int)$rs_request_from;
                        $rs_request_to          =(int)$rs_request_to;
                        $rs_request_id          =(int)$rs_request_id;
                        $rs_forum_id            =(int)$rs_forum_id;
                        $rs_forum_name          =trim($rs_forum_name);
                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_forum_name          =trim($rs_forum_name);
                        $rs_content             ='';
                        $rs_img                 ='image/boy.jpg';

                        if($user_id===$rs_request_from){
                            $rs_request_from_name='你';
                        }
                        if($user_id===$rs_request_to){
                            $rs_request_to_name='你';
                        }

                        $rs_content="
                            {$rs_request_from_name}向{$rs_request_to_name}提出申請，
                            希望能加入你的小組：【{$rs_forum_name}】。
                            &nbsp;&nbsp;&nbsp;
                            <a href='mssr_forum_group_member_check_A.php?forum_id={$rs_forum_id}&user_id={$rs_request_from}&action_type=permit'
                            ><u>允許</u></a>
                            &nbsp;&nbsp;
                            <a href='mssr_forum_group_member_check_A.php?forum_id={$rs_forum_id}&user_id={$rs_request_from}&action_type=reject'
                            ><u>不允許</u></a>
                        ";

                        $arrys_msg[$rs_time]['request_join_my_forum'][trim('request_from_name  ')] =$rs_request_from_name;
                        $arrys_msg[$rs_time]['request_join_my_forum'][trim('request_to_name    ')] =$rs_request_to_name;
                        $arrys_msg[$rs_time]['request_join_my_forum'][trim('request_state      ')] =$rs_request_state;
                        $arrys_msg[$rs_time]['request_join_my_forum'][trim('request_from       ')] =$rs_request_from;
                        $arrys_msg[$rs_time]['request_join_my_forum'][trim('request_to         ')] =$rs_request_to;
                        $arrys_msg[$rs_time]['request_join_my_forum'][trim('request_id         ')] =$rs_request_id;
                        $arrys_msg[$rs_time]['request_join_my_forum'][trim('keyin_cdate        ')] =$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['request_join_my_forum'][trim('content            ')] =$rs_content;
                        $arrys_msg[$rs_time]['request_join_my_forum'][trim('img                ')] =$rs_img;
                    }
                }

            //-------------------------------------------
            //交友訊息
            //-------------------------------------------

                $arrys_friend=array();

                $sql="
                    SELECT
                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr`.`mssr_forum_friend`.`user_id`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `user_name`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr`.`mssr_forum_friend`.`friend_id`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `friend_name`,

                        `mssr`.`mssr_forum_friend`.`user_id`,
                        `mssr`.`mssr_forum_friend`.`friend_id`,
                        `mssr`.`mssr_forum_friend`.`friend_state`,
                        `mssr`.`mssr_forum_friend`.`keyin_cdate`
                    FROM `mssr`.`mssr_forum_friend`
                    WHERE 1=1
                        AND (
                            `mssr`.`mssr_forum_friend`.`user_id`  ={$user_id}
                                OR
                            `mssr`.`mssr_forum_friend`.`friend_id`={$user_id}
                        )
                        AND `mssr`.`mssr_forum_friend`.`friend_state` IN ('成功','失敗')
                        AND DATE(`mssr`.`mssr_forum_friend`.`keyin_cdate`) >= CURDATE() - INTERVAL 1 DAY
                    #--------------- UNION ---------------#
                                     UNION
                    #--------------- UNION ---------------#
                    SELECT
                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr`.`mssr_forum_friend`.`user_id`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `user_name`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr`.`mssr_forum_friend`.`friend_id`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `friend_name`,

                        `mssr`.`mssr_forum_friend`.`user_id`,
                        `mssr`.`mssr_forum_friend`.`friend_id`,
                        `mssr`.`mssr_forum_friend`.`friend_state`,
                        `mssr`.`mssr_forum_friend`.`keyin_cdate`
                    FROM `mssr`.`mssr_forum_friend`
                    WHERE 1=1
                        AND `mssr`.`mssr_forum_friend`.`friend_id`={$user_id}
                        AND `mssr`.`mssr_forum_friend`.`friend_state` IN ('確認中')
                ";
                $arrys_friend=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($arrys_friend)){
                    foreach($arrys_friend as $inx=>$arry_friend){
                        extract($arry_friend, EXTR_PREFIX_ALL, "rs");

                        $rs_user_name       =trim($rs_user_name);
                        $rs_friend_name     =trim($rs_friend_name);
                        $rs_user_id         =(int)$rs_user_id;
                        $rs_friend_id       =(int)$rs_friend_id;
                        $rs_friend_state    =trim($rs_friend_state);
                        $rs_keyin_cdate     =trim($rs_keyin_cdate);
                        $rs_time            =trim(strtotime($rs_keyin_cdate));
                        $rs_content         ='';
                        $rs_img             ='image/boy.jpg';

                        if($user_id===$rs_user_id){
                            $rs_user_name='你';
                        }
                        if($user_id===$rs_friend_id){
                            $rs_friend_name='你';
                        }

                        if(in_array($rs_friend_state,array('成功','失敗'))){
                            $rs_content="
                                <!-- 【通知】交友申請 - {$rs_friend_state} -->
                                【{$rs_user_name}】
                                提出與
                                【{$rs_friend_name}】
                                的
                                交友申請結果為 : {$rs_friend_state}
                            ";
                        }else{
                            $rs_content="
                                <!-- 【通知】交友申請 - {$rs_friend_state} -->
                                【{$rs_user_name}】
                                已經提出要與
                                【{$rs_friend_name}】
                                成為朋友。
                                請問你是否要跟他成為朋友?&nbsp;&nbsp;&nbsp;
                                <a href='mssr_forum_people_friend_request_Act.php?user_id={$rs_user_id}&sess_uid={$rs_friend_id}&fri_check=確認'><u>是</u></a>
                                &nbsp;&nbsp;
                                <a href='mssr_forum_people_friend_request_Act.php?user_id={$rs_user_id}&sess_uid={$rs_friend_id}&fri_check=失敗'><u>否</u></a>
                            ";
                        }

                        $arrys_msg[$rs_time]['friend_msg'][trim('user_name      ')] =$rs_user_name;
                        $arrys_msg[$rs_time]['friend_msg'][trim('friend_name    ')] =$rs_friend_name;
                        $arrys_msg[$rs_time]['friend_msg'][trim('user_id        ')] =$rs_user_id;
                        $arrys_msg[$rs_time]['friend_msg'][trim('friend_id      ')] =$rs_friend_id;
                        $arrys_msg[$rs_time]['friend_msg'][trim('friend_state   ')] =$rs_friend_state;
                        $arrys_msg[$rs_time]['friend_msg'][trim('keyin_cdate    ')] =$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['friend_msg'][trim('content        ')] =$rs_content;
                        $arrys_msg[$rs_time]['friend_msg'][trim('img            ')] =$rs_img;
                    }
                }

            //-------------------------------------------
            //請求推薦書籍
            //-------------------------------------------

                $arrys_book=array();

                $sql="
                    SELECT
                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr`.`mssr_user_request`.`request_from`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_from_name`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr`.`mssr_user_request`.`request_to`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_name`,

                        `mssr`.`mssr_user_request`.`request_from`,
                        `mssr`.`mssr_user_request`.`request_to`,
                        `mssr`.`mssr_user_request`.`request_id`,
                        `mssr`.`mssr_user_request`.`request_state`,
                        `mssr`.`mssr_user_request`.`keyin_cdate`,
                        `mssr`.`mssr_user_request`.`keyin_mdate`,

                        `mssr`.`mssr_user_request_book_rev`.`rev_id`,
                        `mssr`.`mssr_user_request_book_rev`.`request_content`
                    FROM `mssr`.`mssr_user_request`
                        INNER JOIN `mssr_user_request_book_rev` ON
                        `mssr`.`mssr_user_request`.`request_id`=`mssr`.`mssr_user_request_book_rev`.`request_id`
                    WHERE 1=1
                        AND (
                            `mssr`.`mssr_user_request`.`request_from` ={$user_id}
                            OR
                            `mssr`.`mssr_user_request`.`request_to`   ={$user_id}
                        )
                        AND `mssr`.`mssr_user_request`.`request_state`=1
                ";
                $arrys_book=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($arrys_book)){
                    foreach($arrys_book as $inx=>$arry_book){
                        extract($arry_book, EXTR_PREFIX_ALL, "rs");

                        $rs_request_from_name   =trim($rs_request_from_name);
                        $rs_request_to_name     =trim($rs_request_to_name);
                        $rs_request_state       =trim($rs_request_state);
                        $rs_request_content     =trim($rs_request_content);
                        $rs_request_from        =(int)$rs_request_from;
                        $rs_request_to          =(int)$rs_request_to;
                        $rs_request_id          =(int)$rs_request_id;
                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_content             ='';
                        $rs_img                 ='image/book.jpg';

                        if($user_id===$rs_request_from){
                            $rs_request_from_name='你';
                        }
                        if($user_id===$rs_request_to){
                            $rs_request_to_name='你';
                        }

                        $rs_content="
                            <a href='mssr_forum_people_shelf.php?user_id={$rs_request_from}'>{$rs_request_from_name}</a>向
                            <a href='mssr_forum_people_shelf.php?user_id={$rs_request_to}'>{$rs_request_to_name}</a>提出邀請，
                            希望{$rs_request_to_name}能推薦一本書給{$rs_request_from_name}。
                        ";

                        $arrys_msg[$rs_time]['request_book'][trim('request_from_name')] =$rs_request_from_name;
                        $arrys_msg[$rs_time]['request_book'][trim('request_to_name  ')] =$rs_request_to_name;
                        $arrys_msg[$rs_time]['request_book'][trim('request_state    ')] =$rs_request_state;
                        $arrys_msg[$rs_time]['request_book'][trim('request_from     ')] =$rs_request_from;
                        $arrys_msg[$rs_time]['request_book'][trim('request_to       ')] =$rs_request_to;
                        $arrys_msg[$rs_time]['request_book'][trim('request_id       ')] =$rs_request_id;
                        $arrys_msg[$rs_time]['request_book'][trim('keyin_cdate      ')] =$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['request_book'][trim('content          ')] =$rs_content;
                        $arrys_msg[$rs_time]['request_book'][trim('img              ')] =$rs_img;
                    }
                }

            //-------------------------------------------
            //請求推薦文章
            //-------------------------------------------

                $arrys_article=array();

                $sql="
                    SELECT
                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr`.`mssr_user_request`.`request_from`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_from_name`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr`.`mssr_user_request`.`request_to`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_name`,

                        `mssr`.`mssr_user_request`.`request_from`,
                        `mssr`.`mssr_user_request`.`request_to`,
                        `mssr`.`mssr_user_request`.`request_id`,
                        `mssr`.`mssr_user_request`.`request_state`,
                        `mssr`.`mssr_user_request`.`keyin_cdate`,
                        `mssr`.`mssr_user_request`.`keyin_mdate`,

                        `mssr`.`mssr_user_request_discussion_rev`.`rev_id`,
                        `mssr`.`mssr_user_request_discussion_rev`.`article_id`,

                        `mssr_article_book_rev`.`article_id` AS `book_rev_article_id`,

                        `mssr_article_forum_rev`.`article_id` AS `forum_rev_article_id`,

                        `mssr`.`mssr_forum_article`.`article_title`
                    FROM `mssr`.`mssr_user_request`
                        INNER JOIN `mssr`.`mssr_user_request_discussion_rev` ON
                        `mssr`.`mssr_user_request`.`request_id`=`mssr`.`mssr_user_request_discussion_rev`.`request_id`

                        INNER JOIN `mssr`.`mssr_forum_article` ON
                        `mssr`.`mssr_user_request_discussion_rev`.`article_id`=`mssr`.`mssr_forum_article`.`article_id`

                        LEFT JOIN `mssr_article_book_rev` ON
                        `mssr_user_request_discussion_rev`.`article_id`=`mssr_article_book_rev`.`article_id`

                        LEFT JOIN `mssr_article_forum_rev` ON
                        `mssr_user_request_discussion_rev`.`article_id`=`mssr_article_forum_rev`.`article_id`
                    WHERE 1=1
                        AND (
                            `mssr`.`mssr_user_request`.`request_from` ={$user_id}
                            OR
                            `mssr`.`mssr_user_request`.`request_to`   ={$user_id}
                        )
                        AND `mssr`.`mssr_user_request`.`request_state`=1
                ";
                $arrys_article=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($arrys_article)){
                    foreach($arrys_article as $inx=>$arry_article){
                        extract($arry_article, EXTR_PREFIX_ALL, "rs");

                        $rs_request_from_name   =trim($rs_request_from_name);
                        $rs_request_to_name     =trim($rs_request_to_name);
                        $rs_request_state       =trim($rs_request_state);
                        $rs_request_from        =(int)$rs_request_from;
                        $rs_request_to          =(int)$rs_request_to;
                        $rs_request_id          =(int)$rs_request_id;
                        $rs_article_id          =(int)$rs_article_id;
                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_article_title       =trim($rs_article_title);
                        $rs_content             ='';
                        $rs_book_rev_article_id =(int)$rs_book_rev_article_id;
                        $rs_forum_rev_article_id=(int)$rs_forum_rev_article_id;

                        if($user_id===$rs_request_from){
                            $rs_request_from_name='你';
                        }
                        if($user_id===$rs_request_to){
                            $rs_request_to_name='你';
                        }

                        $rs_content="
                            {$rs_request_from_name}向{$rs_request_to_name}提出邀請，
                            希望{$rs_request_to_name}能一起參與討論文章：

                        ";
                        if($rs_book_rev_article_id!==0){
                            $rs_img     ='image/book.jpg';
                            $rs_content.="<a href='mssr_forum_book_reply.php?article_id={$rs_article_id}'>【{$rs_article_title}】。</a>";
                        }
                        if($rs_forum_rev_article_id!==0){
                            $rs_img     ='image/group.png';
                            $rs_content.="<a href='mssr_forum_group_reply.php?article_id={$rs_article_id}'>【{$rs_article_title}】。</a>";
                        }

                        $arrys_msg[$rs_time]['request_article'][trim('request_from_name ')] =$rs_request_from_name;
                        $arrys_msg[$rs_time]['request_article'][trim('request_to_name   ')] =$rs_request_to_name;
                        $arrys_msg[$rs_time]['request_article'][trim('request_state     ')] =$rs_request_state;
                        $arrys_msg[$rs_time]['request_article'][trim('request_from      ')] =$rs_request_from;
                        $arrys_msg[$rs_time]['request_article'][trim('request_to        ')] =$rs_request_to;
                        $arrys_msg[$rs_time]['request_article'][trim('request_id        ')] =$rs_request_id;
                        $arrys_msg[$rs_time]['request_article'][trim('keyin_cdate       ')] =$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['request_article'][trim('content           ')] =$rs_content;
                        $arrys_msg[$rs_time]['request_article'][trim('img               ')] =$rs_img;
                    }
                }

            //-------------------------------------------
            //請求加入小組
            //-------------------------------------------

                $arrys_join_forum=array();

                $sql="
                    SELECT
                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr`.`mssr_user_request`.`request_from`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_from_name`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr`.`mssr_user_request`.`request_to`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_name`,

                        `mssr`.`mssr_user_request`.`request_from`,
                        `mssr`.`mssr_user_request`.`request_to`,
                        `mssr`.`mssr_user_request`.`request_id`,
                        `mssr`.`mssr_user_request`.`request_state`,
                        `mssr`.`mssr_user_request`.`keyin_cdate`,
                        `mssr`.`mssr_user_request`.`keyin_mdate`,

                        `mssr`.`mssr_user_request_forum_join_rev`.`rev_id`,
                        `mssr`.`mssr_user_request_forum_join_rev`.`forum_id`,

                        `mssr`.`mssr_forum`.`forum_name`
                    FROM `mssr`.`mssr_user_request`
                        INNER JOIN `mssr_user_request_forum_join_rev` ON
                        `mssr`.`mssr_user_request`.`request_id`=`mssr`.`mssr_user_request_forum_join_rev`.`request_id`

                        INNER JOIN `mssr_forum` ON
                        `mssr`.`mssr_user_request_forum_join_rev`.`forum_id`=`mssr`.`mssr_forum`.`forum_id`
                    WHERE 1=1
                        AND (
                            `mssr`.`mssr_user_request`.`request_from` ={$user_id}
                            OR
                            `mssr`.`mssr_user_request`.`request_to`   ={$user_id}
                        )
                        AND `mssr`.`mssr_user_request`.`request_state`=1
                ";
                $arrys_join_forum=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($arrys_join_forum)){
                    foreach($arrys_join_forum as $inx=>$arry_join_forum){
                        extract($arry_join_forum, EXTR_PREFIX_ALL, "rs");

                        $rs_request_from_name   =trim($rs_request_from_name);
                        $rs_request_to_name     =trim($rs_request_to_name);
                        $rs_request_state       =trim($rs_request_state);
                        $rs_request_from        =(int)$rs_request_from;
                        $rs_request_to          =(int)$rs_request_to;
                        $rs_request_id          =(int)$rs_request_id;
                        $rs_forum_id            =(int)$rs_forum_id;
                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_forum_name          =trim($rs_forum_name);
                        $rs_content             ='';
                        $rs_img                 ='image/group.png';

                        if($user_id===$rs_request_from){
                            $rs_request_from_name='你';
                        }
                        if($user_id===$rs_request_to){
                            $rs_request_to_name='你';
                        }

                        $rs_content="
                            {$rs_request_from_name}向{$rs_request_to_name}提出邀請，
                            希望{$rs_request_to_name}能加入 小組：【{$rs_forum_name}】。
                        ";
                        if($user_id===$rs_request_to){
                            $rs_content.="
                                &nbsp;&nbsp;&nbsp;
                                <a href='mssr_forum_join_forumDb.php?rs_forum_id={$rs_forum_id}&rs_user_id={$rs_request_to}&rs_request_id={$rs_request_id}&rs_request_from={$rs_request_from}'
                                ><u>加入</u></a>
                                &nbsp;&nbsp;
                                <a href='mssr_forum_people_book_db.php?close_rev=取消&request_id={$rs_request_id}'
                                ><u>不加入</u></a>
                            ";
                        }

                        $arrys_msg[$rs_time]['request_join_forum'][trim('request_from_name  ')] =$rs_request_from_name;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('request_to_name    ')] =$rs_request_to_name;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('request_state      ')] =$rs_request_state;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('request_from       ')] =$rs_request_from;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('request_to         ')] =$rs_request_to;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('request_id         ')] =$rs_request_id;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('keyin_cdate        ')] =$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('content            ')] =$rs_content;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('img                ')] =$rs_img;
                    }
                }

            //-------------------------------------------
            //請求聯署建立小組
            //-------------------------------------------

                $arrys_add_forum=array();

                $sql="
                    SELECT
                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr`.`mssr_user_request`.`request_from`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_from_name`,

                        IFNULL((
                            SELECT
                                `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr`.`mssr_user_request`.`request_to`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `request_to_name`,

                        `mssr`.`mssr_user_request`.`request_from`,
                        `mssr`.`mssr_user_request`.`request_to`,
                        `mssr`.`mssr_user_request`.`request_id`,
                        `mssr`.`mssr_user_request`.`request_state`,
                        `mssr`.`mssr_user_request`.`keyin_cdate`,
                        `mssr`.`mssr_user_request`.`keyin_mdate`,

                        `mssr`.`mssr_user_request_forum_create_rev`.`rev_id`,
                        `mssr`.`mssr_user_request_forum_create_rev`.`forum_id`,

                        `mssr`.`mssr_forum`.`forum_name`
                    FROM `mssr`.`mssr_user_request`
                        INNER JOIN `mssr_user_request_forum_create_rev` ON
                        `mssr`.`mssr_user_request`.`request_id`=`mssr`.`mssr_user_request_forum_create_rev`.`request_id`

                        INNER JOIN `mssr_forum` ON
                        `mssr`.`mssr_user_request_forum_create_rev`.`forum_id`=`mssr`.`mssr_forum`.`forum_id`
                    WHERE 1=1
                        AND (
                            `mssr`.`mssr_user_request`.`request_from` ={$user_id}
                            OR
                            `mssr`.`mssr_user_request`.`request_to`   ={$user_id}
                        )
                        AND `mssr`.`mssr_user_request`.`request_state`=1
                ";
                $arrys_add_forum=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($arrys_add_forum)){
                    foreach($arrys_add_forum as $inx=>$arry_add_forum){
                        extract($arry_add_forum, EXTR_PREFIX_ALL, "rs");

                        $rs_request_from_name   =trim($rs_request_from_name);
                        $rs_request_to_name     =trim($rs_request_to_name);
                        $rs_request_state       =trim($rs_request_state);
                        $rs_request_from        =(int)$rs_request_from;
                        $rs_request_to          =(int)$rs_request_to;
                        $rs_request_id          =(int)$rs_request_id;
                        $rs_forum_id            =(int)$rs_forum_id;
                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_forum_name          =trim($rs_forum_name);
                        $rs_content             ='';
                        $rs_img                 ='image/group.png';

                        if($user_id===$rs_request_from){
                            $rs_request_from_name='你';
                        }
                        if($user_id===$rs_request_to){
                            $rs_request_to_name='你';
                        }

                        $rs_content="
                            {$rs_request_from_name}向{$rs_request_to_name}提出邀請，
                            希望{$rs_request_to_name}能一同聯署建立 小組：【{$rs_forum_name}】。
                        ";
                        if($user_id===$rs_request_to){
                            $rs_content.="
                                &nbsp;&nbsp;&nbsp;
                                <a href='mssr_forum_addForumDb.php?rs_forum_id={$rs_forum_id}&rs_user_id={$rs_request_to}&rs_request_id={$rs_request_id}&rs_request_from={$rs_request_from}'
                                ><u>聯署</u></a>
                                <!-- &nbsp;&nbsp;
                                <a href='mssr_forum_people_book_db.php?close_rev=取消&request_id={$rs_request_id}'
                                ><u>不聯署</u></a> -->
                            ";
                        }

                        $arrys_msg[$rs_time]['request_add_forum'][trim('request_from_name   ')] =$rs_request_from_name;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('request_to_name     ')] =$rs_request_to_name;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('request_state       ')] =$rs_request_state;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('request_from        ')] =$rs_request_from;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('request_to          ')] =$rs_request_to;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('request_id          ')] =$rs_request_id;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('keyin_cdate         ')] =$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('content             ')] =$rs_content;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('img                 ')] =$rs_img;
                    }
                }

        //-----------------------------------------------
        //頁面顯示
        //-----------------------------------------------

            //訊息整理
            krsort($arrys_msg);

//echo "<Pre>";
//print_r($arrys_msg);
//echo "</Pre>";
//die();
            call_page($user_id,$arrys_msg);
    }
?>


<?php function call_page($user_id,$arrys_msg){?>
<?php
//echo "<Pre>";
//print_r($arrys_msg);
//echo "</Pre>";
//die();
?>
    <!-- navbar start -->
    <div class="navbar-top">
        <nav class="navbar navbar-default navbar" role="navigation">
            <div class="navbar-header">
                <a class="navbar-brand" href="javascript:void(0);"></a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="index.php"><em class="glyphicon glyphicon-home"></em>首頁</a>
                    </li>
                    <li>
                        <a class="action_code_n0" action_code="n0" go_url='mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>' href="javascript:void(0);"><em class="glyphicon glyphicon-book"></em>我的書櫃</a>
                    </li>
                    <li>
                        <a class="action_code_n1" action_code="n1" go_url='mssr_forum_people_myreply.php?user_id=<?php echo $user_id?>' href="javascript:void(0);"><em class="glyphicon glyphicon-comment"></em>我的討論</a>
                    </li>
                    <li>
                        <a class="action_code_n2" action_code="n2" go_url='mssr_forum_people_group.php?user_id=<?php echo $user_id?>' href="javascript:void(0);"><em class="glyphicon glyphicon-star"></em>我的聊書小組</a>
                    </li>
                    <li>
                        <a class="action_code_n3" action_code="n3" go_url='mssr_forum_people_friend.php?user_id=<?php echo $user_id?>' href="javascript:void(0);"><em class="glyphicon glyphicon-user"></em>我的朋友</a>
                    </li>

                    <!-- msg start -->
                    <li class="dropdown" onclick="view_msg();void(0);">
                        <a id="drop1" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        class="dropdown-toggle action_code_n4" action_code="n4" go_url=''>
                            <em class="glyphicon glyphicon-list-alt"></em>

                            <?php if(!empty($arrys_msg)):?>
                            <div id="msg_cno" style='text-align:center;border-radius:99px;width:25px;height:25px;display:inline-block;color:#ffffff;background-color:#ff0000;'>
                                <p style="position:relative;top:2px;"><b>
                                    <?php if(count($arrys_msg)<=99){echo count($arrys_msg);}else{echo '99+';}?>
                                </b></p>
                            </div>
                            <?php endif;?>訊息
                            <span class="caret"></span>

                            <ul class="dropdown-menu" role="menu" aria-labelledby="drop1" style="width:450px;height:382px;overflow-y:auto;">
                                <li role="presentation" style="width:100%;height:25px;color:#000000;text-decoration:none;" onmouseover="this.style.cursor='default'"
                                onclick="location.href='mssr_forum_people_friend_request.php?user_id=<?php echo $user_id;?>'">

                                    <span style="position:relative;left:10px;text-decoration:none;"
                                    onmouseover="this.style.cursor='default'"><b>通知</b>
                                    </span>

                                    <span style="position:relative;right:10px;text-decoration:none;float:right;"
                                    onmouseover="this.style.cursor='pointer'">
                                        <b>顯示全部</b>
                                    </span>

                                </li>
                                <?php if(!empty($arrys_msg)):?>
                                <?php foreach($arrys_msg as $arry_msg):?>
                                <?php foreach($arry_msg as $value):?>
                                <?php
                                    $rs_content=trim($value['content']);
                                    $rs_img    =trim($value['img']);
                                ?>
                                <li role="presentation" style="width:100%;height:70px;background-color:#fdfdff;border-bottom:1px solid #ebebeb;">
                                    <div style="width:100%;word-break:break-all;">
                                        <img src="<?php echo $rs_img;?>" width="55" height="55" border="0" alt="0" style="float:left;"
                                        onmouseover="this.style.cursor='default'"/>
                                        <a role="menuitem" tabindex="-1" href="javascript:void(0);" style='color:#000000;text-decoration:none;'
                                        onmouseover="this.style.cursor='default'"><?php echo $rs_content;?></a>
                                    </div>
                                </li>
                                <?php endforeach;?>
                                <?php endforeach;?>
                                <?php endif;?>
                                <!-- <li role="presentation" class="divider" style="width:100%;position:absolute;bottom:20px;"></li> -->
                                <!-- <li role="presentation" style="width:100%;text-align:center;font-size:16px;position:absolute;bottom:0px;background-color:#fdfdff;"> -->
                                <!-- <li role="presentation" class="divider" style="width:100%;"></li> -->
                                <!-- <li role="presentation" style="width:100%;height:30px;text-align:center;font-size:16px;position:relative;background-color:#fdfdff;border-top:1px solid #ebebeb;">
                                    <a role="menuitem" tabindex="-1" href="mssr_forum_people_friend_request.php?user_id=<?php echo $user_id;?>" style="color:#1d118a;">顯示全部</a>
                                </li> -->
                            </ul>
                        </a>
                    </li>
                    <!-- msg end -->

                    <!-- config start -->
                    <li class="dropdown">
                        <a id="drop1" href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <em class="glyphicon glyphicon-cog"></em>設置
                            <span class="caret"></span>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="drop1" style="width:200px;">
                                <li role="presentation" style="width:100%;">
                                    <a role="menuitem" tabindex="-1" href="javascript:void(0);" style='color:#000000;'
                                    class="action_code_n5" action_code="n5" go_url='mssr_forum_create_group.php?user_id=<?php echo $user_id;?>'
                                    >建立聊書小組</a>
                                </li>
                                <li role="presentation" style="width:100%;">
                                    <a role="menuitem" tabindex="-1" href="/mssr/service/mssr_menu.php" style='color:#000000;'>進行閱讀登記</a>
                                </li>
                                <li role="presentation" style="width:100%;">
                                    <a role="menuitem" tabindex="-1" href="logout.php" style='color:#000000;'>登出</a>
                                </li>
                            </ul>
                        </a>
                    </li>
                    <!-- config end -->
                </ul>

                <ul class="nav navbar-nav navbar-right">
                    <form class="navbar-form navbar-left" >
                        <div class="form-group">
                            <input id="search_text" type="text" class="input-medium search-query" style='width:80px;'>
                            <select id="search_type" name="search_type" size="1">
                                <option value="人" selected>人</option>
                                <option value="書">書</option>
                                <option value="群">群</option>
                            </select>
                            <button id="BtnQ" type="button" class="btn btn-default btn-sm">搜尋</button>
                        </div>
                    </form>
                </ul>
            </div>
        </nav>
    </div>
    <!-- navbar end -->

<!-- 頁面js  -->
<script type="text/javascript" src="inc/add_action_forum_log/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    //OBJ
    var oBtnQ       =document.getElementById('BtnQ');
    var osearch_type=document.getElementById('search_type');
    var osearch_text=document.getElementById('search_text');

    //FUNCTION
    $('.action_code_n0, .action_code_n1, .action_code_n2, .action_code_n3, .action_code_n4, .action_code_n5').click(function(){
        //呼叫
        add_action_forum_log(
            process_url ='inc/add_action_forum_log/code.php',
            action_code =$(this).attr('action_code'),
            action_from ='<?php echo (int)$_SESSION["uid"];?>',
            user_id_1   =0,
            user_id_2   =0,
            book_sid_1  ='',
            book_sid_2  ='',
            forum_id_1  =0,
            forum_id_2  =0,
            article_id  =0,
            reply_id    =0,
            go_url      =$(this).attr('go_url')
        );
    });

    $(osearch_text).keypress(function(e){
        if(e.which===13){
            query();
            return false;
        }
    });

    oBtnQ.onclick=function(){

        var search_type_val=osearch_type.value;
        var search_text_val=osearch_text.value;

        if(search_text_val===''){
            alert('請輸入搜尋條件');
            return false;
        }

        var url ='';
        var page=str_repeat('../',0)+'query.php';
        var arg ={
            'psize'          :10,
            'pinx'           :1,
            'search_type_val':search_type_val,
            'search_text_val':search_text_val
        };
        var _arg=[];
        for(var key in arg){
            _arg.push(key+"="+encodeURI(arg[key]));
        }
        arg=_arg.join("&");

        if(arg.length!=0){
            url+=page+"?"+arg;
        }else{
            url+=page;
        }

        switch(search_type_val){
            case'人':
                action_code='n6';
            break;
            case'書':
                action_code='n7';
            break;
            case'群':
                action_code='n8';
            break;
        }
        //呼叫
        add_action_forum_log(
            process_url ='inc/add_action_forum_log/code.php',
            action_code =action_code,
            action_from ='<?php echo (int)$_SESSION["uid"];?>',
            user_id_1   =0,
            user_id_2   =0,
            book_sid_1  ='',
            book_sid_2  ='',
            forum_id_1  =0,
            forum_id_2  =0,
            article_id  =0,
            reply_id    =0,
            go_url      =url
        );

        //go(url,'self');
    }

    function query(){

        var search_type_val=osearch_type.value;
        var search_text_val=osearch_text.value;

        if(search_text_val===''){
            alert('請輸入搜尋條件');
            return false;
        }

        var url ='';
        var page=str_repeat('../',0)+'query.php';
        var arg ={
            'psize'          :10,
            'pinx'           :1,
            'search_type_val':search_type_val,
            'search_text_val':search_text_val
        };
        var _arg=[];
        for(var key in arg){
            _arg.push(key+"="+encodeURI(arg[key]));
        }
        arg=_arg.join("&");

        if(arg.length!=0){
            url+=page+"?"+arg;
        }else{
            url+=page;
        }

        switch(search_type_val){
            case'人':
                action_code='n6';
            break;
            case'書':
                action_code='n7';
            break;
            case'群':
                action_code='n8';
            break;
        }
        //呼叫
        add_action_forum_log(
            process_url ='inc/add_action_forum_log/code.php',
            action_code =action_code,
            action_from ='<?php echo (int)$_SESSION["uid"];?>',
            user_id_1   =0,
            user_id_2   =0,
            book_sid_1  ='',
            book_sid_2  ='',
            forum_id_1  =0,
            forum_id_2  =0,
            article_id  =0,
            reply_id    =0,
            go_url      =url
        );
    }

    function view_msg(){
        if(typeof(Storage)!=="undefined"){
            //var storage=ExtLocalStorage("msg");
            //storage("msg","yes");
            //alert(storage("msg"));
            //localStorage.removeItem('msg');
            sessionStorage.setItem("msg", "yes");
            try{
                var omsg_cno=document.getElementById('msg_cno');
                $(omsg_cno).hide();
            }catch(e){
            }
        }
    }

    //ONLOAD
    $(document).ready(function(){
        //FUNCTION
        var extLocalStorage=function(namespace){
            var localStorage=window.localStorage||{};
            if(typeof namespace!=="string"){
                throw new Error("extLocalStorage: Namespace must be a string");
            }
            var getRealKey=function(key){
                return [namespace,".",key].join('');
            };
            var mainFunction=function(key,value){
                var realKey=getRealKey(key);
                if(value===undefined){
                    return localStorage[realKey];
                }else{
                    return localStorage[realKey]=value;
                }
            };
            mainFunction.remove=function(key){
                var realKey=getRealKey(key);
                delete localStorage[realKey];
            };
            return mainFunction;
        };
        window.ExtLocalStorage=extLocalStorage;

        if((sessionStorage.msg!==undefined)&&(sessionStorage.msg==='yes')){
            try{
                var omsg_cno=document.getElementById('msg_cno');
                $(omsg_cno).hide();
            }catch(e){
            }
        }
    });

</script>
<?php }?>