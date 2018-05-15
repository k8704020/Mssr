<?
//-------------------------------------------------------
//版本編號 1.0
//儲存店員招呼語
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

	require_once(str_repeat("../",5)."/config/config.php");

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



	//-------------------------------------------
	//初始化, curl設定
	//-------------------------------------------
		$array =array();
		$array["error"] = "";
		$array["echo"] = "";
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------

        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
 		//trim();//去空白

		if($user_permission != $_SESSION["permission"] || $user_id  != $_SESSION["uid"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

		//檢查資料正確性
		$t1 =  mysql_prep(base64_encode(gzcompress($_POST["t1"])));
		$t2 =  mysql_prep(base64_encode(gzcompress($_POST["t2"])));
		$t3 =  mysql_prep(base64_encode(gzcompress($_POST["t3"])));
		$t4 =  mysql_prep(base64_encode(gzcompress($_POST["t4"])));
		$t5 =  mysql_prep(base64_encode(gzcompress($_POST["t5"])));
		//序列化-文字陣列
		$array_text = array($t1,$t2,$t3,$t4,$t5);
		$text = serialize($array_text);
	//-------------------------------------------
	//SQL
	//-------------------------------------------
		$sql = "
					UPDATE `mssr_user_info` SET clerk_talk =  '$text'
					WHERE  user_id = {$user_id}
				";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);



		echo json_encode($array,1);
		?>