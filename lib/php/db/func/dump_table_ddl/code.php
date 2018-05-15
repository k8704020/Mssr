<?php
//-------------------------------------------------------
//函式: dump_table_ddl()
//用途: 資料表結構傾倒
//日期: 2012年1月10日
//作者: jeff@max-life
//-------------------------------------------------------

    function dump_table_ddl($arry_conn,$tbl_name){
    //---------------------------------------------------
    //資料表結構傾倒
    //---------------------------------------------------
    //$arry_conn    資料庫連線資訊陣列
    //$tbl_name     表格名稱
    //
    //本函式會傳回該資料表的結構資料(DDL),型態是字串
    //---------------------------------------------------


        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($arry_conn)||(empty($arry_conn))){
                return false;
            }
            if(!isset($tbl_name)||(trim($tbl_name)=='')){
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
                die("dump_table_ddl:db connect fail!");
            }

            if(false===(@mysql_set_charset($db_encode))){
                die("dump_table_ddl:db set charset fail!");
            }

            if(false===(@mysql_select_db($db_name))){
                die("dump_table_ddl:db select db fail!");
            }

        //-----------------------------------------------
        //設定
        //-----------------------------------------------
        //SHOW CREATE TABLE `資料表名稱`;
        //
        //Table       欄位: 資料表名稱
        //Create Table欄位: 資料表DML
        //-----------------------------------------------


            //修改MySQL執行時間,值為-1表示不設限
            ini_set('mysql.connect_timeout',-1);

            //修改php執行時間,值為0表示不設限
            set_time_limit(0);

            //鎖定資料表,不允許寫入
            //@mysql_query("LOCK TABLES `{$tbl_name}` WRITE;");

            //查詢
            $sql ="SHOW CREATE TABLE `{$tbl_name}`"." ";

            $result=@mysql_query($sql,$conn);

            if(false===($result=@mysql_query($sql,$conn))){
                die("dump_table_ddl:table not found!");
                @mysql_close($conn);
            }

            $name=mysql_result($result,0,'Table');
            $sql =mysql_result($result,0,'Create Table');
            //echo "{$name}"."<p>";
            //echo "{$sql }"."<p>";

        //-----------------------------------------------
        //釋放資源
        //-----------------------------------------------
            @mysql_free_result($results);
            //@mysql_query("UNLOCK TABLES;");
            @mysql_close($conn);

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------
            return $sql;
    }
?>