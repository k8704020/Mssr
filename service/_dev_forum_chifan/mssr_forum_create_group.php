<?php
//-------------------------------------------------------
//mssr_fourm
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
    //SESSION
    //---------------------------------------------------

        //$_SESSION['uid']=11111;
        $sess_uid=(int)$_SESSION["uid"];
		$class_code = trim($_SESSION['class'][0][1]);
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
					$user_id = (int)$_GET["user_id"];
					
				//-----------------------------------------------
	        	//SQL-撈出班上所有的學生、老師
	        	//-----------------------------------------------

					$sql="
						SELECT
							`uid`
						FROM
							`teacher`
						WHERE
							`class_code` = '$class_code';
					";
					
					$arrys_result_class_teacher=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
					$arrys_result_class_teacher_con = count($arrys_result_class_teacher);
				//-----------------------------------------------
	        	//SQL-userinfo(學生資訊)
	        	//-----------------------------------------------
					$sql="
						SELECT
							`name`
						FROM
							`member`
						WHERE
							`uid` = $user_id
					";
					$arrys_result_userinfo=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
					$numrow_userinfo=count($arrys_result_userinfo);
				//-----------------------------------------------
	        	//SQL-usergrade(用class_code找學生年級資訊)
	        	//-----------------------------------------------
					$class_code = trim($_SESSION['class'][0][1]);

					$sql="
						SELECT
							`class`.`grade`, `class`.`classroom`, `class`.`class_code`, `semester`.`school_code`
						FROM
							`class` inner join `semester`
							on `class`.`semester_code` = `semester`.`semester_code`
						WHERE
							`class`.`class_code` = '$class_code'
					";


					$arrys_result_usergrade=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);

					$numrow_usergrade=count($arrys_result_usergrade);

				//-----------------------------------------------
	        	//SQL-arrys_result_user_school(用class_code找學生學校資訊)
	        	//-----------------------------------------------
					$user_school = $arrys_result_usergrade[0]["school_code"];

					$sql="
						SELECT
							`school_name`, `region_name`
						FROM
							`school`
						WHERE
							`school_code` = '$user_school'
					";

					$arrys_result_user_school=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);

					$numrow_user_school=count($arrys_result_user_school);
				//-----------------------------------------------
	        	//SQL-shelf(書櫃)
	        	//-----------------------------------------------
					$sql="
						SELECT
							`book_sid`
						FROM
							`mssr_book_borrow_semester`
						WHERE
							`user_id` = $user_id
					";
					$arrys_result_shelf=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_shelf=count($arrys_result_shelf);
				//-----------------------------------------------
	        	//SQL-學生發文數量
	        	//-----------------------------------------------
					$sql="
						SELECT
							`user_id`
						FROM
							`mssr_forum_article`
						WHERE
							`user_id` = $user_id

					";
					$arrys_result_articlenum=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_articlenum=count($arrys_result_articlenum);
				//-----------------------------------------------
	        	//SQL-學生回復數量
	        	//-----------------------------------------------
					$sql="
						SELECT
							`user_id`
						FROM
							`mssr_forum_article_reply`
						WHERE
							`user_id` = $user_id

					";
					$arrys_result_replynum=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_replynum=count($arrys_result_replynum);

				//-----------------------------------------------
	        	//SQL-檢查是否為好友
	        	//-----------------------------------------------

					$sql="
						SELECT
							`user_id`, `friend_id`
						FROM
							`mssr_forum_friend`
						WHERE 1=1
							AND `friend_state` 	= '成功'
							AND ((`user_id`		= $user_id
								AND
								 `friend_id`	= $sess_uid)
									OR
								 (`user_id`		= $sess_uid
								 AND
								 `friend_id`	=$user_id))

					";
					$arrys_result_friend_check=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				//-----------------------------------------------
	        	//SQL-my friend
	        	//-----------------------------------------------
				$sql="
					SELECT
						`user_id`,`friend_id`
					FROM
						`mssr_forum_friend`
					WHERE 1=1
						AND (
							`user_id` =$sess_uid
								OR
							`friend_id` = $sess_uid
						)
						AND `friend_state` = '成功'
				";
				$arrys_result_friend = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


	//---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球-建立小組";



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

    <link type="text/css" rel="stylesheet" href="css/bootstrap.css">
    <link type="text/css" rel="stylesheet" href="css/mssr_forum.css">
    <link type="text/css" rel="stylesheet" href="../../inc/code.css">


	<script>


		  function create_forum_friend(){

				$.blockUI({
							message: $('#create_forum_friend_box'),
							css:{
								top:  ($(window).height() - 400) /2 + 'px',
								left: ($(window).width() - 700) /2 + 'px',
								textAlign:	'left',
								width: '700px'

							}
				});

				$('#joint_box_leave').click(function() {
					$.unblockUI();
					return false;
				});

		  }

        //聯屬好友全選
        function check_all(chk){

            if(document.joint_create.check_ctr.checked==true){

                for (i = 0; i < chk.length; i++)
                    chk[i].checked = true ;
            }else{
                for (i = 0; i < chk.length; i++)
                    chk[i].checked = false ;
            }
        }

		function joint(){

			  var joint_uid_array = document.joint_create.elements["joint_uid"];
			  var joint_friend_name_array = document.joint_create.elements["joint_name"];

			  //var tmp_joint_uid_array = new Array();
			  var tmp_joint_name = '';
			  var tmp_joint_uid = '';


			  var check_joint=0;


			  for(i=0;i < joint_uid_array.length;i++){
				  //判斷勾選是不是至少兩個
				  if(joint_uid_array[i].checked == true){
					tmp_joint_name += '<span id = "joint_name_box-'+ joint_uid_array[i].value +'"><b><i><h4 style="color:#660000;"><a onclick="delete_joint(' + joint_uid_array[i].value + ')"><em class="glyphicon glyphicon-remove"></em></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+joint_friend_name_array[i].value+'<BR></h4></i></b></span>';
					tmp_joint_uid+=' <input id = "joint_uid_box-' + joint_uid_array[i].value + '" type="hidden" value="'+joint_uid_array[i].value+'" name="joint_friend_uid[]">';
					check_joint++;
				}
			  }

			  if(check_joint<2){
					alert("聯屬至少兩位好友，請重新選擇！");
					return false;
			  }
				document.getElementById("joint_friend").innerHTML = tmp_joint_name+tmp_joint_uid;

			$.unblockUI();
			return false;
		}

		function delete_joint(uid){

			$("#joint_name_box-" + uid).remove();
			//???
			$("#joint_uid_box-" + uid).remove();
			//$("[id='joint_uid_box-" + uid +"']").remove();

			//document.getElementById('joint_uid_box-' + uid).removeNode(true);

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
				<a href="index.php">首頁</a> <span class="divider"></span>
			  </li>
			  <li>
				<a href="mssr_forum_people_index.php?user_id=<?php echo $user_id;?>">
						<?php echo $arrys_result_userinfo[0]['name']?>個人頁面</a> 
						<span class="divider"></span>
			  </li>

			  <li class="active">
				聊書小組(建立聊書小組)
			  </li>
			</ul>
	  </div>

	<!--========================group header=====================================-->
	  <div class="group_header">
			<div class="group_image" >
			  <?php
				$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
				if($get_user_info[0]['sex']==1){?>
					<img src="image/boy.jpg" width="100px" height="100px" />
				<?php }else{?>
					<img src="image/girl.jpg" width="100px" height="100px" />
				<?php }?>
			</div>

			<div class="group_info1">

				<?php echo $arrys_result_userinfo[0]['name']?><BR>
				<?php echo $arrys_result_user_school[0]['school_name']?><?php echo $arrys_result_usergrade[0]['grade']?>年<?php echo $arrys_result_usergrade[0]['classroom']?>班
				<BR><BR>



			</div>

			<div class="group_info2">

			  <?php echo $arrys_result_userinfo[0]['name']?>的閱讀資訊:<BR>
				  發表了<?php echo $numrow_articlenum?>篇文章<BR>
				  已經讀了<?php echo $numrow_articlenum?>本書<BR>
				  回覆<?php echo $numrow_replynum?>篇文章<BR>

			</div>

	 </div>


	<!--========================tab_bar=====================================-->
		<div class="tab_bar">

			  <div class="tabbable" id="tabs-215204">
				<ul class="nav nav-tabs">
				<li>
					<a href="mssr_forum_people_index.php?user_id=<?php echo $user_id?>">首頁</a>
				  </li>
				  <li>
					<a href="mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>">書櫃</a>
				  </li>
				  <li>
					<a href="mssr_forum_people_myreply.php?user_id=<?php echo $user_id?>" >討論</a>
				  </li>
				  <li class="active">
					<a href="mssr_forum_people_group.php?user_id=<?php echo $user_id?>">聊書小組</a>
				  </li>
				  <li>
					<a href="mssr_forum_people_friend.php?user_id=<?php echo $user_id?>">朋友</a>
				  </li>
				  <li>
                <a onclick="logFuc('inc/add_action_forum_log/code.php','p11',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'mssr_forum_people_favorite_book.php?user_id=<?php echo $user_id?>')">追蹤書籍</a>
              </li>
				  </ul>
			  </div>

		</div>






	<!--========================content=====================================-->
	<div class="content">

		<div class="left_content">


			<div class="btn-group">
				<input class="btn btn-default" type ="button" onclick="javascript:location.href='mssr_forum_people_group.php?user_id=<?php echo $user_id?>'" value="我的聊書小組"></input>
				<input class="btn btn-default" type ="button" disabled="disabled" onclick="javascript:location.href='mssr_forum_create_group.php'" value="建立聊書小組"></input>
			</div>

		 <div style="padding-left:100px">
		  <form action="mssr_forum_create_group_A.php" method="post">
		  <h3><font color="blue">輸入建立聊書小組基本資料</font></h3><P>


		  <input type="hidden" value="<?php echo $sess_uid?>" name="user_id">

		  聊書小組名稱：<input name="forum_name" type="text" ><P>
		  聊書小組介紹：
		  <!--<input name="forum_content_ex" class="btn btn-default btn-sm" type ="button" value="範例"></input>-->
		  <P><textarea name="forum_content" cols="45" rows="5"></textarea><p>

		 聊書小組規範：
		 <!--<input name="forum_rule_ex" class="btn btn-default btn-sm" type ="button" value="範例"></input>-->
		  <P><textarea name="forum_rule" cols="45" rows="5"></textarea><p>
		  選擇聯署共同建立聊書小組的好友：
		  <button type="button" class="btn btn-default btn-xs" onclick="create_forum_friend()">選擇好友</button><BR>
		  (至少需要兩個好友共同建立聊書小組)
		  <div id="joint_friend">
		  </div>

		  <input class="btn btn-primary " type ="submit" value="送出"></input>
		  </form>
	   </div>

		</div>

	<!----------block ui div(聯屬好友)---------->
	<div id="create_forum_friend_box"  style="display:none; cursor: default;padding:15px;">
	   <form name="joint_create">
			<input type="image" id="joint_box_leave" src="image/xlogo.png" alt="" width="30" height="30"/>
			     <blockquote>
					<p><B>選擇要聯屬建立聊書小組的好友</B></p>
					至少要兩名好友同意，才能建立小組
				 </blockquote>

                 <span id="joint_box_check_all">全選所有好友
                      <input type='checkbox' name='check_ctr' value="yes" onClick="check_all(document.joint_create.joint_uid)">
                 </span>

				<div style="overflow:auto;  height:300px; position:relative; width:100%;">
				<?php if(!$arrys_result_friend){
						echo "現在還沒有朋友喔，趕快去找你的朋友加入成好友吧！";
					}else{
							foreach($arrys_result_friend as $inx=>$arrys_result_friend){
								
								$user_id 			= (int)$arrys_result_friend['user_id'];
								$friend_id 			= (int)$arrys_result_friend['friend_id'];

								if($user_id==$sess_uid){
									$rs_user_id = $user_id;
									$rs_friend_id = $friend_id;
								}else{
									$rs_user_id     	= $friend_id;
									$rs_friend_id		= $user_id;
								}
								$get_user_info = get_user_info($conn_user,$rs_friend_id,$array_filter=array('name','sex'),$arry_conn_user);
								$friend_name 			= trim($get_user_info[0]['name']);
								$friend_sex 			= trim($get_user_info[0]['sex']);
								
								//聯署剔除掉老師
								if($rs_friend_id == $arrys_result_class_teacher[0]['uid']) {
									continue;
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
						  <input type="checkbox" id="joint_uid" name="friend_uid[]" value="<?php echo $rs_friend_id?>" >
						  <input type="hidden" id="joint_name" value="<?php echo $friend_name?>" >
						  <font color=blue><?php echo $friend_name?></font>

						</figcaption>
					</figure>
				<?php


						}
					}


				?>
			</div>
			<BR>
			
			<input  id="joint_box_submit" class="btn btn-default" type="button" onclick="joint()" value="送出"/>
			</form>

	</div>



		

		
		<!--========================排行=====================================-->
		<?php require_once('mssr_forum_right_people.php');  ?>
		

	</body>

</html>