<?php
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    //設定頁面語系
    header("Content-Type: text/html; charset=UTF-8");

    //設定文字內部編碼
    mb_internal_encoding("UTF-8");

    //設定台灣時區
    date_default_timezone_set('Asia/Taipei');

    //外掛設定檔
    require_once(str_repeat("../",5)."config/config.php");
    require_once(str_repeat("../",1)."code.php");

    //---------------------------------------------------
    //TEST FOR CONNECT
    //---------------------------------------------------
    //59.124.193.210
    //127.0.0.1

        $conn=mysql_connect(db_host,db_user,db_pass)
        or die(mysql_error());

        $ip2nation_arry=get_ip2nation($conn,$ip='127.0.0.1',$arry_conn);

        echo "<pre>";
        print_r($ip2nation_arry);
        echo "</pre>";
        mysql_close($conn);

    //---------------------------------------------------
    //TEST FOR NO CONNECT
    //---------------------------------------------------
    //59.124.193.210
    //127.0.0.1

        $ip2nation_arry=get_ip2nation($conn='',$ip='59.124.193.210',$arry_conn);

        echo "<pre>";
        print_r($ip2nation_arry);
        echo "</pre>";
?>