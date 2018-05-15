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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_book');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //book_code         書籍編號
    //book_name         書籍名稱
    //book_no           書籍序號
    //book_author       作者
    //book_publisher    出版社
    //book_phonetic     有無注音
    //borrow_state      借閱狀態
    //keyin_mdate       修改時間

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

            //book_no           書籍序號
            if(isset($_POST['book_no'])&&(trim($_POST['book_no']!==''))){
                $book_no=trim($_POST['book_no']);
            }else{
                $book_no="";
            }
            $arr['book_no']=array(
                'n'=>'書籍序號',    //名稱
                'v'=>$book_no,      //值
                'c'=>'like'         //類型
            );

            //book_author       作者
            if(isset($_POST['book_author'])&&(trim($_POST['book_author']!==''))){
                $book_author=trim($_POST['book_author']);
            }else{
                $book_author="";
            }
            $arr['book_author']=array(
                'n'=>'作者',            //名稱
                'v'=>$book_author,      //值
                'c'=>'like'             //類型
            );

            //book_publisher    出版社
            if(isset($_POST['book_publisher'])&&(trim($_POST['book_publisher']!==''))){
                $book_publisher=trim($_POST['book_publisher']);
            }else{
                $book_publisher="";
            }
            $arr['book_publisher']=array(
                'n'=>'出版社',          //名稱
                'v'=>$book_publisher,   //值
                'c'=>'like'             //類型
            );

            //book_phonetic     有無注音
            if(isset($_POST['book_phonetic'])&&(trim($_POST['book_phonetic']!==''))){
                $book_phonetic=trim($_POST['book_phonetic']);
            }else{
                $book_phonetic="";
            }
            $arr['book_phonetic']=array(
                'n'=>'有無注音',        //名稱
                'v'=>$book_phonetic,    //值
                'c'=>'like'             //類型
            );

            //borrow_state      借閱狀態
            if(isset($_POST['borrow_state'])&&(trim($_POST['borrow_state']!==''))&&(((int)$_POST['borrow_state']===0))&&(trim($_POST['borrow_state']!=='借閱中'))){
                $borrow_state=(int)$_POST['borrow_state'];
            }else{
                $borrow_state="";
            }
            $arr['borrow_state']=array(
                'n'=>'借閱狀態',        //名稱
                'v'=>$borrow_state,     //值
                'c'=>'equal'            //類型
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
    //$_SESSION['m_book']['filter']        查詢條件式
    //$_SESSION['m_book']['query_fields']  查詢欄位,顯示用

        $filter='';
        foreach($query_fields as $key=>$val){
            $filter.=" ".$val['sql'];
        }

        //borrow_state      借閱狀態
        if(isset($_POST['borrow_state'])&&(trim($_POST['borrow_state']==='借閱中'))){
            $filter.="AND borrow_state <>'0'";
        }

        //book_ISBN         ISBN編號
        if(isset($_POST['book_ISBN'])&&(trim($_POST['book_ISBN']!==''))){
            $book_ISBN=trim($_POST['book_ISBN']);
            $filter.="
                AND (
                    `book_isbn_10`      LIKE '{$book_ISBN}%'
                        OR
                    `book_isbn_13`      LIKE '{$book_ISBN}%'
                )
            ";
        }

        //book_library      登錄號
        if(isset($_POST['book_library'])&&(trim($_POST['book_library']!==''))){
            $book_library=trim($_POST['book_library']);
            $filter.="
                AND `book_library_code` = '{$book_library}'
            ";
        }

        //keyin_mdate       修改時間
        if(isset($_POST['keyin_mdate'])&&(trim($_POST['keyin_mdate']!==''))&&(((int)$_POST['keyin_mdate']!==0))){
            $year    =(int)date("Y");
            $last_day=(int)date("d", mktime(0, 0, 0, date("m") + 1, 0, date("Y")));
            $keyin_mdate=(int)$_POST['keyin_mdate'];
            $filter.="
                AND `keyin_mdate` BETWEEN '{$year}-0{$keyin_mdate}-01 00:00:00' AND '{$year}-0{$keyin_mdate}-{$last_day} 23:59:59'
            ";
        }

        //adscription      書籍歸屬類型
        if(isset($_GET['adscription'])&&(trim($_GET['adscription']==='self'))){
            $filter.="AND `create_by` ={$sess_user_id}";
            $_SESSION['m_book']['adscription']='self';
        }else{
            $_SESSION['m_book']['adscription']='school';
        }

        $_SESSION['m_book']['query_fields']=$query_fields;
        $_SESSION['m_book']['filter']=$filter;

        if(1==2){//除錯用
            echo $_SESSION['m_book']['filter'].'<p>';
            echo "<pre>";
            print_r($_SESSION['m_book']['query_fields']);
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