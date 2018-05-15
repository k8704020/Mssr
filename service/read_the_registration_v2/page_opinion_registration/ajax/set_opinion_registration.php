<?
//-------------------------------------------------------
//版本編號 1.0
//登記書籍  寫入回答問題的答案
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
	require_once(str_repeat("../",3)."/bookstore_v2/inc/set_score_exp/code.php");
	require_once(str_repeat("../",2)."/inc/tx_sys_sid/code.php");
	require_once(str_repeat("../",2)."/inc/tx_gift_sid/code.php");
	require_once(str_repeat("../",2)."/inc/set_brench_cs_filter/code.php");

	//外掛函式檔
	$funcs=array(
				APP_ROOT.'inc/conn/code',
				APP_ROOT.'lib/php/db/code'
				);
	func_load($funcs,true);
	

	//清除並停用BUFFER
	@ob_end_clean();
	
	//建立連線 user
	$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
	$conn_user=conn($db_type='mysql',$arry_conn_user);
	
	//-----------------------------------------------
	//通用
	//-----------------------------------------------
	
	//-------------------------------------------
	//初始化, curl設定
	//-------------------------------------------
		$array =array();
		$array["error"] = "";
		$array["echo"] ="";	
		$array["coin"] = 0;
        //POST

		$dadad = date("Y-m-d  H:i:s");
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------
		
		//POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
		$auth_coin_open = $_POST['auth_coin_open']=='yes'?true:false;

		$borrow_sid = mysql_prep($_POST['borrow_sid']);
		$book_sid = mysql_prep($_POST['book_sid']);
		
		if($user_id != $_SESSION["uid"] || $user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}
		if($borrow_sid == ""  || $book_sid == "" || strlen($book_sid)!=25)
		{
			$array["echo"] ="資料有點怪怪的喔！　請重新輸入<BR>CODE : 001";
			die(json_encode($array,1));
		}
		
		$quest_ans_1 = (int)($_POST['quest_ans_1']);
		$quest_topic_id_1 = (int)($_POST['quest_topic_id_1']);
		 
		$quest_ans_2 =  (int)($_POST['quest_ans_2']);
		$quest_topic_id_2 =  (int)($_POST['quest_topic_id_2']);
		 
		$quest_ans_3 =  (int)($_POST['quest_ans_3']);
		$quest_topic_id_3 =  (int)($_POST['quest_topic_id_3']);
		
		$quest_ans_4 =  (int)($_POST['quest_ans_4']);
		$quest_topic_id_4 =  (int)($_POST['quest_topic_id_4']);
	
		$quest_ans_5 =  (int)($_POST['quest_ans_5']);
		$quest_topic_id_5 =  (int)($_POST['quest_topic_id_5']);
		
		$quest_ans_6 =  (int)($_POST['quest_ans_6']);
		$quest_topic_id_6 =  (int)($_POST['quest_topic_id_6']);
		
		$opinion_answer=array
		(
			0=>array(
				'topic_id'      =>$_POST['quest_topic_id_1'],
				'opinion_answer'=>array(
									$_POST['quest_ans_1']
								)
			),
			1=>array(
				'topic_id'      =>$_POST['quest_topic_id_2'],
				'opinion_answer'=>array(
									$_POST['quest_ans_2']
								)
			),
			2=>array(
				'topic_id'      =>$_POST['quest_topic_id_3'],
				'opinion_answer'=>array(
									$_POST['quest_ans_3']
								)
			),
			3=>array(
				'topic_id'      =>$_POST['quest_topic_id_4'],
				'opinion_answer'=>array(
									$_POST['quest_ans_4']
								)
			),
			4=>array(
				'topic_id'      =>$_POST['quest_topic_id_5'],
				'opinion_answer'=>array(
									$_POST['quest_ans_5']
								)
			),
			5=>array(
				'topic_id'      =>$_POST['quest_topic_id_6'],
				'opinion_answer'=>array(
									$_POST['quest_ans_6']
								)
			),
		);
		$answer = serialize($opinion_answer);
		
	//-------------------------------------------
	//SQL
	//-------------------------------------------
	
		//======先搜尋有無借閱資訊(防造假===========
		$sql = "SELECT count(1) AS count ,borrow_sdate
				FROM  `mssr_book_borrow_log` 
				WHERE  `borrow_sid` LIKE  '{$borrow_sid}'
				AND  `book_sid` LIKE  '{$book_sid}'
				AND  `user_id` = '{$user_id}'";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		if($retrun[0]['count'] ==0)
		{	
			$array["echo"] ="資料有點怪怪的喔！　請重新輸入<BR>CODE : 002";
			die(json_encode($array,1));
		}
		
		//=====確認CNO是否已有此書籍的借閱資訊=======
		$sql = "SELECT count(1) AS count
				FROM  `mssr_book_read_opinion_cno`
				WHERE  book_sid = '{$book_sid}'
				AND	   user_id  = '{$user_id}'";
		$retrun_cno = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		if($retrun_cno[0]['count'] == 0 )
		{
			//無  新增cno
			$sql = "INSERT INTO `mssr`.`mssr_book_read_opinion_cno` 
					(
						`user_id`,
						`book_sid`,
						`opinion_cno`
					)
					VALUES
					(
						'{$user_id}',
						'{$book_sid}',
						'1'
					);";
		
		}
		else
		{
			//有  追加cno次數
			$sql = "UPDATE `mssr`.`mssr_book_read_opinion_cno` 
					SET opinion_cno = opinion_cno+1
					WHERE 
						book_sid = '{$book_sid}' and 
						user_id = '{$user_id}'
					;";
		}
		
		//=====寫入opinion AND opinion_LOG==========
		$sql = $sql."
				INSERT INTO  `mssr`.`mssr_book_read_opinion` 
				(
					`user_id` ,
					`book_sid` ,
					`borrow_sid` ,
					`borrow_sdate` ,
					`opinion_answer` ,
					`keyin_cdate` ,
					`keyin_ip`
				)
				VALUES (
					'{$user_id}',
					'{$book_sid}',
					'{$borrow_sid}',
					'".$retrun[0]['borrow_sdate']."',
					'{$answer}',
					'".$dadad."',
					'".$_SERVER["REMOTE_ADDR"]."'
				);
				INSERT INTO  `mssr`.`mssr_book_read_opinion_log` 
				(
					`user_id` ,
					`book_sid` ,
					`borrow_sid` ,
					`borrow_sdate` ,
					`opinion_answer` ,
					`keyin_cdate` ,
					`keyin_ip`
				)
				VALUES (
					'{$user_id}',
					'{$book_sid}',
					'{$borrow_sid}',
					'".$retrun[0]['borrow_sdate']."',
					'{$answer}',
					'".$dadad."',
					'".$_SERVER["REMOTE_ADDR"]."'
				);";
				
		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	
		//=====給予CS 滿意度====================
		//set_brench_cs_filter($conn_mssr,$book_sid,$array_filter=array(),$arry_conn_mssr,$user_id);
		
		
		//=====判斷有無訂閱他人書籍=============== 
		$sql = "SELECT booking_to,keyin_cdate
				FROM  `mssr_book_booking`
				WHERE booking_from = '{$user_id}' AND book_sid = '{$book_sid}'";
		$re_booking = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);	
		if(sizeof($re_booking)>0)
		{//有書籍
			$give_coin = 0;
			if($auth_coin_open)$array["coin"] = 100;
			if($auth_coin_open)$give_coin = 300;
			$buy_exp = 100;
			$sell_exp = 300;
			//取得學生資料
			

			$sql_user = "
					SELECT user_coin,box_item,map_item
					FROM  `mssr_user_info` 
					WHERE user_id = '".$user_id."'";
			$mssr_user_info = db_result($conn_type='pdo',$conn_mssr,$sql_user,$arry_limit=array(0,1),$arry_conn_mssr);

			//獎勵獲得
			if($auth_coin_open){$sql = "UPDATE `mssr`.`mssr_user_info` 
						SET `user_coin` = `user_coin`+".$array["coin"]." 
						WHERE `mssr_user_info`.`user_id` = ".$user_id."
						;";}
			//建立訂閱LOG
			if($auth_coin_open)
			{$sql = $sql."INSERT INTO `mssr`.`mssr_book_booking_log`
					(
						`booking_from`,
						`booking_to`,
						`book_sid`,
						`booking_state`,
						`booking_sdate`,
						`booking_edate`,
						`keyin_ip`
					)VALUES(
						'".$user_id."',
						'".$re_booking[0]["booking_to"]."',
						'".$book_sid."',
						'完成交易',
						'".$re_booking[0]['keyin_cdate']."',
						'".$dadad."',
						'".$_SERVER["REMOTE_ADDR"]."');";}
			$tx_sys_sid = tx_sys_sid(1,mb_internal_encoding());
			//建立系統交易LOG
			if($auth_coin_open){$sql = $sql."INSERT INTO `mssr`.`mssr_tx_sys_log` (
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
							'".$user_id."',
							'".$user_id."',
							'".$tx_sys_sid."',
							'',
							'".$array["coin"]."',
							'正常',
							'',
							'".$dadad."',
							'".$_SERVER["REMOTE_ADDR"]."'
							);";}
			if($auth_coin_open){$sql = $sql."INSERT INTO `mssr`.`mssr_user_item_log` (
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
							'".$user_id."',
							'".$user_id."',
							'".$tx_sys_sid."',
							'buy_book',
							'".$mssr_user_info[0]['map_item']."',
							'".$mssr_user_info[0]['box_item']."',
							'".((int)$mssr_user_info[0]['user_coin']+$array["coin"])."',
							'正常',
							'',
							'".$dadad."',
							'".$_SERVER["REMOTE_ADDR"]."');";
							
			db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);}
			

			//刪除暫存訂閱資訊
			$sql = "DELETE 
					FROM mssr_book_booking
					WHERE book_sid = '".$book_sid."' AND booking_to = '".$re_booking[0]["booking_to"]."' and booking_from = '".$user_id."';
					";
			db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);	
			
			//給予對方金錢訊息
			$get_book_info = get_book_info($conn='',$book_sid,$array_select = array('book_name'),$arry_conn_mssr);
			$sql = "INSERT INTO `mssr_msg_log`
					(
						`user_id`,
						`from_id`,
						`log_text`,
						`log_state`,
						`keyin_cdate`,
						`keyin_mdate`
					) VALUES (
						".$re_booking[0]["booking_to"].",
						".$user_id.",
						'".$_SESSION['name']." 購買了你的「".$get_book_info[0]['book_name']."」',
						'1',
						'".$dadad."',
						'".$dadad."'
					)";
			db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
			
			//寫入禮物籃
			$sql = "SELECT log_id
					FROM  `mssr_msg_log` 
					WHERE  `from_id` = '".$_SESSION['uid']."'
					ORDER BY  `mssr_msg_log`.`keyin_cdate` DESC ";
			$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
			$log_id = $retrun[0]['log_id'];
			$tx_gift_sid = tx_gift_sid(1,mb_internal_encoding());
			
			$sql = "INSERT INTO `mssr_tx_gift_log`
					(
						 `edit_by`,
						 `msg_id`,
						 `tx_from`,
						 `tx_to`,
						 `tx_sid`,
						 `tx_coin`,
						 `tx_state`,
						 `keyin_cdate`,
						 `keyin_mdate`,
						 `keyin_ip`
					) VALUES (
							'".$_SESSION['uid']."',
							'$log_id',
							'".$_SESSION['uid']."',
							".$re_booking[0]["booking_to"].",
							'$tx_gift_sid',
							$give_coin,
							'未領取',
							'".$dadad."',
							'".$dadad."',
							'".$_SERVER["REMOTE_ADDR"]."'
					);";
			  $sql = $sql . "UPDATE mssr_msg_log
							 SET log_state = '1'
							 WHERE log_id = '".$log_id."'
						";
			  $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
			

			  $exp_type="booking_buy";
			  $RE = set_score_exp($conn='',$exp_type,$buy_exp,$user_id,$arry_conn_mssr,$re_booking[0]["booking_to"]);
			  //$$$擬定了對方  所以是對方付你

			  $exp_type="booking_sell";
			  $RE = set_score_exp($conn='',$exp_type,$sell_exp,$re_booking[0]["booking_to"],$arry_conn_mssr,$user_id);
			  
			  
		}
		
		echo json_encode($array,1);
		
		
		?>