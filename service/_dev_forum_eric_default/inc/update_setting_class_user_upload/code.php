<?php
//-------------------------------------------------------
//函式: update_setting_class_user_upload()
//用途: 更新班級條件(使用者上傳)
//-------------------------------------------------------

    function update_setting_class_user_upload($db_type='mysql',$arry_conn,$APP_ROOT,$class_code,$user_id){
    //---------------------------------------------------
    //函式: update_setting_class_user_upload()
    //用途: 更新班級條件(使用者上傳)
    //---------------------------------------------------
    //$db_type      mysql (預設)
    //$arry_conn    資料庫連線資訊陣列
    //$APP_ROOT     網站根目錄
    //$class_code   班級代號
    //$user_id      使用者主索引
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($db_type)||(trim($db_type)==='')){
                $db_type='mysql';
            }

            if(!isset($arry_conn)||(empty($arry_conn))){
                $err='UPDATE_SETTING_CLASS_USER_UPLOAD:NO ARRY_CONN';
                die($err);
            }

            if(!isset($APP_ROOT)||trim($APP_ROOT)===''){
                $err='UPDATE_SETTING_CLASS_USER_UPLOAD:NO APP_ROOT';
                die($err);
            }

            if(!isset($class_code)||(trim($class_code)==='')){
                $err='UPDATE_SETTING_CLASS_USER_UPLOAD:NO CLASS_CODE';
                die($err);
            }else{
                $class_code=addslashes(trim($class_code));
            }

            if(!isset($user_id)||(trim($user_id)==='')){
                $err='UPDATE_SETTING_CLASS_USER_UPLOAD:NO USER_ID';
                die($err);
            }else{
                $user_id=(int)addslashes(trim($user_id));
                if($user_id===0){
                    $err='UPDATE_SETTING_CLASS_USER_UPLOAD:USER_ID IS INFALID';
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
                $err ='UPDATE_SETTING_CLASS_USER_UPLOAD:CONNECT FAIL';
                die($err);
            }

        //-----------------------------------------------
        //串接SQL
        //-----------------------------------------------

            $sql="
                SELECT `mssr_forum`.`mssr_forum_setting_class`.`setting`
                FROM `mssr_forum`.`mssr_forum_setting_class`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_setting_class`.`class_code`='{$class_code}'
                LIMIT 1
            ";
            $err='UPDATE_SETTING_CLASS_USER_UPLOAD:QUERY FAIL';
            $result=$conn->prepare($sql);
            $result->execute() or
            die($err);

            //建立資料集陣列
            $arrys_result=array();

            if(($result->rowCount())!==0){
            //有資料存在
                while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                    $arrys_result[]=$arry_row;

                    //設定資訊
                    if(false===@json_decode($arrys_result[0]['setting'],true)){
                        $arry_setting=array();
                    }else{
                        $arry_setting=@json_decode($arrys_result[0]['setting'],true);
                    }
                }

                if((!empty($arry_setting))&&(isset($arry_setting['user_upload']))){
                    $setting_user_upload=(int)$arry_setting['user_upload'];

                    //-----------------------------------
                    //釋放資源
                    //-----------------------------------

                        $conn=NULL;

                    //-----------------------------------
                    //回傳
                    //-----------------------------------

                        return $setting_user_upload;
                }else{
                //回填預設設定資料
                    $arry_setting['user_upload']=1;
                    $setting=json_encode($arry_setting,true);
                    $class_code=addslashes(trim($class_code));

                    $sql="
                        UPDATE `mssr_forum`.`mssr_forum_setting_class` SET
                            `setting`='{$setting}'
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_setting_class`.`class_code`='{$class_code}'
                        LIMIT 1
                    ";
                    //送出
                    $err ='UPDATE_SETTING_CLASS_USER_UPLOAD:DB QUERY FAIL';
                    $sth=$conn->prepare($sql);
                    $sth->execute()or die($err);

                    //-----------------------------------
                    //釋放資源
                    //-----------------------------------

                        $conn=NULL;

                    //-----------------------------------
                    //回傳
                    //-----------------------------------

                        return $arry_setting['user_upload'];
                }
            }else{
            //新增預設設定資料
                $edit_by     =(int)$user_id;
                $class_code  =addslashes(trim($class_code));
                $keyin_mdate ='NULL';

                $arry_setting=array('user_upload'=>1);
                $setting     =json_encode($arry_setting,true);

                $sql="
                    INSERT INTO `mssr_forum`.`mssr_forum_setting_class` SET
                        `edit_by`    = {$edit_by    } ,
                        `class_code` ='{$class_code }',
                        `setting`    ='{$setting    }',
                        `keyin_mdate`= {$keyin_mdate}
                ";
                //送出
                $err ='UPDATE_SETTING_CLASS_USER_UPLOAD:DB QUERY FAIL';
                $sth=$conn->prepare($sql);
                $sth->execute()or die($err);

                //---------------------------------------
                //釋放資源
                //---------------------------------------

                    $conn=NULL;

                //---------------------------------------
                //回傳
                //---------------------------------------

                    return 1;
            }
    }
?>