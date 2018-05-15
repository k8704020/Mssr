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

        $fld_nos=11; //欄位個數
        $btn_nos=1;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        //學期時間範圍
        $semester_start=trim($arrys_result[0]['semester_start']);
        $semester_end  =trim($arrys_result[0]['semester_end']);

        $filter_semester_start=(isset($get_filter_semester_start)&&trim($get_filter_semester_start)!=='')?trim($get_filter_semester_start):date("Y-m-d",strtotime($semester_start));
        $filter_semester_end  =(isset($get_filter_semester_end)&&trim($get_filter_semester_end)!=='')?trim($get_filter_semester_end):date("Y-m-d",strtotime($semester_end));

        //是否為當學期
        $is_now_semester    =false;
        $now_time           =(double)time();
        $semester_start_time=(double)strtotime($semester_start);
        $semester_end_time  =(double)strtotime($semester_end);
        if(($semester_start_time<=$now_time)&&($semester_end_time>=$now_time))$is_now_semester=true;

        //學生陣列
        $arrys_user=array();

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

        $category_arry_users=array();
        $category_users_list='';
        $category_arry_books=array();
        $category_books_list='';
        foreach($arrys_result as $inx=>$arry_result){
            $rs_uid=(int)$arry_result['uid'];
            $category_arry_users[]=$rs_uid;
        }
        $category_users_list=implode(",",$category_arry_users);
        $sql="
            SELECT
                `mssr`.`mssr_book_borrow_semester`.`book_sid`
            FROM `mssr`.`mssr_book_borrow_semester`
            WHERE 1=1
                AND `mssr`.`mssr_book_borrow_semester`.`user_id` IN ($category_users_list)
                AND `mssr`.`mssr_book_borrow_semester`.`borrow_sdate` BETWEEN '{$filter_semester_start} 00:00:00' AND '{$filter_semester_end} 23:59:59'
            GROUP BY `mssr`.`mssr_book_borrow_semester`.`user_id` , `mssr`.`mssr_book_borrow_semester`.`book_sid`
        ";
        $category_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
        if(!empty($category_results)){
            foreach($category_results as $category_result){
                $rs_book_sid=trim($category_result['book_sid']);
                $category_arry_books[]=$rs_book_sid;
            }
            $category_books_list=implode("','",$category_arry_books);
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
            $category_arrys_book_borrow_info=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($category_arrys_book_borrow_info)){
                foreach($category_arrys_book_borrow_info as $category_arry_book_borrow_info){
                    $rs_cat_name=trim($category_arry_book_borrow_info['cat_name']);
                    if(array_key_exists($rs_cat_name,$category_arry_book_borrow)){
                        $category_arry_book_borrow[$rs_cat_name]=(int)($category_arry_book_borrow[$rs_cat_name]+1);
                    }
                }
                $category_arry_book_borrow['未分類']=(int)(count($category_results)-count($category_arrys_book_borrow_info));
                $json_category_arry_book_borrow=json_encode($category_arry_book_borrow,true);
            }
        }
        //echo "<Pre>";
        //print_r($category_arry_book_borrow);
        //echo "</Pre>";
