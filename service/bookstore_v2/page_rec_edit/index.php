<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 推薦價框
//(內頁)  //主頁面 or 內頁
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
					 APP_ROOT.'lib/php/db/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

    ///---------------------------------------------------
    //SESSION
    //---------------------------------------------------
   
        $user_id        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
		if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
	   //建立連線 user
		$conn_user=conn($db_type='mysql',$arry_conn_user);
		$sess_permission=addslashes(trim($_SESSION['permission']));
		$forum_flag=false;
		$sql="
			SELECT `status`
			FROM `permissions`
			WHERE 1=1
				AND `permission`='{$sess_permission}'
		";
		$db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
		if(!empty($db_results)){
			foreach($db_results as $db_result){
				$rs_status=trim($db_result['status']);
				if($rs_status==='u_mssr_forum'){$forum_flag=true;continue;}
			}
		}

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
	
	//---------------------------------------------------
	//SQL
	//---------------------------------------------------
?>
<!DOCTYPE HTML>
<Html>
<Head>
	<Title>推薦控制台</Title>    
    <link rel="stylesheet" href="../css/btn.css">
    <style>
		.grays { filter: grayscale(100%);
		 -webkit-filter: grayscale(100%);
		 -moz-filter: grayscale(100%);
		 -ms-filter: grayscale(100%);
		 -o-filter: grayscale(100%);
		 }
      
		.comment_box_on
		{
			color:#ffffff;
			text-align: left;
			padding: 20px;
			border:2px solid;
			/* border-radius:25px; */
			-moz-border-radius:25px;
			box-shadow: 2px 2px 2px #888888;
			width:600px;
			height:150px;
			background-color:rgba(0,0,0,0.7);
			position: absolute;
			top:340px;
			left:80px;
			transition: 0.5s;
			box-sizing: border-box;
		}
		.comment_box_of
		{
			color:#ffffff;
			border:2px solid;
			text-align:center;
			border-radius:25px;
			-moz-border-radius:25px;
			box-shadow: 2px 2px 2px #888888;
			font-size:0px;
			width:0px;
			height:10px;
			background-color:rgba(0,0,0,0.1);
			position: absolute;
			top:340px;
			left:50px;
			transition: 0.5s;
		}
		
		
		.madal_1{
			position:absolute;
			width:77px;
			height:163px;
			background: url('./img/medal_01.png') -20px 0;
		}
		.madal_1:hover {
    		background: url('./img/medal_01.png') -20px -163px;
		}
		.madal_2{
			position:absolute;
			width:77px;
			height:163px;
			background: url('./img/medal_01.png') -98px 0;
		}
		.madal_2:hover {
    		background: url('./img/medal_01.png') -98px -163px;
		}
		.madal_3{
			position:absolute;
			width:77px;
			height:163px;
			background: url('./img/medal_01.png') -174px 0;
		}
		.madal_3:hover {
    		background: url('./img/medal_01.png') -174px -163px;
		}
		.madal_4{
			position:absolute;
			width:77px;
			height:163px;
			background: url('./img/medal_01.png') -250px 0;
		}
		.madal_4:hover {
    		background: url('./img/medal_01.png') -250px -163px;
		}
		.madal_5{
			position:absolute;
			width:77px;
			height:163px;
			background: url('./img/medal_01.png') -328px 0;
		}
		.madal_5:hover {
    		background: url('./img/medal_01.png') -328px -163px;
		}
		
		
		
		
		#flag_l_0,#flag_r_0{
			position:absolute;
			width:45px;
			height:35px;
			background: url('img/flag_btn.png?v1') 0 0;
		}
		#flag_l_0:hover,#flag_r_0:hover{
    		background: url('img/flag_btn.png?v1') 0 -35px;
		}
		#flag_l_1,#flag_r_1{
			position:absolute;
			width:45px;
			height:35px;
			background: url('img/flag_btn.png?v1') -45px 0;
		}
		#flag_l_1:hover,#flag_r_1:hover{
    		background: url('img/flag_btn.png?v1') -45px -35px;
		}
		#flag_l_2,#flag_r_2{
			position:absolute;
			width:45px;
			height:35px;
			background: url('img/flag_btn.png?v1') -90px 0;
		}
		#flag_l_2:hover,#flag_r_2:hover{
    		background: url('img/flag_btn.png?v1') -90px -35px;
		}
		#flag_l_3,#flag_r_3{
			position:absolute;
			width:45px;
			height:35px;
			background: url('img/flag_btn.png?v1') -135px 0;
		}
		#flag_l_3:hover,#flag_r_3:hover{
    		background: url('img/flag_btn.png?v1') -135px -35px;
		}
		#flag_l_4,#flag_r_4{
			position:absolute;
			width:45px;
			height:35px;
			background: url('img/flag_btn.png?v2') -180px 0;
		}
		#flag_l_4:hover,#flag_r_4:hover{
    		background: url('img/flag_btn.png?v2') -180px -35px;
		}
		#flag_l_5,#flag_r_5{
			position:absolute;
			width:45px;
			height:35px;
			background: url('img/forum.png?v1') 45px 0;
		}
		#flag_l_5:hover,#flag_r_5:hover{
    		background: url('img/forum.png?v1') 45px -35px;
		}
		.btn5{
			position:absolute;
			width:85px;
			height:85px;
			background: url('../img/btn_list_2.png') -340px 0;
		}
		.btn5:hover {
    		background: url('../img/btn_list_2.png') -340px -170px;
		}
		.msg{
			width: 100%;
			height: auto;
			padding: 5px;
			position:relative; 

		}
		.msg:after{

			content: '';
			display: block;
			border-bottom: 2px dashed #aaa;
			margin: 10px;
		
		}
		.clearFix{
			clear: both;
		}

	</style>
    <script src="../js/select_thing.js" type="text/javascript"></script>
