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
                    APP_ROOT.'lib/php/array/code',
                    APP_ROOT.'lib/php/form/code'
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

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //SESSION
        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);
        $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
        $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
        $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];

        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

        //初始化, 已啟用的組別陣列
        $arrys_group=array();

        //初始化, 已分組人員陣列資訊
        $arrys_has_user_group=array();

        //初始化, 未分組人員陣列資訊
        $arrys_no_user_group=array();

        //初始化, 相關人員陣列資訊
        $arrys_users_info=array();

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

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
                        #AND `uid`={$sess_user_id}
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
        //老師陣列
        //---------------------------------------------------

            $sess_class_code=mysql_prep(trim($sess_login_info['arrys_class_code'][0]['class_code']));
            $arry_teacher_uid=array();
            $teachers="";

            $sql="
                SELECT
                    `uid`
                FROM `teacher`
                WHERE 1=1
                    AND `class_code`='{$sess_class_code}'
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
            if(!empty($arrys_result)){
                foreach($arrys_result as $arry_result){
                    $uid=(int)$arry_result['uid'];
                    $arry_teacher_uid[]=$uid;
                }
            }
            $teachers=implode($arry_teacher_uid,"','");

        //---------------------------------------------------
        //學生陣列
        //---------------------------------------------------

            $users=arrys_users($conn_user,$sess_class_code,$date=date("Y-m-d"),$arry_conn_user);
            if(empty($users)){
                $err='ARRYS_USERS:NO USERS';
                die($err);
            }else{
                //額外加上老師
                if($teachers!==''){
                    $users.=",'".$teachers."'";
                }
                $arrys_users=explode("','",$users);
                $arrys_users_cno=count($arrys_users);
                foreach($arrys_users as $inx=>$val){
                    if(($inx===0)||($inx===$arrys_users_cno-1)){
                        $val=str_replace("'","", $val);
                        $arrys_users[$inx]=(int)$val;
                    }
                }
            }

            //設置, 相關人員陣列資訊
            foreach($arrys_users as $inx=>$tmp_user_id){
                $tmp_user_id=(int)$tmp_user_id;
                $get_user_info=get_user_info($conn_user,$tmp_user_id,$array_filter=array('name'),$arry_conn_user);
                $rs_user_name=trim($get_user_info[0]['name']);

                //匯入, 相關人員陣列資訊
                $arrys_users_info[$tmp_user_id]=$rs_user_name;
            }

            //轉json 格式
            $json_arrys_users_info=json_encode($arrys_users_info,true);

        //---------------------------------------------------
        //已啟用的組別陣列
        //---------------------------------------------------

            $sess_school_code   =mysql_prep($sess_school_code);
            $sess_grade         =(int)$sess_grade;
            $sess_classroom     =(int)$sess_classroom;

            $query_sql="
                SELECT
                    `group_sid`,
                    `group_name`
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
                $group_name=trim($arry_result['group_name']);

                //匯入, 已啟用的組別資訊
                $arrys_group[$inx]['group_sid']=$group_sid;
                $arrys_group[$inx]['group_name']=$group_name;
            }

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_group`.`group_id`,
                    `mssr_group`.`group_sid`,
                    `mssr_group`.`group_name`,

                    `mssr_user_group`.`user_id`
                FROM `mssr_group`
                    INNER JOIN `mssr_user_group` ON
                    `mssr_group`.`group_sid`=`mssr_user_group`.`group_sid`
                WHERE 1=1
                    AND `mssr_group`.`school_code`   ='{$sess_school_code}'
                    AND `mssr_group`.`grade_id`      = {$sess_grade      }
                    AND `mssr_group`.`classroom_id`  = {$sess_classroom  }
                    AND `mssr_user_group`.`user_id` IN ({$users})
                    AND `group_sdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            if(empty($arrys_result)){
            //全部都未分組
                foreach($arrys_users as $inx=>$rs_user_id){
                    $rs_user_id=(int)$rs_user_id;
                    //匯入, 未分組人員陣列資訊
                    array_push($arrys_no_user_group,$rs_user_id);
                }
            }else{
            //已確定有人分過組
                //匯入, 已分組人員陣列資訊
                $arrys_has_user_group=$arrys_result;

                //匯入, 未分組人員陣列資訊
                $tmp_arry_user=array();
                foreach($arrys_result as $inx=>$arry_result){
                    $tmp_arry_user[$inx]=(int)$arry_result['user_id'];
                }
                foreach($arrys_users as $inx=>$rs_user_id){
                    $rs_user_id=(int)$rs_user_id;
                    if(!in_array($rs_user_id,$tmp_arry_user)){
                        array_push($arrys_no_user_group,$rs_user_id);
                    }
                }
            }

            //轉 json
            //$json_has_user_group=json_encode($arrys_has_user_group);
            //$json_no_user_group=json_encode($arrys_no_user_group);

            //$json_has_user_group=array_json2($arrys_has_user_group);
            //$json_no_user_group=array_json1($arrys_no_user_group);

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
            $sys_ename=$FOLDER[count($FOLDER)-3];
            $mod_ename=$FOLDER[count($FOLDER)-2];
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
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/array/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../css/def.css" media="all" />

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
                                        <img width="12" height="12" src="../../../../../img/icon/blue.jpg" border="0">
                                        <a href="../../../index.php">教師中心</a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="../<?php echo htmlspecialchars($sys_url);?>">
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
                <td width="760px" colspan="2">
                    <!-- 資料內容 開始 -->
                    <?php
                        //呼叫頁面
                        page_ok($title);
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
<?php echo fast_area($rd=3);?>
<!-- 快速切換區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    window.onload=function(){
        //快速切換設置
        fast_area_config('#fast_area',0,0);
    }

