<?php
//-------------------------------------------------------
//函式: to_page_encode()
//用途: 轉換為頁面語系編碼
//日期: 2011年12月2日
//作者: jeff@max-life
//-------------------------------------------------------

    function to_page_encode($val){
    //---------------------------------------------------
    //轉換為頁面語系編碼
    //---------------------------------------------------
    //$arry_enc 語系陣列
    //$str_enc  文字語系編碼
    //$page_enc 頁面語系編碼
    //---------------------------------------------------

        if(!isset($val)||(trim($val)=='')){
            return false;
        }

        $arry_enc=array('UTF-8','BIG-5','GB2312');

        $str_enc =mb_detect_encoding($val,$arry_enc);
        $page_enc=mb_internal_encoding();

        return mb_convert_encoding($val,$page_enc,$str_enc);
    }
?>