</Head>
<body>

	<!--==================================================
    html內容
    ====================================================== -->
	<!-- 背景 -->
<div style="position:absolute; top:0px; left:0px; width:1000px; height:500px; background-color:#000000; opacity:0.8;" onClick=""></div>
	  <!-- 改過 -->
    <img src="../img/book_page_back2.png" style="position:absolute; top:5px; left:0px; width:947px; height:480px;"  border="0">
    
    <!-- ifame -->
<div id="iframe"  style="position:absolute;top:0px;left:0px;"></div>
	
<div id="top_btn">
    <!-- 按鈕列 -->
    <a id="bb-nav-out" onClick="out()" class="btn_close" style="position:absolute; top:377px; left:878px; cursor:pointer;" ></a>
      <!-- 改過 -->
    <a id="l_btn" class=" btn_arrow_l" onClick="set_page(-1,0)" style="position:absolute; top:191px; left:0px; cursor:pointer;" border="0"></a>
    <a id="r_btn" class=" btn_arrow_r" onClick="set_page(1,0)" style="position:absolute; top:191px; left:879px; cursor:pointer;" border="0"></a>
	
    <!-- 快樂的老師評語 -->

    <div  id="rec_star_comment" class="comment_box_on" width="500" height="20" style="position:absolute; font-size:20px; resize: none; overflow: auto; display:none;" readOnly>
    	

    </div>
    <a id="rec_star_badge" class="madal_4" onClick="comment_box_click()"  style="display:none;position: absolute;	top:310px;left:33px; " ></a>
    
    <!-- 標籤 -->    
    	  <!-- 改過 -->
    <a class="btn_help" onClick="open_helper(7)" style="position:absolute; top:-6px; left:890px; cursor:pointer;"></a>
	 <!-- 改過 -->
    <a id="flag_l_0" onClick="set_page(0,1)" style="position:absolute; top:2px; left:48px; cursor:pointer;" border="0"></a>
    <!-- 改過 -->
    <a id="flag_l_1" onClick="set_page(1,1)" style="position:absolute; top:39px; left:48px; cursor:pointer;" border="0"></a>
    <!-- 改過 -->
    <a id="flag_l_2" onClick="set_page(2,1)" style="position:absolute; top:77px; left:48px; cursor:pointer;" border="0"></a>
    <!-- 改過 -->
    <a id="flag_l_3" onClick="set_page(3,1)" style="position:absolute; top:114px; left:48px; cursor:pointer;" border="0"></a>
    <!-- 改過 -->
    <a id="flag_r_0" onClick="set_page(0,1)" style="position:absolute; top:2px; left:832px; cursor:pointer;" class="flipx"></a>
    <!-- 改過 -->
    <a id="flag_r_1" onClick="set_page(1,1)" style="position:absolute; top:39px; left:832px; cursor:pointer;" class="flipx"></a>
    <!-- 改過 -->
    <a id="flag_r_2" onClick="set_page(2,1)" style="position:absolute; top:77px; left:832px; cursor:pointer;" class="flipx"></a>
    <!-- 改過 -->
    <a id="flag_r_3" onClick="set_page(3,1)" style="position:absolute; top:114px; left:832px; cursor:pointer;" class="flipx" ></a>
	<!-- 改過 -->
   	<? if($forum_flag){?>
    <a id="flag_l_4" onClick="set_page(4,1)" style="position:absolute; top:153px; left:48px; cursor:pointer;" border="0"></a>
    <a id="flag_r_4" onClick="set_page(4,1)" style="position:absolute; top:153px; left:832px; cursor:pointer;" class="flipx"></a>
    <? } ?>
