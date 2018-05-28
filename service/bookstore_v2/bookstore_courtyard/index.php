<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店花園
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
		require_once(str_repeat("../",3).'inc/get_permission_and_timetable/code.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
					APP_ROOT.'lib/php/db/code',
					APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------
	//中央大學的IP才能登入通訊所
	error_reporting (E_ERROR | E_WARNING | E_PARSE);
	if($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]){
	 $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
	}
	elseif($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]){
	 $ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
	}
	elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"]){
	 $ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
	}
	elseif (getenv("HTTP_X_FORWARDED_FOR")){
	 $ip = getenv("HTTP_X_FORWARDED_FOR");
	}
	elseif (getenv("HTTP_CLIENT_IP")){
	 $ip = getenv("HTTP_CLIENT_IP");
	}
	elseif (getenv("REMOTE_ADDR")){
	 $ip = getenv("REMOTE_ADDR");
	}
	else{
	 $ip = "Unknown";
	}
	//echo $ip;
	
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
	//SQL
	//---------------------------------------------------

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

		$user_id        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$home_id        =(isset($_GET['uid']))?(int)$_GET['uid']:$_SESSION['uid'];
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
		$home_on        = $home_id  == $user_id  ? "user" : "other";
		$status = 'u_mssr_bs';
		$t_p_sut=get_permission_and_timetable($conn='',$permission,$status,$arry_conn_user);

		if($t_p_sut["permission_ok"]==0)die($t_p_sut["permission_msg"]);
		if($t_p_sut["time_ok"]==0)die($t_p_sut["time_msg"]);
		if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");


		$sql="
			SELECT `status`,`permission`
			FROM `permissions`
			WHERE 1=1
				AND `permission`='{$permission}'
		";
		$guest = false;
		$u_mssr_bs = false;
		$db_results=db_result($conn_type='pdo','',$sql,$arry_limit=array(),$arry_conn_user);
		if(!empty($db_results)){
			foreach($db_results as $db_result){
				$rs_status=trim($db_result['status']);
				if(trim($db_result['status'])==='u_mssr_bs'){ $u_mssr_bs = true;}
				if(trim($db_result['permission'])==='guest_s'){ $guest = true;}
				if(trim($db_result['permission'])==='guest_t'){ $guest = true;}
				if(trim($db_result['permission'])==='guest_f'){ $guest = true;}
			}
		}
		if(!$u_mssr_bs)die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");

		$sql="
			SELECT `school_code`
			FROM `member_school`
			WHERE `uid` =".$_SESSION['uid']."
		";
		/*
		$db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
		 if(!empty($db_results)){
			foreach($db_results as $db_result){
				if(trim($db_result['school_code'])==='exp'){ $guest = true;}
			}
		}*/


	//---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------


?>
<!DOCTYPE HTML>
<Html>
<Head>
	<Title>書店GADNNNN</Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <!-- 掛載 -->
    <link rel="stylesheet" href="../css/btn2.css">
    <script type="text/javascript" src="../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
    <script type="text/javascript" src="../../../lib/jquery/ui/code.js"></script>
	<script type="text/javascript" src="../../../lib/jquery/ui/func/jquery_ui/jquery_ui_touch_punch_0.2.2/code.js"></script>
    <script src="../../../../ac/js/user_log.js"></script>
    <script src="../js/set_bookstore_action_log.js"></script>
	<script src="../js/select_thing.js" type="text/javascript"></script>
    <style>
    	/*改過*/
		.feri_friend{
			position:absolute;
			width:40px;
			height:40px;
			background: url('../img/ficon.png') 0px -50px;
		}
		/*改過*/
		.feri_friend:hover {
    		background: url('../img/ficon.png') 0px -143px;
		}
		/*改過*/
		.feri_friend_n{
			position:absolute;
			width:40px;
			height:40px;
			background: url('../img/ficon.png') -5px -5px;
		}
		/*改過*/
		.feri_friend_n:hover {
    		background: url('../img/ficon.png') -5px -92px;
		}
		/*改過*/
		.feri_good{
			position:absolute;
			width:40px;
			height:40px;
			background: url('../img/ficon.png') -45px -52px;
		}
		/*改過*/
		.feri_good:hover {
    		background: url('../img/ficon.png') -45px -145px;
		}
		/*改過*/
		.feri_good_n{
			position:absolute;
			width:40px;
			height:40px;
			background: url('../img/ficon.png') -45px -8px;
		}
		/*改過*/
		.feri_good_n:hover {
    		background: url('../img/ficon.png') -45px -94px;
		}
		/*改過*/
		.feri_home{
			position:absolute;
			width:45px;
			height:45px;
			background: url('../img/ficon.png') -84px -48px;
		}
		/*改過*/
		.feri_home:hover {
    		background: url('../img/ficon.png') -84px -136px;
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

			.number_bar2
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

            font-size:16px;
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
		.forum_exp_img{
			position:absolute;
			width:55px;
			height:61px;
			background: url('../img/forum.png') 0px 0px;
		}
		.forum_exp_img:hover {
    		background: url('../img/forum.png') 0px -61px;
		}
		.score_exp_img{
			position:absolute;
			width:55px;
			height:61px;
			background: url('../img/cpupl.png') 0px 0px;
		}
		.score_exp_img:hover {
    		background: url('../img/cpupl.png') 0px -61px;
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

		#ebook_door_btn:hover {
    		background: url('img/namaha.png') 0 -101px;
			opacity:1;
		}
		#ebook_door_btn{
			position:absolute;
			width:74px;
			height:101px;
			opacity:0;
			background: url('img/namaha.png') 0 0;
		}

		#ebook_door{
			position:absolute;
			width:74px;
			height:101px;
			background: url('img/namaha.png') 0 0;
		}

		#put_btn:hover {
    		background: url('img/put.png') 0 -107px;
			opacity:1;
		}
		#put_btn{
			position:absolute;
			width:138px;
			height:107px;
			background: url('img/put.png') 0 0;
			opacity:0;
		}

		#put{
			position:absolute;
			width:138px;
			height:107px;
			background: url('img/put.png') 0 0;
		}

		#home_ling{
			position:absolute;
			width:240px;
			height:163px;
			background: url('img/SCA.png') 0 0;
		}
		#home_ling_btn:hover {
    		background: url('img/SCA.png') 0 -163px;
			opacity:1;
		}
		#home_ling_btn{
			position:absolute;
			width:240px;
			height:163px;
			opacity:0;
			background: url('img/SCA.png') 0 0;
		}
		#fire_pin_ling{
			position:absolute;
			width:70px;
			height:137px;
			background: url('img/fire.png') 0 0;
		}
		#fire_pin_ling_btn:hover {
    		background: url('img/fire.png') 0 -137px;
			opacity:1;
		}
		#fire_pin_ling_btn{
			position:absolute;
			width:79px;
			height:137px;
			opacity:0;
			background: url('img/fire.png') 0 0;
		}
		#trans{
			position:absolute;
			width:152px;
			height:103px;
			background: url('img/trans.png') 0 0;
		}
		#trans_btn:hover {
    		background: url('img/trans.png') 0 -103px;
			opacity:1;
		}
		#trans_btn{
			position:absolute;
			width:152px;
			height:103px;
			opacity:0;
			background: url('img/trans.png') 0 0;
		}
		#right_btn:hover,#left_btn:hover{
    		background: url('img/blue_arrow.png') 0 -114px;
		}
		#right_btn,#left_btn{
			position:absolute;
			width:41px;
			height:57px;
			background: url('img/blue_arrow.png') 0 0;
		}
		#brownArea{
			position:absolute;
			top:-38px; 
			left:-36px; 
			height:28px; 
			width:450px; 
			background-color: #d19348;
			box-shadow: 0px 1px #777;

		}

		#brownArea:after{
			position: absolute;
			top:0px; 
			right:-10px; 
			content:'';
			background-color: #d19348;
			width: 28.5px;
			height: 28.5px;
			border-radius:50%; 
			display: block;
			box-shadow: 1px 0px #777;
		}

	
		.visitIcon{
			position: relative;
			top: 35px;
			left: 0px;
			width: 300px;
			height: 50px;
		

		}
		

		.visitIconImg{
			position: absolute;
			left: 5px;
			top: -2px;

		}
		
		.rate_number{
			position: absolute;
			left: 32px;
			top: -4px;
			margin-left: 5px;

		}
		
		
		#today_people , #total{
			font-size: 14px;
			color:#444;
			font-weight: 600;

		}
		#today_people {
		 	margin-right: 10px;
		 }

		#name{
			font-family:"微軟正黑體","sans-serif","黑體-繁","新細明體","Ariel";
		}
	

	</style>


