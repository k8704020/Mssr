<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,閱讀登記
//(主頁面)  //主頁面 or 內頁
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",2).'config/config.php');
		require_once(str_repeat("../",2).'inc/get_permission_and_timetable/code.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();
		
		//建立連線 user
		
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
		$user_id        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
		$status = 'u_mssr_bs';
		$t_p_sut=get_permission_and_timetable($conn='',$permission,$status,$arry_conn_user);
	
		if($t_p_sut["permission_ok"]==0)die($t_p_sut["permission_msg"]);
		if($t_p_sut["time_ok"]==0)die($t_p_sut["time_msg"]);
		if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");
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
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <!-- 掛載 -->
    <script type="text/javascript" src="../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
	<script src="js/select_thing.js" type="text/javascript"></script>
    <script src="../../../ac/js/user_log.js"></script>
    
    <style>
        body{
            font-family: Microsoft JhengHei;
        }
		.cover_box{
			padding: 7px 14px;
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
		
		.no_box{
			-webkit-border-radius: 8px;
			-moz-border-radius: 8px;
			border-radius: 8px;
			
			-webkit-box-shadow: #000 2px 2px 3px;
			-moz-box-shadow: #000 2px 2px 3px;
			box-shadow: #000 2px 2px 1px;
			
			border: 1px solid #111;
			font-size:24px;
			color:#333;			
			font-weight: bold;
			
			background: rgb(242,246,248); /* Old browsers */
			background: -moz-linear-gradient(top,  rgba(242,246,248,1) 0%, rgba(216,225,231,1) 50%, rgba(181,198,208,1) 86%, rgba(224,239,249,1) 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(242,246,248,1)), color-stop(50%,rgba(216,225,231,1)), color-stop(86%,rgba(181,198,208,1)), color-stop(100%,rgba(224,239,249,1))); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  rgba(242,246,248,1) 0%,rgba(216,225,231,1) 50%,rgba(181,198,208,1) 86%,rgba(224,239,249,1) 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  rgba(242,246,248,1) 0%,rgba(216,225,231,1) 50%,rgba(181,198,208,1) 86%,rgba(224,239,249,1) 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  rgba(242,246,248,1) 0%,rgba(216,225,231,1) 50%,rgba(181,198,208,1) 86%,rgba(224,239,249,1) 100%); /* IE10+ */
			background: linear-gradient(to bottom,  rgba(242,246,248,1) 0%,rgba(216,225,231,1) 50%,rgba(181,198,208,1) 86%,rgba(224,239,249,1) 100%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f2f6f8', endColorstr='#e0eff9',GradientType=0 ); /* IE6-9 */


		}
		.ok_box{
			-webkit-border-radius: 8px;
			-moz-border-radius: 8px;
			border-radius: 8px;
			
			-webkit-box-shadow: #000 2px 2px 2px;
			-moz-box-shadow: #000 2px 2px 2px;
			box-shadow: #000 2px 2px 2px;
			
			border: 3px solid #FFF;
			font-size:26px;
			color:#FFF;			
			font-weight: bold;
			
			background: rgb(125,185,232); /* Old browsers */
			background: -moz-linear-gradient(top,  rgba(125,185,232,1) 0%, rgba(41,137,216,1) 38%, rgba(41,137,216,1) 38%, rgba(32,124,202,1) 40%, rgba(32,124,202,1) 40%, rgba(30,87,153,1) 96%, rgba(30,87,153,1) 96%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(125,185,232,1)), color-stop(38%,rgba(41,137,216,1)), color-stop(38%,rgba(41,137,216,1)), color-stop(40%,rgba(32,124,202,1)), color-stop(40%,rgba(32,124,202,1)), color-stop(96%,rgba(30,87,153,1)), color-stop(96%,rgba(30,87,153,1))); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  rgba(125,185,232,1) 0%,rgba(41,137,216,1) 38%,rgba(41,137,216,1) 38%,rgba(32,124,202,1) 40%,rgba(32,124,202,1) 40%,rgba(30,87,153,1) 96%,rgba(30,87,153,1) 96%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  rgba(125,185,232,1) 0%,rgba(41,137,216,1) 38%,rgba(41,137,216,1) 38%,rgba(32,124,202,1) 40%,rgba(32,124,202,1) 40%,rgba(30,87,153,1) 96%,rgba(30,87,153,1) 96%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  rgba(125,185,232,1) 0%,rgba(41,137,216,1) 38%,rgba(41,137,216,1) 38%,rgba(32,124,202,1) 40%,rgba(32,124,202,1) 40%,rgba(30,87,153,1) 96%,rgba(30,87,153,1) 96%); /* IE10+ */
			background: linear-gradient(to bottom,  rgba(125,185,232,1) 0%,rgba(41,137,216,1) 38%,rgba(41,137,216,1) 38%,rgba(32,124,202,1) 40%,rgba(32,124,202,1) 40%,rgba(30,87,153,1) 96%,rgba(30,87,153,1) 96%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#7db9e8', endColorstr='#1e5799',GradientType=0 ); /* IE6-9 */


		}
	</style>
