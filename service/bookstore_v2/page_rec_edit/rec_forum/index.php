<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 推薦框 -> 文字推薦
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
        require_once(str_repeat("../",4).'config/config.php');
		require_once(str_repeat("../",4).'service/forum/inc/article_eagle/code.php');

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

   	//---------------------------------------------------
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
        //GET
        $uid          =(isset($_GET['uid']))?(int)$_GET['uid']:$user_id;
        $book_sid    =(isset($_GET['book_sid']))?mysql_prep($_GET['book_sid']):0;
        $book_name         =(isset($_GET['book_name']))?mysql_prep($_GET['book_name']):"？";

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
	
	//-----------------------------------------------
	//發文鷹架
	//-----------------------------------------------

		$article_eagle_content=article_eagle(1);
		$article_eagle_code   =article_eagle(2);
	//---------------------------------------------------
	//SQL
	//---------------------------------------------------
?>
<!DOCTYPE HTML>
<Html>
<Head>

    <!-- 掛載 -->
    <script type="text/javascript" src="../../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
    <script type="text/javascript" src="../../../../service/forum/inc/add_action_forum_log/code.js"></script>
    <link rel="stylesheet" href="../../css/btn.css">
    <style type="text/css">
	.text_1 {
		    display: block;
			width: 100%;
			height: 34px;
			padding: 3px 6px;
			font-size: 18px;
			line-height: 1.42857143;
			color: #555;
			background-color: #fff;
			background-image: none;
			border: 1px solid #ccc;
			border-radius: 4px;
		
	}
	.btn-block {
	display: block;
	width: 92px;
	padding-left: 0;
	padding-right: 0;
	}
	.btn-primary {
		color: #fff;
		background-color: #428bca;
		border-color: #357ebd;
	}
	.btn {
		display: inline-block;
		margin-bottom: 0;
		font-weight: 400;
		text-align: center;
		vertical-align: middle;
		cursor: pointer;
		background-image: none;
		border: 1px solid transparent;
		white-space: nowrap;
		padding: 6px 6px;
		font-size: 14px;
		line-height: 1.42857143;
		border-radius: 4px;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}


	
	</style>
