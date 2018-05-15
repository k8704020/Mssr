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
    //class_code        班級代碼
    //classroom         班級
    //semester_start    學期開始日期
    //semester_end      學期結束日期

        $post_chk=array(
            'class_code    ',
            'classroom     ',
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
    //class_code        班級代碼
    //classroom         班級
    //semester_start    學期開始日期
    //semester_end      學期結束日期

        //POST
        $class_code    =trim($_POST[trim('class_code    ')]);
        $classroom     =trim($_POST[trim('classroom     ')]);
        $semester_start=trim($_POST[trim('semester_start')]);
        $semester_end  =trim($_POST[trim('semester_end  ')]);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //class_code        班級代碼
    //classroom         班級
    //semester_start    學期開始日期
    //semester_end      學期結束日期

        $arry_err=array();

        if($class_code===''){
           $arry_err[]='班級代碼,未輸入!';
        }

        if($classroom===''){
           $arry_err[]='班級,未輸入!';
        }else{
            $classroom=(int)$classroom;
            if($classroom===0){
                $arry_err[]='班級,錯誤!';
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

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //class_code        班級代碼
        //classroom         班級
        //semester_start    學期開始日期
        //semester_end      學期結束日期

            $school_code                         ="";
            $class_code                          =mysql_prep($class_code);
            $classroom                           =(int)$classroom;
            $semester_start                      =mysql_prep($semester_start);
            $semester_end                        =mysql_prep($semester_end);
            $date                                =date("Y-m-d");
            $ch_flag                             ="true";
            $avg_cno                             =5;

            $arrys_class_code                    =array();
            $arrys_users                         =array();
            $arrys_read_group_cno_semester       =array();
            $sort_arrys_read_group_cno_semester  =array();
            $filter_arrys_read_group_cno_semester=array();
            $slice_arrys_read_group_cno_semester =array();
            $avg_arrys_read_group_cno_semester   =array();
            $arrys_output                        =array();

            //-------------------------------------------
            //檢核班級代碼
            //-------------------------------------------

                if($ch_flag==="true"){

                    $sql="
                        SELECT
                            `class`.`class_code`,
                            `class`.`grade`,
                            `semester`.`school_code`
                        FROM `class`
                            INNER JOIN `semester` ON
                            `class`.`semester_code` = `semester`.`semester_code`
                        WHERE 1=1
                            AND `class_code` ='{$class_code }'
                            AND `classroom`  = {$classroom  }
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                    if(empty($arrys_result)){
                        $ch_flag="false";
                    }else{
                        //撈取, 相關資料
                        $grade      =(int)$arrys_result[0]['grade'];
                        $school_code=mysql_prep(trim($arrys_result[0]['school_code']));
                    }
                }

            //-------------------------------------------
            //檢核該班學生的資料
            //-------------------------------------------

                if($ch_flag==="true"){

                    $inx=0;

                    //置換班級名稱
                    $get_class_code_info_single=get_class_code_info_single($conn_user,$school_code,$grade,$classroom,$compile_flag=true,$arry_conn_user);
                    $new_classroom=trim($get_class_code_info_single[0]['classroom']);

                    //回填相關資訊
                    $arrys_users[$inx]['classroom']     =$new_classroom;
                    $arrys_users[$inx]['semester_start']=$semester_start;
                    $arrys_users[$inx]['semester_end']  =$semester_end;

                    $sql="
                        SELECT
                            `uid`
                        FROM `student`
                        WHERE 1=1
                            AND `student`.`class_code`   = '{$class_code}'
                            AND `student`.`start`       <= '{$date}'
                            AND `student`.`end`         >= '{$date}'
                        ORDER BY `student`.`number` ASC
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
                    if(empty($arrys_result)){
                        $ch_flag="false";
                    }else{
                        foreach($arrys_result as $arry_result){
                            $user_id=(int)($arry_result['uid']);
                            $arrys_users[$inx]['user_id'][]=$user_id;
                        }
                    }
                }

            //-------------------------------------------
            //檢核該班學生的閱讀本數資料
            //-------------------------------------------

                if($ch_flag==="true"){
                    if(!empty($arrys_users)){
                        foreach($arrys_users as $inx=>$arry_users){

                            $classroom      =trim($arry_users['classroom']);
                            $semester_start =trim($arry_users['semester_start']);
                            $semester_end   =trim($arry_users['semester_end']);

                            if((isset($arry_users['user_id']))&&(!empty($arry_users['user_id']))){
                                $arry_user =$arry_users['user_id'];
                                foreach($arry_user as $user_id){

                                    $user_id=(int)$user_id;

                                    //本數
                                    $read_group_cno_semester=numrow_book_read_group($conn_mssr,$user_id,'',array('user_id'),$semester_start,$semester_end,$arry_conn_mssr);

                                    //回填相關資訊
                                    $arrys_read_group_cno_semester[$classroom][$user_id]=$read_group_cno_semester;
                                }
                            }else{
                                $ch_flag="false";
                                break;
                            }
                        }
                    }
                }

            //-------------------------------------------
            //檢核並設置該班學生的落後情況
            //-------------------------------------------

                if($ch_flag==="true"){

                    //排序
                    if(!empty($arrys_read_group_cno_semester)){
                        foreach($arrys_read_group_cno_semester as $classroom=>$arry_read_group_cno_semester){
                            $classroom=trim($classroom);
                            //整理並回填
                            asort($arry_read_group_cno_semester);
                            $sort_arrys_read_group_cno_semester[$classroom]=$arry_read_group_cno_semester;
                        }
                    }

                    //取參考值
                    foreach($sort_arrys_read_group_cno_semester as $classroom=>$sort_arry_read_group_cno_semester){
                        //整理並回填
                        $sort_arry_read_group_cno_semester=array_slice($sort_arry_read_group_cno_semester,0,$avg_cno);
                        $slice_arrys_read_group_cno_semester[$classroom]=$sort_arry_read_group_cno_semester;
                    }

                    //算平均
                    foreach($slice_arrys_read_group_cno_semester as $classroom=>$slice_arry_read_group_cno_semester){

                        $array_sum=array_sum($slice_arry_read_group_cno_semester);
                        $array_avg=round($array_sum/$avg_cno,0);

                        //整理並回填
                        $avg_arrys_read_group_cno_semester[$classroom]=$array_avg;
                    }
                }

    //---------------------------------------------------
    //回傳參數
    //---------------------------------------------------

        if($ch_flag==="true"){
            $arrys_output['ch_flag']='true';
            $arrys_output['grade_avg_info']=$avg_arrys_read_group_cno_semester;
        }else{
            $arrys_output['ch_flag']='false';
        }
        die(json_encode($arrys_output));
?>
