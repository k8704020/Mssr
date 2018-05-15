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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_borrow_library_export');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //SESSION
        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用
        $m_user_borrow_library_export_class_code='';

        if(isset($_SESSION['m_user_borrow_library_export']['filter'])){
            $filter=$_SESSION['m_user_borrow_library_export']['filter'];
        }
        if(isset($_SESSION['m_user_borrow_library_export']['query_fields'])){
            $query_fields=$_SESSION['m_user_borrow_library_export']['query_fields'];
        }
        if((isset($_SESSION['m_user_borrow_library_export']['class_code']))&&(trim($_SESSION['m_user_borrow_library_export']['class_code'])!=='')){
            $m_user_borrow_library_export_class_code=$_SESSION['m_user_borrow_library_export']['class_code'];
        }

        if(isset($sess_login_info['uid'])){$sess_user_id=(int)$sess_login_info['uid'];}
        if(isset($sess_login_info['permission'])){$sess_permission=trim($sess_login_info['permission']);}
        if(isset($sess_login_info['school_code'])){$sess_school_code=trim($sess_login_info['school_code']);}
        if(isset($sess_login_info['responsibilities'])){
            $sess_responsibilities=(int)$sess_login_info['responsibilities'];
            if(in_array($auth_sys_check_lv,array(5,14,16,22,99))){
                $sess_class_code=trim($sess_login_info['arrys_class_code'][0]['class_code']);
                $sess_grade=(int)$sess_login_info['arrys_class_code'][0]['grade'];
                $sess_classroom=(int)$sess_login_info['arrys_class_code'][0]['classroom'];
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //目標班級
        $goal_class_code='';
        if((isset($m_user_borrow_library_export_class_code))&&(trim($m_user_borrow_library_export_class_code)!=='')){
            $goal_class_code=$m_user_borrow_library_export_class_code;
        }

    //---------------------------------------------------
    //串接SQL
    //---------------------------------------------------

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

            //初始化, 學年陣列資訊
            $arry_semester_year_info=array();
            $json_semester_year_info=json_encode(array(),true);

            //初始化, 學期陣列資訊
            $arrys_semester_term_info=array();
            $jsons_semester_term_info=json_encode(array(),true);

            //初始化, 年級陣列資訊
            $arrys_grade_info=array();
            $jsons_grade_info=json_encode(array(),true);

            //初始化, 班級陣列資訊
            $arrys_classroom_info=array();
            $jsons_classroom_info=json_encode(array(),true);

            $sess_school_code=mysql_prep($sess_school_code);

        //-----------------------------------------------
        //主SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr`.`mssr_book_borrow_export_log`.`class_code`,
                    `mssr`.`mssr_book_borrow_export_log`.`user_id`,
                    `mssr`.`mssr_book_borrow_export_log`.`keyin_mdate`,

                    `user`.`member`.`name`
                FROM `mssr`.`mssr_book_borrow_export_log`
                    INNER JOIN `user`.`member` ON
                    `mssr`.`mssr_book_borrow_export_log`.`user_id`=`user`.`member`.`uid`
                WHERE 1=1
                    AND `mssr`.`mssr_book_borrow_export_log`.`school_code`='{$sess_school_code}'
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

        //-----------------------------------------------
        //Query班級查詢
        //-----------------------------------------------

            if((isset($m_user_borrow_library_export_class_code))&&(trim($m_user_borrow_library_export_class_code)!=='')){
                $sql="
                    SELECT
                        `class`.`grade`,
                        `class`.`classroom`,

                        `semester`.`semester_year`,
                        `semester`.`semester_term`
                    FROM `class`
                        INNER JOIN `user`.`semester` ON
                        `user`.`class`.`semester_code`=`user`.`semester`.`semester_code`
                    WHERE 1=1
                        AND `class`.`class_code`='{$m_user_borrow_library_export_class_code}'
                ";
                $arrys_query_class_code=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
                if(!empty($arrys_query_class_code)){
                    $rs_query_semester_year=(int)$arrys_query_class_code[0]['semester_year'];
                    $rs_query_semester_term=(int)$arrys_query_class_code[0]['semester_term'];
                    $rs_query_grade        =(int)$arrys_query_class_code[0]['grade'];
                    $rs_query_classroom    =(int)$arrys_query_class_code[0]['classroom'];
                }
            }

        //-----------------------------------------------
        //歷史班級查詢
        //-----------------------------------------------

            //-------------------------------------------
            //學年、學期查詢
            //-------------------------------------------
            //1     校長
            //3     主任
            //5     帶班老師
            //12    行政老師
            //14    主任帶一個班
            //16    主任帶多個班
            //22    老師帶多個班
            //99    管理者

                $curdate=date("Y-m-d");
                $sql="
                    SELECT
                        `semester`.`semester_code`,
                        `semester`.`semester_year`,
                        `semester`.`semester_term`,
                        `semester`.`start`,
                        `semester`.`end`,
                        `semester`.`school_code`
                    FROM `semester`
                    WHERE 1=1
                        AND `semester`.`school_code`='{$sess_school_code}'
                        AND `semester`.`start`      <='{$curdate}'
                        AND `semester`.`end`        >='{$curdate}'
                    ORDER BY `semester`.`start` ASC
                ";
                $arrys_semester=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
                if(!empty($arrys_semester)){
                //---------------------------------------
                //學年、學期處理
                //---------------------------------------

                    foreach($arrys_semester as $arry_semester){

                        $semester_code=trim($arry_semester['semester_code']);
                        $semester_year=trim($arry_semester['semester_year']);
                        $semester_term=(int)$arry_semester['semester_term'];

                        //學年
                        if(!in_array($semester_year,$arry_semester_year_info)){
                            $arry_semester_year_info[]=$semester_year;
                        }

                        //學期
                        if(array_key_exists($semester_year,$arrys_semester_term_info)){
                            $arrys_semester_term_info[$semester_year][$semester_code]=$semester_term;
                        }else{
                            $arrys_semester_term_info[$semester_year]=array();
                            $arrys_semester_term_info[$semester_year][$semester_code]=$semester_term;
                        }
                    }

                //---------------------------------------
                //年級查詢
                //---------------------------------------

                    foreach($arrys_semester as $arry_semester){
                    //-----------------------------------
                    //接收欄位
                    //-----------------------------------

                        extract($arry_semester, EXTR_PREFIX_ALL, "rs");

                    //-----------------------------------
                    //處理欄位
                    //-----------------------------------

                        $rs_semester_code=trim($rs_semester_code);
                        $rs_semester_year=(int)$rs_semester_year;
                        $rs_semester_term=(int)$rs_semester_term;
                        $rs_start        =trim($rs_start);
                        $rs_end          =trim($rs_end);
                        $rs_school_code  =trim($rs_school_code);

                    //-----------------------------------
                    //查詢
                    //-----------------------------------

                        $sql="
                            SELECT
                                `class`.`grade`
                            FROM `class`
                            WHERE 1=1
                                AND `class`.`semester_code`='{$rs_semester_code}'
                            GROUP BY `class`.`grade`
                            ORDER BY `class`.`grade` ASC
                        ";
                        $arrys_grade=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
                        if(!empty($arrys_grade)){
                            foreach($arrys_grade as $arry_grade){
                                $rs_grade=(int)$arry_grade['grade'];

                                //年級
                                if(array_key_exists($rs_semester_code,$arrys_grade_info)){
                                    $arrys_grade_info[$rs_semester_code][]=$rs_grade;
                                }else{
                                    $arrys_grade_info[$rs_semester_code]=array();
                                    $arrys_grade_info[$rs_semester_code][]=$rs_grade;
                                }
                            }
                        }
                    }

                //---------------------------------------
                //班級查詢
                //---------------------------------------

                    if(!empty($arrys_grade_info)){
                        $tmp_arry__class_code=[];
                        foreach($arrys_grade_info as $semester_code=>$arry_grade_info){
                        //-------------------------------
                        //處理欄位
                        //-------------------------------

                            $semester_code=mysql_prep(trim($semester_code));

                            foreach($arry_grade_info as $inx=>$grade){
                            //---------------------------
                            //處理欄位
                            //---------------------------

                                $grade=(int)$grade;

                            //---------------------------
                            //查詢
                            //---------------------------

                                $sql="
                                    SELECT
                                        `class`.`grade`,
                                        `class`.`class_code`,
                                        `class_name`.`classroom`,
                                        `class_name`.`class_name`
                                    FROM `class`
                                        INNER JOIN `class_name` ON
                                        `class`.`classroom`=`class_name`.`classroom`
                                    WHERE 1=1
                                        AND `class`.`grade`         = {$grade        }
                                        AND `class`.`semester_code` ='{$semester_code}'
                                        AND `class`.`class_category`=`class_name`.`class_category`
                                    ORDER BY `class`.`classroom` ASC
                                ";
                                $arrys_classroom=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
                                if(!empty($arrys_classroom)){
                                    foreach($arrys_classroom as $inx=>$arry_classroom){
                                        $rs_classroom          =(int)$arry_classroom['classroom'];
                                        $rs_class_code_info    =trim($arry_classroom['class_code']);
                                        $rs_classroom_name_info=trim($arry_classroom['class_name']);
                                        $rs_semester_code_info =trim($semester_code);

                                        //中平2年29班例外處理
                                        if(($rs_school_code==='gcp')&&($grade===2)&&($rs_classroom===29)){
                                            continue;
                                        }

                                        if($rs_semester_code_info===$semester_code&&!in_array($rs_class_code_info,$tmp_arry__class_code)){
                                            //回填
                                            $arrys_classroom_info[$semester_code][$grade][$inx]['classroom']     =$rs_classroom;
                                            $arrys_classroom_info[$semester_code][$grade][$inx]['classroom_name']=$rs_classroom_name_info;
                                            $arrys_classroom_info[$semester_code][$grade][$inx]['class_code']    =$rs_class_code_info;
                                            $tmp_arry__class_code[]=$rs_class_code_info;
                                        }
                                    }
                                }
                            }
                        }

                    //-----------------------------------
                    //老師帶多班, 過濾老師班級
                    //-----------------------------------

                        if(in_array($auth_sys_check_lv,array(22))){
                            $tmp_rs_arry_grade_info=array();
                            foreach($arrys_sess_login_info[0]['arrys_class_info'] as $arry_sess_login_info){
                                $rs_grade=(int)($arry_sess_login_info['grade']);
                                if(!in_array($rs_grade,$tmp_rs_arry_grade_info)){
                                    $tmp_rs_arry_grade_info[]=$rs_grade;
                                }
                            }
                            foreach($arrys_grade_info as $semester_code=>$arry_grade_info){
                                foreach($arry_grade_info as $inx=>$grade){
                                    $rs_grade=(int)$grade;
                                    if(!in_array($rs_grade,$tmp_rs_arry_grade_info)){
                                        unset($arrys_grade_info[$semester_code][$inx]);
                                    }
                                }
                            }

                            $tmp_sess_arry_class_code=array();
                            foreach($arrys_sess_login_info[0]['arrys_class_info'] as $arry_sess_login_info){
                                $rs_class_code=trim($arry_sess_login_info['class_code']);
                                $tmp_sess_arry_class_code[]=$rs_class_code;
                            }
                            foreach($arrys_classroom_info as $semester_code=>$arrys_classroom_info1){
                                foreach($arrys_classroom_info1 as $grade=>$arrys_classroom_info2){
                                    foreach($arrys_classroom_info2 as $inx=>$arry_classroom_info){
                                        $rs_class_code=trim($arry_classroom_info['class_code']);
                                        if(!in_array($rs_class_code,$tmp_sess_arry_class_code)){
                                            unset($arrys_classroom_info[$semester_code][$grade][$inx]);
                                        }
                                        if(empty($arrys_classroom_info[$semester_code][$grade])){
                                            unset($arrys_classroom_info[$semester_code][$grade]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            //整合
            $json_semester_year_info =json_encode($arry_semester_year_info,true);
            $jsons_semester_term_info=json_encode($arrys_semester_term_info,true);
            $jsons_grade_info        =json_encode($arrys_grade_info,true);
            $jsons_classroom_info    =json_encode($arrys_classroom_info,true);

        //-----------------------------------------------
        //帶班班級查詢 for 一般帶班老師
        //-----------------------------------------------

            if(in_array($auth_sys_check_lv,array(5))){
                $goal_class_code=trim($sess_class_code);
            }

        //-----------------------------------------------
        //國別查詢
        //-----------------------------------------------

            $rs_country_code ='';
            $sess_school_code=mysql_prep($sess_school_code);

            $sql="
                SELECT
                    `country_code`
                FROM `school`
                WHERE 1=1
                    AND `school`.`school_code`='{$sess_school_code}'
            ";
            $arrys_country_code=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
            if(!empty($arrys_country_code)){
                $rs_country_code=trim($arrys_country_code[0]['country_code']);
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
<?php
//echo "<Pre>";
//print_r($goal_class_code);
//echo "</Pre>";
?>
<!-- 容器區塊 開始 -->
<div id="container">

    <!-- 內容區塊 開始 -->
    <div id="content">
        <table id="teacher_datalist_tbl" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td align="left" valign="middle" width="420px">
                    <!-- 教師中心路徑選單 開始 -->
                    <div id="teacher_center_path" style="width:420px;">
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
                        <table cellpadding="0" cellspacing="0" border="1" width="100%" class="table_style2" style="position:relative;top:20px;"/>
                            <tr align="center" valign="middle" class="bg_gray1 fc_white0">
                                <td height="30px" colspan="2">
                                    『圖書館書籍』學期借閱資料匯出
                                </td>
                            </tr>
                            <tr align="center" valign="middle">
                                <td width="550px" height="50px" align="center">
                                    <?php if(in_array($auth_sys_check_lv,array(1,3,14,16,22,99,20,24))):?>
                                    <span>
                                        <select class="mode_select form_select" style="">
                                            <option value="1">請選擇模式
                                            <option value="2">匯出全校
                                            <option value="3">匯出各班
                                        </select>
                                    </span>
                                    <span class="mode_school" style="display:none;">
                                        匯出本學期全校各班『圖書館書籍』學期借閱紀錄
                                        <input type="button" value="點我匯出" class="muilt_borrow_export" onclick="muilt_borrow_export();void(0);"
                                        onmouseover="this.style.cursor='pointer'"
                                        style="position:relative;left:5px;">
                                    </span>
                                    <?php endif;?>
                                    <span class="mode_class" style="display:none;">
                                        <?php if(in_array($auth_sys_check_lv,array(5))):?>
                                        <?php
                                        //1     校長
                                        //3     主任
                                        //5     帶班老師
                                        //12    行政老師
                                        //14    主任帶一個班
                                        //16    主任帶多個班
                                        //22    老師帶多個班
                                        //99    管理者

                                            $goal_class_code=addslashes($goal_class_code);
                                            $sql="
                                                SELECT
                                                    `class`.`grade`,
                                                    `class`.`classroom`,

                                                    `class_name`.`class_name`
                                                FROM `class`
                                                    INNER JOIN `class_name` ON
                                                    `class`.`class_category`=`class_name`.`class_category`
                                                WHERE 1=1
                                                    AND `class`.`class_code`= '{$goal_class_code}'
                                                    AND `class`.`classroom`=`class_name`.`classroom`
                                            ";
                                            $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
                                            if(!empty($arrys_result)){
                                                extract($arrys_result[0], EXTR_PREFIX_ALL, "rs");
                                                $grade=(int)$rs_grade;
                                                $rs_classroom=(int)$rs_classroom;
                                                $rs_class_name=trim($rs_class_name);
                                            }
                                        ?>
                                            您的班級：<?php echo $grade;?> 年 <?php echo htmlspecialchars($rs_class_name);?> 班
                                        <?php elseif(in_array($auth_sys_check_lv,array(1,3,14,16,22,99,20,24))):?>
                                            請選擇：
                                            <span id="span_class_code" style="position:relative;" class="">
                                                <select id="semester_year" class="form_select" tabindex="1" style="position:relative;width:50px;" onchange="sel_semester_year(this);void(0);">
                                                    <option value="">請選擇
                                                <?php foreach($arry_semester_year_info as $semester_year):?>
                                                <?php
                                                    $semester_year=(int)$semester_year;
                                                ?>
                                                    <option value="<?php echo $semester_year;?>"
                                                    <?php if(isset($rs_query_semester_year)&&$semester_year===$rs_query_semester_year)echo 'selected';?>>
                                                    <?php
                                                        if($rs_country_code!=="tw"){
                                                            echo $semester_year;
                                                        }else{
                                                            echo $semester_year-(int)1911;
                                                        }
                                                    ?>
                                                <?php endforeach;?>
                                                </select>
                                                學年

                                                <span style="position:relative;">第</span>
                                                <select id="semester_term" name="semester_term" class="form_select" tabindex="2" style="position:relative;width:50px;"
                                                onchange="sel_semester_term(this);void(0);">
                                                    <option value="<?php if(isset($rs_query_semester_term))echo (int)$rs_query_semester_term;?>"
                                                    ><?php if(isset($rs_query_semester_term)){echo (int)$rs_query_semester_term;}else{echo '請選擇';}?>
                                                </select>
                                                學期

                                                <select id="grade" name="grade" class="form_select" tabindex="3" style="position:relative;width:50px;"
                                                onchange="sel_grade(this);void(0);">
                                                    <option value="<?php if(isset($rs_query_grade))echo (int)$rs_query_grade;?>"
                                                    ><?php if(isset($rs_query_grade)){echo (int)$rs_query_grade;}else{echo '請選擇';}?>
                                                </select>
                                                年

                                                <select id="class_code" name="class_code" class="form_select" tabindex="4" style="position:relative;width:50px;"
                                                onchange="sel_class_code(this);void(0);">
                                                    <option value="<?php if(isset($rs_query_classroom))echo (int)$rs_query_classroom;?>"
                                                    ><?php if(isset($rs_query_classroom)){echo (int)$rs_query_classroom;}else{echo '請選擇';}?>
                                                </select>
                                                班
                                            </span>
                                        <?php else:?>

                                        <?php endif;?>

                                        <?php if($goal_class_code!==''):?>
                                            <?php if(in_array($auth_sys_check_lv,array(1,3,14,16,22,99,20,24))):?>
                                                <input type="button" value="重選" class="_ibtn_gr3020" onclick="reset_sel();void(0);"
                                                onmouseover="this.style.cursor='pointer'"
                                                style="position:relative;left:10px;">
                                            <?php endif;?>
                                        <?php endif;?>
                                    </span>
                                </td>
                                <td height="50px" align="center">
                                    <?php if($goal_class_code!==''):?>
                                        <input type="button" value="點我匯出" class="_ibtn_gr3020" onclick="borrow_export();void(0);"
                                        onmouseover="this.style.cursor='pointer'"
                                        style="position:relative;left:5px;">
                                    <?php endif;?>
                                    <span id='success' style='display:none;position:relative;right:-5px;' class="fc_blue1 fsize_16 font-family1 font-weight1"
                                    onmouseover="mouseover(this);void(0);">
                                        <u>下載EXCEL檔案</u>
                                    </span>
                                    <span id='muilt_success' style='display:none;position:relative;right:-5px;' class="fc_blue1 fsize_16 font-family1 font-weight1"
                                    onmouseover="mouseover(this);void(0);">
                                        <u>下載EXCEL檔案</u>
                                    </span>
                                </td>
                            </tr>
                        </table>

                        <input type="hidden" id="goal_class_code" name="goal_class_code" value="<?php echo addslashes($goal_class_code);?>"
                        style="position:relative;top:35px;">

                        <table height="275px" cellpadding="0" cellspacing="0" border="" width="100%" class="table_style2" style="position:relative;top:75px;"/>
                            <tr align="center" valign="middle" class="">
                                <td height="30px" colspan="3">
                                    <span>【匯出紀錄】</span>
                                </td>
                            </tr>
                            <?php if(!empty($db_results)):?>
                                <tr align="center" valign="middle" class="bg_gray1 fc_white0">
                                    <td height="25px" width="300px">匯出班級  </td>
                                    <td height="25px" width="300px">匯出人員  </td>
                                    <td height="25px" width="">匯出時間       </td>
                                </tr>
                                <?php foreach($db_results as $db_result):?>
                                <?php
                                    extract($db_result, EXTR_PREFIX_ALL, "rs");

                                    $rs_user_id     =(int)$rs_user_id;
                                    $rs_class_code  =addslashes(trim($rs_class_code));
                                    $rs_name        =trim($rs_name);
                                    $rs_keyin_mdate =trim($rs_keyin_mdate);

                                    //查找班級
                                    $sql="
                                        SELECT
                                            `class`.`grade`,
                                            `class_name`.`class_name`
                                        FROM `class`
                                            INNER JOIN `class_name` ON
                                            `class`.`class_category`=`class_name`.`class_category`
                                        WHERE 1=1
                                            AND `class`.`class_code`='{$rs_class_code}'
                                            AND `class_name`.`classroom`=`class`.`classroom`

                                    ";
                                    $arry_class_info=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
                                    if(!empty($arry_class_info)){
                                        $rs_grade     =(int)$arry_class_info[0]['grade'];
                                        $rs_class_name=trim($arry_class_info[0]['class_name']);
                                    }
                                ?>
                                    <tr align="center" valign="middle" class="">
                                        <td height="" width="">
                                            <?php echo $rs_grade;?> 年
                                            <?php echo htmlspecialchars($rs_class_name);?> 班
                                        </td>
                                        <td height="" width=""><?php echo htmlspecialchars($rs_name);?>         </td>
                                        <td height="" width=""><?php echo htmlspecialchars($rs_keyin_mdate);?>  </td>
                                    </tr>
                                <?php endforeach?>
                            <?php else:?>
                                <tr align="center" valign="top">
                                    <td width="100%" height="50px" align="center">
                                        <img src="../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
                                        <span class="fc_red0">目前無任何紀錄</span>
                                    </td>
                                </tr>
                            <?php endif;?>
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
    var psize=10;
    var pinx =1;
    var jsons_semester_term_info=<?php echo $jsons_semester_term_info;?>;
    var jsons_grade_info        =<?php echo $jsons_grade_info;?>;
    var jsons_classroom_info    =<?php echo $jsons_classroom_info;?>;
    var goal_class_code         ='<?php echo $goal_class_code;?>';

    //物件
    var osemester_terms =document.getElementsByName('semester_term');
    var ogrades         =document.getElementsByName('grade');
    var oclass_codes    =document.getElementsByName('class_code');
    var okinships       =document.getElementsByName('kinship');

    //FUNCTION
    try{
        $('.mode_select').change(function(){
            if(parseInt($(this).val())===2){
                $('.mode_school').show();
                $('.muilt_borrow_export').show();
                $('.mode_class').hide();
                $('._ibtn_gr3020').hide();
                $('#success').hide();
                $('#muilt_success').hide();
            }
            if(parseInt($(this).val())===3){
                $('.mode_school').hide();
                $('.muilt_borrow_export').hide();
                $('._ibtn_gr3020').show();
                $('#success').hide();
                $('#muilt_success').hide();
                $('.mode_class').show();
            }
        });
    }catch(e){

    }

    function muilt_borrow_export(){

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :0,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",

            url        :'muilt_exportA.php',
            type       :'post',
            datatype   :'json',
            data       :{
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
                $('#muilt_success').hide();
                block_ui();
            },
            success     :function(respones){
            //成功處理
//console.log(respones);
//return false;
                var respones=jQuery.parseJSON(respones);
                var flag    =trim(respones.flag);
                var msg     =trim(respones.msg);

                if(flag==='true'){
                    $('#muilt_success').show();
                    $('#muilt_success').click(function(e){
                        muilt_download_result_excel();
                    });
                }

                $.unblockUI();
                alert(msg);
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                alert('資料處理失敗，請再試一次...');
                $.unblockUI();
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function muilt_download_result_excel(){
    //下載Excel結果表格
        var url      ='muilt_download_result_excelF.php';
        var oiframe  =document.createElement("IFRAME");
        oiframe.src  =url;
        oiframe.style.display="none";

        //清除新開
        $(document.body).find("IFRAME").remove();
        document.body.appendChild(oiframe);
    }

    function reset_sel(){
        var obj=new Object();
        var osemester_year=document.getElementById('semester_year');
        obj.value='';
        osemester_year.options[0].selected=true;
        sel_semester_year(obj);
    }

    function borrow_export(){

        var ogoal_class_code=document.getElementById('goal_class_code');
        var osuccess        =document.getElementById('success');
        var class_code      =trim(ogoal_class_code.value);

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :0,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",

            url        :'exportA.php',
            type       :'post',
            datatype   :'json',
            data       :{
                class_code     :encodeURI(trim(class_code))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
                $(osuccess).hide();
                block_ui();
            },
            success     :function(respones){
            //成功處理
//console.log(respones);
//return false;
                var respones=jQuery.parseJSON(respones);
                var flag    =trim(respones.flag);
                var msg     =trim(respones.msg);

                if(flag==='true'){
                    $(osuccess).show();
                    osuccess.onclick=function(e){
                        download_result_excel();
                    }
                }

                $.unblockUI();
                alert(msg);
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                if(ajaxoptions==='timeout'){
                    alert('資料處理失敗，請再試一次...');
                }else{
                    alert('資料處理失敗，請再試一次...');
                }

                $.unblockUI();
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function sel_semester_year(obj){
    //選擇學年

        //清除學期選項
        for(var i=0;i<osemester_terms.length;i++){
            var osemester_term=osemester_terms[i];
            $(osemester_term).find('option').remove();;
            $(osemester_term).append('<option value="">請選擇');
        }

        //清除年級選項
        for(var i=0;i<ogrades.length;i++){
            var ograde=ogrades[i];
            $(ograde).find('option').remove();
            $(ograde).append('<option value="">請選擇');
        }

        //清除班級選項
        for(var i=0;i<oclass_codes.length;i++){
            var oclass_code=oclass_codes[i];
            $(oclass_code).find('option').remove();
            $(oclass_code).append('<option value="">請選擇');
        }
        for(var i=0;i<okinships.length;i++){
            var okinship=okinships[i];
            $(okinship).find('option').remove();
            $(okinship).append('<option value="">請選擇');
        }

        var semester_year=parseInt(obj.value);

        for(key1 in jsons_semester_term_info){
            if(parseInt(key1)===semester_year){
                for(key2 in jsons_semester_term_info[key1]){

                    var semester_term=parseInt(jsons_semester_term_info[key1][key2]);
                    var semester_code=trim(key2);

                    //元素設置
                    var _html='';
                    _html+='<option value="'+semester_code+'">'+semester_term+'';

                    //附加
                    for(var i=0;i<osemester_terms.length;i++){
                        var osemester_term=osemester_terms[i];
                        $(osemester_term).append(_html);
                    }
                }
            }
        }
    }

    function sel_semester_term(obj){
    //選擇學期

        //清除年級選項
        for(var i=0;i<ogrades.length;i++){
            var ograde=ogrades[i];
            $(ograde).find('option').remove();
            $(ograde).append('<option value="">請選擇');
        }

        //清除班級選項
        for(var i=0;i<oclass_codes.length;i++){
            var oclass_code=oclass_codes[i];
            $(oclass_code).find('option').remove();
            $(oclass_code).append('<option value="">請選擇');
        }
        for(var i=0;i<okinships.length;i++){
            var okinship=okinships[i];
            $(okinship).find('option').remove();
            $(okinship).append('<option value="">請選擇');
        }

        var semester_code=trim(obj.value);

        for(key1 in jsons_grade_info){
            if(trim(key1)===semester_code){
                for(key2 in jsons_grade_info[key1]){

                    var grade=parseInt(jsons_grade_info[key1][key2]);

                    //元素設置
                    var _html='';
                    _html+='<option value="'+grade+'">'+grade+'';

                    //附加
                    for(var i=0;i<ogrades.length;i++){
                        var ograde=ogrades[i];
                        $(ograde).append(_html);
                        ograde.setAttribute('semester_code',semester_code);
                    }
                }
            }
        }
    }

    function sel_grade(obj){
    //選擇年級

        //清除班級選項
        for(var i=0;i<oclass_codes.length;i++){
            var oclass_code=oclass_codes[i];
            $(oclass_code).find('option').remove();
            $(oclass_code).append('<option value="">請選擇');
            //if(sess_user_lv!==3){
            //    $(oclass_code).append('<option value="'+parseInt(obj.value)+'">不分班級');
            //}
        }
        for(var i=0;i<okinships.length;i++){
            var okinship=okinships[i];
            $(okinship).find('option').remove();
            $(okinship).append('<option value="">請選擇');
            //if(sess_user_lv!==3){
            //    $(okinship).append('<option value="'+parseInt(obj.value)+'">不分班級');
            //}
        }

        var grade=parseInt(obj.value);
        var semester_code=obj.getAttribute('semester_code');

        for(key1 in jsons_classroom_info){
            if(trim(key1)===semester_code){
                for(key2 in jsons_classroom_info[key1]){
                    if(parseInt(key2)===grade){
                        for(key3 in jsons_classroom_info[key1][key2]){

                            var classroom     =parseInt(jsons_classroom_info[key1][key2][key3]['classroom']);
                            var classroom_name=trim(jsons_classroom_info[key1][key2][key3]['classroom_name']);
                            var class_code    =trim(jsons_classroom_info[key1][key2][key3]['class_code']);

                            //元素設置
                            var _html='';
                            _html+='<option value="'+class_code+'">'+classroom_name+'';

                            //附加
                            for(var i=0;i<oclass_codes.length;i++){
                                var oclass_code=oclass_codes[i];
                                $(oclass_code).append(_html);
                            }
                            for(var i=0;i<okinships.length;i++){
                                var okinship=okinships[i];
                                $(okinship).append(_html);
                            }
                        }
                    }
                }
            }
        }
    }

    function sel_class_code(obj){
    //查詢班級

        var val =trim(obj.value);
        var type=trim('class_code');
        var url ='';
        var page=str_repeat('../',0)+'query.php';
        var arg ={
            'type'      :type,
            'class_code':val,
            'psize'     :psize,
            'pinx'      :pinx
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

        block_ui();
        go(url,'self');
    }

    function download_result_excel(){
    //下載Excel結果表格
        var ogoal_class_code=document.getElementById('goal_class_code');
        var class_code      =trim(ogoal_class_code.value);

        var url      ='download_result_excelF.php?class_code='+class_code;
        var oiframe  =document.createElement("IFRAME");
        oiframe.src  =url;
        oiframe.style.display="none";

        //清除新開
        $(document.body).find("IFRAME").remove();
        document.body.appendChild(oiframe);
    }

    function mouseover(obj){
        obj.style.cursor='pointer';
    }

    function block_ui(){
        $.blockUI({
            message:'<h2 class="fc_white0">資料處理中，請勿關閉頁面 !</h2>',
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

    //ONLOAD
    $(function(){
        if(trim(goal_class_code)!==''){
            $('.mode_school').hide();
            $('.mode_class').show();
        }
    });

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
