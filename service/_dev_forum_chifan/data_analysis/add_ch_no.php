<?php 
//-------------------------------------------------------
//mssr_forum
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------
        ///SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",3).'config/config.php');
		require_once(str_repeat("../",0)."search_book_ch_no_online/code.php");
		

        //外掛函式檔
        $funcs=array(
			APP_ROOT.'inc/code',
			APP_ROOT.'lib/php/db/code',
			APP_ROOT.'lib/php/net/code',
			APP_ROOT.'lib/php/array/code'
       	);
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();
	//---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------
            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

			//-----------------------------------------------
	        //下載資料庫
	        //-----------------------------------------------

				//-----------------------------------------------
            	//檢核
            	//-----------------------------------------------
					$sql="
						 SELECT
							`mssr`.`mssr_book_borrow_log`.`book_sid`
							
						FROM `mssr`.`mssr_book_borrow_log`
						
							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_book_borrow_log`.`user_id`=`user`.`member`.`uid`

							INNER JOIN `user`.`student` ON
							`user`.`member`.`uid`=`user`.`student`.`uid`

							INNER JOIN `user`.`class` ON
							`user`.`student`.`class_code`=`user`.`class`.`class_code`
						WHERE 1=1
							AND `user`.`student`.`class_code`IN ('gcp_2014_1_5_1','gcp_2014_1_5_2','gcp_2014_1_5_5')

							AND `mssr`.`mssr_book_borrow_log`.`borrow_sdate` BETWEEN
							'2015-04-01 00:00:00' AND '2015-06-1 00:00:00'

							
					";
					$arrys_result_borrow=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

					//-----------------------------------------------
					//SQL-中國圖書分類號
					//-----------------------------------------------
					 foreach($arrys_result_borrow as $arrys_result_borrow_v){
						$book_sid = $arrys_result_borrow_v['book_sid'];
						/* echo $book_sid;
						die(); */
						
						$sql="
							SELECT
								`book_ch_no`
							FROM
								`mssr_forum_book_ch_no_rev`
							WHERE
								`book_sid` = '{$book_sid}'		;
						";
						
						
						$arrys_result_ch_no=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
						$book_ch_no	=0;
						
						if(empty($arrys_result_ch_no)){
							require_once(str_repeat("../",0)."search_book_ch_no_online/code.php");
							$arrys_book_info	= get_book_info($conn_mssr,$book_sid,$array_filter=array(),$arry_conn_mssr);
							
							$book_isbn_13	= mysql_prep(trim($arrys_book_info[0]['book_isbn_13']));
							$book_isbn_10	= mysql_prep(trim($arrys_book_info[0]['book_isbn_10']));
						
							
							// 線上抓取分類號資料(以$book_isbn_13為優先)
							if($book_isbn_13!=""){
								$book_isbn 	= $book_isbn_13;
							}else if($book_isbn_10!=""){
								$book_isbn	= $book_isbn_10;
							}else{
								$book_isbn	= "";
								
							}

							
							// 如果有有搜尋到分類號，有則寫入資料，沒有則寫入"000"
							if((empty($arrys_result_ch_no)) && ($book_isbn != "")){

									$search_book_ch_no_online = search_book_ch_no_online($book_isbn);
									$book_ch_no				  = mysql_prep((int)$search_book_ch_no_online['book_ch_no'][0]);
									
									
									if($book_ch_no==""){
								
										$sql="
											# for mssr_forum_book_ch_no_rev
											INSERT INTO `mssr_forum_book_ch_no_rev` SET
												`book_sid`				= '{$book_sid}'		,
												`book_ch_no`			= 0			 		;	 			
										";
										// 送出
										$err ='DB QUERY FAIL1';
										$sth=$conn_mssr->prepare($sql);
										$sth->execute()or die($err);
										
									}else{
									
										$sql="
											# for mssr_forum_book_ch_no_rev
											INSERT INTO `mssr_forum_book_ch_no_rev` SET
												`book_sid`				= '{$book_sid}'		,
												`book_ch_no`			= {$book_ch_no}		 		;	 			
										";
										// 送出
										$err ='DB QUERY FAIL2';
										$sth=$conn_mssr->prepare($sql);
										$sth->execute()or die($err);
									
									}
							}
							
							
							
						
						}
				}
						
						
						
					
				
?>





 

 
 

  
  
  

