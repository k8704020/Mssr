<?php
//-------------------------------------------------------
//mssr_forum
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",4).'config/config.php');

        //外掛函式檔
        $funcs=array(
            APP_ROOT.'inc/code',

            APP_ROOT.'lib/php/db/code',
            APP_ROOT.'lib/php/net/code',
            APP_ROOT.'lib/php/array/code'
       	);
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //process_url       處理頁面
    //action_code       動作代號
    //action_from       動作來源使用者主索引
    //user_id_1         動作指向使用者主索引1
    //user_id_2         動作指向使用者主索引2
    //book_sid_1        書籍識別碼1
    //book_sid_2        書籍識別碼2
    //forum_id_1        社團主索引1
    //forum_id_2        社團主索引2
    //article_id        文章主索引
    //reply_id          回覆主索引
    //go_url            跳轉頁面

        $post_chk=array(
            'action_code',
            'action_from',
            'user_id_1  ',
            'user_id_2  ',
            'book_sid_1 ',
            'book_sid_2 ',
            'forum_id_1 ',
            'forum_id_2 ',
            'article_id ',
            'reply_id   '
        );
        $post_chk=array_map("trim",$post_chk);
        foreach($post_chk as $post){
            if(!isset($_POST[$post])){
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //process_url       處理頁面
    //action_code       動作代號
    //action_from       動作來源使用者主索引
    //user_id_1         動作指向使用者主索引1
    //user_id_2         動作指向使用者主索引2
    //book_sid_1        書籍識別碼1
    //book_sid_2        書籍識別碼2
    //forum_id_1        社團主索引1
    //forum_id_2        社團主索引2
    //article_id        文章主索引
    //reply_id          回覆主索引
    //go_url            跳轉頁面

        //POST
        $action_code=trim($_POST[trim('action_code')]);
        $action_from=trim($_POST[trim('action_from')]);
        $user_id_1  =trim($_POST[trim('user_id_1  ')]);
        $user_id_2  =trim($_POST[trim('user_id_2  ')]);
        $book_sid_1 =trim($_POST[trim('book_sid_1 ')]);
        $book_sid_2 =trim($_POST[trim('book_sid_2 ')]);
        $forum_id_1 =trim($_POST[trim('forum_id_1 ')]);
        $forum_id_2 =trim($_POST[trim('forum_id_2 ')]);
        $article_id =trim($_POST[trim('article_id ')]);
        $reply_id   =trim($_POST[trim('reply_id   ')]);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //process_url       處理頁面
    //action_code       動作代號
    //action_from       動作來源使用者主索引
    //user_id_1         動作指向使用者主索引1
    //user_id_2         動作指向使用者主索引2
    //book_sid_1        書籍識別碼1
    //book_sid_2        書籍識別碼2
    //forum_id_1        社團主索引1
    //forum_id_2        社團主索引2
    //article_id        文章主索引
    //reply_id          回覆主索引
    //go_url            跳轉頁面

        $arry_err=array();

        if($action_code===''){
           $arry_err[]='動作代號,未輸入!';
        }else{
            $action_code=trim($action_code);
        }

        if($action_from===''){
           $arry_err[]='動作來源使用者主索引,未輸入!';
        }else{
            $action_from=(int)$action_from;
            if($action_from===0){
                $arry_err[]='動作來源使用者主索引,錯誤!';
            }
        }


        if(count($arry_err)!==0){
            if(1==1){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
            die();
        }

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //process_url       處理頁面
        //action_code       動作代號
        //action_from       動作來源使用者主索引
        //user_id_1         動作指向使用者主索引1
        //user_id_2         動作指向使用者主索引2
        //book_sid_1        書籍識別碼1
        //book_sid_2        書籍識別碼2
        //forum_id_1        社團主索引1
        //forum_id_2        社團主索引2
        //article_id        文章主索引
        //reply_id          回覆主索引
        //go_url            跳轉頁面

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $action_code=mysql_prep(strip_tags($action_code ));
            $action_from=(int)$action_from;

            $user_id_1    =(int)$user_id_1;
            $user_id_2    =(int)$user_id_2;
            $book_sid_1   =mysql_prep(strip_tags($book_sid_1    ));
            $book_sid_2   =mysql_prep(strip_tags($book_sid_2    ));
            $forum_id_1   =(int)$forum_id_1;
            $forum_id_2   =(int)$forum_id_2;

            $article_id =(int)$article_id;
            $reply_id   =(int)$reply_id;

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            $sql="
                # for mssr_action_forum_log
                INSERT INTO `mssr_action_forum_log` SET
                    `action_from`   =   {$action_from   } ,
                    `action_code`   =  '{$action_code   }',

                    `user_id_1`     =   {$user_id_1     } ,
                    `user_id_2`     =   {$user_id_2     } ,
                    `book_sid_1`    =  '{$book_sid_1    }',
                    `book_sid_2`    =  '{$book_sid_2    }',
                    `forum_id_1`    =   {$forum_id_1    } ,
                    `forum_id_2`    =   {$forum_id_2    } ,

                    `article_id`    =   {$article_id    } ,
                    `reply_id`      =   {$reply_id      } ,
                    `keyin_cdate`   =   NOW()             ;
            ";
            //送出
            $conn_mssr->exec($sql);

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------
?>