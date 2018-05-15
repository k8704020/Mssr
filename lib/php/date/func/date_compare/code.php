<?php
//-------------------------------------------------------
//函式: date_compare()
//用途: 比較日期大小
//日期: 2011年12月25日
//作者: jeff@max-life
//-------------------------------------------------------

    function date_compare($date1,$date2){
    //---------------------------------------------------
    //比較日期大小
    //---------------------------------------------------
    //$date1    日期格式字串
    //$date2	日期格式字串
    //
    //日期格式字串,類型
    //yyyy/mm/dd hh:mm:ss
    //yyyy-mm-dd hh:mm:ss
    //yyyy/mm/dd
    //yyyy-mm-dd
    //
    //回傳值格式
    //$date1 = $date2	傳回0
    //$date1 > $date2	傳回1
    //$date1 < $date2	傳回-1
    //---------------------------------------------------

        //參數檢驗
        if(!isset($date1)||(trim($date1)=='')){
            return false;
        }
        if(!isset($date2)||(trim($date2)=='')){
            return false;
        }

        //比較日期
        $timestamp1=strtotime($date1);
        $timestamp2=strtotime($date2);

        if($timestamp1==$timestamp2){
            return 0;
        }elseif($timestamp1>$timestamp2){
            return 1;
        }elseif($timestamp1<$timestamp2){
            return -1;
        }
    }
?>