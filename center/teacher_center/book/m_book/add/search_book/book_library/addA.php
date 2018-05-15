<?php
//-------------------------------------------------------
//教師中心
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();
        @header("Cache-control: private");

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",7).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/vaildate/code',
                    APP_ROOT.'lib/php/string/code',
                    APP_ROOT.'lib/php/db/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

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
    //班級判斷
    //---------------------------------------------------

        $has_class_code=true;
        $arrys_class_code=$sess_login_info['arrys_class_code'];
        if(count($arrys_class_code)===0){
            $has_class_code=false;
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //book_code     書籍編號

        $post_chk=array(
            'book_code'
        );
        $post_chk=array_map("trim",$post_chk);
        foreach($post_chk as $post){
            if(!isset($_POST[$post])){
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //book_code     書籍編號

        //POST
        $book_code      =trim($_POST[trim('book_code')]);
        $book_source    =(isset($_POST['book_source']))?trim($_POST['book_source']):'school';

        //SESSION
        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);

        //有班級才撈取
        if($has_class_code){
            $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
            $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
            $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];
        }

        //分頁
        $psize=(isset($_POST['psize']))?(int)$_POST['psize']:10;
        $pinx =(isset($_POST['pinx']))?(int)$_POST['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //book_code     書籍編號

        // $arry_err=array();

        // if($book_code===''){

        //     no_book_code($book_code);
        // //    $arry_err[]='書籍編號,未輸入!';
        // // }

        // // if(count($arry_err)!==0){
        // //     if(1==2){//除錯用
        // //         echo "<pre>";
        // //         print_r($arry_err);
        // //         echo "</pre>";
        // //     }
        //     die();
        // }

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //book_code     書籍編號

            $sess_user_id       =(int)$sess_user_id;
            $sess_school_code   =mysql_prep($sess_school_code);

            if($has_class_code){
                $sess_grade     =(int)$sess_grade;
                $sess_classroom =(int)$sess_classroom;
            }else{
                $sess_grade     =1;
                $sess_classroom =1;
            }

            $book_code          =mysql_prep($book_code);
            if($book_code===''){
                no_book_code($book_code);
                die();
            }

            //初始化, 找書狀況
            $has_find           =false;

            //初始化, 找到的地方
            $find_area          ='';

            //初始化, 找到的書籍資訊
            $arrys_find_book_info=array();

            //-------------------------------------------
            //班級是否有此書籍
            //-------------------------------------------

                if(!$has_find){
                    $sql="
                        SELECT
                            `book_sid`,
                            `book_isbn_10`,
                            `book_isbn_13`,
                            `book_name`,
                            `book_author`,
                            `book_publisher`
                        FROM `mssr_book_class`
                        WHERE 1=1
                            AND (
                                `book_isbn_10` = '{$book_code}'
                                    OR
                                `book_isbn_13` = '{$book_code}'
                            )
                    ";

                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    $numrow=count($arrys_result);

                    if($numrow!==0){
                        $has_find=true;
                        $find_area='class';
                        $arrys_find_book_info=$arrys_result;
                    }
                }

            //-------------------------------------------
            //圖書館是否有此書籍
            //-------------------------------------------

                if(!$has_find){
                    $sql="
                        SELECT
                            `book_sid`,
                            `book_isbn_10`,
                            `book_isbn_13`,
                            `book_name`,
                            `book_author`,
                            `book_publisher`
                        FROM `mssr_book_library`
                        WHERE 1=1
                            AND (
                                `book_isbn_10` = '{$book_code}'
                                    OR
                                `book_isbn_13` = '{$book_code}'
                            )
                    ";

                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    $numrow=count($arrys_result);

                    if($numrow!==0){
                        $has_find=true;
                        $find_area='library';
                        $arrys_find_book_info=$arrys_result;
                    }
                }

            //-------------------------------------------
            //系統書庫是否有此書籍
            //-------------------------------------------

                if(!$has_find){
                    $sql="
                        SELECT
                            `book_sid`,
                            `book_isbn_10`,
                            `book_isbn_13`,
                            `book_name`,
                            `book_author`,
                            `book_publisher`
                        FROM `mssr_book_global`
                        WHERE 1=1
                            AND (
                                `book_isbn_10` = '{$book_code}'
                                    OR
                                `book_isbn_13` = '{$book_code}'
                            )
                    ";

                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    $numrow=count($arrys_result);

                    if($numrow!==0){
                        $has_find=true;
                        $find_area='global';
                        $arrys_find_book_info=$arrys_result;
                    }
                }

            //-------------------------------------------
            //網路上查找是否有此書籍
            //-------------------------------------------

                if(!$has_find){
                //---------------------------------------
                //查找開始
                //---------------------------------------

                    $_arrys_book_info=search_book_info_online($book_code);

                //---------------------------------------
                //結果彙整
                //---------------------------------------

                    if((trim($_arrys_book_info['book_name'][0])!=='')||(trim($_arrys_book_info['book_author'][0])!=='')||(trim($_arrys_book_info['book_publisher'][0])!=='')){
                        $has_find=true;
                        $find_area='online';
                        $arrys_find_book_info=$_arrys_book_info;
                        $arrys_find_book_info['book_code']=$book_code;
                    }
                }

        //-----------------------------------------------
        //檢核總結
        //-----------------------------------------------

            //有無找到
            $has_find=$has_find;

            //找到的區域
            $find_area=$find_area;

            //找到的書籍資訊
            $arrys_find_book_info=$arrys_find_book_info;

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
            $sys_ename=$FOLDER[count($FOLDER)-5];
            $mod_ename=$FOLDER[count($FOLDER)-4];
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
    <link rel="stylesheet" type="text/css" href="../../../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/form/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/date/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../../../css/def.css" media="all" />

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
                <td align="left" valign="middle" width="400px">
                    <!-- 教師中心路徑選單 開始 -->
                    <div id="teacher_center_path">
                        <table id="teacher_center_path_cont" border="0" width="100%">
                            <tr>
                                <td align="left" valign="middle" class="menu_dot">
                                    <span class="menu_cont">
                                        <img width="12" height="12" src="../../../../../../../img/icon/blue.jpg" border="0">
                                        <a href="../../../../../index.php">教師中心</a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="../../../<?php echo htmlspecialchars($sys_url);?>">
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
                    <div id="qform">
                        <span id="qform1"></span>
                    </div>
                    <!-- 查詢表單列 結束 -->
                </td>
            </tr>
            <tr>
                <td width="760px" colspan="2">
                    <!-- 資料內容 開始 -->
                    <?php
                        if($has_find){
                            page_ok($has_find,$find_area,$arrys_find_book_info);
                            die();
                        }else{
                            page_fail($book_code);
                            die();
                        }
                    ?>
                    <!-- 資料內容 結束 -->
                </td>
            </tr>
        </table>
    </div>
    <!-- 內容區塊 結束 -->

</div>
<!-- 容器區塊 結束 -->

<!-- 快速切換區塊 開始 -->
<?php //echo fast_area($rd=5);?>
<!-- 快速切換區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

</script>

<?php function page_ok($has_find,$find_area,$arrys_find_book_info) {?>
<?php
//-------------------------------------------------------
//page_ok 區塊 -- 開始
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

        //local
        global $psize;
        global $pinx;

        global $book_source;

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        global $arrys_login_info;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        if(isset($arrys_find_book_info['book_code'])){
            $book_isbn_10='';
            $book_isbn_13='';
            $_book_code=$arrys_find_book_info['book_code'];

            $ch_isbn_10=ch_isbn_10($_book_code, $convert=false);
            $ch_isbn_13=ch_isbn_13($_book_code, $convert=false);

            $_lv=0; //錯誤指標
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
                    die();
                break;
            }
        }
?>
<!-- 內容 開始 -->

<?php if($find_area==='online'):?>
    <form id='Form1' name='Form1' method='get' onsubmit="return false;">
        <table align="center" border="0" width="525px" class="table_style0" style="position:relative;top:30px;">
            <tr>
                <td align="left"><h1 class="fc_red0">查詢到的書籍：</h1></td>
            </tr>
            <tr class="fc_gray0">
                <td align="right" width="150px" height="35px" class="b_line gr_dashed">
                    <span class="fc_red1">*</span>
                    書名：
                </td>
                <td class="b_line gr_dashed">
                    <input type="text" id="book_name" name="book_name" value="<?php echo htmlspecialchars(strip_tags($arrys_find_book_info['book_name'][0]));?>" size="10" maxlength="50"
                    tabindex="1" class="form_text" style="width:150px">
                </td>
            </tr>
            <tr class="fc_gray0">
                <td align="right" width="150px" height="35px" class="gr_dashed">
                    <span class="fc_red1">*</span>
                    作者：
                </td>
                <td class="gr_dashed">
                    <input type="text" id="book_author" name="book_author" value="<?php echo htmlspecialchars(strip_tags($arrys_find_book_info['book_author'][0]));?>" size="10" maxlength="50"
                    tabindex="2" class="form_text" style="width:150px">
                </td>
            </tr>
            <tr class="fc_gray0">
                <td align="right" width="150px" height="35px" class="gr_dashed">
                    <span class="fc_red1">*</span>
                    出版社：
                </td>
                <td class="gr_dashed">
                    <input type="text" id="book_publisher" name="book_publisher" value="<?php echo htmlspecialchars(strip_tags($arrys_find_book_info['book_publisher'][0]));?>" size="10" maxlength="50"
                    tabindex="3" class="form_text" style="width:150px">
                </td>
            </tr>
            <tr class="fc_gray0">
                <td align="right" width="150px" height="35px" class="gr_dashed">
                    <span class="fc_red1">*</span>
                    圖書館條碼：
                </td>
                <td class="gr_dashed">
                    <input type="text" id="book_library_code" name="book_library_code" value="" size="10" maxlength="50"
                    tabindex="4" class="form_text" style="width:150px">
                </td>
            </tr>

            <tr>
                <td align="right" colspan="2">
                    <input type="hidden" id="psize" name="psize" value="<?php echo addslashes($psize);?>">
                    <input type="hidden" id="pinx" name="pinx" value="<?php echo addslashes($pinx);?>">
                    <input type="hidden" id="book_isbn_10" name="book_isbn_10" value="<?php echo trim($book_isbn_10);?>" size="20" maxlength="50" class="form_text" style="width:120px;" tabindex="5">
                    <input type="hidden" id="book_isbn_13" name="book_isbn_13" value="<?php echo trim($book_isbn_13);?>" size="20" maxlength="50" class="form_text" style="width:120px;" tabindex="6">
                    <input type="hidden" id="search_type" name="search_type" value="online" tabindex="7">
                    <input type="hidden" id="book_source" name="book_source" value="<?php echo addslashes($book_source);?>">

                    <input type="button" id="BtnS" name="BtnS" value="送出" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="8" onmouseover="this.style.cursor='pointer'">
                    <input type="button" id="BtnB" name="BtnB" value="返回" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="9" onmouseover="this.style.cursor='pointer'">
                </td>
            </tr>
        </table>
    </form>
<?php else:?>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <td align="left"><h1 class="fc_red0">查詢到的書籍：</h1></td>
        </tr>
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="350px" align="center" valign="top">
                <table id="mod_data_tbl" border="0" width="100%" cellpadding="5" cellspacing="0" style="margin-top:10px;" class="table_style1">
                    <tr align="center" valign="middle" class="bg_gray1 fc_white0">
                        <td width="250px">書籍名稱  </td>
                        <td width="250px">書籍作者  </td>
                        <td width="">書籍出版社     </td>
                        <td width="">功能           </td>
                    </tr>

                    <?php foreach($arrys_find_book_info as $arry_find_book_info):?>
                    <?php
                    //---------------------------------------------------
                    //接收欄位
                    //---------------------------------------------------

                        extract($arry_find_book_info, EXTR_PREFIX_ALL, "rs");

                    //---------------------------------------------------
                    //處理欄位
                    //---------------------------------------------------

                        $rs_book_name     =trim($rs_book_name);
                        $rs_book_author   =trim($rs_book_author);
                        $rs_book_publisher=trim($rs_book_publisher);
                        $rs_book_isbn_10  =trim($rs_book_isbn_10);
                        $rs_book_isbn_13  =trim($rs_book_isbn_13);

                    //---------------------------------------------------
                    //特殊處理
                    //---------------------------------------------------
                    ?>
                    <tr>
                        <td height="30px" align="center" valign="middle"><?php echo htmlspecialchars($rs_book_name);?>     </td>
                        <td height="30px" align="center" valign="middle"><?php echo htmlspecialchars($rs_book_author);?>   </td>
                        <td height="30px" align="center" valign="middle"><?php echo htmlspecialchars($rs_book_publisher);?></td>
                        <td height="30px" align="center" valign="middle">
                            <input type="button" value="用這筆資料" class="ibtn_gr9030" onclick="add('local','<?php echo addslashes($rs_book_name);?>','<?php echo addslashes($rs_book_author);?>','<?php echo addslashes($rs_book_publisher);?>','<?php echo addslashes($rs_book_isbn_10);?>','<?php echo addslashes($rs_book_isbn_13);?>')" onmouseover="this.style.cursor='pointer'">
                        </td>
                    </tr>
                    <?php endforeach ;?>
                </table>
                <table border="0" width="100%" style="position:relative;top:20px;">
                    <tr valign="middle" bgcolor="#e6faff">
                        <td align="left" height="50px">
                            <form id='Form1' name='Form1' method='get' onsubmit="return false;">
                                <span class="fc_red1" style="position:relative;;margin:0 10px;">以上書籍資料都不正確，我要自行新增</span><br/>
                                <span style="position:relative;margin-left:10px;">
                                    <img id="img_blue" width="12" height="12" src="../../../../../../../img/icon/blue.jpg" border="0">
                                </span>

                                <span style="position:relative;left:10px;">
                                書名：<input type="text" id="book_name" name="book_name" value="" size="20" maxlength="100" class="form_text" style="width:120px;" tabindex="1">
                                作者：<input type="text" id="book_author" name="book_author" value="" size="20" maxlength="50" class="form_text" style="width:120px;" tabindex="2"><br/>
                                <span style="position:relative;margin-left:14px;">
                                出版社：<input type="text" id="book_publisher" name="book_publisher" value="" size="20" maxlength="50" class="form_text" style="width:120px;" tabindex="3">
                                </span>
                                圖書館條碼：<input type="text" id="book_library_code" name="book_library_code" value="" size="20" maxlength="50" class="form_text" style="width:120px;" tabindex="4">

                                <input type="button" id="BtnS" name="BtnS" value="送出" class="ibtn_gr3020" style="margin:10px 0px;" tabindex="5" onmouseover="this.style.cursor='pointer'">
                                <input type="button" id="BtnB" name="BtnB" value="返回" class="ibtn_gr3020" style="margin:10px 0px;" tabindex="6" onmouseover="this.style.cursor='pointer'">

                                <input type="hidden" id="book_source" name="book_source" value="<?php echo addslashes($book_source);?>">
                                <input type="hidden" id="search_type" name="search_type" value="local" tabindex="7">
                                <input type="hidden" id="psize" name="psize" value="<?php echo addslashes($psize);?>">
                                <input type="hidden" id="pinx" name="pinx" value="<?php echo addslashes($pinx);?>">
                                <input type="hidden" id="book_isbn_10" name="book_isbn_10" value="<?php echo trim($rs_book_isbn_10);?>" size="20" maxlength="50" class="form_text" style="width:120px;" tabindex="8">
                                <input type="hidden" id="book_isbn_13" name="book_isbn_13" value="<?php echo trim($rs_book_isbn_13);?>" size="20" maxlength="50" class="form_text" style="width:120px;" tabindex="9">
                                </span>
                            </form>
                        </td>
                    </tr>
                </table>
            <!-- 內容 -->
            </td>
        </tr>
    </table>

<?php endif;?>

<!-- 內容 結束 -->

<?php
//-------------------------------------------------------
//資料繫結
//-------------------------------------------------------
?>
<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //變數
    var nl    ='\r\n';
    var pinx  =<?php echo addslashes($pinx);?>;     //單頁筆數
    var psize =<?php echo addslashes($psize);?>;    //目前分頁索引
    var book_source='<?php echo addslashes($book_source);?>';

    var oForm1=document.getElementById('Form1');    //表單
    var oBtnS =document.getElementById('BtnS');     //送出
    var oBtnB =document.getElementById('BtnB');     //返回

    var obook_name=document.getElementById('book_name');
    var obook_author=document.getElementById('book_author');
    var obook_publisher=document.getElementById('book_publisher');
    var obook_library_code=document.getElementById('book_library_code');

    $(function(){
        //啟動閃爍
        blink_fadeout();

        //駐點
        obook_name.focus();
    });

    function add(search_type,book_name,book_author,book_publisher,book_isbn_10,book_isbn_13){
    //新增
        var book_library_code=prompt("請輸入圖書館條碼!");

        if((book_library_code==null)||(trim(book_library_code)=="")){
            alert('請輸入圖書館條碼!');
            return false;
        }else{
            var ch_book_library_code=ISBN.parse(book_library_code);
            if(ch_book_library_code){
                alert('圖書館條碼若為ISBN格式，請將書籍建立為 [班級的書] !');
                return false;
            }
        }

        var url ='';
        var page=str_repeat('../',2)+'basic/book_library/addA.php';
        var arg ={
            'book_source':book_source,
            'psize':psize,
            'pinx' :pinx,
            'search_type':search_type,
            'book_name' :book_name,
            'book_author' :book_author,
            'book_publisher' :book_publisher,
            'book_isbn_10' :book_isbn_10,
            'book_isbn_13' :book_isbn_13,
            'book_library_code':book_library_code
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

    oBtnS.onclick=function(){
    //送出

        var arry_err=[];

        if(trim(obook_name.value)==''){
            arry_err.push('請輸入書名!');
        }
        if(trim(obook_author.value)==''){
            arry_err.push('請輸入作者!');
        }
        if(trim(obook_publisher.value)==''){
            arry_err.push('請輸入出版社!');
        }
        if(trim(obook_library_code.value)==''){
            arry_err.push('請輸入圖書館條碼!');
        }else{
            var ch_book_library_code=ISBN.parse(obook_library_code.value);
            if(ch_book_library_code){
                alert('圖書館條碼若為ISBN格式，請將書籍建立為 [班級的書] !');
                return false;
            }
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            //駐點
            obook_name.focus();
            return false;
        }else{
            this.disabled=true;
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
            oForm1.action='../../basic/book_library/addA.php'
            oForm1.submit();
            return true;
        }
    }

    oBtnB.onclick=function(){
    //返回

        var url ='';
        var page=str_repeat('../',2)+'basic/book_library/addF.php';
        var arg ={
            'book_source':book_source,
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

        go(url,'self');
    }

    function blink_fadeout(){
        $("#img_blue").fadeOut(1000,blink_fadein);
    }
    function blink_fadein(){
        $("#img_blue").fadeIn(1000,blink_fadeout);
    }

</script>

<?php
//-------------------------------------------------------
//page_ok 區塊 -- 結束
//-------------------------------------------------------
?>
<?php };?>


<?php function page_fail($_book_code) {?>
<?php
//-------------------------------------------------------
//page_fail 區塊 -- 開始
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

        //local
        global $psize;
        global $pinx;
        global $book_source;

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        global $arrys_login_info;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        $book_isbn_10='';
        $book_isbn_13='';
        $_book_code=$_book_code;

        $ch_isbn_10=ch_isbn_10($_book_code, $convert=false);
        $ch_isbn_13=ch_isbn_13($_book_code, $convert=false);

        $_lv=0; //錯誤指標
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
                die();
            break;
        }
?>
<!-- 內容 開始 -->
<form id='Form1' name='Form1' method='get' onsubmit="return false;">
    <table align="center" border="0" width="525px" class="table_style0" style="position:relative;top:30px;">
        <tr>
            <td align="left"><h1 class="fc_red0">查詢到的書籍：</h1></td>
        </tr>
        <tr class="fc_gray0">
            <td align="right" width="150px" height="35px" class="b_line gr_dashed">
                <span class="fc_red1">*</span>
                書名：
            </td>
            <td class="b_line gr_dashed">
                <input type="text" id="book_name" name="book_name" value="" size="10" maxlength="50"
                tabindex="1" class="form_text" style="width:150px">
            </td>
        </tr>
        <tr class="fc_gray0">
            <td align="right" width="150px" height="35px" class="gr_dashed">
                <span class="fc_red1">*</span>
                作者：
            </td>
            <td class="gr_dashed">
                <input type="text" id="book_author" name="book_author" value="" size="10" maxlength="50"
                tabindex="2" class="form_text" style="width:150px">
            </td>
        </tr>
        <tr class="fc_gray0">
            <td align="right" width="150px" height="35px" class="gr_dashed">
                <span class="fc_red1">*</span>
                出版社：
            </td>
            <td class="gr_dashed">
                <input type="text" id="book_publisher" name="book_publisher" value="" size="10" maxlength="50"
                tabindex="3" class="form_text" style="width:150px">
            </td>
        </tr>
        <tr class="fc_gray0">
            <td align="right" width="150px" height="35px" class="gr_dashed">
                <span class="fc_red1">*</span>
                圖書館條碼：
            </td>
            <td class="gr_dashed">
                <input type="text" id="book_library_code" name="book_library_code" value="" size="10" maxlength="50"
                tabindex="4" class="form_text" style="width:150px">
            </td>
        </tr>

        <tr>
            <td align="right" colspan="2">
                <input type="hidden" id="psize" name="psize" value="<?php echo addslashes($psize);?>">
                <input type="hidden" id="pinx" name="pinx" value="<?php echo addslashes($pinx);?>">
                <input type="hidden" id="book_isbn_10" name="book_isbn_10" value="<?php echo trim($book_isbn_10);?>" size="20" maxlength="50" class="form_text" style="width:120px;" tabindex="5">
                <input type="hidden" id="book_isbn_13" name="book_isbn_13" value="<?php echo trim($book_isbn_13);?>" size="20" maxlength="50" class="form_text" style="width:120px;" tabindex="6">
                <input type="hidden" id="search_type" name="search_type" value="online" tabindex="7">
                <input type="hidden" id="book_source" name="book_source" value="<?php echo addslashes($book_source);?>">

                <input type="button" id="BtnS" name="BtnS" value="送出" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="8" onmouseover="this.style.cursor='pointer'">
                <input type="button" id="BtnB" name="BtnB" value="返回" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="9" onmouseover="this.style.cursor='pointer'">
            </td>
        </tr>
    </table>
</form>
<!-- 內容 結束 -->

<?php
//-------------------------------------------------------
//資料繫結
//-------------------------------------------------------
?>
<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //變數
    var nl    ='\r\n';
    var pinx  =<?php echo addslashes($pinx);?>;     //單頁筆數
    var psize =<?php echo addslashes($psize);?>;    //目前分頁索引
    var book_source='<?php echo addslashes($book_source);?>';

    var oForm1=document.getElementById('Form1');    //表單
    var oBtnS =document.getElementById('BtnS');     //送出
    var oBtnB =document.getElementById('BtnB');     //返回

    var obook_name=document.getElementById('book_name');
    var obook_author=document.getElementById('book_author');
    var obook_publisher=document.getElementById('book_publisher');
    var obook_library_code=document.getElementById('book_library_code');

    $(function(){
        //駐點
        obook_name.focus();
    });

    oBtnS.onclick=function(){
    //送出

        var arry_err=[];

        if(trim(obook_name.value)==''){
            arry_err.push('請輸入書名!');
        }
        if(trim(obook_author.value)==''){
            arry_err.push('請輸入作者!');
        }
        if(trim(obook_publisher.value)==''){
            arry_err.push('請輸入出版社!');
        }
        if(trim(obook_library_code.value)==''){
            arry_err.push('請輸入圖書館條碼!');
        }else{
            var ch_book_library_code=ISBN.parse(obook_library_code.value);
            if(ch_book_library_code){
                alert('圖書館條碼若為ISBN格式，請將書籍建立為 [班級的書] !');
                return false;
            }
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            //駐點
            obook_name.focus();
            return false;
        }else{
            this.disabled=true;
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
            oForm1.action='../../basic/book_library/addA.php'
            oForm1.submit();
            return true;
        }
    }

    oBtnB.onclick=function(){
    //返回

        var url ='';
        var page=str_repeat('../',2)+'basic/book_library/addF.php';
        var arg ={
            'book_source':book_source,
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

        go(url,'self');
    }

</script>

<?php
//-------------------------------------------------------
//page_fail 區塊 -- 結束
//-------------------------------------------------------
?>
<?php };?>





<?php function no_book_code($_book_code) {?>
<?php

echo "44";
//-------------------------------------------------------
//no_book_code 區塊 -- 開始
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

        //local
        global $psize;
        global $pinx;
        global $book_source;

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        global $arrys_login_info;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

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
    <?php echo bing_analysis($allow=false);?>
    <?php echo robots($allow=false);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/form/code.js"></script>
    <script type="text/javascript" src="../../../../../../../lib/js/date/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../../../css/def.css" media="all" />

    <style>
        /* 容器微調 */
        #container, #content, #teacher_datalist_tbl{
            width:760px;
        }
    </style>
</Head>

<Body>
<!-- 內容 開始 -->
<form id='Form1' name='Form1' method='get' onsubmit="return false;">
    <table align="center" border="0" width="525px" class="table_style0" style="position:relative;top:30px;">
        <tr>
            <td align="left"><h1 class="fc_red0">書籍：</h1></td>
        </tr>
        <tr class="fc_gray0">
            <td align="right" width="150px" height="35px" class="b_line gr_dashed">
                <span class="fc_red1">*</span>
                書名：
            </td>
            <td class="b_line gr_dashed">
                <input type="text" id="book_name" name="book_name" value="" size="10" maxlength="50"
                tabindex="1" class="form_text" style="width:150px">
            </td>
        </tr>
        <tr class="fc_gray0">
            <td align="right" width="150px" height="35px" class="gr_dashed">
                <span class="fc_red1">*</span>
                作者：
            </td>
            <td class="gr_dashed">
                <input type="text" id="book_author" name="book_author" value="" size="10" maxlength="50"
                tabindex="2" class="form_text" style="width:150px">
            </td>
        </tr>
        <tr class="fc_gray0">
            <td align="right" width="150px" height="35px" class="gr_dashed">
                <span class="fc_red1">*</span>
                出版社：
            </td>
            <td class="gr_dashed">
                <input type="text" id="book_publisher" name="book_publisher" value="" size="10" maxlength="50"
                tabindex="3" class="form_text" style="width:150px">
            </td>
        </tr>
        <tr class="fc_gray0">
            <td align="right" width="150px" height="35px" class="gr_dashed">
                <span class="fc_red1">*</span>
                圖書館條碼：
            </td>
            <td class="gr_dashed">
                <input type="text" id="book_library_code" name="book_library_code" value="" size="10" maxlength="50"
                tabindex="4" class="form_text" style="width:150px">
            </td>
        </tr>

        <tr>
            <td align="right" colspan="2">
                <input type="hidden" id="psize" name="psize" value="<?php echo addslashes($psize);?>">
                <input type="hidden" id="pinx" name="pinx" value="<?php echo addslashes($pinx);?>">
                <input type="hidden" id="book_isbn_10" name="book_isbn_10" value="<?php echo trim($book_isbn_10);?>" size="20" maxlength="50" class="form_text" style="width:120px;" tabindex="5">
                <input type="hidden" id="book_isbn_13" name="book_isbn_13" value="<?php echo trim($book_isbn_13);?>" size="20" maxlength="50" class="form_text" style="width:120px;" tabindex="6">
                <input type="hidden" id="search_type" name="search_type" value="online" tabindex="7">
                <input type="hidden" id="book_source" name="book_source" value="<?php echo addslashes($book_source);?>">

                <input type="button" id="BtnS" name="BtnS" value="送出" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="8" onmouseover="this.style.cursor='pointer'">
                <input type="button" id="BtnB" name="BtnB" value="返回" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="9" onmouseover="this.style.cursor='pointer'">
            </td>
        </tr>
    </table>
</form>
<!-- 內容 結束 -->
</Body>
<?php
//-------------------------------------------------------
//資料繫結
//-------------------------------------------------------
?>
<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //變數
    var nl    ='\r\n';
    var pinx  =<?php echo addslashes($pinx);?>;     //單頁筆數
    var psize =<?php echo addslashes($psize);?>;    //目前分頁索引
    var book_source='<?php echo addslashes($book_source);?>';

    var oForm1=document.getElementById('Form1');    //表單
    var oBtnS =document.getElementById('BtnS');     //送出
    var oBtnB =document.getElementById('BtnB');     //返回

    var obook_name=document.getElementById('book_name');
    var obook_author=document.getElementById('book_author');
    var obook_publisher=document.getElementById('book_publisher');
    var obook_library_code=document.getElementById('book_library_code');

    $(function(){
        //駐點
        obook_name.focus();
    });

    oBtnS.onclick=function(){
    //送出

        var arry_err=[];

        if(trim(obook_name.value)==''){
            arry_err.push('請輸入書名!');
        }
        if(trim(obook_author.value)==''){
            arry_err.push('請輸入作者!');
        }
        if(trim(obook_publisher.value)==''){
            arry_err.push('請輸入出版社!');
        }
        if(trim(obook_library_code.value)==''){
            arry_err.push('請輸入圖書館條碼!');
        }else{
            var ch_book_library_code=ISBN.parse(obook_library_code.value);
            if(ch_book_library_code){
                alert('圖書館條碼若為ISBN格式，請將書籍建立為 [班級的書] !');
                return false;
            }
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            //駐點
            obook_name.focus();
            return false;
        }else{
            this.disabled=true;
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
            oForm1.action='../../basic/book_library/addA.php'
 
            oForm1.submit();
            return true;
        }
    }

    oBtnB.onclick=function(){
    //返回
        console.log('ww');
        var url ='';
        var page=str_repeat('../',2)+'basic/book_library/addF.php';
        var arg ={
            'book_source':book_source,
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

        go(url,'self');
    }

</script>

<?php
//-------------------------------------------------------
//no_book_code 區塊 -- 結束
//-------------------------------------------------------
?>
<?php };?>