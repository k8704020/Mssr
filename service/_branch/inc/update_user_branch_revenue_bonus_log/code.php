<?php
//-------------------------------------------------------
//更新使用者分店營收紅利報表
//-------------------------------------------------------

    //---------------------------------------------------
    //函式
    //---------------------------------------------------

        function update_user_branch_revenue_bonus_log($conn,$user_id,$school_code,$arry_conn){
        //-----------------------------------------------
        //函式: update_user_branch_revenue_bonus_log()
        //用途: 更新使用者分店營收紅利報表
        //-----------------------------------------------
        //$conn         資料庫連結物件
        //$user_id      使用者主索引
        //$school_code  學校代號
        //$arry_conn    資料庫資訊陣列
        //-----------------------------------------------

            //-------------------------------------------
            //參數處理
            //-------------------------------------------

                //檢核參數
                if(!isset($user_id)||($user_id==='')){
                    $err='UPDATE_USER_BRANCH_REVENUE_BONUS_LOG:NO USER_ID ';
                    die($err);
                }else{
                    $user_id=(int)$user_id;
                    if($user_id===0){
                        $err='UPDATE_USER_BRANCH_REVENUE_BONUS_LOG:USER_ID IS INVALID';
                        die($err);
                    }
                }

                if(!isset($school_code)||($school_code==='')){
                    $err='UPDATE_USER_BRANCH_REVENUE_BONUS_LOG:NO SCHOOL_CODE ';
                    die($err);
                }else{
                    $school_code=addslashes(trim($school_code));
                }

                if((!$arry_conn)||(empty($arry_conn))){
                    $err='UPDATE_USER_BRANCH_REVENUE_BONUS_LOG:NO ARRY_CONN';
                    die($err);
                }

                if(!function_exists('tx_sid')){
                    require_once(str_repeat("../",4).'inc/tx_sid/code.php');
                }

                //資料庫資訊
                $db_host  =$arry_conn['db_host'];
                $db_user  =$arry_conn['db_user'];
                $db_pass  =$arry_conn['db_pass'];
                $db_name  =$arry_conn['db_name'];
                $db_encode=$arry_conn['db_encode'];


                //連結物件判斷
                $has_conn=false;

                if(!$conn){
                    $has_conn=true;

                    $conn_info='mysql'.":host={$db_host}".";dbname={$db_name}";
                    $options = array(
                        PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,           //設置錯誤提示，只獲取代碼
                        PDO::ATTR_CASE,              PDO::CASE_NATURAL,             //列名按照原始的方式
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode}"    //設置語系
                    );

                    try{
                        $conn=@new PDO($conn_info, $db_user, $db_pass,$options);
                    }catch(PDOException $e){
                        $err ='UPDATE_USER_BRANCH_REVENUE_BONUS_LOG:CONNECT FAIL';
                        die($err);
                    }
                }else{
                    $has_conn=false;
                }

            //-------------------------------------------
            //SQL處理
            //-------------------------------------------

                //獲取目前星期幾
                $today_week =(int)date("w");
                $monday_week=(int)1;
                $sunday_week=(int)7;

                if($today_week===0){
                    //獲取這禮拜一的日期
                    $y_monday_date=date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')-(6), date('Y')));

                    //獲取這禮拜天的日期
                    $y_sunday_date=date("Y-m-d", mktime(0, 0, 0, date('m'), (date('d')), date('Y')));

                    //獲取上禮拜一的日期
                    $y_monday_date=date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')-(13), date('Y')));

                    //獲取上禮拜天的日期
                    $y_sunday_date=date("Y-m-d", mktime(0, 0, 0, date('m'), (date('d')-7), date('Y')));
                }else{
                    //獲取這禮拜一的日期
                    $y_monday_date=date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')-($today_week-$monday_week), date('Y')));

                    //獲取這禮拜天的日期
                    $y_sunday_date=date("Y-m-d", mktime(0, 0, 0, date('m'), (date('d')-($today_week-$monday_week))+6, date('Y')));

                    //獲取上禮拜一的日期
                    $y_monday_date=date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')-($today_week-$monday_week)-7, date('Y')));

                    //獲取上禮拜天的日期
                    $y_sunday_date=date("Y-m-d", mktime(0, 0, 0, date('m'), (date('d')-($today_week-$monday_week))-1, date('Y')));
                }

                //---------------------------------------
                //抓取所有已啟用的分店(不包含總店)
                //---------------------------------------

                    $query_sql="
                        SELECT
                            `mssr_book_category`.`cat_code`,

                            `mssr_user_branch`.`branch_id`,
                            `mssr_user_branch`.`branch_rank`,
                            `mssr_user_branch`.`branch_cs`
                        FROM `mssr_user_branch`
                            INNER JOIN `mssr_branch` ON
                            `mssr_user_branch`.`branch_id`=`mssr_branch`.`branch_id`
                            INNER JOIN `mssr_book_category` ON
                            `mssr_branch`.`branch_name`=`mssr_book_category`.`cat_name`
                        WHERE 1=1
                            AND `mssr_user_branch`.`user_id`         ={$user_id}
                            AND `mssr_user_branch`.`branch_id`      <>1
                            AND `mssr_user_branch`.`branch_state`    ='啟用'

                            AND `mssr_branch`.`branch_state`         ='啟用'

                            AND `mssr_book_category`.`cat_state`     ='啟用'
                            AND `mssr_book_category`.`school_code`   ='{$school_code}'
                        GROUP BY `mssr_branch`.`branch_name`
                    ";
                    $err='UPDATE_USER_BRANCH_REVENUE_BONUS_LOG:QUERY FAIL';
                    $result=$conn->prepare($query_sql);
                    $result->execute() or
                    die($err);

                    //建立資料集陣列
                    $arrys_result=array();

                    if(($result->rowCount())!==0){
                    //有資料存在
                        while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                            $arrys_result[]=$arry_row;
                        }
                    }else{
                        return false;
                    }

                //---------------------------------------
                //處理
                //---------------------------------------

                    foreach($arrys_result as $arry_result){

                        //參數
                        $rs_cat_code            =trim($arry_result['cat_code']);
                        $rs_branch_id           =(int)$arry_result['branch_id'];
                        $rs_branch_rank         =(int)$arry_result['branch_rank'];
                        $rs_branch_cs           =(int)$arry_result['branch_cs'];
                        $rs_book_booking_from   =(int)0;
                        $rs_book_booking_to     =(int)0;

//echo "<Pre>";
//print_r($rs_branch_id);
//echo "</Pre>";
//
//echo "<Pre>";
//print_r($rs_branch_cs);
//echo "</Pre>";
                        //撈取該週銷售成功本數
                        $query_sql="
                            SELECT
                                `mssr_book_booking_log`.`booking_to`
                            FROM `mssr_book_booking_log`
                                INNER JOIN `mssr_book_category_rev` ON
                                `mssr_book_booking_log`.`book_sid`=`mssr_book_category_rev`.`book_sid`
                                INNER JOIN `mssr_book_category` ON
                                `mssr_book_category_rev`.`cat_code`=`mssr_book_category`.`cat_code`
                            WHERE 1=1
                                AND `mssr_book_booking_log`.`booking_to`    = {$user_id     }
                                AND `mssr_book_booking_log`.`booking_state` ='完成交易'
                                AND (
                                    `mssr_book_booking_log`.`booking_edate` BETWEEN '{$y_monday_date}' AND '{$y_sunday_date}'
                                )

                                AND `mssr_book_category_rev`.`school_code`  ='{$school_code }'
                                AND `mssr_book_category_rev`.`cat_code`     ='{$rs_cat_code }'

                                AND `mssr_book_category`.`cat_state`        ='啟用'

                            GROUP BY `mssr_book_booking_log`.`book_sid`
                        ";
                        $err='UPDATE_USER_BRANCH_REVENUE_BONUS_LOG:QUERY FAIL';
                        $result=$conn->query($query_sql) or die($err);
                        $rs_book_booking_from=(int)$result->rowCount();

//echo "<Pre>";
//print_r($query_sql);
//echo "</Pre>";

                        //撈取該週訂閱成功本數
                        $query_sql="
                            SELECT
                                `mssr_book_booking_log`.`booking_to`
                            FROM `mssr_book_booking_log`
                                INNER JOIN `mssr_book_category_rev` ON
                                `mssr_book_booking_log`.`book_sid`=`mssr_book_category_rev`.`book_sid`
                                INNER JOIN `mssr_book_category` ON
                                `mssr_book_category_rev`.`cat_code`=`mssr_book_category`.`cat_code`
                            WHERE 1=1
                                AND `mssr_book_booking_log`.`booking_from`  = {$user_id     }
                                AND `mssr_book_booking_log`.`booking_state` ='完成交易'
                                AND (
                                    `mssr_book_booking_log`.`booking_edate` BETWEEN '{$y_monday_date}' AND '{$y_sunday_date}'
                                )

                                AND `mssr_book_category_rev`.`school_code`  ='{$school_code }'
                                AND `mssr_book_category_rev`.`cat_code`     ='{$rs_cat_code }'

                                AND `mssr_book_category`.`cat_state`        ='啟用'

                            GROUP BY `mssr_book_booking_log`.`book_sid`
                        ";
                        $err='UPDATE_USER_BRANCH_REVENUE_BONUS_LOG:QUERY FAIL';
                        $result=$conn->query($query_sql) or die($err);
                        $rs_book_booking_to=(int)$result->rowCount();
//echo "<Pre>";
//print_r($rs_book_booking_from);
//echo "</Pre>";
//
//echo "<Pre>";
//print_r($rs_book_booking_to);
//echo "</Pre>";
                        //總營收
                        $revenue=(int)((int)$rs_branch_cs*0.1)+((int)$rs_book_booking_from*200)+((int)$rs_book_booking_to*100);
//echo "<Pre>";
//print_r($revenue);
//echo "</Pre>";

                        //檢核這週是否已有營收紀錄
                        $query_sql="
                            SELECT
                                `user_id`
                            FROM `mssr_user_branch_revenue_log`
                            WHERE 1=1
                                AND `branch_id`     = {$rs_branch_id }
                                AND `user_id`       = {$user_id      }
                                AND (
                                    `revenue_sdate` BETWEEN '{$y_monday_date}' AND '{$y_sunday_date}'
                                        OR
                                    `revenue_edate` BETWEEN '{$y_monday_date}' AND '{$y_sunday_date}'
                                        OR
                                    `keyin_cdate` BETWEEN '{$y_monday_date}' AND '{$y_sunday_date}'
                                )
                            LIMIT 1;
                        ";
                        $err='UPDATE_USER_BRANCH_REVENUE_BONUS_LOG:QUERY FAIL';
                        $result=$conn->query($query_sql) or die($err);
                        $rowcount=$result->rowCount();
                        if($rowcount===0){
                            $sql="
                                # for mssr_user_branch_revenue_log
                                INSERT INTO `mssr_user_branch_revenue_log` SET
                                    `user_id`       = {$user_id             } ,
                                    `branch_id`     = {$rs_branch_id        } ,
                                    `log_id`        = NULL                    ,
                                    `revenue_sdate` ='{$y_monday_date       }',
                                    `revenue_edate` ='{$y_sunday_date       }',
                                    `revenue_coin`  = {$revenue             } ,
                                    `keyin_cdate`   = NOW()                   ;
                            ";
//echo "<Pre>";
//print_r($sql);
//echo "</Pre>";
                            $conn->exec($sql);

                            //交易紀錄
                            if($revenue!==0){

                                //基本參數
                                $log_id     ="NULL";
                                $tx_sid     =tx_sid($user_id,trim('tx_sys'),mb_internal_encoding());
                                $tx_item    ="";

                                $tx_coin    =$revenue;

                                $tx_state   ="正常";
                                $tx_note    ="";
                                $keyin_cdate="NOW()";
                                $keyin_mdate="NULL";

                                $ip="";
                                if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                                    $ip=$_SERVER["HTTP_X_FORWARDED_FOR"];
                                }else{
                                    $ip=$_SERVER["REMOTE_ADDR"];
                                }
                                $keyin_ip   =$ip;

                                $tx_type    ="branch_revenue";
                                $log_state  ="正常";
                                $log_note   ="";

                            //---------------------------
                            //mssr_tx_sys_log
                            //---------------------------

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
                                $conn->exec($sql);
//echo "<Pre>";
//print_r($sql);
//echo "</Pre>";
                            //---------------------------
                            //mssr_user_info
                            //---------------------------

                                $sql="
                                    UPDATE `mssr_user_info` SET
                                        `user_coin`     = `user_coin`+{$tx_coin}
                                    WHERE 1=1
                                        AND `user_id`   ={$user_id  }
                                    LIMIT 1;
                                ";
                                $conn->exec($sql);
//echo "<Pre>";
//print_r($sql);
//echo "</Pre>";
                            //---------------------------
                            //mssr_user_item_log
                            //---------------------------

                                $query_sql="
                                    SELECT
                                        *
                                    FROM `mssr_user_info`
                                    WHERE 1=1
                                        AND `user_id`   ={$user_id  }
                                ";
                                $arrys_result=db_result($conn_type='pdo',$conn,$query_sql,array(0,1),$arry_conn);
                                if(!empty($arrys_result)){

                                    $rs_tx_type     ="branch_bonus";
                                    $rs_map_item    =mysql_prep(trim($arrys_result[0]['map_item']));
                                    $rs_box_item    =mysql_prep(trim($arrys_result[0]['box_item']));
                                    $rs_user_coin   =(int)$arrys_result[0]['user_coin'];
                                    $rs_log_state   ='正常';
                                    $rs_log_note    ='';
                                    $rs_keyin_cdate ="NOW()";
                                    $rs_keyin_mdate ="NULL";
                                    $rs_keyin_ip    =$keyin_ip;

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
                                    $conn->exec($sql);
//echo "<Pre>";
//print_r($sql);
//echo "</Pre>";
                                }
                            }
                        }

                        ////虛擬顧客
                        //$rs_virtual_member=(int)$rs_book_booking;
                        //
                        //
                        ////撈取該週滿意度
                        //$query_sql="
                        //    SELECT
                        //        `branch_cs`
                        //    FROM `mssr_branch_cs_log`
                        //    WHERE 1=1
                        //        AND `branch_id` = {$rs_branch_id }
                        //        AND `user_id`   = {$user_id      }
                        //        AND (
                        //            `keyin_cdate` BETWEEN '{$y_monday_date}' AND '{$y_sunday_date}'
                        //        )
                        //";
                        //$err='UPDATE_USER_BRANCH_REVENUE_BONUS_LOG:QUERY FAIL';
                        //$result=$conn->prepare($query_sql);
                        //$result->execute() or
                        //die($err);
                        ////建立資料集陣列
                        //$arrys_cs_result=array();
                        //if(($result->rowCount())!==0){
                        ////有資料存在
                        //    while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                        //        $arrys_cs_result[]=$arry_row;
                        //    }
                        //    foreach($arrys_cs_result as $arry_cs_result){
                        //        $branch_cs   =(int)$arry_cs_result['branch_cs'];
                        //        $rs_branch_cs=(int)$rs_branch_cs+(int)$branch_cs;
                        //    }
                        //}
                        //
                        //
                        ////總營收
                        //$revenue=($rs_virtual_member*$rs_branch_cs)+$rs_book_booking;
                        //
                        //
                        ////檢核這週是否已有營收紀錄
                        //$query_sql="
                        //    SELECT
                        //        `user_id`
                        //    FROM `mssr_user_branch_revenue_log`
                        //    WHERE 1=1
                        //        AND `branch_id`     = {$rs_branch_id }
                        //        AND `user_id`       = {$user_id      }
                        //        AND (
                        //            `revenue_sdate` BETWEEN '{$y_monday_date}' AND '{$y_sunday_date}'
                        //                OR
                        //            `revenue_edate` BETWEEN '{$y_monday_date}' AND '{$y_sunday_date}'
                        //                OR
                        //            `keyin_cdate` BETWEEN '{$y_monday_date}' AND '{$y_sunday_date}'
                        //        )
                        //    LIMIT 1;
                        //";
                        //$err='UPDATE_USER_BRANCH_REVENUE_BONUS_LOG:QUERY FAIL';
                        //$result=$conn->query($query_sql)or
                        //die($err);
                        ////取得筆數
                        //$rowcount=$result->rowCount();
                        //if($rowcount===0){
                        //    $sql="
                        //        # for mssr_user_branch_revenue_log
                        //        INSERT INTO `mssr_user_branch_revenue_log` SET
                        //            `user_id`       = {$user_id             } ,
                        //            `branch_id`     = {$rs_branch_id        } ,
                        //            `log_id`        = NULL                    ,
                        //            `revenue_sdate` ='{$y_monday_date       }',
                        //            `revenue_edate` ='{$y_sunday_date       }',
                        //            `revenue_coin`  = {$revenue             } ,
                        //            `keyin_cdate`   = NOW()                   ;
                        //    ";
                        //    $conn->exec($sql);
                        //}


                        ////撈取任務完成數
                        //$query_sql="
                        //    SELECT
                        //        `user_id`
                        //    FROM `mssr_user_task_log`
                        //    WHERE 1=1
                        //        AND `branch_id`     = {$rs_branch_id }
                        //        AND `user_id`       = {$user_id      }
                        //        AND `task_edate` BETWEEN '{$y_monday_date}' AND '{$y_sunday_date}'
                        //        AND `task_state`    = '成功'
                        //    ;
                        //";
                        //$err='UPDATE_USER_BRANCH_REVENUE_BONUS_LOG:QUERY FAIL';
                        //$result=$conn->query($query_sql)or
                        //die($err);
                        ////取得任務完成數
                        //$taskcount=(int)$result->rowCount();
                        //
                        //
                        ////總BONUS
                        //if($taskcount>=6){
                        //    switch((int)$rs_branch_rank){
                        //        case 1:
                        //            $bonus_coin=(int)((int)(((int)50+(int)$revenue)*((int)1+((int)2*(int)$rs_virtual_member/(int)100)))*(int)1.02);
                        //            $bonus_cs  =(int)((int)((int)$rs_branch_cs*(int)((int)1+((int)$rs_virtual_member/(int)125)))*(int)1.03);;
                        //        break;
                        //
                        //        case 2:
                        //            $bonus_coin=(int)((int)(((int)50+(int)$revenue)*((int)1+((int)2*(int)$rs_virtual_member/(int)100)))*(int)1.05);
                        //            $bonus_cs  =(int)((int)((int)$rs_branch_cs*(int)((int)1+((int)$rs_virtual_member/(int)125)))*(int)1.06);;
                        //        break;
                        //
                        //        case 3:
                        //            $bonus_coin=(int)((int)(((int)50+(int)$revenue)*((int)1+((int)2*(int)$rs_virtual_member/(int)100)))*(int)1.08);
                        //            $bonus_cs  =(int)((int)((int)$rs_branch_cs*(int)((int)1+((int)$rs_virtual_member/(int)125)))*(int)1.09);;
                        //        break;
                        //
                        //        default:
                        //            $err='UPDATE_USER_BRANCH_REVENUE_BONUS_LOG:BONUS FAIL';
                        //            die();
                        //        break;
                        //
                        //    }
                        //}else{
                        //    $bonus_coin=(int)(((int)50+(int)$revenue)*((int)1+((int)2*(int)$rs_virtual_member/(int)100)));
                        //    $bonus_cs  =(int)((int)$rs_branch_cs*(int)((int)1+((int)$rs_virtual_member/(int)125)));
                        //}
                        //
                        //
                        ////檢核這週是否已有BONUS紀錄
                        //$query_sql="
                        //    SELECT
                        //        `user_id`
                        //    FROM `mssr_user_branch_bonus_log`
                        //    WHERE 1=1
                        //        AND `branch_id`     = {$rs_branch_id }
                        //        AND `user_id`       = {$user_id      }
                        //        AND (
                        //            `bonus_sdate` BETWEEN '{$y_monday_date}' AND '{$y_sunday_date}'
                        //                OR
                        //            `bonus_edate` BETWEEN '{$y_monday_date}' AND '{$y_sunday_date}'
                        //                OR
                        //            `keyin_cdate` BETWEEN '{$y_monday_date}' AND '{$y_sunday_date}'
                        //        )
                        //    LIMIT 1;
                        //";
                        //$err='UPDATE_USER_BRANCH_REVENUE_BONUS_LOG:QUERY FAIL';
                        //$result=$conn->query($query_sql)or
                        //die($err);
                        ////取得筆數
                        //$rowcount=$result->rowCount();
                        //if($rowcount===0){
                        //
                        //    $sql="
                        //        # for mssr_user_branch_bonus_log
                        //        INSERT INTO `mssr_user_branch_bonus_log` SET
                        //            `user_id`       = {$user_id         } ,
                        //            `branch_id`     = {$rs_branch_id    } ,
                        //            `log_id`        = NULL                ,
                        //            `bonus_sdate`   ='{$y_monday_date   }',
                        //            `bonus_edate`   ='{$y_sunday_date   }',
                        //            `bonus_cs`      = {$bonus_cs        } ,
                        //            `bonus_coin`    = {$bonus_coin      } ,
                        //            `keyin_cdate`   = NOW()               ;
                        //    ";
                        //    $conn->exec($sql);
                        //
                        //
                        //    //交易紀錄
                        //    if($bonus_coin!==0){
                        //
                        //        //基本參數
                        //        $log_id     ="NULL";
                        //        $tx_sid     =tx_sid($user_id,trim('tx_sys'),mb_internal_encoding());
                        //        $tx_item    ="";
                        //
                        //        $tx_coin    =$bonus_coin;
                        //
                        //        $tx_state   ="正常";
                        //        $tx_note    ="";
                        //        $keyin_cdate="NOW()";
                        //        $keyin_mdate="NULL";
                        //
                        //        $ip="";
                        //        if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                        //            $ip=$_SERVER["HTTP_X_FORWARDED_FOR"];
                        //        }else{
                        //            $ip=$_SERVER["REMOTE_ADDR"];
                        //        }
                        //        $keyin_ip   =$ip;
                        //
                        //        $tx_type    ="branch_bonus";
                        //        $log_state  ="正常";
                        //        $log_note   ="";
                        //
                        //    //---------------------------
                        //    //mssr_tx_sys_log
                        //    //---------------------------
                        //
                        //        $sql="
                        //            # for mssr_tx_sys_log
                        //            INSERT INTO `mssr_tx_sys_log` SET
                        //                `edit_by`       =   {$user_id       } ,
                        //                `user_id`       =   {$user_id       } ,
                        //                `log_id`        =   {$log_id        } ,
                        //                `tx_sid`        =  '{$tx_sid        }',
                        //                `tx_item`       =  '{$tx_item       }',
                        //                `tx_coin`       =   {$tx_coin       } ,
                        //                `tx_state`      =  '{$tx_state      }',
                        //                `tx_note`       =  '{$tx_note       }',
                        //                `keyin_cdate`   =   {$keyin_cdate   } ,
                        //                `keyin_mdate`   =   {$keyin_mdate   } ,
                        //                `keyin_ip`      =  '{$keyin_ip      }';
                        //        ";
                        //        $conn->exec($sql);
                        //
                        //    //---------------------------
                        //    //mssr_user_info
                        //    //---------------------------
                        //
                        //        $sql="
                        //            UPDATE `mssr_user_info` SET
                        //                `user_coin`     = `user_coin`+{$tx_coin}
                        //            WHERE 1=1
                        //                AND `user_id`   ={$user_id  }
                        //            LIMIT 1;
                        //        ";
                        //        $conn->exec($sql);
                        //
                        //    //---------------------------
                        //    //mssr_user_item_log
                        //    //---------------------------
                        //
                        //        $query_sql="
                        //            SELECT
                        //                *
                        //            FROM `mssr_user_info`
                        //            WHERE 1=1
                        //                AND `user_id`   ={$user_id  }
                        //        ";
                        //        $arrys_result=db_result($conn_type='pdo',$conn,$query_sql,array(0,1),$arry_conn);
                        //        if(!empty($arrys_result)){
                        //
                        //            $rs_tx_type     ="branch_bonus";
                        //            $rs_map_item    =mysql_prep(trim($arrys_result[0]['map_item']));
                        //            $rs_box_item    =mysql_prep(trim($arrys_result[0]['box_item']));
                        //            $rs_user_coin   =(int)$arrys_result[0]['user_coin'];
                        //            $rs_log_state   ='正常';
                        //            $rs_log_note    ='';
                        //            $rs_keyin_cdate ="NOW()";
                        //            $rs_keyin_mdate ="NULL";
                        //            $rs_keyin_ip    =$keyin_ip;
                        //
                        //            $sql="
                        //                # for mssr_user_item_log
                        //                INSERT INTO `mssr_user_item_log` SET
                        //                    `edit_by`       =   {$user_id           } ,
                        //                    `user_id`       =   {$user_id           } ,
                        //                    `tx_sid`        =  '{$tx_sid            }',
                        //                    `log_id`        =   {$log_id            } ,
                        //                    `tx_type`       =  '{$rs_tx_type        }',
                        //                    `map_item`      =  '{$rs_map_item       }',
                        //                    `box_item`      =  '{$rs_box_item       }',
                        //                    `user_coin`     =   {$rs_user_coin      } ,
                        //                    `log_state`     =  '{$rs_log_state      }',
                        //                    `log_note`      =  '{$rs_log_note       }',
                        //                    `keyin_cdate`   =   {$rs_keyin_cdate    } ,
                        //                    `keyin_mdate`   =   {$rs_keyin_mdate    } ,
                        //                    `keyin_ip`      =  '{$rs_keyin_ip       }';
                        //            ";
                        //            $conn->exec($sql);
                        //        }
                        //    }
                        //
                        //    //---------------------------
                        //    //mssr_user_branch
                        //    //---------------------------
                        //
                        //    if($bonus_cs!==0){
                        //
                        //        //$sql="
                        //        //    UPDATE `mssr_user_branch` SET
                        //        //        `branch_cs`     = `branch_cs`+{$bonus_cs}
                        //        //    WHERE 1=1
                        //        //        AND `branch_id` = {$rs_branch_id }
                        //        //        AND `user_id`   = {$user_id      }
                        //        //    LIMIT 1;
                        //        //";
                        //        //$conn->exec($sql);
                        //    }
                        //}
                    }
        }
?>