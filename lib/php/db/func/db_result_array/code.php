<?php
//-------------------------------------------------------
//函式: db_result_array()
//用途: 傳回查詢結果集陣列
//日期: 2011年11月1日
//作者: jeff@max-life
//-------------------------------------------------------

    function db_result_array($conn='',$arry_conn,$tbl_name,$fields,$where,$order){
    //---------------------------------------------------
    //傳回查詢結果集陣列
    //---------------------------------------------------
    //$conn         資料庫連結物件
    //$arry_conn    資料庫資訊陣列
    //$tbl_name     表格名稱
    //$fields       欄位名稱陣列,若不指定,傳回全部
    //$where        WHERE子句,不含WHERE關鍵字,若不指定,則不篩選
    //$order        ORDER子句,不含ORDER關鍵字,若不指定,則不排序
    //---------------------------------------------------

        //檢核參數
        if((!$arry_conn)||(empty($arry_conn))){
            $err='DB_RESULT_ARRAY:NO ARRY_CONN';
            die($err);
        }
        if(!$tbl_name){
            $err='DB_RESULT_ARRAY:NO TBL_NAME';
            die($err);
        }

        //資料庫資訊
        $db_host  =$arry_conn['db_host'];
        $db_user  =$arry_conn['db_user'];
        $db_pass  =$arry_conn['db_pass'];
        $db_name  =$arry_conn['db_name'];
        $db_encode=$arry_conn['db_encode'];


        //連結物件判斷
        $has_conn=false;

        if(!$conn){
            $has_conn=true;

            $err ='DB_RESULT_ARRAY:CONNECT FAIL';

            $conn=@mysql_connect($db_host,$db_user,$db_pass)
            or die($err);
        }else{
            $has_conn=false;
        }

        //選取資料庫
        $err='DB_RESULT_ARRAY:SELECT DB FAIL';
        @mysql_select_db($db_name,$conn) or
        die($err);

        //選取資料庫編碼
        $err='DB_RESULT_ARRAY:SELECT DB ENCODE FAIL';
        @mysql_set_charset($db_encode,$conn) or
        die($err);

        //有沒有指定欄位陣列
        if(isset($fields)&&!empty($fields)){

            $fields=implode(',',$fields);
        }else{
            $fields='*';
        }

        //有沒有指定WHERE
        if(!isset($where)||trim($where)==''){
            $sql ='';
            $sql.='SELECT '.$fields.' FROM '.$tbl_name.' ';
            $sql.='WHERE 1=1';

            if(isset($order)&&trim($order)!=''){
                $sql.=' ORDER BY '.$order.';';
            }else{
                $sql.=';';
            }

        }else{
            $sql ='';
            $sql.='SELECT '.$fields.' FROM '.$tbl_name.' ';
            $sql.='WHERE'.' '.$where;

            if(isset($order)&&trim($order)!=''){
                $sql.=' ORDER BY '.$order.';';
            }else{
                $sql.=';';
            }
        }
        //echo $sql;

        //擷取資料
        if(($result=@mysql_query($sql,$conn))===false){

            $err='DB_RESULT_ARRAY:QUERY FAIL';
            mysql_close($conn);

            die($err);
        }

        //資料集陣列處理
        $arr=array();

            //列處理
            $i=0;
            while($row=mysql_fetch_assoc($result)){

                $tmp=array();

                //欄處理
                foreach($row as $fld_name=>$fld_value){

                    $tmp[$fld_name]=$fld_value;
                }
                $arr[]=$tmp;
                $i++;
            }

        //回傳陣列
        return $arr;

        //釋放資源
        mysql_free_result($result);
        if($has_conn==true){
            mysql_close($conn);
        }
    }
?>