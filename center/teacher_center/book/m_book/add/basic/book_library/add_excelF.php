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

                    APP_ROOT.'lib/php/db/code'
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

        //-----------------------------------------------
        //身份設定
        //-----------------------------------------------

            //初始化, 身份參考陣列, 1(校長使用者) | 2(主任使用者) | 3(老師使用者)
            $arrys_identity=array(1,2,3);

            //初始化, 身份數量
            $identity_cno=0;

            //初始化, 選擇身份指標
            $choose_identity_flag=true;

            //身份數量統計
            foreach($arrys_login_info as $responsibilities=>$arry_login_info){
                $responsibilities=(int)$responsibilities;
                if(in_array($responsibilities,$arrys_identity)){
                    $identity_cno++;
                }
            }

            //只有1種身份, 自動回填至 $_SESSION['tc']['t|dt']
            if($identity_cno===0){
                $choose_identity_flag=false;
                $_SESSION['tc']['t|dt']=$arry_login_info;
            }else if($identity_cno===1){
                $choose_identity_flag=false;
                foreach($arrys_login_info as $responsibilities=>$arry_login_info){
                    $_SESSION['tc']['t|dt']=$arry_login_info;
                }
                //移除多餘的班級
                foreach($_SESSION['tc']['t|dt'] as $field_name=>$field_value){
                    if($field_name==='arrys_class_code'){
                        foreach($field_value as $inx=>$val){
                            if($inx!==0){
                                unset($_SESSION['tc']['t|dt']['arrys_class_code'][$inx]);
                            }
                        }
                    }
                }
                //沒有任何班級，指派空陣列
                if(!isset($_SESSION['tc']['t|dt']['arrys_class_code'])){
                    $_SESSION['tc']['t|dt']['arrys_class_code']=array();
                }
            }else{
            //如果已選定身份的話
                if((isset($_SESSION['tc']['t|dt']))&&(!empty($_SESSION['tc']['t|dt']))){
                    $choose_identity_flag=false;
                    //移除多餘的班級
                    foreach($_SESSION['tc']['t|dt'] as $field_name=>$field_value){
                        if($field_name==='arrys_class_code'){
                            foreach($field_value as $inx=>$val){
                                if($inx!==0){
                                    unset($_SESSION['tc']['t|dt']['arrys_class_code'][$inx]);
                                }
                            }
                        }
                    }
                    //沒有任何班級，指派空陣列
                    if(!isset($_SESSION['tc']['t|dt']['arrys_class_code'])){
                        $_SESSION['tc']['t|dt']['arrys_class_code']=array();
                    }
                }
            }

        //-----------------------------------------------
        //basic
        //-----------------------------------------------

            $sess_login_info=(isset($_SESSION['tc']['t|dt']))?$_SESSION['tc']['t|dt']:array();

        //-----------------------------------------------
        //其餘設定
        //-----------------------------------------------

            //清空用戶類型
            switch(is_array($_SESSION['config']['user_type'])){
                case true:
                    foreach($_SESSION['config']['user_types'] as $inx=>$val){
                        if(!in_array(trim($val),array_map("trim",$_SESSION['config']['user_type']))){
                            unset($_SESSION[$val]);
                        }
                    }
                break;

                default:
                    foreach($_SESSION['config']['user_types'] as $inx=>$val){
                        if(trim($val)!=trim($_SESSION['config']['user_type'])){
                            unset($_SESSION[$val]);
                        }
                    }
                break;
            }

            //區域資訊
            if(!in_array('teacher_center',$_SESSION['config']['user_area'])){
                array_push($_SESSION['config']['user_area'],"teacher_center");
            }

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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_book');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //SESSION
        if(isset($sess_login_info['uid'])){$sess_user_id=(int)$sess_login_info['uid'];}
        if(isset($sess_login_info['permission'])){$sess_permission=trim($sess_login_info['permission']);}
        if(isset($sess_login_info['school_code'])){$sess_school_code=trim($sess_login_info['school_code']);}
        if(isset($sess_login_info['responsibilities'])){
            $sess_responsibilities=(int)$sess_login_info['responsibilities'];
            if(in_array($auth_sys_check_lv,array(5,14,16,22))){
                $sess_class_code=trim($sess_login_info['arrys_class_code'][0]['class_code']);
                $sess_grade=(int)$sess_login_info['arrys_class_code'][0]['grade'];
                $sess_classroom=(int)$sess_login_info['arrys_class_code'][0]['classroom'];
            }
        }

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        ////建立連線 user
        //$conn_user=conn($db_type='mysql',$arry_conn_user);

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,教師中心";

        //註腳列
        $footer=footer($rd=7);

        //-----------------------------------------------
        //教師中心路徑選單
        //-----------------------------------------------
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

    <!-- <script type="text/javascript" src="inc/jquery_1.2.3/code.js"></script>
    <script type="text/javascript" src="inc/ajax_file_upload/code.js"></script> -->

    <script type="text/javascript" src="../../../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/jquery/plugin/func/block_ui/code.js"></script>
    <script type="text/javascript" src="inc/jquery.form/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/array/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/fso/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/form/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../../../css/def.css" media="all" />
