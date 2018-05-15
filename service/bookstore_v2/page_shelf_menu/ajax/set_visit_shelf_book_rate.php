<?php
//-------------------------------------------------------
//版本編號 1.0
//讀取使用者資料
//ajax
//-------------------------------------------------------

 	//---------------------------------------------------
	//輸入 user_id
 	//輸出
 	//---------------------------------------------------

 	//---------------------------------------------------
 	//設定與引用
 	//---------------------------------------------------


// 	//SESSION
	@session_start();

// 	//啟用BUFFER
	@ob_start();

// 	//外掛設定檔
	
	require_once(str_repeat("../",4)."/config/config.php");

// 	 //外掛函式檔
	$funcs=array(
				APP_ROOT.'inc/code',
				APP_ROOT.'lib/php/db/code',
				);
	func_load($funcs,true);



// 	//清除並停用BUFFER
	@ob_end_clean();

	//建立連線 user
	$conn_user=conn($db_type='mysql',$arry_conn_user);
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
		// $array["book_count"] = "";

		
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
	

		//先搜尋有無瀏覽過此本書籍
		$sql = "
				SELECT count(1) AS count
				FROM  `mssr`.`mssr_visit_on_shelf_book`
				WHERE `visit_from`='".$user_id."' 
				AND `visit_to`='".$home_id."' 
				AND `book_sid`='".$book_sid."'
				AND `keyin_cdate`='".date("Y-m-d")."'

				";
		

		$retrun= db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

		if(empty($retrun)){

			$book_count = 0;

		}else{

			$book_count = $retrun[0]["count"];

		}

		
		//判斷是否之前有瀏覽過此書籍
		if($book_count >= 1){


			  $sql = "
						UPDATE  `mssr`.`mssr_visit_on_shelf_book` 
						SET 	`visit_cno` =`visit_cno`+1 
						WHERE   `visit_from` = '$user_id' 
						AND     `visit_to` = '$home_id' 
						AND     `book_sid` =  '$book_sid'
						AND     `keyin_cdate`= '".date("Y-m-d")."';


						INSERT INTO  `mssr`.`mssr_visit_on_shelf_book_log`(
								`visit_from`,
								`visit_to`,
								`book_sid`,
								`keyin_cdate`
								
						) VALUES (
								 '$user_id',
								 '$home_id',
								 '$book_sid',
								 '$dadad'
								
						);

						
					";


		}else{

		//從以前至今書店被拜訪的總累積數
				$sql = "
						INSERT INTO  `mssr`.`mssr_visit_on_shelf_book`(
								`visit_from`,
								`visit_to`,
								`book_sid`,
								`visit_cno`,
								`keyin_cdate`
								
						)VALUES(
								 '$user_id',
								 '$home_id',
								 '$book_sid',
								 '1',
								'$dadad'
						);

						INSERT INTO  `mssr`.`mssr_visit_on_shelf_book_log`(
								`visit_from`,
								`visit_to`,
								`book_sid`,
								`keyin_cdate`
								
						) VALUES (
								 '$user_id',
								 '$home_id',
								 '$book_sid',
								 '$dadad'
								
						);

						";
				

		}
						

		$retrun= db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

		 

		

		echo json_encode($array,1);
		// die();
		// echo json_encode($book_count);

		// echo $book_count;

		// print_r($book_sid);
		// return "hel1232132123lo";


		
		?>