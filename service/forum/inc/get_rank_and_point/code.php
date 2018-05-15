<?php
//-------------------------------------------------------
//函式: get_rank_and_point()
//用途: 取得積分以及發文點數
//-------------------------------------------------------

	function get_rank_and_point($user_id,$conn_user=NULL,$conn_mssr=NULL,$arry_conn_user,$arry_conn_mssr){
	//---------------------------------------------------
	//函式: get_rank_and_point()
	//用途: 取得積分以及發文點數
	//---------------------------------------------------
	//$user_id              使用者主索引
	//$conn_user            user 資料庫連線物件
	//$conn_mssr            mssr 資料庫連線物件
	//$arry_conn_user       user 資料庫連線資訊陣列
	//$arry_conn_mssr       mssr 資料庫連線資訊陣列
	//---------------------------------------------------

		//-----------------------------------------------
		//參數檢驗
		//-----------------------------------------------

			if(!isset($user_id)){
				return false;
			}else{
				$user_id=(int)$user_id;
				if($user_id===0){
					return false;
				}
			}

			if(!isset($conn_user)||!isset($conn_mssr)||!isset($arry_conn_user)||!isset($arry_conn_mssr)){
				return false;
			}

		//-----------------------------------------------
		//訊息撈取
		//-----------------------------------------------

			$arrys_msg = array();

			//-------------------------------------------
			//使用者的積分
			//-------------------------------------------

				$sql="
					SELECT `mssr_forum`.`mssr_forum_rank`.`total_rank`
					FROM `mssr_forum`.`mssr_forum_rank`
					WHERE 1=1
						AND `mssr_forum`.`mssr_forum_rank`.`user_id` = {$user_id}
				";

				$db_results = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				if (!empty($db_results)) {
					foreach ($db_results as $inx=>$db_result) {
						extract($db_result, EXTR_PREFIX_ALL, "rs");

						$rs_total_rank = (int)$rs_total_rank;

						$arrys_msg['total_rank'] = $rs_total_rank;
					}
				} else {
					$arrys_msg['total_rank'] = 0;
				}

			//-------------------------------------------
			//使用者的發文點數
			//-------------------------------------------

				$sql="
					SELECT `mssr_forum`.`mssr_forum_point`.`total_point`
					FROM `mssr_forum`.`mssr_forum_point`
					WHERE 1=1
						AND `mssr_forum`.`mssr_forum_point`.`user_id` = {$user_id}
				";

				$db_results = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				if (!empty($db_results)) {
					foreach ($db_results as $inx=>$db_result) {
						extract($db_result, EXTR_PREFIX_ALL, "rs");

						$rs_total_point = (int)$rs_total_point;

						$arrys_msg['total_point'] = $rs_total_point;
					}
				} else {
					$arrys_msg['total_point'] = 100;
				}

		//-----------------------------------------------
		//整理回傳
		//-----------------------------------------------

			//訊息整理
			krsort($arrys_msg);

			//回傳
			return $arrys_msg;
	}
?>