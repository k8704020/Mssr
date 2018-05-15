<!DOCTYPE html>
<? session_start(); ?>
	<head>

		<?php
		//---------------------------------------------------
		//設定與引用
		//---------------------------------------------------
		
			//SESSION
			@session_start();
		
			//啟用BUFFER
			@ob_start();
		
			//外掛設定檔
			require_once(str_repeat("../",5)."/config/config.php");
			require_once(str_repeat("../",5)."/inc/get_book_info/code.php");
		
			 //外掛函式檔
			$funcs=array(
						APP_ROOT.'inc/code',
						APP_ROOT.'lib/php/db/code',
						APP_ROOT.'lib/php/array/code'
						);
			func_load($funcs,true);
			
		
			//清除並停用BUFFER
			@ob_end_clean();
			
			//建立連線 user
			$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
		//---------------------------------------------------
		//END   設定與引用
		//---------------------------------------------------		 
		
		//---------------------------------------------------
		//CSS 設定
		//---------------------------------------------------
		?>
		<style>
            body
            {}
			/*灰階特效*/
			.grays { 
				
            }
			table { 
table-layout: fixed;
word-wrap:break-word;
white-space:nowrap;
overflow:hidden;
}
div { 
table-layout: fixed;
word-wrap:break-word;
white-space:nowrap;
overflow:hidden;
}
        </style>
	</head>

