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
    //函式: book_borrow_school_rev()
    //用途: 借閱書學校關聯
    //---------------------------------------------------
    //$db_type              mysql (預設)
    //$arry_conn            資料庫連線資訊陣列
    //$borrow_school_from   借閱的學校代號
    //
    //---------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",7)."config/config.php");
        require_once(str_repeat("../",1)."code.php");

        echo book_borrow_school_rev($db_type='mysql',$arry_conn_mssr,$borrow_school_from='gcp');
?>
