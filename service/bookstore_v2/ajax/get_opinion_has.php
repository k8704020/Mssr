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
		$array =array();
		$array["error"] = "";
		$array["echo"] = "";
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------

        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
		$auth_read_opinion_limit_day=(int)$_POST["auth_read_opinion_limit_day"];
 		//trim();//去空白

		if($user_permission != $_SESSION["permission"]||$user_id==0)
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------
		$sday = date("Y-m-d",strtotime("-".$auth_read_opinion_limit_day." day"));
		$sql = "
				SELECT count(1) AS count
				FROM `mssr_book_borrow_semester`
				LEFT JOIN mssr_book_read_opinion_log
				ON mssr_book_borrow_semester.borrow_sid = mssr_book_read_opinion_log.borrow_sid
				WHERE mssr_book_borrow_semester.user_id = $user_id
				AND mssr_book_read_opinion_log.opinion_answer IS NULL
				AND mssr_book_borrow_semester.`borrow_sdate`  >= '".$sday." 00:00:00'

				";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		$array["opinion_has"]  = $retrun[0]["count"];


		echo json_encode($array,1);
		?>