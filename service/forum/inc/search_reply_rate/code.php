<?php 
//-------------------------------------------------------
//用途：聊書回文率查詢
//-------------------------------------------------------

	//外掛設定檔
	require_once(str_repeat("../", 4).'config/config.php');

	//外掛函式檔
	$funcs = array(
		APP_ROOT.'inc/code',
		APP_ROOT.'lib/php/db/code',
		APP_ROOT.'service/forum/inc/code'
	);
	func_load($funcs, true);

	//建立連線 mssr
	$conn_mssr = conn($db_type='mysql', $arry_conn_mssr);

	//預設參數
	$semester_start = "";
	$semester_end = "";
	$total_count_reply = 0;
	$total_count_user = 0;

	//接收參數
	$year = @$_REQUEST["year"];				//年份(西元)
	$semester = @$_REQUEST["semester"];		//學期
	$group = @$_REQUEST["group"];			//組別

	//參數處理
	if ($semester == 1) {
		$semester_start = $year . "-08-01 00:00:00";
		$semester_end = ($year + 1) . "-01-31 23:59:59";
	} elseif ($semester == 2) {
		$semester_start = ($year + 1) . "-02-01 00:00:00";
		$semester_end = ($year + 1) . "-07-31 23:59:59";
	}

	//查詢
	switch ($group) {
		case 'old':
			$sql = "
				SELECT 
					`user`.`semester`.`school_code`, 
					COUNT(`mssr_forum`.`mssr_forum_reply`.`reply_id`) AS `count_reply`, 
					COUNT(DISTINCT `mssr_forum`.`mssr_forum_reply`.`user_id`) AS `count_user`

				FROM `mssr_forum`.`mssr_forum_reply`
					INNER JOIN `user`.`student`
						ON `mssr_forum`.`mssr_forum_reply`.`user_id` = `user`.`student`.`uid`
					INNER JOIN `user`.`class`
						ON `user`.`student`.`class_code` = `user`.`class`.`class_code`
					INNER JOIN `user`.`semester`
						ON `user`.`class`.`semester_code` = `user`.`semester`.`semester_code`

				WHERE 1=1
					AND `mssr_forum`.`mssr_forum_reply`.`keyin_cdate` BETWEEN '$semester_start' AND '$semester_end'
					AND `user`.`semester`.`semester_year` = '$year'
					AND `user`.`semester`.`semester_term` = '$semester'
					AND `user`.`semester`.`school_code` IN (
						'hop', 'dat', 'tqa', 'zbq', 'tap', 'mid', 'tbn', 'stp', 'cle', 'osl', 
						'uwn', 'ifx', 'lqd', 'dzu', 'lum', 'dxu', 'bts', 'gwh', 'vsa', 'wte', 
						'xql', 'gdc', 'ctc', 'glh', 'gcp', 'don', 'lrb', 'sua', 'pmc', 'smps', 
						'lhes', 'cpe', 'chk', 'chc', 'bjd', 'cte', 'cwl', 'okr', 'shps', 'ybs'
					)

				GROUP BY `user`.`semester`.`school_code`
			";
			break;
		
		case 'new':
			$sql = "
				SELECT 
					`user`.`semester`.`school_code`, 
					COUNT(`mssr_forum`.`mssr_forum_reply`.`reply_id`) AS `count_reply`, 
					COUNT(DISTINCT `mssr_forum`.`mssr_forum_reply`.`user_id`) AS `count_user`

				FROM `mssr_forum`.`mssr_forum_reply`
					INNER JOIN `user`.`student`
						ON `mssr_forum`.`mssr_forum_reply`.`user_id` = `user`.`student`.`uid`
					INNER JOIN `user`.`class`
						ON `user`.`student`.`class_code` = `user`.`class`.`class_code`
					INNER JOIN `user`.`semester`
						ON `user`.`class`.`semester_code` = `user`.`semester`.`semester_code`

				WHERE 1=1
					AND `mssr_forum`.`mssr_forum_reply`.`keyin_cdate` BETWEEN '$semester_start' AND '$semester_end'
					AND `user`.`semester`.`semester_year` = '$year'
					AND `user`.`semester`.`semester_term` = '$semester'
					AND `user`.`semester`.`school_code` IN (
						'nep', 'mdr', 'dwps', 'min', 'cag', 'nwc', 'won', 'chi', 'pqr', 'dme', 
						'gbe', 'cfps', 'otk', 'elf', 'gsk', 'itl', 'gfd', 'did', 'dxi', 'wbp', 
						'skes', 'wof', 'gid', 'gis', 'plg', 'gps', 'gcl', 'bnr', 'api', 'tst', 
						'grm', 'cgs', 'gsw', 'nat', 'bsj', 'clc', 'cyc', 'isg', 'sta', 'xil'
					)

				GROUP BY `user`.`semester`.`school_code`
			";
			break;

		default:
			$sql = "
				SELECT 
					`user`.`semester`.`school_code`, 
					COUNT(`mssr_forum`.`mssr_forum_reply`.`reply_id`) AS `count_reply`, 
					COUNT(DISTINCT `mssr_forum`.`mssr_forum_reply`.`user_id`) AS `count_user`

				FROM `mssr_forum`.`mssr_forum_reply`
					INNER JOIN `user`.`student`
						ON `mssr_forum`.`mssr_forum_reply`.`user_id` = `user`.`student`.`uid`
					INNER JOIN `user`.`class`
						ON `user`.`student`.`class_code` = `user`.`class`.`class_code`
					INNER JOIN `user`.`semester`
						ON `user`.`class`.`semester_code` = `user`.`semester`.`semester_code`

				WHERE 1=1
					AND `mssr_forum`.`mssr_forum_reply`.`keyin_cdate` BETWEEN '$semester_start' AND '$semester_end'
					AND `user`.`semester`.`semester_year` = '$year'
					AND `user`.`semester`.`semester_term` = '$semester'

				GROUP BY `user`.`semester`.`school_code`
			";
			break;
	}

	$result = db_result($conn_type='pdo', $conn_mssr, $sql,array(), $arry_conn_mssr);
 ?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>聊書回文率查詢</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<style>
			.form_area {
				padding-top: 30px;
			}
			.form_area input[type="text"] {
				width: 80px;
			}
			.table_area {
				padding-top: 50px;
			}
			.table_area th {
				text-align: center;
			}
			.total_area {
				padding-bottom: 100px;
				font-size: 20px;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-12 text-center">
					<h1>聊書回文率查詢</h1>
				</div>
				<form>
					<div class="col-xs-12 col-sm-12 form_area">
						<div class="col-xs-12 col-sm-3 col-sm-offset-1 text-center">
							請輸入年份(西元)：
							<input type="text" name="year" maxlength="4">
						</div>
						<div class="col-xs-12 col-sm-3 text-center">
							請選擇學期：
							<input type="radio" id="radio1" name="semester" value="1">
							<label for="radio1">第一學期</label>
							<input type="radio" id="radio2" name="semester" value="2">
							<label for="radio2">第二學期</label>
						</div>
						<div class="col-xs-12 col-sm-3 text-center">
							請選擇組別：
							<select name="group">
								<option value="none" selected>不分組</option>
								<option value="old">舊系統組</option>
								<option value="new">新系統組</option>
							</select>
						</div>
						<div class="col-xs-12 col-sm-1 text-center">
							<input class="btn btn-sm btn-primary" type="submit" value="查詢">
						</div>
					</div>
				</form>
				<div class="col-xs-12 col-sm-12 text-center table_area">
					<table class="table table-striped table-bordered">
						<tr>
							<th>學校代碼</th>
							<th>回文文章數</th>
							<th>回文人數</th>
							<th>回文率</th>
						</tr>
<?php 
	if (!empty($result)) {
		foreach ($result as $key => $value) {
			$school_code = $value['school_code'];
			$count_reply = $value['count_reply'];
			$count_user = $value['count_user'];
			$total_count_reply += $count_reply;
			$total_count_user += $count_user;
 ?>
						<tr>
							<td><?php echo $school_code; ?></td>
							<td><?php echo $count_reply; ?></td>
							<td><?php echo $count_user; ?></td>
							<td><?php echo round($count_reply / $count_user, 2); ?></td>
						</tr>
<?php 
		}
	}
 ?>
					</table>
				</div>
				<div class="col-xs-12 col-sm-12 text-center total_area">
					<div class="col-xs-12 col-sm-4">
						總回文文章數：<?php echo $total_count_reply; ?>
					</div>
					<div class="col-xs-12 col-sm-4">
						總回文人數：<?php echo $total_count_user; ?>
					</div>
					<div class="col-xs-12 col-sm-4">
						平均回文率：<?php echo @round($total_count_reply / $total_count_user, 2); ?>
					</div>
				</div>
			</div>
		</div>

		<script src="https://code.jquery.com/jquery.js"></script>
		<script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	</body>
</html>