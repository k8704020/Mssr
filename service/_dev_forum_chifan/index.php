<?php
//-------------------------------------------------------
//明日星球, 聊書
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",2).'config/config.php');

        //外掛頁面檔
        require_once(str_repeat("../",0).'inc/require_page/code.php');

        //外掛過濾檔
        require_once(str_repeat("../",0).'filter_func.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code'
       	);
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

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
                //$_SESSION["uid"]=5030;
                //$_SESSION["permission"]='41488201409111119159';
                //$_SESSION["name"]='趙明日';
                //$_SESSION["class"][0][0]='i_s';
                //$_SESSION["class"][0][1]='test_2014_2_1_1';

					$user_id=(int)$_SESSION["uid"];
                    if($user_id===0){
                        $msg="你尚未登入!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                location.href='/ac/index.php';
                            </script>
                        ";
                        die($jscript_back);
                    }

				 //-----------------------------------------------
				 //小組權限(session)
				 //-----------------------------------------------
				 $sql="
						SELECT
							`forum_id`
						FROM
							`mssr_user_forum`
						WHERE 1=1
							AND `user_id` ={$user_id	}
							AND	`user_state` LIKE '%啟用%'
							AND `user_type`  LIKE '%一般版主%'
				 ";
				 $arrys_result_forum_manager=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				$_SESSION['forum_id_manager']=$arrys_result_forum_manager;

				//-----------------------------------------------
	        	//mssr_user_forum 撈取
	        	//-----------------------------------------------

                    $arrys_user_forum=array();
                    //$sql="
                    //    SELECT
                    //        `mssr`.`mssr_user_forum`.`forum_id`,
                    //        `mssr`.`mssr_user_forum`.`user_id`,
                    //        `mssr`.`mssr_user_forum`.`keyin_cdate`,
                    //
                    //        `mssr`.`mssr_forum`.`forum_name`,
                    //
                    //        `user`.`member`.`name`
                    //    FROM `mssr`.`mssr_user_forum`
                    //        INNER JOIN `mssr`.`mssr_forum` ON
                    //        `mssr`.`mssr_user_forum`.`forum_id`=`mssr`.`mssr_forum`.`forum_id`
                    //
                    //        INNER JOIN `user`.`member` ON
                    //        `mssr`.`mssr_user_forum`.`user_id`=`user`.`member`.`uid`
                    //    WHERE 1=1
                    //        AND `mssr`.`mssr_user_forum`.`user_state` IN ('申請中')
                    //        AND `mssr`.`mssr_user_forum`.`forum_id` IN (
                    //            SELECT `forum_id`
                    //            FROM `mssr_user_forum`
                    //            WHERE 1=1
                    //                AND `user_id`    = {$user_id}
                    //                AND (
                    //                    `user_type` REGEXP '一般版主'
                    //                        OR
                    //                    `user_type` REGEXP '高級版主'
                    //                )
                    //        )
                    //";
                    //$arrys_user_forum=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				//-----------------------------------------------
	        	//mssr_forum_friend 撈取
	        	//-----------------------------------------------

                    $arrys_friend=array();
                    ////是否成為朋友
                    //$sql="
                    //        SELECT
                    //            *
                    //        FROM `mssr_forum_friend`
                    //        WHERE 1=1
                    //            AND (
                    //                `user_id`  ={$user_id}
                    //                    OR
                    //                `friend_id`={$user_id}
                    //            )
                    //            AND `friend_state` IN ('成功','失敗')
                    //            AND DATE(`keyin_cdate`) >= CURDATE() - INTERVAL 1 DAY
                    //    UNION
                    //        SELECT
                    //            *
                    //        FROM `mssr_forum_friend`
                    //        WHERE 1=1
                    //            AND `friend_id`={$user_id}
                    //            AND `friend_state` IN ('確認中')
                    //";
                    //$arrys_friend=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				//-----------------------------------------------
	        	//SQL-userinfo(學生|老師資訊-姓名)
	        	//-----------------------------------------------
					$sql="
						SELECT `name`
						FROM `member`
						WHERE `uid` = $user_id
					";
					$arrys_result_userinfo=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);


				//-----------------------------------------------
	        	//SQL-usergrade(用class_code找學生年級資訊)
	        	//-----------------------------------------------

					$class_code = trim($_SESSION['class'][0][1]);

					$sql="
						SELECT
							`grade`,
                            `classroom`,
                            `class_code`,
                            `school_code`
						FROM `class`
                            INNER JOIN `semester` ON
                            `class`.`semester_code`=`semester`.`semester_code`
						WHERE `class_code` = '$class_code'
					";
					$arrys_result_usergrade=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);

				//-----------------------------------------------
	        	//SQL-arrys_result_user_school(用class_code找學生學校資訊)
	        	//-----------------------------------------------

					$user_school=trim($arrys_result_usergrade[0]['school_code']);

					$sql="
						SELECT
							`school_name`, `region_name`
						FROM `school`
						WHERE `school_code` = '{$user_school}'
					";
					$arrys_result_user_school=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);

                //-----------------------------------------------
	        	//撈取喜愛的書籍
	        	//-----------------------------------------------

                    $arry_book_favorite=array();
                    $book_favorite     ='';
                    $sql="
                        SELECT
                            `book_sid`
                        FROM `mssr_book_favorite`
                        WHERE 1=1
                            AND `user_id`={$user_id}
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    if(!empty($arrys_result)){
                        foreach($arrys_result as $arry_result){
                            $rs_book_sid=trim($arry_result['book_sid']);
                            $arry_book_favorite[]=$rs_book_sid;
                        }
                        $book_favorite=implode($arry_book_favorite,"','");
                    }

				//-----------------------------------------------
	        	//撈取已加入的討論區
	        	//-----------------------------------------------

                    $arry_forum_favorite=array();
                    $forum_favorite     ='';
                    $sql="
                        SELECT
                            `forum_id`
                        FROM `mssr_user_forum`
                        WHERE 1=1
                            AND `user_id`={$user_id}
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    if(!empty($arrys_result)){
                        foreach($arrys_result as $arry_result){
                            $rs_forum_id=(int)$arry_result['forum_id'];
                            $arry_forum_favorite[]=$rs_forum_id;
                        }
                        $forum_favorite=implode($arry_forum_favorite,",");
                    }

				//-----------------------------------------------
	        	//撈取我的朋友
	        	//-----------------------------------------------

                    $arry_friend=array();
                    $friend     ='';
                    $sql="
                        SELECT
							`user_id`,
                            `friend_id`
                        FROM `mssr_forum_friend`
                        WHERE 1=1
                            AND (
								`user_id`   ={$user_id}
									OR
								`friend_id` ={$user_id}
							)
							AND `friend_state`='成功'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    if(!empty($arrys_result)){
                        foreach($arrys_result as $arry_result){
                            $rs_user_id=(int)$arry_result['user_id'];
							$rs_friend_id=(int)$arry_result['friend_id'];

							if((!in_array($rs_user_id,$arry_friend))&&($rs_user_id!==$user_id)){
								$arry_friend[]=$rs_user_id;
							}
							if((!in_array($rs_friend_id,$arry_friend))&&($rs_friend_id!==$user_id)){
								$arry_friend[]=$rs_friend_id;
							}
                        }
                        $friend=implode($arry_friend,",");
                    }

				//-----------------------------------------------
	        	//SQL-index_info(主頁面)(分頁)
	        	//-----------------------------------------------

					$lv=0;
					if($book_favorite!==''){
						$lv=$lv+1;
					}
					if($forum_favorite!==''){
						$lv=$lv+3;
					}
					if($friend!==''){
						$lv=$lv+5;
					}

					$query_sql ="";
					$query_sql.="
						SELECT
							*
						FROM (
					";

					switch($lv){

						//無任何追蹤
						case 0:
                        	$arrys_result_index_info=array();
                        	$numrow_index_info=count($arrys_result_index_info);
						break;

						//追蹤書
						case 1:
                            $query_sql.="
                                SELECT
                                    `mssr_forum_article`.`user_id`,
                                    `mssr_forum_article`.`article_id`,
                                    `mssr_forum_article`.`article_content`,
									`mssr_forum_article`.`article_state`,
                                    `mssr_forum_article`.`keyin_cdate`,
                                    `mssr_article_book_rev`.`book_sid`,
                                    'book_article' AS article_from
                                FROM
                                    `mssr_forum_article`
                                INNER JOIN
                                    `mssr_article_book_rev`
                                ON
                                    `mssr_forum_article`.`article_id`=`mssr_article_book_rev`.`article_id`
                                WHERE 1=1
                                    AND `mssr_article_book_rev`.`book_sid` IN ('{$book_favorite}')
									AND `mssr_forum_article`.`article_state` LIKE '%正常%'

								UNION

									SELECT
										`request_to` AS `user_id`,
										0  AS `article_id`,
										'' AS`article_content`,
										'' AS `article_state`,
										`mssr_user_request`.`keyin_cdate`,
										`mssr_user_request_book_rev`.`book_sid`,
										 'request_book' AS article_from
									FROM `mssr_user_request_book_rev`
                                        INNER JOIN `mssr_user_request` ON
                                        `mssr_user_request_book_rev`.request_id=`mssr_user_request`.request_id
                                    WHERE 1=1
                                        AND (
                                            `mssr_user_request`.`request_from`={$user_id}
                                        )
                                        AND `mssr_user_request_book_rev`.`book_sid`<>''
                            ";
						break;

						//追蹤群
						case 3:
                            $query_sql.="
                                SELECT
                                    `mssr_forum_article`.`user_id`,
                                    `mssr_forum_article`.`article_id`,
                                    `mssr_forum_article`.`article_content`,
									`mssr_forum_article`.`article_state`,
                                    `mssr_forum_article`.`keyin_cdate`,
                                    `mssr_article_forum_rev`.`forum_id` AS 'book_sid',
                                     'forum_article' AS article_from
                                FROM
                                    `mssr_forum_article`
                                INNER JOIN
                                    `mssr_article_forum_rev`
                                ON
                                    `mssr_forum_article`.`article_id`=`mssr_article_forum_rev`.`article_id`
                                WHERE 1=1
                                    AND `mssr_article_forum_rev`.`forum_id` IN ({$forum_favorite})
									AND `mssr_forum_article`.`article_state` LIKE '%正常%'

								UNION

									SELECT
										`request_to` AS `user_id`,
										0  AS `article_id`,
										'' AS`article_content`,
										'' AS `article_state`,
										`mssr_user_request`.`keyin_cdate`,
										`mssr_user_request_book_rev`.`book_sid`,
										 'request_book' AS article_from
									FROM `mssr_user_request_book_rev`
                                        INNER JOIN `mssr_user_request` ON
                                        `mssr_user_request_book_rev`.request_id=`mssr_user_request`.request_id
                                    WHERE 1=1
                                        AND (
                                            `mssr_user_request`.`request_from`={$user_id}
                                        )
                                        AND `mssr_user_request_book_rev`.`book_sid`<>''
                            ";
						break;

						//追蹤人
						case 5:
                            $query_sql.="
									SELECT
										`mssr_forum_article`.`user_id`,
										`mssr_forum_article`.`article_id`,
										`mssr_forum_article`.`article_content`,
										`mssr_forum_article`.`article_state`,
										`mssr_forum_article`.`keyin_cdate`,
										`mssr_article_book_rev`.`book_sid`,
										'book_article' AS article_from
									FROM
										`mssr_forum_article`
									INNER JOIN
										`mssr_article_book_rev`
									ON
										`mssr_forum_article`.`article_id`=`mssr_article_book_rev`.`article_id`
									WHERE 1=1
										AND `mssr_forum_article`.`user_id` IN ({$friend})
										AND `mssr_forum_article`.`article_state` LIKE '%正常%'

								UNION

									SELECT
										`mssr_forum_article`.`user_id`,
										`mssr_forum_article`.`article_id`,
										`mssr_forum_article`.`article_content`,
										`mssr_forum_article`.`article_state`,
										`mssr_forum_article`.`keyin_cdate`,
										`mssr_article_forum_rev`.`forum_id` AS 'book_sid',
										 'forum_article' AS article_from
									FROM
										`mssr_forum_article`
									INNER JOIN
										`mssr_article_forum_rev`
									ON
										`mssr_forum_article`.`article_id`=`mssr_article_forum_rev`.`article_id`
									WHERE 1=1
										AND `mssr_forum_article`.`user_id` IN ({$friend})
										AND `mssr_forum_article`.`article_state` LIKE '%正常%'

								UNION

									SELECT
										`request_to` AS `user_id`,
										0  AS `article_id`,
										'' AS`article_content`,
										'' AS `article_state`,
										`mssr_user_request`.`keyin_cdate`,
										`mssr_user_request_book_rev`.`book_sid`,
										 'request_book' AS article_from
									FROM `mssr_user_request_book_rev`
                                        INNER JOIN `mssr_user_request` ON
                                        `mssr_user_request_book_rev`.request_id=`mssr_user_request`.request_id
                                    WHERE 1=1
                                        AND (
                                            `mssr_user_request`.`request_from`={$user_id}
                                        )
                                        AND `mssr_user_request_book_rev`.`book_sid`<>''
                            ";
						break;

						//追蹤書、群
						case 4:
                            $query_sql.="
									SELECT
										`mssr_forum_article`.`user_id`,
										`mssr_forum_article`.`article_id`,
										`mssr_forum_article`.`article_content`,
										`mssr_forum_article`.`article_state`,
										`mssr_forum_article`.`keyin_cdate`,
										`mssr_article_book_rev`.`book_sid`,
										'book_article' AS article_from
									FROM
										`mssr_forum_article`
									INNER JOIN
										`mssr_article_book_rev`
									ON
										`mssr_forum_article`.`article_id`=`mssr_article_book_rev`.`article_id`
									WHERE 1=1
										AND `mssr_article_book_rev`.`book_sid` IN ('{$book_favorite}')
										AND `mssr_forum_article`.`article_state` LIKE '%正常%'

								UNION

									SELECT
										`mssr_forum_article`.`user_id`,
										`mssr_forum_article`.`article_id`,
										`mssr_forum_article`.`article_content`,
										`mssr_forum_article`.`article_state`,
										`mssr_forum_article`.`keyin_cdate`,
										`mssr_article_forum_rev`.`forum_id` AS 'book_sid',
										 'forum_article' AS article_from
									FROM
										`mssr_forum_article`
									INNER JOIN
										`mssr_article_forum_rev`
									ON
										`mssr_forum_article`.`article_id`=`mssr_article_forum_rev`.`article_id`
									WHERE 1=1
										AND `mssr_article_forum_rev`.`forum_id` IN ({$forum_favorite})
										AND `mssr_forum_article`.`article_state` LIKE '%正常%'

								UNION

									SELECT
										`request_to` AS `user_id`,
										0  AS `article_id`,
										'' AS`article_content`,
										'' AS `article_state`,
										`mssr_user_request`.`keyin_cdate`,
										`mssr_user_request_book_rev`.`book_sid`,
										 'request_book' AS article_from
									FROM `mssr_user_request_book_rev`
                                        INNER JOIN `mssr_user_request` ON
                                        `mssr_user_request_book_rev`.request_id=`mssr_user_request`.request_id
                                    WHERE 1=1
                                        AND (
                                            `mssr_user_request`.`request_from`={$user_id}
                                        )
                                        AND `mssr_user_request_book_rev`.`book_sid`<>''
                            ";
						break;

						//追蹤群、人
						case 8:
                            $query_sql.="
									SELECT
										`mssr_forum_article`.`user_id`,
										`mssr_forum_article`.`article_id`,
										`mssr_forum_article`.`article_content`,
										`mssr_forum_article`.`article_state`,
										`mssr_forum_article`.`keyin_cdate`,
										`mssr_article_forum_rev`.`forum_id` AS 'book_sid',
										 'forum_article' AS article_from
									FROM
										`mssr_forum_article`
									INNER JOIN
										`mssr_article_forum_rev`
									ON
										`mssr_forum_article`.`article_id`=`mssr_article_forum_rev`.`article_id`
									WHERE 1=1
										AND `mssr_article_forum_rev`.`forum_id` IN ({$forum_favorite})
										AND `mssr_forum_article`.`article_state` LIKE '%正常%'

								UNION

									SELECT
										`mssr_forum_article`.`user_id`,
										`mssr_forum_article`.`article_id`,
										`mssr_forum_article`.`article_content`,
										`mssr_forum_article`.`article_state`,
										`mssr_forum_article`.`keyin_cdate`,
										`mssr_article_book_rev`.`book_sid`,
										'book_article' AS article_from
									FROM
										`mssr_forum_article`
									INNER JOIN
										`mssr_article_book_rev`
									ON
										`mssr_forum_article`.`article_id`=`mssr_article_book_rev`.`article_id`
									WHERE 1=1
										AND `mssr_forum_article`.`user_id` IN ({$friend})
										AND `mssr_forum_article`.`article_state` LIKE '%正常%'

								UNION

									SELECT
										`mssr_forum_article`.`user_id`,
										`mssr_forum_article`.`article_id`,
										`mssr_forum_article`.`article_content`,
										`mssr_forum_article`.`article_state`,
										`mssr_forum_article`.`keyin_cdate`,
										`mssr_article_forum_rev`.`forum_id` AS 'book_sid',
										 'forum_article' AS article_from
									FROM
										`mssr_forum_article`
									INNER JOIN
										`mssr_article_forum_rev`
									ON
										`mssr_forum_article`.`article_id`=`mssr_article_forum_rev`.`article_id`
									WHERE 1=1
										AND `mssr_forum_article`.`user_id` IN ({$friend})
										AND `mssr_forum_article`.`article_state` LIKE '%正常%'

								UNION

									SELECT
										`request_to` AS `user_id`,
										0  AS `article_id`,
										'' AS`article_content`,
										'' AS `article_state`,
										`mssr_user_request`.`keyin_cdate`,
										`mssr_user_request_book_rev`.`book_sid`,
										 'request_book' AS article_from
									FROM `mssr_user_request_book_rev`
                                        INNER JOIN `mssr_user_request` ON
                                        `mssr_user_request_book_rev`.request_id=`mssr_user_request`.request_id
                                    WHERE 1=1
                                        AND (
                                            `mssr_user_request`.`request_from`={$user_id}
                                        )
                                        AND `mssr_user_request_book_rev`.`book_sid`<>''
                            ";
						break;

						//追蹤書、人
						case 6:
                            $query_sql.="
									SELECT
										`mssr_forum_article`.`user_id`,
										`mssr_forum_article`.`article_id`,
										`mssr_forum_article`.`article_content`,
										`mssr_forum_article`.`article_state`,
										`mssr_forum_article`.`keyin_cdate`,
										`mssr_article_book_rev`.`book_sid`,
										'book_article' AS article_from
									FROM
										`mssr_forum_article`
									INNER JOIN
										`mssr_article_book_rev`
									ON
										`mssr_forum_article`.`article_id`=`mssr_article_book_rev`.`article_id`
									WHERE 1=1
										AND `mssr_article_book_rev`.`book_sid` IN ('{$book_favorite}')
										AND `mssr_forum_article`.`article_state` LIKE '%正常%'

								UNION

									SELECT
										`mssr_forum_article`.`user_id`,
										`mssr_forum_article`.`article_id`,
										`mssr_forum_article`.`article_content`,
										`mssr_forum_article`.`article_state`,
										`mssr_forum_article`.`keyin_cdate`,
										`mssr_article_book_rev`.`book_sid`,
										'book_article' AS article_from
									FROM
										`mssr_forum_article`
									INNER JOIN
										`mssr_article_book_rev`
									ON
										`mssr_forum_article`.`article_id`=`mssr_article_book_rev`.`article_id`
									WHERE 1=1
										AND `mssr_forum_article`.`user_id` IN ({$friend})
										AND `mssr_forum_article`.`article_state` LIKE '%正常%'

								UNION

									SELECT
										`mssr_forum_article`.`user_id`,
										`mssr_forum_article`.`article_id`,
										`mssr_forum_article`.`article_content`,
										`mssr_forum_article`.`article_state`,
										`mssr_forum_article`.`keyin_cdate`,
										`mssr_article_forum_rev`.`forum_id` AS 'book_sid',
										 'forum_article' AS article_from
									FROM
										`mssr_forum_article`
									INNER JOIN
										`mssr_article_forum_rev`
									ON
										`mssr_forum_article`.`article_id`=`mssr_article_forum_rev`.`article_id`
									WHERE 1=1
										AND `mssr_forum_article`.`user_id` IN ({$friend})
										AND `mssr_forum_article`.`article_state` LIKE '%正常%'

								UNION

									SELECT
										`request_to` AS `user_id`,
										0  AS `article_id`,
										'' AS`article_content`,
										'' AS `article_state`,
										`mssr_user_request`.`keyin_cdate`,
										`mssr_user_request_book_rev`.`book_sid`,
										 'request_book' AS article_from
									FROM `mssr_user_request_book_rev`
                                        INNER JOIN `mssr_user_request` ON
                                        `mssr_user_request_book_rev`.request_id=`mssr_user_request`.request_id
                                    WHERE 1=1
                                        AND (
                                            `mssr_user_request`.`request_from`={$user_id}
                                        )
                                        AND `mssr_user_request_book_rev`.`book_sid`<>''
                            ";
						break;

						//追蹤書、群、人
						case 9:
                            $query_sql.="
									SELECT
										`mssr_forum_article`.`user_id`,
										`mssr_forum_article`.`article_id`,
										`mssr_forum_article`.`article_content`,
										`mssr_forum_article`.`article_state`,
										`mssr_forum_article`.`keyin_cdate`,
										`mssr_article_book_rev`.`book_sid`,
										'book_article' AS article_from
									FROM
										`mssr_forum_article`
									INNER JOIN
										`mssr_article_book_rev`
									ON
										`mssr_forum_article`.`article_id`=`mssr_article_book_rev`.`article_id`
									WHERE 1=1
										AND `mssr_article_book_rev`.`book_sid` IN ('{$book_favorite}')
										AND `mssr_forum_article`.`article_state` LIKE '%正常%'

								UNION

									SELECT
										`mssr_forum_article`.`user_id`,
										`mssr_forum_article`.`article_id`,
										`mssr_forum_article`.`article_content`,
										`mssr_forum_article`.`article_state`,
										`mssr_forum_article`.`keyin_cdate`,
										`mssr_article_forum_rev`.`forum_id` AS 'book_sid',
										 'forum_article' AS article_from
									FROM
										`mssr_forum_article`
									INNER JOIN
										`mssr_article_forum_rev`
									ON
										`mssr_forum_article`.`article_id`=`mssr_article_forum_rev`.`article_id`
									WHERE 1=1
										AND `mssr_article_forum_rev`.`forum_id` IN ({$forum_favorite})
										AND `mssr_forum_article`.`article_state` LIKE '%正常%'

								UNION

									SELECT
										`mssr_forum_article`.`user_id`,
										`mssr_forum_article`.`article_id`,
										`mssr_forum_article`.`article_content`,
										`mssr_forum_article`.`article_state`,
										`mssr_forum_article`.`keyin_cdate`,
										`mssr_article_book_rev`.`book_sid`,
										'book_article' AS article_from
									FROM
										`mssr_forum_article`
									INNER JOIN
										`mssr_article_book_rev`
									ON
										`mssr_forum_article`.`article_id`=`mssr_article_book_rev`.`article_id`
									WHERE 1=1
										AND `mssr_forum_article`.`user_id` IN ({$friend})
										AND `mssr_forum_article`.`article_state` LIKE '%正常%'

								UNION

									SELECT
										`mssr_forum_article`.`user_id`,
										`mssr_forum_article`.`article_id`,
										`mssr_forum_article`.`article_content`,
										`mssr_forum_article`.`article_state`,
										`mssr_forum_article`.`keyin_cdate`,
										`mssr_article_forum_rev`.`forum_id` AS 'book_sid',
										 'forum_article' AS article_from
									FROM
										`mssr_forum_article`
									INNER JOIN
										`mssr_article_forum_rev`
									ON
										`mssr_forum_article`.`article_id`=`mssr_article_forum_rev`.`article_id`
									WHERE 1=1
										AND `mssr_forum_article`.`user_id` IN ({$friend})
										AND `mssr_forum_article`.`article_state` LIKE '%正常%'

								UNION

									SELECT
										`request_to` AS `user_id`,
										0  AS `article_id`,
										'' AS`article_content`,
										'' AS `article_state`,
										`mssr_user_request`.`keyin_cdate`,
										`mssr_user_request_book_rev`.`book_sid`,
										 'request_book' AS article_from
									FROM `mssr_user_request_book_rev`
                                        INNER JOIN `mssr_user_request` ON
                                        `mssr_user_request_book_rev`.request_id=`mssr_user_request`.request_id
                                    WHERE 1=1
                                        AND (
                                            `mssr_user_request`.`request_from`={$user_id}
                                        )
                                        AND `mssr_user_request_book_rev`.`book_sid`<>''
                            ";
						break;
					}
					$query_sql.="
							) AS `tmp`
						WHERE 1=1

						ORDER BY
							`keyin_cdate` DESC
					";
					if($lv!==0){
						$arrys_result_index_info=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
						$numrow_index_info=count($arrys_result_index_info);
					}

				//-----------------------------------------------
	        	//SQL-arrys_result_top_book(書籍排行榜)
	        	//-----------------------------------------------

					$sql="
						SELECT
                            `mssr_article_book_rev`.`book_sid`
						FROM `mssr_article_book_rev`
                            INNER JOIN `mssr_forum_article` ON
                            `mssr_article_book_rev`.`article_id`=`mssr_forum_article`.`article_id`
						WHERE 1=1
					";
                    if($class_code!=='gcp_2014_2_5_2'&&$class_code!=='gcp_2014_2_5_5'){
                        $sql.="
                            AND (
                                `mssr`.`mssr_forum_article`.`user_id` IN (
                                    SELECT `user`.`student`.`uid`
                                    FROM `user`.`student`
                                    WHERE 1=1
                                        AND `user`.`student`.`class_code`=(
                                            SELECT `user`.`student`.`class_code`
                                            FROM `user`.`student`
                                            WHERE 1=1
                                                AND `user`.`student`.`uid`={$user_id}
                                                AND `user`.`student`.`start`<='{$curdate}'
                                                AND `user`.`student`.`end`  >='{$curdate}'
                                            GROUP BY `user`.`student`.`uid`, `user`.`student`.`class_code`
                                            LIMIT 1

                                            UNION

                                            SELECT `user`.`teacher`.`class_code`
                                            FROM `user`.`teacher`
                                            WHERE 1=1
                                                AND `user`.`teacher`.`uid`={$user_id}
                                                AND `user`.`teacher`.`start`<='{$curdate}'
                                                AND `user`.`teacher`.`end`  >='{$curdate}'
                                            GROUP BY `user`.`teacher`.`uid`, `user`.`teacher`.`class_code`
                                            LIMIT 1
                                        )
                                )
                                OR
                                `mssr`.`mssr_forum_article`.`user_id` IN ({$user_id})
                            )
                        ";
                    }else{
                        $sql.="
                            AND (
                                `mssr`.`mssr_forum_article`.`user_id` IN (
                                    SELECT `user`.`student`.`uid`
                                    FROM `user`.`student`
                                    WHERE 1=1
                                        AND (
                                            `user`.`student`.`class_code`=(
                                                SELECT `user`.`student`.`class_code`
                                                FROM `user`.`student`
                                                WHERE 1=1
                                                    AND `user`.`student`.`uid`={$user_id}
                                                    AND `user`.`student`.`start`<='{$curdate}'
                                                    AND `user`.`student`.`end`  >='{$curdate}'
                                                GROUP BY `user`.`student`.`uid`, `user`.`student`.`class_code`
                                                LIMIT 1

                                                UNION

                                                SELECT `user`.`teacher`.`class_code`
                                                FROM `user`.`teacher`
                                                WHERE 1=1
                                                    AND `user`.`teacher`.`uid`={$user_id}
                                                    AND `user`.`teacher`.`start`<='{$curdate}'
                                                    AND `user`.`teacher`.`end`  >='{$curdate}'
                                                GROUP BY `user`.`teacher`.`uid`, `user`.`teacher`.`class_code`
                                                LIMIT 1
                                            )
                                            OR
                                            `user`.`student`.`class_code` IN ('gcp_2014_2_5_2','gcp_2014_2_5_5')
                                        )

                                )
                                OR
                                `mssr`.`mssr_forum_article`.`user_id` IN ({$user_id})
                            )
                        ";
                    }
					$arrys_result_top_book=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$arry_book_sid_list=array();
					if(!empty($arrys_result_top_book)){
						$arry_list=array();
						foreach($arrys_result_top_book as  $arry_result_recommend){
							$rs_book_sid=trim($arry_result_recommend['book_sid']);
							if(!array_key_exists($rs_book_sid,$arry_book_sid_list)){
								$arry_book_sid_list[$rs_book_sid]=1;
							}else{
								$arry_book_sid_list[$rs_book_sid]=$arry_book_sid_list[$rs_book_sid]+1;
							}
						}
						//排序
						arsort($arry_book_sid_list);
						//篩選
						foreach($arry_book_sid_list as $book_sid=>$cno){
							if(count($arry_list)<5){
								$arry_list[]=trim($book_sid);
							}else{
								break;
							}
						}
					}

				//-----------------------------------------------
				//SQL-arrys_result_top_group(熱門聊書小組)
				//-----------------------------------------------

                    $curdate=date("Y-m-d");
					$sql="
						SELECT
							`mssr`.`mssr_forum`.`forum_id`
						FROM `mssr`.`mssr_article_forum_rev`
                            RIGHT JOIN `mssr`.`mssr_forum` ON
                            `mssr`.`mssr_article_forum_rev`.`forum_id`=`mssr`.`mssr_forum`.`forum_id`
						WHERE 1=1
					";
                    if($class_code!=='gcp_2014_2_5_2'&&$class_code!=='gcp_2014_2_5_5'){
                        $sql.="
                            AND (
                                `mssr`.`mssr_forum`.`create_by` IN (
                                    SELECT `user`.`student`.`uid`
                                    FROM `user`.`student`
                                    WHERE 1=1
                                        AND `user`.`student`.`class_code`=(
                                            SELECT `user`.`student`.`class_code`
                                            FROM `user`.`student`
                                            WHERE 1=1
                                                AND `user`.`student`.`uid`={$user_id}
                                                AND `user`.`student`.`start`<='{$curdate}'
                                                AND `user`.`student`.`end`  >='{$curdate}'
                                            GROUP BY `user`.`student`.`uid`, `user`.`student`.`class_code`
                                            LIMIT 1

                                            UNION

                                            SELECT `user`.`teacher`.`class_code`
                                            FROM `user`.`teacher`
                                            WHERE 1=1
                                                AND `user`.`teacher`.`uid`={$user_id}
                                                AND `user`.`teacher`.`start`<='{$curdate}'
                                                AND `user`.`teacher`.`end`  >='{$curdate}'
                                            GROUP BY `user`.`teacher`.`uid`, `user`.`teacher`.`class_code`
                                            LIMIT 1
                                        )
                                )
                                OR
                                `mssr`.`mssr_forum`.`create_by` IN ({$user_id})
                            )
                        ";
                    }else{
                        $sql.="
                            AND (
                                `mssr`.`mssr_forum`.`create_by` IN (
                                    SELECT `user`.`student`.`uid`
                                    FROM `user`.`student`
                                    WHERE 1=1
                                        AND (
                                            `user`.`student`.`class_code`=(
                                                SELECT `user`.`student`.`class_code`
                                                FROM `user`.`student`
                                                WHERE 1=1
                                                    AND `user`.`student`.`uid`={$user_id}
                                                    AND `user`.`student`.`start`<='{$curdate}'
                                                    AND `user`.`student`.`end`  >='{$curdate}'
                                                GROUP BY `user`.`student`.`uid`, `user`.`student`.`class_code`
                                                LIMIT 1

                                                UNION

                                                SELECT `user`.`teacher`.`class_code`
                                                FROM `user`.`teacher`
                                                WHERE 1=1
                                                    AND `user`.`teacher`.`uid`={$user_id}
                                                    AND `user`.`teacher`.`start`<='{$curdate}'
                                                    AND `user`.`teacher`.`end`  >='{$curdate}'
                                                GROUP BY `user`.`teacher`.`uid`, `user`.`teacher`.`class_code`
                                                LIMIT 1
                                            )
                                            OR
                                            `user`.`student`.`class_code` IN ('gcp_2014_2_5_2','gcp_2014_2_5_5')
                                        )
                                )
                                OR
                                `mssr`.`mssr_forum`.`create_by` IN ({$user_id})
                            )
                        ";
                    }
					$arrys_result_top_group=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,100),$arry_conn_mssr);
					//$numrow_top_book=count($arrys_result_top_book);

					$arry_book_sid_list=array();
					if(!empty($arrys_result_top_group)){
						$arry_list_group=array();
						foreach($arrys_result_top_group as  $arry_result_recommend){

							$rs_book_sid=trim($arry_result_recommend['forum_id']);

							if(!array_key_exists($rs_book_sid,$arry_book_sid_list)){
								$arry_book_sid_list[$rs_book_sid]=1;
							}else{
								$arry_book_sid_list[$rs_book_sid]=$arry_book_sid_list[$rs_book_sid]+1;
							}
						}

						//排序
						arsort($arry_book_sid_list);

						//篩選
						foreach($arry_book_sid_list as $book_sid=>$cno){
							if(count($arry_list_group)<3){
								$arry_list_group[]=trim($book_sid);
							}else{
								break;
							}
						}
					}

				//-----------------------------------------------
				//SQL-arrys_result_top_people(人排行榜)
				//-----------------------------------------------

					$sql="
						SELECT
                            *
						FROM(
                            SELECT
                                `user_id`, `keyin_cdate`
                            FROM `mssr_forum_article`

							UNION ALL

                            SELECT
                                `user_id`, `keyin_cdate`
                            FROM
                            `mssr_forum_article_reply`
                        ) AS tmp
						WHERE 1=1
					";
                    if($class_code!=='gcp_2014_2_5_2'&&$class_code!=='gcp_2014_2_5_5'){
                        $sql.="
                            AND (
                                tmp.user_id IN (
                                    SELECT `user`.`student`.`uid`
                                    FROM `user`.`student`
                                    WHERE 1=1
                                        AND `user`.`student`.`class_code`=(
                                            SELECT `user`.`student`.`class_code`
                                            FROM `user`.`student`
                                            WHERE 1=1
                                                AND `user`.`student`.`uid`={$user_id}
                                                AND `user`.`student`.`start`<='{$curdate}'
                                                AND `user`.`student`.`end`  >='{$curdate}'
                                            GROUP BY `user`.`student`.`uid`, `user`.`student`.`class_code`
                                            LIMIT 1

                                            UNION

                                            SELECT `user`.`teacher`.`class_code`
                                            FROM `user`.`teacher`
                                            WHERE 1=1
                                                AND `user`.`teacher`.`uid`={$user_id}
                                                AND `user`.`teacher`.`start`<='{$curdate}'
                                                AND `user`.`teacher`.`end`  >='{$curdate}'
                                            GROUP BY `user`.`teacher`.`uid`, `user`.`teacher`.`class_code`
                                            LIMIT 1
                                        )
                                )
                                OR
                                tmp.user_id IN ({$user_id})
                            )
                        ";
                    }else{
                        $sql.="
                            AND (
                                tmp.user_id IN (
                                    SELECT `user`.`student`.`uid`
                                    FROM `user`.`student`
                                    WHERE 1=1
                                        AND (
                                            `user`.`student`.`class_code`=(
                                                SELECT `user`.`student`.`class_code`
                                                FROM `user`.`student`
                                                WHERE 1=1
                                                    AND `user`.`student`.`uid`={$user_id}
                                                    AND `user`.`student`.`start`<='{$curdate}'
                                                    AND `user`.`student`.`end`  >='{$curdate}'
                                                GROUP BY `user`.`student`.`uid`, `user`.`student`.`class_code`
                                                LIMIT 1

                                                UNION

                                                SELECT `user`.`teacher`.`class_code`
                                                FROM `user`.`teacher`
                                                WHERE 1=1
                                                    AND `user`.`teacher`.`uid`={$user_id}
                                                    AND `user`.`teacher`.`start`<='{$curdate}'
                                                    AND `user`.`teacher`.`end`  >='{$curdate}'
                                                GROUP BY `user`.`teacher`.`uid`, `user`.`teacher`.`class_code`
                                                LIMIT 1
                                            )
                                            OR
                                            `user`.`student`.`class_code` IN ('gcp_2014_2_5_2','gcp_2014_2_5_5')
                                        )
                                )
                                OR
                                tmp.user_id IN ({$user_id})
                            )
                        ";
                    }
					$arrys_result_top_people=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,300),$arry_conn_mssr);
					//$numrow_top_people=count($arrys_result_top_people);

					$arry_book_sid_list=array();
					if(!empty($arrys_result_top_people)){
						$arry_list_people=array();
						foreach($arrys_result_top_people as  $arry_result_recommend){

							$rs_book_sid=trim($arry_result_recommend['user_id']);

							if(!array_key_exists($rs_book_sid,$arry_book_sid_list)){
								$arry_book_sid_list[$rs_book_sid]=1;
							}else{
								$arry_book_sid_list[$rs_book_sid]=$arry_book_sid_list[$rs_book_sid]+1;
							}
						}

						//排序
						arsort($arry_book_sid_list);

						//篩選
						foreach($arry_book_sid_list as $book_sid=>$cno){
							if(count($arry_list_people)<5){
								$arry_list_people[]=trim($book_sid);
							}else{
								break;
							}
						}
					}

	//---------------------------------------------------
    //檢驗
    //---------------------------------------------------

	//---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球, 聊書首頁";

	//---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

		$numrow=0;  //資料總筆數
        $numrow=count($arrys_result_index_info);

		$psize =12;  //單頁筆數,預設5筆
		$pnos  =0;  //分頁筆數
		$pinx  =1;  //目前分頁索引,預設1
		$sinx  =0;  //值域起始值
		$einx  =0;  //值域終止值

        if(isset($_GET['psize'])){
            $psize=(int)$_GET['psize'];
            if($psize===0){
                $psize=10;
            }
        }
        if(isset($_GET['pinx'])){
            $pinx=(int)$_GET['pinx'];
            if($pinx===0){
                $pinx=1;
            }
        }

		if($numrow!==0){
			$pnos  =ceil($numrow/$psize);
			$pinx  =($pinx>$pnos)?$pnos:$pinx;

			$sinx  =(($pinx-1)*$psize)+1;
			$einx  =(($pinx)*$psize);
			$einx  =($einx>$numrow)?$numrow:$einx;
			//echo $numrow."<br/>";

			$arrys_result_index_info=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx-1,$psize),$arry_conn_mssr);
		}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title><?php echo $title?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="Content-Language" content="UTF-8">

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

    <link type="text/css" rel="stylesheet" href="css/bootstrap.css">
    <link type="text/css" rel="stylesheet" href="css/mssr_forum.css">
    <link type="text/css" rel="stylesheet" href="../../inc/code.css">

    <style>
        /* 頁面微調 */
        .content{
            top:80px;
        }
    </style>
