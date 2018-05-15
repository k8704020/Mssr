<?php
//-------------------------------------------------------
//函式: auth_check()
//用途: 權限檢核
//-------------------------------------------------------

    function auth_check($db_type='mysql',$arry_conn,$user_type,$auth_type){
    //---------------------------------------------------
    //函式: auth_check()
    //用途: 權限檢核
    //---------------------------------------------------
    //$db_type          mysql (預設)
    //$arry_conn        資料庫連線資訊陣列
    //$user_type        使用者身分
    //$auth_type        權限種類
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($db_type)||(trim($db_type)==='')){
                $db_type='mysql';
            }

            if(!isset($arry_conn)||(empty($arry_conn))){
                $err='AUTH_CHECK:NO ARRY_CONN';
                die($err);
            }

            if(!isset($user_type)||trim($user_type)===''){
                $err='AUTH_CHECK:NO USER_TYPE';
                die($err);
            }else{
                $user_type=trim($user_type);
            }

            if(!isset($auth_type)||trim($auth_type)===''){
                $err='AUTH_CHECK:NO AUTH_TYPE';
                die($err);
            }else{
                //前置字元
                $auth_type='u_'.trim($auth_type);
            }

        //-----------------------------------------------
        //設定
        //-----------------------------------------------

            //權限檢核指標
            $auth_check_flag=false;

            $db_host  =$arry_conn['db_host'];
            $db_name  =$arry_conn['db_name'];
            $db_user  =$arry_conn['db_user'];
            $db_pass  =$arry_conn['db_pass'];
            $db_encode=$arry_conn['db_encode'];

            $conn_info="{$db_type}".":host={$db_host}".";dbname={$db_name}";
            $options = array(
                PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,           //設置錯誤提示，只獲取代碼
                PDO::ATTR_CASE,              PDO::CASE_NATURAL,             //列名按照原始的方式
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode}"    //設置語系
            );

            try{
                $conn=@new PDO($conn_info, $db_user, $db_pass,$options);
            }catch(PDOException $e){
                $err ='AUTH_CHECK:CONNECT FAIL';
                die($err);
            }

        //-----------------------------------------------
        //串接SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `using`
                FROM `permissions`
                WHERE 1=1
                    AND `permission` ='{$user_type}'
                    AND `status`     ='{$auth_type}'
                    AND `using`      =1
                LIMIT 1
            ";

            $err='AUTH_CHECK:QUERY FAIL';
            $result=$conn->prepare($sql);
            $result->execute() or
            die($err);

            //建立資料集陣列
            $arrys_result=array();

            if(($result->rowCount())!==0){
            //有資料存在
                while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                    $arrys_result[]=$arry_row;
                }
            }

            if(count($arrys_result)===0){
                $auth_check_flag=false;
            }else{
                $auth_check_flag=true;
            }

        //-----------------------------------------------
        //釋放資源
        //-----------------------------------------------

            $conn=NULL;

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $auth_check_flag;
    }
?>