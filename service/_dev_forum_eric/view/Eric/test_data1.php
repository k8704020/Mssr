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
				a.`group_task_id` =1 
				and sem1.`semester_code` like '%2015_2%' and (a.`start_time`>'2016-05-31' or a.`start_time` is null)
				and p.`status` in ('i_s','i_t')
				and sch1.`school_code` in ('dsg','vqk','gid','pyd','cte','gth','bts','gsl','gfd','pce','ctc','nif','pnr','gnk','gpe','wof','gzj','nhe','gdc','yre','api','csp','gps','smps','nam','cyc','jdy','uwn','ivw','smb','bnr','did','lrb','nep','dru','chi','edl','nsa','zbq','won','dxu','pqr','wbp')
			GROUP BY a.`master_ctask_id`
		";
		
		/*		'bts_2015_2_4_1_2','ctc_2015_2_3_1_1','gpe_2015_2_5_2_3','nhe_2015_2_3_3_1','csp_2015_2_4_7_1','gps_2015_2_3_2_1','gps_2015_2_3_3_1','gps_2015_2_3_5_1','cyc_2015_2_4_1_3','cyc_2015_2_4_2_3','jdy_2015_2_4_1_2','jdy_2015_2_4_2_2','smb_2015_2_3_4_1','bnr_2015_2_4_1_1','bnr_2015_2_4_2_1','bnr_2015_2_4_3_1','bnr_2015_2_4_4_1','bnr_2015_2_4_5_1','cjh_2015_2_3_1_2','cjh_2015_2_4_1_2','cjh_2015_2_5_1_2','nep_2015_2_4_1_1','nep_2015_2_4_2_1','nep_2015_2_4_3_1','dru_2015_2_3_1_1','dru_2015_2_4_1_1','dru_2015_2_4_2_1','nsa_2015_2_4_3_1','nsa_2015_2_5_1_1','nsa_2015_2_5_2_1','nsa_2015_2_5_3_1','nsa_2015_2_5_4_1','zbq_2015_2_3_1_2','zbq_2015_2_3_2_2','zbq_2015_2_3_3_2','zbq_2015_2_4_1_2','zbq_2015_2_4_2_2','zbq_2015_2_4_3_2','pqr_2015_2_3_1_1','pqr_2015_2_3_2_1','pqr_2015_2_3_3_1','pqr_2015_2_4_1_1','pqr_2015_2_4_2_1','pqr_2015_2_4_3_1','pqr_2015_2_4_4_1','pqr_2015_2_5_1_1','pqr_2015_2_5_4_1','wbp_2015_2_3_10_1','wbp_2015_2_3_11_1','wbp_2015_2_3_1_1','wbp_2015_2_3_2_1','wbp_2015_2_3_3_1','wbp_2015_2_3_4_1','wbp_2015_2_3_5_1','wbp_2015_2_3_6_1','wbp_2015_2_3_7_1','wbp_2015_2_3_8_1','wbp_2015_2_3_9_1'
		*/
		/*		'pyd_2015_2_4_1_1','cte_2015_2_3_3_1','wof_2015_2_3_5_1','wof_2015_2_4_4_1','yre_2015_2_4_1_2','yre_2015_2_5_1_2','api_2015_2_4_6_1','smps_2015_2_4_1_2','smps_2015_2_5_1_2','nam_2015_2_3_1_1','nam_2015_2_3_3_1','uwn_2015_2_4_1_1','uwn_2015_2_5_1_2','ivw_2015_2_5_1_4','did_2015_2_4_1_1','did_2015_2_4_2_1','did_2015_2_4_3_1','lrb_2015_2_4_3_2','lrb_2015_2_5_3_2','chi_2015_2_3_1_1','chi_2015_2_3_2_1','chi_2015_2_3_3_1','edl_2015_2_3_1_2','edl_2015_2_3_2_2','edl_2015_2_3_4_2','won_2015_2_3_1_1','won_2015_2_3_6_1','won_2015_2_3_7_1','won_2015_2_4_1_1','won_2015_2_4_2_1','won_2015_2_4_4_1','won_2015_2_4_5_1','won_2015_2_4_6_1','won_2015_2_4_7_1','dxu_2015_2_3_3_8','dxu_2015_2_4_11_8','dxu_2015_2_4_7_8','dxu_2015_2_4_9_8','dxu_2015_2_5_10_8','dxu_2015_2_5_11_8','dxu_2015_2_5_1_8','dxu_2015_2_5_2_8','dxu_2015_2_5_6_8','dxu_2015_2_5_7_8','dxu_2015_2_5_8_8'
		*/
		$get_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		
		//-------------------------------------------------
		//發文篇數
		$sql="
			SELECT count(cno1) AS cno
			FROM (
				SELECT count(am.`article_id`) as cno1
				FROM `mssr_forum`.`dev_article_group_mission_rev` AS am
					LEFT JOIN `mssr_forum`.`mssr_forum_article` AS a ON ( am.`article_id` = a.`article_id` )
					LEFT JOIN `user`.`student` AS st1 ON ( a.`user_id` = st1.`uid` ) 
					LEFT JOIN `user`.`class` AS c1 ON ( c1.`class_code` = st1.`class_code` ) 
					LEFT JOIN `user`.`semester` AS sem1 ON ( sem1.`semester_code` = c1.`semester_code` )
					LEFT JOIN  `user`.`school` AS sch1 ON ( sch1.`school_code` = sem1.`school_code` )
				WHERE 1=1
					AND am.`keyin_mdate`>'2016-05-31' and `group_task_id`=1
					AND am.`keyin_mdate`<'2016-06-07'
					and sch1.`school_code` in ('dsg','vqk','gid','pyd','cte','gth','bts','gsl','gfd','pce','ctc','nif','pnr','gnk','gpe','wof','gzj','nhe','gdc','yre','api','csp','gps','smps','nam','cyc','jdy','uwn','ivw','smb','bnr','did','lrb','nep','dru','chi','edl','nsa','zbq','won','dxu','pqr','wbp')
				GROUP BY am.`article_id`
			) AS query
		";
		$get_article_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		//-------------------------------------------------
		//回文篇數
		$sql="
			SELECT count(ar.`reply_id`) as cno
			FROM `mssr_forum`.`dev_reply_group_mission_rev` AS ar
				LEFT JOIN `mssr_forum`.`mssr_forum_reply` AS r ON ( ar.`reply_id` = r.`reply_id` )
				LEFT JOIN `user`.`student` AS st1 ON ( r.`user_id` = st1.`uid` ) 
				LEFT JOIN `user`.`class` AS c1 ON ( c1.`class_code` = st1.`class_code` ) 
				LEFT JOIN `user`.`semester` AS sem1 ON ( sem1.`semester_code` = c1.`semester_code` )
				LEFT JOIN  `user`.`school` AS sch1 ON ( sch1.`school_code` = sem1.`school_code` )
			WHERE 1=1
				AND ar.`keyin_mdate`>'2016-05-31' and `group_task_id`=1
				AND ar.`keyin_mdate`<'2016-06-07'
				and sch1.`school_code` in ('dsg','vqk','gid','pyd','cte','gth','bts','gsl','gfd','pce','ctc','nif','pnr','gnk','gpe','wof','gzj','nhe','gdc','yre','api','csp','gps','smps','nam','cyc','jdy','uwn','ivw','smb','bnr','did','lrb','nep','dru','chi','edl','nsa','zbq','won','dxu','pqr','wbp')
		";
		$get_reply_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);		
		//-------------------------------------------------
		//被回覆的發文篇數
		$sql="
			SELECT count(cno1) AS cno
			FROM (
			    SELECT count(ar.`reply_id`) as cno1
				FROM `mssr_forum`.`dev_reply_group_mission_rev` AS ar
					LEFT JOIN `mssr_forum`.`mssr_forum_reply` AS r ON ( ar.`reply_id` = r.`reply_id` )
					LEFT JOIN `user`.`student` AS st1 ON ( r.`user_id` = st1.`uid` ) 
					LEFT JOIN `user`.`class` AS c1 ON ( c1.`class_code` = st1.`class_code` ) 
					LEFT JOIN `user`.`semester` AS sem1 ON ( sem1.`semester_code` = c1.`semester_code` )
					LEFT JOIN  `user`.`school` AS sch1 ON ( sch1.`school_code` = sem1.`school_code` )
				WHERE 1=1
					AND ar.`keyin_mdate`>'2016-05-31' and `group_task_id`=1
					AND ar.`keyin_mdate`<'2016-06-07'
					and sch1.`school_code` in ('dsg','vqk','gid','pyd','cte','gth','bts','gsl','gfd','pce','ctc','nif','pnr','gnk','gpe','wof','gzj','nhe','gdc','yre','api','csp','gps','smps','nam','cyc','jdy','uwn','ivw','smb','bnr','did','lrb','nep','dru','chi','edl','nsa','zbq','won','dxu','pqr','wbp')
                GROUP BY ar.`article_id`
			) AS query
		";
		$get_article_replied_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		//-------------------------------------------------
		echo "<table border=1>";
		echo "<tr style='background-color:yellow'>";
			echo "<td>個數</td>";
			echo "<td>任務編號</td>";
			echo "<td>任務主題</td>";
			echo "<td>傳遞者編號</td>";
			echo "<td>使用者編號</td>";
			echo "<td>學校名稱</td>";
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
				echo "<td> $grade[$key]年$classroom[$key]班 </td>";
				echo "<td> $mission_state[$key] </td>";
				echo "<td> $start_time[$key] </td>";
				echo "<td> $status_name[$key] </td>";

			echo "</tr>";
		}
		//-------------------------------------------------

		//[第一層]
		$query1 = array();
		$number1 = 0;//傳遞人數
		$n1 = 0;
		$accept1 = 0;//接受者
		$complete1 = 0;//完成者
		$reject1 = 0;//拒絕者
		$no_reply1 = 0;//不理會者		
		$sql="
			SELECT distinct
				a.`group_task_id`,a.`deliver_uid` ,a.`accept_uid` ,c.`gask_topic`,a.`mission_state`,
				sch1.`school_name`,c1.`grade`,c1.`classroom`,a.`start_time`,sa.`name` as status_name ,p.`status`,c1.`class_code`
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
				a.`group_task_id` =1 and a.`deliver_uid`=5030
				and sem1.`semester_code` like '%2015_2%' and (a.`start_time`>'2016-05-31' or a.`start_time` is null)
				and p.`status` in ('i_s','i_t')
				and sch1.`school_code` in ('dsg','vqk','gid','pyd','cte','gth','bts','gsl','gfd','pce','ctc','nif','pnr','gnk','gpe','wof','gzj','nhe','gdc','yre','api','csp','gps','smps','nam','cyc','jdy','uwn','ivw','smb','bnr','did','lrb','nep','dru','chi','edl','nsa','zbq','won','dxu','pqr','wbp')
				
			GROUP BY a.`master_ctask_id`
		";
		$get_data1 = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_data1 as $key => $get_data1){
			
			$mission_state1[$key]= $get_data1['mission_state'];
			$number1++;
			if($mission_state1[$key]==1||$mission_state1[$key]==2){
				$accept1++;
			}
			if($mission_state1[$key]==1){
				$query1[$n1] 		 = $get_data1['accept_uid'];
				$n1++;
				$complete1++;
			}
			if($mission_state1[$key]==3){
				$reject1++;
			}
			if($mission_state1[$key]==5){
				$no_reply1++;
			}
		}
		$output1=implode(",",$query1);
		//-------------------------------------------------
		//[第二層]
		$query2 = array();
		$number2 = 0;//傳遞人數
		$n2 = 0;
		$accept2 = 0;//接受者
		$complete2 = 0;//完成者
		$reject2 = 0;//拒絕者
		$no_reply2 = 0;//不理會者
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
				a.`group_task_id` =1 and a.`deliver_uid` in ($output1)
				and sem1.`semester_code` like '%2015_2%' and (a.`start_time`>'2016-05-31' or a.`start_time` is null)
				and p.`status` in ('i_s','i_t') 
			GROUP BY a.`master_ctask_id`
		";
		$get_data2 = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_data2 as $key => $get_data2){
			
			$mission_state2[$key]= $get_data2['mission_state'];
			$number2++;
			if($mission_state2[$key]==1||$mission_state2[$key]==2){
				$accept2++;
			}
			if($mission_state2[$key]==1){
				$query2[$n2] 		 = $get_data2['accept_uid'];
				$n2++;
				$complete2++;
			}
			if($mission_state2[$key]==3){
				$reject2++;
			}
			if($mission_state2[$key]==5){
				$no_reply2++;
			}
		}
		$output2=implode(",",$query2);
 		//-------------------------------------------------
		//[第三層]
		$query3 = array();
		$number3 = 0;//傳遞人數
		$n3 = 0;
		$accept3 = 0;//接受者
		$complete3 = 0;//完成者
		$reject3 = 0;//拒絕者
		$no_reply3 = 0;//不理會者
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
				a.`group_task_id` =1 and a.`deliver_uid` in ($output2)
				and sem1.`semester_code` like '%2015_2%' and (a.`start_time`>'2016-05-31' or a.`start_time` is null)
				and p.`status` in ('i_s','i_t') 
			GROUP BY a.`master_ctask_id`
		";
		$get_data3 = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_data3 as $key => $get_data3){
			
			$mission_state3[$key]= $get_data3['mission_state'];
			$number3++;
			if($mission_state3[$key]==1||$mission_state3[$key]==2){
				$accept3++;
			}
			if($mission_state3[$key]==1){
				$query3[$n3] 		 = $get_data3['accept_uid'];
				$n3++;
				$complete3++;
			}
			if($mission_state3[$key]==3){
				$reject3++;
			}
			if($mission_state3[$key]==5){
				$no_reply3++;
			}
		}
		$output3=implode(",",$query3);	
		//-------------------------------------------------
		//[第四層]
		$query4 = array();
		$number4 = 0;//傳遞人數
		$n4 = 0;
		$accept4 = 0;//接受者
		$complete4 = 0;//完成者
		$reject4 = 0;//拒絕者
		$no_reply4 = 0;//不理會者
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
				a.`group_task_id` =1 and a.`deliver_uid` in ($output3)
				and sem1.`semester_code` like '%2015_2%' and (a.`start_time`>'2016-05-31' or a.`start_time` is null)
				and p.`status` in ('i_s','i_t') 
			GROUP BY a.`master_ctask_id`
		";
		$get_data4 = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_data4 as $key => $get_data4){
			
			$mission_state4[$key]= $get_data4['mission_state'];
			$number4++;
			if($mission_state4[$key]==1||$mission_state4[$key]==2){
				$accept4++;
			}
			if($mission_state4[$key]==1){
				$query4[$n4] 		 = $get_data4['accept_uid'];
				$n4++;
				$complete4++;
			}
			if($mission_state4[$key]==3){
				$reject4++;
			}
			if($mission_state4[$key]==5){
				$no_reply4++;
			}
		}
		$output4=implode(",",$query4);	
		//-------------------------------------------------
		//[第五層]
		$query5 = array();
		$number5 = 0;//傳遞人數
		$n5 = 0;
		$accept5 = 0;//接受者
		$complete5 = 0;//完成者
		$reject5 = 0;//拒絕者
		$no_reply5 = 0;//不理會者
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
				a.`group_task_id` =1 and a.`deliver_uid` in ($output4)
				and sem1.`semester_code` like '%2015_2%' and (a.`start_time`>'2016-05-31' or a.`start_time` is null)
				and p.`status` in ('i_s','i_t') 
			GROUP BY a.`master_ctask_id`
		";
		$get_data5 = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_data5 as $key => $get_data5){
			
			$mission_state5[$key]= $get_data5['mission_state'];
			$number5++;
			if($mission_state5[$key]==1||$mission_state5[$key]==2){
				$accept5++;
			}
			if($mission_state5[$key]==1){
				$query5[$n5] 		 = $get_data5['accept_uid'];
				$n5++;
				$complete5++;
			}
			if($mission_state5[$key]==3){
				$reject5++;
			}
			if($mission_state5[$key]==5){
				$no_reply5++;
			}
		}
		$output5=implode(",",$query5);	 
		//-------------------------------------------------
		
		$total = 246;//傳遞人數
		echo "</table>";
		echo "----------------------------";
		echo "</br>";
		echo "系統散播人數：".$total."人";
		echo "</br>";
		echo "擁有推播任務人數：".$i."人";
		echo "</br>";
		echo "未看到任務：".$no_see."人";
		echo "</br>";
		echo "未回應任務(看到不理)：".$no_reply."人";
		echo "</br>";
		echo "有看到任務：".($accept+$reject+$no_reply)."人";
		echo "</br>";
		echo "拒絕者：".$reject."人";		
		echo "</br>";
		echo "接受者：".$accept."人";
		echo "</br>";
		echo "&nbsp;&nbsp;>進行中：".$ing."人";
		echo "</br>";
		echo "&nbsp;&nbsp;>完成者：".$complete."人";
		echo "</br>";	
		echo "----------------------------";
		echo "</br>";	
		echo "全部人下去算";
		echo "</br>";
		echo "&nbsp;&nbsp;>任務接受率：".(round($accept/$i,2)*100)."%";
		echo "</br>";
		echo "&nbsp;&nbsp;>任務完成率：".(round($complete/$i,2)*100)."%";
		echo "</br>";
		echo "僅以「有看到任務者」下去算";
		echo "</br>";
		echo "&nbsp;&nbsp;>任務接受率：".(round($accept/($accept+$reject+$no_reply),2)*100)."%";
		echo "</br>";
		echo "&nbsp;&nbsp;>任務完成率：".(round($complete/($accept+$reject+$no_reply),2)*100)."%";
		echo "</br>";
		echo "----------------------------";
		echo "</br>";
		echo "發文數量：".$get_article_data[0]['cno']."篇";
		echo "</br>";
		echo "回文數量：".$get_reply_data[0]['cno']."篇";
		echo "</br>";
		echo "有被回覆的發文：".$get_article_replied_data[0]['cno']."篇";
		echo "</br>";
		echo "回覆狀況(有被回文/全部發文)：".(round((int)$get_article_replied_data[0]['cno']/(int)$get_article_data[0]['cno'],2)*100)."%";
		echo "</br>";
		echo "有被回覆的發文中：每篇發文會收到&nbsp;".(round((int)$get_reply_data[0]['cno']/(int)$get_article_replied_data[0]['cno'],2))."&nbsp;篇回文";
		echo "</br>";
		echo "----------------------------";
		echo "</br>";
		echo "[第一層]系統傳播人數：".$number1."人";
		echo "</br>";
		echo "任務傳遞者：5030";
		echo "</br>";
		echo "&nbsp;&nbsp;有看到任務：".($accept1+$reject1+$no_reply1)."人";
		echo "</br>";
		echo "&nbsp;&nbsp;接受者：".$accept1."人&nbsp;(完成者：".$complete1."人)";
		echo "</br>";
		echo "&nbsp;&nbsp;拒絕者：".$reject1."人";
		echo "</br>";
		echo "&nbsp;&nbsp;>任務接受率：".(round($accept1/($accept1+$reject1+$no_reply1),2)*100)."%";
		echo "</br>";
		echo "&nbsp;&nbsp;>任務完成率：".(round($complete1/($accept1+$reject1+$no_reply1),2)*100)."%";
		echo "</br>";
		echo "[第二層]使用者傳播人數：".$number2."人 (來自".$n1."人)";
		echo "</br>";
		echo "任務傳遞者：".$output1;
		echo "</br>";
		echo "&nbsp;&nbsp;有看到任務：".($accept2+$reject2+$no_reply2)."人";
		echo "</br>";
		echo "&nbsp;&nbsp;接受者：".$accept2."人&nbsp;(完成者：".$complete2."人)";		
		echo "</br>";
		echo "&nbsp;&nbsp;拒絕者：".$reject2."人";
		echo "</br>";
		echo "&nbsp;&nbsp;>任務接受率：".(round($accept2/($accept2+$reject2+$no_reply2),2)*100)."%";
		echo "</br>";
		echo "&nbsp;&nbsp;>任務完成率：".(round($complete2/($accept2+$reject2+$no_reply2),2)*100)."%";
		echo "</br>";
		echo "[第三層]使用者傳播人數：".$number3."人 (來自".$n2."人)";
		echo "</br>";
		echo "任務傳遞者：".$output2;
		echo "</br>";
		echo "&nbsp;&nbsp;有看到任務：".($accept3+$reject3+$no_reply3)."人";
		echo "</br>";
		echo "&nbsp;&nbsp;接受者：".$accept3."人&nbsp;(完成者：".$complete3."人)";
		echo "</br>";
		echo "&nbsp;&nbsp;拒絕者：".$reject3."人";
		echo "</br>";
		echo "&nbsp;&nbsp;>任務接受率：".(round($accept3/($accept3+$reject3+$no_reply3),2)*100)."%";
		echo "</br>";
		echo "&nbsp;&nbsp;>任務完成率：".(round($complete3/($accept3+$reject3+$no_reply3),2)*100)."%";
		echo "</br>";
		echo "[第四層]使用者傳播人數：".$number4."人 (來自".$n3."人)";
		echo "</br>";
		echo "任務傳遞者：".$output3;
		echo "</br>";
		echo "&nbsp;&nbsp;有看到任務：".($accept4+$reject4+$no_reply4)."人";
		echo "</br>";
		echo "&nbsp;&nbsp;接受者：".$accept4."人&nbsp;(完成者：".$complete4."人)";
		echo "</br>";
		echo "&nbsp;&nbsp;拒絕者：".$reject4."人";
		echo "</br>";
		echo "&nbsp;&nbsp;>任務接受率：".(round($accept4/($accept4+$reject4+$no_reply4),2)*100)."%";
		echo "</br>";
		echo "&nbsp;&nbsp;>任務完成率：".(round($complete4/($accept4+$reject4+$no_reply4),2)*100)."%";
		echo "</br>";
		echo "[第五層]使用者傳播人數：".$number5."人 (來自".$n4.")";
		echo "</br>";
		echo "任務傳遞者：".$output4;
		echo "</br>";
		echo "&nbsp;&nbsp;有看到任務：".($accept5+$reject5+$no_reply5)."人";
		echo "</br>";
		echo "&nbsp;&nbsp;接受者：".$accept5."人&nbsp;(完成者：".$complete5."人)";
		echo "</br>";
		echo "&nbsp;&nbsp;拒絕者：".$reject5."人";
		echo "</br>";
		echo "&nbsp;&nbsp;>任務接受率：".(round($accept5/($accept5+$reject5+$no_reply5),2)*100)."%";
		echo "</br>";
		echo "&nbsp;&nbsp;>任務完成率：".(round($complete5/($accept5+$reject5+$no_reply5),2)*100)."%";
		echo "</br>";
		echo "----------------------------";
		/* echo "<pre>";
		echo print_r($query1);
		echo "</pre>";
		echo "接受者：".$output1; */






?>