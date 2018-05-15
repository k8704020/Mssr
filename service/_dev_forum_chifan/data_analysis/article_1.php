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
        require_once(str_repeat("../",3).'config/config.php');

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
					

				//-----------------------------------------------
				//SQL-一班發表文章數量
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`user_id`,
							`mssr`.`mssr_forum_article`.`article_title`
						
						FROM `mssr`.`mssr_forum_article`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

						WHERE 1=1

						AND `mssr`.`mssr_forum_article`.`user_id` IN (
							SELECT `user`.`student`.`uid`
							FROM `user`.`student`
							WHERE 1=1
								AND `user`.`student`.`class_code`='gcp_2014_2_5_1'
							
						)
					";						
						
						
				
					$article_class_1		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$artilce_class_1_con 	=count($article_class_1);
					
				//-----------------------------------------------
				//SQL-二班發表文章數量
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`user_id`,
							`mssr`.`mssr_forum_article`.`article_title`
						
						FROM `mssr`.`mssr_forum_article`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

						WHERE 1=1

						AND `mssr`.`mssr_forum_article`.`user_id` IN (
							SELECT `user`.`student`.`uid`
							FROM `user`.`student`
							WHERE 1=1
								AND `user`.`student`.`class_code`='gcp_2014_2_5_2'
							
						)
					";						
						
				
					$article_class_2		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$artilce_class_2_con 	=count($article_class_2);
					
				//-----------------------------------------------
				//SQL-五班發表文章數量
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`user_id`,
							`mssr`.`mssr_forum_article`.`article_title`
						
						FROM `mssr`.`mssr_forum_article`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

						WHERE 1=1

						AND `mssr`.`mssr_forum_article`.`user_id` IN (
							SELECT `user`.`student`.`uid`
							FROM `user`.`student`
							WHERE 1=1
								AND `user`.`student`.`class_code`='gcp_2014_2_5_5'
							
						)
					";						
						
				
					$article_class_5		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$artilce_class_5_con 	=count($article_class_5);
					
				//-----------------------------------------------
				//SQL-一班發表文章數量(書籍)
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`user_id`,
							`mssr`.`mssr_forum_article`.`article_title`
							
						FROM `mssr`.`mssr_forum_article`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
							
							 INNER JOIN `mssr`.`mssr_article_book_rev` ON
							`mssr`.`mssr_forum_article`.`article_id`=`mssr`.`mssr_article_book_rev`.`article_id`

						WHERE 1=1

							AND `mssr`.`mssr_forum_article`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`='gcp_2014_2_5_1'
								
							)

					";						
						
				
					$article_book_class_1		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$article_book_class_1_con 	=count($article_book_class_1);
					
					
					
				//-----------------------------------------------
				//SQL-二班發表文章數量(書籍)
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`user_id`,
							`mssr`.`mssr_forum_article`.`article_title`
							
						FROM `mssr`.`mssr_forum_article`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
							
							 INNER JOIN `mssr`.`mssr_article_book_rev` ON
							`mssr`.`mssr_forum_article`.`article_id`=`mssr`.`mssr_article_book_rev`.`article_id`

						WHERE 1=1

							AND `mssr`.`mssr_forum_article`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`='gcp_2014_2_5_2'
								
							)

					";						
						
				
					$article_book_class_2		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$article_book_class_2_con 	=count($article_book_class_2);
					
				//-----------------------------------------------
				//SQL-五班發表文章數量(書籍)
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`user_id`,
							`mssr`.`mssr_forum_article`.`article_title`
							
						FROM `mssr`.`mssr_forum_article`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
							
							 INNER JOIN `mssr`.`mssr_article_book_rev` ON
							`mssr`.`mssr_forum_article`.`article_id`=`mssr`.`mssr_article_book_rev`.`article_id`

						WHERE 1=1

							AND `mssr`.`mssr_forum_article`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`='gcp_2014_2_5_2'
								
							)

					";						
						
				
					$article_book_class_5		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$article_book_class_5_con 	=count($article_book_class_5);
					
				
				//-----------------------------------------------
				//SQL-一班發表文章數量(群)
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`user_id`,
							`mssr`.`mssr_forum_article`.`article_title`
							
						FROM `mssr`.`mssr_forum_article`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
							
							 INNER JOIN `mssr`.`mssr_article_forum_rev` ON
							`mssr`.`mssr_forum_article`.`article_id`=`mssr`.`mssr_article_forum_rev`.`article_id`

						WHERE 1=1

							AND `mssr`.`mssr_forum_article`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`='gcp_2014_2_5_1'
								
							)


					";						
						
				
					$article_group_class_1		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$article_group_class_1_con 	=count($article_group_class_1);
					
				//-----------------------------------------------
				//SQL-二班發表文章數量(群)
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`user_id`,
							`mssr`.`mssr_forum_article`.`article_title`
							
						FROM `mssr`.`mssr_forum_article`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
							
							 INNER JOIN `mssr`.`mssr_article_forum_rev` ON
							`mssr`.`mssr_forum_article`.`article_id`=`mssr`.`mssr_article_forum_rev`.`article_id`

						WHERE 1=1

							AND `mssr`.`mssr_forum_article`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`='gcp_2014_2_5_2'
								
							)


					";						
						
				
					$article_group_class_2		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$article_group_class_2_con 	=count($article_group_class_2);
					
					
				//-----------------------------------------------
				//SQL-五班發表文章數量(群)
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`user_id`,
							`mssr`.`mssr_forum_article`.`article_title`
							
						FROM `mssr`.`mssr_forum_article`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
							
							 INNER JOIN `mssr`.`mssr_article_forum_rev` ON
							`mssr`.`mssr_forum_article`.`article_id`=`mssr`.`mssr_article_forum_rev`.`article_id`

						WHERE 1=1

							AND `mssr`.`mssr_forum_article`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`='gcp_2014_2_5_5'
								
							)


					";						
						
				
					$article_group_class_5		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$article_group_class_5_con 	=count($article_group_class_5);
					
					
					
				//-----------------------------------------------
				//SQL-一班回覆總數()
				//-----------------------------------------------
					$sql="
						SELECT
						 
							`mssr`.`mssr_forum_article_reply`.`user_id`
							
						FROM `mssr`.`mssr_forum_article_reply`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article_reply`.`user_id`=`user`.`member`.`uid`
							

						WHERE 1=1

							AND `mssr`.`mssr_forum_article_reply`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`='gcp_2014_2_5_1'
								
							)


					";						
						
				
					$reply_class_1		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$reply_class_1_con 	=count($reply_class_1);
					
					
				//-----------------------------------------------
				//SQL-二班回覆總數()
				//-----------------------------------------------
					$sql="
						SELECT
						 
							`mssr`.`mssr_forum_article_reply`.`user_id`
							
						FROM `mssr`.`mssr_forum_article_reply`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article_reply`.`user_id`=`user`.`member`.`uid`
							
							

						WHERE 1=1

							AND `mssr`.`mssr_forum_article_reply`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`='gcp_2014_2_5_2'
								
							)


					";						
						
				
					$reply_class_2		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$reply_class_2_con 	=count($reply_class_2);
					
				//-----------------------------------------------
				//SQL-五班回覆總數()
				//-----------------------------------------------
					$sql="
						SELECT
						 
							`mssr`.`mssr_forum_article_reply`.`user_id`
							
						FROM `mssr`.`mssr_forum_article_reply`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article_reply`.`user_id`=`user`.`member`.`uid`
							
							
						WHERE 1=1

							AND `mssr`.`mssr_forum_article_reply`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`='gcp_2014_2_5_5'
								
							)


					";						
						
				
					$reply_class_5		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$reply_class_5_con 	=count($reply_class_5);
					
					
					
					
				//-----------------------------------------------
				//SQL-五班回覆總數()
				//-----------------------------------------------
					$sql="
						SELECT
						 
							`mssr`.`mssr_forum_article_reply`.`user_id`
							
						FROM `mssr`.`mssr_forum_article_reply`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article_reply`.`user_id`=`user`.`member`.`uid`
							
							
						WHERE 1=1

							AND `mssr`.`mssr_forum_article_reply`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`='gcp_2014_2_5_5'
								
							)

					";						
						
				
					$reply_class_5		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$reply_class_5_con 	=count($reply_class_5);
					
							
				//-----------------------------------------------
				//SQL-一班回覆數(group)
				//-----------------------------------------------
					$sql="
						SELECT
						 
							`mssr`.`mssr_forum_article_reply`.`user_id`
							
						FROM `mssr`.`mssr_forum_article_reply`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article_reply`.`user_id`=`user`.`member`.`uid`
							
							 INNER JOIN `mssr_article_reply_forum_rev` ON
							`mssr`.`mssr_forum_article_reply`.`reply_id`=`mssr`.`mssr_article_reply_forum_rev`.`reply_id`

						WHERE 1=1

							AND `mssr`.`mssr_forum_article_reply`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`='gcp_2014_2_5_1'
								
							)
					";						
						
				
					$reply_group_class_1		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$reply_group_class_1_con 	=count($reply_group_class_1);
					
					
					
				//-----------------------------------------------
				//SQL-二班回覆數(group)
				//-----------------------------------------------
					$sql="
						SELECT
						 
							`mssr`.`mssr_forum_article_reply`.`user_id`
							
						FROM `mssr`.`mssr_forum_article_reply`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article_reply`.`user_id`=`user`.`member`.`uid`
							
							 INNER JOIN `mssr_article_reply_forum_rev` ON
							`mssr`.`mssr_forum_article_reply`.`reply_id`=`mssr`.`mssr_article_reply_forum_rev`.`reply_id`

						WHERE 1=1

							AND `mssr`.`mssr_forum_article_reply`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`='gcp_2014_2_5_2'
								
							)
					";						
						
				
					$reply_group_class_2		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$reply_group_class_2_con 	=count($reply_group_class_2);
					
					
				//-----------------------------------------------
				//SQL-五班回覆數(group)
				//-----------------------------------------------
					$sql="
						SELECT
						 
							`mssr`.`mssr_forum_article_reply`.`user_id`
							
						FROM `mssr`.`mssr_forum_article_reply`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article_reply`.`user_id`=`user`.`member`.`uid`
							
							 INNER JOIN `mssr_article_reply_forum_rev` ON
							`mssr`.`mssr_forum_article_reply`.`reply_id`=`mssr`.`mssr_article_reply_forum_rev`.`reply_id`

						WHERE 1=1

							AND `mssr`.`mssr_forum_article_reply`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`='gcp_2014_2_5_5'
								
							)
					";						
						
				
					$reply_group_class_5		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$reply_group_class_5_con 	=count($reply_group_class_5);
					
					
					////////////////////////////////////////////////////////////////////////////////////////////////////
				//-----------------------------------------------
				//SQL-一班回覆數(book)
				//-----------------------------------------------
					$sql="
						SELECT
						 
							`mssr`.`mssr_forum_article_reply`.`user_id`
							
						FROM `mssr`.`mssr_forum_article_reply`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article_reply`.`user_id`=`user`.`member`.`uid`
							
							 INNER JOIN `mssr_article_reply_book_rev` ON
							`mssr`.`mssr_forum_article_reply`.`reply_id`=`mssr`.`mssr_article_reply_book_rev`.`reply_id`

						WHERE 1=1

							AND `mssr`.`mssr_forum_article_reply`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`='gcp_2014_2_5_1'
								
							)
					";						
						
				
					$reply_book_class_1		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$reply_book_class_1_con 	=count($reply_book_class_1);
					
					
					
				//-----------------------------------------------
				//SQL-二班回覆數(book)
				//-----------------------------------------------
					$sql="
						SELECT
						 
							`mssr`.`mssr_forum_article_reply`.`user_id`
							
						FROM `mssr`.`mssr_forum_article_reply`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article_reply`.`user_id`=`user`.`member`.`uid`
							
							 INNER JOIN `mssr_article_reply_book_rev` ON
							`mssr`.`mssr_forum_article_reply`.`reply_id`=`mssr`.`mssr_article_reply_book_rev`.`reply_id`

						WHERE 1=1

							AND `mssr`.`mssr_forum_article_reply`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`='gcp_2014_2_5_2'
								
							)
					";						
						
				
					$reply_book_class_2			=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$reply_book_class_2_con 	=count($reply_book_class_2);
					
					
				//-----------------------------------------------
				//SQL-五班回覆數(book)
				//-----------------------------------------------
					$sql="
						SELECT
						 
							`mssr`.`mssr_forum_article_reply`.`user_id`
							
						FROM `mssr`.`mssr_forum_article_reply`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article_reply`.`user_id`=`user`.`member`.`uid`
							
							 INNER JOIN `mssr_article_reply_book_rev` ON
							`mssr`.`mssr_forum_article_reply`.`reply_id`=`mssr`.`mssr_article_reply_book_rev`.`reply_id`

						WHERE 1=1

							AND `mssr`.`mssr_forum_article_reply`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`='gcp_2014_2_5_5'
								
							)
					";						
						
				
					$reply_book_class_5		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$reply_book_class_5_con 	=count($reply_book_class_5);
					
					
				//-----------------------------------------------
				//SQL-發文字數
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`article_content`,
							`mssr`.`mssr_forum_article`.`article_id`
							
						FROM `mssr`.`mssr_forum_article`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

						WHERE 1=1
								
								AND `mssr`.`mssr_forum_article`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 
								
									`user`.`student`.`class_code` IN ('gcp_2014_2_5_1', 'gcp_2014_2_5_2', 'gcp_2014_2_5_5')
						)
						
						

					";		
				
					$article_word_count			=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);					 $word_i=0;
					$word=0;
					foreach($article_word_count as $v){
						
						if(trim($v['article_content'])!=""){
							$word_i ++;
							$word = $word + mb_strlen($v['article_content']);	
						}
					}
					$avg_word = $word / $word_i;
					
					
				//-----------------------------------------------
				//SQL-回文字數
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article_reply`.`reply_content`,
							`mssr`.`mssr_forum_article_reply`.`reply_id`
							
						FROM `mssr`.`mssr_forum_article_reply`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article_reply`.`user_id`=`user`.`member`.`uid`

						WHERE 1=1
								
								AND `mssr`.`mssr_forum_article_reply`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 
								
									`user`.`student`.`class_code` IN ('gcp_2014_2_5_1', 'gcp_2014_2_5_2', 'gcp_2014_2_5_5')
						)
						
						

					";		
				
					$reply_word_count			=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);				   $reply_word_i=0;
					$reply_word=0;
					foreach($reply_word_count as $v){
						
						if(trim($v['reply_content'])!=""){
							$reply_word_i ++;
							$reply_word = $reply_word + mb_strlen($v['reply_content']);	
						}
					}
					$avg_reply_word = $reply_word / $reply_word_i;
					//echo $avg_reply_word;
				
			
					
				
				//-----------------------------------------------
                //SQL-發表文章數量(群)
                //-----------------------------------------------

                    $sql="
                        SELECT
 
                            `mssr`.`mssr_forum_article`.`user_id`,
                            `mssr`.`mssr_forum_article`.`article_title`,
                            `mssr`.`mssr_forum_article`.article_content
                            
                        FROM `mssr`.`mssr_forum_article`

                            INNER JOIN `user`.`member` ON
                            `mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                            
                             INNER JOIN `mssr`.`mssr_article_forum_rev` ON
                            `mssr`.`mssr_forum_article`.`article_id`=`mssr`.`mssr_article_forum_rev`.`article_id`

                        WHERE 1=1

                            AND `mssr`.`mssr_forum_article`.`user_id` IN (
                                SELECT `user`.`student`.`uid`
                                FROM `user`.`student`
                                WHERE 1=1
                                    AND `user`.`student`.`class_code` in('gcp_2014_2_5_5','gcp_2014_2_5_1','gcp_2014_2_5_2')                
                            )
                    ";                      
                        
                
                    $article_group_class_all      =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    $article_group_class_all_con  =count($article_group_class_all);

// echo '<pre>';
// print_r($article_group_class_all);
// echo '</pre>';
// echo '<pre>';
// print_r($article_group_class_all_con);
// echo '</pre>';
                    $sum = 0; 
                    $i=0;
                    foreach($article_group_class_all as $k=>$v){
                        if(trim($v['article_content']!="")){
                            $sum += mb_strlen($v['article_content'],"utf-8");
                            $i++;
                         }
                        
                    }
                    $avg1 = $sum/$i;

	                //-----------------------------------------------
	                //SQL-發表文章數量(書)
	                //-----------------------------------------------
                    $sql="
                        SELECT
 
                            `mssr`.`mssr_forum_article`.`user_id`,
                            `mssr`.`mssr_forum_article`.`article_title`,
                            `mssr`.`mssr_forum_article`.article_content
                            
                        FROM `mssr`.`mssr_forum_article`

                            INNER JOIN `user`.`member` ON
                            `mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                            
                             INNER JOIN `mssr`.`mssr_article_book_rev` ON
                            `mssr`.`mssr_forum_article`.`article_id`=`mssr`.`mssr_article_book_rev`.`article_id`

                        WHERE 1=1

                            AND `mssr`.`mssr_forum_article`.`user_id` IN (
                                SELECT `user`.`student`.`uid`
                                FROM `user`.`student`
                                WHERE 1=1
                                    AND `user`.`student`.`class_code` in('gcp_2014_2_5_5','gcp_2014_2_5_1','gcp_2014_2_5_2') 
                                
                            )

                    ";                      
                        
                
                    $article_book_class_all       =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    $article_book_class_all_con   =count($article_book_class_1);
                
                    $sum = 0; 
                    $i=0;
                    foreach($article_book_class_all as $k=>$v){
                        if(trim($v['article_content']!="")){
                            $sum += mb_strlen($v['article_content'],"utf-8");
                            $i++;
                         }
                        
                    }
                    $avg2 = $sum/$i; 


				//-----------------------------------------------
				//SQL-回文字數(群)
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article_reply`.`reply_content`,
							`mssr`.`mssr_forum_article_reply`.`reply_id`
							
						FROM `mssr`.`mssr_forum_article_reply`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article_reply`.`user_id`=`user`.`member`.`uid`
						INNER JOIN `mssr`.`mssr_article_reply_forum_rev` ON
							`mssr`.`mssr_article_reply_forum_rev`.`reply_id`=`mssr`.`mssr_forum_article_reply`.`reply_id`

						WHERE 1=1
								
								AND `mssr`.`mssr_forum_article_reply`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 
								
									`user`.`student`.`class_code` IN ('gcp_2014_2_5_1', 'gcp_2014_2_5_2', 'gcp_2014_2_5_5')
						)
						
						

					";		
				
					$reply_word_count			=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);				   $reply_word_i=0;
					$reply_word=0;
					foreach($reply_word_count as $v){
						
						if(trim($v['reply_content'])!=""){
							$reply_word_i ++;
							$reply_word = $reply_word + mb_strlen($v['reply_content']);	
						}
					}
					$avg_reply_word_forum = $reply_word / $reply_word_i;
					//echo $avg_reply_word;	
					echo $reply_word_i;
				//-----------------------------------------------
				//SQL-回文字數(書)
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article_reply`.`reply_content`,
							`mssr`.`mssr_forum_article_reply`.`reply_id`
							
						FROM `mssr`.`mssr_forum_article_reply`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article_reply`.`user_id`=`user`.`member`.`uid`
						INNER JOIN `mssr`.`mssr_article_reply_book_rev` ON
							`mssr`.`mssr_article_reply_book_rev`.`reply_id`=`mssr`.`mssr_forum_article_reply`.`reply_id`

						WHERE 1=1
								
								AND `mssr`.`mssr_forum_article_reply`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 
								
									`user`.`student`.`class_code` IN ('gcp_2014_2_5_1', 'gcp_2014_2_5_2', 'gcp_2014_2_5_5')
						)
						
						

					";		
				
					$reply_word_count			=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);				   $reply_word_i=0;
					$reply_word=0;
					foreach($reply_word_count as $v){
						
						if(trim($v['reply_content'])!=""){
							$reply_word_i ++;
							$reply_word = $reply_word + mb_strlen($v['reply_content']);	
						}
					}
					$avg_reply_word_book = $reply_word / $reply_word_i;
					echo "<BR>".$reply_word_i;
					//echo $avg_reply_word;					
					
										
					
					
					
					
					
										
				

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>發文、回文資料分析</title>
</head>

