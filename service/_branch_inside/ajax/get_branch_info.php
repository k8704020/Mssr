<?
//---------------------------------------------------
// 獲取分店基本資料   
//
//---------------------------------------------------

//---------------------------------------------------
//輸入 user_id,conn_mssr
//輸出 branch_rank,branch_cs,branch_nickname,branch_visit
//---------------------------------------------------
//---------------------------------------------------
//設定與引用
//---------------------------------------------------

	//SESSION
	@session_start();

	//啟用BUFFER
	@ob_start();

	//外掛設定檔
	require_once(str_repeat("../",3)."/config/config.php");
	require_once(str_repeat("../",3)."/inc/get_book_info/code.php");

	 //外掛函式檔
	$funcs=array(
				APP_ROOT.'inc/code',
				APP_ROOT.'lib/php/db/code',
				APP_ROOT.'lib/php/array/code'
				);
	func_load($funcs,true);
	

	//清除並停用BUFFER
	@ob_end_clean(); 
	
	//建立連線 user
	$conn_user=conn($db_type='mysql',$arry_conn_user);
	$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

//---------------------------------------------------
//END   設定與引用
//---------------------------------------------------		 

//
$user_id = $_POST["user_id"];
$branch_id = $_POST["branch_id"];

$sql = "SELECT name,
			   sex 
		FROM  `member` 
		WHERE uid = $user_id";
$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
$array["name"] = $retrun[0]['name'];
$array["sex"] = $retrun[0]['sex'];

$sql = "SELECT user_coin
		FROM  `mssr_user_info` 
		WHERE user_id = $user_id ";
$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);	
$array["user_coin"] = (int)$retrun[0]["user_coin"];

$sql = "SELECT mssr_user_branch.branch_rank,mssr_user_branch.branch_cs,mssr_user_branch.branch_visit,mssr_user_branch.branch_nickname,branch_name,branch_lv
		FROM mssr_user_branch
		LEFT JOIN mssr_branch
		ON mssr_user_branch.branch_id = mssr_branch.branch_id
		WHERE  user_id = $user_id 
		AND mssr_user_branch.branch_state = '啟用'
		AND mssr_branch.branch_state = '啟用'
		AND mssr_user_branch.user_id = '{$user_id}'
		AND mssr_user_branch.branch_id = '{$branch_id}'
		AND mssr_branch.branch_id = '{$branch_id}'";
$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
foreach($retrun as $key1=>$val1)
{
	$array["branch_rank"] = $val1["branch_rank"];
	$array["branch_cs"] = $val1["branch_cs"];
	$array["branch_visit"] = $val1["branch_visit"];
	$array["branch_nickname"] = $val1["branch_nickname"];
	$array["branch_name"] = $val1["branch_name"];
	$array["branch_lv"] = $val1["branch_lv"];
	$array["up_spent_coin"] = -1;
	$array["up_branch_cs"] = -1;
	$sql ="SELECT branch_cs,spent_coin,read_book,rec_book
		   FROM `mssr_branch_rank` 
		   WHERE branch_id = $branch_id
		   AND branch_rank = ".($array["branch_rank"]+1).";";
	$retrun2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	foreach($retrun2 as $key2=>$val2)
	{
		$array["up_spent_coin"] = (int)$val2["spent_coin"];
		$array["up_branch_cs"]  = $val2["branch_cs"];
		$array["up_read_book"]  = $val2["read_book"];
		$array["up_rec_book"]   = $val2["rec_book"];
	}
}
//追加   升級條件:如同圈都擴增到同級才可接受升級
$sql = "SELECT count(1) AS count
		FROM mssr_user_branch
		LEFT JOIN mssr_branch
		ON mssr_user_branch.branch_id = mssr_branch.branch_id
		WHERE  user_id = $user_id 
		AND mssr_user_branch.branch_state = '啟用'
		AND mssr_branch.branch_state = '啟用'
		AND branch_rank < '".$array["branch_rank"]."'
		AND branch_lv = '".$array["branch_lv"]."'";
$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
$array["up_branch_ok"]=$retrun[0]['count'];