</head>
<body>

    <!-- navbar start -->
    <?php r_p_navbar((int)$_SESSION["uid"],$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);?>
    <!-- navbar end -->
	
    <!-- content start -->
    <div class="content">
	<img src="image/forum_logo.png" />

        <!-- left_content start -->
        <div class="left_content" style="border-right:1px solid #ebebeb;">
        <?php if(!empty($arrys_result_index_info)):?>
            <table  border="0" width="100%" style='position:relative;margin:10px 0px 10px 0px; border:1px solid #ebebeb;'>
                <tr valign="middle">
                    <td align="left">
                        <!-- 分頁列 -->
                        <span id="page1" style="position:relative;top:0px;"></span>
                    </td>
                </tr>
            </table>
        <?php endif;?>

            <ul class="media-list">
            <li class="media">
            <div class="media-body">

                <!-- 小組提醒, 至頂 -->
                <?php if(!empty($arrys_user_forum)):?>
                    <?php foreach($arrys_user_forum as $arry_user_forum):?>
                    <?php
                        $rs_user_id      =(int)$arry_user_forum['user_id'];
                        $rs_forum_id     =(int)$arry_user_forum['forum_id'];
                        $rs_user_name    =trim($arry_user_forum['name']);
                        $rs_forum_name   =trim($arry_user_forum['forum_name']);
                        $rs_forumpic_root='image/group.png';
                    ?>
                        <div class="media" style="border:1px solid #ebebeb; padding:15px;background-color:#fff4f4;">
                            <a class="pull-left" href="">
                                <img src="<?php echo $rs_forumpic_root;?>" alt="小組圖片1"  width="64" height="64" border='0'/>
                            </a>
                            <div class="media-body">
                                <h4 class="media-heading" style='color:#ff0000;'>【置頂】加入小組申請 - 申請中</h4>
                                <br/>
                                <p>
                                    <a href="mssr_forum_people_index.php?user_id=<?php echo urlencode(addslashes($rs_user_id));?>">
                                        【<?php echo htmlspecialchars($rs_user_name);?>】
                                    </a>
                                    已經提出要加入
                                    <a href="mssr_forum_group_discussion.php?forum_id=<?php echo urlencode(addslashes($rs_forum_id));?>">
                                        【<?php echo htmlspecialchars($rs_forum_name);?>】
                                    </a>，
                                    請問你是否要讓他加入此小組?<br/><br/>

                                    &nbsp;&nbsp;
                                    <a href="javascript:void(0);" onclick="join_group_chk(<?php echo $rs_user_id?>,<?php echo $rs_forum_id?>,'yes');void(0);">是</a>
                                    &nbsp;&nbsp;&nbsp;
                                    <a href="javascript:void(0);" onclick="join_group_chk(<?php echo $rs_user_id?>,<?php echo $rs_forum_id?>,'no');void(0);">否</a>
                                </p>
                            </div>
                        </div>
                    <?php endforeach;?>
                <?php endif;?>

                <!-- 交友提醒, 至頂 -->
                <?php if(!empty($arrys_friend)):?>
                    <?php foreach($arrys_friend as $arry_friend):?>
                    <?php
                        $rs_user_id     =(int)$arry_friend['user_id'];
                        $rs_friend_id   =(int)$arry_friend['friend_id'];
                        $rs_friend_state=trim($arry_friend['friend_state']);
                        $rs_keyin_cdate =date("Y-m-d",strtotime(trim($arry_friend['keyin_cdate'])));

                        $rs_user_name   ='';
                        $rs_friend_name ='';

                        //使用者名稱
                        $get_user_info=get_user_info($conn_user,$rs_user_id,$array_filter=array('name'),$arry_conn_user);
                        if(!empty($get_user_info)){
                            $rs_user_name=trim($get_user_info[0]['name']);
                        }

                        //朋友名稱
                        $get_user_info=get_user_info($conn_user,$rs_friend_id,$array_filter=array('name'),$arry_conn_user);
                        if(!empty($get_user_info)){
                            $rs_friend_name=trim($get_user_info[0]['name']);
                        }

                        if($user_id===$rs_user_id){
                            $rs_user_name='你';
                        }
                        if($user_id===$rs_friend_id){
                            $rs_friend_name='你';
                        }

                        $rs_user_pic_root="image/boy.jpg";

                    ?>
                        <?php if(in_array($rs_friend_state,array('成功','失敗'))):?>
                            <div class="media" style="border:1px solid #ebebeb; padding:15px;">
                                <a class="pull-left" href="">
                                    <img src="<?php echo $rs_user_pic_root;?>" alt="人員圖片2"  width="64" height="64" border='0'/>
                                    <!-- <br/><span style='position:relative;left:7px;'><?php echo htmlspecialchars($rs_friend_name);?></span> -->
                                </a>
                                <div class="media-body">
                                    <h4 class="media-heading" style='color:#3399ff;'>
                                        【通知】交友申請 - <?php echo htmlspecialchars($rs_friend_state);?>
                                    </h4>
                                    <br/>
                                    <p>
                                        <a href="mssr_forum_people_index.php?user_id=<?php echo urlencode(addslashes($rs_user_id));?>">
                                            【<?php echo htmlspecialchars($rs_user_name);?>】
                                        </a>
                                        在 <?php echo $rs_keyin_cdate;?>
                                        提出與
                                        <a href="mssr_forum_people_index.php?user_id=<?php echo urlencode(addslashes($rs_friend_id));?>">
                                            【<?php echo htmlspecialchars($rs_friend_name);?>】
                                        </a>
                                        的
                                        交友申請結果為 : <?php echo htmlspecialchars($rs_friend_state);?>
                                    </p>
                                </div>
                            </div>
                        <?php else:?>
                            <div class="media" style="border:1px solid #ebebeb; padding:15px;background-color:#fff4f4;">
                                <a class="pull-left" href="">
                                    <img src="<?php echo $rs_user_pic_root;?>" alt="人員圖片2"  width="64" height="64" border='0'/>
                                    <!-- <br/><span style='position:relative;left:7px;'><?php echo htmlspecialchars($rs_friend_name);?></span> -->
                                </a>
                                <div class="media-body">
                                    <h4 class="media-heading" style='color:#ff0000;'>
                                        【置頂】交友申請 - <?php echo htmlspecialchars($rs_friend_state);?>
                                    </h4>
                                    <br/>
                                    <p>
                                        <a href="mssr_forum_people_index.php?user_id=<?php echo urlencode(addslashes($rs_user_id));?>">
                                            【<?php echo htmlspecialchars($rs_user_name);?>】
                                        </a>
                                        已經提出要與
                                        <a href="mssr_forum_people_index.php?user_id=<?php echo urlencode(addslashes($rs_friend_id));?>">
                                            【<?php echo htmlspecialchars($rs_friend_name);?>】
                                        </a>
                                        成為朋友，
                                        請問你是否要跟他成為朋友?<br/><br/>

                                        &nbsp;&nbsp;
                                        <a href="javascript:void(0);" onclick="friend_chk(<?php echo $rs_user_id?>,<?php echo $rs_friend_id?>,'成功');void(0);">是</a>
                                        &nbsp;&nbsp;&nbsp;
                                        <a href="javascript:void(0);" onclick="friend_chk(<?php echo $rs_user_id?>,<?php echo $rs_friend_id?>,'失敗');void(0);">否</a>
                                    </p>
                                </div>
                            </div>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>

                <!-- 文章提醒 -->
                <?php if(!empty($arrys_result_index_info)):?>
                    <?php
                        foreach($arrys_result_index_info as $arry_result_index_info):
                        //-------------------------------
                        //接收欄位
                        //-------------------------------

                            extract($arry_result_index_info, EXTR_PREFIX_ALL, "rs");

                        //-------------------------------
                        //處理欄位
                        //-------------------------------

                            $rs_user_id         =(int)$rs_user_id;
                            $rs_article_id      =(int)$rs_article_id;
                            $rs_article_content =trim($rs_article_content);
                            $rs_article_state   =trim($rs_article_state);
                            $rs_keyin_cdate     =trim($rs_keyin_cdate);
                            $rs_book_sid        =trim($rs_book_sid);
                            $rs_article_from    =trim($rs_article_from);

                            if(mb_strlen($rs_article_content)>30){
                                $rs_article_content=mb_substr($rs_article_content,0,30)."..";
                            }

                        //-------------------------------
                        //特殊處理
                        //-------------------------------

                            //動態牆圖片
                            $dynamic_wall_pic_1  ='';
                            $dynamic_wall_pic_2  ='';

                            //動態牆連結
                            $dynamic_wall_href_1 ='';
                            $dynamic_wall_href_2 ='';
                            $dynamic_wall_href_3 ='';

                            $dynamic_wall_content='';

                            //---------------------------
                            //書名 || 討論區名稱
                            //---------------------------

                                if($rs_article_from==='book_article'){

                                    $dynamic_wall_pic_1 ='image/book.jpg';
                                    $dynamic_wall_pic_2 ='image/book.jpg';
                                    $dynamic_wall_href_1="mssr_forum_book_discussion.php?book_sid={$rs_book_sid}";
                                    $dynamic_wall_href_2="mssr_forum_book_discussion.php?book_sid={$rs_book_sid}";
                                    $dynamic_wall_href_3="mssr_forum_book_reply.php?article_id={$rs_article_id}";

                                    //書名
                                    $arry_book_info=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name','book_author'),$arry_conn_mssr);
                                    if(!empty($arry_book_info)){
                                        $rs_book_name=trim($arry_book_info[0]['book_name']);
                                        $rs_book_author=trim($arry_book_info[0]['book_author']);
                                        if($rs_book_author==='')$rs_book_author='無';
                                        $dynamic_wall_content=$rs_book_name;
                                    }
                                }elseif($rs_article_from==='forum_article'){

                                    $dynamic_wall_pic_1 ='image/group.png';
                                    $dynamic_wall_pic_2 ='image/group.png';
                                    $dynamic_wall_href_1="mssr_forum_group_discussion.php?forum_id={$rs_book_sid}";
                                    $dynamic_wall_href_2="mssr_forum_group_discussion.php?forum_id={$rs_book_sid}";
                                    $dynamic_wall_href_3="mssr_forum_group_reply.php?article_id={$rs_article_id}";

                                    //小組名稱
                                    $sql="
                                        SELECT `forum_name`
                                        FROM `mssr_forum`
                                        WHERE `forum_id` = $rs_book_sid
                                    ";
                                    $arry_forum_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                    if(!empty($arry_forum_result)){
                                        $rs_forum_name=trim($arry_forum_result[0]['forum_name']);
                                        $dynamic_wall_content=$rs_forum_name;
                                    }

                                }elseif($rs_article_from==='request_book'){

                                    $dynamic_wall_pic_1 ='image/book.jpg';
                                    $dynamic_wall_pic_2 ='image/book.jpg';
                                    $dynamic_wall_href_1="mssr_forum_book_discussion.php?book_sid={$rs_book_sid}";
                                    $dynamic_wall_href_2="mssr_forum_book_discussion.php?book_sid={$rs_book_sid}";
                                    $dynamic_wall_href_3="";

                                    //書名
                                    $arry_book_info=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name','book_author'),$arry_conn_mssr);
                                    if(!empty($arry_book_info)){
                                        $rs_book_name=trim($arry_book_info[0]['book_name']);
                                        $rs_book_author=trim($arry_book_info[0]['book_author']);
                                        if($rs_book_author==='')$rs_book_author='無';
                                        $dynamic_wall_content=$rs_book_name;
                                    }

                                }else{
                                    continue;
                                }

                            //---------------------------
                            //人名
                            //---------------------------

                                $arry_user_info=get_user_info($conn_user,$rs_user_id,$array_filter=array('name','sex'),$arry_conn_user);
                                if(!empty($arry_user_info)){
                                    $rs_user_name=trim($arry_user_info[0]['name']);
                                    $rs_user_sex =(int)($arry_user_info[0]['sex']);
                                    //大頭貼
                                    if($rs_user_sex===1){
                                        $rs_userpic_root="image/boy.jpg";
                                    }else{
                                        $rs_userpic_root="image/girl.jpg";
                                    }
                                }

                            //---------------------------
                            //是否為朋友
                            //---------------------------

                                $is_friend=false;
                                if($user_id!==$rs_user_id){
                                    $sql="
                                        SELECT `user_id`
                                        FROM `mssr_forum_friend`
                                        WHERE 1=1
                                            AND (
                                                `user_id`  ={$user_id}
                                                    OR
                                                `friend_id`={$user_id}
                                            )
                                            AND (
                                                `user_id`  ={$rs_user_id}
                                                    OR
                                                `friend_id`={$rs_user_id}
                                            )
                                            AND `friend_state` IN ('成功')
                                    ";
                                    $arry_is_friend=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                    if(!empty($arry_is_friend))$is_friend=true;
                                }
                                if($is_friend){
                                    $dynamic_wall_pic_1 =$rs_userpic_root;
                                    $dynamic_wall_href_1="mssr_forum_people_index.php?user_id={$rs_user_id}";
                                }
                    ?>
                        <?php if($rs_article_from!=='request_book'):?>
                            <div class="media" style="border:1px solid #ebebeb; padding:15px;
                            background-color:<?php if($rs_article_from==='book_article'){echo '#f9ffff';}else{echo '#ffffee';}?>;">
                                <a class="pull-left" href="javascript:void(0);">
                                    <img src="<?php echo $dynamic_wall_pic_1;?>" alt="動態牆圖片1"  width="64" height="64" border='0'
                                    <?php
                                        if($rs_article_from==='book_article'){
                                            if($is_friend){echo 'class=""';}else{echo 'class="action_code_i5"';}
                                            echo " user_id_1={$rs_user_id}";
                                            echo " book_sid_1='{$rs_book_sid}'";
                                            echo " go_url='{$dynamic_wall_href_1}'";
                                        }elseif($rs_article_from==='forum_article'){
                                            if($is_friend){echo 'class="action_code_i14"';}else{echo 'class="action_code_i13"';}
                                            echo " user_id_1={$rs_user_id}";
                                            echo " forum_id_1='{$rs_book_sid}'";
                                            echo " go_url='{$dynamic_wall_href_1}'";
                                        }
                                    ?>/>
                                </a>
                                <div class="media-body">
                                    <h4 class="media-heading">
                                        <?php if($is_friend):?>
                                            <a class="pull-left" href="<?php echo $dynamic_wall_href_1;?>">
                                                你的朋友 - <?php echo htmlspecialchars($rs_user_name);?>
                                            </a>，
                                            <?php if($rs_article_from==='book_article'):?>
                                                對這本書
                                            <?php elseif($rs_article_from==='forum_article'):?>
                                                在這個小組
                                            <?php endif;?>
                                            發文
                                        <?php else:?>
                                            <a href="mssr_forum_people_index.php?user_id=<?php echo urlencode(addslashes($rs_user_id));?>">
                                                <?php echo htmlspecialchars($rs_user_name);?>
                                            </a>
                                            ，在<a href="javascript:void(0);"
                                            <?php
                                                if($rs_article_from==='book_article'){
                                                    if($is_friend){echo 'class=""';}else{echo 'class="action_code_i5"';}
                                                    echo " user_id_1={$rs_user_id}";
                                                    echo " book_sid_1='{$rs_book_sid}'";
                                                    echo " go_url='{$dynamic_wall_href_1}'";
                                                }elseif($rs_article_from==='forum_article'){
                                                    if($is_friend){echo 'class="action_code_i14"';}else{echo 'class="action_code_i13"';}
                                                    echo " user_id_1={$rs_user_id}";
                                                    echo " forum_id_1='{$rs_book_sid}'";
                                                    echo " go_url='{$dynamic_wall_href_1}'";
                                                }
                                            ?>>
                                            【<?php $dynamic_wall_content=filter($dynamic_wall_content); echo htmlspecialchars($dynamic_wall_content);?>】</a>說：
                                        <?php endif;?>
                                    </h4>

                                    <?php if($is_friend):?>
                                    <!-- 朋友的文章訊息 -->
                                        <p>
                                            <a href="javascript:void(0);<?php //echo $dynamic_wall_href_2;?>"
                                            <?php
                                                if($rs_article_from==='book_article'){
                                                    if($is_friend){echo 'class="pull-left action_code_i16"';}else{echo 'class="pull-left action_code_i5"';}
                                                    echo " user_id_1={$rs_user_id}";
                                                    echo " book_sid_1='{$rs_book_sid}'";
                                                    echo " go_url='{$dynamic_wall_href_2}'";
                                                }elseif($rs_article_from==='forum_article'){
                                                    if($is_friend){echo 'class="action_code_i14 pull-left"';}else{echo 'class="action_code_i13 pull-left"';}
                                                    echo " user_id_1={$rs_user_id}";
                                                    echo " forum_id_1='{$rs_book_sid}'";
                                                    echo " go_url='{$dynamic_wall_href_2}'";
                                                }
                                            ?>>
                                                <img src="<?php echo $dynamic_wall_pic_2;?>" alt="動態牆圖片2"  width="64" height="64" border='0'/>
                                            </a>
                                            <?php if($rs_article_from==='book_article'):?>
                                                <p>書名：<?php echo htmlspecialchars($rs_book_name);?></p>
                                                <p>作者：<?php echo htmlspecialchars($rs_book_author);?></p>
                                            <?php elseif($rs_article_from==='forum_article'):?>
                                                <br/>
                                                <p>小組名稱：<?php echo htmlspecialchars($rs_forum_name);?></p>
                                            <?php endif;?>
                                        </p>
                                        <br/>
                                    <?php endif;?>

                                    <p><a style="color:#525252;" href="javascript:void(0);<?php //echo $dynamic_wall_href_3;?>"
                                        <?php
                                            if($rs_article_from==='book_article'){
                                                if($is_friend){echo 'class="action_code_i17"';}else{echo 'class="action_code_i6"';}
                                                echo " user_id_1={$rs_user_id}";
                                                echo " book_sid_1='{$rs_book_sid}'";
                                                echo " article_id='{$rs_article_id}'";
                                                echo " go_url='{$dynamic_wall_href_3}'";
                                            }elseif($rs_article_from==='forum_article'){
                                                if($is_friend){echo 'class="action_code_i15"';}else{echo 'class="action_code_i8"';}
                                                echo " user_id_1={$rs_user_id}";
                                                echo " book_sid_1='{$rs_book_sid}'";
                                                echo " forum_id_1='{$rs_book_sid}'";
                                                echo " article_id='{$rs_article_id}'";
                                                echo " go_url='{$dynamic_wall_href_3}'";
                                            }
                                        ?>><?php $rs_article_content=filter($rs_article_content); echo htmlspecialchars($rs_article_content);?></a></p>
                                    <p style="float:right;color:#d1d1d1;"><?php echo htmlspecialchars($rs_keyin_cdate);?></p>
                                </div>
                            </div>
                        <?php else:?>
                            <div class="media" style="border:1px solid #ebebeb; padding:15px;
                            background-color:#fff4f4;">
                                <a class="pull-left" href="<?php echo $dynamic_wall_href_1;?>">
                                    <img src="<?php echo $dynamic_wall_pic_1;?>" alt="動態牆圖片1"  width="64" height="64" border='0'/>
                                </a>
                                <div class="media-body">
                                    <h4 class="media-heading">
                                        <a class="pull-left" href="<?php echo $dynamic_wall_href_1;?>">
                                            你的朋友 - <?php echo htmlspecialchars($rs_user_name);?>
                                        </a>，推薦一本書給你，書名：
                                        <a href="<?php echo $dynamic_wall_href_2;?>">
                                        【<?php echo htmlspecialchars($dynamic_wall_content);?>】</a>。
                                    </h4>
                                    <p><a style="color:#525252;" href="<?php echo $dynamic_wall_href_3;?>"><?php echo htmlspecialchars($rs_article_content);?></a></p>
                                    <p style="float:right;color:#d1d1d1;"><?php echo htmlspecialchars($rs_keyin_cdate);?></p>
                                </div>
                            </div>
                        <?php endif;?>

                    <?php endforeach;?>
                <?php else:?>

                <?php endif;?>

            </div>
            </li>
            </ul>

        <?php if(!empty($arrys_result_index_info)):?>
            <table  border="0" width="100%" style='position:relative;margin:10px 0px 10px 0px; border:1px solid #ebebeb;'>
                <tr valign="middle">
                    <td align="left">
                        <!-- 分頁列 -->
                        <span id="page2" style="position:relative;top:0px;"></span>
                    </td>
                </tr>
            </table>
        <?php endif;?>
        </div>
        <!-- left_content end -->

        <!-- aside start -->
        <div class="aside">

            <!-- 個人資訊 -->
            <div class="thumbnail" style="height:120px;">
                <div class="caption">

					<?php
					//學生照片
					$get_user_info=get_user_info($conn_user,$_SESSION["uid"],$array_filter=array('sex'),$arry_conn_user);
					if($get_user_info[0]['sex']==1){?>
						<a href="javascript:void(0);">
							<img id="index_user_pic" src="image/boy.jpg" alt="<?php echo $user_id?>" width="60" height="60"/>
						</a>
					<?php }else{?>
						<a href="javascript:void(0);">
							<img id="index_user_pic" src="image/girl.jpg" alt="<?php echo $user_id?>" width="60" height="60"/>
						</a>
					<?php }?>
					<div id="index_user_name">
						<h4><font color=blue><B><?php echo $arrys_result_userinfo[0]['name'];?></B></font></h4>
						<p>	<?php echo $arrys_result_user_school[0]['region_name']?>
							<?php echo $arrys_result_user_school[0]['school_name']?><BR>
							<?php echo $arrys_result_usergrade[0]['grade']?>年
							<?php echo $arrys_result_usergrade[0]['classroom']?>班
						</p>
					</div>

                </div>
            </div>

            <!-- 熱門書籍 -->
            <table class="table">
                <thead>
                    <tr align="center"><td colspan=2>熱門書籍</td></tr>
                </thead>
                <tbody>

                    <?php if(!empty($arry_list)):?>
                        <?php foreach($arry_list as $inx=>$rs_book_sid):?>
                        <?php
                            $rs_book_sid=trim($rs_book_sid);

                            //書名
                            $arry_book_info=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                            if(!empty($arry_book_info)){
                                $rs_book_name=trim($arry_book_info[0]['book_name']);
                            }else{
                                continue;
                            }

                            //書籍圖片
                            $bookpic_root    = '../../info/book/'.$rs_book_sid.'/img/front/simg/1.jpg';
                            $bookpic_root_enc=mb_convert_encoding($bookpic_root,$fso_enc,$page_enc);
                            if(file_exists($bookpic_root_enc)){
                                $rs_bookpic_root = '../../info/book/'.$rs_book_sid.'/img/front/simg/1.jpg';
                            }else{
                                $rs_bookpic_root = 'image/book.jpg';
                            }
                        ?>
                        <tr <?php if($inx%2)echo 'class=active';?>>
                            <td width='40px'>
                                <a href="javascript:void(0);"
                                <?php
                                    echo 'class="action_code_i9"';
                                    echo " book_sid_1='{$rs_book_sid}'";
                                    echo " go_url='mssr_forum_book_discussion.php?book_sid={$rs_book_sid}'";
                                ?>>
                                    <img src="<?php echo $rs_bookpic_root;?>" alt=""  width="40" height="40" border='0'/>
                                </a>
                            </td>
                            <td>
                                <a href="javascript:void(0);"
                                <?php
                                    echo 'class="action_code_i9"';
                                    echo " book_sid_1='{$rs_book_sid}'";
                                    echo " go_url='mssr_forum_book_discussion.php?book_sid={$rs_book_sid}'";
                                ?>>
                                    <?php echo htmlspecialchars($rs_book_name);?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    <?php else:?>
                        <tr><td align='center'>目前暫無資料</td></tr>
                    <?php endif;?>

                </tbody>
            </table>

            <!-- 熱門聊書小組 -->
            <table class="table">
                <thead>
                    <tr align="center"><td colspan=2>熱門聊書小組</td></tr>
                </thead>
                <tbody>

                    <?php if(!empty($arry_list_group)):?>
                        <?php foreach($arry_list_group as $inx=>$rs_forum_id):?>
                        <?php
                            $rs_forum_id=(int)$rs_forum_id;
                            $curdate=date("Y-m-d");

                            //小組名稱
                            $sql="
                                SELECT `mssr`.`mssr_forum`.`forum_name`
                                FROM `mssr`.`mssr_forum`
                                WHERE `mssr`.`mssr_forum`.`forum_id`={$rs_forum_id}
                                LIMIT 1
                            ";
                            $arry_forum_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                            if(!empty($arry_forum_result)){
                                $rs_forum_name=trim($arry_forum_result[0]['forum_name']);
                            }else{
                                continue;
                            }

                            //聊書小組圖片
                            $forumpic_root = 'image/forum_pic_'.$rs_forum_id.'.jpg';
                            $forumpic_root_enc=mb_convert_encoding($forumpic_root,$fso_enc,$page_enc);
                            if(file_exists($forumpic_root_enc)){
                                $rs_forumpic_root = 'image/forum_pic_'.$rs_forum_id.'.jpg';
                            }else{
                                $rs_forumpic_root = 'image/group.png';
                            }
                        ?>
                        <tr <?php if($inx%2)echo 'class=active';?>>
                            <td width='40px'>
                                <a href="javascript:void(0);"
                                <?php
                                    echo 'class="action_code_i10"';
                                    echo " forum_id_1='{$rs_forum_id}'";
                                    echo " go_url='mssr_forum_group_discussion.php?forum_id={$rs_forum_id}'";
                                ?>>
                                    <img src="<?php echo $rs_forumpic_root;?>" alt=""  width="40" height="40" border='0'/>
                                </a>
                            </td>
                            <td>
                                <a href="javascript:void(0);"
                                <?php
                                    echo 'class="action_code_i10"';
                                    echo " forum_id_1='{$rs_forum_id}'";
                                    echo " go_url='mssr_forum_group_discussion.php?forum_id={$rs_forum_id}'";
                                ?>>
                                    <?php echo htmlspecialchars($rs_forum_name);?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    <?php else:?>
                        <tr><td align='center'>目前暫無資料</td></tr>
                    <?php endif;?>

                </tbody>
            </table>

            <!-- 名人區 -->
            <table class="table">
                <thead>
                    <tr align="center"><td colspan=2>名人區</td></tr>
                </thead>
                <tbody>

                    <?php if(!empty($arry_list_people)):?>
                        <?php foreach($arry_list_people as $inx=>$rs_user_id):?>
                        <?php
                            $rs_user_id=(int)$rs_user_id;

                            //人名, 性別
                            $arry_user_info=get_user_info($conn_user,$rs_user_id,$array_filter=array('name','sex'),$arry_conn_user);
                            if(!empty($arry_user_info)){
                                $rs_user_name=trim($arry_user_info[0]['name']);
                                $rs_user_sex =(int)($arry_user_info[0]['sex']);
                            }else{
                                continue;
                            }

                            //大頭貼
                            if($rs_user_sex===1){
                                $rs_userpic_root="image/boy.jpg";
                            }else{
                                $rs_userpic_root="image/girl.jpg";
                            }
                        ?>
                        <tr <?php if($inx%2)echo 'class=active';?>>
                            <td width='40px'>
                                <a href="javascript:void(0);"
                                <?php
                                    echo 'class="action_code_i11"';
                                    echo " user_id_1='{$rs_user_id}'";
                                    echo " go_url='mssr_forum_people_index.php?user_id={$rs_user_id}'";
                                ?>>
                                    <img src="<?php echo $rs_userpic_root;?>" alt=""  width="40" height="40" border='0'/>
                                </a>
                            </td>
                            <td>
                                <a href="javascript:void(0);"
                                <?php
                                    echo 'class="action_code_i11"';
                                    echo " user_id_1='{$rs_user_id}'";
                                    echo " go_url='mssr_forum_people_index.php?user_id={$rs_user_id}'";
                                ?>>
                                    <?php echo htmlspecialchars($rs_user_name);?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    <?php else:?>
                        <tr><td align='center'>目前暫無資料</td></tr>
                    <?php endif;?>

                </tbody>
            </table>
        </div>
        <!-- aside end -->

    </div>
    <!-- content end -->

