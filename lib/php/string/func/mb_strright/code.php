<?php
//-------------------------------------------------------
//函式: mb_strright()
//用途: 由右至左依序取回N個字元
//日期: 2012年3月15日
//作者: jeff@max-life
//-------------------------------------------------------

    function mb_strright($string,$n,$encode=""){
    //---------------------------------------------------
    //由右至左依序取回N個字元
    //---------------------------------------------------
    //$string   字串
    //$n        長度
    //$encode   文字內部編碼,預設"",表示採用文字內部編碼
    //          UTF-8,UTF-8,GB2312,iso-2022-jp
    //---------------------------------------------------

        //參數檢驗
        if(!isset($string)||(trim($string)=='')){
            return '';
        }
        if(!isset($n)||(!is_int($n))){
            return '';
        }
        if(!isset($encode)||(trim($encode)=='')){
            $encode=mb_internal_encoding();
        }

        //處理
        $arry=array();
        $len =mb_strlen($string,$encode);

        //由右至左依序取回N個
        $j=1;    
        for($i=$len-1;$i>=0;$i--,$j++){
            if($j<=$n){
                $char=mb_substr($string,$i,1,$encode);
                $arry[]=$char;        
            }
        }
        $arry=array_reverse($arry);

        echo implode('',$arry);
    }
?>