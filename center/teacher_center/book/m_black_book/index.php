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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_black_book');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //SESSION
        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用

        if(isset($_SESSION['m_black_book']['filter'])){
            $filter=$_SESSION['m_black_book']['filter'];
        }
        if(isset($_SESSION['m_black_book']['query_fields'])){
            $query_fields=$_SESSION['m_black_book']['query_fields'];
        }
        if(isset($_SESSION['m_black_book'])&&(isset($_SESSION['m_black_book']['adscription']))){
            $sess_adscription=trim($_SESSION['m_black_book']['adscription']);
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

        //---------------------------------------------------
        //SQL 查詢
        //---------------------------------------------------

            $sql="
                SELECT *
                FROM `mssr`.`mssr_black_book`
                    INNER JOIN `user`.`member` ON
                    `mssr`.`mssr_black_book`.`create_by`=`user`.`member`.`uid`
                WHERE 1=1
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=count($db_results);     //資料總筆數
        $psize =10;                     //單頁筆數,預設10筆
        $pnos  =0;                      //分頁筆數
        $pinx  =1;                      //目前分頁索引,預設1
        $sinx  =0;                      //值域起始值
        $einx  =0;                      //值域終止值

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

        $numrow=count($db_results);

        $pnos  =ceil($numrow/$psize);
        $pinx  =($pinx>$pnos)?$pnos:$pinx;

        $sinx  =(($pinx-1)*$psize);
        $einx  =(($pinx)*$psize);
        $einx  =($einx>$numrow)?$numrow:$einx;
        //echo $numrow."<br/>";

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
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- 教師中心路徑選單 結束 -->
                </td>
                <td align="right" valign="middle">
                    <!-- 查詢表單列 開始 -->
                    <!-- 查詢表單列 結束 -->
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <!-- 資料列表 開始 -->
                    <?php
                        if($numrow!==0){
                            $arrys_result=$db_results;
                            page_hrs($title);
                        }else{
                            page_nrs($title);
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
            },
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
            'keyin_mdate':{
                'text'      :'建立時間',
                'type'      :'radio',
                'id'        :'keyin_mdate',
                'name'      :'keyin_mdate',
                'vals'      :{
                    <?php echo (int)date('m', strtotime('-2 month'));?>:yest_month2+'月',
                    <?php echo (int)date('m', strtotime('-1 month'));?>:yest_month+'月',
                    <?php echo (int)date("m");?>:month+'月'
                },
                'className' :'qform_select'
            }
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
        //_qform();

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
        global $sess_user_id;

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
                    <td width="150px">建立人</td>
                    <td width="225px">書籍名稱</td>
                    <td width="225px">編號</td>
                    <td width="">功能         </td>
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

                    $rs_book_nonumbering=trim($rs_book_nonumbering);
                    $rs_name            =trim($rs_name);
                    $rs_create_by       =(int)$rs_create_by;

                    $sql="
                        SELECT
                            `mssr`.`mssr_book_global`.`book_name`
                        FROM `mssr`.`mssr_book_global`
                        WHERE 1=1
                            AND (
                                `mssr`.`mssr_book_global`.`book_sid` IN ('{$rs_book_nonumbering}')
                                OR
                                `mssr`.`mssr_book_global`.`book_isbn_10` IN ('{$rs_book_nonumbering}')
                                OR
                                `mssr`.`mssr_book_global`.`book_isbn_13` IN ('{$rs_book_nonumbering}')
                            )
                            UNION
                        SELECT
                            `mssr`.`mssr_book_class`.`book_name`
                        FROM `mssr`.`mssr_book_class`
                        WHERE 1=1
                            AND (
                                `mssr`.`mssr_book_class`.`book_sid` IN ('{$rs_book_nonumbering}')
                                OR
                                `mssr`.`mssr_book_class`.`book_isbn_10` IN ('{$rs_book_nonumbering}')
                                OR
                                `mssr`.`mssr_book_class`.`book_isbn_13` IN ('{$rs_book_nonumbering}')
                            )
                            UNION
                        SELECT
                            `mssr`.`mssr_book_library`.`book_name`
                        FROM `mssr`.`mssr_book_library`
                        WHERE 1=1
                            AND (
                                `mssr`.`mssr_book_library`.`book_sid` IN ('{$rs_book_nonumbering}')
                                OR
                                `mssr`.`mssr_book_library`.`book_isbn_10` IN ('{$rs_book_nonumbering}')
                                OR
                                `mssr`.`mssr_book_library`.`book_isbn_13` IN ('{$rs_book_nonumbering}')
                            )
                            UNION
                        SELECT
                            `mssr`.`mssr_book_unverified`.`book_name`
                        FROM `mssr`.`mssr_book_unverified`
                        WHERE 1=1
                            AND (
                                `mssr`.`mssr_book_unverified`.`book_sid` IN ('{$rs_book_nonumbering}')
                                OR
                                `mssr`.`mssr_book_unverified`.`book_isbn_10` IN ('{$rs_book_nonumbering}')
                                OR
                                `mssr`.`mssr_book_unverified`.`book_isbn_13` IN ('{$rs_book_nonumbering}')
                            )

                    ";
                    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    $rs_book_name='';
                    if(!empty($db_results)){
                        foreach($db_results as $db_result){
                            extract($db_result, EXTR_PREFIX_ALL, "rs");
                            $rs_book_name=trim($rs_book_name);
                        }
                    }
                ?>
                <tr>
                    <td align="center" valign="middle">
                        <?php echo htmlspecialchars($rs_name);?>
                    </td>
                    <td align="center" valign="middle">
                        <?php echo htmlspecialchars($rs_book_name);?>
                    </td>
                    <td align="center" valign="middle">
                        <?php echo htmlspecialchars($rs_book_nonumbering);?>
                    </td>
                    <td align="center" valign="middle">
                        <?php //if($rs_create_by===$sess_user_id):?>
                            <input type="button" value="移除" class="ibtn_gr3020"
                            onclick="del('<?php echo addslashes($rs_book_nonumbering);?>');void(0);"
                            onmouseover="this.style.cursor='pointer'">
                        <?php //endif;?>
                    </td>
                </tr>
                <?php endforeach ;?>
            </table>

            <table border="0" width="100%">
                <tr valign="middle">
                    <td align="left">
                        <!-- 分頁列 -->
                        <span id="page" style="position:relative;top:10px;"></span>
                    </td>
                    <td align="right">
                        <span style="position:relative;top:10px;right:8px;">
                            <input type="text" id="book_nonumbering" name="book_nonumbering" value="" placeholder="請輸入書籍編號">
                            <input type="button" value="新增" class="ibtn_gr3020" onclick="add();" onmouseover="this.style.cursor='pointer'">
                        </span>
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

    function del(book_nonumbering){
    //移除

        var url ='';
        var page=str_repeat('../',0)+'del/delA.php';
        var arg ={
            'psize'   :psize,
            'pinx'    :pinx,
            'book_nonumbering':book_nonumbering
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

        if(confirm('你確定要移除嗎?')){
            go(url,'self');
        }else{
            return false;
        }
    }

    function add(){
    //新增

        var obook_nonumbering=document.getElementById('book_nonumbering');
        var book_nonumbering =$.trim(obook_nonumbering.value);
        var url ='';
        var page=str_repeat('../',0)+'add/addA.php';
        var arg ={
            'psize'   :psize,
            'pinx'    :pinx,
            'book_nonumbering':book_nonumbering
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

        if(book_nonumbering===''){
            alert('請輸入書籍編號');
            return false;
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
                        目前系統無資料，或查無資料!<br/>
                        <input type="text" id="book_nonumbering" name="book_nonumbering" value="" placeholder="請輸入書籍編號">
                        <input type="button" value="新增" class="ibtn_gr3020" onclick="add();" onmouseover="this.style.cursor='pointer'">
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

        var obook_nonumbering=document.getElementById('book_nonumbering');
        var book_nonumbering =$.trim(obook_nonumbering.value);
        var url ='';
        var page=str_repeat('../',0)+'add/addA.php';
        var arg ={
            'psize'   :psize,
            'pinx'    :pinx,
            'book_nonumbering':book_nonumbering
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

        if(book_nonumbering===''){
            alert('請輸入書籍編號');
            return false;
        }

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

<script type="text/javascript" src="../../../../inc/external/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//呼叫使用者頁面紀錄
//-------------------------------------------------------
    user_page_log(rd=4);
</script>