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
		$select_mode        =(isset($_POST['select_mode']))?(int)$_POST['select_mode']:0;
		$auth_read_opinion_limit_day = (int)$_POST["auth_read_opinion_limit_day"];
 		//trim();//去空白

		if($user_permission != $_SESSION["permission"] || $select_mode ==0)
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------
	$array["rec_count"]=0;
		if($select_mode == 1){
			$sql = "
					SELECT count(1) AS count
					FROM   `mssr_book_read_opinion_cno`
					WHERE  user_id = {$user_id}
				";

			$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
			$array["rec_count"]  = $retrun[0]["count"];
			}
		if($select_mode == 2){

			$tmparray = array();
			$sql = "
						SELECT rec_stat_cno,
							rec_draw_cno,
							rec_text_cno,
							rec_record_cno,
							mssr_book_read_opinion_log.book_sid,
							mssr_book_read_opinion_log.user_id,
							MAX(mssr_book_read_opinion_log.borrow_sdate) AS `borrow_sdate`,
							mssr_book_read_opinion_log.keyin_cdate,
							read_state
						FROM `mssr_book_read_opinion_cno`

							LEFT JOIN mssr_book_read_opinion_log
							ON mssr_book_read_opinion_log.book_sid = `mssr_book_read_opinion_cno`.book_sid

					LEFT JOIN mssr_rec_book_cno
					ON mssr_book_read_opinion_log.book_sid = mssr_rec_book_cno.book_sid
					AND  mssr_book_read_opinion_log.user_id = mssr_rec_book_cno.user_id

					LEFT JOIN mssr_rec_teacher_read
					ON mssr_rec_teacher_read.user_id = mssr_book_read_opinion_cno.user_id
					AND mssr_book_read_opinion_log.book_sid = mssr_rec_teacher_read.book_sid
					AND read_state = 1

					WHERE `mssr_book_read_opinion_cno`.user_id = ".$user_id."
					AND  mssr_book_read_opinion_log.user_id = `mssr_book_read_opinion_cno`.user_id
					GROUP BY mssr_book_read_opinion_log.book_sid
					ORDER BY  `mssr_book_read_opinion_log`.`borrow_sdate` DESC
						";
			$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
			foreach($retrun as $key1=>$val1)
			{
				$tmparray[$key1]["stat_txt"] = "";
				$tmparray[$key1]["text_txt"] = "";
				$tmparray[$key1]["draw_txt"] = "";
				$tmparray[$key1]["record_txt"] = "";
				$tmparray[$key1]["teacher_read"] = false;
				//老師閱讀過
				if($val1['read_state'] == 1) $tmparray[$key1]["teacher_read"] = true;



				//教師評論
				$sql = "
						SELECT  `book_sid`,
								`comment_type`,
								`keyin_cdate`,
								`comment_score`,
								`comment_content`,
								`has_del_rec`,
								`rec_sid`,
							MAX(keyin_cdate)
						FROM `mssr_rec_comment_log`
						WHERE comment_to = ".$user_id."
						AND book_sid = '".$val1['book_sid']."'
						GROUP BY `book_sid`,`comment_type`";
				$retrun2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

				//填入欄位顯示的字樣
				$tmparray[$key1]["comment_content"]=$retrun2[0]['comment_content'];
				foreach($retrun2 as $key2=>$val2)
				{
					/*評分算 有閱過*/$tmparray[$key1]["teacher_read"] = true;
					if($val2["has_del_rec"] == "有"&&$val2["comment_type"] == "text")
					{

						$sql = "SELECT rec_state
								FROM  `mssr_rec_book_text_log`
								where user_id = ".$user_id."
								AND   book_sid = '".$val1['book_sid']."'
								ORDER BY keyin_cdate DESC
								";
						$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
						if($retrun3[0]["rec_state"] == "隱藏")$tmparray[$key1]["text_txt"] .= "刪除";
						else if($val2["comment_type"] == "text")$tmparray[$key1]["text_txt"] .= "！";
					}else if($val2["comment_type"] == "text")$tmparray[$key1]["text_txt"] .= "！";

					if($val2["has_del_rec"] == "有"&&$val2["comment_type"] == "draw")
					{

						$sql = "SELECT rec_state
								FROM  `mssr_rec_book_draw_log`
								where user_id = ".$user_id."
								AND   book_sid = '".$val1['book_sid']."'
								ORDER BY keyin_cdate DESC
								";
						$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
						if($retrun3[0]["rec_state"] == "隱藏")$tmparray[$key1]["draw_txt"] .= "刪除";
						else if($val2["comment_type"] == "draw")$tmparray[$key1]["draw_txt"] .= "！";
					}else if($val2["comment_type"] == "draw")$tmparray[$key1]["draw_txt"] .= "！";

					if($val2["has_del_rec"] == "有"&&$val2["comment_type"] == "record")
					{

						$sql = "SELECT rec_state
								FROM  `mssr_rec_book_record_log`
								where user_id = ".$user_id."
								AND   book_sid = '".$val1['book_sid']."'
								ORDER BY keyin_cdate DESC
								";
						$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
						if($retrun3[0]["rec_state"] == "隱藏")$tmparray[$key1]["record_txt"] .= "刪除";
						else if($val2["comment_type"] == "record")$tmparray[$key1]["record_txt"] .= "！";
					}else if($val2["comment_type"] == "record")$tmparray[$key1]["record_txt"] .= "！";




				}
				$count = 0 ;
				if($val1["rec_stat_cno"] >= 1 && $tmparray[$key1]["stat_txt"]!="刪除")
				{
					$tmparray[$key1]["stat_txt"] .= "○";
					$count++;
				}
				if($val1["rec_text_cno"] >= 1 && $tmparray[$key1]["text_txt"]!="刪除")
				{
					$tmparray[$key1]["text_txt"] .= "○";
					$count++;
				}
				if($val1["rec_draw_cno"] >= 1 && $tmparray[$key1]["draw_txt"]!="刪除")
				{
					$tmparray[$key1]["draw_txt"] .= "○";
					$count++;
				}
				if($val1["rec_record_cno"] >= 1 && $tmparray[$key1]["record_txt"]!="刪除")
				{
					$tmparray[$key1]["record_txt"] .= "○";
					$count++;
				}

				//計算該筆是否可選擇
				if($count >= 2 ||   date("Y-m-d",strtotime("-".$auth_read_opinion_limit_day." day")) <= $val1['borrow_sdate'])
				{
					$array["rec_count"]++;
				}
			}
		}
		if($select_mode == 3){
			$tmparray = array();
			$sql = "

						SELECT rec_stat_cno,
							rec_draw_cno,
							rec_text_cno,
							rec_record_cno,
							mssr_book_read_opinion_log.book_sid,
							mssr_book_read_opinion_log.user_id,
							MAX(mssr_book_read_opinion_log.borrow_sdate) AS `borrow_sdate`,
							mssr_book_read_opinion_log.keyin_cdate,
							read_state
						FROM `mssr_book_read_opinion_cno`

							LEFT JOIN mssr_book_read_opinion_log
							ON mssr_book_read_opinion_log.book_sid = `mssr_book_read_opinion_cno`.book_sid

					LEFT JOIN mssr_rec_book_cno
					ON mssr_book_read_opinion_log.book_sid = mssr_rec_book_cno.book_sid
					AND  mssr_book_read_opinion_log.user_id = mssr_rec_book_cno.user_id

					LEFT JOIN mssr_rec_teacher_read
					ON mssr_rec_teacher_read.user_id = mssr_book_read_opinion_cno.user_id
					AND mssr_book_read_opinion_log.book_sid = mssr_rec_teacher_read.book_sid
					AND read_state = 1

					WHERE `mssr_book_read_opinion_cno`.user_id = ".$user_id."
					AND  mssr_book_read_opinion_log.user_id = `mssr_book_read_opinion_cno`.user_id
					GROUP BY mssr_book_read_opinion_log.book_sid
					ORDER BY  `mssr_book_read_opinion_log`.`borrow_sdate` DESC
						";
			$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
			foreach($retrun as $key1=>$val1)
			{
				$tmparray[$key1]["stat_txt"] = "";
				$tmparray[$key1]["text_txt"] = "";
				$tmparray[$key1]["draw_txt"] = "";
				$tmparray[$key1]["record_txt"] = "";
				$tmparray[$key1]["teacher_read"] = false;
				//老師閱讀過
				if($val1['read_state'] == 1) $tmparray[$key1]["teacher_read"] = true;



				//教師評論
				$sql = "
							SELECT  `book_sid`,
									`comment_type`,
									`keyin_cdate`,
									`comment_score`,
									`comment_content`,
									`has_del_rec`,
									`rec_sid`,
									MAX(keyin_cdate)
							FROM `mssr_rec_comment_log`
							WHERE comment_to = ".$user_id."
							AND book_sid = '".$val1['book_sid']."'

						GROUP BY `book_sid`,`comment_type`";
				$retrun2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

				//填入欄位顯示的字樣
				$tmparray[$key1]["comment_content"]=$retrun2[0]['comment_content'];
				foreach($retrun2 as $key2=>$val2)
				{
					/*評分算 有閱過*/$tmparray[$key1]["teacher_read"] = true;
					if($val2["has_del_rec"] == "有"&&$val2["comment_type"] == "text")
					{

						$sql = "SELECT rec_state
								FROM  `mssr_rec_book_text_log`
								where user_id = ".$user_id."
								AND   book_sid = '".$val1['book_sid']."'
								ORDER BY keyin_cdate DESC
								";
						$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
						if($retrun3[0]["rec_state"] == "隱藏")$tmparray[$key1]["text_txt"] .= "刪除";
						else if($val2["comment_type"] == "text")$tmparray[$key1]["text_txt"] .= "！";
					}else if($val2["comment_type"] == "text")$tmparray[$key1]["text_txt"] .= "！";

					if($val2["has_del_rec"] == "有"&&$val2["comment_type"] == "draw")
					{

						$sql = "SELECT rec_state
								FROM  `mssr_rec_book_draw_log`
								where user_id = ".$user_id."
								AND   book_sid = '".$val1['book_sid']."'
								ORDER BY keyin_cdate DESC
								";
						$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
						if($retrun3[0]["rec_state"] == "隱藏")$tmparray[$key1]["draw_txt"] .= "刪除";
						else if($val2["comment_type"] == "draw")$tmparray[$key1]["draw_txt"] .= "！";
					}else if($val2["comment_type"] == "draw")$tmparray[$key1]["draw_txt"] .= "！";

					if($val2["has_del_rec"] == "有"&&$val2["comment_type"] == "record")
					{

						$sql = "SELECT rec_state
								FROM  `mssr_rec_book_record_log`
								where user_id = ".$user_id."
								AND   book_sid = '".$val1['book_sid']."'
								ORDER BY keyin_cdate DESC
								";
						$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
						if($retrun3[0]["rec_state"] == "隱藏")$tmparray[$key1]["record_txt"] .= "刪除";
						else if($val2["comment_type"] == "record")$tmparray[$key1]["record_txt"] .= "！";
					}else if($val2["comment_type"] == "record")$tmparray[$key1]["record_txt"] .= "！";




				}
				$count = 0 ;
				if($val1["rec_stat_cno"] >= 1 && $tmparray[$key1]["stat_txt"]!="刪除")
				{
					$tmparray[$key1]["stat_txt"] .= "○";
					$count++;
				}
				if($val1["rec_text_cno"] >= 1 && $tmparray[$key1]["text_txt"]!="刪除")
				{
					$tmparray[$key1]["text_txt"] .= "○";
					$count++;
				}
				if($val1["rec_draw_cno"] >= 1 && $tmparray[$key1]["draw_txt"]!="刪除")
				{
					$tmparray[$key1]["draw_txt"] .= "○";
					$count++;
				}
				if($val1["rec_record_cno"] >= 1 && $tmparray[$key1]["record_txt"]!="刪除")
				{
					$tmparray[$key1]["record_txt"] .= "○";
					$count++;
				}

				//計算該筆是否可選擇
				if($count >= 2 ||   date("Y-m-d",strtotime("-".$auth_read_opinion_limit_day." day")) <= $val1['borrow_sdate'])
				{

				}
				else
				{
					$array["rec_count"]++;
				}
			}
		}






		echo json_encode($array,1);
		?>