<?php
//-------------------------------------------------------
//函式: get_ip2nation()
//用途: ip國別資料陣列
//日期: 2013年1月23日
//作者: jeff@max-life
//-------------------------------------------------------

    function get_ip2nation($conn='',$ip,$arry_conn){
    //---------------------------------------------------
    //函式: get_ip2nation()
    //用途: ip國別資料陣列
    //---------------------------------------------------
    //參數
    //---------------------------------------------------
    //$conn         資料庫連結物件,若不指定,會自動開啟連結
    //$ip           ip,必須
    //$arry_conn    資料庫資訊陣列,必須
    //---------------------------------------------------
    //本函式,會傳回下列資訊的陣列
    //---------------------------------------------------
    //code            tw
    //iso_code_2      TW
    //iso_code_3      TWN
    //iso_country     Taiwan
    //country         Taiwan
    //lat             23.3
    //lon             121
    //---------------------------------------------------

        //檢核參數
        if(!isset($ip)||(trim($ip)==='')||!mb_eregi($exp="^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$",$ip)){
            $err='GET_IP2NATION:IP IS INVAILD';
            return array();
        }else{
            //內部ip
            $private_ips=array(
                '127',  //for 127.x.x.x
                '192',  //for 192.x.x.x
                '255'   //for 255.x.x.x
            );

            $_arry=explode(".",$ip);
            if(in_array($_arry[0],$private_ips)){
                return array(
                    trim('code       ')=>'01',
                    trim('iso_code_2 ')=>'',
                    trim('iso_code_3 ')=>'',
                    trim('iso_country')=>'',
                    trim('country    ')=>'Private',
                    trim('lat        ')=>'0',
                    trim('lon        ')=>'0'
                );
            }

            //超出範圍
            if(($_arry[1]>=255)||($_arry[2]>=255)||($_arry[3]>=255)){
                return array();
            }
        }

        if((!$arry_conn)||(empty($arry_conn))){
            $err='GET_IP2NATION:NO ARRY_CONN';
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
            $err ='GET_IP2NATION:CONNECT FAIL';
            $conn=@mysql_connect($db_host,$db_user,$db_pass)
            or die($err);
        }else{
            $has_conn=false;
        }

        //資料庫
        $err='GET_IP2NATION:SELECT DB ENCODE FAIL';
        @mysql_set_charset($db_encode,$conn) or
        die($err);

        $err='GET_IP2NATION:SELECT DB FAIL';
        @mysql_select_db($db_name,$conn) or
        die($err);

        $sql="
            -- 取得 國別代碼,國名
            -- 注意 ORDER BY,LIMIT 不可變更
            SELECT
                `c`.*
            FROM
                `sys_ip2nationcountries` AS `c`,
                `sys_ip2nation` AS `i`
            WHERE 1=1
                AND `i`.`ip` < INET_ATON('{$ip}')
                AND `c`.`code`=`i`.`country`
            ORDER BY
                `i`.`ip` DESC
            LIMIT 0,1
        ";
        //echo "{$sql}"."<br/>";

        //資料集
        $err='GET_IP2NATION:SELECT QUERY FAIL';
        $result=@mysql_query($sql,$conn) or
        die($err);

        $arry_result=array();
        if(mysql_num_rows($result)!==0){
            for($j=0;$j<mysql_num_fields($result);$j++){
                $filed_name =mysql_field_name($result,$j);
                $filed_value=mysql_result($result,0,$filed_name);
                $arry_result[$filed_name]=$filed_value;
            }
        }

        //釋放資源
        mysql_free_result($result);
        if($has_conn==true){
            mysql_close($conn);
        }

        //傳回資料集陣列
        return $arry_result;
    }
?>
