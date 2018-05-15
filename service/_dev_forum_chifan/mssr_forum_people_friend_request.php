<?php
//-------------------------------------------------------
//mssr_fourm
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------
//        //SESSION
          @session_start();
//
//        //啟用BUFFER
//        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",0).'inc/require_page/code.php');

        //外掛設定檔
        require_once(str_repeat("../",2).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code'
       	);
        func_load($funcs,true);

        //清除並停用BUFFER
//        @ob_end_clean();
	//---------------------------------------------------
    //SESSION
    //---------------------------------------------------


      $sess_uid=(int)$_SESSION["uid"];
    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
			//-----------------------------------------------
	        //下載資料庫
	        //-----------------------------------------------
        		//-----------------------------------------------
            	//檢核
            	//-----------------------------------------------
					$user_id = (int)$_GET["user_id"];
				//-----------------------------------------------
	        	//SQL-userinfo(學生資訊)
	        	//-----------------------------------------------
					$sql="
						SELECT
							`name`
						FROM
							`member`
						WHERE
							`uid` = $user_id
					";
					$arrys_result_userinfo=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
					$numrow_userinfo=count($arrys_result_userinfo);
				//-----------------------------------------------
	        	//SQL-userclasscode
	        	//-----------------------------------------------
//					$sql="
//						SELECT*
//						FROM
//							(SELECT
//								`student`.`class_code`, `student`.`uid`
//							FROM
//								`student`
//
//							UNION
//
//							SELECT
//								`teacher`.`class_code`, `teacher`.`uid`
//							FROM
//								`teacher`)tmp
//						WHERE 1=1
//							AND	`uid` = $user_id
//						ORDER BY
//							`class_code` DESC
//					";
//					$arrys_result_userclasscode=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
//					$numrow_userclasscode=count($arrys_result_userclasscode);
				//-----------------------------------------------
	        	//SQL-usergrade(用class_code找學生年級資訊)
	        	//-----------------------------------------------
					 $class_code = trim($_SESSION['class'][0][1]);

					$sql="
						SELECT
							`class`.`grade`, `class`.`classroom`, `class`.`class_code`, `semester`.`school_code`
						FROM
							`class` inner join `semester`
							on `class`.`semester_code` = `semester`.`semester_code`
						WHERE
							`class`.`class_code` = '$class_code'
					";


					$arrys_result_usergrade=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);

					$numrow_usergrade=count($arrys_result_usergrade);

				//-----------------------------------------------
	        	//SQL-arrys_result_user_school(用class_code找學生學校資訊)
	        	//-----------------------------------------------
					$user_school = $arrys_result_usergrade[0]["school_code"];

					$sql="
						SELECT
							`school_name`, `region_name`
						FROM
							`school`
						WHERE
							`school_code` = '$user_school'
					";

					$arrys_result_user_school=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);

					$numrow_user_school=count($arrys_result_user_school);
				//-----------------------------------------------
	        	//SQL-shelf(書櫃)
	        	//-----------------------------------------------
					$sql="
						SELECT
							`book_sid`,
                            `borrow_sdate`
						FROM
							`mssr_book_borrow_log`
						WHERE 1=1
							 and `user_id` = $user_id
                             and  borrow_sdate >='2014-08-01'
                             order by borrow_sdate desc
					";
					$arrys_result_shelf=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_shelf=count($arrys_result_shelf);
				//-----------------------------------------------
	        	//SQL-學生發文數量
	        	//-----------------------------------------------
					$sql="
						SELECT
							`user_id`
						FROM
							`mssr_forum_article`
						WHERE
							`user_id` = $user_id

					";
					$arrys_result_articlenum=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_articlenum=count($arrys_result_articlenum);
				//-----------------------------------------------
	        	//SQL-學生回復數量
	        	//-----------------------------------------------
					$sql="
						SELECT
							`user_id`
						FROM
							`mssr_forum_article_reply`
						WHERE
							`user_id` = $user_id

					";
					$arrys_result_replynum=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_replynum=count($arrys_result_replynum);

				//-----------------------------------------------
	        	//SQL-檢查是否為好友
	        	//-----------------------------------------------

					$sql="
						SELECT
							`user_id`, `friend_id`
						FROM
							`mssr_forum_friend`
						WHERE 1=1
							AND `friend_state` 	= '成功'
							AND ((`user_id`		= $user_id
								AND
								 `friend_id`	= $sess_uid)
									OR
								 (`user_id`		= $sess_uid
								 AND
								 `friend_id`	=$user_id))


					";
					$arrys_result_friend_check = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



                //-----------------------------------------------
	        	//SQL-確認是否加入好友
	        	//-----------------------------------------------

					$sql="
						SELECT
							`user_id`, `friend_id`,name,keyin_cdate
						FROM
							`mssr_forum_friend`
                        join user.member on mssr_forum_friend.user_id =  user.member.uid

						WHERE 1=1
							AND `friend_state` 	= '確認中'
							AND `friend_id`		= $sess_uid


					";
					$arrys_friend_check = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


//echo "<Pre>";
//print_r($arrys_friend_check);
//echo "</Pre>";



              $arrys_book = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);





                //推薦
	        	//-----------------------------------------------

					$sql="
						SELECT
							request_from,name,keyin_cdate,mssr_user_request.request_id
						FROM
							 mssr.mssr_user_request
                        join user.member on mssr.mssr_user_request.request_from =  user.member.uid
                        join mssr_user_request_book_rev on mssr_user_request_book_rev.request_id = mssr_user_request.request_id

						WHERE 1=1
							AND `request_state` 	= '1'
							AND `request_to`		= $user_id


					";
					$arrys_book_check = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

                //看過的書
                //-----------------------------------------------
                   $sql="
                                                SELECT
							`book_sid`,
                            `borrow_sdate`
						FROM
							`mssr_book_borrow_log`
						WHERE 1=1
							 and `user_id` = $sess_uid
                             and  borrow_sdate >='2014-08-01'
                             order by borrow_sdate desc
                        ";
                   $lookBook = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

