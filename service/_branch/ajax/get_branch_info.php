<?php
//-------------------------------------------------------
//明日星球,分店
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",3).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'service/branch/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //school_code       學校代號
    //user_id           使用者主索引
    //branch_id         分店主索引

        $post_chk=array(
            'school_code    ',
            'user_id        ',
            'branch_id      '
        );
        $post_chk=array_map("trim",$post_chk);
        foreach($post_chk as $post){
            if(!isset($_POST[$post])){
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //school_code       學校代號
    //user_id           使用者主索引
    //branch_id         分店主索引

        //POST
        $school_code    =trim($_POST[trim('school_code      ')]);
        $user_id        =trim($_POST[trim('user_id          ')]);
        $branch_id      =trim($_POST[trim('branch_id        ')]);

        //SESSION
        $sess_uid        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:0;
        $sess_class_code =(isset($_SESSION['class'][0][1]))?trim($_SESSION['class'][0][1]):'';
        $sess_school_code=(isset($_SESSION['school_code']))?trim($_SESSION['school_code']):'';
        $sess_sem_year   =(isset($_SESSION['sem_year']))?(int)$_SESSION['sem_year']:0;
        $sess_sem_term   =(isset($_SESSION['sem_term']))?(int)$_SESSION['sem_term']:0;
        $sess_grade      =(isset($_SESSION['grade']))?(int)$_SESSION['grade']:0;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //school_code       學校代號
    //user_id           使用者主索引
    //branch_id         分店主索引

        $arry_err=array();

        if($school_code===''){
           $arry_err[]='學校代號,未輸入!';
        }

        if($user_id===''){
           $arry_err[]='使用者主索引,未輸入!';
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $arry_err[]='使用者主索引,錯誤!';
            }
        }

        if($branch_id===''){
           $arry_err[]='分店主索引,未輸入!';
        }else{
            $branch_id=(int)$branch_id;
            if($branch_id===0){
                $arry_err[]='分店主索引,錯誤!';
            }
        }

        if(count($arry_err)!==0){
            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
            die();
        }

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //SQL查詢
        //-----------------------------------------------

            $school_code                =mysql_prep(trim($school_code));
            $rs_cat_code                ='';

            $arrys_output               =array();
            $arrys_output['read_cno']   =0;
            $arrys_output['rec_cno']    =0;

            //-------------------------------------------
            //查詢分店類別代碼
            //-------------------------------------------

                $sql="
                    SELECT
                        `mssr_book_category`.`cat_code`
                    FROM `mssr_branch`
                        INNER JOIN `mssr_book_category` ON
                        `mssr_branch`.`branch_name`=`mssr_book_category`.`cat_name`
                    WHERE 1=1
                        AND `mssr_branch`.`branch_id`         ={$branch_id}
                        AND `mssr_branch`.`branch_id`         <>1
                        AND `mssr_book_category`.`school_code`='{$school_code}'
                        AND `mssr_book_category`.`cat_state`  ='啟用'
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(!empty($arrys_result)){
                    $arry_result=$arrys_result[0];
                    $rs_cat_code=trim($arry_result['cat_code']);
                }

            //-------------------------------------------
            //查詢閱讀、推薦狀態
            //-------------------------------------------

                if($rs_cat_code!==''){
                    //閱讀
                    $sql="
                        SELECT
                            `mqry`.`book_sid`
                        FROM(
                            SELECT
                                `sqry`.`book_sid`
                            FROM (
                                SELECT
                                    `mssr_book_borrow_semester`.`book_sid`
                                FROM `mssr_book_borrow_semester`
                                WHERE 1=1
                                    AND `mssr_book_borrow_semester`.`user_id`       = {$user_id    }
                                    AND `mssr_book_borrow_semester`.`borrow_sdate` >='2013-09-01'
                            ) AS `sqry`
                            GROUP BY `sqry`.`book_sid`
                        ) AS `mqry`
                            LEFT JOIN `mssr_book_category_rev` ON
                            `mqry`.`book_sid`=`mssr_book_category_rev`.`book_sid`
                        WHERE 1=1
                            AND `mssr_book_category_rev`.`cat_code`='{$rs_cat_code}'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    if(!empty($arrys_result)){
                        $arrys_output['read_cno']=count($arrys_result);
                    }

                    //推薦
                    $sql="
                        SELECT
                            `sqry`.`book_sid`,
                            `sqry`.`rec_draw_cno`,
                            `sqry`.`rec_text_cno`,
                            `sqry`.`rec_record_cno`
                        FROM (
                            SELECT
                                `mssr_rec_book_cno_semester`.`book_sid`,
                                `mssr_rec_book_cno_semester`.`rec_draw_cno`,
                                `mssr_rec_book_cno_semester`.`rec_text_cno`,
                                `mssr_rec_book_cno_semester`.`rec_record_cno`
                            FROM `mssr_rec_book_cno_semester`
                            WHERE 1=1
                                AND `mssr_rec_book_cno_semester`.`user_id`      = {$user_id    }
                                AND `mssr_rec_book_cno_semester`.`keyin_cdate` >='2013-09-01'
                        ) AS `sqry`
                            LEFT JOIN `mssr_book_category_rev` ON
                            `sqry`.`book_sid`=`mssr_book_category_rev`.`book_sid`
                        WHERE 1=1
                            AND `mssr_book_category_rev`.`cat_code`='{$rs_cat_code}'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    if(!empty($arrys_result)){
                        foreach($arrys_result as $arry_result){

                            $single_book_rec=0;
                            $rec_draw_cno   =(int)$arry_result['rec_draw_cno'];
                            $rec_text_cno   =(int)$arry_result['rec_text_cno'];
                            $rec_record_cno =(int)$arry_result['rec_record_cno'];

                            if($rec_draw_cno>0){
                                $single_book_rec+=1;
                            }
                            if($rec_text_cno>0){
                                $single_book_rec+=1;
                            }
                            if($rec_record_cno>0){
                                $single_book_rec+=1;
                            }

                            if($single_book_rec>=2){
                                $arrys_output['rec_cno']+=1;
                            }
                        }
                    }
                }

    //---------------------------------------------------
    //回傳參數
    //---------------------------------------------------

        die(json_encode($arrys_output,true));
?>