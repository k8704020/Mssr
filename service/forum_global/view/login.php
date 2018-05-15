<?php
//-------------------------------------------------------
//明日聊書
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

        //外掛頁面檔
        require_once(str_repeat("../",1).'pages/code.php');

        //外掛函式檔
        $funcs=array(
            APP_ROOT.'inc/code',
            APP_ROOT.'service/forum_global/inc/code',

            APP_ROOT.'lib/php/db/code',
            APP_ROOT.'lib/php/net/code',
            APP_ROOT.'lib/php/array/code',
            APP_ROOT.'lib/php/date/code'
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
    //echo "<Pre>";
    //print_r($_SESSION);
    //echo "</Pre>";

        $arrys_sess_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
        if(!empty($arrys_sess_login_info)){
            $jscript_back="
                <script>
                    location.href='index.php';
                </script>
            ";
            die($jscript_back);
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,明日聊書";

        //標籤
        $meta=meta($rd=1);

        //導覽列
        $navbar=navbar($rd=1);

        //廣告牆
        $carousel=carousel($rd=1);

        //註腳列
        $footbar=footbar($rd=1);

?>
<!DOCTYPE html>
<html lang='zh-TW' prefix='og: http://ogp.me/ns#'>
<head>
    <title><?php echo $title;?></title>

    <!-- 標籤,start -->
    <?php echo $meta;?>
    <!-- 標籤,end -->

    <!-- icon -->
    <link rel="shortcut icon" href="">

    <!-- 通用 -->
    <link href="../../../lib/framework/bootstrap/css/code.css" rel="stylesheet" type="text/css">

    <!-- 專屬 -->
    <link href="../css/site.css" rel="stylesheet" type="text/css">

    <!--[if (gte IE 6)&(lte IE 8)]>
        <script>self.location.href='../pages/browser_update/index.php'</script>
    <![endif]-->

    <!--[if lt IE 9]>
        <script src="../../../lib/js/html5/code.js"></script>
        <script src="../../../lib/js/css/code.js"></script>
    <![endif]-->

    <!-- 通用 -->
    <script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>
    <script type="text/javascript" src="../../../lib/js/string/code.js"></script>

    <!-- 專屬 -->
</head>
<body>

<!-- ************* waring: ************** -->
<!-- 請勿以非法方式探索進入本網站，當您看 -->
<!-- 到這個頁面時，您的現在以及後續的操作 -->
<!-- 動作會紀錄在本網站的紀錄裡。         -->
<!-- ************* waring: ************** -->

    <!-- 導覽列,容器,start -->
    <?php echo $navbar;?>
    <!-- 導覽列,容器,end -->

    <!-- 頁面,容器,start -->
    <div class="container" style="margin-bottom:35px;">

        <!-- 內容,start -->
        <div class="alert alert-danger" role="alert">
            <strong>
                <p>
                    【台港聊書系統停機通知】(01/05)暫停使用一天
                </p>
                <p>
                    各位老師好,<br><br>

                    【台港聊書系統】將在(01/05)暫停使用一天，<br>
                    麻煩各位老師及學生在時間內不要使用，以免產生系統錯誤等問題。<br>

                    若造成不便，敬請見諒。<br><br>

                    中大團隊敬上
                </p>
            </strong>
        </div>

        <form class="form-signin text-center" id="Form1" name="Form1" method="post" onsubmit="return false;" style="border:0px solid red;">
            <h1 class="form-signin-heading" style="border:0px solid red;display:inline-block;margin:10px auto;"><strong>台港聊書社群小組</strong></h1>
            <h3 class="form-signin-heading" style="border:0px solid red;display:inline-block;margin:10px auto;">Please sign in</h3>

            <label for="inputAccount" class="sr-only">帳號</label>
            <input type="account" id="user_uid" name="user_uid" class="form-control" placeholder="請輸入帳號" required autofocus>

            <label for="inputPassword" class="sr-only">密碼</label>
            <input type="password" id="user_pwd" name="user_pwd" class="form-control" placeholder="請輸入密碼" required>

            <button class="btn btn-lg btn-primary btn-block btns" type="button">登入</button>

            <div class="form-group hidden">
                <input type="hidden" class="form-control" name="method" value="login">
                <input type="hidden" class="form-control" name="send_url" value="../view/login.php">
            </div>
        </form>
        <!-- 內容,end -->

    </div>
    <!-- 頁面,容器,end -->

    <!-- 註腳列,容器,start -->
    <?php echo $footbar;?>
    <!-- 註腳列,容器,end -->

    <!-- 頁面至頂,start -->
    <div class="scroll_to_top hidden-xs"></div>
    <!-- 頁面至頂,end -->

</body>

<!-- 專屬 -->
<script type="text/javascript" src="../../../lib/jquery/plugin/func/block_ui/code.js"></script>
<script type="text/javascript" src="../inc/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var send_url=document.URL;


    //OBJ


    //FUNCTION
    $('.btns').click(function(){
    //登入

        var oForm1   =document.getElementById('Form1');
        var ouser_uid=document.getElementById('user_uid');
        var ouser_pwd=document.getElementById('user_pwd');
        var arry_err =[];

        if(trim(ouser_uid.value)===''){
            arry_err.push('請輸入帳號');
        }
        if(trim(ouser_pwd.value)===''){
            arry_err.push('請輸入密碼');
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            block_ui('登入中，請稍後');
            oForm1.action='../controller/load.php'
            oForm1.submit();
            return true;
        }
    });


    //ONLOAD


</script>
</html>