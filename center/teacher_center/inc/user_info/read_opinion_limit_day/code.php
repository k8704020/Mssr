<?php
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  user_info/read_opinion_limit_day/update_read_opinion_limit_day()    更新閱讀登記期限條件
//

      //---------------------------------------------------
      //設定與引用
      //---------------------------------------------------

        //外掛函式檔
        $_funcs=array(
            trim('update_read_opinion_limit_day   ')
        );

        foreach($_funcs as $inx=>$_func){
            if(!function_exists("{$_func}")){
                require_once("{$_func}/code.php");
            }
        }
?>