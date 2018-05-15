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
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/string/code',
                    APP_ROOT.'lib/php/array/code',
                    APP_ROOT.'lib/php/vaildate/code'
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
        $sess_school_code=trim($_sess_t[trim('school_code   ')]);

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
        //更新閱讀登記數量限制
        //-----------------------------------------------

            //-------------------------------------------
            //更新 mssr_auth_class
            //-------------------------------------------

                $sess_class_code   =addslashes($_sess_t[trim('class_code')]);
                $borrow_limit_cno  =10;

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
                    }else{
                        $auth['borrow_limit_cno']=$borrow_limit_cno;
                        $auth=serialize($auth);

                        $sql="
                            UPDATE `mssr_auth_class` SET
                                `auth`='{$auth}'
                            WHERE 1=1
                                AND `class_code`='{$sess_class_code}'
                            LIMIT 1;
                        ";
                        $conn_mssr->exec($sql);
                    }
                }else{
                    $create_by   =(int)$_sess_t['uid'];
                    $edit_by     =(int)$_sess_t['uid'];
                    $class_code  =addslashes(trim($sess_class_code));
                    $auth        =serialize(array('borrow_limit_cno'=>$borrow_limit_cno));
                    $keyin_cdate ='NOW()';
                    $keyin_mdate ='NULL';
                    $keyin_ip    =get_ip();

                    $sql="
                        INSERT IGNORE INTO `mssr`.`mssr_auth_class` SET
                            `create_by`  = {$create_by  } ,
                            `edit_by`    = {$edit_by    } ,
                            `class_code` ='{$class_code }',
                            `auth`       ='{$auth       }',
                            `keyin_cdate`= {$keyin_cdate} ,
                            `keyin_mdate`= {$keyin_mdate} ,
                            `keyin_ip`   ='{$keyin_ip   }'
                    ";
                    $conn_mssr->exec($sql);
                }

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //book_code_type    書本種類
        //book_code         書本編號

            $book_code_type     =mysql_prep($book_code_type);
            $book_code          =mysql_prep($book_code);
            $sess_school_code   =mysql_prep($sess_school_code);

            $numrow             =0;             //初始化, 書本數量
            $respones           =array();       //初始化, 回傳格式
            $status             ="";            //初始化, 處理狀態
            $other_school_code  ="";            //初始化, 檢核借閱書學校關聯

        //-----------------------------------------------
        //檢核借閱書學校關聯
        //-----------------------------------------------

            $other_school_code=book_borrow_school_rev($db_type='mysql',$arry_conn_mssr,$sess_school_code);

        //-----------------------------------------------
        //檢核書籍是否存在
        //-----------------------------------------------

            $_SESSION['_read_the_registration_code']['_login']['opinion_success_flag']='no';
            $book_name='';
            $arry_book_name_filter=array();
            $arrys_result=array();

            switch($book_code_type){
                case 'library':
                //圖書館的書
                    $sql="
                        SELECT
                            `book_name`,
                            `book_library_code`
                        FROM `mssr_book_library`
                        WHERE 1=1
                            AND `book_library_code`='{$book_code}'

                    ";
                    if(trim($other_school_code)!==''){
                        $sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                    }else{
                        $sql.="AND `school_code`='{$sess_school_code}'";
                    }

                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    $numrow=count($arrys_result);
                    if($numrow!==0){
                        foreach($arrys_result as $arry_result){
                            $rs_book_name=trim($arry_result['book_name']);
                            if(in_array($rs_book_name,$arry_book_name_filter))continue;
                            $arry_book_name_filter[]=$rs_book_name;
                            if($rs_book_name!=='')$book_name.="{$rs_book_name},";
                        }
                    }
                break;

                case 'class':
                //班級的書
                    $sql="
                        SELECT
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
                    ";
                    if(trim($other_school_code)!==''){
                        $sql.="AND `mssr_book_class`.`school_code` IN ('{$sess_school_code}',{$other_school_code})";
                    }else{
                        $sql.="AND `mssr_book_class`.`school_code`='{$sess_school_code}'";
                    }

                    $sql.="
                        UNION ALL
                            SELECT
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
                        UNION ALL
                            SELECT
                                `book_name`,
                                `book_isbn_10`,
                                `book_isbn_13`
                            FROM `mssr_book_global`
                            WHERE 1=1
                                AND (
                                    `book_isbn_10`='{$book_code}'
                                        OR
                                    `book_isbn_13`='{$book_code}'
                                )
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    $numrow=count($arrys_result);
                    if($numrow!==0){
                        foreach($arrys_result as $arry_result){
                            $rs_book_name=trim($arry_result['book_name']);
                            if(in_array($rs_book_name,$arry_book_name_filter))continue;
                            $arry_book_name_filter[]=$rs_book_name;
                            if($rs_book_name!=='')$book_name.="{$rs_book_name},";
                        }
                    }

                    $arry_book_info_online=array();
                    if($numrow===0){
                    //無global書籍，線上搜尋
                        $arry_book_info_online=search_book_info_online($book_code);
                        if(!empty($arry_book_info_online)){
                            $book_name=trim($arry_book_info_online['book_name'][0]);
                            $numrow   =count($arry_book_info_online['book_name']);

                            $create_by          =(int)$_SESSION['_read_the_registration_code']['_login']['_user_id'];
                            $edit_by            =(int)$_SESSION['_read_the_registration_code']['_login']['_user_id'];
                            $book_id            ="NULL";
                            $book_sid_unverified=book_unverified_sid($create_by,mb_internal_encoding());
                            $book_sid_global    =book_global_sid($create_by,mb_internal_encoding());

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
                            }
                            $book_isbn_10       =mysql_prep(strip_tags($book_isbn_10));
                            $book_isbn_13       =mysql_prep(strip_tags($book_isbn_13));
                            $book_name          =mysql_prep(strip_tags($book_name));
                            $book_author        =mysql_prep(strip_tags($arry_book_info_online['book_author'][0]));
                            $book_publisher     =mysql_prep(strip_tags($arry_book_info_online['book_publisher'][0]));
                            $book_page_count    =(isset($arry_book_info_online['book_page_count'][0]))?(int)$arry_book_info_online['book_page_count'][0]:0;
                            $book_word          =0;
                            $book_from          =2;
                            $book_verified      =3;
                            $book_note          =(isset($arry_book_info_online['book_note'][0]))?mysql_prep(strip_tags($arry_book_info_online['book_note'][0])):'';
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
                        }else{
                            $book_name=trim("");
                            $numrow   =1;

                            $create_by          =(int)1;
                            $edit_by            =(int)1;
                            $book_id            ="NULL";
                            $book_sid_unverified=book_unverified_sid($create_by,mb_internal_encoding());

                            $book_isbn_10       =mysql_prep(strip_tags($book_code));
                            $book_isbn_13       =mysql_prep(strip_tags($book_code));
                            $book_name          =mysql_prep(strip_tags(""));
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
                        }
                    }
                break;
            }

            if($numrow===0){
            //無相關書籍
                $status="false";
            }else{
            //有相關書籍
                $status="true";
            }

            $respones=[];
            $respones[trim('book_name      ')]=$book_name;      //book_name         書本名稱
            $respones[trim('book_numrow    ')]=$numrow;         //book_numrow       書本數量
            $respones[trim('book_code_type ')]=$book_code_type; //book_code_type    書本種類
            $respones[trim('book_code      ')]=$book_code;      //book_code         書本編號
            $respones[trim('status         ')]=$status;         //status            處理狀態
            $respones=json_encode($respones);
            die($respones);
?>

