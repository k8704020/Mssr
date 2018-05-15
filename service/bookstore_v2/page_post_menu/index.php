<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 報表頁面
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
	<Title>上架</Title>
    <!-- 掛載 --> 
    <script type="text/javascript" src="../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
   <script src="../js/select_thing.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../css/btn.css">
    <style>
		
		 /*中文特效用*/
		 .world_bar2
            {
            text-shadow:1px 0px 1px rgba(128,23,15,1),
                        0px -1px 1px rgba(128,23,15,1),
                        -1px 0px 1px rgba(128,23,15,1),
                        0px 1px 1px rgba(128,23,15,1),
                        1px 1px 1px rgba(128,23,15,1),
                        1px -1px 1px rgba(128,23,15,1),
                        -1px 1px 1px rgba(128,23,15,1),
                        -2px -1px 1px rgba(128,23,15,1)
						,1px 0px 1px rgba(128,23,15,1),
                        0px -1px 1px rgba(128,23,15,1),
                        -1px 0px 1px rgba(128,23,15,1),
                        0px 1px 1px rgba(128,23,15,1),
                        1px 1px 1px rgba(128,23,15,1),
                        1px -1px 1px rgba(128,23,15,1),
                        -1px 1px 1px rgba(128,23,15,1),
                        -1px -1px 1px rgba(128,23,15,1);


            font-weight:bold;
            color:#FCFFFF;
            letter-spacing:0pt;

            font-size:22px;
            text-align:left;
            font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif;
            }
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

            font-size:40px;
            text-align:right;
            font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif;
            }

       .flipx {
			-moz-transform:scaleX(-1);
			-webkit-transform:scaleX(-1);
			-o-transform:scaleX(-1);
			transform:scaleX(-1);
			/*IE*/
			filter:FlipH;
		}
		.box1{
			background: rgb(232,239,174); /* Old browsers */
			background: -moz-linear-gradient(top,  rgba(232,239,174,1) 1%, rgba(176,229,43,1) 49%, rgba(130,193,48,1) 50%, rgba(130,193,48,1) 50%, rgba(130,193,48,1) 52%, rgba(182,209,77,1) 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,rgba(232,239,174,1)), color-stop(49%,rgba(176,229,43,1)), color-stop(50%,rgba(130,193,48,1)), color-stop(50%,rgba(130,193,48,1)), color-stop(52%,rgba(130,193,48,1)), color-stop(100%,rgba(182,209,77,1))); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  rgba(232,239,174,1) 1%,rgba(176,229,43,1) 49%,rgba(130,193,48,1) 50%,rgba(130,193,48,1) 50%,rgba(130,193,48,1) 52%,rgba(182,209,77,1) 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  rgba(232,239,174,1) 1%,rgba(176,229,43,1) 49%,rgba(130,193,48,1) 50%,rgba(130,193,48,1) 50%,rgba(130,193,48,1) 52%,rgba(182,209,77,1) 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  rgba(232,239,174,1) 1%,rgba(176,229,43,1) 49%,rgba(130,193,48,1) 50%,rgba(130,193,48,1) 50%,rgba(130,193,48,1) 52%,rgba(182,209,77,1) 100%); /* IE10+ */
			background: linear-gradient(to bottom,  rgba(232,239,174,1) 1%,rgba(176,229,43,1) 49%,rgba(130,193,48,1) 50%,rgba(130,193,48,1) 50%,rgba(130,193,48,1) 52%,rgba(182,209,77,1) 100%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e8efae', endColorstr='#b6d14d',GradientType=0 ); /* IE6-9 */
			box-shadow: 1px 1px 1px #222;
		}
		
	</style>
</Head>
<body>

	<!--==================================================
    html內容
    ====================================================== -->
    <!-- 遮屏-->
	<div id="black" style="position:absolute; top:0px; left:0px; width:1000px; height:500px; background-color:#000000; opacity:0.8;" onClick=""></div>
    <!-- 列表版面-->

