<?php
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  user_info/registration_code_pwd/update_registration_code_pwd()    更新閱讀登記條碼版專用密碼
//

      //---------------------------------------------------
      //設定與引用
      //---------------------------------------------------

        //外掛函式檔
        $_funcs=array(
            trim('update_registration_code_pwd   ')
        );

        foreach($_funcs as $inx=>$_func){
            if(!function_exists("{$_func}")){
                require_once("{$_func}/code.php");
            }
        }
?>