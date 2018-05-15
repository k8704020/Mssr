<?
//-------------------------------------------------------
//版本編號 1.0
//登記書籍  寫入書籍資料
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
	require_once(str_repeat("../",4)."/center/teacher_center/inc/book/book/book_unverified_sid/code.php");

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
		
        $isbn10=(isset($_POST['isbn10']))?mysql_prep(trim($_POST['isbn10'])):"";
		$isbn13=(isset($_POST['isbn13']))?mysql_prep(trim($_POST['isbn13'])):"";
		
		
		$book_name=(isset($_POST['book_name']))?$_POST['book_name']:"";
		$book_author=(isset($_POST['book_author']))?$_POST['book_author']:"";
		$book_publisher=(isset($_POST['book_publisher']))?$_POST['book_publisher']:"";
		
		$book_name = mysql_prep(trim($book_name));
		$book_author = mysql_prep(trim($book_author));
		$book_publisher = mysql_prep(trim($book_publisher));

		$book_sid = mysql_prep($_POST['book_sid']);

		
			
		if($user_id != $_SESSION["uid"] || $user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}
		
		if($book_name == "")
		{
			$array["echo"] ="書名沒有輸入喔";
			die(json_encode($array,1));
		}

		

	//-------------------------------------------
	//SQL
	//-------------------------------------------
	
		
		$dadad = date("Y-m-d  H:i:s");
		// $array["book_sid"] = $book_sid = book_unverified_sid($_SESSION["uid"],mb_internal_encoding());
		
		$sql = "
				
				UPDATE `mssr`.`mssr_book_library`
				SET `book_name`='$book_name'
				WHERE `book_sid`='$book_sid';
				
				UPDATE `mssr`.`mssr_book_unverified` 
				SET `book_name`='$book_name'
				WHERE `book_sid`='$book_sid';

				UPDATE `mssr`.`mssr_book_class` 
				SET `book_name`='$book_name'
				WHERE `book_sid`='$book_sid';

				UPDATE `mssr`.`mssr_book_global` 
				SET `book_name`='$book_name'
				WHERE `book_sid`='$book_sid';

			
				

				";


		
		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		
		echo json_encode($array,1);
		
		
		?>