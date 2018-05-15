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
        require_once(str_repeat("../",7).'config/config.php');

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
            $url=str_repeat("../",8).'index.php';
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
        $_SESSION['tc']['t|dt']['add_book_tip']=1;

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
    //書籍編號
    //---------------------------------------------------

        //-----------------------------------------------
        //字首部分
        //-----------------------------------------------

            $prefix=trim($sess_login_info['school_code']);

        //-----------------------------------------------
        //建立者
        //-----------------------------------------------

            $create_by=(int)$sess_login_info['uid'];

        //-----------------------------------------------
        //亂數部分
        //-----------------------------------------------

            //計算前面長度
            $sid_cno=0;
            $sid_cno+=mb_strlen($prefix,$page_enc);
            $sid_cno+=mb_strlen($create_by,$page_enc);

            //亂數種子
            mt_srand(time());

            //亂數長度
            $size=(int)15-(int)$sid_cno;

            //取回亂數
            $rnd ='';
            for($i=1;$i<=$size;$i++){

               $arry=str_split(strval(mt_rand()),1);
               shuffle($arry);
               $rnd.=$arry[mt_rand(0,count($arry)-1)];
            }

        //-----------------------------------------------
        //產生
        //-----------------------------------------------

            if($size>0){
                $time=$create_by.$rnd;
                $book_library_code=$prefix.$time;
            }else{
                $time=$create_by;
                $book_library_code=$prefix.$time;
            }

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //建立連線 mssr
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

    //---------------------------------------------------
    //SQL串接
    //---------------------------------------------------

        //-----------------------------------------------
        //檢核書籍編號是否重複
        //-----------------------------------------------

            $book_library_code=mysql_prep($book_library_code);
            $sess_school_code =mysql_prep(trim($sess_login_info['school_code']));

            //重複檢核
            while(1===1){
                $sql="
                    SELECT
                        `book_library_code`
                    FROM `mssr_book_library`
                    WHERE 1=1
                        AND `book_library_code`='{$book_library_code}'
                        AND `school_code`      ='{$sess_school_code }';
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(empty($arrys_result)){
                    break;
                }else{
                //------------------------------------------
                //書籍編號
                //------------------------------------------

                    //--------------------------------------
                    //字首部分
                    //--------------------------------------

                        $prefix=trim($sess_login_info['school_code']);

                    //--------------------------------------
                    //建立者
                    //--------------------------------------

                        $create_by=((int)$sess_login_info['uid']);

                    //--------------------------------------
                    //亂數部分
                    //--------------------------------------

                        //計算前面長度
                        $sid_cno=0;
                        $sid_cno+=mb_strlen($prefix,$page_enc);
                        $sid_cno+=mb_strlen($create_by,$page_enc);

                        //亂數種子
                        mt_srand(time());

                        //亂數長度
                        $size=(int)15-(int)$sid_cno;

                        //取回亂數
                        $rnd ='';
                        for($i=1;$i<=$size;$i++){

                           $arry=str_split(strval(mt_rand()),1);
                           shuffle($arry);
                           $rnd.=$arry[mt_rand(0,count($arry)-1)];
                        }

                    //--------------------------------------
                    //產生
                    //--------------------------------------

                        if($size>0){
                            $time=$create_by.$rnd;
                            $book_library_code=$prefix.$time;
                        }else{
                            $time=$create_by;
                            $book_library_code=$prefix.$time;
                        }
                }
            }

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
            $sys_ename=$FOLDER[count($FOLDER)-5];
            $mod_ename=$FOLDER[count($FOLDER)-4];
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
    <link rel="stylesheet" type="text/css" href="../../../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/form/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/date/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../../../css/def.css" media="all" />

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
                                        <img width="12" height="12" src="../../../../../../../img/icon/blue.jpg" border="0">
                                        <a href="../../../../../index.php">教師中心</a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="../../../<?php echo htmlspecialchars($sys_url);?>">
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
<?php //echo fast_area($rd=5);?>
<!-- 快速切換區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

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
        global $time;
        global $book_library_code;

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        global $arrys_login_info;
        global $sess_login_info;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $sess_school_code=trim($sess_login_info['school_code']);

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
?>
<!-- 內容 開始 -->
<form id='Form1' name='Form1' method='post' onsubmit="return false;">
    <table id="tbl1" align="center" border="0" width="750px" class="table_style0" style="position:relative;top:25px;">
        <tr>
            <td height="30px" class="fc_blue0" colspan="8">
                新增書籍資料
                <span class="fsize_18 fc_red0" style="float:right;position:relative;right:10px;">
                    <strong onclick="add_row();void(0);" onmouseover="this.style.cursor='pointer'">+</strong>
                        &nbsp;
                    <strong onclick="del_row();void(0);" onmouseover="this.style.cursor='pointer'">-</strong>
                </span>
            </td>
        </tr>
        <tr class="fc_gray0">
            <td align="right" width="55px" height="45px" class="b_line gr_dashed">
                條碼：
            </td>
            <td class="b_line gr_dashed" width="120px">
                <?php echo htmlspecialchars(strtoupper($book_library_code));?>
                <span name="span_time" style="display:none;"><?php echo $time;?></span>
                <input type="text" id="book_library_code[]" name="book_library_code[]" value="<?php echo htmlspecialchars(strtoupper($book_library_code));?>" size="10" maxlength="50"
                class="form_text" style="width:115px;display:none;">
            </td>
            <td align="right" width="55px" height="45px" class="b_line gr_dashed">
                <span class="fc_red1">*</span>
                書名：
            </td>
            <td class="b_line gr_dashed" width="120px">
                <input type="text" id="book_name[]" name="book_name[]" value="" size="10" maxlength="50"
                class="form_text" style="width:115px;">
            </td>
            <td align="right" width="55px" height="45px" class="b_line gr_dashed">
                作者：
            </td>
            <td class="b_line gr_dashed" width="120px">
                <input type="text" id="book_author[]" name="book_author[]" value="" size="10" maxlength="30"
                class="form_text" style="width:115px;">
            </td>
            <td align="right" width="70px" height="45px" class="b_line gr_dashed">
                出版社：
            </td>
            <td class="b_line gr_dashed">
                <input type="text" id="book_publisher[]" name="book_publisher[]" value="" size="10" maxlength="30"
                class="form_text" style="width:115px;">
            </td>
        </tr>
    </table>

    <table align="center" border="0" width="750px" class="table_style0" style="position:relative;top:25px;">
        <tr>
            <td align="right" colspan="8">
                <input type="hidden" id="psize" name="psize" value="<?php echo addslashes($psize);?>">
                <input type="hidden" id="pinx" name="pinx" value="<?php echo addslashes($pinx);?>">

                <input type="text" id="pdf_flag" name="pdf_flag" value="1" style="display:none;">
                <input type="button" id="BtnP" name="BtnP" value="列印條碼" class="ibtn_gr9030" style="margin:10px 0px;" onmouseover="this.style.cursor='pointer'">
                <input type="button" id="BtnS" name="BtnS" value="送出" class="ibtn_gr6030" style="margin:10px 0px;" onmouseover="this.style.cursor='pointer'">
                <input type="button" id="BtnB" name="BtnB" value="返回" class="ibtn_gr6030" style="margin:10px 0px;" onmouseover="this.style.cursor='pointer'">
            </td>
        </tr>
    </table>
