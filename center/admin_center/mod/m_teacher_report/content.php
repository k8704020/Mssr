<?php
////-------------------------------------------------------
////學校人員專區
////-------------------------------------------------------
//
//    //---------------------------------------------------
//    //設定與引用
//    //---------------------------------------------------
//
//        //SESSION
//        @session_start();
//
//        //啟用BUFFER
//        @ob_start();
//
//        //外掛設定檔
//        require_once(str_repeat("../",2).'config/config.php');
//
//        //外掛函式檔
//        $funcs=array(
//                    APP_ROOT.'inc/code'
//                    );
//        func_load($funcs,true);
//
//        //清除並停用BUFFER
//        @ob_end_clean();
//
//    //---------------------------------------------------
//    //有無登入
//    //---------------------------------------------------
//
//        //初始化，承接變數
//        $arrys_sess_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
//        if(empty($arrys_sess_login_info)){
//            $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
//            $jscript_back="
//                <script>
//                    alert('{$msg}');
//                    parent.location.href='../../index.php';
//                </script>
//            ";
//            die($jscript_back);
//        }
//
//    //---------------------------------------------------
//    //重複登入
//    //---------------------------------------------------
//
//        if(in_array('read_the_registration_code',$config_arrys['user_area'])){
//        //清空閱讀登記條碼版登入資訊
//
//            $_SESSION['config']['user_tbl']=array();
//            $_SESSION['config']['user_type']='';
//            $_SESSION['config']['user_lv']=0;
//            if(in_array('read_the_registration_code',$_SESSION['config']['user_area'])){
//                foreach($_SESSION['config']['user_area'] as $inx=>$area){
//                    if(trim($area)==='read_the_registration_code'){
//                        unset($_SESSION['config']['user_area'][$inx]);
//                    }
//                }
//            }
//        }
//
//    //---------------------------------------------------
//    //SESSION
//    //---------------------------------------------------
//
//        //SESSION
//        if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
//        if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
//        if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
//        if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
//        if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
//        if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
//        if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
//        if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
//        if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
//        if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
//        if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
//            $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
//            foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
//                $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
//            }
//        }
//
//    //---------------------------------------------------
//    //接收參數
//    //---------------------------------------------------
//
//        //SESSION
//        $filter      ='';   //查詢條件式
//        $query_fields='';   //查詢欄位,顯示用
//
//        if(isset($_SESSION['sha']['query']['m_user_view']['filter'])){
//            $filter=$_SESSION['sha']['query']['m_user_view']['filter'];
//            $filter=str_replace("AND","AND `member`.",$filter);
//        }
//        if(isset($_SESSION['sha']['query']['m_user_view']['query_fields'])){
//            $query_fields=$_SESSION['sha']['query']['m_user_view']['query_fields'];
//        }
//        if(isset($_SESSION['sha']['query']['m_user_view']['q_mode'])){
//            $q_mode=trim($_SESSION['sha']['query']['m_user_view']['q_mode']);
//        }else{
//            $q_mode='all';
//        }
//
//    //---------------------------------------------------
//    //設定參數
//    //---------------------------------------------------
//
//        $sess_school_code   =mysql_prep($sess_school_code);
//        $sess_user_lv       =(int)$sess_user_lv;
//
//    //---------------------------------------------------
//    //檢驗參數
//    //---------------------------------------------------
//
//        //老師一定要帶班
//        if(in_array($sess_user_lv,array(3))){
//            if(!isset($sess_arry_class_info)){
//                $msg="您沒有任何班級，無法使用本系統!";
//                $jscript_back="
//                    <script>
//                        alert('{$msg}');
//                        parent.location.href='../../index.php';
//                    </script>
//                ";
//                die($jscript_back);
//            }
//        }
//
//    //---------------------------------------------------
//    //串接SQL
//    //---------------------------------------------------
//
//        //-----------------------------------------------
//        //資料庫
//        //-----------------------------------------------
//
//            //建立連線 user
//            $conn_user     =conn($db_type='mysql',$arry_conn_user);
//
//            $sess_user_id  =(int)$sess_user_id;
//            $arrys_users   =array();
//            $arry_personnel=array();
//
//            $users         ="";
//
//        //-----------------------------------------------
//        //班級人員查詢
//        //-----------------------------------------------
//
//            //若為老師
//            if(in_array($sess_user_lv,array(3))){
//                foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
//                    $sess_class_code =mysql_prep(trim($sess_arry_class_info['class_code']));
//                    $arrys_users_info=arrys_users($conn_user,$sess_class_code,date("Y-m-d"),$arry_conn_user);
//                    if(!empty($arrys_users_info)){
//                        foreach($arrys_users_info as $arry_users_info){
//                            $rs_uid=(int)$arry_users_info['uid'];
//                            if(!in_array($rs_uid,$arrys_users)){
//                                $arrys_users[]=$rs_uid;
//                            }
//                        }
//                    }
//                }
//            }
//
//        //-----------------------------------------------
//        //家長查詢
//        //-----------------------------------------------
//
//            //if(!empty($arrys_users)){
//            //    foreach($arrys_users as $uid){
//            //        $uid=(int)$uid;
//            //        $sql="
//            //            SELECT
//            //                `uid_main`
//            //            FROM `kinship`
//            //            WHERE 1=1
//            //                AND `uid_sub`={$uid}
//            //        ";
//            //        $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
//            //        if(!empty($arrys_result)){
//            //            foreach($arrys_result as $arry_result){
//            //                $rs_uid_main  =(int)$arry_result['uid_main'];
//            //                $arrys_users[]=$rs_uid_main;
//            //            }
//            //        }
//            //    }
//            //}
//
//            //處理
//            if(!empty($arrys_users)){
//                $users="'";
//                $users.=implode("','",$arrys_users);
//                $users.="'";
//            }
//
//        //-----------------------------------------------
//        //學校人員查詢
//        //-----------------------------------------------
//
//            $sql="
//                SELECT
//                    `uid`,
//                    `responsibilities`
//                FROM `personnel`
//                WHERE 1=1
//                    AND `responsibilities`<>4
//                    AND `school_code`     ='{$sess_school_code}'
//                    AND `start`           <=CURDATE()
//                    AND `end`             >=CURDATE()
//            ";
//            $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
//            if(!empty($arrys_result)){
//                foreach($arrys_result as $arry_result){
//                    $rs_uid                 =(int)$arry_result['uid'];
//                    $rs_responsibilities    =(int)$arry_result['responsibilities'];
//                    $arry_personnel[$rs_uid]=$rs_responsibilities;
//                }
//            }
//
//        //-----------------------------------------------
//        //權限陣列查詢
//        //-----------------------------------------------
//
//            $arrys_permissions_info=get_permissions_info($conn_user);
//
//        //-----------------------------------------------
//        //SQL查詢(學校人員)
//        //-----------------------------------------------
//
//            //結果集陣列
//            $db_results=array();
//
//            switch(trim($q_mode)){
//
//                case 'kinship':
//
//                    $query_class_code=mysql_prep(trim($_SESSION['sha']['query']['m_user_view']['q_class_code']));
//
//                    if(in_array((int)$query_class_code,array(1,2,3,4,5,6,7,8,9,10,11,12))){
//                        $query_class_code=(int)$query_class_code;
//                        $query_class_code="AND `class`.`grade`={$query_class_code}";
//                    }else{
//                        $query_class_code="AND `student`.`class_code`='{$query_class_code}'";
//                    }
//
//                    if($filter!==''){
//                        $sql="
//                            SELECT
//                                `tbl1`.`uid_main` AS 'uid',
//
//                                `member`.`name`,
//                                `member`.`sex`,
//                                `member`.`account`,
//                                `member`.`password`,
//                                `member`.`permission`,
//                                `member`.`build_time`
//                            FROM `kinship` AS `tbl1`
//
//                                INNER JOIN (
//                                    SELECT
//                                        `member`.`uid`
//                                    FROM `member_school`
//                                        INNER JOIN `member` ON
//                                        `member_school`.`uid`=`member`.`uid`
//                                        INNER JOIN `student` ON
//                                        `member_school`.`uid`=`student`.`uid`
//                                        INNER JOIN `class` ON
//                                        `student`.`class_code`=`class`.`class_code`
//                                    WHERE 1=1
//                                        -- 排除, 中大團隊
//                                        AND `member`.`uid`                <>1258
//
//                                        AND `member_school`.`school_code` = '{$sess_school_code}'
//                                        AND `member_school`.`start`       <=CURDATE()
//                                        AND `member_school`.`end`         ='0000-00-00'
//
//                                        AND `member`.`permission`         <>'x'
//
//                                        AND `student`.`start`             <=CURDATE()
//                                        AND `student`.`end`               >=CURDATE()
//
//                                        AND `class`.`classroom`           <>29
//
//                                        {$query_class_code}
//
//                                        -- FILTER在此
//                                        {$filter}
//
//                                    ) AS `tbl2`
//                                ON `tbl1`.`uid_sub`=`tbl2`.`uid`
//
//                                INNER JOIN `member` ON
//                                `tbl1`.`uid_main`=`member`.`uid`
//
//                            ORDER BY `tbl1`.`uid_main` DESC
//                        ";
//                    }else{
//                        $sql="
//                            SELECT
//                                `tbl1`.`uid_main` AS 'uid',
//
//                                `member`.`name`,
//                                `member`.`sex`,
//                                `member`.`account`,
//                                `member`.`password`,
//                                `member`.`permission`,
//                                `member`.`build_time`
//                            FROM `kinship` AS `tbl1`
//
//                                INNER JOIN (
//                                    SELECT
//                                        `member`.`uid`
//                                    FROM `member_school`
//                                        INNER JOIN `member` ON
//                                        `member_school`.`uid`=`member`.`uid`
//                                        INNER JOIN `student` ON
//                                        `member_school`.`uid`=`student`.`uid`
//                                        INNER JOIN `class` ON
//                                        `student`.`class_code`=`class`.`class_code`
//                                    WHERE 1=1
//                                        -- 排除, 中大團隊
//                                        AND `member`.`uid`                <>1258
//
//                                        AND `member_school`.`school_code` = '{$sess_school_code}'
//                                        AND `member_school`.`start`       <=CURDATE()
//                                        AND `member_school`.`end`         ='0000-00-00'
//
//                                        AND `member`.`permission`         <>'x'
//
//                                        AND `student`.`start`             <=CURDATE()
//                                        AND `student`.`end`               >=CURDATE()
//
//                                        AND `class`.`classroom`           <>29
//
//                                        {$query_class_code}
//
//                                    ) AS `tbl2`
//                                ON `tbl1`.`uid_sub`=`tbl2`.`uid`
//
//                                INNER JOIN `member` ON
//                                `tbl1`.`uid_main`=`member`.`uid`
//
//                            ORDER BY `tbl1`.`uid_main` DESC
//                        ";
//                    }
//                break;
//
//                case 'class_code':
//
//                    $query_class_code=mysql_prep(trim($_SESSION['sha']['query']['m_user_view']['q_class_code']));
//
//                    if(in_array((int)$query_class_code,array(1,2,3,4,5,6,7,8,9,10,11,12))){
//                        $query_class_code=(int)$query_class_code;
//                        $query_class_code="AND `class`.`grade`={$query_class_code}";
//                    }else{
//                        $query_class_code="AND `student`.`class_code`='{$query_class_code}'";
//                    }
//
//                    if($filter!==''){
//                        $sql="
//                            SELECT
//                                *
//                            FROM(
//                            #    SELECT
//                            #        `member`.`uid`,
//                            #        `member`.`name`,
//                            #        `member`.`sex`,
//                            #        `member`.`account`,
//                            #        `member`.`permission`,
//                            #        `member`.`build_time`,
//                            #
//                            #        99 AS 'number'
//                            #    FROM `member_school`
//                            #        INNER JOIN `member` ON
//                            #        `member_school`.`uid`=`member`.`uid`
//                            #        INNER JOIN `teacher` ON
//                            #        `member_school`.`uid`=`teacher`.`uid`
//                            #    WHERE 1=1
//                            #        -- 排除, 中大團隊
//                            #        AND `member`.`uid`                <>1258
//                            #
//                            #        AND `member_school`.`school_code` = '{$sess_school_code}'
//                            #        AND `member_school`.`start`       <=CURDATE()
//                            #        AND `member_school`.`end`         ='0000-00-00'
//                            #
//                            #        AND `member`.`permission`         <>'x'
//                            #
//                            #        AND `teacher`.`class_code`        ='{$query_class_code}'
//                            #
//                            #        -- FILTER在此
//                            #        {$filter}
//                            #
//                            #UNION
//
//                                SELECT
//                                    `member`.`uid`,
//                                    `member`.`name`,
//                                    `member`.`sex`,
//                                    `member`.`account`,
//                                    `member`.`password`,
//                                    `member`.`permission`,
//                                    `member`.`build_time`,
//
//                                    `student`.`number` AS 'number'
//                                FROM `member_school`
//                                    INNER JOIN `member` ON
//                                    `member_school`.`uid`=`member`.`uid`
//                                    INNER JOIN `student` ON
//                                    `member_school`.`uid`=`student`.`uid`
//                                    INNER JOIN `class` ON
//                                    `student`.`class_code`=`class`.`class_code`
//                                WHERE 1=1
//                                    -- 排除, 中大團隊
//                                    AND `member`.`uid`                <>1258
//
//                                    AND `member_school`.`school_code` = '{$sess_school_code}'
//                                    AND `member_school`.`start`       <=CURDATE()
//                                    AND `member_school`.`end`         ='0000-00-00'
//
//                                    AND `member`.`permission`         <>'x'
//
//                                    AND `student`.`start`             <=CURDATE()
//                                    AND `student`.`end`               >=CURDATE()
//
//                                    AND `class`.`classroom`           <>29
//
//                                    {$query_class_code}
//
//                                    -- FILTER在此
//                                    {$filter}
//
//                            ) AS `sqry`
//                            WHERE 1=1
//                            ORDER BY `sqry`.`number` ASC
//                        ";
//                    }else{
//                        $sql="
//                            SELECT
//                                *
//                            FROM(
//                            #    SELECT
//                            #        `member`.`uid`,
//                            #        `member`.`name`,
//                            #        `member`.`sex`,
//                            #        `member`.`account`,
//                            #        `member`.`permission`,
//                            #        `member`.`build_time`,
//                            #
//                            #        99 AS 'number'
//                            #    FROM `member_school`
//                            #        INNER JOIN `member` ON
//                            #        `member_school`.`uid`=`member`.`uid`
//                            #        INNER JOIN `teacher` ON
//                            #        `member_school`.`uid`=`teacher`.`uid`
//                            #    WHERE 1=1
//                            #        -- 排除, 中大團隊
//                            #        AND `member`.`uid`                <>1258
//                            #
//                            #        AND `member_school`.`school_code` = '{$sess_school_code}'
//                            #        AND `member_school`.`start`       <=CURDATE()
//                            #        AND `member_school`.`end`         ='0000-00-00'
//                            #
//                            #        AND `member`.`permission`         <>'x'
//                            #
//                            #        AND `teacher`.`class_code`        ='{$query_class_code}'
//                            #
//                            #UNION
//
//                                SELECT
//                                    `member`.`uid`,
//                                    `member`.`name`,
//                                    `member`.`sex`,
//                                    `member`.`account`,
//                                    `member`.`password`,
//                                    `member`.`permission`,
//                                    `member`.`build_time`,
//
//                                    `student`.`number` AS 'number'
//                                FROM `member_school`
//                                    INNER JOIN `member` ON
//                                    `member_school`.`uid`=`member`.`uid`
//                                    INNER JOIN `student` ON
//                                    `member_school`.`uid`=`student`.`uid`
//                                    INNER JOIN `class` ON
//                                    `student`.`class_code`=`class`.`class_code`
//                                WHERE 1=1
//                                    -- 排除, 中大團隊
//                                    AND `member`.`uid`                <>1258
//
//                                    AND `member_school`.`school_code` = '{$sess_school_code}'
//                                    AND `member_school`.`start`       <=CURDATE()
//                                    AND `member_school`.`end`         ='0000-00-00'
//
//                                    AND `member`.`permission`         <>'x'
//
//                                    AND `student`.`start`             <=CURDATE()
//                                    AND `student`.`end`               >=CURDATE()
//
//                                    AND `class`.`classroom`           <>29
//
//                                    {$query_class_code}
//
//                            ) AS `sqry`
//                            WHERE 1=1
//                            ORDER BY `sqry`.`number` ASC
//                        ";
//                    }
//                break;
//
//                case 'all':
//                    if(in_array($sess_user_lv,array(1,2,99))){
//                    //學校行政人員,推廣人員
//                        if($filter!==''){
//                            $sql="
//                                SELECT
//                                    `member`.`uid`,
//                                    `member`.`name`,
//                                    `member`.`sex`,
//                                    `member`.`account`,
//                                    `member`.`password`,
//                                    `member`.`permission`
//                                FROM `member_school`
//                                    INNER JOIN `member` ON
//                                    `member_school`.`uid`=`member`.`uid`
//                                WHERE 1=1
//                                    -- 排除, 中大團隊
//                                    AND `member`.`uid`                <>1258
//
//                                    AND `member_school`.`school_code` = '{$sess_school_code}'
//                                    AND `member_school`.`start`       <=CURDATE()
//                                    AND `member_school`.`end`         ='0000-00-00'
//
//                                    AND `member`.`permission`         <>'x'
//
//                                    -- FILTER在此
//                                    {$filter}
//
//                                ORDER BY `member`.`uid` DESC
//                            ";
//                        }else{
//                            $sql="
//                                SELECT
//                                    `member`.`uid`,
//                                    `member`.`name`,
//                                    `member`.`sex`,
//                                    `member`.`account`,
//                                    `member`.`password`,
//                                    `member`.`permission`
//                                FROM `member_school`
//                                    INNER JOIN `member` ON
//                                    `member_school`.`uid`=`member`.`uid`
//                                WHERE 1=1
//                                    -- 排除, 中大團隊
//                                    AND `member`.`uid`                <>1258
//
//                                    AND `member_school`.`school_code` = '{$sess_school_code}'
//                                    AND `member_school`.`start`       <=CURDATE()
//                                    AND `member_school`.`end`         ='0000-00-00'
//
//                                    AND `member`.`permission`         <>'x'
//
//                                ORDER BY `member`.`uid` DESC
//                            ";
//                        }
//                    }elseif((in_array($sess_user_lv,array(3)))&&($users!=="")){
//                    //老師
//                        if($filter!==''){
//                            $sql="
//                                SELECT
//                                    *
//                                FROM(
//                                    SELECT
//                                        `member`.`uid`,
//                                        `member`.`name`,
//                                        `member`.`sex`,
//                                        `member`.`account`,
//                                        `member`.`password`,
//                                        `member`.`permission`,
//                                        `member`.`build_time`,
//
//                                        99 AS 'number'
//                                    FROM `member_school`
//                                        INNER JOIN `member` ON
//                                        `member_school`.`uid`=`member`.`uid`
//                                        INNER JOIN `teacher` ON
//                                        `member_school`.`uid`=`teacher`.`uid`
//                                    WHERE 1=1
//                                        -- 排除, 中大團隊
//                                        AND `member`.`uid`                <>1258
//
//                                        AND `member_school`.`school_code` = '{$sess_school_code}'
//                                        AND `member_school`.`start`       <=CURDATE()
//                                        AND `member_school`.`end`         ='0000-00-00'
//                                        AND `member`.`permission`         <>'x'
//                                        AND `member`.`uid`                ={$sess_user_id}
//
//                                        AND `teacher`.`start`             <=CURDATE()
//                                        AND `teacher`.`end`               >=CURDATE()
//
//                                        -- FILTER在此
//                                        {$filter}
//
//                                UNION
//
//                                    SELECT
//                                        `member`.`uid`,
//                                        `member`.`name`,
//                                        `member`.`sex`,
//                                        `member`.`account`,
//                                        `member`.`password`,
//                                        `member`.`permission`,
//                                        `member`.`build_time`,
//
//                                        `student`.`number` AS 'number'
//                                    FROM `member_school`
//                                        INNER JOIN `member` ON
//                                        `member_school`.`uid`=`member`.`uid`
//                                        INNER JOIN `student` ON
//                                        `member_school`.`uid`=`student`.`uid`
//                                    WHERE 1=1
//
//                                        -- 排除, 中大團隊
//                                        AND `member`.`uid`                <>1258
//
//                                        AND `member_school`.`school_code` = '{$sess_school_code}'
//                                        AND `member_school`.`start`       <=CURDATE()
//                                        AND `member_school`.`end`         ='0000-00-00'
//                                        AND `member`.`permission`         <>'x'
//                                        AND `member`.`uid`                IN ({$users})
//
//                                        AND `student`.`start`             <=CURDATE()
//                                        AND `student`.`end`               >=CURDATE()
//
//                                        -- FILTER在此
//                                        {$filter}
//
//                                ) AS `sqry`
//                                WHERE 1=1
//                                GROUP BY `sqry`.`uid`
//                                ORDER BY `sqry`.`number` ASC
//                            ";
//                        }else{
//                            $sql="
//                                SELECT
//                                    *
//                                FROM(
//                                    SELECT
//                                        `member`.`uid`,
//                                        `member`.`name`,
//                                        `member`.`sex`,
//                                        `member`.`account`,
//                                        `member`.`password`,
//                                        `member`.`permission`,
//                                        `member`.`build_time`,
//
//                                        99 AS 'number'
//                                    FROM `member_school`
//                                        INNER JOIN `member` ON
//                                        `member_school`.`uid`=`member`.`uid`
//                                        INNER JOIN `teacher` ON
//                                        `member_school`.`uid`=`teacher`.`uid`
//                                    WHERE 1=1
//                                        -- 排除, 中大團隊
//                                        AND `member`.`uid`                <>1258
//
//                                        AND `member_school`.`school_code` = '{$sess_school_code}'
//                                        AND `member_school`.`start`       <=CURDATE()
//                                        AND `member_school`.`end`         ='0000-00-00'
//                                        AND `member`.`permission`         <>'x'
//                                        AND `member`.`uid`                ={$sess_user_id}
//
//                                        AND `teacher`.`start`             <=CURDATE()
//                                        AND `teacher`.`end`               >=CURDATE()
//
//                                UNION
//
//                                    SELECT
//                                        `member`.`uid`,
//                                        `member`.`name`,
//                                        `member`.`sex`,
//                                        `member`.`account`,
//                                        `member`.`password`,
//                                        `member`.`permission`,
//                                        `member`.`build_time`,
//
//                                        `student`.`number` AS 'number'
//                                    FROM `member_school`
//                                        INNER JOIN `member` ON
//                                        `member_school`.`uid`=`member`.`uid`
//                                        INNER JOIN `student` ON
//                                        `member_school`.`uid`=`student`.`uid`
//                                    WHERE 1=1
//
//                                        -- 排除, 中大團隊
//                                        AND `member`.`uid`                <>1258
//
//                                        AND `member_school`.`school_code` = '{$sess_school_code}'
//                                        AND `member_school`.`start`       <=CURDATE()
//                                        AND `member_school`.`end`         ='0000-00-00'
//                                        AND `member`.`permission`         <>'x'
//                                        AND `member`.`uid`                IN ({$users})
//
//                                        AND `student`.`start`             <=CURDATE()
//                                        AND `student`.`end`               >=CURDATE()
//
//                                ) AS `sqry`
//                                WHERE 1=1
//                                GROUP BY `sqry`.`uid`
//                                ORDER BY `sqry`.`number` ASC
//                            ";
//                        }
//                    }elseif((in_array($sess_user_lv,array(3)))&&($users==="")){
//                    //老師
//                        if($filter!==''){
//                            $sql="
//                                SELECT
//                                    *
//                                FROM(
//                                    SELECT
//                                        `member`.`uid`,
//                                        `member`.`name`,
//                                        `member`.`sex`,
//                                        `member`.`account`,
//                                        `member`.`password`,
//                                        `member`.`permission`,
//                                        `member`.`build_time`,
//
//                                        99 AS 'number'
//                                    FROM `member_school`
//                                        INNER JOIN `member` ON
//                                        `member_school`.`uid`=`member`.`uid`
//                                        INNER JOIN `teacher` ON
//                                        `member_school`.`uid`=`teacher`.`uid`
//                                    WHERE 1=1
//                                        -- 排除, 中大團隊
//                                        AND `member`.`uid`                <>1258
//
//                                        AND `member_school`.`school_code` = '{$sess_school_code}'
//                                        AND `member_school`.`start`       <=CURDATE()
//                                        AND `member_school`.`end`         ='0000-00-00'
//                                        AND `member`.`permission`         <>'x'
//                                        AND `member`.`uid`                ={$sess_user_id}
//
//                                        AND `teacher`.`start`             <=CURDATE()
//                                        AND `teacher`.`end`               >=CURDATE()
//
//                                        -- FILTER在此
//                                        {$filter}
//
//                                ) AS `sqry`
//                                WHERE 1=1
//                                GROUP BY `sqry`.`uid`
//                                ORDER BY `sqry`.`number` ASC
//                            ";
//                        }else{
//                            $sql="
//                                SELECT
//                                    *
//                                FROM(
//                                    SELECT
//                                        `member`.`uid`,
//                                        `member`.`name`,
//                                        `member`.`sex`,
//                                        `member`.`account`,
//                                        `member`.`password`,
//                                        `member`.`permission`,
//                                        `member`.`build_time`,
//
//                                        99 AS 'number'
//                                    FROM `member_school`
//                                        INNER JOIN `member` ON
//                                        `member_school`.`uid`=`member`.`uid`
//                                        INNER JOIN `teacher` ON
//                                        `member_school`.`uid`=`teacher`.`uid`
//                                    WHERE 1=1
//                                        -- 排除, 中大團隊
//                                        AND `member`.`uid`                <>1258
//
//                                        AND `member_school`.`school_code` = '{$sess_school_code}'
//                                        AND `member_school`.`start`       <=CURDATE()
//                                        AND `member_school`.`end`         ='0000-00-00'
//                                        AND `member`.`permission`         <>'x'
//                                        AND `member`.`uid`                ={$sess_user_id}
//
//                                        AND `teacher`.`start`             <=CURDATE()
//                                        AND `teacher`.`end`               >=CURDATE()
//
//                                ) AS `sqry`
//                                WHERE 1=1
//                                GROUP BY `sqry`.`uid`
//                                ORDER BY `sqry`.`number` ASC
//                            ";
//                        }
//                    }
//                break;
//
//                case 'stuff':
//                    if($filter!==''){
//                        $sql="
//                            SELECT
//                                `member`.`uid`,
//                                `member`.`name`,
//                                `member`.`sex`,
//                                `member`.`account`,
//                                `member`.`password`,
//                                `member`.`permission`
//                            FROM `member_school`
//                                INNER JOIN `member` ON
//                                `member_school`.`uid`=`member`.`uid`
//                                INNER JOIN `personnel` ON
//                                `member`.`uid`=`personnel`.`uid`
//                            WHERE 1=1
//                                -- 排除, 中大團隊
//                                AND `member`.`uid`                <>1258
//
//                                AND `member_school`.`school_code` = '{$sess_school_code}'
//                                AND `member_school`.`start`       <=CURDATE()
//                                AND `member_school`.`end`         ='0000-00-00'
//
//                                AND `member`.`permission`         <>'x'
//
//                                AND `personnel`.`responsibilities` IN (1,2)
//
//                                -- FILTER在此
//                                {$filter}
//
//                            ORDER BY `member`.`uid` DESC
//                        ";
//                    }else{
//                        $sql="
//                            SELECT
//                                `member`.`uid`,
//                                `member`.`name`,
//                                `member`.`sex`,
//                                `member`.`account`,
//                                `member`.`password`,
//                                `member`.`permission`
//                            FROM `member_school`
//                                INNER JOIN `member` ON
//                                `member_school`.`uid`=`member`.`uid`
//                                INNER JOIN `personnel` ON
//                                `member`.`uid`=`personnel`.`uid`
//                            WHERE 1=1
//                                -- 排除, 中大團隊
//                                AND `member`.`uid`                <>1258
//
//                                AND `member_school`.`school_code` = '{$sess_school_code}'
//                                AND `member_school`.`start`       <=CURDATE()
//                                AND `member_school`.`end`         ='0000-00-00'
//
//                                AND `member`.`permission`         <>'x'
//
//                                AND `personnel`.`responsibilities` IN (1,2)
//
//                            ORDER BY `member`.`uid` DESC
//                        ";
//                    }
//                break;
//
//                case 'teacher':
//                    if($filter!==''){
//                        $sql="
//                            SELECT
//                                `member`.`uid`,
//                                `member`.`name`,
//                                `member`.`sex`,
//                                `member`.`account`,
//                                `member`.`password`,
//                                `member`.`permission`
//                            FROM `member_school`
//                                INNER JOIN `member` ON
//                                `member_school`.`uid`=`member`.`uid`
//                                INNER JOIN `personnel` ON
//                                `member`.`uid`=`personnel`.`uid`
//                            WHERE 1=1
//                                -- 排除, 中大團隊
//                                AND `member`.`uid`                <>1258
//
//                                AND `member_school`.`school_code` = '{$sess_school_code}'
//                                AND `member_school`.`start`       <=CURDATE()
//                                AND `member_school`.`end`         ='0000-00-00'
//
//                                AND `member`.`permission`         <>'x'
//
//                                AND `personnel`.`responsibilities` IN (3)
//
//                                -- FILTER在此
//                                {$filter}
//
//                            ORDER BY `member`.`uid` DESC
//                        ";
//                    }else{
//                        $sql="
//                            SELECT
//                                `member`.`uid`,
//                                `member`.`name`,
//                                `member`.`sex`,
//                                `member`.`account`,
//                                `member`.`password`,
//                                `member`.`permission`
//                            FROM `member_school`
//                                INNER JOIN `member` ON
//                                `member_school`.`uid`=`member`.`uid`
//                                INNER JOIN `personnel` ON
//                                `member`.`uid`=`personnel`.`uid`
//                            WHERE 1=1
//                                -- 排除, 中大團隊
//                                AND `member`.`uid`                <>1258
//
//                                AND `member_school`.`school_code` = '{$sess_school_code}'
//                                AND `member_school`.`start`       <=CURDATE()
//                                AND `member_school`.`end`         ='0000-00-00'
//
//                                AND `member`.`permission`         <>'x'
//
//                                AND `personnel`.`responsibilities` IN (3)
//
//                            ORDER BY `member`.`uid` DESC
//                        ";
//                    }
//                break;
//
//                default:
//                    die('this');
//                break;
//            }
//
//            $tmp_results=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
//            if(!empty($tmp_results)){
//                foreach($tmp_results as $tmp_result){
//                    $rs_uid=(int)$tmp_result['uid'];
//                    if(!array_key_exists($rs_uid,$db_results)){
//                        $db_results[$rs_uid]=$tmp_result;
//                    }
//                }
//            }
//
//            //資料總筆數
//            $db_results_cno=count($db_results);
//
//        //-----------------------------------------------
//        //SQL查詢(家長)
//        //-----------------------------------------------
//
//            if(trim($q_mode)==='all'){
//
//                //行政人員
//                if((in_array($sess_user_lv,array(1,2)))&&($sess_school_code==='gcp')){
//                    if($db_results_cno!==0){
//                        foreach($db_results as $rs_uid=>$db_result){
//                            $rs_uid=(int)$rs_uid;
//                            if($filter!==''){
//                                $sql="
//                                    SELECT
//                                        `member`.`name`,
//                                        `member`.`sex`,
//                                        `member`.`account`,
//                                        `member`.`password`,
//                                        `member`.`permission`,
//                                        `kinship`.`uid_main` AS `uid`
//                                    FROM `kinship`
//                                        INNER JOIN `member` ON
//                                        `kinship`.`uid_main`=`member`.`uid`
//                                    WHERE 1=1
//                                        AND `kinship`.`uid_sub`={$rs_uid}
//
//                                        -- FILTER在此
//                                        {$filter}
//
//                                    ORDER BY `member`.`build_time` DESC
//                                ";
//                            }else{
//                                $sql="
//                                    SELECT
//                                        `member`.`name`,
//                                        `member`.`sex`,
//                                        `member`.`account`,
//                                        `member`.`password`,
//                                        `member`.`permission`,
//                                        `kinship`.`uid_main` AS `uid`
//                                    FROM `kinship`
//                                        INNER JOIN `member` ON
//                                        `kinship`.`uid_main`=`member`.`uid`
//                                    WHERE 1=1
//
//                                        AND `kinship`.`uid_sub`={$rs_uid}
//
//                                    ORDER BY `member`.`build_time` DESC
//                                ";
//                            }
//                            $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
//                            if(!empty($arrys_result)){
//                                foreach($arrys_result as $arry_result){
//                                    $rs_main_uid=(int)$arry_result['uid'];
//                                    if(!array_key_exists($rs_main_uid,$db_results)){
//                                        $db_results[$rs_main_uid]=$arry_result;
//                                    }
//                                }
//                            }
//                        }
//                        //資料總筆數
//                        $db_results_cno=count($db_results);
//                    }
//                }
//
//                //推廣
//                if((in_array($sess_user_lv,array(99)))){
//                    if($db_results_cno!==0){
//                        foreach($db_results as $rs_uid=>$db_result){
//                            $rs_uid=(int)$rs_uid;
//                            if($filter!==''){
//                                $sql="
//                                    SELECT
//                                        `member`.`name`,
//                                        `member`.`sex`,
//                                        `member`.`account`,
//                                        `member`.`password`,
//                                        `member`.`permission`,
//                                        `kinship`.`uid_main` AS `uid`
//                                    FROM `kinship`
//                                        INNER JOIN `member` ON
//                                        `kinship`.`uid_main`=`member`.`uid`
//                                    WHERE 1=1
//                                        AND `kinship`.`uid_sub`={$rs_uid}
//
//                                        -- FILTER在此
//                                        {$filter}
//
//                                    ORDER BY `member`.`build_time` DESC
//                                ";
//                            }else{
//                                $sql="
//                                    SELECT
//                                        `member`.`name`,
//                                        `member`.`sex`,
//                                        `member`.`account`,
//                                        `member`.`password`,
//                                        `member`.`permission`,
//                                        `kinship`.`uid_main` AS `uid`
//                                    FROM `kinship`
//                                        INNER JOIN `member` ON
//                                        `kinship`.`uid_main`=`member`.`uid`
//                                    WHERE 1=1
//
//                                        AND `kinship`.`uid_sub`={$rs_uid}
//
//                                    ORDER BY `member`.`build_time` DESC
//                                ";
//                            }
//                            $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
//                            if(!empty($arrys_result)){
//                                foreach($arrys_result as $arry_result){
//                                    $rs_main_uid=(int)$arry_result['uid'];
//                                    if(!array_key_exists($rs_main_uid,$db_results)){
//                                        $db_results[$rs_main_uid]=$arry_result;
//                                    }
//                                }
//                            }
//                        }
//                        //資料總筆數
//                        $db_results_cno=count($db_results);
//                    }
//                }
//            }
//
//    //---------------------------------------------------
//    //分頁處理
//    //---------------------------------------------------
//
//        $numrow=$db_results_cno;    //資料總筆數
//        $psize =14;                 //單頁筆數,預設14筆
//        $pnos  =0;                  //分頁筆數
//        $pinx  =1;                  //目前分頁索引,預設1
//        $sinx  =0;                  //值域起始值
//        $einx  =0;                  //值域終止值
//
//        if(isset($_GET['psize'])){
//            $psize=(int)$_GET['psize'];
//            if($psize===0){
//                $psize=14;
//            }
//        }
//        if(isset($_GET['pinx'])){
//            $pinx=(int)$_GET['pinx'];
//            if($pinx===0){
//                $pinx=1;
//            }
//        }
//
//        $pnos  =ceil($numrow/$psize);
//        $pinx  =($pinx>$pnos)?$pnos:$pinx;
//
//        $sinx  =(($pinx-1)*$psize)+1;
//        $einx  =(($pinx)*$psize);
//        $einx  =($einx>$numrow)?$numrow:$einx;
//        //echo $numrow."<br/>";
//
//    //---------------------------------------------------
//    //資料,設定
//    //---------------------------------------------------
//
//        //網頁標題
//        $title="明日星球,學校人員專區";
//
//        if($numrow!==0){
//            $arrys_chunk =array_chunk($db_results,$psize);
//            $arrys_result=$arrys_chunk[$pinx-1];
//            page_hrs($title);
//        }else{
//            page_nrs($title);
//        }

        page_nrs($title);
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

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $q_mode;

        global $arrys_sess_login_info;

        global $conn_user;

        global $arry_personnel;
        global $arrys_permissions_info;
        global $arrys_result;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0;  //欄位個數
        $btn_nos=6;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        //SESSION
        if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
        if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
        if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
        if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
        if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
        if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
        if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
        if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
        if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
        if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
        if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
            $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
            foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
            }
        }

        $sess_user_lv=(int)$sess_user_lv;

        $status_html='';
        switch(trim($q_mode)){
            case 'kinship':
                $status_html="家長";
            break;

            case 'class_code':
                $status_html="學生";
            break;

            case 'all':
                $status_html='';
            break;

            case 'stuff':
                $status_html="學校人員";
            break;

            case 'teacher':
                $status_html="老師";
            break;

            default:
                die('q_mode fail!');
            break;
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
    <script type="text/javascript" src="../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../lib/js/table/code.js"></script>

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
            <table id="mod_data_tbl" border="0" width="99%" cellpadding="5" cellspacing="0" style="position:relative;margin-top:5px;" class="table_style1">
                <tr align="center" valign="middle" class="bg_gray1 fc_white0">
                    <td width="45px" align="left" valign="middle">
                        <?php
                            if((trim($q_mode)==='class_code')||(in_array($sess_user_lv,array(3)))){
                                echo '座號';
                            }else{
                                echo '編號';
                            }
                        ?>
                    </td>
                    <td width="55px">身分       </td>
                    <td width="110px">姓名      </td>
                    <td width="50px">姓別       </td>
                    <td width="110px">帳戶      </td>
                    <td width="110px">密碼      </td>
                    <td width="100px">學號      </td>
                    <td width="100px">借書證號  </td>
                    <td width="100px">目前班級  </td>
                    <td width="">下學期班級     </td>
                </tr>
                <?php foreach($arrys_result as $inx=>$arry_result) :?>
                <?php
                //---------------------------------------------------
                //接收欄位
                //---------------------------------------------------

                    extract($arry_result, EXTR_PREFIX_ALL, "rs");

                //---------------------------------------------------
                //處理欄位
                //---------------------------------------------------

                    //uid           主索引
                    $rs_uid=(int)$rs_uid;

                    //name          姓名
                    $rs_name=trim($rs_name);
                    if(mb_strlen($rs_name)>10){
                        $rs_name=mb_substr($rs_name,0,10)."..";
                    }

                    //account       帳戶
                    $rs_account=trim($rs_account);

                    //password      密碼
                    $rs_password=trim($rs_password);

                    //sex           姓別
                    $rs_sex=(int)$rs_sex;
                    $rs_sex_html="";
                    switch($rs_sex){
                        case 1:
                            $rs_sex_html=trim("男");
                        break;
                        case 2:
                            $rs_sex_html=trim("女");
                        break;
                    }

                //---------------------------------------------------
                //特殊處理
                //---------------------------------------------------

                    //permission    權限
                    $rs_permission=trim($rs_permission);
                    if(!empty($arrys_permissions_info)){

                        $arry_status  =$arrys_permissions_info[$rs_permission]['status'];

                    //-----------------------------------------------
                    //身分判定
                    //-----------------------------------------------

                        //比對身分
                        $arry_status_t=array('i_t','i_sa');
                        $arry_status_s=array('i_s');
                        $arry_status_f=array('i_f');

                        $arry_status_t_intersect=array_intersect($arry_status_t,$arry_status);
                        $arry_status_s_intersect=array_intersect($arry_status_s,$arry_status);
                        $arry_status_f_intersect=array_intersect($arry_status_f,$arry_status);
                        $arry_status_t_diff     =array_diff($arry_status_t,$arry_status_t_intersect);
                        $arry_status_s_diff     =array_diff($arry_status_s,$arry_status_s_intersect);
                        $arry_status_f_diff     =array_diff($arry_status_f,$arry_status_f_intersect);

                        //身分
                        $status_html="身分不明";
                        if(empty($arry_status_t_diff)){
                            if(isset($arry_personnel[$rs_uid])){
                                $rs_responsibilities=(int)$arry_personnel[$rs_uid];
                                switch($rs_responsibilities){
                                    case 1:
                                    //校長
                                        $status_html="學校人員";
                                    break;

                                    case 2:
                                    //主任
                                        $status_html="學校人員";
                                    break;

                                    case 3:
                                    //老師
                                        $status_html="老師";
                                    break;
                                }
                            }
                        }
                        if(empty($arry_status_s_diff)){
                        //學生
                            $status_html="學生";
                        }
                        if(empty($arry_status_f_diff)){
                        //家長
                            $status_html="家長";
                        }
                    }

                    //-----------------------------------------------
                    //學號查詢
                    //-----------------------------------------------

                        $student_no_html='無';
                        $sql="
                            SELECT
                                `student_no`
                            FROM `student_no`
                            WHERE 1=1
                                AND `uid`={$rs_uid}
                        ";
                        $arrys_student_no=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                        if(!empty($arrys_student_no)){
                            $rs_student_no=trim($arrys_student_no[0]['student_no']);
                            $student_no_html=$rs_student_no;
                        }

                    //-----------------------------------------------
                    //卡號查詢
                    //-----------------------------------------------

                        $card_number_html='無';
                        $sql="
                            SELECT
                                `card_number`
                            FROM `library_card`
                            WHERE 1=1
                                AND `uid`={$rs_uid}
                        ";
                        $arrys_card_number=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                        if(!empty($arrys_card_number)){
                            if($status_html==="學生"){
                                $card_number_html=trim($arrys_card_number[0]['card_number']);
                            }
                        }

                    //-----------------------------------------------
                    //座號查詢
                    //-----------------------------------------------

                        //number        座號
                        if((trim($q_mode)==='class_code')||(in_array($sess_user_lv,array(3)))){
                            $rs_number      =(int)$rs_number;
                            $rs_number_html ='';
                            if($status_html==="學生"){
                                $rs_number_html=$rs_number;
                            }else{
                                $rs_number_html ='無';
                            }
                        }

                    //-----------------------------------------------
                    //目前班級
                    //-----------------------------------------------

                        $sess_school_code=mysql_prep($sess_school_code);
                        $class_code_flag =false;

                        if($status_html==="學生"){
                            $sql="
                                SELECT
                                    `class`.`class_code`,
                                    `class`.`grade`,
                                    `class`.`classroom`,
                                    `class`.`class_category`,

                                    `semester`.`semester_year`,
                                    `semester`.`semester_term`
                                FROM `student`
                                    INNER JOIN `class` ON
                                    `student`.`class_code`=`class`.`class_code`
                                    INNER JOIN `semester` ON
                                    `class`.`semester_code`=`semester`.`semester_code`
                                WHERE 1=1
                                    AND `student`.`uid`          ={$rs_uid}

                                    AND `student`.`start`       <=CURDATE()
                                    AND `student`.`end`         >=CURDATE()

                                    AND `semester`.`start`      <=CURDATE()
                                    AND `semester`.`end`        >=CURDATE()

                                    AND `semester`.`school_code` ='{$sess_school_code}'
                                ORDER BY `student`.`start` DESC
                            ";
                        }else{
                            $sql="
                                SELECT
                                    `class`.`class_code`,
                                    `class`.`grade`,
                                    `class`.`classroom`,
                                    `class`.`class_category`,

                                    `semester`.`semester_year`,
                                    `semester`.`semester_term`
                                FROM `teacher`
                                    INNER JOIN `class` ON
                                    `teacher`.`class_code`=`class`.`class_code`
                                    INNER JOIN `semester` ON
                                    `class`.`semester_code`=`semester`.`semester_code`
                                WHERE 1=1
                                    AND `teacher`.`uid`          ={$rs_uid}

                                    AND `teacher`.`start`       <=CURDATE()
                                    AND `teacher`.`end`         >=CURDATE()

                                    AND `semester`.`start`      <=CURDATE()
                                    AND `semester`.`end`        >=CURDATE()

                                    AND `semester`.`school_code` ='{$sess_school_code}'
                                ORDER BY `teacher`.`start` DESC
                            ";
                        }
                        $arrys_class_code_info=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
                        if(!empty($arrys_class_code_info)){
                            $class_code_flag=true;
                            foreach($arrys_class_code_info as $arry_class_code_info){
                                $rs_class_code   =trim($arry_class_code_info['class_code']);
                                $rs_semester_year=(int)($arry_class_code_info['semester_year']);
                                $rs_semester_term=(int)($arry_class_code_info['semester_term']);
                            }
                        }

                    //-----------------------------------------------
                    //下學期班級
                    //-----------------------------------------------

                        $next_class_code_flag =false;

                        //-------------------------------------------
                        //+1學期
                        //-------------------------------------------

                            if((!$next_class_code_flag)&&($class_code_flag)){

                                $goal_semester_term=$rs_semester_term+1;

                                if($status_html==="學生"){
                                    $sql="
                                        SELECT
                                            `class`.`class_code`,
                                            `class`.`grade`,
                                            `class`.`classroom`,
                                            `class`.`class_category`,

                                            `semester`.`semester_year`,
                                            `semester`.`semester_term`
                                        FROM `student`
                                            INNER JOIN `class` ON
                                            `student`.`class_code`=`class`.`class_code`
                                            INNER JOIN `semester` ON
                                            `class`.`semester_code`=`semester`.`semester_code`
                                        WHERE 1=1
                                            AND `student`.`uid`           ={$rs_uid}
                                            AND `student`.`end`           >CURDATE()

                                            AND `semester`.`semester_year`={$rs_semester_year  }
                                            AND `semester`.`semester_term`={$goal_semester_term}

                                            AND `semester`.`school_code`  ='{$sess_school_code }'
                                        ORDER BY `student`.`start` DESC
                                    ";
                                }else{
                                    $sql="
                                        SELECT
                                            `class`.`class_code`,
                                            `class`.`grade`,
                                            `class`.`classroom`,
                                            `class`.`class_category`,

                                            `semester`.`semester_year`,
                                            `semester`.`semester_term`
                                        FROM `teacher`
                                            INNER JOIN `class` ON
                                            `teacher`.`class_code`=`class`.`class_code`
                                            INNER JOIN `semester` ON
                                            `class`.`semester_code`=`semester`.`semester_code`
                                        WHERE 1=1
                                            AND `teacher`.`uid`           ={$rs_uid}
                                            AND `teacher`.`end`           >CURDATE()

                                            AND `semester`.`semester_year`={$rs_semester_year  }
                                            AND `semester`.`semester_term`={$goal_semester_term}

                                            AND `semester`.`school_code`  ='{$sess_school_code }'
                                        ORDER BY `teacher`.`start` DESC
                                    ";
                                }
                                $arrys_next_class_code_info=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
                                if(!empty($arrys_next_class_code_info)){
                                    $next_class_code_flag =true;
                                }
                            }

                        //-------------------------------------------
                        //+1學年
                        //-------------------------------------------

                            if((!$next_class_code_flag)&&($class_code_flag)){

                                $goal_semester_year=$rs_semester_year+1;

                                if($status_html==="學生"){
                                    $sql="
                                        SELECT
                                            `class`.`class_code`,
                                            `class`.`grade`,
                                            `class`.`classroom`,
                                            `class`.`class_category`,

                                            `semester`.`semester_year`,
                                            `semester`.`semester_term`
                                        FROM `student`
                                            INNER JOIN `class` ON
                                            `student`.`class_code`=`class`.`class_code`
                                            INNER JOIN `semester` ON
                                            `class`.`semester_code`=`semester`.`semester_code`
                                        WHERE 1=1
                                            AND `student`.`uid`           ={$rs_uid}
                                            AND `student`.`end`           >CURDATE()

                                            AND `semester`.`semester_year`={$goal_semester_year }
                                            AND `semester`.`semester_term`=1

                                            AND `semester`.`school_code`  ='{$sess_school_code  }'
                                        ORDER BY `student`.`start` DESC
                                    ";
                                }else{
                                    $sql="
                                        SELECT
                                            `class`.`class_code`,
                                            `class`.`grade`,
                                            `class`.`classroom`,
                                            `class`.`class_category`,

                                            `semester`.`semester_year`,
                                            `semester`.`semester_term`
                                        FROM `teacher`
                                            INNER JOIN `class` ON
                                            `teacher`.`class_code`=`class`.`class_code`
                                            INNER JOIN `semester` ON
                                            `class`.`semester_code`=`semester`.`semester_code`
                                        WHERE 1=1
                                            AND `teacher`.`uid`           ={$rs_uid}
                                            AND `teacher`.`end`           >CURDATE()

                                            AND `semester`.`semester_year`={$goal_semester_year }
                                            AND `semester`.`semester_term`=1

                                            AND `semester`.`school_code`  ='{$sess_school_code  }'
                                        ORDER BY `teacher`.`start` DESC
                                    ";
                                }
                                $arrys_next_class_code_info=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
                                if(!empty($arrys_next_class_code_info)){
                                    $next_class_code_flag =true;
                                }
                            }
                ?>
                <tr>
                    <td align="left" valign="middle">
                        <?php
                            if((trim($q_mode)==='class_code')||(in_array($sess_user_lv,array(3)))){
                                echo $rs_number_html;
                            }else{
                                echo $rs_uid;
                            }
                        ?>
                    </td>
                    <td align="center" valign="middle">
                        <?php echo htmlspecialchars($status_html);?>
                    </td>
                    <td align="center" valign="middle">
                        <?php echo htmlspecialchars($rs_name);?>
                    </td>
                    <td align="center" valign="middle">
                        <?php echo htmlspecialchars($rs_sex_html);?>
                    </td>
                    <td align="center" valign="middle">
                        <?php echo htmlspecialchars($rs_account);?>
                    </td>
                    <td align="center" valign="middle">
                        <?php echo htmlspecialchars($rs_password);?>
                    </td>
                    <td align="center" valign="middle">
                        <?php echo htmlspecialchars($student_no_html);?>
                    </td>
                    <td align="center" valign="middle">
                        <?php echo htmlspecialchars($card_number_html);?>
                    </td>
                    <td align="center" valign="middle">
                        <?php if($class_code_flag):?>
                            <?php foreach($arrys_class_code_info as $arry_class_code_info):?>
                            <?php
                                $rs_grade         =(int)$arry_class_code_info['grade'];
                                $rs_classroom     =(int)$arry_class_code_info['classroom'];
                                $arrys_class_code_compile=get_class_code_info($conn_user,$sess_school_code,$rs_grade,$rs_classroom,$compile_flag=true,$arry_conn_user);
                                if(!empty($arrys_class_code_compile)){
                                    $rs_classroom_name=trim($arrys_class_code_compile[0]['classroom']);
                                    echo $rs_grade.'年'.htmlspecialchars($rs_classroom_name).'班'.'<br/>';
                                }else{
                                    echo $rs_grade.'年'.$rs_classroom.'班'.'<br/>';
                                }
                            ?>
                            <?php endforeach;?>
                        <?php else:?>
                            無
                        <?php endif;?>
                    </td>
                    <td align="center" valign="middle">
                        <?php if($next_class_code_flag):?>
                            <?php foreach($arrys_next_class_code_info as $arry_next_class_code_info):?>
                            <?php
                                $next_rs_grade         =(int)$arry_next_class_code_info['grade'];
                                $next_rs_classroom     =(int)$arry_next_class_code_info['classroom'];
                                $arrys_next_class_code_compile=get_class_code_info($conn_user,$sess_school_code,$next_rs_grade,$next_rs_classroom,$compile_flag=true,$arry_conn_user);
                                if(!empty($arrys_next_class_code_compile)){
                                    $next_rs_classroom_name=trim($arrys_next_class_code_compile[0]['classroom']);
                                    echo $next_rs_grade.'年'.htmlspecialchars($next_rs_classroom_name).'班'.'<br/>';
                                }else{
                                    echo $next_rs_grade.'年'.$next_rs_classroom.'班'.'<br/>';
                                }
                            ?>
                            <?php endforeach;?>
                        <?php else:?>
                            無
                        <?php endif;?>
                    </td>
                </tr>
                <?php endforeach ;?>
            </table>

            <table border="0" width="99%">
                <tr valign="middle">
                    <td align="left">
                        <!-- 分頁列 -->
                        <span id="page" style="position:relative;top:10px;"></span>

                        <span class="fc_brown0" style="position:relative;right:-10px;"
                        onclick="page_click(this,14)" onmouseover="this.style.cursor='pointer'">
                            一頁14筆
                        </span>

                        <span class="fc_brown0" style="position:relative;right:-30px;"
                        onclick="page_click(this,<?php echo $numrow;?>)" onmouseover="this.style.cursor='pointer'">
                            觀看全部
                        </span>

                        <span style="position:relative;top:0px;left:55px;" class="fc_brown0">
                            直接到
                            <input id="page_val" type="text" value="" size="10" maxlength="20"
                            class="form_text" style="width:30px">
                            頁
                            <input type="button" value="GO" class="ibtn_gr3020"
                            onclick="page_go();void(0);"
                            onmouseover="this.style.cursor='pointer'">
                        </span>
                    </td>
                    <td align="right">
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

    //---------------------------------------------------
    //參數
    //---------------------------------------------------

        var psize=<?php echo $psize;?>;
        var pinx =<?php echo $pinx;?>;

    //---------------------------------------------------
    //物件
    //---------------------------------------------------

        var ousers=document.getElementById('users');

    //---------------------------------------------------
    //函式
    //---------------------------------------------------

        function mult_class(type){
        //批次分班

            var url ='';
            var page=str_repeat('../',0)+'mult_class/classF.php';
            var arg ={
                'uid'  :trim(ousers.value),
                'type' :type,
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

            go(url,'self');
        }

        function mult_auth(type){
        //批次權限

            var url ='';
            var page=str_repeat('../',0)+'mult_auth/authF.php';
            var arg ={
                'uid'  :trim(ousers.value),
                'type' :type,
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

            go(url,'self');
        }

        function check_chkbox_true_cno(){
        //chkbox選取偵測

            //清空
            ousers.innerHTML='';

            var ouids=document.getElementsByName('uids');
            for(var i=0;i<ouids.length;i++){
                var ouid=ouids[i];
                var uid =parseInt(ouid.value);
                if(ouid.checked===true){
                    ousers.innerHTML+=uid+',';
                }
            }
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

        function check_chkbox(){
        //檢核chkbox

            var omult_auth =document.getElementById('mult_auth');
            var omult_class=document.getElementById('mult_class');

            var ouids=document.getElementsByName('uids');
            var cno  =0;
            for(var i=0;i<ouids.length;i++){
                var ouid=ouids[i];
                if(ouid.checked===true){
                    cno+=1;
                }
            }
            if(cno>=2){
                $(omult_auth).show();
                $(omult_class).show();
            }else{
                $(omult_auth).hide();
                $(omult_class).hide();
            }
        }

        function chk_all(obj){
        //全選

            var ouids=document.getElementsByName('uids');
            for(var i=0;i<ouids.length;i++){
                var ouid=ouids[i];
                if(obj.checked===true){
                    ouid.checked=true;
                }else{
                    ouid.checked=false;
                }
            }
        }

        function page_go(){
        //頁數指定跳轉

            var opage_val=document.getElementById('page_val');
            var page_val =parseInt(opage_val.value);
            var pnos     =<?php echo (int)$pnos;?>;

            if(isNaN(page_val)){
                alert('請輸入頁數 !');
                return false;
            }

            if((page_val<=0)||(page_val>pnos)){
                alert('頁數錯誤，請重新輸入 !');
                opage_val.value='';
                return false;
            }

            var url ='';
            var page=str_repeat('../',0)+'content.php';
            var arg ={
                'psize':psize,
                'pinx' :page_val
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

        function del(uid,type){
        //轉出

            if(trim(type)==='家長'){
                alert('請先轉出該家長的小孩之後! 家長也將同時一併轉出!');
                return false;
            }

            switch(trim(type)){
                case '學校人員':

                break;

                case '老師':

                break;

                case '學生':

                break;

                default:
                    alert('發生錯誤! 請洽詢明日星球團隊人員!');
                    return false;
                break;
            }

            var url ='';
            var page=str_repeat('../',0)+'del/delA.php';
            var arg ={
                'uid'  :uid,
                'type' :type,
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

            if(confirm('你確定要轉出此人嗎 ?轉出後無法回復!')){
                go(url,'self');
            }else{
                return false;
            }
        }

        function identity(uid,type){
        //身分

            var url ='';
            var page=str_repeat('../',0)+'identity/identityF.php';
            var arg ={
                'uid'  :uid,
                'type' :type,
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

            go(url,'self');
        }

        function pwd(uid,type){
        //密碼

            var url ='';
            var page=str_repeat('../',0)+'pwd/pwdF.php';
            var arg ={
                'uid'  :uid,
                'type' :type,
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

            go(url,'self');
        }

        function view(uid,type){
        //檢視

            var url ='';

            switch(trim(type)){
                case '學校人員':
                    var page=str_repeat('../',0)+'view/stuff/viewF.php';
                break;

                case '老師':
                    var page=str_repeat('../',0)+'view/teacher/viewF.php';
                break;

                case '學生':
                    var page=str_repeat('../',0)+'view/student/viewF.php';
                break;

                case '家長':
                    var page=str_repeat('../',0)+'view/kinship/viewF.php';
                break;

                default:
                    alert('發生錯誤! 請洽詢明日星球團隊人員!');
                    return false;
                break;
            }

            var arg ={
                'uid'  :uid,
                'type' :type,
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

            go(url,'self');
        }

        function edit(uid,type){
        //資料

            var url ='';

            switch(trim(type)){
                case '學校人員':
                    var page=str_repeat('../',0)+'edit/stuff/editF.php';
                break;

                case '老師':
                    var page=str_repeat('../',0)+'edit/teacher/editF.php';
                break;

                case '學生':
                    var page=str_repeat('../',0)+'edit/student/editF.php';
                break;

                case '家長':
                    var page=str_repeat('../',0)+'edit/kinship/editF.php';
                break;

                default:
                    alert('發生錯誤! 請洽詢明日星球團隊人員!');
                    return false;
                break;
            }

            var arg ={
                'uid'  :uid,
                'type' :type,
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

            go(url,'self');
        }

        function auth(uid,type){
        //權限
            var url ='';
            var page=str_repeat('../',0)+'auth/authF.php';
            var arg ={
                'uid'  :uid,
                'type' :type,
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

            go(url,'self');
        }

        function _class(uid,type){
        //分班
            var url ='';
            var page=str_repeat('../',0)+'class/classF.php';
            var arg ={
                'uid'  :uid,
                'type' :type,
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

            go(url,'self');
        }

        function transfer(){
        //轉入帳戶
            var url ='';
            var page=str_repeat('../',0)+'transfer/type/transferF.php';
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

            go(url,'self');
        }

        function add(){
        //新增帳戶
            var url ='';
            var page=str_repeat('../',0)+'add/type/addF.php';
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

            go(url,'self');
        }

        function _mouseover(obj){
            obj.style.cursor='pointer';
        }

    //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        window.onload=function(){

            //套表格列奇偶色
            table_hover(tbl_id='mod_data_tbl',c_odd='#fff',c_even='#fff',c_on='#e6faff');

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
        }
</script>

</Body>
</Html>

<?php
//-------------------------------------------------------
//page_hrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_user=NULL;
?>
<?php };?>


<?php function page_nrs($title="") {?>
<?php
//-------------------------------------------------------
//page_nrs 區塊 -- 開始
//-------------------------------------------------------

//    //---------------------------------------------------
//    //外部變數
//    //---------------------------------------------------
//
//        //config.php
//        global $PAGE_SELF;
//        global $FOLDER_SELF;
//        global $nl;
//        global $tab;
//        global $fso_enc;
//        global $page_enc;
//        global $arry_conn_user;
//
//        //local
//        global $numrow;
//        global $psize;
//        global $pnos;
//        global $pinx;
//        global $sinx;
//        global $einx;
//
//        global $conn_user;
//
//        global $arrys_sess_login_info;
//
//    //---------------------------------------------------
//    //內部變數
//    //---------------------------------------------------
//
//        $fld_nos=0;  //欄位個數
//        $btn_nos=0;  //功能按鈕個數
//
//    //---------------------------------------------------
//    //額外處理
//    //---------------------------------------------------
//
//        //SESSION
//        if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
//        if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
//        if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
//        if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
//        if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
//        if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
//        if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
//        if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
//        if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
//        if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
//        if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
//            $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
//            foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
//                $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
//            }
//        }
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php //echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php //echo Content_Language;?>">
    <?php //echo meta_keywords($key='mssr');?>
    <?php //echo meta_description($key='mssr');?>
    <?php //echo bing_analysis($allow=true);?>
    <?php //echo robots($allow=true);?>

    <!-- 通用 -->
    <script type="text/javascript" src="../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../lib/js/string/code.js"></script>

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
        <td width="80%" height="300px" align="center" valign="top">
            <!-- 內容 -->
            <table align="center" border="0" width="90%" cellpadding="5" cellspacing="0" style="position:relative;top:30px;" class="table_style2">
                <tr align="center" valign="middle" class="bg_gray1">
                    <td>&nbsp;</td>
                </tr>
                <tr align="center">
                    <td height="300px" align="center" valign="middle" class="font-family1 fsize_16">
                        <img src="../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
                        目前系統無資料，或查無資料!<br/><br/>
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

//    //---------------------------------------------------
//    //參數
//    //---------------------------------------------------
//
//        var psize=<?php echo $psize;?>;
//        var pinx =<?php echo $pinx;?>;
//
//    //---------------------------------------------------
//    //函式
//    //---------------------------------------------------
//
//        function add(){
//        //新增帳戶
//            var url ='';
//            var page=str_repeat('../',0)+'add/type/addF.php';
//            var arg ={
//                'psize':psize,
//                'pinx' :pinx
//            };
//            var _arg=[];
//            for(var key in arg){
//                _arg.push(key+"="+encodeURI(arg[key]));
//            }
//            arg=_arg.join("&");
//
//            if(arg.length!=0){
//                url+=page+"?"+arg;
//            }else{
//                url+=page;
//            }
//
//            go(url,'self');
//        }
//
//        function _mouseover(obj){
//            obj.style.cursor='pointer';
//        }
//
//        window.onload=function(){
//
//        }

</script>

</Body>
</Html>

<?php
//-------------------------------------------------------
//page_nrs 區塊 -- 結束
//-------------------------------------------------------
    //$conn_user=NULL;
?>
<?php };?>