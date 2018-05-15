<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,閱讀登記 -> 選擇搜尋的書籍
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
        $uid          =(isset($_GET[trim('uid')]))?(int)$_GET[trim('uid')]:$_SESSION['uid'];
        $branch_id    =(isset($_GET[trim('branch_id')]))?$_GET[trim('branch_id')]:0;
        $zoom         =(isset($_GET[trim('zoom')]))?(int)$_GET[trim('zoom')]:0;

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
    <link href="../css/registration_btn.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
    <script src="../js/select_thing.js" type="text/javascript"></script>
    <style>
	body{
            font-family: Microsoft JhengHei;
        }

		.boxer
		{
			background-color:#FFF;
			-webkit-border-radius: 5px;
			-moz-border-radius: 5px;
			border-radius: 5px;

			-webkit-box-shadow: #242 1px 1px 1px;
			-moz-box-shadow: #242 1px 1px 1px;
			box-shadow: #242 1px 1px 1px;
			border:2px solid #8EA385;
		}
       	.box_ling{
			-webkit-border-radius: 8px;
			-moz-border-radius: 8px;
			border-radius: 8px;

			-webkit-box-shadow: #242 1px 1px 1px;
			-moz-box-shadow: #242 1px 1px 1px;
			box-shadow: #242 1px 1px 1px;
			background-color:#aaffaa;
			border: 2px solid #3a8c3a;
		}
		.box_ling1{
			-webkit-border-radius: 8px;
			-moz-border-radius: 8px;
			border-radius: 8px;

			-webkit-box-shadow: #242 0px 0px 1px;
			-moz-box-shadow: #242 0px 0px 1px;
			box-shadow: #242 0px 0px 1px;
			border:1px solid #8EA385;
			background-color:#FFF;

		}
		.cover_box{
			-webkit-border-radius: 14px;
			-moz-border-radius: 14px;
			border-radius: 14px;

			-webkit-box-shadow: #242 1px 1px 3px;
			-moz-box-shadow: #242 1px 1px 3px;
			box-shadow: #242 2px 1px 1px;

			border: 1px solid #111;
			font-size:24px;
			color:#333;
			font-weight: bold;

			background: rgb(242,246,248); /* Old browsers */
			background: -moz-linear-gradient(top,  rgba(242,246,248,1) 0%, rgba(181,198,208,1) 6%, rgba(181,198,208,1) 6%, rgba(216,225,231,1) 21%, rgba(224,239,249,1) 48%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(242,246,248,1)), color-stop(6%,rgba(181,198,208,1)), color-stop(6%,rgba(181,198,208,1)), color-stop(21%,rgba(216,225,231,1)), color-stop(48%,rgba(224,239,249,1))); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  rgba(242,246,248,1) 0%,rgba(181,198,208,1) 6%,rgba(181,198,208,1) 6%,rgba(216,225,231,1) 21%,rgba(224,239,249,1) 48%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  rgba(242,246,248,1) 0%,rgba(181,198,208,1) 6%,rgba(181,198,208,1) 6%,rgba(216,225,231,1) 21%,rgba(224,239,249,1) 48%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  rgba(242,246,248,1) 0%,rgba(181,198,208,1) 6%,rgba(181,198,208,1) 6%,rgba(216,225,231,1) 21%,rgba(224,239,249,1) 48%); /* IE10+ */
			background: linear-gradient(to bottom,  rgba(242,246,248,1) 0%,rgba(181,198,208,1) 6%,rgba(181,198,208,1) 6%,rgba(216,225,231,1) 21%,rgba(224,239,249,1) 48%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f2f6f8', endColorstr='#e0eff9',GradientType=0 ); /* IE6-9 */
		}
		.text
		{
			font-size: 18px;
			font-weight: bold;
			white-space:nowrap;
			overflow:hidden;
			color:#333;
		}
	</style>
