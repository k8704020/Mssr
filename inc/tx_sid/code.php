<?php
//-------------------------------------------------------
//函式: tx_sid()
//用途: 交易識別碼
//-------------------------------------------------------

    function tx_sid($create_by,$tx_type,$encode){
    //---------------------------------------------------
    //函式: tx_sid()
    //用途: 交易識別碼
    //---------------------------------------------------
    //$create_by    建立者
    //$tx_type      交易類型    tx_sys | tx_gift
    //$encode       頁面編碼
    //
    //---------------------------------------------------
    //字首:
    //      tx_sys部分
    //          mts + create_by(建立者) + YYYYMMDDhhiiss + 亂數組成，共25碼，
    //          mts + 1 + 20130101000000 + 0000001
    //
    //      tx_gift部分
    //          mtg + create_by(建立者) + YYYYMMDDhhiiss + 亂數組成，共25碼，
    //          mtg + 1 + 20130101000000 + 0000001
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

            if(!isset($tx_type)||trim($tx_type)===''){
                return false;
            }else{
                $tx_type=trim($tx_type);
                if(!in_array($tx_type,array('tx_sys','tx_gift'))){
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

            $tx_sid='';

        //-----------------------------------------------
        //字首部分
        //-----------------------------------------------

            switch($tx_type){
                case 'tx_sys':
                    $prefix="mts";
                break;

                case 'tx_gift':
                    $prefix="mtg";
                break;

                default:
                    return false;
                break;
            }

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
                $tx_sid=$prefix.$create_by.$datetime.$rnd;
            }else{
                $tx_sid=$prefix.$create_by.$datetime;
            }

            return "{$tx_sid}";
    }
?>