<?php
//---------------------------------------------------
// 外太空 >
//
//---------------------------------------------------

//---------------------------------------------------
//輸入 user_id,book_sid
//輸出 OK
/*if(!$array["semester_code"])
	{
		$array["echo"] = "查無學期設定或學期設定不正確<BR>請設定正確的學期才可以使用";
		die(json_encode($array,1));
	}
	*/
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

//
$id = (int)mysql_prep($_POST["user_id"]);
$SESSION_id = (int)mysql_prep($_POST["SESSION_id"]);

$dadad2 = date("Y-m-d");
$dadad = date("Y-m-d  H:i:s");
// ID   搜尋年班級資訊
$sql = "SELECT permission,uid
		FROM  `member`
		WHERE ".$id." = uid";
$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);

$array = array(
		"my_school" => "",
		"my_semester_code" => "",
		"school" => "",
		"grade" => "",
		"class" => "",
		"identity" => "",
		"midentity" => "",
		"group" => "",
		"semester_code" => "",
		"class_code" => "",
		"class_category" => "",
		"category" => "",
		"region_name" => "",
		"country_code" => "",
		"group_name" => "",
		"class_name" => "",
		"school_name" => "",
		"grade_name" => "",
		"special_sky" => 0,
		"error" => "",
		"echo" => "",
		"sv_type" => "",
		"credit_school" => "",
		"credit_grade" => "",
		"credit_class" => "",
		"credit_group" => "",
		"credit_school_name" => "",
		"credit_grade_name" => "",
		"credit_class_name" => "",
		"credit_group_name" => "",
		"same_school" => 0,
		"over_school_view" => "yes",
		"my_over_school_view" => "yes",
		"responsibilities"=>0,
		"tag" => ""
		);

//特殊 搜尋學分班資訊


$sql = "SELECT main.group_id,group_name,mssr_credit_class_group_rev.class_id,class_name,mssr_credit_grade_class_rev.grade_id,grade_name
		FROM
		(
			SELECT `group_id`
			FROM  `mssr_user_credit_group_rev`
			WHERE  `user_id` = '$id'
		)AS main
		LEFT JOIN mssr_credit_group ON main.group_id = mssr_credit_group.group_id
		LEFT JOIN mssr_credit_class_group_rev ON main.group_id = mssr_credit_class_group_rev.group_id
		LEFT JOIN mssr_credit_class ON mssr_credit_class_group_rev.class_id = mssr_credit_class.class_id
		LEFT JOIN mssr_credit_grade_class_rev ON mssr_credit_grade_class_rev.class_id = mssr_credit_class.class_id
		LEFT JOIN mssr_credit_grade ON mssr_credit_grade.grade_id = mssr_credit_grade_class_rev.grade_id";
$retrunB = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
if(count($retrunB)!=0){
	$array["credit_school"] = "1";
	$array["credit_grade"] = $retrunB[0]["grade_id"];
	$array["credit_class"] = $retrunB[0]["class_id"];
	$array["credit_group"] = $retrunB[0]["group_id"];

	$array["credit_school_name"] = "中央大學學分班";
	$array["credit_grade_name"] = $retrunB[0]["grade_name"];
	$array["credit_class_name"] = $retrunB[0]["class_name"];
	$array["credit_group_name"] = $retrunB[0]["group_name"];
}



//判斷權限
$sql = "SELECT status_info.status
		FROM `permissions`
		RIGHT JOIN status_info
		on status_info.status = permissions.status
		WHERE  `permission` =  '".$retrun[0]["permission"]."'
		AND category_id = 4";

