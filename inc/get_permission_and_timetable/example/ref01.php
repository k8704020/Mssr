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
    //函式: get_user_info()
    //用途: 提取使用者權限與權限的時間
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$permission       權限名稱
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",3)."config/config.php");
        require_once(str_repeat("../",1)."code.php");

 		//輸入識別碼
        $permission=$_SESSION['permission'];
		$status = 'u_mssr_bs';
		//輸入要查詢的欄位
	
        $mag=get_permission_and_timetable($conn='',$permission,$status,$arry_conn_user);
		
        echo "<pre>";
        print_r($mag);
        echo "</pre>";
?>