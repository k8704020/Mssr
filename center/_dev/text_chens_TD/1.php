!@#$%^&amp;*()_+
<?php
//-------------------------------------------------------
//serialize檢驗
//-------------------------------------------------------

    //外掛設定檔
    require_once(str_repeat("../",2).'config/config.php');

     //外掛函式檔
    $funcs=array(
                APP_ROOT.'inc/code',
                APP_ROOT.'lib/php/db/code',
                APP_ROOT.'lib/php/fso/code'
                );
    func_load($funcs,true);

    //設定頁面語系
    header("Content-Type: text/html; charset=UTF-8");

    //設定文字內部編碼
    mb_internal_encoding("UTF-8");

    //設定台灣時區
    date_default_timezone_set('Asia/Taipei');

//流程
//鎖碼

$i = mysql_prep(base64_encode(gzcompress($text)));








//解碼
$i =  gzuncompress(base64_decode($text));

?>


