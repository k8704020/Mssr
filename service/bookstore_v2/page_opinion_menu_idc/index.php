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

        $user_id       =(isset($_GET['uid']))?(int)$_GET['uid']:$_SESSION['uid'];
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
		$auth_read_opinion_limit_day = (int)$_GET['auth_read_opinion_limit_day'];

  //       if(isset($_SESSION['uid'])){

  //       	$user_id=$_SESSION['uid'];

  //       }else if(isset($_SESSION['t']['uid'])){

  //       	$user_id=$_SESSION['t']['uid'];

  //       }else{
  //       	$user_id=0;
  //       }
   
  //       // $user_id        =(isset($_SESSION['uid']),$_SESSION['t']['uid'])?(int)$_SESSION['uid']:'0';
		// //$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';

		// if(isset($_SESSION['permission'])){

		// 	$permission=$_SESSION['permission'];

		// }else if(isset($_SESSION['t']['permission'])){

		// 	$permission=$_SESSION['t']['permission'];

		// }else{

		// 	$permission=0;

		// }

		// // echo $user_id  ;

		// echo $permission;


// print_r($_SESSION['uid']);


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
    <link rel="stylesheet" href="../css/btn.css">
	<style>

		#popopo{
			position:absolute;
			width:223px;
			height:284px;
			background: url('./img/pagge_2.png') 0px 0;
		}
		#popopo:hover {
    		background: url('./img/pagge_2.png') 0px -284px;
		}
		
	</style>
</Head>
<body>

   <!--==================================================
    遮罩內容
    ====================================================== -->
	<div id="cover" style="position:absolute; top:-8px; left:-8px; display:none;">
    	<div onClick="" style="position:relative; top:0px; left:0px; height:500px; width:900px; cursor:wait; background-color:#000; opacity:0.7; z-index:9999"></div>
        <table width="550"   border="0" cellspacing="0"  style="position:absolute; top:50%; left:50%; transform: translateX(-50%)translateY(-50%);   text-align: center; z-index:10000;">
        	<tr height="90">
            	<td width="500"  valign="center" id="cover_text" style="text-align: center;" class="cover_box" >正在讀取中請稍後...

                </td>
            </tr>
            <tr height="40">
            	<td>
                        <div id="cover_btn_0" onClick="close_cover(2)" style="position:absolute; left:1px; width:110px; height:38px; text-align: center; z-index:10003; display:none; cursor:pointer;" class="ok_box">存檔</div>
                        <div id="cover_btn_1" onClick="close_cover(1)" style="position:absolute; left:141px; width:110px; height:38px; text-align: center; z-index:10001; display:none; cursor:pointer;" class="ok_box">確定</div>
                        <div id="cover_btn_2" onClick="close_cover(0)" style="position:absolute; left:286px; width:110px; height:38px; text-align: center; z-index:10002; display:none; cursor:pointer;" class="no_box">取消</div>
                        

                 </td>
            </tr>
        </table>

	</div>


	<!--==================================================
    html內容
    ====================================================== -->
	


    <!-- 遮屏-->
