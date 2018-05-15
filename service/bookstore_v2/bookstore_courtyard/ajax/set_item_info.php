<?php
//-------------------------------------------------------
//版本編號 1.0
//儲存商品資訊
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
		$coin        =(isset($_POST['user_id']))?(int)$_POST['coin']:0;
		$item_map       =(isset($_POST['item_map']))?mysql_prep($_POST['item_map']):0;
		$item_box		=(isset($_POST['item_box']))?mysql_prep($_POST['item_box']):0;
 		//trim();//去空白



		if($user_permission != $_SESSION["permission"] || $user_id != $_SESSION["uid"] )
		{
			$array["error"] ="你違法進入了喔!!  請重新登入1";
			die(json_encode($array,1));
		}
	//-------------------------------------------
	//SQL
	//-------------------------------------------
		//確認資訊是否異常
		$sql = "SELECT box_item,user_coin,map_item
				FROM  `mssr_user_info`
				WHERE user_id = '$user_id' AND user_coin ='$coin'";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		$user_coin = $retrun[0]["user_coin"];

		if(count($retrun)==0)
		{
			$array["error"] ="錯誤拉 !! KEY:5812";
			die(json_encode($array,1));
		}
		else
		{//核對內容物
			//==============回傳的
			$post_map_array = array();
			//map
			$tmp = explode(",",$item_map);
			for($i = 0 ; $i < (int)(sizeof($tmp)/3);$i++)
			{
                if(isset($tmp[$i*3]) && isset($post_map_array[$tmp[$i*3]])){
                    if(!$post_map_array[$tmp[$i*3]])$post_map_array[$tmp[$i*3]]=1;
                    else $post_map_array[$tmp[$i*3]]++;
                }
			}
			//box
			$tmp = explode(",",$item_box);
			for($i = 0 ; $i < (int)(sizeof($tmp)/2);$i++)
			{
                if(isset($tmp[$i*2]) && isset($post_map_array[$tmp[$i*2]])){
                    if(!$post_map_array[$tmp[$i*2]])$post_map_array[$tmp[$i*2]]=$tmp[$i*2+1];
                    else $post_map_array[$tmp[$i*2]]+=$tmp[$i*2+1];
                }
			}

			$msq_map_array = array();
			//map
			$tmp = explode(",",$retrun[0]["map_item"]);
			for($i = 0 ; $i < (int)(sizeof($tmp)/3);$i++)
			{
                if(isset($tmp[$i*3]) && isset($msq_map_array[$tmp[$i*3]])){
                    if(!$msq_map_array[$tmp[$i*3]])$msq_map_array[$tmp[$i*3]]=1;
                    else $msq_map_array[$tmp[$i*3]]++;
                }
			}
			//box
			$tmp = explode(",",$retrun[0]["box_item"]);
			for($i = 0 ; $i < (int)(sizeof($tmp)/2);$i++)
			{
                if(isset($tmp[$i*2]) && isset($msq_map_array[$tmp[$i*2]])){
                    if(!$msq_map_array[$tmp[$i*2]])$msq_map_array[$tmp[$i*2]]=$tmp[$i*2+1];
                    else $msq_map_array[$tmp[$i*2]]+=$tmp[$i*2+1];
                }
			}


			foreach($post_map_array as $key => $val)
			{
				if($post_map_array[$key] != $msq_map_array[$key])
				{

					$array["error"] ="出錯囉! KEY:581";
					die(json_encode($array,1));
				}

			}
			if(sizeof($post_map_array)!= sizeof($msq_map_array))
			{

					$array["error"] ="出錯囉! KEY:5817";
					die(json_encode($array,1));
			}

		}


		$sql = "UPDATE `mssr`.`mssr_user_info`
				SET
					`map_item` = '".$item_map."' ,
					`box_item` = '".$item_box."'
				WHERE `mssr_user_info`.`user_id` = ".$user_id;
		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

		echo json_encode($array,1);
		?>