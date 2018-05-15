<?php
//-------------------------------------------------------
//明日書店網管中心
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

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'center/admin_center/inc/code',
                    APP_ROOT.'inc/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(!login_check(array('a'))){
            $url=str_repeat("../",0).'mod/m_login/loginF.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        //初始化，承接變數
        $_sess_a=$_SESSION['a'];
        foreach($_sess_a as $field_name=>$field_value){
            if(!is_array($field_value))$$field_name=trim($field_value);
        }

        //清空用戶類型
        switch(is_array($_SESSION['config']['user_type'])){
            case true:
                foreach($_SESSION['config']['user_types'] as $inx=>$val){
                    if(!in_array(trim($val),array_map("trim",$_SESSION['config']['user_type']))){
                        unset($_SESSION[$val]);
                    }
                }
            break;
            default:
                foreach($_SESSION['config']['user_types'] as $inx=>$val){
                    if(trim($val)!=trim($_SESSION['config']['user_type'])){
                        unset($_SESSION[$val]);
                    }
                }
            break;
        }

        //清空查詢
        unset($_SESSION['a']['query']);

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,明日書店網管中心";
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=false);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../lib/php/image/verify/verify_image.js"></script>
    <script type="text/javascript" src="../../inc/code.js"></script>
    <script type="text/javascript" src="../../lib/js/flash/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="css/def.css" media="all" />
    <link rel="stylesheet" type="text/css" href="inc/code.css" media="all" />
    <script type="text/javascript" src="inc/code.js"></script>

    <!-- 本頁 -->
    <style>
        .icon{
            vertical-align:middle;
        }
        .bar1,.bar2{
            border-bottom:1px solid #e1e1e1;
            padding:0;
        }
        .bar1 span,.bar2 span{
            display:block;
            height:30px;
            padding:10px 0 0 10px;
        }
        #Tbl1{
            margin-top:0px;
        }
        #Tbl2,#Tbl3{
            margin-top:5px;
        }
    </style>
</Head>

