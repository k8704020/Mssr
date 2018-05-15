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
	require_once(str_repeat("../",3)."/inc/mssr_rec_book_star_sid/code.php");
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

		$array["coin"] = 0;


	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------
		$dadad = date("Y-m-d  H:i:s");
        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
		$book_sid = mysql_prep(trim($_POST['book_sid']));
 		//trim();//去空白

		if($user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

		//檢查資料正確性
		$rec_star_reason_ans = mysql_prep($_POST["reason"]);
		$uid = mysql_prep($_POST["user_id"]);
		$rec_star_rank_ans = mysql_prep($_POST["rank"]);
		//$time = mysql_prep($_POST["time"]);
		$book_id = mysql_prep($_POST["book_sid"]);
		if($uid == 0 || $book_id == NULL || $book_id == "")
		{
			$array["error"] ="錯誤!!  請重新登入";
			die(json_encode($array,1));
		}
	//-------------------------------------------
	//SQL
	//-------------------------------------------

		//先搜尋有無做過推薦
		$sql = "
				SELECT count(1) AS count
				FROM  `mssr_rec_book_cno`
				WHERE user_id = '".$uid."'
				AND book_sid = '".$book_id."'";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);

		//搜尋學期資料
		$sql = "
				SELECT count(1) AS count
				FROM  `mssr_rec_book_cno_semester`
				WHERE user_id = '".$uid."'
				AND book_sid = '".$book_id."'";
		$retrun_semester = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);

		//搜尋一周資料
		$sql = "
				SELECT count(1) AS count
				FROM  `mssr_rec_book_cno_one_week`
				WHERE user_id = '".$uid."'
				AND book_sid = '".$book_id."'";
		$retrun_week = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);

		$sql = "";
		//確認有無統計數
		if($retrun[0]['count'] >= 1)
		{
			$sql = "UPDATE mssr_rec_book_cno
					SET rec_stat_cno = rec_stat_cno+1 , keyin_mdate = '".$dadad."',`rec_state` = 1, `keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
					WHERE book_sid = '".$book_id."'
					AND   user_id = '".$uid."'
					;";
			//判斷學期資料是否存在
			if($retrun_semester[0]['count'] >= 1)
			{
				$sql = $sql."UPDATE mssr_rec_book_cno_semester
						SET rec_stat_cno = rec_stat_cno+1 , keyin_mdate = '".$dadad."', `rec_state` = 1,`keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
						WHERE book_sid = '".$book_id."'
						AND   user_id = '".$uid."'
						;";
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
									`rec_state`,
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
									`rec_state`,
									`keyin_cdate`,
									`keyin_mdate`,
									`keyin_ip`
								from mssr_rec_book_cno
								WHERE book_sid = '".$book_id."'
								AND   user_id = '".$uid."'
								;";
			}
			//判斷一周資料是否存在
			if($retrun_week[0]['count'] >= 1)
			{
				$sql = $sql."UPDATE mssr_rec_book_cno_one_week
						SET rec_stat_cno = rec_stat_cno+1 , keyin_mdate = '".$dadad."', `rec_state` = 1,`keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
						WHERE book_sid = '".$book_id."'
						AND   user_id = '".$uid."'
						;";
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
									`rec_state`,
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
									`rec_state`,
									`keyin_cdate`,
									`keyin_mdate`,
									`keyin_ip`
								from mssr_rec_book_cno
								WHERE book_sid = '".$book_id."'
								AND   user_id = '".$uid."'
								;";
			}
		}
		else
		{
			$sql = "INSERT INTO `mssr`.`mssr_rec_book_cno`(
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
					)VALUES (
						'".$uid."',
						'".$uid."',
						'".$book_id."',
						'1',
						'0',
						'0',
						'0',
						'否',
						'未動作',
						'".$dadad."',
						'".$dadad."',
						'".$_SERVER["REMOTE_ADDR"]."'
						);
					INSERT INTO `mssr`.`mssr_rec_book_cno_semester`(
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
					)VALUES (
						'".$uid."',
						'".$uid."',
						'".$book_id."',
						'1',
						'0',
						'0',
						'0',
						'否',
						'未動作',
						'".$dadad."',
						'".$dadad."',
						'".$_SERVER["REMOTE_ADDR"]."'
						);
					INSERT INTO `mssr`.`mssr_rec_book_cno_one_week`(
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
					)VALUES (
						'".$uid."',
						'".$uid."',
						'".$book_id."',
						'1',
						'0',
						'0',
						'0',
						'否',
						'未動作',
						'".$dadad."',
						'".$dadad."',
						'".$_SERVER["REMOTE_ADDR"]."'
						);";
		}

		//=====================確認是否得到金錢   評星繪圖無關金錢交易============================
		$rec_reward = "有";



		//=================寫入評星推薦log=====================
		$rec_d_sid = mssr_rec_book_star_sid($uid,mb_internal_encoding());
		$sql = $sql."INSERT INTO `mssr`.`mssr_rec_book_star` (
						  `user_id`,
						  `book_sid`,
						  `rec_sid`,
						  `rec_rank`,
						  `rec_reason`,
						  `rec_operate_time`,
						  `rec_reward`,
						  `rec_state`,
						  `keyin_cdate`,
						  `keyin_ip`
					  )VALUES(
					  	  '".$uid."',
						  '".$book_id."',
						  '".$rec_d_sid."',
						  '".$rec_star_rank_ans."',
						  '".$rec_star_reason_ans."',
						  '".$time."',
						  '有',
						  '顯示',
						  '".$dadad."',
						  '".$_SERVER["REMOTE_ADDR"]."');

		";
		$sql = $sql."INSERT INTO `mssr`.`mssr_rec_book_star_log` (
						  `user_id`,
						  `book_sid`,
						  `rec_sid`,
						  `rec_rank`,
						  `rec_reason`,
						  `rec_operate_time`,
						  `rec_reward`,
						  `rec_state`,
						  `keyin_cdate`,
						  `keyin_ip`
					  )VALUES(
					  	  '".$uid."',
						  '".$book_id."',
						  '".$rec_d_sid."',
						  '".$rec_star_rank_ans."',
						  '".$rec_star_reason_ans."',
						  '".$time."',
						  '有',
						  '顯示',
						  '".$dadad."',
						  '".$_SERVER["REMOTE_ADDR"]."');

		";
		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);




		echo json_encode($array,1);
		?>