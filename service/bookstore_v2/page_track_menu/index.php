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
	<Title>好友功能</Title>
    <!-- 掛載 --> 
    <script type="text/javascript" src="../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
    <script src="../js/select_thing.js" type="text/javascript"></script>
     <link rel="stylesheet" href="../css/btn.css">
	<style>
      
	</style>
</Head>
<body  style="overflow:hidden;">

	<!--==================================================
    html內容
    ====================================================== -->
    <!-- 遮屏-->
	<div style="position:absolute; top:0px; left:0px; width:900px; height:67px;  opacity:0.8;" onClick=""></div>
	<div id="iframe" style="position:absolute;top:0px;left:0px;"></div>
	<a id="left_btn" class="btn_arrow_l" onClick="set_page(-1)" style="position:absolute;cursor:pointer; left:5px; top: 20px; display:none;" border="0"></a>
    <a id="right_btn"  class="btn_arrow_r" onClick="set_page(1)" style="position:absolute;cursor:pointer; left:900px; top: 20px; display:none;" border="0"></a>


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
	function main()
	{
		echo("main:初始開始:追蹤人口筆數");
		var url = "./ajax/get_track_count.php";
		$.post(url, {
					user_permission:window.parent.user_permission,
					auth_open_publish:window.parent.auth_open_publish
			}).success(function (data) 
			{
				echo("AJAX:success:get_sell_count():追蹤人口筆數:已讀出:"+data);
				if(data[0]!="{")
				{
					echo("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員");
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",2,function(){main();});
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
					sell_max_count = data_array["track_count"];
					if(sell_max_count == 0)
					{max_page = 1;}
					else
					{max_page = Math.floor((Math.floor(sell_max_count)-1)/10)+1;}
					
					set_page(0);
					//cover("");
				}
				
			}).error(function(e){
				echo("AJAX:error:get_sell_count():追蹤人口筆數:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){main();});
			}).complete(function(e){
				echo("AJAX:complete:main():追蹤人口筆數:");
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
		window.document.getElementById("iframe").innerHTML ='<iframe src="./manu.php?page='+page+'" frameborder="0" width="555" height="287" style="position:absolute; top:-15px; left:65px; width: 825px; height: 100px;" ></iframe>';
		cover("");
	}
	
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	main();
 

    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    