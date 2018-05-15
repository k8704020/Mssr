<?php
//-------------------------------------------------------
//函式: mb_strrev()
//用途: 字串反轉,同strrev()
//日期: 2012年3月15日
//作者: jeff@max-life
//-------------------------------------------------------

    function mb_strrev($string,$encode=""){
    //---------------------------------------------------
    //字串反轉
    //---------------------------------------------------
    //$string   字串  
    //$encode   文字編碼,預設"",表示採用文字內部編碼 
    //          UTF-8,UTF-8,GB2312,iso-2022-jp 
    //---------------------------------------------------
        
        //參數檢驗
        if(!isset($string)||(trim($string)=='')){
            return false;
        }
        if(!isset($encode)||(trim($encode)=='')){
            $encode=mb_internal_encoding();
        }

        //處理
        $arry=array();
        $len =mb_strlen($string,$encode);

        for($i=$len-1;$i>=0;$i--){
            $char  =mb_substr($string,$i,1,$encode);
            $arry[]=$char;
        }

        return implode('',$arry);
    }
?>