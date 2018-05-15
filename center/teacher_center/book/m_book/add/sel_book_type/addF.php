<?php
//-------------------------------------------------------
//教師中心
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",6).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/form/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['center']['teacher_center']){
            $url=str_repeat("../",7).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        $arrys_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
        if(empty($arrys_login_info)){
            die();
        }

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

        if(in_array('read_the_registration_code',$config_arrys['user_area'])){
        //清空閱讀登記條碼版登入資訊

            $_SESSION['config']['user_tbl']=array();
            $_SESSION['config']['user_type']='';
            $_SESSION['config']['user_lv']=0;
            if(in_array('read_the_registration_code',$_SESSION['config']['user_area'])){
                foreach($_SESSION['config']['user_area'] as $inx=>$area){
                    if(trim($area)==='read_the_registration_code'){
                        unset($_SESSION['config']['user_area'][$inx]);
                    }
                }
            }
        }

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        $sess_login_info=(isset($_SESSION['tc']['t|dt']))?$_SESSION['tc']['t|dt']:array();
        $add_book_tip=(isset($_SESSION['tc']['t|dt']['add_book_tip']))?(int)$_SESSION['tc']['t|dt']['add_book_tip']:(int)0;

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

        if(!empty($sess_login_info)){
            if(!auth_check($db_type='mysql',$arry_conn_user,$sess_login_info['permission'],$auth_type='mssr_tc')){
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }
        }else{
            //權限指標
            $auth_flag=false;
            foreach($arrys_login_info as $inx=>$arry_login_info){
                if(auth_check($db_type='mysql',$arry_conn_user,$arry_login_info['permission'],$auth_type='mssr_tc'))$auth_flag=true;
            }
            if(!$auth_flag){
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }
        }

    //---------------------------------------------------
    //管理者判斷
    //---------------------------------------------------

        if(!empty($sess_login_info)){
            $is_admin=is_admin(trim($sess_login_info['permission']));
            if($is_admin){
                $sess_login_info['responsibilities']=99;
            }
        }

    //---------------------------------------------------
    //系統權限判斷
    //---------------------------------------------------
    //1     校長
    //3     主任
    //5     帶班老師
    //12    行政老師
    //14    主任帶一個班
    //16    主任帶多個班
    //22    老師帶多個班

        if(!empty($sess_login_info)){
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_book');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

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
        $title="明日星球,教師中心";

        //-----------------------------------------------
        //教師中心路徑選單
        //-----------------------------------------------

            $auth_sys_name_arry=auth_sys_name_arry();
            $FOLDER=explode('/',dirname($_SERVER['PHP_SELF']));
            $sys_ename=$FOLDER[count($FOLDER)-4];
            $mod_ename=$FOLDER[count($FOLDER)-3];
            $sys_cname='';  //系統名稱
            $mod_cname='';  //模組名稱

            foreach($auth_sys_name_arry as $key=>$val){
                if($key==$sys_ename){
                    $sys_cname=$val;
                }elseif($key==$mod_ename){
                    $mod_cname=$val;
                }
            }

            if((trim($sys_cname)=='')||(trim($mod_cname)=='')){
                $err ='teacher_center_path err!';

                if(1==2){//除錯用
                    echo "<pre>";
                    print_r($err);
                    echo "</pre>";
                    die();
                }
            }

            //連結路徑
            $sys_url ="";
            $sys_page=str_repeat("../",2)."index.php";
            $sys_arg =array(
                'sys_ename'  =>addslashes($sys_ename)
            );
            $sys_arg=http_build_query($sys_arg);
            $sys_url=$sys_page."?".$sys_arg;
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
    <?php echo robots($allow=false);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/js/string/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../../css/def.css" media="all" />

    <style>
        /* 容器微調 */
        #container, #content, #teacher_datalist_tbl{
            width:760px;
        }
        /* bubble */
        .tip-bubble {
          position: relative;
          background-color: #202020;
          width: 100px;
          padding: 20px;
          color: #CCC;
          text-align: center;
          border-radius: 10px;
          margin: 50px;
          border: 1px solid #111;
          border-shadow: 1px 1px 2px #CCC;
          text-shadow: 0px 0px 5px #404040;
        }

        .tip-bubble:after {
          content: '';
          position: absolute;
          width: 0;
          height: 0;
          border: 15px solid;
        }

        /* Position the Arrow */
        .tip-bubble-top:after {
          border-bottom-color: #202020;
          left: 50%;
          bottom: 100%;
          margin-left: -15px;
        }
        .tip-bubble-right:after {
          border-left-color: #202020;
          left: 100%;
          top: 50%;
          margin-top: -15px;
        }

        .tip-bubble-bottom:after {
          border-top-color: #202020;
          top: 100%;
          left: 50%;
          margin-left: -15px;
        }

        .tip-bubble-left:after {
          border-right-color: #202020;
          top: 50%;
          right: 100%;
          margin-top: -15px;
        }
    </style>
