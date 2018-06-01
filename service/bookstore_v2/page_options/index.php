<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 設定頁面
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
    <div style="position:absolute; left: -22px; width:100px; height:40px; cursor:pointer;" onMouseOver="over(1,1)" onMouseOut="over(0,1)" onClick="window.parent.set_action_bookstore_log(window.parent.user_id,'e28',window.parent.action_on);go_page(1)">
            	<img id="tittle_back_1" src="./img/bar_on.png" style="position:absolute; left: -22px; top:0px;">
                <img src="./img/bar_text_1.png" style="position:absolute; left: 11px; top:3px;">
    </div>
    <div style="position:absolute; left: 118px; width:100px; height:40px; cursor:pointer;" onMouseOver="over(1,2)" onMouseOut="over(0,2)" onClick="window.parent.set_action_bookstore_log(window.parent.user_id,'e29',window.parent.action_on);go_page(2)">
            	<img id="tittle_back_2" src="./img/bar_off.png" style="position:absolute; left: -22px; top:0px;">
                <img src="./img/bar_text_2.png" style="position:absolute; left: -7px; top:3px;">
    </div>
    <div style="position:absolute; left: 256px; width:100px; height:40px; cursor:pointer; opacity:0.3;" onClick="go_page(3)">
            	<img id="tittle_back_3" src="./img/bar_off.png" style="position:absolute; left: -22px; top:0px;">
                <img src="./img/bar_text_3.png" style="position:absolute; left: 6px; top:3px;">
    </div>
    <div style="position:absolute; left: 393px; width:100px; height:40px; cursor:pointer; opacity:0.3;" onClick="go_page(4)">
            	<img id="tittle_back_4" src="./img/bar_off.png" style="position:absolute; left: -22px; top:0px;">
                <img src="./img/bar_text_4.png" style="position:absolute; left: 6px; top:3px;">
    </div>
	</div>
        <a id="out" class="btn_close" style="position:absolute; left: 782px; top:349px;cursor:pointer;" onClick="out()"></a>
        <iframe id="com" src="./set_welcome_page/index.php" scrolling="no" frameborder="0"  width="642" height="355" style="position : absolute; top: 73px; left:100px;"></iframe>
	</div>
	
    <!-- 存檔物件 -->   
	<div style="position:absolute; left: 170px; top:95px;">
    	
          <img src="../img/UI_savef.png" style="position:absolute; left: 679px; top:172px; cursor:no-drop; opacity:0.4">
          <a id="save_btn" class="btn_save" onClick="go_save()" style="position:absolute; left: 679px; top:172px; display:none;"></a>
	</div>
	<!-- 開頭-->
<img src="./img/open_tittle.png" style="position:absolute; left: 35px; top:-7px;">
<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var on_page = 1;
	var on_chick = -1;

	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	function out()
	{
		if(window.document.getElementById("save_btn").style.display == "block")
		{
			cover("在離開之前<BR>是否要存檔",3,function(){
						window.parent.set_page("");	
					},function(){
				
				go_save(function(){window.parent.set_page("");},1);
				});
		}
		else
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
	//點選設定圖
	function click_book(value)
	{
		on_chick = value;
		window.parent.click_book_sid = book_info[value]["book_sid"];
		window.parent.click_book_name = book_info[value]["book_name"];
		for(var i = 0 ; i < 10 ; i++)
		{
			window.document.getElementById("book_"+i).src = "./img/a_book_img.png";
		}
		window.document.getElementById("read_rec_btn").style.display = "block";
		window.document.getElementById("chick_book_name").innerHTML =book_info[value]["book_name"];
		window.document.getElementById("book_"+value).src = "./img/a_book_img_l.png";
	}
	//轉換至觀看頁面
	function read_rec(){
		cover("讀取中");
		window.parent.set_page('page_rec_read');
	}
	function go_page(value)
	{
	
	if(<? echo $_SESSION["uid"];?> != 1238 && (value == 4 || value == 3))return false;
	//轉換分頁
		if(on_page != value)
		{
			if(window.document.getElementById("save_btn").style.display == "block")
			{
				cover("在離開之前<BR>是否要存檔",3,function(){
						on_page = value;
						var i = 1;
						for(; i <= 2 ; i++)
						{
							window.document.getElementById("tittle_back_"+i).src="./img/bar_off.png";
						}
						window.document.getElementById("tittle_back_"+value).src="./img/bar_on.png";
						
						if(value==1)window.document.getElementById("com").src="./set_welcome_page/index.php";
						if(value==2)window.document.getElementById("com").src="./set_star_page/index.php";
						if(value==3)window.document.getElementById("com").src="./set_user_black_page/index.php";
						window.document.getElementById("save_btn").style.display = "none";
					},
					function(){
						window.document.getElementById("save_btn").style.display = "none";
						go_save(function(){go_page(value)},value);
						
						
					});	
			}else
			{
				on_page = value;
				var i = 1;
				for(; i <= 4 ; i++)
				{
					window.document.getElementById("tittle_back_"+i).src="./img/bar_off.png";
				}
				window.document.getElementById("tittle_back_"+value).src="./img/bar_on.png";
				
				if(value==1)window.document.getElementById("com").src="./set_welcome_page/index.php";
				if(value==2)window.document.getElementById("com").src="./set_star_page/index.php";
				if(value==3)window.document.getElementById("com").src="./set_user_black_page/index.php";
				if(value==4)window.document.getElementById("com").src="./set_achievement _page/index.php";
				window.document.getElementById("save_btn").style.display = "none";
			}
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
	function go_save(fun,value)
	{	
			if(fun!=null)
			document.getElementById('com').contentWindow.go_save(fun,value);
			else
			document.getElementById('com').contentWindow.go_save(null,null);
	}
	//=========MAIN=============
	function main()
	{
		 //cover("");
	}
	
	
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	//get_shelf_count();
	main();


    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    