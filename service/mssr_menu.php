<?php
//---------------------------------------------------
//設定與引用
//---------------------------------------------------

	//SESSION
	@session_start();

	//啟用BUFFER
	@ob_start();

	//外掛設定檔
	require_once(str_repeat("../",1)."/config/config.php");

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
//END   設定與引用
//---------------------------------------------------

    //建立連線 user
    $conn_user          =conn($db_type='mysql',$arry_conn_user);
    if(isset($_SESSION['permission'])){$sess_permission=addslashes(trim($_SESSION['permission']));}else{ $sess_permission='';}
    if(isset($_SESSION['name'])){$sess_name=addslashes(trim($_SESSION['name']));}else{ $sess_name='';}
    $forum_flag         =false;
    $forum_global_href  ='';
    $forum_href         ='forum/view/index.php';
    $sess_uid           =0;
    if(isset($_SESSION['uid']))$sess_uid=(int)$_SESSION['uid'];

    $sql="
        SELECT `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_id`
        FROM `mssr_forum_global`.`mssr_forum_group_user_rev`
        WHERE 1=1
            AND `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_id`={$sess_uid}
            AND `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_country_code`='tw'
    ";
    $arrys_result=db_result($conn_type='pdo','',$sql,$arry_limit=array(),$arry_conn_mssr);
    if(!empty($arrys_result))$forum_global_href  ='http://www.cot.org.tw/mssr/service/forum_global/view/login.php';
    //echo "<Pre>";print_r($forum_global_href);echo "</Pre>";

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

    if(isset($_SESSION['class'][0][1])){
        if(in_array(trim($_SESSION['class'][0][1]),$arry_1)){
            $forum_href='_dev_forum_eric_achievement/view/index.php';
        }
        if(in_array(trim($_SESSION['class'][0][1]),$arry_2)){
            $forum_href='_dev_forum_eric_mission/view/index.php';
        }
    }
    if(isset($_SESSION['class'][0][1]) && isset(explode("_",$_SESSION['class'][0][1])[0])){
        $sess_school_code=explode("_",$_SESSION['class'][0][1])[0];
        if(in_array($sess_school_code, $arry_3)){
            $forum_href='_dev_forum_eric_mission/view/index.php';
        }
        if(in_array($sess_school_code, $arry_4)){
            $forum_href='_dev_forum_eric_mission/view/index.php';
        }
        if(in_array($sess_school_code, $arry_5)){
            $forum_href='_dev_forum_eric_achievement/view/index.php';
        }
    }
    if(isset($_SESSION['class'][0][1])){
        if(trim($_SESSION['class'][0][1])===trim('lrb_2015_2_4_2_2')){
            $forum_href='_dev_forum_eric_achievement/view/index.php';
        }
    }

    $u_sb_flag =false;
    $u_pbr_flag=false;
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
            if($rs_status==='u_mssr_forum' || $rs_status==='i_a'){$forum_flag=true;}
            if($rs_status==='u_sb'){$u_sb_flag=true;}
            if($rs_status==='u_pbr'){$u_pbr_flag=true;}
        }
    }

    if(isset($_SESSION['class'][0][1])){
        $sess_class=trim($_SESSION['class'][0][1]);
        $sql="
            SELECT `country_code`,grade
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
            $_SESSION['grade']=trim($db_results[0]['grade']);
            $grade=$_SESSION['grade'];
            if($sess_country_code!=='tw'){
                $forum_flag     =false;
            }
        }
    }

    //導向正式版
    $forum_href='forum/view/index.php';



    //----------------
    //尋找school_code
    //----------------


    if(isset($_SESSION['uid'])){
       
        $sql="
            SELECT `school_code`
            FROM `member_school`
            WHERE 1=1
                AND `member_school`.`uid`='{$_SESSION['uid']}'
        ";

        $db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);

        if(!empty($db_results)){

            $_SESSION['school_code']=$db_results[0]['school_code'];
            $school_code=$_SESSION['school_code'];
           
        }



    }

    //----------------
    //尋找年級
    //----------------

    // if(isset($_SESSION['uid'])){
       
    //     $sql="
    //         SELECT `grade`
    //         FROM `class`
    //         WHERE 1=1
    //             AND `class`.`class_code`='{$sess_class}'
    //     ";

    //     $db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);

    //     if(!empty($db_results)){

    //         $_SESSION['grade']=$db_results[0]['grade'];
    //         $grade=$_SESSION['grade'];
           
    //     }



    // }


    // print_r($_SESSION);





?>
<!DOCTYPE HTML>
<Html lang="zh_TW">
<Head>
    <Title>明日閱讀</Title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="Content-Language" content="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
    <meta name="description" content="明日星球,中央大學明日星球" />
    <meta name="keywords" content="明日星球,中央大學明日星球" />
    <link rel="stylesheet" type="text/css" href="css/mssr_menu.css">
    <style>
        .content_img {
            float: left;
            border: 0;
        }
        table {
            padding: 0;
            margin: 0 auto;
        }
        * {
          -webkit-box-sizing: border-box;
             -moz-box-sizing: border-box;
                  box-sizing: border-box;
        }

    </style>
</head>

