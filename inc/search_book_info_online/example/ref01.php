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
    //函式: search_book_info_online()
    //用途: 查找線上書籍資訊
    //---------------------------------------------------
    //$book_code    書籍代碼
    //
    //---------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",3)."config/config.php");
        require_once(str_repeat("../",1)."code.php");

 		//輸入識別碼
        $book_code='9787540474065';
        //$book_code='9789575709860';
        //$book_code='9789573321742';

        $search_book_info_online=search_book_info_online($book_code);

        echo "<Pre>";
        print_r($search_book_info_online);
        echo "</Pre>";
?>