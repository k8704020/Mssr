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
        require_once(str_repeat("../",6).'config/config.php');

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
            $url=str_repeat("../",7).'index.php';
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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_book');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //cat1_id         類別1主索引
    //cat2_id         類別2主索引
    //cat3_id         類別3主索引
    //book_type       書籍類別
    //book_sid        書籍識別碼
    //has_cat_code_lv 已擁有該類別的階層

        $post_chk=array(
            'book_type      ',
            'book_sid       ',
            'has_cat_code_lv'
        );
        $post_chk=array_map("trim",$post_chk);
        foreach($post_chk as $post){
            if(!isset($_POST[$post])){
                die();
            }
        }

        //特殊處理
        if((!isset($_POST['cat1_id']))||(!isset($_POST['cat2_id']))||(!isset($_POST['cat3_id']))){
            die();
        }else{
            $arry_cat1_id_cno=count($_POST['cat1_id']);
            $arry_cat2_id_cno=count($_POST['cat2_id']);
            $arry_cat3_id_cno=count($_POST['cat3_id']);
            if($arry_cat1_id_cno!==$arry_cat2_id_cno)die();
            if($arry_cat2_id_cno!==$arry_cat3_id_cno)die();
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //cat1_id         類別1主索引
    //cat2_id         類別2主索引
    //cat3_id         類別3主索引
    //book_type       書籍類別
    //book_sid        書籍識別碼
    //has_cat_code_lv 已擁有該類別的階層

        //POST
        $arry_cat1_id       =array_map("trim",$_POST[trim('cat1_id  ')]);
        $arry_cat2_id       =array_map("trim",$_POST[trim('cat2_id  ')]);
        $arry_cat3_id       =array_map("trim",$_POST[trim('cat3_id  ')]);
        $cat_group_cno      =(int)$arry_cat1_id_cno;

        $book_type          =trim($_POST[trim('book_type            ')]);
        $book_sid           =trim($_POST[trim('book_sid             ')]);
        $has_cat_code_lv    =trim($_POST[trim('has_cat_code_lv      ')]);

        //SESSION
        $sess_user_id       =(int)$sess_login_info['uid'];
        $sess_permission    =trim($sess_login_info['permission']);
        $sess_school_code   =trim($sess_login_info['school_code']);
        $sess_class_code    =trim($sess_login_info['arrys_class_code'][0]['class_code']);
        $sess_grade         =(int)$sess_login_info['arrys_class_code'][0]['grade'];
        $sess_classroom     =(int)$sess_login_info['arrys_class_code'][0]['classroom'];

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
    //cat3_id         類別3主索引
    //book_type       書籍類別
    //book_sid        書籍識別碼
    //has_cat_code_lv 已擁有該類別的階層

        $arry_err=array();

        foreach($arry_cat1_id as $cat1_id){
            if((int)$cat1_id===0){
                $arry_err[]='類別1主索引,錯誤!';
            }
        }

        foreach($arry_cat2_id as $cat2_id){
            if((int)$cat2_id===0){
                $arry_err[]='類別2主索引,錯誤!';
            }
        }

        foreach($arry_cat3_id as $cat3_id){
            if((int)$cat3_id===0){
                $arry_err[]='類別3主索引,錯誤!';
            }
        }

        if($book_type===''){
           $arry_err[]='書籍類別,未輸入!';
        }else{
            $book_type=trim($book_type);
            if(!in_array($book_type,array("mssr_book_class","mssr_book_library"))){
                $arry_err[]='書籍類別,錯誤!';
            }
        }

        if($book_sid===''){
           $arry_err[]='書籍識別碼,未輸入!';
        }

        if($has_cat_code_lv===''){
           $arry_err[]='已擁有該類別的階層,未輸入!';
        }else{
            $has_cat_code_lv=(int)$has_cat_code_lv;
            if(!in_array($has_cat_code_lv,array(0,1,2,3))){
                $arry_err[]='已擁有該類別的階層,錯誤!';
            }
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

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //cat1_id         類別1主索引
        //cat2_id         類別2主索引
        //cat3_id         類別3主索引
        //book_type       書籍類別
        //book_sid        書籍識別碼
        //has_cat_code_lv 已擁有該類別的階層

            $arry_cat1_id    =$arry_cat1_id;
            $arry_cat2_id    =$arry_cat2_id;
            $arry_cat3_id    =$arry_cat3_id;

            $book_type       =mysql_prep($book_type);
            $book_sid        =mysql_prep($book_sid);

            $sess_user_id    =(int)$sess_user_id;
            $sess_school_code=mysql_prep($sess_school_code);
            $sess_grade      =(int)$sess_grade;
            $sess_classroom  =(int)$sess_classroom;
            $sess_class_code =mysql_prep($sess_class_code);

            $arrys_cat_code  =array();

            //-------------------------------------------
            //檢核書籍資料
            //-------------------------------------------

                $query_sql="
                    SELECT
                        `book_sid`
                    FROM `{$book_type}`
                    WHERE 1=1
                        AND `book_sid`='{$book_sid}'
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(0,1),$arry_conn_mssr);
                $numrow=count($arrys_result);
                if($numrow===0){
                    $msg="書本不存在, 請重新輸入!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }

            //-------------------------------------------
            //檢核各類別組相對應代號
            //-------------------------------------------

                for($i=0;$i<$cat_group_cno;$i++){

                    $cat1_id=(int)$arry_cat1_id[$i];
                    $cat2_id=(int)$arry_cat2_id[$i];
                    $cat3_id=(int)$arry_cat3_id[$i];

                    $query_sql="
                        SELECT
                            `cat_name`,
                            `cat_code`
                        FROM `mssr_book_category`
                        WHERE 1=1
                            AND `mssr_book_category`.`school_code`  ='{$sess_school_code  }'
                            AND `mssr_book_category`.`cat1_id`      = {$cat1_id           }
                            AND `mssr_book_category`.`cat2_id`      = {$cat2_id           }
                            AND `mssr_book_category`.`cat3_id`      = {$cat3_id           }
                    ";
                    $db_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(0,1),$arry_conn_mssr);
                    if(empty($db_results)){
                        $msg="發生嚴重錯誤, 請重新輸入!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                history.back(-1);
                            </script>
                        ";
                        die($jscript_back);
                        break;
                    }else{
                        $rs_cat_name=trim($db_results[0]['cat_name']);
                        $rs_cat_code=trim($db_results[0]['cat_code']);

                        $arrys_cat_code[$i]['cat_name']=$rs_cat_name;
                        $arrys_cat_code[$i]['cat_code']=$rs_cat_code;
                        $arrys_cat_code[$i]['cat1_id'] =$cat1_id;
                        $arrys_cat_code[$i]['cat2_id'] =$cat2_id;
                        $arrys_cat_code[$i]['cat3_id'] =$cat3_id;
                    }
                }

                //---------------------------------------
                //各階層對應處理
                //---------------------------------------

                    $cat_code_rev_cno=0;
                    $arrys_cat_code_rev=array();

                    foreach($arrys_cat_code as $inx1=>$arry_cat_code){

                        $q_cat_code=mysql_prep(trim($arry_cat_code['cat_code']));
                        $q_cat1_id =(int)$arry_cat_code['cat1_id'];
                        $q_cat2_id =(int)$arry_cat_code['cat2_id'];
                        $q_cat3_id =(int)$arry_cat_code['cat3_id'];

                        $query_sql="
                            SELECT *
                            FROM `mssr_book_category`
                            WHERE 1=1
                                AND `mssr_book_category`.`school_code`='{$sess_school_code}'
                                AND `mssr_book_category`.`cat_code`   ='{$q_cat_code      }'
                        ";

                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
                        if(empty($arrys_result)){
                            $msg="查無資料!";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    history.back(-1);
                                </script>
                            ";
                            die($jscript_back);
                        }

                        foreach($arrys_result as $inx2=>$arry_result){

                            //初始化, 階層數指標
                            $cat_lv_tmp_flag=0;

                            $cat_name=trim($arry_result['cat_name']);
                            $cat_code=trim($arry_result['cat_code']);
                            $cat1_id =(int)$arry_result['cat1_id'];
                            $cat2_id =(int)$arry_result['cat2_id'];
                            $cat3_id =(int)$arry_result['cat3_id'];

                            //---------------------------
                            //第一階層對應
                            //---------------------------

                                if(($cat1_id!==1)){
                                    $catlv1_sql="
                                        SELECT *
                                        FROM `mssr_book_category`
                                        WHERE 1=1
                                            AND `mssr_book_category`.`cat1_id`<>1
                                            AND `mssr_book_category`.`school_code`='{$sess_school_code}'
                                            AND `mssr_book_category`.`cat1_id`={$cat1_id}
                                            AND `mssr_book_category`.`cat2_id`=1
                                            AND `mssr_book_category`.`cat3_id`=1
                                    ";
                                    $catlv1_sql_result=db_result($conn_type='pdo',$conn_mssr,$catlv1_sql,array(0,1),$arry_conn_mssr);
                                    if(!empty($catlv1_sql_result)){
                                        $cat_lv_tmp_flag++;
                                        $rs_cat_name=trim($catlv1_sql_result[0]['cat_name']);
                                        $rs_cat_code=trim($catlv1_sql_result[0]['cat_code']);
                                        $arrys_cat_code_rev[$cat_code_rev_cno]['cat_lv_flag']=$cat_lv_tmp_flag;
                                        $arrys_cat_code_rev[$cat_code_rev_cno]['cat1_name']=$rs_cat_name;
                                        $arrys_cat_code_rev[$cat_code_rev_cno]['cat1_code']=$rs_cat_code;
                                    }
                                }

                            //---------------------------
                            //第二階層對應
                            //---------------------------

                                if(($cat1_id!==1)&&($cat2_id!==1)&&($cat3_id!==1)){
                                    $catlv2_sql="
                                        SELECT *
                                        FROM `mssr_book_category`
                                        WHERE 1=1
                                            AND `mssr_book_category`.`cat1_id`<>1
                                            AND `mssr_book_category`.`school_code`='{$sess_school_code}'
                                            AND `mssr_book_category`.`cat1_id`={$cat1_id}
                                            AND `mssr_book_category`.`cat2_id`={$cat2_id}
                                            AND `mssr_book_category`.`cat3_id`=1
                                    ";
                                    $catlv2_sql_result=db_result($conn_type='pdo',$conn_mssr,$catlv2_sql,array(0,1),$arry_conn_mssr);
                                    if(!empty($catlv2_sql_result)){
                                        $cat_lv_tmp_flag++;
                                        $rs_cat_name=trim($catlv2_sql_result[0]['cat_name']);
                                        $rs_cat_code=trim($catlv2_sql_result[0]['cat_code']);
                                        $arrys_cat_code_rev[$cat_code_rev_cno]['cat_lv_flag']=$cat_lv_tmp_flag;
                                        $arrys_cat_code_rev[$cat_code_rev_cno]['cat2_name']=$rs_cat_name;
                                        $arrys_cat_code_rev[$cat_code_rev_cno]['cat2_code']=$rs_cat_code;
                                    }
                                }

                            //---------------------------
                            //回填最終階層
                            //---------------------------

                                if($q_cat2_id!==1){
                                    $cat_lv_tmp_flag++;
                                    $arrys_cat_code_rev[$cat_code_rev_cno]['cat_lv_flag']=$cat_lv_tmp_flag;
                                    $arrys_cat_code_rev[$cat_code_rev_cno]['cat3_name']=$cat_name;
                                    $arrys_cat_code_rev[$cat_code_rev_cno]['cat3_code']=$cat_code;
                                }
                            $cat_code_rev_cno++;
                        }
                    }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $create_by          =(int)$sess_user_id;
            $sess_school_code   =mysql_prep(strip_tags($sess_school_code));
            $book_sid           =mysql_prep(strip_tags($book_sid));
            $rev_id             ="NULL";
            $keyin_cdate        ="NOW()";

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            //-------------------------------------------
            //刪除書籍相關類別的資料
            //-------------------------------------------

                $sql="
                    # for mssr_book_category_rev
                    DELETE FROM `mssr_book_category_rev`
                    WHERE 1=1
                        AND `mssr_book_category_rev`.`school_code` ='{$sess_school_code  }'
                        AND `mssr_book_category_rev`.`book_sid`    ='{$book_sid          }';
                ";
                //送出
                $conn_mssr->exec($sql);

            //-------------------------------------------
            //檢核書籍最後一組類別組別
            //-------------------------------------------

                $sql="
                    SELECT
                        `cat_group`
                    FROM `mssr_book_category_rev`
                    WHERE 1=1
                        AND `mssr_book_category_rev`.`school_code` ='{$sess_school_code  }'
                        AND `mssr_book_category_rev`.`book_sid`    ='{$book_sid          }'
                    ORDER BY `cat_group` DESC
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(!empty($arrys_result)){
                    $cat_group=((int)$arrys_result[0]['cat_group'])+1;
                }else{
                    $cat_group=(int)1;
                }

            //-------------------------------------------
            //新增書籍相關類別的資料
            //-------------------------------------------

                foreach($arrys_cat_code_rev as $arry_cat_code_rev){

                    //判斷
                    if(isset($arry_cat_code_rev['cat1_code'])){

                        $cat1_code=mysql_prep(strip_tags(trim($arry_cat_code_rev['cat1_code'])));

                        $sql="
                            # for mssr_book_category_rev
                            INSERT INTO `mssr_book_category_rev` SET
                                `create_by`         =  {$create_by          } ,
                                `school_code`       = '{$sess_school_code   }',
                                `cat_code`          = '{$cat1_code          }',
                                `book_sid`          = '{$book_sid           }',
                                `rev_id`            =  {$rev_id             } ,
                                `cat_group`         =  {$cat_group          } ,
                                `keyin_cdate`       =  {$keyin_cdate        } ;
                        ";
                        //echo "<Pre>";
                        //print_r($sql);
                        //echo "</Pre>";

                        //送出
                        $conn_mssr->exec($sql);
                    }

                    if(isset($arry_cat_code_rev['cat2_code'])){

                        $cat2_code=mysql_prep(strip_tags(trim($arry_cat_code_rev['cat2_code'])));

                        $sql="
                            # for mssr_book_category_rev
                            INSERT INTO `mssr_book_category_rev` SET
                                `create_by`         =  {$create_by          } ,
                                `school_code`       = '{$sess_school_code   }',
                                `cat_code`          = '{$cat2_code          }',
                                `book_sid`          = '{$book_sid           }',
                                `rev_id`            =  {$rev_id             } ,
                                `cat_group`         =  {$cat_group          } ,
                                `keyin_cdate`       =  {$keyin_cdate        } ;
                        ";
                        //echo "<Pre>";
                        //print_r($sql);
                        //echo "</Pre>";

                        //送出
                        $conn_mssr->exec($sql);
                    }

                    if(isset($arry_cat_code_rev['cat3_code'])){

                        $cat3_code=mysql_prep(strip_tags(trim($arry_cat_code_rev['cat3_code'])));

                        $sql="
                            # for mssr_book_category_rev
                            INSERT INTO `mssr_book_category_rev` SET
                                `create_by`         =  {$create_by          } ,
                                `school_code`       = '{$sess_school_code   }',
                                `cat_code`          = '{$cat3_code          }',
                                `book_sid`          = '{$book_sid           }',
                                `rev_id`            =  {$rev_id             } ,
                                `cat_group`         =  {$cat_group          } ,
                                `keyin_cdate`       =  {$keyin_cdate        } ;
                        ";
                        //echo "<Pre>";
                        //print_r($sql);
                        //echo "</Pre>";

                        //送出
                        $conn_mssr->exec($sql);
                    }
                    $cat_group++;
                }

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page=str_repeat("../",1)."categoryF.php";
        $arg =array(
            'psize'    =>$psize,
            'pinx'     =>$pinx,
            'book_type'=>$book_type,
            'book_sid' =>$book_sid
        );
        $arg =http_build_query($arg);

        if(!empty($arg)){
            $url="{$page}?{$arg}";
        }else{
            $url="{$page}";
        }

        header("Location: {$url}");
?>