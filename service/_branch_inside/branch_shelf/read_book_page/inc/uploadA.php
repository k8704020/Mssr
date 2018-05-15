<?php
//-------------------------------------------------------
//明日星球,總店
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",3).'config/config.php');
		require_once(str_repeat("../",1)."/mssr_rec_book/mssr_rec_book_record_sid/code.php");
		require_once(str_repeat("../",1)."/inc/tx_sys_sid/code.php");
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

        //SESSION
        $sess_uid   =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:0;

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //user_id   使用者主索引
    //book_sid  書籍識別碼
    //data      檔案編碼
    //fname     檔案名稱

        $post_chk=array(
            'user_id    ',
            'book_sid   ',
			'time   ',
            'data       ',
            'fname      '
        );
        $post_chk=array_map("trim",$post_chk);
        foreach($post_chk as $post){
            if(!isset($_POST[$post])){
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //user_id   使用者主索引
    //book_sid  書籍識別碼
    //data      檔案編碼
    //fname     檔案名稱

        //POST
        $user_id =trim($_POST[trim('user_id ')]);
        $book_sid=trim($_POST[trim('book_sid')]);
		$time=trim($_POST[trim('time')]);
		//傳輸參數
		$retrun_data = array();
		$coin = 0;
		$retrun_data['coin'] = 0;
		$retrun_data['error'] = '';
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //user_id   使用者主索引
    //book_sid  書籍識別碼
    //data      檔案編碼
    //fname     檔案名稱

        $arry_err=array();

        if($user_id===''){
           $arry_err[]='使用者主索引,未輸入!';
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $arry_err[]='使用者主索引,錯誤!';
            }
        }
        if($book_sid===''){
           $arry_err[]='書籍識別碼,未輸入!';
        }

        if(count($arry_err)!==0){
            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
            die();
        }

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------

            $book_sid=mysql_prep($book_sid);

            $get_book_info=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_id'),$arry_conn_mssr);
            if(empty($get_book_info)){
                die();
            }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $user_id    =(int)$user_id;
            $book_sid   =mysql_prep(strip_tags($book_sid));

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
						SET rec_record_cno = rec_record_cno+1 , keyin_mdate = NOW(), `keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
						WHERE book_sid = '".$book_sid."'
						AND   user_id = '".$user_id."';
						";
				//判斷學期資料是否存在
				if($retrun_semester[0]['count'] >= 1)
				{			
					$sql = $sql."UPDATE mssr_rec_book_cno_semester
							SET rec_record_cno = rec_record_cno+1 , keyin_mdate = NOW(), `keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
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
				if($retrun_week[0]['count'] >= 1)
				{			
					$sql = $sql."UPDATE mssr_rec_book_cno_one_week
							SET rec_record_cno = rec_record_cno+1 , keyin_mdate = NOW(), `keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
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
							NOW(),
							NOW(),
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
							NOW(),
							NOW(),
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
							NOW(),
							NOW(),
							'".$_SERVER["REMOTE_ADDR"]."');";
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
				if($time > 10)//作畫時間大於15秒
				{
					
					//給予CS 滿意度
					//set_brench_cs_filter($conn_mssr,$book_id,$array_filter=array(),$arry_conn_mssr);
					
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
									NOW(),
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
			
			$rec_d_sid = mssr_rec_book_record_sid($user_id,mb_internal_encoding());
			$sql = $sql."INSERT INTO  `mssr`.`mssr_rec_book_record_log` (
										`user_id` ,
										`book_sid` ,
										`rec_sid` ,
										`rec_time` ,
										`rec_operate_time` ,
										`rec_reward` ,
										`rec_state` ,
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
										NOW(),
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
										NOW(),
										'".$_SERVER["REMOTE_ADDR"]."'
										);
							  
			";
			
			db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		
		$retrun_data['coin'] = $coin;
    //---------------------------------------------------
    //上傳處理
    //---------------------------------------------------

        //目錄檢測
        $root=str_repeat("../",3)."info/user/".(int)$sess_uid."/book/{$book_sid}";
        if(!is_dir("{$root}/record")){
            mk_dir("{$root}/record",$mode=0777,$recursive=true,$fso_enc);
        }

        //編碼處理
        $data       =substr($_POST['data'], strpos($_POST['data'], ",")+1);
        $decodedData=base64_decode($data);
        $filename   =urldecode(trim($_POST['fname']));
        $path       ="{$root}/record";

        //溢位判斷
        if(!fso_isunder($root,$path,$fso_enc)){
            $retrun_data['error'] = '上傳錯誤';
			echo json_encode($retrun_data,1);
        }

        //上傳
        $fp=fopen("{$path}/{$filename}","wb");
        fwrite($fp, $decodedData);
        fclose($fp);
		
		
		echo json_encode($retrun_data,1);
?>

