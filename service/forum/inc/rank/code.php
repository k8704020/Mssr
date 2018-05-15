<?php
//-------------------------------------------------------
//函式: rank()
//用途: 積分處理
//-------------------------------------------------------

	function rank($user_id,$get_rank,$rank_type,$conn_mssr,$arry_conn_mssr){
	//---------------------------------------------------
	//函式: rank()
	//用途: 積分處理
	//---------------------------------------------------
	//$user_id              使用者主索引
	//$get_rank             獲得的積分
	//$rank_type            積分類型
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

			//預設積分
			$default_rank = 0;

		//-----------------------------------------------
		//訊息處理
		//-----------------------------------------------

			$arrys_msg = array();

			//-------------------------------------------
			//在積分表中新增或修改資料
			//-------------------------------------------

				//檢查使用者在積分表中是否有資料
				$sql = "
					SELECT `mssr_forum`.`mssr_forum_rank`.`total_rank`
					FROM `mssr_forum`.`mssr_forum_rank`
					WHERE 1=1
						AND `mssr_forum`.`mssr_forum_rank`.`user_id` = $user_id;
				";

				$return_rank_data = db_result($conn_type='pdo', $conn_mssr, $sql, array(0, 10), $arry_conn_mssr);

				if (empty($return_rank_data)) {
					//新增一筆資料
					$total_rank = $default_rank + $get_rank;

					//積分無負數
					if ($total_rank < 0) {
						$total_rank = 0;
					}

					$sql = "
						INSERT INTO `mssr_forum`.`mssr_forum_rank` 
						SET
							`user_id` = $user_id,
							`total_rank` = $total_rank;
					";

					$sth = $conn_mssr->prepare($sql);
					$sth->execute();
				} else {
					//修改原有的資料
					$total_rank = (int)$return_rank_data[0]['total_rank'];
					$total_rank = $total_rank + $get_rank;

					//積分無負數
					if ($total_rank < 0) {
						$total_rank = 0;
					}

					$sql = "
						UPDATE `mssr_forum`.`mssr_forum_rank` 
						SET `total_rank` = $total_rank
						WHERE 1=1
							AND `mssr_forum`.`mssr_forum_rank`.`user_id` = $user_id;
					";

					$sth = $conn_mssr->prepare($sql);
					$sth->execute();
				}

			//-------------------------------------------
			//在積分明細表中新增一筆資料
			//-------------------------------------------

				$sql = "
					INSERT INTO `mssr_forum`.`mssr_forum_rank_detail_log` 
					SET
						`user_id` = $user_id,
						`rank_type` = $rank_type,
						`get_rank` = $get_rank,
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