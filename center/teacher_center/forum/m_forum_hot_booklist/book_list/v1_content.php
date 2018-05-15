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

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/date/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();
        // echo '<pre>';
        // print_r($_SESSION);

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
// echo '<pre>';
// print_r($sess_login_info);
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
        $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_forum_hot_booklist');
    }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------


     $class_code = $_GET['class_code'];

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
        //SQL查詢
        //-----------------------------------------------

        //---------------------------------------------------
        //設定參數
        //---------------------------------------------------
        if((isset($class_code))&&(trim($class_code)!=='')){

                    //參數

                    $date=date("Y-m-d");
                    $arrys_class_code=array();

                    $sql="
                        SELECT
                            `class`.`class_code`,
                            `class`.`classroom`,
                            `semester`.`start`,
                            `semester`.`end`
                        FROM `semester`
                            INNER JOIN `class` ON
                            `semester`.`semester_code`=`class`.`semester_code`
                        WHERE 1=1
                            AND `class`.`class_code`      = '{$class_code}'
                            AND `class`.`classroom`      <>29
                            AND `semester`.`start`       <= '{$date            }'
                            AND `semester`.`end`         >= '{$date            }'
                        ORDER BY `class`.`classroom` ASC
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);

                    if(!empty($arrys_result)){
                        foreach($arrys_result as $inx=>$arry_result){
                            $class_code=trim($arry_result['class_code']);
                            $classroom=(int)($arry_result['classroom']);
                            $semester_start=trim($arry_result['start']);
                            $semester_end=trim($arry_result['end']);

                            //回填相關資訊
                            $arrys_class_code[$inx]['class_code']=$class_code;
                            $arrys_class_code[$inx]['classroom']=$classroom;
                            $arrys_class_code[$inx]['semester_start']=$semester_start;
                            $arrys_class_code[$inx]['semester_end']=$semester_end;
                        }

                        //轉json
                        $json_class_code=json_encode($arrys_class_code,true);
                    }


                }else{
                    //網頁標題
                    $title="明日星球,教師中心";
                    page_sel_no_user($title);
                    die();
                }

             //一周日期
             $week_sdate    =trim(date('Y-m-d', time()-86400*date('w')+(date('w')>0?86400:-6*86400)));
             $week_edate    =trim(date("Y-m-d",strtotime($week_sdate)+(86400*6)));

            require_once('model/hot.model.php');
            require_once('contreller/hot.class.php');








    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=count($hotList);  //資料總筆數
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

        $pnos  =ceil($numrow/$psize);
        $pinx  =($pinx>$pnos)?$pnos:$pinx;

        $sinx  =(($pinx-1)*$psize);
        $einx  =(($pinx)*$psize);
        $einx  =($einx>$numrow)?$numrow:$einx;

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,教師中心";

        if($numrow!=0){
            $arrys_chunk =array_chunk($hotList,$psize);
            $hotList=$arrys_chunk[$pinx-1];
            page_hrs($title);

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
        global $arry_conn_user;


        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $hotList;
        global $teacher_uid;
        global $class_code;



        global $semester_start ;
        global $semester_end ;

        global $config_arrys;
        global $conn_mssr;
        global $conn_user;

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
    <?php echo bing_analysis($allow=true);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/string/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../css/def.css" media="all" />

    <style>
    th {
        text-align: center;
    }
    </style>
</Head>

<Body>

    <!-- 資料列表 開始 -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="100%" align="center" valign="top">
            <!-- 內容 -->

               <!-- 資料列表 開始 -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="100%" align="center" valign="top">
            <!-- 內容 -->
                 <!-- 資料資訊表格 開始 -->
                <div class="mod_data_tbl_outline" style="margin-top:35px;">
                    <table id="mod_data_tbl" border="0" width="99%" height="40px" cellpadding="5" cellspacing="0" style="" class="font-weight1 font-family1 fc_green0">
                        <tr align="center" valign="middle" class="fsize_16">
                            <td align="center" valign="middle" width="650px" >
                                <span class="fsize_16">
                                                        ●本頁資料計算時間為
                                    <span class="fc_red1"><?php echo htmlspecialchars($semester_start);?></span>
                                    ~
                                    <span class="fc_red1"><?php echo htmlspecialchars($semester_end);?></span>
                                    為止
                                </span>
                                <input type="button" value="請按我關閉" class="ibtn_gr9030" onclick="parent.close()" onmouseover="this.style.cursor='pointer'" style="cursor: pointer;">

                            </td>
                        </tr>
                    </table>
                </div>
                <!-- 資料資訊表格 結束 -->

                <!-- 資料表格 開始 -->
                <div class="mod_data_tbl_outline" style="margin-top:35px;">
                    <table  id="mod_data_tbl" border="0" width="95%" cellpadding="5" cellspacing="0" style="margin:30px; 0" class="table_style3">
                        <thead>
                            <tr align="center" valign="middle" class="fsize_18">
                                <th>排名</th>
                                <th>書名</th>
                                <th>討論次數</th>


                            </tr>
                        </thead>
                        <?php $i = 0;?>
                        <?php  foreach($hotList as $key=>$value){ $i++;?>
                               <tr align="center">
                                    <td><?php echo $i ?>              </td>
                                    <td >
                                        <?php echo $value['book_name']?>
                                    </td>

                                    <td><?php echo $value['cno']?>次  </td>
                                </tr>
                        <?php } ?>

                    </table>
                </div>
                <!-- 資料表格 結束 -->

            <!-- 內容 -->
            </td>
        </tr>
    </table>
    <!-- 資料列表 結束 -->

            <!-- 內容 -->
            </td>
        </tr>
    </table>
    <!-- 資料列表 結束 -->
    <table border="0" width="500px">
        <tr valign="middle">
            <td align="left">
                <!-- 分頁列 -->
                <span id="page" style="position:relative;top:10px;"></span>
            </td>
        </tr>
    </table>

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;

    window.onload=function(){
        parent.$.unblockUI();
        //設定動態高度
        var oIFC=parent.document.getElementById('IFC');
        var oparent_IFC=parent.parent.document.getElementById('IFC');
        oIFC.style.height=parseInt($(document).height()+50)+'px';
        oparent_IFC.style.height=parseInt($(document).height()+250)+'px';
    }



    //-------------------------------------------------------
    //INSERT
    //-------------------------------------------------------

    $(function() {
        var input =  $("input[name='box']");

        input.click(function(){

        var sid = $(this).attr('sid');
        var tid = $(this).attr('tid');

            if(confirm("您確定要討論這本書嗎?")){
                location.href = "act/hotList/add.php?book_sid=" + sid + "&tid=" + tid
            }


        })

    });

    //-------------------------------------------------------
    //範例
    //-------------------------------------------------------

    var psize=<?php echo (int)$psize;?>;
    var pinx =<?php echo (int)$pinx;?>;
    var class_code ="<?php echo $class_code ?>";


    window.onload=function(){

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
            'page_args' :{
                'class_code':class_code
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
    $conn_user=NULL;
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
        global $arry_conn_user;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $config_arrys;
        global $conn_mssr;
        global $conn_user;

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
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/string/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../css/def.css" media="all" />
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
                            <img src="../../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
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
    $conn_user=NULL;
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
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/string/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../css/def.css" media="all" />
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
                            <img src="../../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
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
        parent.$.unblockUI();
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