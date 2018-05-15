<?php
//-------------------------------------------------------
//函式: arrys_users()
//用途: 班級的學生
//日期: 2013年08月20日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function arrys_users($conn='',$class_code='',$date='',$arry_conn){
    //---------------------------------------------------
    //函式: arrys_users()
    //用途: 班級的學生
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$class_code       學期代號
    //$date             日期,預設不分日期
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //檢核參數
        if(!isset($class_code)||(trim($class_code)==='')){
            $err='ARRYS_USERS:NO CLASS_CODE';
            die($err);
        }else{
            $class_code=trim($class_code);
        }

        if(!isset($date)||(trim($date)==='')){
            $date='';
        }else{
            $date=trim($date);
        }

        if((!$arry_conn)||(empty($arry_conn))){
            $err='ARRYS_USERS:NO ARRY_CONN';
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
                $err ='ARRYS_USERS:CONNECT FAIL';
                die($err);
            }
        }else{
            $has_conn=false;
        }

        //SQL敘述
        if($date!==''){
            $sql="
                SELECT `uid`
                FROM `student`
                WHERE 1=1
                    AND `student`.`class_code`='{$class_code}'
                    AND `student`.`start`<'{$date}'
                    AND `student`.`end`>'{$date}'
            ";
            //echo "{$sql}"."<p>";
        }else{
            $sql="
                SELECT `uid`
                FROM `student`
                WHERE 1=1
                    AND `student`.`class_code`='{$class_code}'
            ";
            //echo "{$sql}"."<p>";
        }

        //資料庫
        $err='ARRYS_USERS:QUERY FAIL';
        $result=$conn->prepare($sql);
        $result->execute() or
        die($err);

        //建立資料集陣列
        $arrys_result=array();
        $arrys_user=array();
        $users="";

        if(($result->rowCount())!==0){
        //有資料存在
            while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                $arrys_result[]=$arry_row;
            }

            foreach($arrys_result as $inx=>$arry_result){
                $user_id=(int)$arry_result['uid'];
                $arrys_user[$inx]=$user_id;
            }
        }

        if(!empty($arrys_user)){
            $users="'";
            $users.=implode("','",$arrys_user);
            $users.="'";
        }

        //傳回資料集陣列
        return $users;

        if($has_conn==true){
            $conn=NULL;
        }
    }
?>