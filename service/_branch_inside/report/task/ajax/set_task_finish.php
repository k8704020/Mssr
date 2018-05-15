<?
//---------------------------------------------------
// 設定完成任務  獲得金錢   
//
//---------------------------------------------------

//---------------------------------------------------
//輸入 task_sid,user_id,branch_id
//輸出 branch_rank,branch_cs,branch_nickname,branch_visit
//---------------------------------------------------
//---------------------------------------------------
//設定與引用
//---------------------------------------------------

	//SESSION
	@session_start();

	//啟用BUFFER
	@ob_start();

	//外掛設定檔
	require_once(str_repeat("../",5)."/config/config.php");
	require_once(str_repeat("../",5)."/inc/get_book_info/code.php");
	require_once(str_repeat("../",4)."/bookstore/inc/tx_sys_sid/code.php");

	 //外掛函式檔
	$funcs=array(
				APP_ROOT.'inc/code',
				APP_ROOT.'lib/php/db/code',
				APP_ROOT.'lib/php/array/code'
				);
	func_load($funcs,true);
	

	//清除並停用BUFFER
	@ob_end_clean(); 
	
	//建立連線 user
	$conn_user=conn($db_type='mysql',$arry_conn_user);
	$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

//---------------------------------------------------
//END   設定與引用
//---------------------------------------------------		 
	//POST
	$task_sid = $_POST["task_sid"];
	$user_id = $_POST["user_id"];
	$branch_id = $_POST["branch_id"];
	
	//回傳
	$data = array();
	$data['coin'] = 0;
	$data['cs'] = 0;
	//=====搜尋任務資訊 JOIN 使用者接取任務的設定數量
	$sql = "SELECT mssr_user_task_tmp.task_sdl_goal,
				   mssr_user_task_tmp.task_coin_unit_price,
				   mssr_user_task_tmp.task_coin_bonus,
				   mssr_task_period.task_id
			FROM  `mssr_user_task_tmp`
			LEFT JOIN mssr_task_period
			ON mssr_task_period.task_sid =  mssr_user_task_tmp.task_sid
			WHERE mssr_user_task_tmp.task_sid = '{$task_sid}'
			AND user_id =$user_id";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	$task_id = $retrun[0]["task_id"];
	
	
	$sc_array = array(0,15,15,50,100,30);
	//=====計算獲得獎勵
	$get_coin = $retrun[0]["task_sdl_goal"]*$retrun[0]["task_coin_unit_price"]*$retrun[0]["task_coin_bonus"];
	$get_sc = (int)$sc_array[$task_id]*$retrun[0]["task_sdl_goal"];
	
	/*//=====增加滿意度至學生資料
	$sql = "UPDATE `mssr_user_branch`
			SET branch_cs = branch_cs + $get_sc
			WHERE user_id = $user_id
			AND branch_id = $branch_id;";
	//=====增加滿意度交易紀錄
	$sql = $sql."INSERT INTO `mssr_branch_cs_log`
						(`user_id`,
						 `branch_id`,
						 `branch_cs`,
						 `keyin_cdate`)
					VALUES
						($user_id,
						 $branch_id,
						 $get_sc,
						 NOW());";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	*/
	//=====增加奎幣至學生資料
	$sql = "UPDATE `mssr_user_info` 
			SET user_coin = user_coin + $get_coin
			WHERE user_id = $user_id
			";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	
	
	
	//=====增加SYS金錢交易紀錄
	$tx_sys_sid = tx_sys_sid($user_id,mb_internal_encoding());
	$sql = "INSERT INTO `mssr_tx_sys_log`(
							`edit_by`,
							`user_id`,
							`tx_sid`,
							`tx_item`,
							`tx_coin`,
							`tx_state`,
							`tx_note`,
							`keyin_cdate`,
							`keyin_mdate`,
							`keyin_ip`
						) VALUES (
							$user_id,
							$user_id,
							'$tx_sys_sid',
							'',
							$get_coin,
							'正常',
							'',
							NOW(),
							NOW(),
							'".$_SERVER["REMOTE_ADDR"]."');";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);					
	
	//增加物品金錢紀錄
	$sql = "SELECT user_coin,box_item,map_item
			FROM  `mssr_user_info` 
			WHERE user_id = $user_id";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);		
	
	$sql = "INSERT INTO `mssr_user_item_log`(
									 `edit_by`,
									 `user_id`,
									 `tx_sid`,
									 `tx_type`,
									 `map_item`,
									 `box_item`,
									 `user_coin`,
									 `log_state`,
									 `log_note`,
									 `keyin_cdate`,
									 `keyin_mdate`,
									 `keyin_ip`
								) VALUES (
									$user_id,
									$user_id,
									'$tx_sys_sid',
									'branch_task_".$task_id."',
									'".$retrun[0]["map_item"]."',
									'".$retrun[0]["box_item"]."',
									'".$retrun[0]["user_coin"]."',
									'正常',
									'',
									NOW(),
									NOW(),
									'".$_SERVER["REMOTE_ADDR"]."');";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	
	//分店收入LOG
	$sql = "INSERT INTO `mssr_branch_coin_log`(
							 `create_by`,
							 `edit_by`,
							 `user_id`,
							 `branch_id`,
							 `branch_coin`,
							 `keyin_cdate`,
							 `keyin_ip`
						) VALUES (
							$user_id,
							$user_id,
							$user_id,
							$branch_id,
							$get_coin,
							NOW(),
							'".$_SERVER["REMOTE_ADDR"]."');";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	
	//=====刪除mssr_user_task_tmp
	$sql = "DELETE FROM `mssr_user_task_tmp` 
			WHERE task_sid ='$task_sid'
			AND user_id = $user_id
			AND branch_id = $branch_id";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	
	//=====將'進行中' 轉為 '成功' mssr_user_task_log
	$sql = "UPDATE `mssr_user_task_log` 
			SET task_state = '成功',
			task_edate = NOW()
			WHERE task_sid ='$task_sid'
			AND user_id = $user_id
			AND branch_id = $branch_id";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

	//設定回傳值
	$data['coin'] = $get_coin;
	$data['cs'] = $get_sc;
echo json_encode($data,1);

?>