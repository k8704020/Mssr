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

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/string/code'
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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_forum_article_info');
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

        if(isset($_SESSION['i_a_school'])){
            $sess_i_a_school=trim($_SESSION['i_a_school']);
            $sess_i_a_school=str_replace(",","','",$sess_i_a_school);
        }else{
            $sess_i_a_school='';
        }

        if(isset($_SESSION['m_forum_article_info']['class_code'])&&trim($_SESSION['m_forum_article_info']['class_code'])!==''){
            $_SESSION['teacher_center']['class_code']=trim($_SESSION['m_forum_article_info']['class_code']);
        }

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //建立連線 user
        $conn_user=conn($db_type='mysql',$arry_conn_user);

        //-----------------------------------------------
        //提取, 過往的學期班級
        //-----------------------------------------------
        //1     校長
        //3     主任
        //5     帶班老師
        //12    行政老師
        //14    主任帶一個班
        //16    主任帶多個班
        //22    老師帶多個班
        //99    管理者

            $arrys_has_leader_class_code_info=array();
            $json_has_leader_class_code_info=json_encode($arrys_has_leader_class_code_info,true);
            if(in_array($auth_sys_check_lv,array(1,20,24,3,5,12,14,16,22,99))){
                if(!empty($sess_login_info)){

                    $sess_user_id=(int)$sess_user_id;
                    $sql="";

                    if(in_array($auth_sys_check_lv,array(5,12,22))){
                        $sql="
                            SELECT
                                `semester`.`semester_code`,
                                `semester`.`semester_year`,
                                `semester`.`semester_term`,
                                `class`.`class_code`,
                                `class`.`grade`,
                                `class`.`classroom`,
                                `class`.`class_category`,
                                `school`.`school_name`

                                #`class_name`.`class_name`
                            FROM `teacher`
                                INNER JOIN `class` ON
                                `teacher`.`class_code`=`class`.`class_code`
                                INNER JOIN `semester` ON
                                `class`.`semester_code`=`semester`.`semester_code`
                                INNER JOIN `school` ON
                                `semester`.`school_code`=`school`.`school_code`
                                #INNER JOIN `class_name` ON
                                #`class`.`class_category`=`class_name`.`class_category`
                            WHERE 1=1
                                AND `teacher`.`uid`={$sess_user_id}
                                AND CURDATE()>=`semester`.`start`
                        ";
                    }else{
                        if(isset($sess_school_code)){
                            $sess_school_code=mysql_prep($sess_school_code);
                            $sql="
                                SELECT
                                    `semester`.`semester_code`,
                                    `semester`.`semester_year`,
                                    `semester`.`semester_term`,
                                    `class`.`class_code`,
                                    `class`.`grade`,
                                    `class`.`classroom`,
                                    `class`.`class_category`,
                                    `school`.`school_name`
                                FROM `class`
                                    INNER JOIN `semester` ON
                                    `class`.`semester_code`=`semester`.`semester_code`
                                    INNER JOIN `school` ON
                                    `semester`.`school_code`=`school`.`school_code`
                                WHERE 1=1
                                    AND `semester`.`school_code`='{$sess_school_code}'
                                    AND CURDATE()>=`semester`.`start`
                            ";
                        }
                    }
                    if($sql!==""){
                        $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
                        if(!empty($arrys_result)){
                            foreach($arrys_result as $inx=>$arry_result){
                                $rs_semester_year   =(int)$arry_result['semester_year'];
                                $rs_semester_term   =(int)$arry_result['semester_term'];
                                $rs_class_category  =(int)$arry_result['class_category'];

                                $rs_grade           =(int)$arry_result['grade'];
                                $rs_classroom       =(int)($arry_result['classroom']);

                                $rs_semester_code   =trim($arry_result['semester_code']);
                                $rs_class_code      =trim($arry_result['class_code']);
                                $rs_school_name     =trim($arry_result['school_name']);

                                $sql="
                                    SELECT
                                        `class_name`.`class_name`
                                    FROM `class_name`
                                    WHERE 1=1
                                        AND `class_name`.`classroom`     ={$rs_classroom}
                                        AND `class_name`.`class_category`={$rs_class_category}
                                ";
                                $arrys_class_name=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
                                if(!empty($arrys_class_name)){
                                    $rs_class_name=trim($arrys_class_name[0]['class_name']);
                                }

                                //回填
                                $arrys_has_leader_class_code_info[$rs_school_name."_".$rs_semester_code][$inx]['grade']      =$rs_grade;
                                $arrys_has_leader_class_code_info[$rs_school_name."_".$rs_semester_code][$inx]['class_name'] =$rs_class_name;
                                $arrys_has_leader_class_code_info[$rs_school_name."_".$rs_semester_code][$inx]['classroom']  =$rs_classroom;
                                $arrys_has_leader_class_code_info[$rs_school_name."_".$rs_semester_code][$inx]['class_code'] =$rs_class_code;
                                $arrys_has_leader_class_code_info[$rs_school_name."_".$rs_semester_code][$inx]['school_name']=$rs_school_name;
                            }
                        }
                        //轉json格式
                        $json_has_leader_class_code_info=json_encode($arrys_has_leader_class_code_info,true);
                        //echo "<Pre>";
                        //print_r($arrys_has_leader_class_code_info);
                        //echo "</Pre>";
                    }
                }
            }

        //-----------------------------------------------
        //提取, 班級年級陣列
        //-----------------------------------------------
        //1     校長
        //3     主任
        //5     帶班老師
        //12    行政老師
        //14    主任帶一個班
        //16    主任帶多個班
        //22    老師帶多個班
        //99    管理者

            $arrys_school_code_rev=array();
            $json_school_code_rev=json_encode(array(),true);
            if(in_array($auth_sys_check_lv,array(1,3,14,16,22,99))){
                if(!empty($sess_login_info)){
                    $json_class_code_rev=json_encode(array(),true);
                    if($auth_sys_check_lv===99){
                        $sql="
                            SELECT
                                `school_code`,
                                `school_name`,
                                `school_category`,
                                `region_name`,
                                `country_code`
                            FROM `school`
                            WHERE 1=1
                        ";
                        if($sess_i_a_school!==''){
                            $sql.="
                                AND `school_code` IN ('{$sess_i_a_school}')
                            ";
                        }
                        $sql.="
                            ORDER BY FIELD(`country_code`,'tw','hk','sg'), HEX(CONVERT(`school_name` USING BIG5)) ASC
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
                        if(!empty($arrys_result)){
                            foreach($arrys_result as $inx=>$arry_school){
                                extract($arry_school, EXTR_PREFIX_ALL, "rs");
                                $rs_school_code     =trim($rs_school_code);
                                $rs_school_name     =trim($rs_school_name);
                                $rs_school_category =(int)$rs_school_category;
                                $rs_region_name     =trim($rs_region_name);
                                $rs_country_code    =trim($rs_country_code);
                                if(!array_key_exists($rs_country_code,$arrys_school_code_rev))$arrys_school_code_rev[$rs_country_code]=array();
                                if(!array_key_exists($rs_region_name,$arrys_school_code_rev[$rs_country_code]))$arrys_school_code_rev[$rs_country_code][$rs_region_name]=array();
                                $substr_school_name =trim(mb_substr($rs_school_name,0,1));
                                $num_school_name    =(int)get_cht_chnnum($substr_school_name);
                                if(!array_key_exists($num_school_name,$arrys_school_code_rev[$rs_country_code][$rs_region_name]))$arrys_school_code_rev[$rs_country_code][$rs_region_name][$num_school_name]=array();
                                $arrys_school_code_rev[$rs_country_code][$rs_region_name][$num_school_name][$inx][trim('school_code    ')]=$rs_school_code;
                                $arrys_school_code_rev[$rs_country_code][$rs_region_name][$num_school_name][$inx][trim('school_name    ')]=$rs_school_name;
                                $arrys_school_code_rev[$rs_country_code][$rs_region_name][$num_school_name][$inx][trim('school_category')]=$rs_school_category;
                                $arrys_school_code_rev[$rs_country_code][$rs_region_name][$num_school_name][$inx][trim('region_name    ')]=$rs_region_name;
                                $arrys_school_code_rev[$rs_country_code][$rs_region_name][$num_school_name][$inx][trim('country_code   ')]=$rs_country_code;
                            }
                            $json_school_code_rev=json_encode($arrys_school_code_rev,true);
                        }else{
                            die('發生嚴重錯誤! 請通知明日星球管理人員。');
                        }
                    }
                    if((isset($sess_school_code))&&(trim($sess_school_code)!=='')){
                        //初始化, 班級對應所屬名稱
                        $arrys_class_code_rev=array();
                        $arrys_class_code_rev['grade'][]='年級';
                        $arrys_class_code_rev['classroom'][]=array('班級');

                        $arrys_class_code_info=get_class_code_info($conn_user,$sess_school_code,$grade=0,$compile_class_code_name=true,$arry_conn_user);
                        if(!empty($arrys_class_code_info)){
                            foreach($arrys_class_code_info as $inx=>$arry_class_code_info){
                                $rs_grade=(int)$arry_class_code_info['grade'];
                                $rs_classroom=trim($arry_class_code_info['classroom']);
                                $rs_class_code=trim($arry_class_code_info['class_code']);

                                //匯入, 班級年級陣列
                                $arrys_class_code_rev['grade'][$rs_grade]=$rs_grade;
                                $arrys_class_code_rev['classroom'][$rs_grade][]=$rs_classroom;
                                $arrys_class_code_rev['class_code'][$rs_grade][]=$rs_class_code;
                            }
                            if($auth_sys_check_lv===22){
                            //22, 老師帶多個班, 只顯示帶的班級可供選擇
                                $i=0;
                                $arry_sess_focus=array();
                                while($i<count($arrys_login_info[3]['arrys_class_code'])){
                                    $sess_grade=(int)$arrys_login_info[3]['arrys_class_code'][$i]['grade'];
                                    $sess_class_code=trim($arrys_login_info[3]['arrys_class_code'][$i]['class_code']);
                                    $arry_sess_focus[$sess_grade][]=$sess_class_code;
                                    $i++;
                                }

                                //開始過濾
                                foreach($arrys_class_code_rev as $field_name=>$arry_class_code_rev){
                                    $field_name=trim($field_name);
                                    if($field_name==='class_code'){
                                        foreach($arry_class_code_rev as $key1=>$arry_val){
                                            foreach($arry_val as $key2=>$val1){
                                                if(isset($arry_sess_focus[$key1])){
                                                    if(!in_array($val1,$arry_sess_focus[$key1])){
                                                        unset($arrys_class_code_rev['classroom'][$key1][$key2]);
                                                        unset($arrys_class_code_rev['class_code'][$key1][$key2]);
                                                    }
                                                }
                                            }
                                        }
                                    }else{
                                        foreach($arry_class_code_rev as $key2=>$arry_val){
                                            if(($key2!==0)&&(!isset($arry_sess_focus[$key2]))&&(empty($arry_sess_focus[$key2]))){
                                                unset($arrys_class_code_rev['grade'][$key2]);
                                                unset($arrys_class_code_rev['classroom'][$key2]);
                                                unset($arrys_class_code_rev['class_code'][$key2]);
                                            }
                                        }
                                    }
                                }
                            }

                            //轉json格式
                            $json_class_code_rev=json_encode($arrys_class_code_rev,true);
                        }
                    }
                }
            }

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,教師中心";

        //註腳列
        $footer=footer($rd=4);

        ////系統陣列
        //$header_right_sys_arry   =$config_arrys['center']['teacher_center']['report']['header_right']['sys_arry'];
        //
        ////系統名稱陣列
        //$header_right_name_arry  =$config_arrys['center']['teacher_center']['report']['header_right']['name_arry'];
        //
        ////系統連結陣列
        //$header_right_url_arry   =$config_arrys['center']['teacher_center']['report']['header_right']['url_arry'];
        //
        ////系統連結框架陣列
        //$header_right_target_arry=$config_arrys['center']['teacher_center']['report']['header_right']['target_arry'];

        //系統權限陣列(報表專用)
        $auth_sys_arry_report=auth_sys_arry_report();

        //系統權限陣列(功能專用)
        $auth_sys_arry_config=auth_sys_arry_config();

        //系統權限名稱陣列
        $auth_sys_name_arry=auth_sys_name_arry();

        //系統權限圖片陣列
        $auth_sys_img_arry=auth_sys_img_arry();

        //系統權限連結陣列
        $auth_sys_url_arry=auth_sys_url_arry();

        //系統權限連結框架陣列
        $auth_sys_target_arry=auth_sys_target_arry();

        //-----------------------------------------------
        //教師中心路徑選單
        //-----------------------------------------------

            $auth_sys_name_arry=auth_sys_name_arry();
            $FOLDER=explode('/',dirname($_SERVER['PHP_SELF']));
            $sys_ename=$FOLDER[count($FOLDER)-2];
            $mod_ename=$FOLDER[count($FOLDER)-1];
            $sys_cname='';  //系統名稱
            $mod_cname='';  //模組名稱

            //清除模組查詢資料
            foreach(auth_sys_arry_report() as $sys_name=>$mods_arry){
                foreach($mods_arry as $mod_name=>$mod_arry){
                    if($mod_name!==$mod_ename){
                        unset($_SESSION[$mod_name]);
                    }
                }
            }

            //進入許可檢核
            $access=$auth_sys_arry_report[$sys_ename][$mod_ename]['access'];
            if(!$access){
                $url=str_repeat("../",5).'index.php';
                header("Location: {$url}");
                die();
            }

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

    <link rel="stylesheet" type="text/css" href="../../../../lib/jquery/ui/code.css" media="all" />
    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/ui/code.js"></script>

    <script type="text/javascript" src="../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/table/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/array/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" />