</Head>
<body>
	<!--==================================================W
    遮罩內容
    ====================================================== -->
	<div id="cover" style="position:absolute; top:-8px; left:-8px; ">
    	<div onClick="" style="position:absolute; top:0px; left:0px; height:480px; width:1000px; cursor:wait; background-color:#000; opacity:0.7; z-index:9999"></div>
        <table width="385"   border="0" cellspacing="0"  style="position:absolute; top:181px; left:318px;   text-align: center; z-index:10000;">
        	<tr height="90">
            	<td width="385" align="center" valign="center" id="cover_text" style=""class="cover_box" >正在讀取中請稍後...

                </td>
            </tr>
            <tr height="40">
            	<td>
                    <div id="cover_btn_0" onClick="close_cover(2)" style="position:absolute; left:1px; width:110px; height:38px; text-align: center; z-index:10003; display:none; cursor:pointer;" class="ok_box">存檔</div>
                    <div id="cover_btn_1" onClick="close_cover(1)" style="position:absolute; left:141px; width:110px; height:38px; text-align: center; z-index:10001; display:none; cursor:pointer;" class="ok_box">確定</div>
                    <div id="cover_btn_2" onClick="close_cover(0)" style="position:absolute; left:286px; width:110px; height:38px; text-align: center; z-index:10002; display:none; cursor:pointer;" class="no_box">取消</div>

                 </td>
            </tr>
        </table>

	</div>
	<!--==================================================
    html內容
    ====================================================== -->

  
	<div style="position:absolute; top:-36px; left:-10px; width:1000px; height:500px;">
	<!-- 內頁內容 -->
		<!-- 改過 -->
    	<img src="./img/backgrand.png"  width="1000" style="position:absolute; top:29px; left:-35px;" border="0">
    	<!-- 改過 -->
 		<img src="./img/but.png"  width="1000" style="position:absolute; top:112px; left:-38px;" border="0">
 		 <!-- 改過 -->
    	<a id="fire_pin_ling"  style="position:absolute; top:27px; left:815px;"></a>
    	 <!-- 改過 -->
    	<a id="put" onClick=""  style="position:absolute; top:28px; left:620px; display:none;"></a>
    	<a id="trans" onClick="" style="position:absolute; top:70px; left:16px; display:none;" border="0"></a>

    	<a id="ebook_door" onClick="go_ebook_door()" style="position:absolute; top:29px; left:226px;display:none;" border="0"></a>
    	<!-- 改過 -->
		<a id="home_ling" onClick=""  style=" top:20px; left:340px;"></a>


	<!-- 圖片圖層 -->
        <div id="linttt" style="position: absolute; top:382px; left:0px; width:1033px; height:59px; z-index:601;">

           <!--  <img src="../img/the_1.png" style="position:absolute; top:-55px; left:282px;"> -->
           <!--好友背景圖-->
            <img src="../img/UI_2t.png" style="position:absolute; top:0px;left:-20px;">
			

			<!-- 咖啡色橫bar-->
   	  	 	<div id="brownArea" ></div>

			<!-- 名字 -->
          	<div id="name" style="position:absolute; top:-45px; left:0px;text-align: center; width: 345px; height: 52px;  white-space:nowrap; overflow:hidden; font-size:29px;" class="number_bar"></div>

       	  	<div id="coin"  style="position:absolute; top:22px; left:127px; width: 114px; height: 42px;display:none;" class="number_bar2">0</div>
       	  	<!-- 經驗值lv-->
	      	<div id="lv" style="position:absolute; top:-6px; left:45px; width: 82px; height: 42px;  text-align:left; font-size: 16px;" class="number_bar2"></div>
		  	<!-- 經驗值數字-->
	      	<div id="score_exp" style="position:absolute; top:22px; left:-15px; width: 125px; height: 42px;display:none;font-size: 16px;" class="number_bar2">0</div>
	       	<!-- 第三格-->
	      	<div id="forum_exp" style="position:absolute; top:20px; left:272px; width: 125px; height: 42px;display:none;" class="number_bar2">0</div>
          	<img src="../img/UI_2t_cover.png" id="bar_cover1" style="position:absolute; top:9px; left:142px; width: 125px; height: 42px;">
          	<img src="../img/UI_2t_cover.png" id="bar_cover2" style="position:absolute; top:9px; left:260px; width: 125px; height: 42px;">
      	</div>
	</div>

    <!-- 好友表表 -->
    <div id="other_ifame" style="position:absolute; left:-8px; top:0px;z-index:889;">
    </div>
    <!-- 物品欄框框 -->
    <div id="box_bar" style="position:absolute; top:-8px; left:-8px; display:none;">
    	<img src="../img/UI_3s.png"  style="position:absolute; top:337px;" border="0">
   		<a id="left_btn" onClick="set_page(-1)" src="./img/blue_arrow.png"  style="position:absolute; top:410px; left:19px;" border="0"></a>

        <a id="right_btn"  onClick="set_page(1)" src="./img/blue_arrow.png"  style="position:absolute; top:410px; left:864px;" border="0" class="flipx"></a>

        <a id="coin_img" class="coin_img"  style="position:absolute; top:339px; left:8px;" border="0"></a>
         <!-- 改過 -->
        <a class="btn_re"  style="position:absolute; top:410px; left:910px;" border="0"></a>
        <!-- 改過 -->
        <a id="inventory_btn_off" class="btn_c_box" onClick="set_page_close()" style="position:absolute; top:330px; left:854px;" ></a>
        
        <!-- 改過 -->
  	  	<div id="coin_box" style="position:absolute; text-align:right; left:-31px; top:355px; width: 179px;" class="number_bar2">0</div>
  	  	<!-- 改過 -->
        <div id="page_box" style="position:absolute; text-align:center; left:698px; top:355px; width: 179px;" class="number_bar2">0</div>
      <!-- 箱子上物件 -->
    	<div id="box" style="position:absolute; top:0px; left:0px;">
    	</div>
    </div>

    <!-- 地圖上物件 -->
	<div id="map" style="position:absolute; top:0px; left:0px; ">
    </div>
     <!-- 高階按鈕曾 -->
    <div id="hight_btn" style="position:absolute; top:-36px; left:-10px; width:960px; height:500px;; z-index:887">
    	 <!-- 改過 -->
        <a id="home_ling_btn" onClick="go_page('home')"  style="position:absolute; top:20px; left:340px; cursor:pointer;"></a>
        <!-- 改過 -->
        <a id="fire_pin_ling_btn" onClick="go_page('space')"  style="position:absolute; top:27px; left:815px; cursor:pointer;"></a>
        <a id="ebook_door_btn" onClick="go_ebook_door()" style="position:absolute; top:29px; left:226px;display:none;cursor:pointer;"></a>
		 <!-- 改過 -->
		<!-- 通訊所 中央大學才看到喔 -->
        <a id="put_btn"  onClick="go_page('communication')" style="position:absolute; top:28px; left:620px; cursor:pointer; display:none;"></a>
        <a id="trans_btn" onClick="" style="position:absolute; top:70px; left:16px;display:none;cursor:pointer; display:none;"></a>

        <!-- 瀏覽率 -->
        <div class="visitIcon" onClick="visit_bookstore_rate()" > 
            <div class="visitIconImg">
            	<img src="./img/visitIcon.png" alt="">
            </div>
            <div class="rate_number">
				<span id="today_people">今日人數:5000</span>
				<span id="total">總累積人數:10000000</span>
			</div>
        </div>
    </div>
	<!-- 好友按按 -->
    <div id="other_ifame2" style="position:absolute; left:-8px; top:0px; z-index:989">
    </div>
    <!-- 最高按鈕圖層 -->

