<?php
//-------------------------------------------------------
//mssr_forum
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------
        //SESSION
        @session_start();

        //外掛設定檔
        require_once(str_repeat("../",2).'config/config.php');
		require_once(str_repeat("../",2)."inc/search_book_info_online/code.php");

		//外掛頁面檔
        require_once(str_repeat("../",0).'inc/require_page/code.php');
        require_once('filter_func.php');


        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code'
       	);
        func_load($funcs,true);


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
	        //下載資料庫
	        //-----------------------------------------------

				//-----------------------------------------------
            	//檢核
            	//-----------------------------------------------
					
					$sess_uid = (int)$_SESSION['uid'];
					$article_id = (int)$_GET["article_id"];
					
				//-----------------------------------------------
	        	//SQL-留言資訊(mssr_reply_box_frame)
	        	//-----------------------------------------------
					$sql="
						SELECT
							`user_id`, `article_title`, `article_content`, `article_like_cno`, `keyin_cdate`, `article_state`, `cat_id`
						FROM
							`mssr_forum_article`
						WHERE 1=1
							AND `article_id` = $article_id
							AND `article_state` LIKE '%正常%'
					";
					$arrys_result_reply_box=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
					

				//-----------------------------------------------
	        	//SQL-mssr_reply_box_frame-名子
	        	//-----------------------------------------------
					
					$user_id = $arrys_result_reply_box[0]['user_id'];
					$sql="
						SELECT
							`name`
						FROM
							`member`
						WHERE
							`uid` = $user_id
					";
					
					$arrys_result_reply_name=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
					$numrow_reply_name=count($arrys_result_reply_name);

				//-----------------------------------------------
	        	//SQL-回復資訊(分頁)(mssr_comment_box_frame)
	        	//-----------------------------------------------
					$query_sql="
						SELECT
							`user_id`, `reply_content`, `reply_like_cno`, `keyin_cdate`, `reply_id`, `reply_state`
						FROM
							`mssr_forum_article_reply`
						WHERE 1=1
							AND `article_id` = $article_id
							AND `reply_state` LIKE '%正常%'
						ORDER BY
							`mssr_forum_article_reply`.`reply_id` ASC

					";
					$arrys_result_comment=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
					$arrys_result_comment_con=count($arrys_result_comment);


				//-----------------------------------------------
	        	//SQL-mssr_comment_box_frame-名子
	        	//-----------------------------------------------
					if($arrys_result_comment_con!==0){
						for($i=0;$i<$arrys_result_comment_con;$i++){
							$user_id = $arrys_result_comment[$i]['user_id'];
							$sql="
								SELECT
									`name`
								FROM
									`member`
								WHERE
									`id` = $user_id
							";
						}
						$arrys_result_comment_name=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
						$numrow_comment_name=count($arrys_result_comment_name);
					}

				//-----------------------------------------------
				//SQL-查book_sid
	        	//-----------------------------------------------
					$sql="
						SELECT
							`book_sid`
						FROM
							`mssr_article_book_rev`
						WHERE
							`article_id`=$article_id
					";
					$arrys_result_book_sid=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$book_sid		= $arrys_result_book_sid[0]['book_sid'];


				//-----------------------------------------------
	        	//SQL-判斷是否有看過這本書
	        	//-----------------------------------------------
				 $selectBook = $book_sid;


				 $sql  = "


                         SELECT  * from(
                         SELECT
                                TRIM(BOTH ' ' FROM  book_name) as name,
                                  book_sid,
                                  book_isbn_13,
                                  book_isbn_10,
                                keyin_cdate
                         FROM
                                mssr_book_library
                         WHERE  book_sid =  '$selectBook'

                         UNION

                         SELECT
                                TRIM(BOTH ' ' FROM  book_name) as name,
                                  book_sid,
                                  book_isbn_13,
                                  book_isbn_10,
                                keyin_cdate
                         FROM
                                mssr_book_class
                         WHERE  book_sid    =  '$selectBook'

                         UNION

                         SELECT
                                    TRIM(BOTH ' ' FROM  book_name) as name,
                                      book_sid,
                                      book_isbn_13,
                                      book_isbn_10,
                                keyin_cdate
                                FROM
                                    `mssr_book_unverified`
                         WHERE  book_sid    =  '$selectBook'

                          UNION

                         SELECT
                                    TRIM(BOTH ' ' FROM  book_name) as name,
                                      book_sid,
                                      book_isbn_13,
                                      book_isbn_10,
                                keyin_cdate
                                FROM
                                    `mssr_book_global`
                         WHERE  book_sid    =  '$selectBook'


                        ) as v
                        GROUP BY name
                            ORDER BY
                            `keyin_cdate` DESC

                ";
                $arrys_A=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