</Head>

<Body>

<!-- 容器區塊 開始 -->
<div id="container">

    <!-- 標題區塊 開始 -->
    <div id="header" style="display:none;">

        <!-- 標題左方區塊 開始 -->
        <ul id="header_left">
            <li>
                <a href="#">
                    <span>
                        您好 !
                        <?php
                            if(isset($sess_login_info['name'])){
                                echo htmlspecialchars($sess_login_info['name']);
                            }
                        ?>
                        歡迎使用-學習歷程專區。
                    </span>
                </a>
            </li>
        </ul>
        <!-- 標題左方區塊 結束 -->

        <!-- 標題右方區塊 開始 -->
        <ul id="header_right">
        <?php foreach($header_right_sys_arry as $sys=>$mod_arry):?>
        <?php
            $sys_en=trim($sys);
            $sys_ch=trim($header_right_name_arry[$sys_en]);
            $sys_url=trim($header_right_url_arry[$sys_en]);
            $sys_target=trim($header_right_target_arry[$sys_en]);
        ?>
            <li>
                <a href="#" target="<?php echo $sys_target;?>" rel="nofollow"><span onclick="parent.location.href='<?php echo $sys_url;?>';"><?php echo htmlspecialchars($sys_ch);?></span></a>
                <?php if(!empty($mod_arry)):?>
                <ul class="child">
                <?php foreach($mod_arry as $inx=>$mod):?>
                <?php
                    $mod_en=trim($mod);
                    $mod_ch=trim($header_right_name_arry[$mod_en]);
                    $mod_url=trim($header_right_url_arry[$mod_en]);
                    $mod_target=trim($header_right_target_arry[$mod_en]);
                ?>
                    <li><a href="#" target="<?php echo $mod_target;?>" rel="nofollow"><span onclick="parent.location.href='<?php echo $mod_url;?>';"><?php echo htmlspecialchars($mod_ch);?></span></a></li>
                <?php endforeach;?>
                </ul>
                <?php endif;?>
            </li>
        <?php endforeach;?>
        </ul>
        <!-- 標題右方區塊 結束 -->

    </div>
    <!-- 標題區塊 結束 -->

    <!-- 內容區塊 開始 -->
    <div id="content">

        <!-- 內容區塊(上半部) 開始 -->
        <div id="content_top">

            <table class="teacher_datalist_tbl" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="left" valign="middle" width="600px">
                        <!-- 導覽列 開始 -->
                        <div id="navbar">
                            <ul id="navbar_ul">
                            <?php foreach($auth_sys_arry_report as $sys=>$mod_arry):?>
                            <?php
                                $sys_en=trim($sys);
                                $sys_ch=trim($auth_sys_name_arry[$sys_en]);
                                $sys_target=trim($auth_sys_target_arry[$sys_en]);
                                $sys_url=trim($auth_sys_url_arry[$sys_en]);
                                $sys_color='#ffffff';
                                if(($sys_en==='go_to_ctrl')||($sys_en==='back_to_front')){
                                    $sys_color='#ffff00';
                                }
                            ?>
                                <li>
                                    <a href="<?php echo $sys_url;?>" target="<?php echo $sys_target;?>" rel="nofollow">
                                        <span style="color:<?php echo $sys_color;?>;"><?php echo htmlspecialchars($sys_ch);?></span>
                                    </a>
                                    <?php if(!empty($mod_arry)):?>
                                    <ul class="child">
                                    <?php foreach($mod_arry as $mod=>$auth_arry):?>
                                    <?php
                                        $mod_en=trim($mod);
                                        $mod_ch=trim($auth_sys_name_arry[$mod_en]);
                                        $mod_url=trim($auth_sys_url_arry[$mod_en]);
                                        $mod_target=trim($auth_sys_target_arry[$mod_en]);
                                    ?>
                                        <li><a href="<?php echo $mod_url;?>" target="<?php echo $mod_target;?>" rel="nofollow">
                                            <span><?php echo htmlspecialchars($mod_ch);?></span>
                                        </a></li>
                                    <?php endforeach;?>
                                    </ul>
                                    <?php endif;?>
                                </li>
                            <?php endforeach;?>
                            </ul>
                        </div>
                        <!-- 導覽列 結束 -->
                    </td>
                    <td align="left" valign="middle">
                        <!-- 查詢表單列 開始 -->
                        <div style="position:relative;float:left;width:195px;top:5px;border:0px solid #f00;">
                            <?php if($auth_sys_check_lv===99):?>
                            <!-- 查詢學校 -->
                            <span>
                                <input type="text" id="school_name"
                                value="" size="30" maxlength="30"
                                tabindex="1" class="form_text" style="width:50px">

                                <input type="button" value="送出" class="ibtn_gr3020" style="margin:0px 0px;"
                                tabindex="2" onmouseover="this.style.cursor='pointer'" onclick="sel_school2();void(0);">
                            </span>
                            <select id="school_code" name="school_code" style="width:95px;" onchange="sel_school(this.options[this.options.selectedIndex].value);void(0);">
                                <?php //if(!isset($sess_school_code)):?>
                                    <option value="" selected>請選擇學校
                                <?php //endif?>
                                <?php foreach($arrys_school_code_rev as $country_code=>$arrys_school_code_rev1):?>
                                <?php
                                    $country_code=trim($country_code);
                                    $country_code_name='';
                                    switch($country_code){
                                        case 'tw':
                                            $country_code_name='台灣';
                                        break;
                                        case 'hk':
                                            $country_code_name='香港';
                                        break;
                                        case 'sg':
                                            $country_code_name='新加坡';
                                        break;
                                        default:
                                            $country_code_name='未知';
                                        break;
                                    }
                                ?>
                                    <!-- <option value="" style="background:#ffff00;" class='fc_blue0'><?php //echo htmlspecialchars($country_code_name);?> -->
                                    <?php foreach($arrys_school_code_rev1 as $rs_region_name=>$arrys_school_code_rev2):?>
                                    <?php
                                        $rs_region_name=trim($rs_region_name);
                                    ?>
                                        <option value="" style="background:#ffff00;" class='fc_blue0'>
                                        <?php //echo htmlspecialchars($country_code_name);?><?php echo htmlspecialchars($rs_region_name);?>
                                            <?php foreach($arrys_school_code_rev2 as $num_school_name=>$arrys_school_code_rev3):?>
                                            <?php
                                                $num_school_name=(int)($num_school_name);
                                            ?>
                                            <option value="" style="background:#ffffff;" class='fc_red0'>
                                            &nbsp;
                                            <?php echo htmlspecialchars($rs_region_name);?> - <?php echo ($num_school_name);?>劃
                                                <?php foreach($arrys_school_code_rev3 as $inx=>$arry_school_code_rev):?>
                                                <?php
                                                    $rs_school_code =trim($arry_school_code_rev['school_code']);
                                                    $rs_school_name =trim($arry_school_code_rev['school_name']);
                                                    $rs_region_name =trim($arry_school_code_rev['region_name']);
                                                    $rs_country_code=trim($arry_school_code_rev['country_code']);
                                                ?>
                                                <?php if((isset($sess_school_code))&&(trim($sess_school_code)!=='')&&($sess_school_code===$rs_school_code)):?>
                                                    <option value="<?php echo htmlspecialchars($rs_school_code);?>" country_code="<?php echo addslashes($rs_country_code);?>" selected>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <?php echo htmlspecialchars($rs_school_name);?>
                                                <?php else:?>
                                                    <option value="<?php echo htmlspecialchars($rs_school_code);?>" country_code="<?php echo addslashes($rs_country_code);?>">
                                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <?php echo htmlspecialchars($rs_school_name);?>
                                                <?php endif;?>
                                                <?php endforeach;?>
                                        <?php endforeach;?>
                                    <?php endforeach;?>
                                <?php endforeach;?>
                             </select>
                            <?php endif?>
                        </div>

                        <div id="q_form_class_code">
                            <ul id="q_form_class_code_ul">
                                <?php if(in_array($auth_sys_check_lv,array(1,3,14,16,22,99))):?>
                                    <?php if(!empty($arrys_class_code_info)):?>
                                    <!-- 查詢年級 -->
                                    <li>
                                        <?php foreach($arrys_class_code_rev['grade'] as $inx=>$grade):?>
                                            <?php if($inx===0):?>
                                            <?php
                                                $grade=trim($grade);
                                            ?>
                                                <a href="javascript:void(0);" target="" rel="nofollow">
                                                    <span><?php echo htmlspecialchars($grade);?></span>
                                                </a>
                                            <?php endif;?>
                                        <?php endforeach;?>
                                        <ul class="child">
                                        <?php foreach($arrys_class_code_rev['grade'] as $inx=>$grade):?>
                                            <?php if($inx!==0):?>
                                            <?php
                                                $grade=(int)$grade;
                                            ?>
                                                <li><a href="javascript:sel_grade(<?php echo $grade;?>);void(0);" target="" rel="nofollow">
                                                    <?php echo $grade;?>年級
                                                </a></li>
                                            <?php endif;?>
                                        <?php endforeach;?>
                                        </ul>
                                    </li>
                                    <!-- 查詢班級 -->
                                    <li>
                                        <?php foreach($arrys_class_code_rev['classroom'] as $inx=>$classroom):?>
                                            <?php if($inx===0):?>
                                            <?php
                                                $classroom=trim($classroom[0]);
                                            ?>
                                                <a href="javascript:void(0);" target="" rel="nofollow">
                                                    <span id="classroom_title"><?php echo htmlspecialchars($classroom);?></span>
                                                </a>
                                            <?php endif;?>
                                        <?php endforeach;?>
                                        <ul class="child child_classroom">
                                            <li><a href="javascript:void(0);" target="" rel="nofollow">請先選擇年級</a></li>
                                        </ul>
                                    </li>
                                    <?php else:?>
                                    <!-- 無班級 -->
                                    <li>
                                        <a href="javascript:void(0);" target="" rel="nofollow">
                                            <span>未選學校或無班級</span>
                                        </a>
                                    </li>
                                    <?php endif;?>
                                <?php endif;?>
                            </ul>
                        </div>
                        <!-- 查詢表單列 結束 -->
                    </td>
                </tr>
            </table>

        </div>
        <!-- 內容區塊(上半部) 結束 -->

        <!-- 內容區塊(下半部) 開始 -->
        <div id="content_bottom">

            <!-- logo 開始 -->
            <div id="logo">
                明日書店
                <span id="logo_min"><?php echo htmlspecialchars($mod_cname);?></span>
            </div>

            <?php if(isset($arrys_has_leader_class_code_info)&&(!empty($arrys_has_leader_class_code_info))):?>
            <div style='position:relative;top:30px;right:25px;float:right;font-family:"微軟正黑體","標楷體","新細明體";font-size:16px;font-weight:700;color:#87CDDC;border:0px solid #f00;'>
                過往的學期班級：
                <select size="" style="border:1px solid #87CDDC;" onchange="sel_leader_class(this);void(0);">
                    <option value="" selected>請選擇學年學期
                    <?php foreach($arrys_has_leader_class_code_info as $key=>$arry_has_leader_class_code_info):?>
                    <?php
                        $rs_semester_code=trim($key);
                        //$rs_semester_code_replace=str_replace($sess_school_code.'_','',$rs_semester_code);
                        $arry_rs_semester_code=explode('_',$rs_semester_code);
                        $rs_school_name  =trim($arry_rs_semester_code[0]);
                        $rs_semester_year=(int)$arry_rs_semester_code[2]-1911;
                        $rs_semester_term=(int)$arry_rs_semester_code[3];
                    ?>
                    <option value="<?php echo addslashes($rs_semester_code);?>"><?php echo htmlspecialchars($rs_school_name);?><?php echo $rs_semester_year;?>學年第<?php echo $rs_semester_term;?>學期
                    <?php endforeach;?>
                </select>
                <select id="leader_class" name="leader_class" size="" style="border:1px solid #87CDDC;">
                    <option value="">請選擇班級
                </select>
            </div>
            <?php endif;?>
            <!-- logo 結束 -->

            <!-- 資料列表 開始 -->
            <table class="teacher_datalist_tbl" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <iframe id="IFC" name="IFC" src="content.php" frameborder="0"
                        style="width:100%;height:555px;overflow:hidden;overflow-y:auto"></iframe>
                    </td>
                </tr>
            </table>
            <!-- 資料列表 結束 -->

        </div>
        <!-- 內容區塊(下半部) 結束 -->

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

