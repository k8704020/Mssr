<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 販售狀況表單頁
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
    <style>
       .flipx {
			-moz-transform:scaleX(-1);
			-webkit-transform:scaleX(-1);
			-o-transform:scaleX(-1);
			transform:scaleX(-1);
			/*IE*/
			filter:FlipH;
		}
		#left_btn,#right_btn{
			position:absolute;
			width:70px;
			height:70px;
			background: url('../img/right.png') 0 0;
		}
		#left_btn:hover,#right_btn:hover {
    		background: url('../img/right.png') 0px -70px;
		}
		#out{
			position:absolute;
			width:100px;
			height:100px;
			background: url('../img/gr_btn_list.png') -300px 0;
		}
		#out:hover {
    		background: url('../img/gr_btn_list.png') -300px -100px;
		}
	</style>
</Head>
<body>

	<!--==================================================
    html內容
    ====================================================== -->
    <!-- 遮屏-->
	<div style="position:absolute; top:0px; left:0px; width:1000px; height:480px; background-color:#000000; opacity:0.8;" onClick=""></div>
	<img src="./img/page_back2.png" style="position:absolute; top::30px; left:80px; top: 26px;" border="0">
    <img src="./img/sell_tittle2.png" style="position:absolute; top::30px; left:380px; top: -14px;" border="0">
    <a id="out" onClick="out()" style="position:absolute; left:771px; top: 377px; cursor:pointer;"></a>
    <div id="sp_help" style="position:absolute; top:236px; left:220px; color:#993300; font-size:36px; text-align:center; width: 568px; display:none;">還沒賣出書本嗎?<BR>可以請別人多到你的書店來參觀喔!</div>
	<div id="iframe" style="position:absolute;top:0px;left:0px;"></div>
	<a id="left_btn" onClick="set_page(-1)" style="position:absolute; cursor:pointer; left:349px; top: 388px; display:none;" class="flipx"></a>
    <a id="right_btn" onClick="set_page(1)" style="position:absolute; cursor:pointer; left:600px; top: 389px; display:none;"></a>
	<div id="page_text" style="position:absolute; top:402px; left:422px; width:177px; height:47px; font-size: 36px; text-align: center; font-weight: bold; color: #406031;">100/160頁</div>
    <script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var sell_max_count = 0;
	var page = 1 ;
	var max_page = 1 ; 
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	function out()
	{
		window.parent.set_page("");	
	}
	
	/*cover 啟用器的用法
	 cover("這嘎");
	 cover("這嘎",1);
	 cover("這嘎",2,function(){echo("哈哈");});
	 cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){get_booking_count();});
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
	//cover
	function cover(text,type,proc)
	{
		
		window.parent.cover(text,type);
		if(type == 2 && proc)
		{
			delayExecute(proc);
		}
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}
	//=========MAIN=============
	function get_sell_count()
	{
		echo("get_sell_count:初始開始:讀取販售筆數");
		cover("讀取販賣頁面")
		var url = "./ajax/get_sell_count.php";
		$.post(url, {
					user_id:window.parent.home_id,
					user_permission:window.parent.user_permission,
					auth_open_publish:window.parent.auth_open_publish
					
			}).success(function (data) 
			{
				
				if(data[0]!="{")
				{
					echo("AJAX:success:get_sell_count():讀取販售筆數:資料庫發生問題");
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員",2,function(){get_sell_count();});
					return false;
				}
				
				data_array = JSON.parse(data);
				echo("AJAX:success:get_sell_count():讀取販售筆數:已讀出:"+data);
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
					sell_max_count = data_array["sell_count"];
					if(sell_max_count == 0)
					{
						max_page = 1;
						window.document.getElementById("sp_help").style.display = "block";
					}
					else
					{max_page = Math.floor((Math.floor(sell_max_count)-1)/10)+1;}
					
					set_page(0);
					//cover("");
				}
			}).error(function(e){
				echo("AJAX:error:get_sell_count():讀取販售筆數:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){get_sell_count();});
			}).complete(function(e){
				echo("AJAX:complete:main():讀取販售筆數:");
			});
	}
	function set_page(value)
	{
		echo("set_page");
		page = page+value;
		if(page == 1) 
		{window.document.getElementById("left_btn").style.display = "none";}
		else
		{window.document.getElementById("left_btn").style.display = "block";}
		
		if(page == max_page) 
		{window.document.getElementById("right_btn").style.display = "none";}
		else
		{window.document.getElementById("right_btn").style.display = "block";}
		window.document.getElementById("page_text").innerHTML = page+" / "+max_page+" 頁";
		window.document.getElementById("iframe").innerHTML ='<iframe src="./manu.php?page='+page+'&uid='+window.parent.home_id+'" frameborder="0" width="555" height="287" style="position:absolute; top:113px; left:226px; " ></iframe>';
		cover("");
	}
	
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	get_sell_count();
 

    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    