<body>
		
		
        
        
       
        <?
        //預設讀入的資料
		
		//GET
		$user_id = (int)$_GET["user_id"];
		$branch_id = (int)$_GET["branch_id"];
		$task_time = $_GET["task_time"];
		$task_number_max = $_GET["task_number"];
		$task_number = 0;
		$data = array();
		
		$sql = "SELECT
				x.book_sid,
				x.rec_stat_cno,
				x.rec_draw_cno,
				x.rec_text_cno,
				x.rec_record_cno,
				read_state
			FROM
			(SELECT 
						b.book_sid,
						rec_stat_cno,
						rec_draw_cno,
						rec_text_cno,
						rec_record_cno
					FROM 
					(
						SELECT a.mintime,
						       a.book_sid,
							   a.user_id
						FROM 
						(
							SELECT MIN(c.mintime) AS mintime,
								   c.book_sid,
								   c.user_id
							FROM
							(						
								SELECT   mssr_book_read_opinion_log.keyin_cdate AS mintime,
										`book_sid`,
										`user_id`
								FROM `mssr_book_read_opinion_log` 
								WHERE `user_id` ='{$user_id}'
								AND keyin_cdate  >=  '2014-04-21 00:00:00'
								
							)AS c
							GROUP BY c.mintime
						)AS a
						WHERE a.mintime   >=  '".$task_time."'
					)AS b
					LEFT JOIN mssr_rec_book_cno_semester
					ON b.book_sid = mssr_rec_book_cno_semester.book_sid
									AND b.user_id = mssr_rec_book_cno_semester.user_id
					
					LEFT JOIN mssr_book_category_rev
					ON mssr_book_category_rev.book_sid = b.book_sid
					
					LEFT JOIN mssr_book_category
					ON mssr_book_category_rev.cat_code = mssr_book_category.cat_code
					AND mssr_book_category.school_code = 'gcp'
					
					LEFT JOIN mssr_branch
					ON mssr_branch.branch_name = mssr_book_category.cat_name
					WHERE mssr_branch.branch_id = '{$branch_id}'
					
					
					GROUP BY b.book_sid)AS x
		LEFT JOIN mssr_rec_teacher_read
		ON {$user_id} = mssr_rec_teacher_read.user_id
		AND mssr_rec_teacher_read.book_sid = x.book_sid
		";
		
		$retrun_2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($retrun_2 as $key2=>$val2)
		{
			$data[$key2]['book_name'] = "" ;
			$data[$key2]['book_rec'] = 0 ;
			$data[$key2]['read_state'] = 0 ;
			
			$re_count = 0;
			$data[$key2]['book_sid'] = $val2["book_sid"];
			
			
			if($val2["rec_stat_cno"] > 0) $re_count++;
			if($val2["rec_draw_cno"] > 0) $re_count++;
			if($val2["rec_text_cno"] > 0) $re_count++;
			if($val2["rec_record_cno"] > 0) $re_count++;
			$data[$key2]['type'] = 0;
			if($val2["read_state"]==1)
			{
				$data[$key2]['read_read'] = 1;//設定有無被觀閱
			}
			else
			{
				$data[$key2]['read_read'] = 0;
				$data[$key2]['type'] = 1;
			}
			if($re_count >= 2)
			{
				$data[$key2]['book_rec'] = 1; //設定有無推薦
				
			}else
			{
				$data[$key2]['book_rec'] = 0;
				$data[$key2]['type'] = 2;
			}
			$array_select = array("book_name");
			$get_book_info=get_book_info($conn='',$val2['book_sid'],$array_select,$arry_conn_mssr);
			$data[$key2]['book_name'] = $get_book_info[0]["book_name"];
			
			if($data[$key2]['type'] == 0) $task_number++;
		}
		
		
		?>
        
        
		<!--========================================================
		//DEBUG文字列
		//========================================================-->
		
		<script language="javascript">
		
		//========================================================
		//建立
		//========================================================
	
		//========================================================
		//圖片預載
		//========================================================
		

		
		</script>
        <!--======================================================
		//Html 內文
		========================================================-->
        <table style="position:absolute; top:30px; left:0px;" cellpadding="0" cellspacing="0" border="0">
        <?php for($i = 0 ; $i < sizeof($data);$i++){ ?>
        	<tr style="overflow:hidden; background-color:#FFF; ">
            	<td width="150">
                	<div style="position: inherit; top:0px; width:150px;"><?php echo $data[$i]['book_name'] ;?></div>
                </td>
                <td  width="50" style=" color:#390;overflow:hidden; ">
                	完成
                </td>
                <td style=" color:<?php if($data[$i]['book_rec']==1){echo '#390';}else{echo '#F30';} ?>" width="80">
                	<?php if($data[$i]['book_rec']==1){echo '已完成';}else{echo '尚未完成';} ?>
                </td>
                <td style=" color:<?php if($data[$i]['read_read']==1){echo '#390';}else{echo '#F30';} ?>" width="80">
                	<?php if($data[$i]['read_read']==1){echo '已核准';}else{echo '尚未核准';} ?>
                </td>
                <td width="60" style=" color:<?php if($data[$i]["type"] == 1){echo '#F30'; }else {echo '#390';}?>">
                	<?php if($data[$i]["type"] == 2){ ?>
                    <img src="./img/btn1.png" style="cursor:pointer;" width="68" height="22" onClick="go_rec('<?php echo htmlspecialchars($data[$i]['book_sid']);?>','<?php echo htmlspecialchars($data[$i]['book_name']);?>')">
                	<?php }else if($data[$i]["type"] == 1){ ?>
                    請老師核准
                	<?php }else{?>
                    完美!
                    <?php } ?>
                </td>
            </tr>
			<?php } ?>
        </table>
        <table height="28" style="position:fixed; top:0px; left:0px; color:#FFFFFF; font-weight: bold;" cellpadding="0" cellspacing="0" border="0" bgcolor="#339999" >
        	<tr>
            	<td width="150">
                書籍名稱
                </td>
                <td width="50">
                	閱讀
                </td>
                <td width="80">
                	推薦
                </td>
                <td width="80">
                	教師觀閱
                </td>
                <td width="80">
                	建議
                </td>
            </tr>
        </table>
        
        
        
		<script>
		//========================================================
		//JS Function
		//========================================================
		function go_rec(value,value2){

            setTimeout(function(){
                window.parent.parent.location.replace( "../../../../bookstore/code.php?do=rec&book_sid="+value+"&book_name="+value2);
            }, 500);
		}
		window.parent.set_task_bar(<?php echo $task_number;?>,<?php echo $task_number_max;?>);
		</script>
</body>