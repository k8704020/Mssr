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
				  AND `article_type` = 2
				ORDER BY `order_filter` DESC
			  ";
			  $arrys_result_article_vip=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
			  $arrys_result_article_vip_con = count($arrys_result_article_vip);

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
            //SQL-討論區書籍
            //-----------------------------------------------
			  $sql="
				SELECT
				  `book_sid`
				FROM
				  `mssr_forum_booklist`
				WHERE
				  `forum_id` = $forum_id
			  ";
			  $arrys_result_forum_booklist=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,3),$arry_conn_mssr);
			  $numrow_forum_booklist=count($arrys_result_forum_booklist);

       
		  
          
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
			//SQL-小組精華區分類
			//-----------------------------------------------
			$sql="
					SELECT
						`cat_name`, `cat_id`
					FROM
						`mssr_forum_best_article_category`
					WHERE 1=1
						AND `forum_id` 	= $forum_id	
						AND	`cat_state` = '1'
			";
			$arrys_group_best_cat=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					
			

        

	//---------------------------------------------------
    //檢驗
    //---------------------------------------------------
    //echo "<pre>";
    //print_r($arrys_group_best_cat);
    //echo "</pre>";
    //die();


	//---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=$arrys_result_article_vip_con;    //資料總筆數
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
        $title="明日星球-聊書小組精華區";
		$site ="mssr_forum_group_discussion_vip.php";
		
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
            $arrys_chunk =array_chunk($arrys_result_article_vip,$psize);
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
	<script type="text/javascript" src="inc/add_action_forum_log/code.js"></script>
 

    <link type="text/css" rel="stylesheet" href="css/bootstrap.css">
    <link type="text/css" rel="stylesheet" href="css/mssr_forum.css">
    <link type="text/css" rel="stylesheet" href="../../inc/code.css">
  

  
  
	  
	<script>
		var psize=<?php echo (int)$psize;?>;
		var pinx =<?php echo (int)$pinx;?>;
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
				'page_name' :'mssr_forum_group_discussion_vip.php',
				'page_args' :{
					'forum_id' :<?php echo (int)$forum_id;?>
				}
			}
			var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
		}
	 

	$(document).ready(function() {

		$('#management_vip_type_button').click(function() {
		
			add_action_forum_log(
				process_url ='inc/add_action_forum_log/code.php',
				action_code ='g23',
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
				
				message: $('#vip_type'),
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
			
			$('#mssr_input_box_submit').click(function() {
				
				$.unblockUI();
			});
		});

		function add_group(){
		//加入小組
			
			var url ='';
			var site = 'mssr_forum_group_discussion_vip.php';
			
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
		
		//精華區分類增加一筆data
		function add_new_data() {
		
		 //先取得目前的row數
			 var num = document.getElementById("type_table").rows.length;
			 
			 //建立新的tr 因為是從0開始算 所以目前的row數剛好為目前要增加的第幾個tr
			 if(num>6){
				alert("自訂分類最多六項");
				return false;
			 }
			 
			 var Tr = document.getElementById("type_table").insertRow(num);
			 //建立新的td 而Tr.cells.length就是這個tr目前的td數
			 Td = Tr.insertCell(Tr.cells.length);
			 //而這個就是要填入td中的innerHTML
			 Td.innerHTML= num;
			 //這裡也可以用不同的變數來辨別不同的td 
			 Td = Tr.insertCell(Tr.cells.length);
			
			 Td.innerHTML='<input name="new_type_name[]" class="btn btn-default btn-sm" type="text" size="24">';
			 
			
		
		}
		
		//精華區分類編輯(edit)
		function edit_type(i,type_name) {
		
			var action_type = "edit";
			
			var cat_id =jQuery("#edit_type_button_"+i).data('cat_id');
			var type_name =document.getElementById('best_type_name_'+i).value;
			var URLs = "mssr_forum_group_manage_typeA.php";
			$.ajax({
					url: URLs,
					data: "action_type="+action_type+"&cat_id="+cat_id+"&type_name="+type_name,
					type:"POST",
					dataType:'text',

					success: function(msg){
						alert("類別名稱已修改成功");
					},

					 error:function(xhr, ajaxOptions, thrownError){ 
						alert(xhr.status); 
						alert(thrownError); 
					 }
				});
				
			
		}
		
		//精華區分類編輯(del)
		function del_type(i) {
			var action_type = "del";
			var cat_id = jQuery("#del_type_button_"+i).data('cat_id');
			var URLs = "mssr_forum_group_manage_typeA.php";
			
			$.ajax({
					url: URLs,
					data: "action_type="+action_type+"&cat_id="+cat_id,
					type:"POST",
					dataType:'text',

					success: function(msg){
						$("#id-" + i).remove();  
						//重新編號
						var otype_table=document.getElementById("type_table");
						var rows_num = otype_table.rows.length;
						var new_cell_num=1;
						for(var j=1;j<rows_num;j++){
							var otype_table_row=otype_table.rows[j];
							var otype_table_cell_1=otype_table_row.cells[0];
							otype_table_cell_1.innerHTML=new_cell_num;
							new_cell_num++;
						}
						alert("此類別名稱已刪除成功");
					},
					 error:function(xhr, ajaxOptions, thrownError){ 
						alert(xhr.status); 
						alert(thrownError); 
					 }
				});
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
				精華區
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
						  <a class="btn" type="button" onclick="add_group()">申請加入</a>
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

			<button class="btn btn-default" type="button" 
			 <?php
				echo ' id="action_code_g2"';
				echo " forum_id_1='{$forum_id}'";
				echo " go_url='mssr_forum_group_discussion.php?forum_id={$forum_id}'";
			?>>
				一般討論區</button>
			 <button class="btn btn-default" type="button" disabled="disabled" onclick="javascript:location.href='mssr_forum_group_discussion_VIP.php'" > 精華區</button> 
		  </div>
		  
		  
		 <?php if($has_manager){?>
			 <div style="float:right">
				<input class="btn btn-primary btn-sm" type ="button" id="management_vip_type_button" value="管理精華區分類"></input>
			  </div>
		  <?php }?>
		 <P>
		 
		<?php
		
			if(empty($arrys_result_article_vip)){
				echo("<h4 align=center>目前精華區沒有文章喔！</h4>");
				
			}else{
		?>
			<table class="table table-hover table-striped">
				<thead>
					<tr>
						<th style="width:12%">分類</th>
						<th style="width:10%">討論書本</th>
						<th style="width:25%">標題</th>
						<th style="width:10%">姓名</th>
						<th style="width:15%">時間</th>
						<th style="width:5%">讚</th>
						<th style="width:7%">回應</th>
							<?php if($has_manager){?>
								<th style="width:10%"></th>
							<?php }?>
					</tr>
				</thead>
				<tbody>
		
		<?php
			
				foreach($arrys_result as $inx=>$arry_result_article_vip):
					$cat_id 				= $arry_result_article_vip['cat_id'];
					$user_id 				= $arry_result_article_vip['user_id'];
					$article_id 			= $arry_result_article_vip['article_id'];
					$article_title 			= $arry_result_article_vip['article_title'];
					$article_content		= $arry_result_article_vip['article_content'];
					$keyin_cdate 			= $arry_result_article_vip['keyin_cdate'];
					$article_like_cno 		= $arry_result_article_vip['article_like_cno'];

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
					$arrys_result_replyname=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
					$reply_name			=$arrys_result_replyname[0]['name'];
					
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
					//cat_name 精華區分類名稱
					//-----------------------------------------------
					$sql="
						SELECT
							`cat_name`
						FROM
							`mssr_forum_best_article_category`
						INNER JOIN `mssr_forum_best_article_category_rev` ON
							`mssr_forum_best_article_category`.`cat_id` = 
							`mssr_forum_best_article_category_rev`.`cat_id`
						WHERE
							`mssr_forum_best_article_category_rev`.`article_id` = $article_id 
							AND `mssr_forum_best_article_category_rev`.`cat_state` = 1						
							
					";

					
					$arrys_result_cat_name=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$cat_name =$arrys_result_cat_name[0]['cat_name'];
					
					
				

		?>
						 <tr class="even">
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
								<a href="mssr_forum_group_reply.php?article_id=<?php echo $article_id;?>"><?php echo $article_title?></a>
							</td>
							<td><?php echo $reply_name?></td>
							<td><?php echo $keyin_cdate?></td>
							<td><?php echo $numrow_like?></td>
							<td><?php echo $numrow_replynum?></td>
							<?php if($has_manager){?>
								<td align="center">
								   <input class="btn btn-default btn-sm" name ="output_article_vip" type ="button" value="移出精華區" 
								   onclick="if(confirm('確定將此討論文移出精華區?')){out_best_article(<?php echo $user_id;?>,<?php echo $forum_id;?>,<?php echo $article_id;?>)}">
								   </input>
								   
								   <input class="btn btn-default btn-sm" type ="button" value="隱藏" 
								   onclick="if(confirm('確定隱藏此篇討論文?')){hide_article(<?php echo $user_id;?>,<?php echo $forum_id;?>,<?php echo $article_id;?>)}">
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
		
		

	<!--==========================管理精華區分類(block ui)==================================-->
	<div id="vip_type" style="display:none; cursor: default;padding:15px;">
		<form name="best_type_form" action="mssr_forum_group_best_cat_A.php" method="post">
	  
			<input type="hidden" value="<?php echo $forum_id;?>" name="forum_id">
			<input type="image" id="input_leave" src="image/xlogo.png" alt="" width="30" height="30" onclick="leaveui();"/>
		   
			<P>管理精華區文章分類</P>
			
			
				 <table id="type_table" width="500px" align="center" border="1px">
						<tr>
							<th style="width:40%">項目</th>
							<th style="width:60%">分類名稱</th>
						   
						</tr> 
						<?php 
						$i =0;
							if(!empty($arrys_group_best_cat)){
							
								foreach($arrys_group_best_cat as $arrys_group_best_cat_value){
						?>		
						
						
						 <tr id="id-<?php echo $i?>">
							<td><?php echo $i+1?></td>
						   
						   
							   <td>
							   
									<input id = "best_type_name_<?php echo $i?>" name="new_type_name[]" class="btn btn-default btn-sm" type="text" size="24" value="<?php echo $arrys_group_best_cat_value['cat_name'];?>"> 	
									<input class="btn btn-default btn-sm" type ="button" id="edit_type_button_<?php echo $i?>" name="edit" data-cat_id ="<?php echo $arrys_group_best_cat_value['cat_id'];?>" onclick="if (confirm('確定是否修改此文分類名稱?')) edit_type(<?php echo $i?>)" value="修改"></input>
									
									
									<input class="btn btn-default btn-sm" type ="button" id="del_type_button_<?php echo $i?>" name="edit" data-cat_id ="<?php echo $arrys_group_best_cat_value['cat_id'];?>" onclick="if (confirm('確定刪除此篇?')) del_type(<?php echo $i?>)" value="刪除"></input>
								</td> 
							
							</td>
							
						<?php $i++;}
						
						
						?>
							
					<?php }else{?>
						 <tr id="id-0">
							<td>1</td>
							   <td>
									<input name="new_type_name[]" class="btn btn-default btn-sm" type="text" size="24"> 	
								</td> 
							</td>
					<?php }?>
					
			</table>
		 <BR>
			<input type="button" class="btn btn-default btn-sm" value="增加" onclick="add_new_data()"> 
			<input id="mssr_group_add_book_submit" class="btn btn-default btn-sm" type="submit" value="送出"/>
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
	$('#action_code_g2').click(function(){
	
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
	
	function out_best_article(user_id_1, forum_id_1, article_id){
		
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='g24',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =user_id_1,
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =forum_id_1,	
			forum_id_2  =0,
			article_id  =article_id,
			reply_id    =0,
			go_url      ='article_vip_A.php?article_id='+article_id+'&forum_id='+forum_id_1+'&type=output'
	
		);
	}
	
	function hide_article(user_id_1, forum_id_1, article_id){
		
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='g25',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =user_id_1,
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =forum_id_1,	
			forum_id_2  =0,
			article_id  =article_id,
			reply_id    =0,
			go_url      ='article_hide_A.php?article_id='+article_id+'&forum_id='+forum_id_1
	
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