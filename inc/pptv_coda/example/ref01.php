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

    //---------------------------------------------------
    //函式: pptv_coda()
    //用途: 設置畢保德測驗次數額度
    //---------------------------------------------------
    //$db_type      mysql (預設)
    //$arry_conn    資料庫連線資訊陣列
    //$APP_ROOT     網站根目錄
    //$user_id      使用者主索引
    //$coda         額度
    //---------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",3)."config/config.php");
        require_once(str_repeat("../",1)."code.php");

        echo pptv_coda($db_type='mysql',$arry_conn_mssr,$APP_ROOT,$user_id=5030,$coda=1);
?>
