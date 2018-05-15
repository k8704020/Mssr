<?
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
		$dadad = date("Y-m-d  H:i:s");
        //POST
		$item_id = (int)($_POST["item_id"]);
		$item_name = trim(addslashes($_POST["item_name"]));
		$item_info = trim(addslashes($_POST["item_info"]));
		$item_coin = (int)($_POST["item_coin"]);
		$item_state = trim(addslashes($_POST["item_state"]));
		$item_note = trim(addslashes($_POST["item_note"]));
		
		$array = array();
		$array["error"]="";
		
		if($item_id == 0)$array["error"]="ID遺失";//ID遺失
		if($item_name == "")$array["error"]="名稱空白";//名稱空白
		if(!($item_state == "上架" || $item_state == "下架"))$array["error"]="上下架選項錯誤";//名稱空白
		if($item_coin == 0)$array["error"]="金錢錯誤";//ID遺失
		
		if($array["error"]!="")die(json_encode($array,1));
		
	//-------------------------------------------
	//SQL
	//-------------------------------------------
		$sql = "UPDATE `mssr`.`mssr_item` 
				SET `item_name` = '$item_name',
					`item_info` = '$item_info',
					`item_coin` = $item_coin,
					`item_state` = '$item_state',
					`item_note` = '$item_note'
				WHERE `item_id` = $item_id ;";
		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

		echo json_encode($array,1);
		?>