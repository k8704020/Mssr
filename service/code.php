<?php
//---------------------------------------------------
//設定與引用
//---------------------------------------------------

	//SESSION
	@session_start();

	//啟用BUFFER
	@ob_start();

	//外掛設定檔
	require_once(str_repeat("../",1)."/config/config.php");

	 //外掛函式檔
	$funcs=array(
				APP_ROOT.'inc/code',
				APP_ROOT.'lib/php/db/code',
				APP_ROOT.'lib/php/array/code'
				);
	func_load($funcs,true);


	//清除並停用BUFFER
	@ob_end_clean();

	// print_r($_SESSION);



//---------------------------------------------------
//END   設定與引用
//---------------------------------------------------

	$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
<meta name="description" content="Your description goes here" />
<meta name="keywords" content="明日星球,中央大學明日星球" />
<link rel="stylesheet" type="text/css" href="css/code.css">
<script src="screenfull/screenfull.js"></script>
<script type="text/javascript" src="../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
<script src="bookstore_v2/js/select_thing.js" type="text/javascript"></script>




<title>明日星球：明日書店</title>



</script>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-116055812-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-116055812-1');
</script>
<style>
	#fullScreen {
		background-color: #4CAF50;
		color: white;
		margin-left: 25px;
		padding: 0px 10px;
		border-radius: 8px;
	}

	#cancelFullScreen {
		background-color: #f44336;
		color: white;
		margin-left: 25px;
		padding: 0px 10px;
		border-radius: 8px;
		display: none;
	}
</style>
</head>

<body>
<?PHP
//SESSION
	@session_start();

?>
<script>
//初始化
/*a:6:{s:12:"open_publish";i:1;s:30:"read_the_registration_code_pwd";s:3:"t01";s:22:"read_opinion_limit_day";i:14;s:12:"rec_en_input";s:3:"yes";s:13:"rec_draw_open";s:3:"yes";s:9:"coin_open";s:3:"yes";}
open_publish  上架的設定
read_the_registration_code_pwd  教師開放借還書的密碼
read_opinion_limit_day 設定到期時間
rec_en_input 禁止英文輸入?
rec_draw_open  是否開放繪圖功能
coin_open  推薦是否獲得金錢
*/
var book_story_auth= new Array();
var help_cover = new Array();
help_cover["courtyard_main_help"] = true;
help_cover["bookstore_main_help"] = true;
help_cover["bookstore_shelf_help"] = true;

function out()
{
	if(confirm("是否要回到首頁"))
	{
			document.location.href='/ac/index.php';
	}
	else
	{

	}

}

