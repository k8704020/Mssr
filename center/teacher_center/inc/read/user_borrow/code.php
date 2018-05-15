<?php
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  read/user_borrow/get_book_borrow_tmp_info() 提取借還書資料表資訊

      //---------------------------------------------------
      //設定與引用
      //---------------------------------------------------

        //外掛函式檔
        $_funcs=array(
            trim('get_book_borrow_tmp_info ')
        );

        foreach($_funcs as $inx=>$_func){
            if(!function_exists("{$_func}")){
                require_once("{$_func}/code.php");
            }
        }
?>