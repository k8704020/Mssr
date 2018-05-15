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
            APP_ROOT.'service/_dev_forum_eric_achievement/inc/code'
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
		$sql="
			SELECT *
			FROM `mssr_forum`.`dev_group_mission`
		";
		$group_mission_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		$task_number =0;
		$task_topic = array();
		$group_task_id = array();
		foreach($group_mission_data as $key => $group_mission_data){//抓取目前有的推播任務
			$group_task_id[$key] = $group_mission_data['group_task_id'];
			$task_topic[$key]    = $group_mission_data['gask_topic'];
			$task_number +=1;//存取任務的數量
		}
		//----------------------------------------
 		$sql="
			SELECT `user_id`,`friend_id`
			FROM `mssr_forum`.`mssr_forum_friend`
			WHERE (`user_id`={$uid} or `friend_id`={$uid})and `friend_state`=1
		";
		$get_friend =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		$friend_number =0;
		foreach($get_friend as $key => $get_friend){
			$my_friend1[$key] = $get_friend['user_id'];
			$my_friend2[$key] = $get_friend['friend_id'];
			$friend_number +=1;//存取好友的數量
		}

		$my_friend = array();//暫存自己與好友們的uid
		for($x=1; $x<=$friend_number; $x++){
			$my_friend[0] = $uid;
			if($my_friend1[$x-1]!=$uid){ $my_friend[$x] = $my_friend1[$x-1];}
			else if($my_friend2[$x-1]!=$uid){ $my_friend[$x] = $my_friend2[$x-1];}
		}
		//----------------------------------------
		$mission_state = array();//暫存自己與好友們的任務狀態
		$get_name = array();//暫存實際顯示的自己與好友們的名字
		$get_potential_score = array();//暫存自己可能拿到的分數
		$article_id = array();
		$article_title = array();
		$get_book_name = array();
		$deliver_cno = array();
		$book_id = array();

		//-------------------------------
 		for($x=0 ;$x<$task_number; $x++){
		//-------------------------------
			$sql="
				SELECT c.`book_sid`
				FROM `mssr_forum`.`dev_mission_step_log` as a
					inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
					inner join `mssr_forum`.`dev_master_step1` as c on a.`master_atask_id` = c.`master_atask_id`
				WHERE a.`step_number`=1 and b.`group_task_id`={$group_task_id[$x]} and a.`accept_uid`={$uid}
			";
			$get_book_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($get_book_data[0]['book_sid'])){
				$arry_book_infos=get_book_info($conn_mssr,$get_book_data[0]['book_sid'],$array_filter=array('book_name'),$arry_conn_mssr);
				$get_book_name[$x] = $arry_book_infos[0]['book_name'];
				$book_id[$x] = $get_book_data[0]['book_sid'];
			}else{
				$get_book_name[$x] = '未選擇書籍';
				$book_id[$x] = 0;
			}

			$sql="
				SELECT count(`master_ctask_id`) as deliver_cno
				FROM `mssr_forum`.`dev_complete_mission_log`
				WHERE `group_task_id`={$group_task_id[$x]} and `deliver_uid`={$uid}
			";
			$get_deliver_cno =db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
			if(isset($get_deliver_cno[$x]['deliver_cno'])){
				$deliver_cno[$x] = $get_deliver_cno[$x]['deliver_cno'];

			}else{
				$deliver_cno[$x] = 0;
			}
			//-------------------------------
			for($y=0; $y<=$friend_number; $y++){
			//-------------------------------
				$sql="
					SELECT *
					FROM `mssr_forum`.`dev_complete_mission_log` as a
					inner join `user`.`member` as b on (a.`accept_uid`=b.`uid`)
					WHERE a.`accept_uid`={$my_friend[$y]} and a.`group_task_id`={$group_task_id[$x]}
				";
				$mission_friend_state =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				if(isset($mission_friend_state[0]['master_ctask_id'])){
					foreach($mission_friend_state as $key => $mission_friend_state){//抓取推播任務的狀態
						$mission_state[$x][$y] = $mission_friend_state['mission_state'];
						$get_name[$x][$y]      = $mission_friend_state['name'];
					}
				}else{
					$mission_state[$x][$y]=4;
					$get_name[$x][$y] = "暫無";
				}

				$sql="
 						SELECT a.`article_id` ,c.`article_title`
						FROM `mssr_forum`.`mssr_forum_article` as a
						inner join `mssr_forum`.`dev_article_group_mission_rev` as b on a.`article_id`=b.`article_id`
						inner join `mssr_forum`.`mssr_forum_article_detail` as c on a.`article_id`=c.`article_id`
						WHERE a.`user_id`={$my_friend[$y]} and a.`article_from`=3 and a.`article_state` =1 and b.`group_task_id`={$group_task_id[$x]}
					";
				$get_article =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				if(isset($get_article[0]['article_id'])){
					$article_id[$x][$y]    = $get_article[0]['article_id'];//抓取文章編號
					$article_title[$x][$y] = $get_article[0]['article_title'];//抓取活動文章的標題
				}else{
					$article_id[$x][$y]    = 0;
					$article_title[$x][$y] = "暫無發文";
				}
				//----------------------------------------------
				$sql="
					SELECT MAX(b.`step_number`) as step_number
					FROM  `mssr_forum`.`dev_mission_step_log` as a
					inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
					WHERE `accept_uid`={$my_friend[$y]} and b.`group_task_id`={$group_task_id[$x]} and a.`step_state`=1
				";
				$mission_step =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				if(isset($mission_step[0]['step_number'])){
					$step_number[$x][$y] = $mission_step[0]['step_number'];
				}else{
					$step_number[$x][$y] = 0;//存取每個人的任務進度步驟
				}
				//----------------------------------------------
				$sql="
					SELECT *
					FROM `mssr_forum`.`dev_mission_step_log` as a
					inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
					WHERE a.`accept_uid`={$my_friend[$y]} and b.`group_task_id`={$group_task_id[$x]}
				";
				$get_master_atask_id =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				foreach($get_master_atask_id as $key => $get_master_atask_id){
					$master_atask_id[$x][$key] = $get_master_atask_id['master_atask_id'];//抓取主的任務接收編號
					$step_score[$x][$key]	   = $get_master_atask_id['available_score'];//抓取主的任務的可獲得分數
				}

				/* echo "<pre>";
				print_r($master_atask_id[$x][0]);
				echo "</pre>"; */
				//----------------------------------------------
/* 				if($y==0){//針對自己的任務
					$sql="
						SELECT c.`book_sid`
						FROM `mssr_forum`.`dev_mission_step_log` as a
							inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
							inner join `mssr_forum`.`dev_master_step1` as c on a.`master_atask_id` = c.`master_atask_id`
						WHERE a.`step_number`=1 and b.`group_task_id`={$group_task_id[$x]} and a.`accept_uid`={$my_friend[$y]}
					";
					$get_book_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
					if(isset($get_book_data[0]['book_sid'])){
						$arry_book_infos=get_book_info($conn_mssr,$get_book_data[0]['book_sid'],$array_filter=array('book_name'),$arry_conn_mssr);
						$get_book_name[$x] = $arry_book_infos['book_name'];

					}else{
						$get_book_name[$x] = '未選擇書籍';
					}

					$sql="
						SELECT count(`master_ctask_id`) as deliver_cno
						FROM `mssr_forum`.`dev_complete_mission_log`
						WHERE `group_task_id`={$group_task_id[$x]} and `deliver_uid`={$my_friend[$y]}
					";
					$get_deliver_cno =db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
					if(isset($get_deliver_cno[$x]['deliver_cno'])){

						$deliver_cno[$x] = $get_deliver_cno[$x]['deliver_cno'];

					}else{
						$deliver_cno[$x] = 0;
					}
				} */

				if($step_number[$x][$y]==0){//正在執行第一步驟
					$get_potential_score[$x][$y] = 0;
					$like_cno[$x][$y] = 0;
					$article_reply_cno[$x][$y] = 0;

				}else if($step_number[$x][$y]==1){//正在執行第二步驟
 					 $sql="
						SELECT sum(a.`article_like_cno`) as cno
						FROM `mssr_forum`.`mssr_forum_article` as a
						inner join `mssr_forum`.`dev_article_group_mission_rev` as b on a.`article_id`=b.`article_id`
						WHERE a.`article_id`={$article_id[$x][$y]}
					";
					$get_article_like_cno =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					if(isset($get_article_like_cno[0]['cno'])){
						$article_like_cno[$x][$y] = $get_article_like_cno[0]['cno'];//抓取活動文章的按讚數
					}else{
						$article_like_cno[$x][$y] = 0;//文章的按讚數
					}
					//----------------------------------------------
					$sql="
						SELECT sum(a.`reply_like_cno`) as cno
						FROM `mssr_forum`.`mssr_forum_reply` as a
						inner join `mssr_forum`.`dev_reply_group_mission_rev` as b on a.`reply_id`=b.`reply_id`
						WHERE a.`article_id`={$article_id[$x][$y]} and `reply_state`=1
					";
					$get_article_reply_like_cno =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					if(isset($get_article_reply_like_cno[0]['cno'])){
						$article_reply_like_cno[$x][$y] = $get_article_reply_like_cno[0]['cno'];//抓取回覆文章的按讚數
					}else{
						$article_reply_like_cno[$x][$y] = 0;//回覆文章的按讚數
					}
					//----------------------------------------------
					$sql="
						SELECT count(a.`reply_id`) as cno
						FROM `mssr_forum`.`mssr_forum_reply` as a
						inner join `mssr_forum`.`dev_reply_group_mission_rev` as b on a.`reply_id`=b.`reply_id`
						WHERE a.`article_id`={$article_id[$x][$y]} and a.`user_id`!={$my_friend[$y]} and a.`reply_state`=1
					";
					$get_article_reply_cno =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					if(isset($get_article_reply_cno[0]['cno'])){
						$article_reply_cno[$x][$y] = $get_article_reply_cno[0]['cno'];
					}else{
						$article_reply_cno[$x][$y] = 0;//回覆文章的次數
					}
					$like_cno[$x][$y] = $article_like_cno[$x][$y]+$article_reply_like_cno[$x][$y];

					$get_potential_score[$x][$y]=
					$step_score[$x][0]+
					$like_cno[$x][$y]*$step_score[$x][1]+
					$article_reply_cno[$x][$y]*$step_score[$x][2];
				//----------------------------------------------
				}else if($step_number[$x][$y]==2){//正在執行第三步驟
					$sql="
						SELECT *
						FROM `mssr_forum`.`dev_master_step2_like`
						WHERE `master_atask_id`={$master_atask_id[$x][1]}
					";
					$get_step2_1_score =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$step2_1_score[$x][$y] = $get_step2_1_score[0]['score'];
					$like_cno[$x][$y]      = $get_step2_1_score[0]['like_number'];

					$sql="
						SELECT *
						FROM `mssr_forum`.`dev_master_step2_reply`
						WHERE `master_atask_id`={$master_atask_id[$x][2]}
					";
					$get_step2_2_score =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$step2_2_score[$x][$y] 	  = $get_step2_2_score[0]['score'];
					$article_reply_cno[$x][$y]= $get_step2_2_score[0]['reply_number'];

					$get_potential_score[$x][$y]=$step_score[$x][0]+$step2_1_score[$x][$y]+$step2_2_score[$x][$y];

				}else if($step_number[$x][$y]==3){//已完成所有步驟
					$sql="
						SELECT *
						FROM `mssr_forum`.`dev_master_step2_like`
						WHERE `master_atask_id`={$master_atask_id[$x][1]}
					";
					$get_step2_1_score =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$like_cno[$x][$y] = $get_step2_1_score[0]['like_number'];

					$sql="
						SELECT *
						FROM `mssr_forum`.`dev_master_step2_reply`
						WHERE `master_atask_id`={$master_atask_id[$x][2]}
					";
					$get_step2_2_score =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$article_reply_cno[$x][$y]= $get_step2_2_score[0]['reply_number'];
					$sql="
						SELECT *
						FROM `mssr_forum`.`dev_complete_mission_log`
						WHERE `accept_uid`={$my_friend[$y]} and `group_task_id`={$group_task_id[$x]}
					";
					$get_total_score =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$get_potential_score[$x][$y] = $get_total_score[0]['total_master_score'];
				}

			}
			//---------------------------------------------
		}
		//------------------------------------------------------
		$deliver_name = array();//暫存任務的傳遞者是誰
		$deliver_uid = array();//暫存任務的傳遞者是誰
		for($x=0 ;$x<$task_number; $x++){
			$sql="
				SELECT a.`deliver_uid`,b.`name` as name
				FROM `mssr_forum`.`dev_complete_mission_log` as a
				inner join `user`.`member` as b on (a.`deliver_uid`=b.`uid`)
				WHERE a.`accept_uid`={$uid} and a.`group_task_id`={$group_task_id[$x]}
			";
			$mission_deliver =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($mission_deliver[0]['deliver_uid'])){
					$deliver_name[$x] = $mission_deliver[0]['name'];//抓取是誰傳給自己(name)
					$deliver_uid[$x]  = $mission_deliver[0]['deliver_uid'];//抓取是誰傳給自己(uid)
			}else{
					$deliver_name[$x] = "暫無";
					$deliver_uid[$x]  = "";
			}
		}
		//------------------------------------------------------
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
			$get_title_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			foreach($get_title_data as $key =>$get_title_data){
				$title_name[$key]  = $get_title_data['title_name'];
				$title_score[$key] = $get_title_data['title_score'];
			}
		//------------------------------------------------------

			$sql="
				SELECT *
				FROM `mssr_forum`.`dev_member_score`
				WHERE `u_id`={$uid}
			";
			$get_member_sid =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$get_score = $get_member_sid[0]["score"];

			$get_score = 0;

			if(isset($get_member_sid[0]["s_id"])){
				$get_score = $get_member_sid[0]["score"];

			//當member_score表中，有使用者的資料時，更新一筆最新的資料
				$sql="
					UPDATE `mssr_forum`.`dev_member_score`
					SET `score`={$get_score}
					WHERE `u_id`={$uid}
				";
				$update_score =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

			} else if(!isset($get_member_sid[0]["s_id"])){//當member_score表中，"沒有"使用者的資料時，新增一筆最新的資料

				$sql="
					INSERT INTO `mssr_forum`.`dev_member_score`(`u_id`,`score`)
					VALUES ({$uid},{$get_score})
				";
				$insert_score =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			}
			for( $x=1; $x<=7; $x++){
				if($get_score < $title_score[$x-1]){
					$title_next = $x;//取得下一個頭銜的等級
					break;
				}
			}

			$progress_score =floor((($get_score-$title_score[$title_next-2])/$title_score[$title_next-1])*100);

		//------------------------------------------------------

		echo json_encode(array(
		"task_number"			=>$task_number,
		"group_task_id"			=>$group_task_id,
		"task_topic"			=>$task_topic,
		"mission_state"			=>$mission_state,
		"friend_number"			=>$friend_number,
		"my_friend"				=>$my_friend,
		"get_name"				=>$get_name,
		"deliver_name"			=>$deliver_name,
		"deliver_uid"			=>$deliver_uid,
		"friend_number"			=>$friend_number,
		"title_name"			=>$title_name,
		"title_score"			=>$title_score,
		"title_next"			=>$title_next,
		"get_score"				=>$get_score,
		"progress_score"		=>$progress_score,
		"article_id"			=>$article_id,
		"article_title"			=>$article_title,
		"like_cno"				=>$like_cno,
		"article_reply_cno"		=>$article_reply_cno,
		"step_number"			=>$step_number,
		"sex"					=>$sex,
		"get_potential_score"	=>$get_potential_score,
		"get_book_name"			=>$get_book_name,
		"deliver_cno"			=>$deliver_cno,
		"book_id"               =>$book_id));

	}








?>