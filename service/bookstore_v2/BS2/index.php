<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,太空社群
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

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------
   
        
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
		

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
        //GET
		//$user_id        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$SESSION_id        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:0;
		$user_id        =(isset($_GET['uid']))?(int)$_GET['uid']:$_SESSION['uid'];
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
		if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");
	//---------------------------------------------------
	//SQL
	//---------------------------------------------------
?>
<!DOCTYPE HTML>
<Html>
<Head>
	<Title>書店GADNNNN</Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <!-- 掛載 -->
    <script type="text/javascript" src="../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
    <script src="../js/select_thing.js" type="text/javascript"></script>
    <script src="../../../../ac/js/user_log.js"></script>
    <script src="../js/set_bookstore_action_log.js"></script>

    <style>
		@-webkit-keyframes rotate{
		from{-webkit-transform:rotate(0deg)}
		to{-webkit-transform:rotate(360deg)}
		}
		
		
		 
		.tupain{
		background-repeat: no-repeat;
		animation: 9.5s linear 0s normal none infinite rotate;
		-webkit-animation:9.5s linear 0s normal none infinite rotate;
	
		position: absolute;
		top: 74px;
		
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
  			font-family: Microsoft JhengHei;
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
            font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif,Microsoft JhengHei;
            }

			 /*中文特效用*/
            .world_bar
            {
            font-weight:bold;
            color:#FCFFFF;
            letter-spacing:0pt;

            font-size:22px;
            text-align:left;
            font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif,Microsoft JhengHei;
            }
			.world_bar2
            {
  


            font-weight:bold;
			text-align:left;
			white-space:nowrap;
			overflow:hidden;
            color:#FCFFFF;
            letter-spacing:0pt;

            font-size:18px;
            text-align:left;
            font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif,Microsoft JhengHei;
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
		.star
		{
			-webkit-transition-duration: 1s; /* Safari */
   			transition-duration: 1s;
			
			
		}
		#left,#right{
			position:absolute;
			width:100px;
			height:100px;
			background: url('../img/gr_btn_list.png') -400px 0;
		}
		#left:hover,#right:hover {
    		background: url('../img/gr_btn_list.png') -400px -100px;
		}
		
		/*----------------------------- 按鈕設定----------------------------------列 */
		.sky_boll_green{
			position:absolute;
			width:180px;
			height:170px;
			background: url('./img/space_btn_list.png') 0 0;
		}
		.sky_boll_green:hover {
    		background: url('./img/space_btn_list.png') 0 -170px;
		}
		.sky_class{
			position:absolute;
			width:180px;
			height:170px;
			background: url('./img/space_btn_list.png') -180px 0;
		}
		.sky_class:hover {
    		background: url('./img/space_btn_list.png') -180px -170px;
		}
		.sky_boll_blue{
			position:absolute;
			width:180px;
			height:170px;
			background: url('./img/space_btn_list.png') -360px 0;
		}
		.sky_boll_blue:hover {
    		background: url('./img/space_btn_list.png') -360px -170px;
		}
		.sky_boll_pink{
			position:absolute;
			width:180px;
			height:170px;
			background: url('./img/space_btn_list.png') -540px 0;
		}
		.sky_boll_pink:hover {
    		background: url('./img/space_btn_list.png') -540px -170px;
		}
		.sky_boll_brown{
			position:absolute;
			width:180px;
			height:170px;
			background: url('./img/space_btn_list.png') -720px 0;
		}
		.sky_boll_brown:hover {
    		background: url('./img/space_btn_list.png') -720px -170px;
		}
		.sky_boll_purple{
			position:absolute;
			width:180px;
			height:170px;
			background: url('./img/space_btn_list.png') -900px 0;
		}
		.sky_boll_purple:hover {
    		background: url('./img/space_btn_list.png') -900px -170px;
		}
		.sky_group{
			position:absolute;
			width:180px;
			height:170px;
			background: url('./img/space_btn_list.png') -1080px 0;
		}
		.sky_group:hover {
    		background: url('./img/space_btn_list.png') -1080px -170px;
		}
		.sky_grade{
			position:absolute;
			width:180px;
			height:170px;
			background: url('./img/space_btn_list.png') -1260px 0;
		}
		.sky_grade:hover {
    		background: url('./img/space_btn_list.png') -1260px -170px;
		}
		.sky_boll_yellow{
			position:absolute;
			width:180px;
			height:170px;
			background: url('./img/space_btn_list.png') -1440px 0;
		}
		.sky_boll_yellow:hover {
    		background: url('./img/space_btn_list.png') -1440px -170px;
		}
		.sky_school{
			position:absolute;
			width:180px;
			height:170px;
			background: url('./img/space_btn_list.png') -1620px 0;
		}
		.sky_school:hover {
    		background: url('./img/space_btn_list.png') -1620px -170px;
		}
		/*  ----------------------------------小型按鈕列------------------------------------------- */
		.sky_boll_green_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') 0 0;
		}
		.sky_boll_green_s:hover {
    		background: url('./img/space_btn_list_s.png') 0 -68px;
		}
		.sky_class_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -72px 0;
		}
		.sky_class_s:hover {
    		background: url('./img/space_btn_list_s.png') -72px -68px;
		}
		.sky_boll_blue_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -144px 0;
		}
		.sky_boll_blue_s:hover {
    		background: url('./img/space_btn_list_s.png') -144px -68px;
		}
		.sky_boll_pink_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -216px 0;
		}
		.sky_boll_pink_s:hover {
    		background: url('./img/space_btn_list_s.png') -216px -68px;
		}
		.sky_boll_brown_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -288px 0;
		}
		.sky_boll_brown_s:hover {
    		background: url('./img/space_btn_list_s.png') -288px -68px;
		}
		.sky_boll_purple_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -360px 0;
		}
		.sky_boll_purple_s:hover {
    		background: url('./img/space_btn_list_s.png') -360px -68px;
		}
		.sky_group_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -432px 0;
		}
		.sky_group_s:hover {
    		background: url('./img/space_btn_list_s.png') -432px -68px;
		}
		.sky_grade_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -504px 0;
		}
		.sky_grade_s:hover {
    		background: url('./img/space_btn_list_s.png') -504px -68px;
		}
		.sky_boll_yellow_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -576px 0;
		}
		.sky_boll_yellow_s:hover {
    		background: url('./img/space_btn_list_s.png') -576px -68px;
		}
		.sky_school_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -648px 0;
		}
		.sky_school_s:hover {
    		background: url('./img/space_btn_list_s.png') -648px -68px;
		}
		.sky_boll_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -720px 0;
		}
		.sky_boll_s:hover {
    		background: url('./img/space_btn_list_s.png') -720px -68px;
		}
		/* 新太空*/
		#home{
			position:absolute;
			width:131px;
			height:122px;
			background: url('img/bi1.png') -131px -0px;
			
		}
		#home:hover {
			background: url('img/bi1.png') -131px -122px;
		}
		#select_cs{
			position:absolute;
			width:134px;
			height:122px;
			background: url('img/bi1_2.png') 0px -0px;
			
		}
		#select_cs:hover {
			background: url('img/bi1_2.png') 0px -122px;
		}
		
		@keyframes arrar_move
		{
		0% {
			position:absolute;
			top:0px;
			}
		50% {
			position:absolute;
			top:10px;
			}
		100% {
			position:absolute;
			top:0px;
			}
		}
		@-webkit-keyframes arrar_move 
		{
		0% {
			position:absolute;
			top:0px;
			}
		50% {
			position:absolute;
			top:10px;
			}
		100% {
			position:absolute;
			top:0px;
			}
		}
		.flipy {
			-moz-transform:scaleY(-1);
			-webkit-transform:scaleY(-1);
			-o-transform:scaleY(-1);
			transform:scaleY(-1);
			/*IE*/
			filter:FlipV;
		}
		.flipx {
			-moz-transform:scaleX(-1);
			-webkit-transform:scaleX(-1);
			-o-transform:scaleX(-1);
			transform:scaleX(-1);
			/*IE*/
			filter:FlipH;
		}.flipyx {
			-moz-transform:scale(-1,-1);	
			-webkit-transform:scale(-1,-1);	
			-o-transform:scale(-1,-1);	
			transform:scale(-1,-1);	
			/*IE*/
			
			filter:FlipH,FlipV;
			
		}
		.now_in
		{
	position:absolute;
	top:225px;
	animation: arrar_move 1s;
	-moz-animation: arrar_move 1s;	/* Firefox */
	-webkit-animation: arrar_move 1s;	/* Safari 和 Chrome */
	-o-animation: arrar_move 1s;	/* Opera */
	animation-iteration-count:infinite;
	-webkit-animation-iteration-count:infinite; /* Safari 和 Chrome */
	left: 926px;
		}
		#r,#l{
			position:absolute;
			width:82px;
			height:83px;
			background: url('img/bi4.png') 0 0;
		}
		#r:hover,#l:hover {
			background: url('img/bi4.png') 0 -83px;
		}
		#select_btn{
			position:absolute;
			width:131px;
			height:122px;
			background: url('img/bi1.png') 0 0;
		}
		#select_btn:hover {
			background: url('img/bi1.png') 0 -122px;
		}
		.start_select{
			position:absolute;
			width:124px;
			height:41px;
			background: url('img/bi6.png') -124px 0;
		}
		.start_select:hover {
			background: url('img/bi6.png') -124px -41px;
		}
		.close{
			position:absolute;
			width:124px;
			height:41px;
			background: url('img/bi6.png') 0 0;
		}
		.close:hover {
			background: url('img/bi6.png') 0 -41px;
		}
		.go_select{
			position:absolute;
			width:124px;
			height:41px;
			background: url('img/bi6.png') -372px 0;
		}
		.go_select:hover {
			background: url('img/bi6.png') -372px -41px;
		}
		.close_2{
			position:absolute;
			width:124px;
			height:41px;
			background: url('img/bi6.png') -248px 0;
		}
		.close_2:hover {
			background: url('img/bi6.png') -248px -41px;
		}
	</style>
