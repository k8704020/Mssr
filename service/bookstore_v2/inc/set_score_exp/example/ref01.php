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
    //函式: set_score_exp()
    //用途: 儲存經驗值分數
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$exp_type         獲得的經驗值類型
		/*
		rec_draw : 由推薦畫圖獲得
		rec_text : 由推薦文字獲得
		rec_recode : 由推薦錄音獲得
		visit_from : 別人拜訪獲得
		visit_to   : 拜訪別人獲得
		booking_sell: 書籍販賣獲得
		booking_buy : 購買書籍獲得 
		comment_draw : 由畫圖評分獲得
		comment_text : 由文字評分獲得
		comment_recode : 由錄音評分獲得
		*/
    //$exp_score     	獲得的經驗量
	//$user_id			獲得者
    //$arry_conn_mssr        資料庫資訊陣列
    //---------------------------------------------------
        //外掛設定檔
        require_once(str_repeat("../",5)."config/config.php");
        require_once(str_repeat("../",1)."code.php");

		
 		//有雙方互給
        $exp_type="comment_text";//獲得類型
		$user_id = 2;	//授予者
		$from_id = 20;	//給予者		 
		$exp_score = 200;//獲得的經驗數
		$re = set_score_exp($conn='',$exp_type,$exp_score,$user_id,$arry_conn_mssr,$from_id);
		
		echo "<pre>";
        echo $re;
        echo "</pre>";
	
		//系統自動給予
		$exp_type="rec_text";//獲得類型
		$user_id = 2;	//授予者
		//$from_id = "";	//給予者		 
		$exp_score = 200;//獲得的經驗數
		$re = set_score_exp($conn='',$exp_type,$exp_score,$user_id,$arry_conn_mssr);
		
        echo "<pre>";
        echo $re;
        echo "</pre>";
?>