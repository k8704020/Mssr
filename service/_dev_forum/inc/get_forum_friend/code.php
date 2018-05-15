<?php
//-------------------------------------------------------
//函式: get_forum_friend()
//用途: 提取聊書好友資訊
//-------------------------------------------------------

    function get_forum_friend($user_id,$friend_id=0,$arry_conn){
    //---------------------------------------------------
    //函式: get_forum_friend()
    //用途: 提取聊書好友資訊
    //---------------------------------------------------
    //$user_id      使用者主索引
    //$friend_id    好友主索引
    //$arry_conn    資料庫連線資訊陣列
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($user_id)||(int)($user_id)===0){
                $err='GET_FORUM_FRIEND:NO USER_ID';
                die($err);
            }else{
                $user_id=(int)($user_id);
            }

            if(!isset($friend_id)){
                $err='GET_FORUM_FRIEND:NO FRIEND_ID';
                die($err);
            }else{
                $friend_id=(int)($friend_id);
            }

            if(!isset($arry_conn)||(empty($arry_conn))){
                $err='GET_FORUM_FRIEND:NO ARRY_CONN';
                die($err);
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
                    $err ='GET_FORUM_FRIEND:CONNECT FAIL';
                    die($err);
                }

        //-----------------------------------------------
        //SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_friend`.`create_by`,
                    `mssr_forum`.`mssr_forum_friend`.`user_id`,
                    `mssr_forum`.`mssr_forum_friend`.`friend_id`,
                    `mssr_forum`.`mssr_forum_friend`.`friend_state`
                FROM `mssr_forum`.`mssr_forum_friend`
                WHERE 1=1
                    AND (
                        `mssr_forum`.`mssr_forum_friend`.`user_id`  ={$user_id}
                        OR
                        `mssr_forum`.`mssr_forum_friend`.`friend_id`={$user_id}
                    )
            ";
            if($friend_id!==0)$sql.="
                AND (
                    `mssr_forum`.`mssr_forum_friend`.`user_id`  ={$friend_id}
                    OR
                    `mssr_forum`.`mssr_forum_friend`.`friend_id`={$friend_id}
                )
            ";
            $db_results=db_result($conn_type='pdo',$conn,$sql,$arry_limit=array(),$arry_conn);

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $db_results;
    }
?>