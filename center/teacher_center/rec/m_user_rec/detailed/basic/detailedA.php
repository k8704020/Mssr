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
                    APP_ROOT.'service/bookstore_v2/inc/set_score_exp/code',

                    APP_ROOT.'lib/php/vaildate/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/fso/code',
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
    //rec_sid           推薦識別碼
    //comment_type      評論類型
    //comment_content   評論內容
    //comment_score     評論得分
    //comment_coin      評論得錢
    //has_del_rec       有無刪除
    //user_id           使用者主索引(被評論人)
    //book_sid          書籍識別碼
    //area              回轉的頁面
    //anchor            錨點
    //date_filter       時間條件
    //comment_public    公開留言

        $post_chk=array(
            'rec_sid        ',
            'comment_type   ',
            'comment_content',
            'comment_score  ',
            'has_del_rec    ',
            'user_id        ',
            'book_sid       ',
            'area           ',
            'anchor         ',
            'comment_public '
        );
        $post_chk=array_map("trim",$post_chk);
        foreach($post_chk as $post){
            if(!isset($_POST[$post])){
                die();
            }
        }

        //初始化, 是否有評論得錢
        $has_coin=false;
        if(isset($_POST['comment_coin'])){
            $_POST['comment_coin']=trim($_POST['comment_coin']);
            $has_coin=true;
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //rec_sid           推薦識別碼
    //comment_type      評論類型
    //comment_content   評論內容
    //comment_score     評論得分
    //comment_coin      評論得錢
    //has_del_rec       有無刪除
    //user_id           使用者主索引(被評論人)
    //book_sid          書籍識別碼
    //area              回轉的頁面
    //anchor            錨點
    //date_filter       時間條件

        //POST
        $rec_sid        =trim($_POST[trim('rec_sid          ')]);
        $comment_type   =trim($_POST[trim('comment_type     ')]);
        $comment_content=trim($_POST[trim('comment_content  ')]);
        $comment_score  =trim($_POST[trim('comment_score    ')]);
        $has_del_rec    =trim($_POST[trim('has_del_rec      ')]);
        $user_id        =trim($_POST[trim('user_id          ')]);
        $book_sid       =trim($_POST[trim('book_sid         ')]);
        $area           =trim($_POST[trim('area             ')]);
        $anchor         =trim($_POST[trim('anchor           ')]);
        $comment_public =(isset($_POST['comment_public']))?(int)$_POST['comment_public']:1;
		
		//暫存老師的選項
		setcookie("uid",$_SESSION['uid'],time()+3600*24,"/");
		setcookie("comment_public",$comment_public,time()+3600*24,"/");
		
		
        $arry_rec_content   =(isset($_POST['rec_content']))?$_POST['rec_content']:array();
        if(!empty($arry_rec_content)){
            $arry_rec_content=array_map("trim",$arry_rec_content);
            $arry_rec_content=array_map("gzcompress",$arry_rec_content);
            $arry_rec_content=array_map("base64_encode",$arry_rec_content);
            //$arry_rec_content=@serialize($arry_rec_content);
        }

        if($has_coin){
            $comment_coin=trim($_POST[trim('comment_coin    ')]);
        }else{
            $comment_coin=0;
        }

        //date_filter   時間條件
        if(isset($_POST[trim('date_filter')])){
            $date_filter=trim($_POST[trim('date_filter')]);
            if(!in_array($date_filter,array("today","three_day","one_week","two_week","lose"))){
                $date_filter='';
            }
        }else{
            $date_filter='';
        }

        $scrolltop=(isset($_POST['scrolltop']))?(int)$_POST['scrolltop']:0;
        $semester_start=(isset($_POST['semester_start']))?trim($_POST['semester_start']):'';
        $semester_end=(isset($_POST['semester_end']))?trim($_POST['semester_end']):'';
        $black_book=(isset($_POST['black_book']))?(int)$_POST['black_book']:0;

        //SESSION
        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);
        $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
        $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
        $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];

        //分頁
        $psize=(isset($_POST['psize']))?(int)$_POST['psize']:10;
        $pinx =(isset($_POST['pinx']))?(int)$_POST['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //rec_sid           推薦識別碼
    //comment_type      評論類型
    //comment_content   評論內容
    //comment_score     評論得分
    //comment_coin      評論得錢
    //has_del_rec       有無刪除
    //user_id           使用者主索引(被評論人)
    //book_sid          書籍識別碼
    //area              回轉的頁面
    //anchor            錨點
    //date_filter       時間條件

        $arry_err=array();

        if($area===''){
           $arry_err[]='回轉的頁面,未輸入!';
        }
        if($rec_sid===''){
           $arry_err[]='推薦識別碼,未輸入!';
        }
        if($comment_type===''){
           $arry_err[]='評論類型,未輸入!';
        }else{
            $comment_type=trim($comment_type);
            if(!in_array($comment_type,array('draw','text','record'))){
                $arry_err[]='評論類型,錯誤!';
            }
        }
        if($comment_score===''){
           $arry_err[]='評論得分,未輸入!';
        }else{
            $comment_score=(int)$comment_score;
            if($comment_score===0){
                $arry_err[]='評論得分,錯誤!';
            }
        }
        if($has_coin){
            if($comment_coin===''){
               $arry_err[]='評論得錢,未輸入!';
            }
        }
        if($has_del_rec===''){
           $arry_err[]='有無刪除,未輸入!';
        }else{
            $has_del_rec=trim($has_del_rec);
            if(!in_array($has_del_rec,array('有','無'))){
                $arry_err[]='有無刪除,錯誤!';
            }
        }
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
        //rec_sid           推薦識別碼
        //comment_type      評論類型
        //comment_content   評論內容
        //comment_score     評論得分
        //comment_coin      評論得錢
        //has_del_rec       有無刪除
        //user_id           使用者主索引(被評論人)
        //book_sid          書籍識別碼
        //area              回轉的頁面
        //anchor            錨點
        //date_filter       時間條件

            $sess_user_id   =(int)$sess_user_id;
            $sess_grade     =(int)$sess_grade;
            $sess_classroom =(int)$sess_classroom;

            $rec_sid        =mysql_prep($rec_sid        );
            $comment_type   =mysql_prep($comment_type   );
            $comment_content=mysql_prep($comment_content);
            $comment_score  =mysql_prep($comment_score  );
            $has_del_rec    =mysql_prep($has_del_rec    );
            $user_id        =mysql_prep($user_id        );
            $book_sid       =mysql_prep($book_sid       );
            $comment_coin   =(int)$comment_coin;

            $table_name="mssr_rec_book_{$comment_type}_log";

            //-------------------------------------------
            //檢核
            //-------------------------------------------

                //---------------------------------------
                //檢核推薦內容
                //---------------------------------------

                    $sql="
                        SELECT
                            `user_id`
                        FROM `{$table_name}`
                        WHERE 1=1
                            AND `user_id` = {$user_id }
                            AND `rec_sid` ='{$rec_sid }'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    $numrow=count($arrys_result);
                    if($numrow===0){
                        $msg="推薦內容不存在, 請重新輸入!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                history.back(-1);
                            </script>
                        ";
                        die($jscript_back);
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
                        $msg="查無學生資訊, 請通知明日星球人員!";
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

            //-------------------------------------------
            //mssr_rec_comment_public 部分
            //-------------------------------------------

                $comment_public=(int)$comment_public;

            //-------------------------------------------
            //mssr_rec_comment_log 部分
            //-------------------------------------------

                $rec_sid         =mysql_prep(strip_tags($rec_sid         ));
                $book_sid        =mysql_prep(strip_tags($book_sid        ));
                $log_id          ="NULL";
                $comment_type    =mysql_prep(strip_tags($comment_type    ));
                $comment_content =mysql_prep(strip_tags($comment_content ));
                $comment_score   =(int)$comment_score;
                $has_del_rec     =mysql_prep(strip_tags($has_del_rec     ));
                $comment_coin    =(int)$comment_coin;
                $keyin_cdate     ="NOW()";
                $keyin_ip        =get_ip();

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
            //mssr_score_exp_log 部分
            //-------------------------------------------

                $exp_type  ="comment_{$comment_type}";  //獲得類型
                $exp_score =(int)$tx_coin;              //獲得的經驗數
                if($exp_score!==0){
                    set_score_exp($conn_mssr,$exp_type,$exp_score,$user_id,$arry_conn_mssr,$sess_user_id);
                }

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

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
                        # for mssr_msg_log
                        INSERT INTO `mssr_msg_log` SET
                            `user_id`           = {$user_id             } ,
                            `from_id`           = {$sess_user_id        } ,
                            `log_id`            = {$log_id              } ,
                            `log_text`          ='{$log_text            }',
                            `log_state`         =1                 ,
                            `keyin_cdate`       = {$keyin_cdate         } ,
                            `keyin_mdate`       = {$keyin_mdate         } ;

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
                            `comment_public`    = {$comment_public      } ,
                            `has_del_rec`       ='{$has_del_rec         }',
                            `keyin_cdate`       = {$keyin_cdate         } ,
                            `keyin_ip`          ='{$keyin_ip            }';
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
                            `comment_public`    = {$comment_public      } ,
                            `has_del_rec`       ='{$has_del_rec         }',
                            `keyin_cdate`       = {$keyin_cdate         } ,
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
                            `comment_public`    = {$comment_public      } ,
                            `has_del_rec`       ='{$has_del_rec         }',
                            `keyin_cdate`       = {$keyin_cdate         } ,
                            `keyin_ip`          ='{$keyin_ip            }';
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
                            `comment_public`    = {$comment_public      } ,
                            `has_del_rec`       ='{$has_del_rec         }',
                            `keyin_cdate`       = {$keyin_cdate         } ,
                            `keyin_ip`          ='{$keyin_ip            }';
                    ";
                break;
            }
            //送出
            $conn_mssr->exec($sql);

            //修正文字推薦
            if(!empty($arry_rec_content)){
                $arry_rec_content=@serialize($arry_rec_content);
                $sql="
                    # for mssr_rec_book_{$comment_type}
                    UPDATE `mssr_rec_book_{$comment_type}` SET
                        `rec_content`='{$arry_rec_content}'
                    WHERE 1=1
                        AND `rec_sid`='{$rec_sid         }'
                    LIMIT 1;

                    # for mssr_rec_book_{$comment_type}_log
                    UPDATE `mssr_rec_book_{$comment_type}_log` SET
                        `rec_content`='{$arry_rec_content}'
                    WHERE 1=1
                        AND `rec_sid`='{$rec_sid         }'
                    LIMIT 1;
                ";
                //送出
                $conn_mssr->exec($sql);
            }

            // 書籍加入黑名單
            if($black_book===1){
                $sql="
                    # for mssr_black_book
                    INSERT IGNORE INTO `mssr_black_book` SET
                        `create_by`         =  {$sess_user_id   } ,
                        `book_nonumbering`  = '{$book_sid       }',
                        `keyin_cdate`       =  NOW()              ;
                ";
                //送出
                $conn_mssr->exec($sql);
            }

    //---------------------------------------------------
    //刪除圖片
    //---------------------------------------------------

        if($comment_type==='draw'){
            if(($lv===3)||($lv===4)){
                if(isset($file_server_enable)&&($file_server_enable)){
                    $ftp_root     ="public_html/mssr/info/user/".(int)$user_id."/book";
                    $ftp_conn     =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                    $ftp_login    =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                    ftp_pasv($ftp_conn,TRUE);

                    $ftp_file_path="{$ftp_root}/".trim($book_sid)."/draw/bimg/1.jpg";
                    $ftp_dir_path ="{$ftp_root}/".trim($book_sid)."/draw/base64_img";

                    @ftp_delete($ftp_conn,$ftp_file_path);
                    if(!(@ftp_rmdir($ftp_conn,$ftp_dir_path) || @ftp_delete($ftp_conn,$ftp_dir_path))){
                        $filelist = @ftp_nlist($ftp_conn, $ftp_dir_path);
                        foreach($filelist as $file){
                            $basename=trim(pathinfo($file)['basename']);
                            $filename=trim(pathinfo($file)['filename']);
                            if($basename==='1'&&$filename==='1'){
                                ftp_delete($ftp_conn,$file);
                            }
                        }
                    }

                    ftp_close($ftp_conn);
                }else{
                    $root           =str_repeat("../",6)."info/user/".(int)$user_id."/book";
                    $draw_path      ="{$root}/".trim($book_sid)."/draw/bimg/1.jpg";
                    $draw_path_enc  =mb_convert_encoding($draw_path,$fso_enc,$page_enc);
                    if(file_exists($draw_path_enc)){
                        @unlink($draw_path);
                    }

                    $dir_path="{$root}/".trim($book_sid)."/draw/base64_img";
                    rm_dir($dir_path,$fso_enc);
                }
            }
        }

    //---------------------------------------------------
    //刪除錄音
    //---------------------------------------------------

        if($comment_type==='record'){
            if(($lv===3)||($lv===4)){
                if(isset($file_server_enable)&&($file_server_enable)){
                    $ftp_root     ="public_html/mssr/info/user/".(int)$user_id."/book";
                    $ftp_conn     =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                    $ftp_login    =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                    ftp_pasv($ftp_conn,TRUE);

                    $record_path_mp3="{$ftp_root}/".trim($book_sid)."/record/1.mp3";
                    $record_path_wav="{$ftp_root}/".trim($book_sid)."/record/1.wav";

                    @ftp_delete($ftp_conn,$record_path_mp3);
                    @ftp_delete($ftp_conn,$record_path_wav);
                }else{
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
        $page=str_repeat("../",1)."{$area}/content.php";
        $arg =array(
            'psize'=>$psize,
            'pinx' =>$pinx,
            'user_id'=>$user_id,
            'book_sid'=>$book_sid,
            'semester_start'=>$semester_start,
            'semester_end'=>$semester_end
        );
        if($date_filter!==''){
            $arg['date_filter']=$date_filter;
        }
        $arg['scrolltop']=$scrolltop;
        $arg =http_build_query($arg);

        if(!empty($arg)){
            $url="{$page}?{$arg}";

            ////建立錨點
            //$url="{$url}#{$anchor}";
        }else{
            $url="{$page}";
        }

        //呼叫頁面
        page_ok($url);
?>


<?php function page_ok($url){?>
<script type="text/javascript" src="../../../../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//重導頁面
//-------------------------------------------------------

    var url='<?php echo $url;?>';
    var par_scrolltop=$(parent.opener.parent).scrollTop();

    window.setTimeout(
        function(){
            parent.opener.location.replace(url);
            callback1();
        },
        50
    );

    function callback1(){
        window.setTimeout(
            function(){
                var $body=(window.opera)?(document.compatMode=="CSS1Compat"?parent.opener.parent.$('html'):parent.opener.parent.$('body')):parent.opener.parent.$('html,body');
                $body.animate({
                    scrollTop:par_scrolltop
                },0);
                callback2();
            },
            300
        );
    }

    function callback2(){
        window.setTimeout(
            function(){
                window.close();
            },
            50
        );
    }

</script>
<?php }?>