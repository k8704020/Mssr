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
        require_once(str_repeat("../",4).'config/config.php');

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
            $url=str_repeat("../",2).'mod/m_login/loginF.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        //初始化，承接變數
        $_sess_a=$_SESSION['a'];
        foreach($_sess_a as $field_name=>$field_value){
            $$field_name=trim($field_value);
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //串接SQL
    //---------------------------------------------------

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

    //---------------------------------------------------
    //分頁處理
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
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../lib/php/image/verify/verify_image.js"></script>
    <script type="text/javascript" src="../../../../inc/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/flash/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" />
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>
</Head>

    <!-- header 開始 -->
    <div id="header">
        <a href="#"><img id="logo" src="../../img/home.gif"></a>
        <span id="title"><?php echo header_slogan();?></span>
        <ul id="navbar">
            <li><a href="#" class="current"><span>xxx模組&nbsp;</span></a></li>
            <li><a href="../../index.php"><span>回主控台&nbsp;</span></a></li>
            <li><a href="#"><span>說明&nbsp;</span></a></li>
            <li><a href="#"><span>首頁&nbsp;</span></a></li>
            <li><a href="../../mod/m_login/logout.php"><span>登出&nbsp;</span></a></li>
        </ul>
    </div>
    <!-- header 結束 -->

    <!-- content 開始 -->
    <div id="content">
        <table id="datalist_tbl" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td align="left" valign="middle" width="317px">
                    <!-- 路徑選單 開始 -->
                    <div id="center_path">
                        <table id="center_path_cont" border="0" width="317px">
                            <tr>
                                <td align="left" valign="middle" class="menu_dot">
                                    <span class="menu_cont">
                                        <img width="12" height="12" src="../../img/blue.jpg" border="0">
                                        <a href="../../index.php">網管中心</a>
                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="../../index.php">報表系統</a>
                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="javascript:void(0);">老師報表</a>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- 路徑選單 結束 -->
                </td>
                <!-- 查詢模式 開始 -->
                <td width='450px' align="center" valign="middle" class='fc_blue0'>
                    查詢模式查詢模式查詢模式查詢模式查詢模式
                </td>
                <!-- 查詢模式 結束 -->
                <td align="right" valign="middle">
                    查詢表單列查詢表單列查詢表單列查詢
                    <!-- 查詢表單列 開始 -->
                    <div id="qform">
                        <span id="qform1"></span>
                    </div>
                    <!-- 查詢表單列 結束 -->
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <!-- 資料列表 開始 -->
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
                        <tr>
                            <!-- 在此設定寬高 -->
                            <td width="100%" height="620px" align="center" valign="top">
                                <!-- 內容 -->
                                <!-- <iframe id="IFC" name="IFC" src="content.php?pinx=<?php //echo (int)$pinx;?>&psize=<?php //(int)$psize;?>" frameborder="0"
                                style="width:100%;height:620px;overflow:hidden;overflow-y:auto"></iframe> -->
                                <!-- 內容 -->
                            </td>
                        </tr>
                    </table>
                    <!-- 資料列表 結束 -->
                </td>
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

    //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        window.onload=function(){

        }
</script>

</Body>
</Html>
