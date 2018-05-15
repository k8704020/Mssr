
<?php
//-------------------------------------------------------
//mssr_forum
//精華區新增類別頁面
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
					$forum_id 				=(int)$_POST["forum_id"];
					
				//-----------------------------------------------
				//檢核上傳內容
	        	//-----------------------------------------------
				//$cat_id				=(int)$_POST["cat_id"];
				$sess_uid 				=(int)$_SESSION["uid"];
				$type_name_arrys		=$_POST["new_type_name"];
				
				
			   
			
				//-----------------------------------------------
				//錯誤處理
	        	//-----------------------------------------------
				//取得新增的欄位，判斷是否為空
					foreach($type_name_arrys as $type_name_value){
						
						if($type_name_value==""){
						$msg='
							<script>
								alert("分類名稱不能為空，請重新輸入！");
								history.back(-1);
							</script>
						';
						die($msg);
						}
					}
					

					if($forum_id===0){
						$msg='
							<script>
								alert("聊書小組不存在!");
								history.back(-1);
							</script>
						';
						die($msg);
					}
/* 					if($last_i===0){
						$msg='
							<script>
								alert("error l_i");
								history.back(-1);
							</script>
						';
						die($msg);
					}
					if($new_last_i===0){
						$msg='
							<script>
								alert("error n_i!");
								history.back(-1);
							</script>
						';
						die($msg);
					}
 // */					//if($sess_uid===0){
						// $msg='
							// <script>
								// alert("error n_i!");
								// history.back(-1);
							// </script>
						// ';
						// die($msg);
					// }

					
				
				//-----------------------------------------------
				//SQL-寫入for mssr_forum_best_article_category
	        	//-----------------------------------------------

				foreach($type_name_arrys as $type_name_value){
				
				$type_name = trim($type_name_value);

					$sql="
						# for mssr_forum_best_article_category
						INSERT INTO `mssr_forum_best_article_category` SET
							`create_by`				= {$sess_uid}			 ,
							`edit_by`				= {$sess_uid}		 	 ,
							`forum_id`				= {$forum_id}			 ,
							`cat_name`				='{$type_name}',
							`keyin_cdate`			= NOW()		 ;

					";
		
					//送出
					$err ='DB QUERY FAIL';
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
	//---------------------------------------------------
    //重導頁面
    //---------------------------------------------------
        $url ="mssr_forum_group_discussion_vip.php?forum_id=";
		$url =$url.$forum_id;
        header("Location: {$url}");
?>




