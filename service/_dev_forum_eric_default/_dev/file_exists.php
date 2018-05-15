<?php
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    //設定頁面語系
    header("Content-Type: text/html; charset=UTF-8");

    //設定文字內部編碼
    mb_internal_encoding("UTF-8");

    //設定台灣時區
    date_default_timezone_set('Asia/Taipei');

    if(file_exists("C:\wamp\www\new_cl_ncu\mssr\info\forum\group\0\article\11782\503014575966638245849.jpg")){
        echo "<Pre>";
        print_r(123);
        echo "</Pre>";
        die();
    }else{
        echo "<Pre>";
        print_r(456);
        echo "</Pre>";
    }
?>