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

    $rs_forum_id    = '';
    if(isset($_GET['rs_forum_id'])){
        $rs_forum_id = $_GET['rs_forum_id'];
    }
    $rs_user_id    = '';
    if(isset($_GET['rs_user_id'])){
        $rs_user_id = $_GET['rs_user_id'];
    }

    $rs_request_id    = '';
    if(isset($_GET['rs_request_id'])){
        $rs_request_id = $_GET['rs_request_id'];
    }

    $rs_user_intro    = '';
    if(isset($_GET['introMy'])){
        $rs_user_intro = $_GET['introMy'];
    }
    $rs_request_from    = '';
    if(isset($_GET['rs_request_from'])){
        $rs_request_from = $_GET['rs_request_from'];
    }
    $rs_user_type ='';




//建立連線 user
    $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
    $conn_user=conn($db_type='mysql',$arry_conn_user);

    $sql = "
        SELECT
            mssr.`mssr_user_forum`.`forum_id`,
            mssr.`mssr_user_forum`.`user_id`,
            mssr.`mssr_user_forum`.`user_type`,
            mssr.`mssr_user_forum`.`user_state`,
            mssr.`mssr_user_forum`.`user_intro`,
            mssr.`mssr_user_forum`.`keyin_cdate`,
            mssr.`mssr_user_forum`.`keyin_mdate`
        FROM
            mssr.`mssr_user_forum`
        WHERE 1 = 1
            and mssr.`mssr_user_forum`.forum_id   = $rs_forum_id
            and mssr.`mssr_user_forum`.`user_id`  = $rs_request_from
            and mssr.`mssr_user_forum`.`user_type`= '一般版主'
    ";
    $arrys_join_check=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

$sql = "
     SELECT
            mssr.`mssr_user_forum`.`forum_id`,
            mssr.`mssr_user_forum`.`user_id`,
            mssr.`mssr_user_forum`.`user_type`,
            mssr.`mssr_user_forum`.`user_state`,
            mssr.`mssr_user_forum`.`user_intro`,
            mssr.`mssr_user_forum`.`keyin_cdate`,
            mssr.`mssr_user_forum`.`keyin_mdate`
        FROM
            mssr.`mssr_user_forum`
        WHERE 1 = 1
            and mssr.`mssr_user_forum`.forum_id   = $rs_forum_id
            and mssr.`mssr_user_forum`.`user_id`  = $rs_user_id
";
    $arrys_join_check2=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);








    if(empty($arrys_join_check)){


        if(!empty($arrys_join_check2)){
            $msg="申請中，等待版主審核";

            $sql="
                UPDATE mssr.`mssr_user_forum` SET `user_state`= '申請中',user_intro='$rs_user_intro'  WHERE  user_id = $rs_user_id and forum_id = $rs_forum_id limit 1;
            ";
            $arrys_join_update2=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            $sql="
                UPDATE mssr.`mssr_user_request` SET `request_state`= 2  WHERE  request_id = $rs_request_id limit 1;
            ";

            $arrys_join_update=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            $jscript_back="
                <script>
                    alert('{$msg}');
                    history.back(-1);
                </script>
            ";
            die($jscript_back);
        }

        $rs_user_state='申請中';
        $sql="
            INSERT INTO mssr.`mssr_user_forum`(`forum_id`, `user_id`, `user_type`, `user_state`, `user_intro`, `keyin_cdate`)
            VALUES ($rs_forum_id,$rs_user_id,'$rs_user_type','$rs_user_state','$rs_user_intro',now())
        ";

        $arrys_join=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



        $sql="
            UPDATE mssr.`mssr_user_request` SET `request_state`= 2  WHERE  request_id = $rs_request_id limit 1;
        ";

        $arrys_join_update=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


        $msg="申請中，等待版主審核";
        $jscript_back="
            <script>
                alert('{$msg}');
                history.back(-1);
            </script>
        ";
        die($jscript_back);
    }else{
        if(!empty($arrys_join_check2)){
            $msg="加入成功";

            $sql="
                UPDATE mssr.`mssr_user_forum` SET `user_state`= '啟用',user_intro='$rs_user_intro'  WHERE  user_id = $rs_user_id and forum_id = $rs_forum_id limit 1;
            ";
            $arrys_join_update2=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            $sql="
                UPDATE mssr.`mssr_user_request` SET `request_state`= 2  WHERE  request_id = $rs_request_id limit 1;
            ";

            $arrys_join_update=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            $jscript_back="
                <script>
                    alert('{$msg}');
                    history.back(-1);
                </script>
            ";
            die($jscript_back);
        }

        $rs_user_state='啟用';
        $sql="
            INSERT INTO mssr.`mssr_user_forum`(`forum_id`, `user_id`, `user_type`, `user_state`, `user_intro`, `keyin_cdate`)
            VALUES ($rs_forum_id,$rs_user_id,'一般','$rs_user_state','$rs_user_intro',now())
        ";

        $arrys_join=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
        $sql="
            UPDATE mssr.`mssr_user_request` SET `request_state`= 2  WHERE  request_id = $rs_request_id limit 1;
        ";
        $arrys_join_update=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
        $msg="加入成功";
        $jscript_back="
            <script>
                alert('{$msg}');
                history.back(-1);
            </script>
        ";
        die($jscript_back);
    }

?>