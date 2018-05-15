<?php
//-------------------------------------------------------
//更新使用者分店
//-------------------------------------------------------

    //---------------------------------------------------
    //函式
    //---------------------------------------------------

        function update_user_branch($conn,$user_id,$arry_conn){
        //-----------------------------------------------
        //函式: update_user_branch()
        //用途: 更新使用者分店
        //-----------------------------------------------
        //$conn         資料庫連結物件
        //$user_id      使用者主索引
        //$arry_conn    資料庫資訊陣列
        //-----------------------------------------------

            //-------------------------------------------
            //參數處理
            //-------------------------------------------

                //檢核參數
                if(!isset($user_id)||($user_id==='')){
                    $err='UPDATE_USER_BRANCH:NO USER_ID ';
                    die($err);
                }else{
                    $user_id=(int)$user_id;
                    if($user_id===0){
                        $err='UPDATE_USER_BRANCH:USER_ID IS INVALID';
                        die($err);
                    }
                }

                if((!$arry_conn)||(empty($arry_conn))){
                    $err='UPDATE_USER_BRANCH:NO ARRY_CONN';
                    die($err);
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
                        $err ='UPDATE_USER_BRANCH:CONNECT FAIL';
                        die($err);
                    }
                }else{
                    $has_conn=false;
                }

            //-------------------------------------------
            //SQL處理
            //-------------------------------------------

                //---------------------------------------
                //抓取所有分店(包含總店)
                //---------------------------------------

                    $query_sql="
                        SELECT `branch_id`
                        FROM `mssr_branch`
                        WHERE 1=1
                            AND `branch_state`='啟用'
                    ";
                    $err='UPDATE_USER_BRANCH:QUERY FAIL';
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
                        $err='UPDATE_USER_BRANCH:ARRYS_RESULT FAIL';
                        die($err);
                    }

                //---------------------------------------
                //更新使用者分店
                //---------------------------------------

                    foreach($arrys_result as $arry_result){

                        //參數
                        $rs_branch_id=(int)$arry_result['branch_id'];

                        //檢核是否已初始化分店
                        $query_sql="
                            SELECT `branch_id`
                            FROM `mssr_user_branch`
                            WHERE 1=1
                                AND `branch_id`={$rs_branch_id}
                                AND `user_id`  ={$user_id     }
                            LIMIT 1
                        ";
                        $err='UPDATE_USER_BRANCH:QUERY FAIL';
                        $result=$conn->query($query_sql)or
                        die($err);

                        //取得筆數
                        $rowcount=$result->rowCount();

                        if($rowcount===0){
                        //-------------------------------
                        //無資料存在，執行初始化
                        //-------------------------------

                            //參數設置
                            $create_by      =(int)$user_id;
                            $edit_by        =(int)$user_id;
                            $user_id        =(int)$user_id;
                            $branch_id      =(int)$rs_branch_id;

                            if(in_array($branch_id,array(1,2,3,4,5,6))){
                                $branch_rank=3;
                            }else{
                                $branch_rank=1;
                            }

                            $branch_cs      =0;
                            $branch_visit   =0;
                            $branch_nickname="";

                            if(!in_array($rs_branch_id,array(1))){
                                $branch_state   ="停用";
                            }else{
                                $branch_state   ="啟用";
                            }

                            $keyin_cdate    ="NOW()";
                            $keyin_mdate    ="NULL";

                            //執行
                            $sql="
                                # for mssr_user_branch
                                INSERT INTO `mssr_user_branch` SET
                                    `create_by`      =  {$create_by      } ,
                                    `edit_by`        =  {$edit_by        } ,
                                    `user_id`        =  {$user_id        } ,
                                    `branch_id`      =  {$branch_id      } ,
                                    `branch_rank`    =  {$branch_rank    } ,
                                    `branch_cs`      =  {$branch_cs      } ,
                                    `branch_visit`   =  {$branch_visit   } ,
                                    `branch_nickname`= '{$branch_nickname}',
                                    `branch_state`   = '{$branch_state   }',
                                    `keyin_cdate`    =  {$keyin_cdate    } ,
                                    `keyin_mdate`    =  {$keyin_mdate    } ;
                            ";

                            //送出
                            $conn->exec($sql);
                        }
                    }
        }
?>