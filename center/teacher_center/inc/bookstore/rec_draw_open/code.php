<?php
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  bookstore/rec_draw_open/update_rec_draw_open()  更新推薦畫圖開放條件
//

      //---------------------------------------------------
      //設定與引用
      //---------------------------------------------------

        //外掛函式檔
        $_funcs=array(
            trim('update_rec_draw_open   ')
        );

        foreach($_funcs as $inx=>$_func){
            if(!function_exists("{$_func}")){
                require_once("{$_func}/code.php");
            }
        }
?>