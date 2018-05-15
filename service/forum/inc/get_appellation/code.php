<?php
//-------------------------------------------------------
//函式: get_appellation()
//用途: 取得頭銜
//-------------------------------------------------------

	function get_appellation($total_rank,$conn_user=NULL,$conn_mssr=NULL,$arry_conn_user,$arry_conn_mssr){
	//---------------------------------------------------
	//函式: get_appellation()
	//用途: 取得頭銜
	//---------------------------------------------------
	//$total_rank           使用者持有的積分
	//$conn_user            user 資料庫連線物件
	//$conn_mssr            mssr 資料庫連線物件
	//$arry_conn_user       user 資料庫連線資訊陣列
	//$arry_conn_mssr       mssr 資料庫連線資訊陣列
	//---------------------------------------------------

		//-----------------------------------------------
		//參數檢驗
		//-----------------------------------------------

			if(!isset($total_rank)){
				return false;
			}else{
				$total_rank=(int)$total_rank;
			}

			if(!isset($conn_user)||!isset($conn_mssr)||!isset($arry_conn_user)||!isset($arry_conn_mssr)){
				return false;
			}

		//-----------------------------------------------
		//訊息撈取
		//-----------------------------------------------

			$arrys_msg = array();

			//-------------------------------------------
			//使用者能使用的最高頭銜
			//-------------------------------------------

				$sql="
					SELECT 
						`mssr_forum`.`mssr_forum_appellation`.`appellation_name`,
						`mssr_forum`.`mssr_forum_appellation`.`appellation_mark`,
						`mssr_forum`.`mssr_forum_appellation`.`appellation_color_id`
					FROM `mssr_forum`.`mssr_forum_appellation`
					WHERE 1=1
						AND `mssr_forum`.`mssr_forum_appellation`.`required_rank` <= {$total_rank}
					ORDER BY `mssr_forum`.`mssr_forum_appellation`.`required_rank` DESC
					LIMIT 0, 1
				";

				$db_results = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				if (!empty($db_results)) {
					$appellation_name = $db_results[0]['appellation_name'];
					$appellation_mark = $db_results[0]['appellation_mark'];
					$appellation_color_id = $db_results[0]['appellation_color_id'];

					$arrys_msg['appellation_name'] = $appellation_name;
					$arrys_msg['appellation_mark'] = $appellation_mark;
					$arrys_msg['appellation_color_id'] = $appellation_color_id;
				}

		//-----------------------------------------------
		//整理回傳
		//-----------------------------------------------

			//回傳
			return $arrys_msg;
	}
?>