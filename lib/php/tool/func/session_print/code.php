<?php
//-------------------------------------------------------
//函式: session_print()
//用途: 列印出SESSION變數
//日期: 2011年10月29日
//作者: jeff@max-life
//-------------------------------------------------------

    function session_print($key=''){
    //---------------------------------------------------
    //列出SESSION變數
    //---------------------------------------------------
    //$key  欲顯示的SESSION名稱,預設為''
    //
    //      如不指定,預設顯示全部
    //---------------------------------------------------

        @session_start();

        if(isset($key)&&(trim($key)!='')){
            if(isset($_SESSION[$key])){
                echo '<pre>';
                print_r($_SESSION[$key]);
                echo '</pre>';
            }else{
                echo '$_SESSION['.$key.'],不存在..';
            }
        }else{
            echo '<pre>';
            print_r($_SESSION);
            echo '</pre>';
        }
        die();
    }
?>