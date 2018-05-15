<?php
//-------------------------------------------------------
//網管中心
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",4).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //data_type     資料條件
    //class_code    班級代號

        $get_chk=array(
            'data_type',
            'class_code'
        );
        $get_chk=array_map("trim",$get_chk);
        foreach($get_chk as $get){
            if(!isset($_GET[$get])){
                die('isset');
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //data_type     資料條件
    //class_code    班級代號

        //GET
        $data_type  =trim($_GET[trim('data_type')]);
        $class_code =trim($_GET[trim('class_code')]);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //data_type     資料條件
    //class_code    班級代號

        $arry_err=array();

        if($data_type===''){
           $arry_err[]='資料條件,未輸入!';
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

        //初始化, 輸出
        $arry_output=array();

        //本學期開始時間
        $semester_start =trim('2014-02-01');

        //本學期結束時間
        $semester_end   =trim('2014-06-30');

        //建立連線 user
        $conn_user=conn($db_type='mysql',$arry_conn_user);

        //建立連線 mssr
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

    //---------------------------------------------------
    //判斷輸出類型
    //---------------------------------------------------

        switch($data_type){
            case 's_read_group':
            //-------------------------------------------
            //查找, 學生閱讀登記平均本數
            //-------------------------------------------

                //---------------------------------------
                //學生人數
                //---------------------------------------

                    $users=arrys_users($conn_user,$class_code,$date=date("Y-m-d"),$arry_conn_user);
                    $arrys_users=array();
                    if(empty($users)){
                        $arrys_users=array();
                    }else{
                        $arrys_users=explode("','",$users);
                        $arrys_users_cno=count($arrys_users);
                        foreach($arrys_users as $inx=>$val){
                            if(($inx===0)||($inx===$arrys_users_cno-1)){
                                $val=str_replace("'","", $val);
                                $arrys_users[$inx]=(int)$val;
                            }
                        }
                    }

                    //學生人數
                    $users_cno=count($arrys_users);

                //---------------------------------------
                //查找
                //---------------------------------------

                    $s_read_group=0;
                    if($users_cno!==0){
                        $sql="
                            SELECT
                                COUNT(*) as `s_read_group`
                            FROM `mssr_book_borrow_log`
                            WHERE 1=1
                                AND `user_id` IN ({$users})
                                #AND `borrow_edate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        if(!empty($arrys_result)){
                            $s_read_group=(int)$arrys_result[0]['s_read_group'];
                            $s_read_group=round($s_read_group/$users_cno, 0);
                        }
                    }

                    //輸出
                    $arry_output['data_type']=$data_type;
                    $arry_output['class_code']=$class_code;
                    $arry_output['cno']=$s_read_group;
                    $arry_output=json_encode($arry_output);
                    die($arry_output);
            break;

            case 's_rec_group':
            //-------------------------------------------
            //查找, 學生推薦平均本數
            //-------------------------------------------

                //---------------------------------------
                //學生人數
                //---------------------------------------

                    $users=arrys_users($conn_user,$class_code,$date=date("Y-m-d"),$arry_conn_user);
                    $arrys_users=array();
                    if(empty($users)){
                        $arrys_users=array();
                    }else{
                        $arrys_users=explode("','",$users);
                        $arrys_users_cno=count($arrys_users);
                        foreach($arrys_users as $inx=>$val){
                            if(($inx===0)||($inx===$arrys_users_cno-1)){
                                $val=str_replace("'","", $val);
                                $arrys_users[$inx]=(int)$val;
                            }
                        }
                    }

                    //學生人數
                    $users_cno=count($arrys_users);

                //---------------------------------------
                //查找
                //---------------------------------------

                    $s_rec_group=0;
                    if($users_cno!==0){
                        $sql="
                            SELECT
                                COUNT(*) as `s_rec_group`
                            FROM `mssr_rec_book_cno`
                            WHERE 1=1
                                AND `user_id` IN ({$users})
                                #AND `keyin_mdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        if(!empty($arrys_result)){
                            $s_rec_group=(int)$arrys_result[0]['s_rec_group'];
                            $s_rec_group=round($s_rec_group/$users_cno, 0);
                        }
                    }

                    //輸出
                    $arry_output['data_type']=$data_type;
                    $arry_output['class_code']=$class_code;
                    $arry_output['cno']=$s_rec_group;
                    $arry_output=json_encode($arry_output);
                    die($arry_output);
            break;

            case 't_rec_group':
            //-------------------------------------------
            //查找, 老師推薦本數
            //-------------------------------------------

                //---------------------------------------
                //老師主索引
                //---------------------------------------

                    $sql="
                        SELECT `uid`
                        FROM `teacher`
                        WHERE 1=1
                            AND `class_code`='{$class_code}'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                    $uid=(int)$arrys_result[0]['uid'];

                //---------------------------------------
                //查找
                //---------------------------------------

                    $t_rec_group=0;
                    $sql="
                        SELECT
                            COUNT(*) as `t_rec_group`
                        FROM `mssr_rec_book_cno`
                        WHERE 1=1
                            AND `user_id` = {$uid}
                            #AND `keyin_mdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    if(!empty($arrys_result)){
                        $t_rec_group=(int)$arrys_result[0]['t_rec_group'];
                    }

                    //輸出
                    $arry_output['data_type']=$data_type;
                    $arry_output['class_code']=$class_code;
                    $arry_output['cno']=$t_rec_group;
                    $arry_output=json_encode($arry_output);
                    die($arry_output);
            break;

            case 't_comment_frequency':
            //-------------------------------------------
            //查找, 老師指導次數
            //-------------------------------------------

                //---------------------------------------
                //老師主索引
                //---------------------------------------

                    $sql="
                        SELECT `uid`
                        FROM `teacher`
                        WHERE 1=1
                            AND `class_code`='{$class_code}'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                    $uid=(int)$arrys_result[0]['uid'];

                //---------------------------------------
                //查找
                //---------------------------------------

                    $t_comment_frequency=0;
                    $sql="
                        SELECT
                            COUNT(*) as `t_comment_frequency`
                        FROM `mssr_rec_comment_log`
                        WHERE 1=1
                            AND `user_id` = {$uid}
                            #AND `keyin_cdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    if(!empty($arrys_result)){
                        $t_comment_frequency=(int)$arrys_result[0]['t_comment_frequency'];
                    }

                    //輸出
                    $arry_output['data_type']=$data_type;
                    $arry_output['class_code']=$class_code;
                    $arry_output['cno']=$t_comment_frequency;
                    $arry_output=json_encode($arry_output);
                    die($arry_output);
            break;

            case 't_comment_group':
            //-------------------------------------------
            //查找, 老師指導本數
            //-------------------------------------------

                //---------------------------------------
                //老師主索引
                //---------------------------------------

                    $sql="
                        SELECT `uid`
                        FROM `teacher`
                        WHERE 1=1
                            AND `class_code`='{$class_code}'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                    $uid=(int)$arrys_result[0]['uid'];

                //---------------------------------------
                //查找
                //---------------------------------------

                    $t_comment_group=0;
                    //$sql="
                    //    SELECT
                    //
                    //        (
                    //            CASE
                    //                WHEN `sqry`.`rec_sid` REGEXP '^mrbs'
                    //                THEN (
                    //                    SELECT `book_sid`
                    //                    FROM `mssr_rec_book_star_log`
                    //                        WHERE `mssr_rec_book_star_log`.`rec_sid`=`sqry`.`rec_sid`
                    //                )
                    //
                    //                WHEN `sqry`.`rec_sid` REGEXP '^mrbd'
                    //                THEN (
                    //                    SELECT `book_sid`
                    //                    FROM `mssr_rec_book_draw_log`
                    //                        WHERE `mssr_rec_book_draw_log`.`rec_sid`=`sqry`.`rec_sid`
                    //                )
                    //
                    //                WHEN `sqry`.`rec_sid` REGEXP '^mrbt'
                    //                THEN (
                    //                    SELECT `book_sid`
                    //                    FROM `mssr_rec_book_text_log`
                    //                        WHERE `mssr_rec_book_text_log`.`rec_sid`=`sqry`.`rec_sid`
                    //                )
                    //            END
                    //        ) AS `book__sid`
                    //
                    //    FROM (
                    //        SELECT
                    //            `rec_sid`
                    //        FROM `mssr_rec_comment_log`
                    //        WHERE 1=1
                    //            AND `user_id` = {$uid}
                    //            #AND `keyin_cdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                    //    ) AS `sqry`
                    //    WHERE 1=1
                    //    GROUP BY `book__sid`
                    //";
                    $arry_rec_sid=array();
                    $arry_book_sid=array();
                    $sql="
                        SELECT
                            `rec_sid`
                        FROM `mssr_rec_comment_log`
                        WHERE 1=1
                            AND `user_id` = {$uid}
                            #AND `keyin_cdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    if(!empty($arrys_result)){
                        foreach($arrys_result as $arry_result){
                            $rs_rec_sid=trim($arry_result['rec_sid']);
                            $arry_rec_sid[]=$rs_rec_sid;
                        }
                        if(!empty($arry_rec_sid)){
                            foreach($arry_rec_sid as $rec_sid){
                                $rec_sid=trim($rec_sid);

                                $sql="
                                    SELECT `book_sid`
                                    FROM `mssr_rec_book_star_log`
                                        WHERE `mssr_rec_book_star_log`.`rec_sid`='{$rec_sid}'
                                ";
                                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                if(!empty($arrys_result)){
                                    $rs_book_sid=trim($arrys_result[0]['book_sid']);
                                    if(!in_array($rs_book_sid,$arry_book_sid)){
                                        $arry_book_sid[]=$rs_book_sid;
                                    }
                                }

                                $sql="
                                    SELECT `book_sid`
                                    FROM `mssr_rec_book_draw_log`
                                        WHERE `mssr_rec_book_draw_log`.`rec_sid`='{$rec_sid}'
                                ";
                                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                if(!empty($arrys_result)){
                                    $rs_book_sid=trim($arrys_result[0]['book_sid']);
                                    if(!in_array($rs_book_sid,$arry_book_sid)){
                                        $arry_book_sid[]=$rs_book_sid;
                                    }
                                }

                                $sql="
                                    SELECT `book_sid`
                                    FROM `mssr_rec_book_text_log`
                                        WHERE `mssr_rec_book_text_log`.`rec_sid`='{$rec_sid}'
                                ";
                                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                if(!empty($arrys_result)){
                                    $rs_book_sid=trim($arrys_result[0]['book_sid']);
                                    if(!in_array($rs_book_sid,$arry_book_sid)){
                                        $arry_book_sid[]=$rs_book_sid;
                                    }
                                }

                            }

                            $t_comment_group=count($arry_book_sid);
                        }
                    }

                    //輸出
                    $arry_output['data_type']=$data_type;
                    $arry_output['class_code']=$class_code;
                    $arry_output['cno']=$t_comment_group;
                    $arry_output=json_encode($arry_output);
                    die($arry_output);

            break;

            case 'f_rec_group':
            //-------------------------------------------
            //查找, 家長推薦總本數
            //-------------------------------------------

                //---------------------------------------
                //學生人數
                //---------------------------------------

                    $users=arrys_users($conn_user,$class_code,$date=date("Y-m-d"),$arry_conn_user);
                    $arrys_users=array();
                    if(empty($users)){
                        $arrys_users=array();
                    }else{
                        $arrys_users=explode("','",$users);
                        $arrys_users_cno=count($arrys_users);
                        foreach($arrys_users as $inx=>$val){
                            if(($inx===0)||($inx===$arrys_users_cno-1)){
                                $val=str_replace("'","", $val);
                                $arrys_users[$inx]=(int)$val;
                            }
                        }
                    }

                    //學生人數
                    $users_cno=count($arrys_users);

                //---------------------------------------
                //查找, 所有家長
                //---------------------------------------

                    //初始化, 所有家長
                    $arrys_kinship=array();
                    if(!empty($arrys_users)){
                        $sql="
                            SELECT
                                `uid_main`
                            FROM `kinship`
                            WHERE 1=1
                                AND `uid_sub` IN ({$users})
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
                        foreach($arrys_result as $inx=>$arry_result){
                            $uid_main=(int)$arry_result['uid_main'];
                            $arrys_kinship[$inx]=$uid_main;
                        }
                        if(!empty($arrys_kinship)){
                            $kinship ="'";
                            $kinship.=implode("','",$arrys_kinship);
                            $kinship.="'";
                        }
                    }

                //---------------------------------------
                //查找
                //---------------------------------------

                    $f_rec_group=0;
                    if(!empty($arrys_kinship)){
                        $sql="
                            SELECT
                                COUNT(*) as `f_rec_group`
                            FROM `mssr_rec_book_cno`
                            WHERE 1=1
                                AND `user_id` IN ({$kinship})
                                #AND `keyin_mdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        if(!empty($arrys_result)){
                            $f_rec_group=(int)$arrys_result[0]['f_rec_group'];
                        }
                    }

                    //輸出
                    $arry_output['data_type']=$data_type;
                    $arry_output['class_code']=$class_code;
                    $arry_output['cno']=$f_rec_group;
                    $arry_output=json_encode($arry_output);
                    die($arry_output);
            break;

            case 'f_rec_cno':
            //-------------------------------------------
            //查找, 家長有推薦本的人數
            //-------------------------------------------

                //---------------------------------------
                //學生人數
                //---------------------------------------

                    $users=arrys_users($conn_user,$class_code,$date=date("Y-m-d"),$arry_conn_user);
                    $arrys_users=array();
                    if(empty($users)){
                        $arrys_users=array();
                    }else{
                        $arrys_users=explode("','",$users);
                        $arrys_users_cno=count($arrys_users);
                        foreach($arrys_users as $inx=>$val){
                            if(($inx===0)||($inx===$arrys_users_cno-1)){
                                $val=str_replace("'","", $val);
                                $arrys_users[$inx]=(int)$val;
                            }
                        }
                    }

                    //學生人數
                    $users_cno=count($arrys_users);

                //---------------------------------------
                //查找, 所有家長
                //---------------------------------------

                    //初始化, 所有家長
                    $arrys_kinship=array();
                    if(!empty($arrys_users)){
                        $sql="
                            SELECT
                                `uid_main`
                            FROM `kinship`
                            WHERE 1=1
                                AND `uid_sub` IN ({$users})
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
                        foreach($arrys_result as $inx=>$arry_result){
                            $uid_main=(int)$arry_result['uid_main'];
                            $arrys_kinship[$inx]=$uid_main;
                        }
                        if(!empty($arrys_kinship)){
                            $kinship ="'";
                            $kinship.=implode("','",$arrys_kinship);
                            $kinship.="'";
                        }
                    }

                //---------------------------------------
                //查找
                //---------------------------------------

                    $f_rec_cno=0;
                    if(!empty($arrys_kinship)){
                        $sql="
                            SELECT
                                COUNT(*) as `f_rec_cno`
                            FROM `mssr_rec_book_cno`
                            WHERE 1=1
                                AND `user_id` IN ({$kinship})
                                #AND `keyin_mdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                            GROUP BY `user_id`
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        if(!empty($arrys_result)){
                            $f_rec_cno=(int)$arrys_result[0]['f_rec_cno'];
                        }
                    }

                    //輸出
                    $arry_output['data_type']=$data_type;
                    $arry_output['class_code']=$class_code;
                    $arry_output['cno']=$f_rec_cno;
                    $arry_output=json_encode($arry_output);
                    die($arry_output);
            break;

            default:
                die();
            break;
        }

        $conn_user=NULL;
        $conn_mssr=NULL;
?>