<?
//-------------------------------------------------------
//版本編號 1.0
//搜尋線上資料 並自動下載
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
	require_once(str_repeat("../",4)."/inc/search_book_info_online/code.php");
	require_once(str_repeat("../",4)."/inc/search_book_page_online/code.php");
	require_once(str_repeat("../",4)."/center/teacher_center/inc/book/book/book_unverified_sid/code.php");
	//require_once(str_repeat("../",4)."/center/teacher_center/inc/book/book/book_global_sid/code.php");

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
        $isbn10=(isset($_POST['isbn10']))?mysql_prep(trim($_POST['isbn10'])):"";
		$isbn13=(isset($_POST['isbn13']))?mysql_prep(trim($_POST['isbn13'])):"";
		
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------
		
		//POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
		
		if($user_id != $_SESSION["uid"] || $user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------
	
		$book_info=search_book_info_online($isbn13);

  		
		
		$array["has_info"] = 0;//到底有沒有資料呢
		
   		for($i = 0; $i < sizeof($book_info["book_name"]);$i++)
		{
			$book_name = mysql_prep(trim($book_info["book_name"][$i]));
			$book_author = mysql_prep(trim($book_info["book_author"][$i]));
			$book_publisher = mysql_prep(trim($book_info["book_publisher"][$i]));
			
			//至少要有書名才可存入資料庫
			if($book_name == "")
			{
			
			}
			else
			{	$search_book_page_online=search_book_page_online($isbn13);
				@$page = (int)@$search_book_page_online["page"][0];
				//@$page2 = (int)@$search_book_page_onlineaq;
				//
				$page  = 0;
				$array["has_info"] =  1 ;
				$sql = "";
				$book_sid = book_unverified_sid($_SESSION["uid"],mb_internal_encoding());
				//$book_sid_2 = book_global_sid($_SESSION["uid"],mb_internal_encoding());
				if($i<10)
				{
					//防止 連續輸入 造成同號
					$book_sid = mb_substr($book_sid, 0, 24);
					$book_sid = $book_sid.$i;
					
				}else if($i<100)
				{
					//防止 連續輸入 造成同號
					$book_sid = mb_substr($book_sid, 0, 23);
					$book_sid = $book_sid.$i;
					
				}
				$dadad = date("Y-m-d  H:i:s");
				$sql = "	
				INSERT INTO `mssr`.`mssr_book_unverified`
				(	`create_by`,
					`edit_by`,
					`book_sid`,
					`book_isbn_10`,
					`book_isbn_13`,
					`book_name`,
					`book_author`,
					`book_publisher`,
					`book_page_count`,
					`book_word`,
					`book_from`,
					`book_note`,
					`book_phonetic`,
					`keyin_cdate`,
					`keyin_mdate`,
					`keyin_ip`
				)VALUES(
					'{$user_id}',
					'{$user_id}',
					'{$book_sid}',
					'{$isbn10}',
					'{$isbn13}',
					'{$book_name}',
					'{$book_author}',
					'{$book_publisher}',
					'{$page}',
					'',
					2,
					'{$page2}',
					'無',
					'".$dadad."',
					'".$dadad."',
					'".$_SERVER["REMOTE_ADDR"]."');
				";
				db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
			}
		}
   		//$array["$book_info"] = $book_info;
		echo json_encode($array,1);
		
		
		?>