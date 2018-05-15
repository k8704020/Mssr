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
			//SQL-二班發表文章
			//-----------------------------------------------
			$sql="
				SELECT
 
					`mssr`.`mssr_forum_article`.`user_id`,
					`mssr`.`mssr_forum_article`.`article_id`,
					`mssr`.`mssr_forum_article`.`article_content`
						
				FROM `mssr`.`mssr_forum_article`

				INNER JOIN `user`.`member` ON
					`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

				WHERE 1=1

				AND `mssr`.`mssr_forum_article`.`user_id` IN (
					SELECT `user`.`student`.`uid`
					FROM `user`.`student`
					WHERE 1=1
						AND `user`.`student`.`class_code` IN ('gcp_2014_2_5_5')
							
				)
			";						
						
			
			$db_results		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			
					
			
			

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>轉TXT檔</title>
</head>

<body>
<I><B><h2 style="color:blue">轉TXT檔</h2></B></I>
<HR>


<table width="1000" border="1" cellpadding="5" cellspacing="2">
	<thead>
        <tr color=blue>
        	
        	<th scope="col">uid</th>
            <th scope="col">article_id</th>
        </tr>
   	</thead>
   	<tbody>  
	
	<?php
		foreach($db_results as $db_result){
		
			$uid				=(int)$db_result['user_id'];
			$article_id 		=(int)$db_result['article_id'];
			$article_content 	=trim($db_result['article_content']);
			
			if($article_content===""){
				continue;
			}
			
			$fp = fopen('content_txt/5_5/'.$uid.'_'.$article_id.'.txt', 'w');
			fwrite($fp, $article_content);
			fclose($fp);
	?>
		
		<tr align=center>
			
				<td class=""><?php echo $uid;?></td>
				<td class=""><?php echo $article_id;?>檔案已匯出成txt檔</td>		
		</tr>
		
	<?php } ?>
	
	

	</tbody>
</table>


</body>
</html>



 

 
 

  
  
  

