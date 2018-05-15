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
    
		if(empty($_GET["article_id"]))
		{
			echo "文章不存在";
			die();
		}
		if(empty($_GET["forum_id"]))
		{
			echo "文章不存在此小組";
			die();
		}
	
		if(empty($_GET["type"]))
		{
			echo "type is null";
			die();
		}


		
    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //GET
      
		$article_id = (int)$_GET["article_id"];
		$forum_id = (int)$_GET["forum_id"];
		$action_type = trim($_GET["type"]);
		

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
		if($action_type == "")
		{
			$arry_err[] = 'action_type error';
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
           // $conn_user=conn($db_type='mysql',$arry_conn_user);
			
            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------


		
				
            //-------------------------------------------
          
            
			
        //-----------------------------------------------
        //預設值
        //-----------------------------------------------
		
	
		
			
        //-----------------------------------------------
        //處理
        //-----------------------------------------------
			if($action_type == "input"){
				$sql="
					UPDATE 
						`mssr_forum_article` 
					SET
						`article_type` = '2'
					WHERE 1=1
						AND `article_id`         	=  {$article_id };
            	";	
			$url ="mssr_forum_group_discussion.php?forum_id=";
			}else if($action_type == "output"){
				$sql="
					UPDATE 
						`mssr_forum_article` 
					SET
						`article_type` = '1'
					WHERE 1=1
						AND `article_id`         	=  {$article_id };
            	";	
			$url ="mssr_forum_group_discussion_vip.php?forum_id=";
			}else{
				echo "error";
				die();
			}
			
		
            //送出
			$err ='DB QUERY FAIL';
			$sth=$conn_mssr->prepare($sql);
			$sth->execute()or die($err);
			
	///---------------------------------------------------
    //重導頁面
    //---------------------------------------------------
		
		
			
			$url =$url.$forum_id;
			header("Location: {$url}");		
?>