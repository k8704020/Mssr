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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_book_ranking');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //SESSION
        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用

        if(isset($_SESSION['m_book_ranking']['filter'])){
            $filter=$_SESSION['m_book_ranking']['filter'];
            if(isset($_SESSION['m_book_ranking']['class_code'])&&(trim($_SESSION['m_book_ranking']['class_code'])!=='')){
                $q_class_code=mysql_prep(trim($_SESSION['m_book_ranking']['class_code']));
            }
        }
        if(isset($_SESSION['m_book_ranking']['query_fields'])){
            $query_fields=$_SESSION['m_book_ranking']['query_fields'];
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
            }
        }

        if(isset($_SESSION['m_book_ranking']['class_code'])&&trim($_SESSION['m_book_ranking']['class_code'])!==''){
            $_SESSION['teacher_center']['class_code']=trim($_SESSION['m_book_ranking']['class_code']);
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //初始化, 選擇身份指標
        $choose_identity_flag=false;
        if(isset($sess_responsibilities)){
            $choose_identity_flag=true;
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
                    case 1:
                        if(($filter!='')&&(isset($q_class_code))){
                            //學生陣列
                            $q_class_code=mysql_prep(trim($_SESSION['m_book_ranking']['class_code']));
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                //網頁標題
                                $title="明日星球,教師中心";
                                page_sel_no_user($title);
                                die();
                            }else{
                                $arrys_user=explode("','",$users);
                                $user_id   =(int)$arrys_user[1];
                                $sql="
                                    SELECT
                                        `start`,
                                        `end`
                                    FROM `semester`
                                    WHERE 1=1
                                        AND `start`<='{$curdate}'
                                        AND `end`>='{$curdate}'
                                ";
                                $arry_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                                if(!empty($arry_result)){
                                    //學期時間範圍
                                    $semester_start =trim($arry_result[0]['start']);
                                    $semester_end   =trim($arry_result[0]['end']);
                                }else{
                                    die();
                                }
                            }

                            $query_sql="
                                SELECT
                                    *
                                FROM(
                                    SELECT
                                        COUNT(`sqry`.`user_id`) AS `borrow_cno`,
                                        `sqry`.`user_id`,
                                        `sqry`.`book_sid`
                                    FROM (
                                        SELECT
                                            `mssr_book_borrow_semester`.`user_id`,
                                            `mssr_book_borrow_semester`.`book_sid`
                                        FROM `mssr_book_borrow_semester`
                                        WHERE 1=1
                                            AND `mssr_book_borrow_semester`.`school_code`='{$sess_school_code}'
                                            AND `mssr_book_borrow_semester`.`user_id` IN ({$users})
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` >= '{$semester_start}'
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` <= '{$semester_end  }'
                                        GROUP BY `mssr_book_borrow_semester`.`book_sid`, `mssr_book_borrow_semester`.`user_id`
                                    ) AS `sqry`
                                    WHERE 1=1
                                    GROUP BY `sqry`.`book_sid`
                                ) AS `mqry`
                                WHERE 1=1
                                ORDER BY `mqry`.`borrow_cno` DESC
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
                            $q_class_code=mysql_prep(trim($_SESSION['m_book_ranking']['class_code']));
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                //網頁標題
                                $title="明日星球,教師中心";
                                page_sel_no_user($title);
                                die();
                            }else{
                                $arrys_user=explode("','",$users);
                                $user_id   =(int)$arrys_user[1];
                                $sql="
                                    SELECT
                                        `start`,
                                        `end`
                                    FROM `semester`
                                    WHERE 1=1
                                        AND `start`<='{$curdate}'
                                        AND `end`>='{$curdate}'
                                ";
                                $arry_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                                if(!empty($arry_result)){
                                    //學期時間範圍
                                    $semester_start =trim($arry_result[0]['start']);
                                    $semester_end   =trim($arry_result[0]['end']);
                                }else{
                                    die();
                                }
                            }

                            $query_sql="
                                SELECT
                                    *
                                FROM(
                                    SELECT
                                        COUNT(`sqry`.`user_id`) AS `borrow_cno`,
                                        `sqry`.`user_id`,
                                        `sqry`.`book_sid`
                                    FROM (
                                        SELECT
                                            `mssr_book_borrow_semester`.`user_id`,
                                            `mssr_book_borrow_semester`.`book_sid`
                                        FROM `mssr_book_borrow_semester`
                                        WHERE 1=1
                                            AND `mssr_book_borrow_semester`.`school_code`='{$sess_school_code}'
                                            AND `mssr_book_borrow_semester`.`user_id` IN ({$users})
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` >= '{$semester_start}'
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` <= '{$semester_end  }'
                                        GROUP BY `mssr_book_borrow_semester`.`book_sid`, `mssr_book_borrow_semester`.`user_id`
                                    ) AS `sqry`
                                    WHERE 1=1
                                    GROUP BY `sqry`.`book_sid`
                                ) AS `mqry`
                                WHERE 1=1
                                ORDER BY `mqry`.`borrow_cno` DESC
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
                            $q_class_code=mysql_prep(trim($_SESSION['m_book_ranking']['class_code']));
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                //網頁標題
                                $title="明日星球,教師中心";
                                page_sel_no_user($title);
                                die();
                            }else{
                                $arrys_user=explode("','",$users);
                                $user_id   =(int)$arrys_user[1];
                                $sql="
                                    SELECT
                                        `start`,
                                        `end`
                                    FROM `semester`
                                    WHERE 1=1
                                        AND `start`<='{$curdate}'
                                        AND `end`>='{$curdate}'
                                ";
                                $arry_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                                if(!empty($arry_result)){
                                    //學期時間範圍
                                    $semester_start =trim($arry_result[0]['start']);
                                    $semester_end   =trim($arry_result[0]['end']);
                                }else{
                                    die();
                                }
                            }

                            $query_sql="
                                SELECT
                                    *
                                FROM(
                                    SELECT
                                        COUNT(`sqry`.`user_id`) AS `borrow_cno`,
                                        `sqry`.`user_id`,
                                        `sqry`.`book_sid`
                                    FROM (
                                        SELECT
                                            `mssr_book_borrow_semester`.`user_id`,
                                            `mssr_book_borrow_semester`.`book_sid`
                                        FROM `mssr_book_borrow_semester`
                                        WHERE 1=1
                                            AND `mssr_book_borrow_semester`.`school_code`='{$sess_school_code}'
                                            AND `mssr_book_borrow_semester`.`user_id` IN ({$users})
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` >= '{$semester_start}'
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` <= '{$semester_end  }'
                                        GROUP BY `mssr_book_borrow_semester`.`book_sid`, `mssr_book_borrow_semester`.`user_id`
                                    ) AS `sqry`
                                    WHERE 1=1
                                    GROUP BY `sqry`.`book_sid`
                                ) AS `mqry`
                                WHERE 1=1
                                ORDER BY `mqry`.`borrow_cno` DESC
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
                            $q_class_code=mysql_prep(trim($_SESSION['m_book_ranking']['class_code']));
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                //網頁標題
                                $title="明日星球,教師中心";
                                page_sel_no_user($title);
                                die();
                            }else{
                                $arrys_user=explode("','",$users);
                                $user_id   =(int)$arrys_user[1];
                                $sql="
                                    SELECT
                                        `start`,
                                        `end`
                                    FROM `semester`
                                    WHERE 1=1
                                        AND `start`<='{$curdate}'
                                        AND `end`>='{$curdate}'
                                ";
                                $arry_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                                if(!empty($arry_result)){
                                    //學期時間範圍
                                    $semester_start =trim($arry_result[0]['start']);
                                    $semester_end   =trim($arry_result[0]['end']);
                                }else{
                                    die();
                                }
                            }

                            $query_sql="
                                SELECT
                                    *
                                FROM(
                                    SELECT
                                        COUNT(`sqry`.`user_id`) AS `borrow_cno`,
                                        `sqry`.`user_id`,
                                        `sqry`.`book_sid`
                                    FROM (
                                        SELECT
                                            `mssr_book_borrow_semester`.`user_id`,
                                            `mssr_book_borrow_semester`.`book_sid`
                                        FROM `mssr_book_borrow_semester`
                                        WHERE 1=1
                                            AND `mssr_book_borrow_semester`.`school_code`='{$sess_school_code}'
                                            AND `mssr_book_borrow_semester`.`user_id` IN ({$users})
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` >= '{$semester_start}'
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` <= '{$semester_end  }'
                                        GROUP BY `mssr_book_borrow_semester`.`book_sid`, `mssr_book_borrow_semester`.`user_id`
                                    ) AS `sqry`
                                    WHERE 1=1
                                    GROUP BY `sqry`.`book_sid`
                                ) AS `mqry`
                                WHERE 1=1
                                ORDER BY `mqry`.`borrow_cno` DESC
                            ";
                        }else{
                            //網頁標題
                            $title="明日星球,教師中心";
                            page_sel_class_code($title);
                            die();
                        }
                    break;

                    case 5:
                        //學生陣列
                        $users=arrys_users($conn_user,$sess_class_code,$date=date("Y-m-d"),$arry_conn_user);
                        if(empty($users)){
                            //網頁標題
                            $title="明日星球,教師中心";
                            page_sel_no_user($title);
                            die();
                        }else{
                            $arrys_user=explode("','",$users);
                            $user_id   =(int)$arrys_user[1];
                            $sql="
                                SELECT
                                    `start`,
                                    `end`
                                FROM `semester`
                                WHERE 1=1
                                    AND `start`<='{$curdate}'
                                    AND `end`>='{$curdate}'
                            ";
                            $arry_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                            if(!empty($arry_result)){
                                //學期時間範圍
                                $semester_start =trim($arry_result[0]['start']);
                                $semester_end   =trim($arry_result[0]['end']);
                            }else{
                                die();
                            }
                        }

                        $query_sql="
                            SELECT
                                *
                            FROM(
                                SELECT
                                    COUNT(`sqry`.`user_id`) AS `borrow_cno`,
                                    `sqry`.`user_id`,
                                    `sqry`.`book_sid`
                                FROM (
                                    SELECT
                                        `mssr_book_borrow_semester`.`user_id`,
                                        `mssr_book_borrow_semester`.`book_sid`
                                    FROM `mssr_book_borrow_semester`
                                    WHERE 1=1
                                        AND `mssr_book_borrow_semester`.`school_code`='{$sess_school_code}'
                                        AND `mssr_book_borrow_semester`.`user_id` IN ({$users})
                                        AND `mssr_book_borrow_semester`.`borrow_sdate` >= '{$semester_start}'
                                        AND `mssr_book_borrow_semester`.`borrow_sdate` <= '{$semester_end  }'
                                    GROUP BY `mssr_book_borrow_semester`.`book_sid`, `mssr_book_borrow_semester`.`user_id`
                                ) AS `sqry`
                                WHERE 1=1
                                GROUP BY `sqry`.`book_sid`
                            ) AS `mqry`
                            WHERE 1=1
                            ORDER BY `mqry`.`borrow_cno` DESC
                        ";
                    break;

                    case 12:
                        $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                history.back(-1);
                            </script>
                        ";
                        die($jscript_back);
                    break;

                    case 14:
                        if(($filter!='')&&(isset($q_class_code))){
                            //學生陣列
                            $q_class_code=mysql_prep(trim($_SESSION['m_book_ranking']['class_code']));
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                //網頁標題
                                $title="明日星球,教師中心";
                                page_sel_no_user($title);
                                die();
                            }else{
                                $arrys_user=explode("','",$users);
                                $user_id   =(int)$arrys_user[1];
                                $sql="
                                    SELECT
                                        `start`,
                                        `end`
                                    FROM `semester`
                                    WHERE 1=1
                                        AND `start`<='{$curdate}'
                                        AND `end`>='{$curdate}'
                                ";
                                $arry_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                                if(!empty($arry_result)){
                                    //學期時間範圍
                                    $semester_start =trim($arry_result[0]['start']);
                                    $semester_end   =trim($arry_result[0]['end']);
                                }else{
                                    die();
                                }
                            }

                            $query_sql="
                                SELECT
                                    *
                                FROM(
                                    SELECT
                                        COUNT(`sqry`.`user_id`) AS `borrow_cno`,
                                        `sqry`.`user_id`,
                                        `sqry`.`book_sid`
                                    FROM (
                                        SELECT
                                            `mssr_book_borrow_semester`.`user_id`,
                                            `mssr_book_borrow_semester`.`book_sid`
                                        FROM `mssr_book_borrow_semester`
                                        WHERE 1=1
                                            AND `mssr_book_borrow_semester`.`school_code`='{$sess_school_code}'
                                            AND `mssr_book_borrow_semester`.`user_id` IN ({$users})
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` >= '{$semester_start}'
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` <= '{$semester_end  }'
                                        GROUP BY `mssr_book_borrow_semester`.`book_sid`, `mssr_book_borrow_semester`.`user_id`
                                    ) AS `sqry`
                                    WHERE 1=1
                                    GROUP BY `sqry`.`book_sid`
                                ) AS `mqry`
                                WHERE 1=1
                                ORDER BY `mqry`.`borrow_cno` DESC
                            ";
                        }else{
                            //學生陣列
                            $users=arrys_users($conn_user,$sess_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                //網頁標題
                                $title="明日星球,教師中心";
                                page_sel_no_user($title);
                                die();
                            }else{
                                $arrys_user=explode("','",$users);
                                $user_id   =(int)$arrys_user[1];
                                $sql="
                                    SELECT
                                        `start`,
                                        `end`
                                    FROM `semester`
                                    WHERE 1=1
                                        AND `start`<='{$curdate}'
                                        AND `end`>='{$curdate}'
                                ";
                                $arry_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                                if(!empty($arry_result)){
                                    //學期時間範圍
                                    $semester_start =trim($arry_result[0]['start']);
                                    $semester_end   =trim($arry_result[0]['end']);
                                }else{
                                    die();
                                }
                            }

                            $query_sql="
                                SELECT
                                    *
                                FROM(
                                    SELECT
                                        COUNT(`sqry`.`user_id`) AS `borrow_cno`,
                                        `sqry`.`user_id`,
                                        `sqry`.`book_sid`
                                    FROM (
                                        SELECT
                                            `mssr_book_borrow_semester`.`user_id`,
                                            `mssr_book_borrow_semester`.`book_sid`
                                        FROM `mssr_book_borrow_semester`
                                        WHERE 1=1
                                            AND `mssr_book_borrow_semester`.`school_code`='{$sess_school_code}'
                                            AND `mssr_book_borrow_semester`.`user_id` IN ({$users})
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` >= '{$semester_start}'
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` <= '{$semester_end  }'
                                        GROUP BY `mssr_book_borrow_semester`.`book_sid`, `mssr_book_borrow_semester`.`user_id`
                                    ) AS `sqry`
                                    WHERE 1=1
                                    GROUP BY `sqry`.`book_sid`
                                ) AS `mqry`
                                WHERE 1=1
                                ORDER BY `mqry`.`borrow_cno` DESC
                            ";
                        }
                    break;

                    case 16:
                        if(($filter!='')&&(isset($q_class_code))){
                            //學生陣列
                            $q_class_code=mysql_prep(trim($_SESSION['m_book_ranking']['class_code']));
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                //網頁標題
                                $title="明日星球,教師中心";
                                page_sel_no_user($title);
                                die();
                            }else{
                                $arrys_user=explode("','",$users);
                                $user_id   =(int)$arrys_user[1];
                                $sql="
                                    SELECT
                                        `start`,
                                        `end`
                                    FROM `semester`
                                    WHERE 1=1
                                        AND `start`<='{$curdate}'
                                        AND `end`>='{$curdate}'
                                ";
                                $arry_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                                if(!empty($arry_result)){
                                    //學期時間範圍
                                    $semester_start =trim($arry_result[0]['start']);
                                    $semester_end   =trim($arry_result[0]['end']);
                                }else{
                                    die();
                                }
                            }

                            $query_sql="
                                SELECT
                                    *
                                FROM(
                                    SELECT
                                        COUNT(`sqry`.`user_id`) AS `borrow_cno`,
                                        `sqry`.`user_id`,
                                        `sqry`.`book_sid`
                                    FROM (
                                        SELECT
                                            `mssr_book_borrow_semester`.`user_id`,
                                            `mssr_book_borrow_semester`.`book_sid`
                                        FROM `mssr_book_borrow_semester`
                                        WHERE 1=1
                                            AND `mssr_book_borrow_semester`.`school_code`='{$sess_school_code}'
                                            AND `mssr_book_borrow_semester`.`user_id` IN ({$users})
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` >= '{$semester_start}'
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` <= '{$semester_end  }'
                                        GROUP BY `mssr_book_borrow_semester`.`book_sid`, `mssr_book_borrow_semester`.`user_id`
                                    ) AS `sqry`
                                    WHERE 1=1
                                    GROUP BY `sqry`.`book_sid`
                                ) AS `mqry`
                                WHERE 1=1
                                ORDER BY `mqry`.`borrow_cno` DESC
                            ";
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
                            $q_class_code=mysql_prep(trim($_SESSION['m_book_ranking']['class_code']));
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                //網頁標題
                                $title="明日星球,教師中心";
                                page_sel_no_user($title);
                                die();
                            }else{
                                $arrys_user=explode("','",$users);
                                $user_id   =(int)$arrys_user[1];
                                $sql="
                                    SELECT
                                        `start`,
                                        `end`
                                    FROM `semester`
                                    WHERE 1=1
                                        AND `start`<='{$curdate}'
                                        AND `end`>='{$curdate}'
                                ";
                                $arry_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                                if(!empty($arry_result)){
                                    //學期時間範圍
                                    $semester_start =trim($arry_result[0]['start']);
                                    $semester_end   =trim($arry_result[0]['end']);
                                }else{
                                    die();
                                }
                            }

                            $query_sql="
                                SELECT
                                    *
                                FROM(
                                    SELECT
                                        COUNT(`sqry`.`user_id`) AS `borrow_cno`,
                                        `sqry`.`user_id`,
                                        `sqry`.`book_sid`
                                    FROM (
                                        SELECT
                                            `mssr_book_borrow_semester`.`user_id`,
                                            `mssr_book_borrow_semester`.`book_sid`
                                        FROM `mssr_book_borrow_semester`
                                        WHERE 1=1
                                            AND `mssr_book_borrow_semester`.`school_code`='{$sess_school_code}'
                                            AND `mssr_book_borrow_semester`.`user_id` IN ({$users})
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` >= '{$semester_start}'
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` <= '{$semester_end  }'
                                        GROUP BY `mssr_book_borrow_semester`.`book_sid`, `mssr_book_borrow_semester`.`user_id`
                                    ) AS `sqry`
                                    WHERE 1=1
                                    GROUP BY `sqry`.`book_sid`
                                ) AS `mqry`
                                WHERE 1=1
                                ORDER BY `mqry`.`borrow_cno` DESC
                            ";
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
                            $q_class_code=mysql_prep(trim($_SESSION['m_book_ranking']['class_code']));
                            $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
                            if(empty($users)){
                                //網頁標題
                                $title="明日星球,教師中心";
                                page_sel_no_user($title);
                                die();
                            }else{
                                $arrys_user=explode("','",$users);
                                $user_id   =(int)$arrys_user[1];
                                $sql="
                                    SELECT
                                        `start`,
                                        `end`
                                    FROM `semester`
                                    WHERE 1=1
                                        AND `start`<='{$curdate}'
                                        AND `end`>='{$curdate}'
                                ";
                                $arry_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                                if(!empty($arry_result)){
                                    //學期時間範圍
                                    $semester_start =trim($arry_result[0]['start']);
                                    $semester_end   =trim($arry_result[0]['end']);
                                }else{
                                    die();
                                }
                            }

                            $query_sql="
                                SELECT
                                    *
                                FROM(
                                    SELECT
                                        COUNT(`sqry`.`user_id`) AS `borrow_cno`,
                                        `sqry`.`user_id`,
                                        `sqry`.`book_sid`
                                    FROM (
                                        SELECT
                                            `mssr_book_borrow_semester`.`user_id`,
                                            `mssr_book_borrow_semester`.`book_sid`
                                        FROM `mssr_book_borrow_semester`
                                        WHERE 1=1
                                            AND `mssr_book_borrow_semester`.`school_code`='{$sess_school_code}'
                                            AND `mssr_book_borrow_semester`.`user_id` IN ({$users})
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` >= '{$semester_start}'
                                            AND `mssr_book_borrow_semester`.`borrow_sdate` <= '{$semester_end  }'
                                        GROUP BY `mssr_book_borrow_semester`.`book_sid`, `mssr_book_borrow_semester`.`user_id`
                                    ) AS `sqry`
                                    WHERE 1=1
                                    GROUP BY `sqry`.`book_sid`
                                ) AS `mqry`
                                WHERE 1=1
                                ORDER BY `mqry`.`borrow_cno` DESC
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

    //---------------------------------------------------
    //學期處理
    //---------------------------------------------------

        $arrys_user=explode("','",$users);
        $user_id   =(int)$arrys_user[1];
        $sql="
            SELECT
                `start`,
                `end`
            FROM `semester`
            WHERE 1=1
                AND `start`<='{$curdate}'
                AND `end`>='{$curdate}'
        ";
        $arry_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
        if(!empty($arry_result)){
            //學期時間範圍
            $semester_start =trim($arry_result[0]['start']);
            $semester_end   =trim($arry_result[0]['end']);
        }else{
            die();
        }

    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        if($choose_identity_flag){
            $numrow=0;  //資料總筆數
            $psize =20; //單頁筆數,預設20筆
            $pnos  =0;  //分頁筆數
            $pinx  =1;  //目前分頁索引,預設1
            $sinx  =0;  //值域起始值
            $einx  =0;  //值域終止值

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

            $numrow=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
            $numrow=count($numrow);

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
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx-1,$psize),$arry_conn_mssr);
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
        global $arry_conn_mssr;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $semester_start;
        global $semester_end;

        global $arrys_result;
        global $config_arrys;
        global $conn_mssr;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=4;  //欄位個數
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
    <script type="text/javascript" src="../../../../lib/js/table/code.js"></script>

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
                <!-- 統計資料表格 開始 -->
                <div class="mod_data_tbl_outline" style="margin-top:35px;">
                    <table id="mod_data_tbl" border="0" width="95%" height="40px" cellpadding="5" cellspacing="0" style="" class="font-weight1 font-family1 fc_green0">
                        <tr align="center" valign="middle" class="fsize_16">
                            <td align="left" width="550px">
                                <span class="fsize_16">
                                    ●本頁資料計算時間為
                                    <span class="fc_red1"><?php echo $semester_start;?></span>
                                    ~
                                    <span class="fc_red1"><?php echo $semester_end;?></span>
                                    為止
                                </span>
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
                            <th width="150px" height="40px">書本排名  </th>
                            <th width="150px" height="40px">借閱人數  </th>
                            <th width="300px" height="40px">書籍名稱  </th>
                            <td width="" height="40px"
                            style="
                                text-align:center;
                                font-family:'微軟正黑體','標楷體','新細明體';
                                font-weight:bold;
                                color:#ffffff;
                                background-color:#B2D4DD;
                                border:1px solid #ffffff;
                            ">
                                書籍編號
                                <input type="button" value="展開" class="" onclick="view_change(this,this.value);" onmouseover="this.style.cursor='pointer'">
                            </td>
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

                            $rs_borrow_cno=(int)$rs_borrow_cno;

                        //---------------------------------------------------
                        //特殊處理
                        //---------------------------------------------------

                            //-----------------------------------------------
                            //查找,書籍資訊
                            //-----------------------------------------------

                                $rs_book_name='';
                                $rs_book_type='';
                                $rs_book_code='';
                                $get_book_info=get_book_info($conn_mssr,trim($rs_book_sid),$array_filter=array('book_isbn_10','book_isbn_13','book_name'),$arry_conn_mssr);
                                if(!empty($get_book_info)){

                                    //book_name     書籍名稱
                                    $rs_book_name=trim($get_book_info[0]['book_name']);
                                    if(mb_strlen($rs_book_name)>20){
                                        $rs_book_name=mb_substr($rs_book_name,0,20)."..";
                                    }

                                    $rs_book_type="";
                                    $rs_book_code="";
                                    $rs_book_isbn_10=trim($get_book_info[0]['book_isbn_10']);
                                    $rs_book_isbn_13=trim($get_book_info[0]['book_isbn_13']);
                                    $rs_book_library_code=trim($get_book_info[0]['book_library_code']);
                                    switch(trim($rs_book_library_code)){
                                        case '':
                                            $rs_book_type='mssr_book_class';
                                            $rs_book_code=$rs_book_isbn_13;
                                        break;
                                        default:
                                            $rs_book_type='mssr_book_library';
                                            $rs_book_code=$rs_book_library_code;
                                        break;
                                    }
                                }else{}
                        ?>
                        <tr class="fsize_16">
                            <td height="35px" align="center" valign="middle">
                                <?php if($pinx!==1):?>
                                    第<?php echo $inx+1+(($pinx-1)*$psize);?>名
                                <?php else:?>
                                    第<?php echo $inx+1;?>名
                                <?php endif;?>
                            </td>
                            <td height="35px" align="center" valign="middle">
                                <?php echo $rs_borrow_cno;?>
                                <input type="button" value="詳細" class="ibtn_gr3020" onclick="detailed('<?php echo htmlspecialchars($rs_book_sid);?>');"
                                onmouseover="this.style.cursor='pointer'">
                            </td>
                            <td height="35px" align="center" valign="middle"><?php echo htmlspecialchars($rs_book_name);?></td>
                            <td height="35px" align="left" valign="middle">
                                <?php if(trim($rs_book_type)==='mssr_book_class'):?>
                                    13碼:<?php echo htmlspecialchars($rs_book_code);?><br/>
                                    <span class="isbn_10" style="display:none;">
                                        10碼:<?php echo htmlspecialchars($rs_book_isbn_10);?>
                                    </span>
                                <?php else:?>
                                    圖書館:<?php echo htmlspecialchars($rs_book_code);?>
                                <?php endif;?>
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
                            <!-- <td width="250px" align="right">
                                <span class="fc_brown0" style="position:relative;right:20px;" onclick="page_click(this,10)" onmouseover="this.style.cursor='pointer'">一頁10筆</span>
                                <span class="fc_brown0" style="position:relative;right:20px;" onclick="page_click(this,20)" onmouseover="this.style.cursor='pointer'">一頁20筆</span>
                                <span class="fc_brown0" style="position:relative;right:20px;" onclick="page_click(this,<?php echo $numrow;?>)" onmouseover="this.style.cursor='pointer'">觀看全部</span>
                            </td> -->
                        </tr>
                    </table>
                </div>
                <!-- 資料表格 結束 -->
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
    var $isbn_10=$('.isbn_10');
    var semester_start='<?php echo $semester_start;?>';
    var semester_end='<?php echo $semester_end;?>';

    function detailed(book_sid){
        var url ='';
        var page=str_repeat('../',0)+'detailed/index.php';
        var arg ={
            'book_sid':book_sid,
            'semester_start':semester_start,
            'semester_end':semester_end
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

    window.onload=function(){

        //設定動態高度
        var oIFC=parent.document.getElementById('IFC');
        var oparent_IFC=parent.parent.document.getElementById('IFC');
        oIFC.style.height=parseInt($(document).height()+50)+'px';
        oparent_IFC.style.height=parseInt($(document).height()+250)+'px';

        //啟動排序設置
        $("table").tablesorter();

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
        var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);

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

    function view_change(obj,type){
    //模式切換
        if(trim(type)==='展開'){
            obj.value='關閉';
            $isbn_10.show();
        }else{
            obj.value='展開';
            $isbn_10.hide();
        }
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
                            您目前所在的班級沒有學生!
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