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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_read');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //GET
        $prompt=(isset($_GET['prompt']))?trim($_GET['prompt']):'no';

        //SESSION
        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用

        if(isset($_SESSION['m_user_read']['filter'])){
            $filter=$_SESSION['m_user_read']['filter'];

            if(isset($_SESSION['m_user_read']['class_code'])&&(trim($_SESSION['m_user_read']['class_code'])!=='')){
                $q_class_code=mysql_prep(trim($_SESSION['m_user_read']['class_code']));

                $sql="
                    SELECT
                        `class`.`grade`,
                        `class`.`classroom`,
                        `class`.`class_category`
                    FROM `class`
                    WHERE 1=1
                        AND `class`.`class_code`='{$q_class_code}'
                ";
                $arrys_result=db_result($conn_type='pdo','',$sql,array(0,1),$arry_conn_user);
                if(!empty($arrys_result)){
                    $q_grade=(int)$arrys_result[0]['grade'];
                    $q_classroom=(int)$arrys_result[0]['classroom'];
                    $q_class_category=(int)$arrys_result[0]['class_category'];

                    //置換班級名稱
                    $get_class_code_info_single=get_class_code_info_single('',mysql_prep($sess_login_info['school_code']),(int)$q_grade,$q_classroom,$compile_flag=true,$arry_conn_user);
                    if(!empty($get_class_code_info_single)){
                        foreach($get_class_code_info_single as $inx=>$class_code_info_single){
                            if($q_class_category===(int)$class_code_info_single['class_category'])$new_q_classroom=trim($get_class_code_info_single[$inx]['classroom']);
                        }
                    }
                }
            }
        }
        if(isset($_SESSION['m_user_read']['query_fields'])){
            $query_fields=$_SESSION['m_user_read']['query_fields'];
        }

        if(isset($sess_login_info['uid'])){$sess_user_id=(int)$sess_login_info['uid'];}
        if(isset($sess_login_info['permission'])){$sess_permission=trim($sess_login_info['permission']);}
        if(isset($sess_login_info['school_code'])){$sess_school_code=trim($sess_login_info['school_code']);}
        if(isset($sess_login_info['responsibilities'])){
            $sess_responsibilities=(int)$sess_login_info['responsibilities'];
            if(in_array($auth_sys_check_lv,array(5,14,16,22))){
                $sess_class_code=trim($sess_login_info['arrys_class_code'][0]['class_code']);
                $sess_grade=(int)$sess_login_info['arrys_class_code'][0]['grade'];
                $sess_classroom=(int)$sess_login_info['arrys_class_code'][0]['classroom'];

                //置換班級名稱
                $get_class_code_info_single=get_class_code_info_single('',mysql_prep($sess_school_code),(int)$sess_grade,$sess_classroom,$compile_flag=true,$arry_conn_user);
                $new_sess_classroom=trim($get_class_code_info_single[0]['classroom']);
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //初始化, 選擇身份指標
        $choose_identity_flag=false;
        if(isset($sess_responsibilities)){
            $choose_identity_flag=true;
        }

        //目標年級
        $grade_goal=0;
        if(isset($sess_grade)){
            $grade_goal=$sess_grade;
        }
        if(isset($q_grade)){
            $grade_goal=$q_grade;
        }

        //目標班級
        $classroom_goal=0;
        if(isset($sess_classroom)){
            $classroom_goal=$sess_classroom;
        }
        if(isset($q_classroom)){
            $classroom_goal=$q_classroom;
        }

        //目標班級(轉換)
        $new_classroom_goal='';
        if(isset($new_sess_classroom)){
            $new_classroom_goal=$new_sess_classroom;
        }
        if(isset($new_q_classroom)){
            $new_classroom_goal=$new_q_classroom;
        }

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

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

        //-----------------------------------------------
        //檢核該學校該年級各班資料
        //-----------------------------------------------
        //1     校長
        //3     主任
        //5     帶班老師
        //12    行政老師
        //14    主任帶一個班
        //16    主任帶多個班
        //22    老師帶多個班
        //99    管理者

            if((isset($sess_school_code))&&(trim($sess_school_code)!=='')){

                //參數
                $json_class_code=json_encode(array(),true);
                $date=date("Y-m-d");
                $arrys_class_code=array();

                $sql="
                    SELECT
                        `class`.`class_code`,
                        `class`.`classroom`,
                        `semester`.`start`,
                        `semester`.`end`
                    FROM `semester`
                        INNER JOIN `class` ON
                        `semester`.`semester_code`=`class`.`semester_code`
                    WHERE 1=1
                        AND `semester`.`school_code` = '{$sess_school_code}'
                        AND `class`.`grade`          =  {$grade_goal      }
                        AND `class`.`classroom`      <>29
                        AND `semester`.`start`       <= '{$date            }'
                        AND `semester`.`end`         >= '{$date            }'
                    ORDER BY `class`.`classroom` ASC
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);

                if(!empty($arrys_result)){
                    foreach($arrys_result as $inx=>$arry_result){
                        $class_code=trim($arry_result['class_code']);
                        $classroom=(int)($arry_result['classroom']);
                        $semester_start=trim($arry_result['start']);
                        $semester_end=trim($arry_result['end']);

                        //回填相關資訊
                        $arrys_class_code[$inx]['class_code']=$class_code;
                        $arrys_class_code[$inx]['classroom']=$classroom;
                        $arrys_class_code[$inx]['semester_start']=$semester_start;
                        $arrys_class_code[$inx]['semester_end']=$semester_end;
                    }

                    //轉json
                    $json_class_code=json_encode($arrys_class_code,true);
                }
            }else{
                //網頁標題
                $title="明日星球,教師中心";
                page_sel_no_user($title);
                die();
            }

        //---------------------------------------------------
        //SQL查詢
        //---------------------------------------------------

            if($choose_identity_flag){

                $sess_school_code=mysql_prep($sess_school_code);
                $curdate=date("Y-m-d");

                switch($auth_sys_check_lv){
                //1     校長
                //3     主任
                //5     帶班老師
                //12    行政老師
                //14    主任帶一個班
                //16    主任帶多個班
                //22    老師帶多個班
                //99    管理者
                    case 1:
                        if(($filter!='')&&(isset($q_class_code))){
                            //學生陣列
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                //網頁標題
                                $title="明日星球,教師中心";
                                page_sel_no_user($title);
                                die();
                            }

                            $query_sql="
                                SELECT
                                    IFNULL((
                                        SELECT
                                            `semester`.`start`
                                        FROM `class`
                                            INNER JOIN `semester` ON
                                            `class`.`semester_code`=`semester`.`semester_code`
                                        WHERE `class`.`class_code`='{$q_class_code}'
                                        LIMIT 1
                                    ),'')AS `semester_start`,

                                    IFNULL((
                                        SELECT
                                            `semester`.`end`
                                        FROM `class`
                                            INNER JOIN `semester` ON
                                            `class`.`semester_code`=`semester`.`semester_code`
                                        WHERE `class`.`class_code`='{$q_class_code}'
                                        LIMIT 1
                                    ),'')AS `semester_end`,
                                    `member`.`uid`,
                                    `member`.`name`,
                                    `student`.`number`,
                                    `student`.`start`,
                                    `student`.`end`
                                FROM `member`
                                    INNER JOIN `student`
                                    ON `member`.`uid`=`student`.`uid`
                                WHERE 1=1
                                    AND `member`.`uid` IN ($users)
                                    AND `member`.`permission`<>'x'
                                    AND `student`.`start` <= '{$curdate}'
                                    AND `student`.`end` >= '{$curdate}'
                                    AND `student`.`class_code`='{$q_class_code}'
                                GROUP BY `member`.`uid`, `number`
                                ORDER BY `number` ASC
                            ";
                        }else{
                            //網頁標題
                            $title="明日星球,教師中心";
                            page_sel_class_code($title);
                            die();
                        }
                    break;

                    case 24:
                        if(($filter!='')&&(isset($q_class_code))){
                            //學生陣列
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                //網頁標題
                                $title="明日星球,教師中心";
                                page_sel_no_user($title);
                                die();
                            }

                            $query_sql="
                                SELECT
                                    IFNULL((
                                        SELECT
                                            `semester`.`start`
                                        FROM `class`
                                            INNER JOIN `semester` ON
                                            `class`.`semester_code`=`semester`.`semester_code`
                                        WHERE `class`.`class_code`='{$q_class_code}'
                                        LIMIT 1
                                    ),'')AS `semester_start`,

                                    IFNULL((
                                        SELECT
                                            `semester`.`end`
                                        FROM `class`
                                            INNER JOIN `semester` ON
                                            `class`.`semester_code`=`semester`.`semester_code`
                                        WHERE `class`.`class_code`='{$q_class_code}'
                                        LIMIT 1
                                    ),'')AS `semester_end`,
                                    `member`.`uid`,
                                    `member`.`name`,
                                    `student`.`number`,
                                    `student`.`start`,
                                    `student`.`end`
                                FROM `member`
                                    INNER JOIN `student`
                                    ON `member`.`uid`=`student`.`uid`
                                WHERE 1=1
                                    AND `member`.`uid` IN ($users)
                                    AND `member`.`permission`<>'x'
                                    AND `student`.`start` <= '{$curdate}'
                                    AND `student`.`end` >= '{$curdate}'
                                    AND `student`.`class_code`='{$q_class_code}'
                                GROUP BY `member`.`uid`, `number`
                                ORDER BY `number` ASC
                            ";
                        }else{
                            //網頁標題
                            $title="明日星球,教師中心";
                            page_sel_class_code($title);
                            die();
                        }
                    break;

                    case 3:
                        if(($filter!='')&&(isset($q_class_code))){
                            //學生陣列
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                ////網頁標題
                                //$title="明日星球,教師中心";
                                //page_sel_no_user($title);
                                //die();
                                $query_sql="
                                    SELECT
                                        IFNULL((
                                            SELECT
                                                `semester`.`start`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_start`,

                                        IFNULL((
                                            SELECT
                                                `semester`.`end`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_end`,
                                        `member`.`uid`,
                                        `member`.`name`,
                                        `student`.`number`,
                                        `student`.`start`,
                                        `student`.`end`
                                    FROM `member`
                                        INNER JOIN `student`
                                        ON `member`.`uid`=`student`.`uid`
                                    WHERE 1=1
                                        AND `student`.`class_code`='{$q_class_code}'
                                        AND `member`.`permission`<>'x'
                                    GROUP BY `member`.`uid`, `number`
                                    ORDER BY `number` ASC
                                ";
                            }else{
                                $query_sql="
                                    SELECT
                                        IFNULL((
                                            SELECT
                                                `semester`.`start`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_start`,

                                        IFNULL((
                                            SELECT
                                                `semester`.`end`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_end`,
                                        `member`.`uid`,
                                        `member`.`name`,
                                        `student`.`number`,
                                        `student`.`start`,
                                        `student`.`end`
                                    FROM `member`
                                        INNER JOIN `student`
                                        ON `member`.`uid`=`student`.`uid`
                                    WHERE 1=1
                                        AND `member`.`permission`<>'x'
                                        AND `member`.`uid` IN ($users)
                                        AND `student`.`start` <= '{$curdate}'
                                        AND `student`.`end` >= '{$curdate}'
                                        AND `student`.`class_code`='{$q_class_code}'
                                    GROUP BY `member`.`uid`, `number`
                                    ORDER BY `number` ASC
                                ";
                            }
                        }else{
                            //網頁標題
                            $title="明日星球,教師中心";
                            page_sel_class_code($title);
                            die();
                        }
                    break;

                    case 5:
                        if(($filter!='')&&(isset($q_class_code))){
                            //學生陣列
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                ////網頁標題
                                //$title="明日星球,教師中心";
                                //page_sel_no_user($title);
                                //die();
                                $query_sql="
                                    SELECT
                                        IFNULL((
                                            SELECT
                                                `semester`.`start`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_start`,

                                        IFNULL((
                                            SELECT
                                                `semester`.`end`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_end`,
                                        `member`.`uid`,
                                        `member`.`name`,
                                        `student`.`number`,
                                        `student`.`start`,
                                        `student`.`end`
                                    FROM `member`
                                        INNER JOIN `student`
                                        ON `member`.`uid`=`student`.`uid`
                                    WHERE 1=1
                                        AND `student`.`class_code`='{$q_class_code}'
                                        AND `member`.`permission`<>'x'
                                    GROUP BY `member`.`uid`, `number`
                                    ORDER BY `number` ASC
                                ";
                            }else{
                                $query_sql="
                                    SELECT
                                        IFNULL((
                                            SELECT
                                                `semester`.`start`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_start`,

                                        IFNULL((
                                            SELECT
                                                `semester`.`end`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_end`,
                                        `member`.`uid`,
                                        `member`.`name`,
                                        `student`.`number`,
                                        `student`.`start`,
                                        `student`.`end`
                                    FROM `member`
                                        INNER JOIN `student`
                                        ON `member`.`uid`=`student`.`uid`
                                    WHERE 1=1
                                        AND `member`.`permission`<>'x'
                                        AND `member`.`uid` IN ($users)
                                        AND `student`.`start` <= '{$curdate}'
                                        AND `student`.`end` >= '{$curdate}'
                                        AND `student`.`class_code`='{$q_class_code}'
                                    GROUP BY `member`.`uid`, `number`
                                    ORDER BY `number` ASC
                                ";
                            }
                        }else{
                            //學生陣列
                            $users=arrys_users($conn_user,$sess_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                //網頁標題
                                $title="明日星球,教師中心";
                                page_sel_no_user($title);
                                die();
                            }

                            $query_sql="
                                SELECT
                                    IFNULL((
                                        SELECT
                                            `semester`.`start`
                                        FROM `class`
                                            INNER JOIN `semester` ON
                                            `class`.`semester_code`=`semester`.`semester_code`
                                        WHERE `class`.`class_code`='{$sess_class_code}'
                                        LIMIT 1
                                    ),'')AS `semester_start`,

                                    IFNULL((
                                        SELECT
                                            `semester`.`end`
                                        FROM `class`
                                            INNER JOIN `semester` ON
                                            `class`.`semester_code`=`semester`.`semester_code`
                                        WHERE `class`.`class_code`='{$sess_class_code}'
                                        LIMIT 1
                                    ),'')AS `semester_end`,
                                    `member`.`uid`,
                                    `member`.`name`,
                                    `student`.`number`,
                                    `student`.`start`,
                                    `student`.`end`
                                FROM `member`
                                    INNER JOIN `student`
                                    ON `member`.`uid`=`student`.`uid`
                                WHERE 1=1
                                    AND `member`.`permission`<>'x'
                                    AND `member`.`uid` IN ($users)
                                    AND `student`.`start` <= '{$curdate}'
                                    AND `student`.`end` >= '{$curdate}'
                                    AND `student`.`class_code`='{$sess_class_code}'
                                GROUP BY `member`.`uid`, `number`
                                ORDER BY `number` ASC
                            ";
                        }
                    break;

                    case 12:
                        if(($filter!='')&&(isset($q_class_code))){
                            //學生陣列
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                ////網頁標題
                                //$title="明日星球,教師中心";
                                //page_sel_no_user($title);
                                //die();
                                $query_sql="
                                    SELECT
                                        IFNULL((
                                            SELECT
                                                `semester`.`start`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_start`,

                                        IFNULL((
                                            SELECT
                                                `semester`.`end`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_end`,
                                        `member`.`uid`,
                                        `member`.`name`,
                                        `student`.`number`,
                                        `student`.`start`,
                                        `student`.`end`
                                    FROM `member`
                                        INNER JOIN `student`
                                        ON `member`.`uid`=`student`.`uid`
                                    WHERE 1=1
                                        AND `student`.`class_code`='{$q_class_code}'
                                        AND `member`.`permission`<>'x'
                                    GROUP BY `member`.`uid`, `number`
                                    ORDER BY `number` ASC
                                ";
                            }else{
                                $query_sql="
                                    SELECT
                                        IFNULL((
                                            SELECT
                                                `semester`.`start`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_start`,

                                        IFNULL((
                                            SELECT
                                                `semester`.`end`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_end`,
                                        `member`.`uid`,
                                        `member`.`name`,
                                        `student`.`number`,
                                        `student`.`start`,
                                        `student`.`end`
                                    FROM `member`
                                        INNER JOIN `student`
                                        ON `member`.`uid`=`student`.`uid`
                                    WHERE 1=1
                                        AND `member`.`permission`<>'x'
                                        AND `member`.`uid` IN ($users)
                                        AND `student`.`start` <= '{$curdate}'
                                        AND `student`.`end` >= '{$curdate}'
                                        AND `student`.`class_code`='{$q_class_code}'
                                    GROUP BY `member`.`uid`, `number`
                                    ORDER BY `number` ASC
                                ";
                            }
                        }else{
                            //網頁標題
                            $title="明日星球,教師中心";
                            page_sel_no_user($title);
                            die();
                        }
                    break;

                    case 14:
                        if(($filter!='')&&(isset($q_class_code))){
                            //學生陣列
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                ////網頁標題
                                //$title="明日星球,教師中心";
                                //page_sel_no_user($title);
                                //die();
                                $query_sql="
                                    SELECT
                                        IFNULL((
                                            SELECT
                                                `semester`.`start`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_start`,

                                        IFNULL((
                                            SELECT
                                                `semester`.`end`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_end`,
                                        `member`.`uid`,
                                        `member`.`name`,
                                        `student`.`number`,
                                        `student`.`start`,
                                        `student`.`end`
                                    FROM `member`
                                        INNER JOIN `student`
                                        ON `member`.`uid`=`student`.`uid`
                                    WHERE 1=1
                                        AND `student`.`class_code`='{$q_class_code}'
                                        AND `member`.`permission`<>'x'
                                    GROUP BY `member`.`uid`, `number`
                                    ORDER BY `number` ASC
                                ";
                            }else{
                                $query_sql="
                                    SELECT
                                        IFNULL((
                                            SELECT
                                                `semester`.`start`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_start`,

                                        IFNULL((
                                            SELECT
                                                `semester`.`end`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_end`,
                                        `member`.`uid`,
                                        `member`.`name`,
                                        `student`.`number`,
                                        `student`.`start`,
                                        `student`.`end`
                                    FROM `member`
                                        INNER JOIN `student`
                                        ON `member`.`uid`=`student`.`uid`
                                    WHERE 1=1
                                        AND `member`.`permission`<>'x'
                                        AND `member`.`uid` IN ($users)
                                        AND `student`.`start` <= '{$curdate}'
                                        AND `student`.`end` >= '{$curdate}'
                                        AND `student`.`class_code`='{$q_class_code}'
                                    GROUP BY `member`.`uid`, `number`
                                    ORDER BY `number` ASC
                                ";
                            }
                        }else{
                            //學生陣列
                            $users=arrys_users($conn_user,$sess_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                //網頁標題
                                $title="明日星球,教師中心";
                                page_sel_no_user($title);
                                die();
                            }

                            $query_sql="
                                SELECT
                                    IFNULL((
                                        SELECT
                                            `semester`.`start`
                                        FROM `class`
                                            INNER JOIN `semester` ON
                                            `class`.`semester_code`=`semester`.`semester_code`
                                        WHERE `class`.`class_code`='{$sess_class_code}'
                                        LIMIT 1
                                    ),'')AS `semester_start`,

                                    IFNULL((
                                        SELECT
                                            `semester`.`end`
                                        FROM `class`
                                            INNER JOIN `semester` ON
                                            `class`.`semester_code`=`semester`.`semester_code`
                                        WHERE `class`.`class_code`='{$sess_class_code}'
                                        LIMIT 1
                                    ),'')AS `semester_end`,
                                    `member`.`uid`,
                                    `member`.`name`,
                                    `student`.`number`,
                                    `student`.`start`,
                                    `student`.`end`
                                FROM `member`
                                    INNER JOIN `student`
                                    ON `member`.`uid`=`student`.`uid`
                                WHERE 1=1
                                    AND `member`.`permission`<>'x'
                                    AND `member`.`uid` IN ($users)
                                    AND `student`.`start` <= '{$curdate}'
                                    AND `student`.`end` >= '{$curdate}'
                                    AND `student`.`class_code`='{$sess_class_code}'
                                GROUP BY `member`.`uid`, `number`
                                ORDER BY `number` ASC
                            ";
                        }
                    break;

                    case 16:
                        if(($filter!='')&&(isset($q_class_code))){
                            //學生陣列
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                ////網頁標題
                                //$title="明日星球,教師中心";
                                //page_sel_no_user($title);
                                //die();
                                $query_sql="
                                    SELECT
                                        IFNULL((
                                            SELECT
                                                `semester`.`start`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_start`,

                                        IFNULL((
                                            SELECT
                                                `semester`.`end`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_end`,
                                        `member`.`uid`,
                                        `member`.`name`,
                                        `student`.`number`,
                                        `student`.`start`,
                                        `student`.`end`
                                    FROM `member`
                                        INNER JOIN `student`
                                        ON `member`.`uid`=`student`.`uid`
                                    WHERE 1=1
                                        AND `student`.`class_code`='{$q_class_code}'
                                        AND `member`.`permission`<>'x'
                                    GROUP BY `member`.`uid`, `number`
                                    ORDER BY `number` ASC
                                ";
                            }else{
                                $query_sql="
                                    SELECT
                                        IFNULL((
                                            SELECT
                                                `semester`.`start`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_start`,

                                        IFNULL((
                                            SELECT
                                                `semester`.`end`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_end`,
                                        `member`.`uid`,
                                        `member`.`name`,
                                        `student`.`number`,
                                        `student`.`start`,
                                        `student`.`end`
                                    FROM `member`
                                        INNER JOIN `student`
                                        ON `member`.`uid`=`student`.`uid`
                                    WHERE 1=1
                                        AND `member`.`permission`<>'x'
                                        AND `member`.`uid` IN ($users)
                                        AND `student`.`start` <= '{$curdate}'
                                        AND `student`.`end` >= '{$curdate}'
                                        AND `student`.`class_code`='{$q_class_code}'
                                    GROUP BY `member`.`uid`, `number`
                                    ORDER BY `number` ASC
                                ";
                            }
                        }else{
                            //網頁標題
                            $title="明日星球,教師中心";
                            page_sel_class_code($title);
                            die();
                        }
                    break;

                    case 22:
                        if(($filter!='')&&(isset($q_class_code))){
                            //學生陣列
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                ////網頁標題
                                //$title="明日星球,教師中心";
                                //page_sel_no_user($title);
                                //die();
                                $query_sql="
                                    SELECT
                                        IFNULL((
                                            SELECT
                                                `semester`.`start`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_start`,

                                        IFNULL((
                                            SELECT
                                                `semester`.`end`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_end`,
                                        `member`.`uid`,
                                        `member`.`name`,
                                        `student`.`number`,
                                        `student`.`start`,
                                        `student`.`end`
                                    FROM `member`
                                        INNER JOIN `student`
                                        ON `member`.`uid`=`student`.`uid`
                                    WHERE 1=1
                                        AND `student`.`class_code`='{$q_class_code}'
                                        AND `member`.`permission`<>'x'
                                    GROUP BY `member`.`uid`, `number`
                                    ORDER BY `number` ASC
                                ";
                            }else{
                                $query_sql="
                                    SELECT
                                        IFNULL((
                                            SELECT
                                                `semester`.`start`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_start`,

                                        IFNULL((
                                            SELECT
                                                `semester`.`end`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_end`,
                                        `member`.`uid`,
                                        `member`.`name`,
                                        `student`.`number`,
                                        `student`.`start`,
                                        `student`.`end`
                                    FROM `member`
                                        INNER JOIN `student`
                                        ON `member`.`uid`=`student`.`uid`
                                    WHERE 1=1
                                        AND `member`.`permission`<>'x'
                                        AND `member`.`uid` IN ($users)
                                        AND `student`.`start` <= '{$curdate}'
                                        AND `student`.`end` >= '{$curdate}'
                                        AND `student`.`class_code`='{$q_class_code}'
                                    GROUP BY `member`.`uid`, `number`
                                    ORDER BY `number` ASC
                                ";
                            }
                        }else{
                            //網頁標題
                            $title="明日星球,教師中心";
                            page_sel_class_code($title);
                            die();
                        }
                    break;

                    case 99:
                        if(($filter!='')&&(isset($q_class_code))){
                            //學生陣列
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                ////網頁標題
                                //$title="明日星球,教師中心";
                                //page_sel_no_user($title);
                                //die();
                                $query_sql="
                                    SELECT
                                        IFNULL((
                                            SELECT
                                                `semester`.`start`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_start`,

                                        IFNULL((
                                            SELECT
                                                `semester`.`end`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_end`,
                                        `member`.`uid`,
                                        `member`.`name`,
                                        `student`.`number`,
                                        `student`.`start`,
                                        `student`.`end`
                                    FROM `member`
                                        INNER JOIN `student`
                                        ON `member`.`uid`=`student`.`uid`
                                    WHERE 1=1
                                        AND `student`.`class_code`='{$q_class_code}'
                                        AND `member`.`permission`<>'x'
                                    GROUP BY `member`.`uid`, `number`
                                    ORDER BY `number` ASC
                                ";
                            }else{
                                $query_sql="
                                    SELECT
                                        IFNULL((
                                            SELECT
                                                `semester`.`start`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_start`,

                                        IFNULL((
                                            SELECT
                                                `semester`.`end`
                                            FROM `class`
                                                INNER JOIN `semester` ON
                                                `class`.`semester_code`=`semester`.`semester_code`
                                            WHERE `class`.`class_code`='{$q_class_code}'
                                            LIMIT 1
                                        ),'')AS `semester_end`,
                                        `member`.`uid`,
                                        `member`.`name`,
                                        `student`.`number`,
                                        `student`.`start`,
                                        `student`.`end`
                                    FROM `member`
                                        INNER JOIN `student`
                                        ON `member`.`uid`=`student`.`uid`
                                    WHERE 1=1
                                        AND `member`.`permission`<>'x'
                                        AND `member`.`uid` IN ($users)
                                        AND `student`.`start` <= '{$curdate}'
                                        AND `student`.`end` >= '{$curdate}'
                                        AND `student`.`class_code`='{$q_class_code}'
                                    GROUP BY `member`.`uid`, `number`
                                    ORDER BY `number` ASC
                                ";
                            }
                        }else{
                            //網頁標題
                            $title="明日星球,教師中心";
                            page_sel_class_code($title);
                            die();
                        }
                    break;

                    case 20:
                        if(($filter!='')&&(isset($q_class_code))){
                            //學生陣列
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                //網頁標題
                                $title="明日星球,教師中心";
                                page_sel_no_user($title);
                                die();
                            }

                            $query_sql="
                                SELECT
                                    IFNULL((
                                        SELECT
                                            `semester`.`start`
                                        FROM `class`
                                            INNER JOIN `semester` ON
                                            `class`.`semester_code`=`semester`.`semester_code`
                                        WHERE `class`.`class_code`='{$q_class_code}'
                                        LIMIT 1
                                    ),'')AS `semester_start`,

                                    IFNULL((
                                        SELECT
                                            `semester`.`end`
                                        FROM `class`
                                            INNER JOIN `semester` ON
                                            `class`.`semester_code`=`semester`.`semester_code`
                                        WHERE `class`.`class_code`='{$q_class_code}'
                                        LIMIT 1
                                    ),'')AS `semester_end`,
                                    `member`.`uid`,
                                    `member`.`name`,
                                    `student`.`number`,
                                    `student`.`start`,
                                    `student`.`end`
                                FROM `member`
                                    INNER JOIN `student`
                                    ON `member`.`uid`=`student`.`uid`
                                WHERE 1=1
                                    AND `member`.`permission`<>'x'
                                    AND `member`.`uid` IN ($users)
                                    AND `student`.`start` <= '{$curdate}'
                                    AND `student`.`end` >= '{$curdate}'
                                    AND `student`.`class_code`='{$q_class_code}'
                                GROUP BY `member`.`uid`, `number`
                                ORDER BY `number` ASC
                            ";
                        }else{
                            //網頁標題
                            $title="明日星球,教師中心";
                            page_sel_class_code($title);
                            die();
                        }
                    break;

                    default:
                        $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                history.back(-1);
                            </script>
                        ";
                        die($jscript_back);
                    break;
                }
            }
            $db_results=db_result($conn_type='pdo',$conn_user,$query_sql,array(),$arry_conn_user);
            $db_results_cno=count($db_results);
            if(empty($db_results)){
                //網頁標題
                $title="明日星球,教師中心";
                page_sel_no_user($title);
                die();
            }
            //echo (int)($db_results_cno);
            //if($sess_user_id===12089){
            //    echo "<Pre>";
            //    print_r($query_sql);
            //    echo "</Pre>";
            //    die();
            //}

    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        if($choose_identity_flag){

            $numrow=$db_results_cno;
            $psize =$numrow;        //單頁筆數,預設全部
            $pnos  =0;              //分頁筆數
            $pinx  =1;              //目前分頁索引,預設1
            $sinx  =0;              //值域起始值
            $einx  =0;              //值域終止值

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
            //echo $numrow."<br/>";
        }

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,教師中心";

        if($choose_identity_flag){
            if($numrow!==0){
                $arrys_chunk =array_chunk($db_results,$psize);
                $arrys_result=$arrys_chunk[$pinx-1];
                page_hrs($title);
                die();
            }else{
                page_nrs($title);
                die();
            }
        }
?>
<?php function page_hrs($title="") {?>
<?php
//-------------------------------------------------------
//page_hrs 區塊 -- 開始
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
        global $arry_conn_user;
        global $arry_conn_mssr;
        global $arry_ftp1_info;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;
        global $auth_sys_check_lv;

        global $arrys_class_code;
        global $json_class_code;
        global $arrys_result;
        global $config_arrys;
        global $conn_user;
        global $conn_mssr;
        global $q_class_code;

        global $sess_school_code;
        global $grade_goal;
        global $classroom_goal;
        global $new_classroom_goal;

        global $prompt;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=11; //欄位個數
        $btn_nos=1;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
//echo "<Pre>";print_r($_SESSION['teacher_center']);echo "</Pre>";

        //學期時間範圍
        $semester_start=trim($arrys_result[0]['semester_start']);
        $semester_end  =trim($arrys_result[0]['semester_end']);

        //是否為當學期
        $is_now_semester    =false;
        $now_time           =(double)time();
        $semester_start_time=(double)strtotime($semester_start);
        $semester_end_time  =(double)strtotime($semester_end);
        if(($semester_start_time<=$now_time)&&($semester_end_time>=$now_time))$is_now_semester=true;

        //學生陣列
        $arrys_user=array();

        //echo "<Pre>";
        //print_r($arrys_result);
        //echo "</Pre>";

    //---------------------------------------------------
    //班級學生
    //---------------------------------------------------

        $arry_uid=[];
        $list_uid='';
        foreach($arrys_result as $arry_result){
            $rs_uid=(int)$arry_result['uid'];
            $arry_uid[]=$rs_uid;
        }
        $list_uid=implode(",",$arry_uid);
        //echo "<Pre>";print_r($list_uid);echo "</Pre>";

    //---------------------------------------------------
    //當學期登記情形
    //---------------------------------------------------

        $read_more_cno  =0;
        $read_less_cno  =0;
        $read_group_cno =0;
        $read_group_cno_today_cno=0;
        $read_group_cno_fda_1_day_cno=0;
        $read_group_cno_fda_2_day_cno=0;
        $read_group_cno_fda_3_day_cno=0;
        $read_group_cno_fda_4_day_cno=0;
        $read_group_cno_fda_5_day_cno=0;
        $read_group_cno_fda_6_day_cno=0;
        foreach($arrys_result as $arry_result){

            $uid=(int)$arry_result['uid'];

            //-------------------------------------------
            //當天閱讀紀錄
            //-------------------------------------------

                $read_group_cno_today=(int)numrow_book_read_group($conn_mssr,$uid,date("Y-m-d"),array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);
                $read_group_cno=$read_group_cno+$read_group_cno_today;
                $read_group_cno_today_cno=$read_group_cno_today_cno+$read_group_cno_today;

            //-------------------------------------------
            //前幾天閱讀紀錄
            //-------------------------------------------

                $read_group_cno_fda_1_day=(int)numrow_book_read_group($conn_mssr,$uid,date('Y-m-d', strtotime('-1 DAY')),array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);
                $read_group_cno_fda_2_day=(int)numrow_book_read_group($conn_mssr,$uid,date('Y-m-d', strtotime('-2 DAY')),array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);
                $read_group_cno_fda_3_day=(int)numrow_book_read_group($conn_mssr,$uid,date('Y-m-d', strtotime('-3 DAY')),array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);
                $read_group_cno_fda_4_day=(int)numrow_book_read_group($conn_mssr,$uid,date('Y-m-d', strtotime('-4 DAY')),array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);
                $read_group_cno_fda_5_day=(int)numrow_book_read_group($conn_mssr,$uid,date('Y-m-d', strtotime('-5 DAY')),array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);
                $read_group_cno_fda_6_day=(int)numrow_book_read_group($conn_mssr,$uid,date('Y-m-d', strtotime('-6 DAY')),array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);

                $read_group_cno=$read_group_cno+$read_group_cno_fda_1_day;
                $read_group_cno=$read_group_cno+$read_group_cno_fda_2_day;
                $read_group_cno=$read_group_cno+$read_group_cno_fda_3_day;
                $read_group_cno=$read_group_cno+$read_group_cno_fda_4_day;
                $read_group_cno=$read_group_cno+$read_group_cno_fda_5_day;
                $read_group_cno=$read_group_cno+$read_group_cno_fda_6_day;

                $read_group_cno_fda_1_day_cno=$read_group_cno_fda_1_day_cno+$read_group_cno_fda_1_day;
                $read_group_cno_fda_2_day_cno=$read_group_cno_fda_2_day_cno+$read_group_cno_fda_2_day;
                $read_group_cno_fda_3_day_cno=$read_group_cno_fda_3_day_cno+$read_group_cno_fda_3_day;
                $read_group_cno_fda_4_day_cno=$read_group_cno_fda_4_day_cno+$read_group_cno_fda_4_day;
                $read_group_cno_fda_5_day_cno=$read_group_cno_fda_5_day_cno+$read_group_cno_fda_5_day;
                $read_group_cno_fda_6_day_cno=$read_group_cno_fda_6_day_cno+$read_group_cno_fda_6_day;

                if($read_group_cno_today>=10 || $read_group_cno_fda_1_day>=10 || $read_group_cno_fda_2_day>=10 || $read_group_cno_fda_3_day>=10 || $read_group_cno_fda_4_day>=10 || $read_group_cno_fda_5_day>=10 || $read_group_cno_fda_6_day>=10){
                    $read_more_cno++;
                }

                if($read_group_cno_today===0 && $read_group_cno_fda_1_day===0 && $read_group_cno_fda_2_day===0 && $read_group_cno_fda_3_day===0 && $read_group_cno_fda_4_day===0 && $read_group_cno_fda_5_day===0 && $read_group_cno_fda_6_day===0){
                    $read_less_cno++;
                }
        }

    //---------------------------------------------------
    //當學期推薦
    //---------------------------------------------------

        $has_comment=0;
        $good_rec   =0;
        $bad_rec    =0;

        $sql="
            SELECT
                `mssr`.`mssr_rec_book_cno`.`user_id`,
                `mssr`.`mssr_rec_book_cno`.`book_sid`,
                `mssr`.`mssr_rec_book_cno`.`keyin_mdate`,
                IFNULL((
                    SELECT `mssr`.`mssr_rec_comment_log`.`comment_to`
                    FROM `mssr`.`mssr_rec_comment_log`
                    WHERE 1=1
                        AND `mssr`.`mssr_rec_comment_log`.`comment_to`=`mssr`.`mssr_rec_book_cno`.`user_id`
                        AND `mssr`.`mssr_rec_comment_log`.`book_sid`=`mssr`.`mssr_rec_book_cno`.`book_sid`
                        AND `mssr`.`mssr_rec_comment_log`.`keyin_cdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                    LIMIT 1
                ),0) AS `has_comment`,
                IFNULL((
                    SELECT `mssr`.`mssr_rec_teacher_read`.`user_id`
                    FROM `mssr`.`mssr_rec_teacher_read`
                    WHERE 1=1
                        AND `mssr`.`mssr_rec_teacher_read`.`user_id`=`mssr`.`mssr_rec_book_cno`.`user_id`
                        AND `mssr`.`mssr_rec_teacher_read`.`book_sid`=`mssr`.`mssr_rec_book_cno`.`book_sid`
                        AND `mssr`.`mssr_rec_teacher_read`.`keyin_mdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                    LIMIT 1
                ),0) AS `has_read`
            FROM `mssr`.`mssr_rec_book_cno`
            WHERE 1=1
                AND `mssr`.`mssr_rec_book_cno`.`user_id` IN ({$list_uid})
                AND `mssr`.`mssr_rec_book_cno`.`keyin_mdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
            GROUP BY `mssr`.`mssr_rec_book_cno`.`user_id`, `mssr`.`mssr_rec_book_cno`.`book_sid`
        ";
        $rec_book_cno_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
        foreach($rec_book_cno_results as $rec_book_cno_result){
            $rs_book_sid    =trim($rec_book_cno_result['book_sid']);
            $rs_user_id     =(int)trim($rec_book_cno_result['user_id']);
            $rs_has_comment =(int)trim($rec_book_cno_result['has_comment']);
            $rs_has_read    =(int)trim($rec_book_cno_result['has_read']);
            if($rs_has_comment!==0 || $rs_has_read!==0){
                $has_comment++;
            }

            $arrys_rs_rec_text_content=array('','','');
            $rec_text_info=get_rec_info($conn_mssr,$rs_user_id,trim($rs_book_sid),$rec_type='text',$array_filter=array("rec_content"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
            //echo "<Pre>";print_r($rs_book_sid);echo "</Pre>";
            //echo "<Pre>";print_r($rs_user_id);echo "</Pre>";
            if(!empty($rec_text_info)){
                //文字內容
                $rs_rec_text_content=trim($rec_text_info[0]['rec_content']);
                if(@unserialize($rs_rec_text_content)){
                    $arrys_rs_rec_text_content=@unserialize($rs_rec_text_content);
                }
            }

            $rs_rec_text_content='';
            foreach($arrys_rs_rec_text_content as $arry_rs_rec_text_content){
                try {
                    if(trim($arry_rs_rec_text_content)!==''){
                        $arry_rs_rec_text_content=htmlspecialchars(gzuncompress(base64_decode($arry_rs_rec_text_content)));
                        $rs_rec_text_content.=$arry_rs_rec_text_content;
                    }
                } catch (Exception $e) {
                    break;
                }
            }

            $rs_rec_text_content=preg_replace('/\s+/','',$rs_rec_text_content);
            if(trim($rs_rec_text_content)!==''){
                $arry_rs_rec_text_content=[];
                //echo "<Pre>";print_r($rs_rec_text_content);echo "</Pre>";
                for($i=0;$i<=(int)mb_strlen($rs_rec_text_content);$i++){
                    $arry_rs_rec_text_content[]=mb_substr($rs_rec_text_content,$i,1);
                }
                $arry_rs_rec_text_content=array_unique($arry_rs_rec_text_content);
                //echo "<Pre>";print_r($arry_rs_rec_text_content);echo "</Pre>";
                //echo "<Pre>";print_r((int)preg_match_all("/[A-Za-z0-9]/",$rs_rec_text_content,$tmp));echo "</Pre>";
                //echo "<Pre>";print_r((int)mb_strlen($rs_rec_text_content)-(int)preg_match_all("/[A-Za-z0-9]/",$rs_rec_text_content,$tmp));echo "</Pre>";
                //echo "<Pre>";print_r((int)mb_strlen($rs_rec_text_content));echo "</Pre>";
                //echo "<Pre>";print_r((int)count($arry_rs_rec_text_content));echo "</Pre>";
                if((int)count($arry_rs_rec_text_content)-(int)preg_match_all("/[A-Za-z0-9]/",$rs_rec_text_content,$tmp)>=50)$good_rec++;
                if((int)preg_match_all("/[A-Za-z0-9]/",$rs_rec_text_content,$tmp)>=30)$bad_rec++;
            }
        }
        //echo "<Pre>";print_r($good_rec);echo "</Pre>";
        //echo "<Pre>";print_r($bad_rec);echo "</Pre>";
        $has_no_comment=count($rec_book_cno_results)-$has_comment;

    //---------------------------------------------------
    //當學期聊書
    //---------------------------------------------------

        $good_a_r   =0;
        $bad_a_r    =0;
        $arry_article_repl_user_id=[];

        $sql="
            SELECT
                `mssr_forum`.`mssr_forum_article`.`user_id` AS `user_id`,
                `mssr_forum`.`mssr_forum_article`.`article_id` AS `a_r_id`,
                `mssr_forum`.`mssr_forum_article_detail`.`article_content` AS `content`
            FROM `mssr_forum`.`mssr_forum_article`
                INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`
            WHERE 1=1
                AND `mssr_forum`.`mssr_forum_article`.`user_id` IN ({$list_uid})
                AND `mssr_forum`.`mssr_forum_article`.`keyin_cdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 狀態

            UNION ALL

            SELECT
                `mssr_forum`.`mssr_forum_reply`.`user_id` AS `user_id`,
                `mssr_forum`.`mssr_forum_reply`.`reply_id` AS `a_r_id`,
                `mssr_forum`.`mssr_forum_reply_detail`.`reply_content` AS `content`
            FROM `mssr_forum`.`mssr_forum_reply`
                INNER JOIN `mssr_forum`.`mssr_forum_reply_detail` ON
                `mssr_forum`.`mssr_forum_reply`.`reply_id`=`mssr_forum`.`mssr_forum_reply_detail`.`reply_id`
            WHERE 1=1
                AND `mssr_forum`.`mssr_forum_reply`.`user_id` IN ({$list_uid})
                AND `mssr_forum`.`mssr_forum_reply`.`keyin_cdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                AND `mssr_forum`.`mssr_forum_reply`.`reply_state`=1 -- 狀態
        ";
        $article_reply_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
        if(!empty($article_reply_results)){
            foreach($article_reply_results as $article_reply_result){
                $rs_user_id=(int)$article_reply_result['user_id'];
                if(!in_array($rs_user_id,$arry_article_repl_user_id))$arry_article_repl_user_id[]=$rs_user_id;

                $rs_content=trim($article_reply_result['content']);
                $rs_content=preg_replace('/\s+/','',$rs_content);
                if(trim($rs_content)!==''){
                    $arry_rs_content=[];
                    for($i=0;$i<=(int)mb_strlen($rs_content);$i++){
                        $arry_rs_content[]=mb_substr($rs_content,$i,1);
                    }
                    $arry_rs_content=array_unique($arry_rs_content);
                    //echo "<Pre>";print_r((int)count($arry_rs_content));echo "</Pre>";
                    //echo "<Pre>";print_r((int)preg_match_all("/[A-Za-z0-9]/",$rs_content,$tmp));echo "</Pre>";
                    if((int)count($arry_rs_content)-(int)preg_match_all("/[A-Za-z0-9]/",$rs_content,$tmp)>=50)$good_a_r++;
                    if((int)preg_match_all("/[A-Za-z0-9]/",$rs_content,$tmp)>=30)$bad_a_r++;
                }
            }
        }
        //echo "<Pre>";print_r($good_a_r);echo "</Pre>";
        //echo "<Pre>";print_r($bad_a_r);echo "</Pre>";
        //echo "<Pre>";print_r($article_reply_results);echo "</Pre>";

    //---------------------------------------------------
    //其他班級學生優秀表現通知
    //---------------------------------------------------

        $sql="
            SELECT *,
                IFNULL((
                    SELECT `name`
                    FROM `user`.`member`
                    WHERE `user`.`member`.`uid`=`mssr`.`mssr_rec_book_best_class`.`create_by`
                    LIMIT 1
                ),'')as `create_name`,
                IFNULL((
                    SELECT `name`
                    FROM `user`.`member`
                    WHERE `user`.`member`.`uid`=`mssr`.`mssr_rec_book_best_class`.`user_id`
                    LIMIT 1
                ),'')as `user_name`
            FROM `mssr`.`mssr_rec_book_best_class`
            WHERE 1=1
                AND `mssr`.`mssr_rec_book_best_class`.`class_code` LIKE '{$sess_school_code}%'
            ORDER BY `mssr`.`mssr_rec_book_best_class`.`keyin_cdate` DESC
            LIMIT 5
        ";
        $rec_book_best_class_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
        //$ftp_root="public_html/mssr/info/user/".(int)$user_id."/book";
        //$http_path="http://".$arry_ftp1_info['host']."/mssr/info/user/".(int)$user_id."/book/";
        $ftp_root="public_html/mssr/info/class";
        $http_path="http://".$arry_ftp1_info['host']."/mssr/info/class";

        //連接 | 登入 FTP
        $ftp_conn  =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
        $ftp_login =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

        //設定被動模式
        ftp_pasv($ftp_conn,TRUE);
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
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <link href="../../../../lib/framework/bootstrap/css/code.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/func/flot/excanvas.min.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/func/flot/jquery.flot.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/func/flot/jquery.flot.pie.js"></script>
    <script type="text/javascript" src="../../../../lib/framework/bootstrap/js/code.js"></script>

    <script type="text/javascript" src="../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/table/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/array/code.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" />
</Head>

<Body>

    <!-- 資料列表 開始 -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="100%" align="center" valign="top">
            <!-- 內容 -->

                <!-- 選單 -->
                <!-- <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top:35px;"/>
                    <tr align='center' class='fsize_16 font-family1 font-weight1' style='color:#8e4408;'>
                        <td height='35px' bgcolor='#87CDDC' width='20%' style='border:1px solid #87CDDC;' name='tab' att='yes' onclick='tab(0);void(0);' onmouseover='mouseover(this);void(0);'>
                            主控台
                        </td>
                        <td height='35px' width='20%' style='border:1px solid #87CDDC;' name='tab' att='no' onclick='tab(1);void(0);' onmouseover='mouseover(this);void(0);'>
                            各項統計
                        </td>
                        <td height='35px'>&nbsp;</td>
                    </tr>
                </table> -->

                <table cellpadding="0" cellspacing="0" border="0" width="100%" class="mod_data_tbl_outline" style="margin-top:35px;margin-bottom:30px;"/>
                    <tr name='content' class='fsize_13 font-family1'>
                        <td width='' height='' valign='top'>
                            <?php if($is_now_semester):?>
                            <div style="border:1px solid #e1e1e1;margin:35px;background-color:#fff;height:270px;">
                                <div style="border:0px solid #e1e1e1;margin:15px;background-color:#fff;">
                                    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top:-15px;"/>
                                        <tr style="border-bottom:1px solid #e1e1e1;"><td align="center">
                                            <h2>
                                                <b>
                                                    學生閱讀登記
                                                    <span style="">(<?php echo date("Y-m-d", strtotime('-6 DAY'))."~".date('Y-m-d');?>)</span>
                                                </b>
                                            </h2>
                                        </td></tr>
                                        <tr style="border-bottom:1px solid #e1e1e1;"><td align="center">
                                            <h3 style="margin-top:10px;">
                                                <b>
                                                    <?php //echo date("Y-m-d", strtotime('-6 DAY'))."~".date('Y-m-d');?>
                                                    本班總共登記了
                                                    <span style="color:red;font-size:18pt;"><?php echo ($read_group_cno);?></span>
                                                    本書
                                                </b>
                                            </h3>
                                        </td></tr>
                                        <tr style="border-bottom:1px solid #e1e1e1;"><td align="center">
                                            <h3 style="margin-top:10px;">
                                                <b>
                                                    有
                                                    <span style="color:red;font-size:18pt;"><?php echo ($read_more_cno);?></span>
                                                    位學生登記有異常情形<span style="font-size:8pt;">( 大於等於 10本/天 )</span>
                                                </b>
                                            </h3>
                                        </td></tr>
                                        <tr style="border-bottom:1px solid #e1e1e1;"><td align="center">
                                            <h3 style="margin-top:10px;">
                                                <b>
                                                    有
                                                    <span style="color:red;font-size:18pt;"><?php echo ($read_less_cno);?></span>
                                                    位學生在這七天中沒有閱讀登記
                                                </b>
                                            </h3>
                                        </td></tr>
                                        <tr style="border-bottom:1px solid #e1e1e1;"><td align="center">
                                            <div class="alert alert-danger text-center" role="alert" style="cursor:pointer;margin-top:10px;margin-bottom:10px;"
                                            onclick="parent.location.href='../../read/m_user_read/index.php';"><b>點我前往觀看所有學生的閱讀資料</b></div>
                                        </td></tr>
                                    </table>
                                    <div id="container_read_book_cno" style="width:99%;float:left;margin-top:15px;"></div>
                                </div>
                            </div>
                            <?php endif;?>
                        </td>
                        <td width='50%' height='' valign='top'>
                            <div style="border:1px solid #e1e1e1;margin:35px;background-color:#fff;height:270px;">
                                <div style="border:0px solid #e1e1e1;margin:15px;background-color:#fff;">
                                    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top:-15px;"/>
                                        <tr style="border-bottom:1px solid #e1e1e1;"><td align="center">
                                            <h2>
                                                <b>
                                                    學生推薦作品
                                                    <span style="">(<?php echo $semester_start."~".$semester_end;?>)</span>
                                                </b>
                                            </h2>
                                        </td></tr>
                                        <tr style="border-bottom:1px solid #e1e1e1;"><td align="center">
                                            <h3 style="margin-top:10px;">
                                                <b>
                                                    本學期共有
                                                    <span style="color:red;font-size:18pt;"><?php echo count($rec_book_cno_results);?></span>
                                                    件推薦作品
                                                </b>
                                            </h3>
                                        </td></tr>
                                        <tr style="border-bottom:1px solid #e1e1e1;"><td align="center">
                                            <h3 style="margin-top:10px;">
                                                <b>
                                                    您還有
                                                    <span style="color:red;font-size:18pt;"><?php echo ($has_no_comment);?></span>
                                                    件作品尚未閱讀或指導
                                                </b>
                                                <?php if($has_no_comment>0):?>
                                                    <input type="button" value="點我前往指導"
                                                    onclick="window.open('../../rec/m_user_rec/detailed/date_between/index.php?date_filter=lose&semester_start=<?php echo $semester_start;?>&semester_end=<?php echo $semester_end;?>','lose_rec');">
                                                <?php endif;?>
                                            </h3>
                                        </td></tr>
                                        <tr style="border-bottom:1px solid #e1e1e1;display:none;"><td align="center">
                                            <h3 style="margin-top:10px;">
                                                <b>
                                                    有
                                                    <span style="color:red;font-size:18pt;"><?php echo ($good_rec);?></span>
                                                    件優秀作品<span style="font-size:8pt;">( 文字推薦，不同的國字大於等於50個 )</span>
                                                </b>
                                            </h3>
                                        </td></tr>
                                        <tr style="border-bottom:1px solid #e1e1e1;"><td align="center">
                                            <h3 style="margin-top:10px;">
                                                <b>
                                                    有
                                                    <span style="color:red;font-size:18pt;"><?php echo ($bad_rec);?></span>
                                                    件作品可能異常
                                                </b>
                                                <?php if($bad_rec>0):?>
                                                    <input type="button" value="點我前往指導"
                                                    onclick="window.open('../../rec/m_user_rec/detailed/date_between/index.php?date_filter=lose&semester_start=<?php echo $semester_start;?>&semester_end=<?php echo $semester_end;?>&abnormal=1','lose_rec');">
                                                <?php endif;?>
                                                <br>
                                                 <b>
                                                    <span style="font-size:8pt;">( 文字推薦，數字或英文大於等於30個 )</span>
                                                </b>
                                            </h3>
                                        </td></tr>
                                        <tr style="border-bottom:1px solid #e1e1e1;"><td align="center">
                                            <div class="alert alert-danger text-center" role="alert" style="cursor:pointer;margin-top:10px;margin-bottom:10px;"
                                            onclick="parent.location.href='../../rec/m_user_rec/index.php';"><b>點我前往觀看所有的推薦作品</b></div>
                                        </td></tr>
                                    </table>
                                    <div id="container_rec_book_cno" style="width:100%;float:left;margin-top:5px;"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr name='content' class='fsize_13 font-family1'>
                        <td width='50%' height='' valign='top'>
                            <div style="border:1px solid #e1e1e1;margin:35px;background-color:#fff;height:270px;margin-top:-25px;">
                                <div style="border:0px solid #e1e1e1;margin:15px;background-color:#fff;">
                                    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top:-15px;"/>
                                        <tr style="border-bottom:1px solid #e1e1e1;"><td align="center">
                                            <h2>
                                                <b>
                                                    學生聊書文章
                                                    <span style="">(<?php echo $semester_start."~".$semester_end;?>)</span>
                                                </b>
                                            </h2>
                                        </td></tr>
                                        <tr style="border-bottom:1px solid #e1e1e1;"><td align="center">
                                            <h3 style="margin-top:10px;">
                                                <b>
                                                    本學期共發表了
                                                    <span style="color:red;font-size:18pt;"><?php echo count($article_reply_results);?></span>
                                                    篇文章
                                                </b>
                                            </h3>
                                        </td></tr>
                                        <tr style="border-bottom:1px solid #e1e1e1;"><td align="center">
                                            <h3 style="margin-top:10px;">
                                                <b>
                                                    有
                                                    <span style="color:red;font-size:18pt;"><?php echo ($good_a_r);?></span>
                                                    篇優秀文章<span style="font-size:8pt;">( 不同的國字大於等於50個 )</span>
                                                </b>
                                            </h3>
                                        </td></tr>
                                        <tr style="border-bottom:1px solid #e1e1e1;"><td align="center">
                                            <h3 style="margin-top:10px;">
                                                <b>
                                                    有
                                                    <span style="color:red;font-size:18pt;"><?php echo ($bad_a_r);?></span>
                                                    篇文章可能異常<span style="font-size:8pt;">( 數字或英文大於等於30個 )</span>
                                                </b>
                                            </h3>
                                        </td></tr>
                                        <tr style="border-bottom:1px solid #e1e1e1;"><td align="center">
                                            <div class="alert alert-danger text-center" role="alert" style="cursor:pointer;margin-top:10px;margin-bottom:10px;"
                                            onclick="parent.location.href='../../forum/m_forum_article_info/index.php';"><b>點我前往觀看所有的聊書資料</b></div>
                                        </td></tr>
                                    </table>
                                    <div id="container_rec_book_cno" style="width:100%;float:left;margin-top:5px;"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php if(count($rec_book_best_class_results)!==0):?>
                        <tr name='content' style=''>
                            <td width='100%' height='' valign='top' colspan="2" style="background-color:#fff;">
                                <div style="width:90%;margin:0 auto;">
                                    <h1 style="color:#8e4408;font-size:18pt;">
                                    <!-- <div class="alert alert-danger text-center" role="alert" style="cursor:pointer;"
                                    onclick="parent.location.href='../../read/m_user_read/index.php';"><b>觀看學生閱讀資料</b></div> -->
                                    <b>近期優秀作品...</b></h1>
                                </div>
                                <div style="border:0px solid #e1e1e1;height:1px;width:90%;margin:0 auto;margin-top:10px;margin-bottom:10px;"></div>
                                <div style="width:90%;margin:0 auto;color:#8e4408;margin-bottom:20px;border:0px solid red;">
                                    <table cellpadding="0" cellspacing="0" border="0" width="700"
                                    style="font-size:12pt;position:relative;top:30px;z-index:2;left:55px;"/>
                                        <tr>
                                        <?php foreach($rec_book_best_class_results as $rec_book_best_class_result):?>
                                        <?php
                                            $rs_best_id     =(int)$rec_book_best_class_result['best_id'];
                                            $rs_create_name =trim($rec_book_best_class_result['create_name']);
                                            $rs_user_name   =trim($rec_book_best_class_result['user_name']);
                                            $rs_class_code  =trim($rec_book_best_class_result['class_code']);
                                            $rs_book_sid    =trim($rec_book_best_class_result['book_sid']);

                                            $rs_create_name =str_replace("老師","",$rs_create_name);
                                            $rs_create_name =str_replace("導師","",$rs_create_name);

                                            $get_book_info=get_book_info($conn_mssr,trim($rs_book_sid),$array_filter=array('book_name'),$arry_conn_mssr);
                                            if(empty($get_book_info))continue;
                                            $rs_book_name=trim($get_book_info[0]['book_name']);
                                            if(mb_strlen($rs_book_name)>8){
                                                $rs_book_name=mb_substr($rs_book_name,0,8);
                                            }

                                            $has_draw     =false;
                                            //圖片識別碼
                                            $rs_rec_draw_sid=trim($rec_book_best_class_result['rec_draw_sid']);

                                            if($rs_rec_draw_sid!==''){
                                                //手繪
                                                $draw_path      ="{$ftp_root}/{$rs_class_code}/rec_book_best/{$rs_best_id}/1.jpg";
                                                $arry_ftp_file_draw_path=ftp_nlist($ftp_conn,$draw_path);
                                                //echo "<Pre>";print_r($arry_ftp_file_draw_path);echo "</Pre>";

                                                //上傳
                                                $up_load_draw_path_1    ="{$ftp_root}/{$rs_class_code}/rec_book_best/{$rs_best_id}/upload_1.jpg";
                                                $up_load_draw_path_2    ="{$ftp_root}/{$rs_class_code}/rec_book_best/{$rs_best_id}/upload_2.jpg";
                                                $up_load_draw_path_3    ="{$ftp_root}/{$rs_class_code}/rec_book_best/{$rs_best_id}/upload_3.jpg";
                                                $arry_ftp_file_up_load_draw_path_1=ftp_nlist($ftp_conn,$up_load_draw_path_1);
                                                $arry_ftp_file_up_load_draw_path_2=ftp_nlist($ftp_conn,$up_load_draw_path_2);
                                                $arry_ftp_file_up_load_draw_path_3=ftp_nlist($ftp_conn,$up_load_draw_path_3);
                                                //echo "<Pre>";print_r($arry_ftp_file_up_load_draw_path_1);echo "</Pre>";

                                                if((!empty($arry_ftp_file_draw_path))||(!empty($arry_ftp_file_up_load_draw_path_1))||(!empty($arry_ftp_file_up_load_draw_path_2))||(!empty($arry_ftp_file_up_load_draw_path_3))){
                                                    $has_draw=true;
                                                }
                                                if($has_draw){
                                                    if(!empty($arry_ftp_file_up_load_draw_path_3))$show_draw_path=$up_load_draw_path_3;
                                                    if(!empty($arry_ftp_file_up_load_draw_path_2))$show_draw_path=$up_load_draw_path_2;
                                                    if(!empty($arry_ftp_file_up_load_draw_path_1))$show_draw_path=$up_load_draw_path_1;
                                                    if(!empty($arry_ftp_file_draw_path))$show_draw_path=$draw_path;
                                                    $show_draw_path=str_replace("public_html/","",$show_draw_path);
                                                    $show_draw_path="http://".$arry_ftp1_info['host']."/{$show_draw_path}";
                                                }
                                            }
                                        ?>

                                            <td height="50" align="center" style="border-top:0px solid #e1e1e1;">
                                                <div style="position:relative;border:0px solid red;cursor:pointer;"
                                                onclick="show_best_rec(<?php echo $rs_best_id;?>,'<?php echo $rs_class_code;?>');">
                                                    <span style="position:absolute;left:35px;top:7px;font-size:9pt;display:block;max-width:60px;"><?php echo htmlspecialchars($rs_book_name);?></span>
                                                    <img src="../../img/obj/book.png" width="72" height="auto" border="0" alt="book" style="margin:0 auto;"/>
                                                    <?php if($has_draw):?>
                                                        <img src="<?php echo $show_draw_path;?>" width="55" height="50" border="0" alt="rec"
                                                        style="margin:0 auto;position:absolute;bottom:5px;right:47px;"/>
                                                    <?php endif;?>
                                                </div>
                                            </td>
                                        <?php endforeach;?>
                                        </tr>
                                    </table>
                                </div>
                                <img src="../../img/obj/tablet_1000.png" width="800" height="auto" border="0" alt="tablet"
                                style="margin-left:55px;border:0px solid red;position:relative;top:-20px;"/>
                            </td>
                        </tr>
                    <?php endif;?>
                </table>
            <!-- 內容 -->
            </td>
        </tr>
    </table>
    <!-- 資料列表 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    ﻿$(function () {
        //// Set up the chart
        //$('#container_read_book_cno').highcharts({
        //    chart: {type: 'line'},
        //    title: {text: ''},
        //    subtitle: {text: ''},
        //    xAxis: {
        //        categories: [
        //            '<?php echo date("Y-m-d", strtotime("-6 DAY"));?>',
        //            '<?php echo date("Y-m-d", strtotime("-5 DAY"));?>',
        //            '<?php echo date("Y-m-d", strtotime("-4 DAY"));?>',
        //            '<?php echo date("Y-m-d", strtotime("-3 DAY"));?>',
        //            '<?php echo date("Y-m-d", strtotime("-2 DAY"));?>',
        //            '<?php echo date("Y-m-d", strtotime("-1 DAY"));?>',
        //            '<?php echo date("Y-m-d", strtotime("-0 DAY"));?>'
        //        ]
        //    },
        //    yAxis: {
        //        title: {
        //            text: '本數'
        //        }
        //    },
        //    tooltip: {
        //        enabled: false,
        //        formatter: function() {
        //            //return '<b>'+ this.series.name +'</b><br/>'+this.x +': '+ this.y +'°C';
        //        }
        //    },
        //    plotOptions: {
        //        line: {
        //            dataLabels: {
        //                enabled: true
        //            },
        //            enableMouseTracking: false
        //        }
        //    },
        //    series: [{
        //        name: '學生登記本數',
        //        data: [
        //            <?php echo ($read_group_cno_fda_6_day_cno);?>,
        //            <?php echo ($read_group_cno_fda_5_day_cno);?>,
        //            <?php echo ($read_group_cno_fda_4_day_cno);?>,
        //            <?php echo ($read_group_cno_fda_3_day_cno);?>,
        //            <?php echo ($read_group_cno_fda_2_day_cno);?>,
        //            <?php echo ($read_group_cno_fda_1_day_cno);?>,
        //            <?php echo ($read_group_cno_today_cno);?>
        //        ]
        //    }]
        //});
        //
        //var chart = new Highcharts.Chart({
        //    chart: {renderTo: 'container_rec_book_cno',type: 'column',margin: 75,
        //        options3d: {enabled: true,alpha: 15,beta: 15,depth: 50,viewDistance: 25}
        //    },
        //    title: {text: ''},
        //    subtitle: {text: ''},
        //    plotOptions: {column: {depth: 25}},
        //    series: [{
        //        name: '學生推薦作品 (<?php echo $semester_start."~".$semester_end;?>)',
        //        data: [<?php echo count($rec_book_cno_results);?>]
        //    }]
        //});
    });

    //---------------------------------------------------
    //參數
    //---------------------------------------------------

        var nl='\r\n';
        var json_class_code     =<?php echo $json_class_code;?>;
        var sess_school_code    ='<?php echo addslashes($sess_school_code);?>';
        var semester_start      ='<?php echo $semester_start;?>';
        var semester_end        ='<?php echo $semester_end;?>';
        var grade_goal          =<?php echo (int)$grade_goal;?>;
        var classroom_goal      =<?php echo (int)$classroom_goal;?>;
        var auth_sys_check_lv   =<?php echo (int)$auth_sys_check_lv;?>;
        var psize               =<?php echo $psize;?>;
        var pinx                =<?php echo $pinx;?>;

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
        var oread_group_cno=document.getElementById('read_group_cno');
        var oread_group_avg=document.getElementById('read_group_avg');
        var oavg_read_group_info=document.getElementById('avg_read_group_info');
        var odata_statistics=document.getElementById('data_statistics');
        var ousers_id=document.getElementsByName('user_id');

        var category_dataset=[];
        <?php if(!empty($category_arry_book_borrow)):?>
            <?php foreach($category_arry_book_borrow as $key=>$val):?>
            <?php $cno=1;?>
                <?php if(trim($key)!=='未分類'):?>
                    var tmp_category_dataset={label:"<?php echo trim($key);?>",data:<?php echo (int)($val);?>};
                    category_dataset.push(tmp_category_dataset);
                <?php endif;?>
            <?php $cno++;endforeach;?>
        <?php endif;?>

    //---------------------------------------------------
    //function
    //---------------------------------------------------

        function show_best_rec(best_id,class_code){
            $.blockUI({
                message: $('<iframe src="../../rec/m_user_rec_book_best_class/show_query.php?class_code='+class_code+'&best_id='+best_id+'" width="900" height="625"></iframe>'),
                css: {
                    border: 'none',
                    top: '25px',
                    left: 0,
                    left: '25px'
                },
                onOverlayClick: $.unblockUI
            });
        }

        function mouseover(obj){
            obj.style.cursor='pointer';
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

    //---------------------------------------------------
    //onload
    //---------------------------------------------------

        window.onload=function(){

            //設定動態高度
            var oIFC=parent.document.getElementById('IFC');
            var oparent_IFC=parent.parent.document.getElementById('IFC');
            oIFC.style.height=parseInt($(document).height()+50)+'px';
            oparent_IFC.style.height=parseInt($(document).height()+250)+'px';

            //學生陣列
            var arrys_user=<?php echo json_encode($arrys_user,true);?>;

            parent.$.unblockUI();
        }

</script>

</Body>
</Html>

<?php
//-------------------------------------------------------
//page_hrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>
<?php };?>


<?php function page_nrs($title="") {?>
<?php
//-------------------------------------------------------
//page_nrs 區塊 -- 開始
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
        global $arry_conn_mssr;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $config_arrys;
        global $conn_mssr;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0;  //欄位個數
        $btn_nos=0;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
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
</Head>

<Body>

    <!-- 資料列表 開始 -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="400px" align="center" valign="top">
                <!-- 內容 -->
                <table border="0" width="100%" cellpadding="5" cellspacing="0" style="position:relative;top:50px;" class="mod_data_tbl_outline">
                    <tr align="center" valign="middle">
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="400px" align="center" valign="middle" class="font-family1 fc_green0 fsize_18">
                            <img src="../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
                            目前系統無資料，或查無資料!
                        </td>
                    </tr>
                </table>
                <!-- 內容 -->
            </td>
        </tr>
    </table>
    <!-- 資料列表 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;

    window.onload=function(){
        parent.$.unblockUI();
    }

</script>

</Body>
</Html>

<?php
//-------------------------------------------------------
//page_nrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>
<?php };?>


<?php function page_sel_class_code($title="") {?>
<?php
//-------------------------------------------------------
//page_sel_class_code 區塊 -- 開始
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
        global $arry_conn_mssr;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $config_arrys;
        global $conn_mssr;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0;  //欄位個數
        $btn_nos=0;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
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
</Head>

<Body>

    <!-- 資料列表 開始 -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="400px" align="center" valign="top">
                <!-- 內容 -->
                <table border="0" width="100%" cellpadding="5" cellspacing="0" style="position:relative;top:50px;" class="mod_data_tbl_outline">
                    <tr align="center" valign="middle">
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="400px" align="center" valign="middle" class="font-family1 fc_green0 fsize_18">
                            <img src="../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
                            請先選擇右上方的年級與班級!
                        </td>
                    </tr>
                </table>
                <!-- 內容 -->
            </td>
        </tr>
    </table>
    <!-- 資料列表 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    window.onload=function(){
        parent.$.unblockUI();
    }

</script>

</Body>
</Html>

<?php
//-------------------------------------------------------
//page_sel_class_code 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>
<?php };?>


<?php function page_sel_no_user($title="") {?>
<?php
//-------------------------------------------------------
//page_sel_no_user 區塊 -- 開始
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
        global $arry_conn_mssr;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $config_arrys;
        global $conn_mssr;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0;  //欄位個數
        $btn_nos=0;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
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
</Head>

<Body>

    <!-- 資料列表 開始 -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="400px" align="center" valign="top">
                <!-- 內容 -->
                <table border="0" width="100%" cellpadding="5" cellspacing="0" style="position:relative;top:50px;" class="mod_data_tbl_outline">
                    <tr align="center" valign="middle">
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="400px" align="center" valign="middle" class="font-family1 fc_green0 fsize_18">
                            <img src="../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
                            目前沒有學生資料!
                        </td>
                    </tr>
                </table>
                <!-- 內容 -->
            </td>
        </tr>
    </table>
    <!-- 資料列表 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    window.onload=function(){
        parent.$.unblockUI();
    }

</script>

</Body>
</Html>

<?php
//-------------------------------------------------------
//page_sel_no_user 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>
<?php };?>