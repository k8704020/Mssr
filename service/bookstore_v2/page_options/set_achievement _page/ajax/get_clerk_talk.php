<?
//-------------------------------------------------------
//版本編號 1.0
//讀取店員招呼語
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




	//-----------------------------------------------
	//通用
	//-----------------------------------------------

	//-------------------------------------------
	//初始化, curl設定
	//-------------------------------------------
		$array =array();
		$array["error"] = "";
		$array["echo"] = "";
		$array["clerk_talk"] = array("","","","","");
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

	//-------------------------------------------
	//SQL
	//-------------------------------------------
		$sql = "
					SELECT clerk_talk
					FROM   `mssr_user_info`
					WHERE  user_id = {$user_id}
				";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);

		if($retrun[0]["clerk_talk"]!="")
		{
			$tmp = unserialize($retrun[0]["clerk_talk"]);
			$array['clerk_talk'][0] = gzuncompress(base64_decode($tmp[0]));
			$array['clerk_talk'][1] = gzuncompress(base64_decode($tmp[1]));
			$array['clerk_talk'][2] = gzuncompress(base64_decode($tmp[2]));
			$array['clerk_talk'][3] = gzuncompress(base64_decode($tmp[3]));
			$array['clerk_talk'][4] = gzuncompress(base64_decode($tmp[4]));
		}
		echo json_encode($array,1);
		?>