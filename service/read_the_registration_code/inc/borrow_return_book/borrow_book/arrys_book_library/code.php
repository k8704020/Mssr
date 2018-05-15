<?php
//-------------------------------------------------------
//函式: arrys_book_library()
//用途: 圖書館書籍陣列
//日期: 2013年08月20日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function arrys_book_library($conn='',$school_code='',$arry_conn){
    //---------------------------------------------------
    //函式: arrys_book_library()
    //用途: 圖書館書籍陣列
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$school_code      學校代號,預設'', 撈出全部
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //檢核參數
        if(isset($school_code)&&trim($school_code)!==''){
            $school_code=trim($school_code);
        }else{
            $school_code='';
        }

        if((!$arry_conn)||(empty($arry_conn))){
            $err='ARRYS_BOOK_LIBRARY:NO ARRY_CONN';
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
                $err ='ARRYS_BOOK_LIBRARY:CONNECT FAIL';
                die($err);
            }
        }else{
            $has_conn=false;
        }

        //SQL敘述
        if($school_code!==''){
            $sql="
                SELECT `book_isbn_10`,`book_isbn_13`
                FROM `mssr_book_library`
                WHERE 1=1
                    AND `mssr_book_library`.`school_code`='{$school_code}'
            ";
            //echo "{$sql}"."<p>";
        }else{
            $sql="
                SELECT `book_isbn_10`,`book_isbn_13`
                FROM `mssr_book_library`
                WHERE 1=1
            ";
            //echo "{$sql}"."<p>";
        }

        //資料庫
        $err='ARRYS_BOOK_LIBRARY:QUERY FAIL';
        $result=$conn->prepare($sql);
        $result->execute() or
        die($err);

        //建立資料集陣列
        $arry_result=array();

        if(($result->rowCount())!==0){
        //有資料存在
            while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                $arrys_result[]=$arry_row;
            }
        }

        //傳回資料集陣列
        return $arry_result;

        if($has_conn==true){
            $conn=NULL;
        }
    }
?>