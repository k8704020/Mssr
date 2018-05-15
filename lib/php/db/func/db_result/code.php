<?php
//-------------------------------------------------------
//函式: db_result()
//用途: 資料集陣列
//日期: 2011年10月29日
//作者: tim@max-life
//-------------------------------------------------------

    function db_result($conn_type='mysql',$conn='',$sql,$arry_limit=array(),$arry_conn){
    //---------------------------------------------------
    //取得資料筆數
    //---------------------------------------------------
    //$conn_type    資料庫連結類型      mysql | pdo     預設 mysql
    //$conn         資料庫連結物件
    //$sql          SQL查詢字串
    //$arry_limit   資料筆數限制陣列(等同LIMIT inx,size)
    //$arry_conn    資料庫資訊陣列
    //---------------------------------------------------

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
?>