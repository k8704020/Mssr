<?php
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
			APP_ROOT.'lib/php/db/code',
            APP_ROOT.'service/_dev_forum_eric/inc/code'
        );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

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
		$sql="
			SELECT a.`uid`,sch1.`school_name`,c1.`grade`,c1.`classroom`
			FROM `user`.`member` as a
				LEFT JOIN  `user`.`student` AS st1 ON ( a.`uid` = st1.`uid` ) 
				LEFT JOIN  `user`.`class` AS c1 ON ( c1.`class_code` = st1.`class_code` ) 
				LEFT JOIN  `user`.`semester` AS sem1 ON ( sem1.`semester_code` = c1.`semester_code` ) 
				LEFT JOIN  `user`.`school` AS sch1 ON ( sch1.`school_code` = sem1.`school_code` )
			WHERE 
				sch1.`school_name` ='中平國小' 
				and sem1.`semester_code` like '%2015_2%' 
				and c1.`grade` =5
				and	c1.`classroom`=3		
		";
		$get_data1 = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		$i = 0;
		foreach($get_data1 as $key =>$get_data1){
			$i++;
			$uid1[$key]        = $get_data1['uid'];
			$school_name[$key] = $get_data1['school_name'];
			$grade[$key]       = $get_data1['grade'];
			$classroom[$key]   = $get_data1['classroom'];			
		}
		$data_from = '2016/04/18';
        //-----------------------------------------------
		echo "<table border=1>";
		echo "<tr>";
			//echo "<td>人數</td>";
			echo "<td>使用者編號</td>";
			echo "<td>學校名稱</td>";
			echo "<td>班級</td>";
			echo "<td>文章按讚數</td>";
			echo "<td>文章按讚數(4/18~5/1)</td>";
			echo "<td>文章按讚數(5/2~now)</td>";
			echo "<td>文章被按讚數</td>";
			echo "<td>文章被按讚數(4/18~5/1)</td>";
			echo "<td>文章被按讚數(5/2~now)</td>";
			echo "<td>回覆文章</td>";
			echo "<td>回覆文章(4/18~5/1)</td>";
			echo "<td>回覆文章(5/2~now)</td>";
			echo "<td>文章被回覆</td>";
			echo "<td>文章被回覆(4/18~5/1)</td>";
			echo "<td>文章被回覆(5/2~now)</td>";
			echo "<td>鷹架發文</td>";
			echo "<td>鷹架發文(4/18~5/1)</td>";
			echo "<td>鷹架發文(5/2~now)</td>";
			echo "<td>發文</td>";
			echo "<td>發文(4/18~5/1)</td>";
			echo "<td>發文(5/2~now)</td>";
			echo "<td>請求推薦</td>";
			echo "<td>請求推薦(4/18~5/1)</td>";
			echo "<td>請求推薦(4/18~5/1)</td>";

		echo "</tr>";
		
		//-----------------------------------------------
		for($k=0; $k < $i; $k++){
		//-----------------------------------------------文章按讚數(發回與回文)
			$article_like_cno1_1[$k] = 0;
			$reply_like_cno1_1[$k]  = 0;
			$sql="
				SELECT
					count(`article_id`) as like_cno,`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_article_like_log`
				WHERE `user_id`={$uid1[$k]} 
				group by `user_id`
				UNION ALL
				SELECT
					count(`reply_id`) as like_cno,`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_reply_like_log`
				WHERE `user_id`={$uid1[$k]} 
				group by `user_id`
			";
			$achieve_data1_1 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);//array(x,y) 第x筆開始找y筆
			if(isset($achieve_data1_1[0]['like_cno'])){$article_like_cno1_1[$k] = (int)$achieve_data1_1[0]['like_cno'];}
			if(isset($achieve_data1_1[1]['like_cno'])){$reply_like_cno1_1[$k]   = (int)$achieve_data1_1[1]['like_cno'];}
			$cno1_1[$k] = $article_like_cno1_1[$k] + $reply_like_cno1_1[$k];
			//-----------------------------------------------
			$article_like_cno1_2[$k] = 0;
			$reply_like_cno1_2[$k]  = 0;
			$sql="
				SELECT
					count(`article_id`) as like_cno,`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_article_like_log`
				WHERE `user_id`={$uid1[$k]} and (`keyin_mdate` BETWEEN '".$data_from."' AND '2016/05/01') 
				group by `user_id`
				UNION ALL
				SELECT
					count(`reply_id`) as like_cno,`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_reply_like_log`
				WHERE `user_id`={$uid1[$k]} and (`keyin_mdate` BETWEEN '".$data_from."' AND '2016/05/01')
				group by `user_id`
			";
			$achieve_data1_2 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);//array(x,y) 第x筆開始找y筆
			if(isset($achieve_data1_2[0]['like_cno'])){$article_like_cno1_2[$k] = (int)$achieve_data1_2[0]['like_cno'];}
			if(isset($achieve_data1_2[1]['like_cno'])){$reply_like_cno1_2[$k]   = (int)$achieve_data1_2[1]['like_cno'];}
			$cno1_2[$k] = $article_like_cno1_2[$k] + $reply_like_cno1_2[$k];			
			//-----------------------------------------------
			$article_like_cno1_3[$k] = 0;
			$reply_like_cno1_3[$k]  = 0;
			$sql="
				SELECT
					count(`article_id`) as like_cno,`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_article_like_log`
				WHERE `user_id`={$uid1[$k]} and (`keyin_mdate` BETWEEN '2016/05/02' AND now()) 
				group by `user_id`
				UNION ALL
				SELECT
					count(`reply_id`) as like_cno,`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_reply_like_log`
				WHERE `user_id`={$uid1[$k]} and (`keyin_mdate` BETWEEN '2016/05/02' AND now())
				group by `user_id`
			";
			$achieve_data1_3 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);//array(x,y) 第x筆開始找y筆
			if(isset($achieve_data1_3[0]['like_cno'])){$article_like_cno1_3[$k] = (int)$achieve_data1_3[0]['like_cno'];}
			if(isset($achieve_data1_3[1]['like_cno'])){$reply_like_cno1_3[$k]   = (int)$achieve_data1_3[1]['like_cno'];}
			$cno1_3[$k] = $article_like_cno1_3[$k] + $reply_like_cno1_3[$k];			
		//-----------------------------------------------文章被按讚數(發回與回文)
			$article_liked_cno2_1[$k] = 0;
			$reply_liked_cno2_1[$k]   = 0;
			$sql="
				SELECT
					sum(`article_like_cno`) as liked_cno
				FROM `mssr_forum`.`mssr_forum_article`
				WHERE `user_id`={$uid1[$k]}
				group by `user_id`
				UNION ALL
				SELECT
					sum(`reply_like_cno`) as liked_cno
				FROM `mssr_forum`.`mssr_forum_reply`
				WHERE `user_id`={$uid1[$k]}
				group by `user_id`
			";
			$achieve_data2_1 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data2_1[0]['liked_cno'])){$article_liked_cno2_1[$k] = (int)$achieve_data2_1[0]['liked_cno'];}
			if(isset($achieve_data2_1[1]['liked_cno'])){$reply_liked_cno2_1[$k]   = (int)$achieve_data2_1[1]['liked_cno'];}
			$cno2_1[$k] = $article_liked_cno2_1[$k] + $reply_liked_cno2_1[$k];
			//-----------------------------------------------
			$article_liked_cno2_2[$k] = 0;
			$reply_liked_cno2_2[$k]   = 0;
			$sql="
				SELECT
					sum(`article_like_cno`) as liked_cno,`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_article`
				WHERE `user_id`={$uid1[$k]} and (`keyin_mdate` BETWEEN '".$data_from."' AND '2016/05/01') 
				group by `user_id`
				UNION ALL
				SELECT
					sum(`reply_like_cno`) as liked_cno,`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_reply`
				WHERE `user_id`={$uid1[$k]} and (`keyin_mdate` BETWEEN '".$data_from."' AND '2016/05/01') 
				group by `user_id`
			";
			$achieve_data2_2 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data2_2[0]['liked_cno'])){$article_liked_cno2_2[$k] = (int)$achieve_data2_2[0]['liked_cno'];}
			if(isset($achieve_data2_2[1]['liked_cno'])){$reply_liked_cno2_2[$k]   = (int)$achieve_data2_2[1]['liked_cno'];}
			$cno2_2[$k] = $article_liked_cno2_2[$k] + $reply_liked_cno2_2[$k];
			//-----------------------------------------------
			$article_liked_cno2_3[$k] = 0;
			$reply_liked_cno2_3[$k]   = 0;
			$sql="
				SELECT
					sum(`article_like_cno`) as liked_cno,`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_article`
				WHERE `user_id`={$uid1[$k]} and (`keyin_mdate` BETWEEN '2016/05/02' AND now()) 
				group by `user_id`
				UNION ALL
				SELECT
					sum(`reply_like_cno`) as liked_cno,`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_reply`
				WHERE `user_id`={$uid1[$k]} and (`keyin_mdate` BETWEEN '2016/05/02' AND now()) 
				group by `user_id`
			";
			$achieve_data2_3 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data2_3[0]['liked_cno'])){$article_liked_cno2_3[$k] = (int)$achieve_data2_3[0]['liked_cno'];}
			if(isset($achieve_data2_3[1]['liked_cno'])){$reply_liked_cno2_3[$k]   = (int)$achieve_data2_3[1]['liked_cno'];}
			$cno2_3[$k] = $article_liked_cno2_3[$k] + $reply_liked_cno2_3[$k];
		//-----------------------------------------------回覆文章
			$cno3_1[$k] = 0;
			$sql="
				SELECT count(`reply_id`) as reply_cno
				FROM `mssr_forum`.`mssr_forum_reply`
				WHERE `user_id`={$uid1[$k]}
			";
			$achieve_data3_1 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data3_1[0]['reply_cno'])){$cno3_1[$k] =(int)$achieve_data3_1[0]['reply_cno'];}
			//-----------------------------------------------
			$cno3_2[$k] = 0;
			$sql="
				SELECT count(`reply_id`) as reply_cno,`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_reply`
				WHERE `user_id`={$uid1[$k]} and (`keyin_mdate` BETWEEN '".$data_from."' AND '2016/05/01')
			";
			$achieve_data3_2 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data3_2[0]['reply_cno'])){$cno3_2[$k] =(int)$achieve_data3_2[0]['reply_cno'];}
			//-----------------------------------------------
			$cno3_3[$k] = 0;
			$sql="
				SELECT count(`reply_id`) as reply_cno,`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_reply`
				WHERE `user_id`={$uid1[$k]} and (`keyin_mdate` BETWEEN '2016/05/02' AND now())
			";
			$achieve_data3_3 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data3_3[0]['reply_cno'])){$cno3_3[$k] =(int)$achieve_data3_3[0]['reply_cno'];}			
		//-----------------------------------------------文章被回覆
			$cno4_1[$k] = 0;
			$sql="
				SELECT a.`user_id` , a.`article_id` ,COUNT( r.`reply_id` ) as replied_cno
				FROM  `mssr_forum`.`mssr_forum_article` AS a
				INNER JOIN  `mssr_forum`.`mssr_forum_reply` AS r ON ( a.`article_id` = r.`article_id` )
				WHERE a.`user_id`={$uid1[$k]} 
			";
			$achieve_data4_1 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data4_1[0]['replied_cno'])){$cno4_1[$k] =(int)$achieve_data4_1[0]['replied_cno'];}
			//-----------------------------------------------
			$cno4_2[$k] = 0;
			$sql="
				SELECT a.`user_id` , a.`article_id` ,COUNT( r.`reply_id` ) as replied_cno,r.`keyin_mdate`
				FROM  `mssr_forum`.`mssr_forum_article` AS a
				INNER JOIN  `mssr_forum`.`mssr_forum_reply` AS r ON ( a.`article_id` = r.`article_id` )
				WHERE a.`user_id`={$uid1[$k]} and (r.`keyin_mdate` BETWEEN '".$data_from."' AND '2016/05/01')
			";
			$achieve_data4_2 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data4_2[0]['replied_cno'])){$cno4_2[$k] =(int)$achieve_data4_2[0]['replied_cno'];}//-----------------------------------------------
			$cno4_3[$k] = 0;
			$sql="
				SELECT a.`user_id` , a.`article_id` ,COUNT( r.`reply_id` ) as replied_cno,r.`keyin_mdate`
				FROM  `mssr_forum`.`mssr_forum_article` AS a
				INNER JOIN  `mssr_forum`.`mssr_forum_reply` AS r ON ( a.`article_id` = r.`article_id` )
				WHERE a.`user_id`={$uid1[$k]} and (r.`keyin_mdate` BETWEEN '2016/05/02' AND now())
			";
			$achieve_data4_3 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data4_3[0]['replied_cno'])){$cno4_3[$k] =(int)$achieve_data4_3[0]['replied_cno'];}			
		//-----------------------------------------------鷹架發文	
			$cno5_1[$k] = 0;
			$sql="
				SELECT count(a.`article_id`) as article_eagle_cno
				FROM `mssr_forum`.`mssr_forum_article`as a
				inner join `mssr_forum`.`mssr_forum_article_eagle_rev` as b on (a.`article_id`=b.`article_id`)
				WHERE `user_id`={$uid1[$k]} and `eagle_code`!=0
				group by `user_id`
			";
			$achieve_data5_1 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data5_1[0]['article_eagle_cno'])){$cno5_1[$k] =(int)$achieve_data5_1[0]['article_eagle_cno'];}		
			//-----------------------------------------------
			$cno5_2[$k] = 0;
			$sql="
				SELECT count(a.`article_id`) as article_eagle_cno,a.`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_article`as a
				inner join `mssr_forum`.`mssr_forum_article_eagle_rev` as b on (a.`article_id`=b.`article_id`)
				WHERE `user_id`={$uid1[$k]} and `eagle_code`!=0 and (a.`keyin_mdate` BETWEEN '".$data_from."' AND '2016/05/01')
				group by `user_id`
			";
			$achieve_data5_2 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data5_2[0]['article_eagle_cno'])){$cno5_2[$k] =(int)$achieve_data5_2[0]['article_eagle_cno'];}//-----------------------------------------------
			$cno5_3[$k] = 0;
			$sql="
				SELECT count(a.`article_id`) as article_eagle_cno,a.`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_article`as a
				inner join `mssr_forum`.`mssr_forum_article_eagle_rev` as b on (a.`article_id`=b.`article_id`)
				WHERE `user_id`={$uid1[$k]} and `eagle_code`!=0  and (a.`keyin_mdate` BETWEEN '2016/05/02' AND now())
				group by `user_id`
			";
			$achieve_data5_3 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data5_3[0]['article_eagle_cno'])){$cno5_3[$k] =(int)$achieve_data5_3[0]['article_eagle_cno'];}			
		//-----------------------------------------------請求推薦
			$cno6_1[$k] = 0;
			$sql="
				SELECT count(rb1.`request_id`) as request_read_cno
				FROM `mssr_forum`.`mssr_forum_user_request` as a1
				inner join `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev` as rb1 on (rb1.`request_id`=a1.`request_id`)
				WHERE `request_read`=1 and a1.`request_from`={$uid1[$k]}
				and rb1.`request_id` in
				(SELECT rb1.`request_id` FROM `mssr_forum`.`mssr_forum_article_book_rev` as b1 where b1.`book_sid`=rb1.`book_sid`)
				group by a1.`request_from`
			";
			$achieve_data6_1 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data6_1[0]['request_read_cno'])){
				$cno6_1[$k] = (int)$achieve_data6_1[0]['request_read_cno'];
			}
			//-----------------------------------------------
			$cno6_2[$k] = 0;
			$sql="
				SELECT count(rb1.`request_id`) as request_read_cno, a1.`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_user_request` as a1
				inner join `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev` as rb1 on (rb1.`request_id`=a1.`request_id`)
				WHERE `request_read`=1 and a1.`request_from`={$uid1[$k]}
				and rb1.`request_id` in
				(SELECT rb1.`request_id` FROM `mssr_forum`.`mssr_forum_article_book_rev` as b1 where b1.`book_sid`=rb1.`book_sid`) and (a1.`keyin_mdate` BETWEEN '".$data_from."' AND '2016/05/01')
				group by a1.`request_from`
			";
			$achieve_data6_2 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data6_2[0]['request_read_cno'])){
				$cno6_2[$k] = (int)$achieve_data6_2[0]['request_read_cno'];
			}
			//-----------------------------------------------
			$cno6_3[$k] = 0;
			$sql="
				SELECT count(rb1.`request_id`) as request_read_cno, a1.`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_user_request` as a1
				inner join `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev` as rb1 on (rb1.`request_id`=a1.`request_id`)
				WHERE `request_read`=1 and a1.`request_from`={$uid1[$k]}
				and rb1.`request_id` in
				(SELECT rb1.`request_id` FROM `mssr_forum`.`mssr_forum_article_book_rev` as b1 where b1.`book_sid`=rb1.`book_sid`) and (a1.`keyin_mdate` BETWEEN '2016/05/02' AND now())
				group by a1.`request_from`
			";
			$achieve_data6_3 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data6_3[0]['request_read_cno'])){
				$cno6_3[$k] = (int)$achieve_data6_3[0]['request_read_cno'];
			}
		//-----------------------------------------------發文
			$cno7_1[$k] = 0;
			$sql="
				SELECT count(a.`article_id`) as article_eagle_cno
				FROM `mssr_forum`.`mssr_forum_article`as a
				inner join `mssr_forum`.`mssr_forum_article_eagle_rev` as b on (a.`article_id`=b.`article_id`)
				WHERE `user_id`={$uid1[$k]} 
				group by `user_id`
			";
			$achieve_data7_1 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data7_1[0]['article_eagle_cno'])){$cno7_1[$k] =(int)$achieve_data7_1[0]['article_eagle_cno'];}		
			//-----------------------------------------------
			$cno7_2[$k] = 0;
			$sql="
				SELECT count(a.`article_id`) as article_eagle_cno,a.`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_article`as a
				inner join `mssr_forum`.`mssr_forum_article_eagle_rev` as b on (a.`article_id`=b.`article_id`)
				WHERE `user_id`={$uid1[$k]} and (a.`keyin_mdate` BETWEEN '".$data_from."' AND '2016/05/01')
				group by `user_id`
			";
			$achieve_data7_2 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data7_2[0]['article_eagle_cno'])){$cno7_2[$k] =(int)$achieve_data7_2[0]['article_eagle_cno'];}//-----------------------------------------------
			$cno7_3[$k] = 0;
			$sql="
				SELECT count(a.`article_id`) as article_eagle_cno,a.`keyin_mdate`
				FROM `mssr_forum`.`mssr_forum_article`as a
				inner join `mssr_forum`.`mssr_forum_article_eagle_rev` as b on (a.`article_id`=b.`article_id`)
				WHERE `user_id`={$uid1[$k]} and (a.`keyin_mdate` BETWEEN '2016/05/02' AND now())
				group by `user_id`
			";
			$achieve_data7_3 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data7_3[0]['article_eagle_cno'])){$cno7_3[$k] =(int)$achieve_data7_3[0]['article_eagle_cno'];}			
		
		
		
		//-----------------------------------------------將資料存進表格內
			echo "<tr>";
				//echo "<td> $k </td>";
				echo "<td> $uid1[$k] </td>";
				echo "<td> $school_name[$k] </td>";
				echo "<td> $grade[$k]年$classroom[$k]班 </td>";
				echo "<td> $cno1_1[$k] </td>";
				echo "<td> $cno1_2[$k] </td>";
				echo "<td> $cno1_3[$k] </td>";
				echo "<td> $cno2_1[$k] </td>";
				echo "<td> $cno2_2[$k] </td>";
				echo "<td> $cno2_3[$k] </td>";
				echo "<td> $cno3_1[$k] </td>";
				echo "<td> $cno3_2[$k] </td>";
				echo "<td> $cno3_3[$k] </td>";
				echo "<td> $cno4_1[$k] </td>";
				echo "<td> $cno4_2[$k] </td>";
				echo "<td> $cno4_3[$k] </td>";
				echo "<td> $cno5_1[$k] </td>";
				echo "<td> $cno5_2[$k] </td>";
				echo "<td> $cno5_3[$k] </td>";
				echo "<td> $cno7_1[$k] </td>";
				echo "<td> $cno7_2[$k] </td>";
				echo "<td> $cno7_3[$k] </td>";
				echo "<td> $cno6_1[$k] </td>";
				echo "<td> $cno6_2[$k] </td>";
				echo "<td> $cno6_3[$k] </td>";
			echo "</tr>";
		}
		echo "</table>";
		//-----------------------------------------------







?>