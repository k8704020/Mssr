<!-- 邀請人員加入小組-->
<?php
	//-----------------------------------------------
	//SQL-朋友名單
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

?>


<!DOCTYPE HTML>
<html>
	<head>
	  <meta charset="utf-8">
	</head>

	<script>

		$(document).ready(function() {

			//分頁列

			//block ui
			$('#open_invite_box').click(function() {
				var article_id=parseInt(<?php if(isset($_GET['article_id'])){echo $_GET['article_id'];}else{echo 0;}?>);
				
				if(article_id===0){
					action_code ='g14';
				}else{
					action_code ='ga17';
				}
			
				add_action_forum_log(
					process_url ='inc/add_action_forum_log/code.php',
					action_code =action_code,
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
		});
	</script>



	<body>


		<!----------block ui div(邀請人員)---------->
		<div id="invite_box"  style="display:none; cursor: default;overflow: auto;">
			<form action="mssr_forum_group_invite_A.php" method="post">
				<input type="image" id="invite_box_leave" src="image/xlogo.png" alt="" width="30" height="30" onclick="leaveui();"/>

					<blockquote>
						<p><B>邀請好友加入小組</B></p>
					</blockquote>

					<input id="" type="hidden" value="<?php echo $forum_id ?>" name="forum_id">
					<input id="" type="hidden" value="<?php echo $site?>"	name="site">

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

								$sql="
									SELECT
									  `mssr_user_request`.`request_id`
									FROM `mssr_user_request`
									  INNER JOIN `mssr_user_request_forum_join_rev` ON
									  `mssr_user_request`.`request_id` = `mssr_user_request_forum_join_rev`.`request_id`
									WHERE 1=1
									  AND `mssr_user_request_forum_join_rev`.`forum_id` = $forum_id
									  AND `mssr_user_request`.`request_from` = $sess_uid
									  AND `mssr_user_request`.`request_to` = $rs_friend_id



								  ";
								  $arrys_result_request=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

								  $sql="
									SELECT
										`user_id`
									FROM `mssr_user_forum`
									WHERE 1=1
										AND `forum_id` = $forum_id
										AND `user_id`  = $rs_friend_id
										AND `user_state` LIKE '%啟用%'
								  ";
								 $arrys_result_if_member=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);




								  //判斷是否有邀請過
									$has_disabled = '';
									$has_request = FALSE;
									$has_member = FALSE;
									if(!empty($arrys_result_request)){
										$has_request = TRUE;
										$has_disabled = 'disabled';
									}else if(!empty($arrys_result_if_member)){
										$has_member = TRUE;
										$has_disabled = 'disabled';
									}

							?>

									<figure class="figure_people">
										<?php
											//處理
											if($friend_sex==1){?>

												<a target="_blank" href="mssr_forum_people_shelf.php?user_id=<?php echo $rs_friend_id?>" >
													<img src="image/boy.jpg" alt="<?php echo $friend_name?>" width="50px" height="50px" />
												</a>

										<?php }else{?>

												<a target="_blank" href="mssr_forum_people_shelf.php?user_id=<?php echo $rs_friend_id?>">
													<img src="image/girl.jpg" alt="<?php echo $friend_name?>" width="50px" height="50px" />
												</a>

										<?php }?>

										<figcaption class="figcaption_people">
											<input type="checkbox" name="friend_uid[]" value="<?php echo $rs_friend_id?>" <?php echo $has_disabled?>>
											<a target="_blank" href="mssr_forum_people_shelf.php?user_id=<?php echo $rs_friend_id?>"><?php echo $friend_name?></a>
													<?php
														if($has_member){
															echo '<BR>(已是成員)';
														}
														if($has_request){
															echo '<BR>(邀請中)';
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

	</body>

</html>