$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
$identity= "";
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
$array["midentity"] = $identity;
if(0)
{
/*	$array["identity"] = "super";
	$array["echo"] = "管理者暫無提供星球社團服務";*/
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
		$SESSION_id = $id;
		$identity = "s";
	}
	if($identity == "t")
	{
		$sql = "
				SELECT class.grade,
					   class.grade AS grade_name,
					   class.class_category AS category,
				       class.classroom AS class,
					   semester.semester_code,
					   semester.school_code AS school,
					   me.class_code,
					   class_name.class_name,
					   semester.semester_year AS year,
					   school.country_code,
					   school.region_name
				FROM
				(
					SELECT class_code
					FROM  `teacher`
					WHERE uid = '$id'
					AND '".$dadad."' BETWEEN start AND end
				)AS me
				LEFT JOIN class
				ON class.class_code = me.class_code

				LEFT JOIN class_name
				ON class_name.class_category = class.class_category
				AND class_name.classroom = class.classroom

				LEFT JOIN semester
				ON semester.semester_code = class.semester_code

				LEFT JOIN school
				ON school.school_code = semester.school_code";
		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		if(count($retrun)>0){

			$array["class_code"] = $retrun[0]["class_code"];//
			$array["school"] = $retrun[0]["school"];///
			$array["category"] = $retrun[0]["category"];
			$array["grade"] = $retrun[0]["grade"];
			$array["grade_name"] = $retrun[0]["grade_name"];
			$array["class"] = $retrun[0]["class"];
			$array["class_name"] = $retrun[0]["class_name"];
			$array["country_code"] = $retrun[0]["country_code"];
			$array["region_name"] = $retrun[0]["region_name"];
			$array["identity"] = "student";//
			$array["semester_code"] = $retrun[0]["semester_code"];
		}

	}
	else if($identity == "super")
	{
		$sql = "
				SELECT class.grade,
					   class.grade AS grade_name,
					   class.class_category AS category,
				       class.classroom AS class,
					   semester.semester_code,
					   semester.school_code AS school,
					   me.class_code,
					   class_name.class_name,
					   semester.semester_year AS year,
					   school.country_code,
					   school.region_name
				FROM
				(
					SELECT class_code
					FROM  `student`
					WHERE uid = '$id'
					AND '".$dadad."' BETWEEN start AND end
				)AS me
				LEFT JOIN class
				ON class.class_code = me.class_code

				LEFT JOIN class_name
				ON class_name.class_category = class.class_category
				AND class_name.classroom = class.classroom

				LEFT JOIN semester
				ON semester.semester_code = class.semester_code
				LEFT JOIN school
				ON school.school_code = semester.school_code";
		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		if(count($retrun)>0){

			$array["class_code"] = $retrun[0]["class_code"];//
			$array["school"] = $retrun[0]["school"];///
			$array["category"] = $retrun[0]["category"];
			$array["grade"] = $retrun[0]["grade"];
			$array["grade_name"] = $retrun[0]["grade_name"];
			$array["class"] = $retrun[0]["class"];
			$array["class_name"] = $retrun[0]["class_name"];
			$array["country_code"] = $retrun[0]["country_code"];
			$array["region_name"] = $retrun[0]["region_name"];
			$array["identity"] = "student";//
			$array["semester_code"] = $retrun[0]["semester_code"];
		}

	}
	else if($identity == "s")
	{
		$sql = "
				SELECT class.grade,
					   class.grade AS grade_name,
					   class.class_category AS category,
				       class.classroom AS class,
					   semester.semester_code,
					   semester.school_code AS school,
					   me.class_code,
					   class_name.class_name,
					   semester.semester_year AS year,
					   school.country_code,
					   school.region_name


				FROM
				(
					SELECT class_code
					FROM  `student`
					WHERE uid = '$id'
					AND '".$dadad."' BETWEEN start AND end
				)AS me
				LEFT JOIN class
				ON class.class_code = me.class_code

				LEFT JOIN class_name
				ON class_name.class_category = class.class_category
				AND class_name.classroom = class.classroom


				LEFT JOIN semester
				ON semester.semester_code = class.semester_code
				LEFT JOIN school
				ON school.school_code = semester.school_code";
		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);

		if(count($retrun)>0)
		{


			$array["class_code"] = $retrun[0]["class_code"];//
			$array["school"] = $retrun[0]["school"];///
			$array["category"] = $retrun[0]["category"];
			$array["grade"] = $retrun[0]["grade"];
			$array["grade_name"] = $retrun[0]["grade_name"];
			$array["class"] = $retrun[0]["class"];
			$array["class_name"] = $retrun[0]["class_name"];
			$array["country_code"] = $retrun[0]["country_code"];
			$array["region_name"] = $retrun[0]["region_name"];
			$array["identity"] = "student";//
			$array["semester_code"] = $retrun[0]["semester_code"];
		}
	}

	//搜尋這傢伙是不是老師群
	$sql =" SELECT `responsibilities`
			FROM `user`.`personnel`
			WHERE `personnel`.`uid` = '".$id."'
			AND '".$dadad."' BETWEEN `start` AND `end`";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	if(count($retrun))
	{
		$array["responsibilities"] = $retrun[0]["responsibilities"];

	}


	//搜尋自己學校是否開放觀看
	$sql = "
			SELECT *
			FROM
			(
				SELECT `user`.`teacher`.`class_code`
				FROM `user`.`teacher`
				WHERE `uid` = $SESSION_id
				AND '".$dadad2."' BETWEEN `start` AND `end`
			)AS aa
			LEFT JOIN `mssr`.`mssr_auth_class`
			ON aa.`class_code` = `mssr`.`mssr_auth_class`.`class_code`";
	$s_auth = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	$sql = "
			SELECT *
			FROM
			(
				SELECT `user`.`student`.`class_code`
				FROM `user`.`student`
				WHERE `uid` = $SESSION_id
				AND '".$dadad2."' BETWEEN `start` AND `end`
			)AS aa
			LEFT JOIN `mssr`.`mssr_auth_class`
			ON aa.`class_code` = `mssr`.`mssr_auth_class`.`class_code`";
	$t_auth = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	if(count($t_auth)>=1)
	{
		$auth=unserialize($t_auth[0]['auth']);
		if($auth["over_school_view"])$array["my_over_school_view"] = $auth["over_school_view"];
	}

	if(count($s_auth)>=1)
	{
		$auth=unserialize($s_auth[0]['auth']);
		if(isset($auth["over_school_view"]))$array["my_over_school_view"] = $auth["over_school_view"];
	}
	//搜尋對方學校是否開放觀看
	if($array["class_code"] !="")
	{
		$sql = "SELECT auth
				FROM  `mssr_auth_class`
				WHERE class_code = '".$array["class_code"]."'";
		$re_auth = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		if(count($re_auth)>=1)
		{
			$auth=unserialize($re_auth[0]['auth']);
			if(isset($auth["over_school_view"]))$array["over_school_view"] = $auth["over_school_view"];
		}

	}

	if($array["semester_code"])
	{
		//擷取學期範圍
		$sql = "SELECT `start`,`end`
				FROM semester
				WHERE semester_code  = '".$array["semester_code"]."';";
		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		$start = $retrun[0]['start'];
		$end = $retrun[0]['end'];



		//搜尋組別依日期
		$sql = "SELECT mssr_user_group.group_sid,keyin_cdate
				FROM mssr_user_group
				LEFT JOIN `mssr_group`
				ON `mssr_group`.`group_sid` = mssr_user_group.`group_sid`
				WHERE user_id = '".$id."'
				AND grade_id= '".$array["grade"]."'
				AND classroom_id = '".$array["class"]."'
				AND keyin_cdate BETWEEN   '".$start." 00:00:00' AND '".$end." 00:00:00';";
		$retrun_user_group_has = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);





		if(sizeof($retrun_user_group_has) == 0)
		{//找不到組
			$array["tag"] = "NO_group";
			//判斷目前班級是否已有組存在
			$sql = "SELECT group_id,group_sid
					FROM  `mssr_group`
					WHERE school_code = '".$array["school"]."'
					AND grade_id = '".$array["grade"]."'
					AND classroom_id = '".$array["class"]."'
					AND  group_sdate BETWEEN '".$start."' AND '".$end."';";
			$retrun_class_has_group = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

			if(count($retrun_class_has_group)>0)
			{//已有組別則隨機插入

				if(1)
				{
					//搜尋此學生的組別(被分到別的般或過期)
					$sql = "SELECT group_sid
							FROM  `mssr_user_group`
							WHERE user_id = '".$id."'";
					$retrun_user_group =  db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);

					if(sizeof($retrun_user_group) !=0)
					{
						//刪除該學生目前組別標記
						$sql = "DELETE FROM `mssr_user_group`
								WHERE user_id = '".$id."';";
						db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

						//搜尋組別內還有沒有學生
						$sql = "SELECT count(1) as count
								FROM mssr_user_group
								WHERE group_sid = '".$retrun_user_group[0]['group_sid']."'
								AND  keyin_cdate > '".$start."';";
						$retrun_has_count = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
						if($retrun_has_count[0]["count"]==0)
						{

						//移除組別
						$sql = "DELETE FROM `mssr_group`
								WHERE group_sid = '".$retrun_user_group[0]['group_sid']."';"
								.
								"UPDATE `mssr_group_log` SET group_mdate = '".$dadad."'
								WHERE group_sid = '".$retrun_user_group[0]['group_sid']."';";
						db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
						}
					}


					if(count($retrun_class_has_group)>0)
					{//班級有組別 分配人員至班上隨機一個組別
						$array["tag"] = "class_has_group AND random_in";
						$rand_number = rand(0,(count($retrun_class_has_group)-1));

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
											'".$retrun_class_has_group[$rand_number]["group_sid"]."',
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
											'".$retrun_class_has_group[$rand_number]["group_sid"]."',
											'".$id."',
											'".$dadad."',
											'".$_SERVER["REMOTE_ADDR"]."')"
											;
						db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
						$array["group"]=@$retrun_class_has_group[$rand_number ]["group_sid"];
					}
				}
			}else if($array["class_code"] != "" )//向上搜尋目前班級
			{//該班級沒有組別   =============================全部重新抓入!!!!!!!!!!!!!================================================
				$array["tag"] = "class_no_group AND random_all";
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

				$retrun_class_uid =  db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
				$class_check = "";
				$aute_set_group = 1;//分組類型  預設全部同班
				$class_hassss =  0 ;
				$tmp_count = count($retrun_class_uid);
				//判定上學期是否同班

				foreach($retrun_class_uid as $key1=>$val1)
				{
					//搜尋此學生的組別
					$sql = "SELECT group_sid
							FROM  `mssr_user_group`
							WHERE user_id = '".$val1['id']."'";
					$retrun_user_group =  db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);

					if(sizeof($retrun_user_group) !=0)
					{
						//刪除該學生目前組別標記
						$sql = "DELETE FROM `mssr_user_group`
								WHERE user_id = '".$val1['id']."';";
						db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

						//搜尋組別內還有沒有學生
						$sql = "SELECT count(1) as count
								FROM mssr_user_group
								WHERE group_sid = '".$retrun_user_group[0]['group_sid']."'
								AND  keyin_cdate > '".$start."';";
						$retrun_has_count = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
						if($retrun_has_count[0]["count"]==0)
						{

						//移除組別
						$sql = "DELETE FROM `mssr_group`
								WHERE group_sid = '".$retrun_user_group[0]['group_sid']."';"
								.
								"UPDATE `mssr_group_log` SET group_mdate = '".$dadad."'
								WHERE group_sid = '".$retrun_user_group[0]['group_sid']."';";
						db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
						}
					}

					$sql = "SELECT class_code
							FROM  `student`
							WHERE uid = ".$val1['id']."
							ORDER BY  `start` DESC ";

					$retrun_class =  db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,2),$arry_conn_user);

					if($class_check == "" && $retrun_class[1]["class_code"]!=NULL) $class_check = $retrun_class_uid[1]["class_code"];
					if($class_check != "" &&$retrun_class[1]["class_code"]!=NULL  && $class_check !=  $retrun_class_uid[1]["class_code"])
					{
						//不同班 給予標記
						$aute_set_group = 2;

					}
					if($retrun_class[1]["class_code"]!=NULL && sizeof($retrun_user_group) > 0)
					{$class_hassss = 1;}
				}
				if($class_hassss == 0)$aute_set_group = 2;//全部人都是新的狀況

				if($aute_set_group == 2)
				{//=========================================不同班//隨機分組=========================================
				//=========================================依班級人數建立組別=========================================

					$sql = "";
					$group_sid_list=array();

					for($i = 1 ; $i*$i<= $tmp_count;$i++)
					{
						$array["tag"] = "all_no_group AND random_all";
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


					$nu = count($retrun_class_uid)/count($group_sid_list);
					for($i = 0;$i < count($retrun_class_uid); $i++)
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
											'".$retrun_class_uid[$i]["id"]."',
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
										   ('1',
											'".$group_sid_list[$i%((int)count($group_sid_list))]."',
											'".$retrun_class_uid[$i]["id"]."',
											'".$dadad."',
											'".$_SERVER["REMOTE_ADDR"]."');"
											;
						db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

					}
				}else if($aute_set_group == 1)
				{//=========================================同班//依上次組別分組=========================================
					$array["tag"] = "front_has_group AND like_front";
					//搜尋
					$student_list = array();
					$group_list=  array();
					foreach($retrun_class_uid as $key1=>$val1)
					{
						$sql = "SELECT group_name,
									  `mssr_group_log`.`group_sid`
								FROM (
										SELECT group_sid
										FROM  `mssr_user_group_log`
										WHERE user_id = '".$val1['id']."'
										ORDER BY `keyin_cdate` DESC
										LIMIT 0 , 1
								) AS group_s
								LEFT JOIN mssr_group_log
								ON `mssr_group_log`.`group_sid` =   `group_s`.`group_sid`";
						$retrun_mssr_user_group = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);

						if(sizeof($retrun_mssr_user_group) != 0)
						{
							$student_list[$val1['id']]["group_sid"] = $retrun_mssr_user_group[0]["group_sid"];
							$group_list[$retrun_mssr_user_group[0]["group_sid"]]["group_name"] = $retrun_mssr_user_group[0]["group_name"];
						}
					}
					$sql = "";
					$i = 0;
					foreach($group_list as $key1=>$val1)
					{
						$i++ ;

							$group_sid=group_sid($i,mb_internal_encoding());
							$group_list[$key1]["new_sid"] =  $group_sid;
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
										'".$group_list[$key1]["new_sid"]."',
										'".$val1["group_name"]."',
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
										'".$group_list[$key1]["new_sid"]."',
										'".$val1["group_name"]."',
										'".$dadad."',
										'".$_SERVER["REMOTE_ADDR"]."'
									);";
					}
					db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

					$sql = "";
					foreach($student_list as $key1=>$val1)
					{
						$sql = $sql."INSERT INTO `mssr_user_group`
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
											'".$group_list[$val1["group_sid"]]["new_sid"] ."',
											'".$key1."',
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
										   ('1',
											'".$group_list[$val1["group_sid"]]["new_sid"] ."',
											'".$key1."',
											'".$dadad."',
											'".$_SERVER["REMOTE_ADDR"]."');"
											;
					}

					db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

				}

			}
			else
			{//無班級

				//顯示目前無班級  請洽教師

				$array["sv_type"] = "no_class";
			}

		}

		//建立後再次搜索組別
		 $sql = "SELECT group_sid
			FROM  `mssr_user_group`
			WHERE user_id = ".$id."
			AND  keyin_cdate BETWEEN '".$start."' AND '".$end."'";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		if(!is_null(@$retrun[0]["group_sid"]))$array["group"]=@$retrun[0]["group_sid"];
		else $array["sv_type"] = "no_class";
	}

	//再次搜索學校(因應組任等等沒有班級
	if($array["school"]=="")
	{
		$sql = "SELECT semester.`school_code`,
					   `semester_code`,
					   school.country_code,
					   school.region_name
				FROM(
					SELECT `school_code`,start
					FROM  `member_school`
					WHERE  `uid` = ".$id."
					ORDER BY  `member_school`.`start` DESC
				) AS SSS
				LEFT JOIN semester
				ON SSS.`school_code` = semester.`school_code`
				LEFT JOIN school
				ON school.`school_code` = SSS.`school_code`
				WHERE '".$dadad."' BETWEEN semester.`start` AND semester.`end`
				ORDER BY SSS.start DESC
				";
		$retrun =  db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		if(count($retrun)>0)
		{
			$array["school"] = $retrun[0]["school_code"];
			$array["semester_code"] = $retrun[0]["semester_code"];
			$array["country_code"] = $retrun[0]["country_code"];
			$array["region_name"] = $retrun[0]["region_name"];
		}
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
	if(!is_null($array["group"]) && $start && $end)
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
//特殊與使用者同校判斷
//-----------------------------------------
//搜尋使用者學校
	$sql = "SELECT semester.`school_code`,
					   `semester_code`
				FROM(
					SELECT `school_code`,start
					FROM  `member_school`
					WHERE  `uid` = ".$SESSION_id."
					ORDER BY  `member_school`.`start` DESC
				) AS SSS
				LEFT JOIN semester
				ON SSS.`school_code` = semester.`school_code`
				WHERE '".$dadad."' BETWEEN semester.`start` AND semester.`end`
				ORDER BY SSS.start DESC
				";
		$retrun =  db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		if(count($retrun)>0)
		{
			//記錄使用者著學校
			$array["my_school"] = $retrun[0]["school_code"];
			$array["my_semester_code"] = $retrun[0]["semester_code"];

			if($array["school"] != $retrun[0]["school_code"] && ($array["over_school_view"]!='yes' && $array["my_over_school_view"]!='yes'))
			{	$array["same_school"] = 0;
				$array["school"] = $retrun[0]["school_code"];
				$array["semester_code"] = $retrun[0]["semester_code"];

				$array["grade"] = "";
				$array["class"] = "";
				$array["class_code"] = "";
				$array["class_category"] = "";
				$array["category"] = "";
				$array["group_name"] = "";
				$array["class_name"] = "";
				$array["grade_name"] = "";
				//搜尋學校名稱
				if(!is_null($array["school"]))
				{
					$sql = "SELECT school_name
							FROM  `school`
							WHERE school_code = '".$array["school"]."'";
					$retrun =  db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
					$array["school_name"] = $retrun[0]["school_name"];
				}
			}
			else
			{
				$array["same_school"] = 1;
			}
		}else
		{
			$array["same_school"] = 0;
			$array["school"] ="";
			$array["semester_code"] = "";
			$array["school_name"] = "";
			$array["school"] = "";
			$array["grade"] = "";
			$array["class"] = "";
			$array["semester_code"] = "";
			$array["class_code"] = "";
			$array["class_category"] = "";
			$array["category"] = "";
			$array["group_name"] = "";
			$array["class_name"] = "";
			$array["school_name"] = "";
			$array["grade_name"] = "";
		}

//-----------------------------------------
//特殊搜尋 - 進入到特別的太空領域  - 現在全部都可以看  所以關閉功能囉
//-----------------------------------------
/*$sql = "SELECT count(1) AS count
		FROM  `mssr_user_credit_class`
		WHERE user_id = ".$_SESSION['uid'].";";
$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
$array["special_sky"] = $retrun[0]['count'];*/

echo json_encode($array,1);


/*找組別
有{砍組別} 無:不需要動作
新增 組別
分配組別


$s
	*/
?>