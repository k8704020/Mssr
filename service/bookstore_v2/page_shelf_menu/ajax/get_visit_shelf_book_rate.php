<?php
//-------------------------------------------------------
//版本編號 1.0
//讀取販售書籍之數量
//ajax
//-------------------------------------------------------

	//---------------------------------------------------
	//輸入 user_id
	//輸出
	//---------------------------------------------------

	//---------------------------------------------------
	//設定與引用
	//---------------------------------------------------

	//SESSION
	@session_start();

	//啟用BUFFER
	@ob_start();

	//外掛設定檔
	require_once(str_repeat("../",4)."/config/config.php");

	 //外掛函式檔
	$funcs=array(
				APP_ROOT.'inc/code',
				APP_ROOT.'lib/php/db/code',
				);
	func_load($funcs,true);


	//清除並停用BUFFER
	@ob_end_clean();

	//建立連線 user
	$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);




	//-----------------------------------------------
	//通用
	//-----------------------------------------------

	//-------------------------------------------
	//初始化, curl設定
	//-------------------------------------------
		$array =array();
		$array["error"] = "";
		$array["echo"] = "";
		$array["on_shelf_date"] = 0;
		$array["total_visit_count"] = 0;
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------
		$dadad = date("Y-m-d  H:i:s");
        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
       	$home_id        =(isset($_POST['home_id']))?(int)$_POST['home_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;

		//檢查資料正確性
		$book_sid = mysql_prep($_POST['book_sid']);


		if($user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------

		$dadad = date("Y-m-d  H:i:s");
		$today=date("Y-m-d");
	

		//撈出現在上架的書籍的這次上架時間
		$sql= "
				SELECT on_shelf_date
				FROM  `mssr_rec_on_off_shelf_log` 
				WHERE user_id='$home_id'
				AND book_sid='$book_sid'
				AND off_shelf_date = '0000-00-00 00:00:00'
				ORDER BY on_shelf_date DESC
				LIMIT 1
				;

				";
		$return = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);


		
		if(empty($return)){
			
				$array["on_shelf_date"] =0;

		}else{
				$array["on_shelf_date"] = $return[0]["on_shelf_date"];
		}


		$on_shelf_date=date("Y-m-d",strtotime($array["on_shelf_date"]));

		$datearray["on_shelf_date"] =$on_shelf_date;



		// print_r($datearray["on_shelf_date"]);

		//這次上架所累積的次數

		$array["this_time_visit_count"] = 0;



		$sql1 = "
				SELECT 
					IFNULL((
							sum(`visit_cno`) 
					),0 ) as `this_time_visit_count`
				FROM `mssr_visit_on_shelf_book`
				WHERE `visit_to` = '$home_id'
				AND  `book_sid`='$book_sid'

				";

		$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql1,$arry_limit=array(),$arry_conn_mssr);

				// echo $sql1; print_r($retrun3);die();

		if(empty($retrun3)){
			
				$array["this_time_visit_count"]=0;

		}else{

				$array["this_time_visit_count"]= $retrun3[0]["this_time_visit_count"];

		}

		
		

		//撈出從上架到現在平均一天有多少瀏覽率

		$sql2 = "
						SELECT 
							IFNULL((
								sum(`visit_cno`) 
							),0 ) as `total_visit_count`
						FROM `mssr_visit_on_shelf_book`
						WHERE `visit_to` = '$user_id'
						AND  `book_sid`='$book_sid'
						";

		$return_visit_cno = db_result($conn_type='pdo',$conn_mssr,$sql2,$arry_limit=array(),$arry_conn_mssr);

		if(empty($return_visit_cno )){
			
					$array["total_visit_count"]=0;

		}else{
					$array["total_visit_count"]= $return_visit_cno [0]["total_visit_count"];

		}

		// $array["avg_visit_count"]=$today-$array["on_shelf_date"] ;
		// print_r($array["avg_visit_count"]);
		// echo $sql1;
		// print_r($array["total_visit_count"]);

		// die();

		$today = strtotime(date("Y-m-d"));
		$on_shelf_date = strtotime($array["on_shelf_date"]);
		$diff = ( $today-$on_shelf_date ) / 86400;
			
		if($diff==0){
				$diff=1;
		}

		$array["avg_visit_count"]=floor($array["total_visit_count"]/$diff);




		//撈出從以前到現在這本書所有累積的瀏覽率(不管上架或下架幾次)


		$sql3 = "
				SELECT count(*)  as total_visit_count
				FROM  `mssr_visit_on_shelf_book_log` 
				WHERE visit_to='$home_id'
				AND book_sid='$book_sid'

				;

				";
		$return_total_visit_count= db_result($conn_type='pdo',$conn_mssr,$sql3,$arry_limit=array(),$arry_conn_mssr);


		if(empty($return_total_visit_count)){
			
				$array["total_visit_count"] =0;

		}else{
				$array["total_visit_count"] =$return_total_visit_count[0]["total_visit_count"];
		}



		//計算從以前上下架 平均一天的瀏覽率(不管上下架幾次)

		$sql4="
				SELECT `on_shelf_date`,`off_shelf_date`
				FROM `mssr_rec_on_off_shelf_log` 
				WHERE `book_sid`='$book_sid' 
				AND `user_id`=$home_id;


			  ";

		$shelf_date = db_result($conn_type='pdo',$conn_mssr,$sql4,$arry_limit=array(),$arry_conn_mssr);


		// $day=array();

		$total=0;

		foreach($shelf_date as $key => $val){

			$firstTime=strtotime($val["on_shelf_date"]);
			$lastTime=strtotime($val["off_shelf_date"]);
			
			if($lastTime==""){

				$lastTime=0;

			}else{

				$lastTime=strtotime($val["off_shelf_date"]);

			}

			$difference=floor (( $lastTime-$firstTime ) / 86400);

			if($difference<0){

				$total=0;

			}else{

				$total+=$difference;
			}


			// $day[]=$difference;
				
		} 

		if($total>0){

			$array["avg_every_count"]=floor($array["total_visit_count"]/$total);

		}else{

			$array["avg_every_count"]=0;
		}

		// print_r($difference); die();





		// 書名

		$sql5="

				SELECT `book_name`
				FROM  `mssr_book_class` 
				WHERE  `book_sid` = '$book_sid'

				union 

				SELECT  `book_name` 
				FROM  `mssr_book_library` 
				WHERE  `book_sid` = '$book_sid'
		
				union

				SELECT `book_name`
				FROM  `mssr_book_global` 
				WHERE  `book_sid` = '$book_sid'

				union

				SELECT  `book_name` 
				FROM  `mssr_book_unverified` 
				WHERE  `book_sid` =  '$book_sid'
				
			 	
			  ";

		$return_book_name = db_result($conn_type='pdo',$conn_mssr,$sql5,$arry_limit=array(),$arry_conn_mssr);
   		
		

		if(empty($return_book_name)){
			
				$array["book_name"] =0;

		}else{
				$array["book_name"] =$return_book_name[0]["book_name"];
		}




		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);


		echo json_encode($array,1);




		
		?>