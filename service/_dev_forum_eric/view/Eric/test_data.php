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


		$sql="
			SELECT 
				a.`group_task_id`,b.`deliver_uid` ,b.`accept_uid`, b.`step_number`, b.`step_state`,c.`gask_topic`,b.`start_step_time`,b.`end_step_time`,
				sch1.`school_name`,c1.`grade`,c1.`classroom`
			FROM `mssr_forum`.`dev_complete_mission_log` as a
				INNER JOIN `mssr_forum`.`dev_mission_step_log` as b ON (a.`accept_uid` = b.`accept_uid`)
				INNER JOIN `mssr_forum`.`dev_group_mission` as c ON (a.`group_task_id` = c.`group_task_id`)
				LEFT JOIN  `user`.`student` AS st1 ON ( a.`accept_uid` = st1.`uid` ) 
				LEFT JOIN  `user`.`class` AS c1 ON ( c1.`class_code` = st1.`class_code` ) 
				LEFT JOIN  `user`.`semester` AS sem1 ON ( sem1.`semester_code` = c1.`semester_code` ) 
				LEFT JOIN  `user`.`school` AS sch1 ON ( sch1.`school_code` = sem1.`school_code` )
			WHERE 
				a.`mission_state`!=0 and b.`accept_uid`!=2 and b.`accept_uid`!=93126 and b.`accept_uid`!=36152 and b.`accept_uid`!=5030 and sem1.`semester_code` like '%2015_2%' and sch1.`school_name` in ('中平國小','興安國小','九德國小','僑忠國小')
			GROUP BY b.`master_atask_id`
		";
		$get_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		echo "<table border=1>";
		echo "<tr>";
			echo "<td>任務編號</td>";
			echo "<td>任務主題</td>";
			echo "<td>傳遞者編號</td>";
			echo "<td>使用者編號</td>";
			echo "<td>學校名稱</td>";
			echo "<td>班級</td>";
			echo "<td>任務步驟</td>";
			echo "<td>任務狀態</td>";
			echo "<td>步驟開始時間</td>";
			echo "<td>步驟結束時間</td>";
		echo "</tr>";
		foreach($get_data as $key => $get_data){
			$task_id[$key]	   	   = $get_data['group_task_id'];
			$task_name[$key]   	   = $get_data['gask_topic'];
			$deliver_uid[$key]	   = $get_data['deliver_uid'];
			$accept_uid[$key]  	   = $get_data['accept_uid'];
			$school_name[$key]	   = $get_data['school_name'];
			$grade[$key]		   = $get_data['grade'];
			$classroom[$key]	   = $get_data['classroom'];
			$step_number[$key] 	   = $get_data['step_number'];
			$step_state[$key]      = $get_data['step_state'];
			$step_start_time[$key] = $get_data['start_step_time'];
			$step_end_time[$key]   = $get_data['end_step_time'];

			echo "<tr>";
				echo "<td> $task_id[$key] </td>";
				echo "<td> $task_name[$key] </td>";
				echo "<td> $deliver_uid[$key] </td>";
				echo "<td> $accept_uid[$key] </td>";
				echo "<td> $school_name[$key] </td>";
				echo "<td> $grade[$key]年$classroom[$key]班 </td>";
				echo "<td> $step_number[$key] </td>";
				echo "<td> $step_state[$key] </td>";
				echo "<td> $step_start_time[$key] </td>";
				echo "<td> $step_end_time[$key] </td>";
			echo "</tr>";
		}
		echo "</table>";
		//--------------------------------------------------------------
		$sql="
			SELECT distinct
				a.`group_task_id`,a.`deliver_uid` ,a.`accept_uid` ,c.`gask_topic`,a.`mission_state`,
				sch1.`school_name`,c1.`grade`,c1.`classroom`,a.`start_time`,sa.`name` as status_name ,p.`status`
			FROM `mssr_forum`.`dev_complete_mission_log` as a
				INNER JOIN `mssr_forum`.`dev_group_mission` as c ON (a.`group_task_id` = c.`group_task_id`)
				LEFT JOIN  `user`.`student` AS st1 ON ( a.`accept_uid` = st1.`uid` ) 
				LEFT JOIN  `user`.`member` AS m1 ON ( a.`accept_uid` = m1.`uid` ) 
				LEFT JOIN  `user`.`permissions` AS p ON ( p.`permission` = m1.`permission` )				
				LEFT JOIN  `user`.`status_info` AS sa ON (sa.`status` = p.`status`)
				LEFT JOIN  `user`.`class` AS c1 ON ( c1.`class_code` = st1.`class_code` ) 
				LEFT JOIN  `user`.`semester` AS sem1 ON ( sem1.`semester_code` = c1.`semester_code` ) 
				LEFT JOIN  `user`.`school` AS sch1 ON ( sch1.`school_code` = sem1.`school_code` )
			WHERE 
				sem1.`semester_code` like '%2015_2%' 
				and p.`status` in ('i_s','i_t') and sch1.`school_name` in ('中平國小','九德國小','僑忠國小','興安國小')
			GROUP BY a.`master_ctask_id`
		";
		$get_data1 = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		$a1=0;$a2=0;$a3=0;$a4=0;
		$b1=0;$b2=0;$b3=0;$b4=0;
		$c1=0;$c2=0;$c3=0;$c4=0;
		$d1=0;$d2=0;$d3=0;$d4=0;
		foreach($get_data1 as $key =>$get_data1){
			$task_id[$key]	   	   = $get_data1['group_task_id'];
			$task_name[$key]   	   = $get_data1['gask_topic'];
			$school_name[$key]	   = $get_data1['school_name'];
			$mission_state[$key]   = $get_data1['mission_state'];
			if($task_id[$key]==1){
				if($school_name[$key]=='中平國小'){
					if($mission_state[$key]==1||$mission_state[$key]==2){
						$a1++;
					}
				}
				if($school_name[$key]=='九德國小'){
					if($mission_state[$key]==1||$mission_state[$key]==2){
						$b1++;
					}
				}
				if($school_name[$key]=='僑忠國小'){
					if($mission_state[$key]==1||$mission_state[$key]==2){
						$c1++;
					}
				}
				if($school_name[$key]=='興安國小'){
					if($mission_state[$key]==1||$mission_state[$key]==2){
						$d1++;
					}
				}
			}
			if($task_id[$key]==2){
				if($school_name[$key]=='中平國小'){
					if($mission_state[$key]==1||$mission_state[$key]==2){
						$a2++;
					}
				}
				if($school_name[$key]=='九德國小'){
					if($mission_state[$key]==1||$mission_state[$key]==2){
						$b2++;
					}
				}
				if($school_name[$key]=='僑忠國小'){
					if($mission_state[$key]==1||$mission_state[$key]==2){
						$c2++;
					}
				}
				if($school_name[$key]=='興安國小'){
					if($mission_state[$key]==1||$mission_state[$key]==2){
						$d2++;
					}
				}
			}
			if($task_id[$key]==3){
				if($school_name[$key]=='中平國小'){
					if($mission_state[$key]==1||$mission_state[$key]==2){
						$a3++;
					}
				}
				if($school_name[$key]=='九德國小'){
					if($mission_state[$key]==1||$mission_state[$key]==2){
						$b3++;
					}
				}
				if($school_name[$key]=='僑忠國小'){
					if($mission_state[$key]==1||$mission_state[$key]==2){
						$c3++;
					}
				}
				if($school_name[$key]=='興安國小'){
					if($mission_state[$key]==1||$mission_state[$key]==2){
						$d3++;
					}
				}
			}
			if($task_id[$key]==4){
				if($school_name[$key]=='中平國小'){
					if($mission_state[$key]==1||$mission_state[$key]==2){
						$a4++;
					}
				}
				if($school_name[$key]=='九德國小'){
					if($mission_state[$key]==1||$mission_state[$key]==2){
						$b4++;
					}
				}
				if($school_name[$key]=='僑忠國小'){
					if($mission_state[$key]==1||$mission_state[$key]==2){
						$c4++;
					}
				}
				if($school_name[$key]=='興安國小'){
					if($mission_state[$key]==1||$mission_state[$key]==2){
						$d4++;
					}
				}
			}
			
			
		}
	
		//--------------------------------------------------------------
		echo "</br>";
		echo "--------------------------";
		echo "</br>";
		echo "任務一：";
		echo "</br>";
		echo "&nbsp;&nbsp;中平國小：".$a1."個";
		echo "</br>";
		echo "&nbsp;&nbsp;九德國小：".$b1."個";
		echo "</br>";
		echo "&nbsp;&nbsp;僑忠國小：".$c1."個";
		echo "</br>";
		echo "&nbsp;&nbsp;興安國小：".$d1."個";
		echo "</br>";
		echo "任務二：";
		echo "</br>";
		echo "&nbsp;&nbsp;中平國小：".$a2."個";
		echo "</br>";
		echo "&nbsp;&nbsp;九德國小：".$b2."個";
		echo "</br>";
		echo "&nbsp;&nbsp;僑忠國小：".$c2."個";
		echo "</br>";
		echo "&nbsp;&nbsp;興安國小：".$d2."個";
		echo "</br>";
		echo "任務三：";
		echo "</br>";
		echo "&nbsp;&nbsp;中平國小：".$a3."個";
		echo "</br>";
		echo "&nbsp;&nbsp;九德國小：".$b3."個";
		echo "</br>";
		echo "&nbsp;&nbsp;僑忠國小：".$c3."個";
		echo "</br>";
		echo "&nbsp;&nbsp;興安國小：".$d3."個";
		echo "</br>";
		echo "任務四：";
		echo "</br>";
		echo "&nbsp;&nbsp;中平國小：".$a4."個";
		echo "</br>";
		echo "&nbsp;&nbsp;九德國小：".$b4."個";
		echo "</br>";
		echo "&nbsp;&nbsp;僑忠國小：".$c4."個";
		echo "</br>";
		echo "&nbsp;&nbsp;興安國小：".$d4."個";
		echo "</br>";
		echo "--------------------------";
		echo "</br>";
	
		$sql="
			SELECT a.`u_id`,sch1.`school_name`,c1.`grade`,c1.`classroom` 
			FROM `mssr_forum`.`dev_member_score` as a
				LEFT JOIN  `user`.`student` AS st1 ON ( a.`u_id` = st1.`uid` ) 
				LEFT JOIN  `user`.`class` AS c1 ON ( c1.`class_code` = st1.`class_code` ) 
				LEFT JOIN  `user`.`semester` AS sem1 ON ( sem1.`semester_code` = c1.`semester_code` ) 
				LEFT JOIN  `user`.`school` AS sch1 ON ( sch1.`school_code` = sem1.`school_code` )
			WHERE sem1.`semester_code` like '%2015_2%' and sch1.`school_name` in ('中平國小','興安國小','九德國小','僑忠國小')
			GROUP BY a.`u_id`
		";
		
		$get_achievement_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		echo "<table border=1>";
		echo "<tr>";
			echo "<td>人數</td>";
			echo "<td>使用者編號</td>";
			echo "<td>學校名稱</td>";
			echo "<td>班級</td>";
		echo "</tr>";
		$i=0;
		foreach($get_achievement_data as $key => $get_achievement_data){
			
			$get_uid[$key] 		= $get_achievement_data['u_id'];
			$school_name[$key]	= $get_achievement_data['school_name'];
			$grade[$key]		= $get_achievement_data['grade'];
			$classroom[$key]	= $get_achievement_data['classroom'];			
			if($school_name[$key]=='中平國小'){
				if($grade[$key]==5){
					$i++;
					echo "<tr>";
						echo "<td> $i</td>";
						echo "<td> $get_uid[$key] </td>";
						echo "<td> $school_name[$key] </td>";
						echo "<td> $grade[$key]年$classroom[$key]班 </td>";
					echo "</tr>";	
				}
			}
			if($school_name[$key]=='九德國小'){
				if($grade[$key]==4){
					$i++;
					echo "<tr>";
						echo "<td> $i </td>";
						echo "<td> $get_uid[$key] </td>";
						echo "<td> $school_name[$key] </td>";
						echo "<td> $grade[$key]年$classroom[$key]班 </td>";
					echo "</tr>";	
				}
			}
			if($school_name[$key]=='興安國小'){
				if($grade[$key]==4){
					$i++;
					echo "<tr>";
						echo "<td> $i </td>";
						echo "<td> $get_uid[$key] </td>";
						echo "<td> $school_name[$key] </td>";
						echo "<td> $grade[$key]年$classroom[$key]班 </td>";
					echo "</tr>";	
				}
			}
			if($school_name[$key]=='僑忠國小'){
				if($grade[$key]==4){
					$i++;
					echo "<tr>";
						echo "<td> $i </td>";
						echo "<td> $get_uid[$key] </td>";
						echo "<td> $school_name[$key] </td>";
						echo "<td> $grade[$key]年$classroom[$key]班 </td>";
					echo "</tr>";	
				}
			}
		}
		echo "</table>";
		
