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
        unset($_SESSION['tc']['t|dt']['add_book_tip']);

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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_book_borrow_search');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //SESSION
        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用

        if(isset($_SESSION['m_book_borrow_search']['filter'])){
            $filter=$_SESSION['m_book_borrow_search']['filter'];
        }
        if(isset($_SESSION['m_book_borrow_search']['query_fields'])){
            $query_fields=$_SESSION['m_book_borrow_search']['query_fields'];
        }
        if(isset($_SESSION['m_book_borrow_search'])&&(isset($_SESSION['m_book_borrow_search']['adscription']))){
            $sess_adscription=trim($_SESSION['m_book_borrow_search']['adscription']);
        }else{
            $sess_adscription='school';
        }

        if(isset($sess_login_info['uid'])){$sess_user_id=(int)$sess_login_info['uid'];}
        if(isset($sess_login_info['permission'])){$sess_permission=trim($sess_login_info['permission']);}
        if(isset($sess_login_info['school_code'])){$sess_school_code=trim($sess_login_info['school_code']);}
        if(isset($sess_login_info['responsibilities'])){
            $sess_responsibilities=(int)$sess_login_info['responsibilities'];
            if(in_array($auth_sys_check_lv,array(5,14,16,22,99))){
                $sess_class_code=trim($sess_login_info['arrys_class_code'][0]['class_code']);
                $sess_grade=(int)$sess_login_info['arrys_class_code'][0]['grade'];
                $sess_classroom=(int)$sess_login_info['arrys_class_code'][0]['classroom'];
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        $sess_school_code=mysql_prep($sess_school_code);

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

        //-----------------------------------------------
        //檢核借閱書學校關聯
        //-----------------------------------------------

            $other_school_code=book_borrow_school_rev($db_type='mysql',$arry_conn_mssr,$sess_school_code);

        //---------------------------------------------------
        //SQL 筆數查詢
        //---------------------------------------------------
//echo "<Pre>";print_r($filter);echo "</Pre>";
            if($filter!=''){
                $query_sql ="";
                $query_sql.="
                    SELECT
                        COUNT(*) AS `cno`
                    FROM `mssr_book_library`
                    WHERE 1=1
                        -- FILTER在此
                        {$filter}
                ";
                if(trim($other_school_code)!==''){
                    $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                }else{
                    $query_sql.="AND `school_code`='{$sess_school_code}'";
                }
                $filter2=trim(preg_replace("/AND `book_library_code` = .*/",'',$filter));
                if($filter2!==''){
                    $query_sql.="
                        UNION ALL
                            SELECT
                                COUNT(*) AS `cno`
                            FROM `mssr_book_class`
                            WHERE 1=1
                                -- FILTER在此
                                {$filter2}
                    ";
                    if(trim($other_school_code)!==''){
                        $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                    }else{
                        $query_sql.="AND `school_code`='{$sess_school_code}'";
                    }
                }
            }else{
                //$query_sql ="";
                //$query_sql.="
                //    SELECT
                //        COUNT(*) AS `cno`
                //    FROM `mssr_book_class`
                //    WHERE 1=1
                //        -- FILTER在此
                //        {$filter}
                //";
                //if(trim($other_school_code)!==''){
                //    $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                //}else{
                //    $query_sql.="AND `school_code`='{$sess_school_code}'";
                //}
                //$query_sql.="
                //    UNION ALL
                //        SELECT
                //            COUNT(*) AS `cno`
                //        FROM `mssr_book_library`
                //        WHERE 1=1
                //            -- FILTER在此
                //            {$filter}
                //";
                //if(trim($other_school_code)!==''){
                //    $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                //}else{
                //    $query_sql.="AND `school_code`='{$sess_school_code}'";
                //}
            }
            //$sth=$conn_mssr->prepare($query_sql);
            //$sth->execute();
            //$db_results_cno=$sth->rowCount();
            //echo "<Pre>";
            //print_r($query_sql);
            //echo "</Pre>";
            //die();
            $db_results_cno=0;
            if($filter!==''){
                $db_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $db_result){
                        $db_results_cno+=(int)$db_result['cno'];
                    }
                }
            }

            if($db_results_cno===0){
                //page_nrs($title="明日星球,教師中心");
                //die();
            }

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
            $psize=(int)10;
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

        $pnos  =ceil($numrow/$psize);
        $pinx  =($pinx>$pnos)?$pnos:$pinx;

        $sinx  =(($pinx-1)*$psize);
        $einx  =(($pinx)*$psize);
        $einx  =($einx>$numrow)?$numrow:$einx;
        //echo $numrow."<br/>";

    //---------------------------------------------------
    //SQL 查詢
    //---------------------------------------------------

        $arrys_result     =[];
        $tmp_total_results=[];

        if($db_results_cno!==0){
            if($filter!=''){
                $query_sql ="";
                $query_sql.="
                    SELECT
                        'mssr_book_library' AS `book_type`,
                        `book_sid`,
                        `book_isbn_10`,
                        `book_isbn_13`,
                        `book_library_code`,
                        '無' AS `book_no`,
                        `keyin_mdate`
                    FROM `mssr_book_library`
                    WHERE 1=1
                        -- FILTER在此
                        {$filter}
                ";
                if(trim($other_school_code)!==''){
                    $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                }else{
                    $query_sql.="AND `school_code`='{$sess_school_code}'";
                }
                $filter2=trim(preg_replace("/AND `book_library_code` = .*/",'',$filter2));
                $tmp_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
                if(!empty($tmp_results)){
                    foreach($tmp_results as $tmp_result){
                        $tmp_total_results[]=$tmp_result;
                    }
                }
                if($filter2!==''){
                    $query_sql ="";
                    $query_sql.="
                        SELECT
                            'mssr_book_class' AS `book_type`,
                            `book_sid`,
                            `book_isbn_10`,
                            `book_isbn_13`,
                            '' AS `book_library_code`,
                            `book_no`,
                            `keyin_mdate`
                        FROM `mssr_book_class`
                        WHERE 1=1
                            -- FILTER在此
                            {$filter2}
                    ";
                    if(trim($other_school_code)!==''){
                        $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                    }else{
                        $query_sql.="AND `school_code`='{$sess_school_code}'";
                    }
                    $tmp_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
                    if(!empty($tmp_results)){
                        foreach($tmp_results as $tmp_result){
                            $tmp_total_results[]=$tmp_result;
                        }
                    }
                }
                $arrys_chunk =array_chunk($tmp_total_results,$psize);
                $arrys_result=$arrys_chunk[$pinx-1];
            }else{
                //$has_search_cno=$psize*$pinx;
                //$query_sql ="";
                //$query_sql.="
                //    SELECT
                //        'mssr_book_class' AS `book_type`,
                //        `book_sid`,
                //        `book_isbn_10`,
                //        `book_isbn_13`,
                //        '' AS `book_library_code`,
                //        `book_no`,
                //        `keyin_mdate`
                //    FROM `mssr_book_class`
                //    WHERE 1=1
                //        -- FILTER在此
                //        {$filter}
                //";
                //if(trim($other_school_code)!==''){
                //    $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                //}else{
                //    $query_sql.="AND `school_code`='{$sess_school_code}'";
                //}
                //$tmp_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx,$psize),$arry_conn_mssr);
                //if(!empty($tmp_results)){
                //    foreach($tmp_results as $tmp_result){
                //        $tmp_total_results[]=$tmp_result;
                //    }
                //}else{
                //    $tmp_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
                //}
                //if($has_search_cno!==($sinx+count($tmp_results))){
                //    if($has_search_cno>$sinx+count($tmp_results)){
                //        $sinx2=0;
                //        $einx2=$psize-count($tmp_results);
                //    }else{
                //        $sinx2=$sinx-count($tmp_results);
                //        $einx2=$psize;
                //    }
                //    $query_sql ="";
                //    $query_sql.="
                //        SELECT
                //            'mssr_book_library' AS `book_type`,
                //            `book_sid`,
                //            `book_isbn_10`,
                //            `book_isbn_13`,
                //            `book_library_code`,
                //            '無' AS `book_no`,
                //            `keyin_mdate`
                //        FROM `mssr_book_library`
                //        WHERE 1=1
                //            -- FILTER在此
                //            {$filter}
                //    ";
                //    if(trim($other_school_code)!==''){
                //        $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                //    }else{
                //        $query_sql.="AND `school_code`='{$sess_school_code}'";
                //    }
                //    $tmp_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx2,$einx2),$arry_conn_mssr);
                //    if(!empty($tmp_results)){
                //        foreach($tmp_results as $tmp_result){
                //            $tmp_total_results[]=$tmp_result;
                //        }
                //    }
                //}
                //$arrys_result=$tmp_total_results;
            }
        }
