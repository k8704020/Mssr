<?php
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
		$dadad = date("Y-m-d  H:i:s");
        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;

		//檢查資料正確性
		$book_sid = mysql_prep($_POST['book_sid']);
		if($user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------

		if($user_id == 0)die("");

		$note="";
		//非自行下架\\判斷是否為 下架不正常書籍
		if($user_id != $_SESSION["uid"])
		{
			//搜尋書籍審核
				$book_verified = 1;

					$sql =	"SELECT `book_verified`
							FROM `mssr_book_unverified`
							WHERE `book_sid` = '".$book_sid."'";
					$book_tmpmp = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
					$book_verified = $book_tmpmp[0]["book_verified"];
				if($book_verified != 2)
				{
					$array["error"] ="你違法進入了喔!!  請重新登入";
					die(json_encode($array,1));
				}
				$note = "問題書籍下架";
		}

	//先搜尋有無做過推薦(學期)
		$sql = "
				SELECT count(1) AS count
				FROM  `mssr_rec_book_cno_semester`
				WHERE user_id = '".$user_id."'
				AND book_sid = '".$book_sid."'";
		$retrun_semester = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
			//搜尋一周資料
		$sql = "
				SELECT count(1) AS count
				FROM  `mssr_rec_book_cno_one_week`
				WHERE user_id = '".$user_id."'
				AND book_sid = '".$book_sid."'";
		$retrun_week = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);

	//將書籍資訊改為下架
	$sql = "UPDATE mssr_rec_book_cno
				 SET book_on_shelf_state = '下架', keyin_mdate = '".$dadad."', `keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
				 WHERE user_id = '".$user_id."' and book_sid = '".$book_sid."';";

	//判斷學期資料是否存在
	if($retrun_semester[0]['count'] >= 1)
	{
		$sql = $sql."UPDATE mssr_rec_book_cno_semester
		 SET book_on_shelf_state = '下架', keyin_mdate = '".$dadad."', `keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
		 WHERE user_id = '".$user_id."' and book_sid = '".$book_sid."';";
	}
	else
	{
		$sql = $sql."Insert into mssr_rec_book_cno_semester
							(`edit_by`,
							`user_id`,
							`book_sid`,
							`rec_stat_cno`,
							`rec_draw_cno`,
							`rec_text_cno`,
							`rec_record_cno`,
							`has_publish`,
							`book_on_shelf_state`,
							`keyin_cdate`,
							`keyin_mdate`,
							`keyin_ip`)
						select
							`edit_by`,
							`user_id`,
							`book_sid`,
							`rec_stat_cno`,
							`rec_draw_cno`,
							`rec_text_cno`,
							`rec_record_cno`,
							`has_publish`,
							`book_on_shelf_state`,
							`keyin_cdate`,
							`keyin_mdate`,
							`keyin_ip`
						from mssr_rec_book_cno
						WHERE book_sid = '".$book_sid."'
						AND   user_id = '".$user_id."'
						;";
	}

	db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	//判斷學期資料是否存在
	if($retrun_week[0]['count'] >= 1)
	{
		$sql = $sql."UPDATE mssr_rec_book_cno_one_week
		 SET book_on_shelf_state = '下架', keyin_mdate = '".$dadad."', `keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
		 WHERE user_id = '".$user_id."' and book_sid = '".$book_sid."';";
	}
	else
	{
		$sql = $sql."Insert into mssr_rec_book_cno_one_week
							(`edit_by`,
							`user_id`,
							`book_sid`,
							`rec_stat_cno`,
							`rec_draw_cno`,
							`rec_text_cno`,
							`rec_record_cno`,
							`has_publish`,
							`book_on_shelf_state`,
							`keyin_cdate`,
							`keyin_mdate`,
							`keyin_ip`)
						select
							`edit_by`,
							`user_id`,
							`book_sid`,
							`rec_stat_cno`,
							`rec_draw_cno`,
							`rec_text_cno`,
							`rec_record_cno`,
							`has_publish`,
							`book_on_shelf_state`,
							`keyin_cdate`,
							`keyin_mdate`,
							`keyin_ip`
						from mssr_rec_book_cno
						WHERE book_sid = '".$book_sid."'
						AND   user_id = '".$user_id."'
						;";
	}

	db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

	//將下架LOG  便更為下架狀態   待補	  及  刪除瀏覽書籍暫存
	$sql = "UPDATE mssr_rec_on_off_shelf_log
			SET keyin_ip = '".$_SERVER["REMOTE_ADDR"]."' , off_shelf_date = '".$dadad."'
			WHERE user_id = '".$user_id."' and book_sid = '".$book_sid."' AND off_shelf_date = '0000-00-00 00:00:00'
			ORDER BY  `mssr_rec_on_off_shelf_log`.`on_shelf_date` DESC
			LIMIT 1 ; 


			DELETE FROM `mssr_visit_on_shelf_book` 
		  	WHERE visit_to= '".$user_id."' 
		  	AND book_sid='".$book_sid."';


				 ";
		 


	db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);


		echo json_encode($array,1);
		?>