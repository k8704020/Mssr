<?php
//-------------------------------------------------------
//函式: get_rec_info()
//用途: 提取推薦資訊
//日期: 2013年09月20日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function get_rec_info($conn='',$user_id,$book_sid,$rec_type,$array_filter=array(),$order_by_filter='',$arry_limit=array(),$arry_conn){
    //---------------------------------------------------
    //函式: get_rec_info()
    //用途: 提取推薦資訊
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$user_id          使用者主索引
    //$book_sid         書籍識別碼
    //$rec_type         推薦類型
    //$array_filter     欄位條件,   預設空陣列 => 全部撈取
    //$order_by_filter  排序設定,   預設 keyin_cdate(建立時間) DESC
    //$arry_limit       資料筆數限制陣列(等同LIMIT inx,size)
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //檢核參數
        if(!isset($user_id)||(trim($user_id)==='')){
            $err='GET_REC_INFO:NO USER_ID';
            die($err);
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $err='GET_REC_INFO:USER_ID IS INVAILD';
                die($err);
            }
        }

        if(!isset($book_sid)||(trim($book_sid)==='')){
            $err='GET_REC_INFO:NO BOOK_SID';
            die($err);
        }else{
            $book_sid=trim($book_sid);
            if(!preg_match("/^mbc|^mbl|^mbg|^mbu/i",$book_sid)){
                $err='GET_REC_INFO:BOOK_SID IS INVAILD';
                die($err);
            }else{
                $book_sid=addslashes($book_sid);
            }
        }

        if(!isset($rec_type)||(trim($rec_type)==='')){
            $err='GET_REC_INFO:NO REC_TYPE';
            die($err);
        }else{
            $rec_type=trim($rec_type);
        }

		if(!is_array($array_filter)){
			$err='GET_REC_INFO:ARRAY_FILTER IS INVAILD';
            die($err);
		}else{
            $array_filter=array_map("trim",$array_filter);
            if(empty($array_filter)){
                $array_filter=array();
            }else{
                $array_filter=$array_filter;
            }
        }

        if(!isset($order_by_filter)||(trim($order_by_filter)==='')){
            $order_by_filter='`keyin_cdate` DESC';
        }else{
            $order_by_filter=trim($order_by_filter);
        }

        if((!$arry_conn)||(empty($arry_conn))){
            $err='GET_REC_INFO:NO ARRY_CONN';
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
                $err ='GET_REC_INFO:CONNECT FAIL';
                die($err);
            }
        }else{
            $has_conn=false;
        }

        //SQL敘述
        switch(trim($rec_type)){
            case 'star':
            //星評
                switch(empty($array_filter)){
                    case true:
                        $sql="
                            SELECT
                                *
                            FROM `mssr_rec_book_star_log`
                            WHERE 1=1
                                AND `user_id`   = {$user_id }
                                AND `book_sid`  ='{$book_sid}'
                            ORDER BY {$order_by_filter}
                        ";
                    break;

                    default:
                        $array_filter="`".implode("`,`",$array_filter)."`";
                        $sql="
                            SELECT
                                {$array_filter}
                            FROM `mssr_rec_book_star_log`
                            WHERE 1=1
                                AND `user_id`   = {$user_id }
                                AND `book_sid`  ='{$book_sid}'
                            ORDER BY {$order_by_filter}
                        ";
                    break;
                }
            break;
            case 'draw':
            //畫圖
                switch(empty($array_filter)){
                    case true:
                        $sql="
                            SELECT
                                *
                            FROM `mssr_rec_book_draw_log`
                            WHERE 1=1
                                AND `user_id`   = {$user_id }
                                AND `book_sid`  ='{$book_sid}'
                            ORDER BY {$order_by_filter}
                        ";
                    break;

                    default:
                        $array_filter="`".implode("`,`",$array_filter)."`";
                        $sql="
                            SELECT
                                {$array_filter}
                            FROM `mssr_rec_book_draw_log`
                            WHERE 1=1
                                AND `user_id`   = {$user_id }
                                AND `book_sid`  ='{$book_sid}'
                            ORDER BY {$order_by_filter}
                        ";
                    break;
                }
            break;
            case 'text':
            //文字
                switch(empty($array_filter)){
                    case true:
                        $sql="
                            SELECT
                                *
                            FROM `mssr_rec_book_text_log`
                            WHERE 1=1
                                AND `user_id`   = {$user_id }
                                AND `book_sid`  ='{$book_sid}'
                            ORDER BY {$order_by_filter}
                        ";
                    break;

                    default:
                        $array_filter="`".implode("`,`",$array_filter)."`";
                        $sql="
                            SELECT
                                {$array_filter}
                            FROM `mssr_rec_book_text_log`
                            WHERE 1=1
                                AND `user_id`   = {$user_id }
                                AND `book_sid`  ='{$book_sid}'
                            ORDER BY {$order_by_filter}
                        ";
                    break;
                }
            break;
            case 'record':
            //錄音
                switch(empty($array_filter)){
                    case true:
                        $sql="
                            SELECT
                                *
                            FROM `mssr_rec_book_record_log`
                            WHERE 1=1
                                AND `user_id`   = {$user_id }
                                AND `book_sid`  ='{$book_sid}'
                            ORDER BY {$order_by_filter}
                        ";
                    break;

                    default:
                        $array_filter="`".implode("`,`",$array_filter)."`";
                        $sql="
                            SELECT
                                {$array_filter}
                            FROM `mssr_rec_book_record_log`
                            WHERE 1=1
                                AND `user_id`   = {$user_id }
                                AND `book_sid`  ='{$book_sid}'
                            ORDER BY {$order_by_filter}
                        ";
                    break;
                }
            break;

            default:
                $err='GET_REC_INFO:REC_TYPE IS INVAILD';
                die($err);
            break;
        }
        if(!empty($arry_limit)){
           $a=$arry_limit[0];
           $b=$arry_limit[1];
           $sql.=" LIMIT {$a},{$b}";
        }

        //資料庫
        $err='GET_REC_INFO:QUERY FAIL';
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