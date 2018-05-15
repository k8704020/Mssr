<?php
//-------------------------------------------------------
//教師中心
//-------------------------------------------------------


//-------------------------------------------------------

//設定頁面語系
    header("Content-Type: text/html; charset=UTF-8");

//設定文字內部編碼
    mb_internal_encoding("UTF-8");

//設定台灣時區
    date_default_timezone_set('Asia/Taipei');

//--------------------------------------------------------

//---------------------------------------------------
//設定與引用
//---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",5).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/db/code'
                    );
        func_load($funcs,true);
//建立連線 user
    $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
    $conn_user=conn($db_type='mysql',$arry_conn_user);

$forum_id = $_GET['forum_id'];

$sql = "
    update mssr.mssr_forum set mssr.mssr_forum.forum_state = '停用' where mssr.mssr_forum.forum_id = $forum_id limit 1
";

$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

?>
<script>
    history.back(-1);
</script>