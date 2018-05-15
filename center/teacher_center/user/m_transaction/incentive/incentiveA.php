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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_transaction');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //user_id
    //comment_coin
    //note

        $get_chk=array(
            'user_id     ',
            'comment_coin'
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
    //user_id
    //comment_coin
    //note

        //GET
        $user_id     =trim($_GET[trim('user_id     ')]);
        $comment_coin=trim($_GET[trim('comment_coin')]);
        $note        =(isset($_GET['note']))?trim($_GET['note']):'';

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
    //user_id
    //comment_coin
    //note

        $arry_err=array();

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
        //user_id
        //comment_coin
        //note

            $user_id        =(int)$user_id;
            $comment_coin   =(int)$comment_coin;
            $note           =mysql_prep($note);

            //-------------------------------------------
            //查詢物品目前數量
            //-------------------------------------------

                $sql="
                    SELECT
                        `mssr_user_info`.`map_item`,
                        `mssr_user_info`.`box_item`,
                        `mssr_user_info`.`user_coin`
                    FROM `mssr_user_info`
                    WHERE 1=1
                        AND `user_id`={$user_id}
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(empty($arrys_result)){
                    $msg="紀錄不存在, 請重新輸入!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }else{
                    $map_item   =trim($arrys_result[0]['map_item']);
                    $box_item   =trim($arrys_result[0]['box_item']);
                    $user_coin  =trim($arrys_result[0]['user_coin']);
                }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $sess_user_id   =(int)$sess_user_id;
            $edit_by        =(int)$sess_user_id;
            $user_id        =(int)$user_id;
            $comment_coin   =(int)$comment_coin;
            $note           =mysql_prep($note);
            $tx_note        =$note;
            $log_note       =$note;

            $log_id         ="NULL";
            $map_item       =trim($map_item);
            $box_item       =trim($box_item);
            $user_coin      =(int)$user_coin;
            $keyin_cdate    ="NOW()";
            $keyin_mdate    ="NOW()";
            $keyin_ip       =get_ip();
            $log_text       ='老師已經';
            $tx_state       ='正常';

            $tx_type        ='teacher_incentive';

            if(preg_match("/^-/i",$comment_coin)){
                $user_coin=$user_coin-(int)mb_substr($comment_coin,1);
            }else{
                $user_coin=$user_coin+(int)$comment_coin;
            }

            if($comment_coin<0){
                $log_coin =abs($comment_coin);
                $log_text.="扣除了{$log_coin}元 !";
            }else{
                $log_coin =abs($comment_coin);
                $log_text.="給予了{$log_coin}元 !";
            }

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

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

            if($comment_coin>0){
                $tx_sid=tx_sid($user_id,'tx_gift',mb_internal_encoding());

                $sql="
                    # for mssr_tx_gift_log
                    INSERT INTO `mssr_tx_gift_log` SET
                        `edit_by`           = {$edit_by             } ,
                        `msg_id`            = {$msg_id              } ,
                        `tx_from`           = {$sess_user_id        } ,
                        `tx_to`             = {$user_id             } ,
                        `log_id`            = {$log_id              } ,
                        `tx_sid`            ='{$tx_sid              }',
                        `tx_item`           =''                       ,
                        `tx_coin`           = {$comment_coin        } ,
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
                        `tx_item`           =''                       ,
                        `tx_coin`           = {$comment_coin        } ,
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
            ";
            //送出
            $conn_mssr->exec($sql);

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page=str_repeat("../",1).'index.php';
        $arg =array(
            'psize'  =>$psize,
            'pinx'   =>$pinx
        );
        $arg =http_build_query($arg);

        if(!empty($arg)){
            $url="{$page}?{$arg}";
        }else{
            $url="{$page}";
        }

        //header("Location: {$url}");

        $msg="獎懲成功!";
        $jscript_back="
            <script>
                alert('{$msg}');
                location.href='{$url}';
            </script>
        ";
        die($jscript_back);
?>