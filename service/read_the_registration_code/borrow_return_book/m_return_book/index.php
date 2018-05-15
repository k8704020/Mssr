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
        require_once(str_repeat("../",4).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'service/read_the_registration_code/inc/code',

                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['service']['read_the_registration_code']){
            $url=str_repeat("../",5).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(!login_check(array('t'))){
            $url=str_repeat("../",2).'login/loginF.php';
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
            $field_name=$field_value;
        }

        //清除學生登入紀錄
        unset($_SESSION['_read_the_registration_code']['_login']);

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
$school_code=$_SESSION['t']['school_code'];
    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,閱讀登記條碼版";

        //註腳列
        $footer=footer($rd=4);
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
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/table/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/effect/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" />
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
                        <img width="110" height="19" src="../../css/structure/img/header/home_2.jpg" target="_self" alt="回星球首頁" border="0">
                    </a> --><a href="../../login/logout.php" alt="登出" style="display:inline-block;">
                        <img width="113" height="19" src="../../css/structure/img/header/logout.jpg" target="_self" alt="登出" border="0">
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
    <div id="content" style="margin-top:5px;">

        <table id="read_the_registration_code_datalist_tbl" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td align="left" valign="middle" width="780px">
                    <!-- 閱讀登記條碼版中心路徑選單 開始 -->
                    <div id="read_the_registration_code_center_path">
                        <table id="read_the_registration_code_center_path_cont" border="0" width="100%">
                            <tr>
                                <td align="left" valign="middle" class="menu_dot">
                                    <span class="menu_cont">
                                        <img width="12" height="12" src="../../../../img/icon/blue.jpg" border="0">
                                        <a href="../../index.php">系統中心</a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="../../index.php">借還書系統</a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="javascript:void(0);">還書</a>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- 閱讀登記條碼版中心路徑選單 結束 -->
                </td>
                <td align="left" valign="middle">
                    <input id="BtnG" type="button" value="我要借書" style="position:relative;left:35px;display:none;" class="ibtn_gr9030">
                    <input id="BtnR" type="button" value="" style="position:relative;left:35px;display:none;" class="ibtn_gr12030">
                </td>
            </tr>
            <tr>
                <td>
                    <!-- 資料列表 開始 -->
                    <iframe id="IFC" name="IFC" src="content.php" frameborder="0"
                    style="width:100%;height:600px;overflow:hidden;overflow-y:auto"></iframe>
                    <!-- 資料列表 結束 -->
                </td>
                <td align="right" valign="top">
                    <a href="../../borrow_return_book/m_borrow_book/index.php">
                        <img id="img_borrow_book" width="100" height="100" src="../../img/borrow_book.jpg" border="0"
                        style="margin-top:60px;">
                    </a>
                    <a href="#">
                        <img id="img_return_book" width="100" height="100" src="../../img/return_book.jpg" border="0"
                        style="margin-top:30px;">
                    </a>
                    <?php if($school_code==='idc'){?>
                     <a href="../../borrow_return_book/m_answer_qa/index.php">
                        <img id="img_qa" width="100" height="100" src="../../img/q&a.png" border="0"
                        style="margin-top:30px;">
                    </a>
                    <?php } ?>
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

    var oBtnG=document.getElementById('BtnG');
    var oBtnR=document.getElementById('BtnR');

    var oimg_borrow_book=document.getElementById('img_borrow_book');
    var oimg_return_book=document.getElementById('img_return_book');
     var oimg_qa=document.getElementById('img_qa');

    //ajax設定
    var $_url           ="multi_add/addA.php";
    var $_type          ="POST";
    var $_datatype      ="json";

    window.onload=function(){

        //首頁logo
        logo(rd=2,'index.php',{});

        //透明設定
        set_opacity(oimg_borrow_book,60);
        set_opacity(oimg_qa,60);
    }

    oimg_borrow_book.onmouseover= function(){
    //動作
        //透明設定
        set_opacity(oimg_borrow_book,100)
    }

    oimg_borrow_book.onmouseout= function(){
    //動作
        //透明設定
        set_opacity(oimg_borrow_book,60)
    }
    oimg_qa.onmouseover= function(){
    //動作
        //透明設定
        set_opacity(oimg_qa,100)
    }

    oimg_qa.onmouseout= function(){
    //動作
        //透明設定
        set_opacity(oimg_qa,60)
    }

    oBtnG.onclick=function(){
    //我要借書
        location.href='../m_borrow_book/login/loginF.php';
    }
    oBtnR.onclick=function(){
    //重新歸還書籍
        if(localStorage){
            if(localStorage['arrys_return_false']!==undefined){
            //ajax設置
                $.ajax({
                //參數設置
                    async      :true,      //瀏覽器在發送的時候，停止任何其他的呼叫。
                    cache      :false,     //快取的回應。
                    global     :true,      //是否使用全局 AJAX 事件。
                    timeout    :10000,
                    contentType:"application/x-www-form-urlencoded; charset=UTF-8",

                    url        :$_url,     //請求的頁面
                    type       :$_type,    //GET or POST
                    datatype   :$_datatype,
                    data       :{
                        arrys_return_false:encodeURI(trim(localStorage['arrys_return_false']))
                    },

                //事件
                    beforeSend  :function(){
                    //傳送前處理
                        $.blockUI({
                            message:'<h2>還書中...</h2>',
                            overlayCSS:{
                                backgroundColor:'#000',
                                opacity:0.9,
                                cursor:'default'
                            },
                            timeout: 10000
                        });
                    },
                    success     :function(respones){
                    //成功處理
                        var state=trim(respones);
                        if(state==='ok'){

                            //移除書籍清單
                            localStorage.removeItem('arrys_return_false');

                            $.blockUI({
                                message:'<h2>書籍已歸還成功 !</h2>',
                                overlayCSS:{
                                    backgroundColor:'#000',
                                    opacity:0.9,
                                    cursor:'default'
                                },
                                timeout: 2000
                            });
                            setTimeout(
                                function _location_href(){
                                    location.href='index.php';
                                },
                                2000
                            );
                        }
                    },
                    error       :function(xhr, ajaxoptions, thrownerror){
                    //失敗處理
                        if(ajaxoptions==='timeout'){
                            $.blockUI({
                                message:'<h2 class="fc_red1">連線過久，請檢查網路是否斷線 !</h2>',
                                overlayCSS:{
                                    backgroundColor:'#000',
                                    opacity:0.9,
                                    cursor:'default'
                                },
                                timeout: 2000
                            });
                        }else{
                            $.blockUI({
                                message:'<h2 class="fc_red1">連線失敗，請檢查網路是否斷線 !</h2>',
                                overlayCSS:{
                                    backgroundColor:'#000',
                                    opacity:0.9,
                                    cursor:'default'
                                },
                                timeout: 2000
                            });
                        }
                    },
                    complete    :function(){
                    //傳送後處理
                    }
                });
            }
        }else{
            alert('您的瀏覽器目前不支援此功能，請改用google chrome瀏覽器 !');
        }
    }

    oBtnG.onmouseover= function(){
    //動作
        this.style.cursor='pointer';
    }
    oBtnG.onmouseout= function(){
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

<script type="text/javascript" src="../../../../inc/external/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//呼叫使用者頁面紀錄
//-------------------------------------------------------
    user_page_log(rd=4);
</script>
</Body>
</Html>