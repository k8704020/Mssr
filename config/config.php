<?php
//-------------------------------------------------------
//通用設定主檔
//-------------------------------------------------------

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        @session_start();

    //---------------------------------------------------
    //PHP.ini設定
    //---------------------------------------------------

        //線上模式指標,true=線上 | false=本機
        $is_online_version=true;

        if($is_online_version===true){
        //線上

            //錯誤等級(關閉所有錯誤回報)
            error_reporting(0);

            //上傳設定
            ini_set('file_uploads',1);
            ini_set('memory_limit','128M');
            ini_set('post_max_size','8M');
            ini_set('upload_max_filesize','2M');
            ini_set('max_input_time','60');
            ini_set('max_execution_time','30');
        }else{
        //本機

            //錯誤等級(顯示所有錯誤回報)
            error_reporting(E_ALL);

            //上傳設定
            ini_set('file_uploads',1);
            ini_set('memory_limit','128M');
            ini_set('post_max_size','8M');
            ini_set('upload_max_filesize','2M');
            ini_set('max_input_time','60');
            ini_set('max_execution_time','30');

            //郵件設定(本機發信)
            ini_set('SMTP','msa.hinet.net');
            ini_set('smtp_port','25');
            ini_set('sendmail_from','test@ncu.cot.org.tw/ac/');
        }

    //---------------------------------------------------
    //常數|通用變數
    //---------------------------------------------------

        //通用
        define('TZone','Asia/Taipei');      //時區
        define('Encode','UTF-8');           //編碼
        define('Charset','UTF-8');          //頁面顯示語系設定
        define('Content_Language','UTF-8'); //頁面顯示語系設定

        //主機網址
        $HOST_ROOT=($is_online_version===true)?'http://www.cot.org.tw/mssr/':'http://localhost/new_cl_ncu/mssr/';
        define('HOST_ROOT',$HOST_ROOT);

        //系統根目錄路徑
        $APP_ROOT ='';
        $APP_ROOT.=str_replace(DIRECTORY_SEPARATOR,'/',$_SERVER['DOCUMENT_ROOT']);
        $APP_ROOT.=($is_online_version===true)?'/mssr/':'/new_cl_ncu/mssr/';
        define('APP_ROOT',$APP_ROOT);

        //手稿資訊
        $FOLDER_SELF=explode('/',dirname($_SERVER['PHP_SELF']));
        $FOLDER_SELF=$FOLDER_SELF[count($FOLDER_SELF)-1];   //手稿所在目錄
        $PAGE_SELF  =basename($_SERVER['PHP_SELF']);        //手稿名稱

        //特殊字元
        $nl ="\r\n";
        $tab="\t";

    //---------------------------------------------------
    //通用設定
    //---------------------------------------------------

        //文字內部編碼
        mb_internal_encoding(Encode);

        //時區
        date_default_timezone_set(TZone);

        //設定頁面語系
        header('Content-Type: text/html; charset='.Charset);

        //檔案系統語系編碼,本機檔案系統BIG5,線上主機檔案系統UTF-8
        $fso_enc=($is_online_version===true)?'UTF-8':'BIG5';

        //頁面編碼
        $page_enc=mb_internal_encoding();

        //FILE SERVER 啟用設定
        $file_server_enable=true;

        //FTP資訊
        if($file_server_enable){
            $arry_ftp1_info=array(
                trim('host      ')=>trim('140.115.16.105' ),
                trim('port      ')=>trim('21'             ),
                trim('account   ')=>trim('webadmin'       ),
                trim('password  ')=>trim('n15-c03-u19'    )
            );
        }

        //-----------------------------------------------
        //通用設定陣列
        //-----------------------------------------------

            //通用設定陣列
            $config_arrys=array();

            //-------------------------------------------
            //清單設定
            //-------------------------------------------

                //---------------------------------------
                //前台service清單
                //---------------------------------------
                //bookstore                         明日書店
                //pptv                              畢寶德
                //read_the_registration             閱讀登記
                //read_the_registration_code        閱讀登記條碼版

                    //$config_arrys['service_list']=array(
                    //    trim('bookstore                 ')=>trim('明日書店      '),
                    //    trim('pptv                      ')=>trim('畢寶德        '),
                    //    trim('read_the_registration     ')=>trim('閱讀登記      '),
                    //    trim('read_the_registration_code')=>trim('閱讀登記條碼版')
                    //);

                //---------------------------------------
                //後台center清單
                //---------------------------------------
                //admin_center                      網管中心
                //teacher_center                    教師中心
                //association_member_center         協會成員中心

                    //$config_arrys['center_list']=array(
                    //    trim('admin_center              ')=>trim('網管中心      '),
                    //    trim('teacher_center            ')=>trim('教師中心      '),
                    //    trim('association_member_center ')=>trim('協會成員中心  ')
                    //);

            //-------------------------------------------
            //維護模式指標,true=啟用 | false=停用
            //-------------------------------------------

                //---------------------------------------
                //前台service部分
                //---------------------------------------

                    $config_arrys['is_offline']['service']=array(
                        trim('bookstore                 ')=>false,
                        trim('pptv                      ')=>false,
                        trim('read_the_registration     ')=>false,
                        trim('read_the_registration_code')=>false
                    );

                //---------------------------------------
                //後台center部分
                //---------------------------------------

                    $config_arrys['is_offline']['center']=array(
                        trim('admin_center              ')=>false,
                        trim('teacher_center            ')=>false,
                        trim('association_member_center ')=>false
                    );

            //-------------------------------------------
            //服務別設定
            //-------------------------------------------

                $config_arrys['service']['bookstore']['rec_reason']=array(
                    trim('內容很有趣        '),
                    trim('封面畫插圖很好看  '),
                    trim('內容輕鬆好讀      '),
                    trim('內容很感人        '),
                    trim('喜歡故事人物      '),
                    trim('可以學到很多知識  '),
                    trim('印象深刻          ')
                );

            //-------------------------------------------
            //中心設定
            //-------------------------------------------

                //---------------------------------------
                //header設置
                //---------------------------------------

                    //$config_arrys['center']['teacher_center']['report']['header_right']['sys_arry']=array(
                    //    'teacher_institute' =>array(
                    //
                    //    ),
                    //    'teacher_gallery'   =>array(
                    //
                    //    ),
                    //    'teacher_management'=>array(
                    //        trim('user          '),
                    //        trim('book          '),
                    //        trim('user_info     ')
                    //    ),
                    //    'learning_process'  =>array(
                    //        trim('mssr          '),
                    //        trim('ac_type       '),
                    //        trim('draw_story    '),
                    //        trim('mssc          '),
                    //        trim('math          ')
                    //        //trim('pt            ')
                    //    )
                    //);
                    //
                    //$config_arrys['center']['teacher_center']['report']['header_right']['name_arry']=array(
                    //    trim('learning_process      ')=>trim('學習歷程      '),
                    //        trim('mssr              ')=>trim('明日書店      '),
                    //        trim('ac_type           ')=>trim('寵物打字      '),
                    //        trim('draw_story        ')=>trim('塗鴉寫作      '),
                    //        trim('mssc              ')=>trim('明日創作      '),
                    //        trim('math              ')=>trim('數學島        '),
                    //        //trim('pt                ')=>trim('數學解說員    '),
                    //
                    //    trim('teacher_management    ')=>trim('教師管理      '),
                    //        trim('user              ')=>trim('學生管理      '),
                    //        trim('book              ')=>trim('書籍管理      '),
                    //        trim('user_info         ')=>trim('教師專用功能  '),
                    //
                    //    trim('teacher_gallery       ')=>trim('教師百寶箱    '),
                    //
                    //    trim('teacher_institute     ')=>trim('夫子學院      ')
                    //);
                    //
                    //$config_arrys['center']['teacher_center']['report']['header_right']['target_arry']=array(
                    //    trim('learning_process      ')=>trim('_self'),
                    //        trim('mssr              ')=>trim('_self'),
                    //        trim('ac_type           ')=>trim('_self'),
                    //        trim('draw_story        ')=>trim('_self'),
                    //        trim('mssc              ')=>trim('_self'),
                    //        trim('math              ')=>trim('_self'),
                    //        //trim('pt                ')=>trim('_self'),
                    //
                    //    trim('teacher_management    ')=>trim('_self'),
                    //        trim('user              ')=>trim('_self'),
                    //        trim('book              ')=>trim('_self'),
                    //        trim('user_info         ')=>trim('_self'),
                    //
                    //    trim('teacher_gallery       ')=>trim('_self'),
                    //
                    //    trim('teacher_institute     ')=>trim('_self')
                    //);
                    //
                    //$config_arrys['center']['teacher_center']['report']['header_right']['url_arry']=array(
                    //    trim('learning_process      ')=>trim('/ta/learning_process.php              '),
                    //        trim('mssr              ')=>trim('#'),
                    //        trim('ac_type           ')=>trim('/ta/ta_learn/ac_type/ac_type.php      '),
                    //        trim('draw_story        ')=>trim('/ta/ta_learn/draw_story/draw_story.php'),
                    //        trim('mssc              ')=>trim("http://140.115.135.107/ac/user/ta_setcookie.php?unique_id=".@$_SESSION["uid"]."&identity=".@$_SESSION["identity"]."&name=".@$_SESSION["name"]."&school_id=".@$_SESSION["school"]),
                    //        trim('math              ')=>trim('/ta/ta_learn/math/mi.php              '),
                    //        //trim('pt                ')=>trim('/ta/ta_learn/pt/pt.php                '),
                    //
                    //    trim('teacher_management    ')=>trim('/ta/teacher_manage.php                '),
                    //        trim('user              ')=>trim('/ta/tm_mssr_info_control.php          '),
                    //        trim('book              ')=>trim('/ta/tm_mssr_teacherbook.php           '),
                    //        trim('user_info         ')=>trim('/ta/tm_mssr_set.php                   '),
                    //
                    //    trim('teacher_gallery       ')=>trim('/ta/treasure_chest.php                '),
                    //
                    //    trim('teacher_institute     ')=>trim('/ta/index.php                         ')
                    //);
                    //
                    ////例外處理, 3年級看到的是"數學任務"。
                    //if(isset($_SESSION['tc']['t|dt']['arrys_class_code'][0]['grade'])){
                    //    $sess_grade=(int)$_SESSION['tc']['t|dt']['arrys_class_code'][0]['grade'];
                    //    if($sess_grade===3){
                    //        $config_arrys['center']['teacher_center']['report']['header_right']['name_arry']['math']='數學任務';
                    //        $config_arrys['center']['teacher_center']['report']['header_right']['url_arry']['math']='/ta/ta_learn/math/teacher_finish.php';
                    //    }
                    //}

                //---------------------------------------
                //學期時間設置
                //---------------------------------------

                    ////本學期開始時間
                    //$config_arrys['center']['teacher_center']['semester_start']='2013-09-01';
                    //
                    ////本學期結束時間
                    //$config_arrys['center']['teacher_center']['semester_end']='2014-01-31';

            //-------------------------------------------
            //用戶資訊
            //-------------------------------------------
            //用戶類型    中文            用戶層級    SESSION
            //a           系統使用者      1           $_SESSION['a']['uid']
            //t           教師使用者      3           $_SESSION['t']['uid']
            //s           學生使用者      5           $_SESSION['s']['uid']
            //am          協會成員使用者  7           $_SESSION['am']['uid']
            //dt          主任成員使用者  13          $_SESSION['dt']['uid']

                //儲存用戶資料表欄位
                $config_arrys['user_tbl'] =(empty($_SESSION['config']['user_tbl']))?array():$_SESSION['config']['user_tbl'];

                //儲存用戶類型(a,t,s,am,dt ...)
                $user_types=array('a','t','s','am','dt');

                $config_arrys['user_types']=(empty($_SESSION['config']['user_types']))?$user_types:$_SESSION['config']['user_types'];
                $config_arrys['user_type'] =(empty($_SESSION['config']['user_type']))?'':$_SESSION['config']['user_type'];

                //儲存用戶層級(1,3,5,7 ...)
                $config_arrys['user_lv']  =(empty($_SESSION['config']['user_lv']))?0:$_SESSION['config']['user_lv'];

            //-------------------------------------------
            //區域資訊
            //-------------------------------------------

                $config_arrys['user_area'] =(empty($_SESSION['config']['user_area']))?array():$_SESSION['config']['user_area'];

            //-------------------------------------------
            //書店上架條件
            //-------------------------------------------

                $config_arrys['bookstore_open_publish']=array(
                    1=>'做2個以上的推薦才可以上架, 不包括評星(預設)',
                    2=>'教師同意才可上架',
                    3=>'教師指導至少一項4分(笑臉)以上才可上架',
                    4=>'做3個以上的推薦才可以上架, 不包括評星',
                    5=>'做4個以上的推薦才可以上架, 不包括評星'
                );

            //-------------------------------------------
            //回存
            //-------------------------------------------

                //回存
                $_SESSION['config']=$config_arrys;

    //---------------------------------------------------
    //引用
    //---------------------------------------------------

        //資料庫連線資訊
        require_once('conn.php');

        //-----------------------------------------------
        //user部分
        //-----------------------------------------------

            //資料庫連結資訊陣列
            $arry_conn_user=array(
                'db_host'   =>db_host_user,
                'db_name'   =>db_name_user,
                'db_user'   =>db_user_user,
                'db_pass'   =>db_pass_user,
                'db_encode' =>db_encode_user
            );
            if(1==2){ //除錯用
                echo "<pre>";
                print_r($arry_conn_user);
                echo "</pre>";
            }

        //-----------------------------------------------
        //mssr部分
        //-----------------------------------------------

            //資料庫連結資訊陣列
            $arry_conn_mssr=array(
                'db_host'   =>db_host_mssr,
                'db_name'   =>db_name_mssr,
                'db_user'   =>db_user_mssr,
                'db_pass'   =>db_pass_mssr,
                'db_encode' =>db_encode_mssr
            );
            if(1==2){ //除錯用
                echo "<pre>";
                print_r($arry_conn_mssr);
                echo "</pre>";
            }

        //-----------------------------------------------
        //工具程式
        //-----------------------------------------------

            require_once(APP_ROOT.'lib/php/tool/code.php');

        //-----------------------------------------------
        //掛載user_login_log
        //-----------------------------------------------

            //@ob_start();
            //    $DOCUMENT_ROOT=$_SERVER['DOCUMENT_ROOT'];
            //    $config_user_login_log_script="
            //        <!-- <script type='text/javascript' src='/mssr/lib/jquery/basic/code.js'></script> -->
            //        <script src='//code.jquery.com/jquery-1.11.0.min.js'></script>
            //        <script type='text/javascript' src='{$DOCUMENT_ROOT}/ac/js/user_log.js'></script>
            //    ";
            //    echo $config_user_login_log_script;
            //    unset($DOCUMENT_ROOT);
            //    unset($config_user_login_log_script);
            //@ob_end_clean();
?>