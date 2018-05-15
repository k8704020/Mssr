<?php
//-------------------------------------------------------
//明日星球,分店
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",2).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'service/branch/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //user_id           使用者主索引
    //branch_id         分店主索引

        $get_chk=array(
            'user_id    ',
            'branch_id  '
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
    //user_id           使用者主索引
    //branch_id         分店主索引

        //GET
        $user_id       =trim($_GET[trim('user_id       ')]);
        $branch_id     =trim($_GET[trim('branch_id     ')]);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //user_id           使用者主索引
    //branch_id         分店主索引

        $arry_err=array();

        if($user_id===''){
           $arry_err[]='使用者主索引,未輸入!';
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $arry_err[]='使用者主索引,錯誤!';
            }
        }

        if($branch_id===''){
           $arry_err[]='分店主索引,未輸入!';
        }else{
            $branch_id=(int)$branch_id;
            if($branch_id===0){
                $arry_err[]='分店主索引,錯誤!';
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
        //SQL處理
        //-----------------------------------------------

            $user_id  =(int)$user_id;
            $branch_id=(int)$branch_id;

            //交易指標
            $tx_flag  =false;
            if(!in_array($branch_id,array(1,2,3,4,5,6))){
                $tx_flag=true;
            }


            //基本參數
            $log_id   ="NULL";
            $tx_sid   =tx_sid($user_id,trim('tx_sys'),mb_internal_encoding());
            $tx_item  ="";

            if(in_array($branch_id,array(1,2,3,4,5,6))){
                $tx_coin    =0;
                $branch_rank=3;
            }else{
                $tx_coin=-100;
                $branch_rank=1;
            }
            $tx_state   ="正常";
            $tx_note    ="";
            $keyin_cdate="NOW()";
            $keyin_mdate="NULL";
            $keyin_ip   =get_ip();

            $tx_type    ="branch_open";
            $log_state  ="正常";
            $log_note   ="";

            //-------------------------------------------
            //mssr_user_branch 處理
            //-------------------------------------------

                $sql="
                    UPDATE `mssr_user_branch` SET
                        `branch_rank`  = {$branch_rank  },
                        `branch_state` = '啟用'
                    WHERE 1=1
                        AND `user_id`  ={$user_id       }
                        AND `branch_id`={$branch_id     }
                    LIMIT 1;
                ";
                $conn_mssr->exec($sql);

                //有交易情形
                if($tx_flag){
                //---------------------------------------
                //mssr_tx_sys_log
                //---------------------------------------

                    $sql="
                        # for mssr_tx_sys_log
                        INSERT INTO `mssr_tx_sys_log` SET
                            `edit_by`       =   {$user_id       } ,
                            `user_id`       =   {$user_id       } ,
                            `log_id`        =   {$log_id        } ,
                            `tx_sid`        =  '{$tx_sid        }',
                            `tx_item`       =  '{$tx_item       }',
                            `tx_coin`       =   {$tx_coin       } ,
                            `tx_state`      =  '{$tx_state      }',
                            `tx_note`       =  '{$tx_note       }',
                            `keyin_cdate`   =   {$keyin_cdate   } ,
                            `keyin_mdate`   =   {$keyin_mdate   } ,
                            `keyin_ip`      =  '{$keyin_ip      }';
                    ";
                    $conn_mssr->exec($sql);

                //---------------------------------------
                //mssr_user_info
                //---------------------------------------

                    $sql="
                        UPDATE `mssr_user_info` SET
                            `user_coin`     = `user_coin`{$tx_coin}
                        WHERE 1=1
                            AND `user_id`   ={$user_id  }
                        LIMIT 1;
                    ";
                    $conn_mssr->exec($sql);

                //---------------------------------------
                //mssr_user_item_log
                //---------------------------------------

                    $query_sql="
                        SELECT
                            *
                        FROM `mssr_user_info`
                        WHERE 1=1
                            AND `user_id`   ={$user_id  }
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(0,1),$arry_conn_mssr);
                    if(!empty($arrys_result)){

                        $rs_tx_type     ="branch_open";
                        $rs_map_item    =mysql_prep(trim($arrys_result[0]['map_item']));
                        $rs_box_item    =mysql_prep(trim($arrys_result[0]['box_item']));
                        $rs_user_coin   =(int)$arrys_result[0]['user_coin'];
                        $rs_log_state   ='正常';
                        $rs_log_note    ='';
                        $rs_keyin_cdate ="NOW()";
                        $rs_keyin_mdate ="NULL";
                        $rs_keyin_ip    =get_ip();

                        $sql="
                            # for mssr_user_item_log
                            INSERT INTO `mssr_user_item_log` SET
                                `edit_by`       =   {$user_id           } ,
                                `user_id`       =   {$user_id           } ,
                                `tx_sid`        =  '{$tx_sid            }',
                                `log_id`        =   {$log_id            } ,
                                `tx_type`       =  '{$rs_tx_type        }',
                                `map_item`      =  '{$rs_map_item       }',
                                `box_item`      =  '{$rs_box_item       }',
                                `user_coin`     =   {$rs_user_coin      } ,
                                `log_state`     =  '{$rs_log_state      }',
                                `log_note`      =  '{$rs_log_note       }',
                                `keyin_cdate`   =   {$rs_keyin_cdate    } ,
                                `keyin_mdate`   =   {$rs_keyin_mdate    } ,
                                `keyin_ip`      =  '{$rs_keyin_ip       }';
                        ";
                        $conn_mssr->exec($sql);
                    }
                }

    //---------------------------------------------------
    //回傳
    //---------------------------------------------------

        $msg="分店開啟成功!";
        $jscript_back="
            <script>
                alert('{$msg}');
                location.href='index_php.php';
            </script>
        ";
        die($jscript_back);
?>