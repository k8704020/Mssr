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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_book');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //book_type       書籍類別
    //book_sid        書籍識別碼

        $get_chk=array(
            'book_type',
            'book_sid'
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
    //book_type       書籍類別
    //book_sid        書籍識別碼

        //GET
        $book_type=trim($_GET[trim('book_type')]);
        $book_sid=trim($_GET[trim('book_sid')]);

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
    //book_type       書籍類別
    //book_sid        書籍識別碼

        $arry_err=array();

        if($book_type===''){
           $arry_err[]='書籍類別,未輸入!';
        }else{
            $book_type=trim($book_type);
            if(!in_array($book_type,array("mssr_book_class","mssr_book_library"))){
                $arry_err[]='書籍類別,錯誤!';
            }
        }

        if($book_sid===''){
           $arry_err[]='書籍識別碼,未輸入!';
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

        //-----------------------------------------------
        //SQL處理
        //-----------------------------------------------

            $book_type       =mysql_prep($book_type);
            $book_sid        =mysql_prep($book_sid);
            $sess_school_code=mysql_prep($sess_school_code);

            //初始化, 階層陣列
            $arrys_lv_info   =array();

            //初始化, 書籍擁有的類別
            $arrys_has_cat_code=array();

            //暫存, 書籍擁有的類別
            $tmp_arrys_has_cat_code=array();

            //-------------------------------------------
            //檢核書籍資料
            //-------------------------------------------

                $query_sql="
                    SELECT
                        `book_sid`
                    FROM `{$book_type}`
                    WHERE 1=1
                        AND `book_sid`='{$book_sid}'
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(0,1),$arry_conn_mssr);
                $numrow=count($arrys_result);
                if($numrow===0){
                    $msg="書本不存在, 請重新輸入!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }

            //-------------------------------------------
            //檢核書籍類別資料
            //-------------------------------------------

                $query_sql="
                    SELECT
                        `cat_code`,
                        `cat_group`,
                        `keyin_cdate`
                    FROM `mssr_book_category_rev`
                    WHERE 1=1
                        AND `mssr_book_category_rev`.`school_code`='{$sess_school_code}'
                        AND `mssr_book_category_rev`.`book_sid`   ='{$book_sid        }'
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
//echo "<Pre>";
//print_r($arrys_result);
//echo "</Pre>";
                if(!empty($arrys_result)){
                    //回填暫存, 書籍擁有的類別以及相關資訊
                    foreach($arrys_result as $arry_result){
                        $rs_cat_code=trim($arry_result['cat_code']);
                        $rs_cat_group=(int)($arry_result['cat_group']);
                        $tmp_arrys_has_cat_code[$rs_cat_group][]=$rs_cat_code;
                    }
                }
//echo "<Pre>";
//print_r($tmp_arrys_has_cat_code);
//echo "</Pre>";
            //-------------------------------------------
            //檢核書籍類別對應各階層資料
            //-------------------------------------------

                if(!empty($tmp_arrys_has_cat_code)){

                    $cno=0;

                    foreach($tmp_arrys_has_cat_code as $cat_group=>$tmp_arry_has_cat_code){

                        $cat_group=(int)($cat_group);

                        foreach($tmp_arry_has_cat_code as $inx=>$cat_code){

                            //參數過濾
                            $cat_code=mysql_prep(trim($cat_code));

                            //---------------------------
                            //檢核第一階類別
                            //---------------------------

                                $query_sql="
                                    SELECT *
                                    FROM `mssr_book_category`
                                    WHERE 1=1
                                        AND `mssr_book_category`.`school_code`='{$sess_school_code}'
                                        AND `mssr_book_category`.`cat_code`='{$cat_code}'
                                        AND `mssr_book_category`.`cat1_id`<>1
                                        AND `mssr_book_category`.`cat2_id`=1
                                        AND `mssr_book_category`.`cat3_id`=1
                                ";
                                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
                                if(!empty($arrys_result)){

                                    foreach($arrys_result as $arry_result){

                                        $cat1_id=(int)$arry_result['cat1_id'];
                                        $cat1_name=trim($arry_result['cat_name']);
                                        $cat1_code=trim($arry_result['cat_code']);

                                        //回填第一階類別
                                        $arrys_has_cat_code[$cno]['cat_lv_cno'] =count($tmp_arry_has_cat_code);
                                        $arrys_has_cat_code[$cno]['cat_group']  =$cat_group;
                                        $arrys_has_cat_code[$cno]['cat1_id']    =$cat1_id;
                                        $arrys_has_cat_code[$cno]['cat1_name']  =$cat1_name;
                                        $arrys_has_cat_code[$cno]['cat1_code']  =$cat1_code;
                                    }
                                }

                            //---------------------------
                            //檢核第二階類別
                            //---------------------------

                                if(isset($cat1_id)){

                                    //參數過濾
                                    $cat_lv_cno=(int)count($tmp_arry_has_cat_code);

                                    if($cat_lv_cno>1){

                                        $cat1_id=(int)$cat1_id;

                                        $query_sql="
                                            SELECT *
                                            FROM `mssr_book_category`
                                            WHERE 1=1
                                                AND `mssr_book_category`.`school_code`='{$sess_school_code}'
                                                AND `mssr_book_category`.`cat_code`='{$cat_code}'
                                                AND `mssr_book_category`.`cat1_id`={$cat1_id}
                                                AND `mssr_book_category`.`cat2_id`<>1
                                                AND `mssr_book_category`.`cat3_id`=1
                                        ";
                                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
                                        if(!empty($arrys_result)){

                                            foreach($arrys_result as $inx=>$arry_result){

                                                if(isset($arry_result['cat1_id']))$cat1_id=(int)$arry_result['cat1_id'];
                                                if(isset($arry_result['cat2_id']))$cat2_id=(int)$arry_result['cat2_id'];
                                                if(isset($arry_result['cat_name']))$cat2_name=trim($arry_result['cat_name']);
                                                if(isset($arry_result['cat_code']))$cat2_code=trim($arry_result['cat_code']);

                                                foreach($arrys_has_cat_code as $inx=>$arry_has_cat_code){

                                                    if(($cat_group===(int)$arrys_has_cat_code[$inx]['cat_group'])&&($cat1_id===(int)$arrys_has_cat_code[$inx]['cat1_id'])){

                                                        //回填第二階類別
                                                        $arrys_has_cat_code[$inx]['cat2_id']  =$cat2_id;
                                                        $arrys_has_cat_code[$inx]['cat2_name']=$cat2_name;
                                                        $arrys_has_cat_code[$inx]['cat2_code']=$cat2_code;

                                                        continue;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                            //---------------------------
                            //檢核第三階類別
                            //---------------------------

                                if(isset($cat2_id)){

                                    //參數過濾
                                    $cat_lv_cno=(int)count($tmp_arry_has_cat_code);

                                    if($cat_lv_cno>2){

                                        $cat2_id=(int)$cat2_id;

                                        $query_sql="
                                            SELECT *
                                            FROM `mssr_book_category`
                                            WHERE 1=1
                                                AND `mssr_book_category`.`school_code`='{$sess_school_code}'
                                                AND `mssr_book_category`.`cat_code`='{$cat_code}'
                                                AND `mssr_book_category`.`cat1_id`={$cat1_id}
                                                AND `mssr_book_category`.`cat2_id`={$cat2_id}
                                                AND `mssr_book_category`.`cat3_id`<>1
                                        ";
                                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
                                        if(!empty($arrys_result)){

                                            foreach($arrys_result as $inx=>$arry_result){

                                                if(isset($arry_result['cat2_id']))$cat2_id=(int)$arry_result['cat2_id'];
                                                if(isset($arry_result['cat3_id']))$cat3_id=(int)$arry_result['cat3_id'];
                                                if(isset($arry_result['cat_name']))$cat3_name=trim($arry_result['cat_name']);
                                                if(isset($arry_result['cat_code']))$cat3_code=trim($arry_result['cat_code']);

                                                foreach($arrys_has_cat_code as $inx=>$arry_has_cat_code){

                                                    if(($cat_group===(int)$arrys_has_cat_code[$inx]['cat_group'])&&($cat2_id===(int)$arrys_has_cat_code[$inx]['cat2_id'])){

                                                        //回填第三階類別
                                                        $arrys_has_cat_code[$inx]['cat3_id']  =$cat3_id;
                                                        $arrys_has_cat_code[$inx]['cat3_name']=$cat3_name;
                                                        $arrys_has_cat_code[$inx]['cat3_code']=$cat3_code;

                                                        continue;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            $cno++;
                        }
                    }
                }

            //-----------------------------------------------
            //查找, 各階層類別
            //-----------------------------------------------

                $query_sql="
                    SELECT *
                    FROM `mssr_book_category`
                    WHERE 1=1
                        AND `mssr_book_category`.`cat1_id`<>1
                        AND `mssr_book_category`.`school_code`='{$sess_school_code}'
                        AND `mssr_book_category`.`cat_state`='啟用'
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
        global $book_type;
        global $book_sid;
        global $sess_school_code;
        global $arrys_has_cat_code;
        global $arrys_lv_info;

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

        if((isset($arrys_has_cat_code))&&(!empty($arrys_has_cat_code))){
            $json_has_cat_code=json_encode($arrys_has_cat_code,true);
        }else{
            $json_has_cat_code=json_encode(array(),true);
        }

        if((isset($arrys_lv_info))&&(!empty($arrys_lv_info))){
            $json_lv_info=json_encode($arrys_lv_info,true);
        }else{
            $json_lv_info=json_encode(array(),true);
        }
?>
<!-- 內容 開始 -->
<form id='Form1' name='Form1' method='post' onsubmit="return false;">
    <table align="center" border="0" width="525px" class="table_style0" style="position:relative;top:30px;">
        <tr>
            <td height="30px" class="fc_blue0" colspan="2">
                設定書籍類別
            </td>
        </tr>
        <?php if((isset($arrys_has_cat_code))&&(!empty($arrys_has_cat_code))):?>
        <?php
            $con_num=0;
        ?>
            <?php foreach($arrys_has_cat_code as $inx=>$arry_has_cat_code):?>
            <?php
            //-----------------------------------------------
            //接收欄位
            //-----------------------------------------------

                extract($arry_has_cat_code, EXTR_PREFIX_ALL, "rs");

            //-----------------------------------------------
            //處理欄位
            //-----------------------------------------------

                //cat1_name         類別1名稱
                //$rs_cat1_name_html='';
                if(isset($rs_cat1_name))$rs_cat1_name=trim($rs_cat1_name);

                //cat2_name         類別2名稱
                //$rs_cat2_name_html='';
                if(isset($rs_cat2_name))$rs_cat2_name=trim($rs_cat2_name);

                //cat3_name         類別3名稱
                //$rs_cat3_name_html='';
                if(isset($rs_cat3_name))$rs_cat3_name=trim($rs_cat3_name);

                //cat_group         類別組別
                if(isset($rs_cat_group))$rs_cat_group=(int)($rs_cat_group);

                $rs_cat_lv_cno=(int)$rs_cat_lv_cno;

            //-----------------------------------------------
            //特殊處理
            //-----------------------------------------------
            ?>
            <?php
                $con_num++;
            ?>
            <tr class="fc_gray0">
                <td align="right" width="100px" height="45px" class="<?php if((int)$con_num===1){echo 'b_line';}?>">
                    第<?php echo $con_num;?>種類別：
                </td>
                <td height="45px" class="<?php if((int)$con_num===1){echo 'b_line';}?>">
                    <?php if(isset($rs_cat1_name))echo htmlspecialchars($rs_cat1_name);?>

                    <?php if(isset($rs_cat2_name)&&($rs_cat_lv_cno>=2))echo " &gt;&gt; ".htmlspecialchars($rs_cat2_name);?>

                    <?php if(isset($rs_cat3_name)&&($rs_cat_lv_cno>=3))echo " &gt;&gt; ".htmlspecialchars($rs_cat3_name);?>

                    <span class="fc_red1" style="position:relative;" onmouseover="this.style.cursor='pointer'"
                    onclick="del('<?php echo addslashes($book_type);?>','<?php echo addslashes($book_sid);?>','<?php echo addslashes($sess_school_code);?>',<?php echo $rs_cat_group;?>);">
                        ... 按我刪除
                    </span>
                </td>
            </tr>
            <?php endforeach;?>
        <?php else:?>
            <tr class="fc_gray0">
                <td align="center" height="60px" class="b_line" colspan="2">
                    <span class="fc_red1">目前尚未設定類別 !</span>
                </td>
            </tr>
        <?php endif;?>
        <tr class="fc_gray0">
            <td align="right" width="100px" height="80px" class="gr_dashed">
                <span class="fc_red1">*</span>
                新增類別：
            </td>
            <td class="gr_dashed">
                <span class="fc_red1">第一階：</span>
                <select id="cat1_id" name="cat1_id" class="form_select" tabindex="1"
                onchange="lv1_set(this.options[this.selectedIndex].value);">
                    <option value="1" att='請選擇'>請選擇
                    <?php if(!empty($arrys_lv_info['arrys_lv1'])):?>
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
                        <option value="<?php echo $rs_cat1_id;?>"><?php echo htmlspecialchars($rs_cat_name);?>
                    <?php endforeach;?>
                    <?php endif;?>
                </select>

                <span class="fc_red1">第二階：</span>
                <select id="cat2_id" name="cat2_id" class="form_select" tabindex="2">
                    <option value="1" att='請選擇'>請選擇
                </select>

                <span class="fc_red1">第三階：</span>
                <select id="cat3_id" name="cat3_id" class="form_select" tabindex="3">
                    <option value="1" att='請選擇'>請選擇
                </select>
            </td>
        </tr>

        <tr>
            <td align="right" colspan="2">
                <input type="hidden" id="psize" name="psize" value="<?php echo addslashes($psize);?>">
                <input type="hidden" id="pinx" name="pinx" value="<?php echo addslashes($pinx);?>">
                <input type="hidden" id="book_type" name="book_type" value="<?php echo addslashes($book_type);?>">
                <input type="hidden" id="book_sid" name="book_sid" value="<?php echo addslashes($book_sid);?>">
                <input type="hidden" id="has_cat_code_lv" name="has_cat_code_lv" value="0">

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
echo "<Pre>";
print_r($arrys_has_cat_code);
echo "</Pre>";
?>
<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //變數
    var nl    ='\r\n';
    var pinx  =<?php echo addslashes($pinx);?>;     //單頁筆數
    var psize =<?php echo addslashes($psize);?>;    //目前分頁索引
    var json_lv_info=<?php echo ($json_lv_info);?>;
    var json_has_cat_code=<?php echo ($json_has_cat_code);?>;
    var has_cat_code_lv=0;

    var oForm1=document.getElementById('Form1');    //表單
    var oBtnS =document.getElementById('BtnS');     //送出
    var oBtnB =document.getElementById('BtnB');     //返回

    var ocat1_id=document.getElementById('cat1_id');
    var ocat2_id=document.getElementById('cat2_id');
    var ocat3_id=document.getElementById('cat3_id');
    var ohas_cat_code_lv=document.getElementById('has_cat_code_lv');

    oBtnS.onclick=function(){
    //送出

        var cat1_id  =parseInt(ocat1_id.value);
        var cat2_id  =parseInt(ocat2_id.value);
        var cat3_id  =parseInt(ocat3_id.value);
        var cat_lv   =0;

        var arry_err =[];

        if(cat1_id===1){
            alert('請選擇類別!');
            return false;
        }else{
            if((cat1_id!==1)&&(cat2_id===1)&&(cat3_id===1)){
                cat_lv=1;
            }else if((cat1_id!==1)&&(cat2_id!==1)&&(cat3_id===1)){
                cat_lv=2;
            }else if((cat1_id!==1)&&(cat2_id!==1)&&(cat3_id!==1)){
                cat_lv=3;
            }else{
                return false;
            }

            for(var key in json_has_cat_code){
                if((cat1_id===json_has_cat_code[key]['cat1_id'])){
                    has_cat_code_lv=1;
                    if(json_has_cat_code[key]['cat2_id']!==undefined){
                        has_cat_code_lv=2;
                        if(json_has_cat_code[key]['cat3_id']!==undefined){
                            has_cat_code_lv=3;
                            break;
                        }
                        break;
                    }
                    break;
                }else{
                    has_cat_code_lv=0;
                }
            }
            //回填
            ohas_cat_code_lv.value=has_cat_code_lv;

            for(var key in json_has_cat_code){

                if(json_has_cat_code[key]['cat1_id'])var js_cat1_id=parseInt(json_has_cat_code[key]['cat1_id']);
                if(json_has_cat_code[key]['cat2_id'])var js_cat2_id=parseInt(json_has_cat_code[key]['cat2_id']);
                if(json_has_cat_code[key]['cat3_id'])var js_cat3_id=parseInt(json_has_cat_code[key]['cat3_id']);

                switch(cat_lv){
                    case 1:
                        if((cat1_id===js_cat1_id)){
                            alert('已有相同的類別，請重新選擇!');
                            return false;
                            break;
                        }
                    break;

                    case 2:
                        if(cat1_id===js_cat1_id){
                            if(js_cat2_id!==undefined){
                                if(cat2_id===js_cat2_id){
                                    alert('已有相同的類別，請重新選擇!');
                                    return false;
                                    break;
                                }
                            }
                        }
                    break;

                    case 3:
                        if(cat1_id===js_cat1_id){
                        //alert(cat1_id);
                        //alert(js_cat1_id);
                            if(js_cat2_id!==undefined){
                                if(cat2_id===js_cat2_id){
                                    if(js_cat3_id!==undefined){
                                        if(cat3_id===js_cat3_id){
                                            //alert(cat1_id);
                                            //alert(cat2_id);
                                            //alert(cat3_id);
                                            alert('已有相同的類別，請重新選擇!');
                                            return false;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    break;

                    default:
                        return false;
                        break;
                    break;
                }
            }
            if(confirm('你確定要將書籍設定為此類別嗎?')){
                oForm1.action='add/addA.php'
                oForm1.submit();
                return true;
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

    function del(book_type,book_sid,school_code,cat_group){
    //刪除類別

        if(confirm('你確定要刪除此類別嗎?')){
            var url ='';
            var page=str_repeat('../',0)+'del/delA.php';
            var arg ={
                'book_type':book_type,
                'book_sid':book_sid,
                'school_code':school_code,
                'cat_group':cat_group,
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
        }else{
            return false;
        }
    }

    function lv1_set(cat1_id){
    //設置第一階類別

        var cat1_id=cat1_id;

        //移除第二階舊有資訊, 附加第二階基本資訊
        $(ocat2_id).empty().append('<option value="1" att="請選擇">請選擇');

        //移除第三階舊有資訊, 附加第三階基本資訊
        $(ocat3_id).empty().append('<option value="1" att="請選擇">請選擇');

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
            $(ocat2_id)[0].setAttribute('onchange','lv2_set(this.options[this.selectedIndex].value);');
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