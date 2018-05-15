<?php
//-------------------------------------------------------
//函式: date_monthdays()
//用途: 取回月份天數
//日期: 2011年12月25日
//作者: jeff@max-life
//-------------------------------------------------------

    function date_monthdays($year,$month){
    //---------------------------------------------------
    //取回月份天數
    //---------------------------------------------------
    //$year     年
    //$month    月
    //---------------------------------------------------
        
        //參數檢驗
        if(!isset($year)||!is_numeric($year)){
            return false;
        }
        if(!isset($month)||!is_numeric($month)){
            return false;
        }

        //date("t") 取回月份天數
        $date="{$year}-{$month}-01";
        return date("t",strtotime($date));
    }
?>