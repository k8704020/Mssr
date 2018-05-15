<?php
    //---------------------------------------------------
        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",5).'config/config.php');

        //外掛頁面檔
        require_once(str_repeat("../",3).'pages/code.php');

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
			

		//-------------------------------------------------
		echo "功能：「原系統」";
		echo "</br>";
		echo "---------------------------------------------";
		echo "</br>";
		echo "主題：最喜歡書中的人物：";
		echo "</br>";
		$join_number = array();
		//-------------------------------------------------
		$sql="
			SELECT 
				user.student.uid
				FROM user.student
					inner join user.class on user.class.class_code = user.student.class_code 
					inner join user.semester on user.class.semester_code = user.semester.semester_code 
				where 1=1 
					AND user.class.class_code in ('don_2015_2_3_4_3','gcx_2015_2_3_1_3','gcx_2015_2_4_1_3','gcx_2015_2_5_1_3','dat_2015_2_3_1_2','dat_2015_2_5_1_2','xql_2015_2_4_1_2','xql_2015_2_5_1_2','zhu_2015_2_5_1_2','chc_2015_2_3_2_2','gwh_2015_2_3_4_1','gwh_2015_2_3_6_1','gwh_2015_2_3_8_1','gwh_2015_2_4_1_1','gwh_2015_2_4_4_1','gwh_2015_2_4_9_1','gcl_2015_2_3_1_1','gcl_2015_2_4_1_1','xul_2015_2_4_1_2','xul_2015_2_4_5_2','xul_2015_2_4_6_2','rww_2015_2_3_3_1','rww_2015_2_3_4_1','rww_2015_2_4_1_1','rww_2015_2_4_2_1','rww_2015_2_4_3_1','rww_2015_2_4_4_1','rww_2015_2_4_6_1','chz_2015_2_3_1_1','chz_2015_2_3_3_1' )	
			group by user.student.uid
		";
		$get_data_uid = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_data_uid as $key =>$get_data_uid){
			$class_uid[$key] = $get_data_uid['uid'];
		}
		$result1 = implode(",",$class_uid);
		
		
		//-------------------------------------------------
		//找出發文的所有article_id
		//發文篇數
		$i = 0;
		$article_id = array();
		$sql="			
			SELECT a.`user_id`,am.`article_id`
			FROM `mssr_forum`.`dev_article_group_mission_rev` AS am
				INNER JOIN `mssr_forum`.`mssr_forum_article` AS a ON ( am.`article_id` = a.`article_id` )
			WHERE 1=1 
				AND `group_task_id` = 5 
				AND a.`article_from`= 3
				AND a.`user_id` in ($result1)
			group by am.`article_id`
		";
		$get_article_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_article_data as $key => $get_article_data){
			$article_id[$key] = $get_article_data['article_id'];
			$join_number[$i] = $get_article_data['user_id'];
			$i++;
		}
		$result2 = implode(",",$article_id);
		echo "發文篇數：".$i;
		echo "</br>";
		/* echo "<pre>"; 
		print_r($article_id);
		echo "</pre>"; */
		//echo $result;
		//-------------------------------------------------
		//被回覆的發文篇數
		$article_replied = 0;
		$sql="
			SELECT count(cno1) AS cno
			FROM (
			    SELECT count(ar.`reply_id`) as cno1
				FROM `mssr_forum`.`dev_reply_group_mission_rev` AS ar
					LEFT JOIN `mssr_forum`.`mssr_forum_reply` AS r ON ( ar.`reply_id` = r.`reply_id` )
					LEFT JOIN `user`.`student` AS st1 ON ( r.`user_id` = st1.`uid` ) 
					LEFT JOIN `user`.`class` AS c1 ON ( c1.`class_code` = st1.`class_code` ) 
					LEFT JOIN `user`.`semester` AS sem1 ON ( sem1.`semester_code` = c1.`semester_code` )
					LEFT JOIN `user`.`school` AS sch1 ON ( sch1.`school_code` = sem1.`school_code` )
				WHERE 1=1
					AND `group_task_id`=5
					AND ar.`article_id` in ($result2)
                GROUP BY ar.`article_id`
			) AS query
		";
		$get_article_replied_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		$article_replied = $get_article_replied_data[0]['cno'];
		echo "有被回覆的發文篇數：".$article_replied;
		echo "</br>";
		//-------------------------------------------------
		//誰回覆了這些發文，回覆了多少篇
		$reply_array = array();
		$sql="
			SELECT r.`user_id`,count(ar.`reply_id`) as cno
			FROM `mssr_forum`.`dev_reply_group_mission_rev` AS ar
				INNER JOIN `mssr_forum`.`mssr_forum_reply` AS r ON ( ar.`reply_id` = r.`reply_id` )				
			WHERE 1=1
				AND `group_task_id` = 5
				AND r.`reply_from`  = 3
				AND ar.`article_id` in ($result2)				
			Group by r.`user_id`			
		";
		$get_reply_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_reply_data as $key => $get_reply_data){
			$join_number[$i] = $get_reply_data['user_id'];
			$reply_array[$key] = $get_reply_data['cno'];
			$i++;
		}	
		$reply_cno = 0;
		for($j=0; $j<count($reply_array); $j++){
			
			$reply_cno+=$reply_array[$j];
		}
		
		//-------------------------------------------------

		echo "這些發文產生了多少回文：".$reply_cno;
		echo "</br>";
		
		//-------------------------------------------------
		//按讚->發文
		$article_like_array = array();
		$sql="
			SELECT
				a1.`user_id`,count(a1.`article_id`) as like_cno
			FROM `mssr_forum`.`mssr_forum_article_like_log` as a1
				INNER join `mssr_forum`.`mssr_forum_article` as a2 ON (a1.`article_id` = a2.`article_id`)
				INNER join `mssr_forum`.`dev_article_group_mission_rev` as am ON (am.`article_id` = a1.`article_id`)
			WHERE 1=1
				AND am.`group_task_id`=5
				AND a2.`article_from`=3
				AND a1.`article_id` in ($result2)	
			group by a1.`user_id`		
		";
		$get_article_like = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_article_like as $key => $get_article_like){
			$join_number[$i] = $get_article_like['user_id'];
			$article_like_array[$key] = $get_article_like['like_cno'];		
			$i++;
		}
		$article_like_cno = 0;
		for($j=0; $j<count($article_like_array); $j++){
			
			$article_like_cno+=$article_like_array[$j];
		}
		
		echo "這些發文獲得了多少個讚：".$article_like_cno;
		echo "</br>";
		//-------------------------------------------------
		//找出回文的所有reply_id
		$reply_id = array();
		$sql="
			SELECT ar.`reply_id`
			FROM `mssr_forum`.`dev_reply_group_mission_rev` AS ar
				INNER JOIN `mssr_forum`.`mssr_forum_reply` AS r ON ( ar.`reply_id` = r.`reply_id` )				
			WHERE 1=1
				AND `group_task_id` = 5
				AND r.`reply_from`  = 3
				AND ar.`article_id` in ($result2)				
			Group by ar.`reply_id`			
		";
		$get_reply_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_reply_data as $key =>$get_reply_data){
			$reply_id[$key] = $get_reply_data['reply_id'];
		}
		$result3 = implode(",",$reply_id);
		
		
		//-------------------------------------------------
		//按讚->回文
		if(count($reply_id)>0){
			$sql="
				SELECT
					a1.`user_id`,count(a1.`reply_id`) as like_cno
				FROM `mssr_forum`.`mssr_forum_reply_like_log` as a1
					inner join `mssr_forum`.`mssr_forum_reply` as a2 ON (a1.`reply_id` = a2.`reply_id`)
					inner join `mssr_forum`.`dev_reply_group_mission_rev` as rm ON (rm.`reply_id` = a1.`reply_id`)
					
				WHERE 1=1
					AND rm.`group_task_id`=5
					AND a2.`reply_from`=3
					AND a1.`reply_id` in ($result3)			
				group by a1.`user_id`		
			";
			$get_reply_like = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			foreach($get_reply_like as $key => $get_reply_like){
				$join_number[$i] = $get_reply_like['user_id'];
				$reply_like_array[$key] = $get_reply_like['like_cno'];	
				$i++;
			}
			
			$reply_like_cno = 0;
			for($j=0; $j<count($reply_like_array); $j++){
				
				$reply_like_cno += $reply_like_array[$j];
				
			}
			echo "這些回文獲得了多少個讚：".$reply_like_cno;
			echo "</br>";
		}else{
			$reply_like_cno = 0;
			echo "這些回文獲得了多少個讚：".$reply_like_cno;
			echo "</br>";
			
		}
		//-------------------------------------------------
		/* echo "<pre>";
		print_r($join_number);
		echo "</pre>"; */
		$result = 0;
		$result = array_unique($join_number);
		echo "<pre>";
		print_r($result);
		echo "</pre>";
		echo "總參與人數：".COUNT($result);
		//-------------------------------------------------

