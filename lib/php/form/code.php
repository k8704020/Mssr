<?php
//-------------------------------------------------------
//form
//-------------------------------------------------------
//db_checkbox()     資料繫結核取方塊
//db_radio()        資料繫結圈選方塊
//db_select()       資料繫結下拉選單
//db_txt()          資料庫繫結文字欄位
//db_txtarea()      資料庫繫結文字欄位

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //外掛函式檔
        require_once(preg_replace('/\s+/','','func/db_checkbox/code.php'));
        require_once(preg_replace('/\s+/','','func/db_radio   /code.php'));
        require_once(preg_replace('/\s+/','','func/db_select  /code.php'));
        require_once(preg_replace('/\s+/','','func/db_txt     /code.php'));
        require_once(preg_replace('/\s+/','','func/db_txtarea /code.php'));
?>