<!-- 頁面js  -->
<script type="text/javascript">
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    //FUNCTION
    $('.action_code_i5').click(function(){
        //呼叫
        add_action_forum_log(
            process_url ='inc/add_action_forum_log/code.php',
            action_code ='i5',
            action_from ='<?php echo (int)$_SESSION["uid"];?>',
            user_id_1   =$(this).attr('user_id_1'),
            user_id_2   =0,
            book_sid_1  =$(this).attr('book_sid_1'),
            book_sid_2  ='',
            forum_id_1  =0,
            forum_id_2  =0,
            article_id  =0,
            reply_id    =0,
            go_url      =$(this).attr('go_url')
        );
    });
    $('.action_code_i6').click(function(){
        //呼叫
        add_action_forum_log(
            process_url ='inc/add_action_forum_log/code.php',
            action_code ='i6',
            action_from ='<?php echo (int)$_SESSION["uid"];?>',
            user_id_1   =$(this).attr('user_id_1'),
            user_id_2   =0,
            book_sid_1  =$(this).attr('book_sid_1'),
            book_sid_2  ='',
            forum_id_1  =0,
            forum_id_2  =0,
            article_id  =$(this).attr('article_id'),
            reply_id    =0,
            go_url      =$(this).attr('go_url')
        );
    });
    $('.action_code_i8').click(function(){
        //呼叫
        add_action_forum_log(
            process_url ='inc/add_action_forum_log/code.php',
            action_code ='i8',
            action_from ='<?php echo (int)$_SESSION["uid"];?>',
            user_id_1   =$(this).attr('user_id_1'),
            user_id_2   =0,
            book_sid_1  ='',
            book_sid_2  ='',
            forum_id_1  =$(this).attr('forum_id_1'),
            forum_id_2  =0,
            article_id  =$(this).attr('article_id'),
            reply_id    =0,
            go_url      =$(this).attr('go_url')
        );
    });
    $('.action_code_i9').click(function(){
        //呼叫
        add_action_forum_log(
            process_url ='inc/add_action_forum_log/code.php',
            action_code ='i9',
            action_from ='<?php echo (int)$_SESSION["uid"];?>',
            user_id_1   =0,
            user_id_2   =0,
            book_sid_1  =$(this).attr('book_sid_1'),
            book_sid_2  ='',
            forum_id_1  =0,
            forum_id_2  =0,
            article_id  =0,
            reply_id    =0,
            go_url      =$(this).attr('go_url')
        );
    });
    $('.action_code_i10').click(function(){
        //呼叫
        add_action_forum_log(
            process_url ='inc/add_action_forum_log/code.php',
            action_code ='i10',
            action_from ='<?php echo (int)$_SESSION["uid"];?>',
            user_id_1   =0,
            user_id_2   =0,
            book_sid_1  ='',
            book_sid_2  ='',
            forum_id_1  =$(this).attr('forum_id_1'),
            forum_id_2  =0,
            article_id  =0,
            reply_id    =0,
            go_url      =$(this).attr('go_url')
        );
    });
    $('.action_code_i11').click(function(){
        //呼叫
        add_action_forum_log(
            process_url ='inc/add_action_forum_log/code.php',
            action_code ='i11',
            action_from ='<?php echo (int)$_SESSION["uid"];?>',
            user_id_1   =$(this).attr('user_id_1'),
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
    $('.action_code_i13').click(function(){
        //呼叫
        add_action_forum_log(
            process_url ='inc/add_action_forum_log/code.php',
            action_code ='i13',
            action_from ='<?php echo (int)$_SESSION["uid"];?>',
            user_id_1   =$(this).attr('user_id_1'),
            user_id_2   =0,
            book_sid_1  ='',
            book_sid_2  ='',
            forum_id_1  =$(this).attr('forum_id_1'),
            forum_id_2  =0,
            article_id  =0,
            reply_id    =0,
            go_url      =$(this).attr('go_url')
        );
    });
    $('.action_code_i14').click(function(){
        //呼叫
        add_action_forum_log(
            process_url ='inc/add_action_forum_log/code.php',
            action_code ='i14',
            action_from ='<?php echo (int)$_SESSION["uid"];?>',
            user_id_1   =$(this).attr('user_id_1'),
            user_id_2   =0,
            book_sid_1  ='',
            book_sid_2  ='',
            forum_id_1  =$(this).attr('forum_id_1'),
            forum_id_2  =0,
            article_id  =0,
            reply_id    =0,
            go_url      =$(this).attr('go_url')
        );
    });
    $('.action_code_i15').click(function(){
        //呼叫
        add_action_forum_log(
            process_url ='inc/add_action_forum_log/code.php',
            action_code ='i15',
            action_from ='<?php echo (int)$_SESSION["uid"];?>',
            user_id_1   =$(this).attr('user_id_1'),
            user_id_2   =0,
            book_sid_1  ='',
            book_sid_2  ='',
            forum_id_1  =$(this).attr('forum_id_1'),
            forum_id_2  =0,
            article_id  =$(this).attr('article_id'),
            reply_id    =0,
            go_url      =$(this).attr('go_url')
        );
    });
    $('.action_code_i16').click(function(){
        //呼叫
        add_action_forum_log(
            process_url ='inc/add_action_forum_log/code.php',
            action_code ='i16',
            action_from ='<?php echo (int)$_SESSION["uid"];?>',
            user_id_1   =$(this).attr('user_id_1'),
            user_id_2   =0,
            book_sid_1  =$(this).attr('book_sid_1'),
            book_sid_2  ='',
            forum_id_1  =0,
            forum_id_2  =0,
            article_id  =0,
            reply_id    =0,
            go_url      =$(this).attr('go_url')
        );
    });
    $('.action_code_i17').click(function(){
        //呼叫
        add_action_forum_log(
            process_url ='inc/add_action_forum_log/code.php',
            action_code ='i17',
            action_from ='<?php echo (int)$_SESSION["uid"];?>',
            user_id_1   =$(this).attr('user_id_1'),
            user_id_2   =0,
            book_sid_1  =$(this).attr('book_sid_1'),
            book_sid_2  ='',
            forum_id_1  =0,
            forum_id_2  =0,
            article_id  =$(this).attr('article_id'),
            reply_id    =0,
            go_url      =$(this).attr('go_url')
        );
    });

    function join_group_chk(user_id, forum_id, flag){

		//參數
		var user_id   		=parseInt(user_id);
		var forum_id      	=parseInt(forum_id);
		var flag      		=trim(flag);

		if((user_id===0)){
			alert('動作失敗!');
			return false;
		}else if(forum_id===0){
			alert('動作失敗!');
			return false;

		}

		//頁面條件
		var url='mssr_user_forum_check_A.php';
		url+='?user_id='+encodeURI(user_id);
		url+='&forum_id='+encodeURI(forum_id);
		url+='&flag='+encodeURI(flag);

        //轉頁
        location.href=url;
    }

	function friend_chk(user_id, friend_id, state){
	//檢驗是否為朋友

		//參數
		var user_id   		=parseInt(user_id);
		var friend_id      	=parseInt(friend_id);
		var state      		=trim(state);

		if((user_id===0)){
			alert('動作失敗!');
			return false;
		}else if(friend_id===0){
			alert('動作失敗!');
			return false;

		}

		//頁面條件
		var url='mssr_forum_friend_check_A.php';
		url+='?user_id='+encodeURI(user_id);
		url+='&friend_id='+encodeURI(friend_id);
		url+='&state='+encodeURI(state);
		//alert(user_id);

		//轉頁
        //action_log('inc/add_action_forum_log/code.php','i12',friend_id,user_id,0,'','',0,0,0,0,url);
		location.href=url;
	}


    //ONLOAD
    $(document).ready(function(){

		//分頁列
		var numrow      =<?php echo (int)$numrow;?>;    //資料總筆數
		var psize       =<?php echo (int)$psize ;?>;    //單頁筆數,預設10筆
		var pnos        =<?php echo (int)$pnos  ;?>;    //分頁筆數
		var pinx        =<?php echo (int)$pinx  ;?>;    //目前分頁索引,預設1
		var sinx        =<?php echo (int)$sinx  ;?>;    //值域起始值
		var einx        =<?php echo (int)$einx  ;?>;    //值域終止值
		var list_size   =5;                             //分頁列顯示筆數,5
		var url_args    ={};                            //連結資訊
		url_args={
			'pinx_name' :'pinx',
			'psize_name':'psize',
			'page_name' :'index.php',

		}
        pages("page1",numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
        pages("page2",numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
    });

</script>
</body>
</Html>