<head>
<script src="../../../../lib/jquery/basic/code.js"></script>
</head>
<body>
<?
//---------------------------------------------------
// 外太空 >   
//
//---------------------------------------------------

//---------------------------------------------------
//輸入 user_id,book_sid
//輸出 OK
//---------------------------------------------------
//---------------------------------------------------
//設定與引用
//---------------------------------------------------

	//SESSION
	@session_start();

	//啟用BUFFER
	@ob_start();

	//外掛設定檔
	require_once(str_repeat("../",4)."/config/config.php");
	require_once(str_repeat("../",4)."/inc/get_book_info/code.php");

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

	?> 
	<?
//---------------------------------------------------
//END   設定與引用
//---------------------------------------------------
		 
//---------------------------------------------------
//有無維護
//---------------------------------------------------

//---------------------------------------------------
//有無登入
//---------------------------------------------------

//---------------------------------------------------
//重複登入
//---------------------------------------------------

//---------------------------------------------------
//接收,設定參數
//---------------------------------------------------
$user_id = $_GET["user_id"];
$branch_id = $_GET["branch_id"];
//---------------------------------------------------
//檢驗參數
//---------------------------------------------------	 

//---------------------------------------------------
//SQL
//---------------------------------------------------	
$data = array(); // 確認資料



$sql = "SELECT mssr_user_task_tmp.task_sid,
			   mssr_user_task_tmp.task_sdl_goal,
			   mssr_user_task_tmp.task_sdate,
			   mssr_task_period.cat_id,
			   mssr_task_period.task_name
		FROM  `mssr_user_task_tmp` 
		LEFT JOIN mssr_task_period
		ON mssr_user_task_tmp.task_sid = mssr_task_period.task_sid
		WHERE user_id = '{$user_id}'
		AND branch_id = '{$branch_id}'
		AND task_state = '啟用'";
$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
foreach($retrun as $key1=>$val1)
{
	$count = 0;
	if($val1["cat_id"] == 1)
	{//閱讀
	
				
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
						WHERE a.mintime   >=  '".$val1["task_sdate"]."'
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
				
				
		$retrun_2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		$count = sizeof($retrun_2);
		
	}else if($val1["cat_id"] == 2)
	{//推薦
		$sql = "
				
				
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
						WHERE a.mintime   >=  '".$val1["task_sdate"]."'
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
		$retrun_2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);	
		foreach($retrun_2 as $key2=>$val2)
		{
			$re_count = 0;
			
			if($val2["rec_draw_cno"] > 0) $count++;
			if($val2["rec_text_cno"] > 0) $count++;
			if($val2["rec_record_cno"] > 0) $count++;
			
			//if($re_count >= 1 )$count++;
		}
				
	}else if($val1["cat_id"] == 3)
	{//看與讀
	 	$sql = "
				SELECT b.book_sid
				FROM
				(
					SELECT a.mintime,
						   a.book_sid
					FROM
					(
						SELECT MIN(`mssr_book_booking_log`.booking_edate)AS mintime,
							   book_sid
						FROM  `mssr_book_booking_log`
						WHERE `mssr_book_booking_log`.`booking_from` = '{$user_id}'
						AND booking_state = '完成交易'
						GROUP BY `mssr_book_booking_log`.book_sid
					)AS a
					WHERE a.mintime >= '".$val1["task_sdate"]."'
				)AS b
				
				LEFT JOIN mssr_book_category_rev
				ON mssr_book_category_rev.book_sid = b.book_sid
				
				LEFT JOIN mssr_book_category
				ON mssr_book_category_rev.cat_code = mssr_book_category.cat_code
				AND mssr_book_category.school_code = 'gcp'
				
				LEFT JOIN mssr_branch
				ON mssr_branch.branch_name = mssr_book_category.cat_name
				
				WHERE 
				mssr_branch.branch_id = '{$branch_id}'
	
				GROUP BY b.book_sid
				";
		$retrun_2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);	
		$count = sizeof($retrun_2);
	}else if($val1["cat_id"] == 4)
	{//被看與被讀
		$sql = "
				SELECT b.book_sid
				FROM
				(
					SELECT a.mintime,
						   a.book_sid
					FROM
					(
						SELECT MIN(`mssr_book_booking_log`.booking_edate)AS mintime,
							   book_sid
						FROM  `mssr_book_booking_log`
						WHERE `mssr_book_booking_log`.`booking_to` = '{$user_id}'
						AND booking_state = '完成交易'
						GROUP BY `mssr_book_booking_log`.book_sid
					)AS a
					WHERE a.mintime >= '".$val1["task_sdate"]."'
				)AS b
				
				LEFT JOIN mssr_book_category_rev
				ON mssr_book_category_rev.book_sid = b.book_sid
				
				LEFT JOIN mssr_book_category
				ON mssr_book_category_rev.cat_code = mssr_book_category.cat_code
				AND mssr_book_category.school_code = 'gcp'
				
				LEFT JOIN mssr_branch
				ON mssr_branch.branch_name = mssr_book_category.cat_name
				
				WHERE 
				mssr_branch.branch_id = '{$branch_id}'
	
				GROUP BY b.book_sid
				";
		$retrun_2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);	
		$count = sizeof($retrun_2);
	}else if($val1["cat_id"] == 5)
	{//閱讀+推薦
		$sql = "SELECT
				x.book_sid,
				x.rec_stat_cno,
				x.rec_draw_cno,
				x.rec_text_cno,
				x.rec_record_cno,
				read_state
			FROM
			(SELECT 
						b.book_sid,
						rec_stat_cno,
						rec_draw_cno,
						rec_text_cno,
						rec_record_cno
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
						WHERE a.mintime   >=  '".$val1["task_sdate"]."'
					)AS b
					LEFT JOIN mssr_rec_book_cno_semester
					ON b.book_sid = mssr_rec_book_cno_semester.book_sid
									AND b.user_id = mssr_rec_book_cno_semester.user_id
					
					LEFT JOIN mssr_book_category_rev
					ON mssr_book_category_rev.book_sid = b.book_sid
					
					LEFT JOIN mssr_book_category
					ON mssr_book_category_rev.cat_code = mssr_book_category.cat_code
					AND mssr_book_category.school_code = 'gcp'
					
					LEFT JOIN mssr_branch
					ON mssr_branch.branch_name = mssr_book_category.cat_name
					WHERE mssr_branch.branch_id = '{$branch_id}'
					
					
					GROUP BY b.book_sid)AS x
		LEFT JOIN mssr_rec_teacher_read
		ON {$user_id} = mssr_rec_teacher_read.user_id
		AND mssr_rec_teacher_read.book_sid = x.book_sid
		";
		
		$retrun_2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($retrun_2 as $key2=>$val2)
		{
			$re_count = 0;
			//if($val2["rec_stat_cno"] > 0) $re_count++;
			if($val2["rec_draw_cno"] > 0) $re_count++;
			if($val2["rec_text_cno"] > 0) $re_count++;
			if($val2["rec_record_cno"] > 0) $re_count++;
			
			if($re_count >= 1&& $val2["read_state"]==1)$count++;
		}
	}
	$task_tag = array("","本","次","本","本","本");
	
	//處理數值
	
	$data[$key1]["task_sdl_goal"] = $val1["task_sdl_goal"];//需求本數
	$data[$key1]["task_tag"] = $task_tag[$val1["cat_id"]];//直
	$data[$key1]["task_sdate"] = $val1["task_sdate"];//接取日期
	$data[$key1]["task_doing"] = $count;//當前進度
	$data[$key1]["task_name"] = $val1["task_name"];//任務名稱
	$data[$key1]["task_sid"] = $val1["task_sid"];//任務SID
	if($count >= $val1["task_sdl_goal"])
	{$data[$key1]["task_finished"] = 1;}//任務是否完成
	else
	{$data[$key1]["task_finished"] = 0;}
		
}

