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
    //函式: auth_check()
    //用途: 權限檢核
    //---------------------------------------------------
    //$db_type          mysql (預設)
    //$arry_conn        資料庫連線資訊陣列
    //$user_type        使用者身分
    //$auth_type        權限種類
    //---------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",3)."config/config.php");
        require_once(str_repeat("../",1)."code.php");

        echo auth_check($db_type='mysql',$arry_conn_user,$user_type='gcp_t',$auth_type='mssr_tc');
?>
