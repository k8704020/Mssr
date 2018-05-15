<?php
//-------------------------------------------------------
//函式: get_class_user_read_group_detail_info()
//用途: 取得班級人員閱讀本數
//日期: 2015年07月30日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    ////---------------------------------------------------
    ////設置測試資料
    ////---------------------------------------------------
    //
    //    $arry_class_code =array("gcp_2014_2_3_4","gcp_2014_2_3_5");
    //    $start_time      =trim('2014-03-01 00:00:00');
    //    $end_time        =trim('2014-03-10 00:00:00');
    //    echo "<Pre>";
    //    print_r(get_class_user_read_group_detail_info($arry_class_code,$start_time,$end_time));
    //    echo "</Pre>";

    //---------------------------------------------------
    //函式
    //---------------------------------------------------

        function get_class_user_read_group_detail_info($arry_class_code,$start_time,$end_time){
        //-----------------------------------------------
        //函式: get_class_user_read_group_detail_info()
        //用途: 取得班級人員閱讀本數
        //-----------------------------------------------
        //$arry_class_code  班級代號
        //$start_time       起始時間
        //$end_time         結束時間
        //-----------------------------------------------

            //-------------------------------------------
            //自訂函式
            //-------------------------------------------

                if(!function_exists("db_result")){
                    function db_result($conn_type='mysql',$conn='',$sql,$arry_limit=array(),$arry_conn){
                    //---------------------------------------
                    //取得資料筆數
                    //---------------------------------------
                    //$conn_type    資料庫連結類型      mysql | pdo     預設 mysql
                    //$conn         資料庫連結物件
                    //$sql          SQL查詢字串
                    //$arry_limit   資料筆數限制陣列(等同LIMIT inx,size)
                    //$arry_conn    資料庫資訊陣列
                    //---------------------------------------

                        //檢核參數
                        if(!in_array(trim($conn_type),array('','mysql','pdo'))){
                            $err='DB_RESULT:CONN_TYPE INVALID';
                            die($err);
                        }else{
                            if($conn_type===''){
                                $conn_type='mysql';
                            }
                        }
                        if(!$sql){
                            $err='DB_RESULT:NO SQL';
                            die($err);
                        }
                        if((!$arry_conn)||(empty($arry_conn))){
                            $err='DB_RESULT:NO ARRY_CONN';
                            die($err);
                        }

                        //資料庫資訊
                        $db_host  =$arry_conn['db_host'];
                        $db_user  =$arry_conn['db_user'];
                        $db_pass  =$arry_conn['db_pass'];
                        $db_name  =$arry_conn['db_name'];
                        $db_encode=$arry_conn['db_encode'];

                        switch($conn_type){
                        //資料庫連結類型

                            case 'pdo':
                            //連結類型為pdo

                                //連結物件判斷
                                $has_conn=false;

                                if(!$conn){
                                    $has_conn=true;

                                    $conn_info='mysql'.":host={$db_host}".";dbname={$db_name}";
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
                                }else{
                                    $has_conn=false;
                                }

                                //SQL敘述
                                if(!empty($arry_limit)){
                                   $a=$arry_limit[0];
                                   $b=$arry_limit[1];
                                   $sql.=" LIMIT {$a},{$b}";
                                }
                                //echo $sql;

                                //資料庫
                                $err='DB_RESULT:QUERY FAIL';
                                $result=$conn->query($sql) or
                                die($err);

                                //建立資料集陣列
                                $arry_result=array();

                                if(($result->rowCount())!==0){
                                    while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                                        $arry_result[]=$arry_row;
                                    }
                                }

                                //傳回資料集陣列
                                return $arry_result;

                                //釋放資源
                                //mysql_free_result($result);
                                if($has_conn==true){
                                    $conn=NULL;
                                }

                            break;

                            default:
                            //例外處理

                                $err='DB_RESULT:CONN_TYPE INVALID';
                                die($err);

                            break;
                        }
                    }
                }

            //-------------------------------------------
            //參數處理
            //-------------------------------------------
            //$arry_class_code  班級代號
            //$start_time       起始時間
            //$end_time         結束時間

                //檢核參數
                if(!isset($arry_class_code)||(!is_array($arry_class_code))||(empty($arry_class_code))){
                    $err='GET_CLASS_USER_READ_GROUP_DETAIL_INFO:NO ARRY_CLASS_CODE';
                    die($err);
                }else{
                    $arry_class_code=array_map("trim",$arry_class_code);
                    $arry_class_code=array_map("addslashes",$arry_class_code);
                }

                if(!isset($start_time)||(trim($start_time)==='')){
                    $err='GET_CLASS_USER_READ_GROUP_DETAIL_INFO:NO START_TIME';
                    die($err);
                }else{
                    $start_time=addslashes(trim($start_time));
                }

                if(!isset($end_time)||(trim($end_time)==='')){
                    $err='GET_CLASS_USER_READ_GROUP_DETAIL_INFO:NO END_TIME';
                    die($err);
                }else{
                    $end_time=addslashes(trim($end_time));
                }


                //資料庫資訊
                $arry_conn_user=array(
                    'db_host'   =>'140.115.16.104',
                    'db_name'   =>'user',
                    'db_user'   =>'mssr',
                    'db_pass'   =>'UeR1up0u',
                    'db_encode' =>'UTF8'
                );
                $db_host_user  =$arry_conn_user['db_host'];
                $db_user_user  =$arry_conn_user['db_user'];
                $db_pass_user  =$arry_conn_user['db_pass'];
                $db_name_user  =$arry_conn_user['db_name'];
                $db_encode_user=$arry_conn_user['db_encode'];


                //連結物件判斷
                $conn_info='mysql'.":host={$db_host_user}".";dbname={$db_name_user}";
                $options = array(
                    PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,               //設置錯誤提示，只獲取代碼
                    PDO::ATTR_CASE,              PDO::CASE_NATURAL,                 //列名按照原始的方式
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode_user}"   //設置語系
                );

                try{
                    $conn_user=@new PDO($conn_info, $db_user_user, $db_pass_user,$options);
                }catch(PDOException $e){
                    $err ='GET_CLASS_USER_READ_GROUP_DETAIL_INFO:CONNECT FAIL';
                    die($err);
                }

                $arry_conn_mssr=array(
                    'db_host'   =>'140.115.16.104',
                    'db_name'   =>'mssr',
                    'db_user'   =>'mssr',
                    'db_pass'   =>'UeR1up0u',
                    'db_encode' =>'UTF8'
                );
                $db_host_mssr  =$arry_conn_mssr['db_host'];
                $db_user_mssr  =$arry_conn_mssr['db_user'];
                $db_pass_mssr  =$arry_conn_mssr['db_pass'];
                $db_name_mssr  =$arry_conn_mssr['db_name'];
                $db_encode_mssr=$arry_conn_mssr['db_encode'];


                //連結物件判斷
                $conn_info='mysql'.":host={$db_host_mssr}".";dbname={$db_name_mssr}";
                $options = array(
                    PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,               //設置錯誤提示，只獲取代碼
                    PDO::ATTR_CASE,              PDO::CASE_NATURAL,                 //列名按照原始的方式
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode_mssr}"   //設置語系
                );

                try{
                    $conn_mssr=@new PDO($conn_info, $db_user_mssr, $db_pass_mssr,$options);
                }catch(PDOException $e){
                    $err ='GET_CLASS_USER_READ_GROUP_DETAIL_INFO:CONNECT FAIL';
                    die($err);
                }

            //-------------------------------------------
            //初始化
            //-------------------------------------------
            //$arry_class_code  班級代號
            //$start_time       起始時間
            //$end_time         結束時間

                $arry_class_code=array_map("trim",$arry_class_code);
                $start_time     =trim($start_time);
                $end_time       =trim($end_time);
                $class_code_list=trim(implode("','",$arry_class_code));
                $arry_class_user_read_group_detail=array();

            //-------------------------------------------
            //SQL處理
            //-------------------------------------------

                //---------------------------------------
                //檢核班級是否存在
                //---------------------------------------

                    $sql="
                        SElECT
                            `class_code`
                        FROM `class`
                        WHERE 1=1
                            AND `class_code` IN ('{$class_code_list}')
                    ";
                    $db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
                    if(empty($db_results)){
                        $err='GET_CLASS_USER_READ_GROUP_DETAIL_INFO:CLASS_CODE ERROR';
                        die($err);
                    }

                //---------------------------------------
                //檢核uid
                //---------------------------------------

                    $arry_uid=array();
                    $uid_list="";
                    $sql="
                        SElECT
                            `uid`
                        FROM `student`
                        WHERE 1=1
                            AND `class_code` IN ('{$class_code_list}')
                        GROUP BY `uid`
                    ";
                    $db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
                    if(empty($db_results)){
                        $err='GET_CLASS_USER_READ_GROUP_DETAIL_INFO:CLASS_CODE ERROR';
                        die($err);
                    }else{
                        foreach($db_results as $db_result){
                            $rs_uid=(int)$db_result['uid'];
                            $arry_uid[]=$rs_uid;
                        }
                        $uid_list=implode(",",$arry_uid);
                    }

                //---------------------------------------
                //SQL撈取
                //---------------------------------------

                    foreach($arry_uid as $rs_uid){
                        $rs_uid=(int)$rs_uid;
                        $sql="
                            SELECT
                                `mssr`.`mssr_book_borrow_log`.`user_id`
                            FROM `mssr`.`mssr_book_borrow_log`
                            WHERE 1=1
                                AND `mssr`.`mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$start_time}' AND '{$end_time}'
                                AND `mssr`.`mssr_book_borrow_log`.`user_id` = {$rs_uid}
                            GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`, `mssr`.`mssr_book_borrow_log`.`book_sid`
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        $arry_class_user_read_group_detail[$rs_uid]=count($db_results);
                    }


                    //回傳
                    return $arry_class_user_read_group_detail;
        }
?>