$last_day = date("d",mktime(date("d")-date("w")+7)); // 距離剩餘的時間
if((date("w")-date("d"))>0)
{
	$last_day = 23-date("h")."時";
}else
{
	$last_day = 7-date("w")."天";
}?>
<!---------------------------------------------------
//JS 初始化
//-------------------------------------------------->
user_id = <? echo $user_id;?>;
branch_id = <? echo $branch_id;?>;
var visit = <?php if($user_id != $_SESSION['uid']){echo 1;}else{echo 0;}?>;


<!---------------------------------------------------
//html 輸出
//-------------------------------------------------->


<table  style="font-size:18px; position:absolute; top:26px;"><?
 for($i = 0  ; $i < sizeof($data) ;  $i++)
{?>
	<tr>
    
    	<td width="190" align="center"><? echo $data[$i]["task_name"];?></td>
        <td width="190" align="center"><? echo $data[$i]["task_doing"]."/".$data[$i]["task_sdl_goal"]."(".$data[$i]["task_tag"].")";?></td>
        <td width="190" align="center" style=" color:<? if((date("w")-date("d"))>0){echo '#F00';}else{echo '#000';};?>" ><? if($data[$i]["task_finished"])
		{
			if($user_id == $_SESSION['uid'])echo '<input type="button" value="完成任務" onClick="set_task_finish(\''.$data[$i]["task_sid"].'\')"/>';
		}else
		{
			if($user_id == $_SESSION['uid'])echo $last_day ;
		} ?></td>
        <td width="190" align="center" style=" <? if($user_id != $_SESSION['uid'])echo "display:none;";?>">
        <div style="color:#04480D; font-weight: bold; background-color:#BCDEC4; width:50px; cursor:pointer;"  onClick="window.parent.parent.go_task_info(window.parent.branch_name,'<? echo $data[$i]["task_sdl_goal"];?>','<? echo $data[$i]["task_sdate"]; ?>','<? echo $branch_id;?>','<? echo $user_id;?>')" >詳細</div>
    </tr>
<? }

?> 
</table>
<table style="position : fixed; top:0px; left:0px; background-color:#FFFFFF; font-size:22px; background-color:#A6D39A; ">
	<tr>
    	<td width="190"   align="center">
        任務名稱
        </td>
        <td width="190"  align="center">
        目前進度
        </td>
        <td width="190"  align="center">
        剩餘時間
        </td>
        <td width="190"  align="center">
        任務詳細
        </td>
    </tr>
</table> 

<script>

function set_task_finish(task_sid)
		{
			window.parent.parent.add_debug("set_task_finish:post:開始" );
			window.parent.parent.set_hide(1,"讀取中");
			var url = "./ajax/set_task_finish.php";
			$.post(url, {
					task_sid:task_sid,
					user_id:user_id,
					branch_id:branch_id
				}).success(function (data) 
				{
					window.parent.parent.add_debug("set_task_finish:post:get_data -> "+data);
					tmp = JSON.parse(data);
					window.parent.parent.add_coin(tmp['coin']);//追加金錢
					//window.parent.parent.data_array = JSON.parse(data);
				}).error(function(e){
					window.parent.parent.add_debug("set_task_finish:post:error -> "+e );
				}).complete(function(e){
					window.parent.parent.add_debug("set_task_finish:post:complete");
					window.parent.parent.set_hide(0,"");
					window.parent.document.getElementsByName('task_log')[0].contentWindow.location.reload();
					window.location.reload();
				});
		}

</script>
		
</body>