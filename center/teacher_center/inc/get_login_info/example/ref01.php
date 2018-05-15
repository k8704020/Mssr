<?php
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    //SESSION
    @session_start();

    //設定頁面語系
    header("Content-Type: text/html; charset=UTF-8");

    //設定文字內部編碼
    mb_internal_encoding("UTF-8");

    //設定台灣時區
    date_default_timezone_set('Asia/Taipei');

    //---------------------------------------------------
    //函式: get_login_info()
    //用途: 提取登入資訊
    //---------------------------------------------------

        setcookie("t1[id]",   38      ,time()+3600*24,"/");
        setcookie("t1[uid]",  1       ,time()+3600*24,"/");
        setcookie("t1[name]", 'Yayax' ,time()+3600*24,"/");
        $_SESSION['t']=array(
            'id'    =>38    ,
            'uid'   =>1     ,
            'name'  =>'Yayax'
        );

        //外掛設定檔
        require_once(str_repeat("../",1)."code.php");
        $arrys_login_info=get_login_info();

        echo "<Pre>";
        print_r($arrys_login_info);
        echo "</Pre>";
?>
