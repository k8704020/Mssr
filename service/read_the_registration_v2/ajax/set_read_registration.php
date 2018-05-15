<?
//-------------------------------------------------------
//版本編號 1.0
//登記書籍  寫入借還書資料(借書  還書  同時)
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
	require_once(str_repeat("../",3)."/inc/book_borrow_sid/code.php");
	require_once(str_repeat("../",3)."/inc/get_book_info/code.php");

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
	$conn_user=conn($db_type='mysql',$arry_conn_user);


	

	//-----------------------------------------------
	//通用
	//-----------------------------------------------
	
	//-------------------------------------------
	//初始化, curl設定
	//-------------------------------------------
		$array =array();
		$array["error"] = "";
		$array["echo"] ="";
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------
		$dadad = date("Y-m-d  H:i:s");
        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
		$book_sid		=(isset($_POST['book_sid']))?mysql_prep(trim($_POST['book_sid'])):"";
		$book_name      =(isset($_POST['book_name']))?mysql_prep(trim($_POST['book_name'])):"";
		
		$shool_code     =(isset($_POST['shool_code']))?mysql_prep(trim($_POST['shool_code'])):"";
		$school_category=(isset($_POST['school_category']))?mysql_prep(trim($_POST['school_category'])):0;
		$grade_id       =(isset($_POST['grade_id']))?mysql_prep(trim($_POST['grade_id'])):0;
		$classroom_id   =(isset($_POST['classroom_id']))?mysql_prep(trim($_POST['classroom_id'])):0;
		
		$array["borrow_sid"] = $borrow_sid = book_borrow_sid(1,mb_internal_encoding());
		
		if($user_id != $_SESSION["uid"] || $user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}
	
		
		if($book_sid =="")
		{
			$array["echo"] ="好像有點錯誤再來一次吧";
			die(json_encode($array,1));
		}
	
	//-------------------------------------------
	//SQL  登記借還書資訊
	//-------------------------------------------
		$sql = "INSERT INTO  `mssr`.`mssr_book_borrow_log` 
				(
				`user_id` ,
				`book_sid` ,
				`school_code` ,
				`school_category` ,
				`grade_id` ,
				`classroom_id` ,
				`borrow_sid` ,
				`borrow_sdate` ,
				`borrow_edate` ,
				`keyin_ip`
				)VALUES(
				'{$user_id}',
				'{$book_sid}',
				'".$shool_code."',
				'".$school_category."',
				'".$grade_id."',
				'".$classroom_id."',
				'{$borrow_sid}',
				'".$dadad."',
				'".$dadad."',
				'".$_SERVER["REMOTE_ADDR"]."'
				);";
		$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

		$sql = "SELECT `log_id`
				FROM `mssr_book_borrow_log`
				WHERE `user_id` = {$user_id}
				AND `book_sid` = '{$book_sid}'
				ORDER BY `log_id` DESC";
		$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		//擷取log_id
		$array["borrow_sid"] = $log_id = $arrys_result[0]['log_id'];
		
		$sql = "
			UPDATE `mssr`.`mssr_book_borrow_log`
			SET `borrow_sid` = '{$log_id}'
			WHERE `log_id` = '{$log_id}'
			
			;INSERT INTO  `mssr`.`mssr_book_borrow` 
				(
				`user_id` ,
				`book_sid` ,
				`school_code` ,
				`school_category` ,
				`grade_id` ,
				`classroom_id` ,
				`borrow_sid` ,
				`borrow_sdate` ,
				`borrow_edate` ,
				`keyin_ip`
				)VALUES(
				'{$user_id}',
				'{$book_sid}',
				'".$shool_code."',
				'".$school_category."',
				'".$grade_id."',
				'".$classroom_id."',
				'{$log_id}',
				'".$dadad."',
				'".$dadad."',
				'".$_SERVER["REMOTE_ADDR"]."'
				);
			INSERT INTO  `mssr`.`mssr_book_borrow_semester` 
				(
				`user_id` ,
				`book_sid` ,
				`school_code` ,
				`school_category` ,
				`grade_id` ,
				`classroom_id` ,
				`borrow_sid` ,
				`borrow_sdate` ,
				`borrow_edate` ,
				`keyin_ip`
				)VALUES(
				'{$user_id}',
				'{$book_sid}',
				'".$shool_code."',
				'".$school_category."',
				'".$grade_id."',
				'".$classroom_id."',
				'{$log_id}',
				'".$dadad."',
				'".$dadad."',
				'".$_SERVER["REMOTE_ADDR"]."'
				);
			";
		$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	
	//-------------------------------------------
	//特殊:增加家長資訊進入 PS
	//-------------------------------------------

		// echo $get_book_info[0]['book_name'];
		// die()
		

	
		//搜尋家長ID
		$sql = "SELECT uid_main 
				FROM `kinship`
				WHERE uid_sub = $user_id";
		$uid_main_result = db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
	
		if(sizeof($uid_main_result)!=0)
		{//寫入家長通資訊息
			
			$O_SQL_HOST = $arry_conn_mssr["db_host"];  // MySQL database server
			$O_SQL_DB = "ps";              // MySQL database containing user authentication information
			$O_SQL_USER = $arry_conn_mssr["db_user"];           // MySQL username for querying authentication information
			$O_SQL_PW = $arry_conn_mssr["db_pass"];   // MySQL password for querying authentication information
			
			$O_link = mysql_connect($O_SQL_HOST, $O_SQL_USER, $O_SQL_PW); 
			if (!$O_link) die("建立資料連接失敗");
	
			//開啟資料表
			$O_db_selected = mysql_select_db($O_SQL_DB, $O_link);
			if (!$O_db_selected) die("開啟資料庫失敗");
			
			mysql_query("set names utf8");
			
			$array_select = array("book_name");
			$get_book_info=get_book_info($conn='',$book_sid,$array_select,$arry_conn_mssr);
			
			$O_sql = "INSERT INTO `msg_notice`
							(
								
								`target`,
								`msg_type`,
								`number`,
								`msg_title`,
								`date`,
								`state`,
								`complete`
							) VALUES (
								'".$uid_main_result[0]["uid_main"]."',
								'7',
								'".$book_sid."',
								'".$get_book_info[0]['book_name']."',
								'".$dadad."',
								0,
								3
							)
								
					 ";
			mysql_query($O_sql, $O_link);
			
		}
	//-------------------------------------------
	//
	//-------------------------------------------	
		echo json_encode($array,1);
		
		
		?>