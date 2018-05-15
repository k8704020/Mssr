<?php 
//-------------------------------------------------------
//修改書籍語言
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

		$sess_user_id = $_SESSION['user_id'];
		$sess_permission = $_SESSION['permission'];
		$sess_name = $_SESSION['name'];

		if (!isset($sess_user_id) && !isset($sess_permission) && !isset($sess_name)) {
			echo '<span style="font-size:40px; color:red;">請先登入!!</span>';
			header('Location:http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/index.php');
			die();		
		}

		if ($sess_permission != "3") {
			echo '<span style="font-size:40px; color:red;">你沒有權限進入!!</span>';
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

			$sess_user_id = (int)$_SESSION['user_id'];
			$create_by = $sess_user_id;
			$edit_by = $sess_user_id;
			$book_sid = $_POST['book_sid'];
			$language = $_POST['language'];

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
				$sql = "
					UPDATE `mssr_idc_book_sticker_level_info` 
					SET 
						`language` = '$language',
						`edit_by` = '$sess_user_id'
					WHERE `book_sid` = '$book_sid'
				";
				
				$language_result = db_result($conn_type='pdo', $conn_mssr, $sql, array(), $arry_conn_mssr);
			}

	//---------------------------------------------------
	//重導頁面
	//---------------------------------------------------

		return true;
 ?>