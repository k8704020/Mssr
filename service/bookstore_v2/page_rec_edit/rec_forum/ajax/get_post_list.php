<?
//-------------------------------------------------------
//版本編號 1.0
//讀上架書籍資訊
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
	require_once(str_repeat("../",5)."/inc/get_book_info/code.php");

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
		//文字
		$array["error"] = "";
		$array["echo"] = "";
		$array["count"] = 0;
		$array["data"] = array();
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------

        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
		$book_sid = mysql_prep(trim($_POST['book_sid']));
 		//trim();//去空白

		if($user_permission != $_SESSION["permission"]|| $user_id ==0)
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------

		//蚊自s
		$sql = "
				SELECT  `edit_by`,
						`mssr_forum_article`.`article_id`,
						`article_like_cno`,
						`article_report_cno`,
						`article_title`,
						`article_content`
				FROM `mssr_forum`.`mssr_forum_article`

				LEFT JOIN `mssr_forum`.`mssr_forum_article_book_rev`
				ON `mssr_forum_article_book_rev`.`article_id` = `mssr_forum_article`.`article_id`

				LEFT JOIN `mssr_forum`.`mssr_forum_article_detail`
				ON `mssr_forum_article_detail`.`article_id` = `mssr_forum_article`.`article_id`

				WHERE `mssr_forum_article`.`user_id`={$user_id}
				AND `mssr_forum_article_book_rev`.`book_sid` = '{$book_sid}'

			";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		$array["count"] = count($retrun);
		$array["data"] = $retrun;


		echo json_encode($array,1);
		?>