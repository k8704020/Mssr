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

			$uid = (int)$arrys_sess_login_info[0]['uid'];
			$task_id = $_POST["task_id"];
			$deliver_uid = $_POST["deliver_uid"];

			$step_number = 0;//(0:表示還沒有進行任務)
			$sql="
				SELECT MAX(b.`step_number`) as step_number
				FROM  `mssr_forum`.`dev_mission_step_log` as a
				inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
				WHERE `accept_uid`={$uid} and `deliver_uid`={$deliver_uid} and b.`group_task_id`={$task_id} and a.`step_state`=1
			";
			$mission_step =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($mission_step[0]['step_number'])){$step_number = $mission_step[0]['step_number'];}

			$step_number_now = $step_number+1;//暫存正要進行的「任務編號」

			//-------------------------------------------------------------------
			if($step_number==0){//尚未完成任何任務步驟，從第一個任務開始進行
			//-------------------------------------------------------------------
				$sql="
					SELECT * FROM `mssr_forum`.`dev_mission_step_log` as a
					inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
					WHERE a.`accept_uid`={$uid} and a.`deliver_uid`={$deliver_uid} and b.`step_number`={$step_number_now} and b.`group_task_id`={$task_id}
				";
				$mission_step1 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$master_atask_id = $mission_step1[0]['master_atask_id'];
				$end_time  	     = $mission_step1[0]['end_step_time'];
				$step_score      = $mission_step1[0]['available_score'];
				$step_content    = $mission_step1[0]['step_content'];
				$score_content   = $mission_step1[0]['score_content'];

				echo json_encode(array("step_number"=>$step_number,"end_time"=>$end_time,"step_score"=>$step_score,"step_content"=>$step_content,"score_content"=>$score_content,"uid"=>$uid));
			//-------------------------------------------------------------------
			}else if($step_number==1){//已完成第一個步驟，正在進行第二個任務步驟
			//-------------------------------------------------------------------
				$sql="
					SELECT a.`master_atask_id`,b.`master_task_id`,a.`end_step_time`,b.`available_score`,b.`step_content`,b.`score_content`
					FROM `mssr_forum`.`dev_mission_step_log` as a
					inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
					WHERE a.`accept_uid`={$uid} and a.`deliver_uid`={$deliver_uid} and b.`step_number`={$step_number_now} and b.`group_task_id`={$task_id}
				";
				$mission_step2 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				    $end_time  	           = $mission_step2[0]['end_step_time'];
				foreach($mission_step2 as $key => $mission_step2){
					$step_score[$key]      = $mission_step2['available_score'];//step_score[0]存按讚資料、step_score[1]存回覆資料
					$step_content[$key]    = $mission_step2['step_content'];
					$score_content[$key]   = $mission_step2['score_content'];
					$master_atask_id[$key] = $mission_step2['master_atask_id'];
				}
				//-------------------------------------------------------------------
				$sql="
					SELECT a.`article_id`,c.`article_title`
					FROM `mssr_forum`.`mssr_forum_article` as a
					inner join `mssr_forum`.`dev_article_group_mission_rev` as b on a.`article_id`=b.`article_id`
					inner join `mssr_forum`.`mssr_forum_article_detail` as c on a.`article_id`=c.`article_id`
					WHERE `user_id`={$uid} and `article_from`=3 and `article_state` =1 and `group_task_id`={$task_id}
				";
				$get_article =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$article_id    = $get_article[0]['article_id'];//抓取文章編號
				$article_title = $get_article[0]['article_title'];//抓取活動文章的標題
				//-------------------------------------------------------------------
				$article_like_cno = 0;//文章的按讚數
				$sql="
					SELECT sum(a.`article_like_cno`) as cno
					FROM `mssr_forum`.`mssr_forum_article` as a
					inner join `mssr_forum`.`dev_article_group_mission_rev` as b on a.`article_id`=b.`article_id`
					WHERE a.`article_id`={$article_id}
				";
				$get_article_like_cno =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				if(isset($get_article_like_cno[0]['cno'])){
					$article_like_cno = $get_article_like_cno[0]['cno'];//抓取活動文章的按讚數
				}else{
					$article_like_cno = 0;//文章的按讚數
				}
				//-------------------------------------------------------------------
				$sql="
					SELECT sum(a.`reply_like_cno`) as cno
					FROM `mssr_forum`.`mssr_forum_reply` as a
					inner join `mssr_forum`.`dev_reply_group_mission_rev` as b on a.`reply_id`=b.`reply_id`
					WHERE a.`article_id`={$article_id} and `reply_state`=1
				";
				$get_article_reply_like_cno =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				if(isset($get_article_reply_like_cno[0]['cno'])){
					$article_reply_like_cno = $get_article_reply_like_cno[0]['cno'];//抓取回覆文章的按讚數
				}else{
					$article_reply_like_cno = 0;//回覆文章的按讚數
				}
				//-------------------------------------------------------------------
				$article_reply_cno = 0;//回覆文章的次數
				$sql="
					SELECT count(a.`reply_id`) as cno
					FROM `mssr_forum`.`mssr_forum_reply` as a
					inner join `mssr_forum`.`dev_reply_group_mission_rev` as b on a.`reply_id`=b.`reply_id`
					WHERE a.`article_id`={$article_id} and a.`user_id`!={$uid} and a.`reply_state`=1
				";
 				//echo "<Pre>";
				//print_r($sql);
				//echo "</Pre>";
				$get_article_reply_cno =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				if(isset($get_article_reply_cno[0]['cno'])){$article_reply_cno = $get_article_reply_cno[0]['cno'];}//抓取活動文章的回覆數
				//-------------------------------------------------------------------
				//處理目前的按讚、回覆分數
				$like_score  = 0;//暫存
				$article_reply_score = 0;

				$like_score = ($article_like_cno+$article_reply_like_cno)*$step_score[0];
				$article_reply_score= $article_reply_cno*$step_score[1];
				$expect_score = $like_score + $article_reply_score;

				echo json_encode(array("step_number"=>$step_number,"article_id"=>$article_id,"article_title"=>$article_title,"end_time"=>$end_time,"step_content"=>$step_content,"score_content"=>$score_content,"like_cno"=>$article_like_cno+$article_reply_like_cno,"article_reply_cno"=>$article_reply_cno,"expect_score"=>$expect_score));

			//-------------------------------------------------------------------
			}else if($step_number==2){//已完成第二個步驟，正在進行第三個任務
			//-------------------------------------------------------------------
				$sql="
					SELECT * FROM `mssr_forum`.`dev_mission_step_log` as a
					inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
					WHERE a.`accept_uid`={$uid} and a.`deliver_uid`={$deliver_uid} and b.`step_number`={$step_number_now} and b.`group_task_id`={$task_id}
				";
				$mission_step3 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$master_atask_id = $mission_step3[0]['master_atask_id'];
				$end_time  	     = $mission_step3[0]['end_step_time'];
				$step_score      = $mission_step3[0]['available_score'];
				$step_content    = $mission_step3[0]['step_content'];
				$score_content   = $mission_step3[0]['score_content'];
				//-------------------------------------------------------------------
 				$sql="
						SELECT a.`article_id`,c.`article_title`
						FROM `mssr_forum`.`mssr_forum_article` as a
						inner join `mssr_forum`.`dev_article_group_mission_rev` as b on a.`article_id`=b.`article_id`
						inner join `mssr_forum`.`mssr_forum_article_detail` as c on a.`article_id`=c.`article_id`
						WHERE a.`user_id`={$uid} and a.`article_from`=3 and a.`article_state` =1 and b.`group_task_id`={$task_id}
					";
				$get_article =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$article_id    = $get_article[0]['article_id'];//抓取文章編號
				$article_title = $get_article[0]['article_title'];//抓取活動文章的標題
				//-------------------------------------------------------------------
				$sql="
					SELECT a.`master_atask_id`,b.`master_task_id`,a.`end_step_time`,b.`available_score`,b.`step_content`,b.`score_content`
					FROM `mssr_forum`.`dev_mission_step_log` as a
					inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
					WHERE a.`accept_uid`={$uid} and a.`deliver_uid`={$deliver_uid} and b.`step_number`=2 and b.`group_task_id`={$task_id}
				";
				$mission_step2 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				foreach($mission_step2 as $key => $mission_step2){
					$master_atask_id_step2[$key] = $mission_step2['master_atask_id'];//抓出step2的主的接收任務編號
				}
				//-------------------------------------------------------------------
				//echo json_encode(array("1"=>$master_atask_id_step2[0],"2"=>$master_atask_id_step2[1]));
				$sql="
					SELECT *
					FROM `mssr_forum`.`dev_master_step2_like`
					WHERE `master_atask_id`={$master_atask_id_step2[0]}
				";
				$mission_step2_like_score =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$step2_like_score = $mission_step2_like_score[0]['score'];

				$sql="
					SELECT *
					FROM `mssr_forum`.`dev_master_step2_reply`
					WHERE `master_atask_id`={$master_atask_id_step2[1]}
				";
				$mission_step2_reply_score =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$step2_reply_score = $mission_step2_reply_score[0]['score'];
 			/*	echo "<Pre>";
				echo print_r($sql);
				echo "</Pre>"; */
				$step3_score =0;
				$sql="
					SELECT *
					FROM `mssr_forum`.`dev_master_step3`
					WHERE `master_atask_id`={$master_atask_id}
				";
				$mission_step3_score =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				if(isset($mission_step3_score[0]['score'])){$step3_score = $mission_step3_score[0]['score'];}

				$total_score = $step2_like_score+$step2_reply_score+$step3_score;
				//-------------------------------------------------------------------
				$sql="
					SELECT *
					FROM `mssr_forum`.`dev_complete_mission_log`
					WHERE `group_task_id`={$task_id} and `accept_uid`={$uid} and `deliver_uid`={$deliver_uid}
				";
				$get_mission_finish_time =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$mission_finish_time = $get_mission_finish_time[0]['finish_time'];
				//-------------------------------------------------------------------

				$friend_results =get_forum_friend($uid,$friend_id=0,$arry_conn_mssr);
				$j = 0;
				for($i=0;$i<count($friend_results);$i++){
					if($friend_results[$i]['friend_state']==1){
						if($friend_results[$i]['friend_id']==$uid){
							$friend_list[$j] = $friend_results[$i]['user_id'];
							$j++;
						}else if($friend_results[$i]['friend_id']!=$uid){
							$friend_list[$j] = $friend_results[$i]['friend_id'];
							$j++;
						}
					}
				}
			//--------------------------------------------------
				$i = 0;
				$friend_select_list = array();//暫存可以選擇的好友
				for($k=0; $k<count($friend_list); $k++){
					$sql="
						SELECT *
						FROM `mssr_forum`.`dev_complete_mission_log`
						WHERE `accept_uid`={$friend_list[$k]} and `group_task_id`={$task_id}
					";
					$get_friend_list =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					if(!isset($get_friend_list[0]['master_ctask_id'])){
						$friend_select_list[$i] = $friend_list[$k];
						$i++;
					}
				}
			//--------------------------------------------------					
				$sql="
					SELECT st1.`uid`,sch1.`school_name`,max(c1.`grade`) as grade,c1.`classroom`,c1.`class_code`
					FROM `user`.`student` AS st1
						INNER JOIN  `user`.`class` AS c1 ON ( c1.`class_code` = st1.`class_code` ) 
						INNER JOIN  `user`.`semester` AS sem1 ON ( sem1.`semester_code` = c1.`semester_code` ) 
						INNER JOIN  `user`.`school` AS sch1 ON ( sch1.`school_code` = sem1.`school_code` ) 
					WHERE 
						st1.`uid`={$uid} 
						and NOW() between st1.`start` AND st1.`end`
						and NOW() < st1.`end`
				";
				$get_uid_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$get_school     = $get_uid_data[0]['school_name'];
				$get_grade      = $get_uid_data[0]['grade'];
				$get_classroom  = $get_uid_data[0]['classroom'];
				$get_class_code = $get_uid_data[0]['class_code'];
			
				$friend_class_list = array();//暫存侷限在相同班級的朋友名單
				$j = 0;
				for($w=0; $w<count($friend_select_list); $w++){
					$sql="
						SELECT st1.`uid`
						FROM `user`.`student` AS st1
							INNER JOIN  `user`.`class` AS c1 ON ( c1.`class_code` = st1.`class_code` ) 
							INNER JOIN  `user`.`semester` AS sem1 ON ( sem1.`semester_code` = c1.`semester_code` ) 
							INNER JOIN  `user`.`school` AS sch1 ON ( sch1.`school_code` = sem1.`school_code` ) 
						WHERE 
							st1.`uid`			   ={$friend_select_list[$w]}  
							and c1.`class_code`    ='$get_class_code'
					";				
					$get_class_friend =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					if(isset($get_class_friend[0]['uid'])){
						$friend_class_list[$j] = $get_class_friend[0]['uid'];
						$j++;
					} 			
				}
/* 				echo "<pre>";
				print_r($friend_select_list);
				echo "</pre>";
				echo "<pre>";
				print_r($friend_class_list);
				echo "</pre>";
				die(); */
			//--------------------------------------------------
/* 				$get_name = array();//暫存好友們的名字
				for($k=0; $k<count($friend_select_list); $k++){
					$sql="
						SELECT *
						FROM `user`.`member`
						WHERE `uid`={$friend_select_list[$k]}
					";
					$get_friend_name =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$get_name[$k] = $get_friend_name[0]["name"];
				} */
				$get_name = array();//暫存好友們的名字
				for($k=0; $k<count($friend_class_list); $k++){
					$sql="
						SELECT *
						FROM `user`.`member`
						WHERE `uid`={$friend_class_list[$k]}
					";
					$get_friend_name =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$get_name[$k] = $get_friend_name[0]["name"];
				}
			//--------------------------------------------------
				echo json_encode(array(
				"step_number"			=> $step_number,
				"article_id"			=> $article_id,
				"article_title"			=> $article_title,
				"score_content"			=> $score_content,
				"total_score"			=> $total_score,
				"mission_finish_time"	=> $mission_finish_time,
				"friend_list"			=> $friend_class_list,
				"get_name"				=> $get_name));
			}
		}


?>