<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,購買頁面
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
		$user_id        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$home_id        =(isset($_GET['uid']))?(int)$_GET['uid']:$_SESSION['uid'];
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
		$home_on        = $home_id  == $user_id  ? "user" : "other";
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
    <link rel="stylesheet" href="../css/btn.css">
    <script type="text/javascript" src="../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
    <script type="text/javascript" src="../../../lib/jquery/ui/code.js"></script>
	<script type="text/javascript" src="../../../lib/jquery/ui/func/jquery_ui/jquery_ui_touch_punch_0.2.2/code.js"></script>
    <script src="../js/select_thing.js" type="text/javascript"></script>
    <script src="../../../../ac/js/user_log.js"></script>
    <script src="../js/set_bookstore_action_log.js"></script>
    <style>
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

            font-size:22px;
            text-align:right;
            font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif,Microsoft JhengHei;
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
            font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif,Microsoft JhengHei;
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

		.say{-moz-transform:rotate(-8deg);
-webkit-transform:rotate(-8deg);
-o-transform:rotate(-8deg);
-ms-transform:rotate(-8deg);
transform:rotate(-8deg);
font-size:24px;
font-weight:bold;
            color:#512;
}
		/*按鈕系列*/

		#1{
			position:absolute;
			width:100px;
			height:100px;
			background: url('img/gr_btn_list.png') -100px 0;
		}
		#1:hover {
    		background: url('img/gr_btn_list.png') -100px -100px;
		}


		.la{
			position:absolute;
			width:103px;
			height:29px;
			background: url('img/buy3_btn.png') 0 0;
		}
		.la:hover {
    		background: url('img/buy3_btn.png') 0px -58px;
		}
		.la:active {
    		background: url('img/buy3_btn.png') 0px -29px;
		}
		.coin_img{
			position:absolute;
			width:55px;
			height:61px;
			background: url('../img/coin.png ') 0 0;
		}
		.coin_img:hover {
    		background: url('../img/coin.png ') 0 -61px;
		}
	</style>


</Head>
<body>
	<!--==================================================W
    遮罩內容
    ====================================================== -->
