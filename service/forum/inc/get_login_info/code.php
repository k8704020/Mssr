<?php
//-------------------------------------------------------
//函式: get_login_info()
//用途: 提取登入資訊
//-------------------------------------------------------

    function get_login_info($db_type='mysql',$arry_conn,$APP_ROOT){
    //---------------------------------------------------
    //函式: get_login_info()
    //用途: 提取登入資訊
    //---------------------------------------------------
    //$db_type      mysql (預設)
    //$arry_conn    資料庫連線資訊陣列
    //$APP_ROOT     網站根目錄
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            //接收參數
            global $_COOKIE;
            global $_SESSION;

            $_cookie_info=0;
            if(isset($_COOKIE['uid'])&&(trim($_COOKIE['uid'])!=='')){
                $_cookie_info=(int)$_COOKIE['uid'];
            }
            $_session_info=0;
            if(isset($_SESSION['uid'])&&(trim($_SESSION['uid'])!=='')){
                $_session_info=(int)$_SESSION['uid'];
            }

            if(($_cookie_info===0)&&($_session_info===0)){
                //$msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                //$jscript_back="
                //    <script>
                //        alert('{$msg}');
                //        location.href='/ac/index.php';
                //    </script>
                //";
                //die($jscript_back);
                return array();
            }

            if(!isset($db_type)||(trim($db_type)==='')){
                $db_type='mysql';
            }

            if(!isset($arry_conn)||(empty($arry_conn))){
                $err='GET_LOGIN_INFO:NO ARRY_CONN';
                die($err);
            }

            if(!isset($APP_ROOT)||trim($APP_ROOT)===''){
                $err='GET_LOGIN_INFO:NO APP_ROOT';
                die($err);
            }

        //-----------------------------------------------
        //外掛函式檔
        //-----------------------------------------------

            if((!function_exists("mysql_prep"))&&(!function_exists("db_result"))){
                if(false===@include_once($APP_ROOT.'lib/php/db/code.php')){
                    return false;
                }
            }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            //cookie
            $_cookie_info=(int)$_cookie_info;

            //session
            $_session_info=(int)$_session_info;

            //登入指標
            $login_flag=false;

            //登入資訊
            $arrys_login_info=array();

            //初始化, 等級
            $lv=0;

            if($_cookie_info!==0){
               $lv=$lv+1;
            }
            if($_session_info!==0){
               $lv=$lv+3;
            }

            switch($lv){

                case 0:
                //無可用資訊
                    $arrys_login_info=array();
                    $err='GET_LOGIN_INFO:UID IS INVAILD';
                    die($err);
                break;

                case 1:
                //COOKIE為主
                    $arrys_login_info['uid']=$_cookie_info;
                break;

                case 3:
                //SESSION為主
                    $arrys_login_info['uid']=$_session_info;
                break;

                case 4:
                //SESSION為主
                    $arrys_login_info['uid']=$_session_info;
                break;
            }

        //-----------------------------------------------
        //檢核登入狀況
        //-----------------------------------------------

            if((isset($_SESSION['mssr_forum']))||(!empty($_SESSION['mssr_forum']))){
                $login_flag=true;
            }

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //-------------------------------------------
            //通用
            //-------------------------------------------

                if(!$login_flag){

                    //資料庫資訊
                    $db_host  =$arry_conn['db_host'];
                    $db_user  =$arry_conn['db_user'];
                    $db_pass  =$arry_conn['db_pass'];
                    $db_name  =$arry_conn['db_name'];
                    $db_encode=$arry_conn['db_encode'];

                    //建立連線
                    $conn_info="{$db_type}".":host={$db_host}".";dbname={$db_name}";
                    $options = array(
                        PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,           //設置錯誤提示，只獲取代碼
                        PDO::ATTR_CASE,              PDO::CASE_NATURAL,             //列名按照原始的方式
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode}"    //設置語系
                    );

                    try{
                        $conn=@new PDO($conn_info, $db_user, $db_pass,$options);
                    }catch(PDOException $e){
                        $err ='DB_RESULT:CONNECT FAIL';
                        die($err);
                    }
                }

        //-----------------------------------------------
        //查找, 使用者資訊
        //-----------------------------------------------

            if(!$login_flag){
                $_uid=mysql_prep((int)$arrys_login_info['uid']);

                //重置, 登入資訊
                $arrys_login_info=array();

                //初始話, 是否為管理者
                $admin_flag=false;
            }

            //-------------------------------------------
            //判定是否為管理者
            //-------------------------------------------

                if(!$login_flag){
                    $sql="
                        SELECT
                            `member`.`uid`,
                            `member`.`name`,
                            `member`.`sex`,
                            `member`.`account`,
                            `member`.`permission`,

                            `permissions`.`status`
                        FROM `member`
                            INNER JOIN `permissions` ON
                            `member`.`permission`=`permissions`.`permission`
                        WHERE 1=1
                            AND `member`.`uid`={$_uid}
                    ";
                    //echo $sql.'<br/>';
                    $arrys_result=db_result($conn_type='pdo',$conn,$sql,$arry_limit=array(),$arry_conn);

                    if(!empty($arrys_result)){

                        foreach($arrys_result as $arry_result){

                            $rs_status=trim($arry_result['status']);

                            if(in_array($rs_status,array(trim('i_a')))){
                                $admin_flag=true;
                            }
                        }

                        if($admin_flag){
                            //登入資訊, 欄位值
                            $arrys_login_info[0]=$arrys_result[0];

                            //回填用戶等級
                            $arrys_login_info[0][trim('user_lv')]=99;

                            //清除功能
                            unset($arrys_login_info[0][trim('status')]);
                        }
                    }
                }

            //-------------------------------------------
            //撈取基本資訊(校長，主任，老師，學生)
            //-------------------------------------------

                if(!$login_flag&&!$admin_flag){

                    //權限,與判斷
                    $status=trim('u_mssr_forum');

                    $sql="
                            SELECT
                                `member`.`uid`,
                                `member`.`name`,
                                `member`.`sex`,
                                `member`.`account`,
                                `member`.`permission`,

                                `personnel`.`school_code`,
                                `personnel`.`responsibilities`,

                                `school`.`school_name`,
                                `school`.`country_code`
                            FROM `member`
                                INNER JOIN `personnel` ON
                                `member`.`uid`=`personnel`.`uid`

                                INNER JOIN `school` ON
                                `personnel`.`school_code`=`school`.`school_code`

                                INNER JOIN `permissions` ON
                                `member`.`permission`=`permissions`.`permission`
                            WHERE 1=1
                                AND `member`.`uid`        =   {$_uid }
                                AND `member`.`permission` <> 'x'

                                AND `personnel`.`uid`     =   {$_uid }
                                AND CURDATE() BETWEEN `personnel`.`start` AND `personnel`.`end`

                                AND `permissions`.`status`='{$status}'

                                -- 限定校長，主任，老師 - 學校人員
                                AND `personnel`.`responsibilities` IN (1,2,3)
                            #LIMIT 1

                        UNION

                            SELECT
                                `member`.`uid`,
                                `member`.`name`,
                                `member`.`sex`,
                                `member`.`account`,
                                `member`.`permission`,

                                `school`.`school_code`,
                                4 AS `responsibilities`,

                                `school`.`school_name`,
                                `school`.`country_code`
                            FROM `member`
                                LEFT JOIN `student` ON
                                `member`.`uid`=`student`.`uid`

                                LEFT JOIN `class` ON
                                `student`.`class_code`=`class`.`class_code`

                                LEFT JOIN `semester` ON
                                `class`.`semester_code`=`semester`.`semester_code`

                                LEFT JOIN `school` ON
                                `semester`.`school_code`=`school`.`school_code`

                                LEFT JOIN `permissions` ON
                                `member`.`permission`=`permissions`.`permission`
                            WHERE 1=1
                                AND `member`.`uid`        =   {$_uid }
                                AND `member`.`permission` <> 'x'

                                AND `permissions`.`status`='{$status}'
                            #ORDER BY `student`.`end` DESC
                            #LIMIT 1
                    ";
                    //echo $sql.'<br/>';
                    $arrys_result=db_result($conn_type='pdo',$conn,$sql,$arry_limit=array(),$arry_conn);

                    if(!empty($arrys_result)){
                    //比對成功

                        //登入資訊, 欄位值
                        foreach($arrys_result as $inx=>$arry_result){

                            //參數
                            $responsibilities=(int)$arry_result[trim('responsibilities')];

                            foreach($arry_result as $field_name=>$field_value){

                                $arrys_login_info[$inx][$field_name]=$field_value;

                                //回填用戶等級
                                $arrys_login_info[$inx][trim('user_lv')]=$responsibilities;
                            }
                        }
                    }
                }

            //-------------------------------------------
            //撈取學期班級資訊
            //-------------------------------------------

                if(!$login_flag&&!$admin_flag){
                    $sql="
                            SELECT
                                `teacher`.`class_code`,

                                `class`.`class_category`,
                                `class`.`grade`,
                                `class`.`classroom`,
                                `class`.`semester_code`,

                                `semester`.`semester_year`,
                                `semester`.`semester_term`,
                                `semester`.`start`,
                                `semester`.`end`
                            FROM `teacher`
                                INNER JOIN `class` ON
                                `teacher`.`class_code`=`class`.`class_code`

                                INNER JOIN `semester` ON
                                `class`.`semester_code`=`semester`.`semester_code`
                            WHERE 1=1
                                AND `teacher`.`uid`    =  {$_uid }
                                AND CURDATE() BETWEEN `teacher`.`start` AND `teacher`.`end`

                        UNION

                            SELECT
                                `student`.`class_code`,

                                `class`.`class_category`,
                                `class`.`grade`,
                                `class`.`classroom`,
                                `class`.`semester_code`,

                                `semester`.`semester_year`,
                                `semester`.`semester_term`,
                                `semester`.`start`,
                                `semester`.`end`
                            FROM `student`
                                INNER JOIN `class` ON
                                `student`.`class_code`=`class`.`class_code`

                                INNER JOIN `semester` ON
                                `class`.`semester_code`=`semester`.`semester_code`
                            WHERE 1=1
                                AND `student`.`uid`    =  {$_uid }
                                AND CURDATE() BETWEEN `student`.`start` AND `student`.`end`
                    ";
                    //echo $sql.'<br/>';
                    $arrys_result=db_result($conn_type='pdo',$conn,$sql,$arry_limit=array(),$arry_conn);

                    if(!empty($arrys_result)){

                        foreach($arrys_login_info as $inx=>$arry_login_info){

                            //回填初始班級
                            $arrys_login_info[$inx][trim('arry_class_info')] =$arrys_result[0];

                            //回填複數班級
                            $arrys_login_info[$inx][trim('arrys_class_info')]=$arrys_result;
                        }
                    }
                }

        //-----------------------------------------------
        //SESSION處理
        //-----------------------------------------------

            if(empty($arrys_login_info)){
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        location.href='/ac/index.php';
                    </script>
                ";
                die($jscript_back);
            }else{
                if(!$login_flag){

                    //回填, 登入資訊
                    $_SESSION['mssr_forum']=$arrys_login_info;

                    //釋放資源
                    $conn=NULL;
                }
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $_SESSION['mssr_forum'];
    }
?>