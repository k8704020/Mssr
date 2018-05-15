<?php
//-------------------------------------------------------
//函式: mobile_borrow()
//用途: 行動裝置閱讀登記
//日期: 2015年09月05日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    ////---------------------------------------------------
    ////設置測試資料
    ////---------------------------------------------------
    //
    //    //實體化
    //    $omobile_borrow =new mobile_borrow();
    //
    //    //檢核書籍資訊
    //    $uid            =(int)5030;
    //    $school_code    =trim('gcp');
    //    $book_code      =trim('9789862115503');
    //    $arrys_book_info=$omobile_borrow->get_books_info($uid,$book_code,$school_code);
    //
    //    //登記書籍
    //    $school_code    =trim('gcp');
    //    $book_sid       =trim('mbg5030201509052234325173');
    //    $borrow_book    =$omobile_borrow->borrow_book($uid,$school_code,$book_sid);
    //
    //    //新增並登記書籍
    //    $book_name      =addslashes(strip_tags(trim('自建書籍')));
    //    $book_author    =addslashes(strip_tags(trim('自建書籍')));
    //    $book_publisher =addslashes(strip_tags(trim('自建書籍')));
    //    $add_borrow_book=$omobile_borrow->add_borrow_book($uid,$school_code,$book_code,$book_name,$book_author,$book_publisher);

    //---------------------------------------------------
    //函式
    //---------------------------------------------------

        class mobile_borrow{

            public function add_borrow_book($uid,$school_code,$book_code,$book_name,$book_author,$book_publisher){
            //新增並登記書籍

                //參數檢驗
                $this->verify_parameter($uid);

                //引用函式檔
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/inc/search_book_info_online/code.php");
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/center/teacher_center/inc/book/book/book_unverified_sid/code.php");
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/lib/php/string/code.php");
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/lib/php/vaildate/code.php");
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/lib/php/net/code.php");
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/inc/book_borrow_sid/code.php");

                //建立連線
                $conn_mssr=$this->db_conn('mssr');

                //參數設置
                $create_by  =(int)$uid;
                $edit_by    =(int)$uid;
                $book_id    ="NULL";
                $book_sid_unverified=book_unverified_sid($create_by,mb_internal_encoding());

                $ch_isbn_10=ch_isbn_10($book_code, $convert=false);
                $ch_isbn_13=ch_isbn_13($book_code, $convert=false);

                $_lv=0; //錯誤指標
                if(isset($ch_isbn_10['error'])){
                    $_lv=$_lv+1;
                }
                if(isset($ch_isbn_13['error'])){
                    $_lv=$_lv+3;
                }

                switch($_lv){
                    case 1:
                    //10碼錯誤，利用13碼轉換更新
                        $book_isbn_10=isbn_13_to_10($book_code);
                        $book_isbn_13=$book_code;
                    break;

                    case 3:
                    //13碼錯誤，利用10碼轉換更新
                        $book_isbn_10=$book_code;
                        $book_isbn_13=isbn_10_to_13($book_code);
                    break;

                    case 4:
                        $err_msg='GET_BOOKS_INFO() IS INVALID';
                        $this->err_report($err_msg);
                    break;
                }

                $book_name      =addslashes(strip_tags(trim($book_name)));
                $book_author    =addslashes(strip_tags(trim($book_author)));
                $book_publisher =addslashes(strip_tags(trim($book_publisher)));
                $book_page_count=0;
                $book_word      =0;
                $book_note      ='';
                $book_phonetic  ='無';
                $keyin_cdate    ="NOW()";
                $keyin_mdate    ="NULL";
                $keyin_ip       =get_ip();

                $sql="
                    # for mssr_book_unverified
                    INSERT INTO `mssr_book_unverified` SET
                        `create_by`         =  {$create_by          } ,
                        `edit_by`           =  {$edit_by            } ,
                        `book_id`           =  {$book_id            } ,
                        `book_sid`          = '{$book_sid_unverified}',
                        `book_isbn_10`      = '{$book_isbn_10       }',
                        `book_isbn_13`      = '{$book_isbn_13       }',
                        `book_name`         = '{$book_name          }',
                        `book_author`       = '{$book_author        }',
                        `book_publisher`    = '{$book_publisher     }',
                        `book_page_count`   =  {$book_page_count    } ,
                        `book_word`         =  {$book_word          } ,
                        `book_from`         =1                        ,
                        `book_note`         = '{$book_note          }',
                        `book_phonetic`     = '{$book_phonetic      }',
                        `book_verified`     =3                        ,
                        `keyin_cdate`       =  {$keyin_cdate        } ,
                        `keyin_mdate`       =  {$keyin_mdate        } ,
                        `keyin_ip`          = '{$keyin_ip           }';
                ";
                //送出
                $err_msg ='ADD_BORROW_BOOK() IS DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or $this->err_report($err_msg);

                //參數設置
                $uid            =(int)$uid;
                $book_sid       =addslashes(strip_tags($book_sid_unverified));
                $school_code    =addslashes(strip_tags($school_code));
                $class_category =(int)0;
                $grade          =(int)0;
                $classroom      =(int)0;
                $user_id        =(int)$uid;
                $borrow_sid     =book_borrow_sid($user_id,mb_internal_encoding());
                $keyin_cdate    ='NOW()';
                $log_id         ='NULL';
                $borrow_sdate   ='NOW()';
                $borrow_edate   ='0000-00-00 00:00:00';
                $keyin_ip       =get_ip();

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
                        `borrow_edate`          ='{$borrow_edate            }',
                        `keyin_ip`              ='{$keyin_ip                }';
                ";
                //送出
                $err_msg ='ADD_BORROW_BOOK() IS DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or $this->err_report($err_msg);

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
                        `borrow_edate`          ='{$borrow_edate            }',
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
                        `borrow_edate`          ='{$borrow_edate            }',
                        `keyin_ip`              ='{$keyin_ip                }';

                    # for mssr_book_borrow_log
                    UPDATE `mssr_book_borrow_log` SET
                        `borrow_sid`            ='{$new_borrow_sid          }'
                    WHERE 1=1
                        AND `log_id`            ='{$new_borrow_sid          }'
                    LIMIT 1;
                ";
                //送出
                $err_msg ='ADD_BORROW_BOOK() IS DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or $this->err_report($err_msg);

                //回傳
                return true;
            }
            public function borrow_book($uid,$school_code,$book_sid){
            //登記書籍

                //參數檢驗
                $this->verify_parameter($uid);

                //引用函式檔
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/inc/search_book_info_online/code.php");
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/center/teacher_center/inc/book/book/book_global_sid/code.php");
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/lib/php/string/code.php");
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/lib/php/vaildate/code.php");
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/lib/php/net/code.php");
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/inc/book_borrow_sid/code.php");
                //建立連線
                $conn_mssr=$this->db_conn('mssr');

                //參數設置
                $uid            =(int)$uid;
                $book_sid       =addslashes(strip_tags($book_sid));
                $school_code    =addslashes(strip_tags($school_code));
                $class_category =(int)0;
                $grade          =(int)0;
                $classroom      =(int)0;
                $user_id        =(int)$uid;
                $borrow_sid     =book_borrow_sid($user_id,mb_internal_encoding());
                $keyin_cdate    ='NOW()';
                $log_id         ='NULL';
                $borrow_sdate   ='NOW()';
                $borrow_edate   ='0000-00-00 00:00:00';
                $keyin_ip       =get_ip();

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
                        `borrow_edate`          ='{$borrow_edate            }',
                        `keyin_ip`              ='{$keyin_ip                }';
                ";
                //送出
                $err_msg ='BORROW_BOOK() IS DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or $this->err_report($err_msg);

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
                        `borrow_edate`          ='{$borrow_edate            }',
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
                        `borrow_edate`          ='{$borrow_edate            }',
                        `keyin_ip`              ='{$keyin_ip                }';

                    # for mssr_book_borrow_log
                    UPDATE `mssr_book_borrow_log` SET
                        `borrow_sid`            ='{$new_borrow_sid          }'
                    WHERE 1=1
                        AND `log_id`            ='{$new_borrow_sid          }'
                    LIMIT 1;
                ";
                //送出
                $err_msg ='BORROW_BOOK() IS DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or $this->err_report($err_msg);

                //回傳
                return true;
            }
            public function get_books_info($uid,$book_code,$school_code){
            //檢核書籍資訊

                //參數檢驗
                $this->verify_parameter($uid);

                //引用函式檔
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/inc/search_book_info_online/code.php");
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/center/teacher_center/inc/book/book/book_global_sid/code.php");
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/lib/php/string/code.php");
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/lib/php/vaildate/code.php");
                require_once("{$_SERVER['DOCUMENT_ROOT']}/mssr/lib/php/net/code.php");

                //建立連線
                $conn_mssr=$this->db_conn('mssr');

                //參數設置
                $uid            =(int)$uid;
                $book_code      =addslashes(trim($book_code));
                $arrys_book_info=[];

                //SQL設置
                $sql="
                        SELECT
                            `book_sid`,
                            `book_name`,
                            `book_author`,
                            `book_publisher`
                        FROM `mssr_book_class`
                        WHERE 1=1
                            AND (
                                `book_isbn_10`='{$book_code}'
                                    OR
                                `book_isbn_13`='{$book_code}'
                            )
                            AND
                            `school_code` = '{$school_code}'
                    UNION
                        SELECT
                            `book_sid`,
                            `book_name`,
                            `book_author`,
                            `book_publisher`
                        FROM `mssr_book_library`
                        WHERE 1=1
                            AND (
                                `book_isbn_10`='{$book_code}'
                                    OR
                                `book_isbn_13`='{$book_code}'
                            )
                            AND
                            `school_code` = '{$school_code}'
                    UNION
                        SELECT
                            `book_sid`,
                            `book_name`,
                            `book_author`,
                            `book_publisher`
                        FROM `mssr_book_global`
                        WHERE 1=1
                            AND (
                                `book_isbn_10`='{$book_code}'
                                    OR
                                `book_isbn_13`='{$book_code}'
                            )
                    UNION
                        SELECT
                            `book_sid`,
                            `book_name`,
                            `book_author`,
                            `book_publisher`
                        FROM `mssr_book_unverified`
                        WHERE 1=1
                            AND (
                                `book_isbn_10`='{$book_code}'
                                    OR
                                `book_isbn_13`='{$book_code}'
                            )
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array());
                if(empty($db_results)){
                //線上搜尋
                    $arry_book_info_online=search_book_info_online($book_code);

                    if(!empty($arry_book_info_online['book_name'][0])){ //$arry_book_info_online['book_name'][0]
                        $create_by      =(int)$uid;
                        $edit_by        =(int)$uid;
                        $book_id        ="NULL";
                        $book_sid_global=book_global_sid($create_by,mb_internal_encoding());

                        $ch_isbn_10=ch_isbn_10($book_code, $convert=false);
                        $ch_isbn_13=ch_isbn_13($book_code, $convert=false);

                        $_lv=0; //錯誤指標
                        if(isset($ch_isbn_10['error'])){
                            $_lv=$_lv+1;
                        }
                        if(isset($ch_isbn_13['error'])){
                            $_lv=$_lv+3;
                        }

                        switch($_lv){
                            case 1:
                            //10碼錯誤，利用13碼轉換更新
                                $book_isbn_10=isbn_13_to_10($book_code);
                                $book_isbn_13=$book_code;
                            break;

                            case 3:
                            //13碼錯誤，利用10碼轉換更新
                                $book_isbn_10=$book_code;
                                $book_isbn_13=isbn_10_to_13($book_code);
                            break;

                            case 4:
                                $err_msg='GET_BOOKS_INFO() IS INVALID';
                                $this->err_report($err_msg);
                            break;
                        }

                        $book_name      =addslashes(strip_tags($arry_book_info_online['book_name'][0]));
                        $book_author    =addslashes(strip_tags($arry_book_info_online['book_author'][0]));
                        $book_publisher =addslashes(strip_tags($arry_book_info_online['book_publisher'][0]));
                        $book_page_count=0;
                        $book_word      =0;
                        $book_note      ='';
                        $book_phonetic  ='無';
                        $keyin_cdate    ="NOW()";
                        $keyin_mdate    ="NULL";
                        $keyin_ip       =get_ip();

                        $sql="
                            # for mssr_book_global
                            INSERT INTO `mssr_book_global` SET
                                `create_by`         =  {$create_by      } ,
                                `edit_by`           =  {$edit_by        } ,
                                `book_id`           =  {$book_id        } ,
                                `book_sid`          = '{$book_sid_global}',
                                `book_isbn_10`      = '{$book_isbn_10   }',
                                `book_isbn_13`      = '{$book_isbn_13   }',
                                `book_name`         = '{$book_name      }',
                                `book_author`       = '{$book_author    }',
                                `book_publisher`    = '{$book_publisher }',
                                `book_page_count`   =  {$book_page_count} ,
                                `book_word`         =  {$book_word      } ,
                                `book_note`         = '{$book_note      }',
                                `book_phonetic`     = '{$book_phonetic  }',
                                `keyin_cdate`       =  {$keyin_cdate    } ,
                                `keyin_mdate`       =  {$keyin_mdate    } ,
                                `keyin_ip`          = '{$keyin_ip       }';
                        ";
                        $conn_mssr->exec($sql);

                        $arrys_book_info[0]['book_sid']      =$book_sid_global;
                        $arrys_book_info[0]['book_name']     =$book_name;
                        $arrys_book_info[0]['book_author']   =$book_author;
                        $arrys_book_info[0]['book_publisher']=$book_publisher;
                    }else{
                        $arrys_book_info=[];
                    }
                }else{
                    $arrys_book_info=$db_results;
                }

                //回傳
                return $arrys_book_info;
            }

            Protected function verify_parameter($uid=0){
            //參數檢驗

                if(isset($uid)&&(int)$uid!==0){

                }else{
                    $err_msg='VERIFY_PARAMETER() IS INVALID';
                    $this->err_report($err_msg);
                }
            }
            Protected function err_report($err_msg=NULL){
            //錯誤回報

                echo '<p>'.'INC MOBILE_BORROW '.$err_msg.'</p>';
                die();
            }

            //建構子
            function __construct(){
            }

            //解構子
            function __destruct(){
            }

            //自訂
            Protected function db_conn($db_name=''){
            //取得連線資訊

                if(!is_string($db_name)||trim($db_name)===''){
                    $err_msg='DB_CONN() IS INVALID';
                    $this->err_report($err_msg);
                }

                switch(trim($db_name)){
                    case 'mssr':
                        $arry_conn=array(
                            'db_host'   =>'140.115.16.104',
                            'db_name'   =>'mssr',
                            'db_user'   =>'mssr',
                            'db_pass'   =>'UeR1up0u',
                            'db_encode' =>'UTF8'
                        );
                        $db_host  =$arry_conn['db_host'];
                        $db_user  =$arry_conn['db_user'];
                        $db_pass  =$arry_conn['db_pass'];
                        $db_name  =$arry_conn['db_name'];
                        $db_encode=$arry_conn['db_encode'];
                    break;
                    case 'user':
                        $arry_conn=array(
                            'db_host'   =>'140.115.16.104',
                            'db_name'   =>'user',
                            'db_user'   =>'mssr',
                            'db_pass'   =>'UeR1up0u',
                            'db_encode' =>'UTF8'
                        );
                        $db_host  =$arry_conn['db_host'];
                        $db_user  =$arry_conn['db_user'];
                        $db_pass  =$arry_conn['db_pass'];
                        $db_name  =$arry_conn['db_name'];
                        $db_encode=$arry_conn['db_encode'];
                    break;
                    default:
                        $err_msg='DB_CONN() IS INVALID';
                        $this->err_report($err_msg);
                    break;
                }

                //連結物件判斷
                $conn_info='mysql'.":host={$db_host}".";dbname={$db_name}";
                $options = array(
                    PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,           //設置錯誤提示，只獲取代碼
                    PDO::ATTR_CASE,              PDO::CASE_NATURAL,             //列名按照原始的方式
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode}"    //設置語系
                );

                //執行連線
                try{
                    $conn=@new PDO($conn_info, $db_user, $db_pass,$options);
                }catch(PDOException $e){
                    $err_msg='DB_CONN() IS INVALID';
                    $this->err_report($err_msg);
                }

                //回傳
                return $conn;
            }
            Protected function db_result($conn_type='pdo',$conn='',$sql,$arry_limit=array()){
            //---------------------------------------------------
            //取得資料筆數
            //---------------------------------------------------
            //$conn_type    資料庫連結類型      mysql | pdo     預設 mysql
            //$conn         資料庫連結物件
            //$sql          SQL查詢字串
            //$arry_limit   資料筆數限制陣列(等同LIMIT inx,size)
            //---------------------------------------------------

                //檢核參數
                if(!in_array(trim($conn_type),array('','mysql','pdo'))){
                    $err='DB_RESULT:CONN_TYPE INVALID';
                    die($err);
                }else{
                    if($conn_type===''){
                        $conn_type='mysql';
                    }
                }
                if((!$conn)||(!is_object($conn))){
                    $err='DB_RESULT:NO CONN';
                    die($err);
                }
                if(!$sql){
                    $err='DB_RESULT:NO SQL';
                    die($err);
                }

                switch($conn_type){
                //資料庫連結類型

                    case 'pdo':
                    //連結類型為pdo

                        //連結物件判斷
                        $has_conn=false;

                        if(!$conn){
                            $err='DB_RESULT:NO CONN';
                            die($err);
                        }else{
                            $has_conn=false;
                        }

                        //SQL敘述
                        if(!empty($arry_limit)){
                           $a=$arry_limit[0];
                           $b=$arry_limit[1];
                           $sql.=" LIMIT {$a},{$b}";
                        }
                        //echo $sql;

                        //資料庫
                        $err='DB_RESULT:QUERY FAIL';
                        $result=$conn->query($sql) or
                        die($err);

                        //建立資料集陣列
                        $arry_result=array();

                        if(($result->rowCount())!==0){
                            while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                                $arry_result[]=$arry_row;
                            }
                        }

                        //傳回資料集陣列
                        return $arry_result;

                        //釋放資源
                        //mysql_free_result($result);
                        if($has_conn==true){
                            $conn=NULL;
                        }

                    break;

                    default:
                    //例外處理
                        $err='DB_RESULT:CONN_TYPE INVALID';
                        die($err);
                    break;
                }
            }
        }
?>