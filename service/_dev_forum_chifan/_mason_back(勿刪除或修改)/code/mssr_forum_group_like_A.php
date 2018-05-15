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

        $get_chk=array(
			'type',
			'user_id',
            'article_id'
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
    //type			類別
	//user_id		使用者索引
	//article_id	文章|回覆索引

        //GET
        $type		=trim($_GET[trim('type')]);
		$user_id	=trim($_GET[trim('user_id')]);
		$article_id	=trim($_GET[trim('article_id')]);

        //SESSION
		
        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //type			類別
	//user_id		使用者索引
	//article_id	文章|回覆索引

        $arry_err=array();

        if($type===''){
           $arry_err[]='類別,未輸入!';
        }

        if($user_id===''){
           $arry_err[]='使用者索引,未輸入!';
        }else{
			$user_id=(int)$user_id;
			if($user_id===0){
				$arry_err[]='使用者索引,錯誤!';
			}
		}
		
        if($article_id===''){
           $arry_err[]='文章|回覆索引,未輸入!';
        }else{
			$article_id=(int)$article_id;
			if($article_id===0){
				$arry_err[]='文章|回覆索引,錯誤!';
			}
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

			$type		=$type;
			$user_id	=$user_id;
			$article_id	=$article_id;
			
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
            //檢核文章 | 回覆存在與否
            //-------------------------------------------	

			if($type==='article'){
				$sql="
					SELECT
						`article_id`
					FROM `mssr_forum_article`
					WHERE 1=1
						AND `article_id`={$article_id}
				";
				$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
				$numrow=count($arrys_result);
				if($numrow===0){
					$msg="文章不存在, 請重新輸入!";
					$jscript_back="
						<script>
							alert('{$msg}');
							history.back(-1);
						</script>
					";
					die($jscript_back);
				}		
				echo("article");
				
			}else if($type==='reply'){
				$sql="
					SELECT
						`reply_id`
					FROM `mssr_forum_article_reply`
					WHERE 1=1
						AND `reply_id`={$article_id}
				";
				$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
				$numrow=count($arrys_result);
				if($numrow===0){
					$msg="回覆不存在, 請重新輸入!";
					$jscript_back="
						<script>
							alert('{$msg}');
							history.back(-1);
						</script>
					";
					die($jscript_back);
				}		
				echo("reply");
					
			}else{
				
			}
			
            //-------------------------------------------
            //是不是有按過讚
            //-------------------------------------------	
			
			if($type==='article'){
				$sql="
					SELECT
						`user_id`, `article_id`
					FROM `mssr_forum_article_like_log`
					WHERE 1=1
						AND `user_id`		={$user_id}
						AND `article_id`	={$article_id}
				";
				$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
				$numrow=count($arrys_result);
				if($numrow!==0){
					$msg="你已經按過讚了!";
					$jscript_back="
						<script>
							alert('{$msg}');
							history.back(-1);
						</script>
					";
					die($jscript_back);
				}		
				echo("article_like");
			}else if($type==='reply'){
				$sql="
					SELECT
						`user_id`, `reply_id`
					FROM `mssr_forum_article_reply_like_log`
					WHERE 1=1
						AND `user_id`		={$user_id}
						AND `reply_id`		={$article_id}
				";
				$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
				$numrow=count($arrys_result);
				if($numrow===1){
					$msg="你已經按過讚了!";
					$jscript_back="
						<script>
							alert('{$msg}');
							history.back(-1);
						</script>
					";
					die($jscript_back);
				}		
				echo("reply_like");
			}else{
				
			}
			
        //-----------------------------------------------
        //預設值
        //-----------------------------------------------
		
			$user_id		=(int)$user_id;
			$article_id		=(int)$article_id;
			$keyin_cdate	="NOW()";
			
        //-----------------------------------------------
        //處理
        //-----------------------------------------------
		
			if($type==='article'){
				$sql="
					# for mssr_forum_article_like_log
					INSERT INTO `mssr_forum_article_like_log` SET
						`user_id`         		=  {$user_id          		} ,
						`article_id`       		=  {$article_id        		} ,
						`keyin_cdate`         	=  {$keyin_cdate           	} ;
            	";	
			}else{	
				$sql="
					# for mssr_forum_article_reply_like_log
					INSERT INTO `mssr_forum_article_reply_like_log` SET
						`user_id`         		=  {$user_id          		} ,
						`reply_id`       		=  {$article_id        		} ,
						`keyin_cdate`         	=  {$keyin_cdate           	} ;
            	";	
			}
					
            //送出
			$err ='DB QUERY FAIL';
			$sth=$conn_mssr->prepare($sql);
			$sth->execute()or die($err);
			
	///---------------------------------------------------
    //重導頁面
    //---------------------------------------------------
		
		if($type==='reply'){
			$reply_id		=(int)$article_id;
			$sql="
				SELECT
					`article_id`
				FROM `mssr_article_reply_forum_rev`
				WHERE 1=1
					AND `reply_id`		={$reply_id}
			";
			$arrys_result_article_id=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
			if(count($arrys_result_article_id)!==0){
				$article_id=(int)$arrys_result_article_id[0]['article_id'];
			}else{
				$msg="查無文章資訊!";
				$jscript_back="
					<script>
						alert('{$msg}');
						history.back(-1);
					</script>
				";
				die($jscript_back);
			}	
		}
	
        $url ="mssr_forum_group_reply.php?article_id=";
		$url =$url.$article_id;
        header("Location: {$url}");		
?>



