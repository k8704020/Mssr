<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,通訊所
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
		require_once(str_repeat("../",0).'inc/communicaton_type/code.php');

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

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
        $user_id        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
		$home_id        =(isset($_GET['home_id']))?(int)$_GET['home_id']:$_SESSION['uid'];
		if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");

		//匯入選項陣列
		$communicaton_type = communicaton_type();
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

	//---------------------------------------------------
	//SQL
	//---------------------------------------------------
	$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);


	$sql="
        SELECT `status`,`permission`
        FROM `permissions`
        WHERE 1=1
            AND `permission`='{$permission}'
    ";
	$u_mssr_bs = false;
    $db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
    if(!empty($db_results)){
        foreach($db_results as $db_result){
            $rs_status=trim($db_result['status']);
            if(trim($db_result['status'])==='u_mssr_bs'){ $u_mssr_bs = true;}
			if(trim($db_result['permission'])==='guest_s'){ die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");}
			if(trim($db_result['permission'])==='guest_t'){die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");}
				if(trim($db_result['permission'])==='guest_f'){ die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");}
        }
    }
	if(!$u_mssr_bs)die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");

	$sql="
        SELECT `school_code`
		FROM `member_school`
		WHERE `uid` =".$_SESSION['uid']."
    ";
	/*$db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
     if(!empty($db_results)){
        foreach($db_results as $db_result){
			if(trim($db_result['school_code'])==='exp'){ die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");}
        }
    }**/


	$dadad = date("Y-m-d  H:i:s");
	$sql =" SELECT `user`.`student`.`class_code`,`user`.`school`.`school_code`,`user`.`class`.`grade`
			FROM `user`.`student`
			LEFT JOIN `user`.`class`
			ON `user`.`class`.`class_code` = `user`.`student`.`class_code`

			LEFT JOIN `user`.`member_school`
			ON `user`.`member_school`.`uid` = `user`.`student`.`uid`

			LEFT JOIN `user`.`school`
			ON `user`.`school`.`school_code` = `user`.`member_school`.`school_code`

			WHERE `user`.`student`.`uid` = $user_id
			AND '$dadad' BETWEEN `user`.`student`.`start` AND `user`.`student`.`end`";
	$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	if(!count($result))
	{
		$sql =" SELECT `user`.`teacher`.`class_code`,`user`.`school`.`school_code`,`user`.`class`.`grade`
			FROM `user`.`teacher`
			LEFT JOIN `user`.`class`
			ON `user`.`class`.`class_code` = `user`.`teacher`.`class_code`

			LEFT JOIN `user`.`member_school`
			ON `user`.`member_school`.`uid` = `user`.`teacher`.`uid`

			LEFT JOIN `user`.`school`
			ON `user`.`school`.`school_code` = `user`.`member_school`.`school_code`

			WHERE `user`.`teacher`.`uid` = $user_id
			AND '$dadad' BETWEEN `user`.`teacher`.`start` AND `user`.`teacher`.`end`";
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
	}
	/*
	echo "<pre>";
	print_r($sql);
	echo "</pre>";
	 * */
?>
<!DOCTYPE HTML>
<Html>
<Head>
	<Title>閱讀登記</Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <!-- 掛載 -->
    <link rel="stylesheet" href="../css/btn.css">
    <script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
    <script src="../js/set_bookstore_action_log.js"></script>



    <style>
		.text_1 {
		    display: block;
			width: 100%;
			height: 34px;
			padding: 3px 6px;
			font-size: 18px;
			line-height: 1.42857143;
			color: #555;
			background-color: #fff;
			background-image: none;
			border: 1px solid #ccc;
			border-radius: 4px;

	}
	   .none
	   {
		   visibility:hidden;
			opacity:0;
			transform:translateX(20px);
		}
		.none2
	   {
		   visibility:hidden;
			opacity:0;
			transform:translateY(20px);
		}

	   .show_up
	   {
		   visibility:visible;
		   opacity:1;
		   transform:translateX(0px);
		   transition-duration:1s;

		}
       body{
            overflow:hidden;
            position:relative;
			font-family: Microsoft JhengHei;
            z-index:1;
        }
		.btn_in {
  			position:absolute;
			width:55px;
			height:27px;
			cursor:pointer;
			background: url('img/btn.png') 0 0;
	   }
		.btn_in:hover {
				background: url('img/btn.png') 0 -27px;
	   }
		.btn_in:active {
				background: url('img/btn.png') 0 -54px;
	   }
		 /*中文特效用*/
            .world_bar
            {
            text-shadow:2px 0px 1px rgba(9,39,99,1),
                        0px -2px 1px rgba(9,39,99,1),
                        -2px 0px 1px rgba(9,39,99,1),
                        0px 2px 1px rgba(9,39,99,1),
                        2px 2px 1px rgba(9,39,99,1),
                        2px -2px 1px rgba(9,39,99,1),
                        -2px 2px 1px rgba(9,39,99,1),
                        -2px -2px 1px rgba(9,39,99,1)
						,2px 0px 1px rgba(9,39,99,1),
                        0px -2px 1px rgba(9,39,99,1),
                        -2px 0px 1px rgba(9,39,99,1),
                        0px 2px 1px rgba(9,39,99,1),
                        2px 2px 1px rgba(9,39,99,1),
                        2px -2px 1px rgba(9,39,99,1),
                        -2px 2px 1px rgba(9,39,99,1),
                        -2px -2px 1px rgba(9,39,99,1);


            font-weight:bold;
            color:#FCFFFF;
            letter-spacing:0pt;
            text-align:center;

            }
			.world_bar2
            {
            text-shadow:1px 0px 1px rgba(9,39,99,1),
                        0px -1px 1px rgba(9,39,99,1),
                        -1px 0px 1px rgba(9,39,99,1),
                        0px 1px 1px rgba(9,39,99,1),
                        1px 1px 1px rgba(9,39,99,1),
                        1px -1px 1px rgba(9,39,99,1),
                        -1px 1px 1px rgba(9,39,99,1),
                        -1px -1px 1px rgba(9,39,99,1)
						,1px 0px 1px rgba(9,39,99,1),
                        0px -1px 1px rgba(9,39,99,1),
                        -1px 0px 1px rgba(9,39,99,1),
                        0px 1px 1px rgba(9,39,99,1),
                        1px 1px 1px rgba(9,39,99,1),
                        1px -1px 1px rgba(9,39,99,1),
                        -1px 1px 1px rgba(9,39,99,1),
                        -1px -1px 1px rgba(9,39,99,1);


            font-weight:bold;
            color:#FCFFFF;
            letter-spacing:0pt;
            text-align:center;

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
	</style>
</Head>
<body  style="position:absolute; top:-8px; left:-8px;">
	<!--==================================================
    遮罩內容
    ====================================================== -->
    <div id="cover" style="position:absolute; top:-8px; left:-8px; display:none;">
    	<div onClick="" style="position:absolute; top:0px; left:0px; height:480px; width:1000px; cursor:wait; background-color:#000; opacity:0.7; z-index:9999"></div>
        <table width="500"   border="0" cellspacing="0"  style="position:absolute; top:181px; left:230px;   text-align: center; z-index:10000;">
        	<tr height="90">
            	<td width="385" align="center" valign="center" id="cover_text" style=""class="cover_box" >正在讀取中請稍後...

                </td>
            </tr>
            <tr height="40">
            	<td>
                        <div id="cover_btn_0" onClick="close_cover(2)" style="position:absolute; left:1px; width:110px; height:38px; text-align: center; z-index:10003; display:; cursor:pointer;" class="ok_box">存檔</div>
                        <div id="cover_btn_1" onClick="close_cover(1)" style="position:absolute; left:141px; width:110px; height:38px; text-align: center; z-index:10001; display:; cursor:pointer;" class="ok_box">確定</div>
                        <div id="cover_btn_2" onClick="close_cover(0)" style="position:absolute; left:286px; width:110px; height:38px; text-align: center; z-index:10002; display:; cursor:pointer;" class="no_box">取消</div>

                 </td>
            </tr>
        </table>

	</div>
	<!--==================================================
    html內容
    ====================================================== -->
    <!-- 背景圖 -->
    <img src="img/back.png" style="position:absolute ; top:0px; left:0px;">

    <!-- 選項功能 -->
	<div id="menu" style="position:absolute; top:-7px; left:262px; visibility:hidden;">
        <img src="img/bar2.png" style="position:absolute ; top:30px; left:30px;">
        <div id="on_1" style="position:absolute; top:48px; left:53px; width: 623px; height: 21px; font-size:16px; font-weight:bold; color:#FFF;">

        </div>
        <a class="btn_arrow_l" style="position:absolute; top:149px; left:33px; display:none;"></a>
        <a class="btn_arrow_r" style="position:absolute; top:149px; left:603px; display:none;"></a>
            <!-- 小排1 -->
  			<div id="menu_box_1" style="position:absolute; top:86px; left:93px;" class="none2">
                <img src="img/paper.png" style="position:absolute; top:0px; left:0px;">
                <div id="menu_tittle_1" style="position:absolute; top:3px; left:13px; width: 136px; height: 33px; font-size:22px; font-weight:bold; color:#FFF;" class="world_bar">
                	熱門推薦作品
                </div>
                <img id="menu_pic_1" src="img/a1.png" style="position:absolute; top:49px; left:22px; width:120px; height:61px;">
                <img src="img/box.png" style="position:absolute; top:46px; left:15px;">

        		<div id="menu_cont_1" style="position:absolute; top:127px; left:15px; width: 129px; height: 74px; word-break:break-all;font-size: 18px;">
                	由推薦作品所獲得的按讚數排行。
                </div>
                <a class="btn_in" onClick="menu_click(1)" style="position:absolute; top:187px; left:92px;"></a>
        	</div>
            <!-- 小排2 -->
      		<div id="menu_box_2" style="position:absolute; top:86px; left:263px;" class="none2">
                <img src="img/paper.png" style="position:absolute; top:0px; left:0px;">
                <div id="menu_tittle_2" style="position:absolute; top:3px; left:13px; width: 136px; height: 33px; font-size:22px; font-weight:bold; color:#FFF;" class="world_bar">
                	熱門星球佈置
                </div>
                <img id="menu_pic_2" src="img/a2.png" style="position:absolute; top:49px; left:22px; width:120px; height:61px;">
                <img src="img/box.png" style="position:absolute; top:46px; left:15px;">

        		<div id="menu_cont_2" style="position:absolute; top:127px; left:15px; width: 129px; height: 74px; word-break:break-all;font-size: 18px;">
                	由星球佈置所獲得的按讚數排行。
                </div>
                <a class="btn_in" onClick="menu_click(2)" style="position:absolute; top:187px; left:92px;"></a>
        	</div>
            <!-- 小排3 -->
      		<div id="menu_box_3" style="position:absolute; top:86px; left:433px;" class="none2">
                <img src="img/paper.png" style="position:absolute; top:0px; left:0px;">
                <div id="menu_tittle_3" style="position:absolute; top:3px; left:13px; width: 136px; height: 33px; font-size:22px; font-weight:bold; color:#FFF;" class="world_bar">
                	熱門閱讀書籍
                </div>
                <img id="menu_pic_3" src="img/a3.png" style="position:absolute; top:49px; left:22px; width:120px; height:61px;">
                <img src="img/box.png" style="position:absolute; top:46px; left:15px;">

        		<div id="menu_cont_3" style="position:absolute; top:127px; left:15px; width: 129px; height: 74px; word-break:break-all;font-size: 18px;">
                	書籍閱讀次數的排行。
                </div>
                <a class="btn_in" onClick="menu_click(3)" style="position:absolute; top:187px; left:92px;"></a>
			</div>
		</div>

    </div>

    <!-- 報表功能 -->
	<div id="post" style="position:absolute; top:13px; left:71px; visibility:hidden;">
    	<img src="img/bar.png">
        <iframe  frameborder="0" id="ininin" src="" style="position:absolute; top: 48px; left:52px; width:761px; height:367px;" >

        </iframe>
        <div id="on_2"  style="position:absolute; top:18px; left:42px; width: 683px; height: 21px; font-size:16px; font-weight:bold; color:#FFF;">
       	  <div id="tititit" style="float:left;font-size:28px; font-weight:bold; color:#FFF;" class="world_bar"></div> <select id="set_ramger_1" class="text_1" onchange="set_ramger_1();" style="width:180px; float:left;color:#F11;">
          </select>

          <select id="set_ramger_2" class="text_1" onchange="set_ramger_2();" style="width:180px;float:left; color:#F11;">
          </select>
        </div>

</div>
    <!-- 研究所導覽員 -->
    <div  class="none" id="sever_box" style="position:absolute; top:0px; left:0px;">
        <img id="sever_man" src="../img/S2.png" style="position:absolute; top:77px; left:0px; height:400px;">
        <img id="sever_bar" src="img/say.png" style="position:absolute; top:313px; left:376px;">
        <div id="sever_txt" style="position:absolute; top:346px; left:419px; width:397px; height:119px; word-break:break-all; font-size:24px;">
        </div>
    </div>

    <!-- 最上按鈕 -->
    <div id="all_bar" onClick="click_say()" style="position:absolute; top:0px; left:0px; width:1000px; height:480px; cursor:pointer"></div>
  <!-- 改過 -->   
    <a id="btn_out" class="btn_out" onClick="go_out()" style= "position:absolute; top:378px; left:870px;"></a>
    <a id="btn_back_up" onClick="btn_back_up()" class="btn_back_up" style= "position:absolute; top:293px; left:870px; display:none;"></a>
	<!-- 內頁內容
    <iframe id="" src="./find_book/index.php" width="1000" height="480" scrolling="no" frameborder="0" style="overflow:hidden;"></iframe>

     -->
<!--==================================================
    debug內容
    ====================================================== -->
<div id="debug" style="position:absolute;top:480px;"></div>
</body>


    <script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	
	var communicaton_type = <?php echo json_encode($communicaton_type,true);?>;
	var chick_type = "";
	var home_id='<? echo $home_id;?>';
	var cover_click = -1;
	var cover_level = 0;
	var say_array ;
	var say_on = 0;
	var rec_type = 0;
	var menu_page = 0;
	var level_on = 0;

	var class_code = '<?PHP echo $result[0]["class_code"]; ?>';
	var school_code = '<?PHP echo $result[0]["school_code"]; ?>';
	var grade_code = '<?PHP echo $result[0]["grade"]; ?>';

	var welcome = new Array("open",function(){set_menu(1,0);},"歡迎光臨通訊研究所，研究數據的地方，在這裡可以獲得宇宙各星球之間的資料。","我們統計了許多資料並整合成了文件，點擊上方各類型文件來觀看!","stay");
	//if(home_id != 65003)welcome = new Array("目前通訊所正在整修，預計2/22整修完畢。","stay");
	var post_1 = new Array("open",function(){set_post(0);},"您選擇的是推薦熱門排行榜，這份資料可以讓你清楚的了解那些人認真做推薦書籍。","認真做推薦讓更多人給讚吧!","","close");
	var post_2 = new Array("open",function(){set_post(0);},"您選擇的是星球熱門排行榜，這份資料可以讓你清楚的了解那些精心佈置的星球。","裝飾您的庭院獲得更多按讚數!","","close");
	var post_3 = new Array("open",function(){set_post(0);},"您選擇的是書籍熱門排行榜，這份資料可以讓你清楚的了解那些書籍是不錯的。","找到喜歡的書籍可以馬上去借來看或是建議爸媽購買。","","close");

	var back_1 = new Array("open",function(){set_menu(1,0);},"還有其他統計資料可以查看","stay");

	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	
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
	function main()
	{
		set_action_bookstore_log(<? echo $_SESSION["uid"];?>,'d1',1);//action_log

		say_array = welcome ;
	 	click_say();
		cover("");


	}

	function click_say()
	{
		window.document.getElementById("sever_box").className="show_up";
		if( say_array[say_on] == "stay" )
		{
			say_on=0;
			window.document.getElementById("all_bar").style.display = "none";
		}
		else if(say_array[say_on] == "close" )
		{
			say_on=0;
			window.document.getElementById("sever_box").className="none";
			window.document.getElementById("all_bar").style.display = "none";
		}
		else if(say_array[say_on] == "open" )
		{
			say_on++;
			say_array[say_on]();
			say_on++;
			click_say();
		}
		else
		{
			window.document.getElementById("all_bar").style.display = "block";
			window.document.getElementById("sever_txt").innerHTML = say_array[say_on];
			say_on++;
		}
		if(say_array.length == say_on+1)click_say();

	}
	function set_menu(op,level)
	{
		if(op == 1)
		{
			window.document.getElementById("menu").style.visibility = "";

			window.document.getElementById("menu_box_1").style.transitionDelay = "0.1s";
			window.document.getElementById("menu_box_1").className = "show_up";

			window.document.getElementById("menu_box_2").style.transitionDelay = "0.5s";
			window.document.getElementById("menu_box_2").className = "show_up";

			window.document.getElementById("menu_box_3").style.transitionDelay = "1s";
			window.document.getElementById("menu_box_3").className = "show_up";
		}
		else
		{
			window.document.getElementById("menu").style.visibility = "hidden";

			window.document.getElementById("menu_box_1").style.transitionDelay = "0s";
			window.document.getElementById("menu_box_1").className = "none";

			window.document.getElementById("menu_box_2").style.transitionDelay = "0s";
			window.document.getElementById("menu_box_2").className = "none";

			window.document.getElementById("menu_box_3").style.transitionDelay = "0s";
			window.document.getElementById("menu_box_3").className = "none";
		}

	}
	function set_ramger_3()
	{
		cover("讀取中");
		var e = document.getElementById("set_ramger_3");
		rec_type = 	e.options[e.selectedIndex].value;
		document.getElementById("ininin").src = "./page/"+tmp_array[0]+"/index.php?ramge="+tmp_array[1]+"&time="+tmp_array[2]+"&class_code="+class_code+"&school_code="+school_code+"&grade_code="+grade_code+"&rec_type="+rec_type;
		
	}

	function set_ramger_2(chick_type_class='')
	{
		var e1 = document.getElementById("set_ramger_1");
		var e2 = document.getElementById("set_ramger_2");
		cover("讀取"+ e1.options[e1.selectedIndex].value + "-" + e2.options[e2.selectedIndex].value +"中");
		tmp_array = communicaton_type[chick_type][e1.options[e1.selectedIndex].value][e2.options[e2.selectedIndex].value];
		e2.style.color = "#000";
		document.getElementById("ininin").onload= function () {close_cover();}
		document.getElementById("ininin").src = "./page/"+tmp_array[0]+"/index.php?ramge="+tmp_array[1]+"&time="+tmp_array[2]+"&class_code="+class_code+"&school_code="+school_code+"&grade_code="+grade_code+"&rec_type="+rec_type;
		
	}
	function set_ramger_1(value,chick_type_class='')
	{
		var tmeop = 0;
		var e = document.getElementById("set_ramger_1");
		e.style.color = "#000";
		tmp_array = communicaton_type[chick_type][e.options[e.selectedIndex].value];
		window.document.getElementById("set_ramger_2").innerHTML = '<option disabled="disabled" id="ffsfsfsf"  style="color:#000;" selected="">[請選擇時間範圍]</option>';
		
		document.getElementById("ininin").src ="";
		for(key in tmp_array)
		{
			if(key == value)
			{

				tmeop = 1 ;
				window.document.getElementById("set_ramger_2").innerHTML = window.document.getElementById("set_ramger_2").innerHTML+'<option value="'+key+'"  style="color:#000;" selected="selected">'+key+'</option>';
			}
			else
			 window.document.getElementById("set_ramger_2").innerHTML = window.document.getElementById("set_ramger_2").innerHTML+'<option value="'+key+'"  style="color:#000;" >'+key+'</option>';
		}
		if(tmeop == 1 )set_ramger_2(chick_type_class);

		window.document.getElementById("ffsfsfsf").style.color="#c11";
	}
	function menu_click(on)
	{
		var tmp_on = menu_page*3 + on;
		if(tmp_on == 1)
		{

			chick_type = "熱門推薦";
			chick_type_class = "rec";
			say_array = post_1;
			say_on = 0;
			set_menu(0,0);

			click_say();
		}
		if(tmp_on == 2)
		{

			chick_type = "熱門佈置";
			chick_type_class = "star";
			say_array = post_2;
			say_on = 0;
			set_menu(0,0);
			click_say();
		}
		if(tmp_on == 3)
		{
			chick_type = "熱門書籍";
			chick_type_class ="books";
			say_array = post_3;
			say_on = 0;
			set_menu(0,0);
			click_say();
		}
		window.document.getElementById("tititit").innerHTML = chick_type;
		tmp_array = communicaton_type[chick_type];
		document.getElementById("ininin").src ="";
		window.document.getElementById("set_ramger_1").innerHTML = '<option id="dasdasdasd" disabled="disabled" >[請選擇範圍]</option>';
		window.document.getElementById("set_ramger_2").innerHTML = '<option id="ffsfsfsf" disabled="disabled" ></option>';
		for(key in tmp_array)
		{
			if('所有玩家'==key)window.document.getElementById("set_ramger_1").innerHTML = window.document.getElementById("set_ramger_1").innerHTML+'<option value="'+key+'"  style="color:#000;" selected="selected">'+key+'</option>';
			else if('所有學校同年級'==key)window.document.getElementById("set_ramger_1").innerHTML = window.document.getElementById("set_ramger_1").innerHTML+'<option value="'+key+'"  style="color:#000;" selected="selected">'+key+'</option>';
			else window.document.getElementById("set_ramger_1").innerHTML = window.document.getElementById("set_ramger_1").innerHTML+'<option value="'+key+'"  style="color:#000;">'+key+'</option>';

		}
		set_ramger_1('這周的排行',chick_type_class);//設定預設條件

		window.document.getElementById("dasdasdasd").style.color="#c11";
	}
	function set_post(op)
	{
		window.document.getElementById("btn_back_up").style.display = "block";
		level_on = 1000;
		window.document.getElementById("post").style.visibility = "";
	}
	function btn_back_up()
	{
		if(level_on == 1000)
		{
			window.document.getElementById("btn_back_up").style.display = "none";
			window.document.getElementById("post").style.visibility = "hidden";
			say_array = back_1;
			say_on = 0;
			click_say();
		}
	}
	//debug
	function echo(text)
	{
		if(0)window.document.getElementById("debug").innerHTML = text+"<br>"+window.document.getElementById("debug").innerHTML;
	}
	function go_out()
	{
		window.location.href = "../bookstore_courtyard/index.php?uid="+home_id;
	}



	//---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

	main();

    </script>
</Html>