//                 echo '<pre>';                    
//                 print_r($arrys_A);
//                 echo '<pre>';    
// die();

            if(!$arrys_A[0]['book_isbn_13']==""){
                $book_isbn =$arrys_A[0]['book_isbn_13'];
            }else{
                $book_isbn =$arrys_A[0]['book_isbn_10'];
            }
         
           
            $sql  = "
                         SELECT * from(
                         SELECT
                                TRIM(BOTH ' ' FROM  book_name) as name,
                                book_sid,
                                keyin_cdate
                         FROM
                                mssr_book_library
                         WHERE  (
                                book_isbn_10    =  '$book_isbn'
                                OR book_isbn_13 =  '$book_isbn'
                                )



                         UNION

                         SELECT
                                TRIM(BOTH ' ' FROM  book_name) as name,
                                  book_sid,
                                keyin_cdate
                         FROM
                                mssr_book_class
                         WHERE  (
                                book_isbn_10    =  '$book_isbn'
                                OR book_isbn_13 =  '$book_isbn'
                                )
                         UNION

                         SELECT
                                TRIM(BOTH ' ' FROM  book_name) as name,
                                  book_sid,
                                keyin_cdate

                         FROM
                                mssr_book_unverified
                         WHERE  (
                                book_isbn_10    =  '$book_isbn'
                                OR book_isbn_13 =  '$book_isbn'
                                )
                         UNION

                         SELECT
                                TRIM(BOTH ' ' FROM  book_name) as name,
                                book_sid,
                                keyin_cdate
                         FROM
                                mssr_book_global
                         WHERE  (
                                book_isbn_10    =  '$book_isbn'
                                OR book_isbn_13 =  '$book_isbn'
                                )

                        ) as v
                            ORDER BY
                            `keyin_cdate` DESC

                ";

                    $arrys_if_B=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);





                    $arr_b = array();
                    foreach($arrys_if_B as $k=>$v){
                       $arr_b[] =  "'".$v['book_sid']."'" ;
                    }

                    $str = implode(",", $arr_b);




				//-----------------------------------------------
	        	//SQL-這本書有多少發文
	        	//-----------------------------------------------
					$sql="
						SELECT
							`article_id`
						FROM
							`mssr_article_book_rev`
						WHERE
							`book_sid` in($str)
					";


					$arrys_result_book_article=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_book_article=count($arrys_result_book_article);

			
				//-----------------------------------------------
	        	//SQL-這本書有多少回覆
	        	//-----------------------------------------------
					$sql="
						SELECT
							`article_id`
						FROM
							`mssr_article_reply_book_rev`
						WHERE
							`book_sid` in($str) 
					";
					$arrys_result_book_reply=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_book_reply=count($arrys_result_book_reply);
				//-----------------------------------------------
	        	//SQL-誰也看過這本書
	        	//-----------------------------------------------
					$book_sid = $arrys_result_book_sid[0]['book_sid'];
					$sql="
						SELECT
							`book_sid`, `user_id`
						FROM
							`mssr_book_borrow_semester`
						WHERE
							`book_sid` in($str)
						ORDER BY
							`borrow_sdate` DESC

					";
					$arrys_result_aside=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);
					$numrow_aside=count($arrys_result_aside);
					
				//-----------------------------------------------
	        	//SQL-幾位朋友看過這本書
	        	//-----------------------------------------------
					//先找出朋友名單
					$sql="
						SELECT
							`user_id`,`friend_id`
						FROM
							`mssr_forum_friend`
						WHERE 1=1
							AND (
								`user_id` = {$_SESSION['uid']}
									OR
								`friend_id` = {$_SESSION['uid']}
							)
							AND `friend_state` = '成功'
					";
					$arrys_result_friend=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_friend=count($arrys_result_friend);

					//mssr_forum_friend：user|friend欄位判斷
					$rs_arrys_result_friend=array();
					for($i=0; $i<$numrow_friend;$i++){
						if($arrys_result_friend[$i]['user_id']<>$_SESSION['uid']){
							$rs_arrys_result_friend[$i]['user_id']   = $arrys_result_friend[$i]['friend_id'];
							$rs_arrys_result_friend[$i]['friend_id'] = $arrys_result_friend[$i]['user_id'];
						}else{
							$rs_arrys_result_friend[$i]['user_id']   = $arrys_result_friend[$i]['user_id'];
							$rs_arrys_result_friend[$i]['friend_id'] = $arrys_result_friend[$i]['friend_id'];
						}
					}

					//判斷朋友是否看過這本書
					$rs_arrys_result_friend_also_look = array();
					for($i=0; $i<count($rs_arrys_result_friend); $i++){
						$sql="
							SELECT
								`book_sid`, `user_id`
							FROM
								`mssr_book_borrow_semester`
							WHERE 1=1
								AND `book_sid` in($str)
								AND `user_id`  = '{$rs_arrys_result_friend[$i]['friend_id']}'


						";
						$arrys_result_friend_also_look=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
						$rs_arrys_result_friend_also_look += $arrys_result_friend_also_look;
					}

					//剔除重複資料
					$rs_arrys_result_friend_also_look = array_unique($rs_arrys_result_friend_also_look);

				//-----------------------------------------------
	        	//SQL-看過這本書的人，他們也看了?
	        	//-----------------------------------------------
					//找最近看過這本書的3個人
					$rs_uid = $_SESSION['uid'];
					$sql="
						SELECT
							`user_id`
						FROM
							`mssr_book_borrow_semester`
						WHERE 1=1
							AND `book_sid` = '$book_sid'
						ORDER BY
							`borrow_sdate` DESC

					";
					$arrys_result_who_read=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,3),$arry_conn_mssr);
					$numrow_book_who_read=count($arrys_result_who_read);

					$arry_book_sid_list=array();
					for($i=0;$i<$numrow_book_who_read;$i++){
						$user_id 			= (int)$arrys_result_who_read[$i]['user_id'];
						//看過這本書的人，他們最近也看過甚麼書，挑重複度最高的3本
						$sql="
							SELECT
								`book_sid`
							FROM
								`mssr_book_borrow_semester`
							WHERE 1=1
								AND `user_id` = $user_id
								AND `book_sid` <> '$book_sid'

						";

						$arrys_result_recommend=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

						if(!empty($arrys_result_recommend)){
							$arry_list=array();
							foreach($arrys_result_recommend as  $arry_result_recommend){

								$rs_book_sid=trim($arry_result_recommend['book_sid']);

								if(!array_key_exists($rs_book_sid,$arry_book_sid_list)){
									$arry_book_sid_list[$rs_book_sid]=1;
								}else{
									$arry_book_sid_list[$rs_book_sid]=$arry_book_sid_list[$rs_book_sid]+1;
								}
							}
							//排序
							arsort($arry_book_sid_list);

							//篩選
							foreach($arry_book_sid_list as $book_sid_tmp=>$cno){
								if(count($arry_list)<3){
									$arry_list[]=trim($book_sid_tmp);
								}else{
									break;
								}
							}
						}
						
					}


				//-----------------------------------------------
	        	//SQL-哪個聊書小組也在討論這本書
	        	//-----------------------------------------------
					$sql="
						SELECT
							`forum_id`
						FROM
							`mssr_forum_booklist`
						WHERE
							`book_sid` = '{$book_sid}'
					";
					$arrys_result_which_group=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,3),$arry_conn_mssr);
					$numrow_which_group=count($arrys_result_which_group);
					
				//-----------------------------------------------
	        	//SQL-追蹤書籍
	        	//-----------------------------------------------
					$sql="
						SELECT
							`user_id`
						FROM
							`mssr_book_favorite`
						WHERE 1=1
                            AND `user_id`  =  {$_SESSION['uid']}
							AND `book_sid` = '{$book_sid       }'
					";
					$arrys_result_book_check=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				//-----------------------------------------------
	        	//SQL-判斷是否有看過這本書
	        	//-----------------------------------------------
					$sql="
						SELECT
							`borrow_sdate`
						FROM
							`mssr_book_borrow_log`
						WHERE 1=1
							AND `user_id` = $sess_uid
							AND `book_sid` = '{$book_sid}'
					";
		
					$arrys_if_borrow=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
	//---------------------------------------------------
    //檢驗
    //---------------------------------------------------
		//echo "<pre>";
		//print_r($sql);
		//echo "</pre>";
		//die();
	//---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=$arrys_result_comment_con;    //資料總筆數
        $psize =8;                 //單頁筆數,預設10筆
        $pnos  =0;                  //分頁筆數
        $pinx  =1;                  //目前分頁索引,預設1
        $sinx  =0;                  //值域起始值
        $einx  =0;                  //值域終止值

        if(isset($_GET['psize'])){
            $psize=(int)$_GET['psize'];
            if($psize===0){
                $psize=10;
            }
        }
        if(isset($_GET['pinx'])){
            $pinx=(int)$_GET['pinx'];
            if($pinx===0){
                $pinx=1;
            }
        }

        $pnos  =ceil($numrow/$psize);
        $pinx  =($pinx>$pnos)?$pnos:$pinx;

        $sinx  =(($pinx-1)*$psize)+1;
        $einx  =(($pinx)*$psize);
        $einx  =($einx>$numrow)?$numrow:$einx;

	//---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球-書籍回覆文章";
		$cat_id =$arrys_result_reply_box[0]['cat_id'];
		
		 if($numrow!==0){
            $arrys_chunk =array_chunk($arrys_result_comment,$psize);
            $arrys_result=$arrys_chunk[$pinx-1];
        }else{
			
        }
		
		//是否有借閱過書籍
		$has_borrow_book = FALSE;
		if(!empty($arrys_if_borrow)){
				$has_borrow_book = TRUE;
				
		}


