<?php

//-------------------------------------------------------
//教師中心
//-------------------------------------------------------


//-------------------------------------------------------

//設定頁面語系
    header("Content-Type: text/html; charset=UTF-8");

//設定文字內部編碼
    mb_internal_encoding("UTF-8");

//設定台灣時區
    date_default_timezone_set('Asia/Taipei');

//--------------------------------------------------------

//---------------------------------------------------
//設定與引用
//---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",4).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/string/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

//$link = mysql_connect($host, $db_user, $db_pass);
//mysql_select_db($db_name, $link);
//mysql_query("SET names UTF8");
//header("Content-Type: text/html; charset=utf-8");
//date_default_timezone_set($timezone); //北京时间



$field=$_POST['id'];
$val=$_POST['value'];



$sql ="update mssr_forum set forum_name='$val' where forum_id='$field' limit 1";

//echo "<Pre>";
//print_r($sql);
//echo "</Pre>";
$arr_date=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


echo $val;

?>

