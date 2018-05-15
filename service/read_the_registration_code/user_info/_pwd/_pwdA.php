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
        require_once(str_repeat("../",4).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'service/read_the_registration_code/inc/code',

                    APP_ROOT.'lib/php/array/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

echo "<Pre>";
print_r($_POST);
echo "</Pre>";
die();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['service']['read_the_registration_code']){
            $url=str_repeat("../",5).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(!login_check(array('t'))){
            $url=str_repeat("../",2).'login/loginF.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        //初始化，承接變數
        $_sess_t=$_SESSION['t'];
        foreach($_sess_t as $field_name=>$field_value){
            $$field_name=$field_value;
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //n_pwd     新密碼
    //n_pwd2    確認密碼

        $post_chk=array(
            'n_pwd ',
            'n_pwd2'
        );
        $post_chk=array_map("trim",$post_chk);
        foreach($post_chk as $post){
            if(!isset($_POST[$post])){
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //n_pwd     新密碼
    //n_pwd2    確認密碼

        //POST
        $n_pwd =trim($_POST[trim('n_pwd ')]);
        $n_pwd2=trim($_POST[trim('n_pwd2')]);

        //分頁
        $psize=(isset($_POST['psize']))?(int)$_POST['psize']:10;
        $pinx =(isset($_POST['pinx']))?(int)$_POST['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //n_pwd     新密碼
    //n_pwd2    確認密碼

        $arry_err=array();

        if($n_pwd===''){
           $arry_err[]='新密碼,未輸入!';
        }
        if($n_pwd2===''){
           $arry_err[]='確認密碼,未輸入!';
        }
        if($n_pwd!==$n_pwd2){
            $arry_err[]='新密碼與確認密碼不一致!';
        }

        if(count($arry_err)!==0){
            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
            die();
        }

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            $err ='DB CONNECT FAIL';
            $conn=@mysql_connect(db_host,db_user,db_pass) or
            die($err);

            $err ='DB SET CHARSET FAIL';
            @mysql_set_charset(db_encode) or
            die($err);

            $err ='DB SELECT DB FAIL';
            @mysql_select_db(db_name) or
            die($err);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //n_pwd         新密碼
        //n_pwd2        確認密碼

            $user_id =(int)$user_id;
            $user_uid=mysql_prep($user_uid);

            $sql="
                SELECT
                    `user_id`,
                    `user_uid`
                FROM `user`
                WHERE 1=1
                    AND `user`.`user_id`  = {$user_id }
                    AND `user`.`user_uid` ='{$user_uid}'
            ";
            //echo $sql.'<br/>';

            if(db_num_rows($conn,$sql,$arry_conn)===0){
                $msg="帳號,並不存在!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }

        //-----------------------------------------------
        //脫序
        //-----------------------------------------------
        //n_pwd     新密碼
        //n_pwd2    確認密碼

            $user_id     =(int)$user_id;
            $user_uid    =mysql_prep($user_uid);

            $n_pwd       =md5($n_pwd);

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            $sql="
                UPDATE `user` SET
                    `user_pwd` ='{$n_pwd }'
                WHERE 1=1
                    AND `user`.`user_id`  = {$user_id }
                    AND `user`.`user_uid` ='{$user_uid}'
                LIMIT 1
            ";
            //echo $sql.'<br/>';

            $err ='DB QUERY FAIL';
            @mysql_query($sql,$conn) or
            die($err);

            //關閉連線
            @mysql_close($conn);

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page="";
        $arg =array();

        $page=str_repeat("../",3)."logout.php";
        $arg =http_build_query($arg);

        if(!empty($arg)){
            $url="{$page}?{$arg}";
        }else{
            $url="{$page}";
        }

        $msg="密碼已更換,系統自動登出,請重新登入!";
        $jscript_back="
            <script>
                alert('{$msg}');
                self.location.href='{$url}';
            </script>
        ";

        die($jscript_back);
?>

