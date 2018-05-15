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
        require_once(str_repeat("../",5).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/vaildate/code',
                    APP_ROOT.'lib/php/string/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code',
                    APP_ROOT.'lib/php/fso/code',
                    APP_ROOT.'lib/php/upload/file_upload_save/code',
                    APP_ROOT.'lib/php/image/phpthumb/thumb_imagebysize'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['center']['teacher_center']){
            $url=str_repeat("../",6).'index.php';
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

    //---------------------------------------------------
    //班級判斷
    //---------------------------------------------------

        $has_class_code=true;
        $arrys_class_code=$sess_login_info['arrys_class_code'];
        if(count($arrys_class_code)===0){
            $has_class_code=false;
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //book_sid  書籍識別碼

        $post_chk=array(
            'book_sid   '
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
    //book_sid  書籍識別碼

        //POST
        $book_sid        =trim($_POST[trim('book_sid')]);

        //SESSION
        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);

        //有班級才撈取
        if($has_class_code){
            $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
            $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
            $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];
        }

        //分頁
        $psize=(isset($_POST['psize']))?(int)$_POST['psize']:10;
        $pinx =(isset($_POST['pinx']))?(int)$_POST['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //book_sid  書籍識別碼

        $arry_err=array();

        if($book_sid===''){
           $arry_err[]='書籍識別碼,未輸入!';
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
        //book_sid  書籍識別碼

            $book_sid=mysql_prep($book_sid);

            //-------------------------------------------
            //檢核書籍識別碼
            //-------------------------------------------

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

    //---------------------------------------------------
    //查找, 書籍圖片
    //---------------------------------------------------

        $err_msg     ="";   //錯誤訊息
        $jscript_back="";   //返回js
        $allow_exts  ="";   //類型清單陣列
        $allow_mimes ="";   //mime清單陣列
        $allow_size  ="";   //檔案容量上限

        $allow_exts=array(
            "jpg",
            "jpeg"
        );
        $allow_mimes=array(
            "image/jpeg",
            "image/jpg",
            "image/pjpeg"
        );
        $allow_size=array('kb'=>100);

        //判斷有沒有上傳檔案
        if(isset($_FILES["file_front"])&&!empty($_FILES["file_front"])&&$_FILES["file_front"]['error']===0){

            //變數設定
            $File        =$_FILES["file_front"];
            $root        =str_repeat("../",5)."info/book/{$book_sid}/img";

            $path_b      ="{$root}/front/bimg";
            $path_enc_b  =mb_convert_encoding($path_b,$fso_enc,$page_enc);
            $path_s      ="{$root}/front/simg";
            $path_enc_s  =mb_convert_encoding($path_s,$fso_enc,$page_enc);

            //開目錄
            if(!is_dir("{$path_b}")){
                mk_dir("{$path_b}",$mode=0777,$recursive=true,$fso_enc);
            }
            if(!is_dir("{$path_s}")){
                mk_dir("{$path_s}",$mode=0777,$recursive=true,$fso_enc);
            }

            //上傳處理
            $upload=file_upload_save($File,$path_b,$fso_enc,$allow_exts,$allow_mimes,$allow_size,true);

            //成功:儲存的路徑(檔案系統編碼),失敗:false
            if($upload!==false){

                //刪除既有檔案
                @unlink("{$path_b}/1.jpg");

                //更改原檔名
                @rename($upload,"{$path_b}/1.jpg");

                //縮圖
                $thumb_width =160;
                $thumb_height=160;
                thumb_imagebysize("{$path_b}/1.jpg","{$path_s}/1.jpg",$thumb_width,$thumb_height,$type='same');
            }else{
            //上傳失敗

                $err_msg=array(
                    "上傳失敗,可能原因如下,請重新上傳!",
                    "",
                    "1.檔案類型不符合",
                    "2.檔案大小超出限制"
                );
                $err_msg=implode('~',$err_msg);

                $jscript_back="
                    <script>
                        var err_msg='{$err_msg}'.split('~');
                        alert(err_msg.join('\\r\\n'));
                        history.back(-1);
                    </script>
                ";

                die($jscript_back);
            }
        }

        //判斷有沒有上傳檔案
        if(isset($_FILES["file_back"])&&!empty($_FILES["file_back"])&&$_FILES["file_back"]['error']===0){

            //變數設定
            $File        =$_FILES["file_back"];
            $root        =str_repeat("../",5)."info/book/{$book_sid}/img";

            $path_b      ="{$root}/back/bimg";
            $path_enc_b  =mb_convert_encoding($path_b,$fso_enc,$page_enc);
            $path_s      ="{$root}/back/simg";
            $path_enc_s  =mb_convert_encoding($path_s,$fso_enc,$page_enc);

            //開目錄
            if(!is_dir("{$path_b}")){
                mk_dir("{$path_b}",$mode=0777,$recursive=true,$fso_enc);
            }
            if(!is_dir("{$path_s}")){
                mk_dir("{$path_s}",$mode=0777,$recursive=true,$fso_enc);
            }

            //上傳處理
            $upload=file_upload_save($File,$path_b,$fso_enc,$allow_exts,$allow_mimes,$allow_size,true);

            //成功:儲存的路徑(檔案系統編碼),失敗:false
            if($upload!==false){

                //刪除既有檔案
                @unlink("{$path_b}/1.jpg");

                //更改原檔名
                @rename($upload,"{$path_b}/1.jpg");

                //縮圖
                $thumb_width =160;
                $thumb_height=160;
                thumb_imagebysize("{$path_b}/1.jpg","{$path_s}/1.jpg",$thumb_width,$thumb_height,$type='same');
            }else{
            //上傳失敗

                $err_msg=array(
                    "上傳失敗,可能原因如下,請重新上傳!",
                    "",
                    "1.檔案類型不符合",
                    "2.檔案大小超出限制"
                );
                $err_msg=implode('~',$err_msg);

                $jscript_back="
                    <script>
                        var err_msg='{$err_msg}'.split('~');
                        alert(err_msg.join('\\r\\n'));
                        history.back(-1);
                    </script>
                ";

                die($jscript_back);
            }
        }

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page=str_repeat("../",0)."index.php";
        $arg =array(
            'book_sid'=>$book_sid,
            'psize'=>$psize,
            'pinx' =>$pinx
        );
        $arg =http_build_query($arg);

        if(!empty($arg)){
            $url="{$page}?{$arg}";
        }else{
            $url="{$page}";
        }

        header("Location: {$url}");
?>