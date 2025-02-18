<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,閱讀登記 -> 回答問題
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
    <!-- 掛載 -->
    <script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
    <script src="../js/select_thing.js" type="text/javascript"></script>
    <link href="../css/registration_btn.css" rel="stylesheet" type="text/css">
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
		#check_vote_page_btn a.vote_1:hover {
			background: url('img/vote_page_btn.png') 0 -120px;
		}
		#check_vote_page_btn a.vote_1{
			position:absolute;
			width:629px;
			height:120px;
			background: url('img/vote_page_btn.png') 0 0;
		}
		#check_vote_page_btn a.vote_2{
			position:absolute;
			width:629px;
			height:120px;
			background: url('img/vote_page_btn.png') -629px 0;
		}
		#check_vote_page_btn a.vote_3{
			position:absolute;
			width:629px;
			height:120px;
			background: url('img/vote_page_btn.png') -1258px 0;
		}
		#check_vote_page_btn a.vote_4{
			position:absolute;
			width:629px;
			height:120px;
			background: url('img/vote_page_btn.png') -629px -120px;
		}
		#check_vote_page_btn a.vote_5{
			position:absolute;
			width:629px;
			height:120px;
			background: url('img/vote_page_btn.png') -1258px -120px;
		}
		.btn_help_page2:hover {
    		background: url('../img/btn_help2.png') -0px -49px;
		}
		.btn_help_page2:active {
    		background: url('../img/btn_help2.png') -0px -98px;
		}
		.btn_help_page2{
			position:absolute;
			width:148px;
			height:49px;
			background: url('../img/btn_help2.png') -0px 0;
		}
		.btn_help_page2_n{
			position:absolute;
			width:148px;
			height:49px;
			background: url('../img/btn_help2.png') -0px -147px;
		}
		.btn_help_page:hover {
    		background: url('../img/btn_help.png') -0px -49px;
		}
		.btn_help_page:active {
    		background: url('../img/btn_help.png') -0px -98px;
		}
		.btn_help_page{
			position:absolute;
			width:148px;
			height:49px;
			background: url('../img/btn_help.png') -0px 0;
		}
		.btn_help_page_n{
			position:absolute;
			width:148px;
			height:49px;
			background: url('../img/btn_help.png') -0px -147px;
		}
		
		.box_ling2{
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			border-radius: 3px;

			-webkit-box-shadow: #242 1px 1px 3px;
			-moz-box-shadow: #242 1px 1px 3px;
			box-shadow: #242 2px 1px 1px;

			background: rgb(180,227,145); /* Old browsers */
			background: -moz-linear-gradient(top,  rgba(180,227,145,1) 9%, rgba(97,196,25,1) 15%, rgba(180,227,145,1) 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(9%,rgba(180,227,145,1)), color-stop(15%,rgba(97,196,25,1)), color-stop(100%,rgba(180,227,145,1))); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  rgba(180,227,145,1) 9%,rgba(97,196,25,1) 15%,rgba(180,227,145,1) 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  rgba(180,227,145,1) 9%,rgba(97,196,25,1) 15%,rgba(180,227,145,1) 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  rgba(180,227,145,1) 9%,rgba(97,196,25,1) 15%,rgba(180,227,145,1) 100%); /* IE10+ */
			background: linear-gradient(to bottom,  rgba(180,227,145,1) 9%,rgba(97,196,25,1) 15%,rgba(180,227,145,1) 100%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b4e391', endColorstr='#b4e391',GradientType=0 ); /* IE6-9 */

			border: 1px solid #020;
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
			-webkit-transition-duration: 1s; /* Safari */
   			transition-duration: 1s;
		}
		.text
		{
			font-size: 18px;
			font-weight: bold;
			white-space:nowrap;
			overflow:hidden;
			color:#333;
		}
		.btn_off
		{
			-webkit-border-radius: 16px;
			-moz-border-radius: 16px;
			border-radius: 16px;

			-webkit-box-shadow: #242 1px 1px 1px;
			-moz-box-shadow: #242 1px 1px 1px;
			box-shadow: #242 1px 1px 1px;
			background-color:#fff;
			border: 2px solid #3a8c3a;
		}
		.btn_on
		{
			-webkit-border-radius: 16px;
			-moz-border-radius: 16px;
			border-radius: 16px;

			-webkit-box-shadow: #242 1px 1px 1px;
			-moz-box-shadow: #242 1px 1px 1px;
			box-shadow: #242 1px 1px 1px;

			background: rgb(114,170,0); /* Old browsers */
			background: -moz-radial-gradient(center, ellipse cover,  rgba(114,170,0,1) 0%, rgba(114,170,0,1) 25%, rgba(158,203,45,1) 28%, rgba(158,203,45,1) 33%, rgba(158,203,45,1) 33%, rgba(255,255,255,1) 35%); /* FF3.6+ */
			background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,rgba(114,170,0,1)), color-stop(25%,rgba(114,170,0,1)), color-stop(28%,rgba(158,203,45,1)), color-stop(33%,rgba(158,203,45,1)), color-stop(33%,rgba(158,203,45,1)), color-stop(35%,rgba(255,255,255,1))); /* Chrome,Safari4+ */
			background: -webkit-radial-gradient(center, ellipse cover,  rgba(114,170,0,1) 0%,rgba(114,170,0,1) 25%,rgba(158,203,45,1) 28%,rgba(158,203,45,1) 33%,rgba(158,203,45,1) 33%,rgba(255,255,255,1) 35%); /* Chrome10+,Safari5.1+ */
			background: -o-radial-gradient(center, ellipse cover,  rgba(114,170,0,1) 0%,rgba(114,170,0,1) 25%,rgba(158,203,45,1) 28%,rgba(158,203,45,1) 33%,rgba(158,203,45,1) 33%,rgba(255,255,255,1) 35%); /* Opera 12+ */
			background: -ms-radial-gradient(center, ellipse cover,  rgba(114,170,0,1) 0%,rgba(114,170,0,1) 25%,rgba(158,203,45,1) 28%,rgba(158,203,45,1) 33%,rgba(158,203,45,1) 33%,rgba(255,255,255,1) 35%); /* IE10+ */
			background: radial-gradient(ellipse at center,  rgba(114,170,0,1) 0%,rgba(114,170,0,1) 25%,rgba(158,203,45,1) 28%,rgba(158,203,45,1) 33%,rgba(158,203,45,1) 33%,rgba(255,255,255,1) 35%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#72aa00', endColorstr='#ffffff',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */


			border: 2px solid #3a8c3a;
		}
		
	</style>
     <link rel="stylesheet" href="../../bookstore_v2/css/btn.css">
