<head>
<script src="../../../../lib/jquery/basic/code.js"></script>
</head>
<body>
<?
//---------------------------------------------------
// 書籍報表 : 販賣紀錄INFO
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
$data = array();
$sql = "
				SELECT mssr_book_booking.book_sid,booking_from
		
				FROM  `mssr_book_booking`
				 
				LEFT JOIN mssr_book_category_rev
				ON mssr_book_category_rev.book_sid = mssr_book_booking.book_sid
				
				LEFT JOIN mssr_book_category
				ON mssr_book_category_rev.cat_code = mssr_book_category.cat_code
				
				LEFT JOIN mssr_branch
				ON mssr_branch.branch_name = mssr_book_category.cat_name
				WHERE `mssr_book_booking`.`booking_to` = '{$user_id}'
				AND mssr_branch.branch_id = '{$branch_id}'
				AND mssr_book_booking.keyin_cdate >= '2013-09-01'
				
				";
$count = 0 ;
$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);	
foreach($retrun as $key1=>$val1)
{
		$get_book_info=get_book_info($conn='',$val1['book_sid'],array("book_name"),$arry_conn_mssr);
		if(mb_strlen($get_book_info[0]["book_name"])>10)$get_book_info[0]["book_name"]=mb_substr($get_book_info[0]["book_name"],0,10)."..";
		$data[$count]["book_name"] = $get_book_info[0]["book_name"];
		
		$sql = "SELECT name
				FROM  `member` 
				WHERE uid = ".$val1["booking_from"]."";
		$retrun2 = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);	
		$data[$count]["name"] = $retrun2[0]["name"];
		$data[$count]["type"] = "尚未閱讀";
}
$sql = "
				SELECT mssr_book_booking_log.book_sid,booking_from
		
				FROM  `mssr_book_booking_log`
				 
				LEFT JOIN mssr_book_category_rev
				ON mssr_book_category_rev.book_sid = mssr_book_booking_log.book_sid
				
				LEFT JOIN mssr_book_category
				ON mssr_book_category_rev.cat_code = mssr_book_category.cat_code
				
				LEFT JOIN mssr_branch
				ON mssr_branch.branch_name = mssr_book_category.cat_name
				WHERE `mssr_book_booking_log`.`booking_to` = '{$user_id}'
				AND mssr_branch.branch_id = '{$branch_id}'
				AND mssr_book_booking_log.booking_sdate >= '2013-09-01'
				AND booking_state = '完成交易'
				
				";
$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);	
foreach($retrun as $key1=>$val1)
{
		$get_book_info=get_book_info($conn='',$val1['book_sid'],array("book_name"),$arry_conn_mssr);
		if(mb_strlen($get_book_info[0]["book_name"])>10)$get_book_info[0]["book_name"]=mb_substr($get_book_info[0]["book_name"],0,10)."..";
		$data[$count]["book_name"] = $get_book_info[0]["book_name"];
		
		$sql = "SELECT name
				FROM  `member` 
				WHERE uid = ".$val1["booking_from"]."";
		$retrun2 = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);	
		$data[$count]["name"] = $retrun2[0]["name"];
		$data[$count]["type"] = "已閱讀";
}

//---------------------------------------------------
//Html
//---------------------------------------------------
?>

<table style="font-size:18px; position:absolute; top:26px;">
    <? for($i=0 ; $i < sizeof($retrun);$i++){
		echo "<tr>";
    	echo '<td width="200"   align="left">';
		echo $data[$i]["book_name"];
		echo "</td>";
		echo '<td width="80"  align="center">';
		echo $data[$i]["name"];
		echo "</td>";
		echo '<td width="80"  align="center">';
		echo $data[$i]["type"];
		echo "</td>";
		echo "</tr>";
	} ?> 
</table>   

<table style="position : fixed; top:0px; left:0px; background-color:#FFFFFF; font-size:22px; ">
	<tr>
    	<td width="200"   align="center">
        書籍名稱
        </td>
        <td width="100"  align="center">
        購買人
        </td>
        <td width="100"  align="center">
        交易狀況
        </td>
    </tr>
</table> 
<script> 

</script>		
</body>