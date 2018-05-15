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
    //semester_start    開始時間
    //semester_end      結束時間
    //data_type         資料條件
    //class_code        班級代號

        $get_chk=array(
            'semester_start ',
            'semester_end   ',
            'data_type      ',
            'class_code     '
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
    //semester_start    開始時間
    //semester_end      結束時間
    //data_type         資料條件
    //class_code        班級代號

        //GET
        $semester_start =trim($_GET[trim('semester_start')]);
        $semester_end   =trim($_GET[trim('semester_end  ')]);
        $data_type      =trim($_GET[trim('data_type     ')]);
        $class_code     =trim($_GET[trim('class_code    ')]);

        if(isset($_GET['semester_code'])){
            $semester_code=trim($_GET[trim('semester_code')]);
        }

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //semester_start    開始時間
    //semester_end      結束時間
    //data_type         資料條件
    //class_code        班級代號

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
        $semester_start =trim($semester_start);

        //本學期結束時間
        $semester_end   =trim($semester_end);

        //建立連線 user
        $conn_user=conn($db_type='mysql',$arry_conn_user);

        //建立連線 mssr
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

    //---------------------------------------------------
    //判斷輸出類型
    //---------------------------------------------------

        switch($data_type){
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
                    $sql="
                        SELECT
                            `t_comment_group`
                        FROM `mssr_cache_user`
                        WHERE 1=1
                            AND `user_id` = {$uid}
                            AND `semester_code` ='{$semester_code}'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    if(!empty($arrys_result)){
                        $t_comment_group=(int)$arrys_result[0]['t_comment_group'];
                    }

                    //輸出
                    $arry_output['data_type']=$data_type;
                    $arry_output['class_code']=$class_code;
                    $arry_output['cno']=$t_comment_group;
                    $arry_output=json_encode($arry_output);
                    die($arry_output);
            break;

            case 's_read_word':
            //-------------------------------------------
            //查找, 學生閱讀登記平均字數
            //-------------------------------------------

                //---------------------------------------
                //查找
                //---------------------------------------

                    $s_read_word=0;
                    $sql="
                        SELECT
                            `s_read_word`
                        FROM `mssr_cache_class_code`
                        WHERE 1=1
                            AND `class_code`   ='{$class_code   }'
                            AND `semester_code`='{$semester_code}'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    if(!empty($arrys_result)){
                        $s_read_word=(int)$arrys_result[0]['s_read_word'];
                    }

                    //輸出
                    $arry_output['data_type']=$data_type;
                    $arry_output['class_code']=$class_code;
                    $arry_output['cno']=$s_read_word;
                    $arry_output=json_encode($arry_output);
                    die($arry_output);
            break;

            default:
                die();
            break;
        }

        //$conn_user=NULL;
        //$conn_mssr=NULL;
?>