g <?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店
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
                    APP_ROOT.'inc/code',
					APP_ROOT.'lib/php/db/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();
		$conn_user=conn($db_type='mysql',$arry_conn_user);

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
	$home_id        =(isset($_GET['uid']))?(int)$_GET['uid']:$_SESSION['uid'];
	$open        =(isset($_GET['open']))?$_GET['open']:"";
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
	<Title>書店</Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <!-- 掛載 -->
    <script type="text/javascript" src="../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
	<script src="js/select_thing.js" type="text/javascript"></script>
    <script src="../../../ac/js/user_log.js"></script>
    
    
    <style>
		html{
		/*cursor : url("img/coin_add.gif"), pointer;
		cursor : url("cur/point.ani"), default;*/
				}
		.flipx {
			-moz-transform:scaleX(-1);
			-webkit-transform:scaleX(-1);
			-o-transform:scaleX(-1);
			transform:scaleX(-1);
			/*IE*/
			filter:FlipH;
		}
        body{
            overflow:hidden;
            position:relative;

            z-index:1;
        }
		 /*數字特效用*/
        .number_bar
        {
            text-shadow:2px 0px 0px rgba(128,23,15,1),
                        0px -2px 0px rgba(128,23,15,1),
                        -2px 0px 0px rgba(128,23,15,1),
                        0px 2px 0px rgba(128,23,15,1),
                        2px 2px 0px rgba(128,23,15,1),
                        2px -2px 0px rgba(128,23,15,1),
                        -2px 2px 0px rgba(128,23,15,1),
                        -2px -2px 0px rgba(128,23,15,1);
            font-weight:bold;
            color:#FCFFFF;
            letter-spacing:0pt;

            font-size:40px;
            text-align:right;
            font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif;
            }

			 /*中文特效用*/
            .world_bar
            {
            text-shadow:2px 0px 1px rgba(0,0,0,1),
                        0px -2px 1px rgba(0,0,0,1),
                        -2px 0px 1px rgba(0,0,0,1),
                        0px 2px 1px rgba(0,0,0,1),
                        2px 2px 1px rgba(0,0,0,1),
                        2px -2px 1px rgba(0,0,0,1),
                        -2px 2px 1px rgba(0,0,0,1),
                        -2px -2px 1px rgba(0,0,0,1)
						,2px 0px 1px rgba(0,0,0,1),
                        0px -2px 1px rgba(0,0,0,1),
                        -2px 0px 1px rgba(0,0,0,1),
                        0px 2px 1px rgba(0,0,0,1),
                        2px 2px 1px rgba(0,0,0,1),
                        2px -2px 1px rgba(0,0,0,1),
                        -2px 2px 1px rgba(0,0,0,1),
                        -2px -2px 1px rgba(0,0,0,1);


            font-weight:bold;
            color:#FCFFFF;
            letter-spacing:0pt;

            font-size:22px;
            text-align:center;
            font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif;
            }
			.world_bar2
            {
            text-shadow:2px 0px 1px rgba(128,23,15,1),
                        0px -2px 1px rgba(128,23,15,1),
                        -2px 0px 1px rgba(128,23,15,1),
                        0px 2px 1px rgba(128,23,15,1),
                        2px 2px 1px rgba(128,23,15,1),
                        2px -2px 1px rgba(128,23,15,1),
                        -2px 2px 1px rgba(128,23,15,1),
                        -2px -2px 1px rgba(128,23,15,1)
						,2px 0px 1px rgba(128,23,15,1),
                        0px -2px 1px rgba(128,23,15,1),
                        -2px 0px 1px rgba(128,23,15,1),
                        0px 2px 1px rgba(128,23,15,1),
                        2px 2px 1px rgba(128,23,15,1),
                        2px -2px 1px rgba(128,23,15,1),
                        -2px 2px 1px rgba(128,23,15,1),
                        -2px -2px 1px rgba(128,23,15,1);


            font-weight:bold;
            color:#FCFFFF;
            letter-spacing:0pt;

            font-size:22px;
            text-align:left;
            font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif;
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
		
		/*按鈕系列*/
		#btn1:hover {
    		background: url('img/btn_list_2.png?v1') 0 -170px;
		}
		#btn1{
			position:absolute;
			width:85px;
			height:85px;
			background: url('img/btn_list_2.png?v1') 0 0;
		}
		
		#btn2{
			position:absolute;
			width:85px;
			height:85px;
			background: url('img/btn_list_2.png?v1') -85px 0;
		}
		#btn2:hover {
    		background: url('img/btn_list_2.png?v1') -85px -170px;
		}
		
		#btn3{
			position:absolute;
			width:85px;
			height:85px;
			background: url('img/btn_list_2.png?v1') -170px 0;
		}
		#btn3:hover {
    		background: url('img/btn_list_2.png?v1') -170px -170px;
		}
		
		#btn4{
			position:absolute;
			width:85px;
			height:85px;
			background: url('img/btn_list_2.png?v1') -255px 0;
		}
		#btn4:hover {
    		background: url('img/btn_list_2.png?v1') -255px -170px;
		}
		
		.btn5{
			position:absolute;
			width:85px;
			height:85px;
			background: url('img/btn_list_2.png?v1') -340px 0;
		}
		.btn5:hover {
    		background: url('img/btn_list_2.png?v1') -340px -170px;
		}
		
		#btn6{
			position:absolute;
			width:85px;
			height:85px;
			background: url('img/btn_list_2.png?v1') -425px 0;
		}
		#btn6:hover {
    		background: url('img/btn_list_2.png?v1') -425px -170px;
		}
		#opinion_btn{
			position:absolute;
			width:183px;
			height:130px;
			background: url('img/package_line.png?v1') 0 0;
		}
		#opinion_btn:hover {
    		background: url('img/package_line.png?v1') 0 -130px;
		}
		#options_btn{
			position:absolute;
			width:85px;
			height:85px;
			background: url('img/btn_list_2.png?v1') -510px 0;
		}
		#options_btn:hover {
    		background: url('img/btn_list_2.png?v1') -510px -170px;
		}
		
	</style>