<!-- 快速切換區塊 開始 -->
<?php //echo fast_area($rd=2);?>
<!-- 快速切換區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var choose_identity_flag=<?php echo ($choose_identity_flag)?1:0;?>;
    var child_classroom=$('.child_classroom');
    var oclassroom_title=$('#classroom_title');
    var json_has_leader_class_code_info=<?php echo $json_has_leader_class_code_info;?>;
    var json_school_code_rev=<?php echo $json_school_code_rev;?>;

    $('#school_name').keypress(function(e){
        if(e.which==13){
            sel_school2();
        }
    });

    function sel_leader_class(obj){
    //選擇曾領導的學期

        var semester_code=trim(obj.value);
        var oleader_class=document.getElementById('leader_class');
        $(oleader_class).empty().append('<option value="">請選擇班級');

        for(key1 in json_has_leader_class_code_info){
            if(trim(key1)===semester_code){
                for(key2 in json_has_leader_class_code_info[key1]){
                    var grade      =parseInt(json_has_leader_class_code_info[key1][key2]['grade']);
                    var classroom  =parseInt(json_has_leader_class_code_info[key1][key2]['classroom']);
                    var class_name =trim(json_has_leader_class_code_info[key1][key2]['class_name']);
                    var class_code =trim(json_has_leader_class_code_info[key1][key2]['class_code']);

                    //設置附加元素
                    var _html_opt='';
                    _html_opt+='<option value="'+class_code+'">'+grade+'年'+class_name+'班';

                    try{
                        //附加
                        $(oleader_class).append(_html_opt);
                    }catch(err){
                        return false;
                    }
                }
            }
        }

        oleader_class.onchange=function(){
        //送出查詢
            var class_code=trim(this.value);
            q_form(class_code);
        }
    }

    function sel_school2(){
    //選擇學校

        var oschool_name=document.getElementById('school_name');
        var school_name =trim(oschool_name.value);
        var school_code ='';
        var country_code='';

        if(school_name===''){
            alert('請輸入學校!');
            return false;
        }else{
            for(key1 in json_school_code_rev){
                for(key2 in json_school_code_rev[key1]){
                    for(key3 in json_school_code_rev[key1][key2]){
                        for(key4 in json_school_code_rev[key1][key2][key3]){
                            var j_school_name=trim(json_school_code_rev[key1][key2][key3][key4]['school_name']);
                            if(school_name===j_school_name){
                                school_code=trim(json_school_code_rev[key1][key2][key3][key4]['school_code']);
                                country_code=trim(json_school_code_rev[key1][key2][key3][key4]['country_code']);
                            }
                        }
                    }
                }
            }
            if((school_code==='')||(country_code==='')){
                alert('無此學校!');
                return false;
            }
        }
        if(school_code!==""){

            var url ='';
            var page=str_repeat('../',0)+'query.php';
            var arg ={
                'school_code':school_code,
                'type':'self'
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

    function block_ui(){
        $.blockUI({
            message:'<h2 class="fc_white0">處理中，請稍後 !</h2>',
            css: {
                top:'500px',
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

    <?php if(in_array($auth_sys_check_lv,array(1,3,14,16,22,99))):?>

        //初始化, 全校年級班級json
        var jsons_class_code_rev=<?php echo $json_class_code_rev;?>;

        //初始化, 帶領的班級陣列
        var arry_sess_class_code=[];

        //回填, 帶領的班級陣列
        <?php foreach($arrys_login_info as $arry_login_info):?>
        <?php
            if((isset($arry_login_info['arrys_class_code']))&&(!empty($arry_login_info['arrys_class_code']))):
                $sess_arrys_class_code=$arry_login_info['arrys_class_code'];
        ?>
                <?php foreach($sess_arrys_class_code as $inx=>$sess_arry_class_code):?>
                <?php
                    $sess_class_code=trim($sess_arry_class_code['class_code']);
                ?>
                    var sess_class_code='<?php echo $sess_class_code;?>';
                    arry_sess_class_code.push(sess_class_code);
                <?php endforeach;?>
            <?php endif;?>
        <?php endforeach;?>

    <?php endif;?>

    function sel_school(school_code){
    //學校下拉設置
        var url ='';
        var page=str_repeat('../',0)+'query.php';
        var arg ={
            'school_code':school_code,
            'type':'self'
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

        $.blockUI({
            message:'<h2 class="fc_white0">處理中，請稍後 !</h2>',
            css: {
                top:'500px',
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity:.8,
                color: '#437C85'
            }
        });

        go(url,'self');
    }

    function sel_grade(grade){
    //年級班級下拉設置
        var grade=grade;

        //附加年級提示
        oclassroom_title.text(grade+'年級');

        //清除班級選項
        child_classroom.find("li").remove();

        //附加班級選項
        for(key1 in jsons_class_code_rev){
            if(key1==='classroom'){
                var json_classroom_rev=jsons_class_code_rev[key1];
            }
            if(key1==='class_code'){
                var json_class_code_rev=jsons_class_code_rev[key1];;
            }
        }
        for(key2 in json_class_code_rev[grade]){
            class_code=trim(json_class_code_rev[grade][key2]);
            classroom=trim(json_classroom_rev[grade][key2]);

            if(in_array(class_code,arry_sess_class_code)){
            //帶領中的班級, 顏色區分
                child_classroom.append(
                    '<li><a href="javascript:void(0);" target="" rel="nofollow"><span id="'+class_code+'" grade="'+grade+'" classroom="'+classroom+'" style="color:#ffff00;">'+grade+'年'+classroom+'班(帶班)</span></a></li>'
                );
            }else{
                child_classroom.append(
                    '<li><a href="javascript:void(0);" target="" rel="nofollow"><span id="'+class_code+'" grade="'+grade+'" classroom="'+classroom+'">'+grade+'年'+classroom+'班</span></a></li>'
                );
            }

            document.getElementById(class_code).onclick=function(){

                var class_code=this.id;
                var grade=this.getAttribute('grade');
                var classroom=this.getAttribute('classroom');

                //附加年級提示
                if(in_array(class_code,arry_sess_class_code)){
                    oclassroom_title.html("<span style='color:#ffff00;'>"+grade+'年'+classroom+'班'+"</span>");
                }else{
                    oclassroom_title.html("<span>"+grade+'年'+classroom+'班'+"</span>");
                }

                //送出查詢
                var class_code=trim(this.id);
                q_form(class_code);
            }
        }

        return true;
    }

    function q_form(class_code){
    //年級班級快速查詢
        var url ='';
        var page=str_repeat('../',0)+'query.php';
        var arg ={
            'class_code':class_code,
            'type':'blank'
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

        $.blockUI({
            message:'<h2 class="fc_white0">處理中，請稍後 !</h2>',
            css: {
                top:'500px',
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity:.8,
                color: '#437C85'
            }
        });

        go(url,'IFC');
    }

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

    $(function(){
        <?php if(!empty($arrys_school_code_rev)):?>
            var arry_school_name=[];
            <?php foreach($arrys_school_code_rev as $key=>$arrys_school_code_rev1):?>
                <?php foreach($arrys_school_code_rev1 as $key=>$arrys_school_code_rev2):?>
                    <?php foreach($arrys_school_code_rev2 as $key=>$arrys_school_code_rev3):?>
                        <?php foreach($arrys_school_code_rev3 as $arry_school_code_rev):?>
                            var school_name=trim('<?php echo $arry_school_code_rev["school_name"];?>');
                            arry_school_name.push(school_name);
                        <?php endforeach;?>
                    <?php endforeach;?>
                <?php endforeach;?>
            <?php endforeach;?>
            $("#school_name").autocomplete({
                source: arry_school_name
            });
        <?php endif;?>

        <?php if(!empty($arrys_class_code_info)):?>
            var grade=parseInt(<?php echo $arrys_class_code_rev['grade'][1];?>);
            for(key1 in jsons_class_code_rev){
                if(key1==='class_code'){
                    var json_class_code_rev=jsons_class_code_rev[key1];;
                }
            }
            for(key2 in json_class_code_rev[grade]){
                class_code=trim(json_class_code_rev[grade][key2]);
                <?php if(isset($_SESSION['teacher_center']['class_code'])&&trim($_SESSION['teacher_center']['class_code'])!==''):?>
                    class_code=trim("<?php echo $_SESSION['teacher_center']['class_code'];?>");
                <?php endif;?>
                q_form(class_code);
            }
        <?php endif;?>

        //if(choose_identity_flag===1){
        //    alert('請先選擇身份!');
        //    choose_identity();
        //}

        //快速切換設置
        //fast_area_config('#fast_area',_top=0,_right=0);
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

