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
					$user_id 				=(int)$_POST["user_id"];
					$forum_name				=mysql_prep(strip_tags(trim($_POST["mssr_forum_create_group_title"])));
					$forum_content			=mysql_prep(strip_tags(trim($_POST["mssr_forum_create_group_content"])));
					
				//-----------------------------------------------
				//錯誤處理
	        	//-----------------------------------------------
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
					//forum_name處理
					if($forum_name===''){
						$msg='
							<script>
								alert("請輸入討論區名稱!");
								history.back(-1);
							</script>
						';
						die($msg);
					}
					//forum_content處理
					if($forum_content===''){
						$msg='
							<script>
								alert("請輸入討論區簡介!");
								history.back(-1);
							</script>
						';
						die($msg);
					}
			


					
				//-----------------------------------------------
				//檢核上傳內容
	        	//-----------------------------------------------
					$create_by				=$user_id;
					$edit_by				=$user_id;
					$forum_name				=$forum_name;
					$forum_content			=$forum_content;
					$forum_state			='啟用';
					$keyin_cdate			="NOW()";
					$keyin_mdate			="NOW()";
					$keyin_ip				=get_ip();
					
					$user_type				='一般版主';
					$user_state				='啟用';

				//-----------------------------------------------
				//SQL-寫入for mssr_forum
	        	//-----------------------------------------------
					$sql="
						# for mssr_forum
						INSERT INTO `mssr_forum` SET
							`create_by`				= {$create_by}			     ,
							`edit_by`				= {$edit_by}		 	 	 ,
							`forum_name`			='{$forum_name}				',
							`forum_content`			='{$forum_content}			',
							`forum_state`			='{$forum_state}			',
							`keyin_cdate`			= {$keyin_cdate}			 ,
							`keyin_mdate`			= {$keyin_mdate}    	 ,
							`keyin_ip`				='{$keyin_ip}				';
					";
					//送出
					$err ='DB QUERY FAIL';
					$sth=$conn_mssr->prepare($sql);
					$sth->execute()or die($err);
					
					$forum_id_lastinsertid = $conn_mssr->lastInsertId();
				//-----------------------------------------------
				//SQL-寫入for mssr_user_forum
	        	//-----------------------------------------------
					$sql="
						# for mssr_forum
						INSERT INTO `mssr_user_forum` SET
							`forum_id`				= {$forum_id_lastinsertid}			     ,
							`user_id`				= {$user_id}			     ,
							`user_type`				='{$user_type}				',
							`user_state`			='{$user_state}				',
							`keyin_cdate`			= {$keyin_cdate}			 ,
							`keyin_mdate`			= {$keyin_mdate}    	 	 ;
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
        $url ="mssr_forum_group_discussion.php?forum_id=";
		$url =$url.$forum_id_lastinsertid;
			//echo "<pre>";
//		print_r($url);
//		echo "</pre>";

        header("Location: {$url}");
?>