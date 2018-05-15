<?php
//-------------------------------------------------------
//設定主檔
//-------------------------------------------------------

    //---------------------------------------------------
    //引用
    //---------------------------------------------------

        //-----------------------------------------------
        //TW 資料庫連結資訊陣列
        //-----------------------------------------------

            $arry_conn_user_tw=$arry_conn_user;
            $arry_conn_mssr_tw=$arry_conn_mssr;
            $arry_conn_user_tw['db_country']='tw';
            $arry_conn_mssr_tw['db_country']='tw';

        //-----------------------------------------------
        //HK 資料庫連結資訊陣列
        //-----------------------------------------------

            $arry_conn_user_hk=array(
                'db_country'=>'hk',
                'db_host'   =>'175.159.131.246',
                'db_name'   =>'user',
                'db_user'   =>'user',
                'db_pass'   =>'eiP8ohY5',
                'db_encode' =>'UTF8'
            );
            $arry_conn_mssr_hk=array(
                'db_country'=>'hk',
                'db_host'   =>'175.159.131.246',
                'db_name'   =>'mssr',
                'db_user'   =>'mssr',
                'db_pass'   =>'UeR1up0u',
                'db_encode' =>'UTF8'
            );

        //-----------------------------------------------
        //sg 資料庫連結資訊陣列
        //-----------------------------------------------

            $arry_conn_user_sg=array(
                'db_country'=>'sg',
                'db_host'   =>'175.159.131.246',
                'db_name'   =>'user',
                'db_user'   =>'user',
                'db_pass'   =>'eiP8ohY5',
                'db_encode' =>'UTF8'
            );
            $arry_conn_mssr_sg=array(
                'db_country'=>'sg',
                'db_host'   =>'175.159.131.246',
                'db_name'   =>'mssr',
                'db_user'   =>'mssr',
                'db_pass'   =>'UeR1up0u',
                'db_encode' =>'UTF8'
            );

        //-----------------------------------------------
        //資料庫連結資訊國籍類別
        //-----------------------------------------------

            $conn_host_country_code=array(
                "175.159.131.246"=>"hk",
                "175.159.131.246"=>"sg",
                "140.115.16.104" =>"tw"
            );
//echo "<Pre>";
//print_r($arry_conn_user_tw);
//echo "</Pre>";
//echo "<Pre>";
//print_r($arry_conn_mssr_tw);
//echo "</Pre>";
//echo "<Pre>";
//print_r($arry_conn_user_hk);
//echo "</Pre>";
//echo "<Pre>";
//print_r($arry_conn_mssr_hk);
//echo "</Pre>";
//die();
?>