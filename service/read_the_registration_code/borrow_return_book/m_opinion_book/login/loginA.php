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
        require_once(str_repeat("../",5)."config/config.php");

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
            $url=str_repeat("../",6).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(!login_check(array('t'))){
            $url=str_repeat("../",3).'login/loginF.php';
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
    //card_number  借書證號碼

        $post_chk=array(
            'card_number'
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
    //card_number  借書證號碼

        //POST
        $card_number=trim($_POST[trim('card_number')]);

        //SESSION
        $sess_class_code=trim($_sess_t['class_code']);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //card_number  借書證號碼

        $arry_err=array();

        if($card_number===''){
           $arry_err[]='借書證號碼,未輸入!';
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

            //-------------------------------------------
            //檢核學生陣列
            //-------------------------------------------

                //老師的班級
                $sess_class_code=mysql_prep($sess_class_code);
                $date=date("Y-m-d");

                $sql="
                    SELECT `uid`
                    FROM `student`
                    WHERE 1=1
                        AND `student`.`class_code`='{$sess_class_code}'
                        AND `student`.`start`<='{$date}'
                        AND `student`.`end`>='{$date}'
                ";
                $db_results=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
                if(!empty($db_results)){
                    //建立學生陣列
                    $arrys_user=array();
                    foreach($db_results as $inx=>$db_result){
                        $user_id=(int)$db_result['uid'];
                        $arrys_user[$inx]=$user_id;
                    }
                    if(!empty($arrys_user)){
                        $users="'";
                        $users.=implode("','",$arrys_user);
                        $users.="'";
                    }
                }else{
                    $msg="查無, 可用的學生資料!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }

            //-------------------------------------------
            //檢核借書證
            //-------------------------------------------
            //card_number  借書證號碼

                $card_number=mysql_prep($card_number);

                //老師的班級
                $sess_class_code=mysql_prep($sess_class_code);

                //目前日期
                $_date=date("Y-m-d");


                $sql="
                    SELECT
                        `uid`,
                        `card_number`
                    FROM `library_card`
                    WHERE 1=1
                        AND `card_number`='{$card_number}'
                        AND `uid` IN ($users)
                ";
                //echo $sql.'<br/>';

                $db_results=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);

                if(count($db_results)===0){
                    $msg="查無, 借書證號碼!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }else{
                    //使用者主索引
                    $_user_id=(int)$db_results[0]['uid'];
                }

            //-------------------------------------------
            //檢核學生資料
            //-------------------------------------------

                $sql="
                    SELECT
                        `student`.`number`,
                        `member`.`name`,

                        `class`.`class_category`,
                        `class`.`grade`,
                        `class`.`classroom`
                    FROM `student`

                        INNER JOIN `member` ON
                        `student`.`uid`=`member`.`uid`

                        INNER JOIN `class` ON
                        `student`.`class_code`=`class`.`class_code`

                    WHERE 1=1
                        AND `student`.`uid`='{$_user_id}'
                        AND `student`.`class_code`='{$sess_class_code}'
                        AND `student`.`start`<='{$_date}'
                        AND `student`.`end`>='{$_date}'
                ";
                //echo $sql.'<br/>';

                $db_results=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);

                if(count($db_results)===0){
                    $msg="查無, 學生資料!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }else{
                    //使用者資訊
                    $_user_name  =trim($db_results[0]['name']);
                    $_user_number=(int)$db_results[0]['number'];

                    //學校班級資訊
                    $_class_category=(int)$db_results[0]['class_category'];
                    $_grade=(int)$db_results[0]['grade'];
                    $_classroom=(int)$db_results[0]['classroom'];
                }


        //資訊回存
        $_SESSION['_read_the_registration_code']['_login']['_user_id']       =$_user_id;
        $_SESSION['_read_the_registration_code']['_login']['_user_name']     =$_user_name;
        $_SESSION['_read_the_registration_code']['_login']['_user_number']   =$_user_number;

        $_SESSION['_read_the_registration_code']['_login']['_class_category']=$_class_category;
        $_SESSION['_read_the_registration_code']['_login']['_grade']         =$_grade;
        $_SESSION['_read_the_registration_code']['_login']['_classroom']     =$_classroom;

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page=str_repeat("../",1)."index.php";
        $arg =array(

        );
        $arg =http_build_query($arg);

        if(!empty($arg)){
            $url="{$page}?{$arg}";
        }else{
            $url="{$page}";
        }

        header("Location: {$url}");
?>