?>

<!DOCTYPE HTML>
<html>
	<head>
	  <meta charset="utf-8">
	<title><?php echo $title?></title>
	</head>


	<!-- 通用js  -->
    <script type="text/javascript" src="../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../inc/code.js"></script>
    <script type="text/javascript" src="../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../lib/js/table/code.js"></script>

    <!-- 專屬js  -->
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/jquery.blockUI.js"></script>
	<script type="text/javascript" src="js/mssr_forum/report_code.js"></script>
	<script type="text/javascript" src="js/mssr_forum/like_code.js"></script>
	<script type="text/javascript" src="js/mssr_forum/track_code.js"></script>
	<script type="text/javascript" src="inc/add_action_forum_log/code.js"></script>
	<script type="text/javascript" src="js/mssr_forum/scaffolding_reply_code.js"></script>

    <link type="text/css" rel="stylesheet" href="css/bootstrap.css">
    <link type="text/css" rel="stylesheet" href="css/mssr_forum.css">
    <link type="text/css" rel="stylesheet" href="../../inc/code.css">
  
  <script>
    //action log

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


    </script>
  
  
  <script>
 /* 		哪一本書發文 */
		var book_sid	='<?php echo addslashes($book_sid);?>';
		
		// /* 發文者uid */
		var user_id_1 	=<?php echo $arrys_result_reply_box[0]['user_id'];?>;
		var article_id	=<?php echo $article_id;?>;
		var sess_uid	=<?php echo $sess_uid;?>;
		
	
		
		var psize		=<?php echo (int)$psize;?>;
		var pinx 		=<?php echo (int)$pinx;?>;
		var check 		=<?php echo count($arrys_result_book_check);?>;
		

		window.onload=function(){

			//分頁列
			var cid         ="page";                        //容器id
			var numrow      =<?php echo (int)$numrow;?>;    //資料總筆數
			var psize       =<?php echo (int)$psize ;?>;    //單頁筆數,預設10筆
			var pnos        =<?php echo (int)$pnos  ;?>;    //分頁筆數
			var pinx        =<?php echo (int)$pinx  ;?>;    //目前分頁索引,預設1
			var sinx        =<?php echo (int)$sinx  ;?>;    //值域起始值
			var einx        =<?php echo (int)$einx  ;?>;    //值域終止值
			var list_size   =5;                             //分頁列顯示筆數,5
			var url_args    ={};                            //連結資訊
			url_args={
				'pinx_name' :'pinx',
				'psize_name':'psize',
				'page_name' :'mssr_forum_book_reply.php',
				'page_args' :{
					'article_id' :<?php echo (int)$article_id;?>
				}
			}
			var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
		}
		
	  
		
		

			
		
			 function go_back(){
				window.history.back();

			}
			
			
			//隱藏回覆按鈕
			function reply_disable_close(obj){
							
				
				var reply_content	  			=document.getElementsByName('mssr_comment_input_name_content[]');
				var mssr_forum_reply			=document.getElementById('mssr_forum_reply');
				
				
				for(var i = 0;i<reply_content.length;i++){
				
					if(trim(reply_content[i].value) ==""){
						alert("請確認內容都有輸入");
						return false;
						
					}
				}
				//obj.disabled=true;
				//action_log('inc/add_action_forum_log/code.php','b1',sess_uid,0,0,book_sid,0,0,0,0,0,'');
				action_log('inc/add_action_forum_log/code.php','ba9',sess_uid,user_id_1,0,book_sid,0,0,0,article_id,0,'');
				
				mssr_forum_reply.submit();
			}
				
		
		$(document).ready(function() {

			//分頁列
			
			//block ui

				
			$('#open_invite_box').click(function() {
					
				$.blockUI({
					message: $('#invite_box'),
						
					css:{
						top:  ($(window).height() - 400) /2 + 'px',
						left: ($(window).width() - 700) /2 + 'px',
						textAlign:	'left',
						width: '700px'
					
					}
				});
			});
				
			$('#invite_box_leave').click(function() {
				$.unblockUI();
				return false;
			});

			$('#mssr_input_box_submit').click(function() {
					
				$.unblockUI();
			});
		});
		
	</script>
	
	<script>
    //action log

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


    </script>

	  
	<body>

		<!-- navbar start -->
		<?php r_p_navbar((int)$_SESSION["uid"],$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);?>
		<!-- navbar end -->



	<!--========================root=====================================-->
	  <div class="root" >
	  
		  <ul class="breadcrumb">
		目前位置：
			  <li>
				<a href="index.php">首頁</a> 
			  </li>
			  <li>
				<a href="#">書籍</a> 
			  </li>
			  
			</ul>
	  </div>

	<!--========================group header=====================================-->
	  <div class="book_header">
			<div class="group_image" >
			 <?php
			//書籍封面處理
			$bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
			if(file_exists($bookpic_root)){
				$rs_bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
			}else{
				$rs_bookpic_root = 'image/book.jpg';
			}?>
			  <img  src="<?php echo $rs_bookpic_root?>" class="img-thumbnail" width="100" height="100">
			</div>
		  
			<div class="group_info1">
			  <?php
				
					
					$arrys_book_info	= get_book_info($conn_mssr,$book_sid,$array_filter=array(),$arry_conn_mssr);
					$book_name 			= mysql_prep(trim($arrys_book_info[0]['book_name']));
					$book_author 		= mysql_prep(trim($arrys_book_info[0]['book_author']));
					$book_publisher 	= mysql_prep(trim($arrys_book_info[0]['book_publisher']));
					$book_isbn_13 		= mysql_prep(trim($arrys_book_info[0]['book_isbn_13']));?>

			  
				
				<p>
				  <?php echo $book_name?><P>
				  作者：<?php echo $book_author?><P>
				  出版社：<?php echo $book_publisher?><P><P><P>
				  
				  
				
				   <?php if(count($arrys_result_book_check)===0){?>
					  <a class="btn" type="button"  
						  onclick=
						  "book_track('ba2',<?php echo $_SESSION["uid"];?>,'<?php echo $book_sid;?>',<?php echo count($arrys_result_book_check);?>)">
						  追蹤書籍
					  </a>
				  
				  <?php 
				  }else{
				  ?>
						  <a class="btn" type="button"  
								 onclick=
								 "book_track('ba3',<?php echo $_SESSION["uid"];?>,'<?php echo $book_sid;?>',<?php echo count($arrys_result_book_check);?>)">
							取消追蹤
						  </a>
				<?php }?>
			  
			  
			   
		
			</div>

			<div class="group_info2">
			 <?php
					 //書籍簡介
						$array_select = array ("book_name");
						get_book_info($conn_mssr,$book_sid,$array_filter=array(),$arry_conn_mssr);
						$book_note = get_book_info($conn_mssr,$book_sid,$array_select,$arry_conn_mssr);

						$book_note 		= mysql_prep(trim($arrys_book_info[0]['book_note']));
						
						//判斷資料庫是否沒有書籍簡介資料
						if(empty($book_note)){

							$book_isbn_13	= mysql_prep(trim($arrys_book_info[0]['book_isbn_13']));
							$book_isbn_10	= mysql_prep(trim($arrys_book_info[0]['book_isbn_10']));
							$book_type = substr($book_sid, 0, 3);
							
								if($book_type === "mbg"){
									$book_type_db	= "mssr_book_global";
								}else if($book_type === "mbl"){
									$book_type_db	= "mssr_book_library";
								}else if($book_type === "mbc"){
									$book_type_db	= "mssr_book_class";
								}else if($book_type === "mbu"){
									$book_type_db	= "mssr_book_unverified";
								}else{
									
								}
							
								
							//線上抓取書籍簡介資料(以$book_isbn_13為優先)
							if($book_isbn_13===""){
								$book_code 		= $book_isbn_13;
							}else if($book_isbn_10===""){
								$book_code		= $book_isbn_10;
							}else{
								$book_code= "";
							}
							
							
							$search_book_info_online = search_book_info_online($book_code);
							if(isset($search_book_info_online['book_note'][0])){
							$book_note		= mysql_prep(trim($search_book_info_online['book_note'][0]));
							}else{
							$book_note="";
							}
							
							
							//簡介資料寫進book_note 
							if($book_note===""){
								$book_note = '無';
								$sql="
									UPDATE 
										`{$book_type_db         }` 
									SET
										`book_note` = '{$book_note          		}'
									WHERE 1=1
										AND `book_sid`         	=  '{$book_sid          		}';
								";
			
							}
							else{
								$sql="
									UPDATE 
										`{$book_type_db         }` 
									SET
										`book_note` = '{$book_note          		}'
									WHERE 1=1
										AND `book_sid`         	=  '{$book_sid          		}';
								";
							
							}
							
							//送出
							$err ='DB QUERY FAIL';
							$sth=$conn_mssr->prepare($sql);
							$sth->execute()or die($err);
							
							$book_note=mb_substr($book_note,0,150)."..";
					?>
						內容簡介：<BR><?php echo $book_note?>
					<?php
						
						}else{
							$book_note=mb_substr($book_note,0,150)."..";
					?>
						內容簡介：<BR><?php echo $book_note?>
						
					<?php }?>
			  
			  
			</div>
		
	 </div>
	 


	 
	 
	 
	<!--========================content=====================================--> 
	<div class="content">

		<div class="left_content">
		
			<div class="btn-group" >
				<button class="btn btn-default" type="button" onclick="go_back();">回上一頁</button>
			</div>
			<?php if($has_borrow_book){ ?>
				<div style="float:right">
					<input class="btn btn-primary btn-sm" id="open_invite_box" type ="button" value="邀請好友一同討論"></input>
				</div>
			<?php }?>
			
		  
			<P>	  
			 
			 <!----------發文列表---------->
			<div id="mssr_reply_box" >

			<!----------留言資訊---------->
			<div id="mssr_reply_box_frame">
				<?php
					if(!empty($arrys_result_reply_box)){
					
						$name 						= $arrys_result_reply_name[0]['name'];
						$user_id 					= $arrys_result_reply_box[0]['user_id'];
						$article_title 				= $arrys_result_reply_box[0]['article_title'];
						$article_content 			= $arrys_result_reply_box[0]['article_content'];
						$article_like_cno 			= $arrys_result_reply_box[0]['article_like_cno'];
						$keyin_cdate 				= $arrys_result_reply_box[0]['keyin_cdate'];
						//-----------------------------------------------
						//SQL-讚
						//-----------------------------------------------
						$sql="
							SELECT
								`user_id`
							FROM
								`mssr_forum_article_like_log`
							WHERE
								`article_id` = '$article_id'
						";

						$arrys_result_like=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
						$numrow_like=count($arrys_result_like);
						
						//-----------------------------------------------
						//SQL-檢舉
						//-----------------------------------------------
						$sql="
							SELECT
								`article_id`
							FROM
								`mssr_forum_article_report_log`
							WHERE
								`article_id` = $article_id;
						";
						$arrys_result_report=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
						$arrys_result_report_con=count($arrys_result_report);
						
						if($arrys_result_report_con>=5){
							$article_content		='<B><I><h4><font color=red>此篇發文檢舉次數過多</font></h4></I></B>';
						
						}

						//學生照片
						$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
					
						if($get_user_info[0]['sex']==1){
				?>
							<a onclick="action_log('inc/add_action_forum_log/code.php','ba10',<?php echo $sess_uid;?>,<?php echo $user_id;?>,0,'<?php echo $book_sid;?>',0,0,0,<?php echo $article_id;?>,0,'mssr_forum_people_index.php?user_id=<?php echo $user_id?>');"  href="javascript:void(0);">
								<img id="mssr_reply_box_pic" src="image/boy.jpg" alt="" width="100" height="100"/>
							</a>

				<?php }else{?>
				
							<a onclick="action_log('inc/add_action_forum_log/code.php','ba10',<?php echo $sess_uid;?>,<?php echo $user_id;?>,0,'<?php echo $book_sid;?>',0,0,0,<?php echo $article_id;?>,0,'mssr_forum_people_index.php?user_id=<?php echo $user_id?>');"  href="javascript:void(0);">
								<img id="mssr_reply_box_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
							</a>
							
				<?php }?>
							<figcaption id="mssr_reply_box_name"><?php echo $name?></figcaption>
							<div id="mssr_reply_box_title"><?php echo filter($article_title)?></div>
							<div id="mssr_reply_box_content"><?php echo filter($article_content)?></div>
							<div id="mssr_reply_box_time"><?php echo $keyin_cdate?>(#<?php echo $_GET["article_id"];?>)</div>
							
							<!--發文按讚pic-->			
							<img id="mssr_reply_box_likepic" src="image/like.png" alt="" width="10" height="10"
							onmouseover="mouse_over(this);void(0);"
							<?php
								echo ' class="action_code_ba7"';
								echo " user_id_1='{$user_id}'";
								echo " book_sid_1='{$book_sid}'";
								echo " article_id='{$article_id}'";
								echo " con_like='{$numrow_like}'";
							?>>
							
							
							
							
							
							
							<!--發文按讚cnt-->
							<div id="mssr_reply_box_likecnt">
								<p id="mssr_reply_box_likecnt-<?php echo $_GET["article_id"];?>"><?php echo $numrow_like?></p>
							</div>
							
							<!--發文檢舉pic-->
							<img id="mssr_reply_box_reportpic" src="image/report.png"  
								onmouseover="mouse_over(this);void(0);" 
								onclick="report('article',<?php echo $_SESSION['uid'];?>,<?php echo $user_id;?>,<?php echo $article_id;?>,<?php echo $arrys_result_report_con ?>);void(0);" 
								alt="" width="10" height="10"/>
							
							<!--發文檢舉cnt-->
							<div id="mssr_reply_box_reportcnt">
								<p id="mssr_reply_box_reportcnt-<?php echo $article_id?>"><?php echo $arrys_result_report_con	?></p>
							</div>
							
							<!--回覆數量-->
							<img id="mssr_reply_box_replypic" src="image/icon.png" alt="" width="10" height="10"/>
							<p id="mssr_reply_box_replycnt"><?php echo $numrow?></p>
					
				<?php }else{ 
							echo '<font color=red>找不到文章，請回上一頁重新點選文章標題</font>';
							die();
					 }
				?>
				
			

					
				
			</div>

			<!----------回覆留言---------->
			<?php
			
			if(empty($arrys_result_comment)){
				echo("這個文章還沒有人回覆，趕快來留言!");
			}else{	
				foreach($arrys_result as $inx=>$arrys_result_comment):
			
				$user_id 			= $arrys_result_comment['user_id'];
				$reply_id 			= $arrys_result_comment['reply_id'];
				$reply_content 		= $arrys_result_comment['reply_content'];
				$keyin_cdate 		= $arrys_result_comment['keyin_cdate'];
				$reply_like_cno 	= $arrys_result_comment['reply_like_cno'];
				$sql="
					SELECT
						`name`
					FROM
						`member`
					WHERE
						`uid` = $user_id
				";
				$arrys_result_username=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);


				//-----------------------------------------------
				//SQL-讚
				//-----------------------------------------------
				$sql="
					SELECT
						`user_id`
					FROM
						`mssr_forum_article_reply_like_log`
					WHERE
						`reply_id` = '$reply_id'
				";
				$arrys_result_like_reply=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$numrow_like_reply=count($arrys_result_like_reply);
				
				//-----------------------------------------------
				//SQL-檢舉
				//-----------------------------------------------
				$sql="
					SELECT
						`report_from`
					FROM
						`mssr_forum_article_reply_report_log`
					WHERE
						`reply_id` = '$reply_id'
				";
				$arrys_result_report_reply=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$numrow_like_report=count($arrys_result_report_reply);
				
				
				if($numrow_like_report>=5){
					$reply_content = '<font color=red>這篇留言已經被檢舉！</font>';
					
				}
				
				?>
				<div id="mssr_comment_box_frame">

					<?php
					//學生照片
					$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
					if($get_user_info[0]['sex']==1){
					?>
						<a onclick="action_log('inc/add_action_forum_log/code.php','ba11',<?php echo $sess_uid;?>,<?php echo $arrys_result_reply_box[0]['user_id'];?>,<?php echo $user_id?>,'<?php echo $book_sid;?>',0,0,0,<?php echo $article_id;?>,<?php echo $reply_id;?>,'mssr_forum_people_index.php?user_id=<?php echo $user_id?>');"  href="javascript:void(0);">
							<img id="mssr_comment_box_pic" src="image/boy.jpg" alt="" width="100" height="100"/>
						</a>
					<?php }else{?>
						<a onclick="action_log('inc/add_action_forum_log/code.php','ba11',<?php echo $sess_uid;?>,<?php echo $arrys_result_reply_box[0]['user_id'];?>,<?php echo $user_id?>,'<?php echo $book_sid;?>',0,0,0,<?php echo $article_id;?>,<?php echo $reply_id;?>,'mssr_forum_people_index.php?user_id=<?php echo $user_id?>');"  href="javascript:void(0);">
							<img id="mssr_comment_box_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
						</a>
					<?php }?>


					<figcaption id="mssr_comment_box_name"><?php echo $arrys_result_username[0]['name'];?></figcaption>

					<?php 
					if($numrow_like_report>=5){
						$reply_content = "這篇留言已經被檢舉！";?>
						 <p id="mssr_comment_box_content" style="text-align:center; color:#FF0000"; ><?php echo $reply_content?></p>
						 
						 <p id="mssr_comment_box_time"><?php echo $keyin_cdate?>(#<?php echo $reply_id; ?>)</p>


						<img id="mssr_comment_box_likepic" src="image/like.png" alt="" width="10" height="10"
						onmouseover="mouse_over(this);void(0);"
							<?php
								$user_id_1		=(int)$arrys_result_reply_box[0]['user_id'];
								echo ' class="action_code_ba8"';
								echo " user_id_1='{$user_id_1}'";
								echo " user_id_2='{$user_id}'";
								echo " book_sid_1='{$book_sid}'";
								echo " article_id='{$article_id}'";
								echo " reply_id='{$reply_id}'";
								echo " con_like='{$numrow_like}'";
							?>>
						
						<!--onclick="like('reply',<?php echo (int)$_SESSION["uid"];?>,<?php echo $reply_id;?>,<?php echo $numrow_like_reply;?>,'ba8',<?php echo $arrys_result_reply_box[0]['user_id'];?>,'<?php echo $book_sid;?>',<?php echo $user_id;?>,<?php echo $article_id;?>);void(0);"/>-->
						
						
						
						
		
					
						
						<img id="mssr_comment_box_reportpic" src="image/report.png" alt="" width="10" height="10"
							onmouseover="mouse_over(this);void(0);" 
							onclick="report('reply',<?php echo $_SESSION['uid'];?>,<?php echo $user_id;?>,<?php echo $reply_id;?>);void(0);" alt="" width="10" height="10"/>
						
						<div id="mssr_comment_box_reportcnt">
							<p id="mssr_comment_box_reportcnt-<?php echo $reply_id?>"><?php echo $numrow_like_report; ?></p>
						</div>
						
					<?php }?>
					
					
						<p id="mssr_comment_box_content"><?php echo filter($reply_content)?></p>
						<p id="mssr_comment_box_time"><?php echo $keyin_cdate?>(#<?php echo $reply_id; ?>)</p>


						<img id="mssr_comment_box_likepic" src="image/like.png" alt="" width="10" height="10"
							onmouseover="mouse_over(this);void(0);"
							<?php
								$user_id_1		=(int)$arrys_result_reply_box[0]['user_id'];
								echo ' class="action_code_ba8"';
								echo " user_id_1='{$user_id_1}'";
								echo " user_id_2='{$user_id}'";
								echo " book_sid_1='{$book_sid}'";
								echo " article_id='{$article_id}'";
								echo " reply_id='{$reply_id}'";
								echo " con_like='{$numrow_like}'";
							?>>

						<div id="mssr_comment_box_likecnt">
							<p id="mssr_comment_box_likecnt-<?php echo $reply_id;?>"><?php echo $numrow_like_reply?></p>
						</div>
						
						<img id="mssr_comment_box_reportpic" src="image/report.png"  
							onmouseover="mouse_over(this);void( 0);" 
							onclick="report('reply',<?php echo $_SESSION['uid'];?>,<?php echo $user_id;?>,<?php echo $reply_id;?>,<?php echo $numrow_like_report;?>);void(0);" 
							alt="" width="10" height="10"/>
						
						<div id="mssr_comment_box_reportcnt">
							<p id="mssr_comment_box_reportcnt-<?php echo $reply_id;?>"><?php echo $numrow_like_report; ?></p>
						</div>
					
					
				</div>
			<?php 
				endforeach ;
			}
			?>

			<?php
				if($has_borrow_book){
			?>
					<!----------回覆的發文---------->
					<div id="mssr_comment_input_frame">
						<form id="mssr_forum_reply" action="mssr_forum_book_reply_A.php" method="post">
							
								<?php
								//學生照片
								$get_user_info=get_user_info($conn_user,$_SESSION["uid"],$array_filter=array('sex','name'),$arry_conn_user);
								if($get_user_info[0]['sex']==1){?>
									<img id="mssr_comment_box_pic" src="image/boy.jpg" alt="" width="100" height="100"/>
								<?php }else{?>
									<img id="mssr_comment_box_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
								<?php }?>
								
								
								<figcaption id="mssr_comment_input_name"><?php echo $get_user_info[0]['name'];?></figcaption>
								
								<div id="scaffolding_reply_text">
									<input id="r1"  type="radio" name="article_refer_code" value="11a" 
										onclick="article_input_text('r1')" />
										<span  id="p1" style="width:400px; height:25px;" >XXX</span ><BR>
								
									<input id="r2"  type="radio" name="article_refer_code" value="11b" 
										onclick="article_input_text('r2')" />
										<span  id="p2" style="width:400px; height:25px;" >XXX</span ><BR>
								
									<input id="r0" type="radio" name="article_refer_code" value="0" 
										onclick="article_input_text('r0')"/>
											<span id="p0" style="width:400px; height:25px;">其他</span>
								</div>
								
								<div id="mssr_comment_input_content">
									<font color=red style="font-size:28px;">請選擇上放要輸入的內容</font>
								</div>
					
								<textarea name="cat_id"  style="display:none" cols="2" rows="8"><?php echo (int)$cat_id;?></textarea>
								<textarea 	name="article_id"  	style="display:none" cols="2" rows="8"><?php echo (int)$_GET["article_id"]?></textarea>

								<input 	class="btn btn-default btn-xs" id="mssr_comment_input_submit" 
										onclick="reply_disable_close(this);" type="button" value="送出"/>
							
						</form>
					</div>
			<?php	}else{		?>
						<FONT Color="red" align="center">
							<H3>你還沒看過這本書，不能發表文章，趕快去看吧!
								<input class="btn btn-danger" onclick="action_log('inc/add_action_forum_log/code.php','ba17',<?php echo $_SESSION["uid"];?>,<?php echo $arrys_result_reply_box[0]['user_id'];?>,0,'<?php echo $book_sid;?>','',0,0,0,0,'../mssr_menu.php');" type ="button" value="進行閱讀登記">
								</input>
							</H3>
						</FONT>	
		
			<?php	}		?>
			
				
			<!--========================分頁=====================================--> 	
			<div style="top:50px;position:relative">
				<table style="float:right;">
					<tr valign="middle">
						<td align="left">
							<!-- 分頁列 -->
							<span id="page" style="position:relative;"></span>
						</td>
					</tr>
				</table>
				
			</div>
			
		</div>
		 
		
	 </div>
	 
<script  type="text/javascript">

	//FUNCTION
	$('.action_code_ba7').click(function(){
	
	//var con_like = $(this).attr('con_like');
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='ba7',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =$(this).attr('user_id_1'),
			user_id_2   =0,
			book_sid_1  =$(this).attr('book_sid_1'),
			book_sid_2  ='',
			forum_id_1  =0,	
			forum_id_2  =0,
			article_id  =$(this).attr('article_id'),
			reply_id    =0,
			go_url      =''
			
		);
		like('article',	<?php echo (int)$_SESSION["uid"];?>, $(this).attr('article_id'), $(this).attr('con_like'));
	});
	
	
	
	$('.action_code_ba8').click(function(){
	
	//var con_like = $(this).attr('con_like');
		//呼叫
		
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='ba8',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =$(this).attr('user_id_1'),
			user_id_2   =$(this).attr('user_id_2'),
			book_sid_1  =$(this).attr('book_sid_1'),
			book_sid_2  ='',
			forum_id_1  =0,	
			forum_id_2  =0,
			article_id  =$(this).attr('article_id'),
			reply_id    =$(this).attr('reply_id'),
			go_url      =''
			
		);
		like('reply',	<?php echo (int)$_SESSION["uid"];?>, $(this).attr('reply_id'), $(this).attr('con_like'));
	});
	
	var cat_id =<?php echo (int)$cat_id;?>;
		
	scaffolding_reply_text(cat_id);
	
	
	
