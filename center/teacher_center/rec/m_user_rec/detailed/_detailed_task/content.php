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

                    APP_ROOT.'lib/php/db/code'
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

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //SESSION
        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用

        if(isset($_SESSION['m_user_rec']['filter'])){
            $filter=$_SESSION['m_user_rec']['filter'];
        }
        if(isset($_SESSION['m_user_rec']['query_fields'])){
            $query_fields=$_SESSION['m_user_rec']['query_fields'];
        }

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
        //學生陣列
        //---------------------------------------------------

            if($filter!=''){
                //學生陣列
                $q_class_code=mysql_prep(trim($_SESSION['m_user_rec']['class_code']));
                $users=arrys_users($conn_user,$q_class_code,$date=date("Y-m-d"),$arry_conn_user);
            }else{
                //學生陣列
                $users=arrys_users($conn_user,$sess_class_code,$date=date("Y-m-d"),$arry_conn_user);
            }

        //---------------------------------------------------
        //SQL條件
        //---------------------------------------------------

            $query_sql="
                SELECT
                    `edit_by`,
                    `user_id`,
                    `book_sid`,
                    `keyin_cdate`,
                    `keyin_mdate`
                FROM `mssr_rec_book_cno_semester`
                WHERE 1=1
                    AND `user_id` IN ($users)
                    AND `rec_state`=1
                    AND `keyin_mdate` >='2014-04-01 00:00:00'
                ORDER BY `keyin_mdate` DESC
            ";
