<?php
//-------------------------------------------------------
//函式: get_member_title()
//用途: 提取聊書頭銜
//-------------------------------------------------------

    function get_member_title($user_id,$arry_conn){
    //---------------------------------------------------
    //函式: get_member_title()
    //用途: 提取聊書頭銜
    //---------------------------------------------------
    //$user_id      使用者主索引
    //$arry_conn    資料庫連線資訊陣列
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($user_id)||(int)($user_id)===0){
                $err='GET_MEMBER_TITLE:NO USER_ID';
                die($err);
            }else{
                $user_id=(int)($user_id);
            }

        //-----------------------------------------------
        //外掛函式檔
        //-----------------------------------------------

            if((!function_exists("mysql_prep"))&&(!function_exists("db_result"))){
                if(false===@include_once($APP_ROOT.'lib/php/db/code.php')){
                    return false;
                }
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
                    $err ='GET_MEMBER_TITLE:CONNECT FAIL';
                    die($err);
                }

        //-----------------------------------------------
        //SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `title_name`
                FROM `mssr_forum`.`dev_member_title`
                    INNER JOIN `user`.`member` ON
                    `mssr_forum`.`dev_member_title`.`title_sex`=`user`.`member`.`sex`
                WHERE `title_score` <= (
                    SELECT `score`
                    FROM `mssr_forum`.`dev_member_score`
                    WHERE `u_id`={$user_id}
                    LIMIT 1
                )
                    AND `user`.`member`.`uid`={$user_id}
                ORDER BY `title_score` DESC
                LIMIT 1
            ";
            $db_results=db_result($conn_type='pdo',$conn,$sql,$arry_limit=array(),$arry_conn);

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            $conn=NULL;

            return $db_results;
    }
?>