</Head>
<body>

	<!--==================================================
    html內容
    ====================================================== -->
    <div style="position:absolute; top:0px; left:0px; width:1000px; height:480px; background-color:#56ffd6; z-index:;"></div>
    <!--書籍資料顯示-->
    <div style="position:absolute; width:269px; height:330px; top:92px; left:17px;" class="box_ling"></div>
	<div style="position:absolute; height:30px; width:251px; top:344px; left:24px;" class="text">作者:</div>
<div style="position:absolute; height:30px; width:251px; top:367px; left:24px;" class="text">出版社:</div>
    <div style="position:absolute; height:30px; width:251px; top:390px; left:24px;" class="text">捐書者:</div>
    <img id="book_pic" src="./0.png" width="133" height="166" style="position:absolute; top:122px; left:33px; display:none;">
    <div id="text_name" style="position:absolute; height:30px; width:255px; top:298px; left:24px;" class="text"></div>
    <div id="text_author" style="position:absolute; height:30px; width:192px; top:344px; left:85px;" class="text"></div>
	<div id="text_publisher" style="position:absolute; height:30px; width:166px; top:367px; left:112px;" class="text"></div>
    <div id="text_donor" style="position:absolute; height:30px; width:166px; top:390px; left:112px;" class="text"></div>

    <div id="out_b" style="position:absolute; top:402px; left:981px; width:37px; height:46px; background-color:#F8C967"></div>
    <a id="out" class="btn_close" onClick="go_out()" style="position:absolute; top:370px; left:868px; cursor:pointer; display:none"></a>
    <!--回答頁框-->
	<div id="quset_box" style="position:absolute; top:92px; left:299px; height:184px; width:671px; overflow:hidden" class="box_ling">

        <img id="quset_img" src="./img/opin_text_1.png" style="position:absolute; top:3px; left:22px;">
      <div id="btn_list" style="position:absolute; top:63px; left:36px;"></div>
        <a id="yes_btn"  class="btn_5" onClick="go_next_quest()" style="position:absolute; top:137px; left:452px; cursor:pointer; "></a>
        <a id="up_btn"  class="btn_6" onClick="go_from_quest()" style="position:absolute; top:137px; left:51px; cursor:pointer; display:none"></a>
  	<!--特殊輸入框-->
    	<div id="sp" style="position:absolute; top:43px; left:29px; display:none;">
            <img id="sp1" src="img/11.png" onClick="sp_btn(1)" style="position:absolute; top:10px; left:-3px; opacity:0.4;">
            <img id="sp2" src="img/22.png" onClick="sp_btn(2)" style="position:absolute; top:10px; left:120px; opacity:0.4;">
            <img id="sp3" src="img/33.png" onClick="sp_btn(3)" style="position:absolute; top:10px; left:240px; opacity:0.4;">
            <img id="sp4" src="img/44.png" onClick="sp_btn(4)" style="position:absolute; top:10px; left:360px; opacity:0.4;">
            <img id="sp5" src="img/55.png" onClick="sp_btn(5)" style="position:absolute; top:10px; left:480px; opacity:0.4;">

      </div>
	</div>
	<!--確認頁框-->
    <div id="check_box" style="position:absolute; top:490px; left:299px; height:234px; width:671px;" class="box_ling">

    	<img src="./img/opin_text_1_t.png" style="position:absolute; top:5px; left:22px;">
        <img src="./img/opin_text_2_t.png" style="position:absolute; top:40px; left:22px;">
        <img src="./img/opin_text_3_t.png" style="position:absolute; top:75px; left:22px;">
        <img src="./img/opin_text_4_t.png" style="position:absolute; top:110px; left:22px;">
        <img src="./img/opin_text_5_t.png" style="position:absolute; top:145px; left:22px;">
        <img src="./img/opin_text_6_t.png" style="position:absolute; top:180px; left:20px;">
        <input id="chick_book_page" type="text" value="" style="position:absolute; top:178px; left:254px; font-size:32px; resize: none; width: 81px; border: 0;" readonly/>

        <img id="answer_list_1" src="./img/opin_text_1_1.png" style="position:absolute; top:5px; left:245px;">
        <img id="answer_list_2" src="./img/opin_text_2_1.png" style="position:absolute; top:40px; left:245px;">
        <img id="answer_list_3" src="./img/opin_text_3_1.png" style="position:absolute; top:75px; left:245px;">
        <img id="answer_list_4" src="./img/opin_text_4_1.png" style="position:absolute; top:110px; left:245px;">
        <img id="answer_list_5" src="./img/opin_text_5_1.png" style="position:absolute; top:145px; left:245px;">
		
        <a class="btn_9"  onClick="opinion_registration()" style="position:absolute; top:240px; left:0px; cursor:pointer;"></a>
        <a class="btn_8" onClick="set_quset(1)" style="position:absolute; top:240px; left:324px; cursor:pointer;"></a>
    </div>
    <a id="btn_help_page_n" class="btn_help_page_n"  style="position:absolute; top:429px; left:18px; display:none;"></a>
    <a id="btn_help_page" class="btn_help_page" onClick="open_vate_page()" style="position:absolute; top:429px; left:18px; cursor:pointer;display:none;"></a>
    <!--a id="btn_help_page2_n" class="btn_help_page2_n" onClick="open_books_ver_page_page()" style="position:absolute; top:429px; left:168px;display:none;"></a>
    <!--a id="btn_help_page2" class="btn_help_page2" onClick="open_books_ver_page_page()" style="position:absolute; top:429px; left:168px; cursor:pointer;display:none;"></a>
    <!--================================================第六頁特殊輸入頁數功能=============================================================-->
    <!--頁數填寫數值班-->
    <div id="page_input" style="position:absolute; top:0px; left:0px;display:none;">

    	<input id="read_page" type="text" value="" style="position:absolute; top:147px; left:454px; font-size:32px; resize: none; width: 81px;"/>
        <input id="book_max_page" type="text" value="" style="position:absolute; top:147px; left:566px; font-size:32px; resize: none; width: 81px;background-color: transparent; border: 0;" readonly/>
    	<!-- 數字按鈕 -->
        <div style="position:absolute; top:215px; left:324px;">
			<?PHP for($i = 0 ; $i <= 9 ; $i++){?>
            <div onClick="set_read_page(<?PHP echo $i;?>)" style="position:absolute; top:<?PHP echo ((int)($i/5))*48;?>px; left:<?PHP echo ((int)($i%5))*52;?>px; width:40px; height:39px; background-color:#00ca00; font-size:36px; text-align: center; cursor: pointer;" class="box_ling2"><?PHP echo $i;?></div>
            <?PHP } ?>
    	</div>
        <img src="img/re.png" onClick="re_read_page()" style="position:absolute; top:215px; left:588px; cursor:pointer">

    </div>

    <!--填寫頁數的按鈕-->
