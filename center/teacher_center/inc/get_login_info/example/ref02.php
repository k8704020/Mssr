<?php
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    //SESSION
    @session_start();

    //設定頁面語系
    header("Content-Type: text/html; charset=UTF-8");

    //設定文字內部編碼
    mb_internal_encoding("UTF-8");

    //設定台灣時區
    date_default_timezone_set('Asia/Taipei');

    echo "<Pre>";
    print_r($_COOKIE);
    echo "</Pre>";
?>