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
	//id			文章|回覆索引

        $get_chk=array(
			'type',
			'report_from',
			'report_to',
            'id'
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
	//id	文章|回覆索引

        //GET
        $type			=trim($_GET[trim('type')]);
		$report_from	=trim($_GET[trim('report_from')]);
		$report_to		=trim($_GET[trim('report_to')]);
		$id				=trim($_GET[trim('id')]);
	
		
		
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //type			類別
	//user_id		使用者索引
	//id			文章|回覆索引
	
        $arry_err=array();

        if($type===''){
           $arry_err[]='類別,未輸入!';
        }

        if($report_from===''){
           $arry_err[]='使用者索引,未輸入!';
        }else{
			$report_from=(int)$report_from;
			if($report_from===0){
				$arry_err[]='使用者索引,錯誤!';
			}
		}
		
		if($report_to===''){
           $arry_err[]='使用者索引,未輸入!';
        }else{
			$report_to=(int)$report_to;
			if($report_to===0){
				$arry_err[]='使用者索引,錯誤!';
			}
		}
		
        if($id===''){
           $arry_err[]='文章|回覆索引,未輸入!';
        }else{
			$id		=(int)$id;
			if($id===0){
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
		//id			文章|回覆索引

			$type			=$type;
			$report_from	=(int)$report_from;
			$report_to		=(int)$report_to;
			$id				=(int)$id;
			
			
				
				
           	//-------------------------------------------
            //用reply_id找article_id
            //-------------------------------------------
				if($type==='reply'){
				
					$sql="
                    SELECT
                        `article_id`, `reply_id`
                    FROM `mssr_forum_article_reply`
                    WHERE 1=1
                        AND `reply_id`={$id}
					";
					$arrys_result_rs_article_id=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$rs_article_id = $arrys_result_rs_article_id[0]['article_id'];
					

					
				}

                
         
			
			
            //-------------------------------------------
            //檢核使用者存在與否
            //-------------------------------------------
			
                $sql="
                    SELECT
                        `uid`
                    FROM `member`
                    WHERE 1=1
                        AND `uid`={$report_to}
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                $numrow=count($arrys_result);
                if($numrow===0){
                    $msg="user_error";
                  
                    die($msg);
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
						AND `article_id`={$id}
				";
				$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
				$numrow=count($arrys_result);
				if($numrow===0){
					$msg="article_error";
					
					die($msg);
				}		
				
				
			}else if($type==='reply' || $type==='forum_reply'){
		
				$sql="
					SELECT
						`reply_id`
					FROM `mssr_forum_article_reply`
					WHERE 1=1
						AND `reply_id`={$id}
				";
				$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
				$numrow=count($arrys_result);
				if($numrow===0){
					$msg="reply_error";
				
					die($msg);
				}		
				
					
			}else{
				
			}
			
		 //-------------------------------------------
         //是不是有檢舉過
         //-------------------------------------------	
              
			if($type==='article'){
				$sql="
					SELECT
						`report_from`, `report_to`, `article_id`
					FROM `mssr_forum_article_report_log`
					WHERE 1=1
						AND `report_from`		={$report_from}
						AND `article_id`	={$id}
						AND `report_to`	={$report_to}
				";
				$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
				$numrow=count($arrys_result);
				if($numrow!==0){
					$msg="has_report";
					
					die($msg);
				}		
				
			}else if($type==='reply'){
		
				$sql="
					SELECT
						`report_from`, `report_to`, `article_id`
					FROM `mssr_forum_article_reply_report_log`
					WHERE 1=1
						AND `report_from`		={$report_from}
						AND `reply_id`	={$id}
						AND `report_to`	={$report_to}
				";
				$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
				$numrow=count($arrys_result);

				if($numrow===1){
					
					$msg="has_report";
					
					die($msg);
				}		
			
			}else{
				
			}
			
        
			
        //-----------------------------------------------
        //處理
        //-----------------------------------------------
			if($type==='article'){
			 
				//-----------------------------------------------
				//預設值
				//-----------------------------------------------
					$report_from	=(int)$report_from;
					$report_to		=(int)$report_to;
					$article_id		=(int)$id;
					$keyin_cdate	="NOW()";
					$keyin_ip 		=get_ip();
					
				//-----------------------------------------------
				//SQL-寫入 mssr_forum_article_report_log
	        	//-----------------------------------------------
					$sql="
						# for mssr_forum_article_report_log
						INSERT INTO `mssr_forum_article_report_log` SET
							`report_from`			= {$report_from}		,
							`report_to`				= {$report_to}		 	,
							`article_id`			= {$id}					,
							`keyin_cdate`			= {$keyin_cdate}		,
							`keyin_ip`				='{$keyin_ip}'			;
					";
				
			}else if($type==='reply'){	
				//-----------------------------------------------
				//預設值
				//-----------------------------------------------
					$report_from	=(int)$report_from;
					$report_to		=(int)$report_to;
					$reply_id 		=(int)$id;
					$article_id		=(int)$rs_article_id;
					$keyin_cdate	="NOW()";
					$keyin_ip 		=get_ip();
					
	
				//-----------------------------------------------
				//SQL-寫入 mssr_forum_article_reply_report_log
	        	//-----------------------------------------------
					$sql="
						# for mssr_forum_article_reply_report_log
						INSERT INTO `mssr_forum_article_reply_report_log` SET
							`report_from`			= {$report_from}		,
							`report_to`				= {$report_to}		 	,
							`article_id`			= {$id}					,
							`reply_id`				= {$reply_id}			,
							`keyin_cdate`			= {$keyin_cdate}		,
							`keyin_ip`				='{$keyin_ip}'			;
					";
				
				
			}
			
            //送出
			$err ='DB QUERY FAIL';
			$sth=$conn_mssr->prepare($sql);
			$sth->execute()or die($err);
			
// 		echo "<pre>";
//		print_r($rs_article_id);
//		echo "</pre>";
//		die();
			
	///---------------------------------------------------
    //重導頁面
    //---------------------------------------------------
		
	
?>