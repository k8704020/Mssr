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


$data = array(); // 確認資料


$sql = "SELECT mssr_user_task_log.task_sid,
			   mssr_user_task_log.task_sdl_goal,
			   mssr_user_task_log.task_coin_unit_price,
			   mssr_user_task_log.task_coin_bonus,
			   LEFT(mssr_user_task_log.task_edate,10) AS task_edate,
			   mssr_user_task_log.task_state,
			   mssr_task_period.task_name
		FROM  `mssr_user_task_log`
		LEFT JOIN mssr_task_period
		ON mssr_user_task_log.task_sid = mssr_task_period.task_sid
		WHERE user_id = $user_id
		AND branch_id = $branch_id
		AND mssr_user_task_log.task_state != '進行中'";
$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
foreach($retrun as $key1=>$val1)
{
	

	
	
	//處理數值
	$data[$key1]["task_state"] = $val1["task_state"];//達成狀態
	$data[$key1]["task_edate"] = $val1["task_edate"];//結束日期
	$data[$key1]["task_coin"] = $val1["task_coin_unit_price"]*$val1["task_coin_bonus"]*$val1["task_sdl_goal"];//任務獎勵
	if($data[$key1]["task_state"]=='失敗')$data[$key1]["task_coin"] = '--';
	$data[$key1]["task_name"] = $val1["task_name"];//任務名稱
	$data[$key1]["task_sid"] = $val1["task_sid"];//任務SID
		
}


//---------------------------------------------------
//html 輸出
//---------------------------------------------------
echo '<table  style="font-size:18px; position:absolute; top:26px;">';
?>

<? for($i = 0  ; $i < sizeof($data) ;  $i++)
{?>
	<tr>
    
    	<td width="189" align="center"><? echo $data[$i]["task_name"];?></td>
        <td width="70" align="left"></td>
        <td width="189" align="left"><? echo $data[$i]["task_state"];?></td>
        <td width="119" align="left"><img src="../../img/bar/coin.png" style=" height:30px;" /><? echo $data[$i]["task_coin"];?></td>
        <td width="189" align="center"><? echo $data[$i]["task_edate"];?></td>
   
    </tr>

	
	
<? }
echo "</table>";
?>

<table style="position : fixed; top:0px; left:0px; background-color:#FFFFFF; font-size:22px;  background-color:#A6D39A;">
	<tr>
    	<td width="189"   align="center">
        任務名稱
        </td>
        <td width="189"  align="center">
        達成狀態
        </td>
        <td width="189"  align="center">
        任務獎勵
        </td>
        <td width="189"  align="center">
        結束日期
        </td>
    </tr>
</table> 




		