//追加  升級條件:調查閱讀量(本)
$sql = "
		SELECT 
			b.book_sid
		FROM 
		(
			SELECT a.mintime,
				   a.book_sid,
				   a.user_id
			FROM 
			(
				SELECT MIN(c.mintime) AS mintime,
					   c.book_sid,
					   c.user_id
				FROM
				(						
					SELECT   mssr_book_read_opinion_log.keyin_cdate AS mintime,
							`book_sid`,
							`user_id`
					FROM `mssr_book_read_opinion_log` 
					WHERE `user_id` ='{$user_id}'
					AND keyin_cdate  >=  '2014-04-21 00:00:00'
					
				)AS c
				GROUP BY c.mintime
			)AS a
			WHERE a.mintime   >=  '2014-04-21 00:00:00'
		)AS b
		
		LEFT JOIN mssr_book_category_rev
		ON mssr_book_category_rev.book_sid = b.book_sid
		
		LEFT JOIN mssr_book_category
		ON mssr_book_category_rev.cat_code = mssr_book_category.cat_code
		AND mssr_book_category.school_code = 'gcp'
		
		LEFT JOIN mssr_branch
		ON mssr_branch.branch_name = mssr_book_category.cat_name
		WHERE mssr_branch.branch_id = '{$branch_id}'

		GROUP BY b.book_sid
		";	
$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
$array["up_branch_read"]= sizeof($retrun);

//追加  升級條件:調查推薦量(本)
$sql = "SELECT
				x.book_sid,
				x.rec_stat_cno,
				x.rec_draw_cno,
				x.rec_text_cno,
				x.rec_record_cno,
				read_state
			FROM
			(
			SELECT 
					b.book_sid,
					b.rec_stat_cno,
					b.rec_draw_cno,
					b.rec_text_cno,
					b.rec_record_cno
				FROM 
				(
					SELECT a.mintime,
						   a.book_sid,
						   a.user_id,
						   a.rec_stat_cno,
						   a.rec_draw_cno,
						   a.rec_text_cno,
						   a.rec_record_cno
					FROM 
					(
						SELECT MIN(c.mintime) AS mintime,
							   c.book_sid,
							   c.user_id,
							   c.rec_stat_cno,
							   c.rec_draw_cno,
							   c.rec_text_cno,
							   c.rec_record_cno
						FROM
						(						
							SELECT   mssr_rec_book_cno_semester.keyin_cdate AS mintime,
									rec_stat_cno,
									rec_draw_cno,
									rec_text_cno,
									rec_record_cno,
									`book_sid`,
									`user_id`
							FROM `mssr_rec_book_cno_semester` 
							WHERE `user_id` ='{$user_id}'
							AND keyin_cdate  >=  '2014-04-21 00:00:00'
							
						)AS c
						GROUP BY c.mintime			
						
					)AS a
					WHERE a.mintime   >=  '2014-04-21 00:00:00'
				)AS b
				
				LEFT JOIN mssr_book_category_rev
				ON mssr_book_category_rev.book_sid = b.book_sid
				
				LEFT JOIN mssr_book_category
				ON mssr_book_category_rev.cat_code = mssr_book_category.cat_code
				AND mssr_book_category.school_code = 'gcp'
				
				LEFT JOIN mssr_branch
				ON mssr_branch.branch_name = mssr_book_category.cat_name
				WHERE mssr_branch.branch_id = '{$branch_id}'
	
				GROUP BY b.book_sid
				)AS x
		LEFT JOIN mssr_rec_teacher_read
		ON {$user_id} = mssr_rec_teacher_read.user_id
		AND mssr_rec_teacher_read.book_sid = x.book_sid
		";
$retrun_2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);	
$count = 0 ;
foreach($retrun_2 as $key2=>$val2)
{
	$re_count = 0;
	if($val2["rec_stat_cno"] > 0) $re_count++;
	if($val2["rec_draw_cno"] > 0) $re_count++;
	if($val2["rec_text_cno"] > 0) $re_count++;
	if($val2["rec_record_cno"] > 0) $re_count++;
	
	if($re_count >= 2 && $val2["read_state"]==1)$count++;
}
$array["up_branch_rec"]= $count;

echo json_encode($array,1);



		
?>