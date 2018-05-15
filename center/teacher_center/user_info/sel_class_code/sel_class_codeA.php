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
    //接收參數
    //---------------------------------------------------
    //class_code        班級代號
    //class_category    班級類型
    //grade             年級
    //classroom         班級
    //semester_code     學期代號

        $get_chk=array(
            'class_code    ',
            'class_category',
            'grade         ',
            'classroom     ',
            'semester_code '
        );

        $get_chk=array_map("trim",$get_chk);
        foreach($get_chk as $get){
            if(!isset($_GET[$get])){
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //class_code        班級代號
    //class_category    班級類型
    //grade             年級
    //classroom         班級
    //semester_code     學期代號

        //GET
        $class_code    =trim($_GET[trim('class_code    ')]);
        $class_category=trim($_GET[trim('class_category')]);
        $grade         =trim($_GET[trim('grade         ')]);
        $classroom     =trim($_GET[trim('classroom     ')]);
        $semester_code =trim($_GET[trim('semester_code ')]);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //class_code        班級代號
    //class_category    班級類型
    //grade             年級
    //classroom         班級
    //semester_code     學期代號

        $arry_err=array();

        if($class_code===''){
           $arry_err[]='班級代號,未輸入!';
        }

        if($class_category===''){
           $arry_err[]='班級類型,未輸入!';
        }else{
            $class_category=(int)$class_category;
            if($class_category===0){
                $arry_err[]='班級類型,錯誤!';
            }
        }

        if($grade===''){
           $arry_err[]='年級,未輸入!';
        }else{
            $grade=(int)$grade;
            if($grade===0){
                $arry_err[]='年級,錯誤!';
            }
        }

        if($classroom===''){
           $arry_err[]='班級,未輸入!';
        }else{
            $classroom=(int)$classroom;
            if($classroom===0){
                $arry_err[]='班級,錯誤!';
            }
        }

        if($semester_code===''){
           $arry_err[]='學期代號,未輸入!';
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

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //class_code        班級代號
        //class_category    班級類型
        //grade             年級
        //classroom         班級
        //semester_code     學期代號

            $class_code     =mysql_prep($class_code);
            $class_category =(int)$class_category;
            $grade          =(int)$grade;
            $classroom      =(int)$classroom;
            $semester_code  =mysql_prep($semester_code);

            //初始化, 身份參考陣列, 1(校長使用者) | 2(主任使用者) | 3(老師使用者)
            $arrys_identity=array(1,2,3);

            //切換指標
            $class_code_flag=false;

            foreach($arrys_identity as $responsibilities){
                $responsibilities=(int)$responsibilities;
                if(isset($arrys_login_info[$responsibilities])){
                    foreach($arrys_login_info[$responsibilities]['arrys_class_code'] as $inx=>$login_info){
                        if((in_array($class_code,$login_info))&&(in_array($class_category,$login_info)&&(in_array($grade,$login_info))&&(in_array($classroom,$login_info))&&in_array($semester_code,$login_info))){
                            $class_code_flag=true;
                        }
                    }
                }
            }

            if($class_code_flag){
                unset($_SESSION['tc']['t|dt']['arrys_class_code']);
                $_SESSION['tc']['t|dt']['arrys_class_code'][0]=array(
                    'class_code'        =>$class_code,
                    'class_category'    =>$class_category,
                    'grade'             =>$grade,
                    'classroom'         =>$classroom,
                    'semester_code'     =>$semester_code
                );
            }

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $respones=json_encode(array(
            'msg'=>'班級更換成功!'  //訊息
        ));
        die($respones);
?>