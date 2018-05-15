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
          //---------------------------------------------

			$forum_id = (int)$_GET["forum_id"];
			$sess_uid = (int)$_SESSION['uid'];


			//-----------------------------------------------
			//SQL-討論區發文列表(分頁)
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

					  `mssr_article_forum_rev`.`article_cdate`,
					  `mssr_article_forum_rev`.`reply_cdate`,
					  `mssr_article_forum_rev`.`forum_id`,

					  (
						SELECT CASE

						  WHEN `mssr_forum_article`.`keyin_cdate` > `mssr_article_forum_rev`.`reply_cdate`
							THEN `mssr_forum_article`.`keyin_cdate`

						  WHEN `mssr_forum_article`.`keyin_cdate` < `mssr_article_forum_rev`.`reply_cdate`
							THEN `mssr_article_forum_rev`.`reply_cdate`
						END

					  ) AS `order_filter`

					FROM `mssr_forum_article`
					  INNER JOIN `mssr_article_forum_rev` ON
					  `mssr_forum_article`.`article_id`=`mssr_article_forum_rev`.`article_id`
					WHERE 1=1
					  AND `mssr_article_forum_rev`.`forum_id` = $forum_id
					  AND `article_state` LIKE '%正常%'
					  AND `article_type` = 1
					ORDER BY `order_filter` DESC
			  ";
			  $arrys_result_article=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
			  $arrys_result_article_con = count($arrys_result_article);



			//-----------------------------------------------
			//SQL-討論區資訊
			//-----------------------------------------------
			  $sql="
					SELECT
					  `forum_name`, `forum_content`, `forum_state`, `create_by`
					FROM
					  `mssr_forum`
					WHERE
					  `forum_id` = $forum_id
			  ";
			  $arrys_result_2=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			  $numrow_2=count($arrys_result_2);

			//-----------------------------------------------
			//SQL-這個討論區有多少發文
			//-----------------------------------------------
			  $sql="
					SELECT
					  `article_id`
					FROM
					  `mssr_article_forum_rev`
					WHERE
					  `forum_id` = '$forum_id'
			  ";
			  $arrys_result_forum_article=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			  $numrow_forum_article=count($arrys_result_forum_article);

			//-----------------------------------------------
            //SQL-這個討論區有多少回覆
            //-----------------------------------------------
			  $sql="
					SELECT
					  `reply_id`
					FROM
					  `mssr_article_reply_forum_rev`
					WHERE
					  `forum_id` = '$forum_id'
			  ";
			  $arrys_result_forum_reply=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			  $numrow_forum_reply=count($arrys_result_forum_reply);

			//-----------------------------------------------
			//SQL-這個討論區有多少成員
			//-----------------------------------------------
			  $sql="
					SELECT
						`user_id`
					FROM
						`mssr_user_forum`
					WHERE 1=1
						AND `forum_id` = $forum_id
						AND `user_state` LIKE '%啟用%'
			  ";
			  $arrys_result_member=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			  $arrys_result_member_con=count($arrys_result_member);



			//-----------------------------------------------
			//SQL-判斷登入者是否為該社團學生
			//-----------------------------------------------
			$sql="
					SELECT
						`forum_id`, `user_id`
					FROM
						`mssr_user_forum`
					WHERE 1=1
						AND `forum_id` 	= $forum_id
						AND `user_id`	= $sess_uid
						AND	`user_state` LIKE '%啟用%'
			";
			$arrys_group_stud_check=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

			//-----------------------------------------------
	        //SQL-聊書小組書單
	        //-----------------------------------------------
			$sql="
				SELECT
					`mssr_forum_booklist`.`book_sid`, `mssr_forum_book_ch_no_rev`.`book_ch_no`
				FROM
					`mssr_forum_booklist`
				INNER JOIN `mssr_forum_book_ch_no_rev` ON
					`mssr_forum_book_ch_no_rev`.`book_sid`=`mssr_forum_booklist`.`book_sid`
				WHERE 1=1
					AND `forum_id` = $forum_id
					AND `book_state` = 1;
			";


			$arrys_result_shelf=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$arrys_result_shelf_con = count($arrys_result_shelf);

			//-----------------------------------------------
	        //SQL-聊書小組精華區分類
	        //-----------------------------------------------
			$sql="
				SELECT
					`cat_name`, `cat_id`
				FROM
					`mssr_forum_best_article_category`
				WHERE 1=1
					AND `forum_id` = $forum_id
					AND `cat_state` = 1;
			";
			$arrys_result_best_cat=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);




  //---------------------------------------------------
    //檢驗
    //---------------------------------------------------
    //echo "<pre>";
    //print_r($sql);
    //echo "</pre>";
    //die();

  //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------




  //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=$arrys_result_article_con;    //資料總筆數
        $psize =10;                 //單頁筆數,預設10筆
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
        //echo $numrow."<br/>";

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

		//網頁標題
        $title="明日星球-聊書小組一般討論區";

		$site ="mssr_forum_group_discussion.php";


		//判斷是否在此小組有管理權限
		$has_manager = FALSE;

		$forum_id_manager = $_SESSION['forum_id_manager'];
			for($i=0; $i < count($forum_id_manager);$i++){
				if(in_array($forum_id , $forum_id_manager[$i])){
					$has_manager	=	TRUE;
				}
			}

		//是否為小組成員
		$has_group_member = FALSE;
		if(!empty($arrys_group_stud_check)){
			$has_group_member = TRUE;
		}

        if($numrow!==0){
            $arrys_chunk =array_chunk($arrys_result_article,$psize);
            $arrys_result=$arrys_chunk[$pinx-1];
        }else{

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
	<script type="text/javascript" src="js/mssr_forum/scaffolding_code.js"></script>
	<script type="text/javascript" src="inc/add_action_forum_log/code.js"></script>


    <link type="text/css" rel="stylesheet" href="css/bootstrap.css">
    <link type="text/css" rel="stylesheet" href="css/mssr_forum.css">
    <link type="text/css" rel="stylesheet" href="../../inc/code.css">








	<script>
		var psize=<?php echo $psize;?>;
		var pinx =<?php echo $pinx;?>;
		var forum_id=<?php echo addslashes($forum_id);?>;
		var sess_uid=<?php echo (int)$sess_uid;?>;

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
				'page_name' :'mssr_forum_group_discussion.php',
				'page_args' :{
					'forum_id' :<?php echo (int)$forum_id;?>
				}
			}
			var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
		}

		//滑鼠移入
		function mouse_over(obj){
			obj.style.cursor='pointer';
		}

		//移至精華區block-ui
		function open_move_best_article(choose_article_id, user_id, forum_id) {

			var choose_article_id 	=	parseInt(choose_article_id);
			var user_id				=	parseInt(user_id);
			var forum_id			=	parseInt(forum_id);


			//action log
			add_action_forum_log(
				process_url ='inc/add_action_forum_log/code.php',
				action_code ='g20',
				action_from ='<?php echo (int)$_SESSION["uid"];?>',
				user_id_1   =user_id,
				user_id_2   =0,
				book_sid_1  ='',
				book_sid_2  ='',
				forum_id_1  =forum_id,
				forum_id_2  =0,
				article_id  =choose_article_id,
				reply_id    =0,
				go_url      =''
			);

			document.getElementById('choose_article_id').value = choose_article_id;

			$.blockUI({
				message: $('#move_best_article'),
				css:{
					top:  ($(window).height() - 400) /2 + 'px',
					left: ($(window).width() - 700) /2 + 'px',
					textAlign:	'left',
					width: '700px'

				}
			});

		

		}
		
		//發表文章 選擇書籍block-ui
		function select_book(){

			add_action_forum_log(
				process_url ='inc/add_action_forum_log/code.php',
				action_code ='g22',
				action_from ='<?php echo (int)$_SESSION["uid"];?>',
				user_id_1   =0,
				user_id_2   =0,
				book_sid_1  ='',
				book_sid_2  ='',
				forum_id_1  =<?php echo (int)$_GET["forum_id"];?>,
				forum_id_2  =0,
				article_id  =0,
				reply_id    =0,
				go_url      =''
			);

			$.blockUI({
					message: $('#mssr_select_book_box'),

					css:{
						top:  ($(window).height() - 400) /2 + 'px',
						left: ($(window).width() - 750) /2 + 'px',
						textAlign:	'left',
						width: '750px'

					}
				});
			$('#mssr_select_book_box_leave').click(function() {
					$.unblockUI();
					return false;
			});
		}
		
		
		
		



		function add_group(){
		//加入小組

			var url ='';
			var site = 'mssr_forum_group_discussion.php';

			var page=str_repeat('../',0)+'mssr_forum_group_add_groupA.php';
			var arg ={
				'sess_uid':sess_uid,
				'forum_id' :forum_id,
				'psize'   :psize,
				'pinx'    :pinx,
				'site'	  :site
			};
			var _arg=[];
			for(var key in arg){
				_arg.push(key+"="+encodeURI(arg[key]));
			}
			arg=_arg.join("&");

			if(arg.length!=0){
				url+=page+"?"+arg;
			}else{
				url+=page;
			}

			if(confirm('你確定要加入此聊書小組嗎?')){
				location.href=url;

			}else{
				return false;
			}
		}

		//檢查內容是否有輸入
		function input_article_check(){

			var input_content	=document.getElementsByName("mssr_input_box_name_content[]");
			var title			=document.getElementsByName("mssr_input_box_name_title");



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
            <a href="mssr_forum_group_index.php?forum_id=<?php echo $forum_id?>"><?php echo trim($arrys_result_2[0]['forum_name']);?></a>
          </li>
          <li>
            <a href="mssr_forum_group_discussion.php?forum_id=<?php echo $forum_id?>">大家來聊書</a>
          </li>
          <li class="active">
            一般討論區
          </li>
        </ul>
  </div>

<!--========================group header=====================================-->
  <div class="group_header">

        <div class="group_image" >
          <img  src="image/group.png" class="img-thumbnail" width="100" height="100">
        </div>

			<div class="group_info1">
					<?php

							$forum_name     = trim($arrys_result_2[0]['forum_name']);
							$forum_content  = trim($arrys_result_2[0]['forum_content']);
							$create_by      = trim($arrys_result_2[0]['create_by']);
							$forum_state    = trim($arrys_result_2[0]['forum_state']);
							$get_user_info=get_user_info($conn_user,$create_by,$array_filter=array('name'),$arry_conn_user);
				   ?>
					<p>
					  <?php echo $forum_name?><P>
					  版主:<?php echo $get_user_info[0]['name']?><P><P><P>


					<?php
						if($forum_state=="申請中"){
							$msg='
								<script>
									alert("目前小組還在建構中喔！");
								</script>

							';
							echo '<font color=red><h3>目前小組還在建構中喔！</h3></font>';
							die($msg);
						}
					?>

					<?php if($has_group_member){?>
					   <a class="btn" id="open_invite_box" type="button">邀請人員</a>
					<?php } ?>
					<?php if(!$has_group_member){?>
							<a class="btn" type="button" href="javascript:void(0);"
							<?php
								echo 'id="action_code_g15"';
								echo " forum_id_1='{$forum_id}'";
							?>>
								申請加入</a>
					<?php }?>


			</div>

        <div class="group_info2">

            <p>
              小組資訊：<br>
              <?php echo $numrow_forum_article?>篇發文<br>
              <?php echo $numrow_forum_reply?>篇回覆<br>
              <?php echo $arrys_result_member_con?>位成員<br>

        </div>

 </div>


<!--========================tab_bar=====================================-->

	<div class="tab_bar">

		  <div class="tabbable" id="tabs-215204">
			<ul class="nav nav-tabs">

			<li>
				<a 	href="javascript:void(0);"
				<?php
					echo 'class="action_code_g11"';
					echo " forum_id_1='{$forum_id}'";
					echo " go_url='mssr_forum_group_index.php?forum_id={$forum_id}'";
				?>>
					首頁
				</a>

			</li>
			<li class="active">
				<a 	href="javascript:void(0);"
				<?php
					echo 'class="action_code_g2"';
					echo " forum_id_1='{$forum_id}'";
					echo " go_url='mssr_forum_group_discussion.php?forum_id={$forum_id}'";
				?>>
					大家來聊書
				</a>
			</li>
			<li>
				<a 	href="javascript:void(0);"
				<?php
					echo 'class="action_code_g3"';
					echo " forum_id_1='{$forum_id}'";
					echo " go_url='mssr_forum_group_shelf.php?forum_id={$forum_id}'";
				?>>
					興趣書單
				</a>
			</li>
			<li>
				<a 	href="javascript:void(0);"
				<?php
					echo 'class="action_code_g4"';
					echo " forum_id_1='{$forum_id}'";
					echo " go_url='mssr_forum_group_member.php?forum_id={$forum_id}'";
				?>>
					聊書小組成員
				</a>
			</li>
			</ul>
		  </div>

	</div>



<!--========================content=====================================-->
<div class="content">

    <div class="left_content">

      <div class="btn-group">
         <button class="btn btn-default" type="button" disabled="disabled" onclick="javascript:location.href='mssr_forum_group_discussion.php'">一般討論區</button>
         <button class="btn btn-default" type="button"
		 <?php
			echo ' id="action_code_g12"';
			echo " forum_id_1='{$forum_id}'";
			echo " go_url='mssr_forum_group_discussion_vip.php?forum_id={$forum_id}'";
		?>>
			精華區
		 </button>
      </div>


     <?php if($has_group_member){?>
		 <div style="float:right">

			<input class="btn btn-primary btn-sm" type ="button" value="發表文章" onclick="select_book()"></input>
		  </div>
	<?php }?>

     <P>



		 <?php
			if(empty($arrys_result_article)){
				echo("<H4 align=center>目前還沒有文章喔，趕快來留言吧！</H4>");

			}else{
		 ?>
			<table class="table table-hover table-striped">
				<thead>
				  <tr>
					<th style="width:12%">
						種類
					</th>
					<th style="width:10%">
						討論書本
					</th>
					<th style="width:25%">
						標題
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
					<?php if($has_manager){?>
						<th>
						</th>
					<?php }?>
				  </tr>
				</thead>
				<tbody>

			 <?php

				foreach($arrys_result as $inx=>$arry_result_article):

				$cat_id 				= $arry_result_article['cat_id'];
				$user_id 				= $arry_result_article['user_id'];
				$article_id 			= $arry_result_article['article_id'];
				$article_title 			= $arry_result_article['article_title'];
				$article_content		= $arry_result_article['article_content'];
				$keyin_cdate 			= $arry_result_article['keyin_cdate'];
				$article_like_cno 		= $arry_result_article['article_like_cno'];

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
				//SQL-姓名
				//-----------------------------------------------
				$sql="
					SELECT
						`name`
					FROM
						`member`
					WHERE
						`uid` = $user_id
				";
				$arrys_result_replyname	=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
				$reply_name				=$arrys_result_replyname[0]['name'];
				//-----------------------------------------------
				//SQL-mark book(article)
				//-----------------------------------------------
				$sql="
					SELECT
						`book_sid`
					FROM
						`mssr_forum_article_mark_rev`
					WHERE
						`article_id` = $article_id
				";
				$arrys_result_mark_book		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$mark_book_sid	=	"";
				if(!empty($arrys_result_mark_book)){
					$mark_book_sid				=trim($arrys_result_mark_book[0]['book_sid']);
					$arrys_book_info	=get_book_info($conn_mssr,$mark_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
					$mark_book_name 			= trim($arrys_book_info[0]['book_name']);

					//book_name		書名
					if(mb_strlen($mark_book_name)>4){
						$mark_book_name=mb_substr($mark_book_name,0,4)."..";
					}

					//書籍封面處理
					$mark_book_pic		 = '../../info/book/'.$mark_book_sid.'/img/front/simg/1.jpg';
					if(file_exists($mark_book_pic)){
						$rs_bookpic_root = '../../info/book/'.$mark_book_sid.'/img/front/simg/1.jpg';
					}else{
						$rs_bookpic_root = 'image/book.jpg';
					}
				}


				//-----------------------------------------------
				//cat_name
				//-----------------------------------------------
				$cat_name = "未分類";
				$new_cat_id = substr($cat_id,0,1);

				if($new_cat_id==1){$cat_name="我想要分享<BR>(小說類)";}
				elseif($new_cat_id==2){$cat_name="我想要問<BR>(小說類)";}
				elseif($new_cat_id==3){$cat_name="我想要分享<BR>(非小說類)";}
				elseif($new_cat_id==4){$cat_name="我想要問<BR>(非小說類)";}
				?>

				<tr>
					<td><?php echo $cat_name?></td>
					<td>

						<?php
							if($mark_book_sid!=""){

						?>
						<a href="mssr_forum_book_discussion.php?book_sid=<?php echo $mark_book_sid?>">
							<img src="<?php echo $rs_bookpic_root ?>" alt="<?php echo $mark_book_name?>"  width="30" height="30"/>
						</a>
						<br>
						<a href="mssr_forum_book_discussion.php?book_sid=<?php echo $mark_book_sid?>"><?php echo $mark_book_name?></a>
						<?php
							}

						?>


					</td>
					<td>
						<a 	href="javascript:void(0);"
							<?php
								echo 'class="action_code_g19"';
								echo " user_id_1='{$user_id}'";
								echo " forum_id_1='{$forum_id}'";
								echo " article_id='{$article_id}'";
								echo " go_url='mssr_forum_group_reply.php?article_id={$article_id}'";
							?>>
				
							<?php echo filter($article_title);?>
						</a>
					</td>
					<td><?php echo $reply_name?></td>
					<td><?php echo $keyin_cdate?></td>
					<td><?php echo $numrow_like?></td>
					<td><?php echo $numrow_replynum?></td>

					<?php if($has_manager){?>
						<td align="center">
							 <input class="btn btn-default btn-sm" type ="button" value="移至精華區"
								onclick="open_move_best_article(<?php echo $article_id;?>,<?php echo $user_id;?>,<?php echo $forum_id;?>)">
							 </input>
							 <input class="btn btn-default btn-sm" type ="button" value="隱藏"
								onclick="if(confirm('確定隱藏此篇討論文?')){hide_article(<?php echo $user_id;?>,<?php echo $forum_id;?>,<?php echo $article_id;?>);}">
							</input>
						</td>
					<?php }?>
				</tr>
		 <?php
		 endforeach ;
		 }
		 ?>

			</tbody>
		</table>

<!--========================分頁=====================================-->
		  <table style="float:right;">
			<tr valign="middle">
				<td align="left">
					<!-- 分頁列 -->
					<span id="page" style="position:relative;"></span>
				</td>
			</tr>
		  </table>

    </div>




<!------------block ui div(發表文章-選擇書籍 step1)------------>
<div id="mssr_select_book_box" style="display:none; cursor: default;">
	<input type="image" id="mssr_select_book_box_leave" src="image/xlogo.png" alt="離開" onclick="close_blockui()" width="30" height="30"/>

		<H3 style="color:#660000;font-family:微軟正黑體;"><B>請選擇針對發表文章的書籍</B></H3>

	<form id="select_book_form" name="select_book_form">

	<div style="overflow:auto;  height:300px; position:relative; width:100%;">
		<?php
			if(empty($arrys_result_shelf)){
				echo("聊書小組的興趣書單，還沒有新增書籍");
			}else{

			foreach($arrys_result_shelf as $arrys_result_shelf):
					$book_sid 			= trim($arrys_result_shelf['book_sid']);

					$arrys_book_info	=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
					$book_name 			= trim($arrys_book_info[0]['book_name']);

					//book_name		書名
						if(mb_strlen($book_name)>7){
							$book_name=mb_substr($book_name,0,7)."..";
						}
					//書籍封面處理
						$bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
						if(file_exists($bookpic_root)){
							$rs_bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
						}else{
							$rs_bookpic_root = 'image/book.jpg';
						}
					
					$sql="
						SELECT
							`book_ch_no`
						FROM
							`mssr_forum_book_ch_no_rev`
						WHERE
							`book_sid` = '$book_sid'
							
					";
					
					
					$arrys_result_book_ch_no=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
					$book_ch_no = 0;
					
					if(!empty($arrys_result_book_ch_no)){
						$book_ch_no = $arrys_result_book_ch_no[0]['book_ch_no'];
					}
					

			?>

					<figure class="figure_book">
					<img src="<?php echo $rs_bookpic_root ?>" alt="<?php echo $book_name?>" width="50px" height="60px" />
						<figcaption class="figcaption_book">
							<input type="radio" name="select_book_sid" value="<?php echo $book_sid?>" data-book_ch_no=<?php echo $book_ch_no;?>>
							<FONT COLOR=BLUE><?php echo $book_name?></FONT>
						</figcaption>
					</figure>
				<?php
					endforeach ;
					}
				?>
	</div>
			<BR>
		
		<input class="btn btn-primary" id="mssr_select_book_box_next" type="button" onclick="input_article()" value="下一步"/>
	</form>
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
	<input class="btn btn-primary" id="mssr_select_book_box_next-last" type="button" onclick="select_book()" value="上一步"/>
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
	<input class="btn btn-primary" id="mssr_select_book_box_next-last" type="button" onclick="select_book()" value="上一步"/>
	<input class="btn btn-primary" id="mssr_select_book_box_next" type="button" onclick="input_step_2()" value="下一步"/>

</div>

<!----------block ui div(發表文章-輸入發文內容 step3)---------->
<div id="mssr_article_input_box_3" style="display:none; cursor: default;">
	<input type="image" id="mssr_select_book_box_leave" src="image/xlogo.png" alt="離開" onclick="close_blockui()" width="30" height="30"/>
	<H3 style="color:#660000;font-family:微軟正黑體;"><B>請選擇想發文的類別</B></H3>
	
	<div id="scaffolding_step2_content">
	 
	</div>
	
	<BR>
	<input class="btn btn-primary" id="mssr_select_book_box_next-last" type="button" onclick="input_article()" value="上一步"/>
	<input class="btn btn-primary" id="mssr_select_book_box_next" type="button" onclick="input_step_3()" value="下一步"/>

</div>

<!----------block ui div(發表文章-輸入發文內容 step4)---------->
<div id="mssr_article_input_box_4" style="display:none; cursor: default;">
	<input type="image" id="mssr_select_book_box_leave" src="image/xlogo.png" alt="離開" onclick="close_blockui()" width="30" height="30"/>
	<H3 style="color:#660000;font-family:微軟正黑體;"><B>請選擇想發文的例句</B></H3>
	
	<div id="scaffolding_step3_content" style="position:absolute;	left:2%;	width: 350px">
	 
	</div>
	
	<form id="article_input_form" name="article_input_form" action="mssr_forum_group_discussion_A.php" method="post">
		<input type="hidden" value="無" name="input_article_book_sid" id="input_article_book_sid">
	<div id="input_content" style="position:absolute;top:-20px;	left:50%;	width: 300px">
        <p id="mssr_input_box_title">標題：&nbsp; &nbsp;<input  id="haha" type="text"  name="mssr_input_box_name_title" size="40" maxlength="40"/></p>
		<span id="mssr_input_box_content_text">內容：</span>
		<div id="scaffolding_step4_content">
		</div>
		<textarea name="forum_id" style="display:none" cols="3" rows="8"><?php echo (int)$_GET["forum_id"]?></textarea>
		<input type="hidden" id ="select_input_type" name="type" value="無">
		<input type="hidden" id ="article_refer_code" name="article_refer_code" value="無">
		
		<input type="text" id="action_code" name="action_code" value="g1" style='display:none;'>
		</div>
		<BR>
		<input class="btn btn-primary" id="mssr_select_book_box_next-last" type="button" onclick="mssr_article_input_box_3()" value="上一步"/>
		<input id="mssr_input_box_submit" class="btn btn-primary" type="button" onclick="input_article_check()" value="送出"/>
	</form>
	

	
	
	
</div>







	<!----------block ui div(移至精華區)---------->
	<div id="move_best_article"  style="display:none; cursor: default;padding:15px;">
		<form action="mssr_forum_group_move_best_A.php" method="post">
			<input type="image" id="move_best_ui_leave" src="image/xlogo.png" alt="" width="30" height="30"/>
				<blockquote>
					<p><B>文章移至精華區</B></p>
				</blockquote>
			<input type="hidden" value="<?php echo $forum_id?>" name="forum_id">
			<input id="choose_article_id" type="hidden" value="" name="choose_article_id">
				<?php
				if(!empty($arrys_result_best_cat)){
					foreach($arrys_result_best_cat as $arrys_best_cat_value){
						$cat_name		=	$arrys_best_cat_value['cat_name'];
						$cat_id			=	$arrys_best_cat_value['cat_id'];
					?>
						<div class="radio" style="padding-left:50px;">
						  <label>
							<input type="radio" name="cat_id" id="optionsRadios1" value="<?php echo $cat_id?>">
								<?php echo $cat_name?>
						  </label>
						</div>

				<?php } ?>
				 <input  class="btn btn-default" type="submit" value="送出"/>
				<?php
				}else{
					echo '<h4><B><font color=red>目前還沒有精華區的分類喔！<BR>請先去新增分類</font></B></h4>';
				}
				?>
				<BR>

		</form>
	</div>

<script  type="text/javascript">

	//FUNCTION
	$('.action_code_g2').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='g2',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =0,
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),
			forum_id_2  =0,
			article_id  =0,
			reply_id    =0,
			go_url      =$(this).attr('go_url')
		);
	});

	//FUNCTION
	$('.action_code_g3').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='g3',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =0,
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),
			forum_id_2  =0,
			article_id  =0,
			reply_id    =0,
			go_url      =$(this).attr('go_url')
		);
	});

	//FUNCTION
	$('.action_code_g4').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='g4',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =0,
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),
			forum_id_2  =0,
			article_id  =0,
			reply_id    =0,
			go_url      =$(this).attr('go_url')
		);
	});


	//FUNCTION
	$('.action_code_g11').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='g11',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =0,
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),
			forum_id_2  =0,
			article_id  =0,
			reply_id    =0,
			go_url      =$(this).attr('go_url')
		);
	});



	//FUNCTION
	$('#action_code_g15').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='g15',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =0,
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),
			forum_id_2  =0,
			article_id  =0,
			reply_id    =0,
			go_url      =''
		);

		add_group();
	});

	//FUNCTION
	$('#action_code_g12').click(function(){

		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='g12',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =0,
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),
			forum_id_2  =0,
			article_id  =0,
			reply_id    =0,
			go_url      =$(this).attr('go_url')
		);
	});
	
	
	
	$('.action_code_g19').click(function(){
	
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='g19',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =0,
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),
			forum_id_2  =0,
			article_id  =0,
			reply_id    =0,
			go_url      =$(this).attr('go_url')
		);
	});
	
	
	


	function hide_article(user_id_1, forum_id_1, article_id){

		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='g21',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =user_id_1,
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =forum_id_1,
			forum_id_2  =0,
			article_id  =article_id,
			reply_id    =0,
			go_url      ='article_hide_A.php?article_id='+article_id+'&forum_id='+forum_id
		);
	}
	
	



</script>

	<!----------block ui div(邀請人員) start---------->
	<?php require_once(str_repeat("../",0).'mssr_forum_group_invite.php');?>
	<!----------block ui div(邀請人員) end------------>


	<!---------------------------------側欄------------------------------------->
    <div class="aside">
		<!---------------------------------側欄(群)------------------------------------->
		<?php require_once(str_repeat("../",0).'group_aside.php');?>
		<!---------------------------------側欄(群)------------------------------------->

	</div>



</body>

</html>