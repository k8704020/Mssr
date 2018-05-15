<?php
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  book/book/arrys_book_class()            班級書籍陣列
//  book/book/arrys_book_library()          圖書館書籍陣列
//  book/book/arrys_users()                 班級的學生
//  book/book/book_class_sid()              班級書籍.識別碼
//  book/book/book_global_sid()             系統書籍.識別碼
//  book/book/book_library_sid()            圖書館書籍.識別碼
//  book/book/book_unverified_sid()         未檢核的書籍.識別碼
//  book/book/find_book_bkl()               查找博客來書籍資訊
//  book/book/find_book_bkl_m()             查找博客來書籍資訊(行動版)
//  book/book/find_book_fbk()               查找Findbook書籍資訊
//  book/book/find_book_fbk_m()             查找Findbook書籍資訊(行動版)
//  book/book/find_book_fbk_img()           查找Findbook書籍的圖片資訊
//  book/book/find_book_kst()               查找金石堂書籍資訊
//  book/book/find_book_kst_m()             查找金石堂書籍資訊(行動版)
//  book/book/find_book_ntu()               查找台灣大學圖書館書籍資訊
//  book/book/get_book_info()               提取書本資訊
//  book/book/get_class_code_info_revise()  提取班級資訊(修正)
//

      //---------------------------------------------------
      //設定與引用
      //---------------------------------------------------

        //外掛函式檔
        $_funcs=array(
            //trim('find_book_bkl             '),
            //trim('find_book_bkl_m           '),
            //trim('find_book_fbk             '),
            //trim('find_book_fbk_m           '),
            trim('find_book_fbk_img         '),
            //trim('find_book_kst             '),
            //trim('find_book_kst_m           '),
            //trim('find_book_ntu             '),
            trim('arrys_book_class          '),
            trim('arrys_book_library        '),
            trim('arrys_users               '),
            trim('book_class_sid            '),
            trim('book_global_sid           '),
            trim('book_library_sid          '),
            trim('book_unverified_sid       '),
            trim('get_book_info             '),
            trim('get_class_code_info_revise')
        );

        foreach($_funcs as $inx=>$_func){
            if(!function_exists("{$_func}")){
                require_once("{$_func}/code.php");
            }
        }
?>