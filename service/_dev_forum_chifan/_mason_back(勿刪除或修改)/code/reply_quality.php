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
    //設定參數
    //---------------------------------------------------
	//article_id	
  
        //GET
		$reply_id	=trim($_GET[trim('reply_id')]);
		
		
		

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
        //檢核
        //-----------------------------------------------
		//user_id		使用者索引
		//book_sid		文章索引

			$reply_id	=$reply_id;

			$sql="          
				UPDATE `mssr_forum_article_reply` SET
					`reply_quality_2`= 0
				WHERE
					`reply_id` = $reply_id;
          	";
			
			//送出
			$err ='DB QUERY FAIL';
			$sth=$conn_mssr->prepare($sql);
			$sth->execute()or die($err);
			
			$sql="          
				UPDATE `mssr_forum_article_reply_log` SET
					`reply_quality_2`= 0
				WHERE
					`reply_id` = $reply_id;
          	";
//echo "<pre>";
//                print_r($sql);
//                echo "</pre>";
//				die();
            //送出
			$err ='DB QUERY FAIL';
			$sth=$conn_mssr->prepare($sql);
			$sth->execute()or die($err);
			
			
			//echo "<pre>";
//		print_r($reply_id);
//		echo "</pre>";
//		die();
			
			
			
			$sql="
				SELECT
					`article_id`
				FROM
					`mssr_forum_article_reply`
				WHERE
					`reply_id`=$reply_id
			";
			$forum_article_id=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$article_id = $forum_article_id[0]['article_id'];
	///---------------------------------------------------
    //重導頁面
    //---------------------------------------------------
		
		

	
        $url ="da_5.php?article_id=";
		$url =$url.$article_id;
        header("Location: {$url}");		


?>