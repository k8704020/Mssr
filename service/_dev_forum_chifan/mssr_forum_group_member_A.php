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
				
				$user_id = (int)$_POST["user_id"];
				$forum_id = (int)$_POST["forum_id"];
				$user_type	= trim(mysql_prep(strip_tags($_POST["optionsRadios"])));
				

				//-----------------------------------------------
				//錯誤處理
	        	//-----------------------------------------------
				
					if($user_id===0){
						$msg='
							<script>
								alert("user_id error");
								history.back(-1);
							</script>
						';
						die($msg);
					}
					if($forum_id===0){
						$msg='
							<script>
								alert("forum_id error");
								history.back(-1);
							</script>
						';
						die($msg);
					}
					
					if($user_type==""){
						$msg='
							<script>
								alert("member_type error");
								history.back(-1);
							</script>
						';
						die($msg);
					}
					
				//-----------------------------------------------
	        	//SQL
	        	//-----------------------------------------------
				
	
				
					$sql="
					UPDATE 
						`mssr_user_forum` 
					SET
						`user_type`				= '{$user_type         		}'    		
					WHERE 1=1
						AND `forum_id` 			=  {$forum_id         		}
						AND `user_id` 			=  {$user_id         		}
					LIMIT 1
					";	




					 //送出
				$err ='DB QUERY FAIL';
				$sth=$conn_mssr->prepare($sql);
				$sth->execute()or die($err);
					
					
			

	//---------------------------------------------------
    //檢驗
    //---------------------------------------------------
		//		echo "<pre>";
		//		print_r($sql);
		//		echo "</pre>";
		//		die();

	//---------------------------------------------------
    //重導頁面
    //---------------------------------------------------
        $url ="mssr_forum_group_member.php?forum_id=";
		$url =$url.$forum_id;


        header("Location: {$url}");
?>