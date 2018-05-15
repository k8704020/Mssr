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
	require_once(str_repeat("../",1)."/mssr_rec_book/mssr_rec_book_text_sid/code.php");
	require_once(str_repeat("../",1)."/inc/tx_sys_sid/code.php");
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

		//回傳數值
		$coin = 0;//金錢
		
		//檢查資料正確性
		$rec_text_ans1 = mysql_prep(base64_encode(gzcompress(serialize($_POST["rec_text_ans1"]))));
		$rec_text_ans2 = mysql_prep(base64_encode(gzcompress(serialize($_POST["rec_text_ans2"]))));
		$rec_text_ans3 = mysql_prep(base64_encode(gzcompress(serialize($_POST["rec_text_ans3"]))));
		$uid = mysql_prep($_POST["sid"]);
		$time = mysql_prep($_POST["time"]);
		$book_id = mysql_prep($_POST["book_id"]);
		if($uid == 0)die();
		
		//序列化-文字陣列
		$array_text = array($rec_text_ans1,$rec_text_ans2,$rec_text_ans3);
		$text = serialize($array_text);
		
		//先搜尋有無做過推薦
		$sql = "
				SELECT count(1) AS count
				FROM  `mssr_rec_book_cno` 
				WHERE user_id = '".$uid."'
				AND book_sid = '".$book_id."'";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		
		
		//搜尋推薦有無增加過獎勵
		$sql = "
				SELECT rec_reward
				FROM  `mssr_rec_book_text_log` 
				WHERE user_id = '".$uid."'
				AND book_sid = '".$book_id."'
				ORDER BY `keyin_cdate` DESC ";
		$retrun_rec_reward = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		
		
		$sql = "";
		//確認有無統計數
		if($retrun[0]['count'] >= 1)
		{
			$sql = "UPDATE mssr_rec_book_cno
					SET rec_text_cno = rec_text_cno+1  , keyin_mdate = NOW() , `keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
					WHERE book_sid = '".$book_id."'
					AND   user_id = '".$uid."' 
					;";
			
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
						'0',
						'0',
						'1',
						'0',
						'否',
						'未動作',
						NOW(),
						NOW(),
						'".$_SERVER["REMOTE_ADDR"]."'
						);";
		}
		
		//=====================確認是否得到金錢============================
		$rec_reward = "無";
		//已有給錢的狀況
		if(@$retrun_rec_reward[0]['rec_reward'] == "有")
		{
			$rec_reward = "有";
		}
		
		//無給錢狀況
		else if(@$retrun_rec_reward[0]['rec_reward'] == "無" || sizeof($retrun_rec_reward) == 0)
		{
			//判斷是否到達給金錢標標準
			if($time > 20)//作畫時間大於15秒
			{
				
						
				$coin =100;//給予的金錢數
				$sql = $sql."UPDATE `mssr`.`mssr_user_info` 
							SET `user_coin` = `user_coin`+".$coin." 
							WHERE `mssr_user_info`.`user_id` = ".$uid."
							;";
				$tx_sys_sid = tx_sys_sid(1,mb_internal_encoding());
				$rec_reward = "有";
				
				
				//===========寫入系統交易LOG  以及學生物品經前LOG=============================
				//取得學生資料
				$sql_user = "
						SELECT user_coin,box_item,map_item
						FROM  `mssr_user_info` 
						WHERE user_id = '".$uid."'";
				$mssr_user_info = db_result($conn_type='pdo',$conn_mssr,$sql_user,$arry_limit=array(0,1),$arry_conn_mssr);
				
				//系統交易LOG
				$sql = $sql."INSERT INTO `mssr`.`mssr_tx_sys_log` (
								`edit_by`,
								`user_id`,
								`tx_sid`,
								`tx_item`,
								`tx_coin`,
								`tx_state`,
								`tx_note`,
								`keyin_cdate`,
								`keyin_ip`
							)VALUES(
								'".$uid."',
								'".$uid."',
								'".$tx_sys_sid."',
								'',
								'".$coin."',
								'正常',
								'',
								NOW(),
								'".$_SERVER["REMOTE_ADDR"]."'
								);";
								
				//USER物品金錢LOG
				$sql = $sql."INSERT INTO `mssr`.`mssr_user_item_log` (
								`edit_by`,
								`user_id`,
								`tx_sid`,
								`tx_type`,
								`map_item`,
								`box_item`,
								`user_coin`,
								`log_state`,
								`log_note`,
								`keyin_cdate`,
								`keyin_ip`
							)VALUES(
								'".$uid."',
								'".$uid."',
								'".$tx_sys_sid."',
								'book_text',
								'".$mssr_user_info[0]['map_item']."',
								'".$mssr_user_info[0]['box_item']."',
								'".((int)$mssr_user_info[0]['user_coin']+$coin)."',
								'正常',
								'',
								NOW(),
								'".$_SERVER["REMOTE_ADDR"]."');";
				
			}
			else
			{
				$rec_reward = "無";
			}
		}
	
		
		
		
		
		$rec_d_sid = mssr_rec_book_text_sid(1,mb_internal_encoding());
		$sql = $sql."INSERT INTO `mssr`.`mssr_rec_book_text_log` (
						  `user_id`,
						  `book_sid`,
						  `rec_sid`,
						  `rec_operate_time`,
						  `rec_reward`,
						  `rec_content`,
						  `rec_state`,
						  `keyin_cdate`,
						  `keyin_ip`
					  )VALUES(
					  	  '".$uid."',
						  '".$book_id."',
						  '".$rec_d_sid."',
						  '".$time."',
						  '".$rec_reward ."',
						  '".$text."',
						  '顯示',
						  NOW(),
						  '".$_SERVER["REMOTE_ADDR"]."');
						  
		";
		$sql = $sql."INSERT INTO `mssr`.`mssr_rec_book_text` (
						  `user_id`,
						  `book_sid`,
						  `rec_sid`,
						  `rec_operate_time`,
						  `rec_reward`,
						  `rec_content`,
						  `rec_state`,
						  `keyin_cdate`,
						  `keyin_ip`
					  )VALUES(
					  	  '".$uid."',
						  '".$book_id."',
						  '".$rec_d_sid."',
						  '".$time."',
						  '".$rec_reward ."',
						  '".$text."',
						  '顯示',
						  NOW(),
						  '".$_SERVER["REMOTE_ADDR"]."');
						  
		";
		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		
		//回傳增加的金錢
		echo $coin ;

?>