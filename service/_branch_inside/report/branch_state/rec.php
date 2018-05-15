<head>
<script src="../../../../lib/jquery/basic/code.js"></script>
</head>
<body>
<?
//---------------------------------------------------
// 書籍報表 : 推薦紀錄INFO
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
$rec_stat_cno = 0;
$rec_draw_cno = 0;
$rec_text_cno = 0;
$rec_record_cno = 0;
$count = 0;
$sql = "SELECT 
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
				SELECT   MIN(mssr_rec_book_cno_semester.keyin_cdate) AS mintime,
						rec_stat_cno,
						rec_draw_cno,
						rec_text_cno,
						rec_record_cno,
						`book_sid`,
						`user_id`
				FROM `mssr_rec_book_cno_semester` 
				WHERE `user_id` ='{$user_id}'
				GROUP BY mssr_rec_book_cno_semester.`book_sid`
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
		foreach($retrun as $key2=>$val2)
		{
			if($val2["rec_stat_cno"]>0)$rec_stat_cno++;
			if($val2["rec_draw_cno"]>0)$rec_draw_cno++;
			if($val2["rec_text_cno"]>0)$rec_text_cno++;
			if($val2["rec_record_cno"]>0)$rec_record_cno++;
			$count ++ ;
		}
		
		
//---------------------------------------------------
//Html
//---------------------------------------------------
?>

<table>
	<tr>
    	<td style="background-color:#FFFFFF; font-size:22px; ">
			評星本數 : 
		</td>
        <td style="  font-size:18px; ">
			　<? echo $rec_stat_cno; ?>
        </td>
    </tr>
    <tr>
    	<td >
        </td>
    </tr>
    <tr>
    	<td style="background-color:#FFFFFF; font-size:22px; ">
        	繪圖本數 :
        </td>
        <td style="  font-size:18px; ">
        	　<? echo $rec_draw_cno; ?>
        </td>
    </tr>
    <tr>
    	<td >
        </td>
    </tr>
    <tr>
    	<td style="background-color:#FFFFFF; font-size:22px; ">
        	文字本數 :
        </td>
        <td style="  font-size:18px; ">
        	　<? echo $rec_text_cno; ?>
        </td>
    </tr>
    <tr>
    	<td >
        </td>
    </tr>
    <tr>
    	<td style="background-color:#FFFFFF; font-size:22px; ">
        	錄音本數 :
        </td>
        <td style="  font-size:18px; ">
        	　<? echo $rec_record_cno; ?>
        </td>
    </tr>

</table>
<script> 

</script>		
</body>