</script>
    <div id="table" >
    	<div  class="top " >
        		<div class="title"  >

           		明日書店
           		</div>
				<button id="fullScreen">
					全螢幕模式
				</button>
				<button id="cancelFullScreen">
					關閉全螢幕
				</button>
				<span id="fullScreenMessage">你的螢幕有一點小耶，點選左邊的全螢幕模式才能看到完整的畫面喔！</span>
	
            	<div class="retrunIndexBtn"  >
                	<a><? echo $_SESSION["name"]?></a> | <a onclick="out()" style='color:#000099' target='_self' style="cursor:pointer;">回明日星球首頁</a>
            	</div>
           
        </div>
        <div class="clearFix">
        	
        </div>
        <div  class="logo" >
        	<a onclick="out()" title="按此回首頁" name="按此回首頁" target="_self" >
        			<img src="img/banner new(0120)_0217.png">
        	</a>
        </div>
        <div class="content">
        	
		  <!-- <a id="engBookstore" onclick="stateChange()">English</a> -->
          <a name="content"></a>
          <? if(@$_SESSION["uid"]!="")
		  { ?>
			  <? if(@$_GET["mode"]=="bookstore"){
			  //===========================書店========================================
				  $sql = "SELECT count(1) AS count
						  FROM  `mssr_user_info`
						  WHERE user_id = '".$_SESSION["uid"]."'";
				  $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
				  if($retrun[0]["count"]==0)
				  {
					   $sql = "INSERT INTO `mssr`.`mssr_user_info`
					  					   (`user_id`,
											`user_nickname`,
											`user_content`,
											`map_item`,
											`box_item`,
											`user_coin`,
											`star_style`,
											`star_declaration`,
											`pet_declaration`,
											`keyin_cdate`,
											`keyin_mdate`,
											`keyin_ip`
										)VALUES(
											'".$_SESSION["uid"]."',
											'".$_SESSION["name"]."',
											'',
											'55,325,220,',
											'',
											'1000',
											'green',
											'',
											'',
											now(),
											now(),
											'".$_SERVER["REMOTE_ADDR"]."');";

					  $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
				  }
				  if($_SESSION['class'][0][1] == 'gcp_2013_2_2_209' || $_SESSION['class'][0][1] == 'gcp_2013_2_3_7' || $_SESSION['class'][0][1] == 'gcp_2013_2_3_6'){?>
				  <iframe id="rec_record_iframe" src="./branch/index_php.php?zoom=1"  > </iframe>
				  <? }else{
					  ?>
                   <iframe id="rec_record_iframe" src="./bookstore_v2/bookstore_courtyard/index.php?v=0112" > </iframe><?
					  }
			  //===========================END書店========================================
			  }else if(@$_GET["mode"]=="bookstore_home"){
			  //===========================書店========================================
				  $sql = "SELECT count(1) AS count
						  FROM  `mssr_user_info`
						  WHERE user_id = '".$_SESSION["uid"]."'";
				  $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
				  if($retrun[0]["count"]==0)
				  {
					   $sql = "INSERT INTO `mssr`.`mssr_user_info`
					  					   (`user_id`,
											`user_nickname`,
											`user_content`,
											`map_item`,
											`box_item`,
											`user_coin`,
											`star_style`,
											`star_declaration`,
											`pet_declaration`,
											`keyin_cdate`,
											`keyin_mdate`,
											`keyin_ip`
										)VALUES(
											'".$_SESSION["uid"]."',
											'".$_SESSION["name"]."',
											'',
											'55,325,220,',
											'',
											'1000',
											'green',
											'',
											'',
											now(),
											now(),
											'".$_SERVER["REMOTE_ADDR"]."');";

					  $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
				  }
				  ?>
                   <iframe id="rec_record_iframe" src="./bookstore_v2/index.php" > </iframe><?

			  //===========================END書店========================================


			  }else if(@$_GET["mode"]=="read_the_registration"){
			  //===========================閱讀登記========================================
				  ?>
				  <iframe id="rec_record_iframe" src="./read_the_registration_v2/index.php" > </iframe>
				  <?

			  //===========================END閱讀登記========================================

			  }else if(@$_GET["mode"]=="page_opinion_menu"){
			  	//===========================idc回答問題========================================

			  	   ?>
				  <iframe id="rec_record_iframe" src="./bookstore_v2/page_opinion_menu_idc/index.php" > </iframe>
				  <?
 				//=========================== idc回答問題END========================================
			  }else if(@$_GET["mode"]=="pptv_index")
              {
			  //===========================必寶德========================================

                  $tmp = explode("_",$_SESSION["permission"]);
                  if($tmp[0] == "super")
                  {
                       ?>
                       <iframe id="rec_record_iframe" src="./pptv/index.php" > </iframe>
                       <?
                  }else if($tmp[1]=="t")
                  {
                       ?>
                       <iframe id="rec_record_iframe" src="./pptv/index.php" > </iframe>
                       <?
                  }
                  else
                  {
                       ?>
                       <iframe id="rec_record_iframe" src="./pptv/index.php"> </iframe>
                       <?
                  }
			   //===========================END必寶德========================================
              }
		  }?>


      
        </div>
        <div class="footerTxt" >

        	
            	<P>
            		校址：(32001)桃園縣中壢市五權里2鄰中大路300號‧總機電話：03-4227151<br/>
                	國立中央大學 版權所有 &copy; 2008-2011 National Central University All Rights Reserved.
				</P>
				<img src="img/copyright.png";alt="">
        	
        </div>
    </div>

