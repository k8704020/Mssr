<?php
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  root/footer_slogan()    註腳
//  root/header_slogan()    標頭口號
//  root/sys_info()         系統資訊
//

      //---------------------------------------------------
      //設定與引用
      //---------------------------------------------------

        //外掛函式檔
        $_funcs=array(
            trim('footer_slogan '),
            trim('header_slogan '),
            trim('sys_info      ')
        );

        foreach($_funcs as $inx=>$_func){
            if(!function_exists("{$_func}")){
                require_once("{$_func}/code.php");
            }
        }
?>