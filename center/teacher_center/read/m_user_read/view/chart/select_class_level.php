<?php 
//-------------------------------------------------------
//實驗班圖表
//-------------------------------------------------------

	//---------------------------------------------------
	//設定與引用
	//---------------------------------------------------

		//SESSION
		@session_start();

		//啟用BUFFER
		@ob_start();

		//外掛設定檔
		require_once(str_repeat("../",6).'config/config.php');

		//外掛函式檔
		$funcs = array(
			APP_ROOT.'inc/code',
			APP_ROOT.'center/teacher_center/inc/code',
			APP_ROOT.'lib/php/db/code'
		);
		func_load($funcs, true);

		//清除並停用BUFFER
		@ob_end_clean();

	//---------------------------------------------------
	//外部變數
	//---------------------------------------------------

		global $arry_conn_mssr;
		global $conn_mssr;

	//---------------------------------------------------
	//設定參數
	//---------------------------------------------------

		if (isset($_GET['user_id'])) {
			$user_id = $_GET['user_id'];
		}

		$class_code = "idc_2017_2_1_1_1";
		$get_filter_semester_start = "2017-08-01";
		$get_filter_semester_end = "2018-05-31";

	//---------------------------------------------------
	//資料處理
	//---------------------------------------------------

		//範圍日期分成個月份
		function diffdate($date1, $date2) {
			if (strtotime($date1) > strtotime($date2)) {
				$ymd = $date2;
				$date2 = $date1;
				$date1 = $ymd;
			}

			list($y1, $m1, $d1) = explode('-', $date1);
			list($y2, $m2, $d2) = explode('-', $date2);

			$math = ($y2 - $y1) * 12 + $m2 - $m1;
			$my_arr = array();

			if ($y1 == $y2 && $m1 == $m2) {
				if ($m1 < 10) {
					$m1 = intval($m1);
					$m1 = '0' . $m1;
				}
				if ($m2 < 10) {
					$m2 = intval($m2);
					$m2 = '0' . $m2;
				}
				$my_arr[] = $y1 . '-' . $m1;
				$my_arr[] = $y2 . '-' . $m2;
				return $my_arr;
			}

			$p = $m1;
			$x = $y1;

			for ($i = 0; $i <= $math; $i++) {
				if ($p > 12) { 
					$x = $x + 1;
					$p = $p - 12;
					if ($p < 10) {
						$p = intval($p);
						$p = '0' . $p;
					}
					$my_arr[] = $x . '-' . $p;
				} else {
					if ($p < 10) {
						$p = intval($p);
						$p = '0' . $p;
					}
					$my_arr[] = $x . '-' . $p;
				}
				$p = $p + 1;
			}
			return $my_arr;
		}

		$arrys_month = [];
		foreach (diffdate($get_filter_semester_start, $get_filter_semester_end) as $month) {
			$arrys_month[$month]['group'] = 0;
			$arrys_month[$month]['picture_book'] = 0;
			$arrys_month[$month]['bridge_book'] = 0;
			$arrys_month[$month]['words_book'] = 0;
			$arrys_month[$month]['total_book'] = 0;
			$arrys_month[$month]['picture_book_rate'] = 0;
			$arrys_month[$month]['bridge_book_rate'] = 0;
			$arrys_month[$month]['words_book_rate'] = 0;
		}

	//---------------------------------------------------
	//SQL處理
	//---------------------------------------------------

		if (isset($user_id)) {
			//學生個人
			$students = $user_id;
		} else {
			//搜尋班級學生
			$sql = "
				SELECT `user`.`student`.`uid`
				FROM `user`.`student`
				WHERE `user`.`student`.`class_code` = '$class_code'
				GROUP BY `user`.`student`.`uid`;
			";

			$student_result = db_result($conn_type='pdo', $conn_mssr, $sql,array(), $arry_conn_mssr);

			foreach ($student_result as $student) {
				$arry_students[] = (int)$student['uid'];
			}

			$students = implode(",", $arry_students);
		}

		//取得學生每個月看的所有書的本數
		$sql = "
			SELECT
				`mssr_book_borrow_log`.`user_id`,
				`mssr_book_borrow_log`.`borrow_sdate`

			FROM `mssr_book_borrow_log`
				JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid` = `mssr_idc_book_sticker_level_info`.`book_sid`

			WHERE 1=1
				AND `mssr_book_borrow_log`.`user_id` IN ($students)
				AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
				AND `mssr_idc_book_sticker_level_info`.administrator_level > 0

			GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid`;
		";

		$arrys_results = db_result($conn_type='pdo', $conn_mssr, $sql, array(), $arry_conn_mssr);

		foreach ($arrys_results as $arrys_result) {
			$rs_user_id = (int)$arrys_result['user_id'];
			$rs_borrow_sdate = trim($arrys_result['borrow_sdate']);
			$rs_borrow_sdate = date("Y-m",strtotime($rs_borrow_sdate));

			if (array_key_exists($rs_borrow_sdate,$arrys_month)) {
				$arrys_month[$rs_borrow_sdate]['group'] = $arrys_month[$rs_borrow_sdate]['group'] + 1;
			}
		}

		//取得學生每個月看的所有書的總數
		$sql = "
			SELECT COUNT(*) AS `total_book`

			FROM (
				SELECT
					`mssr_book_borrow_log`.`user_id`,
					`mssr_book_borrow_log`.`borrow_sdate`

				FROM `mssr_book_borrow_log`
					JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid` = `mssr_idc_book_sticker_level_info`.`book_sid`

				WHERE 1=1
					AND `mssr_book_borrow_log`.`user_id` IN ($students)
					AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
					AND `mssr_idc_book_sticker_level_info`.administrator_level > 0

				GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid`
			) AS `total_book_data`;
		";

		$total_book_result = db_result($conn_type='pdo', $conn_mssr, $sql, array(), $arry_conn_mssr);

		$total_book = $total_book_result[0]['total_book'];

		//取得學生每個月看的繪本本數
		$sql = "
			SELECT 
				`mssr_book_borrow_log`.`user_id`,
				MIN(`mssr_book_borrow_log`.`borrow_sdate`) AS `borrow_sdate`,
				`mssr_book_borrow_log`.`book_sid`

			FROM `mssr_book_borrow_log`
				JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid` = `mssr_idc_book_sticker_level_info`.`book_sid`

			WHERE 1=1
				AND `mssr_book_borrow_log`.`user_id` IN ($students)
				AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
				AND `mssr_idc_book_sticker_level_info`.administrator_level > 0
				AND `mssr_idc_book_sticker_level_info`.administrator_level < 3

			GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid`;
		";

		$arrys_results = db_result($conn_type='pdo', $conn_mssr, $sql,array(), $arry_conn_mssr);

		foreach ($arrys_results as $arrys_result) {
			$rs_user_id = (int)$arrys_result['user_id'];
			$rs_borrow_sdate = trim($arrys_result['borrow_sdate']);
			$rs_book_sid = trim($arrys_result['book_sid']);
			$rs_borrow_sdate = date("Y-m",strtotime($rs_borrow_sdate));

			if (array_key_exists($rs_borrow_sdate,$arrys_month)) {
				$arrys_month[$rs_borrow_sdate]['picture_book'] = $arrys_month[$rs_borrow_sdate]['picture_book'] + 1;
			}

			//學生每個月的繪本的比例
			$arrys_month[$rs_borrow_sdate]['picture_book_rate'] = round(($arrys_month[$rs_borrow_sdate]['picture_book'] / $arrys_month[$rs_borrow_sdate]['group']) * 100, 2);
		}

		//取得學生每個月看的繪本總數
		$sql = "
			SELECT COUNT(*) AS `total_picture_book`

			FROM (
				SELECT 
					`mssr_book_borrow_log`.`user_id`,
					MIN(`mssr_book_borrow_log`.`borrow_sdate`) AS `borrow_sdate`,
					`mssr_book_borrow_log`.`book_sid`

				FROM `mssr_book_borrow_log`
					JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid` = `mssr_idc_book_sticker_level_info`.`book_sid`

				WHERE 1=1
					AND `mssr_book_borrow_log`.`user_id` IN ($students)
					AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
					AND `mssr_idc_book_sticker_level_info`.administrator_level > 0
					AND `mssr_idc_book_sticker_level_info`.administrator_level < 3

				GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid`
			) AS `picture_book_data`;
		";

		$total_picture_book_result = db_result($conn_type='pdo', $conn_mssr, $sql,array(), $arry_conn_mssr);

		$total_picture_book = $total_picture_book_result[0]['total_picture_book'];

		//取得學生每個月看的橋梁書本數
		$sql = "
			SELECT 
				`mssr_book_borrow_log`.`user_id`,
				MIN(`mssr_book_borrow_log`.`borrow_sdate`) AS `borrow_sdate`,
				`mssr_book_borrow_log`.`book_sid`

			FROM `mssr_book_borrow_log`
				JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid` = `mssr_idc_book_sticker_level_info`.`book_sid`

			WHERE 1=1
				AND `mssr_book_borrow_log`.`user_id` IN ($students)
				AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
				AND `mssr_idc_book_sticker_level_info`.administrator_level > 2
				AND `mssr_idc_book_sticker_level_info`.administrator_level < 5

			GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid`;
		";

		$arrys_results = db_result($conn_type='pdo', $conn_mssr, $sql, array(), $arry_conn_mssr);

		foreach ($arrys_results as $arrys_result) {
			$rs_user_id = (int)$arrys_result['user_id'];
			$rs_borrow_sdate = trim($arrys_result['borrow_sdate']);
			$rs_book_sid = trim($arrys_result['book_sid']);
			$rs_borrow_sdate = date("Y-m",strtotime($rs_borrow_sdate));

			if (array_key_exists($rs_borrow_sdate, $arrys_month)) {
				$arrys_month[$rs_borrow_sdate]['bridge_book'] = $arrys_month[$rs_borrow_sdate]['bridge_book'] + 1;
			}

			//學生每個月的橋樑書的比例
			$arrys_month[$rs_borrow_sdate]['bridge_book_rate'] = round(($arrys_month[$rs_borrow_sdate]['bridge_book'] / $arrys_month[$rs_borrow_sdate]['group']) * 100, 2);
		}

		//取得學生每個月看的橋梁書總數
		$sql = "
			SELECT COUNT(*) AS `total_bridge_book`

			FROM(
				SELECT 
					`mssr_book_borrow_log`.`user_id`,
					MIN(`mssr_book_borrow_log`.`borrow_sdate`) AS `borrow_sdate`,
					`mssr_book_borrow_log`.`book_sid`

				FROM `mssr_book_borrow_log`
					JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid` = `mssr_idc_book_sticker_level_info`.`book_sid`

				WHERE 1=1
					AND `mssr_book_borrow_log`.`user_id` IN ($students)
					AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
					AND `mssr_idc_book_sticker_level_info`.administrator_level > 2
					AND `mssr_idc_book_sticker_level_info`.administrator_level < 5

				GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid`
			) AS `bridge_book_data`;
		";

		$total_bridge_book_result = db_result($conn_type='pdo', $conn_mssr, $sql, array(), $arry_conn_mssr);

		$total_bridge_book = $total_bridge_book_result[0]['total_bridge_book'];

		//取得學生每個月看的文字書本數
		$sql = "
			SELECT
				`mssr_book_borrow_log`.`user_id`,
				MIN(`mssr_book_borrow_log`.`borrow_sdate`) AS `borrow_sdate`,
				`mssr_book_borrow_log`.`book_sid`

			FROM `mssr_book_borrow_log`
				JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid`=`mssr_idc_book_sticker_level_info`.`book_sid`

			WHERE 1=1
				AND `mssr_book_borrow_log`.`user_id` IN ($students)
				AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
				AND `mssr_idc_book_sticker_level_info`.administrator_level > 4

			GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid`;
		";

		$arrys_results = db_result($conn_type='pdo', $conn_mssr, $sql, array(), $arry_conn_mssr);

		foreach ($arrys_results as $arrys_result) {
			$rs_user_id = (int)$arrys_result['user_id'];
			$rs_borrow_sdate = trim($arrys_result['borrow_sdate']);
			$rs_book_sid = trim($arrys_result['book_sid']);
			$rs_borrow_sdate = date("Y-m",strtotime($rs_borrow_sdate));

			if (array_key_exists($rs_borrow_sdate, $arrys_month)) {
				$arrys_month[$rs_borrow_sdate]['words_book'] = $arrys_month[$rs_borrow_sdate]['words_book'] + 1;
			}

			//學生每個月的文字書的比例
			$arrys_month[$rs_borrow_sdate]['words_book_rate'] = round(($arrys_month[$rs_borrow_sdate]['words_book'] / $arrys_month[$rs_borrow_sdate]['group']) * 100, 2);
		}

		//取得學生每個月看的文字書總數
		$sql = "
			SELECT COUNT(*) AS `total_words_book`

			FROM(
				SELECT
					`mssr_book_borrow_log`.`user_id`,
					MIN(`mssr_book_borrow_log`.`borrow_sdate`) AS `borrow_sdate`,
					`mssr_book_borrow_log`.`book_sid`

				FROM `mssr_book_borrow_log`
					JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid`=`mssr_idc_book_sticker_level_info`.`book_sid`

				WHERE 1=1
					AND `mssr_book_borrow_log`.`user_id` IN ($students)
					AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
					AND `mssr_idc_book_sticker_level_info`.administrator_level > 4

				GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid`
			) AS `words_book_data`;
		";

		$total_words_book_result = db_result($conn_type='pdo', $conn_mssr, $sql, array(), $arry_conn_mssr);

		$total_words_book = $total_words_book_result[0]['total_words_book'];

		//取得學生每日登記書籍
		if (isset($user_id)) {
			//取得學生每個天看的繪本本數
			$sql = "
				SELECT DATE(`mssr_book_borrow_log`.`borrow_sdate`) AS `borrow_sdate`

				FROM `mssr_book_borrow_log`
					JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid` = `mssr_idc_book_sticker_level_info`.`book_sid`

				WHERE 1=1
					AND `mssr_book_borrow_log`.`user_id` = $user_id
					AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
					AND `mssr_idc_book_sticker_level_info`.administrator_level > 0
					AND `mssr_idc_book_sticker_level_info`.administrator_level < 3

				GROUP BY DATE(`mssr_book_borrow_log`.`borrow_sdate`);
			";

			$picture_book_results = db_result($conn_type='pdo', $conn_mssr, $sql,array(), $arry_conn_mssr);

			$year_month = array();
			$day = array();

			//將日期區分年月跟日
			foreach ($picture_book_results as $value) {
				$date = date_create($value['borrow_sdate']);
				$picture_book_year_month[] = date_format($date, "Y-m");
				$picture_book_day[date_format($date, "Y-m")][] = date_format($date, "d");
				$picture_book_date[] = date_format($date, "Y-m-d");
			}

			//取得學生每個天看的橋梁書本數
			$sql = "
				SELECT DATE(`mssr_book_borrow_log`.`borrow_sdate`) AS `borrow_sdate`

				FROM `mssr_book_borrow_log`
					JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid` = `mssr_idc_book_sticker_level_info`.`book_sid`

				WHERE 1=1
					AND `mssr_book_borrow_log`.`user_id` = $user_id
					AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
					AND `mssr_idc_book_sticker_level_info`.administrator_level > 2
					AND `mssr_idc_book_sticker_level_info`.administrator_level < 5

				GROUP BY DATE(`mssr_book_borrow_log`.`borrow_sdate`);
			";

			$bridge_book_results = db_result($conn_type='pdo', $conn_mssr, $sql,array(), $arry_conn_mssr);

			$year_month = array();
			$day = array();

			//將日期區分年月跟日
			foreach ($bridge_book_results as $value) {
				$date = date_create($value['borrow_sdate']);
				$bridge_book_year_month[] = date_format($date, "Y-m");
				$bridge_book_day[date_format($date, "Y-m")][] = date_format($date, "d");
				$bridge_book_date[] = date_format($date, "Y-m-d");
			}

			//取得學生每個天看的文字書本數
			$sql = "
				SELECT DATE(`mssr_book_borrow_log`.`borrow_sdate`) AS `borrow_sdate`

				FROM `mssr_book_borrow_log`
					JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid` = `mssr_idc_book_sticker_level_info`.`book_sid`

				WHERE 1=1
					AND `mssr_book_borrow_log`.`user_id` = $user_id
					AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
					AND `mssr_idc_book_sticker_level_info`.administrator_level > 4

				GROUP BY DATE(`mssr_book_borrow_log`.`borrow_sdate`);
			";

			$words_book_results = db_result($conn_type='pdo', $conn_mssr, $sql,array(), $arry_conn_mssr);

			$year_month = array();
			$day = array();

			//將日期區分年月跟日
			foreach ($words_book_results as $value) {
				$date = date_create($value['borrow_sdate']);
				$words_book_year_month[] = date_format($date, "Y-m");
				$words_book_day[date_format($date, "Y-m")][] = date_format($date, "d");
				$words_book_date[] = date_format($date, "Y-m-d");
			}

			//取得時間內所有的日期
			$datediff = strtotime($get_filter_semester_end) - strtotime($get_filter_semester_start);
			$datediff = floor($datediff / (60 * 60 * 24));
		}
 ?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>學生書籍登記狀況(只包含新書)</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<!-- Highcharts -->
		<script src="https://code.highcharts.com/highcharts.js"></script>
		<script src="https://code.highcharts.com/highcharts-3d.js"></script>
		<script src="https://code.highcharts.com/modules/exporting.js"></script>
		<style>
			.total_book_area table {
				width: 700px;
				margin: auto;
				font-weight: bold;
			}
			.table_area {
				padding-top: 35px;
			}
			.table_area td {
				width: 35px;
			}
			.table_area .month {
				width: 75px;
				line-height: 250%;
			}
			.table_area2 {
				/*padding-top: 35px;*/
				padding-top: 200px;
				padding-bottom: 30px;
			}
			.table_area2 table {
				width: 350px;
				margin: auto;
				font-size: 8px;
			}
			.table_area2 .month {
				width: 100px;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-12 text-center">
					<h1>學生書籍登記狀況(只包含新書)</h1>
				</div>
				<div class="col-xs-12 col-sm-12" id="month_about_book">
				</div>
				<div class="col-xs-12 col-sm-12 text-center total_book_area">
					<table class="table">
						<tr>
							<td>
								<span class="glyphicon glyphicon-book"></span>
								總本數：<?php echo $total_book; ?>本
							</td>
							<td>
								<span class="glyphicon glyphicon-book" style="color: #d6d4d4;"></span>
								繪本總數：<?php echo $total_picture_book; ?>本
							</td>
							<td>
								<span class="glyphicon glyphicon-book" style="color: #5b9bd5;"></span>
								橋梁書總數：<?php echo $total_bridge_book; ?>本
							</td>
							<td>
								<span class="glyphicon glyphicon-book" style="color: #df6613;"></span>
								文字書總數：<?php echo $total_words_book; ?>本
							</td>
						</tr>
					</table>
				</div>
<!-- <?php 
	if (isset($user_id)) {
 ?>
				<div class="col-xs-12 col-sm-12 text-center table_area">
					<h1>學生每日登記書籍狀況</h1>
					<table class="table table-bordered">
						<tr>
<?php 
		for ($i = 0; $i < 32; $i++) {
 ?>
							<td><?php echo $i != 0 ? $i : "#"; ?></td>
<?php 
		}
 ?>
						</tr>
<?php 
		foreach (diffdate($get_filter_semester_start, $get_filter_semester_end) as $month) {
 ?>
						<tr>
							<td rowspan="3" class="month"><?php echo $month; ?></td>
<?php 
			for ($i = 1; $i < 32; $i++) {
				if (in_array($month, $picture_book_year_month) && in_array($i, $picture_book_day[$month])) {
 ?>
							<td style="background-color: #d6d4d4;"></td>
<?php 
				} else {
 ?>
							<td></td>
<?php 
				}
			}
 ?>
						</tr>
						<tr>
<?php 
			for ($i = 1; $i < 32; $i++) {
				if (in_array($month, $bridge_book_year_month) && in_array($i, $bridge_book_day[$month])) {
 ?>
							<td style="background-color: #5b9bd5;"></td>
<?php 
				} else {
 ?>
							<td></td>
<?php 
				}
			}
 ?>
						</tr>
						<tr>
<?php 
			for ($i = 1; $i < 32; $i++) {
				if (in_array($month, $words_book_year_month) && in_array($i, $words_book_day[$month])) {
 ?>
							<td style="background-color: #df6613;"></td>
<?php 
				} else {
 ?>
							<td></td>
<?php 
				}
			}
 ?>
						</tr>
<?php 
		}
 ?>
					</table>
				</div>
<?php 
	}
 ?> -->

<?php 
	if (isset($user_id)) {
 ?>
				<div class="col-xs-12 col-sm-12 text-center table_area2">
					<h1>學生每日登記書籍狀況</h1>
					<table class="table table-bordered">
<?php 
		for ($i = 0; $i < $datediff; $i++) {
			$show_date = date("Y-m-d", strtotime($get_filter_semester_start . ' + ' . $i . 'day'));
 ?>
						<tr>
							<td class="month" style="padding: 0px;"><?php echo $show_date; ?></td>
<?php 
			if (in_array($show_date, $picture_book_date)) {
 ?>
							<td style="background-color: #d6d4d4; padding: 0px;"></td>
<?php 
			} else {
 ?>
							<td style="padding: 0px;"></td>
<?php 
			}

			if (in_array($show_date, $bridge_book_date)) {
 ?>
							<td style="background-color: #5b9bd5; padding: 0px;"></td>
<?php 
			} else {
 ?>
							<td style="padding: 0px;"></td>
<?php 
			}

			if (in_array($show_date, $words_book_date)) {
 ?>
							<td style="background-color: #df6613; padding: 0px;"></td>
<?php 
			} else {
 ?>
							<td style="padding: 0px;"></td>
<?php 
			}
 ?>
						</tr>
<?php 
		}
 ?>
					</table>
				</div>
<?php 
	}
 ?>
			</div>
		</div>

		<script src="https://code.jquery.com/jquery.js"></script>
		<script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	</body>
	<script>
		//圖表用參數
		var categories = [];
		var group = [];
		var picture_book = [];
		var picture_book_rate = [];
		var bridge_book = [];
		var bridge_book_rate = [];
		var words_book = [];
		var words_book_rate = [];

<?php 
	foreach ($arrys_month as $month => $arry_month) {
 ?>
		categories.push('<?php echo $month; ?>');
		group.push(<?php echo $arry_month['group']; ?>);
		picture_book.push(<?php echo $arry_month['picture_book']; ?>);
		picture_book_rate.push(<?php echo $arry_month['picture_book_rate']; ?>);
		bridge_book.push(<?php echo $arry_month['bridge_book']; ?>);
		bridge_book_rate.push(<?php echo $arry_month['bridge_book_rate']; ?>);
		words_book.push(<?php echo $arry_month['words_book']; ?>);
		words_book_rate.push(<?php echo $arry_month['words_book_rate']; ?>);
<?php 
	}
 ?>

		//圖表設定
		Highcharts.chart('month_about_book', {
			colors: ['#d6d4d4', '#5b9bd5', '#df6613', '#d6d4d4', '#5b9bd5', '#df6613', '#D2A2CC', '#d3a4ff', '#EAC100', '#FF9224'],
			chart: {
				zoomType: 'xy'
			},
			title: {
				text: ''
			},
			subtitle: {
				text: ''
			},
			xAxis: [{
				categories: categories,
				crosshair: true
			}],
			yAxis: [{
				max: 100,
				labels: {
					align: 'right',
					x: 0,
					y: 0,
					format: '{value}%',
					style: {
						color: Highcharts.getOptions().colors[1],
					}
				},
				title: {
					text: '',
					style: {
						color: Highcharts.getOptions().colors[0]
					}
				},
			}, {
				labels: {
					align: 'left',
					x: 0,
					y: 0,
					format: '{value} 本',
					style: {
						color: Highcharts.getOptions().colors[0],
					}
				},
				title: {
					text: '',
					style: {
						color: Highcharts.getOptions().colors[1]
					}
				},
				opposite: true
			}],
			tooltip: {
				headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' + '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {
				column: {
					pointPadding: 0.1,
					borderWidth: 0,
					pointWidth: 20,
					dataLabels: {
						enabled: true,
						allowOverlap: true,
						useHTML: true,
						formatter: function() {
							return '<div class="datalabelInside" style="position: relative; top: 0px; left: -5px">'+ this.y +'</div>';
						}
					}
				},
				spline: {
					lineWidth: 2,
					dataLabels: {
						enabled: true,
						allowOverlap: true,
						formatter: function() {
								return '<div class="datalabelInside" style="position: relative; top: 30px; left: -5px; color: #C4E1E1;">' + this.y + '%</div>';
						}
					},
					enableMouseTracking: true
				}
			},
			series: [{
				name: '繪本書本數',
				type: 'column',
				yAxis: 1,
				data: picture_book,
				tooltip: {
					valueSuffix: ' 本'
				}
			}, 
			{
				name: '橋梁書本數',
				type: 'column',
				yAxis: 1,
				data: bridge_book,
				tooltip: {
					valueSuffix: ' 本'
				}
			},
			{
				name: '文字書本數',
				type: 'column',
				yAxis: 1,
				data: words_book,
				tooltip: {
					valueSuffix: ' 本'
				}
			},
			{
				name: '繪本比例',
				type: 'spline',
				data: picture_book_rate,
				tooltip: {
					valueSuffix: '%'
				}
			},
			{
				name: '橋梁書比例',
				type: 'spline',
				data: bridge_book_rate,
				tooltip: {
					valueSuffix: '%'
				}
			},
			{
				name: '文字書比例',
				type: 'spline',
				data: words_book_rate,
				tooltip: {
					valueSuffix: '%'
				}
			}]
		});
	</script>
</html>