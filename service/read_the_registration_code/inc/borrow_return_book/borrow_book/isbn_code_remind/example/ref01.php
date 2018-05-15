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

    //-------------------------------------------------------
    //函式: isbn_code_remind()
    //用途: isbn碼輸入提醒
    //-------------------------------------------------------
    //$db_type      mysql (預設)
    //$arry_conn    資料庫連線資訊陣列
    //$user_id      使用者主索引
    //-------------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",7)."config/config.php");
        require_once(str_repeat("../",1)."code.php");

        $isbn_code_remind=isbn_code_remind($db_type='mysql',$arry_conn_mssr,$user_id=1);
        echo $isbn_code_remind;
?>