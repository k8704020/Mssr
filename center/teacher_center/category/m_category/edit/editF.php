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
        require_once(str_repeat("../",5).'config/config.php');

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
            $url=str_repeat("../",6).'index.php';
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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_category');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //cat_code        類別代號

        $get_chk=array(
            'cat_code'
        );
        $get_chk=array_map("trim",$get_chk);
        foreach($get_chk as $get){
            if(!isset($_GET[$get])){
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //cat_code        類別代號

        //GET
        $cat_code=trim($_GET[trim('cat_code')]);

        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

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
    //檢驗參數
    //---------------------------------------------------
    //cat_code        類別代號

        $arry_err=array();

        if($cat_code===''){
           $arry_err[]='類別代號,未輸入!';
        }

        if(count($arry_err)!==0){
            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
            die();
        }

    //---------------------------------------------------
    //串接SQL
    //---------------------------------------------------

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //---------------------------------------------------
        //SQL處理
        //---------------------------------------------------

            $cat_lv_flag=0;
            $arry_cat_lv_flag=array();

            //初始化, 階層陣列
            $arrys_lv_info=array();

            $cat_code=mysql_prep($cat_code);
            $sess_school_code=mysql_prep($sess_school_code);

            //-----------------------------------------------
            //各階層SQL處理
            //-----------------------------------------------

                $query_sql="
                    SELECT *
                    FROM `mssr_book_category`
                    WHERE 1=1
                        AND `mssr_book_category`.`cat1_id`<>1
                        AND `mssr_book_category`.`school_code`='{$sess_school_code}'
                        AND `mssr_book_category`.`cat_state`='啟用'
                        AND `mssr_book_category`.`cat_code`<>'{$cat_code}'
                ";

                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);

                if(!empty($arrys_result)){
                //回填各階層相關陣列
                    foreach($arrys_result as $inx=>$arry_result){
                        $cat1_id=(int)$arry_result['cat1_id'];
                        $cat2_id=(int)$arry_result['cat2_id'];
                        $cat3_id=(int)$arry_result['cat3_id'];

                        if(($cat2_id===1)&&($cat3_id===1)){
                            $arrys_lv_info['arrys_lv1'][]=$arry_result;
                        }else if(($cat2_id!==1)&&($cat3_id===1)){
                            $arrys_lv_info['arrys_lv2'][$cat1_id][]=$arry_result;
                        }else if(($cat2_id!==1)&&($cat3_id!==1)){
                            $arrys_lv_info['arrys_lv3'][$cat2_id][]=$arry_result;
                        }else{
                            die("發生嚴重錯誤，請洽詢明日星球團隊人員!");
                        }
                    }
                }

            //-----------------------------------------------
            //主SQL處理
            //-----------------------------------------------

                $query_sql="
                    SELECT *
                    FROM `mssr_book_category`
                    WHERE 1=1
                        AND `mssr_book_category`.`cat1_id`<>1
                        AND `mssr_book_category`.`cat2_id`<>1
                        AND `mssr_book_category`.`school_code`='{$sess_school_code}'
                        AND `mssr_book_category`.`cat_code`   ='{$cat_code}'
                        AND `mssr_book_category`.`cat_state`  ='啟用'
                ";

                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
                if(empty($arrys_result)){
                    $msg="該類別已經成為第一階層的類別，不允許修改!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }

            //-----------------------------------------------
            //主SQL對應各階層處理
            //-----------------------------------------------

                foreach($arrys_result as $inx=>$arry_result){

                    //初始化, 階層數
                    $cat_lv_tmp_flag=0;

                    //初始化, 已選定之路徑
                    $arrys_has_cat_lv_info=array();

                    $cat1_id=(int)$arry_result['cat1_id'];
                    $cat2_id=(int)$arry_result['cat2_id'];
                    $cat3_id=(int)$arry_result['cat3_id'];

                    if(($cat2_id===1)){
                    //判定為第一階層
                        break;
                    }

                //-----------------------------------------------
                //第一階層對應
                //-----------------------------------------------

                    if(($cat1_id!==1)){
                        $catlv1_sql="
                            SELECT *
                            FROM `mssr_book_category`
                            WHERE 1=1
                                AND `mssr_book_category`.`cat1_id`<>1
                                AND `mssr_book_category`.`school_code`='{$sess_school_code}'
                                AND `mssr_book_category`.`cat_state`  ='啟用'
                                AND `mssr_book_category`.`cat1_id`={$cat1_id}
                                AND `mssr_book_category`.`cat2_id`=1
                                AND `mssr_book_category`.`cat3_id`=1
                        ";
                        $catlv1_sql_result=db_result($conn_type='pdo',$conn_mssr,$catlv1_sql,array(0,1),$arry_conn_mssr);
                        if(!empty($catlv1_sql_result)){
                            $cat_lv_tmp_flag++;
                            $rs_cat_name=trim($catlv1_sql_result[0]['cat_name']);
                            $rs_cat_code=trim($catlv1_sql_result[0]['cat_code']);
                            $arrys_result['cat_lv_info'][$inx]['cat_lv_flag']=$cat_lv_tmp_flag;
                            $arrys_result['cat_lv_info'][$inx]['cat1_name']=$rs_cat_name;
                            $arrys_result['cat_lv_info'][$inx]['cat1_code']=$rs_cat_code;
                        }
                    }

                //-----------------------------------------------
                //第二階層對應
                //-----------------------------------------------

                    if(($cat1_id!==1)&&($cat2_id!==1)&&($cat3_id!==1)){
                        $catlv2_sql="
                            SELECT *
                            FROM `mssr_book_category`
                            WHERE 1=1
                                AND `mssr_book_category`.`cat1_id`<>1
                                AND `mssr_book_category`.`school_code`='{$sess_school_code}'
                                AND `mssr_book_category`.`cat_state`  ='啟用'
                                AND `mssr_book_category`.`cat1_id`={$cat1_id}
                                AND `mssr_book_category`.`cat2_id`={$cat2_id}
                                AND `mssr_book_category`.`cat3_id`=1
                        ";
                        $catlv2_sql_result=db_result($conn_type='pdo',$conn_mssr,$catlv2_sql,array(0,1),$arry_conn_mssr);
                        if(!empty($catlv2_sql_result)){
                            $cat_lv_tmp_flag++;
                            $rs_cat_name=trim($catlv2_sql_result[0]['cat_name']);
                            $rs_cat_code=trim($catlv2_sql_result[0]['cat_code']);
                            $arrys_result['cat_lv_info'][$inx]['cat_lv_flag']=$cat_lv_tmp_flag;
                            $arrys_result['cat_lv_info'][$inx]['cat2_name']=$rs_cat_name;
                            $arrys_result['cat_lv_info'][$inx]['cat2_code']=$rs_cat_code;
                        }
                    }
                }

        //---------------------------------------------------
        //類別max階層搜索
        //---------------------------------------------------

            if((isset($arrys_result['cat_lv_info']))&&(!empty($arrys_result['cat_lv_info']))){
                foreach($arrys_result['cat_lv_info'] as $inx=>$arry_result){
                    $rs_cat_lv_flag=(int)$arry_result['cat_lv_flag'];
                    $arry_cat_lv_flag[]=$rs_cat_lv_flag;
                }
                //排序
                arsort($arry_cat_lv_flag);
                $arry_cat_lv_flag=array_values($arry_cat_lv_flag);
                $cat_lv_flag=$arry_cat_lv_flag[0];
            }else{
                $cat_lv_flag=0;
            }

        //---------------------------------------------------
        //回填, 已選定之路徑
        //---------------------------------------------------

            if((isset($arrys_result['cat_lv_info']))&&(!empty($arrys_result['cat_lv_info']))){
                $arrys_has_cat_lv_info=$arrys_result['cat_lv_info'];
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
            $sys_ename=$FOLDER[count($FOLDER)-3];
            $mod_ename=$FOLDER[count($FOLDER)-2];
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
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/string/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../css/def.css" media="all" />

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
                                        <img width="12" height="12" src="../../../../../img/icon/blue.jpg" border="0">
                                        <a href="../../../index.php">教師中心</a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="../<?php echo htmlspecialchars($sys_url);?>">
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
<?php //echo fast_area($rd=3);?>
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
        global $arry_conn;

        //local
        global $psize;
        global $pinx;
        global $cat_code;
        global $arrys_result;
        global $cat_lv_flag;
        global $arrys_lv_info;
        global $arrys_has_cat_lv_info;

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        global $arrys_login_info;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0;  //欄位個數
        $btn_nos=0;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        $json_lv_info=json_encode($arrys_lv_info,true);
        $json_has_cat_lv_info=json_encode($arrys_has_cat_lv_info,true);
?>
<!-- 內容 開始 -->
<form id='Form1' name='Form1' method='post' onsubmit="return false;">
    <table align="center" border="0" width="525px" class="table_style0" style="position:relative;top:30px;">
        <tr>
            <td height="30px" class="fc_blue0" colspan="2">
                設定類別對應
            </td>
        </tr>
        <?php foreach($arrys_result as $inx=>$arry_result):?>
        <?php
        //-----------------------------------------------
        //接收欄位
        //-----------------------------------------------

            if(trim($inx)==='cat_lv_info'){
                break;
            }

            extract($arry_result, EXTR_PREFIX_ALL, "rs");

        //-----------------------------------------------
        //處理欄位
        //-----------------------------------------------

            //cat_id            類別主索引
            $rs_cat_id=(int)$rs_cat_id;

            //cat1_id           類別1主索引
            $rs_cat1_id=(int)$rs_cat1_id;

            //cat_code          類別代號
            $rs_cat_code=trim($rs_cat_code);

            //cat_name          類別名稱
            $rs_cat_name=trim($rs_cat_name);

        //-----------------------------------------------
        //特殊處理
        //-----------------------------------------------
        ?>
            <?php if((isset($arrys_result['cat_lv_info']))&&(!empty($arrys_result['cat_lv_info']))):?>
                <tr class="fc_gray0">
                    <td align="right" width="100px" height="<?php if((int)$inx===0){echo '45px';}else{echo '45px';}?>" class="<?php if((int)$inx===0){echo 'b_line';}?>">
                        第<?php echo $inx+1;?>種路徑：
                    </td>
                    <td class="<?php if((int)$inx===0){echo 'b_line';}?>">
                        <?php if(isset($arrys_result['cat_lv_info'][$inx]['cat1_name']))echo htmlspecialchars(trim($arrys_result['cat_lv_info'][$inx]['cat1_name']))." &gt;&gt;";?>
                        <?php if(isset($arrys_result['cat_lv_info'][$inx]['cat2_name']))echo htmlspecialchars(trim($arrys_result['cat_lv_info'][$inx]['cat2_name']))." &gt;&gt;";?>
                        <span class="fc_red1">*</span><?php echo htmlspecialchars($rs_cat_name);?>
                    </td>
                </tr>
            <?php else:?>
                <tr class="fc_gray0">
                    <td align="right" width="100px" height="<?php if((int)$inx===0){echo '45px';}else{echo '45px';}?>" class="<?php if((int)$inx===0){echo 'b_line';}?>">
                        第<?php echo $inx+1;?>種路徑：
                    </td>
                    <td class="<?php if((int)$inx===0){echo 'b_line';}?>">
                        <span class="fc_red1">*</span><?php echo htmlspecialchars($rs_cat_name);?>
                    </td>
                </tr>
            <?php endif;?>
        <?php endforeach;?>
        <tr class="fc_gray0">
            <td align="right" width="100px" height="80px" class="gr_dashed">
                <span class="fc_red1">*</span>
                新增路徑：
            </td>
            <td class="gr_dashed">
                <span class="fc_red1">第一階：</span>
                <select id="cat1_id" name="cat1_id" class="form_select" tabindex="1"
                onchange="lv1_set(this.options[this.selectedIndex].value);">
                    <option value="1" att='請選擇'>請選擇
                    <?php foreach($arrys_lv_info['arrys_lv1'] as $inx=>$arry_result):?>
                    <?php
                    //-----------------------------------------------
                    //接收欄位
                    //-----------------------------------------------

                        extract($arry_result, EXTR_PREFIX_ALL, "rs");

                    //-----------------------------------------------
                    //處理欄位
                    //-----------------------------------------------

                        //cat_id            類別主索引
                        $rs_cat_id=(int)$rs_cat_id;

                        //cat1_id           類別1主索引
                        $rs_cat1_id=(int)$rs_cat1_id;

                        //cat_code          類別代號
                        $rs_cat_code=trim($rs_cat_code);

                        //cat_name          類別名稱
                        $rs_cat_name=trim($rs_cat_name);

                    //-----------------------------------------------
                    //特殊處理
                    //-----------------------------------------------
                    ?>
                        <option value="<?php echo $rs_cat1_id;?>" att='<?php echo htmlspecialchars($rs_cat_name);?>'><?php echo htmlspecialchars($rs_cat_name);?>
                    <?php endforeach;?>
                </select>

                <?php //if(in_array($cat_lv_flag,array(2))):?>
                <span class="fc_red1">第二階：</span>
                <select id="cat2_id" name="cat2_id" class="form_select" tabindex="2">
                    <option value="1" att='請選擇'>請選擇
                </select>
                <?php //endif;?>

                <?php if(in_array($cat_lv_flag,array(2))):?>
                <!-- <span class="fc_red1">第三階：</span>
                <select id="cat3_id" name="cat3_id" class="form_select" tabindex="3">
                    <option value="1" att='請選擇'>請選擇
                </select> -->
                <?php endif;?>
            </td>
        </tr>

        <tr>
            <td align="right" colspan="2">
                <input type="hidden" id="psize" name="psize" value="<?php echo addslashes($psize);?>">
                <input type="hidden" id="pinx" name="pinx" value="<?php echo addslashes($pinx);?>">
                <input type="hidden" id="cat_code" name="cat_code" value="<?php echo addslashes(trim($cat_code));?>">

                <input type="button" id="BtnS" name="BtnS" value="送出" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="4" onmouseover="this.style.cursor='pointer'">
                <input type="button" id="BtnB" name="BtnB" value="返回" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="5" onmouseover="this.style.cursor='pointer'">
            </td>
        </tr>
    </table>
</form>
<!-- 內容 結束 -->

<?php
//-------------------------------------------------------
//資料繫結
//-------------------------------------------------------
//echo "<Pre>";
//print_r($arrys_has_cat_lv_info);
//echo "</Pre>";
?>
<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //變數
    var nl    ='\r\n';
    var pinx  =<?php echo addslashes($pinx);?>;     //單頁筆數
    var psize =<?php echo addslashes($psize);?>;    //目前分頁索引
    var cat_lv_flag=parseInt('<?php echo ($cat_lv_flag);?>');
    var json_lv_info=<?php echo ($json_lv_info);?>;
    var json_has_cat_lv_info=<?php echo ($json_has_cat_lv_info);?>;

    var oForm1=document.getElementById('Form1');    //表單
    var oBtnS =document.getElementById('BtnS');     //送出
    var oBtnB =document.getElementById('BtnB');     //返回

    var ocat1_id=document.getElementById('cat1_id');
    var ocat2_id=document.getElementById('cat2_id');
    var ocat3_id=document.getElementById('cat3_id');

    oBtnS.onclick=function(){
    //送出

        var cat1_name=trim(ocat1_id.options[ocat1_id.selectedIndex].getAttribute('att'));
        var cat2_name=trim(ocat2_id.options[ocat2_id.selectedIndex].getAttribute('att'));
        var cat1_id  =parseInt(ocat1_id.value);
        var cat2_id  =parseInt(ocat2_id.value);
        var arry_err =[];

        if(cat1_name==='請選擇'){
            alert('請選擇路徑!');
            return false;
        }else{
            for(key in json_has_cat_lv_info){

                var cat_lv_flag =parseInt(json_has_cat_lv_info[key]['cat_lv_flag']);
                var js_cat1_name=json_has_cat_lv_info[key]['cat1_name'];
                var js_cat2_name=json_has_cat_lv_info[key]['cat2_name'];
                var path_flag   =true;

                switch(cat_lv_flag){

                    case 1:
                        if(cat1_name===js_cat1_name)path_flag=false;
                        if(cat2_id!==1)path_flag=true;
                        if(!path_flag){
                            alert('已有相同的路徑，請重新選擇!');
                            return false;
                            break;
                        }
                    break;

                    case 2:
                        if((cat1_name===js_cat1_name)&&(cat2_id===1))path_flag=false;
                        if((cat1_name===js_cat1_name)&&(cat2_name===js_cat2_name))path_flag=false;
                        if(!path_flag){
                            alert('已有相同的路徑，請重新選擇!');
                            return false;
                            break;
                        }
                    break;

                    default:
                        return false;
                    break;
                }
            }
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if((cat1_id===1)&&(cat2_id===1)){
                if(confirm('你新增的路徑將使此類別成為第一類別，你確定要新增嗎?')){
                    oForm1.action='editA.php'
                    oForm1.submit();
                    return true;
                }else{
                    return false;
                }
            }else if((cat1_id!==1)&&(cat2_id===1)){
                if(confirm('你新增的路徑將使此類別成為第二類別，你確定要新增嗎?')){
                    oForm1.action='editA.php'
                    oForm1.submit();
                    return true;
                }else{
                    return false;
                }
            }else if((cat1_id!==1)&&(cat2_id!==1)){
                if(confirm('你新增的路徑將使此類別成為第三類別，你確定要新增嗎?')){
                    oForm1.action='editA.php'
                    oForm1.submit();
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    }

    oBtnB.onclick=function(){
    //返回

        var url ='';
        var page=str_repeat('../',1)+'index.php';
        var arg ={
            //'psize':psize,
            //'pinx' :pinx
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

    function lv1_set(cat1_id){
    //設置第一階類別

        var cat1_id=cat1_id;

        //移除第二階舊有資訊, 附加第二階基本資訊
        $(ocat2_id).empty().append('<option value="1" att="請選擇">請選擇');

        //移除第三階舊有資訊, 附加第三階基本資訊
        //$(ocat3_id).empty().append('<option value="1" att="請選擇">請選擇');

        try{
            var json_lv2=json_lv_info['arrys_lv2'][cat1_id];
            for(key in json_lv2){
                var js_cat_name =trim(json_lv2[key]['cat_name']);
                var js_cat1_id  =parseInt(json_lv2[key]['cat1_id']);
                var js_cat2_id  =parseInt(json_lv2[key]['cat2_id']);
                var js_cat3_id  =parseInt(json_lv2[key]['cat3_id']);
                var js_cat_code =trim(json_lv2[key]['cat_code']);

                //設置附加元素
                var _html_tbl='';
                _html_tbl+='<option value="'+js_cat2_id+'" att="'+js_cat_name+'">'+js_cat_name;

                //附加
                $(ocat2_id).append(_html_tbl);
            }

            //附加切換函式
            //$(ocat2_id)[0].setAttribute('onchange','lv2_set(this.options[this.selectedIndex].value);');
        }catch(err){
            return false;
        }
    }

    function lv2_set(cat2_id){
    //設置第二階類別

        var cat1_id=parseInt(ocat1_id.value);
        var cat2_id=cat2_id;

        //移除第三階舊有資訊, 附加第三階基本資訊
        $(ocat3_id).empty().append('<option value="1" att="請選擇">請選擇');

        try{
            var json_lv3=json_lv_info['arrys_lv3'][cat2_id];
            for(key in json_lv3){
                var js_cat_name =trim(json_lv3[key]['cat_name']);
                var js_cat1_id  =parseInt(json_lv3[key]['cat1_id']);
                var js_cat2_id  =parseInt(json_lv3[key]['cat2_id']);
                var js_cat3_id  =parseInt(json_lv3[key]['cat3_id']);
                var js_cat_code =trim(json_lv3[key]['cat_code']);

                //設置附加元素
                var _html_tbl='';
                _html_tbl+='<option value="'+js_cat3_id+'" att="'+js_cat_name+'">'+js_cat_name;

                //附加
                if(cat1_id===js_cat1_id){
                    $(ocat3_id).append(_html_tbl);
                }
            }
        }catch(err){
            return false;
        }
    }

</script>

<?php
//-------------------------------------------------------
//page_ok 區塊 -- 結束
//-------------------------------------------------------
?>
<?php };?>