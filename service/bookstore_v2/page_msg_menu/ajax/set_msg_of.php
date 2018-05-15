<?
//-------------------------------------------------------
//版本編號 1.0
//讀取訊息書籍之數量
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
	require_once(str_repeat("../",2)."/inc/tx_sys_sid/code.php");
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
	$conn_user=conn($db_type='mysql',$arry_conn_user);
	//---------------------------------------------------
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
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------

        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
		$log_id = (int)$_POST['log_id'];
 		//trim();//去空白

		if($user_permission != $_SESSION["permission"] && $_POST['user_id'] != $_SESSION["uid"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------
	$sql = "SELECT *
			FROM  `mssr_msg_log`
			WHERE log_state='1' AND log_id = '{$log_id}'";
    $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	if(count($retrun)==0)
	{
		die(json_encode($array,1));
	}
	$sql = "SELECT tx_sid,tx_coin
			FROM  `mssr_tx_gift_log`
			WHERE msg_id = '{$log_id}';";
	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	if(count($retrun)==0)
	{
		//============回填已閱讀==============
		$sql = "UPDATE  `mssr`.`mssr_msg_log` SET  `log_state` =  '2' WHERE  `mssr_msg_log`.`log_id` ='{$log_id}';";
	}else if($retrun[0]["tx_coin"]>0)
	{
		$array["coin"]=$retrun[0]["tx_coin"];
		//============讀取玩家資訊============
		$sql = "SELECT map_item,box_item,user_coin
				FROM  `mssr_user_info`
				WHERE user_id = '{$user_id}'
				";
		$retrun_user = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		$add_coin = (int)$retrun[0]["tx_coin"];
		//============回填已領取==============
		$sql = "UPDATE  `mssr`.`mssr_tx_gift_log` SET  `tx_state` ='已領取' WHERE  `mssr_tx_gift_log`.`msg_id` = '{$log_id}';";
		//============回填已閱讀==============
		$sql = $sql."UPDATE  `mssr`.`mssr_msg_log` SET  `log_state` =  '2' WHERE  `mssr_msg_log`.`log_id` ='{$log_id}';";
		//============回填增加使用者金錢=======
		$sql = $sql."UPDATE  `mssr`.`mssr_user_info` SET  `user_coin` = user_coin+$add_coin WHERE  user_id = '{$user_id}';";
		//============增加使用者交易變動=======
		$tx_sys_sid = tx_sys_sid($user_id,mb_internal_encoding());
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
						'gift',
						'".$retrun_user[0]['map_item']."',
						'".$retrun_user[0]['box_item']."',
						'".((int)$retrun_user[0]['user_coin']+$add_coin)."',
						'正常',
						'',
						'".date("Y-m-d  H:i:s")."',
						'".$_SERVER["REMOTE_ADDR"]."');";
		$array["coin"] = $add_coin;


	}else
	{
		//============回填已領取==============
		$sql = "UPDATE  `mssr`.`mssr_tx_gift_log` SET  `tx_state` ='已領取' WHERE  `mssr_tx_gift_log`.`log_id` = '{$log_id}';";
		//============回填已閱讀==============
		$sql = $sql."UPDATE  `mssr`.`mssr_msg_log` SET  `log_state` =  '2' WHERE  `mssr_msg_log`.`log_id` ='{$log_id}';";
	}

	db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	echo json_encode($array,1)

?>