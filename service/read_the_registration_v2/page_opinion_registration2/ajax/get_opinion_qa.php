<?php
//-------------------------------------------------------
//版本編號 1.0
//登記書籍  讀取閱讀登記 Q & A
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
	//-----------------------------------------------
	//通用
	//-----------------------------------------------

	//-------------------------------------------
	//初始化, curl設定
	//-------------------------------------------
		$array =array();
		$array["error"] = "";
		$array["echo"] ="";
        //POST


	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------

		//POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;

		if($user_id != $_SESSION["uid"] || $user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------



		//=======================================================
		//新版本設置

		//讀取班級上的問答回答設定
		$sql = "SELECT `topic_id`
				FROM `mssr_book_topic_sel`
				WHERE `class_code` = '".$_SESSION["class"][0][1]."'
			   ";
		$i = 1;
		$data = array();

		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		if(!count($retrun))
		{
			$sql = "SELECT `topic_id`
					FROM `mssr_book_topic_sel`
					WHERE `class_code` = ''
			   ";
		}
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		if(count($retrun))
		{
			foreach($retrun as $key => $val)
			{

				$tmp["topic_id"] = $val["topic_id"];



				$sql = "SELECT `topic_title`,`topic_type`,`topic_options`
						FROM `mssr_book_topic_log`
						WHERE `topic_id` = '".$val["topic_id"]."'
						";
				$retrun_2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);

				if(count($retrun_2))
				{
					$tmp["quest"] = $retrun_2[0]["topic_title"];
					$tmp["type"] =$retrun_2[0]["topic_type"];
					$tmp["answer"] = unserialize($retrun_2[0]["topic_options"]);

				}
				array_push($data,$tmp);
			}
		}
		//====================
		$array["topic"] = $data;
		$array["count"] = count($retrun_2);

		echo json_encode($array,1);


		?>