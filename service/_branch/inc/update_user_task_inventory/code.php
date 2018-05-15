<?php
//-------------------------------------------------------
//更新使用者任務清單
//-------------------------------------------------------

    function update_user_task_inventory($conn_user,$conn_mssr,$user_id,$APP_ROOT,$arry_conn_user,$arry_conn_mssr){
    //---------------------------------------------------
    //函式: update_user_task_inventory()
    //用途: 更新使用者任務清單
    //---------------------------------------------------
    //$conn_user        資料庫連結物件
    //$conn_mssr        資料庫連結物件
    //$user_id          使用者主索引
    //$APP_ROOT         網站根目錄
    //$arry_conn_user   資料庫資訊陣列
    //$arry_conn_mssr   資料庫資訊陣列
    //---------------------------------------------------

        //-----------------------------------------------
        //參數處理
        //-----------------------------------------------

            //檢核參數
            if(!isset($conn_user)){
                return false;
            }

            if(!isset($conn_mssr)){
                return false;
            }

            if(!isset($user_id)||($user_id==='')){
                $err='UPDATE_USER_TASK_INVENTORY:NO USER_ID ';
                die($err);
            }else{
                $user_id=(int)$user_id;
                if($user_id===0){
                    $err='UPDATE_USER_TASK_INVENTORY:USER_ID IS INVALID';
                    die($err);
                }
            }

            if(!isset($APP_ROOT)||trim($APP_ROOT)===''){
                return false;
            }

            if(!isset($arry_conn_user)){
                return false;
            }

            if(!isset($arry_conn_mssr)){
                return false;
            }

            //外掛函式檔
            if(!function_exists("db_result")){
                if(false===@include_once($APP_ROOT.'lib/php/db/code.php')){
                    return false;
                }
            }

            //獲取目前星期幾
            $today_week =(int)date("w");
            $monday_week=(int)1;
            $sunday_week=(int)7;

            if($today_week===0){

                //獲取這禮拜一的日期
                $monday_date=date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')-(6), date('Y')));

                //獲取這禮拜天的日期
                $sunday_date=date("Y-m-d", mktime(0, 0, 0, date('m'), (date('d')), date('Y')));

                //獲取上禮拜一的日期
                $y_monday_date=date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')-(13), date('Y')));

                //獲取上禮拜天的日期
                $y_sunday_date=date("Y-m-d", mktime(0, 0, 0, date('m'), (date('d')-7), date('Y')));
            }else{

                //獲取這禮拜一的日期
                $monday_date=date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')-($today_week-$monday_week), date('Y')));

                //獲取這禮拜天的日期
                $sunday_date=date("Y-m-d", mktime(0, 0, 0, date('m'), (date('d')-($today_week-$monday_week))+6, date('Y')));

                //獲取上禮拜一的日期
                $y_monday_date=date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')-($today_week-$monday_week)-7, date('Y')));

                //獲取上禮拜天的日期
                $y_sunday_date=date("Y-m-d", mktime(0, 0, 0, date('m'), (date('d')-($today_week-$monday_week))-1, date('Y')));
            }

        //-----------------------------------------------
        //SQL處理
        //-----------------------------------------------

            $branch_cno     =0;
            $task_branch_cno=0;
            $arrys_branch   =array();

            //-------------------------------------------
            //1. 抓取目標分店(排除總店)
            //-------------------------------------------

                $sql="
                    SELECT
                        `branch_id`
                    FROM `mssr_user_branch`
                    WHERE 1=1
                        AND `user_id`     ={$user_id}
                        AND `branch_id`   <>1
                        AND `branch_state`='啟用'
                    ORDER BY `branch_cs` ASC
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($arrys_result)){
                    $branch_cno=(int)count($arrys_result);
                    foreach($arrys_result as $inx=>$arry_result){
                        $rs_branch_id=(int)$arry_result['branch_id'];
                        $arrys_branch[$inx]['branch_id']=$rs_branch_id;
                        $arrys_branch[$inx]['task_coin_bonus']=1;
                    }
                }else{
                    return false;
                }

            //-------------------------------------------
            //2. 判定分店任務的紅利倍率
            //-------------------------------------------

                //抓取前 1/4 為紅色任務的分店
                $good_branch_filter=(int)ceil($branch_cno*0.25);
                $arrys_good_branch =array_slice($arrys_branch,0,$good_branch_filter);

                //抓取後 1/8 為綠色任務的分店
                $bad_branch_filter =(int)ceil($branch_cno*0.125);
                $arrys_bad_branch  =array_slice($arrys_branch,($branch_cno-$bad_branch_filter));

                //重置任務名單
                $arrys_branch_task=array();
                $arrys_branch_goal=array();

                //回填紅色任務
                foreach($arrys_good_branch as $inx=>$arry_good_branch){
                    $rs_branch_id=(int)$arry_good_branch['branch_id'];
                    $task_type   =trim('red');
                    $arrys_branch_task[$inx]['branch_id']=$rs_branch_id;
                    $arrys_branch_task[$inx]['task_type']=$task_type;
                    $arrys_branch_goal[]=$rs_branch_id;
                }
                $branch_cno=count($arrys_branch_task);

                ////回填綠色任務
                //foreach($arrys_bad_branch as $arry_bad_branch){
                //    $rs_branch_id=(int)$arry_bad_branch['branch_id'];
                //    $task_type   =trim('green');
                //    $arrys_branch_task[$branch_cno]['branch_id']=$rs_branch_id;
                //    $arrys_branch_task[$branch_cno]['task_type']=$task_type;
                //    $branch_cno++;
                //}

                //回填任務倍率
                foreach($arrys_branch_task as $inx=>$arry_branch){
                    $task_type=trim($arry_branch['task_type']);
                    if($task_type==='red'){
                        $arrys_branch_task[$inx]['task_coin_bonus']=1;
                    }elseif($task_type==='green'){
                        $arrys_branch_task[$inx]['task_coin_bonus']=1;
                    }else{
                        die('UPDATE_USER_TASK_INVENTORY: TASK_COIN_BONUS FAIL');
                    }
                }

            //-------------------------------------------
            //3. 判定已有任務的分店數量
            //-------------------------------------------

                //生成任務的分店數量
                $task_branch_cno=(int)count($arrys_branch_task);
                $create_task=true;

                $sql="
                    SELECT
                        `branch_id`
                    FROM `mssr_user_task_inventory`
                    WHERE 1=1
                        AND `user_id`={$user_id}
                        AND `keyin_cdate` BETWEEN CURDATE() AND '{$sunday_date}'
                ";
                //echo "<Pre>";
                //print_r($sql);
                //echo "</Pre>";
                //die();
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if((count($db_results)>=$task_branch_cno)){
                    $create_task=false;
                }

            //-------------------------------------------
            //抓取所有啟用中的任務
            //-------------------------------------------

                $sql="
                    SELECT
                        `task_sid`,
                        `task_type`
                    FROM `mssr_task_period`
                    WHERE 1=1
                        AND `task_state`='啟用'
                ";
                $arrys_task=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
