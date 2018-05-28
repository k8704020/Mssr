<?php
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

   	$sql="
			SELECT `status`,`permission`
			FROM `permissions`
			WHERE 1=1
				AND `permission`='{$permission}'
		";
		$guest = false;
		$u_mssr_bs = false;
		$db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
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


	//---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------

	//建立連線 user
    $conn_user=conn($db_type='mysql',$arry_conn_user);
    $sess_permission=addslashes(trim($_SESSION['permission']));
    $forum_flag=false;
	$book_store=false;
	$forum_flag_home=false;
    $sql="
        SELECT `status`
		FROM `member`
		LEFT JOIN `permissions`
		ON `member`.`permission` = permissions.`permission`
		WHERE `member`.`uid` = {$home_id}
    ";
    $db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
    if(!empty($db_results)){
        foreach($db_results as $db_result){
            $rs_status=trim($db_result['status']);
            if($rs_status==='u_mssr_forum'){$forum_flag_home=true;continue;}
        }
    }
	$_SESSION["forum_flag_home"] = $forum_flag_home;

	$sql="
        SELECT `status`
        FROM `permissions`
        WHERE `permission`='{$sess_permission}'
    ";
    $db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
    if(!empty($db_results)){
        foreach($db_results as $db_result){
            $rs_status=trim($db_result['status']);
            if($rs_status==='u_mssr_forum'){$forum_flag=true;continue;}
        }
    }

	$_SESSION["forum_flag"] = $forum_flag;
	if(!$book_store)
	{
		//die('你沒權限進入窩');
	}

    $arry_1=array(
        'gcp_2015_2_5_6_1','gcp_2015_2_5_8_1','tst_2015_2_4_4_1',
        'tst_2015_2_4_7_1','tbn_2015_2_4_2_1','apa_2015_2_4_5_1'
    );
    $arry_2=array(
        'gcp_2015_2_5_2_1','gcp_2015_2_5_4_1','gcp_2015_2_5_7_1',
        'gcp_2015_2_5_9_1','tst_2015_2_4_2_1','tst_2015_2_4_3_1',
        'tst_2015_2_4_6_1','tst_2015_2_4_9_1','tbn_2015_2_4_1_1',
        'tbn_2015_2_4_4_1','apa_2015_2_4_3_1','apa_2015_2_4_4_1'
    );
    $arry_3=array(
        trim('dsg'),trim('jzm'),trim('uxo'),trim('sgc'),
        trim('zpi'),trim('irx'),trim('gsz'),trim('vqk'),
        trim('gid'),trim('gth'),trim('bts'),trim('pce'),
        trim('ctc'),trim('gnk'),trim('gpe'),trim('nhe'),
        trim('gdc'),trim('csp'),trim('gps'),trim('cyc'),
        trim('jdy'),trim('smb'),trim('bnr'),trim('nep'),
        trim('dru'),trim('nsa'),trim('zbq'),trim('pqr'),
        trim('wbp'),trim('cjh  ')
    );
    $arry_4=array(
        trim('ged '),trim('ghf '),trim('ghl '),trim('zla '),
        trim('glh '),trim('zsk '),trim('star'),trim('bjd '),
        trim('pyd '),trim('cte '),trim('gsl '),trim('gfd '),
        trim('nif '),trim('pnr '),trim('wof '),trim('gzj '),
        trim('yre '),trim('api '),trim('smps'),trim('nam '),
        trim('uwn '),trim('ivw '),trim('did '),trim('lrb '),
        trim('chi '),trim('edl '),trim('won '),trim('dxu ')
    );
    $arry_5=array(
        trim('lqe  '),trim('cnh  '),trim('mosll'),trim('tnl  '),
        trim('kch  '),trim('cde  '),trim('gry  '),trim('pmc  '),
        trim('gis  '),trim('18361'),trim('gnps '),trim('nat  '),
        trim('ctg  '),trim('dles '),trim('wxt  '),trim('gpj  '),
        trim('gsw  '),trim('gnd  '),trim('wuh  '),trim('lqd  '),
        trim('ybs  '),trim('dzu  '),trim('clc  '),
        trim('dhl  '),trim('test '),trim('itl  '),trim('dxi  ')
    );

    $forum_href     ="../_dev_forum_eric_default/view/user.php?user_id={$user_id}&tab=1";
    if(isset($_SESSION['class'][0][1])){
        if(in_array(trim($_SESSION['class'][0][1]),$arry_1)){
            $forum_href="../_dev_forum_eric_achievement/view/user.php?user_id={$user_id}&tab=1";
        }
        if(in_array(trim($_SESSION['class'][0][1]),$arry_2)){
            $forum_href="../_dev_forum_eric_mission/view/user.php?user_id={$user_id}&tab=1";
        }
    }
    if(isset($_SESSION['class'][0][1]) && isset(explode("_",$_SESSION['class'][0][1])[0])){
        $sess_school_code=explode("_",$_SESSION['class'][0][1])[0];
        if(in_array($sess_school_code, $arry_3)){
            $forum_href="../_dev_forum_eric_mission/view/user.php?user_id={$user_id}&tab=1";
        }
        if(in_array($sess_school_code, $arry_4)){
            $forum_href="../_dev_forum_eric_mission/view/user.php?user_id={$user_id}&tab=1";
        }
        if(in_array($sess_school_code, $arry_5)){
            $forum_href="../_dev_forum_eric_achievement/view/user.php?user_id={$user_id}&tab=1";
        }
    }
    if(isset($_SESSION['class'][0][1])){
        if(trim($_SESSION['class'][0][1])===trim('lrb_2015_2_4_2_2')){
            $forum_href='_dev_forum_eric_achievement/view/index.php';
        }
    }
    if(isset($_SESSION['class'][0][1])){
        $sess_class=trim($_SESSION['class'][0][1]);
        $sql="
            SELECT `country_code`
            FROM `class`
                INNER JOIN `semester` ON
                `class`.`semester_code` = `semester`.`semester_code`

                INNER JOIN `school` ON
                `semester`.`school_code` = `school`.`school_code`
            WHERE 1=1
                AND `class`.`class_code`='{$sess_class}'
        ";
        $db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
        if(!empty($db_results)){
            $sess_country_code=trim($db_results[0]['country_code']);
            if($sess_country_code!=='tw'){
                $forum_flag     =false;
            }
        }
    }

    $forum_href     ="../forum/view/user.php?user_id={$user_id}&tab=1";

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
    <link rel="stylesheet" href="./css/btn2.css">
    <script type="text/javascript" src="../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
	<script src="js/select_thing.js" type="text/javascript"></script>
    <script src="../../../ac/js/user_log.js"></script>
    <script src="js/set_bookstore_action_log.js"></script>


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

            font-family:Microsoft JhengHei,comic sans, comic sans ms, cursive, verdana, arial, sans-serif;
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
		.pc_l{
			position:absolute;
			opacity:0;
			width:72px;
			height:83px;
			background: url('img/PC2.png') -0px -0px;
		}
		.pc_l:hover {
			opacity:1;
    		background: url('img/PC2.png') -0px -83px;
		}
		.PC_hh{
			position:absolute;
			width:108px;
			height:259px;
			background: url('img/PC_hh.png') -0px -0px;
			opacity:0;
		}
		.PC_hh:hover {
    		background: url('img/PC_hh.png') -0px -0px;
			opacity:1;
		}
		.pc{
			position:absolute;
			width:72px;
			height:83px;
			background: url('img/PC2.png') -0px -0px;
		}
		.pc:hover {
    		background: url('img/PC2.png') -0px -83px;
		}
        /*改過*/
		.feri_friend{
			position:absolute;
			width:40px;
			height:40px;
			background: url('img/ficon.png') 0px -50px;
		}
        /*改過*/
		.feri_friend:hover {
    		background: url('img/ficon.png') 0px -143px;
		}
        /*改過*/
		.feri_friend_n{
			position:absolute;
			width:40px;
			height:40px;
			background: url('img/ficon.png') -5px -5px;
		}
        /*改過*/
		.feri_friend_n:hover {
    		background: url('img/ficon.png') -5px -92px;
		}
        /*改過*/
		.feri_good{
			position:absolute;
			width:40px;
			height:40px;
			background: url('img/ficon.png') -45px -52px;
		}
        /*改過*/
		.feri_good:hover {
    		background: url('img/ficon.png') -45px -145px;
		}
        /*改過*/
		.feri_good_n{
			position:absolute;
			width:40px;
			height:40px;
			background: url('img/ficon.png') -45px -8px;
		}
        /*改過*/
		.feri_good_n:hover {
    		background: url('img/ficon.png') -45px -94px;
		}
        .feri_home{
            position:absolute;
            width:32px;
            height:31px;
            background: url('./img/feri2.png') -72px -45px;
        }
        .feri_home:hover {
            background: url('./img/feri2.png') -72px -130px;
        }

		.forum_exp_img{
			position:absolute;
			width:55px;
			height:61px;
			background: url('./img/forum.png') 0px 0px;
		}
		.forum_exp_img:hover {
    		background: url('./img/forum.png') 0px -61px;
		}
		.score_exp_img{
			position:absolute;
			width:55px;
			height:61px;
			background: url('./img/cpupl.png') 0px 0px;
		}
		.score_exp_img:hover {
    		background: url('./img/cpupl.png') 0px -61px;
		}
		.coin_img{
			position:absolute;
			width:55px;
			height:61px;
			background: url('./img/coin.png ') 0 0;
		}
		.coin_img:hover {
    		background: url('./img/coin.png ') 0 -61px;
		}



		#opinion_btn{
			position:absolute;
			width:183px;
			height:130px;
			background: url('img/package_line.png') 0 0;
		}
		#opinion_btn:hover {
    		background: url('img/package_line.png') 0 -130px;
		}
        #brownArea{
            position:absolute;
            top:-38px;
            left:-36px;
            height:28px;
            width:450px;
            background-color: #d19348;
            box-shadow: 0px 1px #333;


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
            box-shadow: 1px 0px #333;
        }
        #name{
            font-family:"微軟正黑體","sans-serif","黑體-繁","新細明體","Ariel";
        }

        #bookstoreBtn,#bookstoreRecBtn{
            top:0px; 
            left:200px; 
            width:110px; 
            height:35px; 
            text-align: center; 
            z-index:10002; 
            border:2px solid #7db9e8;
            background-color:#2989d8; 
            cursor:pointer; 
            display:inline-block;
            /* margin-left:70px; */
            margin-right:3px;
            margin-bottom: 10px;
            border-radius: 5px;
            color: #fff;
        }
        #forumBtn,#forumBookBtn{

            top:0px; left:250px; 
            width:110px; 
            height:35px; 
            text-align: center;
            z-index:10002; 
            border:2px solid #7db9e8;
            background-color:#2989d8;
            cursor:pointer; 
            display:inline-block;
            margin-left:40px;
            margin-right:3px;
            margin-bottom: 10px;
            border-radius: 5px;
            color: #fff;
        }

        .score_1{
            width: 130px;
            height: 130px;
            float:left;
            background:url('img/all.png') 0 0px;
           

        }
        .score_2{
            width: 130px;
            height: 130px;
            float:left;
            background:url('img/all.png') 250px 0px;
           

        }
        .score_3{
            width: 130px;
            height: 130px;
            float:left;
            background:url('img/all.png') 120px 0px;
           

        }


	</style>
	</Head>
	<body bgcolor="#C0895A">
	<!--==================================================
    遮罩內容
    ====================================================== -->
	<div id="cover" style="position:absolute; top:-8px; left:-8px; display:none;">
    	<div onClick="" style="position:relative; top:0px; left:0px; height:500px; width:1000px; cursor:wait; background-color:#000; opacity:0.7; z-index:9999"></div>
        <table width="550"   border="0" cellspacing="0"  style="position:absolute; top:50%; left:50%; transform: translateX(-50%)translateY(-50%);   text-align: center; z-index:10000;">
        	<tr height="90">
            	<td width="500"  valign="center" id="cover_text" style="text-align: center;" class="cover_box" >正在讀取中請稍後...

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
    <!-- 背景 -->
	<div style="position:absolute; top:-36px; left:-10px; width:1000px; height:480px;">
    	<img src="./img/bookstort_back2.png" style="position:absolute; top:28px; left:2px;">˙0
        <img id="l_door" src="./img/bookstort_door_line.png" style="position:absolute; top:28px; left:3px;display:none;">
        <!-- 背景層1 -->

        <img id="book_16_img" src="./img/B8.png" style="position:absolute; top:124px; left:139px;display:none;" border="0">
        <img id="book_15_img" src="./img/B1.png" style="position:absolute; top:124px; left:354px;display:none;" border="0">
        <img id="book_14_img" src="./img/B2.png" style="position:absolute; top:133px; left:333px;display:none;" border="0">
        <img id="book_13_img" src="./img/B3.png" style="position:absolute; top:139px; left:301px;display:none;" border="0">
        <img id="book_11_img" src="./img/A7.png" style="position:absolute; top:143px; left:813px;display:none;" border="0">
        <img id="book_10_img" src="./img/A6.png" style="position:absolute; top:113px; left:813px;display:none;" border="0">

        <img id="book_12_img" src="./img/A10.png" style="position:absolute; top:175px; left:813px;display:none;" border="0">
        <img id="book_9_img" src="./img/A5.png" style="position:absolute; top:79px; left:813px;display:none;" border="0">

        <!-- 背景層2 -->

        <img id="book_6_img" src="./img/A7.png" style="position:absolute; top:144px; left:674px;display:none;"  border="0">
        <img id="book_5_img" src="./img/A6.png" style="position:absolute; top:113px; left:675px;display:none;"  border="0">
        <img id="book_7_img" src="./img/A8.png" style="position:absolute; top:144px; left:759px;display:none;" border="0">
        <img id="book_8_img" src="./img/A9.png" style="position:absolute; top:77px; left:759px;display:none;" border="0">

     	<img id="r_box" src="./img/bookstort_box_1_line.png" style="position:absolute; top:64px; left:590px;display:none;"  border="0">

        <img id="book_4_img" src="./img/A5.png" style="position:absolute; top:79px; left:674px;display:none;"class="flipx" border="0">
        <img id="book_1_img" src="./img/A5.png" style="position:absolute; top:79px; left:609px;display:none;"  border="0">
        <img id="book_2_img" src="./img/A6.png" style="position:absolute; top:113px; left:609px;display:none;" border="0">
        <img id="book_3_img" src="./img/A7.png" style="position:absolute; top:145px; left:609px;display:none;" border="0">
        <!-- 背景層3 -->
        <img id="cluck" src="./img/0.png" style="position:absolute; top:115px; left:686px;">
    <!--
        <img id="m_1" src="./img/m_1.png" style="position:absolute; top:284px; left:722px;">
        <img id="m_2" src="./img/m_2.png" style="position:absolute; top:284px; left:722px;">-->

        <img id="l_box"  src="./img/bookstort_box_0_line.png"  style="position:absolute; top:158px; left:83px;display:none;" border="0">

   		<img id="up_book_1_img" src="./img/book_1.png"  style="position:absolute; top:151px; left:258px;display:none;" border="0">
        <img id="up_book_2_img" src="./img/book_1.png"  style="position:absolute; top:151px; left:294px;display:none;" border="0">
        <img id="up_book_3_img" src="./img/book_1.png"  style="position:absolute; top:150px; left:330px;display:none;" border="0">
        <img id="up_book_4_img" src="./img/book_1.png"  style="position:absolute; top:175px; left:252px;display:none;" border="0">
        <img id="up_book_5_img" src="./img/book_1.png"  style="position:absolute; top:177px; left:291px;display:none;" border="0">
        <img id="up_book_6_img" src="./img/book_1.png"  style="position:absolute; top:176px; left:328px;display:none;" border="0">
        <img id="up_book_7_img" src="./img/book_1.png"  style="position:absolute; top:201px; left:251px;display:none;" border="0">
        <img id="up_book_8_img" src="./img/book_1.png"  style="position:absolute; top:201px; left:293px;display:none;" border="0">
        <img id="up_book_9_img" src="./img/book_1.png"  style="position:absolute; top:203px; left:326px;display:none;" border="0">
        <img id="up_book_10_img" src="./img/book_1.png"  style="position:absolute; top:227px; left:251px;display:none;" border="0">
     	<img id="up_book_11_img" src="./img/book_1.png"  style="position:absolute; top:229px; left:289px;display:none;" border="0">
        <img id="up_book_12_img" src="./img/book_1.png"  style="position:absolute; top:226px; left:323px;display:none;" border="0">
        <img id="up_book_13_img" src="./img/book_1.png"  style="position:absolute; top:254px; left:250px;display:none;" border="0">
        <img id="up_book_14_img" src="./img/book_1.png"  style="position:absolute; top:255px; left:285px;display:none;" border="0">
        <img id="up_book_15_img" src="./img/book_1.png"  style="position:absolute; top:253px; left:324px;display:none;" border="0">

        <img id="up_book_16_img" src="./img/book_1.png"  style="position:absolute; top:151px; left:145px;display:none;" border="0">
        <img id="up_book_17_img" src="./img/book_1.png"  style="position:absolute; top:151px; left:174px;display:none;" border="0">
        <img id="up_book_18_img" src="./img/book_1.png"  style="position:absolute; top:150px; left:206px;display:none;" border="0">
        <img id="up_book_19_img" src="./img/book_1.png"  style="position:absolute; top:175px; left:137px;display:none;" border="0">
        <img id="up_book_20_img" src="./img/book_1.png"  style="position:absolute; top:177px; left:169px;display:none;" border="0">
        <img id="up_book_21_img" src="./img/book_1.png"  style="position:absolute; top:176px; left:202px;display:none;" border="0">
        <img id="up_book_22_img" src="./img/book_1.png"  style="position:absolute; top:201px; left:132px;display:none;" border="0">
        <img id="up_book_23_img" src="./img/book_1.png"  style="position:absolute; top:201px; left:166px;display:none;" border="0">
        <img id="up_book_24_img" src="./img/book_1.png"  style="position:absolute; top:203px; left:196px;display:none;" border="0">
        <img id="up_book_25_img" src="./img/book_1.png"  style="position:absolute; top:227px; left:125px;display:none;" border="0">
     	<img id="up_book_26_img" src="./img/book_1.png"  style="position:absolute; top:229px; left:163px;display:none;" border="0">
        <img id="up_book_27_img" src="./img/book_1.png"  style="position:absolute; top:227px; left:193px;display:none;" border="0">
        <img id="up_book_28_img" src="./img/book_1.png"  style="position:absolute; top:254px; left:120px;display:none;" border="0">
        <img id="up_book_29_img" src="./img/book_1.png"  style="position:absolute; top:255px; left:155px;display:none;" border="0">
        <img id="up_book_30_img" src="./img/book_1.png"  style="position:absolute; top:255px; left:189px;display:none;" border="0">

        <img src="./img/G1.png"  style="position:absolute; top:143px; left:619px;" border="0">
        <!-- 店長嘴砲層 -->
        <div id = "talk_bar" style="position: absolute; top:123px; left:425px;display:none" >
        	<div id="talk_text" style="position: absolute; top:0px; left:-27px; width:246px; word-break:break-all;" class="cover_box"></div>
        	<img src="./img/tatalk.png"  style="position:absolute; top:-3px; left:236px;" border="0">
   	  </div>
