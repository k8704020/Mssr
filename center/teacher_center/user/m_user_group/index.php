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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_group');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //SESSION
        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用

        if(isset($_SESSION['m_user_group']['filter'])){
            $filter=$_SESSION['m_user_group']['filter'];
        }
        if(isset($_SESSION['m_user_group']['query_fields'])){
            $query_fields=$_SESSION['m_user_group']['query_fields'];
        }

        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);
        $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
        $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
        $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //初始化, 已分組人數
        $has_group=array();

        //初始化, 已啟用的組別陣列
        $arrys_group=array();

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

            $users=arrys_users($conn_user,$sess_class_code,$date=date("Y-m-d"),$arry_conn_user);

        //---------------------------------------------------
        //學期時間
        //---------------------------------------------------

            $curdate=date("Y-m-d");

            if(in_array($auth_sys_check_lv,array(99))){
                $sql="
                    SELECT
                        `start`,
                        `end`
                    FROM `semester`
                    WHERE 1=1
                        AND '{$curdate}' BETWEEN `start` AND `end`
                    ORDER BY `end` DESC
                ";
            }else{
                $sql="
                    SELECT
                        `start`,
                        `end`
                    FROM `semester`
                    WHERE 1=1
                        #AND `uid`    ={$sess_user_id}
                        AND '{$curdate}' BETWEEN `start` AND `end`
                    ORDER BY `end` DESC
                ";
            }
            $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
            if(!empty($arrys_result)){
                $semester_start=trim($arrys_result[0]['start']);
                $semester_end=trim($arrys_result[0]['end']);
            }else{
                die();
            }

        //---------------------------------------------------
        //已分組人數
        //---------------------------------------------------

            if(!empty($users)){
                $query_sql="
                    SELECT
                        `user_id`
                    FROM `mssr_user_group`
                    WHERE 1=1
                        AND `user_id` IN ($users)
                        AND `keyin_mdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                ";
                $has_group=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
            }else{
                $msg="您目前所在的班級沒有學生!!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }

        //---------------------------------------------------
        //已啟用的組別陣列
        //---------------------------------------------------

            $sess_school_code   =mysql_prep($sess_school_code);
            $sess_grade         =(int)$sess_grade;
            $sess_classroom     =(int)$sess_classroom;

            $query_sql="
                SELECT
                    `group_sid`
                FROM `mssr_group`
                WHERE 1=1
                    AND `school_code` ='{$sess_school_code  }'
                    AND `grade_id`    = {$sess_grade        }
                    AND `classroom_id`= {$sess_classroom    }
                    AND `group_sdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                ORDER BY `group_id` DESC
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
            foreach($arrys_result as $inx=>$arry_result){
                $group_sid=trim($arry_result['group_sid']);

                //匯入, 已啟用的組別資訊
                $arrys_group[$inx]=$group_sid;
            }

        //---------------------------------------------------
        //全部的組別陣列
        //---------------------------------------------------

            if($filter!=''){
                $query_sql="
                    SELECT
                        IFNULL((
                            SELECT COUNT(`user_id`)
                            FROM `mssr_user_group`
                            WHERE 1=1
                                AND `mssr_user_group`.`group_sid`=`mssr_group`.`group_sid`
                        ),0) AS `user_group_cno`,
                        `create_by`,
                        `edit_by`,
                        `school_code`,
                        `grade_id`,
                        `classroom_id`,
                        `group_id`,
                        `group_sid`,
                        `group_name`,
                        `group_sdate`,
                        `group_mdate`,
                        `keyin_ip`
                    FROM `mssr_group`
                    WHERE 1=1
                        AND `school_code` ='{$sess_school_code  }'
                        AND `grade_id`    = {$sess_grade        }
                        AND `classroom_id`= {$sess_classroom    }
                        AND `group_sdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                        -- FILTER在此
                        {$filter}
                    #ORDER BY FIELD(`edit_by`,$sess_user_id) DESC
                    ORDER BY `group_id` DESC
                ";
            }else{
                $query_sql="
                    SELECT
                        IFNULL((
                            SELECT COUNT(`user_id`)
                            FROM `mssr_user_group`
                            WHERE 1=1
                                AND `mssr_user_group`.`group_sid`=`mssr_group`.`group_sid`
                        ),0) AS `user_group_cno`,
                        `create_by`,
                        `edit_by`,
                        `school_code`,
                        `grade_id`,
                        `classroom_id`,
                        `group_id`,
                        `group_sid`,
                        `group_name`,
                        `group_sdate`,
                        `group_mdate`,
                        `keyin_ip`
                    FROM `mssr_group`
                    WHERE 1=1
                        AND `school_code` ='{$sess_school_code  }'
                        AND `grade_id`    = {$sess_grade        }
                        AND `classroom_id`= {$sess_classroom    }
                        AND `group_sdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                    #ORDER BY FIELD(`edit_by`,$sess_user_id) DESC
                    ORDER BY `group_id` DESC
                ";
            }

    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=0;  //資料總筆數
        $psize =5;  //單頁筆數,預設5筆
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
                <td align="left" valign="middle" width="400px">
                    <!-- 教師中心路徑選單 開始 -->
                    <div id="teacher_center_path">
                        <table id="teacher_center_path_cont" border="0" width="100%">
                            <tr>
                                <td align="left" valign="middle" class="menu_dot">
                                    <span class="menu_cont">
                                        <img width="12" height="12" src="../../../../img/icon/blue.jpg" border="0">
                                        <!-- <a href="../../index.php">教師中心</a> -->

                                        <!-- <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span> -->
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
                            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx-1,$psize),$arry_conn_mssr);
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

    $(function(){

        //快速切換設置
        fast_area_config('#fast_area',_top=0,_right=0);
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

        global $users;
        global $has_group;
        global $sess_login_info;
        global $arrys_result;
        global $arrys_group;
        global $config_arrys;
        global $conn_user;
        global $conn_mssr;
        global $auth_sys_check_lv;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=4; //欄位個數
        $btn_nos=2; //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        $sess_user_id=(int)$sess_login_info['uid'];

        if($users!==''){
            $arrys_user=explode(',',$users);
        }else{
            $arrys_user=array();
        }
?>
<!-- 學生資訊  開始 -->
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table_style2" style="position:relative;top:20px;"/>
    <tr align="center" valign="middle" class="bg_gray1 fc_white0">
        <td height="30px" colspan="4">
            學生資訊
        </td>
    </tr>
    <tr align="left" valign="middle">
        <td width="205px" height="50px">
            <span style="position:relative;left:10px;" class="fc_gray0">學生人數：</span>
            <span style="position:relative;left:5px;" class="fc_gray0">
                <?php echo count($arrys_user);?> 人
            </span>
        </td>
        <td width="205px" height="50px">
            <span style="position:relative;left:10px;" class="fc_gray0">組別數：</span>
            <span style="position:relative;left:5px;" class="fc_gray0">
                <?php echo count($arrys_group);?> 組
            </span>
        </td>
        <td width="205px" height="50px">
            <!-- <span style="position:relative;left:10px;" class="fc_gray0">未分組人數：</span>
            <span style="position:relative;left:5px;" class="fc_gray0">
                <?php echo count($arrys_user)-count($has_group);;?> 人
            </span> -->
        </td>
        <td height="50px">
            <input type="button" value="手動分組" class="ibtn_gr6030" onclick="mt();" onmouseover="this.style.cursor='pointer'">
            <?php if(!in_array($auth_sys_check_lv,array(99))):?>
                <input type="button" value="隨機分組" class="ibtn_gr6030" onclick="at();" onmouseover="this.style.cursor='pointer'">
            <?php endif;?>
        </td>
    </tr>
</table>
<!-- 學生資訊  結束 -->

<!-- 組別資訊  開始 -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="position:relative;top:30px;">
    <tr>
        <td align="left"><h1 class="fc_red0">組別資訊：</h1></td>
    </tr>
    <tr>
        <!-- 在此設定寬高 -->
        <td width="100%" height="250px" align="center" valign="top">
            <table id="mod_data_tbl" border="0" width="100%" cellpadding="5" cellspacing="0" style="margin-top:0px;" class="table_style1">
                <tr align="center" valign="middle" class="bg_gray1 fc_white0">
                    <td width="120px">建立人  </td>
                    <td width="120px">組別名稱</td>
                    <td width="120px">建立日期</td>
                    <td width="150px">組別人數</td>
                    <td width=""><!-- 功能 -->         </td>
                </tr>

                <?php foreach($arrys_result as $inx=>$arry_result) :?>
                <?php
                //---------------------------------------------------
                //接收欄位
                //---------------------------------------------------

                    extract($arry_result, EXTR_PREFIX_ALL, "rs");

                //---------------------------------------------------
                //處理欄位
                //---------------------------------------------------

                    //create_by         建立人
                    $rs_create_by=(int)$rs_create_by;

                    //edit_by           修改人
                    $rs_edit_by=(int)$rs_edit_by;

                    //user_group_cno    組別人數
                    $rs_user_group_cno=(int)$rs_user_group_cno;

                    //group_sid         組別識別碼
                    $rs_group_sid=trim($rs_group_sid);

                    //rs_group_name     組別名稱
                    $rs_group_name=trim($rs_group_name);
                    if(mb_strlen($rs_group_name)>10){
                        $rs_group_name=mb_substr($rs_group_name,0,10)."..";
                    }

                    //group_sdate       建立日期
                    $rs_group_sdate=date("Y-m-d",strtotime($rs_group_sdate));

                //---------------------------------------------------
                //特殊處理
                //---------------------------------------------------

                    //-----------------------------------------------
                    //查找, 建立人名稱
                    //-----------------------------------------------

                        $rs_create_by_name='';
                        $query_sql="
                            SELECT
                                `name`
                            FROM `member`
                            WHERE 1=1
                                AND `uid`={$rs_create_by}
                        ";
                        //送出
                        $err ='DB QUERY FAIL';
                        $sth=$conn_user->query($query_sql);
                        foreach($sth as $value){
                            $rs_create_by_name=trim($value['name']);
                        }
                ?>
                <tr>
                    <td height="30px" align="center" valign="middle"><?php echo htmlspecialchars($rs_create_by_name);?> </td>
                    <td height="30px" align="center" valign="middle"><?php echo htmlspecialchars($rs_group_name);?>     </td>
                    <td height="30px" align="center" valign="middle"><?php echo htmlspecialchars($rs_group_sdate);?>    </td>
                    <td height="30px" align="center" valign="middle"><?php echo $rs_user_group_cno;?>                   </td>
                    <td height="30px" align="center" valign="middle">
                        <?php if(!in_array($auth_sys_check_lv,array(99))):?>
                            <?php if($rs_user_group_cno===0):?>
                                <!-- <input type="button" value="停用" class="ibtn_gr6030" onclick="edit('停用','<?php echo addslashes($rs_group_sid);?>')" onmouseover="this.style.cursor='pointer'"> -->
                            <?php else:?>
                                <!-- <input type="button" value="停用" class="ibtn_gr6030 btn_disabled" onmouseover="this.style.cursor='pointer'" disabled> -->
                            <?php endif;?>

                            <?php if($rs_user_group_cno!==0):?>
                                <!-- <input type="button" value="移除組別成員" class="ibtn_gr9030" onclick="alert('即將開放!');" onmouseover="this.style.cursor='pointer'"> -->
                            <?php else:?>
                                <!-- <input type="button" value="移除組別成員" class="ibtn_gr9030 btn_disabled" onmouseover="this.style.cursor='pointer'" disabled> -->
                            <?php endif;?>
                        <?php endif;?>
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
                        <span style="position:relative;top:10px;right:25px;">
                            <input type="button" value="新增組別" class="ibtn_gr6030" onclick="add();" onmouseover="this.style.cursor='pointer'">
                        </span>
                    </td>
                </tr>
            </table>

        <!-- 內容 -->
        </td>
    </tr>
</table>
<!-- 組別資訊  結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;

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

    function edit(edit_type,group_sid){
    //採用 | 停用
        var url ='';
        var page=str_repeat('../',0)+'edit/editA.php';
        var arg ={
            'psize'     :psize,
            'pinx'      :pinx,
            'edit_type' :edit_type,
            'group_sid' :group_sid
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

    function add(){
    //新增
        var url ='';
        var page=str_repeat('../',0)+'add/addF.php';
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

        go(url,'self');
    }

    function at(){
    //自動分組
        var url ='';
        var page=str_repeat('../',0)+'at/atA.php';
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

        go(url,'self');
    }

    function mt(){
    //手動分組
        var url ='';
        var page=str_repeat('../',0)+'mt/mtF.php';
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

        go(url,'self');
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
                        目前系統無組別，或查無組別，請先行分組!<br/>
                        <input type="button" value="新增組別" class="ibtn_gr6030" onclick="javascript:add();" onmouseover="this.style.cursor='pointer'">
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
        var page=str_repeat('../',0)+'add/addF.php';
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
