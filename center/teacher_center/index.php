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
        require_once(str_repeat("../",2).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/array/code',
                    APP_ROOT.'lib/php/string/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

//$_SESSION['uid']=2029;  //super
//$_SESSION['uid']=5029;  //t01
//$_SESSION['uid']=686;  //林良祉老師
//$_SESSION['uid']=4848;  //石老師
//$_SESSION['uid']=19837; //沒班級的老師
//$_SESSION['uid']=2033;  //主任
//$_SESSION['uid']=4657;  //主任該學校沒任何班級
//$_SESSION['uid']=4933;  //校長

//$_SESSION['uid']=4381;  //邱順明 老師多個班級
//$_SESSION['uid']=5184;  //六和高中主任

//if((int)$_SESSION['uid']===2029){
//    echo "<Pre>";
//    print_r($_SESSION);
//    echo "</Pre>";
//    die();
//}

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['center']['teacher_center']){
            $url=str_repeat("../",3).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        //初始化，承接變數
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

            //清除模組查詢資料
            foreach(auth_sys_arry_config() as $sys_name=>$mods_arry){
                foreach($mods_arry as $mod_name=>$mod_arry){
                    unset($_SESSION[$mod_name]);
                }
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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'index');
        }

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
    //sys_name  系統名稱

        //GET
        $_get_sys_ename=(isset($_GET[trim('sys_ename')]))?$_GET[trim('sys_ename')]:'';

        //SESSION
        if(isset($sess_login_info['uid'])){$sess_user_id=(int)$sess_login_info['uid'];}
        if(isset($sess_login_info['permission'])){$sess_permission=trim($sess_login_info['permission']);}
        if(isset($sess_login_info['school_code'])){$sess_school_code=trim($sess_login_info['school_code']);}
        if(isset($sess_login_info['responsibilities'])){
            $sess_responsibilities=(int)$sess_login_info['responsibilities'];
            $sess_class_code='';
            $sess_grade=0;
            $sess_classroom=0;
            if(in_array($auth_sys_check_lv,array(5,14,16,22,99,1,3,20,24))){
                $sess_class_code=(isset($sess_login_info['arrys_class_code'][0]['class_code']))?trim($sess_login_info['arrys_class_code'][0]['class_code']):'';
                $sess_grade=(isset($sess_login_info['arrys_class_code'][0]['grade']))?(int)$sess_login_info['arrys_class_code'][0]['grade']:0;
                $sess_classroom=(isset($sess_login_info['arrys_class_code'][0]['classroom']))?(int)$sess_login_info['arrys_class_code'][0]['classroom']:0;
            }
        }

        if(isset($_SESSION['i_a_school'])){
            $sess_i_a_school=trim($_SESSION['i_a_school']);
            $sess_i_a_school=str_replace(",","','",$sess_i_a_school);
        }else{
            $sess_i_a_school='';
        }

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //sys_name  系統名稱

        $auth_arry=auth_sys_arry_config();
        $sys_names=array_keys($auth_arry);

        $arry_err=array();
        if($_get_sys_ename!==''){
            if(!in_array($_get_sys_ename,$sys_names)){
                $arry_err[]='系統名稱,不在允許清單裡!';
            }
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
    //資料庫
    //---------------------------------------------------

        //建立連線 user
        $conn_user=conn($db_type='mysql',$arry_conn_user);

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
            if(in_array($auth_sys_check_lv,array(1,3,14,16,22,99,20,24))){
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
                        $arrys_class_code_rev['grade'][]='請選擇年級';
                        $arrys_class_code_rev['classroom'][]=array('請選擇班級');

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

        //sys顯示設定
        $_sys_state="";
        switch(empty($auth_arry)){
            case true:
                $_sys_state='off';
            break;

            default:
                $_sys_state='on';
            break;
        }

        //內容顯示設定
        if($choose_identity_flag){
            $_mod_state='choose_identity';
        }else{
        //mod顯示設定
            $_mod_state="";
            $_get_sys_ename=trim($_get_sys_ename);
            switch($_get_sys_ename){
                case '':
                    $_mod_state='off';
                break;

                default:
                    $_mod_state='on';
                break;
            }
        }
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
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../lib/jquery/ui/code.css" media="all" />
    <script type="text/javascript" src="../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../lib/jquery/plugin/code.js"></script>
    <script type="text/javascript" src="../../lib/jquery/ui/code.js"></script>

    <script type="text/javascript" src="../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../lib/js/array/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="inc/code.css" media="all" />
    <script type="text/javascript" src="inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="css/def.css" media="all" />

    <style>
        /* 容器微調 */
        #container, #content, #teacher_center_div{
            width:760px;
        }
        h2{
            -webkit-box-shadow: 3px 3px 5px #f3d42e;
            -moz-box-shadow: 3px 3px 5px #f3d42e;
            box-shadow: 5px 2px 4px #000000;
            height: 20px;
        }
    </style>
</Head>

<Body>

<!-- 容器區塊 開始 -->
<div id="container">
    <!-- 內容區塊 開始 -->
    <div id="content">
        <table id="teacher_center_div" align="center" cellpadding="0" cellspacing="0" border="0"/>
            <tr>
                <td>
                    <table align="center" cellpadding="10" cellspacing="0" border="0" width="100%"
                    style="border-top:0px dashed #868686;border-bottom:0px dashed #868686;background-color:#e1e1e1;font-size:12pt;"/>
                        <tr>
                            <td width="60px" valign="middle">
                                <?php if(in_array($auth_sys_check_lv,array(1,3,14,16,22,99,20,24))):?>
                                    <div id="sys_list" style=""></div>
                                <?php endif;?>
                            </td>
                            <td valign="middle">
                                <!-- 系統功能 開始 -->
                                <?php if($_sys_state==="on"):?>
                                    <?php foreach($auth_arry as $sys_ename=>$mod_ename) :?>
                                    <?php
                                    //---------------------------------------
                                    //選項
                                    //---------------------------------------
                                    //系統權限名稱陣列
                                    //$auth_sys_name_arry=auth_sys_name_arry();
                                    //
                                    //系統權限圖片陣列
                                    //$auth_sys_img_arry=auth_sys_img_arry();
                                    //
                                    //系統權限連結陣列
                                    //$auth_sys_url_arry=auth_sys_url_arry();
                                    //
                                    //系統權限連結框架陣列
                                    //$auth_sys_target_arry=auth_sys_target_arry();
                                    //---------------------------------------
                                    //sys_cname     系統中文名稱

                                        $sys_cname=trim($auth_sys_name_arry[$sys_ename]);

                                        //sys顯示設定
                                        $_sys_html="";
                                        switch(trim($_get_sys_ename)){
                                            case $sys_ename:
                                                $_sys_html="<span class='fc_blue0'>{$sys_cname}</span>";
                                            break;

                                            default:
                                                $_sys_html="<span class=''>{$sys_cname}</span>";
                                            break;
                                        }

                                        //mod連結
                                        $url ="";
                                        $page="index.php";
                                        $arg =array(
                                            'sys_ename'  =>addslashes($sys_ename)
                                        );
                                        $arg=http_build_query($arg);
                                        $url=$page."?".$arg;
                                    ?>
                                        <span style="margin:0 15px;display:none;">
                                            <a href="<?php echo $url;?>"><?php echo $_sys_html;?></a>
                                        </span>
                                    <?php endforeach ;?>
                                <?php endif;?>
                                <!-- 系統功能 結束 -->

                                <!-- 查詢表單列 開始 -->
                                <div style="position:relative;float:left;width:100%;font-size:12pt;">
                                    <?php if(in_array($auth_sys_check_lv,array(1,3,14,16,22,99,20,24))):?>
                                        <?php if($auth_sys_check_lv===99):?>
                                            <!-- 查詢學校 -->
                                            <span>
                                                學校名稱

                                                <input type="text" id="school_name"
                                                value="<?php //if((isset($sess_school_code))&&(trim($sess_school_code)!==''))echo $sess_school_code;?>" size="30" maxlength="30"
                                                tabindex="1" class="form_text" style="width:80px;font-size:12pt;">

                                                <input type="button" value="送出" class="ibtn_gr3020" style="margin:10px 0px;font-size:12pt;"
                                                tabindex="2" onmouseover="this.style.cursor='pointer'" onclick="sel_school();void(0);">
                                            </span>
                                            <select id="school_code" name="school_code" style="width:150px;font-size:12pt;" onchange="sel_school2(this.options[this.options.selectedIndex].value);void(0);">
                                                <?php //if(!isset($sess_school_code)):?>
                                                    <option value="" selected disabled>請選擇學校
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
                                                    <!-- <option value="" style="background:#ffff00;" class='fc_blue0'><?php echo htmlspecialchars($country_code_name);?> -->
                                                    <?php foreach($arrys_school_code_rev1 as $rs_region_name=>$arrys_school_code_rev2):?>
                                                    <?php
                                                        $rs_region_name=trim($rs_region_name);
                                                    ?>
                                                        <option value="" style="background:#ffff00;" class='fc_blue0' disabled>&nbsp;&nbsp;
                                                        <?php echo htmlspecialchars($rs_region_name);?>
                                                            <?php foreach($arrys_school_code_rev2 as $num_school_name=>$arrys_school_code_rev3):?>
                                                            <?php
                                                                $num_school_name=(int)($num_school_name);
                                                            ?>
                                                            <option value="" style="background:#ffffff;" class='fc_red0' disabled>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
                                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                    <?php echo htmlspecialchars($rs_school_name);?>
                                                                <?php else:?>
                                                                    <option value="<?php echo htmlspecialchars($rs_school_code);?>" country_code="<?php echo addslashes($rs_country_code);?>">
                                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                    <?php echo htmlspecialchars($rs_school_name);?>
                                                                <?php endif;?>
                                                                <?php endforeach;?>
                                                        <?php endforeach;?>
                                                    <?php endforeach;?>
                                                <?php endforeach;?>
                                            </select>
                                        <?php endif;?>

                                        <?php if(!empty($arrys_class_code_info)):?>
                                        <!-- 查詢年級 -->
                                        <select id="grade" style="width:125px;font-size:12pt;" onchange="javascript:sel_grade(this.options[this.options.selectedIndex].value);void(0);">
                                            <?php foreach($arrys_class_code_rev['grade'] as $inx=>$grade):?>
                                                <?php if($inx===0):?>
                                                <?php
                                                    $grade=trim($grade);
                                                ?>
                                                    <option value="<?php echo htmlspecialchars($grade);?>" selected><?php echo htmlspecialchars($grade);?>
                                                <?php else:?>
                                                <?php
                                                    $grade=(int)($grade);
                                                ?>
                                                    <option value="<?php echo $grade;?>"><?php echo $grade;?>年
                                                <?php endif;?>
                                            <?php endforeach;?>
                                        </select>

                                        <!-- 查詢班級 -->
                                        <select id="classroom" style="width:125px;font-size:12pt;" onchange="void(0);">
                                            <?php foreach($arrys_class_code_rev['classroom'] as $inx=>$classroom):?>
                                                <?php if($inx===0):?>
                                                <?php
                                                    $classroom=trim($classroom[0]);
                                                ?>
                                                    <option value="<?php echo htmlspecialchars($classroom);?>" selected><?php echo htmlspecialchars($classroom);?>
                                                <?php endif;?>
                                            <?php endforeach;?>
                                        </select>
                                        <?php else:?>
                                            <span>您未選學校或學校沒有班級 !</span>
                                        <?php endif;?>
                                    <?php endif?>
                                    <!-- 查詢表單列 結束 -->
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <?php if($_mod_state==='on'):?>
            <tr>
                <td align="center">
                    <h2 style="background-color:#ebebeb;" onmouseover="this.style.cursor='pointer'" onclick="auth_sys_diff_lv(1);"
                    >常用功能</h2>
                </td>
            </tr>
            <?php endif;?>

            <tr style="display:none;" class="auth_sys_diff_lv_1">
                <!-- 內容 開始 -->
                <?php if($_mod_state==='choose_identity'):?>

                    <!-- 選擇身份 -->
                    <td align="center" valign="middle" height="250px">
                        <img width="13" height="13" src="../../img/icon/red.jpg" border="0">
                        <span class="fc_blue0" onclick="choose_identity();" onmouseover="this.style.cursor='pointer';">
                            請按此選擇身份
                        </span>
                    </td>

                <?php elseif($_mod_state==='off'):?>

                    <!-- 不顯示mod -->
                    <td align="center" valign="middle" height="250px">
                        <img width="13" height="13" src="../../img/icon/red.jpg" border="0">
                        <span class="fc_blue0">
                            請選擇上列功能列表
                        </span>
                    </td>

                <?php elseif($_mod_state==='on'):?>

                    <!-- 顯示mod -->
                    <td align="center" valign="top" height="250px">
                        <?php foreach($auth_arry[$_get_sys_ename] as $mod_ename=>$auth_ename) :?>
                        <?php
                        //---------------------------------------
                        //選項
                        //---------------------------------------
                        //系統權限名稱陣列
                        //$auth_sys_name_arry=auth_sys_name_arry();
                        //
                        //系統權限圖片陣列
                        //$auth_sys_img_arry=auth_sys_img_arry();
                        //
                        //系統權限連結陣列
                        //$auth_sys_url_arry=auth_sys_url_arry();
                        //
                        //系統權限連結框架陣列
                        //$auth_sys_target_arry=auth_sys_target_arry();
                        //---------------------------------------
                        //mod_cname     模組中文名稱
                        //mod_img       模組圖片路徑
                        //mod_url       模組連結路徑
                        //mod_target    模組連結框架

                            $mod_cname  =trim($auth_sys_name_arry[$mod_ename]);
                            $mod_img    =trim($auth_sys_img_arry[$mod_ename]);
                            $mod_url    =trim($auth_sys_url_arry[$mod_ename]);
                            $mod_target =trim($auth_sys_target_arry[$mod_ename]);

                            //mod權限設定
                            $_mod_auth=0;
                            $_mod_auth=$auth_ename['access'];

                            switch((int)$_mod_auth){
                                case 0:
                                    $_mod_auth="javascript:alert('尚未開放!');void(0);";
                                break;

                                default:
                                    $_mod_auth="{$mod_url}";
                                break;
                            }

                            //mod難易層級
                            $auth_sys_diff_lv=(int)auth_sys_diff_lv($mod_ename);
                            if($auth_sys_diff_lv!==1)continue;
                        ?>
                            <?php if((isset($sess_school_code))&&(trim($sess_school_code)!=='')):?>
                                <?php if(in_array($auth_sys_check_lv,array(99))&&($sess_class_code!=='')&&($sess_grade!==0)&&($sess_classroom!==0)):?>
                                    <table width="140px" cellpadding="0" cellspacing="0" border="1" align="left" onclick="block_ui();location.href='<?php echo htmlspecialchars($_mod_auth);?>';void(0);"
                                    style="position:relative;left:5px;float:left;margin:0 5px;margin-top:20px;background-color:#c6ecff;border-color:#c6ecff;"
                                    onmouseover='this.style.cursor="pointer"'/>
                                        <tr align="center">
                                            <td height="120px">
                                                <span class='fc_gray0 fsize_12'><?php echo htmlspecialchars($mod_cname);?></span>
                                            </td>
                                        </tr>
                                    </table>
                                <?php endif;?>

                                <?php if(!in_array($auth_sys_check_lv,array(99))):?>
                                    <table width="140px" cellpadding="0" cellspacing="0" border="1" align="left" onclick="block_ui();location.href='<?php echo htmlspecialchars($_mod_auth);?>';void(0);"
                                    style="position:relative;left:5px;float:left;margin:0 5px;margin-top:20px;background-color:#c6ecff;border-color:#c6ecff;"
                                    onmouseover='this.style.cursor="pointer"'/>
                                        <tr align="center">
                                            <td height="120px">
                                                <span class='fc_gray0 fsize_12'><?php echo htmlspecialchars($mod_cname);?></span>
                                            </td>
                                        </tr>
                                    </table>
                                <?php endif;?>
                            <?php else:?>
                                <!-- <table width="140px" cellpadding="0" cellspacing="0" border="1" align="left" onclick="alert('您未選學校或學校沒有班級 !');void(0);';"
                                style="position:relative;left:5px;float:left;margin:0 5px;margin-top:20px;background-color:#c6ecff;border-color:#c6ecff;"
                                onmouseover='this.style.cursor="pointer"'/>
                                    <tr align="center">
                                        <td height="120px">
                                            <span class='fc_gray0'><?php echo htmlspecialchars($mod_cname);?></span>
                                        </td>
                                    </tr>
                                </table> -->
                            <?php endif;?>
                        <?php endforeach;?>
                    </td>
                <?php endif;?>
                <!-- 內容 結束 -->
            </tr>

            <?php if($_mod_state==='on'):?>
            <tr>
                <td align="center">
                    <h2 style="background-color:#ebebeb;" onmouseover="this.style.cursor='pointer'" onclick="auth_sys_diff_lv(2);"
                    >進階功能</h2>
                </td>
            </tr>
            <?php endif;?>

            <tr style="" class="auth_sys_diff_lv_2">
                <!-- 內容 開始 -->
                <?php if($_mod_state==='choose_identity'):?>

                    <!-- 選擇身份 -->
                    <td align="center" valign="middle" height="250px">
                        <img width="13" height="13" src="../../img/icon/red.jpg" border="0">
                        <span class="fc_blue0" onclick="choose_identity();" onmouseover="this.style.cursor='pointer';">
                            請按此選擇身份
                        </span>
                    </td>

                <?php elseif($_mod_state==='off'):?>

                    <!-- 不顯示mod -->
                    <td align="center" valign="middle" height="250px">
                        <img width="13" height="13" src="../../img/icon/red.jpg" border="0">
                        <span class="fc_blue0">
                            請選擇上列功能列表
                        </span>
                    </td>

                <?php elseif($_mod_state==='on'):?>

                    <!-- 顯示mod -->
                    <td align="center" valign="top" height="250px">
                        <?php foreach($auth_arry[$_get_sys_ename] as $mod_ename=>$auth_ename) :?>
                        <?php
                        //---------------------------------------
                        //選項
                        //---------------------------------------
                        //系統權限名稱陣列
                        //$auth_sys_name_arry=auth_sys_name_arry();
                        //
                        //系統權限圖片陣列
                        //$auth_sys_img_arry=auth_sys_img_arry();
                        //
                        //系統權限連結陣列
                        //$auth_sys_url_arry=auth_sys_url_arry();
                        //
                        //系統權限連結框架陣列
                        //$auth_sys_target_arry=auth_sys_target_arry();
                        //---------------------------------------
                        //mod_cname     模組中文名稱
                        //mod_img       模組圖片路徑
                        //mod_url       模組連結路徑
                        //mod_target    模組連結框架

                            $mod_cname  =trim($auth_sys_name_arry[$mod_ename]);
                            $mod_img    =trim($auth_sys_img_arry[$mod_ename]);
                            $mod_url    =trim($auth_sys_url_arry[$mod_ename]);
                            $mod_target =trim($auth_sys_target_arry[$mod_ename]);

                            //mod權限設定
                            $_mod_auth=0;
                            $_mod_auth=$auth_ename['access'];

                            switch((int)$_mod_auth){
                                case 0:
                                    $_mod_auth="javascript:alert('尚未開放!');void(0);";
                                break;

                                default:
                                    $_mod_auth="{$mod_url}";
                                break;
                            }

                            //mod難易層級
                            $auth_sys_diff_lv=(int)auth_sys_diff_lv($mod_ename);
                            if($auth_sys_diff_lv!==2)continue;
                        ?>
                            <?php if((isset($sess_school_code))&&(trim($sess_school_code)!=='')):?>
                                <?php if(in_array($auth_sys_check_lv,array(99))&&($sess_class_code!=='')&&($sess_grade!==0)&&($sess_classroom!==0)):?>
                                    <table width="140px" cellpadding="0" cellspacing="0" border="1" align="left" onclick="block_ui();location.href='<?php echo htmlspecialchars($_mod_auth);?>';void(0);"
                                    style="position:relative;left:5px;float:left;margin:0 5px;margin-top:20px;background-color:#c6ecff;border-color:#c6ecff;"
                                    onmouseover='this.style.cursor="pointer"'/>
                                        <tr align="center">
                                            <td height="120px">
                                                <span class='fc_gray0 fsize_12'><?php echo htmlspecialchars($mod_cname);?></span>
                                            </td>
                                        </tr>
                                    </table>
                                <?php endif;?>

                                <?php if(!in_array($auth_sys_check_lv,array(99))):?>
                                    <table width="140px" cellpadding="0" cellspacing="0" border="1" align="left" onclick="block_ui();location.href='<?php echo htmlspecialchars($_mod_auth);?>';void(0);"
                                    style="position:relative;left:5px;float:left;margin:0 5px;margin-top:20px;background-color:#c6ecff;border-color:#c6ecff;"
                                    onmouseover='this.style.cursor="pointer"'/>
                                        <tr align="center">
                                            <td height="120px">
                                                <span class='fc_gray0 fsize_12'><?php echo htmlspecialchars($mod_cname);?></span>
                                            </td>
                                        </tr>
                                    </table>
                                <?php endif;?>
                            <?php else:?>
                                <!-- <table width="140px" cellpadding="0" cellspacing="0" border="1" align="left" onclick="alert('您未選學校或學校沒有班級 !');void(0);';"
                                style="position:relative;left:5px;float:left;margin:0 5px;margin-top:20px;background-color:#c6ecff;border-color:#c6ecff;"
                                onmouseover='this.style.cursor="pointer"'/>
                                    <tr align="center">
                                        <td height="120px">
                                            <span class='fc_gray0'><?php echo htmlspecialchars($mod_cname);?></span>
                                        </td>
                                    </tr>
                                </table> -->
                            <?php endif;?>
                        <?php endforeach;?>
                    </td>
                <?php endif;?>
                <!-- 內容 結束 -->
            </tr>

        </table>
    </div>
    <!-- 內容區塊 結束 -->

    <!-- google分析 開始 -->
    <?php echo google_analysis($allow=true);?>
    <!-- google分析 結束 -->

</div>
<!-- 容器區塊 結束 -->

<!-- 快速切換區塊 開始 -->
<?php if($auth_sys_check_lv!==99):?>
    <?php echo fast_area($rd=0);?>
<?php endif;?>
<!-- 快速切換區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //參數
    var sys_ename           ='<?php echo trim($_get_sys_ename);?>';
    var sess_class_code     ='<?php echo trim($sess_class_code);?>';
    var sess_grade          =<?php echo (int)$sess_grade;?>;
    var sess_classroom      =<?php echo (int)$sess_classroom;?>;
    var json_school_code_rev=<?php echo $json_school_code_rev;?>;

    //物件
    var ograde=document.getElementById('grade');
    var oclassroom=document.getElementById('classroom');

    function init_sel(ograde,oclassroom){
    //初始化, 下拉選單

        for(var i=0;i<ograde.options.length;i++){
            var ograde_opt=ograde.options[i];
            var ograde_opt_val=parseInt(ograde_opt.value);
            if(ograde_opt_val===sess_grade){
                ograde_opt.selected=true;
                sel_grade(ograde_opt_val);
                break;
            }
        }

        for(var j=0;j<oclassroom.options.length;j++){
            var oclassroom_opt=oclassroom.options[j];
            var oclassroom_opt_val=trim(oclassroom_opt.value);
            if(oclassroom_opt_val===sess_class_code){
                oclassroom_opt.selected=true;
                break;
            }
        }
    }

    $('#school_name').keypress(function(e){
        if(e.which==13){
            sel_school();
        }
    });

    function sel_school(){
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
                'sys_ename'  :sys_ename
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

    function sel_school2(school_code){
    //學校下拉設置

        var url ='';
        var page=str_repeat('../',0)+'query.php';
        var arg ={
            'school_code':school_code,
            'sys_ename'  :sys_ename
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

    function sel_grade(grade){
    //年級班級下拉設置

        var grade=grade;

        //清除班級選項
        $(oclassroom).find('option').remove();
        $(oclassroom).append(
            '<option value="">請選擇班級'
        );

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
                $(oclassroom).append(
                    '<option value="'+class_code+'">'+classroom+'班'
                );
            }else{
                $(oclassroom).append(
                    '<option value="'+class_code+'">'+classroom+'班'
                );
            }

            $(oclassroom).change(function(){
                //送出查詢
                var class_code=$(this).val();
                q_form(class_code);
            });
        }

        return true;
    }

    function q_form(class_code){
    //年級班級快速查詢

        var url ='';
        var page=str_repeat('../',0)+'query.php';
        var arg ={
            'class_code':class_code,
            'sys_ename' :sys_ename
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

    <?php if(in_array($auth_sys_check_lv,array(1,3,14,16,22,99,20,24))):?>

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

    function auth_sys_diff_lv(lv){
        var lv=parseInt(lv);
        if(lv===1){
            $('.auth_sys_diff_lv_1').show(500);
            $('.auth_sys_diff_lv_2').hide(0);
        }
        if(lv===2){
            $('.auth_sys_diff_lv_2').show(500);
            $('.auth_sys_diff_lv_1').hide(0);
        }
    }

    $(function(){

        $('.auth_sys_diff_lv_1').show(500);

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

        //快速切換設置
        fast_area_config('#fast_area',_top=0,_right=0);

        try{
            <?php if(in_array($auth_sys_check_lv,array(1,3,14,16,22,99,20,24))):?>
                //初始化, 下拉選單
                init_sel(ograde,oclassroom);
            <?php endif;?>
        }catch(e){
        }
    });

</script>

<script type="text/javascript" src="../../inc/external/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//呼叫使用者頁面紀錄
//-------------------------------------------------------
    user_page_log(rd=2);
</script>
</Body>
</Html>