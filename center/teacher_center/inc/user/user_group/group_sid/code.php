<?php
//-------------------------------------------------------
//函式: group_sid()
//用途: 組別識別碼
//-------------------------------------------------------

    function group_sid($create_by,$encode){
    //---------------------------------------------------
    //函式: group_sid()
    //用途: 組別識別碼
    //---------------------------------------------------
    //$create_by    建立者
    //$encode       頁面編碼
    //
    //---------------------------------------------------
    //字首: mg + create_by(建立者) + YYYYMMDDhhiiss + 亂數組成，共25碼，
    //      mg + 1 + 20130101000000 + 00000001
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($create_by)||trim($create_by)===''){
                return false;
            }else{
                $create_by=(int)$create_by;
                if($create_by===0){
                    return false;
                }
            }

            if(!isset($encode)||trim($encode)===''){
                return false;
            }

        //-----------------------------------------------
        //時區
        //-----------------------------------------------

            date_default_timezone_set('Asia/Taipei');

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $group_sid='';

        //-----------------------------------------------
        //字首部分
        //-----------------------------------------------

            $prefix="mg";

        //-----------------------------------------------
        //建立者
        //-----------------------------------------------

            $create_by=(int)$create_by;

        //-----------------------------------------------
        //時間部分
        //-----------------------------------------------

            $datetime=date("YmdHis",time());

        //-----------------------------------------------
        //亂數部分
        //-----------------------------------------------

            //計算前面長度
            $sid_cno=0;
            $sid_cno+=mb_strlen($prefix,$encode);
            $sid_cno+=mb_strlen($create_by,$encode);
            $sid_cno+=mb_strlen($datetime,$encode);

            //亂數種子
            mt_srand(time());

            //亂數長度
            $size=(int)25-(int)$sid_cno;

            //取回亂數
            $rnd ='';
            for($i=1;$i<=$size;$i++){

               $arry=str_split(strval(mt_rand()),1);
               shuffle($arry);
               $rnd.=$arry[mt_rand(0,count($arry)-1)];
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            if($size>0){
                $group_sid=$prefix.$create_by.$datetime.$rnd;
            }else{
                $group_sid=$prefix.$create_by.$datetime;
            }

            return "{$group_sid}";
    }
?>