//echo "<Pre>";
//print_r($lookBook);
//echo "</Pre>";




                //---------------------------------------------------
                //好友列表
                //---------------------------------------------------

                $sql = "
                        SELECT
							`user_id`, `friend_id`
						FROM
							`mssr_forum_friend`
						WHERE 1=1
							AND `friend_state` 	= '成功'
							AND ((`user_id`		= $user_id
								AND
								 `friend_id`	= $sess_uid)
									OR
								 (`user_id`		= $sess_uid
								 AND
								 `friend_id`	=$user_id))

                       ";

            $check_friend = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $arrys_msg=array();

            //-------------------------------------------
            //交友訊息
            //-------------------------------------------

                $arrys_friend=array();


//                    SELECT
//                        IFNULL((
//                            SELECT
//                                `user`.`member`.`name`
//                            FROM `user`.`member`
//                            WHERE `mssr`.`mssr_forum_friend`.`user_id`=`user`.`member`.`uid`
//                            LIMIT 1
//                        ),'') AS `user_name`,
//
//                        IFNULL((
//                            SELECT
//                                `user`.`member`.`name`
//                            FROM `user`.`member`
//                            WHERE `mssr`.`mssr_forum_friend`.`friend_id`=`user`.`member`.`uid`
//                            LIMIT 1
//                        ),'') AS `friend_name`,
//
//                        `mssr`.`mssr_forum_friend`.`user_id`,
//                        `mssr`.`mssr_forum_friend`.`friend_id`,
//                        `mssr`.`mssr_forum_friend`.`friend_state`,
//                        `mssr`.`mssr_forum_friend`.`keyin_cdate`
//                    FROM `mssr`.`mssr_forum_friend`
//                    WHERE 1=1
//                        AND (
//                            `mssr`.`mssr_forum_friend`.`user_id`  ={$user_id}
//                                OR
//                            `mssr`.`mssr_forum_friend`.`friend_id`={$user_id}
//                        )
//                        AND `mssr`.`mssr_forum_friend`.`friend_state` IN ('成功','失敗')
//                        AND DATE(`mssr`.`mssr_forum_friend`.`keyin_cdate`) >= CURDATE() - INTERVAL 1 DAY
                    #--------------- UNION ---------------#
                                     //UNION
                    #--------------- UNION ---------------#
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
                                <!-- 請問你是否要跟他成為朋友? -->
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
            //請求推加入小組
            //-------------------------------------------
            $arrays_group = array();
            $sql="
                SELECT
                    IFNULL((
                        SELECT
                            `user`.`member`.`name`
                        FROM `user`.`member`
                        WHERE mssr.mssr_forum.create_by =`user`.`member`.`uid`
                        LIMIT 1
                    ),'') AS create_name,

                    IFNULL((
                        SELECT
                            `user`.`member`.`name`
                        FROM `user`.`member`
                        WHERE mssr.mssr_user_forum.user_id =`user`.`member`.`uid`
                        LIMIT 1
                    ),'') AS `friend_name`,
                    mssr.mssr_forum.forum_name,
                    mssr.mssr_forum.create_by,

                    mssr.mssr_user_forum.keyin_cdate,
                    mssr.mssr_user_forum.user_id,
                    mssr.mssr_user_forum.forum_id
                FROM
                      mssr.mssr_forum
                INNER JOIN  mssr_user_forum  on mssr_forum.forum_id = mssr_user_forum.forum_id
                WHERE   1=1
                  AND  mssr_forum.create_by = $sess_uid
                  AND  mssr_user_forum.user_state ='申請中'
                  AND  mssr.mssr_forum.forum_state='啟用'
                ";
            $arrays_group=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($arrays_group)){
                foreach($arrays_group as $inx =>$array_group){
                    extract($array_group, EXTR_PREFIX_ALL, "rs");
                        $rs_forum_name          =trim($rs_forum_name);
                        $rs_friend_name         =trim($rs_friend_name);

                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_content             ='';
                        $rs_img                 ='image/book.jpg';

                        $rs_content="
                            {$rs_friend_name}向你提出申請，
                            希望{$rs_friend_name}能加入【{$rs_forum_name}】小組。
                        ";
                        $arrys_msg[$rs_time]['request_group'][trim('user_id          ')] =$rs_user_id;
                        $arrys_msg[$rs_time]['request_group'][trim('forum_id         ')] =$rs_forum_id;
                        $arrys_msg[$rs_time]['request_group'][trim('forum_name       ')] =$rs_forum_name;
                        $arrys_msg[$rs_time]['request_group'][trim('friend           ')] =$rs_friend_name;
                        $arrys_msg[$rs_time]['request_group'][trim('keyin_cdate      ')] =$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['request_group'][trim('content          ')] =$rs_content;
                        $arrys_msg[$rs_time]['request_group'][trim('img              ')] =$rs_img;
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
                        `mssr`.`mssr_user_request`.`request_question`,

                        `mssr`.`mssr_user_request_book_rev`.`rev_id`,
                        `mssr`.`mssr_user_request_book_rev`.`request_content`
                    FROM `mssr`.`mssr_user_request`
                        INNER JOIN `mssr_user_request_book_rev` ON
                        `mssr`.`mssr_user_request`.`request_id`=`mssr`.`mssr_user_request_book_rev`.`request_id`
                    WHERE 1=1
                        AND `mssr`.`mssr_user_request`.`request_to` ={$user_id}


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
                        $rs_request_question    =trim($rs_request_question);
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
                            {$rs_request_from_name}向{$rs_request_to_name}提出邀請，
                            希望{$rs_request_to_name}能推薦一本書給{$rs_request_from_name}。 {$rs_request_question}

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

                        `mssr`.`mssr_forum_article`.`article_title`
                    FROM `mssr`.`mssr_user_request`
                        INNER JOIN `mssr`.`mssr_user_request_discussion_rev` ON
                        `mssr`.`mssr_user_request`.`request_id`=`mssr`.`mssr_user_request_discussion_rev`.`request_id`

                        INNER JOIN `mssr`.`mssr_forum_article` ON
                        `mssr`.`mssr_user_request_discussion_rev`.`article_id`=`mssr`.`mssr_forum_article`.`article_id`
                    WHERE 1=1
                        AND `mssr`.`mssr_user_request`.`request_to`   ={$user_id}
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
                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_article_title       =trim($rs_article_title);
                        $rs_content             ='';
                        $rs_img                 ='image/book.jpg';

                        if($user_id===$rs_request_from){
                            $rs_request_from_name='你';
                        }
                        if($user_id===$rs_request_to){
                            $rs_request_to_name='你';
                        }

                        $rs_content="
                            {$rs_request_from_name}向{$rs_request_to_name}提出邀請，
                            希望{$rs_request_to_name}能一起參與討論文章:【{$rs_article_title}】。
                        ";

                        $arrys_msg[$rs_time]['request_article'][trim('request_from_name ')] =$rs_request_from_name;
                        $arrys_msg[$rs_time]['request_article'][trim('article_id        ')] =$rs_article_id;
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

                        `mssr`.`mssr_forum`.`forum_id`,
                        `mssr`.`mssr_forum`.`forum_name`
                    FROM `mssr`.`mssr_user_request`
                        INNER JOIN `mssr_user_request_forum_join_rev` ON
                        `mssr`.`mssr_user_request`.`request_id`=`mssr`.`mssr_user_request_forum_join_rev`.`request_id`

                        INNER JOIN `mssr_forum` ON
                        `mssr`.`mssr_user_request_forum_join_rev`.`forum_id`=`mssr`.`mssr_forum`.`forum_id`
                    WHERE 1=1
                        AND `mssr`.`mssr_user_request`.`request_to` ={$user_id}
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
                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_forum_name          =trim($rs_forum_name);
                        $rs_forum_id            =(int)($rs_forum_id);
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
                            希望{$rs_request_to_name}能加入【{$rs_forum_name}】。
                        ";

                        $arrys_msg[$rs_time]['request_join_forum'][trim('request_from_name  ')] =$rs_request_from_name;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('request_to_name    ')] =$rs_request_to_name;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('request_state      ')] =$rs_request_state;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('request_from       ')] =$rs_request_from;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('request_to         ')] =$rs_request_to;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('request_id         ')] =$rs_request_id;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('keyin_cdate        ')] =$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('content            ')] =$rs_content;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('img                ')] =$rs_img;
                        $arrys_msg[$rs_time]['request_join_forum'][trim('fourm_id           ')] =$rs_forum_id;
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
                        `mssr`.`mssr_forum`.`forum_id`,
                        `mssr`.`mssr_forum`.`forum_name`
                    FROM `mssr`.`mssr_user_request`
                        INNER JOIN `mssr_user_request_forum_create_rev` ON
                        `mssr`.`mssr_user_request`.`request_id`=`mssr`.`mssr_user_request_forum_create_rev`.`request_id`

                        INNER JOIN `mssr_forum` ON
                        `mssr`.`mssr_user_request_forum_create_rev`.`forum_id`=`mssr`.`mssr_forum`.`forum_id`
                    WHERE 1=1
                        AND
                            `mssr`.`mssr_user_request`.`request_to`   ={$user_id}
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
                        $rs_keyin_cdate         =trim($rs_keyin_cdate);
                        $rs_time                =trim(strtotime($rs_keyin_cdate));
                        $rs_forum_name          =trim($rs_forum_name);
                        $rs_forum_id            =(int)($rs_forum_id);
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
                            希望{$rs_request_to_name}能一同聯署建立【{$rs_forum_name}】。
                        ";

                        $arrys_msg[$rs_time]['request_add_forum'][trim('request_from_name   ')] =$rs_request_from_name;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('request_to_name     ')] =$rs_request_to_name;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('request_state       ')] =$rs_request_state;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('request_from        ')] =$rs_request_from;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('request_to          ')] =$rs_request_to;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('request_id          ')] =$rs_request_id;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('keyin_cdate         ')] =$rs_keyin_cdate;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('content             ')] =$rs_content;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('img                 ')] =$rs_img;
                        $arrys_msg[$rs_time]['request_add_forum'][trim('fourm_id            ')] =$rs_forum_id;
                    }
                }

        //-----------------------------------------------
        //頁面顯示
        //-----------------------------------------------




