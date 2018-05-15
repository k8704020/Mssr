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
    //book_no           書本序號

        $get_chk=array(
            'book_code  ',
            'book_no    '
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
    //book_no           書本序號

        //GET
        $book_code      =trim($_GET[trim('book_code')]);
        $book_no        =(int)$_GET[trim('book_no')];

        //SESSION
        $school_code    =trim($_sess_t[trim('school_code   ')]);
        $class_category =(int)$_SESSION['_read_the_registration_code']['_login']['_class_category'];
        $grade          =(int)$_SESSION['_read_the_registration_code']['_login']['_grade'];
        $classroom      =(int)$_SESSION['_read_the_registration_code']['_login']['_classroom'];
        $user_id        =(int)$_SESSION['_read_the_registration_code']['_login']['_user_id'];

        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:8;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?8:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //book_code         書本編號
    //book_no           書本序號

        $arry_err=array();

        if($book_code===''){
           $arry_err[]='書本編號,未輸入!';
        }
        if($book_no===''){
           $arry_err[]='書本序號,未輸入!';
        }else{
           $book_no=(int)$book_no;
           if($book_no===0){
              $arry_err[]='書本序號,不為整數!';
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
        //book_code         書本編號
        //book_no           書本序號

            $book_code      =mysql_prep($book_code);    //書本編號
            $book_no        =(int)$book_no;             //書本序號

            $school_code    =mysql_prep($school_code);  //學校代號
            $class_category =(int)$class_category;      //學校類型
            $grade          =(int)$grade;               //年級
            $classroom      =(int)$classroom;           //班級
            $user_id        =(int)$user_id;             //借閱人主索引

            //其他人借閱狀態
            $other_has_borrow="false";

            //初始化, 檢核借閱書學校關聯
            $other_school_code="";

            //-------------------------------------------
            //檢核是否超越閱讀登記數量限制
            //-------------------------------------------

                $sess_class_code=mysql_prep(trim($_sess_t['class_code']));
                $borrow_limit_cno=10;

                $sql="
                    SELECT `mssr`.`mssr_auth_class`.`auth`
                    FROM `mssr`.`mssr_auth_class`
                    WHERE 1=1
                        AND `mssr`.`mssr_auth_class`.`class_code`='{$sess_class_code}'
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(!empty($db_results)){
                    //權限資訊
                    if(false===@unserialize($db_results[0]['auth'])){
                        $auth=array();
                    }else{
                        $auth=@unserialize($db_results[0]['auth']);
                    }
                    if((!empty($auth))&&(isset($auth['borrow_limit_cno']))){
                        $borrow_limit_cno=(int)($auth['borrow_limit_cno']);
                    }
                }

                $sql="
                    SELECT `mssr`.`mssr_book_borrow`.`user_id`
                    FROM `mssr`.`mssr_book_borrow`
                    WHERE 1=1
                        AND `mssr`.`mssr_book_borrow`.`user_id`={$user_id}
                        AND DATE(`mssr`.`mssr_book_borrow`.`borrow_sdate`)=CURDATE()
                    #GROUP BY `mssr`.`mssr_book_borrow`.`user_id`, `mssr`.`mssr_book_borrow`.`book_sid`
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if((int)count($db_results)>(int)$borrow_limit_cno){
                    $msg="一天只能借{$borrow_limit_cno}本書喔!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }

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
                        `book_isbn_10`,
                        `book_isbn_13`
                    FROM `mssr_book_class`
                    WHERE 1=1
                        AND (
                            `book_isbn_10`='{$book_code}'
                                OR
                            `book_isbn_13`='{$book_code}'
                        )
                        AND `book_no`     = {$book_no  }
                ";
                if(trim($other_school_code)!==''){
                    $sql.="AND `mssr_book_class`.`school_code` IN ('{$school_code}',{$other_school_code})";
                }else{
                    $sql.="AND `mssr_book_class`.`school_code`='{$school_code}'";
                }
                $sql.="
                    UNION ALL
                        SELECT
                            `book_sid`,
                            `book_name`,
                            `book_isbn_10`,
                            `book_isbn_13`
                        FROM `mssr_book_unverified`
                        WHERE 1=1
                            AND (
                                `book_isbn_10`='{$book_code}'
                                    OR
                                `book_isbn_13`='{$book_code}'
                            )
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
                }else{
                    //提取, 書本識別碼
                    $book_sid=trim($arrys_result[0]['book_sid']);
                    $book_name=trim($arrys_result[0]['book_name']);
                }

            //-------------------------------------------
            //2.是否自己借閱中
            //-------------------------------------------

                $sql="
                    SELECT
                        `user_id`
                    FROM `mssr_book_borrow_tmp`
                    WHERE 1=1
                        AND `user_id` ='{$user_id}'
                        AND `book_sid`='{$book_sid}'
                ";

                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                $numrow=count($arrys_result);

                if($numrow!==0){
                    $msg="書本借閱中, 請先行歸還!";
                    $jscript_back="
                        <script>
                            
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                
                }

            //-------------------------------------------
            //3.是否別人借閱中
            //-------------------------------------------

                $sql="
                    SELECT
                        `user_id`,
                        `borrow_sid`
                    FROM `mssr_book_borrow_tmp`
                    WHERE 1=1
                        AND `user_id` <>'{$user_id}'
                        AND `book_sid`='{$book_sid}'
                ";

                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                $numrow=count($arrys_result);

                if($numrow!==0){
                //有借閱中，代表別人未主動歸還
                    $other_has_borrow="true";
                    $return_to=(int)$arrys_result[0]['user_id'];
                    $return_borrow_sid=trim($arrys_result[0]['borrow_sid']);
                }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $book_code          =mysql_prep($book_code);                            //書本編號
            $book_sid           =mysql_prep(strip_tags($book_sid));                 //書本識別碼
            $school_code        =mysql_prep(strip_tags($school_code));              //學校代號
            $class_category     =(int)$class_category;                              //學校類型
            $grade              =(int)$grade;                                       //年級
            $classroom          =(int)$classroom;                                   //班級
            $user_id            =(int)$user_id;                                     //借閱人主索引
            $borrow_sid         =book_borrow_sid($user_id,mb_internal_encoding());  //借閱識別碼
            $keyin_cdate        ='NOW()';
            $log_id             ='NULL';
            $borrow_sdate       ='NOW()';
            $borrow_edate       ='NOW()';
            $keyin_ip           =get_ip();

            if($other_has_borrow==="true"){
                $return_to        =(int)$return_to;
                $return_borrow_sid=mysql_prep($return_borrow_sid);
            }

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            //-------------------------------------------
            //1.處理借閱書籍
            //-------------------------------------------

                $sql="
                    # for mssr_book_borrow_log
                    INSERT INTO `mssr_book_borrow_log` SET
                        `user_id`               = {$user_id                 } ,
                        `book_sid`              ='{$book_sid                }',
                        `school_code`           ='{$school_code             }',
                        `school_category`       = {$class_category          } ,
                        `grade_id`              = {$grade                   } ,
                        `classroom_id`          = {$classroom               } ,
                        `log_id`                = {$log_id                  } ,
                        `borrow_sid`            ='{$borrow_sid              }',
                        `borrow_sdate`          = {$borrow_sdate            } ,
                        `borrow_edate`          = {$borrow_edate            } ,
                        `keyin_ip`              ='{$keyin_ip                }';
                ";
                //送出
                $err ='DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or die($err);

                $new_borrow_sid=(int)$conn_mssr->lastInsertId();

                $sql="
                    # for mssr_book_borrow
                    INSERT INTO `mssr_book_borrow` SET
                        `user_id`               = {$user_id                 } ,
                        `book_sid`              ='{$book_sid                }',
                        `school_code`           ='{$school_code             }',
                        `school_category`       = {$class_category          } ,
                        `grade_id`              = {$grade                   } ,
                        `classroom_id`          = {$classroom               } ,
                        `borrow_sid`            ='{$new_borrow_sid          }',
                        `borrow_sdate`          = {$borrow_sdate            } ,
                        `borrow_edate`          = {$borrow_edate            } ,
                        `keyin_ip`              ='{$keyin_ip                }';

                    # for mssr_book_borrow_semester
                    INSERT INTO `mssr_book_borrow_semester` SET
                        `user_id`               = {$user_id                 } ,
                        `book_sid`              ='{$book_sid                }',
                        `school_code`           ='{$school_code             }',
                        `school_category`       = {$class_category          } ,
                        `grade_id`              = {$grade                   } ,
                        `classroom_id`          = {$classroom               } ,
                        `borrow_sid`            ='{$new_borrow_sid          }',
                        `borrow_sdate`          = {$borrow_sdate            } ,
                        `borrow_edate`          = {$borrow_edate            } ,
                        `keyin_ip`              ='{$keyin_ip                }';

                    # for mssr_book_borrow_log
                    UPDATE `mssr_book_borrow_log` SET
                        `borrow_sid`            ='{$new_borrow_sid          }'
                    WHERE 1=1
                        AND `log_id`            ='{$new_borrow_sid          }'
                    LIMIT 1;
                ";

            //-------------------------------------------
            //2.處理他人未主動歸還
            //-------------------------------------------

                if($other_has_borrow==="true"){
                    $sql.="
                        # for mssr_book_borrow_tmp
                        DELETE FROM `mssr_book_borrow_tmp`
                        WHERE 1=1
                            AND `user_id`       = {$return_to               }
                            AND `borrow_sid`    ='{$return_borrow_sid       }'
                        LIMIT 1;

                        # for mssr_book_borrow
                        UPDATE `mssr_book_borrow` SET
                            `borrow_edate`      = {$borrow_sdate            }
                        WHERE 1=1
                            AND `user_id`       = {$return_to               }
                            AND `borrow_sid`    ='{$return_borrow_sid       }'
                        LIMIT 1;

                        # for mssr_book_borrow_semester
                        UPDATE `mssr_book_borrow_semester` SET
                            `borrow_edate`      = {$borrow_sdate            }
                        WHERE 1=1
                            AND `user_id`       = {$return_to               }
                            AND `borrow_sid`    ='{$return_borrow_sid       }'
                        LIMIT 1;

                        # for mssr_book_borrow_log
                        UPDATE `mssr_book_borrow_log` SET
                            `borrow_edate`      = {$borrow_sdate            }
                        WHERE 1=1
                            AND `user_id`       = {$return_to               }
                            AND `borrow_sid`    ='{$return_borrow_sid       }'
                        LIMIT 1;

                        # for mssr_book_auto_return_log
                        INSERT INTO `mssr_book_auto_return_log` SET
                            `return_from`       = {$user_id                 } ,
                            `return_to`         = {$return_to               } ,
                            `books_sid`         ='{$book_sid                }',
                            `log_id`            = {$log_id                  } ,
                            `keyin_cdate`       = {$keyin_cdate             } ;

                    ";
                }
                //echo "<Pre>";
                //print_r($sql);
                //echo "</Pre>";
                //die();


            //送出
            $err ='DB QUERY FAIL';
            $conn_mssr->exec($sql)
            or die($err);

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        //是否第一次借書
        $_SESSION['_read_the_registration_code']['_login']['first_borrow']='no';
        $_SESSION['_read_the_registration_code']['_login']['opinion_success_flag']='yes';
        $_SESSION['_read_the_registration_code']['_login']['opinion_book_name']=trim($book_name);

        $url ="";
        $page=str_repeat("../",2)."content.php";
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

