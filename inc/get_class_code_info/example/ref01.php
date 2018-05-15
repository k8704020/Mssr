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
    //函式: get_class_code_info()
    //用途: 提取班級資訊
    //---------------------------------------------------
    //$conn                     資料庫連結物件
    //$school_code              學校代號            預設空字串 => 不分學校
    //$grade                    年級主索引          預設空字串 => 不分年級
    //$compile_class_code_name  是否轉換班級名稱    預設       => 不轉換
    //$arry_conn                資料庫資訊陣列
    //---------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",3)."config/config.php");
        require_once(str_repeat("../",1)."code.php");

        $get_class_code_info=get_class_code_info($conn='',$school_code='',$grade=0,$compile_class_code_name=true,$arry_conn_user);
        echo "<pre>";
        print_r($get_class_code_info);
        echo "</pre>";
?>