// foreach($arrys_msg as $arrys_msg_k=>$arrys_msg_v){
//            foreach($arrys_msg_v as $arrys_msg_v_k => $arrys_msg_v_v){
//                echo "<Pre>";
//                print_r($arrys_msg_v_k);
//                echo "</Pre>";
//    }
//}



            //訊息整理
            krsort($arrys_msg);
//echo "<Pre>";
//print_r($arrys_msg);
//echo "</Pre>";



	//---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球-好友請求/邀請";

        //加入好友顯示隱藏

        if($sess_uid == $user_id){
            $firShow = 'display:none';
        }else{
            $firShow = 'display:';
        }






?>
<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
<title><?php echo $title?></title>
</head>

     <!-- 通用js  -->
    <script type="text/javascript" src="../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../inc/code.js"></script>
    <script type="text/javascript" src="../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../lib/js/table/code.js"></script>
     <script type="text/javascript" src="inc/add_action_forum_log/code.js"></script>

    <!-- 專屬js  -->
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/chosen.css">
    <link type="text/css" rel="stylesheet" href="css/bootstrap.css">
    <link type="text/css" rel="stylesheet" href="css/mssr_forum.css">
    <link type="text/css" rel="stylesheet" href="../../inc/code.css">
    <script src="js/chosen.jquery.js" type="text/javascript"></script>
  <style>
    #open_book_rev{

           border: 1px solid #000;
           background-color:#FFF;
           position: fixed;
           z-index:100;

        }

