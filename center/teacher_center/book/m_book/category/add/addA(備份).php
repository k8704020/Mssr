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
            'cat1_id        ',
            'cat2_id        ',
            'cat3_id        ',
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
        $cat1_id            =trim($_POST[trim('cat1_id          ')]);
        $cat2_id            =trim($_POST[trim('cat2_id          ')]);
        $cat3_id            =trim($_POST[trim('cat3_id          ')]);
        $book_type          =trim($_POST[trim('book_type        ')]);
        $book_sid           =trim($_POST[trim('book_sid         ')]);
        $has_cat_code_lv    =trim($_POST[trim('has_cat_code_lv  ')]);

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

        if((int)$cat3_id===0){
            $arry_err[]='類別3主索引,錯誤!';
        }else{
            $cat3_id=(int)$cat3_id;
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

            $cat1_id         =(int)$cat1_id;
            $cat2_id         =(int)$cat2_id;
            $cat3_id         =(int)$cat3_id;
            $book_type       =mysql_prep($book_type);
            $book_sid        =mysql_prep($book_sid);
            $has_cat_code_lv =(int)$has_cat_code_lv;
            $cat_group       =1;
            $arrys_cat_code  =array();

            $sess_user_id    =(int)$sess_user_id;
            $sess_school_code=mysql_prep($sess_school_code);
            $sess_grade      =(int)$sess_grade;
            $sess_classroom  =(int)$sess_classroom;
            $sess_class_code =mysql_prep($sess_class_code);

            $cat_lv          =0;
            if(($cat1_id!==1)&&($cat2_id===1)&&($cat3_id===1)){
                $cat_lv=1;
            }elseif(($cat1_id!==1)&&($cat2_id!==1)&&($cat3_id===1)){
                $cat_lv=2;
            }elseif(($cat1_id!==1)&&($cat2_id!==1)&&($cat3_id!==1)){
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
            //檢核各類別相對應代號
            //-------------------------------------------

                switch($cat_lv){

                    case 1:
                    //第一階
                        $sql="
                            SELECT
                                `cat_code`
                            FROM `mssr_book_category`
                            WHERE 1=1
                                AND `mssr_book_category`.`school_code` ='{$sess_school_code  }'
                                AND `mssr_book_category`.`cat1_id`={$cat1_id}
                                AND `mssr_book_category`.`cat2_id`=1
                                AND `mssr_book_category`.`cat3_id`=1
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(!empty($arrys_result)){
                            $rs_cat_code=trim($arrys_result[0]['cat_code']);
                            $arrys_cat_code[]=$rs_cat_code;
                        }
                    break;

                    case 2:
                    //第二階
                        $sql="
                            SELECT
                                `cat_code`
                            FROM `mssr_book_category`
                            WHERE 1=1
                                AND `mssr_book_category`.`school_code` ='{$sess_school_code  }'
                                AND `mssr_book_category`.`cat1_id`={$cat1_id}
                                AND `mssr_book_category`.`cat2_id`=1
                                AND `mssr_book_category`.`cat3_id`=1
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(!empty($arrys_result)){
                            $rs_cat_code=trim($arrys_result[0]['cat_code']);
                            $arrys_cat_code[]=$rs_cat_code;
                        }

                        $sql="
                            SELECT
                                `cat_code`
                            FROM `mssr_book_category`
                            WHERE 1=1
                                AND `mssr_book_category`.`school_code` ='{$sess_school_code  }'
                                AND `mssr_book_category`.`cat1_id`={$cat1_id}
                                AND `mssr_book_category`.`cat2_id`={$cat2_id}
                                AND `mssr_book_category`.`cat3_id`=1
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(!empty($arrys_result)){
                            $rs_cat_code=trim($arrys_result[0]['cat_code']);
                            $arrys_cat_code[]=$rs_cat_code;
                        }
                    break;

                    case 3:
                    //第三階
                        $sql="
                            SELECT
                                `cat_code`
                            FROM `mssr_book_category`
                            WHERE 1=1
                                AND `mssr_book_category`.`school_code` ='{$sess_school_code  }'
                                AND `mssr_book_category`.`cat1_id`={$cat1_id}
                                AND `mssr_book_category`.`cat2_id`=1
                                AND `mssr_book_category`.`cat3_id`=1
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(!empty($arrys_result)){
                            $rs_cat_code=trim($arrys_result[0]['cat_code']);
                            $arrys_cat_code[]=$rs_cat_code;
                        }

                        $sql="
                            SELECT
                                `cat_code`
                            FROM `mssr_book_category`
                            WHERE 1=1
                                AND `mssr_book_category`.`school_code` ='{$sess_school_code  }'
                                AND `mssr_book_category`.`cat1_id`={$cat1_id}
                                AND `mssr_book_category`.`cat2_id`={$cat2_id}
                                AND `mssr_book_category`.`cat3_id`=1
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(!empty($arrys_result)){
                            $rs_cat_code=trim($arrys_result[0]['cat_code']);
                            $arrys_cat_code[]=$rs_cat_code;
                        }

                        $sql="
                            SELECT
                                `cat_code`
                            FROM `mssr_book_category`
                            WHERE 1=1
                                AND `mssr_book_category`.`school_code` ='{$sess_school_code  }'
                                AND `mssr_book_category`.`cat1_id`={$cat1_id}
                                AND `mssr_book_category`.`cat2_id`={$cat2_id}
                                AND `mssr_book_category`.`cat3_id`={$cat3_id}
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(!empty($arrys_result)){
                            $rs_cat_code=trim($arrys_result[0]['cat_code']);
                            $arrys_cat_code[]=$rs_cat_code;
                        }
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

                //查無各類別相對應代號
                if(empty($arrys_cat_code)){
                    $msg="發生嚴重錯誤, 請重新輸入!";
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

            $create_by          =(int)$sess_user_id;
            $sess_school_code   =mysql_prep(strip_tags($sess_school_code));

            $arrys_cat_code     =array_map("strip_tags",$arrys_cat_code);
            $arrys_cat_code     =array_map("mysql_prep",$arrys_cat_code);

            $book_sid           =mysql_prep(strip_tags($book_sid));
            $rev_id             ="NULL";
            $keyin_cdate        ="NOW()";

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            //-------------------------------------------
            //刪除
            //-------------------------------------------

                //---------------------------------------
                //判斷
                //---------------------------------------

                    $cat_lv=(int)$cat_lv;
                    $has_cat_code_lv=(int)$has_cat_code_lv;
                    if(isset($arrys_cat_code[0])){
                        $cat_code1=mysql_prep((trim($arrys_cat_code[0])));
                    }
                    if(isset($arrys_cat_code[1])){
                        $cat_code2=mysql_prep((trim($arrys_cat_code[1])));
                    }
                    $arry_del_cat_grouplist =array();
                    $arry_del_rev_id_list   =array();

                    switch($has_cat_code_lv){

                        case 0:
                        //無對應資料

                        break;

                        case 1:
                        //一階層
                            if($cat_lv===1){
                                $msg="發生嚴重錯誤, 請重新輸入!";
                                $jscript_back="
                                    <script>
                                        alert('{$msg}');
                                        history.back(-1);
                                    </script>
                                ";
                                die($jscript_back);
                            }elseif(($cat_lv===2)||($cat_lv===3)){
                            //-----------------------------
                            //檢核
                            //-----------------------------

                                $sql="
                                    SELECT
                                        `rev_id`,
                                        `cat_group`
                                    FROM `mssr_book_category_rev`
                                    WHERE 1=1
                                        AND `mssr_book_category_rev`.`school_code` ='{$sess_school_code  }'
                                        AND `mssr_book_category_rev`.`cat_code`    ='{$cat_code1         }'
                                        AND `mssr_book_category_rev`.`book_sid`    ='{$book_sid          }'
                                ";
                                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                if(!empty($arrys_result)){
                                    $arry_del_cat_grouplist=array();
                                    $arry_del_rev_id_list=array();
                                    foreach($arrys_result as $arry_result){
                                        $rs_rev_id=(int)$arry_result['rev_id'];
                                        $rs_cat_group=(int)$arry_result['cat_group'];
                                        $arry_del_cat_grouplist[$rs_cat_group]=$rs_rev_id;
                                        $arry_del_rev_id_list[$rs_rev_id]=$rs_rev_id;
                                    }
                                }

                                if(!empty($arry_del_cat_grouplist)){
                                    foreach($arry_del_cat_grouplist as $rs_cat_group=>$rs_rev_id){
                                        $rs_rev_id=(int)$rs_rev_id;
                                        $rs_cat_group=(int)$rs_cat_group;
                                        $sql="
                                            SELECT
                                                `rev_id`
                                            FROM `mssr_book_category_rev`
                                            WHERE 1=1
                                                AND `mssr_book_category_rev`.`school_code` ='{$sess_school_code  }'
                                                AND `mssr_book_category_rev`.`cat_code`   <>'{$cat_code1         }'
                                                AND `mssr_book_category_rev`.`book_sid`    ='{$book_sid          }'
                                                AND `mssr_book_category_rev`.`cat_group`   = {$rs_cat_group      }
                                        ";
                                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                        if(!empty($arrys_result)){
                                            foreach($arrys_result as $arry_result){
                                                $result_rev_id=(int)$arry_result['rev_id'];
                                                if(in_array($result_rev_id,$arry_del_rev_id_list)){
                                                    unset($arry_del_rev_id_list[$rs_rev_id]);
                                                }
                                            }
                                        }
                                    }
                                }

                                if(!empty($arry_del_rev_id_list)){
                                    foreach($arry_del_rev_id_list as $rs_rev_id){
                                        $rs_rev_id=(int)$rs_rev_id;
                                        $sql="
                                            # for mssr_book_category_rev
                                            DELETE FROM `mssr_book_category_rev`
                                            WHERE 1=1
                                                AND `mssr_book_category_rev`.`school_code` ='{$sess_school_code  }'
                                                AND `mssr_book_category_rev`.`book_sid`    ='{$book_sid          }'
                                                AND `mssr_book_category_rev`.`rev_id`      = {$rs_rev_id         };
                                        ";
                                        //送出
                                        $conn_mssr->exec($sql);
                                    }
                                }
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
                        break;

                        case 2:
                        //二階層
                            if($cat_lv===1){
                                $msg="發生嚴重錯誤, 請重新輸入!";
                                $jscript_back="
                                    <script>
                                        alert('{$msg}');
                                        history.back(-1);
                                    </script>
                                ";
                                die($jscript_back);
                            }elseif($cat_lv===2){

                            }elseif($cat_lv===3){
                            //-----------------------------
                            //檢核
                            //-----------------------------

                                $sql="
                                    SELECT
                                        `rev_id`,
                                        `cat_group`
                                    FROM `mssr_book_category_rev`
                                    WHERE 1=1
                                        AND `mssr_book_category_rev`.`school_code` ='{$sess_school_code  }'
                                        AND `mssr_book_category_rev`.`cat_code`    ='{$cat_code1         }'
                                        AND `mssr_book_category_rev`.`book_sid`    ='{$book_sid          }'
                                ";
                                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                if(!empty($arrys_result)){
                                    $arry_del_cat_grouplist=array();
                                    $arry_del_rev_id_list=array();
                                    foreach($arrys_result as $arry_result){
                                        $rs_rev_id=(int)$arry_result['rev_id'];
                                        $rs_cat_group=(int)$arry_result['cat_group'];
                                        $arry_del_cat_grouplist[$rs_cat_group]=$rs_rev_id;
                                        $arry_del_rev_id_list[$rs_rev_id]=$rs_rev_id;
                                    }
                                }

                                if(!empty($arry_del_cat_grouplist)){
                                    foreach($arry_del_cat_grouplist as $rs_cat_group=>$rs_rev_id){
                                        $rs_rev_id=(int)$rs_rev_id;
                                        $rs_cat_group=(int)$rs_cat_group;
                                        $sql="
                                            SELECT
                                                `cat_code`,
                                                `rev_id`
                                            FROM `mssr_book_category_rev`
                                            WHERE 1=1
                                                AND `mssr_book_category_rev`.`school_code` ='{$sess_school_code  }'
                                                AND `mssr_book_category_rev`.`cat_code`    ='{$cat_code2         }'
                                                AND `mssr_book_category_rev`.`book_sid`    ='{$book_sid          }'
                                                AND `mssr_book_category_rev`.`cat_group`   = {$rs_cat_group      }
                                        ";
                                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                        if(!empty($arrys_result)){
                                            foreach($arrys_result as $arry_result){
                                                $result_cat_code=trim($arry_result['cat_code']);
                                                $result_rev_id=(int)$arry_result['rev_id'];
                                                $arry_del_rev_id_list[$result_rev_id]=$result_rev_id;
                                            }
                                        }else{
                                            unset($arry_del_rev_id_list[$rs_rev_id]);
                                        }
                                    }
                                }

                                if(!empty($arry_del_rev_id_list)){
                                    foreach($arry_del_rev_id_list as $rs_rev_id){
                                        $rs_rev_id=(int)$rs_rev_id;
                                        $sql="
                                            # for mssr_book_category_rev
                                            DELETE FROM `mssr_book_category_rev`
                                            WHERE 1=1
                                                AND `mssr_book_category_rev`.`school_code` ='{$sess_school_code  }'
                                                AND `mssr_book_category_rev`.`book_sid`    ='{$book_sid          }'
                                                AND `mssr_book_category_rev`.`rev_id`      = {$rs_rev_id         };
                                        ";
                                        //送出
                                        $conn_mssr->exec($sql);
                                    }
                                }
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
                        break;

                        case 3:
                        //三階層

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

            //-------------------------------------------
            //檢核最後一組類別組別
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
            //新增
            //-------------------------------------------

                if(!empty($arrys_cat_code)){

                    foreach($arrys_cat_code as $arry_cat_code){

                        $cat_code=mysql_prep(strip_tags(trim($arry_cat_code)));

                        $sql="
                            # for mssr_book_category_rev
                            INSERT INTO `mssr_book_category_rev` SET
                                `create_by`         =  {$create_by          } ,
                                `school_code`       = '{$sess_school_code   }',
                                `cat_code`          = '{$cat_code           }',
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