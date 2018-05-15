<?php
//-------------------------------------------------------
//函式: session_end()
//用途: 終止SESSION
//日期: 2011年11月10日
//作者: jeff@max-life
//-------------------------------------------------------

    function session_end($TZone="Asia/Taipei"){
    //---------------------------------------------------
    //終止SESSION
    //---------------------------------------------------
    //$TZone    時區代碼,預設台灣台北
    //---------------------------------------------------

        //設定時區
        date_default_timezone_set($TZone);

        //清除SESSION
        @session_start();

        $_SESSION=array();

        if(isset($_COOKIE[session_name()])){
            setcookie(session_name(),"",time()-3600,"/");
        }

        session_destroy();
    }
?>
