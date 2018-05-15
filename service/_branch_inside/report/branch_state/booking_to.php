<head>
<script src="../../../../lib/jquery/basic/code.js"></script>
</head>
<body>
<?
//---------------------------------------------------
// 書籍報表 : 購買紀錄INFO
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
				SELECT mssr_book_booking.book_sid,mssr_book_booking.keyin_cdate
		
				FROM  `mssr_book_booking`
				 
				LEFT JOIN mssr_book_category_rev
				ON mssr_book_category_rev.book_sid = mssr_book_booking.book_sid
				
				LEFT JOIN mssr_book_category
				ON mssr_book_category_rev.cat_code = mssr_book_category.cat_code
				
				LEFT JOIN mssr_branch
				ON mssr_branch.branch_name = mssr_book_category.cat_name
				WHERE `mssr_book_booking`.`booking_from` = '{$user_id}'
				AND mssr_branch.branch_id = '{$branch_id}'
				AND mssr_book_booking.keyin_cdate >= '2013-09-01'
				GROUP BY mssr_book_booking.book_sid
				";

$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);	
foreach($retrun as $key1=>$val1)
{
		$get_book_info=get_book_info($conn='',$val1['book_sid'],array("book_name"),$arry_conn_mssr);
		if(mb_strlen($get_book_info[0]["book_name"])>18)$get_book_info[0]["book_name"]=mb_substr($get_book_info[0]["book_name"],0,18)."..";
		$data[$key1]["book_name"] = $get_book_info[0]["book_name"];
		$data[$key1]["keyin_cdate"] = $val1["keyin_cdate"];
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
		echo '<td width="170"  align="left">';
		echo "尚未去閱讀";
		echo "</td>";
		echo "</tr>";
	} ?> 
</table>   

<table style="position : fixed; top:0px; left:0px; background-color:#FFFFFF; font-size:22px; ">
	<tr>
    	<td width="200"   align="center">
        書籍名稱
        </td>
        <td width="200"  align="center">
        訂閱狀態
        </td>
    </tr>
</table> 

<script> 

</script>		
</body>