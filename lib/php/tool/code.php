<?php
//-------------------------------------------------------
//tool
//-------------------------------------------------------
//const_print()     列印出自訂常數
//func_load()       動態載入函式
//func_print()      列印出自訂函數
//robots()          防止網站登錄
//server_print()    列印出SERVER變數
//session_end()     終止SESSION
//session_print()   列印出SESSION變數

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //外掛函式檔
        require_once(preg_replace('/\s+/','','func/const_print  /code.php'));
        require_once(preg_replace('/\s+/','','func/func_load    /code.php'));
        require_once(preg_replace('/\s+/','','func/func_print   /code.php'));
        require_once(preg_replace('/\s+/','','func/robots       /code.php'));
        require_once(preg_replace('/\s+/','','func/server_print /code.php'));
        require_once(preg_replace('/\s+/','','func/session_end  /code.php'));
        require_once(preg_replace('/\s+/','','func/session_print/code.php'));
?>