<?
//---------------------------------------------------
// 外太空 >   
//
//---------------------------------------------------

//---------------------------------------------------
//輸入 user_id,book_sid
//輸出 OK
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
//
$id = (int)mysql_prep($_POST["user_id"]);

// ID   搜尋年班級資訊
$sql = "SELECT permission,uid
		FROM  `member` 
		WHERE ".$id." = uid";
$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);

$array = array(
		"school" => "",
		"grade" => "",
		"class" => "",
		"identity" => "",
		"group" => "",
		"semester_code" => "",
		"class_code" => "",
		"class_category" => "",
		"category" => "",
		"group_name" => "",
		"class_name" => "",
		"school_name" => "",
		"grade_name" => "",
		"special_sky" => 0,
		"error" => "",
		"echo" => ""
		
		);
		
$sql = "SELECT status_info.status
		FROM `permissions` 
		RIGHT JOIN status_info 
		on status_info.status = permissions.status 
		WHERE  `permission` =  '".$retrun[0]["permission"]."'
		AND category_id = 4";

$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);

foreach($retrun as $key1=>$val1)
{	
	if($val1['status'] == 'i_s') $identity = "s";
}
foreach($retrun as $key1=>$val1)
{	
	if($val1['status'] == 'i_f') $identity = "f";
}
foreach($retrun as $key1=>$val1)
{	
	if($val1['status'] == 'i_t') $identity = "t";
}
foreach($retrun as $key1=>$val1)
{	
	if($val1['status'] == 'i_sa') $identity = "t";
}
foreach($retrun as $key1=>$val1)
{	
	if($val1['status'] == 'i_a') $identity = "super";
}

