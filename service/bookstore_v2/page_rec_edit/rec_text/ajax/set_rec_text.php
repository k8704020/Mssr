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
	require_once(str_repeat("../",3)."/inc/mssr_rec_book_text_sid/code.php");
	require_once(str_repeat("../",3)."/inc/tx_sys_sid/code.php");
	require_once(str_repeat("../",3)."/inc/set_score_exp/code.php");

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
		$array["text"] = "";
		$array["coin"] = 0;
		$array["exp_score"] = 0;
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------
		$dadad = date("Y-m-d  H:i:s");
        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;		
		
		if($user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}
		
		//檢查資料正確性
		$uid = mysql_prep($_POST["user_id"]);
		$time = (int)($_POST["time"]);
		$auth_coin_open = $_POST['auth_coin_open']=='yes'?true:false;
		$book_id = mysql_prep($_POST["book_sid"]);
		
		$rec_text_ans1 =  mysql_prep(base64_encode(gzcompress(trim($_POST["rec_text_ans1"]))));
		$rec_text_ans2 =  mysql_prep(base64_encode(gzcompress(trim($_POST["rec_text_ans2"]))));
		$rec_text_ans3 =  mysql_prep(base64_encode(gzcompress(trim($_POST["rec_text_ans3"]))));
		
		if($uid == 0 || $book_id == NULL || $book_id == "")
		{
			$array["error"] ="錯誤!!  請重新登入";
			die(json_encode($array,1));
		}
	//-------------------------------------------
	//SQL
	//-------------------------------------------
	
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
					SET rec_text_cno = rec_text_cno+1  , keyin_mdate = '".$dadad."' ,`rec_state` = 1, `keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
					WHERE book_sid = '".$book_id."'
					AND   user_id = '".$uid."' 
					;";
			//判斷學期資料是否存在
			if($retrun_semester[0]['count'] >= 1)
			{			
				$sql = $sql."UPDATE mssr_rec_book_cno_semester
						SET rec_text_cno = rec_text_cno+1 , keyin_mdate = '".$dadad."', `rec_state` = 1,`keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
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
						SET rec_text_cno = rec_text_cno+1 , keyin_mdate = '".$dadad."',`rec_state` = 1 , `keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
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
						'0',
						'0',
						'1',
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
						'0',
						'0',
						'1',
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
						'0',
						'0',
						'1',
						'0',
						'否',
						'未動作',
						'".$dadad."',
						'".$dadad."',
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
		
		//無給錢狀況 + 教師要給開放
		else if((@$retrun_rec_reward[0]['rec_reward'] == "無" || sizeof($retrun_rec_reward) == 0)&&$auth_coin_open)
		{
			//判斷是否到達給金錢標標準
			if($time > 20)//作畫時間大於15秒
			{
	
				$coin =100;//給予的金錢數
				
				$exp_score = 100;//獲得的經驗數
				
				$sql = $sql."UPDATE `mssr`.`mssr_user_info` 
							SET `user_coin` = `user_coin`+".$coin." 
							WHERE `mssr_user_info`.`user_id` = ".$uid."
							;";
				$tx_sys_sid = tx_sys_sid($uid,mb_internal_encoding());
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
								'".$dadad."',
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
								'".$dadad."',
								'".$_SERVER["REMOTE_ADDR"]."');";
				
				//==============經驗值獲得==================
				//獲得類型
				$exp_type="rec_text";
				
				
				$RE = set_score_exp($conn='',$exp_type,$exp_score,$uid,$arry_conn_mssr);
			
			}
			else
			{
				$rec_reward = "無";
				$array["text"]="要認真做才有獎勵喔";
			}
		}
	
		$rec_d_sid = mssr_rec_book_text_sid($uid,mb_internal_encoding());
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
						  '".$dadad."',
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
						  '".$dadad."',
						  '".$_SERVER["REMOTE_ADDR"]."');
						  
		";
		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		
		$array["coin"] = $coin ;
		$array["exp_score"] = $exp_score ;
		if($coin > 0)$array["text"]="文字 推薦獎勵:".$coin;
		//回傳增加的金錢
		
		
		
		echo json_encode($array,1);
		?>