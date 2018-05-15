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
				//檢核上傳內容
	        	//-----------------------------------------------
					$cat_id					=(int)1;//預設值
					$user_id				=(int)$_SESSION["uid"];//預設值
					$article_id				=(int)$_POST["article_id"];
					$reply_content			=mysql_prep(strip_tags(trim($_POST["mssr_comment_input_name_content"])));
					$reply_type				='一般';
					$reply_state			='正常';
					$reply_like_cno			=(int)0;//預設值
					$keyin_cdate			="NOW()";
					$keyin_ip				=get_ip();

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
					//article_id處理
					if($article_id===0){
						$msg='
							<script>
								alert("文章不存在!");
								history.back(-1);
							</script>
						';
						die($msg);
					}
					//reply_content處理
					if($reply_content===''){
						$msg='
							<script>
								alert("請輸入回覆內容!");
								history.back(-1);
							</script>
						';
						die($msg);
					}
				//-----------------------------------------------
				//參數處理
	        	//-----------------------------------------------
					//-----------------------------------------------
					//SQL-查book_sid(for mssr_article_reply_book_rev)
					//-----------------------------------------------
						$sql="
							SELECT
								`book_sid`
							FROM
								`mssr_article_book_rev`
							WHERE
								`article_id`=$article_id
						";
						$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
						$numrow=count($arrys_result);
						$book_sid = $arrys_result[0]['book_sid'];
				//-----------------------------------------------
				//SQL-寫入 mssr_forum_reply_article
	        	//-----------------------------------------------
					$sql="
						# for mssr_forum_reply_article
						INSERT INTO `mssr_forum_article_reply` SET
							`cat_id`				= {$cat_id}			     ,
							`user_id`				= {$user_id}		 	 ,
							`article_id`			= {$article_id}			 ,
							`reply_content`			='{$reply_content}		',
							`reply_type`			='{$reply_type}			',
							`reply_state`			='{$reply_state}    	',
							`reply_like_cno`		= {$reply_like_cno}		 ,
							`keyin_cdate`			= {$keyin_cdate}		 ,
							`keyin_ip`				='{$keyin_ip}			';
					";
					//送出
					$err ='DB QUERY FAIL';
					$sth=$conn_mssr->prepare($sql);
					$sth->execute()or die($err);

					//PDO-lastInsertId
					$reply_id_lastinsertid = $conn_mssr->lastInsertId();

				//-----------------------------------------------
				//SQL-寫入for mssr_forum_article_reply_log與mssr_article_reply_book_rev
	        	//-----------------------------------------------
					$sql="
						# for mssr_forum_article_reply_log
						INSERT INTO `mssr_forum_article_reply_log` SET
							`cat_id`				= {$cat_id}			     	 ,
							`user_id`				= {$user_id}		 	 	 ,
							`article_id`			= {$article_id}			 	 ,
							`reply_id`				= {$reply_id_lastinsertid}	 ,
							`reply_content`			='{$reply_content}			',
							`reply_type`			='{$reply_type}				',
							`reply_state`			='{$reply_state}    		',
							`reply_like_cno`		= {$reply_like_cno}		  	 ,
							`keyin_cdate`			= {$keyin_cdate}		 	 ,
							`keyin_ip`				='{$keyin_ip}			 	';

						# for mssr_article_reply_book_rev
						INSERT INTO `mssr_article_reply_book_rev` SET
							`book_sid`				='{$book_sid}			    ',
							`article_id`			= {$article_id} 			 ,
							`reply_id`				= {$reply_id_lastinsertid} 	 ;
					";
					//送出
					$err ='DB QUERY FAIL';
					$sth=$conn_mssr->prepare($sql);
					$sth->execute()or die($err);

				//-----------------------------------------------
				//SQL-更新mssr_article_book_rev
	        	//-----------------------------------------------
					$sql="
						UPDATE
							`mssr_article_book_rev`
						SET
							`reply_cdate`	={$keyin_cdate}

						WHERE 1=1
							AND `book_sid`	='$book_sid'
							AND `article_id`=$article_id
					";
					//送出
					$err ='DB QUERY FAIL';
					$sth=$conn_mssr->prepare($sql);
					$sth->execute()or die($err);

	//---------------------------------------------------
    //檢驗
    //---------------------------------------------------
		//echo "<pre>";
		//print_r($sql);
		//echo "</pre>";
		//die();
	///---------------------------------------------------
    //重導頁面
    //---------------------------------------------------
        $url ="mssr_forum_reply.php?article_id=";
		$url =$url.$article_id;
        //header("Location: {$url}");
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="Content-Language" content="UTF-8">
    <script	type="text/javascript" 	src="jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="inc/add_action_forum_log/code.js"></script>
    <link rel="stylesheet" href=""/>
</head>

<body>

</body>
<script type="text/javascript">
//-------------------------------------------------------
//範例
//-------------------------------------------------------

	function action_log(
        process_url,
        action_code,
        action_from,
        user_id_1,
        user_id_2,
        book_sid_1,
        book_sid_2,
        forum_id_1,
        forum_id_2,
        article_id,
        reply_id,
        go_url
    ){
        add_action_forum_log(
            process_url,
            action_code,
            action_from,
            user_id_1,
            user_id_2,
            book_sid_1,
            book_sid_2,
            forum_id_1,
            forum_id_2,
            article_id,
            reply_id,
            go_url
        );
    }

    window.onload=function(){
        action_log('inc/add_action_forum_log/code.php','ba9',<?php echo $_SESSION["uid"];?>,0,0,'<?php echo $book_sid;?>','',0,0,<?php echo $article_id;?>,<?php echo $reply_id_lastinsertid;?>,'<?php echo $url;?>');
    }

</script>
</Html>