</body>
<script type="text/javascript">






// localStorage.setItem("state", traditionalChinese);
// var state = localStorage.getItem("state");

// // console.log("1",state);
// window.document.getElementById("engBookstore").innerHTML = state=="traditionalChinese"? "English":"中文";
// window.document.getElementById("rec_record_iframe").src=state=="traditionalChinese"? "./bookstore_v2/bookstore_courtyard/index.php?v=0112":"./bookstore_v2/bookstore_courtyard/indexEng.php?v=0112";
// //點擊按鈕切換語言並更改localstorage
// function stateChange(){

//   var state = localStorage.getItem("state");
//   var language = state == "traditionalChinese" ? "english" : "traditionalChinese";
//   // console.log("2",state);
//   localStorage.setItem("state", language);
//   window.document.getElementById("engBookstore").innerHTML = state=="traditionalChinese"? "English":"中文";
//   window.document.getElementById("rec_record_iframe").src=state=="traditionalChinese"? "./bookstore_v2/bookstore_courtyard/index.php?v=0112":"./bookstore_v2/bookstore_courtyard/indexEng.php?v=0112";
// }


//偵測行動裝置
if (/Android|webOS|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
	document.write("<style> html, body { margin: 0; height: 100%; overflow: hidden; } </style>");
	if (screen.height <= 750) {
		document.write("<style> .logo img { display: none; } </style>");
	} else {
		document.write("<style> #fullScreenMessage { display: none; } </style>");
	}
} else if (/Windows NT/i.test(navigator.userAgent) && window.innerHeight <= 600) {
	document.write("<style> html, body { margin: 0; height: 100%; overflow: hidden; } </style>");
	if (window.innerHeight <= 520) {
		document.write("<style> .logo img { display: none; } </style>");
	} else {
		document.write("<style> #fullScreenMessage { display: none; } </style>");
	}
} else {
	document.write("<style> #fullScreen { display: none; } #fullScreenMessage { display: none; } </style>");
}

//iOS disable bounce scroll
window.addEventListener('touchstart', function(evt) {
	startY = evt.touches ? evt.touches[0].screenY : evt.screenY
}, { passive: false });
window.addEventListener('touchmove', function(evt) {
	var el = evt.target;
	while (el !== document.body) {
		var style = window.getComputedStyle(el);
		var scrolling = style.getPropertyValue("-webkit-overflow-scrolling");
		var overflow = style.getPropertyValue("overflow");
		var height = parseInt(style.getPropertyValue("height"), 10);
		var isScrollable = scrolling === "touch" && overflow === "auto";
		var canScroll = el.scrollHeight > el.offsetHeight;
		if (isScrollable && canScroll) {
			var curY = evt.touches ? evt.touches[0].screenY : evt.screenY;
			var isAtTop = startY <= curY && el.scrollTop === 0;
			var isAtBottom = startY >= curY && el.scrollHeight - el.scrollTop === height;
			if (isAtTop || isAtBottom) {
				evt.preventDefault()
			} return
		} el = el.parentNode
	} evt.preventDefault()
}, { passive: false });

$('#fullScreen').click(function() {
	if (screenfull.enabled) {
		screenfull.request();
	}
	$("#fullScreen").css("display","none");
	$("#cancelFullScreen").css("display","inline");

	if (fullScreenMessage) {
		$("#fullScreenMessage").css("display","none");
	}
});

$('#cancelFullScreen').click(function() {
	if (screenfull.enabled) {
		screenfull.exit();
	}
	$("#fullScreen").css("display","inline");
	$("#cancelFullScreen").css("display","none");

	if (fullScreenMessage) {
		$("#fullScreenMessage").css("display","inline");
	}
});

//-------------------------------------------------------
//範例
//-------------------------------------------------------
//console.log($(document).width());
//console.log($(window).width());
//console.log($("#table").width());
</script>
</html>