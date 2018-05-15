<?php
//-------------------------------------------------------
//函式: date_daycnos()
//用途: 取回2日期間的天數
//日期: 2012年2月16日
//作者: jeff@max-life
//-------------------------------------------------------

    function date_daycnos($sdate,$edate,$TZone='Asia/Taipei'){
    //---------------------------------------------------
    //取回2日期間的天數
    //---------------------------------------------------
    //$sdate    起始日
    //$edate    終止日
    //$TZone    時區,預設 'Asia/Taipei'
    //---------------------------------------------------

        if(!isset($sdate)||(trim($sdate)=='')){
            return false;
        }
        if(!isset($edate)||(trim($edate)=='')){
            return false;
        }
        if(!isset($TZone)||(trim($TZone)=='')){
            $TZone='Asia/Taipei';
        }

        //設定時區
        date_default_timezone_set($TZone);

        //處理
        $sts=strtotime($sdate);
        $ets=strtotime($edate);
        $tts=0;

        if($sts>$ets){
           $tts=$sts;
           $sts=$ets;
           $ets=$tts;
        }

        $cnos=0;
        while($sts<=$ets){
            $cnos++;
            $sts+=86400;
        }

        //回傳
        return $cnos;
    }
?>