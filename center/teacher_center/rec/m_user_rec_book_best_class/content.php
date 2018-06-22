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
                page_nrs($title="明日星球,教師中心");
                die();
            }
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $db_results_cno=count($db_results);

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
                <!-- 統計資料表格 開始 -->
                <div class="mod_data_tbl_outline" style="margin-top:35px;">
                    <table id="mod_data_tbl" border="0" width="95%" height="40px" cellpadding="5" cellspacing="0" style="" class="font-weight1 font-family1 fc_green0">
                        <tr align="center" valign="middle" class="fsize_16">
                            <td align="center" width="470px">
                                <span class="fsize_16">
                                    <input type="button" value="請按我重新整理" class="ibtn_gr12030" onclick="location.reload();" onmouseover="this.style.cursor='pointer'">
                                    <input type="button" value="PDF檢視" class="ibtn_gr9030" onmouseover="this.style.cursor='pointer'"
                                    onclick="view_pdf();"
                                    >
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- 統計資料表格 結束 -->
                <div class="mod_data_tbl_outline" style="margin-top:35px;">
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
                            }

                        //-----------------------------------------------
                        //查找, 書名
                        //-----------------------------------------------

                            $get_book_info=get_book_info($conn_mssr,trim($rs_book_sid),$array_filter=array('book_name'),$arry_conn_mssr);
                            $rs_book_name='查無書名!';
                            if(!empty($get_book_info)){
                                $rs_book_name=trim($get_book_info[0]['book_name']);
                                if(mb_strlen($rs_book_name)>15){
                                    $rs_book_name=mb_substr($rs_book_name,0,15)."..";
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
                            }

                        //-----------------------------------------------
                        //查找, 星評資訊
                        //-----------------------------------------------

                            $has_star=false;
                            //星評識別碼
                            $rs_rec_star_sid=trim($rs_rec_star_sid);

                            if($rs_rec_star_sid!==''){
                                //評星評價
                                $rs_rec_star_rank=str_repeat('★',(int)$rs_rec_star_rank);

                                //評星理由
                                $arrys_rs_rec_star_reason=array();
                                $rs_rec_star_reason=trim($rs_rec_star_reason);
                                foreach($config_arrys['service']['bookstore']['rec_reason'] as $inx1=>$val1){
                                    if($rs_rec_star_reason[$inx1]==='o'){
                                        //匯入評星理由
                                        array_push($arrys_rs_rec_star_reason,$val1);
                                    }
                                }
                                $arrys_rs_rec_star_reason=implode("、",$arrys_rs_rec_star_reason);

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
                        <h1 style="color:#8e4408;font-size:18pt;background-color:#d9edf7;height:50px;width:97%;margin-top:10px;border-radius:3px;border:1px solid #bce8f1;color:#3d7792;">
                            <div class="alert alert-info text-center" role="alert" style="position:relative;cursor:pointer;top:12px;"
                            onclick="parent.$.unblockUI();"><b>關閉視窗</b></div>
                        </h1>
                    <?php endif;?>
                    
                    <table id="tbl_<?php echo $arrys_inx;?>" width="93%" align="center" border="0" cellpadding="0" cellspacing="0" style="margin-top:10px;"
                    class="mod_data_tbl_outline tbl_<?php echo $rs_user_id;?>_<?php echo $rs_book_sid;?>"
                    arrys_inx='<?php echo $arrys_inx;?>'
                    has_text='<?php if($has_text){echo 1;}else{echo 0;}?>'>
                        <tr>
                            <td align='left' valign="middle" bgcolor='#87CDDC' colspan="2">
                                <?php if($q_best_id===0):?>
                                    <input type="button" value="刪除推薦" style="margin:5px 5px;" onclick="del(<?php echo $rs_best_id;?>);">
                                    <img title="下載PDF" id="img_pdf_<?php echo $arrys_inx;?>" src="../../img/user/user_rec/pdf.png" width="30" height="30" border="0" alt="pdf"
                                    style="position:relative;left:15px;top:5px;cursor: pointer;" onclick="view_one_pdf('<?php echo $rs_user_id ?>_<?php echo $rs_book_sid ?>')"/>
                                    <input type="checkbox" id="pdf_list" name="pdf_list" value="<?php echo $rs_user_id;?>_<?php echo $rs_book_sid;?>" att="pdf_list_<?php echo $arrys_inx;?>"
                                    style='position:relative;left:15px;bottom:5px;' checked>
                                <?php endif;?>
                            </td>
                        </tr>
                        <tr>
                            <td align='left' valign="middle" bgcolor='#87CDDC'>
                                <?php //if($has_leader):?>
                                    <!-- <img id="img_read_<?php echo $arrys_inx;?>" src="../../../../img/user/user_rec/read.png" width="40" height="40" border="0" alt="read"/>
                                    <input type="checkbox" id="read_list" name="read_list" value="<?php echo $rs_user_id;?>" att="read_list_<?php echo $arrys_inx;?>"
                                    book_sid='<?php echo $rs_book_sid;?>'
                                    style='position:relative;bottom:15px;'
                                    onclick='ajax_read(this,<?php echo $rs_user_id;?>,"<?php echo $rs_book_sid;?>","tbl_<?php echo $arrys_inx;?>");void(0);'
                                    <?php if($has_read)echo 'checked';?>> -->
                                <?php //endif;?>
                            </td>
                            <td align='right' valign="middle" bgcolor='#87CDDC'>
                                <?php if(($has_draw)||($has_text)||($has_record)):?>
                                    <?php //if($has_leader):?>
                                        <!-- <img id="img_trash_<?php echo $arrys_inx;?>" src="../../../../img/user/user_rec/trash.png" width="40" height="40" border="0" alt="trash"
                                        onmouseover='this.style.cursor="pointer"'
                                        onclick='trash(<?php echo $rs_user_id;?>,"<?php echo $rs_book_sid;?>","tbl_<?php echo $arrys_inx;?>");void(0);'/> -->
                                    <?php //endif;?>
                                <?php endif;?>
                            </td>
                        </tr>

                        <tr valign="top">
                            <td align="center" colspan="2">
                                <!-- 基本資訊 -->
                                <table cellpadding="0" cellspacing="0" border="0" width="100%" height="60px" style="padding:3px;"/>
                                    <tr align="left" valign="middle" class="<?php echo $tbl_bg_color;?> fsize_18 <?php echo $tbl_fc_color;?>">
                                        <td align='center'>
                                            <table cellpadding="0" cellspacing="0" border="0" width="98%" class="<?php echo $tbl_bg_color;?> fsize_18 <?php echo $tbl_fc_color;?>"/>
                                                <tr>
                                                    <td width='475px'>
                                                        學生姓名：<?php echo htmlspecialchars($rs_user_name);?>
                                                    </td>
                                                    <td>
                                                        作品納入時間：<?php echo htmlspecialchars($rs_keyin_cdate);?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        書籍名稱：<?php echo htmlspecialchars($rs_book_name);?>
                                                    </td>
                                                    <td>
                                                        <!-- 最後更新時間：<?php echo htmlspecialchars($rs_keyin_mdate);?> -->
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr valign="top">
                            <td align="center" width="55%">
                                <!-- 文字資訊 -->
                                <table cellpadding="0" cellspacing="0" border="0" width="100%" height="570px" style="padding:3px;"/>
                                    <tr align="left" valign="top" class="<?php echo $tbl_bg_color;?>">
                                        <td>
                                            <span class="fsize_18 font-weight1 <?php echo $tbl_fc_color;?>" style="position:relative;padding:5px;display:block;">
                                                <textarea cols="40" rows="10" wrap="hard" class="form_textarea fsize_18 font-weight1 <?php echo $tbl_fc_color;?> <?php echo $tbl_bg_color;?>"
                                                style="width:460px;height:508px;display:block;border:0;">
												<?php if(trim($arrys_rs_rec_text_content[0])!==''):?>
												【最喜歡的一句話】
												<?php echo htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[0])));?>
												<?php endif;?>
												
												
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
                                            </span>
                                        </td>
                                    </tr>
                                    <!-- 文字指導 -->
                                    <!-- <tr align="center" valign="top" class="<?php echo $tbl_bg_color;?>">
                                        <td>
                                            <?php //if(in_array($auth_sys_check_lv,array(5,14,22))):?>
                                                <?php if(($has_text)&&($sess_user_id!==$rs_user_id)&&($has_leader)):?>
                                                    <?php if(in_array('text',$arry_has_rec_comment)):?>
                                                        <input type="button" style="display:none;" id="btn_text_<?php echo $arrys_inx;?>" value="✔老師指導(文字已指導)" class="" onclick="detailed('date_between','text','<?php echo addslashes($rs_rec_text_sid);?>',<?php echo addslashes($rs_user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')" onmouseover="this.style.cursor='pointer'">
                                                    <?php else:?>
                                                        <input type="button" style="display:none;" id="btn_text_<?php echo $arrys_inx;?>" value="老師指導(文字)" class="" onclick="detailed('date_between','text','<?php echo addslashes($rs_rec_text_sid);?>',<?php echo addslashes($rs_user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')" onmouseover="this.style.cursor='pointer'">
                                                    <?php endif;?>
                                                    <input type="button" style="display:none;" id="btn_del_text_<?php echo $arrys_inx;?>" value="直接刪除推薦" class=""
                                                    onclick="del('date_between','text','<?php echo addslashes($rs_rec_text_sid);?>',<?php echo addslashes($rs_user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')"
                                                    onmouseover="this.style.cursor='pointer'">
                                                    <select id="btn_single_del_text_<?php echo $arrys_inx;?>" class="btn_single_del_text form_select" style="display:none;position:relative;background-color:#ebebeb;bottom:2px;height:25px;"
                                                    rec_sid='<?php echo addslashes($rs_rec_text_sid);?>'>
                                                        <option value="">請選擇文字推薦刪除項目
                                                        <option value="1">刪除文字推薦第一項
                                                        <option value="2">刪除文字推薦第二項
                                                        <option value="3">刪除文字推薦第三項
                                                    </select>
                                                <?php endif;?>
                                            <?php //endif;?>
                                        </td>
                                    </tr> -->
                                </table>
                            </td>
                            <td align="center" width="45%" style="background-color:#ffffff;">
                                <!-- 評星資訊 -->
                                <table cellpadding="0" cellspacing="0" border="0" width="100%" height="70px" style="padding:3px;"/>
                                    <tr align="left" valign="middle" class="<?php echo $tbl_bg_color;?>">
                                        <td>
                                            <span class="fsize_18 font-weight1 <?php echo $tbl_fc_color;?>" style="position:relative;padding:5px;display:block;">
                                                【評星】<?php echo htmlspecialchars($rs_rec_star_rank);?>
                                            </span>
                                            <span class="fsize_18 font-weight1 <?php echo $tbl_fc_color;?>" style="position:relative;padding:5px;display:block;">
                                                【理由】<?php if($has_star){echo htmlspecialchars($arrys_rs_rec_star_reason);}else{echo $rs_rec_star_reason;}?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>

                                <!-- 畫圖資訊 -->
                                <table id="tbl_draw_<?php echo $arrys_inx;?>" cellpadding="0" cellspacing="0" border="0" width="100%" height="400px" style="padding:3px;"/>
                                    <tr align="center" valign="middle" class="<?php echo $tbl_bg_color;?>">
                                        <td>
                                            <!-- 目標圖片 -->
                                            <div class="goal_img_<?php echo $arrys_inx;?>" style="width:355px;height:283px;">
                                            <?php if((!empty($arry_ftp_file_draw_path))):?>
                                                <img src="<?php echo $http_path."/{$rs_best_id}/1.jpg";?>" width="355px" height="283px" border="0" alt="畫圖資訊"/>
                                            <?php else:?>
                                                <span class="fsize_18 font-weight1 <?php echo $tbl_fc_color;?>" style='position:relative;top:100px;'>尚未畫圖 !</span>
                                            <?php endif;?>
                                            </div>

                                            <!-- 手繪 && 上傳 -->
                                            <table cellpadding="0" cellspacing="0" border="0" width="380px" style="position:relative;margin:13px 0px 5px 0px;"/>
                                                <tr align="center">
                                                    <td width="100px">
                                                        <?php if((!empty($arry_ftp_file_draw_path))):?>
                                                            <span style="display:block;border:0px solid red;width:70px;height:56px;" onmouseover='this.style.cursor="pointer"'>
                                                                <img src="<?php echo $http_path."/{$rs_best_id}/1.jpg";?>" width="70px" height="56px" border="0" alt="畫圖資訊" style="width:70px;height:56px;" onclick="change_rec_img(this,<?php echo $arrys_inx;?>);void(0);"/>
                                                            </span>
                                                        <?php else:?>
                                                            <span class="fsize_12 font-weight1 <?php echo $tbl_fc_color;?>" style="background-color:#fff;display:block;border:0px solid red;width:70px;height:56px;"
                                                            onmouseover='this.style.cursor="pointer"' onclick="change_rec_img(this,<?php echo $arrys_inx;?>);void(0);">
                                                                <span style='position:relative;top:20px;' onclick="change_rec_img(this,<?php echo $arrys_inx;?>);void(0);">尚未畫圖</span>
                                                            </span>
                                                        <?php endif;?>
                                                    </td>
                                                    <td width="100px">
                                                        <span style="display:block;border:0px solid red;width:70px;height:56px;" onmouseover='this.style.cursor="pointer"'>
                                                            <?php if((!empty($arry_ftp_file_up_load_draw_path_1))):?>
                                                                <img src="<?php echo $http_path."/{$rs_best_id}/upload_1.jpg";?>" width="70px" height="56px" border="0" alt="畫圖資訊" style="width:70px;height:56px;" onclick="change_rec_img(this,<?php echo $arrys_inx;?>);void(0);"/>
                                                            <?php else:?>
                                                                <!-- <img src="../../../../img/user/user_rec/rec_up_load_draw_noimg_s.png"
                                                                width="70px" height="56px" border="0" alt="畫圖資訊" style="width:70px;height:56px;" onclick="change_rec_img(this,<?php echo $arrys_inx;?>);void(0);"/> -->
                                                            <?php endif;?>
                                                        </span>
                                                    </td>
                                                    <td width="100px">
                                                        <span style="display:block;border:0px solid red;width:70px;height:56px;" onmouseover='this.style.cursor="pointer"'>
                                                            <?php if((!empty($arry_ftp_file_up_load_draw_path_2))):?>
                                                                <img src="<?php echo $http_path."/{$rs_best_id}/upload_2.jpg";?>" width="70px" height="56px" border="0" alt="畫圖資訊" style="width:70px;height:56px;" onclick="change_rec_img(this,<?php echo $arrys_inx;?>);void(0);"/>
                                                            <?php else:?>
                                                                <!-- <img src="../../../../img/user/user_rec/rec_up_load_draw_noimg_s.png"
                                                                width="70px" height="56px" border="0" alt="畫圖資訊" style="width:70px;height:56px;" onclick="change_rec_img(this,<?php echo $arrys_inx;?>);void(0);"/> -->
                                                            <?php endif;?>
                                                        </span>
                                                    </td>
                                                    <td width="100px">
                                                        <span style="display:block;border:0px solid red;width:70px;height:56px;" onmouseover='this.style.cursor="pointer"'>
                                                            <?php if((!empty($arry_ftp_file_up_load_draw_path_3))):?>
                                                                <img src="<?php echo $http_path."/{$rs_best_id}/upload_3.jpg";?>" width="70px" height="56px" border="0" alt="畫圖資訊" style="width:70px;height:56px;" onclick="change_rec_img(this,<?php echo $arrys_inx;?>);void(0);"/>
                                                            <?php else:?>
                                                                <!-- <img src="../../../../img/user/user_rec/rec_up_load_draw_noimg_s.png"
                                                                width="70px" height="56px" border="0" alt="畫圖資訊" style="width:70px;height:56px;" onclick="change_rec_img(this,<?php echo $arrys_inx;?>);void(0);"/> -->
                                                            <?php endif;?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>

                                <!-- 錄音資訊 -->
                                <table cellpadding="0" cellspacing="0" border="0" width="100%" height="100px" style="padding:3px;"/>
                                    <tr align="left" valign="middle" class="<?php echo $tbl_bg_color;?>">
                                        <td>
                                            <?php if(($has_record)):?>
                                                <div id="player" class='player_<?php echo $rs_user_id;?>_<?php echo $rs_best_id;?>' style='float:left;'>
                                                    <div class="ctrl" id='ctrl_<?php echo $rs_user_id;?>_<?php echo $rs_best_id;?>'>
                                                        <div class="control" id='control_<?php echo $rs_user_id;?>_<?php echo $rs_best_id;?>'>
                                                            <div class="left" id='left_<?php echo $rs_user_id;?>_<?php echo $rs_best_id;?>'>
                                                                <div class="playback icon" id='playback_<?php echo $rs_user_id;?>_<?php echo $rs_best_id;?>'></div>
                                                            </div>
                                                            <div class="volume right" id='volume_<?php echo $rs_user_id;?>_<?php echo $rs_best_id;?>1'>
                                                                <div class="mute icon left" id='mute_<?php echo $rs_user_id;?>_<?php echo $rs_best_id;?>'></div>
                                                                <div class="slider left" id='slider_<?php echo $rs_user_id;?>_<?php echo $rs_best_id;?>'>
                                                                    <div class="pace" id='pace_<?php echo $rs_user_id;?>_<?php echo $rs_best_id;?>'></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="progress" id='progress_<?php echo $rs_user_id;?>_<?php echo $rs_best_id;?>'>
                                                            <div class="slider" id='slider_<?php echo $rs_user_id;?>_<?php echo $rs_best_id;?>'>
                                                                <div class="loaded" id='loaded_<?php echo $rs_user_id;?>_<?php echo $rs_best_id;?>'></div>
                                                                <div class="pace" id='pace_<?php echo $rs_user_id;?>_<?php echo $rs_best_id;?>'></div>
                                                            </div>
                                                            <div class="timer left" id='timer_<?php echo $rs_user_id;?>_<?php echo $rs_best_id;?>'
                                                            style='position:relative;bottom:3px;'>0:00</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else:?>
                                                <span class="fsize_18 font-weight1 <?php echo $tbl_fc_color;?>" style="position:relative;padding:5px;left:195px">
                                                    尚未錄音 !
                                                </span>
                                            <?php endif;?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- 教師同意才可上架 -->
                        <?php //if($update_open_publish===2):?>
                        <!-- <tr valign="middle">
                            <td align="center" colspan="2">
                                <table cellpadding="0" cellspacing="0" border="0" width="100%" height="40px" style="padding:3px;"/>
                                    <tr align="center" valign="middle" class="<?php echo $tbl_bg_color;?> fsize_18 <?php echo $tbl_fc_color;?>">
                                        <td>
                                            <span class="font-weight1">
                                                開放學生上架此本書籍 :
                                                <select id="has_publish" name="has_publish" class="form_select" onchange="has_publish('date_between',this.options[this.options.selectedIndex].value,<?php echo addslashes($rs_user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>');">
                                                    <option value="可" <?php echo ($has_publish==='可')?'selected':'';?>>開放
                                                    <option value="否" <?php echo ($has_publish==='否')?'selected':'';?>>關閉
                                                </select>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr> -->
                        <?php //endif;?>

                        <!-- 教師評論紀錄 -->

                    </table>
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
    
    function view_one_pdf(user_id_book_sid){
        method = "post"; 
	    
	    //模擬Form 表單 以post 送出
	    var form = document.createElement("form");
	    var url="content_pdf.php?psize=<?php echo $psize;?>&pinx=<?php echo $pinx;?>";
        
	    form.setAttribute("method", method);
	    form.setAttribute("action", url);
	    form.setAttribute("target", '_blank');
		
		var input = document.createElement("input");
        input.setAttribute("type", "hidden");
        input.setAttribute("name", 'user_id_book_sid[]');
        input.setAttribute("value", user_id_book_sid);
        form.appendChild(input);
		
		document.body.appendChild(form);    // Not entirely sure if this is necessary
	    form.submit();
    }
	
	function view_pdf() {
	    method = "post"; 
	    
	    //模擬Form 表單 以post 送出
	    var form = document.createElement("form");
	    var url="content_pdf.php?psize=<?php echo $psize;?>&pinx=<?php echo $pinx;?>";
        var opdf_lists=document.getElementsByName('pdf_list');
        
	    form.setAttribute("method", method);
	    form.setAttribute("action", url);
	    form.setAttribute("target", '_blank');
		
		for(var i=0;i<opdf_lists.length;i++){
            var opdf_list=opdf_lists[i];
            var user_id_book_sid =trim(opdf_list.value);
            if(opdf_list.checked===true){
                
                var input = document.createElement("input");
		        input.setAttribute("type", "hidden");
		        input.setAttribute("name", 'user_id_book_sid[]');
		        input.setAttribute("value", user_id_book_sid);
		        form.appendChild(input);
            }
        }
		
		document.body.appendChild(form);    // Not entirely sure if this is necessary
	    form.submit();
		
	}
	
    function change_rec_img(obj,inx){
        var o_src    =obj.src;
        var $goal_img=$('.goal_img_'+inx);
        if(o_src!==undefined){
            //清空
            $goal_img.empty();
            $goal_img.append('<img src="'+o_src+'" width="355px" height="283px" border="0" alt="畫圖資訊"/>');
        }else{
            //清空
            $goal_img.empty();
            $goal_img.append('<span class="fsize_18 font-weight1 fc_brown0" style="position:relative;top:100px;">尚未畫圖 !</span>');
        }
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

        //設定動態高度
        var oIFC=parent.document.getElementById('IFC');
        var oparent_IFC=parent.parent.document.getElementById('IFC');
        oIFC.style.height=parseInt($(document).height()+50)+'px';
        oparent_IFC.style.height=parseInt($(document).height()+250)+'px';

        //parent.$.unblockUI();
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