<div id="cover" style="position:absolute; top:-8px; left:-8px; display:none">
    	<div onClick="" style="position:absolute; top:0px; left:0px; height:480px; width:1000px; cursor:wait; background-color:#000; opacity:0.7; z-index:9999; "></div>
		<table width="385"  border="0" cellspacing="0" class="cover_box" style="position:absolute; top:181px; left:318px; height:90px; text-align: center; z-index:10000;">
        	<tr>
            	<td width="385" align="center" valign="center" id="cover_text" style="">正在讀取中請稍後...

                </td>
            </tr>
        </table>
        <div id="cover_btn_1" onClick="close_cover(1)" style="position:absolute; top:283px; left:381px; width:110px; height:38px; text-align: center; z-index:10001; display:none; cursor:pointer;" class="ok_box">確定</div>
        <div id="cover_btn_2" onClick="close_cover(0)" style="position:absolute; top:285px; left:540px; width:110px; height:38px; text-align: center; z-index:10002; display:none; cursor:pointer;" class="no_box">取消</div>
	</div>
	<!--==================================================
    html內容
    ====================================================== -->
	<div style="position:absolute; top:-8px; left:-9px; width:1000px; height:480px;">
		<img src="./img/b3.png" style="position:absolute; top:0px; left:0px;">
        <img src="./img/shoper1.png" style="position:absolute; top:101px; left:9px; height: 377px; z-index:99;">

        <img src="./img/say.png" width="170" height="120" style="position:absolute; z-index:100;top:116px; left:6px; width: 144px; height: 81px;">
        <!-- 圖片圖層 -->
      <div style="position: absolute; top:382px; left:0px; width:1033px; heights:59px;">
        <img src="./img/bar.png"  style="position:absolute; top:5px; left:100px;z-index:100;" border="0">
            <img src="./img/tittle.png"  style="position:absolute; top:-372px; left:61px;" border="0">
                        <a id="coin_img" class="coin_img"  style="position:absolute; top:1px; left:85px;z-index:100;" border="0"></a>

        <div id="coin"  style="position:absolute;z-index:100; top:20px; left:142px; width: 121px; height: 25px;" class="number_bar">424</div>
        <div id="talk"  style="position:absolute; z-index:100; top:-257px; left:22px; width: 120px; z-index:100; font-size:20px; text-align:center; height: 42px;" class="say">歡迎光臨！</div>
      </div>
        <img src="../img/back_s.png" style="position:absolute; top:36px; left:367px;">
        <div id="page_text" style="position:absolute; top:394px; left:566px; text-align:center; font-weight:bold; font-size:28px; color:#6C2113; width: 217px; height: 40px;"></div>
        <a id="left" onClick="set_box(-1)" style="display:none; position:absolute; top:390px; left:510px; cursor:pointer;" class=" btn_arrow_l"></a>
        <a id="right" onClick="set_box(1)" style="display:none; position:absolute; top:390px; left:783px; cursor:pointer;" class=" btn_arrow_r"></a>



      	<div id="box_0" style="position:absolute; top:67px; left:401px;display:none;">
        	<img src="./img/t_back.png">
            <div id="coin_0" style="position:absolute; top:37px; left:108px; color:#6C2113; font-size:18px; width: 101px; text-align:right;">2000</div>
            <div id="text_0" style="position:absolute; top:6px; left:88px; color: #4C2113; font-size:18px; width: 151px; text-align: center; font-weight:bold;">綠色大樹果</div>
            <img id="img_0" onClick="buy(0)" src="../bookstore_courtyard/img/0.png" style="position:absolute; top:14px; left:17px; max-height:70px; max-width:70px; cursor:pointer;">
    		<a class="la" onClick="buy(0)" style="position:absolute; top:60px; left:132px; cursor:pointer;"></a>
        </div>

   	  <div id="box_1" style="position:absolute; top:67px; left:691px;display:none;">
        	<img src="./img/t_back.png">
            <div id="coin_1" style="position:absolute; top:37px; left:108px; color:#6C2113; font-size:18px; width: 101px; text-align:right;">2000</div>
       	<div id="text_1" style="position:absolute; top:6px; left:88px; color: #4C2113; font-size:18px; width: 151px; text-align: center; font-weight:bold;">綠色大樹果</div>
            <img id="img_1" onClick="buy(1)" src="../bookstore_courtyard/img/0.png" style="position:absolute; top:14px; left:17px; max-height:70px; max-width:70px; cursor:pointer;">
    		<a class="la" onClick="buy(1)" style="position:absolute; top:60px; left:132px; cursor:pointer;"></a>
      </div>
   	  	<div id="box_2" style="position:absolute; top:173px; left:401px;display:none;">
        	<img src="./img/t_back.png">
          <div id="coin_2" style="position:absolute; top:37px; left:108px; color:#6C2113; font-size:18px; width: 101px; text-align:right;">2000</div>
            <div id="text_2" style="position:absolute; top:6px; left:88px; color: #4C2113; font-size:18px; width: 151px; text-align: center; font-weight:bold;">綠色大樹果</div>
            <img id="img_2" onClick="buy(2)" src="../bookstore_courtyard/img/0.png" style="position:absolute; top:14px; left:17px; max-height:70px; max-width:70px; cursor:pointer;">
    		<a class="la" onClick="buy(2)" style="position:absolute; top:60px; left:132px; cursor:pointer;"></a>
        </div>
     	<div id="box_3" style="position:absolute; top:173px; left:691px;display:none;">
        	<img src="./img/t_back.png">
            <div id="coin_3" style="position:absolute; top:37px; left:108px; color:#6C2113; font-size:18px; width: 101px; text-align:right;">2000</div>
            <div id="text_3" style="position:absolute; top:6px; left:88px; color: #4C2113; font-size:18px; width: 151px; text-align: center; font-weight:bold;">綠色大樹果</div>
            <img id="img_3" onClick="buy(3)" src="../bookstore_courtyard/img/0.png" style="position:absolute; top:14px; left:17px; max-height:70px; max-width:70px; cursor:pointer;">
    		<a class="la" onClick="buy(3)" style="position:absolute; top:60px; left:132px; cursor:pointer;"></a>
        </div>
        <div id="box_4" style="position:absolute; top:283px; left:401px;display:none;">
        	<img src="./img/t_back.png">
            <div id="coin_4" style="position:absolute; top:37px; left:108px; color:#6C2113; font-size:18px; width: 101px; text-align:right;">2000</div>
            <div id="text_4" style="position:absolute; top:6px; left:88px; color: #4C2113; font-size:18px; width: 151px; text-align: center; font-weight:bold;">綠色大樹果</div>
            <img id="img_4" onClick="buy(4)" src="../bookstore_courtyard/img/0.png" style="position:absolute; top:14px; left:17px; max-height:70px; max-width:70px; cursor:pointer;">
    		<a class="la" onClick="buy(4)" style="position:absolute; top:60px; left:132px; cursor:pointer;"></a>
        </div>
        <div id="box_5" style="position:absolute; top:283px; left:691px;display:none;">
        	<img src="./img/t_back.png">
            <div id="coin_5" style="position:absolute; top:37px; left:108px; color:#6C2113; font-size:18px; width: 101px; text-align:right;">2000</div>
            <div id="text_5" style="position:absolute; top:6px; left:88px; color: #4C2113; font-size:18px; width: 151px; text-align: center; font-weight:bold;">綠色大樹果</div>
            <img id="img_5" onClick="buy(5)" src="../bookstore_courtyard/img/0.png" style="position:absolute; top:14px; left:17px; max-height:70px; max-width:70px; cursor:pointer;">
    		<a class="la" onClick="buy(5)" style="position:absolute; top:60px; left:132px; cursor:pointer;"></a>
        </div>
        <a id="out" class="btn_out" onClick="out()" style="position:absolute; top:379px; left:882px; cursor:pointer;"></a>
        <img src="./img/shoper_hand.png" style="position:absolute; top:303px; left:237px; height: 123px; z-index:101;">
        <!-- 確認購買面面 -->
  	  <div id="wow" style="position:absolute; top:0px; left:50px;display:none; ">
       		<div onClick="" style="position:absolute; top:0px; left:-50px; height:480px; width:1000px; cursor:wait; background-color:#000; opacity:0.7; "></div>
             <img src="../img/back_ss.png"  style="position:absolute; z-index:99; top:28px; left:331px; cursor:pointer;">
            <img src="./img/check_to_buy2.png"  style="position:absolute; z-index:102; top:49px; left:375px; cursor:pointer;">
            <div id="page_coin" style="position:absolute;z-index:102; top:230px; left:467px; color:#F90; font-size:24px; width: 101px; text-align:right; display:none"></div>
   	        <div id="page_name" style="position:absolute;z-index:102; top:160px; left:439px; color:#370E0B; font-size:24px; width: 194px; text-align:center; font-weight:bold;"></div>
            <img id="page_img" src="../bookstore_courtyard/img/0.png" style="position:absolute;z-index:102; top:189px; left:484px; max-height:100px; max-width:100px; cursor:pointer;">
            <a id="no"  class="btn_no" onClick="close_page()" style="position:absolute; z-index:102; top:377px; left:581px; cursor:pointer;"></a>
            <a id="page_buy_btn" class="btn_yes" onClick="buy_it()" style="position:absolute; z-index:102; top:380px; left:407px; cursor:pointer;"></a>
   		<div id="page_coin_my" style="position:absolute; top:295px;z-index:102; left:443px; color:#F90; font-size:24px; width: 140px; text-align:right;">3000</div>
            <div  style="position:absolute; top:358px; left:336px;z-index:102; color:#F90; font-size:24px; width: 140px; text-align:right;">=</div>
       	  <div id="page_name" style="position:absolute; top:352px;z-index:102; left:434px; width: 194px; height:1px; background-color:#660000"></div>
          <div id="page_coin_use" style="position:absolute; top:324px;z-index:102; left:496px; color:#900; font-size:24px; width: 86px; text-align:right;">3000</div>
       	  <div id="page_coin_last" style="position:absolute; top:357px;z-index:102; left:446px; color:#F90; font-size:24px; width: 139px; text-align:right;">3000</div>

      </div>


