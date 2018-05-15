<?php
//-------------------------------------------------------
//學校人員專區
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",0).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

//$_SESSION['uid']=2029;      //lala

//tw
//$_SESSION['uid']=4933;      //校長
//$_SESSION['uid']=28971;      //主任
//$_SESSION['uid']=686;       //老師

//hk
//$_SESSION['uid']=29075;      //校長
//$_SESSION['uid']=29084;      //主任
//$_SESSION['uid']=3173;       //老師無斑
//$_SESSION['uid']=22645; //老師有斑

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        //初始化，承接變數
        $arrys_sess_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
        if(empty($arrys_sess_login_info)){
            $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
            $jscript_back="
                <script>
                    alert('{$msg}');
                    history.back(-1);
                </script>
            ";
            die($jscript_back);
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

        //SESSION
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

        //學校篩選
        if((isset($_SESSION[trim('sha')][trim('school_filter')]))&&(trim($_SESSION[trim('sha')][trim('school_filter')])!=='')){
            $school_filter=$_SESSION[trim('sha')][trim('school_filter')];
        }else{
            $school_filter='';
        }

        $arry_uid_chk=array(
            2029,
            2,
            374,
            5165,
            4756,
            1260,
            4889,
            12089,
            5029,
            29802,
            6,
            30242,
            30243,
            30244,
            30245,
            30246,
            30247,
            30248
        );
        if(!in_array($sess_user_id,$arry_uid_chk)){
            $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
            $jscript_back="
                <script>
                    alert('{$msg}');
                    history.back(-1);
                </script>
            ";
            die($jscript_back);
        }

    //---------------------------------------------------
    //清除查詢紀錄
    //---------------------------------------------------

        unset($_SESSION['sha']['query']);

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------

        $sess_user_lv=(int)$sess_user_lv;

        //初始化, 學校陣列資訊
        $arrys_school_info=array();

        //初始化, 學年陣列資訊
        $arry_semester_year_info=array();
        $json_semester_year_info="";

        //初始化, 學期陣列資訊
        $arrys_semester_term_info=array();
        $jsons_semester_term_info="";

        //初始化, 年級陣列資訊
        $arrys_grade_info=array();
        $jsons_grade_info="";

        //初始化, 班級陣列資訊
        $arrys_classroom_info=array();
        $jsons_classroom_info="";

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

        //老師一定要帶班
        if(in_array($sess_user_lv,array(3))){
            if(!isset($sess_arry_class_info)){
                $msg="您沒有任何班級，無法使用本系統!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        parent.history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }
        }

    //---------------------------------------------------
    //串接SQL
    //---------------------------------------------------

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //-------------------------------------------
            //學校查詢 - 推廣人員專用
            //-------------------------------------------

                //推廣人員專用
                if(in_array($sess_user_lv,array(99))){

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
                        }
                    }
                }

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,學校人員專區";
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
    <script type="text/javascript" src="lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="lib/js/public/code.js"></script>
    <script type="text/javascript" src="lib/js/string/code.js"></script>
    <script type="text/javascript" src="lib/js/array/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="inc/code.css" media="all" />
    <script type="text/javascript" src="inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="css/def.css" media="all" />
</Head>

<Body>

