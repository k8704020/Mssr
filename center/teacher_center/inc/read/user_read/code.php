<?php
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  read/user_read/get_book_read_opinion_log_info() 提取閱讀調查log表資訊
//  read/user_read/numrow_book_read_group()         學生閱讀本數
//  read/user_read/numrow_book_read_frequency()     學生閱讀次數
//  read/user_read/get_class_code_info_single()     提取班級資訊(修正版)
//  read/user_read/get_class_code_info_easy()       提取班級資訊(簡易版)

      //---------------------------------------------------
      //設定與引用
      //---------------------------------------------------

        //外掛函式檔
        $_funcs=array(
            trim('get_book_read_opinion_log_info    '),
            trim('numrow_book_read_group            '),
            trim('numrow_book_read_frequency        '),
            trim('get_class_code_info_single        '),
            trim('get_class_code_info_easy          ')
        );

        foreach($_funcs as $inx=>$_func){
            if(!function_exists("{$_func}")){
                require_once("{$_func}/code.php");
            }
        }
?>