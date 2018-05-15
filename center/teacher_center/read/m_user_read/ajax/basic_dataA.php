<?php
//-------------------------------------------------------
//教師中心
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",5).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['center']['teacher_center']){
            $url=str_repeat("../",6).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        $arrys_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
        if(empty($arrys_login_info)){
            die();
        }

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

        if(in_array('read_the_registration_code',$config_arrys['user_area'])){
        //清空閱讀登記條碼版登入資訊

            $_SESSION['config']['user_tbl']=array();
            $_SESSION['config']['user_type']='';
            $_SESSION['config']['user_lv']=0;
            if(in_array('read_the_registration_code',$_SESSION['config']['user_area'])){
                foreach($_SESSION['config']['user_area'] as $inx=>$area){
                    if(trim($area)==='read_the_registration_code'){
                        unset($_SESSION['config']['user_area'][$inx]);
                    }
                }
            }
        }

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        $sess_login_info=(isset($_SESSION['tc']['t|dt']))?$_SESSION['tc']['t|dt']:array();

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

        if(!empty($sess_login_info)){
            if(!auth_check($db_type='mysql',$arry_conn_user,$sess_login_info['permission'],$auth_type='mssr_tc')){
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }
        }else{
            //權限指標
            $auth_flag=false;
            foreach($arrys_login_info as $inx=>$arry_login_info){
                if(auth_check($db_type='mysql',$arry_conn_user,$arry_login_info['permission'],$auth_type='mssr_tc'))$auth_flag=true;
            }
            if(!$auth_flag){
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }
        }

    //---------------------------------------------------
    //管理者判斷
    //---------------------------------------------------

        if(!empty($sess_login_info)){
            $is_admin=is_admin(trim($sess_login_info['permission']));
            if($is_admin){
                $sess_login_info['responsibilities']=99;
            }
        }

    //---------------------------------------------------
    //系統權限判斷
    //---------------------------------------------------
    //1     校長
    //3     主任
    //5     帶班老師
    //12    行政老師
    //14    主任帶一個班
    //16    主任帶多個班
    //22    老師帶多個班
    //99    管理者

        if(!empty($sess_login_info)){
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_read');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //read_type         閱讀類型
    //user_id           使用者主索引
    //semester_start    學期開始日期
    //semester_end      學期結束日期

        $post_chk=array(
            'read_type     ',
            'user_id       ',
            'semester_start',
            'semester_end  '
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
    //read_type         閱讀類型
    //user_id           使用者主索引
    //semester_start    學期開始日期
    //semester_end      學期結束日期

        //POST
        $read_type     =trim($_POST[trim('read_type     ')]);
        $user_id       =trim($_POST[trim('user_id       ')]);
        $semester_start=trim($_POST[trim('semester_start')]);
        $semester_end  =trim($_POST[trim('semester_end  ')]);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //read_type         閱讀類型
    //user_id           使用者主索引
    //semester_start    學期開始日期
    //semester_end      學期結束日期

        $arry_err=array();

        if($read_type===''){
           $arry_err[]='閱讀類型,未輸入!';
        }else{
            $read_type=trim($read_type);
            if(!in_array($read_type,array('group','frequency'))){
                $arry_err[]='閱讀類型,錯誤!';
            }
        }

        if($user_id===''){
           $arry_err[]='使用者主索引,未輸入!';
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $arry_err[]='使用者主索引,錯誤!';
            }
        }

        if($semester_start===''){
           $arry_err[]='學期開始日期,未輸入!';
        }

        if($semester_end===''){
           $arry_err[]='學期結束日期,未輸入!';
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
        //檢核
        //-----------------------------------------------
        //read_type         閱讀類型
        //user_id           使用者主索引
        //semester_start    學期開始日期
        //semester_end      學期結束日期

            $read_type      =mysql_prep($read_type     );
            $user_id        =(int)$user_id ;
            $semester_start =mysql_prep($semester_start);
            $semester_end   =mysql_prep($semester_end  );
            $arry_output    =array();

            //是否為當學期
            $is_now_semester    =false;
            $now_time           =(double)time();
            $semester_start_time=(double)strtotime($semester_start);
            $semester_end_time  =(double)strtotime($semester_end);
            if(($semester_start_time<=$now_time)&&($semester_end_time>=$now_time))$is_now_semester=true;

            $query_table="";
            if($is_now_semester){
                $query_table="mssr_book_borrow_semester";
            }else{
                $query_table="mssr_book_borrow_log";
            }

            //-------------------------------------------
            //檢核
            //-------------------------------------------

                switch($read_type){

                    case 'group':
                        $sql="
                            SELECT COUNT(*) AS `cno`
                            FROM(
                                SELECT `user_id`
                                FROM `{$query_table}`
                                WHERE 1=1
                                    AND `user_id`={$user_id}
                                    AND `borrow_sdate` BETWEEN '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
                                GROUP BY `user_id`, `book_sid`
                            ) AS `sqry`
                            WHERE 1=1
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        $arry_output['read_type']='group';
                        $arry_output['cno']=0;
                        if(!empty($arrys_result)){
                            $arry_output['cno']=$arrys_result[0]['cno'];
                        }
                    break;

                    case 'frequency':
                        $sql="
                            SELECT COUNT(*) AS `cno`
                            FROM(
                                SELECT `user_id`
                                FROM `{$query_table}`
                                WHERE 1=1
                                    AND `user_id`={$user_id}
                                    AND `borrow_sdate` BETWEEN '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
                            ) AS `sqry`
                            WHERE 1=1
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        $arry_output['read_type']='frequency';
                        $arry_output['cno']=0;
                        if(!empty($arrys_result)){
                            $arry_output['cno']=$arrys_result[0]['cno'];
                        }
                    break;
                }

    //---------------------------------------------------
    //回傳參數
    //---------------------------------------------------

        die(json_encode($arry_output));
?>