</Head>
<body>
 	<!--==================================================
    遮罩內容
    ====================================================== -->
    <div id="cover" style="position:absolute; top:0px; left:0px;">
       <!-- 改過 -->
	<div onClick="" style="position:absolute; top:0px; left:0px; height:480px; width:955px; cursor:wait; background-color:#000; opacity:0.7; z-index:9999"></div>
        <table width="385"   border="0" cellspacing="0"  style="position:absolute; top:181px; left:318px;   text-align: center; z-index:10000;">
        	<tr height="90">
            	<td width="385" align="center" valign="center" id="cover_text" style="" class="cover_box" >正在讀取中請稍後...
                
                </td>
            </tr>
            <tr height="40">
            	<td>
                        <div id="cover_btn_0" onClick="close_cover(2)" style="position:absolute; left:1px; width:110px; height:38px; text-align: center; z-index:10003; display:none; cursor:pointer;" class="ok_box">存檔</div>
                        <div id="cover_btn_1" onClick="close_cover(1)" style="position:absolute; left:141px; width:110px; height:38px; text-align: center; z-index:10001; display:none; cursor:pointer;" class="ok_box">確定</div>
                        <div id="cover_btn_2" onClick="close_cover(0)" style="position:absolute; left:286px; width:110px; height:38px; text-align: center; z-index:10002; display:none; cursor:pointer;" class="no_box">取消</div>

                 </td>
            </tr>
        </table></div>
	
	<!--==================================================
    html內容
    ====================================================== -->
    <!-- 背景 -->
   <!-- 改過 -->

	<div style="position:absolute; top:0px; left:0px; width:955px; height:480px; background-color:#56ffd6; z-index:0;"></div>
	<!-- 內頁內容 -->
	  <!-- 改過 -->
    <iframe id="" src="./page_find_book/index.php" width="955" height="480" scrolling="no" frameborder="0" style="position:absolute; top:0px; left:0px;overflow:hidden;z-index:1;"></iframe>
    
    <!--==================================================
    debug內容
    ====================================================== -->
	<div id="debug" style="position:absolute;top:480px;"></div>
    
</body>
	
    
	<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var tittle = "op";
	//使用著資料
	var user_id = "<?PHP echo $user_id;?>";
	var user_name = "";
	
	var user_sex = "";
	var user_permission = "<?PHP echo $permission;?>";
	var user_personnel ="";
	
	var user_class_code ="";
	var user_school = "";
	var user_school_category ="";
	var user_grade ="";
	var user_class ="";

	var isbn13 = "";
	var isbn10 = "";
	var book_id = "";
	var book_sid = "";
	var auth_coin_open ="yes";
	var borrow_sid = "";
	//書籍資料
	var book_info = new Array();
	var book_choose = -1;
	
	var cover_level = 0;
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	
	//close_cover
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
			
			window.document.getElementById("cover_btn_1").style.left = "140px";
			window.document.getElementById("cover_btn_1").style.display = "block";	
			window.document.getElementById("cover_btn_1").className = "no_box";	
			window.document.getElementById("cover_btn_1").innerHTML = "不存檔";	
			window.document.getElementById("cover_btn_2").style.left = "270px";
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
			window.document.getElementById("cover_btn_1").style.left = "30px";
			window.document.getElementById("cover_btn_1").style.display = "block";
			window.document.getElementById("cover_btn_1").className = "ok_box";		
			window.document.getElementById("cover_btn_1").innerHTML = "確定";	
			window.document.getElementById("cover_btn_2").style.left = "240px";
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
			window.document.getElementById("cover_btn_1").style.left = "140px";
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
		if(user_id == 1238)window.document.getElementById("debug").innerHTML = text+"<br>"+window.document.getElementById("debug").innerHTML;
	}
	

	
	
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        
    	
    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    