</Head>

<Body>

<!-- 容器區塊 開始 -->
<div id="container">

    <!-- 標題區塊 開始 -->
    <div id="header" style="">

        <!-- 標題左方區塊 開始 -->
        <ul id="header_left">
            <li>
                <a href="javascript:void(0);">
                    <span>
                        您好 !
                        <?php
                            if(isset($sess_login_info['name'])){
                                echo htmlspecialchars($sess_login_info['name']);
                            }
                        ?>
                        ，歡迎使用 - 圖書館書籍Excel匯入系統。
                    </span>
                </a>
            </li>
        </ul>
        <!-- 標題左方區塊 結束 -->

        <!-- 標題右方區塊 開始 -->

        <!-- 標題右方區塊 結束 -->

    </div>
    <!-- 標題區塊 結束 -->

    <!-- 內容區塊 開始 -->
    <div id="content">

        <!-- 資料列表 開始 -->
        <?php
            //呼叫頁面
            page_ok($title);
        ?>
        <!-- 資料列表 結束 -->

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
    <?php echo google_analysis($allow=false);?>
    <!-- google分析 結束 -->

</div>
<!-- 容器區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    $(function(){

    });

</script>
</Body>
</Html>

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

    $content1='
        <span class="fsize_18 font-family1 font-weight1">說明 :</span>

            將表格內部各欄位填妥後即可上傳建立，資料務必填妥正確，避免書籍資料建立失敗。

            以下為Excel輸入規則:


        <span class="fsize_18 font-family1 font-weight1" style="color:#ff6600;">必填欄位(橘色) :</span>

            <span class="fc_blue1">圖書館條碼 :</span> 可輸入 1~20 碼 英文或數字(含小數點)，且必須是唯一，請務必輸入正確。

            <span class="fc_blue1">書名 :</span> 可輸入 1~50 碼 英文、數字或中文。


        <span class="fsize_18 font-family1 font-weight1" style="color:#009900;">選填欄位(綠色) :</span>

            <span class="fc_blue1">ISBN10碼 :</span> 只能輸入英文、數字，(EX:9747581094)，如未填寫，系統將不填入。

            <span class="fc_blue1">ISBN13碼 :</span> 只能輸入英文、數字，(EX:9780747581086)，如未填寫，系統將不填入。

            <span class="fc_blue1">作者 :</span> 可輸入 1~50 碼 英文、數字或中文。

            <span class="fc_blue1">出版社 :</span> 可輸入 1~30 碼 英文、數字或中文。

            <span class="fc_blue1">頁數 :</span> 只能輸入數字，如未填寫，系統將預設為 0 頁。

            <span class="fc_blue1">字數 :</span> 只能輸入數字，如未填寫，系統將預設為 0 字。

            <span class="fc_blue1">有無注音 :</span> 請輸入數字，(0:無, 1:有)，如未填寫，系統將預設為 0。
    ';

    $content2_1='
        <span class="fsize_18 font-family1 font-weight1">以下為Excel輸入範例 :</span>

    ';

    $content2_2='
        <span class="fc_red0 fsize_18 font-family1 font-weight1">表格下載 :</span><span class="fc_red0">(請擇一下載即可)</span>


            <span class="fc_blue1 fsize_16 font-family1 font-weight1" onclick="download_sample_excel(3);void(0);"  onmouseover="mouseover(this);void(0);">1. <u>下載 Excel 2003 版本</u></span>

            <span class="fc_blue1 fsize_16 font-family1 font-weight1" onclick="download_sample_excel(10);void(0);" onmouseover="mouseover(this);void(0);">2. <u>下載 Excel 2010 版本</u></span>
    ';

    $content3='
        <span class="fsize_18 font-family1 font-weight1">說明 :</span>

            將表格填妥送出後，若有錯誤資料，畫面將會出現「下載書籍匯入失敗清單」，

            該Eexcl檔會顯示建立失敗的書籍有哪些，以及告知建立失敗原因。


            您可以直接下載並修改完後，再次上傳表格，直到畫面不再出現「下載書籍匯入失敗清單」為止。


        <span class="fsize_18 font-family1 font-weight1">上傳檔案 :</span><span class="fc_red0">(請使用本網站下載制式表格，單檔大小限制為3MB、筆數限制最多為5千筆)</span>
    ';
