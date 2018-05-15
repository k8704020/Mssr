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
		require_once(str_repeat("../",0)."inc/search_book_ch_no_online/code.php");
        require_once('filter_func.php');

		 //外掛頁面檔
        require_once(str_repeat("../",0).'inc/require_page/code.php');

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


					$book_sid = mysql_prep(trim($_GET["book_sid"]));
					$sess_uid = (int)$_SESSION['uid'];
					$class_code = trim($_SESSION['class'][0][1]);




				//-----------------------------------------------
	        	//SQL-撈出班上所有的學生、老師
	        	//-----------------------------------------------

					$sql="
						SELECT
							`uid`
						FROM(
							SELECT
									`class_code` ,`uid`
								FROM
									`student`
							UNION ALL
								SELECT
									`class_code` ,`uid`
								FROM
									`teacher`
						)v1
						WHERE
							`class_code` = '$class_code';
					";

					$arrys_result_class_member=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
					$arrys_class_member =	array();

					foreach($arrys_result_class_member as $key=>$j){
						foreach($j as $jkey=>$value){
							$arrys_class_member[$key] = $value;
						}
					}




				//-----------------------------------------------
	        	//SQL-判斷是否有看過這本書
	        	//-----------------------------------------------




				 $selectBook = $_GET['book_sid'];


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

//                 echo '<pre>';                    
//                 print_r($str);
//                 echo '<pre>';    
// die();


					$sql="
						SELECT
							`borrow_sdate`
						FROM
							`mssr_book_borrow_log`
						WHERE 1=1
							AND `user_id` = $sess_uid
							AND `book_sid` in ($str)
					";

					$arrys_if_borrow=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

