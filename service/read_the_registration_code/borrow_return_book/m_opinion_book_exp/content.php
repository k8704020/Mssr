<?php
//-------------------------------------------------------
//閱讀登記條碼版
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
                    APP_ROOT.'service/read_the_registration_code/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['service']['read_the_registration_code']){
            $url=str_repeat("../",5).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(!login_check(array('t'))){
            die();
        }

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        //初始化，承接變數
        $_sess_t=$_SESSION['t'];
        foreach($_sess_t as $field_name=>$field_value){
            $$field_name=$field_value;
        }

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        if((!isset($_SESSION['_read_the_registration_code']['_login']))||(empty($_SESSION['_read_the_registration_code']['_login']))){
            $page=str_repeat("../",0)."login/loginF.php";

            $jscript_back="
                <script>
                    parent.location.href='{$page}';
                </script>
            ";

            die($jscript_back);
        }else{
            //借書人資訊
            $_user_id    =(int)$_SESSION['_read_the_registration_code']['_login']['_user_id'];
            $_user_name  =trim($_SESSION['_read_the_registration_code']['_login']['_user_name']);
            $_user_number=(int)$_SESSION['_read_the_registration_code']['_login']['_user_number'];
        }

        //是否第一次借書
        $first_borrow='yes';
        if((isset($_SESSION['_read_the_registration_code']['_login']['first_borrow']))&&($_SESSION['_read_the_registration_code']['_login']['first_borrow']==='no')){
            $first_borrow='no';
        }

        $opinion_success_flag='no';
        if((isset($_SESSION['_read_the_registration_code']['_login']['opinion_success_flag']))&&($_SESSION['_read_the_registration_code']['_login']['opinion_success_flag']==='yes')){
            $opinion_success_flag='yes';
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //初始化, isbn碼輸入提醒
        $_isbn_code_remind='yes';

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
        //學生陣列
        //---------------------------------------------------

            $users=arrys_users($conn_user,addslashes(trim($_sess_t['arrys_class_code'][0]['class_code'])),$date=date("Y-m-d"),$arry_conn_user);

        //---------------------------------------------------
        //提取圖書館的書籍資訊
        //---------------------------------------------------

            $arrys_book_library_isbn_10=array();
            $arrys_book_library_isbn_13=array();
            $arrys_book_library=arrys_book_library($conn_mssr,mysql_prep(trim($_sess_t['school_code'])),$arry_conn_mssr);
            if(!empty($arrys_book_library)){
                foreach($arrys_book_library as $inx=>$arry_book_library){
                    $book_isbn_10=trim($arry_book_library['book_isbn_10']);
                    $book_isbn_13=trim($arry_book_library['book_isbn_13']);
                    $arrys_book_library_isbn_10[]=$book_isbn_10;
                    $arrys_book_library_isbn_13[]=$book_isbn_13;
                }
            }

        //---------------------------------------------------
        //提取所有使用者的借書證資訊
        //---------------------------------------------------

            $get_user_library_card_info=get_user_library_card_info($conn_user,$users,$array_filter=array('card_number'),$arry_conn_user);

        //---------------------------------------------------
        //isbn碼輸入提醒查詢
        //---------------------------------------------------

            $isbn_code_remind=isbn_code_remind($db_type='mysql',$arry_conn_mssr,(int)$_sess_t['uid']);

            if(!$isbn_code_remind){
                $_isbn_code_remind='no';
            }

        //---------------------------------------------------
        //借閱的書本資訊
        //---------------------------------------------------

            $query_sql="
                SELECT * FROM (
                    SELECT
                        'mssr_book_class' AS `book_type`,
                        `mssr_book_borrow_tmp`.`keyin_cdate`,
                        `mssr_book_borrow_tmp`.`book_sid`,

                        `mssr_book_class`.`book_isbn_10`,
                        `mssr_book_class`.`book_isbn_13`,
                        '' AS `book_library_code`,
                        `mssr_book_class`.`book_name`,
                        `mssr_book_class`.`book_no`,
                        `mssr_book_class`.`book_donor`

                    FROM `mssr_book_borrow_tmp`
                        INNER JOIN `mssr_book_class` ON
                        `mssr_book_borrow_tmp`.`book_sid`=`mssr_book_class`.`book_sid`
                    WHERE 1=1
                        AND `mssr_book_borrow_tmp`.`user_id`='{$_user_id}'

                        UNION

                    SELECT
                        'mssr_book_library' AS `book_type`,
                        `mssr_book_borrow_tmp`.`keyin_cdate`,
                        `mssr_book_borrow_tmp`.`book_sid`,

                        `mssr_book_library`.`book_isbn_10`,
                        `mssr_book_library`.`book_isbn_13`,
                        `mssr_book_library`.`book_library_code`,
                        `mssr_book_library`.`book_name`,
                        `mssr_book_library`.`book_no`,
                        '' AS `book_donor`

                    FROM `mssr_book_borrow_tmp`
                        INNER JOIN `mssr_book_library` ON
                        `mssr_book_borrow_tmp`.`book_sid`=`mssr_book_library`.`book_sid`
                    WHERE 1=1
                        AND `mssr_book_borrow_tmp`.`user_id`='{$_user_id}'
                ) AS `sqry`

                WHERE 1=1
                ORDER BY `sqry`.`keyin_cdate` DESC
            ";
            //echo $query_sql;

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
        $title="明日星球,閱讀登記條碼版";

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
        global $arry_conn_mssr;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $arrys_result;
        global $conn_mssr;

        //借閱人資訊
        global  $_user_id;
        global  $_user_name;
        global  $_user_number;

        //isbn碼輸入提醒
        global $_isbn_code_remind;

        //是否第一次借書
        global $first_borrow;

        //提取所有使用者的借書證資訊
        global $get_user_library_card_info;

        //提取圖書館的書籍資訊
        global $arrys_book_library_isbn_10;
        global $arrys_book_library_isbn_13;

        global $opinion_success_flag;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=3;  //欄位個數
        $btn_nos=0;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        //最後借閱時間
        $_borrow_cdate=date("Y-m-d",strtotime($arrys_result[0]['keyin_cdate']));
        if($_borrow_cdate==='1970-01-01'){
            $_borrow_cdate='無';
        }
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
    <script type="text/javascript" src="../../../../lib/js/array/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/table/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" />
</Head>

<Body>

    <!-- 資料列表 開始 -->
        <!-- 借閱人資訊  開始 -->
        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table_style2" style="position:relative;top:20px;"/>
            <tr align="center" valign="middle" class="bg_gray1 fc_white0">
                <td height="30px" colspan="3">
                    <span class="wp010-08 fsize_17">登記人資訊</span>
                </td>
            </tr>
            <tr align="left" valign="middle">
                <td width="170px" height="50px">
                    <span style="position:relative;left:10px;" class="fc_gray0 wp010-08 fsize_17">姓名：</span>
                    <span style="position:relative;left:5px;" class="fc_gray0 wp010-08 fsize_17">
                        <?php echo htmlspecialchars($_user_name);?>
                    </span>
                </td>
                <td width="150px" height="50px">
                    <span style="position:relative;left:10px;" class="fc_gray0 wp010-08 fsize_17">座號：</span>
                    <span style="position:relative;left:5px;" class="fc_gray0 wp010-08 fsize_17">
                        <?php echo $_user_number;?>號
                    </span>
                </td>
                <td height="50px">
                    <form id='Form1' name='Form1' action='' method='post' onsubmit="return false;">
                        <span class="fc_blue0 wp010-08 fsize_24">我要登記</span>
                        <span style="position:relative;top:-2px;">
                            <img id="img_blue" width="12" height="12" src="../../../../img/icon/blue.jpg" border="0">
                        </span>

                        <span style="position:relative;left:0px;" class="fc_gray0 wp010-08 fsize_24">書號：</span>
                        <span style="position:relative;left:0px;" class="fc_gray0">
                            <input type="text" id="book_code" name="book_code" value="" size="20" maxlength="20" class="form_text" style="width:120px;" tabindex="1">
                            <input id="BtnA" type="button" value="送出" class="ibtn_gr3020" tabindex="2" onclick="add();">
                        </span>
                    </form>
                </td>
            </tr>
        </table>
        <!-- 借閱人資訊  結束 -->

        <?php if(trim($opinion_success_flag)==='yes'):?>
            <table id="tbl_opinion_success" cellpadding="0" cellspacing="0" border="0" width="100%" class="table_style2" style="position:relative;top:40px;"/>
                <tr align="center" valign="middle">
                    <td width="170px" height="100px">
                        <span class='fc_red0 wp010-08 fsize_24'>登記成功！</span>
                    </td>
                </tr>
            </table>
        <?php endif;?>

        <!-- 借閱中書籍  開始 -->
        <table id="tbl_borrow_book" width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="position:relative;top:30px;display:none;"/>
            <tr>
                <td align="left"><h1 class="fc_red0 wp010-08 fsize_19">借閱中書籍：</h1></td>
            </tr>
            <tr>
                <!-- 在此設定寬高 -->
                <td width="100%" height="250px" align="center" valign="top">
                <!-- 內容 -->
                    <table id="mod_data_tbl" border="0" width="100%" cellpadding="5" cellspacing="0" class="table_style2">
                        <tr height="30px" align="center" valign="middle" class="bg_gray1 fc_white0">
                            <td class="wp010-08 fsize_17">書名    </td>
                            <td class="wp010-08 fsize_17">書號    </td>
                            <td class="wp010-08 fsize_17">序號    </td>
                            <td class="wp010-08 fsize_17">捐贈者  </td>
                            <td class="wp010-08 fsize_17">借閱日期</td>
                        </tr>
                        <?php foreach($arrys_result as $inx=>$arry_result) :?>
                        <?php
                        //---------------------------------------------------
                        //接收欄位
                        //---------------------------------------------------
                        //keyin_cdate       最後借閱日期
                        //book_sid          書本識別碼
                        //book_isbn_10      書本isbn10瑪
                        //book_isbn_13      書本isbn13瑪
                        //book_name         書本名稱
                        //book_no           書本序號
                        //book_donor        書籍捐贈者

                            extract($arry_result, EXTR_PREFIX_ALL, "rs");

                        //---------------------------------------------------
                        //處理欄位
                        //---------------------------------------------------

                            //book_name           書籍名稱
                            if(mb_strlen($rs_book_name)>20){
                                $rs_book_name=mb_substr($rs_book_name,0,20)."..";
                            }

                            //book_no       書本序號
                            $rs_book_no=(int)$rs_book_no;

                            //keyin_cdate   最後借閱日期
                            $rs_keyin_cdate=date("Y-m-d",strtotime($rs_keyin_cdate));

                            $rs_book_code="";
                            switch(trim($rs_book_type)){
                                case 'mssr_book_class':
                                    if((isset($rs_book_isbn_10))&&(trim($rs_book_isbn_10)!=='')){
                                        $rs_book_code='ISBN:'.$rs_book_isbn_10;
                                    }
                                    if((isset($rs_book_isbn_13))&&(trim($rs_book_isbn_13)!=='')){
                                        $rs_book_code='ISBN:'.$rs_book_isbn_13;
                                    }
                                break;
                                case 'mssr_book_library':
                                    $rs_book_code='圖書館:'.$rs_book_library_code;
                                break;
                            }
                        ?>
                        <tr>
                            <td class="wp010-08 fsize_17" width="200px" align="center" valign="middle"><?php echo htmlspecialchars($rs_book_name);?>    </td>
                            <td class="wp010-08 fsize_17" width="250px" align="center" valign="middle">
                                <?php echo htmlspecialchars($rs_book_code);?>
                            </td>
                            <td class="wp010-08 fsize_17" width="75px" align="center" valign="middle"><?php echo $rs_book_no;?>                         </td>
                            <td class="wp010-08 fsize_17" width="85px" align="center" valign="middle"><?php echo htmlspecialchars($rs_book_donor);?>    </td>
                            <td class="wp010-08 fsize_17" width="" align="center" valign="middle"><?php echo htmlspecialchars($rs_keyin_cdate);?>       </td>
                        </tr>
                        <?php endforeach ;?>
                    </table>

                    <table border="0" width="100%">
                        <tr valign="middle">
                            <td align="left">
                                <!-- 分頁列 -->
                                <span id="page" style="position:relative;top:10px;"></span>
                            </td>
                        </tr>
                    </table>
                <!-- 內容 -->
                </td>
            </tr>
        </table>
        <!-- 借閱中書籍  結束 -->

        <!-- 貼紙編號  開始 -->
        <table id="tbl_sel_book_no" cellpadding="0" cellspacing="0" border="0" width="100%" class="table_style2" style="position:relative;top:50px;display:none;" onmouseover="this.style.cursor='default';"/>
            <tr align="center" valign="middle" class="bg_gray1 fc_white0">
                <td height="30px" colspan="2">
                    <span class="wp010-08 fsize_17">貼紙編號</span>
                </td>
            </tr>
            <tr valign="top">
                <td width="48%" height="245px">
                    <table cellpadding="0" cellspacing="0" border="0" width="100%"/>
                        <tr align="left">
                            <td>
                                <span style="position:relative;left:50px;">
                                    <h1 class="wp010-08">請按照書本背後的貼紙<br/><br/>選擇號碼唷!</h1>
                                </span>
                            </td>
                        </tr>
                        <tr align="left">
                            <td>
                                <span style="position:relative;left:50px;top:30px;">
                                    <h1 class="wp010-08">貼紙編號：</h1>
                                    <input type="text" id="book_no" name="book_no" class="form_text" value="" tabindex='1' maxlength="3" style="width:150px;">
                                    <input class="ibtn_gr6030" type="button" value="送出" onmouseover="this.style.cursor='pointer'"
                                    onclick="_sel_book_no(document.getElementById('book_no').value,'multi');void(0);">
                                    <input class="ibtn_gr6030" type="button" value="清空" onmouseover="this.style.cursor='pointer'"
                                    onclick="document.getElementById('book_no').value='';">
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td height="245px">
                    <?php for($i=0;$i<=15;$i++):?>
                    <table cellpadding="0" cellspacing="0" border="0" width="71px" height="71px" class="bg_gray1 tbl_book_no" style="margin:5px 10px;float:left;"
                    onclick="_set_book_no(<?php echo $i;?>);" onmouseover="this.style.cursor='pointer';"/>
                        <tr align="center" valign="middel">
                            <td>
                                <h1 class="fc_white0">
                                    <?php echo $i;?>
                                </h1>
                            </td>
                        </tr>
                    </table>
                    <?php endfor;?>
                </td>
            </tr>
        </table>
        <!-- 貼紙編號  結束 -->

        <!-- 書籍類別  開始 -->
        <table id="tbl_sel_book_category" cellpadding="0" cellspacing="0" border="0" width="100%" class="table_style2" style="position:relative;display:none;" onmouseover="this.style.cursor='default';"/>
            <tr>
                <td>
                    <span style="position:relative;left:20px;">
                        <h1 class="wp010-08">請仔細選擇書籍類別唷!</h1>
                    </span>
                </td>
                <td>
                    <table cellpadding="0" cellspacing="0" border="0" width="150px" height="150px"
                    class="bg_gray1"
                    style="margin:5px 10px;float:left;"
                    onclick="_sel_book_category('class');" onmouseover="this.style.cursor='pointer';"/>
                        <tr align="center" valign="middel">
                            <td>
                                <h1 class="wp010-08 fc_white0">
                                    班上的書
                                </h1>
                            </td>
                        </tr>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="0" width="150px" height="150px"
                    class="bg_gray1"
                    style="margin:5px 10px;float:left;"
                    onclick="_sel_book_category('library');" onmouseover="this.style.cursor='pointer';"/>
                        <tr align="center" valign="middel">
                            <td>
                                <h1 class="wp010-08 fc_white0">
                                    圖書館的書
                                </h1>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!-- 書籍類別  結束 -->

        <!-- 圖書館選書  開始 -->
        <table id="tbl_library_book" width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="position:relative;display:none;"/>
            <tr>
                <!-- 在此設定寬高 -->
                <td width="100%" height="250px" align="center" valign="top">
                    <!-- 內容 -->
                    <table border="0" width="100%" cellpadding="5" cellspacing="0" style="" class="table_style2">
                        <tr>
                            <td id="td_library_book" height="470px" align="left" valign="top"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!-- 圖書館選書  結束 -->
    <!-- 資料列表 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;

    //物件
    var oForm1=document.getElementById('Form1');
    var oBtnA=document.getElementById('BtnA');

    var otbl_borrow_book=document.getElementById('tbl_borrow_book');
    var otbl_sel_book_no=document.getElementById('tbl_sel_book_no');
    var otbl_sel_book_category=document.getElementById('tbl_sel_book_category');
    var $tbl_book_nos   =$('.tbl_book_no');

    var obook_code=document.getElementById('book_code');
    var obook_no=document.getElementById('book_no');

    //值
    var isbn_code_remind='<?php echo $_isbn_code_remind;?>';
    var first_borrow='<?php echo $first_borrow;?>'

    //ajax設定
    var $_url           ="add/book_ch_code/addA.php";
    var $_type          ="POST";
    var $_datatype      ="json";

    //借書證資訊設定
    var arry_card_number=[];
    <?php foreach($get_user_library_card_info as $inx=>$val):?>
    <?php
        $card_number=trim($val['card_number']);
    ?>
        var card_number='<?php echo $card_number;?>';
        arry_card_number.push(card_number);
    <?php endforeach;?>

    //圖書館資訊設定
    var arrys_book_library_isbn_10=[];
    var arrys_book_library_isbn_13=[];

    //10碼匯入
    <?php foreach($arrys_book_library_isbn_10 as $inx=>$val):?>
    <?php
        $book_isbn_10=trim($val);
    ?>
        var book_isbn_10='<?php echo $book_isbn_10;?>';
        arrys_book_library_isbn_10.push(book_isbn_10);
    <?php endforeach;?>

    //13碼匯入
    <?php foreach($arrys_book_library_isbn_13 as $inx=>$val):?>
    <?php
        $book_isbn_13=trim($val);
    ?>
        var book_isbn_13='<?php echo $book_isbn_13;?>';
        arrys_book_library_isbn_13.push(book_isbn_13);
    <?php endforeach;?>

    $(function(){

        if(first_borrow==='yes'){
        //第一次借書
            //載入書號輸入表單
            $.blockUI({
                message:$('#Form1'),
                css:{
                    width:'60%',
                    left:'155px'
                },
                overlayCSS:{
                    backgroundColor:'#000',
                    opacity:0.6,
                    cursor:'default'
                }
            });
        }

        //套表格列奇偶色
        table_hover(tbl_id='mod_data_tbl',c_odd='#fff',c_even='#fff',c_on='#e6faff');

        //啟動閃爍
        blink_fadeout();

        //駐點
        obook_code.focus();

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
            'page_name' :'content.php',
            'page_args' :{}
        }
        var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
    });

    obook_code.onblur=function(){
    //失去駐點
        setTimeout(function(){
            obook_code.focus();
        });
    };

    function _ajax($_url,$_type,$_datatype,$_code,$_code_type){
    //ajax設置
        $.ajax({
        //參數設置
            async      :true,      //瀏覽器在發送的時候，停止任何其他的呼叫。
            cache      :false,     //快取的回應。
            global     :true,      //是否使用全局 AJAX 事件。
            timeout    :10000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",

            url        :$_url,     //請求的頁面
            type       :$_type,    //GET or POST
            datatype   :$_datatype,
            data       :{
                book_code_type:encodeURI(trim($_code_type)),
                book_code     :encodeURI(trim($_code))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
                $.blockUI({
                    message:'<h2>借書中...</h2>',
                    overlayCSS:{
                        backgroundColor:'#000',
                        opacity:0.9,
                        cursor:'default'
                    },
                    timeout: 10000
                });
            },
            success     :function(respones){
            //成功處理
                respones=jQuery.parseJSON(respones);
                var _book_name=trim(respones.book_name);             //書本名稱
                var _book_numrow=trim(respones.book_numrow);         //書本數量
                var _book_code_type=trim(respones.book_code_type);   //書本種類
                var _book_code=trim(respones.book_code);             //書本編號
                var _status=trim(respones.status);                   //處理狀態

                switch(_book_code_type){
                    case 'library':
                    //圖書館書籍處理
                        if(_status!=='true'){
                        //圖書館無相關書籍
                            alert('書本不存在, 請重新輸入!');
                            location.reload();
                            if(first_borrow==='yes'){
                            //第一次借書
                                //載入書號輸入表單
                                $.blockUI({
                                    message:$('#Form1'),
                                    css:{
                                        width:'60%',
                                        left:'155px'
                                    },
                                    overlayCSS:{
                                        backgroundColor:'#000',
                                        opacity:0.6,
                                        cursor:'default'
                                    }
                                });
                            }else{
                                $.unblockUI();
                            }
                            obook_code.value='';
                            obook_code.focus();
                            return false;
                        }else{
                        //圖書館有相關書籍
                            if(_book_numrow>1){
                                $.blockUI({
                                    message:$('#tbl_library_book'),
                                    css:{
                                        width:'87%',
                                        left:'50px',
                                        top:'30px'
                                    },
                                    overlayCSS:{
                                        backgroundColor:'#000',
                                        opacity:0.6,
                                        cursor:'default'
                                    }
                                });
                                arry_book_names=_book_name.split(",");
                                for(var key in arry_book_names){
                                    var book_name=trim(arry_book_names[key]);
                                    if(book_name!==''){
                                        var _html='';
                                           _html+='<table align="left" cellpadding="0" cellspacing="0" border="0" style="width:160px;">';
                                           _html+='<tr><td align="center"><img book_name="'+book_name+'" book_code="'+_book_code+'" onclick="muilt_library_process(this);" src="../../img/book.jpg" width="50" height="50" border="0" alt="book" onmouseover="this.style.cursor='+"'"+'pointer'+"'"+';"></td></tr>';
                                           _html+='<tr><td class="wp010-08 fsize_17" align="center">';
                                           _html+=book_name;
                                           _html+='</td></tr>';
                                           _html+='</table>';
                                        $('#td_library_book').append(_html);
                                    }
                                }
                            }else{
                                _book_name=_book_name.replace(/,$/,"");
                                var url ='';
                                var page=str_repeat('../',0)+'add/book_library_process/addA.php';
                                var arg ={
                                    'psize':psize,
                                    'pinx':pinx,
                                    'book_code':_book_code,
                                    'book_name':_book_name
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
                        }
                    break;

                    case 'class':
                        if(_status!=='true'){
                        //班級無相關書籍
                            alert('書本不存在, 請重新輸入!');
                            location.reload();
                            if(first_borrow==='yes'){
                            //第一次借書
                                //載入書號輸入表單
                                $.blockUI({
                                    message:$('#Form1'),
                                    css:{
                                        width:'60%',
                                        left:'155px'
                                    },
                                    overlayCSS:{
                                        backgroundColor:'#000',
                                        opacity:0.6,
                                        cursor:'default'
                                    }
                                });
                            }else{
                                $.unblockUI();
                            }
                            obook_code.value='';
                            obook_code.focus();
                            return false;
                        }else{
                        //班級有相關書籍
                            $.unblockUI();
                            if(_book_numrow>1){
                                $tbl_book_nos.hide();
                                for(var i=0;i<10;i++){
                                    $tbl_book_nos.eq(i).show();
                                }
                                if(first_borrow==='yes'){
                                    $.blockUI({
                                        message:$(otbl_sel_book_no),
                                        css:{
                                            top: '12%',
                                            left: '-3px',
                                            width:'100%'
                                        },
                                        overlayCSS:{
                                            backgroundColor:'#000',
                                            opacity:0.6,
                                            cursor:'default'
                                        }
                                    });
                                }else{
                                    //切換視窗
                                    otbl_borrow_book.style.display='none';
                                    otbl_sel_book_no.style.display='';
                                }

                            }else{
                                _sel_book_no(1,'single');
                            }
                        }
                    break;
                }
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                if(ajaxoptions==='timeout'){
                    $.blockUI({
                        message:'<h2 class="fc_red1">連線失敗，請檢查網路是否斷線 !</h2>',
                        overlayCSS:{
                            backgroundColor:'#000',
                            opacity:0.9,
                            cursor:'default'
                        },
                        timeout: 2000
                    });
                    if(first_borrow==='yes'){
                    //第一次借書
                        //載入書號輸入表單
                        setTimeout(
                            function blockUI(){
                                $.blockUI({
                                    message:$('#Form1'),
                                    css:{
                                        width:'60%',
                                        left:'155px'
                                    },
                                    overlayCSS:{
                                        backgroundColor:'#000',
                                        opacity:0.6,
                                        cursor:'default'
                                    }
                                })
                            },
                            2000
                        );
                    }
                    obook_code.value='';
                    obook_code.focus();
                    return false;
                }else{
                    $.blockUI({
                        message:'<h2 class="fc_red1">連線失敗，請檢查網路是否斷線 !</h2>',
                        overlayCSS:{
                            backgroundColor:'#000',
                            opacity:0.9,
                            cursor:'default'
                        },
                        timeout: 2000
                    });
                    if(first_borrow==='yes'){
                    //第一次借書
                        //載入書號輸入表單
                        setTimeout(
                            function blockUI(){
                                $.blockUI({
                                    message:$('#Form1'),
                                    css:{
                                        width:'60%',
                                        left:'155px'
                                    },
                                    overlayCSS:{
                                        backgroundColor:'#000',
                                        opacity:0.6,
                                        cursor:'default'
                                    }
                                })
                            },
                            2000
                        );
                    }
                    obook_code.value='';
                    obook_code.focus();
                    return false;
                }
            },
            complete    :function(){//傳送後處理
            }
        });
    }

    function muilt_library_process(obj){
    //複選圖書館的書
        var book_code=obj.getAttribute('book_code');
        var book_name=obj.getAttribute('book_name');
        var url ='';
        var page=str_repeat('../',0)+'add/book_library_process/addA.php';
        var arg ={
            'psize':psize,
            'pinx':pinx,
            'book_code':book_code,
            'book_name':book_name
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

    $(obook_code).keypress(function(e){
        if(e.which===13){
        //送出
            var book_code=trim(obook_code.value);
            var ch_isbn=ISBN.parse(book_code);
            var ch_issn  =parseInt(book_code.substring(0,3));

            //切換視窗
            otbl_borrow_book.style.display='none';
            otbl_sel_book_no.style.display='none';

            if(trim(book_code)===''){
                alert('請輸入書號!');
                obook_code.focus();
                return false;
            }else{
                if(in_array(trim(book_code),arry_card_number)){
                    parent.location.href='login/loginF.php';
                    return false;
                }
                if(ch_isbn ||(ch_issn===977)){
                //是isbn碼
                    if(ch_isbn){
                        var ch_isbn_10=ch_isbn.isIsbn10();
                        var ch_isbn_13=ch_isbn.isIsbn13();

                        if((ch_isbn_10)||(ch_isbn_13)){
                        //判定isbn條碼，判斷提醒
                            if(isbn_code_remind==='yes'){
                                if((in_array(trim(book_code),arrys_book_library_isbn_10))||(in_array(trim(book_code),arrys_book_library_isbn_13))){
                                    $.blockUI({
                                        message:$(otbl_sel_book_category),
                                        css:{
                                            top: '25%',
                                            left: '-3px',
                                            width:'100%'
                                        },
                                        overlayCSS:{
                                            backgroundColor:'#000',
                                            opacity:0.6,
                                            cursor:'default'
                                        }
                                    });
                                }else{
                                    _ajax($_url,$_type,$_datatype,book_code,'class');
                                }
                            }else{
                                _ajax($_url,$_type,$_datatype,book_code,'class');
                            }
                        }else{
                        //判定圖書館條碼，直接送出檢查
                            _ajax($_url,$_type,$_datatype,book_code,'library');
                        }
                    }else{
                        if(isbn_code_remind==='yes'){
                            if((in_array(trim(book_code),arrys_book_library_isbn_10))||(in_array(trim(book_code),arrys_book_library_isbn_13))){
                                $.blockUI({
                                    message:$(otbl_sel_book_category),
                                    css:{
                                        top: '25%',
                                        left: '-3px',
                                        width:'100%'
                                    },
                                    overlayCSS:{
                                        backgroundColor:'#000',
                                        opacity:0.6,
                                        cursor:'default'
                                    }
                                });
                            }else{
                                _ajax($_url,$_type,$_datatype,book_code,'class');
                            }
                        }else{
                            _ajax($_url,$_type,$_datatype,book_code,'class');
                        }
                    }
                }else{
                //判定圖書館條碼，直接送出檢查
                    _ajax($_url,$_type,$_datatype,book_code,'library');
                }
            }
        }
    });

    function add(){
    //送出
        var book_code=trim(obook_code.value);
        var ch_isbn=ISBN.parse(book_code);
        var ch_issn  =parseInt(book_code.substring(0,3));

        //切換視窗
        otbl_borrow_book.style.display='none';
        otbl_sel_book_no.style.display='none';

        if(trim(book_code)===''){
            alert('請輸入書號!');
            obook_code.focus();
            return false;
        }else{
            if(in_array(trim(book_code),arry_card_number)){
                parent.location.href='login/loginF.php';
                return false;
            }
            if(ch_isbn ||(ch_issn===977)){
            //是isbn碼
                if(ch_isbn){
                    var ch_isbn_10=ch_isbn.isIsbn10();
                    var ch_isbn_13=ch_isbn.isIsbn13();

                    if((ch_isbn_10)||(ch_isbn_13)){
                    //判定isbn條碼，判斷提醒
                        if(isbn_code_remind==='yes'){
                            if((in_array(trim(book_code),arrys_book_library_isbn_10))||(in_array(trim(book_code),arrys_book_library_isbn_13))){
                                $.blockUI({
                                    message:$(otbl_sel_book_category),
                                    css:{
                                        top: '25%',
                                        left: '-3px',
                                        width:'100%'
                                    },
                                    overlayCSS:{
                                        backgroundColor:'#000',
                                        opacity:0.6,
                                        cursor:'default'
                                    }
                                });
                            }else{
                                _ajax($_url,$_type,$_datatype,book_code,'class');
                            }
                        }else{
                            _ajax($_url,$_type,$_datatype,book_code,'class');
                        }
                    }else{
                    //判定圖書館條碼，直接送出檢查
                        _ajax($_url,$_type,$_datatype,book_code,'library');
                    }
                }else{
                    if(isbn_code_remind==='yes'){
                        if((in_array(trim(book_code),arrys_book_library_isbn_10))||(in_array(trim(book_code),arrys_book_library_isbn_13))){
                            $.blockUI({
                                message:$(otbl_sel_book_category),
                                css:{
                                    top: '25%',
                                    left: '-3px',
                                    width:'100%'
                                },
                                overlayCSS:{
                                    backgroundColor:'#000',
                                    opacity:0.6,
                                    cursor:'default'
                                }
                            });
                        }else{
                            _ajax($_url,$_type,$_datatype,book_code,'class');
                        }
                    }else{
                        _ajax($_url,$_type,$_datatype,book_code,'class');
                    }
                }
            }else{
            //判定圖書館條碼，直接送出檢查
                _ajax($_url,$_type,$_datatype,book_code,'library');
            }
        }
    }

    function _sel_book_category(category){

        var book_code=trim(obook_code.value);
        var ch_isbn=ISBN.parse(book_code);

        //切換視窗
        otbl_borrow_book.style.display='none';
        otbl_sel_book_no.style.display='none';

        if(trim(book_code)===''){
            alert('請輸入書號!');
            obook_code.focus();
            return false;
        }else{
            if(first_borrow==='yes'){
                if(ch_isbn){
                //是isbn碼
                    var ch_isbn_10=ch_isbn.isIsbn10();
                    var ch_isbn_13=ch_isbn.isIsbn13();

                    if((ch_isbn_10)||(ch_isbn_13)){
                    //判定isbn條碼，判斷提醒
                        if(category==='class'){
                            _ajax($_url,$_type,$_datatype,book_code,'class');
                        }else{
                            alert('請刷入書本前面的圖書館條碼!');
                            //載入書號輸入表單
                            $.blockUI({
                                message:$('#Form1'),
                                css:{
                                    width:'60%',
                                    left:'155px'
                                },
                                overlayCSS:{
                                    backgroundColor:'#000',
                                    opacity:0.6,
                                    cursor:'default'
                                }
                            });
                            //初始化, 書單輸入框
                            obook_code.value='';
                            obook_code.focus();
                        }
                    }else{
                        if(category==='library'){
                            _ajax($_url,$_type,$_datatype,book_code,'library');
                        }else{
                            alert('請刷入書本的ISBN條碼!');
                            //載入書號輸入表單
                            $.blockUI({
                                message:$('#Form1'),
                                css:{
                                    width:'60%',
                                    left:'155px'
                                },
                                overlayCSS:{
                                    backgroundColor:'#000',
                                    opacity:0.6,
                                    cursor:'default'
                                }
                            });
                            //初始化, 書單輸入框
                            obook_code.value='';
                            obook_code.focus();
                        }
                    }
                }else{
                    if(category==='library'){
                        _ajax($_url,$_type,$_datatype,book_code,'library');
                    }else{
                        alert('請刷入書本的ISBN條碼!');
                        //載入書號輸入表單
                        $.blockUI({
                            message:$('#Form1'),
                            css:{
                                width:'60%',
                                left:'155px'
                            },
                            overlayCSS:{
                                backgroundColor:'#000',
                                opacity:0.6,
                                cursor:'default'
                            }
                        });
                        //初始化, 書單輸入框
                        obook_code.value='';
                        obook_code.focus();
                    }
                }
            }else{
                if(ch_isbn){
                //是isbn碼
                    var ch_isbn_10=ch_isbn.isIsbn10();
                    var ch_isbn_13=ch_isbn.isIsbn13();

                    if((ch_isbn_10)||(ch_isbn_13)){
                    //判定isbn條碼，判斷提醒
                        if(category==='class'){
                            _ajax($_url,$_type,$_datatype,book_code,'class');
                        }else{
                            alert('請刷入書本前面的圖書館條碼!');
                            $.unblockUI();
                            //初始化, 書單輸入框
                            obook_code.value='';
                            obook_code.focus();
                        }
                    }else{
                        if(category==='library'){
                            _ajax($_url,$_type,$_datatype,book_code,'library');
                        }else{
                            alert('請刷入書本的ISBN條碼!');
                            $.unblockUI();
                            //初始化, 書單輸入框
                            obook_code.value='';
                            obook_code.focus();
                        }
                    }
                }else{
                    if(category==='library'){
                        _ajax($_url,$_type,$_datatype,book_code,'library');
                    }else{
                        alert('請刷入書本的ISBN條碼!');
                        $.unblockUI();
                        //初始化, 書單輸入框
                        obook_code.value='';
                        obook_code.focus();
                    }
                }
            }
        }
    }

    function _set_book_no(book_no){
        var _book_no=parseInt(book_no);
        if(obook_no.value.length<3){
            obook_no.value+=_book_no;
        }
    }

    function _sel_book_no(book_no,sel_type){
        var _book_code=trim(obook_code.value);
        var _book_no=parseInt(book_no);

        if((isNaN(_book_no))||(_book_no===0)){
            alert('請輸入正確的貼紙編號!');
            obook_no.focus();
            return false;
        }

        var url ='';
        var page=str_repeat('../',0)+'add/book_class_process/addA.php';
        var arg ={
            'psize':psize,
            'pinx':pinx,
            'book_code':_book_code,
            'book_no':_book_no
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

        if(sel_type==='multi'){
            go(url,'self');
        }else{
            go(url,'self');
        }
    }

    function blink_fadeout(){
        $("#img_blue").fadeOut(1000,blink_fadein);
    }
    function blink_fadein(){
        $("#img_blue").fadeIn(1000,blink_fadeout);
    }

    oBtnA.onmouseover= function(){
    //動作
        this.style.cursor='pointer';
    }
    oBtnA.onmouseout= function(){
        this.style.cursor='none';
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

        global $arrys_result;
        global $conn_mssr;

        //借閱人資訊
        global  $_user_id;
        global  $_user_name;
        global  $_user_number;

        //isbn碼輸入提醒
        global $_isbn_code_remind;

        //是否第一次借書
        global $first_borrow;

        //提取所有使用者的借書證資訊
        global $get_user_library_card_info;

        //提取圖書館的書籍資訊
        global $arrys_book_library_isbn_10;
        global $arrys_book_library_isbn_13;

        global $opinion_success_flag;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0;  //欄位個數
        $btn_nos=0;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        //最後借閱時間
        $_borrow_cdate=date("Y-m-d",strtotime($arrys_result[0]['keyin_cdate']));
        if($_borrow_cdate==='1970-01-01'){
            $_borrow_cdate='無';
        }
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
    <script type="text/javascript" src="../../../../lib/js/array/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" />
</Head>

<Body>

    <!-- 資料列表 開始 -->
        <!-- 借閱人資訊  開始 -->
        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table_style2" style="position:relative;top:20px;"/>
            <tr align="center" valign="middle" class="bg_gray1 fc_white0">
                <td height="30px" colspan="3">
                    <span class="wp010-08 fsize_17">登記人資訊</span>
                </td>
            </tr>
            <tr align="left" valign="middle">
                <td width="170px" height="50px">
                    <span style="position:relative;left:10px;" class="fc_gray0 wp010-08 fsize_17">姓名：</span>
                    <span style="position:relative;left:5px;" class="fc_gray0 wp010-08 fsize_17">
                        <?php echo htmlspecialchars($_user_name);?>
                    </span>
                </td>
                <td width="150px" height="50px">
                    <span style="position:relative;left:10px;" class="fc_gray0 wp010-08 fsize_17">座號：</span>
                    <span style="position:relative;left:5px;" class="fc_gray0 wp010-08 fsize_17">
                        <?php echo $_user_number;?>號
                    </span>
                </td>
                <td height="50px">
                    <form id='Form1' name='Form1' action='' method='post' onsubmit="return false;">
                        <span class="fc_blue0 wp010-08 fsize_24">我要登記</span>
                        <span style="position:relative;top:-2px;">
                            <img id="img_blue" width="12" height="12" src="../../../../img/icon/blue.jpg" border="0">
                        </span>

                        <span style="position:relative;left:0px;" class="fc_gray0 wp010-08 fsize_24">書號：</span>
                        <span style="position:relative;left:0px;" class="fc_gray0">
                            <input type="text" id="book_code" name="book_code" value="" size="20" maxlength="20" class="form_text" style="width:120px;" tabindex="1">
                            <input id="BtnA" type="button" value="送出" class="ibtn_gr3020" tabindex="2" onclick="add();">
                        </span>
                    </form>
                </td>
            </tr>
        </table>
        <!-- 借閱人資訊  結束 -->

        <?php if(trim($opinion_success_flag)==='yes'):?>
            <table id="tbl_opinion_success" cellpadding="0" cellspacing="0" border="0" width="100%" class="table_style2" style="position:relative;top:40px;"/>
                <tr align="center" valign="middle">
                    <td width="170px" height="100px">
                        <span class='fc_red0 wp010-08 fsize_24'>登記成功！</span>
                    </td>
                </tr>
            </table>
        <?php endif;?>

        <!-- 借閱中書籍  開始 -->
        <table id="tbl_borrow_book" width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="display:none;"/>
            <tr>
                <!-- 在此設定寬高 -->
                <td width="100%" height="250px" align="center" valign="top">
                    <!-- 內容 -->
                    <table border="0" width="100%" cellpadding="5" cellspacing="0" style="position:relative;top:55px;" class="table_style2">
                        <tr align="center" valign="middle" class="bg_gray1">
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="250px" align="center" valign="middle">
                                <img src="../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
                                <span class="wp010-08 fsize_17">目前無借閱中的書籍!</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!-- 借閱中書籍  結束 -->

        <!-- 貼紙編號  開始 -->
        <table id="tbl_sel_book_no" cellpadding="0" cellspacing="0" border="0" width="100%" class="table_style2" style="position:relative;top:50px;display:none;" onmouseover="this.style.cursor='default';"/>
            <tr align="center" valign="middle" class="bg_gray1 fc_white0">
                <td height="30px" colspan="2">
                    <span class="wp010-08 fsize_17">貼紙編號</span>
                </td>
            </tr>
            <tr valign="top">
                <td width="48%" height="245px">
                    <table cellpadding="0" cellspacing="0" border="0" width="100%"/>
                        <tr align="left">
                            <td>
                                <span style="position:relative;left:50px;">
                                    <h1 class="wp010-08">請按照書本背後的貼紙<br/><br/>選擇號碼唷!</h1>
                                </span>
                            </td>
                        </tr>
                        <tr align="left">
                            <td>
                                <span style="position:relative;left:50px;top:30px;">
                                    <h1 class="wp010-08">貼紙編號：</h1>
                                    <input type="text" id="book_no" name="book_no" class="form_text" value="" tabindex='1' maxlength="3" style="width:150px;">
                                    <input class="ibtn_gr6030" type="button" value="送出" onmouseover="this.style.cursor='pointer'"
                                    onclick="_sel_book_no(document.getElementById('book_no').value,'multi');void(0);">
                                    <input class="ibtn_gr6030" type="button" value="清空" onmouseover="this.style.cursor='pointer'"
                                    onclick="document.getElementById('book_no').value='';">
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td height="245px">
                    <?php for($i=0;$i<=15;$i++):?>
                    <table cellpadding="0" cellspacing="0" border="0" width="71px" height="71px" class="bg_gray1 tbl_book_no" style="margin:5px 10px;float:left;"
                    onclick="_set_book_no(<?php echo $i;?>);" onmouseover="this.style.cursor='pointer';"/>
                        <tr align="center" valign="middel">
                            <td>
                                <h1 class="fc_white0">
                                    <?php echo $i;?>
                                </h1>
                            </td>
                        </tr>
                    </table>
                    <?php endfor;?>
                </td>
            </tr>
        </table>
        <!-- 貼紙編號  結束 -->

        <!-- 書籍類別  開始 -->
        <table id="tbl_sel_book_category" cellpadding="0" cellspacing="0" border="0" width="100%" class="table_style2" style="position:relative;display:none;" onmouseover="this.style.cursor='default';"/>
            <tr>
                <td>
                    <span style="position:relative;left:20px;">
                        <h1 class="wp010-08">請仔細選擇書籍類別唷!</h1>
                    </span>
                </td>
                <td>
                    <table cellpadding="0" cellspacing="0" border="0" width="150px" height="150px"
                    class="bg_gray1"
                    style="margin:5px 10px;float:left;"
                    onclick="_sel_book_category('class');" onmouseover="this.style.cursor='pointer';"/>
                        <tr align="center" valign="middel">
                            <td>
                                <h1 class="wp010-08 fc_white0">
                                    班上的書
                                </h1>
                            </td>
                        </tr>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="0" width="150px" height="150px"
                    class="bg_gray1"
                    style="margin:5px 10px;float:left;"
                    onclick="_sel_book_category('library');" onmouseover="this.style.cursor='pointer';"/>
                        <tr align="center" valign="middel">
                            <td>
                                <h1 class="wp010-08 fc_white0">
                                    圖書館的書
                                </h1>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!-- 書籍類別  結束 -->

        <!-- 圖書館選書  開始 -->
        <table id="tbl_library_book" width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="position:relative;display:none;"/>
            <tr>
                <!-- 在此設定寬高 -->
                <td width="100%" height="250px" align="center" valign="top">
                    <!-- 內容 -->
                    <table border="0" width="100%" cellpadding="5" cellspacing="0" style="" class="table_style2">
                        <tr>
                            <td id="td_library_book" height="470px" align="left" valign="top"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!-- 圖書館選書  結束 -->
    <!-- 資料列表 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;

    //物件
    var oForm1=document.getElementById('Form1');
    var oBtnA=document.getElementById('BtnA');

    var otbl_borrow_book=document.getElementById('tbl_borrow_book');
    var otbl_sel_book_no=document.getElementById('tbl_sel_book_no');
    var otbl_sel_book_category=document.getElementById('tbl_sel_book_category');
    var $tbl_book_nos   =$('.tbl_book_no');

    var obook_code=document.getElementById('book_code');
    var obook_no=document.getElementById('book_no');

    //值
    var isbn_code_remind='<?php echo $_isbn_code_remind;?>';
    var first_borrow='<?php echo $first_borrow;?>'

    //ajax設定
    var $_url           ="add/book_ch_code/addA.php";
    var $_type          ="POST";
    var $_datatype      ="json";

    //借書證資訊設定
    var arry_card_number=[];
    <?php foreach($get_user_library_card_info as $inx=>$val):?>
    <?php
        $card_number=trim($val['card_number']);
    ?>
        var card_number='<?php echo $card_number;?>';
        arry_card_number.push(card_number);
    <?php endforeach;?>

    //圖書館資訊設定
    var arrys_book_library_isbn_10=[];
    var arrys_book_library_isbn_13=[];

    //10碼匯入
    <?php foreach($arrys_book_library_isbn_10 as $inx=>$val):?>
    <?php
        $book_isbn_10=trim($val);
    ?>
        var book_isbn_10='<?php echo $book_isbn_10;?>';
        arrys_book_library_isbn_10.push(book_isbn_10);
    <?php endforeach;?>

    //13碼匯入
    <?php foreach($arrys_book_library_isbn_13 as $inx=>$val):?>
    <?php
        $book_isbn_13=trim($val);
    ?>
        var book_isbn_13='<?php echo $book_isbn_13;?>';
        arrys_book_library_isbn_13.push(book_isbn_13);
    <?php endforeach;?>

    $(function(){

        if(first_borrow==='yes'){
        //第一次借書
            //載入書號輸入表單
            $.blockUI({
                message:$('#Form1'),
                css:{
                    width:'60%',
                    left:'155px'
                },
                overlayCSS:{
                    backgroundColor:'#000',
                    opacity:0.6,
                    cursor:'default'
                }
            });
        }

        //啟動閃爍
        blink_fadeout();

        //駐點
        obook_code.focus();
    });

    obook_code.onblur=function(){
    //失去駐點
        setTimeout(function(){
            obook_code.focus();
        });
    };

    function _ajax($_url,$_type,$_datatype,$_code,$_code_type){
    //ajax設置
        $.ajax({
        //參數設置
            async      :true,      //瀏覽器在發送的時候，停止任何其他的呼叫。
            cache      :false,     //快取的回應。
            global     :true,      //是否使用全局 AJAX 事件。
            timeout    :10000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",

            url        :$_url,     //請求的頁面
            type       :$_type,    //GET or POST
            datatype   :$_datatype,
            data       :{
                book_code_type:encodeURI(trim($_code_type)),
                book_code     :encodeURI(trim($_code))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
                $.blockUI({
                    message:'<h2>借書中...</h2>',
                    overlayCSS:{
                        backgroundColor:'#000',
                        opacity:0.9,
                        cursor:'default'
                    },
                    timeout: 10000
                });
            },
            success     :function(respones){
            //成功處理
//console.log(jQuery.parseJSON(respones));
//return false;
                respones=jQuery.parseJSON(respones);
                var _book_name=trim(respones.book_name);             //書本名稱
                var _book_numrow=trim(respones.book_numrow);         //書本數量
                var _book_code_type=trim(respones.book_code_type);   //書本種類
                var _book_code=trim(respones.book_code);             //書本編號
                var _status=trim(respones.status);                   //處理狀態

                switch(_book_code_type){
                    case 'library':
                    //圖書館書籍處理
                        if(_status!=='true'){
                        //圖書館無相關書籍
                            alert('書本不存在, 請重新輸入!');
                            location.reload();
                            if(first_borrow==='yes'){
                            //第一次借書
                                //載入書號輸入表單
                                $.blockUI({
                                    message:$('#Form1'),
                                    css:{
                                        width:'60%',
                                        left:'155px'
                                    },
                                    overlayCSS:{
                                        backgroundColor:'#000',
                                        opacity:0.6,
                                        cursor:'default'
                                    }
                                });
                            }else{
                                $.unblockUI();
                            }
                            obook_code.value='';
                            obook_code.focus();
                            return false;
                        }else{
                        //圖書館有相關書籍
                            if(_book_numrow>1){
                                $.blockUI({
                                    message:$('#tbl_library_book'),
                                    css:{
                                        width:'87%',
                                        left:'50px',
                                        top:'30px'
                                    },
                                    overlayCSS:{
                                        backgroundColor:'#000',
                                        opacity:0.6,
                                        cursor:'default'
                                    }
                                });
                                arry_book_names=_book_name.split(",");
                                for(var key in arry_book_names){
                                    var book_name=trim(arry_book_names[key]);
                                    if(book_name!==''){
                                        var _html='';
                                           _html+='<table align="left" cellpadding="0" cellspacing="0" border="0" style="width:160px;">';
                                           _html+='<tr><td align="center"><img book_name="'+book_name+'" book_code="'+_book_code+'" onclick="muilt_library_process(this);" src="../../img/book.jpg" width="50" height="50" border="0" alt="book" onmouseover="this.style.cursor='+"'"+'pointer'+"'"+';"></td></tr>';
                                           _html+='<tr><td class="wp010-08 fsize_17" align="center">';
                                           _html+=book_name;
                                           _html+='</td></tr>';
                                           _html+='</table>';
                                        $('#td_library_book').append(_html);
                                    }
                                }
                            }else{
                                _book_name=_book_name.replace(/,$/,"");
                                var url ='';
                                var page=str_repeat('../',0)+'add/book_library_process/addA.php';
                                var arg ={
                                    'psize':psize,
                                    'pinx':pinx,
                                    'book_code':_book_code,
                                    'book_name':_book_name
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
                        }
                    break;

                    case 'class':
                        if(_status!=='true'){
                        //班級無相關書籍
                            alert('書本不存在, 請重新輸入!');
                            location.reload();
                            if(first_borrow==='yes'){
                            //第一次借書
                                //載入書號輸入表單
                                $.blockUI({
                                    message:$('#Form1'),
                                    css:{
                                        width:'60%',
                                        left:'155px'
                                    },
                                    overlayCSS:{
                                        backgroundColor:'#000',
                                        opacity:0.6,
                                        cursor:'default'
                                    }
                                });
                            }else{
                                $.unblockUI();
                            }
                            obook_code.value='';
                            obook_code.focus();
                            return false;
                        }else{
                        //班級有相關書籍
                            $.unblockUI();
                            if(_book_numrow>1){
                                $tbl_book_nos.hide();
                                for(var i=0;i<10;i++){
                                    $tbl_book_nos.eq(i).show();
                                }
                                if(first_borrow==='yes'){
                                    $.blockUI({
                                        message:$(otbl_sel_book_no),
                                        css:{
                                            top: '12%',
                                            left: '-3px',
                                            width:'100%'
                                        },
                                        overlayCSS:{
                                            backgroundColor:'#000',
                                            opacity:0.6,
                                            cursor:'default'
                                        }
                                    });
                                }else{
                                    //切換視窗
                                    otbl_borrow_book.style.display='none';
                                    otbl_sel_book_no.style.display='';
                                }

                            }else{
                                _sel_book_no(1,'single');
                            }
                        }
                    break;
                }
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                if(ajaxoptions==='timeout'){
                    $.blockUI({
                        message:'<h2 class="fc_red1">連線失敗，請檢查網路是否斷線 !</h2>',
                        overlayCSS:{
                            backgroundColor:'#000',
                            opacity:0.9,
                            cursor:'default'
                        },
                        timeout: 2000
                    });
                    if(first_borrow==='yes'){
                    //第一次借書
                        //載入書號輸入表單
                        setTimeout(
                            function blockUI(){
                                $.blockUI({
                                    message:$('#Form1'),
                                    css:{
                                        width:'60%',
                                        left:'155px'
                                    },
                                    overlayCSS:{
                                        backgroundColor:'#000',
                                        opacity:0.6,
                                        cursor:'default'
                                    }
                                })
                            },
                            2000
                        );
                    }
                    obook_code.value='';
                    obook_code.focus();
                    return false;
                }else{
                    $.blockUI({
                        message:'<h2 class="fc_red1">連線失敗，請檢查網路是否斷線 !</h2>',
                        overlayCSS:{
                            backgroundColor:'#000',
                            opacity:0.9,
                            cursor:'default'
                        },
                        timeout: 2000
                    });
                    if(first_borrow==='yes'){
                    //第一次借書
                        //載入書號輸入表單
                        setTimeout(
                            function blockUI(){
                                $.blockUI({
                                    message:$('#Form1'),
                                    css:{
                                        width:'60%',
                                        left:'155px'
                                    },
                                    overlayCSS:{
                                        backgroundColor:'#000',
                                        opacity:0.6,
                                        cursor:'default'
                                    }
                                })
                            },
                            2000
                        );
                    }
                    obook_code.value='';
                    obook_code.focus();
                    return false;
                }
            },
            complete    :function(){//傳送後處理
            }
        });
    }

    function muilt_library_process(obj){
    //複選圖書館的書
        var book_code=obj.getAttribute('book_code');
        var book_name=obj.getAttribute('book_name');
        var url ='';
        var page=str_repeat('../',0)+'add/book_library_process/addA.php';
        var arg ={
            'psize':psize,
            'pinx':pinx,
            'book_code':book_code,
            'book_name':book_name
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

    $(obook_code).keypress(function(e){
        if(e.which===13){
        //送出
            var book_code=trim(obook_code.value);
            var ch_isbn=ISBN.parse(book_code);

            //切換視窗
            otbl_borrow_book.style.display='none';
            otbl_sel_book_no.style.display='none';

            if(trim(book_code)===''){
                alert('請輸入書號!');
                obook_code.focus();
                return false;
            }else{
                if(in_array(trim(book_code),arry_card_number)){
                    parent.location.href='login/loginF.php';
                    return false;
                }
                if(ch_isbn){
                //是isbn碼
                    var ch_isbn_10=ch_isbn.isIsbn10();
                    var ch_isbn_13=ch_isbn.isIsbn13();

                    if((ch_isbn_10)||(ch_isbn_13)){
                    //判定isbn條碼，判斷提醒
                        if(isbn_code_remind==='yes'){
                            if((in_array(trim(book_code),arrys_book_library_isbn_10))||(in_array(trim(book_code),arrys_book_library_isbn_13))){
                                $.blockUI({
                                    message:$(otbl_sel_book_category),
                                    css:{
                                        top: '25%',
                                        left: '-3px',
                                        width:'100%'
                                    },
                                    overlayCSS:{
                                        backgroundColor:'#000',
                                        opacity:0.6,
                                        cursor:'default'
                                    }
                                });
                            }else{
                                _ajax($_url,$_type,$_datatype,book_code,'class');
                            }
                            //if(confirm('你確定書本前面沒有圖書館條碼?')){
                            //    _ajax($_url,$_type,$_datatype,book_code,'class');
                            //}
                            //else{
                            //    alert('請刷入書本前面的圖書館條碼!');
                            //    return false;
                            //}
                        }else{
                            _ajax($_url,$_type,$_datatype,book_code,'class');
                        }
                    }else{
                    //判定圖書館條碼，直接送出檢查
                        _ajax($_url,$_type,$_datatype,book_code,'library');
                    }
                }else{
                //判定圖書館條碼，直接送出檢查
                    _ajax($_url,$_type,$_datatype,book_code,'library');
                }
            }
        }
    });

    function add(){
    //送出
        var book_code=trim(obook_code.value);
        var ch_isbn=ISBN.parse(book_code);

        //切換視窗
        otbl_borrow_book.style.display='none';
        otbl_sel_book_no.style.display='none';

        if(trim(book_code)===''){
            alert('請輸入書號!');
            obook_code.focus();
            return false;
        }else{
            if(in_array(trim(book_code),arry_card_number)){
                parent.location.href='login/loginF.php';
                return false;
            }
            if(ch_isbn){
            //是isbn碼
                var ch_isbn_10=ch_isbn.isIsbn10();
                var ch_isbn_13=ch_isbn.isIsbn13();

                if((ch_isbn_10)||(ch_isbn_13)){
                //判定isbn條碼，判斷提醒
                    if(isbn_code_remind==='yes'){
                        if((in_array(trim(book_code),arrys_book_library_isbn_10))||(in_array(trim(book_code),arrys_book_library_isbn_13))){
                            $.blockUI({
                                message:$(otbl_sel_book_category),
                                css:{
                                    top: '25%',
                                    left: '-3px',
                                    width:'100%'
                                },
                                overlayCSS:{
                                    backgroundColor:'#000',
                                    opacity:0.6,
                                    cursor:'default'
                                }
                            });
                        }else{
                            _ajax($_url,$_type,$_datatype,book_code,'class');
                        }
                        //if(confirm('你確定書本前面沒有圖書館條碼?')){
                        //    _ajax($_url,$_type,$_datatype,book_code,'class');
                        //}
                        //else{
                        //    alert('請刷入書本前面的圖書館條碼!');
                        //    return false;
                        //}
                    }else{
                        _ajax($_url,$_type,$_datatype,book_code,'class');
                    }
                }else{
                //判定圖書館條碼，直接送出檢查
                    _ajax($_url,$_type,$_datatype,book_code,'library');
                }
            }else{
            //判定圖書館條碼，直接送出檢查
                _ajax($_url,$_type,$_datatype,book_code,'library');
            }
        }
    }

    function _sel_book_category(category){

        var book_code=trim(obook_code.value);
        var ch_isbn=ISBN.parse(book_code);

        //切換視窗
        otbl_borrow_book.style.display='none';
        otbl_sel_book_no.style.display='none';

        if(trim(book_code)===''){
            alert('請輸入書號!');
            obook_code.focus();
            return false;
        }else{
            if(first_borrow==='yes'){
                if(ch_isbn){
                //是isbn碼
                    var ch_isbn_10=ch_isbn.isIsbn10();
                    var ch_isbn_13=ch_isbn.isIsbn13();

                    if((ch_isbn_10)||(ch_isbn_13)){
                    //判定isbn條碼，判斷提醒
                        if(category==='class'){
                            _ajax($_url,$_type,$_datatype,book_code,'class');
                        }else{
                            alert('請刷入書本前面的圖書館條碼!');
                            //載入書號輸入表單
                            $.blockUI({
                                message:$('#Form1'),
                                css:{
                                    width:'60%',
                                    left:'155px'
                                },
                                overlayCSS:{
                                    backgroundColor:'#000',
                                    opacity:0.6,
                                    cursor:'default'
                                }
                            });
                            //初始化, 書單輸入框
                            obook_code.value='';
                            obook_code.focus();
                        }
                    }else{
                        if(category==='library'){
                            _ajax($_url,$_type,$_datatype,book_code,'library');
                        }else{
                            alert('請刷入書本的ISBN條碼!');
                            //載入書號輸入表單
                            $.blockUI({
                                message:$('#Form1'),
                                css:{
                                    width:'60%',
                                    left:'155px'
                                },
                                overlayCSS:{
                                    backgroundColor:'#000',
                                    opacity:0.6,
                                    cursor:'default'
                                }
                            });
                            //初始化, 書單輸入框
                            obook_code.value='';
                            obook_code.focus();
                        }
                    }
                }else{
                    if(category==='library'){
                        _ajax($_url,$_type,$_datatype,book_code,'library');
                    }else{
                        alert('請刷入書本的ISBN條碼!');
                        //載入書號輸入表單
                        $.blockUI({
                            message:$('#Form1'),
                            css:{
                                width:'60%',
                                left:'155px'
                            },
                            overlayCSS:{
                                backgroundColor:'#000',
                                opacity:0.6,
                                cursor:'default'
                            }
                        });
                        //初始化, 書單輸入框
                        obook_code.value='';
                        obook_code.focus();
                    }
                }
            }else{
                if(ch_isbn){
                //是isbn碼
                    var ch_isbn_10=ch_isbn.isIsbn10();
                    var ch_isbn_13=ch_isbn.isIsbn13();

                    if((ch_isbn_10)||(ch_isbn_13)){
                    //判定isbn條碼，判斷提醒
                        if(category==='class'){
                            _ajax($_url,$_type,$_datatype,book_code,'class');
                        }else{
                            alert('請刷入書本前面的圖書館條碼!');
                            $.unblockUI();
                            //初始化, 書單輸入框
                            obook_code.value='';
                            obook_code.focus();
                        }
                    }else{
                        if(category==='library'){
                            _ajax($_url,$_type,$_datatype,book_code,'library');
                        }else{
                            alert('請刷入書本的ISBN條碼!');
                            $.unblockUI();
                            //初始化, 書單輸入框
                            obook_code.value='';
                            obook_code.focus();
                        }
                    }
                }else{
                    if(category==='library'){
                        _ajax($_url,$_type,$_datatype,book_code,'library');
                    }else{
                        alert('請刷入書本的ISBN條碼!');
                        $.unblockUI();
                        //初始化, 書單輸入框
                        obook_code.value='';
                        obook_code.focus();
                    }
                }
            }
        }
    }

    function _set_book_no(book_no){
        var _book_no=parseInt(book_no);
        if(obook_no.value.length<3){
            obook_no.value+=_book_no;
        }
    }

    function _sel_book_no(book_no,sel_type){
        var _book_code=trim(obook_code.value);
        var _book_no=parseInt(book_no);

        if((isNaN(_book_no))||(_book_no===0)){
            alert('請輸入正確的貼紙編號!');
            obook_no.focus();
            return false;
        }

        var url ='';
        var page=str_repeat('../',0)+'add/book_class_process/addA.php';
        var arg ={
            'psize':psize,
            'pinx':pinx,
            'book_code':_book_code,
            'book_no':_book_no
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

        if(sel_type==='multi'){
            go(url,'self');
        }else{
            go(url,'self');
        }
    }

    function blink_fadeout(){
        $("#img_blue").fadeOut(1000,blink_fadein);
    }
    function blink_fadein(){
        $("#img_blue").fadeIn(1000,blink_fadeout);
    }

    oBtnA.onmouseover= function(){
    //動作
        this.style.cursor='pointer';
    }
    oBtnA.onmouseout= function(){
        this.style.cursor='none';
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