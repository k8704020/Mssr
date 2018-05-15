<?php
//-------------------------------------------------------
//函式: dump_table()
//用途: 資料表資料傾倒
//日期: 2012年1月10日
//作者: jeff@max-life
//-------------------------------------------------------

    function dump_table($arry_conn,$tbl_name){
    //---------------------------------------------------
    //資料表資料傾倒
    //---------------------------------------------------
    //$arry_conn    資料庫連線資訊陣列
    //$tbl_name     表格名稱
    //
    //本函式會傳回該資料表的INSERT INTO 資料,型態是字串
    //格式如下..
    //
    //INSERT INTO 資料表 (欄1,欄2,欄3,...欄N)
    //VALUES
    //(值1,值2,值3,...,值N),
    //(值1,值2,值3,...,值N),
    //(值1,值2,值3,...,值N);
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
                die("dump_table:db connect fail!");
            }

            if(false===(@mysql_set_charset($db_encode))){
                die("dump_table:db set charset fail!");
            }

            if(false===(@mysql_select_db($db_name))){
                die("dump_table:db select db fail!");
            }

        //-----------------------------------------------
        //設定
        //-----------------------------------------------

            //修改MySQL執行時間,值為-1表示不設限
            ini_set('mysql.connect_timeout',-1);

            //修改php執行時間,值為0表示不設限
            set_time_limit(0);

            //鎖定資料表,不允許寫入
            //@mysql_query("LOCK TABLES `{$tbl_name}` WRITE;");

            //查詢
            $sql="SELECT * FROM `{$tbl_name}` WHERE 1=1"." ";

            if(false===($results=@mysql_query($sql,$conn))){
                @mysql_close($conn);
                die("dump_table:db query fail!");
            }

            //資料筆數
            $numrows=mysql_num_rows($results);
            if($numrows===0){
                //無資料存在,回傳空字串
                return '';
                @mysql_free_result($results);
                @mysql_close($conn);
            }

        //-----------------------------------------------
        //欄位部分
        //-----------------------------------------------

            //列出欄位名稱
            $fld_nums =mysql_num_fields($results);
            $fld_names=array();
            for($i=0;$i<$fld_nums;$i++){
                $fld_name   =mysql_field_name($results,$i);
                $fld_names[]=$fld_name;
            }

            $fld_names="(`".implode("`,`",$fld_names)."`)";
            //echo "{$fld_names}"."<p>";

        //-----------------------------------------------
        //資料部分
        //-----------------------------------------------
            $fld_vals=array();  //儲存所有列
            $fld_val =array();  //儲存某列

            //處理
            while($result=mysql_fetch_assoc($results)){

                //列欄位值
                $fld_val=array();
                foreach($result as $key=>$val){
                    $val=addslashes($val);
                    array_push($fld_val,$val);
                }
                $fld_val="('".implode("','",$fld_val)."')";
                array_push($fld_vals,$fld_val);
            }

            //所有列
            $fld_vals=implode(",\r\n",$fld_vals);
            $fld_vals.=";";
            //echo "<pre>$fld_vals</pre>";

        //-----------------------------------------------
        //建立sql
        //-----------------------------------------------
            $nl="\r\n";
            $backup="";
            $backup.="INSERT INTO `{$tbl_name}` {$fld_names} VALUES {$nl}"." ";
            $backup.="{$fld_vals}"." ";

            //echo "$backup";

        //-----------------------------------------------
        //釋放資源
        //-----------------------------------------------
            @mysql_free_result($results);
            //@mysql_query("UNLOCK TABLES;");
            @mysql_close($conn);

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------
            return $backup;
    }
?>