</Head>

<Body>

<!-- 容器區塊 開始 -->
<div id="container">

    <!-- 內容區塊 開始 -->
    <div id="content">
        <table id="teacher_datalist_tbl" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td align="left" valign="middle" width="400px">
                    <!-- 教師中心路徑選單 開始 -->
                    <div id="teacher_center_path">
                        <table id="teacher_center_path_cont" border="0" width="100%">
                            <tr>
                                <td align="left" valign="middle" class="menu_dot">
                                    <span class="menu_cont">
                                        <img width="12" height="12" src="../../../../../../img/icon/blue.jpg" border="0">
                                        <a href="../../<?php echo htmlspecialchars($sys_url);?>">教師中心</a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="../../<?php echo htmlspecialchars($sys_url);?>">
                                            <?php echo htmlspecialchars($sys_cname);?>
                                        </a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="javascript:void(0);"><?php echo htmlspecialchars($mod_cname);?></a>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- 教師中心路徑選單 結束 -->
                </td>
                <td align="right" valign="middle">
                    <!-- 查詢表單列 開始 -->
                    <div id="qform">
                        <span id="qform1"></span>
                    </div>
                    <!-- 查詢表單列 結束 -->
                </td>
            </tr>
            <tr>
                <td width="760px" colspan="2">
                    <!-- 資料內容 開始 -->
                    <?php
                        //呼叫頁面
                        page_ok($title);
                    ?>
                    <!-- 資料內容 結束 -->
                </td>
            </tr>
        </table>
    </div>
    <!-- 內容區塊 結束 -->

</div>
<!-- 容器區塊 結束 -->

<!-- 快速切換區塊 開始 -->
<?php //echo fast_area($rd=4);?>
<!-- 快速切換區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    window.onload=function(){
        //快速切換設置
        //fast_area_config('#fast_area',0,0);
    }

</script>