//                 echo '<pre>';                    
//                 print_r($arrys_if_B);
//                 echo '<pre>';    
// die();




				//-----------------------------------------------
	        	//SQL-發文列表(分頁)
	        	//-----------------------------------------------
					$query_sql="
						SELECT
							`mssr_forum_article`.`cat_id`,
							`mssr_forum_article`.`user_id`,
							`mssr_forum_article`.`article_content`,
							`mssr_forum_article`.`keyin_cdate`,
							`mssr_forum_article`.`article_title`,
							`mssr_forum_article`.`article_like_cno`,
							`mssr_forum_article`.`article_id`,
							`mssr_forum_article`.`article_state`,

							`mssr_article_book_rev`.`article_cdate`,
							`mssr_article_book_rev`.`reply_cdate`,
							`mssr_article_book_rev`.`book_sid`,

							(
								SELECT CASE

									WHEN `mssr_forum_article`.`keyin_cdate` > `mssr_article_book_rev`.`reply_cdate`
										THEN `mssr_forum_article`.`keyin_cdate`

									WHEN `mssr_forum_article`.`keyin_cdate` < `mssr_article_book_rev`.`reply_cdate`
										THEN `mssr_article_book_rev`.`reply_cdate`
								END

							) AS `order_filter`

						FROM `mssr_forum_article`
							INNER JOIN `mssr_article_book_rev` ON
							`mssr_forum_article`.`article_id`=`mssr_article_book_rev`.`article_id`
						WHERE 1=1
							AND `mssr_article_book_rev`.`book_sid` in ($str)
							AND `article_state` LIKE '%正常%'
						ORDER BY `order_filter` DESC
					";
					$arrys_result_article=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);

				//-----------------------------------------------
	        	//SQL-這本書有多少發文
	        	//-----------------------------------------------
					$sql="
						SELECT
							`article_id`
						FROM
							`mssr_article_book_rev`
						WHERE
							`book_sid` = '$book_sid'
					";
					$arrys_result_book_article=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_book_article=count($arrys_result_article);

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
					$sql="
						SELECT
							`user_id`, `book_sid`
						FROM
							`mssr_book_borrow_semester`
						WHERE 1=1
							and `book_sid` in($str) 
						GROUP BY
							`user_id`
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
	        	//SQL-哪個聊書小組也在討論這本書
	        	//-----------------------------------------------
					$sql="
						SELECT
							`forum_id`
						FROM
							`mssr_forum_booklist`
						WHERE
							`book_sid` in($str) 
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
					//SQL-中國圖書分類號
					//-----------------------------------------------

						$sql="
							SELECT
								`book_ch_no`
							FROM
								`mssr_forum_book_ch_no_rev`
							WHERE
								`book_sid` = '{$book_sid}'		;
						";


						$arrys_result_ch_no=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
						$book_ch_no	=0;

						if(empty($arrys_result_ch_no)){

							$arrys_book_info	= get_book_info($conn_mssr,$book_sid,$array_filter=array(),$arry_conn_mssr);
							$book_isbn_13	= mysql_prep(trim($arrys_book_info[0]['book_isbn_13']));
							$book_isbn_10	= mysql_prep(trim($arrys_book_info[0]['book_isbn_10']));


							// 線上抓取分類號資料(以$book_isbn_13為優先)
							if($book_isbn_13!=""){
								$book_isbn 	= $book_isbn_13;
							}else if($book_isbn_10!=""){
								$book_isbn	= $book_isbn_10;
							}else{
								$book_isbn	= "";

							}


							// 如果有有搜尋到分類號，有則寫入資料，沒有則寫入"000"
							if((empty($arrys_result_ch_no)) && ($book_isbn != "")){

									$search_book_ch_no_online = search_book_ch_no_online($book_isbn);
									$book_ch_no				  = mysql_prep((int)$search_book_ch_no_online['book_ch_no'][0]);

									if($book_ch_no==""){

										$sql="
											# for mssr_forum_book_ch_no_rev
											INSERT INTO `mssr_forum_book_ch_no_rev` SET
												`book_sid`				= '{$book_sid}'		,
												`book_ch_no`			= 0			 		;
										";
										// 送出
										$err ='DB QUERY FAIL1';
										$sth=$conn_mssr->prepare($sql);
										$sth->execute()or die($err);

									}else{

										$sql="
											# for mssr_forum_book_ch_no_rev
											INSERT INTO `mssr_forum_book_ch_no_rev` SET
												`book_sid`				= '{$book_sid}'		,
												`book_ch_no`			= {$book_ch_no}		 		;
										";
										// 送出
										$err ='DB QUERY FAIL2';
										$sth=$conn_mssr->prepare($sql);
										$sth->execute()or die($err);

									}
							}




						}else{
							$book_ch_no	=$arrys_result_ch_no[0]['book_ch_no'];
						}









	//---------------------------------------------------
    //檢驗
    //---------------------------------------------------
		//echo "<pre>";
		//print_r($arrys_if_borrow);
		//echo "</pre>";
		//die();

	//---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球-書籍討論";
		$has_borrow_book = FALSE;
			if(!empty($arrys_if_borrow)){
				$has_borrow_book = TRUE;

		}
		$book_ch_no = mb_substr($book_ch_no,0,1);
		
		//$book_ch_no = 842;
		 // echo $book_ch_no;
		 // die();

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
	<script type="text/javascript" src="js/mssr_forum/track_code.js"></script>
	<script type="text/javascript" src="js/mssr_forum/scaffolding_code.js"></script>
    <script type="text/javascript" src="inc/add_action_forum_log/code.js"></script>

    <link type="text/css" rel="stylesheet" href="css/bootstrap.css">
    <link type="text/css" rel="stylesheet" href="css/mssr_forum.css">
    <link type="text/css" rel="stylesheet" href="../../inc/code.css">


	<script>
		var book_sid    ='<?php echo addslashes($book_sid);?>';
        var sess_uid    =<?php echo $sess_uid;?>;



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


			$('#input_leave').click(function() {
				$.unblockUI();
				return false;
			});

			$('#invite_box_leave').click(function() {
				$.unblockUI();
				return false;
			});


		});
		
		
		function input_article(ch_no){
			action_log('inc/add_action_forum_log/code.php','b1',sess_uid,0,0,book_sid,'',0,0,0,0,'');
			var ch_no =parseInt(ch_no);
			//alert(ch_no);
			if(ch_no==0){
				choose_article_type();
			}else if(ch_no==8){
				mssr_article_input_box_1_F();
			}else{
				mssr_article_input_box_1_NF();
			}

		}

		
	

		function open_mssr_input_box(){

			action_log('inc/add_action_forum_log/code.php','b1',sess_uid,0,0,book_sid,'',0,0,0,0,'');

				$.blockUI({
					//action_log('inc/add_action_forum_log/code.php','b1',0,0,0,0,0,0,0,0,0,);
					message: $('#mssr_article_input_box'),

					css:{
						top:  ($(window).height() - 500) /2 + 'px',
						left: ($(window).width() - 900) /2 + 'px',
						textAlign:	'left',
						width: '900px'

					}
				});
		}


		//檢查內容是否有輸入
		function input_article_check(){

			var input_content	=document.getElementsByName("mssr_input_box_name_content[]");
			var title			=document.getElementsByName("mssr_input_box_name_title");


			add_action_forum_log(
				process_url ='inc/add_action_forum_log/code.php',
				action_code ='b14',
				action_from ='<?php echo (int)$_SESSION["uid"];?>',
				user_id_1   =0,
				user_id_2   =0,
				book_sid_1  =book_sid,
				book_sid_2  ='',
				forum_id_1  =0,
				forum_id_2  =0,
				article_id  =0,
				reply_id    =0,
				go_url      =''
			);

			if(title.value==""){
				alert("請輸入標題是否有輸入");
				return false;
			}



			for(var i = 0;i<input_content.length;i++){
				if(trim(input_content[i].value)==""){
					alert("請確認內容都有輸入");
					return false;

				}
			}

			article_input_form.submit();

		}
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

						$book_sid 			= mysql_prep(trim($_GET["book_sid"]));
						$arrys_book_info	= get_book_info($conn_mssr,$book_sid,$array_filter=array(),$arry_conn_mssr);
						$book_name 			= mysql_prep(trim($arrys_book_info[0]['book_name']));
						$book_author 		= mysql_prep(trim($arrys_book_info[0]['book_author']));
						$book_publisher 	= mysql_prep(trim($arrys_book_info[0]['book_publisher']));
						$book_isbn_13 		= mysql_prep(trim($arrys_book_info[0]['book_isbn_13']));?>



					<p>
					  <?php echo filter($book_name)?><P>
					  作者：<?php echo filter($book_author)?><P>
					  出版社：<?php echo filter($book_publisher)?><P><P><P>



					<?php if(count($arrys_result_book_check)===0){?>
							<a class="btn" type="button"
								onclick=
								"book_track('b2',<?php echo $_SESSION["uid"];?>,'<?php echo $_GET["book_sid"];?>',<?php echo count($arrys_result_book_check);?>)">
								追蹤書籍
						  </a>

					  <?php
					  }else{
					  ?>
							<a class="btn" type="button"
								onclick=
								"book_track('b3',<?php echo $_SESSION["uid"];?>,'<?php echo $_GET["book_sid"];?>',<?php echo count($arrys_result_book_check);?>)">
								取消追蹤
							</a>
					<?php }?>



				</div>

				<div class="group_info2">
				 <?php
					 /*
						---書籍簡介---
						先撈出book_note
						判斷book_note是不是空值
							為空:線上APK找簡介(search_book_info_online)
								 找到資料==>update book_note
								 沒找到資料==>book_note="無";
							不為空
							   echo book_note

					 */
					//撈出書籍資訊
					$array_select = array ("book_note","book_isbn_13","book_isbn_10");
					get_book_info($conn_mssr,$book_sid,$array_filter=array(),$arry_conn_mssr);
					$arrys_book_info= get_book_info($conn_mssr,$book_sid,$array_select,$arry_conn_mssr);
					$book_note 		= mysql_prep(trim($arrys_book_info[0]['book_note']));



					//判斷資料庫是否沒有書籍簡介資料
					if($book_note==""){

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
						if($book_isbn_13!=""){
							$book_isbn 		= $book_isbn_13;
						}else if($book_isbn_10!=""){
							$book_isbn		= $book_isbn_10;
						}else{
							$book_isbn= "";
						}



						if(($book_note=="") && ($book_isbn != "")){

							$search_book_info_online = search_book_info_online($book_isbn);
							$book_note		= mysql_prep(trim($search_book_info_online['book_note'][0]));


							//簡介資料寫進book_note
							if($book_note==""){
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
							}

							$book_note=mb_substr($book_note,0,150)."..";

				?>
					內容簡介：<BR><?php echo filter($book_note)?>
				<?php

					}else{
					$book_note=mb_substr($book_note,0,150)."..";
				?>
					內容簡介：<BR><?php echo $book_note?>

				<?php }


				?>


				</div>

		 </div>






	<!--========================content=====================================-->
	<div class="content">

		<div class="left_content">


		<?php	if($has_borrow_book){		?>
					  <div class="btn-group">

						
							<input class="btn btn-primary btn-sm" id="open_mssr_input_box" type ="button" onclick="input_article(<?php echo $book_ch_no;?>);" value="發表文章">
                            </input>
						
					  </div>

		<?php	}else{		?>
						<FONT Color="red" align="center">
							<H3>你還沒看過這本書，不能發表文章，趕快去看吧!

							<input class="btn btn-danger" onclick="action_log('inc/add_action_forum_log/code.php','b13',<?php echo $_SESSION["uid"];?>,0,0,'<?php echo $_GET["book_sid"];?>','',0,0,0,0,'../mssr_menu.php');" type ="button" value="進行閱讀登記"></input></H3>
						</FONT>

		<?php	}		?>



			<p>
			<p>



			   <!----------書本發文---------->
			<?php
				if(empty($arrys_result_article)){
					echo("這本書目前還沒有留言喔，趕快來留言吧！");
				}else{
			?>
					<table class="table table-hover table-striped">
							<thead>
								  <tr>
									<th style="width:14%">
									  種類
									</th>
									<th style="width:25%">
									  標題
									</th>
									<th style="">
									  針對發文書籍
									</th>
									<th style="width:10%">
									  姓名
									</th>
									<th style="width:15%">
									  時間
									</th>
									<th style="width:5%">
									  讚
									</th>
									<th style="width:7%">
									  回應
									</th>

								  </tr>
							</thead>
						<tbody>
			<?php

					for($i=0; $i<count($arrys_result_article); $i++){
						//echo count($arrys_result_article)."article_con";


						$cat_id 			= (int)$arrys_result_article[$i]['cat_id'];
						$user_id 			= (int)$arrys_result_article[$i]['user_id'];
						$article_id 		= (int)$arrys_result_article[$i]['article_id'];
						$article_title 		= mysql_prep(trim($arrys_result_article[$i]['article_title']));
						$article_content 	= mysql_prep(trim($arrys_result_article[$i]['article_content']));
						$keyin_cdate 		= mysql_prep(trim($arrys_result_article[$i]['keyin_cdate']));
						$article_like_cno 	= (int)$arrys_result_article[$i]['article_like_cno'];

						//echo $arrys_result_class_member_con."member_con";

						//如果不是班上學生不顯示資料

						// if(!in_array($user_id, $arrys_class_member)) {
							// continue;
						// }


						//article_title			文章標題
						if(mb_strlen($article_title)>15){
							$article_title=mb_substr($article_title,0,15)."..";
						}
						//article_content		文章內容
						if(mb_strlen($article_content)>30){
							$article_content=mb_substr($article_content,0,30)."..";
						}
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
						//SQL-回復數量
						//-----------------------------------------------
						$sql="
							SELECT
								`reply_id`
							FROM
								`mssr_forum_article_reply`
							WHERE
								`article_id` = $article_id
						";
						$arrys_result_replynum=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
						$numrow_replynum=count($arrys_result_replynum);
						//-----------------------------------------------
						//SQL-回復姓名
						//-----------------------------------------------
						$sql="
							SELECT
								`name`
							FROM
								`member`
							WHERE
								`uid` = $user_id
						";

						$arrys_result_replyname=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
						$reply_name			=$arrys_result_replyname[0]['name'];




						//-----------------------------------------------
						//cat_name
						//-----------------------------------------------
						$cat_name = "未分類";
						$new_cat_id = substr($cat_id,0,1);

						if($new_cat_id==1){$cat_name="我想要分享<BR>(小說類)";}
						elseif($new_cat_id==2){$cat_name="我想要問<BR>(小說類)";}
						elseif($new_cat_id==3){$cat_name="我想要分享<BR>(非小說類)";}
						elseif($new_cat_id==4){$cat_name="我想要問<BR>(非小說類)";}

						$sql = "
						select 
							book_sid
						from 
							mssr_article_book_rev
						where article_id = $article_id; 	
						";
					$arrys_result_book_name=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$book_sid_simple 	= $arrys_result_book_name[0]['book_sid'];
					$arrys_book_info	= get_book_info($conn_mssr,$book_sid_simple,$array_filter=array(),$arry_conn_mssr);
					$book_name 			= mysql_prep(trim($arrys_book_info[0]['book_name']));

					



			?>
			 
				


			



				<tr>
					<td><?php echo $cat_name?></td>
					<td>


						<a onclick="action_log('inc/add_action_forum_log/code.php','b7',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'<?php echo $book_sid_simple;?>','',0,0,<?php echo $article_id;?>,0,'mssr_forum_book_reply.php?article_id=<?php echo $article_id;?>');" href="javascript:void(0);">
							<?php echo filter($article_title)?>
						</a>
					</td>
					<td><?php echo $book_name ?></td>
					<td><?php echo $reply_name?></td>
					<td><?php echo $keyin_cdate?></td>
					<td><?php echo $numrow_like?></td>
					<td><?php echo $numrow_replynum?></td>
				</tr>


			<?php }?>
		<?php }?>

			</tbody>
		  </table>
		</div>

<!----------block ui div---------->
<div id="choose_article_type"  style="display:none; cursor:default; font-size:18px; height:300px;width:600px">
	<input type="image" id="mssr_select_book_box_leave" src="image/xlogo.png" alt="離開" onclick="close_blockui()" width="30" height="30"/>
	<P>
	<H3>這是什麼類型的書？</h3>
	<P><P>
		<div style="width:500px;margin:0px auto;">
			<button type="button" class="btn btn-primary btn-lg btn-block"
				onclick="choose_type('F');">
				故事類
			</button>
			<button type="button" class="btn btn-success btn-lg btn-block"
				onclick=" choose_type('NF');">
				非故事類
			</button>
		</div>
</div>
		
		
<!----------block ui div(發表文章-輸入發文內容 step1 小說類)---------->
<div id="mssr_article_input_box_1_F" style="display:none; cursor: default;">
	<input type="image" id="mssr_select_book_box_leave" src="image/xlogo.png" alt="離開" onclick="close_blockui()" width="30" height="30"/>
	<H3 style="color:#660000;font-family:微軟正黑體;"><B>看完這本書後，我想要分享還是有什麼問題想提問？</B></H3>
	
		<div class="radio">
		  <label>
			<input type="radio" name="scaffolding_step1_choose" id="optionsRadios1" value="FA">
			我想要問
		  </label>
		</div>
		
		<div class="radio">
		  <label>
			<input type="radio" name="scaffolding_step1_choose" id="optionsRadios1" value="FL">
			我想要分享
		  </label>
		</div>
		
		
	
	<BR>
	<?php if($book_ch_no == 0){ ?>
		<input class="btn btn-primary" id="mssr_select_book_box_next-last" type="button" onclick="choose_article_type()" value="上一步"/>
	<?php } ?>
	
	<input class="btn btn-primary" id="mssr_select_book_box_next" type="button" onclick="input_step_2()" value="下一步"/>

</div>


<!----------block ui div(發表文章-輸入發文內容 step1)---------->
<div id="mssr_article_input_box_1_NF" style="display:none; cursor: default;">
	<input type="image" id="mssr_select_book_box_leave" src="image/xlogo.png" alt="離開" onclick="close_blockui()" width="30" height="30"/>
	<H3 style="color:#660000;font-family:微軟正黑體;"><B>看完這本書後，我想要分享還是有什麼問題想提問？</B></H3>
	
		<div class="radio">
		  <label>
			<input type="radio" name="scaffolding_step1_choose" id="optionsRadios1" value="NFA">
			我想要問
		  </label>
		</div>
		
		<div class="radio">
		  <label>
			<input type="radio" name="scaffolding_step1_choose" id="optionsRadios1" value="NFL">
			我想要分享
		  </label>
		</div>
		
		
	
	<BR>
	<?php if($book_ch_no == 0){ ?>
		<input class="btn btn-primary" id="mssr_select_book_box_next-last" type="button" onclick="choose_article_type()" value="上一步"/>
	<?php } ?>
	
	<input class="btn btn-primary" id="mssr_select_book_box_next" type="button" onclick="input_step_2()" value="下一步"/>

</div>

<!----------block ui div(發表文章-輸入發文內容 step3)---------->
<div id="mssr_article_input_box_3" style="display:none; cursor: default;">
	<input type="image" id="mssr_select_book_box_leave" src="image/xlogo.png" alt="離開" onclick="close_blockui()" width="30" height="30"/>
	<H3 style="color:#660000;font-family:微軟正黑體;"><B>請選擇想發文的類別</B></H3>
	
	<div id="scaffolding_step2_content">
	 
	</div>
	
	<BR>
	<?php if($book_ch_no == 0){ ?>
		<input class="btn btn-primary" id="mssr_select_book_box_next-last" type="button" onclick="choose_article_type()" value="上一步"/>
	<?php }else if($book_ch_no ==8){ ?>
		<input class="btn btn-primary" id="mssr_select_book_box_next-last" type="button" onclick="mssr_article_input_box_1_F()" value="上一步"/>
	<?php }else{ ?>
		<input class="btn btn-primary" id="mssr_select_book_box_next-last" type="button" onclick="mssr_article_input_box_1_NF()" value="上一步"/>
	<?php } ?>
	
	
	<input class="btn btn-primary" id="mssr_select_book_box_next" type="button" onclick="input_step_3()" value="下一步"/>

</div>

<!----------block ui div(發表文章-輸入發文內容 step4)---------->
<div id="mssr_article_input_box_4" style="display:none; cursor: default;">
	<input type="image" id="mssr_select_book_box_leave" src="image/xlogo.png" alt="離開" onclick="close_blockui()" width="30" height="30"/>
	<H3 style="color:#660000;font-family:微軟正黑體;"><B>請選擇想發文的例句</B></H3>
	
	<div id="scaffolding_step3_content">
	 
	</div>
	
	<form id="article_input_form" name="article_input_form" action="mssr_forum_book_discussion_A.php" method="post">
		<input type="hidden" value="無" name="input_article_book_sid" id="input_article_book_sid">
	<div id="input_content" style="position:absolute;top:-20px;	left:50%;	width: 300px">
        <p id="mssr_input_box_title">標題：&nbsp; &nbsp;<input  id="haha" type="text"  name="mssr_input_box_name_title" size="40" maxlength="40"/></p>
		<span id="mssr_input_box_content_text">內容：</span>
		<div id="scaffolding_step4_content">
		</div>
		<textarea name="book_sid"  style="display:none" cols="3" rows="8"><?php echo $_GET["book_sid"]?></textarea>
		<input type="hidden" id ="select_input_type" name="type" value="無">
		<input type="hidden" id ="article_refer_code" name="article_refer_code" value="無">
		
		<input type="text" id="action_code" name="action_code" value="g1" style='display:none;'>
		</div>
		<BR>
	<input class="btn btn-primary" id="mssr_select_book_box_next-last" type="button" onclick="mssr_article_input_box_3()" value="上一步"/>
	<input id="mssr_input_box_submit" class="btn btn-primary" type="button" onclick="input_article_check()" value="送出"/>
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
											<?php   echo $arry_book[0]['main'][$inx];?>
											<BR>
                                            <?php
                                                    if(!empty($arry_book[0]['submain'][$inx])){
                                                        echo '因為'.$arry_book[0]['submain'][$inx];


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