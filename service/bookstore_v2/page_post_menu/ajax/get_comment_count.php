<?
//-------------------------------------------------------
//版本編號 1.0
//讀取評論之數量
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
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------

        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
 		//trim();//去空白

		if($user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------
		$sql = "SELECT count(1) as count
				FROM `mssr_rec_comment_log`
				WHERE `comment_to` = $user_id
				";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		$array["count"]  = $retrun[0]["count"];


		echo json_encode($array,1);
		?>