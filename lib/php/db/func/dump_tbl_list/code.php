<?php
//-------------------------------------------------------
//函式: dump_tbl_list()
//用途: 取回資料庫下所有資料表
//日期: 2012年1月10日
//作者: jeff@max-life
//-------------------------------------------------------

    function dump_tbl_list($arry_conn){
    //---------------------------------------------------
    //取回資料庫下所有資料表
    //---------------------------------------------------
    //$arry_conn    資料庫連線資訊陣列
    //
    //函式回傳型態是陣列
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($arry_conn)||(empty($arry_conn))){
                return false;
            }

        //-----------------------------------------------
        //設定
        //-----------------------------------------------

            $db_host  =$arry_conn['db_host'];
            $db_name  =$arry_conn['db_name'];
            $db_user  =$arry_conn['db_user'];
            $db_pass  =$arry_conn['db_pass'];
            $db_encode=$arry_conn['db_encode'];

            if(false===($conn=@mysql_connect($db_host,$db_user,$db_pass))){
                die("dump_tbl_list:db connect fail!");
            }

            if(false===(@mysql_set_charset($db_encode))){
                die("dump_tbl_list:db set charset fail!");
            }

            if(false===(@mysql_select_db($db_name))){
                die("dump_tbl_list:db select db fail!");
            }

        //-----------------------------------------------
        //查詢
        //-----------------------------------------------
        //SHOW TABLES;  顯示所有表格

            $sql ="SHOW TABLES;"." ";

            $arry_tbls=array();

            $results=@mysql_query($sql,$conn);
            while($result=mysql_fetch_array($results)){

                $tbl_name=$result[0];
                $arry_tbls[]=$tbl_name;
            }
            //echo "<pre>";
            //print_r($arry_tbls);
            //echo "</pre>";

        //-----------------------------------------------
        //釋放資源
        //-----------------------------------------------
            @mysql_free_result($results);
            @mysql_close($conn);

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------
            return $arry_tbls;
    }
?>