<?php
//-------------------------------------------------------
//函式: mutiple_query()
//用途: 複合查詢
//日期: 2012年5月3日
//作者: jeff@max-life
//-------------------------------------------------------

    function mutiple_query($arr){
    //---------------------------------------------------
    //複合查詢
    //---------------------------------------------------
    //$arr  複合查詢設定陣列
    //
    //回傳結果,如下所示
    //
    //$arry_query=array(
    //    'query_fields'=>查詢欄位陣列,
    //    'query_sql'   =>查詢敘述句
    //);
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($arr)||empty($arr)){
                $arry_query=array(
                    'query_fields'=>array(),
                    'query_sql'   =>''
                );
                return $arry_query;
            }

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //偵測是否有mysql連線
            $is_mysql_conn=(@mysql_ping())?true:false;

            //脫序函式
            if($is_mysql_conn){
                $query_sql_escape='mysql_real_escape_string';
            }else{
                $query_sql_escape='addslashes';
            }

        //-----------------------------------------------
        //處理
        //-----------------------------------------------
            $query_sql   ='';
            $query_fields=array();

            foreach($arr as $key => $val){
                $n=$val['n'];   //名稱
                $v=$val['v'];   //值
                $c=$val['c'];   //類型

                //判斷值,是不是陣列
                if(is_array($v)){
                //值,是陣列
                    if($c=='between'){

                        //有值才串接
                        if(!empty($v)){
                            $s=(int)($v[0]);
                            $e=(int)($v[1]);

                            if($s>$e){
                                $tmp=$s;
                                $s=$e;
                                $e=$tmp;
                            }

                            $query_sql.="AND {$key} between {$s} AND {$e} ";

                            //查詢欄位,顯示用
                            $query_fields[$key]=array(
                                'sql'=>"AND {$key} between {$s} AND {$e} ",
                                'text'=>"{$n}介於{$s}至{$e}"
                            );
                        }
                    }else if($c=='date-between'){
                        //有值才串接
                        if(!empty($v)){

                            //可轉timestamp才串接
                            $re='/^\d{4}-\d{1,2}-\d{1,2}$/i';
                            if(preg_match($re,trim($v[0]))&&preg_match($re,trim($v[1]))){
                                $s=strtotime($query_sql_escape($v[0]));
                                $e=strtotime($query_sql_escape($v[1]));

                                if(($e!==false)&&($e!==false)){
                                    if($s>$e){
                                        $tmp=$s;
                                        $s=$e;
                                        $e=$tmp;
                                    }

                                    $_syear =date("Y",$s);
                                    $_smonth=date("m",$s);
                                    $_sday  =date("d",$s);
                                    $_eyear =date("Y",$e);
                                    $_emonth=date("m",$e);
                                    $_eday  =date("d",$e);

                                    $query_sql.="AND DATE({$key}) between '{$_syear}-{$_smonth}-{$_sday}' AND '{$_eyear}-{$_emonth}-{$_eday}' ";

                                    //查詢欄位,顯示用
                                    $query_fields[$key]=array(
                                        'sql'=>"AND DATE({$key}) between '{$_syear}-{$_smonth}-{$_sday}' AND '{$_eyear}-{$_emonth}-{$_eday}' ",
                                        'text'=>"{$n}介於{$_syear}-{$_smonth}-{$_sday}至{$_eyear}-{$_emonth}-{$_eday}"
                                    );
                                }
                            }
                        }
                    }else if($c=='timestamp-between'){
                        //有值才串接
                        if(!empty($v)){

                            //可轉timestamp才串接
                            $re='/^\d{4}-\d{1,2}-\d{1,2}$/i';
                            if(preg_match($re,trim($v[0]))&&preg_match($re,trim($v[1]))){
                                $s=strtotime($query_sql_escape($v[0]));
                                $e=strtotime($query_sql_escape($v[1]));

                                if(($e!==false)&&($e!==false)){
                                    if($s>$e){
                                        $tmp=$s;
                                        $s=$e;
                                        $e=$tmp;
                                    }

                                    $query_sql.="AND UNIX_TIMESTAMP({$key}) between {$s} AND {$e} ";

                                    //查詢欄位,顯示用
                                    $query_fields[$key]=array(
                                        'sql'=>"AND UNIX_TIMESTAMP({$key}) between {$s} AND {$e} ",
                                        'text'=>"{$n}介於{$s}至{$e}"
                                    );
                                }
                            }
                        }
                    }else if($c=='bdyear-between'){
                        //有值才串接
                        if(!empty($v)){

                            //可轉timestamp才串接
                            $re='/^\d{1,2}$/i';
                            if(preg_match($re,trim($v[0]))&&preg_match($re,trim($v[1]))){

                                $s=$query_sql_escape($v[0]);
                                $e=$query_sql_escape($v[1]);
                                //echo $s.'<br/>';
                                //echo $e.'<br/>';

                                if(($e!==false)&&($e!==false)){
                                    if($s<$e){
                                        $tmp=$s;
                                        $s=$e;
                                        $e=$tmp;
                                    }

                                    //幾歲轉出生年
                                    $year=date("Y",time());
                                    $s=$year-$s;
                                    $e=$year-$e;

                                    $query_sql.="AND {$key} between {$s} AND {$e} ";

                                    //查詢欄位,顯示用
                                    $query_fields[$key]=array(
                                        'sql'=>"AND {$key} between {$s} AND {$e} ",
                                        'text'=>"{$n}介於{$s}至{$e}"
                                    );
                                }
                            }
                        }
                    }else if($c=='city_region'){

                        if(!empty($v)){
                        //縣市鄉鎮

                            //欄位值
                            $city_val  =trim($v['city']);
                            $region_val=trim($v['region']);

                            //欄位名稱
                            $tmp=explode(',',$key);
                            $city_key  =trim($tmp[0]);
                            $region_key=trim($tmp[1]);

                            $sql ="";   //查詢用
                            $text="";   //顯示用

                            if($city_val!='請選擇'){
                            //有選縣市
                                $tmp=explode(',',$n);

                                $query_sql.="AND {$city_key}='{$city_val}' ";
                                $sql ="AND {$city_key}='{$city_val}' ";
                                $text=$tmp[0]."為{$city_val}";

                                if($region_val!='請選擇'){
                                //有選鄉鎮

                                    $query_sql.="AND {$region_key}='{$region_val}' ";
                                    $sql .="AND {$region_key}='{$region_val}' ";
                                    $text.=",且".$tmp[1]."為{$region_val}的.";
                                }

                                //查詢欄位,顯示用
                                $query_fields[$key]=array(
                                    'sql' =>$sql,
                                    'text'=>$text
                                );
                            }
                        }
                    }else if($c=='in'){

                        //檢核是否有值
                        //echo "<pre>";
                        //print_r($v);
                        //echo "</pre>";

                        if(!empty($v)){

                            foreach($v as $tmp_key=>$tmp_val){
                                $v[$tmp_key]=$query_sql_escape($tmp_val);
                            }

                            //有值才串接
                            $in="('".implode("','",$v)."')";
                            $query_sql.="AND {$key} in {$in} ";

                            //查詢欄位,顯示用
                            $query_fields[$key]=array(
                                'sql'=>"AND {$key} in {$in} ",
                                'text'=>"{$n}在{$in}"
                            );
                        }
                    }
                }else{
                //值,不是陣列

                    //有值才串接
                    if($v!==""){
                        $v=$query_sql_escape(trim($v));

                        if($c=='like'){
                            $query_sql.="AND {$key} LIKE '" . $v . "%' ";

                            $query_fields[$key]=array(
                                'sql'=>"AND {$key} LIKE '" . $v . "%' ",
                                'text'=>"{$n}裡有'{$v}'的"
                            );
                        }elseif($c=='equal'){
                            $query_sql.="AND {$key} ='" . $v . "' ";

                            $query_fields[$key]=array(
                                'sql'=>"AND {$key} ='" . $v . "' ",
                                'text'=>"{$n}裡等於'{$v}'的"
                            );
                        }elseif($c=='isnotnull'){
                            if(intval($v)===1){
                                $query_sql.="AND {$key} IS NOT NULL ";

                                $query_fields[$key]=array(
                                    'sql'=>"AND {$key} IS NOT NULL ",
                                    'text'=>"{$n}裡是有值的"
                                );
                            }else{
                                $query_sql.="AND {$key} IS NULL ";

                                $query_fields[$key]=array(
                                    'sql'=>"AND {$key} IS NULL ",
                                    'text'=>"{$n}裡是無值的"
                                );
                            }
                        }elseif($c=='isnull'){
                            if(intval($v)===1){
                                $query_sql.="AND {$key} IS NULL ";

                                $query_fields[$key]=array(
                                    'sql'=>"AND {$key} IS NULL ",
                                    'text'=>"{$n}裡是無值的"
                                );
                            }else{
                                $query_sql.="AND {$key} IS NOT NULL ";

                                $query_fields[$key]=array(
                                    'sql'=>"AND {$key} IS NOT NULL ",
                                    'text'=>"{$n}裡是有值的"
                                );
                            }
                        }elseif($c=='hasval'){
                            if(intval($v)===1){
                                $query_sql.="AND TRIM({$key})<>'' ";

                                $query_fields[$key]=array(
                                    'sql'=>"AND TRIM({$key})<>'' ",
                                    'text'=>"{$n}裡是有內容值的"
                                );
                            }else{
                                $query_sql.="AND TRIM({$key})='' ";

                                $query_fields[$key]=array(
                                    'sql'=>"AND TRIM({$key})='' ",
                                    'text'=>"{$n}裡是無內容值的"
                                );
                            }
                        }else{
                            $query_sql.="AND {$key} ='" . $v . "' ";

                            $query_fields[$key]=array(
                                'sql'=>"AND {$key} ='" . $v . "' ",
                                'text'=>"{$n}裡等於'{$v}'的"
                            );
                        }
                    }
                }
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------
            $arry_query=array(
                'query_fields'=>$query_fields,
                'query_sql'   =>$query_sql
            );
            return $arry_query;
    }
?>