</form>
<!-- 內容 結束 -->

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
    var nl              ='\r\n';
    var pinx            =<?php echo addslashes($pinx);?>;     //單頁筆數
    var psize           =<?php echo addslashes($psize);?>;    //目前分頁索引
    var time            =<?php echo addslashes($time);?>;
    var sess_school_code=trim('<?php echo addslashes(strtoupper($sess_school_code));?>');

    //物件
    var oForm1      =document.getElementById('Form1');    //表單
    var oBtnP       =document.getElementById('BtnP');     //列印條碼
    var oBtnS       =document.getElementById('BtnS');     //送出
    var oBtnB       =document.getElementById('BtnB');     //返回
    var otbl1       =document.getElementById('tbl1');
    var opdf_flag   =document.getElementById('pdf_flag');

    function add_row(){
    //新增列
        var ospan_times   =document.getElementsByName('span_time');
        var span_times_num=parseInt(ospan_times.length);
        var time          =parseInt(ospan_times[span_times_num-1].innerHTML)+1;
        var _html ="";
            _html+='<tr class="fc_gray0">';
                _html+='<td align="right" width="55px" height="45px" class="gr_dashed">';
                    _html+='條碼：';
                _html+='</td>';
                _html+='<td class="gr_dashed" width="120px">';
                    _html+=sess_school_code+time+'<span name="span_time" style="display:none;">'+time+'</span><input type="text" id="book_library_code[]" name="book_library_code[]" value="'+sess_school_code+''+time+'" size="10" maxlength="50" class="form_text" style="width:115px;display:none;">';
                _html+='</td>';
                _html+='<td align="right" width="55px" height="45px" class="gr_dashed">';
                    _html+='<span class="fc_red1">*</span> 書名：';
                _html+='</td>';
                _html+='<td class="gr_dashed" width="120px">';
                    _html+='<input type="text" id="book_name[]" name="book_name[]" value="" size="10" maxlength="50" class="form_text" style="width:115px">';
                _html+='</td>';
                _html+='<td align="right" width="55px" height="45px" class="gr_dashed">';
                    _html+='作者：';
                _html+='</td>';
                _html+='<td class="gr_dashed" width="120px">';
                    _html+='<input type="text" id="book_author[]" name="book_author[]" value="" size="10" maxlength="30" class="form_text" style="width:115px">';
                _html+='</td>';
                _html+='<td align="right" width="70px" height="45px" class="gr_dashed">';
                    _html+='出版社：';
                _html+='</td>';
                _html+='<td class="gr_dashed">';
                    _html+='<input type="text" id="book_publisher[]" name="book_publisher[]" value="" size="10" maxlength="30" class="form_text" style="width:115px">';
                _html+='</td>';
            _html+='</tr>';

        //附加
        $(otbl1).append(_html);
    }

    function del_row(){
    //刪除列
        var rows_num=parseInt(otbl1.rows.length);
        if(rows_num<=2){
            alert('請至少填寫一筆資料');
            return false;
        }else{
            otbl1.deleteRow(parseInt(rows_num-1));
        }
    }

    oBtnP.onclick=function(){
    //列印條碼
        var ospan_times     =document.getElementsByName('span_time');
        var span_times_num  =parseInt(ospan_times.length);
        var time            =parseInt(ospan_times[0].innerHTML);
        var sess_school_code=trim('<?php echo addslashes($sess_school_code);?>');
        oForm1.style.display="none";
        opdf_flag.value     =1;

        var url             ='pdfF.php?book_library_code='+time+'&cno='+span_times_num;
        var oiframe         =document.createElement("IFRAME");
        oiframe.id          ="pdfF";
        oiframe.style.border="0px";
        oiframe.src         =url;
        oiframe.width       ="770px";
        oiframe.height      ="425px";

        //清除新開
        $(document.body).find("IFRAME").remove();
        document.body.appendChild(oiframe);
    }

    oBtnS.onclick=function(){
    //送出
        var obook_names =document.getElementsByName('book_name[]');
        var pdf_flag    =parseInt(opdf_flag.value);
        var arry_err    =[];

        for(var i=0;i<obook_names.length;i++){
            var obook_name=obook_names[i];
            var book_name=trim(obook_name.value);
            if(book_name===''){
                arry_err.push('請輸入書名!');
            }
        }

        if(pdf_flag!==1){
            alert('請先列印書本條碼!');
            return false;
        }else{
            if(arry_err.length!=0){
                alert(arry_err.join(nl));
                return false;
            }else{
                if(confirm('你確定要送出嗎?')){
                    oForm1.action='addA.php'
                    oForm1.submit();
                    return true;
                }else{
                    return false;
                }
            }
        }
    }

    oBtnB.onclick=function(){
    //返回

        var url ='';
        var page=str_repeat('../',2)+'sel_book_type/addF.php';
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

    window.onload=function(){
        ////駐點
        //obook_code.focus();
    }

</script>

<?php
//-------------------------------------------------------
//page_ok 區塊 -- 結束
//-------------------------------------------------------
?>
<?php };?>