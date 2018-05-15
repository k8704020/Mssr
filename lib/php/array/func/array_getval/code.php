<?php
//-------------------------------------------------------
//函式: array_getval()
//用途: 比對值,取回對應值
//日期: 2011年11月30日
//作者: jeff@max-life
//-------------------------------------------------------

    function array_getval($key,$arry,$equal=false){
    //---------------------------------------------------
    //比對鍵,取回對應值
    //---------------------------------------------------
    //$key      鍵
    //$arry     比對陣列
    //$equal    完全比對
    //          $equal=true  完全比對
    //          $equal=false 不完全比對,預設
    //
    //本函式比對成功,回傳回對應的值
    //本函式比對失敗,回傳false
    //---------------------------------------------------

        if(!isset($key)||(trim($key)=='')){
            return false;
        }
        if(!isset($arry)||(empty($arry))){
            return false;
        }

        if($equal==false){
            $key =strtolower($key);

            $arry_keys=array_keys($arry);
            $arry_vals=array_values($arry);
            $arry_keys=array_map('strtolower',$arry_keys);
            $arry=array_combine($arry_keys,$arry_vals);
        }

        if(array_key_exists($key,$arry)){
            $val=$arry[$key];
            return $val;
        }else{
            return false;
        }
    }
?>