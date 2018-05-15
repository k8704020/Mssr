<?php
//-------------------------------------------------------
//函式: get_friend_intimate()
//用途: 取得使用者與書友的親密度
//-------------------------------------------------------

	function get_friend_intimate($user_id,$link_user_id,$conn_user=NULL,$conn_mssr=NULL,$arry_conn_user,$arry_conn_mssr){
	//---------------------------------------------------
	//函式: get_friend_intimate()
	//用途: 取得使用者與書友的親密度
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
			}

			if(!isset($conn_user)||!isset($conn_mssr)||!isset($arry_conn_user)||!isset($arry_conn_mssr)){
				return false;
			}

			//-------------------------------------------
			//使用者與書友的親密度
			//-------------------------------------------

				$sql="
					SELECT `mssr_forum`.`mssr_forum_friend_intimate`.`intimate_amount`
					FROM `mssr_forum`.`mssr_forum_friend_intimate`
					WHERE 1=1
						AND (
							`mssr_forum`.`mssr_forum_friend_intimate`.`user_id` = $user_id
							OR
							`mssr_forum`.`mssr_forum_friend_intimate`.`link_user_id` = $user_id
							)
						AND (
							`mssr_forum`.`mssr_forum_friend_intimate`.`user_id` = $link_user_id
							OR
							`mssr_forum`.`mssr_forum_friend_intimate`.`link_user_id` = $link_user_id
							)
				";

				$db_results = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				if (!empty($db_results)) {
					$intimate_amount = $db_results[0]['intimate_amount'];
				} else {
					$intimate_amount = 0;
				}

		//-----------------------------------------------
		//整理回傳
		//-----------------------------------------------

			//回傳
			return $intimate_amount;
	}
?>