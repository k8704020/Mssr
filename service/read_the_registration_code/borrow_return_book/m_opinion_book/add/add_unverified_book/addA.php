<?php
//-------------------------------------------------------
//閱讀登記條碼版
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        ////SESSION
        //@session_start();
        //
        ////啟用BUFFER
        //@ob_start();
        //
        ////外掛設定檔
        //require_once(str_repeat("../",6).'config/config.php');
        //
        ////外掛函式檔
        //$funcs=array(
        //            APP_ROOT.'inc/code',
        //            APP_ROOT.'service/read_the_registration_code/inc/code',
        //            APP_ROOT.'center/teacher_center/inc/code',
        //
        //            APP_ROOT.'lib/php/db/code',
        //            APP_ROOT.'lib/php/net/code',
        //            APP_ROOT.'lib/php/string/code',
        //            APP_ROOT.'lib/php/array/code',
        //            APP_ROOT.'lib/php/vaildate/code'
        //            );
        //func_load($funcs,true);
        //
        ////清除並停用BUFFER
        //@ob_end_clean();

    ////---------------------------------------------------
    ////有無維護
    ////---------------------------------------------------
    //
    //    if($config_arrys['is_offline']['service']['read_the_registration_code']){
    //        $url=str_repeat("../",7).'index.php';
    //        header("Location: {$url}");
    //        die();
    //    }
    //
    ////---------------------------------------------------
    ////有無登入
    ////---------------------------------------------------
    //
    //    if(!login_check(array('t'))){
    //        $url=str_repeat("../",4).'login/loginF.php';
    //        header("Location: {$url}");
    //        die();
    //    }
    //
    ////---------------------------------------------------
    ////重複登入
    ////---------------------------------------------------
    //
    ////---------------------------------------------------
    ////SESSION
    ////---------------------------------------------------
    //
    //    //初始化，承接變數
    //    $_sess_t=$_SESSION['t'];
    //    foreach($_sess_t as $field_name=>$field_value){
    //        $$field_name=$field_value;
    //    }
    //
    ////---------------------------------------------------
    ////權限,與判斷
    ////---------------------------------------------------

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //book_code_type    書本種類
    //book_code         書本編號

        $post_chk=array(
            'book_code_type ',
            'book_code      '
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
    //book_code_type    書本種類
    //book_code         書本編號

        //POST
        $book_code_type  =trim($_POST[trim('book_code_type  ')]);
        $book_code       =trim($_POST[trim('book_code       ')]);

        //SESSION
        //$sess_school_code=trim($_sess_t[trim('school_code   ')]);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //book_code_type    書本種類
    //book_code         書本編號

        $arry_err=array();

        if($book_code_type===''){
           $arry_err[]='書本種類,未輸入!';
        }else{
           if(!in_array($book_code_type,array('library','class'))){
                $arry_err[]='書本種類,錯誤!';
           }
        }
        if($book_code===''){
           $arry_err[]='書本編號,未輸入!';
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
        //book_code_type    書本種類
        //book_code         書本編號

            $book_code_type     =mysql_prep($book_code_type);
            $book_code          =mysql_prep($book_code);

            $numrow             =0;             //初始化, 書本數量
            $respones           =array();       //初始化, 回傳格式
            $status             ="";            //初始化, 處理狀態
            $other_school_code  ="";            //初始化, 檢核借閱書學校關聯

        ////-----------------------------------------------
        ////檢核借閱書學校關聯
        ////-----------------------------------------------
        //
        //    $other_school_code=book_borrow_school_rev($db_type='mysql',$arry_conn_mssr,$sess_school_code);

        //-----------------------------------------------
        //檢核書籍是否存在
        //-----------------------------------------------
echo "<Pre>";print_r($_POST);echo "</Pre>";
die();
            $book_name=trim("");
            $numrow   =1;

            $create_by          =(int)1;
            $edit_by            =(int)1;
            $book_id            ="NULL";
            $book_sid_unverified=book_unverified_sid($create_by,mb_internal_encoding());

            $book_isbn_10       =mysql_prep(strip_tags($book_code));
            $book_isbn_13       =mysql_prep(strip_tags($book_code));
            $book_name          =mysql_prep(strip_tags($book_name));
            $book_author        =mysql_prep(strip_tags(""));
            $book_publisher     =mysql_prep(strip_tags(""));
            $book_page_count    =0;
            $book_word          =0;
            $book_from          =2;
            $book_verified      =3;
            $book_note          ='';
            $book_phonetic      ='無';
            $keyin_cdate        ="NOW()";
            $keyin_mdate        ="NULL";
            $keyin_ip           =get_ip();

            //建立自建書庫
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
                    `book_from`         =  {$book_from          } ,
                    `book_note`         = '{$book_note          }',
                    `book_phonetic`     = '{$book_phonetic      }',
                    `book_verified`     =  {$book_verified      } ,
                    `keyin_cdate`       =  {$keyin_cdate        } ,
                    `keyin_mdate`       =  {$keyin_mdate        } ,
                    `keyin_ip`          = '{$keyin_ip           }';
            ";
            //送出
            $err ='DB QUERY FAIL';
            $conn_mssr->exec($sql)
            or die($err);

            if($numrow===0){
            //無相關書籍
                $status="false";
            }else{
            //有相關書籍
                $status="true";
            }
            $respones=json_encode(array(
                "book_name"=>$book_name,            //book_name         書本名稱
                "book_numrow"=>$numrow,             //book_numrow       書本數量
                "book_code_type"=>$book_code_type,  //book_code_type    書本種類
                "book_code"=>$book_code,            //book_code         書本編號
                "status"=>$status                   //status            處理狀態
            ));
            die($respones);
?>

