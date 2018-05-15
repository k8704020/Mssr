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
        require_once(str_repeat("../",4).'config/config.php');

        //外掛頁面檔
        require_once(str_repeat("../",2).'pages/code.php');

        //外掛函式檔
        $funcs=array(
            APP_ROOT.'inc/code',
            APP_ROOT.'service/forum/inc/code',

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

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        $arrys_sess_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);

        if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
        if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
        if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
        if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
        if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
        if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
        if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
        if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
        if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
        if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
        if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
            $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
            foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
            }
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        $sess_user_id  =(int)$_SESSION['uid'];
        $sess_user_name=trim($_SESSION['mssr_forum'][0]['name']);
        $sess_user_sex =(int)$_SESSION['mssr_forum'][0]['sex'];

        $sess_user_img ='';
        if($sess_user_sex===1)$sess_user_img='../../img/default/user_boy.png';
        if($sess_user_sex===2)$sess_user_img='../../img/default/user_girl.png';

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,明日聊書";

        //標籤
        $meta=meta($rd=2);

        //導覽列
        $navbar=navbar($rd=2);

        //廣告牆
        $carousel=carousel($rd=2);

        //註腳列
        $footbar=footbar($rd=2);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title;?></title>

    <!-- 標籤,start -->
    <?php echo $meta;?>
    <!-- 標籤,end -->

    <!-- icon -->
    <link rel="shortcut icon" href="">

    <!-- 通用 -->
    <link href="../../../../lib/jquery/ui/code.css" rel="stylesheet" type="text/css">
    <link href="../../../../lib/framework/bootstrap/css/code.css" rel="stylesheet" type="text/css">
    <link href="../../../../inc/code.css" rel="stylesheet" type="text/css">

    <!-- 專屬 -->
    <link href="../../css/site.css" rel="stylesheet" type="text/css">

    <!--[if lt IE 9]>
        <script src="../../../../lib/js/html5/code.js"></script>
        <script src="../../../../lib/js/css/code.js"></script>
    <![endif]-->
</head>
<style>
    .jumbotron{
        position: relative;
        margin: 50px 0 0 0;
        height: 450px;
        border-radius: 3px;
        background-image: url('#');
    }
</style>
<body>

    <!-- 導覽列,容器,start -->
    <?php echo $navbar;?>
    <!-- 導覽列,容器,end -->

    <!-- 頁面,容器,start -->
    <div class="container">

        <!-- 內容,start -->
        <div class="jumbotron">
            <p>親愛的使用者您好，本網站為了提升您的瀏覽經驗，以及提供手機與平板裝置瀏覽，故我們已不再支援舊版的瀏覽器，如IE6,IE7,IE8..等等。</p>
            <p>由於各家瀏覽器版本眾多，爲了給您更好的瀏覽體驗，請立即更新您的瀏覽器。</p>
            <p>強烈建議您安裝下列瀏覽器，以便得到最佳的瀏覽結果。<p>
            <ol>
                <li>FireFox 火狐瀏覽器。  <a href="http://moztw.org/firefox/">按此下載</a></li>
                <li>Google Chrome 瀏覽器。<a href="https://www.google.com/intl/zh-TW/chrome/browser/">按此下載</a></li>
            </ol>

            <p style="position:;margin-top:100px;">
                如造成您的不便，敬請見諒，中央大學明日聊書團隊敬上!
            </p>
        </div>
        <!-- 內容,end -->

    </div>
    <!-- 頁面,容器,end -->

    <!-- 註腳列,容器,start -->
    <?php echo $footbar;?>
    <!-- 註腳列,容器,end -->

</body>

<!-- 通用 -->
<script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>

<!-- 專屬 -->

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var send_url=document.URL;
    var sess_user_img=$.trim('<?php echo $sess_user_img;?>');


    //FUNCTION


    //ONLOAD
    $(function(){
        //$('.user_img').attr('src',sess_user_img);
        $('.navbar-collapse').remove();
    })


</script>
</html>
<?php
    $conn_user=NULL;
    $conn_mssr=NULL;
?>