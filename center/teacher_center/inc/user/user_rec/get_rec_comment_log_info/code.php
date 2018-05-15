<?php
//-------------------------------------------------------
//函式: get_rec_comment_log_info()
//用途: 提取老師對推薦內容評論表資訊
//日期: 2013年09月20日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function get_rec_comment_log_info($conn='',$user_id,$arrys_rec_sid,$array_filter=array(),$arry_limit=array(),$arry_conn){
    //---------------------------------------------------
    //函式: get_rec_comment_log_info()
    //用途: 提取老師對推薦內容評論表資訊
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$user_id          評論人主索引
    //$arrys_rec_sid    推薦識別碼陣列
    //$array_filter     欄位條件,   預設空陣列 => 全部撈取
    //$arry_limit       資料筆數限制陣列(等同LIMIT inx,size)
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //檢核參數
        if(!isset($user_id)||(trim($user_id)==='')){
            $err='GET_REC_COMMENT_LOG_INFO:NO USER_ID';
            die($err);
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $err='GET_REC_COMMENT_LOG_INFO:USER_ID IS INVAILD';
                die($err);
            }
        }

        if(!isset($arrys_rec_sid)||(!is_array($arrys_rec_sid))){
            $err='GET_REC_COMMENT_LOG_INFO:NO REC_SID';
            die($err);
        }else{
            $arrys_rec_sid=array_map("trim",$arrys_rec_sid);
            if(empty($arrys_rec_sid)){
                $err='GET_REC_COMMENT_LOG_INFO:REC_SID IS INVAILD';
                die($err);
            }else{
                $arrys_rec_sid="'".implode("','",$arrys_rec_sid)."'";
            }
        }

		if(!is_array($array_filter)){
			$err='GET_REC_COMMENT_LOG_INFO:ARRAY_FILTER IS INVAILD';
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
            $err='GET_REC_COMMENT_LOG_INFO:NO ARRY_CONN';
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
                $err ='GET_REC_COMMENT_LOG_INFO:CONNECT FAIL';
                die($err);
            }
        }else{
            $has_conn=false;
        }


        //SQL敘述
        switch(empty($array_filter)){
            case true:
                $sql="
                    SELECT
                        *
                    FROM `mssr_rec_comment_log`
                    WHERE 1=1
                        AND `user_id`  =  {$user_id      }
                        AND `rec_sid` IN ({$arrys_rec_sid})
                ";
            break;

            default:
                $array_filter="`".implode("`,`",$array_filter)."`";
                $sql="
                    SELECT
                        {$array_filter}
                    FROM `mssr_rec_comment_log`
                    WHERE 1=1
                        AND `user_id`  =  {$user_id      }
                        AND `rec_sid` IN ({$arrys_rec_sid})
                ";
            break;
        }
        if(!empty($arry_limit)){
           $a=$arry_limit[0];
           $b=$arry_limit[1];
           $sql.=" LIMIT {$a},{$b}";
        }


        //資料庫
        $err='GET_REC_COMMENT_LOG_INFO:QUERY FAIL';
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