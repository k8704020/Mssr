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
				if($p > 12) { 
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

		//搜尋班級學生
		$sql = "
			SELECT `user`.`student`.`uid`
			FROM `user`.`student`
			WHERE `user`.`student`.`class_code` = '$class_code'
			GROUP BY `user`.`student`.`uid`
		";

		$student_result = db_result($conn_type='pdo', $conn_mssr, $sql,array(), $arry_conn_mssr);

		foreach ($student_result as $student) {
			$arry_students[] = (int)$student['uid'];
		}

		$students = implode(",", $arry_students);

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

			GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid`, MONTH(`mssr_book_borrow_log`.`borrow_sdate`)
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

			GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid`, MONTH(`mssr_book_borrow_log`.`borrow_sdate`)
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

			GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid`, MONTH(`mssr_book_borrow_log`.`borrow_sdate`)
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

			GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid`, MONTH(`mssr_book_borrow_log`.`borrow_sdate`)
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
 ?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>學生書籍登記狀況(包含新舊書籍)</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<!-- Highcharts -->
		<script src="https://code.highcharts.com/highcharts.js"></script>
		<script src="https://code.highcharts.com/highcharts-3d.js"></script>
		<script src="https://code.highcharts.com/modules/exporting.js"></script>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-12 text-center">
					<h1>學生書籍登記狀況(包含新舊書籍)</h1>
				</div>
				<div class="col-xs-12 col-sm-12" id="month_about_book">
				</div>
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
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {
				column: {
					pointPadding: 0.1,
					borderWidth: 0,
					pointWidth:20,
					dataLabels: {
						enabled: true,
						allowOverlap: true,
						useHTML:true,
						formatter: function() {
							return '<div class="datalabelInside" style="position: relative; top: 0px; left: -5px">'+ this.y +'</div>';
						}
					}
				},
				spline: {
					lineWidth:2,
					dataLabels: {
						enabled: true,
						allowOverlap: true,
						formatter: function() {
							   return '<div class="datalabelInside" style="position: relative; top: 30px; left: -5px; color: #C4E1E1;">'+ this.y +'%</div>';
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