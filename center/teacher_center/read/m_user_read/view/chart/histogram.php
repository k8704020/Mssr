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
        require_once(str_repeat("../",6).'config/config.php');

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
            $url=str_repeat("../",7).'index.php';
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
        $get_filter_semester_start=(isset($_GET['filter_semester_start']))?trim($_GET['filter_semester_start']):'';
        $get_filter_semester_end  =(isset($_GET['filter_semester_end']))?trim($_GET['filter_semester_end']):'';

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

        if(isset($_SESSION['m_user_read']['class_code'])&&trim($_SESSION['m_user_read']['class_code'])!==''){
            $_SESSION['teacher_center']['class_code']=trim($_SESSION['m_user_read']['class_code']);
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
            //echo (int)($db_results_cno);
            //if($sess_user_id===12089){
            //    echo "<Pre>";
            //    print_r($query_sql);
            //    echo "</Pre>";
            //    die();
            //}
//echo "<Pre>";print_r($get_filter_semester_start);echo "</Pre>";
//echo "<Pre>";print_r($get_filter_semester_end  );echo "</Pre>";
//echo "<Pre>";print_r($_GET);echo "</Pre>";
//echo "<Pre>";print_r($_POST);echo "</Pre>";
//echo "<Pre>";print_r($db_results);echo "</Pre>";
//die();
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

        global $sess_school_code;
        global $grade_goal;
        global $classroom_goal;
        global $new_classroom_goal;

        global $prompt;
        global $get_filter_semester_start;
        global $get_filter_semester_end;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        function diffdate($date1,$date2)
        {
        if(strtotime($date1)>strtotime($date2))
          {
          $ymd = $date2;
          $date2 = $date1;
          $date1 = $ymd;
          }
        list($y1,$m1,$d1) = explode('-',$date1);
        list($y2,$m2,$d2) = explode('-',$date2);
        $math = ($y2-$y1)*12+$m2-$m1;
        $my_arr=array();
        if($y1==$y2&&$m1==$m2)
            {
        if($m1<10){$m1=intval($m1);$m1='0'.$m1;}
        if($m2<10){$m2=intval($m2);$m2='0'.$m2;}
            $my_arr[]=$y1.'-'.$m1;
            $my_arr[]=$y2.'-'.$m2;
            return $my_arr;
            }

        $p=$m1;
        $x=$y1;

        for($i=0;$i<=$math;$i++)
           {
          if($p>12)
            { $x=$x+1;
              $p=$p-12;
              if($p<10){$p=intval($p);$p='0'.$p;}
              $my_arr[]=$x.'-'.$p;
            }
          else
              {
              if($p<10){$p=intval($p);$p='0'.$p;}
              $my_arr[]=$x.'-'.$p;
              }
              $p=$p+1;
           }
           return $my_arr;


        }

        $arry_uid=[];
        $list_uid='';
        foreach($arrys_result as $arrys_resul){
            $rs_uid=(int)$arrys_resul['uid'];
            $arry_uid[]=$rs_uid;
        }
        $list_uid=implode(",",$arry_uid);

        $arrys_month=[];
        foreach(diffdate($get_filter_semester_start,$get_filter_semester_end) as $month){

            $arrys_month[$month]['group']=0;
            $arrys_month[$month]['frequency']=0;
            $arrys_month[$month]['picture_book']=0;
            $arrys_month[$month]['bridge_book']=0;
            $arrys_month[$month]['words_book']=0;
            $arrys_month[$month]['total_book']=0;
            $arrys_month[$month]['bridge_book_rate']= 0;
            $arrys_month[$month]['words_book_rate']= 0;
        }

        $sql="
            SELECT `user_id`, `borrow_sdate`
            FROM `mssr_book_borrow_log`
            WHERE 1=1
                AND `user_id` IN ({$list_uid})
                AND `borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
            GROUP BY `user_id`, `book_sid`
        ";

        $arrys_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

        foreach($arrys_results as $arrys_result){


            $rs_user_id     =(int)$arrys_result['user_id'];
            $rs_borrow_sdate=trim($arrys_result['borrow_sdate']);
            $rs_borrow_sdate=date("Y-m",strtotime($rs_borrow_sdate));

  
            if(array_key_exists($rs_borrow_sdate,$arrys_month))$arrys_month[$rs_borrow_sdate]['group']=$arrys_month[$rs_borrow_sdate]['group']+1;


        }

        $sql="
            SELECT `user_id`, `borrow_sdate`
            FROM `mssr_book_borrow_log`
            WHERE 1=1
                AND `user_id` IN ({$list_uid})
                AND `borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
        ";
        $arrys_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
        foreach($arrys_results as $arrys_result){
            $rs_user_id     =(int)$arrys_result['user_id'];
            $rs_borrow_sdate=trim($arrys_result['borrow_sdate']);
            $rs_borrow_sdate=date("Y-m",strtotime($rs_borrow_sdate));
            if(array_key_exists($rs_borrow_sdate,$arrys_month))$arrys_month[$rs_borrow_sdate]['frequency']=$arrys_month[$rs_borrow_sdate]['frequency']+1;
        }


        //學生繪本每個月看的本數

        $sql="
            SELECT `mssr_book_borrow_log`.`user_id`, 
                   `mssr_book_borrow_log`.`borrow_sdate`,
                   `mssr_book_borrow_log`.`book_sid` 
            FROM `mssr_book_borrow_log` 
            JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid`=`mssr_idc_book_sticker_level_info`.`book_sid` 
            WHERE 1=1
                AND `mssr_book_borrow_log`.`user_id` IN ({$list_uid})
                AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
                AND `mssr_idc_book_sticker_level_info`.administrator_level<3
                AND `mssr_idc_book_sticker_level_info`.administrator_level>0
            GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid` 
        ";

        $arrys_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



        foreach($arrys_results as $arrys_result){

            $rs_user_id     =(int)$arrys_result['user_id'];
            $rs_borrow_sdate=trim($arrys_result['borrow_sdate']);
            $rs_book_sid=trim($arrys_result['book_sid']);
            $rs_borrow_sdate=date("Y-m",strtotime($rs_borrow_sdate));
            if(array_key_exists($rs_borrow_sdate,$arrys_month))$arrys_month[$rs_borrow_sdate]['picture_book']=$arrys_month[$rs_borrow_sdate]['picture_book']+1;

        }

        //學生橋梁書每個月看的本數

        $sql="
            SELECT `mssr_book_borrow_log`.`user_id`, 
                   `mssr_book_borrow_log`.`borrow_sdate`,
                   `mssr_book_borrow_log`.`book_sid` 
            FROM `mssr_book_borrow_log` 
            JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid`=`mssr_idc_book_sticker_level_info`.`book_sid` 
            WHERE 1=1
                AND `mssr_book_borrow_log`.`user_id` IN ({$list_uid})
                AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
                AND `mssr_idc_book_sticker_level_info`.administrator_level>2
                AND `mssr_idc_book_sticker_level_info`.administrator_level<5
            GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid` 
        ";

        $arrys_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


        foreach($arrys_results as $arrys_result){

            $rs_user_id     =(int)$arrys_result['user_id'];
            $rs_borrow_sdate=trim($arrys_result['borrow_sdate']);
            $rs_book_sid=trim($arrys_result['book_sid']);
            $rs_borrow_sdate=date("Y-m",strtotime($rs_borrow_sdate));
            if(array_key_exists($rs_borrow_sdate,$arrys_month))$arrys_month[$rs_borrow_sdate]['bridge_book']=$arrys_month[$rs_borrow_sdate]['bridge_book']+1;
            

             //學生每個月的橋樑書的比例
             $arrys_month[$rs_borrow_sdate]['bridge_book_rate']=round(($arrys_month[$rs_borrow_sdate]['bridge_book']/$arrys_month[$rs_borrow_sdate]['group'])*100,2);

        }


        //學生文字書每個月看的本數

        $sql="
            SELECT `mssr_book_borrow_log`.`user_id`, 
                   `mssr_book_borrow_log`.`borrow_sdate`,
                   `mssr_book_borrow_log`.`book_sid` 
            FROM `mssr_book_borrow_log` 
            JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid`=`mssr_idc_book_sticker_level_info`.`book_sid` 
            WHERE 1=1
                AND `mssr_book_borrow_log`.`user_id` IN ({$list_uid})
                AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
                AND `mssr_idc_book_sticker_level_info`.administrator_level>4
            GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid` 
        ";

     

        $arrys_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

        foreach($arrys_results as $arrys_result){

            $rs_user_id     =(int)$arrys_result['user_id'];
            $rs_borrow_sdate=trim($arrys_result['borrow_sdate']);
            $rs_book_sid=trim($arrys_result['book_sid']);
            $rs_borrow_sdate=date("Y-m",strtotime($rs_borrow_sdate));
            if(array_key_exists($rs_borrow_sdate,$arrys_month))$arrys_month[$rs_borrow_sdate]['words_book']=$arrys_month[$rs_borrow_sdate]['words_book']+1;

            //學生每個月的文字書的比例
             $arrys_month[$rs_borrow_sdate]['words_book_rate']=round(($arrys_month[$rs_borrow_sdate]['words_book']/$arrys_month[$rs_borrow_sdate]['group'])*100,2);


        }


        //======================================
       	//兩星期為區間學生繪本,橋梁書,文字書閱讀狀況
        //======================================

	$two_weeks_data=[];

	$currentDate=date("Y-m-d",strtotime($get_filter_semester_start));
    $endDate=date("Y-m-d",strtotime($get_filter_semester_end));

  
    $key=0;
    $first_time=false;
    while($currentDate < $endDate){
// echo "A:" . $currentDate . "-" . $endDate ."<br>";

  					$two_weeks_data[$key]['start']=$currentDate;
  					$two_weeks_data[$key]['end']=date("Y-m-d",strtotime($currentDate."+2 week"));
  					if ($two_weeks_data[$key]['end'] > $endDate) {
  						$two_weeks_data[$key]['end']=$endDate;
  					}
		  			$two_weeks_data[$key]['total']=0;
		  			$two_weeks_data[$key]['picture_book']=0;
			        $two_weeks_data[$key]['bridge_book']=0;
			        $two_weeks_data[$key]['words_book']=0;
			        $two_weeks_data[$key]['bridge_book_rate']= 0;
                    $two_weeks_data[$key]['words_book_rate']= 0;

			    	$current=date("Y-m-d",strtotime($currentDate."+2 week"));

			    	$currentDate=date("Y-m-d",strtotime($current. "+1 days"));
			  		

			  	$first_time=true;
			  	$key++;
			  
			  	// print_r( $two_weeks_data[$two_weeks]);
			  
           
	}	


	//print_r($two_weeks_data);

     //時間區間顯示

    $time_step=[];

    foreach($two_weeks_data as $ind_key => $val){

  			array_push($time_step,"{$val['start']}"."~"."{$val['end']}");					
  	}
		
   foreach($two_weeks_data as $index_key => $val){    
    

	    	//每兩星期全班所閱讀的書本數 

		    $total_sql="
		            SELECT `user_id`, `borrow_sdate`
		            FROM `mssr_book_borrow_log`
		            WHERE 1=1
		                AND `user_id` IN ({$list_uid})
		                AND `borrow_sdate` BETWEEN '{$val['start']} 00:00:00' AND '{$val['end']} 23:59:59'
		            GROUP BY `user_id`, `book_sid`
		    ";


		    $total_arrys_results=db_result($conn_type='pdo',$conn_mssr,$total_sql,array(),$arry_conn_mssr);


		    foreach($total_arrys_results as $arrys_result){

		            $rs_user_id     =(int)$arrys_result['user_id'];
		            $rs_borrow_sdate=trim($arrys_result['borrow_sdate']);
		            $rs_borrow_sdate=date("Y-m-d",strtotime($rs_borrow_sdate));

		  			
		  			$two_weeks_data[$index_key]['total']+=1;

		  				// echo '<pre>';echo $two_weeks_data[$index_key]['total'];echo '</pre>';
		  			
		  		
		     }


		      //學生繪本兩星期看的本數
	        $picture_book_sql="
	            SELECT `mssr_book_borrow_log`.`user_id`, 
	                   `mssr_book_borrow_log`.`borrow_sdate`,
	                   `mssr_book_borrow_log`.`book_sid` 
	            FROM `mssr_book_borrow_log` 
	            JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid`=`mssr_idc_book_sticker_level_info`.`book_sid` 
	            WHERE 1=1
	                AND `mssr_book_borrow_log`.`user_id` IN ({$list_uid})
	                AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$val['start']} 00:00:00' AND '{$val['end']} 23:59:59'
	                AND `mssr_idc_book_sticker_level_info`.administrator_level<3
                    AND `mssr_idc_book_sticker_level_info`.administrator_level>0
	            GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid` 
	        ";


	        $picture_book_arrys_results=db_result($conn_type='pdo',$conn_mssr,$picture_book_sql,array(),$arry_conn_mssr);

	        foreach($picture_book_arrys_results as $arrys_result){

	            $rs_user_id     =(int)$arrys_result['user_id'];
	            $rs_borrow_sdate=trim($arrys_result['borrow_sdate']);
	            $rs_borrow_sdate=date("Y-m-d",strtotime($rs_borrow_sdate));
	  			$two_weeks_data[$index_key]['picture_book']+=1;

	  				
	  		

	        }

        	//學生橋梁書每兩星期看的本數

	        $bridge_book_sql="
	            SELECT `mssr_book_borrow_log`.`user_id`, 
	                   `mssr_book_borrow_log`.`borrow_sdate`,
	                   `mssr_book_borrow_log`.`book_sid` 
	            FROM `mssr_book_borrow_log` 
	            JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid`=`mssr_idc_book_sticker_level_info`.`book_sid` 
	            WHERE 1=1
	                AND `mssr_book_borrow_log`.`user_id` IN ({$list_uid})
	                AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$val['start']} 00:00:00' AND '{$val['end']} 23:59:59'
	                AND `mssr_idc_book_sticker_level_info`.administrator_level>2
	                AND `mssr_idc_book_sticker_level_info`.administrator_level<5
	            GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid` 
	        ";


	        $bridge_book_arrys_results=db_result($conn_type='pdo',$conn_mssr,$bridge_book_sql,array(),$arry_conn_mssr);


	        foreach($bridge_book_arrys_results as $arrys_result){

	            $rs_user_id     =(int)$arrys_result['user_id'];
	            $rs_borrow_sdate=trim($arrys_result['borrow_sdate']);
	            $rs_book_sid=trim($arrys_result['book_sid']);
	            $rs_borrow_sdate=date("Y-m-d",strtotime($rs_borrow_sdate));

	  			

	  				$two_weeks_data[$index_key]['bridge_book']+=1;
	  					//學生每個月的橋樑書的比例
	  				$two_weeks_data[$index_key]['bridge_book_rate']=round(($two_weeks_data[$index_key]['bridge_book']/$two_weeks_data[$index_key]['total'])*100,2);

	  				
	  			
	             

	        }


	        //學生文字書每兩星期看的本數

	        $words_book_sql="
	            SELECT `mssr_book_borrow_log`.`user_id`, 
	                   `mssr_book_borrow_log`.`borrow_sdate`,
	                   `mssr_book_borrow_log`.`book_sid` 
	            FROM `mssr_book_borrow_log` 
	            JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid`=`mssr_idc_book_sticker_level_info`.`book_sid` 
	            WHERE 1=1
	                AND `mssr_book_borrow_log`.`user_id` IN ({$list_uid})
	                AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$val['start']} 00:00:00' AND '{$val['end']} 23:59:59'
	                AND `mssr_idc_book_sticker_level_info`.administrator_level>4
	            GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid` 
	        ";


	        $words_book_arrys_results=db_result($conn_type='pdo',$conn_mssr,$words_book_sql,array(),$arry_conn_mssr);

	        foreach($words_book_arrys_results as $arrys_result){

	            $rs_user_id     =(int)$arrys_result['user_id'];
	            $rs_borrow_sdate=trim($arrys_result['borrow_sdate']);
	            $rs_book_sid=trim($arrys_result['book_sid']);
	            $rs_borrow_sdate=date("Y-m-d",strtotime($rs_borrow_sdate));
	  			

	  			$two_weeks_data[$index_key]['words_book']+=1;
                $two_weeks_data[$index_key]['words_book_rate']=round(($two_weeks_data[$index_key]['words_book']/$two_weeks_data[$index_key]['total'])*100,2);

	  				



	        }


	}

     


   //======================================
   //一星期為區間學生繪本,橋梁書,文字書閱讀狀況
   //======================================

	$one_weeks_data=[];

	$currentDate=date("Y-m-d",strtotime($get_filter_semester_start));
    $endDate=date("Y-m-d",strtotime($get_filter_semester_end));

  
    $key=0;
    $first_time=false;
    while($currentDate < $endDate){
// echo "A:" . $currentDate . "-" . $endDate ."<br>";

  					$one_weeks_data[$key]['start']=$currentDate;
  					$one_weeks_data[$key]['end']=date("Y-m-d",strtotime($currentDate."+1 week"));
  					if ($one_weeks_data[$key]['end'] > $endDate) {
  						$one_weeks_data[$key]['end']=$endDate;
  					}
		  			$one_weeks_data[$key]['total']=0;
		  			$one_weeks_data[$key]['picture_book']=0;
			        $one_weeks_data[$key]['bridge_book']=0;
			        $one_weeks_data[$key]['words_book']=0;
			        $one_weeks_data[$key]['bridge_book_rate']= 0;
                    $one_weeks_data[$key]['words_book_rate']= 0;

			    	$current=date("Y-m-d",strtotime($currentDate."+1 week"));

			    	$currentDate=date("Y-m-d",strtotime($current. "+1 days"));
			  		

			  	$first_time=true;
			  	$key++;
			  
			  	// print_r( $one_weeks_data[$two_weeks]);
			  
           
	}	


     //時間區間顯示

    $time_step_one_week=[];

    foreach($one_weeks_data as $ind_key => $val){

  			array_push($time_step_one_week,"{$val['start']}"."~"."{$val['end']}");					
  	}



  	foreach($one_weeks_data as $index_key => $val){	
		
       
			    //每一星期全班所閱讀的書本數 

			    $total_sql="
			            SELECT `user_id`, `borrow_sdate`
			            FROM `mssr_book_borrow_log`
			            WHERE 1=1
			                AND `user_id` IN ({$list_uid})
			                AND `borrow_sdate` BETWEEN '{$val['start']} 00:00:00' AND '{$val['end']} 23:59:59'
			            GROUP BY `user_id`, `book_sid`
			    ";



			    $total_arrys_results=db_result($conn_type='pdo',$conn_mssr,$total_sql,array(),$arry_conn_mssr);

			    foreach($total_arrys_results as $arrys_result){

			            $rs_user_id     =(int)$arrys_result['user_id'];
			            $rs_borrow_sdate=trim($arrys_result['borrow_sdate']);
			            $rs_borrow_sdate=date("Y-m-d",strtotime($rs_borrow_sdate));

			  				
			  			$one_weeks_data[$index_key]['total']+=1;

			  				// echo '<pre>';echo $one_weeks_data[$index_key]['total'];echo '</pre>';
			  			
  		
      
     			}

		      //學生繪本每一星期看的本數
		        $picture_book_sql="
		            SELECT `mssr_book_borrow_log`.`user_id`, 
		                   `mssr_book_borrow_log`.`borrow_sdate`,
		                   `mssr_book_borrow_log`.`book_sid` 
		            FROM `mssr_book_borrow_log` 
		            JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid`=`mssr_idc_book_sticker_level_info`.`book_sid` 
		            WHERE 1=1
		                AND `mssr_book_borrow_log`.`user_id` IN ({$list_uid})
		                AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$val['start']} 00:00:00' AND '{$val['end']} 23:59:59'
		                AND `mssr_idc_book_sticker_level_info`.administrator_level<3
                         AND `mssr_idc_book_sticker_level_info`.administrator_level>0
		            GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid` 
		        ";

		        $picture_book_arrys_results=db_result($conn_type='pdo',$conn_mssr,$picture_book_sql,array(),$arry_conn_mssr);

		        foreach($picture_book_arrys_results as $arrys_result){

		            $rs_user_id     =(int)$arrys_result['user_id'];
		            $rs_borrow_sdate=trim($arrys_result['borrow_sdate']);
		            $rs_borrow_sdate=date("Y-m-d",strtotime($rs_borrow_sdate));
		  		

		  			$one_weeks_data[$index_key]['picture_book']+=1;

		  				
		  			

		        }

		        //學生橋梁書每星期看的本數

		        $bridge_book_sql="
		            SELECT `mssr_book_borrow_log`.`user_id`, 
		                   `mssr_book_borrow_log`.`borrow_sdate`,
		                   `mssr_book_borrow_log`.`book_sid` 
		            FROM `mssr_book_borrow_log` 
		            JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid`=`mssr_idc_book_sticker_level_info`.`book_sid` 
		            WHERE 1=1
		                AND `mssr_book_borrow_log`.`user_id` IN ({$list_uid})
		                AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$val['start']} 00:00:00' AND '{$val['end']} 23:59:59'
		                AND `mssr_idc_book_sticker_level_info`.administrator_level>2
		                AND `mssr_idc_book_sticker_level_info`.administrator_level<5
		            GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid` 
		        ";

		        $bridge_book_arrys_results=db_result($conn_type='pdo',$conn_mssr,$bridge_book_sql,array(),$arry_conn_mssr);


		        foreach($bridge_book_arrys_results as $arrys_result){

		            $rs_user_id     =(int)$arrys_result['user_id'];
		            $rs_borrow_sdate=trim($arrys_result['borrow_sdate']);
		            $rs_book_sid=trim($arrys_result['book_sid']);
		            $rs_borrow_sdate=date("Y-m-d",strtotime($rs_borrow_sdate));

		  			$one_weeks_data[$index_key]['bridge_book']+=1;
		  					//學生每個月的橋樑書的比例
		  			$one_weeks_data[$index_key]['bridge_book_rate']=round(($one_weeks_data[$index_key]['bridge_book']/$one_weeks_data[$index_key]['total'])*100,2);

		  				
		  			
		             

		        }


	        //學生文字書每星期看的本數

	        $words_book_sql="
	            SELECT `mssr_book_borrow_log`.`user_id`, 
	                   `mssr_book_borrow_log`.`borrow_sdate`,
	                   `mssr_book_borrow_log`.`book_sid` 
	            FROM `mssr_book_borrow_log` 
	            JOIN `mssr_idc_book_sticker_level_info` ON `mssr_book_borrow_log`.`book_sid`=`mssr_idc_book_sticker_level_info`.`book_sid` 
	            WHERE 1=1
	                AND `mssr_book_borrow_log`.`user_id` IN ({$list_uid})
	                AND `mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$val['start']} 00:00:00' AND '{$val['end']} 23:59:59'
	                AND `mssr_idc_book_sticker_level_info`.administrator_level>4
	            GROUP BY `mssr_book_borrow_log`.`user_id`, `mssr_book_borrow_log`.`book_sid` 
	        ";

	     

	        $words_book_arrys_results=db_result($conn_type='pdo',$conn_mssr,$words_book_sql,array(),$arry_conn_mssr);

	        foreach($words_book_arrys_results as $arrys_result){

	            $rs_user_id     =(int)$arrys_result['user_id'];
	            $rs_borrow_sdate=trim($arrys_result['borrow_sdate']);
	            $rs_book_sid=trim($arrys_result['book_sid']);
	            $rs_borrow_sdate=date("Y-m-d",strtotime($rs_borrow_sdate));
				$one_weeks_data[$index_key]['words_book']+=1;

                $one_weeks_data[$index_key]['words_book_rate']=round(($one_weeks_data[$index_key]['words_book']/$one_weeks_data[$index_key]['total'])*100,2);

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
    <link rel="stylesheet" type="text/css" href="../../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/jquery/plugin/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/jquery/plugin/func/flot/excanvas.min.js"></script>
    <script type="text/javascript" src="../../../../../../lib/jquery/plugin/func/flot/jquery.flot.js"></script>
    <script type="text/javascript" src="../../../../../../lib/jquery/plugin/func/flot/jquery.flot.pie.js"></script>

    <script type="text/javascript" src="../../../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/js/table/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/js/array/code.js"></script>
    <script type="text/javascript" src="../../../../../../inc/datepicker/WdatePicker.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../../css/def.css" media="all" />
   	<script src="dist/echarts.js"></script>
	<!-- <script src="dist/echarts.min.js"></script> -->
	<script src="theme/macarons.js"></script><!-- 主題 -->
	<script src="theme/infographic.js"></script><!-- 主題 -->
    <style>
 
       
        *{
            font-size: 11pt;
            margin: 10px auto;
        } 

        .title {
				margin-top: 50px;
				padding: 20px;

        }

        .title span{

        	margin-top: 20px;
        	font-family: 微軟正黑體;
        	font-size: 18px;
        	font-weight: 600;
        }
    </style>
</Head>

<Body>

	<?php if($sess_school_code=='idc'){ ?>
    <div class="mod_data_tbl_outline" style="margin-top:35px;">
                    <table id="mod_data_tbl" border="0" width="95%" height="40px" cellpadding="5" cellspacing="0" style="" class="font-weight1 font-family1 fc_green0">
                        <tr align="center" valign="middle" class="fsize_16">
                            <td align="center" width="470px">
                                <span class="fsize_16">
                                    <span class="fc_red1">●請注意! 此頁為新開頁面，</span>
                                    <input type="button" value="請按我關閉" class="ibtn_gr9030" onclick="parent.close();" onmouseover="this.style.cursor='pointer'">
                                </span>
                            </td>
                        </tr>
                    </table>
    </div>
	<div class="title">	<span>一學期單月學生登記橋梁書狀況</span><div id="month_about_book" style="width: 1000px; height: 400px;"></div></div>
	<div class="title">	<span >一學期每星期學生登記橋梁書狀況</span><div id="one_week_about_book" style="width:3595px; height: 500px;"></div></div>
	<div class="title">	<span >一學期兩星期學生登記橋梁書狀況</span><div id="two_week_about_book" style="width: 2080px; height: 500px; "></div></div>
	<div id="container" style="width:800px;"></div>
    <?php }else{ ?>
    	<div style="width:800px;">
			<input type="button" value="關閉頁面" style="width:800px;height:35px;margin:20px auto;cursor:pointer;"onclick="parent.$.unblockUI();">
		</div>
		<div id="container" style="width:800px;"></div>

    <?php }?>
</Body>
<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var categories=[];
    var group=[];
    var frequency=[];
    <?php foreach($arrys_month as $month=>$arry_month):?>
        categories.push('<?php echo $month;?>');
        group.push(<?php echo $arry_month["group"];?>);
        frequency.push(<?php echo $arry_month["frequency"];?>);
    <?php endforeach;?>



    var bridge_book=[];
    var bridge_book_rate=[];
    var picture_book=[];
    var words_book=[];
    var words_book_rate=[];
    <?php foreach($arrys_month as $month=>$arry_month):?>
        bridge_book.push(<?php echo $arry_month["bridge_book"];?>);
        bridge_book_rate.push(<?php echo $arry_month["bridge_book_rate"];?>);
        picture_book.push(<?php echo $arry_month["picture_book"] ;?>);
        words_book.push(<?php echo $arry_month["words_book"] ;?>);
        words_book_rate.push(<?php echo $arry_month['words_book_rate'];?>);
    <?php endforeach;?>

    console.log(bridge_book);
    console.log(bridge_book_rate);
    console.log(picture_book);
    console.log(words_book);

    var time=[];
    <?php foreach($time_step as $key=>$arry):?>
		time.push('<?php echo $arry;?>');
    <?php endforeach;?>
   
 	var two_weeks_total=[];
 	var two_weeks_picture_book=[];
 	var two_weeks_bridge_book=[];
 	var two_weeks_words_book=[];
 	var two_weeks_bridge_book_rate=[];
    var two_weeks_words_book_rate=[];
  	<?php foreach($two_weeks_data as $key=>$data):?>
		 	two_weeks_total.push(<?php echo $data['total'];?>);
		 	two_weeks_picture_book.push(<?php echo (int)$data['picture_book'];?>);
		 	two_weeks_bridge_book.push(<?php echo (int)$data['bridge_book'];?>);
		 	two_weeks_words_book.push(<?php echo (int)$data['words_book'];?>);
		 	two_weeks_bridge_book_rate.push(<?php echo (int)$data['bridge_book_rate'];?>);
            two_weeks_words_book_rate.push(<?php echo (int)$data['words_book_rate'];?>);

   <?php endforeach;?>

    console.log(time);
    	
    console.log(two_weeks_total);
    console.log(two_weeks_picture_book);
    console.log(two_weeks_bridge_book);
    console.log(two_weeks_words_book);
    console.log(two_weeks_bridge_book_rate);

     var time_one_week=[];
    <?php foreach($time_step_one_week as $key=>$arry):?>
		time_one_week.push('<?php echo $arry;?>');
    <?php endforeach;?>
   
 	var one_weeks_total=[];
 	var one_weeks_picture_book=[];
 	var one_weeks_bridge_book=[];
 	var one_weeks_words_book=[];
 	var one_weeks_bridge_book_rate=[];
    var one_weeks_words_book_rate=[];
  	<?php foreach($one_weeks_data as $key=>$data):?>
		 	one_weeks_total.push(<?php echo (int)$data['total'];?>);
		 	one_weeks_picture_book.push(<?php echo (int)$data['picture_book'];?>);
		 	one_weeks_bridge_book.push(<?php echo (int)$data['bridge_book'];?>);
		 	one_weeks_words_book.push(<?php echo (int)$data['words_book'];?>);
		 	one_weeks_bridge_book_rate.push(<?php echo (int)$data['bridge_book_rate'];?>);
            one_weeks_words_book_rate.push(<?php echo (int)$data['words_book_rate'];?>);


   <?php endforeach;?>

 
    	
    // console.log(one_weeks_total);
    // console.log(one_weeks_picture_book);
    // console.log(one_weeks_bridge_book);
    // console.log(one_weeks_words_book);
    // console.log(one_weeks_bridge_book_rate);



    $(function () {
        $('#container').highcharts({
            chart: {type: 'column'},
            title: {text: '學生登記資料 (本數/次數)'},
            subtitle: {text: ''},
            xAxis: {
                categories:categories,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: '登記本數'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                },
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.f}'
                    }
                }
            },
            series: [
                {
                    name: '當月登記本數',
                    data: group
                },
                {
                    name: '當月登記次數',
                    data: frequency
                }
            ]
        });
    });
    Highcharts.chart('month_about_book', {
                 colors: ['#d6d4d4','#5b9bd5', '#df6613', '#5b9bd5',  '#df6613', '#D2A2CC', '#d3a4ff', 
                '#EAC100', '#FF9224'],
                chart: {
                   
                    zoomType: 'xy'
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: ''
                },
                xAxis: [{
                    categories: categories,
                    crosshair: true
                }],
                yAxis: [{ 

                    //X轴内刻度间隔5-1个显示下一个
                   
                    labels: {     
                        align: 'right',
                        x: 985,
                        y: 0,
                        
                        format: '{value}%',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                       
                        }
                    }, // Primary yAxis
                    title: {
                        text: '',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    
                }, { // Secondary yAxis
      
                    labels: {
                        align: 'left',
                        x: -980,
                        y: 0,
                        format: '{value} 本',
                        style: {
                            color: Highcharts.getOptions().colors[0],
                          
                        }
                    },
                 
                    title: {
                        text: '',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    opposite: true
                }],
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
               },
                plotOptions: {

                    column: {
                        pointPadding: 0.1,
                        borderWidth: 0,
                        pointWidth:20,
                        dataLabels: {
		                    enabled: true,
		                    allowOverlap: true,
		                    useHTML:true,
		                    formatter: function() {
		                        return '<div class="datalabelInside" style="position: relative; top: 0px; left: -5px">'+ this.y +'</div>';
		                    }
		                }
                    },
	                spline: {

		                lineWidth:2,
						dataLabels: 
						{
							enabled: true,
							allowOverlap: true,
							formatter:function(){

		                           return '<div class="datalabelInside" style="position: relative; top: 30px; left: -5px; color: #C4E1E1;">'+ this.y +'%</div>';
		                        
							}
						},
						enableMouseTracking: true
				
		             }
            	},
                series: [{
                    name: '繪本書本數',
                    type: 'column',
                    yAxis: 1,
                    data: picture_book,
                    tooltip: {
                        valueSuffix: ' 本'
                    }

                }, 
                {
                    name: '橋梁書本數',
                    type: 'column',
                    yAxis: 1,
                    data: bridge_book,
                    tooltip: {
                        valueSuffix: ' 本'
                    }

                },

                {
                    name: '文字書本數',
                    type: 'column',
                    yAxis: 1,
                    data: words_book,
                    tooltip: {
                        valueSuffix: ' 本'
                    }

                },


                {
                    name: '橋梁書比例',
                    type: 'spline',
                    data: bridge_book_rate,
                    tooltip: {
                        valueSuffix: '%'
                    }
                },
                {
                    name: '文字書比例',
                    type: 'spline',
                    data: words_book_rate,
                    tooltip: {
                        valueSuffix: '%'
                    }
                }]
    });

    //===============================================
    //////////////////////兩星期/////////////////////
    //===============================================

     Highcharts.chart('two_week_about_book', {
                  colors: ['#d6d4d4','#5b9bd5', '#df6613', '#5b9bd5',  '#df6613', '#D2A2CC', '#d3a4ff', 
                '#EAC100', '#FF9224'],
                chart: {
                   
                    zoomType: 'xy',
                    width:2080
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: ''
                },
                xAxis: [{
                    categories: time,
                    crosshair: true
                }],
                yAxis: [{ 

                    //X轴内刻度间隔5-1个显示下一个
                   
                    labels: {     
                        align: 'right',
                        x: 2070,
                        y: 0,
                        
                        format: '{value}%',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                       
                        }
                    }, // Primary yAxis
                    title: {
                        text: '',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    
                }, { // Secondary yAxis
      
                    labels: {
                        align: 'left',
                        x: -2070,
                        y: 0,
                        format: '{value} 本',
                        style: {
                            color: Highcharts.getOptions().colors[0],
                          
                        }
                    },
                 
                    title: {
                        text: '',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    opposite: true
                }],
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:5px;"><b>{point.y:.1f}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
               },
               plotOptions: {

                    column: {
                        pointPadding: 0.1,
                        borderWidth: 0,
                        pointWidth:20,
                        dataLabels: {
		                    enabled: true,
		                    allowOverlap: true,
		                    useHTML:true,
		                    formatter: function() {
		                        return '<div class="datalabelInside" style="position: relative; top: 0px; left: -5px">'+ this.y +'</div>';
		                    }
		                }
                    },
	                spline: {

		                lineWidth:2,
						dataLabels: 
						{
							enabled: true,
							allowOverlap: true,
							formatter:function(){

		                           return '<div class="datalabelInside" style="position: relative; top: 30px; left: -5px; color: #C4E1E1;">'+ this.y +'%</div>';
		                        
							}
						},
						enableMouseTracking: true
				
		             }
            	},
                series: [{
                    name: '繪本書本數',
                    type: 'column',
                    yAxis: 1,
                    data: two_weeks_picture_book,
                    tooltip: {
                        valueSuffix: ' 本'
                    }

                }, 
                {
                    name: '橋梁書本數',
                    type: 'column',
                    yAxis: 1,
                    data: two_weeks_bridge_book,
                    tooltip: {
                        valueSuffix: ' 本'
                    }

                },

                {
                    name: '文字書本數',
                    type: 'column',
                    yAxis: 1,
                    data: two_weeks_words_book,
                    tooltip: {
                        valueSuffix: ' 本'
                    }

                },


                {
                    name: '橋梁書比例',
                    type: 'spline',
                    data: two_weeks_bridge_book_rate,
                    tooltip: {
                        valueSuffix: '%'
                    }
                },
                {
                    name: '文字書比例',
                    type: 'spline',
                    data: words_book_rate,
                    tooltip: {
                        valueSuffix: '%'
                    }
                }]
    });

    //===============================================
    //////////////////////一星期/////////////////////
    //===============================================
         Highcharts.chart('one_week_about_book', {
                  colors: ['#d6d4d4','#5b9bd5', '#df6613', '#5b9bd5',  '#df6613', '#D2A2CC', '#d3a4ff', 
                '#EAC100', '#FF9224'],
                chart: {
                   
                    zoomType: 'xy',
                    width:3595
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: ''
                },
                xAxis: [{
                    categories: time_one_week,
                    crosshair: true
                }],
                yAxis: [{ 

                    //X轴内刻度间隔5-1个显示下一个
                   
                    labels: {     
                        align: 'right',
                        x: 3590,
                        y: 0,
                        
                        format: '{value}%',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                       
                        }
                    }, // Primary yAxis
                    title: {
                        text: '',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    
                }, { // Secondary yAxis
      
                    labels: {
                        align: 'left',
                        x: -3580,
                        y: 0,
                        format: '{value} 本',
                        style: {
                            color: Highcharts.getOptions().colors[0],
                          
                        }
                    },
                 
                    title: {
                        text: '',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    opposite: true
                }],
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:5px;"><b>{point.y:.1f}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
               },
                plotOptions: {

                    column: {
                        pointPadding: 0.1,
                        borderWidth: 0,
                        pointWidth:20,
                        dataLabels: {
		                    enabled: true,
		                    allowOverlap: true,
		                    useHTML:true,
		                    formatter: function() {
		                        return '<div class="datalabelInside" style="position: relative; top: 0px; left: -5px">'+ this.y +'</div>';
		                    }
		                }
                    },
	                spline: {

		                lineWidth:2,
						dataLabels: 
						{
							enabled: true,
							allowOverlap: true,
							formatter:function(){

		                           return '<div class="datalabelInside" style="position: relative; top: 30px; left: -5px; color: #C4E1E1;">'+ this.y +'%</div>';
		                        
							}
						},
						enableMouseTracking: true
				
		             }
            	},
                series: [{
                    name: '繪本書本數',
                    type: 'column',
                    yAxis: 1,
                    data: one_weeks_picture_book,
                    tooltip: {
                        valueSuffix: ' 本'
                    }

                }, 
                {
                    name: '橋梁書本數',
                    type: 'column',
                    yAxis: 1,
                    data: one_weeks_bridge_book,
                    tooltip: {
                        valueSuffix: ' 本'
                    }

                },

                {
                    name: '文字書本數',
                    type: 'column',
                    yAxis: 1,
                    data: one_weeks_words_book,
                    tooltip: {
                        valueSuffix: ' 本'
                    }

                },


                {
                    name: '橋梁書比例',
                    type: 'spline',
                    data: one_weeks_bridge_book_rate,
                    tooltip: {
                        valueSuffix: '%'
                    }
                },
                {
                    name: '文字書比例',
                    type: 'spline',
                    data: words_book_rate,
                    tooltip: {
                        valueSuffix: '%'
                    }
                }]
    });



//     $(document).ready(function(){

//   	two_weeks();
//   	one_week();

//   	function two_weeks()
// 	{
			
// 		var myChart_main_line = echarts.init(document.getElementById('two_week_about_book'));	
// 			option = {
// 			    title: {
        				
//         				text: '每星期學生閱讀書籍分類'
        
//     			},
// 			    tooltip: {
// 			        trigger: 'axis',
// 			        axisPointer: {
// 			            type: 'cross',
// 			            crossStyle: {
// 			                color: '#999'
// 			            },
// 			            label:{

// 			                        show: true,
// 			                        position: 'top',
// 			                        formatter: '{b}\n{c}'
			                    

// 			            }
// 			        }
// 			    },
// 			    toolbox: {
// 			        feature: {
// 			            dataView: {show: true, readOnly: false},
// 			            magicType: {show: true, type: ['line', 'bar']},
// 			            restore: {show: true},
// 			            saveAsImage: {show: true}
// 			        }
// 			    },
// 			    legend: {
// 			        data:['繪本','橋梁書','文字書','橋梁書比例']
// 			    },
// 			    xAxis: [
// 			        {
// 			            type: 'category',
// 			            data: time,
// 			            axisPointer: {
// 			                type: 'shadow'
// 			            }
// 			        }
// 			    ],
// 			    yAxis: [
// 			        {
// 			            type: 'value',
// 			            name: '本數',
// 			            min: 0,
// 			            // max: 250,
// 			            interval: 100,
// 			            axisLabel: {
// 			                formatter: '{value} 本'
// 			            }
// 			        },
// 			        {
// 			            type: 'value',
// 			            name: '比例',
// 			            min: 0,
// 			            max: 100,
// 			            interval: 5,
// 			            axisLabel: {
// 			                formatter: '{value} %'
// 			            }
// 			        }
// 			    ],
// 			    series: [
// 			        {
// 			            name:'繪本書',
// 			            type:'bar',
// 			            data:two_weeks_picture_book
			       
// 			        },
// 			        {
// 			            name:'橋梁書',
// 			            type:'bar',
// 			            data:two_weeks_bridge_book
			       
			             
// 			        },
// 			        {
// 			            name:'文字書',
// 			            type:'bar',
// 			            data:two_weeks_words_book
			          
// 			        },
// 			        {
// 			            name:'橋梁書比例',
// 			            type:'line',
// 			            yAxisIndex: 1,
// 			            data:two_weeks_bridge_book_rate
// 			        }
// 			    ]
// 			};


//         myChart_main_line.setOption(option);
// 	}
	  	

//   	function one_week()
// 	{
			
// 		var myChart_main_line = echarts.init(document.getElementById('one_week_about_book'));	
// 			option = {
// 				 title: {
        				
//         				text: '每星期學生閱讀書籍分類'
        
//     			},
// 			    tooltip: {
// 			        trigger: 'axis',
// 			        axisPointer: {
// 			            type: 'cross',
// 			            crossStyle: {
// 			                color: '#999'
// 			            },
// 			            label:{

// 			                        show: true,
// 			                        position: 'top',
// 			                        formatter: '{b}\n{c}'
			                    

// 			            }
// 			        }
// 			    },
// 			    toolbox: {
// 			        feature: {
// 			            dataView: {show: true, readOnly: false},
// 			            magicType: {show: true, type: ['line', 'bar']},
// 			            restore: {show: true},
// 			            saveAsImage: {show: true}
// 			        }
// 			    },
// 			    legend: {
// 			        data:['繪本','橋梁書','文字書','橋梁書比例']
// 			    },
// 			    xAxis: [
// 			        {
// 			            type: 'category',
// 			            data: time_one_week,
// 			            axisPointer: {
// 			                type: 'shadow'
// 			            }
// 			        }
// 			    ],
// 			    yAxis: [
// 			        {
// 			            type: 'value',
// 			            name: '本數',
// 			            min: 0,
// 			            // max: 250,
// 			            interval: 100,
// 			            axisLabel: {
// 			                formatter: '{value} 本'
// 			            }
// 			        },
// 			        {
// 			            type: 'value',
// 			            name: '比例',
// 			            min: 0,
// 			            max: 100,
// 			            interval: 5,
// 			            axisLabel: {
// 			                formatter: '{value} %'
// 			            }
// 			        }
// 			    ],
// 			    series: [

// 			        {
// 			            name:'繪本書',
// 			            type:'bar',
// 			            data:one_weeks_picture_book
			       
// 			        },
// 			        {
// 			            name:'橋梁書',
// 			            type:'bar',
// 			            data:one_weeks_bridge_book
			       
			             
// 			        },
// 			        {
// 			            name:'文字書',
// 			            type:'bar',
// 			            data:one_weeks_words_book
			          
// 			        },
// 			        {
// 			            name:'橋梁書比例',
// 			            type:'line',
// 			            yAxisIndex: 1,
// 			            data:one_weeks_bridge_book_rate
// 			        }
// 			    ]
// 			};


//         myChart_main_line.setOption(option);
// 	}


// });

</script>
</Html>

<?php
//-------------------------------------------------------
//page_hrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>
<?php };?>