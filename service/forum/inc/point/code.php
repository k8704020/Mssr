<?php
//-------------------------------------------------------
//函式: point()
//用途: 發文點數處理
//-------------------------------------------------------

	function point($user_id,$get_point,$friend_plus_point,$point_type,$conn_mssr,$arry_conn_mssr){
	//---------------------------------------------------
	//函式: point()
	//用途: 發文點數處理
	//---------------------------------------------------
	//$user_id              使用者主索引
	//$get_point            獲得的發文點數
	//$friend_plus_point    好友加成的發文點數
	//$point_type           發文點數類型
	//$conn_mssr            mssr 資料庫連線物件
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

			if(!isset($conn_mssr)||!isset($arry_conn_mssr)){
				return false;
			}

		//-----------------------------------------------
		//預設值
		//-----------------------------------------------

			//預設發文點數
			$default_point = 100;

		//-----------------------------------------------
		//訊息處理
		//-----------------------------------------------

			$arrys_msg = array();

			//-------------------------------------------
			//在發文點數表中新增或修改資料
			//-------------------------------------------

				//檢查使用者在發文點數表中是否有資料
				$sql = "
					SELECT `mssr_forum`.`mssr_forum_point`.`total_point`
					FROM `mssr_forum`.`mssr_forum_point`
					WHERE 1=1
						AND `mssr_forum`.`mssr_forum_point`.`user_id` = $user_id;
				";

				$return_point_data = db_result($conn_type='pdo', $conn_mssr, $sql, array(0, 10), $arry_conn_mssr);

				if (empty($return_point_data)) {
					//新增一筆資料
					$total_point = $default_point + $get_point + $friend_plus_point;

					$sql = "
						INSERT INTO `mssr_forum`.`mssr_forum_point` 
						SET
							`user_id` = $user_id,
							`total_point` = $total_point;
					";

					$sth = $conn_mssr->prepare($sql);
					$sth->execute();
				} else {
					//修改原有的資料
					$total_point = (int)$return_point_data[0]['total_point'];
					$total_point = $total_point + $get_point + $friend_plus_point;

					$sql = "
						UPDATE `mssr_forum`.`mssr_forum_point` 
						SET `total_point` = $total_point
						WHERE 1=1
							AND `mssr_forum`.`mssr_forum_point`.`user_id` = $user_id;
					";

					$sth = $conn_mssr->prepare($sql);
					$sth->execute();
				}

			//-------------------------------------------
			//在發文點數明細表中新增一筆資料
			//-------------------------------------------

				$get_point = $get_point + $friend_plus_point;

				$sql = "
					INSERT INTO `mssr_forum`.`mssr_forum_point_detail_log` 
					SET
						`user_id` = $user_id,
						`point_type` = $point_type,
						`get_point` = $get_point,
						`keyin_cdate` = NOW();
				";

				$sth = $conn_mssr->prepare($sql);
				$sth->execute();

		//-----------------------------------------------
		//整理回傳
		//-----------------------------------------------

			//訊息整理
			krsort($arrys_msg);

			//回傳
			return $arrys_msg;
	}
?>