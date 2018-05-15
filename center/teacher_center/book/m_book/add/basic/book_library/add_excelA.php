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
        require_once(str_repeat("../",7).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/fso/code',
                    APP_ROOT.'lib/php/array/code',
                    APP_ROOT.'lib/php/string/code',
                    APP_ROOT.'lib/php/plugin/code',
                    APP_ROOT.'lib/php/vaildate/code',
                    APP_ROOT.'lib/php/upload/file_upload_save/code',
                    APP_ROOT.'lib/php/plugin/func/php_excel/PHPExcel/IOFactory'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

        //調配
        set_time_limit(0);
        ini_set( 'memory_limit', '3072M' );

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['center']['teacher_center']){
            $url=str_repeat("../",8).'index.php';
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

        if(!empty($sess_login_info)){
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_book');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //回傳訊息
        $arrys_ouput=array();
        $arrys_ouput['flag']        ='';
        $arrys_ouput['extension']   ='';
        $arrys_ouput['succes_cno']  =0;
        $arrys_ouput['error_cno']   =0;
        $arrys_ouput['msg']         ='';

        //SESSION
        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);

        if(!isset($sess_login_info['arrys_class_code'])||(empty($sess_login_info['arrys_class_code']))||!is_array($sess_login_info['arrys_class_code'])){
            $sess_grade      =(int)0;
            $sess_classroom  =(int)0;
        }else{
            $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
            $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
            $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];
        }

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

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
    //上傳處理
    //---------------------------------------------------

        $file_element_name='file';  //檔案物件名稱

        $allow_exts ="";            //類型清單陣列
        $allow_mimes="";            //mime清單陣列
        $allow_size ="";            //檔案容量上限

        $allow_exts=array(
            trim("xls"),
            trim("xlsx ")
        );
        $allow_mimes=array(
            trim("application/vnd.ms-excel"),
            trim("application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"),
            trim("application/kset")
        );
        $allow_size=array('kb'=>3000);

        //判斷有沒有上傳檔案
        if(isset($_FILES[$file_element_name])&&!empty($_FILES[$file_element_name])&&$_FILES[$file_element_name]['error']===0){

            //變數設定
            $File       =$_FILES[$file_element_name];
            $name       =$File['name'];
            $type	    =$File['type'];
            $info       =pathinfo($name);
            $extension  =(isset($info['extension']))?$info['extension']:'xls';

            //資料夾建立設置
            $root=str_repeat("../",7)."info/excel_file/mssr_book_library/{$sess_user_id}";
            $path="{$root}/file_upload";
            $arrys_path=array(
                "{$root}"               =>mb_convert_encoding("{$root}",$fso_enc,$page_enc),
                "{$path}"               =>mb_convert_encoding("{$path}",$fso_enc,$page_enc),
                "{$root}/file_download" =>mb_convert_encoding("{$root}/file_download",$fso_enc,$page_enc)
            );
            foreach($arrys_path as $c_path=>$c_path_enc){
                if(!file_exists($c_path_enc)){
                    mk_dir($c_path,$mode=0777,$recursive=true,$fso_enc);
                }
            }

            //溢位判斷
            if(!fso_isunder($root,$path,$fso_enc)){
                $arrys_ouput['flag']='false';
                $arrys_ouput['msg'] ='上傳失敗,溢位.請重新上傳!';
                echo json_encode($arrys_ouput,true);
                die();
            }

            //移除舊有檔案
            if(file_exists("{$path}/upload.xls")){
                @unlink("{$path}/upload.xls");
            }
            if(file_exists("{$path}/upload.xlsx")){
                @unlink("{$path}/upload.xlsx");
            }

            //上傳處理,成功:儲存的路徑(檔案系統編碼),失敗:false
            $upload_file=file_upload_save($File,$path,$fso_enc,$allow_exts,$allow_mimes,$allow_size,true);

            if($upload_file!==false){
            //上傳處理,成功
                //更改原檔名
                @rename($upload_file,"{$path}/upload.{$extension}");
                $arrys_ouput['extension']=$extension;
            }else{
            //上傳處理,失敗
                $arrys_ouput['flag']='false';
                $arrys_ouput['msg'] ='上傳失敗,可能原因如下: 1.檔案類型不符合 2.檔案大小超出限制';
                echo json_encode($arrys_ouput,true);
                die();
            }
        }else{
        //沒有上傳檔案
            $arrys_ouput['flag']='false';
            $arrys_ouput['msg'] ='沒有上傳檔案或檔案類型不符!';
            echo json_encode($arrys_ouput,true);
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

            //-------------------------------------------
            //檢核, 學校類別
            //-------------------------------------------

                $sess_school_code=mysql_prep($sess_school_code);

                $query_sql="
                    SELECT
                        `school_category`
                    FROM `school`
                    WHERE 1=1
                        AND `school_code`='{$sess_school_code}'
                    LIMIT 1;
                ";
                $err='QUERY FAIL';
                $result=$conn_user->query($query_sql) or die($err);
                $rowcount=$result->rowCount();
                if($rowcount!==0){
                    while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                        $school_category=(int)$arry_row['school_category'];
                    }
                }else{
                    $arrys_ouput['flag']='false';
                    $arrys_ouput['msg'] ='學校類型錯誤!';
                    echo json_encode($arrys_ouput,true);
                    die();
                }

    //---------------------------------------------------
    //讀檔&處理
    //---------------------------------------------------

        //初始化, 讀取結果陣列
        $arrys_success_info     =array();
        $arrys_error_info       =array();
        $tmp_arrys_info         =array();
        $tmp_book_library_code  =array();

        //檔案路徑
        $file_path="{$path}/upload.{$extension}";
        if(!file_exists($file_path)){
            $arrys_ouput['flag']='false';
            $arrys_ouput['msg'] ='沒有上傳檔案或檔案類型不符!';
            echo json_encode($arrys_ouput,true);
            die();
        }

        //由PHPExcel決定載入檔案的種類，不硬性設定讀取的檔案種類，但將檔案設定為唯讀屬性
        $reader=PHPExcel_IOFactory::createReaderForFile($file_path);
        $reader->setReadDataOnly(true);
        $excel =$reader->load($file_path);

        //取得第一個工作表
        $sheet =$excel->getActiveSheet(0);

        //取得總列數
        $highestRow=(int)$sheet->getHighestRow();
        if($highestRow>5000){
            $arrys_ouput['flag']='false';
            $arrys_ouput['msg'] ='單檔筆數最大值限制為5千筆!';
            echo json_encode($arrys_ouput,true);
            die();
        }

        //取得驗證碼
        $verify=$sheet->getCell('Z1')->getValue();
        if(trim($verify)!=='verify_mssr_book_library_excel_import'){
            $arrys_ouput['flag']='false';
            $arrys_ouput['msg'] ='請使用制式表格填寫!';
            echo json_encode($arrys_ouput,true);
            die();
        }

        //讀取內容
        for($row=2;$row<=$highestRow;$row++){

            $val_flag=true;

            for($column=0;$column<=9;$column++){

                $column=(int)$column;
                $val=$sheet->getCellByColumnAndRow($column,$row)->getValue();
                if($column===0)$err_msg='';
                if($column===0)$tmp_arrys_info=array();

                //ISBN預設值
                if($column===2){
                    $book_isbn_10=(strip_tags(trim($val)));
                    $_lv=0; //錯誤指標
                    if(trim($val)!==''){
                        if((!preg_match("/^[A-Za-z0-9]{10}+$/",$val))){
                            $val_flag=false;
                            $err_msg.='ISBN10碼錯誤, ';
                        }else{
                            $book_isbn_10=(strip_tags(trim($val)));
                            //轉換ISBN10碼
                            $_book_code=$book_isbn_10;
                            $ch_isbn_10=ch_isbn_10($_book_code, $convert=false);
                            $ch_isbn_13=ch_isbn_13($_book_code, $convert=false);
                            if(isset($ch_isbn_10['error'])){
                                $_lv=$_lv+1;
                            }
                            if(isset($ch_isbn_13['error'])){
                                $_lv=$_lv+3;
                            }
                            switch($_lv){
                                case 1:
                                //10碼錯誤，利用13碼轉換更新
                                    $book_isbn_10=isbn_13_to_10($_book_code);
                                    $book_isbn_13=$_book_code;
                                break;

                                case 3:
                                //13碼錯誤，利用10碼轉換更新
                                    $book_isbn_10=$_book_code;
                                    $book_isbn_13=isbn_10_to_13($_book_code);
                                break;

                                case 4:
                                    $val_flag=false;
                                    $err_msg.='ISBN10碼錯誤, ';
                                    $book_isbn_10=$val;
                                break;
                            }
                        }
                    }else{
                        if((($sheet->getCellByColumnAndRow(5,$row)->getValue())!=='')&&(preg_match("/^[A-Za-z0-9]{13}+$/",$sheet->getCellByColumnAndRow(5,$row)->getValue()))){
                            $book_isbn_13=(strip_tags(trim($sheet->getCellByColumnAndRow(5,$row)->getValue())));
                            //轉換ISBN13碼
                            $_book_code=$book_isbn_13;
                            $ch_isbn_10=ch_isbn_10($_book_code, $convert=false);
                            $ch_isbn_13=ch_isbn_13($_book_code, $convert=false);
                            if(isset($ch_isbn_10['error'])){
                                $_lv=$_lv+1;
                            }
                            if(isset($ch_isbn_13['error'])){
                                $_lv=$_lv+3;
                            }
                            switch($_lv){
                                case 1:
                                //10碼錯誤，利用13碼轉換更新
                                    $book_isbn_10=isbn_13_to_10($_book_code);
                                    $book_isbn_13=$_book_code;
                                break;

                                case 3:
                                //13碼錯誤，利用10碼轉換更新
                                    $book_isbn_10=$_book_code;
                                    $book_isbn_13=isbn_10_to_13($_book_code);
                                break;

                                case 4:
                                    $val_flag=false;
                                    $err_msg.='ISBN13碼錯誤, ';
                                    $book_isbn_13=$sheet->getCellByColumnAndRow(5,$row)->getValue();
                                break;
                            }
                        }
                    }
                }

                if($column===3){
                    if($_lv!==3){
                        $book_isbn_13=(strip_tags(trim($val)));
                        $_lv=0; //錯誤指標
                        if(trim($val)!==''){
                            if((!preg_match("/^[A-Za-z0-9]{13}+$/",$val))){
                                $val_flag=false;
                                $err_msg.='ISBN13碼錯誤, ';
                            }else{
                                $book_isbn_13=(strip_tags(trim($val)));
                                //轉換ISBN13碼
                                $_book_code=$book_isbn_13;
                                $ch_isbn_10=ch_isbn_10($_book_code, $convert=false);
                                $ch_isbn_13=ch_isbn_13($_book_code, $convert=false);
                                if(isset($ch_isbn_10['error'])){
                                    $_lv=$_lv+1;
                                }
                                if(isset($ch_isbn_13['error'])){
                                    $_lv=$_lv+3;
                                }
                                switch($_lv){
                                    case 1:
                                    //10碼錯誤，利用13碼轉換更新
                                        $book_isbn_10=isbn_13_to_10($_book_code);
                                        $book_isbn_13=$_book_code;
                                    break;

                                    case 3:
                                    //13碼錯誤，利用10碼轉換更新
                                        $book_isbn_10=$_book_code;
                                        $book_isbn_13=isbn_10_to_13($_book_code);
                                    break;

                                    case 4:
                                        $val_flag=false;
                                        $err_msg.='ISBN13碼錯誤, ';
                                        $book_isbn_13=$val;
                                    break;
                                }
                            }
                        }
                    }
                }

                //圖書館條碼
                if($column===0){
                    $book_library_code=(strip_tags(trim($val)));
                    if(trim($val)!==''){
                        if((!preg_match("/^[A-Za-z0-9.]{1,20}+$/",$val))){
                            $val_flag=false;
                            $err_msg.='圖書館條碼錯誤, ';
                        }else{
                            $book_library_code=(strip_tags(trim($val)));
                        }
                    }else{
                        $val_flag=false;
                        $err_msg.='圖書館條碼未輸入, ';
                    }
                }

                //書名
                if($column===1){
                    $book_name=(strip_tags(trim($val)));
                    if(strip_tags(trim($val))!==''){
                        if(mb_strlen($val)>50){
                            $book_name=((trim($val)));
                            $val_flag=false;
                            $err_msg.='書名長度只能為 1~50 碼, ';
                        }else{
                            $book_name=(strip_tags(trim($val)));
                        }
                    }else{
                        $book_name=((trim($val)));
                        $val_flag=false;
                        $err_msg.='書名未輸入或包含非法字元, ';
                    }
                }

                //作者
                if($column===4){
                    $book_author=(strip_tags(trim($val)));
                    if(trim($val)!==''){
                        if(mb_strlen($val)>50){
                            $val_flag=false;
                            $err_msg.='作者長度只能為 1~50 碼, ';
                        }else{
                            $book_author=(strip_tags(trim($val)));
                        }
                    }
                }

                //出版社
                if($column===5){
                    $book_publisher=(strip_tags(trim($val)));
                    if(trim($val)!==''){
                        if(mb_strlen($val)>30){
                            $val_flag=false;
                            $err_msg.='出版社長度只能為 1~30 碼, ';
                        }else{
                            $book_publisher=(strip_tags(trim($val)));
                        }
                    }
                }

                //頁數
                if($column===6){
                    $book_page_count=(strip_tags(trim($val)));
                    if((!is_numeric($val))&&(trim($val)!=='')){
                        $val_flag=false;
                        $err_msg.='頁數只能輸入整數, ';
                    }else{
                        $book_page_count=(int)$val;
                    }
                }

                //字數
                if($column===7){
                    $book_word=(strip_tags(trim($val)));
                    if((!is_numeric($val))&&(trim($val)!=='')){
                        $val_flag=false;
                        $err_msg.='字數只能輸入整數, ';
                    }else{
                        $book_word=(int)$val;
                    }
                }

                //有無注音(0:無 , 1:有)
                if($column===8){
                    $book_phonetic=(strip_tags(trim($val)));
                    if((!is_numeric($val))&&(trim($val)!=='')){
                        $val_flag=false;
                        $err_msg.='注音格式為(0:無 , 1:有), ';
                    }else{
                        if(in_array((int)$val,array(0,1))){
                            $book_phonetic=(int)$val;
                            switch($book_phonetic){
                                case 0:
                                    $book_phonetic='無';
                                break;

                                case 1:
                                    $book_phonetic='有';
                                break;

                                default:
                                    $val_flag=false;
                                    $err_msg.='注音格式為(0:無 , 1:有), ';
                                break;
                            }
                        }else{
                            $val_flag=false;
                            $err_msg.='注音格式為(0:無 , 1:有), ';
                        }
                    }
                }

                //中國圖書分類號
                if($column===9){
                    $value=(int)$val;
                    $book_ch_no=(strip_tags(trim($value)));
                    if(trim($value)!==''){
                        if(mb_strlen($value)>10){
                            $val_flag=false;
                            $err_msg.='中國圖書分類號長度只能為 1~10 碼, ';
                        }else{
                            $book_ch_no=(strip_tags(trim($value)));
                        }
                    }
                }

                //---------------------------------------
                //檢核圖書館條碼
                //---------------------------------------

                    if($column===0){
                        if($book_library_code!==''){
                            $book_library_code=mysql_prep(strip_tags(trim($val)));
                            $query_sql="
                                SELECT
                                    `book_library_code`
                                FROM `mssr_book_library`
                                WHERE 1=1
                                    AND `school_code`       ='{$sess_school_code }'
                                    AND `book_library_code` ='{$book_library_code}'
                                LIMIT 1;
                            ";
                            $err='QUERY FAIL';
                            $result=$conn_mssr->query($query_sql) or die($err);
                            $rowcount=$result->rowCount();
                            if($rowcount!==0){
                                //$val_flag=false;
                                //$err_msg.='圖書館條碼重複, ';
                            }elseif(in_array($book_library_code,$tmp_book_library_code)){
                                $val_flag=false;
                                $err_msg.='圖書館條碼重複, ';
                            }else{
                                $tmp_book_library_code[]=$book_library_code;
                            }
                        }
                    }

                //---------------------------------------
                //暫存結果
                //---------------------------------------

                    if($column===0)$tmp_arrys_info[]=$book_library_code;
                    if($column===1)$tmp_arrys_info[]=$book_name;
                    if($column===2)$tmp_arrys_info[]=$book_isbn_10;
                    if($column===3)$tmp_arrys_info[]=$book_isbn_13;
                    if($column===4)$tmp_arrys_info[]=$book_author;
                    if($column===5)$tmp_arrys_info[]=$book_publisher;
                    if($column===6)$tmp_arrys_info[]=$book_page_count;
                    if($column===7)$tmp_arrys_info[]=$book_word;
                    if($column===8)$tmp_arrys_info[]=$book_phonetic;
                    if($column===9)$tmp_arrys_info[]=$book_ch_no;
                    if($column===9)$tmp_arrys_info[]=$err_msg;
            }
            //-------------------------------------------
            //彙整結果
            //-------------------------------------------

                if($val_flag){
                //成功資料
                    $arrys_success_info[]=$tmp_arrys_info;
                }else{
                //失敗資料
                    $arrys_error_info[]=$tmp_arrys_info;
                }
        }

    //---------------------------------------------------
    //SQL處理
    //---------------------------------------------------

        //-----------------------------------------------
        //預設值&處理
        //-----------------------------------------------

            $create_by=mb_substr(strtotime(date("Y-m-d H:i:s")),-3,3);
            $edit_by  =mb_substr(strtotime(date("Y-m-d H:i:s")),-3,3);
            if(!empty($arrys_success_info)){
                foreach($arrys_success_info as $arry_success_info){
                    $create_by          =(int)$create_by+1;
                    $edit_by            =(int)$edit_by+1;
                    $sess_school_code   =mysql_prep(strip_tags(trim($sess_school_code)));
                    $school_category    =(int)$school_category;
                    $grade_id           =(int)$sess_grade;
                    $classroom_id       =(int)$sess_classroom;
                    $book_id            ="NULL";
                    $book_sid_library   =book_library_sid($create_by,mb_internal_encoding());
                    $book_sid_global    =book_global_sid($create_by,mb_internal_encoding());
                    $book_no            =(int)1;
                    $book_note          ='';
                    $keyin_cdate        ="NOW()";
                    $keyin_mdate        ="NULL";
                    $keyin_ip           =get_ip();
                    $book_library_code  =mysql_prep(strip_tags(trim($arry_success_info[0])));
                    $book_name          =mysql_prep(strip_tags(trim($arry_success_info[1])));
                    $book_isbn_10       =mysql_prep(strip_tags(trim($arry_success_info[2])));
                    $book_isbn_13       =mysql_prep(strip_tags(trim($arry_success_info[3])));
                    $book_author        =mysql_prep(strip_tags(trim($arry_success_info[4])));
                    $book_publisher     =mysql_prep(strip_tags(trim($arry_success_info[5])));
                    $book_page_count    =(int)$arry_success_info[6];
                    $book_word          =(int)$arry_success_info[7];
                    $book_phonetic      =mysql_prep(strip_tags(trim($arry_success_info[8])));
                    $book_ch_no         =mysql_prep(strip_tags(trim($arry_success_info[9])));

                    $sql="
                        # for mssr_book_library
                        INSERT IGNORE INTO `mssr`.`mssr_book_library` SET
                            `create_by`         =  {$create_by          } ,
                            `edit_by`           =  {$edit_by            } ,
                            `school_code`       = '{$sess_school_code   }',
                            `school_category`   =  {$school_category    } ,
                            `grade_id`          =  {$grade_id           } ,
                            `classroom_id`      =  {$classroom_id       } ,
                            `book_id`           =  {$book_id            } ,
                            `book_sid`          = '{$book_sid_library   }',
                            `book_isbn_10`      = '{$book_isbn_10       }',
                            `book_isbn_13`      = '{$book_isbn_13       }',
                            `book_library_code` = '{$book_library_code  }',
                            `book_no`           =  {$book_no            } ,
                            `book_name`         = '{$book_name          }',
                            `book_author`       = '{$book_author        }',
                            `book_publisher`    = '{$book_publisher     }',
                            `book_page_count`   =  {$book_page_count    } ,
                            `book_word`         =  {$book_word          } ,
                            `book_note`         = '{$book_note          }',
                            `book_phonetic`     = '{$book_phonetic      }',
                            `keyin_cdate`       =  {$keyin_cdate        } ,
                            `keyin_mdate`       =  {$keyin_mdate        } ,
                            `keyin_ip`          = '{$keyin_ip           }';

                        UPDATE `mssr`.`mssr_book_library` SET
                            `book_name`             ='{$book_name           }',
                            `book_author`           ='{$book_author         }',
                            `book_publisher`        = '{$book_publisher     }',
                            `book_isbn_10`          = '{$book_isbn_10       }',
                            `book_isbn_13`          = '{$book_isbn_13       }'
                        WHERE 1=1
                            AND `school_code`       ='{$sess_school_code    }'
                            AND `book_library_code` ='{$book_library_code   }'
                        LIMIT 1;
                    ";
                    if(trim($book_ch_no)!==''){
                        $sql.="
                            # for mssr_book_ch_no
                            INSERT IGNORE INTO `mssr`.`mssr_book_ch_no` SET
                                `book_sid`      = '{$book_sid_library   }',
                                `book_ch_no`    = '{$book_ch_no         }';
                        ";
                    }
                    if(trim($book_isbn_10)!=='' || trim($book_isbn_13)!==''){
                        $sql.="
                            # for mssr_book_global
                            INSERT IGNORE INTO `mssr_book_global` SET
                                `create_by`         =  {$create_by          } ,
                                `edit_by`           =  {$edit_by            } ,
                                `book_id`           =  {$book_id            } ,
                                `book_sid`          = '{$book_sid_global    }',
                                `book_isbn_10`      = '{$book_isbn_10       }',
                                `book_isbn_13`      = '{$book_isbn_13       }',
                                `book_name`         = '{$book_name          }',
                                `book_author`       = '{$book_author        }',
                                `book_publisher`    = '{$book_publisher     }',
                                `book_page_count`   =  {$book_page_count    } ,
                                `book_word`         =  {$book_word          } ,
                                `book_note`         = '{$book_note          }',
                                `book_phonetic`     = '{$book_phonetic      }',
                                `keyin_cdate`       =  {$keyin_cdate        } ,
                                `keyin_mdate`       =  {$keyin_mdate        } ,
                                `keyin_ip`          = '{$keyin_ip           }';
                        ";
                    }
                    try{
                        //送出
                        $conn_mssr->exec($sql);
                    }catch(Exception $e){
                        //echo "<Pre>";
                        //print_r($sql);
                        //echo "</Pre>";
                    }
                }
            }

    //---------------------------------------------------
    //寫檔
    //---------------------------------------------------

        //Excel標題資訊
        $arry_excel_title_info=array(
            'A'=>'A1',
            'B'=>'B1',
            'C'=>'C1',
            'D'=>'D1',
            'E'=>'E1',
            'F'=>'F1',
            'G'=>'G1',
            'H'=>'H1',
            'I'=>'I1',
            'J'=>'J1',
            'K'=>'K1'
        );

        //-----------------------------------------------
        //失敗檔案
        //-----------------------------------------------

            if(!empty($arrys_error_info)){
                //初始化, Excel物件
                $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
                $cacheSettings = array('memoryCacheSize'=>'8MB');
                PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
                $obj_error_excel=new PHPExcel();
                $obj_error_excel->setActiveSheetIndex(0);

                //設置並隱藏驗證碼
                $obj_error_excel->getActiveSheet(0)->getColumnDimension('Z')->setVisible(false);
                $obj_error_excel->getActiveSheet(0)->setCellValue('Z1','verify_mssr_book_library_excel_import');

                //設定標題背景顏色單色
                $obj_error_excel->getActiveSheet(0)->getStyle('A1:B1')->applyFromArray(
                    array('fill'=>array(
                            'type' =>PHPExcel_Style_Fill::FILL_SOLID,
                            'color'=>array('rgb'=>'f89748')
                        ),
                        'alignment' => array(
                            'wrap'      =>true,
                            'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical'  =>PHPExcel_Style_Alignment::VERTICAL_CENTER
                        )
                    )
                );
                $obj_error_excel->getActiveSheet(0)->getStyle('C1:J1')->applyFromArray(
                    array('fill'=>array(
                            'type' =>PHPExcel_Style_Fill::FILL_SOLID,
                            'color'=>array('rgb'=>'8ed247')
                        ),
                        'alignment' => array(
                            'wrap'      =>true,
                            'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical'  =>PHPExcel_Style_Alignment::VERTICAL_CENTER
                        )
                    )
                );
                $obj_error_excel->getActiveSheet(0)->getStyle('K1')->applyFromArray(
                    array('fill'=>array(
                            'type' =>PHPExcel_Style_Fill::FILL_SOLID,
                            'color'=>array('rgb'=>'ff0066')
                        ),
                        'alignment' => array(
                            'wrap'      =>true,
                            'vertical'  =>PHPExcel_Style_Alignment::VERTICAL_CENTER
                        )
                    )
                );

                //設置標題欄位資訊
                foreach($arry_excel_title_info as $key=>$val){
                    //欄位寬度
                    if($key==='A')$obj_error_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(20);
                    if($key==='B')$obj_error_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='C')$obj_error_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='D')$obj_error_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='E')$obj_error_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='F')$obj_error_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='G')$obj_error_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(10);
                    if($key==='H')$obj_error_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(10);
                    if($key==='I')$obj_error_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(25);
                    if($key==='J')$obj_error_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(25);
                    if($key==='K')$obj_error_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(150);

                    //欄位值
                    if($val==='A1')$obj_error_excel->getActiveSheet(0)->setCellValue($val,'圖書館條碼(唯一)');
                    if($val==='B1')$obj_error_excel->getActiveSheet(0)->setCellValue($val,'書名');
                    if($val==='C1')$obj_error_excel->getActiveSheet(0)->setCellValue($val,'ISBN10碼(請盡可能填寫)');
                    if($val==='D1')$obj_error_excel->getActiveSheet(0)->setCellValue($val,'ISBN13碼(請盡可能填寫)');
                    if($val==='E1')$obj_error_excel->getActiveSheet(0)->setCellValue($val,'作者');
                    if($val==='F1')$obj_error_excel->getActiveSheet(0)->setCellValue($val,'出版社');
                    if($val==='G1')$obj_error_excel->getActiveSheet(0)->setCellValue($val,'頁數');
                    if($val==='H1')$obj_error_excel->getActiveSheet(0)->setCellValue($val,'字數');
                    if($val==='I1')$obj_error_excel->getActiveSheet(0)->setCellValue($val,'有無注音(0:無 , 1:有)');
                    if($val==='J1')$obj_error_excel->getActiveSheet(0)->setCellValue($val,'中國圖書分類號');
                    if($val==='K1')$obj_error_excel->getActiveSheet(0)->setCellValue($val,'錯誤提示(僅供提示，修正資料後，可不理會此項欄位直接上傳即可)');
                }

                //設置內容資訊
                foreach($arrys_error_info as $rs_row=>$arry_error_info){
                    $rs_row=$rs_row+2;
                    foreach($arry_error_info as $rs_cell=>$val){
                        //欄位值, 上下左右置中
                        switch($rs_cell){
                            case 0:
                                $rs_book_library_code=trim($arry_error_info[$rs_cell]);
                                $obj_error_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_library_code}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 1:
                                $rs_book_name=trim($arry_error_info[$rs_cell]);
                                $obj_error_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_name}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 2:
                                $rs_book_isbn_10=trim($arry_error_info[$rs_cell]);
                                $obj_error_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_isbn_10}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 3:
                                $rs_book_isbn_13=trim($arry_error_info[$rs_cell]);
                                $obj_error_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_isbn_13}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 4:
                                $rs_book_author=trim($arry_error_info[$rs_cell]);
                                $obj_error_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_author}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 5:
                                $rs_book_publisher=trim($arry_error_info[$rs_cell]);
                                $obj_error_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_publisher}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 6:
                                $rs_book_page_count=trim($arry_error_info[$rs_cell]);
                                $obj_error_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_page_count}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 7:
                                $rs_book_word=trim($arry_error_info[$rs_cell]);
                                $obj_error_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_word}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 8:
                                $rs_book_phonetic=trim($arry_error_info[$rs_cell]);
                                switch($rs_book_phonetic){
                                    case '無':
                                        $rs_book_phonetic=(int)0;
                                    break;
                                    case '有':
                                        $rs_book_phonetic=(int)1;
                                    break;
                                    default:
                                        $rs_book_phonetic=trim($arry_error_info[$rs_cell]);
                                    break;
                                }
                                $obj_error_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_phonetic}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 9:
                                $rs_book_ch_no=trim($arry_error_info[$rs_cell]);
                                $obj_error_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_ch_no}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 10:
                                $rs_err_msg=trim($arry_error_info[$rs_cell]);
                                $obj_error_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_err_msg}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_error_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                        }
                    }
                }
            }

        //-----------------------------------------------
        //成功檔案
        //-----------------------------------------------

            if(!empty($arrys_success_info)){
                //初始化, Excel物件
                $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
                $cacheSettings = array('memoryCacheSize'=>'8MB');
                PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
                $obj_success_excel=new PHPExcel();
                $obj_success_excel->setActiveSheetIndex(0);

                //設定標題背景顏色單色
                $obj_success_excel->getActiveSheet(0)->getStyle('A1:B1')->applyFromArray(
                    array('fill'=>array(
                            'type' =>PHPExcel_Style_Fill::FILL_SOLID,
                            'color'=>array('rgb'=>'f89748')
                        ),
                        'alignment' => array(
                            'wrap'      =>true,
                            'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical'  =>PHPExcel_Style_Alignment::VERTICAL_CENTER
                        )
                    )
                );
                $obj_success_excel->getActiveSheet(0)->getStyle('C1:J1')->applyFromArray(
                    array('fill'=>array(
                            'type' =>PHPExcel_Style_Fill::FILL_SOLID,
                            'color'=>array('rgb'=>'8ed247')
                        ),
                        'alignment' => array(
                            'wrap'      =>true,
                            'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical'  =>PHPExcel_Style_Alignment::VERTICAL_CENTER
                        )
                    )
                );

                //設置標題欄位資訊
                foreach($arry_excel_title_info as $key=>$val){
                    //欄位寬度
                    if($key==='A')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(20);
                    if($key==='B')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='C')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='D')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='E')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='F')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='G')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(10);
                    if($key==='H')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(10);
                    if($key==='I')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(25);
                    if($key==='J')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(25);

                    //欄位值
                    if($val==='A1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,'圖書館條碼(唯一)');
                    if($val==='B1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,'書名');
                    if($val==='C1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,'ISBN10碼(請盡可能填寫)');
                    if($val==='D1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,'ISBN13碼(請盡可能填寫)');
                    if($val==='E1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,'作者');
                    if($val==='F1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,'出版社');
                    if($val==='G1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,'頁數');
                    if($val==='H1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,'字數');
                    if($val==='I1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,'有無注音(0:無 , 1:有)');
                    if($val==='J1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,'中國圖書分類號');
                }

                //設置內容資訊
                foreach($arrys_success_info as $rs_row=>$arry_success_info){
                    $rs_row=$rs_row+2;
                    foreach($arry_success_info as $rs_cell=>$val){
                        //欄位值, 上下左右置中
                        switch($rs_cell){
                            case 0:
                                $rs_book_library_code=trim($arry_success_info[$rs_cell]);
                                $obj_success_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_library_code}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 1:
                                $rs_book_name=trim($arry_success_info[$rs_cell]);
                                $obj_success_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_name}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 2:
                                $rs_book_isbn_10=trim($arry_success_info[$rs_cell]);
                                $obj_success_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_isbn_10}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 3:
                                $rs_book_isbn_13=trim($arry_success_info[$rs_cell]);
                                $obj_success_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_isbn_13}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 4:
                                $rs_book_author=trim($arry_success_info[$rs_cell]);
                                $obj_success_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_author}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 5:
                                $rs_book_publisher=trim($arry_success_info[$rs_cell]);
                                $obj_success_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_publisher}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 6:
                                $rs_book_page_count=trim($arry_success_info[$rs_cell]);
                                $obj_success_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_page_count}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 7:
                                $rs_book_word=trim($arry_success_info[$rs_cell]);
                                $obj_success_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_word}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 8:
                                $rs_book_phonetic=trim($arry_success_info[$rs_cell]);
                                switch($rs_book_phonetic){
                                    case '無':
                                        $rs_book_phonetic=(int)0;
                                    break;
                                    case '有':
                                        $rs_book_phonetic=(int)1;
                                    break;
                                    default:
                                        $rs_book_phonetic=trim($arry_success_info[$rs_cell]);
                                    break;
                                }
                                $obj_success_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_phonetic}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                            case 9:
                                $rs_book_ch_no=trim($arry_success_info[$rs_cell]);
                                $obj_success_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$rs_book_ch_no}",PHPExcel_Cell_DataType::TYPE_STRING);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            break;
                        }
                    }
                }
            }

    //---------------------------------------------------
    //匯出
    //---------------------------------------------------
    //Successful_books_list
    //Error_books_list

        //-----------------------------------------------
        //移除舊檔
        //-----------------------------------------------

            $arry_save_excel_info=array(
                trim('Successful_books_list'),
                trim('Error_books_list     ')
            );
            foreach($arry_save_excel_info as $excel_neme){

                $excel_neme=trim($excel_neme);

                //執行
                if(file_exists("{$root}/file_download/{$excel_neme}.xls")){
                    @unlink("{$root}/file_download/{$excel_neme}.xls");
                }
                if(file_exists("{$root}/file_download/{$excel_neme}.xlsx")){
                    @unlink("{$root}/file_download/{$excel_neme}.xlsx");
                }
            }

        //-----------------------------------------------
        //儲存新檔
        //-----------------------------------------------

            switch($extension){
                case 'xls':
                    if(!empty($arrys_error_info)){
                        $obj_error_writer=PHPExcel_IOFactory::createWriter($obj_error_excel,'Excel5');
                    }
                    if(!empty($arrys_success_info)){
                        $obj_success_writer=PHPExcel_IOFactory::createWriter($obj_success_excel,'Excel5');
                    }
                break;

                case 'xlsx':
                    if(!empty($arrys_error_info)){
                        $obj_error_writer=PHPExcel_IOFactory::createWriter($obj_error_excel,'Excel2007');
                    }
                    if(!empty($arrys_success_info)){
                        $obj_success_writer=PHPExcel_IOFactory::createWriter($obj_success_excel,'Excel2007');

                    }
                break;
            }

            //執行
            if(!empty($arrys_error_info)){
                $obj_error_writer->setPreCalculateFormulas(false);
                $obj_error_writer->save("{$root}/file_download/Error_books_list.{$extension}");
            }
            if(!empty($arrys_success_info)){
                $obj_success_writer->setPreCalculateFormulas(false);
                $obj_success_writer->save("{$root}/file_download/Successful_books_list.{$extension}");
            }

    //---------------------------------------------------
    //結果回傳
    //---------------------------------------------------

        $arrys_ouput['flag']        ='true';
        $arrys_ouput['extension']   =$extension;
        $arrys_ouput['succes_cno']  =(int)count($arrys_success_info);
        $arrys_ouput['error_cno']   =(int)count($arrys_error_info);
        $arrys_ouput['msg']         ='';
        echo json_encode($arrys_ouput,true);
        die();
?>