<?php
//-------------------------------------------------------
//函式: set_score_exp()
//用途: 儲存經驗值分數
//日期: 2015年06月16日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function set_score_exp($conn='',$exp_type,$exp_score,$user_id,$arry_conn,$from_id){
    //---------------------------------------------------
    //函式: set_score_exp()
    //用途: 儲存經驗值分數
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$exp_type         獲得的經驗值類型
		/*
		rec_draw : 由推薦畫圖獲得 OK
		rec_text : 由推薦文字獲得 OK
		rec_recode : 由推薦錄音獲得 OK
		
		visit_from : 別人拜訪獲得 OK
		visit_to   : 拜訪別人獲得 OK 
		
		booking_sell: 書籍販賣獲得 OK
		booking_buy : 購買書籍獲得 OK
		
		comment_draw : 由畫圖評分獲得
		comment_text : 由文字評分獲得
		comment_recode : 由錄音評分獲得
		*/
    //$exp_score     	獲得的經驗量
	//$user_id			獲得者
    //$arry_conn_mssr        資料庫資訊陣列
    //---------------------------------------------------

        //檢核參數
        if(!isset($exp_type)||(trim($exp_type)==='')){
            $err='SET_SCORE_EXP:NO EXP_TYPE';
            die($err);
        }
		if(!isset($exp_score)||(trim($exp_score)==='')){
            $err='SET_SCORE_EXP:NO EXP_SCORE';
            die($err);
        }
		if(!isset($user_id)||(trim($user_id)==='')){
            $err='SET_SCORE_EXP:NO USER_ID';
            die($err);
        }
 		if((!$arry_conn)||(empty($arry_conn))){
            $err='SET_SCORE_EXP:NO ARRY_CONN';
            die($err);
        }
		if(!isset($from_id)||(trim($from_id)==='')||is_null($from_id)){
            $from_id= 1;
            
        }

        //資料庫資訊
        $db_host  =$arry_conn['db_host'];
        $db_user  =$arry_conn['db_user'];
        $db_pass  =$arry_conn['db_pass'];
        $db_name  =$arry_conn['db_name'];
        $db_encode=$arry_conn['db_encode'];


        //連結物件判斷
        $has_conn=false;

        if(!$conn){
            $has_conn=true;

            $conn_info='mysql'.":host={$db_host}".";dbname={$db_name}";
            $options = array(
                PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,           //設置錯誤提示，只獲取代碼
                PDO::ATTR_CASE,              PDO::CASE_NATURAL,             //列名按照原始的方式
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode}"    //設置語系
            );

            try{
                $conn=@new PDO($conn_info, $db_user, $db_pass,$options);
            }catch(PDOException $e){
                $err ='SET_SCORE_EXP:CONNECT FAIL';
                die($err);
            }
        }else{
            $has_conn=false;
        }



		
        //mssr_score_exp_log寫入LOG
		$sql = "INSERT INTO `mssr`.`mssr_score_exp_log`
								(
								`from_id`,
								 `user_id`,
								 `exp_type`,
								 `exp_score`,
								 `keyin_cdate`,
								 `keyin_ip`) 
						VALUES  
								(
								 ".$from_id.",
								 ".$user_id.",
								 '".$exp_type."',
								 ".$exp_score.",
								 '".date("Y-m-d  H:i:s")."',
								 '".$_SERVER["REMOTE_ADDR"]."'); ";
		//資料庫
        $err='SET_SCORE_EXP:QUERY FAIL';
        $result=$conn->prepare($sql);
        $result->execute() or
        die($err);
		
		
		//mssr_user_info增加經驗值
		$sql = "UPDATE `mssr`.`mssr_user_info`
				SET `score_exp` = `score_exp` + ".$exp_score."
				WHERE `mssr_user_info`.`user_id` = ".$user_id.";";			
		//資料庫
        $err='SET_SCORE_EXP:QUERY FAIL';
        $result=$conn->prepare($sql);
        $result->execute() or
        die($err);
        


        //傳回資料集陣列
        return  "OK";

        if($has_conn==true){
            $conn=NULL;
        }
    }
?>