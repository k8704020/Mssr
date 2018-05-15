<?php
//-------------------------------------------------------
//函式: numrow_book_rec()
//用途: 學生推薦數
//日期: 2013年08月20日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function numrow_book_rec($conn='',$user_id='',$date='',$rec_type='',$semester_start='',$semester_end='',$arry_conn){
    //---------------------------------------------------
    //函式: numrow_book_rec()
    //用途: 學生推薦數
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$user_id          學生主索引
    //$date             日期, 預設不分日期
    //$rec_type         推薦類型, 預設不分類型
    //$semester_start   本學期開始時間
    //$semester_end     本學期結束時間
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //檢核參數
        if(!isset($user_id)||(trim($user_id)==='')){
            $err='NUMROW_BOOK_REC:NO USER_ID';
            die($err);
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $err='NUMROW_BOOK_REC:USER_ID IS INVAILD';
                die($err);
            }
        }

        if(!isset($date)||(trim($date)==='')){
            $date='';
        }else{
            $date=trim($date);
        }

        if(!isset($rec_type)||(trim($rec_type)==='')){
            $rec_type='';
        }else{
            $rec_type=trim($rec_type);
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
            $err='NUMROW_BOOK_REC:NO ARRY_CONN';
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
                $err ='NUMROW_BOOK_REC:CONNECT FAIL';
                die($err);
            }
        }else{
            $has_conn=false;
        }


        $query_table="mssr_rec_book_cno";
        if($has_semester){
            //是否為當學期
            $is_now_semester    =false;
            $now_time           =(double)time();
            $semester_start_time=(double)strtotime($semester_start);
            $semester_end_time  =(double)strtotime($semester_end);
            if(($semester_start_time<=$now_time)&&($semester_end_time>=$now_time))$is_now_semester=true;
            if($is_now_semester)$query_table="mssr_rec_book_cno_semester";
        }else{
            $query_table="mssr_rec_book_cno";
        }


        //SQL敘述
        if($rec_type===''){
        //不分類型
            if($date!==''){
                $sql="
                    SELECT `user_id`
                    FROM `mssr_rec_book_cno_one_week`
                    WHERE 1=1
                        AND `user_id`={$user_id}
                        AND `keyin_mdate` BETWEEN '{$date} 00:00:00' AND '{$date} 23:59:59'
                        AND `rec_state`=1
                    GROUP BY `user_id`, `book_sid`
                ";
            }else{
                $sql="
                    SELECT `user_id`
                    FROM `{$query_table}`
                    WHERE 1=1
                        AND `user_id`={$user_id}
                        AND `rec_state`=1
                ";
                if($has_semester){
                    $sql.="
                        AND `keyin_mdate` BETWEEN
                            '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
                    ";
                }
                $sql.="GROUP BY `user_id`, `book_sid`";
            }
        }else{
        //分類型
            switch(trim($rec_type)){

                case 'star':
                    if($date!==''){
                        $sql="
                            SELECT `user_id`
                            FROM `mssr_rec_book_cno_one_week`
                            WHERE 1=1
                                AND `user_id`={$user_id}
                                AND `rec_stat_cno`<>0
                                AND `keyin_mdate` BETWEEN '{$date} 00:00:00' AND '{$date} 23:59:59'
                                AND `rec_state`=1
                        ";
                        $sql.="GROUP BY `user_id`, `book_sid`";
                    }else{
                        $sql="
                            SELECT `user_id`
                            FROM `{$query_table}`
                            WHERE 1=1
                                AND `user_id`={$user_id}
                                AND `rec_stat_cno`<>0
                                AND `rec_state`=1
                        ";
                        if($has_semester){
                            $sql.="
                                AND `keyin_mdate` BETWEEN
                                    '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
                            ";
                        }
                        $sql.="GROUP BY `user_id`, `book_sid`";
                    }
                break;

                case 'draw':
                    if($date!==''){
                        $sql="
                            SELECT `user_id`
                            FROM `mssr_rec_book_cno_one_week`
                            WHERE 1=1
                                AND `user_id`={$user_id}
                                AND `rec_draw_cno`<>0
                                AND `keyin_mdate` BETWEEN '{$date} 00:00:00' AND '{$date} 23:59:59'
                                AND `rec_state`=1
                        ";
                        $sql.="GROUP BY `user_id`, `book_sid`";
                    }else{
                        $sql="
                            SELECT `user_id`
                            FROM `{$query_table}`
                            WHERE 1=1
                                AND `user_id`={$user_id}
                                AND `rec_draw_cno`<>0
                                AND `rec_state`=1
                        ";
                        if($has_semester){
                            $sql.="
                                AND `keyin_mdate` BETWEEN
                                    '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
                            ";
                        }
                        $sql.="GROUP BY `user_id`, `book_sid`";
                    }
                break;

                case 'text':
                    if($date!==''){
                        $sql="
                            SELECT `user_id`
                            FROM `mssr_rec_book_cno_one_week`
                            WHERE 1=1
                                AND `user_id`={$user_id}
                                AND `rec_text_cno`<>0
                                AND `keyin_mdate` BETWEEN '{$date} 00:00:00' AND '{$date} 23:59:59'
                                AND `rec_state`=1
                        ";
                        $sql.="GROUP BY `user_id`, `book_sid`";
                    }else{
                        $sql="
                            SELECT `user_id`
                            FROM `{$query_table}`
                            WHERE 1=1
                                AND `user_id`={$user_id}
                                AND `rec_text_cno`<>0
                                AND `rec_state`=1
                        ";
                        if($has_semester){
                            $sql.="
                                AND `keyin_mdate` BETWEEN
                                    '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
                            ";
                        }
                        $sql.="GROUP BY `user_id`, `book_sid`";
                    }
                break;

                case 'record':
                    if($date!==''){
                        $sql="
                            SELECT `user_id`
                            FROM `mssr_rec_book_cno_one_week`
                            WHERE 1=1
                                AND `user_id`={$user_id}
                                AND `rec_record_cno`<>0
                                AND `keyin_mdate` BETWEEN '{$date} 00:00:00' AND '{$date} 23:59:59'
                                AND `rec_state`=1
                        ";
                        $sql.="GROUP BY `user_id`, `book_sid`";
                    }else{
                        $sql="
                            SELECT `user_id`
                            FROM `{$query_table}`
                            WHERE 1=1
                                AND `user_id`={$user_id}
                                AND `rec_record_cno`<>0
                                AND `rec_state`=1
                        ";
                        if($has_semester){
                            $sql.="
                                AND `keyin_mdate` BETWEEN
                                    '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
                            ";
                        }
                        $sql.="GROUP BY `user_id`, `book_sid`";
                    }
                break;

            }
        }


        //資料庫
        $err='NUMROW_BOOK_REC:QUERY FAIL';
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