<body>


<I><B><h2 style="color:blue">發文、回文資料分析</h2></B></I>
<HR>
<h3>各班發文、回文數</h3>
<table width="1000" border="1" cellpadding="5" cellspacing="2">
	<thead>
        <tr color=blue>
        	<th scope="col">班級</th>
        	<th scope="col">總發文</th>
            <th scope="col">書籍發文數</th>
            <th scope="col">聊書小組發文數</th>
            <th scope="col">總回文</th>
            <th scope="col">書籍回覆</th>
            <th scope="col">聊書小組回覆數</th>
        </tr>
   	</thead>
   	<tbody>  
	
	
		<tr >
			<td class="">一班</td>
			<td class=""><?php echo $artilce_class_1_con;?></td>
            <td class=""><?php echo $article_book_class_1_con;?></td>
			<td class=""><?php echo $article_group_class_1_con;?></td>
			<td class=""><?php echo $reply_class_1_con;?></td>
			<td class=""><?php echo $reply_book_class_1_con;?></td>
			<td class=""><?php echo $reply_group_class_1_con;?></td>
			
        </tr>
		
		<tr >
			<td class="">二班</td>
			<td class=""><?php echo $artilce_class_2_con;?></td>
            <td class=""><?php echo $article_book_class_2_con;?></td>
			<td class=""><?php echo $article_group_class_2_con;?></td>
			<td class=""><?php echo $reply_class_2_con;?></td>
			<td class=""><?php echo $reply_book_class_2_con;?></td>
			<td class=""><?php echo $reply_group_class_2_con;?></td>
        </tr>
		
		<tr >
			<td class="">五班</td>
			<td class=""><?php echo $artilce_class_5_con;?></td>
            <td class=""><?php echo $article_book_class_5_con;?></td>
			<td class=""><?php echo $article_group_class_5_con;?></td>
			<td class=""><?php echo $reply_class_5_con;?></td>
			<td class=""><?php echo $reply_book_class_5_con;?></td>
			<td class=""><?php echo $reply_group_class_5_con;?></td>
        </tr>
		
	</tbody>
