<?php
//-------------------------------------------------------
//函式: get_class_code_read_group_info()
//用途: 取得班級平均閱讀本數
//日期: 2014年11月30日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    ////---------------------------------------------------
    ////設置測試資料
    ////---------------------------------------------------
    //
    //    $class_code=trim("gcp_2014_1_3_5");
    //    $arry_time =array('2014-08','2014-09','2014-10','2014-11');
    //    echo "<Pre>";
    //    print_r(get_class_code_read_group_info($class_code,$arry_time));
    //    echo "</Pre>";

    //---------------------------------------------------
    //函式
    //---------------------------------------------------

        function get_class_code_read_group_info($class_code,$arry_time){
        //-----------------------------------------------
        //函式: get_class_code_read_group_info()
        //用途: 取得班級平均閱讀本數
        //-----------------------------------------------
        //$class_code   班級代號
        //$arry_time    時間條件
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

                            case 'mysql':
                            //連結類型為mysql

                                //連結物件判斷
                                $has_conn=false;

                                if(!$conn){
                                    $has_conn=true;

                                    $err ='DB_RESULT:CONNECT FAIL';

                                    $conn=@mysql_connect($db_host,$db_user,$db_pass)
                                    or die($err);
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
                                $err='DB_RESULT:SELECT DB ENCODE FAIL';
                                @mysql_set_charset($db_encode,$conn) or
                                die($err);

                                $err='DB_RESULT:SELECT DB FAIL';
                                @mysql_select_db($db_name,$conn) or
                                die($err);

                                $err='DB_RESULT:QUERY FAIL';
                                $result=@mysql_query($sql,$conn) or
                                die($err);

                                //建立資料集陣列
                                $arry_result=array();

                                if(mysql_num_rows($result)!==0){
                                    while($row=mysql_fetch_assoc($result)){
                                        $i=0;
                                        $arry_row=array();
                                        for($j=0;$j<mysql_num_fields($result);$j++){
                                            $filed_name =mysql_field_name($result,$j);
                                            $filed_value=$row[$filed_name];

                                            $arry_row[$filed_name]=$filed_value;
                                        }
                                        $i++;
                                        $arry_result[]=$arry_row;
                                    }
                                }

                                //傳回資料集陣列
                                return $arry_result;

                                //釋放資源
                                mysql_free_result($result);
                                if($has_conn==true){
                                    mysql_close($conn);
                                }

                            break;

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
            //$class_code   班級代號
            //$arry_time    時間條件

                //檢核參數
                if(!isset($class_code)||(trim($class_code)==='')){
                    $err='GET_CLASS_CODE_READ_GROUP_INFO:NO CLASS_CODE';
                    die($err);
                }else{
                    $class_code=addslashes(trim($class_code));
                }

                if(!isset($arry_time)||(!is_array($arry_time))||(empty($arry_time))){
                    $err='GET_CLASS_CODE_READ_GROUP_INFO:NO ARRY_TIME';
                    die($err);
                }else{
                    $arry_time=array_map("addslashes",array_map("trim",$arry_time));
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
                    $err ='GET_CLASS_CODE_READ_GROUP_INFO:CONNECT FAIL';
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
                    $err ='GET_CLASS_CODE_READ_GROUP_INFO:CONNECT FAIL';
                    die($err);
                }

            //-------------------------------------------
            //初始化
            //-------------------------------------------
            //$class_code   班級代號
            //$arry_time    時間條件

                $class_code=trim($class_code);
                $arry_time =$arry_time;
                $arry_class_code_read_group=array();

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
                            AND `class_code`='{$class_code}'
                    ";
                    $db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
                    if(empty($db_results)){
                        $err='GET_CLASS_CODE_READ_GROUP_INFO:CLASS_CODE ERROR';
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
                            AND `class_code`='{$class_code}'
                        GROUP BY `uid`
                    ";
                    $db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
                    if(empty($db_results)){
                        foreach($arry_time as $time){
                            $arry_class_code_read_group[$time]=0;
                        }
                        //回傳
                        return $arry_class_code_read_group;
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

                    foreach($arry_time as $time){

                        $time =trim($time);
                        $year =date("Y",strtotime($time));
                        $month=date("m",strtotime($time));
                        $begin=mktime(0,0,0,$month,1,$year);
                        $end  =mktime(0,0,0,$month+1,0,$year);
                        $diff =$end-$begin;
                        $days =($diff/86400)+1;

                        $sql="
                            SELECT
                                `mssr`.`mssr_book_borrow_log`.`user_id`,
                                `mssr`.`mssr_book_borrow_log`.`borrow_sdate`
                            FROM  `mssr`.`mssr_book_borrow_log`
                            WHERE 1=1
                                AND `mssr`.`mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$time}-1 00:00:00' AND '{$time}-{$days} 23:59:59'
                                AND `mssr`.`mssr_book_borrow_log`.`user_id` IN ({$uid_list})
                            GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`, `mssr`.`mssr_book_borrow_log`.`book_sid`
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        if(empty($db_results)){
                            $class_code_read_group  =0;
                        }else{
                            $total_read_group       =count($db_results);
                            $class_code_read_group  =ceil((int)$total_read_group/(int)count($arry_uid));
                        }
                        $arry_class_code_read_group[$time]=$class_code_read_group;
                    }

                    //回傳
                    return $arry_class_code_read_group;
        }
?>