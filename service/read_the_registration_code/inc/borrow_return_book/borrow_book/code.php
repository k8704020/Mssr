<?php
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  borrow_return_book/borrow_book/arrys_book_library()             圖書館書籍陣列
//  borrow_return_book/borrow_book/arrys_users()                    班級的學生
//  borrow_return_book/borrow_book/book_borrow_school_rev()         借閱書學校關聯
//  borrow_return_book/borrow_book/book_borrow_sid()                借閱識別碼
//  borrow_return_book/borrow_book/get_user_library_card_info()     提取使用者借書證資訊
//  borrow_return_book/borrow_book/isbn_code_remind()               isbn碼輸入提醒

      //---------------------------------------------------
      //設定與引用
      //---------------------------------------------------

        //外掛函式檔
        $_funcs=array(
            trim('arrys_book_library            '),
            trim('arrys_users                   '),
            trim('book_borrow_school_rev        '),
            trim('book_borrow_sid               '),
            trim('get_user_library_card_info    '),
            trim('isbn_code_remind              ')
        );

        foreach($_funcs as $inx=>$_func){
            if(!function_exists("{$_func}")){
                require_once("{$_func}/code.php");
            }
        }
?>