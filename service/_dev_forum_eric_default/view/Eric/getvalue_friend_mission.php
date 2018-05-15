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
		$friend_uid = $_POST["friend_uid"];
		$task_id = $_POST["task_id"];
		//--------------------------------------------------
		$sql="
			SELECT *
			FROM `mssr_forum`.`dev_mission_slave_log`
			WHERE `group_task_id`={$task_id} and `view_uid`={$uid} and `accept_uid`={$friend_uid}
		";
		$geted_slave_atask_id = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		if(isset($geted_slave_atask_id[0]['slave_atask_id'])){//如果已有紀錄，則撈出該筆紀錄的id
			$slave_atask_id = $geted_slave_atask_id[0]['slave_atask_id'];//取得之前insert進去的id
		}else{//如果沒有紀錄，則insert一筆新的資料
			$sql="
				INSERT INTO `mssr_forum`.`dev_mission_slave_log`(`group_task_id`,`view_uid`,`accept_uid`)
				VALUES ({$task_id},{$uid},{$friend_uid})
			";
			$insert_slave_atask_id =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$sql="
				SELECT *
				FROM `mssr_forum`.`dev_mission_slave_log`
				WHERE `group_task_id`={$task_id} and `view_uid`={$uid} and `accept_uid`={$friend_uid}
			";
			$get_slave_atask_id =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$slave_atask_id = $get_slave_atask_id[0]['slave_atask_id'];//取得剛剛insert進去的id
		}
		//--------------------------------------------------
		$article_id = 0;
		$sql="
			SELECT a.`article_id`,c.`article_title`
			FROM `mssr_forum`.`dev_article_group_mission_rev` as a
			inner join `mssr_forum`.`mssr_forum_article` as b on a.`article_id`=b.`article_id`
			inner join `mssr_forum`.`mssr_forum_article_detail` as c on a.`article_id`=c.`article_id`
			WHERE b.`user_id`={$friend_uid} and a.`group_task_id`={$task_id}
		";
		$get_article =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		if(isset($get_article[0]['article_id'])){//如果朋友「有發表文章」的話...
			$article_id    = $get_article[0]['article_id'];//抓取文章編號
			$article_title = $get_article[0]['article_title'];//抓取活動文章的標題

		//--------------------------------------------------
				$article_like_cno = 0;//「自已」對文章的按讚數
				$sql="
					SELECT *
					FROM `mssr_forum`.`mssr_forum_article_like_log`
					WHERE `article_id`={$article_id} and `user_id`={$uid}
				";
				$get_article_like_cno =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				if(isset($get_article_like_cno[0]['article_id'])){//抓取活動文章的按讚數
					$article_like_cno = 1;
				}
		//-------------------------------------------------------------------
				$article_reply_like_cno = 0;//「自已」對回覆文章的按讚數
				$sql="
					SELECT count(`reply_id`) as cno
					FROM `mssr_forum`.`mssr_forum_reply_like_log`
					WHERE `article_id`={$article_id} and `user_id`={$uid}
				";
				$get_article_reply_like_cno =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$article_reply_like_cno = $get_article_reply_like_cno[0]['cno'];//抓取回覆文章的按讚數
		//-------------------------------------------------------------------
			$article_reply_cno = 0;//「自己」回覆文章的次數
			$sql="
				SELECT count(a.`reply_id`) as cno
				FROM `mssr_forum`.`mssr_forum_reply` as a
				inner join `mssr_forum`.`dev_reply_group_mission_rev` as b on a.`reply_id`=b.`reply_id`
				WHERE a.`article_id`={$article_id} and a.`user_id`={$uid} and a.`reply_state`=1
			";
			$get_article_reply_cno =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($get_article_reply_cno[0]['cno'])){
				$article_reply_cno = $get_article_reply_cno[0]['cno'];
			}
		//-------------------------------------------------------------------
			$reply_be_like_cno = 0;//「自己」的回覆被按讚
			$sql="
				SELECT count(b.`reply_id`) as cno
				FROM `mssr_forum`.`mssr_forum_reply` as a
				inner join 	`mssr_forum`.`mssr_forum_reply_like_log` as b on a.`article_id`=b.`article_id` and a.`reply_id`=b.`reply_id`
				WHERE a.`article_id`={$article_id} and a.`user_id`={$uid} and b.`user_id`!={$uid} and a.`reply_state`=1
			";
			$get_reply_be_like_cno =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($get_reply_be_like_cno[0]['cno'])){
				$reply_be_like_cno = $get_reply_be_like_cno[0]['cno'];
			}
		//-------------------------------------------------------------------取得旁觀者任務資料
			$sql="
				SELECT *
				FROM  `mssr_forum`.`dev_group_mission_slave`
				WHERE `group_task_id`={$task_id} and `step_number`=2
			";
			$get_mission_slave =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			foreach($get_mission_slave as $key =>$get_mission_slave){
				$slave_task_id[$key] = $get_mission_slave['slave_task_id'];
				$step_content[$key]  = $get_mission_slave['step_content'];
				$score_content[$key] = $get_mission_slave['score_content'];
				$step_score[$key]    = $get_mission_slave['available_score'];
			}
		//-------------------------------------------------------------------結算目前獲得的分數
			$like_cno = $article_like_cno + $article_reply_like_cno;
			$step2_like_score		 = $like_cno*$step_score[0];
			$step2_reply_score       = $article_reply_cno*$step_score[1];
			$step2_reply_liked_score = $reply_be_like_cno*$step_score[2];
		//-------------------------------------------------------------------將分數更新進資料庫(條件：任者接受者必須是要在第二步驟)
			$sql="
				SELECT *
				FROM `mssr_forum`.`dev_slave_step2_like`
				WHERE `slave_atask_id`={$slave_atask_id}
			";
			$get_mission_slave_step2 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