//echo "<Pre>";
//print_r($arrys_result);
//echo "</Pre>";
//die();
    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,教師中心";

        //-----------------------------------------------
        //教師中心路徑選單
        //-----------------------------------------------

            $auth_sys_name_arry=auth_sys_name_arry();
            $FOLDER=explode('/',dirname($_SERVER['PHP_SELF']));
            $sys_ename=$FOLDER[count($FOLDER)-2];
            $mod_ename=$FOLDER[count($FOLDER)-1];
            $sys_cname='';  //系統名稱
            $mod_cname='';  //模組名稱

            foreach($auth_sys_name_arry as $key=>$val){
                if($key==$sys_ename){
                    $sys_cname=$val;
                }elseif($key==$mod_ename){
                    $mod_cname=$val;
                }
            }

            if((trim($sys_cname)=='')||(trim($mod_cname)=='')){
                $err ='teacher_center_path err!';

                if(1==2){//除錯用
                    echo "<pre>";
                    print_r($err);
                    echo "</pre>";
                    die();
                }
            }

            //連結路徑
            $sys_url ="";
            $sys_page=str_repeat("../",2)."index.php";
            $sys_arg =array(
                'sys_ename'  =>addslashes($sys_ename)
            );
            $sys_arg=http_build_query($sys_arg);
            $sys_url=$sys_page."?".$sys_arg;
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
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/table/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" />

    <style>
        /* 容器微調 */
        #container, #content, #teacher_datalist_tbl{
            width:760px;
        }
    </style>
