<?php
//-------------------------------------------------------
//函式: get_blacklist_article_school()
//用途: 提取學校文章黑名單資訊
//-------------------------------------------------------

    function get_blacklist_article_school($school_code,$article_id,$arry_conn){
    //---------------------------------------------------
    //函式: get_blacklist_article_school()
    //用途: 提取學校文章黑名單資訊
    //---------------------------------------------------
    //$school_code  學校代號
    //$article_id   文章主索引
    //$arry_conn    資料庫連線資訊陣列
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($school_code)||(trim($school_code)==='')){
                $err='GET_BLACKLIST_ARTICLE_SCHOOL:NO SCHOOL_CODE';
                die($err);
            }else{
                $school_code=trim($school_code);
            }

            if(!isset($article_id)||(int)($article_id)===0){
                $err='GET_BLACKLIST_ARTICLE_SCHOOL:NO ARTICLE_ID';
                die($err);
            }else{
                $article_id=(int)($article_id);
            }

            if(!isset($arry_conn)||(empty($arry_conn))){
                $err='GET_BLACKLIST_ARTICLE_SCHOOL:NO ARRY_CONN';
                die($err);
            }

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //-------------------------------------------
            //通用
            //-------------------------------------------

                //資料庫資訊
                $db_host  =$arry_conn['db_host'];
                $db_user  =$arry_conn['db_user'];
                $db_pass  =$arry_conn['db_pass'];
                $db_name  =$arry_conn['db_name'];
                $db_encode=$arry_conn['db_encode'];

                //建立連線
                $conn_info="mysql".":host={$db_host}".";dbname={$db_name}";
                $options = array(
                    PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,           //設置錯誤提示，只獲取代碼
                    PDO::ATTR_CASE,              PDO::CASE_NATURAL,             //列名按照原始的方式
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode}"    //設置語系
                );

                try{
                    $conn=@new PDO($conn_info, $db_user, $db_pass,$options);
                }catch(PDOException $e){
                    $err ='GET_BLACKLIST_ARTICLE_SCHOOL:CONNECT FAIL';
                    die($err);
                }

        //-----------------------------------------------
        //SQL
        //-----------------------------------------------

            $school_code=addslashes($school_code);
            $db_results=array();
            $sql="
                SELECT `mssr_forum`.`mssr_forum_blacklist_article_school`.`article_id`
                FROM  `mssr_forum`.`mssr_forum_blacklist_article_school`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_blacklist_article_school`.`school_code`= '{$school_code  }'
                    AND `mssr_forum`.`mssr_forum_blacklist_article_school`.`article_id` =  {$article_id   }
                LIMIT 1
            ";
            $result=$conn->query($sql) or die('DB_RESULT:QUERY FAIL');
            if(($result->rowCount())!==0){
                while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                    $db_results[]=$arry_row;
                }
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $db_results;
    }
?>