.elseFriends{
    padding:20px;
    height:auto;
    display:none;
    background-color:#FFFFFF;
    border: 1px solid red;
    width: 500px;
    position: fixed;
    left:50%;
    top:30%;
    margin-left:-250px;
}
.intro{
    padding:20px;
    height:auto;
    display:none;
    background-color:#FFFFFF;
    border: 1px solid red;
    width: 500px;
    position: fixed;
    left:50%;
    top:30%;
    margin-left:-250px;
}
a{
    cursor: pointer;
}


  </style>

    <script>

$(document).ready(Block);
$(document).ready(ada);

function ada(){
		// 轉換成 chosen 效果
		$(".chzn-select").chosen({
            search_contains: true,
            allow_single_deselect: true
        });
        $('.chzn-select').trigger('chosen:updated');
};

function ad(){


		// 轉換成 chosen 效果
		$(".select_book").chosen({
            search_contains: true,
            allow_single_deselect: true
        });
        $('.select_book').trigger('chosen:updated');
};




function Block(){
	$('.book_rev').click(function() {

            $('#open_book_rev').show();
            $('#open_book_rev').css
                ({
					top:  ($(window).height() - 400) /2 + 'px',
					left: ($(window).width() - 700) /2 + 'px',
					textAlign:	'left',
					width: '700px'
				});

                $('#input_leave').click(function() {
                     $('#open_book_rev').hide();
                     return false
		        });
		});
}







function search_book()
{

      var b = $("[id='get_request_id']").val();
      var m = $("[id='request_book_id']").val();
      var s = $("[id='select_books']:selected").val();


    if(m == '' && s == ''){
        alert('輸入ISBN或選擇一本書');
    }else{
       $.ajax
           ({
                    type:"get", //提交类型
                    url:"mssr_forum_people_book_request_Act.php", //提交页面
                    data:{ request_book_id: m , get_request_id: b , select_book: s},
                    async: false,
                    contentType: "application/json; charset=utf-8",
                    dataType: 'json',
                    success:function(respones)
                    {




                        var res  = eval(respones);
                        var flag = res[0].flag;
                        if(flag == 'err')
                        {
                            alert('書本不存在');
                        }else
                        {

                           $('#selectBook').show();
                           $('#inputBook').hide();
                           $('#request_id').attr("value",b);


                            for(var i=0;i<res.length;i++)
                            {

                                $('#select_book').append("<option  value ='"+res[i].book_sid+"'>"+res[i].name+"</option>");
                                ad();
                            }

                        }
                    }
            });
    }
}



function check(){
      var select = $("[id='select_books']:selected").val()
      if(select == null){
            alert('請選擇一本書');
            return false
      }
}


    $(document).ready(function() {
        $('.forum_sessId').click(function(){
            $('.elseFriends').show();
                $('#close').click(function(){
                    $('.elseFriends').hide();
                    $('.remove').remove();
                })
        });
    });

    function elsefriends(forumId){
        var forum_name = $("[class='forum_span"+forumId+"']").val();
        var friends = $("[class='all_friends"+forumId+"']").val();
        var fail = $("[class='fail_friends"+forumId+"']").val();
        $('.remove').remove();

         $.ajax
           ({
                    type:"get", //提交类型
                    url:"mssr_forum_elsefirends.php", //提交页面
                    data:{ forum_span: forum_name , all_friends: friends , fail_friends: fail },
                    async: false,
                    contentType: "application/json; charset=utf-8",
                    dataType: 'json',
                    success:function(respones) {
                        var res  = eval(respones);

                        for(var i=0;i<res.length;i++){
                           $('.insetFriends').append("<figure class = 'remove' style='margin-top:35px;margin-left:30px'><img src='image/boy.jpg'  width='80px' height='80px' /><figcaption><div  class='checkbox disabled' style='width:110px;margin-left:-15px'><span style='margin-left:-10px'><a href='mssr_forum_people_index.php?user_id="+res[i].user_id+"'>"+res[i].name+"</a></span></div></figcaption></figure>");
                        }
                    },
                    error       :function(xhr, ajaxoptions, thrownerror){
                    //失敗處理
                        if(ajaxoptions==='timeout'){
                            alert('timeout');
                            return false;
                        }else{
                            alert('error');
                            return false;
                        }
                    },
                    complete    :function(){
                    //傳送後處理
                    }
            });

    }

        function bookelsefriends(forumId){
            var book_sid     = $("[class='book_sid"+forumId+"']").val();
            var all_friends  = $("[class='all_friends"+forumId+"']").val();
            var user_id      = $("[class='user_id"+forumId+"']").val();
            $('.remove').remove();
//            alert(book_sid);
//            alert(all_friends);
//            alert(user_id);



         $.ajax
           ({
                    type:"get", //提交类型
                    url:"mssr_forum_bookelsefirends.php", //提交页面
                    data:{ book_sid: book_sid , all_friends: all_friends , user_id: user_id },
                    async: false,
                    contentType: "application/json; charset=utf-8",
                    dataType: 'json',
                    success:function(respones) {
                        var res  = eval(respones);

                        for(var i=0;i<res.length;i++){
                                $('.insetFriends').append("<figure class = 'remove' style='margin-top:35px;margin-left:30px'><img src='image/boy.jpg'  width='80px' height='80px' /><figcaption><div  class='checkbox disabled' style='width:110px;margin-left:-15px'><span style='margin-left:-10px'><a href='mssr_forum_people_index.php?user_id="+res[i].user_id+"'>"+res[i].name+"</a></span></div></figcaption></figure>");
                        }
                    },
                    error       :function(xhr, ajaxoptions, thrownerror){
                    //失敗處理
                        if(ajaxoptions==='timeout'){
                            alert('timeout');
                            return false;
                        }else{
                            alert('error');
                            return false;
                        }
                    },
                    complete    :function(){
                    //傳送後處理
                    }
            });

    }
        function samebookelsefriends(forumId){
            var book_sid     = $("[class='book_sid"+forumId+"']").val();
            var all_friends  = $("[class='all_friends"+forumId+"']").val();
            var user_id      = $("[class='user_id"+forumId+"']").val();
            var asc         = $("[class='user"+forumId+"']").val();
            $('.remove').remove();
//            alert(book_sid);
//            alert(all_friends);
//            alert(user_id);
//            alert(asc);





         $.ajax
           ({
                    type:"get", //提交类型
                    url:"mssr_forum_samebookelsefirends.php", //提交页面
                    data:{ user: asc , book_sid: book_sid , all_friends: all_friends , user_id: user_id },
                    async: false,
                    contentType: "application/json; charset=utf-8",
                    dataType: 'json',
                    success:function(respones) {
                        var res  = eval(respones);

                        for(var i=0;i<res.length;i++){
                                $('.insetFriends').append("<figure class = 'remove' style='margin-top:35px;margin-left:30px'><img src='image/boy.jpg'  width='80px' height='80px' /><figcaption><div  class='checkbox disabled' style='width:110px;margin-left:-15px'><span style='margin-left:-10px'><a href='mssr_forum_people_index.php?user_id="+res[i].user_id+"'>"+res[i].name+"</a></span></div></figcaption></figure>");
                        }
                    },
                    error       :function(xhr, ajaxoptions, thrownerror){
                    //失敗處理
                        if(ajaxoptions==='timeout'){
                            alert('timeout');
                            return false;
                        }else{
                            alert('error');
                            return false;
                        }
                    },
                    complete    :function(){
                    //傳送後處理
                    }
            });

    }


        $(document).ready(function() {
        $('.introPop').click(function(){
            $('.intro').show();
                $('.introClose').click(function(){
                    $('.intro').hide();
                })
        });
    });



    </script>