</Head>

<Body>

<!-- 容器區塊 開始 -->
<div id="container">

    <!-- 內容區塊 開始 -->
    <div id="content">
        <table id="teacher_datalist_tbl" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td align="left" valign="middle" width="300px">
                    <!-- 教師中心路徑選單 開始 -->
                    <div id="teacher_center_path">
                        <table id="teacher_center_path_cont" border="0" width="100%">
                            <tr>
                                <td align="left" valign="middle" class="menu_dot">
                                    <span class="menu_cont">
                                        <img width="12" height="12" src="../../../../img/icon/blue.jpg" border="0">
                                        <!-- <a href="../../index.php">教師中心</a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span> -->
                                        <a href="<?php echo htmlspecialchars($sys_url);?>">
                                            <?php echo htmlspecialchars($sys_cname);?>
                                        </a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="javascript:void(0);"><?php echo htmlspecialchars($mod_cname);?></a>
                                    </span>
                                    <!-- <span style="position:relative;left:80px;background-color:#ffffff;">
                                        <input type="radio" id="book_adscription1" name="book_adscription" value="school" checked
                                        onclick='sel_book_adscription(this);void(0);' <?php if($sess_adscription==='school')echo 'checked';?>>全校
                                        <input type="radio" id="book_adscription2" name="book_adscription" value="self"
                                        onclick='sel_book_adscription(this);void(0);' <?php if($sess_adscription==='self')echo 'checked';?>>自行建立
                                    </span> -->
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- 教師中心路徑選單 結束 -->
                </td>
                <td align="right" valign="middle">
                    <!-- 查詢表單列 開始 -->
                    <div id="qform">
                        <span id="qform1"></span>
                    </div>
                    <!-- 查詢表單列 結束 -->
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <!-- 資料列表 開始 -->
                    <?php
                        if($filter===''){
                            page_qrs("明日星球,教師中心");
                        }else{
                            if($numrow!==0){
                                $arrys_result=$arrys_result;
                                page_hrs($title);
                            }else{
                                page_nrs($title);
                            }
                        }
                    ?>
                    <!-- 資料列表 結束 -->
                </td>
            </tr>
        </table>
    </div>
    <!-- 內容區塊 結束 -->

</div>
<!-- 容器區塊 結束 -->

