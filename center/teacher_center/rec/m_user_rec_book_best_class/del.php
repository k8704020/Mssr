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

                    APP_ROOT.'lib/php/vaildate/code',
                    APP_ROOT.'lib/php/string/code',
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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_book');
        }

    ////---------------------------------------------------
    ////班級判斷
    ////---------------------------------------------------
    //
    //    $has_class_code=true;
    //    $arrys_class_code=$sess_login_info['arrys_class_code'];
    //    if(count($arrys_class_code)===0){
    //        $has_class_code=false;
    //    }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //best_id

        $post_chk=array(
            'best_id      '
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
    //best_id

        //POST
        $best_id=trim($_POST[trim('best_id')]);

        //SESSION
        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);
        $q_class_code    =mysql_prep(trim($_SESSION['m_user_rec_book_best_class']['class_code']));

        //分頁
        $psize=(isset($_POST['psize']))?(int)$_POST['psize']:10;
        $pinx =(isset($_POST['pinx']))?(int)$_POST['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //best_id

        $arry_err=array();

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

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //best_id

            $best_id=(int)$best_id;

            //-------------------------------------------
            //檢核
            //-------------------------------------------

                $sql="
                    SELECT `best_id`
                    FROM `mssr_rec_book_best_class`
                    WHERE 1=1
                        AND `mssr_rec_book_best_class`.`best_id`={$best_id}
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                $numrow=count($arrys_result);
                if($numrow===0){
                    $msg="刪除失敗!";
                    $jscript_back="{$msg}";
                    die($jscript_back);
                }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $best_id     =(int)$best_id;
            $q_class_code=trim($q_class_code);

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            $sql="
                # for mssr_rec_book_best_class
                DELETE FROM `mssr`.`mssr_rec_book_best_class`
                WHERE 1=1
                    AND `mssr_rec_book_best_class`.`best_id`={$best_id}
            ";

            //送出
            $err ='DB QUERY FAIL';
            $sth=$conn_mssr->prepare($sql);
            $sth->execute()or die($err);

        //-----------------------------------------------
        //FTP 登入
        //-----------------------------------------------

            //連接 | 登入 FTP
            if(isset($file_server_enable)&&($file_server_enable)){

                function recursiveDelete($directory){
                    global $ftp_conn;
                    # here we attempt to delete the file/directory
                    if( !(@ftp_rmdir($ftp_conn, $directory) || @ftp_delete($ftp_conn, $directory)) )
                    {
                        # if the attempt to delete fails, get the file listing
                        $filelist = @ftp_nlist($ftp_conn, $directory);

                        # loop through the file list and recursively delete the FILE in the list
                        foreach($filelist as $file)
                        {
                            recursiveDelete($file);
                        }

                        #if the file list is empty, delete the DIRECTORY we passed
                        recursiveDelete($directory);
                    }
                }

                //FTP 路徑
                $ftp_root="public_html/mssr/info/class/{$q_class_code}/rec_book_best/{$best_id}";

                $ftp_conn  =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                $ftp_login =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

                //設定被動模式
                ftp_pasv($ftp_conn,TRUE);

                $ftp_nlist=ftp_nlist($ftp_conn,$ftp_root);

                if(!empty($ftp_nlist)){
                    recursiveDelete($ftp_root);
                    ftp_rmdir($ftp_conn, $ftp_root);
                }
            }

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $msg="刪除成功!";
        $jscript_back="{$msg}";
        die($jscript_back);
?>