<?php function page_ok($title="") {?>
<?php
//-------------------------------------------------------
//page_ok 區塊 -- 開始
//-------------------------------------------------------

    //---------------------------------------------------
    //外部變數
    //---------------------------------------------------

        //config.php
        global $PAGE_SELF;
        global $FOLDER_SELF;
        global $nl;
        global $tab;
        global $fso_enc;
        global $page_enc;

        //local
        global $psize;
        global $pinx;

        global $add_book_tip;

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        global $arrys_login_info;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
?>
<!-- 內容 開始 -->
<form id='Form1' name='Form1' method='post' onsubmit="return false;">
    <table align="center" border="0" width="750px" class="table_style0" style="position:relative;top:30px;display:none;">
        <tr>
            <td height="30px" class="fc_blue0" colspan="2">
                新增書籍資料
            </td>
        </tr>
        <tr class="fc_gray0">
            <td align="right" width="150px" height="35px" class="b_line gr_dashed">
                <span class="fc_red1">*</span>
                請選擇書籍類型：
            </td>
            <td class="b_line gr_dashed">

                <table width="150px" cellpadding="0" cellspacing="0" border="1" align="left"
                style="background-color:#c6ecff;border-color:#c6ecff;position:relative;margin:30px 15px;"
                onmouseover="this.style.cursor='pointer'"
                onclick="location.href='../basic/book_library/sel_add_source.php?psize=<?php echo $psize;?>&pinx=<?php echo $pinx;?>';"/>
                    <tr align="center">
                        <td height="130px">
                            <span class='fc_gray0'>有圖書館條碼的書</span><br/><br/>
                            <span class='fc_gray0 fc_red0'>(適合圖書館人員或<br/>各班老師)</span>
                        </td>
                    </tr>
                </table>

                <table width="150px" cellpadding="0" cellspacing="0" border="1" align="left"
                style="background-color:#c6ecff;border-color:#c6ecff;position:relative;margin:30px 15px;"
                onmouseover="this.style.cursor='pointer'"
                onclick="location.href='../basic/book_class/addF.php?psize=<?php echo $psize;?>&pinx=<?php echo $pinx;?>';"/>
                    <tr align="center">
                        <td height="130px">
                            <span class='fc_gray0'>只有ISBN條碼的書</span><br/><br/>
                            或<br/><br/>
                            <span class='fc_gray0'>只有ISSN條碼的書</span><br/><br/>
                            <span class='fc_gray0 fc_red0'>(適合各班老師)</span>
                        </td>
                    </tr>
                </table>

                <table width="150px" cellpadding="0" cellspacing="0" border="1" align="left"
                style="background-color:#c6ecff;border-color:#c6ecff;position:relative;margin:30px 15px;"
                onmouseover="this.style.cursor='pointer'"
                onclick="location.href='../basic/book_ncode/addF.php?psize=<?php echo $psize;?>&pinx=<?php echo $pinx;?>';"/>
                    <tr align="center">
                        <td height="130px">
                            <span class='fc_gray0'>完全沒有條碼的書</span><br/><br/>
                            <span class='fc_gray0 fc_red0'>(適合各班老師)</span>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>

        <tr>
            <td align="right" colspan="2">
                <input type="hidden" id="psize" name="psize" value="<?php echo addslashes($psize);?>">
                <input type="hidden" id="pinx" name="pinx" value="<?php echo addslashes($pinx);?>">

                <input type="button" id="BtnB" name="BtnB" value="返回" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="2" onmouseover="this.style.cursor='pointer'">
            </td>
        </tr>
    </table>
</form>
<!-- 內容 結束 -->

<!-- 新增提示 結束 -->
<table id="tbl_add_prompt" cellpadding="0" cellspacing="0" border="0" width="100%" style="display:none;"/>
    <tr><td><img src="../../../../img/book/book/add_prompt.jpg" width="760px" height="450px" border="0" alt="提示"/></td></tr>
    <tr><td>
        <input type="button" id="BtnP" name="BtnP" value="下一步"
        style="position:relative;top:5px;" onmouseover="this.style.cursor='pointer'">
        <input type="button" id="BtnR" name="BtnR" value="上一頁"
        style="position:relative;top:5px;" onmouseover="this.style.cursor='pointer'">
    </td></tr>
</table>
<!-- 新增提示 結束 -->

<?php
//-------------------------------------------------------
//資料繫結
//-------------------------------------------------------
?>
<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //變數
    var nl    ='\r\n';
    var pinx  =<?php echo addslashes($pinx);?>;     //單頁筆數
    var psize =<?php echo addslashes($psize);?>;    //目前分頁索引
    var add_book_tip =<?php echo addslashes($add_book_tip);?>;

    var oForm1=document.getElementById('Form1');    //表單
    var oBtnB =document.getElementById('BtnB');     //返回
    var oBtnP =document.getElementById('BtnP');     //提示確認鈕
    var oBtnR =document.getElementById('BtnR');     //上一頁

    //新增提示
    var otbl_add_prompt =document.getElementById('tbl_add_prompt');

    $(function(){
        if(add_book_tip!==1){
            $.blockUI({
                message:$(otbl_add_prompt),
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: 1,
                    color: '#fff',
                    top:  0,
                    left: 0,
                    width: '760px'
                },
                overlayCSS:{
                    backgroundColor:'#000',
                    opacity:0.6,
                    cursor:'default'
                }
            });
        }else{
            //關閉提示
            $.unblockUI();
            $('.table_style0').show();
        }
    });

    oBtnP.onclick=function(){
    //提示確認鈕

        //關閉提示
        $.unblockUI();
        $('.table_style0').show();
    }

    oBtnB.onclick=function(){
    //返回

        $.blockUI({
            message:'<h2 class="fc_white0">處理中，請稍後 !</h2>',
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity:.8,
                color: '#437C85'
            }
        });

        var url ='';
        var page=str_repeat('../',2)+'index.php';
        var arg ={
            'psize':psize,
            'pinx' :pinx
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

    oBtnR.onclick=function(){
    //上一頁

        $.blockUI({
            message:'<h2 class="fc_white0">處理中，請稍後 !</h2>',
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity:.8,
                color: '#437C85'
            }
        });

        var url ='';
        var page=str_repeat('../',2)+'index.php';
        var arg ={
            'psize':psize,
            'pinx' :pinx
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

</script>

<?php
//-------------------------------------------------------
//page_ok 區塊 -- 結束
//-------------------------------------------------------
?>
<?php };?>