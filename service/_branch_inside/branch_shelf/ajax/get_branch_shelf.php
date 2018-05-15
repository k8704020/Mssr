<?
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
	require_once(str_repeat("../",4)."/config/config.php");
	require_once(str_repeat("../",4)."/inc/get_book_info/code.php");

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
	$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

//---------------------------------------------------
//END   設定與引用
//---------------------------------------------------		 


$data_array = array();
$user_id = (int)$_POST["user_id"];
$branch_id = (int)$_POST["branch_id"];
if($user_id ==0 || $branch_id == 0)
{
	$data_array["state"] = "error";
	die(json_encode($data_array,1));
}



$sql = "SELECT `book_sid` 
		FROM `mssr_branch_shelf`
		WHERE `user_id` = $user_id
		AND `book_on_shelf_state` = $branch_id";
$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
foreach($retrun as $key1=>$val1)
{
	$data_array[$key1]['book_sid'] = $val1['book_sid'];
	$array_select = array("book_name");
	$get_book_info=get_book_info($conn='',$val1['book_sid'],$array_select,$arry_conn_mssr);
	$data_array[$key1]['book_name'] = $get_book_info[0]["book_name"];
}

echo json_encode($data_array,1);



		
?>