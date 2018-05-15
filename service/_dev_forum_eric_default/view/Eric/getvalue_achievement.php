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
            APP_ROOT.'service/_dev_forum_eric_default/inc/code'
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

	if(isset($arrys_sess_login_info[0]['uid'])){

		$uid = (int)$arrys_sess_login_info[0]['uid'];//存取session
		$mission_type = $_POST["type"];

		switch($mission_type){
		case 0: //成就總覽
		//-------------------------------------------
		$cno = array();//存取每個成就的次數
		//文章按讚
		$article_like_cno = 0;
		$reply_like_cno   = 0;
		$sql="
			SELECT
				count(`article_id`) as like_cno
			FROM `mssr_forum`.`mssr_forum_article_like_log`
			WHERE `user_id`={$uid}
			group by `user_id`
			UNION ALL
			SELECT
				count(`reply_id`) as like_cno
			FROM `mssr_forum`.`mssr_forum_reply_like_log`
			WHERE `user_id`={$uid}
			group by `user_id`
		";
		$achieve_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);//array(x,y) 第x筆開始找y筆
		if(isset($achieve_data[0]['like_cno'])){$article_like_cno =	(int)$achieve_data[0]['like_cno'];}
		if(isset($achieve_data[1]['like_cno'])){$reply_like_cno   =	(int)$achieve_data[1]['like_cno'];}
		$cno[0] = $article_like_cno + $reply_like_cno;
		//文章被按讚
		$article_liked_cno = 0;
		$reply_liked_cno   = 0;
		$sql="
			SELECT
				sum(`article_like_cno`) as liked_cno
			FROM `mssr_forum`.`mssr_forum_article`
			WHERE `user_id`={$uid}
			group by `user_id`
			UNION ALL
			SELECT
				sum(`reply_like_cno`) as liked_cno
			FROM `mssr_forum`.`mssr_forum_reply`
			WHERE `user_id`={$uid}
			group by `user_id`
		";
		$achieve_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		if(isset($achieve_data[0]['liked_cno'])){$article_liked_cno = (int)$achieve_data[0]['liked_cno'];}
		if(isset($achieve_data[1]['liked_cno'])){$reply_liked_cno   = (int)$achieve_data[1]['liked_cno'];}
		$cno[1] = $article_liked_cno + $reply_liked_cno;
		//回覆文章
		$cno[2] = 0;
		$sql="
			SELECT count(`reply_id`) as reply_cno
			FROM `mssr_forum`.`mssr_forum_reply`
			WHERE `user_id`={$uid}
		";
		$achieve_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		if(isset($achieve_data[0]['reply_cno'])){$cno[2] =(int)$achieve_data[0]['reply_cno'];}
		//文章被回覆
		$cno[3] = 0;
		$sql="
			SELECT a.`user_id` , a.`article_id` , COUNT( r.`reply_id` ) as replied_cno
			FROM  `mssr_forum`.`mssr_forum_article` AS a
			INNER JOIN  `mssr_forum`.`mssr_forum_reply` AS r ON ( a.`article_id` = r.`article_id` )
			WHERE a.`user_id`={$uid}
		";
		$achieve_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		if(isset($achieve_data[0]['replied_cno'])){$cno[3] =(int)$achieve_data[0]['replied_cno'];}
		//鷹架發文
		$cno[4] = 0;
		$sql="
			SELECT count(a.`article_id`) as article_eagle_cno
			FROM `mssr_forum`.`mssr_forum_article`as a
			inner join `mssr_forum`.`mssr_forum_article_eagle_rev` as b on (a.`article_id`=b.`article_id`)
			WHERE `user_id`={$uid} and `eagle_code`!=0
			group by `user_id`
		";
		$achieve_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		if(isset($achieve_data[0]['article_eagle_cno'])){$cno[4] =(int)$achieve_data[0]['article_eagle_cno'];}
		//請求推薦
		$cno[5] = 0;
		$sql="
			SELECT count(rb1.`request_id`) as request_read_cno
			FROM `mssr_forum`.`mssr_forum_user_request` as a1
			inner join `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev` as rb1 on (rb1.`request_id`=a1.`request_id`)
			WHERE `request_read`=1 and a1.`request_from`={$uid}
			and rb1.`request_id` in
			(SELECT rb1.`request_id` FROM `mssr_forum`.`mssr_forum_article_book_rev` as b1 where b1.`book_sid`=rb1.`book_sid`)
			group by a1.`request_from`
		";
		$achieve_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		if(isset($achieve_data[0]['request_read_cno'])){$cno[5] = (int)$achieve_data[0]['request_read_cno'];}

		//-------------------------------------------
		for( $i=1; $i<=6; $i++){
			$sql="
				SELECT `task_type`,`achieve_name`,`achieve_content`,`level_degree`,`achieve_value`,`available_score`,`person_task_sid`
				FROM `mssr_forum`.`dev_person_achievement` as a
				inner join `mssr_forum`.`dev_person_achievement_detail` as b on a.`person_task_id`=b.`person_task_id`
				where a.`person_task_id`={$i}
			";
			$get_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

			foreach($get_data as $key => $get_data){
				$level_degree[$i-1][$key]    = (int)$get_data["level_degree"];
				$achieve_value[$i-1][$key]   = (int)$get_data["achieve_value"];
				$available_score[$i-1][$key] = (int)$get_data["available_score"];
				$sid[$i-1][$key]			 = (int)$get_data["person_task_sid"];
				$level_degree[$i-1][$key]  	 = (int)$get_data["level_degree"];
			}
			$Arrlevel = array();//暫存個數
			for( $x=1; $x<=5; $x++){//比較學生的表現的等級
				if($cno[$i-1] >= $achieve_value[$i-1][$x-1]){
					$Arrlevel[$i-1][$x-1]=$achieve_value[$i-1][$x-1];
				}else{
					$Arrlevel[$i-1][$x-1]=$cno[$i-1];
					$level_now[$i-1]=(int)$x; //當前正在進行的level
					break;
				}
			}
			$level_past = array();
			$level_past[$i-1] = 0;
			$get_score = 0;
			//找出資料庫中，使用者最新(MAX)完成的任務
			$sql="SELECT * FROM `mssr_forum`.`dev_complete_achievement_log` as c
				inner join `mssr_forum`.`dev_person_achievement_detail` as b on (c.`person_task_sid`=b.`person_task_sid`)
				inner join `mssr_forum`.`dev_person_achievement` as a on (a.`person_task_id`=b.`person_task_id`)
				where c.`u_id`={$uid} and a.`person_task_id`={$i}
				order by `level` desc
			";
			$get_level =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($get_level[$i-1]["level"])){
				$level_past[$i-1] =(int)$get_level[$i-1]["level"]; //取得過去資料中，存取最新完成的等級
			}
			if($level_now[$i-1] > ($level_past[$i-1]+1)){//將高於過去等級的資料作處理
				for($y=($level_past[$i-1]+1); $y<$level_now[$i-1]; $y++){

					$get_score += $available_score[$i-1][$y-1];//儲存通過任務的分數

					$sql="
						INSERT INTO `mssr_forum`.`dev_complete_achievement_log`(u_id, person_task_sid, level, finsih_time)
						VALUES ({$uid},{$sid[$i-1][$y-1]},{$level_degree[$i-1][$y-1]}, NOW())
					";
					//echo $sql;
					//die();
					$insert_level =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				}
					$sql="
						SELECT *
						FROM `mssr_forum`.`dev_member_score`
						WHERE `u_id`={$uid}
					";
					$get_member_sid =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				if(isset($get_member_sid["s_id"])){
				//當member_score表中，有使用者的資料時，更新一筆最新的資料

					$sql="
						UPDATE `mssr_forum`.`dev_member_score`
						SET `score`={$get_score}
						WHERE `u_id`={$uid}
					";
					$update_score =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				}else if(!isset($get_member_sid["s_id"])){//當member_score表中，"沒有"使用者的資料時，新增一筆最新的資料

					$sql="
						INSERT INTO `mssr_forum`.`dev_member_score`(`u_id`,`score`)
						VALUES ({$uid},{$get_score})
					";
					$insert_score =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				}

			}
		}
		//-------------------------------------------
		$Arrtotal = array();//暫存所有成就的進度
		$Arrtotal_process = array();//暫存所有成就的進度
		$total_number =0;
		for( $x=1; $x<=6; $x++){
			$sql="
				SELECT (case when MAX(`level`) is null then 0 Else  MAX(`level`) END) as ans
				FROM `mssr_forum`.`dev_complete_achievement_log` as c
				inner join `mssr_forum`.`dev_person_achievement_detail` as b on (c.`person_task_sid`=b.`person_task_sid`)
				inner join `mssr_forum`.`dev_person_achievement` as a on  (a.`person_task_id`=b.`person_task_id`)
				WHERE `u_id`={$uid} and a.`person_task_id`={$x}
			";
			$achieve_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$Arrtotal[$x-1] 	    = (int)$achieve_data[0]['ans'];
			$total_number		   += (int)$achieve_data[0]['ans'];
			$Arrtotal_process[$x-1] = floor((int)$achieve_data[0]['ans']/5*100);
		}
			$Arrtotal[6]         =$total_number;
			$Arrtotal_process[6] =floor($total_number/30*100);

			$sql="
				SELECT *
				FROM `mssr_forum`.`dev_person_achievement`
			";
			$get_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			foreach($get_data as $key => $get_data){
				$achieve_type[$key] = $get_data["task_type"];//取得成就的類型
			}

			$sql="
				SELECT `sex`
				FROM `user`.`member`
				WHERE `uid`={$uid}
			";
			$get_sex =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$sex = $get_sex[0]['sex'];

			$sql="
				SELECT *
				FROM `mssr_forum`.`dev_member_title`
				WHERE `title_sex`={$sex}
			";
			$get_title_score =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			foreach($get_title_score as $key => $get_title_score){
				$title_name[$key] = $get_title_score["title_name"];//取得頭銜的名稱
				$title_score[$key] = $get_title_score["title_score"];//取得頭銜的分數
			}

			$sql="
				SELECT `score`
				FROM `mssr_forum`.`dev_member_score`
				WHERE `u_id`={$uid}
			";
			$get_data_score =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($get_data_score[0]['score'])){//取得目前的分數
				$get_score = (int)$get_data_score[0]['score'];
			}else{ $get_score = 0 ;}

			for( $x=1; $x<=7; $x++){
				if($get_score < $title_score[$x-1]){
					$title_next = $x;//取得下一個頭銜的等級
					break;
				}
			}
			$Arrtotal_process[7] =floor((($get_score-$title_score[$title_next-2])/$title_score[$title_next-1])*100);
			echo json_encode(array("Arrtotal"=>$Arrtotal,"Arrtotal_process"=>$Arrtotal_process,"achieve_type"=>$achieve_type,"title_name"=>$title_name,"title_score"=>$title_score,"title_next"=>$title_next,"get_score"=>$get_score,"sex"=>$sex));

		break;
		case 1: //文章按讚
			$article_like_cno = 0;
			$reply_like_cno   = 0;
			$sql="
                SELECT
                    count(`article_id`) as like_cno
                FROM `mssr_forum`.`mssr_forum_article_like_log`
                WHERE `user_id`={$uid}
				group by `user_id`
				UNION ALL
				SELECT
                    count(`reply_id`) as like_cno
                FROM `mssr_forum`.`mssr_forum_reply_like_log`
                WHERE `user_id`={$uid}
				group by `user_id`
            ";
			$achieve_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);//array(x,y) 第x筆開始找y筆
			if(isset($achieve_data[0]['like_cno'])){$article_like_cno =	(int)$achieve_data[0]['like_cno'];}
			if(isset($achieve_data[1]['like_cno'])){$reply_like_cno   =	(int)$achieve_data[1]['like_cno'];}
			$cno = $article_like_cno + $reply_like_cno;
			/*echo "<Pre>";
			print_r($cno);
			echo "</Pre>";*/
		break;
		case 2: //文章被按讚
			$article_liked_cno = 0;
			$reply_liked_cno   = 0;
			$sql="
				SELECT
					sum(`article_like_cno`) as liked_cno
				FROM `mssr_forum`.`mssr_forum_article`
				WHERE `user_id`={$uid}
				group by `user_id`
				UNION ALL
				SELECT
					sum(`reply_like_cno`) as liked_cno
				FROM `mssr_forum`.`mssr_forum_reply`
				WHERE `user_id`={$uid}
				group by `user_id`
			";
			$achieve_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data[0]['liked_cno'])){$article_liked_cno = (int)$achieve_data[0]['liked_cno'];}
			if(isset($achieve_data[1]['liked_cno'])){$reply_liked_cno   = (int)$achieve_data[1]['liked_cno'];}
			$cno = $article_liked_cno + $reply_liked_cno;

		break;
		case 3: //回覆文章
			$cno = 0;
			$sql="
				SELECT count(`reply_id`) as reply_cno
				FROM `mssr_forum`.`mssr_forum_reply`
				WHERE `user_id`={$uid}
			";
			$achieve_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data[0]['reply_cno'])){$cno =(int)$achieve_data[0]['reply_cno'];}

		break;
		case 4: //文章被回覆
			$cno = 0;
			$sql="
				SELECT a.`user_id` , a.`article_id` , COUNT( r.`reply_id` ) as replied_cno
				FROM  `mssr_forum`.`mssr_forum_article` AS a
				INNER JOIN  `mssr_forum`.`mssr_forum_reply` AS r ON ( a.`article_id` = r.`article_id` )
				WHERE a.`user_id`={$uid}
			";
			$achieve_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data[0]['replied_cno'])){$cno =(int)$achieve_data[0]['replied_cno'];}

		break;
		case 5: //鷹架發文
			$cno = 0;
			$sql="
				SELECT count(a.`article_id`) as article_eagle_cno
				FROM `mssr_forum`.`mssr_forum_article`as a
				inner join `mssr_forum`.`mssr_forum_article_eagle_rev` as b on (a.`article_id`=b.`article_id`)
				WHERE `user_id`={$uid} and `eagle_code`!=0
				group by `user_id`
			";
			$achieve_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data[0]['article_eagle_cno'])){$cno =(int)$achieve_data[0]['article_eagle_cno'];}

		break;
		case 6: //請求推薦
			$sql="
				SELECT count(rb1.`request_id`) as request_read_cno
				FROM `mssr_forum`.`mssr_forum_user_request` as a1
				inner join `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev` as rb1 on (rb1.`request_id`=a1.`request_id`)
				WHERE `request_read`=1 and a1.`request_from`={$uid}
				and rb1.`request_id` in
				(SELECT rb1.`request_id` FROM `mssr_forum`.`mssr_forum_article_book_rev` as b1 where b1.`book_sid`=rb1.`book_sid`)
				group by a1.`request_from`
			";
			$achieve_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($achieve_data[0]['request_read_cno'])){
				$cno = (int)$achieve_data[0]['request_read_cno'];
			}else{ $cno=0; }
		break;
		}
	}
		if($mission_type!=0){//成就類型是個別種類才執行
			$sql="
				SELECT `task_type`,`achieve_name`,`achieve_content`,`level_degree`,`achieve_value`,`available_score`,`person_task_sid`
				FROM `mssr_forum`.`dev_person_achievement` as a
				inner join `mssr_forum`.`dev_person_achievement_detail` as b on a.`person_task_id`=b.`person_task_id`
				where a.`person_task_id`={$mission_type}
			";
			$get_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

			foreach($get_data as $key => $get_data){
				$achieve_name[$key]    =  $get_data["achieve_name"];
				$achieve_content[$key] =  $get_data["achieve_content"];
				$level_degree[$key]    = (int)$get_data["level_degree"];
				$achieve_value[$key]   = (int)$get_data["achieve_value"];
				$sid[$key]			   = (int)$get_data["person_task_sid"];
				$available_score[$key] = (int)$get_data["available_score"];
			}
			$Arrlevel = array();//暫存個數
			$Arrprocess = array();//暫存進度比例
			for( $x=1; $x<=5; $x++){//比較學生的表現的等級
				if($cno >= $achieve_value[$x-1]){
					$Arrlevel[$x-1]=$achieve_value[$x-1];
				}else{
					$Arrlevel[$x-1]=$cno;
					$level_now=(int)$x; //當前正在進行的level
					break;
				}
			}
			for( $x=$level_now; $x<5; $x++){ $Arrlevel[$x]=0; }
			for( $x=1; $x<=5; $x++){
				if(floor(($Arrlevel[$x-1]/$achieve_value[$x-1])*100)<=100){
					$Arrprocess[$x-1]=floor(($Arrlevel[$x-1]/$achieve_value[$x-1])*100);
				}else{ $Arrprocess[$x-1]=100; }//超過100%則維持進度為100
			}
			echo json_encode(array("cno"=>$cno,"achieve_name"=>$achieve_name,"achieve_content"=>$achieve_content,"achieve_value"=>$achieve_value,"level_degree"=>$level_degree,"Arrlevel"=>$Arrlevel,"Arrprocess"=>$Arrprocess,"level_now"=>$level_now));

			$get_score=0;
			$level_past=0;
			//找出資料庫中，使用者最新(MAX)完成的任務
			$sql="SELECT * FROM `mssr_forum`.`dev_complete_achievement_log` as c
				inner join `mssr_forum`.`dev_person_achievement_detail` as b on (c.`person_task_sid`=b.`person_task_sid`)
				inner join `mssr_forum`.`dev_person_achievement` as a on (a.`person_task_id`=b.`person_task_id`)
				where c.`u_id`={$uid} and a.`person_task_id`={$mission_type}
				order by `level` desc
			";
			$get_level =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($get_level[0]["level"])){
				$level_past =(int)$get_level[0]["level"]; //取得過去資料中，存取最新完成的等級
			}

			if($level_now > $level_past+1){//將高於過去等級的資料作處理
				for($y=$level_past+1; $y<$level_now; $y++){

					$get_score += $available_score[$y-1];//儲存通過任務的分數

					$sql="
						INSERT INTO `mssr_forum`.`dev_complete_achievement_log`(u_id, person_task_sid, level, finsih_time)
						VALUES ({$uid},{$sid[$y-1]},{$level_degree[$y-1]}, NOW())
					";
					//echo $sql;
					//die();
					$insert_level =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				}
					$sql="
						SELECT *
						FROM `mssr_forum`.`dev_member_score`
						WHERE `u_id`={$uid}
					";
					$get_member_sid =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				if(isset($get_member_sid["s_id"])){//當member_score表中，"沒有"使用者的資料時，新增一筆最新的資料

					$sql="
						UPDATE `mssr_forum`.`dev_member_score`
						SET `score`={$get_score}
						WHERE `u_id`={$uid}
					";
					$update_score =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				}else{//當member_score表中，有使用者的資料時，更新一筆最新的資料

					$sql="
						INSERT INTO `mssr_forum`.`dev_member_score`(`u_id`,`score`)
						VALUES ({$uid},{$get_score})
					";
					$insert_score =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				}

			}
 		}







?>