<?php
//-------------------------------------------------------
//函式: numrow_book_read_group()
//用途: 學生閱讀本數
//日期: 2013年08月20日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function numrow_book_read_group($conn='',$user_id='',$date='',$array_filter=array(),$semester_start='',$semester_end='',$arry_conn){
    //---------------------------------------------------
    //函式: numrow_book_read_group()
    //用途: 學生閱讀本數
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$user_id          學生主索引
    //$date             日期, 預設不分日期
    //$array_filter     欄位條件,   預設空陣列 => 全部撈取
    //$semester_start   本學期開始時間
    //$semester_end     本學期結束時間
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //檢核參數
        if(!isset($user_id)||(trim($user_id)==='')){
            $err='NUMROW_BOOK_READ_GROUP:NO USER_ID';
            die($err);
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $err='NUMROW_BOOK_READ_GROUP:USER_ID IS INVAILD';
                die($err);
            }
        }

        if(!isset($date)||(trim($date)==='')){
            $date='';
        }else{
            $date=trim($date);
        }

		if(!is_array($array_filter)){
			$err='NUMROW_BOOK_READ_GROUP:ARRAY_FILTER IS INVAILD';
            die($err);
		}else{
            $array_filter=array_map("trim",$array_filter);
            if(empty($array_filter)){
                $array_filter=array();
            }else{
                $array_filter=$array_filter;
            }
        }

        if(!isset($semester_start)||(trim($semester_start)==='')){
            $semester_start='';
        }else{
            $semester_start=trim($semester_start);
        }
        if(!isset($semester_end)||(trim($semester_end)==='')){
            $semester_end='';
        }else{
            $semester_end=trim($semester_end);
        }
        $has_semester=false;
        if(($semester_start!=='')&&($semester_end!=='')){
            $has_semester=true;
        }

        if((!$arry_conn)||(empty($arry_conn))){
            $err='ARRYS_BOOK_CLASS:NO ARRY_CONN';
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
                $err ='NUMROW_BOOK_READ_GROUP:CONNECT FAIL';
                die($err);
            }
        }else{
            $has_conn=false;
        }


        //SQL敘述
        $array_filter="`".implode("`,`",$array_filter)."`";
        if($date!==''){
            $sql="
                SELECT {$array_filter}
                FROM `mssr_book_borrow`
                WHERE 1=1
                    AND `user_id`={$user_id}
                    AND date(`borrow_sdate`)='{$date}'
                GROUP BY `book_sid`
            ";
            //echo "{$sql}"."<p>";
        }else{
            $sql="
                SELECT {$array_filter}
                FROM `mssr_book_borrow_semester`
                WHERE 1=1
                    AND `user_id`={$user_id}

            ";
            if($has_semester){
                $sql.="
                    AND date(`borrow_sdate`) BETWEEN
                        '{$semester_start}' AND '{$semester_end}'
                ";
            }
            $sql.="
                GROUP BY `book_sid`
            ";
            //echo "{$sql}"."<p>";
        }


        //資料庫
        $err='NUMROW_BOOK_READ_GROUP:QUERY FAIL';
        $result=$conn->prepare($sql);
        $result->execute() or
        die($err);

        //傳回筆數
        return $result->rowCount();

        if($has_conn==true){
            $conn=NULL;
        }
    }
?>