<!-- 容器區塊 開始 -->
<div id="container">

    <!-- 內容區塊 開始 -->
    <div id="content">

        <table align="center" cellpadding="0" cellspacing="0" border="0" width="99%"/>
            <tr>
                <td align="left">
                    <table align="left" cellpadding="10" cellspacing="0" border="0" width="100%"
                    style="border-top:0px dashed #868686;border-bottom:0px dashed #868686;"/>
                        <tr>
                            <td align="left" width="60px" valign="middle">
                                <!-- 推廣人員專用 -->
                                <?php if(in_array($sess_user_lv,array(99))):?>
                                    <div id="sys_list"></div>
                                <?php else:?>
                                    <div id="sys_list" style="display:none;"></div>
                                <?php endif;?>
                            </td>
                            <td valign="middle">
                                <!-- 查詢表單列 開始 -->

                                <div style="position:relative;float:left;width:100%;">
                                    <!-- 推廣人員專用 -->
                                    <?php if(in_array($sess_user_lv,array(99))):?>
                                        <span>
                                            學校名稱

                                            <input type="text" id="school_name" value="<?php if($school_filter!=='')echo $school_filter;?>" size="30" maxlength="30"
                                            tabindex="1" class="form_text" style="width:100px">

                                            <input type="button" value="篩選" class="ibtn_gr3020" style="margin:10px 0px;"
                                            tabindex="2" onmouseover="this.style.cursor='pointer'" onclick="unset_school(this);void(0);">
                                        </span>
                                        <select id="school_code" name="school_code" style="width:150px;" onchange="sel_school(this.options[this.options.selectedIndex]);void(0);">
                                            <?php //if(!isset($sess_school_code)):?>
                                                <option value="" selected>請選擇學校
                                            <?php //endif?>

                                            <?php foreach($arrys_school_info as $inx=>$arry_school_info):?>
                                            <?php
                                                $rs_school_code =trim($arry_school_info['school_code']);
                                                $rs_school_name =trim($arry_school_info['school_name']);
                                                $rs_country_code=trim($arry_school_info['country_code']);
                                                $inx++;
                                            ?>

                                                <?php if((isset($sess_school_code))&&(trim($sess_school_code)!=='')&&($sess_school_code===$rs_school_code)):?>
                                                    <option value="<?php echo htmlspecialchars($rs_school_code);?>" country_code="<?php echo addslashes($rs_country_code);?>" selected>
                                                    <?php echo $inx.".".htmlspecialchars($rs_school_name);?>
                                                <?php else:?>
                                                    <option value="<?php echo htmlspecialchars($rs_school_code);?>" country_code="<?php echo addslashes($rs_country_code);?>">
                                                    <?php echo $inx.".".htmlspecialchars($rs_school_name);?>
                                                <?php endif;?>

                                            <?php endforeach;?>
                                        </select>
                                    <?php endif?>

                                </div>

                                <!-- 查詢表單列 結束 -->
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align="left">
                    <!-- 內容 開始 -->

                    <?php if((isset($sess_school_code))&&(trim($sess_school_code)!=='')):?>

                        <?php if(in_array($sess_user_lv,array(99))):?>
                            <table width="140px" cellpadding="0" cellspacing="0" border="1" align="left" onclick="location.href='mod/m_school_permit/index.php';void(0);"
                            style="position:relative;left:0px;float:left;margin:0 10px;top:20px;background-color:#c6ecff;border-color:#c6ecff;"
                            onmouseover="this.style.cursor='pointer'"/>
                                <tr align="center">
                                    <td height="120px">
                                        <span class='fc_gray0'>學校權限設定</span>
                                    </td>
                                </tr>
                            </table>
                        <?php endif;?>

                        <table width="140px" cellpadding="0" cellspacing="0" border="1" align="left" onclick="location.href='mod/m_semester_class/index.php';void(0);"
                        style="position:relative;left:0px;float:left;margin:0 10px;top:20px;background-color:#c6ecff;border-color:#c6ecff;"
                        onmouseover="this.style.cursor='pointer'"/>
                            <tr align="center">
                                <td height="120px">
                                    <span class='fc_gray0'>學期班級設定</span>
                                </td>
                            </tr>
                        </table>

                        <table width="140px" cellpadding="0" cellspacing="0" border="1" align="left" onclick="location.href='mod/m_user/index.php';void(0);"
                        style="position:relative;left:0px;float:left;margin:0 10px;top:20px;background-color:#c6ecff;border-color:#c6ecff;"
                        onmouseover="this.style.cursor='pointer'"/>
                            <tr align="center">
                                <td height="120px">
                                    <span class='fc_gray0'>老師學生帳戶設定</span>
                                </td>
                            </tr>
                        </table>

                        <?php if(in_array($sess_user_lv,array(1,2,99))):?>
                            <table width="140px" cellpadding="0" cellspacing="0" border="1" align="left" onclick="location.href='mod/m_status_group/index.php';void(0);"
                            style="position:relative;left:0px;float:left;margin:0 10px;top:20px;background-color:#c6ecff;border-color:#c6ecff;"
                            onmouseover="this.style.cursor='pointer'"/>
                                <tr align="center">
                                    <td height="120px">
                                        <span class='fc_gray0'>權限組合設定</span>
                                    </td>
                                </tr>
                            </table>
                        <?php endif;?>

                        <?php if(in_array($sess_user_lv,array(99))):?>
                            <table width="140px" cellpadding="0" cellspacing="0" border="1" align="left" onclick="location.href='mod/m_report_school/index.php';void(0);"
                            style="position:relative;left:0px;float:left;margin:0 10px;top:20px;background-color:#c6ecff;border-color:#c6ecff;"
                            onmouseover="this.style.cursor='pointer'"/>
                                <tr align="center">
                                    <td height="120px">
                                        <span class='fc_gray0'>學校報表</span>
                                    </td>
                                </tr>
                            </table>
                        <?php endif;?>

                    <?php else:?>

                        <table width="140px" cellpadding="0" cellspacing="0" border="1" align="left" onclick="alert('請先選擇學校!');void(0);"
                        style="position:relative;left:0px;float:left;margin:0 10px;top:20px;background-color:#c6ecff;border-color:#c6ecff;"
                        onmouseover="this.style.cursor='pointer'"/>
                            <tr align="center">
                                <td height="120px">
                                    <span class='fc_gray0'>請先選擇學校!</span>
                                </td>
                            </tr>
                        </table>

                    <?php endif;?>

                    <!-- 內容 結束 -->
                </td>
            </tr>
        </table>

    </div>
    <!-- 內容區塊 結束 -->

    <!-- google分析 開始 -->
    <?php echo google_analysis($allow=false);?>
    <!-- google分析 結束 -->

</div>
<!-- 容器區塊 結束 -->

</Body>

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //參數
    var oschool_name=document.getElementById('school_name');


    //凾式
    function block_ui(){
        $.blockUI({
            message:'<h2 class="fc_white0">處理中，請稍後 !</h2>',
            css: {
                top:'300px',
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

    function unset_school(obj){
    //篩選學校

        var school_name=trim(oschool_name.value);
        var url ='';
        var page=str_repeat('../',0)+'query.php';
        var arg ={
            'school_name'  :school_name
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

        //呼叫遮罩
        block_ui();

        go(url,'self');
    }

    function sel_school(obj){
    //選擇學校

        var school_code=trim(obj.value);

        if(school_code!==""){

            var country_code=trim(obj.getAttribute('country_code'));

            var url ='';
            var page=str_repeat('../',0)+'query.php';
            var arg ={
                'school_code'  :school_code,
                'country_code' :country_code
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

            //呼叫遮罩
            block_ui();

            go(url,'self');

        }else{
            return false;
        }
    }

</script>
<Html>