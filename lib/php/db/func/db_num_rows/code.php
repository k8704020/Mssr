<?php
//-------------------------------------------------------
//函式: db_num_rows()
//用途: 取回資料集筆數
//日期: 2011年11月1日
//作者: jeff@max-life
//-------------------------------------------------------

    function db_num_rows($conn,$sql,$arry_conn){
    //---------------------------------------------------
    //取得資料筆數
    //---------------------------------------------------
    //$conn         資料庫連結物件
    //$sql          SQL查詢字串
    //$arry_conn    資料庫連結資訊陣列
    //---------------------------------------------------

        //檢核參數
        if(!$sql){
            $err='DB_NUM_ROWS:NO SQL';
            die($err);
        }

        if(empty($arry_conn)){
            $err='DB_NUM_ROWS:NO ARRY_CONN';
            die($err);
        }else{
            $db_host    =$arry_conn['db_host'];
            $db_user    =$arry_conn['db_user'];
            $db_pass    =$arry_conn['db_pass'];
            $db_name    =$arry_conn['db_name'];
            $db_encode  =$arry_conn['db_encode'];
        }

        $has_conn=false;
        if(!$conn){
            $has_conn=true;

            $err ='DB_NUM_ROWS:CONNECT FIAL';

            $conn=@mysql_connect($db_host,$db_user,$db_pass) or
            die($err);
        }

        //資料庫
        $err='DB_NUM_ROWS:SELECT DB ENCODE FAIL';
        @mysql_set_charset($db_encode,$conn) or
        die($err);

        $err='DB_NUM_ROWS:SELECT DB FAIL';
        @mysql_select_db($db_name,$conn) or
        die($err);

        $err='DB_NUM_ROWS:QUERY FAIL';
        $result=@mysql_query($sql,$conn) or
        die($err);

        //回傳筆數
        return mysql_num_rows($result);

        //釋放資源
        mysql_free_result($result);
        if($has_conn==true){
            mysql_close($conn);
        }
    }
?>