<Body>

    <!-- header 開始 -->
    <div id="header">
        <a href="#"><img id="logo" src="img/home.gif"></a>
        <span id="title"><?php echo header_slogan();?></span>
        <ul id="navbar">
            <li><a href="#" class="current">主控台</a></li>
            <li><a href="#">說明中心</a></li>
            <li><a href="index.php">首頁</a></li>
            <li><a href="mod/m_login/logout.php">登出</a></li>
        </ul>
    </div>
    <!-- header 結束 -->

    <!-- content 開始 -->
    <div id="content">
        <table id="Tbl0" border="0" width="100%">
            <tr>
                <td width="150px" align="left" valign="top">
                    <!-- 登入資訊 -->
                    <table id="Tbl1" border="1" width="100%" class="table_style1">
                        <tr align="center" valign="center">
                            <td colspan="2" class="bg_gray2" height="30px">
                                登入資訊
                            </td>
                        </tr>
                        <tr align="center" valign="center">
                            <td class="bg_gray1" align="center" width="50px" height="20px">
                                姓名
                            </td>
                            <td>
                                <?php echo htmlspecialchars($_sess_a['name']);?>
                            </td>
                        </tr>
                        <tr align="center" valign="center">
                            <td class="bg_gray1" align="center" width="50px" height="20px">
                                帳號
                            </td>
                            <td>
                                <?php echo htmlspecialchars($_sess_a['account']);?>
                            </td>
                        </tr>
                        <tr align="center" valign="center">
                            <td class="bg_gray1" align="center" width="50px" height="20px">
                                密碼
                            </td>
                            <td>
                                <?php
                                //$url ="";
                                //$page="user_info/index.php";
                                //$arg =array(
                                //    'rnd'=>mt_rand()
                                //);
                                //
                                //$arg=http_build_query($arg);
                                //$url="{$page}";
                                ?>
                                <a href="<?php // //echo $url;?>#">重設密碼</a>
                            </td>
                        </tr>
                    </table>

                    <!-- 時鐘 -->
                    <table id="Tbl2" border="1" width="100%" class="table_style1">
                        <!-- <tr align="center">
                            <td colspan="2" class="bg_gray2" height="30px">
                                標準時間
                            </td>
                        </tr> -->
                        <tr>
                            <td id="clock" height="200px" align="center" class="bg_gray1">
                                <img src="flash/fail.gif" style="vertical-align:middle">
                                <a href="http://get.adobe.com/tw/flashplayer/" target="_blank">請下載Flash Player</a>
                            </td>
                        </tr>
                    </table>

                    <!-- 系統資訊 -->
                    <table id="Tbl3" border="1" width="100%" class="table_style1">
                        <tr align="center">
                            <td colspan="2" class="bg_gray2" height="30px">
                                系統資訊
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="bg_gray1">
                                <?php sys_info();?>
                            </td>
                        </tr>
                    </table>
                </td>

                <!-- 模組表格 -->
                <td align="left" valign="top">

                    <!-- 管理系統 -->
                    <h1 class="bar1 bg_img01" onmouseover="this.style.cursor='pointer'" onmouseout="this.style.cursor=''"
                    onclick="toggle('sTbl')">
                        <span>
                            <img id="arrow_sTbl" src='img/arrow_bottom.gif' alt='縮合圖示' style="vertical-align:middle">
                            管理系統
                            <a href="#" style="float:right;vertical-align:middle">
                                <img src="img/question.png" border="0" class="icon">
                                系統說明&nbsp;
                            </a>
                        </span>
                    </h1>
                    <table align="center" id="sTbl" border="0" width="95%">
                        <tr align="center" valign="center">
                            <td align="center" width="100px" height="100px">
                                <span style="float:left;">
                                    <a href="mod/m_verified_credit_class/index.php"><img src="img/mod/m_verified_credit_class.png" width="32" height="32" border="0"></a><br/>
                                    <span><a href="mod/m_verified_credit_class/index.php"><img src="img/ok.png" border="0" class="icon" width="16" height="16">學分班作業檢核</a></span>
                                </span>
                            </td>
                            <!-- <td width="100px" height="100px">
                                <a href="#"><img src="img/mod/m_semester_report.png" width="32" height="32" border="0"></a><br/>
                                <span><a href="#"><img src="img/fail.gif" border="0" class="icon" width="16" height="16">
                                    目前尚無任何模組!
                                </a></span>
                            </td> -->
                        </tr>
                    </table>

                    <!-- 活耀/非活耀報表 -->
                    <h1 class="bar2 bg_img01" onmouseover="this.style.cursor='pointer'" onmouseout="this.style.cursor=''"
                    onclick="toggle('mTbl3')" style="margin-bottom:25px;">
                        <span>
                            <img id="arrow_mTbl3" src='img/arrow_up.gif' alt='縮合圖示' style="vertical-align:middle">
                            活耀/非活耀報表
                            <a href="#" style="float:right;vertical-align:middle">
                                <img src="img/question.png" border="0" class="icon">
                                系統說明&nbsp;
                            </a>
                        </span>
                    </h1>
                    <table align="center" id="mTbl3" border="0" width="95%" style='display:none;'>
                        <tr align="left" valign="center">
                            <td align="center" width="" height="100">
                                <span style="float:left;">
                                    <a href="mod/m_all_sys_analysis/index.php"><img src="img/mod/m_teacher_report.png" width="32" height="32" border="0"></a><br/>
                                    <span><a href="mod/m_all_sys_analysis/index.php"><img src="img/ok.png" border="0" class="icon" width="16" height="16">全系統使用分析(非活耀)</a></span>
                                </span>
                            </td>
                            <td align="center" width="" height="100">
                                <span style="float:left;">
                                    <a href="mod/m_bookstore_analysis/index.php"><img src="img/mod/m_teacher_report.png" width="32" height="32" border="0"></a><br/>
                                    <span><a href="mod/m_bookstore_analysis/index.php"><img src="img/ok.png" border="0" class="icon" width="16" height="16">閱讀使用分析(非活耀)</a></span>
                                </span>
                            </td>
                            <td align="center" width="" height="100">
                                <span style="float:left;">
                                    <a href="mod/m_forum_analysis/index.php"><img src="img/mod/m_teacher_report.png" width="32" height="32" border="0"></a><br/>
                                    <span><a href="mod/m_forum_analysis/index.php"><img src="img/ok.png" border="0" class="icon" width="16" height="16">聊書使用分析(非活耀)</a></span>
                                </span>
                            </td>
                        </tr>
                        <tr align="left" valign="center">
                            <td align="center" width="" height="100">
                                <span style="float:left;">
                                    <a href="mod/m_bookstore_active_analysis/index.php"><img src="img/mod/m_teacher_report.png" width="32" height="32" border="0"></a><br/>
                                    <span><a href="mod/m_bookstore_active_analysis/index.php"><img src="img/ok.png" border="0" class="icon" width="16" height="16">閱讀使用分析(活耀)</a></span>
                                </span>
                            </td>
                            <td align="center" width="" height="100">
                                <span style="float:left;">
                                    <a href="mod/m_forum_active_analysis/index.php"><img src="img/mod/m_teacher_report.png" width="32" height="32" border="0"></a><br/>
                                    <span><a href="mod/m_forum_active_analysis/index.php"><img src="img/ok.png" border="0" class="icon" width="16" height="16">聊書使用分析(活耀)</a></span>
                                </span>
                            </td>
                        </tr>
                    </table>

                    <!-- 其他報表 -->
                    <h1 class="bar2 bg_img01" onmouseover="this.style.cursor='pointer'" onmouseout="this.style.cursor=''"
                    onclick="toggle('mTbl1')" style="margin-bottom:25px;">
                        <span>
                            <img id="arrow_mTbl1" src='img/arrow_up.gif' alt='縮合圖示' style="vertical-align:middle">
                            其他報表
                            <a href="#" style="float:right;vertical-align:middle">
                                <img src="img/question.png" border="0" class="icon">
                                系統說明&nbsp;
                            </a>
                        </span>
                    </h1>
                    <table align="center" id="mTbl1" border="0" width="95%" style='display:none;'>
                        <tr align="left" valign="center">
                            <td align="center" width="" height="100">
                                <span style="float:left;">
                                    <a href="mod/m_forum_use_detail_analysis/index.php"><img src="img/mod/m_teacher_report.png" width="32" height="32" border="0"></a><br/>
                                    <span><a href="mod/m_forum_use_detail_analysis/index.php"><img src="img/ok.png" border="0" class="icon" width="16" height="16">聊書系統細部使用分析</a></span>
                                </span>
                            </td>
                            <td align="center" width="" height="100">
                                <span style="float:left;">
                                    <a href="mod/m_book_borrow_rank_info/index.php"><img src="img/mod/m_teacher_report.png" width="32" height="32" border="0"></a><br/>
                                    <span><a href="mod/m_book_borrow_rank_info/index.php"><img src="img/ok.png" border="0" class="icon" width="16" height="16">書籍借閱前150名</a></span>
                                </span>
                            </td>
                            <td align="center" width="" height="100">
                                <span style="float:left;">
                                    <a href="https://drive.google.com/a/cl.ncu.edu.tw/folderview?id=0B2uqH3DVBITHb1RvVVQ2WXhuWU0&usp=sharing_eid&ts=566e9be1&tid=0B3kzZxfR-62JdmdKWXNzWEdNYUE" target="_blank"><img src="img/mod/m_teacher_report.png" width="32" height="32" border="0"></a><br/>
                                    <span><a href="https://drive.google.com/a/cl.ncu.edu.tw/folderview?id=0B2uqH3DVBITHb1RvVVQ2WXhuWU0&usp=sharing_eid&ts=566e9be1&tid=0B3kzZxfR-62JdmdKWXNzWEdNYUE" target="_blank"><img src="img/ok.png" border="0" class="icon" width="16" height="16">各學校報表<br>2015-12-22</a></span>
                                </span>
                            </td>
                        </tr>
                        <tr align="left" valign="center">
                            <td align="center" width="" height="100">
                                <span style="float:left;">
                                    <a href="https://drive.google.com/a/cl.ncu.edu.tw/folderview?id=0B2uqH3DVBITHb1RvVVQ2WXhuWU0&usp=sharing_eid&ts=566e9be1&tid=0B3kzZxfR-62JdmdKWXNzWEdNYUE" target="_blank"><img src="img/mod/m_teacher_report.png" width="32" height="32" border="0"></a><br/>
                                    <span><a href="https://drive.google.com/a/cl.ncu.edu.tw/folderview?id=0B2uqH3DVBITHb1RvVVQ2WXhuWU0&usp=sharing_eid&ts=566e9be1&tid=0B3kzZxfR-62JdmdKWXNzWEdNYUE" target="_blank"><img src="img/ok.png" border="0" class="icon" width="16" height="16">桃園市明日學校平台<br>使用數據</a></span>
                                </span>
                            </td>
                            <td align="center" width="" height="100">
                                <span style="float:left;">
                                    <a href="https://drive.google.com/a/cl.ncu.edu.tw/folderview?id=0B2uqH3DVBITHb1RvVVQ2WXhuWU0&usp=sharing_eid&ts=566e9be1&tid=0B3kzZxfR-62JdmdKWXNzWEdNYUE" target="_blank"><img src="img/mod/m_teacher_report.png" width="32" height="32" border="0"></a><br/>
                                    <span><a href="https://drive.google.com/a/cl.ncu.edu.tw/folderview?id=0B2uqH3DVBITHb1RvVVQ2WXhuWU0&usp=sharing_eid&ts=566e9be1&tid=0B3kzZxfR-62JdmdKWXNzWEdNYUE" target="_blank"><img src="img/ok.png" border="0" class="icon" width="16" height="16">校內至少有2班使用書店</a></span>
                                </span>
                            </td>
                            <td align="center" width="" height="100">
                                <span style="float:left;">
                                    <a href="mod/m_user_member_analysis/index.php"><img src="img/mod/m_teacher_report.png" width="32" height="32" border="0"></a><br/>
                                    <span><a href="mod/m_user_member_analysis/index.php"><img src="img/ok.png" border="0" class="icon" width="16" height="16">星球帳號成長趨勢圖</a></span>
                                </span>
                            </td>
                        </tr>
                        <tr align="left" valign="center">
                            <td align="center" width="" height="100">
                                <span style="float:left;">
                                    <a href="mod/m_user_guest_member_analysis/index.php"><img src="img/mod/m_teacher_report.png" width="32" height="32" border="0"></a><br/>
                                    <span><a href="mod/m_user_guest_member_analysis/index.php"><img src="img/ok.png" border="0" class="icon" width="16" height="16">星球體驗帳號成長趨勢圖</a></span>
                                </span>
                            </td>
                            <!-- <td align="center" width="" height="100">
                                <span style="float:left;">
                                    <a href="mod/m_bookstore_member_analysis/index.php"><img src="img/mod/m_teacher_report.png" width="32" height="32" border="0"></a><br/>
                                    <span><a href="mod/m_bookstore_member_analysis/index.php"><img src="img/ok.png" border="0" class="icon" width="16" height="16">書店活耀人數</a></span>
                                </span>
                            </td> -->
                            <td align="center" width="" height="100">
                                <span style="float:left;">
                                    <a href="mod/m_forum_use_analysis_photo/index.php"><img src="img/mod/m_teacher_report.png" width="32" height="32" border="0"></a><br/>
                                    <span><a href="mod/m_forum_use_analysis_photo/index.php"><img src="img/ok.png" border="0" class="icon" width="16" height="16">聊書系統文章發表趨勢圖</a></span>
                                </span>
                            </td>
                        </tr>
                    </table>

                </td>
                <!-- 模組表格 -->
            </tr>
        </table>

    </div>
    <!-- content 結束 -->

    <!-- footer 開始 -->
    <div id="footer">
        <?php echo footer_slogan();?>
    </div>
    <!-- footer 開始 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //---------------------------------------------------
    //參數
    //---------------------------------------------------

    //---------------------------------------------------
    //物件
    //---------------------------------------------------

        //縮合圖示
        var o_img_arrow_up    =new Image();
        var o_img_arrow_bottom=new Image();
        o_img_arrow_up.src    ="img/arrow_up.gif";
        o_img_arrow_bottom.src="img/arrow_bottom.gif";

    //---------------------------------------------------
    //函式
    //---------------------------------------------------

        function toggle(tbl_id){
        //縮核區塊
            try{
                var oTbl   =document.getElementById(tbl_id);
                var o_arrow=document.getElementById('arrow_'+tbl_id);

                if(oTbl.style.display==''){
                    oTbl.style.display='none';
                    o_arrow.src=o_img_arrow_up.src;
                }else{
                    oTbl.style.display='';
                    o_arrow.src=o_img_arrow_bottom.src;
                }
            }catch(e){

            }
        }

    //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        window.onload=function(){

            //時鐘
            var obj=new SWFObject("flash/clock.swf",'player','180','180','9');
            obj.addParam('allowfullscreen','true');
            obj.addParam('allowScriptaccess','always');
            obj.addParam('wmode','transparent');
            obj.write('clock');

            //模組表格
        }
</script>

</Body>
</Html>