<!-- 快速切換區塊 開始 -->
<?php echo fast_area($rd=2);?>
<!-- 快速切換區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var month=<?php echo (int)date("m");?>;
    var yest_month=<?php echo (int)date('m', strtotime('-1 month'));?>;
    var yest_month2=<?php echo (int)date('m', strtotime('-2 month'));?>;

    function sel_book_adscription(obj){
    //選擇顯示的書籍歸屬類型
        var adscription=trim(obj.value);
        var url ='';
        var page=str_repeat('../',0)+'query.php';
        var arg ={
            'adscription':adscription
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

        $.blockUI({
            message:'<h2 class="fc_white0">處理中，請稍後 !</h2>',
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity:.8,
                color: '#437C85'
            }
        });

        go(url,'self');
    }

    function _qform(){
    //---------------------------------------------------
    //查詢表單列
    //---------------------------------------------------

        //設定
        var configs={
            'book_ISBN':{
                'text'      :'ISBN編號',
                'type'      :'text',
                'id'        :'book_ISBN',
                'name'      :'book_ISBN',
                'vals'      :'',
                'className' :'qform_text'
            },
            'book_library':{
                'text'      :'登錄號',
                'type'      :'text',
                'id'        :'book_library',
                'name'      :'book_library',
                'vals'      :'',
                'className' :'qform_text'
            },
            'book_name':{
                'text'      :'書籍名稱',
                'type'      :'text',
                'id'        :'book_name',
                'name'      :'book_name',
                'vals'      :'',
                'className' :'qform_text'
            }
            //'borrow_state':{
            //    'text'      :'有無借閱',
            //    'type'      :'radio',
            //    'id'        :'borrow_state',
            //    'name'      :'borrow_state',
            //    'vals'      :{
            //        '借閱中':'借閱中',
            //        0:'無人借閱'
            //    },
            //    'className' :'qform_select'
            //},
            //'keyin_mdate':{
            //    'text'      :'建立時間',
            //    'type'      :'radio',
            //    'id'        :'keyin_mdate',
            //    'name'      :'keyin_mdate',
            //    'vals'      :{
            //        <?php echo (int)date('m', strtotime('-2 month'));?>:yest_month2+'月',
            //        <?php echo (int)date('m', strtotime('-1 month'));?>:yest_month+'月',
            //        <?php echo (int)date("m");?>:month+'月'
            //    },
            //    'className' :'qform_select'
            //}
            //'book_no':{
            //    'text'      :'書籍序號',
            //    'type'      :'text',
            //    'id'        :'book_no',
            //    'name'      :'book_no',
            //    'vals'      :'',
            //    'className' :'qform_text'
            //},
            //'book_author':{
            //    'text'      :'作者',
            //    'type'      :'text',
            //    'id'        :'book_author',
            //    'name'      :'book_author',
            //    'vals'      :'',
            //    'className' :'qform_text'
            //},
            //'book_publisher':{
            //    'text'      :'出版社',
            //    'type'      :'text',
            //    'id'        :'book_publisher',
            //    'name'      :'book_publisher',
            //    'vals'      :'',
            //    'className' :'qform_text'
            //},
            //'book_phonetic':{
            //    'text'      :'有無注音',
            //    'type'      :'radio',
            //    'id'        :'book_phonetic',
            //    'name'      :'book_phonetic',
            //    'vals'      :{
            //        '有':'有',
            //        '無':'無'
            //    },
            //    'className' :'qform_select'
            //}
        };

        var o_qform=qform(id='qform1',configs);
        var o_qform_form   =o_qform.qform_form;
        var o_qform_tbl    =o_qform.qform_tbl;
        var o_qform_type   =o_qform.qform_type;
        var o_qform_sbtn   =o_qform.qform_sbtn;
        var o_qform_abtn   =o_qform.qform_abtn;
        var o_qform_rbtn   =o_qform.qform_rbtn;
        var o_qform_tbl_mtd=o_qform.qform_tbl_mtd;

        o_qform_form.action="query.php";
        o_qform_form.method="POST";
        o_qform_form.target="IFC";
        o_qform_sbtn.className="ibtn_gr3020";
        o_qform_sbtn.style.margin="0 1px";
        o_qform_abtn.className="ibtn_gr3020";
        o_qform_abtn.style.margin="0 1px";
        o_qform_rbtn.className="ibtn_gr3020";
        o_qform_rbtn.style.margin="0 1px";

        o_qform_sbtn.onclick=function(){
            $.blockUI({
                message:'<h2 class="fc_white0">處理中，請稍後 !</h2>',
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity:.8,
                    color: '#437C85'
                }
            });
            o_qform_form.submit();
        }
        o_qform_abtn.onclick=function(){
            $.blockUI({
                message:'<h2 class="fc_white0">處理中，請稍後 !</h2>',
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity:.8,
                    color: '#437C85'
                }
            });
            var inx=o_qform_type.selectedIndex;
            var key=o_qform_type.options[inx].value;

            o_qform_tbl_mtd.innerHTML="";
            o_qform_form.submit();

            o_qform_type.options[inx].selected=true;
            o_qform._createElement(o_qform_tbl_mtd,key);
        }
        o_qform_rbtn.onclick=function(){
            var inx=o_qform_type.selectedIndex;
            var key=o_qform_type.options[inx].value;
            o_qform_tbl_mtd.innerHTML="";
            o_qform_type.options[inx].selected=true;
            o_qform._createElement(o_qform_tbl_mtd,key);
        }

        o_qform_sbtn.onmouseover=function(){
            this.style.cursor='pointer';
        }
        o_qform_abtn.onmouseover=function(){
            this.style.cursor='pointer';
        }
        o_qform_rbtn.onmouseover=function(){
            this.style.cursor='pointer';
        }
    }

    function choose_identity(){
    //開啟身分選擇區塊
        $.blockUI({
            message:$('#choose_identity'),
            css:{
                width:'260px'
            },
            overlayCSS:{
                backgroundColor:'#000',
                opacity:0.6,
                cursor:'default'
            }
        });
    }

    function choose_class_code(){
    //開啟班級選擇區塊
        $.blockUI({
            message:$('#choose_class_code'),
            css:{
                width:'260px'
            },
            overlayCSS:{
                backgroundColor:'#000',
                opacity:0.6,
                cursor:'default'
            }
        });
    }

    $(function(){
        //查詢表單列
        _qform();

        //快速切換設置
        fast_area_config('#fast_area',0,0);
    });

