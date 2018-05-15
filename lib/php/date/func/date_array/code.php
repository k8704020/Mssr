<?php
//-------------------------------------------------------
//函式: date_array()
//用途: 日期陣列
//日期: 2012年1月31日
//作者: jeff@max-life
//-------------------------------------------------------

    function date_array($sdate,$edate){
    //---------------------------------------------------
    //日期陣列
    //---------------------------------------------------
    //$sdate    起始日期,格式 yyyy-mm-dd
    //$edate    終止日期,格式 yyyy-mm-dd
    //
    //本函式會傳回起始日期至終止日期間的日期陣列
    //---------------------------------------------------

        //參數檢驗
        if(!isset($sdate)||(trim($sdate)==='')){
            return array();
        }
        if(!isset($edate)||(trim($edate)==='')){
            return array();
        }

        //處理
        $sts=strtotime($sdate);
        $ets=strtotime($edate);

        if($sts>$ets){
            $tmp=$sts;
            $sts=$ets;
            $ets=$tmp;
        }

        $days=array();
        while($sts<=$ets){
            $ndate=date("Y-m-d",$sts);
            $days[]=$ndate;
            $sts+=86400;
        }

        //回傳
        return $days;
    }
?>