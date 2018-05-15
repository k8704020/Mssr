<?php 
	//-------------------------------------------------------
	//名稱：明日閱讀等級認證 聊書系統 function
	//函式：aorft_cert_forum()
	//用途：取得指定學校一學年中所有的學生
	//-------------------------------------------------------

	//使用範例
	// $_REQUEST["funName"] = "aorft_cert_forum";
	// $_REQUEST["school_code"] = "gcp";
	// $_REQUEST["semester"] = 2017;
	// $_REQUEST["art_num"] = 1;

	//呼叫並執行function
	if (isset($_REQUEST["funName"])) {
		$funName = $_REQUEST["funName"];
		echo JSON_encode(@$funName());	//執行並回傳(轉成JSON格式)
	}

	function aorft_cert_forum() {
		//外掛設定檔
		require_once(str_repeat("../", 2).'config/config.php');

		//外掛函式檔
		$funcs = array(
			APP_ROOT.'inc/code',
			APP_ROOT.'lib/php/db/code',
			APP_ROOT.'service/forum/inc/code'
		);
		func_load($funcs, true);

		//建立連線 mssr
		$conn_mssr = conn($db_type='mysql', $arry_conn_mssr);

		//接收參數
		$school_code = @$_REQUEST["school_code"];	//學校代碼
		$semester = @$_REQUEST["semester"];			//學期(單位西元)
		$art_num = @$_REQUEST["art_num"];			//指定每位學生需要的討論(發文或回文)次數

		//參數處理
		$class_code = $school_code . "_" . $semester . "%";
		$semester_start = $semester . "-08-01 00:00:00";
		$semester_end = ($semester + 1) . "-07-31 23:59:59";
		$student_start = $semester . "-08-01";

		//搜尋班級學生
		$sql = "
			SELECT `user`.`student`.`uid`
			FROM `user`.`student`
			WHERE `user`.`student`.`class_code` LIKE '$class_code'
			GROUP BY `user`.`student`.`uid`
		";

		$student_result = db_result($conn_type='pdo', $conn_mssr, $sql,array(), $arry_conn_mssr);

		if (!empty($student_result)) {
			foreach ($student_result as $student) {
				$arry_students[] = (int)$student['uid'];
			}
			
			$students = implode(",", $arry_students);

			//搜尋聊書資料
			$sql = "
				SELECT COUNT(`data2`.`user_id`) AS `result`
				FROM (
					SELECT 
						`data`.`user_id`, 
						COUNT(`data`.`user_id`) AS `count_user`
					FROM (
						SELECT `mssr_forum`.`mssr_forum_article`.`user_id`
						FROM `mssr_forum`.`mssr_forum_article`
							INNER JOIN `user`.`student`
								ON `mssr_forum`.`mssr_forum_article`.`user_id` = `user`.`student`.`uid`
						WHERE 1=1
							AND `mssr_forum`.`mssr_forum_article`.`user_id` IN ($students)
							AND `mssr_forum`.`mssr_forum_article`.`keyin_cdate` BETWEEN '$semester_start' AND '$semester_end'
							AND `mssr_forum`.`mssr_forum_article`.`keyin_cdate` BETWEEN '$student_start' AND `user`.`student`.`end`
						GROUP BY `mssr_forum`.`mssr_forum_article`.`article_id`

						UNION ALL

						SELECT `mssr_forum`.`mssr_forum_reply`.`user_id`
						FROM `mssr_forum`.`mssr_forum_reply`
							INNER JOIN `user`.`student`
								ON `mssr_forum`.`mssr_forum_reply`.`user_id` = `user`.`student`.`uid`
						WHERE 1=1
							AND `mssr_forum`.`mssr_forum_reply`.`user_id` IN ($students)
							AND `mssr_forum`.`mssr_forum_reply`.`keyin_cdate` BETWEEN '$semester_start' AND '$semester_end'
							AND `mssr_forum`.`mssr_forum_reply`.`keyin_cdate` BETWEEN '$student_start' AND `user`.`student`.`end`
						GROUP BY `mssr_forum`.`mssr_forum_reply`.`reply_id`
					) AS `data`
					GROUP BY `data`.`user_id`
				) AS `data2`
				WHERE `data2`.`count_user` >= $art_num;
			";

			$forum_result = db_result($conn_type='pdo', $conn_mssr, $sql,array(), $arry_conn_mssr);
			$total_number = $forum_result[0]['result'];

			//回傳資料
			return $total_number;
		} else {
			return 0;
		}
	}
 ?>