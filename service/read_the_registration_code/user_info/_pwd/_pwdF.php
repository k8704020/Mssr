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
            $$field_name=$field_value;
        }

        //清空用戶類型
        foreach($_SESSION['config']['user_types'] as $inx=>$val){
            if(trim($val)!=trim($_SESSION['config']['user_type'])){
                unset($_SESSION[$val]);
            }
        }

        //清除學生登入紀錄
        unset($_SESSION['_read_the_registration_code']['_login']);

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
        $title="明日星球,閱讀登記條碼版";

        //註腳列
        $footer=footer($rd=2);
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

    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/vaildate/code.js"></script>

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
                    <a href="../../../../../index.php" alt="回星球首頁" style="display:inline-block;">
                        <img width="110" height="19" src="../../css/structure/img/header/home_2.jpg" target="_self" alt="回星球首頁" border="0">
                    </a><a href="../../login/logout.php" alt="登出" style="display:inline-block;">
                        <img width="83" height="19" src="../../css/structure/img/header/logout.jpg" target="_self" alt="登出" border="0">
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

        <table id="read_the_registration_code_center_div" align="center" cellpadding="0" cellspacing="0" border="0"/>
            <tr>
                <td width="150px" align="left">
                    <!-- 大頭貼 開始 -->
                    <img width="111" height="110" src="../../img/user_admin.jpg" border="0">
                    <!-- 大頭貼 結束 -->
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="150px" align="left">
                    <!-- 個人資訊 開始 -->
                    <div class="fc_gray0 fsize_08" style="padding:15px 0;padding-left:15px;">姓名：<?php echo htmlspecialchars($name);?>   </div>
                    <div class="fc_gray0 fsize_08" style="padding:15px 0;padding-left:15px;">身分：老師                                    </div>
                    <div class="fc_gray0 fsize_08" style="padding:15px 0;padding-left:15px;">帳號：<?php echo htmlspecialchars($account);?></div>
                    <!-- 個人資訊 結束 -->

                    <!-- 個人功能 開始 -->
                    <div style="margin:15px 0;">
                        <!-- <img width="117" height="36" src="img/edit.jpg" border="0"> -->
                        <a href="pwdF.php">
                            <img width="117" height="36" src="../../img/pwd.jpg" border="0">
                        </a>
                        <div style="position:relative;margin-top:55px;"></div>
                    </div>
                    <!-- 個人功能 結束 -->
                </td>
                <td align="center" valign="top">
                    <!-- 內容 開始 -->
                    <form id='Form1' name='Form1' method='post' onsubmit="return false;">
                        <table border="0" width="325px" class="table_style0" cellpadding="3" style="right:70px;">
                            <tr>
                                <td id="pwd_title" colspan="2">&nbsp;</td>
                            </tr>
                            <tr class="fc_gray0">
                                <td align="right" width="140px" class="b_line gr_dashed">
                                    <span class="fc_red1">*</span>
                                    請輸入您的舊密碼：
                                </td>
                                <td align="left" class="b_line gr_dashed">
                                    <input type="password" id="o_pwd" name="o_pwd" value="" size="20" maxlength="30" tabindex="1"
                                    class="form_pass" style="width:150px;">
                                </td>
                            </tr>
                            <tr class="fc_gray0">
                                <td align="right" width="140px" class="gr_dashed">
                                    <span class="fc_red1">*</span>
                                    請輸入您的新密碼：
                                </td>
                                <td align="left" class="gr_dashed">
                                    <input type="password" id="n_pwd" name="n_pwd" value="" size="20" maxlength="30" tabindex="2"
                                    class="form_pass" style="width:150px;">
                                </td>
                            </tr>
                            <tr class="fc_gray0">
                                <td align="right" width="140px" class="gr_dashed">
                                    <span class="fc_red1">*</span>
                                    請再次輸入新密碼：
                                </td>
                                <td align="left" class="gr_dashed">
                                    <input type="password" id="n_pwd2" name="n_pwd2" value="" size="20" maxlength="30" tabindex="3"
                                    class="form_pass" style="width:150px;">
                                </td>
                            </tr>
                            <tr>
                                <td align="right" colspan="2">
                                    <input type="button" id="BtnS" name="BtnS" value="確認修改" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="4">
                                    <input type="button" id="BtnR" name="BtnR" value="取消修改" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="5">
                                </td>
                            </tr>
                        </table>
                    </form>
                    <!-- 內容 結束 -->
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
    var nl    ='\r\n';
    var oForm1=document.getElementById('Form1');    //表單
    var oBtnS =document.getElementById('BtnS');     //確認修改
    var oBtnR =document.getElementById('BtnR');     //取消修改

    //欄位
    var oo_pwd =document.getElementById('o_pwd');   //舊密碼
    var on_pwd =document.getElementById('n_pwd');   //新密碼
    var on_pwd2=document.getElementById('n_pwd2');  //確認密碼

    window.onload=function(){

        //首頁logo
        logo(rd=2,'index.php',{});

        //駐點
        oo_pwd.focus();
    }

    oBtnS.onclick=function(){
    //確認修改
        var arry_err=[];

        if(trim(oo_pwd.value)==''){
            arry_err.push('請輸入舊密碼!');
        }
        if(trim(on_pwd.value)==''){
            arry_err.push('請輸入新密碼!');
        }
        if(trim(on_pwd2.value)==''){
            arry_err.push('請輸入確認密碼!');
        }
        if((trim(on_pwd.value))!==(trim(on_pwd2.value))){
            arry_err.push('新密碼與確認密碼不一致!');
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要修改嗎?')){
                oForm1.action='pwdA.php'
                oForm1.submit();
                return true;
            }
            else{
                return false;
            }
        }
    }

    oBtnR.onclick=function(){
    //取消修改

        var url ='';
        var page=str_repeat('../',2)+'index.php';
        var arg ={

        };
        var _arg=[];
        for(var key in arg){
            _arg.push(key+"="+encodeURI(arg[key]));
        }
        arg=_arg.join("&");

        if(arg.length!=0){
            url+=page+"?"+arg;
        }else{
            url+=page;
        }

        go(url,'self');
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

