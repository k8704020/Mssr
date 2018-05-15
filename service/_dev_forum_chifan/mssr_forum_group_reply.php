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
        //-----------------------------------------------
			$article_id = (int)$_GET["article_id"];
			$sess_uid   = (int)$_SESSION["uid"];

				//-----------------------------------------------
	        	//SQL-留言資訊(mssr_reply_box_frame)
	        	//-----------------------------------------------
					$sql="
						SELECT
							`article_title`, `article_content`, `keyin_cdate`, `user_id`, `article_state`, `cat_id`
						FROM
							`mssr_forum_article`
						WHERE 1=1
							AND `article_id` = $article_id
							AND `article_state` LIKE '%正常%'
					";
					$arrys_result_reply_box=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
					$numrow_reply_box=count($arrys_result_reply_box);

				//-----------------------------------------------
	        	//SQL-mssr_reply_box_frame-名子(發文者)
	        	//-----------------------------------------------
					for($i=0;$i<$numrow_reply_box;$i++){
						$user_id = $arrys_result_reply_box[$i]['user_id'];
						$sql="
							SELECT
								`name`
							FROM
								`member`
							WHERE
								`uid` = $user_id
						";
					}
					$arrys_result_reply_name=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
					$numrow_reply_name=count($arrys_result_reply_name);


				//-----------------------------------------------
	        	//SQL-回覆資訊(分頁)(mssr_comment_box_frame)
	        	//-----------------------------------------------
					$query_sql="
						SELECT
							`user_id`, `reply_content`, `keyin_cdate`, `reply_id`, `reply_state`
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
	        	//SQL-mssr_comment_box_frame-名子(回覆)
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
				//SQL-查forum_id
	        	//-----------------------------------------------
				
					$sql="
						SELECT
							`forum_id`
						FROM
							`mssr_article_forum_rev`
						WHERE
							`article_id`=$article_id
					";
					$arrys_result_forum_id=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
					$forum_id = $arrys_result_forum_id[0]["forum_id"];
					
				//-----------------------------------------------
	        	//SQL-是否是板主
	        	//-----------------------------------------------
				
					$sql="
						SELECT
							`forum_id`, `user_id`, `user_type`
						FROM
							`mssr_user_forum`
						WHERE 1=1
							ANd `forum_id` = $forum_id
							AND `user_id` = $sess_uid
							AND `user_type` LIKE '%一般版主%'
					";
					$arrys_admin=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_arrys_admin=count($arrys_admin);
					

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
					$query_sql="
						SELECT
							`book_sid`
						FROM
							`mssr_forum_booklist`
						WHERE 1=1
							AND `forum_id` = $forum_id
							AND `book_state` = 1;
					";
					$arrys_result_forum_booklist=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
					$numrow_forum_booklist = count($arrys_result_forum_booklist);
					
					

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
				//SQL-我的好友
				//-----------------------------------------------
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
        //echo $numrow."<br/>";

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------
		//網頁標題
        $title="明日星球-聊書小組一般討論區";
		
		$site ="mssr_forum_group_discussion.php";
		//發文者UID
		$user_id_1 =$arrys_result_reply_box[0]['user_id'];
		$book_sid_1        ="";
		
		$cat_id =$arrys_result_reply_box[0]['cat_id'];
		
		
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
            $arrys_chunk =array_chunk($arrys_result_comment,$psize);
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
	<script type="text/javascript" src="js/mssr_forum/report_code.js"></script>
	<script type="text/javascript" src="js/mssr_forum/like_code.js"></script>
	<script type="text/javascript" src="js/mssr_forum/scaffolding_reply_code.js"></script>
	<script type="text/javascript" src="inc/add_action_forum_log/code.js"></script>

	
    <link type="text/css" rel="stylesheet" href="css/bootstrap.css">
    <link type="text/css" rel="stylesheet" href="css/mssr_forum.css">
    <link type="text/css" rel="stylesheet" href="../../inc/code.css">
  

	<script>
		var psize=<?php echo (int)$psize;?>;
		var pinx =<?php echo (int)$pinx;?>;
		var article_id='<?php echo addslashes($article_id);?>';
	

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
				'page_name' :'mssr_forum_group_reply.php',
				'page_args' :{
					'article_id' :<?php echo (int)$article_id;?>
				}
			}
			var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
		}
		


		function mouse_over(obj){
		//滑鼠移入
			obj.style.cursor='pointer';
		}
		
		
		
		function go_back(){
			window.history.back();

		}
		
		//隱藏回覆按鈕
		function reply_disable_close(obj){
		
			add_action_forum_log(
				process_url ='inc/add_action_forum_log/code.php',
				action_code ='ga14',
				action_from ='<?php echo (int)$_SESSION["uid"];?>',
				user_id_1   =<?php echo $user_id_1;?>,
				user_id_2   =0,
				book_sid_1  ='',
				book_sid_2  ='',
				forum_id_1  =<?php echo $forum_id;?>,
				forum_id_2  =0,
				article_id  =<?php echo $article_id;?>,
				reply_id    =0,
				go_url      =''
			);
		
			var reply_content	  			=document.getElementsByName('mssr_comment_input_name_content[]');
			var mssr_forum_group_reply		=document.getElementById('mssr_forum_group_reply');
			
			
			// if(trim(mssr_comment_input_content.value)===''){
				// alert("請輸入回復！");	
				// return false; 
			// }
			
			for(var i = 0;i<reply_content.length;i++){
				
					if(trim(reply_content[i].value) ==""){
						alert("請確認內容都有輸入");
						return false;
						
					}
				}
			obj.disabled=true;
			mssr_forum_group_reply.submit();
		}
		
		$(document).ready(function() {

			//分頁列
			
			
			
			//block ui
			$('#open_invite_box').click(function() {
				add_action_forum_log(
					process_url ='inc/add_action_forum_log/code.php',
					action_code ='ga16',
					action_from ='<?php echo (int)$_SESSION["uid"];?>',
					user_id_1   =<?php echo $user_id_1;?>,
					user_id_2   =0,
					book_sid_1  ='',
					book_sid_2  ='',
					forum_id_1  =<?php echo $forum_id;?>,
					forum_id_2  =0,
					article_id  =0,
					reply_id    =0,
					go_url      =''
				);
				
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
		
		//選擇書籍
		function select_book(){
			$.blockUI({
					message: $('#mssr_select_book_box'),
					
					css:{
						top:  ($(window).height() - 400) /2 + 'px',
						left: ($(window).width() - 700) /2 + 'px',
						textAlign:	'left',
						width: '700px'
				
					}
				});
			$('#mssr_select_book_box_leave').click(function() {
					$.unblockUI();
					return false;
			});
		}
		
		function select_book_submit(){

			var select_book_name = $('input[name=select_book_sid]:checked').data('name');
			var select_book_sid =  $('input[name=select_book_sid]:checked').val();
			
			document.getElementById('reply_select_book_sid').value = select_book_sid;
			document.getElementById('mssr_comment_input_select_book_name').innerHTML = select_book_name;
			
			$.unblockUI();
			return false;
			
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
					
					echo 'class="action_code_ga15"';
					echo " user_id_1='{$user_id_1}'";
					echo " forum_id_1='{$forum_id}'";
					echo " article_id='{$article_id}'";
					echo " go_url='mssr_forum_group_index.php?forum_id={$forum_id}'";
				?>>
					首頁
				</a>
				
			</li>
			<li class="active">
				<a 	href="javascript:void(0);"
				<?php
					echo 'class="action_code_ga2"';
					echo " user_id_1='{$user_id_1}'";
					echo " forum_id_1='{$forum_id}'";
					echo " article_id='{$article_id}'";
					echo " go_url='mssr_forum_group_discussion.php?forum_id={$forum_id}'";
				?>>
					大家來聊書
				</a>
			</li>
			<li>
				<a 	href="javascript:void(0);"
				<?php
					echo 'class="action_code_ga3"';
					echo " user_id_1='{$user_id_1}'";
					echo " forum_id_1='{$forum_id}'";
					echo " article_id='{$article_id}'";
					echo " go_url='mssr_forum_group_shelf.php?forum_id={$forum_id}'";
				?>>
					興趣書單
				</a>
			</li>
			<li>
				<a 	href="javascript:void(0);"
				<?php
					echo 'class="action_code_ga4"';
					echo " user_id_1='{$user_id_1}'";
					echo " forum_id_1='{$forum_id}'";
					echo " article_id='{$article_id}'";
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
	  
		<div class="btn-group" >
			<button class="btn btn-default" type="button" onclick="go_back();">回上一頁</button>
		</div>
		
		<?php if($has_group_member){ ?>
				<div style="float:right">
					<input class="btn btn-primary btn-sm" id="open_invite_box" type ="button" value="邀請好友一同討論"></input>
				</div>
		<?php } ?>
	  
		<P>
	 
	<!----------發文列表---------->
	<div id="mssr_reply_box" >

        <!----------留言資訊---------->
        <div id="mssr_reply_box_frame">
            <?php
            for($i=0; $i<$numrow_reply_name; $i++){
                $name 	= $arrys_result_reply_name[$i]['name'];?>
                <figcaption id="mssr_reply_box_name">
					<a href="javascript:void(0);"
					<?php
						echo 'class="action_code_ga7"';
						echo " user_id_1='{$user_id_1}'";
						echo " forum_id_1='{$forum_id}'";
						echo " article_id='{$article_id}'";
						echo " go_url='mssr_forum_people_index.php?user_id={$user_id}'";
						?>>	
					<?php echo $name?>
					</a>
				</figcaption>
            <?php }?>
            <?php
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
			//SQL-reply_mark_book
			//-----------------------------------------------
			$sql="
				SELECT
					`book_sid`
				FROM
					`mssr_forum_article_mark_rev`
				WHERE
					`article_id` = '$article_id'
				";
				$arrys_result_article_mark_book=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$book_sid_1        =trim($arrys_result_article_mark_book[0]['book_sid']);

			?>
			
				<?php 
					//article mark book
					if(!empty($arrys_result_article_mark_book)):
						$select_book_sid        =trim($arrys_result_article_mark_book[0]['book_sid']);
						$arrys_select_book_info	=get_book_info($conn_mssr,$select_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
						$select_book_name       =trim($arrys_select_book_info[0]['book_name']);

						//book_name		書名
						if(mb_strlen($select_book_name)>15){
							$select_book_name=mb_substr($select_book_name,0,10)."..";
						}

						//書籍封面處理
						$bookpic_root = '../../info/book/'.$select_book_sid.'/img/front/simg/1.jpg';
						if(file_exists($bookpic_root)){
							$rs_bookpic_root = '../../info/book/'.$select_book_sid.'/img/front/simg/1.jpg';
						}else{
							$rs_bookpic_root = 'image/book.jpg';
						}						
				?>
					<div id="mssr_article_box_select_book">
						討論書籍：
							<a 	href="javascript:void(0);"
								<?php
									echo 'class="action_code_ga23"';
									echo " user_id_1='{$user_id_1}'";
									echo " book_sid_1='{$select_book_sid}'";
									echo " forum_id_1='{$forum_id}'";
									echo " article_id='{$article_id}'";
									echo " go_url='mssr_forum_book_discussion.php?book_sid={$select_book_sid}'";
								?>>
				
							<img src="<?php echo $rs_bookpic_root; ?>" alt="<?php echo $select_book_name;?>" 
							width="20px" height="25px" /><?php echo $select_book_name;?>
						</a>		
					</div>
				<?php endif;?>
				
				
				
            <?php
            for($i=0; $i<$numrow_reply_box; $i++){
                $user_id 					= $arrys_result_reply_box[$i]['user_id'];
                $article_title 				= $arrys_result_reply_box[$i]['article_title'];
                $article_content 			= $arrys_result_reply_box[$i]['article_content'];
                $keyin_cdate 				= $arrys_result_reply_box[$i]['keyin_cdate'];

				//學生照片
				$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
				if($get_user_info[0]['sex']==1){?>
					<a href="javascript:void(0);"
						<?php
						echo 'class="action_code_ga7"';
						echo " user_id_1='{$user_id_1}'";
						echo " forum_id_1='{$forum_id}'";
						echo " article_id='{$article_id}'";
						echo " go_url='mssr_forum_people_index.php?user_id={$user_id}'";
						?>>					
						<img id="mssr_reply_box_pic" src="image/boy.jpg" alt="" width="100" height="100"/>
					</a>
				<?php }else{?>
					<a href="javascript:void(0);">
						<a href="javascript:void(0);"
							<?php
							echo 'class="action_code_ga7"';
							echo " user_id_1='{$user_id_1}'";
							echo " forum_id_1='{$forum_id}'";
							echo " article_id='{$article_id}'";
							echo " go_url='mssr_forum_people_index.php?user_id={$user_id}'";
						?>>	
						<img id="mssr_reply_box_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
					</a>
				<?php }?>

                <div id="mssr_reply_box_title" style="color:#660000;">標題：<?php echo filter($article_title)?></div>
                <div id="mssr_reply_box_content"><?php echo filter($article_content)?></div>
                <div id="mssr_reply_box_time"><?php echo $keyin_cdate?>(#<?php echo $_GET["article_id"];?>)</div>
				
				<!--發文按讚pic-->	
                <img id="mssr_reply_box_likepic" src="image/like.png" alt="" width="10" height="10"
					onmouseover="mouse_over(this);void(0);"
					<?php
							
							echo ' class="action_code_ga12"';
							echo " user_id_1='{$user_id}'";
							echo " forum_id_1='{$forum_id}'";
							echo " article_id='{$article_id}'";
							echo " con_like='{$numrow_like}'";
					?>>
				<!--發文按讚cnt-->	
				<div id="mssr_reply_box_likecnt">
					<p id="mssr_reply_box_likecnt-<?php echo $_GET["article_id"];?>"><?php echo $numrow_like?></p>
				</div>

				<!--回覆數量-->
                <img id="mssr_reply_box_replypic" src="image/icon.png" alt="" width="10" height="10"/>
                <p id="mssr_reply_box_replycnt"><?php echo $numrow?></p>
				
			

               
            <?php }?>
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
			//SQL-reply_mark_book
			//-----------------------------------------------
				$sql="
				SELECT
					`book_sid`
				FROM
					`mssr_forum_reply_mark_rev`
				WHERE
					`reply_id` = '$reply_id'
				";
				$arrys_result_reply_mark_book=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				
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
					//reply mark book
					if(!empty($arrys_result_reply_mark_book)):
						$select_book_sid        =trim($arrys_result_reply_mark_book[0]['book_sid']);
						$arrys_select_book_info	=get_book_info($conn_mssr,$select_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
						$select_book_name       =trim($arrys_select_book_info[0]['book_name']);

						//book_name		書名
						if(mb_strlen($select_book_name)>15){
							$select_book_name=mb_substr($select_book_name,0,10)."..";
						}

						//書籍封面處理
						$bookpic_root = '../../info/book/'.$select_book_sid.'/img/front/simg/1.jpg';
						if(file_exists($bookpic_root)){
							$rs_bookpic_root = '../../info/book/'.$select_book_sid.'/img/front/simg/1.jpg';
						}else{
							$rs_bookpic_root = 'image/book.jpg';
						}						
				?>
					<div id="mssr_comment_box_select_book" style="color:#660000;">
					回覆相關書籍：
						<a 	href="javascript:void(0);"
								<?php
									echo 'class="action_code_ga24"';
									echo " user_id_1='{$user_id_1}'";
									echo " user_id_1='{$user_id}'";
									echo " book_sid_1='{$book_sid_1}'";
									echo " book_sid_2='{$select_book_sid}'";
									echo " forum_id_1='{$forum_id}'";
									echo " article_id='{$article_id}'";
									echo " reply_id='{$reply_id}'";
									echo " go_url='mssr_forum_book_discussion.php?book_sid={$select_book_sid}'";
								?>>
							<img src="<?php echo $rs_bookpic_root; ?>" alt="<?php echo $select_book_name;?>" 
								width="20px" height="25px" /><?php echo $select_book_name;?>
						</a>
					</div>
					
				<?php endif;?>
			
            	<?php
				//學生照片
				$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
				if($get_user_info[0]['sex']==1){?>
					<a href="javascript:void(0);"
						<?php
							echo 'class="action_code_ga8"';
							echo " user_id_1='{$user_id_1}'";
							echo " user_id_2='{$user_id}'";
							echo " forum_id_1='{$forum_id}'";
							echo " article_id='{$article_id}'";
							echo " reply_id='{$reply_id}'";
							echo " go_url='mssr_forum_people_index.php?user_id={$user_id}'";
						?>>	
						<img id="mssr_comment_box_pic" src="image/boy.jpg" alt="" width="100" height="100"/>
					</a>
				<?php }else{?>
						<a href="javascript:void(0);"
							<?php
							echo 'class="action_code_ga8"';
							echo " user_id_1='{$user_id_1}'";
							echo " user_id_2='{$user_id}'";
							echo " forum_id_1='{$forum_id}'";
							echo " article_id='{$article_id}'";
							echo " reply_id='{$reply_id}'";
							echo " go_url='mssr_forum_people_index.php?user_id={$user_id}'";
						?>>	
						<img id="mssr_comment_box_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
					</a>
				<?php }?>
				
                <figcaption id="mssr_comment_box_name">
					<a href="javascript:void(0);"
						<?php
							echo 'class="action_code_ga8"';
							echo " user_id_1='{$user_id_1}'";
							echo " user_id_2='{$user_id}'";
							echo " forum_id_1='{$forum_id}'";
							echo " article_id='{$article_id}'";
							echo " reply_id='{$reply_id}'";
							echo " go_url='mssr_forum_people_index.php?user_id={$user_id}'";
						?>>	
						<?php echo $arrys_result_username[0]['name']?>
					</a>
				</figcaption>

                <p id="mssr_comment_box_content"><?php echo filter($reply_content)?></p>
                <p id="mssr_comment_box_time"><?php echo $keyin_cdate?>(#<?php echo $reply_id;?>)</p>


                <img id="mssr_comment_box_likepic" src="image/like.png" alt="" width="10" height="10"
					onmouseover="mouse_over(this);void(0);"
					<?php
							$user_id_2 =$arrys_result_reply_box[0]['user_id'];
							echo ' class="action_code_ga13"';
							echo " user_id_1='{$user_id}'";
							echo " user_id_2='{$user_id_2}'";
							echo " forum_id_1='{$forum_id}'";
							echo " article_id='{$article_id}'";
							echo " reply_id='{$reply_id}'";
							echo " con_like='{$numrow_like_reply}'";
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
		 endforeach;
		 
		}
		?>
		
            <!--回覆留言-->
			<?php 
				if($has_group_member){
			?>
            
                <div id="mssr_comment_input_frame">
					<form id="mssr_forum_group_reply" action="mssr_forum_group_reply_A.php" method="post">
                	<?php
						//學生照片
						$get_user_info=get_user_info($conn_user,$_SESSION["uid"],$array_filter=array('sex','name'),$arry_conn_user);
						if($get_user_info[0]['sex']==1){?>
							<img id="mssr_comment_box_pic" src="image/boy.jpg" alt="" width="100" height="100"/>
						<?php }else{?>
							<img id="mssr_comment_box_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
					<?php }?>

                    <figcaption id="mssr_comment_input_name"><?php echo $get_user_info[0]['name']?></figcaption>
					
					<input id= "mssr_comment_input_select_book" class="btn btn-default btn-sm" type ="button" 
						value="選擇書籍" onclick="select_book()"></input>
					<font id="mssr_comment_input_select_book_text" style="color:#467500;">回覆相關書籍：</font>
					<B><font id="mssr_comment_input_select_book_name" style="color:#467500; font-size:17px;"></font></B>
					
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
					
					
					<input id="reply_select_book_sid" name="reply_select_book_sid" value="" type="hidden">
					
					<div id="mssr_comment_input_content">
						<font color=red style="font-size:28px;">請選擇上放要輸入的內容</font>
					</div>
					
                    <textarea name="article_id"  style="display:none" cols="2" rows="8"><?php echo $_GET["article_id"]?></textarea>
                    <textarea name="book_sid"  style="display:none" cols="2" rows="8"><?php echo $_GET["book_sid"]?></textarea>
					<textarea name="cat_id"  style="display:none" cols="2" rows="8"><?php echo (int)$cat_id;?></textarea>
					
                    <input  class="btn btn-primary  btn-sm" id="mssr_comment_input_submit" 
						onclick="reply_disable_close(this);" type="button" value="送出"/>
					</form>
				</div>
			<?php	
				}else{
					echo '<h3 align=center><font color=red>請加入小組，才能進行回覆喔！</font></h3>';
				
			}
			
			?>
            
			
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


<!--========================邀請人員block ui=====================================--> 
<div id="invite_box"  style="display:none; cursor: default;overflow: auto;">
    <form action="mssr_forum_request_discussion_A.php" method="post">
        <input type="image" id="invite_box_leave" src="image/xlogo.png" alt="" width="30" height="30" onclick="leaveui();"/>
			<blockquote>
			<p><B>邀請好友一同討論</B></p>
			  
			</blockquote>
			  
			<input id="" type="hidden" value="<?php echo $article_id ?>" name="article_id">
			<input id="" type="hidden" value="mssr_forum_group_reply.php" name="site">
			<input id="" type="hidden" value="1" name="type">
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
						  AND   `mssr_user_request`.`request_state`				=1				
					
					;";
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
					<figcaption class="figcaption_people">
					  <input type="checkbox" name="friend_uid[]" value="<?php echo $rs_friend_id;?>" <?php echo $has_disabled;?>> 
						
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

	
	



	<!------------block ui div(回覆-選擇書籍)------------>	
	<div id="mssr_select_book_box" style="display:none; cursor: default;overflow: auto;">
		<input type="image" id="mssr_select_book_box_leave" src="image/xlogo.png" alt="" width="30" height="30"/>
		
			<H4 style="color:#660000;font-family:微軟正黑體;text-align:center;"><B>請選擇回覆相關書籍</B></H4>
		
		<form id="select_book_form" name="select_book_form">
		
		<?php
			if(empty($arrys_result_forum_booklist)){
				echo("聊書小組的興趣書單，還沒有新增書籍");
			}else{
			
			foreach($arrys_result_forum_booklist as $arrys_result_forum_booklist):
					$book_sid 			= trim($arrys_result_forum_booklist['book_sid']);

					$arrys_book_info	=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
					$book_name 			= trim($arrys_book_info[0]['book_name']);

					//book_name		書名
						if(mb_strlen($book_name)>10){
							$book_name=mb_substr($book_name,0,10)."..";
						}
					//書籍封面處理
						$bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
						if(file_exists($bookpic_root)){
							$rs_bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
						}else{
							$rs_bookpic_root = 'image/book.jpg';
						}
					
			?>
			

					<figure class="figure_book">
					<a href="mssr_forum_book_discussion.php?book_sid=<?php echo $book_sid?>"><img src="<?php echo $rs_bookpic_root ?>" alt="<?php echo $book_name?>" width="50px" height="60px" /></a>
						<figcaption class="figcaption_book"><a href="mssr_forum_book_discussion.php?book_sid=<?php echo $book_sid?>">
							<input type="radio" name="select_book_sid" value="<?php echo $book_sid?>" data-name="<?php echo $book_name?>">
							<?php echo $book_name?></a>
						</figcaption>
					</figure>
				<?php 
					endforeach ;
					}
				?>
			
				<BR>
			<input class="btn btn-primary" id="mssr_select_book_box_next" type="button" onclick="select_book_submit()" value="下一步"/>
		</form>
	</div>
	
<script  type="text/javascript">

	//FUNCTION
	$('.action_code_ga12').click(function(){


		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='ga12',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =$(this).attr('user_id_1'),
			user_id_2   =0,
			book_sid_1  =0,
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),	
			forum_id_2  =0,
			article_id  =$(this).attr('article_id'),
			reply_id    =0,
			go_url      =''
			
		);
		like('article',	<?php echo (int)$_SESSION["uid"];?>, $(this).attr('article_id'), $(this).attr('con_like'));
	});
	
	
	
	$('.action_code_ga13').click(function(){
	
	
		//呼叫
		
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='ga13',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =$(this).attr('user_id_1'),
			user_id_2   =$(this).attr('user_id_2'),
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),	
			forum_id_2  =0,
			article_id  =$(this).attr('article_id'),
			reply_id    =$(this).attr('reply_id'),
			go_url      =''
			
		);
		like('reply',	<?php echo (int)$_SESSION["uid"];?>, $(this).attr('reply_id'), $(this).attr('con_like'));
	});
	
	//FUNCTION
	$('.action_code_ga15').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='ga15',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =$(this).attr('user_id_1'),
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),	
			forum_id_2  =0,
			article_id  =$(this).attr('article_id'),
			reply_id    =0,
			go_url      =$(this).attr('go_url')
		);
	});
	
	
	//FUNCTION
	$('.action_code_ga2').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='ga2',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =$(this).attr('user_id_1'),
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),	
			forum_id_2  =0,
			article_id  =$(this).attr('article_id'),
			reply_id    =0,
			go_url      =$(this).attr('go_url')
		);
	});
	
	//FUNCTION
	$('.action_code_ga3').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='ga3',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =$(this).attr('user_id_1'),
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),	
			forum_id_2  =0,
			article_id  =$(this).attr('article_id'),
			reply_id    =0,
			go_url      =$(this).attr('go_url')
		);
	});
	
	//FUNCTION
	$('.action_code_ga4').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='ga4',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =$(this).attr('user_id_1'),
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),	
			forum_id_2  =0,
			article_id  =$(this).attr('article_id'),
			reply_id    =0,
			go_url      =$(this).attr('go_url')
		);
	});
	
	//FUNCTION
	$('.action_code_ga7').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='ga7',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =$(this).attr('user_id_1'),
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),	
			forum_id_2  =0,
			article_id  =$(this).attr('article_id'),
			reply_id    =0,
			go_url      =$(this).attr('go_url')
		);
	});
	
	//FUNCTION
	$('.action_code_ga8').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='ga8',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =$(this).attr('user_id_1'),
			user_id_2   =$(this).attr('user_id_2'),
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),	
			forum_id_2  =0,
			article_id  =$(this).attr('article_id'),
			reply_id    =$(this).attr('reply_id'),
			go_url      =$(this).attr('go_url')
		);
	});
	
	//FUNCTION
	$('.action_code_ga23').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='ga23',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =$(this).attr('user_id_1'),
			user_id_2   =0,
			book_sid_1  =$(this).attr('book_sid_1'),
			book_sid_2  ='',
			forum_id_1  =$(this).attr('forum_id_1'),	
			forum_id_2  =0,
			article_id  =$(this).attr('article_id'),
			reply_id    =0,
			go_url      =$(this).attr('go_url')
		);
	});
	
	
	//FUNCTION
	$('.action_code_ga24').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='ga24',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =$(this).attr('user_id_1'),
			user_id_2   =$(this).attr('user_id_2'),
			book_sid_1  =$(this).attr('book_sid_1'),
			book_sid_2  =$(this).attr('book_sid_2'),
			forum_id_1  =$(this).attr('forum_id_1'),	
			forum_id_2  =0,
			article_id  =$(this).attr('article_id'),
			reply_id    =$(this).attr('reply_id'),
			go_url      =$(this).attr('go_url')
		);
	});
	
	var cat_id =<?php echo (int)$cat_id;?>;
		
	scaffolding_reply_text(cat_id);
	
	
</script>
	

<!---------------------------------側欄------------------------------------->	
    <div class="aside">
		<!---------------------------------側欄(群)------------------------------------->	
		<?php require_once(str_repeat("../",0).'group_aside.php');?>
		<!---------------------------------側欄(群)------------------------------------->

	</div>



</body>

</html>