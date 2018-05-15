<!-- 邀請人員加入小組-->


<!DOCTYPE HTML>
<html>
	<head>
	  <meta charset="utf-8">
	</head>

	

	<body>


		<!---------------------------------側欄------------------------------------->



				<?php
					$oright_side=new right_side((int)$sess_uid,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);

					$arry_forum=$oright_side->forum((int)$sess_id,$forum_id,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
				


				?>

					<table class="table">
						<thead>
							<tr align="center">
								<TD COLSPAN=2>
                                    <span style="font-size:14px; color:#000079;">誰也參加這個聊書小組</span>
                                </TD>
							</tr>
						</thead>
						<tbody>
						<?php
							 if(!empty($arry_forum[0])){

								foreach($arry_forum[0] as $arry_forum_k =>$arry_forum_main){
									foreach($arry_forum_main as	$inx=> $arry_forum_value){

										if($arry_forum_k  =="main"){
						?>
										<tr>
											<td>
												<img src="image/boy.jpg" alt=""  width="30" height="30"/>
											</td>
											<td>
												<?php echo $arry_forum[0]['main'][$inx];?>
												<BR>
												<?php echo '<span style="font-size:8px">也看過'.$arry_forum[0]['submain'][$inx].'這本書</span>';?>
											</td>
										</tr>

							<?php
										}
									}

								}
							}else{
								echo '<tr><td>';
								echo '目前還沒有人加入，趕快來加入這個小組吧！';
								echo '</td></<tr>';
							}

						?>

						</tbody>
					</table>

					<table class="table">
						<thead>
							<tr align="center">
								<TD COLSPAN=2>
                                    <span style="font-size:14px; color:#000079;">聊書小組的熱門討論書籍</span>
                                </TD>
							</tr>
						</thead>

						<tbody>
							<?php
							if(!empty($arry_forum[1])){

								foreach($arry_forum[1] as $arry_forum_k =>$arry_forum_main){
									foreach($arry_forum_main as	$inx=> $arry_forum_value){

										if($arry_forum_k  =="main"){

							?>
								<tr>
									<td>

										<img src="image/book.jpg" alt=""  width="30" height="30"/>

									</td>
									<td>
									<?php
										$book_sid 			= $arry_forum[1]['main'][$inx];
										$arrys_book_info	= get_book_info($conn_mssr,$book_sid,$array_filter=array(),$arry_conn_mssr);
										$book_name 			= mysql_prep(trim($arrys_book_info[0]['book_name']));

									?>
										<a href="javascript:void(0);"
											<?php
												echo 'class="action_code_g33_ga20"';
												echo " book_sid_1='{$book_sid}'";
												echo " forum_id_1='{$forum_id}'";
												echo " go_url='mssr_forum_book_discussion.php?book_sid={$book_sid}'";
											?>>
											<?php echo $book_name;?>
										</a>
										<BR>
									<?php echo '<span style="font-size:8px">目前討論'.$arry_forum[1]['submain'][$inx].'次</span>';?>



									</td>
								</tr>

									<?php
										}
									}

								}
							}else{
								echo '<tr><td>';
								echo '目前聊書小組的興趣書單，還沒有書籍喔！趕快來新增吧！';
								echo '</td></<tr>';
							}
							?>

						</tbody>
					</table>


								<table class="table">

									<thead>
										<tr align="center">
											<TD COLSPAN=2>
                                                <span style="font-size:14px; color:#000079;">參加這個聊書小組的人<BR>也參加了那些聊書小組?</span>
                                            </TD>
										</tr>
									</thead>
									<tbody>
									<?php
										 if(!empty($arry_forum[2])){

											foreach($arry_forum[2] as $arry_forum_k =>$arry_forum_main){
												foreach($arry_forum_main as	$inx=> $arry_forum_value){


													if($arry_forum_k  =="main"){

										?>
													<tr>
														<td>

															<img src="image/group.png" alt=""  width="30" height="30"/>

														</td>
														<td>
															<?php echo $arry_forum[2]['main'][$inx];?>
															<BR>
															<?php //echo $arry_forum[2]['submain'][$inx];?>
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
								

	<script type="text/javascript">	

			//FUNCTION
			$('.action_code_g33_ga20').click(function(){
			
				var article_id=parseInt(<?php if(isset($_GET['article_id'])){echo $_GET['article_id'];}else{echo 0;}?>);
				
				if(article_id===0){
					action_code ='g33';
				}else{
					action_code ='ga20';
				}
			
				//呼叫
				add_action_forum_log(
					process_url ='inc/add_action_forum_log/code.php',
					action_code =action_code,
					action_from ='<?php echo (int)$_SESSION["uid"];?>',
					user_id_1   =0,
					user_id_2   =0,
					book_sid_1  =$(this).attr('book_sid_1'),
					book_sid_2  ='',
					forum_id_1  =$(this).attr('forum_id_1'),
					forum_id_2  =0,
					article_id  =article_id,
					reply_id    =0,
					go_url      =$(this).attr('go_url')
				);
			});
		</script>
	

	</body>
					
		

</html>