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
                    APP_ROOT.'lib/php/fso/code'
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
    //系統權限判斷
    //---------------------------------------------------
    //1     校長
    //3     主任
    //5     帶班老師
    //12    行政老師
    //14    主任帶一個班
    //16    主任帶多個班
    //22    老師帶多個班

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

        $get_chk=array(
            'book_sid   '
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
    //book_sid  書籍識別碼

        //GET
        $book_sid        =trim($_GET[trim('book_sid')]);

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
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
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

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //book_sid  書籍識別碼

            $book_sid=mysql_prep($book_sid);
            $find_book_flag=false;

            //-------------------------------------------
            //檢核書籍識別碼
            //-------------------------------------------

                if(!$find_book_flag){
                    $sql="
                        SELECT
                            `book_isbn_10`,
                            `book_isbn_13`
                        FROM `mssr_book_library`
                        WHERE 1=1
                            AND `book_sid`='{$book_sid}'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    if(!empty($arrys_result)){
                        $book_isbn_10=trim($arrys_result[0]['book_isbn_10']);
                        $book_isbn_13=trim($arrys_result[0]['book_isbn_13']);
                        $find_book_flag=true;
                    }
                }

                if(!$find_book_flag){
                    $sql="
                        SELECT
                            `book_isbn_10`,
                            `book_isbn_13`
                        FROM `mssr_book_class`
                        WHERE 1=1
                            AND `book_sid`='{$book_sid}'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    if(!empty($arrys_result)){
                        $book_isbn_10=trim($arrys_result[0]['book_isbn_10']);
                        $book_isbn_13=trim($arrys_result[0]['book_isbn_13']);
                        $find_book_flag=true;
                    }
                }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

    //---------------------------------------------------
    //查找, 書籍圖片
    //---------------------------------------------------

        //curl一般設置
        $_timeout=15;
        $_curlopt_useragent="Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1)";

        //圖片類型允許值
        $allow_exts=array(
            trim(".jpeg"),
            trim(".jpg ")
        );
        $allow_mimes=array(
            trim("image/jpeg "),
            trim("image/jpg  "),
            trim("image/pjpeg")
        );

        //圖片路徑設定
        $_root=str_repeat("../",5)."info/book/{$book_sid}/img/front/simg/";
        $_img_name="";
        $_img_name.="1.jpg";
        $_img_path="{$_root}{$_img_name}";
        $_img_path_enc=mb_convert_encoding($_img_path,$fso_enc,$page_enc);

        //開目錄
        if(!is_dir("{$_root}")){
            mk_dir("{$_root}",$mode=0777,$recursive=true,$fso_enc);
        }

        //找尋10碼圖片
        $_curl=curl_init();
        $_url="http://static.findbook.tw/image/book/{$book_isbn_10}/large";
        find_book_fbk_img($_curl,$_url,$_timeout,$_curlopt_useragent,$allow_exts,$allow_mimes,$page_enc,$fso_enc,$_img_path,$_img_path_enc);

        //找尋13碼圖片
        $_url="http://static.findbook.tw/image/book/{$book_isbn_13}/large";
        find_book_fbk_img($_curl,$_url,$_timeout,$_curlopt_useragent,$allow_exts,$allow_mimes,$page_enc,$fso_enc,$_img_path,$_img_path_enc);
        curl_close($_curl);

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