/* 			echo "<pre>";
			print_r ($sql);
			echo "</pre>"; */
			if(isset($get_mission_slave_step2[0]['slave_step2_sid1'])){
				$sql="
					UPDATE `mssr_forum`.`dev_slave_step2_like`
					SET `like_number`={$like_cno},`score`={$step2_like_score}
					WHERE `slave_atask_id`={$slave_atask_id}
				";
				$update_slave_step2_like =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

			}else{
				$sql="
					INSERT INTO `mssr_forum`.`dev_slave_step2_like`(`slave_atask_id`,`slave_task_id`,`like_number`,`score`)
					VALUES ({$slave_atask_id},{$slave_task_id[0]},{$like_cno},{$step2_like_score})
				";
				$insert_slave_step2_like =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			}
		//-------------------------------------------------------------------
			$sql="
				SELECT *
				FROM `mssr_forum`.`dev_slave_step2_reply`
				WHERE `slave_atask_id`={$slave_atask_id}
			";
			$get_mission_slave_step2 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($get_mission_slave_step2[0]['slave_step2_sid1'])){
				$sql="
					UPDATE `mssr_forum`.`dev_slave_step2_reply`
					SET `reply_number`={$article_reply_cno},`score`={$step2_reply_score}
					WHERE `slave_atask_id`={$slave_atask_id}
				";
				$update_slave_step2_reply =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

			}else{
				$sql="
					INSERT INTO `mssr_forum`.`dev_slave_step2_reply`(`slave_atask_id`,`slave_task_id`,`reply_number`,`score`)
					VALUES ({$slave_atask_id},{$slave_task_id[1]},{$article_reply_cno},{$step2_reply_score})
				";
				$insert_slave_step2_reply =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			}
		//-------------------------------------------------------------------
			$sql="
				SELECT *
				FROM `mssr_forum`.`dev_slave_step2_reply_liked`
				WHERE `slave_atask_id`={$slave_atask_id}
			";
			$get_mission_slave_step2 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($get_mission_slave_step2[0]['slave_step2_sid1'])){
				$sql="
					UPDATE `mssr_forum`.`dev_slave_step2_reply_liked`
					SET `reply_like_number`={$reply_be_like_cno},`score`={$step2_reply_liked_score}
					WHERE `slave_atask_id`={$slave_atask_id}
				";
				$update_slave_step2_reply_liked =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			}else{
				$sql="
					INSERT INTO `mssr_forum`.`dev_slave_step2_reply_liked`(`slave_atask_id`,`slave_task_id`,`reply_like_number`,`score`)
					VALUES ({$slave_atask_id},{$slave_task_id[2]},{$reply_be_like_cno},{$step2_reply_liked_score})
				";
				$insert_slave_step2_reply_liked =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			}
			echo json_encode(array(
			"article_id"				=>$article_id,
			"article_title"				=>$article_title,
			"like_cno"					=>$like_cno,
			"step2_like_score"			=>$step2_like_score,
			"article_reply_cno"			=>$article_reply_cno,
			"step2_reply_score"			=>$step2_reply_score,
			"reply_be_like_cno"			=>$reply_be_like_cno,
			"step2_reply_liked_score"	=>$step2_reply_liked_score,
			"step_content"				=>$step_content,
			"score_content"				=>$score_content));

		}else{//如果朋友「還沒有發表文章」的話...
			$article_id = 0;
			echo json_encode(array("article_id"=>$article_id));//尚未發表文章
		}


 	}







?>