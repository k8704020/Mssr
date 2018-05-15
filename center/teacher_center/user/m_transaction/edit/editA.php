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
    //tx_sid    交易編號

        $get_chk=array(
            'tx_sid '
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
    //tx_sid    交易編號

        //GET
        $tx_sid=trim($_GET[trim('tx_sid ')]);

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
    //tx_sid    交易編號

        $arry_err=array();

        if($tx_sid===''){
           $arry_err[]='交易編號,未輸入!';
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
        //tx_sid    交易編號

            $tx_sid=mysql_prep($tx_sid);

            //-------------------------------------------
            //檢核交易編號
            //-------------------------------------------

                $sql="
                    SELECT `user_id`,`tx_sid`, `tx_item`, `tx_coin`
                    FROM `mssr_tx_sys_log`
                    WHERE 1=1
                        AND `tx_sid`='{$tx_sid}'
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(empty($arrys_result)){
                    $msg="交易紀錄不存在, 請重新輸入!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }else{
                    $user_id=(int)$arrys_result[0]['user_id'];
                    $tx_item=(int)$arrys_result[0]['tx_item'];
                    $tx_coin=(int)$arrys_result[0]['tx_coin'];
                }

            //-------------------------------------------
            //檢核交易類型
            //-------------------------------------------

                $sql="
                    SELECT
                        `mssr_user_item_log`.`tx_type`,

                        `mssr_user_info`.`map_item`,
                        `mssr_user_info`.`box_item`,
                        `mssr_user_info`.`user_coin`
                    FROM `mssr_user_item_log`
                        INNER JOIN `mssr_user_info` ON
                        `mssr_user_item_log`.`user_id`=`mssr_user_info`.`user_id`
                    WHERE 1=1
                        AND `tx_sid`='{$tx_sid}'
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(empty($arrys_result)){
                    $msg="交易紀錄不存在, 請重新輸入!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }else{
                    $tx_type    =trim($arrys_result[0]['tx_type']);
                    $map_item   =trim($arrys_result[0]['map_item']);
                    $box_item   =trim($arrys_result[0]['box_item']);
                    $user_coin  =trim($arrys_result[0]['user_coin']);
                }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $sess_user_id   =(int)$sess_user_id;
            $user_id        =(int)($user_id );
            $tx_sid         =mysql_prep($tx_sid);
            $tx_item        =abs((int)$tx_item);
            $tx_coin        =abs((int)$tx_coin);
            $tx_type        =mysql_prep($tx_type);
            $map_item       =trim($map_item);
            $box_item       =trim($box_item);
            $user_coin      =(int)$user_coin;
            $keyin_ip       =get_ip();

            $tmp_arrys_map_item =array();
            $arrys_map_item     =array();
            if($map_item!==''){
                $tmp_arrys_map_item=explode(",",$map_item);
                foreach($tmp_arrys_map_item as $inx=>$tmp_arry_map_item){
                    $inx=(int)$inx;
                    if($tmp_arry_map_item==='')unset($tmp_arrys_map_item[$inx]);
                }
            }

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

           $sql="
                # for mssr_tx_sys_log
                UPDATE `mssr_tx_sys_log` SET
                    `edit_by`    ={$sess_user_id}               ,
                    `tx_state`   ='資料異常'                    ,
                    `tx_note`    ='審核資料異常, 予以取消。'
                WHERE 1=1
                    AND `user_id`= {$user_id}
                    AND `tx_sid` ='{$tx_sid }'
                LIMIT 1;
            ";
            switch($tx_type){
                case 'buy':
                //補回錢，回收物品

                    $user_coin  =$user_coin+$tx_coin;
                    $cancel_flag=false;

                    if(!$cancel_flag){
                        if(!empty($tmp_arrys_map_item)){
                            $map_item=implode(",",$tmp_arrys_map_item).",";
                            foreach($tmp_arrys_map_item as $inx=>$tmp_arry_map_item){
                                $inx=(int)$inx;
                                if($inx%3===0){
                                    if((int)$tmp_arry_map_item===(int)$tx_item){
                                        unset($tmp_arrys_map_item[$inx]);
                                        unset($tmp_arrys_map_item[$inx+1]);
                                        unset($tmp_arrys_map_item[$inx+2]);
                                        $cancel_flag=true;
                                        break;
                                    }
                                }
                            }
                            $map_item=implode(",",$tmp_arrys_map_item).",";
                        }
                    }

                    $box_item=trim($box_item);
                    $tmp_arry_box_item=array();
                    if(!$cancel_flag){
                        if($box_item!==''){
                            $tmp_arry_box_item=explode(',',$box_item);
                            if(!empty($tmp_arry_box_item)){
                                foreach($tmp_arry_box_item as $inx=>$item){
                                    $inx =(int)$inx;
                                    $item=(int)$item;
                                    if($inx%2===0){
                                        if($item===$tx_item){
                                            if((int)$tmp_arry_box_item[$inx+1]>1){
                                                $tmp_arry_box_item[$inx+1]=(int)$tmp_arry_box_item[$inx+1]-1;
                                            }else{
                                                unset($tmp_arry_box_item[$inx]);
                                                unset($tmp_arry_box_item[$inx+1]);
                                            }
                                            $cancel_flag=true;
                                            break;
                                        }
                                    }
                                }
                                $box_item=implode(",",$tmp_arry_box_item);
                            }
                        }
                    }

                    $sql.="
                        # for mssr_user_item_log
                        UPDATE `mssr_user_item_log` SET
                            `edit_by`       = {$sess_user_id            } ,
                            `tx_type`       ='cancel'                     ,
                            `map_item`      ='{$map_item                }',
                            `box_item`      ='{$box_item                }',
                            `user_coin`     = {$user_coin               } ,
                            `log_state`     ='資料異常'                   ,
                            `log_note`      ='審核資料異常, 予以取消。'   ,
                            `keyin_mdate`   = NULL                        ,
                            `keyin_ip`      ='{$keyin_ip                }'
                        WHERE 1=1
                            AND `user_id`   = {$user_id}
                            AND `tx_sid`    ='{$tx_sid }'
                        LIMIT 1;

                        # for mssr_user_info
                        UPDATE `mssr_user_info` SET
                            `map_item`  ='{$map_item    }',
                            `box_item`  ='{$box_item    }',
                            `user_coin` = {$user_coin   }
                        WHERE 1=1
                            AND `user_id`={$user_id     }
                        LIMIT 1;
                    ";
                break;

                case 'sell':
                //回收錢，補回物品

                    $user_coin=$user_coin-$tx_coin;
                    $return_flag=false;
                    if($box_item!==''){
                        $tmp_arry_box_item=explode(',',$box_item);
                        if(!empty($tmp_arry_box_item)){
                            foreach($tmp_arry_box_item as $inx=>$item){
                                $inx =(int)$inx;
                                $item=(int)$item;
                                if($inx%2===0){
                                    if($item===$tx_item){
                                        $tmp_arry_box_item[$inx+1]=(int)$tmp_arry_box_item[$inx+1]+1;
                                        $return_flag=true;
                                        break;
                                    }
                                }
                            }
                            $box_item=implode(",",$tmp_arry_box_item);
                        }
                    }
                    if(!$return_flag){
                        $box_item =$box_item.$tx_item.',1,';
                    }

                    $sql.="
                        # for mssr_user_item_log
                        UPDATE `mssr_user_item_log` SET
                            `edit_by`       = {$sess_user_id            } ,
                            `tx_type`       ='cancel'                     ,
                            `map_item`      ='{$map_item                }',
                            `box_item`      ='{$box_item                }',
                            `user_coin`     = {$user_coin               } ,
                            `log_state`     ='資料異常'                   ,
                            `log_note`      ='審核資料異常, 予以取消。'   ,
                            `keyin_mdate`   = NULL                        ,
                            `keyin_ip`      ='{$keyin_ip                }'
                        WHERE 1=1
                            AND `user_id`   = {$user_id}
                            AND `tx_sid`    ='{$tx_sid }'
                        LIMIT 1;

                        # for mssr_user_info
                        UPDATE `mssr_user_info` SET
                            `map_item`  ='{$map_item    }',
                            `box_item`  ='{$box_item    }',
                            `user_coin` = {$user_coin   }
                        WHERE 1=1
                            AND `user_id`={$user_id     }
                        LIMIT 1;
                    ";
                break;

                default:
                    die('噢噢! 系統發生錯誤了‧‧‧‧‧ 我們已回報相關人員，敬請見諒。');
                break;
            }

            //送出
            $conn_mssr->exec($sql);

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page=str_repeat("../",1).'view/viewF.php';
        $arg =array(
            'psize'  =>$psize,
            'pinx'   =>$pinx,
            'user_id'=>$user_id,
            'mode'   =>'tx'
        );
        $arg =http_build_query($arg);

        if(!empty($arg)){
            $url="{$page}?{$arg}";
        }else{
            $url="{$page}";
        }

        header("Location: {$url}");
?>