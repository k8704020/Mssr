<?php
//-------------------------------------------------------
//函式: isbn_code_remind()
//用途: isbn碼輸入提醒
//日期: 2013年8月13日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function isbn_code_remind($db_type='mysql',$arry_conn,$user_id=0){
    //-------------------------------------------------------
    //函式: isbn_code_remind()
    //用途: isbn碼輸入提醒
    //-------------------------------------------------------
    //$db_type      mysql (預設)
    //$arry_conn    資料庫連線資訊陣列
    //$user_id      使用者主索引
    //-------------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($db_type)||(trim($db_type)==='')){
                $db_type='mysql';
            }

            if(!isset($arry_conn)||(empty($arry_conn))){
                $err='ISBN_CODE_REMIND:NO ARRY_CONN';
                die($err);
            }

            if(!isset($user_id)||(trim($user_id)==='')){
                $err='ISBN_CODE_REMIND:NO USER_ID';
                die($err);
            }else{
                $user_id=(int)$user_id;
                if($user_id===0){
                    $err='ISBN_CODE_REMIND:USER_ID IS INVAILD';
                    die($err);
                }
            }

        //-----------------------------------------------
        //設定
        //-----------------------------------------------

            //isbn碼輸入提醒指標
            $isbn_code_remind=true;

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
                $err ='ISBN_CODE_REMIND:CONNECT FAIL';
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
                LIMIT 1
            ";

            $err='ISBN_CODE_REMIND:QUERY FAIL';
            $result=$conn->prepare($sql);
            $result->execute() or
            die($err);

            //建立資料集陣列
            $arry_result=array();

            if(($result->rowCount())!==0){
            //有資料存在
                while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                    $arry_result[]=$arry_row;
                    $auth=unserialize($arry_result[0]['auth']);

                    if(isset($auth['isbn_code_remind'])&&(trim($auth['isbn_code_remind'])!=="")){
                        $_isbn_code_remind=$auth['isbn_code_remind'];

                        if($_isbn_code_remind==='no'){
                            $isbn_code_remind=false;
                        }else{
                            $isbn_code_remind=true;
                        }

                    }else{
                        $isbn_code_remind=true;
                    }
                }
            }else{
            //無資料存在
                $isbn_code_remind=true;
            }

        //-----------------------------------------------
        //釋放資源
        //-----------------------------------------------

            $conn=NULL;

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $isbn_code_remind;
    }
?>