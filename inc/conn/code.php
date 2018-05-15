<?php
//-------------------------------------------------------
//函式: conn()
//用途: 資料庫連線設定
//日期: 2013年8月12日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function conn($db_type='mysql',$arry_conn){
    //---------------------------------------------------
    //函式: conn()
    //用途: 資料庫連線設定
    //---------------------------------------------------
    //$db_type      mysql (預設)
    //$arry_conn    資料庫資訊陣列
    //---------------------------------------------------

        //-----------------------------------------------
        //檢驗參數
        //-----------------------------------------------

            if(!isset($db_type)||(trim($db_type)==='')){
                $db_type='mysql';
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

        //-----------------------------------------------
        //開啟連線
        //-----------------------------------------------

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

            //回傳
            return $conn;
    }
?>