?>

<!-- 內容 開始 -->

    <!-- 選單 -->
    <table cellpadding="0" cellspacing="0" border="0" width="100%" class="" style="margin-top:35px;"/>
        <tr align='center' class='fsize_16 font-family1 font-weight1' style='color:#8e4408;'>
            <td height='35px' bgcolor='' width='' style=''>&nbsp;</td>
            <td height='35px' bgcolor='#87CDDC' width='20%' style='border:1px solid #87CDDC;' name='tab' att='yes' onclick='tab(0);void(0);' onmouseover='mouseover(this);void(0);'>1. 使用說明</td>
            <td height='35px' bgcolor='' width='20%' style='border:1px solid #87CDDC;' name='tab' att='no' onclick='tab(1);void(0);' onmouseover='mouseover(this);void(0);'>2. 表格下載</td>
            <td height='35px' bgcolor='' width='20%' style='border:1px solid #87CDDC;' name='tab' att='no' onclick='tab(2);void(0);' onmouseover='mouseover(this);void(0);'>3. 開始匯入</td>
        </tr>
    </table>

    <!-- 內容 -->
    <table cellpadding="0" cellspacing="0" border="0" width="100%" class="mod_data_tbl_outline" style="margin-bottom:30px;"/>
        <tr name='content' class='fsize_13 font-family1' style=''>
            <td width='100%' height='480px'>
                <table cellpadding="0" cellspacing="0" border="0" width="100%" height='100%'/>
                    <tr align='center'>
                        <td align='left' valign='middle' width='750px'><pre><?php echo $content1;?></pre></td>
                        <td align='left' valign='middle' width=''>
                            <img src="img/icon.jpg" width="185" height="290" border="0" alt="形象圖"/>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr name='content' style='display:none;'>
            <td width='100%' height='480px' valign='top'>
                <pre style='position:relative;margin-top:30px;display:block;'><?php echo $content2_1;?></pre>
                <table align='center' cellpadding="0" cellspacing="0" border="0" width="800" height='225'/>
                    <tr align='center'>
                        <td align='center' valign='top'>
                            <img src="img/example.png" width="825px" height="200px" border="0" alt="Excel輸入範例"/>
                        </td>
                    </tr>
                </table>
                <pre style='position:relative;margin-top:30px;display:block;'><?php echo $content2_2;?></pre>
            </td>
        </tr>
        <tr name='content' style='display:none;'>
            <td width='100%' height='480px' align='left' valign='top'>
                <pre style='position:relative;margin-top:30px;display:block;'><?php echo $content3;?></pre>

                <center id='center_file' style='position:relative;top:20px;display:block;'>
                    <form action="add_excelA.php" method="post" id="ajaxform">
                        <input type="file" id="file" name="file" value="">
                        <input type="button" id='BtnU' value="上傳檔案" class="ibtn_gr6030" onmouseover="this.style.cursor='pointer'"
                        onclick="ajax_file_upload();void(0);">
                    </form>
                </center>

                <!-- 結果下載 -->
                <table cellpadding="0" cellspacing="0" border="0" width="80%" align='center' style='position:relative;top:55px;'/>
                    <tr>
                        <td width='250px' align='left'>
                            <span id='success' style='display:none;' class="fc_blue1 fsize_16 font-family1 font-weight1" onmouseover="mouseover(this);void(0);"><u>下載書籍匯入成功清單</u></span>
                        </td>
                        <td width='' align='left'>
                            <span id='error' style='display:none;' onmouseover="mouseover(this);void(0);"><span class="fc_blue1 fsize_16 font-family1 font-weight1" ><u>下載書籍匯入失敗清單</u></span><span class="fc_red0">(下載並修改完後，直接再次上傳即可)</span></span>
                        </td>
                    </tr>
                </table>

                <!-- 訊息視窗 -->
                <table cellpadding="0" cellspacing="0" border="0" width="80%" height='125px' align='center' class="mod_data_tbl_outline" style='position:relative;top:80px;'/>
                    <tr>
                        <td id='msg' valign='top' style='padding:5px;'>
                            上傳作業準備中...<br/>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

