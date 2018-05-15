<?php
//-------------------------------------------------------
//函式: get_book_info()
//用途: 提取書本資訊
//日期: 2013年08月20日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function get_book_info($conn='',$book_sid,$arry_conn){
    //---------------------------------------------------
    //函式: get_book_info()
    //用途: 提取書本資訊
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$book_sid         書籍識別碼
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //檢核參數
        if(!isset($book_sid)||(trim($book_sid)==='')){
            $err='GET_BOOK_INFO:NO BOOK_SID';
            die($err);
        }else{
            $book_sid=trim($book_sid);
            if(!preg_match("/^mbc|^mbl|^mbg/i",$book_sid)){
                $err='GET_BOOK_INFO:BOOK_SID IS INVAILD';
                die($err);
            }else{
                $book_sid=addslashes($book_sid);
            }
        }

        if((!$arry_conn)||(empty($arry_conn))){
            $err='GET_BOOK_INFO:NO ARRY_CONN';
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
                $err ='GET_BOOK_INFO:CONNECT FAIL';
                die($err);
            }
        }else{
            $has_conn=false;
        }


        //SQL敘述
        if(preg_match("/^mbc/i",$book_sid)){
            $sql="
                SELECT *
                FROM `mssr_book_class`
                WHERE 1=1
                    AND `book_sid`='{$book_sid}'
            ";
        }elseif(preg_match("/^mbl/i",$book_sid)){
            $sql="
                SELECT *
                FROM `mssr_book_library`
                WHERE 1=1
                    AND `book_sid`='{$book_sid}'
            ";
        }elseif(preg_match("/^mbg/i",$book_sid)){
            $sql="
                SELECT *
                FROM `mssr_book_global`
                WHERE 1=1
                    AND `book_sid`='{$book_sid}'
            ";
        }else{
            $err='GET_BOOK_INFO:BOOK_SID IS INVAILD';
            die($err);
        }

        //資料庫
        $err='GET_BOOK_INFO:QUERY FAIL';
        $result=$conn->prepare($sql);
        $result->execute() or
        die($err);

        //建立資料集陣列
        $arrys_result=array();

        if(($result->rowCount())!==0){
        //有資料存在
            while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                $arrys_result[]=$arry_row;
            }
        }

        //傳回資料集陣列
        return $arrys_result;

        if($has_conn==true){
            $conn=NULL;
        }
    }
?>