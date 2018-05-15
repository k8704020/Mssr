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

		$uid = (int)$arrys_sess_login_info[0]['uid'];
		$u_mission_state = $_POST["u_mission_state"];
		$u_task_id = $_POST["u_task_id"];
		$i_deliver_uid = $_POST["i_deliver_uid"];

		$sql="
			UPDATE `mssr_forum`.`dev_complete_mission_log`
			SET `mission_state`={$u_mission_state},`start_time`= NOW(),`finish_time`=DATE_ADD(NOW(),INTERVAL 7 DAY)
			WHERE `accept_uid`={$uid} and `group_task_id`={$u_task_id} and `deliver_uid`={$i_deliver_uid}
		";
		$update_mission_state =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

		$sql="
			SELECT *
			FROM  `mssr_forum`.`dev_group_mission_master`
			WHERE `group_task_id`={$u_task_id}
		";
 		$mission_master = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($mission_master as $key => $mission_master){
			$master_task_id[$key] = $mission_master['master_task_id'];//取得主的任務流水號
		}


 		if($u_mission_state==2){//接受任務後，新增任務步驟的資料


			$sql="
				SELECT a.`master_atask_id`,a.`accept_uid`,b.`group_task_id`
				FROM `mssr_forum`.`dev_mission_step_log` as a
					inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
				WHERE a.`accept_uid`={$uid} and b.`group_task_id`={$u_task_id} and a.`deliver_uid`={$i_deliver_uid}
			";
			$get_step_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($get_step_data[0]['master_atask_id'])){//拒絕任務後，再次接取，須更新步驟(第一步)的時間
				$sql="
					UPDATE `mssr_forum`.`dev_mission_step_log` as a
					inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
					SET `start_step_time`=NOW(),`end_step_time`=DATE_ADD(NOW(),INTERVAL 3 DAY)
					WHERE a.`step_number`=1 and a.`accept_uid`={$uid} and a.`deliver_uid`={$i_deliver_uid} and b.`group_task_id`={$u_task_id}
				";
				$re_update_step1_data =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

			}else{
				$sql="
					INSERT INTO `mssr_forum`.`dev_mission_step_log`(`deliver_uid`,`master_task_id`,`accept_uid`,`step_number`,`start_step_time`,`end_step_time`)
					VALUES ({$i_deliver_uid},{$master_task_id[0]},{$uid},1,NOW(),DATE_ADD(NOW(),INTERVAL 3 DAY))
				";
				$insert_step1 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				$sql="
					INSERT INTO `mssr_forum`.`dev_mission_step_log`(`deliver_uid`,`master_task_id`,`accept_uid`,`step_number`)
					VALUES ({$i_deliver_uid},{$master_task_id[1]},{$uid},2)
				";
				$insert_step2_1 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				$sql="
					INSERT INTO `mssr_forum`.`dev_mission_step_log`(`deliver_uid`,`master_task_id`,`accept_uid`,`step_number`)
					VALUES ({$i_deliver_uid},{$master_task_id[2]},{$uid},2)
				";
				$insert_step2_2 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				$sql="
					INSERT INTO `mssr_forum`.`dev_mission_step_log`(`deliver_uid`,`master_task_id`,`accept_uid`,`step_number`)
					VALUES ({$i_deliver_uid},{$master_task_id[3]},{$uid},3)
				";
				$insert_step3 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			}
		}
	}







?>