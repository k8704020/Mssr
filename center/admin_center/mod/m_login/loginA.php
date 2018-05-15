<?php
//-------------------------------------------------------
//明日書店網管中心
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",4)."config/config.php");

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'center/admin_center/inc/code',
                    APP_ROOT.'inc/code',

                    APP_ROOT.'lib/php/image/verify/verify_code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(login_check(array('a'))){
            $url=str_repeat("../",2).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //uid   帳號    v
    //pwd   密碼    v
    //vcode 驗證碼  v

        $post_chk=array(
            'uid',
            'pwd',
            'vcode'
        );
        foreach($post_chk as $post){
            if(!isset($_POST[trim($post)])){
                $page='loginF.php';
                header("Location: {$page}");
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //uid   帳號    v
    //pwd   密碼    v
    //vcode 驗證碼  v

        //POST
        $acc  =trim($_POST['uid']);     //帳號
        $pwd  =trim($_POST['pwd']);     //密碼
        $vcode=trim($_POST['vcode']);   //驗證碼

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //uid   帳號    v
    //pwd   密碼    v
    //vcode 驗證碼  v

        $arry_err=array();

        if($acc===''){
            $arry_err[]='帳號,未輸入';
        }
        if($pwd===''){
            $arry_err[]='密碼,未輸入';
        }
        if($vcode===''){
            $arry_err[]='驗證碼,未輸入';
        }else{
            if(verify_code('vcode')===false){
                $arry_err[]='驗證碼,比對失敗';
                $jscript_back="
                    <script>
                        alert('驗證碼錯誤, 請重新輸入');
                        location.href='loginF.php';
                    </script>
                ";
                die($jscript_back);
            }
        }

        if(count($arry_err)!==0){
            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
            $jscript_back="
                <script>
                    alert('參數比對失敗, 請重新輸入');
                    location.href='loginF.php';
                </script>
            ";
            die($jscript_back);
        }

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------

            //登入指標
            $login_flag      =false;
            $is_super        =false;
            $arrys_login_info=array();

            //處理
            $acc=mysql_prep(mb_strtolower($acc));
            $pwd=mysql_prep(mb_strtolower($pwd));

            //-------------------------------------------
            //檢核帳號密碼
            //-------------------------------------------

                if(!$login_flag){
                    $sql ="
                        SELECT
                            `member`.`uid`
                        FROM `member`
                        WHERE 1=1
                            AND `member`.`account`   ='{$acc}'
                            AND `member`.`password`  ='{$pwd}'
                            AND `member`.`permission`<>'x'
                    ";
                    //echo $sql.'<br/>';
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
                    if(empty($arrys_result)){
                    //比對失敗
                        $msg="帳號或密碼錯誤, 請重新輸入";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                location.href='loginF.php';
                            </script>
                        ";
                        die($jscript_back);
                    }else{
                        //撈取, 使用者資訊
                        $uid=(int)$arrys_result[0]['uid'];
                    }
                }

            //-------------------------------------------
            //檢核管理者
            //-------------------------------------------

                if(!$login_flag){
                    $sql="
                        SELECT
                            `member`.`uid`,
                            `member`.`name`,
                            `member`.`account`,
                            `member`.`permission`,

                            `permissions`.`status`
                        FROM `member`
                            INNER JOIN `permissions` ON
                            `member`.`permission`=`permissions`.`permission`
                        WHERE 1=1
                            AND `member`.`uid`        ={$uid}
                            AND `member`.`permission`<>'x'
                    ";
                    //echo $sql.'<br/>';
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
                    if(!empty($arrys_result)){
                        foreach($arrys_result as $arry_result){
                            $rs_status=trim($arry_result['status']);
                            if(in_array($rs_status,array(trim('i_a')))){
                                $is_super=true;
                            }
                        }
                        if(!$is_super){
                        //比對失敗
                            $msg="您非管理者, 請重新登入";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    location.href='loginF.php';
                                </script>
                            ";
                            die($jscript_back);
                        }
                    }else{
                    //比對失敗
                        $msg="帳號或密碼錯誤, 請重新輸入";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                location.href='loginF.php';
                            </script>
                        ";
                        die($jscript_back);
                    }
                }

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        //SESSION, 欄位值
        $arry_result=$arrys_result[0];
        unset($arry_result['status']);

        foreach($arry_result as $field_name=>$field_value){
            $field_value=trim($field_value);
            $$field_name=$field_value;
            //回填
            $_SESSION['a'][$field_name]=$field_value;
        }

        //用戶資訊
        $_SESSION['config']['user_type']='a';           //用戶類型(a,t,s,am,dt ...)
        $_SESSION['config']['user_lv']  =1;             //用戶層級(1,3,5,7,13 ...)

        //區域資訊
        array_push($_SESSION['config']['user_area'],"mssr_admin_center");

        //釋放資源
        $conn_user=NULL;

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page=str_repeat("../",2)."index.php";
        $arg =array();
        $arg =http_build_query($arg);

        if(!empty($arg)){
            $url ="{$page}?{$arg}";
        }else{
            $url ="{$page}";
        }

        header("Location: {$page}");
        die();
?>