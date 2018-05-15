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
		"grade" => $_POST["grade"],
		"class" => $_POST["class"],
		"group" => $_POST["group"],
		);
//======================輸入: X      輸出: 學校
if($array["school"] =="")
{
	
}
//======================輸入:  學校      輸出: 年級
else if($array["grade"] =="")
{
	$array_data = array();
	$sql = "SELECT grade_id,grade_name
			FROM  `mssr_credit_grade`";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	
	foreach($retrun  as $key1=>$val1)
	{
		$array_data[$key1]["grade"] = $val1["grade_id"];
		$array_data[$key1]["grade_name"] = $val1["grade_name"];
	}
	$array_data["error"]="";
	$array_data["echo"]="";
	$array_data["count"]=count($retrun);
	echo json_encode(@$array_data,1);
}
//======================輸入:  學校,年級      輸出: 班級
else if($array["class"] =="")
{
	$array_data = array();
	$sql = "SELECT mssr_credit_class.class_name,main.class_id
			FROM(
				SELECT class_id 
				FROM  `mssr_credit_grade_class_rev` 
				WHERE  `grade_id` = '".$array["grade"]."'
			)AS main
			LEFT JOIN mssr_credit_class ON main.class_id = mssr_credit_class.class_id";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	$array_data = $retrun;
	$array_data["error"]="";
	$array_data["echo"]="";
	$array_data["count"]=count($retrun);
	echo json_encode(@$array_data,1);
}
//======================輸入:  學校,年級,班級      輸出: 組別
else if($array["group"] =="")
{
	$array_data = array();
	$sql = "SELECT mssr_credit_group.group_name,main.group_id
			FROM(
				SELECT group_id 
				FROM  `mssr_credit_class_group_rev` 
				WHERE  `class_id` =  '".$array['class']."'
			)AS main
			LEFT JOIN mssr_credit_group ON main.group_id = mssr_credit_group.group_id";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	$array_data = $retrun;
	$array_data["error"]="";
	$array_data["echo"]="";
	$array_data["count"]=count($retrun);
	echo json_encode(@$array_data,1);
}
//======================輸入:  學校,年級,班級,組別      輸出: 學生ID
else
{
	$array_data = array();

	$user_info = array();
	$user_info["error"]="";
	$user_info["echo"]="";
	$user_info["count"]=-1;
	
	$sql = "SELECT user_id 
			FROM  `mssr_user_credit_group_rev` 
			WHERE  `group_id` = '".$array["group"]."'
			;";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	
	$user_info["echo"]="此學生暫無組別喔";
	foreach($retrun as $key1=>$val1)
	{$user_info["echo"]="";
		$tmp = array();
		$sql = "SELECT user_nickname,star_declaration,star_style
				FROM  `mssr_user_info` 
				WHERE user_id ='".$val1["user_id"]."'";
		$retrun_2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		//如出現無資料學生  將自動建立該學生星球資料
		if(count($retrun_2)==0)
		{
			//搜索該生姓名
			$sql = "SELECT name 
					FROM  `member` 
					WHERE  uid='".$val1["user_id"]."'";
			$retrun_3 = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
			
		    $sql = "INSERT INTO `mssr`.`mssr_user_info`
										 (`user_id`,
										  `user_nickname`,
										  `user_content`,
										  `map_item`,
										  `box_item`,
										  `user_coin`,
										  `star_style`,
										  `star_declaration`,
										  `pet_declaration`,
										  `keyin_cdate`,
										  `keyin_mdate`,
										  `keyin_ip`
									  )VALUES(
										  '".$val1["user_id"]."',
										  '".$retrun_3[0]["name"]."',
										  '',
										  '55,325,220,',
										  '',
										  '1000',
										  'green',
										  '',
										  '',
										  '".$dadad."',
										  '".$dadad."',
										  '".$_SERVER["REMOTE_ADDR"]."');";
			 db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
			 
			 $sql = "SELECT user_nickname,star_declaration,star_style
					 FROM  `mssr_user_info` 
					 WHERE user_id ='".$val1["user_id"]."'";
			 $retrun_2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		}
		
		//搜索父母資料
		$tmp["parent_id"] = "";
		$sql = "SELECT uid_main
				FROM  `kinship` 
				WHERE uid_sub ='".$val1["user_id"]."'";
		$retrun_5 = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);	
		if(count($retrun_5)>0)
		{//有家長時 搜尋有無書店基本資料
			$tmp["parent_id"] = $retrun_5[0]["uid_main"];
			$sql = "SELECT user_id 
					FROM  `mssr_user_info` 
					WHERE  user_id = '".$retrun_5[0]["uid_main"]."'";
			$retrun_3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);	
			if(count($retrun_3)==0)//搜尋無資料時  建立家長的書店資料
			{
				//搜索該生姓名
				$sql = "SELECT name 
						FROM  `member` 
						WHERE  uid='".$retrun_5[0]["uid_main"]."'";
				$retrun_4 = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
				
				$sql = "INSERT INTO `mssr`.`mssr_user_info`
											 (`user_id`,
											  `user_nickname`,
											  `user_content`,
											  `map_item`,
											  `box_item`,
											  `user_coin`,
											  `star_style`,
											  `star_declaration`,
											  `pet_declaration`,
											  `keyin_cdate`,
											  `keyin_mdate`,
											  `keyin_ip`
										  )VALUES(
											  '".$retrun_5[0]["uid_main"]."',
											  '".$retrun_4[0]["name"]."',
											  '',
											  '55,325,220,',
											  '',
											  '1000',
											  'green',
											  '',
											  '',
											  '".$dadad."',
											  '".$dadad."',
											  '".$_SERVER["REMOTE_ADDR"]."');";
				 db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);	
			}
		$tmp["parent_id"] = $retrun_5[0]["uid_main"];
		}
		
		if($array["school"]!="gcp")
		{
			$tmp["parent_id"] = "";
		}
		
		//搜索該生姓名((改填UID))
		$sql = "SELECT name 
				FROM  `member` 
				WHERE  uid='".$val1["user_id"]."'";
		$retrun_3 = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		
		$tmp["user_id"] = $val1["user_id"];
		$tmp["user_nickname"] = $retrun_3[0]["name"];
		$tmp["star_declaration"] = $retrun_2[0]["star_declaration"];
		$tmp["star_style"] = $retrun_2[0]["star_style"];
		array_push($user_info,$tmp);
		$user_info["count"]++;
	}
	$array_data = $user_info;
	echo json_encode($array_data,1);
	
}

		
?>