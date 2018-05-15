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
    //函式: get_rec_book_cno_info()
    //用途: 提取書本推薦內容總調查計數表資訊
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$user_id          使用者主索引
    //$book_sid         書籍識別碼
    //$array_filter     欄位條件,   預設空陣列 => 全部撈取
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",7)."config/config.php");
        require_once(str_repeat("../",1)."code.php");

 		//使用者主索引
        $user_id=2;
        $book_sid='mbc1201301010000000000002';

        $get_rec_book_cno_info=get_rec_book_cno_info($conn='',$user_id,$book_sid,$array_filter=array(),$arry_conn_mssr);
        echo "<pre>";
        print_r($get_rec_book_cno_info);
        echo "</pre>";
?>