</Head>
<body bgcolor="#fff0ca">
	<!--==================================================
    遮罩內容
    ====================================================== -->
<div id="cover" style="position:absolute; top:-8px; left:-8px; display:none;">
    	<div onClick="" style="position:absolute; top:0px; left:0px; height:480px; width:1000px; cursor:wait; background-color:#000; opacity:0.7; z-index:9999"></div>
        <div id="cover_text" style="position:absolute; top:181px; left:318px; width:361px; height:90px; text-align: center; z-index:10000;" class="cover_box">正在讀取中請稍後...</div>
        <div id="cover_btn_0" onClick="close_cover(2)" style="position:absolute; top:283px; left:301px; width:110px; height:38px; text-align: center; z-index:10003; display:none; cursor:pointer;" class="ok_box">存檔</div>
        <div id="cover_btn_1" onClick="close_cover(1)" style="position:absolute; top:283px; left:441px; width:110px; height:38px; text-align: center; z-index:10001; display:none; cursor:pointer;" class="ok_box">確定</div>
        <div id="cover_btn_2" onClick="close_cover(0)" style="position:absolute; top:285px; left:536px; width:110px; height:38px; text-align: center; z-index:10002; display:none; cursor:pointer; " class="no_box">取消</div>
	</div>
    
	<!--==================================================
    html內容
    ====================================================== -->
    <!-- 背景 -->
	<div style="position:absolute; top:-36px; left:-10px; width:1000px; height:480px;">
    	<img src="./img/bookstort_back.png?v1" style="position:absolute; top:34px; left:8px;">˙0
        <img id="l_door" src="./img/bookstort_door_line.png?v1" style="position:absolute; top:34px; left:8px;display:none;">
        <!-- 背景層1 -->

        <img id="book_16_img" src="./img/B8.png?v1" style="position:absolute; top:130px; left:142px;display:none;" border="0">
        <img id="book_15_img" src="./img/B1.png?v1" style="position:absolute; top:130px; left:357px;display:none;" border="0">
        <img id="book_14_img" src="./img/B2.png?v1" style="position:absolute; top:139px; left:336px;display:none;" border="0">
        <img id="book_13_img" src="./img/B3.png?v1" style="position:absolute; top:145px; left:304px;display:none;" border="0">
        <img id="book_11_img" src="./img/A3.png?v1" style="position:absolute; top:164px; left:915px;display:none;" border="0">
        <img id="book_10_img" src="./img/A2.png?v1" style="position:absolute; top:119px; left:915px;display:none;" border="0">
        
        <img id="book_12_img" src="./img/A4.png?v1" style="position:absolute; top:217px; left:914px;display:none;" border="0">
        <img id="book_9_img" src="./img/A1.png?v1" style="position:absolute; top:74px; left:916px;display:none;" border="0">
        
        <!-- 背景層2 -->

        <img id="book_6_img" src="./img/A7.png?v1" style="position:absolute; top:150px; left:677px;display:none;"  border="0">
        <img id="book_5_img" src="./img/A6.png?v1" style="position:absolute; top:119px; left:678px;display:none;"  border="0">
        <img id="book_7_img" src="./img/A8.png?v1" style="position:absolute; top:150px; left:762px;display:none;" border="0">
        <img id="book_8_img" src="./img/A9.png?v1" style="position:absolute; top:83px; left:762px;display:none;" border="0">

     	<img id="r_box" src="./img/bookstort_box_1_line.png?v1" style="position:absolute; top:68px; left:594px;display:none;" class="flipx" border="0">
        
        <img id="book_4_img" src="./img/A5.png?v1" style="position:absolute; top:85px; left:677px;display:none;"class="flipx" border="0">
        <img id="book_1_img" src="./img/A5.png?v1" style="position:absolute; top:85px; left:612px;display:none;"  border="0">
        <img id="book_2_img" src="./img/A6.png?v1" style="position:absolute; top:119px; left:612px;display:none;" border="0">
        <img id="book_3_img" src="./img/A7.png?v1" style="position:absolute; top:151px; left:612px;display:none;" border="0">
        <!-- 背景層3 -->
        <img id="cluck" src="./img/0.png?v1" style="position:absolute; top:121px; left:689px;">
        <!--
        <img id="m_1" src="./img/m_1.png?v1" style="position:absolute; top:284px; left:722px;">
        <img id="m_2" src="./img/m_2.png?v1" style="position:absolute; top:284px; left:722px;">-->
        
        <img id="l_box"  src="./img/bookstort_box_0_line.png?v1"  style="position:absolute; top:164px; left:90px;display:none;" border="0">
        
   		<img id="up_book_1_img" src="./img/book_1.png?v1"  style="position:absolute; top:157px; left:261px;display:none;" border="0"> 
        <img id="up_book_2_img" src="./img/book_1.png?v1"  style="position:absolute; top:157px; left:297px;display:none;" border="0"> 
        <img id="up_book_3_img" src="./img/book_1.png?v1"  style="position:absolute; top:156px; left:333px;display:none;" border="0"> 
        <img id="up_book_4_img" src="./img/book_1.png?v1"  style="position:absolute; top:181px; left:255px;display:none;" border="0"> 
        <img id="up_book_5_img" src="./img/book_1.png?v1"  style="position:absolute; top:183px; left:294px;display:none;" border="0"> 
        <img id="up_book_6_img" src="./img/book_1.png?v1"  style="position:absolute; top:182px; left:331px;display:none;" border="0"> 
        <img id="up_book_7_img" src="./img/book_1.png?v1"  style="position:absolute; top:207px; left:254px;display:none;" border="0"> 
        <img id="up_book_8_img" src="./img/book_1.png?v1"  style="position:absolute; top:207px; left:296px;display:none;" border="0"> 
        <img id="up_book_9_img" src="./img/book_1.png?v1"  style="position:absolute; top:209px; left:329px;display:none;" border="0">
        <img id="up_book_10_img" src="./img/book_1.png?v1"  style="position:absolute; top:233px; left:254px;display:none;" border="0"> 
     	<img id="up_book_11_img" src="./img/book_1.png?v1"  style="position:absolute; top:235px; left:292px;display:none;" border="0"> 
        <img id="up_book_12_img" src="./img/book_1.png?v1"  style="position:absolute; top:233px; left:326px;display:none;" border="0"> 
        <img id="up_book_13_img" src="./img/book_1.png?v1"  style="position:absolute; top:260px; left:253px;display:none;" border="0"> 
        <img id="up_book_14_img" src="./img/book_1.png?v1"  style="position:absolute; top:261px; left:288px;display:none;" border="0"> 
        <img id="up_book_15_img" src="./img/book_1.png?v1"  style="position:absolute; top:259px; left:327px;display:none;" border="0">
        
        <img id="up_book_16_img" src="./img/book_1.png?v1"  style="position:absolute; top:157px; left:148px;display:none;" border="0"> 
        <img id="up_book_17_img" src="./img/book_1.png?v1"  style="position:absolute; top:157px; left:177px;display:none;" border="0"> 
        <img id="up_book_18_img" src="./img/book_1.png?v1"  style="position:absolute; top:156px; left:209px;display:none;" border="0"> 
        <img id="up_book_19_img" src="./img/book_1.png?v1"  style="position:absolute; top:181px; left:140px;display:none;" border="0"> 
        <img id="up_book_20_img" src="./img/book_1.png?v1"  style="position:absolute; top:183px; left:172px;display:none;" border="0"> 
        <img id="up_book_21_img" src="./img/book_1.png?v1"  style="position:absolute; top:182px; left:205px;display:none;" border="0"> 
        <img id="up_book_22_img" src="./img/book_1.png?v1"  style="position:absolute; top:207px; left:135px;display:none;" border="0"> 
        <img id="up_book_23_img" src="./img/book_1.png?v1"  style="position:absolute; top:207px; left:169px;display:none;" border="0"> 
        <img id="up_book_24_img" src="./img/book_1.png?v1"  style="position:absolute; top:209px; left:199px;display:none;" border="0">
        <img id="up_book_25_img" src="./img/book_1.png?v1"  style="position:absolute; top:233px; left:128px;display:none;" border="0"> 
     	<img id="up_book_26_img" src="./img/book_1.png?v1"  style="position:absolute; top:235px; left:166px;display:none;" border="0"> 
        <img id="up_book_27_img" src="./img/book_1.png?v1"  style="position:absolute; top:233px; left:196px;display:none;" border="0"> 
        <img id="up_book_28_img" src="./img/book_1.png?v1"  style="position:absolute; top:260px; left:123px;display:none;" border="0"> 
        <img id="up_book_29_img" src="./img/book_1.png?v1"  style="position:absolute; top:261px; left:158px;display:none;" border="0"> 
        <img id="up_book_30_img" src="./img/book_1.png?v1"  style="position:absolute; top:261px; left:192px;display:none;" border="0">
        <!-- 店長嘴砲層 -->
        <div id = "talk_bar" style="position: absolute; top:184px; left:423px; display:none" >
        	<div id="talk_text" style="position: absolute; top:0px; left:0px; width:246px;word-break:break-all; " class="cover_box">...</div>
        	<img src="./img/tatalk.png?v1"  style="position:absolute; top:-3px; left:236px;" border="0"> 
   	  </div>
   		<!-- 按鈕圖層 -->
   	  <div style="position: absolute; top:382px; left:0px; width:1033px; height:59px;">
        <div style="position: absolute; top:-3px; left:0px; width:1033px; height:55px; background:#F93; opacity:0.85; background-color: #FB984F;"></div>
          <div id="name" style="position:absolute; top:-57px; left:45px; width: 320px; height: 60px; text-align:left; white-space:nowrap; overflow:hidden;" class="number_bar"></div>
          <img id="coin_img" src="./img/coin.png?v1"  style="position:absolute; top:-16px; left:239px; width: 70px;display:none;" border="0">
	    <div id="coin"  style="position:absolute; top:-8px; left:47px; width: 193px; height: 42px;display:none;" class="number_bar">0</div>
   	  	  <a id="btn6" onClick="go_op()" style="position:absolute; top:-21px; left:379px; cursor:pointer;"></a>
          <a id="btn2" onClick="set_page('page_rec_menu')" style="position:absolute; top:-18px; left:470px; cursor:pointer;"></a>
          
          <a id="btn3" onClick="set_page('page_post_menu')" style="position:absolute; top:-19px; left:738px; cursor:pointer;display:none;"></a>
          <a id="btn1" onClick="set_page('page_shelf_menu')" style="position:absolute; top:-19px; left:559px; cursor:pointer;"></a> 
          <a id="btn5" onClick="set_page('page_help')" style="position:absolute; top:-15px; left:900px; cursor:pointer;"></a>
          <a id="options_btn" onClick="set_page('page_options')" width="75"  style="position:absolute; cursor:pointer; top:-16px; left:820px;display:none;" ></a> 
   	    <a id="opinion_btn" onClick="set_page('page_opinion_menu')" border="0"   style="position:absolute; top:-157px; left:409px; cursor:pointer;display:none;" ></a>
          
          
   	  </div>
        <!-- 觸發層 -->
        <div onClick="set_page('page_rec_menu')" onMouseOver="{window.document.getElementById('r_box').style.display = 'block';}" onMouseOut="{window.document.getElementById('r_box').style.display = 'none';}" style="position: absolute; top:71px; left:593px; width:297px; height:204px; cursor:pointer;"></div>
  		<div onClick="set_page('page_shelf_menu')" onMouseOver="{window.document.getElementById('l_box').style.display = 'block';}" onMouseOut="{window.document.getElementById('l_box').style.display = 'none';}"  style="position: absolute; top:169px; left:113px; width:278px; height:149px; cursor:pointer;"></div>
      	<div onClick="go_outside()" style="position: absolute; top:59px; left:6px; width:94px; height:355px; cursor:pointer;"  onMouseOver="{window.document.getElementById('l_door').style.display = 'block';}" onMouseOut="{window.document.getElementById('l_door').style.display = 'none';}" ></div>
        <!-- 追加的錶功能 -->
        <div id="other_ifame" style="position:absolute; left:0px; top:36px;">
        </div>
      
   		
        
