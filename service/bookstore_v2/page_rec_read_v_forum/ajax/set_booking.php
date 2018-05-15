<?
//-------------------------------------------------------
//版本編號 1.0
//訂閱書籍
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
	require_once(str_repeat("../",4)."/config/config.php");
	require_once(str_repeat("../",4)."/inc/get_book_info/code.php");

	 //外掛函式檔
	$funcs=array(
				APP_ROOT.'inc/code',
				APP_ROOT.'lib/php/db/code',
				);
	func_load($funcs,true);


	//清除並停用BUFFER
	@ob_end_clean();

	//建立連線 user
	$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
	$conn_user=conn($db_type='mysql',$arry_conn_user);




	//-----------------------------------------------
	//通用
	//-----------------------------------------------

	//-------------------------------------------
	//初始化, curl設定
	//-------------------------------------------
		$array =array();
		$array["error"] = "";
		$array["echo"] = "";


	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------
		$dadad = date("Y-m-d  H:i:s");
        //POST
       	$my_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$booking_to   =(isset($_POST['home_id']))?(int)$_POST['home_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
		$book_sid = mysql_prep(trim($_POST['book_sid']));
 		//trim();//去空白

		if($user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------

		//寫入訂閱書籍資訊
		$sql = "INSERT INTO  `mssr`.`mssr_book_booking`
				(
					`booking_from` ,
					`booking_to` ,
					`book_sid` ,
					`keyin_cdate` ,
					`keyin_ip`
				)
				VALUES
				(
					'".$my_id."',
					'".$booking_to."',
					'".$book_sid."',
					'".$dadad."',
					'".$_SERVER["REMOTE_ADDR"]."'
				);";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

		//寫入訊息
		$get_book_info = get_book_info($conn='',$book_sid,$array_select = array('book_name'),$arry_conn_mssr);
		if(mb_strlen($get_book_info[0]['book_name'])>10)$get_book_info[0]['book_name'] = mb_substr($get_book_info[0]['book_name'],0,10)."...";

		$sql = "SELECT name
				FROM  `member`
				WHERE uid = '".$_SESSION['uid']."'";
		$name = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);

		$sql = "INSERT INTO `mssr_msg_log`
				(
					`user_id`,
					`from_id`,
					`log_text`,
					`log_state`,
					`keyin_cdate`,
					`keyin_mdate`
				) VALUES (
					$booking_to,
					'".$my_id."',
					'".$name[0]['name']."  訂閱了你的  「".$get_book_info[0]['book_name']."」',
					'1',
					'".$dadad."',
					'".$dadad."'
				)";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		$array["booking_state"] = 0;
		echo json_encode($array,1);
		?>