//echo "<Pre>";
//print_r($query_sql);
//echo "</Pre>";
//die();
    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=0;  //資料總筆數
        $psize =10; //單頁筆數,預設10筆
        $pnos  =0;  //分頁筆數
        $pinx  =1;  //目前分頁索引,預設1
        $sinx  =0;  //值域起始值
        $einx  =0;  //值域終止值

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

        $numrow=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
        $numrow=count($numrow);

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

        if($numrow!==0){
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx-1,$psize),$arry_conn_mssr);
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
        global $date_filter;
        global $update_open_publish;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0; //欄位個數
        $btn_nos=0; //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        //推薦類型陣列
        $arrys_rec_type=array(
            trim('draw  ')=>trim('繪圖'),
            trim('text  ')=>trim('文字'),
            trim('record')=>trim('錄音')
        );

        $arry_record_info=array();
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
                                    <input type="button" value="儲存閱讀紀錄" id='BtnR' name='BtnR' class="ibtn_gr9030" onclick="void(0);" onmouseover="this.style.cursor='pointer'"
                                    style='color:#ff0000;'>
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
                            <td align="left">
                                <!-- 分頁列 -->
                                <span id="page1" style="position:relative;margin-top:10px;"></span>
                            </td>
                        </tr>
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

                        $rs_user_id=(int)$rs_user_id;
                        $rs_book_sid=trim($rs_book_sid);

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
                            $rec_draw_info=get_rec_info($conn_mssr,$rs_user_id,trim($rs_book_sid),$rec_type='draw',$array_filter=array("rec_sid"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                            if(!empty($rec_draw_info)){
                                foreach($rec_draw_info as $inx=>$arry){
                                    //匯入推薦識別碼陣列
                                    array_push($arrys_rec_sid,trim($arry['rec_sid']));
                                }
                                //圖片識別碼
                                $rs_rec_draw_sid=trim($rec_draw_info[0]['rec_sid']);

                                $root           =str_repeat("../",6)."info/user/".(int)$rs_user_id."/book";
                                $draw_path      ="{$root}/".trim($rs_book_sid)."/draw/bimg/1.jpg";
                                $draw_path_enc  =mb_convert_encoding($draw_path,$fso_enc,$page_enc);
                                if(file_exists($draw_path_enc)){
                                    $has_draw=true;
                                }
                            }

                        //-----------------------------------------------
                        //查找, 星評資訊
                        //-----------------------------------------------

                            $has_star=false;
                            $rs_rec_star_rank='尚未選擇星等 !';
                            $rs_rec_star_reason='尚未選擇評星理由 !';
                            $rec_star_info=get_rec_info($conn_mssr,$rs_user_id,trim($rs_book_sid),$rec_type='star',$array_filter=array("rec_sid","rec_rank","rec_reason"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
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
                                foreach($config_arrys['service']['bookstore']['rec_reason'] as $inx1=>$val1){
                                    if($rs_rec_star_reason[$inx1]==='o'){
                                        //匯入評星理由
                                        array_push($arrys_rs_rec_star_reason,$val1);
                                    }
                                }
                                $arrys_rs_rec_star_reason=implode(" , ",$arrys_rs_rec_star_reason);

                                $has_star=true;
                            }

                        //-----------------------------------------------
                        //查找, 文字資訊
                        //-----------------------------------------------

                            $has_text   =false;
                            $rs_rec_text_state='';
                            $arrys_rs_rec_text_content=array('','','');
                            $rec_text_info=get_rec_info($conn_mssr,$rs_user_id,trim($rs_book_sid),$rec_type='text',$array_filter=array("rec_sid","rec_content","rec_state"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
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
                            $rec_record_info=get_rec_info($conn_mssr,$rs_user_id,trim($rs_book_sid),$rec_type='record',$array_filter=array("rec_sid"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                            if(!empty($rec_record_info)){
                                foreach($rec_record_info as $inx=>$arry){
                                    //匯入推薦識別碼陣列
                                    array_push($arrys_rec_sid,trim($arry['rec_sid']));
                                }
                                //錄音識別碼
                                $rs_rec_record_sid=trim($rec_record_info[0]['rec_sid']);

                                $root               =str_repeat("../",6)."info/user/".(int)$rs_user_id."/book";

                                $record_path_mp3    ="{$root}/".trim($rs_book_sid)."/record/1.mp3";
                                $record_path_mp3_enc=mb_convert_encoding($record_path_mp3,$fso_enc,$page_enc);

                                $record_path_wav    ="{$root}/".trim($rs_book_sid)."/record/1.wav";
                                $record_path_wav_enc=mb_convert_encoding($record_path_wav,$fso_enc,$page_enc);

                                if((file_exists($record_path_mp3_enc))||(file_exists($record_path_wav_enc))){
                                    $has_record =true;
                                    $arry_record_info[$rs_book_sid]=$rs_user_id;
                                }
                            }

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
//echo "<Pre>";
//print_r($arrys_rec_sid);
//echo "</Pre>";
//echo "<Pre>";
//print_r($arry_has_rec_comment);
//echo "</Pre>";
                        //-----------------------------------------------
                        //查找, 是否已有被閱讀紀錄
                        //-----------------------------------------------

                            $sql="
                                SELECT
                                    `book_sid`
                                FROM `mssr_rec_teacher_read`
                                WHERE 1=1
                                    AND `user_id`   = {$rs_user_id }
                                    AND `book_sid`  ='{$rs_book_sid}'
                                    AND `read_state`=1;
                            ";
                            $has_read_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                            if(!empty($has_read_result)){
                                $has_read=true;
                            }else{
                                $has_read=false;
                            }
                    ?>
                    <table id="tbl_<?php echo $arrys_inx;?>" width="93%" align="center" border="0" cellpadding="0" cellspacing="0" style="position:relative;margin-top:25px;" class="mod_data_tbl_outline">
                        <tr>
                            <td align='left' valign="middle" bgcolor='#87CDDC'>
                                <img src="img/read.png" width="50" height="50" border="0" alt="read"/>
                                <input type="checkbox" id="read_list" name="read_list" value="<?php echo $rs_user_id;?>"
                                book_sid='<?php echo $rs_book_sid;?>'
                                style='position:relative;bottom:15px;'
                                onclick='chk_read_list();void(0);'
                                <?php if($has_read)echo 'checked';?>>
                            </td>
                            <td align='right' valign="middle" bgcolor='#87CDDC'>
                                <?php if(($has_draw)||($has_text)||($has_record)):?>
                                    <img src="img/trash.png" width="50" height="50" border="0" alt="trash"
                                    onmouseover='this.style.cursor="pointer"'
                                    onclick='trash(<?php echo $rs_user_id;?>,"<?php echo $rs_book_sid;?>","tbl_<?php echo $arrys_inx;?>");void(0);'/>
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
                                                        學生名稱: <?php echo htmlspecialchars($rs_user_name);?>
                                                    </td>
                                                    <td>
                                                        建立時間: <?php echo htmlspecialchars($rs_keyin_cdate);?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        書籍名稱: <?php echo htmlspecialchars($rs_book_name);?>
                                                    </td>
                                                    <td>
                                                        最後更新時間: <?php echo htmlspecialchars($rs_keyin_mdate);?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr valign="top">
                            <td align="center" width="45%">
                                <!-- 畫圖資訊 -->
                                <table cellpadding="0" cellspacing="0" border="0" width="100%" height="380px" style="padding:3px;"/>
                                    <tr align="center" valign="middle" class="<?php echo $tbl_bg_color;?>">
                                        <td>
                                            <?php if(($has_draw)&&($sess_user_id!==$rs_user_id)):?>
                                                <img src="<?php echo $draw_path;?>" width="355px" height="283px" border="0" alt="畫圖資訊"/>
                                            <?php else:?>
                                                <span class="fsize_18 font-weight1 <?php echo $tbl_fc_color;?>">尚未畫圖 !</span>
                                            <?php endif;?>
                                        </td>
                                    </tr>
                                    <!-- 畫圖指導 -->
                                    <?php //if(in_array($auth_sys_check_lv,array(5,14,22))):?>
                                        <?php if($has_draw):?>
                                        <tr align="center" valign="top" class="<?php echo $tbl_bg_color;?>">
                                            <td>
                                                <?php if(in_array('draw',$arry_has_rec_comment)):?>
                                                    <input type="button" value="✔老師指導(畫圖已指導)" class="" onclick="detailed('detailed_task','draw','<?php echo addslashes($rs_rec_draw_sid);?>',<?php echo addslashes($rs_user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')" onmouseover="this.style.cursor='pointer'">
                                                <?php else:?>
                                                    <input type="button" value="老師指導(畫圖)" class="" onclick="detailed('detailed_task','draw','<?php echo addslashes($rs_rec_draw_sid);?>',<?php echo addslashes($rs_user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')" onmouseover="this.style.cursor='pointer'">
                                                <?php endif;?>
                                                <input type="button" value="直接刪除推薦" class=""
                                                onclick="del('detailed_task','draw','<?php echo addslashes($rs_rec_draw_sid);?>',<?php echo addslashes($rs_user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')"
                                                onmouseover="this.style.cursor='pointer'">
                                            </td>
                                        </tr>
                                        <?php endif;?>
                                    <?php //endif;?>
                                </table>
                            </td>
                            <td align="center" width="55%">
                                <!-- 評星資訊 -->
                                <table cellpadding="0" cellspacing="0" border="0" width="100%" height="70px" style="padding:3px;"/>
                                    <tr align="left" valign="middle" class="<?php echo $tbl_bg_color;?>">
                                        <td>
                                            <span class="fsize_18 font-weight1 <?php echo $tbl_fc_color;?>" style="position:relative;padding:5px;display:block;">
                                                評價 : <?php echo htmlspecialchars($rs_rec_star_rank);?>
                                            </span>
                                            <span class="fsize_18 font-weight1 <?php echo $tbl_fc_color;?>" style="position:relative;padding:5px;display:block;">
                                                理由 : <?php if($has_star){echo htmlspecialchars($arrys_rs_rec_star_reason);}else{echo $rs_rec_star_reason;}?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>

                                <!-- 文字資訊 -->
                                <table cellpadding="0" cellspacing="0" border="0" width="100%" height="230px" style="padding:3px;"/>
                                    <tr align="left" valign="top" class="<?php echo $tbl_bg_color;?>">
                                        <td>
                                            <span class="fsize_18 font-weight1 <?php echo $tbl_fc_color;?>" style="position:relative;padding:5px;display:block;">
                                                <textarea cols="40" rows="10" wrap="hard" class="form_textarea fsize_18 font-weight1 <?php echo $tbl_fc_color;?> <?php echo $tbl_bg_color;?>"
                                                style="width:460px;height:208px;display:block;border:0;">
【最喜歡的一句話】
<?php if($rs_rec_text_state==='顯示'):?>
<?php echo htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[0])));?>
<?php endif;?>


【書本內容介紹】
<?php if($rs_rec_text_state==='顯示'):?>
<?php echo htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[1])));?>
<?php endif;?>


【書中所學到的事】
<?php if($rs_rec_text_state==='顯示'):?>
<?php echo htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[2])));?>
<?php endif;?>
                                                </textarea>
                                            </span>
                                        </td>
                                    </tr>
                                    <!-- 文字指導 -->
                                    <tr align="center" valign="top" class="<?php echo $tbl_bg_color;?>">
                                        <td>
                                            <?php //if(in_array($auth_sys_check_lv,array(5,14,22))):?>
                                                <?php if(($has_text)&&($sess_user_id!==$rs_user_id)):?>
                                                    <?php if(in_array('text',$arry_has_rec_comment)):?>
                                                        <input type="button" value="✔老師指導(文字已指導)" class="" onclick="detailed('detailed_task','text','<?php echo addslashes($rs_rec_text_sid);?>',<?php echo addslashes($rs_user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')" onmouseover="this.style.cursor='pointer'">
                                                    <?php else:?>
                                                        <input type="button" value="老師指導(文字)" class="" onclick="detailed('detailed_task','text','<?php echo addslashes($rs_rec_text_sid);?>',<?php echo addslashes($rs_user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')" onmouseover="this.style.cursor='pointer'">
                                                    <?php endif;?>
                                                    <input type="button" value="直接刪除推薦" class=""
                                                    onclick="del('detailed_task','text','<?php echo addslashes($rs_rec_text_sid);?>',<?php echo addslashes($rs_user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')"
                                                    onmouseover="this.style.cursor='pointer'">
                                                <?php endif;?>
                                            <?php //endif;?>
                                        </td>
                                    </tr>
                                </table>

                                <!-- 錄音資訊 -->
                                <table cellpadding="0" cellspacing="0" border="0" width="100%" height="50px" style="padding:3px;"/>
                                    <tr align="left" valign="middle" class="<?php echo $tbl_bg_color;?>">
                                        <td>
                                            <?php if(($has_record)&&($sess_user_id!==$rs_user_id)):?>
                                                <!-- <span class="fsize_18 font-weight1 <?php echo $tbl_fc_color;?>" style="position:relative;padding:5px;top:5px;">
                                                    <audio id="audio" controls="controls">
                                                        <source id="record1" src="<?php echo $record_path_wav;?>"/>
                                                        <source id="record2" src="<?php echo $record_path_mp3;?>"/>
                                                            無法播放，建議使用 chrome 瀏覽器獲得更佳體驗。
                                                    </audio>
                                                </span> -->
                                                <div id="player" class='player_<?php echo $rs_user_id;?>_<?php echo $rs_book_sid;?>' style='float:left;'>
                                                    <div class="ctrl" id='ctrl_<?php echo $rs_user_id;?>_<?php echo $rs_book_sid;?>'>
                                                        <div class="control" id='control_<?php echo $rs_user_id;?>_<?php echo $rs_book_sid;?>'>
                                                            <div class="left" id='left_<?php echo $rs_user_id;?>_<?php echo $rs_book_sid;?>'>
                                                                <div class="playback icon" id='playback_<?php echo $rs_user_id;?>_<?php echo $rs_book_sid;?>'></div>
                                                            </div>
                                                            <div class="volume right" id='volume_<?php echo $rs_user_id;?>_<?php echo $rs_book_sid;?>1'>
                                                                <div class="mute icon left" id='mute_<?php echo $rs_user_id;?>_<?php echo $rs_book_sid;?>'></div>
                                                                <div class="slider left" id='slider_<?php echo $rs_user_id;?>_<?php echo $rs_book_sid;?>'>
                                                                    <div class="pace" id='pace_<?php echo $rs_user_id;?>_<?php echo $rs_book_sid;?>'></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="progress" id='progress_<?php echo $rs_user_id;?>_<?php echo $rs_book_sid;?>'>
                                                            <div class="slider" id='slider_<?php echo $rs_user_id;?>_<?php echo $rs_book_sid;?>'>
                                                                <div class="loaded" id='loaded_<?php echo $rs_user_id;?>_<?php echo $rs_book_sid;?>'></div>
                                                                <div class="pace" id='pace_<?php echo $rs_user_id;?>_<?php echo $rs_book_sid;?>'></div>
                                                            </div>
                                                            <div class="timer left" id='timer_<?php echo $rs_user_id;?>_<?php echo $rs_book_sid;?>'
                                                            style='position:relative;bottom:3px;'>0:00</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 錄音指導 -->
                                                <br/><br/>
                                                <span style="position:relative;bottom:5px;left:25px;">
                                                    <?php //if(in_array($auth_sys_check_lv,array(5,14,22))):?>
                                                        <?php if(in_array('record',$arry_has_rec_comment)):?>
                                                            <input type="button" value="✔老師指導(錄音已指導)" class="" onclick="detailed('detailed_task','record','<?php echo addslashes($rs_rec_record_sid);?>',<?php echo addslashes($rs_user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')" onmouseover="this.style.cursor='pointer'">
                                                        <?php else:?>
                                                            <input type="button" value="老師指導(錄音)" class="" onclick="detailed('detailed_task','record','<?php echo addslashes($rs_rec_record_sid);?>',<?php echo addslashes($rs_user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')" onmouseover="this.style.cursor='pointer'">
                                                        <?php endif;?>
                                                        <input type="button" value="直接刪除推薦" class=""
                                                        onclick="del('detailed_task','record','<?php echo addslashes($rs_rec_record_sid);?>',<?php echo addslashes($rs_user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>')"
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
                                                <select id="has_publish" name="has_publish" class="form_select" onchange="has_publish('detailed_task',this.options[this.options.selectedIndex].value,<?php echo addslashes($rs_user_id);?>,'<?php echo addslashes($rs_book_sid);?>','tbl_<?php echo $arrys_inx;?>');">
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
                                        $keyin_cdate    =date("m-d",strtotime($keyin_cdate));

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
                                                時間: <?php echo htmlspecialchars($keyin_cdate);?>
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
                                <span id="page2" style="position:relative;margin-top:10px;"></span>
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

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;
    var date_filter='<?php echo $date_filter;?>';
    var json_record_info=<?php echo json_encode($arry_record_info,true);?>;
    var read_lists=document.getElementsByName('read_list');
    var oBtnR=document.getElementById('BtnR');

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

    <?php if($auth_sys_check_lv!==99):?>
        oBtnR.onclick=function(){
            if(!confirm('你確定要儲存嗎?')){
                return false;
            }
            $.blockUI({
                message:'<h2 class="fc_white0">處理中，請稍後 !</h2>',
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
            var ajax_cno=0;
            for(var i=0;i<read_lists.length;i++){
                ajax_cno++;
                var read_list=read_lists[i];
                var user_id =parseInt(read_list.value);
                var book_sid=trim(read_list.getAttribute('book_sid'));
                var chk_flag=read_list.checked;

                var flag='fasle';
                if(chk_flag){
                    flag='true';
                }

                //ajax
                $.post('readA.php',{
                    user_id :encodeURI(user_id  ),
                    book_sid:encodeURI(book_sid ),
                    flag    :encodeURI(flag     ),
                    ajax_cno:encodeURI(ajax_cno )
                },"json")
                .success(function(respones){
                    var respones=jQuery.parseJSON(respones);
                    var ajax_cno=parseInt(respones.ajax_cno);
                    if(ajax_cno===10){
                        $.blockUI({
                            message:'<h2 class="fc_white0">儲存成功 !</h2>',
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
                        setTimeout($.unblockUI, 1000);
                    }
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
                    setTimeout($.unblockUI, 1000);
                })
                .complete(function(e){

                });
            }
        }
    <?php endif;?>

    function chk_read_list(){
        var chk_cno=0;
        for(var i=0;i<read_lists.length;i++){
            var read_list=read_lists[i];
            var chk_flag=read_list.checked;
            if(chk_flag){
                chk_cno++;
            }
        }
        if(chk_cno>=1){
            //$(oBtnR).show();
        }else{
            //$(oBtnR).hide();
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
            'date_filter'    :date_filter
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
            'date_filter':date_filter
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
            config='width=650,height=500,toolbar=no,scrollbar=no,resizable=no,location=no'
        );

        return false;
    }

    function has_publish(area,publish_state,user_id,book_sid,anchor){
    //開放學生上架
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
            'date_filter':date_filter
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

    window.onload=function(){

        for(key in json_record_info){
            var rs_user_id =json_record_info[key];
            var record_path=[{
                    mp3: '../../../../../../info/user/'+rs_user_id+'/book/'+key+'/record/1.mp3?<?php echo(mt_rand(11111,99999));?>'
                }];
            audio_player(record_path,rs_user_id,key,'load');
        }

        //設定動態高度
        var oIFC=parent.document.getElementById('IFC');
        var oparent_IFC=parent.parent.document.getElementById('IFC');
        var _height=parseInt($(document).height());

        if(_height<=4560){
            oIFC.style.height=parseInt(_height+40)+'px';
            oparent_IFC.style.height=parseInt($(document).height()+40)+'px';
        }else{
            oIFC.style.height='5570px';
            oparent_IFC.style.height='5570px';
        }

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
                'date_filter':date_filter
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
                'date_filter':date_filter
            }
        }
        var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
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
                        <td>&nbsp;</td>
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