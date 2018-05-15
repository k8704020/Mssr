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
    //函式: get_book_info()
    //用途: 提取書本資訊
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$book_sid         書籍識別碼
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",7)."config/config.php");
        require_once(str_repeat("../",1)."code.php");

        //$book_sid='mbc1201301010000000000001';
        //$book_sid='mbl1201301010000000000001';
        $book_sid='mbg1201301010000000000001';

        $get_book_info=get_book_info($conn='',$book_sid,$arry_conn_mssr);

        echo "<pre>";
        print_r($get_book_info);
        echo "</pre>";
?>