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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_class_read_info');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //SESSION
        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用

        if(isset($_SESSION['m_class_read_info']['filter'])){
            $filter=$_SESSION['m_class_read_info']['filter'];
            if(isset($_SESSION['m_class_read_info']['class_code'])&&(trim($_SESSION['m_class_read_info']['class_code'])!=='')){
                $q_class_code=mysql_prep(trim($_SESSION['m_class_read_info']['class_code']));
            }
        }
        if(isset($_SESSION['m_class_read_info']['query_fields'])){
            $query_fields=$_SESSION['m_class_read_info']['query_fields'];
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
            }
        }

        if(isset($_SESSION['m_class_read_info']['class_code'])&&trim($_SESSION['m_class_read_info']['class_code'])!==''){
            $_SESSION['teacher_center']['class_code']=trim($_SESSION['m_class_read_info']['class_code']);
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //初始化, 選擇身份指標
        $choose_identity_flag=false;
        if(isset($sess_responsibilities)){
            $choose_identity_flag=true;
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

            if($choose_identity_flag){

                $sess_school_code=mysql_prep($sess_school_code);
                $curdate=date("Y-m-d");

                switch($auth_sys_check_lv){
                //1     校長
                //3     主任
                //5     帶班老師
                //12    行政老師
                //14    主任帶一個班
                //16    主任帶多個班
                //22    老師帶多個班
                    case 1:
                        $query_sql="
                            SELECT
                                `user`.`class`.`grade`,
                                `user`.`class_name`.`class_name`,
                                `user`.`class`.`class_code`
                            FROM `user`.`semester`
                                INNER JOIN `user`.`class` ON
                                `user`.`semester`.`semester_code`=`user`.`class`.`semester_code`
                                INNER JOIN `user`.`class_name` ON
                                `user`.`class`.`class_category`=`user`.`class_name`.`class_category`
                            WHERE 1=1
                                AND `user`.`class`.`classroom`=`user`.`class_name`.`classroom`
                                AND `user`.`semester`.`school_code`='{$sess_school_code}'
                                AND `user`.`semester`.`start`<='{$curdate}'
                                AND `user`.`semester`.`end`  >='{$curdate}'
                            GROUP BY `user`.`class`.`grade`, `user`.`class`.`classroom`, `user`.`class`.`class_category`
                            ORDER BY `user`.`class`.`grade`, `user`.`class_name`.`classroom` ASC
                        ";
                    break;

                    case 24:
                        $query_sql="
                            SELECT
                                `user`.`class`.`grade`,
                                `user`.`class_name`.`class_name`,
                                `user`.`class`.`class_code`
                            FROM `user`.`semester`
                                INNER JOIN `user`.`class` ON
                                `user`.`semester`.`semester_code`=`user`.`class`.`semester_code`
                                INNER JOIN `user`.`class_name` ON
                                `user`.`class`.`class_category`=`user`.`class_name`.`class_category`
                            WHERE 1=1
                                AND `user`.`class`.`classroom`=`user`.`class_name`.`classroom`
                                AND `user`.`semester`.`school_code`='{$sess_school_code}'
                                AND `user`.`semester`.`start`<='{$curdate}'
                                AND `user`.`semester`.`end`  >='{$curdate}'
                            GROUP BY `user`.`class`.`grade`, `user`.`class`.`classroom`, `user`.`class`.`class_category`
                            ORDER BY `user`.`class`.`grade`, `user`.`class_name`.`classroom` ASC
                        ";
                    break;

                    case 20:
                        $query_sql="
                            SELECT
                                `user`.`class`.`grade`,
                                `user`.`class_name`.`class_name`,
                                `user`.`class`.`class_code`
                            FROM `user`.`semester`
                                INNER JOIN `user`.`class` ON
                                `user`.`semester`.`semester_code`=`user`.`class`.`semester_code`
                                INNER JOIN `user`.`class_name` ON
                                `user`.`class`.`class_category`=`user`.`class_name`.`class_category`
                            WHERE 1=1
                                AND `user`.`class`.`classroom`=`user`.`class_name`.`classroom`
                                AND `user`.`semester`.`school_code`='{$sess_school_code}'
                                AND `user`.`semester`.`start`<='{$curdate}'
                                AND `user`.`semester`.`end`  >='{$curdate}'
                            GROUP BY `user`.`class`.`grade`, `user`.`class`.`classroom`, `user`.`class`.`class_category`
                            ORDER BY `user`.`class`.`grade`, `user`.`class_name`.`classroom` ASC
                        ";
                    break;

                    case 3:
                        $query_sql="
                            SELECT
                                `user`.`class`.`grade`,
                                `user`.`class_name`.`class_name`,
                                `user`.`class`.`class_code`
                            FROM `user`.`semester`
                                INNER JOIN `user`.`class` ON
                                `user`.`semester`.`semester_code`=`user`.`class`.`semester_code`
                                INNER JOIN `user`.`class_name` ON
                                `user`.`class`.`class_category`=`user`.`class_name`.`class_category`
                            WHERE 1=1
                                AND `user`.`class`.`classroom`=`user`.`class_name`.`classroom`
                                AND `user`.`semester`.`school_code`='{$sess_school_code}'
                                AND `user`.`semester`.`start`<='{$curdate}'
                                AND `user`.`semester`.`end`  >='{$curdate}'
                            GROUP BY `user`.`class`.`grade`, `user`.`class`.`classroom`, `user`.`class`.`class_category`
                            ORDER BY `user`.`class`.`grade`, `user`.`class_name`.`classroom` ASC
                        ";
                    break;

                    case 5:
                        $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                history.back(-1);
                            </script>
                        ";
                        die($jscript_back);
                    break;

                    case 12:
                        $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                history.back(-1);
                            </script>
                        ";
                        die($jscript_back);
                    break;

                    case 14:
                        $query_sql="
                            SELECT
                                `user`.`class`.`grade`,
                                `user`.`class_name`.`class_name`,
                                `user`.`class`.`class_code`
                            FROM `user`.`semester`
                                INNER JOIN `user`.`class` ON
                                `user`.`semester`.`semester_code`=`user`.`class`.`semester_code`
                                INNER JOIN `user`.`class_name` ON
                                `user`.`class`.`class_category`=`user`.`class_name`.`class_category`
                            WHERE 1=1
                                AND `user`.`class`.`classroom`=`user`.`class_name`.`classroom`
                                AND `user`.`semester`.`school_code`='{$sess_school_code}'
                                AND `user`.`semester`.`start`<='{$curdate}'
                                AND `user`.`semester`.`end`  >='{$curdate}'
                            GROUP BY `user`.`class`.`grade`, `user`.`class`.`classroom`, `user`.`class`.`class_category`
                            ORDER BY `user`.`class`.`grade`, `user`.`class_name`.`classroom` ASC
                        ";
                    break;

                    case 16:
                        $query_sql="
                            SELECT
                                `user`.`class`.`grade`,
                                `user`.`class_name`.`class_name`,
                                `user`.`class`.`class_code`
                            FROM `user`.`semester`
                                INNER JOIN `user`.`class` ON
                                `user`.`semester`.`semester_code`=`user`.`class`.`semester_code`
                                INNER JOIN `user`.`class_name` ON
                                `user`.`class`.`class_category`=`user`.`class_name`.`class_category`
                            WHERE 1=1
                                AND `user`.`class`.`classroom`=`user`.`class_name`.`classroom`
                                AND `user`.`semester`.`school_code`='{$sess_school_code}'
                                AND `user`.`semester`.`start`<='{$curdate}'
                                AND `user`.`semester`.`end`  >='{$curdate}'
                            GROUP BY `user`.`class`.`grade`, `user`.`class`.`classroom`, `user`.`class`.`class_category`
                            ORDER BY `user`.`class`.`grade`, `user`.`class_name`.`classroom` ASC
                        ";
                    break;

                    case 22:
                        $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                history.back(-1);
                            </script>
                        ";
                        die($jscript_back);
                    break;

                    case 99:
                        $query_sql="
                            SELECT
                                `user`.`class`.`grade`,
                                `user`.`class_name`.`class_name`,
                                `user`.`class`.`class_code`
                            FROM `user`.`semester`
                                INNER JOIN `user`.`class` ON
                                `user`.`semester`.`semester_code`=`user`.`class`.`semester_code`
                                INNER JOIN `user`.`class_name` ON
                                `user`.`class`.`class_category`=`user`.`class_name`.`class_category`
                            WHERE 1=1
                                AND `user`.`class`.`classroom`=`user`.`class_name`.`classroom`
                                AND `user`.`semester`.`school_code`='{$sess_school_code}'
                                AND `user`.`semester`.`start`<='{$curdate}'
                                AND `user`.`semester`.`end`  >='{$curdate}'
                            GROUP BY `user`.`class`.`grade`, `user`.`class`.`classroom`, `user`.`class`.`class_category`
                            ORDER BY `user`.`class`.`grade`, `user`.`class_name`.`classroom` ASC
                        ";
                    break;

                    default:
                        $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                history.back(-1);
                            </script>
                        ";
                        die($jscript_back);
                    break;
                }
            }
            $db_results=db_result($conn_type='pdo',$conn_user,$query_sql,array(),$arry_conn_user);
            $db_results_cno=count($db_results);

    //---------------------------------------------------
    //學期處理
    //---------------------------------------------------

        $sql="
            SELECT `start`, `end`
            FROM `semester`
            WHERE 1=1
                AND `start`<='{$curdate}'
                AND `end`  >='{$curdate}'
        ";
        $arry_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
        if(!empty($arry_result)){
            //學期時間範圍
            $semester_start =trim($arry_result[0]['start']);
            $semester_end   =trim($arry_result[0]['end']);
        }else{
            page_nrs("明日星球,教師中心");
            die();
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

            $pnos  =ceil($numrow/$psize);
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
                $arrys_chunk =array_chunk($db_results,$psize);
                $arrys_result=$arrys_chunk[$pinx-1];
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
        global $arry_conn_mssr;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $semester_start;
        global $semester_end;

        global $arrys_result;
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
    <script type="text/javascript" src="../../../../lib/js/table/code.js"></script>

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
            <td width="100%" height="100%" align="center" valign="top">
            <!-- 內容 -->
                <!-- 統計資料表格 開始 -->
                <div class="mod_data_tbl_outline" style="margin-top:35px;">
                    <table id="mod_data_tbl" border="0" width="95%" height="40px" cellpadding="5" cellspacing="0" style="" class="font-weight1 font-family1 fc_green0">
                        <tr align="center" valign="middle" class="fsize_16">
                            <td align="center" width="550px">
                                <span class="fsize_16">
                                    ●本頁資料計算時間為
                                    <span class="fc_red1"><?php echo $semester_start;?></span>
                                    ~
                                    <span class="fc_red1"><?php echo $semester_end;?></span>
                                    為止
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- 統計資料表格 結束 -->

                <!-- 資料表格 開始 -->
                <div class="mod_data_tbl_outline" style="margin-top:35px;">
                    <table id="mod_data_tbl" border="0" width="95%" cellpadding="5" cellspacing="0" style="margin:30px 0;" class="table_style3">
                        <thead>
                        <tr align="center" valign="middle" class="fsize_18">
                            <th width="150px" height="40px">班級名稱    </th>
                            <th width="150px" height="40px">學生數目    </th>
                            <th width="300px" height="40px">閱讀書本總數</th>
                            <th width="300px" height="40px">平均閱讀書本</th>
                        </tr>
                        </thead>

                        <?php foreach($arrys_result as $inx=>$arry_result) :?>
                        <?php
                        //---------------------------------------------------
                        //接收欄位
                        //---------------------------------------------------

                            extract($arry_result, EXTR_PREFIX_ALL, "rs");

                        //---------------------------------------------------
                        //處理欄位
                        //---------------------------------------------------

                            $rs_grade     =(int)$rs_grade;
                            $rs_class_name=trim($rs_class_name);
                            $rs_class_code=trim($rs_class_code);

                        //---------------------------------------------------
                        //特殊處理
                        //---------------------------------------------------

                            //-----------------------------------------------
                            //學生數目
                            //-----------------------------------------------

                                $rs_class_code   =addslashes($rs_class_code);
                                $curdate         =date("Y-m-d");
                                $student_cno     =0;
                                $arry_student_uid=array();
                                $sql="
                                    SELECT `user`.`student`.`uid`
                                    FROM `user`.`student`
                                    WHERE 1=1
                                        AND `user`.`student`.`class_code`='{$rs_class_code}'
                                        AND `user`.`student`.`start`<='{$curdate}'
                                        AND `user`.`student`.`end`  >='{$curdate}'
                                ";
                                $student_cno_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                if(!empty($student_cno_results)){
                                    $student_cno=(int)count($student_cno_results);
                                    foreach($student_cno_results as $student_cno_result){
                                        $arry_student_uid[]=(int)$student_cno_result['uid'];
                                    }
                                }

                            //-----------------------------------------------
                            //閱讀書本總數
                            //-----------------------------------------------

                                $book_borrow_cno=0;
                                if(!empty($arry_student_uid)){
                                    $list_student_uid=implode(",",$arry_student_uid);
                                    $sql="
                                        SELECT `mssr`.`mssr_book_borrow_semester`.`user_id`
                                        FROM `mssr`.`mssr_book_borrow_semester`
                                        WHERE 1=1
                                            AND `mssr`.`mssr_book_borrow_semester`.`user_id` IN ($list_student_uid)
                                            AND `mssr`.`mssr_book_borrow_semester`.`borrow_sdate` BETWEEN '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
                                        GROUP BY `mssr`.`mssr_book_borrow_semester`.`user_id`, `mssr`.`mssr_book_borrow_semester`.`book_sid`
                                    ";
                                    $book_borrow_cno_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                    $book_borrow_cno=count($book_borrow_cno_results);
                                }

                                $book_borrow_avg=0;
                                if($student_cno!==0 && $book_borrow_cno!==0){$book_borrow_avg=round($book_borrow_cno/$student_cno,1);}
                        ?>
                        <tr class="fsize_16">
                            <td height="35px" align="center" valign="middle"><?php echo $rs_grade.'年'.$rs_class_name.'班'?></td>
                            <td height="35px" align="center" valign="middle"><?php echo $student_cno;?>人</td>
                            <td height="35px" align="center" valign="middle"><?php echo $book_borrow_cno;?>本</td>
                            <td height="35px" align="center" valign="middle"><?php echo $book_borrow_avg;?>本</td>
                        </tr>
                        <?php endforeach ;?>
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

    window.onload=function(){

        //設定動態高度
        var oIFC=parent.document.getElementById('IFC');
        var oparent_IFC=parent.parent.document.getElementById('IFC');
        oIFC.style.height=parseInt($(document).height()+50)+'px';
        oparent_IFC.style.height=parseInt($(document).height()+250)+'px';

        //滑鼠動作設置
        $('#mod_data_tbl th').mouseover(function(){
            $(this).css('cursor', 'pointer');
        });

        parent.$.unblockUI();
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
                            目前系統無班級資料，或查無班級資料!
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
        parent.$.unblockUI();
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