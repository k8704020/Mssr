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
    require_once(str_repeat("../",5)."config/config.php");
    require_once(str_repeat("../",3)."code.php");

    //---------------------------------------------------
    //函式: copy_folder()
    //用途: 複製目錄
    //---------------------------------------------------
    //$spath    來源路徑
    //$tpath    目的路徑,預設'.' 表示目前目錄下
    //$fso_enc  檔案系統語系編碼,預設'BIG5'
    //
    //本函式會從來源路徑複製所有內容,包含所有子資料夾與
    //檔案到目的路徑.
    //
    //成功傳回true
    //失敗傳回false,或錯誤訊息陣列
    //---------------------------------------------------

        if(1==2){
        //相對路徑

            $spath='來源';
            $tpath='目的';
            $arry_err=copy_folder($spath,$tpath,$fso_enc='BIG5');

            if($arry_err===false){
            //檔案系統錯誤
                echo 'fail!'.'<br/>';
            }elseif(is_array($arry_err)&&!empty($arry_err)){
            //複製錯誤
                echo 'fail!'.'<br/>';
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }else{
            //執行成功
                echo 'ok'.'<br/>';
            }
        }

        if(1==1){
        //絕對路徑

            $spath='來源';
            $tpath='c:\temp\目的';
            $arry_err=copy_folder($spath,$tpath,$fso_enc='BIG5');

            if($arry_err===false){
            //檔案系統錯誤
                echo 'fail!'.'<br/>';
            }elseif(is_array($arry_err)&&!empty($arry_err)){
            //複製錯誤
                echo 'fail!'.'<br/>';
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }else{
            //執行成功
                echo 'ok'.'<br/>';
            }
        }
?>