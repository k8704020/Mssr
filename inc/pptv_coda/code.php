<?php
//-------------------------------------------------------
//函式: pptv_coda()
//用途: 設置畢保德測驗次數額度
//-------------------------------------------------------

    function pptv_coda($db_type='mysql',$arry_conn,$APP_ROOT,$user_id,$coda){
    //---------------------------------------------------
    //函式: pptv_coda()
    //用途: 設置畢保德測驗次數額度
    //---------------------------------------------------
    //$db_type      mysql (預設)
    //$arry_conn    資料庫連線資訊陣列
    //$APP_ROOT     網站根目錄
    //$user_id      使用者主索引
    //$coda         額度
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($db_type)||(trim($db_type)==='')){
                $db_type='mysql';
            }

            if(!isset($arry_conn)||(empty($arry_conn))){
                $err='PPTV_CODA:NO ARRY_CONN';
                die($err);
            }

            if(!isset($APP_ROOT)||trim($APP_ROOT)===''){
                $err='PPTV_CODA:NO APP_ROOT';
                die($err);
            }

            if(!isset($user_id)||(trim($user_id)==='')){
                $err='PPTV_CODA:NO USER_ID';
                die($err);
            }else{
                $user_id=(int)$user_id;
                if($user_id===0){
                    $err='PPTV_CODA:USER_ID IS INVAILD';
                    die($err);
                }
            }

            if(!isset($coda)||(trim($coda)==='')){
                $err='PPTV_CODA:NO CODA';
                die($err);
            }else{
                $coda=(int)$coda;
                if($coda===0){
                    $err='PPTV_CODA:CODA IS INVAILD';
                    die($err);
                }
            }

            //外掛函式檔
            if(!function_exists("mysql_prep")){
                if(false===@include_once($APP_ROOT.'lib/php/db/code.php')){
                    return false;
                }
            }

            if(!function_exists("get_ip")){
                if(false===@include_once($APP_ROOT.'lib/php/net/code.php')){
                    return false;
                }
            }

        //-----------------------------------------------
        //設定
        //-----------------------------------------------

            $db_host  =$arry_conn['db_host'];
            $db_name  =$arry_conn['db_name'];
            $db_user  =$arry_conn['db_user'];
            $db_pass  =$arry_conn['db_pass'];
            $db_encode=$arry_conn['db_encode'];

            $conn_info="{$db_type}".":host={$db_host}".";dbname={$db_name}";
            $options = array(
                PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,           //設置錯誤提示，只獲取代碼
                PDO::ATTR_CASE,              PDO::CASE_NATURAL,             //列名按照原始的方式
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode}"    //設置語系
            );

            try{
                $conn=@new PDO($conn_info, $db_user, $db_pass,$options);
            }catch(PDOException $e){
                $err ='PPTV_CODA:CONNECT FAIL';
                die($err);
            }

        //-----------------------------------------------
        //串接SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `auth`
                FROM `mssr_auth_user`
                WHERE 1=1
                    AND `user_id`={$user_id}
            ";

            $err='PPTV_CODA:QUERY FAIL';
            $result=$conn->prepare($sql);
            $result->execute() or
            die($err);

            //建立資料集陣列
            $arrys_result=array();

            if(($result->rowCount())!==0){
            //有資料存在

                while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                    $arrys_result[]=$arry_row;

                    //權限資訊
                    if(false===@unserialize($arrys_result[0]['auth'])){
                        $auth=array();
                    }else{
                        $auth=@unserialize($arrys_result[0]['auth']);
                    }
                }

                if((!empty($auth))&&(isset($auth['pptv_coda']))){

                    $pptv_coda=(int)$auth['pptv_coda'];

                    //-----------------------------------
                    //釋放資源
                    //-----------------------------------

                        $conn=NULL;

                    //-----------------------------------
                    //回傳
                    //-----------------------------------

                        return $pptv_coda;
                }else{
                //回填畢保德測驗次數額度

                    $auth['pptv_coda']=$coda;
                    $auth=serialize($auth);

                    $sql="
                        UPDATE `mssr_auth_user` SET
                            `auth`='{$auth}'
                        WHERE 1=1
                            AND `user_id`={$user_id}
                        LIMIT 1;
                    ";
                    //送出
                    $err ='PPTV_CODA:DB QUERY FAIL';
                    $sth=$conn->prepare($sql);
                    $sth->execute()or die($err);

                    //-----------------------------------
                    //釋放資源
                    //-----------------------------------

                        $conn=NULL;

                    //-----------------------------------
                    //回傳
                    //-----------------------------------

                        return $coda;
                }
            }else{
            //新增預設權限資料

                $create_by          =(int)$user_id;
                $edit_by            =(int)$user_id;
                $user_id            =(int)$user_id;
                $auth['pptv_coda']  =$coda;
                $auth               =serialize($auth);
                $keyin_cdate        ='NOW()';
                $keyin_mdate        ='NULL';
                $keyin_ip           =get_ip();

                $sql="
                    INSERT INTO `mssr_auth_user` SET
                        `create_by`  = {$create_by  } ,
                        `edit_by`    = {$edit_by    } ,
                        `user_id`    = {$user_id    } ,
                        `auth`       ='{$auth       }',
                        `keyin_cdate`= {$keyin_cdate} ,
                        `keyin_mdate`= {$keyin_mdate} ,
                        `keyin_ip`   ='{$keyin_ip   }'
                ";

                //送出
                $err ='PPTV_CODA:DB QUERY FAIL';
                $sth=$conn->prepare($sql);
                $sth->execute()or die($err);

                //---------------------------------------
                //釋放資源
                //---------------------------------------

                    $conn=NULL;

                //---------------------------------------
                //回傳
                //---------------------------------------

                    return $coda;
            }
    }
?>