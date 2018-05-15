<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,閱讀登記 -> 建立書籍頁面
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
        $uid          =(isset($_GET[trim('uid')]))?(int)$_GET[trim('uid')]:$sess_uid;
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
    <script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../lib/jquery/plugin/func/block_ui/code.js"></script>
    <script type="text/javascript" src="../../../lib/jquery/ui/code.js"></script>
    <link href="../css/registration_btn.css" rel="stylesheet" type="text/css">
    
    <style>
	body{
            font-family: Microsoft JhengHei;
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
	</style>
</Head>
<body>

	<!--==================================================
    html內容
    ====================================================== -->
	<div style="position:absolute; top:402px; left:981px; width:37px; height:46px; background-color:#F8C967"></div>
    <div id="book_choose_page" style="position:absolute; top:74px; left:184px; width:667px; height:227px;" class="box_ling">
    	<img src="./img/opin_unfind_text.png"  style="position:absolute; top:11px; left:5px;">
        <input id="input_text_1" type="text" value="" style="position:absolute; top:75px; left:220px; font-size:32px; resize: none; width: 417px;"/>
        <input id="input_text_2" type="text" value="" style="position:absolute; top:126px; left:141px; font-size:32px; resize: none; width: 496px;"/>
        <input id="input_text_3" type="text" value="" style="position:absolute; top:179px; left:167px; font-size:32px; resize: none; width: 470px;"/>
        <a  class="btn_9" onClick="book_the_registration()" style="position:absolute; top:250px; left:48px;cursor:pointer;"></a>
        <a  class="btn_1" src="./img/btn_g_check_no.png" onClick="back_page()"  style="position:absolute; top:242px; left:450px; cursor:pointer;"></a>
    </div>
    



<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var book_sid = "";
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
	//建立一張新~~~書籍
	function book_the_registration()
	{
		echo("book_the_registration:寫入書籍資料->"+window.document.getElementById("input_text_1").value);
		cover("寫入書籍資料");
		var url = "./ajax/set_book_registration.php";
		$.post(url, {
					user_id:window.parent.user_id,
					user_permission:window.parent.user_permission,
					
					isbn10:window.parent.isbn10,
					isbn13:window.parent.isbn13,
					
					book_name:window.document.getElementById("input_text_1").value,
					book_author:window.document.getElementById("input_text_2").value,
					book_publisher:window.document.getElementById("input_text_3").value
			}).success(function (data) 
			{
				echo("AJAX:success:book_the_registration:寫入書籍資料:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",1);
					echo("AJAX:success:book_the_registration:寫入書籍資料中:資料庫發生問題");
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
					window.parent.book_choose = 0;
					window.parent.book_sid=data_array["book_sid"];
					window.parent.book_info[0] = new Array();
					window.parent.book_info[0]["src"] = "./0.png";
					window.parent.book_info[0]["book_sid"] = data_array["book_sid"];
					window.parent.book_info[0]["book_name"] = window.document.getElementById("input_text_1").value;
					window.parent.book_info[0]["book_author"] = window.document.getElementById("input_text_2").value;
					window.parent.book_info[0]["book_publisher"] = window.document.getElementById("input_text_3").value;
					read_the_registration();
				}
				
			}).error(function(e){
				echo("AJAX:error:book_the_registration:寫入書籍資料中:");
				window.alert("喔喔?! 讀取失敗了喔  請確認網路連");
				read_the_registration();
			}).complete(function(e){
				echo("AJAX:complete:book_the_registration:寫入書籍資料中:");
			});		
	}
	
	
	//確認登記書籍
	function read_the_registration()
	{
		echo("read_the_registration:登記書籍資料中: 書籍SID => "+window.parent.book_sid);
		cover("登記書籍資料中");
		var url = "../ajax/set_read_registration.php";
		$.post(url, {
					user_id:window.parent.user_id,
					book_sid:window.parent.book_sid,
					book_name:window.parent.book_name,
					user_permission:window.parent.user_permission,
					shool_code:window.parent.user_school,
					school_category:window.parent.user_school_category,
					grade_id:window.parent.user_grade,
					classroom_id:window.parent.user_class
			}).success(function (data) 
			{
				echo("AJAX:success:read_the_registration:登記書籍資料中:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",1);
					echo("AJAX:success:read_the_registration:登記書籍資料中:資料庫發生問題");
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
					window.alert(data_array["echo"]);
					read_the_registration();
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
				echo("AJAX:error:read_the_registration:登記書籍資料中:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",1);
				//read_the_registration();
			}).complete(function(e){
				echo("AJAX:complete:read_the_registration:登記書籍資料中:");
			});		
	}
	//回上頁
	function back_page()
	{
	document.location.href="../page_find_book/index.php";
	}
	
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        
	cover("找不到書籍請自行輸入",1);
    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    