/* 		echo "<table border=1>";
		echo "<tr style='background-color:yellow'>";
			echo "<td>個數</td>";
			echo "<td>任務編號</td>";
			echo "<td>任務主題</td>";
			echo "<td>傳遞者編號</td>";
			echo "<td>使用者編號</td>";
			echo "<td>學校名稱</td>";
			echo "<td>學校代號</td>";
			echo "<td>班級</td>";
			echo "<td>任務狀態</td>";
			echo "<td>開始時間</td>";
			echo "<td>身分</td>";

		echo "</tr>";
		$i = 0;//編號
		$accept = 0;//接受者
		$complete = 0;//完成者
		$reject = 0;//拒絕者
		$ing = 0;//進行者
		$no_see = 0;//未看到者
		$no_reply = 0;//未回應者
		foreach($get_data as $key => $get_data){
			$i++;
			$task_id[$key]	   	   = $get_data['group_task_id'];
			$task_name[$key]   	   = $get_data['gask_topic'];
			$deliver_uid[$key]	   = $get_data['deliver_uid'];
			$accept_uid[$key]  	   = $get_data['accept_uid'];
			$school_name[$key]	   = $get_data['school_name'];
			$grade[$key]		   = $get_data['grade'];
			$classroom[$key]	   = $get_data['classroom'];
			$mission_state[$key]   = $get_data['mission_state'];
			$start_time[$key]      = $get_data['start_time'];
			$status_name[$key]	   = $get_data['status_name'];
			$school_code[$key]     = $get_data['school_code'];

			if($mission_state[$key]==1||$mission_state[$key]==2){
				$accept++;
			}
			if($mission_state[$key]==0){
				$no_see++;
			}
			if($mission_state[$key]==1){
				$complete++;
			}		
			if($mission_state[$key]==2){
				$ing++;
			}
			if($mission_state[$key]==3){
				$reject++;
			}
			if($mission_state[$key]==5){
				$no_reply++;
			}

			echo "<tr>";
				echo "<td> $i </td>";
				echo "<td> $task_id[$key] </td>";
				echo "<td> $task_name[$key] </td>";
				echo "<td> $deliver_uid[$key] </td>";
				echo "<td> $accept_uid[$key] </td>";
				echo "<td> $school_name[$key] </td>";
				echo "<td> $school_code[$key] </td>";
				echo "<td> $grade[$key]年$classroom[$key]班 </td>";
				echo "<td> $mission_state[$key] </td>";
				echo "<td> $start_time[$key] </td>";
				echo "<td> $status_name[$key] </td>";

			echo "</tr>";
		} */
		//-------------------------------------------------
		//--------------------------------------------------------------------------------------------------
		echo "</br>";
		echo "-------------------------------------------------";
		echo "</br>";
		echo "主題：最喜歡書中的故事：";
		echo "</br>";
		
		$join_number = array();
		//-------------------------------------------------
		$sql="
			SELECT 
				user.student.uid
				FROM user.student
					inner join user.class on user.class.class_code = user.student.class_code 
					inner join user.semester on user.class.semester_code = user.semester.semester_code 
				where 1=1 
					AND user.class.class_code in ('don_2015_2_3_4_3','gcx_2015_2_3_1_3','gcx_2015_2_4_1_3','gcx_2015_2_5_1_3','dat_2015_2_3_1_2','dat_2015_2_5_1_2','xql_2015_2_4_1_2','xql_2015_2_5_1_2','zhu_2015_2_5_1_2','chc_2015_2_3_2_2','gwh_2015_2_3_4_1','gwh_2015_2_3_6_1','gwh_2015_2_3_8_1','gwh_2015_2_4_1_1','gwh_2015_2_4_4_1','gwh_2015_2_4_9_1','gcl_2015_2_3_1_1','gcl_2015_2_4_1_1','xul_2015_2_4_1_2','xul_2015_2_4_5_2','xul_2015_2_4_6_2','rww_2015_2_3_3_1','rww_2015_2_3_4_1','rww_2015_2_4_1_1','rww_2015_2_4_2_1','rww_2015_2_4_3_1','rww_2015_2_4_4_1','rww_2015_2_4_6_1','chz_2015_2_3_1_1','chz_2015_2_3_3_1' )	
			group by user.student.uid
		";
		$get_data_uid = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_data_uid as $key =>$get_data_uid){
			$class_uid[$key] = $get_data_uid['uid'];
		}
		$result1 = implode(",",$class_uid);
		//找出發文的所有article_id
		//發文篇數
		$i = 0;
		$article_id = array();
		$sql="			
			SELECT a.`user_id`,am.`article_id`
			FROM `mssr_forum`.`dev_article_group_mission_rev` AS am
				INNER JOIN `mssr_forum`.`mssr_forum_article` AS a ON ( am.`article_id` = a.`article_id` )
			WHERE 1=1
				AND `group_task_id` = 6
				AND a.`article_from`= 3
				AND a.`user_id` in ($result1)
			group by am.`article_id`
		";
		$get_article_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_article_data as $key => $get_article_data){
			$article_id[$key] = $get_article_data['article_id'];
			$join_number[$i] = $get_article_data['user_id'];
			$i++;
		}
		$result2 = implode(",",$article_id);
		echo "發文篇數：".$i;
		echo "</br>";
		/* echo "<pre>"; 
		print_r($article_id);
		echo "</pre>"; */
		//echo $result;
		//-------------------------------------------------
		//被回覆的發文篇數
		$article_replied = 0;
		$sql="
			SELECT count(cno1) AS cno
			FROM (
			    SELECT count(ar.`reply_id`) as cno1
				FROM `mssr_forum`.`dev_reply_group_mission_rev` AS ar
					LEFT JOIN `mssr_forum`.`mssr_forum_reply` AS r ON ( ar.`reply_id` = r.`reply_id` )
					LEFT JOIN `user`.`student` AS st1 ON ( r.`user_id` = st1.`uid` ) 
					LEFT JOIN `user`.`class` AS c1 ON ( c1.`class_code` = st1.`class_code` ) 
					LEFT JOIN `user`.`semester` AS sem1 ON ( sem1.`semester_code` = c1.`semester_code` )
					LEFT JOIN `user`.`school` AS sch1 ON ( sch1.`school_code` = sem1.`school_code` )
				WHERE 1=1
					AND `group_task_id`=6
					AND ar.`article_id` in ($result2)
                GROUP BY ar.`article_id`
			) AS query
		";
		$get_article_replied_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		$article_replied = $get_article_replied_data[0]['cno'];
		echo "有被回覆的發文篇數：".$article_replied;
		echo "</br>";
		//-------------------------------------------------
		//誰回覆了這些發文，回覆了多少篇
		$reply_array = array();
		$sql="
			SELECT r.`user_id`,count(ar.`reply_id`) as cno
			FROM `mssr_forum`.`dev_reply_group_mission_rev` AS ar
				INNER JOIN `mssr_forum`.`mssr_forum_reply` AS r ON ( ar.`reply_id` = r.`reply_id` )				
			WHERE 1=1
				AND `group_task_id` = 6
				AND r.`reply_from`  = 3
				AND ar.`article_id` in ($result2)				
			Group by r.`user_id`			
		";
		$get_reply_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_reply_data as $key => $get_reply_data){
			$join_number[$i] = $get_reply_data['user_id'];
			$reply_array[$key] = $get_reply_data['cno'];
			$i++;
		}	
		$reply_cno = 0;
		for($j=0; $j<count($reply_array); $j++){
			
			$reply_cno+=$reply_array[$j];
		}
		
		//-------------------------------------------------

		echo "這些發文產生了多少回文：".$reply_cno;
		echo "</br>";
		
		//-------------------------------------------------
		//按讚->發文
		$article_like_array = array();
		$sql="
			SELECT
				a1.`user_id`,count(a1.`article_id`) as like_cno
			FROM `mssr_forum`.`mssr_forum_article_like_log` as a1
				INNER join `mssr_forum`.`mssr_forum_article` as a2 ON (a1.`article_id` = a2.`article_id`)
				INNER join `mssr_forum`.`dev_article_group_mission_rev` as am ON (am.`article_id` = a1.`article_id`)
			WHERE 1=1
				AND am.`group_task_id`=6
				
				AND a2.`article_from`=3
				AND a1.`article_id` in ($result2)	
			group by a1.`user_id`		
		";
		$get_article_like = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_article_like as $key => $get_article_like){
			$join_number[$i] = $get_article_like['user_id'];
			$article_like_array[$key] = $get_article_like['like_cno'];		
			$i++;
		}
		$article_like_cno = 0;
		for($j=0; $j<count($article_like_array); $j++){
			
			$article_like_cno+=$article_like_array[$j];
		}
		
		echo "這些發文獲得了多少個讚：".$article_like_cno;
		echo "</br>";
		//-------------------------------------------------
		//找出回文的所有reply_id
		$reply_id = array();
		$sql="
			SELECT ar.`reply_id`
			FROM `mssr_forum`.`dev_reply_group_mission_rev` AS ar
				INNER JOIN `mssr_forum`.`mssr_forum_reply` AS r ON ( ar.`reply_id` = r.`reply_id` )				
			WHERE 1=1
				
				AND `group_task_id` = 6
				AND r.`reply_from`  = 3
				AND ar.`article_id` in ($result2)				
			Group by ar.`reply_id`			
		";
		$get_reply_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_reply_data as $key =>$get_reply_data){
			$reply_id[$key] = $get_reply_data['reply_id'];
		}
		$result3 = implode(",",$reply_id);
		
		
		//-------------------------------------------------
		//按讚->回文
		if(count($reply_id)>0){
			$sql="
				SELECT
					a1.`user_id`,count(a1.`reply_id`) as like_cno
				FROM `mssr_forum`.`mssr_forum_reply_like_log` as a1
					inner join `mssr_forum`.`mssr_forum_reply` as a2 ON (a1.`reply_id` = a2.`reply_id`)
					inner join `mssr_forum`.`dev_reply_group_mission_rev` as rm ON (rm.`reply_id` = a1.`reply_id`)
					
				WHERE 1=1
					AND rm.`group_task_id`=6
					
					AND a2.`reply_from`=3
					AND a1.`reply_id` in ($result3)			
				group by a1.`user_id`		
			";
			$get_reply_like = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			foreach($get_reply_like as $key => $get_reply_like){
				$join_number[$i] = $get_reply_like['user_id'];
				$reply_like_array[$key] = $get_reply_like['like_cno'];	
				$i++;
			}
			
			$reply_like_cno = 0;
			for($j=0; $j<count($reply_like_array); $j++){
				
				$reply_like_cno += $reply_like_array[$j];
				
			}
			echo "這些回文獲得了多少個讚：".$reply_like_cno;
			echo "</br>";
		}else{
			$reply_like_cno = 0;
			echo "這些回文獲得了多少個讚：".$reply_like_cno;
			echo "</br>";
			
		}
		
		//-------------------------------------------------
		/* echo "<pre>";
		print_r($join_number);
		echo "</pre>"; */
		$result = 0;
		$result = array_unique($join_number);
		echo "<pre>";
		print_r($result);
		echo "</pre>";
		echo "總參與人數：".COUNT($result);






?>