<?php
//-------------------------------------------------------
//明日書店網管中心
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
                    APP_ROOT.'center/admin_center/inc/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'inc/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(!login_check(array('a'))){
            $url=str_repeat("../",2).'mod/m_login/loginF.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        //初始化，承接變數
        $_sess_a=$_SESSION['a'];
        foreach($_sess_a as $field_name=>$field_value){
            $$field_name=trim($field_value);
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //grade_id
    //class_id

        //查詢欄位,顯示用
        $query_fields=array();

        //查詢欄位,串接用
        $arr=array();

            //grade
            if(isset($_GET['grade'])&&(trim($_GET['grade']!==''))){
                $grade=trim($_GET['grade']);
            }else{
                $grade="";
            }
            $arr['grade_id']=array(
                'n'=>'',        //名稱
                'v'=>$grade,    //值
                'c'=>'equal'    //類型
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
    //$_SESSION['a']['query']['m_book_borrow_rank_info']['filter']        查詢條件式
    //$_SESSION['a']['query']['m_book_borrow_rank_info']['query_fields']  查詢欄位,顯示用

        $filter='';
        foreach($query_fields as $key=>$val){
            $filter.=" ".$val['sql'];
        }

        $_SESSION['a']['query']['m_book_borrow_rank_info']['query_fields']=$query_fields;
        $_SESSION['a']['query']['m_book_borrow_rank_info']['filter']=$filter;

        if(1==2){//除錯用
            echo $_SESSION['a']['query']['m_book_borrow_rank_info']['filter'].'<p>';
            echo "<pre>";
            print_r($_SESSION['a']['query']['m_book_borrow_rank_info']['query_fields']);
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