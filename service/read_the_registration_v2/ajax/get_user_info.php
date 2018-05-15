<?php
//-------------------------------------------------------
//版本編號 1.0
//讀取使用者資料
//ajax
//-------------------------------------------------------

	//---------------------------------------------------
	//輸入 user_id
	//輸出
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

	 //外掛函式檔
	$funcs=array(
				APP_ROOT.'inc/code',
				APP_ROOT.'lib/php/db/code',
				);
	func_load($funcs,true);


	//清除並停用BUFFER
	@ob_end_clean();

	//建立連線 user
	$conn_user=conn($db_type='mysql',$arry_conn_user);




	//-----------------------------------------------
	//通用
	//-----------------------------------------------

	//-------------------------------------------
	//初始化, curl設定
	//-------------------------------------------
		$array =array();
		$array["error"] = "";


		$array["auth_open_publish"] = 1;
		$array["auth_read_opinion_limit_day"] = 14;
		$array["auth_rec_en_input"] = "yes";
		$array["auth_rec_draw_open"] = "yes";
		$array["auth_coin_open"] = "yes";

	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------
		$dadad = date("Y-m-d  H:i:s");
        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
 		//trim();//去空白

		if($user_id != $_SESSION["uid"] || $user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------

		//搜尋使用者基本資料
		$sql = "SELECT `permission`,
					   `sex`,
					   `name`
				FROM  `member`
				WHERE uid = {$user_id}";

		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		$array['user_name'] = $retrun[0]["name"];
		$array['user_sex'] = $retrun[0]["sex"];
		$array['user_permission'] = $retrun[0]["permission"];



		//尋找班級資料
		$array['user_class_code'] = array();
		$class_has = false;

		//老師
		$sql = "SELECT class_code
				FROM  `teacher`
				WHERE uid = {$user_id}
				ORDER BY `start` DESC";
		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		foreach($retrun as $key1=>$val1)
		{
			//分割班級資訊

			$tmp_1 = array();
			$tmp_1 = explode("_", $val1["class_code"]);

			//搜尋學校代號
			$sql = "SELECT school_category
					FROM  `school`
					WHERE  `school_code` LIKE  '".$tmp_1[0]."'";
			$retrun2 = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);

			$tmp_2 = array(
			"class_code" => $val1["class_code"],
			"school" => $tmp_1[0],
			"year" => $tmp_1[1],
			"semester" => $tmp_1[2],
			"grade" => $tmp_1[3],
			"class" => $tmp_1[4],
			"school_category" => $retrun2[0]["school_category"],
			"user_personnel" =>"teacher"
			);
			$class_has = true;
			array_push($array['user_class_code'],$tmp_2);
		}
		//學生
		$sql = "SELECT class_code
				FROM  `student`
				WHERE uid = {$user_id}
				ORDER BY `start` DESC";
		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		foreach($retrun as $key1=>$val1)
		{
			//分割班級資訊

			$tmp_1 = array();
			$tmp_1 = explode("_", $val1["class_code"]);

			//搜尋學校代號
			$sql = "SELECT school_category
					FROM  `school`
					WHERE  `school_code` LIKE  '".$tmp_1[0]."'";
			$retrun2 = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);

			$tmp_2 = array(
			"class_code" => $val1["class_code"],
			"school" => $tmp_1[0],
			"year" => $tmp_1[1],
			"semester" => $tmp_1[2],
			"grade" => $tmp_1[3],
			"class" => $tmp_1[4],
			"school_category" => $retrun2[0]["school_category"],
			"user_personnel" =>"student");
			$class_has = true;
			array_push($array['user_class_code'],$tmp_2);

			//獲取帶班教師
			$sql = "SELECT uid
					FROM  `teacher`
					WHERE  `class_code` LIKE  '".$val1["class_code"]."'";
			$retrun3 = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
			if(sizeof($retrun3)>0)
			{//獲取教師設定的權限
				$sql ="SELECT auth
					   FROM  `mssr_auth_user`
					   WHERE user_id = ".$retrun3[0]["uid"].";";
				$retrun4 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
				if(sizeof($retrun4)>0)
				{
					$auth=unserialize($retrun4[0]['auth']);
					if($auth["open_publish"])$array["auth_open_publish"] = $auth["open_publish"];
					if($auth["read_opinion_limit_day"])$array["auth_read_opinion_limit_day"] = $auth["read_opinion_limit_day"];
					if($auth["rec_en_input"])$array["auth_rec_en_input"] = $auth["rec_en_input"];
					if($auth["rec_draw_open"])$array["auth_rec_draw_open"] = $auth["rec_draw_open"];
					if($auth["coin_open"])$array["auth_coin_open"] = $auth["coin_open"];
				}
			}
		}
		//未有班級
		if(!$class_has)
		{
			$tmp_2 = array(
			"class_code" => "",
			"school" => "",
			"year" => "",
			"semester" => "",
			"grade" => "",
			"class" => "",
			"school_category" => "",
			"user_personnel" =>"");
			array_push($array['user_class_code'],$tmp_2);
		}



		echo json_encode($array,1);
		?>