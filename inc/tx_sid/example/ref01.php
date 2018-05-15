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
    //函式: tx_sid()
    //用途: 交易識別碼
    //---------------------------------------------------
    //$create_by    建立者
    //$tx_type      交易類型    tx_sys | tx_gift
    //$encode       頁面編碼
    //
    //---------------------------------------------------
    //字首:
    //      tx_sys部分
    //          mts + create_by(建立者) + YYYYMMDDhhiiss + 亂數組成，共25碼，
    //          mts + 1 + 20130101000000 + 0000001
    //
    //      tx_gift部分
    //          mtg + create_by(建立者) + YYYYMMDDhhiiss + 亂數組成，共25碼，
    //          mtg + 1 + 20130101000000 + 0000001
    //---------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",1)."code.php");
        echo tx_sid(1,'tx_sys',mb_internal_encoding());
        echo '<br/>';
        echo tx_sid(1,'tx_gift',mb_internal_encoding());
?>