<!-- 內容 結束 -->

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

        var otabs       =document.getElementsByName('tab');
        var ocontents   =document.getElementsByName('content');
        var ocenter_file=document.getElementById('center_file');
        var ofile       =document.getElementById('file');
        var oBtnU       =document.getElementById('BtnU');
        var osuccess    =document.getElementById('success');
        var oerror      =document.getElementById('error');
        var omsg        =document.getElementById('msg');

    //---------------------------------------------------
    //函式
    //---------------------------------------------------

        function download_sample_excel(version){
        //下載Excel制式表格
            version      =parseInt(version);
            var url      ='download/download_sample_excelF.php?version='+version;
            var oiframe  =document.createElement("IFRAME");
            oiframe.src  =url;
            oiframe.style.display="none";

            //清除新開
            $(document.body).find("IFRAME").remove();
            document.body.appendChild(oiframe);
        }

        function tab(cno){
        //頁籤變換
            cno=parseInt(cno);
            for(var i=0;i<otabs.length;i++){
                var otab=otabs[i];
                if(i===cno){
                    otab.bgColor='#87CDDC';
                    $(ocontents[i]).fadeIn(500);
                }else{
                    otab.bgColor='#FFFFFF';
                    $(ocontents[i]).hide();
                }
            }
        }

        function download_result_excel(flag,extension){
        //下載Excel結果表格
            flag=trim(flag);
            extension=trim(extension);
            var url      ='download/download_result_excelF.php?flag='+flag+'&extension='+extension;
            var oiframe  =document.createElement("IFRAME");
            oiframe.src  =url;
            oiframe.style.display="none";

            //清除新開
            $(document.body).find("IFRAME").remove();
            document.body.appendChild(oiframe);
        }

        function block_ui(){
            $.blockUI({
                message:'<h2 class="fc_white0">檔案上傳處理中，請勿關閉頁面 !</h2>',
                css: {
                    top:'30%',
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity:.8,
                    color: '#437C85'
                }
            });
        }

        function ajax_file_upload(){
        //ajax Excel檔案上傳
            var arry_type=[
                'xls',
                'xlsx'
            ]
            var file_val=trim(ofile.value);
            var info=pathinfo(file_val);
            var filename =info['filename'];
            var extension=info['extension'];

            if(file_val===''){
                alert('請選擇上傳的檔案!');
                return false;
            }
            if(!in_array(extension.toLowerCase(),arry_type,false)){
                alert('請選擇Excel檔案!');
                return false;
            }

            //上傳進度
            omsg.innerHTML ='上傳作業準備中...<br/>';
            omsg.innerHTML+='檔案上傳開始...<br/>';
            omsg.innerHTML+='檔案上傳中...<br/>';
            oBtnU.disabled=true;
            block_ui();

            $("#ajaxform").ajaxSubmit({
                beforeSubmit: function(){
                },
                success: function(respone,st,xhr,$form){
                    //console.log(respone);
                    //return false;

                    var respone     =JSON.parse(respone);
                    var flag        =(respone.flag);
                    var extension   =(respone.extension);
                    var succes_cno  =parseInt(respone.succes_cno);
                    var error_cno   =parseInt(respone.error_cno);
                    var msg         =(respone.msg);

                    if(flag==='true'){
                        omsg.innerHTML+='檔案上傳成功...<br/><br/>';
                        omsg.innerHTML+='成功筆數:'+succes_cno+'筆<br/>';
                        omsg.innerHTML+='失敗筆數:'+error_cno+'筆<br/>';

                        $(osuccess).hide();
                        $(oerror).hide();

                        if(succes_cno>0){
                            $(osuccess).show();
                            osuccess.onclick=function(e){
                                download_result_excel('Successful_books_list',extension);
                            }
                        }
                        if(error_cno>0){
                            $(oerror).show();
                            oerror.onclick=function(e){
                                download_result_excel('Error_books_list',extension);
                            }
                        }
                    }else{
                        alert(msg);
                        omsg.innerHTML+='檔案上傳發生問題...<br/><br/>';
                        omsg.innerHTML+=msg+'...<br/>';
                    }
                    $.unblockUI();
                    oBtnU.disabled=false;
                    return true;
                },
                error: function(){
                    $.unblockUI();
                    omsg.innerHTML+='檔案上傳失敗，請再上傳一次...<br/>';
                    oBtnU.disabled=false;
                    return true;
                }
            });

            ////ajax啟用
            //$.ajaxFileUpload({
            //    timeout : 0,
            //    url:'add_excelA.php',
            //    secureuri:false,
            //    fileElementId:'file',
            //    dataType:'json',
            //    data:{
            //        //"name":user.now
            //    },
            //    success:function(data,status){
            //        if(typeof(data.error)==='undefined'){
            //
            //            var flag        =trim(data.flag);
            //            var extension   =trim(data.extension);
            //            var succes_cno  =parseInt(data.succes_cno);
            //            var error_cno   =parseInt(data.error_cno);
            //            var msg         =trim(data.msg);
            //
            //            if(flag==='true'){
            //                omsg.innerHTML+='檔案上傳成功...<br/><br/>';
            //                omsg.innerHTML+='成功筆數:'+succes_cno+'筆<br/>';
            //                omsg.innerHTML+='失敗筆數:'+error_cno+'筆<br/>';
            //
            //                $(osuccess).hide();
            //                $(oerror).hide();
            //
            //                if(succes_cno>0){
            //                    $(osuccess).show();
            //                    osuccess.onclick=function(e){
            //                        download_result_excel('Successful_books_list',extension);
            //                    }
            //                }
            //                if(error_cno>0){
            //                    $(oerror).show();
            //                    oerror.onclick=function(e){
            //                        download_result_excel('Error_books_list',extension);
            //                    }
            //                }
            //            }else{
            //                alert(msg);
            //                omsg.innerHTML+='檔案上傳發生問題...<br/><br/>';
            //                omsg.innerHTML+=msg+'...<br/>';
            //            }
            //        }else{
            //        //錯誤提示
            //            if(data.error!==''){
            //                omsg.innerHTML+='檔案上傳失敗，請再上傳一次...<br/>';
            //            }else{
            //                omsg.innerHTML+='檔案上傳失敗，請再上傳一次...<br/>';
            //            }
            //        }
            //        $.unblockUI();
            //        oBtnU.disabled=false;
            //        return true;
            //    },
            //    error:function(data,xml,status,e){
            //        $.unblockUI();
            //        omsg.innerHTML+='檔案上傳失敗，請再上傳一次...<br/>';
            //        oBtnU.disabled=false;
            //        return true;
            //    }
            //});
        }

        function mouseover(obj){
            obj.style.cursor='pointer';
        }

    //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        $(function(){

        });

</script>

<?php
//-------------------------------------------------------
//page_ok 區塊 -- 結束
//-------------------------------------------------------
?>
<?php };?>
