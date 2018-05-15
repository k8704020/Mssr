<?php
//-------------------------------------------------------
//函式: to_fso_encode()
//用途: 轉換為檔案系統語系編碼
//日期: 2011年12月2日
//作者: jeff@max-life
//-------------------------------------------------------

    function to_fso_encode($val,$fso_enc='BIG5'){
    //---------------------------------------------------
    //轉換為檔案系統語系編碼
    //---------------------------------------------------
    //$arry_enc 語系陣列
    //$fso_enc  系統語系編碼,預設 BIG5
    //$str_enc  文字語系編碼
    //$page_enc 頁面語系編碼
    //---------------------------------------------------

        if(!isset($val)||(trim($val)=='')){
            return false;
        }

        $arry_enc=array('UTF-8','BIG-5','GB2312');
        $str_enc =mb_detect_encoding($val,$arry_enc);
        $page_enc=mb_internal_encoding();

        return mb_convert_encoding($val,$fso_enc,$str_enc);
    }
?>