//echo "<Pre>";
//print_r($arrys_branch);
//echo "</Pre>";
//
//echo "<Pre>";
//print_r($arrys_branch_goal);
//echo "</Pre>";
//die();
            //-------------------------------------------
            //開始更新任務清單
            //-------------------------------------------

                foreach($arrys_task as $inx1=>$arry_task){

                    //參數
                    $rs_task_sid =mysql_prep(trim($arry_task['task_sid']));
                    $rs_task_type=mysql_prep(trim($arry_task['task_type']));

                    foreach($arrys_branch as $inx2=>$arry_branch){

                        $rs_branch_id =(int)$arry_branch['branch_id'];
                        $rs_coin_bonus=(int)$arry_branch['task_coin_bonus'];

                        switch($rs_task_type){

                            case'每日':

                                //更新指標
                                $update_flag=true;
                                $y_day_task_update_flag=false;

                                //-----------------------
                                //檢核今天是否已完成該任務
                                //-----------------------

                                    if($update_flag){
                                        $sql="
                                            SELECT
                                                `user_id`
                                            FROM `mssr_user_task_log`
                                            WHERE 1=1
                                                AND `mssr_user_task_log`.`user_id`   = {$user_id     }
                                                AND `mssr_user_task_log`.`task_sid`  ='{$rs_task_sid }'
                                                AND `mssr_user_task_log`.`branch_id` = {$rs_branch_id}
                                                AND `mssr_user_task_log`.`task_state` IN ('成功','失敗','放棄')
                                                AND (
                                                    DATE(`mssr_user_task_log`.`task_sdate`)
                                                    BETWEEN CURDATE()
                                                    AND     CURDATE()+ INTERVAL 1 DAY
                                                )
                                        ";
                                        $arrys_task_log=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                        if(count($arrys_task_log)!==0){

                                            //若已經完成則不更新
                                            $update_flag=false;
                                        }
                                    }

                                //-----------------------
                                //檢核今天是否正在進行任務
                                //-----------------------

                                    if($update_flag){
                                        $sql="
                                            SELECT
                                                `user_id`
                                            FROM `mssr_user_task_tmp`
                                            WHERE 1=1
                                                AND `mssr_user_task_tmp`.`user_id`   = {$user_id     }
                                                AND `mssr_user_task_tmp`.`task_sid`  ='{$rs_task_sid }'
                                                AND `mssr_user_task_tmp`.`branch_id` = {$rs_branch_id}
                                                AND (
                                                    DATE(`mssr_user_task_tmp`.`task_sdate`)
                                                    BETWEEN CURDATE()
                                                    AND     CURDATE()+ INTERVAL 1 DAY
                                                )
                                        ";
                                        $arrys_task_tmp=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                        if(count($arrys_task_tmp)!==0){

                                            //若正在進行則不更新
                                            $update_flag=false;
                                        }
                                    }

                                //-------------------------
                                //檢核今天是否已經更新過清單
                                //-------------------------

                                    if($update_flag){
                                        $sql="
                                            SELECT
                                                `user_id`
                                            FROM `mssr_user_task_update_log`
                                            WHERE 1=1
                                                AND `mssr_user_task_update_log`.`user_id`   = {$user_id     }
                                                AND `mssr_user_task_update_log`.`task_sid`  ='{$rs_task_sid }'
                                                AND `mssr_user_task_update_log`.`branch_id` = {$rs_branch_id}
                                                AND (
                                                    DATE(`mssr_user_task_update_log`.`keyin_cdate`)
                                                    BETWEEN CURDATE()
                                                    AND     CURDATE()+ INTERVAL 1 DAY
                                                )
                                        ";
                                        $arrys_task_inventory=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                        if(count($arrys_task_inventory)!==0){

                                            //若已經更新過清單則不更新
                                            $update_flag=false;
                                        }
                                    }

                                //-------------------------
                                //檢核任務是否還在進行中
                                //-------------------------

                                    if($update_flag){
                                       $sql="
                                            SELECT
                                                `user_id`
                                            FROM `mssr_user_task_log`
                                            WHERE 1=1
                                                AND `mssr_user_task_log`.`user_id`   = {$user_id     }
                                                AND `mssr_user_task_log`.`task_sid`  ='{$rs_task_sid }'
                                                AND `mssr_user_task_log`.`branch_id` = {$rs_branch_id}
                                                AND `mssr_user_task_log`.`task_state` IN ('進行中')
                                                AND DATE(`mssr_user_task_log`.`task_sdate`)<CURDATE()
                                        ";
                                        $arrys_task_log=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                        if(count($arrys_task_log)!==0){
                                            $y_day_task_update_flag=true;
                                        }
                                    }

                                //-------------------------
                                //開始更新任務清單
                                //-------------------------

                                    if($update_flag){

                                        //-----------------
                                        //1. 移除過期清單
                                        //-----------------

                                            $sql="
                                                # for mssr_user_task_inventory
                                                DELETE FROM `mssr_user_task_inventory`
                                                WHERE 1=1
                                                    AND `user_id`   = {$user_id        }
                                                    AND `task_sid`  ='{$rs_task_sid    }'
                                                    AND `branch_id` = {$rs_branch_id   }
                                                LIMIT 1;
                                            ";
                                            //送出
                                            $err ='UPDATE_USER_TASK_INVENTORY:DB QUERY FAIL(1)';
                                            $conn_mssr->exec($sql);

                                        //-----------------
                                        //2. 移除已過期但進行中的任務
                                        //-----------------

                                            $sql="
                                                # for mssr_user_task_tmp
                                                DELETE FROM `mssr_user_task_tmp`
                                                WHERE 1=1
                                                    AND `user_id`   = {$user_id        }
                                                    AND `task_sid`  ='{$rs_task_sid    }'
                                                    AND `branch_id` = {$rs_branch_id   }
                                                LIMIT 1;
                                            ";
                                            //送出
                                            $err ='UPDATE_USER_TASK_INVENTORY:DB QUERY FAIL(2)';
                                            $conn_mssr->exec($sql);

                                        //-----------------
                                        //3. 更新任務log表為失敗
                                        //-----------------

                                            if($y_day_task_update_flag){
                                                $sql="
                                                    # for mssr_user_task_log
                                                    UPDATE `mssr_user_task_log` SET
                                                        `task_state`            ='失敗',
                                                        `task_edate`            =NOW()
                                                    WHERE 1=1
                                                        AND `user_id`           = {$user_id        }
                                                        AND `task_sid`          ='{$rs_task_sid    }'
                                                        AND `branch_id`         = {$rs_branch_id   }
                                                        AND `task_state`        IN ('進行中')
                                                        AND DATE(`task_sdate`)  <CURDATE();
                                                ";
                                                //送出
                                                $err ='UPDATE_USER_TASK_INVENTORY:DB QUERY FAIL(3)';
                                                $conn_mssr->exec($sql);
                                            }

                                        //-----------------
                                        //4. 加入最新清單
                                        //-----------------

                                            if($create_task){
                                                if(in_array($rs_branch_id,$arrys_branch_goal)){
                                                    $sql="
                                                        # for mssr_user_task_inventory
                                                        INSERT INTO `mssr_user_task_inventory` SET
                                                            `user_id`           =  {$user_id          } ,
                                                            `task_sid`          = '{$rs_task_sid      }',
                                                            `branch_id`         =  {$rs_branch_id     } ,
                                                            `task_coin_bonus`   =  {$rs_coin_bonus    } ,
                                                            `keyin_cdate`       =  NOW()                ,
                                                            `keyin_mdate`       =  NULL                 ;
                                                    ";
                                                    //送出
                                                    $err ='UPDATE_USER_TASK_INVENTORY:DB QUERY FAIL(4)';
                                                    $conn_mssr->exec($sql);
                                                }
                                            }

                                        //-----------------
                                        //5. 回填更新紀錄
                                        //-----------------

                                            $sql="
                                                # for mssr_user_task_update_log
                                                INSERT INTO `mssr_user_task_update_log` SET
                                                    `user_id`           =  {$user_id          } ,
                                                    `task_sid`          = '{$rs_task_sid      }',
                                                    `branch_id`         =  {$rs_branch_id     } ,
                                                    `log_id`            =  NULL                 ,
                                                    `keyin_cdate`       =  NOW()                ;
                                            ";
                                            //送出
                                            $err ='UPDATE_USER_TASK_INVENTORY:DB QUERY FAIL(5)';
                                            $conn_mssr->exec($sql);
                                    }
                            break;

                            case'每週':

                                //更新指標
                                $update_flag=true;

                                $y_week_task_update_flag=false;

                                //-------------------
                                //檢核這週是否已完成該任務
                                //-------------------

                                    if($update_flag){
                                        $sql="
                                            SELECT
                                                `user_id`
                                            FROM `mssr_user_task_log`
                                            WHERE 1=1
                                                AND `mssr_user_task_log`.`user_id`   = {$user_id     }
                                                AND `mssr_user_task_log`.`task_sid`  ='{$rs_task_sid }'
                                                AND `mssr_user_task_log`.`branch_id` = {$rs_branch_id}
                                                AND `mssr_user_task_log`.`task_state` IN ('成功','失敗','放棄')
                                                AND (
                                                    DATE(`mssr_user_task_log`.`task_sdate`)
                                                    BETWEEN '{$monday_date}'
                                                    AND     '{$sunday_date}'
                                                )
                                        ";
                                        $arrys_task_log=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                        if(count($arrys_task_log)!==0){

                                            //若已經完成則不更新
                                            $update_flag=false;
                                        }
                                    }

                                //-------------------
                                //檢核這週是否正在進行任務
                                //-------------------

                                    if($update_flag){
                                        $sql="
                                            SELECT
                                                `user_id`
                                            FROM `mssr_user_task_tmp`
                                            WHERE 1=1
                                                AND `mssr_user_task_tmp`.`user_id`   = {$user_id     }
                                                AND `mssr_user_task_tmp`.`task_sid`  ='{$rs_task_sid }'
                                                AND `mssr_user_task_tmp`.`branch_id` = {$rs_branch_id}
                                                AND (
                                                    DATE(`mssr_user_task_tmp`.`task_sdate`)
                                                    BETWEEN '{$monday_date}'
                                                    AND     '{$sunday_date}'
                                                )
                                        ";
                                        $arrys_task_tmp=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                        if(count($arrys_task_tmp)!==0){

                                            //若正在進行則不更新
                                            $update_flag=false;
                                        }
                                    }

                                //-------------------
                                //檢核這週是否已經更新過清單
                                //-------------------

                                    if($update_flag){
                                        $sql="
                                            SELECT
                                                `user_id`
                                            FROM `mssr_user_task_update_log`
                                            WHERE 1=1
                                                AND `mssr_user_task_update_log`.`user_id`   = {$user_id     }
                                                AND `mssr_user_task_update_log`.`task_sid`  ='{$rs_task_sid }'
                                                AND `mssr_user_task_update_log`.`branch_id` = {$rs_branch_id}
                                                AND (
                                                    DATE(`mssr_user_task_update_log`.`keyin_cdate`)
                                                    BETWEEN '{$monday_date}'
                                                    AND     '{$sunday_date}'
                                                )
                                        ";
                                        $arrys_task_inventory=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                        if(count($arrys_task_inventory)!==0){

                                            //若已經更新過清單則不更新
                                            $update_flag=false;
                                        }
                                    }

                                //-------------------
                                //檢核任務是否還在進行中
                                //-------------------

                                    if($update_flag){
                                       $sql="
                                            SELECT
                                                `user_id`
                                            FROM `mssr_user_task_log`
                                            WHERE 1=1
                                                AND `mssr_user_task_log`.`user_id`          = {$user_id         }
                                                AND `mssr_user_task_log`.`task_sid`         ='{$rs_task_sid     }'
                                                AND `mssr_user_task_log`.`branch_id`        = {$rs_branch_id    }
                                                AND `mssr_user_task_log`.`task_state`       IN ('進行中')
                                                AND DATE(`mssr_user_task_log`.`task_sdate`) <'{$y_sunday_date   }'
                                        ";
                                        $arrys_task_log=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                        if(count($arrys_task_log)!==0){
                                            $y_week_task_update_flag=true;
                                        }
                                    }

                                //-------------------
                                //開始更新任務清單
                                //-------------------

                                    if($update_flag){

                                        //-----------
                                        //1. 移除過期清單
                                        //-----------

                                            $sql="
                                                # for mssr_user_task_inventory
                                                DELETE FROM `mssr_user_task_inventory`
                                                WHERE 1=1
                                                    AND `user_id`   = {$user_id        }
                                                    AND `task_sid`  ='{$rs_task_sid    }'
                                                    AND `branch_id` = {$rs_branch_id   }
                                                LIMIT 1;
                                            ";
                                            //送出
                                            $err ='UPDATE_USER_TASK_INVENTORY:DB QUERY FAIL(1)';
                                            $conn_mssr->exec($sql);

                                        //-----------
                                        //2. 移除已過期但進行中的任務
                                        //-----------

                                            $sql="
                                                # for mssr_user_task_tmp
                                                DELETE FROM `mssr_user_task_tmp`
                                                WHERE 1=1
                                                    AND `user_id`   = {$user_id        }
                                                    AND `task_sid`  ='{$rs_task_sid    }'
                                                    AND `branch_id` = {$rs_branch_id   }
                                                LIMIT 1;
                                            ";
                                            //送出
                                            $err ='UPDATE_USER_TASK_INVENTORY:DB QUERY FAIL(2)';
                                            $conn_mssr->exec($sql);

                                        //-----------
                                        //3. 更新任務log表為失敗
                                        //-----------

                                            if($y_week_task_update_flag){
                                                $sql="
                                                    # for mssr_user_task_log
                                                    UPDATE `mssr_user_task_log` SET
                                                        `task_state`            ='失敗',
                                                        `task_edate`            =NOW()
                                                    WHERE 1=1
                                                        AND `user_id`           = {$user_id         }
                                                        AND `task_sid`          ='{$rs_task_sid     }'
                                                        AND `branch_id`         = {$rs_branch_id    }
                                                        AND `task_state`        IN ('進行中')
                                                        AND DATE(`task_sdate`)  <'{$y_sunday_date   }';
                                                ";
                                                //送出
                                                $err ='UPDATE_USER_TASK_INVENTORY:DB QUERY FAIL(3)';
                                                $conn_mssr->exec($sql);
                                            }

                                        //-----------
                                        //4. 加入最新清單
                                        //-----------

                                            if($create_task){
                                                if(in_array($rs_branch_id,$arrys_branch_goal)){
                                                    $sql="
                                                        # for mssr_user_task_inventory
                                                        INSERT INTO `mssr_user_task_inventory` SET
                                                            `user_id`           =  {$user_id          } ,
                                                            `task_sid`          = '{$rs_task_sid      }',
                                                            `branch_id`         =  {$rs_branch_id     } ,
                                                            `task_coin_bonus`   =  {$rs_coin_bonus    } ,
                                                            `keyin_cdate`       = '{$monday_date      }',
                                                            `keyin_mdate`       =  NULL                 ;
                                                    ";
                                                    //送出
                                                    $err ='UPDATE_USER_TASK_INVENTORY:DB QUERY FAIL(4)';
                                                    $conn_mssr->exec($sql);
                                                }
                                            }

                                        //---------------
                                        //5. 回填更新紀錄
                                        //---------------

                                            $sql="
                                                # for mssr_user_task_update_log
                                                INSERT INTO `mssr_user_task_update_log` SET
                                                    `user_id`           =  {$user_id          } ,
                                                    `task_sid`          = '{$rs_task_sid      }',
                                                    `branch_id`         =  {$rs_branch_id     } ,
                                                    `log_id`            =  NULL                 ,
                                                    `keyin_cdate`       = '{$monday_date      }';
                                            ";
                                            //送出
                                            $err ='UPDATE_USER_TASK_INVENTORY:DB QUERY FAIL(5)';
                                            $conn_mssr->exec($sql);
                                    }
                            break;

                            case'每月':

                            break;

                            default:

                            break;
                        }
                    }
                }

        return true;
    }
?>