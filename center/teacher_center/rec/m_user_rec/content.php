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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_rec');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //SESSION
        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用

        $get_filter_semester_start=(isset($_GET['filter_semester_start']))?trim($_GET['filter_semester_start']):'';
        $get_filter_semester_end  =(isset($_GET['filter_semester_end']))?trim($_GET['filter_semester_end']):'';

        if(isset($_SESSION['m_user_rec']['filter'])){
            $filter=$_SESSION['m_user_rec']['filter'];

            if(isset($_SESSION['m_user_rec']['class_code'])&&(trim($_SESSION['m_user_rec']['class_code'])!=='')){
                $q_class_code=mysql_prep(trim($_SESSION['m_user_rec']['class_code']));

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
        if(isset($_SESSION['m_user_rec']['query_fields'])){
            $query_fields=$_SESSION['m_user_rec']['query_fields'];
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

        if(isset($_SESSION['m_user_rec']['class_code'])&&trim($_SESSION['m_user_rec']['class_code'])!==''){
            $_SESSION['teacher_center']['class_code']=trim($_SESSION['m_user_rec']['class_code']);
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

        //---------------------------------------------------
        //SQL查詢
        //---------------------------------------------------

            if($choose_identity_flag){

                $curdate=date("Y-m-d");
                if(isset($sess_login_info['school_code']))$sess_school_code=mysql_prep($sess_school_code);

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
                                        0 AS `number`,
                                        `teacher`.`start`,
                                        `teacher`.`end`
                                    FROM `member`
                                        INNER JOIN `teacher`
                                        ON `member`.`uid`=`teacher`.`uid`
                                    WHERE 1=1
                                        AND `teacher`.`start` <= '{$curdate}'
                                        AND `teacher`.`end` >= '{$curdate}'
                                        AND `teacher`.`class_code`='{$q_class_code}'

                                UNION ALL

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
                                        0 AS `number`,
                                        `teacher`.`start`,
                                        `teacher`.`end`
                                    FROM `member`
                                        INNER JOIN `teacher`
                                        ON `member`.`uid`=`teacher`.`uid`
                                    WHERE 1=1
                                        AND `teacher`.`start` <= '{$curdate}'
                                        AND `teacher`.`end` >= '{$curdate}'
                                        AND `teacher`.`class_code`='{$q_class_code}'

                                UNION ALL

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
                                            0 AS `number`,
                                            `teacher`.`start`,
                                            `teacher`.`end`
                                        FROM `member`
                                            INNER JOIN `teacher`
                                            ON `member`.`uid`=`teacher`.`uid`
                                        WHERE 1=1
                                            AND `teacher`.`class_code`='{$q_class_code}'
                                            AND `member`.`permission`<>'x'

                                    UNION ALL

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
                                            0 AS `number`,
                                            `teacher`.`start`,
                                            `teacher`.`end`
                                        FROM `member`
                                            INNER JOIN `teacher`
                                            ON `member`.`uid`=`teacher`.`uid`
                                        WHERE 1=1
                                            AND `teacher`.`start` <= '{$curdate}'
                                            AND `teacher`.`end` >= '{$curdate}'
                                            AND `teacher`.`class_code`='{$q_class_code}'

                                    UNION ALL

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
                                            0 AS `number`,
                                            `teacher`.`start`,
                                            `teacher`.`end`
                                        FROM `member`
                                            INNER JOIN `teacher`
                                            ON `member`.`uid`=`teacher`.`uid`
                                        WHERE 1=1
                                            AND `teacher`.`class_code`='{$q_class_code}'
                                            AND `member`.`permission`<>'x'

                                    UNION ALL

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
                                            0 AS `number`,
                                            `teacher`.`start`,
                                            `teacher`.`end`
                                        FROM `member`
                                            INNER JOIN `teacher`
                                            ON `member`.`uid`=`teacher`.`uid`
                                        WHERE 1=1
                                            AND `teacher`.`start` <= '{$curdate}'
                                            AND `teacher`.`end` >= '{$curdate}'
                                            AND `teacher`.`class_code`='{$q_class_code}'

                                    UNION ALL

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
                                    0 AS `number`,
                                    `teacher`.`start`,
                                    `teacher`.`end`
                                FROM `member`
                                    INNER JOIN `teacher`
                                    ON `member`.`uid`=`teacher`.`uid`
                                WHERE 1=1
                                    AND `teacher`.`start` <= '{$curdate}'
                                    AND `teacher`.`end` >= '{$curdate}'
                                    AND `teacher`.`class_code`='{$sess_class_code}'

                            UNION ALL

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
                                            0 AS `number`,
                                            `teacher`.`start`,
                                            `teacher`.`end`
                                        FROM `member`
                                            INNER JOIN `teacher`
                                            ON `member`.`uid`=`teacher`.`uid`
                                        WHERE 1=1
                                            AND `teacher`.`class_code`='{$q_class_code}'
                                            AND `member`.`permission`<>'x'

                                    UNION ALL

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
                                            0 AS `number`,
                                            `teacher`.`start`,
                                            `teacher`.`end`
                                        FROM `member`
                                            INNER JOIN `teacher`
                                            ON `member`.`uid`=`teacher`.`uid`
                                        WHERE 1=1
                                            AND `teacher`.`start` <= '{$curdate}'
                                            AND `teacher`.`end` >= '{$curdate}'
                                            AND `teacher`.`class_code`='{$q_class_code}'

                                    UNION ALL

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
                                            0 AS `number`,
                                            `teacher`.`start`,
                                            `teacher`.`end`
                                        FROM `member`
                                            INNER JOIN `teacher`
                                            ON `member`.`uid`=`teacher`.`uid`
                                        WHERE 1=1
                                            AND `teacher`.`class_code`='{$q_class_code}'
                                            AND `member`.`permission`<>'x'

                                    UNION ALL

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
                                            0 AS `number`,
                                            `teacher`.`start`,
                                            `teacher`.`end`
                                        FROM `member`
                                            INNER JOIN `teacher`
                                            ON `member`.`uid`=`teacher`.`uid`
                                        WHERE 1=1
                                            AND `teacher`.`start` <= '{$curdate}'
                                            AND `teacher`.`end` >= '{$curdate}'
                                            AND `teacher`.`class_code`='{$q_class_code}'

                                    UNION ALL

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
                                    0 AS `number`,
                                    `teacher`.`start`,
                                    `teacher`.`end`
                                FROM `member`
                                    INNER JOIN `teacher`
                                    ON `member`.`uid`=`teacher`.`uid`
                                WHERE 1=1
                                    AND `teacher`.`start` <= '{$curdate}'
                                    AND `teacher`.`end` >= '{$curdate}'
                                    AND `teacher`.`class_code`='{$sess_class_code}'

                            UNION ALL

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
                                            0 AS `number`,
                                            `teacher`.`start`,
                                            `teacher`.`end`
                                        FROM `member`
                                            INNER JOIN `teacher`
                                            ON `member`.`uid`=`teacher`.`uid`
                                        WHERE 1=1
                                            AND `teacher`.`class_code`='{$q_class_code}'
                                            AND `member`.`permission`<>'x'

                                    UNION ALL

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
                                            0 AS `number`,
                                            `teacher`.`start`,
                                            `teacher`.`end`
                                        FROM `member`
                                            INNER JOIN `teacher`
                                            ON `member`.`uid`=`teacher`.`uid`
                                        WHERE 1=1
                                            AND `teacher`.`start` <= '{$curdate}'
                                            AND `teacher`.`end` >= '{$curdate}'
                                            AND `teacher`.`class_code`='{$q_class_code}'

                                    UNION ALL

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
                                            0 AS `number`,
                                            `teacher`.`start`,
                                            `teacher`.`end`
                                        FROM `member`
                                            INNER JOIN `teacher`
                                            ON `member`.`uid`=`teacher`.`uid`
                                        WHERE 1=1
                                            AND `teacher`.`class_code`='{$q_class_code}'
                                            AND `member`.`permission`<>'x'

                                    UNION ALL

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
                                            0 AS `number`,
                                            `teacher`.`start`,
                                            `teacher`.`end`
                                        FROM `member`
                                            INNER JOIN `teacher`
                                            ON `member`.`uid`=`teacher`.`uid`
                                        WHERE 1=1
                                            AND `teacher`.`start` <= '{$curdate}'
                                            AND `teacher`.`end` >= '{$curdate}'
                                            AND `teacher`.`class_code`='{$q_class_code}'

                                    UNION ALL

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
                                            0 AS `number`,
                                            `teacher`.`start`,
                                            `teacher`.`end`
                                        FROM `member`
                                            INNER JOIN `teacher`
                                            ON `member`.`uid`=`teacher`.`uid`
                                        WHERE 1=1
                                            AND `teacher`.`class_code`='{$q_class_code}'
                                            AND `member`.`permission`<>'x'

                                    UNION ALL

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
                                            0 AS `number`,
                                            `teacher`.`start`,
                                            `teacher`.`end`
                                        FROM `member`
                                            INNER JOIN `teacher`
                                            ON `member`.`uid`=`teacher`.`uid`
                                        WHERE 1=1
                                            AND `teacher`.`start` <= '{$curdate}'
                                            AND `teacher`.`end` >= '{$curdate}'
                                            AND `teacher`.`class_code`='{$q_class_code}'

                                    UNION ALL

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
                            }
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

        global $sess_login_info;
        global $arrys_result;
        global $config_arrys;
        global $conn_mssr;

        global $grade_goal;
        global $classroom_goal;
        global $new_classroom_goal;
        global $get_filter_semester_start;
        global $get_filter_semester_end;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=14;    //欄位個數
        $btn_nos=1;     //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

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

        //學期時間範圍
        $semester_start=trim($arrys_result[0]['semester_start']);
        $semester_end  =trim($arrys_result[0]['semester_end']);

        //是否為當學期
        $is_now_semester    =false;
        $now_time           =(double)time();
        $semester_start_time=(double)strtotime($semester_start);
        $semester_end_time  =(double)strtotime($semester_end);
        if(($semester_start_time<=$now_time)&&($semester_end_time>=$now_time))$is_now_semester=true;

        $filter_semester_start=(isset($get_filter_semester_start)&&trim($get_filter_semester_start)!=='')?trim($get_filter_semester_start):date("Y-m-d",strtotime($semester_start));
        $filter_semester_end  =(isset($get_filter_semester_end)&&trim($get_filter_semester_end)!=='')?trim($get_filter_semester_end):date("Y-m-d",strtotime($semester_end));
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
    <script type="text/javascript" src="../../../../lib/js/table/code.js"></script>
    <script type="text/javascript" src="../../../../inc/datepicker/WdatePicker.js"></script>

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
                <!-- 資料資訊表格 開始 -->
                <div class="mod_data_tbl_outline" style="margin-top:35px;">
                    <table id="mod_data_tbl" border="0" width="99%" cellpadding="5" cellspacing="0" style="" class="font-weight1 font-family1 fc_green0">
                        <tr align="center" valign="middle" class="fsize_16">
                            <td align="left" width="500px">
                                <span class="fsize_16">
                                    ●資料計算時間為
                                    <span class="fc_red1"><?php echo htmlspecialchars($filter_semester_start);?></span>
                                    ~
                                    <span class="fc_red1"><?php echo htmlspecialchars($filter_semester_end);?></span>
                                    為止
                                </span>
                            </td>
                            <td align="right" valign="middle" style="border-left:1px solid #b2d4dd;">
                                篩選時間：

                                <input id="filter_semester_start" name="filter_semester_start"
                                class="form_text Wdate" type="text" style="width:85px" value="<?php echo htmlspecialchars($filter_semester_start);?>"
                                onClick="WdatePicker({minDate:'<?php echo date("Y-m-d",strtotime($semester_start));?>',maxDate:'<?php echo date("Y-m-d",strtotime($semester_end));?>'})"> &nbsp;至&nbsp;

                                <input id="filter_semester_end" name="filter_semester_end"
                                class="form_text Wdate" type="text" style="width:85px" value="<?php echo htmlspecialchars($filter_semester_end);?>"
                                onClick="WdatePicker({minDate:'<?php echo date("Y-m-d",strtotime($semester_start));?>',maxDate:'<?php echo date("Y-m-d",strtotime($semester_end));?>'})">

                                <input type="button" value="篩選" class="ibtn_gr3020"
                                onclick="filter_semester();" onmouseover="this.style.cursor='pointer'">

                                <?php if($filter_semester_start!==$semester_start):?>
                                    <input type="button" value="回復" class="ibtn_gr3020"
                                    onclick="filter_semester_back();" onmouseover="this.style.cursor='pointer'">
                                <?php endif;?>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- 資料資訊表格 結束 -->

                <!-- 統計資料表格 開始 -->
                <div id="data_statistics" class="mod_data_tbl_outline" style="position:relative;margin-top:35px;height:70px;">
                    <table width="98%" align="center" cellpadding="0" cellspacing="0" border="0" style="position:relative;top:20px;"/>
                        <tr>
                            <td align="left" class="fsize_18 font-weight1 font-family1 fc_green0">
                                <?php //if(($auth_sys_check_lv===99)||(in_array($sess_class_code,array('gcp_2013_2_3_6','gcp_2013_2_3_7')))):?>
                                    <!-- <input type="button" value="任務推薦專區" class="ibtn_gr9030" onclick="detailed_task();void(0);"  onmouseover="this.style.cursor='pointer'"
                                    style='position:relative;left:45px;'>
                                    <img src="../../../../img/icon/new_orange.png" width="80" height="80" border="0" alt="new"
                                    style='position:absolute;bottom:0;left:-15px;'/> -->
                                <?php //endif;?>
                                <?php if($is_now_semester && $filter_semester_start==$semester_start && $filter_semester_end==$semester_end):?>
                                    <input type="button" value="當天的推薦"   class="ibtn_gr9030" onclick="detailed_date_between('today');"     onmouseover="this.style.cursor='pointer'">
                                    <input type="button" value="三天內的推薦" class="ibtn_gr9030" onclick="detailed_date_between('three_day');" onmouseover="this.style.cursor='pointer'">
                                    <input type="button" value="一周內的推薦" class="ibtn_gr9030" onclick="detailed_date_between('one_week');"  onmouseover="this.style.cursor='pointer'">
                                    <input type="button" value="兩周內的推薦" class="ibtn_gr9030" onclick="detailed_date_between('two_week');"  onmouseover="this.style.cursor='pointer'">
                                <?php endif;?>
                            </td>
                            <td align="right" class="fsize_18 font-weight1 font-family1 fc_green0">
                                <img src="../../img/user/user_read/histogram.png" width="35" height="35" border="0" alt="histogram"
                                style="cursor:pointer;" onclick="show_histogram();"/>
                                <?php echo (int)$grade_goal;?>年<?php echo htmlspecialchars(trim($new_classroom_goal));?>班&nbsp;
                                <span id="rec_group_cno" class="fsize_18 font-weight1 font-family1 fc_red1">推薦總本數：</span>
                                <span id="rec_group_avg" class="fsize_18 font-weight1 font-family1 fc_red1">推薦平均本數：</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- 統計資料表格 結束 -->

                <!-- 資料表格 開始 -->
                <div class="mod_data_tbl_outline" style="margin-top:35px;">
                    <table id="mod_data_tbl" border="0" width="95%" cellpadding="5" cellspacing="0" style="margin-top:30px;" class="table_style3">
                        <thead>
                        <tr align="center" valign="middle" class="fsize_18">
                            <th width="<?php if($is_now_semester && $filter_semester_start==$semester_start && $filter_semester_end==$semester_end){echo '55px';}else{echo '100px';}?>" height="40px">座號       </th>
                            <th width="<?php if($is_now_semester && $filter_semester_start==$semester_start && $filter_semester_end==$semester_end){echo '95px';}else{echo '100px';}?>" height="40px">姓名       </th>
                            <?php if($is_now_semester && $filter_semester_start==$semester_start && $filter_semester_end==$semester_end):?>
                                <th width="50px" height="40px">今日       </th>
                                <th width="30px" height="40px">
                                    <?php echo date("d", strtotime('-1 DAY'));?>
                                </th>
                                <th width="30px" height="40px">
                                    <?php echo date("d", strtotime('-2 DAY'));?>
                                </th>
                                <th width="30px" height="40px">
                                    <?php echo date("d", strtotime('-3 DAY'));?>
                                </th>
                                <th width="30px" height="40px">
                                    <?php echo date("d", strtotime('-4 DAY'));?>
                                </th>
                                <th width="30px" height="40px">
                                    <?php echo date("d", strtotime('-5 DAY'));?>
                                </th>
                                <th width="30px" height="40px">
                                    <?php echo date("d", strtotime('-6 DAY'));?>
                                </th>
                            <?php endif;?>
                            <th width="<?php if($is_now_semester && $filter_semester_start==$semester_start && $filter_semester_end==$semester_end){echo '55px';}else{echo '100px';}?>" height="40px">總推薦數   </th>
                            <th width="<?php if($is_now_semester && $filter_semester_start==$semester_start && $filter_semester_end==$semester_end){echo '60px';}else{echo '100px';}?>" height="40px">星星       </th>
                            <th width="<?php if($is_now_semester && $filter_semester_start==$semester_start && $filter_semester_end==$semester_end){echo '60px';}else{echo '100px';}?>" height="40px">繪圖       </th>
                            <th width="<?php if($is_now_semester && $filter_semester_start==$semester_start && $filter_semester_end==$semester_end){echo '60px';}else{echo '100px';}?>" height="40px">文字       </th>
                            <th width="<?php if($is_now_semester && $filter_semester_start==$semester_start && $filter_semester_end==$semester_end){echo '60px';}else{echo '100px';}?>" height="40px">錄音       </th>
                            <th width="">其他           </th>
                        </tr>
                        </thead>

                        <?php foreach($arrys_result as $inx=>$arry_result) :?>
                        <?php
                        //---------------------------------------------------
                        //接收欄位
                        //---------------------------------------------------

                            extract($arry_result, EXTR_PREFIX_ALL, "rs");

                        //---------------------------------------------------
                        //處理欄位
                        //---------------------------------------------------

                            $rs_uid     =(int)$rs_uid;
                            $rs_number  =(int)$rs_number;
                            if($rs_number===0){
                                $rs_number=trim('無');
                            }

                            //name      學生名稱
                            $rs_name    =trim($rs_name);
                            if(mb_strlen($rs_name)>10){
                                $rs_name=mb_substr($rs_name,0,10)."..";
                            }

                        //---------------------------------------------------
                        //特殊處理
                        //---------------------------------------------------

                            //-----------------------------------------------
                            //總推薦數
                            //-----------------------------------------------

                                $all_rec_num=numrow_book_rec($conn_mssr,$rs_uid,$date='',$rec_type='',$filter_semester_start,$filter_semester_end,$arry_conn_mssr);
                        ?>
                        <tr class="fsize_16">
                            <td height="30px" align="center" valign="middle">
                                <?php echo $rs_number;?>
                            </td>
                            <td height="30px" align="center" valign="middle">
                                <span name="user_id" inx="<?php echo (int)$inx;?>" rec_group_cno_semester="<?php echo (int)$all_rec_num;?>">
                                    <?php echo htmlspecialchars($rs_name);?>
                                </span>
                            </td>
                            <?php if($is_now_semester && $filter_semester_start==$semester_start && $filter_semester_end==$semester_end):?>
                                <td height="30px" align="center" valign="middle">
                                    <?php echo numrow_book_rec($conn_mssr,$rs_uid,date("Y-m-d"),$rec_type='',$semester_start,$semester_end,$arry_conn_mssr);?>
                                </td>
                                <td height="30px" align="center" valign="middle">
                                    <?php echo numrow_book_rec($conn_mssr,$rs_uid,date('Y-m-d', strtotime('-1 DAY')),$rec_type='',$semester_start,$semester_end,$arry_conn_mssr);?>
                                </td>
                                <td height="30px" align="center" valign="middle">
                                    <?php echo numrow_book_rec($conn_mssr,$rs_uid,date('Y-m-d', strtotime('-2 DAY')),$rec_type='',$semester_start,$semester_end,$arry_conn_mssr);?>
                                </td>
                                <td height="30px" align="center" valign="middle">
                                    <?php echo numrow_book_rec($conn_mssr,$rs_uid,date('Y-m-d', strtotime('-3 DAY')),$rec_type='',$semester_start,$semester_end,$arry_conn_mssr);?>
                                </td>
                                <td height="30px" align="center" valign="middle">
                                    <?php echo numrow_book_rec($conn_mssr,$rs_uid,date('Y-m-d', strtotime('-4 DAY')),$rec_type='',$semester_start,$semester_end,$arry_conn_mssr);?>
                                </td>
                                <td height="30px" align="center" valign="middle">
                                    <?php echo numrow_book_rec($conn_mssr,$rs_uid,date('Y-m-d', strtotime('-5 DAY')),$rec_type='',$semester_start,$semester_end,$arry_conn_mssr);?>
                                </td>
                                <td height="30px" align="center" valign="middle">
                                    <?php echo numrow_book_rec($conn_mssr,$rs_uid,date('Y-m-d', strtotime('-6 DAY')),$rec_type='',$semester_start,$semester_end,$arry_conn_mssr);?>
                                </td>
                            <?php endif;?>
                            <td height="30px" align="center" valign="middle">
                                <?php echo numrow_book_rec($conn_mssr,$rs_uid,$date='',$rec_type='',$filter_semester_start,$filter_semester_end,$arry_conn_mssr);?>
                            </td>
                            <td height="30px" align="center" valign="middle">
                                <?php
                                    $star_rec_num=numrow_book_rec($conn_mssr,$rs_uid,$date='',$rec_type='star',$filter_semester_start,$filter_semester_end,$arry_conn_mssr);

                                    if(($star_rec_num===0)||($all_rec_num===0)){
                                        echo 0;
                                    }else{
                                        $person=$star_rec_num/$all_rec_num;
                                        echo round($person*100,0);
                                    }
                                ?>%
                            </td>
                            <td height="30px" align="center" valign="middle">
                                <?php
                                    $draw_rec_num=numrow_book_rec($conn_mssr,$rs_uid,$date='',$rec_type='draw',$filter_semester_start,$filter_semester_end,$arry_conn_mssr);

                                    if(($draw_rec_num===0)||($all_rec_num===0)){
                                        echo 0;
                                    }else{
                                        $person=$draw_rec_num/$all_rec_num;
                                        echo round($person*100,0);
                                    }
                                ?>%
                            </td>
                            <td height="30px" align="center" valign="middle">
                                <?php
                                    $text_rec_num=numrow_book_rec($conn_mssr,$rs_uid,$date='',$rec_type='text',$filter_semester_start,$filter_semester_end,$arry_conn_mssr);

                                    if(($text_rec_num===0)||($all_rec_num===0)){
                                        echo 0;
                                    }else{
                                        $person=$text_rec_num/$all_rec_num;
                                        echo round($person*100,0);
                                    }
                                ?>%
                            </td>
                            <td height="30px" align="center" valign="middle">
                                <?php
                                    $record_rec_num=numrow_book_rec($conn_mssr,$rs_uid,$date='',$rec_type='record',$filter_semester_start,$filter_semester_end,$arry_conn_mssr);

                                    if(($record_rec_num===0)||($all_rec_num===0)){
                                        echo 0;
                                    }else{
                                        $person=$record_rec_num/$all_rec_num;
                                        echo round($person*100,0);
                                    }
                                ?>%
                            </td>
                            <td height="30px" align="center" valign="middle">
                                <input type="button" value="詳細" class="ibtn_gr6030" onclick="detailed_single(<?php echo addslashes($rs_uid);?>)" onmouseover="this.style.cursor='pointer'">
                            </td>
                        </tr>
                        <?php endforeach ;?>
                    </table>

                    <table border="0" width="100%">
                        <tr valign="middle">
                            <td align="left">
                                <!-- 分頁列 -->
                                <span id="page" style="position:relative;margin-top:10px;"></span>
                            </td>
                            <td width="250px" align="right">

                            </td>
                        </tr>
                    </table>
                </div>
                <!-- 資料表格 結束 -->
            <!-- 內容 -->
            </td>
        </tr>
    </table>
    <!-- 資料列表 結束 -->

    <!-- 柱狀圖區塊 開始 -->
        <iframe id="ifc_histogram" name="ifc_histogram" src="" frameborder="0"
        style="width:100%;height:555px;overflow:hidden;overflow-y:auto;display:none;"></iframe>
    <!-- 柱狀圖區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;
    var $isbn_10=$('.isbn_10');
    var filter_semester_start='<?php echo $filter_semester_start;?>';
    var filter_semester_end='<?php echo $filter_semester_end;?>';

    //物件
    var orec_group_cno=document.getElementById('rec_group_cno');
    var orec_group_avg=document.getElementById('rec_group_avg');

    function show_histogram(){
        var oifc_histogram=document.getElementById('ifc_histogram');
        oifc_histogram.src='view/chart/histogram.php?filter_semester_start='+filter_semester_start+'&filter_semester_end='+filter_semester_end;
        $.blockUI({
            message:$(oifc_histogram),
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#fff',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: 1,
                color: '#fff',
                top:  50,
                left: 50,
                width: '800px'
            },
            overlayCSS:{
                backgroundColor:'#000',
                opacity:0.6,
                cursor:'default'
            }
        });
    }

    function filter_semester(){
        var filter_semester_start=$.trim(document.getElementById('filter_semester_start').value);
        var filter_semester_end  =$.trim(document.getElementById('filter_semester_end').value);
        location.href=document.URL.split("?")[0]+'?filter_semester_start='+filter_semester_start+'&filter_semester_end='+filter_semester_end;
    }
    function filter_semester_back(){
        location.href=document.URL.split("?")[0];
    }

    window.onload=function(){

        //設定動態高度
        var oIFC=parent.document.getElementById('IFC');
        var oparent_IFC=parent.parent.document.getElementById('IFC');
        oIFC.style.height=parseInt($(document).height()+50)+'px';
        oparent_IFC.style.height=parseInt($(document).height()+250)+'px';

        //ajax載入進度
        get_ajax_load_info();

        //滑鼠動作設置
        $('#mod_data_tbl th').mouseover(function(){
            $(this).css('cursor', 'pointer');
        });

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
            'page_name' :'content.php',
            'page_args' :{}
        }
        //var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);

        parent.$.unblockUI();
    }

    function page_click(obj,psize){
    //筆數條件設定
        var url ='';
        var page=str_repeat('../',0)+'content.php';
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

        $.blockUI({
            message:'<h2 class="fc_white0">處理中，請稍後 !</h2>',
            css: {
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

    function detailed_single(user_id){
    //個人詳細
        var url ='';
        var page=str_repeat('../',0)+'detailed/single/index.php';
        var arg ={
            'user_id':user_id
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

        window.open(url);
    }

    function detailed_date_between(date_filter){
    //時間內詳細
        var url ='';
        var page=str_repeat('../',0)+'detailed/date_between/index.php';
        var arg ={
            'date_filter':date_filter
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

        window.open(url);
    }

    function detailed_task(){
    //任務專區詳細
        var url ='';
        var page=str_repeat('../',0)+'detailed/detailed_task/index.php';
        var arg ={

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

        window.open(url);
    }

    function get_ajax_load_info(){
    //ajax載入進度

        //抓取
        var rec_group_cno=0;
        var arry_rec_group_cno_semester=[];
        var user_ids=document.getElementsByName('user_id');
        for(i=0;i<user_ids.length;i++){
            var user_id=user_ids[i];
            var rec_group_cno_semester=parseInt(user_id.getAttribute('rec_group_cno_semester'));
            var inx=parseInt(user_id.getAttribute('inx'));
            if(inx>0){
                rec_group_cno=rec_group_cno+rec_group_cno_semester;
                arry_rec_group_cno_semester.push(rec_group_cno_semester);
            }
        }

        //回填總本數
        $(orec_group_cno).append(rec_group_cno);

        //回填平均本數
        $(orec_group_avg).append(Math.ceil(rec_group_cno/arry_rec_group_cno_semester.length));
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