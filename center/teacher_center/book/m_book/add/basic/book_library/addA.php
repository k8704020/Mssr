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
        require_once(str_repeat("../",7).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/vaildate/code',
                    APP_ROOT.'lib/php/string/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code',
                    APP_ROOT.'lib/php/fso/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['center']['teacher_center']){
            $url=str_repeat("../",8).'index.php';
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
    //search_type       搜尋位置
    //book_name         書籍名稱
    //book_author       作者
    //book_publisher    出版社
    //book_isbn_10      isbn10碼
    //book_isbn_13      isbn13碼
    //book_library_code 圖書館編碼

        $get_chk=array(
            'search_type        ',
            'book_name          ',
            'book_author        ',
            'book_publisher     ',
            'book_isbn_10       ',
            'book_isbn_13       ',
            'book_library_code  '
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
    //search_type       搜尋位置
    //book_name         書籍名稱
    //book_author       作者
    //book_publisher    出版社
    //book_isbn_10      isbn10碼
    //book_isbn_13      isbn13碼
    //book_library_code 圖書館編碼

        //GET
        $search_type        =trim($_GET[trim('search_type       ')]);
        $book_name          =trim($_GET[trim('book_name         ')]);
        $book_author        =trim($_GET[trim('book_author       ')]);
        $book_publisher     =trim($_GET[trim('book_publisher    ')]);
        $book_isbn_10       =trim($_GET[trim('book_isbn_10      ')]);
        $book_isbn_13       =trim($_GET[trim('book_isbn_13      ')]);
        $book_library_code  =trim($_GET[trim('book_library_code ')]);
        $book_source        =(isset($_GET['book_source']))?trim($_GET['book_source']):'school';

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
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //search_type       搜尋位置
    //book_name         書籍名稱
    //book_author       作者
    //book_publisher    出版社
    //book_isbn_10      isbn10碼
    //book_isbn_13      isbn13碼
    //book_library_code 圖書館編碼

        $arry_err=array();

        if($search_type===''){
           $arry_err[]='搜尋位置,未輸入!';
        }else{
            if(!in_array($search_type,array('online','local'))){
                $arry_err[]='搜尋位置,錯誤!';
            }
        }
        if($book_name===''){
           $arry_err[]='書籍名稱,未輸入!';
        }
        //if($book_author===''){
        //   $arry_err[]='作者,未輸入!';
        //}
        //if($book_publisher===''){
        //   $arry_err[]='出版社,未輸入!';
        //}

        // if(($book_isbn_10==='')&&($book_isbn_13==='')){
        //     $arry_err[]='isbn13碼,isbn13碼 未輸入!';
        // }else{
        //     if($book_isbn_10===''){
        //        $book_isbn_10=isbn_13_to_10($book_isbn_13);
        //     }else{
        //         $ch_isbn_10=ch_isbn_10($book_isbn_10, $convert=false);
        //         if(isset($ch_isbn_10['error'])){
        //             $arry_err[]='isbn10碼,錯誤!';
        //         }
        //     }
        //     if($book_isbn_13===''){
        //        $book_isbn_13=isbn_10_to_13($book_isbn_10);
        //     }else{
        //         $ch_isbn_13=ch_isbn_13($book_isbn_13, $convert=false);
        //         if(isset($ch_isbn_13['error'])){
        //             $arry_err[]='isbn13碼,錯誤!';
        //         }
        //     }
        // }

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
        //search_type       搜尋位置
        //book_name         書籍名稱
        //book_author       作者
        //book_publisher    出版社
        //book_isbn_10      isbn10碼
        //book_isbn_13      isbn13碼
        //book_library_code 圖書館編碼

            $search_type        =mysql_prep($search_type            );
            $book_name          =mysql_prep($book_name              );
            $book_author        =mysql_prep($book_author            );
            $book_publisher     =mysql_prep($book_publisher         );
            $book_isbn_10       =mysql_prep($book_isbn_10           );
            $book_isbn_13       =mysql_prep($book_isbn_13           );
            $book_library_code  =mysql_prep($book_library_code      );
            $book_source        =mysql_prep($book_source            );

            $sess_user_id       =(int)$sess_user_id;

            if($has_class_code){
                $sess_class_code    =mysql_prep($sess_class_code    );
                $sess_grade         =(int)$sess_grade;
                $sess_classroom     =(int)$sess_classroom;
            }

            $sess_school_code   =mysql_prep($sess_school_code       );

            //-------------------------------------------
            //檢核借閱書學校關聯
            //-------------------------------------------

                $other_school_code=book_borrow_school_rev($db_type='mysql',$arry_conn_mssr,$sess_school_code);

            //-------------------------------------------
            //檢核圖書館編碼
            //-------------------------------------------

                if($book_source==='school'){
                    $sql="
                        SELECT
                            `book_library_code`
                        FROM `mssr_book_library`
                        WHERE 1=1
                            AND `book_library_code` ='{$book_library_code}'
                            AND `book_source`       ='{$book_source      }'
                    ";
                    if(trim($other_school_code)!==''){
                        $sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                    }else{
                        $sql.="AND `school_code`='{$sess_school_code}'";
                    }

                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    $numrow=count($arrys_result);
                    if($numrow!==0){
                        $msg="圖書館條碼重複, 請重新輸入!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                history.back(-1);
                            </script>
                        ";
                        die($jscript_back);
                    }
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

            $create_by          =(int)$sess_user_id;
            $edit_by            =(int)$sess_user_id;
            $sess_school_code   =mysql_prep(strip_tags($sess_school_code));

            if($has_class_code){
                $school_category    =(int)$class_category;
                $grade_id           =(int)$sess_grade;
                $classroom_id       =(int)$sess_classroom;
            }else{
                $school_category    =(int)1;
                $grade_id           =(int)1;
                $classroom_id       =(int)1;
            }

            $book_id            ="NULL";

            $book_sid_library   =book_library_sid($create_by,mb_internal_encoding());
            $book_sid_global    =book_global_sid($create_by,mb_internal_encoding());

            $book_isbn_10       =mysql_prep(strip_tags($book_isbn_10));
            $book_isbn_13       =mysql_prep(strip_tags($book_isbn_13));
            $book_library_code  =mysql_prep(strip_tags($book_library_code));
            $book_no            =1;
            $book_name          =mysql_prep(strip_tags($book_name));
            $book_author        =mysql_prep(strip_tags($book_author));
            $book_publisher     =mysql_prep(strip_tags($book_publisher));
            $book_page_count    =0;
            $book_word          =0;
            $book_note          ='';
            $book_phonetic      ='無';
            $keyin_cdate        ="NOW()";
            $keyin_mdate        ="NULL";
            $keyin_ip           =get_ip();
            $book_source        =mysql_prep($book_source);

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            switch($search_type){
                case 'local':
                    $sql="
                        # for mssr_book_library
                        INSERT INTO `mssr_book_library` SET
                            `create_by`         =  {$create_by          } ,
                            `edit_by`           =  {$edit_by            } ,
                            `book_source`       = '{$book_source        }',
                            `school_code`       = '{$sess_school_code   }',
                            `school_category`   =  {$school_category    } ,
                            `grade_id`          =  {$grade_id           } ,
                            `classroom_id`      =  {$classroom_id       } ,
                            `book_id`           =  {$book_id            } ,
                            `book_sid`          = '{$book_sid_library   }',
                            `book_isbn_10`      = '{$book_isbn_10       }',
                            `book_isbn_13`      = '{$book_isbn_13       }',
                            `book_library_code` = '{$book_library_code  }',
                            `book_no`           =  {$book_no            } ,
                            `book_name`         = '{$book_name          }',
                            `book_author`       = '{$book_author        }',
                            `book_publisher`    = '{$book_publisher     }',
                            `book_page_count`   =  {$book_page_count    } ,
                            `book_word`         =  {$book_word          } ,
                            `book_note`         = '{$book_note          }',
                            `book_phonetic`     = '{$book_phonetic      }',
                            `keyin_cdate`       =  {$keyin_cdate        } ,
                            `keyin_mdate`       =  {$keyin_mdate        } ,
                            `keyin_ip`          = '{$keyin_ip           }';

                        # for mssr_book_global
                        INSERT IGNORE INTO `mssr_book_global` SET
                            `create_by`         =  {$create_by          } ,
                            `edit_by`           =  {$edit_by            } ,
                            `book_id`           =  {$book_id            } ,
                            `book_sid`          = '{$book_sid_global    }',
                            `book_isbn_10`      = '{$book_isbn_10       }',
                            `book_isbn_13`      = '{$book_isbn_13       }',
                            `book_name`         = '{$book_name          }',
                            `book_author`       = '{$book_author        }',
                            `book_publisher`    = '{$book_publisher     }',
                            `book_page_count`   =  {$book_page_count    } ,
                            `book_word`         =  {$book_word          } ,
                            `book_note`         = '{$book_note          }',
                            `book_phonetic`     = '{$book_phonetic      }',
                            `keyin_cdate`       =  {$keyin_cdate        } ,
                            `keyin_mdate`       =  {$keyin_mdate        } ,
                            `keyin_ip`          = '{$keyin_ip           }';
                    ";
                break;

                case 'online':
                    $sql="
                        # for mssr_book_library
                        INSERT INTO `mssr_book_library` SET
                            `create_by`         =  {$create_by          } ,
                            `edit_by`           =  {$edit_by            } ,
                            `book_source`       = '{$book_source        }',
                            `school_code`       = '{$sess_school_code   }',
                            `school_category`   =  {$school_category    } ,
                            `grade_id`          =  {$grade_id           } ,
                            `classroom_id`      =  {$classroom_id       } ,
                            `book_id`           =  {$book_id            } ,
                            `book_sid`          = '{$book_sid_library   }',
                            `book_isbn_10`      = '{$book_isbn_10       }',
                            `book_isbn_13`      = '{$book_isbn_13       }',
                            `book_library_code` = '{$book_library_code  }',
                            `book_no`           =  {$book_no            } ,
                            `book_name`         = '{$book_name          }',
                            `book_author`       = '{$book_author        }',
                            `book_publisher`    = '{$book_publisher     }',
                            `book_page_count`   =  {$book_page_count    } ,
                            `book_word`         =  {$book_word          } ,
                            `book_note`         = '{$book_note          }',
                            `book_phonetic`     = '{$book_phonetic      }',
                            `keyin_cdate`       =  {$keyin_cdate        } ,
                            `keyin_mdate`       =  {$keyin_mdate        } ,
                            `keyin_ip`          = '{$keyin_ip           }';

                        # for mssr_book_global
                        INSERT INTO `mssr_book_global` SET
                            `create_by`         =  {$create_by          } ,
                            `edit_by`           =  {$edit_by            } ,
                            `book_id`           =  {$book_id            } ,
                            `book_sid`          = '{$book_sid_global    }',
                            `book_isbn_10`      = '{$book_isbn_10       }',
                            `book_isbn_13`      = '{$book_isbn_13       }',
                            `book_name`         = '{$book_name          }',
                            `book_author`       = '{$book_author        }',
                            `book_publisher`    = '{$book_publisher     }',
                            `book_page_count`   =  {$book_page_count    } ,
                            `book_word`         =  {$book_word          } ,
                            `book_note`         = '{$book_note          }',
                            `book_phonetic`     = '{$book_phonetic      }',
                            `keyin_cdate`       =  {$keyin_cdate        } ,
                            `keyin_mdate`       =  {$keyin_mdate        } ,
                            `keyin_ip`          = '{$keyin_ip           }';
                    ";
                break;
            }
            //echo "<Pre>";
            //print_r($sql);
            //echo "</Pre>";

            //送出
            $err ='DB QUERY FAIL';
            $sth=$conn_mssr->prepare($sql);
            $sth->execute()or die($err);

    //---------------------------------------------------
    //查找, 書籍圖片
    //---------------------------------------------------

        //if($search_type==='online'){
        //    //curl一般設置
        //    $_timeout=50;
        //    $_curlopt_useragent="Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1)";
        //
        //    //圖片類型允許值
        //    $allow_exts=array(
        //        trim(".jpeg"),
        //        trim(".jpg ")
        //    );
        //    $allow_mimes=array(
        //        trim("image/jpeg "),
        //        trim("image/jpg  "),
        //        trim("image/pjpeg")
        //    );
        //
        //    //圖片路徑設定
        //    $_root=str_repeat("../",7)."info/book/{$book_sid_library}/img/front/simg/";
        //    $_img_name="";
        //    $_img_name.="1.jpg";
        //    $_img_path="{$_root}{$_img_name}";
        //    $_img_path_enc=mb_convert_encoding($_img_path,$fso_enc,$page_enc);
        //
        //    //開目錄
        //    if(!is_dir("{$_root}")){
        //        mk_dir("{$_root}",$mode=0777,$recursive=true,$fso_enc);
        //    }
        //
        //    //找尋10碼圖片
        //    $_curl=curl_init();
        //    $_url="http://static.findbook.tw/image/book/{$book_isbn_10}/large";
        //    find_book_fbk_img($_curl,$_url,$_timeout,$_curlopt_useragent,$allow_exts,$allow_mimes,$page_enc,$fso_enc,$_img_path,$_img_path_enc);
        //
        //    //找尋13碼圖片
        //    $_url="http://static.findbook.tw/image/book/{$book_isbn_13}/large";
        //    find_book_fbk_img($_curl,$_url,$_timeout,$_curlopt_useragent,$allow_exts,$allow_mimes,$page_enc,$fso_enc,$_img_path,$_img_path_enc);
        //    curl_close($_curl);
        //}

    //---------------------------------------------------
    //提示頁面
    //---------------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,教師中心";

        //-----------------------------------------------
        //教師中心路徑選單
        //-----------------------------------------------

            $auth_sys_name_arry=auth_sys_name_arry();
            $FOLDER=explode('/',dirname($_SERVER['PHP_SELF']));
            $sys_ename=$FOLDER[count($FOLDER)-5];
            $mod_ename=$FOLDER[count($FOLDER)-4];
            $sys_cname='';  //系統名稱
            $mod_cname='';  //模組名稱

            foreach($auth_sys_name_arry as $key=>$val){
                if($key==$sys_ename){
                    $sys_cname=$val;
                }elseif($key==$mod_ename){
                    $mod_cname=$val;
                }
            }

            if((trim($sys_cname)=='')||(trim($mod_cname)=='')){
                $err ='teacher_center_path err!';

                if(1==2){//除錯用
                    echo "<pre>";
                    print_r($err);
                    echo "</pre>";
                    die();
                }
            }

            //連結路徑
            $sys_url ="";
            $sys_page=str_repeat("../",2)."index.php";
            $sys_arg =array(
                'sys_ename'  =>addslashes($sys_ename)
            );
            $sys_arg=http_build_query($sys_arg);
            $sys_url=$sys_page."?".$sys_arg;
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=false);?>
    <?php echo robots($allow=false);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/form/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/date/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../../../css/def.css" media="all" />

    <style>
        /* 容器微調 */
        #container, #content, #teacher_datalist_tbl{
            width:760px;
        }
    </style>
</Head>

<Body>

<!-- 容器區塊 開始 -->
<div id="container">

    <!-- 內容區塊 開始 -->
    <div id="content">
        <table id="teacher_datalist_tbl" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td align="left" valign="middle" width="400px">
                    <!-- 教師中心路徑選單 開始 -->
                    <div id="teacher_center_path">
                        <table id="teacher_center_path_cont" border="0" width="100%">
                            <tr>
                                <td align="left" valign="middle" class="menu_dot">
                                    <span class="menu_cont">
                                        <img width="12" height="12" src="../../../../../../../img/icon/blue.jpg" border="0">
                                        <a href="../../../../../index.php">教師中心</a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="../../../<?php echo htmlspecialchars($sys_url);?>">
                                            <?php echo htmlspecialchars($sys_cname);?>
                                        </a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="javascript:void(0);"><?php echo htmlspecialchars($mod_cname);?></a>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- 教師中心路徑選單 結束 -->
                </td>
                <td align="right" valign="middle">
                    <!-- 查詢表單列 開始 -->
                    <div id="qform">
                        <span id="qform1"></span>
                    </div>
                    <!-- 查詢表單列 結束 -->
                </td>
            </tr>
            <tr>
                <td width="760px" colspan="2">
                    <!-- 資料內容 開始 -->
                    <?php
                        page_ok($book_name,$book_no);
                    ?>
                    <!-- 資料內容 結束 -->
                </td>
            </tr>
        </table>
    </div>
    <!-- 內容區塊 結束 -->

</div>
<!-- 容器區塊 結束 -->

<!-- 快速切換區塊 開始 -->
<?php //echo fast_area($rd=5);?>
<!-- 快速切換區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

</script>

<?php function page_ok($book_name) {?>
<?php
//-------------------------------------------------------
//page_ok 區塊 -- 開始
//-------------------------------------------------------

    //---------------------------------------------------
    //外部變數
    //---------------------------------------------------

        //config.php
        global $PAGE_SELF;
        global $FOLDER_SELF;
        global $nl;
        global $tab;
        global $fso_enc;
        global $page_enc;

        //local
        global $psize;
        global $pinx;

        global $book_source;

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        global $arrys_login_info;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
?>
<!-- 內容 開始 -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <!-- 在此設定寬高 -->
        <td width="100%" height="350px" align="center" valign="top">
            <!-- 內容 -->
            <table border="0" width="100%" cellpadding="5" cellspacing="0" style="position:relative;top:50px;" class="table_style2">
                <tr align="center" valign="middle" class="bg_gray1">
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td height="350px" align="center" valign="middle">
                        <img src="../../../../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
                        書籍名稱：
                        <span class="fc_blue0"><?php echo htmlspecialchars($book_name);?></span> 已新增成功!<br/><br/>
                        <input type="button" id="BtnB" name="BtnB" value="繼續新增" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="1" onmouseover="this.style.cursor='pointer'">
                        <input type="button" id="BtnC" name="BtnC" value="返回書籍列表" class="ibtn_gr9030" style="margin:10px 0px;" tabindex="2" onmouseover="this.style.cursor='pointer'">
                    </td>
                </tr>
            </table>
            <!-- 內容 -->
        </td>
    </tr>
</table>
<!-- 內容 結束 -->

<?php
//-------------------------------------------------------
//資料繫結
//-------------------------------------------------------
?>
<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //變數
    var nl    ='\r\n';
    var pinx  =<?php echo addslashes($pinx);?>;     //單頁筆數
    var psize =<?php echo addslashes($psize);?>;    //目前分頁索引
    var book_source='<?php echo addslashes($book_source);?>';

    var oBtnB =document.getElementById('BtnB');     //返回
    var oBtnC =document.getElementById('BtnC');     //返回書籍列表

    oBtnC.onclick=function(){
    //返回書籍列表

        var url ='';
        var page=str_repeat('../',3)+'index.php';
        var arg ={
            'psize':psize,
            'pinx' :pinx
        };
        var _arg=[];
        for(var key in arg){
            _arg.push(key+"="+encodeURI(arg[key]));
        }
        arg=_arg.join("&");

        if(arg.length!=0){
            url+=page+"?"+arg;
        }else{
            url+=page;
        }

        go(url,'self');
    }

    oBtnB.onclick=function(){
    //繼續新增

        var url ='';
        var page=str_repeat('../',0)+'addF.php';
        var arg ={
            'book_source':book_source,
            'psize':psize,
            'pinx' :pinx
        };
        var _arg=[];
        for(var key in arg){
            _arg.push(key+"="+encodeURI(arg[key]));
        }
        arg=_arg.join("&");

        if(arg.length!=0){
            url+=page+"?"+arg;
        }else{
            url+=page;
        }

        go(url,'self');
    }

</script>

<?php
//-------------------------------------------------------
//page_ok 區塊 -- 結束
//-------------------------------------------------------
?>
<?php };?>