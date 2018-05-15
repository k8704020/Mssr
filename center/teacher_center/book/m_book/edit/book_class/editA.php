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

                    APP_ROOT.'lib/php/vaildate/code',
                    APP_ROOT.'lib/php/string/code',
                    APP_ROOT.'lib/php/db/code'
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
    //99    管理者

        if(!empty($sess_login_info)){
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_book');
        }

    //---------------------------------------------------
    //班級判斷
    //---------------------------------------------------

        $has_class_code=true;
        $arrys_class_code=$sess_login_info['arrys_class_code'];
        if(count($arrys_class_code)===0){
            $has_class_code=false;
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //book_name         書名
    //book_author       作者
    //book_publisher    出版社
    //book_sid          書籍識別碼
    //book_isbn_10      ISBN10碼
    //book_isbn_13      ISBN13碼
    //book_no           書籍序號
    //book_donor        書籍捐贈者

        $post_chk=array(
            'book_name      ',
            'book_author    ',
            'book_publisher ',
            'book_sid       ',
            'book_no        ',
            'book_donor     '
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
    //book_name         書名
    //book_author       作者
    //book_publisher    出版社
    //book_sid          書籍識別碼
    //book_isbn_10      ISBN10碼
    //book_isbn_13      ISBN13碼
    //book_no           書籍序號
    //book_donor        書籍捐贈者

        //POST
        $book_name      =trim($_POST[trim('book_name        ')]);
        $book_author    =trim($_POST[trim('book_author      ')]);
        $book_publisher =trim($_POST[trim('book_publisher   ')]);
        $book_sid       =trim($_POST[trim('book_sid         ')]);
        $book_no        =trim($_POST[trim('book_no          ')]);
        $book_donor     =trim($_POST[trim('book_donor       ')]);

        if(isset($_POST[trim('book_isbn_10')])){
            $book_isbn_10=trim($_POST[trim('book_isbn_10')]);
        }
        if(isset($_POST[trim('book_isbn_13')])){
            $book_isbn_13=trim($_POST[trim('book_isbn_13')]);
        }

        //SESSION
        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);

        //有班級才撈取
        if($has_class_code){
            $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
            $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
            $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];
        }

        //分頁
        $psize=(isset($_POST['psize']))?(int)$_POST['psize']:10;
        $pinx =(isset($_POST['pinx']))?(int)$_POST['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //book_name         書名
    //book_author       作者
    //book_publisher    出版社
    //book_sid          書籍識別碼
    //book_isbn_10      ISBN10碼
    //book_isbn_13      ISBN13碼
    //book_no           書籍序號
    //book_donor        書籍捐贈者

        $arry_err=array();

        if($book_name===''){
           $arry_err[]='書名,未輸入!';
        }
        if($book_author===''){
           $arry_err[]='作者,未輸入!';
        }
        if($book_publisher===''){
           $arry_err[]='出版社,未輸入!';
        }
        if($book_sid===''){
           $arry_err[]='書籍識別碼,未輸入!';
        }
        if($book_no===''){
           $arry_err[]='書籍序號,未輸入!';
        }else{
            $book_no=(int)$book_no;
            if($book_no===0){
                $arry_err[]='書籍序號,錯誤!';
            }
        }
        if((isset($book_isbn_10))&&($book_isbn_10!=='')){
            $ch_book_isbn_10=ch_isbn_10($book_isbn_10, $convert=false);
            if(isset($ch_book_isbn_10['error'])){
                $arry_err[]='ISBN10碼,錯誤!';
            }
        }
        if((isset($book_isbn_13))&&($book_isbn_13!=='')){
            $ch_book_isbn_13=ch_isbn_13($book_isbn_13, $convert=false);
            if(isset($ch_book_isbn_13['error'])){
                $arry_err[]='ISBN13碼,錯誤!';
            }
        }
        if((!isset($book_isbn_10))&&(!isset($book_isbn_13))){
            $arry_err[]='ISBN10碼,ISBN13碼,都不存在!';
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
        //book_name         書名
        //book_author       作者
        //book_publisher    出版社
        //book_sid          書籍識別碼
        //book_isbn_10      ISBN10碼
        //book_isbn_13      ISBN13碼
        //book_no           書籍序號
        //book_donor        書籍捐贈者

            $sess_user_id   =(int)$sess_user_id;
            $sess_school_code=mysql_prep($sess_school_code);

            if($has_class_code){
                $sess_grade     =(int)$sess_grade;
                $sess_classroom =(int)$sess_classroom;
            }else{
                $sess_grade     =1;
                $sess_classroom =1;
            }

            $book_name      =mysql_prep($book_name);
            $book_author    =mysql_prep($book_author);
            $book_publisher =mysql_prep($book_publisher);
            $book_sid       =mysql_prep($book_sid);
            $book_no        =(int)$book_no;
            $book_donor     =mysql_prep($book_donor);

            if((isset($book_isbn_10))&&($book_isbn_10!=='')){
                $book_isbn_10=mysql_prep($book_isbn_10);
            }

            if((isset($book_isbn_13))&&($book_isbn_13!=='')){
                $book_isbn_13=mysql_prep($book_isbn_13);
            }

            //-------------------------------------------
            //檢核
            //-------------------------------------------

                //---------------------------------------
                //檢核書籍識別碼
                //---------------------------------------

                    $sql="
                        SELECT
                            `create_by`,
                            `edit_by`,
                            `school_code`,
                            `school_category`,
                            `grade_id`,
                            `classroom_id`,
                            `book_id`,
                            `book_sid`,
                            `book_isbn_10`,
                            `book_isbn_13`,
                            `book_no`,
                            `book_name`,
                            `book_author`,
                            `book_publisher`,
                            `book_page_count`,
                            `book_word`,
                            `book_note`,
                            `book_phonetic`,
                            `keyin_cdate`,
                            `keyin_mdate`,
                            `keyin_ip`
                        FROM `mssr_book_class`
                        WHERE 1=1
                            AND `book_sid`='{$book_sid}'
                    ";

                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
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

                //---------------------------------------
                //檢核借閱書學校關聯
                //---------------------------------------

                    $other_school_code=book_borrow_school_rev($db_type='mysql',$arry_conn_mssr,$sess_school_code);

                //---------------------------------------
                //檢核書籍序號
                //---------------------------------------

                    $sql="
                        SELECT
                            `school_code`,
                            `book_isbn_10`,
                            `book_isbn_13`,
                            `book_no`
                        FROM `mssr_book_class`
                        WHERE 1=1
                            AND (
                                `mssr_book_class`.`book_isbn_10`= '{$book_isbn_10    }'
                                    OR
                                `mssr_book_class`.`book_isbn_13`= '{$book_isbn_13    }'
                            )
                            AND `mssr_book_class`.`book_no`     =  {$book_no         }
                            AND `mssr_book_class`.`book_sid`    <>'{$book_sid        }'
                    ";
                    if(trim($other_school_code)!==''){
                        $sql.="AND `mssr_book_class`.`school_code` IN ('{$sess_school_code}',{$other_school_code})";
                    }else{
                        $sql.="AND `mssr_book_class`.`school_code` = '{$sess_school_code}'";
                    }

                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    $numrow=count($arrys_result);

                    if($numrow!==0){
                        $msg="書本ISBN碼與貼紙編號重複, 請重新輸入!";
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

            $edit_by       =(int)$sess_user_id;

            if($has_class_code){
                $sess_grade     =(int)$sess_grade;
                $sess_classroom =(int)$sess_classroom;
            }else{
                $sess_grade     =1;
                $sess_classroom =1;
            }

            $book_name     =mysql_prep(strip_tags($book_name));
            $book_author   =mysql_prep(strip_tags($book_author));
            $book_publisher=mysql_prep(strip_tags($book_publisher));
            $book_sid      =mysql_prep($book_sid);

            if((isset($book_isbn_10))&&($book_isbn_10!=='')){
                $book_isbn_10=mysql_prep(strip_tags($book_isbn_10));
            }

            if((isset($book_isbn_13))&&($book_isbn_13!=='')){
                $book_isbn_13=mysql_prep(strip_tags($book_isbn_13));
            }

            $book_donor     =mysql_prep(strip_tags($book_donor));

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            $sql="
                UPDATE `mssr_book_class` SET
                    `edit_by`       = {$edit_by         } ,
                    `grade_id`      = {$sess_grade      } ,
                    `classroom_id`  = {$sess_classroom  } ,
                    `book_name`     ='{$book_name       }',
                    `book_author`   ='{$book_author     }',
                    `book_donor`    ='{$book_donor      }',
            ";
            if((isset($book_isbn_10))){
                $sql.="
                    `book_isbn_10`  ='{$book_isbn_10    }',
                ";
            }
            if((isset($book_isbn_13))){
                $sql.="
                    `book_isbn_13`  ='{$book_isbn_13    }',
                ";
            }
            $sql.="
                    `book_publisher`='{$book_publisher  }'
                WHERE 1=1
                    AND `book_sid`='{$book_sid}'
                LIMIT 1;
            ";

            //送出
            $err ='DB QUERY FAIL';
            $sth=$conn_mssr->prepare($sql);
            $sth->execute()or die($err);

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page=str_repeat("../",2)."index.php";
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