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
				

					$type					=(int)$_POST["type"];
					$user_id				=(int)$_SESSION["uid"];
					$article_title			=mysql_prep(strip_tags(trim($_POST["mssr_input_box_name_title"])));
					//$article_content		=mysql_prep(strip_tags(trim($_POST["mssr_input_box_name_content"])));
					$article_content		= implode($_POST["mssr_input_box_name_content"]);
					$book_sid 				=mysql_prep(strip_tags(trim($_POST["book_sid"])));
                    $action_code            =mysql_prep(strip_tags(trim($_POST["action_code"])));
					$article_refer_code    	=(isset($_POST["article_refer_code"]))?mysql_prep(strip_tags(trim($_POST["article_refer_code"]))):'';
					
				//-----------------------------------------------
				//錯誤處理
	        	//-----------------------------------------------
					//type處理
					if($type===0){
						$msg='
							<script>
								alert("文章分類不存在!");
								history.back(-1);
							</script>
						';
						die($msg);
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
					//article_title處理
					if($article_title===''){
						$msg='
							<script>
								alert("請輸入標題內容!");
								history.back(-1);
							</script>
						';
						die($msg);
					}
					//article_content處理
					if($article_content===''){
						$msg='
							<script>
								alert("請輸入回覆內容!");
								history.back(-1);
							</script>
						';
						die($msg);
					}
					//book_sid處理
					if($book_sid===''){
						$msg='
							<script>
								alert("書籍不存在!");
								history.back(-1);
							</script>
						';
						die($msg);
					}

				//-----------------------------------------------
				//檢核上傳內容
	        	//-----------------------------------------------

					$cat_id					=$type;
					$user_id				=$user_id;
					$article_title			=$article_title;
					$article_content		=$article_content;
					$article_refer_code		=$article_refer_code;
					$article_state			='正常';
					$article_like_cno		=(int)0;//預設值
					$keyin_cdate			="NOW()";
					$keyin_ip				=get_ip();


				
				//-----------------------------------------------
				//SQL-寫入for mssr_forum_article
	        	//-----------------------------------------------
					$sql="
						# for mssr_forum_article
						INSERT INTO `mssr_forum_article` SET
							`cat_id`				= {$cat_id}			     ,
							`user_id`				= {$user_id}		 	 ,
							`article_title`			='{$article_title}'		 ,
							`article_content`		='{$article_content}'	 ,
							`article_refer_code`	='{$article_refer_code}' ,
							`article_state`			='{$article_state}'		 ,
							`article_like_cno`		= {$article_like_cno}    ,
							`keyin_cdate`			= {$keyin_cdate}		 ,
							`keyin_ip`				='{$keyin_ip}'			 ;
					";
					//送出
					$err ='DB QUERY FAIL';
					$sth=$conn_mssr->prepare($sql);
					$sth->execute()or die($err);

				
				
					//PDO-lastInsertId
					$article_id_lastinsertid = $conn_mssr->lastInsertId();
				//-----------------------------------------------
				//SQL-寫入for mssr_forum_article_log與mssr_article_book_rev
	        	//-----------------------------------------------
					$sql="
						# for mssr_forum_article_log
						INSERT INTO `mssr_forum_article_log` SET
							`cat_id`				= {$cat_id}			         ,
							`user_id`				= {$user_id}		 	     ,
							`article_id`			= {$article_id_lastinsertid} ,
							`article_title`			='{$article_title}'			 ,
							`article_content`		='{$article_content}'		 ,
							`article_refer_code`	='{$article_refer_code}'	 ,
							`article_state`			='{$article_state}'			 ,
							`article_like_cno`		= {$article_like_cno}        ,
							`keyin_cdate`			= {$keyin_cdate}		     ,
							`keyin_ip`				='{$keyin_ip}'				 ;


						# for mssr_article_book_rev
						INSERT INTO `mssr_article_book_rev` SET
							`book_sid`				='{$book_sid}'				 ,
							`article_id`			= {$article_id_lastinsertid} ,
							`article_cdate`			= {$keyin_cdate} 			 ;
					";


				
					//送出
					$err ='DB QUERY FAIL';
					$sth=$conn_mssr->prepare($sql);
					$sth->execute()or die($err);
					
					
				
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
        $url ="mssr_forum_book_discussion.php?book_sid=";
		$url =$url.$book_sid;
			//echo "<pre>";
//		print_r($url);
//		echo "</pre>";

        header("Location: {$url}");
?>