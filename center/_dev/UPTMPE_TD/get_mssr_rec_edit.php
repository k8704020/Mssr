<?

//---------------------------------------------------
//設定與引用
//---------------------------------------------------

	//SESSION
	@session_start();

	//啟用BUFFER
	@ob_start();

	//外掛設定檔
	require_once(str_repeat("../",3)."/config/config.php");
	require_once(str_repeat("../",3)."/inc/get_book_info/code.php");

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


	
	
	
	$book_sid = $_POST['book_sid'] ;
	$user_id = $_POST['sid'] ;
	//=======初始化========	
	$array = array();
	//評星
	$array['rec_star_rank']="-1";
	$array['rec_star_reason']="xxxxxx";
	//繪圖
	$array['rec_draw']="-1";
	//文字
	$array['rec_text_content_1']="";
	$array['rec_text_content_2']="";
	$array['rec_text_content_3']="";
	//錄音
	$array['rec_record_operate_time']="-1";


	
	//=======讀取資料=====
	//評星
	$sql = "SELECT rec_rank, rec_reason
			FROM  `mssr_rec_book_star_log` 
			WHERE  `user_id` LIKE  '".$user_id."'
			AND  `book_sid` LIKE  '".$book_sid."'
			ORDER BY  `mssr_rec_book_star_log`.`keyin_cdate` DESC ";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	foreach($retrun as $key1=>$val1)
	{	
		$array['rec_star_rank'] = mysql_real_escape_string($val1['rec_rank']);
		$array['rec_star_reason'] = mysql_real_escape_string($val1['rec_reason']);
	}
	
	//繪圖
	$sql = "SELECT count(1) AS count
			FROM  `mssr_rec_book_draw_log` 
			WHERE  `user_id` LIKE  '".$user_id."'
			AND  `book_sid` LIKE  '".$book_sid."'
			ORDER BY  `mssr_rec_book_draw_log`.`keyin_cdate` DESC ";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	foreach($retrun as $key1=>$val1)
	{	
		if($val1['count'] >0)$array['rec_draw']="1";
	}
	
	//文字
	$sql = "SELECT rec_content
			FROM  `mssr_rec_book_text_log` 
			WHERE  `user_id` LIKE  '".$user_id."'
			AND  `book_sid` LIKE  '".$book_sid."'
			ORDER BY  `mssr_rec_book_text_log`.`keyin_cdate` DESC ";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	foreach($retrun as $key1=>$val1)
	{	
	 $content=$val1['rec_content'];
	  $tmp = unserialize($content);
	  $array['rec_text_content_1'] = unserialize(gzuncompress(base64_decode($tmp[0])));
	  $array['rec_text_content_2'] = unserialize(gzuncompress(base64_decode($tmp[1])));
	  $array['rec_text_content_3'] = unserialize(gzuncompress(base64_decode($tmp[2])));
	}
	
	//錄音
	$sql = "SELECT rec_operate_time
			FROM  `mssr_rec_book_record_log` 
			WHERE  `user_id` LIKE  '".$user_id."'
			AND  `book_sid` LIKE  '".$book_sid."'
			ORDER BY  `mssr_rec_book_record_log`.`keyin_cdate` DESC ";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	foreach($retrun as $key1=>$val1)
	{	
	  $array['rec_record_operate_time']=mysql_real_escape_string($val1['rec_operate_time']);
	}
	
	
	echo json_encode($array,1);
	
	
	
	
	
	
?>