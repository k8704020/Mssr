<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 觀看推薦內容
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
		require_once(str_repeat("../",3)."/inc/get_book_info/code.php");

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

        $user_id        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
		if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");
		$hom = "http://".$arry_ftp1_info['host']."/mssr/";
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
	<Title>瀏覽書架的單本書推薦內容(含聊書)</Title>
    <!-- 掛載 -->
	<script type="text/javascript" src="../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
	<script src="../../../lib/jquery/ui/code.js"></script>
    <script src="../js/select_thing.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../css/btn.css">
    <style>
		.border_{
			border-radius:10px;
		}
		.star{
			position:absolute;
			width:25px;
			height:25px;
			background: url('./img/star.png') -25px 0;
			background-size:50px;
		}
		.feri_good_n{
			position:absolute;
			width:34px;
			height:30px;
			background: url('./img/good.png') -0px -30px;
		}
		.feri_good_n:hover {
    		background: url('./img/good.png') -34px -30px;
		}
		.feri_good{
			position:absolute;
			width:34px;
			height:30px;
			background: url('./img/good.png') -0px -0px;
		}
		.feri_good:hover {
    		background: url('./img/good.png') -34px -0px;
		}
		.star_n{
			position:absolute;
			width:25px;
			height:25px;
			background: url('./img/star.png') 0 0;
			background-size:50px;
		}

		.s0{
			position:absolute;
			width:35px;
			height:34px;
			background: url('./img/star_line_big.png') 0 0;
		}
		.s1{
			position:absolute;
			width:35px;
			height:34px;
			background: url('./img/star_line_big.png') -35px 0;
		}
		.s2{
			position:absolute;
			width:35px;
			height:34px;
			background: url('./img/star_line_big.png') -70px 0;
		}
		.s3{
			position:absolute;
			width:35px;
			height:34px;
			background: url('./img/star_line_big.png') -105px 0;
		}
		.bar_1
		{
			text-align:center;
			border:0px ;
		}
		.bar_2
		{
			text-align:center;
			border:1px solid;
			color:#AAA;

			box-shadow: 2px 2px 1px #440;
			font-size:20px;
			text-shadow:#666
			width:600px;
			height:120px;

			position: absolute;
			top:340px;
			left:80px;
			transition: 0.1s;
		}
		.bar_2_s
		{
			text-align:center;
			border:2px solid #0AF;
			color:#AAA;

			box-shadow: 2px 2px 1px #44a0;
			font-size:20px;
			text-shadow:#666
			width:600px;
			height:120px;

			position: absolute;
			top:340px;
			left:80px;
			transition: 0.1s;
		}
		.abrrr1{}
		.abrrr2{
			background:#f3e4bc;
			font-weight:bold;
			/*border-top: 1px solid #fff5c4;
   background: #e3ca8b;
   background: -webkit-gradient(linear, left top, left bottom, from(#fff8ab), to(#e3ca8b));
   background: -webkit-linear-gradient(top, #fff8ab, #e3ca8b);
   background: -moz-linear-gradient(top, #fff8ab, #e3ca8b);
   background: -ms-linear-gradient(top, #fff8ab, #e3ca8b);
   background: -o-linear-gradient(top, #fff8ab, #e3ca8b);*/

			}
			.line_bar
			{
				box-shadow:0px 0px 1px 3px rgba(100%,100%,0%,1)
			}
	</style>
