<?php

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
//權限,與判斷
//---------------------------------------------------

$sess_permission = addslashes(trim($_SESSION['permission']));

$administrator = false;

$sql = "
	SELECT `status`
	FROM `permissions`
	WHERE 1=1
		AND `permission`='{$sess_permission}'
";

$db_results = db_result($conn_type='pdo', $conn_user, $sql, $arry_limit=array(), $arry_conn_user);

if (!empty($db_results)) {
	foreach ($db_results as $value) {
		if (trim($value['status']) === 'i_a') {
			$administrator = true;
		}
	}
}

//---------------------------------------------------
//END   設定與引用
//---------------------------------------------------

$dadad = date("Y-m-d  H:i:s");

$my_school='';
if(isset($_POST["my_school"]))$my_school=trim($_POST["my_school"]);

$my_semester_code='';
if(isset($_POST["my_semester_code"]))$my_semester_code=trim($_POST["my_semester_code"]);

$class_code='';
if(isset($_POST["class_code"]))$class_code=trim($_POST["class_code"]);


//依資料搜尋班  年   學校
$array = array(
		"school" => $_POST["school"],
		"grade" => $_POST["grade"],
		"class" => $_POST["class"],
		"identity" => $_POST["identity"],
		"group" => $_POST["group"],
		"category" => $_POST["category"],
		"semester_code" => $_POST["semester_code"],
		"class_code" => $class_code,
		"over_school_view" => $_POST["over_school_view"],
		"my_over_school_view" => $_POST["my_over_school_view"],
		"my_school" => $my_school,
		"my_semester_code" => $my_semester_code,
		"region_name" => $_POST["region_name"],
		"country_code" => $_POST["country_code"]
		);
//======================輸入: ?      輸出: 區域
if($array["region_name"] =="")
{
	$auth_list = array();

		if($array["my_over_school_view"]!="yes")
		{
			$sql = "SELECT school.country_code,
						   school.region_name
					FROM school
					WHERE school.school_code = '".$array["my_school"]."'

					";
			$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);

		}else
		{
			$sql = "SELECT school.country_code,
						   school.region_name
					FROM school
					GROUP BY  school.country_code,school.region_name
					";
			$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
		}
		$array_data = $retrun;
		$array_data["error"]="";
		$array_data["echo"]="";
		$array_data["count"]=count($retrun);



		echo json_encode(@$array_data,1);
}


