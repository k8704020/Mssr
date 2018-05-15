<?php
//-------------------------------------------------------
//mssr_forum_group_move_best_A
//聊書小組	移至精華區處理頁
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
				$sess_uid				=(int)$_SESSION["uid"];
				$forum_id		    	=(int)$_POST["forum_id"];
				$choose_article_id	    =(int)$_POST["choose_article_id"];
				$cat_id				    =(int)$_POST["cat_id"];
                $has_record             =false;


				//-----------------------------------------------
				//錯誤處理
	        	//-----------------------------------------------
					if($sess_uid===0){
						$msg='
							<script>
								alert("使用者ID錯誤，從重新選擇");
								history.back(-1);
							</script>
						';
						die($msg);
					}

					if($cat_id===0){
						$msg='
							<script>
								alert("分類ID錯誤，請重新選擇");
								history.back(-1);
							</script>
						';
						die($msg);
					}

					if($forum_id==""){
						$msg='
							<script>
								alert("聊書小組ID錯誤，請重新選擇");
								history.back(-1);
							</script>
						';
						die($msg);
					}

					if($choose_article_id==""){
						$msg='
							<script>
								alert("文章ID錯誤，請重新選擇");
								history.back(-1);
							</script>
						';
						die($msg);
					}

                //-------------------------------------------
                //檢核精華區是否有資料
                //-------------------------------------------

                $sql="
                    SELECT
                        `article_id`
                    FROM
						`mssr_forum_best_article_category_rev`
                    WHERE
					     `article_id` = $choose_article_id


                ";
               // echo $sql;
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

                if(!empty($arrys_result)){
                   $has_record = TRUE;
                }




				//-----------------------------------------------
	        	//SQL處理
	        	//-----------------------------------------------

					$keyin_cdate			="NOW()";

                    //article_type改為精華區(2)
					$sql="
						UPDATE
							`mssr_forum_article`
						SET
							`article_type` = '2'
						WHERE 1=1
							AND `article_id`         	=  {$choose_article_id };
					";
					$err ='DB QUERY FAIL';
					$sth=$conn_mssr->prepare($sql);
					$sth->execute()or die($err);

                    //如果分類關聯表已經有紀錄，更新cat_id
                    if($has_record)
                    {
                        $sql="
                            # for mssr_forum_best_article_category_rev
                            UPDATE `mssr_forum_best_article_category_rev` SET
                                `cat_id`=  {$cat_id }
                            WHERE 1=1
                                AND `article_id`=  {$choose_article_id }
                            LIMIT 1;
                        ";
                        //送出
                        echo "jidsfjoi";
                        $err ='DB QUERY FAIL';
                        $sth=$conn_mssr->prepare($sql);
                        $sth->execute()or die($err);
                        echo "test2";

                    }else{


                        //寫入分類關聯表 mssr_forum_best_article_category_rev
                        $sql="
                            # for mssr_forum_best_article_category_rev
                            INSERT INTO `mssr_forum_best_article_category_rev` SET
                                `create_by`				= {$sess_uid}               ,
                                `edit_by`				= {$sess_uid}               ,
                                `article_id`			='{$choose_article_id}'     ,
                                `cat_id`				='{$cat_id}'                ,
                                `keyin_cdate`			={$keyin_cdate}             ;
                        ";
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

	    $url ="mssr_forum_group_discussion.php?forum_id=";
		$url =$url.$forum_id;
        header("Location: {$url}");

?>

