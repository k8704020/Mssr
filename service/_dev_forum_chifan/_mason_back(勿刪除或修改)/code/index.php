<?php
//-------------------------------------------------------
//mssr_forum
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

                    //$_SESSION["uid"]=815;
					$user_id = (int)$_SESSION["uid"];
				//-----------------------------------------------
	        	//mssr_forum_friend 撈取
	        	//-----------------------------------------------

                    //是否成為朋友
                    $sql="
                            SELECT
                                *
                            FROM `mssr_forum_friend`
                            WHERE 1=1
                                AND (
                                    `user_id`  ={$user_id}
                                        OR
                                    `friend_id`={$user_id}
                                )
                                AND `friend_state` IN ('成功','失敗')
                                AND DATE(`keyin_cdate`) >= CURDATE() - INTERVAL 1 DAY
                        UNION
                            SELECT
                                *
                            FROM `mssr_forum_friend`
                            WHERE 1=1
                                AND `friend_id`={$user_id}
                                AND `friend_state` IN ('確認中')
                    ";
                    $arrys_friend=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				//-----------------------------------------------
	        	//SQL-userinfo(學生|老師資訊-姓名)
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
	        	//SQL-class_code(找學生|老師的class_code)
	        	//-----------------------------------------------
					$sql="
						SELECT*
						FROM
							(SELECT
								`student`.`class_code`, `student`.`uid`
							FROM
								`student`

							UNION

							SELECT
								`teacher`.`class_code`, `teacher`.`uid`
							FROM
								`teacher`)tmp
						WHERE 1=1
							AND	`uid` = $user_id
						ORDER BY
							`class_code` DESC
					";
					$arrys_result_userclasscode=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
					$numrow_userclasscode=count($arrys_result_userclasscode);
				//-----------------------------------------------
	        	//SQL-usergrade(用class_code找學生年級資訊)
	        	//-----------------------------------------------
					$class_code = $arrys_result_userclasscode[0]['class_code'];
					$sql="
						SELECT
							`grade`, `classroom`, `class_code`
						FROM
							`class`
						WHERE
							`class_code` = '$class_code'
					";
					$arrys_result_usergrade=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
					$numrow_usergrade=count($arrys_result_usergrade);
				//-----------------------------------------------
	        	//SQL-arrys_result_user_school(用class_code找學生學校資訊)
	        	//-----------------------------------------------
					$user_school = mb_substr($arrys_result_userclasscode[0]['class_code'],0,3);
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


//					echo '<pre>';
//					print_r($query_sql);
//					echo '</pre>';
//					die();


				//-----------------------------------------------
	        	//SQL-arrys_result_top_book(書籍排行榜)
	        	//-----------------------------------------------
					$sql="
						SELECT
							`book_sid`
						FROM
							`mssr_book_borrow_semester`
						WHERE 1=1
							AND	`school_code` 	= 'gcp'
							AND	`grade_id` 		= 3
					";
					$arrys_result_top_book=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,100),$arry_conn_mssr);
					//$numrow_top_book=count($arrys_result_top_book);




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
					$sql="
						SELECT
							`forum_id`
						FROM
							`mssr_article_forum_rev`
						WHERE
							1=1
					";
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
						SELECT*
						FROM
								(SELECT
									`user_id`, `keyin_cdate`
								FROM
									`mssr_forum_article`

							UNION ALL

								SELECT
									`user_id`, `keyin_cdate`
								FROM
									`mssr_forum_article_reply`)tmp
						WHERE 1=1
						ORDER BY
							`keyin_cdate` DESC

					";
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
		//echo "<pre>";
//		print_r($arrys_result_index_info);
//		echo "</pre>";
//		die();

	//---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球-聊書首頁";

	//---------------------------------------------------
    //分頁處理
    //---------------------------------------------------
		$numrow=0;  //資料總筆數
		$psize =12;  //單頁筆數,預設5筆
		$pnos  =0;  //分頁筆數
		$pinx  =1;  //目前分頁索引,預設1
		$sinx  =0;  //值域起始值
		$einx  =0;  //值域終止值
		if(count($arrys_result_index_info)!==0){
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

			//$numrow=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
			$numrow=count($arrys_result_index_info);

			$pnos  =ceil($numrow/$psize);
			$pinx  =($pinx>$pnos)?$pnos:$pinx;

			$sinx  =(($pinx-1)*$psize)+1;
			$einx  =(($pinx)*$psize);
			$einx  =($einx>$numrow)?$numrow:$einx;
			//echo $numrow."<br/>";

			$arrys_result_index_info=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx-1,$psize),$arry_conn_mssr);
		}else{}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title?></title>