</table>



<h3>發文、回文平均字數</h3>
<table width="1000" border="1" cellpadding="5" cellspacing="2">
	<thead>
        <tr color=blue>
        	
        	<th scope="col">發文平均字數</th>
            <th scope="col">回文平均字數</th>
        </tr>
   	</thead>
   	<tbody>  
	
		<tr >
			<td class=""><?php echo (int)$avg_word;?></td>
			<td class=""><?php echo (int)$avg_reply_word;?></td>

        </tr>

	</tbody>
</table>

<h3>聊書小組發文、書頁發文各平均字數</h3>
<table width="1000" border="1" cellpadding="5" cellspacing="2">
	<thead>
        <tr color=blue>
        	
        	<th scope="col">群組發文平均字數</th>
            <th scope="col">書籍發文平均字數</th>
        </tr>
   	</thead>
   	<tbody>  
	
		<tr >
			<td class=""><?php echo (int)$avg1 ?></td>
			<td class=""><?php echo (int)$avg2 ?></td>

        </tr>

	</tbody>
</table>

<h3>聊書小組發文、書頁回文各平均字數</h3>
<table width="1000" border="1" cellpadding="5" cellspacing="2">
	<thead>
        <tr color=blue>
        	
        	<th scope="col">群組回文平均字數</th>
            <th scope="col">書籍回文平均字數</th>
        </tr>
   	</thead>
   	<tbody>  
	
		<tr >
			<td class=""><?php echo (int)$avg_reply_word_forum ?></td>
			<td class=""><?php echo (int)$avg_reply_word_book ?></td>

        </tr>

	</tbody>
</table>

</body>
</html>



 

 
 

  
  
  

