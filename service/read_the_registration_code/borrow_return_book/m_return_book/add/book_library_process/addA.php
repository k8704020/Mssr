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
        require_once(str_repeat("../",6).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'service/read_the_registration_code/inc/code',

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

        if($config_arrys['is_offline']['service']['read_the_registration_code']){
            $url=str_repeat("../",7).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(!login_check(array('t'))){
            $url=str_repeat("../",4).'login/loginF.php';
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
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //book_code         書本編號
    //book_name         書籍名稱

        $get_chk=array(
            'book_code'
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
    //book_code         書本編號
    //book_name         書籍名稱

        //GET
        $book_code      =trim($_GET[trim('book_code')]);
        $book_name      =trim($_GET[trim('book_name')]);

        //SESSION
        $school_code    =trim($_sess_t[trim('school_code    ')]);

        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:3;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?3:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //book_code         書本編號
    //book_name         書籍名稱

        $arry_err=array();

        if($book_code===''){
           $arry_err[]='書本編號,未輸入!';
        }

        if($book_name===''){
           $arry_err[]='書籍名稱,未輸入!';
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
        //book_code         書本編號
        //book_name         書籍名稱

            $book_name  =mysql_prep($book_name);        //書籍名稱
            $book_code  =mysql_prep($book_code);        //書本編號
            $school_code=mysql_prep($school_code);      //學校代號

            //老師的班級
            $class_code =mysql_prep($_sess_t['class_code']);

            //初始化, 檢核借閱書學校關聯
            $other_school_code="";

            //-------------------------------------------
            //檢核借閱書學校關聯
            //-------------------------------------------

                $other_school_code=book_borrow_school_rev($db_type='mysql',$arry_conn_mssr,$school_code);

            //-------------------------------------------
            //1.有無書籍
            //-------------------------------------------

                $sql="
                    SELECT
                        `book_sid`,
                        `book_name`,
                        `book_library_code`,
                        `book_no`
                    FROM `mssr_book_library`
                    WHERE 1=1
                        AND `book_library_code` ='{$book_code}'
                        AND `book_name`         ='{$book_name}'
                ";
                if(trim($other_school_code)!==''){
                    $sql.="AND `school_code` IN ('{$school_code}',{$other_school_code})";
                }else{
                    $sql.="AND `school_code`='{$school_code}'";
                }

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
                }else{
                    //提取, 書本資訊
                    $book_sid=trim($arrys_result[0]['book_sid']);
                    $book_name=trim($arrys_result[0]['book_name']);
                    $book_no=trim($arrys_result[0]['book_no']);
                    $return_date=date("Y-m-d");
                }

            //-------------------------------------------
            //2.是否借閱中
            //-------------------------------------------

                $sql="
                    SELECT
                        `user_id`,
                        `borrow_sid`
                    FROM `mssr_book_borrow_tmp`
                    WHERE 1=1
                        AND `book_sid`='{$book_sid}'
                ";

                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                $numrow=count($arrys_result);

                if($numrow===0){
                    $msg="書本尚未借閱, 請先行借閱!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }else{
                    //提取, 借閱資訊
                    $user_id=(int)$arrys_result[0]['user_id'];
                    $borrow_sid=trim($arrys_result[0]['borrow_sid']);
                }

            //-------------------------------------------
            //抓取學生資料
            //-------------------------------------------

                $sql="
                    SELECT
                        `student`.`number`,
                        `member`.`name`
                    FROM `student`
                        INNER JOIN `member` ON
                        `student`.`uid`=`member`.`uid`
                    WHERE 1=1
                        AND `student`.`uid`='{$user_id}'
                        AND `student`.`class_code`='{$class_code}'
                        #AND `student`.`start`<'{$return_date}'
                        #AND `student`.`end`>'{$return_date}'
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                $numrow=count($arrys_result);
                if($numrow===0){
                    $msg="查無, 還書人資料!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }else{
                    //提取, 借閱人資訊
                    $user_number=(int)$arrys_result[0]['number'];
                    $user_name=trim($arrys_result[0]['name']);
                }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $user_id        =(int)$user_id;
            $school_code    =mysql_prep($school_code);  //學校代號
            $book_sid       =mysql_prep($book_sid);     //書本識別碼
            $book_code      =mysql_prep($book_code);    //書本編號
            $borrow_sid     =mysql_prep($borrow_sid);   //借閱識別碼
            $borrow_edate   ='NOW()';

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            $sql="
                # for mssr_book_borrow_tmp
                DELETE FROM `mssr_book_borrow_tmp`
                WHERE 1=1
                    AND `user_id`       = {$user_id     }
                    AND `borrow_sid`    ='{$borrow_sid  }'
                LIMIT 1;

                # for mssr_book_borrow
                UPDATE `mssr_book_borrow` SET
                    `borrow_edate`      = {$borrow_edate}
                WHERE 1=1
                    AND `user_id`       = {$user_id     }
                    AND `borrow_sid`    ='{$borrow_sid  }'
                LIMIT 1;

                # for mssr_book_borrow_semester
                UPDATE `mssr_book_borrow_semester` SET
                    `borrow_edate`      = {$borrow_edate}
                WHERE 1=1
                    AND `user_id`       = {$user_id     }
                    AND `borrow_sid`    ='{$borrow_sid  }'
                LIMIT 1;

                # for mssr_book_borrow_log
                UPDATE `mssr_book_borrow_log` SET
                    `borrow_edate`      = {$borrow_edate}
                WHERE 1=1
                    AND `user_id`       = {$user_id     }
                    AND `borrow_sid`    ='{$borrow_sid  }'
                LIMIT 1;
            ";

            //送出
            $err ='DB QUERY FAIL';
            $conn_mssr->exec($sql)
            or die($err);

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page=str_repeat("../",2)."content_result.php";
        $arg =array(
            'psize'      =>$psize,
            'pinx'       =>1,
            'user_id'    =>$user_id,
            'user_name'  =>$user_name,
            'user_number'=>$user_number,
            'return_date'=>$return_date,
            'book_name'  =>$book_name,
            'book_no'    =>$book_no
        );
        $arg =http_build_query($arg);

        if(!empty($arg)){
            $url="{$page}?{$arg}";
        }else{
            $url="{$page}";
        }

        header("Location: {$url}");
?>

