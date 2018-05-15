<?php
//-------------------------------------------------------
//inc
//-------------------------------------------------------
//borrow_return_book    借還書系統
//user_info             教師專用功能
//
//-------------------------------------------------------
//borrow_return_book
//-------------------------------------------------------
//  borrow_book/arrys_book_library()                    圖書館書籍陣列
//  borrow_book/arrys_users()                           班級的學生
//  borrow_book/book_borrow_school_rev()                借閱書學校關聯
//  borrow_book/book_borrow_sid()                       借閱識別碼
//  borrow_book/get_user_library_card_info()            提取使用者借書證資訊
//  borrow_book/isbn_code_remind()                      isbn碼輸入提醒
//
//-------------------------------------------------------
//user_info
//-------------------------------------------------------
//  user_info/update_read_the_registration_code_pwd()   更新閱讀登記條碼版專用密碼
//
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //外掛函式檔
        require_once(preg_replace('/\s+/','','borrow_return_book    /code.php'));

        require_once(preg_replace('/\s+/','','user_info             /code.php'));
?>