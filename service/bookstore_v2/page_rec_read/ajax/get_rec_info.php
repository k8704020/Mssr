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
	require_once(str_repeat("../",4)."/config/config.php");
	require_once(str_repeat("../",4)."/inc/get_book_info/code.php");

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
		$array["rec_reason_1"] = "";
		$array["rec_reason_2"] = "";
		$array["rec_rank"] = "";
		$array["rec_draw_link"] = "";
		$array["rec_draw_bc"] = "";
		$array["rec_draw_data"] = "";
		$array["rec_draw_type"] = "";
		$array["rec_content_1"] = "";
		$array["rec_content_2"] = "";
		$array["rec_content_3"] = "";
		$array["rec_record_book_sid"] = "";
		$array["rec_record_file"] ="";
		$array["on_booking"] = 0;
		$array["on_booking30"] = 0;
		$array["upload_cno"] = 0;
		$array["book_sid"] = "";
		$array["book_name"] = "";
		$array["have_good"] = 0;
		$array["have_good_1"] = 0;
		$array["have_good_2"] = 0;
		$array["have_good_3"] = 0;
		$array["have_good_4"] = 0;
		$array["have_good_5"] = 0;
		$array["have_good_6"] = 0;
		$array["have_good_count"] = 0;
		$array["have_good_count_1"] = 0;
		$array["have_good_count_2"] = 0;
		$array["have_good_count_3"] = 0;
		$array["have_good_count_4"] = 0;
		$array["have_good_count_5"] = 0;
		$array["have_good_count_6"] = 0;
		$array["rec_draw_has_list"] = array(0,0,0,0);

	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------
		$day30 = date("Y-m-d");

		$day30 = date("Y-m-d",strtotime($day30."-30 day"));
        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
		$read_on        =(isset($_POST['read_on']))?(int)$_POST['read_on']:0;
		$read_max_count=(isset($_POST['read_max_count']))?$_POST['read_max_count']:0;
		//$book_sid = mysql_prep(trim($_POST['book_sid']));
 		//trim();//去空白

		if($user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------
		//搜尋該書籍  以第幾本來獲取資料
		$sql = "SELECT book_sid,
							   user_id
						FROM  `mssr_rec_book_cno`
						WHERE user_id = $user_id
						AND book_on_shelf_state = '上架'
						ORDER BY  `keyin_mdate` DESC
						LIMIT ".$read_on." , 1";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,"",$arry_conn_mssr);
		$array["book_sid"] = $book_sid = $retrun[0]["book_sid"];
	//-------------------------------------------
	//資料存在查詢
	//-------------------------------------------




		//FTP 路徑
		$ftp_root="public_html/mssr/info/user";
		$ftp_path_record="{$ftp_root}/{$user_id}/book/{$book_sid}/record";
		$ftp_path_bimg="{$ftp_root}/{$user_id}/book/{$book_sid}/draw/bimg";
		$http_path="http://".$arry_ftp1_info['host']."/mssr/info/user/{$user_id}/book/{$book_sid}";
		$file_list = array();

		//獲取檔案目錄(錄音)
		//連接 | 登入 FTP
		/*
		$ftp_conn  =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
		$ftp_login =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
		ftp_pasv($ftp_conn,TRUE);
		$arry_ftp_file=ftp_nlist($ftp_conn,$ftp_path_record);
		if(in_array($ftp_path_record."/1.wav",$arry_ftp_file))$file_list["r_w"]=$http_path."/record/1.wav";
		if(in_array($ftp_path_record."/1.mp3",$arry_ftp_file))$file_list["r_m"]=$http_path."/record/1.mp3";
		ftp_close($ftp_conn);*/

		//獲取檔案目錄(繪圖)
		//連接 | 登入 FTP
		$ftp_conn  =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
		$ftp_login =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
		ftp_pasv($ftp_conn,TRUE);
		$arry_ftp_file=ftp_nlist($ftp_conn,$ftp_path_bimg);
		if(in_array($ftp_path_bimg."/1.jpg",$arry_ftp_file))$file_list["d_1"]=$http_path."/draw/bimg/1.jpg";
		if(in_array($ftp_path_bimg."/upload_1.jpg",$arry_ftp_file))$file_list["du_1"]=$http_path."/draw/bimg/upload_1.jpg";
		if(in_array($ftp_path_bimg."/upload_2.jpg",$arry_ftp_file))$file_list["du_2"]=$http_path."/draw/bimg/upload_2.jpg";
		if(in_array($ftp_path_bimg."/upload_3.jpg",$arry_ftp_file))$file_list["du_3"]=$http_path."/draw/bimg/upload_3.jpg";
		ftp_close($ftp_conn);
	//-------------------------------------------
	//SQL
	//-------------------------------------------
		//搜尋該書籍名稱
		$array_select = array("book_name");

				$get_book_info=get_book_info($conn='',$array["book_sid"],$array_select,$arry_conn_mssr);
				$array["book_name"]=$get_book_info[0]['book_name'];

		//先搜尋推薦資料的有無
		$sql = "SELECT `rec_stat_cno`,`rec_draw_cno`,`rec_text_cno`,`rec_record_cno`
				FROM  `mssr_rec_book_cno`
				WHERE book_sid = '".$book_sid."'
				AND user_id = '".$user_id."'";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		//======================================搜尋此書是否在30天內有訂過 同一個人的書======================================
		$sql = "SELECT `booking_from`
				FROM `mssr_book_booking_log`
				WHERE `booking_from` = ".$_SESSION['uid']."
				AND `book_sid`  = '".$book_sid."'
				AND `booking_to` = '".$user_id."'
				AND `booking_edate` >= '$day30'
				AND `booking_state` = '完成交易'";
		$retrunday30 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		if(count($retrunday30))
		{
			$array["on_booking30"] = 1;
		}
		//======================================搜尋此書有無被按讚=============================================
		$sql = "SELECT `rec_score`,`rec_type`
			FROM `mssr_score_rec_log`
			WHERE take_from = ".$_SESSION['uid']."
			AND take_to = ".$user_id."
			AND book_sid = '".$book_sid."'
			";
		$retrun_score = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($retrun_score as $key2 => $val2)
		{
			if($val2["rec_score"]>0)$array['have_good_'.$val2["rec_type"]] = 1;
		}

		//======================================搜尋此書有按讚量=============================================
		 $sql = "SELECT count(1) as count,`rec_type`
			FROM `mssr_score_rec_log`
			WHERE take_to = ".$user_id."
			AND book_sid = '".$book_sid."'
			AND `rec_score` = 1
			GROUP BY `rec_type`
			";
		$retrun_score = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($retrun_score as $key2 => $val2)
		{
			$array['have_good_count_'.$val2["rec_type"]] = $val2["count"];
		}

		//======================================搜尋此書有無被訂閱=============================================
		$sql = "SELECT count(1) AS count
				FROM mssr_book_booking
				WHERE book_sid = '".$book_sid."'
				AND booking_to = '".$user_id."'
				AND booking_from = ".$_SESSION['uid'].";
				";
		$retrun_booking = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		$array["on_booking"] = $retrun_booking[0]["count"];

		//======================================搜尋4像推薦===================================================
		//搜尋評星
		if($retrun[0]["rec_stat_cno"] > 0)
		{
			$sql_s = "SELECT `rec_reason`,`rec_rank`
				FROM  `mssr_rec_book_star_log`
				WHERE book_sid = '".$book_sid."'
				AND user_id = '".$user_id."'
				ORDER BY  `keyin_cdate` DESC ";

			$retrun_s = db_result($conn_type='pdo',$conn_mssr,$sql_s,$arry_limit=array(0,1),$arry_conn_mssr);
			@$tmp = $retrun_s[0]['rec_reason'];
			$reason_count = 1;
			if($tmp[0]=="o")
			{
				$array["rec_reason_".$reason_count] = "內容很有趣";
				$reason_count++;
			}
			if($tmp[1]=="o")
			{
				$array["rec_reason_".$reason_count] = "封面畫插圖很好看";
				$reason_count++;
			}
			if($tmp[2]=="o")
			{
				$array["rec_reason_".$reason_count] = "內容輕鬆好讀";
				$reason_count++;
			}
			if($tmp[3]=="o")
			{
				$array["rec_reason_".$reason_count] = "內容很感人";
				$reason_count++;
			}
			if($tmp[4]=="o")
			{
				$array["rec_reason_".$reason_count] = "喜歡故事人物";
				$reason_count++;
			}
			if($tmp[5]=="o")
			{
				$array["rec_reason_".$reason_count] = "可以學到很多知識";
				$reason_count++;
			}
			if($tmp[6]=="o")
			{
				$array["rec_reason_".$reason_count] = "印象深刻";
				$reason_count++;
			}
			@$array["rec_rank"] = $retrun_s[0]['rec_rank'];
		}
		else
		{
			$array["rec_rank"] = 0;
			$array["rec_reason_1"] = "";
			$array["rec_reason_2"] = "";
		}
		//搜尋繪圖
		if($retrun[0]["rec_draw_cno"] > 0)
		{
			$sql_d = "SELECT `book_sid`
					  FROM  `mssr_rec_book_draw_log`
					  WHERE book_sid = '".$book_sid."'
					  AND user_id = '".$user_id."'
					  ORDER BY  `keyin_cdate` DESC ";

			$retrun_d = db_result($conn_type='pdo',$conn_mssr,$sql_d,$arry_limit=array(0,1),$arry_conn_mssr);
			/*@$filename = sprintf("../../../../info/user/%s/book/%s/draw/base64_img/1",$user_id,$retrun_d[0]["book_sid"]);
			if(file_exists($filename))
			{

				$array["rec_draw_link"] = "$filename";
				$array["rec_draw_type"] = "base64";
				$str = file_get_contents($filename);
				//check format and separate bgcolor and imgdata
				if(preg_match('/^rgba?\(\d+(,\s\d+)+\)data:image\/png;base64,/',$str))
				{
					$c = strpos($str,")") + 1;
					$bc = substr($str,0,$c);
					$data = substr($str,$c);
					$array["rec_draw_bc"] = $bc;
					$array["rec_draw_data"] = $data;
				}

			}
			else
			{
				@$filename = sprintf("../../../info/user/%s/book/%s/draw/bimg/1.jpg",$user_id,$retrun_d[0]["book_sid"]);
				if(file_exists("../".$filename))
				{
					$array["rec_draw_link"] = "$filename";
					$array["rec_draw_type"] = "bimg";
				}
				else
				{
					$array["rec_draw_type"] = "";
				}
			}*/

			//繪圖擷取上傳的3張圖像
			$array["rec_draw_link_list"] = array();
			$array["rec_draw_link_list"][1] = "img/im.png";
			$array["rec_draw_link_list"][2] = "img/im.png";
			$array["rec_draw_link_list"][3] = "img/im.png";

			$upload_cno = 0 ;
			if($file_list["d_1"])
			{
				$array["rec_draw_has_list"][0] = 1;
				$upload_cno++;
				$array["rec_draw_link_list"][$upload_cno]=$file_list["d_1"]."?t=".time();
			}
			for($i=1;$i <= 3;$i++){
				if($file_list["du_".$i])
				{	$upload_cno++;
					$array["rec_draw_has_list"][$i] = 1;
					$array["rec_draw_link_list"][$upload_cno]=$file_list["du_".$i]."?t=".time();

				}
			}
			$array["upload_cno"] = $upload_cno;



		}
		//搜尋文字
		if($retrun[0]["rec_text_cno"] > 0)
		{
			$sql_t = "SELECT `rec_content`
				FROM  `mssr_rec_book_text_log`
				WHERE book_sid = '".$book_sid."'
				AND user_id = '".$user_id."'
				ORDER BY  `keyin_cdate` DESC ";

			$retrun_t = db_result($conn_type='pdo',$conn_mssr,$sql_t,$arry_limit=array(0,1),$arry_conn_mssr);
			@$tmp=unserialize($retrun_t[0]["rec_content"]);
			@$array["rec_content_1"] = gzuncompress(base64_decode($tmp[0]));
			@$array["rec_content_2"] = gzuncompress(base64_decode($tmp[1]));
			@$array["rec_content_3"] = gzuncompress(base64_decode($tmp[2]));
		}
		//搜尋錄音
		if($retrun[0]["rec_record_cno"] > 0)
		{
			$sql_r = "SELECT `book_sid`,`rec_filename`
					FROM  `mssr_rec_book_record_log`
					WHERE book_sid = '".$book_sid."'
					AND user_id = '".$user_id."'
					ORDER BY  `keyin_cdate` DESC ";

			$retrun_r = db_result($conn_type='pdo',$conn_mssr,$sql_r,$arry_limit=array(0,1),$arry_conn_mssr);
			@$array["rec_record_book_sid"] = $retrun_r[0]["book_sid"];
			$array["rec_record_file"] = $retrun_r[0]["rec_filename"];

		}

		//搜尋推薦評分
		$sql = "
				SELECT  *
						FROM
						(
							SELECT DISTINCT comment_type,
								   keyin_cdate,
								   comment_score,
								   book_sid,
								   comment_content,
								   comment_public
							FROM   mssr_rec_comment_log
							WHERE comment_to = $user_id
							AND book_sid = '".$book_sid."'
							ORDER BY  `keyin_cdate` DESC
						)AS B
				GROUP BY B.book_sid,B.comment_type;

				";
		$retrun2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		$array["c_draw"] = 0;
		$array["c_record"] = 0;
		$array["c_text"] = 0;
		if(count($retrun2)>0)
		{
			foreach($retrun2 as $key2 => $val2)
			{
				if($val2["comment_type"] == "draw")$array["c_draw"] = $val2["comment_score"]-2;
				if($val2["comment_type"] == "record")$array["c_record"] = $val2["comment_score"]-2;
				if($val2["comment_type"] == "text")$array["c_text"] = $val2["comment_score"]-2;
			}
		}
		if($array["c_draw"]<0)$array["c_draw"] = 0;
		if($array["c_record"]<0)$array["c_record"] = 0;
		if($array["c_text"]<0)$array["c_text"] = 0;
		//print_r($array);
		echo json_encode($array,1);
		?>