</Head>
<body>

	<!--==================================================
    html內容
    ====================================================== -->
	<!-- 底圖-->
	<div style="position:absolute; top:0px; left:0px; width:1000px; height:500px; background-color:#000000; opacity:0.8;" onClick=""></div>
        <img src="../img/book_page_back2.png" width="1202" height="483" border="0" style="position:absolute; top:0px; left:-80px; width: 1140px; height: 485px;">

    <!-- 推薦格-->
    <!-- 改過 -->
	<div id="book_name" style="position:absolute; top:10px; left:38px;  font-family: Microsoft JhengHei;overflow:hidden; font-size:32px; color:#630; width: 427px; height: 40px;line-height:48px;" align="center"></div>
	 <!-- 改過 -->
    <div id="r_1" style="position:absolute; top:61px; left:140px;" class="star_n"></div>
     <!-- 改過 -->
	<div id="r_2" style="position:absolute; top:61px; left:176px;" class="star_n"></div>
	 <!-- 改過 -->
    <div id="r_3" style="position:absolute; top:61px; left:212px;" class="star_n"></div>
     <!-- 改過 -->
    <div id="r_4" style="position:absolute; top:61px; left:248px;" class="star_n"></div>
     <!-- 改過 -->
    <div id="r_5" style="position:absolute; top:61px; left:284px;" class="star_n"></div>



    <!-- omg 1 -->
	<div id="feri_good_bar_1" style="position:absolute; top:34px; left:608px;">
	        <img id="feri_good_bg_1" src="./img/gb2.png" style="position:absolute; top:2px; left:3px;">
	        <div id="feri_good_count_1" style="position:absolute; top:-5px; left:9px; width: 114px; text-align:left; color:#7f552c; font-family: Microsoft JhengHei;font-size: 16px;">120人說讚</div>
	        <a id="feri_good_1" class="feri_good" onClick="set_good(1)" border="0" style="position:absolute; top:-27px; left:4px;display:none;"></a>
	</div>
	<!-- 評星理由1的底圖 -->
    <img src="./img/BAR.png" border="0" style="position:absolute; top:94px; left:140px;">
    <!-- 評星理由2的底圖 --> 
    <img src="./img/BAR.png" border="0" style="position:absolute; top:128px; left:140px; height: 25px; width: 310px;">
    <!-- 繪圖-左1縮圖的 底圖-->  
    <img src="./img/BAR2.png" border="0" style="position:absolute; top:159px; left:200px; width: 52px;">
    <!-- 繪圖-左2縮圖的 底圖--> 
    <img src="./img/BAR2.png" border="0" style="position:absolute; top:159px; left:260px; width: 52px;">
    <!-- 繪圖-左3縮圖的底圖--> 
    <img src="./img/BAR2.png" border="0" style="position:absolute; top:159px; left:320px; width: 52px;">
    <!-- 繪圖-左4縮圖的 底圖--> 
	<img src="./img/BAR2.png" border="0" style="position:absolute; top:159px; left:380px; width: 52px;">
	<!-- 改過 -->
    <img src="./img/BAR3.png" border="0" style="position:absolute; top:58px; left:510px; width: 406px; height: 134px;">
    <img src="./img/BAR3.png" border="0" style="position:absolute; top:245px; left:510px; width: 406px; height: 151px;">
     <!-- 評星理由1的文字 -->  
	<div id="rec_reason_1" style="position:absolute; top:94px; left:140px; white-space:nowrap; font-family: Microsoft JhengHei; font-size:20px; color:#7c552c; width: 301px; height: 28px;" align="left"></div>
	  <!-- 評星理由2的文字 -->  
    <div id="rec_reason_2" style="position:absolute; top:122px; left:140px; white-space:nowrap; font-family: Microsoft JhengHei; font-size:20px; color:#7c552c; width: 301px; height: 32px;" align="left"></div>
 <!-- 改過 -->
	<canvas id="up_show_draw_info"  width="700" height="400" style="position:absolute; top:192px; left:55px; height:204px; width:390px;" ></canvas>
   <!-- 繪畫的大圖 -->  
	<img src="" width="413" height="215" id="big_pic"  style="position:absolute; top:192px; left:55px; height:204px; width:390px;" >
	<!--  FORUM -->
	<div id="forum" style="position:absolute; top:200px; left:530px; width:300px; display:none;" ><BR>
			  <!-- 改過 -->
            <div id="post_count" style="position:absolute; top:10px; left:185px; width: 200px; text-align:right; font-family:微軟正黑體;color:#7f552c; font-size: 16px;" class="little">aaa</div>
            <div id="po" style="position:absolute; top:116px; left:15px; width: 200px; color: #7D4622;z-index:-1;">尚未發文，無顯示內容</div>

            <!--  發文列表內文 -->
            <div style="position: relative;width: 400px;height: 250px;">

			  <!-- 改過 -->
             	 <select id="post_tittle_list" class="text_1" onchange="show_post_conten();" style="width:340px;font-size:18px; position:relative; float:left;left:-20px;top:16px;">
             	 </select>
              <!-- 改過 -->
              	<button style="position:absolute;  left:322px; top:16px; cursor:pointer; width:63px;" onClick="go_talk()">去聊書</button>
              <!-- 改過 -->
              	<textarea id="post_conten_list"  placeholder="選擇的標題觀看發文內容" class="text_1" style="width:399px;font-size:18px; height:130px; resize:none; display;position: absolute;top:40px; left:-20px;" readonly></textarea>
               	<div id="sadfasf" onClick="open_jump('forum')" onMouseOver="window.document.getElementById('post_conten_list').className='line_bar'" onMouseOut="window.document.getElementById('post_conten_list').className=''" style="position:absolute; top:72px; left:2px; width: 374px; height: 124px; cursor:pointer;"></div>
                <img id="feri_good_bg_1" src="./img/gb2.png" style="position:absolute; top:-20px; left:79px;">
                <img id="feri_good_bg_1" src="./img/gb2.png" style="position:absolute; top:-3px; left:79px;">

              	<div id="list_show2" style="position:absolute; top:-20px; left:88px; width: 124px; display:;color:#7f552c;font-size: 16px;">0人說讚</div>

                <div id="list_show4" style="position:absolute; top:-3px; left:88px; width: 129px; display:;color:#7f552c;font-size: 16px;">0篇回文</div>
            </div>
