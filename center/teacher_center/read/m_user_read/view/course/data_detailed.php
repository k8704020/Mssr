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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_read');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //inx           行數
    //data_filter   資料條件
    //borrow_sid    借閱識別碼

        $get_chk=array(
            'inx        ',
            'data_filter',
            'borrow_sid '
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
    //inx           行數
    //data_filter   資料條件
    //borrow_sid    借閱識別碼

        //GET
        $inx=trim($_GET[trim('inx')]);
        $data_filter=trim($_GET[trim('data_filter')]);
        $borrow_sid =trim($_GET[trim('borrow_sid')]);

        //背景顏色
        $bgcolor='';
        if($inx%2===0){
            $bgcolor='#CEE8E9';
        }else{
            $bgcolor='#F7F8F8';
        }

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //inx           行數
    //data_filter   資料條件
    //borrow_sid    借閱識別碼

        $arry_err=array();

        if($data_filter===''){
           $arry_err[]='資料條件,未輸入!';
        }

        if($borrow_sid===''){
           $arry_err[]='借閱識別碼,未輸入!';
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
    //資料庫
    //---------------------------------------------------

        //建立連線 mssr
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //判斷獲取的資料流
        $lv=0;
        if($data_filter==='read'){
            $lv=$lv+1;
        }
        if($data_filter==='like'){
            $lv=$lv+3;
        }
        if($data_filter==='rec'){
            $lv=$lv+5;
        }

        switch($lv){
            case 1:
            //-------------------------------------------
            //查找, 閱讀資訊
            //-------------------------------------------

                $borrow_sid=mysql_prep($borrow_sid);
                $get_book_read_opinion_log_info=get_book_read_opinion_log_info($conn_mssr,$borrow_sid,$array_filter=array('opinion_answer'),$arry_conn_mssr);
                $read_num_html='';  //閱讀程度
                if(!empty($get_book_read_opinion_log_info)){
                    $rs_opinion_answer=$get_book_read_opinion_log_info[0]['opinion_answer'];
                    if(unserialize($rs_opinion_answer)){
                        $arrys_opinion_answer=unserialize($rs_opinion_answer);
                        foreach($arrys_opinion_answer as $inx=>$arry_opinion_answer){
                            $topic_id=(int)$arry_opinion_answer['topic_id'];

                            //閱讀程度
                            if($topic_id===1){
                                $opinion_answer=(int)$arry_opinion_answer['opinion_answer'][0];
                                $read_num=(int)$opinion_answer;
                                $sql="
                                    SELECT `topic_options`
                                    FROM `mssr_book_topic_log`
                                    WHERE 1=1
                                        AND `topic_id`={$topic_id}
                                ";
                                $read_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                $topic_options=trim($read_result[0]['topic_options']);
                                if(unserialize($topic_options)){
                                    $arrys_topic_options=unserialize($topic_options);
                                    if(isset($arrys_topic_options[$read_num])){
                                        $read_num_html=trim($arrys_topic_options[$read_num]);
                                    }
                                }
                            }
                        }
                    }
                }
            break;

            case 3:
            //-------------------------------------------
            //查找, 閱讀資訊
            //-------------------------------------------

                $borrow_sid=mysql_prep($borrow_sid);
                $get_book_read_opinion_log_info=get_book_read_opinion_log_info($conn_mssr,$borrow_sid,$array_filter=array('opinion_answer'),$arry_conn_mssr);
                $like_num_html='';  //喜愛程度
                if(!empty($get_book_read_opinion_log_info)){
                    $rs_opinion_answer=$get_book_read_opinion_log_info[0]['opinion_answer'];
                    if(unserialize($rs_opinion_answer)){
                        $arrys_opinion_answer=unserialize($rs_opinion_answer);
                        foreach($arrys_opinion_answer as $inx=>$arry_opinion_answer){
                            $topic_id=(int)$arry_opinion_answer['topic_id'];
                            //喜愛程度
                            if($topic_id===3){
                                $opinion_answer=(int)$arry_opinion_answer['opinion_answer'][0];
                                $like_num=(int)$opinion_answer;
                                $sql="
                                    SELECT `topic_options`
                                    FROM `mssr_book_topic_log`
                                    WHERE 1=1
                                        AND `topic_id`={$topic_id}
                                ";
                                $like_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                $topic_options=trim($like_result[0]['topic_options']);
                                if(unserialize($topic_options)){
                                    $arrys_topic_options=unserialize($topic_options);
                                    if(isset($arrys_topic_options[$like_num])){
                                        $like_num_html=trim($arrys_topic_options[$like_num]);
                                    }
                                }
                            }
                        }
                    }
                }
            break;

            case 5:
                $has_rec='X';
                $borrow_sid=mysql_prep($borrow_sid);
                $get_book_read_opinion_log_info=get_book_read_opinion_log_info($conn_mssr,$borrow_sid,$array_filter=array('user_id','book_sid'),$arry_conn_mssr);
                if(!empty($get_book_read_opinion_log_info)){
                    $rs_user_id=(int)$get_book_read_opinion_log_info[0]['user_id'];
                    $rs_book_sid=mysql_prep(trim($get_book_read_opinion_log_info[0]['book_sid']));
                    $sql="
                        SELECT `user_id`
                        FROM `mssr_rec_book_cno`
                        WHERE 1=1
                            AND `user_id` = {$rs_user_id }
                            AND `book_sid`='{$rs_book_sid}'
                            AND `rec_state`=1
                    ";
                    $rec_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    if(!empty($rec_result)){
                        $has_rec='O';
                    }
                }
            break;

            default:
                die();
            break;
        }

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,教師中心";
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=false);?>
    <?php echo robots($allow=false);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../../inc/code.js"></script>

    <!-- 專屬 -->
    <!-- <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../../css/def.css" media="all" /> -->

    <style>

    </style>
</Head>

<Body bgcolor="<?php echo $bgcolor;?>">
    <?php
        switch($lv){
            case 1:
                page_read($read_num_html);
            break;

            case 3:
                page_like($like_num_html);
            break;

            case 5:
                page_rec($has_rec);
            break;
        }
    ?>
</Body>

<?php function page_read($read_num_html) {?>
<?php
//-------------------------------------------------------
//page_read 區塊 -- 開始
//-------------------------------------------------------

    //---------------------------------------------------
    //外部變數
    //---------------------------------------------------

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------


    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
?>

<!-- 內容 開始 -->
    <span style="color:#8e4408;font-size:15px;">
        <?php echo htmlspecialchars($read_num_html);?>
    </span>
<!-- 內容 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

</script>

<?php
//-------------------------------------------------------
//page_read 區塊 -- 結束
//-------------------------------------------------------
?>
<?php };?>

<?php function page_like($like_num_html) {?>
<?php
//-------------------------------------------------------
//page_like 區塊 -- 開始
//-------------------------------------------------------

    //---------------------------------------------------
    //外部變數
    //---------------------------------------------------

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------


    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
?>

<!-- 內容 開始 -->
    <span style="color:#8e4408;font-size:15px;">
        <?php echo htmlspecialchars($like_num_html);?>
    </span>
<!-- 內容 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

</script>

<?php
//-------------------------------------------------------
//page_like 區塊 -- 結束
//-------------------------------------------------------
?>
<?php };?>

<?php function page_rec($has_rec) {?>
<?php
//-------------------------------------------------------
//page_rec 區塊 -- 開始
//-------------------------------------------------------

    //---------------------------------------------------
    //外部變數
    //---------------------------------------------------

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------


    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
?>

<!-- 內容 開始 -->
    <span style="color:#8e4408;font-size:15px;">
        <?php echo htmlspecialchars($has_rec);?>
    </span>
<!-- 內容 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

</script>

<?php
//-------------------------------------------------------
//page_rec 區塊 -- 結束
//-------------------------------------------------------
?>
<?php };?>