</script>
</Body>
</Html>


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
        global $arry_conn_mssr;
        global $arry_conn_user;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;
        global $auth_sys_check_lv;

        global $arrys_result;
        global $config_arrys;
        global $conn_mssr;
        global $conn_user;

        global $sess_school_code;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=4;  //欄位個數
        $btn_nos=3;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
?>
<!-- 資料列表 開始 -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <!-- 在此設定寬高 -->
        <td width="100%" height="250px" align="center" valign="top">
            <table id="mod_data_tbl" border="0" width="100%" cellpadding="5" cellspacing="0" style="margin-top:10px;" class="table_style1">
                <tr align="center" valign="middle" class="bg_gray1 fc_white0">
                    <td width="156px">
                        <span style="position:relative;float:left;">
                            <input type="text" id="prt_list" name="prt_list" value="" style="display:none;">

                            <input type="checkbox" style="position:relative;top:3px;display:none;" onclick="muilt_chk(this);void(0);">

                            <input id="BtnP" type="button" value="列印" class="" onclick="" onmouseover="this.style.cursor='pointer'"
                            style="width:40px;height:22px;font-size:12px;display:none;">

                            <input type="button" value="展開" class="" onclick="view_change(this,this.value);" onmouseover="this.style.cursor='pointer'"
                            style="width:40px;height:22px;font-size:12px;display:none;">
                        </span>
                        <span style="position:relative;top:0px;">
                            書籍編號
                        </span>
                    </td>
                    <td width="305px">書籍名稱  </td>
                    <td width="60px">序號       </td>
                    <td width="60px">借閱人數   </td>
                    <td width="60px">借閱次數   </td>
                    <td width="">功能           </td>
                </tr>

                <?php foreach($arrys_result as $inx=>$arry_result) :?>
                <?php
                //---------------------------------------------------
                //接收欄位
                //---------------------------------------------------
                //create_by           建立者
                //edit_by             修改者
                //school_code         學校代號
                //school_category     學校類別
                //grade_id            年級
                //classroom_id        班級
                //book_id             書籍主索引
                //book_sid            書籍識別碼
                //book_isbn_10        書籍ISBN10碼
                //book_isbn_13        書籍ISBN13碼
                //book_library_code   書籍圖書館條碼
                //book_no             書籍序號
                //book_name           書籍名稱
                //book_author         書籍作者
                //book_publisher      書籍出版社
                //book_page_count     書籍頁數
                //book_word           書籍總字數
                //book_note           書籍備註
                //book_phonetic       書籍有無注音
                //keyin_cdate         建立時間
                //keyin_mdate         修改時間
                //keyin_ip            登打IP

                    extract($arry_result, EXTR_PREFIX_ALL, "rs");

                //---------------------------------------------------
                //處理欄位
                //---------------------------------------------------

                    //book_sid            書籍識別碼
                    $rs_book_sid=addslashes(trim($rs_book_sid));

                    $get_book_info=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);

                    //book_no             書籍序號
                    $rs_book_no=(int)$rs_book_no;

                    //book_name           書籍名稱
                    $rs_book_name='';
                    if(!empty($get_book_info)){
                        $rs_book_name=trim($get_book_info[0]['book_name']);
                        if(mb_strlen($rs_book_name)>16){
                            $rs_book_name=mb_substr($rs_book_name,0,16)."..";
                        }
                    }

                    $rs_book_code="";
                    $rs_book_code_html='';
                    switch(trim($rs_book_type)){
                        case 'mssr_book_class':
                            $rs_book_code=$rs_book_isbn_13;
                            $rs_book_code_html=$rs_book_isbn_13;
                        break;
                        case 'mssr_book_library':
                            $rs_book_code=$rs_book_library_code;
                            $rs_book_code_html=$rs_book_library_code;
                        break;
                    }
                    //if(mb_strlen($rs_book_code)>11){
                    //    $rs_book_code_html=mb_substr($rs_book_code,0,11)."..";
                    //}

                //---------------------------------------------------
                //特殊處理
                //---------------------------------------------------

                    //-----------------------------------------------
                    //查找借閱人數
                    //-----------------------------------------------

                        $sql="
                            SELECT `user_id`
                            FROM `mssr_book_borrow_log`
                            WHERE 1=1
                                AND `book_sid`='{$rs_book_sid}'
                            GROUP bY `user_id`,`book_sid`
                        ";
                        $borrow_results_group=$conn_mssr->prepare($sql);
                        $borrow_results_group->execute();
                        $borrow_results_group=$borrow_results_group->rowCount();

                    //-----------------------------------------------
                    //查找借閱次數
                    //-----------------------------------------------

                        $sql="
                            SELECT `user_id`
                            FROM `mssr_book_borrow_log`
                            WHERE 1=1
                                AND `book_sid`='{$rs_book_sid}'
                        ";
                        $borrow_results_cno=$conn_mssr->prepare($sql);
                        $borrow_results_cno->execute();
                        $borrow_results_cno=$borrow_results_cno->rowCount();
                ?>
                <tr>
                    <td align="left" valign="middle">

                        <input name="chk_book" type="checkbox" value="<?php echo htmlspecialchars($rs_book_code);?>"
                        style="position:relative;top:3px;display:none;"
                        onclick="single_chk(this);void(0);">

                        <?php if(trim($rs_book_type)==='mssr_book_class'):?>
                            13碼: <?php echo htmlspecialchars($rs_book_code_html);?><br/>
                            <span class="isbn_10" style="">
                                10碼: <?php echo htmlspecialchars($rs_book_isbn_10);?>
                            </span>
                        <?php else:?>
                            <?php if(preg_match("/{$sess_school_code}/i", $rs_book_code_html)):?>
                                自訂:
                            <?php else:?>
                                圖書館:
                            <?php endif;?>
                            <?php echo htmlspecialchars($rs_book_code_html);?>
                        <?php endif;?>

                    </td>
                    <td align="center" valign="middle"><?php echo htmlspecialchars($rs_book_name  );?>     </td>
                    <td align="center" valign="middle">
                        <?php if(trim($rs_book_type)==='mssr_book_class'):?>
                            <?php echo $rs_book_no;?>
                        <?php else:?>
                            無
                        <?php endif;?>
                    </td>
                    <td align="center" valign="middle">
                        <?php echo $borrow_results_group;?>
                    </td>
                    <td align="center" valign="middle">
                        <?php echo $borrow_results_cno;?>
                    </td>

                    <td align="center" valign="middle">
                        <input type="button" value="詳細" class="ibtn_gr3020"
                            onclick="detail('<?php echo addslashes($rs_book_sid);?>');"
                            onmouseover="this.style.cursor='pointer'"
                        >
                    </td>
                </tr>
                <?php endforeach ;?>
            </table>

            <table border="0" width="100%">
                <tr valign="middle">
                    <td align="left">
                        <!-- 分頁列 -->
                        <span id="page" style="position:relative;top:10px;"></span>
                        <span style="position:relative;top:0px;display:none;" class="fc_brown0">
                            到
                            <input id="page_val" type="text" value="" size="10" maxlength="20"
                            class="form_text" style="width:30px">
                            頁
                            <input type="button" value="GO" class="ibtn_gr3020"
                            onclick="page_go();void(0);"
                            onmouseover="this.style.cursor='pointer'">
                        </span>
                    </td>
                    <td align="right">
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
    var $isbn_10=$('.isbn_10');
    var oBtnP=document.getElementById('BtnP');
    var oprt_list=document.getElementById('prt_list');

    function detail(book_sid){

        var url ='';
        var page=str_repeat('../',0)+'detail/detailF.php';
        var arg ={
            'psize'   :psize,
            'pinx'    :pinx,
            'book_sid':book_sid
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

    window.onload=function(){

        //套表格列奇偶色
        table_hover(tbl_id='mod_data_tbl',c_odd='#fff',c_even='#fff',c_on='#e6faff');

        //分頁列
        var cid         ="page";                        //容器id
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
            'page_name' :'index.php',
            'page_args' :{}
        }
        var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);

        var arry_book_code=[];
        var ochk_books=document.getElementsByName('chk_book');
        var cno=0;
        for(var i=0;i<ochk_books.length;i++){
            var ochk_book=ochk_books[i];
            var book_code=trim(ochk_book.value);
            if(ochk_book.checked===true){
                cno++;
                arry_book_code.push(book_code);
            }
        }
        if(cno>=1){
            $(oBtnP).show();
            oprt_list.value=arry_book_code;
        }else{
            $(oBtnP).hide();
            oprt_list.value="";
        }
    }

