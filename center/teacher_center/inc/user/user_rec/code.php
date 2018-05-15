<?php
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  user/user_rec/numrow_book_rec()             學生推薦數
//  user/user_rec/get_rec_comment_log_info()    提取老師對推薦內容評論表資訊
//  user/user_rec/get_rec_book_cno_info()       提取書本推薦內容總調查計數表資訊

      //---------------------------------------------------
      //設定與引用
      //---------------------------------------------------

        //外掛函式檔
        $_funcs=array(
            trim('numrow_book_rec           '),
            trim('get_rec_comment_log_info  '),
            trim('get_rec_book_cno_info     ')
        );

        foreach($_funcs as $inx=>$_func){
            if(!function_exists("{$_func}")){
                require_once("{$_func}/code.php");
            }
        }
?>