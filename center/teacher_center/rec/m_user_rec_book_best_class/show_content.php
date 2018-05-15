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
        //if(empty($arrys_login_info)){
        //    die();
        //}

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
            if(!empty($arrys_login_info)){
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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_rec_book_best_class');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //SESSION
        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用

        if(isset($_SESSION['m_user_rec_book_best_class']['filter'])){
            $filter=$_SESSION['m_user_rec_book_best_class']['filter'];

            if(isset($_SESSION['m_user_rec_book_best_class']['class_code'])&&(trim($_SESSION['m_user_rec_book_best_class']['class_code'])!=='')){
                $q_class_code=mysql_prep(trim($_SESSION['m_user_rec_book_best_class']['class_code']));

                $sql="
                    SELECT
                        `class`.`grade`,
                        `class`.`classroom`
                    FROM `class`
                    WHERE 1=1
                        AND `class`.`class_code`='{$q_class_code}'
                ";
                $arrys_result=db_result($conn_type='pdo','',$sql,array(0,1),$arry_conn_user);
                if(!empty($arrys_result)){
                    $q_grade=(int)$arrys_result[0]['grade'];
                    $q_classroom=(int)$arrys_result[0]['classroom'];

                    //置換班級名稱
                    $get_class_code_info_single=get_class_code_info_single('',mysql_prep($sess_login_info['school_code']),(int)$q_grade,$q_classroom,$compile_flag=true,$arry_conn_user);
                    if(!empty($get_class_code_info_single)){
                        $new_q_classroom=trim($get_class_code_info_single[0]['classroom']);
                    }else{
                        $get_class_code_info_easy=get_class_code_info_easy('',mysql_prep($q_class_code),$compile_flag=true,$arry_conn_user);
                        $new_q_classroom=trim($get_class_code_info_easy[0]['classroom']);
                    }
                }
            }

            if(isset($_SESSION['m_user_rec_book_best_class']['best_id'])&&(trim($_SESSION['m_user_rec_book_best_class']['best_id'])!=='')){
                $q_best_id=(int)(trim($_SESSION['m_user_rec_book_best_class']['best_id']));
            }
        }
        if(isset($_SESSION['m_user_rec_book_best_class']['query_fields'])){
            $query_fields=$_SESSION['m_user_rec_book_best_class']['query_fields'];
        }

        if(isset($sess_login_info['uid'])){$sess_user_id=(int)$sess_login_info['uid'];}
        if(isset($sess_login_info['permission'])){$sess_permission=trim($sess_login_info['permission']);}
        if(isset($sess_login_info['school_code'])){$sess_school_code=trim($sess_login_info['school_code']);}
        if(isset($sess_login_info['responsibilities'])){
            $sess_responsibilities=(int)$sess_login_info['responsibilities'];
            if(in_array($auth_sys_check_lv,array(5,14,16,22))){
                $sess_class_code=trim($sess_login_info['arrys_class_code'][0]['class_code']);
                $sess_grade=(int)$sess_login_info['arrys_class_code'][0]['grade'];
                $sess_classroom=(int)$sess_login_info['arrys_class_code'][0]['classroom'];

                //置換班級名稱
                $get_class_code_info_single=get_class_code_info_single('',mysql_prep($sess_school_code),(int)$sess_grade,$sess_classroom,$compile_flag=true,$arry_conn_user);
                $new_sess_classroom=trim($get_class_code_info_single[0]['classroom']);
            }
        }

        if(isset($_SESSION['m_user_rec_book_best_class']['class_code'])&&trim($_SESSION['m_user_rec_book_best_class']['class_code'])!==''){
            $_SESSION['teacher_center']['class_code']=trim($_SESSION['m_user_rec_book_best_class']['class_code']);
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //初始化, 選擇身份指標
        $choose_identity_flag=false;
        if(isset($sess_responsibilities)){
            $choose_identity_flag=true;
        }

        //目標年級
        $grade_goal=0;
        if(isset($sess_grade)){
            $grade_goal=$sess_grade;
        }
        if(isset($q_grade)){
            $grade_goal=$q_grade;
        }

        //目標班級
        $classroom_goal=0;
        if(isset($sess_classroom)){
            $classroom_goal=$sess_classroom;
        }
        if(isset($q_classroom)){
            $classroom_goal=$q_classroom;
        }

        //目標班級(轉換)
        $new_classroom_goal='';
        if(isset($new_sess_classroom)){
            $new_classroom_goal=$new_sess_classroom;
        }
        if(isset($new_q_classroom)){
            $new_classroom_goal=$new_q_classroom;
        }

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //串接SQL
    //---------------------------------------------------

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //---------------------------------------------------
        //SQL查詢
        //---------------------------------------------------

            if($choose_identity_flag&isset($q_class_code)&&trim($q_class_code)!==''){
                $sql="
                    SELECT *
                    FROM `mssr`.`mssr_rec_book_best_class`
                    WHERE 1=1
                        AND `mssr`.`mssr_rec_book_best_class`.`class_code`='{$q_class_code}'

                ";
                if($q_best_id!==0){
                    $sql.="
                        AND `mssr`.`mssr_rec_book_best_class`.`best_id`={$q_best_id}
                    ";
                }
                $sql.="
                    ORDER BY `mssr`.`mssr_rec_book_best_class`.`keyin_cdate` DESC
                ";
            }else{
                $q_best_id=(isset($_GET['best_id']))?(int)$_GET['best_id']:0;
                if($q_best_id!==0){
                    $sql="
                        SELECT *
                        FROM `mssr`.`mssr_rec_book_best_class`
                        WHERE 1=1
                            #AND `mssr`.`mssr_rec_book_best_class`.`class_code`='{$q_class_code}'
                            AND `mssr`.`mssr_rec_book_best_class`.`best_id`={$q_best_id}
                    ";
                }else{
                    page_nrs($title="明日星球,教師中心");
                    die();
                }
            }
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $db_results_cno=count($db_results);
            if($choose_identity_flag&isset($q_class_code)&&trim($q_class_code)!==''){}else{
                $q_class_code=trim($db_results[0]['class_code']);
                if($db_results_cno>0)$choose_identity_flag=true;
            }

            $arry_user_id=[];
            if($db_results_cno!==0){
                foreach($db_results as $db_result){
                    $arry_user_id[]=(int)$db_result['user_id'];
                }
                $arry_user_id=array_count_values($arry_user_id);
            }

    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        if($choose_identity_flag){

            $numrow=$db_results_cno;
            $psize =$numrow;        //單頁筆數,預設全部
            $pnos  =0;              //分頁筆數
            $pinx  =1;              //目前分頁索引,預設1
            $sinx  =0;              //值域起始值
            $einx  =0;              //值域終止值

            if(isset($_GET['psize'])){
                $psize=(int)$_GET['psize'];
                if($psize===0){
                    $psize=10;
                }
            }
            if(isset($_GET['pinx'])){
                $pinx=(int)$_GET['pinx'];
                if($pinx===0){
                    $pinx=1;
                }
            }

            $pnos  =@ceil($numrow/$psize);
            $pinx  =($pinx>$pnos)?$pnos:$pinx;

            $sinx  =(($pinx-1)*$psize)+1;
            $einx  =(($pinx)*$psize);
            $einx  =($einx>$numrow)?$numrow:$einx;
            //echo $numrow."<br/>";
        }

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,教師中心";

        if($choose_identity_flag){
            if($numrow!==0){
                //$arrys_chunk =array_chunk($db_results,$psize);
                //$arrys_result=$arrys_chunk[$pinx-1];
                $arrys_result=$db_results;
                page_hrs($title);
                die();
            }else{
                page_nrs($title);
                die();
            }
        }
?>
<?php function page_hrs($title="") {?>
<?php
//-------------------------------------------------------
//page_hrs 區塊 -- 開始
//-------------------------------------------------------

    //---------------------------------------------------
    //外部變數
    //---------------------------------------------------

        //config.php
        global $PAGE_SELF;
        global $FOLDER_SELF;
        global $nl;
        global $tab;
        global $fso_enc;
        global $page_enc;
        global $arry_conn_user;
        global $arry_conn_mssr;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;
        global $auth_sys_check_lv;

        global $sess_login_info;
        global $arrys_result;
        global $arry_user_id;
        global $config_arrys;
        global $conn_mssr;

        global $grade_goal;
        global $classroom_goal;
        global $new_classroom_goal;
        global $q_class_code;
        global $arry_ftp1_info;
        global $sess_user_id;
        global $q_best_id;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=14;    //欄位個數
        $btn_nos=1;     //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        //建立連線 user
        $conn_user=conn($db_type='mysql',$arry_conn_user);

        //推薦類型陣列
        $arrys_rec_type=array(
            trim('draw  ')=>trim('繪圖'),
            trim('text  ')=>trim('文字'),
            trim('record')=>trim('錄音')
        );

        $arry_record_info=array();
        $curdate=date("Y-m-d");

        //$ftp_root="public_html/mssr/info/user/".(int)$user_id."/book";
        //$http_path="http://".$arry_ftp1_info['host']."/mssr/info/user/".(int)$user_id."/book/";
        $ftp_root="public_html/mssr/info/class/{$q_class_code}/rec_book_best";
        $http_path="http://".$arry_ftp1_info['host']."/mssr/info/class/{$q_class_code}/rec_book_best";

        //連接 | 登入 FTP
        $ftp_conn  =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
        $ftp_login =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

        //設定被動模式
        ftp_pasv($ftp_conn,TRUE);
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=true);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <!-- <link href="../../../../lib/framework/bootstrap/css/code.css" rel="stylesheet" type="text/css"> -->
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/ui/code.js"></script>

    <script type="text/javascript" src="../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/table/code.js"></script>
    <!-- <script type="text/javascript" src="../../../../lib/framework/bootstrap/js/code.js"></script> -->

    <!-- 專屬 -->
    <link rel="stylesheet" href="../../inc/rec/audio_player/code.css">
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" />
    <style>
        body{
            background-image:url('');
            /*background-color:#2f3133;*/
        }
    </style>
</Head>

<Body>

    <!-- 資料列表 開始 -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="350px" align="center" valign="top">
            <!-- 內容 -->

                <?php if($q_best_id===0):?>
                    <div class="mod_data_tbl_outline" style="margin-top:20px;">
                        <table id="mod_data_tbl" border="0" width="95%" cellpadding="5" cellspacing="0" style="margin:30px 0;" class="table_style3">
                            <thead>
                                <tr align="center" valign="middle" class="fsize_18">
                                    <th width="" height="40px">姓名</th>
                                    <th width="" height="40px">篇數</th>
                                </tr>
                            </thead>
                            <?php foreach($arry_user_id as $key=>$val) :?>
                            <?php
                                $get_user_info=get_user_info($conn_user,(int)$key,$array_filter=array("name"),$arry_conn_user);
                                $user_name=trim($get_user_info[0]['name']);
                            ?>
                                <tr class="fsize_16">
                                    <td height="40px" align="center" valign="middle">
                                        <?php echo htmlspecialchars($user_name);?>
                                    </td>
                                    <td height="40px" align="center" valign="middle">
                                        <?php echo (int)$val;?>
                                    </td>
                                </tr>
                            <?php endforeach ;?>
                        </table>
                    </div>
                <?php endif;?>

                <!-- 資料表格 開始 -->
                <div class="" style="margin-top:35px;">
                    <?php foreach($arrys_result as $arrys_inx=>$arry_result) :?>
                    <?php
                    //---------------------------------------------------
                    //接收欄位
                    //---------------------------------------------------

                        extract($arry_result, EXTR_PREFIX_ALL, "rs");

                    //---------------------------------------------------
                    //處理欄位
                    //---------------------------------------------------

                        $rs_best_id=(int)$rs_best_id;
                        $rs_user_id=(int)$rs_user_id;
                        $rs_book_sid=trim($rs_book_sid);
                        $rs_class_code=trim($rs_class_code);

                        if(preg_match("/^mbu/i",$rs_book_sid)){
                            $get_book_info=get_book_info($conn_mssr,trim($rs_book_sid),$array_filter=array('book_verified'),$arry_conn_mssr);
                            if(!empty($get_book_info)){
                                $rs_book_verified=(int)$get_book_info[0]['book_verified'];
                                if($rs_book_verified===2)continue;
                            }else{continue;}
                        }

                    //---------------------------------------------------
                    //特殊處理
                    //---------------------------------------------------

                        //推薦識別碼陣列
                        $arrys_rec_sid=array();

                        //-----------------------------------------------
                        //查找, 使用者資訊
                        //-----------------------------------------------

                            $get_user_info=get_user_info($conn_user,$rs_user_id,$array_filter=array("name"),$arry_conn_user);
                            if(!empty($get_user_info)){
                                $rs_user_name=trim($get_user_info[0]['name']);
                                $rs_user_name=mb_substr($rs_user_name,0,1)."同學";
                            }

                        //-----------------------------------------------
                        //查找, 學校年級
                        //-----------------------------------------------

                            $sql="
                                SELECT `user`.`class`.`grade`,`user`.`school`.`school_name`
                                FROM `user`.`class`
                                    INNER JOIN `user`.`semester` ON
                                    `user`.`class`.`semester_code`=`user`.`semester`.`semester_code`

                                    INNER JOIN `user`.`school` ON
                                    `user`.`semester`.`school_code`=`user`.`school`.`school_code`
                                WHERE 1=1
                                    AND `user`.`class`.`class_code`='{$rs_class_code}'
                            ";
                            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                            if(!empty($db_results)){
                                $rs_grade=(int)$db_results[0]['grade'];
                                $rs_school_name=trim($db_results[0]['school_name']);
                            }

                        //-----------------------------------------------
                        //查找, 書名
                        //-----------------------------------------------

                            $get_book_info=get_book_info($conn_mssr,trim($rs_book_sid),$array_filter=array('book_name'),$arry_conn_mssr);
                            $rs_book_name='查無書名!';
                            if(!empty($get_book_info)){
                                $rs_book_name=trim($get_book_info[0]['book_name']);
                                if(mb_strlen($rs_book_name)>10){
                                    $rs_book_name=mb_substr($rs_book_name,0,10)."";
                                }
                            }

                        //-----------------------------------------------
                        //查找, 圖片
                        //-----------------------------------------------

                            $has_draw     =false;
                            //$rec_draw_info=get_rec_info($conn_mssr,$rs_user_id,trim($rs_book_sid),$rec_type='draw',$array_filter=array("rec_sid"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                            //if(!empty($rec_draw_info)){
                            //    foreach($rec_draw_info as $inx=>$arry){
                            //        //匯入推薦識別碼陣列
                            //        array_push($arrys_rec_sid,trim($arry['rec_sid']));
                            //    }
                            //    //圖片識別碼
                            //    $rs_rec_draw_sid=trim($rs_rec_draw_sid);
                            //}

                            //圖片識別碼
                            $rs_rec_draw_sid=trim($rs_rec_draw_sid);

                            if($rs_rec_draw_sid!==''){
                                //手繪
                                $draw_path      ="{$ftp_root}/{$rs_best_id}/1.jpg";
                                $arry_ftp_file_draw_path=ftp_nlist($ftp_conn,$draw_path);

                                //上傳
                                $up_load_draw_path_1    ="{$ftp_root}/{$rs_best_id}/upload_1.jpg";
                                $up_load_draw_path_2    ="{$ftp_root}/{$rs_best_id}/upload_2.jpg";
                                $up_load_draw_path_3    ="{$ftp_root}/{$rs_best_id}/upload_3.jpg";
                                $arry_ftp_file_up_load_draw_path_1=ftp_nlist($ftp_conn,$up_load_draw_path_1);
                                $arry_ftp_file_up_load_draw_path_2=ftp_nlist($ftp_conn,$up_load_draw_path_2);
                                $arry_ftp_file_up_load_draw_path_3=ftp_nlist($ftp_conn,$up_load_draw_path_3);

                                if((!empty($arry_ftp_file_draw_path))||(!empty($arry_ftp_file_up_load_draw_path_1))||(!empty($arry_ftp_file_up_load_draw_path_2))||(!empty($arry_ftp_file_up_load_draw_path_3))){
                                    $has_draw=true;
                                }

                                $draw_path='';
                                if((!empty($arry_ftp_file_up_load_draw_path_1))){
                                    $draw_path=$http_path."/{$rs_best_id}/upload_1.jpg";
                                }
                                if((!empty($arry_ftp_file_up_load_draw_path_2))){
                                    $draw_path=$http_path."/{$rs_best_id}/upload_2.jpg";
                                }
                                if((!empty($arry_ftp_file_up_load_draw_path_3))){
                                    $draw_path=$http_path."/{$rs_best_id}/upload_3.jpg";
                                }
                                if((!empty($arry_ftp_file_draw_path))){
                                    $draw_path=$http_path."/{$rs_best_id}/1.jpg";
                                }
                            }

                        //-----------------------------------------------
                        //查找, 星評資訊
                        //-----------------------------------------------

                            $has_star=false;
                            //星評識別碼
                            $rs_rec_star_sid=trim($rs_rec_star_sid);

                            if($rs_rec_star_sid!==''){
                                //評星評價
                                //$rs_rec_star_rank=str_repeat('★',(int)$rs_rec_star_rank);
                                //echo "<Pre>";print_r($rs_rec_star_rank);echo "</Pre>";

                                //評星理由
                                $arrys_rs_rec_star_reason=array();
                                $rs_rec_star_reason=trim($rs_rec_star_reason);
                                foreach($config_arrys['service']['bookstore']['rec_reason'] as $inx1=>$val1){
                                    if($rs_rec_star_reason[$inx1]==='o'){
                                        //匯入評星理由
                                        array_push($arrys_rs_rec_star_reason,$val1);
                                    }
                                }
                                $arrys_rs_rec_star_reason=array_map("htmlspecialchars",$arrys_rs_rec_star_reason);
                                $arrys_rs_rec_star_reason=implode("<br>",$arrys_rs_rec_star_reason);

                                $has_star=true;
                            }else{
                                $rs_rec_star_rank='尚未選擇星等 !';
                                $rs_rec_star_reason='尚未選擇評星理由 !';
                            }

                            //$rec_star_info=get_rec_info($conn_mssr,$rs_user_id,trim($rs_book_sid),$rec_type='star',$array_filter=array("rec_sid","rec_rank","rec_reason"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                            //if(!empty($rec_star_info)){
                            //    foreach($rec_star_info as $inx=>$arry){
                            //        //匯入推薦識別碼陣列
                            //        array_push($arrys_rec_sid,trim($arry['rec_sid']));
                            //    }
                            //
                            //
                            //    //評星評價
                            //    $rs_rec_star_rank=str_repeat('★',(int)$rs_rec_star_rank);
                            //
                            //    //評星理由
                            //    $arrys_rs_rec_star_reason=array();
                            //    $rs_rec_star_reason=trim($rec_star_info[0]['rec_reason']);
                            //    foreach($config_arrys['service']['bookstore']['rec_reason'] as $inx1=>$val1){
                            //        if($rs_rec_star_reason[$inx1]==='o'){
                            //            //匯入評星理由
                            //            array_push($arrys_rs_rec_star_reason,$val1);
                            //        }
                            //    }
                            //    $arrys_rs_rec_star_reason=implode("、",$arrys_rs_rec_star_reason);
                            //
                            //    $has_star=true;
                            //}

                        //-----------------------------------------------
                        //查找, 文字資訊
                        //-----------------------------------------------

                            $has_text   =false;

                            //文字識別碼
                            $rs_rec_text_sid=trim($rs_rec_text_sid);

                            if($rs_rec_text_sid!==''){
                                //文字內容
                                $rs_rec_text_content=trim($rs_rec_text_content);
                                if(@unserialize($rs_rec_text_content)){
                                    $arrys_rs_rec_text_content=@unserialize($rs_rec_text_content);
                                }
                                $has_text=true;
                            }else{
                                $rs_rec_text_state='';
                                $arrys_rs_rec_text_content=array('','','');
                            }

                            //$rec_text_info=get_rec_info($conn_mssr,$rs_user_id,trim($rs_book_sid),$rec_type='text',$array_filter=array("rec_sid","rec_content","rec_state"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                            //if(!empty($rec_text_info)){
                            //    foreach($rec_text_info as $inx=>$arry){
                            //        //匯入推薦識別碼陣列
                            //        array_push($arrys_rec_sid,trim($arry['rec_sid']));
                            //    }
                            //    //文字識別碼
                            //    $rs_rec_text_sid=trim($rec_text_info[0]['rec_sid']);
                            //
                            //
                            //
                            //    //文字推薦狀態
                            //    $rs_rec_text_state=trim($rec_text_info[0]['rec_state']);
                            //    if($rs_rec_text_state==='顯示'){
                            //        $has_text=true;
                            //    }
                            //}

                        //-----------------------------------------------
                        //查找, 錄音資訊
                        //-----------------------------------------------

                            $has_record     =false;

                            //錄音識別碼
                            $rs_rec_record_sid=trim($rs_rec_record_sid);

                            if($rs_rec_record_sid!==''){
                                $record_path_mp3    ="{$ftp_root}/{$rs_best_id}/1.mp3";
                                $arry_ftp_file_record_path_mp3=ftp_nlist($ftp_conn,$record_path_mp3);

                                $record_path_wav    ="{$ftp_root}/{$rs_best_id}/1.wav";
                                $arry_ftp_file_record_path_wav=ftp_nlist($ftp_conn,$record_path_wav);

                                if((!empty($arry_ftp_file_record_path_mp3))||(!empty($arry_ftp_file_record_path_wav))){
                                    $has_record =true;
                                    $arry_record_info[$rs_best_id]=$rs_user_id;
                                    //echo "<Pre>";print_r($arry_record_info);echo "</Pre>";
                                }
                            }else{

                            }

                            //$rec_record_info=get_rec_info($conn_mssr,$rs_user_id,trim($rs_book_sid),$rec_type='record',$array_filter=array("rec_sid"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                            //if(!empty($rec_record_info)){
                            //    foreach($rec_record_info as $inx=>$arry){
                            //        //匯入推薦識別碼陣列
                            //        array_push($arrys_rec_sid,trim($arry['rec_sid']));
                            //    }
                            //    //錄音識別碼
                            //    $rs_rec_record_sid=trim($rec_record_info[0]['rec_sid']);
                            //}

                        //-----------------------------------------------
                        //查找, 書本推薦內容總調查計數表資訊
                        //-----------------------------------------------

                            $get_rec_book_cno_info=get_rec_book_cno_info($conn_mssr,$rs_user_id,trim($rs_book_sid),$array_filter=array('has_publish'),$arry_conn_mssr);
                            $has_publish=trim($get_rec_book_cno_info[0]['has_publish']);

                        //-----------------------------------------------
                        //設定, 表格背景顏色
                        //-----------------------------------------------

                            $tbl_bg_color="bg_gray0";
                            $tbl_fc_color="fc_brown0";
                            //if($update_open_publish===2){
                            //    if($has_publish==='可'){
                            //        $tbl_bg_color="bg_gray1";
                            //        $tbl_fc_color="fc_white0";
                            //    }
                            //}
                    ?>
                    <?php if($q_best_id!==0):?>
                        <h1
                        style="color:#8e4408;font-size:18pt;background-color:#f3e4bc;height:50px;width:95%;margin-top:0px;border-radius:3px;border:2px solid #ca8334;color:#7c552c;margin-left:-10px;">
                            <div class="alert alert-info text-center" role="alert" style="position:relative;cursor:pointer;top:12px;"
                            onclick="parent.$.unblockUI();parent.$.colorbox.close();"><b>關閉視窗</b></div>
                        </h1>
                    <?php endif;?>
                        <div style="position:relative;width:890px;border:0px solid red;margin-top:35px;overflow:hidden;">
                            <span style="position:absolute;font-size:14pt;z-index:2;left:150px;top:15px;color:#663300;">
                                <strong style="display:block;"><?php echo htmlspecialchars("{$rs_school_name} {$rs_grade}年級 {$rs_user_name}");?></strong>
                                <strong style="position:relative;top:5px;">【<?php echo htmlspecialchars($rs_book_name);?>】</strong>
                            </span>
                            <img src="../../../../service/bookstore_v2/img/book_page_back2.png" width="1000" height="auto" border="0" alt="book"
                            style="position:relative;left:-45px;border:0px solid red;"/>
                            <img src="../../../../service/bookstore_v2/page_rec_read_v_forum/img/stars_pot.png" width="" height="auto" border="0" alt="star"
                            style="position:absolute;top:55px;left:40px;"/>
                            <img src="../../../../service/bookstore_v2/page_rec_read_v_forum/img/star_pot.png" width="" height="auto" border="0" alt="star"
                            style="position:absolute;top:115px;left:40px;"/>
                            <img src="../../../../service/bookstore_v2/page_rec_read_v_forum/img/draw_pot.png" width="" height="auto" border="0" alt="star"
                            style="position:absolute;top:185px;left:40px;z-index:2;"/>
                            <img src="../../../../service/bookstore_v2/page_rec_read_v_forum/img/recode_pot.png" width="" height="auto" border="0" alt="star"
                            style="position:absolute;top:355px;left:40px;z-index:2;"/>
                            <img src="../../../../service/bookstore_v2/page_rec_read_v_forum/img/text_pot.png" width="" height="auto" border="0" alt="star"
                            style="position:absolute;top:15px;right:305px;"/>

                            <?php for($i=1;$i<=$rs_rec_star_rank;$i++):?>
                            <?php
                                $left=(185)+($i*30);
                            ?>
                                <img src="../../img/obj/star.png" width="auto" height="auto" border="0" alt="star"
                                style="position:absolute;top:60px;left:<?php echo $left;?>px;"/>
                            <?php endfor;?>
                            <span style="font-size:15pt;color:#7c552c;position:absolute;top:110px;left:220px;"><strong>
                                <?php if($has_star){echo ($arrys_rs_rec_star_reason);}else{echo $rs_rec_star_reason;}?>
                            </strong></span>
                            <div style="position:absolute;top:190px;left:220px;">
                                <?php if((!empty($arry_ftp_file_draw_path))):?>
                                    <img src="<?php echo $http_path."/{$rs_best_id}/1.jpg";?>" width="48px" height="36px" border="0" alt="畫圖資訊" style="width:48px;height:36px;"
                                    onclick="change_rec_pic(this);" onmouseover="this.style.cursor='pointer'"/>
                                <?php endif;?>
                                <?php if((!empty($arry_ftp_file_up_load_draw_path_1))):?>
                                    <img src="<?php echo $http_path."/{$rs_best_id}/upload_1.jpg";?>" width="48px" height="36px" border="0" alt="畫圖資訊" style="width:48px;height:36px;"
                                    onclick="change_rec_pic(this);" onmouseover="this.style.cursor='pointer'"/>
                                <?php endif;?>
                                <?php if((!empty($arry_ftp_file_up_load_draw_path_2))):?>
                                    <img src="<?php echo $http_path."/{$rs_best_id}/upload_2.jpg";?>" width="48px" height="36px" border="0" alt="畫圖資訊" style="width:48px;height:36px;"
                                    onclick="change_rec_pic(this);" onmouseover="this.style.cursor='pointer'"/>
                                <?php endif;?>
                                <?php if((!empty($arry_ftp_file_up_load_draw_path_3))):?>
                                    <img src="<?php echo $http_path."/{$rs_best_id}/upload_3.jpg";?>" width="48px" height="36px" border="0" alt="畫圖資訊" style="width:48px;height:36px;"
                                    onclick="change_rec_pic(this);" onmouseover="this.style.cursor='pointer'"/>
                                <?php endif;?>
                            </div>
                            <div style="position:absolute;top:230px;left:125px;">
                                <?php if(($has_draw)):?>
                                    <img class="main_rec_pic" src="<?php echo $draw_path;?>" width="300px" height="155px" border="0" alt="畫圖資訊"/>
                                <?php endif;?>
                            </div>
                            <?php if(($has_record)):?>
                                <div style="position:absolute;top:410px;left:125px;">
                                    <audio controls>
                                        <source src="<?php echo "http://".$arry_ftp1_info['host'];?>/mssr/info/class/<?php echo $q_class_code;?>/rec_book_best/<?php echo $rs_best_id;?>/1.mp3" type="audio/mpeg">
                                        <source src="<?php echo "http://".$arry_ftp1_info['host'];?>/mssr/info/class/<?php echo $q_class_code;?>/rec_book_best/<?php echo $rs_best_id;?>/1.wav" type="audio/wav">
                                    </audio>
                                </div>
                            <?php endif;?>
                            <?php if(($has_text)):?>
                                <div style="position:absolute;top:70px;right:70px;">
                                    <textarea cols="40" rows="10" wrap="hard" class="form_textarea fsize_18 "
                                    style="width:330px;height:350px;display:block;border:0;">
<?php if(trim($arrys_rs_rec_text_content[1])!==''):?>
【書本內容介紹】
<?php echo htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[1])));?>
<?php endif;?>


