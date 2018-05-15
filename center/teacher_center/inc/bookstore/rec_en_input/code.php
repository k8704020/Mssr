<?php
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  bookstore/rec_en_input/update_rec_en_input()    更新推薦英文輸入鎖定條件
//

      //---------------------------------------------------
      //設定與引用
      //---------------------------------------------------

        //外掛函式檔
        $_funcs=array(
            trim('update_rec_en_input   ')
        );

        foreach($_funcs as $inx=>$_func){
            if(!function_exists("{$_func}")){
                require_once("{$_func}/code.php");
            }
        }
?>