<?php
//-------------------------------------------------------
//函式: array_getkey()
//用途: 比對值,取回對應鍵
//日期: 2011年11月30日
//作者: jeff@max-life
//-------------------------------------------------------

    function array_getkey($val,$arry,$equal=false){
    //---------------------------------------------------
    //比對值,取回對應鍵
    //---------------------------------------------------
    //$val      值
    //$arry     比對陣列
    //$equal    完全比對
    //          $equal=true  完全比對
    //          $equal=false 不完全比對,預設
    //
    //本函式比對成功,回傳回對應的鍵
    //本函式比對失敗,回傳false
    //---------------------------------------------------

        if(!isset($val)||(trim($val)=='')){
            return false;
        }
        if(!isset($arry)||(empty($arry))){
            return false;
        }

        if($equal==false){
            $val =strtolower($val);

            $arry_keys=array_keys($arry);
            $arry_vals=array_values($arry);
            $arry_vals=array_map('strtolower',$arry_vals);
            $arry=array_combine($arry_keys,$arry_vals);
        }

        if(in_array($val,$arry)){
            $key=array_search($val,$arry);
            return $key;
        }else{
            return false;
        }
    }
?>