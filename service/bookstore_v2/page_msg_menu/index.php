<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 訊息框
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
      /*數字特效用*/
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

            font-size:22px;
            text-align:right;
            font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif;
            }
			#btn{
				position:absolute;
				width:55px;
				height:55px;
				background: url('img/msg_omg.png') 0px 0;
			}
			#btn:hover {
				background: url('img/msg_omg.png') 0px -55px;
			}
	</style>
</Head>
<body>

	<!--==================================================
    html內容
    ====================================================== -->
        <!-- 改過 -->
    <a id="btn" onClick="chang_mode()" style="position:absolute; top:0px; left:5px; cursor:pointer; display:none;"></a>
    <iframe id="iframe" src="manu.php?count=1" frameborder="0" style="position:absolute; top:4px; left:53px; width: 488px; height: 110px;"></iframe>
	<div id="count" style="position:absolute; top:42px; left:13px;display:none;" class="number_bar"></div>
    

    <script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var count_flag = 1;
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	function out()
	{
		window.parent.set_page("");	
	}
	function chang_mode()
	{
		if(count_flag==1)count_flag = 0;
		else if(count_flag==0)count_flag = 1;
		window.document.getElementById("iframe").src = "manu.php?count="+count_flag;
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
		echo("get_msg_count:初始開始:讀取訊息筆數");
		
		var url = "./ajax/get_msg_count.php";
		$.post(url, {
					user_id:window.parent.home_id,
					user_permission:window.parent.user_permission					
			}).success(function (data) 
			{
				
				if(data[0]!="{")
				{
					echo("AJAX:success:get_msg_count():讀取訊息筆數:資料庫發生問題");
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",2,function(){main();})
					return false;
				}
				
				data_array = JSON.parse(data);
				echo("AJAX:success:get_msg_count():讀取訊息筆數:已讀出:"+data);
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
					set_count(data_array["msg_count"]);
				}
				
			}).error(function(e){
				echo("AJAX:error:get_msg_count():讀取訊息筆數:");
			}).complete(function(e){
				echo("AJAX:complete:main():讀取訊息筆數:");
			});
	}
	//確定閱讀
	function set_msg_of(log_id,mode)
	{
		echo("set_msg_of:初始開始:存入已閱讀+"+log_id);
		var x = log_id;
		var y = mode;
		if(log_id=0)return false;
		var url = "./ajax/set_msg_of.php";
		$.post(url, {
					user_id:window.parent.home_id,
					user_permission:window.parent.user_permission,
					log_id:x
			}).success(function (data) 
			{
				
				if(data[0]!="{")
				{
					echo("AJAX:success:set_msg_of():存入已閱讀:資料庫發生問題");
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",2,function(){set_msg_of();})
					return false;
				}
				
				data_array = JSON.parse(data);
				echo("AJAX:success:set_msg_of():存入已閱讀:已讀出:"+data);
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
					window.parent.set_coin(data_array["coin"]);
					set_count(-1);
					if(data_array["coin"]>0)cover("獲得金錢 + "+data_array["coin"],1);
					if(count_flag==1)window.document.getElementById("iframe").src = "manu.php?count=1";
				}
				
			}).error(function(e){
				echo("AJAX:error:set_msg_of():存入已閱讀:");
			}).complete(function(e){
				echo("AJAX:complete:set_msg_of():存入已閱讀:");
			});
	}
	function set_count(value)
	{
		if(value!= -1)
		{
			window.document.getElementById("count").innerHTML = value; 
		}
		else
		{
			window.document.getElementById("count").innerHTML--; 
		}
		if(window.document.getElementById("count").innerHTML == 0)
		{
			if(count_flag==0)window.document.getElementById("iframe").src = "manu.php?count=1";
			window.document.getElementById("btn").style.display = "none";
			window.document.getElementById("count").style.display = "none";
		}else
		{	window.document.getElementById("btn").style.display = "block";	
			window.document.getElementById("count").style.display = "block";	
		}
	}
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	main();
 

    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    