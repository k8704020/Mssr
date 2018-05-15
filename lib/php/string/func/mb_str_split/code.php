<?php
//-------------------------------------------------------
//函式: mb_str_split()
//用途: 實作多字元版的str_split()函式,指定長度分割字串成陣列
//日期: 2012年3月15日
//作者: jeff@max-life
//-------------------------------------------------------

    function mb_str_split($string,$n=1,$encode=""){
    //---------------------------------------------------
    //實作多字元版的str_split()函式,指定長度分割字串成陣列
    //---------------------------------------------------
    //$string   字串
    //$n        分割長度,預設1
    //$encode   文字內部編碼,預設"",表示採用文字內部編碼
    //          UTF-8,UTF-8,GB2312,iso-2022-jp
    //---------------------------------------------------

        //參數檢驗
        if(!isset($string)||(trim($string)=='')){
            return '';
        }
        if(!isset($n)||(!is_int($n))){
            $n=1;
        }
        if(!isset($encode)||(trim($encode)=='')){
            $encode=mb_internal_encoding();
        }

        //處理
        $len=mb_strlen($string,$encode);

        $i=0;
        $arry=array();
        while($i<$len){
            $part  =mb_substr($string,$i,$n,$encode);
            $arry[]=$part;
            $i=$i+$n;
        }

        //回傳
        return $arry;
    }
?>