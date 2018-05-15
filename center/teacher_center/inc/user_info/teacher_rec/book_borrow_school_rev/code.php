<?php
//-------------------------------------------------------
//函式: book_borrow_school_rev()
//用途: 借閱書學校關聯
//-------------------------------------------------------

    function book_borrow_school_rev($db_type='mysql',$arry_conn,$borrow_school_from){
    //---------------------------------------------------
    //函式: book_borrow_school_rev()
    //用途: 借閱書學校關聯
    //---------------------------------------------------
    //$db_type              mysql (預設)
    //$arry_conn            資料庫連線資訊陣列
    //$borrow_school_from   借閱的學校代號
    //
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($db_type)||(trim($db_type)==='')){
                $db_type='mysql';
            }

            if(!isset($arry_conn)||(empty($arry_conn))){
                $err='BOOK_BORROW_SCHOOL_REV:NO ARRY_CONN';
                die($err);
            }

            if(!isset($borrow_school_from)||trim($borrow_school_from)===''){
                return false;
            }else{
                $borrow_school_from=trim($borrow_school_from);
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
                $err ='BOOK_BORROW_SCHOOL_REV:CONNECT FAIL';
                die($err);
            }

        //-----------------------------------------------
        //串接SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `borrow_school_to`
                FROM `mssr_book_borrow_school_rev`
                WHERE 1=1
                    AND `borrow_school_from`='{$borrow_school_from}'
            ";

            $err='BOOK_BORROW_SCHOOL_REV:QUERY FAIL';
            $result=$conn->prepare($sql);
            $result->execute() or
            die($err);

            //建立資料集陣列
            $arrys_result=array();
            $arrys_school=array();
            $schools="";

            if(($result->rowCount())!==0){
            //有資料存在
                while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                    $arrys_result[]=$arry_row;
                }

                foreach($arrys_result as $inx=>$arry_result){
                    $school_code=trim($arry_result['borrow_school_to']);
                    $arrys_school[$inx]=$school_code;
                }
            }

            if(!empty($arrys_school)){
                $schools="'";
                $schools.=implode("','",$arrys_school);
                $schools.="'";
            }

        //-----------------------------------------------
        //釋放資源
        //-----------------------------------------------

            $conn=NULL;

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $schools;
    }
?>