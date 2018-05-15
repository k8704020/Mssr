<?
//-------------------------------------------------------
//版本編號 1.0
//登記書籍  讀取書籍分類(書局分店用)
//ajax
//-------------------------------------------------------
	
	//---------------------------------------------------
	//輸入 
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
				APP_ROOT.'inc/conn/code',
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
		$array["echo"] ="";	
        //POST

		
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------
		
		//POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
		$book_sid   =(isset($_POST['book_sid']))?$_POST['book_sid']:0;
		$user_school   =(isset($_POST['user_school']))?$_POST['user_school']:0;	
		if($user_id != $_SESSION["uid"] || $user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------
		$array["book_category"]= array();
		$sql = "SELECT `cat_name`,`cat_id`
				FROM `mssr_book_category`
				WHERE `school_code` = '$user_school'
				AND `cat_state` = '啟用'
				AND `cat3_id` = 1
				AND `cat2_id` = 1
				AND `cat_name` != '未分類'";
		$are_you_cracra = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		{
			$array["book_category"] = $are_you_cracra;
			
		}
		$array["count_category"] = count($array["book_category"]);
		
		
		$array["category_user"]= array();
		$sql = "SELECT `rev_id`
				FROM `mssr_book_category_user_rev`
				WHERE `book_sid` = '$book_sid' 
				AND `create_by` ='$user_id'";
		$are_you_crycry = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		{
			$array["category_user"] = $are_you_crycry;
			
		}
		$array["count_category_user"] = count($array["category_user"]);
		
		
		echo json_encode($array,1);
		
		
		?>