</div>
</div>
<!--==================================================
    debug內容
    ====================================================== -->
<div id="debug" style="position:absolute;top:480px; "></div>
 </body>


    <script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var lock = false;
	//物品列
	var _item = new Array();
	var max_count = -1;
	var now_count = -1;
	//
	var tittle = "st";
	var cover_level = 0;
	var home_id = '<? echo $home_id;?>';
	var user_id = '<? echo $user_id;?>';
	var home_on = '<? echo $home_on;?>';
	var clerk_talk = new Array();
	if(home_id != user_id)
	{
		home_on = 'other';
	}
	var user_permission = '<? echo $permission;?>';

	var coin = 0;
	var name = "";
	var sex = 0;

	var cover_click = -1;

	var auth_open_publish = 1;
	var auth_read_opinion_limit_day = 14;
	var auth_rec_en_input = "yes";
	var auth_rec_draw_open = "yes";
	var auth_coin_open = "yes";


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
	function main()
	{
		echo("Main:初始開始:讀取使用者資料");
		cover("讀取使用者資料中");
		set_action_bookstore_log(user_id,'c1',3);//action_log

		var url = "../ajax/get_mssr_user_info.php";
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
					set_coin(data_array["user_coin"])
					cover("讀取物品");
					get_item();
				}
			}).error(function(e){
				echo("AJAX:error:main():讀取使用者資料:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){main();});

			}).complete(function(e){
				echo("AJAX:complete:main():讀取使用者資料:");
			});
	}
	function get_item()
	{
		echo("Main:初始開始:讀取物品資料");
		cover("讀取物品資料中")
		var url = "./ajax/get_item_info.php";
		$.post(url, {
					user_id:user_id,
					home_id:home_id,
					user_permission:user_permission,
					screening:1

			}).success(function (data)
			{
				echo("AJAX:success:get_item():讀取物品資料:已讀出:"+data);
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
					for(var i = 0 ; i< data_array["count"]; i++ )//
					{
						_item[i] = new Array();
						_item[i] = data_array[i];
						_item[i]["coin"]= Math.floor(_item[i]["coin"]);
						max_count = data_array["count"];
					}
					max_count = data_array["count"];
					now_count = 0;
					set_box(0);
					cover("");

				}
			}).error(function(e){
				echo("AJAX:error:get_item():讀取物品資料:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){get_item();});

			}).complete(function(e){
				echo("AJAX:complete:get_item():讀取物品資料:");
			});
	}
	function set_box(value)
	{

		now_count += value * 6 ;
		var tmp = Math.floor(now_count / 6)+1;
		var tmp2 = Math.floor((max_count-1) / 6)+1;
		window.document.getElementById("page_text").innerHTML = tmp+"/"+tmp2+"頁";
		if(tmp == 1) window.document.getElementById("left").style.display = "none";
		else window.document.getElementById("left").style.display = "block";
		if(tmp == tmp2) window.document.getElementById("right").style.display = "none";
		else window.document.getElementById("right").style.display = "block";
		for( i = 0 ; i < 6 ; i++)
		{
			if(i+now_count < max_count)
			{
				window.document.getElementById("coin_"+i).innerHTML = _item[i+now_count]["coin"];
				window.document.getElementById("text_"+i).innerHTML = _item[i+now_count]["name"];
				window.document.getElementById("img_"+i).src = "../bookstore_courtyard/img/"+_item[i+now_count]["id"]+".png";
				window.document.getElementById("box_"+i).style.display = "block";
			}
			else
			{
				window.document.getElementById("box_"+i).style.display = "none";
			}
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
	//確認購買區
	var buy_item_flag = -1;
	function close_page()
	{
		window.document.getElementById("wow").style.display = "none";
		window.document.getElementById("talk").innerHTML = "看看別的貨吧";
	}
	function buy(value)
	{
		//計算是否夠錢
		window.document.getElementById("page_buy_btn").style.display = "none";
		window.document.getElementById("page_coin_last").innerHTML = coin - _item[now_count+value]["coin"];
		window.document.getElementById("page_coin_use").innerHTML = _item[now_count+value]["coin"]*-1;
		window.document.getElementById("page_coin_my").innerHTML = coin;
		if(coin - _item[now_count+value]["coin"]>=0)
		{
			window.document.getElementById("page_buy_btn").style.display = "block";
			window.document.getElementById("page_coin_last").style.color = "#F90";
			window.document.getElementById("talk").innerHTML = "需要購買嗎?";
		}else
		{
			window.document.getElementById("page_buy_btn").style.display = "none";
			window.document.getElementById("page_coin_last").style.color = "#900";
			window.document.getElementById("talk").innerHTML = "葵幣不足喔";
		}

		buy_item_flag  = value + now_count;
		window.document.getElementById("page_name").innerHTML = _item[buy_item_flag]["name"];
		window.document.getElementById("page_coin").innerHTML = _item[buy_item_flag]["coin"];
		window.document.getElementById("page_img").src = "../bookstore_courtyard/img/"+_item[buy_item_flag]["id"]+".png";
		window.document.getElementById("wow").style.display = "block";
	}
	function buy_it()
	{
		if(lock)return false;
		lock = true ;

		echo("buy_it:初始開始:購買物品資料");
		cover("購買中")
		var url = "./ajax/set_buy_item.php";
		$.post(url, {
					user_id:user_id,
					item_id:_item[buy_item_flag]["id"],
					coin:coin,
					user_permission:user_permission
			}).success(function (data)
			{
				echo("AJAX:success:buy_it():購買物品資料:已讀出:"+data);
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
					cover("購買完成",1);
					set_coin( Math.floor(data_array["item_coin"])*-1);
					window.document.getElementById("wow").style.display = "none";
					window.document.getElementById("talk").innerHTML = "感謝購買!";
				}
			}).error(function(e){
				echo("AJAX:error:buy_it():購買物品資料:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function()
				{lock = false ;
				buy_it();});

			}).complete(function(e){
				echo("AJAX:complete:buy_it():購買物品資料:");
				lock = false ;
			});
	}
	//debug
	function echo(text)
	{
		if(0)window.document.getElementById("debug").innerHTML = text+"<br>"+window.document.getElementById("debug").innerHTML;
	}

	function out()
	{
		window.location.href = "../bookstore_courtyard/index.php";
	}
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

//
main();

    </script>
</Html>














