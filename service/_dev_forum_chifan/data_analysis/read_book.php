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
				//SQL-撈出各班學生

				//-----------------------------------------------
					
					$sql="
							SELECT 
								`user`.`student`.`uid`,
								`user`.`student`.`class_code`
								
							FROM 
								`user`.`student`
							WHERE 1=1
							
									AND `user`.`student`.`class_code` IN ('gcp_2014_2_5_1', 'gcp_2014_2_5_2', 'gcp_2014_2_5_5')
							
						
							ORDER BY `user`.`student`.`class_code` ASC;
					";						
					$array_result_uid		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
						
					
					
	
				
				

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>閱讀書籍數量</title>
</head>

<body>
<I><B><h2 style="color:blue">閱讀書籍數量</h2></B></I>
<HR>


<table width="1000" border="1" cellpadding="5" cellspacing="2">
	<thead>
        <tr color=blue>
        	
        	<th scope="col">班級</th>
            <th scope="col">UID</th>
			<th scope="col">2、3月閱讀數(前)</th>
			<th scope="col">4、5月閱讀數(後)</th>
        </tr>
   	</thead>
	<?php 
		foreach($array_result_uid as $array_result_uid_v){
			$user_id 	 =$array_result_uid_v['uid'];
			$class_code  =$array_result_uid_v['class_code'];
		
	
	
				$sql="
						SELECT
							COUNT(`mssr`.`mssr_book_borrow_log`.`book_sid`) AS book_borrow_con

						FROM `mssr`.`mssr_book_borrow_log`

					
						WHERE 1=1

								AND `mssr`.`mssr_book_borrow_log`.`user_id` =$user_id
								AND `mssr`.`mssr_book_borrow_log`.`borrow_sdate` BETWEEN
								'2015-02-01 00:00:00' AND '2015-03-31 00:00:00'
					";
				$array_result_borrow_book_1		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);					


				$sql="
						SELECT
							COUNT(`mssr`.`mssr_book_borrow_log`.`book_sid`) AS book_borrow_con

						FROM `mssr`.`mssr_book_borrow_log`

					
						WHERE 1=1

								AND `mssr`.`mssr_book_borrow_log`.`user_id` =$user_id
								AND `mssr`.`mssr_book_borrow_log`.`borrow_sdate` BETWEEN
								'2015-04-01 00:00:00' AND '2015-05-31 00:00:00'
					";
				$array_result_borrow_book_2		=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
						
	
	?>
		<tbody>  
		
			<tr align=center>
			
					<td class=""><?php echo $class_code; ?></td>
					<td class=""><?php echo $user_id; ?></td>
					<td class=""><?php echo $array_result_borrow_book_1[0]['book_borrow_con']; ?></td>
					<td class=""><?php echo $array_result_borrow_book_2[0]['book_borrow_con']; ?></td>
				  
					
					
			</tr>

		</tbody>
		
	<?php } ?>
</table>

</body>
</html>



 

 
 

  
  
  

