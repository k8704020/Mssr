<head>
<script src="../../../../lib/jquery/basic/code.js"></script>
</head>
<body>
<?
//---------------------------------------------------
// 書籍報表 : 閱讀紀錄
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
$sql = "
					SELECT 
						DATEDIFF(now(),b.mintime) AS time
					FROM 
					(
						SELECT a.mintime,
						       a.book_sid,
							   a.user_id
						FROM 
						(
							SELECT   MIN(mssr_book_read_opinion_log.keyin_cdate) AS mintime,
									`book_sid`,
									`user_id`
							FROM `mssr_book_read_opinion_log` 
							WHERE `user_id` ='{$user_id}'
							GROUP BY mssr_book_read_opinion_log.`book_sid`
						)AS a
						WHERE a.mintime   >=  '2013-09-01'
					)AS b
					
					LEFT JOIN mssr_book_category_rev
					ON mssr_book_category_rev.book_sid = b.book_sid
					
					LEFT JOIN mssr_book_category
					ON mssr_book_category_rev.cat_code = mssr_book_category.cat_code
					AND mssr_book_category.school_code = 'gcp'
					
					LEFT JOIN mssr_branch
					ON mssr_branch.branch_name = mssr_book_category.cat_name
					WHERE mssr_branch.branch_id = '{$branch_id}'

					GROUP BY b.book_sid";				
					
$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);	
$one_day_count = 0;
$one_week_count= 0;
$all_count = 0 ;

foreach($retrun as $key1=>$val1)
{
	if($val1["time"] < 1) $one_day_count++;  
	if($val1["time"] < 7) $one_week_count++;  
	$all_count++;
}
//---------------------------------------------------
//Html
//---------------------------------------------------
?>
<table>
	<tr>
    	<td style="background-color:#FFFFFF; font-size:22px; ">
			當天閱讀本數 : 
		</td>
        <td style="  font-size:18px; ">
			　<? echo $one_day_count; ?>
        </td>
    </tr>
    <tr>
    	<td >
        </td>
    </tr>
    <tr>
    	<td style="background-color:#FFFFFF; font-size:22px; ">
        	當週閱讀本數 :
        </td>
        <td style="  font-size:18px; ">
        	　<? echo $one_week_count; ?>
        </td>
    </tr>
    <tr>
    	<td style=" height:30px;">
        	
        </td>
        <td style="   height:30px; ">
        	
        </td>
    </tr>
    <tr>
    	<td style="background-color:#FFFFFF; font-size:22px; ">
        	總閱讀本數 :
        </td>
        <td style="  font-size:18px; ">
        	　<? echo $all_count; ?>
        </td>
    </tr>
</table>

<script> 
 window.parent.parent.parent.add_debug("分店概況報表開啟:點選:<? echo value; ?>");
</script>		
</body>

















