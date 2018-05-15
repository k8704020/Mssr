<?php
//-------------------------------------------------------
//函式: shuffle_savekey()
//用途: 打亂陣列元素,並保留鍵
//日期: 2011年12月1日
//作者: jeff@max-life
//-------------------------------------------------------

    function shuffle_savekey(&$arr){
    //---------------------------------------------------
    //打亂陣列元素,並保留鍵
    //---------------------------------------------------

        //重建陣列
        $dc=",";
        $arr_temp=array();
        foreach($arr as $key => $val){
            $arr_temp[]=$key.$dc.$val;
        }
        $arr=$arr_temp;

        //打亂陣列
        shuffle($arr);

        //還原陣列
        $arr_temp=array();
        foreach($arr as $item){
            $temp=explode($dc,$item);
            $key=$temp[0];
            $val=$temp[1];
            $arr_temp[$key]=$val;
        }
        $arr=$arr_temp;

        //回傳
        return $arr;
    }
?>