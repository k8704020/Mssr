<?php
//-------------------------------------------------------
//版本編號 1.0
//購買商品
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
				APP_ROOT.'lib/php/db/code'
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
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------

        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
		$coin        =(isset($_POST['coin']))?(int)$_POST['coin']:0;
		$item_id = (isset($_POST['item_id']))?(int)$_POST["item_id"]:0;

 		//trim();//去空白
		if($user_permission != $_SESSION["permission"] || $user_id != $_SESSION["uid"] || $coin ==0 || $item_id ==0)
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}
		$dadad = date("Y-m-d  H:i:s");
	//-------------------------------------------
	//SQL
	//-------------------------------------------
		//確認資訊是否異常
		$sql = "SELECT box_item,user_coin,map_item
			FROM  `mssr_user_info`
			WHERE user_id = '$user_id' and user_coin = '$coin'";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		$box_item = $retrun[0]["box_item"];
		$user_coin = $retrun[0]["user_coin"];
		$map_item = $retrun[0]["map_item"];

		if(count($retrun)==0)
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

		//讀取物品價位
		$sql = "SELECT *
			FROM  `mssr_item`
			WHERE item_id = '$item_id'
			AND item_state = '上架'";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		if(!count($retrun))die($sql);
		$item_coin = $retrun[0]["item_coin"];
		$item_id = $retrun[0]["item_id"];

		if(count($retrun)==0 || $item_coin > $user_coin)
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}
		$array["item_coin"] =$item_coin;
		//將物品加入使用著物品欄
		@$tmp = split(",",$box_item);

		$tmp_item = "";

		$item_has = 0;
		for($i = 0 ; $i < (sizeof($tmp)-1);$i++)
		{
			if($i%2==0)
			{
				if($tmp[$i] == $item_id)
				{
					$tmp[$i+1]++;
					$item_has++;
				}
			}
			$tmp_item = $tmp_item.$tmp[$i].",";
		}
		if($item_has == 0)$tmp_item = $item_id.",1,".$tmp_item;
		$user_coin = $user_coin - $item_coin;

		//回存使用者資料
		$sql = "UPDATE mssr_user_info
				SET box_item = '$tmp_item',
				user_coin = $user_coin
				WHERE user_id = '$user_id' ;";

		//回填購買log紀錄

		//系統交易LOG
		$tx_sys_sid = tx_sys_sid($user_id,mb_internal_encoding());
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
						'".$item_id."',
						'-".$item_coin."',
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
						'buy',
						'".$map_item."',
						'".$tmp_item."',
						'".$user_coin."',
						'正常',
						'',
						'".$dadad."',
						'".$_SERVER["REMOTE_ADDR"]."');";
		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);


		echo json_encode($array,1);
		?>