<?
//-------------------------------------------------------
//版本編號 1.0
//讀取販售書籍之數量
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
		$auth_open_publish=(isset($_POST['auth_open_publish']))?$_POST['auth_open_publish']:0;
		$i_s = (int)$_POST['i_s'];
 		//trim();//去空白
		if($i_s==0)$auth_open_publish=1;//非學生設為預設條件
		if($user_permission != $_SESSION["permission"] || $user_id != $_SESSION["uid"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------

	//搜尋推薦資訊
	//==================================上架條件1:  兩項推薦才可上架==============================================================
	if($auth_open_publish <= 1)
	{
		$count=0;
		$sql = "SELECT
					count(1) AS count
				FROM mssr_rec_book_cno
				WHERE `user_id` ='".$user_id."'
				AND
				book_on_shelf_state != '上架'
				ORDER BY  `mssr_rec_book_cno`.`keyin_cdate` DESC ";

		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		$array["count"] = $retrun[0]["count"];

	}
	//==================================上架條件2:  老師同意才可上架==============================================================
	else if($auth_open_publish == 2)
	{

		$sql = "SELECT
					count(1) AS count
				FROM mssr_rec_book_cno
				WHERE `user_id` ='".$user_id."'
				AND	book_on_shelf_state != '上架'
				AND has_publish = '可'
				ORDER BY  `mssr_rec_book_cno`.`keyin_cdate` DESC ";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		$array["count"] = $retrun[0]["count"];

	}
	//==================================上架條件3:  老師評分4以上才可上架==============================================================
	else if($auth_open_publish == 3)
	{
		$sql = "SELECT
					count(1) AS count
				FROM mssr_rec_book_cno
				WHERE `user_id` ='".$user_id."'
				AND
				book_on_shelf_state != '上架'
				ORDER BY  `mssr_rec_book_cno`.`keyin_cdate` DESC ";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		$array["count"] = $retrun[0]["count"];
	}



		echo json_encode($array,1);
		?>