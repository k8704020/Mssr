<?php
//-------------------------------------------------------
//函式: array_keyl_ereg()
//用途: 從陣列裡,取回符合條件的元素,本函式僅比對鍵部分
//日期: 2011年12月3日
//作者: jeff@max-life
//-------------------------------------------------------

    function array_keyl_ereg($exp,$arry=array(),$equal=false){
    //---------------------------------------------------
    //從陣列裡,取回符合條件的元素,本函式僅比對鍵部分
    //---------------------------------------------------
    //$exp      正規式
    //$arry     比對陣列
    //$equal    完全比對
    //          $equal=true  完全比對
    //          $equal=false 不完全比對,預設
    //
    //本函式比對成功,回傳符合條件的元素陣列
    //本函式比對失敗,回傳false
    //---------------------------------------------------

        //參數檢驗
        if(!isset($exp)||(trim($exp)=='')){
            if(!isset($arry)||(empty($arry))){
                return false;
            }else{
                return $arry;
            }
        }
        if(!isset($arry)||(empty($arry))){
            return false;
        }
        if(!isset($equal)||(trim($equal)=='')){
            $equal=false;
        }

        //處理
        $out=array();
        if($equal==false){
        //不完全比對
            //echo '不完全比對'.'<br/>';
            foreach($arry as $key=>$val){
                if(mb_eregi($exp,$key)){
                    $out[$key]=$val;
                }
            }
        }else{
        //完全比對
            //echo '完全比對'.'<br/>';
            foreach($arry as $key=>$val){
                if(mb_ereg($exp,$key)){
                   $out[$key]=$val;
                }
            }
        }

        //回傳
        return $out;
    }
?>