</script>
		


	<!----------block ui div---------->

	<div id="invite_box"  style="display:none; cursor: default">
		<form action="mssr_forum_request_discussion_A.php" method="post">
			<input type="image" id="invite_box_leave" src="image/xlogo.png" alt="" width="30" height="30" onclick="leaveui();"/>
			  <blockquote>
			  <p><B>邀請好友一同討論</B></p>
			  
			  </blockquote>
			  
			  <input id="" type="hidden" value="<?php echo $article_id ?>" name="article_id">
			  <input id="" type="hidden" value="mssr_forum_book_reply.php" name="site">
			  <input id="" type="hidden" value="2" name="type">
			  <input id="" type="hidden" value="<?php echo $pinx?>" name="pinx">
			  <input id="" type="hidden" value="<?php echo $psize?>" name="psize">
		 
		<div style="overflow:auto;  height:300px; position:relative; width:100%;">
		<?php
			if(empty($arrys_result_friend)){
				echo("現在還沒有朋友喔，趕快去找朋友吧！");
			}else{
			
			foreach($arrys_result_friend as $inx=>$arrys_result_friend):
						

					$user_id 			= (int)$arrys_result_friend['user_id'];
					$friend_id 			= (int)$arrys_result_friend['friend_id'];
					
					if($user_id==$sess_uid){
						$rs_user_id     	= $user_id;
						$rs_friend_id		= $friend_id;

					}else{
						$rs_user_id     	= $friend_id;
						$rs_friend_id		= $user_id;
					}
					$get_user_info=get_user_info($conn_user,$rs_friend_id,$array_filter=array('name','sex'),$arry_conn_user);


					$friend_name 			= trim($get_user_info[0]['name']);
					$friend_sex 			= trim($get_user_info[0]['sex']);
					
					//-----------------------------------------------
					//SQL-是否已邀請一同討論
					//-----------------------------------------------
					$has_request_discussion	= FALSE;
					$has_disabled = '';
					
					$sql="
						SELECT
						  `mssr_user_request`.`request_id`
						FROM
							`mssr_user_request`
						INNER JOIN
							`mssr_user_request_discussion_rev`	ON
							`mssr_user_request`.`request_id`	=	`mssr_user_request_discussion_rev`.`request_id`
						WHERE 1=1
						  AND	`mssr_user_request_discussion_rev`.`article_id` =$article_id
						  AND	`mssr_user_request`.`request_to`				=$rs_friend_id
					
					";
					$arrys_result_request_discussion=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
					
					if(!empty($arrys_result_request_discussion)){
						$has_request_discussion = TRUE;
						$has_disabled			= 'disabled';
					}
					
					
		?>



					<figure class="figure_people">
					<?php
						//處理
						if($friend_sex==1){?>
							
								<img src="image/boy.jpg" alt="<?php echo $friend_name?>" width="60px" height="60px" />
							
						<?php }else{?>
							
								<img src="image/girl.jpg" alt="<?php echo $friend_name?>" width="60px" height="60px" />
							
						<?php }?>
						<figcaption class="figcatpion_people">
							<input type="checkbox" name="friend_uid[]" value="<?php echo $rs_friend_id?>" <?php echo $has_disabled;?>> 
								
								<?php 	
									echo $friend_name;
									if($has_request_discussion){
										echo '<BR><FONT COLOR=BLUE>(邀請中)</FONT>';
									}
								?>
						</figcaption>
					</figure>
				<?php 
					endforeach ;
					}
				?>
			</div>
			
			<input  class="btn btn-default" id="invite_box_submit" type="submit" value="送出"/>
			
			
		</form>
	</div>

	<!----------aside---------->

		<div class="aside">
			<p>
				<div class="panel panel-primary">
						<div class="panel-heading" style="font-size:16px;" align="center" ><B>書籍討論資訊</B></div>
							<div class="panel-body" style="font-size:14px;" >
									<?php echo $numrow_book_article?>篇發文<br>
									<?php echo $numrow_book_reply?>篇回復<br>
									<?php echo $numrow_aside?>位看過這本書<br>
									<?php echo count($rs_arrys_result_friend_also_look)?>位好友看過這本書<br>
							</div>
				</div>
				
				<?php	
					   $oright_side=new right_side($sess_uid,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
					   $arry_book=$oright_side->book($sess_uid,$book_sid,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);


				?>
				
				<table class="table">
					<thead>
						<tr align="center">
							<TD COLSPAN=2>誰也看過這本書？</TD>
						</tr>
					</thead>
					<tbody>
					<?php		  
						 if(!empty($arry_book[0])){
							
							foreach($arry_book[0] as $arry_book_k =>$arry_book_main){
								foreach($arry_book_main as	$inx=> $arry_book_value){
								
									if($arry_book_k  =="main"){
					?>
									<tr>
										<td>
											
												<img src="image/boy.jpg" alt=""  width="40" height="40"/>
										</td>
										<td>
											<?php echo $arry_book[0]['main'][$inx];?>
											<BR>
											因為<?php echo $arry_book[0]['submain'][$inx];?>
										</td>
									</tr>
					
					<?php
									}
								}
							
							}
						}else{
							echo '<tr><td>';
							echo '目前還沒有人借閱過這本書喔！';
							echo '</td></<tr>';
						}
						
					?>

					</tbody>
				</table>
				
				<table class="table">
					<thead>
						<tr align="center">
							<TD COLSPAN=2>看過這本書的人，他們也看了哪些書? </TD>
						</tr>
					</thead>
					<tbody>
					<?php		  
						 if(!empty($arry_book[1])){
							
							foreach($arry_book[1] as $arry_book_k =>$arry_book_main){
								foreach($arry_book_main as	$inx=> $arry_book_value){
								
									if($arry_book_k  =="main"){
							
					?>
								<tr>
									<td>
										<img src="image/book.jpg" alt=""  width="40" height="40"/>
									</td>	
									<td>
										<?php echo $arry_book[1]['main'][$inx];?>
										<BR>
										<?php
											if(!$arry_book[1]['submain'][$inx]['user_id']==""){

												if($arry_book[1]['submain'][$inx]['borrow_cno']>0){
													echo $arry_book[1]['submain'][$inx]['user_id'];
													echo '和其他'.$arry_book[1]['submain'][$inx]['borrow_cno'].'位好友借閱過這本書';
												
												}else{
													echo '目前只有'.$arry_book[1]['submain'][$inx]['user_id'].'看過';
												}
											
											}
										?>
									
								</td>
							</tr>
					
					<?php
									}
								}
							
							}
						}else{
							echo '<tr><td>';
							echo '目前還沒有資料喔！';
							echo '</td></<tr>';
						}
						
						
					?>

					</tbody>
				</table>
				
				
				<table class="table">
					<thead>
						<tr align="center">
							<TD COLSPAN=2>哪些小組討論這本書？ </TD>
						</tr>
					</thead>
					<tbody>
					<?php		  
						 if(!empty($arry_book[2])){
							
							foreach($arry_book[2] as $arry_book_k =>$arry_book_main){
								foreach($arry_book_main as	$inx=> $arry_book_value){
								
								
									if($arry_book_k  =="main"){
									
						?>
									<tr>
										<td>
											
											<img src="image/group.png" alt=""  width="40" height="40"/>
											
										</td>
										<td>
											<?php echo $arry_book[2]['main'][$inx];?>
											<BR>
											<?php echo $arry_book[2]['submain'][$inx];?>
										</td>
									</tr>
						
					<?php
									}
								}
							
							}
						}
						else{
							echo '<tr><td>';
							echo '目前還沒有聊書小組在討論這本書！';
							echo '</td></<tr>';
						}
						
						
					?>

					</tbody>
				</table>
			</div>
				
		</div>




	</body>

</html>