<?php
//-------------------------------------------------------
//函式: friend_intimate()
//用途: 書友親密度處理
//-------------------------------------------------------

	function friend_intimate($user_id,$link_user_id,$get_intimate,$intimate_type,$conn_mssr,$arry_conn_mssr){
	//---------------------------------------------------
	//函式: friend_intimate()
	//用途: 書友親密度處理
	//---------------------------------------------------
	//$user_id              使用者主索引
	//$link_user_id         對應者主索引
	//$get_intimate         獲得的親密度
	//$intimate_type        親密度類型
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

			//預設親密度
			$default_intimate = 0;

		//-----------------------------------------------
		//訊息處理
		//-----------------------------------------------

			$arrys_msg = array();

			//-------------------------------------------
			//在書友親密度中新增或修改資料
			//-------------------------------------------

				//檢查使用者在書友親密度表中是否有資料
				$sql = "
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

				$return_friend_intimate_data = db_result($conn_type='pdo', $conn_mssr, $sql, array(0, 10), $arry_conn_mssr);

				if (empty($return_friend_intimate_data)) {
					//新增一筆資料
					$intimate_amount = $default_intimate + $get_intimate;

					$sql = "
						INSERT INTO `mssr_forum`.`mssr_forum_friend_intimate` 
						SET
							`user_id` = $user_id,
							`link_user_id` = $link_user_id,
							`intimate_amount` = $intimate_amount;
					";

					$sth = $conn_mssr->prepare($sql);
					$sth->execute();
				} else {
					//修改原有的資料
					$intimate_amount = (int)$return_friend_intimate_data[0]['intimate_amount'];
					$intimate_amount = $intimate_amount + $get_intimate;

					$sql = "
						UPDATE `mssr_forum`.`mssr_forum_friend_intimate` 
						SET `intimate_amount` = $intimate_amount
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

					$sth = $conn_mssr->prepare($sql);
					$sth->execute();
				}

			//-------------------------------------------
			//在積分明細表中新增一筆資料
			//-------------------------------------------

				$sql = "
					INSERT INTO `mssr_forum`.`mssr_forum_friend_intimate_detail_log` 
					SET
						`user_id_from` = $user_id,
						`user_id_to` = $link_user_id,
						`intimate_type` = $intimate_type,
						`get_intimate` = $get_intimate,
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