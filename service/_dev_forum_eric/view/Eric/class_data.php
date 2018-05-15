<?php
    //---------------------------------------------------
        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",4).'config/config.php');

        //外掛頁面檔
        require_once(str_repeat("../",2).'pages/code.php');

        //外掛函式檔
        $funcs=array(
            APP_ROOT.'inc/code',
			APP_ROOT.'lib/php/db/code',
            APP_ROOT.'service/_dev_forum_eric/inc/code'
        );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //連線物件
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);
		
		$school = 'dsg';
		
		//有發文或回文過的班級
		$sql="
			SELECT 
				user.student.class_code  
			FROM mssr_forum.`mssr_forum_article` 
				inner join user.student on 
				mssr_forum.`mssr_forum_article`.`user_id`=user.student.uid 
				inner join user.class on 
				user.class.class_code=user.student.class_code 
				inner join user.semester on 
				user.class.semester_code=user.semester.semester_code 
			where 1=1 
				AND user.semester.school_code = 'ctc'
				AND user.semester.semester_year =2015
				AND user.semester.semester_term =2
				AND user.class.grade in (3,4,5)
				GROUP BY user.class.class_code 

			UNION 

			SELECT 
				user.student.class_code 
			FROM mssr_forum.`mssr_forum_reply` 
				inner join user.student on 
				mssr_forum.`mssr_forum_reply`.`user_id`=user.student.uid 
				inner join user.class on 
				user.class.class_code=user.student.class_code 
				inner join user.semester on 
				user.class.semester_code=user.semester.semester_code 
			where 1=1 
				AND user.semester.school_code = 'ctc'
				AND user.semester.semester_year = 2015
				AND user.semester.semester_term = 2
				AND user.class.grade in (3,4,5)
				GROUP BY user.class.class_code
		";
		$get_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		$class_data = array();
		$k = 0;
		foreach($get_data as $key => $get_data){
			$class_data[$key] = $get_data['class_code'];
			$k++;
		}
		//$output = implode(",",$class_data);
		echo "<pre>";
		print_r($class_data);
		echo "</pre>";

		
		//班級人數
		$class_number = array();
		$class_cno1 = array();
		$class_cno2 = array();
		for($i=0; $i<$k; $i++){
			$w = "'".$class_data[$i]."'";
			$sql="
				SELECT 
					count(user.student.uid) as class_number
				FROM user.student
					inner join user.class on user.class.class_code = user.student.class_code 
					inner join user.semester on user.class.semester_code = user.semester.semester_code 
				where 1=1 
					AND user.class.class_code = {$w}
			";
			$get_data_number = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$class_number[$i] = $get_data_number[0]['class_number'];

			//發文篇數 
			$sql="
				SELECT 
					user.student.class_code,count(mssr_forum.`mssr_forum_article`.article_id) as cno1  
				FROM mssr_forum.`mssr_forum_article` 
					inner join user.student on 
					mssr_forum.`mssr_forum_article`.`user_id`=user.student.uid 
					inner join user.class on 
					user.class.class_code=user.student.class_code 
					inner join user.semester on 
					user.class.semester_code=user.semester.semester_code 
				where 1=1 
					AND user.class.class_code = {$w}                               
					GROUP BY user.class.class_code 
			";
			$get_data_cno1 = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			if(isset($get_data_cno1[0]['cno1'])){
					$class_cno1[$i] = $get_data_cno1[0]['cno1'];
				}else{
					$class_cno1[$i] = 0;
				}
			
			//回文篇數
			$sql="
				SELECT 
					user.student.class_code,count(mssr_forum.`mssr_forum_reply`.reply_id) as cno2  
				FROM mssr_forum.`mssr_forum_reply` 
					inner join user.student on 
					mssr_forum.`mssr_forum_reply`.`user_id`=user.student.uid 
					inner join user.class on 
					user.class.class_code=user.student.class_code 
					inner join user.semester on 
					user.class.semester_code=user.semester.semester_code 
				where 1=1 
					AND user.class.class_code = {$w}                                 
					GROUP BY user.class.class_code 
			";
			$get_data_cno2 = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);					
			if(isset($get_data_cno2[0]['cno2'])){
				$class_cno2[$i] = $get_data_cno2[0]['cno2'];
			}else{
				$class_cno2[$i] = 0;
			}	
						
		}
		echo "<pre>";
		print_r($class_number);
		echo "</pre>";

		
		for($i=0; $i<$k; $i++){
			if((($class_cno1[$i]+$class_cno2[$i])/$class_number[$i])>=1){
				echo "班級：".$class_data[$i]."&nbsp;&nbsp;平均文章篇數(發文+回文)：".($class_cno1[$i]+$class_cno2[$i])/$class_number[$i]."&nbsp;&nbsp;班級人數：".$class_number[$i];
				echo "</br>";
			}
		}





?>