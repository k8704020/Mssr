<?php
//-------------------------------------------------------
//函式: array_js2()
//用途: 2維陣列轉js陣列
//日期: 2011年10月31日
//作者: jeff@max-life
//-------------------------------------------------------

    function array_js2($arrys){
    //---------------------------------------------------
    //PHP2維陣列轉js陣列
    //---------------------------------------------------

        if(!isset($arrys)||!is_array($arrys)){
            return '';
        }

        //處理陣列  第一層
        $temps=array();
        foreach($arrys as $arry){

            if(is_array($arry)&&!empty($arry))
            {
            //處理陣列  第二層
                $temp=array();
                foreach($arry as $key=>$val){
                    if(!is_numeric($val)){
                        $temp[]="'{$val}'";
                    }else{
                        $temp[]="{$val}";
                    }
                }
            }else{
                $temp="";

                if(!is_numeric($arry)){
                    //non-numeric

                    if(is_array($arry)&&empty($arry)){
                        //empty array
                        $temp="[]";
                    }else{
                        //string
                        $temp="'{$arry}'";
                    }
                }else{
                    //numeric
                    $temp="{$arry}";
                }
            }
            $temps[]=$temp;
        }

        //格式化
        $nl ="\r\n";
        $tab="\t";
        foreach($temps as $key=>$val){
            if(is_array($temps[$key])){
                $temps[$key]='['.implode(",",$val).']';
            }
        }
        $temps=implode(",{$nl}",$temps);

        $out ="";
        $out.="[".$nl;
        $out.=$temps.$nl;
        $out.="]".";".$nl;

        //回傳
        return $out;
    }
?>