<div id="high_btm" style="position:absolute; left:-8px; top:0px; z-index:888">
  <div id="other_store" style="position:absolute; top:290px; left:216px;display:none;">
 
               <!-- <img src="../img/firend_bar.png" style="position:absolute; top:-141px; left: -231px;"> -->
                <!-- 加入喜愛的書店 -->
               <a id="feri_friend" class="feri_friend" onClick="set_track()" style="cursor:pointer; position:absolute; top:10px; left:120px;display:none;"></a>
                <!-- 按讚 -->
               <a id="feri_good" class="feri_good" onClick="set_good()" style="cursor:pointer; position:absolute; top:12px; left:164px;display:none;"></a>
                <!-- 回到自己家 -->
               <a class="feri_home" style="cursor:pointer; position:absolute; top:-290px; left:685px;" onClick="cover('是否要回到自己家',2,function(){set_action_bookstore_log(user_id,'e37',action_on);back_home();})"></a>
    </div>
  	<a id="coin_imgs" class="coin_img" onClick="cover('<a style=\'color:#903;\'>葵幣:購買裝飾品的貨幣</a><BR>做推薦與販賣書籍獲得，也可由教師給予',1)" style="cursor:pointer; position:absolute; top:340px; left:115px;display:none;" border="0"></a>
  	<a id="score_exp_img" class="score_exp_img" onClick="cover('<a style=\'color:#903;\'>經驗值:經營書店的經驗值</a><BR>越努力經營書店數值越高',1)"   style="cursor:pointer; position:absolute; top:340px; left:-11px;display:none;" border="0"></a>
  	<a id="forum_exp_img" class="forum_exp_img" onClick="cover('<a style=\'color:#903;\'>聊書經驗值:經營聊書的經驗值</a><BR>越努力進行聊書數值越高',1)"   style="cursor:pointer; position:absolute; top:340px; left:259px;display:none;" border="0"></a>
  	<!-- 進書店icon-->
  	<a id="home_btn" class="btn_bst" onClick="go_page('home')"  style="position:absolute; top:328px; left:440px; cursor:pointer;"></a>
  	<!-- 去宇宙icon -->
  	<a id="fire_pin_btn" class="btn_nuv" onClick="go_page('space')"  style="position:absolute; top:328px; left:520px; cursor:pointer;" ></a>
  	<!-- 通訊所icon -->
  	<a id="communication_btn" class="btn_communication" onClick="go_page('communication')"  style="position:absolute; top:328px; left:600px; display:none; cursor:pointer;" ></a>
 	<!-- 進商場icon -->
  	<a id="shop_btn" class="btn_shop" onClick="go_page('shop')"  style="position:absolute; cursor:pointer; top:328px; left:686px;display:none;"></a>
  	<!-- 物品欄icon-->
  	<a id="box_btn" class="btn_o_box" onClick="set_box_page(0)"  style="position:absolute; cursor:pointer; top:328px; left:770px;display:none;"></a>
  	<!-- 離開遊戲icon -->  
  	<a id="out_btn" class="btn_outgame" onClick="go_page('menu')"  style="position:absolute; top:328px; left:850px; cursor:pointer;"></a>


  </div>
    <!-- 賣東西頁面 -->
    <div id="sell_item" style="position:absolute; left:-8px; top:0px; z-index:890; display:none;">
    	<div onClick="" style="position:absolute; top:0px; left:0px; height:480px; width:1000px; background-color:#000; opacity:0.7;"></div>
        <img src="./img/check_to_buy.png" onClick="go_page('fight')"  style="position:absolute; top:29px; left:235px;" border="0">
        <div style="position:absolute; left:363px; top:43px; color:#060; font-size:56px; width: 359px; text-align:center; width: 267px;">確認販賣</div>
        <div id="item_name" style="position:absolute; left:399px; top:136px; color: #8F553D; font-size:28px; width: 359px; text-align:right; width: 209px;">確認販賣</div>
        <div id="item_coin" style="position:absolute; left:399px; top:174px; color: #F07D0B; font-size:28px; width: 359px; text-align:right; width: 154px;">確認販賣</div>
    	<img id="item_png" src="./img/0.png"  style="position:absolute; top:139px; left:299px; max-height:90px; max-width:96px;" border="0">
        <img src="./img/coin.png"  style="position:absolute; top:168px; left:558px; width: 50px;" border="0">
        <img src="./img/yes.png" onClick="sell_it()" style="position:absolute; top:282px; left:310px; cursor:pointer;" border="0">
        <img src="./img/no.png" onClick="close_sell()" style="position:absolute; top:283px; left:579px; cursor:pointer;" border="0">
    </div>
     <!-- 說明頁面 -->
     <!-- 改過 -->
   		<img id="book_store_help" onClick="close_help()" src="./img/go_help.png" width="1000"style=" position: absolute; top: -8px; left: -38px; height: 497px;cursor: pointer; z-index: 9998; display: block;">

