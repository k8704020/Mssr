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
$mission_number = $_POST["mission_number"];
$mission_sid = $_POST["mission_sid"];
$branch_id = $_POST["branch_id"];
$array = array();
$array["state"] ="";
$count = 0;

$select_data = array();
$sql = "SELECT  mssr_task_period.task_coin_unit_price,
				mssr_task_period.task_coin_bonus,
				mssr_task_period.task_sid,
				mssr_task_period.task_sdl_initial_easy,
				mssr_task_period.task_sdl_initial_normal,
				mssr_task_period.task_sdl_initial_hard
		FROM  `mssr_user_task_inventory`
		LEFT JOIN  mssr_task_period 
		ON mssr_user_task_inventory.task_sid = mssr_task_period.task_sid
		WHERE mssr_user_task_inventory.user_id = '{$user_id}'
		AND mssr_user_task_inventory.branch_id = '{$branch_id}'
		AND mssr_user_task_inventory.task_sid = '{$mission_sid}'
		AND mssr_task_period.task_state = '啟用'";
		
$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
if(count($retrun))
{
	$select_data["task_coin_bonus"] = $retrun[0]['task_coin_bonus'];
	
	$select_data["task_sid"] = $retrun[0]['task_sid'];
	
	$tmp = unserialize($retrun[0]['task_sdl_initial_easy']);
	$select_data["task_sdl_initial_easy"] = $tmp[0];
	
	$tmp = unserialize($retrun[0]['task_sdl_initial_normal']);
	$select_data["task_sdl_initial_normal"] = $tmp[0];
	
	$tmp = unserialize($retrun[0]['task_sdl_initial_hard']);
	$select_data["task_sdl_initial_hard"] = $tmp[0];
	
	$tmp = unserialize($retrun[0]['task_coin_unit_price']);
	$select_data["task_coin_unit_price"] = $tmp[0];
}
else
{
	$array["state"] = "error";
	$array["error"] = "任務已被接取";
	die(json_encode($select_data,1));	
}

//刪除可使用任務
$sql = "DELETE FROM `mssr_user_task_inventory` 
		WHERE mssr_user_task_inventory.user_id = '{$user_id}'
		AND mssr_user_task_inventory.branch_id = '{$branch_id}'
		AND mssr_user_task_inventory.task_sid = '{$mission_sid}';";
//加入進行任務
$sql = $sql."INSERT INTO `mssr_user_task_log`
		(`user_id`,
		`task_sid`,
		`branch_id`,
		`task_sdl_goal`,
		`task_sdl_initial`,
		`task_coin_unit_price`,
		`task_coin_bonus`,
		`task_sdate`,
		`task_edate`,
		`task_state`
		)VALUES(
		'".$user_id."',
		'".$select_data["task_sid"]."',
		'".$branch_id."',
		'".$mission_number."',
		'".$select_data["task_sdl_initial_normal"]."',
		'".$select_data["task_coin_unit_price"]."',
		'".$select_data["task_coin_bonus"]."',
		now(),
		'',
		'進行中');";
//加入進行任務
$sql = $sql."INSERT INTO `mssr_user_task_tmp`
		(`user_id`,
		`task_sid`,
		`branch_id`,
		`task_sdl_goal`,
		`task_sdl_initial`,
		`task_coin_unit_price`,
		`task_coin_bonus`,
		`task_sdate`,
		`task_edate`
		)VALUES(
		'".$user_id."',
		'".$select_data["task_sid"]."',
		'".$branch_id."',
		'".$mission_number."',
		'".$select_data["task_sdl_initial_normal"]."',
		'".$select_data["task_coin_unit_price"]."',
		'".$select_data["task_coin_bonus"]."',
		now(),
		'');";
$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
$array["state"] = "ok";

echo json_encode($select_data,1);



		
?>