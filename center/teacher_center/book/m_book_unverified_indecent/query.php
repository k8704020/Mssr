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
        if(isset($sess_login_info['uid'])){$sess_user_id=(int)$sess_login_info['uid'];}

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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_book_unverified_verified');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //查詢欄位,顯示用
        $query_fields=array();

        //查詢欄位,串接用
        $arr=array();

            //book_name         書籍名稱
            if(isset($_POST['book_name'])&&(trim($_POST['book_name']!==''))){
                $book_name=trim($_POST['book_name']);
            }else{
                $book_name="";
            }
            $arr['book_name']=array(
                'n'=>'書籍名稱',    //名稱
                'v'=>$book_name,    //值
                'c'=>'like'         //類型
            );

            //book_isbn_10           ISBN10碼編號
            if(isset($_POST['book_isbn_10'])&&(trim($_POST['book_isbn_10']!==''))){
                $book_isbn_10=trim($_POST['book_isbn_10']);
            }else{
                $book_isbn_10="";
            }
            $arr['book_isbn_10']=array(
                'n'=>'ISBN10碼編號',    //名稱
                'v'=>$book_isbn_10,     //值
                'c'=>'like'             //類型
            );

            //book_isbn_13           ISBN13碼編號
            if(isset($_POST['book_isbn_13'])&&(trim($_POST['book_isbn_13']!==''))){
                $book_isbn_13=trim($_POST['book_isbn_13']);
            }else{
                $book_isbn_13="";
            }
            $arr['book_isbn_13']=array(
                'n'=>'ISBN13碼編號',    //名稱
                'v'=>$book_isbn_13,     //值
                'c'=>'like'             //類型
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
    //$_SESSION['m_book_unverified_verified']['filter']        查詢條件式
    //$_SESSION['m_book_unverified_verified']['query_fields']  查詢欄位,顯示用

        $filter='';
        foreach($query_fields as $key=>$val){
            $filter.=" ".$val['sql'];
        }

        //keyin_cdate       修改時間
        if(isset($_POST['keyin_cdate'])&&(trim($_POST['keyin_cdate']!==''))&&(((int)$_POST['keyin_cdate']!==0))){
            $year    =(int)date("Y");
            $last_day=(int)date("d", mktime(0, 0, 0, date("m") + 1, 0, date("Y")));
            $keyin_cdate=(int)$_POST['keyin_cdate'];
            $filter.="
                AND `mssr_book_unverified`.`keyin_cdate` BETWEEN '{$year}-0{$keyin_cdate}-01 00:00:00' AND '{$year}-0{$keyin_cdate}-{$last_day} 23:59:59'
            ";
        }

        $_SESSION['m_book_unverified_verified']['query_fields']=$query_fields;
        $_SESSION['m_book_unverified_verified']['filter']=$filter;

        if(1==2){//除錯用
            echo $_SESSION['m_book_unverified_verified']['filter'].'<p>';
            echo "<pre>";
            print_r($_SESSION['m_book_unverified_verified']['query_fields']);
            echo "</pre>";
            die();
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