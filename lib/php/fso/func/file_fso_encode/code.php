<?php
//-------------------------------------------------------
//函式: file_fso_encode()
//用途: 轉換路徑成檔案系統編碼
//日期: 2011年12月2日
//作者: jeff@max-life
//-------------------------------------------------------

    function file_fso_encode($file,$fso_enc='BIG5',$topath=false){
    //---------------------------------------------------
    //轉換路徑成檔案系統編碼
    //---------------------------------------------------
    //$file         檔案路徑(相對)
    //$fso_enc      檔案系統編碼(同OS編碼),預設:BIG5
    //$topath       是否轉換成實際路徑,預設:false
    //---------------------------------------------------

        if(!isset($file)||(trim($file)=='')){
            return false;
        }
        if(!isset($fso_enc)||(trim($fso_enc)=='')){
            return false;
        }

        $arry_enc=array('UTF-8','BIG-5','GB2312');
        $str_enc =mb_detect_encoding($file,$arry_enc);
        $file_enc=mb_convert_encoding($file,$fso_enc,$str_enc);

        if($topath===true){
            //如果指定路徑不存在,回傳false
            return realpath($file_enc);
        }else{
            return $file_enc;
        }
    }
?>
