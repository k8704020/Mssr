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
			
		//-----------------------------------------------	
			$sql="
				SELECT a.`deliver_uid`
				FROM `mssr_forum`.`dev_mission_step_log` as a
					inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
				WHERE a.`accept_uid`={$uid} and b.`group_task_id`={$task_id} 
			";
			$get_data1 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$deliver_uid = $get_data1[0]['deliver_uid'];	
			
			$sql="
				SELECT Max(a.`step_number`) as step_number
				FROM `mssr_forum`.`dev_mission_step_log` as a
					inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
				WHERE a.`deliver_uid`={$deliver_uid} and a.`accept_uid`={$uid} and b.`group_task_id`={$task_id} and a.`step_state`=1
			";
			$get_data2 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);		
			if(is_null($get_data2[0]['step_number'])){//步驟是空值，表示正在進行第一步
				$step_number = 0;
				$step_number_now = (int)$step_number+1;//現在正在進行的步驟
			}			
			
			//------------------------------------------------------
			$sql="
				SELECT *
				FROM `mssr_forum`.`dev_mission_step_log` as a
				inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
				WHERE a.`deliver_uid`={$deliver_uid} and a.`accept_uid`={$uid} and b.`step_number`={$step_number_now} and b.`group_task_id`={$task_id}
			";
			$get_master_atask_id =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			foreach($get_master_atask_id as $key => $get_master_atask_id){
				$master_atask_id[$key] = $get_master_atask_id['master_atask_id'];//抓取主的任務接收編號
				$step_score[$key]	   = $get_master_atask_id['available_score'];//抓取主的任務的可獲得分數
			}
			//------------------------------------------------------
			//第一步驟> 抓取文章編號與書本編號
			//------------------------------------------------------
			if($step_number==0){
				//------------------------------------------------------
				$sql="
					SELECT *
					FROM `mssr_forum`.`mssr_forum_article` as a
					inner join `mssr_forum`.`mssr_forum_article_book_rev` as b on a.`article_id`=b.`article_id`
					inner join `mssr_forum`.`mssr_forum_article_eagle_rev` as c on a.`article_id`=c.`article_id`
					inner join `mssr_forum`.`dev_article_group_mission_rev` as d on a.`article_id`=d.`article_id`
					WHERE `user_id`={$uid} and `article_from`=3 and `article_state` =1 and `group_task_id`={$task_id}
				";
				$get_article =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$article_id = $get_article[0]['article_id'];
				$book_sid   = $get_article[0]['book_sid'];

				if(isset($get_article[0]['article_id'])){//如果有實際抓到文章編號，則新增資料進第一步的log、更新第一步已完成、更新任務步驟時間
					//------------------------------------------------------
					//將資料Insert進step1的資料表
					//------------------------------------------------------
					$sql="
						INSERT INTO `mssr_forum`.`dev_master_step1`(`master_atask_id`,`book_sid`,`article_id`,`score`)
						VALUES ({$master_atask_id[0]},'$book_sid',{$article_id},30)
					";
					$insert_mission_step1 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					//------------------------------------------------------
					//更新log資料表
					//------------------------------------------------------
					$sql="
						UPDATE `mssr_forum`.`dev_mission_step_log` as a
						inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
						SET `step_state`=1
						WHERE a.`step_number`=1 and a.`accept_uid`={$uid} and a.`deliver_uid`={$deliver_uid} and b.`group_task_id`={$task_id}
					";
					$update_mission_step1 =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

					$sql="
						UPDATE `mssr_forum`.`dev_mission_step_log` as a
						inner join `mssr_forum`.`dev_group_mission_master` as b on a.`master_task_id` = b.`master_task_id`
						SET `start_step_time`= NOW(),`end_step_time`=DATE_ADD(NOW(),INTERVAL 3 DAY)
						WHERE a.`step_number`=2 and a.`accept_uid`={$uid} and a.`deliver_uid`={$deliver_uid} and b.`group_task_id`={$task_id}
					";
					$update_mission_step_time =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				}
			}
		}


?>