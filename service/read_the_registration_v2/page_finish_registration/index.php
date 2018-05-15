<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,閱讀登記 -> 轉跳頁面
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
	@session_start();
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
	//建立連線 user
		$conn_user=conn($db_type='mysql',$arry_conn_user);
		$sess_permission=addslashes(trim($_SESSION['permission']));
		$forum_flag=false;
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
				if($rs_status==='u_mssr_forum'){$forum_flag=true;continue;}
			}
		}
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
<Html><Head>
	<Title>閱讀登記</Title>
   
    <link href="../css/registration_btn.css" rel="stylesheet" type="text/css">
    <script src="../js/select_thing.js" type="text/javascript"></script>
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
       .text
		{
			font-size: 18px;
			font-weight: bold;
			white-space:nowrap;
			overflow:hidden;
			color:#333;
			font-family: Microsoft JhengHei;
		}
		.fff{
			position:absolute;
			width:215px;
			height:46px;
			background: url('./img/to_fun.png') 0 0;
		}
		.fff:hover {
    		background: url('./img/to_fun.png') 0 -46px;
		}
	</style>
</Head>
<body>

	<!--==================================================
    html內容
    ====================================================== -->
	<!--書籍資料顯示-->
    <div style="position:absolute; top:402px; left:981px; width:37px; height:46px; background-color:#F8C967"></div>
    <div style="position:absolute; width:269px; height:330px; top:92px; left:17px;" class="box_ling"></div>
    <div style="position:absolute; height:30px; width:251px; top:344px; left:24px;" class="text">作者:</div>
	<div style="position:absolute; height:30px; width:251px; top:367px; left:24px;" class="text">出版社:</div>
    <div style="position:absolute; height:30px; width:251px; top:390px; left:24px;" class="text">捐書者:</div>
    <img id="book_pic" src="./0.png" width="133" height="166" style="position:absolute; top:122px; left:33px; display:none;">
	<div id="text_name" style="position:absolute; height:30px; width:255px; top:298px; left:24px;" class="text"></div>
	<div id="text_author" style="position:absolute; height:30px; width:192px; top:344px; left:85px;" class="text"></div>
	<div id="text_publisher" style="position:absolute; height:30px; width:166px; top:367px; left:112px;" class="text"></div>
    <div id="text_donor" style="position:absolute; height:30px; width:166px; top:390px; left:112px;" class="text"></div>
    <?php if($forum_flag){?>
    <a id="fff" class="fff" onClick="go_fff()" style="position:absolute; top:200px; left:490px; cursor:pointer"></a>
   	<?PHP  }?>
    <a id="book_pic" class="btn_11" onClick="go_agian()" style="position:absolute; top:310px; left:331px; cursor:pointer"></a>
    <a class="btn_10" onClick="go_store()" style="position:absolute; top:308px; left:658px; cursor:pointer;"></a>
 	<img src="./img/opin_finish.png" style="position:absolute; top:84px; left:310px;">
	<img id="opin_finish1" src="./img/opin_finish1.png" style="position:absolute; top:263px; left:311px; display:none;">
	<img id="opin_finish2" src="./img/opin_finish2.png" style="position:absolute; top:263px; left:311px; display:none;">
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
	//繼續登記下一本
	function go_agian()
	{
		window.document.location.href="../page_find_book/index.php";
	}
	//進入書店
	function go_store()
	{
		if(1)
		{
			window.parent.document.location.href="../../bookstore_v2/bookstore_courtyard/index.php";
		}else
		{
			window.parent.document.location.href="../../bookstore_v2/index.php?open=page_opinion_menu";
		}
	}
	//進入
	function go_fff()
	{
		window.parent.parent.document.location.href="../../forum/view/index.php";
	}
	//show_book
	function show_book(value)
	{
		window.document.getElementById("book_pic").src = window.parent.book_info[value]["src"];
		window.document.getElementById("book_pic").style.display = "block";
		window.document.getElementById("text_name").innerHTML = window.parent.book_info[value]["book_name"];
		window.document.getElementById("text_author").innerHTML = window.parent.book_info[value]["book_author"];
		window.document.getElementById("text_publisher").innerHTML = window.parent.book_info[value]["book_publisher"];
		if(window.parent.book_info[value]["book_donor"])window.document.getElementById("text_donor").innerHTML = window.parent.book_info[value]["book_donor"];

		//沒錯  又是中平模式
		if(window.parent.user_school == 'gcp')
		{
			window.document.getElementById("opin_finish1").style.display = "block";
		}
		else
		{
			window.document.getElementById("opin_finish2").style.display = "block";
		}
		cover("");
	}
	
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        
		show_book(window.parent.book_choose);
    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    