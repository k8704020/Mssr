<?php
//-------------------------------------------------------
//函式: add_forum_article_view_log()
//用途: 新增文章瀏覽次數log
//-------------------------------------------------------

    function add_forum_article_view_log($user_id,$article_id,$arry_conn){
    //---------------------------------------------------
    //函式: add_forum_article_view_log()
    //用途: 新增文章瀏覽次數log
    //---------------------------------------------------
    //$user_id      使用者主索引
    //$article_id   文章主索引
    //$arry_conn    資料庫連線資訊陣列
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($user_id)||(int)($user_id)===0){
                $err='add_forum_article_view_log:NO USER_ID';
                die($err);
            }else{
                $user_id=(int)($user_id);
            }

            if(!isset($article_id)||(int)($article_id)===0){
                $err='add_forum_article_view_log:NO ARTICLE_ID';
                die($err);
            }else{
                $article_id=(int)($article_id);
            }

            if(!isset($arry_conn)||(empty($arry_conn))){
                $err='add_forum_article_view_log:NO ARRY_CONN';
                die($err);
            }

        //-----------------------------------------------
        //外掛函式檔
        //-----------------------------------------------

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
                    $err ='add_forum_article_view_log:CONNECT FAIL';
                    die($err);
                }

        //-----------------------------------------------
        //SQL
        //-----------------------------------------------

            $sql="
                # for mssr_forum_article_view_log
                INSERT IGNORE INTO `mssr_forum`.`mssr_forum_article_view_log` SET
                    `user_id`       =  {$user_id    },
                    `article_id`    =  {$article_id },
                    `keyin_mdate`   =  NOW();
            ";
            //echo "<Pre>";print_r($sql);echo "</Pre>";
            $conn->exec($sql);

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return true;
    }
?>