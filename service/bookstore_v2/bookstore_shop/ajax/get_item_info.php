<?
//-------------------------------------------------------
//版本編號 1.0
//讀取商品資訊
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
				APP_ROOT.'lib/php/db/code'
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
       	
		
		/*if($user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}
		if($auth_read_opinion_limit_day==0)
		{
			$array["error"] ="?";
			die(json_encode($array,1));
		}*/
	//-------------------------------------------
	//SQL
	//-------------------------------------------
		$sql = "
				SELECT * 
				FROM  `mssr_item` 
				WHERE item_state='上架'
				ORDER BY `mssr_item`.`item_id` DESC";

		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($retrun as $key1=>$val1)
		{
			$array[$key1]["id"] = $val1["item_id"];
			$array[$key1]["name"] = $val1["item_name"];
			$array[$key1]["coin"] = $val1["item_coin"];
		}
		$array["count"] = sizeof($retrun);
		
		echo json_encode($array,1);
		?>