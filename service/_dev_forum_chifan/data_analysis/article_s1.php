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
				//SQL-我想要分享(小說類1)

				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`article_id`
							
						FROM `mssr`.`mssr_forum_article`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

						WHERE 1=1
							   AND `mssr`.`mssr_forum_article`.`cat_id` LIKE '1%'
							   
								AND `mssr`.`mssr_forum_article`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 
								
									`user`.`student`.`class_code` IN ('gcp_2014_2_5_1', 'gcp_2014_2_5_2', 'gcp_2014_2_5_5')
						)						

					";						
						
				
					$article_s1		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$article_s1_con 	=count($article_s1);
					
					
				//-----------------------------------------------
				//SQL-我想要問(小說類2)
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`article_id`
							
						FROM `mssr`.`mssr_forum_article`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

						WHERE 1=1
							   AND `mssr`.`mssr_forum_article`.`cat_id` LIKE '2%'
							   
								AND `mssr`.`mssr_forum_article`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 
								
									`user`.`student`.`class_code` IN ('gcp_2014_2_5_1', 'gcp_2014_2_5_2', 'gcp_2014_2_5_5')
						)						

					";						
						
				
					$article_s2			=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$article_s2_con 	=count($article_s2);
					
					
				//-----------------------------------------------
				//SQL-我想分享(非小說類3)
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`article_id`
							
						FROM `mssr`.`mssr_forum_article`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

						WHERE 1=1
							   AND `mssr`.`mssr_forum_article`.`cat_id` LIKE '3%'
							   
								AND `mssr`.`mssr_forum_article`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 
								
									`user`.`student`.`class_code` IN ('gcp_2014_2_5_1', 'gcp_2014_2_5_2', 'gcp_2014_2_5_5')
						)						

					";						
						
				
					$article_s3		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$article_s3_con 	=count($article_s3);
					
					
				//-----------------------------------------------
				//SQL-我想要問(非小說類4)
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`article_id`
							
						FROM `mssr`.`mssr_forum_article`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

						WHERE 1=1
							   AND `mssr`.`mssr_forum_article`.`cat_id` LIKE '4%'
							   
								AND `mssr`.`mssr_forum_article`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 
								
									`user`.`student`.`class_code` IN ('gcp_2014_2_5_1', 'gcp_2014_2_5_2', 'gcp_2014_2_5_5')
						)						

					";	
				
						
				
					$article_s4			=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$article_s4_con 	=count($article_s4);
					
					
				//-----------------------------------------------
				//SQL-鷹架分類
				//-----------------------------------------------
					$sql="
						SELECT
 
							`cat_id`, count(cat_id) as cat_id_con
							
						FROM `mssr`.`mssr_forum_article`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

						WHERE 1=1
							
							   
								AND `mssr`.`mssr_forum_article`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 
								
									`user`.`student`.`class_code` IN ('gcp_2014_2_5_1', 'gcp_2014_2_5_2', 'gcp_2014_2_5_5')
						)
						
						GROUP BY `mssr`.`mssr_forum_article`.`cat_id`;

					";		
				
				
					$article_s_type			=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					
					
				//-----------------------------------------------
				//SQL-鷹架分類例句
				//-----------------------------------------------
					$sql="
						SELECT
 
							`article_refer_code`, count(article_refer_code) as article_refer_code_con
							
						FROM `mssr`.`mssr_forum_article`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

						WHERE 1=1
							
							   
								AND `mssr`.`mssr_forum_article`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 
								
									`user`.`student`.`class_code` IN ('gcp_2014_2_5_1', 'gcp_2014_2_5_2', 'gcp_2014_2_5_5')
						)
						
						GROUP BY TRIM(`mssr`.`mssr_forum_article`.`article_refer_code`);

					";		
			
					$article_refer_code			=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				//-----------------------------------------------
				//SQL-沒使用鷹架
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`article_refer_code`,
							`mssr`.`mssr_forum_article`.`article_content`
							
						FROM `mssr`.`mssr_forum_article`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

						WHERE 1=1
								AND `mssr`.`mssr_forum_article`.`article_refer_code` =0
								AND `mssr`.`mssr_forum_article`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 
								
									`user`.`student`.`class_code` IN ('gcp_2014_2_5_1', 'gcp_2014_2_5_2', 'gcp_2014_2_5_5')
						)
						
						

					";		
			
					$article_NS			=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$article_NS_con 	=count($article_NS);
					
					$article_word_NS=0;
					$article_word_NS_i=0;
					
					foreach($article_NS as $v){
						
						if(trim($v['article_content'])!=""){
							$article_word_NS_i ++;
							$article_word_NS = $article_word_NS + mb_strlen($v['article_content']);	
						}
					}
					$avg_article_word_ns = $article_word_NS / $article_word_NS_i;

				//-----------------------------------------------
				//SQL-有使用鷹架
				//-----------------------------------------------
					$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`article_refer_code`,
							`mssr`.`mssr_forum_article`.`article_content`
							
						FROM `mssr`.`mssr_forum_article`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

						WHERE 1=1
								AND `mssr`.`mssr_forum_article`.`article_refer_code` != 0
								AND `mssr`.`mssr_forum_article`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 
								
									`user`.`student`.`class_code` IN ('gcp_2014_2_5_1', 'gcp_2014_2_5_2', 'gcp_2014_2_5_5')
						)
						
						

					";		
			
					$article_S			=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$article_S_con 	=count($article_S);
					
					$article_word_S=0;
					$article_word_S_i=0;
					
					foreach($article_S as $v){
						
						if(trim($v['article_content'])!=""){
							$article_word_S_i ++;
							$article_word_S = $article_word_S + mb_strlen($v['article_content']);	
						}
					}
					$avg_article_word_s = $article_word_S / $article_word_S_i;
					
					
				
										
				

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>鷹架資料分析</title>
</head>