<!-- 按鈕圖層 -->
   	  <div style="position: absolute; top:382px; left:0px; width:1033px; height:59px;">
          <img src="./img/UI_2t.png" style="position:absolute; top:0px;left:-20px;">
            <!--  <img src="./img/the_1.png" style="position:absolute; top:-53px; left:182px;"> -->
           <!-- 改過 -->

            <div id="brownArea" ></div>
            <!-- 名字 -->
          <div id="name" style="position:absolute; top:-45px; left:-5px; width: 345px; height: 58px; text-align: center; white-space:nowrap; overflow:hidden; font-size:29px;" class="number_bar"></div>

       	  <div id="coin"  style="position:absolute; top:22px; left:127px; width: 114px; height: 42px;display:none;" class="number_bar2">0</div>
	      <div id="score_exp" style="position:absolute; top:22px; left:-16px; width: 125px; height: 42px;display:none;" class="number_bar2">0</div>
	      <div id="forum_exp" style="position:absolute; top:-1px; left:272px; width: 125px; height: 42px;display:none;" class="number_bar2">0</div>

          <img src="./img/UI_2t_cover.png" id="bar_cover1" style="position:absolute; top:8px; left:142px; width: 125px; height: 42px;">
          <img src="./img/UI_2t_cover.png" id="bar_cover2" style="position:absolute; top:8px; left:262px; width: 125px; height: 42px;">
    <!-- lv -->
          <div id="lv" style="position:absolute; top:-3px; left:50px; width: 82px; height: 42px; font-size:16px; text-align:left;" class="number_bar2"></div>

          <a id="coin_imgs" class="coin_img" onClick="cover('<a style=\'color:#903;\'>葵幣:購買裝飾品的貨幣</a><BR>做推薦與販賣書籍獲得，也可由教師給予',1)" style="cursor:pointer; position:absolute; top:-4px; left:127px;display:none;" border="0"></a>
          <a id="score_exp_img" class="score_exp_img" onClick="cover('<a style=\'color:#903;\'>經驗值:經營書店的經驗值</a><BR>越努力經營書店數值越高',1)"   style="cursor:pointer; position:absolute; top:-4px; left:-11px;display:none;" border="0"></a>
          <a id="forum_exp_img" class="forum_exp_img" onClick="cover('<a style=\'color:#903;\'>聊書經驗值:經營聊書的經驗值</a><BR>越努力進行聊書數值越高',1)"   style="cursor:pointer; position:absolute; top:-4px; left:259px;display:none;" border="0"></a>


        <!-- 登記icon -->
          <a id="btn6" class="btn_rbook" onClick="go_op()" style="position:absolute; top:-16px; left:430px; cursor:pointer;"></a>
        <!-- 推薦icon -->
          <a id="btn2" class="btn_rec" onClick="set_page('page_rec_menu');set_action_bookstore_log(user_id,'e2',action_on);" style="position:absolute; top:-16px; left:510px; cursor:pointer;"></a>
        <!-- 上架icon -->
          <a id="btn1" class="btn_shelf" onClick="set_page('page_shelf_menu');set_action_bookstore_log(user_id,'e3',action_on);" style="position:absolute; top:-16px; left:590px; cursor:pointer;"></a>
        <!-- 報表icon -->
          <a id="btn3" class="btn_post" onClick="set_page('page_post_menu');set_action_bookstore_log(user_id,'e23',action_on);" style="position:absolute; top:-16px; left:663px; cursor:pointer;display:none;"></a>

          <a id="btn5" class="btn_help" onClick="set_page('page_help');set_action_bookstore_log(user_id,'e5',action_on);" style="position:absolute; top:-46px; left:900px; cursor:pointer; display:none;"></a>
        <!-- 設定icon -->
          <a id="options_btn" class="btn_set" onClick="set_page('page_options');set_action_bookstore_log(user_id,'e28',action_on);" width="75"  style="position:absolute; cursor:pointer; top:-16px; left:740px;display:none;" ></a>
        <!-- 進聊書icon -->
          <a id="btn_talk" class="btn_talk" onClick="go_talk(<?php echo $user_id ?>)" style="position:absolute; top:-16px; left:815px; cursor:pointer;display:none;"></a>
        <!-- 離開icon -->
   	   	  <a  class="btn_out" onClick="go_outside()" width="75"  style="position:absolute; cursor:pointer; top:-16px; left:890px;" ></a>
          <a id="opinion_btn"  onClick="set_page('page_opinion_menu');set_action_bookstore_log(user_id,'e4',action_on);" border="0"   style="position:absolute; top:-163px; left:403px; cursor:pointer;display:none;" ></a>


   	  </div>
        <!-- 觸發層 !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! -->
      <div onClick="set_page('page_rec_menu');set_action_bookstore_log(user_id,'e2',action_on);" onMouseOver="{window.document.getElementById('r_box').style.display = 'block';}" onMouseOut="{window.document.getElementById('r_box').style.display = 'none';}" style="position: absolute; top:71px; left:593px; width:291px; height:201px; cursor:pointer; background:#FFF; opacity:0;"></div>
	  <div onClick="set_page('page_shelf_menu');set_action_bookstore_log(user_id,'e3',action_on);" onMouseOver="{window.document.getElementById('l_box').style.display = 'block';}" onMouseOut="{window.document.getElementById('l_box').style.display = 'none';}"  style="position: absolute; top:169px; left:113px; width:272px; height:146px; cursor:pointer; background:#FFF; opacity:0;"></div>
      	<div id='goout' onClick="go_outside()" style="position: absolute; top:53px; left:3px; width:94px; height:286px; cursor:pointer; background:#FFF; opacity:0;"  onMouseOver="{window.document.getElementById('l_door').style.display = 'block';}" onMouseOut="{window.document.getElementById('l_door').style.display = 'none';}" ></div>

        <!-- 改過 -->
      <div id="other_store" style="position:absolute; top:328px; left:216px;display: none;">
               <!-- 改過 -->
              <!--  <img src="./img/firend_bar.png" style="position:absolute; top:-141px; left: -231px;"> -->
               <!-- 改過 -->
               <a id="feri_friend" class="feri_friend" onClick="set_track()" style="cursor:pointer; position:absolute; top:10px; left:120px;display:none;"></a>
               <!-- 改過 -->
               <a id="feri_good" class="feri_good" onClick="set_good()" style="cursor:pointer; position:absolute;  top:12px; left:164px;display:none;"></a>
                <!-- 改過 -->
                <!--  <a class="feri_home" style="cursor:pointer; position:absolute; top:-40px; left:-210px;" onClick="cover('是否要回到自己家',2,function(){set_action_bookstore_log(user_id,'e37',action_on);back_home();})"></a> -->
      </div>
      	<a id="PC_hh" class="PC_hh" style="position:absolute; top:46px; left:891px; display:none; cursor:pointer;" onClick="go_talk()" ></a>

      	<img id="PC_t" src="img/go_fum.png" style="position:absolute; top:107px; left:833px; display:none; cursor:pointer;" onClick="go_talk()" >
        <a id="PC_b" class="pc" style="position:absolute; top:127px; left:766px; display:none; cursor:pointer;" onClick="go_talk()" ></a>
        <img src="img/Pc.gif"  id="PC_gif" style="position:absolute; display:none; top:144px; left:793px; width: 29px; height: 50px;">
        <!-- 改過 -->
		<a id="PC" class="pc_l" style="position:absolute; top:127px; left:766px; display:none; cursor:pointer;"  onClick="set_page('page_post_menu');set_action_bookstore_log(user_id,'e23',action_on);"  ></a>
      <!-- 追加的錶功能 -->
        <div id="other_ifame" style="position:absolute; left:0px; top:36px;">
      </div>



