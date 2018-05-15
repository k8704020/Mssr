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
        require_once(str_repeat("../",4).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/db/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['center']['teacher_center']){
            $url=str_repeat("../",5).'index.php';
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
    //99    管理者

        if(!empty($sess_login_info)){
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_teacher_rec');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //SESSION
        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用

    //---------------------------------------------------
    //串接SQL
    //---------------------------------------------------

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

    //---------------------------------------------------
    //分頁處理
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
            $sys_ename=$FOLDER[count($FOLDER)-2];
            $mod_ename=$FOLDER[count($FOLDER)-1];
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
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/table/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" />

    <style>
        /* 容器微調 */
        #container, #content, #teacher_datalist_tbl{
            width:760px;
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
                                        <img width="12" height="12" src="../../../../img/icon/blue.jpg" border="0">
                                        <!-- <a href="../../index.php">教師中心</a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span> -->
                                        <a href="<?php echo htmlspecialchars($sys_url);?>">
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
                <td colspan="2">
                    <!-- 資料列表 開始 -->
                        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table_style2" style="position:relative;top:20px;"/>
                            <tr align="center" valign="middle" class="bg_gray1 fc_white0">
                                <td height="30px">
                                    教師快速登記書籍
                                </td>
                            </tr>
                            <tr align="center" valign="middle">
                                <td height="50px" align="center">
                                    <br/><br/>
                                    <span style="position:relative;top:20px;right:10px;" class="fc_blue0">我要登記</span>
                                    <span style="position:relative;top:20px;right:10px;">
                                        <img id="img_blue" width="12" height="12" src="../../../../img/icon/blue.jpg" border="0">
                                    </span>

                                    <span class="fc_red1">*</span>
                                    <span style="position:relative;left:2px;" class="fc_gray0">請輸入書籍編號：</span>
                                    <span style="position:relative;left:2px;" class="fc_gray0">
                                        <input type="text" id="book_code" name="book_code" value="" size="20" maxlength="20" class="form_text" style="width:120px;" tabindex="1">
                                    </span>

                                    <br/><br/>

                                    <input id="chkboox" style="position:relative;left:52px;top:2px;" type="checkbox" tabindex="2">
                                    <span style="position:relative;left:52px;" class="fc_gray0">請輸入貼紙編號：</span>
                                    <span style="position:relative;left:52px;" class="fc_gray0">
                                        <input type="text" id="book_no" name="book_no" value="" size="20" maxlength="20" class="form_text" style="width:120px;" tabindex="3" disabled>
                                        <input id="BtnA" type="button" value="送出" class="ibtn_gr3020" tabindex="4" onclick="add();"
                                        style="position:relative;left:15px;bottom:20px;" onmouseover="this.style.cursor='pointer';">
                                    </span>
                                    <br/><br/>
                                </td>
                            </tr>
                        </table>

                        <table id="tbl_teacher_rec" width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="position:relative;top:55px;"/>
                            <tr>
                                <!-- 在此設定寬高 -->
                                <td width="100%" height="55px" align="center" valign="top">
                                <!-- 內容 -->
                                    <table id="mod_data_tbl" border="0" width="100%" cellpadding="5" cellspacing="0" class="table_style2">
                                        <tr height="30px" align="center" valign="middle" class="bg_gray1 fc_white0">
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td width="300px" align="center" valign="middle">
                                                <div id="teacher_rec_msg"></div>
                                            </td>
                                        </tr>
                                    </table>
                                <!-- 內容 -->
                                </td>
                            </tr>
                        </table>
                    <!-- 資料列表 結束 -->
                </td>
            </tr>
        </table>
    </div>
    <!-- 內容區塊 結束 -->

</div>
<!-- 容器區塊 結束 -->

<!-- 快速切換區塊 開始 -->
<?php //echo fast_area($rd=2);?>
<!-- 快速切換區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //變數
    var nl='\r\n';

    //物件
    var oBtnA=document.getElementById('BtnA');
    var obook_code=document.getElementById('book_code');
    var obook_no=document.getElementById('book_no');
    var ochkboox=document.getElementById('chkboox');
    var otbl_teacher_rec=document.getElementById('tbl_teacher_rec');
    var oteacher_rec_msg=document.getElementById('teacher_rec_msg');

    //ajax設定
    var $_url           ="add/addA.php";
    var $_type          ="POST";
    var $_datatype      ="json";

    $(function(){

        //駐點
        obook_code.focus();

        //啟動閃爍
        blink_fadeout();

        //隱藏登記訊息
        $(otbl_teacher_rec).hide();

        //快速切換設置
        //fast_area_config('#fast_area',0,0);
    });

    function choose_identity(){
    //開啟身分選擇區塊
        $.blockUI({
            message:$('#choose_identity'),
            css:{
                width:'260px'
            },
            overlayCSS:{
                backgroundColor:'#000',
                opacity:0.6,
                cursor:'default'
            }
        });
    }

    function choose_class_code(){
    //開啟班級選擇區塊
        $.blockUI({
            message:$('#choose_class_code'),
            css:{
                width:'260px'
            },
            overlayCSS:{
                backgroundColor:'#000',
                opacity:0.6,
                cursor:'default'
            }
        });
    }

    function _ajax($_url,$_type,$_datatype,$_book_code,$_book_no){
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
                book_code:encodeURI(trim($_book_code)),
                book_no  :encodeURI(trim($_book_no))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
                $.blockUI({
                    message:'<h2>處理中...</h2>',
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

                respones=jQuery.parseJSON(respones);
                var has_find=respones.has_find;     //has_find  查找狀態
                var book_name=respones.book_name;   //book_name 書籍名稱
                var msg=respones.msg;               //err訊息

                if(!has_find){
                    $.unblockUI();
                    alert(msg);
                    obook_code.focus();
                    return false;
                }else{
                    $.unblockUI();
                    //顯示登記訊息
                    $(otbl_teacher_rec).show();
                    $(oteacher_rec_msg).html('<h1>'+'<span class="fc_red1">'+msg+'</span>'+' <input type="button" value="前往書店推薦" class="ibtn_gr9030" onmouseover="_mouseover(this);void(0);" onclick="go_bookstore();void(0);"></h1>');
                }
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                if(ajaxoptions==='timeout'){
                    $.blockUI({
                        message:'<h2 class="fc_red1">連線發生問題，請檢查網路是否斷線 !</h2>',
                        overlayCSS:{
                            backgroundColor:'#000',
                            opacity:0.9,
                            cursor:'default'
                        },
                        timeout: 2000
                    });
                }else{
                    $.blockUI({
                        message:'<h2 class="fc_red1">連線發生問題，請檢查網路是否斷線 !</h2>',
                        overlayCSS:{
                            backgroundColor:'#000',
                            opacity:0.9,
                            cursor:'default'
                        },
                        timeout: 2000
                    });
                }
            },
            complete    :function(){//傳送後處理
            }
        });
    }

    function _mouseover(obj){
        obj.style.cursor='pointer';
    }

    function go_bookstore(){
    //前往書店
        parent.location.href='../../../../../mssr/service/mssr_menu.php';
    }

    function add(){
    //送出
        var book_code=trim(obook_code.value);
        var book_no=trim(obook_no.value);

        if(trim(book_code)===''){
            alert('請輸入書籍編號!');
            obook_code.focus();
            return false;
        }else{
            //隱藏登記訊息
            $(otbl_teacher_rec).hide();
            _ajax($_url,$_type,$_datatype,book_code,book_no);
        }
    }

    function blink_fadeout(){
        $("#img_blue").fadeOut(1000,blink_fadein);
    }
    function blink_fadein(){
        $("#img_blue").fadeIn(1000,blink_fadeout);
    }

    ochkboox.onclick=function(){
        var _checked=this.checked;
        if(_checked===true){
            obook_no.disabled=false;
        }else{
            obook_no.value='';
            obook_no.disabled=true;
        }
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
