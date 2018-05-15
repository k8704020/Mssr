<?php
//-------------------------------------------------------
//string
//-------------------------------------------------------
//addslashes_deep()             實作addslashes,套用在所有陣列成員
//barcode()                     code39, code128
//br2nl()                       取代br轉nl
//br2nl_deep()                  取代br轉nl,回遞套用
//get_cht_chnnum()              取得中文字筆畫
//isbn_10_to_13()               國際標準書號轉碼(10碼轉13碼)
//isbn_13_to_10()               國際標準書號轉碼(13碼轉10碼)
//mb_chunk_split()              實作多字元版的chunk_split()函式
//mb_encode()                   設定內部字串編碼
//mb_getrnd_string()            隨機取回字串,雙位元,可重複選到
//mb_getrnd_string_unique()     隨機取回字串,雙位元,不可重複選到
//mb_strleft()                  由左取N個字元
//mb_strrev()                   實作多字元版的strrev()函式,反轉字串
//mb_strright()                 由右至左依序取回N個字元
//mb_str_shuffle()              實作多字元版的str_shuffle()函式,打亂字串
//mb_str_split()                實作多字元版的str_split()函式,指定長度分割字串成陣列
//nl2br_deep()                  取代nl轉br,回遞套用
//strchr_fix()                  strchr修正,模仿php5.3,支援往前取
//stripslashes_deep()           實作stripslashe,套用在所有陣列成員套用
//stristr_fix()                 stristr修正,模仿php5.3,支援往前取
//strstr_fix()                  strstr修正,模仿php5.3,支援往前取
//str_truncate()                字串截斷

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //外掛函式檔
        require_once(preg_replace('/\s+/','','func/addslashes_deep        /code.php'));
        require_once(preg_replace('/\s+/','','func/barcode                /code.php'));
        require_once(preg_replace('/\s+/','','func/br2nl                  /code.php'));
        require_once(preg_replace('/\s+/','','func/br2nl_deep             /code.php'));
        require_once(preg_replace('/\s+/','','func/get_cht_chnnum         /code.php'));
        require_once(preg_replace('/\s+/','','func/isbn_10_to_13          /code.php'));
        require_once(preg_replace('/\s+/','','func/isbn_13_to_10          /code.php'));
        require_once(preg_replace('/\s+/','','func/mb_chunk_split         /code.php'));
        require_once(preg_replace('/\s+/','','func/mb_encode              /code.php'));
        require_once(preg_replace('/\s+/','','func/mb_getrnd_string       /code.php'));
        require_once(preg_replace('/\s+/','','func/mb_getrnd_string_unique/code.php'));
        require_once(preg_replace('/\s+/','','func/mb_strleft             /code.php'));
        require_once(preg_replace('/\s+/','','func/mb_strrev              /code.php'));
        require_once(preg_replace('/\s+/','','func/mb_strright            /code.php'));
        require_once(preg_replace('/\s+/','','func/mb_str_shuffle         /code.php'));
        require_once(preg_replace('/\s+/','','func/mb_str_split           /code.php'));
        require_once(preg_replace('/\s+/','','func/nl2br_deep             /code.php'));
        require_once(preg_replace('/\s+/','','func/strchr_fix             /code.php'));
        require_once(preg_replace('/\s+/','','func/stripslashes_deep      /code.php'));
        require_once(preg_replace('/\s+/','','func/stristr_fix            /code.php'));
        require_once(preg_replace('/\s+/','','func/strstr_fix             /code.php'));
        require_once(preg_replace('/\s+/','','func/str_truncate           /code.php'));
?>