</div>
    <a class="btn5" onClick="open_helper(13)" style="position:absolute; top:-6px; left:911px; cursor:pointer;"></a>
	<div id="helper" style="position:absolute; top:-17px; left:-17px; width:1100px; height:520px; display:none; overflow:hidden;"></div>
    <!-- 說明頁面 -->
    <img id="book_store_help" onClick="close_help()" src="./img/book_store_help.png?v1" style="position:absolute; top:-41px; left:-51px; cursor:pointer;" border="0">
	<!-- 內頁內容 -->
    
<div id="iframe" style="position:absolute;top:-8px;left:-8px;"></div>
    
    
    
<!--==================================================
    debug內容
    ====================================================== -->
<div id="debug" style="position:absolute;top:500px;"></div>
</body>


    <script>
	
	
		
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var tittle = "st";
	//顯示用控制值
	var cover_level = 0;
	var open_fun = '<? echo $open;?>';
	var home_id = '<? echo $home_id;?>';
	var user_id = '<? echo $user_id;?>';
	var home_on = 'user';
	var clerk_talk = new Array("你好 歡迎光臨","來買書喔！","推薦做了嗎？<BR>快來做喔","我後面的櫃子可以點喔","要出去了嗎?你可以點大門走出去","外面有我精心布置的花園喔");
	if(home_id != user_id)
	{
		home_on = 'other';
	}
	var user_permission = '<? echo $permission;?>';
	var status = new Array();
	var coin = 0;
	var name = "";
	var sex = 0;
	
	var cover_click = -1;
	//身分列表
	var auth_i_a = 0;
	var auth_i_f = 0;
	var auth_i_s = 0;
	var auth_i_sa = 0;
	var auth_i_t = 0;
	//權限列表
	var auth_open_publish = 1;
	var auth_read_opinion_limit_day = 14;
	var auth_rec_en_input = "yes";
	var auth_rec_draw_open = "yes";
	var auth_coin_open = "yes";
	//暫存列
	var click_book_sid="";//暫存點選用SID
	var click_book_name="";//暫存點選用書名
	var click_book_star_1=0;//暫存點選的分數1
	var click_book_star_2=0;//暫存點選的分數1
	var click_book_star_3=0;//暫存點選的分數1
	//各項翻頁數的紀錄
	var page_list = new Array();
	page_list["rec"] = 1;
	page_list["shelf"] = 1;
	page_list["select_shelf"] = 1;
	page_list["opinion"] = 1;
	page_list["rec_mode"] = 1;
	//進貨專用
	var book_choose = -1;
	var book_info = new Array();
	var borrow_sid = "";
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
			
			window.document.getElementById("cover_btn_1").style.left = "441px";
			window.document.getElementById("cover_btn_1").style.display = "block";	
			window.document.getElementById("cover_btn_1").className = "no_box";	
			window.document.getElementById("cover_btn_1").innerHTML = "不存檔";	
			window.document.getElementById("cover_btn_2").style.left = "576px";
			window.document.getElementById("cover_btn_2").style.display = "block";	
			window.document.getElementById("cover_btn_2").className = "no_box";	
			window.document.getElementById("cover_btn_0").style.left = "301px";
			window.document.getElementById("cover_btn_0").style.display = "block";	
			window.document.getElementById("cover_btn_0").className = "ok_box";	
			window.document.getElementById("cover_btn_0").innerHTML = "存檔";	
			cover_level = 3;
			window.document.getElementById("cover_text").innerHTML=text;
			window.document.getElementById("cover").style.display = "block";		
		
			
		}
		else if(type == 2 && cover_level <= 2)
		{
			window.document.getElementById("cover_btn_1").style.left = "351px";
			window.document.getElementById("cover_btn_1").style.display = "block";
			window.document.getElementById("cover_btn_1").className = "ok_box";		
			window.document.getElementById("cover_btn_1").innerHTML = "確定";	
			window.document.getElementById("cover_btn_2").style.left = "536px";
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
			window.document.getElementById("cover_btn_1").style.left = "440px";
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
	//=========MAIN=============
	function main()
	{
		if(!window.parent.help_cover["bookstore_main_help"])
		{
	
			window.document.getElementById("book_store_help").style.display = "none";
		};
		
		echo("Main:初始開始:讀取使用者資料");
		cover("讀取使用者資料中")
		var url = "./ajax/get_mssr_user_info.php";
		$.post(url, {
					user_id:user_id,
					home_id:home_id,
					user_permission:user_permission
					
			}).success(function (data) 
			{
				echo("AJAX:success:main():讀取使用者資料:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",1);
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
					coin = data_array["user_coin"];
					name = data_array["user_name"];
					window.document.getElementById("name").innerHTML = name+"的書店";
					sex = data_array["user_sex"];
					if(sex == 1)
					{
						window.document.getElementById("cluck").src="./img/man_1.png?v1";
					}
					else
					{
						window.document.getElementById("cluck").src="./img/gril_1.png?v1";
					}
					
					
					
					auth_open_publish = data_array["auth_open_publish"];
					auth_read_opinion_limit_day = data_array["auth_read_opinion_limit_day"];
					auth_rec_en_input = data_array["auth_rec_en_input"];
					auth_rec_draw_open = data_array["auth_rec_draw_open"];
					auth_coin_open = data_array["auth_coin_open"];
					
					auth_i_a = data_array['status']['i_a'];
					auth_i_f = data_array['status']['i_f'];
					auth_i_s = data_array['status']['i_s'];
					auth_i_sa = data_array['status']['i_sa'];
					auth_i_t = data_array['status']['i_t'];
					//設定說話
					if(data_array["clerk_talk"][0] == "" && data_array["clerk_talk"][1] == "" && data_array["clerk_talk"][2] == "" && data_array["clerk_talk"][3] == "" && data_array["clerk_talk"][4] == "")
					{}else
					{
						echo("寫入說話內容");
						clerk_talk = new Array();
						echo(data_array["clerk_talk"]);
						if(data_array["clerk_talk"][0]!="")clerk_talk.push(data_array["clerk_talk"][0]);
						if(data_array["clerk_talk"][1]!="")clerk_talk.push(data_array["clerk_talk"][1]);
						if(data_array["clerk_talk"][2]!="")clerk_talk.push(data_array["clerk_talk"][2]);
						if(data_array["clerk_talk"][3]!="")clerk_talk.push(data_array["clerk_talk"][3]);
						if(data_array["clerk_talk"][4]!="")clerk_talk.push(data_array["clerk_talk"][4]);
					}
					if(auth_coin_open != "all_no")//設定金錢隱藏
					{
						window.document.getElementById("coin").style.display = "block";
						window.document.getElementById("coin_img").style.display = "block";
					}
					set_other_iframe();
					set_coin(0);
					now_i_talking();//說話啊笨蛋
					get_shelf();
					if(open_fun!=""){
						set_page(open_fun);
						close_help();
						}
					
				}
			}).error(function(e){
				echo("AJAX:error:main():讀取使用者資料:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){main();});
			}).complete(function(e){
				echo("AJAX:complete:main():讀取使用者資料:");
			});
	}
	//讀取上架資訊
	function get_shelf()
	{
		echo("get_shelf():初始開始:讀取上架資訊資料");
		var url = "./ajax/get_shelf_count.php";
		$.post(url, {
					user_id:home_id,
					user_permission:user_permission
					
			}).success(function (data) 
			{
				echo("AJAX:success:get_shelf():已讀出:"+data);
				if(data[0]!="{")
				{
					window.alert("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員");
					get_shelf();
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
					set_shelf_img(data_array["shelf_count"]);
					
			
					for(var i = 1 ; i <= 30 ; i++)
					{
						if(i <= data_array["shelf_count"])
						{
							score = Math.round(data_array[i]["score"])-1;
							if(score <= 1) score = 1;
							if(score >=  Math.round(data_array[i]["count"])+2) score = Math.round(data_array[i]["count"])+1;
							window.document.getElementById("up_book_"+i+"_img").src = "./img/book_"+score+".png?v1";
						}
						else
						{
							window.document.getElementById("up_book_"+i+"_img").src = "./img/book_1.png?v1";
						}
					}
					
						
					get_read();
				}
			}).error(function(e){
				echo("AJAX:error:get_shelf():");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){get_shelf();});
			}).complete(function(e){
				echo("AJAX:complete:get_shelf():");
			});
	}
	function set_shelf_img(value)
	{
		for(var i = 1; i<= 30;i++)
		{	
			
			if(value >= i)
			{
		
				window.document.getElementById("up_book_"+i+"_img").style.display = "block";
			}else
			{
				
				window.document.getElementById("up_book_"+i+"_img").style.display = "none";
			}
		}
		
	}
	//讀取閱讀量資訊
	function get_read()
	{
		echo("get_read():初始開讀取閱讀量資訊資料");
		var url = "./ajax/get_read_count.php";
		$.post(url, {
					user_id:home_id,
					user_permission:user_permission
					
			}).success(function (data) 
			{
				echo("AJAX:success:get_read():已讀出:"+data);
				if(data[0]!="{")
				{
					window.alert("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員");
					get_read();
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
					set_read_img(data_array["read_count"]);
					get_opinion();
				}
			}).error(function(e){
				echo("AJAX:error:get_read():");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){get_read();});
			}).complete(function(e){
				echo("AJAX:complete:get_read():");
			});
	}
	function set_read_img(value)
	{
		for(var i = 1; i<= 16;i++)
		{	
			
			if(value >= ((i-1)*30))
			{
				window.document.getElementById("book_"+i+"_img").style.display = "block";
			}else
			{
				window.document.getElementById("book_"+i+"_img").style.display = "none";
			}
		}
		
	}
	//讀取進貨資訊
	function get_opinion()
	{
		echo("get_read():初始開讀取閱讀量資訊資料 天數>"+auth_read_opinion_limit_day);
		var url = "./ajax/get_opinion_has.php";
		$.post(url, {
					user_id:home_id,
					user_permission:user_permission,
					auth_read_opinion_limit_day:auth_read_opinion_limit_day
					
			}).success(function (data) 
			{
				echo("AJAX:success:get_opinion():已讀出:"+data);
				if(data[0]!="{")
				{
					window.alert("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員");
					get_opinion();
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
					set_opinion_img(data_array["opinion_has"]);
					cover("");
				}
			}).error(function(e){
				echo("AJAX:error:get_opinion():");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){get_opinion();});
				
			}).complete(function(e){
				echo("AJAX:complete:get_opinion():");
			});
	}
	function set_opinion_img(value)
	{
		if(value>0)
		{
			window.document.getElementById("opinion_btn").style.display = "block";
		}else
		{
			window.document.getElementById("opinion_btn").style.display = "none";
		}
		
	}
	//設定金錢
	function set_coin(value)
	{
		echo("set_coin(value):設定金錢:value->"+value);
		coin = coin + value;
		coin = Math.floor(coin);
		window.document.getElementById("coin").innerHTML = coin ; 
		cover("");
	}	
	//開啟子功能頁面
	function set_page(value)
	{
		echo("set_page:開啟畫面 value>"+value);
		if(value !="")
		{
			cover("讀取中");
			window.document.getElementById("iframe").innerHTML = '<iframe id="" src="./'+value+'/index.php" width="1000" height="480" scrolling="no" frameborder="0" style=" top:0px; left:0px; overflow:hidden;"></iframe>';
		}
		else
		{
			get_shelf();
			window.document.getElementById("iframe").innerHTML = '';	
		}
	}
	//關閉說明頁面
	function close_help()
	{
		window.parent.help_cover["bookstore_main_help"] = false;
		window.document.getElementById("book_store_help").style.display = "none";
	}
	
	//走出去啊 有意見嗎
	function go_outside()
	{
		echo("go_outside:離家出走");
		window.location.href = "./bookstore_courtyard/index.php?uid="+home_id;
	}
	function go_op()
	{
		
		cover("確定要前往閱讀登記嗎?",2,function(){window.location.href = '../read_the_registration_v2/index.php';});	
	}
	//debug
	function echo(text)
	{
		if(0)window.document.getElementById("debug").innerHTML = text+"<br>"+window.document.getElementById("debug").innerHTML;
	}
	function set_other_iframe()
	{
		if(home_on == "other")
		{
			echo("啟動去別人家的功能");
			window.document.getElementById("other_ifame").innerHTML = window.document.getElementById("other_ifame").innerHTML+'<iframe src="page_other_store_info/index.php?home_id='+home_id+'" frameborder="0" height="300" width="550" style="position:absolute; left:0px; top:0px; width: 551px; height: 117px;"></iframe>';
			
		}else if(home_on =="user")
		{
			echo("啟動自己家的功能");	
			window.document.getElementById("btn3").style.display = "block";
			window.document.getElementById("options_btn").style.display = "block";
			window.document.getElementById("other_ifame").innerHTML = window.document.getElementById("other_ifame").innerHTML+'<iframe src="page_msg_menu/index.php" frameborder="0" height="300" width="550" style="position:absolute; left:0px; top:0px; width: 551px; height: 117px;"></iframe>';
      
		}
		window.document.getElementById("other_ifame").innerHTML = window.document.getElementById("other_ifame").innerHTML+'<iframe name="page_track_menu" src="page_track_menu/index.php" frameborder="0" height="300" width="550" style="position:absolute; left:0px; top:400px; width: 1000px; height: 80px;"></iframe>';
	}
	//交談器
	
	function now_i_talking() 
	{
		var x = 5000;
	/*	var hnd = window.setInterval(function () 
		{
			clerk_talk.length
			window.document.getElementById("talk_text").innerHTML =clerk_talk[Math.floor(Math.random()*clerk_talk.length)];
			if(Math.floor(Math.random()*3))
			{
				window.document.getElementById("talk_bar").style.display = "block";
				window.document.getElementById("m_2").style.display = "none";
			}
			else
			{ 
				window.document.getElementById("talk_bar").style.display = "none";
				window.document.getElementById("m_2").style.display = "block";
			}
		}, x);*/
	}
	//---------------------------------------------------
    //helper
    //---------------------------------------------------
	function open_helper(value)
	{
		window.document.getElementById("helper").innerHTML="<iframe src='page_helper/index.php?id="+value+"' style='position:absolute; top:0px; left:0px; width:1000px; height:590px;  overflow:hidden;' frameborder='0'></iframe>";
		window.document.getElementById("helper").style.display = "block";
	}
	//---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

	/*$(function(){
		//初始化, 禁止滑鼠事件
		$(document).on("mousewheel DOMMouseScroll", function(e){
			e.preventDefault();
			return false;
		}).dblclick(function(e){
			e.preventDefault();
			return false;
		});
		
	});*/
	main();
	
    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    