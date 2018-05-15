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
			//require_once(str_repeat("../",5)."/inc/get_book_info/code.php");
		
			 //外掛函式檔
			/*$funcs=array(
						APP_ROOT.'inc/code',
						APP_ROOT.'lib/php/db/code',
						APP_ROOT.'lib/php/array/code'
						);
			func_load($funcs,true);*/
			
		
			//清除並停用BUFFER
			@ob_end_clean();
			
			//建立連線 user
			//$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
		//---------------------------------------------------
		//END   設定與引用
		//---------------------------------------------------		 
		
		//---------------------------------------------------
		//CSS 設定
		//---------------------------------------------------
		?>
		<style>
            body
            {overflow:hidden;
            position:relative;}
			/*灰階特效*/
			.bar { 
			background: rgb(183,222,237); /* Old browsers */
			background: -moz-linear-gradient(top, rgba(183,222,237,1) 0%, rgba(113,206,239,1) 20%, rgba(34,153,226,1) 23%, rgba(34,153,226,1) 65%, rgba(183,222,237,1) 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(183,222,237,1)), color-stop(20%,rgba(113,206,239,1)), color-stop(23%,rgba(34,153,226,1)), color-stop(65%,rgba(34,153,226,1)), color-stop(100%,rgba(183,222,237,1))); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top, rgba(183,222,237,1) 0%,rgba(113,206,239,1) 20%,rgba(34,153,226,1) 23%,rgba(34,153,226,1) 65%,rgba(183,222,237,1) 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top, rgba(183,222,237,1) 0%,rgba(113,206,239,1) 20%,rgba(34,153,226,1) 23%,rgba(34,153,226,1) 65%,rgba(183,222,237,1) 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top, rgba(183,222,237,1) 0%,rgba(113,206,239,1) 20%,rgba(34,153,226,1) 23%,rgba(34,153,226,1) 65%,rgba(183,222,237,1) 100%); /* IE10+ */
			background: linear-gradient(to bottom, rgba(183,222,237,1) 0%,rgba(113,206,239,1) 20%,rgba(34,153,226,1) 23%,rgba(34,153,226,1) 65%,rgba(183,222,237,1) 100%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b7deed', endColorstr='#b7deed',GradientType=0 ); /* IE6-9 */
            }
			
			
        </style>
	</head>

<body>
		
		
        
        
        <div id="debug" style="position:absolute; top:505px; left:8px;">fffffffffff</div>
        <?
        //預設讀入的資料
		
		//GET
		$user_id = (int)$_GET["user_id"];
		$branch_id = (int)$_GET["branch_id"];
		$task_number = (int)$_GET["task_number"];
		$task_time = $_GET["task_time"];
		
		$data = array();
		
		
		
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
		function add_debug(value)
		{
			if(1)
			{
				window.document.getElementById("debug").innerHTML = value+"<br>"+window.document.getElementById("debug").innerHTML;
			}
		}
		//set_hide(1,"讀取中");
		//========================================================
		//建立
		//========================================================
		var user_id = <? echo $user_id; ?>;
		var branch_id = <? echo $branch_id;?>;
		var task_number = <? echo $task_number;?>; 
		var task_time =  <? echo $task_time; ?>;
		var up_book_array = new Array();
	
		//========================================================
		//圖片預載
		//========================================================
		
		//===========設定圖片預載陣列==============
		add_debug("圖片預載:開始");

		
		</script>
        <!--======================================================
		//Html 內文
		========================================================-->
        <!--   背景-->
        <img src="img/mission_back.png" style=" position:absolute;left:0px;top:0px;">
        <img src="img/saver_f.png" style="position:absolute; left:216px; top:99px;">
        <!--   任務說明-->
        <div align="center"  id = "mission_content_tittle"  style="left:16px; width:330px; top:80px; position: absolute; font-weight: bold; font-size:30px;" class="title_0">
預售任務(閱讀+推薦)</div>
<div id = "mission_content"  style="left:14px; width:330px; top:120px; position: absolute;" class="title_1">
            根據市場調查。<BR>
            最近客人比較喜歡買<div style="color:#FF0000;display:inline;font-size:30px;" id="mission_content_branch"><? echo $_GET["name"];?></div>
            <BR>類的書籍。<BR>
			右邊為幾位客人的意見。<BR>
			<BR>
			店長您覺得哪位的意見<BR>比較好呢？
		</div>
        <!--   任務項目-->
<iframe src="./task_list.php?user_id=<? echo $user_id; ?>&branch_id=<? echo $branch_id;?>&task_number=<? echo $task_number;?>&task_time=<? echo $task_time; ?>"  style="position:absolute; top:91px; left:375px; width: 439px; height: 198px;" width="300" height="200"></iframe>
<div align="center"  id = "mission_content_tittle"  style="left:441px; width:330px; top:23px; position: absolute; font-weight: bold; font-size:30px;" class="title_0">任務完成項目</div>
		<!--   任務BAR-->
        <div style="position:absolute; top:66px; left:376px; background-color:#333333; height:13px; width:294px;"></div>
        <div id="task_bar" style="position:absolute; top:69px; left:379px; height:8px; width:288px;" class="bar"></div>
        <div align="center"  id = "task_bar_text"  style="left:660px; width:160px; top:56px; position: absolute; font-weight: bold; font-size:26px;" class="title_0"></div>
        <!--   離開紐-->
        <img src="./img/out.png" onClick="out()" width="94" style="position:absolute; top:311px; left:749px; width: 77px;">
		<script>
		//========================================================
		//JS Function
		//========================================================
		function set_task_bar(number,max_number)
		{
			
			var tmp = parseInt(number/max_number*100);
			if(max_number <= number)tmp=100;
			window.document.getElementById("task_bar").style.width  = tmp*288/100+"px";
			window.document.getElementById("task_bar_text").innerHTML = tmp+"%("+number+"/"+max_number+")";
		}
		function out()
		{
			window.parent.document.getElementById("task_info_page").innerHTML ="";
		}
		
		</script>
</body>