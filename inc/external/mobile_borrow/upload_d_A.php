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

    //引用函式檔
        require_once("mobile_code.php");

    //實體化
        $omobile_borrow =new mobile_borrow();

    //檢核書籍資訊
        $uid            =(int)5030;
        $school_code    =trim('gcp');
        $book_code      =trim('9783110336191');

    //新增推薦
        $add_rec_book_draw=$omobile_borrow->add_rec_book_draw($uid,$book_sid='mbl1201310091653558761791');
?>