if($identity == "super")
{
	$array["identity"] = "super";
	$array["echo"] = "管理者暫無提供星球社團服務";
}
else
{	
	//分割權限資訊
	
	
	
	
	//辨識身分
	if($identity == "f")//父母帳號 :  將ID轉換為孩子的ID 
	{
		$sql = "SELECT uid_sub 
				FROM  `kinship` 
				WHERE  `uid_main` ='$id'";
		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		$id = $retrun[0]["uid_sub"];
		$identity = "s";
	}
	
	if($identity == "t")
	{
		$sql = "SELECT class_code 
				FROM  `teacher` 
				WHERE uid = ".$id." and start <= '".$dadad."' and end >= '".$dadad."' ";
		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		$tmp = explode("_", $retrun[0]["class_code"]);
		$array["class_code"] = $retrun[0]["class_code"];
		$array["school"] = $tmp[0];
		$array["category"] = $tmp[2];
		$array["grade"] = $tmp[3];
		$array["grade_name"] = $tmp[3];
		$array["class"] = $tmp[4];
		$array["identity"] = "teacher";
		$array["semester_code"] = $tmp[0]."_".$tmp[1]."_".$tmp[2];
		
	}
	else if($identity == "s")
	{
		$sql = "SELECT class_code 
				FROM  `student` 
				WHERE uid = ".$id." and start <= '".$dadad."' and end >= '".$dadad."' ";
		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		$tmp = explode("_", $retrun[0]["class_code"]);
		$array["class_code"] = $retrun[0]["class_code"];
		$array["school"] = $tmp[0];
		$array["category"] = $tmp[2];
		$array["grade"] = $tmp[3];
		$array["grade_name"] = $tmp[3];
		$array["class"] = $tmp[4];
		$array["identity"] = "student";
		$array["semester_code"] = $tmp[0]."_".$tmp[1]."_".$tmp[2];
	}
	//搜尋組別有效時間
	$sql = "SELECT `start`,`end`
			FROM semester
			WHERE semester_code  = '".$array["semester_code"]."';";
	$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
	$start = $retrun[0]['start'];
	$end = $retrun[0]['end'];
	
	//搜尋 並刪除過期組別
	$sql = "SELECT group_sid,keyin_cdate
			FROM mssr_user_group
			WHERE user_id = '".$id."'
			AND  keyin_cdate < '".$start."';";
	$retrunx = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	
	foreach($retrunx as $key1 => $val1)
	{
		 $sql = "DELETE FROM `mssr_user_group` 
				
				WHERE  `user_id` = '".$id."'
				AND group_sid = '".$val1["group_sid"]."';";
		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		
		//向上搜尋該組別		
		$sql = "SELECT count(1) AS count
				FROM mssr_group
				WHERE group_sid = '".$val1["group_sid"]."';";
		$retrun_g = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		
		if($retrun_g[0]["count"]>0)
		{//尋找該組應該中斷的時間 給予中斷
			$sql = "SELECT end 
					FROM  `semester` 
					WHERE school_code = '".$array["school"]."'
					AND '".$val1["keyin_cdate"]."' BETWEEN start AND end
					;";
			$retrunxcc = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);

			$sql = "DELETE FROM `mssr_group` 
					WHERE group_sid = '".$val1["group_sid"]."';";
					
			$sql = $sql."UPDATE `mssr_group_log` SET
					group_edate = '".$retrunxcc[0]["end"]."'
					WHERE group_sid = '".$val1["group_sid"]."';";		
					
			db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		}
	}
	
	//進行搜尋組別	
	$sql = "SELECT group_sid 
			FROM  `mssr_user_group` 
			WHERE user_id = ".$id."
			AND  keyin_cdate BETWEEN '".$start."' AND '".$end."';";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	if(!is_null(@$retrun[0]["group_sid"]))$array["group"]=@$retrun[0]["group_sid"];
	if(!is_null(@$retrun[0]["group_name"]))$array["group_name"]=@$retrun[0]["group_name"];
	
	//無組別的狀態 將分配組別
	if(count($retrun)==0)
	{
		//查詢班級上有無組別
		$sql = "SELECT group_id,group_sid
	 			FROM  `mssr_group` 
				WHERE school_code = '".$array["school"]."'
				AND grade_id = '".$array["grade"]."'
				AND classroom_id = '".$array["class"]."'
				AND  group_sdate BETWEEN '".$start."' AND '".$end."';";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		if(count($retrun)>0)
		{//班級有組別 分配人員至班上隨機一個組別
			$rand_number = rand(0,(count($retrun)-1));
		
			$sql = "INSERT INTO `mssr_user_group`
							   (`create_by`,
								`edit_by`,
								`group_sid`,
								`user_id`,
								`keyin_cdate`,
								`keyin_mdate`,
								`keyin_ip`)
							VALUES
							   ('1',
								'1',
								'".$retrun[$rand_number]["group_sid"]."',
								'".$id."',
								'".$dadad."',
								'".$dadad."',
								'".$_SERVER["REMOTE_ADDR"]."');"
								;
			
			$sql = $sql."INSERT INTO `mssr_user_group_log`
							   (`create_by`,
								`group_sid`,
								`user_id`,
								`keyin_cdate`,
								`keyin_ip`)
							VALUES
							   ('".$id."',
								'".$retrun[$rand_number]["group_sid"]."',
								'".$id."',
								'".$dadad."',
								'".$_SERVER["REMOTE_ADDR"]."')"
								;
			db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
			$array["group"]=@$retrun[$rand_number ]["group_sid"];
			
		}
		else
		{//班級無組別 自動建立該班下所有組別 並分配人員
		
			//搜尋該班人員
			$sql = "SELECT  class,id 
					FROM (
						SELECT class_code AS class,uid AS id
						FROM  `user`.`student`
						WHERE class_code = '".$array["class_code"]."' and start <= '".$dadad."' and end >= '".$dadad."'
						
						UNION  ALL
						
						SELECT class_code AS class,uid AS id
						FROM  `user`.`teacher`
						WHERE class_code = '".$array["class_code"]."' and start <= '".$dadad."' and end >= '".$dadad."'
						)V1";
			$retrun2 =  db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
			$tmp_count = count($retrun2);
			//有學生才進行分組
			if(count($retrun2)>0)
			{
				//依班級人數建立組別
				$sql = "";
				$group_sid_list=array();
				for($i = 1 ; $i*$i<= $tmp_count;$i++)
				{
					$group_sid=group_sid($i,mb_internal_encoding());
					$group_sid_list[($i-1)]=$group_sid;
					$sql = $sql."INSERT INTO `mssr_group`
							(
								`create_by`,
								`edit_by`,
								`school_code`,
								`grade_id`,
								`classroom_id`,
								`group_sid`,
								`group_name`,
								`group_sdate`,
								`group_mdate`,
								`keyin_ip`
							) VALUES (
								'".$i."',
								'".$i."',
								'".$array["school"]."',
								'".$array["grade"]."',
								'".$array["class"]."',
								'".$group_sid."',
								'第".$i."組',
								'".$dadad."',
								'".$dadad."',
								'".$_SERVER["REMOTE_ADDR"]."'
							);";
					$sql = $sql."INSERT INTO `mssr_group_log`
							(
								`create_by`,
								`edit_by`,
								`school_code`,
								`grade_id`,
								`classroom_id`,
								`group_sid`,
								`group_name`,
								`group_sdate`,
								`keyin_ip`
							) VALUES (
								'".$i."',
								'".$i."',
								'".$array["school"]."',
								'".$array["grade"]."',
								'".$array["class"]."',
								'".$group_sid."',
								'第".$i."組',
								'".$dadad."',
								'".$_SERVER["REMOTE_ADDR"]."'
							);";
				}
				db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
				
				$nu = count($retrun2)/count($group_sid_list);
				for($i = 0;$i < count($retrun2); $i++)
				{
					//分配人員至班上排序好的組別
					$group_sid_list[$i%(int)count($group_sid_list)]."___".(int)count($group_sid_list)."<BR>";
					
					$sql = "INSERT INTO `mssr_user_group`
									   (`create_by`,
										`edit_by`,
										`group_sid`,
										`user_id`,
										`keyin_cdate`,
										`keyin_mdate`,
										`keyin_ip`)
									VALUES
									   ('1',
										'1',
										'".$group_sid_list[$i%((int)count($group_sid_list))]."',
										'".$retrun2[$i]["id"]."',
										'".$dadad."',
										'".$dadad."',
										'".$_SERVER["REMOTE_ADDR"]."');"
										;
					
					$sql = $sql."INSERT INTO `mssr_user_group_log`
									   (`create_by`,
										`group_sid`,
										`user_id`,
										`keyin_cdate`,
										`keyin_ip`)
									VALUES
									   ('".$retrun2[$i]["id"]."',
										'".$group_sid_list[$i%((int)count($group_sid_list))]."',
										'".$retrun2[$i]["id"]."',
										'".$dadad."',
										'".$_SERVER["REMOTE_ADDR"]."')"
										;
					db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);				
				}
			}
			
		}
		sleep(1);
		//建立後再次搜索組別
	 	$sql = "SELECT group_sid
			FROM  `mssr_user_group` 
			WHERE user_id = ".$id."
			AND  keyin_cdate BETWEEN '".$start."' AND '".$end."'";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		if(!is_null(@$retrun[0]["group_sid"]))$array["group"]=@$retrun[0]["group_sid"];
		else $array["echo"] = "喔喔，您沒有在班級內喔，請老師將您的帳號加入班級內吧!";
			
	}

	
	//搜索名稱序列
	//搜尋學校名稱
	if(!is_null($array["school"]))
	{
		$sql = "SELECT school_name 
				FROM  `school` 
				WHERE school_code = '".$array["school"]."'";
		$retrun =  db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		$array["school_name"] = $retrun[0]["school_name"];
	}
	//搜尋班級名稱
	if(!is_null($array["class_code"]))
	{
		$sql = "SELECT class_name.class_name,class.class_category
				FROM  `class` 
				LEFT JOIN `class_name` 
				ON class.classroom = class_name.classroom
				AND class.class_category = class_name.class_category
				WHERE class_code = '".$array["class_code"]."'";
		$retrun =  db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		$array["class_name"] = $retrun[0]["class_name"];
		$array["class_category"] = $retrun[0]["class_category"];
	}	
	//搜尋組別名稱
	if(!is_null($array["group"]))
	{
		$sql = "SELECT group_name 
				FROM  `mssr_group` 
				WHERE group_sid = '".$array["group"]."'
				AND  group_sdate BETWEEN '".$start."' AND '".$end."'";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		$array["group_name"]=$retrun[0]["group_name"];
	}
}

//-----------------------------------------
//特殊搜尋 - 進入到特別的太空領域
//-----------------------------------------
$sql = "SELECT count(1) AS count 
		FROM  `mssr_user_credit_class` 
		WHERE user_id = ".$_SESSION['uid'].";";
$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
$array["special_sky"] = $retrun[0]['count'];

echo json_encode($array,1);


/*找組別
有{砍組別} 無:不需要動作
新增 組別
分配組別
	*/	
?>