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
	一、	函數功能：實驗班學生每學期看書狀況(將學生的書籍用5個等級做區分) 
	二、	函數名稱：系統名稱_function名稱  ex:ps_abc()
	三、	參數：(學生uid,作品數量)  ex: ps_abc(105,4)
	四、	回傳方式：陣列(5個等級)
	*///
	function idc_book_level($user_id,$semester_year,$semester_term){


				
                $school_code='idc';

				//---------------------------------------------------
				//全域變數宣告
				//---------------------------------------------------
				global $conn_mssr;
				global $conn_user;
				global $arry_conn_mssr;
				global $arry_conn_user;

				$student_book=array();

				//------------------------------------
				//先撈出學生在idc的這學期時間區間
				//-----------------------------------

                $sql = "
                    SELECT user.`student`.start,user.`student`.end
                    FROM   user.`student`
                    WHERE  user.`student`.class_code like '{$school_code}_{$semester_year}_{$semester_term}%'
                    AND    user.`student`.uid ='{$user_id}'
                  
                    
               ";

                $result =db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);


              if(!empty($result)){


                //------------------------------------
                //撈出學生在這時間區間借的書的等級
                //-----------------------------------


                            $start=$result[0]['start'];
                            $end=$result[0]['end'];

            				$score_sql ="  

            								
            								SELECT mbbl.user_id,mibsli.administrator_level 
            								FROM  `mssr_book_borrow_log` AS mbbl
            								JOIN `mssr_idc_book_sticker_level_info` AS mibsli ON mbbl.book_sid=mibsli.book_sid 
            								WHERE mbbl.`user_id`='{$user_id}'
                                            AND borrow_sdate>='{$start} 00:00:00'
                                            AND borrow_edate<='{$end} 23:59:59'
            								GROUP BY `mbbl`.book_sid
            						
            					
            							
            				";	


            			    $score_result = db_result($conn_type='pdo',$conn_mssr,$score_sql,$arry_limit=array(),$arry_conn_mssr);

            			        $level1=0;
                                $level2=0;
                                $level3=0;
                                $level4=0;
                                $level5=0;


            				foreach ($score_result as $key => $val) {

            						$level=$val['administrator_level'];

                                	if($level=='1'){
                                		$level1 +=1;
                                	}else if($level=='2'){
                                		$level2 +=1;
                                	}else if($level=='3'){
                                		$level3 +=1;

                                	}else if($level=='4'){
                                		$level4 +=1;

                                	}else{

                                		$level5 +=1;

                                	}

            				}

              			array_push($student_book,$level1, $level2, $level3, $level4, $level5);

              			return $student_book;
            }


	}
	// }

// print_r($student_book);

// $aaa=idc_book_level(214206,2017,1);
// print_r($aaa);


?>