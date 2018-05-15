<?php
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  forum/update_setting_class/update_setting_class_user_upload()   更新班級條件(使用者上傳)
//

      //---------------------------------------------------
      //設定與引用
      //---------------------------------------------------

        //外掛函式檔
        $_funcs=array(
            trim('update_setting_class_user_upload   ')
        );

        foreach($_funcs as $inx=>$_func){
            if(!function_exists("{$_func}")){
                require_once("{$_func}/code.php");
            }
        }
?>