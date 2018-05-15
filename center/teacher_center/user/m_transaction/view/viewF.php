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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_transaction');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //mode      觀看模式
    //user_id   使用者主索引

        $get_chk=array(
            'mode   ',
            'user_id'
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
    //mode      觀看模式
    //user_id   使用者主索引

        //GET
        $mode   =trim($_GET[trim('mode   ')]);
        $user_id=trim($_GET[trim('user_id')]);

        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //mode      觀看模式
    //user_id   使用者主索引

        $arry_err=array();

        if($mode===''){
           $arry_err[]='觀看模式,未輸入!';
        }else{
            $mode=trim($mode);
            if(!in_array($mode,array('map','box','tx'))){
                $arry_err[]='觀看模式,錯誤!';
            }
        }

        if($user_id===''){
           $arry_err[]='使用者主索引,未輸入!';
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $arry_err[]='使用者主索引,錯誤!';
            }
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

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //mode      觀看模式
        //user_id   使用者主索引

            $mode   =mysql_prep($mode);
            $user_id=(int)$user_id;

            //-------------------------------------------
            //檢核學生資料
            //-------------------------------------------

                $arry_user_info=get_user_info($conn='',$user_id,$array_filter=array('name'),$arry_conn_user);
                if(empty($arry_user_info)){
                    die();
                }else{
                    $user_name=trim($arry_user_info[0]['name']);
                }

            //-------------------------------------------
            //SQL撈取
            //-------------------------------------------

                switch($mode){
                    case 'map':
                        $sql="
                            SELECT
                                `user`.`member`.`name`,

                                `mssr`.`mssr_user_info`.`user_id`,
                                `mssr`.`mssr_user_info`.`map_item`,
                                `mssr`.`mssr_user_info`.`box_item`,
                                `mssr`.`mssr_user_info`.`user_coin`
                            FROM `user`.`member`
                                INNER JOIN `mssr`.`mssr_user_info` ON
                                `user`.`member`.`uid`=`mssr`.`mssr_user_info`.`user_id`
                            WHERE 1=1
                                AND  `user`.`member`.`uid` IN ({$user_id})
                            GROUP BY `mssr`.`mssr_user_info`.`user_id`
                            ORDER BY `user_id` ASC
                            LIMIT 1
                        ";
                    break;

                    case 'box':
                        $sql="
                            SELECT
                                `user`.`member`.`name`,

                                `mssr`.`mssr_user_info`.`user_id`,
                                `mssr`.`mssr_user_info`.`map_item`,
                                `mssr`.`mssr_user_info`.`box_item`,
                                `mssr`.`mssr_user_info`.`user_coin`
                            FROM `user`.`member`
                                INNER JOIN `mssr`.`mssr_user_info` ON
                                `user`.`member`.`uid`=`mssr`.`mssr_user_info`.`user_id`
                            WHERE 1=1
                                AND  `user`.`member`.`uid` IN ({$user_id})
                            GROUP BY `mssr`.`mssr_user_info`.`user_id`
                            ORDER BY `user_id` ASC
                            LIMIT 1
                        ";
                    break;

                    case 'tx':
                        $sql="
                            SELECT
                                `mssr`.`mssr_tx_sys_log`.`tx_sid`,
                                `mssr`.`mssr_tx_sys_log`.`tx_item`,
                                `mssr`.`mssr_item`.`item_name`,
                                `mssr`.`mssr_tx_sys_log`.`tx_coin`,

                                `mssr`.`mssr_user_item_log`.`tx_type`,
                                `mssr`.`mssr_user_item_log`.`map_item`,
                                `mssr`.`mssr_user_item_log`.`box_item`,
                                `mssr`.`mssr_user_item_log`.`log_note`,

                                `mssr`.`mssr_tx_sys_log`.`keyin_mdate`,
                                `mssr`.`mssr_tx_sys_log`.`keyin_ip`
                            FROM  `mssr`.`mssr_tx_sys_log`
                                LEFT JOIN `mssr`.`mssr_item` ON
                                ABS(`mssr`.`mssr_tx_sys_log`.`tx_item`)=`mssr`.`mssr_item`.`item_id`

                                INNER JOIN `mssr`.`mssr_user_item_log` ON
                                `mssr`.`mssr_tx_sys_log`.`tx_sid`=`mssr`.`mssr_user_item_log`.`tx_sid`
                            WHERE  1=1
                                AND `mssr`.`mssr_tx_sys_log`.`user_id`={$user_id}

                        UNION ALL

                            SELECT
                                `mssr`.mssr_tx_gift_log.`tx_sid`,
                                `mssr`.mssr_tx_gift_log.`tx_item`,
                                `mssr`.`mssr_item`.`item_name`,
                                `mssr`.mssr_tx_gift_log.`tx_coin`,

                                `mssr`.`mssr_user_item_log`.`tx_type`,
                                `mssr`.`mssr_user_item_log`.`map_item`,
                                `mssr`.`mssr_user_item_log`.`box_item`,
                                `mssr`.`mssr_user_item_log`.`log_note`,

                                `mssr`.mssr_tx_gift_log.`keyin_mdate`,
                                `mssr`.mssr_tx_gift_log.`keyin_ip`
                            FROM  `mssr`.mssr_tx_gift_log
                                LEFT JOIN `mssr`.`mssr_item` ON
                                ABS(`mssr`.mssr_tx_gift_log.`tx_item`)=`mssr`.`mssr_item`.`item_id`

                                INNER JOIN `mssr`.`mssr_user_item_log` ON
                                `mssr`.mssr_tx_gift_log.`tx_sid`=`mssr`.`mssr_user_item_log`.`tx_sid`
                            WHERE  1=1
                                AND `mssr`.mssr_tx_gift_log.tx_to={$user_id}
                        ORDER BY `keyin_mdate` DESC
                        ";
                    break;

                    default:
                        die();
                    break;
                }
                //echo $sql.'<br/>';
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(empty($arrys_result)){
                    //page_nrs($title);
                }

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

    <script type="text/javascript" src="../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/vaildate/code.js"></script>

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
                        //呼叫頁面
                        if(!empty($arrys_result)){
                            switch($mode){
                                case 'map':
                                    page_hrs_map($title);
                                break;

                                case 'box':
                                    page_hrs_box($title);
                                break;

                                case 'tx':
                                    page_hrs_tx($title);
                                break;

                                default:
                                    die();
                                break;
                            }
                        }else{
                            page_nrs($title);
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
<?php //echo fast_area($rd=4);?>
<!-- 快速切換區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

</script>


<?php function page_hrs_map($title="") {?>
<?php
//-------------------------------------------------------
//page_hrs_map 區塊 -- 開始
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
        global $conn_mssr;
        global $arry_conn_mssr;

        //local
        global $psize;
        global $pinx;
        global $user_name;

        global $arrys_result;

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        global $arrys_login_info;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        extract($arrys_result[0],EXTR_PREFIX_ALL,"rs");

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        $user_name  =trim($user_name);
        $rs_map_item=trim($rs_map_item);

        $arrys_map_item    =array();
        $arry_view_map_item=array();
        $rs_map_item_lists ='';
        if($rs_map_item!==''){
            $arrys_map_item=explode(",",$rs_map_item);
            foreach($arrys_map_item as $inx=>$arry_map_item){
                $inx=(int)$inx;
                if(($inx!==0)&&($inx%3!==0)){
                    unset($arrys_map_item[$inx]);
                }
                if($arry_map_item==='')unset($arrys_map_item[$inx]);
            }
            if(!empty($arrys_map_item)){
                $arrys_map_item_group=array();
                $arrys_map_item_cno  =array();
                foreach($arrys_map_item as $arry_map_item){
                    $item_id=(int)$arry_map_item;
                    $sql="
                        SELECT
                            `mssr`.`mssr_item`.`item_name`
                        FROM `mssr`.`mssr_item`
                        WHERE 1=1
                            AND `mssr`.`mssr_item`.`item_id`={$item_id}
                    ";
                    $arrys_map_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    if(!empty($arrys_map_result)){
                        $rs_item_name=trim($arrys_map_result[0]['item_name']);
                        $arry_view_map_item[$item_id]=$rs_item_name;
                    }
                    if(!in_array($item_id,$arrys_map_item_group)){
                        $arrys_map_item_group[]      =$item_id;
                        $arrys_map_item_cno[$item_id]=1;
                    }else{
                        $arrys_map_item_cno[$item_id]++;
                    }
                }
                if(!empty($arry_view_map_item)){
                    $rs_map_item_lists=implode(", ",$arry_view_map_item);
                }
            }
        }
?>
<!-- 內容 開始 -->
<form id='Form1' name='Form1' method='post' onsubmit="return false;">
    <table align="center" border="0" width="700px" class="table_style0" style="position:relative;top:30px;">
        <tr>
            <td height="30px" class="fc_blue0" colspan="1">
                <?php echo htmlspecialchars($user_name);?> - 放置在大地圖中的物品
            </td>
        </tr>
        <tr class="fc_gray0">
            <td align="left" valign='top' width="" height="150px" class="b_line gr_dashed">
                <?php //echo htmlspecialchars($rs_map_item_lists);?>
                <?php
                    if(!empty($arrys_map_item_cno)){
                        foreach($arrys_map_item_cno as $item_id=>$cno){
                            $item_id=(int)$item_id;
                            $cno    =(int)$cno;

                            //查找 圖片
                            $has_item_img =false;
                            $root         =str_repeat("../",5)."service/bookstore_v2/bookstore_courtyard/img";
                            $bimg_path    ="{$root}/{$item_id}.png";
                            $simg_path    ="{$root}/{$item_id}.png";
                            $bimg_path_enc=mb_convert_encoding($bimg_path,$fso_enc,$page_enc);
                            $simg_path_enc=mb_convert_encoding($simg_path,$fso_enc,$page_enc);
                            if(file_exists($bimg_path_enc)&&file_exists($simg_path_enc))$has_item_img=true;

                            if($has_item_img){
                                $html ="";
                                $html.="
                                    <table align='left' cellpadding='0' cellspacing='0' border='0' width='55px' style='position:relative;margin:15px 0px;'/>
                                        <tr><td>
                                            <img src='{$bimg_path}' width='55px' height='55px' border='0'
                                            title='{$arry_view_map_item[$item_id]}' alt='{$arry_view_map_item[$item_id]}'
                                            style='margin:0 10px;'/>
                                        </td></tr>
                                        <tr><td align='center'>
                                            $arry_view_map_item[$item_id]
                                        </td></tr>
                                        <tr><td align='center' height='20px'>
                                            數量：$cno
                                        </td></tr>
                                    </table>
                                ";
                                echo $html;
                            }
                        }
                    }
                ?>
            </td>
        </tr>

        <tr>
            <td align="right" colspan="2">
                <!-- hidden -->
                <input type="hidden" id="psize" name="psize" value="<?php echo addslashes($psize);?>">
                <input type="hidden" id="pinx" name="pinx" value="<?php echo addslashes($pinx);?>">

                <!-- <input type="button" id="BtnS" name="BtnS" value="送出" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="6" onmouseover="this.style.cursor='pointer'"> -->
                <input type="button" id="BtnB" name="BtnB" value="返回" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="7" onmouseover="this.style.cursor='pointer'">
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

    var oForm1=document.getElementById('Form1');    //表單
    var oBtnS =document.getElementById('BtnS');     //送出
    var oBtnB =document.getElementById('BtnB');     //返回

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

</script>

<?php
//-------------------------------------------------------
//page_hrs_map 區塊 -- 結束
//-------------------------------------------------------
?>
<?php };?>


<?php function page_hrs_box($title="") {?>
<?php
//-------------------------------------------------------
//page_hrs_box 區塊 -- 開始
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
        global $conn_mssr;
        global $arry_conn_mssr;

        //local
        global $psize;
        global $pinx;
        global $user_name;

        global $arrys_result;

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        global $arrys_login_info;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        extract($arrys_result[0],EXTR_PREFIX_ALL,"rs");

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        $user_name  =trim($user_name);

        $arrys_box_item    =array();
        $arry_view_box_item=array();
        $arry_cno_box_item =array();
        if($rs_box_item!==''){
            $arrys_box_item=explode(",",$rs_box_item);
            foreach($arrys_box_item as $inx=>$box_item){
                if($box_item==='')unset($arrys_box_item[$inx]);
            }
            if(!empty($arrys_box_item)){
                foreach($arrys_box_item as $inx=>$box_item){
                    if($inx%2===0){
                        $box_item=(int)$box_item;
                        $sql="
                            SELECT
                                `mssr`.`mssr_item`.`item_name`
                            FROM `mssr`.`mssr_item`
                            WHERE 1=1
                                AND `mssr`.`mssr_item`.`item_id`={$box_item}
                        ";
                        $arrys_box_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(!empty($arrys_box_result)){
                            $rs_item_name=trim($arrys_box_result[0]['item_name']);
                            $arry_view_box_item[$box_item]=trim($rs_item_name);
                        }
                    }
                    if($inx<count($arrys_box_item)-1){
                        $arry_cno_box_item[$box_item]=$arrys_box_item[$inx+1];
                    }
                }
            }
        }
?>
<!-- 內容 開始 -->
<form id='Form1' name='Form1' method='post' onsubmit="return false;">
    <table align="center" border="0" width="700px" class="table_style0" style="position:relative;top:30px;">
        <tr>
            <td height="30px" class="fc_blue0" colspan="1">
                <?php echo htmlspecialchars($user_name);?> - 放置在物品欄中的物品
            </td>
        </tr>
        <tr class="fc_gray0">
            <td align="left" valign='top' width="" height="150px" class="b_line gr_dashed">
                <?php
                    if(!empty($arrys_box_item)){
                        foreach($arrys_box_item as $inx=>$box_item){
                            $item_id=(int)$box_item;

                            //查找 圖片
                            if($inx%2===0){
                                $has_item_img =false;
                                $root         =str_repeat("../",5)."service/bookstore_v2/bookstore_courtyard/img";
                                $bimg_path    ="{$root}/{$item_id}.png";
                                $simg_path    ="{$root}/{$item_id}.png";
                                $bimg_path_enc=mb_convert_encoding($bimg_path,$fso_enc,$page_enc);
                                $simg_path_enc=mb_convert_encoding($simg_path,$fso_enc,$page_enc);
                                if(file_exists($bimg_path_enc)&&file_exists($simg_path_enc))$has_item_img=true;

                                if($has_item_img){
                                    $html ="<table align='left' cellpadding='0' cellspacing='0' border='0' width='55px' style='position:relative;margin:15px 0px;'/>";
                                    $html.="
                                        <tr><td>
                                            <img src='{$bimg_path}' width='55px' height='55px' border='0'
                                            title='{$arry_view_box_item[$item_id]}' alt='{$arry_view_box_item[$item_id]}'
                                            style='margin:0 10px;'/>
                                        </td></tr>
                                        <tr><td align='center'>
                                            $arry_view_box_item[$item_id]
                                        </td></tr>
                                        <tr><td align='center' height='20px'>
                                            數量：$arry_cno_box_item[$item_id]
                                        </td></tr>
                                    ";
                                    $html.="</table>";
                                    echo $html;
                                }
                            }
                        }
                    }
                ?>
            </td>
        </tr>

        <tr>
            <td align="right" colspan="2">
                <!-- hidden -->
                <input type="hidden" id="psize" name="psize" value="<?php echo addslashes($psize);?>">
                <input type="hidden" id="pinx" name="pinx" value="<?php echo addslashes($pinx);?>">

                <!-- <input type="button" id="BtnS" name="BtnS" value="送出" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="6" onmouseover="this.style.cursor='pointer'"> -->
                <input type="button" id="BtnB" name="BtnB" value="返回" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="7" onmouseover="this.style.cursor='pointer'">
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

    var oForm1=document.getElementById('Form1');    //表單
    var oBtnS =document.getElementById('BtnS');     //送出
    var oBtnB =document.getElementById('BtnB');     //返回

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

</script>

<?php
//-------------------------------------------------------
//page_hrs_box 區塊 -- 結束
//-------------------------------------------------------
?>
<?php };?>


<?php function page_hrs_tx($title="") {?>
<?php
//-------------------------------------------------------
//page_hrs_tx 區塊 -- 開始
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
        global $conn_mssr;
        global $arry_conn_mssr;

        //local
        global $psize;
        global $pinx;
        global $user_name;

        global $arrys_result;

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

        $user_name  =trim($user_name);

        $arry_tx_type_cname=array(
            trim('teacher_incentive ')=>trim('導師直接獎懲  '),
            trim('book_recode       ')=>trim('錄音獎勵      '),
            trim('book_draw         ')=>trim('繪圖獎勵      '),
            trim('book_draw_up      ')=>trim('繪圖上傳獎勵  '),
            trim('book_text         ')=>trim('文字獎勵      '),
            trim('branch_bonus      ')=>trim('分店紅利      '),
            trim('branch_open       ')=>trim('分店開啟      '),
            trim('branch_task_1     ')=>trim('分店任務1     '),
            trim('branch_task_2     ')=>trim('分店任務2     '),
            trim('branch_task_3     ')=>trim('分店任務3     '),
            trim('branch_task_4     ')=>trim('分店任務4     '),
            trim('branch_task_5     ')=>trim('分店任務5     '),
            trim('buy               ')=>trim('購買          '),
            trim('buy_book          ')=>trim('訂閱並登記    '),
            trim('gift              ')=>trim('送禮          '),
            trim('sell              ')=>trim('賣出          '),
            trim('sys               ')=>trim('導師指導      '),
            trim('teacher_comment   ')=>trim('導師指導      '),
            trim('visit             ')=>trim('拜訪          '),
            trim('cancel            ')=>trim('交易取消      ')
        );
?>
<!-- 內容 開始 -->
<style>
    body{
        overflow-x: hidden;
    }
</style>
<form id='Form1' name='Form1' method='post' onsubmit="return false;">
    <table align="center" border="0" width="740px" class="table_style0" style="position:relative;top:30px;">
        <tr>
            <td height="30px" class="fc_blue0" colspan="6">
                <?php echo htmlspecialchars($user_name);?> - 交易紀錄表
            </td>
        </tr>
        <tr class="fc_gray0">
            <!-- <td align="center" valign='middle' width="" height="35px" class="b_line gr_dashed">交易編號</td> -->
            <td align="center" valign='middle' width="" height="35px" class="b_line gr_dashed">交易物品</td>
            <td align="center" valign='middle' width="" height="35px" class="b_line gr_dashed">交易金額</td>
            <td align="center" valign='middle' width="" height="35px" class="b_line gr_dashed">交易類型</td>
            <td align="center" valign='middle' width="" height="35px" class="b_line gr_dashed">交易時間</td>
            <td align="center" valign='middle' width="" height="35px" class="b_line gr_dashed">備註    </td>
            <!-- <td align="center" valign='middle' width="" height="35px" class="b_line gr_dashed">交易IP  </td> -->
            <td align="center" valign='middle' width="" height="35px" class="b_line gr_dashed">功能</td>
        </tr>
        <?php if(!empty($arrys_result)):?>
            <?php foreach($arrys_result as $arry_result):?>
            <?php
            //tx_sid
            //tx_item
            //item_name
            //tx_coin
            //tx_type
            //map_item
            //box_item
            //keyin_mdate
            //keyin_ip

                extract($arry_result,EXTR_PREFIX_ALL,"rs");

                $rs_tx_sid      =trim($rs_tx_sid     );
                $rs_tx_item     =trim($rs_tx_item    );
                $rs_item_name   =trim($rs_item_name  );
                $rs_tx_coin     =(int)($rs_tx_coin   );
                $rs_tx_type     =trim($rs_tx_type    );
                $rs_map_item    =trim($rs_map_item   );
                $rs_box_item    =trim($rs_box_item   );
                $rs_log_note    =trim($rs_log_note   );
                $rs_keyin_mdate =trim($rs_keyin_mdate);
                $rs_keyin_ip    =trim($rs_keyin_ip   );
            ?>
            <tr class="fc_gray0">
                <!-- <td align="center" valign='middle' width="" height="35px" class="gr_dashed"><?php echo htmlspecialchars($rs_tx_sid);?></td> -->
                <td align="center" valign='middle' width="" height="35px" class="gr_dashed"><?php echo htmlspecialchars($rs_item_name);?></td>
                <td align="center" valign='middle' width="" height="35px" class="gr_dashed">
                    <?php
                        if((int)$rs_tx_coin!==0){
                            switch (abs((int)$rs_tx_coin)/(int)$rs_tx_coin){
                                case 1:
                                    echo '得到';
                                break;
                                case -1:
                                    echo '失去';
                                break;
                            }
                            echo str_replace('-','',$rs_tx_coin);
                        }else{
                            echo $rs_tx_coin;
                        }
                    ?>元
                </td>
                <td align="center" valign='middle' width="" height="35px" class="gr_dashed">
                    <?php
                        if($arry_tx_type_cname[$rs_tx_type]){
                            echo htmlspecialchars($arry_tx_type_cname[$rs_tx_type]);
                        }else{
                            echo htmlspecialchars($rs_tx_type);
                        }
                    ?>
                </td>
                <td align="center" valign='middle' width="" height="35px" class="gr_dashed"><?php echo htmlspecialchars($rs_keyin_mdate);?></td>
                <td align="center" valign='middle' width="" height="35px" class="gr_dashed"><?php echo htmlspecialchars($rs_log_note);?></td>
                <td align="center" valign='middle' width="" height="35px" class="gr_dashed">

                    <?php if(in_array($rs_tx_type,array('buy','sell'))):?>
                        <input type="button" value="取消" class="ibtn_gr3020" style="margin:0px 0px;" tabindex="" onmouseover="this.style.cursor='pointer'"
                        onclick="edit('<?php echo addslashes($rs_tx_sid);?>');">
                    <?php endif;?>

                </td>
            </tr>
            <?php endforeach;?>
        <?php endif;?>

        <tr>
            <td align="right" colspan="6">
                <!-- hidden -->
                <input type="hidden" id="psize" name="psize" value="<?php echo addslashes($psize);?>">
                <input type="hidden" id="pinx" name="pinx" value="<?php echo addslashes($pinx);?>">

                <!-- <input type="button" id="BtnS" name="BtnS" value="送出" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="6" onmouseover="this.style.cursor='pointer'"> -->
                <input type="button" id="BtnB" name="BtnB" value="返回" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="7" onmouseover="this.style.cursor='pointer'">
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

    var oForm1=document.getElementById('Form1');    //表單
    var oBtnS =document.getElementById('BtnS');     //送出
    var oBtnB =document.getElementById('BtnB');     //返回

    function edit(tx_sid){

        var tx_sid  =trim(tx_sid);
        var url     ='';
        var page    =str_repeat('../',1)+'edit/editA.php';
        var arg     ={
            'psize' :psize,
            'pinx'  :pinx,
            'tx_sid':tx_sid
        };
        var _arg    =[];
        for(var key in arg){
            _arg.push(key+"="+encodeURI(arg[key]));
        }
        arg=_arg.join("&");

        if(arg.length!=0){
            url+=page+"?"+arg;
        }else{
            url+=page;
        }

        if(confirm('你確定要取消這筆交易紀錄嗎?')){
            go(url,'self');
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

</script>

<?php
//-------------------------------------------------------
//page_hrs_tx 區塊 -- 結束
//-------------------------------------------------------
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
        global $conn_mssr;
        global $arry_conn_mssr;

        //local
        global $psize;
        global $pinx;
        global $user_name;

        global $arrys_result;

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

        $user_name  =trim($user_name);
?>
<!-- 內容 開始 -->
<form id='Form1' name='Form1' method='post' onsubmit="return false;">
    <table align="center" border="0" width="750px" class="table_style0" style="position:relative;top:30px;">
        <tr>
            <td height="30px" class="fc_blue0" colspan="1">
                <?php echo htmlspecialchars($user_name);?> - 交易紀錄表
            </td>
        </tr>
        <tr class="fc_gray0">
            <td align="center" valign='middle' width="" height="250px" class="b_line gr_dashed">
                目前系統無資料!
            </td>
        </tr>

        <tr>
            <td align="right" colspan="1">
                <!-- hidden -->
                <input type="hidden" id="psize" name="psize" value="<?php echo addslashes($psize);?>">
                <input type="hidden" id="pinx" name="pinx" value="<?php echo addslashes($pinx);?>">

                <!-- <input type="button" id="BtnS" name="BtnS" value="送出" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="6" onmouseover="this.style.cursor='pointer'"> -->
                <input type="button" id="BtnB" name="BtnB" value="返回" class="ibtn_gr6030" style="margin:10px 0px;" tabindex="7" onmouseover="this.style.cursor='pointer'">
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

    var oForm1=document.getElementById('Form1');    //表單
    var oBtnS =document.getElementById('BtnS');     //送出
    var oBtnB =document.getElementById('BtnB');     //返回

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

</script>

<?php
//-------------------------------------------------------
//page_nrs 區塊 -- 結束
//-------------------------------------------------------
?>
<?php };?>