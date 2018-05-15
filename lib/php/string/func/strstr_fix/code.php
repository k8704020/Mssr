<?php
//-------------------------------------------------------
//函式: strstr_fix()
//用途: strstr修正,模仿php5.3,支援往前取
//日期: 2012年3月16日
//作者: jeff@max-life
//-------------------------------------------------------

    function strstr_fix($string,$find,$before=false){
    //---------------------------------------------------
    //strstr修正,模仿php5.3,支援往前取
    //---------------------------------------------------
    //strstr(字串,搜尋)             預設往後取
    //strstr(字串,搜尋,是否往前取)  php5.3 後才支援往前取
    //---------------------------------------------------

        //參數檢驗
        if(!isset($string)||(trim($string)==='')){
            return false;
        }
        if(!isset($find)||(trim($find)==='')){
            return false;
        }
        if(!isset($before)){
            $before=false;
        }else{
            $before=(bool)$before;
        }

        //找不到搜尋字串,大小寫有分
        if(strpos($string,$find)===false){
            return false;
        }

        //處理
        $arry=explode($find,$string);
        if($before===true){
            //往前取
            $result=$arry[0];
        }else{
            //往後取
            $result=$find.$arry[1];
        }

        return $result;
    }
?>