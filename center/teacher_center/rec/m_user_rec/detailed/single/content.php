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

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/fso/code',
                    APP_ROOT.'lib/php/upload/file_upload_save/code'
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
    //user_id   使用者主索引

        $get_chk=array(
            'user_id'
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
    //user_id   使用者主索引

        //GET
        $user_id     =trim($_GET[trim('user_id ')]);
        $get_book_sid=(isset($_GET[trim('book_sid')]))?$_GET[trim('book_sid')]:'';
        $view        =(isset($_GET[trim('view')]))?$_GET[trim('view')]:'';
        $scrolltop   =(isset($_GET['scrolltop']))?(int)$_GET['scrolltop']:0;
//echo "<Pre>";
//print_r($scrolltop);
//echo "</Pre>";
        //SESSION
        if(isset($sess_login_info['uid'])){$sess_user_id=(int)$sess_login_info['uid'];}
        if(isset($sess_login_info['permission'])){$sess_permission=trim($sess_login_info['permission']);}
        if(isset($sess_login_info['school_code'])){$sess_school_code=trim($sess_login_info['school_code']);}
        if(isset($sess_login_info['responsibilities'])){
            $sess_responsibilities=(int)$sess_login_info['responsibilities'];
            if(in_array($auth_sys_check_lv,array(5,14,22))){
                $sess_class_code=trim($sess_login_info['arrys_class_code'][0]['class_code']);
                $sess_grade=(int)$sess_login_info['arrys_class_code'][0]['grade'];
                $sess_classroom=(int)$sess_login_info['arrys_class_code'][0]['classroom'];
            }
        }

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //user_id   使用者主索引

        $arry_err=array();

        if($user_id===''){
           $arry_err[]='使用者主索引,未輸入!';
        }else{
           $user_id=(int)$user_id;
           if($user_id===0){
              $arry_err[]='使用者主索引,不為整數!';
           }
        }

        if(count($arry_err)!==0){
            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
            die();
        }

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
        //更新上架條件
        //---------------------------------------------------

            $update_open_publish=update_open_publish($db_type='mysql',$arry_conn_mssr,$APP_ROOT,$sess_user_id);

        //---------------------------------------------------
        //是否正在帶領學生
        //---------------------------------------------------

            $user_id     =(int)$user_id;
            $sess_user_id=(int)$sess_user_id;
            $has_leader  =true;
            $curdate     =date("Y-m-d");

            $query_sql="
                SELECT
                    `teacher`.`uid` AS `t_uid`,
                    `student`.`uid` AS `s_uid`
                FROM `teacher`
                    INNER JOIN `student`
                    ON `teacher`.`class_code`=`student`.`class_code`
                WHERE 1=1
                    AND `teacher`.`start` <='{$curdate}'
                    AND `teacher`.`end`   >='{$curdate}'

                    AND `student`.`start` <='{$curdate}'
                    AND `student`.`end`   >='{$curdate}'

                    AND `teacher`.`uid`    ={$sess_user_id}
                    AND `student`.`uid`    ={$user_id}
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_user,$query_sql,array(0,1),$arry_conn_user);
            if(empty($arrys_result)){
                $has_leader=false;
            }

        //---------------------------------------------------
        //已評分的書籍
        //---------------------------------------------------

            $has_comment_books=[];
            $query_sql="
                SELECT `book_sid`
                FROM `mssr_rec_comment_log`
                WHERE 1=1
                    AND `user_id`={$sess_user_id}
                    AND `comment_to`={$user_id}
                GROUP BY `book_sid`
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
            if(!empty($arrys_result)){
                foreach($arrys_result as $arrys_resul){
                    $has_comment_books[]=trim($arrys_resul['book_sid']);
                }
            }

        //---------------------------------------------------
        //SQL條件
        //---------------------------------------------------

            $user_id=(int)$user_id;

            $query_sql="
                SELECT
                    *
                FROM(
                    SELECT
                        `book_sid`,
                        `keyin_cdate`,
                        `keyin_mdate`
                    FROM `mssr_rec_book_cno`
                    WHERE 1=1
                        AND `user_id`={$user_id}
                        AND `rec_state`=1
                ) AS `sqry`
                WHERE 1=1
                #GROUP BY `sqry`.`book_sid`
                ORDER BY `sqry`.`keyin_mdate` DESC
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
            foreach($db_results as $inx=>$db_result){
                //已評分
                if(!in_array(trim($db_result['book_sid']),$has_comment_books)&&$view==='has_comment'){
                    unset($db_results[$inx]);
                }

                //未評分
                if(in_array(trim($db_result['book_sid']),$has_comment_books)&&$view==='has_no_comment'){
                    unset($db_results[$inx]);
                }
            }
            $db_results_cno=count($db_results);

    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=0;      //資料總筆數
        $psize =10;     //單頁筆數,預設10筆
        $pnos  =0;      //分頁筆數
        $pinx  =1;      //目前分頁索引,預設1
        $sinx  =0;      //值域起始值
        $einx  =0;      //值域終止值

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

        $numrow=$db_results_cno;

        if(in_array($view,array("has_comment","has_no_comment"))){
            $psize=$db_results_cno;
        }

        $pnos  =ceil($numrow/$psize);
        $pinx  =($pinx>$pnos)?$pnos:$pinx;

        $sinx  =(($pinx-1)*$psize)+1;
        $einx  =(($pinx)*$psize);
        $einx  =($einx>$numrow)?$numrow:$einx;
        //echo $numrow."<br/>";

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,教師中心";

        rm_dir($path="tmp1",$fso_enc);

        if($numrow!==0){
            $arrys_chunk =array_chunk($db_results,$psize);
            $arrys_result=$arrys_chunk[$pinx-1];
            if($view==='all')$arrys_result=$db_results;
            page_hrs($title);
            die();
        }else{
            page_nrs($title);
            die();
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

        global $arrys_result;
        global $config_arrys;
        global $conn_user;
        global $conn_mssr;

        global $sess_user_id;
        global $auth_sys_check_lv;
        global $get_book_sid;
        global $user_id;
        global $view;
        global $update_open_publish;
        global $has_leader;
        global $arry_ftp1_info;
        global $scrolltop;
//echo "<Pre>";
//print_r($scrolltop);
//echo "</Pre>";
    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0; //欄位個數
        $btn_nos=0; //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        $user_id=(int)$user_id;
        $get_user_info=get_user_info($conn_user,$user_id,$array_filter=array("name"),$arry_conn_user);
        if(!empty($get_user_info)){
            $rs_user_name=trim($get_user_info[0]['name']);
        }

        //推薦類型陣列
        $arrys_rec_type=array(
            trim('draw  ')=>trim('繪圖'),
            trim('text  ')=>trim('文字'),
            trim('record')=>trim('錄音')
        );

        $arry_record_info=array();

        $ftp_root="public_html/mssr/info/user/".(int)$user_id."/book";
        $http_path="http://".$arry_ftp1_info['host']."/mssr/info/user/".(int)$user_id."/book/";

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
    <link rel="stylesheet" type="text/css" href="../../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/jquery/plugin/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/jquery/ui/code.js"></script>

    <script type="text/javascript" src="../../../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/js/table/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" href="../../../../inc/rec/audio_player/code.css">
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../../css/def.css" media="all" />
</Head>

<Body>

    <!-- 資料列表 開始 -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="350px" align="center" valign="top">
            <!-- 內容 -->
                <!-- 統計資料表格 開始 -->
                <div class="mod_data_tbl_outline" style="margin-top:35px;">
                    <table id="mod_data_tbl" border="0" width="95%" height="40px" cellpadding="5" cellspacing="0" style="" class="font-weight1 font-family1 fc_green0">
                        <tr align="center" valign="middle" class="fsize_16">
                            <td align="center" width="470px">
                                <span class="fsize_16">
                                    <span class="fc_red1">●請注意! 此頁為新開頁面，</span>
                                    <input type="button" value="請按我關閉" class="ibtn_gr9030" onclick="parent.close();" onmouseover="this.style.cursor='pointer'">
                                    <input type="button" value="請按我重新整理" class="ibtn_gr12030" onclick="location.reload();" onmouseover="this.style.cursor='pointer'">
                                    <input type="button" value="PDF檢視" class="ibtn_gr9030" onmouseover="this.style.cursor='pointer'"
                                    onclick="view_pdf();"
                                    >
                                    <input type="button" value="WORD下載" class="ibtn_gr9030" onmouseover="this.style.cursor='pointer'"
                                    onclick="view_word();"
                                    >
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- 統計資料表格 結束 -->

                <!-- 資料表格 開始 -->
                <div class="mod_data_tbl_outline" style="margin-top:35px;">
                    <table border="0" width="100%">
                        <tr valign="middle">
                            <td align="center">
                                <!-- 分頁列 -->
                                <?php if($view!=='all'):?><span id="page1" style="position:relative;margin-top:10px;float:left;clear:left;"></span><?php endif;?>
                                <span style='position:relative;left:-50px;top:10px;color:#804000;'>
                                    <?php //if($view!=='all'):?>
                                        <input type="button" value="全部已閱" onclick="ajax_read_all();">
                                    <?php //endif;?>
                                    <!-- <input type="checkbox" style='position:relative;top:3px;margin-right:3px;' <?php if($view==='all')echo 'checked';?>
                                    onclick="location.href='content.php?user_id=<?php echo $user_id;?>&view=all';void(0);">觀看全部作品 -->
                                    <select style="height:23px;" onchange="view_mode(this);">
                                        <option value="" selected disabled>選擇觀看模式
                                        <option value="all" onclick="location.href='content.php?user_id=<?php echo $user_id;?>&view=all';void(0);"
                                        <?php if($view==='all')echo 'selected';?>>觀看全部作品

                                        <option value="has_comment" onclick="location.href='content.php?user_id=<?php echo $user_id;?>&view=has_comment';void(0);"
                                        <?php if($view==='has_comment')echo 'selected';?>>觀看已評作品

                                        <option value="has_no_comment" onclick="location.href='content.php?user_id=<?php echo $user_id;?>&view=has_no_comment';void(0);"
                                        <?php if($view==='has_no_comment')echo 'selected';?>>觀看未評作品
                                    </select>
                                    <input type="radio" name='all_control' style='position:relative;top:3px;margin-right:3px;display:none;' onclick="all_control(1);"><!-- 全部切換為詳細指導 -->
                                    <input type="radio" name='all_control' style='position:relative;top:3px;margin-right:3px;display:none;' onclick="all_control(2);" checked><!-- 全部切換為簡易閱覽 -->
                                </span>
                            </td>
                        </tr>
                        <?php if($update_open_publish===2):?>
                            <tr valign="top">
                                <td align="center" valign="top">
                                    <div style="margin-top:20px;">
                                        <span style="display:inline-block;width:25px;height:25px;background-color:#696969;"></span>
                                        <span style="display:inline-block;height:25px;font-size:13pt;position:relative;bottom:5px;color:#8e4408;"><b>已開放上架</b></span>

                                        <span style="display:inline-block;width:25px;height:25px;background-color:#dbdbdb;"></span>
                                        <span style="display:inline-block;height:25px;font-size:13pt;position:relative;bottom:5px;color:#8e4408;"><b>未開放上架</b></span>
                                    </div>
                                </td>
                            </tr>
                        <?php endif;?>
                    </table>
                    <?php foreach($arrys_result as $arrys_inx=>$arry_result) :?>
                    <?php
                    //---------------------------------------------------
                    //接收欄位
                    //---------------------------------------------------

                        extract($arry_result, EXTR_PREFIX_ALL, "rs");

                    //---------------------------------------------------
                    //處理欄位
                    //---------------------------------------------------

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

                            $rs_user_name=trim($rs_user_name);

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
                            $rec_draw_info=get_rec_info($conn_mssr,$user_id,trim($rs_book_sid),$rec_type='draw',$array_filter=array("rec_sid"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                            if(!empty($rec_draw_info)){
                                foreach($rec_draw_info as $inx=>$arry){
                                    //匯入推薦識別碼陣列
                                    array_push($arrys_rec_sid,trim($arry['rec_sid']));
                                }
                                //圖片識別碼
                                $rs_rec_draw_sid=trim($rec_draw_info[0]['rec_sid']);
                            }

                            //$root         =str_repeat("../",6)."info/user/".(int)$user_id."/book";

                            //手繪
                            $draw_path      ="{$ftp_root}/".trim($rs_book_sid)."/draw/bimg/1.jpg";
                            //$draw_path_enc  =mb_convert_encoding($draw_path,$fso_enc,$page_enc);
                            $arry_ftp_file_draw_path=ftp_nlist($ftp_conn,$draw_path);

                            //上傳
                            $up_load_draw_path_1    ="{$ftp_root}/".trim($rs_book_sid)."/draw/bimg/upload_1.jpg";
                            //$up_load_draw_path_1_enc=mb_convert_encoding($up_load_draw_path_1,$fso_enc,$page_enc);
                            $up_load_draw_path_2    ="{$ftp_root}/".trim($rs_book_sid)."/draw/bimg/upload_2.jpg";
                            //$up_load_draw_path_2_enc=mb_convert_encoding($up_load_draw_path_2,$fso_enc,$page_enc);
                            $up_load_draw_path_3    ="{$ftp_root}/".trim($rs_book_sid)."/draw/bimg/upload_3.jpg";
                            //$up_load_draw_path_3_enc=mb_convert_encoding($up_load_draw_path_3,$fso_enc,$page_enc);
                            $arry_ftp_file_up_load_draw_path_1=ftp_nlist($ftp_conn,$up_load_draw_path_1);
                            $arry_ftp_file_up_load_draw_path_2=ftp_nlist($ftp_conn,$up_load_draw_path_2);
                            $arry_ftp_file_up_load_draw_path_3=ftp_nlist($ftp_conn,$up_load_draw_path_3);

                            if((!empty($arry_ftp_file_draw_path))||(!empty($arry_ftp_file_up_load_draw_path_1))||(!empty($arry_ftp_file_up_load_draw_path_2))||(!empty($arry_ftp_file_up_load_draw_path_3))){
                                $has_draw=true;
                            }

                        //-----------------------------------------------
                        //查找, 星評資訊
                        //-----------------------------------------------

                            $has_star=false;
                            $rs_rec_star_rank='尚未選擇星等 !';
                            $rs_rec_star_reason='尚未選擇評星理由 !';
                            $rec_star_info=get_rec_info($conn_mssr,$user_id,trim($rs_book_sid),$rec_type='star',$array_filter=array("rec_sid","rec_rank","rec_reason"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                            if(!empty($rec_star_info)){
                                foreach($rec_star_info as $inx=>$arry){
                                    //匯入推薦識別碼陣列
                                    array_push($arrys_rec_sid,trim($arry['rec_sid']));
                                }
                                //星評識別碼
                                $rs_rec_star_sid=trim($rec_star_info[0]['rec_sid']);

                                //評星評價
                                $rs_rec_star_rank=str_repeat('★',(int)$rec_star_info[0]['rec_rank']);

                                //評星理由
                                $arrys_rs_rec_star_reason=array();
                                $rs_rec_star_reason=trim($rec_star_info[0]['rec_reason']);
                                if($rs_rec_star_reason!==''){
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
                            }

                        //-----------------------------------------------
                        //查找, 文字資訊
                        //-----------------------------------------------

                            $has_text   =false;
                            $rs_rec_text_state='';
                            $arrys_rs_rec_text_content=array('','','');
                            $rec_text_info=get_rec_info($conn_mssr,$user_id,trim($rs_book_sid),$rec_type='text',$array_filter=array("rec_sid","rec_content","rec_state"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                            if(!empty($rec_text_info)){
                                foreach($rec_text_info as $inx=>$arry){
                                    //匯入推薦識別碼陣列
                                    array_push($arrys_rec_sid,trim($arry['rec_sid']));
                                }
                                //文字識別碼
                                $rs_rec_text_sid=trim($rec_text_info[0]['rec_sid']);

                                //文字內容
                                $rs_rec_text_content=trim($rec_text_info[0]['rec_content']);
                                if(@unserialize($rs_rec_text_content)){
                                    $arrys_rs_rec_text_content=@unserialize($rs_rec_text_content);
                                }

                                //文字推薦狀態
                                $rs_rec_text_state=trim($rec_text_info[0]['rec_state']);

                                if($rs_rec_text_state==='顯示'){
                                    $has_text=true;
                                }
                            }

                        //-----------------------------------------------
                        //查找, 錄音資訊
                        //-----------------------------------------------

                            $has_record     =false;
                            $rec_record_info=get_rec_info($conn_mssr,$user_id,trim($rs_book_sid),$rec_type='record',$array_filter=array("rec_sid"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                            if(!empty($rec_record_info)){
                                foreach($rec_record_info as $inx=>$arry){
                                    //匯入推薦識別碼陣列
                                    array_push($arrys_rec_sid,trim($arry['rec_sid']));
                                }
                                //錄音識別碼
                                $rs_rec_record_sid=trim($rec_record_info[0]['rec_sid']);

                                //$root               =str_repeat("../",6)."info/user/".(int)$user_id."/book";

                                $record_path_mp3    ="{$ftp_root}/".trim($rs_book_sid)."/record/1.mp3";
                                //$record_path_mp3_enc=mb_convert_encoding($record_path_mp3,$fso_enc,$page_enc);
                                $arry_ftp_file_record_path_mp3=ftp_nlist($ftp_conn,$record_path_mp3);

                                $record_path_wav    ="{$ftp_root}/".trim($rs_book_sid)."/record/1.wav";
                                //$record_path_wav_enc=mb_convert_encoding($record_path_wav,$fso_enc,$page_enc);
                                $arry_ftp_file_record_path_wav=ftp_nlist($ftp_conn,$record_path_wav);

                                if((!empty($arry_ftp_file_record_path_mp3))||(!empty($arry_ftp_file_record_path_wav))){
                                    $has_record =true;
                                    $arry_record_info[$rs_book_sid]=$user_id;
                                }
                            }

                        //-----------------------------------------------
                        //查找, 書本推薦內容總調查計數表資訊
                        //-----------------------------------------------

                            $get_rec_book_cno_info=get_rec_book_cno_info($conn_mssr,$user_id,trim($rs_book_sid),$array_filter=array('has_publish'),$arry_conn_mssr);
                            $has_publish=trim($get_rec_book_cno_info[0]['has_publish']);

                        //-----------------------------------------------
                        //設定, 表格背景顏色
                        //-----------------------------------------------

                            $tbl_bg_color="bg_gray0";
                            $tbl_fc_color="fc_brown0";
                            if($update_open_publish===2){
                                if($has_publish==='可'){
                                    $tbl_bg_color="bg_gray1";
                                    $tbl_fc_color="fc_white0";
                                }
                            }

                        //-----------------------------------------------
                        //查找, 提取老師對推薦內容評論表資訊
                        //-----------------------------------------------

                            $has_rec_comment=false;
                            $arry_has_rec_comment=array();
                            $get_rec_comment_log_info=get_rec_comment_log_info($conn_mssr,$sess_user_id,$arrys_rec_sid,$array_filter=array('comment_type','comment_score','has_del_rec','comment_coin','comment_content','keyin_cdate'),$arry_limit=array(),$arry_conn_mssr);
                            if(!empty($get_rec_comment_log_info)){
                                foreach($get_rec_comment_log_info as $inx=>$arry_rec_comment){
                                    $comment_type=trim($arry_rec_comment['comment_type']);
                                    $arry_has_rec_comment[]=$comment_type;
                                }
                                $has_rec_comment=true;
                            }

                        //-----------------------------------------------
                        //查找, 是否已有被閱讀紀錄
                        //-----------------------------------------------

                            $sql="
                                SELECT
                                    `book_sid`
                                FROM `mssr_rec_teacher_read`
                                WHERE 1=1
                                    AND `user_id`   = {$user_id }
                                    AND `book_sid`  ='{$rs_book_sid}'
                                    AND `read_state`=1;
                            ";
                            $has_read_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                            if(!empty($has_read_result)){
                                $has_read=true;
                            }else{
                                $has_read=false;
                            }

                        //-----------------------------------------------
                        //查找, 是否已被納入優秀作品
                        //-----------------------------------------------

                            $sql="
                                SELECT `book_sid`
                                FROM `mssr_rec_book_best_class`
                                WHERE 1=1
                                    AND `user_id`   = {$user_id }
                                    AND `book_sid`  ='{$rs_book_sid}'
                            ";
                            $has_best_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                            if(!empty($has_best_result)){
                                $has_best="✔已納入班級優秀作品";
                            }else{
                                $has_best="納入班級優秀作品";
                            }
                    ?>
                    <!-- 指導模式 開始 -->
                    <?php if($has_leader):?>
                        <table width="93%" cellpadding="0" cellspacing="0" border="0" width="100%"
                        style='margin-top:25px;font-family:"微軟正黑體","標楷體","新細明體";font-size:16px;font-weight:700;color:#87CDDC;'/>
                            <tr>
                                <td width="65px"><!-- 模式： --></td>
                                <td width="">
                                    <!-- <select id="" name="" size="" style="position:relative;top:1px;border:1px solid #87CDDC;"
                                    onchange="chahge_rec_mode(this,<?php echo $arrys_inx;?>,<?php if($has_text){echo 1;}else{echo 0;}?>);void(0);">
                                        <option value="1" selected>簡易
                                        <option value="2">一般
                                    </select> -->
                                    <input type="button" value="切換為詳細指導" onclick="btn_chahge_rec_mode(this,<?php echo $arrys_inx;?>,<?php if($has_text){echo 1;}else{echo 0;}?>);void(0);"
                                    class='btn_chahge_rec_mode' arrys_inx='<?php echo $arrys_inx;?>'
                                    style="display:none;">

                                    <input type="button" value="<?php echo $has_best;?>" onclick="rec_book_best_class(this,<?php echo $user_id;?>,'<?php echo $rs_book_sid;?>');void(0);"
                                    style="float:right;">

                                    <!-- <img src="../../../../img/user/user_rec/rec_book_best_class_help.jpg" width="" height="75" border="0" alt="0"
                                    style="border:1px solid red;position:relative;top:5px;"/> -->

                                    <!-- <input type="radio" id="" name="rec_mode_radio" value="1" checked
                                    onclick="chahge_rec_mode(1,<?php echo $arrys_inx;?>,<?php if($has_text){echo 1;}else{echo 0;}?>);void(0);">簡易
                                    <input type="radio" id="" name="rec_mode_radio" value="2"
                                    onclick="chahge_rec_mode(2,<?php echo $arrys_inx;?>,<?php if($has_text){echo 1;}else{echo 0;}?>);void(0);">一般 -->
                                </td>
                            </tr>
                        </table>
                    <?php endif;?>
                    <!-- 指導模式 結束 -->

                    <table id="tbl_<?php echo $arrys_inx;?>" width="93%" align="center" border="0" cellpadding="0" cellspacing="0" style="margin-top:10px;"
                    class="mod_data_tbl_outline tbl_<?php echo $user_id;?>_<?php echo $rs_book_sid;?>"
                    arrys_inx='<?php echo $arrys_inx;?>'
                    has_text='<?php if($has_text){echo 1;}else{echo 0;}?>'>
                        <tr>
                            <td align='left' valign="middle" bgcolor='#87CDDC'>
                                <?php if($has_leader):?>
                                    <img id="img_read_<?php echo $arrys_inx;?>" src="../../../../img/user/user_rec/read.png" width="40" height="40" border="0" alt="read"/>
                                    <input type="checkbox" id="read_list" name="read_list" value="<?php echo $user_id;?>" att="read_list_<?php echo $arrys_inx;?>"
                                    book_sid='<?php echo $rs_book_sid;?>'
                                    style='position:relative;bottom:15px;'
                                    onclick='ajax_read(this,<?php echo $user_id;?>,"<?php echo $rs_book_sid;?>","tbl_<?php echo $arrys_inx;?>");void(0);'
                                    <?php if($has_read)echo 'checked';?>>
                                <?php endif;?>

                                <?php if($has_leader):?>
                                    <img title="下載PDF" onclick="view_one_pdf('<?php echo $rs_book_sid;?>');" id="img_pdf_<?php echo $arrys_inx;?>" src="../../../../img/user/user_rec/pdf.png" width="30" height="30" border="0" alt="pdf"
                                    style="position:relative;left:15px;bottom:5px;cursor: pointer;"/>
                                    <input type="checkbox" id="pdf_list" name="pdf_list" value="<?php echo $rs_book_sid;?>" att="pdf_list_<?php echo $arrys_inx;?>"
                                    style='position:relative;left:15px;bottom:15px;'
                                    >
                                <?php endif;?>
                            </td>
                            <td align='right' valign="middle" bgcolor='#87CDDC'>
                                <?php if(($has_draw)||($has_text)||($has_record)):?>
                                    <?php if($has_leader):?>
                                        <img id="img_trash_<?php echo $arrys_inx;?>" src="../../../../img/user/user_rec/trash.png" width="40" height="40" border="0" alt="trash"
                                        onmouseover='this.style.cursor="pointer"'
                                        onclick='trash(<?php echo $user_id;?>,"<?php echo $rs_book_sid;?>","tbl_<?php echo $arrys_inx;?>");void(0);'/>
                                    <?php endif;?>
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
                                                        首次推薦時間：<?php echo htmlspecialchars($rs_keyin_cdate);?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        書籍名稱：<?php echo htmlspecialchars($rs_book_name);?>
                                                    </td>
                                                    <td>
                                                        最後更新時間：<?php echo htmlspecialchars($rs_keyin_mdate);?>
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
<?php if($rs_rec_text_state==='顯示'&&trim($arrys_rs_rec_text_content[0])!==''):?>
【最喜歡的一句話】
<?php echo htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[0])));?>
<?php endif;?>


<?php if($rs_rec_text_state==='顯示'&&trim($arrys_rs_rec_text_content[1])!==''):?>
【書本內容介紹】
<?php echo htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[1])));?>
<?php endif;?>


<?php if($rs_rec_text_state==='顯示'&&trim($arrys_rs_rec_text_content[2])!==''):?>
【書中所學到的事】
<?php echo htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[2])));?>
<?php endif;?>

<?php
//if($user_id===5030){
//echo "<Pre>";
//print_r($arrys_rs_rec_text_content);
//echo "</Pre>";
//}
?>
<?php if(trim($arrys_rs_rec_text_content[0])===''&&trim($arrys_rs_rec_text_content[1])===''&&trim($arrys_rs_rec_text_content[2])===''):?>
             尚未填寫文字推薦！
<?php endif;?>
                                                </textarea>
                                            </span>
                                        </td>
                                    </tr>
                                    <!-- 文字指導 -->
                                    <tr align="center" valign="top" class="<?php echo $tbl_bg_color;?>">
                                        <td>
                                            <?php //if(in_array($auth_sys_check_lv,array(5,14,22))):?>
                                                <?php if(($has_text)&&($sess_user_id!==$user_id)&&($has_leader)):?>
                                                    <?php if(in_array('text',$arry_has_rec_comment)):?>
                                                        <input style="display:none;" id="btn_text_<?php echo $arrys_inx;?>" type="button" value="✔老師指導(文字已指導)" class="" onclick="detailed('single','text','<?php echo addslashes($rs_rec_text_sid);?>',<?php echo addslashes($user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')" onmouseover="this.style.cursor='pointer'">
                                                    <?php else:?>
                                                        <input style="display:none;" id="btn_text_<?php echo $arrys_inx;?>" type="button" value="老師指導(文字)" class="" onclick="detailed('single','text','<?php echo addslashes($rs_rec_text_sid);?>',<?php echo addslashes($user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')" onmouseover="this.style.cursor='pointer'">
                                                    <?php endif;?>
                                                    <input style="display:none;" id="btn_del_text_<?php echo $arrys_inx;?>" type="button" value="直接刪除推薦" class=""
                                                    onclick="del('single','text','<?php echo addslashes($rs_rec_text_sid);?>',<?php echo addslashes($user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')"
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
                                    </tr>
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
                                                <img src="<?php echo $http_path.trim($rs_book_sid)."/draw/bimg/1.jpg";?>" width="355px" height="283px" border="0" alt="畫圖資訊"/>
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
                                                                <img src="<?php echo $http_path.trim($rs_book_sid)."/draw/bimg/1.jpg";?>" width="70px" height="56px" border="0" alt="畫圖資訊" style="width:70px;height:56px;" onclick="change_rec_img(this,<?php echo $arrys_inx;?>);void(0);"/>
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
                                                                <img src="<?php echo $http_path.trim($rs_book_sid)."/draw/bimg/upload_1.jpg";?>" width="70px" height="56px" border="0" alt="畫圖資訊" style="width:70px;height:56px;" onclick="change_rec_img(this,<?php echo $arrys_inx;?>);void(0);"/>
                                                            <?php else:?>
                                                                <!-- <img src="../../../../img/user/user_rec/rec_up_load_draw_noimg_s.png"
                                                                width="70px" height="56px" border="0" alt="畫圖資訊" style="width:70px;height:56px;" onclick="change_rec_img(this,<?php echo $arrys_inx;?>);void(0);"/> -->
                                                            <?php endif;?>
                                                        </span>
                                                    </td>
                                                    <td width="100px">
                                                        <span style="display:block;border:0px solid red;width:70px;height:56px;" onmouseover='this.style.cursor="pointer"'>
                                                            <?php if((!empty($arry_ftp_file_up_load_draw_path_2))):?>
                                                                <img src="<?php echo $http_path.trim($rs_book_sid)."/draw/bimg/upload_2.jpg";?>" width="70px" height="56px" border="0" alt="畫圖資訊" style="width:70px;height:56px;" onclick="change_rec_img(this,<?php echo $arrys_inx;?>);void(0);"/>
                                                            <?php else:?>
                                                                <!-- <img src="../../../../img/user/user_rec/rec_up_load_draw_noimg_s.png"
                                                                width="70px" height="56px" border="0" alt="畫圖資訊" style="width:70px;height:56px;" onclick="change_rec_img(this,<?php echo $arrys_inx;?>);void(0);"/> -->
                                                            <?php endif;?>
                                                        </span>
                                                    </td>
                                                    <td width="100px">
                                                        <span style="display:block;border:0px solid red;width:70px;height:56px;" onmouseover='this.style.cursor="pointer"'>
                                                            <?php if((!empty($arry_ftp_file_up_load_draw_path_3))):?>
                                                                <img src="<?php echo $http_path.trim($rs_book_sid)."/draw/bimg/upload_3.jpg";?>" width="70px" height="56px" border="0" alt="畫圖資訊" style="width:70px;height:56px;" onclick="change_rec_img(this,<?php echo $arrys_inx;?>);void(0);"/>
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
                                    <!-- 畫圖指導 -->
                                    <?php //if(in_array($auth_sys_check_lv,array(5,14,22))):?>
                                        <?php if(($has_draw)&&($sess_user_id!==$user_id)&&($has_leader)):?>
                                        <tr align="center" valign="top" class="<?php echo $tbl_bg_color;?>">
                                            <td>
                                                <?php if(in_array('draw',$arry_has_rec_comment)):?>
                                                    <input style="display:none;" id="btn_draw_<?php echo $arrys_inx;?>" type="button" value="✔老師指導(畫圖已指導)" class="" onclick="detailed('single','draw','<?php echo addslashes($rs_rec_draw_sid);?>',<?php echo addslashes($user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')" onmouseover="this.style.cursor='pointer'">
                                                <?php else:?>
                                                    <input style="display:none;" id="btn_draw_<?php echo $arrys_inx;?>" type="button" value="老師指導(畫圖)" class="" onclick="detailed('single','draw','<?php echo addslashes($rs_rec_draw_sid);?>',<?php echo addslashes($user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')" onmouseover="this.style.cursor='pointer'">
                                                <?php endif;?>
                                                <input style="display:none;" id="btn_del_draw_<?php echo $arrys_inx;?>" type="button" value="直接刪除推薦" class=""
                                                onclick="del('single','draw','<?php echo addslashes($rs_rec_draw_sid);?>',<?php echo addslashes($user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')"
                                                onmouseover="this.style.cursor='pointer'">
                                            </td>
                                        </tr>
                                        <?php endif;?>
                                    <?php //endif;?>
                                </table>

                                <!-- 錄音資訊 -->
                                <table cellpadding="0" cellspacing="0" border="0" width="100%" height="100px" style="padding:3px;"/>
                                    <tr align="left" valign="middle" class="<?php echo $tbl_bg_color;?>">
                                        <td>
                                            <?php if(($has_record)&&($sess_user_id!==$user_id)&&($has_leader)):?>
                                                <!-- <span class="fsize_18 font-weight1 <?php echo $tbl_fc_color;?>" style="position:relative;padding:5px;top:5px;">
                                                    <audio id="audio" controls="controls">
                                                        <source id="record1" src="<?php echo $record_path_wav;?>"/>
                                                        <source id="record2" src="<?php echo $record_path_mp3;?>"/>
                                                            無法播放，建議使用 chrome 瀏覽器獲得更佳體驗。
                                                    </audio>
                                                </span> -->
                                                <div id="player" class='player_<?php echo $user_id;?>_<?php echo $rs_book_sid;?>' style='float:left;'>
                                                    <div class="ctrl" id='ctrl_<?php echo $user_id;?>_<?php echo $rs_book_sid;?>'>
                                                        <div class="control" id='control_<?php echo $user_id;?>_<?php echo $rs_book_sid;?>'>
                                                            <div class="left" id='left_<?php echo $user_id;?>_<?php echo $rs_book_sid;?>'>
                                                                <div class="playback icon" id='playback_<?php echo $user_id;?>_<?php echo $rs_book_sid;?>'></div>
                                                            </div>
                                                            <div class="volume right" id='volume_<?php echo $user_id;?>_<?php echo $rs_book_sid;?>1'>
                                                                <div class="mute icon left" id='mute_<?php echo $user_id;?>_<?php echo $rs_book_sid;?>'></div>
                                                                <div class="slider left" id='slider_<?php echo $user_id;?>_<?php echo $rs_book_sid;?>'>
                                                                    <div class="pace" id='pace_<?php echo $user_id;?>_<?php echo $rs_book_sid;?>'></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="progress" id='progress_<?php echo $user_id;?>_<?php echo $rs_book_sid;?>'>
                                                            <div class="slider" id='slider_<?php echo $user_id;?>_<?php echo $rs_book_sid;?>'>
                                                                <div class="loaded" id='loaded_<?php echo $user_id;?>_<?php echo $rs_book_sid;?>'></div>
                                                                <div class="pace" id='pace_<?php echo $user_id;?>_<?php echo $rs_book_sid;?>'></div>
                                                            </div>
                                                            <div class="timer left" id='timer_<?php echo $user_id;?>_<?php echo $rs_book_sid;?>'
                                                            style='position:relative;bottom:3px;'>0:00</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 錄音指導 -->
                                                <br/><br/>
                                                <span style="position:relative;bottom:5px;left:5px;">
                                                    <?php //if(in_array($auth_sys_check_lv,array(5,14,22))):?>
                                                        <?php if(in_array('record',$arry_has_rec_comment)):?>
                                                            <input style="display:none;" id="btn_record_<?php echo $arrys_inx;?>" type="button" value="✔老師指導(錄音已指導)" class="" onclick="detailed('single','record','<?php echo addslashes($rs_rec_record_sid);?>',<?php echo addslashes($user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')" onmouseover="this.style.cursor='pointer'">
                                                        <?php else:?>
                                                            <input style="display:none;" id="btn_record_<?php echo $arrys_inx;?>" type="button" value="老師指導(錄音)" class="" onclick="detailed('single','record','<?php echo addslashes($rs_rec_record_sid);?>',<?php echo addslashes($user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')" onmouseover="this.style.cursor='pointer'">
                                                        <?php endif;?>
                                                        <input style="display:none;" id="btn_del_record_<?php echo $arrys_inx;?>" type="button" value="直接刪除推薦" class=""
                                                        onclick="del('single','record','<?php echo addslashes($rs_rec_record_sid);?>',<?php echo addslashes($user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')"
                                                        onmouseover="this.style.cursor='pointer'">
                                                    <?php //endif;?>
                                                </span>
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
                        <?php if($update_open_publish===2):?>
                        <tr valign="middle">
                            <td align="center" colspan="2">
                                <!-- 基本資訊 -->
                                <table cellpadding="0" cellspacing="0" border="0" width="100%" height="40px" style="padding:3px;"/>
                                    <tr align="center" valign="middle" class="<?php echo $tbl_bg_color;?> fsize_18 <?php echo $tbl_fc_color;?>">
                                        <td>
                                            <span class="font-weight1">
                                                開放學生上架此本書籍 :
                                                <select id="has_publish" name="has_publish" class="form_select" onchange="confirm_has_publish('single',this.options[this.options.selectedIndex].value,<?php echo addslashes($user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>');">
                                                    <option value="可" <?php echo ($has_publish==='可')?'selected':'';?>>開放
                                                    <option value="否" <?php echo ($has_publish==='否')?'selected':'';?>>關閉
                                                </select>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <?php endif;?>

                        <!-- 教師評論紀錄 -->
                        <?php if($has_rec_comment):?>
                        <tr valign="middle">
                            <td align="center" colspan="2">
                                <!-- 基本資訊 -->
                                <table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding:3px;"/>
                                    <tr align="center" valign="middle" class="<?php echo $tbl_bg_color;?> fsize_18 <?php echo $tbl_fc_color;?>">
                                        <td height="30px">
                                            <input type="button" name="view_comment_logo" value="點擊展開歷史紀錄，共有(<?php echo count($get_rec_comment_log_info);?>)筆紀錄"
                                            onclick="view_comment_logo(<?php echo $arrys_inx;?>);" onmouseover="this.style.cursor='pointer'">
                                        </td>
                                    </tr>
                                </table>
                                <table id="tbl_comment_<?php echo $arrys_inx;?>" cellpadding="0" cellspacing="0" border="1" width="100%" style="padding:3px;display:none;"/>
                                    <?php foreach($get_rec_comment_log_info as $inx=>$arry_filed):?>
                                    <?php
                                        $comment_type   =trim($arry_filed[trim('comment_type   ')]);
                                        $comment_score  =trim($arry_filed[trim('comment_score  ')]);
                                        $has_del_rec    =trim($arry_filed[trim('has_del_rec    ')]);
                                        $comment_coin   =trim($arry_filed[trim('comment_coin   ')]);
                                        $comment_content=trim($arry_filed[trim('comment_content')]);
                                        $keyin_cdate    =trim($arry_filed[trim('keyin_cdate    ')]);

                                        $comment_score  =(int)$comment_score;
                                        $comment_coin   =(int)$comment_coin;
                                        $keyin_cdate    =date("Y-m-d",strtotime($keyin_cdate));

                                        if(mb_strlen($comment_content)>28){
                                            $comment_content=mb_substr($comment_content,0,28)."..";
                                        }
                                    ?>
                                    <tr align="center" valign="middle" class="<?php echo $tbl_bg_color;?> <?php echo $tbl_fc_color;?>">
                                        <td width="40px" height="30px" align="left">
                                            <span class="font-weight1 <?php echo $tbl_fc_color;?>" style="position:relative;padding:5px;">
                                                <?php if(isset($arrys_rec_type[$comment_type])):?>
                                                    <?php echo $arrys_rec_type[$comment_type];?>
                                                <?php endif;?>
                                            </span>
                                        </td>
                                        <td width="60px" height="30px" align="left">
                                            <span class="<?php echo $tbl_fc_color;?>" style="position:relative;padding:5px;">
                                                評分: <?php echo $comment_score;?>
                                            </span>
                                        </td>
                                        <td width="95px" height="30px" align="left">
                                            <span class="<?php echo $tbl_fc_color;?>" style="position:relative;padding:5px;">
                                                刪除推薦: <?php echo htmlspecialchars($has_del_rec);?>
                                            </span>
                                        </td>
                                        <td width="95px" height="30px" align="left">
                                            <span class="<?php echo $tbl_fc_color;?>" style="position:relative;padding:5px;">
                                                獎懲: <?php echo $comment_coin;?>$
                                            </span>
                                        </td>
                                        <td width="" height="30px" align="left">
                                            <span class="<?php echo $tbl_fc_color;?>" style="position:relative;padding:5px;">
                                                留言: <?php echo htmlspecialchars($comment_content);?>
                                            </span>
                                        </td>
                                        <td width="95px" height="30px" align="left">
                                            <span class="<?php echo $tbl_fc_color;?>" style="position:relative;padding:5px;">
                                                <?php echo htmlspecialchars($keyin_cdate);?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach;?>
                                </table>
                            </td>
                        </tr>
                        <?php endif;?>

                    </table>
                    <?php endforeach ;?>

                    <table border="0" width="100%">
                        <tr valign="middle">
                            <td align="left">
                                <!-- 分頁列 -->
                                <?php if($view!=='all'):?><span id="page2" style="position:relative;margin-top:10px;"></span><?php endif;?>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- 資料表格 結束 -->
            <!-- 內容 -->
            </td>
        </tr>
    </table>
    <!-- 資料列表 結束 -->
<?php
//echo "<Pre>";
//print_r($arry_record_info);
//echo "</Pre>";
?>
<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;
    var user_id=<?php echo $user_id;?>;
    var get_book_sid='<?php echo trim($get_book_sid);?>';
    var json_record_info=<?php echo json_encode($arry_record_info,true);?>;
    var get_scrolltop=<?php echo $scrolltop;?>;

    function view_mode(obj){
        var val=obj.options[obj.options.selectedIndex].value;
        if(val==='has_comment')location.href='content.php?user_id=<?php echo $user_id;?>&view=has_comment';
        if(val==='all')location.href='content.php?user_id=<?php echo $user_id;?>&view=all';
        if(val==='has_no_comment')location.href='content.php?user_id=<?php echo $user_id;?>&view=has_no_comment';
    }

    function ajax_read_all(){
        var user_id =parseInt(<?php echo $user_id;?>);
        var oread_lists=document.getElementsByName('read_list');
        for(key in oread_lists){
            var oread_list=oread_lists[key];
            var book_sid=trim($(oread_list).attr('book_sid'));
            var flag='true';
            //ajax
            $.post('readA.php',{
                user_id :encodeURI(user_id  ),
                book_sid:encodeURI(book_sid ),
                flag    :encodeURI(flag     )
            },"json")
            .success(function(respones){
                var respones=jQuery.parseJSON(respones);
                //alert('已全部設定已閱!');
                location.reload();
            })
            .error(function(e){
                $.blockUI({
                    message:'<h2 class="fc_white0">存取發生問題，請再儲存一次 !</h2>',
                    css: {
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity:.8,
                        color: '#437C85',
                        top: '200px'
                    }
                });
                setTimeout($.unblockUI, 500);
            })
            .complete(function(e){
            });
        }
    }

    function rec_book_best_class(obj,user_id,book_sid){

        if(!confirm('你確定要納入嗎?')){
            return false;
        }

        var user_id =parseInt(user_id);
        var book_sid=trim(book_sid);

        $.post('rec_book_best_classA.php',{
            user_id :encodeURI(user_id  ),
            book_sid:encodeURI(book_sid )
        },"json")
        .success(function(respones){
            alert(respones);
            $(obj).val('✔已納入班級優秀作品');
            return false;
            //var respones=jQuery.parseJSON(respones);
        })
        .error(function(e){
            $.blockUI({
                message:'<h2 class="fc_white0">存取發生問題，請再試一次 !</h2>',
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity:.8,
                    color: '#437C85',
                    top: '200px'
                }
            });
            setTimeout($.unblockUI, 500);
            return false;
        })
        .complete(function(e){
        });
    }

    $('.btn_single_del_text').change(function(){
    //個別刪除文字推薦
        var obj    =$(this)[0];
        var rec_sid=trim(obj.getAttribute('rec_sid'));
        var no     =parseInt(obj.value);

        if(isNaN(no)){
            alert('請選擇刪除項目');
            return false;
        }

        //ajax
        $.get('../del/single_del_textA.php',{
            rec_sid :encodeURI(rec_sid  ),
            no      :encodeURI(no       )
        },"json")
        .success(function(respones){
            window.localStorage["scrollTop"]=$(window.parent.document).scrollTop();
            location.reload();
            return true;
        })
        .error(function(e){
        })
        .complete(function(e){
        });
    });


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

    function ajax_read(obj,user_id,book_sid,anchor){

        var user_id =parseInt(user_id);
        var book_sid=trim(book_sid);

        var chk_flag=obj.checked;
        var flag='fasle';
        if(chk_flag){
            flag='true';
        }

        //ajax
        $.post('readA.php',{
            user_id :encodeURI(user_id  ),
            book_sid:encodeURI(book_sid ),
            flag    :encodeURI(flag     )
        },"json")
        .success(function(respones){
            var respones=jQuery.parseJSON(respones);
            //$.blockUI({
            //    message:'<h2 class="fc_white0">儲存成功 !</h2>',
            //    css: {
            //        border: 'none',
            //        padding: '15px',
            //        backgroundColor: '#000',
            //        '-webkit-border-radius': '10px',
            //        '-moz-border-radius': '10px',
            //        opacity:.8,
            //        color: '#437C85',
            //        top: '200px'
            //    }
            //});
            //setTimeout($.unblockUI, 500);
        })
        .error(function(e){
            $.blockUI({
                message:'<h2 class="fc_white0">存取發生問題，請再儲存一次 !</h2>',
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity:.8,
                    color: '#437C85',
                    top: '200px'
                }
            });
            setTimeout($.unblockUI, 500);
        })
        .complete(function(e){
        });
    }

    function trash(user_id,book_sid,anchor){

        var url ='';
        var page=str_repeat('../',0)+'delA.php';
        var arg ={
            'user_id' : parseInt(user_id),
            'book_sid': trim(book_sid   ),
            'anchor'  : trim(anchor     )
        };
        var _arg=[];
        for(var key in arg){
            _arg.push(key+"="+encodeURI(arg[key]));
        }
        arg=_arg.join("&");

        if(arg.length!=0){
            url+=page+"?"+arg;
        }else{
            url+=page;
        }

        if(confirm('你確定要直接刪除整個推薦嗎? 會同時給予所有類型的推薦1分以及各扣除100葵幣 !')){
            //console.log(url);
            go(url,'self');
        }
        else{
            return false;
        }
    }

    function all_control(val){
        var val=parseInt(val);
        if(val===1){
        //切換為詳細
            $(".btn_chahge_rec_mode").val('切換為簡易閱覽');

            //$("[name='read_list']").hide();
            //$("[name='pdf_list']").hide();
            //
            //$("[id^='img_pdf_']").hide();
            //$("[id^='img_read_']").hide();
            //$("[id^='img_trash_']").hide();

            $("[id^='btn_draw_']").show();
            $("[id^='btn_del_draw_']").show();

            $("[id^='btn_text_']").show();
            $("[id^='btn_del_text_']").show();
            $('.btn_single_del_text').show();

            $("[id^='btn_record_']").show();
            $("[id^='btn_del_record_']").show();

            //$("[id^='tbl_draw_']")[0].style.height="510px";
        }else{
        //切換為簡易
            $(".btn_chahge_rec_mode").val('切換為詳細指導');

            $("[name='read_list']").show();
            $("[name='pdf_list']").show();

            $("[id^='img_pdf_']").show();
            $("[id^='img_read_']").show();
            $("[id^='img_trash_']").show();

            $("[id^='btn_draw_']").hide();
            $("[id^='btn_del_draw_']").hide();

            $("[id^='btn_text_']").hide();
            $("[id^='btn_del_text_']").hide();
            $('.btn_single_del_text').hide();

            $("[id^='btn_record_']").hide();
            $("[id^='btn_del_record_']").hide();

            //$("[id^='tbl_draw_']")[0].style.height="500px";
        }
    }

    function btn_chahge_rec_mode(obj,tbl_inx,has_text){
        var mode_val        =parseInt(obj.value);
        var oimg_pdf        =document.getElementById('img_pdf_'+tbl_inx);
        var oimg_read       =document.getElementById('img_read_'+tbl_inx);
        var oread_lists     =document.getElementsByName('read_list');
        var opdf_lists      =document.getElementsByName('pdf_list');
        var oimg_trash      =document.getElementById('img_trash_'+tbl_inx);

        var obtn_draw       =document.getElementById('btn_draw_'+tbl_inx);
        var obtn_del_draw   =document.getElementById('btn_del_draw_'+tbl_inx);

        var obtn_text       =document.getElementById('btn_text_'+tbl_inx);
        var obtn_del_text   =document.getElementById('btn_del_text_'+tbl_inx);
        var obtn_single_del_text=document.getElementById('btn_single_del_text_'+tbl_inx);

        var obtn_record     =document.getElementById('btn_record_'+tbl_inx);
        var obtn_del_record =document.getElementById('btn_del_record_'+tbl_inx);

        var otbl_draw=document.getElementById('tbl_draw_'+tbl_inx);

        try{
            if(obj.value==='切換為簡易閱覽'){
                obj.value='切換為詳細指導';
                for(var i=0;i<oread_lists.length;i++){
                    var oread_list=oread_lists[i];
                    var opdf_list =opdf_lists[i];
                    var att=oread_list.getAttribute('att');
                    if((att)===('read_list_'+tbl_inx)){
                        $(oread_list).show();
                        $(opdf_list).show();
                    }
                }
                $(oimg_pdf).show();
                $(oimg_read).show();
                $(oimg_trash).show();

                $(obtn_draw).hide();
                $(obtn_del_draw).hide();

                $(obtn_text).hide();
                $(obtn_del_text).hide();
                $(obtn_single_del_text).hide();

                $(obtn_record).hide();
                $(obtn_del_record).hide();

                //otbl_draw.style.height="500px";
            }else{
                obj.value='切換為簡易閱覽';
                for(var i=0;i<oread_lists.length;i++){
                    var oread_list=oread_lists[i];
                    var opdf_list =opdf_lists[i];
                    var att=oread_list.getAttribute('att');
                    if((att)===('read_list_'+tbl_inx)){
                        $(oread_list).hide();
                        $(opdf_list).hide();
                    }
                }
                $(oimg_pdf).hide();
                $(oimg_read).hide();
                $(oimg_trash).hide();

                $(obtn_draw).show();
                $(obtn_del_draw).show();

                $(obtn_text).show();
                $(obtn_del_text).show();
                $(obtn_single_del_text).show();

                $(obtn_record).show();
                $(obtn_del_record).show();

                //if(has_text===1)otbl_draw.style.height="525px";
            }
        }catch(e){
        }
    }

    function chahge_rec_mode(obj,tbl_inx,has_text){
    //變更指導模式
        var mode_val        =parseInt(obj.value);
        var oimg_read       =document.getElementById('img_read_'+tbl_inx);
        var oread_lists     =document.getElementsByName('read_list');
        var oimg_trash      =document.getElementById('img_trash_'+tbl_inx);

        var obtn_draw       =document.getElementById('btn_draw_'+tbl_inx);
        var obtn_del_draw   =document.getElementById('btn_del_draw_'+tbl_inx);

        var obtn_text       =document.getElementById('btn_text_'+tbl_inx);
        var obtn_del_text   =document.getElementById('btn_del_text_'+tbl_inx);
        var obtn_single_del_text=document.getElementById('btn_single_del_text_'+tbl_inx);

        var obtn_record     =document.getElementById('btn_record_'+tbl_inx);
        var obtn_del_record =document.getElementById('btn_del_record_'+tbl_inx);

        var otbl_draw=document.getElementById('tbl_draw_'+tbl_inx);

        try{
            if(mode_val===1){
                for(var i=0;i<oread_lists.length;i++){
                    var oread_list=oread_lists[i];
                    var att=oread_list.getAttribute('att');
                    if((att)===('read_list_'+tbl_inx)){
                        $(oread_list).show();
                    }
                }
                $(oimg_read).show();
                $(oimg_trash).show();

                $(obtn_draw).hide();
                $(obtn_del_draw).hide();

                $(obtn_text).hide();
                $(obtn_del_text).hide();
                $(obtn_single_del_text).hide();

                $(obtn_record).hide();
                $(obtn_del_record).hide();

                //otbl_draw.style.height="500px";
            }else{
                for(var i=0;i<oread_lists.length;i++){
                    var oread_list=oread_lists[i];
                    var att=oread_list.getAttribute('att');
                    if((att)===('read_list_'+tbl_inx)){
                        $(oread_list).hide();
                    }
                }
                $(oimg_read).hide();
                $(oimg_trash).hide();

                $(obtn_draw).show();
                $(obtn_del_draw).show();

                $(obtn_text).show();
                $(obtn_del_text).show();
                $(obtn_single_del_text).show();

                $(obtn_record).show();
                $(obtn_del_record).show();

                //if(has_text===1)otbl_draw.style.height="525px";
            }
        }catch(e){

        }
    }

    function del(area,type,rec_sid,user_id,book_sid,anchor){
    //直接刪除推薦
        var url ='';
        var page=str_repeat('../',1)+'del/delA.php';
        var arg ={
            'rec_sid'        :rec_sid,
            'comment_type'   :type,
            'comment_content':'',
            'comment_score'  :1,
            'comment_coin'   :0,
            'has_del_rec'    :'有',
            'user_id'        :user_id,
            'book_sid'       :book_sid,
            'area'           :area,
            'anchor'         :anchor,
            'psize'          :psize,
            'pinx'           :pinx,
            'scrolltop':$(window.parent).scrollTop()
        };
        var _arg=[];
        for(var key in arg){
            _arg.push(key+"="+encodeURI(arg[key]));
        }
        arg=_arg.join("&");

        if(arg.length!=0){
            url+=page+"?"+arg;
        }else{
            url+=page;
        }

        if(confirm('你確定要直接刪除推薦嗎? 會同時給予1分以及扣除100葵幣 !')){
            window.open(
                url,
                'del',
                config='width=650,height=500,toolbar=no,scrollbar=no,resizable=no,location=no'
            );
        }
        else{
            return false;
        }
    }

    function detailed(area,type,rec_sid,user_id,book_sid,anchor){
    //老師指導
        var url ='';
        var page=str_repeat('../',1)+'basic/detailedF.php';
        var arg ={
            'psize':psize,
            'pinx' :pinx,
            'area':area,
            'type':type,
            'rec_sid':rec_sid,
            'user_id':user_id,
            'book_sid':book_sid,
            'anchor':anchor,
            'scrolltop':$(window.parent).scrollTop()
        };
        var _arg=[];
        for(var key in arg){
            _arg.push(key+"="+encodeURI(arg[key]));
        }
        arg=_arg.join("&");

        if(arg.length!=0){
            url+=page+"?"+arg;
        }else{
            url+=page;
        }

        window.open(
            url,
            'detailed',
            config='width=750,height=600,toolbar=no,scrollbar=no,resizable=no,location=no'
        );

        return false;
    }
    //若老師同意開放,跳出提醒視窗


    function confirm_has_publish(area,publish_state,user_id,book_sid,anchor)
    {   
      

        if(publish_state=="可"){

            if(confirm("請老師確認以下幾點:\n 1.請確認書籍名稱是否正確 \n 2.請確認文字內容是否恰當 \n 3.請確認繪畫圖片是否恰當 \n 4.請確認錄音檔案內容是否恰當\n")){


                has_publish(area,publish_state,user_id,book_sid,anchor);
                
            }else {

                
                publish_state ="否";   
                has_publish(area,publish_state,user_id,book_sid,anchor);
               

            } 
            

        }else if (publish_state=="否"){
                has_publish(area,publish_state,user_id,book_sid,anchor);
        }
        ;
    }


    function has_publish(area,publish_state,user_id,book_sid,anchor){
 
    // //開放學生上架
        var url ='';
        var page=str_repeat('../',1)+'has_publish/has_publishA.php';
        var arg ={
            'psize':psize,
            'pinx' :pinx,
            'area':area,
            'publish_state':publish_state,
            'user_id':user_id,
            'book_sid':book_sid,
            'anchor':anchor,
            'scrolltop':$(window.parent).scrollTop()
        };
        var _arg=[];
        for(var key in arg){
            _arg.push(key+"="+encodeURI(arg[key]));//把物件轉換成陣列
        }
        arg=_arg.join("&");//將陣列[1,2,3,4,5]原本隔開的符號是逗號轉換成& 順便轉換成字串"1&2"


        if(arg.length!=0){
            url+=page+"?"+arg;
        }else{
            url+=page;
        }
        go(url,'self');

    }

    function view_comment_logo(inx){
    //展開歷史紀錄
        var tbl_comment='tbl_comment_'+inx;
        var otbl_comment=document.getElementById(tbl_comment);
        var display=otbl_comment.style.display;
        if(display===''){
            otbl_comment.style.display='none';
        }else{
            otbl_comment.style.display='';
        }
    }

    function view_word(){
        var opdf_lists=document.getElementsByName('pdf_list');
        var url="content_word.php?user_id=<?php echo $user_id;?>&view=<?php echo $view;?>&psize=<?php echo $psize;?>&pinx=<?php echo $pinx;?>";
        for(var i=0;i<opdf_lists.length;i++){
            var opdf_list=opdf_lists[i];
            var book_sid =trim(opdf_list.value);
            if(opdf_list.checked===false){
                url+='&book_sid[]=';
                url+=book_sid;
            }
        }
        window.open(url,'pdf');
    }
	
	function view_one_pdf(book_sid){
        var url="content_pdf.php?user_id=<?php echo $user_id;?>&view=<?php echo $view;?>&psize=<?php echo $psize;?>&pinx=<?php echo $pinx;?>";
        if(book_sid.length > 0){
            url+='&book_sid[]=';
            url+=book_sid;
        }
        window.open(url,'pdf');
    }
	
	function view_pdf() {
	    method = "post"; 
	    
	    //模擬Form 表單 以post 送出
	    var form = document.createElement("form");
	    var url="content_pdf.php?user_id=<?php echo $user_id;?>&view=<?php echo $view;?>&psize=<?php echo $psize;?>&pinx=<?php echo $pinx;?>";
        var opdf_lists=document.getElementsByName('pdf_list');
        
	    form.setAttribute("method", method);
	    form.setAttribute("action", url);
	    form.setAttribute("target", '_blank');
		
		for(var i=0;i<opdf_lists.length;i++){
            var opdf_list=opdf_lists[i];
            var book_sid =trim(opdf_list.value);
            if(opdf_list.checked===true){
                url+='&book_sid[]=';
                url+=book_sid;
                
                var input = document.createElement("input");
		        input.setAttribute("type", "hidden");
		        input.setAttribute("name", 'book_sid[]');
		        input.setAttribute("value", book_sid);
		        form.appendChild(input);
            }
        }
		
		document.body.appendChild(form);    // Not entirely sure if this is necessary
	    form.submit();
		
	}

	
	
    window.onload=function(){
        setTimeout(function(){
            $(window.parent).scrollTop(get_scrolltop);
            //console.log(get_scrolltop);
        }, 500);
        for(key in json_record_info){
            var rs_user_id =json_record_info[key];
            var record_path=[{
                    mp3: 'http://<?php echo $arry_ftp1_info["host"];?>/mssr/info/user/'+rs_user_id+'/book/'+key+'/record/1.mp3?<?php echo(mt_rand(11111,99999));?>'
                }];
            if(rs_user_id!==5029){
                try{
                    audio_player(record_path,rs_user_id,key,'load');
                }catch(e){}
            }
        }

        //設定動態高度
        var oIFC=parent.document.getElementById('IFC');
        var oparent_IFC=parent.parent.document.getElementById('IFC');
        var _height=parseInt($(document).height())+550;
        //console.log(_height);
        oIFC.style.height=_height+'px';
        oparent_IFC.style.height=_height+'px';
        //if(_height<=4560){
        //    oIFC.style.height=parseInt(_height+250)+'px';
        //    oparent_IFC.style.height=parseInt($(document).height()+250)+'px';
        //}else{
        //    oIFC.style.height='5500px';
        //    oparent_IFC.style.height='5500px';
        //}

        //回到頂部
        parent.$('html, body').scrollTop(0);

        //分頁列1
        var cid         ="page1";                       //容器id
        var numrow      =<?php echo (int)$numrow;?>;    //資料總筆數
        var psize       =<?php echo (int)$psize ;?>;    //單頁筆數,預設10筆
        var pnos        =<?php echo (int)$pnos  ;?>;    //分頁筆數
        var pinx        =<?php echo (int)$pinx  ;?>;    //目前分頁索引,預設1
        var sinx        =<?php echo (int)$sinx  ;?>;    //值域起始值
        var einx        =<?php echo (int)$einx  ;?>;    //值域終止值
        var list_size   =5;                             //分頁列顯示筆數,5
        var url_args    ={};                            //連結資訊
        url_args={
            'pinx_name' :'pinx',
            'psize_name':'psize',
            'page_name' :'content.php',
            'page_args' :{
                'user_id'   :user_id
            }
        }
        var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);

        //分頁列2
        var cid         ="page2";                       //容器id
        var numrow      =<?php echo (int)$numrow;?>;    //資料總筆數
        var psize       =<?php echo (int)$psize ;?>;    //單頁筆數,預設10筆
        var pnos        =<?php echo (int)$pnos  ;?>;    //分頁筆數
        var pinx        =<?php echo (int)$pinx  ;?>;    //目前分頁索引,預設1
        var sinx        =<?php echo (int)$sinx  ;?>;    //值域起始值
        var einx        =<?php echo (int)$einx  ;?>;    //值域終止值
        var list_size   =5;                             //分頁列顯示筆數,5
        var url_args    ={};                            //連結資訊
        url_args={
            'pinx_name' :'pinx',
            'psize_name':'psize',
            'page_name' :'content.php',
            'page_args' :{
                'user_id'   :user_id
            }
        }
        var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);

        if((user_id!==0)&&(get_book_sid!=='')){
            var otbl=$('.tbl_'+user_id+'_'+get_book_sid)[0];
            var arrys_inx=otbl.getAttribute('arrys_inx');
            var has_text=parseInt(otbl.getAttribute('has_text'));
            var obj=new Object();
            obj.value='切換為詳細指導';
            btn_chahge_rec_mode(obj,arrys_inx,has_text);
            var $btn_chahge_rec_modes=$('.btn_chahge_rec_mode');
            for(var i=0;i<$btn_chahge_rec_modes.length;i++){
                var obtn_chahge_rec_mode=$btn_chahge_rec_modes.eq(i)[0];
                if(obtn_chahge_rec_mode.getAttribute('arrys_inx')===arrys_inx){
                    obtn_chahge_rec_mode.value='切換為簡易閱覽';
                }
            }
        }

        var scrolltop=window.localStorage["scrollTop"];
        if(scrolltop!==undefined){
            $(window.parent.document).scrollTop(scrolltop);
        }

        all_control(1);
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
        global $view;
        global $user_id;

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
    <link rel="stylesheet" type="text/css" href="../../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/js/string/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../../css/def.css" media="all" />
