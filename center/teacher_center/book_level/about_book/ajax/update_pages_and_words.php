<?php 
//-------------------------------------------------------
//修改頁數及字數
//-------------------------------------------------------
	//---------------------------------------------------
	//設定與引用
	//---------------------------------------------------

		//SESSION
		@session_start();

		//啟用BUFFER
		@ob_start();

		//外掛設定檔
		require_once(str_repeat("../",5).'config/config.php');

		//外掛函式檔
		$funcs = array(
			APP_ROOT.'inc/code',
			APP_ROOT.'center/teacher_center/inc/code',
			APP_ROOT.'lib/php/vaildate/code',
			APP_ROOT.'lib/php/db/code',
			APP_ROOT.'lib/php/net/code',
			APP_ROOT.'lib/php/array/code'
		);
		func_load($funcs,true);

		//清除並停用BUFFER
		@ob_end_clean();

	//---------------------------------------------------
	//權限,與判斷
	//---------------------------------------------------

		$sess_user_id = $_SESSION['book_level_user_id'];
		$sess_permission = $_SESSION['book_level_permission'];
		$sess_name = $_SESSION['book_level_name'];

		//預設
		//預設
		$data['type'] = 'error';
		$data['error_text']= '好像有問題請與系統人員聯絡!!';
		$data['error_go_to_url'] = '';
		
		
		if (!isset($sess_user_id) && !isset($sess_permission) && !isset($sess_name)) {
			$data['type'] = 'error';
			$data['error_text']= '請先登入!!';
			$data['error_go_to_url'] = 'http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/index.php';
			echo json_encode($data);
			die();
		}
	
		if ($sess_permission != "3") {
			$data['type'] = 'error';
			$data['error_text'] = '你沒有權限進入!!';
			$data['error_go_to_url'] = 'http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/super_use_index.php';
			
			echo json_encode($data);
			die();
		}

	//---------------------------------------------------
	//資料庫
	//---------------------------------------------------

		//-----------------------------------------------
		//通用
		//-----------------------------------------------

			//建立連線 mssr
			$conn_mssr = conn($db_type='mysql', $arry_conn_mssr);

		//-----------------------------------------------
		//預設值
		//-----------------------------------------------

			$sess_user_id = (int)$_SESSION['book_level_user_id'];
			$create_by = $sess_user_id;
			$edit_by = $sess_user_id;
			$book_sid = $_POST['book_sid'];
			$action = $_POST['action'];
			if (isset($_POST['pages'])) {
				$pages = $_POST['pages'];
			}
			if (isset($_POST['words'])) {
				$words = $_POST['words'];
			}

		//-----------------------------------------------
		//處理
		//-----------------------------------------------

			$sql = "
				SELECT * 
				FROM `mssr_idc_book_sticker_level_info` 
				WHERE book_sid = '$book_sid'
			";

			$result = db_result($conn_type='pdo', $conn_mssr, $sql, array(), $arry_conn_mssr);

			if (!empty($result)) {
				switch ($action) {
					case 'update_pages':
						$sql = "
							UPDATE `mssr_idc_book_sticker_level_info` 
							SET 
								`pages` = '$pages',
								`edit_by` = '$sess_user_id'
							WHERE `book_sid` = '$book_sid'
						";

						$pages_result = db_result($conn_type='pdo', $conn_mssr, $sql, array(), $arry_conn_mssr);
						$data['type']= 'ok';
						$data['error_text'] = '';
						$data['error_go_to_url']= '';
						break;
					
					case 'update_words':
						$sql = "
							UPDATE `mssr_idc_book_sticker_level_info` 
							SET 
								`words` = '$words',
								`edit_by` = '$sess_user_id'
							WHERE `book_sid` = '$book_sid'
						";
						
						$words_result = db_result($conn_type='pdo', $conn_mssr, $sql, array(), $arry_conn_mssr);
						$data['type']= 'ok';
						$data['error_text'] = '';
						$data['error_go_to_url']= '';
						break;
				}
			}else{
				$data['type']= 'error';
				$data['error_text'] = '資料庫錯誤請聯繫系統人員';
				$data['error_go_to_url']= '';
			}

	//---------------------------------------------------
	//重導頁面
	//---------------------------------------------------

		echo json_encode($data);
 ?>