<!--==================================================
    debug內容
    ====================================================== -->
<div id="debug" style="position:absolute;top:480px; "></div>
</body>


    <script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	//很特別的納馬下小風車開關
	var ebook = false;
	//滑鼠事件

	var IE = document.all?true:false
	if (!IE) document.captureEvents(Event.MOUSEMOVE)
	document.onmousemove = getMouseXY;
	var tempX = 0
	var tempY = 0
	function getMouseXY(e) {
	  if (IE) { // grab the x-y pos.s if browser is IE
		tempX = event.clientX + document.body.scrollLeft
		tempY = event.clientY + document.body.scrollTop
	  } else {  // grab the x-y pos.s if browser is NS
		tempX = e.pageX
		tempY = e.pageY
	  }
	  if (tempX < 0){tempX = 0}
	  if (tempY < 0){tempY = 0}

	//  echo("滑鼠"+tempX+","+tempY);
	  return true
	}

	//好友與按讚
	var have_track = 0;
	var btn_track_type = "";
	var have_good = 0;
	var btn_good_type = "";

	//物品陣列
	var map_item = new Array();
	var map_flag = 0;
	var box_item = new Array();
	var box_flag = 0;
	var _item = new Array();
	//道具蘭用具
	var max_count = 0;
	var now_count = 0;
	//
	var tittle = "st";
	var cover_level = 0;
	var home_id = '<? echo $home_id;?>';
	var user_id = '<? echo $user_id;?>';
	var home_on = '<? echo $home_on;?>';
	
	//通訊所 維修
	<?php if($ip == '140.115.135.36'):?>
	window.document.getElementById("communication_btn").style.display = "block";
	window.document.getElementById("put").style.display = "block";
	window.document.getElementById("put_btn").style.display = "block";
	<?php endif;?>
	

	var action_on = 0;
	var clerk_talk = new Array();
	if(home_id != user_id)
	{
		home_on = 'other';
		action_on = 2;
	}else
	{
		action_on = 1;
	}
	var user_permission = '<? echo $permission;?>';

	var coin = 0;
	var score_exp = 0;
	var forum_exp = 0;
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
	//關閉說明頁面
	function close_help()
	{
		window.parent.help_cover["courtyard_main_help"] = false;
		window.document.getElementById("book_store_help").style.display = "none";
	}
	//=========MAIN=============
	//拜訪別人的書店金錢
	function set_visit()
	{
		echo("set_visit:初始開始:給予拜訪金錢系列");
		cover("讀取物品資訊中")
		var url = "../ajax/set_mssr_visit_log.php";
		$.post(url, {
				user_id:user_id,
				home_id:home_id,
				user_permission:user_permission

			}).success(function (data)
			{
				echo("AJAX:success:set_visit():給予拜訪金錢系列:已讀出:"+data);
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
					if(data_array["mas"] != "")
					{
						set_coin(data_array["coin"]);
						cover(data_array["mas"],1);
					}
				}
			}).error(function(e){
				echo("AJAX:error:set_visit():給予拜訪金錢系列:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){set_visit();});

			}).complete(function(e){
				echo("AJAX:complete:set_visit():給予拜訪金錢系列:");
			});

	}
	
	//瀏覽率

	function visit_bookstore_rate(){
		echo("visit_bookstore_rate:初始開始:讀取使用者瀏覽率");
		// cover("讀取物品資訊中")
		var url= "../ajax/get_mssr_visit_log.php";
		$.post(url, {
				user_id:user_id,
				home_id:home_id,
				user_permission:user_permission

			}).success(function (data)
			{
				echo("AJAX:success:set_visit():讀取使用者瀏覽率:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",1);
					return false;
				}
				data_array = JSON.parse(data);
				// console.log(data);

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
					total_visit_count = data_array["total_visit_count"];
					today_visit_count = data_array["today_visit_count"];
					
					
					// var userName = name.substr(0,8);
					window.document.getElementById("today_people").innerHTML =  "今日人數："+ today_visit_count;
					window.document.getElementById("total").innerHTML =  "總累積人數："+ total_visit_count;
					
					// clerk_talk = data_array["clerk_talk"];
					
				}

			}).error(function(e){
				echo("AJAX:error:visit_bookstore_rate:讀取使用者瀏覽率:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){set_visit();});

			}).complete(function(e){
				echo("AJAX:complete:visit_bookstore_rate():讀取使用者瀏覽率:");
			});
		
	}



	function main()
	{
		echo("Main:初始開始:讀取使用者資料");
		cover("讀取使用者資料中");
		set_action_bookstore_log(user_id,'b1',action_on);//action_log
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
					coin = data_array["user_coin"];
					score_exp = data_array["score_exp"];
					forum_exp = 0;
					name = data_array["user_name"];
					var userName = name.substr(0,8);
					window.document.getElementById("name").innerHTML = userName +"的書店";
					clerk_talk = data_array["clerk_talk"];
					auth_coin_open = data_array["auth_coin_open"];
					//ebook
					ebook = data_array["ebook"];
					if(ebook == 1 )
					{
						window.document.getElementById("ebook_door").style.display = "block";
						window.document.getElementById("ebook_door_btn").style.display = "block";
					}
					//

					if(auth_coin_open == "all_no")
					{
						data_array["map_item"] = "";
						data_array["box_item"] = "";
					}
					//建立MAP資訊  並 建立圖片
					var tmp = new Array();
					tmp = data_array["map_item"].split(",");
					for( var i = 0 ; i< (tmp.length -1);i+=3 )//-1為易位防止
					{
						map_item[map_flag] = new Array();
						map_item[map_flag]["_id"] = tmp[i];
						map_item[map_flag]["_x"] = tmp[i+1];
						map_item[map_flag]["_y"] = tmp[i+2];

						window.document.getElementById("map").innerHTML = window.document.getElementById("map").innerHTML +'<img name="'+tmp[i]+'" id="'+map_flag+'" src="img/'+tmp[i]+'.png"  class="draggable" style=" position:absolute; top:'+tmp[i+2]+'px; opacity:1; z-index:'+tmp[i+2]+';  left:'+tmp[i+1]+'px;">';
						map_flag++;
					}
					//建立BOX資訊  XXXXXXXXXX
					tmp = new Array();
					tmp = data_array["box_item"].split(",");
					for(var i = 0 ; i< (tmp.length -1); i+=2 )//-1為易位防止
					{
						box_item[box_flag] = new Array();
						box_item[box_flag]["_id"] = tmp[i];
						box_item[box_flag]["_n"] = Math.floor(tmp[i+1]);
						box_flag++;
						max_count++;
					}

					$("#map img").each(function()
					{
							if (this.complete) //if already loaded
								{
									add_map.call(this);
									draggable_map_stop.call(this);
									echo("complete"+ this.id);
								}
							else //hook load event
								{
									var tmp = $(this);
									tmp.load(add_map);
									tmp.load(draggable_map_stop);
									echo("load"+ this.id);
								}
					});

					//設定顯示按鈕
					if(home_on=="user" && auth_coin_open != "all_no")
					{
						if(window.parent.help_cover["courtyard_main_help"])
						{
							window.document.getElementById("book_store_help").style.display = "block";
						};

						window.document.getElementById("shop_btn").style.display = "block";
						window.document.getElementById("box_btn").style.display = "block";
					}

					window.document.getElementById("bar_cover1").style.display = "block";
					window.document.getElementById("bar_cover2").style.display = "block";
					window.document.getElementById("score_exp").style.display = "block";
					window.document.getElementById("score_exp_img").style.display = "block";
					if(data_array["forum_open"])//設定聊書功能隱藏
					{
						window.document.getElementById("forum_exp_img").style.display = "block";
						window.document.getElementById("forum_exp").style.display = "block";
						window.document.getElementById("forum_exp").innerHTML = data_array["forum_exe"];
						window.document.getElementById("bar_cover1").style.display = "none";
					}
					else
					{

					}
					if(auth_coin_open != "all_no")//設定金錢隱藏
					{
						window.document.getElementById("coin").style.display = "block";
						window.document.getElementById("coin_imgs").style.display = "block";
						window.document.getElementById("score_exp").style.display = "block";
						window.document.getElementById("score_exp_img").style.display = "block";
						window.document.getElementById("bar_cover1").style.display = "none";
						if(data_array["forum_open"])window.document.getElementById("bar_cover2").style.display = "none";
					}
					else
					{

						if(data_array["forum_open"])
						{
							window.document.getElementById("bar_cover1").style.display = "none";
							window.document.getElementById("forum_exp").style.left = "134px";
							window.document.getElementById("forum_exp_img").style.left = "127px";
						}
					}
					if(auth_coin_open == "yes" && home_on == 'other')//拜訪金錢系列
					{   set_coin(0);
						set_score_exp(0);
						set_visit();
						
					}
					else
					{
						set_coin(0);
						set_score_exp(0);
					}

					set_other_iframe();
					visit_bookstore_rate();

				}
			}).error(function(e){
				echo("AJAX:error:main():讀取使用者資料:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){main();});

			}).complete(function(e){
				echo("AJAX:complete:main():讀取使用者資料:");
			});
	}
	function main_front()
	{

		echo("main_front:初始開始:讀取物品資訊");
		cover("讀取物品資訊中")
		var url = "./ajax/get_item_info.php";
		$.post(url, {
				screening:1
			}).success(function (data)
			{
				echo("AJAX:success:main_front():讀取物品資訊:已讀出:"+data);
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
						_item[data_array[i]["id"]] = new Array();
						_item[data_array[i]["id"]] = data_array[i];
					}
					main();
				}
			}).error(function(e){
				echo("AJAX:error:main_front():讀取物品資訊:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){main_front();});

			}).complete(function(e){
				echo("AJAX:complete:main_front():讀取物品資訊:");
			});

	}
	var dradra;

	function add_map()
	{
		//依點的位置  改變大小
		var tmpx = $(this).position().left;
		var tmpy = $(this).position().top;
		if($(this).position().top>=80)
		var tmp = (($(this).position().top-50)*0.0035 + 0.2);
		else var tmp = ((30)*0.0035 + 0.2);
		this.style.transform= "scale("+tmp+")";
		this.style.msTransform = "scale("+tmp+")";
		this.style.webkitTransform = "scale("+tmp+")";
		//進行移動
		echo("後取"+$(this).position().top);
		this.style.top = (tmpy  -  ($(this).height()*tmp)/2 - $(this).height()/2)+"px";
		this.style.left = (tmpx -  ($(this).width()/2))+"px";

		//ui.position.top = (tempY -  ($(this).height()*tmp)/2 - $(this).height()/2);
		//ui.position.left = (tempX -  $(this).width()/2);;
		//塗層位置
		this.style.zIndex = $(this).position().top;
	}
	function draggable_map_stop()
	{

		if(home_on=="user")$(this).draggable({cancel: "img"});
	}
	function draggable_map()
	{

		if(home_on=="user")$(this).draggable({
							start: function( event, ui )
								{

								},

							cancel: "",
							drag: function( event, ui )
								{
									//判定滑鼠位置
									if(tempY < 360)
									{	//在地圖上
										//改變大小]
										if(tempY>=80)
											var tmp = ((tempY-50)*0.0035 + 0.2);
										else
											var tmp = ((30)*0.0035 + 0.2);
										this.style.transform= "scale("+tmp+")";
										this.style.msTransform = "scale("+tmp+")";
										this.style.webkitTransform = "scale("+tmp+")";

										//進行移動
										ui.position.top = (tempY -  ($(this).height()*tmp)/2 - $(this).height()/2);
										ui.position.left = (tempX -  $(this).width()/2);;

										this.style.zIndex = tempY;


									}
									else
									{	//在物品欄中
										var tmp = 0;
										if(this.width > this.height)  tmp=70/this.width;
       									else tmp=70/this.height;
										this.style.transform= "scale("+tmp+")";
										ui.position.top = (tempY -  ($(this).height()*tmp));
										ui.position.left = (tempX -  ($(this).width())*tmp/2);;
									}
								},

							stop: function(event, ui)
								{
									if($(this).position().top +(this.height) <360)
									{
										//哈哈  沒是我好棒 ((吃土
										echo("位置移動("+map_item[this.id]["_x"]+","+map_item[this.id]["_y"]+") → ("+tempX+","+tempY+")");

										map_item[this.id]["_x"]=tempX;
										map_item[this.id]["_y"]=tempY;
									}
									else
									{
										if(tempX <= 900)
										{//在物品欄
											var tmp =-1;
											echo("這個?"+this.name);
											for(var i = 0 ; i < box_item.length ; i++)
											{
												if(this.name == box_item[i]["_id"])
												{echo("這??????"+box_item[i]["_id"]);
													tmp = i;
												}

											}
											if(tmp == -1)
											{//物品欄無此物  新建
												echo("物品欄無此物  新建");

												var tmp2 = new Array();
												tmp2["_id"] = this.name;
												tmp2["_n"] = 1;
												box_item.push(tmp2);
												box_flag++;

											}
											else
											{//物品欄有此物   +1
												echo("物品欄有此物  +1");

												box_item[tmp]["_n"]++;

											}
											//刪除地圖物件
											map_item[this.id]["_id"] = "-1";
											map_item[this.id] = null;

											remove_move_map_obj(this.id);

											set_page(0);
											//放到箱子裡面

										}
										else
										{//賣掉

												var tmp =-1;
												echo("這個?"+this.name);
												for(var i = 0 ; i < box_item.length ; i++)
												{
													if(this.name == box_item[i]["_id"])
													{echo("這??????"+box_item[i]["_id"]);
														tmp = i;
													}

												}
												if(tmp == -1)
												{//物品欄無此物  新建
													echo("物品欄無此物  新建");

													var tmp2 = new Array();
													tmp2["_id"] = this.name;
													tmp2["_n"] = 1;
													box_item.push(tmp2);
													box_flag++;

												}
												else
												{//物品欄有此物   +1
													echo("物品欄有此物  +1");

													box_item[tmp]["_n"]++;

												}
												//刪除地圖物件
												map_item[this.id]["_id"] = "-1";
												map_item[this.id] = null;

												//set_page(0);
												//放到箱子裡面
											sell_item(this);
										}
									}
								}

						});
	}
	function draggable_box()
	{

		if(home_on=="user")$(this).draggable({
							start: function( event, ui )
								{

								},

							drag: function( event, ui )
								{
									//判定滑鼠位置
									if(tempY < 360)
									{	//在地圖上

										//改變大小]
										if(tempY>=80)
											var tmp = ((tempY-50)*0.0035 + 0.2);
										else
											var tmp = ((30)*0.0035 + 0.2);
										this.style.transform= "scale("+tmp+")";
										this.style.msTransform = "scale("+tmp+")";
										this.style.webkitTransform = "scale("+tmp+")";

										//先進行移動
										ui.position.top = (tempY -  ($(this).height()*tmp)/2 - $(this).height()/2);
										ui.position.left = (tempX -  $(this).width()/2);;

										this.style.zIndex = tempY;


									}
									else
									{	//在物品欄中
										var tmp = 0;
										if(this.width > this.height)  tmp=70/this.width;
       									else tmp=70/this.height;
										this.style.transform= "scale("+tmp+")";
										this.style.msTransform = "scale("+tmp+")";
										this.style.webkitTransform = "scale("+tmp+")";
										ui.position.top = (tempY -  ($(this).height()*tmp));
										ui.position.left = (tempX -  ($(this).width())*tmp/2);;
									}
								},

							stop: function(event, ui)
								{
									if($(this).position().top +(this.height) <360)
									{
										//建立新地圖物件  資料處裡
										map_item[map_flag] = new Array();
										map_item[map_flag]["_id"] = this.name;
										map_item[map_flag]["_x"] = tempX;
										map_item[map_flag]["_y"] = tempY;

										window.document.getElementById("map").innerHTML = window.document.getElementById("map").innerHTML +'<img name="'+this.name+'" id="'+map_flag+'" src="img/'+this.name+'.png"  class="draggable" style=" position:absolute; top:'+tempY+'px; opacity:1; z-index:'+tempY+';  left:'+tempX+'px;">';

										//建立新地圖物件  拖動功能
										$("#"+map_flag).each(function()
										{

											if (this.complete) //if already loaded
											{

												add_map.call(this);
											}
											else //hook load event
											{

												var tmp = $(this);
												tmp.load(add_map);
											}
										})
										$("#map img").each(function()
										{
												if (this.complete) //if already loaded
												{

													draggable_map.call(this);

												}
												else //hook load event
												{
													var tmp = $(this);

													tmp.load(draggable_map);

												}
										});
										map_flag++;
										//刪除物品欄的資
										var tmp = ""
										for(var i = 0 ; i < box_item.length ; i++)
										{
											if(this.name == box_item[i]["_id"])
											{
												tmp = i;
											}

										}
										;
										if(box_item[tmp]["_n"] == 1 )
										{
											box_item.splice(tmp,1);
											box_flag--;
										}
										else box_item[tmp]["_n"] --;





										//重新整理物品欄
										set_page(0);
									}
									else
									{
										if(tempX <= 900)
										{//在物品欄
											set_page(0);
										}
										else
										{//賣掉
											//set_page(0);
											sell_item(this);
										}
									}
								}

						});
		/*var tmp = 0;
		if(this.width > this.height)  tmp=70/this.width;
		else tmp=70/this.height;
		this.style.transform= "scale("+tmp+")";
		this.style.msTransform = "scale("+tmp+")";
		this.style.webkitTransform = "scale("+tmp+")";*/
	}
	//設定金錢
	function set_coin(value)
	{
		echo("set_coin(value):設定金錢:value->"+value);
		coin = Math.floor(coin) + Math.floor(value);
		coin = Math.floor(coin);
		window.document.getElementById("coin").innerHTML = coin ;
		window.document.getElementById("coin_box").innerHTML = coin ;
		cover("");
	}
	//設定經驗值
	function set_score_exp(value)
	{
		echo("set_score_exp(value):設定經驗值:value->"+value);
		score_exp = Math.floor(score_exp) + value;
		score_exp = Math.floor(score_exp);
		window.document.getElementById("score_exp").innerHTML = score_exp ;
		for( var tmp_exp = 0 ,up = 300 , lv = 1 ;tmp_exp <= score_exp ; up =up* 1.2,lv++,tmp_exp = tmp_exp+up)
		{

			window.document.getElementById("lv").innerHTML = "Lv:"+lv;
		}
		cover("");
	}
	//設定星球讚
	function set_good()
	{
		cover("讚!!送出中");
		echo("set_track:初始開始:設定好友+"+btn_good_type);
		if(btn_good_type=="") return false;
		var tmp = btn_good_type;
		btn_good_type = "";
		var url = "../page_other_store_info/ajax/set_star.php";
		$.post(url, {
					home_id:home_id,
					type:tmp
			}).success(function (data)
			{
				echo("AJAX:success:set_good():設定好友:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>",1);
					echo("AJAX:success:set_good():設定好友:資料庫發生問題");
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

					if(have_good==0)
					{
						have_good = 1;
						window.document.getElementById("feri_good").className = "feri_good";
						btn_good_type = "del";
						cover("成功按讚",1);
					}else
					{
						have_good = 0;
						window.document.getElementById("feri_good").className = "feri_good_n";
						btn_good_type = "add";
						cover("成功回收讚",1);
					}
					window.document.getElementById("feri_good").style.display = "block";

				}

			}).error(function(e){
				echo("AJAX:error:set_good():設定好友:");

			}).complete(function(e){
				echo("AJAX:complete:set_good():設定好友:");
			});
	}
	//設定好友
	function set_track()
	{
		cover("加入或刪除好友中");
		echo("set_track:初始開始:設定好友+"+btn_track_type);
		if(btn_track_type=="") return false;
		var tmp = btn_track_type;
		btn_track_type = "";
		var url = "../page_other_store_info/ajax/set_track.php";
		$.post(url, {
					home_id:home_id,
					type:tmp
			}).success(function (data)
			{
				echo("AJAX:success:set_track():設定好友:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>",1);
					echo("AJAX:success:set_track():設定好友:資料庫發生問題");
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

					if(have_track==0)
					{
						have_track = 1;
						window.document.getElementById("feri_friend").className = "feri_friend";
						btn_track_type = "del";
						cover("已加入喜愛的書店",1);
					}else
					{
						have_track = 0;
						window.document.getElementById("feri_friend").className = "feri_friend_n";
						btn_track_type = "add";
						cover("已刪除喜愛的書店",1);
					}
					window.document.getElementById("feri_friend").style.display = "block";
					window.document.getElementsByName('page_track_menu')[0].src = window.document.getElementsByName('page_track_menu')[0].src;

				}

			}).error(function(e){
				echo("AJAX:error:set_track():設定好友:");

			}).complete(function(e){
				echo("AJAX:complete:set_track():設定好友:");
			});
	}
	//獲取朋友列
	function get_track_have()
	{
		echo("Main:初始開始:讀取店家資料");

		var url = "../page_other_store_info/ajax/get_track_have.php";
		$.post(url, {
					user_id:user_id,
					user_permission:user_permission,
					home_id:home_id
			}).success(function (data)
			{
				echo("AJAX:success:main():讀取店家資料:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",1);
					echo("AJAX:success:main():讀取店家資料:資料庫發生問題");
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
					have_track = data_array["have_track"];
					if(data_array["have_track"]==1)
					{
						window.document.getElementById("feri_friend").className = "feri_friend";
						btn_track_type = "del";
					}else
					{
						window.document.getElementById("feri_friend").className = "feri_friend_n";
						btn_track_type = "add";
					}
					window.document.getElementById("feri_friend").style.display = "block";

					have_good = data_array["have_good"];
					if(data_array["have_good"]==1)
					{
						window.document.getElementById("feri_good").className = "feri_good";
						btn_good_type = "del";
					}else
					{
						window.document.getElementById("feri_good").className = "feri_good_n";
						btn_good_type = "add";
					}
					window.document.getElementById("feri_good").style.display = "block";


					//黑名單  不採用
					//set_black_user_text(data_array["have_black"]);
				}

			}).error(function(e){
				echo("AJAX:error:main():讀取店家資料:");

			}).complete(function(e){
				echo("AJAX:complete:main():讀取店家資料:");
			});
	}
	function set_other_iframe()
	{
		if(home_on == "other")
		{
			echo("啟動去別人家的功能");
			window.document.getElementById("other_store").style.display = "block";
			get_track_have();
			//window.document.getElementById("other_ifame2").innerHTML = window.document.getElementById("other_ifame2").innerHTML+'<iframe src="../page_other_store_info/index.php?home_id='+home_id+'" frameborder="0" height="300" width="550" style="position:absolute; left:0px; top:0px; width: 551px; height: 117px;"></iframe>';

		}else if(home_on =="user")
		{
			echo("啟動自己家的功能");
			window.document.getElementById("other_store").style.display = "none";

			//window.document.getElementById("other_ifame").innerHTML = window.document.getElementById("other_ifame").innerHTML+'<iframe src="../page_msg_menu/index.php" frameborder="0" height="300" width="550" style="position:absolute; left:0px; top:0px; width: 551px; height: 117px;"></iframe>';

		}
		window.document.getElementById("other_ifame").innerHTML = window.document.getElementById("other_ifame").innerHTML+'<iframe name="page_track_menu" src="../page_track_menu/index.php" frameborder="0" height="300" width="550" style="position:absolute; left:0px; top:400px; width: 1000px; height: 80px;"></iframe>';
	}
	//debug
	function echo(text)
	{
		if(0)window.document.getElementById("debug").innerHTML = text+"<br>"+window.document.getElementById("debug").innerHTML;
	}

	function set_page_close()
	{
		cover("儲存物品中，請稍後")
		$("#map img").each(function()
		{
				if (this.complete) //if already loaded
					{

						draggable_map_stop.call(this);
						echo("complete"+ this.id);
					}
				else //hook load event
					{
						var tmp = $(this);

						tmp.load(draggable_map_stop);
						echo("load"+ this.id);
					}
		});
		window.document.getElementById("box_bar").style.display = "none";
		window.document.getElementById("linttt").style.display = "block";
		window.document.getElementById("other_ifame").style.display = "block";
		set_item();
		window.document.getElementById("high_btm").style.display = "block";
	}
	var tmp_item
	function sell_item(obj)
	{
		echo("賣掉 "+ obj.name);
		tmp_item = obj ;
		window.document.getElementById("sell_item").style.display = "block";
		window.document.getElementById("item_name").innerHTML = _item[obj.name]["name"];
		window.document.getElementById("item_coin").innerHTML = Math.floor(_item[obj.name]["coin"])/5;
		window.document.getElementById("item_png").src = "./img/"+obj.name+".png";
	}
	function close_sell()
	{


		set_page(0);
		window.document.getElementById("sell_item").style.display = "none";

		remove_move_map_obj(tmp_item.id);

	}
	//處裡販賣物品
	function sell_it()
	{

		//tmp_item.name
		//刪除物品欄的資
		var tmp_i = ""
		for(var i = 0 ; i < box_item.length ; i++)
		{
			if(tmp_item.name == box_item[i]["_id"])
			{
				tmp_i = i;
			}

		}
		;
		if(box_item[tmp_i]["_n"] == 1 )
		{
			box_item.splice(tmp_i,1);
			box_flag--;
		}
		else box_item[tmp_i]["_n"] --;


		cover("販賣物品中")
		//輸出<br>
		echo("====================輸出======================");
		var tmp = "";
		for(var i = 0 ; i < box_item.length ; i++)
		{
			tmp = tmp + box_item[i]["_id"] + "," + box_item[i]["_n"]+ ",";

		}
		echo("BOX : "+tmp);

		tmp2 = "";
		for(var i = 0 ; i < map_flag ; i++)
		{
			if(map_item[i])tmp2 = tmp2 + map_item[i]["_id"] + "," + Math.floor(map_item[i]["_x"])+ "," +  Math.floor(map_item[i]["_y"])+",";
		}
		echo("MAP : "+tmp2);

		set_sell(tmp,tmp2,tmp_item.name);
	}
	function set_sell(tmp,tmp2,item_id)
	{
		echo("set_sell:初始開始:販賣物品中，請稍後");
		cover("販賣物品中")
		var url = "./ajax/set_sell_item_info.php";
		$.post(url, {
				user_permission:'<? echo $_SESSION['permission'];?>',
				user_id:user_id,
				item_map:tmp2,
				item_box:tmp,
				sell_item:item_id,
				coin:coin
			}).success(function (data)
			{
				echo("AJAX:success:set_sell():販賣物品中:已讀出:"+data);
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
					set_coin(data_array["coin"]);
					set_page(0);
					window.document.getElementById("sell_item").style.display = "none";

					remove_move_map_obj(tmp_item.id);

					cover("銷售完成",1);

				}
			}).error(function(e){
				echo("AJAX:error:set_sell():販賣物品中:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){set_sell(tmp,tmp2,item_id);});

			}).complete(function(e){
				echo("AJAX:complete:set_sell():販賣物品中:");
			});

	}

	function set_item()
	{
		//輸出<br>
		echo("====================輸出======================");
		var tmp = "";
		for(var i = 0 ; i < box_item.length ; i++)
		{
			tmp = tmp + box_item[i]["_id"] + "," + box_item[i]["_n"]+ ",";

		}
		echo("BOX : "+tmp);

		tmp2 = "";
		for(var i = 0 ; i < map_flag ; i++)
		{
			if(map_item[i])tmp2 = tmp2 + map_item[i]["_id"] + "," + Math.floor(map_item[i]["_x"])+ "," + Math.floor(map_item[i]["_y"])+",";
		}
		echo("MAP : "+tmp2);

		echo("set_item:初始開始:儲存物品中，請稍後");
		cover("讀取物品資訊中")
		var url = "./ajax/set_item_info.php";
		$.post(url, {
				user_permission:'<? echo $_SESSION['permission'];?>',
				user_id:user_id,
				item_map:tmp2,
				item_box:tmp,
				coin:coin
			}).success(function (data)
			{
				echo("AJAX:success:main_front():存取物品資訊:已讀出:"+data);
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
					window.document.getElementById("hight_btn").style.display = "block";
					cover("物品儲存完成",1);

				}
			}).error(function(e){
				echo("AJAX:error:set_item():存取物品資訊:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){set_item();});

			}).complete(function(e){
				echo("AJAX:complete:set_item():存取物品資訊:");
			});

	}
	//物品蘭公用
	function set_box_page()
	{

		set_action_bookstore_log(user_id,'b2',action_on);//action_log
		window.document.getElementById("hight_btn").style.display = "none";
		$("#map img").each(function()
		{
				if (this.complete) //if already loaded
					{

						draggable_map.call(this);
						echo("complete"+ this.id);
					}
				else //hook load event
					{
						var tmp = $(this);

						tmp.load(draggable_map);
						echo("load"+ this.id);
					}
		});
		set_page(0);

	}
	function set_page(value)
	{
		window.document.getElementById("high_btm").style.display = "none";
		now_count = now_count+(10*value);
		max_count = box_flag;
		var tmp = Math.floor(((max_count-1)/10)+1);
		var tmp2 = Math.floor(((now_count)/10)+1);
		if(tmp2 <= 0 ) tmp2 = 1;
		if(tmp <= 0 ) tmp = 1;
		window.document.getElementById("page_box").innerHTML=tmp2+"/"+tmp;
		if(now_count == 0) window.document.getElementById("left_btn").style.display = "none";
		else window.document.getElementById("left_btn").style.display = "block";
		if(now_count+10>=max_count) window.document.getElementById("right_btn").style.display = "none";
		else window.document.getElementById("right_btn").style.display = "block";

		open_box();
	}
	function open_box()
	{
		echo("打開物品蘭燒");
		window.document.getElementById("box").innerHTML = "";
		//box_item.splice(1,2);
		for(var i = now_count,j = 0 ; i < max_count  && j < 10;  )//-1為易位防止
		{
			if(box_item[i]["_n"] != null && box_item[i]["_n"])
			{
				window.document.getElementById("box").innerHTML = window.document.getElementById("box").innerHTML + '<div id="box_'+box_item[i]["_id"]+'count" style="position:absolute; top:446px; left:'+(75+(j*79))+'px; width:80px; font-size:26px" class="number_bar">x'+box_item[i]["_n"]+'</div>';
				window.document.getElementById("box").innerHTML = window.document.getElementById("box").innerHTML + ' <img name="'+box_item[i]["_id"]+'" id="" src="./img/'+box_item[i]["_id"]+'.png" id="box_'+box_item[i]["_id"]+'_count" style="position:absolute; top:415px; left:'+(85+(j*79))+'px; max-height:54px; max-width:54px;" border="0">';
				i++,j++;
			}
		}
		$("#box img").each(function()
		{
				if (this.complete) //if already loaded
					{
						draggable_box.call(this);
					}
				else //hook load event
					{
						var tmp = $(this);
						tmp.load(draggable_box);
					}
		});
		window.document.getElementById("box_bar").style.display = "block";
		window.document.getElementById("linttt").style.display = "none";
		window.document.getElementById("other_ifame").style.display = "none";


	}
	function remove_move_map_obj(id)
	{
		var parent=document.getElementById("map");
		var child=document.getElementById(id);
		if(child)parent.removeChild(child);
	}
	function go_page(value)
	{
		<? if($guest){?>

			if(value == "home")window.location.href = "../index.php?uid="+home_id;

			else if(value == "menu")window.parent.location.href = "../../mssr_menu.php";

			else if(value == "space")cover("前往太空!<BR>可進入宇宙搜拜訪其他人的星球<BR>請申請正式帳號才可以使用",1);

			else if(value == "communication")cover("研究所!<BR>提供推薦、星球排行榜的地方<BR>請申請正式帳號才可以使用",1);

			else if(value == "shop")window.location.href = "../bookstore_shop/index.php";

		<? }else {?>
			if(value == "home")window.location.href = "../index.php?uid="+home_id;

			else if(value == "menu")window.parent.location.href = "../../mssr_menu.php";

			else if(value == "space")window.location.href = "../bookstore_space/index.php?uid="+home_id;

			else if(value == "communication")window.location.href = "../bookstore_communication/index.php?home_id="+home_id;

			else if(value == "shop")window.location.href = "../bookstore_shop/index.php";
		<? };?>


	}
	//打開那馬ˇ下EBOOK
	function go_ebook_door()
	{
		window.location="../bookstore_ebook/index4.html?id="+home_id;
	}
	function back_home()
	{
		window.location.href="./index.php";
		;
	}
	 //---------------------------------------------------
    //ONLOADㄒ
    //---------------------------------------------------

//
main_front();

    </script>
</Html>














