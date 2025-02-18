<?php
//-------------------------------------------------------
//函式: stripslashes_deep()
//用途: 實作stripslashe,套用在所有陣列成員套用
//日期: 2012年3月13日
//作者: jeff@max-life
//-------------------------------------------------------

    function stripslashes_deep($val){
    //---------------------------------------------------
    //實作stripslashe,套用在所有陣列成員套用
    //---------------------------------------------------
    //如果$val不是陣列,則直接套用
    //如果$val是陣列,則每一個元素(含子陣列元素)均會被套用
    //---------------------------------------------------

        if(!isset($val)){
            return false;
        }

        //如果不是陣列,直接套用
        //如果是陣列,直接利用array_map函式,套用在每一個
        $val = is_array($val) ?
                array_map('stripslashes_deep', $val) :
                stripslashes($val);
        return $val;
    }
?>