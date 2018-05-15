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
				//SQL-actio log  發文內容(group)
				//-----------------------------------------------
					$sql="
						SELECT 
							`user_id`, 
							`mssr_forum_article`.`article_id`,
							`keyin_cdate` 
							
						FROM  
							`mssr`.`mssr_forum_article` 
							
						INNER JOIN `mssr`.`mssr_article_forum_rev` ON
							`mssr`.`mssr_forum_article`.`article_id`=`mssr`.`mssr_article_forum_rev`.`article_id`

							
						WHERE 1=1
								AND `mssr`.`mssr_forum_article`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 
								
									`user`.`student`.`class_code` IN ('gcp_2014_2_5_1', 'gcp_2014_2_5_2', 'gcp_2014_2_5_5')
						)
						
						ORDER BY `mssr_forum_article`.`article_id` ASC
						

					";	

				
					$array_group_article					=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$array_group_article_con				=count($array_group_article);
				
				
	
			
				
				
					
					
				
				
					
		$action_code		=array();
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
<title>發文路徑</title>
</head>

<body>
<I><B><h2 style="color:blue">發文路徑分析</h2></B></I>
<HR>
<H3>
	1為接近最發文的動作<BR>
	排除：<BR>
	g22(在群的一般討論區頁面裡，點擊發表文章按鈕)<BR>
	g2(群的頁面，點擊大家來聊書頁籤(一般討論區))<BR>
	p6(點擊人的聊書群頁面裡的其中一個聊書群)<BR>
	p10(點擊人頁的聊書小組頁籤)<BR>
	p2(點擊導覽列(我的聊書小組))<BR>
	g11(在群的頁面，點擊群首頁頁籤)<BR>
	g5在群的興趣書單頁面，點擊裡面的書<BR>
	('g22', 'g2', 'p6', 'p10', 'n2', 'g11', 'g5', 'n4')

	筆數<?php echo $array_group_article_con;?><BR>
</H3>
<BR>

群頁發文
<table width="1000" border="1" cellpadding="5" cellspacing="2">
	<thead>
        <tr color=blue>
        	
        	<th scope="col">uid</th>
            <th scope="col">article_id</th>
			<th scope="col">1</th>
			
        </tr>
   	</thead>
   	<tbody>  
	<?php 
	
		foreach($array_group_article as $array_article_v){ 
		
			$uid 				=$array_article_v['user_id'];
			$article_id			=$array_article_v['article_id'];
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
							AND `mssr`.`mssr_action_forum_log`.`action_code` NOT IN ('g22', 'g2', 'p6', 'p10', 'n2', 'g11', 'g5', 'n4')
						ORDER BY `keyin_cdate` DESC
					";					
					
				
				
					$array_article_log					=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);	
				
				
				
			
					
					
	
	?>
	<tr align=center>
			<td class=""><?php echo $uid;?><BR>(<?php echo $keyin_cdate;?>)</td>
			<td class=""><?php echo $article_id?></td>
			
			<?php 
			
				 foreach($array_article_log as $array_article_log_v){ 
				 
					if(trim($array_article_log_v['action_code']) =="g26"){
						$code_cdate = trim($array_article_log_v['keyin_cdate']);
						
						$sql="
						SELECT 
							`action_code`,`keyin_cdate`
						FROM  
							`mssr`.`mssr_action_forum_log` 
						WHERE 1=1
							AND `action_from` = {$uid}
							AND `keyin_cdate` < '$code_cdate'
							AND `mssr`.`mssr_action_forum_log`.`action_code` NOT IN ('g3','g26','g22', 'g2', 'p6', 'p10', 'n2', 'g11', 'g5', 'n4')
						ORDER BY `keyin_cdate` DESC
					";
						$array_article_log_2	=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
						$array_article_log_v['action_code'] = trim($array_article_log_2[0]['action_code']);
					}
				
					if(empty($action_code)){
					
						$action_code[0][0]		=trim($array_article_log_v['action_code']);
						$action_code[0][1]		=1;
						
					}else{
					
						foreach($action_code as $inx => $action_code_v){
							
							
						
						
							if(trim($array_article_log_v['action_code']) == trim($action_code[$inx][0])){
								
								
								$action_code[$inx][1] =$action_code[$inx][1]+1;
								break;
								
							}else if(count($action_code)-1 == $inx){
								
								$action_code[$inx+1][0] = trim($array_article_log_v['action_code']);
								$action_code[$inx+1][1] = 1;
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
		<?php 	foreach($action_code as $inx => $action_code_v){ ?>
			<tr align=center>
				<td class=""><?php echo $action_code[$inx][0];?></td>
				<td class=""><?php echo $action_code[$inx][1];?></td>
			<tr>
		<?php } ?>
		</tbody>
	</table>
	



	
	








</body>
</html>

