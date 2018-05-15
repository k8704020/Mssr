<!DOCTYPE html>
<? session_start(); ?>
	<head>

		<script src="../../../lib/jquery/basic/code.js"></script>
		<script src="../../../lib/jquery/plugin/code.js"></script>
		<?php
		//---------------------------------------------------
		//設定與引用
		//---------------------------------------------------
		
			//SESSION
			@session_start();
		
			//啟用BUFFER
			@ob_start();
		
			//外掛設定檔
			require_once(str_repeat("../",3)."/config/config.php");
			require_once(str_repeat("../",3)."/inc/get_book_info/code.php");
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
			word-break: break-all;
			overflow:hidden;
			style="table-layout:fixed;
			word-wrap:break-word;
			word-break;
			break-all;
			overflow:hidden;
			white-space: nowrap;
			}
        </style>
	</head>

<body  bgcolor="#FFFFFF">
		
		
        
        
       
        <?
        //預設讀入的資料
		
		//GET
		$user_id = (int)$_GET["user_id"];
		$branch_id = (int)$_GET["branch_id"];
		$data_array = array();
	 	$sql = "SELECT 
				b.book_sid,
				b.rec_stat_cno,
				b.rec_draw_cno,
				b.rec_text_cno,
			    b.rec_record_cno,
				b.read_state
		FROM 
		(
			SELECT 
			a.mintime,
			a.book_sid,
			a.user_id,
			a.rec_stat_cno,
			a.rec_draw_cno,
			a.rec_text_cno,
			a.rec_record_cno,
			read_state
			FROM 
			(
				SELECT   MIN(mssr_rec_book_cno_semester.keyin_cdate) AS mintime,
						rec_stat_cno,
						rec_draw_cno,
						rec_text_cno,
						rec_record_cno,
						`book_sid`,
						`user_id`
				FROM `mssr_rec_book_cno_semester` 
				WHERE `user_id` ='{$user_id}'
				GROUP BY mssr_rec_book_cno_semester.`book_sid`
			)AS a
			LEFT JOIN mssr_rec_teacher_read
			ON mssr_rec_teacher_read.book_sid = a.book_sid 
			AND mssr_rec_teacher_read.user_id = a.user_id

			WHERE a.mintime   >=  '2013-09-01'
			
		)AS b
		
		
		LEFT JOIN mssr_book_category_rev
		ON mssr_book_category_rev.book_sid = b.book_sid
					
		LEFT JOIN mssr_book_category
		ON mssr_book_category_rev.cat_code = mssr_book_category.cat_code
		
					
		LEFT JOIN mssr_branch
		ON mssr_branch.branch_name = mssr_book_category.cat_name
		
		WHERE mssr_branch.branch_id = '{$branch_id}'
		AND mssr_book_category.school_code = 'gcp'

		GROUP BY b.book_sid";
		
		/*
		AND read_state = 1
		*/
		$count = 0 ;
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);	
		foreach($retrun as $key2=>$val2)
		{
			
			
			$sql = "SELECT count(1) AS count
					FROM  `mssr_branch_shelf`
					WHERE book_on_shelf_state = '$branch_id'
					AND book_sid = '".$val2['book_sid']."'
					AND user_id = '$user_id';
					
			";
			$retrun2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);	
			if($retrun2[0]["count"]==0)
			{
				$array_select = array("book_name");
				$get_book_info=get_book_info($conn='',$val2['book_sid'],$array_select,$arry_conn_mssr);
				$data_array[$count]['book_name'] = $get_book_info[0]["book_name"];
				
				if($val2['read_state'] !=1)
				{
					$data_array[$count]['type'] ='未被許可上架';
					$data_array[$count]['read_state'] = 0;
				}
				else
				{
					$data_array[$count]['type'] ='可以上架';
					$data_array[$count]['read_state'] = 1;
				}
				$data_array[$count]['book_sid'] = $val2['book_sid'];
				$count++;
			}
		}
		?>
        
        
		<!--========================================================
		//DEBUG文字列
		//========================================================-->
		<div style="left:0px; top:480px; position: absolute;" id="debug"></div>
		<script language="javascript">
		
		//事件 :  遮罩開關
		function set_hide(open_on,text)
		{
			if(open_on==false)
			{
				//$.unblockUI();  
			}else if(open_on==true)
			{
				//$.blockUI({ message: '<div style="z-index: 2000;">'+text+'</div>'});
			}
		}
		//set_hide(1,"讀取中");
		//========================================================
		//建立
		//========================================================
		var user_id = <? echo $user_id; ?>;
		var branch_id = <? echo $branch_id;?>;
		
		var up_book_array = new Array();
		var up_book_array_sid = new Array();
		<? for($i = 0 ; $i < sizeof($data_array);$i++)
		{?>
		up_book_array[<? echo $i; ?>] = '<? echo $data_array[$i]['read_state']; ?>';
		up_book_array_sid[<? echo $i; ?>] = '<? echo $data_array[$i]['book_sid']; ?>';
		<? }?>
		var click_book_list_number = -1;//選擇的書籍列編號
		var book_list_size = <? echo sizeof($data_array);?>; //書籍列長度
		
		//========================================================
		//圖片預載
		//========================================================
		
		</script>
        
        <!--======================================================
		//Html 內文
		========================================================-->
        
        <!-- 背景 -->
        <table cellpadding="0" border="0" cellspacing="0">            
			<?php
            for($i= 0 ; $i < sizeof($data_array);$i++)
            {?>
            <tr id="bar_<?php echo $i;?>" onMouseOver="over_bar('<?php echo $i;?>','<?php echo $data_array[$i]['read_state'];?>')" onClick="click_bar('<?php echo $i;?>','<?php echo $data_array[$i]['read_state'];?>')" style=" color:#<? if($data_array[$i]['read_state'] ==1){echo "000000;cursor:pointer";}else{echo "AAAAAA;cursor:no-drop";}?>; ">
            	<td width="291"><?php echo $data_array[$i]['book_name'];?></td>
            	<td width="140"><?php echo $data_array[$i]['type'];?></td>
            </tr>
            <?php
            }		
            ?>
        </table>
       	<!--     -->
        
        
		<script>
		//========================================================
		//JS Function
		//========================================================
		//點選bar時的顯示
		function click_bar(value,ok)
		{
			parent.add_debug("click_bar:開始:"+ok);
			for(i=0;i<book_list_size;i++)
			{
				window.document.getElementById('bar_'+i).style.backgroundColor = "#ffffff";
			}
			if(ok == 1)
			{
				window.parent.document.getElementById("set_up_book_btn").style.display = "block";
				window.document.getElementById('bar_'+value).style.backgroundColor = "#DDFFCC";
				click_book_list_number = value;
				window.parent.click_book_list_sid = up_book_array_sid[value];
			}
			if(click_book_list_number!=-1)window.document.getElementById('bar_'+click_book_list_number).style.backgroundColor = "#DDFFCC";
		}
		//掃過BAR時的顯示
		function over_bar(value,ok)
		{
			parent.add_debug("over_bar:開始:"+up_book_array[value]+"  "+click_book_list_number+"  "+value);
			for(i=0;i<book_list_size;i++)
			{
				window.document.getElementById('bar_'+i).style.backgroundColor = "#ffffff";
			}
			if(ok == 1)
			{
				window.document.getElementById('bar_'+value).style.backgroundColor = "#ffffDD";
				if(click_book_list_number!=-1)window.document.getElementById('bar_'+click_book_list_number).style.backgroundColor = "#DDFFCC";
			}
			if(click_book_list_number!=-1)window.document.getElementById('bar_'+click_book_list_number).style.backgroundColor = "#DDFFCC";
		}
		</script>
</body>