<body>

 <!-- navbar start -->
    <?php r_p_navbar((int)$_SESSION["uid"],$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);?>
<!-- navbar end -->

<!--========================root=====================================-->
  <div class="root" >

      <ul class="breadcrumb">
    目前位置：
		  <li>
            <a href="index.php">首頁</a> <span class="divider"></span>
          </li>
          <li>
            <a href="mssr_forum_people_index.php"><?php echo $arrys_result_userinfo[0]['name']?>個人頁面</a> <span class="divider"></span>
          </li>
		  <li>
            <a href="mssr_forum_people_friend.php?user_id=<?php echo $user_id?>">朋友</a> <span class="divider"></span>
          </li>

          <li class="active">
            收到請求/邀請
          </li>
        </ul>
  </div>

<!--========================group header=====================================-->
  <div class="group_header">
        <div class="group_image" >
          <?php
			$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
			if($get_user_info[0]['sex']==1){?>
            	<img src="image/boy.jpg" width="100px" height="100px" />
            <?php }else{?>
            	<img src="image/girl.jpg" width="100px" height="100px" />
            <?php }?>
        </div>

        <div class="group_info1">

			<?php echo $arrys_result_userinfo[0]['name']?><BR>
            <?php echo $arrys_result_user_school[0]['school_name']?><?php echo $arrys_result_usergrade[0]['grade']?>年<?php echo $arrys_result_usergrade[0]['classroom']?>班
			<BR><BR>

                <?php if(!empty($check_friend)){ ?>

                    <a style="float:left" class="btn" type="button">已是好友</a>
                <?php }else{ ?>

                    <a style="float:left;<?php echo $firShow ?>;" href="add/add_friendA.php?user_id=<?php echo $user_id; ?>&sess_uid=<?php echo $sess_uid; ?>" class="btn" type="button">加為好友</a>
                <?php } ?>

        </div>

        <div class="group_info2">

          <?php echo $arrys_result_userinfo[0]['name']?>的閱讀資訊:<BR>
			  發表了<?php echo $numrow_articlenum?>篇文章<BR>
			  已經讀了<?php echo $numrow_shelf?>本書<BR>
			  回覆<?php echo $numrow_replynum?>篇文章<BR>
<I></I>
        </div>

 </div>


<!--========================tab_bar=====================================-->
    <div class="tab_bar">

         <div class="tabbable" id="tabs-215204">
			<ul class="nav nav-tabs">
			<li>
				<a onclick="logFuc('inc/add_action_forum_log/code.php','p14',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'mssr_forum_people_index.php?user_id=<?php echo $user_id?>')">首頁</a>
			  </li>
			  <li>
				<a onclick="logFuc('inc/add_action_forum_log/code.php','p8',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>')">書櫃</a>
			  </li>
			  <li >
				<a onclick="logFuc('inc/add_action_forum_log/code.php','p9',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'mssr_forum_people_myreply.php?user_id=<?php echo $user_id?>')">討論</a>
			  </li>
			  <li>
				<a onclick="logFuc('inc/add_action_forum_log/code.php','p10',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'mssr_forum_people_group.php?user_id=<?php echo $user_id?>')">聊書小組</a>
			  </li>
			  <li class="active">
				<a onclick="logFuc('inc/add_action_forum_log/code.php','p11',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'mssr_forum_people_friend.php?user_id=<?php echo $user_id?>')">朋友</a>
			  </li>
			  </ul>
		  </div>

	</div>




