<?php
//-------------------------------------------------------
//mssr_branch
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
    //user_id           動作執行者主索引
    //visit_to          被拜訪人主索引
    //book_sid          書籍識別碼
    //friend_ident      是否為朋友(1=是,0=不是)
    //branch_id         分店主索引
    //branch_state      分店狀態(啟用,停用)
    //branch_rank       分店等級
    //branch_lv         分店圈數
    //task_ident        分店有無可接任務(1=有,0=無)
    //task_state        分店有無進行中的任務(1=有,0=無)
    //cat_id            分店相關類別id
    //task_sdate        任務起始時間
    //go_url            動作完畢, 跳轉頁面

        $post_chk=array(
            'action_code ',
            'user_id     ',
            'visit_to    ',
            'book_sid    ',
            'friend_ident',
            'branch_id   ',
            'branch_state',
            'branch_rank ',
            'branch_lv   ',
            'task_ident  ',
            'task_state  ',
            'cat_id      ',
            'task_sdate  '
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
    //user_id           動作執行者主索引
    //visit_to          被拜訪人主索引
    //book_sid          書籍識別碼
    //friend_ident      是否為朋友(1=是,0=不是)
    //branch_id         分店主索引
    //branch_state      分店狀態(啟用,停用)
    //branch_rank       分店等級
    //branch_lv         分店圈數
    //task_ident        分店有無可接任務(1=有,0=無)
    //task_state        分店有無進行中的任務(1=有,0=無)
    //cat_id            分店相關類別id
    //task_sdate        任務起始時間
    //go_url            動作完畢, 跳轉頁面

        //POST
        $action_code =trim($_POST[trim('action_code ')]);
        $user_id     =trim($_POST[trim('user_id     ')]);
        $visit_to    =trim($_POST[trim('visit_to    ')]);
        $book_sid    =trim($_POST[trim('book_sid    ')]);
        $friend_ident=trim($_POST[trim('friend_ident')]);
        $branch_id   =trim($_POST[trim('branch_id   ')]);
        $branch_state=trim($_POST[trim('branch_state')]);
        $branch_rank =trim($_POST[trim('branch_rank ')]);
        $branch_lv   =trim($_POST[trim('branch_lv   ')]);
        $task_ident  =trim($_POST[trim('task_ident  ')]);
        $task_state  =trim($_POST[trim('task_state  ')]);
        $cat_id      =trim($_POST[trim('cat_id    ')]);
        $task_sdate  =trim($_POST[trim('task_sdate  ')]);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //process_url       處理頁面
    //action_code       動作代號
    //user_id           動作執行者主索引
    //visit_to          被拜訪人主索引
    //book_sid          書籍識別碼
    //friend_ident      是否為朋友(1=是,0=不是)
    //branch_id         分店主索引
    //branch_state      分店狀態(啟用,停用)
    //branch_rank       分店等級
    //branch_lv         分店圈數
    //task_ident        分店有無可接任務(1=有,0=無)
    //task_state        分店有無進行中的任務(1=有,0=無)
    //cat_id            分店相關類別id
    //task_sdate        任務起始時間
    //go_url            動作完畢, 跳轉頁面

        $arry_err=array();

        if($action_code===''){
           $arry_err[]='動作代號,未輸入!';
        }else{
            $action_code=trim($action_code);
        }

        if($user_id===''){
           $arry_err[]='動作執行者主索引,未輸入!';
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $arry_err[]='動作執行者主索引,錯誤!';
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
        //user_id           動作執行者主索引
        //visit_to          被拜訪人主索引
        //book_sid          書籍識別碼
        //friend_ident      是否為朋友(1=是,0=不是)
        //branch_id         分店主索引
        //branch_state      分店狀態(啟用,停用)
        //branch_rank       分店等級
        //branch_lv         分店圈數
        //task_ident        分店有無可接任務(1=有,0=無)
        //task_state        分店有無進行中的任務(1=有,0=無)
        //cat_id            分店相關類別id
        //task_sdate        任務起始時間
        //go_url            動作完畢, 跳轉頁面

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $action_code    =mysql_prep(strip_tags($action_code));
            $user_id        =(int)$user_id;
            $visit_to       =(int)$visit_to;
            $book_sid       =mysql_prep(strip_tags($book_sid));
            $friend_ident   =(int)$friend_ident;
            $branch_id      =(int)$branch_id;
            $branch_state   =mysql_prep(strip_tags($branch_state));
            $branch_rank    =(int)$branch_rank;
            $branch_lv      =(int)$branch_lv;
            $task_ident     =(int)$task_ident;
            $task_state     =(int)$task_state;
            $cat_id         =(int)$cat_id;
            $task_sdate     =mysql_prep(strip_tags($task_sdate ));
            if($task_sdate===''){
                $task_sdate='0000-00-00 00:00:00';
            }

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            $sql="
                # for mssr_action_branch_log
                INSERT INTO `mssr_action_branch_log` SET
                    `action_code`   ='{$action_code     }',
                    `user_id`       = {$user_id         } ,
                    `visit_to`      = {$visit_to        } ,
                    `book_sid`      ='{$book_sid        }',
                    `friend_ident`  = {$friend_ident    } ,
                    `branch_id`     = {$branch_id       } ,
                    `branch_state`  ='{$branch_state    }',
                    `branch_rank`   = {$branch_rank     } ,
                    `branch_lv`     = {$branch_lv       } ,
                    `task_ident`    = {$task_ident      } ,
                    `task_state`    = {$task_state      } ,
                    `cat_id`        = {$cat_id          } ,
                    `task_sdate`    ='{$task_sdate      }',
                    `keyin_cdate`   =NOW()                ;
            ";
            //echo "<Pre>";
            //print_r($sql);
            //echo "</Pre>";
            //die();

            //送出
            $conn_mssr->exec($sql);

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------
?>