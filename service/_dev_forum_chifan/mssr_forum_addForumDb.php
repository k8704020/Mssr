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








    $sql="
        SELECT
           `mssr_user_request`.`request_from`,
           `mssr_user_request`.`request_to`,
           `mssr_user_request`.`request_id`,
           `mssr_user_request`.`request_state`,
           `mssr_user_request`.`request_question`,
           `mssr_user_request`.`keyin_cdate`,
           `mssr_user_request`.`keyin_mdate`,

           `mssr_user_request_forum_create_rev`.`request_id`,
           `mssr_user_request_forum_create_rev`.`forum_id`,
           `mssr_user_request_forum_create_rev`.`rev_id`
        FROM
            `mssr_user_request`

        JOIN `mssr_user_request_forum_create_rev` on `mssr_user_request`.`request_id` = `mssr_user_request_forum_create_rev`.`request_id`

        WHERE 1=1
            and `mssr_user_request_forum_create_rev`.`forum_id`= $rs_forum_id
            and `mssr_user_request`.request_from               = $rs_request_from
            and `mssr_user_request`.request_state              = 2
    ";
    $arrys_add_check=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);





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

$sql="
    SELECT
        mssr.mssr_forum.forum_state
    FROM
        mssr.mssr_forum
    where 1=1
        and  mssr.mssr_forum.forum_state = '啟用'
        and  mssr.mssr_forum.forum_id    = $rs_forum_id
";

$arrys_join_check3=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



if(!empty($arrys_join_check3)){


    if(!empty($arrys_join_check2)){

            $sql="
                UPDATE mssr.`mssr_user_forum` SET `user_state`= '啟用',user_intro='$rs_user_intro'  WHERE  user_id = $rs_user_id and forum_id = $rs_forum_id limit 1;
            ";
            $arrys_join_update2=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            $sql="
                UPDATE mssr.`mssr_user_request` SET `request_state`= 2  WHERE  request_id = $rs_request_id limit 1;
            ";
             $arrys_add_update=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            $msg="申請成功";
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
        $arrys_add_peolpe=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

         $sql="
                UPDATE mssr.`mssr_user_request` SET `request_state`= 2  WHERE  request_id = $rs_request_id limit 1;
        ";
        $arrys_add_updateRquest=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

        $msg="申請成功";
        $jscript_back="
            <script>
                alert('{$msg}');
                history.back(-1);
            </script>
        ";
        die($jscript_back);

}else{


    $sql="
            UPDATE mssr.`mssr_user_request` SET `request_state`= 2  WHERE  request_id = $rs_request_id limit 1;
    ";
    $arrys_add_updateRquest=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

    $rs_user_state='申請中';
    $sql="
            INSERT INTO mssr.`mssr_user_forum`(`forum_id`, `user_id`, `user_type`, `user_state`, `user_intro`, `keyin_cdate`)
            VALUES ($rs_forum_id,$rs_user_id,'一般','$rs_user_state','$rs_user_intro',now())
        ";
    $arrys_add_peolpe=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



     $sql="
        SELECT
           `mssr_user_request`.`request_from`,
           `mssr_user_request`.`request_to`,
           `mssr_user_request`.`request_id`,
           `mssr_user_request`.`request_state`,
           `mssr_user_request`.`request_question`,
           `mssr_user_request`.`keyin_cdate`,
           `mssr_user_request`.`keyin_mdate`,

           `mssr_user_request_forum_create_rev`.`request_id`,
           `mssr_user_request_forum_create_rev`.`forum_id`,
           `mssr_user_request_forum_create_rev`.`rev_id`
        FROM
            `mssr_user_request`

        JOIN `mssr_user_request_forum_create_rev` on `mssr_user_request`.`request_id` = `mssr_user_request_forum_create_rev`.`request_id`

        WHERE 1=1
            and `mssr_user_request_forum_create_rev`.`forum_id`= $rs_forum_id
            and `mssr_user_request`.request_from               = $rs_request_from
            and `mssr_user_request`.request_state              = 2
    ";
    $arrys_add_check=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



    if(count($arrys_add_check) == 2){


         $sql="
            SELECT
                user.`student`.`class_code`,
                ifnull((
                    SELECT
                        user.`teacher`.`uid`
                    from
                        user.`teacher`
                    where
                        user.`teacher`.class_code =  user.`student`.`class_code`
                ),'') as t_uid

            FROM
            user.`student`
            WHERE 1=1
                AND user.`student`.`uid` = $rs_request_from
                AND user.`student`.`start` < CURDATE()
                AND user.`student`.`end` > CURDATE()
            limit 1
        ";
       $arrys_t_uid=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);




       if(!empty($arrys_t_uid)){
            $t_uid = $arrys_t_uid[0]['t_uid'];
            $sql="
                INSERT INTO mssr.`mssr_user_forum`(`forum_id`, `user_id`, `user_type`, `user_state`, `user_intro`, `keyin_cdate`)
                VALUES ($rs_forum_id,$t_uid,'高級版主','啟用','$rs_user_intro',now())
            ";

            $arrys_add_t_uid=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



       }else{

             $sql="
                UPDATE `mssr_user_forum` SET `user_type`='高級版主' WHERE `forum_id` = $rs_forum_id and  user_id = $rs_request_from  limit 1
            ";
            $arrys_add_t_uid=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
       }



        $sql="
            UPDATE `mssr_forum` SET `forum_state`='啟用' WHERE `forum_id` = $rs_forum_id
        ";
        $arrys_add_update=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



        $sql="
            UPDATE `mssr_user_forum` SET `user_state`='啟用' WHERE  forum_id = $rs_forum_id;
        ";
        $arrys_add_people=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



    }

    $msg="申請成功";
    $jscript_back="
        <script>
            alert('{$msg}');
            history.back(-1);
        </script>
    ";
    die($jscript_back);
}


?>