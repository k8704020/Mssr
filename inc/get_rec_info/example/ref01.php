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
    //函式: get_rec_info()
    //用途: 提取推薦資訊
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$user_id          使用者主索引
    //$book_sid         書籍識別碼
    //$rec_type         推薦類型
    //$array_filter     欄位條件,   預設空陣列 => 全部撈取
    //$order_by_filter  排序設定,   預設 keyin_cdate(建立時間) DESC
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",3)."config/config.php");
        require_once(str_repeat("../",1)."code.php");
?>