</Head>
<body style="overflow:hidden;">

	<!--==================================================
    html內容
    ====================================================== -->
    <!--  左葉框 -->
    <!-- 改過 -->
	<div style="position:absolute; top:11px; left:147px; width: 377px;">
         <img style="position:absolute; top:-10px; left:-24px;" src="img/tittle_2.png" />
	</div>
	 <!-- 改過 -->
	<div style="position:absolute; top:5px; left:110px; width: 350px; height: 450px">
	 <!-- 改過 -->
         <img style="position:absolute; top:95px; left:-9px; width: 341px; height: 340px; z-index:-1" src="img/BAR3.png" />

       
        <BR><BR>
        <div style=" width: 275px;position: absolute;color:#660000;font-family:微軟正黑體;top: 35px;" class="little">
        	<span style="font-size: 28px; font-weight: 600;position:absolute; ">聊書發文<span>
    		<a href="javascript:void(0);" class="btn btn-primary btn-block" role="button" style="color:#ffffff; position:absolute; top:22px; left:223px; height: 19px; cursor:pointer;" onclick="open_eagle()">使用發文輔助</a>
    	</div>
    	<div style="position: absolute;top:110px;">
        	<input  placeholder="請輸入文章標題" class="text_1" id="tittle" type="text"  name="mssr_input_box_name_title" size="40" style="width:300PX;" maxlength="40"/>
       		 <BR>
   	 		 <textarea  placeholder="請輸入文章內容" id = "conten" class="text_1" style="width:300px; height:240px; resize:none;"></textarea>	
			<img style="position:absolute; top:40px; left:-21px;" src="img/a.png" />
       		<img style="position:absolute; top:-20px; left:-21px;" src="img/b.png" />
        </div>
    </div>
                
    </div>
    <!--  右葉框 -->
     <!-- 改過 -->
	<div style="position:absolute; top:5px; left:495px; width:350px;height: 480;" >
    	<img style="position:absolute; top:95px; left:-9px; width: 341px; height: 340px; z-index:-1" src="img/BAR3.png" />
        <BR>
        <BR>
      	<span style=" width: 275px; color:#660000; font-family:微軟正黑體;font-size: 28px;font-weight: 600;position: absolute;top:30px;" class="little">發文列表</span>
      	 <!-- 改過 -->
      	<div id="post_count" style="position:absolute; top:70px; left:133px; width: 200px; text-align:right;font-family:微軟正黑體;font-size: 18px;"class="little"></div>
        <div id="po" style="position:absolute; top:150px; left:15px; width: 200px; color: #7D4622;z-index:-1;font-size: 18px;">尚未發文，無顯示內容</div>
      	
        <!--  發文列表內文 -->
        <div style=" position: absolute;top: 105px;">
        	<select id="post_tittle_list" class="text_1" onchange="show_post_conten();" style="width:310px;">
          	</select>
            <BR>
            <textarea id="post_conten_list"  placeholder="選擇的標題觀看發文內容" class="text_1" style="width:300px; height:210px; resize:none; display:none;" readonly></textarea>	
            <div id="list_show2" style="position:absolute; top:393px; left:33px; width: 124px;display:none;">喜歡:0人</div>
            
            <div id="list_show4" style="position:absolute; top:393px; left:188px; width: 129px;display:none;">回文:0篇</div>
  		</div>
        <img id="aaa" style="position:absolute; top:135px; left:-21px;display:none;" src="img/a.png" />
        <img id="bbb" style="position:absolute; top:80px; left:-21px; " src="img/b.png" />
         
    
    </div>
	<!-- 加載葉匡 -->
    <div id="eagle_tool_box" style="position:absolute; top:50px; left:134px; display:none;">
    	<img src="img/info.png" width="739" height="407">
    	<h2 style="position:absolute; top:-4px; left:15px; color:#347DA9; font-family:微軟正黑體; width: 198px;"class="little">發文補助工具</h2>
      	<div style="position:absolute; top:69px; left:62px;">
          	<select id="eagle_1" class="text_1" onchange="select_eagle(1);" style="width:600px;"></select><BR>
            <select id="eagle_2" class="text_1" onchange="select_eagle(2);" style="width:600px;"></select><BR>
            <select id="eagle_3" class="text_1" onchange="select_eagle(3);" style="width:600px;"></select><BR>
            <select id="eagle_4" class="text_1" onchange="select_eagle(4);" style="width:600px;"></select><BR>
        </div>
        <a href="#" class="btn btn-primary btn-block" id="eagle_ok" role="button" style="color:#ffffff; position:absolute; top:347px; left: 470px; display:none;" onclick="save_eagle();">確定</a></h2>
        <a href="#" class="btn btn-primary btn-block" role="button" style="color:#ffffff; position:absolute; top:347px; left: 600px;" onclick="close_eagle();">取消</a></h2>
    </div>
    <a id="save_btn" class="btn_talk" onClick="go_talk()" style="position:absolute; top:196px; left:879px; cursor:pointer;"></a>
    <a id="save_btn" class="btn_save" src="../img/save.png" onClick="open_stttttyle()" style="position:absolute; top:286px; left:879px; cursor:pointer;"></a>
	<!-- 最終大確認 -->
    <div id="stttttyle_box" style="position:absolute; top:127px; left:348px;display:none;">
    	<div id="stttttyle_box" style="position:absolute; top:-125px; left:-347px; height:480px; width:1000px; background-color:#000; opacity:0.2;"></div>
   	  	<img src="img/info.png" style="position:absolute;">
      	<h2 style="position:absolute; top:-4px; left:15px; color:#347DA9; font-family:微軟正黑體; width: 198px;"class="little">發文類型</h2>
		<div style="position:absolute; top:52px; left:14px; height: 35px;">

            <select id="stttttyle" class="text_1" onchange="select_stttttyle(1);" style="width:250px;">
            <option disabled="disabled" selected="">請選擇發文類型</option>
            <option value="1">綜合討論</option>
            <option value="2">我想要問</option>
            <option value="3">我想分享</option>
            </select>
        </div>
        <a href="#" class="btn btn-primary btn-block" id="stttttyle_ok" role="button" style="color:#ffffff; position:absolute; top:177px; left: 35px;display:none;" onclick="save_stttttyle();">送出</a></h2>
        <a href="#" class="btn btn-primary btn-block" role="button" style="color:#ffffff; position:absolute; top:177px; left: 165px;" onclick="close_stttttyle();">取消</a></h2>

    </div>
</div>