</script>

<?php function page_ok($title="") {?>
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
        global $arry_conn_user;

        //local
        global $psize;
        global $pinx;
        global $auth_sys_check_lv;

        global $conn_user;

        global $arrys_group;
        global $arrys_has_user_group;
        global $arrys_no_user_group;
        global $json_arrys_users_info;
        //global $json_has_user_group;
        //global $json_no_user_group;

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        global $sess_login_info;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
?>
<!-- 內容 開始 -->
<form id='Form1' name='Form1' method='post' onsubmit="return false;">
    <table align="center" border="0" width="750px" class="table_style0" style="position:relative;top:30px;">
        <tr valign="center">
            <td height="30px" class="fc_blue0">
                手動分組
                <span style="display:none;">
                    <span class="fc_red1" style="position:relative;left:100px;">目前選中的組別為: </span>
                    <span class="fc_blue0" style="position:relative;left:100px;" id="focus_msg"></span>
                </span>
            </td>
            <td height="30px" class="fc_blue0" align="right">
                <?php if(in_array($auth_sys_check_lv,array(99))):?>
                    <input type="button" id="BtnS" name="BtnS" value="儲存" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="1" onmouseover="this.style.cursor='pointer'">
                    <input type="button" id="BtnB" name="BtnB" value="返回" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="2" onmouseover="this.style.cursor='pointer'">
                    <input type="button" value="新增組別" class="ibtn_gr6030" onclick="add();" style="" onmouseover="this.style.cursor='pointer'">
                    <input type="button" id="BtnU" name="BtnU" value="取消鎖定" class="ibtn_gr6030" style="margin:10px 0px;color:#ff0000;display:none;" tabindex="3" onmouseover="this.style.cursor='pointer'" onclick="unset_tbl_focus(this);">
                <?php else:?>
                    <input type="button" id="BtnS" name="BtnS" value="儲存" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="1" onmouseover="this.style.cursor='pointer'">
                    <input type="button" id="BtnB" name="BtnB" value="返回" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="2" onmouseover="this.style.cursor='pointer'">
                    <input type="button" value="新增組別" class="ibtn_gr6030" onclick="add();" onmouseover="this.style.cursor='pointer'">
                    <input type="button" id="BtnU" name="BtnU" value="取消鎖定" class="ibtn_gr6030" style="margin:10px 0px;color:#ff0000;display:none;" tabindex="3" onmouseover="this.style.cursor='pointer'" onclick="unset_tbl_focus(this);">
                <?php endif;?>
            </td>
        </tr>
        <tr class="fc_gray0">
            <td align="left" valign="top" width="490px" height="" class="b_line gr_dashed" bgcolor="#f9f9f9">
                <form id='Form1' name='Form1' method='post' onsubmit="return false;">
                <!-- 顯示已啟用組別 開始 -->
                <?php foreach($arrys_group as $inx=>$arry_group):?>
                <?php
                    $group_sid=trim($arry_group['group_sid']);  //組別識別碼
                    $group_name=trim($arry_group['group_name']);//組別名稱
                ?>
                    <table id="<?php echo addslashes($group_sid);?>" cellpadding="0" cellspacing="0" border="1" align="left" valign="top" width="88px" height="180px"
                    style="position:relative;margin:5px;left:0px;border:1px solid #cccccc;"
                    class="bg_gray0" ondrop="drop(this, event)" ondragover="dragover(this, event)" focus='no'/>
                        <tr>
                            <td height="20px" align="center" valign="middle" class="bg_gray1 fc_white0"
                            onclick="tbl_focus('<?php echo addslashes($group_sid);?>');"
                            onmouseover="this.style.cursor='pointer'">
                                <span ondblclick="edit(this,event,'<?php echo addslashes($group_sid);?>');"><?php echo htmlspecialchars($group_name);?></span>
                                <span class="fsize_18 fc_red1" style="position:relative;bottom:3px;float:right;"
                                onclick="del(this,event,'<?php echo addslashes($group_sid);?>');">⊗</span>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" valign="top">
                                <!-- 顯示組別內的人員 開始 -->
                                <?php foreach($arrys_has_user_group as $inx=>$arry_has_user_group):?>
                                <?php
                                    $rs_group_sid=trim($arry_has_user_group['group_sid']);
                                    $rs_user_id=(int)$arry_has_user_group['user_id'];
                                ?>
                                    <?php if($rs_group_sid===$group_sid):?>
                                    <?php
                                        $get_user_info=get_user_info($conn_user,$rs_user_id,$array_filter=array('name'),$arry_conn_user);
                                        $rs_user_name=trim($get_user_info[0]['name']);
                                    ?>
                                        <table id="<?php echo (int)$rs_user_id;?>" cellpadding="0" cellspacing="0" border="3" width="80px" align="center" bgcolor="#ffffff"
                                        style="position:relative;margin:2px;border-color:#dbdbdb"
                                        onmouseover="this.style.cursor='pointer'"
                                        draggable="true" ondragstart="dragstart(this,event)" ondblclick='dblclick(this);'/>
                                            <tr align="center" valign="middle">
                                                <td height="20px">
                                                    <?php echo htmlspecialchars($rs_user_name);?>
                                                </td>
                                            </tr>
                                        </table>
                                    <?php endif;?>
                                <?php endforeach;?>
                                <!-- 顯示組別內的人員 結束 -->

                                <!-- 隱藏的紀錄 開始 -->
                                <textarea name="<?php echo addslashes($group_sid);?>"
                                cols="3" rows="3" wrap="hard" style="resize:none;display:none;" readonly></textarea>
                                <!-- 隱藏的紀錄 結束 -->
                            </td>
                        </tr>
                    </table>
                <?php endforeach;?>
                <!-- 顯示已啟用組別 結束 -->
                </form>
            </td>
            <td align="right" valign="top" width="" height="" class="b_line gr_dashed" bgcolor="#f9f9f9">
                <table id="tbl_no_group" class="bg_gray0" cellpadding="0" cellspacing="0" border="1" width="100%" height="350px"
                ondrop="drop(this, event)" ondragover="dragover(this, event)" focus='no' bgcolor="" style="border:1px solid #cccccc;"/>
                    <tr><td align="center" height="25px" class="bg_gray1 fc_white0" onclick="tbl_focus('tbl_no_group');" onmouseover="this.style.cursor='pointer'">
                        <span>未分組的人員區塊</span>
                    </td></tr>
                    <tr>
                        <td align="left" valign="top">
                            <!-- 顯示未分組的人員 開始 -->
                            <?php foreach($arrys_no_user_group as $rs_user_id):?>
                            <?php
                                $rs_user_id=(int)$rs_user_id;
                                $get_user_info=get_user_info($conn_user,$rs_user_id,$array_filter=array('name'),$arry_conn_user);
                                $rs_user_name=trim($get_user_info[0]['name']);
                            ?>
                            <table id="<?php echo (int)$rs_user_id;?>" cellpadding="0" cellspacing="0" border="3" width="80px" align="left"
                            style="position:relative;margin:2px;left:1px;border-color:#dbdbdb"
                            onmouseover="this.style.cursor='pointer'" bgcolor="#ffffff"
                            draggable="true" ondragstart="dragstart(this,event)" ondblclick='dblclick(this);'/>
                                <tr align="center" valign="middle">
                                    <td height="20px">
                                        <?php echo htmlspecialchars($rs_user_name);?>
                                    </td>
                                </tr>
                            </table>
                            <?php endforeach;?>
                            <!-- 顯示未分組的人員 結束 -->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td align="center" colspan="2">
                <input type="hidden" id="psize" name="psize" value="<?php echo addslashes($psize);?>">
                <input type="hidden" id="pinx" name="pinx" value="<?php echo addslashes($pinx);?>">
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
    var nl        ='\r\n';
    var pinx      =<?php echo addslashes($pinx);?>;                 //單頁筆數
    var psize     =<?php echo addslashes($psize);?>;                //目前分頁索引
    var json_arrys_users_info=<?php echo $json_arrys_users_info;?>; //相關人員陣列資訊

    //物件
    var oForm1    =document.getElementById('Form1');    //表單
    var oBtnS     =document.getElementById('BtnS');     //儲存
    var oBtnB     =document.getElementById('BtnB');     //返回
    var oBtnU     =document.getElementById('BtnU');     //取消紅框鎖定

    function edit(obj,event,_group_sid){

        if(confirm('要修改組別名稱嗎?')){
            var group_name=prompt("請輸入組別名稱!");
            var arry_err=[];

            if(!group_name){
                unset_tbl_focus($('#BtnU')[0]);
                event.stopPropagation();
                return false;
            }else{
                if(trim(group_name)==''){
                    arry_err.push('請輸入組別名稱!');
                }
            }

            if(arry_err.length!=0){
                alert(arry_err.join(nl));
            }else{
                var url ='';
                var page=str_repeat('../',0)+'edit/editA.php';
                var arg ={
                    'group_name':group_name,
                    'group_sid':_group_sid,
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
        }

        unset_tbl_focus($('#BtnU')[0]);
        event.stopPropagation();
        return false;
    }

    function del(obj,event,_group_sid){

        if(confirm('你確定要刪除組別嗎?')){
            var url ='';
            var page=str_repeat('../',0)+'del/delA.php';
            var arg ={
                'group_sid':_group_sid,
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

        event.stopPropagation();
        return false;
    }

    function tbl_focus(_group_sid){
        var arry_group=[];
        <?php foreach($arrys_group as $inx=>$arry_group):?>
        <?php
            $group_sid=trim($arry_group['group_sid']);
            $group_name=trim($arry_group['group_name']);
        ?>
            var group_sid='<?php echo $group_sid;?>';
            arry_group.push(group_sid);
        <?php endforeach;?>

        var otbls=document.getElementsByTagName('TABLE');
        for(var i=0;i<otbls.length;i++){
            var otbl=otbls[i];
            if(in_array(otbl.id,arry_group)){
                otbl.style.border='1px solid #cccccc';
                otbl.style.backgroundColor="#dbdbdb";
                otbl.setAttribute('focus','no');
            }
        }
        if(_group_sid!=='tbl_no_group'){
            var otbl_no_group=document.getElementById('tbl_no_group');
            otbl_no_group.style.border='1px solid #cccccc';
            otbl_no_group.style.backgroundColor="#dbdbdb";
            otbl_no_group.setAttribute('focus','no');
        }

        //設定目標
        var obj=document.getElementById(_group_sid);
        obj.style.border='1px solid #cccccc';
        obj.style.backgroundColor="#cc0000";
        obj.setAttribute('focus','yes');

        //顯示取消紅框鎖定之按鈕
        oBtnU.style.display="";
    }

    function unset_tbl_focus(obj){
        var arry_group=[];
        <?php foreach($arrys_group as $inx=>$arry_group):?>
        <?php
            $group_sid=trim($arry_group['group_sid']);
            $group_name=trim($arry_group['group_name']);
        ?>
            var group_sid='<?php echo $group_sid;?>';
            arry_group.push(group_sid);
        <?php endforeach;?>

        var otbls=document.getElementsByTagName('TABLE');
        for(var i=0;i<otbls.length;i++){
            var otbl=otbls[i];
            if(in_array(otbl.id,arry_group)){
                otbl.style.border='1px solid #cccccc';
                otbl.style.backgroundColor="#dbdbdb";
                otbl.setAttribute('focus','no');
            }
        }

        var otbl_no_group=document.getElementById('tbl_no_group');
        otbl_no_group.style.border='1px solid #cccccc';
        otbl_no_group.style.backgroundColor="#dbdbdb";
        otbl_no_group.setAttribute('focus','no');

        //隱藏按鈕
        obj.style.display="none";
    }

    function dblclick(obj){
        var has_find_focus=false;
        var arry_group=[];
        <?php foreach($arrys_group as $inx=>$arry_group):?>
        <?php
            $group_sid=trim($arry_group['group_sid']);
            $group_name=trim($arry_group['group_name']);
        ?>
            var group_sid='<?php echo $group_sid;?>';
            arry_group.push(group_sid);
        <?php endforeach;?>

        var otbls=document.getElementsByTagName('TABLE');
        for(var i=0;i<otbls.length;i++){
            var otbl=otbls[i];
            if(in_array(otbl.id,arry_group)){
                var _focus=otbl.getAttribute('focus');
                if(_focus==='yes'){
                    has_find_focus=true;

                    //設置附加元素
                    var _html_tbl='';
                    _html_tbl+='';
                    _html_tbl+='<table id='+obj.id+' cellpadding="0" cellspacing="0" border="3" width="80px" align="center" bgcolor="#ffff99" style="position:relative;margin:2px;left:1px;border-color:#dbdbdb" draggable="true" ondragstart="dragstart(this,event)" ondblclick="dblclick(this);" onmouseover="_mouseover(this);">';
                        _html_tbl+='<tr align="center" valign="middle">';
                            _html_tbl+='<td height="20px">';
                                _html_tbl+=json_arrys_users_info[obj.id];
                                //_html_tbl+=obj.id;
                            _html_tbl+='</td>';
                        _html_tbl+='</tr>';
                    _html_tbl+='</table>';

                    //附加
                    $(otbl).append(_html_tbl);

                    //移除舊有元素
                    obj.remove();
                }
            }
        }

        if(!has_find_focus){
            var otbl_no_group=document.getElementById('tbl_no_group');
            var _focus=otbl_no_group.getAttribute('focus');
            if(_focus==='yes'){
                //設置附加元素
                var _html_tbl='';
                _html_tbl+='';
                _html_tbl+='<table id='+obj.id+' cellpadding="0" cellspacing="0" border="3" width="80px" align="left" bgcolor="#ffff99" style="position:relative;margin:2px;left:1px;border-color:#dbdbdb" draggable="true" ondragstart="dragstart(this,event)" ondblclick="dblclick(this);" onmouseover="_mouseover(this);">';
                    _html_tbl+='<tr align="center" valign="middle">';
                        _html_tbl+='<td height="20px">';
                            _html_tbl+=json_arrys_users_info[obj.id];
                            //_html_tbl+=obj.id;
                        _html_tbl+='</td>';
                    _html_tbl+='</tr>';
                _html_tbl+='</table>';

                //附加
                $(otbl_no_group).append(_html_tbl);

                //移除舊有元素
                obj.remove();
            }else{
                alert('請選擇組別名稱的黑框框當作標的物 !');
                return false;
            }
        }
    }

    oBtnS.onclick=function(){
    //儲存
        var arry_group=[];
        <?php foreach($arrys_group as $inx=>$arry_group):?>
        <?php
            $group_sid=trim($arry_group['group_sid']);
            $group_name=trim($arry_group['group_name']);
        ?>
            var group_sid='<?php echo $group_sid;?>';
            arry_group.push(group_sid);
        <?php endforeach;?>

        var otbls=document.getElementsByTagName('TABLE');
        for(var i=0;i<otbls.length;i++){
            var otbl=otbls[i];
            if(in_array(otbl.id,arry_group)){
                var otbl_users=otbl.getElementsByTagName('TABLE');
                var otextarea=otbl.getElementsByTagName('textarea')[0];
                otextarea.value='';
                for(var j=0;j<otbl_users.length;j++){
                    var ouser=otbl_users[j];
                    otextarea.value+=ouser.id+",";
                }
            }
        }
        if(confirm('你確定要儲存嗎?')){
            oForm1.action='mtA.php'
            oForm1.submit();
            return true;
        }else{
            return false;
        }
    }

    oBtnB.onclick=function(){
    //返回

        var url ='';
        var page=str_repeat('../',1)+'index.php';
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

    function add(){
    //新增
        var group_name=prompt("請輸入組別名稱!");
        var arry_err=[];

        if(!group_name){
            return false;
        }else{
            if(trim(group_name)==''){
                arry_err.push('請輸入組別名稱!');
            }
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            var url ='';
            var page=str_repeat('../',0)+'add/addA.php';
            var arg ={
                'group_name':group_name,
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
    }

    function _mouseover(obj){
        obj.style.cursor='pointer';
    }
    function dragstart(target, e){
        e.dataTransfer.setData('Text', target.id);
    }
    function dragover(target, e){
        if(e.preventDefault){
            e.preventDefault();
        }
    }
    function drop(target, e){
        var id = e.dataTransfer.getData('Text');
        target.appendChild(document.getElementById(id));

        //標記移動物件
        document.getElementById(id).bgColor="#ffff99"
        if(e.preventDefault){
            e.preventDefault();
        }
    }

</script>
</Body>
</Html>

<?php
//-------------------------------------------------------
//page_ok 區塊 -- 結束
//-------------------------------------------------------
?>
<?php };?>