<body>

    <table id="Content" align="center" cellpadding="0" cellspacing="0" border="0" width="960" bgcolor="#fff2c6"/>
        <!-- Header start -->
    	<tr align="center" style="font-size:12px; color:#000099;">
            <td valign="bottom" align="left" colspan="1" style="font-size:18px; font-weight:900; color:#006699">
                明日書店
            </td>
            <td valign="bottom" align="right" style="font-size:12px; color:#000099;">
                <?php echo $sess_name?> | <a href='/ac/index.php' style='color:#000099' target='_self'>回明日星球首頁</a>
            </td>
        </tr>
        <tr align="center" valign="top" border="0">
            <td colspan="2">
                <a href="../../ac/index.php" title="按此回首頁" name="按此回首頁" target="_self" border="0">
                    <img src="img/banner new(0120)_0217.png" border="0" width="100%">
                </a>
            </td>
        </tr>
        <!-- Header end -->

        <!-- Content start -->
        <tr align="center" valign="top" border="0">
            <td colspan="2">
                <div style="width:798px;margin:0 auto;padding:0;position:relative;">
                    <img src="img/sto_01.png" width="798px" height="auto" alt="明日閱讀"  border="0" class="content_img"
                    style="position:relative;top:1px;">

                    <img src="img/sto_02.png" width="101px" height="46px" alt="明日閱讀"  border="0" class="content_img">
                    <a href="code.php?mode=read_the_registration" target="_self" border="0">
                        <img src="img/sto_03.png" width="116px" height="46px" alt="明日閱讀"  border="0" class="content_img">
                    </a>

                    <img src="img/sto_04.png" width="160px" height="46px" alt="明日閱讀"  border="0" class="content_img">   

                    <?php if($school_code=='idc'&&$grade==='1'){?>
                    

                        <a href="code.php?mode=page_opinion_menu" target="_self" border="0">
                          <img src="img/sto_10.png" width="119px" height="46px" alt="回答問題"  border="0" class="content_img">
                        </a>

                    <?php }else {?>
                        <a href='code.php?mode=bookstore' target="_self" border= "0">
                            <img src="img/sto_05.png" width="119px" height="46px" alt="明日書店 " border="0" class="content_img">
                        </a>         

                    <?php }?>
                    <img src="img/sto_06.png" width="99px"  height="46px" alt="明日書店"  border="0" class="content_img">

                    <?php if($u_sb_flag):?>
                        <a href="../../draw_story/storyBooks.php" border = "0">
                            <img src="img/sto_07.png" width="118px" height="46px" alt="說書人" border="0" class="content_img">
                        </a>
                    <?php else:?>
                        <div style="width:118px; height:46px;background-color:#b9dff6;" class="content_img"></div>
                    <?php endif;?>
                    <img src="img/sto_08.png" width="85px"  height="46px" alt="說書人" border="0" class="content_img">
                </div>
            </td>
        </tr>
        <tr align="center" valign="top" border="0">
            <td colspan="2">
                <div style="width:798px;margin:0 auto;padding:0;position:relative;">
                    <img src="img/sto_09.png" width="798px" height="auto" alt="明日閱讀" border="0" class="content_img"
                    style="position:relative;bottom:1px;">
                    <?php if($forum_global_href!==''):?>
                        <a href="<?php echo $forum_global_href;?>" border="0" target='_blank'>
                            <img src="img/forum_global.png" width="137" height="59" alt="聊書國際版" border="0"
                            style="position:absolute; left:11.5%; top:75px;">
                        </a>
                    <?php endif;?>

                    <?php if($forum_flag):?>
                        <a href="<?php echo $forum_href;?>" border="0" target='_blank'>
                            <img src="img/forum.png" width="137px" height="59px" alt="聊書" border="0"
                            style="position:absolute; left:46%; top:75px;">
                        </a>
                    <?php endif;?>

                    <?php if($u_pbr_flag):?>
                        <!-- <a href="../../mssc/pbr/activity.php" border="0" target='_blank'>
                            <img src="img/pbr.png" width="137" height="59" alt="主題閱讀" border="0"
                            style="position:absolute; right:9%; top:75px;">
                        </a> -->
                    <?php endif;?>
                <!-- -->
                </div>
            </td>
        </tr>
        <!-- Content end -->
    </table>

    <!-- Footer start -->
    <table id="Footer" align="center" cellpadding="0" cellspacing="0" border="0" width="960" bgcolor="#fff2c6"/>
        <tr align="center" valign="top"  border="0">
            <td background="img/copyright.png" height="79" align="center" valign="bottom" style="font-size:12px; color:#666666">
                校址：(32001)桃園縣中壢市五權里2鄰中大路300號‧總機電話：03-4227151<br />
                國立中央大學 版權所有 &copy; 2008-2011 National Central University All Rights Reserved.
            </td>
        </tr>
    </table>
    <!-- Footer end -->

</body>
<script>
    var oContent     =document.getElementById('Content');
    var oFooter      =document.getElementById('Footer');
    var screen_width =(window.innerWidth > 0) ? window.innerWidth : screen.width;


//將狀態存入localstorage(英文版書店還是中文版書店)

if (typeof(Storage) !== "undefined") {
    // Store


    if(localStorage.getItem("state")==""){
        
          localStorage.setItem("state", "traditionalChinese");

        console.log("1",localStorage.getItem("state"));

    }
    // Retrieve 
} 

    window.onload=function(){
        if(parseInt(screen_width)<=1000){
            oContent.style.width=screen_width+'px';
            oFooter.style.width=screen_width+'px';
        }
    }


</script>
</Html>