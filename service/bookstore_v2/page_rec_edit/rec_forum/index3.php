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
        $book_sid    =(isset($_GET['book_sid']))?mysql_real_escape_string($_GET['book_sid']):0;
        $book_name         =(isset($_GET['book_name']))?mysql_real_escape_string($_GET['book_name']):"？";

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

    <!-- 掛載 -->
    <script type="text/javascript" src="../../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
    <script type="text/javascript" src="../../../../service/forum/inc/add_action_forum_log/code.js"></script>
    <link rel="stylesheet" href="../../css/btn.css">
    <style type="text/css">
	.buttoni {
	   border-top: 1px solid #96d1f8;
	   background: #65a9d7;
	   background: -webkit-gradient(linear, left top, left bottom, from(#3e779d), to(#65a9d7));
	   background: -webkit-linear-gradient(top, #3e779d, #65a9d7);
	   background: -moz-linear-gradient(top, #3e779d, #65a9d7);
	   background: -ms-linear-gradient(top, #3e779d, #65a9d7);
	   background: -o-linear-gradient(top, #3e779d, #65a9d7);
	   padding: 5.5px 11px;
	   -webkit-border-radius: 8px;
	   -moz-border-radius: 8px;
	   border-radius: 8px;
	   -webkit-box-shadow: rgba(0,0,0,1) 0 1px 0;
	   -moz-box-shadow: rgba(0,0,0,1) 0 1px 0;
	   box-shadow: rgba(0,0,0,1) 0 1px 0;
	   text-shadow: rgba(0,0,0,.4) 0 1px 0;
	   color: white;
	   font-size: 18px;
	   font-family: Georgia, serif;
	   text-decoration: none;
	   vertical-align: middle;
	   }
	.buttoni:hover {
	   border-top-color: #28597a;
	   background: #28597a;
	   color: #ccc;
	   }
	.buttoni:active {
	   border-top-color: #1b435e;
	   background: #1b435e;
	   }
	.button_on {
	   border-top: 1px solid #bcc5cd;
	   background: #bcc5cd;
	   background: -webkit-gradient(linear, left top, left bottom, from(#bcc5cd), to(#bcc5cd));
	   background: -webkit-linear-gradient(top, #bcc5cd, #bcc5cd);
	   background: -moz-linear-gradient(top, #bcc5cd, #bcc5cd);
	   background: -ms-linear-gradient(top, #bcc5cd, #bcc5cd);
	   background: -o-linear-gradient(top, #bcc5cd, #bcc5cd);
	   padding: 4.5px 9px;
	   -webkit-border-radius: 4px;
	   -moz-border-radius: 4px;
	   border-radius: 4px;
	   -webkit-box-shadow: rgba(0,0,0,1) 0 1px 0;
	   -moz-box-shadow: rgba(0,0,0,1) 0 1px 0;
	   box-shadow: rgba(0,0,0,1) 0 1px 0;
	   text-shadow: rgba(0,0,0,.4) 0 1px 0;
	   color: #000000;
	   font-size: 14px;
	   font-family: Helvetica, Arial, Sans-Serif;
	   text-decoration: none;
	   vertical-align: middle;
	   cursor:pointer;
	   }
		.button {
	   border-top: 1px solid #a8deff;
	   background: #dce5ed;
	   background: -webkit-gradient(linear, left top, left bottom, from(#ffffff), to(#dce5ed));
	   background: -webkit-linear-gradient(top, #ffffff, #dce5ed);
	   background: -moz-linear-gradient(top, #ffffff, #dce5ed);
	   background: -ms-linear-gradient(top, #ffffff, #dce5ed);
	   background: -o-linear-gradient(top, #ffffff, #dce5ed);
	   padding: 4.5px 9px;
	   -webkit-border-radius: 4px;
	   -moz-border-radius: 4px;
	   border-radius: 4px;
	   -webkit-box-shadow: rgba(0,0,0,1) 0 1px 0;
	   -moz-box-shadow: rgba(0,0,0,1) 0 1px 0;
	   box-shadow: rgba(0,0,0,1) 0 1px 0;
	   text-shadow: rgba(0,0,0,.4) 0 1px 0;
	   color: #000000;
	   font-size: 14px;
	   font-family: Helvetica, Arial, Sans-Serif;
	   text-decoration: none;
	   vertical-align: middle;
	   cursor:pointer;
	   }
	.button:hover {
	   border-top-color: #ffffff;
	   background: #ffffff;
	   color: #633f63;
	   }
	.button:active {
	   border-top-color: #b3b3b3;
	   background: #b3b3b3;
	   }
	.tillt {
		font-size: 36px;
		font-family: Verdana, Geneva, sans-serif;
		font-weight: bold;
		color: #000;
	}
	.u9ipup {
		text-align: center;
	}
	.tillt_2 {
		font-weight: bold;
		font-size: 24px;
		color: #000;
	}
	.tillt_1 {
		font-size: 24px;
		color: #000;
	}
	.btn-up2 {
	   border-top: 1px solid #96d1f8;
	   background: #79bfed;
	   background: -webkit-gradient(linear, left top, left bottom, from(#4c96c7), to(#79bfed));
	   background: -webkit-linear-gradient(top, #4c96c7, #79bfed);
	   background: -moz-linear-gradient(top, #4c96c7, #79bfed);
	   background: -ms-linear-gradient(top, #4c96c7, #79bfed);
	   background: -o-linear-gradient(top, #4c96c7, #79bfed);
	   padding: 6.5px 13px;
	   -webkit-border-radius: 8px;
	   -moz-border-radius: 8px;
	   border-radius: 8px;
	   -webkit-box-shadow: rgba(0,0,0,1) 0 1px 0;
	   -moz-box-shadow: rgba(0,0,0,1) 0 1px 0;
	   box-shadow: rgba(0,0,0,1) 0 1px 0;
	   text-shadow: rgba(0,0,0,.4) 0 1px 0;
	   color: white;
	   font-size: 16px;
	   font-family: Georgia, serif;
	   text-decoration: none;
	   vertical-align: middle;
	   }
	.btn-up2:hover {
	   border-top-color: #2b6f9c;
	   background: #2b6f9c;
	   color: #ccc;
	   }
	.btn-up2:active {
	   border-top-color: #1b435e;
	   background: #1b435e;
	   }
	   .btn-up1 {
	   border-top: 1px solid #969696;
	   background: #d9d9d9;
	   background: -webkit-gradient(linear, left top, left bottom, from(#9c9c9c), to(#d9d9d9));
	   background: -webkit-linear-gradient(top, #9c9c9c, #d9d9d9);
	   background: -moz-linear-gradient(top, #9c9c9c, #d9d9d9);
	   background: -ms-linear-gradient(top, #9c9c9c, #d9d9d9);
	   background: -o-linear-gradient(top, #9c9c9c, #d9d9d9);
	   padding: 6.5px 13px;
	   -webkit-border-radius: 8px;
	   -moz-border-radius: 8px;
	   border-radius: 8px;
	   -webkit-box-shadow: rgba(0,0,0,1) 0 1px 0;
	   -moz-box-shadow: rgba(0,0,0,1) 0 1px 0;
	   box-shadow: rgba(0,0,0,1) 0 1px 0;
	   text-shadow: rgba(0,0,0,.4) 0 1px 0;
	   color: white;
	   font-size: 16px;
	   font-family: Georgia, serif;
	   text-decoration: none;
	   vertical-align: middle;
	   }
	.btn-up1:hover {
	   border-top-color: #878787;
	   background: #878787;
	   color: #ccc;
	   }
	.btn-up1:active {
	   border-top-color: #808080;
	   background: #808080;
	   }
	
	.btn-on1{
		border-top: 1px solid #96d1f8;
	   background: #68bbf2;
	   background: -webkit-gradient(linear, left top, left bottom, from(#4a8cb8), to(#68bbf2));
	   background: -webkit-linear-gradient(top, #4a8cb8, #68bbf2);
	   background: -moz-linear-gradient(top, #4a8cb8, #68bbf2);
	   background: -ms-linear-gradient(top, #4a8cb8, #68bbf2);
	   background: -o-linear-gradient(top, #4a8cb8, #68bbf2);
	   padding: 7px 14px;
	   -webkit-border-radius: 11px;
	   -moz-border-radius: 11px;
	   border-radius: 11px;
	   -webkit-box-shadow: rgba(0,0,0,1) 0 1px 0;
	   -moz-box-shadow: rgba(0,0,0,1) 0 1px 0;
	   box-shadow: rgba(0,0,0,1) 0 1px 0;
	   text-shadow: rgba(0,0,0,.4) 0 1px 0;
	   color: white;
	   font-size: 18px;
	   font-family: Georgia, serif;
	   text-decoration: none;
	   vertical-align: middle;
	}
	.barrrr{
		-webkit-border-top-left-radius:15px;     /*左上角*/
-webkit-border-top-right-radius:15px;     /*右上角*/
-webkit-border-bottom-right-radius:10px;     /*右下角*/
-webkit-border-bottom-left-radius:10px;     /*左下角*/

-moz-border-top-left-radius:15px;     /*左上角*/
-moz-border-top-right-radius:15px;     /*右上角*/
-moz-border-bottom-right-radius:10px;     /*右下角*/
-moz-border-bottom-left-radius:10px;     /*左下角*/

border-top-left-radius:15px;     /*左上角*/
border-top-right-radius:15px;     /*右上角*/
border-bottom-left-radius:10px;     /*左下角*/
border-bottom-right-radius:10px;     /*右下角*/
background: #ffffff; /* Old browsers */
background: -moz-linear-gradient(top,  #ffffff 0%, #f1f1f1 14%, #e1e1e1 14%, #e1e1e1 14%, #f6f6f6 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(14%,#f1f1f1), color-stop(14%,#e1e1e1), color-stop(14%,#e1e1e1), color-stop(100%,#f6f6f6)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #ffffff 0%,#f1f1f1 14%,#e1e1e1 14%,#e1e1e1 14%,#f6f6f6 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #ffffff 0%,#f1f1f1 14%,#e1e1e1 14%,#e1e1e1 14%,#f6f6f6 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #ffffff 0%,#f1f1f1 14%,#e1e1e1 14%,#e1e1e1 14%,#f6f6f6 100%); /* IE10+ */
background: linear-gradient(to bottom,  #ffffff 0%,#f1f1f1 14%,#e1e1e1 14%,#e1e1e1 14%,#f6f6f6 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#f6f6f6',GradientType=0 ); /* IE6-9 */
}

	
	</style>
</Head>
<body style="overflow:hidden;">

	<!--==================================================
    html內容
    ====================================================== --><!--  左葉框 -->
<div style="position:absolute; top:11px; left:139px; width: 377px;">
    
        <h3 id="mssr_input_box_name" style="color:#660000;font-family:微軟正黑體;"><B>輸入發文內容與標題</B></h3>
        <a id="chs_4" class="btn-up2" onClick="sel_list_open(1)" style="float:left; cursor:pointer;">
        	點我選擇發文樣式
        </a>
        <BR><BR><BR>
        <strong>標題：</strong><input  id="haha" type="text"  name="mssr_input_box_name_title" size="40" style="width:260PX;" maxlength="40"/>
        <BR>
        <BR>
    	<span id="mssr_input_box_content"><strong>內容：</strong></span>
        <BR>
        
        <div id="mssr_input_box_content_text1">
        	<font color=red></font>
        </div>
        <div id="mssr_input_box_content_text2" style="display:none;">
        	<div id="ct_1">ffff</div>
        	<textarea id = "bar_1" style="width:320px; height:40px; resize:none;"></textarea>
            <div id="ct_2">fff</div>
            <textarea id = "bar_2" style="width:320px; height:40px; resize:none;"></textarea>
        	<div id="ct_3">fff</div>
            <textarea id = "bar_3" style="width:320px; height:40px; resize:none;"></textarea>
			<div id="ct_4">fff</div>
            <textarea id = "bar_4" style="width:320px; height:40px; resize:none;"></textarea>
	
        </div>
                
    </div>
    <!--  右葉框 -->
    <div style="position:absolute; top:25px; left:533px; width:300px;" >
      <h3 style="position:absolute; top:-15px; left:19px; width: 275px; color:#660000; font-family:微軟正黑體;"class="little"><B>你的發文列表</B></h3>
        <div id="tittle_list" style="position:absolute; top:36px; left:16px; width: 300px; height:121px; overflow:auto;">
        	
        </div>
        <div style="position:absolute; top:186px; left:14px; width: 275px;" class="little">內容:</div>
        <textarea id="con" style="position:absolute; top:209px; left:21px; width: 281px; height: 146px; resize:none;" readonly></textarea>
         <img src="../../../forum/image/like.png" style="position:absolute; top:356px; left:24px; width: 34px;">
      <div id="like" style="position:absolute; top:363px; left:74px; width: 34px;"></div>
      <img src="../../../forum/image/icon.png" style="position:absolute; top:362px; left:165px; width: 26px;">
      <div id="re" style="position:absolute; top:363px; left:204px; width: 34px;"></div>
         <div id="go_btn" class="buttoni" style="position:absolute; top:294px; left:59px; display:none;" onClick="out()">前往聊書觀看全部內容</div>
</div>
    <!--  中葉框 -->
        <img src="../../img/UI_savef.png" style="position:absolute; top:286px; left:879px; cursor: no-drop; opacity:0.4" border="0"> 

            <a id="save_btn" class="btn_save" onClick="go_save()" style="position:absolute; top:286px; left:879px; cursor:pointer; "></a>

<div id="article_refer"  style="display: none;">
            	<div style="background-color:#000; opacity:0.7; width:1000px; height:480px;"></div>
   	<div class="barrrr" style="background-color:#FFF; width:660px; height:298px; position:absolute; top:115px; left:190px; box-shadow: 3px 3px 3px #AAA;"></div>
    <div id="tittle" style="font-size:26px; position:absolute; top:125px; left:198px; width: 636px;">--選擇你想要的類型--</div>
    <div  style=" position:absolute; top:171px; left:225px; width: 612px; font-size:22px;">
        <input id="ac1s" class="mssr_input_box_radio" type="radio" name="a" value="a" onclick="article_input_text(1)"  style=" "/>
            <div id="ac1" class="mssr_input_box_radio_text" style="width:580px; height:25px; position:relative; left:20px; top:-20px;">aaaaaaaaaaaaaaa</div>
        <input id="ac2s" class="mssr_input_box_radio" type="radio" name="a" value="b" onclick="article_input_text(2)"  style=" "/>
            <div id="ac2" class="mssr_input_box_radio_text" style="width:580px; height:25px; position:relative; left:20px; top:-20px;">bbbbbbbbbbbb</div>
        <input id="ac3s" class="mssr_input_box_radio" type="radio" name="a" value="c" onclick="article_input_text(3)" style=" "/>
            <div id="ac3" class="mssr_input_box_radio_text" style="width:580px; height:25px; position:relative; left:20px; top:-20px;">cccccccccccc</div>
        <input id="ac4s" class="mssr_input_box_radio" type="radio" name="a" value="d" onclick="article_input_text(4)" style=" "/>
            <div id="ac4" class="mssr_input_box_radio_text" style="width:580px; height:25px; position:relative; left:20px; top:-20px;">ddddddddddddd</div>
   	  <a id="chs_0" class="btn-up1" onClick="sel_list_open(0)" style="float:left; cursor:pointer;">
        	取消
        </a>&nbsp;
        <a id="chs_1" class="btn-up2" onClick="load_qa(-1)" style="float:left; cursor:pointer;">
        	上一步
        </a> &nbsp;
        <a id="chs_2" class="btn-up2" onClick="load_qa(1)" style="float:left; cursor:pointer;">
        	下一步
        </a>
        <a id="chs_3" class="btn-up2" onClick="sel_list_open(2)" style="float:left; cursor:pointer;">
        	完成
        </a>
  </div>
</div>

<span style="position:absolute; top:25px; left:533px; width:300px;"><img src="img/paper.png" style="position:absolute; top:19px; left:513px; width: 350px;"></span>
<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
     var book_sid = '<? echo $book_sid;?>';
	 var book_name = '<? echo $book_name;?>';
	 var uid = '<? echo $uid;?>';

	 var qu_tittle =
	 	{	1:"看完這本書後，我想要分享還是有什麼問題想提問？",
			2:"請選擇想發文的類別",
			3:"請選擇想發文的例句"
		};
	 var tezt_b_list=
	 	{
			"11":"你覺得這個故事在說什麼？",
			"12":"你有跟故事有類似的經驗嗎？",
			"13":"你看完這個故事的感覺是什麼？",
			"14":"你對這個故事內容有什麼看法？",
			"21":"看完這本書後，對情節中有什麼不了解？",
			"22":"看完這本書後，還想知道什麼？",
			"31":"你覺得這本書內容的重點是什麼？",
			"32":"你有書中相關的知識想要分享嗎？",
			"33":"關於書中的內容你有什麼想要評論？",
			"41":"看完這本書後，對內容中有什麼不了解？",
			"42":"看完這本書後，還想知道什麼？"
		}
	 var text_list = 
	 	{
			"0":{
					"ct" : "1",
					"bar" : "1",
					"1":"",
					"2":"",
					"3":"",
					"4":""
				},
			"11a":{
					"ct" : "1",
					"bar" : "1",
					"1":"這本書的情節在說",
					"2":"",
					"3":"",
					"4":""
				},
			"11b":{
					"ct" : "4",
					"bar" : "4",
					"1":"這是",
					"2":"的故事，主角是",
					"3":"，一開始",
					"4":"，後來"
					
				},
			"11c":{
					"ct" : "1",
					"bar" : "1",
					"1":"這本書的情節背景是",
					"2":"",
					"3":"",
					"4":""
				},
			"12a":{
					"ct" : "2",
					"bar" : "2",
					"1":"這本書提到",
					"2":"，讓我想到",
					"3":"",
					"4":""
				},
			"12b":{
					"ct" : "2",
					"bar" : "2",
					"1":"這本書故事講的",
					"2":"角色，讓我想到",
					"3":"",
					"4":""
				},
			"12c":{
					"ct" : "2",
					"bar" : "1",
					"1":"我以前發生",
					"2":"的情況，跟書裡的情節很類似",
					"3":"",
					"4":""
				},
			"13a":{
					"ct" : "2",
					"bar" : "2",
					"1":"我現在心情很",
					"2":"，因為(情節、角色)",
					"3":"",
					"4":""
				},
			"13b":{
					"ct" : "2",
					"bar" : "2",
					"1":"看完這本書的結局，我感覺很",
					"2":"，因為",
					"3":"",
					"4":""
				},
			"13c":{
					"ct" : "2",
					"bar" : "2",
					"1":"我對這本書",
					"2":"的部分(情節、角色)很感動，因為",
					"3":"",
					"4":""
				},
			"14a":{
					"ct" : "3",
					"bar" : "2",
					"1":"這本書的情節內容，在",
					"2":"寫得很(好/不好)，因為",
					"3":"",
					"4":""
				},
			"14b":{
					"ct" : "1",
					"bar" : "1",
					"1":"我覺得這本書的情節沒有特色，因為",
					"2":"",
					"3":"",
					"4":""
				},
			"14c":{
					"ct" : "3",
					"bar" : "2",
					"1":"我很喜歡(或不喜歡)",
					"2":"角色，因為",
					"3":"",
					"4":""
				},
			"21a":{
					"ct" : "2",
					"bar" : "1",
					"1":"我對書中情節提到",
					"2":"的地方不太明白，有人知道嗎？",
					"3":"",
					"4":""
				},
			"21b":{
					"ct" : "3",
					"bar" : "2",
					"1":"為什麼",
					"2":"(角色)要",
					"3":"這樣，有人知道嗎？",
					"4":""
				},
			"21c":{
					"ct" : "2",
					"bar" : "1",
					"1":"為什麼結局最後會",
					"2":"，有人知道嗎？",
					"3":"",
					"4":""
				},
			"22a":{
					"ct" : "2",
					"bar" : "1",
					"1":"這本書情節提到",
					"2":"，有什麼相關的書籍嗎？",
					"3":"",
					"4":""
				},
			"22b":{
					"ct" : "3",
					"bar" : "2",
					"1":"我最喜歡書中",
					"2":"的角色，因為",
					"3":"，大家最喜歡哪個角色呢？",
					"4":""
				},
			"22c":{
					"ct" : "3",
					"bar" : "2",
					"1":"我最喜歡書中",
					"2":"的情節內容，因為",
					"3":"，大家最喜歡哪個部分呢？",
					"4":""
				},
			"31a":{
					"ct" : "2",
					"bar" : "2",
					"1":"這本書在說跟",
					"2":"有關的知識，例如",
					"3":"",
					"4":""
				},
			"31b":{
					"ct" : "2",
					"bar" : "2",
					"1":"我覺得這本書的重點概念是",
					"2":"，因為",
					"3":"",
					"4":""
					
				},
			"31c":{
					"ct" : "2",
					"bar" : "2",
					"1":"這本書提到",
					"2":"的知識，重點是",
					"3":"",
					"4":""
					
				},
			"32a":{
					"ct" : "2",
					"bar" : "2",
					"1":"這本書講到",
					"2":"的內容，讓我想到",
					"3":"",
					"4":""
				},
			"32b":{
					"ct" : "3",
					"bar" : "2",
					"1":"我看過其他",
					"2":"的書，也有類似",
					"3":"的內容",
					"4":""
				},
			"32c":{
					"ct" : "3",
					"bar" : "2",
					"1":"我以前也有",
					"2":"的情況，跟書中",
					"3":"的內容一樣",
					"4":""
				},
			"33a":{
					"ct" : "2",
					"bar" : "2",
					"1":"書中提到",
					"2":"的內容，跟我想法不一樣，因為",
					"3":"",
					"4":""
				},
			"33b":{
					"ct" : "2",
					"bar" : "2",
					"1":"我覺得這本書在",
					"2":"的內容，寫的(好/不好)，因為",
					"3":"",
					"4":""
				},
			"33c":{
					"ct" : "2",
					"bar" : "2",
					"1":"這本書在",
					"2":"的內容，寫的不清楚，應該補充",
					"3":"",
					"4":""
				},
			"41a":{
					"ct" : "3",
					"bar" : "2",
					"1":"我對書中提到",
					"2":"的知識不太明白，因為",
					"3":"，有人知道嗎？",
					"4":""
				},
			"41b":{
					"ct" : "2",
					"bar" : "1",
					"1":"為什麼書中提到",
					"2":"會這樣，有人知道嗎？",
					"3":"",
					"4":""
				},
			"41c":{
					"ct" : "3",
					"bar" : "2",
					"1":"關於書本所提到",
					"2":"的知識，我覺得是",
					"3":"，有沒有人想法跟我一樣啊？",
					"4":""
				},
			"42a":{
					"ct" : "2",
					"bar" : "1",
					"1":"我想對",
					"2":"的相關知識多了解一點，有什麼推薦的書籍嗎？",
					"3":"",
					"4":""
				},
			"42b":{
					"ct" : "2",
					"bar" : "1",
					"1":"看完這本書後，我學到了",
					"2":"的知識，大家有學到什麼嗎？",
					"3":"",
					"4":""
				},
			"42c":{
					"ct" : "2",
					"bar" : "1",
					"1":"書中",
					"2":"的概念，在你的生活中有經歷過嗎？為什麼？",
					"3":"",
					"4":""
				},
			
		}
	 var real_ation = "";
	 var ation = "";
	 var bar_count = 0;
	 var scaffolding = 11;
	 var real_scaffolding = 0;
	 var chs = 1 ;
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	
	
	function article_input_text(value)
	{
		//紀錄填寫資訊
		if(ques == 1)
		{
			if(value == 1 )
			{
				ans_1 = "3";
		
			}
			if(value == 2 )
			{
				ans_1 = "4";
			}
			
		}else if(ques == 2)
		{
			ans_2 = value;
		}
		
		else if(ques == 3)
		{
			var xtmp = "";
			if(value == 1 )ans_3 = "a";
			if(value == 2 )ans_3 = "b";
			if(value == 3 )ans_3 = "c";
			if(value == 4 )ans_3 = "0";
		}
		
		
		if(ques!=3)
		window.document.getElementById("chs_2").style.visibility = "";
		else window.document.getElementById("chs_3").style.visibility = "";
	}
	function load_qa(value)
	{
		
		
		//改變頁數
		if(value == 1)
		{
			ques++;
		}else if(value == -1)
		{
			ques--;
		}else if(value == 0)
		{
			ques = 1;
		}
		
		var show_count = 0;
		//處理顯示內容
		if(ques == 1)
		{
			show_count = 2;
			window.document.getElementById("chs_1").style.visibility = "hidden";
			window.document.getElementById("chs_2").style.visibility = "hidden";
			window.document.getElementById("chs_3").style.visibility = "hidden";	
			window.document.getElementById("ac1").innerHTML = "我想要分享";	
			window.document.getElementById("ac2").innerHTML = "我想要問";	
		}else if(ques == 2)
		{
			
			
			
			window.document.getElementById("chs_1").style.visibility = "";
			window.document.getElementById("chs_2").style.visibility = "hidden";
			window.document.getElementById("chs_3").style.visibility = "hidden";
			for(var i = 1 ; i <= 4; i++)
			{
				if(tezt_b_list[ans_1+""+i])
				{
					window.document.getElementById("ac"+i).innerHTML = tezt_b_list[ans_1+""+i];	
					show_count = i;
				}else
				{
					
				}
			}
		}
		
		else if(ques == 3)
		{
			window.document.getElementById("chs_1").style.visibility = "";
			window.document.getElementById("chs_2").style.visibility = "hidden";
			window.document.getElementById("chs_3").style.visibility = "hidden";
			show_count = 4;
			//設定內容
			for(var j = 1 ; j <= 3 ; j ++)
			{
				if(j==1)no_ = "a";
				if(j==2)no_ = "b";
				if(j==3)no_ = "c";
				obj = window.document.getElementById("ac"+j);
				obj.innerHTML = "";
				for( var i = 1 ; i <= 4 ; i ++)
				{
					if( i <= text_list[ans_1+ans_2+no_]["ct"])
					{
						obj.innerHTML = obj.innerHTML+text_list[ans_1+ans_2+no_][i];
					}
					
					if( i <= text_list[ans_1+ans_2+no_]["bar"])
					{
						obj.innerHTML = obj.innerHTML+"……";
					}
					
				}
				window.document.getElementById("ac4").innerHTML = "其他(自行輸入內容)";
			}
		}
		//清除按鈕
		for(var i=1 ; i<=4 ; i++)
		{
			window.document.getElementById("ac"+i+"s").checked = false;
			if(i<=show_count)
			{	
				window.document.getElementById("ac"+i+"s").style.visibility = "";
				window.document.getElementById("ac"+i).style.visibility = "";
			}else
			{
				window.document.getElementById("ac"+i+"s").style.visibility = "hidden";
				window.document.getElementById("ac"+i).style.visibility = "hidden";
			}
		}
		//設定問題
		window.document.getElementById("tittle").innerHTML = qu_tittle[ques];
		
	}
	function sel_list_open(value)
	{
		if(value == 1)
		{
			window.parent.document.getElementById("top_btn").style.display = "none";
			//window.parent.document.getElementById("helper").style.display = "block";
			
			window.document.getElementById("article_refer").style.display = "block";
			load_qa(0);
		}
		else if(value == 0)	
		{
			window.parent.document.getElementById("top_btn").style.display = "block";
			window.document.getElementById("article_refer").style.display = "none";
			load_qa(0);
		}
		else if(value == 2)	
		{
			real_ation = ""+ans_1+ans_2+ans_3;
			real_scaffolding = ""+ans_1+ans_2;
			
			if(ans_3 != 0)
			{
				bar_count = text_list[real_ation]["bar"];	
			}
			else
			{
				real_ation = 0;
				bar_count = 1;	
			}
		
			if(real_ation==0)
			{
				window.document.getElementById("bar_1").style.height="250px";
				for(var i = 1; i <= 4;  i++)
				{
					
					window.document.getElementById("ct_"+i).style.display = "none";
					window.document.getElementById("ct_"+i).innerHTML = "";
				}
				
				for(var i = 1; i <= 4;  i++)
				{
					if(  i <= 1)
					{
						window.document.getElementById("bar_"+i).style.display = "block";
						window.document.getElementById("bar_"+i).value = "";
					}
					else
					{
						window.document.getElementById("bar_"+i).style.display = "none";
						window.document.getElementById("bar_"+i).value = "";
					}
				}
			}else
			{	
				window.document.getElementById("bar_1").style.height="40px";
				for(var i = 1; i <= 4;  i++)
				{
					if(  i <= text_list[real_ation]["ct"])
					{
						window.document.getElementById("ct_"+i).style.display = "block";
						window.document.getElementById("ct_"+i).innerHTML = text_list[real_ation][i];
					}
					else
					{
						window.document.getElementById("ct_"+i).style.display = "none";
						window.document.getElementById("ct_"+i).innerHTML = "";
					}
				}
				
				for(var i = 1; i <= 4;  i++)
				{
					if(  i <= text_list[real_ation]["bar"])
					{
						window.document.getElementById("bar_"+i).style.display = "block";
						window.document.getElementById("bar_"+i).value = "";
					}
					else
					{
						window.document.getElementById("bar_"+i).style.display = "none";
						window.document.getElementById("bar_"+i).value = "";
					}
				}
				
			}
			window.document.getElementById("mssr_input_box_content_text1").style.display = "none";
			window.document.getElementById("mssr_input_box_content_text2").style.display = "block";
			window.document.getElementById("article_refer").style.display = "none";
			window.parent.document.getElementById("top_btn").style.display = "block";
		}
		
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
	function ch_bar(value)
	{
		window.document.getElementById("like").innerHTML = forum_data["data"][value]["article_like_cno"];
		window.document.getElementById("re").innerHTML = forum_data["data"][value]["re_count"];
		window.document.getElementById("con").innerHTML = forum_data["data"][value]["article_content"];
		article_id = forum_data["data"][value]["article_id"];
		window.document.getElementById("go_btn").style.display = "block";
		for(var i = 0 ; i < forum_data["count"];i++)
		{
			window.document.getElementById("b"+i).className = "button";
		}
		window.document.getElementById("b"+value).className = "button_on";
	}
	//=========建立LIST=======
	function run_list()
	{
		var left_page = window.document.getElementById("tittle_list");
		left_page.innerHTML = "尚未發文，無發文列表";
		for(var i = 0 ; i < forum_data["count"];i++)
		{
			left_page.innerHTML = left_page.innerHTML+"<div id='b"+i+"' class = 'button' onClick='ch_bar("+i+")' style='overflow:hidden; white-space:nowrap; width:260px;'>"+forum_data["data"][i]["article_title"]+"</div><BR>";
		}
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
		echo("Main:初始開始:讀取聊書資料>"+book_sid+"_"+uid);
		cover("讀取聊書資料")
		var url = "./ajax/get_rec_forum.php";
		$.post(url, {
					user_id:uid,
					book_sid:book_sid,
					user_permission:'<? echo $permission;?>'				
			}).success(function (data) 
			{
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員",2,function(){main();});
					echo("AJAX:success:main():讀取聊書資料:資料庫發生問題");
					return false;
				}
				
				data_array = JSON.parse(data);
				echo("AJAX:success:main():讀取聊書資料:已讀出:"+data);
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
					forum_data = data_array;
					run_list();
					cover("");
				}
				
			}).error(function(e){
				echo("AJAX:error:main():讀取聊書資料:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){main();});
			}).complete(function(e){
				echo("AJAX:complete:main():讀取聊書資料:");
			});
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}
	
	function go_save()
	{
		echo("go_save:文字存檔判定");
		
		set_rec_forum();
	}
	
	//存檔
	function set_rec_forum()
	{
		if(real_ation === "" || bar_count == 0)
		{
			cover("請先選擇欲輸入的內容"+bar_count,1);
			return false;
		}
		echo("set_rec_forum:初始開始:儲存文字資料>"+book_sid+"_"+uid);
		cover("儲存聊書資料中")
		var url = "./ajax/set_rec_forum.php";
		$.post(url, {
					user_id:uid,
					book_sid:book_sid,
					rec_tittle:document.getElementById('haha').value,
					rec_text_1:document.getElementById('bar_1').value,
					rec_text_2:document.getElementById('bar_2').value,
					rec_text_3:document.getElementById('bar_3').value,
					rec_text_4:document.getElementById('bar_4').value,
					ct:text_list[real_ation]["ct"],
					ct_1:text_list[real_ation]["1"],
					ct_2:text_list[real_ation]["2"],
					ct_3:text_list[real_ation]["3"],
					ct_4:text_list[real_ation]["4"],
					scaffolding:real_scaffolding,
					ation:real_ation,
	    			bar_count:bar_count,
					user_permission:'<? echo $permission;?>'
					
			}).success(function (data) 
			{
				
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員",1);
					echo("AJAX:success:set_rec_forum():儲存聊書資料中:資料庫發生問題");
					return false;
				}
				
				data_array = JSON.parse(data);
				echo("AJAX:success:set_rec_forum():儲存文字資料:已讀出:"+data);
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
					
					//
					var process_url     ='../../../../service/forum/inc/add_action_forum_log/code.php';
					var action_code     ='bs0';
					var action_from     =uid;
			
					var user_id_1       =0;
					var user_id_2       =0;
					var book_sid_1      =book_sid;
					var book_sid_2      ='';
					var forum_id_1      =0;
					var forum_id_2      =0;
			
					var article_id      =0;
					var reply_id        =0;
					var go_url          ='';

					add_action_forum_log(
						process_url,
						action_code,
						action_from,
						user_id_1,
						user_id_2,
						book_sid_1,
						book_sid_2,
						forum_id_1,
						forum_id_2,
						article_id,
						reply_id,
						go_url
					);
					
					window.document.getElementById("article_refer").style.display = "none";
					window.document.getElementById("mssr_input_box_content_text1").style.display = "block";
					window.document.getElementById("mssr_input_box_content_text2").style.display = "none";
					cover("成功完成發文",1);
					main();
				}
				
			}).error(function(e){
				echo("AJAX:error:set_rec_forum():儲存聊書資:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){set_rec_star();});
			}).complete(function(e){
				echo("AJAX:complete:set_rec_forum():儲存聊書資:");
			});
	}
	
	//=========MAIN=============
	
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

       main();

    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    