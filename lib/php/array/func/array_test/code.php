<?php
//-------------------------------------------------------
//函式: array_test()
//用途: 陣列測試工具函式
//日期: 2011年12月1日
//作者: jeff@max-life
//-------------------------------------------------------

    function array_test($arr,$ch='is_array'){
    //---------------------------------------------------
    //陣列測試工具函式
    //---------------------------------------------------
    //is_array      判斷非陣列
    //is_empty      判斷空陣列
    //is_all_value  判斷陣列元素均有值
    //is_all_empty  判斷陣列元素均為空值
    //is_part_empty 判斷陣列元素部分為空值

        switch(strtolower($ch)){
            case 'is_array':        //非陣列

                if(is_array($arr)){
                    return true;
                }else{
                    return false;
                }
                break;
            case 'is_empty':        //空陣列

                if(empty($arr)){
                    return true;
                }else{
                    return false;
                }
                break;
            case 'is_all_value':    //陣列元素均有值

                if(!is_array($arr)||empty($arr)){
                    return false;
                }

                $cNo=0;
                foreach($arr as $key=>$val){
                    if(trim($arr[$key])==""){
                        $cNo++;
                    }
                }

                if($cNo==0){
                    return true;
                }else{
                    return false;
                }
                break;
            case 'is_all_empty':    //陣列元素均為空值

                if(!is_array($arr)||empty($arr)){
                    return false;
                }

                $cNo=0;
                foreach($arr as $key=>$val){
                    if(trim($arr[$key])==""){
                        $cNo++;
                    }
                }

                if($cNo==count($arr)){
                    return true;
                }else{
                    return false;
                }
                break;
            case 'is_part_empty':   //陣列元素部分為空值

                if(!is_array($arr)||empty($arr)){
                    return false;
                }

                $cNo=0;
                foreach($arr as $key=>$val){
                    if(trim($arr[$key])==""){
                        $cNo++;
                    }
                }

                if(($cNo>=1)&&($cNo<count($arr))){
                    return true;
                }else{
                    return false;
                }
                break;
        }
    }
?>