</div>
    <!-- 改過 -->
    <a class="btn_help" onClick="open_helper(13)" style="position:absolute; top:-6px; left:890px; cursor:pointer;"></a>
	<div id="helper" style="position:absolute; top:-17px; left:-17px; width:1100px; height:520px; display:none; overflow:hidden;"></div>
    <!-- 說明頁面 -->
        <!-- 改過 -->
    <img id="book_store_help" onClick="close_help()" src="./img/book_store_help.png" style="position:absolute; top:-41px; left:-75px; cursor:pointer; z-index:999" border="0">
	<!-- 內頁內容 -->

<div id="iframe" style="position:absolute;top:-8px;left:-8px;z-index:999"></div>



<!--==================================================
    debug內容
    ====================================================== -->
<div id="debug" style="position:absolute;top:500px;"></div>

</body>


    <script>
//console.log($(document).width());
//console.log($(window).width());


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
	var action_on = 0;
	var book_sid = '<?php if(isset($_GET["book_sid"]))echo $_GET["book_sid"];?>';
	var clerk_talk = new Array("你好 歡迎光臨","來買書喔！","推薦做了嗎？<BR>快來做喔","我後面的櫃子可以點喔","要出去了嗎?你可以點大門走出去","外面有我精心布置的花園喔");
	if(home_id != user_id)
	{
		home_on = 'other';
	}
	var user_permission = '<? echo $permission;?>';

	var status = new Array();
	var coin = 0;
	var score_exp = 0;
	var name = "";
	var sex = 0;
	var forum_exp = 0;
	//好友與按讚
	var have_track = 0;
	var btn_track_type = "";
	var have_good = 0;
	var btn_good_type = "";

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
	var auth_open_publish_cno = 10;
	//暫存列
	var click_book_sid="";//暫存點選用SID
	var click_book_name="";//暫存點選用書名
	var click_book_star_1=0;//暫存點選的分數1
	var click_book_star_2=0;//暫存點選的分數1
	var click_book_star_3=0;//暫存點選的分數1
	//聊書開關
	var forum_flag = "<? if($forum_flag) echo "y";?>";

	//關閱用
	var read_max_count=0;
	var read_on=1;
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

			window.document.getElementById("cover_btn_1").style.left = "223px";
			window.document.getElementById("cover_btn_1").style.display = "block";
			window.document.getElementById("cover_btn_1").className = "no_box";
			window.document.getElementById("cover_btn_1").innerHTML = "不存檔";
			window.document.getElementById("cover_btn_2").style.left = "430px";
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
			window.document.getElementById("cover_btn_1").style.left = "100px";
			window.document.getElementById("cover_btn_1").style.display = "block";
			window.document.getElementById("cover_btn_1").className = "ok_box";
			window.document.getElementById("cover_btn_1").innerHTML = "確定";
			window.document.getElementById("cover_btn_2").style.left = "320px";
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
			window.document.getElementById("cover_btn_1").style.left = "210px";
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

        // console.log(forum_flag);
        
		$.post(url, {
					user_id:user_id,
					home_id:home_id,
					user_permission:user_permission,
                    forum_flag:forum_flag



			}).success(function (data)
			{

                // console.log(data);return false;
                
				echo("AJAX:success:main():讀取使用者資料:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通資系統人員",1);
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
					window.document.getElementById("name").innerHTML = name+"的書店";
					sex = data_array["user_sex"];
					if(sex == 1)
					{
						window.document.getElementById("cluck").src="./img/man_2.png";
					}
					else
					{
						window.document.getElementById("cluck").src="./img/gril_2.png";
					}



					auth_open_publish = data_array["auth_open_publish"];
                    // console.log(auth_open_publish);
					auth_read_opinion_limit_day = data_array["auth_read_opinion_limit_day"];
					auth_rec_en_input = data_array["auth_rec_en_input"];
					auth_rec_draw_open = data_array["auth_rec_draw_open"];
					auth_coin_open = data_array["auth_coin_open"];
					auth_open_publish_cno = data_array["auth_open_publish_cno"];

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

					}else
					{

						if(data_array["forum_open"])
						{
							window.document.getElementById("bar_cover1").style.display = "none";
							window.document.getElementById("forum_exp").style.left = "134px";
							window.document.getElementById("forum_exp_img").style.left = "127px";
						}
					}
					set_other_iframe();
					set_coin(0);
					set_score_exp(0);
					get_track_have();
					now_i_talking();//說話啊笨蛋
					window.document.getElementById("talk_text").innerHTML =clerk_talk[Math.floor(Math.random()*clerk_talk.length)];
					window.document.getElementById("talk_bar").style.display = "block";
					get_shelf();
					<?php if($forum_flag){?>
						window.document.getElementById("btn_talk").style.display = "block";
						window.document.getElementById("PC").style.display = "block";
						window.document.getElementById("PC_t").style.display = "block";
						window.document.getElementById("PC_b").style.display = "block";
						window.document.getElementById("PC_gif").style.display = "block";


					<?php }?>
					//set_action_bookstore_log(user_id,'e1',action_on);//action_log
					if(open_fun!=""){
						set_page(open_fun);
						close_help();
						}
					<? if(isset($_GET["book_sid"])){?>
					set_page('page_shelf_menu');
					<? } ?>
				}
			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
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
					window.alert("資料庫好像有點問題呢，請再試試看<BR>或著通資系統人員");
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
							window.document.getElementById("up_book_"+i+"_img").src = "./img/book_"+score+".png";
						}
						else
						{
							window.document.getElementById("up_book_"+i+"_img").src = "./img/book_1.png";
						}
					}


					get_read();
				}
			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
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
					window.alert("資料庫好像有點問題呢，請再試試看<BR>或著通資系統人員");
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
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
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
					window.alert("資料庫好像有點問題呢，請再試試看<BR>或著通資系統人員");
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
				cover("");
				window.alert("連線失敗請重新讀取/存檔");

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
		coin = Math.floor(coin) + value;
		coin = Math.floor(coin);
		window.document.getElementById("coin").innerHTML = coin ;
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
	//開啟子功能頁面
	function set_page(value)
	{
		echo("set_page:開啟畫面 value>"+value);
		if(value !="")
		{
			window.document.getElementById("other_ifame").style.display = "none";
			window.document.getElementById("goout").style.display = "none";
			cover("讀取中");
			window.document.getElementById("iframe").innerHTML = '<iframe id="" src="./'+value+'/index.php" width="1000" height="500" scrolling="no" frameborder="0" style=" top:0px; left:0px; overflow:hidden;"></iframe>';
		}
		else
		{
			cover("讀取中");
			window.document.getElementById("other_ifame").style.display = "block";
			window.document.getElementById("goout").style.display = "block";
			get_shelf();
			coin_coin();
			window.document.getElementById("iframe").innerHTML = '';
		}
	}
	//單純讀取$$
	function coin_coin()
	{
		echo("Main:初始開始:讀取使用者資料");
		cover("讀取使用者資料中")
		var url = "./ajax/get_mssr_user_coin.php";
		$.post(url, {
					user_id:user_id,
					home_id:home_id,
					user_permission:user_permission

			}).success(function (data)
			{
				echo("AJAX:success:main():讀取使用者資料:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通資系統人員",1);
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
					set_coin(0);

				}
			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){
				echo("AJAX:complete:main():讀取使用者資料:");
			});
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

		cover("確定要前往閱讀登記嗎?",2,function(){set_action_bookstore_log(user_id,'e22',action_on);window.location.href = '../read_the_registration_v2/index.php';});
	}
	function go_talk()
	{
		cover("確定要前往聊書嗎?",2,function(){
		    set_action_bookstore_log(user_id,'e41',action_on);
            // 原本
            window.parent.location.href = '<?php echo $forum_href;?>';

            // window.open ('<?php echo $forum_href;?>');
            //window.open("../_dev_forum_eric_default/view/user.php?user_id=<?php echo $user_id ?> &tab=1");

        });
	}
	//設定星球讚
	function set_good()
	{
		cover("讚!!送出中");
		echo("set_track:初始開始:設定星球讚+"+btn_good_type);
		if(btn_good_type=="") return false;
		var tmp = btn_good_type;
		btn_good_type = "";
		var url = "page_other_store_info/ajax/set_star.php";
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
				echo("AJAX:error:set_good():設定星球讚:");

			}).complete(function(e){
				echo("AJAX:complete:set_good():設定\星球讚:");
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
		var url = "page_other_store_info/ajax/set_track.php";
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
						cover("成功加入喜愛的書店",1);
					}else
					{
						have_track = 0;
						window.document.getElementById("feri_friend").className = "feri_friend_n";
						btn_track_type = "add";
						cover("成功刪除喜愛的書店",1);
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

		var url = "page_other_store_info/ajax/get_track_have.php";
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

	//debug
	function echo(text)
	{
		if(0)window.document.getElementById("debug").innerHTML = text+"<br>"+window.document.getElementById("debug").innerHTML;
	}
	function set_other_iframe()
	{
		if(home_on == "other")
		{
			action_on = 2 ;
			echo("啟動去別人家的功能");
			window.document.getElementById("other_store").style.display = "block";
			//window.document.getElementById("other_ifame").innerHTML = window.document.getElementById("other_ifame").innerHTML+'<iframe src="page_other_store_info/index.php?home_id='+home_id+'" frameborder="0" height="300" width="550" style="position:absolute; left:0px; top:0px; width: 551px; height: 117px;"></iframe>';

		}else if(home_on =="user")
		{
			action_on = 1 ;
			echo("啟動自己家的功能");
			window.document.getElementById("other_store").style.display = "none";
			window.document.getElementById("btn3").style.display = "block";
			window.document.getElementById("options_btn").style.display = "block";
			window.document.getElementById("other_ifame").innerHTML = window.document.getElementById("other_ifame").innerHTML+'<iframe src="page_msg_menu/index.php" frameborder="0" height="300" width="550" style="position:absolute; left:0px; top:0px; width: 551px; height: 117px;" id="pageMsgMenuIframe"></iframe>';

		}
		window.document.getElementById("other_ifame").innerHTML = window.document.getElementById("other_ifame").innerHTML+'<iframe name="page_track_menu" src="page_track_menu/index.php" frameborder="0" height="300" width="550" style="position:absolute; left:0px; top:400px; width: 1000px; height: 80px;"></iframe>';
	}
	//交談器

	function now_i_talking()
	{
		var x = 5000;
		var hnd = window.setInterval(function ()
		{
			clerk_talk.length
			window.document.getElementById("talk_text").innerHTML =clerk_talk[Math.floor(Math.random()*clerk_talk.length)];

			if(Math.floor(Math.random()*3))
			{
				window.document.getElementById("talk_bar").style.display = "block";
				//window.document.getElementById("m_2").style.display = "none";
			}
			else
			{
				window.document.getElementById("talk_bar").style.display = "none";
				//window.document.getElementById("m_2").style.display = "block";
			}
		}, x);
	}
	function back_home()
	{
		window.location.href="./bookstore_courtyard/index.php";
		;
	}
	//---------------------------------------------------
    //helper
    //---------------------------------------------------
	function open_helper(value)
	{
		window.document.getElementById("helper").innerHTML="<iframe src='page_helper/index.php?id="+value+"' style='position:absolute; top:0px; left:0px; width:1050px; height:590px;  overflow:hidden;' frameborder='0'></iframe>";
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
	$.ajaxSetup({
		timeout: 15*1000
	});
	main();

    </script>
</Html>