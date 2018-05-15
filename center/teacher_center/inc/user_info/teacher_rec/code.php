<?php
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  user_info/teacher_rec/book_borrow_school_rev()  借閱書學校關聯

      //---------------------------------------------------
      //設定與引用
      //---------------------------------------------------

        //外掛函式檔
        $_funcs=array(
            trim('book_borrow_school_rev    ')
        );

        foreach($_funcs as $inx=>$_func){
            if(!function_exists("{$_func}")){
                require_once("{$_func}/code.php");
            }
        }
?>