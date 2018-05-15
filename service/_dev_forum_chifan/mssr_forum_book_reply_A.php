<?php
//-------------------------------------------------------
//mssr_fourm
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
		
        //外掛頁面檔
        require_once(str_repeat("../",0).'inc/require_page/code.php');

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
				//檢核上傳內容
	        	//-----------------------------------------------
					$cat_id					=(int)$_POST["cat_id"];//預設值
					$reply_refer_code		=trim($_POST["article_refer_code"]);
					$user_id				=(int)$_SESSION["uid"];//預設值
					$article_id				=(int)$_POST["article_id"];
					
					$reply_content_array	 = array();
					$reply_content_array 	=$_POST["mssr_comment_input_name_content"];
					$reply_type				='一般';
					$reply_state			='正常';
					$reply_like_cno			=(int)0;//預設值
					$keyin_cdate			="NOW()";
					$keyin_ip				=get_ip();
					
					

				//-----------------------------------------------
				//錯誤處理
	        	//-----------------------------------------------
				
					//content_array處理
					for($i = 0;$i<count($reply_content_array);$i++){
						if(trim($reply_content_array[$i])== ""){
							$msg='
							<script>
								alert("請確認內容都有輸入");
								history.back(-1);
							</script>
							';
							die($msg);
							
						}
					}
					//user_id處理
					if($user_id===0){
						$msg='
							<script>
								alert("使用者不存在!");
								history.back(-1);
							</script>
						';
						die($msg);
					}
					//article_id處理
					if($article_id===0){
						$msg='
							<script>
								alert("文章不存在!");
								history.back(-1);
							</script>
						';
						die($msg);
					}
					
				//-----------------------------------------------
				//參數處理
	        	//-----------------------------------------------
				
					$reply_content		 = implode($reply_content_array);
				
					
					
					//-------------------------------------------------------------------------------
					//SQL-判斷是否有邀請討論(for mssr_user_request_discussion_rev、mssr_user_request)
					//-------------------------------------------------------------------------------
					$has_request_discussion	= FALSE;
					
					$sql="
						SELECT
						  `mssr_user_request`.`request_id`
						FROM
							`mssr_user_request`
						INNER JOIN
							`mssr_user_request_discussion_rev`	ON
							`mssr_user_request`.`request_id` = `mssr_user_request_discussion_rev`.`request_id`
						WHERE 1=1
							AND	`mssr_user_request_discussion_rev`.`article_id` =$article_id
							AND	`mssr_user_request`.`request_to`				=$user_id
					
					";
					$arrys_result_request_discussion=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
					if(!empty($arrys_result_request_discussion)){
						$has_request_discussion = TRUE;
						
					
					}
					
					//-----------------------------------------------
					//SQL-查book_sid(for mssr_article_reply_book_rev)
					//-----------------------------------------------
						$sql="
							SELECT
								`book_sid`
							FROM
								`mssr_article_book_rev`
							WHERE
								`article_id`=$article_id
						";
						$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
						$numrow=count($arrys_result);
						$book_sid = $arrys_result[0]['book_sid'];
				//-----------------------------------------------
				//SQL-寫入 mssr_forum_reply_article
	        	//-----------------------------------------------
					$sql="
						# for mssr_forum_reply_article
						INSERT INTO `mssr_forum_article_reply` SET
							`cat_id`				= {$cat_id}			     ,
							`user_id`				= {$user_id}		 	 ,
							`article_id`			= {$article_id}			 ,
							`reply_content`			='{$reply_content}'		 ,
							`reply_refer_code`		='{$reply_refer_code}'   ,
							`reply_type`			='{$reply_type}'		 ,
							`reply_state`			='{$reply_state}'		 ,
							`reply_like_cno`		= {$reply_like_cno}		 ,
							`keyin_cdate`			= {$keyin_cdate}		 ,
							`keyin_ip`				='{$keyin_ip}'  		 ;
					";
					//送出
					$err ='DB QUERY FAIL1';
					$sth=$conn_mssr->prepare($sql);
					$sth->execute()or die($err);

					//PDO-lastInsertId
					$reply_id_lastinsertid = $conn_mssr->lastInsertId();

				//-----------------------------------------------
				//SQL-寫入for mssr_forum_article_reply_log與mssr_article_reply_book_rev
	        	//-----------------------------------------------
					$sql="
						# for mssr_forum_article_reply_log
						INSERT INTO `mssr_forum_article_reply_log` SET
							`cat_id`				= {$cat_id}			     	 ,
							`user_id`				= {$user_id}		 	 	 ,
							`article_id`			= {$article_id}			 	 ,
							`reply_id`				= {$reply_id_lastinsertid}	 ,
							`reply_content`			='{$reply_content}' 		 ,
							`reply_refer_code`		='{$reply_refer_code}'       ,
							`reply_type`			='{$reply_type}'			 ,
							`reply_state`			='{$reply_state}'			 ,
							`reply_like_cno`		= {$reply_like_cno}		  	 ,
							`keyin_cdate`			= {$keyin_cdate}		 	 ,
							`keyin_ip`				='{$keyin_ip}'   			 ;

						# for mssr_article_reply_book_rev
						INSERT INTO `mssr_article_reply_book_rev` SET
							`book_sid`				='{$book_sid}'				 ,
							`article_id`			= {$article_id} 			 ,
							`reply_id`				= {$reply_id_lastinsertid} 	 ;
					";
					//送出
					$err ='DB QUERY FAIL2';
					$sth=$conn_mssr->prepare($sql);
					$sth->execute()or die($err);

				//-----------------------------------------------
				//SQL-更新mssr_article_book_rev
	        	//-----------------------------------------------
					$sql="
						UPDATE
							`mssr_article_book_rev`
						SET
							`reply_cdate`	={$keyin_cdate}

						WHERE 1=1
							AND `book_sid`	='$book_sid'
							AND `article_id`=$article_id
					";
					//送出
					$err ='DB QUERY FAIL3';
					$sth=$conn_mssr->prepare($sql);
					$sth->execute()or die($err);
					
					
					
				//-----------------------------------------------
				//SQL-有邀請的一同討論，更新 mssr_user_request
	        	//-----------------------------------------------
				if($has_request_discussion){
					$request_id 	=$arrys_result_request_discussion[0]['request_id'];
				
					$sql="
						# for update mssr_user_request
						UPDATE 
							`mssr_user_request` 
						SET
							`request_state` = 2
						WHERE 1=1
							AND `request_id` =  {$request_id};
							
					";
		
					$err ='DB QUERY FAIL5';
					$sth=$conn_mssr->prepare($sql);
					$sth->execute()or die($err);
					
				}

	//---------------------------------------------------
    //檢驗
    //---------------------------------------------------
		//echo "<pre>";
		//print_r($sql);
		//echo "</pre>";
		//die();
	///---------------------------------------------------
    //重導頁面
    //---------------------------------------------------
        $url ="mssr_forum_book_reply.php?article_id=";
		$url =$url.$article_id;
        header("Location: {$url}");
?>




