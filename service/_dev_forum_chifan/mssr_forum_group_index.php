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
		//$_SESSION['uid']        =5030;
		$class_code = trim($_SESSION['class'][0][1]);



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
         //小組權限(session)
         //-----------------------------------------------
		 $sql="
				SELECT
					`forum_id`
				FROM
					`mssr_user_forum`
				WHERE 1=1
					AND `user_id` ={$sess_uid	}
					AND	`user_state` LIKE '%啟用%'
					AND `user_type`  LIKE '%一般版主%'
		 ";

		 $arrys_result_forum_manager=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

		 $_SESSION['forum_id_manager']=$arrys_result_forum_manager;


		//判斷是否在此小組有管理權限
		$has_manager = FALSE;

		$forum_id_manager = $_SESSION['forum_id_manager'];


		for($i=0; $i < count($forum_id_manager);$i++){
			if(in_array($forum_id , $forum_id_manager[$i])){
				$has_manager	=	TRUE;
			}
		}




        //-----------------------------------------------
        //SQL-討論區發文列表
        //-----------------------------------------------
          $query_sql="
            SELECT
              `mssr_forum_article`.`article_title`,
              `mssr_forum_article`.`article_id`,
			  `mssr_forum_article`.`user_id`,
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
          $arrys_result_article=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(0,5),$arry_conn_mssr);

          //-----------------------------------------------
	      //SQL-最新討論書籍
	      //-----------------------------------------------
			$query_sql="
				SELECT
					`book_sid`,`keyin_cdate`
				FROM
					`mssr_forum_booklist`
				WHERE 1=1
					AND `forum_id` = $forum_id
					AND `book_state` = 1
				ORDER BY `keyin_cdate` DESC
				";
			$arrys_result_shelf=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(0,5),$arry_conn_mssr);


			//-----------------------------------------------
	        //SQL-討論區成員
	        //-----------------------------------------------
				$query_sql="
					SELECT
						`user_id`
					FROM
						`mssr_user_forum`
					WHERE 1=1
						AND `forum_id` = $forum_id
						AND `user_state` LIKE '%啟用%'
					ORDER BY `keyin_mdate` DESC
					";
				$arrys_result_member_recent=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(0,5),$arry_conn_mssr);
				$arrys_result_member_recent_con = count($arrys_result_member_recent);

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
            //SQL-誰也在這個聊書小組
            //-----------------------------------------------
          $sql="
            SELECT
              `forum_id`, `user_id`
            FROM
              `mssr_user_forum`
            WHERE 1=1
              AND `forum_id` = $forum_id
          ";
          $arrys_result_aside=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
          $numrow_aside=count($arrys_result_aside);

        //-----------------------------------------------
            //SQL-誰也參加這個聊書小組
            //-----------------------------------------------
          $sql="
            SELECT
              `forum_id`, `user_id`
            FROM
              `mssr_user_forum`
            WHERE 1=1
              AND `forum_id` = $forum_id
              AND `user_state` LIKE '%啟用%'
          ";
          $arrys_result_forum_member=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);
          $numrow_forum_member=count($arrys_result_forum_member);

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
            //SQL-參加這個聊書小組的人，他們也參加了?
            //-----------------------------------------------
          //找最近參加此聊書小組的3個人
          $sql="
            SELECT
              `user_id`
            FROM
              `mssr_user_forum`
            WHERE
              `forum_id` = '$forum_id'
            ORDER BY
              `keyin_cdate` DESC

          ";
          $arrys_result_who_join=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,3),$arry_conn_mssr);
          $numrow_group_who_join=count($arrys_result_who_join);

          $arry_group_id_list=array();
          for($i=0;$i<$numrow_group_who_join;$i++){
            $user_id      = (int)$arrys_result_who_join[$i]['user_id'];
            //找參加過這個聊書小組的人，他們也參加甚麼聊書小組，挑重複度最高的三位
            $sql="
              SELECT
                `forum_id`
              FROM
                `mssr_user_forum`
              WHERE 1=1
                AND `user_id` = $user_id
                AND `forum_id`  <> $forum_id

            ";
            $arrys_result_recommend=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


            if(!empty($arrys_result_recommend)){
              $arry_list=array();
              foreach($arrys_result_recommend as  $arry_result_recommend){

                $rs_group_id=trim($arry_result_recommend['forum_id']);

                if(!array_key_exists($rs_group_id,$arry_group_id_list)){
                  $arry_group_id_list[$rs_group_id]=1;
                }else{
                  $arry_group_id_list[$rs_group_id]=$arry_group_id_list[$rs_group_id]+1;
                }
              }
              //排序
              arsort($arry_group_id_list);

              //篩選
              foreach($arry_group_id_list as $book_sid_tmp=>$cno){
                if(count($arry_list)<3){
                  $arry_list[]=trim($book_sid_tmp);
                }else{
                  break;
                }
              }
            }
            //$numrow_recommend=count($arrys_result_recommend);
          }


		   //-----------------------------------------------
	      //SQL-friend(我的好友)
	      //-----------------------------------------------
					$query_sql="
						SELECT
							`user_id`,`friend_id`
						FROM
							`mssr_forum_friend`
						WHERE 1=1
							AND (
								`user_id` = $sess_uid
									OR
								`friend_id` = $sess_uid
							)
							AND `friend_state` = '成功'
					";
					$arrys_result_friend=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
					$arrys_result_friend_con=count($arrys_result_friend);
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
	        //SQL-撈出班上的老師
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



		//是否為小組成員
		$has_group_member = FALSE;
		if(!empty($arrys_group_stud_check)){
			$has_group_member = TRUE;
		}


        //網頁標題
        $title="明日星球-聊書小組首頁";
		$site ="mssr_forum_group_index.php";



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

  	var forum_id=<?php echo addslashes($forum_id);?>;
	var sess_uid=<?php echo (int)$sess_uid;?>;


	function add_group(){
    //加入小組

        var url ='';
		var site = 'mssr_forum_group_index.php';
        var page=str_repeat('../',0)+'mssr_forum_group_add_groupA.php';
        var arg ={
            'sess_uid':sess_uid,
            'forum_id' :forum_id,
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
          <li class="active">
            <a href="mssr_forum_group_index.php?forum_id=<?php echo $forum_id?>"><?php echo trim($arrys_result_2[0]['forum_name']);?>首頁</a>
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
          			$create_by      = trim($arrys_result_2[0]['create_by']);
                    $forum_state      = trim($arrys_result_2[0]['forum_state']);
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

			<li class="active">
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
			<li><a 	href="javascript:void(0);"
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

	<!--========================left_content start=====================================-->
    <div class="left_content">


		<!--========================最新討論回覆文章=====================================-->
		<div class="group_index">
			<H4 style="background-color:#CCC;"><B>聊書小組介紹	</B></h4>
			<?php echo filter(trim($arrys_result_2[0]['forum_content']));?>
		</div>

		<!--========================最新討論回覆文章=====================================-->
		<div class="group_index">

			<H4 style="background-color:#CCC;"><B>最新討論回覆文章</B></h4>

					<?php
						if(empty($arrys_result_article)){
							echo("<h4 align=center>這此小組目前還沒有討論留言喔，趕快來討論吧！</h4>");
						}else{
					?>
						 <table class="table table-hover table-striped">
							<thead>
							  <tr>
								<th style="width:80%">
								  標題
								</th>
								<th style="width:20%">
								  回應數
								</th>

							  </tr>
							</thead>
								<tbody>
					<?php

							for($i=0; $i<count($arrys_result_article); $i++){


								$article_id 			= $arrys_result_article[$i]['article_id'];
								$article_title 			= $arrys_result_article[$i]['article_title'];
								$user_id	 			= $arrys_result_article[$i]['user_id'];

								//article_title			文章標題
								if(mb_strlen($article_title)>15){
									$article_title=mb_substr($article_title,0,15)."..";
								}

								//-----------------------------------------------
								//SQL-回覆數量
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
					?>
							  <tr>
								<td>

									<a href="javascript:void(0);"
									<?php
										echo 'class="action_code_g16"';
										echo " user_id_1='{$user_id}'";
										echo " forum_id_1='{$forum_id}'";
										echo " article_id='{$article_id}'";
										echo " go_url='mssr_forum_group_reply.php?article_id={$article_id}'";
									?>>

										<?php echo filter($article_title); ?>
									</a>

								</td>
								<td>
									<?php echo $numrow_replynum?>
								</td>
					<?php
							}
						}
					?>
							</tbody>
						  </table>

						 </table>


		</div>

		<!--========================最新興趣書單書籍=====================================-->
		<div class="group_index">

			<H4 style="background-color:#CCC;"><B>最新興趣書單書籍</B></h4>

				<?php
						if(empty($arrys_result_shelf)){
							echo("<h4 align=center>現在書櫃沒有書喔，趕快來看書吧！</h4>");
						}else{
							for($i=0; $i<count($arrys_result_shelf); $i++){
								$book_sid 			= trim($arrys_result_shelf[$i]['book_sid']);

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
								}?>


								<figure class="figure_book">

									<a href="javascript:void(0);"
									<?php
										echo 'class="action_code_g17"';
										echo " book_sid_1='{$book_sid}'";
										echo " forum_id_1='{$forum_id}'";
										echo " go_url='mssr_forum_book_discussion.php?book_sid={$book_sid}'";
									?>>

										<img src="<?php echo $rs_bookpic_root ?>" alt="<?php echo $book_name?>" width="100px" height="100px" />
									</a>

									<figcaption class="figcaption_book">
										<a href="javascript:void(0);"
										<?php
											echo 'class="action_code_g17"';
											echo " book_sid_1='{$book_sid}'";
											echo " forum_id_1='{$forum_id}'";
											echo " go_url='mssr_forum_book_discussion.php?book_sid={$book_sid}'";
										?>>
											<?php echo $book_name?>
										</a>
									</figcaption>

								</figure>
							<?php }?>
						<?php }?>

		</div>



		<!--========================最新加入成員=====================================-->

		<div class="group_index">

			<H4 style="background-color:#CCC;"><B>最新加入成員</B></h4>
				<?php
					if(empty($arrys_result_member_recent)){
						echo("<h4 align=center>這個討論區目前沒人喔！</h4>");
					}else{
						for($i=0;$i<count($arrys_result_member_recent);$i++){
							$user_id = $arrys_result_member_recent[$i]['user_id'];

							if($user_id == $arrys_result_class_teacher[0]['uid']) {
									continue;
							}


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
							$user_name   	= $arrys_result_userinfo[0]['name'];?>

							<figure class="figure_book">
									<?php
									$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
									if($get_user_info[0]['sex']==1){
									?>

										<a href="javascript:void(0);"
										<?php
											echo 'class="action_code_g18"';
											echo " user_id_1='{$user_id}'";
											echo " forum_id_1='{$forum_id}'";
											echo " go_url='mssr_forum_people_shelf.php?user_id={$user_id}'";
										?>>

											<img src="image/boy.jpg" />
										</a>


									<?php }else{?>

										<a href="javascript:void(0);"
										<?php
											echo 'class="action_code_g18"';
											echo " user_id_1='{$user_id}'";
											echo " forum_id_1='{$forum_id}'";
											echo " go_url='mssr_forum_people_shelf.php?user_id={$user_id}'";
										?>>
											<img src="image/girl.jpg" />
										</a>

									<?php }?>


								<figcaption class="figcaption_book">
									<a href="mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>"><?php echo $user_name?></a>
								</figcaption>
							</figure>
				  <?php }?>
			<?php }?>

		</div>



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
	$('.action_code_g16').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='g16',
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
	$('.action_code_g17').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='g17',
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
	$('.action_code_g18').click(function(){
		//呼叫
		add_action_forum_log(
			process_url ='inc/add_action_forum_log/code.php',
			action_code ='g18',
			action_from ='<?php echo (int)$_SESSION["uid"];?>',
			user_id_1   =$(this).attr('user_id_1'),
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






</script>


	<!--========================left_content end=====================================-->


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