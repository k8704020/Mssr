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
    //檔案目錄清單,回遞所有結構
    //---------------------------------------------------
    //$path         路徑
    //$arry_dirs    目錄結果陣列
    //$arry_files   檔案結果陣列
    //$fso_enc      檔案系統語系編碼,預設 'BIG5'
    //---------------------------------------------------

        if(1==2){
            //上一層目錄
            $path="..";
            $arry_dirs =array();
            $arry_files=array();
            fsolists_recursive($path,$arry_dirs,$arry_files,$fso_enc='BIG5');

            echo "<pre>";
            print_r($arry_dirs );
            print_r($arry_files);
            echo "</pre>";
        }

        if(1==2){
            //目前目錄
            $path=".";
            $arry_dirs =array();
            $arry_files=array();
            fsolists_recursive($path,$arry_dirs,$arry_files,$fso_enc='BIG5');

            echo "<pre>";
            print_r($arry_dirs );
            print_r($arry_files);
            echo "</pre>";
        }

        if(1==2){
            //絕對路徑
            $path="c:/temp";
            $arry_dirs =array();
            $arry_files=array();
            fsolists_recursive($path,$arry_dirs,$arry_files,$fso_enc='BIG5');

            echo "<pre>";
            print_r($arry_dirs );
            print_r($arry_files);
            echo "</pre>";
        }

        if(1==1){
            //相對路徑
            $path="來源";
            $arry_dirs =array();
            $arry_files=array();
            fsolists_recursive($path,$arry_dirs,$arry_files,$fso_enc='BIG5');

            echo "<pre>";
            print_r($arry_dirs );
            print_r($arry_files);
            echo "</pre>";
        }
?>