</div>
	<div id="helper" style="position:absolute; top:-8px; left:-8px; width:1040px; height:480px; display:none; overflow:hidden;"></div>

<!--  --><script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var save_lock = 0;
	var now_page = 0;
	
	var page_list = new Array("star","draw","text","record"<? if($forum_flag)echo ',"forum"';?>);	
	if(1)
	{
		page_list = new Array("star","draw","text","god"<? if($forum_flag)echo ',"forum"';?>);	
	}
	
	var draw_open = true;
	if(window.parent.auth_rec_draw_open != "yes")
	{//特殊設定
		draw_open = false;//grays
		window.document.getElementById("flag_l_1").className = "grays";
		window.document.getElementById("flag_l_1").onclick = "";
		window.document.getElementById("flag_l_1").style.cursor = "no-drop";
		window.document.getElementById("flag_l_1").style.opacity = "0.3";
		window.document.getElementById("flag_r_1").className = "grays";
		window.document.getElementById("flag_r_1").onclick = "";
		window.document.getElementById("flag_r_1").style.cursor = "no-drop";
		window.document.getElementById("flag_r_1").style.opacity = "0.3";
	}

	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	//離開//轉回推薦表頁面
	function out()
	{
		if(save_lock==1)
		{
			
			cover("在離開之前<BR>是否要存檔",3,function(){
				save_lock=0;
				window.parent.set_page("page_rec_menu");
				},function(){
				save_lock=0;
				run_save_close(function(){window.parent.set_page("page_rec_menu");},1,1);
				});
			return false;
		}else
		{
			window.parent.set_page("page_rec_menu");	
		}
	}
	//cover
	function cover(text,type,proc,proc2)
	{
		echo(proc2);
		window.parent.cover(text,type);
		if(type == 2 && proc != null)
		{
			delayExecute(proc);
		}
		else if(type == 3 && proc2 != null)
		{
			delayExecute(proc,proc2);
		}	
	}
	/*cover 啟用器的用法
	 cover("這嘎");
	 cover("這嘎",1);
	 cover("這嘎",2,function(){echo("哈哈");});
	*/
	//cover 點選器
	function delayExecute(proc,proc2) {
		var x = 100;
		
		var hnd = window.setInterval(function () {
			if(window.parent.cover_click ==1 )
			{//點選確定的狀況
				window.parent.cover_click = -1;
				window.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選確定");
				proc();
				//cover("");
			}
			else if(window.parent.cover_click ==0 )
			{//點選取消的狀況
				window.parent.cover_click = -1;
				window.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選取消");
				cover("");
			}
			else if(window.parent.cover_click ==2 )
			{//點選取消的狀況
				window.parent.cover_click = -1;
				window.parent.cover_level = 0;
				window.clearInterval(hnd);
				proc2();
				echo("COVER點選取消");
			}
		}, x);
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}
	
	function set_top_btn(value)
	{
		window.document.getElementById("top_btn").style.display = value;
	}
	//存檔後關閉系列
	function run_save_close(fun,value,state)
	{
		if(now_page == 0)
		{
			document.getElementsByName('ifr')[0].contentWindow.go_save(fun,value,state);
		}
		if(now_page == 2)
		{
			document.getElementsByName('ifr')[0].contentWindow.go_save(fun,value,state);
		}
		if(now_page == 1)
		{
			document.getElementsByName('ifr')[0].contentWindow.go_save(fun,value,state);
		}
		//fun(value,state);	
	}
	//set_page
	function set_page(value,state)
	{
		cover("讀取中");	
		if(save_lock==1)
		{
			cover("在離開之前<BR>是否要存檔",3,function(){
				save_lock=0;
				set_page(value,state);
				},function(){
				save_lock=0;
				run_save_close(function(){set_page(value,state)},value,state);
				});
			return false;
		}else
		{
			if(state == 1)
			{
				now_page = value;
			}else if(state == 0)
			{
					now_page = now_page+value;
					if(!draw_open && now_page ==1)now_page = now_page+value;
			}
			echo("前往頁面為"+page_list[now_page]);
			
			for(var i = 0 ; i < page_list.length ;i++)
			{
				echo(i);
				if(i <= now_page)
				{
					window.document.getElementById("flag_l_"+i).style.display = "block";	
					window.document.getElementById("flag_r_"+i).style.display = "none";	
				}
				else
				{
					window.document.getElementById("flag_l_"+i).style.display = "none";	
					window.document.getElementById("flag_r_"+i).style.display = "block";	
				}
			}
			if(now_page == 0 )
			{window.document.getElementById("l_btn").style.display = "none";}
			else 
			{window.document.getElementById("l_btn").style.display = "block";}
			
			if(now_page == (page_list.length-1))
			{window.document.getElementById("r_btn").style.display = "none";}
			else 
			{window.document.getElementById("r_btn").style.display = "block";}
			set_content("",0,"");
			
			window.document.getElementById("iframe").innerHTML ='<iframe id="ifr" name="ifr" src="./rec_'+page_list[now_page]+'/index.php?uid='+window.parent.home_id+'&book_sid='+window.parent.click_book_sid+'&book_name='+window.parent.click_book_name+'" frameborder="0" width="1000" height="480" style="position:absolute; top:0px; left:0px; " ></iframe>';
		}
	}
	
	//設定 快樂的老師評語 
	function set_content(text,score,time,key)
	{
		if(score==0)
		{
			window.document.getElementById("rec_star_comment").style.display = "none";
			window.document.getElementById("rec_star_badge").style.display = "none";
			window.document.getElementById("rec_star_comment").className = "comment_box_of";
		}
		else
		{
			if(text != ""){
				window.document.getElementById("rec_star_comment").className = "comment_box_on";
			var intiText = window.document.getElementById("rec_star_comment").innerHTML;
			
			window.document.getElementById("rec_star_comment").innerHTML= intiText + 
			"<div class='msg' ><span id='commetText_'"+key+" style='position:relative;  width:70%; font-size:18px; font-weight:600;float:left;'>"+text+"</span>" + 
			"<span id='commetTime_'"+key+" style='position:relative; width:26%; font-size:14px; font-weight:600;float:right;'>"+time+"</span><div class='clearFix'></div></div>";
			}else{
				window.document.getElementById("rec_star_comment").className = "comment_box_of";
			}
			
			
			window.document.getElementById("rec_star_badge").className = "madal_"+score;
			window.document.getElementById("rec_star_comment").style.display = "block";
			window.document.getElementById("rec_star_badge").style.display = "block";

			

				
			console.log(text);
			console.log(time);
			

		}
	}
	
	//打開快樂的評語
	function comment_box_click()
	{
		if( "comment_box_of"==window.document.getElementById("rec_star_comment").className)
		window.document.getElementById("rec_star_comment").className = "comment_box_on";
		else
		window.document.getElementById("rec_star_comment").className = "comment_box_of";
	}
	
	//---------------------------------------------------
    //helper
    //---------------------------------------------------
	function open_helper(value)
	{
		if(now_page == 4)
		{
			cover("暫無說明",1);
		}else if(now_page == 3)
		{
			window.open("img/howtorecorde.png","help");
		}
		else
		{
			window.document.getElementById("helper").innerHTML="<iframe src='../page_helper/index.php?id="+(value-now_page)+"' style='position:absolute; top:0px; left:0px; width:1040px; height:590px;  overflow:hidden;' frameborder='0'></iframe>";
			window.document.getElementById("helper").style.display = "block";
		}
	}
	//---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	set_page(0,1);


	
	
	
        

    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    