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


     			
     			$sql="
						SELECT
							`mssr`.`mssr_forum_article`.`user_id`,
							`mssr`.`mssr_forum_article`.`article_id`,
							`mssr`.`mssr_forum_article`.`keyin_cdate`,
                            `user`.`student`.`class_code`

						
						FROM `mssr`.`mssr_forum_article`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                        INNER JOIN  `user`.`student` ON 
                           `user`.`member`.`uid`=`user`.`student`.`uid`
                           

		

						WHERE 1=1

						AND `mssr`.`mssr_forum_article`.`user_id` IN (
							SELECT `user`.`student`.`uid`
							FROM `user`.`student`
							WHERE 1=1
								AND `user`.`student`.`class_code`in('gcp_2014_2_5_1','gcp_2014_2_5_5','gcp_2014_2_5_2')											
						)
                         AND `student`.`start` < CURDATE()
                         AND `student`.`end` > CURDATE()
                         order by article_id										
					";					
					
					$article_class =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			            // echo '<pre>';
                        // print_r($article_class);		

                    foreach ($article_class as $key => $value) {
                        $class_code = $value['class_code'];
                        $id =     $value['article_id'];               
                        
                            $sql="
                                    SELECT
                                        `mssr`.`mssr_forum_article_reply`.`user_id`,
                                        `mssr`.`mssr_forum_article_reply`.`article_id`,
                                        `mssr`.`mssr_forum_article_reply`.`keyin_cdate`,
                                        `user`.`student`.`class_code`

                                    
                                    FROM `mssr`.`mssr_forum_article_reply`

                                    INNER JOIN `user`.`member` ON
                                        `mssr`.`mssr_forum_article_reply`.`user_id`=`user`.`member`.`uid`
                                    INNER JOIN  `user`.`student` ON 
                                       `user`.`member`.`uid`=`user`.`student`.`uid`

                                    WHERE 1=1
                                    
                                    AND `user`.`student`.`class_code`in('gcp_2014_2_5_5','gcp_2014_2_5_2','gcp_2014_2_5_1')                                           
                                    
                                    AND `mssr`.`mssr_forum_article_reply`.`article_id` =  $id
                                    AND `student`.`start` < CURDATE()
                                    AND `student`.`end` > CURDATE() 
                                    AND `user`.`student`.`class_code` <> '$class_code'                                  
                        ";                 

                        $article = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

                        if(empty($article)){
                            $arr[] = '否';
                        }else{
                            $arr[] = '是';
                        }
                    }
                        // echo '<pre>';
                        // print_r($sql);          


?>            		


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<table border="1" align="center">
			<tr>
				<th>文章代號article_id</th>
                <th>發文者user_id</th>
                <th>是否有跨班(是、否)</th>
                <th>班級</th>
			</tr>
			
			<?php foreach($article_class as $k=>$v){ ?>
			<tr align="center">
                <td><?php echo $v['article_id']?></td>
			    <td><?php echo $v['user_id']?></td>
                <td><?php echo $arr[$k] ?></td>
                <td><?php echo $v['class_code']?></td>
			</tr>
			<?php } ?>

	</table>


</body>
</html>


 

 
 

  
  
  

