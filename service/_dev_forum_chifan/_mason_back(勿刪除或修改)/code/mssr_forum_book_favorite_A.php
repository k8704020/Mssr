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
	//user_id		使用者索引
	//book_sid		文章索引

        $get_chk=array(
			'user_id',
            'book_sid'
        );
        $get_chk=array_map("trim",$get_chk);
        foreach($get_chk as $get){
            if(!isset($_GET[$get])){
                die();
            }
        }
		
		
		
    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
	//user_id		使用者索引
	//book_sid		文章索引
  
        //GET
		$user_id	=trim($_GET[trim('user_id')]);
		$book_sid	=trim($_GET[trim('book_sid')]);

        //SESSION
		
        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

	

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
	//user_id		使用者索引
	//book_sid		文章索引

        $arry_err=array();
		
        if($user_id===''){
           $arry_err[]='使用者索引,未輸入!';
        }else{
			$user_id=(int)$user_id;
			if($user_id===0){
				$arry_err[]='使用者索引,錯誤!';
			}
		}
		
        if($book_sid===''){
           $arry_err[]='文章|回覆索引,未輸入!';
        }
				
        if(count($arry_err)!==0){
            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
			echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
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
		//user_id		使用者索引
		//book_sid		文章索引

			$user_id	=$user_id;
			$book_sid	=$book_sid;
			
            //-------------------------------------------
            //檢核使用者存在與否
            //-------------------------------------------
			
                $sql="
                    SELECT
                        `uid`
                    FROM `member`
                    WHERE 1=1
                        AND `uid`={$user_id}
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                $numrow=count($arrys_result);
                if($numrow===0){
                    $msg="使用者不存在, 請重新輸入!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }
				
		
				
			
            //-------------------------------------------
            //檢核書籍存在與否
            //-------------------------------------------	

			
				$sql="
						SELECT
							`book_sid`
						FROM `mssr_book_class`
						WHERE 1=1
							AND `book_sid`='{$book_sid}'
					UNION
						SELECT
							`book_sid`
						FROM `mssr_book_library`
						WHERE 1=1
							AND `book_sid`='{$book_sid}'	
					
				";
				$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
				$numrow=count($arrys_result);
				if($numrow==0){
					$msg="書籍不存在, 請重新輸入!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);	
				}
				
			
			
			
		
            //-------------------------------------------
            //是不是有按過讚
            //-------------------------------------------	
			
		
				$sql="
					SELECT
						`user_id`, `book_sid`
					FROM `mssr_book_favorite`
					WHERE 1=1
						AND `user_id`		={$user_id}
						AND `book_sid`		='{$book_sid}'
				";
				$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
				$numrow=count($arrys_result);
				if($numrow!==0){
					$has_favorite = true;
					
				}else{
					$has_favorite = false;
				}
				
			
				
			
	
			
        //-----------------------------------------------
        //預設值
        //-----------------------------------------------
		
			$user_id		=(int)$user_id;
			$book_sid		=mysql_prep($book_sid);
			$keyin_cdate	="NOW()";
			
			
			
	
        //-----------------------------------------------
        //處理
        //-----------------------------------------------
		
			if($has_favorite===true){
				$sql="
					#
					DELETE FROM`mssr_book_favorite` 
					WHERE 1=1
						AND `user_id`         		=  {$user_id          		} 
						AND `book_sid`       		=  '{$book_sid        		}' ;
					
            	";	
			}else{	
				$sql="
					# 
					INSERT INTO `mssr_book_favorite` SET
						`user_id`         		=  {$user_id          		} ,
						`book_sid`       		= '{$book_sid        		}',
						`keyin_cdate`         	=  {$keyin_cdate           	} ;
            	";	
			}
					
					
		//echo "<pre>";
//                print_r($sql);
//                echo "</pre>";
//				die();
            //送出
			$err ='DB QUERY FAIL';
			$sth=$conn_mssr->prepare($sql);
			$sth->execute()or die($err);
			
			
			
			
	///---------------------------------------------------
    //重導頁面
    //---------------------------------------------------
		
		

	
        $url ="mssr_forum_book_discussion.php?book_sid=";
		$url =$url.$book_sid;
        header("Location: {$url}");		
		
		
		
?>



