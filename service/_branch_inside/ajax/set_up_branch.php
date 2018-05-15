<?
//---------------------------------------------------
// 接下任務
//
//---------------------------------------------------

//---------------------------------------------------
//輸入 user_id,mission_number,mission_sid,branch_id
//輸出 ok
//---------------------------------------------------
//---------------------------------------------------
//設定與引用
//---------------------------------------------------

	//SESSION
	@session_start();

	//啟用BUFFER
	@ob_start();

	//外掛設定檔
	require_once(str_repeat("../",3)."/config/config.php");
	require_once(str_repeat("../",3)."/inc/get_book_info/code.php");
	require_once(str_repeat("../",2)."/bookstore/inc/tx_sys_sid/code.php");

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

//
	
	$user_id = $_POST["user_id"];
	$branch_id = $_POST["branch_id"];
	$data = array();
	$tmp = array();
	$data["state"]= "";
	
	//=====判斷  是否為本人
	/*if($_SESSION['uid'] != $user_id) 
	{
		$data["state"]="not_user";
		die(json_encode($data,1));
	}
	*/
	//=====搜尋  本人金錢
	$sql = "SELECT user_coin,map_item,box_item
	FROM  `mssr_user_info` 
	WHERE user_id = $user_id";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	$tmp["user_coin"] = $retrun[0]['user_coin'];
	$tmp["map_item"] = $retrun[0]['map_item'];
	$tmp["box_item"] = $retrun[0]['box_item'];

	
	//=====搜尋  本人分店滿意度 現在等級
	$sql = "SELECT branch_cs,branch_rank
	FROM  `mssr_user_branch` 
	WHERE user_id = $user_id
	AND branch_id = $branch_id
	AND branch_state = '啟用'";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	$tmp["branch_rank"] = $retrun[0]['branch_rank'];
	$tmp["branch_cs"] = $retrun[0]['branch_cs'];
	
	//=====搜尋  升級所需金錢
	$sql = "SELECT spent_coin,branch_cs
	FROM  `mssr_branch_rank` 
	WHERE branch_rank = ".((int)$tmp["branch_rank"]+1)."
	AND branch_id = $branch_id";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	$tmp["need_branch_cs"] = $retrun[0]['branch_cs'];
	$tmp["need_spent_coin"] = $retrun[0]['spent_coin'];
	
	//=====判斷  是否足夠升級
	if($tmp["need_spent_coin"] > $tmp["user_coin"])
	{
		$data["state"]="not_using_coin";
		die(json_encode($data,1));
	}
	
	
	/*if($tmp["need_branch_cs"] > $tmp["branch_cs"])
	{
		$data["state"]="not_using_cs";
		die(json_encode($data,1));
	}*/
	
	//=====寫入  提升本人店等級
	$sql = "UPDATE `mssr_user_branch` 
			SET branch_rank = branch_rank +1
			WHERE user_id = $user_id
			AND branch_id = $branch_id
			AND branch_state = '啟用';";

	
	//=====寫入  本人扣除的金錢
	$sql = $sql."UPDATE `mssr_user_info` 
			SET user_coin = user_coin - ".$tmp["need_spent_coin"]."
			WHERE user_id = $user_id
			;";
	
	
	//=====增加SYS金錢交易紀錄
	$tx_sys_sid = tx_sys_sid($user_id,mb_internal_encoding());
	$sql = $sql."INSERT INTO `mssr_tx_sys_log`(
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
							-".$tmp["need_spent_coin"].",
							'正常',
							'',
							NOW(),
							NOW(),
							'".$_SERVER["REMOTE_ADDR"]."');";
					
	
	//=====增加物品金錢紀錄
	$sql = $sql."INSERT INTO `mssr_user_item_log`(
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
									'branch_lv_up_".$branch_id."',
									'".$tmp["map_item"]."',
									'".$tmp["box_item"]."',
									".((int)$tmp["user_coin"]-(int)$tmp["need_spent_coin"]).",
									'正常',
									'',
									NOW(),
									NOW(),
									'".$_SERVER["REMOTE_ADDR"]."');";

	
	//分店收入LOG
	$sql = $sql."INSERT INTO `mssr_branch_coin_log`(
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
							-".((int)$tmp["need_spent_coin"]).",
							NOW(),
							'".$_SERVER["REMOTE_ADDR"]."');";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	$data["state"]="ok";
	
echo json_encode($data,1);



		
?>