//======================輸入: X      輸出: 學校
else if($array["school"] =="")
{
	$array_data = array();

		$auth_list = array();

		$sql = "SELECT `semester_code`,
						school.school_name,
						school.`school_code`
				FROM school

				LEFT JOIN semester
				ON semester.`school_code` = school.school_code
				AND '".$dadad."' BETWEEN semester.`start` AND semester.`end`
				WHERE `start` IS NOT NULL
				AND school.`school_code` != 'exp'
				AND region_name = '".$array["region_name"]."'
				AND country_code = '".$array["country_code"]."'";
		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
		$i=0;
		foreach($retrun as $key => $val)
		{
			if($val["semester_code"]!= $array["my_semester_code"])
			{
				$sql = "SELECT auth
						FROM `mssr_auth_class`
						WHERE `class_code` LIKE '".$val["semester_code"]."%'";
				$retru_t = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
				foreach($retru_t as $keya => $vala)
				{

					$auth = unserialize($vala["auth"]);
                    if(isset($auth["over_school_view"])){
                        if(!$auth["over_school_view"] || $auth["over_school_view"] =="yes")
                        {
                            $auth_list[$val["school_code"]]=$key;
                            break;
                        }
                    }
				}
				if(count($retru_t)==0)
				{
					$auth_list[$val["school_code"]]=$key;
				}
			}else
			{
				$auth_list[$val["school_code"]]=$key;
			}
			//管理員可以查看所有學校(包含關閉跨校瀏覽的學校)
			if ($administrator) {
				$auth_list[$val["school_code"]]=$key;
			}
		}
		foreach($auth_list as $key => $val)
		{
			array_push($array_data,$retrun[$val]);
		}
		$array_data["count"]=count($array_data);
		$array_data["error"]="";
		$array_data["echo"]=$i;

	//}
	echo json_encode(@$array_data,1);
}
//======================輸入:  學校      輸出: 年級
else if($array["grade"] =="")
{
	if( $array["my_school"] ==  $array["school"] )
	{//全數開放
		$array_data = array();
		$sql = "SELECT grade
				FROM  `class`
				WHERE  `semester_code` =  '".$array["semester_code"]."'
				GROUP BY  `grade` ";
		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
		$array_data = $retrun;
		$array_data["error"]="";
		$array_data["echo"]="";
		$array_data["count"]=count($retrun);
	}else
	{
		$array_data = array();
		$auth_list = array();
		$sql = "SELECT `grade`,`auth`
				FROM  `user`.`class`
				LEFT JOIN `mssr`.`mssr_auth_class`
				ON `mssr`.`mssr_auth_class`.`class_code` =  `user`.`class`.`class_code`
				WHERE  `user`.`class`.`semester_code` =  '".$array["semester_code"]."'
				";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($retrun as $key => $val)
		{
			$auth = unserialize($val["auth"]);
			if(!$auth["over_school_view"] || $auth["over_school_view"] =="yes")
			{
				$auth_list[$val["grade"]]=$key;
				//break;
			}
		}
		foreach($auth_list as $key => $val)
		{
			array_push($array_data,$retrun[$val]);
		}

		$array_data["error"]="";
		$array_data["echo"]="";
		$array_data["count"]=count($auth_list);
	}
	echo json_encode(@$array_data,1);
}
//======================輸入:  學校,年級      輸出: 班級
else if($array["class"] =="")
{
	$array_data = array();
	if( $array["my_school"] ==  $array["school"] )
	{//全數開放
		$sql = "SELECT me.`classroom`,
					   me.class_code,
					   class_name.class_name
				FROM
				(
					SELECT  `classroom`,class_code,class_category
					FROM  `user`.`class`
					WHERE  `semester_code` =  '".$array["semester_code"]."'
					AND  `grade` ='".$array["grade"]."'
					GROUP BY  `classroom`
				)AS me
				LEFT JOIN `user`.class_name
				ON `user`.class_name.class_category = me.class_category
				AND `user`.class_name.classroom = me.classroom
				";

		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		$array_data = $retrun;
		$array_data["error"]="";
		$array_data["echo"]="";
		$array_data["count"]=count($retrun);
	}else
	{//部分開放
		$sql = "SELECT me.`classroom`,
					   me.class_code,
					   class_name.class_name,
					   `auth`
				FROM
				(
					SELECT  `classroom`,class_code,class_category
					FROM  `user`.`class`
					WHERE  `semester_code` =  '".$array["semester_code"]."'
					AND  `grade` ='".$array["grade"]."'
					GROUP BY  `classroom`
				)AS me
				LEFT JOIN `user`.class_name
				ON `user`.class_name.class_category = me.class_category
				AND `user`.class_name.classroom = me.classroom
				LEFT JOIN `mssr`.`mssr_auth_class`
				ON `mssr`.`mssr_auth_class`.`class_code` =  me.`class_code`
				";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($retrun as $key => $val)
		{
			$auth = unserialize($val["auth"]);
			if(!$auth["over_school_view"] || $auth["over_school_view"] =="yes")
			{
				$auth_list[$val["class_code"]]=$key;
				//break;
			}
		}
		foreach($auth_list as $key => $val)
		{
			array_push($array_data,$retrun[$val]);
		}

		$array_data["error"]="";
		$array_data["echo"]="";
		$array_data["count"]=count($retrun);
	}
	echo json_encode(@$array_data,1);
}
//======================輸入:  學校,年級,班級      輸出: 組別
else if($array["group"] =="")
{
	$array_data = array();
	//先搜尋本學期的時間
	$sql = "SELECT `start`,`end`
			FROM `user`.semester
			WHERE semester_code  = '".$array["semester_code"]."'";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	$start = $retrun[0]['start'];
	$end = $retrun[0]['end'];
	//搜尋該班人員
	$sql = "SELECT group_sid,group_name
			FROM  `mssr_group`
			WHERE  `grade_id` ='".$array["grade"]."'
			AND  `school_code` ='".$array["school"]."'
			AND  `classroom_id` ='".$array["class"]."'
			AND  group_sdate BETWEEN '".$start."' AND '".$end."'
			 ";
	
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

	//讀取組員中的經驗值綜合值

	foreach($retrun as $key => $val)
	{
		$retrun[$key]["group_exp"] = 0 ;
		$sql = "SELECT `user_id`
				FROM `mssr_user_group`
				WHERE `group_sid` = '".$val['group_sid']."'";
		$mssr_user_group = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($mssr_user_group as $key_g => $val_g)
		{
			$sql = "
					SELECT `score_exp`
					FROM `mssr_user_info`
					WHERE `user_id` = '".$val_g['user_id']."'";
			$mssr_score_exp = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
			@$retrun[$key]["group_exp"] += $mssr_score_exp[0]["score_exp"];

		}

	}
	if(count($retrun)==0)  //停止分組
	{//有學生才進行分組↓
		/*$tmp = $array["semester_code"]."_".$array["grade"]."_".$array["class"];
		//搜尋該班人員
		$sql = "SELECT  class,id
				FROM (
					SELECT class_code AS class,uid AS id
					FROM  `user`.`student`
					WHERE class_code = '".$tmp."' and start <= '".$dadad."' and end >= '".$dadad."'

					UNION  ALL

					SELECT class_code AS class,uid AS id
					FROM  `user`.`teacher`
					WHERE class_code = '".$tmp."' and start <= '".$dadad."' and end >= '".$dadad."'
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
			$sql = "SELECT group_sid,group_name
			FROM  `mssr_group`
			WHERE  `grade_id` ='".$array["grade"]."'
			AND  `school_code` ='".$array["school"]."'
			AND  `classroom_id` ='".$array["class"]."'
			AND  group_sdate BETWEEN '".$start."' AND '".$end."'
			 ";
			$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		}
		*/
	}
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
	//先搜尋本學期的時間
	$sql = "SELECT `start`,`end`
			FROM semester
			WHERE semester_code  = '".$array["semester_code"]."'";
	$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
	$start = $retrun[0]['start'];
	$end = $retrun[0]['end'];

	$sql = "SELECT user_id
			FROM  `mssr_user_group`
			WHERE  `group_sid` =  '".$array["group"]."'
			AND  keyin_cdate BETWEEN '".$start."' AND '".$end."' ORDER BY user_id;
			";
	
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

		//if($array["school"]!="gcp")
		//{
		//	$tmp["parent_id"] = "";
		//}

		//搜索該生姓名((改填UID))
		$sql = "SELECT name
				FROM  `member`
				WHERE  uid='".$val1["user_id"]."'";
		$retrun_3 = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);

		$sql = "

				SELECT uid
				FROM  `student`
				WHERE  uid ='".$val1["user_id"]."'
				AND `class_code` =  '".$array["class_code"]."'
				AND '".$dadad."' BETWEEN student.`start` AND student.`end`

				UNION  ALL

				SELECT uid
				FROM  `teacher`
				WHERE  uid ='".$val1["user_id"]."'
				AND `class_code` =  '".$array["class_code"]."'
				AND '".$dadad."' BETWEEN teacher.`start` AND teacher.`end`
					";
		$retrun_f = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		if(count($retrun_f)>0)
		{
			$tmp["user_id"] = $val1["user_id"];
			$tmp["user_nickname"] = $retrun_3[0]["name"];
			$tmp["star_declaration"] = $retrun_2[0]["star_declaration"];
			$tmp["star_style"] = $retrun_2[0]["star_style"];
			array_push($user_info,$tmp);
			$user_info["count"]++;
		}
	}
	$array_data = $user_info;
	
	echo json_encode($array_data,1);

}


?>