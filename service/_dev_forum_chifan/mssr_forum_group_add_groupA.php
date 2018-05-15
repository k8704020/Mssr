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
    //接收參數
    //---------------------------------------------------

        $post_chk=array(
            'sess_uid',
            'forum_id '
        );
        $post_chk=array_map("trim",$post_chk);
        foreach($post_chk as $post){
            if(!isset($_GET[$post])){
                die();
            }
        }

	  
    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //sess_uid  使用者主索引
    //user_id   被加入的人主索引

        //GET
        $sess_uid=trim($_GET[trim('sess_uid')]);
        $forum_id =trim($_GET[trim('forum_id')]);
		$site =trim($_GET[trim('site')]);
		

        //SESSION

        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;
		
	
		
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //sess_uid  使用者主索引
    //user_id   被加入的人主索引

        $arry_err=array();

        if($sess_uid===''){
           $arry_err[]='使用者主索引,未輸入!';
        }else{
            $sess_uid=(int)$sess_uid;
            if($sess_uid===0){
                $arry_err[]='使用者主索引,錯誤!';
            }
        }
        if($forum_id===''){
           $arry_err[]='被加入的人主索引,未輸入!';
        }else{
            $forum_id=(int)$forum_id;
            if($forum_id===0){
                $arry_err[]='被加入的人主索引,錯誤!';
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
        //檢核
        //-----------------------------------------------
        //sess_uid  使用者主索引
        //user_id   被加入的人主索引

            $sess_uid    =(int)$sess_uid;
            $forum_id    =(int)$forum_id;
            $has_record =false;

            //-------------------------------------------
            //檢核使用者是否存在
            //-------------------------------------------

                $sql="
                    SELECT
                        `uid`
                    FROM `member`
                    WHERE 1=1
                        AND `uid`={$sess_uid}
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                if(empty($arrys_result)){
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
            //檢核社團是否存在
            //-------------------------------------------

                $sql="
                    SELECT
                        `forum_id`
                    FROM `mssr_forum`
                    WHERE 1=1
                        AND `forum_id`={$forum_id}
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(empty($arrys_result)){
                    $msg="社團不存在, 請重新輸入!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }

            //-------------------------------------------
            //檢核是否已經加入社團
            //-------------------------------------------
				
                $sql="
                    SELECT *
                    FROM 
						`mssr_user_forum`
                    WHERE 1=1
						AND `forum_id` = $forum_id
						AND `user_id` = $sess_uid
						AND `user_state` = '啟用'
                        
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				
                if(!empty($arrys_result)){
                    $msg="已經加入社團了!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }


			

            //-------------------------------------------
            //檢核是否還在確認中
            //-------------------------------------------

                $sql="
                    SELECT *
                    FROM 
						`mssr_user_forum`
                    WHERE 1=1
						AND `forum_id` = $forum_id
						AND `user_id` = $sess_uid
						AND `user_state` = '申請中'
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($arrys_result)){
                    $msg="申請社團目前雙方還在確認中!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }

				
            //-------------------------------------------
            //檢核是否已有社團紀錄
            //-------------------------------------------

                $sql="
                    SELECT *
                    FROM 
						`mssr_user_forum`
                    WHERE 1=1
						AND `forum_id` = $forum_id
						AND `user_id` = $sess_uid
						
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($arrys_result)){
                    $has_record=true;
                }
				
			//-------------------------------------------
            //找出社團建立者
            //-------------------------------------------

                 $sql="
                    SELECT
                        `create_by`
                    FROM `mssr_forum`
                    WHERE 1=1
                        AND `forum_id`={$forum_id}
                ";
                $arrys_result_forum_create_uid=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                $create_uid		=$arrys_result_forum_create_uid[0]['create_by'];
				
				

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $sess_uid    =(int)$sess_uid;
            $forum_id    =(int)$forum_id;

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            if($has_record){
                $sql="
                    # for mssr_user_forum
                    UPDATE `mssr_user_forum` SET
                        `user_state`='申請中'
                    WHERE 1=1
                        AND user_id = $sess_uid
                    LIMIT 1;
				";
				//送出
				$err ='DB QUERY FAIL';
				$sth=$conn_mssr->prepare($sql);
				$sth->execute()or die($err);
			
            }else{
                $sql="
                    INSERT INTO `mssr_user_forum` SET
                        `forum_id`       	=   {$forum_id  }	,
                        `user_id`     		=   {$sess_uid   }	,
						`user_type`  		=   '一般'     		,
                        `user_state`  		=   '申請中'  		,
                        `keyin_cdate`   	=   NOW()   	    ,
						`keyin_mdate`   	=   NOW()   		;
                ";
				//送出
				$err ='DB QUERY FAIL';
				$sth=$conn_mssr->prepare($sql);
				$sth->execute()or die($err);
				
				//寫入mssr_user_request
				// $sql="
					// INSERT INTO `mssr_user_request` SET
						// `request_from`       	={$sess_uid}	,
						// `request_to`   		 	={$create_uid}	,
						// `request_state`			=1				,
						// `keyin_cdate`   		=NOW()       	;
				// ";
				
				//送出
				// $err ='DB QUERY FAIL';
				// $sth=$conn_mssr->prepare($sql);
				// $sth->execute()or die($err);
				// $request_id_lastinsertid = $conn_mssr->lastInsertId();
			
				
				//寫入mssr_user_request_forum_join_rev
				// $sql="
					// INSERT INTO `mssr_user_request_forum_join_rev` SET
						// `request_id`    =   {$request_id_lastinsertid},
						// `forum_id`    	=   {$forum_id}				  ;
				// ";
				//送出
			
				// $err ='DB QUERY FAIL';
				// $sth=$conn_mssr->prepare($sql);
				// $sth->execute()or die($err);
            }

          

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $msg="申請成功!!";
        $jscript_back="
            <script>
                alert('{$msg}');
                location.href='{$site}?forum_id={$forum_id}&psize={$psize}&pinx={$pinx}';
            </script>
        ";
        die($jscript_back);
?>