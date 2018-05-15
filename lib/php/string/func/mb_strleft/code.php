<?php
//-------------------------------------------------------
//函式: mb_strleft()
//用途: 由左取N個字元
//日期: 2012年3月15日
//作者: jeff@max-life
//-------------------------------------------------------

    function mb_strleft($string,$n,$encode=""){
    //---------------------------------------------------
    //由左取N個字元
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

        $j=1;
        for($i=0;$i<$len;$i++,$j++){
            if($j<=$n){
                $char=mb_substr($string,$i,1,$encode);
                $arry[]=$char;
            }
        }

        echo implode('',$arry);
    }
?>