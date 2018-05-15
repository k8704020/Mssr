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

        if(login_check(array('a'))){
            $url=str_repeat("../",2).'index.php';
            header("Location: {$url}");
            die();
        }

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
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" />
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <!-- 本頁 -->
    <style alt="IE6,PNG修正">
        #Tbl1{

        }
        #Tbl1 tr,td{
            height:40px;
        }
    </style>
</Head>

<Body>

<!-- ************* waring: ************** -->
<!-- 請勿以非法方式探索進入本網站，當您看 -->
<!-- 到這個頁面時，您的現在以及後續的操作 -->
<!-- 動作會紀錄在本網站的紀錄裡。         -->
<!-- ************* waring: ************** -->

    <!-- header 開始 -->
    <div id="header">
        <a href="#"><img id="logo" src="../../img/home.gif" alt="logo"></a>
        <span id="title"><?php echo header_slogan();?></span>
        <ul id="navbar">
            <li><a href="#" class="current">後台登入頁</a></li>
            <li><a href="../../../../../index.php">回首頁</a></li>
        </ul>
    </div>
    <!-- header 結束 -->

    <!-- content 開始 -->
    <div id="content" style="margin-top:100px">
        <form id='Form1' name='Form1' action='' method='post' onsubmit="return false">
        <table id='Tbl1' width='400px' border='0' align='center' class="table_style8 bg_gray0">
            <tr>
                <td colspan='3' align="left" class="bg_gray1">
                    <span style="float:left;margin-left:15px;">
                        明日書店 - 後台管理登入
                    </span>
                </td>
            </tr>
            <tr>
                <td align="center" width="100px">
                帳號
                </td>
                <td>
                    <input type="text" id="uid" name="uid" value="" size="20" maxlength="20" tabindex="1"
                    cname="帳號" style="width:150px;border:1px solid #e1e1e1;padding:3px;">
                </td>
                <td rowspan="3" width="100px" align="left" valign="middle">
                    <img id='vimg' cname="驗證碼圖片" alt="vimg">
                </td>
            </tr>
            <tr>
                <td align="center" width="100px">
                密碼
                </td>
                <td valign="middle">
                    <input type="password" id="pwd" name="pwd" value="" size="20" maxlength="20" tabindex="2"
                    cname="密碼" style="width:150px;border:1px solid #e1e1e1;padding:3px;">
                </td>
            </tr>
            <tr>
                <td align="center" width="100px">
                驗證碼
                </td>
                <td>
                    <input type="text" id="vcode" name="vcode" value="" size="20" maxlength="" tabindex="3"
                    cname="驗證碼" style="width:150px;border:1px solid #e1e1e1;padding:3px;">
                </td>
            </tr>
            <tr>
                <td colspan='3' align="center" valign="middle">
                    <span style="margin-left:15px;">
                        <input type="button" id="BtnS" name="BtnS" value="登入" class="ibtn_w6030" onmouseover='_mouse_over(this);void(0);'>
                        <input type="button" id="BtnR" name="BtnR" value="看不清楚驗證碼" class="ibtn_w9030" onmouseover='_mouse_over(this);void(0);'>
                        <input type="button" id="BtnH" name="BtnH" value="首頁" class="ibtn_w6030" onmouseover='_mouse_over(this);void(0);'>
                    </span>
                </td>
            </tr>
        </table>
        </form>
    </div>
    <!-- content 結束 -->

    <!-- footer 開始 -->
    <div id="footer" style="margin-top:100px">
        <?php echo footer_slogan();?>
    </div>
    <!-- footer 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //---------------------------------------------------
    //參數
    //---------------------------------------------------

        var nl='\r\n';

    //---------------------------------------------------
    //物件
    //---------------------------------------------------

        var oForm1=document.getElementById('Form1');
        var oBtnS =document.getElementById('BtnS');
        var oBtnR =document.getElementById('BtnR');
        var oBtnH =document.getElementById('BtnH');

        var ouid  =document.getElementById('uid');
        var opwd  =document.getElementById('pwd');
        var ovcode=document.getElementById('vcode');

    //---------------------------------------------------
    //函式
    //---------------------------------------------------

        oBtnR.onclick=function(){
            verify_image(4,'vimg',100,50);
        }

        oBtnS.onclick=function(){
        //登入
            var uid  =trim(ouid.value);
            var pwd  =trim(opwd.value);
            var vcode=trim(ovcode.value);

            var arry_err=[];
            if(uid==''){
                arry_err.push('請輸入帳號');
            }
            if(pwd==''){
                arry_err.push('請輸入密碼');
            }
            if(vcode==''){
                arry_err.push('請輸入驗證碼');
            }
            if(arry_err.length!=0){
                alert(arry_err.join(nl))
                return false;
            }else{
                oForm1.action='loginA.php';
                oForm1.submit();
            }
        }

        oBtnH.onclick=function(){
        //首頁
            var url ='';
            var page='../../index.php';
            var arg ='';

            if(arg.length!=0){
                url+=page+"?"+arg;
            }else{
                url+=page;
            }

            go(url,'self');
        }

    //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        window.onload=function(){
            ouid.focus();
            verify_image(4,'vimg',100,50);
        }
</script>
</Body>
</Html>