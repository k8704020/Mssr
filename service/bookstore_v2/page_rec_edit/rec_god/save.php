<?php
//-------------------------------------------------------
//明日星球,總店 -> 繪圖推薦上傳功能
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",4).'config/config.php');
		require_once(str_repeat("../",2)."/inc/mssr_rec_book_record_sid/code.php");
		require_once(str_repeat("../",2)."/inc/set_score_exp/code.php");
		require_once(str_repeat("../",2)."/inc/tx_sys_sid/code.php");
        //外掛函式檔
		
        $funcs=array(
                    APP_ROOT.'inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/fso/code',
                    APP_ROOT.'lib/php/upload/file_upload_save/code',
                    APP_ROOT.'lib/php/image/phpthumb/thumb_imagebysize'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

 	//---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
	//---------------------------------------------------
    //初始參數
    //---------------------------------------------------
	$retrun_data = array();
	$coin = 0;
	$retrun_data['exp_score'] = 0;
	$retrun_data['coin'] = 0;
	$retrun_data['error'] = '';
	$retrun_data["text"] = "";
	$retrun_data["echo"] = "";
    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
	//auth_coin_open 教師是否開放賺錢
    //datetime  現在時間
    //user_id   使用者主索引
    //book_sid  書籍識別碼
    //time      錄音時間
	$auth_coin_open = $_POST['auth_coin_open']=='yes'?true:false;
	$datetime = date("Y-m-d  H:i:s");
	$user_id = ($_POST["user_id"] == $_SESSION['uid'])?(int)$_SESSION['uid']:0;
	$book_sid = mysql_prep($_POST["book_sid"]);
	$time = (int)$_POST["time"];		
	//-----------------------------------------------
	//通用
	//-----------------------------------------------

		//建立連線 mssr
		$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

	//-----------------------------------------------
	//檢核
	//-----------------------------------------------
	if($user_id ==0)
	{
		die();
	}
	
	$get_book_info=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_id'),$arry_conn_mssr);
    if(empty($get_book_info)){
    	die();
    }
	//-----------------------------------------------
    //SQL處理
    //-----------------------------------------------
		//先搜尋有無做過推薦
			$sql = "
					SELECT count(1) AS count
					FROM  `mssr_rec_book_cno` 
					WHERE user_id = '".$user_id."'
					AND book_sid = '".$book_sid."'";
			$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
			//搜尋學期資料
			$sql = "
					SELECT count(1) AS count
					FROM  `mssr_rec_book_cno_semester` 
					WHERE user_id = '".$user_id."'
					AND book_sid = '".$book_sid."'";
			$retrun_semester  = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
			//搜尋一周資料
			$sql = "
					SELECT count(1) AS count
					FROM  `mssr_rec_book_cno_one_week` 
					WHERE user_id = '".$user_id."'
					AND book_sid = '".$book_sid."'";
			$retrun_week = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
			//搜尋推薦有無增加過獎勵
			$sql = "
					SELECT rec_reward
					FROM  `mssr_rec_book_record_log` 
					WHERE user_id = '".$user_id."'
					AND book_sid = '".$book_sid."'
					ORDER BY `keyin_cdate` DESC ";
			$retrun_rec_reward = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
			
			$sql = "";
			//確認有無統計數
			if($retrun[0]['count'] >= 1)
			{
				$sql = "UPDATE mssr_rec_book_cno
						SET rec_record_cno = rec_record_cno+1 , keyin_mdate = '".$datetime."',`rec_state` = 1, `keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
						WHERE book_sid = '".$book_sid."'
						AND   user_id = '".$user_id."';
						";
				//判斷學期資料是否存在
				if($retrun_semester[0]['count'] >= 1)
				{			
					$sql = $sql."UPDATE mssr_rec_book_cno_semester
							SET rec_record_cno = rec_record_cno+1 , keyin_mdate = '".$datetime."',`rec_state` = 1, `keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
							WHERE book_sid = '".$book_sid."'
							AND   user_id = '".$user_id."';";
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
									WHERE book_sid = '".$book_sid."'
									AND   user_id = '".$user_id."' 
									;";
				}
				if($retrun_week[0]['count'] >= 1)
				{			
					$sql = $sql."UPDATE mssr_rec_book_cno_one_week
							SET rec_record_cno = rec_record_cno+1 , keyin_mdate ='".$datetime."',`rec_state` = 1, `keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
							WHERE book_sid = '".$book_sid."'
							AND   user_id = '".$user_id."' 
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
									WHERE book_sid = '".$book_sid."'
									AND   user_id = '".$user_id."';";
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
							'".$user_id."',
							'".$user_id."',
							'".$book_sid."',
							'0',
							'0',
							'0',
							'1',
							'否',
							'未動作',
							'".$datetime."',
							'".$datetime."',
							'".$_SERVER["REMOTE_ADDR"]."');
							
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
							'".$user_id."',
							'".$user_id."',
							'".$book_sid."',
							'0',
							'0',
							'0',
							'1',
							'否',
							'未動作',
							'".$datetime."',
							'".$datetime."',
							'".$_SERVER["REMOTE_ADDR"]."');
							
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
							'".$user_id."',
							'".$user_id."',
							'".$book_sid."',
							'0',
							'0',
							'0',
							'1',
							'否',
							'未動作',
							'".$datetime."',
							'".$datetime."',
							'".$_SERVER["REMOTE_ADDR"]."');";
			}
			
			//=====================確認是否得到金錢============================
			$rec_reward = "無";
			//已有給錢的狀況
			
			if(@$retrun_rec_reward[0]['rec_reward'] == "有")
			{
				$rec_reward = "有";
			}			
			//無給錢狀
			else if((@$retrun_rec_reward[0]['rec_reward'] == "無" || sizeof($retrun_rec_reward) == 0)&&$auth_coin_open)
			{
				//判斷是否到達給金錢標標準
				if($time > 10)//作畫時間大於15秒
				{
					
					//給予CS 滿意度
					//set_brench_cs_filter($conn_mssr,$book_id,$array_filter=array(),$arry_conn_mssr);
					
					$exp_score = 100;//獲得的經驗數
					$coin =100;//給予的金錢數
					$sql = $sql."UPDATE `mssr`.`mssr_user_info` 
								SET `user_coin` = `user_coin`+".$coin." 
								WHERE `mssr_user_info`.`user_id` = ".$user_id.";";
					
					$tx_sys_sid = tx_sys_sid($user_id,mb_internal_encoding());
					$rec_reward = "有";
					
					
					//===========寫入系統交易LOG  以及學生物品經前LOG=============================
					//取得學生資料
					$sql_user = "
							SELECT user_coin,box_item,map_item
							FROM  `mssr_user_info` 
							WHERE user_id = '".$user_id."'";
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
									'".$user_id."',
									'".$user_id."',
									'".$tx_sys_sid."',
									'',
									'".$coin."',
									'正常',
									'',
									'".$datetime."',
									'".$_SERVER["REMOTE_ADDR"]."');";
									
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
									'".$user_id."',
									'".$user_id."',
									'".$tx_sys_sid."',
									'book_recode',
									'".$mssr_user_info[0]['map_item']."',
									'".$mssr_user_info[0]['box_item']."',
									'".((int)$mssr_user_info[0]['user_coin']+$coin)."',
									'正常',
									'',
									'".$datetime."',
									'".$_SERVER["REMOTE_ADDR"]."');";
				
					//獲得類型
					$exp_type="rec_recode";					
					set_score_exp($conn='',$exp_type,$exp_score,$user_id,$arry_conn_mssr);
				}
				else
				{
					$rec_reward = "無";
					$retrun_data["text"]="要認真做才有獎勵喔";
				}
			}
			
			$rec_d_sid = mssr_rec_book_record_sid($user_id,mb_internal_encoding());
			$sql = $sql."INSERT INTO  `mssr`.`mssr_rec_book_record_log` (
										`user_id` ,
										`book_sid` ,
										`rec_sid` ,
										`rec_time` ,
										`rec_operate_time` ,
										`rec_reward` ,
										`rec_state` ,
										`rec_filename`,
										`keyin_cdate` ,
										`keyin_ip`
										)
										VALUES (
										'".$user_id."',
										'".$book_sid."',
										'".$rec_d_sid."',
										'".$time."',
										'".$time."',
										'".$rec_reward."',
										'顯示',
										'".$_POST["filename"]."',
										'".$datetime."',
										'".$_SERVER["REMOTE_ADDR"]."'
										);
							  
			";
			$sql = $sql."INSERT INTO  `mssr`.`mssr_rec_book_record` (
										`user_id` ,
										`book_sid` ,
										`rec_sid` ,
										`rec_time` ,
										`rec_operate_time` ,
										`rec_reward` ,
										`rec_state` ,
										`rec_filename`,
										`keyin_cdate` ,
										`keyin_ip`
										)
										VALUES (
										'".$user_id."',
										'".$book_sid."',
										'".$rec_d_sid."',
										'".$time."',
										'".$time."',
										'".$rec_reward."',
										'顯示',
										'".$_POST["filename"]."',
										'".$datetime."',
										'".$_SERVER["REMOTE_ADDR"]."'
										);
							  
			";
			
			db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		
		$retrun_data['coin'] = $coin;
		$retrun_data['exp_score'] = $exp_score;
		if($coin > 0)$retrun_data["text"]="錄音 推薦獎勵:".$coin;
	
	
	
	
	
	
	//---------------------------------------------------
    //上傳處理
    //---------------------------------------------------
		//目錄檢測
        $root=str_repeat("../",4)."info/user/".(int)$user_id."/book/{$book_sid}";
        if(!is_dir("{$root}/record"))
		{
            mk_dir("{$root}/record",$mode=0777,$recursive=true,$fso_enc);
        }
		/*$root=str_repeat("../",4)."info/user/".(int)$user_id."/book/{$book_sid}";
        if(!is_dir("{$root}/record")){
            mk_dir("{$root}/record",$mode=0777,$recursive=true,$fso_enc);
        }*/
		$path       ="{$root}/record/";
	
		// Muaz Khan     - www.MuazKhan.com 
		// MIT License   - https://www.webrtc-experiment.com/licence/
		// Documentation - https://github.com/muaz-khan/WebRTC-Experiment/tree/master/RecordRTC
		foreach(array('video', 'audio') as $type)
		{
			if (isset($_FILES["${type}-blob"]))
			{
		
			
			  
			
				$fileName = $_POST["${type}-filename"];
				$uploadDirectory = $path.$fileName;
				
				if (!move_uploaded_file($_FILES["${type}-blob"]["tmp_name"], $uploadDirectory))
				{
					$retrun_data["error"];
				}
	
			}
		}
	
	echo json_encode($retrun_data,1);
?>