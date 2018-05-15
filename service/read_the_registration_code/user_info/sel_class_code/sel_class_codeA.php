<?php
//-------------------------------------------------------
//閱讀登記條碼版
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
                    APP_ROOT.'service/read_the_registration_code/inc/code',

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

        if($config_arrys['is_offline']['service']['read_the_registration_code']){
            $url=str_repeat("../",5).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(!login_check(array('t'))){
            $url=str_repeat("../",2).'login/loginF.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        //初始化，承接變數
        $_sess_t=$_SESSION['t'];
        foreach($_sess_t as $field_name=>$field_value){
            $$field_name=$field_value;
        }

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

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

        //SESSION
        $sess_uid=(int)$_sess_t['uid'];

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

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

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

            $sess_uid=(int)$sess_uid;


            $sql="
                SELECT
                    `class_code`,
                    `class_category`,
                    `grade`,
                    `classroom`,
                    `semester_code`
                FROM `class`
                WHERE 1=1
                    AND `class_code`    ='{$class_code      }'
                    AND `class_category`= {$class_category  }
                    AND `grade`         = {$grade           }
                    AND `classroom`     = {$classroom       }
                    AND `semester_code` ='{$semester_code   }'
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);

            if(!empty($arrys_result)){
                //SESSION, 欄位值
                $arry_result=$arrys_result[0];
                foreach($arry_result as $field_name=>$field_value){
                    $field_value=trim($field_value);
                    $$field_name=$field_value;

                    $_SESSION['t'][$field_name]=$field_value;
                }
            }

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page=str_repeat("../",2)."index.php";
        $arg =array();
        $arg =http_build_query($arg);

        if(!empty($arg)){
            $url ="{$page}?{$arg}";
        }else{
            $url ="{$page}";
        }

        header("Location: {$page}");
        die();
?>