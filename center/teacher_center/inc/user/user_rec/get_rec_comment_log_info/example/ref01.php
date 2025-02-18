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
    //函式: get_rec_comment_log_info()
    //用途: 提取老師對推薦內容評論表資訊
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$user_id          評論人主索引
    //$arrys_rec_sid    推薦識別碼陣列
    //$array_filter     欄位條件,   預設空陣列 => 全部撈取
    //$arry_limit       資料筆數限制陣列(等同LIMIT inx,size)
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",7)."config/config.php");
        require_once(str_repeat("../",1)."code.php");

 		//使用者主索引
        $user_id=6;
        $arrys_rec_sid=array(
            'mrbs120130825005256641003',
            'mrbs120130825005258892697'
        );

        $get_rec_comment_log_info=get_rec_comment_log_info($conn='',$user_id,$arrys_rec_sid,$array_filter=array(),$arry_limit=array(),$arry_conn_mssr);
        echo "<pre>";
        print_r($get_rec_comment_log_info);
        echo "</pre>";
?>