</Head>
<body>
	<!--==================================================W
    遮罩內容
    ====================================================== -->
<div id="cover" style="position:absolute; top:-8px; left:-8px; display:none;">
    	<div onClick="" style="position:absolute; top:0px; left:0px; height:480px; width:1000px; cursor:wait; background-color:#000; opacity:0.7; z-index:9999;"></div>
		<table width="385"  border="0" cellspacing="0" class="cover_box" style="position:absolute; top:181px; left:318px; height:90px; text-align: center; z-index:10000;">
        	<tr>
            	<td width="385" align="center" valign="center" id="cover_text" style="">正在讀取中請稍後...
                
                </td>
            </tr>
        </table>	  	<div id="cover_btn_1" onClick="close_cover(1)" style="position:absolute; top:283px; left:381px; width:110px; height:38px; text-align: center; z-index:10001; display:none; cursor:pointer;" class="ok_box">確定</div>
        <div id="cover_btn_2" onClick="close_cover(0)" style="position:absolute; top:285px; left:540px; width:110px; height:38px; text-align: center; z-index:10002; display:none; cursor:pointer;" class="no_box">取消</div>
	</div>
<!--==================================================
    html內容
    ====================================================== -->
    <!-- 背景-->
    <img src="img/sky_back.png" style="position:absolute; top:-8px; left:-8px;">
    
    <!-- 選擇內容-->
	<div style="position:absolute; top:-38px; left:-8px; width:1000px; height:520px; overflow:hidden;">
		<div id="star" style="position:absolute; top:0px; left:0px; width:1000px;" class="star"></div>
    </div>
    
    <!-- 複製用 TEXT BAR -->
    <div  name="talk_box"  id="talk_box">
    	<table name="talk_box_table" border="0" cellpadding="0" cellspacing="0" style="position:absolute; top:98px; left:0px; display:none; width: 180px;">
            <tr>
                <td style=" width:12px; height:12px; background-image:url(img/line_box/line_tl.png); background-repeat:no-repeat;">
                    
                </td>
                <td style="height:12px; background-image:url(img/line_box/line_t.png); background-repeat:repeat-x; text-align:center;">
                	<img name="talk_box_u" src="img/line_box/line_uuu.png" style="position:absolute;top:-8px;left:47%;">
                </td>
                <td style="  width:12px; height:12px; background-image:url(img/line_box/line_tr.png); background-repeat:no-repeat;">
                </td>
          </tr>
            <tr>
                <td style="  width:12px; background-image:url(img/line_box/line_l.png)">
                </td>
              	<td id="talk_box_text" style="background-image:url(img/line_box/line_c.png); color:#FFF;  width: 180px;  word-break:break-all;text-align:center;">
              </td>
                <td style="  width:12px; background-image:url(img/line_box/line_r.png); background-repeat:repeat-y;">
                </td>
            </tr>
            <tr>
                <td style="  width:12px; height:12px; background-image:url(img/line_box/line_dl.png); background-repeat:no-repeat;">
                </td>
                <td style="height:12px; background-image:url(img/line_box/line_d.png); position:relative; background-repeat:repeat-x; text-align:center">
                	<img name="talk_box_d" src="img/line_box/line_ddd.png" style="position:absolute;top:0px;left:47%;">
                </td>
                <td style="   width:12px; height:12px; background-image:url(img/line_box/line_dr.png); background-repeat:no-repeat;">
                </td>
            </tr>
      </table>
    </div>
    
    
    <!-- 上下柵欄-->
    <div style="background-color:#001; position:absolute; top:20px; left:962px; width:58px; height:569px;"></div>
	<div style="position:absolute; top:11px; left:12px; background-image:url(img/bi3.png); width:1000px; height:37px;" class="flipy"></div>
    <div style="position:absolute; top:430px; left:12px; background-image:url(img/bi3.png); width:1000px; height:37px;"></div>
    <!-- 說明表格-->
    <div id="select_page" style=" position:absolute; top:-30px; left:0px; width:1000px; height:500px; display:none;">
        <img src="img/bi5.png" style="position:absolute; top:80px; left:131px;" border="0">
        <img src="img/bi5.png" style="position:absolute; top:262px; left:568px;" border="0"class="flipyx">
        <table border="0" cellpadding="0" cellspacing="0" style="position:absolute; top:98px; left:147px;">
            <tr>
                <td style=" width:12px; height:12px; background-image:url(img/line_box/line_tl.png); background-repeat:no-repeat;">
                    
                </td>
                <td style="height:12px; background-image:url(img/line_box/line_t.png)">
                </td>
                <td style="  width:12px; height:12px; background-image:url(img/line_box/line_tr.png); background-repeat:no-repeat;">
                </td>
          </tr>
            <tr>
                <td style="  width:12px; background-image:url(img/line_box/line_l.png)">
                </td>
              	<td style="background-image:url(img/line_box/line_c.png); color:#FFF;  width: 630px; height: 320px; ">
                <!-- ====================================內容框======================================= --> 
                <div style="position:absolute; top:4px; font-size:38px; font-weight:bold; width: 242px;"><u>搜尋功能&nbsp;&nbsp;&nbsp;&nbsp;</u></div>
                <div id="select_page_block_1" style="display:block;">
                    
                    <div style="position:absolute; top:52px; left: 23px; font-size:24px; width: 467px;">輸入想找的人名，即可快速找到對方!!</div>
                    <select id="select_main"  style="position:absolute; top:88px; left: 22px; font-size:20px; width:200px; background-color:#000033; color:#FFF;"> 
                        <option id="select_option" value=""></option>
                    </select>
                    <input id="select_input_text" onFocus="select_input_text_down()" type="text" value="點我輸入姓名" style="position:absolute; top:88px; left:238px; font-size:20px; resize: none; width: 270px; background-color:#000033; color:#77F;"/>
                    <a id="start_select" class="start_select" style="position:absolute; top:280px; left:92px;" onClick="start_select()"></a>
                    <a class="close" style="position:absolute; top:279px; left:457px;" onClick="open_select()"></a>
                </div>
                <div id="select_page_block_2" style="display:none;">
                	<iframe id="select_iframe" src="" style="position:absolute; top:51px; left:20px; width:614px; height:218px;"></iframe>
                	<a id="go_select" class="go_select" style="position:absolute; top:280px; left:92px; display:none;" onClick="go_this_star()"></a>
                    <a class="close_2" style="position:absolute; top:279px; left:457px;" onClick="close_select_page_block_2()"></a>
                </div>
                <!-- ====================================內容框END==================================== -->
              </td>
                <td style="  width:12px; background-image:url(img/line_box/line_r.png); background-repeat:repeat-y;">
                </td>
            </tr>
            <tr>
                <td style="  width:12px; height:12px; background-image:url(img/line_box/line_dl.png); background-repeat:no-repeat;">
                </td>
                <td style="height:12px; background-image:url(img/line_box/line_d.png); background-repeat:repeat-x;">
                </td>
                <td style="   width:12px; height:12px; background-image:url(img/line_box/line_dr.png); background-repeat:no-repeat;">
                    
                </td>
            </tr>
            
      </table>
    </div>
    <!-- ///END說明表格-->
    <!-- 連結文字-->
	<div id="gogo" style="background-image:url(img/line_box/line_c.png); position:absolute; top:-9px; left:-31px; width:1053px; line-height:40px;height:35px; font-size:26px; text-align:center; color:#E1FDFF; font-weight:bold;" ></div>
    <!-- 側邊攔 -->
	<div style="position:absolute; top:8px; left:854px; background-image:url(img/bi2.png); width:145px; height:448px;"></div>
    <div id="s" style="position:absolute; top:0px; left:0;">
    <a id="s0" onClick="set_school_layer()" class="sky_school_s" style="position:absolute; cursor:pointer; top:26px; left:914px;"></a>
    <a id="s1" onClick="set_grade_layer()" class="sky_grade_s" style="position:absolute; cursor:pointer; top:84px; left:910px;"></a>
    <a id="s2" onClick="set_class_layer()" class="sky_class_s" style="position:absolute; cursor:pointer; top:141px; left:920px;"></a>
    <a id="s3" onClick="set_group_layer()" class="sky_group_s" style="position:absolute; cursor:pointer; top:205px; left:914px;"></a>
    <a id="s4" onClick="set_peason_layer()" class="sky_boll_green_s" style="position:absolute; cursor:pointer; top:269px; left:918px;"></a>
    </div>
    <div id="credit_s" style="position:absolute; top:0px; left:0; display:none;">
    <a id="credit_s0" onClick="set_school_layer()" class="sky_school_s" style="position:absolute; cursor:pointer; top:26px; left:914px;"></a>
    <a id="credit_s1" onClick="set_credit_grade_layer()" class="sky_grade_s" style="position:absolute; cursor:pointer; top:84px; left:910px;"></a>
    <a id="credit_s2" onClick="set_credit_class_layer()" class="sky_class_s" style="position:absolute; cursor:pointer; top:141px; left:920px;"></a>
    <a id="credit_s3" onClick="set_credit_group_layer()" class="sky_group_s" style="position:absolute; cursor:pointer; top:205px; left:914px;"></a>
    <a id="credit_s4" onClick="set_credit_peason_layer()" class="sky_boll_green_s" style="position:absolute; cursor:pointer; top:269px; left:918px;"></a>
    </div>
    <a id="home" style="position:absolute; top:333px; left:863px; cursor:pointer;" onClick="set_action_bookstore_log(SESSION_id,'a3',1);out(<? echo $_SESSION['uid'];?>);"></a>
	<a id="select_btn" style="position:absolute; top:333px; left:13px; cursor:pointer;" onClick="open_select()"></a>
    <a id="select_cs" style="position:absolute; top:333px; left:133px; cursor:pointer;" onClick="set_credit_school_info()"></a>
    <div id="onononLA" style="position:absolute; top:38px; left:10px;"><img src="img/arrar.png" class="now_in"></div>
    <!-- 左右紐 -->
    <a onClick="set_mx(-1)" id="r" style="position:absolute; cursor:pointer; top:178px; left:820px;"></a>
    <a onClick="set_mx(1)" id="r" style="position:absolute; cursor:pointer; top:180px; left:0px;" class="flipx"></a>

    <!--==================================================
    debug內容
    ====================================================== -->
