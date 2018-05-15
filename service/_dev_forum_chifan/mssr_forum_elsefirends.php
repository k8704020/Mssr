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



        //建立連線 user
        $conn_user=conn($db_type='mysql',$arry_conn_user);

        //建立連線 mssr
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
        //-----------------------------------------------

        $all_friends  =  '';
        if(isset($_GET['all_friends'])){
            $all_friends = $_GET['all_friends'];
        }

        $forum_span ='';
        if(isset($_GET['forum_span'])){
            $forum_span =  $_GET['forum_span'];
        }

        $fail_friends ='';
        if(isset($_GET['fail_friends'])){
            $fail_friends =  $_GET['fail_friends'];
        }






            $sql  = "
                SELECT forum_name, name, user_id
                FROM mssr_user_forum
                JOIN mssr_forum ON mssr_forum.forum_id = mssr_user_forum.forum_id
                JOIN user.member ON user.member.uid = mssr_user_forum.user_id
                WHERE 1 =1
                AND mssr_user_forum.user_id
                IN ( $all_friends )
                AND mssr_forum.forum_name =  '$forum_span'
            ";


            $arrys_elseFriends = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            unset($arrys_elseFriends[0]);
            sort($arrys_elseFriends);


            echo json_encode($arrys_elseFriends);




?>









