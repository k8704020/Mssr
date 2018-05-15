
<script type="text/javascript" src="js/common.js"></script>

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

    $id  = '';
    $uid = '';
    $class_code ='';
    if(isset($_GET['id'])){
        $id = $_GET['id'];
    }
    if(isset($_GET['uid'])){
        $uid = $_GET['uid'];
    }
    if(isset($_GET['uid'])){
        $class_code = $_GET['class_code'];
    }



//建立連線 user
    $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
    $conn_user=conn($db_type='mysql',$arry_conn_user);



    $sql = "
            select
                 mssr.mssr_auth_user.auth
            from
                 mssr.mssr_auth_user

            where 1 = 1
                and mssr.mssr_auth_user.user_id = $uid
           ";
    


    $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr); //執行sql反回數組
// echo "<Pre>";
// print_r($retrun);
// echo "</Pre>";
// die;

    $auth = $retrun[0]['auth'];
    $auth = unserialize($auth);



    if($auth['pptv_coda']== 0){ //考完試了
        $auth['pptv_coda'] = (int)$auth['pptv_coda'] + (int)1;


        $auth=serialize($auth);
        $sql = "update mssr.mssr_auth_user set mssr.mssr_auth_user.auth = '$auth' where mssr.mssr_auth_user.user_id = $uid limit 1";
        $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
    }else{
        //call_js_function('get_Test()');
    }

    $sql = " update mssr.pptv_score set mssr.pptv_score.is_show = 2  where mssr.pptv_score.id = $id  limit 1";



    $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);


   header('Location: mange.php?class_code='.$class_code);





















function call_js_function($function_name){
    echo '<script type="text/javascript">'
   , $function_name.';'
   , '</script>';
}

?>


