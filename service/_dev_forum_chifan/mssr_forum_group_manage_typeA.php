<?php
//-------------------------------------------------------
//mssr_forum_group_manage_typeA
//(edit、delete)
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
				//$type_name			=trim($_POST["type_name"]);
				$cat_id				=(int)$_POST["cat_id"];
				$action_type	    =trim($_POST["action_type"]);
				//-----------------------------------------------
				//錯誤處理
	        	//-----------------------------------------------
				
					if($cat_id===0){
						$msg='
							<script>
								alert("id error");
							</script>
						';
						die($msg);
					}
				
					if($action_type==""){
						$msg='
							<script>
								alert("action error");
							</script>
						';
						die($msg);
					}
				//-----------------------------------------------
	        	//SQL
	        	//-----------------------------------------------
				
				//edit
				if($action_type=="edit"){
					$type_name			=trim($_POST["type_name"]);
					if($type_name==""){
						$msg='
							<script>
								alert("請輸入類別名稱");
							</script>
						';
						die($msg);
					}
				
					$sql="
					UPDATE 
						`mssr_forum_best_article_category` 
					SET
						`cat_name` = '{$type_name}'
					WHERE 1=1
						AND `cat_id` 			=  {$cat_id}
					";	

					//送出
					$err ='DB QUERY FAIL';
					$sth=$conn_mssr->prepare($sql);
					$sth->execute()or die($err);
					
					die("類別名稱已修改成功");
				}
				
				//delete
				if($action_type=="del"){
			
				
					$sql="
					UPDATE 
						`mssr_forum_best_article_category` 
					SET
						`cat_state` = '2'
					WHERE 1=1
						AND `cat_id` 			=  {$cat_id}
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
//		print_r($book_sid);
//		echo "</pre>";
//		die();

	//---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

?>

