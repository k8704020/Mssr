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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_category');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //cat1_id         類別1主索引
    //cat2_id         類別2主索引
    //cat_name        類別名稱

        $post_chk=array(
            'cat1_id ',
            'cat2_id ',
            'cat_name'
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
    //cat1_id         類別1主索引
    //cat2_id         類別2主索引
    //cat_name        類別名稱

        //POST
        $cat1_id =trim($_POST[trim('cat1_id ')]);
        $cat2_id =trim($_POST[trim('cat2_id ')]);
        $cat_name=trim($_POST[trim('cat_name')]);

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
    //cat1_id         類別1主索引
    //cat2_id         類別2主索引
    //cat_name        類別名稱

        $arry_err=array();

        if((int)$cat1_id===0){
            $arry_err[]='類別1主索引,錯誤!';
        }else{
            $cat1_id=(int)$cat1_id;
        }

        if((int)$cat2_id===0){
            $arry_err[]='類別2主索引,錯誤!';
        }else{
            $cat2_id=(int)$cat2_id;
        }

        if($cat_name===''){
           $arry_err[]='類別名稱,未輸入!';
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
        //cat1_id         類別1主索引
        //cat2_id         類別2主索引
        //cat_name        類別名稱

            $cat1_id        =(int)$cat1_id;
            $cat2_id        =(int)$cat2_id;
            $cat_name       =mysql_prep($cat_name);

            $sess_user_id    =(int)$sess_user_id;
            $sess_school_code=mysql_prep($sess_school_code);
            $sess_grade      =(int)$sess_grade;
            $sess_classroom  =(int)$sess_classroom;
            $sess_class_code =mysql_prep($sess_class_code);

            $cat_lv             =0;
            if(($cat1_id===1)&&($cat2_id===1)){
                $cat_lv=1;
            }elseif(($cat1_id!==1)&&($cat2_id===1)){
                $cat_lv=2;
            }elseif(($cat1_id!==1)&&($cat2_id!==1)){
                $cat_lv=3;
            }else{
                $msg="發生嚴重錯誤, 請重新輸入!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }

            //-------------------------------------------
            //檢核類別名稱
            //-------------------------------------------

                $sql="
                    SELECT
                        `cat_name`,
                        `cat_code`
                    FROM `mssr_book_category`
                    WHERE 1=1
                        AND `school_code`   ='{$sess_school_code    }'
                        AND `cat_name`      ='{$cat_name            }'
                        AND `cat_state`     ='啟用'
                ";

                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                $numrow=count($arrys_result);

                if($numrow!==0){
                    $cat_code=mysql_prep(trim($arrys_result[0]['cat_code']));
                    $msg="類別名稱已重複, 你要設置路徑嗎?";
                    $jscript_back="
                        <script>
                            var cat_code='{$cat_code}';
                            if(confirm('{$msg}')){
                                location.href='../edit/editF.php?cat_code='+cat_code;
                            }else{
                                history.back(-1);
                            }
                        </script>
                    ";
                    die($jscript_back);
                }

            //-------------------------------------------
            //檢核各類別最後的號碼
            //-------------------------------------------

                switch($cat_lv){

                    case 1:
                    //第一階
                        $sql="
                            SELECT
                                `cat1_id`
                            FROM `mssr_book_category`
                            WHERE 1=1
                                AND `school_code` ='{$sess_school_code  }'
                            ORDER BY `cat1_id` DESC
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        $last_cat1_id=(int)$arrys_result[0]['cat1_id']+1;
                    break;

                    case 2:
                    //第二階
                        $sql="
                            SELECT
                                `cat2_id`
                            FROM `mssr_book_category`
                            WHERE 1=1
                                AND `school_code` ='{$sess_school_code  }'
                                AND `cat1_id`     = {$cat1_id}
                            ORDER BY `cat2_id` DESC
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        $last_cat2_id=(int)$arrys_result[0]['cat2_id']+1;
                    break;

                    case 3:
                    //第三階
                        $sql="
                            SELECT
                                `cat3_id`
                            FROM `mssr_book_category`
                            WHERE 1=1
                                AND `school_code` ='{$sess_school_code  }'
                                AND `cat1_id`     = {$cat1_id}
                                AND `cat2_id`     = {$cat2_id}
                            ORDER BY `cat3_id` DESC
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        $last_cat3_id=(int)$arrys_result[0]['cat3_id']+1;
                    break;

                    default:
                        $msg="發生嚴重錯誤, 請重新輸入!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                history.back(-1);
                            </script>
                        ";
                        die($jscript_back);
                    break;
                }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $create_by          =(int)$sess_user_id;
            $edit_by            =(int)$sess_user_id;
            $sess_school_code   =mysql_prep(strip_tags($sess_school_code));
            $cat_id             ="NULL";
            $cat_name           =mysql_prep(strip_tags($cat_name));
            $cat_code           =cat_code($create_by,mb_internal_encoding());
            $cat1_id            =(int)$cat1_id;
            $cat2_id            =(int)$cat2_id;
            $cat_state          ='啟用';
            $keyin_cdate        ="NOW()";
            $keyin_mdate        ="NULL";

            if(isset($last_cat1_id)){$last_cat1_id=(int)$last_cat1_id;}
            if(isset($last_cat2_id)){$last_cat2_id=(int)$last_cat2_id;}
            if(isset($last_cat3_id)){$last_cat3_id=(int)$last_cat3_id;}

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            switch($cat_lv){
                case 1:
                    $sql="
                        # for mssr_book_category
                        INSERT INTO `mssr_book_category` SET
                            `create_by`         =  {$create_by          } ,
                            `edit_by`           =  {$edit_by            } ,
                            `school_code`       = '{$sess_school_code   }',
                            `cat_id`            =  {$cat_id             } ,
                            `cat_name`          = '{$cat_name           }',
                            `cat_code`          = '{$cat_code           }',
                            `cat1_id`           =  {$last_cat1_id       } ,
                            `cat2_id`           =  1                      ,
                            `cat3_id`           =  1                      ,
                            `cat_state`         = '{$cat_state          }',
                            `keyin_cdate`       =  {$keyin_cdate        } ,
                            `keyin_mdate`       =  {$keyin_mdate        } ;
                    ";
                break;

                case 2:
                    $sql="
                        # for mssr_book_category
                        INSERT INTO `mssr_book_category` SET
                            `create_by`         =  {$create_by          } ,
                            `edit_by`           =  {$edit_by            } ,
                            `school_code`       = '{$sess_school_code   }',
                            `cat_id`            =  {$cat_id             } ,
                            `cat_name`          = '{$cat_name           }',
                            `cat_code`          = '{$cat_code           }',
                            `cat1_id`           =  {$cat1_id            } ,
                            `cat2_id`           =  {$last_cat2_id       } ,
                            `cat3_id`           =  1                      ,
                            `cat_state`         = '{$cat_state          }',
                            `keyin_cdate`       =  {$keyin_cdate        } ,
                            `keyin_mdate`       =  {$keyin_mdate        } ;
                    ";
                break;

                case 3:
                    $sql="
                        # for mssr_book_category
                        INSERT INTO `mssr_book_category` SET
                            `create_by`         =  {$create_by          } ,
                            `edit_by`           =  {$edit_by            } ,
                            `school_code`       = '{$sess_school_code   }',
                            `cat_id`            =  {$cat_id             } ,
                            `cat_name`          = '{$cat_name           }',
                            `cat_code`          = '{$cat_code           }',
                            `cat1_id`           =  {$cat1_id            } ,
                            `cat2_id`           =  {$cat2_id            } ,
                            `cat3_id`           =  {$last_cat3_id       } ,
                            `cat_state`         = '{$cat_state          }',
                            `keyin_cdate`       =  {$keyin_cdate        } ,
                            `keyin_mdate`       =  {$keyin_mdate        } ;
                    ";
                break;

                default:
                    $msg="發生嚴重錯誤, 請重新輸入!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                break;
            }

            //送出
            $err ='DB QUERY FAIL';
            $sth=$conn_mssr->prepare($sql);
            $sth->execute()or die($err);

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page=str_repeat("../",1)."index.php";
        $arg =array(
            'psize'=>$psize,
            'pinx' =>1
        );
        $arg =http_build_query($arg);

        if(!empty($arg)){
            $url="{$page}?{$arg}";
        }else{
            $url="{$page}";
        }

        header("Location: {$url}");
?>