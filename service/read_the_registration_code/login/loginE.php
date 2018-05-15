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
    //接收參數
    //---------------------------------------------------
    //err   錯誤代碼

        $get_chk=array(
            'err'
        );
        $get_chk=array_map("trim",$get_chk);
        foreach($get_chk as $get){
            if(!isset($_GET[$get])){
                $url='loginF.php';
                header("Location: {$url}");
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        $err=trim($_GET['err']);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //err   錯誤代碼
    //---------------------------------------------------
    //參數有誤  arg           登入資訊不足
    //帳號密碼  db            帳號密碼比對失敗
    //帳戶停用  user_state    該帳號被停用

        $errs=array(
            trim('arg')=>array(
                'log_reason' =>'arg',
                'display_msg'=>'登入失敗，請重新登入'
            ),
            trim('db')=>array(
                'log_reason' =>'db',
                'display_msg'=>'登入失敗，請重新登入'
            ),
            trim('user_state')=>array(
                'log_reason' =>'user_state',
                'display_msg'=>'您的帳戶已被管理人員停用，如有疑問請洽詢明日星球服務人員!'
            )
        );

        $arry_err=array();

        if($err===''){
            $arry_err[]='錯誤代碼,未輸入!';
        }else{
            if(!in_array($err,array_keys($errs))){
                $arry_err[]='錯誤代碼,未在允許清單裡!';
            }
        }

        if(count($arry_err)!==0){

            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }

            $url='loginF.php';
            header("Location: {$url}");
            die();
        }else{
            $log_reason =$errs[$err]['log_reason'];
            $display_msg=$errs[$err]['display_msg'];
        }

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
                    <td align="left" valign="middle">
                        <span style="float:left;padding:9px 0;padding-left:15px;font-weight:bold;">
                            閱讀登記條碼版 - 登入中心
                        </span>
                    </td>
                </tr>
                <tr class="fc_gray0" align="center" valign="middle" height="112px">
                    <td valign="middle">
                        <div style="margin-top:15px;margin-left:15px;">
                            <img src="../../../img/icon/fail.gif">
                            <span class="fc_gray0"><?php echo $display_msg;?></span>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td align="center" valign="middle">
                        <div style="margin:15px 0;margin-left:15px;">
                            <input type="button" id="BtnR" name="BtnR" value="重新登入" class="ibtn_b6030 fc_white0" style="margin:0 2px;">
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
    var oBtnR    =document.getElementById('BtnR');

    window.onload=function(){

        //首頁logo
        logo(rd=1,'index.php',{});
    }

    oBtnR.onclick=function(){
    //登入
        go(url='loginF.php','self');
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

