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
    require_once(str_repeat("../",1)."code.php");

    //---------------------------------------------------
    //取得目錄容量,回遞所有結構
    //---------------------------------------------------
    //$path     路徑
    //$size     容量,單位 : bytes
    //$fso_enc  檔案系統語系編碼,預設 'BIG5'
    //---------------------------------------------------

        if(1==1){
            //上一層目錄
            $path="../";
            $size=0;
            dir_size($path,$size,$fso_enc='BIG5');
            echo $size."<br/>";
        }

        if(1==1){
            //目前目錄
            $path=".";
            $size=0;
            dir_size($path,$size,$fso_enc='BIG5');
            echo $size."<br/>";
        }

        if(1==1){
            //絕對路徑
            $path="c:/temp";
            $size=0;
            dir_size($path,$size,$fso_enc='BIG5');
            echo $size."<br/>";
        }
?>