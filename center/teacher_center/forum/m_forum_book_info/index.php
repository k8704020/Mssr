<?php
//-------------------------------------------------------
//教師中心
//-------------------------------------------------------


//-------------------------------------------------------

//設定頁面語系
    header("Content-Type: text/html; charset=UTF-8");

//設定文字內部編碼
    mb_internal_encoding("UTF-8");

//設定台灣時區
    date_default_timezone_set('Asia/Taipei');

//--------------------------------------------------------

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




//=========================================================================

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
            if(in_array($auth_sys_check_lv,array(1,3,5,12,14,16,22,99))){
                if(!empty($sess_login_info)){

                    $sess_user_id=(int)$sess_user_id;
                    $sql="";

                    if($auth_sys_check_lv!==99){
                        $sql="
                            SELECT
                                `semester`.`semester_code`,
                                `semester`.`semester_year`,
                                `semester`.`semester_term`,
                                `class`.`class_code`,
                                `class`.`grade`,
                                `class`.`classroom`,
                                `class`.`class_category`

                                #`class_name`.`class_name`
                            FROM `teacher`
                                INNER JOIN `class` ON
                                `teacher`.`class_code`=`class`.`class_code`
                                INNER JOIN `semester` ON
                                `class`.`semester_code`=`semester`.`semester_code`

                                #INNER JOIN `class_name` ON
                                #`class`.`class_category`=`class_name`.`class_category`
                            WHERE 1=1
                                AND `teacher`.`uid`={$sess_user_id}
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
                                    `class`.`class_category`
                                FROM `class`
                                    INNER JOIN `semester` ON
                                    `class`.`semester_code`=`semester`.`semester_code`
                                WHERE 1=1
                                    AND `semester`.`school_code`='{$sess_school_code}'
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
                                $arrys_has_leader_class_code_info[$rs_semester_code][$inx]['grade']     =$rs_grade;
                                $arrys_has_leader_class_code_info[$rs_semester_code][$inx]['class_name']=$rs_class_name;
                                $arrys_has_leader_class_code_info[$rs_semester_code][$inx]['classroom'] =$rs_classroom;
                                $arrys_has_leader_class_code_info[$rs_semester_code][$inx]['class_code']=$rs_class_code;
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

//--------------------------------------------------------------






function call_alert(){
    $msg='
        <script>
            alert("您沒有權限進入，請洽詢明日星球團隊人員");
            history.back(-1);
        </script>
    ';
    return $msg;
}
//清除並停用BUFFER
        @ob_end_clean();

//建立連線 user
    $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
    $conn_user=conn($db_type='mysql',$arry_conn_user);
    $class_code = $_SESSION['class_code'];

    $arr_classCode = array('test_2014_2_1_1','gcp_2014_2_5_5','gcp_2014_2_5_1','gcp_2014_2_5_2');
    if(!in_array($class_code,$arr_classCode)){
        $msg = call_alert();
        die($msg);
    }


//學生陣列
    $users=arrys_users($conn_user,$class_code,$date=date("Y-m-d"),$arry_conn_user);
    $t_uid= $_SESSION['uid'];
    $users .=",'".$t_uid."'";

     $sql="
        SELECT
            end,
            start
        FROM
            user.class join user.semester on  user.`semester`.`semester_code`  =   user.class.semester_code
        where  user.class.class_code = '$class_code'
    ";
    $arr_date=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

   $start= $arr_date[0]['start'];
   $end = $arr_date[0]['end'];

                $sql="
                    SELECT
                        `sqry`.`book_sid`,
                        IFNULL((
                            SELECT
                                COUNT(*)
                            FROM `mssr_article_book_rev`
                                LEFT JOIN `mssr_forum_article` ON
                                `mssr_article_book_rev`.`article_id`=`mssr_forum_article`.`article_id`
                            WHERE 1=1
                                AND `mssr_forum_article`.`user_id` IN ($users)
                                AND `mssr_article_book_rev`.`book_sid`=`sqry`.`book_sid`
                            GROUP BY `mssr_article_book_rev`.`book_sid`
                        ),0) AS 'fawon',

                        IFNULL((
                            SELECT
                                COUNT(*)
                            FROM `mssr_article_reply_book_rev`
                                LEFT JOIN `mssr_forum_article_reply` ON
                                `mssr_article_reply_book_rev`.`reply_id`=`mssr_forum_article_reply`.`reply_id`
                            WHERE 1=1
                                AND `mssr_forum_article_reply`.`user_id` IN ($users)
                                AND `mssr_article_reply_book_rev`.`book_sid`=`sqry`.`book_sid`
                            GROUP BY `mssr_article_reply_book_rev`.`book_sid`
                        ),0) AS 'hawon',

                        (IFNULL((
                            SELECT
                                COUNT(*)
                            FROM `mssr_article_book_rev`
                                LEFT JOIN `mssr_forum_article` ON
                                `mssr_article_book_rev`.`article_id`=`mssr_forum_article`.`article_id`
                            WHERE 1=1
                                AND `mssr_forum_article`.`user_id` IN ($users)
                                AND `mssr_article_book_rev`.`book_sid`=`sqry`.`book_sid`
                            GROUP BY `mssr_article_book_rev`.`book_sid`
                        ),0)+IFNULL((
                            SELECT
                                COUNT(*)
                            FROM `mssr_article_reply_book_rev`
                                LEFT JOIN `mssr_forum_article_reply` ON
                                `mssr_article_reply_book_rev`.`reply_id`=`mssr_forum_article_reply`.`reply_id`
                            WHERE 1=1
                                AND `mssr_forum_article_reply`.`user_id` IN ($users)
                                AND `mssr_article_reply_book_rev`.`book_sid`=`sqry`.`book_sid`
                            GROUP BY `mssr_article_reply_book_rev`.`book_sid`
                        ),0)) as 'ta'
                    FROM (
                        SELECT
                            `mssr_article_book_rev`.`book_sid`
                        FROM `mssr_article_book_rev`
                            LEFT JOIN `mssr_forum_article` ON
                            `mssr_article_book_rev`.`article_id`=`mssr_forum_article`.`article_id`
                        WHERE 1=1
                            AND `mssr_forum_article`.`user_id` IN ($users)

                        GROUP BY `book_sid`

                            UNION

                        SELECT
                            `book_sid`
                        FROM `mssr_article_reply_book_rev`
                            LEFT JOIN `mssr_forum_article_reply` ON
                            `mssr_article_reply_book_rev`.`reply_id`=`mssr_forum_article_reply`.`reply_id`
                        WHERE 1=1
                            AND `mssr_forum_article_reply`.`user_id` IN ($users)

                        GROUP BY `book_sid`
                    ) AS `sqry`
                    WHERE 1=1
                    order by ta desc
                    limit 180

                ";
                // echo '<pre>';
                // print_r($sql);
                // echo '</pre>';
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                $arrys_result_pages=count($db_results);

                //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=$arrys_result_pages;    //資料總筆數
        $psize =20;                 //單頁筆數,預設10筆
        $pnos  =0;                  //分頁筆數
        $pinx  =1;                  //目前分頁索引,預設1
        $sinx  =0;                  //值域起始值
        $einx  =0;                  //值域終止值

        if(isset($_GET['psize'])){
            $psize=(int)$_GET['psize'];
            if($psize===0){
                $psize=10;
            }
        }
        if(isset($_GET['pinx'])){
            $pinx=(int)$_GET['pinx'];
            if($pinx===0){
                $pinx=1;
            }
        }

        $pnos  =ceil($numrow/$psize);
        $pinx  =($pinx>$pnos)?$pnos:$pinx;

        $sinx  =(($pinx-1)*$psize)+1;
        $einx  =(($pinx)*$psize);
        $einx  =($einx>$numrow)?$numrow:$einx;


    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        if($numrow!==0){
            $arrys_chunk =array_chunk($db_results,$psize);
            $db_results=$arrys_chunk[$pinx-1];
        }else{

        }


               foreach($db_results as $inxs=>$db_result){

                extract($db_result, EXTR_PREFIX_ALL, "rs");

                $rs_book_sid  = $rs_book_sid;
                $rs_fawon     = $rs_fawon;
                $rs_hawon     = $rs_hawon;
//討論人數 發文
                    $sql="
                            SELECT
                                 `mssr_forum_article`.`user_id`,
                                 `mssr_article_book_rev`.`book_sid`
                            FROM `mssr_article_book_rev`
                                LEFT JOIN `mssr_forum_article` ON
                                `mssr_article_book_rev`.`article_id`=`mssr_forum_article`.`article_id`
                            WHERE 1=1
                                AND `mssr_forum_article`.`user_id` IN ($users)
                                AND `mssr_article_book_rev`.`book_sid`= '$rs_book_sid'
                    ";


                    $arr_fawonPeoples = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        $arry_user=array();
                        foreach($arr_fawonPeoples as $inx=>$arr_fawonPeople){
                        extract($arr_fawonPeople, EXTR_PREFIX_ALL,"rs");
                            $rs_user_id = $rs_user_id;

                            if(!in_array($rs_user_id,$arry_user)){
                                $arry_user[] = $rs_user_id;
                            }
                        }


    //討論人數 回文
                    $sql="
                            SELECT
                                `mssr_forum_article_reply`.`user_id`
                            FROM `mssr_article_reply_book_rev`
                                LEFT JOIN `mssr_forum_article_reply` ON
                                `mssr_article_reply_book_rev`.`reply_id`=`mssr_forum_article_reply`.`reply_id`
                            WHERE 1=1
                                AND `mssr_forum_article_reply`.`user_id` IN ($users)
                                AND `mssr_article_reply_book_rev`.`book_sid`='$rs_book_sid'
                     ";
                    $arr_hawonPeoples = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

                    foreach($arr_hawonPeoples as $inx=>$arr_hawonPeople){
                        extract($arr_hawonPeoples, EXTR_PREFIX_ALL,"rs");
                        $rs_user_id = $rs_user_id;

                        if(!in_array($rs_user_id,$arry_user)){
                            $arry_user[] = $rs_user_id;
                        }
                     }

                    $db_results[$inxs]['cno']  = count($arry_user);
}




                    $arr_final = array();

                    foreach($db_results as $inx =>$db_result){
                        extract($db_result, EXTR_PREFIX_ALL, "rs");

                        $rs_book_sid  = $rs_book_sid;
                        $rs_fawon     = $rs_fawon;
                        $rs_hawon     = $rs_hawon;
                        $rs_cno       = $rs_cno;
                        $total        = $rs_cno+$rs_hawon+$rs_fawon;

                        $arr_final[$rs_book_sid] = $total;
                    }
                    arsort($arr_final);




                    $new_final = array();
                    foreach($arr_final as $inx =>$db_result){

                        extract($arr_final, EXTR_PREFIX_ALL, "rs");
                        $rs_book_sid  = $rs_book_sid;
                        $rs_fawon     = $rs_fawon;
                        $rs_hawon     = $rs_hawon;
                        $rs_cno       = $rs_cno;

                        foreach($db_results as $a =>$b){
                            if($inx == $b['book_sid']){

                                $new_final[$inx]['rs_book_sid'] = $b['book_sid'];
                                $new_final[$inx]['rs_fawon']    = $b['fawon'];
                                $new_final[$inx]['rs_hawon']    = $b['hawon'];
                                $new_final[$inx]['rs_cno']      = $b['cno'];
                            }
                        }
                    }



?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title></Title>
    <script type="text/javascript" src=""></script>
    <script  src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <link rel="stylesheet" href=""/>

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


      <script>

	var psize=<?php echo (int)$psize;?>;
    var pinx =<?php echo (int)$pinx;?>;

    window.onload=function(){

        //分頁列
        var cid         ="page";                        //容器id
        var numrow      =<?php echo (int)$numrow;?>;    //資料總筆數
        var psize       =<?php echo (int)$psize ;?>;    //單頁筆數,預設10筆
        var pnos        =<?php echo (int)$pnos  ;?>;    //分頁筆數
        var pinx        =<?php echo (int)$pinx  ;?>;    //目前分頁索引,預設1
        var sinx        =<?php echo (int)$sinx  ;?>;    //值域起始值
        var einx        =<?php echo (int)$einx  ;?>;    //值域終止值
        var list_size   =5;                             //分頁列顯示筆數,5
        var url_args    ={};                            //連結資訊
        url_args={
            'pinx_name' :'pinx',
            'psize_name':'psize',
            'page_name' :'index.php',
            'page_args' :{
                'user_id' :<?php echo (int)$user_id;?>
            }
        }
        var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
    }


        $(document).ready(function() {
        $('.forum_sessId').click(function(){
            $('.elseFriends').show();
                $('#close').click(function(){
                    $('.elseFriends').hide();
                    $('.remove').remove();
                })
        });
    });
</script>
</head>
<style>
/*
-------------------------------------------------------
table
-------------------------------------------------------
    .table_style0
    .table_style1
    .table_style2
    .table_style3
*/
    body{
        font-family:ProximaNovaRgRegular,"Helvetica Neue",Arial,sans-serif;
        margin:0 auto 0;
        color:#333;
        background-image:url(../images/bg_content.gif);
    }

    /* 表格樣式 */
    .mod_data_tbl_outline {
        border-radius: 5px;
        letter-spacing: 1px;
        border: 1px solid #87CDDC;
        width:950px;
        margin: 0px auto;
    }
    .table_style0{
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        background-color: #fff;
        position:relative;
        color: #333;
        border-collapse:collapse;
        border: 0px solid #fff;

        line-height:35px;
    }
    .table_style0 td{
        border-top: 0px solid #c0c0c0;
        border-right: 0px solid #fff;
        border-left: 0px solid #fff;
        border-bottom: 0px solid #c0c0c0;
    }
    .table_style0 .b_line{
        border-top: 1px solid #3571d4;
    }
    .table_style0 .gr_dashed{
        border-bottom: 1px dashed #c0c0c0;
    }

    .table_style1{
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        background-color: #fff;
        position:relative;
        color: #4e4e4e;
        border-collapse:collapse;
        border: 0px solid #fff;
        border-bottom: 1px solid #c0c0c0;
    }
    .table_style1 td{
        border-top: 0px solid #c0c0c0;
        border-right: 0px solid #fff;
        border-left: 0px solid #fff;
        border-bottom: 1px solid #c0c0c0;
    }

    .table_style2{
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        background-color: #fff;
        position:relative;
        color: #4e4e4e;
        border-collapse:collapse;
        border: 1px solid #c0c0c0;
        border-bottom: 1px solid #c0c0c0;
    }
    .table_style2 td{
        border-top: 0px solid #c0c0c0;
        border-right: 0px solid #fff;
        border-left: 0px solid #fff;
        border-bottom: 0px solid #c0c0c0;
    }

    .table_style3{
        border-collapse:collapse;
    }

    .table_style3 tr:nth-child(odd){
    	background-color:#F7F8F8;
    }

    .table_style3 tr:nth-child(even){
    	background-color:#CEE8E9;
    }

    /*.table_style3 tr:first-child td{
        text-align:center;

        font-family:"微軟正黑體","標楷體","新細明體";
        font-weight:bold;

        color:#ffffff;
        background-color:#B2D4DD;
        border:1px solid #ffffff;
    }*/

    .table_style3 tr:first-child th{
        text-align:center;

        font-family:"微軟正黑體","標楷體","新細明體";
        font-weight:bold;

        color:#ffffff;
        background-color:#B2D4DD;
        border:1px solid #ffffff;
    }

    .table_style3 td{
        padding:5px;

        font-family:Arial;

        color:#8e4408;
        border:1px solid #ffffff;
    }
/*
-------------------------------------------------------
font
-------------------------------------------------------
文字尺寸    小->大
    .fsize_07
    .fsize_08
    .fsize_09
    .fsize_10
    .fsize_11

文字顏色    淡->深
    .fc_red0
    .fc_red1
    .fc_green0
    .fc_green1
    .fc_blue0
    .fc_blue1
    .fc_black0
    .fc_black1
    .fc_black2
    .fc_orange0
    .fc_orange1
特殊
    .btn_disabled
*/

    /* 文字尺寸 */
    .fsize_07{
        font-size:7pt;
    }
    .fsize_08{
        font-size:8pt;
    }
    .fsize_09{
        font-size:9pt;
    }
    .fsize_10{
        font-size:10pt;
    }
    .fsize_11{
        font-size:11pt;
    }

    /* 文字顏色 */
    .fc_red0{
        color:#ff0000;
    }
    .fc_red1{
        color:#f55555;
    }

    .fc_green0{
        color: #437C85;
    }
    .fc_green1{
        color:#154D31;
    }

    .fc_blue0{
        color:#0000ff;
    }
    .fc_blue1{
        color:#03629D;
    }

    .fc_black0{
        color:#000000;
    }
    .fc_black1{
        color:#333333;
    }
    .fc_black2{
        color:#999999;
    }

    .fc_orange0{
        color:#ef841c;
    }
    .fc_orange1{
        color:#ec6c01;
    }

    .btn_disabled{
        color:#86989d;
    }

</style>
<body>
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
 <!-- 內容區塊(下半部) 開始 -->
        <div id="content_bottom">

            <!-- logo 開始 -->
            <div id="logo" style="margin:15px 0px 0px 0px">
                明日書店
                <span id="logo_min"><?php echo htmlspecialchars($mod_cname);?></span>
            </div>
            <!-- logo 結束 -->
        </div>
        <!-- 內容區塊(下半部) 結束 -->

    <div class="mod_data_tbl_outline" style="margin-top:35px;">
        <table id="mod_data_tbl" style="" class="font-weight1 font-family1 fc_green0" border="0" cellpadding="5" cellspacing="0" height="40px" width="99%">
            <tbody><tr class="fsize_16" align="center" valign="middle">
                <td align="center" valign="middle" width="650px">
                    <span class="fsize_16">
                        ●本頁資料計算時間為
                        <span class="fc_red1"><?php echo $start; ?></span>
                        ~
                        <span class="fc_red1"><?php echo $end; ?></span>
                        為止
                    </span>
                </td>
            </tr>
        </tbody></table>
    </div>

     <div class="mod_data_tbl_outline" style="margin-top:35px;">
       <table id="mod_data_tbl" class="table_style3"  border="0" width="901px" cellpadding="5" cellspacing="0" style="margin-top:30px;" align ='center'>
        <tr  class="fsize_16" align="center" valign="middle" class="fsize_18">
            <th >書籍                   </th>
            <th >發文次數                   </th>
            <th >回文次數               </th>
            <th >討論人數               </th>
            <th>前往此書頁               </th>
        </tr>
<?php
              foreach($new_final as $k=>$v){
                            $book_sid = $v['rs_book_sid'];
                            $book_name = get_book_info($conn='',$book_sid,array('book_name'),$arry_conn_mssr);
                            $book_name = $book_name[0]['book_name'];
?>

        <tr class="fsize_16" align="center" valign="middle"">
            <td height="35px" ><?php echo $book_name;          ?></td>
            <td height="35px"><?php echo $v['rs_fawon'];       ?></td>
            <td height="35px"><?php echo $v['rs_hawon'];       ?></td>
            <td height="35px"><?php echo $v['rs_cno'];         ?></td>
            <td><input  type="button" value="前往此書頁" onclick="window.open('../../../../service/forum/mssr_forum_book_discussion.php?book_sid=<?php echo $book_sid;?>')"></a></td>
        </tr>

<?php
              }
?>
                    <table border="0" width="100%">
                        <tr valign="middle">
                            <td align="left">
                                <!-- 分頁列 -->
                                <span id="page" style="position:relative;margin-top:10px;"></span>
                            </td>
                        </tr>
                    </table>
   </div>
</body>
</Html>




<!-- //foreach(
//    book_sid
//    book_sid
//    book_sid
//    book_sid
//    book_sid
//
//    $arry_user=[];
//
//    //針對這本書發文的人有誰 張三 李四 王五
//    $sql="
//
//    ";
//    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
//    foreach(
//        if(!in_array(user_id,$arry_user))$arry_user[]=user_id;
//    )
//
//    .......$arry_user[張三 李四 王五]
//
//    //針對這本書回文的人有誰 王五 林六
//    foreach(
//        if(!in_array(user_id,$arry_user))$arry_user[]=user_id;
//    )
//
//    .......$arry_user[張三 李四 王五 林六]
//
//    count($arry_user)
//)
//
//
//                    $sql ="
//                         SELECT
//
//                           mssr_forum_article.user_id,
//                           mssr_article_book_rev.book_sid,
//                           count(mssr_article_book_rev.book_sid) as fawun_cno,
//
//                               (select
//                                   COUNT(DISTINCT user_id)
//                               from
//                                    mssr_forum_article
//                               join mssr_article_book_rev  on  mssr_article_book_rev.article_id   =  mssr_forum_article.article_id
//
//                               where 1=1
//                                    and user_id in($users)
//                                    and book_sid = mssr_article_book_rev.book_sid) as people_cno
//
//                         FROM
//                             `mssr_forum_article`
//                         join mssr_article_book_rev    on  mssr_article_book_rev.article_id   =  mssr_forum_article.article_id
//
//                         AND mssr_forum_article.`user_id`in($users)
//                         group by mssr_article_book_rev.book_sid
//					";
//                    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr); -->



















