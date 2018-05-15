<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 進貨狀況表單頁
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
                    APP_ROOT.'inc/code'
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
        //GET


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
	<Title>閱讀登記</Title>
    <!-- 掛載 --> 
    <script type="text/javascript" src="../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
    <script src="../js/select_thing.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../css/btnEng.css">
	<style>
     
		#popopo{
			position:absolute;
			width:223px;
			height:284px;
			background: url('./img/pagge_2_eng.png') -9px -13px;
		}
		#popopo:hover {
    		background: url('./img/pagge_2_eng.png') -12px -298px;
		}
		.number_bar
        {
            text-shadow:2px 0px 0px rgba(128,23,15,1),
                        0px -2px 0px rgba(128,23,15,1),
                        -2px 0px 0px rgba(128,23,15,1),
                        0px 2px 0px rgba(128,23,15,1),
                        2px 2px 0px rgba(128,23,15,1),
                        2px -2px 0px rgba(128,23,15,1),
                        -2px 2px 0px rgba(128,23,15,1),
                        -2px -2px 0px rgba(128,23,15,1);
            font-weight:bold;
            color:#FCFFFF;
            letter-spacing:0pt;

            font-size:40px;
            text-align:right;

            font-family:Microsoft JhengHei,comic sans, comic sans ms, cursive, verdana, arial, sans-serif;
        }
			.number_bar2
        {
            text-shadow:2px 0px 0px rgba(128,23,15,1),
                        0px -2px 0px rgba(128,23,15,1),
                        -2px 0px 0px rgba(128,23,15,1),
                        0px 2px 0px rgba(128,23,15,1),
                        2px 2px 0px rgba(128,23,15,1),
                        2px -2px 0px rgba(128,23,15,1),
                        -2px 2px 0px rgba(128,23,15,1),
                        -2px -2px 0px rgba(128,23,15,1);
            font-weight:bold;
            color:#FCFFFF;
            letter-spacing:0pt;

            font-size:16px;
            text-align:right;
            font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif;
            }
		
	</style>
</Head>
<body>

	<!--==================================================
    html內容
    ====================================================== -->
    <!-- 遮屏-->