<?php if(trim($arrys_rs_rec_text_content[2])!==''):?>
【書中所學到的事】
<?php echo htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[2])));?>
<?php endif;?>

<?php if(trim($arrys_rs_rec_text_content[0])===''&&trim($arrys_rs_rec_text_content[1])===''&&trim($arrys_rs_rec_text_content[2])===''):?>
             尚未填寫文字推薦！
<?php endif;?>
                                    </textarea>
                                </div>
                            <?php endif;?>
                        </div>
                    <?php endforeach ;?>

                </div>
                <!-- 資料表格 結束 -->
            <!-- 內容 -->
            </td>
        </tr>
    </table>
    <!-- 資料列表 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------


    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;
    var json_record_info=<?php echo json_encode($arry_record_info,true);?>;


    //物件

    function del(best_id){
        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"del.php",
            type       :"POST",
            data       :{
                best_id:best_id
            },

        //事件
            success     :function(respones){
            //成功處理
                alert(respones);
                location.reload();
            }
        });
    }

    function change_rec_pic(obj){
        //console.log(obj.src);
        //console.log($('.main_rec_pic')[0].src);
        $('.main_rec_pic')[0].src=obj.src;
    }


    window.onload=function(){

        for(key in json_record_info){
            var rs_user_id =json_record_info[key];
            var record_path=[{
                    mp3: 'http://<?php echo $arry_ftp1_info["host"];?>/mssr/info/class/'+'<?php echo($q_class_code);?>/rec_book_best/'+key+'/1.mp3?<?php echo(mt_rand(11111,99999));?>'
                }];
            try{
                audio_player(record_path,rs_user_id,key,'load');
            }catch(e){}
        }

        ////設定動態高度
        //var oIFC=parent.document.getElementById('IFC');
        //var oparent_IFC=parent.parent.document.getElementById('IFC');
        //oIFC.style.height=parseInt($(document).height()+50)+'px';
        //oparent_IFC.style.height=parseInt($(document).height()+250)+'px';
        //
        ////parent.$.unblockUI();
    }