<body>
<I><B><h2 style="color:blue">鷹架資料分析</h2></B></I>
<HR>

<h3>有使用鷹架VS沒有使用鷹架</h3>
<table width="1000" border="1" cellpadding="5" cellspacing="2">
	<thead>
        <tr color=blue>
        	
        	<th scope="col">有使用鷹架(平均字數)</th>
            <th scope="col">沒有使用鷹架(平均字數)</th>
        </tr>
   	</thead>
   	<tbody>  
	
	<tr align=center>
	
			<td class=""><?php echo $article_S_con;?>(<?php echo (int)$avg_article_word_s;?>)</td>
			<td class=""><?php echo $article_NS_con;?>(<?php echo (int)$avg_article_word_ns;?>)</td>
          
			
			
        </tr>

	</tbody>
</table>

<h3>小說類VS非小說類</h3>
<table width="1000" border="1" cellpadding="5" cellspacing="2">
	<thead>
        <tr color=blue>
        	
        	<th scope="col">小說類</th>
            <th scope="col">非小說類</th>
        </tr>
   	</thead>
   	<tbody>  
	
	<tr >
	
			<td class=""><?php echo $article_s1_con + $article_s2_con;?></td>
			<td class=""><?php echo $article_s3_con + $article_s4_con;?></td>
          
			
			
        </tr>

	</tbody>
</table>
	

<h3>鷹架種類</h3>
<table width="1000" border="1" cellpadding="5" cellspacing="2">
	<thead>
        <tr color=blue>
        	
        	<th scope="col">我想要分享(小說類1)</th>
            <th scope="col">我想要問(小說類2)</th>
            <th scope="col">我想要分享(非小說類3)</th>
            <th scope="col">我想要問(非小說類4)</th>
        
        </tr>
   	</thead>
   	<tbody>  
	
	
		<tr >
	
			<td class=""><?php echo $article_s1_con;?></td>
            <td class=""><?php echo $article_s2_con;?></td>
			<td class=""><?php echo $article_s3_con;?></td>
			<td class=""><?php echo $article_s4_con;?></td>
			
			
        </tr>

	</tbody>
</table>

<h3>鷹架分類</h3>
<table width="500" border="1" cellpadding="5" cellspacing="2">
	<thead>
        <tr color=blue>
        	
        	<th scope="col">鷹架分類(代號)</th>
            <th scope="col">發文數</th>
        
        </tr>
   	</thead>
   	<tbody>  
	
	
		
		
			<?php 
			foreach($article_s_type as $inx=> $v){ 
			?>
				<tr >
					<td class=""><?php echo $v['cat_id'];?></td>
					<td class=""><?php echo $v['cat_id_con'];?></td>
					
						
				</tr>
			<?php } ?>
			
			
        

	</tbody>
</table>


<h3>鷹架例句</h3>
<table width="500" border="1" cellpadding="5" cellspacing="2">
	<thead>
        <tr color=blue>
        	
        	<th scope="col">鷹架例句代號</th>
            <th scope="col">發文數</th>
        
        </tr>
   	</thead>
   	<tbody>  
	
	
		
		
			<?php 
			foreach($article_refer_code as $inx=> $v){ 
			?>
				<tr >
					<td class=""><?php echo $v['article_refer_code'];?></td>
					<td class=""><?php echo $v['article_refer_code_con'];?></td>
					
						
				</tr>
			<?php } ?>
			
			
        

	</tbody>
</table>

</body>
</html>



 

 
 

  
  
  

