<?php 
//-------------------------------------------------------
//mssr_forum
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------
        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",3).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code'
       	);
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

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
					

				//-----------------------------------------------
				//SQL-actio log  發文內容(book)
				//-----------------------------------------------
					$sql="
						SELECT 
							`user_id`, 
							`mssr_forum_article_reply`.`reply_id`,
							`keyin_cdate` 
							
						FROM  
							`mssr`.`mssr_forum_article_reply` 
							
						INNER JOIN `mssr`.`mssr_article_reply_book_rev` ON
							`mssr`.`mssr_forum_article_reply`.`reply_id`=`mssr`.`mssr_article_reply_book_rev`.`reply_id`

							
						WHERE 1=1
								AND `mssr`.`mssr_forum_article_reply`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 
								
									`user`.`student`.`class_code` IN ('gcp_2014_2_5_1', 'gcp_2014_2_5_2', 'gcp_2014_2_5_5')
						)
						
						ORDER BY `mssr_forum_article_reply`.`reply_id` ASC
						

					";	

				
					$array_book_article					=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$array_book_article_con				=count($array_book_article);
				
				
	
			
				
				
					
					
				
				
					
		$book_action_code	=array();
		$group_action_code	=array();
					
		// echo "<pre>";
		// print_r($array_book_article2);
		// echo "</pre>";
		// die();
					
					
				
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>回文路徑</title>
</head>

<body>
<I><B><h2 style="color:blue">回文路徑分析</h2></B></I>
<HR>
<H3>
	1為接近最發文的動作<BR>
	排除：<BR>
	b7(在書的頁面，點擊進入某篇討論串內)<BR>
	ba9(在書的回覆頁面，點擊回覆文章按鈕)<BR>
	
	筆數<?php echo $array_book_article_con;?><BR>
</H3>
<BR>

書頁回文
<table width="1000" border="1" cellpadding="5" cellspacing="2">
	<thead>
        <tr color=blue>
        	
        	<th scope="col">uid</th>
            <th scope="col">reply_id</th>
			<th scope="col">1</th>
			
        </tr>
   	</thead>
   	<tbody>  
	<?php 
	
		foreach($array_book_article as $array_article_v){ 
		
			$uid 				=$array_article_v['user_id'];
			$article_id			=$array_article_v['reply_id'];
			$keyin_cdate		=$array_article_v['keyin_cdate'];
			
			
			
		
				//-----------------------------------------------
				//SQL-actio log  action_log
				//-----------------------------------------------
					$sql="
						SELECT 
							`action_code`,`keyin_cdate`
						FROM  
							`mssr`.`mssr_action_forum_log` 
						WHERE 1=1
							AND `action_from` = {$uid}
							AND `keyin_cdate` < '$keyin_cdate'
							AND `mssr`.`mssr_action_forum_log`.`action_code` NOT IN ('b7', 'ba9', 'n4')
						ORDER BY `keyin_cdate` DESC
					";					
					
						
				
					$array_article_log					=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);	
				
				
				
			
					
					
	
	?>
	<tr align=center>
			<td class=""><?php echo $uid;?><BR>(<?php echo $keyin_cdate;?>)</td>
			<td class=""><?php echo $article_id?></td>
			
			<?php 
			
				 foreach($array_article_log as $array_article_log_v){ 
				 
				
				
					if(empty($book_action_code)){
					
						$book_action_code[0][0]		=trim($array_article_log_v['action_code']);
						$book_action_code[0][1]		=1;
						
					}else{
					
						foreach($book_action_code as $inx => $book_action_code_v){
							
							
						
						
							if(trim($array_article_log_v['action_code']) == trim($book_action_code[$inx][0])){
								
								
								$book_action_code[$inx][1] =$book_action_code[$inx][1]+1;
								break;
								
							}else if(count($book_action_code)-1 == $inx){
								
								$book_action_code[$inx+1][0] = trim($array_article_log_v['action_code']);
								$book_action_code[$inx+1][1] = 1;
							}
							
						
						}
					}
					
					
					
				
			
			?>
				<td class="">
					<?php echo $array_article_log_v['action_code'];?>
					<BR>
					<!--(<?php echo $array_article_log_v['keyin_cdate'];?>)-->
				</td>
			<?php } ?>
    </tr>
	
	<?php }  ?>
	
		</tbody>
</table>	
			
		
<P> <HR><P>
	<table width="500" border="1" cellpadding="5" cellspacing="2">
		<thead>
			<tr color=blue>
				
				<th scope="col">action_code</th>
				<th scope="col">次數</th>
				
			</tr>
		</thead>
		<tbody>  
		<?php 	foreach($book_action_code as $inx => $book_action_code_v){?>
			<tr align=center>
				<td class=""><?php echo $book_action_code[$inx][0];?></td>
				<td class=""><?php echo $book_action_code[$inx][1];?></td>
			<tr>
		<?php } ?>
		</tbody>
	</table>
	



	
	








</body>
</html>

