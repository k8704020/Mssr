<?php
//-------------------------------------------------------
//函式: array_out()
//用途: 陣列內容輸出
//日期: 2012年9月21日
//作者: pg_team@max-life
//-------------------------------------------------------

    function array_out($arry,$type='html'){
    //---------------------------------------------------
    //函式: array_out()
    //用途: 陣列內容輸出
    //---------------------------------------------------
    //$arry 內容陣列
    //$type 回傳類型,html | text ,html 預設
    //---------------------------------------------------

        //-----------------------------------------------
        //檢驗參數
        //-----------------------------------------------
            if(!isset($arry)||empty($arry)){
                return '';
            }

            if(!isset($type)||trim($type)===''){
                $type='html';
            }else{
                if(!in_array($type,array('html','text'))){
                    $type='html';
                }
            }

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            switch($type){
                case 'html':

                    //僅去除右邊空白
                    $arry=array_map("rtrim",$arry);
                    foreach($arry as $inx=>$item){
                        if($item==''){
                            $arry[$inx]="<p>";
                        }else{
                            $item=strtr($item,array("\t"=>"&nbsp;","\s"=>"&nbsp;"," "=>"&nbsp;"));
                            $arry[$inx]=$item."<br/>";
                        }
                    }
                    $out=implode("",$arry);

                    break;

                case 'text':

                //僅去除右邊空白
                    $arry=array_map("rtrim",$arry);
                    foreach($arry as $inx=>$item){
                        $arry[$inx].="\r\n";
                    }
                    $out=implode("",$arry);

                    break;
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------
            return $out;
    }
?>