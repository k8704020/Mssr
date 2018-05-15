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
                    APP_ROOT.'center/teacher_center/inc/code'
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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_mail');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

    //---------------------------------------------------
    //串接SQL
    //---------------------------------------------------

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //建立連線 user
        $conn_user=conn($db_type='mysql',$arry_conn_user);

        //學校篩選
        $school_filter='';
        if(isset($_SESSION['m_mail']['send_filter'])){
            $school_filter=trim($_SESSION['m_mail']['send_filter']);
        }
        $school_list_en='';
        if(isset($_SESSION['m_mail']['school_list_en'])){
            $school_list_en=trim($_SESSION['m_mail']['school_list_en']);
        }
        $school_list_ch='';
        if(isset($_SESSION['m_mail']['school_list_ch'])){
            $school_list_ch=trim($_SESSION['m_mail']['school_list_ch']);
        }

        $arry_school_name_ch=array();

        //-------------------------------------------
        //學校名稱查詢
        //-------------------------------------------

            $sql="
                SELECT
                    `school_name`,
                    `school_code`
                FROM `school`
                WHERE 1=1
            ";
            $arrys_school_result=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
            if(!empty($arrys_school_result)){
                foreach($arrys_school_result as $arry_school_result){
                    $rs_school_name=trim($arry_school_result['school_name']);
                    $rs_school_code=trim($arry_school_result['school_code']);
                    $arry_school_name_ch[$rs_school_code]=$rs_school_name;
                }
            }

        //-------------------------------------------
        //學校查詢 - 推廣人員專用
        //-------------------------------------------

            //推廣人員專用
            if((int)$sess_login_info['responsibilities']===99){

                $sql ="";
                $sql.="
                    SELECT *
                    FROM `school`
                    WHERE 1=1
                ";
                if($school_filter!==''){
                    $school_filter=mysql_prep($school_filter);
                    $sql.="
                        AND `school`.`school_name` REGEXP '{$school_filter}'
                    ";
                }
                $arrys_school=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
                if(!empty($arrys_school)){

                    foreach($arrys_school as $inx=>$arry_school){

                        extract($arry_school, EXTR_PREFIX_ALL, "rs");

                        $rs_school_code     =trim($rs_school_code);
                        $rs_school_name     =trim($rs_school_name);
                        $rs_school_category =(int)$rs_school_category;
                        $rs_region_name     =trim($rs_region_name);
                        $rs_country_code    =trim($rs_country_code);

                        $arrys_school_info[$inx][trim('school_code    ')]=$rs_school_code;
                        $arrys_school_info[$inx][trim('school_name    ')]=$rs_school_name;
                        $arrys_school_info[$inx][trim('school_category')]=$rs_school_category;
                        $arrys_school_info[$inx][trim('region_name    ')]=$rs_region_name;
                        $arrys_school_info[$inx][trim('country_code   ')]=$rs_country_code;

                        $arrys_region_school_info[$rs_region_name][$inx][trim('school_code    ')]=$rs_school_code;
                        $arrys_region_school_info[$rs_region_name][$inx][trim('school_name    ')]=$rs_school_name;
                        $arrys_region_school_info[$rs_region_name][$inx][trim('school_category')]=$rs_school_category;
                        $arrys_region_school_info[$rs_region_name][$inx][trim('region_name    ')]=$rs_region_name;
                        $arrys_region_school_info[$rs_region_name][$inx][trim('country_code   ')]=$rs_country_code;
                    }
                }
            }

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
        </tr>
    </table>

    <form id='Form1' name='Form1' method='post' onsubmit="return false;">

        <!-- 報表資訊 開始 -->
        <table align="left" border="0" width="760px" cellpadding="0" cellspacing="0" style="position:relative;top:10px;" class="table_style2">
            <tr align="center" valign="middle" class="bg_gray1">
                <td>&nbsp;</td>
            </tr>
            <tr align="center">
                <td align="center" valign="middle" class="font-family1 fsize_16">
                    <span class='fc_red0'>請輸入下列各項資訊</span>

                    <input id="BtnA" type="button" value="發送" class="ibtn_gr6030" style="margin:5px 10px;"
                    onmouseover="this.style.cursor='pointer'"
                    onclick="BtnS();void(0);">

                    <input id="BtnR" type="button" value="再次發送" class="ibtn_gr9030" style="margin:5px 10px;display:none;"
                    onmouseover="this.style.cursor='pointer'"
                    onclick="location.reload();void(0);">
                </td>
            </tr>
        </table>
        <!-- 報表資訊 結束 -->

        <!-- 郵件資訊 開始 -->
        <table height='390px' align="left" border="0" width="760px" cellpadding="0" cellspacing="0" style="position:relative;top:15px;" class="table_style2">
            <tr align="center">
                <td align="center" valign="top" class="font-family1 fsize_14">
                    <table id='tbl1' align='center' cellpadding="0" cellspacing="0" border="0" width="755px" class="font-family1 fsize_14"
                    style='position:relative;top:10px;line-height:30px;'/>
                        <tr>
                            <td align='right' width='175px'>
                                <span class="fc_red0">*</span>
                                寄件者姓名：
                            </td>
                            <td align='left'>
                                <input type="text" id="send_from_name" name="send_from_name" value="" size="16" maxlength="16"
                                tabindex="1" class="form_text" style="width:175px">
                            </td>
                        </tr>
                        <tr>
                            <td align='right' width='175px'>
                                <span class="fc_red0">*</span>
                                寄件者信箱：
                            </td>
                            <td align='left'>
                                <input type="text" id="send_from_email" name="send_from_email" value="" size="16" maxlength="255"
                                tabindex="2" class="form_text" style="width:300px">
                            </td>
                        </tr>
                        <tr>
                            <td align='right' width='175px'>
                                <span class="fc_red0">*</span>
                                郵件標題：
                            </td>
                            <td align='left'>
                                <input type="text" id="email_title" name="email_title" value="" size="50" maxlength="50"
                                tabindex="3" class="form_text" style="width:450px">
                            </td>
                        </tr>
                        <tr>
                            <td align='right' width='175px'>
                                <span class="fc_red0">*</span>
                                郵件內容：
                            </td>
                            <td align='left'>
                                <textarea id="email_content" name="email_content" cols="80" rows="5" tabindex="4"
                                wrap="hard" class="form_textarea" style="width:500px;position:relative;top:10px;"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td align='right' width='175px'>
                                <span class="fc_red0" style='position:relative;top:12px;'>*</span>
                                <span style='position:relative;top:12px;'>選擇發送的學校：</span>
                            </td>
                            <td align='left' width=''>
                                <!-- 學校查詢 開始 -->
                                <span style="position:relative;top:15px;float:left;width:100%;">
                                    <!-- 推廣人員專用 -->
                                    <?php if((int)$sess_login_info['responsibilities']===99):?>
                                        <span>
                                            名稱篩選

                                            <input type="text" id="school_name" value="<?php if($school_filter!=='')echo $school_filter;?>" size="30" maxlength="30"
                                            tabindex="6" class="form_text" style="width:70px">

                                            <input type="button" value="篩選" class="ibtn_gr3020" style="margin:10px 0px;"
                                            tabindex="7" onmouseover="this.style.cursor='pointer'" onclick="unset_school(this);void(0);">

                                            <input type="button" value="全部" class="ibtn_gr3020" style="margin:10px 0px;"
                                            tabindex="8" onmouseover="this.style.cursor='pointer'" onclick="sel_all();void(0);">

                                            <input type="button" value="重置" class="ibtn_gr3020" style="margin:10px 0px;"
                                            tabindex="9" onmouseover="this.style.cursor='pointer'" onclick="reset_list();void(0);">
                                        </span>

                                        <select id="region_name" name="region_name" style="width:90px;" onchange="sel_region(this.options[this.options.selectedIndex]);void(0);">
                                            <option value="" selected>請選擇地區
                                            <?php foreach($arrys_region_school_info as $region_name=>$arry_region_school_info):?>
                                            <?php
                                                $region_name=trim($region_name);
                                            ?>
                                                <option value="<?php echo htmlspecialchars($region_name);?>">
                                                <?php echo htmlspecialchars($region_name);?>

                                            <?php endforeach;?>
                                        </select>

                                        <select id="school_code" name="school_code" style="width:90px;" onchange="sel_school(this.options[this.options.selectedIndex]);void(0);">
                                            <option value="" selected>請選擇學校
                                            <?php foreach($arrys_school_info as $inx=>$arry_school_info):?>
                                            <?php
                                                $rs_school_code =trim($arry_school_info['school_code']);
                                                $rs_school_name =trim($arry_school_info['school_name']);
                                                $rs_country_code=trim($arry_school_info['country_code']);
                                                $inx++;
                                            ?>
                                            <?php endforeach;?>
                                        </select>
                                    <?php endif?>
                                    <!-- <span class="fc_red0">*</span>
                                    <span class="fc_blue0 fsize_12">
                                        郵件將會發送至各校校長登錄之信箱，請先確認信箱資料是否正確。
                                    </span> -->
                                </span>
                                <!-- 學校查詢 結束 -->
                            </td>
                        </tr>
                        <tr>
                            <td align='right' width='175px'>
                                <span style='position:relative;top:12px;'>
                                    <span class="fc_red0">*</span>
                                    發送的學校清單：
                                </span>
                            </td>
                            <td align='left'>
                                <textarea id="school_list_ch" name="school_list_ch" cols="80" rows="6" tabindex="10"
                                wrap="hard" class="form_textarea" style="width:500px;position:relative;top:15px;" readonly></textarea>

                                <textarea id="school_list_en" name="school_list_en" cols="80" rows="6" tabindex="11"
                                wrap="hard" class="form_textarea" style="width:500px;position:relative;top:15px;display:none;" readonly></textarea>

                                <input type="text" id="cno" name="cno" value="0" size="16" maxlength="16"
                                class="form_text" style="width:175px;display:none;">

                                <input type="text" id="total" name="total" value="0" size="16" maxlength="16"
                                class="form_text" style="width:175px;display:none;">
                            </td>
                        </tr>
                    </table>

                    <!-- 失敗清單 -->
                    <table id='tbl2' align='center' cellpadding="0" cellspacing="0" border="0" width="755px" class="font-family1 fsize_14"
                    style='position:relative;top:25px;line-height:35px;display:none;'/>
                        <tr>
                            <td align='right' width='175px'>
                                發送失敗的學校清單：
                            </td>
                            <td>
                                <textarea id="send_fail" name="send_fail" cols="80" rows="15" tabindex="12"
                                wrap="hard" class="form_textarea" style="width:500px;position:relative;top:15px;" readonly></textarea>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!-- 郵件資訊 結束 -->

    </form>
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

    //參數
    var nl                      ='\r\n';
    var ospan_q_school          =parent.document.getElementById('span_q_school');
    var oschool_name            =document.getElementById('school_name');
    var school_list_ch          =trim('<?php echo trim($school_list_ch);?>');
    var school_list_en          =trim('<?php echo trim($school_list_en);?>');

    var osend_from_name         =document.getElementById('send_from_name');
    var osend_from_email        =document.getElementById('send_from_email');
    var oemail_title            =document.getElementById('email_title');
    var oemail_content          =document.getElementById('email_content');
    var oschool_list_ch         =document.getElementById('school_list_ch');
    var oschool_list_en         =document.getElementById('school_list_en');

    var ocno                    =document.getElementById('cno');
    var ototal                  =document.getElementById('total');

    //物件
    var oBtnA                   =document.getElementById('BtnA');
    var oBtnR                   =document.getElementById('BtnR');
    var oregion_name            =document.getElementById('region_name');
    var oschool_code            =document.getElementById('school_code');
    var otbl1                   =document.getElementById('tbl1');
    var otbl2                   =document.getElementById('tbl2');
    var osend_fail              =document.getElementById('send_fail');
    var jsons_school_info       =<?php echo json_encode($arrys_school_info,true);?>;
    var json_school_name_ch     =<?php echo json_encode($arry_school_name_ch,true);?>;
    var jsons_region_school_info=<?php echo json_encode($arrys_region_school_info,true);?>;

    //凾式
    function unset_school(obj){
    //篩選學校

        var school_name=trim(oschool_name.value);
        var url ='';
        var page=str_repeat('../',0)+'query.php';
        var arg ={
            'school_name'    :school_name,
            'school_list_ch' :trim(oschool_list_ch.value),
            'school_list_en' :trim(oschool_list_en.value)
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

    function sel_region(obj){
    //選擇地區

        var region_name=trim(obj.value);

        if((region_name!=="")&&(jsons_region_school_info[region_name])){

            //初始值回填
            $(oschool_code).empty().append('<option value="" selected>請選擇學校');
            var inx=1;

            for(key in jsons_region_school_info[region_name]){

                var school_code     =trim(jsons_region_school_info[region_name][key]['school_code']);
                var school_name     =trim(jsons_region_school_info[region_name][key]['school_name']);
                var school_category =parseInt(jsons_region_school_info[region_name][key]['school_category']);
                var region_name     =trim(jsons_region_school_info[region_name][key]['region_name']);
                var country_code    =trim(jsons_region_school_info[region_name][key]['country_code']);

                //附加
                $(oschool_code).append('<option value="'+school_code+'" country_code="'+country_code+'">'+inx+'.'+school_name+'');
                inx+=1;
            }
        }
    }

    function sel_school(obj){
    //選擇學校

        var school_code        =trim(obj.value);
        var school_list_ch     =trim(oschool_list_ch.value);
        var arry_school_list_ch=school_list_ch.split(",");

        if((school_code!=="")&&(json_school_name_ch[school_code])){

            var school_name=trim(json_school_name_ch[school_code]);

            for(var i=0;i<arry_school_list_ch.length;i++){
                if(school_name===trim(arry_school_list_ch[i])){
                    alert('已在清單中!');
                    return false;
                }
            }

            //附加
            oschool_list_ch.value+=school_name+",";
            oschool_list_en.value+=school_code+",";
        }else{
            return false;
        }
    }

    function reset_list(){
    //重置
        oschool_list_ch.value='';
        oschool_list_en.value='';
    }

    function sel_all(){
    //全部

        //重置
        reset_list();
        var region_name=trim(oregion_name.value);

        if((region_name!=="")&&(jsons_region_school_info[region_name])){
            for(key in jsons_region_school_info[region_name]){
                var school_code     =trim(jsons_region_school_info[region_name][key]['school_code']);
                var school_name     =trim(jsons_region_school_info[region_name][key]['school_name']);
                var school_category =parseInt(jsons_region_school_info[region_name][key]['school_category']);
                var region_name     =trim(jsons_region_school_info[region_name][key]['region_name']);
                var country_code    =trim(jsons_region_school_info[region_name][key]['country_code']);

                //附加
                oschool_list_ch.value+=school_name+",";
                oschool_list_en.value+=school_code+",";
            }
        }
    }

    function block_ui(cno,total){
        $.blockUI({
            message:'<h2 class="fc_white0">信件發送中，發送進度為'+cno+'/'+total+'，發送中請勿關閉頁面 !</h2>',
            css: {
                width:'450px',
                top:'200px',
                left: ($(window).width()-500)/2+'px',
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

    function ajax(cno,total,send_from_name,send_from_email,email_title,email_content,school_en){
    //ajax設置

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :0,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",

            url        :"addA.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                cno            :parseInt(cno        ),
                total          :parseInt(total      ),
                send_from_email:trim(send_from_email),
                send_from_name :trim(send_from_name ),
                email_title    :trim(email_title    ),
                email_content  :trim(email_content  ),
                school_en      :trim(school_en      )
            },
        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                ocno.value      =parseInt(ocno.value)+1;
                var respones    =jQuery.parseJSON(respones);
                var flag        =trim(respones['flag']);
                if(flag!=='true'){
                    var school_code  =trim(respones['school_code']);
                    osend_fail.value+=json_school_name_ch[school_code]+",";
                }
                var cno         =parseInt(ocno.value);
                var total       =parseInt(ototal.value);
                if(cno<=total){
                    block_ui(cno,total);
                }
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
            },
            complete    :function(){
            //傳送後處理
                var cno     =parseInt(ocno.value);
                var total   =parseInt(ototal.value);
                if(cno===total){
                    $.unblockUI();
                    alert('信件發送完畢 !');
                }
                $(otbl1).remove();
                if(osend_fail.value!==''){
                    $(otbl2).show();
                }
                $(oBtnA).hide();
                $(oBtnR).show();
            }
        });
    }

    function BtnS(){
    //發送

        var send_from_name      =trim(osend_from_name.value);
        var send_from_email     =trim(osend_from_email.value);
        var email_title         =trim(oemail_title.value);
        var email_content       =trim(oemail_content.value);
        var school_list_ch      =trim(oschool_list_ch.value);
        var school_list_en      =trim(oschool_list_en.value);
        var arry_err            =[];

        if(trim(send_from_name)===''){
            arry_err.push('請輸入, 寄件者姓名');
        }
        if(trim(send_from_email)===''){
            arry_err.push('請輸入, 寄件者信箱');
        }else{
            if(ch_email(send_from_email)===false){
                arry_err.push('寄件者信箱,格式不符');
            }
        }
        if(trim(email_title)===''){
            arry_err.push('請輸入, 郵件標題');
        }
        if(trim(email_content)===''){
            arry_err.push('請輸入, 郵件內容');
        }
        if((trim(school_list_ch)==='')||(trim(school_list_en)==='')){
            arry_err.push('請選擇, 發送的學校');
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要發送嗎?')){
                var arry_school_list_en=school_list_en.split(",");
                var school_list_cno    =parseInt(arry_school_list_en.length);
                ocno.value             =0;
                ototal.value           =parseInt(school_list_cno-1);
                block_ui(ocno.value,ototal.value);
                osend_fail.value       ='';

                for(var i=0;i<school_list_cno;i++){
                    var school_en   =trim(arry_school_list_en[i]);
                    if(school_en!==''){
                        var cno         =parseInt(i+1);
                        var total       =parseInt(school_list_cno-1);
                        ajax(cno,total,send_from_name,send_from_email,email_title,email_content,school_en);
                    }
                }
            }else{
                return false;
            }
        }
    }

    window.onload=function(){

        $(ospan_q_school).hide();

        //回填已選中之學校
        if(school_list_ch!==''){
            oschool_list_ch.value=school_list_ch;
        }
        if(school_list_en!==''){
            oschool_list_en.value=school_list_en;
        }

        //快速切換設置
        //fast_area_config('#fast_area',0,0);
    }

</script>
</Body>
</Html>
