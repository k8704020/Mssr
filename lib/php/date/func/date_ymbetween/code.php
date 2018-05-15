<?php
//-------------------------------------------------------
//函式: date_ymbetween()
//用途: 根據 起始年,月 至 終止年,月 取得起始日到終止日資訊
//日期: 2012年2月20日
//作者: jeff@max-life
//-------------------------------------------------------

    function date_ymbetween(&$syear,&$smonth,&$eyear,&$emonth,$TZone='Asia/Taipei'){
    //---------------------------------------------------
    //根據 起始年,月 至 終止年,月 取得起始日到終止日資訊
    //---------------------------------------------------
    //$syear    起始年
    //$smonth   起始月
    //$eyear    終止年
    //$emonth   終止月
    //$TZone    時區,預設 'Asia/Taipei'
    //---------------------------------------------------

        //參數檢驗
        $year_exp ="^\d{4}$";
        $month_exp="^(0?[1-9]|1[012])$";

        if(!isset($syear)||(mb_eregi($year_exp,$syear)===FALSE)){
            return false;
        }
        if(!isset($smonth)||(mb_eregi($month_exp,$smonth)===FALSE)){
            return false;
        }
        if(!isset($eyear)||(mb_eregi($year_exp,$eyear)===FALSE)){
            return false;
        }
        if(!isset($emonth)||(mb_eregi($month_exp,$emonth)===FALSE)){
            return false;
        }
        if(!isset($TZone)||trim($TZone)==''){
            $TZone='Asia/Taipei';
        }

        //設定時區
        date_default_timezone_set($TZone);

        //日期比較
        $sts   =strtotime("{$syear}-{$smonth}-1");
        $sdate ="";

        $ets   =strtotime("{$eyear}-{$emonth}-1");
        $edate ="";

        //先以1號比較
        $tmp=array();
        if($sts>$ets){
        //起始大於終止
            $tmp['year'] =$syear;
            $tmp['month']=$smonth;
            $syear =$eyear;
            $smonth=$emonth;
            $eyear =$tmp['year'];
            $emonth=$tmp['month'];

            $sdays =date("t",strtotime("{$syear}-{$smonth}-1"));
            $edays =date("t",strtotime("{$eyear}-{$emonth}-1"));

            $sdate="{$syear}-{$smonth}-01";
            $edate="{$eyear}-{$emonth}-{$edays}";
        }else{
        //起始未大於終止
            $sdays =date("t",strtotime("{$syear}-{$smonth}-1"));
            $edays =date("t",strtotime("{$eyear}-{$emonth}-1"));

            $sdate="{$syear}-{$smonth}-01";
            $edate="{$eyear}-{$emonth}-{$edays}";
        }

        //補零
        $sdate=date("Y-m-d",strtotime($sdate));
        $edate=date("Y-m-d",strtotime($edate));

        //回傳
        return array(
            'sdate'=>$sdate,
            'edate'=>$edate
        );
    }
?>