<div id="check_vote_page_btn" style="position:absolute; top:150px; left:310px; display:none; cursor:pointer;" onClick="open_vate_page()">
        <a id="check_vote_page_btn_a" class="vote_1"></a>
        <div id="my_vote_page" style="position:absolute; top:14px; left:359px; font-size:32px;"></div>
    </div>
<div id="check_vote_page_btn_list"  style="position:absolute;  top:150px; left:310px; display:none;">
    	<a onClick="tk(1)" class="btn_4" style=" position:absolute; top:130px; left:80px; cursor:pointer"></a>
        <a onClick="tk(0)"  class="btn_3" style=" position:absolute; top:130px; left:380px; cursor:pointer"></a>
    </div>


	<!-- 填寫頁數的頁面 -->
	<div id="vote_bookpage_page" style="position:absolute; top:0px; left:0px; display:none;">
        <div onClick="" style="position:absolute; top:0px; left:0px; height:480px; width:1000px; cursor:wait; background-color:#000; opacity:0.7;"></div>
    	<img src="img/vote_page_bar.png" style="position:absolute; top:79px; left:265px;">

        <!-- 文字藍 -->
        <input id="borrow_page_input_text" type="text" value="" style="position:absolute; top:137px; left:405px; font-size:32px; resize: none; width: 136px;"/>
    
        <!-- 輸入小鍵盤 -->
        <div style="position:absolute; top:214px; left:363px;">
        	<div style="position:absolute; top:-13px; left:-27px; width:303px; height:183px; background-color:#aaffaa;" class="box_ling"></div>
            <!-- 數字按鈕 -->
            <?PHP for($i = 0 ; $i <= 9 ; $i++){?>
      		<div onClick="input_number(<?PHP echo $i;?>)" style="position:absolute; top:<?PHP echo ((int)($i/5))*48;?>px; left:<?PHP echo ((int)($i%5))*52;?>px; width:40px; height:39px; background-color:#00ca00; font-size:36px; text-align: center; cursor: pointer;" class="box_ling2"><?PHP echo $i;?></div>
            <?PHP } ?>

            <!-- 確認取消按鈕 -->
            <div onClick="set_vote()" style="position:absolute; top:127px; left:-14px; width:110px; height:38px; text-align: center; cursor:pointer;" class="ok_box">確定</div>
            <div onClick="out()" style="position:absolute; top:130px; left:155px; width:110px; height:38px; text-align: center; cursor:pointer;" class="no_box">取消</div>
   	  	</div>
	</div>
    
    <!-- 協助書籍分類 -->
	<div id="books_ver_page" style="position:absolute; top:0px; left:0px; display:none;">
        <div onClick="" style="position:absolute; top:0px; left:0px; height:480px; width:1000px; cursor:wait; background-color:#000; opacity:0.7;"></div>
    	
        <div id="books_ver_check_box" style="position:absolute; top:89px; left:193px; height:234px; width:671px;" class="box_ling">
  		<!-- 內容 -->
        <!-- 內容 -->
        </div>
                
            <!-- 確認取消按鈕 -->
  		<div onClick="set_books_ver_check_box()" style="position:absolute; top:268px; left:411px; width:110px; height:38px; text-align: center; cursor:pointer;" class="ok_box">確定</div>
        <div onClick="outt()" style="position:absolute; top:271px; left:580px; width:110px; height:38px; text-align: center; cursor:pointer;" class="no_box">取消</div>
	</div>

