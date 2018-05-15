<?php
//-------------------------------------------------------
//教師中心
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
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/vaildate/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['center']['teacher_center']){
            $url=str_repeat("../",6).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        $arrys_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
        if(empty($arrys_login_info)){
            die();
        }

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

        if(in_array('read_the_registration_code',$config_arrys['user_area'])){
        //清空閱讀登記條碼版登入資訊

            $_SESSION['config']['user_tbl']=array();
            $_SESSION['config']['user_type']='';
            $_SESSION['config']['user_lv']=0;
            if(in_array('read_the_registration_code',$_SESSION['config']['user_area'])){
                foreach($_SESSION['config']['user_area'] as $inx=>$area){
                    if(trim($area)==='read_the_registration_code'){
                        unset($_SESSION['config']['user_area'][$inx]);
                    }
                }
            }
        }

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        $sess_login_info=(isset($_SESSION['tc']['t|dt']))?$_SESSION['tc']['t|dt']:array();

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

        if(!empty($sess_login_info)){
            if(!auth_check($db_type='mysql',$arry_conn_user,$sess_login_info['permission'],$auth_type='mssr_tc')){
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }
        }else{
            //權限指標
            $auth_flag=false;
            foreach($arrys_login_info as $inx=>$arry_login_info){
                if(auth_check($db_type='mysql',$arry_conn_user,$arry_login_info['permission'],$auth_type='mssr_tc'))$auth_flag=true;
            }
            if(!$auth_flag){
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }
        }

    //---------------------------------------------------
    //管理者判斷
    //---------------------------------------------------

        if(!empty($sess_login_info)){
            $is_admin=is_admin(trim($sess_login_info['permission']));
            if($is_admin){
                $sess_login_info['responsibilities']=99;
            }
        }

    //---------------------------------------------------
    //系統權限判斷
    //---------------------------------------------------
    //1     校長
    //3     主任
    //5     帶班老師
    //12    行政老師
    //14    主任帶一個班
    //16    主任帶多個班
    //22    老師帶多個班
    //99    管理者

        if(!empty($sess_login_info)){
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_articile');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //user_id       使用者主索引
    //article_id    文章主索引
    //del_code      隱藏代號

        $get_chk=array(
            'user_id   ',
            'article_id',
            'del_code  '
        );
        $get_chk=array_map("trim",$get_chk);
        foreach($get_chk as $get){
            if(!isset($_GET[$get])){
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //user_id       使用者主索引
    //article_id    文章主索引
    //del_code      隱藏代號

        //GET
        $user_id   =trim($_GET[trim('user_id   ')]);
        $article_id=trim($_GET[trim('article_id')]);
        $del_code  =trim($_GET[trim('del_code  ')]);
        $ajax_flag =(isset($_GET[trim('ajax_flag')]))?(int)$_GET[trim('ajax_flag')]:0;

        //SESSION
        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);
        $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
        $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
        $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];

        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //user_id       使用者主索引
    //article_id    文章主索引
    //del_code      隱藏代號

        $arry_err=array();

        if($user_id===''){
           $arry_err[]='使用者主索引,未輸入!';
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $arry_err[]='使用者主索引,錯誤!';
            }
        }

        if($article_id===''){
           $arry_err[]='文章主索引,未輸入!';
        }else{
            $article_id=(int)$article_id;
            if($article_id===0){
                $arry_err[]='文章主索引,錯誤!';
            }
        }

        if($del_code===''){
           $arry_err[]='隱藏代號,未輸入!';
        }else{
            $del_code=(int)$del_code;
            if($del_code===0){
                $arry_err[]='隱藏代號,錯誤!';
            }
        }

        if(count($arry_err)!==0){
            if(1==1){//除錯用
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

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //user_id       使用者主索引
        //article_id    文章主索引
        //del_code      隱藏代號

            $sess_user_id   =(int)$sess_user_id;
            $sess_grade     =(int)$sess_grade;
            $sess_classroom =(int)$sess_classroom;

            $user_id        =(int)$user_id;
            $article_id     =(int)$article_id;
            $del_code       =(int)$del_code;

            //-------------------------------------------
            //檢核
            //-------------------------------------------

                //---------------------------------------
                //檢核使用者
                //---------------------------------------

                    $sql="
                        SELECT
                            `user_id`
                        FROM `mssr_user_info`
                        WHERE 1=1
                            AND `user_id` = {$user_id }
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    if(empty($arrys_result)){
                        $msg="查無學生資訊, 請通知明日星球人員!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                history.back(-1);
                            </script>
                        ";
                        die($jscript_back);
                    }

                //---------------------------------------
                //檢核文章
                //---------------------------------------

                    $sql="
                        SELECT
                            `article_id`
                        FROM `mssr_forum_article`
                        WHERE 1=1
                            AND `user_id`    = {$user_id    }
                            AND `article_id` = {$article_id }
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    if(empty($arrys_result)){
                        $msg="查無文章資訊, 請通知明日星球人員!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                history.back(-1);
                            </script>
                        ";
                        die($jscript_back);
                    }

                //---------------------------------------
                //查詢文章log紀錄
                //---------------------------------------

                    $sql="
                        SELECT
                            `log_id`
                        FROM `mssr_forum_article_log`
                        WHERE 1=1
                            AND `user_id`    = {$user_id    }
                            AND `article_id` = {$article_id }
                        ORDER BY `log_id` DESC
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    if(empty($arrys_result)){
                        $msg="查無文章資訊, 請通知明日星球人員!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                history.back(-1);
                            </script>
                        ";
                        die($jscript_back);
                    }else{
                        $log_id=$arrys_result[0]['log_id'];
                    }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            //SESSION
            $sess_user_id   =(int)$sess_user_id;
            $sess_grade     =(int)$sess_grade;
            $sess_classroom =(int)$sess_classroom;

            $user_id        =(int)$user_id;
            $article_id     =(int)$article_id;
            $del_code       =(int)$del_code;
            $log_id         =(int)$log_id;

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            switch($del_code){

                case 1:
                    $sql="
                        # for mssr_forum_article
                        UPDATE `mssr_forum_article` SET
                            `article_state`     ='正常'
                        WHERE 1=1
                            AND `user_id`    = {$user_id    }
                            AND `article_id` = {$article_id }
                        LIMIT 1;

                        # for mssr_forum_article_log
                        UPDATE `mssr_forum_article_log` SET
                            `article_state`     ='正常'
                        WHERE 1=1
                            AND `user_id`    = {$user_id    }
                            AND `article_id` = {$article_id }
                            AND `log_id`     = {$log_id     }
                        LIMIT 1;
                    ";
                break;

                case 2:
                    $sql="
                        # for mssr_forum_article
                        UPDATE `mssr_forum_article` SET
                            `article_state`     ='刪除'
                        WHERE 1=1
                            AND `user_id`    = {$user_id    }
                            AND `article_id` = {$article_id }
                        LIMIT 1;

                        # for mssr_forum_article_log
                        UPDATE `mssr_forum_article_log` SET
                            `article_state`     ='刪除'
                        WHERE 1=1
                            AND `user_id`    = {$user_id    }
                            AND `article_id` = {$article_id }
                            AND `log_id`     = {$log_id     }
                        LIMIT 1;
                    ";
                break;

                default:
                    $sql="
                        # for mssr_forum_article
                        UPDATE `mssr_forum_article` SET
                            `article_state`     ='正常'
                        WHERE 1=1
                            AND `user_id`    = {$user_id    }
                            AND `article_id` = {$article_id }
                        LIMIT 1;

                        # for mssr_forum_article_log
                        UPDATE `mssr_forum_article_log` SET
                            `article_state`     ='正常'
                        WHERE 1=1
                            AND `user_id`    = {$user_id    }
                            AND `article_id` = {$article_id }
                            AND `log_id`     = {$log_id     }
                        LIMIT 1;
                    ";
                break;
            }

            //執行
            $conn_mssr->exec($sql);

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        if($ajax_flag===0){
            $url ="";
            $page=str_repeat("../",0)."content.php";
            $arg =array(
                'user_id'=>$user_id,
                'psize'=>$psize,
                'pinx' =>$pinx
            );
            $arg =http_build_query($arg);

            if(!empty($arg)){
                $url="{$page}?{$arg}";
            }else{
                $url="{$page}";
            }

            header("Location: {$url}");
        }else{
            $arry_output=array(
                'state'=>'ok',
                'code' =>$del_code
            );
            die(json_encode($arry_output,true));
        }
?>