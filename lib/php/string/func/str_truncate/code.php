<?php
//-------------------------------------------------------
//函式: str_truncate()
//用途: 字串截斷
//日期: 2011年11月1日
//作者: jeff@max-life
//-------------------------------------------------------

    function str_truncate($str,$len,$suffix='...',$encode='UTF-8'){
    //---------------------------------------------------
    //字串截斷
    //---------------------------------------------------
    //$str      字串
    //$len      長度
    //$suffix   附加字尾
    //$encode   文字編碼(預設:UTF-8)
    //
    //UTF-8,GB2312,BIG5,iso-2022-jp
    //---------------------------------------------------

        if(!isset($str)||trim($str)==''){
            return '';
        }else{
            $str=trim($str);
        }

        if(mb_strlen($str,$encode)<=$len){
            return $str.$suffix;
        }else{
            return mb_substr($str,0,$len,$encode).$suffix;
        }
    }
?>
