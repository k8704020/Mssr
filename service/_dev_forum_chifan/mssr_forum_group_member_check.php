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
					$user_id =(int)$_SESSION["uid"];
					

      
					
				//-----------------------------------------------
	        	//SQL-討論區成員(分頁)
	        	//-----------------------------------------------
					$query_sql="
						SELECT
							`user_id`, `user_state`,`user_type`,`user_intro`,`keyin_cdate`
						FROM
							`mssr_user_forum`
						WHERE 1=1
							AND `forum_id` = $forum_id
							AND `user_state` LIKE '%申請中%'
					";
					$arrys_result_member_check=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
					$arrys_result_member_check_con = count($arrys_result_member_check);


				
              
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
				  
				

   

    //---------------------------------------------------
    //檢驗
    //---------------------------------------------------
 


	//---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=$arrys_result_member_check_con ;    //資料總筆數
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
      

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------
		//網頁標題
        $title="明日星球-聊書小組成員";
		$site ="mssr_forum_group_member_check.php";
		
		//判斷是否在此小組有管理權限
			
		$has_manager = FALSE;
		
		$forum_id_manager = $_SESSION['forum_id_manager'];
			
		
		for($i=0; $i < count($forum_id_manager);$i++){
			if(in_array($forum_id , $forum_id_manager[$i])){
				$has_manager	=	TRUE;
			}
		}

        if($numrow!==0){
            $arrys_chunk =array_chunk($arrys_result_member_check,$psize);
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
				'page_name' :'mssr_forum_group_member_check.php',
				'page_args' :{
					'forum_id' :<?php echo (int)$forum_id;?>
				}
			}
			var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
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
          </li>
          <li class="active">
            聊書小組成員
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
          
			  <a class="btn" id="open_invite_box" type="button">邀請人員</a>

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
			<li class="active">
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
		  <input class="btn btn-default" type ="button" onclick="javascript:location.href='mssr_forum_group_member.php?forum_id=<?php echo $forum_id?>'" value="成員列表"></input>
         <input class="btn btn-default" type ="button" onclick="javascript:location.href='mssr_forum_group_member_check.php'" value="審核欲加入成員" disabled="disabled"></input>
         
	  </div>
      
     
	<P>
      <table class="table table-hover table-striped">
        <thead>
                    <tr>
                        <th style="width:10%">姓名</th>
                        <th style="width:40%">自我簡介</th>
                        <th style="width:15%">加入時間</th>
                        <th style="width:15%">成員類型</th>
						<?php if($has_manager){?>
							<th style="width:15%"></th>
						<?php }?>
                    </tr>
                </thead>
                <tbody>
				<?php
					if(empty($arrys_result_member_check)){
						echo("這個討論區目前沒人喔！");
					}else{
						foreach($arrys_result as $inx=>$arrys_result_member):
							$user_id = trim($arrys_result_member['user_id']);
							$user_intro = trim($arrys_result_member['user_intro']);
							$date = trim($arrys_result_member['keyin_cdate']);
							$user_type = trim($arrys_result_member['user_type']);

							$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('name','sex'),$arry_conn_user);
							$user_name 			= trim($get_user_info[0]['name']);
							$user_sex 			= trim($get_user_info[0]['sex']);
				?>
                     <tr class="even">
                        <td>
							<?php
								//處理
								if($member_sex==1){?>
							
								<a href="mssr_forum_people_index.php?user_id=<?php echo $user_id;?>">
									<img src="image/boy.jpg" alt="<?php echo $user_name;?>" width="40px" height="40px"/>
								</a>
											
							<?php }else{?>
											
										
								<a href="mssr_forum_people_index.php?user_id=<?php echo $user_id;?>">
									<img src="image/girl.jpg" alt="<?php echo $user_name;?>" width="40px" height="40px"/>
								</a>

							<?php }?>
								<BR>
								<a href="mssr_forum_people_index.php?user_id=<?php echo $user_id;?>">
									<?php echo $user_name;?>
								</a>
						</td>
                        <td><?php echo $user_intro?></td>
                        <td><?php echo $date?></td>
                        <td><?php echo $user_type?></td>
                        <?php if($has_manager){?>	
							<td align="center">
								
									<input class="btn btn-default btn-xs" type ="button" value="允許" 
										onclick="if(confirm('確定將此成員加入?')){check_permit(<?php echo $user_id;?>,<?php echo $forum_id;?>)}">
									</input>
									<input class="btn btn-default btn-xs" type ="button" value="拒絕"
										onclick="if(confirm('確定拒絕?')){check_reject(<?php echo $user_id;?>,<?php echo $forum_id;?>)}">
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
	
	function check_permit(user_id_1, forum_id_1){

		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='g29',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =user_id_1,
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =forum_id_1,	
			forum_id_2  =0,
			article_id  =0,
			reply_id    =0,
			go_url      ='mssr_forum_group_member_check_A.php?user_id='+user_id_1+'&forum_id='+forum_id_1+'&action_type=permit'
		);
		
		
	}
	
	function check_reject(user_id_1, forum_id_1){
		
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='g30',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =user_id_1,
			user_id_2   =0,
			book_sid_1  ='',
			book_sid_2  ='',
			forum_id_1  =forum_id_1,	
			forum_id_2  =0,
			article_id  =0,
			reply_id    =0,
			go_url      ='mssr_forum_group_member_check_A.php?user_id='+user_id_1+'&forum_id='+forum_id_1+'&action_type=reject'
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