</head>
<link href="css/mssr_forum(position).css" type="text/css" rel="stylesheet" />
<link href="css/mssr_forum(style).css" type="text/css" rel="stylesheet" />
<link href="../../inc/code.css" type="text/css" rel="stylesheet" />
<script	type="text/javascript" src="jquery-1.10.2.min.js"></script>
<script	type="text/javascript" src="jquery.blockUI.js"></script>
<script type="text/javascript" src="../../inc/code.js"></script>
<script type="text/javascript" src="../../lib/js/vaildate/code.js"></script>
<script type="text/javascript" src="../../lib/js/public/code.js"></script>
<script type="text/javascript" src="../../lib/js/string/code.js"></script>
<script type="text/javascript" src="../../lib/js/table/code.js"></script>
<script type="text/javascript" src="inc/add_action_forum_log/code.js"></script>
<script>

	var psize=<?php echo $psize;?>;
	var pinx =<?php echo $pinx;?>;

	//Action Log
	function forum_action_log(user_id, type){
		var user_id			=parseInt(user_id);
		var type      		=trim(type);

		//頁面條件
		var url='mssr_forum_action_log_A.php';
		url+='?user_id='+encodeURI(user_id);
		url+='&type='+encodeURI(type);

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
        action_log('inc/add_action_forum_log/code.php','i12',friend_id,user_id,0,'','',0,0,0,0,url);
		//location.href=url;
	}

	function action_log(
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
    ){
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

	$(document).ready(function() {

		//分頁列
		var cid         ="page";                        //容器id
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
		var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
	});
</script>
<body>
<!--=======================================================================================================-->
<!--=============================================頁頭=======================================================-->
<!--=======================================================================================================-->
<section id="logopic">
	<img src="image/logopic2.jpg" alt="" width=100% height="150"/>
    
        <a onclick=""
        href="mssr_forum_create_group.php?user_id=<?php echo $user_id;?>"><img id="create_group" src="image/create_group.png" /></a>
    
</section>

<!--=======================================================================================================-->
<!--=============================================主頁面=====================================================-->
<!--=======================================================================================================-->
<!----------分頁---------->
<div class="index_table_page">
	<?php
	if(count($arrys_result_index_info)!==0):?>
        <table  border="0" width="100%" style='position:relative;top:0px; left:-10px;'>
            <tr valign="middle">
                <td align="left">
                    <!-- 分頁列 -->
                    <span id="page" style="position:relative;top:0px;"></span>
                </td>
            </tr>
        </table>
	<?php endif;?>
</div>
<section class="index_course">
    <!----------交友提醒置頂 開始---------->
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

        ?>
            <?php if(in_array($rs_friend_state,array('成功','失敗'))):?>
                <figure id="index_info" style="background-color:#ccffff">
                    <a href="mssr_forum_people_shelf.php?user_id=<?php echo $user_id;?>">
                        <img id="index_info_pic" src="image/11111.jpg" alt="" width="100" height="100"/>
                    </a>

                    <p id="index_info_name">
                        <?php echo htmlspecialchars($rs_user_name);?>
                        提出與
                        <?php echo htmlspecialchars($rs_friend_name);?>
                        交友申請結果為 : <?php echo htmlspecialchars($rs_friend_state);?>
                    </p>

                    <p id="index_info_time">
                        <?php echo htmlspecialchars($rs_keyin_cdate);?>
                    </p>
                </figure>
            <?php else:?>
                <figure id="index_info" style="background-color:#ccffff">
                    <a href="mssr_forum_people_shelf.php?user_id=<?php echo $user_id;?>">
                        <img id="index_info_pic" src="image/11111.jpg" alt="" width="100" height="100"/>
                    </a>

                    <p id="index_info_name">
                        <?php echo htmlspecialchars($rs_user_name);?>
                        已經提出要與
                        <?php echo htmlspecialchars($rs_friend_name);?>
                        成為朋友，
                        請問是否要跟他成為朋友?<br/>

                        &nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="friend_chk(<?php echo $rs_user_id?>,<?php echo $rs_friend_id?>,'成功');void(0);">是</a>
                        &nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="friend_chk(<?php echo $rs_user_id?>,<?php echo $rs_friend_id?>,'失敗');void(0);">否</a>
                    </p>

                    <p id="index_info_time">
                        <?php echo htmlspecialchars($rs_keyin_cdate);?>
                    </p>
                </figure>
            <?php endif;?>


        <?php endforeach;?>
    <?php endif;?>
    <!-- 交友提醒置頂 結束 -->


	<?php
		if(empty($arrys_result_index_info)){
			echo("目前還沒有留過言喔，趕快去參與討論吧！");
		}else{
			for($i=0; $i<count($arrys_result_index_info); $i++){
				$user_id 			= trim($arrys_result_index_info[$i]['user_id']);
				$book_sid 			= trim($arrys_result_index_info[$i]['book_sid']);
				$article_content 	= trim($arrys_result_index_info[$i]['article_content']);
				$keyin_cdate 		= trim($arrys_result_index_info[$i]['keyin_cdate']);
				$article_from 		= trim($arrys_result_index_info[$i]['article_from']);
				$article_id 		= trim($arrys_result_index_info[$i]['article_id']);

				//article_content		書籍內容
				if(mb_strlen($article_content)>25){
					$article_content=mb_substr($article_content,0,25)."..";
				}
				$sql="
					SELECT
						`name`
					FROM
						`member`
					WHERE
						`uid` = $user_id
				";
                $arrys_result_username=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
				//書籍發文
				if($article_from =="book_article"){
					$arrys_book_info=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
					$book_name 			= trim($arrys_book_info[0]['book_name']);

					//book_name		書名
					if(mb_strlen($book_name)>13){
						$book_name=mb_substr($book_name,0,13)."..";
					}?>
					<figure id="index_info" style="background-color:#FFCFCE">
                    	<?php
						//學生照片
						$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
						if($get_user_info[0]['sex']==1){?>
							<a onclick="action_log('inc/add_action_forum_log/code.php','i4',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',0,0,0,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id;?>');void(0);"
                            href="javascript:void(0);"><img id="index_info_pic" src="image/boy.jpg" alt="" width="100" height="100"/></a>
						<?php }else{?>
							<a onclick="action_log('inc/add_action_forum_log/code.php','i4',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',0,0,0,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id;?>');void(0);"
                            href="javascript:void(0);"><img id="index_info_pic" src="image/girl.jpg" alt="" width="100" height="100"/></a>
						<?php }?>

      					<p id="index_info_name">
                            <?php echo $arrys_result_username[0]['name']?>，
                            剛剛在「<a onclick="action_log('inc/add_action_forum_log/code.php','i5',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'<?php echo $book_sid?>','',0,0,0,0,'mssr_forum_book_discussion.php?book_sid=<?php echo $book_sid?>');void(0);"
                            href="javascript:void(0);"><?php echo $book_name?></a>」說：<br/>
                            「<a onclick="action_log('inc/add_action_forum_log/code.php','i6',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'<?php echo $book_sid?>','',0,0,<?php echo $article_id;?>,0,'mssr_forum_reply.php?article_id=<?php echo $article_id;?>');void(0);"
                            href="javascript:void(0);"><?php echo $article_content?></a>」</p>

   						<p id="index_info_time"><?php echo $keyin_cdate?></p>
					</figure>
				<?php
				//社團發文
				}else if($article_from =="forum_article"){
					$sql="
						SELECT
							`forum_name`
						FROM
							`mssr_forum`
						WHERE
							`forum_id` = $book_sid
					";
					$arrys_result_forum_name=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

					//forum_name		討論區名稱
					if(mb_strlen($arrys_result_forum_name[0]['forum_name'])>13){
						$arrys_result_forum_name[0]['forum_name']=mb_substr($arrys_result_forum_name[0]['forum_name'],0,13)."..";
					}?>
                    <figure id="index_info" style="background-color:#DBFFD7">
      					<?php
						//學生照片
						$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
						if($get_user_info[0]['sex']==1){?>
							<a onclick="action_log('inc/add_action_forum_log/code.php','i4',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',0,0,0,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id;?>');void(0);"
                            href="javascript:void(0);"><img id="index_info_pic" src="image/boy.jpg" alt="" width="100" height="100"/></a>
						<?php }else{?>
							<a onclick="action_log('inc/add_action_forum_log/code.php','i4',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',0,0,0,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id;?>');void(0);"
                            href="javascript:void(0);"><img id="index_info_pic" src="image/girl.jpg" alt="" width="100" height="100"/></a>
						<?php }?>
      					<p id="index_info_name">
                            <?php echo $arrys_result_username[0]['name']?>，剛剛在「
                            <a onclick="action_log('inc/add_action_forum_log/code.php','i7',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',<?php echo $book_sid?>,0,0,0,'mssr_forum_group_discussion.php?forum_id=<?php echo $book_sid?>');void(0);"
                            href="javascript:void(0);">
                                <?php echo $arrys_result_forum_name[0]['forum_name']?>
                            </a>」說：<br/>
                            「<a onclick="action_log('inc/add_action_forum_log/code.php','i8',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',<?php echo $book_sid?>,0,<?php echo $article_id;?>,0,'mssr_forum_group_reply.php?article_id=<?php echo $article_id;?>');void(0);"
                              href="javascript:void(0);"><?php echo $article_content?></a>」</p>
   						<p id="index_info_time"><?php echo $keyin_cdate?></p>
					</figure>
				<?php }?>
			<?php }?>
  		<?php }?>
</section>


<!--=======================================================================================================-->
<!--=============================================側欄=======================================================-->
<!--=======================================================================================================-->

<!----------登入者資訊---------->
<section class="index_name">
	<?php
	//學生照片
	$get_user_info=get_user_info($conn_user,$_SESSION["uid"],$array_filter=array('sex'),$arry_conn_user);
	if($get_user_info[0]['sex']==1){?>
		<a onclick="action_log('inc/add_action_forum_log/code.php','i0',<?php echo $_SESSION["uid"];?>,<?php echo $_SESSION["uid"];?>,0,'','',0,0,0,0,'mssr_forum_people_shelf.php?user_id=<?php echo $_SESSION["uid"];?>');void(0);"
        href="javascript:void(0);"><img id="index_user_pic" src="image/boy.jpg" alt="<?php echo $user_id?>" width="100" height="100"/></a>
	<?php }else{?>
		<a onclick="action_log('inc/add_action_forum_log/code.php','i0',<?php echo $_SESSION["uid"];?>,<?php echo $_SESSION["uid"];?>,0,'','',0,0,0,0,'mssr_forum_people_shelf.php?user_id=<?php echo $_SESSION["uid"];?>');void(0);"
        href="javascript:void(0);"><img id="index_user_pic" src="image/girl.jpg" alt="<?php echo $user_id?>" width="100" height="100"/></a>
	<?php }?>


	<a onclick="action_log('inc/add_action_forum_log/code.php','i0',<?php echo $_SESSION["uid"];?>,<?php echo $_SESSION["uid"];?>,0,'','',0,0,0,0,'mssr_forum_people_shelf.php?user_id=<?php echo $_SESSION["uid"];?>');void(0);"
    href="javascript:void(0);"><p id="index_user_shelf">書櫃</p></a>

    <a onclick="action_log('inc/add_action_forum_log/code.php','i1',<?php echo $_SESSION["uid"];?>,<?php echo $_SESSION["uid"];?>,0,'','',0,0,0,0,'mssr_forum_people_myreply.php?user_id=<?php echo $_SESSION["uid"];?>');void(0);"
    href="javascript:void(0);"><p id="index_user_discussion">討論</p></a>

    <a onclick="action_log('inc/add_action_forum_log/code.php','i2',<?php echo $_SESSION["uid"];?>,<?php echo $_SESSION["uid"];?>,0,'','',0,0,0,0,'mssr_forum_people_group.php?user_id=<?php echo $_SESSION["uid"];?>');void(0);"
    href="javascript:void(0);"><p id="index_user_group">聊書小組</p></a>

    <a onclick="action_log('inc/add_action_forum_log/code.php','i3',<?php echo $_SESSION["uid"];?>,<?php echo $_SESSION["uid"];?>,0,'','',0,0,0,0,'mssr_forum_people_friend.php?user_id=<?php echo $_SESSION["uid"];?>');void(0);"
    href="javascript:void(0);"><p id="index_user_friend">朋友</p></a>

    <h1 id="index_user_name"><?php echo $arrys_result_userinfo[0]['name']?></h1>
    <p id="index_user_school"><?php echo $arrys_result_user_school[0]['region_name']?><?php echo $arrys_result_user_school[0]['school_name']?><?php echo $arrys_result_usergrade[0]['grade']?>年<?php echo $arrys_result_usergrade[0]['classroom']?>班</p>
</section>

<!----------書籍排行榜---------->
<section class="index_top_book">
	<p id="index_top_book_title">書籍排行榜TOP 5</p>
    <img id="index_top_book_icon" src="image/icon_hot.png" alt="" width="30" height="30"/>
    <?php
	if(count($arry_list)!==0){
		for($i=0; $i<count($arry_list); $i++){
			$book_sid 				= mysql_prep(trim($arry_list[$i]));
			$arrys_book_info		= get_book_info($conn_mssr,$book_sid,$array_filter=array(),$arry_conn_mssr);
			$book_name 				= mysql_prep(trim($arrys_book_info[0]['book_name']));

			//book_name		書名
			if(mb_strlen($book_name)>7){
				$book_name=mb_substr($book_name,0,7)."..";
			}

			//書籍封面處理
			$bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
			if(file_exists($bookpic_root)){
				$rs_bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
			}else{
				$rs_bookpic_root = 'image/book.jpg';
			}?>

			<figure id="index_top_book_info">
				<p id="index_top_book_info_number">No.<?php echo $i+1 ?></p>
				<a onclick="action_log('inc/add_action_forum_log/code.php','i9',<?php echo $_SESSION["uid"];?>,0,0,'<?php echo $book_sid;?>','',0,0,0,0,'mssr_forum_book_discussion.php?book_sid=<?php echo $book_sid?>');void(0);"
                href="javascript:void(0);"><img id="index_top_book_info_pic" src="<?php echo $rs_bookpic_root?>" alt="" width="50" height="50"/></a>
				<a onclick="action_log('inc/add_action_forum_log/code.php','i9',<?php echo $_SESSION["uid"];?>,0,0,'<?php echo $book_sid;?>','',0,0,0,0,'mssr_forum_book_discussion.php?book_sid=<?php echo $book_sid?>');void(0);"
                href="javascript:void(0);" id="index_top_book_info_bookname"><?php echo $book_name?></a>
			</figure>
		<?php }?>
    <?php }else{
		echo "目前暫無資料";
	}?>
</section>

<!----------討論區排行榜---------->
<section class="index_top_group">
	<p id="index_top_group_title">熱門聊書小組</p>
    <img id="index_top_group_icon" src="image/icon_hot.png" alt="" width="30" height="30"/>
    <?php
	if(count($arry_list_group)!==0){
		for($i=0; $i<count($arry_list_group); $i++){
			$forum_id 				= (int)$arry_list_group[$i];
			$sql="
				SELECT
					`forum_name`
				FROM
					`mssr_forum`
				WHERE
					`forum_id` = $forum_id
			";
			$arrys_result_forum_name=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

			//forum_name		聊書小組名稱
			if(mb_strlen($arrys_result_forum_name[0]['forum_name'])>7){
				$arrys_result_forum_name[0]['forum_name']=mb_substr($arrys_result_forum_name[0]['forum_name'],0,7)."..";
			}

			//聊書小組封面處理
			$forumpic_root = 'image/forum_pic_'.$forum_id.'.jpg';
			if(file_exists($forumpic_root)){
				$rs_forumpic_root = 'image/forum_pic_'.$forum_id.'.jpg';
			}else{
				$rs_forumpic_root = 'image/forum_pic.jpg';
			}?>

			<figure id="index_top_group_info">
				<p id="index_top_group_info_number">No.<?php echo $i+1 ?></p>
				<a onclick="action_log('inc/add_action_forum_log/code.php','i10',<?php echo $_SESSION["uid"];?>,0,0,'','',<?php echo $forum_id?>,0,0,0,'mssr_forum_group_discussion.php?forum_id=<?php echo $forum_id?>');void(0);"
                href="javascript:void(0);"><img id="index_top_group_info_pic" src="<?php echo $rs_forumpic_root?>" alt="" width="50" height="50"/></a>
				<a onclick="action_log('inc/add_action_forum_log/code.php','i10',<?php echo $_SESSION["uid"];?>,0,0,'','',<?php echo $forum_id?>,0,0,0,'mssr_forum_group_discussion.php?forum_id=<?php echo $forum_id?>');void(0);"
                href="javascript:void(0);" id="index_top_group_info_groupname"><?php echo $arrys_result_forum_name[0]['forum_name']?></a>
			</figure>
		<?php }?>
    <?php }else{
		echo "目前暫無資料";
	}?>
</section>

<!----------人排行榜---------->
<section class="index_top_people">
	<p id="index_top_people_title">聊書名人區</p>
    <img id="index_top_people_icon" src="image/icon_hot.png" alt="" width="30" height="30"/>
    <?php
	if(count($arry_list_people)!==0){
		for($i=0; $i<count($arry_list_people); $i++){
			$user_id = (int)$arry_list_people[$i];
			$sql="
				SELECT
					`name`
				FROM
					`member`
				WHERE
					`uid` = $user_id
			";
			$arrys_result_user_name=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
			$user_name = $arrys_result_user_name[0]['name'];
				//-----------------------------------------------
	        	//SQL-class_code(找學生|老師的class_code)
	        	//-----------------------------------------------
					$sql="
						SELECT*
						FROM
							(SELECT
								`student`.`class_code`, `student`.`uid`
							FROM
								`student`

							UNION

							SELECT
								`teacher`.`class_code`, `teacher`.`uid`
							FROM
								`teacher`)tmp
						WHERE 1=1
							AND	`uid` = $user_id
						ORDER BY
							`class_code` DESC
					";
					$arrys_result_userclasscode=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
					$numrow_userclasscode=count($arrys_result_userclasscode);
				//-----------------------------------------------
	        	//SQL-usergrade(用class_code找學生年級資訊)
	        	//-----------------------------------------------
					$class_code = $arrys_result_userclasscode[0]['class_code'];
					$sql="
						SELECT
							`grade`, `classroom`, `class_code`
						FROM
							`class`
						WHERE
							`class_code` = '$class_code'
					";
					$arrys_result_usergrade=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
					$numrow_usergrade=count($arrys_result_usergrade);
				//-----------------------------------------------
	        	//SQL-arrys_result_user_school(用class_code找學生學校資訊)
	        	//-----------------------------------------------
					$user_school = mb_substr($arrys_result_userclasscode[0]['class_code'],0,3);
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
			?>

			<figure id="index_top_people_info">
				<p id="index_top_people_info_number">No.<?php echo $i+1 ?></p>
				<?php
				//學生照片
				$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
				if($get_user_info[0]['sex']==1){?>
					<a onclick="action_log('inc/add_action_forum_log/code.php','i11',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',0,0,0,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                    href="javascript:void(0);"><img id="index_top_people_info_pic" src="image/boy.jpg" alt="<?php echo $user_id?>" width="50" height="50"/></a>
				<?php }else{?>
					<a onclick="action_log('inc/add_action_forum_log/code.php','i11',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',0,0,0,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                    href="javascript:void(0);"><img id="index_top_people_info_pic" src="image/girl.jpg" alt="<?php echo $user_id?>" width="50" height="50"/></a>
				<?php }?>

				<a onclick="action_log('inc/add_action_forum_log/code.php','i11',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',0,0,0,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                href="javascript:void(0);" id="index_top_people_info_name"><?php echo $arrys_result_usergrade[0]['grade'].年.$arrys_result_usergrade[0]['classroom'].班 .$user_name?></a>
			</figure>
		<?php }?>
    <?php }else{
		echo "目前暫無資料";
	}?>
</section>
<!--=======================================================================================================-->
<!--=============================================頁尾=======================================================-->
<!--=======================================================================================================-->
</body>
</html>