</Head>

<Body>

    <!-- 統計資料表格 開始 -->
    <div class="mod_data_tbl_outline" style="margin-top:35px;">
        <table id="mod_data_tbl" border="0" width="95%" height="40px" cellpadding="5" cellspacing="0" style="" class="font-weight1 font-family1 fc_green0">
            <tr align="center" valign="middle" class="fsize_16">
                <td align="center" width="470px">
                    <span class="fsize_16">
                        <span class="fc_red1">●請注意! 此頁為新開頁面，</span>
                        <input type="button" value="請按我關閉" class="ibtn_gr9030" onclick="parent.close();" onmouseover="this.style.cursor='pointer'">
                    </span>
                </td>
            </tr>
        </table>
    </div>
    <!-- 統計資料表格 結束 -->

    <!-- 資料列表 開始 -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="400px" align="center" valign="top">
                <!-- 內容 -->
                <table border="0" width="100%" cellpadding="5" cellspacing="0" style="position:relative;top:50px;" class="mod_data_tbl_outline">
                    <tr align="center" valign="middle">
                        <td>
                            <table border="0" width="100%">
                                <tr valign="middle">
                                    <td align="center">
                                        <select style="height:23px;" onchange="view_mode(this);">
                                            <option value="" selected disabled>選擇觀看模式
                                            <option value="all" onclick="location.href='content.php?user_id=<?php echo $user_id;?>&view=all';void(0);"
                                            <?php if($view==='all')echo 'selected';?>>觀看全部作品

                                            <option value="has_comment" onclick="location.href='content.php?user_id=<?php echo $user_id;?>&view=has_comment';void(0);"
                                            <?php if($view==='has_comment')echo 'selected';?>>觀看已評作品

                                            <option value="has_no_comment" onclick="location.href='content.php?user_id=<?php echo $user_id;?>&view=has_no_comment';void(0);"
                                            <?php if($view==='has_no_comment')echo 'selected';?>>觀看未評作品
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="400px" align="center" valign="middle" class="font-family1 fc_green0 fsize_18">
                            <img src="../../../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
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

    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;

    function view_mode(obj){
        var val=obj.options[obj.options.selectedIndex].value;
        if(val==='has_comment')location.href='content.php?user_id=<?php echo $user_id;?>&view=has_comment';
        if(val==='all')location.href='content.php?user_id=<?php echo $user_id;?>';
        if(val==='has_no_comment')location.href='content.php?user_id=<?php echo $user_id;?>&view=has_no_comment';
    }

    window.onload=function(){

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