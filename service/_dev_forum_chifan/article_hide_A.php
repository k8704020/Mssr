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
    //接收參數
    //---------------------------------------------------
    //type			類別
	//user_id		使用者索引
	//article_id	文章|回覆索引
	
		if($_GET["article_id"]== null)
		{
			echo "刪除的文章不存在";
			die();
		}
			if($_GET["forum_id"]== null)
		{
			echo "刪除的文章不存在此小組";
			die();
		}


		
    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //type			類別
	//user_id		使用者索引
	//article_id	文章|回覆索引

        //GET
      
		$article_id = (int)$_GET["article_id"];
		$forum_id = (int)$_GET["forum_id"];
		
		
		
		
			

        //SESSION
		
        //分頁
  
		
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

        $arry_err=array();
		if($article_id  == 0)
		{
			$arry_err[] ='文章索引，錯誤!';
		}
		     
		if($forum_id  == 0)
		{
			$arry_err[] ='文章索引，錯誤!';
		}
        
				
        if(count($arry_err)!==0){
            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
            die();
        }

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
		//type			類別
		//user_id		使用者索引
		//article_id	文章|回覆索引

		
				
            //-------------------------------------------
          
            
			
        //-----------------------------------------------
        //預設值
        //-----------------------------------------------
		
	
		
			
        //-----------------------------------------------
        //處理
        //-----------------------------------------------

				$sql="
					UPDATE 
						`mssr_forum_article` 
					SET
						`article_state` = '刪除'
					WHERE 1=1
						AND `article_id`         	=  {$article_id          		};
						
					UPDATE 
						`mssr_forum_article_reply` 
					SET
						`reply_state` = '刪除'
					WHERE 1=1
						AND `article_id`         	=  {$article_id          		};
            	";	
			
					
            //送出
			$err ='DB QUERY FAIL';
			$sth=$conn_mssr->prepare($sql);
			$sth->execute()or die($err);
			
	///---------------------------------------------------
    //重導頁面
    //---------------------------------------------------
		
		
			$url ="mssr_forum_group_discussion.php?forum_id=";
			$url =$url.$forum_id;

        header("Location: {$url}");		
?>