<!--========================content=====================================-->
<div class="content">

    <div class="left_content">







        <div class="btn-group">

			<input class="btn btn-default" type ="button" onclick="javascript:location.href='mssr_forum_people_friend.php?user_id=<?php echo $user_id?>'" value="我的朋友"></input>
         <input class="btn btn-default" type ="button" onclick="logFuc('inc/add_action_forum_log/code.php','p15',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'mssr_forum_people_friend_request.php?user_id=<?php echo $sess_uid?>')" value="收到邀請/請求"></input>
		</div>


     <BR> <BR>



<?php foreach($arrys_msg as $arrys_msg_k=>$arrys_msg_v){
            foreach($arrys_msg_v as $arrys_msg_v_k => $arrys_msg_v_v){?>




                    <?php  if($arrys_msg_v_k == 'friend_msg'){ ?>

                    <figure id="index_info">
                        <a href="#">
                            <img id="index_info_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
                        </a>
                        <form action ='mssr_forum_people_friend_request_Act.php'>
                            <p id="index_info_name">
                            <?php echo $arrys_msg_v_v['content'] ?>

                            <br><br>
                                <input type= "hidden" name ="user_id"  value = "<?php echo $arrys_msg_v_v['user_id'];  ?>" />
                                <input type= "hidden" name ="sess_uid" value = "<?php echo $arrys_msg_v_v['friend_id']; ?>" />
                                <input type= "submit" class="btn btn-success btn-xs" value="確認"  name='fri_check'/>
                                <input type= "submit" class="btn btn-default btn-xs" value="取消"  name='no_check' />
                            </p>

                            <p id="index_info_time">
                                <?php echo $arrys_msg_v_v['keyin_cdate'] ?>
                            </p>
                        </form>
                    </figure>




                    <?php }elseif($arrys_msg_v_k == 'request_book'){?>

                    <figure id="index_info">
                            <a href="#">
                                <img id="index_info_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
                            </a>

                            <p id="index_info_name">
                            <?php echo $arrys_msg_v_v['content']; ?>

                               <br><br>
                                <input type ="hidden"      id="get_request_id"    value= <?php echo $arrys_msg_v_v['request_id'] ?> >
                                <input type="submit"       class="btn btn-success btn-xs book_rev" value="進行推薦"></input>
                                <input type="submit"       class="btn btn-default btn-xs" name = "close_rev" value="取消" onclick="location.href='mssr_forum_people_book_db.php?close_rev=取消&request_id=<?php echo $arrys_msg_v_v['request_id'] ?>'"></input>

                            </p>

                            <p id="index_info_time">
                                 <?php echo $arrys_msg_v_v['keyin_cdate'] ?>
                            </p>
                  </figure>


                    <?php }elseif($arrys_msg_v_k == 'request_join_forum'){ ?>
                    <figure id="index_info">
                            <a href="#">
                                <img id="index_info_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
                            </a>

                            <p id="index_info_name">
                            <?php echo $arrys_msg_v_v['content'];
                                $rs_request_id   = $arrys_msg_v_v['request_id'];
                                $rs_user_id      = $arrys_msg_v_v['request_to'];
                                $rs_forum_id     = $arrys_msg_v_v['fourm_id'];
                                $rs_request_from = $arrys_msg_v_v['request_from'];
                            ?>

                               <br><br>
                                <input type ="hidden"      id="get_request_id"    value= <?php echo $arrys_msg_v_v['request_id'] ?> >
                                <input type="submit"       class="btn btn-success btn-xs introPop" value="加入"></input>
                                <input type="submit"       class="btn btn-default btn-xs" name = "close_rev" value="取消" onclick="location.href='mssr_forum_people_book_db.php?close_rev=取消&request_id=<?php echo $arrys_msg_v_v['request_id'] ?>'"></input>

                            </p>

                            <p id="index_info_time">
                                 <?php echo $arrys_msg_v_v['keyin_cdate'] ?>
                            </p>
                  </figure>


                    <?php }elseif($arrys_msg_v_k == 'request_article'){
                                $article_id = $arrys_msg_v_v['article_id'];
                    ?>
                    <figure id="index_info">
                            <a href="#">
                                <img id="index_info_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
                            </a>

                            <p id="index_info_name">
                            <?php echo $arrys_msg_v_v['content']; ?>
                            <?php
                                $sql="
                                    select article_id from  mssr_article_forum_rev where article_id = $article_id
                                ";
                                $arrys_article_id=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                            ?>



                               <br><br>
                            <?php if(!empty($arrys_article_id)){ ?>
                                <input type="submit"       class="btn btn-success btn-xs" value="進行聊書" onclick="location.href='mssr_forum_group_reply.php?article_id=<?php echo $arrys_msg_v_v['article_id']; ?>'"/>
                            <?php } else {?>
                                <input type="submit"       class="btn btn-success btn-xs" value="進行聊書" onclick="location.href='mssr_forum_book_reply.php?article_id=<?php echo $arrys_msg_v_v['article_id']; ?>'"/>
                            <?php }; ?>

                                <input type="submit"       class="btn btn-default btn-xs" name = "close_rev" value="取消" onclick="location.href='mssr_forum_people_book_db.php?close_rev=取消&request_id=<?php echo $arrys_msg_v_v['request_id'] ?>'"/>

                            </p>

                            <p id="index_info_time">
                                 <?php echo $arrys_msg_v_v['keyin_cdate'] ?>
                            </p>
                  </figure>


                    <?php }elseif($arrys_msg_v_k == 'request_add_forum'){?>
                    <figure id="index_info">
                            <a href="#">
                                <img id="index_info_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
                            </a>
                            <form action ='mssr_forum_addForumDb.php'>
                            <p id="index_info_name">

                            <?php echo $arrys_msg_v_v['content'];
                                $rs_request_id   = $arrys_msg_v_v['request_id'];
                                $rs_user_id      = $arrys_msg_v_v['request_to'];
                                $rs_forum_id     = $arrys_msg_v_v['fourm_id'];
                                $rs_request_from = $arrys_msg_v_v['request_from'];

                            ?>
<input type="hidden"   name="rs_request_id" value="<?php echo $rs_request_id; ?>">
<input type="hidden"   name="rs_user_id"  value="<?php echo $rs_user_id; ?>" >
<input type="hidden"   name="rs_forum_id"  value="<?php echo $rs_forum_id; ?>" >
<input type="hidden"   name="rs_request_from"  value="<?php echo $rs_request_from; ?>" >
                               <br><br>
                                <input type="submit"       class="btn btn-success btn-xs" value="聯署" onclick="location.href='mssr_forum_addForumDb.php?'"/>
                                <!-- <input type="submit"       class="btn btn-default btn-xs" name = "close_rev" value="取消" onclick="location.href='mssr_forum_people_book_db.php?close_rev=取消&request_id=<?php echo $arrys_msg_v_v['request_id'] ?>'"/> -->

                            </p>

                            <p id="index_info_time">
                                 <?php echo $arrys_msg_v_v['keyin_cdate'] ?>
                            </p>
                             </form>
                  </figure>
                   <?php }elseif($arrys_msg_v_k == 'request_add_forum'){ ?>
                            <figure id="index_info">
                            <a href="#">
                                <img id="index_info_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
                            </a>

                            <p id="index_info_name">
                            <?php echo $arrys_msg_v_v['content'];
                                $rs_request_id   = $arrys_msg_v_v['request_id'];
                                $rs_user_id      = $arrys_msg_v_v['request_to'];
                                $rs_forum_id     = $arrys_msg_v_v['fourm_id'];
                                $rs_request_from = $arrys_msg_v_v['request_from'];
                            ?>

                               <br><br>
                                <input type ="hidden"      id="get_request_id"    value= <?php echo $arrys_msg_v_v['request_id'] ?> >
                                <input type="submit"       class="btn btn-success btn-xs introPop" value="加入"></input>
                                <input type="submit"       class="btn btn-default btn-xs" name = "close_rev" value="取消" onclick="location.href='mssr_forum_people_book_db.php?close_rev=取消&request_id=<?php echo $arrys_msg_v_v['request_id'] ?>'"></input>

                            </p>

                            <p id="index_info_time">
                                 <?php echo $arrys_msg_v_v['keyin_cdate'] ?>
                            </p>
                  </figure>


                        <?php } elseif($arrys_msg_v_k == 'request_group'){ ?>

                       <figure id="index_info">
                            <a href="#">
                                <img id="index_info_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
                            </a>

                            <p id="index_info_name">
                            <?php echo $arrys_msg_v_v['content'];
                                $rs_forum_id     = $arrys_msg_v_v['forum_id'];
                                $rs_user_id      = $arrys_msg_v_v['user_id'];

                            ?>

                               <br><br>
                                <input type="button"       class="btn btn-success btn-xs"  value="同意"   onclick="location.href='mssr_forum_group_member_check_A.php?forum_id=<?php echo $rs_forum_id  ?>&user_id=<?php echo $rs_user_id;?>&action_type=permit'"></input>
                                <input type="button"       class="btn btn-default btn-xs"  value="不同意" onclick="location.href='mssr_forum_group_member_check_A.php?forum_id=<?php echo $rs_forum_id  ?>&user_id=<?php echo $rs_user_id;?>&action_type=reject'"></input>

                            </p>

                            <p id="index_info_time">
                                 <?php echo $arrys_msg_v_v['keyin_cdate'] ?>
                            </p>
                    </figure>


                        <?php } ?>









    <?php }
}?>






