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
    //函式: set_user_vote_book_page()
    //用途: 提取推薦資訊
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$user_id          使用者
    //$book_sid         書籍識別碼
    //$book_page         填寫的頁數
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",3)."config/config.php");
		require_once(str_repeat("../",3)."/service/read_the_registration_v2/inc/tx_gift_sid/code.php");
        require_once(str_repeat("../",1)."code.php");
		
		
		$book_sid = "mbl1201310091653561202409";
		$book_page = 183;
		$user_id = 2;
		set_user_vote_book_page($conn_mssr,$user_id,$book_sid,$book_page,$arry_conn_mssr);
		/*
		//連發測試用  別管...
		user_id = 3;
		set_user_vote_book_page($conn_mssr,$user_id,$book_sid,$book_page,$arry_conn_mssr);
		$user_id = 4;
		set_user_vote_book_page($conn_mssr,$user_id,$book_sid,$book_page,$arry_conn_mssr);
		$user_id = 5;
		set_user_vote_book_page($conn_mssr,$user_id,$book_sid,$book_page,$arry_conn_mssr);
		$user_id = 6;
		set_user_vote_book_page($conn_mssr,$user_id,$book_sid,$book_page,$arry_conn_mssr);
		$user_id = 7;
		set_user_vote_book_page($conn_mssr,$user_id,$book_sid,$book_page,$arry_conn_mssr);
		$user_id = 8;
		set_user_vote_book_page($conn_mssr,$user_id,$book_sid,$book_page,$arry_conn_mssr);
		$user_id = 9;
		set_user_vote_book_page($conn_mssr,$user_id,$book_sid,$book_page,$arry_conn_mssr);
		$user_id = 10;
		set_user_vote_book_page($conn_mssr,$user_id,$book_sid,$book_page,$arry_conn_mssr);
		$user_id = 11;
		set_user_vote_book_page($conn_mssr,$user_id,$book_sid,$book_page,$arry_conn_mssr);*/
?>