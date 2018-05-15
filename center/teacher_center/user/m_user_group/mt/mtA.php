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

        $post_chk=array(

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

        //POST

        //SESSION
        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);
        $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
        $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
        $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];

        //分頁
        $psize=(isset($_POST['psize']))?(int)$_POST['psize']:10;
        $pinx =(isset($_POST['pinx']))?(int)$_POST['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

        $arry_err=array();

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

            $sess_user_id       =(int)$sess_user_id;
            $sess_grade         =(int)$sess_grade;
            $sess_classroom     =(int)$sess_classroom;
            $sess_class_code    =mysql_prep($sess_class_code);
            $sess_school_code   =mysql_prep($sess_school_code);
            $date               =date("Y-m-d");

            //初始化, 學生陣列
            $arrys_user=array();

            //初始化, 已啟用的組別陣列
            $arrys_group=array();

        //---------------------------------------------------
        //學期時間
        //---------------------------------------------------

            $curdate=date("Y-m-d");

            if(in_array($auth_sys_check_lv,array(99))){
                $sql="
                    SELECT
                        `start`,
                        `end`
                    FROM `semester`
                    WHERE 1=1
                        AND `start` <='{$curdate}'
                        AND `end`   >='{$curdate}'
                    ORDER BY `end` DESC
                ";
            }else{
                $sql="
                    SELECT
                        `start`,
                        `end`
                    FROM `semester`
                    WHERE 1=1
                        #AND `uid`={$sess_user_id}
                        AND `start` <='{$curdate}'
                        AND `end`   >='{$curdate}'
                    ORDER BY `end` DESC
                ";
            }
            $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
            if(!empty($arrys_result)){
                $semester_start=trim($arrys_result[0]['start']);
                $semester_end=trim($arrys_result[0]['end']);
            }else{
                die();
            }

        //---------------------------------------------------
        //已啟用的組別陣列
        //---------------------------------------------------

            $query_sql="
                SELECT
                    `group_sid`
                FROM `mssr_group`
                WHERE 1=1
                    AND `school_code` ='{$sess_school_code  }'
                    AND `grade_id`    = {$sess_grade        }
                    AND `classroom_id`= {$sess_classroom    }
                    AND `group_sdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                #組別亂序
                ORDER BY RAND()
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
            foreach($arrys_result as $inx=>$arry_result){
                $group_sid=trim($arry_result['group_sid']);

                //匯入, 已啟用的組別資訊
                $arrys_group[$inx]=$group_sid;
            }
            if(empty($arrys_group)){
                $msg="無任何可分組的組別!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            //-------------------------------------------
            //分組設定
            //-------------------------------------------

                //初始化, 新的組別
                $new_arrys_group_user=array();
                $cno=0;

                foreach($arrys_group as $inx=>$group_sid){
                    $group_sid=trim($group_sid);

                    if(isset($_POST[$group_sid])){
                        $users_id=$_POST[$group_sid];
                        $arrys_user=explode(",",$users_id);

//echo "<Pre>";print_r($group_sid);echo "</Pre>";
//echo "<Pre>";print_r($arrys_user);echo "</Pre>";

                        //去除陣列空值
                        $arrys_user=array_diff($arrys_user,array(null,'null','',' '));

//echo "<Pre>";print_r($arrys_user);echo "</Pre>";

                        foreach($arrys_user as $inx=>$user_id){
                            $user_id=(int)$user_id;
                            $cno=$inx+1;

                            //匯入新的組別
                            $new_arrys_group_user[$cno][$group_sid]=$user_id;
                        }

                        if(!isset($new_arrys_group_user[$cno])){
                            $new_arrys_group_user[$cno][$group_sid]=0;
                        }
                    }else{
                        $msg="組別錯誤!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                history.back(-1);
                            </script>
                        ";
                        die($jscript_back);
                    }
                    $cno++;
                }
//echo "<Pre>";print_r($new_arrys_group_user);echo "</Pre>";
//die();
            //-------------------------------------------
            //參數設置
            //-------------------------------------------

                $create_by  =(int)$sess_user_id;
                $edit_by    =(int)$sess_user_id;
                $keyin_cdate="NOW()";
                $keyin_mdate="NULL";
                $keyin_ip   =get_ip();

                $log_id     ="NULL";

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            //-------------------------------------------
            //移除所有分組紀錄
            //-------------------------------------------

                $query_sql="
                    SELECT
                        `uid`
                    FROM `student`
                    WHERE 1=1
                        AND `student`.`class_code`='{$sess_class_code}'
                        #AND `student`.`start`<'{$date}'
                        #AND `student`.`end`>'{$date}'
                    #人數亂序
                    ORDER BY RAND()
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_user,$query_sql,array(),$arry_conn_user);
                foreach($arrys_result as $inx=>$arry_result){
                    $uid=(int)$arry_result['uid'];

                    //匯入, 學生陣列
                    $arrys_user[$inx]=$uid;
                }
                if(empty($arrys_user)){
                    $msg="無任何可分組的學生!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }else{
                //---------------------------------------
                //老師陣列
                //---------------------------------------

                    $sess_class_code=mysql_prep(trim($sess_login_info['arrys_class_code'][0]['class_code']));

                    $sql="
                        SELECT
                            `uid`
                        FROM `teacher`
                        WHERE 1=1
                            AND `class_code`='{$sess_class_code}'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
                    if(!empty($arrys_result)){
                        foreach($arrys_result as $arry_result){
                            $uid=(int)$arry_result['uid'];
                            //匯入, 老師主索引
                            $arrys_user[]=$uid;
                        }
                    }
                }

                $arry_group =array();
                $group_lists='';
                foreach($new_arrys_group_user as $inx=>$new_arry_group_user){
                    foreach($new_arry_group_user as $group_sid=>$user_id){
                        $arry_group[]=trim($group_sid);
                    }
                }
                $arry_group=array_unique($arry_group);
                $group_lists="'".implode("','",$arry_group)."'";

                foreach($arrys_user as $inx=>$del_user_id){

                    $del_user_id=(int)$del_user_id;

                    $sql="
                        # for mssr_user_group
                        DELETE FROM `mssr_user_group`
                        WHERE 1=1
                            AND `user_id`= {$del_user_id}
                            AND `group_sid` IN ({$group_lists})
                    ";
                    //送出
                    $conn_mssr->exec($sql);
                }

            //-------------------------------------------
            //重新分組
            //-------------------------------------------

                foreach($new_arrys_group_user as $inx=>$new_arry_group_user){
                    foreach($new_arry_group_user as $group_sid=>$user_id){

                        $user_id=(int)$user_id;
                        $group_sid=mysql_prep(strip_tags($group_sid));

                        if($user_id===0)continue;

                        $sql="
                            # for mssr_user_group
                            INSERT IGNORE INTO `mssr_user_group` SET
                                `create_by`         =  {$create_by  } ,
                                `edit_by`           =  {$edit_by    } ,
                                `group_sid`         = '{$group_sid  }',
                                `user_id`           =  {$user_id    } ,
                                `keyin_cdate`       =  {$keyin_cdate} ,
                                `keyin_mdate`       =  {$keyin_mdate} ,
                                `keyin_ip`          = '{$keyin_ip   }';
                        ";
                        //送出
                        $conn_mssr->exec($sql);

                        $sql="
                            # for mssr_user_group_log
                            INSERT IGNORE INTO `mssr_user_group_log` SET
                                `create_by`         =  {$create_by  } ,
                                `group_sid`         = '{$group_sid  }',
                                `user_id`           =  {$user_id    } ,
                                `log_id`            =  {$log_id     } ,
                                `keyin_cdate`       =  {$keyin_cdate} ,
                                `keyin_ip`          = '{$keyin_ip   }';
                        ";
                        //送出
                        $conn_mssr->exec($sql);
                    }
                }

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page=str_repeat("../",0)."mtF.php";
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