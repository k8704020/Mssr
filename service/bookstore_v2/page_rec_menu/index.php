<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 推薦狀況表單頁
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
		require_once(str_repeat("../",3).'inc/get_permission_and_timetable/code.php');


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

    ///---------------------------------------------------
    //SESSION
    //---------------------------------------------------


    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
		$user_id        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';



		$status = 'u_mssr_bs';
		$t_p_sut=get_permission_and_timetable($conn='',$permission,$status,$arry_conn_user);

		//建立連線 user
    	$conn_user=conn($db_type='mysql',$arry_conn_user);

		$sess_permission=addslashes(trim($_SESSION['permission']));
		$u_sb_flag=false;
		$sql="
			SELECT `status`
			FROM `permissions`
			WHERE 1=1
				AND `permission`='{$sess_permission}'
			";
		$db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
		if(!empty($db_results)){
			foreach($db_results as $db_result){
				$rs_status=trim($db_result['status']);
				if($rs_status==='u_sb'){$u_sb_flag=true;}
			}
		}




		if($t_p_sut["permission_ok"]==0)die($t_p_sut["permission_msg"]);
		if($t_p_sut["time_ok"]==0)die($t_p_sut["time_msg"]);
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
    <script src="js/JScript.js" type="text/javascript"></script>
	<script src="../js/select_thing.js" type="text/javascript"></script>
	<script src="../js/set_bookstore_action_log.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../css/btn.css">
	<style>



		#rec_btn{
			position:absolute;
			width:262px;
			height:303px;
			background: url('./img/rec_book_btn.png') 0 0;
		}
		#rec_btn:hover {
    		background: url('./img/rec_book_btn.png') 0 -303px;
		}
		#bookstory_btn{
			<? if($u_sb_flag){?>
			position:absolute;
			width:132px;
			height:132px;
			background: url('./img/bookstory.png?141') 0 0;<? }?>
		}
		#bookstory_btn:hover {
    		<? if($u_sb_flag){?>
			background: url('./img/bookstory.png?141') 0 -132px;
			<? }?>
		}
		.rec_bar1{
			position:absolute;
			width:65px;
			height:26px;
			background: url('img/rec_bar.png') 0px -26px;
		}
		.rec_bar1:hover {
			background: url('img/rec_bar.png') 0px 0;
		}
		.rec_bar1s{
			position:absolute;
			width:65px;
			height:26px;
			background: url('img/rec_bar.png') 0px -52px;
		}
		.rec_bar4{
			position:absolute;
			width:65px;
			height:26px;
			background: url('img/rec_bar.png') -200px -26px;
		}
		.rec_bar4:hover {
			background: url('img/rec_bar.png') -200px 0;
		}
		.rec_bar4s{
			position:absolute;
			width:65px;
			height:26px;
			background: url('img/rec_bar.png') -200px -52px;
		}
		.rec_bar2{
			position:absolute;
			width:78px;
			height:26px;
			background: url('img/rec_bar.png') -64px -26px;
		}
		.rec_bar2:hover {
			background: url('img/rec_bar.png') -64px 0;
		}
		.rec_bar2s {
			position:absolute;
			width:78px;
			height:26px;
			background: url('img/rec_bar.png') -64px -52px;
		}
		.rec_bar3{
			position:absolute;
			width:59px;
			height:26px;
			background: url('img/rec_bar.png') -141px -26px;
		}
		.rec_bar3:hover {
			background: url('img/rec_bar.png') -141px 0;
		}
		.rec_bar3s {
			position:absolute;
			width:59px;
			height:26px;
			background: url('img/rec_bar.png') -141px -52px;
		}
		.btn5{
			position:absolute;
			width:85px;
			height:85px;
			background: url('../img/btn_list_2.png') -340px 0;
		}
		.btn5:hover {
    		background: url('../img/btn_list_2.png') -340px -170px;
		}
	</style>
