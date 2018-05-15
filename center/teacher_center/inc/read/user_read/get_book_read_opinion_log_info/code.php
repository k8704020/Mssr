<?php
//-------------------------------------------------------
//函式: get_book_read_opinion_log_info()
//用途: 提取閱讀調查log表資訊
//日期: 2013年10月20日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function get_book_read_opinion_log_info($conn='',$borrow_sid='',$array_filter=array(),$arry_conn){
    //---------------------------------------------------
    //函式: get_book_read_opinion_log_info()
    //用途: 提取閱讀調查log表資訊
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$borrow_sid       借閱識別碼  預設空字串 => 全部撈取
    //$array_filter     欄位條件,   預設空陣列 => 全部撈取
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //檢核參數
        if(!isset($borrow_sid)||(trim($borrow_sid)==='')){
            $borrow_sid='';
        }else{
            $borrow_sid=trim($borrow_sid);
        }

		if(!is_array($array_filter)){
			$err='GET_BOOK_READ_OPINION_LOG_INFO:ARRAY_FILTER IS INVAILD';
            die($err);
		}else{
            $array_filter=array_map("trim",$array_filter);
            if(empty($array_filter)){
                $array_filter=array();
            }else{
                $array_filter=$array_filter;
            }
        }

        if((!$arry_conn)||(empty($arry_conn))){
            $err='GET_BOOK_READ_OPINION_LOG_INFO:NO ARRY_CONN';
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
                $err ='GET_BOOK_READ_OPINION_LOG_INFO:CONNECT FAIL';
                die($err);
            }
        }else{
            $has_conn=false;
        }

        //SQL敘述
        switch(empty($array_filter)){
            case true:
                if($borrow_sid===''){
                    $sql="
                        SELECT
                            *
                        FROM `mssr_book_read_opinion_log`
                        WHERE 1=1
                    ";
                }else{
                    $sql="
                        SELECT
                            *
                        FROM `mssr_book_read_opinion_log`
                        WHERE 1=1
                            AND `borrow_sid` = '{$borrow_sid}'
                        LIMIT 1
                    ";
                }
            break;

            default:
                $array_filter="`".implode("`,`",$array_filter)."`";
                if($borrow_sid===''){
                    $sql="
                        SELECT
                            {$array_filter}
                        FROM `mssr_book_read_opinion_log`
                        WHERE 1=1
                    ";
                }else{
                    $sql="
                        SELECT
                            {$array_filter}
                        FROM `mssr_book_read_opinion_log`
                        WHERE 1=1
                            AND `borrow_sid` = '{$borrow_sid}'
                        LIMIT 1
                    ";
                }
            break;
        }

        //資料庫
        $err='GET_BOOK_READ_OPINION_LOG_INFO:QUERY FAIL';
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