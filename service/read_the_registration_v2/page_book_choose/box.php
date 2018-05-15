<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,閱讀登記 -> 選擇搜尋的書籍 -> 選書欄位
//(內頁)  //主頁面 or 內頁
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

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
    <script src="../js/select_thing.js" type="text/javascript"></script>
    
    <style>
		body
		{
			overflow-y: scroll;
			overflow-x: hidden;
		}
       
		.box_ling1{
			-webkit-border-radius: 8px;
			-moz-border-radius: 8px;
			border-radius: 8px;
			float:left;
			-webkit-box-shadow: #242 0px 0px 1px;
			-moz-box-shadow: #242 0px 0px 1px;
			box-shadow: #242 0px 0px 1px;
			border:1px solid #8EA385;
			background-color:#FFF;
			width:310px;
			height:167px;
		}
		.box_ling2{
			-webkit-border-radius: 8px;
			-moz-border-radius: 8px;
			border-radius: 8px;
			float:left;
			-webkit-box-shadow: #242 0px 0px 1px;
			-moz-box-shadow: #242 0px 0px 1px;
			box-shadow: #242 0px 0px 1px;
			border:6px solid #F2BD40;
			background-color:#FFF;
			width:300px;
			height:157px;
		
		}
		.text_1
		{
			font-size: 18px;
			font-weight: bold;
			white-space:nowrap;
			overflow:hidden;
			color:#333;
		}
		.text_2
		{ 
			font-size: 12px;
			
			white-space:nowrap;
			overflow:hidden;
			color:#666;
		}
	</style>
</Head>
<body>

	<!--==================================================
    html內容
    ====================================================== -->

	<div id="box" style="position:absolute; top:0px; left:0px; width:632px;">
    </div>

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
	
	//點取書按鈕
	function click_box(value)
	{
		echo("點選box -> "+value);
		for(var i = 0 ; i < window.parent.parent.book_info.length ; i++)
		{
			window.document.getElementById("box_"+i).className = "box_ling1";
		}
		window.parent.parent.book_choose = value;
		window.parent.click_box(value);
		window.document.getElementById("box_"+value).className = "box_ling2";
	}
	//建立選擇項目
	function set_choose_box(value)
	{
		
		//先建立空欄位		
		for(var i = 0 ; i < window.parent.parent.book_info.length ; i++)
		{
			window.document.getElementById("box").innerHTML+='<div id="box_'+i+'" onClick="click_box('+i+')" style="position:relative;" class="box_ling1"></div>';
		}
		
		for(var i = 0 ; i < window.parent.parent.book_info.length ; i++)
		{
			
			//建立圖片
			window.document.getElementById("box_"+i).innerHTML+='<img src="'+window.parent.parent.book_info[i]["src"]+'" width="107" height="138" style="position:absolute; top:11px; left:18px; height: 146px;">';
			//建立書籍名稱
			window.document.getElementById("box_"+i).innerHTML+='<div  style="position:absolute; top:12px; left:129px; height: 28px; width: 176px;" class="text_1">'+window.parent.parent.book_info[i]["book_name"]+'</div>';
			//建立作者名稱
			window.document.getElementById("box_"+i).innerHTML+='<div  style="position:absolute; top:93px; left:132px; height: 18px; width: 174px;" class="text_2">'+window.parent.parent.book_info[i]["book_author"]+'</div>';
			//建立出版社名稱
			window.document.getElementById("box_"+i).innerHTML+='<div  style="position:absolute; top:109px; left:132px; height: 18px; width: 174px;" class="text_2">'+window.parent.parent.book_info[i]["book_publisher"]+'</div>';
			//建立捐獻者名稱
			if(window.parent.parent.book_info[i]["book_donor"])window.document.getElementById("box_"+i).innerHTML+='<div  style="position:absolute; top:115px; left:132px; height: 18px; width: 174px;" class="text_2">捐書者:'+window.parent.parent.book_info[i]["book_donor"]+'</div>';
			//建立點選按鈕(改過)
			window.document.getElementById("box_"+i).innerHTML+='<button style="position:absolute; top:135px; left:132px; height: 22px; width: 160px; cursor:pointer;" >選擇這本書</button>';
		}
		cover("");
	}
	
	
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        $(function(){
           
        });
		
		set_choose_box();

    </script>

</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    