</Head>
<body>

	<!--==================================================
    html內容
    ====================================================== -->
    <!-- 遮屏-->
	<div style="position:absolute; top:0px; left:0px; width:1000px; height:480px; background-color:#000000; opacity:0.8;" onClick=""><span style="position:absolute; left:-122px; top: -3px;"></span></div>
    <img src="../img/back.png" style="position:absolute; left:20px; top: 59px;" border="0">
	<div style="position:absolute; left:-122px; top: -3px;"><img src="./img/rec_tittle.png" style="position:absolute; left:380px; top: -14px;" border="0">
		<!-- 改過 -->
        <a id="out" class="btn_close" onClick="out()" style="position:absolute; top::30px; left:755px; top: 400px; cursor:pointer;"></a>
      <div id="sp_help" style="position:absolute; top:236px; left:220px; color:#993300; font-size:36px; text-align:center; width: 568px; display:none;">沒有書籍可以推薦?<BR>
      可以先看書登記喔!</div>
      <div id="iframe" style="position:absolute; top:8px; left:-54px;">aaa</div>
      	 <!-- 改過 -->
        <a id="left_btn" class="btn_arrow_l" onClick="set_page(-1)" style="position:absolute; cursor:pointer; left:349px; top: 405px;display:none;"></a>
         <!-- 改過 -->
        <a id="right_btn" class="btn_arrow_r" onClick="set_page(1)" style="position:absolute; cursor:pointer; left:600px; top: 405px;display:none;"></a>
         <!-- 改過 -->
      	<div id="page_text" style="position:absolute; top:408px; left:422px; width:177px; height:47px; font-size: 36px; text-align: center; font-weight: bold; color: #406031;"></div>
		  <!-- 改過 -->
        <a id="rec_btn" onClick="goooooooo()" style="position:absolute; left:840px; top: 48px;  cursor:pointer;" border="0"></a>
         <!-- 改過 -->
        <a id="bookstory_btn" onClick="gobookstory()" style="position:absolute; left:900px; top: 345px; cursor:pointer;" ></a>
		<!-- 推薦選單-->
    	<div style="position:absolute; top:96px; left:174px;">
            <a id="rec_bar1" class="rec_bar1s" onClick="window.parent.set_action_bookstore_log(window.parent.user_id,'e38',window.parent.action_on);change_mode(1)" style="position:absolute; top:-10px; left:0px; cursor:pointer;"></a>
            <a id="rec_bar2" class="rec_bar2" onClick="window.parent.set_action_bookstore_log(window.parent.user_id,'e39',window.parent.action_on);change_mode(2)" style="position:absolute; top:-10px; left:65px; cursor:pointer;"></a>
            <a id="rec_bar3" class="rec_bar3" onClick="window.parent.set_action_bookstore_log(window.parent.user_id,'e40',window.parent.action_on);change_mode(3)" style="position:absolute; top:-10px; left:143px; cursor:pointer;"></a>
            <a id="rec_bar4" class="rec_bar4" onClick="change_mode(4)" style="position:absolute; top:-10px; left:480px; cursor:pointer; display:none;"></a>
        </div>
</div>
	<form method="post" name="form1D" id="form1D" action='./php/content_pdf.php?user_id=<? echo $_SESSION['uid'];?>' target='選擇項目列印' onsubmit="go_point('','選擇項目列印','width=200,height=200')">
      <input id="post_books_sid" name='try' type="text" value="d" style="position:absolute; top:395px; left:356px; display:none;">
        <span id="sub_print_explanation"
        style="color:#000;position:absolute;bottom:50px;left:55px;display:none;"
        >請先選擇想要列印的推薦內容</span>
		<input id="sub_print_1" type='submit' name='submit' value='列印'  style="position:absolute; display:none; top:430px; left:280px;"></form>
    	<!-- input id="sub_print_2" type="button" onClick="go_point_all()" value="全部列印" style="position:absolute; display:none; top:418px; left:261px;" -->
    </form>
      <!-- 改過 -->
	<a class="btn_help" onClick="open_helper(8)" style="position:absolute; top:-6px; left:890px; cursor:pointer;"></a>
	<div id="helper" style="position:absolute; top:-8px; left:-8px; width:1050px; height:480px; display:none; overflow:hidden;"></div>
