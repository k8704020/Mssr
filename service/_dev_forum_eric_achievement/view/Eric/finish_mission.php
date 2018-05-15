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

		if(isset($_POST["deliver_uid"])){//自己的完成任務

			$uid = (int)$arrys_sess_login_info[0]['uid'];
			$task_id = $_POST["task_id"];
			$deliver_uid = $_POST["deliver_uid"];

			//-------------------------------------------------------------------
			$sql="
				SELECT *
				FROM `mssr_forum`.`dev_mission_step_log` as a
				inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
				WHERE a.`accept_uid`={$uid} and a.`deliver_uid`={$deliver_uid} and b.`group_task_id`={$task_id}
			";
			$get_all_master_id = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			foreach($get_all_master_id as $key =>$get_all_master_id){
				$all_master_id[$key] = $get_all_master_id['master_atask_id'];
				$step_content[$key]  = $get_all_master_id['step_content'];
			}
			//-------------------------------------------------------------------
			$sql="
				SELECT *
				FROM `mssr_forum`.`dev_master_step1` as a
				inner join `mssr_forum`.`mssr_forum_article_detail` as b on a.`article_id`=b.`article_id`
				WHERE master_atask_id={$all_master_id[0]}
			";
			$get_article_title = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$article_id    = $get_article_title[0]['article_id'];
			$article_title = $get_article_title[0]['article_title'];
			$step1_score   = $get_article_title[0]['score'];
			//-------------------------------------------------------------------
			$sql="
				SELECT *
				FROM `mssr_forum`.`dev_master_step2_like`
				WHERE master_atask_id={$all_master_id[1]}
			";
			$get_article_like = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($get_article_like[0]['like_number'])){
				$like_cno = $get_article_like[0]['like_number'];
			}else{
				$like_cno = 0;
			}

			$sql="
				SELECT *
				FROM `mssr_forum`.`dev_master_step2_reply`
				WHERE master_atask_id={$all_master_id[2]}
			";
			$get_article_reply = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($get_article_reply[0]['reply_number'])){
				$reply_cno = $get_article_reply[0]['reply_number'];
			}else{
				$reply_cno = 0;
			}

			//-------------------------------------------------------------------
			$sql="
				SELECT *
				FROM  `mssr_forum`.`dev_complete_mission_log`
				WHERE `accept_uid`={$uid} and `deliver_uid`={$deliver_uid} and `group_task_id`={$task_id}
			";
			$get_complete_mission_log = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$total_score = $get_complete_mission_log[0]['total_master_score'];
			$finish_time = $get_complete_mission_log[0]['finish_time'];

			echo json_encode(array("article_id"=>$article_id,"article_title"=>$article_title,"like_cno"=>$like_cno,"reply_cno"=>$reply_cno,"total_score"=>$total_score,"finish_time"=>$finish_time,"step_content"=>$step_content));
		}else if(isset($_POST["friend_uid"])){//朋友的完成任務

			$uid = (int)$arrys_sess_login_info[0]['uid'];
			$task_id = $_POST["task_id"];
			$friend_uid = $_POST["friend_uid"];
			//-------------------------------------------------------------------
			$sql="
				SELECT *
				FROM `mssr_forum`.`dev_mission_step_log` as a
				inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
				WHERE a.`accept_uid`={$friend_uid} and b.`group_task_id`={$task_id}
			";
			$get_all_master_id = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			foreach($get_all_master_id as $key =>$get_all_master_id){
				$all_master_id[$key] = $get_all_master_id['master_atask_id'];
				$step_content[$key]  = $get_all_master_id['step_content'];
			}
			//-------------------------------------------------------------------
			$sql="
				SELECT *
				FROM `mssr_forum`.`dev_master_step1` as a
				inner join `mssr_forum`.`mssr_forum_article_detail` as b on a.`article_id`=b.`article_id`
				WHERE master_atask_id={$all_master_id[0]}
			";
			$get_article_title = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$article_id    = $get_article_title[0]['article_id'];
			$article_title = $get_article_title[0]['article_title'];
			$step1_score   = $get_article_title[0]['score'];
			//-------------------------------------------------------------------
			$sql="
				SELECT *
				FROM `mssr_forum`.`dev_master_step2_like`
				WHERE master_atask_id={$all_master_id[1]}
			";
			$get_article_like = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$like_cno = $get_article_like[0]['like_number'];

			$sql="
				SELECT *
				FROM `mssr_forum`.`dev_master_step2_reply`
				WHERE master_atask_id={$all_master_id[2]}
			";
			$get_article_reply = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$reply_cno = $get_article_reply[0]['reply_number'];
			//-------------------------------------------------------------------
			$sql="
				SELECT *
				FROM  `mssr_forum`.`dev_complete_mission_log`
				WHERE `accept_uid`={$friend_uid} and `group_task_id`={$task_id}
			";
			$get_complete_mission_log = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$total_score = $get_complete_mission_log[0]['total_master_score'];
			$finish_time = $get_complete_mission_log[0]['finish_time'];

			echo json_encode(array("article_id"=>$article_id,"article_title"=>$article_title,"like_cno"=>$like_cno,"reply_cno"=>$reply_cno,"total_score"=>$total_score,"finish_time"=>$finish_time,"step_content"=>$step_content));

		}
	}


?>