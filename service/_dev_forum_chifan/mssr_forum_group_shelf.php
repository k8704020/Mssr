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
                    APP_ROOT.'lib/php/array/code',
           
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
					$sess_uid = (int)$_SESSION["uid"];
					

					
                //-----------------------------------------------
	        	//SQL-討論區書櫃(分頁)
	        	//-----------------------------------------------
					$query_sql="
						SELECT
							`book_sid`,`keyin_cdate`,`create_by`
						FROM
							`mssr_forum_booklist`
						WHERE 1=1
							AND `forum_id` = $forum_id
							AND `book_state` = 1
						ORDER BY
							`keyin_cdate` DESC;
					";
					$arrys_result_shelf=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
					$arrys_result_shelf_con = count($arrys_result_shelf);
                
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
				//學生有閱讀登記過的書
                //-----------------------------------------------
                   $sql="
                        SELECT
							`book_sid`
						FROM
							`mssr_book_borrow_semester`
						WHERE 1=1
							 and `user_id` = $sess_uid
                        ORDER BY
							borrow_sdate desc
                        ";
                   $array_result_borrow_book= 	db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				
			
          

    //---------------------------------------------------
    //檢驗
    //---------------------------------------------------
 


//---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=$arrys_result_shelf_con ;    //資料總筆數
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
		
        //網頁標題
        $title="明日星球-聊書小組興趣書單";
		$site ="mssr_forum_group_shelf.php";
		
        if($numrow!==0){
            $arrys_chunk =array_chunk($arrys_result_shelf,$psize);
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
	<script type="text/javascript" src="js/chosen.jquery.js"></script>

    <link type="text/css" rel="stylesheet" href="css/bootstrap.css">
    <link type="text/css" rel="stylesheet" href="css/mssr_forum.css">
    <link type="text/css" rel="stylesheet" href="../../inc/code.css">
	<link type="text/css" href="css/chosen.css" rel="stylesheet">
  
	  
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
				'page_name' :'mssr_forum_group_shelf.php',
				'page_args' :{
					'forum_id' :<?php echo (int)$forum_id;?>
				}
			}
			var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
		}
		
		$(document).ready(function() {

			$('#open_mssr_forum_group_add_book').click(function() {
					
					//action log g26
					add_action_forum_log(
						process_url ='inc/add_action_forum_log/code.php',
						action_code ='g26',
						action_from ='<?php echo (int)$_SESSION["uid"];?>',
						user_id_1   =0,
						user_id_2   =0,
						book_sid_1  ='',
						book_sid_2  ='',
						forum_id_1  =<?php echo $forum_id;?>,	
						forum_id_2  =0,
						article_id  =0,
						reply_id    =0,
						go_url      =''
					);
					
					//blockUI
					$.blockUI({
						message: $('#mssr_add_book'),
						css:{
							top:  ($(window).height() - 400) /2 + 'px',
							left: ($(window).width() - 700) /2 + 'px',
							textAlign:	'left',
							width: '700px'
						}
					});
				});
				
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

			
			$('#input_leave').click(function() {
					$.unblockUI();
					return false;
			});
			
		});
		
		//刪除書籍
		function book_delete(book_sid, forum_id){
		
			//action log g27
			add_action_forum_log(
				process_url ='inc/add_action_forum_log/code.php',
				action_code ='g27',
				action_from ='<?php echo (int)$_SESSION["uid"];?>',
				user_id_1   =0,
				user_id_2   =0,
				book_sid_1  ='',
				book_sid_2  ='',
				forum_id_1  =<?php echo $forum_id;?>,	
				forum_id_2  =0,
				article_id  =0,
				reply_id    =0,
				go_url      ='mssr_forum_group_del_book_A.php?book_sid='+book_sid+'&forum_id='+forum_id
			);

			
		}
		
		function add_group(){
		//加入小組
			
			var url ='';
			var site = 'mssr_forum_group_shelf.php';
			
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
		
		
		$(function(){
			// 轉換成 chosen 效果
			$(".chzn-select").chosen({
				search_contains: true,
				allow_single_deselect: true
			});
			$('.chzn-select').trigger('chosen:updated');
		});
		
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
				 
				  <li class="active">
					興趣書單
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
							
								申請加入
							</a>
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
			<li>
				<a 	href="javascript:void(0);"
				<?php
					echo 'class="action_code_g2"';
					echo " forum_id_1='{$forum_id}'";
					echo " go_url='mssr_forum_group_discussion.php?forum_id={$forum_id}'";
				?>>
					大家來聊書
				</a>
			</li>
			<li class="active">
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
			
		  
		  
			<?php if($has_group_member){?>
				<div style="float:right">
					<input class="btn btn-primary btn-sm" id="open_mssr_forum_group_add_book" type ="button" value="新增書籍" ></input>
					<BR><P><P>
				</div>
			<?php }?>
		
			<table class="table table-hover table-striped">
				<thead>
						<tr>
							<th style="width:10%"></th>
							<th style="width:50%">書籍名稱</th>
							
							<th style="width:10%">建立者</th>
							<th style="width:15%">建立時間</th>
							<?php if($has_manager){?>
								<th style="width:10%"></th>
							<?php }?>
						</tr>
				</thead>
				<tbody>
					<?php
						if(empty($arrys_result_shelf)){
							echo("現在書櫃沒有書喔，趕快來新增小組書籍吧！");
						}else{
							foreach($arrys_result as $inx=>$arrys_result_shelf):
								$book_sid 			= trim($arrys_result_shelf['book_sid']);

								$arrys_book_info	=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
								$book_name 			= trim($arrys_book_info[0]['book_name']);
								$keyin_cdate = trim($arrys_result_shelf['keyin_cdate']);
								$create_uid = trim($arrys_result_shelf['create_by']);

								//book_name		書名
								if(mb_strlen($book_name)>15){
									$book_name=mb_substr($book_name,0,15)."..";
								}

								//書籍封面處理
								$bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
								if(file_exists($bookpic_root)){
									$rs_bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
								}else{
									$rs_bookpic_root = 'image/book.jpg';

								}
								
								//-----------------------------------------------
								//SQL-姓名
								//-----------------------------------------------
								$sql="
									SELECT
										`name`
									FROM
										`member`
									WHERE
										`uid` = $create_uid
								";
								$arrys_result_name		=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
								$create_name			=$arrys_result_name[0]['name'];
					?>
						
						<tr class="even">
							<td align="center">
								<a 	href="javascript:void(0);"
								<?php
									echo 'class="action_code_g5"';
									echo " book_sid_1='{$book_sid}'";
									echo " forum_id_1='{$forum_id}'";
									echo " go_url='mssr_forum_book_discussion.php?book_sid={$book_sid}'";
								?>>
								
									<img src="<?php echo $rs_bookpic_root ?>" alt="<?php echo $book_name?>"  width="50" height="50"/>
								</a>
								
							</td>
							<td>
								<a 	href="javascript:void(0);"
								<?php
									echo 'class="action_code_g5"';
									echo " book_sid_1='{$book_sid}'";
									echo " forum_id_1='{$forum_id}'";
									echo " go_url='mssr_forum_book_discussion.php?book_sid={$book_sid}'";
								?>>
									<?php echo $book_name?>
								</a>
							</td>
					
							<td><?php echo $create_name?></td>
							<td><?php echo $keyin_cdate?></td>
							<?php if($has_manager){?>
								<td align="center">
									 <input class="btn btn-default btn-sm" type ="button" value="刪除" 
										onclick="if(confirm('確定將此書籍從書單刪除?')){book_delete('<?php echo $book_sid;?>',<?php echo $forum_id;?>);}">
									 </input>
								</td>
							<?php }?>
						</tr>
						
				</tbody>
			  <?php 
				 endforeach ;
			  }
			  ?>
						
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
		
		
		
		<!--=================================add book (block ui)==================================-->
		<div id="mssr_add_book" style="display:none; cursor: default;">
			<form action="mssr_forum_group_add_book_A.php" method="post">
			
				<input type="image" id="input_leave" src="image/xlogo.png" alt="" width="30" height="30"/>
				 
				<div id ="slectbook" style="padding:20px;">
					
					<span style="font-size:20px;">請選擇閱讀過的書籍:</span>
					<select class = "chzn-select" name="mssr_group_add_book_name_title">
					<option value='' >請選擇書籍</option>
					<?php foreach($array_result_borrow_book as $v){?>



					   <?php $book_name = get_book_info($conn='',$v['book_sid'],array('book_name'),$arry_conn_mssr); ?>
					   <option value="<?php echo $v['book_sid'] ?>"> <?php echo $book_name[0]['book_name']; ?></option>





					<?php } ?>
					</select>
					<p><P><P>
					
				
					</p>
					
					
					<textarea name="forum_id"  style="display:none" cols="3" rows="8">
						<?php echo $_GET["forum_id"]?>
					</textarea>
					
					<input id="mssr_group_add_book_submit" type="submit" style="left:50%;" value="送出"/>
				</div>
			</form>
		</div>
		
<script  type="text/javascript">


	//FUNCTION
	$('.action_code_g5').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='g5',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =0,
			user_id_2   =0,
			book_sid_1  =$(this).attr('book_sid_1'),
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),	
			forum_id_2  =0,
			article_id  =0,
			reply_id    =0,
			go_url      =$(this).attr('go_url')
		);
	});
	
	
	
	
	
	
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