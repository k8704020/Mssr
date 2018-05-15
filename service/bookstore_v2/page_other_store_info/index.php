<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,閱讀登記 -> 書店狀態欄
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

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------
   		
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
		$user_id        = (isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
		$home_id 		= (isset($_GET['home_id']))?$_GET['home_id']:'0';
  

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
		if($permission =='0' || $user_id=='0' || $home_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");
	//---------------------------------------------------
	//SQL
	//---------------------------------------------------
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <!-- 掛載 -->
     <script type="text/javascript" src="../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script> 
     <script src="../js/select_thing.js" type="text/javascript"></script>
    <style>
       .btn_1{
		position:absolute;
		width:98px;
		height:28px;
		background: url('./img/welcome_bar_btn.png') 0 0;
	}
		.btn_2 {
    	position:absolute;
		width:98px;
		height:28px;
		background: url('./img/welcome_bar_btn.png') 0 -28px;
	}
	</style>
</Head>
<body>

	<!--==================================================
    html內容
    ====================================================== -->
	<img src="img/welcome_bar.png" style="position:absolute; top:0px;left:0px;">
    <div id="name" style="color:#FFF; position:absolute; top:43px; left:71px; text-align:right; width: 150px; font-size:22px;"></div>
    <div id="btn_1" onClick="set_track()" style="position:absolute; top:71px; left:10px; cursor:pointer;" onMouseOver="window.document.getElementById('btn_back_1').className='btn_2';" onMouseOut="window.document.getElementById('btn_back_1').className='btn_1';">
   		<a id="btn_back_1" class="btn_1"></a>
        <div id="btn_1_text" style="color:#FFF; position:absolute; top:5px; left:16px; width:100px;">加入好友</div>
    </div>
   <!-- <div id="btn_3" onClick="click_black_user()" style="position:absolute; top:71px; left:113px; cursor:pointer;" onMouseOver="window.document.getElementById('btn_back_2').className='btn_2';" onMouseOut="window.document.getElementById('btn_back_2').className='btn_1';">
   		<a id="btn_back_2" class="btn_1"></a>
        <div id="btn_3_text" style="color:#FFF; position:absolute; top:5px; left:16px; width:100px;">加入黑單</div>
    </div>-->
	<div id="btn_2" onClick="back_home()" style="position:absolute; top:71px; left:216px; cursor:pointer;" onMouseOver="window.document.getElementById('btn_back_3').className='btn_2';" onMouseOut="window.document.getElementById('btn_back_3').className='btn_1';">
   		<a id="btn_back_3" class="btn_1"></a>
      <div style="color:#FFF; position:absolute; top:5px; left:16px; width:100px;">回到自家</div>
</div>


    <script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	user_id = <? echo $user_id;?>;
	permission = '<? echo $permission;?>';
	home_id = <? echo $home_id;?>;
	btn_type = '';
	btn_type3 = '';
	
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
	function set_track()
	{
		echo("set_track:初始開始:設定好友+"+btn_type);
		if(btn_type=="") return false;
		var tmp = btn_type;
		btn_type = "";
		var url = "./ajax/set_track.php";
		$.post(url, {
					home_id:window.parent.home_id,
					type:tmp
			}).success(function (data) 
			{
				echo("AJAX:success:set_track():設定好友:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>",1);
					echo("AJAX:success:set_track():設定好友:資料庫發生問題");
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
					cover("完成"+window.document.getElementById("btn_1_text").innerHTML,1);
					set_track_text(data_array["type"]);
					window.parent.document.getElementsByName('page_track_menu')[0].src = window.parent.document.getElementsByName('page_track_menu')[0].src;
					
				}
				
			}).error(function(e){
				echo("AJAX:error:set_track():設定好友:");
				
			}).complete(function(e){
				echo("AJAX:complete:set_track():設定好友:");
			});
	}
	function set_black_user()
	{
		echo("set_black_user:初始開始:設定黑單+"+btn_type3);
		if(btn_type3=="") return false;
		var tmp = btn_type3;
		btn_type3 = "";
		var url = "./ajax/set_black.php";
		$.post(url, {
					home_id:window.parent.home_id,
					type:tmp
			}).success(function (data) 
			{
				echo("AJAX:success:set_black_user():設定黑單:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>",1);
					echo("AJAX:success:set_black_user():設定黑單:資料庫發生問題");
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
					cover("完成"+window.document.getElementById("btn_3_text").innerHTML,1);
					//set_black_user_text(data_array["type"]);
					
				}
				
			}).error(function(e){
				echo("AJAX:error:set_black_user():設定黑單:");
				
			}).complete(function(e){
				echo("AJAX:complete:set_black_user():設定黑單:");
			});
	}
	//調整加入黑單
	function set_black_user_text(value)
	{/*
		if(value ==0)
		{
			window.document.getElementById("btn_3_text").innerHTML = "加入黑單";
			btn_type3 = "add";
		}
		else 
		{
			window.document.getElementById("btn_3_text").innerHTML = "取消黑單";
			btn_type3 = "del";
		}
		*/
	}
	function click_black_user()
	{
		//cover("是否要"+window.document.getElementById("btn_3_text").innerHTML+"<BR>加入黑名單將無法收到此同學的<img src='/mssr/service/bookstore_v2/page_other_store_info/img/msg_omg.png'>訊息喔",2,function(){set_black_user();});
	}
	//
	function back_home()
	{
		window.parent.location.href="../bookstore_courtyard/index.php";
		;	
	}
	//調整家好友顯示
	function set_track_text(value)
	{
		if(value ==0)
		{
			window.document.getElementById("btn_1_text").innerHTML = "加入追蹤";
			btn_type = "add";
		}
		else 
		{
			window.document.getElementById("btn_1_text").innerHTML = "刪除追蹤";
			btn_type = "del";
		}
		
	}
	//=========MAIN=============
	function main()
	{
		echo("Main:初始開始:讀取店家資料");
		
		var url = "./ajax/get_track_have.php";
		$.post(url, {
					user_id:window.parent.user_id,
					user_permission:window.parent.user_permission,
					home_id:window.parent.home_id
			}).success(function (data) 
			{
				echo("AJAX:success:main():讀取店家資料:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",1);
					echo("AJAX:success:main():讀取店家資料:資料庫發生問題");
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
					set_track_text(data_array["have_track"]);
					//set_black_user_text(data_array["have_black"]);
				}
				
			}).error(function(e){
				echo("AJAX:error:main():讀取店家資料:");
				
			}).complete(function(e){
				echo("AJAX:complete:main():讀取店家資料:");
			});
	}
	
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	main();
	window.document.getElementById("name").innerHTML = window.parent.name;
    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    