<?php
//-------------------------------------------------------
//版本編號 1.0
//登記書籍  讀取閱讀登記 Q & A
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
				APP_ROOT.'inc/conn/code',
				APP_ROOT.'lib/php/db/code'
				);
	func_load($funcs,true);


	//清除並停用BUFFER
	@ob_end_clean();

	//建立連線 user
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
		$array["echo"] ="";
        //POST


	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------

		//POST
  //      	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		// $user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;

		// if($user_id != $_SESSION["uid"] || $user_permission != $_SESSION["permission"])
		// {
		// 	$array["error"] ="你違法進入了喔!!  請重新登入";
		// 	die(json_encode($array,1));
		// }

	//-------------------------------------------
	//SQL
	//-------------------------------------------



		//=======================================================
		//第一題
		// $qq=array('0'=>'無','1'=>'Read all.','2'=>'More than a  half.','3'=>'Just a little bit.');
		// $aa=serialize($qq);

		// $sql = "INSERT INTO `mssr_book_topic_log`(`create_by`, `school_code`, `school_category`, `grade_id`, `topic_title`, `topic_type`, `topic_options`, `keyin_cdate`) VALUES (1,'system',0,0,'Please choose the reading status.','radio','".$aa."',now())
		// ";



		//第二題
		// $qq=array('0'=>'無','1'=>'Easy','2'=>'Just right','3'=>'Challenging');
		// $aa=serialize($qq);

		// $sql = "INSERT INTO `mssr_book_topic_log`(`create_by`, `school_code`, `school_category`, `grade_id`, `topic_title`, `topic_type`, `topic_options`, `keyin_cdate`) VALUES (1,'system',0,0,'Which level is the book?','radio','".$aa."',now())
		// ";




		//第三題
		// $qq=array('0'=>'無','1'=>'Like','2'=>'Just right','3'=>'Dislike');
		// $aa=serialize($qq);

		// $sql = "INSERT INTO `mssr_book_topic_log`(`create_by`, `school_code`, `school_category`, `grade_id`, `topic_title`, `topic_type`, `topic_options`, `keyin_cdate`) VALUES (1,'system',0,0,'Do you like the book?','radio','".$aa."',now())
		// ";


		//第四題
		// $qq=array('0'=>'無','1'=>'Yes','2'=>'No');
		// $aa=serialize($qq);

		// $sql = "INSERT INTO `mssr_book_topic_log`(`create_by`, `school_code`, `school_category`, `grade_id`, `topic_title`, `topic_type`, `topic_options`, `keyin_cdate`) VALUES (1,'system',0,0,'Do you want to read it again?','radio','".$aa."',now())
		// ";


		//第五題
		// $qq=array('0'=>'無','1'=>'Friends’ Recommendation','2'=>'Attractive Title','3'=>'Attractive cover','4'=>'Attractive introduction','5'=>'I like the author.');
		// $aa=serialize($qq);

		// $sql = "INSERT INTO `mssr_book_topic_log`(`create_by`, `school_code`, `school_category`, `grade_id`, `topic_title`, `topic_type`, `topic_options`, `keyin_cdate`) VALUES (1,'system',0,0,'Why did you choose this book?','radio','".$aa."',now())
		// 	   ";
		//第六題

					$sql="
						SELECT
							`class_code`
						FROM
							`class`

						WHERE
							`grade` = 1

					";


					$arrys_class_code=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);

					foreach ($arrys_class_code as $key => $value) {
						print_r($value[0]['class_code']);
					}

				
					echo $class_code;
	
		// if(count($retrun))
		// {
		// 	foreach($retrun as $key => $val)
		// 	{

		// 		$tmp["topic_id"] = $val["topic_id"];



		// 		$sql = "SELECT `topic_title`,`topic_type`,`topic_options`
		// 				FROM `mssr_book_topic_log`
		// 				WHERE `topic_id` = '".$val["topic_id"]."'
		// 				";
		// 		$retrun_2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);

		// 		if(count($retrun_2))
		// 		{
		// 			$tmp["quest"] = $retrun_2[0]["topic_title"];
		// 			$tmp["type"] =$retrun_2[0]["topic_type"];
		// 			$tmp["answer"] = unserialize($retrun_2[0]["topic_options"]);

		// 		}
		// 		array_push($data,$tmp);
		// 	}
		// }
		// //====================
		// $array["topic"] = $data;
		// $array["count"] = count($retrun_2);

		// echo json_encode($array,1);


		?>