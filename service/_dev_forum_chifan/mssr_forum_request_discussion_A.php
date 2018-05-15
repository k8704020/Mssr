<?php
    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        ///SESSION
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
    //登入判斷
    //---------------------------------------------------


    //---------------------------------------------------
    //權限判斷
    //---------------------------------------------------


    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
		$sess_uid 		=(int)$_SESSION["uid"];
		$friend_uid		=$_POST["friend_uid"];
		$friend_uid_con = count($friend_uid);
		$article_id		= (int)$_POST["article_id"];
		$site 			= trim($_POST["site"]);
		

	


    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
		//分頁
        $psize=(isset($_POST['psize']))?(int)$_POST['psize']:10;
        $pinx =(isset($_POST['pinx']))?(int)$_POST['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;
	
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
      $arry_err=array();

        if($sess_uid===''){
           $arry_err[]='使用者主索引,未輸入!';
        }else{
            $sess_uid=(int)$sess_uid;
            if($sess_uid===0){
                $arry_err[]='使用者主索引,錯誤!';
            }
        }
        if($friend_uid===''){
           $arry_err[]='好友主索引,未輸入!';
        }
		if($article_id===''){
           $arry_err[]='文章主索引,未輸入!';
        }else{
            $article_id=(int)$article_id;
            if($article_id===0){
                $arry_err[]='文章主索引,,錯誤!';
            }
        }

        if(count($arry_err)!==0){
            if(1==1){//除錯用
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
        //檢核資料正確性
	
        //-----------------------------------------------


    //---------------------------------------------------
    //進入前處理(預設值)
	
    //---------------------------------------------------


    //---------------------------------------------------
    //SQL處理 INSERT, UPDATE, DELETE
    //---------------------------------------------------
		for($i=0; $i<$friend_uid_con;$i++){
		
		
		//寫入mssr_user_request
		$sql="
               INSERT INTO `mssr_user_request` SET
                        `request_from`      =   {$sess_uid  		}		,
                        `request_to`     	=   {$friend_uid[$i]   	}		,
                        `keyin_cdate`   	=   NOW()       				 ;
                ";
        

            //送出
			
            $err ='DB QUERY FAIL1';
            $sth=$conn_mssr->prepare($sql);
            $sth->execute()or die($err);
			$request_id_lastinsertid = $conn_mssr->lastInsertId();
	
		
		//寫入mssr_user_request_forum_join_rev
		$sql="
               INSERT INTO `mssr_user_request_discussion_rev` SET
                        `request_id`      	 =   {$request_id_lastinsertid  }	,
                        `article_id`    	 =   {$article_id   }				,
						`rev_id`			 =	NULL							;
                ";
            //送出
		

	
            $err ='DB QUERY FAIL2';
            $sth=$conn_mssr->prepare($sql);
            $sth->execute()or die($err);
			}

    //---------------------------------------------------
    //頁面導向
    //---------------------------------------------------
	
        $msg="邀請成功，好友已接收到邀請！！";
        $jscript_back="
            <script>
                alert('{$msg}');
                location.href='{$site}?article_id={$article_id}&psize={$psize}&pinx={$pinx}';
            </script>
        ";
        die($jscript_back);
		
        
?>