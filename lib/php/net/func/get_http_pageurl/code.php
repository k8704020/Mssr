<?php
//-------------------------------------------------------
//函式: get_http_pageurl()
//用途: 取得本頁網址
//日期: 2011年10月29日
//作者: jeff@max-life
//-------------------------------------------------------

    function get_http_pageurl(){
    //---------------------------------------------------
    //取得本頁網址
    //---------------------------------------------------
    //回傳值    http://www.xxx.xx/xx/01.php?id=1
    //回傳值	http://www.xxx.xx/xx/01.php
    //---------------------------------------------------

        if(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'])!='off'){
            $SCHEMA="https";
        }else{
            $SCHEMA="http";
        }

        $HOST=$_SERVER['HTTP_HOST'];    // localhost or localhost:8080
        $URI =$_SERVER['REQUEST_URI'];	// /xx/01.php?id=1

        return "{$SCHEMA}://{$HOST}{$URI}";
    }
?>