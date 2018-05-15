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

                    APP_ROOT.'lib/php/db/code'
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

        //-----------------------------------------------
        //身份設定
        //-----------------------------------------------

            //初始化, 身份參考陣列, 1(校長使用者) | 2(主任使用者) | 3(老師使用者)
            $arrys_identity=array(1,2,3);

            //初始化, 身份數量
            $identity_cno=0;

            //初始化, 選擇身份指標
            $choose_identity_flag=true;

            //身份數量統計
            foreach($arrys_login_info as $responsibilities=>$arry_login_info){
                $responsibilities=(int)$responsibilities;
                if(in_array($responsibilities,$arrys_identity)){
                    $identity_cno++;
                }
            }

            //只有1種身份, 自動回填至 $_SESSION['tc']['t|dt']
            if($identity_cno===0){
                $choose_identity_flag=false;
                $_SESSION['tc']['t|dt']=$arry_login_info;
            }else if($identity_cno===1){
                $choose_identity_flag=false;
                foreach($arrys_login_info as $responsibilities=>$arry_login_info){
                    $_SESSION['tc']['t|dt']=$arry_login_info;
                }
                //移除多餘的班級
                foreach($_SESSION['tc']['t|dt'] as $field_name=>$field_value){
                    if($field_name==='arrys_class_code'){
                        foreach($field_value as $inx=>$val){
                            if($inx!==0){
                                unset($_SESSION['tc']['t|dt']['arrys_class_code'][$inx]);
                            }
                        }
                    }
                }
                //沒有任何班級，指派空陣列
                if(!isset($_SESSION['tc']['t|dt']['arrys_class_code'])){
                    $_SESSION['tc']['t|dt']['arrys_class_code']=array();
                }
            }else{
            //如果已選定身份的話
                if((isset($_SESSION['tc']['t|dt']))&&(!empty($_SESSION['tc']['t|dt']))){
                    $choose_identity_flag=false;
                    //移除多餘的班級
                    foreach($_SESSION['tc']['t|dt'] as $field_name=>$field_value){
                        if($field_name==='arrys_class_code'){
                            foreach($field_value as $inx=>$val){
                                if($inx!==0){
                                    unset($_SESSION['tc']['t|dt']['arrys_class_code'][$inx]);
                                }
                            }
                        }
                    }
                    //沒有任何班級，指派空陣列
                    if(!isset($_SESSION['tc']['t|dt']['arrys_class_code'])){
                        $_SESSION['tc']['t|dt']['arrys_class_code']=array();
                    }
                }
            }

        //-----------------------------------------------
        //basic
        //-----------------------------------------------

            $sess_login_info=(isset($_SESSION['tc']['t|dt']))?$_SESSION['tc']['t|dt']:array();

        //-----------------------------------------------
        //其餘設定
        //-----------------------------------------------

            //清空用戶類型
            switch(is_array($_SESSION['config']['user_type'])){
                case true:
                    foreach($_SESSION['config']['user_types'] as $inx=>$val){
                        if(!in_array(trim($val),array_map("trim",$_SESSION['config']['user_type']))){
                            unset($_SESSION[$val]);
                        }
                    }
                break;

                default:
                    foreach($_SESSION['config']['user_types'] as $inx=>$val){
                        if(trim($val)!=trim($_SESSION['config']['user_type'])){
                            unset($_SESSION[$val]);
                        }
                    }
                break;
            }

            //區域資訊
            if(!in_array('teacher_center',$_SESSION['config']['user_area'])){
                array_push($_SESSION['config']['user_area'],"teacher_center");
            }

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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_book_ranking');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        $get_chk=array(
            'book_sid',
            'semester_start',
            'semester_end'
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

        //GET
        $book_sid         =trim($_GET[trim('book_sid         ')]);
        $semester_start   =trim($_GET[trim('semester_start   ')]);
        $semester_end     =trim($_GET[trim('semester_end     ')]);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

        $arry_err=array();

        if($book_sid===''){
           $arry_err[]='書籍識別碼,未輸入!';
        }
        if($semester_start===''){
           $arry_err[]='未輸入!';
        }
        if($semester_end===''){
           $arry_err[]='未輸入!';
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

        //建立連線
        $conn_user=conn($db_type='mysql',$arry_conn_user);
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //撈取, 使用者名稱
        //-----------------------------------------------

            $rs_book_name='';
            $rs_book_type='';
            $rs_book_code='';
            $get_book_info=get_book_info($conn_mssr,trim($book_sid),$array_filter=array('book_isbn_10','book_isbn_13','book_name'),$arry_conn_mssr);
            if(!empty($get_book_info)){

                //book_name     書籍名稱
                $rs_book_name=trim($get_book_info[0]['book_name']);
                if(mb_strlen($rs_book_name)>20){
                    $rs_book_name=mb_substr($rs_book_name,0,20)."..";
                }

                $rs_book_type="";
                $rs_book_code="";
                $rs_book_isbn_10=trim($get_book_info[0]['book_isbn_10']);
                $rs_book_isbn_13=trim($get_book_info[0]['book_isbn_13']);
                $rs_book_library_code=trim($get_book_info[0]['book_library_code']);
                switch(trim($rs_book_library_code)){
                    case '':
                        $rs_book_type='mssr_book_class';
                        $rs_book_code=$rs_book_isbn_13;
                    break;
                    default:
                        $rs_book_type='mssr_book_library';
                        $rs_book_code=$rs_book_library_code;
                    break;
                }
            }else{}

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,教師中心";

        //註腳列
        $footer=footer($rd=5);

        //系統陣列
        //$header_right_sys_arry   =$config_arrys['center']['teacher_center']['report']['header_right']['sys_arry'];

        //系統名稱陣列
        //$header_right_name_arry  =$config_arrys['center']['teacher_center']['report']['header_right']['name_arry'];

        //系統連結陣列
        //$header_right_url_arry   =$config_arrys['center']['teacher_center']['report']['header_right']['url_arry'];

        //系統連結框架陣列
        //$header_right_target_arry=$config_arrys['center']['teacher_center']['report']['header_right']['target_arry'];

        //系統權限陣列(報表專用)
        $auth_sys_arry_report=auth_sys_arry_report();

        //系統權限陣列(功能專用)
        $auth_sys_arry_config=auth_sys_arry_config();

        //系統權限名稱陣列
        $auth_sys_name_arry=auth_sys_name_arry();

        //系統權限圖片陣列
        $auth_sys_img_arry=auth_sys_img_arry();

        //系統權限連結陣列
        $auth_sys_url_arry=auth_sys_url_arry();

        //系統權限連結框架陣列
        $auth_sys_target_arry=auth_sys_target_arry();

        //-----------------------------------------------
        //教師中心路徑選單
        //-----------------------------------------------

            $auth_sys_name_arry=auth_sys_name_arry();
            $FOLDER=explode('/',dirname($_SERVER['PHP_SELF']));
            $sys_ename=$FOLDER[count($FOLDER)-3];
            $mod_ename=$FOLDER[count($FOLDER)-2];
            $sys_cname='';  //系統名稱
            $mod_cname='';  //模組名稱

            //清除模組查詢資料
            foreach(auth_sys_arry_report() as $sys_name=>$mods_arry){
                foreach($mods_arry as $mod_name=>$mod_arry){
                    if($mod_name!==$mod_ename){
                        unset($_SESSION[$mod_name]);
                    }
                }
            }

            //進入許可檢核
            $access=$auth_sys_arry_report[$sys_ename][$mod_ename]['access'];
            if(!$access){
                $url=str_repeat("../",6).'index.php';
                header("Location: {$url}");
                die();
            }

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
            $sys_page=str_repeat("../",3)."index.php";
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
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/jquery/plugin/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/table/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../css/def.css" media="all" />
</Head>

<Body>

<!-- 容器區塊 開始 -->
<div id="container">

    <!-- 標題區塊 開始 -->
    <div id="header">

        <!-- 標題左方區塊 開始 -->
        <ul id="header_left">
            <li>
                <a href="#">
                    <span>
                        您好 !
                        <?php
                            if(isset($sess_login_info['name'])){
                                echo htmlspecialchars($sess_login_info['name']);
                            }
                        ?>
                        歡迎使用-學習歷程專區。
                    </span>
                </a>
            </li>
        </ul>
        <!-- 標題左方區塊 結束 -->

        <!-- 標題右方區塊 開始 -->
        <ul id="header_right" style="display:none;">
        <?php foreach($header_right_sys_arry as $sys=>$mod_arry):?>
        <?php
            $sys_en=trim($sys);
            $sys_ch=trim($header_right_name_arry[$sys_en]);
            $sys_url=trim($header_right_url_arry[$sys_en]);
            $sys_target=trim($header_right_target_arry[$sys_en]);
        ?>
            <li>
                <a href="<?php echo $sys_url;?>" target="<?php echo $sys_target;?>" rel="nofollow"><span><?php echo htmlspecialchars($sys_ch);?></span></a>
                <?php if(!empty($mod_arry)):?>
                <ul class="child">
                <?php foreach($mod_arry as $inx=>$mod):?>
                <?php
                    $mod_en=trim($mod);
                    $mod_ch=trim($header_right_name_arry[$mod_en]);
                    $mod_url=trim($header_right_url_arry[$mod_en]);
                    $mod_target=trim($header_right_target_arry[$mod_en]);
                ?>
                    <li><a href="<?php echo $mod_url;?>" target="<?php echo $mod_target;?>" rel="nofollow"><?php echo htmlspecialchars($mod_ch);?></a></li>
                <?php endforeach;?>
                </ul>
                <?php endif;?>
            </li>
        <?php endforeach;?>
        </ul>
        <!-- 標題右方區塊 結束 -->

    </div>
    <!-- 標題區塊 結束 -->

    <!-- 內容區塊 開始 -->
    <div id="content">

        <!-- 內容區塊(上半部) 開始 -->
        <div id="content_top">

            <!-- logo 開始 -->
            <div id="logo">
                明日書店
                <span id="logo_min">
                    <span class="fc_blue0"><?php echo htmlspecialchars($rs_book_name);?></span>
                    <?php //echo htmlspecialchars($mod_cname);?>
                </span>
            </div>
            <!-- logo 結束 -->

        </div>
        <!-- 內容區塊(上半部) 結束 -->

        <!-- 內容區塊(下半部) 開始 -->
        <div id="content_bottom">
            <table id="teacher_datalist_tbl" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="left" valign="middle" width="450px">
                        <!-- 導覽列 開始 -->
                        <div id="navbar" style="display:none;">
                            <ul id="navbar_ul">
                            <?php foreach($auth_sys_arry_report as $sys=>$mod_arry):?>
                            <?php
                                $sys_en=trim($sys);
                                $sys_ch=trim($auth_sys_name_arry[$sys_en]);
                                $sys_url=trim($auth_sys_url_arry[$sys_en]);
                                $sys_target=trim($auth_sys_target_arry[$sys_en]);
                            ?>
                                <li>
                                    <a href="<?php echo $sys_url;?>" target="<?php echo $sys_target;?>" rel="nofollow"><?php echo htmlspecialchars($sys_ch);?></span></a>
                                    <?php if(!empty($mod_arry)):?>
                                    <ul class="child">
                                    <?php foreach($mod_arry as $mod=>$auth_arry):?>
                                    <?php
                                        $mod_en=trim($mod);
                                        $mod_ch=trim($auth_sys_name_arry[$mod_en]);
                                        $mod_url=trim($auth_sys_url_arry[$mod_en]);
                                        $mod_target=trim($auth_sys_target_arry[$mod_en]);
                                    ?>
                                        <li><a href="<?php echo $mod_url;?>" target="<?php echo $mod_target;?>" rel="nofollow"><?php echo htmlspecialchars($mod_ch);?></a></li>
                                    <?php endforeach;?>
                                    </ul>
                                    <?php endif;?>
                                </li>
                            <?php endforeach;?>
                            </ul>
                        </div>
                        <!-- 導覽列 結束 -->
                    </td>
                    <td align="right" valign="middle">
                        <!-- 查詢表單列 開始 -->

                        <!-- 查詢表單列 結束 -->
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <!-- 資料列表 開始 -->
                        <iframe id="IFC" name="IFC" src="content.php?book_sid=<?php echo $book_sid;?>&semester_start=<?php echo $semester_start;?>&semester_end=<?php echo $semester_end;?>" frameborder="0"
                        style="width:100%;height:600px;overflow:hidden;overflow-y:auto"></iframe>
                        <!-- 資料列表 結束 -->
                    </td>
                </tr>
            </table>
        </div>
        <!-- 內容區塊(下半部) 結束 -->

    </div>
    <!-- 內容區塊 結束 -->

    <!-- 註腳區塊 開始 -->
    <div id="footer">
        <div id="footline"></div>
        <span id="footbar">
            <!-- 註腳列 -->
            <?php foreach($footer as $footer_item) :?>
            <?php
            //-------------------------------------------
            //選項
            //-------------------------------------------
            //key       代碼
            //cname     文字
            //url       路徑
            //target    框架    _blank | _self | 視窗id
            //state     狀態    on:顯示 | off:隱藏
            //-------------------------------------------

                $key   =trim($footer_item['key']);
                $cname =trim($footer_item['cname']);
                $url   =trim($footer_item['url']);
                $target=trim($footer_item['target']);
                $state =trim($footer_item['state']);
            ?>
                <a href="<?php echo $url;?>" target="<?php echo $target;?>"><?php echo $cname;?></a>&nbsp;
            <?php endforeach ;?>
        </span>
    </div>
    <!-- 註腳區塊 結束 -->

    <!-- google分析 開始 -->
    <?php echo google_analysis($allow=false);?>
    <!-- google分析 結束 -->

</div>
<!-- 容器區塊 結束 -->

<!-- 快速切換區塊 開始 -->
<?php //echo fast_area($rd=4);?>
<!-- 快速切換區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var choose_identity_flag=<?php echo ($choose_identity_flag)?1:0;?>;

    $(function(){

        //if(choose_identity_flag===1){
        //    alert('請先選擇身份!');
        //    choose_identity();
        //}

        //快速切換設置
        //fast_area_config('#fast_area',_right=0,_bottom=10);
    });

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

</script>
</Body>
</Html>
