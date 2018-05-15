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
    //函式: get_book_borrow_tmp_info()
    //用途: 提取借還書資料表資訊
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$user_id          使用者主索引
    //$array_filter     欄位條件,   預設空陣列 => 全部撈取
    //$date_filter      日期條件,   預設0      => 全部天數
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",7)."config/config.php");
        require_once(str_repeat("../",1)."code.php");

 		//使用者主索引
        $user_id=2;

        $get_book_borrow_tmp_info=get_book_borrow_tmp_info($conn='',$user_id,$array_filter=array('book_sid'),$date_filter=14,$arry_conn_mssr);
        echo "<pre>";
        print_r($get_book_borrow_tmp_info);
        echo "</pre>";
?>