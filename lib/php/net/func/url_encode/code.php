<?php
//-------------------------------------------------------
//函式: url_encode()
//用途: 建立參數經 urlencode 處理過的網址
//日期: 2011年12月1日
//作者: jeff@max-life
//-------------------------------------------------------

    function url_encode($url='',$arry_arg=array()){
    //---------------------------------------------------
    //建立參數經 urlencode 處理過的網址
    //---------------------------------------------------
    //url       網址
    //arry_arg  參數陣列
    //
    //假設: url =01.php
    //假設: 參數陣列如下
    //      $arry_arg['id']  =1
    //      $arry_arg['name']='姓名'
    //
    //則本函式傳回
    //  01.php?id=1&name=%E5%A7%93%E5%90%8D
    //---------------------------------------------------

        if(!isset($arry_arg)||empty($arry_arg)){
            return $url;
        }else{
            $tmp=array();
            foreach($arry_arg as $key=>$val){
                $val=urlencode(trim($val));
                $tmp[]=$key.'='.$val;
            }
            $tmp=implode('&',$tmp);

            return $url.'?'.$tmp;
        }
    }
?>