</script>

</Body>
</Html>

<?php
//-------------------------------------------------------
//page_hrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>
<?php };?>


<?php function page_nrs($title="") {?>
<?php
//-------------------------------------------------------
//page_nrs 區塊 -- 開始
//-------------------------------------------------------

    //---------------------------------------------------
    //外部變數
    //---------------------------------------------------

        //config.php
        global $PAGE_SELF;
        global $FOLDER_SELF;
        global $nl;
        global $tab;
        global $fso_enc;
        global $page_enc;
        global $arry_conn_mssr;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $config_arrys;
        global $conn_mssr;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0;  //欄位個數
        $btn_nos=0;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=true);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" />
</Head>

<Body>

    <!-- 資料列表 開始 -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="400px" align="center" valign="top">
                <!-- 內容 -->
                <table border="0" width="100%" cellpadding="5" cellspacing="0" style="position:relative;top:50px;" class="mod_data_tbl_outline">
                    <tr align="center" valign="middle">
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="400px" align="center" valign="middle" class="font-family1 fc_green0 fsize_18">
                            <img src="../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
                            目前系統無資料，或查無資料!
                        </td>
                    </tr>
                </table>
                <!-- 內容 -->
            </td>
        </tr>
    </table>
    <!-- 資料列表 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    window.onload=function(){
        //parent.$.unblockUI();
    }