<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
     var book_sid = '<? echo $book_sid;?>';
	 var book_name = '<? echo $book_name;?>';
	 var uid = '<? echo $uid;?>';

	 var post_list;
	 
	 var eagle_code_list = new Array();
	 //OBJ
     var article_eagle_content=<?php echo json_encode($article_eagle_content,true);?>;
     var article_eagle_code   =<?php echo json_encode($article_eagle_code,true);?>;

	var stttttyle = 0;
	 
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	function close_stttttyle()
	{
		window.document.getElementById("stttttyle_box").style.display = "none";
	}
	function open_stttttyle()
	{
		select_eagle(0);
		window.document.getElementById("stttttyle").innerHTML = '<option disabled="disabled" selected="">請選擇發文類型</option><option value="1">綜合討論</option><option value="2">我想要問</option><option value="3">我想分享</option>';
		window.document.getElementById("stttttyle_ok").style.display = "none";
		window.document.getElementById("stttttyle_box").style.display = "block";
	}
	function save_stttttyle()
	{
		go_save();
		close_stttttyle();
	}
	function select_stttttyle()
	{	
		var e = document.getElementById("stttttyle");
		stttttyle = e.options[e.selectedIndex].value;
		
		window.document.getElementById("stttttyle_ok").style.display = "block";
	}
	
	
	function close_eagle()
	{
		window.document.getElementById("eagle_tool_box").style.display = "none";
	}
	function open_eagle()
	{
		select_eagle(0);
		window.document.getElementById("eagle_tool_box").style.display = "block";
	}
	function save_eagle()
	{
		var e = document.getElementById("eagle_4");
		eagle_code_list.push(e.options[e.selectedIndex].value);
		window.document.getElementById("conten").value = window.document.getElementById("conten").value +  e.options[e.selectedIndex].innerHTML;
		close_eagle();
	}
	function select_eagle(value)
	{
		if(value == 4)
		{
			window.document.getElementById("eagle_"+value).style.color = "#000";
			window.document.getElementById("eagle_ok").style.display = "block";
			return false;
		}	
		 
		//獲取目前陣列
		var tmp_eagle = article_eagle_content;
		for(var i = 1; i <= value ; i++)
		{
			var e = document.getElementById("eagle_"+i);
			tmp_eagle = tmp_eagle[e.options[e.selectedIndex].value];	
		}
		
		var tmp_eagle_code = article_eagle_code;
		for(var i = 1; i <= value ; i++)
		{
			var e = document.getElementById("eagle_"+i);
			tmp_eagle_code = tmp_eagle_code[e.options[e.selectedIndex].value];	
		}
		
		if(value==0)window.document.getElementById("eagle_"+(Math.abs(value)+1)).innerHTML = '<option disabled="disabled" style="color:#F11;" selected="">請選擇書籍類型</option>';
		if(value==1)window.document.getElementById("eagle_"+(Math.abs(value)+1)).innerHTML = '<option disabled="disabled" style="color:#F11;" selected="">請選擇你想要做甚麼?</option>';
		if(value==2)window.document.getElementById("eagle_"+(Math.abs(value)+1)).innerHTML = '<option disabled="disabled" style="color:#F11;" selected="">你可以參考下列問題</option>';
		if(value==3)
		{
			window.document.getElementById("eagle_"+(Math.abs(value)+1)).innerHTML = '<option disabled="disabled"  style="color:#F11;" selected="">你也許可以這樣?</option>';
			for(key1 in tmp_eagle)
			{
				 
				 window.document.getElementById("eagle_"+(value+1)).innerHTML = window.document.getElementById("eagle_"+(value+1)).innerHTML+'<option  style="color:#000;" value="'+tmp_eagle_code[key1]+'">'+tmp_eagle[key1]+'</option>';
			}
		}else
		{
			for(key1 in tmp_eagle)
			{
				 
				 window.document.getElementById("eagle_"+(value+1)).innerHTML = window.document.getElementById("eagle_"+(value+1)).innerHTML+'<option  style="color:#000;" value="'+key1+'">'+key1+'</option>';
			}
		}
		for(var i = 1; i <= 4 ; i++)
		{
			if(i-1 <= value)
			{
				window.document.getElementById("eagle_"+i).style.color = "#000";
				window.document.getElementById("eagle_"+i).style.display = "block";
			}
			else
			{
				
				window.document.getElementById("eagle_"+i).style.display = "none";
			}
			
		}
		window.document.getElementById("eagle_"+(value+1)).style.color = "#F11";
		window.document.getElementById("eagle_ok").style.display = "none";
	}

	
	//cover
	function cover(text,type,proc)
	{
		
		window.parent.cover(text,type);
		if(type == 2)
		{
			delayExecute(proc);
		}
	}
	function show_post_conten()
	{
		
		var e = document.getElementById("post_tittle_list");
		var strUser = e.options[e.selectedIndex].value;
		window.document.getElementById("post_conten_list").innerHTML=post_list[e.selectedIndex-1]["article_content"];
		window.document.getElementById("list_show2").innerHTML="喜歡:"+post_list[e.selectedIndex-1]["article_like_cno"]+"人";
		window.document.getElementById("list_show4").innerHTML="回文:"+post_list[e.selectedIndex-1]["article_report_cno"]+"篇";

		window.document.getElementById("post_conten_list").style.display = "block";
		
		window.document.getElementById("list_show2").style.display = "block";
		
		window.document.getElementById("list_show4").style.display = "block";
		window.document.getElementById("aaa").style.display = "block";
	}
	/*cover 啟用器的用法
	 cover("這嘎");
	 cover("這嘎",1);
	 cover("這嘎",2,function(){echo("哈哈");});
	*/
	//cover 點選器
	function delayExecute(proc) {
		var x = 100;
		var hnd = window.setInterval(function () {
			if(window.parent.parent.cover_click ==1 )
			{//點選確定的狀況
				window.parent.parent.cover_click = -1;
				window.parent.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選確定");
				proc();
				cover("");
			}
			else if(window.parent.parent.cover_click ==0 )
			{//點選取消的狀況
				window.parent.parent.cover_click = -1;
				window.parent.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選取消");
				cover("");
			}
		}, x);
	}
	
	function out()
	{
		if(article_id == 0)
			window.parent.parent.parent.location.href="../../../forum/mssr_forum_book_discussion.php?book_sid="+book_sid;
		else 
			window.parent.parent.parent.location.href="../../../forum/mssr_forum_book_reply.php?article_id="+article_id;
	}
	
	//=========MAIN=============
	function main()
	{
		window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e14',window.parent.parent.action_on);
		get_post_list();
		
			
	}
	//
	function get_post_list()
	{
		cover("讀取聊書資料")
		var url = "./ajax/get_post_list.php";
		
		$.post(url, {
					user_id:uid,
					book_sid:book_sid,
					
					user_permission:'<? echo $permission;?>'				
			}).success(function (data) 
			{
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員",2,function(){main();});
					return false;
				}
				
				data_array = JSON.parse(data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					cover(data_array["echo"],1);
					
				}else
				{
					post_list = data_array["data"];
					cover("");
					show_post_list();
					
				}
				
			}).error(function(e){
				
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){get_post_list();});
			}).complete(function(e){
			});	
	}
	function show_post_list()
	{	
		window.document.getElementById("post_tittle_list").style.display = "block";
		window.document.getElementById("post_conten_list").style.display = "none";
		window.document.getElementById("list_show2").style.display = "none";

		window.document.getElementById("list_show4").style.display = "none";
		window.document.getElementById("aaa").style.display = "none";
		window.document.getElementById("post_tittle_list").innerHTML = '<option disabled="disabled" selected="">請選擇文章標題觀看發文內容</option>';
		for(var i = 0 ; i < post_list.length ; i++)
		{
			
			window.document.getElementById("post_tittle_list").innerHTML =window.document.getElementById("post_tittle_list").innerHTML+'<option value="'+i+'">'+post_list[i]["article_title"]+'</option>'
			
		}
		window.document.getElementById("post_count").innerHTML = "發文篇數 : "+post_list.length+" 篇";
		
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}
	
	function go_talk()
	{
		
		cover("確定要前往聊書嗎?",2,function(){
		window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e42',window.parent.parent.action_on);
		// window.open('../../../forum/view/article.php?get_from=1&book_sid='+book_sid);});	
		window.parent.parent.parent.location.href = '../../../forum/view/article.php?get_from=1&book_sid='+book_sid;});		
	}
	function go_save()
	{
		if(stttttyle==0)
		{
			cover("?!",1);	
			return false;
		}
		window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e20',window.parent.parent.action_on);
		
		cover("儲存聊書發文")
		var url = "./ajax/set_rec_forum.php";
		eagle_code_listJSON=JSON.stringify(eagle_code_list);
		$.post(url, {
					user_id:uid,
					book_sid:book_sid,
					conten:window.document.getElementById("conten").value ,
					tittle:window.document.getElementById("tittle").value ,
					eagle_code_list:eagle_code_listJSON,
					stttttyle:stttttyle,
					user_permission:'<? echo $permission;?>'				
			}).success(function (data) 
			{
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員",2,function(){go_save();});
					return false;
				}
				
				data_array = JSON.parse(data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					cover(data_array["echo"],1);
					
				}else
				{
					
					//清空
					window.document.getElementById("conten").value = "";
					window.document.getElementById("tittle").value = "";
					eagle_code_list = new Array();
					cover("發文成功",1);
					get_post_list();
					
				}
				
			}).error(function(e){
				
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){go_save();});
			}).complete(function(e){
			});	
	}
	
		
	//=========MAIN=============
	
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
$.ajaxSetup({
		timeout: 15*1000
	});
       main();

    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    