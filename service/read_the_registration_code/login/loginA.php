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
        require_once(str_repeat("../",3)."config/config.php");

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'service/read_the_registration_code/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['service']['read_the_registration_code']){
            $url=str_repeat("../",4).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(login_check(array('t'))){
            $url=str_repeat("../",1).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //user_uid  帳號
    //user_pwd  密碼

        $post_chk=array(
            'user_uid',
            'user_pwd'
        );
        $post_chk=array_map("trim",$post_chk);
        foreach($post_chk as $post){
            if(!isset($_POST[$post])){

                $page='loginF.php';
                header("Location: {$page}");
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //user_uid  帳號
    //user_pwd  密碼

        //POST
        $user_uid=trim($_POST[trim('user_uid')]);
        $user_pwd=trim($_POST[trim('user_pwd')]);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //user_uid  帳號
    //user_pwd  密碼

        $arry_err=array();

        if($user_uid===''){
           $arry_err[]='帳號,未輸入!';
        }
        if($user_pwd===''){
           $arry_err[]='密碼,未輸入!';
        }

        if(count($arry_err)!==0){

            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }

            $url="loginE.php?err=arg";
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------

            //登入指標
            $login_flag=false;

            //處理
            $_user_uid=mysql_prep(mb_strtolower($user_uid));
            $_user_pwd=mysql_prep(mb_strtolower($user_pwd));
            $_date    =date("Y-m-d");

            //初始化, 班級數目
            $_class_code_cno=0;

            //-------------------------------------------
            //檢核帳號
            //-------------------------------------------

                if(!$login_flag){
                    //分大小寫
                    $sql ="
                        SELECT
                            `member`.`uid`
                        FROM `member`
                        WHERE 1=1
                            AND `member`.`account` ='{$_user_uid}'
                    ";
                    //echo $sql.'<br/>';
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);

                    if(empty($arrys_result)){
                    //比對失敗
                        $msg="帳號錯誤, 請重新輸入!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                location.href='loginE.php?err=db';
                            </script>
                        ";
                        die($jscript_back);
                    }else{
                        //撈取, 使用者資訊
                        $_uid=(int)$arrys_result[0]['uid'];
                    }
                }

            //-------------------------------------------
            //檢核明日星球密碼
            //-------------------------------------------

                if(!$login_flag){
                    //分大小寫
                    $sql ="
                        SELECT
                            `member`.`uid`
                        FROM `member`
                        WHERE 1=1
                            AND `member`.`uid` ={$_uid}
                            AND `member`.`account` ='{$_user_uid}'
                            AND `member`.`password`='{$_user_pwd}'
                    ";
                    //echo $sql.'<br/>';
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);

                    if(!empty($arrys_result)){
                        $login_flag=true;
                    }
                }

            //-----------------------------------------------
            //更新閱讀登記條碼版專用密碼
            //-----------------------------------------------

                if(!$login_flag){
                    update_read_the_registration_code_pwd($db_type='mysql',$arry_conn_mssr,$APP_ROOT,$_uid);
                }

            //-------------------------------------------
            //檢核閱讀登記條碼版專用密碼
            //-------------------------------------------

                if(!$login_flag){
                    $sql="
                        SELECT
                            `auth`
                        FROM `mssr_auth_user`
                        WHERE 1=1
                            AND `user_id`={$_uid}
                    ";
                    //echo $sql.'<br/>';
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);

                    if(!empty($arrys_result)){
                        $_auth=unserialize($arrys_result[0]['auth']);
                        $_read_the_registration_code_pwd=trim($_auth['read_the_registration_code_pwd']);

                        if(($_user_pwd)===$_read_the_registration_code_pwd){
                            $login_flag=true;
                        }
                    }
                }

            //-------------------------------------------
            //檢核總結
            //-------------------------------------------

                if(!$login_flag){
                //比對失敗
                    $msg="密碼錯誤, 請重新輸入!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            location.href='loginE.php?err=db';
                        </script>
                    ";
                    die($jscript_back);
                }else{
                //---------------------------------------
                //撈取基本資訊
                //---------------------------------------

                    $sql="
                        SELECT
                            `member`.`uid`,
                            `member`.`name`,
                            `member`.`account`,
                            `member`.`permission`,

                            `personnel`.`school_code`,
                            `school`.`school_name`
                        FROM `member`
                            INNER JOIN `personnel` ON
                            `member`.`uid`=`personnel`.`uid`

                            INNER JOIN `school` ON
                            `personnel`.`school_code`=`school`.`school_code`
                        WHERE 1=1
                            AND `member`.`uid`={$_uid}
                            AND `personnel`.`uid`={$_uid}
                            AND `personnel`.`responsibilities` IN (2,3)
                            AND `personnel`.`start` <= '{$_date}'
                            AND `personnel`.`end`   >= '{$_date}'
                    ";
                    //echo $sql.'<br/>';
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);

                    if(empty($arrys_result)){
                    //比對失敗
                        $msg="您非老師，無法使用本系統!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                location.href='loginE.php?err=db';
                            </script>
                        ";
                        die($jscript_back);
                    }else{
                    //比對成功
                        $_user_permission=trim($arrys_result[0]['permission']);

                        //該帳號被停用
                        if($_user_permission==='x'){
                            $msg="您帳戶已被停用，無法使用本系統!";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    location.href='loginE.php?err=user_state';
                                </script>
                            ";
                            die($jscript_back);
                        }

                        //SESSION, 欄位值
                        $arry_result=$arrys_result[0];
                        foreach($arry_result as $field_name=>$field_value){
                            $field_value=trim($field_value);
                            $$field_name=$field_value;

                            $_SESSION['t'][$field_name]=$field_value;
                        }
                    }

                //---------------------------------------
                //撈取學校、學期資訊
                //---------------------------------------

                    $sql="
                        SELECT
                            `teacher`.`class_code`,

                            `class`.`class_category`,
                            `class`.`grade`,
                            `class`.`classroom`,
                            `class`.`semester_code`
                        FROM `teacher`
                            INNER JOIN `class` ON
                            `teacher`.`class_code`=`class`.`class_code`
                        WHERE 1=1
                            AND `teacher`.`uid`={$_uid}
                            AND `teacher`.`start` <= '{$_date}'
                            AND `teacher`.`end`   >= '{$_date}'
                    ";
                    //echo $sql.'<br/>';
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);

                    //班級數目
                    $_class_code_cno=count($arrys_result);

                    switch($_class_code_cno){

                        case 0:
                        //比對失敗
                            unset($_SESSION['t']);
                            $msg="您非帶班老師，無法使用本系統!";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    location.href='loginE.php?err=db';
                                </script>
                            ";
                            die($jscript_back);
                        break;

                        case 1:
                            //SESSION, 欄位值
                            $arry_result=$arrys_result[0];
                            foreach($arry_result as $field_name=>$field_value){
                                $field_value=trim($field_value);
                                $$field_name=$field_value;

                                $_SESSION['t'][$field_name]=$field_value;
                            }
                            $_SESSION['t']['arrys_class_code']=$arrys_result;
                        break;

                        default:
                            //SESSION, 欄位值
                            $_SESSION['t']['arrys_class_code']=$arrys_result;
                        break;
                    }
                }

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        //用戶資訊
        $_SESSION['config']['user_tbl'] =$arry_result;  //用戶資料表欄位
        $_SESSION['config']['user_type']='t';           //用戶類型(a,t,s,am,dt ...)
        $_SESSION['config']['user_lv']  =3;             //用戶層級(1,3,5,7,13 ...)

        //區域資訊
        array_push($_SESSION['config']['user_area'],"read_the_registration_code");

        //釋放資源
        $conn_user=NULL;

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page=str_repeat("../",1)."index.php";
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