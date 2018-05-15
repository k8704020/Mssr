<?php
//-------------------------------------------------------
//函式: date_week_array()
//用途: 週區段陣列
//日期: 2012年2月8日
//作者: jeff@max-life
//-------------------------------------------------------

    function date_week_array($year,$month){
    //---------------------------------------------------
    //週區段陣列
    //---------------------------------------------------
    //$year     年
    //$month    月
    //
    //
    //本函式會傳回某年某月,每一週的起始與終止日的資訊
    //如果該月月初,非禮拜一,則要往前推至禮拜一
    //如果該月月尾,非禮拜日,則要往後推至禮拜日
    //
    //範例如下,假設2012年2月為我們的目標月份,則本函式推出
    //
    //第1週 --> 起始日: 2012-01-30,終止日 2012-02-05
    //第2週 --> 起始日: 2012-02-06,終止日 2012-02-12
    //第3週 --> 起始日: 2012-02-13,終止日 2012-02-19
    //第4週 --> 起始日: 2012-02-20,終止日 2012-02-26
    //第5週 --> 起始日: 2012-02-27,終止日 2012-03-04
    //---------------------------------------------------

        //參數檢驗
        if(!isset($year)||!is_numeric($year)){
            return array();
        }
        if(!isset($month)||!is_numeric($month)){
            return array();
        }

        //週區段參數陣列
        $arry=array(
            0=>array('f'=>6,'b'=>0), //禮拜日
            1=>array('f'=>0,'b'=>6), //禮拜一
            2=>array('f'=>1,'b'=>5), //禮拜二
            3=>array('f'=>2,'b'=>4), //禮拜三
            4=>array('f'=>3,'b'=>3), //禮拜四
            5=>array('f'=>4,'b'=>2), //禮拜五
            6=>array('f'=>5,'b'=>1)  //禮拜六
        );

        //變數設定
        $arry_week=array();
        $arry_week_first  =array();
        $arry_week_between=array();
        $arry_week_last   =array();

        //第一天,最末天
        $first_date=$year."-".$month."-"."01";
        $last_date ="";
        $last_day  =date("t",strtotime($first_date));
        $info      =date_parse($first_date);
        $last_date =$info["year"]."-".$info["month"]."-".$last_day;

        //-----------------------------------------------
        //推最各週,起始日,終止日
        //-----------------------------------------------

            //第一週
            $week      =date('w',strtotime($first_date));
            $sday      =$arry[$week]['f'];
            $eday      =$arry[$week]['b'];
            $sdate     =date("Y-m-d",strtotime($first_date)-($sday)*86400);
            $edate     =date("Y-m-d",strtotime($first_date)+($eday)*86400);
            $other_s   =date("Y-m-d",strtotime($edate)+(1)*86400);
            $arry_week_first[]=array('sdate'=>$sdate,'edate'=>$edate);

            //最末週
            $week      =date('w',strtotime($last_date));
            $sday      =$arry[$week]['f'];
            $eday      =$arry[$week]['b'];
            $sdate     =date("Y-m-d",strtotime($last_date)-($sday)*86400);
            $edate     =date("Y-m-d",strtotime($last_date)+($eday)*86400);
            $other_e   =date("Y-m-d",strtotime($sdate)-(1)*86400);
            $arry_week_last[]=array('sdate'=>$sdate,'edate'=>$edate);

            //其他週
            $st=strtotime($other_s);
            $et=strtotime($other_e);
            while($st<$et){
                $sdate=date("Y-m-d",$st);
                $edate=date("Y-m-d",$st+(86400*6));
                $arry_week_between[]=array('sdate'=>$sdate,'edate'=>$edate);
                $st+=(86400*7);
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------
            $arry_week=array_merge($arry_week_first,$arry_week_between,$arry_week_last);
            return $arry_week;
    }
?>