<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var rec_max_count = 0;
	var max_page = 1 ;
	var chick_sid = "";
	var book_sid = new Array();
	var select_book = new Array();
	var select_count = 0;
	var books_count = 0;
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	function out()
	{
		window.parent.set_page("");
	}
	function go_point_all()
	{


	//	window.alert(window.document.getElementById("post_books_sid").value);
		window.open("./php/content_pdf.php?user_id=<? echo $_SESSION['uid'];?>","");
	}
	function go_point(url,target_name,wh_value)
	{
		window.document.getElementById("post_books_sid").value = "";
		
		for( var i = 0 ; i < books_count;i++)
		{
			if(select_book[i]==0)
			{
				if(window.document.getElementById("post_books_sid").value != "")window.document.getElementById("post_books_sid").value += "_";
				window.document.getElementById("post_books_sid").value += book_sid[i];
			}//window.alert(book_sid[i]);
		}
		window.open("",target_name);

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
	function change_mode(value)
	{
		if(value==4)
		{
			set_rec_bar(value);
			window.document.getElementById("sub_print_1").style.display = "block";
            window.document.getElementById("sub_print_explanation").style.display = "";
			//window.document.getElementById("sub_print_2").style.display = "block";
			window.document.getElementById("right_btn").style.display = "none";
			window.document.getElementById("left_btn").style.display = "none";
			window.document.getElementById("page_text").style.display = "none";
			window.document.getElementById("iframe").innerHTML ='<iframe src="./manu_print.php" id="IIO" frameborder="0" width="658" height="287" style="position:absolute; top:104px; left:228px; " ></iframe>';
		}
		else
		{
			window.document.getElementById("sub_print_1").style.display = "none";
            window.document.getElementById("sub_print_explanation").style.display = "none";
			//window.document.getElementById("sub_print_2").style.display = "none";
			window.document.getElementById("page_text").style.display = "block";
			window.parent.page_list["rec_mode"] = value;
			rec_max_count = 0;
			max_page = 1 ;
			window.parent.page_list["rec"] = 1;
			chick_sid = "";
			set_rec_bar(value);
			get_rec_count();
		}
	}
	function set_rec_bar(value)
	{
		for(var i = 1 ; i <= 4  ; i++)
		{
			window.document.getElementById("rec_bar"+i).className = "rec_bar"+i;
		}
		window.document.getElementById("rec_bar"+value).className = "rec_bar"+value+"s";
	}
	if(window.parent.home_id == <? echo $_SESSION["uid"];?>)
	{
		window.document.getElementById("rec_bar4").style.display = "block";
	}
	//=========MAIN=============
	function get_rec_count()
	{
		set_rec_bar(window.parent.page_list["rec_mode"]);
		echo("get_rec_count:初始開始:讀取推薦筆數");
		cover("讀取中");
		var url = "./ajax/get_rec_count.php";
		$.post(url, {
					user_id:window.parent.home_id,
					user_permission:window.parent.user_permission,
					select_mode:window.parent.page_list["rec_mode"],
					auth_read_opinion_limit_day:window.parent.auth_read_opinion_limit_day

			}).success(function (data)
			{

				if(data[0]!="{")
				{
					echo("AJAX:success:get_rec_count():讀取推薦筆數:資料庫發生問題");
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",2,function(){get_rec_count();});
					return false;
				}

				data_array = JSON.parse(data);
				echo("AJAX:success:get_rec_count():讀取推薦筆數:已讀出:"+data);
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
					rec_max_count = data_array["rec_count"];
					if(rec_max_count == 0 && window.parent.page_list["rec_mode"] ==1)
					{
						max_page = 1;
						window.document.getElementById("sp_help").style.display = "block";
					}
					else
					{
						max_page = Math.floor((Math.floor(rec_max_count)-1)/10)+1
						if(max_page == 0){
							max_page = 1;
						}
						
					}
						
					set_page(0);
					//cover("");
				}
			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){
				echo("AJAX:complete:main():讀取推薦筆數:");
			});
	}
	function set_page(value)
	{
		cover("讀取中");
		window.document.getElementById("rec_btn").style.display = "none";
		window.document.getElementById("bookstory_btn").style.display = "none";
		echo("set_page:到期天數:"+window.parent.auth_read_opinion_limit_day);
		window.parent.page_list["rec"] = window.parent.page_list["rec"]+value;

		if(window.parent.page_list["rec"] == 1)
		{window.document.getElementById("left_btn").style.display = "none";}
		else
		{window.document.getElementById("left_btn").style.display = "block";}

		if(window.parent.page_list["rec"] == max_page)
		{window.document.getElementById("right_btn").style.display = "none";}
		else
		{window.document.getElementById("right_btn").style.display = "block";}
		window.document.getElementById("page_text").innerHTML = window.parent.page_list["rec"]+" / "+max_page+" 頁";
		//改過
		window.document.getElementById("iframe").innerHTML ='<iframe src="./manu.php?page='+window.parent.page_list["rec"]+'&uid='+window.parent.home_id+'&auth_read_opinion_limit_day='+window.parent.auth_read_opinion_limit_day+'&select_mode='+window.parent.page_list["rec_mode"]+'" frameborder="0" width="658" height="310" style="position:absolute; top:103px; left:228px; " ></iframe>';

	}
	//GGGGGGGGGGGGGGGGGGGOOOOOOOOOOOOOO近推薦拉
	function goooooooo()
	{
		//吃土
		echo("我要這本書拉WW"+chick_sid);
		window.parent.set_page("page_rec_edit");

	}

	//去說書人
	function gobookstory()
	{
		post('../../../../draw_story/storyBooks.php', {book_ID: chick_sid});

	}

	function post(path, params, method) {
		method = method || "post"; // Set method to post by default if not specified.

		// The rest of this code assumes you are not using a library.
		// It can be made less wordy if you use one.
		var form =window.parent.parent.document.createElement("form");
		form.setAttribute("method", method);
		form.setAttribute("action", path);

		for(var key in params) {
			if(params.hasOwnProperty(key)) {
				var hiddenField = window.parent.parent.document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", key);
				hiddenField.setAttribute("value", params[key]);

				form.appendChild(hiddenField);
			 }
		}

		window.parent.parent.document.body.appendChild(form);
		form.submit();
	}
	//---------------------------------------------------
    //helper
    //---------------------------------------------------
	function open_helper(value)
	{
		window.document.getElementById("helper").innerHTML="<iframe src='../page_helper/index.php?id="+value+"' style='position:absolute; top:0px; left:0px; width:1050px; height:590px;  overflow:hidden;' frameborder='0'></iframe>";
		window.document.getElementById("helper").style.display = "block";
	}
	//---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	get_rec_count();


    </script>
</Html>