<!-- <?php foreach($arrys_friend_check as $v){ ?>
     <figure id="index_info">


                    <a href="#">
                        <img id="index_info_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
                    </a>

                  <form action ='mssr_forum_people_friend_request_Act.php'>
                    <p id="index_info_name">
                    <?php echo $v['name'] ?> 對你提出好友申請!

                       <br><br>
                        <input type= "hidden" name ="user_id"  value = "<?php echo $v['user_id'];  ?>" />
                        <input type= "hidden" name ="sess_uid" value = "<?php echo $v['friend_id']; ?>" />
						<input type= "submit" class="btn btn-success btn-xs" value="確認"  name='fri_check'/>
                        <input type= "submit" class="btn btn-default btn-xs" value="取消"  name='no_check' />

                    </p>

                    <p id="index_info_time">
                         <?php echo $v['keyin_cdate'] ?>
                    </p>
                  </form>
     </figure>
<?php } ?> -->

        <!-- <figure id="index_info">
                    <a href="#">
                        <img id="index_info_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
                    </a>

                    <p id="index_info_name">
                       李明日
                       ，邀請你加入「XXX」書籍中的「XXX」討論文共同聊書
                       <br><br>
						<input type="submit" class="btn  btn-success btn-xs" value="進行聊書"></input>
                        <input type="submit" class="btn btn-default btn-xs" value="取消"></input>

                    </p>

                    <p id="index_info_time">
                        2014-10-20 10:23:41
                    </p>
     </figure>
     <figure id="index_info">
                    <a href="#">
                        <img id="index_info_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
                    </a>

                    <p id="index_info_name">
                       李明日
                       ，邀請你加入「XXX」聊書小組
                       <br><br>
						<input type="submit" class="btn  btn-success btn-xs" value="加入"></input>
                        <input type="submit" class="btn btn-default btn-xs" value="取消"></input>

                    </p>

                    <p id="index_info_time">
                        2014-10-20 10:23:41
                    </p>
     </figure>
     <figure id="index_info">
                    <a href="#">
                        <img id="index_info_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
                    </a>

                    <p id="index_info_name">
                       李明日
                       ，邀請你建立聊書小組
                       <br><br>
						<input type="submit" class="btn  btn-success btn-xs" value="進行聊書"></input>
                        <input type="submit" class="btn btn-default btn-xs" value="取消"></input>

                    </p>

                    <p id="index_info_time">
                        2014-10-20 10:23:41
                    </p>
     </figure> -->


