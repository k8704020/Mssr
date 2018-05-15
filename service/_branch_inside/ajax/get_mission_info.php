<?php
//---------------------------------------------------
// 搜尋任務資訊
//
//---------------------------------------------------

//---------------------------------------------------
//輸入 user_id,book_sid
//輸出 任務總總
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
$branch_id = $_POST["branch_id"];

$array = array();
$count = 0;
$sql = "SELECT *
		FROM  `mssr_user_task_inventory`
		LEFT JOIN  mssr_task_period
		ON mssr_user_task_inventory.task_sid = mssr_task_period.task_sid
		WHERE user_id = '{$user_id}'
		AND branch_id = '{$branch_id}'
		AND task_state = '啟用'";
$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
foreach($retrun as $key1=>$val1)
{
	$array[$count]["task_coin_unit_price"] = $val1['task_coin_unit_price'];
	$array[$count]["task_name"] = $val1['task_name'];
	$array[$count]["task_title"] = $val1['task_title'];
	$array[$count]["task_content"] = $val1['task_content'];
	$array[$count]["task_award"] = $val1['task_award'];
	$array[$count]["task_sid"] = $val1['task_sid'];
	$tmp = unserialize($val1['task_sdl_initial_easy']);
	$array[$count]["task_sdl_initial_easy"] = $tmp[0];
	$tmp = unserialize($val1['task_sdl_initial_normal']);
	$array[$count]["task_sdl_initial_normal"] = $tmp[0];
	$tmp = unserialize($val1['task_sdl_initial_hard']);
	$array[$count]["task_sdl_initial_hard"] = $tmp[0];
	$tmp = unserialize($val1['task_sdl_max']);
	$array[$count]["task_sdl_max"] = $tmp[0];
	$count ++;
}

echo json_encode($array,1);




?>