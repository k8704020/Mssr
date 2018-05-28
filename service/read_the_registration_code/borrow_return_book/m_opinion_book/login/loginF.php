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
        require_once(str_repeat("../",5).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'service/read_the_registration_code/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['service']['read_the_registration_code']){
            $url=str_repeat("../",6).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(!login_check(array('t'))){
            $url=str_repeat("../",3).'login/loginF.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        //初始化，承接變數
        $_sess_t=$_SESSION['t'];
        foreach($_sess_t as $field_name=>$field_value){
            $$field_name=$field_value;
        }

        //清除學生登入紀錄
        unset($_SESSION['_read_the_registration_code']['_login']);

        //$_SESSION['_read_the_registration_code']['opinion_book']['login']='loginF.php';
        if($_SESSION['_read_the_registration_code']['opinion_book']['login']==='loginF2'){
            $jscript_back="
                <script>
                    location.href='loginF2.php';
                </script>
            ";
            die($jscript_back);
        }
//echo "<Pre>";print_r($_SESSION['_read_the_registration_code']['opinion_book']);echo "</Pre>";
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,閱讀登記條碼版";

        //註腳列
        $footer=footer($rd=5);
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
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/effect/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../css/def.css" media="all" />
</Head>

<Body>

<!-- 容器區塊 開始 -->
<div id="container">

    <!-- 標題區塊 開始 -->
    <div id="header">

        <!-- 標題上方 開始 -->
        <div id="header_top">
            <div id="header_menu_div">
                <!-- 標題選單列 開始 -->
                <span id="header_menu">
                    <!-- <a href="#" alt="回星球首頁" style="display:inline-block;">
                        <img width="110" height="19" src="../../../css/structure/img/header/home_2.jpg" target="_self" alt="回星球首頁" border="0">
                    </a> --><a href="../../../login/logout.php" alt="登出" style="display:inline-block;">
                        <img width="113" height="19" src="../../../css/structure/img/header/logout.jpg" target="_self" alt="登出" border="0">
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

        <table id="read_the_registration_code_datalist_tbl" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td align="left" valign="middle" width="650px" height="30px">
                    <!-- 閱讀登記條碼版中心路徑選單 開始 -->
                    <div id="read_the_registration_code_center_path">
                        <table id="read_the_registration_code_center_path_cont" border="0" width="100%">
                            <tr>
                                <td align="left" valign="middle" class="menu_dot">
                                    <span class="menu_cont">
                                        <img width="12" height="12" src="../../../../../img/icon/blue.jpg" border="0">
                                        <a href="../../../index.php">系統中心</a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="../../../index.php">借還書系統</a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="javascript:void(0);">登記</a>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- 閱讀登記條碼版中心路徑選單 結束 -->
                </td>
                <td align="left" valign="middle">
                    <input id="BtnG" type="button" value="我要還書" style="position:relative;left:35px;display:none;" class="ibtn_gr9030">
                </td>
            </tr>
            <tr>
                <td height="426px">
                    <!-- 資料列表 開始 -->
                    <form id='Form1' name='Form1' action='' method='post' onsubmit="return _submit(this);">
                        <table id='login_tbl' border='0' align='center' cellpadding="0" cellspacing="0" style="height:200px;">
                            <tr class="bg_gray0 fc_gray0">
                                <td colspan='2' align="left" valign="middle">
                                    <span style="float:left;padding-left:15px;font-weight:bold;">
                                        學生登入
                                    </span>
                                </td>
                            </tr>
                            <tr class="fc_gray0">
                                <td align="right" width="150px">
                                    <div style="position:relative;top:30px;">
                                        借書證號碼：
                                    </div>
                                </td>
                                <td>
                                    <div style="position:relative;top:30px;">
                                        <input type="text" id="card_number" name="card_number" value="" size="20" maxlength="30" tabindex="1"
                                        class="form_text" style="width:150px;">
                                    </div>
                                </td>
                            </tr>
                            <tr class="fc_gray0">
                                <td align="right" width="150px">&nbsp;</td>
                                <td class="fc_gray0">&nbsp;</td>
                            </tr>

                            <tr>
                                <td colspan='2' align="center" valign="middle">
                                    <span style="margin-left:15px;">
                                        <input type="button" id="BtnR" name="BtnR" value="重填" class="ibtn_b6030 fc_white0" style="margin:0 2px;" tabindex="2">
                                        <input type="button" id="BtnS" name="BtnS" value="送出" class="ibtn_b6030 fc_white0" style="margin:0 2px;" tabindex="3">
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <!-- 資料列表 結束 -->
                </td>
                <td align="right" valign="top" width="100px">
                    <input type="button" value="選擇學生登入" class="ibtn_gr9030" style="position:relative;right:5px;cursor:pointer;"
                    onclick="location.href='login_type.php?login=loginF2'">

                    <a href="../../m_borrow_book/login/loginF.php">
                        <img id="img_borrow_book" width="100" height="100" src="../../../img/borrow_book.jpg" border="0"
                        style="margin-top:55px;display:none;">
                    </a>
                    <a href="../../../borrow_return_book/m_return_book/index.php">
                        <img id="img_return_book" width="100" height="100" src="../../../img/return_book.jpg" border="0"
                        style="margin-top:30px;display:none;">
                    </a>
                    <a href="../../m_opinion_book/login/loginF.php">
                        <img id="img_opinion_book" width="100" height="100" src="../../../img/opinion_book.jpg" border="0"
                        style="margin-top:30px;">
                    </a>
                </td>
            </tr>
        </table>

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
    var oForm1      =document.getElementById('Form1');
    var oBtnS       =document.getElementById('BtnS');
    var oBtnR       =document.getElementById('BtnR');
    var oBtnG       =document.getElementById('BtnG');
    var oimg_borrow_book=document.getElementById('img_borrow_book');
    var oimg_return_book=document.getElementById('img_return_book');

    var ocard_number=document.getElementById('card_number');

    window.onload=function(){

        //首頁logo
        logo(rd=3,'index.php',{});

        //駐點
        ocard_number.focus();

        //透明設定
        set_opacity(oimg_return_book,60)
    }

    oimg_return_book.onmouseover= function(){
    //動作
        //透明設定
        set_opacity(oimg_return_book,100)
    }

    oimg_return_book.onmouseout= function(){
    //動作
        //透明設定
        set_opacity(oimg_return_book,60)
    }

    oBtnS.onclick=function(){
    //登入
        var card_number=trim(ocard_number.value);

        var arry_err=[];
        if(card_number==''){
            arry_err.push('請在借書證號碼裡輸入值!');
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
        ocard_number.focus();
    }

    oBtnG.onclick=function(){
    //我要還書
        location.href='../../m_return_book/index.php';
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

    oBtnG.onmouseover= function(){
    //動作
        this.style.cursor='pointer';
    }
    oBtnG.onmouseout= function(){
        this.style.cursor='none';
    }

    function _submit(_this){
    //登入
        var card_number=trim(ocard_number.value);

        var arry_err=[];
        if(card_number==''){
            arry_err.push('請在借書證號碼裡輸入值!');
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl))
            return false;
        }else{
            oForm1.action='loginA.php';
            oForm1.submit();
        }
    }

    ocard_number.onblur=function(){
    //失去駐點
        setTimeout(function(){
            ocard_number.focus();
        });
    };

</script>

</Body>
</Html>