<!-- <?php foreach($arrys_book_check as $k =>$v){?>

     <figure id="index_info">
                    <a href="#">
                        <img id="index_info_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
                    </a>

                    <p id="index_info_name">
                    <?php echo $v['name'] ?> ，請求你推薦最近讀過的好書

                       <br><br>
                        <input type ="hidden"      id="get_request_id"    value= <?php echo $v['request_id'] ?> >
						<input type="submit"       class="btn btn-success btn-xs book_rev" value="進行推薦"></input>
                        <input type="submit"       class="btn btn-default btn-xs" name = "close_rev" value="取消" onclick="location.href='mssr_forum_people_book_db.php?close_rev=取消&request_id=<?php echo $v['request_id'] ?>'"></input>

                    </p>

                    <p id="index_info_time">
                         <?php echo $v['keyin_cdate'] ?>
                    </p>
     </figure>
<?php } ?> -->


    </div>


<!-- 推薦書籍跳窗 -->

<div id="open_book_rev" style="display:none"  >
	<form action="mssr_forum_people_book_db.php" method="GET"  onsubmit = 'return check()' >


		<input type="image" id="input_leave" onclick =" window.location.reload()" name="input_leave" src="image/xlogo.png" alt="" width="30" height="30" />

<div id = "tablelist">

      <div id = "inputBook" style="padding:20px;">

    	<p><span style="font-size:20px;">輸入推薦書籍ISBN:</span><input id ="request_book_id"  type="text" size="30" maxlength="30"/></p>




        <p><span style="font-size:20px;">或選擇你要的書籍:</span>
        <select class = "chzn-select">
        <option value='' id ='select_books'  class ='select_books'  name ='select_books'>請選擇</option>
        <?php foreach($lookBook as $v){?>



           <?php $book_name = get_book_info($conn='',$v['book_sid'],array('book_name'),$arry_conn_mssr); ?>
           <option value="<?php echo $v['book_sid'] ?>"  id ='select_books'  class ='select_books'  name ='select_books'><?php echo  $book_name[0]['book_name']; ?></option>





        <?php } ?>
        </select>
        </p>

               <figure class="col-md-12 col-md-offset-5">

                   <input style = "margin:5px" class="btn btn-success" id="decide" onclick="search_book()" type ="button" value="確定"></input>

               </figure>
       </div>
<!-- 書籍 -->

<div id = "selectBook"  style="display:none">

    <h3>你要選擇哪本書</h3>




        <div class="insetBook" style="height:50px">
        <select   name='select_book' id='select_book' class ='select_book'>
        </select>
        </div>


          <div  style="clear:left; width:150px; margin-top:20px;"></div>
          <textarea class="form-control" rows="3" name = "rev_tex" id ="rev_tex" placeholder="輸入推薦內容" ></textarea>
                <figure class="col-md-12 col-md-offset-5">
                   <input type ="hidden" name = "request_id"  id = "request_id" ></input>
                   <input style = "margin:5px" class="btn btn-success" id="decide" type ="submit" value="確定"></input>
               </figure>




</div>
</form>



</div>

</div>
<!--=================================彈出其他好友==================================-->
<div class="elseFriends">
    <input type="button" id="close" class = "close" value = "關閉">
    <div class ="insetFriends"></div>
</div>
<!--=================================彈出自介==================================-->
<div class="intro">
<form action='mssr_forum_join_forumDb.php'>
    輸入自我介紹:<br/><textarea  name="introMy" rows="4" cols="60"></textarea>
    <input type="hidden"   name="rs_request_id" value="<?php echo $rs_request_id; ?>">
    <input type="hidden"   name="rs_user_id"  value="<?php echo $rs_user_id; ?>" >
    <input type="hidden"   name="rs_forum_id"  value="<?php echo $rs_forum_id; ?>" >
     <input type="hidden"   name="rs_request_from"  value="<?php echo $rs_request_from; ?>" ><br/>
    <input type="submit"  class = "btn btn-success btn-xs" value = "確定" >
    <input type="button"  class = "btn btn-default btn-xs introClose" value = "關閉">
</form>
</div>











<!--========================排行=====================================-->


  <?php require_once('mssr_forum_right_people.php');  ?>









</body>

</html>

<script>

function logFuc(process_url,action_code,action_from,user_id_1,user_id_2,book_sid_1,book_sid_2,forum_id_1,forum_id_2,article_id,reply_id,go_url){

            var process_url     = process_url;
            var action_code     = action_code;
            var action_from     = action_from;

            var user_id_1       = user_id_1;
            var user_id_2       = user_id_2;
            var book_sid_1      = book_sid_1;
            var book_sid_2      = book_sid_2;
            var forum_id_1      = forum_id_1;
            var forum_id_2      = forum_id_2;

            var article_id      = article_id;
            var reply_id        = reply_id;
            var go_url          = go_url;

            add_action_forum_log(
                process_url,
                action_code,
                action_from,
                user_id_1,
                user_id_2,
                book_sid_1,
                book_sid_2,
                forum_id_1,
                forum_id_2,
                article_id,
                reply_id,
                go_url
            );
        }

</script>