</script>

</Body>
</Html>

<?php
//-------------------------------------------------------
//page_nrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>
<?php };?>


<?php function page_sel_class_code($title="") {?>
<?php
//-------------------------------------------------------
//page_sel_class_code 區塊 -- 開始
//-------------------------------------------------------

    //---------------------------------------------------
    //外部變數
    //---------------------------------------------------

        //config.php
        global $PAGE_SELF;
        global $FOLDER_SELF;
        global $nl;
        global $tab;
        global $fso_enc;
        global $page_enc;
        global $arry_conn_mssr;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $config_arrys;
        global $conn_mssr;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0;  //欄位個數
        $btn_nos=0;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=true);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" />
</Head>

<Body>

    <!-- 資料列表 開始 -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="400px" align="center" valign="top">
                <!-- 內容 -->
                <table border="0" width="100%" cellpadding="5" cellspacing="0" style="position:relative;top:50px;" class="mod_data_tbl_outline">
                    <tr align="center" valign="middle">
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="400px" align="center" valign="middle" class="font-family1 fc_green0 fsize_18">
                            <img src="../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
                            請先選擇右上方的年級與班級!
                        </td>
                    </tr>
                </table>
                <!-- 內容 -->
            </td>
        </tr>
    </table>
    <!-- 資料列表 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    window.onload=function(){
        //parent.$.unblockUI();
    }

