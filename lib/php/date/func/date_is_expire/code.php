<?php
//-------------------------------------------------------
//函式: date_is_expire()
//用途: 日期是否過期
//日期: 2011年12月25日
//作者: jeff@max-life
//-------------------------------------------------------

    function date_is_expire($date,$date_end){
    //---------------------------------------------------
    //日期是否過期
    //---------------------------------------------------
    //$date         指定的日期
    //$date_end     底限的日期
    //
    //如果過期,函式傳回 false
    //如果未過期,函式傳回 差異天數
    //---------------------------------------------------

        //參數檢驗
        if(!isset($date)||(trim($date)=="")){
            return false;
        }
        if(!isset($date_end)||(trim($date_end)=="")){
            return false;
        }

        //計算差異
        $timestamp0=strtotime($date);
        $timestamp1=strtotime($date_end);
        $date_diff =ceil(($timestamp1-$timestamp0)/(60*60*24));

        if($date_diff>=0){
            //echo "未超過,差異天數:{$date_diff}"."<br/>";
            return $date_diff;
        }else{
            //echo "已超過,差異天數:{$date_diff}"."<br/>";
            return false;
        }
    }
?>
