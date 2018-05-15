<?php
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  user_info/open_publish/update_open_publish()    更新上架條件
//

      //---------------------------------------------------
      //設定與引用
      //---------------------------------------------------

        //外掛函式檔
        $_funcs=array(
            trim('update_open_publish   ')
        );

        foreach($_funcs as $inx=>$_func){
            if(!function_exists("{$_func}")){
                require_once("{$_func}/code.php");
            }
        }
?>