<?php
//-------------------------------------------------------
//函式: mysql_prep()
//用途: SQL脫序函式
//日期: 2011年11月8日
//作者: jeff@max-life
//-------------------------------------------------------

    function mysql_prep($val){
    //---------------------------------------------------
    //SQL脫序函式
    //---------------------------------------------------
    //$val 值
    //---------------------------------------------------

        //參數
        if(trim($val)==''){
            return $val;
        }

        //決定脫序函式總類
        if(function_exists('mysql_real_escape_string')){
            $escape='mysql_real_escape_string';
        }else{
            $escape='addslashes';
        }

        //判斷是否有啟用magic_quotes
        if(get_magic_quotes_gpc()){
            $val=stripslashes($val);

            //避免mysql連結未建立
            if(@$escape($val)){
                //echo '有mysql連結'.'<br/>';
                return $escape($val);
            }else{
                //echo '無mysql連結'.'<br/>';
                return addslashes($val);
            }
        }else{
            //避免mysql連結未建立
            if(@$escape($val)){
                //echo '有mysql連結'.'<br/>';
                return $escape($val);
            }else{
                //echo '無mysql連結'.'<br/>';
                return addslashes($val);
            }
        }
    }
?>