<div style="position:absolute; left: 68px; top:22px;">
      <img src="./img/back.png" style="position:absolute; left: 68px; top:41px;">
	
        
  <div style="position:absolute; left: 225px; top:9px;">
    <div style="position:absolute; left: 40px; width:104px; height:32px; cursor:pointer;  top: -33px;" onMouseOver="over(1,5)" onMouseOut="over(0,5)" onClick="window.parent.set_action_bookstore_log(window.parent.user_id,'e27',window.parent.action_on);go_page(5)">
            	<img id="tittle_back_5" src="./img/bar_off.png" style="position:absolute; left: -22px; top:0px;">
                <img src="./img/bar_text_5.png" style="position:absolute; left: -8px; top:3px;">
    </div>
    <div style="position:absolute; left: -22px; width:100px; height:40px; cursor:pointer;" onMouseOver="over(1,1)" onMouseOut="over(0,1)" onClick="window.parent.set_action_bookstore_log(window.parent.user_id,'e23',window.parent.action_on);go_page(1)">
            	<img id="tittle_back_1" src="./img/bar_on.png" style="position:absolute; left: -22px; top:0px;">
                <img src="./img/bar_text_1.png" style="position:absolute; left: -6px; top:3px;">
    </div>
    <div style="position:absolute; left: 118px; width:100px; height:40px; cursor:pointer;" onMouseOver="over(1,2)" onMouseOut="over(0,2)" onClick="window.parent.set_action_bookstore_log(window.parent.user_id,'e24',window.parent.action_on);go_page(2)">
            	<img id="tittle_back_2" src="./img/bar_off.png" style="position:absolute; left: -22px; top:0px;">
                <img src="./img/bar_text_2.png" style="position:absolute; left: -7px; top:3px;">
    </div>
    <div style="position:absolute; left: 256px; width:100px; height:40px; cursor:pointer; " onMouseOver="over(1,3)" onMouseOut="over(0,3)" onClick="window.parent.set_action_bookstore_log(window.parent.user_id,'e25',window.parent.action_on);go_page(3)">
            	<img id="tittle_back_3" src="./img/bar_off.png" style="position:absolute; left: -22px; top:0px;">
                <img src="./img/bar_text_3.png" style="position:absolute; left: -8px; top:3px;">
    </div>
    <div style="position:absolute; left: 393px; width:100px; height:40px; cursor:pointer; " onMouseOver="over(1,4)" onMouseOut="over(0,4)" onClick="window.parent.set_action_bookstore_log(window.parent.user_id,'e26',window.parent.action_on);go_page(4)">
            	<img id="tittle_back_4" src="./img/bar_off.png" style="position:absolute; left: -22px; top:0px;">
                <img src="./img/bar_text_4.png" style="position:absolute; left: -8px; top:3px;">
    </div>
    
	</div>
        <a id="out"  class="btn_close"  style="position:absolute; left: 782px; top:349px;cursor:pointer;" onClick="out()"></a>
        <div id="iframe" style="position : absolute; top: 73px; left:148px;"></div>
	</div>
	 <!-- 改過 -->
    <a id="left_btn"  class="btn_arrow_l"  onClick="set_page(-1)" style="position:absolute; cursor:pointer; left:349px; top: 400px; display:;"></a>
     <!-- 改過 -->
    <a id="right_btn"  class="btn_arrow_r"  onClick="set_page(1)" style="position:absolute; cursor:pointer; left:600px; top: 400px;display:;"></a>
     
    <div id="page_text" style="position:absolute; top:402px; left:422px; width:177px; height:47px; font-size: 36px; text-align: center; font-weight: bold; color: #406031;">100/160頁</div>
	<!-- 開頭-->
<img src="./img/tittle.png" style="position:absolute; left: 11px; top:-7px;">
<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var on_page = 1;
	var on_chick = -1;
	
	var max_count = 0;
	var page = 1 ;
	var max_page = 1 ; 
	
	var page_name = new Array("","sell","booking","comment","msg","shop");
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
	//cover
	function cover(text,type,proc,proc2)
	{
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
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}

	function go_page(value)
	{
	
	
	//轉換分頁
		if(on_page != value)
		{
				on_page = value;
				var i = 1;
				for(; i <= 5 ; i++)
				{
					window.document.getElementById("tittle_back_"+i).src="./img/bar_off.png";
				}
				window.document.getElementById("tittle_back_"+value).src="./img/bar_on.png";
				page = 1 ;
				main();	
		}
		
	}
	
	//閃耀模式
	function over( flag , page)
	{
		if(on_page != page)
		{
			if(flag==1)
			{
				window.document.getElementById("tittle_back_"+page).src="./img/bar_off_line.png";
				
			}else
			{
				window.document.getElementById("tittle_back_"+page).src="./img/bar_off.png";
			}		
		}
	}
	
	//=========MAIN=============
	function main()
	{
		echo("main:初始開始:讀取販售筆數");
		cover("讀取販賣頁面")
		var url = "./ajax/get_"+page_name[on_page]+"_count.php";
		$.post(url, {
					user_id:window.parent.home_id,
					user_permission:window.parent.user_permission,
					auth_open_publish:window.parent.auth_open_publish
					
			}).success(function (data) 
			{	
				
				if(data[0]!="{")
				{
					echo("AJAX:success:main():讀取筆數:資料庫發生問題");
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員",2,function(){main();});
					return false;
				}
				
				data_array = JSON.parse(data);
				echo("AJAX:success:main():讀取筆數:已讀出:"+data);
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
					sell_max_count = data_array["count"];
					if(sell_max_count == 0)
					{
						max_page = 1;
						
					}
					else
					{
						max_page = Math.floor((Math.floor(sell_max_count)-1)/10)+1;
					}
					
					set_page(0);
					//cover("");
				}
			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){
				echo("AJAX:complete:main():讀取筆數:");
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
		{
			
			window.document.getElementById("right_btn").style.display = "block";
		
		}

		window.document.getElementById("page_text").innerHTML = page+" / "+max_page+" 頁";
		//改過
		window.document.getElementById("iframe").innerHTML ='<iframe src="./p_'+page_name[on_page]+'_manu.php?page='+page+'&uid='+window.parent.home_id+'" frameborder="0" width="555" height="310" style="position:absolute; top:0px; left:0px; " ></iframe>';
		cover("");
	}
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	//get_shelf_count();
	main();


    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    