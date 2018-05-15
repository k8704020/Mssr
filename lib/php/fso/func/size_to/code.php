<?php
//-------------------------------------------------------
//函式: size_to()
//用途: 單位互轉
//日期: 2011年12月12日
//作者: jeff@max-life
//-------------------------------------------------------

    function size_to($val,$sunit,$tunit){
    //---------------------------------------------------
    //單位互轉
    //---------------------------------------------------
    //$val   值(bytes)
    //$sunit 來源單位 {'bytes'|'kb'|'mb'|'gb'|'tb'}
    //$tunit 目的單位 {'bytes'|'kb'|'mb'|'gb'|'tb'}
    //---------------------------------------------------

        if(!isset($val)||(trim($val)=='')||!is_numeric($val)){
            return false;
        }
        if(!isset($sunit)||(trim($sunit)=='')){
            return false;
        }
        if(!isset($tunit)||(trim($tunit)=='')){
            return false;
        }

        $size=1024;
        $arry_unit=array('bytes','kb','mb','gb','tb');

        if(!in_array(strtolower($sunit),$arry_unit)){
            return false;
        }
        if(!in_array(strtolower($tunit),$arry_unit)){
            return false;
        }

        //先轉成bytes
        switch(strtolower($sunit)){
            case 'bytes':
                $val=$val*pow($size,0);
                $val=sprintf("%.2f",round($val,2));
                break;
            case 'kb':
                $val=$val*pow($size,1);
                $val=sprintf("%.2f",round($val,2));
                break;
            case 'mb':
                $val=$val*pow($size,2);
                $val=sprintf("%.2f",round($val,2));
                break;
            case 'gb':
                $val=$val*pow($size,3);
                $val=sprintf("%.2f",round($val,2));
                break;
            case 'tb':
                $val=$val*pow($size,4);
                $val=sprintf("%.2f",round($val,2));
                break;
        }

        //再轉成其他單位
        switch(strtolower($tunit)){
            case 'bytes':
                $val=$val/pow($size,0);
                $val=sprintf("%.2f",round($val,2));
                break;
            case 'kb':
                $val=$val/pow($size,1);
                $val=sprintf("%.2f",round($val,2));
                break;
            case 'mb':
                $val=$val/pow($size,2);
                $val=sprintf("%.2f",round($val,2));
                break;
            case 'gb':
                $val=$val/pow($size,3);
                $val=sprintf("%.2f",round($val,2));
                break;
            case 'tb':
                $val=$val/pow($size,4);
                $val=sprintf("%.2f",round($val,2));
                break;
        }

        //回傳
        return $val;
    }
?>