<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var quset_answer_data = new Array();
	var answer_list = new Array(0,0,0,0,0,0,0);
	var quest_on = -1;
	var ver_on_list = new Array();
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	function open_books_ver_page_page()
	{
		window.document.getElementById("books_ver_page").style.display = "block";
	}
	function outt()
	{
		window.document.getElementById("books_ver_page").style.display = "none";
		get_books_ver();
	}
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

	//初始化  回答問題
	function set_new_quset()
	{
		answer_list = new Array(0,0,0,0,0,0,0);
		quest_on = -1;//現在的題目
		echo("set_new_quset:開始");
		cover("");
		set_quset(1);
	}
	//特殊按鈕
	function sp_btn(value)
	{
		echo("點選答案=>"+value);
		answer_list[quest_on] = value;
		for(var i = 1 ; i <= 5 ; i++ )
		{
			window.document.getElementById("sp"+i).style.opacity = 0.4;
		}

		if(value != 0)
		{
			window.document.getElementById("sp"+value).style.opacity = 1;
		}
		window.document.getElementById("yes_btn").style.display = "block";

		window.document.getElementById("answer_list_"+quest_on).src = "./img/opin_text_"+quest_on+"_"+value+".png";

	}
	//按下選擇按鈕
	function chick_ans_btn(value)
	{
		echo("點選答案=>"+value);
		answer_list[quest_on] = value;
		for(var i = 1 ; i <= quset_answer_data['answer_count'][quest_on]; i ++)
		{
			window.document.getElementById("btn_list_"+i).className = "btn_off";
		}
		window.document.getElementById("btn_list_"+value).className = "btn_on";
		window.document.getElementById("answer_list_"+quest_on).src = "./img/opin_text_"+quest_on+"_"+value+".png";
		window.document.getElementById("yes_btn").style.display = "block";
	}
	//按下上一題按鈕
	function go_from_quest()
	{
		quest_on--;

		set_quset(quest_on);
	}
	//按下下一題按鈕
	function go_next_quest()
	{

		if(quest_on == 6)
		{//第六題額外處裡
			if(window.document.getElementById("book_max_page").value != "- - ")
			{
				if(Math.floor(window.document.getElementById("read_page").value) > Math.floor(window.document.getElementById("book_max_page").value))
				{
				cover("填入的頁數錯誤了",1);
				return false;
				}
			}

			if(Math.floor(window.document.getElementById("read_page").value) <= 4000 && Math.floor(window.document.getElementById("read_page").value) >= 1 )
			{
				answer_list[6] = window.document.getElementById("read_page").value;
				window.document.getElementById("page_input").style.display = "none";
				window.document.getElementById("check_vote_page_btn").style.display = "none";
				window.document.getElementById("chick_book_page").value = window.document.getElementById("read_page").value;
			}else
			{
				cover("填入的頁數錯誤了喔!!",1);
				return false;
			}
		}

		quest_on++;


		/*if(quest_on == 7 )
		{
			window.document.getElementById("yes_btn").style.display = "none";
			window.document.getElementById("up_btn").style.display = "none";
			window.document.getElementById("quset_img").src = "./img/opin_text_7.png";
			sp_7_set_quset();

			return false;
		}*/


		if(quset_answer_data["quest_count"] < quest_on)
		{
			set_finish_page();
			return false;
		}else
		{
			set_quset(quest_on);
		}
	}

	//設定完成頁面
	function set_finish_page()
	{
		//顯示初始化
		window.document.getElementById("yes_btn").style.display = "none";
		window.document.getElementById("up_btn").style.display = "none";
		window.document.getElementById("btn_list").style.display = "none";
		//移動顯示框
		window.document.getElementById("quset_box").style.top = "-400px";
		window.document.getElementById("check_box").style.top = "92px";
	}

	//開啟投票頁
	function open_vate_page()
	{
		//if(window.document.getElementById("check_vote_page_btn_a").className == "vote_1")
		window.document.getElementById("vote_bookpage_page").style.display = "block";
		window.document.getElementById("borrow_page_input_text").focus();
	}
	//投票箱的按鈕數字
	function input_number(value)
	{
		window.document.getElementById("borrow_page_input_text").value = window.document.getElementById("borrow_page_input_text").value+value;
		window.document.getElementById("borrow_page_input_text").focus();
	}
	//離開投票頁
	function out()
	{
		window.document.getElementById("vote_bookpage_page").style.display = "none";

	}
	//投票送出
	function set_vote()
	{//驗證
		var nuber = Math.floor(window.document.getElementById("borrow_page_input_text").value);

		if(nuber <= 0 || nuber >= 4000)
		{
			cover("亂輸入是不行的喔!!",1);
			return false;
		}

		cover("儲存中")
		var url = "./ajax/set_user_vote_book_page.php";
		$.post(url, {
					user_id:window.parent.user_id,
					user_permission:window.parent.user_permission,
					book_sid:window.parent.book_info[window.parent.book_choose]["book_sid"],
					vote_page:nuber

			}).success(function (data)
			{

				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員",1);
					echo("AJAX:success:set_vote():資料庫發生問題");
					return false;
				}

				data_array = JSON.parse(data);
				echo("AJAX:success:set_vote():已讀出:"+data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					cover(data_array["echo"],1);
					//set_vote();

				}else
				{
					if(data_array["bad_count"]>=33)
					{//做壞指數大於3

						window.document.getElementById("btn_help_page").style.display = "none";

					}else if(data_array["has_done"]>=1)
					{//有做過
						if(data_array["vote_state"]=="差")
						{
							window.document.getElementById("btn_help_page").style.display = "none";

						}else if(data_array["vote_state"]=="良")
						{
							window.document.getElementById("btn_help_page").style.display = "none";
						}else if(data_array["vote_state"]=="未確認")
						{
							window.document.getElementById("btn_help_page").style.display = "none";
						}
					}
					else
					{
						window.document.getElementById("btn_help_page").style.display = "block";
					}
					window.document.getElementById("btn_help_page_n").style.display = "block";
					echo("AJAX:success:set_vote():完成:存入書籍頁數");
					window.document.getElementById("vote_bookpage_page").style.display = "none";
					cover('');

				}

			}).error(function(e){
				echo("AJAX:error:set_vote():");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",1);
				//get_opinion_qa();
			}).complete(function(e){
				echo("AJAX:complete:set_vote():");
			});


	}
	//
	function tk(value)
	{
		if(value == 0)
		{
			window.document.getElementById("check_vote_page_btn_list").style.display = "none";
			window.document.getElementById("check_vote_page_btn").style.display = "none";
			set_finish_page();
		}
		else if(value == 1)
		{
			open_vate_page();
		}

	}


	//re_read_page() 清空填寫數字
	function set_read_page(value)
	{
		window.document.getElementById("read_page").value = window.document.getElementById("read_page").value+value;
		window.document.getElementById("read_page").focus();
	}
	function re_read_page()
	{
		window.document.getElementById("read_page").value = "";
		window.document.getElementById("read_page").focus();
	}
	//設定題目7  非常特別
	function sp_7_set_quset()
	{
		echo("sp_7_set_quset:讀取頁數填寫資訊+++"+window.parent.book_info[window.parent.book_choose]["book_sid"]);
		cover("讀取中")
		var url = "./ajax/get_user_vote_book_page.php";
		$.post(url, {
					user_id:window.parent.user_id,
					user_permission:window.parent.user_permission,
					book_sid:window.parent.book_info[window.parent.book_choose]["book_sid"]

			}).success(function (data)
			{

				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員",1);
					echo("AJAX:success:sp_7_set_quset():資料庫發生問題");
					return false;
				}

				data_array = JSON.parse(data);
				echo("AJAX:success:sp_7_set_quset():已讀出:"+data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					window.alert(data_array["echo"]);
					main();

				}else
				{
					if(data_array["bad_count"]>=99)
					{//做壞指數大於3
						
						window.document.getElementById("btn_help_page").style.display = "none";
						
					}else if(data_array["has_done"]>=1)
					{//有做過
						if(data_array["vote_state"]=="差")
						{
							window.document.getElementById("btn_help_page").style.display = "none";
							
						}else if(data_array["vote_state"]=="良")
						{
							window.document.getElementById("btn_help_page").style.display = "none";
							
						}else if(data_array["vote_state"]=="未確認")
						{
							window.document.getElementById("btn_help_page").style.display = "none";
							
						}
					}
					else
					{//沒做過
						window.document.getElementById("btn_help_page").style.display = "block";
					}
					window.document.getElementById("btn_help_page_n").style.display = "block";

					echo("AJAX:success:sp_7_set_quset():完成:讀取頁數填寫資訊");
					cover("");
				}

			}).error(function(e){
				echo("AJAX:error:sp_7_set_quset():");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",1);
				//get_opinion_qa();
			}).complete(function(e){
				echo("AJAX:complete:sp_7_set_quset():");
			});

	}

	//設定題目
	function set_quset(value)
	{
		echo("set_quset:開始"+quset_answer_data['answer_count'][value]);
		quest_on = value;

		//顯示初始化
		window.document.getElementById("page_input").style.display = "none";
		window.document.getElementById("yes_btn").style.display = "none";
		window.document.getElementById("btn_list").style.display = "block";
		window.document.getElementById("quset_box").style.top = "92px";
		window.document.getElementById("check_box").style.top = "490px";
		if(quest_on == 1 ) window.document.getElementById("up_btn").style.display = "none";
		else  window.document.getElementById("up_btn").style.display = "block";

		//特殊例外
		if(value == 6)
		{	echo("特殊題目6");
			//sp_7_set_quset();
			window.document.getElementById("page_input").style.display = "block";
			window.document.getElementById("sp").style.display = "none";
			window.document.getElementById("quset_img").src = "./img/opin_text_"+value+".png";
			var height_tmp = 55+(42*4);
			window.document.getElementById("quset_box").style.height = (height_tmp+100)+"px";
			window.document.getElementById("yes_btn").style.top = (height_tmp+15)+"px";
			window.document.getElementById("up_btn").style.top = (height_tmp+15)+"px";
			window.document.getElementById("btn_list").innerHTML = "";
			window.document.getElementById("yes_btn").style.display = "block";
			window.document.getElementById("read_page").focus();
			return false ;
		}
		if(value == 5)
		{
			window.document.getElementById("sp").style.display = "block";
			window.document.getElementById("quset_img").src = "./img/opin_text_"+value+"s.png";
			var height_tmp = 55+(42*quset_answer_data['answer_count'][value]);
			window.document.getElementById("quset_box").style.height = (height_tmp+100)+"px";

			window.document.getElementById("yes_btn").style.top = (height_tmp+15)+"px";
			window.document.getElementById("up_btn").style.top = (height_tmp+15)+"px";
			window.document.getElementById("btn_list").innerHTML = "";
			return false ;
		}



		window.document.getElementById("sp").style.display = "none";
		window.document.getElementById("quset_img").src = "./img/opin_text_"+value+".png";

		//設定框大小
		//0 55px
		//1 95px
		//2 135px
		var height_tmp = 55+(42*quset_answer_data['answer_count'][value]);
		window.document.getElementById("quset_box").style.height = (height_tmp+100)+"px";
		//設定下一步按鈕位置
		window.document.getElementById("yes_btn").style.top = (height_tmp+15)+"px";
		window.document.getElementById("up_btn").style.top = (height_tmp+15)+"px";

		//設置按鈕

		window.document.getElementById("btn_list").innerHTML = "";
		for(var i = 1 ; i <= quset_answer_data['answer_count'][quest_on]; i ++)
		{
			window.document.getElementById("btn_list").innerHTML = window.document.getElementById("btn_list").innerHTML+'<div id="btn_list_'+i+'" onClick="chick_ans_btn('+i+')" style="position:absolute; top:'+(42*i-42)+'px; left:0px; width:30px; height:30px;  cursor:pointer" class="btn_off"></div>';
		}


	}
	
	//送出填寫的
	function set_books_ver_check_box()
	{
		var ver_key = new Array();
		var ver_val = new Array();
		var i = 0;
		for(key in ver_on_list)
		{
			ver_key[i] = key;
			ver_val[i] = ver_on_list[key];
			i++;
		}
		//統計
		var url = "./ajax/set_books_ver.php";
		$.post(url, {
					user_id:window.parent.user_id,
					book_sid:window.parent.book_info[window.parent.book_choose]["book_sid"],
					user_permission:window.parent.user_permission,
					ver_key:JSON.stringify(ver_key),
					ver_val:JSON.stringify(ver_val),

		}).success(function (data)
		{
			console.log(data);
			if(data[0]!="{")
			{
				cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員 cod2",1);
				return false;
			}

			
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
				
				outt();
				cover("");
				
			}
		}).error(function(e){
			echo("AJAX:error:get_opinion_qa():");
			cover("喔喔?! 讀取失敗了喔  請確認網路連",1);
			//get_opinion_qa();
		}).complete(function(e){
			echo("AJAX:complete:get_opinion_qa():");
		});
	}

	//讀取答題的題目與回答
	function get_opinion_qa()
	{
		echo("get_opinion_qa:初始開始");
		cover("讀取題庫中")
		var url = "./ajax/get_opinion_qa.php";
		$.post(url, {
					user_id:window.parent.user_id,
					user_permission:window.parent.user_permission

			}).success(function (data)
			{

				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員 cod2",1);
					echo("AJAX:success:get_opinion_qa():資料庫發生問題");
					return false;
				}

				quset_answer_data = data_array = JSON.parse(data);
				echo("AJAX:success:get_opinion_qa():已讀出:"+data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					window.alert(data_array["echo"]);
					get_opinion_qa();

				}else
				{
					echo("AJAX:success:get_opinion_qa():完成:開始建立題庫");
					set_new_quset();

				}
				sp_7_set_quset();
			}).error(function(e){
				echo("AJAX:error:get_opinion_qa():");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",1);
				//get_opinion_qa();
			}).complete(function(e){
				echo("AJAX:complete:get_opinion_qa():");
			});
	}
	
	//讀取書籍類型
	function get_books_ver()
	{	
		
		if(window.parent.user_school=="")return false;
		
		var url = "./ajax/get_books_ver.php";
		$.post(url, {
					user_id:window.parent.user_id,
					book_sid:window.parent.book_info[window.parent.book_choose]["book_sid"],
					user_permission:window.parent.user_permission,
					user_school:window.parent.user_school

			}).success(function (data)
			{

				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員 cod2",1);
					echo("AJAX:success:get_opinion_qa():資料庫發生問題");
					return false;
				}

				data_array = JSON.parse(data);
				echo("AJAX:success:get_opinion_qa():已讀出:"+data);
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
					
					if(data_array["count_category"]>0)
					{
						
						//window.document.getElementById("btn_help_page2_n").style.display = "block";
						//if(data_array["count_category_user"]==0)window.document.getElementById("btn_help_page2").style.display = "block";
						//else window.document.getElementById("btn_help_page2").style.display = "none";
						//建立書籍選填欄位
						var bvcb = window.document.getElementById("books_ver_check_box");
						bvcb.innerHTML = "<div style='font-weight:bold; font-size:24px;text-decoration:underline;'>請選擇書籍的類型(可複選)</div><BR>";
						
						for(var i = 0; i < data_array["count_category"]; i++)
						{
							var has_click = 0;
							for(var j = 0; j < data_array["count_category_user"]; j++)
							{
								if(data_array["book_category"][i]['cat_id'] == data_array["category_user"][j]['rev_id'])has_click = 1
							}
							if(has_click == 0)
							{
								bvcb.innerHTML += '<div style="float:left; width:100px; height:40px; position:relative;"><div id="books_ver_'+data_array["book_category"][i]['cat_id']+'" onClick="click_books_ver('+data_array["book_category"][i]['cat_id']+')" style="width:30px; height:30px;position:absolute; left:0px;  cursor:pointer; float:left;" class="btn_off"></div><div style="width:100px; position:absolute; left:40px; font-weight:bold; font-size:24px;">'+data_array["book_category"][i]['cat_name']+'</div></div>';
								ver_on_list[data_array["book_category"][i]['cat_id']] = 0;
							}
							else
							{
								bvcb.innerHTML += '<div style="float:left; width:100px; height:40px; position:relative;"><div id="books_ver_'+data_array["book_category"][i]['cat_id']+'" onClick="click_books_ver('+data_array["book_category"][i]['cat_id']+')" style="width:30px; height:30px;position:absolute; left:0px;  cursor:pointer; float:left;" class="btn_on"></div><div style="width:100px; position:absolute; left:40px; font-weight:bold; font-size:24px;">'+data_array["book_category"][i]['cat_name']+'</div></div>';
								ver_on_list[data_array["book_category"][i]['cat_id']] = 1;
							}
						}
					}
				}
				
			}).error(function(e){
			}).complete(function(e){
			});
	}
	function click_books_ver(val)
	{
		if(window.document.getElementById("books_ver_"+val).className == "btn_on")
		{
			ver_on_list[val] = 0;
			window.document.getElementById("books_ver_"+val).className = "btn_off";
		}
		else
		{
			ver_on_list[val] = 1;
			window.document.getElementById("books_ver_"+val).className = "btn_on";
		}
		
	}
	
	function opinion_registration()
	{
		var answer_tmp = new Array();
		var tmp_echo = "";
		for(var i = 1 ; i<=quset_answer_data["quest_count"];i++)
		{

			answer_tmp["quest_ans_"+i] = answer_list[i];
			answer_tmp["quest_topic_id_"+i] = i;
			tmp_echo = tmp_echo+"Q_id = "+answer_tmp["quest_topic_id_"+i]+" => A:"+ answer_tmp["quest_ans_"+i]+" -- ";
		}

		echo("opinion_registration:初始開始");
		echo("回答=>"+tmp_echo);
		echo("啟用的借閱ID=>"+window.parent.borrow_sid);
		echo("啟用的書籍ID=>"+window.parent.book_info[window.parent.book_choose]["book_sid"]);
		cover("儲存中");
		var url = "./ajax/set_opinion_registration.php";
		$.post(url, {  //3跟5調換 日期 12/18
					quest_ans_1:answer_tmp["quest_ans_1"],
					quest_ans_2:answer_tmp["quest_ans_2"],
					quest_ans_3:answer_tmp["quest_ans_5"],
					quest_ans_4:answer_tmp["quest_ans_4"],
					quest_ans_5:answer_tmp["quest_ans_3"],
					quest_ans_6:answer_tmp["quest_ans_6"],
					quest_topic_id_1:answer_tmp["quest_topic_id_1"],
					quest_topic_id_2:answer_tmp["quest_topic_id_2"],
					quest_topic_id_3:answer_tmp["quest_topic_id_3"],
					quest_topic_id_4:answer_tmp["quest_topic_id_4"],
					quest_topic_id_5:answer_tmp["quest_topic_id_5"],
					quest_topic_id_6:answer_tmp["quest_topic_id_6"],
					auth_coin_open:window.parent.auth_coin_open,
					borrow_sid:window.parent.borrow_sid,
					book_sid:window.parent.book_info[window.parent.book_choose]["book_sid"],
					user_id:window.parent.user_id,
					user_permission:window.parent.user_permission
			}).success(function (data)
			{
				echo("AJAX:success:opinion_registration():已讀出:"+data);
				if(data[0]!="{")
				{
					cover(data+"資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員cod3",1);
					echo("AJAX:success:opinion_registration():資料庫發生問題");
					return false;
				}

				quset_answer_data = data_array = JSON.parse(data);
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
					echo("AJAX:success:opinion_registration():完成閱讀登記");

					if(data_array["coin"]>0)
					{
						cover("完成登記了喔<BR>並獲得訂閱獎勵<BR>"+data_array["coin"]+"$",1);
						if(window.parent.tittle =="op")
						document.location.href="../page_finish_registration/index.php";
						if(window.parent.tittle =="st")
						window.parent.set_page("page_opinion_menu");
					}
					else
					{
						cover("完成登記了");
						if(window.parent.tittle =="op")
						document.location.href="../page_finish_registration/index.php";
						if(window.parent.tittle =="st")
						window.parent.set_page("page_opinion_menu");
					}



				}

			}).error(function(e){
				echo("AJAX:error:get_opinion_qa():");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",1);
				//get_opinion_qa();
			}).complete(function(e){
				echo("AJAX:complete:get_opinion_qa():");
			});


	}
	function go_out()
	{
		window.parent.set_page("page_opinion_menu");
	}
	function show_book(value)
	{
		//在書店內展開離開按鈕
		if(window.parent.tittle =="st")
		{
			window.document.getElementById("out_b").style.left = "829px";
			window.document.getElementById("out_b").style.width = "189px";
			window.document.getElementById("out").style.display = "block";
		}

		window.document.getElementById("book_pic").src = window.parent.book_info[value]["src"];
		window.document.getElementById("book_pic").style.display = "block";
		window.document.getElementById("text_name").innerHTML = window.parent.book_info[value]["book_name"];
		window.document.getElementById("text_author").innerHTML = window.parent.book_info[value]["book_author"];
		window.document.getElementById("text_publisher").innerHTML = window.parent.book_info[value]["book_publisher"];
		if(window.parent.book_info[value]["book_donor"])window.document.getElementById("text_donor").innerHTML = window.parent.book_info[value]["book_donor"];
		
		if(window.parent.book_info[value]["book_page_count"]>0)
		{
			window.document.getElementById("book_max_page").value = window.parent.book_info[value]["book_page_count"];
		}else
		{
			window.document.getElementById("book_max_page").value = " ?? ";
		}
		get_books_ver();
		get_opinion_qa();
	}
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        $(function(){
            //初始化, 禁止滑鼠事件
            $(document).on("mousewheel DOMMouseScroll", function(e){
                e.preventDefault();
                return false;
            }).dblclick(function(e){
                e.preventDefault();
                return false;
            });
        });
	show_book(window.parent.book_choose);
    </script>
</Html>














