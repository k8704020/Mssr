<?php
//-------------------------------------------------------
//mssr_forum
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------
        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",2).'config/config.php');
		require_once(str_repeat("../",0)."inc/search_book_ch_no_online/code.php");
		

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
            //$conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

			//-----------------------------------------------
			//上傳資料庫
	        //-----------------------------------------------

				//-----------------------------------------------
				//檢核
	        	//-----------------------------------------------
					$sess_uid 				=(int)$_SESSION["uid"];
					$forum_id				=(int)$_POST["forum_id"];
					//$book_isbn				=trim($_POST["mssr_group_add_book_name_title"]);
					$book_sid				=trim($_POST["mssr_group_add_book_name_title"]);
					
				
				
					
					
					
					
			
				//-----------------------------------------------
				//錯誤處理
	        	//-----------------------------------------------
					//book_isbn處理
					if($book_sid===""){
						$msg='
							<script>
								alert("請選擇書籍");
								history.back(-1);
							</script>
						';
						die($msg);
					}
					
				//-----------------------------------------------
	        	//SQL-用ISBN找書籍
	        	//-----------------------------------------------
					// $sql="
						// SELECT `book_sid`
						// FROM(
								// SELECT
									// `book_isbn_13`, `book_sid`, `keyin_cdate`, `book_isbn_10`
								// FROM
									// `mssr_book_library`
							// UNION ALL
								// SELECT
									// `book_isbn_13`, `book_sid`, `keyin_cdate`, `book_isbn_10`
								// FROM
									// `mssr_book_global`
							// UNION ALL
								// SELECT
									// `book_isbn_13`, `book_sid`, `keyin_cdate`, `book_isbn_10`
								// FROM
									// `mssr_book_class`
							// UNION ALL
								// SELECT
									// `book_isbn_13`, `book_sid`, `keyin_cdate`, `book_isbn_10`
								// FROM
									// `mssr_book_unverified`
						// ) V1
						// WHERE
							// `book_isbn_13` = '$book_isbn'
							// OR `book_isbn_10` = '$book_isbn'
						// ORDER BY
							// `keyin_cdate` DESC
					// ";
					// $arrys_book_isbn=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);


					//$numrow_which_group=count($arrys_book_isbn);
					
					
					
					//找不到書籍處裡 除錯
					/* if(empty($arrys_book_isbn)){
						$msg='
							<script>
								alert("書籍不存在，請重新輸入!");
								history.back(-1);
							</script>
						';
						die($msg);
					}else{
						$book_sid				=$arrys_book_isbn[0]['book_sid'];
					} */
					
				//-----------------------------------------------
				//檢查聊書小組內是已有此本書
	        	//-----------------------------------------------
					$sql="
						SELECT
							`book_sid`
						FROM
							`mssr_forum_booklist`
						WHERE 1=1
							AND `forum_id` 	= $forum_id	
							AND	`book_state` = 1
							AND `book_sid`  = '$book_sid';
					";
					$booklist_has_book_check=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					
							
					
					if(!empty($booklist_has_book_check)){
						$msg='
							<script>
								alert("興趣書單目前已經有這本書！");
								history.back(-1);
							</script>
						';
						die($msg);
					}
					

				
				//-----------------------------------------------
				//檢核上傳內容
	        	//-----------------------------------------------
					$create_by				=$sess_uid;
					$edit_by				=$sess_uid;	
					$forum_id				=$forum_id;
					$keyin_cdate			="NOW()";
					$keyin_mdate			="NOW()";
					

				//-----------------------------------------------
				//SQL-寫入for mssr_forum
	        	//-----------------------------------------------
					$sql="
						# for mssr_forum
						INSERT INTO `mssr_forum_booklist` SET
							`create_by`				= {$create_by}			     ,
							`edit_by`				= {$edit_by}		 	 	 ,
							`forum_id`				= {$forum_id}		 	 	 ,
							`book_sid`				='{$book_sid}',
							`keyin_cdate`			= {$keyin_cdate}			 ,
							`keyin_mdate`			= {$keyin_mdate}    	 ;
					";
					//送出
					$err ='DB QUERY FAIL1';
					$sth=$conn_mssr->prepare($sql);
					$sth->execute()or die($err);
					
					//-----------------------------------------------
					//SQL-中國圖書分類號
					//-----------------------------------------------
					 
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
						
						
						
						
						
					
					
				

	//---------------------------------------------------
    //檢驗
    //---------------------------------------------------
		//echo "<pre>";
//		print_r($book_sid);
//		echo "</pre>";
//		die();

	//---------------------------------------------------
    //重導頁面
    //---------------------------------------------------
        $url ="mssr_forum_group_shelf.php?forum_id=";
		$url =$url.$forum_id;
			//echo "<pre>";
//		print_r($url);
//		echo "</pre>";

        header("Location: {$url}");
?>