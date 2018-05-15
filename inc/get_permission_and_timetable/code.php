<?php
//-------------------------------------------------------
//函式: get_permission_and_timetable()
//用途: 提取使用者權限與權限的時間
//日期: 2014年11月18日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function get_permission_and_timetable($conn='',$permission,$status,$arry_conn){
    //---------------------------------------------------
    //函式: get_user_info()
    //用途: 提取使用者權限與權限的時間
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$permission       權限名稱
	//$status			判別地進入權限名稱
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------
	
		//檢核參數
		if((!$arry_conn)||(empty($arry_conn))){
            $err='get_permission_and_timetable:NO ARRY_CONN';
            die($err);
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
                $err ='get_permission_and_timetable:CONNECT FAIL';
                die($err);
            }
        }else{
            $has_conn=false;
        }


        //初始部分
		$result_data = array(
						'permission_ok' => 0,
						'time_ok' => 0,
						'permission_msg' => "",
						'time_msg' => "");
						
		$day = date("w");
		$day_text = "";
		if($day == 0) $day_text = "Sun";
		else if($day == 1) $day_text = "Mon";
		else if($day == 2) $day_text = "Tue";
		else if($day == 3) $day_text = "Wed";
		else if($day == 4) $day_text = "Thu";
		else if($day == 5) $day_text = "Fri";
		else if($day == 6) $day_text = "Sat";
		
		
		//資料庫 判別權限===========1
		$sql = "SELECT count(1) as count
				FROM permissions
				WHERE permission = '".$permission."'
				AND status = '$status'";		
		
        $err='get_permission_and_timetable:QUERY FAIL';
        $result=$conn->prepare($sql);
        $result->execute() or
        die($err);
	
		 //建立資料集陣列
        $arrys_1=array();

        if(($result->rowCount())!==0){
        //有資料存在
            while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                $arrys_1[]=$arry_row;
            }
        }
		if($arrys_1[0]["count"]>=1)
		{
			$result_data["permission_ok"] = 1;
			$result_data["permission_msg"] = "";	
		}
		else
		{
			$result_data["permission_ok"] = 0;
			$result_data["permission_msg"] = "你沒有權限進入這裡喔，請按上一頁返回吧";	
				
		}
		
		
		//資料庫 判別時間===========2
		$sql = "SELECT ".$day_text."Start AS Start ,
				   	   ".$day_text."End AS End
				FROM timetable
				WHERE permission = '".$permission."'";
		$err='get_permission_and_timetable:QUERY FAIL';
        $result=$conn->prepare($sql);
        $result->execute() or
        die($err);
		
		//建立資料集陣列
        $arrys_2=array();

        if(($result->rowCount())!==0){
        //有資料存在
            while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                $arrys_2[]=$arry_row;
            }
        }
		
		$sql = "SELECT count(1) as count
				FROM permissions
				WHERE permission = '".$permission."'
				AND status = '$status'";
        $err='get_permission_and_timetable:QUERY FAIL';
        $result=$conn->prepare($sql);
        $result->execute() or
        die($err);
		
		//建立資料集陣列
        $arrys_3=array();

        if(($result->rowCount())!==0){
        //有資料存在
            while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                $arrys_3[]=$arry_row;
            }
        }
		
		if($arrys_3[0]["count"]>=1)
		{
			$result_data["time_ok"] = 1;
			$result_data["time_msg"] = "";	
		}
		else
		{
			$result_data["time_ok"] = 0;
			$result_data["time_msg"] = "現在非使用時間喔，請在 ".$arrys_2[0]["Start"]." ~ ".$arrys_2[0]["End"]."之間使用系統";	
				
		}







        //傳回資料集陣列
        return $result_data;


        if($has_conn==true){
            $conn=NULL;
        }
	
	}
?>