<div style="position:absolute; top:0px; left:0px; width:1000px; height:500px; background-color:#000000; opacity:0.8;" onClick=""></div>

	<div style="position:absolute; top:-1px; left:-80px;">
        
        <img src="../img/back_s.png" style="position:absolute; left:89px; top: 57px;" border="0">
        <img src="./img/reg_tittle_eng.png" style="position:absolute; left:280px; top: -14px;" border="0">
        <a id="out" class="btn_close" onClick="out()" style="position:absolute; left:698px; top: 383px; cursor:pointer;"><span style="position:absolute;left:15px;top:50px;font-size:22px;color: #fff; font-weight: 600;">Exit</span></a>
      <div id="iframe" style="position:absolute;top:0px;left:-100px;"></div>
        <a id="left_btn" class="btn_arrow_l" onClick="set_page(-1)" style="position:absolute; cursor:pointer; left:249px; top: 399px;display:none;"></a>
        <a id="right_btn" class="btn_arrow_r" onClick="set_page(1)" style="position:absolute; cursor:pointer;left:500px; top: 399px; display:none;" ></a>
      <div id="page_text" style="position:absolute; top:402px; left:322px; width:177px; height:47px; font-size: 36px; text-align: center; font-weight: bold; color: #406031;"></div>
    </div>
    <!--按鈕列-->
    <img src="./img/pagge_1.png" style="position:absolute; left:695px; top: 379px; width: 295px; height: 74px;" border="0">
	<a id="popopo" onClick="goooooooo()" style="position:absolute; left:708px; top: 82px; display:none;" ></a>
	<!-- 說明-->
    <a class=" btn_help" onClick="open_helper(9)" style="position:absolute; top:0px; left:896px; cursor:pointer;"><span class="number_bar" style="position:absolute;left:6px;top:45px;font-size:14px;"></span></a>
	<div id="helper" style="position:absolute; top:-8px; left:-8px; width:1040px; height:480px; display:none; overflow:hidden;"></div>

	<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var opinion_max_count = 0;
	var max_page = 1 ; 
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	function out()
	{
		window.parent.set_page("");	
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
	/*cover 啟用器的用法
	 cover("這嘎");
	 cover("這嘎",1);
	 cover("這嘎",2,function(){echo("哈哈");});
	*/
	//cover 點選器
	function delayExecute(proc) {
		var x = 100;
		var hnd = window.setInterval(function () {
			if(window.parent.cover_click ==1 )
			{//點選確定的狀況
				window.parent.cover_click = -1;
				window.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選確定");
				proc();
				cover("");
			}
			else if(window.parent.cover_click ==0 )
			{//點選取消的狀況
				window.parent.cover_click = -1;
				window.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選取消");
				cover("");
			}
		}, x);
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}
	//=========MAIN=============
	function get_opinion_count()
	{
		echo("get_opinion_count:初始開始:讀取進貨筆數");
		cover("Loading...")
		var url = "./ajax/get_opinion_count.php";
		$.post(url, {
					auth_read_opinion_limit_day:window.parent.auth_read_opinion_limit_day,
					user_id:window.parent.home_id,
					user_permission:window.parent.user_permission,
					auth_open_publish:window.parent.auth_open_publish
					
			}).success(function (data) 
			{
				echo("AJAX:success:get_opinion_count():讀取進貨筆數:已讀出:"+data);
				if(data[0]!="{")
				{
					echo("AJAX:success:get_opinion_count():讀取進貨筆數:資料庫發生問題");
					cover("資料庫好像有點問題呢，請再試試看<BR>",2,function(){get_opinion_count();});
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
					opinion_max_count = data_array["opinion_count"];
					if(opinion_max_count == 0)
					{max_page = 1;}
					else
					{max_page = Math.floor((Math.floor(opinion_max_count)-1)/10)+1;}
					
					set_page(0);
				}
				
			}).error(function(e){
				echo("AJAX:error:get_opinion_count():讀取進貨筆數:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){get_opinion_count();});
			}).complete(function(e){
				echo("AJAX:complete:get_opinion_count():讀取進貨筆數:");
			});
	}
	function set_page(value)
	{
		window.document.getElementById("popopo").style.display = "none";
		echo("set_page");
		window.parent.page_list["opinion"] = window.parent.page_list["opinion"]+value;
		if(window.parent.page_list["opinion"] == 1 ) 
		{window.document.getElementById("left_btn").style.display = "none";}
		else
		{window.document.getElementById("left_btn").style.display = "block";}
		
		if(window.parent.page_list["opinion"] == max_page) 
		{window.document.getElementById("right_btn").style.display = "none";}
		else
		{window.document.getElementById("right_btn").style.display = "block";}
		window.document.getElementById("page_text").innerHTML = window.parent.page_list["opinion"]+" / "+max_page+" 頁";
		window.document.getElementById("iframe").innerHTML ='<iframe src="./manuEng.php?page='+window.parent.page_list["opinion"]+'&uid='+window.parent.home_id+'&auth_read_opinion_limit_day='+window.parent.auth_read_opinion_limit_day+'" frameborder="0" width="500" height="287" style="position:absolute; top:113px; left:236px; " ></iframe>';
		cover("");
	}
	//GGGGGGGGGGGGGGGGGGGOOOOOOOOOOOOOO近進貨拉
	function goooooooo() {
		//吃土
		echo("我要這本書拉WW");
		window.parent.set_action_bookstore_log(window.parent.user_id,'e21',window.parent.action_on);
		window.parent.set_page("../read_the_registration_v2/page_opinion_registration2");
		
	}
	//---------------------------------------------------
    //helper
    //---------------------------------------------------
	function open_helper(value)
	{
		window.document.getElementById("helper").innerHTML="<iframe src='../page_helper/index.php?id="+value+"' style='position:absolute; top:0px; left:0px; width:1040px; height:590px;  overflow:hidden;' frameborder='0'></iframe>";
		window.document.getElementById("helper").style.display = "block";
	}
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	get_opinion_count();
 

    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    