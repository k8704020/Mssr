                                <?
//-------------------------------------------------------
//版本編號 1.0
//寫入set_bookstore_action_log
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
	$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
	


	
	//-----------------------------------------------
	//通用
	//-----------------------------------------------
	
	//-------------------------------------------
	//初始化, curl設定
	//-------------------------------------------

	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------

        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$action_code    =(isset($_POST['action_code']))?mysql_prep($_POST['action_code']):0;
		$action_on      =(isset($_POST['action_on']))?(int)($_POST['action_on']):0;

		
		if($action_on == 0 )
		{
			$array["error"] .="你違法進入了喔!!  請重新登入";
			die(json_encode($action_on,1));
		}
	
	//-------------------------------------------
	//SQL
	//-------------------------------------------
		$dadad = date("Y-m-d  H:i:s");
		
		//搜尋今日拜訪數
		$sql = "INSERT INTO `mssr_action_bookstore_log`
					(
						`user_id`,
						`action_code`,
						`action_on`,
						`keyin_cdate`
					) VALUES (
						".$user_id.",
						'".$action_code."',
						".$action_on.",
						'".$dadad."'
					);";
		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		?>