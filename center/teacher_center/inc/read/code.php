<?php
//-------------------------------------------------------
//book
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  read/user_borrow/get_book_borrow_tmp_info()     提取借還書資料表資訊
//  read/user_read/get_book_read_opinion_log_info() 提取閱讀調查log表資訊
//  read/user_read/numrow_book_read_group()         學生閱讀本數
//  read/user_read/numrow_book_read_frequency()     學生閱讀次數
//  read/user_read/get_class_code_info_single()     提取班級資訊(修正版)
//  read/user_read/get_class_code_info_easy()       提取班級資訊(簡易版)

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //外掛函式檔
        require_once(preg_replace('/\s+/','','user_borrow   /code.php'));
        require_once(preg_replace('/\s+/','','user_read     /code.php'));
?>