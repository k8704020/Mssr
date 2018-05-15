<?php
//-------------------------------------------------------
//閱讀登記條碼版
//-------------------------------------------------------

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
                    APP_ROOT.'inc/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //處理
    //---------------------------------------------------

        $login=(isset($_GET['login']))?trim($_GET['login']):'loginF';

        if($login==='loginF'){
            $_SESSION['_read_the_registration_code']['opinion_book']['login']='loginF';
            $url='loginF.php';
        }
        if($login==='loginF2'){
            $_SESSION['_read_the_registration_code']['opinion_book']['login']='loginF2';
            $url='loginF2.php';
        }

        $jscript_back="
            <script>
                location.href='{$url}';
            </script>
        ";
        die($jscript_back);
?>