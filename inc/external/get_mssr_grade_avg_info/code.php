<?php
//-------------------------------------------------------
//函式: get_mssr_grade_avg_info()
//用途: 取得全學年平均積分資訊
//日期: 2014年1月20日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    ////---------------------------------------------------
    ////設置測試資料
    ////---------------------------------------------------
    //
    //    $school_code=trim("gcp");
    //    $grade=2;
    //    get_mssr_grade_avg_info($school_code,$grade);

    //---------------------------------------------------
    //函式
    //---------------------------------------------------

        function get_mssr_grade_avg_info($school_code,$grade){
        //-----------------------------------------------
        //函式: get_mssr_grade_avg_info()
        //用途: 取得全學年平均積分資訊
        //-----------------------------------------------
        //$school_code  學校代號
        //$grade        年級
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
                                $result=$conn->prepare($sql);
                                $result->execute() or
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

                if(!function_exists("stb")){
                    function stb($arry){
                    //先算出平均值
                        $avg=array_sum($arry)/count($arry);
                        $ds=array();
                        $i=0;
                        while(list($k,$v)=each($arry)){
                            $d=($v-$avg);
                            $ds[$i]=pow($d,2);
                            $i++;
                        }
                        $sqrt=sqrt((array_sum($ds)/count($ds)));
                        return $sqrt;
                    }
                }

            //-------------------------------------------
            //參數處理
            //-------------------------------------------

                //檢核參數
                if(!isset($school_code)||(trim($school_code)==='')){
                    $err='GET_MSSR_GRADE_AVG_INFO:NO SCHOOL_CODE ';
                    die($err);
                }else{
                    $school_code=addslashes(trim($school_code));
                }

                if(!isset($grade)||(trim($grade)==='')){
                    $err='GET_MSSR_GRADE_AVG_INFO:NO GRADE ';
                    die($err);
                }else{
                    $grade=(int)$grade;
                    if($grade===0){
                        $err='GET_MSSR_GRADE_AVG_INFO:GRADE IS INVALID';
                        die($err);
                    }
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
                    $err ='GET_MSSR_GRADE_AVG_INFO:CONNECT FAIL';
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
                    $err ='GET_MSSR_GRADE_AVG_INFO:CONNECT FAIL';
                    die($err);
                }

            //-------------------------------------------
            //初始化
            //-------------------------------------------

                $date_time=date("Y-m-d h:i:s");
                $date_time=date('Y-m-d h:i:s', strtotime('-7 day', strtotime($date_time)));

                $arry_user=array();
                $arry_user_avg_info=array();
                $curdate=date("Y-m-d");

            //-------------------------------------------
            //SQL處理
            //-------------------------------------------

                //---------------------------------------
                //抓取該學校該年級所有使用者
                //---------------------------------------

                    $query_sql="
                        SELECT
                            `student`.`uid`
                        FROM `student`
                            INNER JOIN `class` ON
                            `student`.`class_code`=`class`.`class_code`
                            INNER JOIN `semester` ON
                            `class`.`semester_code`=`semester`.`semester_code`
                        WHERE 1=1
                            AND `semester`.`school_code`='{$school_code}'
                            AND `class`.`grade`={$grade}
                            AND `semester`.`start` < '{$curdate}'
                            AND `semester`.`end` > '{$curdate}'
                    ";
                    //echo "<Pre>";
                    //print_r($query_sql);
                    //echo "</Pre>";
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$query_sql,array(),$arry_conn_user);
                    if(!empty($arrys_result)){
                        foreach($arrys_result as $arry_result){
                            $uid=(int)$arry_result['uid'];
                            $arry_user[]=$uid;
                        }
                    }else{
                        $err ='GET_MSSR_GRADE_AVG_INFO:RESULT FAIL';
                        die($err);
                    }

                //---------------------------------------
                //抓取該學校該年級所有使用者積分資訊
                //---------------------------------------
                //積分算法
                //1. 閱讀量*1
                //2. 推薦量*3
                //---------------------------------------

                    foreach($arry_user as $user_id){

                        $user_total_cno=0;
                        $user_id       =(int)$user_id;

                        //-------------------------------
                        //抓取使用者閱讀量
                        //-------------------------------

                            $sql="
                                SELECT
                                    `user_id`
                                FROM `mssr_book_borrow`
                                WHERE 1=1
                                    AND `user_id`={$user_id}
                                    AND `borrow_sdate` > '{$date_time}'
                                GROUP BY `book_sid`
                            ";
                            $result_cno=count(db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr));
                            $result_cno=((int)$result_cno)*1;
                            $user_total_cno+=$result_cno;

                        //-------------------------------
                        //抓取使用者推薦量
                        //-------------------------------

                            $sql="
                                SELECT
                                    count(*) AS `result_cno`
                                FROM `mssr_rec_book_cno_one_week`
                                WHERE 1=1
                                    AND `user_id`={$user_id}
                            ";
                            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                            if(!empty($arrys_result)){
                                $result_cno=((int)$arrys_result[0]['result_cno'])*3;
                            }else{
                                $result_cno=0;
                            }
                            $user_total_cno+=$result_cno;

                            //回填
                            $arry_user_avg_info[$user_id]=$user_total_cno;
                    }

                    //四捨五入
                    $stb=(int)round(stb($arry_user_avg_info),0);
                    $stb_len=(int)round($stb/3);

                    //標準差區間
                    $arry_stb=array(
                        "好"  =>$stb+$stb_len,
                        "普通"=>$stb,
                        "加強"=>$stb-$stb_len
                    );

                    return $arry_stb;
        }
?>