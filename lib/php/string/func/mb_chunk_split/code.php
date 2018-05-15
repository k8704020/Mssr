<?php
//-------------------------------------------------------
//函式: mb_chunk_split()
//用途: 實作多字元版的chunk_split()函式
//日期: 2012年3月15日
//作者: jeff@max-life
//-------------------------------------------------------

    function mb_chunk_split($body,$chunklen,$end="\r\n",$encode=""){
    //---------------------------------------------------
    //實作多字元版的chunk_split()函式
    //---------------------------------------------------
    //$body         欲分割的來源字串
    //$chunklen     欲分割長度
    //$end          欲串接的字元,預設 \r\n 換行字元
    //$encode       字串編碼,預設 UTF-8
    //---------------------------------------------------

        //參數檢驗
        if(!isset($body)||trim($body)==''){
            return false;
        }
        if(!isset($chunklen)||!is_numeric($chunklen)){
            return false;
        }else{
           $chunklen=(int)$chunklen;
        }
        if(!isset($end)||trim($end)==''){
           $end="\r\n" ;
        }
        if(!isset($encode)||trim($encode)==''){
           $encode=mb_internal_encoding();
        }

        //處理
        $cno =ceil(mb_strlen($body,$encode)/$chunklen);
        $arry=array();
        for($i=0;$i<$cno;$i++){
            $arry[]=mb_substr($body,$i*($chunklen),$chunklen,$encode);
        }
        //$arry[]='';

        $body=implode($end,$arry);

        return $body;
    }
?>