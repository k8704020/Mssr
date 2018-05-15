<?php
//-------------------------------------------------------
//得取每一天的閱讀本數、字數 //不重複
//-------------------------------------------------------



    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION

        //啟用BUFFER
        @ob_start();
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
        $DOCUMENT_ROOT=str_replace(DIRECTORY_SEPARATOR,'/',$_SERVER['DOCUMENT_ROOT']);

        //外掛設定檔
        require_once("{$DOCUMENT_ROOT}/mssr/config/config.php");
		require_once("{$DOCUMENT_ROOT}/mssr/inc/get_book_info/code.php");



         //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);


        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

       if(isset($_REQUEST["funName"])){

	        $funName=$_REQUEST["funName"];

	        echo JSON_encode(@$funName()); //執行並回傳(轉成JSON格式)

	 }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
	//參數處理

       
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------


        //建立連線 user
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
 		$conn_user=conn($db_type='mysql',$arry_conn_user);
	//---------------------------------------------------
    //END
    //---------------------------------------------------


 	//呼叫並執行function




	/*
	一、	函數功能：學生在某個月推薦作品 分數為 4分以上  
	二、	函數名稱：系統名稱_function名稱  ex:ps_abc()
	三、	參數：(學生uid,作品數量)  ex: ps_abc(105,4)
	四、	回傳方式：學生的值
	*///
	function about_elementary_school_recommend(){
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------

		$school_code=@$_REQUEST["school_code"];

        $year=@$_REQUEST["year"];

        $month=@$_REQUEST["month"];

        $Eart_num=@$_REQUEST["Eart_num"]; //指定佳作篇數(與$excellent搭配)

		// $student=0;
 	// 	$school_code='gcp';
 	// 	$year='2017';
 	// 	$month='03';
 		// $Eart_num
 		

		$start=mb_strlen($month, "utf-8");

		if($month<2){
			$next_month="0".$next_month;
		}

		$about_student=array();


		//---------------------------------------------------
		//全域變數宣告
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;

		//------------------------------------
		//學生的uid
		//------------------------------------

		$sql = "SELECT user.`student`.uid, user.`student`.start, user.`student`.end
				FROM  user. `student`
				WHERE user.`student`.end >='{$year}-{$month}-01'
				AND user.`student`.class_code like '{$school_code}%'
				GROUP BY user.`student`.uid
				
				";	


		$result = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);

		if(!empty($result)){


					foreach ($result as $key => $value) {

						$uid[]=$value['uid'];

						
					}

					$student_uid=implode(",",$uid);

					//------------------------------------
					//達到推薦4分的人數
					//-----------------------------------

					$score_sql ="  
										   SELECT count(student_rec.num) as student_rec_total
										   FROM(
											   SELECT count(*) as num
											   FROM  `mssr_rec_book_cno` AS mrbc
											   WHERE mrbc.`user_id`IN ({$student_uid})
											   AND mrbc.keyin_cdate >='{$year}-{$month}-01 00:00:00'
											   AND mrbc.keyin_cdate <='{$year}-{$month}-31 23:59:59'
											   GROUP BY  mrbc.`user_id`
										   )AS student_rec
									
								
					";	



				    $score_result = db_result($conn_type='pdo',$conn_mssr,$score_sql,$arry_limit=array(),$arry_conn_mssr);

					array_push($about_student,$score_result[0]['student_rec_total']);

					//------------------------------------
					//達到推薦4分的人數
					//-----------------------------------

					$score_sql ="  
									SELECT count(*) as student
									FROM (
										   SELECT mrcl.comment_to,count(mrcl.comment_score) as num
										   FROM  `mssr_rec_comment_log` AS mrcl
										   JOIN `mssr_rec_book_cno` AS mrbc ON mrcl.book_sid=mrbc.book_sid and mrcl.`comment_to` = mrbc.user_id
										   WHERE mrcl.`comment_to`IN ({$student_uid})
										   AND mrcl.comment_score >='4'
										   AND mrbc.keyin_cdate >='{$year}-{$month}-01 00:00:00'
										   AND mrbc.keyin_cdate <='{$year}-{$month}-31 23:59:59'
										   GROUP BY  mrcl.`comment_to`
									)AS student_score
									WHERE student_score.num >= '{$Eart_num}'
						
								
					";	



				    $score_result = db_result($conn_type='pdo',$conn_mssr,$score_sql,$arry_limit=array(),$arry_conn_mssr);

					array_push($about_student,$score_result[0]['student']);
					return $about_student;



		}else{

			return  0;
		}




	}



	/*
	一、	函數功能：國中有做推薦人數
	二、	函數名稱：系統名稱_function名稱  ex:ps_abc()
	三、	參數：(學生uid)  ex: ps_abc(105)
	四、	回傳方式：學生的值
	*/
	///
	function about_junior_high_school_recommend()
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------

		$school_code=@$_REQUEST["school_code"];

		$semester=@$_REQUEST["semester"]; //學年


 		// $school_code='glj';
 		// $semester='2016';
 		$year=$semester+1;
 		// $student=0;
 		
		//---------------------------------------------------
		//全域變數宣告
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		//-------------------------------
		//國中學生uid
		//-------------------------------

		$sql = "
				SELECT user.`student`.uid
				FROM  user. `student`
				WHERE  user.`student`.class_code like '{$school_code}_{$semester}%'
				GROUP BY user.`student`.uid
				
		";

		// echo $sql;


		$result =db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);


		if(!empty($result)){

					foreach ($result as $key => $value) {

						$uid[]=$value['uid'];
						
					}

					$student_uid=implode(",",$uid);




					//-----------------------------------
					//在學年度有做推薦的人數(不含評星)
					//-----------------------------------

					$recomment_sql ="  

										   SELECT count(*) as student
										   FROM (
											   SELECT mrbc.user_id,mrbc.rec_stat_cno,mrbc.rec_draw_cno,mrbc.rec_text_cno,mrbc.rec_record_cno
											   FROM  mssr.`mssr_rec_book_cno` as mrbc 
											   JOIN  user.student as s ON s.uid=mrbc.user_id

											   WHERE mrbc.`user_id`IN ({$student_uid})
											   AND mrbc.keyin_cdate >='{$semester}-08-01 00:00:00' 
											   AND mrbc.keyin_cdate <=s.end
											   AND( mrbc.rec_draw_cno >='1'
											   		OR mrbc.rec_text_cno >='1'
											   		OR mrbc.rec_record_cno >='1'
											   	   )			
											   GROUP BY mrbc.user_id
										   ) AS student_recommend

								
					";	



				    $recomment_result = db_result($conn_type='pdo',$conn_mssr,$recomment_sql,$arry_limit=array(),$arry_conn_mssr);

				    

				    return  $recomment_result[0]['student'];





		}else{

			return  0;

		}

		

	}



?>