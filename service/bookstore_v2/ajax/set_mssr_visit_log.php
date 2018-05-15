<?
//-------------------------------------------------------
//版本編號 1.0
//讀取使用者資料
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
	require_once(str_repeat("../",3)."/config/config.php");
	require_once(str_repeat("../",1)."/inc/set_score_exp/code.php");
	require_once(str_repeat("../",1)."/inc/tx_sys_sid/code.php");


	 //外掛函式檔
	$funcs=array(
				APP_ROOT.'inc/code',
				APP_ROOT.'lib/php/db/code',
				);
	func_load($funcs,true);


	//清除並停用BUFFER
	@ob_end_clean();

	//建立連線 user
	$conn_user=conn($db_type='mysql',$arry_conn_user);
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
		$array["coin"] = 0;
		$array["mas"] = "";
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------

        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$home_id        =(isset($_POST['home_id']))?(int)$_POST['home_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
 		//trim();//去空白

		if($user_id != $_SESSION["uid"] || $user_permission != $_SESSION["permission"])
		{
			$array["error"] .="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------
		$dadad = date("Y-m-d  H:i:s");

		//搜尋今日拜訪數
		$sql = "
				SELECT count(1) AS count
				FROM `mssr_visit_log`
				WHERE `visit_from` = '$user_id'
				AND '".date("Y-m-d")."' = `keyin_cdate`
				GROUP BY `visit_from`
				";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		$today_visit_count = $retrun[0]["count"];



		//這家有無拜訪
		$sql = "
				SELECT count(1) AS count
				FROM `mssr_visit_log`
				WHERE `visit_from` = '$user_id'
				AND `visit_to` =  '$home_id'
				AND '".date("Y-m-d")."' = `keyin_cdate`
				";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		$today_visit_home_hs = $retrun[0]["count"];



		$sql = "";
		//填寫今日拜訪
		if($today_visit_home_hs > 0)
		{   //UP
			$sql = $sql."UPDATE `mssr_visit_log`
						 SET `visit_cno`= `visit_cno`+1
						 WHERE `visit_from` = '$user_id'
						 AND `visit_to` =  '$home_id'
						 AND '".date("Y-m-d")."' = `keyin_cdate`";

		}else
		{
			//填寫訊息至對方!!
			$sql = $sql."INSERT INTO `mssr_msg_log`
					(
						`user_id`,
						`from_id`,
						`log_text`,
						`log_state`,
						`keyin_cdate`,
						`keyin_mdate`
					) VALUES (
						".$home_id.",
						".$user_id.",
						'".$_SESSION['name']." 來你書店拜訪了!!',
						'1',
						'".$dadad."',
						'".$dadad."'
					);";

			//NEW
			$sql = $sql."INSERT INTO `mssr_visit_log`
					(
						`visit_from`,
						`visit_to`,
						`visit_cno`,
						`keyin_cdate`
					)
					VALUES
					(
						'$user_id',
						'$home_id',
						1,
						'".date("Y-m-d")."'
					);";
			//今日5次內有$$
			if($today_visit_count < 5 )
			{
				$array["coin"] = 0;
				$array["mas"] = "拜訪書店 獲得拜訪金10葵幣 <br>今日拜訪獲得葵幣次數 : ".($today_visit_count+1)." / 5";

				$sql_user = "
						SELECT user_coin,box_item,map_item
						FROM  `mssr_user_info`
						WHERE user_id = '".$user_id."'";
				$mssr_user_info = db_result($conn_type='pdo',$conn_mssr,$sql_user,$arry_limit=array(0,1),$arry_conn_mssr);

				$coin =10;
				//給予的金錢數
				$sql = $sql."UPDATE `mssr`.`mssr_user_info`
							SET `user_coin` = `user_coin`+".$coin."
							WHERE `mssr_user_info`.`user_id` = ".$user_id."
							;";
				$tx_sys_sid = tx_sys_sid($user_id,mb_internal_encoding());
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
								'".$user_id."',
								'".$user_id."',
								'".$tx_sys_sid."',
								'visit',
								'".$mssr_user_info[0]['map_item']."',
								'".$mssr_user_info[0]['box_item']."',
								'".((int)$mssr_user_info[0]['user_coin']+$coin)."',
								'正常',
								'',
								'".$dadad."',
								'".$_SERVER["REMOTE_ADDR"]."');";
				//==============經驗值獲得==================
				//獲得類型
				$exp_type="visit_to";

				//獲得的經驗數
				$exp_score = 10;
				$RE = set_score_exp($conn='',$exp_type,$exp_score,$user_id,$arry_conn_mssr);

			}//if結尾
		}//else結尾
		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

		echo json_encode($array,1);
		?>