<?php
//-------------------------------------------------------
//函式: stranger_familiar()
//用途: 陌生人熟悉度處理
//-------------------------------------------------------

	function stranger_familiar($user_id,$link_user_id,$get_familiar,$familiar_type,$conn_mssr,$arry_conn_mssr){
	//---------------------------------------------------
	//函式: stranger_familiar()
	//用途: 陌生人熟悉度處理
	//---------------------------------------------------
	//$user_id              使用者主索引
	//$link_user_id         對應者主索引
	//$get_familiar         獲得的親密度
	//$familiar_type        熟悉度類型
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

			//預設熟悉度
			$default_familiar = 0;

		//-----------------------------------------------
		//訊息處理
		//-----------------------------------------------

			$arrys_msg = array();

			//-------------------------------------------
			//在書友親密度中新增或修改資料
			//-------------------------------------------

				//檢查使用者在書友親密度表中是否有資料
				$sql = "
					SELECT `mssr_forum`.`mssr_forum_stranger_familiar`.`familiar_amount`
					FROM `mssr_forum`.`mssr_forum_stranger_familiar`
					WHERE 1=1
						AND (
							`mssr_forum`.`mssr_forum_stranger_familiar`.`user_id` = $user_id
							OR
							`mssr_forum`.`mssr_forum_stranger_familiar`.`link_user_id` = $user_id
							)
						AND (
							`mssr_forum`.`mssr_forum_stranger_familiar`.`user_id` = $link_user_id
							OR
							`mssr_forum`.`mssr_forum_stranger_familiar`.`link_user_id` = $link_user_id
							)
				";

				$return_stranger_familiar_data = db_result($conn_type='pdo', $conn_mssr, $sql, array(0, 10), $arry_conn_mssr);

				if (empty($return_stranger_familiar_data)) {
					//新增一筆資料
					$familiar_amount = $default_familiar + $get_familiar;

					$sql = "
						INSERT INTO `mssr_forum`.`mssr_forum_stranger_familiar` 
						SET
							`user_id` = $user_id,
							`link_user_id` = $link_user_id,
							`familiar_amount` = $familiar_amount;
					";

					$sth = $conn_mssr->prepare($sql);
					$sth->execute();
				} else {
					//修改原有的資料
					$familiar_amount = (int)$return_stranger_familiar_data[0]['familiar_amount'];
					$familiar_amount = $familiar_amount + $get_familiar;

					$sql = "
						UPDATE `mssr_forum`.`mssr_forum_stranger_familiar` 
						SET `familiar_amount` = $familiar_amount
						WHERE 1=1
							AND (
								`mssr_forum`.`mssr_forum_stranger_familiar`.`user_id` = $user_id
								OR
								`mssr_forum`.`mssr_forum_stranger_familiar`.`link_user_id` = $user_id
								)
							AND (
								`mssr_forum`.`mssr_forum_stranger_familiar`.`user_id` = $link_user_id
								OR
								`mssr_forum`.`mssr_forum_stranger_familiar`.`link_user_id` = $link_user_id
								)
					";

					$sth = $conn_mssr->prepare($sql);
					$sth->execute();
				}

			//-------------------------------------------
			//在積分明細表中新增一筆資料
			//-------------------------------------------

				$sql = "
					INSERT INTO `mssr_forum`.`mssr_forum_stranger_familiar_detail_log` 
					SET
						`user_id_from` = $user_id,
						`user_id_to` = $link_user_id,
						`familiar_type` = $familiar_type,
						`get_familiar` = $get_familiar,
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