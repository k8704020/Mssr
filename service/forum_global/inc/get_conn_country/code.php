<?php
//-------------------------------------------------------
//函式: get_conn_country()
//用途: 取得國際連線物件
//-------------------------------------------------------

    function get_conn_country($user_id,$account){
    //---------------------------------------------------
    //函式: get_conn_country()
    //用途: 取得國際連線物件
    //---------------------------------------------------
    //$user_id  使用者主索引
    //$account  使用者帳號
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            global $APP_ROOT;
            global $arry_conn_mssr_hk;
            global $arry_conn_mssr_tw;
            global $arry_conn_mssr_sg;

            if(!isset($user_id)||(int)($user_id)===0){
                $err='GET_CONN_COUNTRY:NO USER_ID';
                die($err);
            }else{
                $user_id=(int)($user_id);
            }

            if(!isset($account)||trim($account)===''){
                $err='GET_CONN_COUNTRY:NO ACCOUNT';
                die($err);
            }else{
                $account=trim($account);
            }

            error_reporting(E_ALL);

        //-----------------------------------------------
        //外掛函式檔
        //-----------------------------------------------

            if((!function_exists("mysql_prep"))&&(!function_exists("db_result"))){
                if(false===@include_once($APP_ROOT.'lib/php/db/code.php')){
                    return false;
                }
            }

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //-------------------------------------------
            //通用
            //-------------------------------------------

        //-----------------------------------------------
        //SQL
        //-----------------------------------------------

            $arry_conn=[];

            $sql="
                SELECT `user`.`member`.`uid`,`user`.`school`.`country_code`
                FROM `user`.`member`
                    INNER JOIN `user`.`member_school` ON
                    `user`.`member`.`uid`=`user`.`member_school`.`uid`
                    INNER JOIN `user`.`school` ON
                    `user`.`member_school`.`school_code`=`user`.`school`.`school_code`
                WHERE `user`.`member`.`uid`={$user_id}
                    AND `user`.`member`.`account`='{$account}'
            ";
            $db_hk_results=db_result($conn_type='pdo','',$sql,$arry_limit=array(),$arry_conn_mssr_hk);
            if(!empty($db_hk_results)){
                $rs_country_code=$db_hk_results[0]['country_code'];
                $arry_conn="arry_conn_mssr_{$rs_country_code}";
                $arry_conn=$$arry_conn;
            }else{
                $db_tw_results=db_result($conn_type='pdo','',$sql,$arry_limit=array(),$arry_conn_mssr_tw);
                if(!empty($db_tw_results))$arry_conn=$arry_conn_mssr_tw;
            }

        //-----------------------------------------------
        //建立連線
        //-----------------------------------------------

            //$db_host  =$arry_conn['db_host'];
            //$db_user  =$arry_conn['db_user'];
            //$db_pass  =$arry_conn['db_pass'];
            //$db_name  =$arry_conn['db_name'];
            //$db_encode=$arry_conn['db_encode'];
            //
            //$conn_info="mysql".":host={$db_host}".";dbname={$db_name}";
            //$options = array(
            //    PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,           //設置錯誤提示，只獲取代碼
            //    PDO::ATTR_CASE,              PDO::CASE_NATURAL,             //列名按照原始的方式
            //    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode}"    //設置語系
            //);
            //
            //try{
            //    $conn=@new PDO($conn_info, $db_user, $db_pass,$options);
            //}catch(PDOException $e){
            //    $err ='GET_CONN_COUNTRY:CONNECT FAIL';
            //    die($err);
            //}

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $arry_conn;
    }
?>