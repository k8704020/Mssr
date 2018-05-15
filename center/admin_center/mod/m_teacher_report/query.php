<?php
//-------------------------------------------------------
//學校人員專區
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",2).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        //初始化，承接變數
        $arrys_sess_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
        if(empty($arrys_sess_login_info)){
            $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
            $jscript_back="
                <script>
                    alert('{$msg}');
                    parent.location.href='../../index.php';
                </script>
            ";
            die($jscript_back);
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

        //SESSION
        if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
        if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
        if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
        if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
        if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
        if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
        if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
        if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
        if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
        if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
        if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
            $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
            foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
            }
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //class_code    班級代號
    //type          查詢類型
    //uid           使用者主索引
    //name          名稱
    //sex           性別
    //account       帳戶

        //查詢欄位,顯示用
        $query_fields=array();

        //查詢欄位,串接用
        $arr=array();

            //class_code    班級代號
            if(isset($_GET['class_code'])&&(trim($_GET['class_code']!==''))){
                $class_code=trim($_GET['class_code']);
            }else{
                $class_code='';
            }

            //type          查詢類型
            if(isset($_GET['type'])&&(trim($_GET['type']!==''))){
                $type=trim($_GET['type']);
            }else{
                $type='';
            }

            //uid           使用者主索引
            if(isset($_POST['uid'])&&(trim($_POST['uid']!==''))){
                $uid=trim($_POST['uid']);
            }else{
                $uid="";
            }
            $arr['uid']=array(
                'n'=>'使用者主索引',//名稱
                'v'=>$uid,          //值
                'c'=>'equal'        //類型
            );

            //name          名稱
            if(isset($_POST['name'])&&(trim($_POST['name']!==''))){
                $name=trim($_POST['name']);
            }else{
                $name="";
            }
            $arr['name']=array(
                'n'=>'名稱',        //名稱
                'v'=>$name,         //值
                'c'=>'like'         //類型
            );

            //sex           性別
            if(isset($_POST['sex'])&&(trim($_POST['sex']!==''))){
                $sex=trim($_POST['sex']);
            }else{
                $sex="";
            }
            $arr['sex']=array(
                'n'=>'性別',        //名稱
                'v'=>$sex,          //值
                'c'=>'equal'        //類型
            );

            //account       帳戶
            if(isset($_POST['account'])&&(trim($_POST['account']!==''))){
                $account=trim($_POST['account']);
            }else{
                $account="";
            }
            $arr['account']=array(
                'n'=>'帳戶',        //名稱
                'v'=>$account,      //值
                'c'=>'like'         //類型
            );

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
    //$_SESSION['sha']['query']['m_user_view']['filter']        查詢條件式
    //$_SESSION['sha']['query']['m_user_view']['query_fields']  查詢欄位,顯示用

        $filter='';
        foreach($query_fields as $key=>$val){
            $filter.=" ".$val['sql'];
        }

        $_SESSION['sha']['query']['m_user_view']['query_fields']=$query_fields;
        $_SESSION['sha']['query']['m_user_view']['filter']=$filter;

        if(1==2){//除錯用
            echo $_SESSION['sha']['query']['m_user_view']['filter'].'<p>';
            echo "<pre>";
            print_r($_SESSION['sha']['query']['m_user_view']['query_fields']);
            echo "</pre>";
            die();
        }

        if($type!==''){
            $_SESSION['sha']['query']['m_user_view']['q_mode']=$type;
            $_SESSION['sha']['query']['m_user_view']['q_class_code']=$class_code;
        }

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page="";

        $page=str_repeat("../",0)."index.php";
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