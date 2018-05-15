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

        //閱讀比例圓餅圖
        $category_arry_book_borrow=array();
        $sql="
            SELECT
                `mssr`.`mssr_book_category`.`cat_name`
            FROM `mssr`.`mssr_book_category`
            WHERE 1=1
                AND `mssr`.`mssr_book_category`.`cat2_id`    =1
                AND `mssr`.`mssr_book_category`.`cat3_id`    =1
                AND `mssr`.`mssr_book_category`.`cat_state`  ='啟用'
                AND `mssr`.`mssr_book_category`.`school_code`='{$sess_school_code}'
        ";
        $category_arrys_book_borrow=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
        if(!empty($category_arrys_book_borrow)){
            foreach($category_arrys_book_borrow as $arry_val){
                $category_arry_book_borrow[trim($arry_val['cat_name'])]=0;
            }
        }

        $arrys_month=[];
        foreach(diffdate($get_filter_semester_start,$get_filter_semester_end) as $month){
            $arrys_month[$month]['group']=$category_arry_book_borrow;
            //$arrys_month[$month]['frequency']=0;
        }

        $category_users_list=$list_uid;
        $sql="
            SELECT
                `mssr`.`mssr_book_borrow_log`.`book_sid`,
                `mssr`.`mssr_book_borrow_log`.`borrow_sdate`
            FROM `mssr`.`mssr_book_borrow_log`
            WHERE 1=1
                AND `mssr`.`mssr_book_borrow_log`.`user_id` IN ($category_users_list)
                AND `mssr`.`mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$get_filter_semester_start} 00:00:00' AND '{$get_filter_semester_end} 23:59:59'
            GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id` , `mssr`.`mssr_book_borrow_log`.`book_sid`
        ";
        $category_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
        if(!empty($category_results)){
            foreach($category_results as $category_result){
                $rs_book_sid=trim($category_result['book_sid']);
                $rs_borrow_sdate=trim($category_result['borrow_sdate']);
                $rs_borrow_sdate=date("Y-m",strtotime($rs_borrow_sdate));
                $category_arry_books[$rs_borrow_sdate][]=$rs_book_sid;
            }
            foreach($category_arry_books as $rs_borrow_sdate=>$category_arry_book){
                $rs_borrow_sdate=trim($rs_borrow_sdate);
                $category_books_list=implode("','",$category_arry_book);
                //echo "<Pre>";print_r($category_books_list);echo "</Pre>";
                $sql="
                    SELECT
                        `mssr`.`mssr_book_category`.`cat_name`
                    FROM `mssr`.`mssr_book_category`
                        INNER JOIN `mssr`.`mssr_book_category_rev` ON
                        `mssr`.`mssr_book_category`.`cat_code`=`mssr`.`mssr_book_category_rev`.`cat_code`
                    WHERE 1=1
                        AND `mssr`.`mssr_book_category`.`cat2_id`    =1
                        AND `mssr`.`mssr_book_category`.`cat3_id`    =1
                        AND `mssr`.`mssr_book_category`.`cat_state`  ='啟用'
                        AND `mssr`.`mssr_book_category`.`school_code`='{$sess_school_code}'
                        AND `mssr`.`mssr_book_category_rev`.`book_sid` IN ('{$category_books_list}')
                ";
                //echo "<Pre>";print_r($sql);echo "</Pre>";
                $category_arrys_book_borrow_info=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                //echo "<Pre>";print_r($category_arrys_book_borrow_info);echo "</Pre>";
                if(!empty($category_arrys_book_borrow_info)){
                    foreach($category_arrys_book_borrow_info as $category_arry_book_borrow_info){
                        $rs_cat_name=trim($category_arry_book_borrow_info['cat_name']);
                        //echo "<Pre>";print_r($rs_borrow_sdate);echo "</Pre>";
                        //echo "<Pre>";print_r($rs_cat_name);echo "</Pre>";
                        if(array_key_exists($rs_borrow_sdate,$arrys_month)&&array_key_exists($rs_cat_name,$arrys_month[$rs_borrow_sdate]['group'])){
                            $arrys_month[$rs_borrow_sdate]['group'][$rs_cat_name]=(int)($arrys_month[$rs_borrow_sdate]['group'][$rs_cat_name]+1);
                        }
                    }
                }
                //$arrys_month[$rs_borrow_sdate]['group']['未分類']=(int)(count($category_arry_book)-count($category_arrys_book_borrow_info));
            }
        }
        foreach($arrys_month as $rs_month=>$arry_month){
            foreach($arry_month as $group=>$arry_val){
                foreach($arry_val as $cat_name=>$val){
                    if((int)$val>0){
                        $arrys_month[$rs_month][$group][$cat_name]=($val/array_sum($arry_val)*100);
                    }
                }
            }
        }

        //折線圖資料整理
        $series=[];
        foreach($arrys_month as $rs_month=>$arry_month){
            foreach($arry_month as $group=>$arry_val){
                foreach($arry_val as $cat_name=>$val){
                    if($cat_name=='未分類')continue;
                    if(!array_key_exists($cat_name,$series))$series[$cat_name]=[];
                    $series[$cat_name][]=$val;
                }
            }
        }
//echo "<Pre>";print_r(($series));echo "</Pre>";
//echo "<Pre>";print_r($arrys_month);echo "</Pre>";
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
    <style>
        *{
            font-size: 12pt;
        }
    </style>
</Head>

<Body>
    <div style="width:800px;">
        <input type="button" value="關閉頁面" style="width:800px;height:35px;margin:20px auto;cursor:pointer;"
        onclick="parent.$.unblockUI();">
    </div>
    <div id="container" style="width:800px;"></div>
</Body>
<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var categories=[];
    var group=[];
    var series=[];

    <?php foreach($arrys_month as $month=>$arry_month):?>
        categories.push('<?php echo $month;?>');
        <?php foreach($arry_month as $group=>$arry_val):?>
            <?php foreach($arry_val as $cat_name=>$val):?>

            <?php endforeach;?>
        <?php endforeach;?>
    <?php endforeach;?>

    <?php foreach($series as $cat_name=>$serie):?>
        var serie=new Object;
        var arry_val=[];
        <?php foreach($serie as $val):?>
            var val=formatFloat(parseFloat('<?php echo $val;?>'),2);
            arry_val.push(val);
        <?php endforeach;?>
        serie.name='<?php echo trim($cat_name);?>';
        serie.data=arry_val;
        series.push(serie);
    <?php endforeach;?>


    function formatFloat(num, pos){
        var size = Math.pow(10, pos);
        return Math.round(num * size) / size;
    }


    $(function () {
        $('#container').highcharts({
            chart: {type: 'line'},
            title: {text: '學生登記書本類別'},
            subtitle: {text: ''},
            xAxis: {
                categories:categories,
                crosshair: true
            },
            yAxis: {
                title: {
                    text: '百分比(%)'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                valueSuffix: '%'
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                },
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                },
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.f} %'
                    }
                }
            },
            series: series
        });
    });

</script>
</Html>

<?php
//-------------------------------------------------------
//page_hrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>
<?php };?>