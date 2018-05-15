<?php
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  bookstore/coin_open/update_coin_open()      更新葵幣開放條件
//

      //---------------------------------------------------
      //設定與引用
      //---------------------------------------------------

        //外掛函式檔
        $_funcs=array(
            trim('update_coin_open   ')
        );

        foreach($_funcs as $inx=>$_func){
            if(!function_exists("{$_func}")){
                require_once("{$_func}/code.php");
            }
        }
?>