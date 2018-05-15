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
    //函式: arrys_book_class()
    //用途: 班級書籍陣列
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$school_code      學校代號,預設'', 撈出全部
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",7)."config/config.php");
        require_once(str_repeat("../",1)."code.php");

        $school_code='';
        $arrys_book_class=arrys_book_class($conn_mssr='',$school_code,$arry_conn_mssr);

        echo "<pre>";
        print_r($arrys_book_class);
        echo "</pre>";
?>