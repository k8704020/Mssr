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

    //---------------------------------------------------
    //函式: arrys_users()
    //用途: 班級的學生
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$class_code       學期代號
    //$date             日期,預設不分日期
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",7)."config/config.php");
        require_once(str_repeat("../",1)."code.php");

        $class_code='gcp_2012_2_1_1';
        $date=date("Y-m-d");
        $arrys_users=arrys_users($conn_user='',$class_code,$date,$arry_conn_user);

        echo "<pre>";
        print_r($arrys_users);
        echo "</pre>";
?>