<div style="position:absolute; top:0px; left:0px; width:955px; height:495px; background-color:#000000; opacity:0.8;" onClick=""></div>

	<div style=" top:-1px; left:-80px; margin: 0 auto;">
        
        <img src="../img/back_s.png" style="position:absolute; left:89px; top: 57px;" border="0">
        <img src="./img/reg_tittle.png" style="position:absolute; left:280px; top: -14px;" border="0">
        <a id="out" class="btn_close" onClick="out()" style="position:absolute; left:698px; top: 383px; cursor:pointer;z-index: 999;"></a>
      <div id="iframe" style="position:absolute;top:0px;left:-100px;"></div>
        <a id="left_btn" class="btn_arrow_l" onClick="set_page(-1)" style="position:absolute; cursor:pointer; left:249px; top: 399px;display:none;"></a>
        <a id="right_btn" class="btn_arrow_r" onClick="set_page(1)" style="position:absolute; cursor:pointer;left:500px; top: 399px; display:none;" ></a>
      <div id="page_text" style="position:absolute; top:402px; left:322px; width:177px; height:47px; font-size: 36px; text-align: center; font-weight: bold; color: #406031;"></div>
    </div>
    <!--按鈕列-->
    <!-- <img src="./img/pagge_1.png" style="position:absolute; left:660px; top: 379px; width: 295px; height: 74px;" border="0"> -->
	<a id="popopo" onClick="goooooooo()" style="position:absolute; left:708px; top: 82px; display:none;" ></a>
	<!-- 說明-->
   <!--  <a class=" btn_help" onClick="open_helper(9)" style="position:absolute; top:0px; left:890px; cursor:pointer;"></a>
	<div id="helper" style="position:absolute; top:-8px; left:-8px; width:1040px; height:480px; display:none; overflow:hidden;"></div> -->


	<!--==================================================
    debug內容
    ====================================================== -->
	<div id="debug" style="position:absolute;top:500px;"></div>

	<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var opinion_max_count = 0;
	var max_page = 1 ; 
	var cover_level = 0;


		//各項翻頁數的紀錄
	var page_list = new Array();
	page_list["opinion"] = 1;

	var home_id='<?php echo $user_id;?>';
	var auth_read_opinion_limit_day=14;

	var book_info = new Array();

	var about_book_sid;    
    var about_book_name;
    var about_book_author;
    var about_book_page_count;
    var about_book_publisher;
    var book_borrow_sid;
    var about_book_src;




	
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	function out()
	{
		// window.location.href='../../mssr_menu.php';

		window.parent.location.href = "../../mssr_menu.php";
	}

	// function set_page(value)
	// {
	// 	echo("set_page:開啟畫面 value>"+value);
	// 	if(value !="")
	// 	{
	// 		window.document.getElementById("other_ifame").style.display = "none";
	// 		window.document.getElementById("goout").style.display = "none";
	// 		cover("讀取中");
	// 		window.document.getElementById("iframe").innerHTML = '<iframe id="" src="./'+value+'/index.php" width="1000" height="500" scrolling="no" frameborder="0" style=" top:0px; left:0px; overflow:hidden;"></iframe>';
	// 	}
	// 	else
	// 	{
	// 		cover("讀取中");
	// 		window.document.getElementById("other_ifame").style.display = "block";
	// 		window.document.getElementById("goout").style.display = "block";

	// 		window.document.getElementById("iframe").innerHTML = '';
	// 	}
	// }
	
	//cover
	function cover(text,type,proc)
	{
		
		window.parent.cover(text,type);
		if(type == 2)
		{
			delayExecute(proc);
		}
	}
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	/*cover 啟用器的用法
		 cover("這嘎");
		 cover("這嘎",1);
		 cover("這嘎",2,function(){echo("哈哈");});
		*/
		//cover 點選器
	function delayExecute(proc,proc2)
	{
		var x = 100;
		var hnd = window.setInterval(function ()
		{
			if(cover_click ==1 )
			{//點選確定的狀況
				cover_click = -1;
				cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選確定");
				proc();
				cover("");
			}
			else if(cover_click ==0 )
			{//點選取消的狀況
				cover_click = -1;
				cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選取消");
				cover("");
			}
		}, x);
	}
	//close_cover
	function close_cover(value)
	{
		if(value == 0)cover_click = 0;
		if(value == 1)cover_click = 1;
		if(value == 2)cover_click = 2;
		echo("cover_level"+cover_level);
		if(cover_level<2)
		{
			window.document.getElementById("cover").style.display = "none";
			cover_click = -1;
			cover_level = 0;
		}

	}
	//cover
	function cover(text,type,proc,proc2)
	{
		if(type == 3 && cover_level <= 3)
		{

			window.document.getElementById("cover_btn_1").style.left = "223px";
			window.document.getElementById("cover_btn_1").style.display = "block";
			window.document.getElementById("cover_btn_1").className = "no_box";
			window.document.getElementById("cover_btn_1").innerHTML = "不存檔";
			window.document.getElementById("cover_btn_2").style.left = "430px";
			window.document.getElementById("cover_btn_2").style.display = "block";
			window.document.getElementById("cover_btn_2").className = "no_box";
			window.document.getElementById("cover_btn_0").style.left = "0px";
			window.document.getElementById("cover_btn_0").style.display = "block";
			window.document.getElementById("cover_btn_0").className = "ok_box";
			window.document.getElementById("cover_btn_0").innerHTML = "存檔";
			cover_level = 3;
			window.document.getElementById("cover_text").innerHTML=text;
			window.document.getElementById("cover").style.display = "block";


		}
		else if(type == 2 && cover_level <= 2)
		{
			window.document.getElementById("cover_btn_1").style.left = "100px";
			window.document.getElementById("cover_btn_1").style.display = "block";
			window.document.getElementById("cover_btn_1").className = "ok_box";
			window.document.getElementById("cover_btn_1").innerHTML = "確定";
			window.document.getElementById("cover_btn_2").style.left = "320px";
			window.document.getElementById("cover_btn_2").style.display = "block";
			window.document.getElementById("cover_btn_2").className = "no_box";
			window.document.getElementById("cover_btn_0").style.left = "536px";
			window.document.getElementById("cover_btn_0").style.display = "none";
			window.document.getElementById("cover_btn_0").className = "no_box";
			cover_level = 2;
			window.document.getElementById("cover_text").innerHTML=text;
			window.document.getElementById("cover").style.display = "block";
		}
		else if(type==1 && cover_level <= 1 )
		{
			window.document.getElementById("cover_btn_1").style.left = "210px";
			window.document.getElementById("cover_btn_1").style.display = "block";
			window.document.getElementById("cover_btn_1").className = "ok_box";
			window.document.getElementById("cover_btn_1").innerHTML = "確定";
			window.document.getElementById("cover_btn_2").style.left = "536px";
			window.document.getElementById("cover_btn_2").style.display = "none";
			window.document.getElementById("cover_btn_2").className = "no_box";
			window.document.getElementById("cover_btn_0").style.left = "536px";
			window.document.getElementById("cover_btn_0").style.display = "none";
			window.document.getElementById("cover_btn_0").className = "no_box";
			cover_level = 1;
			window.document.getElementById("cover_text").innerHTML=text;
			window.document.getElementById("cover").style.display = "block";
		}
		else if( cover_level <= 0)
		{
			window.document.getElementById("cover_btn_1").style.display = "none";
			window.document.getElementById("cover_btn_2").style.display = "none";
			window.document.getElementById("cover_btn_0").style.display = "none";
			cover_level = 0;

			if(text!=""&&text!=null)
			{
				window.document.getElementById("cover_text").innerHTML=text;
				window.document.getElementById("cover").style.display = "block";
			}
			else
			{
				window.document.getElementById("cover").style.display = "none";
			}
		}


		if(type == 2 && proc != null)
		{
			delayExecute(proc,null);
		}
		else if(type == 3 && proc2 != null)
		{
			delayExecute(proc,proc2);
		}
	}

		//debug
	function echo(text)
	{
		if(0)window.document.getElementById("debug").innerHTML = text+"<br>"+window.document.getElementById("debug").innerHTML;
	}
	//=========MAIN=============
	function get_opinion_count()
	{
		// echo("get_opinion_count:初始開始:讀取進貨筆數");
		// cover("讀取販賣頁面")
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
		page_list["opinion"] = page_list["opinion"]+value;
		if(page_list["opinion"] == 1 ) 
		{window.document.getElementById("left_btn").style.display = "none";}
		else
		{window.document.getElementById("left_btn").style.display = "block";}
		
		if(page_list["opinion"] == max_page) 
		{window.document.getElementById("right_btn").style.display = "none";}
		else
		{window.document.getElementById("right_btn").style.display = "block";}
		window.document.getElementById("page_text").innerHTML = page_list["opinion"]+" / "+max_page+" 頁";
		window.document.getElementById("iframe").innerHTML ='<iframe src="./manu.php?page='+page_list["opinion"]+'&uid='+home_id+'&auth_read_opinion_limit_day='+auth_read_opinion_limit_day+'" frameborder="0" width="500" height="287" style="position:absolute; top:113px; left:236px; " ></iframe>';
		cover("");
	}
	//GGGGGGGGGGGGGGGGGGGOOOOOOOOOOOOOO近進貨拉

	  function change_page(value)
    {
       

        if(value !="")
        {

            window.location.href='../../read_the_registration_v2/'+ value+'/index.php';
        }
        else
        {
            // cover("讀取中");
            window.document.getElementById("other_ifame").style.display = "block";
            window.document.getElementById("goout").style.display = "block";
            get_shelf();
            coin_coin();
            window.document.getElementById("iframe_area").innerHTML = '';
        }
    }


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
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    