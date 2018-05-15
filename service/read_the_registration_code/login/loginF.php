<?php
//-------------------------------------------------------
//閱讀登記條碼版
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

        if($config_arrys['is_offline']['service']['read_the_registration_code']){
            $url=str_repeat("../",4).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(login_check(array('t'))){
            $url=str_repeat("../",1).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,閱讀登記條碼版";

        //註腳列
        $footer=footer($rd=3);
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=true);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../lib/js/public/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../inc/code.css" media="all" />
    <script type="text/javascript" src="../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../css/def.css" media="all" />
</Head>

<Body>

<!-- ************* waring: ************** -->
<!-- 請勿以非法方式探索進入本網站，當您看 -->
<!-- 到這個頁面時，您的現在以及後續的操作 -->
<!-- 動作會紀錄在本網站的紀錄裡。         -->
<!-- ************* waring: ************** -->

<!-- 容器區塊 開始 -->
<div id="container">

    <!-- 標題區塊 開始 -->
    <div id="header">

        <!-- 標題上方 開始 -->
        <div id="header_top">
            <div id="header_menu_div">
                <!-- 標題選單列 開始 -->
                <span id="header_menu">
                    <a href="#" alt="回星球首頁">
                        <!-- <img width="110" height="19" src="../css/structure/img/header/home_1.jpg" target="_self" alt="回星球首頁" border="0"> -->
                    </a>
                </span>
                <!-- 標題選單列 結束 -->
            </div>
        </div>
        <!-- 標題上方 結束 -->

        <!-- 標題下方 開始 -->
        <div id="header_bottom">
            <div id="logo_div">
                <!-- logo 開始 -->
                <div id="logo"></div>
                <!-- logo 結束 -->
            </div>
        </div>
        <!-- 標題下方 結束 -->

    </div>
    <!-- 標題區塊 結束 -->

    <!-- 內容區塊 開始 -->
    <div id="content">

        <form id='Form1' name='Form1' action='' method='post' onsubmit="return false">
            <table id='login_tbl' border='0' align='center' cellpadding="0" cellspacing="0">
                <tr class="bg_gray0 fc_gray0">
                    <td colspan='2' align="left" valign="middle">
                        <span style="float:left;padding:9px 0;padding-left:15px;font-weight:bold;">
                            閱讀登記條碼版 - 登入中心
                        </span>
                    </td>
                </tr>
                <tr class="fc_gray0">
                    <td align="right" width="150px">
                        <div style="margin-top:20px;">
                            老師帳號：
                        </div>
                    </td>
                    <td>
                        <div style="margin-top:20px;">
                            <input type="text" id="user_uid" name="user_uid" value="" size="20" maxlength="30" tabindex="1"
                            class="form_text" style="width:150px;">
                        </div>
                    </td>
                </tr>
                <tr class="fc_gray0">
                    <td align="right" width="150px">
                        <div style="margin-top:20px;">
                            老師密碼：
                        </div>
                    </td>
                    <td>
                        <div style="margin-top:20px;">
                        <input type="password" id="user_pwd" name="user_pwd" value="" size="20" maxlength="30" tabindex="2"
                        class="form_pass" style="width:150px;">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan='2' align="center" valign="middle">
                        <div style="margin:15px 0;margin-left:15px;">
                            <input type="button" id="BtnR" name="BtnR" value="重填" class="ibtn_b6030 fc_white0" style="margin:0 2px;" tabindex="3">
                            <input type="button" id="BtnS" name="BtnS" value="登入" class="ibtn_b6030 fc_white0" style="margin:0 2px;" tabindex="4">
                        </div>
                    </td>
                </tr>
            </table>
        </form>
        <div style="margin-top:160px;"></div>

    </div>
    <!-- 內容區塊 結束 -->

    <!-- 註腳區塊 開始 -->
    <div id="footer">
        <div id="footline"></div>
        <span id="footbar">
            <!-- 註腳列 -->
            <?php foreach($footer as $footer_item) :?>
            <?php
            //-------------------------------------------
            //選項
            //-------------------------------------------
            //key       代碼
            //cname     文字
            //url       路徑
            //target    框架    _blank | _self | 視窗id
            //state     狀態    on:顯示 | off:隱藏
            //-------------------------------------------

                $key   =trim($footer_item['key']);
                $cname =trim($footer_item['cname']);
                $url   =trim($footer_item['url']);
                $target=trim($footer_item['target']);
                $state =trim($footer_item['state']);
            ?>
                <a href="<?php echo $url;?>" target="<?php echo $target;?>"><?php echo $cname;?></a>&nbsp;
            <?php endforeach ;?>
        </span>
    </div>
    <!-- 註腳區塊 結束 -->

    <!-- google分析 開始 -->
    <?php echo google_analysis($allow=true);?>
    <!-- google分析 結束 -->

</div>
<!-- 容器區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var oForm1   =document.getElementById('Form1');
    var oBtnS    =document.getElementById('BtnS');
    var oBtnR    =document.getElementById('BtnR');

    var ouser_uid=document.getElementById('user_uid');
    var ouser_pwd=document.getElementById('user_pwd');

    window.onload=function(){

        //首頁logo
        logo(rd=1,'index.php',{});

        //駐點
        ouser_uid.focus();
    }

    oBtnS.onclick=function(){
    //登入
        var user_uid=trim(ouser_uid.value);
        var user_pwd=trim(ouser_pwd.value);

        var arry_err=[];
        if(user_uid==''){
            arry_err.push('請在帳號裡輸入值!');
        }
        if(user_pwd==''){
            arry_err.push('請在密碼裡輸入值!');
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl))
            return false;
        }else{
            oForm1.action='loginA.php';
            oForm1.submit();
        }
    }

    oBtnR.onclick=function(){
    //重填
        oForm1.reset();

        //駐點
        ouser_uid.focus();
    }

    oBtnS.onmouseover= function(){
    //動作
        this.style.cursor='pointer';
    }
    oBtnS.onmouseout= function(){
        this.style.cursor='none';
    }

    oBtnR.onmouseover= function(){
    //動作
        this.style.cursor='pointer';
    }
    oBtnR.onmouseout= function(){
        this.style.cursor='none';
    }

</script>

</Body>
</Html>