</div>

<!-- omg 3 -->
    <div id="feri_good_bar_3" style="position:absolute; top:149px; left:125px;">
        <img id="feri_good_bg_3" src="./img/gb2.png" style="position:absolute; top:26px; left:5px;">
        <div id="feri_good_count_3" style="position:absolute; top:18px; left:5px; width: 94px; text-align:left; color:#7f552c; font-family: Microsoft JhengHei; font-size: 16px;">120人說讚</div>
        <a id="feri_good_3" class="feri_good" onClick="set_good(3)" border="0" style="position:absolute; top:-3px; left:3px; display:none;"></a>
	</div>
	 <!-- 改過 -->
	<textarea name="up_show_text_info" cols=34 rows=9 readonly id="up_show_text_info" style="position:absolute; top:58px; left:510px; font-size:18px; resize: none; width: 399px; height: 136px;"></textarea>
    <div id="up_show_text_info_br" onClick="open_jump('text')" onMouseOver="window.document.getElementById('up_show_text_info').className='line_bar'" onMouseOut="window.document.getElementById('up_show_text_info').className=''" style="position:absolute; top:59px; left:531px; width: 375px; height: 133px; cursor:pointer; display:none;"></div>
    <!-- 繪圖-左1縮圖-->  
    <div id="p_btn_1" onClick="click_p(1)" class="bar_1" style="position:absolute; width:50px; height:30px; top:156px; left:200px;"  ><img id="p_img_1" style="width:50px; height:30px;" src="img/im.png" border="0"></div>
	 <!-- 繪圖-左2縮圖--> 
	<div id="p_btn_2" onClick="click_p(2)" class="bar_1" style="position:absolute; width:50px; height:30px; top:156px; left:260px;"  ><img id="p_img_2" style="width:50px; height:30px;"  src="img/im.png" border="0"></div>
	 <!-- 繪圖-左3縮圖-->  
	<div id="p_btn_3" onClick="click_p(3)" class="bar_1" style="position:absolute; width:50px; height:30px; top:156px; left:320px;"  ><img id="p_img_3" style="width:50px; height:30px;"  src="img/im.png" border="0"></div>
	 <!-- 繪圖-左4縮圖--> 
	<div id="p_btn_4" onClick="click_p(4)" class="bar_1" style="position:absolute; width:50px; height:30px; top:156px; left:380px;"  ><img id="p_img_4" style="width:50px; height:30px;"  src="img/im.png" border="0"></div>
	<!-- 左邊按鈕--> 
    <a id="left_btn" class="btn_arrow_l" onClick="set_page(-1)" style="position:absolute; cursor:pointer; left:0px; top: 238px;display:none;"></a>
    <!-- 右邊按鈕--> 
    <a id="right_btn" class="btn_arrow_r" onClick="set_page(1)" style="position:absolute; cursor:pointer; left:898px; top: 238px;display:none;"></a>

    <a id="booking_btn" class="btn_booking" onClick="booking()" border="0" style="position:absolute; top:381px; left:780px; display:none;" >
    <!-- 改過 -->
    <a id="out" class="btn_close" onClick="out()" border="0" style="position:absolute; top:381px; left:875px;"></a>





    <img src="img/BAR4.png" style="position:absolute; top:421px; left:60px; height: 26px; width: 386px;">
	<div id="recode_bar"  style="position : absolute; top: 387px; left:185px;display:none;">

	  <div id="player" style="position:absolute; top:31px; left:-125px; height:32px; width:388px; background-color:#f5e4bb; overflow:hidden;">

      </div>
    </div>
    <!-- omg 2 -->
	<div id="feri_good_bar_2" style="position:absolute; top:378px; left:134px;display:;">
        <img id="feri_good_bg_2" src="./img/gb2.png" style="position:absolute; top:22px; left:-4px;">
        <div id="feri_good_count_2" style="position:absolute; top:20px; left:-4px; width: 114px; text-align:left; color:#7f552c; font-family: Microsoft JhengHei;font-size: 16px;">120人說讚</div>
        <a id="feri_good_2" class="feri_good" onClick="set_good(2)" border="0" style="position:absolute; top:-3px; left:3px; display:none;"></a>
	</div>


    <!-- 標籤格-->
	<img src="./img/stars_pot.png" width="121" border="0" style="position:absolute; top:52px; left:7px;width: 125px;">
	<!-- 改過 -->
    <img src="./img/text_pot.png"  border="0" style="position:absolute; top:17px; left:480px; width: 125px;">
    <img src="./img/star_pot.png"  border="0" style="position:absolute; top:97px; left:7px; width: 125px;">
    <img src="./img/draw_pot.png"  border="0" style="position:absolute; top:160px; left:7px; width: 125px;">
    <img src="./img/recode_pot.png"  border="0" style="position:absolute; top:379px; left:7px; width: 125px;">
    <!-- 改過 -->
    <img src="./img/draw_forum.png"  border="0" style="position:absolute; top:198px; left:480px; width: 125px;">
    <!-- 星星標記格-->
	<div id="s_1" class="s0" style="position:absolute; top:161px; left:82px;"></div>
	    <!-- 改過 -->
	<div id="s_2" class="s0" style="position:absolute; top:18px; left:555px;"></div>
	<div id="s_3" class="s0" style="position:absolute; top:382px; left:82px;"></div>

	<!-- 改過 -->
    <a  class="btn_help" onClick="open_helper(10)" style="position:absolute; top:-6px; left:890px; cursor:pointer;"></a>
	<div id="helper" style="position:absolute; top:-8px; left:-8px; width:1040px; height:480px; display:none; overflow:hidden;"></div>

	<!-- 跳超大的窗窩 -->
    <div id="jump_bar" style="width:0px; height:0px; position:absolute; top:0px; left:0px; display:none;">
    	<!-- 改過 -->
		<div class="border_" style="background-color:#2f1308; opacity:0.85; width:928px; height:456px; top:8px; left:13px; position:absolute;"></div>
		<!-- 改過 -->
		<textarea id="jump_text" readonly  style="BORDER-BOTTOM: 0px solid; BORDER-LEFT: 0px solid; BORDER-RIGHT: 0px solid; BORDER-TOP: 0px solid; resize: none; color:#FFF; font-size:24px; position:absolute; top:24px; width: 900px; height: 382px; left: 25px; background-color:transparent;"></textarea>
   		<button onClick="window.document.getElementById('jump_bar').style.display='none';" style="position:absolute; top:416px; left:445px; width:100px; font-size:18px;">關閉</button>
    	<img id="jump_tittle" src="./img/text_pot.png"  border="0" style=" display:none;font-size:28px; position:absolute; top:41px; left:47px;" >
	</div>

	<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var bookin_on = -1; //-1無法訂閱   0//尚未訂閱   1//以訂閱
	var on_booking30 = 0 ; // 30天內是否已跟這位仁兄交易過   0//沒有 1//有
	var coco = 0;
	var img_link = new Array();
	var link_count = 0;
	var booksid = "";
	var article_id = 0;
	//按讚
	var draw_on = 0;
	var feri_good_count = new Array(0,0,0,0,0,0);
	var have_good  = new Array(0,0,0,0,0,0);
	var btn_good_lock = "ok";
	var rec_draw_has_list  = new Array(0,0,0,0);
	var good_flag  = new Array(0,0,0,0,0,0);
	var host = '<? echo $hom;?>';
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------

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
		if(type == 2)
		{
			delayExecute(proc);
		}
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}
	//開啟跳窗
	function open_jump(val)
	{

		if(val == 'text')
		{
			window.document.getElementById("jump_bar").style.display="block";
			window.document.getElementById("jump_text").value = "文字推薦\n────────────────────────────\n"+document.getElementById('up_show_text_info').value;
			window.document.getElementById("jump_tittle").src="./img/text_pot.png";
		}
		if(val == 'forum')
		{
			window.document.getElementById("jump_bar").style.display="block";
			var x = document.getElementById("post_tittle_list").selectedIndex;

			window.document.getElementById("jump_text").value = "聊書"+document.getElementsByTagName("option")[x].innerHTML+"\n────────────────────────────\n"+document.getElementById('post_conten_list').value;
			window.document.getElementById("jump_tittle").src="./img/draw_forum.png";
		}

	}
	//設置播放裝置
	function set_player(value,file)
	{
		var record_path = host+"info/user/"+window.parent.home_id+"/book/"+value+"/record/"+file;



		var audio = new Audio();
			audio.src = record_path;
			audio.controls = true;
			window.document.getElementById("player").style.display= "block";
			window.document.getElementById("player").innerHTML =' <video controls muted style="display:none;" ></video>';
			//recordingPlayer=recordingDIV.querySelector('video');
			window.document.getElementById("player").appendChild(audio);

			//if(audio.paused) audio.play();

			audio.onended = function() {
				//window.document.getElementById("player").style.display= "block";
				audio.pause();
				//audio.src = URL.createObjectURL(button.recordRTC.blob);
			};
	}

	//訂閱
	function booking()
	{
		if(booksid == "") return false;
		if(on_booking30 == 1)//30天內不可以跟同一個人交易同一本書
		{
			cover("書籍已經訂閱過<BR>建議去看看其他書籍",1);
			return false;
		}
		echo("booking():初始開始:訂閱進行中");
		cover("讀取中");
		var url ="";
		if(bookin_on ==0)url = "./ajax/set_booking.php";
		if(bookin_on ==1)url = "./ajax/set_unbooking.php";
		$.post(url, {
					user_id:window.parent.user_id,
					home_id:window.parent.home_id,
					book_sid:booksid,
					user_permission:window.parent.user_permission

			}).success(function (data)
			{
				echo("AJAX:success:booking():訂閱進行中:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",2,function(){booking();});
					echo("AJAX:success:booking():訂閱進行中:資料庫發生問題");
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
					if(coco > 5)
					{
						cover("按鈕按太多次按鈕 畫面鎖定");
					}
					else if(data_array["booking_state"]==1)
					{

						bookin_on = 0;
						window.document.getElementById("booking_btn").className = "btn_booking";
						cover("成功取消訂閱",1);
						if(coco > 3)cover("請勿玩按鈕喔<br>不然會減少葵幣",1);
					}else if(data_array["booking_state"]==0)
					{

						bookin_on = 1;
						window.document.getElementById("booking_btn").className = "btn_unbooking";
						cover("訂閱完成",1);
						if(coco > 3)cover("請勿玩按鈕喔<br>不然會減少葵幣",1);
					}
					coco++;
				}

			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){
				echo("AJAX:complete:booking():訂閱進行中:");
			});
	}
	//設定推薦按讚
	function set_good(value)
	{
		cover("讚!!送出中");
		if(btn_good_lock=="") return false;
		btn_good_lock = "";
		cover("讚!!送出中!?");
		var tmp = value;
		if(value == 3)value = draw_on+value;
		var url = "./ajax/set_rec_good.php";
		$.post(url, {
					user_id:window.parent.user_id,
					home_id:window.parent.home_id,
					type:good_flag[value-1],
					book_sid:booksid,
					user_permission:window.parent.user_permission
			}).success(function (data)
			{
				echo("AJAX:success:set_good():設定好友:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>",1);
					echo("AJAX:success:set_good():設定好友:資料庫發生問題");
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

					if(data_array["type"]==0)
					{//OP
						if(value<3)window.document.getElementById("feri_good_"+value).className = "feri_good_n";
						else window.document.getElementById("feri_good_3").className = "feri_good_n";
						feri_good_count[good_flag[value-1]] --;
						have_good[good_flag[value-1]]=0;
						cover("成功回收讚",1);
					}else
					{//CL

						if(value<3)window.document.getElementById("feri_good_"+value).className = "feri_good";
						else window.document.getElementById("feri_good_3").className = "feri_good";
						feri_good_count[good_flag[value-1]] ++ ;
						have_good[good_flag[value-1]]=1;
						cover("成功按讚",1);
					}
					if(value<3)window.document.getElementById("feri_good_count_"+value).innerHTML = feri_good_count[value]+"人按讚";
					else window.document.getElementById("feri_good_count_3").innerHTML = feri_good_count[good_flag[value-1]]+"人按讚";
					btn_good_lock = "ok";
				}

			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");

			}).complete(function(e){
				echo("AJAX:complete:set_good():設定推薦按讚:");
			});
	}
	//=========MAIN=============
	function main()
	{
		echo("Main:初始開始:讀取推薦資料");
		cover("讀取中")

		var url = "./ajax/get_rec_info.php";
		$.post(url, {
					user_id:window.parent.home_id,
					book_sid:window.parent.click_book_sid,
					user_permission:window.parent.user_permission,
					read_on:window.parent.read_on,
					read_max_count:window.parent.read_max_count

			}).success(function (data)
			{
				echo("AJAX:success:main():讀取使用者資料:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",2,function(){main();});
					echo("AJAX:success:main():讀取推薦資料:資料庫發生問題");
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

					//========================設定畫面=======================================
					document.getElementById("feri_good_bar_1").style.display = "none";
					document.getElementById("feri_good_bar_2").style.display = "none";
					document.getElementById("feri_good_bar_3").style.display = "none";

					feri_good_count = new Array(0,0,0,0,0,0);

					booksid = data_array["book_sid"];
					//書名
					window.document.getElementById("book_name").innerHTML = data_array["book_name"];
					//星等設置

					for(var i = 1 ; i <=5 ; i++)
					{
						if(data_array["rec_rank"]>=i)window.document.getElementById("r_"+i).className ="star";
						else window.document.getElementById("r_"+i).className ="star_n";
					}
					//if(data_array["rec_rank"] !=0)window.document.getElementById("r_"+data_array["rec_rank"]).src ="./img/r_"+data_array["rec_rank"]+".png";

					//推薦理由
					window.document.getElementById("rec_reason_1").innerHTML = data_array["rec_reason_1"];
					if(data_array["rec_reason_1"]=="")
					window.document.getElementById("rec_reason_1").className = "abrrr1";
					else window.document.getElementById("rec_reason_1").className = "abrrr2";
					window.document.getElementById("rec_reason_2").innerHTML = data_array["rec_reason_2"];
					if(data_array["rec_reason_2"]=="")
					window.document.getElementById("rec_reason_2").className = "abrrr1";
					else window.document.getElementById("rec_reason_2").className = "abrrr2";
					//文字
					 document.getElementById('up_show_text_info').value ="一句話：\n"+data_array['rec_content_1']+"\n\n內容：\n"+data_array['rec_content_2']+"\n\n學到的事：\n"+data_array['rec_content_3'];
					if(data_array['rec_content_1']=="" &&data_array['rec_content_2']=="" &&data_array['rec_content_3']=="" )
					{
						window.document.getElementById("up_show_text_info").style.display = "none";
						window.document.getElementById("up_show_text_info_br").style.display = "none";

					}
					else
					{
						window.document.getElementById("up_show_text_info_br").style.display = "block";
						window.document.getElementById("up_show_text_info").style.display = "block";
						document.getElementById("feri_good_bar_1").style.display = "block";
					}
					rec_draw_has_list = data_array["rec_draw_has_list"];
					good_flag  = new Array(1,2,0,0,0,0);
					var tmpmpm = 0;
					for(var i = 0 ; i<=3;i++)
					{
						if(rec_draw_has_list[i] == 1)
						{
							good_flag[(tmpmpm+2)] = i+3
							tmpmpm++;
						}
					}

					//繪圖上傳型
					link_count = data_array["upload_cno"];
					for(var i = 1 ; i<=4;i++)
					{

						if(i <= data_array["upload_cno"])
						{
							document.getElementById("feri_good_bar_3").style.display = "block";
							window.document.getElementById("p_btn_"+i).className = "bar_2";

							window.document.getElementById("p_img_"+i).src = data_array["rec_draw_link_list"][i];

							img_link[i] = data_array["rec_draw_link_list"][i];
						}else
						{window.document.getElementById("p_img_"+i).src = 'img/im.png';
						window.document.getElementById("p_btn_"+i).className = "bar_1";}
					}
					if(link_count > 0) click_p(1);
					else window.document.getElementById("big_pic").src = "img/BAR3.png";
					//朗讀
					if(data_array["rec_record_book_sid"] != "" )
					{
						document.getElementById("feri_good_bar_2").style.display = "block";
						document.getElementById("recode_bar").style.display = "block";
						set_player(data_array["rec_record_book_sid"] , data_array["rec_record_file"]);
					}else
					{
						document.getElementById("recode_bar").style.display = "none";
					}


					//設定訂閱 與 按讚
					on_booking30 = data_array["on_booking30"];
					if(window.parent.home_on != "user")
					{
						if(data_array["on_booking"]>0)
						{
							bookin_on = 1;
							window.document.getElementById("booking_btn").className = "btn_unbooking";
						}else
						{
							bookin_on = 0;
							window.document.getElementById("booking_btn").className = "btn_booking";
						}

						document.getElementById("booking_btn").style.display = "block";
						document.getElementById("feri_good_1").style.display = "block";
						document.getElementById("feri_good_2").style.display = "block";
						document.getElementById("feri_good_3").style.display = "block";

					}
					//按讚量
					for(var i = 1 ; i <= 6 ; i++)
					{
						feri_good_count[i] = data_array["have_good_count_"+i];
						have_good[i] = data_array["have_good_"+i];
						//window.document.getElementById("feri_good_count").innerHTML = feri_good_count+"人按讚";
					}

					window.document.getElementById("feri_good_count_1").innerHTML = feri_good_count[1]+"人按讚";
					window.document.getElementById("feri_good_count_2").innerHTML = feri_good_count[2]+"人按讚";
					for(var i = 3 ; i >= 0 ; i--)
					{

						if(rec_draw_has_list[i] == 1)
						{
							window.document.getElementById("feri_good_count_3").innerHTML = feri_good_count[(i+3)]+"人按讚";
							draw_on = i;
						}
						else
						{

						}

					}
					//有無按過好棒棒
					for(i = 1 ; i <= 2 ; i++)
					{
						if(have_good[i]==1)
						{
							window.document.getElementById("feri_good_"+i).className = "feri_good";

						}
						else
						{

							window.document.getElementById("feri_good_"+i).className = "feri_good_n";
						}
					}

					if(have_good[(draw_on+3)]==1)
					{
						window.document.getElementById("feri_good_3").className = "feri_good";
					}
					else
					{
						window.document.getElementById("feri_good_3").className = "feri_good_n";
					}

					//評價
					window.document.getElementById("s_1").className = "s"+data_array["c_draw"];;
					window.document.getElementById("s_2").className = "s"+data_array["c_text"];;
					window.document.getElementById("s_3").className = "s"+data_array["c_record"];;

					if(window.parent.read_on == 0)
					window.document.getElementById("left_btn").style.display = "none";
					else window.document.getElementById("left_btn").style.display = "block";

					if(window.parent.read_on+1 == window.parent.read_max_count)
					window.document.getElementById("right_btn").style.display = "none";
					else window.document.getElementById("right_btn").style.display = "block";
					get_post_list(booksid);
					cover("");

				}
			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){
				echo("AJAX:complete:main():讀取推薦資料:");
			});
	}

	//FUN
	function click_p(value)
	{

		if(value>link_count) return false;
		for(var i = 1; i <= link_count; i++)
		{

			window.document.getElementById("p_btn_"+i).className = "bar_2";
		}

		draw_on = value-1;

		if(have_good[good_flag[(draw_on+2)]]==1)
		{
			window.document.getElementById("feri_good_3").className = "feri_good";
		}
		else
		{
			window.document.getElementById("feri_good_3").className = "feri_good_n";
		}

		window.document.getElementById("feri_good_count_3").innerHTML = feri_good_count[good_flag[(draw_on+2)]]+"人按讚";

		window.document.getElementById("p_btn_"+value).className = "bar_2_s";
		window.document.getElementById("big_pic").src = img_link[value];
	}
	//轉換至上架頁
	function out(){
		cover("讀取中");
		window.parent.set_page('page_shelf_menu');
	}
	function show_post_list()
	{
		window.document.getElementById("post_tittle_list").style.display = "block";
		window.document.getElementById("post_conten_list").style.display = "none";

		window.document.getElementById("list_show2").style.display = "none";

		window.document.getElementById("list_show4").style.display = "none";
		window.document.getElementById("post_tittle_list").innerHTML = '';
		for(var i = 0 ; i < post_list.length ; i++)
		{

			window.document.getElementById("post_tittle_list").innerHTML =window.document.getElementById("post_tittle_list").innerHTML+'<option value="'+i+'">標題 : '+post_list[i]["article_title"]+'</option>'

		}
		window.document.getElementById("post_count").innerHTML = "共"+post_list.length+"篇發文數";
		if(post_list.length>=1)
		{
			window.document.getElementById("forum").style.display = "block";
			show_post_conten(1);

		}
	}
	function get_post_list(booksid)
	{
		cover("讀取聊書資料")
		var url = "../page_rec_edit/rec_forum/ajax/get_post_list.php";

		$.post(url, {
					user_id:window.parent.home_id,
					book_sid:booksid,

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
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){
			});
	}
	function show_post_conten(vvv)
	{
		if (vvv == 1)
		{
			window.document.getElementById("post_conten_list").innerHTML="內容 : "+post_list[0]["article_content"];
			window.document.getElementById("list_show2").innerHTML=""+post_list[0]["article_like_cno"]+"人說讚";
			window.document.getElementById("list_show4").innerHTML=""+post_list[0]["article_report_cno"]+"篇回文";
			article_id = post_list[0]["article_id"];


		}else
		{
			var e = document.getElementById("post_tittle_list");
			var strUser = e.options[e.selectedIndex].value;
			window.document.getElementById("post_conten_list").innerHTML="內容 : "+post_list[e.selectedIndex]["article_content"];
			window.document.getElementById("list_show2").innerHTML=""+post_list[e.selectedIndex]["article_like_cno"]+"人說讚";
			window.document.getElementById("list_show4").innerHTML=""+post_list[e.selectedIndex]["article_report_cno"]+"篇回文";
			article_id = post_list[e.selectedIndex]["article_id"];
		}
		window.document.getElementById("post_conten_list").style.display = "block";

		window.document.getElementById("list_show2").style.display = "block";

		window.document.getElementById("list_show4").style.display = "block";

	}
	function go_talk()
	{

		cover("確定要前往聊書嗎?",2,function(){
		window.parent.set_action_bookstore_log(window.parent.user_id,'e43',window.parent.action_on);
		// window.open('../../forum/view/reply.php?get_from=1&article_id='+article_id);});
		window.parent.parent.location.href = '../../forum/view/reply.php?get_from=1&article_id='+article_id;});
	}
	//---------------------------------------------------
    //更換頁面
    //---------------------------------------------------
	function set_page(value)
	{
		window.parent.read_on = window.parent.read_on+value;
		window.parent.set_page('page_rec_read_v_forum');
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
main();
	cover("");
    </script>
</Html>














