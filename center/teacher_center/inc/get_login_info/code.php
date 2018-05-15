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
                $err='GET_LOGIN_INFO:UID IS INVAILD';
                die($err);
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

            //現在日期
            $_date=date("Y-m-d");

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

            if((isset($_SESSION['tc']))||(!empty($_SESSION['tc']))){
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
                $is_super=false;
            }

            //-------------------------------------------
            //判定是否為管理者
            //-------------------------------------------

                if(!$login_flag){
                    //$sql="
                    //    SELECT
                    //        `member`.`uid`,
                    //        `member`.`name`,
                    //        `member`.`account`,
                    //        `member`.`permission`
                    //    FROM `member`
                    //    WHERE 1=1
                    //        AND `member`.`uid`={$_uid}
                    //        AND `member`.`permission`='super'
                    //";
                    ////echo $sql.'<br/>';
                    //$arrys_result=db_result($conn_type='pdo',$conn,$sql,$arry_limit=array(0,1),$arry_conn);
                    //if(!empty($arrys_result)){
                    //    $is_super=true;
                    //    if($is_super){
                    //        //登入資訊, 欄位值
                    //        $arrys_login_info=$arrys_result;
                    //    }
                    //}
                    $sql="
                        SELECT
                            `member`.`uid`,
                            `member`.`name`,
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
                                $is_super=true;
                            }
                        }
                        if($is_super){
                            //登入資訊, 欄位值
                            $arrys_login_info[0]=$arrys_result[0];
                            $arrys_login_info[0]['permission']=trim('super');
                        }
                    }
                }

            //-------------------------------------------
            //撈取基本資訊
            //-------------------------------------------
            //Array
            //(
            //    [3] => Array
            //        (
            //            [uid] => 5029
            //            [name] => 老師A
            //            [account] => t01
            //            [permission] => test_t_mssr
            //            [school_code] => test
            //            [responsibilities] => 3
            //            [school_name] => 明日學校
            //        )
            //
            //)

                if(!$login_flag){
                    if(!$is_super){
                        $sql="
                            SELECT
                                `member`.`uid`,
                                `member`.`name`,
                                `member`.`account`,
                                `member`.`permission`,

                                `personnel`.`school_code`,
                                `personnel`.`responsibilities`,

                                `school`.`school_name`
                            FROM `member`
                                INNER JOIN `personnel` ON
                                `member`.`uid`=`personnel`.`uid`

                                INNER JOIN `school` ON
                                `personnel`.`school_code`=`school`.`school_code`
                            WHERE 1=1
                                AND `member`.`uid`={$_uid}
                                AND `personnel`.`uid`={$_uid}
                                AND `personnel`.`start` <= '{$_date}'
                                AND `personnel`.`end`   >= '{$_date}'
                        ";
                        //echo $sql.'<br/>';
                        $arrys_result=db_result($conn_type='pdo',$conn,$sql,$arry_limit=array(),$arry_conn);

                        if(empty($arrys_result)){
                        //比對失敗
                            //$err='GET_LOGIN_INFO:err=db';
                            //die($err);
                        }else{
                        //比對成功
                            $_user_permission=trim($arrys_result[0]['permission']);

                            //該帳號被停用
                            if($_user_permission==='x'){
                                $err='GET_LOGIN_INFO:err=user_state';
                                die($err);
                            }

                            //登入資訊, 欄位值
                            foreach($arrys_result as $inx=>$arry_result){
                                $responsibilities=(int)$arry_result['responsibilities'];
                                foreach($arry_result as $field_name=>$field_value){
                                    $arrys_login_info[$responsibilities][$field_name]=$field_value;
                                }
                            }
                        }
                    }
                }

            //-------------------------------------------
            //撈取學校、學期資訊
            //-------------------------------------------

                if(!$login_flag){
                    if(!$is_super){
                        $sql="
                            SELECT
                                `teacher`.`class_code`,

                                `class`.`class_category`,
                                `class`.`grade`,
                                `class`.`classroom`,
                                `class`.`semester_code`
                            FROM `teacher`
                                INNER JOIN `class` ON
                                `teacher`.`class_code`=`class`.`class_code`
                            WHERE 1=1
                                AND `teacher`.`uid`={$_uid}
                                AND `teacher`.`start` <= '{$_date}'
                                AND `teacher`.`end`   >= '{$_date}'
                        ";
                        //echo $sql.'<br/>';
                        $arrys_result=db_result($conn_type='pdo',$conn,$sql,$arry_limit=array(),$arry_conn);

                        //班級數目
                        $_class_code_cno=count($arrys_result);

                        switch($_class_code_cno){

                            case 0:
                            //比對失敗
                                //$err='GET_LOGIN_INFO:err=db';
                                //die($err);
                            break;

                            default:
                                //登入資訊, 欄位值
                                foreach($arrys_login_info as $inx=>$arry_login_info){
                                    //回填班級
                                    $arrys_login_info[$inx]['arrys_class_code']=$arrys_result;
                                }
                            break;
                        }
                    }
                }

        //-----------------------------------------------
        //SESSION
        //-----------------------------------------------
        //$_SESSION['config']['user_tbl'] =$arry_result;  //用戶資料表欄位

            if(!$login_flag){
                if(!$is_super){

                    //初始化, 用戶資訊
                    unset($_SESSION['config']['user_type']);                //用戶類型(a,t,s,am,dt ...)
                    unset($_SESSION['config']['user_lv']);                  //用戶層級(1,3,5,7,13 ...)

                    //回填, 用戶資訊
                    foreach($arrys_login_info as $inx=>$arry_login_info){
                        $responsibilities=(int)$arry_login_info['responsibilities'];
                        switch($responsibilities){
                            case 1:
                            //校長身分
                                $_SESSION['config']['user_type'][]='dt';    //用戶類型(a,t,s,am,dt ...)
                                $_SESSION['config']['user_lv'][]=13;        //用戶層級(1,3,5,7,13 ...)
                            break;

                            case 2:
                            //主任身分
                                $_SESSION['config']['user_type'][]='dt';    //用戶類型(a,t,s,am,dt ...)
                                $_SESSION['config']['user_lv'][]=13;        //用戶層級(1,3,5,7,13 ...)
                            break;

                            case 3:
                            //老師身分
                                $_SESSION['config']['user_type'][]='t';     //用戶類型(a,t,s,am,dt ...)
                                $_SESSION['config']['user_lv'][]=3;         //用戶層級(1,3,5,7,13 ...)
                            break;

                            default:
                            //比對失敗
                                $err='GET_LOGIN_INFO:err=chk';
                                die($err);
                            break;
                        }
                    }
                }

                //回填, 登入資訊
                $_SESSION['tc']=$arrys_login_info;

                //區域資訊
                array_push($_SESSION['config']['user_area'],"teacher_center");

                //釋放資源
                $conn=NULL;
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $_SESSION['tc'];
    }
?>