</Head>
<body>

	<!--==================================================
    html內容
    ====================================================== -->
    <!--標題開頭-->
    <img src="./img/text1.png" style="position:absolute; top:20px; left:25%" class="box_ling">

    <!--書籍選擇欄位-->
      <!-- 改過 -->
    <div id="book_choose_page" style="position:absolute; top:82px; left:16px; width:600px; height:300px;" class="box_ling">
    	  <!-- 改過 -->
    	<iframe src="box.php" style="position:absolute; top:8px; left:8px; width:580px; height:280px;" frameborder="0"></iframe>
    </div>


    <!--按鈕-->
    <a id="ok_btn" onClick="read_the_registration()" class="btn_2" style="position:absolute; top:379px; left:265px; display:none; cursor:pointer;" ></a>
	<a onClick="back_page()" class="btn_1" style="position:absolute; top:379px; left:645px; cursor:pointer;" ></a>
    <div style="position:absolute; top:402px; left:981px; width:37px; height:46px; background-color:#F8C967"></div>
    <!--書籍資料顯示-->

    <div style="position:absolute; width:269px; height:300px; top:82px; left:650px;" class="box_ling"></div>

    <div style="position:absolute; height:30px; width:251px; top:301px; left:665px;" class="text">作者:</div>
 
    <div style="position:absolute; height:30px; width:251px; top:324px; left:665px;" class="text">出版社:</div>

    <div style="position:absolute; height:30px; width:251px; top:347px; left:665px;" class="text">捐書者:</div>
     
    <img id="book_pic" src="./0.png" width="133" height="166" style="position:absolute; top:96px; left:670px; display:none;">
     
    <div id="text_name" style="position:absolute; height:30px; width:255px; top:275px; left:665px;" class="text"></div>
     
    <div id="text_author" style="position:absolute; height:30px; width:192px; top:301px; left:715px;" class="text"></div>
     
    <div id="text_publisher" style="position:absolute; height:30px; width:166px; top:324px; left:730px;" class="text"></div>
     
    <div id="text_donor" style="position:absolute; height:30px; width:166px; top:347px; left:730px;" class="text"></div>
<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------

	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------

	//cover
	function cover(text,type)
	{
		window.parent.cover(text,type);
	}

	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}

	//點選書籍
	function click_box(value)
	{
		window.document.getElementById("book_pic").src = window.parent.book_info[value]["src"];
		window.document.getElementById("ok_btn").style.display = "block";
		window.document.getElementById("book_pic").style.display = "block";
		window.document.getElementById("text_name").innerHTML = window.parent.book_info[value]["book_name"];
		window.document.getElementById("text_author").innerHTML = window.parent.book_info[value]["book_author"];
		window.document.getElementById("text_publisher").innerHTML = window.parent.book_info[value]["book_publisher"];
		window.document.getElementById("text_publisher").innerHTML = "";
		if(window.parent.book_info[value]["book_donor"])window.document.getElementById("text_publisher").innerHTML = window.parent.book_info[value]["book_donor"];
	}



	//確認登記書籍
	function read_the_registration()
	{


		echo("set_read_registration:登記書籍資料中: 書籍SID => "+window.parent.book_info[window.parent.book_choose]["book_sid"]);
		cover("登記書籍資料中");
		var url = "../ajax/set_read_registration.php";
		$.post(url, {
					user_id:window.parent.user_id,
					book_sid:window.parent.book_info[window.parent.book_choose]["book_sid"],
					book_name:window.parent.book_info[window.parent.book_choose]["book_name"],
					user_permission:window.parent.user_permission,
					shool_code:window.parent.user_school,
					school_category:window.parent.user_school_category,
					grade_id:window.parent.user_grade,
					classroom_id:window.parent.user_class
			}).success(function (data)
			{
				console.log(data);
				echo("AJAX:success:set_read_registration:登記書籍資料中:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",1);
					echo("AJAX:success:set_read_registration:登記書籍資料中:資料庫發生問題");
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
				{//成功  轉跳頁面

					window.parent.borrow_sid = data_array["borrow_sid"];
					if(1)//新版囉
					{

						document.location.href="../page_opinion_registration2/index.php";
					}
					else
					{
						document.location.href="../page_opinion_registration/index.php";
					}


				}

			}).error(function(e){
				echo("AJAX:error:set_read_registration:登記書籍資料中:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",1);
			}).complete(function(e){
				echo("AJAX:complete:set_read_registration:登記書籍資料中:");
			});
	}
	//回上頁
	function back_page()
	{
		document.location.href="../page_find_book/index.php";
	}


	//=========MAIN=============
	function main()
	{
		echo("Main:初始開始:讀取使用者資料");
		cover("讀取使用者資料中")
		var url = "../ajax/get_user_info.php";
		$.post(url, {
					user_id:window.parent.user_id,
					user_permission:window.parent.user_permission

			}).success(function (data)
			{
				echo("AJAX:success:main():讀取使用者資料:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",1);
					echo("AJAX:success:main():讀取使用者資料:資料庫發生問題");
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
					cover("");
				}

			}).error(function(e){

				cover("喔喔?! 讀取失敗了喔  請確認網路連",1);
				//main();
			}).complete(function(e){
				echo("AJAX:complete:main():讀取使用者資料:");
			});
	}

	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        $(function(){

        });



    </script>
</Html>














