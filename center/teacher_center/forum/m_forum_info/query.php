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
        require_once(str_repeat("../",4).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/db/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['center']['teacher_center']){
            $url=str_repeat("../",5).'index.php';
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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_forum_info');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //school_code   學校代號
    //class_code    班級代號
    //type          類型

        //查詢欄位,顯示用
        $query_fields=array();

        //查詢欄位,串接用
        $arr=array();

            //school_code   學校代號
            if(isset($_GET['school_code'])&&(trim($_GET['school_code']!==''))){
                $school_code=trim($_GET['school_code']);
            }else{
                $school_code="";
            }
            $arr['school_code']=array(
                'n'=>'學校代號',    //名稱
                'v'=>$school_code,  //值
                'c'=>'equal'        //類型
            );

            //class_code    班級代號
            if(isset($_GET['class_code'])&&(trim($_GET['class_code']!==''))){
                $class_code=trim($_GET['class_code']);
            }else{
                $class_code="";
            }
            $arr['class_code']=array(
                'n'=>'班級代號',    //名稱
                'v'=>$class_code,   //值
                'c'=>'equal'        //類型
            );

            //type          類型
            if(isset($_GET['type'])&&(trim($_GET['type']!==''))){
                $type=trim($_GET['type']);
            }else{
                $type="";
            }

        if(1==2){//除錯用
            echo "<pre>";
            print_r($arr);
            echo "</pre>";
        }

    //---------------------------------------------------
    //串接查詢欄位
    //---------------------------------------------------

        $arry_query  =mutiple_query($arr);
        $query_fields=$arry_query['query_fields'];
        $query_sql   =$arry_query['query_sql'];

    //---------------------------------------------------
    //儲存條件式
    //---------------------------------------------------
    //$_SESSION['mod_sample']['filter']        查詢條件式
    //$_SESSION['mod_sample']['query_fields']  查詢欄位,顯示用

        $filter='';
        foreach($query_fields as $key=>$val){
            $filter.=" ".$val['sql'];
        }

        $_SESSION['m_forum_info']['query_fields']=$query_fields;
        $_SESSION['m_forum_info']['filter']=$filter;
        if($school_code!=='')$_SESSION['m_forum_info']['school_code']=$school_code;
        $_SESSION['m_forum_info']['class_code']=$class_code;

        if(1==2){//除錯用
            echo $_SESSION['m_forum_info']['filter'].'<p>';
            echo "<pre>";
            print_r($_SESSION['m_forum_info']['query_fields']);
            echo "</pre>";
            die();
        }

    //---------------------------------------------------
    //$_SESSION['tc']['t|dt'] 設置
    //---------------------------------------------------
    //1     校長
    //3     主任
    //5     帶班老師
    //12    行政老師
    //14    主任帶一個班
    //16    主任帶多個班
    //22    老師帶多個班
    //99    管理者

        if(in_array($auth_sys_check_lv,array(99))){
            if($school_code!==''){
                $_SESSION['tc'][0]['school_code']=$school_code;
                $_SESSION['tc']['t|dt']['school_code']=$school_code;
            }
        }else if(in_array($auth_sys_check_lv,array(16,22))){
            foreach($arrys_login_info as $arry_login_info){
                if((isset($arry_login_info['arrys_class_code']))&&(!empty($arry_login_info['arrys_class_code']))){
                    $sess_arrys_class_code=$arry_login_info['arrys_class_code'];
                    foreach($sess_arrys_class_code as $inx=>$sess_arry_class_code){

                        $sess_class_code    =trim($sess_arry_class_code['class_code']);
                        $sess_class_category=(int)$sess_arry_class_code['class_category'];
                        $sess_grade         =(int)$sess_arry_class_code['grade'];
                        $sess_classroom     =trim($sess_arry_class_code['classroom']);
                        $sess_semester_code =trim($sess_arry_class_code['semester_code']);

                        if($sess_class_code===$class_code){
                            unset($_SESSION['tc']['t|dt']['arrys_class_code']);
                            $_SESSION['tc']['t|dt']['arrys_class_code'][0]=array(
                                'class_code'        =>$sess_class_code,
                                'class_category'    =>$sess_class_category,
                                'grade'             =>$sess_grade,
                                'classroom'         =>$sess_classroom,
                                'semester_code'     =>$sess_semester_code
                            );
                        }
                    }
                }
            }
        }

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------
        $url ="";
        $page="";

        if($type==='self'){
            $page=str_repeat("../",0)."index.php";
        }else{
            $page=str_repeat("../",0)."content.php";
        }

        $arg =array();
        $arg =http_build_query($arg);

        if(!empty($arg)){
            $url="{$page}?{$arg}";
        }else{
            $url="{$page}";
        }

        header("Location: {$url}");
        die();
?>