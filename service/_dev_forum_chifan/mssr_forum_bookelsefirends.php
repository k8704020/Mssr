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

        $book_sid ='';
        if(isset($_GET['book_sid'])){
            $book_sid =  $_GET['book_sid'];
        }

        $user_id ='';
        if(isset($_GET['user_id'])){
            $user_id =  $_GET['user_id'];
        }






            $sql  = "
                         SELECT
                            book_sid,
                            user_id,
                            user.member.name

                        FROM
                            mssr_book_borrow_semester
                            join  user.member   on user.member.uid = mssr.mssr_book_borrow_semester.user_id

                        WHERE 1=1
                            and user_id in($all_friends)
                            and user_id not in($user_id)
                            and book_sid = '$book_sid'
                        group by user.member.name

            ";

            $arrys_elseFriends = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



            echo json_encode($arrys_elseFriends);




?>









