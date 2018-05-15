<?php
//-------------------------------------------------------
//資料庫連線資訊
//-------------------------------------------------------

    //---------------------------------------------------
    //user部分
    //---------------------------------------------------

        if(!db_config_user($is_online_version)){
            $errmsg ='';
            $errmsg.='載入資料庫連線資訊失敗';
            die("{$errmsg}");
        }else{
            //測試輸出
            if(1==2){
                $out ='db_host_user:'    .db_host_user    .'<br/>';
                $out.='db_name_user:'    .db_name_user    .'<br/>';
                $out.='db_user_user:'    .db_user_user    .'<br/>';
                $out.='db_pass_user:'    .db_pass_user    .'<br/>';
                $out.='db_encode_user:'  .db_encode_user  .'<br/>';
                echo "<Pre>";
                print_r($out);
                echo "</Pre>";

            }
        }

        function db_config_user($is_online_version){
        //-----------------------------------------------
        //資料庫連線資訊設定
        //-----------------------------------------------
        //$is_online_version    線上模式指標,true | false
        //-----------------------------------------------

            $APP_ROOT ='';
            $APP_ROOT.=str_replace(DIRECTORY_SEPARATOR,'/',$_SERVER['DOCUMENT_ROOT']);

            if(file_exists("{$APP_ROOT}/system/server-connection/db_info_user.php")){
                require_once("{$APP_ROOT}/system/server-connection/db_info_user.php");
                $db_host_user=USER_HOST;
                $db_name_user=USER_DB_NAME;
                $db_user_user=USER_USER;
                $db_pass_user=USER_PASSWD;
            }else{
                $db_host_user='localhost';
                $db_name_user='user';
                $db_user_user='mssr_user';
                $db_pass_user='mssr_pwd';
            }

            if($is_online_version===true){
            //線上模式
                $arr=array(
                    'db_host_user'   =>$db_host_user,
                    'db_name_user'   =>$db_name_user,
                    'db_user_user'   =>$db_user_user,
                    'db_pass_user'   =>$db_pass_user,
                    'db_encode_user' =>'UTF8'
                );
            }else{
            //本機模式
                $arr=array(
                    'db_host_user'   =>'localhost',
                    'db_name_user'   =>'user',
                    'db_user_user'   =>'mssr_user',
                    'db_pass_user'   =>'mssr_pwd',
                    'db_encode_user' =>'UTF8'
                );
            }

            foreach($arr as $key=>$val){
                define($key,$val);
            }

            return true;
        }

    //---------------------------------------------------
    //mssr部分
    //---------------------------------------------------

        if(!db_config_mssr($is_online_version)){
            $errmsg ='';
            $errmsg.='載入資料庫連線資訊失敗';
            die("{$errmsg}");
        }else{
            //測試輸出
            if(1==2){
                $out ='db_host_mssr:'    .db_host_mssr    .'<br/>';
                $out.='db_name_mssr:'    .db_name_mssr    .'<br/>';
                $out.='db_user_mssr:'    .db_user_mssr    .'<br/>';
                $out.='db_pass_mssr:'    .db_pass_mssr    .'<br/>';
                $out.='db_encode_mssr:'  .db_encode_mssr  .'<br/>';
                echo "<Pre>";
                print_r($out);
                echo "</Pre>";
            }
        }

        function db_config_mssr($is_online_version){
        //-----------------------------------------------
        //資料庫連線資訊設定
        //-----------------------------------------------
        //$is_online_version    線上模式指標,true | false
        //-----------------------------------------------

            $APP_ROOT ='';
            $APP_ROOT.=str_replace(DIRECTORY_SEPARATOR,'/',$_SERVER['DOCUMENT_ROOT']);

            if(file_exists("{$APP_ROOT}/system/server-connection/db_info_mssr.php")){
                require_once("{$APP_ROOT}/system/server-connection/db_info_mssr.php");
                $db_host_mssr=MSSR_HOST;
                $db_name_mssr=MSSR_DB_NAME;
                $db_user_mssr=MSSR_USER;
                $db_pass_mssr=MSSR_PASSWD;
            }else{
                $db_host_mssr='localhost';
                $db_name_mssr='mssr';
                $db_user_mssr='mssr_user';
                $db_pass_mssr='mssr_pwd';
            }

            if($is_online_version===true){
            //線上模式
                $arr=array(
                    'db_host_mssr'   =>$db_host_mssr,
                    'db_name_mssr'   =>$db_name_mssr,
                    'db_user_mssr'   =>$db_user_mssr,
                    'db_pass_mssr'   =>$db_pass_mssr,
                    'db_encode_mssr' =>'UTF8'
                );
            }else{
            //本機模式
                $arr=array(
                    'db_host_mssr'   =>'localhost',
                    'db_name_mssr'   =>'mssr',
                    'db_user_mssr'   =>'mssr_user',
                    'db_pass_mssr'   =>'mssr_pwd',
                    'db_encode_mssr' =>'UTF8'
                );
            }

            foreach($arr as $key=>$val){
                define($key,$val);
            }

            return true;
        }
?>
