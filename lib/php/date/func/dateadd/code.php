<?php
//-------------------------------------------------------
//函式: dateadd()
//用途: 日期運算
//日期: 2011年12月25日
//作者: jeff@max-life
//-------------------------------------------------------

    function dateadd($unit,$number,$date='',$type='timestamp'){
    //---------------------------------------------------
    //日期運算
    //---------------------------------------------------
    //$unit		單位		year,month,day,hour,min,sec
    //$number	數值
    //$date		日期字串
    //                      格式1:yyyy/mm/dd hh:ii:ss
    //                      格式2:yyyy/mm/dd
    //                      預設 :yyyy/mm/dd hh:ii:ss (今天)
    //$type		回傳類型	timestamp|string
    //---------------------------------------------------

        //參數檢驗
        $arry_unit=array('year','month','day','hour','min','sec');
        if(!isset($unit)||(trim($unit)=='')){
            return false;
        }else{
            if(!in_array(strtolower($unit),$arry_unit)){
                return false;
            }else{
                $unit=strtolower($unit);
            }
        }

        if(!isset($number)||(!is_numeric($number))){
            return false;
        }

        if(!isset($date)||(trim($date)=='')){
            $date=date("Y/m/d H:i:s",time());
        }

        $arry_type=array('timestamp','string');
        if(!isset($type)||(trim($type)=='')){
            $type='timestamp';
        }else{
            if(!in_array(strtolower($type),$arry_type)){
                $type='timestamp';
            }
        }

        //日期資訊
        $dayinfo=date_parse($date);
        $year	=$dayinfo["year"];
        $month	=$dayinfo["month"];
        $day	=$dayinfo["day"];
        $hour	=$dayinfo["hour"];
        $min	=$dayinfo["minute"];
        $sec	=$dayinfo["second"];

        $$unit=$$unit+$number;
        $timestamp=mktime($hour,$min,$sec,$month,$day,$year);

        if($type=="timestamp"){
            return $timestamp;
        }elseif($type=="string"){
            return date("Y/m/d H:i:s",$timestamp);
        }
    }
?>