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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_teacher_rec');
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
    //book_code     書籍編號
    //book_no       書籍序號

        $post_chk=array(
            'book_code',
            'book_no'
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
    //book_code     書籍編號
    //book_no       書籍序號

        //POST
        $book_code       =trim($_POST[trim('book_code')]);
        $book_no         =(int)$_POST[trim('book_no')];

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

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //book_code     書籍編號
    //book_no       書籍序號

        $arry_err=array();

        if($book_code===''){
           $arry_err[]='書籍編號,未輸入!';
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
        //book_code     書籍編號
        //book_no       書籍序號

            $sess_school_code=mysql_prep($sess_school_code);
            $book_code=mysql_prep($book_code);
            $book_no=(int)$book_no;

            //初始化, 是否找到書籍
            $has_find=false;

            //初始化, 登記的書籍識別碼
            $rec_book_sid='';

            //初始化, 是否已經回答問題過
            $has_opinion=false;

            //初始化, 回傳格式
            $respones=array();

            //-------------------------------------------
            //檢核借閱書學校關聯
            //-------------------------------------------

                $other_school_code=book_borrow_school_rev($db_type='mysql',$arry_conn_mssr,$sess_school_code);

            //-------------------------------------------
            //檢核圖書館書籍
            //-------------------------------------------

                $sql="
                    SELECT
                        `book_sid`,
                        `book_name`
                    FROM `mssr_book_library`
                    WHERE 1=1
                        AND (
                            `book_library_code`='{$book_code}'
                            OR
                            `book_isbn_10`='{$book_code}'
                            OR
                            `book_isbn_13`='{$book_code}'
                        )
                ";
                if(trim($other_school_code)!==''){
                    $sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                }else{
                    $sql.="AND `school_code`='{$sess_school_code}'";
                }

                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                $numrow=count($arrys_result);

                if($numrow!==0){
                    $has_find=true;

                    //提取, 書籍識別碼
                    $rec_book_sid=trim($arrys_result[0]['book_sid']);

                    //提取, 書籍名稱
                    $rec_book_name=trim($arrys_result[0]['book_name']);
                }

            //-------------------------------------------
            //檢核班級書籍
            //-------------------------------------------

                switch($book_no){
                //是否填入書籍序號
                    case 0:
                    //無填入
                        $sql="
                            SELECT
                                `book_sid`,
                                `book_name`
                            FROM `mssr_book_class`
                            WHERE 1=1
                                AND (
                                    `book_isbn_10`='{$book_code}'
                                        OR
                                    `book_isbn_13`='{$book_code}'
                                )
                                AND `book_no`=1
                        ";
                        if(trim($other_school_code)!==''){
                            $sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                        }else{
                            $sql.="AND `school_code`='{$sess_school_code}'";
                        }

                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        $numrow=count($arrys_result);

                        if($numrow!==0){
                            $has_find=true;

                            //提取, 書籍識別碼
                            $rec_book_sid=trim($arrys_result[0]['book_sid']);

                            //提取, 書籍名稱
                            $rec_book_name=trim($arrys_result[0]['book_name']);
                        }
                    break;

                    default:
                    //有填入
                        $sql="
                            SELECT
                                `book_sid`,
                                `book_name`
                            FROM `mssr_book_class`
                            WHERE 1=1
                                AND (
                                    `book_isbn_10`='{$book_code}'
                                        OR
                                    `book_isbn_13`='{$book_code}'
                                )
                                AND `book_no`={$book_no}
                        ";
                        if(trim($other_school_code)!==''){
                            $sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                        }else{
                            $sql.="AND `school_code`='{$sess_school_code}'";
                        }

                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        $numrow=count($arrys_result);

                        if($numrow!==0){
                            $has_find=true;

                            //提取, 書籍識別碼
                            $rec_book_sid=trim($arrys_result[0]['book_sid']);

                            //提取, 書籍名稱
                            $rec_book_name=trim($arrys_result[0]['book_name']);
                        }
                    break;
                }

                if(!$has_find){
                    $respones=json_encode(array(
                        "has_find"=>$has_find,              //has_find  查找狀態
                        "book_name"=>'',                    //book_name 書籍名稱
                        'msg'=>'書本不存在, 請重新輸入!'    //err訊息
                    ));
                    die($respones);
                }

            //-------------------------------------------
            //檢核是否曾經閱讀登記過
            //-------------------------------------------

                $sql="
                    SELECT
                        `user_id`,
                        `book_sid`
                    FROM `mssr_book_read_opinion_cno`
                    WHERE 1=1
                        AND `user_id` = {$sess_user_id}
                        AND `book_sid`='{$rec_book_sid}'
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                $numrow=count($arrys_result);

                if($numrow!==0){
                    $has_opinion=true;
                }

            //-------------------------------------------
            //檢核學校類別
            //-------------------------------------------

                if($has_class_code){
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
                }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $user_id            =(int)$sess_user_id;
            $book_sid           =mysql_prep(strip_tags($rec_book_sid));
            $school_code        =mysql_prep(strip_tags($sess_school_code));

            if($has_class_code){
                $school_category    =(int)$class_category;
                $grade_id           =(int)$sess_grade;
                $classroom_id       =(int)$sess_classroom;
            }else{
                $school_category    =(int)1;
                $grade_id           =(int)1;
                $classroom_id       =(int)1;
            }

            $log_id             ="NULL";
            $borrow_sid         =book_borrow_sid($user_id,mb_internal_encoding());
            $borrow_sdate       ="NOW()";
            $borrow_edate       ="NOW()";
            $keyin_ip           =get_ip();

            $opinion_answer     ='a:5:{i:0;a:2:{s:8:"topic_id";i:1;s:14:"opinion_answer";a:1:{i:0;s:1:"3";}}i:1;a:2:{s:8:"topic_id";i:2;s:14:"opinion_answer";a:1:{i:0;s:1:"3";}}i:2;a:2:{s:8:"topic_id";i:3;s:14:"opinion_answer";a:1:{i:0;s:1:"3";}}i:3;a:2:{s:8:"topic_id";i:4;s:14:"opinion_answer";a:1:{i:0;s:1:"3";}}i:4;a:2:{s:8:"topic_id";i:5;s:14:"opinion_answer";a:1:{i:0;s:1:"6";}}}';
            $keyin_cdate        ="NOW()";
            $keyin_mdate        ="NULL";

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            if($has_opinion){
            //有閱讀登記過

                $sql="
                    # for mssr_book_borrow_log
                    INSERT INTO `mssr_book_borrow_log` SET
                        `user_id`           = {$user_id         } ,
                        `book_sid`          ='{$book_sid        }',
                        `school_code`       ='{$school_code     }',
                        `school_category`   = {$school_category } ,
                        `grade_id`          = {$grade_id        } ,
                        `classroom_id`      = {$classroom_id    } ,
                        `log_id`            = {$log_id          } ,
                        `borrow_sid`        ='{$borrow_sid      }',
                        `borrow_sdate`      = {$borrow_sdate    } ,
                        `borrow_edate`      = {$borrow_edate    } ,
                        `keyin_ip`          ='{$keyin_ip        }';
                ";
                //送出
                $err ='DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or die($err);

                $new_borrow_sid=(int)$conn_mssr->lastInsertId();

                $sql="
                    # for mssr_book_borrow
                    INSERT INTO `mssr_book_borrow` SET
                        `user_id`           = {$user_id         } ,
                        `book_sid`          ='{$book_sid        }',
                        `school_code`       ='{$school_code     }',
                        `school_category`   = {$school_category } ,
                        `grade_id`          = {$grade_id        } ,
                        `classroom_id`      = {$classroom_id    } ,
                        `borrow_sid`        ='{$new_borrow_sid  }',
                        `borrow_sdate`      = {$borrow_sdate    } ,
                        `borrow_edate`      = {$borrow_edate    } ,
                        `keyin_ip`          ='{$keyin_ip        }';

                    # for mssr_book_borrow_semester
                    INSERT INTO `mssr_book_borrow_semester` SET
                        `user_id`           = {$user_id         } ,
                        `book_sid`          ='{$book_sid        }',
                        `school_code`       ='{$school_code     }',
                        `school_category`   = {$school_category } ,
                        `grade_id`          = {$grade_id        } ,
                        `classroom_id`      = {$classroom_id    } ,
                        `borrow_sid`        ='{$new_borrow_sid  }',
                        `borrow_sdate`      = {$borrow_sdate    } ,
                        `borrow_edate`      = {$borrow_edate    } ,
                        `keyin_ip`          ='{$keyin_ip        }';

                    # for mssr_book_read_opinion
                    INSERT INTO `mssr_book_read_opinion` SET
                        `user_id`           = {$user_id         } ,
                        `book_sid`          ='{$book_sid        }',
                        `borrow_sid`        ='{$new_borrow_sid  }',
                        `borrow_sdate`      = {$keyin_cdate     } ,
                        `opinion_answer`    ='{$opinion_answer  }',
                        `keyin_cdate`       = {$keyin_cdate     } ,
                        `keyin_ip`          ='{$keyin_ip        }';

                    # for mssr_book_read_opinion_log
                    INSERT INTO `mssr_book_read_opinion_log` SET
                        `user_id`           = {$user_id         } ,
                        `book_sid`          ='{$book_sid        }',
                        `borrow_sid`        ='{$new_borrow_sid  }',
                        `borrow_sdate`      = {$keyin_cdate     } ,
                        `log_id`            = {$log_id          } ,
                        `opinion_answer`    ='{$opinion_answer  }',
                        `keyin_cdate`       = {$keyin_cdate     } ,
                        `keyin_ip`          ='{$keyin_ip        }';

                    # for mssr_book_read_opinion_cno
                    UPDATE `mssr_book_read_opinion_cno` SET
                        `opinion_cno`       =`opinion_cno`+1
                    WHERE 1=1
                        AND `user_id`       = {$user_id         }
                        AND `book_sid`      ='{$book_sid        }'
                    LIMIT 1;

                    # for mssr_book_borrow_log
                    UPDATE `mssr_book_borrow_log` SET
                        `borrow_sid`        ='{$new_borrow_sid  }'
                    WHERE 1=1
                        AND `log_id`        ='{$new_borrow_sid  }'
                    LIMIT 1;
                ";

                //送出
                $err ='DB QUERY FAIL';
                $sth=$conn_mssr->prepare($sql);
                $sth->execute()or die($err);
            }else{
            //沒閱讀登記過

                $sql="
                    # for mssr_book_borrow_log
                    INSERT INTO `mssr_book_borrow_log` SET
                        `user_id`           = {$user_id         } ,
                        `book_sid`          ='{$book_sid        }',
                        `school_code`       ='{$school_code     }',
                        `school_category`   = {$school_category } ,
                        `grade_id`          = {$grade_id        } ,
                        `classroom_id`      = {$classroom_id    } ,
                        `log_id`            = {$log_id          } ,
                        `borrow_sid`        ='{$borrow_sid      }',
                        `borrow_sdate`      = {$borrow_sdate    } ,
                        `borrow_edate`      = {$borrow_edate    } ,
                        `keyin_ip`          ='{$keyin_ip        }';
                ";
                //送出
                $err ='DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or die($err);

                $new_borrow_sid=(int)$conn_mssr->lastInsertId();

                $sql="
                    # for mssr_book_borrow
                    INSERT INTO `mssr_book_borrow` SET
                        `user_id`           = {$user_id         } ,
                        `book_sid`          ='{$book_sid        }',
                        `school_code`       ='{$school_code     }',
                        `school_category`   = {$school_category } ,
                        `grade_id`          = {$grade_id        } ,
                        `classroom_id`      = {$classroom_id    } ,
                        `borrow_sid`        ='{$new_borrow_sid  }',
                        `borrow_sdate`      = {$borrow_sdate    } ,
                        `borrow_edate`      = {$borrow_edate    } ,
                        `keyin_ip`          ='{$keyin_ip        }';

                    # for mssr_book_borrow_semester
                    INSERT INTO `mssr_book_borrow_semester` SET
                        `user_id`           = {$user_id         } ,
                        `book_sid`          ='{$book_sid        }',
                        `school_code`       ='{$school_code     }',
                        `school_category`   = {$school_category } ,
                        `grade_id`          = {$grade_id        } ,
                        `classroom_id`      = {$classroom_id    } ,
                        `borrow_sid`        ='{$new_borrow_sid  }',
                        `borrow_sdate`      = {$borrow_sdate    } ,
                        `borrow_edate`      = {$borrow_edate    } ,
                        `keyin_ip`          ='{$keyin_ip        }';

                    # for mssr_book_read_opinion
                    INSERT INTO `mssr_book_read_opinion` SET
                        `user_id`           = {$user_id         } ,
                        `book_sid`          ='{$book_sid        }',
                        `borrow_sid`        ='{$new_borrow_sid  }',
                        `borrow_sdate`      = {$keyin_cdate     } ,
                        `opinion_answer`    ='{$opinion_answer  }',
                        `keyin_cdate`       = {$keyin_cdate     } ,
                        `keyin_ip`          ='{$keyin_ip        }';

                    # for mssr_book_read_opinion_log
                    INSERT INTO `mssr_book_read_opinion_log` SET
                        `user_id`           = {$user_id         } ,
                        `book_sid`          ='{$book_sid        }',
                        `borrow_sid`        ='{$new_borrow_sid  }',
                        `borrow_sdate`      = {$keyin_cdate     } ,
                        `log_id`            = {$log_id          } ,
                        `opinion_answer`    ='{$opinion_answer  }',
                        `keyin_cdate`       = {$keyin_cdate     } ,
                        `keyin_ip`          ='{$keyin_ip        }';

                    # for mssr_book_read_opinion_cno
                    INSERT INTO `mssr_book_read_opinion_cno` SET
                        `user_id`           = {$user_id         } ,
                        `book_sid`          ='{$book_sid        }',
                        `opinion_cno`       = 1,
                        `keyin_mdate`       = {$keyin_mdate     } ;

                    # for mssr_book_borrow_log
                    UPDATE `mssr_book_borrow_log` SET
                        `borrow_sid`        ='{$new_borrow_sid  }'
                    WHERE 1=1
                        AND `log_id`        ='{$new_borrow_sid  }'
                    LIMIT 1;
                ";

                //送出
                $err ='DB QUERY FAIL';
                $sth=$conn_mssr->prepare($sql);
                $sth->execute()or die($err);
            }

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $respones=json_encode(array(
            "has_find"=>$has_find,                                          //has_find  查找狀態
            "book_name"=>$rec_book_name,                                    //book_name 書籍名稱
            'msg'=>"{$rec_book_name} 已登記成功，請繼續進入書店進行推薦 !"  //err訊息
        ));
        die($respones);
?>