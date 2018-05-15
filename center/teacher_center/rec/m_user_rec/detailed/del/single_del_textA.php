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
        require_once(str_repeat("../",6).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/vaildate/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/fso/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['center']['teacher_center']){
            $url=str_repeat("../",7).'index.php';
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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_rec');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //rec_sid   推薦識別碼
    //no        文字推薦項目

        $get_chk=array(
            'rec_sid',
            'no     '
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
    //rec_sid   推薦識別碼
    //no        文字推薦項目

        //POST
        $rec_sid=trim($_GET[trim('rec_sid')]);
        $no     =trim($_GET[trim('no')]);

        //SESSION
        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);
        $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
        $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
        $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];

        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //rec_sid   推薦識別碼
    //no        文字推薦項目

        $arry_err=array();

        if($rec_sid===''){
           $arry_err[]='推薦識別碼,未輸入!';
        }
        if($no===''){
           $arry_err[]='文字推薦項目,未輸入!';
        }else{
            $no=(int)$no;
            if($no===0){
                $arry_err[]='文字推薦項目,錯誤!';
            }
        }

        if(count($arry_err)!==0){
            if(1==1){//除錯用
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
        //rec_sid   推薦識別碼
        //no        文字推薦項目

            $sess_user_id   =(int)$sess_user_id;
            $sess_grade     =(int)$sess_grade;
            $sess_classroom =(int)$sess_classroom;

            $rec_sid        =mysql_prep($rec_sid);
            $no             =(int)($no);

            //-------------------------------------------
            //檢核
            //-------------------------------------------

                $sql="
                    SELECT `rec_content`
                    FROM  `mssr_rec_book_text_log`
                    WHERE 1=1
                        AND `rec_sid`='{$rec_sid}'
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                $arrys_rs_rec_text_content=array('','','');
                if(!empty($db_results)){
                    //文字內容
                    $rs_rec_text_content=trim($db_results[0]['rec_content']);
                    if(@unserialize($rs_rec_text_content)){
                        $arrys_rs_rec_text_content=@unserialize($rs_rec_text_content);
                    }
                }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            //SESSION
            $sess_user_id  =(int)$sess_user_id;
            $sess_grade    =(int)$sess_grade;
            $sess_classroom=(int)$sess_classroom;

            $rec_sid=mysql_prep($rec_sid);
            $no     =(int)($no);

            $arrys_rs_rec_text_content[$no-1]='';
            $rs_rec_text_content=@serialize($arrys_rs_rec_text_content);

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            $sql="
                # for mssr_rec_book_text
                UPDATE `mssr_rec_book_text` SET
                    `rec_content`='{$rs_rec_text_content}'
                WHERE 1=1
                    AND `rec_sid`='{$rec_sid            }'
                LIMIT 1;

                # for mssr_rec_book_text_log
                UPDATE `mssr_rec_book_text_log` SET
                    `rec_content`='{$rs_rec_text_content}'
                WHERE 1=1
                    AND `rec_sid`='{$rec_sid            }'
                LIMIT 1;
            ";

            //送出
            $err ='DB QUERY FAIL';
            $sth=$conn_mssr->prepare($sql);
            $sth->execute()or die($err);

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------
?>
