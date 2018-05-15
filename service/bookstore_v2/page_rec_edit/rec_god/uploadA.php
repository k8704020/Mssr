<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店
//(內頁)  //主頁面 or 內頁
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
        @ob_end_clean();

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

        $user_id    =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$permission =(isset($_SESSION['permission']))?trim($_SESSION['permission']):'0';

        if($permission =='0'||$user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //file_no   圖片序號
    //book_sid  書本識別碼

        $post_chk=array(
            'book_sid   '
        );

        $post_chk=array_map("trim",$post_chk);
        foreach($post_chk as $post){
            if(!isset($_POST[$post])){
                die("?");
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //file_no   圖片序號
    //book_sid  書本識別碼

        //POST
        //$file_no =trim($_POST[trim('file_no ')]);
        $book_sid=trim($_POST[trim('book_sid')]);
		$auth_coin_open =trim($_POST[trim('auth_coin_open')]);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //file_no   圖片序號
    //book_sid  書本識別碼

	$dadad = date("Y-m-d  H:i:s");
        $arry_err=array();


        if($book_sid===''){
           $arry_err[]='書本識別碼,未輸入!';
        }

        if(count($arry_err)!==0){
            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
            die("!");
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
/*
            $file_no =(int)$file_no;
            $book_sid=mysql_prep($book_sid);

            //-----------------------------------------------
            //書籍是否存在
            //-----------------------------------------------

                $get_book_info=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_sid'),$arry_conn_mssr);
*/
        //-----------------------------------------------
        //處理
        //-----------------------------------------------
			function save_rec_draw($book_sid,$user_id,$conn_mssr,$arry_conn_mssr,$auth_coin_open,$chas,$filename)
				{
					$datetime = date("Y-m-d  H:i:s");


					//回傳數值
					$array =array();
					$array["error"] = "";
					$array["echo"] = "";
					$array["text"] = "";
					$array["coin"] = 0;
					$coin = 0;//金錢
					//檢查資料正確性
					$book_sid = mysql_prep($book_sid);
					$user_id = mysql_prep($user_id);
					if($user_id == 0 || $book_sid == NULL || $book_sid == "")die("ERROR");

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
					$time =999;
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
												'".$filename."',
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
												'".$filename."',
												'".$datetime."',
												'".$_SERVER["REMOTE_ADDR"]."'
												);

					";

					db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

					$retrun_data['coin'] = $coin;
					$retrun_data['exp_score'] = $exp_score;
					if($coin > 0)$retrun_data["text"]="錄音 推薦獎勵:".$coin;
					//回傳增加的金錢
					///$array["coin"]=$coin;

					///echo json_encode($array,1);
					//重導頁面
					$url ="";
					$page=str_repeat("../",0)."index.php?book_sid=".$book_sid."&chas=".$chas;
					$arg=array();
					$arg =http_build_query($arg);

					if(!empty($arg)){
						$url="{$page}?{$arg}";
					}else{
						$url="{$page}";
					}

					if($coin == 0)
					{	$msg="上傳成功!";
						$jscript_back="
							<script>

								window.parent.cover('{$msg}',1);
								window.parent.set_top_btn('block');
								self.location.href='{$url}';
							</script>
						";
					}
					else
					{
						$msg="上傳成功!<BR>並獲得繪圖 推薦獎勵:".$coin."葵幣";
						$jscript_back="
							<script>
								window.parent.parent.set_coin({$coin});
								window.parent.cover('{$msg}',1);
								window.parent.set_top_btn('block');
								self.location.href='{$url}';
							</script>
						";
					}

					die($jscript_back);
				}
    //---------------------------------------------------
    //上傳處理
    //---------------------------------------------------

        $allow_exts ="";  //類型清單陣列
        $allow_mimes="";  //mime清單陣列
        $allow_size ="";  //檔案容量上限


		$allow_exts=array(
            trim("wav"),
            trim("mp3 ")
        );

		$allow_mimes=array(
            trim("audio/wav"),
            trim("audio/x-wav"),
            trim("audio/mpeg3"),
            trim("audio/x-mpeg-3"),
            trim("video/mpeg"),
            trim("video/x-mpeg"),
			trim("audio/mp3")
        );
        $allow_size=array('kb'=>500000);
//echo "<Pre>";print_r($_POST);echo "</Pre>";
//echo "<Pre>";print_r($_FILES);echo "</Pre>";
//die();
        //判斷有沒有上傳檔案
        if(isset($_FILES["file"])&&!empty($_FILES["file"])&&$_FILES["file"]['error']===0){

            //變數設定
            $File=$_FILES["file"];
            $root=str_repeat("../",4)."info/user";
            $path="{$root}/{$user_id}/book/{$book_sid}/record";

            //檢核資料夾
            $_arrys_path=array(
                "{$path}"       =>mb_convert_encoding("{$path}",$fso_enc,$page_enc)
            );
            foreach($_arrys_path as $_path=>$_path_enc){
                if(!file_exists($_path_enc)){
                    mk_dir($_path,$mode=0777,$recursive=true,$fso_enc);
                }
            }

            //溢位判斷
            if(!fso_isunder($root,$path,$fso_enc)){
                $err_msg     ="上傳失敗,溢位.請重新上傳!";
                $jscript_back="
                    <script>

								window.parent.cover('{$msg}',1);
								window.parent.set_top_btn('block');
								self.location.href='{$url}';
					</script>
                ";
                die($jscript_back);
            }

            //上傳處理,成功:儲存的路徑(檔案系統編碼),失敗:false
            $upload_file=file_upload_save($File,$path.'',$fso_enc,$allow_exts,$allow_mimes,$allow_size,true);

            if($upload_file!==false){

            //檔名更動
			$info     =pathinfo($upload_file);
			$extension=strtolower(trim($info['extension']));
			$filename ="1";
			$oldFile  ="{$path}/".trim($info['basename']);
			$newFile  ="{$path}/{$filename}.{$extension}";


			//更改錄音名稱
			rename($oldFile,$newFile);

                if($file_server_enable){
                //---------------------------------------------------
                //FTP DATASERVER 資料夾確認
                //---------------------------------------------------
                    //ftp路徑
                    $ftp_root="public_html/mssr/info/user";
                    $ftp_path="{$ftp_root}/{$user_id}/book/{$book_sid}/record";

                    //檢核資料夾
                    $_arrys_path=array(
                        "{$ftp_root}"                            =>mb_convert_encoding("{$ftp_root}",$fso_enc,$page_enc),
                        "{$ftp_root}/{$user_id}"                 =>mb_convert_encoding("{$ftp_root}/{$user_id}",$fso_enc,$page_enc),
                        "{$ftp_root}/{$user_id}/book"            =>mb_convert_encoding("{$ftp_root}/{$user_id}/book",$fso_enc,$page_enc),
                        "{$ftp_root}/{$user_id}/book/{$book_sid}"=>mb_convert_encoding("{$ftp_root}/{$user_id}/book/{$book_sid}",$fso_enc,$page_enc),

                        "{$ftp_path}"                            =>mb_convert_encoding("{$ftp_path}",$fso_enc,$page_enc)
                    );
                    foreach($_arrys_path as $_path=>$_path_enc){
                        //重新連接 | 重新登入 FTP
                        $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                        $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

                        ftp_pasv($ftp_conn,TRUE);

                        if(false===@ftp_chdir($ftp_conn,$_path_enc)){
                            mk_dir_ftp($ftp_conn,$_path,$mode=0777,$fso_enc);
                        }
                        //關閉連線
                        ftp_close($ftp_conn);
                    }
                //---------------------------------------------------
                //FTP DATASERVER 上傳錄音
                //---------------------------------------------------



                    //重新連接 | 重新登入 FTP
                        $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                        $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

                        //設置ftp路徑
                        ftp_chdir($ftp_conn,"{$ftp_path}");

                        //ftp上傳
                        ftp_put($ftp_conn,"{$filename}.{$extension}",$newFile,FTP_BINARY);

                        //關閉連線
                        ftp_close($ftp_conn);

                        @unlink($newFile);
                    //rm_dir(str_repeat("../",4)."info/user/$user_id","");
                }
            //////////////////////////////////////////////////////////////////////////////////////////////
            //echo "<Pre>";print_r("{$filename}.{$extension}");echo "</Pre>";
            //die();
			save_rec_draw($book_sid,$user_id,$conn_mssr,$arry_conn_mssr,$auth_coin_open,$chas,"{$filename}.{$extension}");
			}else{
            //上傳處理,失敗

                $err_msg=array(
                    "上傳失敗,可能原因如下,請重新上傳!",
                    "",
                    "1.檔案類型不符合"
                    //"2.檔案大小超出限制"
                );
                $err_msg=implode('~',$err_msg);


				//重導頁面
                $url ="";
                $page=str_repeat("../",0)."code.php?book_sid=".$book_sid;
                $arg=array();
                $arg =http_build_query($arg);

                if(!empty($arg)){
                    $url="{$page}?{$arg}";
                }else{
                    $url="{$page}";
                }

                $jscript_back="
                    <script>

								window.parent.cover('{$msg}',1);
								window.parent.set_top_btn('block');
								self.location.href='{$url}';
					</script>
                ";

                die($jscript_back);
            }
        }else{
        //沒有上傳檔案
            die();
        }
?>