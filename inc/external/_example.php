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

    //外掛設定檔
    require_once('code.php');

    //---------------------------------------------------
    //設置測試資料
    //---------------------------------------------------

        $school_code=trim("gcp");
        $grade      =2;
        $user_id    =26442;

        $arry_mssr_grade_avg_info=get_mssr_grade_avg_info($school_code,$grade);
        $user_avg_flag           =get_mssr_user_avg_info($user_id,$arry_mssr_grade_avg_info);

echo "<Pre>";
print_r($arry_mssr_grade_avg_info);
echo "</Pre>";

echo "<Pre>";
print_r($user_avg_flag);
echo "</Pre>";
?>