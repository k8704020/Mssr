<?php
//-------------------------------------------------------
//函式: array_json1()
//用途: 一維陣列轉json
//日期: 2011年11月28日
//作者: jeff@max-life
//-------------------------------------------------------

    function array_json1($arry){
    //---------------------------------------------------
    //一維陣列轉json
    //---------------------------------------------------
    //$arry 陣列
    //
    //註:本函式會自動將陣列的鍵與值予以脫序
    //---------------------------------------------------

        //參數
        if(!isset($arry)||!is_array($arry)||empty($arry)){
            return '{}';
        }

        //解析
        $tmp=array();
        foreach($arry as $key=>$val){
            if(is_numeric($val)){
            //格式:'key':val
                $key="'".addslashes($key)."'";
                $val="".addslashes($val)."";
                $tmp[]=$key.':'.$val;
            }else{
            //格式:'key':'val'
                $key="'".addslashes($key)."'";
                $val="'".addslashes($val)."'";
                $tmp[]=$key.':'.$val;
            }

        }
        $tmp="{".implode(",",$tmp)."}";

        return $tmp;
    }
?>
