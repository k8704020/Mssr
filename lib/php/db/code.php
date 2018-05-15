<?php
//-------------------------------------------------------
//db
//-------------------------------------------------------
//db_num_rows()     取得資料筆數
//db_result()       資料集陣列
//db_result_array() 傳回查詢結果集陣列
//dump_table()      資料表資料傾倒
//dump_table_ddl()  資料表結構傾倒
//dump_tbl_list()   取回資料庫下所有資料表
//mutiple_query()   複合查詢
//mysql_prep()      SQL脫序函式

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //外掛函式檔
        require_once(preg_replace('/\s+/','','func/db_num_rows    /code.php'));
        require_once(preg_replace('/\s+/','','func/db_result      /code.php'));
        require_once(preg_replace('/\s+/','','func/db_result_array/code.php'));
        require_once(preg_replace('/\s+/','','func/dump_table     /code.php'));
        require_once(preg_replace('/\s+/','','func/dump_table_ddl /code.php'));
        require_once(preg_replace('/\s+/','','func/dump_tbl_list  /code.php'));
        require_once(preg_replace('/\s+/','','func/mutiple_query  /code.php'));
        require_once(preg_replace('/\s+/','','func/mysql_prep     /code.php'));
?>