<?php
//-------------------------------------------------------
//函式: fcsv_to_array()
//用途: 載入csv檔,並傳回資料陣列
//日期: 2012年3月7日
//作者: jeff@max-life
//-------------------------------------------------------

    function fcsv_to_array($file,$delimiter=",",$enclosure="'",$fso_enc="BIG5"){
    //---------------------------------------------------
    //載入csv檔,並傳回資料陣列
    //---------------------------------------------------
    //$file         檔案路徑
    //$delimiter    欄分隔符號,預設 ,
    //$enclosure    值包圍符號,預設 '
    //$fso_enc      檔案系統編碼,預設 BIG5
    //
    //本函式執行成功時傳回資料陣列
    //本函式執行失敗時傳回空陣列
    //---------------------------------------------------
    //csv檔範例內容如下
    //---------------------------------------------------
    //'列1欄1','列1欄2','列1欄3'
    //'列2欄1','列2欄2','列2欄3'
    //'列3欄1','列3欄2','列3欄3'
    //---------------------------------------------------

        //參數檢驗
        if(!isset($file)||trim($file)===''){
            return array();
        }
        if(!isset($delimiter)||trim($delimiter)===''){
            $delimiter=",";
        }
        if(!isset($enclosure)||trim($enclosure)===''){
            $enclosure="'";
        }
        if(!isset($fso_enc)||trim($fso_enc)===''){
            $fso_enc="BIG5";
        }

        //處理
        $page_enc=mb_internal_encoding();
        $file_enc=mb_convert_encoding($file,$fso_enc,$page_enc);

        if(!file_exists($file_enc)){
            return array();
        }

        $arrys =array();
        $handle=fopen($file_enc,"r");
        while(($arry=fgetcsv($handle,1000,$delimiter,$enclosure))!==false){

            //排除空白列
            if($arry[0]!=null){
                $arrys[]=$arry;
            }
        }
        fclose($handle);

        //回傳
        return $arrys;
    }
?>