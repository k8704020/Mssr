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

                    APP_ROOT.'lib/php/net/code',
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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_over_school_view');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //SESSION
        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用

        $sess_user_id   =(int)$sess_login_info['uid'];
        $sess_permission=trim($sess_login_info['permission']);
        $sess_class_code=trim($sess_login_info['arrys_class_code'][0]['class_code']);

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        $sess_user_id=(int)$sess_user_id;
        $sess_permission=mysql_prep(trim($sess_permission));
        $sess_class_code=mysql_prep(trim($sess_class_code));

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //串接SQL
    //---------------------------------------------------

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //---------------------------------------------------
        //更新是否開放跨校瀏覽條件
        //---------------------------------------------------

            //---------------------------------------------------
            //更新 mssr_auth_class
            //---------------------------------------------------

                $sess_class_code =addslashes($sess_class_code);
                $over_school_view='yes';

                $sql="
                    SELECT
                        `mssr`.`mssr_auth_class`.`auth`
                    FROM `mssr`.`mssr_auth_class`
                    WHERE 1=1
                        AND `mssr`.`mssr_auth_class`.`class_code`='{$sess_class_code}'
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(!empty($db_results)){

                    //權限資訊
                    if(false===@unserialize($db_results[0]['auth'])){
                        $auth=array();
                    }else{
                        $auth=@unserialize($db_results[0]['auth']);
                    }

                    if((!empty($auth))&&(isset($auth['over_school_view']))){
                        $over_school_view=trim(($auth['over_school_view']));
                    }else{
                        $auth['over_school_view']=$over_school_view;
                        $auth=serialize($auth);

                        $sql="
                            UPDATE `mssr_auth_class` SET
                                `auth`='{$auth}'
                            WHERE 1=1
                                AND `class_code`='{$sess_class_code}'
                            LIMIT 1;
                        ";
                        $conn_mssr->exec($sql);
                    }
                }else{
                    $create_by   =(int)$sess_user_id;
                    $edit_by     =(int)$sess_user_id;
                    $class_code  =addslashes(trim($sess_class_code));
                    $auth        =serialize(array('over_school_view'=>'yes'));
                    $keyin_cdate ='NOW()';
                    $keyin_mdate ='NULL';
                    $keyin_ip    =get_ip();

                    $sql="
                        INSERT INTO `mssr`.`mssr_auth_class` SET
                            `create_by`  = {$create_by  } ,
                            `edit_by`    = {$edit_by    } ,
                            `class_code` ='{$class_code }',
                            `auth`       ='{$auth       }',
                            `keyin_cdate`= {$keyin_cdate} ,
                            `keyin_mdate`= {$keyin_mdate} ,
                            `keyin_ip`   ='{$keyin_ip   }'
                    ";
                    $conn_mssr->exec($sql);
                }

            //---------------------------------------------------
            //更新 mssr_auth_user
            //---------------------------------------------------

                $sess_user_id=(int)$sess_user_id;

                $sql="
                    SELECT
                        `mssr`.`mssr_auth_user`.`auth`
                    FROM `mssr`.`mssr_auth_user`
                    WHERE 1=1
                        AND `mssr`.`mssr_auth_user`.`user_id`={$sess_user_id}
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(!empty($db_results)){

                    //權限資訊
                    if(false===@unserialize($db_results[0]['auth'])){
                        $auth=array();
                    }else{
                        $auth=@unserialize($db_results[0]['auth']);
                    }

                    if((!empty($auth))&&(isset($auth['over_school_view']))){
                        $over_school_view=trim(($auth['over_school_view']));
                    }else{
                        $auth['over_school_view']=$over_school_view;
                        $auth=serialize($auth);

                        $sql="
                            UPDATE `mssr_auth_user` SET
                                `auth`='{$auth}'
                            WHERE 1=1
                                AND `user_id`={$sess_user_id}
                            LIMIT 1;
                        ";
                        $conn_mssr->exec($sql);
                    }
                }else{
                    $create_by   =(int)$sess_user_id;
                    $edit_by     =(int)$sess_user_id;
                    $user_id     =(int)$sess_user_id;
                    $auth        =serialize(array('over_school_view'=>'yes'));
                    $keyin_cdate ='NOW()';
                    $keyin_mdate ='NULL';
                    $keyin_ip    =get_ip();

                    $sql="
                        INSERT INTO `mssr`.`mssr_auth_user` SET
                            `create_by`  = {$create_by  } ,
                            `edit_by`    = {$edit_by    } ,
                            `user_id`    = {$user_id    } ,
                            `auth`       ='{$auth       }',
                            `keyin_cdate`= {$keyin_cdate} ,
                            `keyin_mdate`= {$keyin_mdate} ,
                            `keyin_ip`   ='{$keyin_ip   }'
                    ";
                    $conn_mssr->exec($sql);
                }

        //---------------------------------------------------
        //是否開放跨校瀏覽條件
        //---------------------------------------------------

            $query_sql="
                SELECT
                    `mssr`.`mssr_auth_class`.`auth`,
                    `mssr`.`mssr_auth_class`.`keyin_mdate`
                FROM `mssr`.`mssr_auth_class`
                WHERE 1=1
                    AND `mssr`.`mssr_auth_class`.`class_code`='{$sess_class_code}'
            ";
            //echo $query_sql;
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(0,1),$arry_conn_mssr);

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

        if(!empty($arrys_result)){
            $numrow=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
            $numrow=count($numrow);
        }else{
            $numrow=db_result($conn_type='pdo',$conn_user,$query_sql,array(),$arry_conn_user);
            $numrow=count($numrow);
        }

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
                    <div id="qform">
                        <span id="qform1"></span>
                    </div>
                    <!-- 查詢表單列 結束 -->
                </td>
            </tr>
            <tr>
                <td width="760px" colspan="2">
                    <!-- 資料列表 開始 -->
                    <?php
                        if($numrow!==0){
                            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx-1,$psize),$arry_conn_mssr);
                            page_hrs($title);
                        }else{
                            die();
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
<?php //echo fast_area($rd=2);?>
<!-- 快速切換區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    $(function(){

        //快速切換設置
        //fast_area_config('#fast_area',0,0);

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
        global $APP_ROOT;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $arrys_result;
        global $sess_login_info;
        global $sess_class_code;
        global $conn_user;
        global $conn_mssr;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0;  //欄位個數
        $btn_nos=0;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        //權限資訊
        $over_school_view='no';
        $arry_over_school_view=array(
            "yes"=>"班級開放跨校瀏覽",
            "no" =>"班級禁止跨校瀏覽"
        );
        if(false===@unserialize($arrys_result[0]['auth'])){
            $auth=array();
        }else{
            $auth=@unserialize($arrys_result[0]['auth']);
        }
        if((!empty($auth))&&(isset($auth['over_school_view']))){
            $over_school_view=trim(($auth['over_school_view']));
        }
?>
<!-- 條件資訊  開始 -->
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table_style2" style="position:relative;top:20px;"/>
    <tr align="center" valign="middle" class="bg_gray1 fc_white0">
        <td height="30px" colspan="4">
            書店開放跨校瀏覽條件
        </td>
    </tr>
    <tr align="left" valign="middle">
        <td width="200px" height="50px">
            <span style="position:relative;left:10px;" class="fc_gray0">老師姓名：</span>
            <span style="position:relative;left:5px;" class="fc_gray0">
                <?php echo htmlspecialchars($sess_login_info['name']);?>
            </span>
        </td>
        <td width="305px" height="50px">
            <span style="position:relative;left:10px;" class="fc_gray0">書店開放跨校瀏覽條件：</span>
            <span style="position:relative;left:5px;" class="fc_blue0">
                <?php echo htmlspecialchars($arry_over_school_view[$over_school_view]);?>
            </span>
        </td>
        <td width="" height="50px">
            <span style="position:relative;left:10px;" class="fc_gray0">最後設定日期：</span>
            <span style="position:relative;left:5px;" class="fc_gray0">
                <?php echo htmlspecialchars(date('Y-m-d', strtotime($arrys_result[0]['keyin_mdate'])));?>
            </span>
        </td>
    </tr>
</table>
<!-- 條件資訊  結束 -->

<!-- 設定條件資訊  開始 -->
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table_style2" style="position:relative;top:40px;"/>
    <tr align="left" valign="middle">
        <td width="100%" height="100px" align="center">
            重新設定：
            <select id="over_school_view" name="over_school_view" class="form_select" tabindex="" style="position:relative;"
            onchange="edit(this);void(0);">
                <option value="yes" <?php if($over_school_view==="yes")echo 'selected';?>><?php echo $arry_over_school_view['yes'];?>
                <option value="no" <?php if($over_school_view==="no")echo 'selected';?>><?php echo $arry_over_school_view['no'];?>
            </select>
        </td>
    </tr>
</table>
<!-- 設定條件資訊  結束 -->

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

    function edit(obj){
    //修改條件
        var url ='';
        var page=str_repeat('../',0)+'edit/editA.php';
        var arg ={
            'psize':psize,
            'pinx' :pinx,
            'over_school_view':encodeURI(obj.value),
            'class_code':encodeURI('<?php echo $sess_class_code;?>')
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
<script type="text/javascript" src="../../../../inc/external/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//呼叫使用者頁面紀錄
//-------------------------------------------------------
    user_page_log(rd=4);
</script>