<?php
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    //設定頁面語系
    header("Content-Type: text/html; charset=UTF-8");

    //設定文字內部編碼
    mb_internal_encoding("UTF-8");

    //設定台灣時區
    date_default_timezone_set('Asia/Taipei');


    //-------------------------------------------------------
//mssr_fourm
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------
        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",2).'config/config.php');

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


            //接收參數
            $user_id  = addslashes($_GET['user_id']);
            $sess_uid = addslashes($_GET['sess_uid']);
            $fri_check = '';
            if(isset($_GET['fri_check'])){
            $fri_check= addslashes($_GET['fri_check']);
            }
            //$fri_check= addslashes($_GET['no_check']);

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);





        if($fri_check == '確認'){
            $sql = "update mssr_forum_friend set friend_state = '成功' where user_id = $user_id and friend_id = $sess_uid limit 1";
            $fri_check_arr = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $msg="加入成功";
            $jscript_back="
                <script>
                    history.back(-1);
                    alert('{$msg}');


                </script>
            ";
            exit($jscript_back);

        }else{

            $sql = "update mssr_forum_friend set friend_state = '失敗' where user_id = $user_id and friend_id = $sess_uid limit 1";
            $fri_check_arr = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            $jscript_back="
                <script>
                    history.back(-1);
                </script>
            ";
            exit($jscript_back);
        }


?>