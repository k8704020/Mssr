<?php
//-------------------------------------------------------
//函式: array_json2()
//用途: 二維陣列轉json
//日期: 2011年11月28日
//作者: jeff@max-life
//-------------------------------------------------------

    function array_json2($arry){
    //---------------------------------------------------
    //二維陣列轉json
    //---------------------------------------------------
    //$arry 陣列
    //
    //註:本函式會自動將陣列的鍵與值予以脫序
    //---------------------------------------------------

        //參數
        if(!isset($arry)||!is_array($arry)||empty($arry)){
            return '{}';
        }

        //解析
        $tmp0=array();
        foreach($arry as $key0=>$arry0){//第一層

            if(is_array($arry0)){
            //第一層值是陣列

                $tmp1=array();
                foreach($arry0 as $key1=>$val1){//第二層
                    if(is_numeric($val1)){
                        //格式:'key':val
                        $key1="'".addslashes($key1)."'";
                        $val1="".addslashes($val1)."";
                        $tmp1[]=$key1.':'.$val1;
                    }else{
                        //格式:'key':'val'
                        $key1="'".addslashes($key1)."'";
                        $val1="'".addslashes($val1)."'";
                        $tmp1[]=$key1.':'.$val1;
                    }
                }
                //格式:'key0':{'key1':'val1','key2':2,..}
                $tmp0[]="'".addslashes($key0)."'".":"."{".implode(",",$tmp1)."}";
            }else{
            //第一層值不是陣列

                $key0="'".addslashes($key0)."'";
                $val0="{}";
                $tmp0[]=$key0.':'.$val0;
            }
        }
        //輸出
        $tmp0="{".implode(",",$tmp0)."}";
        return $tmp0;
    }
?>