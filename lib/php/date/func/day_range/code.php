<?php
//-------------------------------------------------------
//函式: day_range()
//用途: 日期區段
//日期: 2012年1月31日
//作者: jeff@max-life
//-------------------------------------------------------

    function day_range($date,$dayno,$TZone='Asia/Taipei'){
    //---------------------------------------------------
    //日期區段
    //---------------------------------------------------
    //$date     日期
    //$dayno    天數,值可以為正或負
    //$TZone    時區,預設 'Asia/Taipei'
    //
    //本函式會傳回指定日期往前推n日或往後推n日的日期陣列
    //
    //  $dayno為正時,則往後推
    //  $dayno為負時,則往前推
    //---------------------------------------------------

        //參數檢驗
        if(!isset($dayno)||(!is_numeric($dayno))){
            return array();
        }
        if(!isset($TZone)||(trim($TZone)==='')){
            $TZone='Asia/Taipei';
            date_default_timezone_set($TZone);
        }else{
            date_default_timezone_set($TZone);
        }
        if(!isset($date)||(trim($date)==='')){
            $date=date("Y-m-d",time());
        }

        //處理
        if($dayno<0){
            $sdate=strtotime($date);
            $edate=strtotime($date)+(($dayno+1)*86400);
        }elseif($dayno>0){
            $sdate=strtotime($date);
            $edate=strtotime($date)+(($dayno-1)*86400);
        }else{
            return array($date);
        }
        //echo date("Y-m-d",$sdate)."<br/>";
        //echo date("Y-m-d",$edate)."<br/>";

        if($sdate>$edate){
            $tmp=$sdate;
            $sdate=$edate;
            $edate=$tmp;
        }

        $days=array();
        while($sdate<=$edate){
            $tmp=date("Y-m-d",$sdate);
            $days[]=$tmp;
            $sdate+=86400;
        }
        //echo "<pre>";
        //print_r($days);
        //echo "</pre>";

        return $days;
    }
?>