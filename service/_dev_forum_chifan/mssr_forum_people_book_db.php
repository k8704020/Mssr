<?php


//-------------------------------------------------------

//設定頁面語系
    header("Content-Type: text/html; charset=UTF-8");

//設定文字內部編碼
    mb_internal_encoding("UTF-8");

//設定台灣時區
    date_default_timezone_set('Asia/Taipei');

//--------------------------------------------------------




//外掛設定檔
    require_once(str_repeat("../",2)."/config/config.php");



//外掛函式檔
    $funcs=array(
                APP_ROOT.'inc/code',
                APP_ROOT.'lib/php/db/code',
                APP_ROOT.'lib/php/array/code'
                );
    func_load($funcs,true);
//接參數




    $close_rev    = '';
    if(isset($_GET['close_rev'])){
        $close_rev = $_GET['close_rev'];
    }

    $select_book  = '';
    if(isset($_GET['select_book'])){
       $select_book  = $_GET['select_book'];
    }

    $rev_tex      = '';
    if(isset($_GET['rev_tex'])){
       $rev_tex      = $_GET['rev_tex'];
    }

    $request_id   = '';
    if(isset($_GET['request_id'])){
        $request_id   = $_GET['request_id'];
    }


//建立連線 user
    $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
    $conn_user=conn($db_type='mysql',$arry_conn_user);



//if($select_book ==''){
//    echo 123;
//}


    if($close_rev == '取消'){

         $sql = "
            update
                mssr_user_request

            set
               request_state = 3
            where
                request_id = $request_id
            limit 1
           ";

         db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


                    $jscript_back="
                        <script>
                            history.back(-1);
                        </script>
                    ";



                    die($jscript_back);
    }else{




    $sql = "
            update
                mssr_user_request_book_rev

            set
                book_sid        = '$select_book',
                request_content = '$rev_tex'
            where
                request_id = $request_id
            limit 1
           ";


    db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



    $sql = "
            update
                mssr_user_request

            set
               request_state = 2
            where
                request_id = $request_id
            limit 1
           ";

    db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



     $msg= "推薦成功";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    echo $jscript_back;
    }
?>