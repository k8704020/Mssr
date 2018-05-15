<?

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
				APP_ROOT.'lib/php/array/code',
				APP_ROOT.'center/teacher_center/inc/code'
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

$dadad = date("Y-m-d  H:i:s");


//依資料搜尋班  年   學校
$array = array(
		"school" => $_POST["school"],
		"group" => $_POST["group"]
		);
//======================輸入: ?      輸出: 區域
$dadad2 = date("Y-m-d");
$user_info = array();
	$user_info["error"]="";
	$user_info["echo"]="";
	//$user_info["count"]=-1;
	$user_info["count"]= -1;
	
	
	
if($array["group"] =="教師")
{
	$sql =" SELECT `star_style`,`star_declaration`,`user_id`,`name`
			FROM `user`.`personnel`
			LEFT JOIN `mssr`.`mssr_user_info`
			ON `mssr_user_info`.`user_id` = `personnel`.`uid`
			LEFT JOIN `user`.`member`
			ON `member`.`uid` = `personnel`.`uid`
			WHERE `school_code` = '".$array["school"]."'
			AND `responsibilities` = 3
			AND '".$dadad2."' BETWEEN `start` AND `end`
			AND `mssr_user_info`.`user_id` IS NOT NULL";
	$user_info["sql"] = $sql;
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	foreach($retrun as $key => $vul)
	{
			$tmp["user_id"] = $vul["user_id"];
			$tmp["user_nickname"] = $vul["name"];
			$tmp["star_declaration"] = $vul["star_declaration"];
			$tmp["star_style"] = $vul["star_style"];
			array_push($user_info,$tmp);
			$user_info["count"]++;
	}		
	$array_data = $user_info;
	die(json_encode($array_data,1));

}
else if($array["group"] =="校長與主任")
{
	$sql =" SELECT `star_style`,`star_declaration`,`user_id`,`name`
			FROM `user`.`personnel`
			LEFT JOIN `mssr`.`mssr_user_info`
			ON `mssr_user_info`.`user_id` = `personnel`.`uid`
			LEFT JOIN `user`.`member`
			ON `member`.`uid` = `personnel`.`uid`
			WHERE `school_code` = '".$array["school"]."'
			AND `responsibilities` <= 2
			AND '".$dadad2."' BETWEEN `start` AND `end`
			AND `mssr_user_info`.`user_id` IS NOT NULL";
	$user_info["sql"] = $sql;
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	foreach($retrun as $key => $vul)
	{
			$tmp["user_id"] = $vul["user_id"];
			$tmp["user_nickname"] = $vul["name"];
			$tmp["star_declaration"] = $vul["star_declaration"];
			$tmp["star_style"] = $vul["star_style"];
			array_push($user_info,$tmp);
			$user_info["count"]++;
	}
	$array_data = $user_info;
	die(json_encode($array_data,1));
}


		
?>