//echo "<Pre>";print_r($filter_semester_start);echo "</Pre>";
//echo "<Pre>";print_r($filter_semester_end);echo "</Pre>";
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
    <script type="text/javascript" src="../../../../lib/jquery/plugin/func/flot/excanvas.min.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/func/flot/jquery.flot.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/func/flot/jquery.flot.pie.js"></script>

    <script type="text/javascript" src="../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/table/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/array/code.js"></script>
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
                    <table id="mod_data_tbl" border="0" width="99%" height="40px" cellpadding="5" cellspacing="0" style="" class="font-weight1 font-family1 fc_green0">
                        <tr align="center" valign="middle" class="fsize_16">
                            <td align="left" valign="middle" width="500px">
                                <span class="fsize_16">
                                    <!-- <input type="button" value="說明文件" class="ibtn_gr6030"
                                    onclick="read_me();" onmouseover="this.style.cursor='pointer'"> -->
                                    <!-- <input type="button" value="全班書單" class="ibtn_gr6030"
                                    onclick="class_code_book_list();" onmouseover="this.style.cursor='pointer'"> -->
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
                <?php if($is_now_semester):?>
                    <div id="data_statistics" class="mod_data_tbl_outline" style="margin-top:35px;height:150px;display:none;">
                        <table width="98%" align="center" cellpadding="0" cellspacing="0" border="0" style="position:relative;top:20px;"/>
                            <tr>
                                <td align="left" class="fsize_18 font-weight1 font-family1 fc_green0">
                                    <?php echo (int)$grade_goal;?>年級各班 最後5名的平均本數
                                    <span id="avg_read_group_info" class="fc_red1" flag="star"> ...處理中，請稍後!</span>
                                </td>
                            </tr>
                        </table>

                        <table border="0" width="80px" cellpadding="0" cellspacing="0" align="left" bgcolor="#87CDDC"
                        style="position:relative;top:45px;margin-left:5px;border:1px solid #87CDDC;">
                            <tr align="center" valign="middle" class="fc_white0 font-weight1 font-family1">
                                <td height="30px" align="center" valign="middle">
                                    班級
                                </td>
                            <tr/>
                            <tr align="center" valign="middle" class="fc_red1 font-weight1 font-family1">
                                <td height="30px" align="center" valign="middle" style="background-color:#F7F8F8;border:1px solid #ffffff;">
                                    本數
                                </td>
                            <tr/>
                        </table>

                        <?php foreach($arrys_class_code as $inx=>$arry_class_code):?>
                        <?php
                            $rs_class_code=trim($arry_class_code['class_code']);
                            $rs_classroom =(int)$arry_class_code['classroom'];

                            //置換班級名稱
                            $get_class_code_info_single=get_class_code_info_single($conn_user,mysql_prep($sess_school_code),(int)$grade_goal,$rs_classroom,$compile_flag=true,$arry_conn_user);
                            $new_classroom=trim($get_class_code_info_single[0]['classroom']);
                        ?>
                        <table id="<?php echo $rs_class_code;?>" border="0" width="80px" cellpadding="0" cellspacing="0" align="left" bgcolor="#87CDDC"
                        style="position:relative;top:45px;margin-left:5px;border:1px solid #87CDDC;" new_classroom="<?php echo $new_classroom;?>">
                            <tr align="center" valign="middle" class="fc_white0 font-weight1 font-family1">
                                <td height="30px" align="center" valign="middle">
                                    <img src="../../../../img/icon/loader.gif" width="20" height="20" border="0" alt="讀取中..."/>
                                </td>
                            <tr/>
                            <tr align="center" valign="middle" class="fc_red1 font-weight1 font-family1">
                                <td height="30px" align="center" valign="middle" style="background-color:#F7F8F8;border:1px solid #ffffff;">
                                    <img src="../../../../img/icon/loader.gif" width="20" height="20" border="0" alt="讀取中..."/>
                                </td>
                            <tr/>
                        </table>
                        <?php endforeach;?>
                    </div>
                <?php endif;?>
                <!-- 統計資料表格 結束 -->

                <!-- 資料表格 開始 -->
                <div class="mod_data_tbl_outline" style="margin-top:35px;">
                    <table cellpadding="0" cellspacing="0" border="0" width="95%"
                    style='position:relative;top:15px;border:1px solid #87CDDC;border-radius:5px;'/>
                        <tr>
                            <td align='center' width="65%" class="fsize_18 font-weight1 font-family1 fc_green0" valign='middle'>
                                <div style="margin:10px 0;">剩餘<span id="sec" class="fc_red0">60</span>秒將會自動更新本頁面 !</div>
                                <div style="margin:10px 0;">


                                    <?php if(addslashes($sess_school_code)=='idc'){?>
                                        <a href="view/chart/histogram.php?filter_semester_start=<?php echo $filter_semester_start; ?>&filter_semester_end=<?php echo $filter_semester_end;?>" target="_blank" id="chart" ><img src="../../img/user/user_read/histogram.png" width="35" height="35" border="0" alt="histogram"
                                        style="cursor:pointer;" /></a>
                                    <?php }else{ ?>

                                        <img src="../../img/user/user_read/histogram.png" width="35" height="35" border="0" alt="histogram"
                                                                            style="cursor:pointer;" onclick="show_histogram();"/>

                                    <?php }?>
                                    
                                    <?php echo (int)$grade_goal;?>年<?php echo htmlspecialchars(trim($new_classroom_goal));?>班&nbsp;
                                    <span id="read_group_cno" class="fsize_18 font-weight1 font-family1 fc_red1">閱讀總本數：</span>&nbsp;
                                    <span id="read_group_avg" class="fsize_18 font-weight1 font-family1 fc_red1">閱讀平均本數：</span>
                                </div>
                                <?php if(!empty($category_arrys_book_borrow_info)):?>
                                    <table align="center" cellpadding="5" cellspacing="0" border="0" width="80%" style="margin-top:10px;margin-right:20px;"/>
                                        <tr align="left">
                                            <td width="50%">
                                                生活：<?php echo (int)($category_arry_book_borrow[trim('生活')]);?> 本
                                                (<?php echo round((int)($category_arry_book_borrow[trim('生活')])/(count($category_results)-(int)($category_arry_book_borrow[trim('未分類')]))*100);?>%)
                                            </td>
                                            <td>
                                                藝術：<?php echo (int)($category_arry_book_borrow[trim('藝術')]);?> 本
                                                (<?php echo round((int)($category_arry_book_borrow[trim('藝術')])/(count($category_results)-(int)($category_arry_book_borrow[trim('未分類')]))*100);?>%)
                                            </td>
                                        </tr>
                                        <tr align="left">
                                            <td>
                                                文學：<?php echo (int)($category_arry_book_borrow[trim('文學')]);?> 本
                                                (<?php echo round((int)($category_arry_book_borrow[trim('文學')])/(count($category_results)-(int)($category_arry_book_borrow[trim('未分類')]))*100);?>%)
                                            </td>
                                            <td>
                                                社會：<?php echo (int)($category_arry_book_borrow[trim('社會')]);?> 本
                                                (<?php echo round((int)($category_arry_book_borrow[trim('社會')])/(count($category_results)-(int)($category_arry_book_borrow[trim('未分類')]))*100);?>%)
                                            </td>
                                        </tr>
                                        <tr align="left">
                                            <td>
                                                科學：<?php echo (int)($category_arry_book_borrow[trim('科學')]);?> 本
                                                (<?php echo round((int)($category_arry_book_borrow[trim('科學')])/(count($category_results)-(int)($category_arry_book_borrow[trim('未分類')]))*100);?>%)
                                            </td>
                                            <td>
                                                未分類：<?php echo (int)($category_arry_book_borrow[trim('未分類')]);?> 本
                                                <!-- (<?php echo round((int)($category_arry_book_borrow[trim('未分類')])/count($category_results)*100);?>%) -->
                                            </td>
                                        </tr>
                                    </table>
                                <?php endif;?>
                            </td>
                            <td align='left' valign='middle'>
                                <?php if(!empty($category_arrys_book_borrow_info)):?>
                                    <div id="flot_pie" style="width:220px;height:140px;"></div>
                                    <img src="../../img/user/user_read/line_chart.png" width="35" height="35" border="0" alt="line_chart"
                                    style="cursor:pointer;position:absolute;right:110px;bottom:10px;" onclick="show_line_chart();"/>
                                <?php endif;?>
                            </td>
                        </tr>
                    </table>
                    <table id="mod_data_tbl" border="0" width="95%" cellpadding="5" cellspacing="0" style="margin-top:30px;" class="table_style3">
                        <thead>
                        <tr align="center" valign="middle" class="fsize_18">
                            <th width="40px" height="40px">座號     </th>
                            <th width="135px" height="40px">姓名    </th>
                            <?php if($is_now_semester && $filter_semester_start==$semester_start && $filter_semester_end==$semester_end):?>
                                <th width="50px" height="40px">今日     </th>
                                <th width="50px" height="40px">
                                    <?php echo date("d", strtotime('-1 DAY'));?>日
                                </th>
                                <th width="50px" height="40px">
                                    <?php echo date("d", strtotime('-2 DAY'));?>日
                                </th>
                                <th width="50px" height="40px">
                                    <?php echo date("d", strtotime('-3 DAY'));?>日
                                </th>
                                <th width="50px" height="40px">
                                    <?php echo date("d", strtotime('-4 DAY'));?>日
                                </th>
                                <th width="50px" height="40px">
                                    <?php echo date("d", strtotime('-5 DAY'));?>日
                                </th>
                                <th width="50px" height="40px">
                                    <?php echo date("d", strtotime('-6 DAY'));?>日
                                </th>
                            <?php endif;?>
                            <th width="<?php if($is_now_semester && $filter_semester_start==$semester_start && $filter_semester_end==$semester_end){echo '45px';}else{echo '250px';}?>" height="40px">登記本數 </th>
                            <th width="<?php if($is_now_semester && $filter_semester_start==$semester_start && $filter_semester_end==$semester_end){echo '45px';}else{echo '250px';}?>" height="40px">登記次數 </th>
                            <th width="" height="40px">其他</th>
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

                            //name      學生名稱
                            $rs_name    =trim($rs_name);
                            if(mb_strlen($rs_name)>10){
                                $rs_name=mb_substr($rs_name,0,10)."..";
                            }

                        //---------------------------------------------------
                        //特殊處理
                        //---------------------------------------------------

                            //-----------------------------------------------
                            //匯入學生陣列
                            //-----------------------------------------------

                                $arrys_user[]=$rs_uid;

                            //-----------------------------------------------
                            //閱讀狀態背景顏色區隔
                            //-----------------------------------------------

                                if(!function_exists("read_state_bg_color_code")){
                                    function read_state_bg_color_code($read_cno){
                                    //閱讀狀態顏色區隔
                                        $read_state_bg_color_code='';
                                        if($read_cno===0){
                                            $read_state_bg_color_code='bg_orange0';
                                        }
                                        if($read_cno>=10){
                                            $read_state_bg_color_code='bg_red0';
                                        }
                                        return $read_state_bg_color_code;
                                    }
                                }

                            //-----------------------------------------------
                            //當天閱讀紀錄
                            //-----------------------------------------------

                                $read_group_cno_today=numrow_book_read_group($conn_mssr,$rs_uid,date("Y-m-d"),array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);

                            //-----------------------------------------------
                            //前幾天閱讀紀錄
                            //-----------------------------------------------

                                $read_group_cno_fda_1_day=numrow_book_read_group($conn_mssr,$rs_uid,date('Y-m-d', strtotime('-1 DAY')),array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);
                                $read_group_cno_fda_2_day=numrow_book_read_group($conn_mssr,$rs_uid,date('Y-m-d', strtotime('-2 DAY')),array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);
                                $read_group_cno_fda_3_day=numrow_book_read_group($conn_mssr,$rs_uid,date('Y-m-d', strtotime('-3 DAY')),array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);
                                $read_group_cno_fda_4_day=numrow_book_read_group($conn_mssr,$rs_uid,date('Y-m-d', strtotime('-4 DAY')),array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);
                                $read_group_cno_fda_5_day=numrow_book_read_group($conn_mssr,$rs_uid,date('Y-m-d', strtotime('-5 DAY')),array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);
                                $read_group_cno_fda_6_day=numrow_book_read_group($conn_mssr,$rs_uid,date('Y-m-d', strtotime('-6 DAY')),array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);

                            //-----------------------------------------------
                            //學期閱讀紀錄
                            //-----------------------------------------------

                                //本數
                                //$read_group_cno_semester=numrow_book_read_group($conn_mssr,$rs_uid,'',array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);

                                //次數
                                //$read_frequency_cno_semester=numrow_book_read_frequency($conn_mssr,$rs_uid,'',array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);
                        ?>
                        <tr class="fsize_16">
                            <td height="30px" align="center" valign="middle">
                                <?php if ($sess_school_code == 'idc') { ?>
								<a  href="view/chart/select_class_level.php?user_id=<?php echo $rs_uid; ?>"  target="_blank">
									<?php echo $rs_number;?>
								</a>
								<?php }else { ?>
									<?php echo $rs_number;?>
								<?php } ?>
                            </td>
                            <td height="30px" align="center" valign="middle">
                                <span id="user_id_<?php echo $rs_uid;?>" name="user_id" user_id="<?php echo $rs_uid;?>">
                                    <?php if($sess_school_code==='idc'){ ?>
                                                <a href="view/book_log_info/content.php?user_id=<?php echo $rs_uid;?>&filter_semester_start=<?php echo $filter_semester_start; ?>&filter_semester_end=<?php echo $filter_semester_end;?>" target="_blank" >

                                                    <?php echo htmlspecialchars($rs_name);?>
                                                        
                                                </a>
                                    <?php }else{
                                           
                                           echo htmlspecialchars($rs_name);

                                        
                                    }?>

                                          
                                    
                                </span>
                            </td>
                            <?php if($is_now_semester && $filter_semester_start==$semester_start && $filter_semester_end==$semester_end):?>
                                <td height="30px" align="center" valign="middle"
                                class="<?php echo read_state_bg_color_code((int)$read_group_cno_today);?>">
                                    <?php echo (int)$read_group_cno_today;?>
                                </td>
                                <td height="30px" align="center" valign="middle"
                                class="<?php echo read_state_bg_color_code((int)$read_group_cno_fda_1_day);?>">
                                    <?php echo (int)$read_group_cno_fda_1_day;?>
                                </td>
                                <td height="30px" align="center" valign="middle"
                                class="<?php echo read_state_bg_color_code((int)$read_group_cno_fda_2_day);?>">
                                    <?php echo (int)$read_group_cno_fda_2_day;?>
                                </td>
                                <td height="30px" align="center" valign="middle"
                                class="<?php echo read_state_bg_color_code((int)$read_group_cno_fda_3_day);?>">
                                    <?php echo (int)$read_group_cno_fda_3_day;?>
                                </td>
                                <td height="30px" align="center" valign="middle"
                                class="<?php echo read_state_bg_color_code((int)$read_group_cno_fda_4_day);?>">
                                    <?php echo (int)$read_group_cno_fda_4_day;?>
                                </td>
                                <td height="30px" align="center" valign="middle"
                                class="<?php echo read_state_bg_color_code((int)$read_group_cno_fda_5_day);?>">
                                    <?php echo (int)$read_group_cno_fda_5_day;?>
                                </td>
                                <td height="30px" align="center" valign="middle"
                                class="<?php echo read_state_bg_color_code((int)$read_group_cno_fda_6_day);?>">
                                    <?php echo (int)$read_group_cno_fda_6_day;?>
                                </td>
                            <?php endif;?>
                            <td height="30px" align="center" valign="middle">
                                <span id="read_group_<?php echo $rs_uid;?>" name="read_group">
                                    <img name="img_read_group" src="../../../../img/icon/loader.gif" width="20" height="20" border="0" alt="讀取中..."/>
                                </span>
                                <?php //echo (int)$read_group_cno_semester;?>
                            </td>
                            <td height="30px" align="center" valign="middle">
                                <span id="read_frequency_<?php echo $rs_uid;?>" name="read_frequency">
                                    <img name="img_read_frequency" src="../../../../img/icon/loader.gif" width="20" height="20" border="0" alt="讀取中..."/>
                                </span>
                                <?php //echo (int)$read_frequency_cno_semester;?>
                            </td>
                            <td height="30px" align="center" valign="middle">
                                <input type="button" value="書單" class="ibtn_gr6030" onclick="book_list(<?php echo addslashes($rs_uid);?>)" onmouseover="this.style.cursor='pointer'">
                                <input type="button" value="歷程" class="ibtn_gr6030" onclick="course(<?php echo addslashes($rs_uid);?>)" onmouseover="this.style.cursor='pointer'">
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
                                <!-- <span class="fc_brown0" style="position:relative;right:20px;" onclick="page_click(this,10)" onmouseover="this.style.cursor='pointer'">一頁10筆</span>
                                <span class="fc_brown0" style="position:relative;right:20px;" onclick="page_click(this,20)" onmouseover="this.style.cursor='pointer'">一頁20筆</span>
                                <span class="fc_brown0" style="position:relative;right:20px;" onclick="page_click(this,<?php echo $numrow;?>)" onmouseover="this.style.cursor='pointer'">觀看全部</span> -->
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- 資料表格 結束 -->
            <!-- 內容 -->
            </td>
        </tr>
    </table>

    <!-- 說明文件 開始 -->
    <table id="tbl_read_me" cellpadding="0" cellspacing="0" border="0" width="100%" style="display:none;"/>
        <tr><td><img src="../../img/user/user_read/read_me.jpg" width="800px" height="600px" border="0" alt="說明文件"/></td></tr>
        <tr><td>
            <input type="button" id="BtnC" name="BtnC" value="關閉"
            style="position:relative;top:5px;" onmouseover="this.style.cursor='pointer'">
        </td></tr>
    </table>
    <!-- 說明文件 結束 -->

    <!-- 提示區塊 開始 -->
    <table id="tbl_prompt" align="center" cellpadding="0" cellspacing="0" border="0" width="100%" style="display:none;"/>
        <tr align="center">
            <td>
                <img src="../../img/user/user_read/prompt_1.jpg" width="800px" height="500px" border="0" alt="提示"/>
            </td>
        </tr>
        <tr align="center">
            <td>
                <img src="../../img/user/user_read/prompt_2.jpg" width="800px" height="500px" border="0" alt="提示"/>
            </td>
        </tr>
        <tr align="center">
            <td>
                <img src="../../img/user/user_read/prompt_3.jpg" width="800px" height="500px" border="0" alt="提示"/>
            </td>
        </tr>
        <tr align="center">
            <td>
                <img src="../../img/user/user_read/prompt_4.jpg" width="800px" height="500px" border="0" alt="提示"/>
            </td>
        </tr>
        <tr align="center">
            <td>
                <input type="button" value="點我關閉" class="" onclick="location.href='content.php?prompt=yes';void(0);"
                style="position:relative;top:5px;width:150px;font-size:18pt;color:#ff0000;" onmouseover="this.style.cursor='pointer'">
            </td>
        </tr>
    </table>
    <!-- 提示區塊 開始 -->

    <!-- 柱狀圖區塊 開始 -->
    <iframe id="ifc_histogram" name="ifc_histogram" src="" frameborder="0"
    style="width:100%;height:555px;overflow:hidden;overflow-y:auto;display:none;"></iframe>
    <!-- 柱狀圖區塊 結束 -->

    <!-- 折線圖區塊 開始 -->
    <iframe id="ifc_line_chart" name="ifc_line_chart" src="" frameborder="0"
    style="width:100%;height:555px;overflow:hidden;overflow-y:auto;display:none;"></iframe>
    <!-- 折線圖區塊 結束 -->

    <!-- 資料列表 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var json_class_code     =<?php echo $json_class_code;?>;
    var sess_school_code    ='<?php echo addslashes($sess_school_code);?>';
    var semester_start      ='<?php echo $semester_start;?>';
    var semester_end        ='<?php echo $semester_end;?>';

    var filter_semester_start='<?php echo $filter_semester_start;?>';
    var filter_semester_end='<?php echo $filter_semester_end;?>';


    var grade_goal          =<?php echo (int)$grade_goal;?>;
    var classroom_goal      =<?php echo (int)$classroom_goal;?>;
    var auth_sys_check_lv   =<?php echo (int)$auth_sys_check_lv;?>;
    var psize               =<?php echo $psize;?>;
    var pinx                =<?php echo $pinx;?>;
    var sec                 =60;
    var prompt_val          =trim('<?php echo $prompt;?>');

    //物件
    var oread_group_cno=document.getElementById('read_group_cno');
    var oread_group_avg=document.getElementById('read_group_avg');
    var oavg_read_group_info=document.getElementById('avg_read_group_info');
    var odata_statistics=document.getElementById('data_statistics');
    var ousers_id=document.getElementsByName('user_id');
    var oBtnC =document.getElementById('BtnC');

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

    function show_line_chart(){
        var oifc_line_chart=document.getElementById('ifc_line_chart');
        oifc_line_chart.src='view/chart/line_chart.php?filter_semester_start='+filter_semester_start+'&filter_semester_end='+filter_semester_end;
        $.blockUI({
            message:$(oifc_line_chart),
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
                width: '850px'
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

    function _prompt(){
    //提示文件

        $.blockUI({
            message:$('#tbl_prompt'),
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: 1,
                color: '#fff',
                top:  0,
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

    function grade_avg_info_set(json_class_code,school_code,grade_goal){
    //設置年級平均資訊

        //json處理
        for(key1 in json_class_code){

            for(key2 in json_class_code[key1]){

                //參數設置
                if(key2==='class_code'      ){var class_code     =trim(json_class_code[key1][key2]);}
                if(key2==='classroom'       ){var classroom      =trim(json_class_code[key1][key2]);}
                if(key2==='semester_start'  ){var semester_start =trim(json_class_code[key1][key2]);}
                if(key2==='semester_end'    ){var semester_end   =trim(json_class_code[key1][key2]);}
            }

            //呼叫年級平均資訊
            grade_avg_info(class_code,classroom,semester_start,semester_end);
        }
    }

    function grade_avg_info(class_code,classroom,semester_start,semester_end){
    //年級平均資訊

        //啟用ajax設置

            $.ajax({
            //參數設置
                async      :true,
                cache      :false,
                global     :true,
                timeout    :50000,
                contentType:"application/x-www-form-urlencoded; charset=UTF-8",

                url        :"ajax/grade_avg_infoA.php",
                type       :"POST",
                datatype   :"json",
                data       :{
                    class_code      :encodeURI(trim(class_code      )),
                    classroom       :encodeURI(trim(classroom       )),
                    semester_start  :encodeURI(trim(semester_start  )),
                    semester_end    :encodeURI(trim(semester_end    ))
                },

            //事件
                beforeSend  :function(){
                //傳送前處理
                },
                success     :function(respones){
                //成功處理

                    var oclass_code=document.getElementById(class_code);
                    var respones=jQuery.parseJSON(respones);
                    var ch_flag=trim(respones.ch_flag);

                    if(ch_flag==='true'){
                        var json_grade_avg_info=respones.grade_avg_info;
                        for(classroom in json_grade_avg_info){

                            //班級
                            var classroom=trim(classroom);

                            //平均本數
                            var avg_read_group=parseInt(json_grade_avg_info[classroom]);

                            //標記點
                            var new_classroom=trim(oclass_code.getAttribute('new_classroom'));

                            if(classroom===new_classroom){
                                //回填
                                oclass_code.rows[0].cells[0].innerHTML=classroom+'班';
                                oclass_code.rows[2].cells[0].innerHTML=avg_read_group+'本';
                            }else{
                                //回填
                                oclass_code.rows[0].cells[0].innerHTML=new_classroom+'班';
                                oclass_code.rows[2].cells[0].innerHTML=0+'本';
                            }
                        }

                        //隱藏
                        $(oavg_read_group_info).attr('flag','end').hide();
                    }else{
                        //標記點
                        var new_classroom=trim(oclass_code.getAttribute('new_classroom'));

                        //回填
                        oclass_code.rows[0].cells[0].innerHTML=new_classroom+'班';
                        oclass_code.rows[2].cells[0].innerHTML=0+'本';
                    }
                },
                error       :function(xhr, ajaxoptions, thrownerror){
                //失敗處理
                    if(ajaxoptions==='timeout'){
                        return false;
                    }else{
                        return false;
                    }
                },
                complete    :function(){
                //傳送後處理
                }
            });
    }

    function class_code_book_list(){
    //全班書單
        var url ='';
        var page=str_repeat('../',0)+'view/class_code_book_list/index.php';
        var arg ={
            'grade_goal':grade_goal,
            'classroom_goal':classroom_goal
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

        window.open(url,'win');
    }


    function read_me(){
    //說明文件

        $.blockUI({
            message:$('#tbl_read_me'),
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: 1,
                color: '#fff',
                top:  0,
                left: 50,
                width: '800px'
            },
            overlayCSS:{
                backgroundColor:'#000',
                opacity:0.6,
                cursor:'default'
            }
        });

        //回到頂部
        //$(window.top).scrollTop(0);
    }

    oBtnC.onclick=function(){
    //關閉
        //關閉說明文件
        $.unblockUI();
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

    function at_update_page(){
    //自動更新頁面
        $('#sec').text(sec);
        if(sec===0){
            location.reload();
        }else{
            sec--;
            setTimeout(at_update_page,1000);
        }
    }

    function course(user_id){
    //歷程
        var url ='';
        var page=str_repeat('../',0)+'view/course/content.php';
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

        go(url,'self');
    }

    function book_list(user_id){
    //書單
        var url ='';
        var page=str_repeat('../',0)+'view/book_list/content.php';
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

        go(url,'self');
    }


    function about_book_chat(user_id){
    //圖表
        var url ='';
        var page=str_repeat('../',0)+'view/book_log_info/content.php';
        var arg ={
            'user_id':user_id,
            'filter_semester_start':filter_semester_start,
            'filter_semester_end':filter_semester_end
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

    function ajax_set(arrys_user){
    //啟用ajax設置

        //參數
        var $url        ="ajax/basic_dataA.php";
        var $type       ="POST";
        var $datatype   ="json";
        var arrys_user  =arrys_user;

        for(key in arrys_user){
            var user_id=arrys_user[key];
            var key    =parseInt(key);
            var numrow =parseInt(arrys_user.length);

            //ajax呼叫, 本數
            ajax_read_group_cno_semester(user_id,filter_semester_start,filter_semester_end,key,numrow);

            //ajax呼叫, 次數
            ajax_read_frequency_cno_semester(user_id,filter_semester_start,filter_semester_end,key,numrow);
        }

        function ajax_read_group_cno_semester(user_id,semester_start,semester_end,key,numrow){
        //ajax呼叫, 本數
            ajax($url,$type,$datatype,'group',user_id,semester_start,semester_end,key,numrow);
        }

        function ajax_read_frequency_cno_semester(user_id,semester_start,semester_end,key,numrow){
        //ajax呼叫, 次數
            ajax($url,$type,$datatype,'frequency',user_id,semester_start,semester_end,key,numrow);
        }

        function ajax($url,$type,$datatype,read_type,$user_id,$semester_start,$semester_end,$key,$numrow){
        //ajax設置
            $.ajax({
            //參數設置
                async      :true,
                cache      :false,
                global     :true,
                timeout    :50000,
                contentType:"application/x-www-form-urlencoded; charset=UTF-8",

                url        :$url,
                type       :$type,
                datatype   :$datatype,
                data       :{
                    read_type     :encodeURI(trim(read_type      )),
                    user_id       :encodeURI(trim($user_id       )),
                    semester_start:encodeURI(trim($semester_start)),
                    semester_end  :encodeURI(trim($semester_end  ))
                },

            //事件
                beforeSend  :function(){
                //傳送前處理
                },
                success     :function(respones){
                //成功處理

                    var respones=jQuery.parseJSON(respones);
                    var read_type=respones.read_type;
                    var cno=parseInt(respones.cno);

                    switch(trim(read_type)){
                        case 'group':
                            //附加
                            var oread_group_user_id=document.getElementById('read_group_'+$user_id);
                            var ouser_id=document.getElementById('user_id_'+$user_id);
                            $(oread_group_user_id).empty().append(cno);
                            ouser_id.setAttribute('read_group_cno_semester',cno);
                        break;

                        case 'frequency':
                            //附加
                            var oread_frequency_user_id=document.getElementById('read_frequency_'+$user_id);
                            $(oread_frequency_user_id).empty().append(cno);
                        break;
                    }
                },
                error       :function(xhr, ajaxoptions, thrownerror){
                //失敗處理
                    if(ajaxoptions==='timeout'){

                    }else{

                    }
                },
                complete    :function(){
                //傳送後處理
                }
            });
        }
    }

    function get_ajax_load_info(){
    //ajax載入進度

        var img_info_flag=false;
        var img_read_group=document.getElementsByName('img_read_group');
        var img_read_frequency=document.getElementsByName('img_read_frequency');

        //學生陣列
        var arrys_user=<?php echo json_encode($arrys_user,true);?>;

        if((img_read_group.length===0)&&(img_read_frequency.length===0)){
            img_info_flag=true;

            //啟動表格排序設置
            $("table").tablesorter();

            //抓取
            var read_group_cno=0;
            var arry_read_group_cno_semester=[];
            var user_ids=document.getElementsByName('user_id');
            for(i=0;i<user_ids.length;i++){
                var user_id=user_ids[i];
                var read_group_cno_semester=parseInt(user_id.getAttribute('read_group_cno_semester'));
                read_group_cno=read_group_cno+read_group_cno_semester;
                arry_read_group_cno_semester.push(read_group_cno_semester);
            }

            //排序
            arry_read_group_cno_semester.sort(sortnumber);
            var arry_read_group_cno_semester_limit=[];
            for(j=0;j<5;j++){
                arry_read_group_cno_semester_limit.push(arry_read_group_cno_semester[j]);
            }

            //設定落後指標 only for gcp
            if(sess_school_code==='gcp'){
                //設置
                for(i=0;i<user_ids.length;i++){
                    var user_id=user_ids[i];
                    var read_group_cno_semester=user_id.getAttribute('read_group_cno_semester');
                    if(in_array(read_group_cno_semester,arry_read_group_cno_semester_limit)){
                        $(user_id).append("[落後]");
                    }
                }
            }

            //回填總本數
            $(oread_group_cno).append(read_group_cno);

            //回填平均本數
            $(oread_group_avg).append((read_group_cno/<?php echo (int)($numrow);?>).toFixed(1));

            <?php if($is_now_semester):?>
                //啟用年級平均資訊設置
                grade_avg_info_set(json_class_code,sess_school_code,grade_goal);
            <?php endif;?>

            //自動更新頁面
            at_update_page();
        }

        if(!img_info_flag){
            setTimeout(get_ajax_load_info,1000);
        }

        function sortnumber(a,b){
        //排序
            return a - b
        }
    }

    function blink(){
        try{
            var flag=$('#avg_read_group_info').attr('flag');
            if(flag==="star"){
                $('#avg_read_group_info').fadeIn(1000).fadeOut(1000);
            }else{
                return false;
            }
        }catch(err){
            return false;
        }
    }

    function blink_fadeout(){
        try{
            $oavg_read_group_info=$('#avg_read_group_info');
            var flag=$oavg_read_group_info.attr('flag');
            if(flag==="star"){
                $(oavg_read_group_info).fadeOut(1000,blink_fadein);
            }else{
                return false;
            }
        }catch(err){
            return false;
        }
    }

    function blink_fadein(obj){
        try{
            $oavg_read_group_info=$('#avg_read_group_info');
            var flag=$oavg_read_group_info.attr('flag');
            if(flag==="star"){
                $(oavg_read_group_info).fadeIn(1000,blink_fadeout);
            }else{
                return false;
            }
        }catch(err){
            return false;
        }
    }

    window.onload=function(){

        //設定動態高度
        var oIFC=parent.document.getElementById('IFC');
        var oparent_IFC=parent.parent.document.getElementById('IFC');
        oIFC.style.height=parseInt($(document).height()+50)+'px';
        oparent_IFC.style.height=parseInt($(document).height()+250)+'px';

        //學生陣列
        var arrys_user=<?php echo json_encode($arrys_user,true);?>;

        //ajax載入進度
        get_ajax_load_info();

        //滑鼠動作設置
        $('#mod_data_tbl th').mouseover(function(){
            $(this).css('cursor', 'pointer');
        });

        //啟用ajax設置
        ajax_set(arrys_user);

        //啟動閃爍
        setInterval(blink,1000);

        //提示文件
        <?php if(in_array($auth_sys_check_lv,array(5,22))):?>
            <?php if($sess_school_code==='gcp'):?>
                if(prompt_val==='no'){
                    //_prompt();
                }
            <?php endif;?>
        <?php endif;?>

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

        try{
            $.plot($("#flot_pie"),category_dataset,{
                series:{
                    pie:{show:true}
                },
                //legend:{show:false},
                //grid: {hoverable:true}
            });
        }catch(e){}
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