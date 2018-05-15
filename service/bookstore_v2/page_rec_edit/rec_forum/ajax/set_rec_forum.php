<?
//-------------------------------------------------------
//版本編號 1.0
//訂閱書籍
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
	require_once(str_repeat("../",3)."/inc/mssr_rec_book_text_sid/code.php");
	require_once(str_repeat("../",3)."/inc/tx_sys_sid/code.php");

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
		$array["text"] = "";
		$array["coin"] = 0;
		
		
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------

        //POST
		$book_sid        =(isset($_POST['book_sid']))?mysql_prep(trim($_POST['book_sid'])):"";
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$stttttyle        =(isset($_POST['stttttyle']))?(int)$_POST['stttttyle']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
		$eagle_code_list=(isset($_POST['eagle_code_list']))?json_decode($_POST['eagle_code_list']):0;		
		$conten = mysql_prep(trim($_POST['conten']));
		$tittle = mysql_prep(trim($_POST['tittle']));
		$slecet = mysql_prep(trim($_POST['slecet']));
				
		if($user_permission != $_SESSION["permission"]||$stttttyle ==0|| $user_id != $_SESSION["uid"] || $user_id == 0 || $book_sid =="")
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}
		
		
		if($conten =="")
		{
			$array["echo"] ="你還沒輸入內容";
			die(json_encode($array,1));
		}
		if($tittle =="")
		{
			$array["echo"] ="你還沒輸入標題";
			die(json_encode($array,1));
		}
	//-------------------------------------------
	//SQL
	//-------------------------------------------
		$dadad = date("Y-m-d  H:i:s");
		$sql = "INSERT INTO `mssr_forum`.`mssr_forum_article`
				(
					`edit_by`,
					`user_id`,
					`group_id`,
					`article_from`,
					`article_category`,
					`article_type`,
					`article_state`,
					`article_like_cno`,
					`article_report_cno`,
					`keyin_cdate`,
					`keyin_mdate`
				)VALUES(
					$user_id,
					$user_id,
					0,
					1,
					$stttttyle,
					1,
					1,
					0,
					0,
					'{$dadad}',
					'{$dadad}'
				)";
		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		
		$sql = "SELECT `article_id`
				FROM `mssr_forum`.`mssr_forum_article`
				WHERE `user_id` = {$user_id}
				ORDER BY `keyin_mdate`  DESC";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		$article_id = $retrun[0]["article_id"];
		
		$book_db ="";
		if($book_sid[2] == "u"){$book_db = "mssr_book_unverified";}
		else if($book_sid[2] == "l"){$book_db = "mssr_book_library";}
		else if($book_sid[2] == "g"){$book_db = "mssr_book_global";}
		else if($book_sid[2] == "c"){$book_db = "mssr_book_class";}
		else 
		{
			$array["error"]= "錯誤 請重新開啟 CODE:E1023";
			json_encode($array,1);
		}
		
		$sql = "SELECT
					`book_isbn_10`,
					`book_isbn_13`
				FROM `mssr`.`{$book_db}`
				WHERE `book_sid` = '{$book_sid}'";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		if(count($retrun)!=0)
		{
			$book_isbn_13 = $retrun[0]["book_isbn_13"];
			$book_isbn_10 = $retrun[0]["book_isbn_10"];
		}
		else
		{
			$array["error"]= "錯誤 請重新開啟 CODE:E1024";
			json_encode($array,1);
		}
		
		$sql = "INSERT INTO `mssr_forum`.`mssr_forum_article_book_rev`
							(
								`book_sid`,
								`book_isbn_10`,
								`book_isbn_13`,
								`article_id`
							)
							VALUE
							(
								'{$book_sid}',
								'{$book_isbn_10}',
								'{$book_isbn_13}',
								{$article_id}
							)";
		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		
		$sql = "INSERT INTO `mssr_forum`.`mssr_forum_article_detail`
							(
								`article_id`,
								`article_title`,
								`article_content`,
								`keyin_ip`
							)
							VALUE
							(
								{$article_id},
								'{$tittle}',
								'{$conten}',
								'".$_SERVER["REMOTE_ADDR"]."'
							)";
		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);	
		
		
		$aa = array();
		if(!$eagle_code_list)
		{
			$sql = "INSERT INTO `mssr_forum`.`mssr_forum_article_eagle_rev`
								(
									`eagle_code`,
									`article_id`,
									`keyin_mdate`
								)
								VALUE
								(
									0,
									{$article_id},
									'{$dadad}'
								)";
				db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		}else
		{
			foreach($eagle_code_list as $key => $vul)
			{
				if(@!$aa[$vul])
				{
					$aa[(int)$vul] = 1;
					$sql = "INSERT INTO `mssr_forum`.`mssr_forum_article_eagle_rev`
									(
										`eagle_code`,
										`article_id`,
										`keyin_mdate`
									)
									VALUE
									(
										".(int)$vul.",
										{$article_id},
										'{$dadad}'
									)";
					db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
				}
			}
		}
		
		echo json_encode($array,1);
		?>