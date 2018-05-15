<?php
//-------------------------------------------------------
//函式: default_book_category()
//用途: 初始化書籍類別
//-------------------------------------------------------

    function default_book_category($school_code,$user_id,$conn,$arry_conn){
    //---------------------------------------------------
    //函式: default_book_category()
    //用途: 初始化書籍類別
    //---------------------------------------------------
    //$school_code      學校代號
    //$user_id          使用者主索引
    //$conn             連線物件
    //$arry_conn        連線陣列
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if((!isset($school_code))||(trim($school_code)==='')||(is_numeric($school_code))){
                return false;
            }else{
                $school_code=trim($school_code);
            }

            if((!isset($user_id))||((int)$user_id===0)||(!is_numeric($user_id))){
                return false;
            }else{
                $user_id=(int)($user_id);
            }

        //-----------------------------------------------
        //SQL處理
        //-----------------------------------------------

            $arry_default_book_category=array(
                trim('未分類')=>1,
                trim('生活'  )=>2,
                trim('藝術'  )=>3,
                trim('文學'  )=>4,
                trim('社會'  )=>5,
                trim('科學'  )=>6
            );

            $sql="
                SELECT `mssr`.`mssr_book_category`.`cat_id`
                FROM `mssr`.`mssr_book_category`
                WHERE 1=1
                    AND `mssr`.`mssr_book_category`.`cat2_id`    =1
                    AND `mssr`.`mssr_book_category`.`cat3_id`    =1
                    AND `mssr`.`mssr_book_category`.`cat_state`  ='啟用'
                    AND `mssr`.`mssr_book_category`.`school_code`='{$school_code}'
            ";
            $book_category_results=db_result($conn_type='pdo',$conn,$sql,array(0,1),$arry_conn);
            if(empty($book_category_results)){
                $sql="";
                foreach($arry_default_book_category as $default_category_name=>$default_cat1_id){
                    $cat_code=cat_code((int)$default_cat1_id,mb_internal_encoding());
                    $default_cat1_id=(int)$default_cat1_id;
                    $default_category_name=trim($default_category_name);
                    $sql.="
                        INSERT IGNORE INTO `mssr`.`mssr_book_category`(
                            `create_by`,`edit_by`,`school_code`,`cat_id`,`cat_name`,`cat_code`,`cat1_id`,`cat2_id`,`cat3_id`,`cat_state`,`keyin_cdate`,`keyin_mdate`
                        )VALUES({$user_id},{$user_id},'{$school_code}',NULL,'{$default_category_name}','{$cat_code}',{$default_cat1_id},'1','1','啟用',NOW(),NOW());
                    ";
                }
                @$conn->exec($sql);
            }

            //foreach($arry_default_book_category as $default_category_name=>$default_cat1_id){
            //    $default_category_name=trim($default_category_name);
            //    $sql="
            //        SELECT `mssr`.`mssr_book_category`.`cat_id`, `mssr`.`mssr_book_category`.`cat1_id`, `mssr`.`mssr_book_category`.`cat_name`
            //        FROM `mssr`.`mssr_book_category`
            //        WHERE 1=1
            //            AND `mssr`.`mssr_book_category`.`cat1_id`    <>1
            //            AND `mssr`.`mssr_book_category`.`cat2_id`    =1
            //            AND `mssr`.`mssr_book_category`.`cat3_id`    =1
            //            AND `mssr`.`mssr_book_category`.`cat_state`  ='啟用'
            //            AND `mssr`.`mssr_book_category`.`cat_name`   ='{$default_category_name}'
            //            AND `mssr`.`mssr_book_category`.`school_code`='{$school_code}'
            //    ";
            //    $book_category1_results=db_result($conn_type='pdo',$conn,$sql,array(0,1),$arry_conn);
            //    if(!empty($book_category1_results)){
            //        //$arry_default_book_category2=array(
            //        //    trim('生活'  )=>array(
            //        //        trim('數學'),
            //        //        trim('科技'),
            //        //        trim('理財'),
            //        //        trim('能源'),
            //        //        trim('理化'),
            //        //        trim('健康'),
            //        //        trim('法律'),
            //        //        trim('宗教')
            //        //    ),
            //        //    trim('藝術'  )=>array(
            //        //        trim('攝影'),
            //        //        trim('舞蹈'),
            //        //        trim('繪畫'),
            //        //        trim('音樂'),
            //        //        trim('戲劇'),
            //        //        trim('書法')
            //        //    ),
            //        //    trim('文學'  )=>array(
            //        //
            //        //    ),
            //        //    trim('社會'  )=>array(
            //        //
            //        //    ),
            //        //    trim('科學'  )=>array(
            //        //
            //        //    )
            //        //);
            //        //echo "<Pre>";
            //        //print_r($book_category1_results);
            //        //echo "</Pre>";
            //    }
            //}
            ////die();

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return true;
    }
?>