</script>

</Body>
</Html>

<?php
//-------------------------------------------------------
//page_sel_class_code 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>
<?php };?>


<?php function page_sel_no_user($title="") {?>
<?php
//-------------------------------------------------------
//page_sel_no_user 區塊 -- 開始
//-------------------------------------------------------

    //---------------------------------------------------
    //外部變數
    //---------------------------------------------------

        //config.php
        global $PAGE_SELF;
        global $FOLDER_SELF;
        global $nl;
        global $tab;
        global $fso_enc;
        global $page_enc;
        global $arry_conn_mssr;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $config_arrys;
        global $conn_mssr;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0;  //欄位個數
        $btn_nos=0;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=true);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" />
</Head>

<Body>

    <!-- 資料列表 開始 -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="400px" align="center" valign="top">
                <!-- 內容 -->
                <table border="0" width="100%" cellpadding="5" cellspacing="0" style="position:relative;top:50px;" class="mod_data_tbl_outline">
                    <tr align="center" valign="middle">
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="400px" align="center" valign="middle" class="font-family1 fc_green0 fsize_18">
                            <img src="../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
                            目前沒有學生資料!
                        </td>
                    </tr>
                </table>
                <!-- 內容 -->
            </td>
        </tr>
    </table>
    <!-- 資料列表 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    window.onload=function(){
        //parent.$.unblockUI();
    }

</script>

</Body>
</Html>

<?php
//-------------------------------------------------------
//page_sel_no_user 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>
<?php };?>