<div id="debug" style="position:absolute;top:520px;"></div>
</body>


    <script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var tittle = "st";
	
	var cover_level = 0;
	var main_layer_x = 0;
	var SESSION_id = '<? echo $SESSION_id;?>';
	var user_id = '<? echo $user_id;?>';	
	var user_permission = '<? echo $permission;?>';
	var over_school_view = 'yes';
	var my_over_school_view = 'yes';
	var game_data = new Array();
	game_data["school"] = "";
	game_data["grade"] = "";
	game_data["class"] = "";
	game_data["identity"] = "";
	game_data["semester_code"] = "";
	game_data["group"] = "";
	game_data["category"] = "";
	game_data["school_name"] = "";
	game_data["grade_name"] = "";
	game_data["class_name"] = "";
	game_data["group_name"] = "";
	game_data["class_code"] = "";
	game_data["my_school"] = "";
	game_data["my_semester_code"] = "";
	
	var game_credit_data = new Array();
	game_credit_data["school"] = "";
	game_credit_data["grade"] = "";
	game_credit_data["class"] = "";
	game_credit_data["group"] = "";

	game_credit_data["school_name"] = "";
	game_credit_data["grade_name"] = "";
	game_credit_data["class_name"] = "";
	game_credit_data["group_name"] = "";
		
	//搜尋功能專用
	var list_id = -1;
	var select_user_id = -1;
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	/*cover 啟用器的用法
		 cover("這嘎");
		 cover("這嘎",1);
		 cover("這嘎",2,function(){echo("哈哈");});
	*/
	//cover 點選器
	
	function delayExecute(proc) 
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
		echo("cover_level"+cover_level);
		if(cover_level!=2)
		{
			window.document.getElementById("cover").style.display = "none";
			cover_click = -1;
			cover_level = 0;
		}
		
	}

	
	//cover
	function cover(text,type,proc)
	{
		if(type==1 && cover_level <= 1 )
		{
			window.document.getElementById("cover_btn_1").style.left = "455px";
			window.document.getElementById("cover_btn_1").style.display = "block";	
			window.document.getElementById("cover_btn_2").style.display = "none";	
			cover_level = 1;
			window.document.getElementById("cover_text").innerHTML=text;
			window.document.getElementById("cover").style.display = "block";
		}
		else if(type == 2 && cover_level <= 2)
		{
			window.document.getElementById("cover_btn_1").style.left = "370px";
			window.document.getElementById("cover_btn_1").style.display = "block";	
			window.document.getElementById("cover_btn_2").style.display = "block";	
			cover_level = 2;
			window.document.getElementById("cover_text").innerHTML=text;
			window.document.getElementById("cover").style.display = "block";		
		}
		else if( cover_level <= 0)
		{
			window.document.getElementById("cover_btn_1").style.display = "none";	
			window.document.getElementById("cover_btn_2").style.display = "none";	
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
			delayExecute(proc);
		}	
	}
	//=========MAIN=============
	
	function get_peason_layer_by_uid()
	{
		echo("Main:初始開始:讀取使用者資料");
		cover("讀取使用者資料中");
		

		var url = "./ajax/get_space_user_info.php";
		$.post(url, {
					user_id:user_id,
					SESSION_id:SESSION_id,
					user_permission:user_permission
			}).success(function (data) 
			{
				echo("AJAX:success:get_peason_layer_by_uid():讀取使用者資料:已讀出:"+data);
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
					
					game_data["school"] = data_array["school"];
					game_data["grade"] = data_array["grade"];
					game_data["class"] = data_array["class"];
					game_data["category"] = data_array["category"];
					game_data["identity"] = data_array["identity"];
					game_data["group"] = data_array["group"];
					game_data["semester_code"] = data_array["semester_code"];
					game_data["school_name"] = data_array["school_name"];
					game_data["grade_name"] = data_array["grade_name"];
					game_data["class_name"] = data_array["class_name"];
					game_data["group_name"] = data_array["group_name"];
					game_data["special_sky"] = data_array["special_sky"];
					game_data["class_code"] = data_array["class_code"];
					game_data["my_semester_code"] = data_array["my_semester_code"];
					game_data["my_school"] = data_array["my_school"];
					over_school_view = data_array["over_school_view"];
					my_over_school_view = data_array["my_over_school_view"];
					//if(SESSION_id == 29936)cover(data_array["my_school"]+"<BR>tag"+data_array["my_semester_code"],1);
					game_credit_data["school"] = data_array["credit_school"];
					game_credit_data["grade"] = data_array["credit_grade"];
					game_credit_data["class"] = data_array["credit_class"];
					game_credit_data["group"] = data_array["credit_group"];
				
					game_credit_data["school_name"] = data_array["credit_school_name"];
					game_credit_data["grade_name"] = data_array["credit_grade_name"];
					game_credit_data["class_name"] = data_array["credit_class_name"];
					game_credit_data["group_name"] = data_array["credit_group_name"];
					
					if(game_data["school"]=="")window.document.getElementById("select_btn").style.display = "none";
					window.document.getElementById("select_option").text = game_data["school_name"];
					window.document.getElementById("select_option").value = game_data["school"];
					
					if(game_data["group"]!="" &&(data_array["same_school"] == 1 || (data_array["same_school"] == 0 && (data_array["over_school_view"] == 'yes' || data_array["my_over_school_view"] == 'yes' )) ))
					{			
						set_peason_layer();
					}else if(game_credit_data["group"]!="")
					{
						set_credit_peason_layer();	
					}else
					{
						cover("此星球尚未被分配組別無法進入，請老師分組後可看見星球",1);
						
						set_school_layer();
					}
				}
			}).error(function(e){
				echo("AJAX:error:get_peason_layer_by_uid():讀取使用者資料:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){get_peason_layer_by_uid();});

			}).complete(function(e){
				echo("AJAX:complete:get_peason_layer_by_uid():讀取使用者資料:");
			});
	}
	
	//設定路由
	function set_round(value)
	{
		window.document.getElementById("gogo").innerHTML= "";
		if(value >= 1 )window.document.getElementById("gogo").innerHTML = window.document.getElementById("gogo").innerHTML+game_data["school_name"]+"星系團[校]";
		if(value >= 2 )window.document.getElementById("gogo").innerHTML = window.document.getElementById("gogo").innerHTML+" ► "+game_data["grade_name"]+"星系[年]";
		if(value >= 3 )window.document.getElementById("gogo").innerHTML = window.document.getElementById("gogo").innerHTML+" ► "+game_data["class_name"]+"星團[班]";
		if(value >= 4 )window.document.getElementById("gogo").innerHTML = window.document.getElementById("gogo").innerHTML+" ► "+game_data["group_name"]+"星座[組]";
		
	}
	function set_credit_round(value)
	{
		window.document.getElementById("gogo").innerHTML= "";
		if(value >= 1 )window.document.getElementById("gogo").innerHTML = window.document.getElementById("gogo").innerHTML+game_credit_data["school_name"]+"星系團[校]";
		if(value >= 2 )window.document.getElementById("gogo").innerHTML = window.document.getElementById("gogo").innerHTML+" ► "+game_credit_data["grade_name"]+"星系[年]";
		if(value >= 3 )window.document.getElementById("gogo").innerHTML = window.document.getElementById("gogo").innerHTML+" ► "+game_credit_data["class_name"]+"星團[班]";
		if(value >= 4 )window.document.getElementById("gogo").innerHTML = window.document.getElementById("gogo").innerHTML+" ► "+game_credit_data["group_name"]+"星座[組]";
		
	}
	//===============================================================================
	//畫面設置 : 建立學校層
	//===============================================================================
	function set_school_layer()
	{	
		
		set_round(0);
		set_vx(0);
		set_s_lavel(0);
		
		window.document.getElementById("star").innerHTML = "";
		if(game_data["semester_code"]!=""){
			var url = "./ajax/get_space_stat_info.php";
			$.post(url, {
					school:"",
					grade:"",
					class:"",
					category:"",
					identity:"",
					semester_code:"",
					group:"",
					my_school:game_data["my_school"],
					my_semester_code:game_data["my_semester_code"],
					over_school_view:over_school_view,
					my_over_school_view:my_over_school_view
			}).success(function (data) 
			{
					echo("AJAX:success:set_school_layer():讀取建立班級層:已讀出:"+data);
				
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
						
						var i = 0 ;
						if(my_over_school_view =='yes')
						{////全體開放跨校
							for(i = 0 ; i < data_array["count"] ; i++)
							{
							
								//權開放
								window.document.getElementById("star").innerHTML = window.document.getElementById("star").innerHTML + '<div id="star_'+i+'" onClick="set_school_info(\''+data_array[i]["school_code"]+'\',\''+data_array[i]["semester_code"]+'\',\''+data_array[i]["school_name"]+'\')" style="cursor:pointer; position:absolute; top:'+(120*i%300+30)+'px; left:'+(120*i+40)+'px;"> </div>';
								window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<a class="sky_school" style="position:absolute; top:0px; left:0px;"></a>';
								window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<div style="position:absolute; top:150px; left:12px; width: 236px; height: 33px;" class="world_bar2">'+data_array[i]["school_name"]+'星系團</div>';
								
								if(data_array[i]["school_code"] == game_data["school"])
								{//設定移動位置
									window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML + '<div style="position:absolute; top:40px; left:-840px;"><img src="img/arrar.png" class="now_in"></div>';
									set_vx(-(120*i-280));
								}
							}
							//分支劇情  學分
							window.document.getElementById("star").innerHTML = window.document.getElementById("star").innerHTML + '<div id="star_s" onClick="set_credit_school_info()" style="cursor:pointer; position:absolute; top:'+(120*i%300+30)+'px; left:'+(120*i+40)+'px;"> </div>';
							window.document.getElementById("star_s").innerHTML = window.document.getElementById("star_s").innerHTML +'<a class="sky_school" style="position:absolute; top:0px; left:0px;"></a>';
							window.document.getElementById("star_s").innerHTML = window.document.getElementById("star_s").innerHTML +'<div style="position:absolute; top:150px; left:12px; width: 236px; height: 33px;" class="world_bar2">'+"中央大學學分班"+'星系團</div>';
							
							
							
							
							cover("");
						
						}
						else 
						{//XXX無跨校
						
							//目前只開放自己學校
							window.document.getElementById("star").innerHTML = window.document.getElementById("star").innerHTML + '<div id="star_'+i+'" onClick="set_school_info(\''+game_data["school"]+'\',\''+game_data["semester_code"]+'\',\''+data_array[i]["school_name"]+'\')" style="cursor:pointer; position:absolute; top:'+(120*i%300+30)+'px; left:'+(120*i+40)+'px;"> </div>';
							window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<a class="sky_school" style="position:absolute; top:0px; left:0px;"></a>';
							window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<div style="position:absolute; top:150px; left:12px; width: 236px; height: 33px;" class="world_bar2">'+game_data["school_name"]+'星系團</div>';
							i++;
							//分支劇情  學分
							window.document.getElementById("star").innerHTML = window.document.getElementById("star").innerHTML + '<div id="star_s" onClick="set_credit_school_info()" style="cursor:pointer; position:absolute; top:'+(120*i%300+30)+'px; left:'+(120*i+40)+'px;"> </div>';
							window.document.getElementById("star_s").innerHTML = window.document.getElementById("star_s").innerHTML +'<a class="sky_school" style="position:absolute; top:0px; left:0px;"></a>';
							window.document.getElementById("star_s").innerHTML = window.document.getElementById("star_s").innerHTML +'<div style="position:absolute; top:150px; left:12px; width: 236px; height: 33px;" class="world_bar2">'+"中央大學學分班"+'星系團</div>';
							cover("");
						}
					}
					cover("");
				}).error(function(e){
					echo("AJAX:error:set_school_layer():建立學校層:");
					cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){set_school_layer();});
	
				}).complete(function(e){
					echo("AJAX:complete:set_school_layer():建立學校層:");
				});
		}
		
	}
	function open_cs()
	{
		
	
	}
	function set_school_info(value,value2,value3)
	{
		game_data["school"] = value;
		game_data["semester_code"] = value2;
		game_data["school_name"] = value3;
		set_grade_layer();
	}
	function set_credit_school_info()
	{	
		game_credit_data["school"] = "學分班";
		game_credit_data["school_name"] = "中央大學學分班";

		set_credit_grade_layer();
	}
	//===============================================================================
	//畫面設置 : 建立年級層
	//===============================================================================
	//一般介面
	function set_grade_layer()
	{	
		set_round(1);
		set_vx(0);
		set_s_lavel(1);
		window.document.getElementById("star").innerHTML = "";

		
		var url = "./ajax/get_space_stat_info.php";
		$.post(url, {
				school:game_data["school"],
				grade:"",
				class:"",
				category:game_data["category"],
				identity:game_data["identity"],
				semester_code:game_data["semester_code"],
				group:"",
				class_code:game_data["class_code"],
				my_school:game_data["my_school"],
				my_semester_code:game_data["my_semester_code"],
				over_school_view:over_school_view,
				my_over_school_view:my_over_school_view
		}).success(function (data) 
		{
				echo("AJAX:success:set_grade_layer():讀取建立年級層:已讀出:"+data);
				
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
					return false;		
				}else
				{
					for(var i = 0 ; i < data_array["count"] ; i++)
					{
						window.document.getElementById("star").innerHTML = window.document.getElementById("star").innerHTML + '<div id="star_'+i+'" onClick="set_grade_info(\''+data_array[i]["grade"]+'\',\''+data_array[i]["grade"]+'\')" style="cursor:pointer; position:absolute; top:'+(120*i%300+30)+'px; left:'+(120*i+40)+'px;"> </div>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<a class="sky_grade" style="position:absolute; top:0px; left:0px;"></a>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<div style="position:absolute; top:150px; left:12px; width: 236px; height: 33px;" class="world_bar2">'+data_array[i]["grade"]+'星系</div>';
						if(data_array[i]["grade"] == game_data["grade"])
						{//設定移動位置
							window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML + '<div style="position:absolute; top:40px; left:-840px;"><img src="img/arrar.png" class="now_in"></div>';
							set_vx(-(120*i-320));
						}
						
						
					}
				}
			}).error(function(e){
				echo("AJAX:error:set_grade_layer():讀取建立年級層:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){set_grade_layer();});

			}).complete(function(e){
				echo("AJAX:complete:set_grade_layer():讀取建立年級層:");
			});
	}
	function set_grade_info(value,value2)
	{
		game_data["grade"] = value;
		game_data["grade_name"] = value2;
		
		set_class_layer();
	}
	
	//學分班介面
	function set_credit_grade_layer()
	{	
		set_credit_round(1);
		set_vx(0);
		set_credit_s_lavel(1);
		window.document.getElementById("star").innerHTML = "";

		var url = "./ajax/get_space_credit_stat_info.php";
		$.post(url, {
				school:game_credit_data["school"],
				grade:"",
				class:"",
				group:""
		}).success(function (data) 
		{
				echo("AJAX:success:set_credit_grade_layer():讀取建立年級層:已讀出:"+data);
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
					for(var i = 0 ; i < data_array["count"] ; i++)
					{
						window.document.getElementById("star").innerHTML = window.document.getElementById("star").innerHTML + '<div id="star_'+i+'" onClick="set_credit_grade_info(\''+data_array[i]["grade"]+'\',\''+data_array[i]["grade_name"]+'\')" style="cursor:pointer; position:absolute; top:'+(120*i%300+30)+'px; left:'+(120*i+40)+'px;"> </div>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<a class="sky_grade" style="position:absolute; top:0px; left:0px;"></a>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<div style="position:absolute; top:150px; left:12px; width: 236px; height: 33px;" class="world_bar2">'+data_array[i]["grade_name"]+' 星系</div>';
						if(data_array[i]["grade"] == game_credit_data["grade"])
						{//設定移動位置
							window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML + '<div style="position:absolute; top:40px; left:-840px;"><img src="img/arrar.png" class="now_in"></div>';
							set_vx(-(120*i-320));
						}
					}
				}
			}).error(function(e){
				echo("AJAX:error:set_credit_grade_layer():讀取建立年級層:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){set_credit_grade_layer();});

			}).complete(function(e){
				echo("AJAX:complete:set_credit_grade_layer():讀取建立年級層:");
			});
	}
	function set_credit_grade_info(value,value2)
	{	
		game_credit_data["grade"] = value;
		game_credit_data["grade_name"] = value2;
		
		set_credit_class_layer();
	}
	//===============================================================================
	//畫面設置 : 建立班級層
	//===============================================================================
	//一般介面
	function set_class_layer()
	{	
		set_round(2);
		set_vx(0);
		set_s_lavel(2);
		window.document.getElementById("star").innerHTML = "";

		var url = "./ajax/get_space_stat_info.php";
		$.post(url, {
				school:game_data["school"],
				grade:game_data["grade"],
				class:"",
				category:game_data["category"],
				identity:game_data["identity"],
				semester_code:game_data["semester_code"],
				group:"",
				class_code:game_data["class_code"],
				my_school:game_data["my_school"],
				my_semester_code:game_data["my_semester_code"],
				over_school_view:over_school_view,
				my_over_school_view:my_over_school_view
		}).success(function (data) 
		{
				echo("AJAX:success:set_class_layer():讀取建立班級層:已讀出:"+data);
			
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
					for(var i = 0 ; i < data_array["count"] ; i++)
					{
						window.document.getElementById("star").innerHTML = window.document.getElementById("star").innerHTML + '<div id="star_'+i+'" onClick="set_class_info(\''+data_array[i]["classroom"]+'\',\''+data_array[i]["class_name"]+'\',\''+data_array[i]["class_code"]+'\')" style="cursor:pointer; position:absolute; top:'+(60*i*i%300+50)+'px; left:'+(150*i+20)+'px;"> </div>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<a class="sky_class" style="position:absolute; top:0px; left:0px;"></a>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<div style="position:absolute; top:150px; left:12px; width: 236px; height: 33px;" class="world_bar2">'+data_array[i]["class_name"]+'星團</div>';
						if(data_array[i]["classroom"] == game_data["class"])
						{//設定移動位置
							window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML + '<div style="position:absolute; top:40px; left:-860px;"><img src="img/arrar.png" class="now_in"></div>';
							set_vx(-(150*i-320));
						}
					
					}
				}
			}).error(function(e){
				echo("AJAX:error:set_class_layer():讀取建立班級層:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){set_class_layer();});

			}).complete(function(e){
				echo("AJAX:complete:set_class_layer():讀取建立班級層:");
			});
	}
	function set_class_info(value,value2,value3)
	{
		game_data["class"] = value;
		game_data["class_name"] = value2;
		game_data["class_code"] = value3;
		set_group_layer();
	}
	//學分班介面
	function set_credit_class_layer()
	{	
		set_credit_round(2);
		set_vx(0);
		set_credit_s_lavel(2);
		window.document.getElementById("star").innerHTML = "";
		echo(game_credit_data["grade"]);
		var url = "./ajax/get_space_credit_stat_info.php";
		$.post(url, {
				school:game_credit_data["school"],
				grade:game_credit_data["grade"],
				class:"",
				group:""
		}).success(function (data) 
		{
				echo("AJAX:success:set_credit_class_layer():讀取建立班級層:已讀出:"+data);
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
					for(var i = 0 ; i < data_array["count"] ; i++)
					{
						window.document.getElementById("star").innerHTML = window.document.getElementById("star").innerHTML + '<div id="star_'+i+'" onClick="set_credit_class_info(\''+data_array[i]["class_id"]+'\',\''+data_array[i]["class_name"]+'\')" style="cursor:pointer; position:absolute; top:'+(60*i*i%300+50)+'px; left:'+(150*i+20)+'px;"> </div>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<a class="sky_class" style="position:absolute; top:0px; left:0px;"></a>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<div style="position:absolute; top:150px; left:12px; width: 236px; height: 33px;" class="world_bar2">'+data_array[i]["class_name"]+'星團</div>';
						if(data_array[i]["class_id"] == game_credit_data["class"])
						{//設定移動位置
							window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML + '<div style="position:absolute; top:40px; left:-860px;"><img src="img/arrar.png" class="now_in"></div>';
							set_vx(-(150*i-320));
						}
					}
				}
			}).error(function(e){
				echo("AJAX:error:set_credit_class_layer():讀取建立班級層:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){set_credit_class_layer();});

			}).complete(function(e){
				echo("AJAX:complete:set_credit_class_layer():讀取建立班級層:");
			});
	}
	function set_credit_class_info(value,value2)
	{
		game_credit_data["class"] = value;
		game_credit_data["class_name"] = value2;
		
		set_credit_group_layer();
	}
	//===============================================================================
	//畫面設置 : 建立組別層
	//===============================================================================
	//一般介面
	function set_group_layer()
	{	
		set_round(3);
		set_vx(0);
		set_s_lavel(3);
		window.document.getElementById("star").innerHTML = "";
		
		var url = "./ajax/get_space_stat_info.php";
		$.post(url, {
				school:game_data["school"],
				grade:game_data["grade"],
				class:game_data["class"],
				category:game_data["category"],
				identity:game_data["identity"],
				semester_code:game_data["semester_code"],
				group:"",
				class_code:game_data["class_code"],
				my_school:game_data["my_school"],
				my_semester_code:game_data["my_semester_code"],
				over_school_view:over_school_view,
				my_over_school_view:my_over_school_view
		}).success(function (data) 
		{
				echo("AJAX:success:set_group_layer():讀取建立組別層:已讀出:"+data);
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
					for(var i = 0 ; i < data_array["count"] ; i++)
					{
						window.document.getElementById("star").innerHTML = window.document.getElementById("star").innerHTML + '<div id="star_'+i+'" onClick="set_group_info(\''+data_array[i]["group_sid"]+'\',\''+data_array[i]["group_name"]+'\')" style="cursor:pointer; position:absolute; top:'+((180*i )%300+50)+'px; left:'+(180*i)+'px;"> </div>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<a class="sky_group" style="position:absolute; top:0px; left:0px;"></a>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<div style="position:absolute; top:150px; left:12px; width: 236px; height: 33px;" class="world_bar2">'+data_array[i]["group_name"]+'星座</div>';
						
						if(data_array[i]["group_sid"] == game_data["group"])
						{//設定移動位置
							window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML + '<div style="position:absolute; top:40px; left:-840px;"><img src="img/arrar.png" class="now_in"></div>';
							set_vx(-(180*i-320));
						}
					}
					
				}
			}).error(function(e){
				echo("AJAX:error:set_group_layer():讀取建立組別層:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){set_group_layer();});

			}).complete(function(e){
				echo("AJAX:complete:set_group_layer():讀取建立組別層:");
			});
	}
	function set_group_info(value,value2)
	{
		game_data['group'] = value;
		game_data['group_name'] = value2;
		
		set_peason_layer();
	}
	//學分班介面
	function set_credit_group_layer()
	{	
		set_credit_round(3);
		set_vx(0);
		set_credit_s_lavel(3);
		window.document.getElementById("star").innerHTML = "";
		var url = "./ajax/get_space_credit_stat_info.php";
		$.post(url, {
				school:game_credit_data["school"],
				grade:game_credit_data["grade"],
				class:game_credit_data["class"],
				group:""
		}).success(function (data) 
		{
				echo("AJAX:success:set_group_layer():讀取建立組別層:已讀出:"+data);
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
					for(var i = 0 ; i < data_array["count"] ; i++)
					{
						window.document.getElementById("star").innerHTML = window.document.getElementById("star").innerHTML + '<div id="star_'+i+'" onClick="set_credit_group_info(\''+data_array[i]["group_id"]+'\',\''+data_array[i]["group_name"]+'\')" style="cursor:pointer; position:absolute; top:'+((180*i )%300+50)+'px; left:'+(180*i)+'px;"> </div>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<a class="sky_group" style="position:absolute; top:0px; left:0px;"></a>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<div style="position:absolute; top:150px; left:12px; width: 236px; height: 33px;" class="world_bar2">'+data_array[i]["group_name"]+'星團</div>';
						if(data_array[i]["group_id"] == game_credit_data["group"])
						{//設定移動位置
							window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML + '<div style="position:absolute; top:40px; left:-840px;"><img src="img/arrar.png" class="now_in"></div>';
							set_vx(-(180*i-320));
						}
					}
					
					
				}
		}).error(function(e){
			echo("AJAX:error:set_group_layer():讀取建立組別層:");
			cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){set_group_layer();});

		}).complete(function(e){
			echo("AJAX:complete:set_group_layer():讀取建立組別層:");
		});
			
	}
	function set_credit_group_info(value,value2)
	{
		game_credit_data["group"] = value;
		game_credit_data["group_name"] = value2;
		
		set_credit_peason_layer();
	}
	//===============================================================================
	//畫面設置 : 建立個別層 P
	//===============================================================================
	//一般介面
	function set_peason_layer()
	{	
		set_round(4);
		set_vx(0);
		set_s_lavel(4);
		window.document.getElementById("star").innerHTML = "";
		var url = "./ajax/get_space_stat_info.php";
		$.post(url, {
				school:game_data["school"],
				grade:game_data["grade"],
				class:game_data["class"],
				category:game_data["category"],
				identity:game_data["identity"],
				semester_code:game_data["semester_code"],
				group:game_data["group"],
				class_code:game_data["class_code"],
				over_school_view:over_school_view,
				my_over_school_view:my_over_school_view
		}).success(function (data) 
		{
				echo("AJAX:success:set_peason_layer():讀取建立個別層:已讀出:"+data);
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
					for(var i = 0 ; i <= data_array["count"] ; i++)
					{
						
						window.document.getElementById("star").innerHTML = window.document.getElementById("star").innerHTML + '<div id="star_'+i+'" onClick="out('+data_array[i]["user_id"]+')" style="cursor:pointer; position:absolute; top:'+(90*i%300+20)+'px; left:'+(200*i+80)+'px;"> </div>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<a class="sky_boll_'+data_array[i]["star_style"]+'" style="position:absolute; "></a>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<div style="position:absolute; top:150px; left:12px; width: 236px; height: 33px;" class="world_bar2">'+data_array[i]["user_nickname"]+'的星球</div>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +window.document.getElementById("talk_box").innerHTML;
						if(data_array[i]["parent_id"]!="")
						{		
							window.document.getElementById("star").innerHTML = window.document.getElementById("star").innerHTML +'<a  onClick="out('+data_array[i]["parent_id"]+')"  class="sky_boll_'+data_array[i]["star_style"]+'_s"  style="position:absolute; cursor:pointer; top:'+(90*i%300+140)+'px; left:'+(200*i+213)+'px;"></a>';
						}
						if(data_array[i]["user_id"] == user_id)
						{//設定移動位置
							window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML + '<div style="position:absolute; top:40px; left:-860px;"><img src="img/arrar.png" class="now_in"></div>';
							set_vx(-(200*i-320));
						}
						
						if(data_array[i]["star_declaration"] != "")
						{
							if((90*i%300+20) >= 240 )
							{
								window.document.getElementById("star_"+i).getElementsByTagName("img")[0].style.display = "none";
								window.document.getElementById("star_"+i).getElementsByTagName("td")[4].innerHTML = data_array[i]["star_declaration"];
								window.document.getElementById("star_"+i).getElementsByTagName("table")[0].style.display = "block";
								window.document.getElementById("star_"+i).getElementsByTagName("table")[0].style.top = "-40px";
							}else
							{
								window.document.getElementById("star_"+i).getElementsByTagName("img")[1].style.display = "none";
								window.document.getElementById("star_"+i).getElementsByTagName("td")[4].innerHTML = data_array[i]["star_declaration"];
								window.document.getElementById("star_"+i).getElementsByTagName("table")[0].style.display = "block";
								window.document.getElementById("star_"+i).getElementsByTagName("table")[0].style.top = "180px";
							}
						}
					}
					cover("");
				}
			}).error(function(e){
				echo("AJAX:error:set_peason_layer():讀取建立個別層:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){set_peason_layer();});

			}).complete(function(e){
				echo("AJAX:complete:set_peason_layer():讀取建立個別層:");
			});
	}
	//學分班介面
	function set_credit_peason_layer()
	{	
		set_credit_round(4);
		set_vx(0);
		set_credit_s_lavel(4);
		window.document.getElementById("star").innerHTML = "";
		var url = "./ajax/get_space_credit_stat_info.php";
		
		$.post(url, {
				school:game_credit_data["school"],
				grade:game_credit_data["grade"],
				class:game_credit_data["class"],
				group:game_credit_data["group"]
		}).success(function (data) 
		{
				echo("AJAX:success:set_credit_peason_layer():讀取建立個別層:已讀出:"+data);
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
					for(var i = 0 ; i <= data_array["count"] ; i++)
					{
						
						window.document.getElementById("star").innerHTML = window.document.getElementById("star").innerHTML + '<div id="star_'+i+'" onClick="out('+data_array[i]["user_id"]+')" style="cursor:pointer; position:absolute; top:'+(90*i%300+20)+'px; left:'+(200*i+80)+'px;"> </div>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<a class="sky_boll_'+data_array[i]["star_style"]+'" style="position:absolute; "></a>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +'<div style="position:absolute; top:150px; left:12px; width: 236px; height: 33px;" class="world_bar2">'+data_array[i]["user_nickname"]+'的星球</div>';
						window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML +window.document.getElementById("talk_box").innerHTML;
						if(data_array[i]["parent_id"]!="")
						{		
							window.document.getElementById("star").innerHTML = window.document.getElementById("star").innerHTML +'<a  onClick="out('+data_array[i]["parent_id"]+')"  class="sky_boll_'+data_array[i]["star_style"]+'_s"  style="position:absolute; cursor:pointer; top:'+(90*i%300+140)+'px; left:'+(200*i+213)+'px;"></a>';
						}
						if(data_array[i]["user_id"] == user_id)
						{//設定移動位置
							window.document.getElementById("star_"+i).innerHTML = window.document.getElementById("star_"+i).innerHTML + '<div style="position:absolute; top:40px; left:-860px;"><img src="img/arrar.png" class="now_in"></div>';
							set_vx(-(200*i-320));
						}
						
						if(data_array[i]["star_declaration"] != "")
						{
							if((90*i%300+20) >= 240 )
							{
								window.document.getElementById("star_"+i).getElementsByTagName("img")[0].style.display = "none";
								window.document.getElementById("star_"+i).getElementsByTagName("td")[4].innerHTML = data_array[i]["star_declaration"];
								window.document.getElementById("star_"+i).getElementsByTagName("table")[0].style.display = "block";
								window.document.getElementById("star_"+i).getElementsByTagName("table")[0].style.top = "-40px";
							}else
							{
								window.document.getElementById("star_"+i).getElementsByTagName("img")[1].style.display = "none";
								window.document.getElementById("star_"+i).getElementsByTagName("td")[4].innerHTML = data_array[i]["star_declaration"];
								window.document.getElementById("star_"+i).getElementsByTagName("table")[0].style.display = "block";
								window.document.getElementById("star_"+i).getElementsByTagName("table")[0].style.top = "180px";
							}
						}
					}
					cover("");
				}
			}).error(function(e){
				echo("AJAX:error:set_credit_peason_layer():讀取建立個別層:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){set_credit_peason_layer();});

			}).complete(function(e){
				echo("AJAX:complete:set_credit_peason_layer():讀取建立個別層:");
			});
	}
	function set_s_lavel(value)
	{
		window.document.getElementById("s").style.display = "block";
		window.document.getElementById("credit_s").style.display = "none";
		for(i = 0 ; i <= 4 ; i++)
		{
			if(value >= i)
			{
				window.document.getElementById("s"+i).style.display = "block";
				window.document.getElementById("onononLA").style.top = window.document.getElementById("s"+i).style.top;
			}
			else window.document.getElementById("s"+i).style.display = "none";
		}
	}
	function set_credit_s_lavel(value)
	{
		window.document.getElementById("s").style.display = "none";
		window.document.getElementById("credit_s").style.display = "block";
		for(i = 0 ; i <= 4 ; i++)
		{
			if(value >= i)
			{
				window.document.getElementById("credit_s"+i).style.display = "block";
				window.document.getElementById("onononLA").style.top = window.document.getElementById("s"+i).style.top;
			}
			else window.document.getElementById("credit_s"+i).style.display = "none";
		}
	}
	function set_vx(value)
	{
		main_layer_x=value;
		window.document.getElementById("star").style.left=value+"px";
	}
	function set_mx(value)
	{
		main_layer_x = main_layer_x + value*250;
		window.document.getElementById("star").style.left=main_layer_x+"px";
	}
	function out(value)
	{
		window.location.href ="../bookstore_courtyard/index.php?uid="+value;
	}
	
	//=========================================================================================
	//==============================搜尋系統====================================================\
	//=========================================================================================
	//搜尋開始
	function start_select()
	{   
		set_action_bookstore_log(SESSION_id,'a2',1);//action_log
		window.document.getElementById("select_page_block_1").style.display = 'none';
		var site = document.getElementById("select_main").selectedIndex; 
		var sel_value = document.getElementById("select_main").options[site].value;
		
		window.document.getElementById("select_iframe").src = "manu.php?select="+window.document.getElementById("select_input_text").value+"&school="+sel_value;
		
		window.document.getElementById("select_page_block_2").style.display = 'block';
	}
	function open_select()
	{
		
		
		if(window.document.getElementById("select_page").style.display == "none")
		{
			
			window.document.getElementById("select_page").style.display = "block";
			window.document.getElementById("select_cs").style.display = "none";
		}
		else
		{
			window.document.getElementById("select_page").style.display = "none";
			window.document.getElementById("select_cs").style.display = "block";
		}
	}
	function select_input_text_down()
	{
		//window.document.getElementById("go_select").style.display = 'block';
		window.document.getElementById("select_input_text").style.color = "#FFF";
		window.document.getElementById("select_input_text").value = "";
		window.document.getElementById("select_input_text").onfocus = "";
		
	}
	function close_select_page_block_2()
	{
		window.document.getElementById("select_page_block_2").style.display = 'none';
		window.document.getElementById("select_page_block_1").style.display = 'block';
	}
	function go_this_star()
	{
		
		user_id = select_user_id;
		window.document.getElementById("go_select").style.display = "none";
		window.document.getElementById("select_page").style.display = "none";
		window.document.getElementById("select_page_block_2").style.display = 'none';
		window.document.getElementById("select_page_block_1").style.display = 'block';
		
		list_id = -1;
		select_user_id = -1;
		
		get_peason_layer_by_uid();
	}
	//debug
	function echo(text)
	{
		
		if(0)window.document.getElementById("debug").innerHTML = text+"<br>"+window.document.getElementById("debug").innerHTML;
	}
	
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	set_action_bookstore_log(SESSION_id,'a1',1);//action_log
	get_peason_layer_by_uid();

    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    