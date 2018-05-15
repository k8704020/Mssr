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

        if(!empty($sess_login_info)){
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_group');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //edit_type     修改類型
    //group_sid     組別識別碼

        $get_chk=array(
            'edit_type',
            'group_sid'
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
    //edit_type     修改類型
    //group_sid     組別識別碼

        //GET
        $edit_type     =trim($_GET[trim('edit_type')]);
        $group_sid     =trim($_GET[trim('group_sid')]);

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
    //edit_type     修改類型
    //group_sid     組別識別碼

        $arry_err=array();

        if($edit_type===''){
           $arry_err[]='修改類型,未輸入!';
        }else{
            $edit_type=trim($edit_type);
            if(!in_array($edit_type,array('採用','停用'))){
                $arry_err[]='修改類型,錯誤!';
            }
        }

        if($group_sid===''){
           $arry_err[]='組別識別碼,未輸入!';
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

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //edit_type     修改類型
        //group_sid     組別識別碼

            $edit_type      =mysql_prep($edit_type);
            $group_sid      =mysql_prep($group_sid);

            $sess_user_id   =(int)$sess_user_id;
            $sess_grade     =(int)$sess_grade;
            $sess_classroom =(int)$sess_classroom;
            $sess_class_code=mysql_prep($sess_class_code);

            //-------------------------------------------
            //檢核組別識別碼
            //-------------------------------------------

                $sql="
                    SELECT
                        `group_name`
                    FROM `mssr_group`
                    WHERE 1=1
                        AND `group_sid`='{$group_sid}'
                ";

                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                $numrow=count($arrys_result);

                if($numrow===0){
                    $msg="組別不存在, 請重新輸入!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }else{
                    //提取, 組別名稱
                    $group_name=trim($arrys_result[0]['group_name']);
                }

            //-------------------------------------------
            //檢核學校類別
            //-------------------------------------------

                $sql="
                    SELECT
                        `class_category`
                    FROM `class`
                    WHERE 1=1
                        AND `class_code`='{$sess_class_code}'
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                $numrow=count($arrys_result);

                if($numrow!==0){
                    $class_category=(int)$arrys_result[0]['class_category'];
                }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $edit_type          =mysql_prep($edit_type);

            $create_by          =(int)$sess_user_id;
            $edit_by            =(int)$sess_user_id;
            $sess_school_code   =mysql_prep(strip_tags($sess_school_code));
            $school_category    =(int)$class_category;
            $grade_id           =(int)$sess_grade;
            $classroom_id       =(int)$sess_classroom;
            $group_id           ="NULL";

            switch($edit_type){
                case '採用':
                    $group_sid=group_sid($edit_by,mb_internal_encoding());
                break;

                case '停用':
                    $group_sid=mysql_prep(strip_tags($group_sid));
                break;
            }

            $group_name     =mysql_prep(strip_tags($group_name));
            $group_sdate    ="NOW()";
            $group_mdate    ="NULL";
            $keyin_ip       =get_ip();

            $log_id         ="NULL";
            $group_edate    ="0000-00-00 00:00:00";

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            switch($edit_type){
                case '採用':
                    $sql="
                        # for mssr_group
                        INSERT INTO `mssr_group` SET
                            `create_by`         =  {$create_by          } ,
                            `edit_by`           =  {$edit_by            } ,
                            `school_code`       = '{$sess_school_code   }',
                            `grade_id`          =  {$grade_id           } ,
                            `classroom_id`      =  {$classroom_id       } ,
                            `group_id`          =  {$group_id           } ,
                            `group_sid`         = '{$group_sid          }',
                            `group_name`        = '{$group_name         }',
                            `group_sdate`       =  {$group_sdate        } ,
                            `group_mdate`       =  {$group_mdate        } ,
                            `keyin_ip`          = '{$keyin_ip           }';

                        # for mssr_group_log
                        INSERT INTO `mssr_group_log` SET
                            `create_by`         =  {$create_by          } ,
                            `edit_by`           =  {$edit_by            } ,
                            `school_code`       = '{$sess_school_code   }',
                            `grade_id`          =  {$grade_id           } ,
                            `classroom_id`      =  {$classroom_id       } ,
                            `log_id`            =  {$log_id             } ,
                            `group_sid`         = '{$group_sid          }',
                            `group_name`        = '{$group_name         }',
                            `group_sdate`       =  {$group_sdate        } ,
                            `group_edate`       = '{$group_edate        }',
                            `keyin_ip`          = '{$keyin_ip           }';
                    ";

                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);
                break;

                case '停用':
                    $sql="
                        # for mssr_group
                        DELETE FROM `mssr_group`
                        WHERE 1=1
                            AND `school_code`   ='{$sess_school_code    }'
                            AND `grade_id`      = {$grade_id            }
                            AND `classroom_id`  = {$classroom_id        }
                            AND `group_sid`     ='{$group_sid           }'
                        LIMIT 1;

                        # for mssr_group_log
                        UPDATE `mssr_group_log` SET
                            `group_edate`      =  {$group_sdate         }
                        WHERE 1=1
                            AND `school_code`   ='{$sess_school_code    }'
                            AND `grade_id`      = {$grade_id            }
                            AND `classroom_id`  = {$classroom_id        }
                            AND `group_sid`     ='{$group_sid           }'
                        LIMIT 1;
                    ";

                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);
                break;
            }

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page=str_repeat("../",1)."index.php";
        $arg =array(
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
?>