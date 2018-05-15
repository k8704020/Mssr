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
					$user_id 				=(int)$_SESSION["uid"];
					$forum_id				=(int)$_POST["forum_id"];
					$book_isbn				=(int)$_POST["mssr_group_add_book_name_title"];
					
					
					
					
				//-----------------------------------------------
				//錯誤處理
	        	//-----------------------------------------------
					//book_isbn處理
					if($book_isbn===0){
						$msg='
							<script>
								alert("書籍不存在!");
								history.back(-1);
							</script>
						';
						die($msg);
					}
					
				//-----------------------------------------------
	        	//SQL-用ISBN找書籍
	        	//-----------------------------------------------
					$sql="
						SELECT *
						FROM(
								SELECT
									`book_isbn_13`, `book_sid`, `keyin_cdate`
								FROM
									`mssr_book_library`
							UNION ALL
								SELECT
									`book_isbn_13`, `book_sid`, `keyin_cdate`
								FROM
									`mssr_book_global`
							UNION ALL
								SELECT
									`book_isbn_13`, `book_sid`, `keyin_cdate`
								FROM
									`mssr_book_class`
						) V1
						WHERE
							`book_isbn_13` = $book_isbn
						ORDER BY
							`keyin_cdate` DESC
					";
					$arrys_book_isbn=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
					//$numrow_which_group=count($arrys_book_isbn);

				
				//-----------------------------------------------
				//檢核上傳內容
	        	//-----------------------------------------------
					$create_by				=$user_id;
					$edit_by				=$user_id;	
					$forum_id				=$forum_id;
					$book_sid				=$arrys_book_isbn[0]['book_sid'];
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
							`book_sid`				='{$book_sid}				',
							`keyin_cdate`			= {$keyin_cdate}			 ,
							`keyin_mdate`			= {$keyin_mdate}    	 ;
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
        $url ="mssr_forum_group_shelf.php?forum_id=";
		$url =$url.$forum_id;
			//echo "<pre>";
//		print_r($url);
//		echo "</pre>";

        header("Location: {$url}");
?>