</script>

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

        global $auth_sys_check_lv;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0;  //欄位個數
        $btn_nos=0;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
?>
<!-- 資料列表 開始 -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <!-- 在此設定寬高 -->
        <td width="100%" height="250px" align="center" valign="top">
            <!-- 內容 -->
            <table border="0" width="100%" cellpadding="5" cellspacing="0" style="position:relative;top:50px;" class="table_style2">
                <tr align="center" valign="middle" class="bg_gray1">
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td height="250px" align="center" valign="middle">
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

    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;

    window.onload=function(){

    }

    function add(){
    //新增
        var url ='';
        var page=str_repeat('../',0)+'add/sel_book_type/addF.php';
        var arg ={
            'psize':psize,
            'pinx' :pinx
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

        $.blockUI({
            message:'<h2 class="fc_white0">處理中，請稍後 !</h2>',
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity:.8,
                color: '#437C85'
            }
        });

        go(url,'self');
    }

</script>

<?php
//-------------------------------------------------------
//page_nrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>
<?php };?>

<?php function page_qrs($title="") {?>
<?php
//-------------------------------------------------------
//page_qrs 區塊 -- 開始
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

        global $auth_sys_check_lv;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0;  //欄位個數
        $btn_nos=0;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
?>
<!-- 資料列表 開始 -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <!-- 在此設定寬高 -->
        <td width="100%" height="250px" align="center" valign="top">
            <!-- 內容 -->
            <table border="0" width="100%" cellpadding="5" cellspacing="0" style="position:relative;top:50px;" class="table_style2">
                <tr align="center" valign="middle" class="bg_gray1">
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td height="250px" align="center" valign="middle">
                        <img src="../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
                        請查詢書籍<br/>
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

    function add(){
    //新增
        var url ='';
        var page=str_repeat('../',0)+'add/sel_book_type/addF.php';
        var arg ={
            'psize':psize,
            'pinx' :pinx
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

        $.blockUI({
            message:'<h2 class="fc_white0">處理中，請稍後 !</h2>',
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity:.8,
                color: '#437C85'
            }
        });

        go(url,'self');
    }

</script>

<?php
//-------------------------------------------------------
//page_qrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>
<?php };?>

<script type="text/javascript" src="../../../../inc/external/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//呼叫使用者頁面紀錄
//-------------------------------------------------------
    user_page_log(rd=4);
</script>