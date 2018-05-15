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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_rec');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //user_id       使用者主索引(被評論人)
    //book_sid      書籍識別碼
    //anchor        錨點

        $get_chk=array(
            'user_id ',
            'book_sid',
            'anchor  '
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
    //user_id       使用者主索引(被評論人)
    //book_sid      書籍識別碼
    //anchor        錨點

        //GET
        $user_id    =trim($_GET[trim('user_id ')]);
        $book_sid   =trim($_GET[trim('book_sid')]);
        $anchor     =trim($_GET[trim('anchor  ')]);

        //SESSION
        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);
        $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
        $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
        $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];

        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //user_id       使用者主索引(被評論人)
    //book_sid      書籍識別碼
    //anchor        錨點

        $arry_err=array();

        if($user_id===''){
           $arry_err[]='使用者主索引(被評論人),未輸入!';
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $arry_err[]='使用者主索引(被評論人),錯誤!';
            }
        }
        if($book_sid===''){
           $arry_err[]='書籍識別碼,未輸入!';
        }

        if(count($arry_err)!==0){
            if(1==1){//除錯用
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
        //user_id       使用者主索引(被評論人)
        //book_sid      書籍識別碼
        //anchor        錨點

            $sess_user_id   =(int)$sess_user_id;
            $sess_grade     =(int)$sess_grade;
            $sess_classroom =(int)$sess_classroom;

            $user_id        =(int)($user_id );
            $book_sid       =mysql_prep($book_sid);

            //推薦識別碼陣列
            $arrys_rec_sid=array();
            $inx=0;

            //-------------------------------------------
            //檢核
            //-------------------------------------------

                //---------------------------------------
                //查詢各項推薦最後一篇的內容
                //---------------------------------------

                    //-----------------------------------
                    //查找, 圖片資訊
                    //-----------------------------------

                        $rec_draw_info=get_rec_info($conn_mssr,$user_id,$book_sid,$rec_type='draw',$array_filter=array("rec_sid","rec_state"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                        if(!empty($rec_draw_info)){
                            $rs_rec_draw_sid    =trim($rec_draw_info[0]['rec_sid']);
                            $rs_rec_draw_state  =trim($rec_draw_info[0]['rec_state']);
                            if($rs_rec_draw_state==='顯示'){
                                //匯入推薦識別碼陣列
                                $arrys_rec_sid[$inx]['rec_sid']       =$rs_rec_draw_sid;
                                $arrys_rec_sid[$inx]['comment_type']  ='draw';
                                $inx++;
                            }
                        }

                    //-----------------------------------
                    //查找, 文字資訊
                    //-----------------------------------

                        $rec_text_info=get_rec_info($conn_mssr,$user_id,$book_sid,$rec_type='text',$array_filter=array("rec_sid","rec_state"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                        if(!empty($rec_text_info)){
                            $rs_rec_text_sid    =trim($rec_text_info[0]['rec_sid']);
                            $rs_rec_text_state  =trim($rec_text_info[0]['rec_state']);
                            if($rs_rec_text_state==='顯示'){
                                //匯入推薦識別碼陣列
                                $arrys_rec_sid[$inx]['rec_sid']       =$rs_rec_text_sid;
                                $arrys_rec_sid[$inx]['comment_type']  ='text';
                                $inx++;
                            }
                        }

                    //-----------------------------------
                    //查找, 錄音資訊
                    //-----------------------------------

                        $rec_record_info=get_rec_info($conn_mssr,$user_id,$book_sid,$rec_type='record',$array_filter=array("rec_sid","rec_state"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                        if(!empty($rec_record_info)){
                            $rs_rec_record_sid    =trim($rec_record_info[0]['rec_sid']);
                            $rs_rec_record_state  =trim($rec_record_info[0]['rec_state']);
                            if($rs_rec_record_state==='顯示'){
                                //匯入推薦識別碼陣列
                                $arrys_rec_sid[$inx]['rec_sid']       =$rs_rec_record_sid;
                                $arrys_rec_sid[$inx]['comment_type']  ='record';
                                $inx++;
                            }
                        }

                //---------------------------------------
                //檢核被評論人資訊
                //---------------------------------------
                    $sql="
                        SELECT
                            `user_id`,
                            `map_item`,
                            `box_item`,
                            `user_coin`
                        FROM `mssr_user_info`
                        WHERE 1=1
                            AND `user_id` = {$user_id }
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    $numrow=count($arrys_result);
                    if($numrow===0){
                        $msg="被評論人不存在, 請重新輸入!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                history.back(-1);
                            </script>
                        ";
                        die($jscript_back);
                    }else{
                        //被評論人資訊
                        $map_item=trim($arrys_result[0]['map_item']);
                        $box_item=trim($arrys_result[0]['box_item']);
                        $user_coin=(int)$arrys_result[0]['user_coin'];
                    }

                //---------------------------------------
                //檢核書籍資訊
                //---------------------------------------

                    $get_book_info=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                    $book_name='查無書名!';
                    if(!empty($get_book_info)){
                        $book_name=trim($get_book_info[0]['book_name']);
                    }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            //SESSION
            $sess_user_id    =(int)$sess_user_id;
            $sess_grade      =(int)$sess_grade;
            $sess_classroom  =(int)$sess_classroom;

            $user_id         =(int)$user_id;
//echo "<Pre>";
//print_r($arrys_rec_sid);
//echo "</Pre>";
            foreach($arrys_rec_sid as $inx=>$arry_rec_sid){
            //-------------------------------------------
            //mssr_rec_comment_log 部分
            //-------------------------------------------

                $rec_sid        =mysql_prep(strip_tags($arry_rec_sid['rec_sid']));
                $book_sid       =mysql_prep(strip_tags($book_sid));
                $log_id         ="NULL";
                $comment_type   =mysql_prep(strip_tags($arry_rec_sid['comment_type']));
                $comment_content='';
                $comment_score  =(int)1;
                $has_del_rec    ='有';
                $comment_coin   =(int)0;
                $keyin_cdate    ="NOW()";
                $keyin_ip       =get_ip();

            //-------------------------------------------
            //mssr_tx_sys_log 部分
            //-------------------------------------------

                $edit_by    =(int)$user_id;
                $tx_sid     =tx_sid($user_id,'tx_gift',mb_internal_encoding());
                $tx_item    ='';
                $tx_coin    =(int)$comment_coin;
                $tx_state   ='正常';
                $tx_note    ='';
                $keyin_mdate="NULL";

            //-------------------------------------------
            //mssr_user_item_log 部分
            //-------------------------------------------

                $has_coin   =true;
                $tx_type    ='teacher_comment';
                $map_item   =mysql_prep(strip_tags($map_item));
                $box_item   =mysql_prep(strip_tags($box_item));
                $log_note   ='';

                $lv=0;
                if($has_coin){
                    $lv=$lv+1;
                }
                if($has_del_rec==='有'){
                    $lv=$lv+3;
                }

                $user_coin  =(int)$user_coin;
                switch($lv){
                    case 0:
                        $user_coin  =(int)$user_coin;
                    break;

                    case 1:
                        if(preg_match("/^-/i",$tx_coin)){
                            $user_coin=$user_coin-(int)mb_substr($tx_coin,1);
                        }else{
                            $user_coin=$user_coin+(int)$tx_coin;
                        }
                    break;

                    case 3:
                        $user_coin=$user_coin-100;
                    break;

                    case 4:
                        if(preg_match("/^-/i",$tx_coin)){
                            $user_coin=$user_coin-(int)mb_substr($tx_coin,1)-100;
                        }else{
                            $user_coin=$user_coin+(int)$tx_coin-100;
                        }
                    break;
                }
                if($user_coin<0){
                //避免負資產
                    $user_coin=0;
                }

            //-------------------------------------------
            //mssr_user_info 部分
            //-------------------------------------------

            //-------------------------------------------
            //mssr_msg_log 部分
            //-------------------------------------------

                switch($comment_type){
                    case 'draw':
                        $log_text ="老師已經針對你的 {$book_name} 書籍的畫圖推薦做出評論 !";
                    break;

                    case 'record':
                        $log_text ="老師已經針對你的 {$book_name} 書籍的錄音推薦做出評論 !";
                    break;

                    case 'star':
                        $log_text ="老師已經針對你的 {$book_name} 書籍的評星推薦做出評論 !";
                    break;

                    case 'text':
                        $log_text ="老師已經針對你的 {$book_name} 書籍的文字推薦做出評論 !";
                    break;

                    default:
                        $log_text ="老師已經針對你的 {$book_name} 書籍做出評論 !";
                    break;
                }

            //-------------------------------------------
            //處理
            //-------------------------------------------

                $lv=0;
                if($has_coin){
                    $lv=$lv+1;
                }
                if($has_del_rec==='有'){
                    $lv=$lv+3;
                }
                switch($lv){
                    case 0:
                    //無獎懲無刪除推薦資料
                        $sql="
                            # for mssr_rec_comment_log
                            INSERT INTO `mssr_rec_comment_log` SET
                                `user_id`           = {$sess_user_id        } ,
                                `comment_to`        = {$user_id             } ,
                                `rec_sid`           ='{$rec_sid             }',
                                `book_sid`          ='{$book_sid            }',
                                `log_id`            = {$log_id              } ,
                                `comment_type`      ='{$comment_type        }',
                                `comment_content`   ='{$comment_content     }',
                                `comment_score`     = {$comment_score       } ,
                                `comment_coin`      = {$comment_coin        } ,
                                `has_del_rec`       ='{$has_del_rec         }',
                                `keyin_cdate`       = {$keyin_cdate         } ,
                                `keyin_ip`          ='{$keyin_ip            }';

                            # for mssr_msg_log
                            INSERT INTO `mssr_msg_log` SET
                                `user_id`           = {$user_id             } ,
                                `from_id`           = {$sess_user_id        } ,
                                `log_id`            = {$log_id              } ,
                                `log_text`          ='{$log_text            }',
                                `log_state`         =1                 ,
                                `keyin_cdate`       = {$keyin_cdate         } ,
                                `keyin_mdate`       = {$keyin_mdate         } ;
                        ";
                    break;

                    case 1:
                    //有獎懲無刪除推薦資料
                        if($tx_coin<0){
                            $log_coin =abs($tx_coin);
                            $log_text.=" 並扣除了{$log_coin}元 !";
                        }

                        $sql="
                            # for mssr_msg_log
                            INSERT INTO `mssr_msg_log` SET
                                `user_id`           = {$user_id             } ,
                                `from_id`           = {$sess_user_id        } ,
                                `log_id`            = {$log_id              } ,
                                `log_text`          ='{$log_text            }',
                                `log_state`         =1                 ,
                                `keyin_cdate`       = {$keyin_cdate         } ,
                                `keyin_mdate`       = {$keyin_mdate         } ;
                        ";
                        $conn_mssr->exec($sql);
                        $msg_id=$conn_mssr->lastInsertId();

                        if($tx_coin>0){
                            $sql="
                                # for mssr_tx_gift_log
                                INSERT INTO `mssr_tx_gift_log` SET
                                    `edit_by`           = {$edit_by             } ,
                                    `msg_id`            = {$msg_id              } ,
                                    `tx_from`           = {$sess_user_id        } ,
                                    `tx_to`             = {$user_id             } ,
                                    `log_id`            = {$log_id              } ,
                                    `tx_sid`            ='{$tx_sid              }',
                                    `tx_item`           ='{$tx_item             }',
                                    `tx_coin`           = {$tx_coin             } ,
                                    `tx_state`          ='未領取'                 ,
                                    `tx_note`           ='{$tx_note             }',
                                    `keyin_cdate`       = {$keyin_cdate         } ,
                                    `keyin_mdate`       = {$keyin_mdate         } ,
                                    `keyin_ip`          ='{$keyin_ip            }';
                            ";
                        }else{
                            $tx_sid=tx_sid($user_id,'tx_sys',mb_internal_encoding());

                            $sql="
                                # for mssr_tx_sys_log
                                INSERT INTO `mssr_tx_sys_log` SET
                                    `edit_by`           = {$edit_by             } ,
                                    `user_id`           = {$user_id             } ,
                                    `log_id`            = {$log_id              } ,
                                    `tx_sid`            ='{$tx_sid              }',
                                    `tx_item`           ='{$tx_item             }',
                                    `tx_coin`           = {$tx_coin             } ,
                                    `tx_state`          ='{$tx_state            }',
                                    `tx_note`           ='{$tx_note             }',
                                    `keyin_cdate`       = {$keyin_cdate         } ,
                                    `keyin_mdate`       = {$keyin_mdate         } ,
                                    `keyin_ip`          ='{$keyin_ip            }';

                                # for mssr_user_info
                                UPDATE `mssr_user_info` SET
                                    `user_coin`         = {$user_coin           }
                                WHERE 1=1
                                    AND `user_id`       = {$user_id             }
                                LIMIT 1;
                            ";
                        }
                        $conn_mssr->exec($sql);

                        $sql="
                            # for mssr_rec_comment_log
                            INSERT INTO `mssr_rec_comment_log` SET
                                `user_id`           = {$sess_user_id        } ,
                                `comment_to`        = {$user_id             } ,
                                `rec_sid`           ='{$rec_sid             }',
                                `book_sid`          ='{$book_sid            }',
                                `log_id`            = {$log_id              } ,
                                `comment_type`      ='{$comment_type        }',
                                `comment_content`   ='{$comment_content     }',
                                `comment_score`     = {$comment_score       } ,
                                `comment_coin`      = {$comment_coin        } ,
                                `has_del_rec`       ='{$has_del_rec         }',
                                `keyin_cdate`       = {$keyin_cdate         } ,
                                `keyin_ip`          ='{$keyin_ip            }';

                            # for mssr_user_item_log
                            INSERT INTO `mssr_user_item_log` SET
                                `edit_by`           = {$edit_by             } ,
                                `user_id`           = {$user_id             } ,
                                `tx_sid`            ='{$tx_sid              }',
                                `log_id`            = {$log_id              } ,
                                `tx_type`           ='{$tx_type             }',
                                `map_item`          ='{$map_item            }',
                                `box_item`          ='{$box_item            }',
                                `user_coin`         = {$user_coin           } ,
                                `log_state`         ='正常'                   ,
                                `log_note`          ='{$log_note            }',
                                `keyin_cdate`       = {$keyin_cdate         } ,
                                `keyin_mdate`       = {$keyin_mdate         } ,
                                `keyin_ip`          ='{$keyin_ip            }';
                        ";
                    break;

                    case 3:
                    //無獎懲有刪除推薦資料
                        $log_text.=" 並扣除了100元 !";
                        $sql="
                            # for mssr_msg_log
                            INSERT INTO `mssr_msg_log` SET
                                `user_id`           = {$user_id             } ,
                                `from_id`           = {$sess_user_id        } ,
                                `log_id`            = {$log_id              } ,
                                `log_text`          ='{$log_text            }',
                                `log_state`         =1                 ,
                                `keyin_cdate`       = {$keyin_cdate         } ,
                                `keyin_mdate`       = {$keyin_mdate         } ;
                        ";
                        $conn_mssr->exec($sql);
                        $msg_id=$conn_mssr->lastInsertId();
                        $tx_sid=tx_sid($user_id,'tx_sys',mb_internal_encoding());

                        $sql="
                            # for mssr_rec_comment_log
                            INSERT INTO `mssr_rec_comment_log` SET
                                `user_id`           = {$sess_user_id        } ,
                                `comment_to`        = {$user_id             } ,
                                `rec_sid`           ='{$rec_sid             }',
                                `book_sid`          ='{$book_sid            }',
                                `log_id`            = {$log_id              } ,
                                `comment_type`      ='{$comment_type        }',
                                `comment_content`   ='{$comment_content     }',
                                `comment_score`     = {$comment_score       } ,
                                `comment_coin`      = -100                    ,
                                `has_del_rec`       ='{$has_del_rec         }',
                                `keyin_cdate`       = {$keyin_cdate         } ,
                                `keyin_ip`          ='{$keyin_ip            }';

                            # for mssr_tx_sys_log
                            INSERT INTO `mssr_tx_sys_log` SET
                                `edit_by`           = {$edit_by             } ,
                                `user_id`           = {$user_id             } ,
                                `log_id`            = {$log_id              } ,
                                `tx_sid`            ='{$tx_sid              }',
                                `tx_item`           ='{$tx_item             }',
                                `tx_coin`           = -100                    ,
                                `tx_state`          ='{$tx_state            }',
                                `tx_note`           ='{$tx_note             }',
                                `keyin_cdate`       = {$keyin_cdate         } ,
                                `keyin_mdate`       = {$keyin_mdate         } ,
                                `keyin_ip`          ='{$keyin_ip            }';

                            # for mssr_user_item_log
                            INSERT INTO `mssr_user_item_log` SET
                                `edit_by`           = {$edit_by             } ,
                                `user_id`           = {$user_id             } ,
                                `tx_sid`            ='{$tx_sid              }',
                                `log_id`            = {$log_id              } ,
                                `tx_type`           ='{$tx_type             }',
                                `map_item`          ='{$map_item            }',
                                `box_item`          ='{$box_item            }',
                                `user_coin`         = {$user_coin           } ,
                                `log_state`         ='正常'                   ,
                                `log_note`          ='{$log_note            }',
                                `keyin_cdate`       = {$keyin_cdate         } ,
                                `keyin_mdate`       = {$keyin_mdate         } ,
                                `keyin_ip`          ='{$keyin_ip            }';

                            # for mssr_user_info
                            UPDATE `mssr_user_info` SET
                                `user_coin`         = {$user_coin           }
                            WHERE 1=1
                                AND `user_id`       = {$user_id             }
                            LIMIT 1;

                            # for mssr_rec_book_{$comment_type}
                            UPDATE `mssr_rec_book_{$comment_type}` SET
                                `rec_state`         ='隱藏'
                            WHERE 1=1
                                AND `rec_sid`       ='{$rec_sid             }'
                            LIMIT 1;

                            # for mssr_rec_book_{$comment_type}_log
                            UPDATE `mssr_rec_book_{$comment_type}_log` SET
                                `rec_state`         ='隱藏'
                            WHERE 1=1
                                AND `rec_sid`       ='{$rec_sid             }'
                            LIMIT 1;
                        ";
                    break;

                    case 4:
                    //有獎懲有刪除推薦資料
                        $comment_coin=$comment_coin-100;
                        $tx_coin     =$tx_coin-100;

                        if($tx_coin<0){
                            $log_coin =abs($tx_coin);
                            $log_text.=" 並扣除了{$log_coin}元 !";
                        }

                        $sql="
                            # for mssr_msg_log
                            INSERT INTO `mssr_msg_log` SET
                                `user_id`           = {$user_id             } ,
                                `from_id`           = {$sess_user_id        } ,
                                `log_id`            = {$log_id              } ,
                                `log_text`          ='{$log_text            }',
                                `log_state`         =1                        ,
                                `keyin_cdate`       = {$keyin_cdate         } ,
                                `keyin_mdate`       = {$keyin_mdate         } ;
                        ";
                        $conn_mssr->exec($sql);
                        $msg_id=$conn_mssr->lastInsertId();

                        if($tx_coin>0){
                            $sql="
                                # for mssr_tx_gift_log
                                INSERT INTO `mssr_tx_gift_log` SET
                                    `edit_by`           = {$edit_by             } ,
                                    `msg_id`            = {$msg_id              } ,
                                    `tx_from`           = {$sess_user_id        } ,
                                    `tx_to`             = {$user_id             } ,
                                    `log_id`            = {$log_id              } ,
                                    `tx_sid`            ='{$tx_sid              }',
                                    `tx_item`           ='{$tx_item             }',
                                    `tx_coin`           = {$tx_coin             } ,
                                    `tx_state`          ='未領取'                 ,
                                    `tx_note`           ='{$tx_note             }',
                                    `keyin_cdate`       = {$keyin_cdate         } ,
                                    `keyin_mdate`       = {$keyin_mdate         } ,
                                    `keyin_ip`          ='{$keyin_ip            }';
                            ";
                        }else{
                            $tx_sid=tx_sid($user_id,'tx_sys',mb_internal_encoding());

                            $sql="
                                # for mssr_tx_sys_log
                                INSERT INTO `mssr_tx_sys_log` SET
                                    `edit_by`           = {$edit_by             } ,
                                    `user_id`           = {$user_id             } ,
                                    `log_id`            = {$log_id              } ,
                                    `tx_sid`            ='{$tx_sid              }',
                                    `tx_item`           ='{$tx_item             }',
                                    `tx_coin`           = {$tx_coin             } ,
                                    `tx_state`          ='{$tx_state            }',
                                    `tx_note`           ='{$tx_note             }',
                                    `keyin_cdate`       = {$keyin_cdate         } ,
                                    `keyin_mdate`       = {$keyin_mdate         } ,
                                    `keyin_ip`          ='{$keyin_ip            }';

                                # for mssr_user_info
                                UPDATE `mssr_user_info` SET
                                    `user_coin`         = {$user_coin           }
                                WHERE 1=1
                                    AND `user_id`       = {$user_id             }
                                LIMIT 1;
                            ";
                        }
                        $conn_mssr->exec($sql);

                        $sql="
                            # for mssr_rec_comment_log
                            INSERT INTO `mssr_rec_comment_log` SET
                                `user_id`           = {$sess_user_id        } ,
                                `comment_to`        = {$user_id             } ,
                                `rec_sid`           ='{$rec_sid             }',
                                `book_sid`          ='{$book_sid            }',
                                `log_id`            = {$log_id              } ,
                                `comment_type`      ='{$comment_type        }',
                                `comment_content`   ='{$comment_content     }',
                                `comment_score`     = {$comment_score       } ,
                                `comment_coin`      = {$comment_coin        } ,
                                `has_del_rec`       ='{$has_del_rec         }',
                                `keyin_cdate`       = {$keyin_cdate         } ,
                                `keyin_ip`          ='{$keyin_ip            }';

                            # for mssr_user_item_log
                            INSERT INTO `mssr_user_item_log` SET
                                `edit_by`           = {$edit_by             } ,
                                `user_id`           = {$user_id             } ,
                                `tx_sid`            ='{$tx_sid              }',
                                `log_id`            = {$log_id              } ,
                                `tx_type`           ='{$tx_type             }',
                                `map_item`          ='{$map_item            }',
                                `box_item`          ='{$box_item            }',
                                `user_coin`         = {$user_coin           } ,
                                `log_state`         ='正常'                   ,
                                `log_note`          ='{$log_note            }',
                                `keyin_cdate`       = {$keyin_cdate         } ,
                                `keyin_mdate`       = {$keyin_mdate         } ,
                                `keyin_ip`          ='{$keyin_ip            }';

                            # for mssr_rec_book_{$comment_type}
                            UPDATE `mssr_rec_book_{$comment_type}` SET
                                `rec_state`         ='隱藏'
                            WHERE 1=1
                                AND `rec_sid`       ='{$rec_sid             }'
                            LIMIT 1;

                            # for mssr_rec_book_{$comment_type}_log
                            UPDATE `mssr_rec_book_{$comment_type}_log` SET
                                `rec_state`         ='隱藏'
                            WHERE 1=1
                                AND `rec_sid`       ='{$rec_sid             }'
                            LIMIT 1;
                        ";
                    break;
                }

                //送出
                $conn_mssr->exec($sql);

            //-------------------------------------------
            //刪除圖片
            //-------------------------------------------

                if($comment_type==='draw'){
                    if(($lv===3)||($lv===4)){
                        $root           =str_repeat("../",6)."info/user/".(int)$user_id."/book";
                        $draw_path      ="{$root}/".trim($book_sid)."/draw/bimg/1.jpg";
                        $draw_path_enc  =mb_convert_encoding($draw_path,$fso_enc,$page_enc);
                        if(file_exists($draw_path_enc)){
                            @unlink($draw_path);
                        }
                    }
                }

            //-------------------------------------------
            //刪除錄音
            //-------------------------------------------

                if($comment_type==='record'){
                    if(($lv===3)||($lv===4)){
                        $root               =str_repeat("../",6)."info/user/".(int)$user_id."/book";

                        $record_path_mp3    ="{$root}/".trim($book_sid)."/record/1.mp3";
                        $record_path_mp3_enc=mb_convert_encoding($record_path_mp3,$fso_enc,$page_enc);

                        $record_path_wav    ="{$root}/".trim($book_sid)."/record/1.wav";
                        $record_path_wav_enc=mb_convert_encoding($record_path_wav,$fso_enc,$page_enc);

                        if(file_exists($record_path_mp3_enc)){
                            @unlink($record_path_mp3);
                        }
                        if(file_exists($record_path_wav_enc)){
                            @unlink($record_path_wav);
                        }
                    }
                }
            }

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page=str_repeat("../",0)."content.php";
        $arg =array(
            'psize'=>$psize,
            'pinx' =>$pinx
        );
        $arg =http_build_query($arg);

        if(!empty($arg)){
            $url="{$page}?{$arg}";

            //建立錨點
            $url="{$url}#{$anchor}";
        }else{
            $url="{$page}";
        }

        header("Location: {$url}");
?>