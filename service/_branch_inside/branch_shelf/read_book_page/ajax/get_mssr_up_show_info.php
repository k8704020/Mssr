<?
//---------------------------------------------------
//書店 > 上架 > 觀閱 
//
//---------------------------------------------------

//---------------------------------------------------
//輸入 user_id,book_sid
//輸出 OK
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
				APP_ROOT.'lib/php/array/code'
				);
	func_load($funcs,true);
	

	//清除並停用BUFFER
	@ob_end_clean(); 
	
	//建立連線 user
	$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

//---------------------------------------------------
//END   設定與引用
//---------------------------------------------------		 


	
	
	
	$book_sid = $_POST['book_sid']; 
	$sid = $_POST['sid'];

	//先搜尋推薦資料的有無
	$sql = "SELECT `rec_stat_cno`,`rec_draw_cno`,`rec_text_cno`,`rec_record_cno`
			FROM  `mssr_rec_book_cno` 
			WHERE book_sid = '".$book_sid."'
			AND user_id = '".$sid."'";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	
	//資料初始化
	$array = array();
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
	$array["on_booking"] = 0;
	
	//======================================搜尋此書有無被訂閱=============================================
	$sql = "SELECT count(1) AS count
			FROM mssr_book_booking
			WHERE book_sid = '".$book_sid."' AND booking_to = '".$sid."' AND booking_from = ".$_SESSION['uid'].";
			";
	$retrun_booking = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	$array["on_booking"] = $retrun_booking[0]["count"];
	
	 //搜尋書籍名稱
	$array_select = array("book_name");
	$get_book_info=get_book_info($conn='',$book_sid,$array_select,$arry_conn_mssr);
	$array["book_name"]=$get_book_info[0]["book_name"];
	//======================================搜尋4像推薦===================================================
	//搜尋評星
	if($retrun[0]["rec_stat_cno"] > 0)
	{
		$sql_s = "SELECT `rec_reason`,`rec_rank`
			FROM  `mssr_rec_book_star_log` 
			WHERE book_sid = '".$book_sid."'
			AND user_id = '".$sid."'
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
	//搜尋繪圖
	if($retrun[0]["rec_draw_cno"] > 0)
	{
		$sql_d = "SELECT `book_sid`
				  FROM  `mssr_rec_book_draw_log` 
				  WHERE book_sid = '".$book_sid."'
				  AND user_id = '".$sid."'
				  ORDER BY  `keyin_cdate` DESC ";
			
		$retrun_d = db_result($conn_type='pdo',$conn_mssr,$sql_d,$arry_limit=array(0,1),$arry_conn_mssr);
		@$filename = sprintf("../../../../../info/user/%s/book/%s/draw/base64_img/1",$sid,$retrun_d[0]["book_sid"]);
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
			@$filename = sprintf("../../../../info/user/%s/book/%s/draw/bimg/1.jpg",$sid,$retrun_d[0]["book_sid"]);
			if(file_exists("../".$filename))
			{
				$array["rec_draw_link"] = "$filename";
				$array["rec_draw_type"] = "bimg";
			}
			else
			{
				$array["rec_draw_type"] = "";
			}
		}
	}
	//搜尋文字
	if($retrun[0]["rec_text_cno"] > 0)
	{
		$sql_t = "SELECT `rec_content`
			FROM  `mssr_rec_book_text_log` 
			WHERE book_sid = '".$book_sid."'
			AND user_id = '".$sid."'
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
		$sql_r = "SELECT `book_sid`
				FROM  `mssr_rec_book_record_log` 
				WHERE book_sid = '".$book_sid."'
				AND user_id = '".$sid."'
				ORDER BY  `keyin_cdate` DESC ";
			
		$retrun_r = db_result($conn_type='pdo',$conn_mssr,$sql_r,$arry_limit=array(0,1),$arry_conn_mssr);
		@$array["rec_record_book_sid"] = $retrun_r[0]["book_sid"];
	}
	//print_r($array);
	echo json_encode($array,1);
	
	
	
	
	
	
?>