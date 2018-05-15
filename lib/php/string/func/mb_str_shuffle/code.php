<?php
//-------------------------------------------------------
//函式: mb_str_shuffle()
//用途: 實作多字元版的str_shuffle()函式,打亂字串
//日期: 2012年3月15日
//作者: jeff@max-life
//-------------------------------------------------------

    function mb_str_shuffle($string,$encode=""){
    //---------------------------------------------------
    //實作多字元版的str_shuffle()函式,打亂字串
    //---------------------------------------------------
    //$string   字串
    //$encode   文字內部編碼,預設"",表示採用文字內部編碼
    //          UTF-8,UTF-8,GB2312,iso-2022-jp
    //---------------------------------------------------

        //參數檢驗
        if(!isset($string)||(trim($string)=='')){
            return '';
        }
        if(!isset($encode)||(trim($encode)=='')){
            $encode=mb_internal_encoding();
        }

        //處理
        $len=mb_strlen($string,$encode);

        //由左至右依序取出
        $arry=array();
        for($i=0;$i<$len;$i++){
            $char=mb_substr($string,$i,1,$encode);
            $arry[]=$char;
        }

        //打亂陣列
        shuffle($arry);

        //回傳
        return implode($arry);
    }
?>