/* 		$sql="
			SELECT m1.`uid`,
				   st1.`class_code`,
				   sch1.`school_name`
			FROM `user`.`member` AS m1
				INNER JOIN  `user`.`student` AS st1 ON ( m1.`uid` = st1.`uid` ) 
				INNER JOIN  `user`.`permissions` AS p1 ON ( m1.`permission` = p1.`permission` ) 
				INNER JOIN  `user`.`class` AS c1 ON ( c1.`class_code` = st1.`class_code` ) 
				INNER JOIN  `user`.`semester` AS sem1 ON ( sem1.`semester_code` = c1.`semester_code` ) 
				INNER JOIN  `user`.`school` AS sch1 ON ( sch1.`school_code` = sem1.`school_code` ) 
			WHERE p1.`status` = 'i_s'
			AND sch1.`region_name` <>  '測試'
			AND sch1.`school_name` <>  '中央大學'
			and sch1.`school_name` = '中平國小'
			and sem1.`semester_code` like '%2015_2%'
			and c1.`grade`=5
			and c1.`classroom`=4
		";
		$get_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_data as $key => $get_data){
			$student_uid[$key] = $get_data['uid'];
		}
			
		$friend_list = array();
		$friend_number = array();
		for($x=0; $x<count($student_uid); $x++){
			
			$friend_results =get_forum_friend($student_uid[$x],$friend_id=0,$arry_conn_mssr);
				$j = 0;
				for($i=0;$i<count($friend_results);$i++){
					if($friend_results[$i]['friend_state']==1){
						if($friend_results[$i]['friend_id']==$student_uid[$x]){
							$friend_list[$x][$j] = $friend_results[$i]['user_id'];
							$j++;
							$friend_number[$x] = $j;
						}else if($friend_results[$i]['friend_id']!=$student_uid[$x]){
							$friend_list[$x][$j] = $friend_results[$i]['friend_id'];							
							$j++;
							$friend_number[$x] = $j;
						}
					}
				}
			
		}	
		echo "<pre>";
		print_r($student_uid);
		echo "</pre>";
		echo "<pre>";
		print_r($friend_number);
		echo "</pre>";
		echo "<pre>";
		print_r($friend_list);
		echo "</pre>"; */








?>