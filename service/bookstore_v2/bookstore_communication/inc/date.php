<?php 
//這邊是設定時間的參數
$star_time = array();
//============周============
$first_day = 0;
$getdate = date("Y-m-d");
//取得一周的第幾天,星期天開始0-6
$weekday = date("w", strtotime($getdate));
//要減去的天數
$del_day = $weekday - $first_day;
//本週開始日期
$week_s = date("Y-m-d", strtotime("$getdate -".$del_day." days"));
$star_time["now_week"] = $week_s;
//上週開始日期
$star_time["last_week"] = date('Y-m-d',strtotime("$week_s - 7 days"));


//============月============	
//本月開始日期
$month_s = date('Y-m-01');
$star_time["now_month"] = $month_s;
//上月開始日期
$_tmptime = strtotime($month_s);
$_tmptime = strtotime('-1 month', $_tmptime); 
$star_time["last_month"] = date("Y-m-d", $_tmptime);  



//============年============
$month = (int)date('m');

$year = date('Y'); 
if($month < 2)
{
	$flag = 2;	
	$year_s = date('Y-08-01',strtotime("-1 year"));
}
else if($month<8)
{
	$flag = 1;	
	$year_s = date('Y-02-01');
}
else
{
	$flag = 2;	
	$year_s = date('Y-08-01');
}
//這學期
$star_time["now_semester"] = $year_s;

//學年
if($flag == 2)
{
	$flag = 1;
	$_tmptime = strtotime($year_s);
	$_tmptime = strtotime('-6 month', $_tmptime); 
	$star_time["last_semester"] = date("Y-m-d", $_tmptime);  
}
else
{
	$flag = 1;
	$_tmptime = strtotime($year_s);
	$_tmptime = strtotime('-6 month', $_tmptime); 
	$star_time["last_semester"] = date("Y-m-d", $_tmptime);
}

?>