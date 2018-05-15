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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_transaction');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //SESSION
        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用

        if(isset($_SESSION['m_transaction']['filter'])){
            $filter=$_SESSION['m_transaction']['filter'];
        }
        if(isset($_SESSION['m_transaction']['query_fields'])){
            $query_fields=$_SESSION['m_transaction']['query_fields'];
        }

        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);

        if(isset($sess_login_info['arrys_class_code']) && !empty($sess_login_info['arrys_class_code'])){
            $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
            $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
            $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];
        }else{
            $msg="請先選擇年級與班級!";
            $jscript_back="
                <script>
                    alert('{$msg}');
                    history.back(-1);
                </script>
            ";
            die($jscript_back);
        }

    //---------------------------------------------------
    //設定參數
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
        //班級資訊
        //---------------------------------------------------

            //$sql="
            //    SELECT
            //        `semester`.`semester_code`,
            //        `semester`.`semester_year`,
            //        `semester`.`semester_term`,
            //        `class`.`class_code`,
            //        `class`.`grade`,
            //        `class`.`classroom`,
            //        `class`.`class_category`,
            //        `school`.`school_name`,
            //        `class_name`.`class_name`
            //    FROM `teacher`
            //        INNER JOIN `class` ON
            //        `teacher`.`class_code`=`class`.`class_code`
            //        INNER JOIN `semester` ON
            //        `class`.`semester_code`=`semester`.`semester_code`
            //        INNER JOIN `school` ON
            //        `semester`.`school_code`=`school`.`school_code`
            //
            //        INNER JOIN `class_name` ON
            //        `class_name`.`class_category`=`class`.`class_category`
            //    WHERE 1=1
            //        AND CURDATE() BETWEEN `semester`.`start` AND `semester`.`end`
            //        AND `class_name`.`classroom`=`class`.`classroom`
            //        AND `school`.`school_code`='{$sess_school_code}'
            //";
            //$arry_class_info=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
            //echo "<Pre>";
            //print_r($sess_class_code);
            //echo "</Pre>";
            //die();

        //---------------------------------------------------
        //學生陣列
        //---------------------------------------------------

            $users=arrys_users($conn_user,$sess_class_code,$date=date("Y-m-d"),$arry_conn_user);

        //---------------------------------------------------
        //全部的組別陣列
        //---------------------------------------------------

            if($users===''){
                $users=0;
            }

            if($filter!=''){
                $query_sql="
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
                        AND  `user`.`member`.`uid` IN ({$users})
                        -- FILTER在此
                        {$filter}
                    GROUP BY `mssr`.`mssr_user_info`.`user_id`
                    ORDER BY `user_id` ASC
                ";
            }else{
                $query_sql="
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
                        AND  `user`.`member`.`uid` IN ({$users})
                    GROUP BY `mssr`.`mssr_user_info`.`user_id`
                    ORDER BY `user_id` ASC
                ";
            }

    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=0;  //資料總筆數
        $psize =11; //單頁筆數,預設11筆
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
        global $sess_login_info;
        global $arrys_result;
        global $config_arrys;
        global $conn_user;
        global $conn_mssr;
        global $auth_sys_check_lv;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0; //欄位個數
        $btn_nos=0; //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);
        $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
        $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
        $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];
?>
<!-- 資料列表 開始 -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <!-- 在此設定寬高 -->
        <td width="100%" height="250px" align="center" valign="top">
        <!-- 內容 -->

            <table id="mod_data_tbl" border="0" width="100%" cellpadding="5" cellspacing="0" style="margin-top:10px;" class="table_style1">
                <tr align="center" valign="middle" class="bg_gray1 fc_white0">
                    <td width="75px">姓名               </td>
                    <td width="75px">大地圖中的物品     </td>
                    <td width="75px">物品欄中的物品     </td>
                    <td width="60px">總營收             </td>
                    <td width="60px">結餘葵幣           </td>
                    <td width="60px">交易紀錄           </td>
                    <td width="">功能                   </td>
                </tr>

                <?php foreach($arrys_result as $inx=>$arry_result) :?>
                <?php
                //---------------------------------------------------
                //接收欄位
                //---------------------------------------------------
                //name         使用者姓名
                //user_id      使用者主索引
                //map_item     大地圖的物品
                //box_item     物品欄的物品
                //user_coin    擁有葵幣

                    extract($arry_result, EXTR_PREFIX_ALL, "rs");

                //---------------------------------------------------
                //處理欄位
                //---------------------------------------------------

                    //user_id       使用者主索引
                    $rs_user_id=(int)$rs_user_id;

                    //name          使用者姓名
                    $rs_name=trim($rs_name);

                    //map_item      大地圖的物品
                    $rs_map_item=trim($rs_map_item);

                    //box_item      物品欄的物品
                    $rs_box_item=trim($rs_box_item);

                    //user_coin     擁有葵幣
                    $rs_user_coin=(int)$rs_user_coin;

                    ////keyin_cdate         建立時間
                    //$rs_keyin_cdate=date("Y-m-d",strtotime($rs_keyin_cdate));

                //---------------------------------------------------
                //特殊處理
                //---------------------------------------------------

                    //-----------------------------------------------
                    //大地圖的物品(物品id, x座標, y座標)
                    //-----------------------------------------------

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
                                        $arry_view_map_item[]=$rs_item_name;
                                    }
                                }
                                if(!empty($arry_view_map_item)){
                                    $rs_map_item_lists=implode(", ",$arry_view_map_item);
                                    if(mb_strlen($rs_map_item_lists)>15){
                                        $rs_map_item_lists=mb_substr($rs_map_item_lists,0,15)."..";
                                    }
                                }
                            }
                        }

                    //-----------------------------------------------
                    //物品欄的物品(物品id, 數量)
                    //-----------------------------------------------

                        $arrys_box_item    =array();
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
                                            $arrys_box_item[$inx]=trim($rs_item_name);
                                        }
                                    }
                                }
                            }
                        }

                    //-----------------------------------------------
                    //總營收
                    //-----------------------------------------------

                        $revenue=1000;
                        $sql="
                            SELECT
                                `mssr`.`mssr_tx_sys_log`.`tx_coin`
                            FROM  `mssr`.`mssr_tx_sys_log`
                                LEFT JOIN `mssr`.`mssr_item` ON
                                ABS(`mssr`.`mssr_tx_sys_log`.`tx_item`)=`mssr`.`mssr_item`.`item_id`

                                INNER JOIN `mssr`.`mssr_user_item_log` ON
                                `mssr`.`mssr_tx_sys_log`.`tx_sid`=`mssr`.`mssr_user_item_log`.`tx_sid`
                            WHERE  1=1
                                AND `mssr`.`mssr_tx_sys_log`.`user_id`={$rs_user_id}
                            ORDER BY `mssr`.`mssr_tx_sys_log`.`keyin_mdate` DESC
                        ";
                        $arrys_revenue_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        if(!empty($arrys_revenue_result)){
                            foreach($arrys_revenue_result as $arry_revenue_result){
                                $rs_tx_coin=(int)$arry_revenue_result['tx_coin'];
                                if($rs_tx_coin>0){
                                    $revenue+=$rs_tx_coin;
                                }
                            }
                        }
                ?>
                <tr>
                    <td align="center" valign="middle">
                        <?php echo htmlspecialchars($rs_name);?>
                    </td>
                    <td align="center" valign="middle">
                        <?php //echo htmlspecialchars($rs_map_item_lists);?>
                        <?php if($rs_map_item_lists!==''):?>
                            <input type="button" value="詳細" class="ibtn_gr3020" style=""
                            onclick="view('map',<?php echo (int)$rs_user_id;?>);void(0);" onmouseover="this.style.cursor='pointer'">
                        <?php endif;?>
                    </td>
                    <td align="center" valign="middle">
                        <?php if(!empty($arrys_box_item)):?>
                            <!-- <?php $str_len=0;?>
                            <?php foreach($arrys_box_item as $inx=>$box_item):?>
                                <?php if((int)$str_len<8):?>
                                    <?php if($inx%2===0):?>
                                        <?php
                                            $str_len+=(int)mb_strlen($box_item);
                                            echo htmlspecialchars($box_item);
                                        ?>
                                    <?php endif;?>
                                    <?php if($inx%2===1):?>
                                        x
                                        <?php
                                            $str_len+=1;
                                            $str_len+=(int)mb_strlen($box_item);
                                            echo (int)($box_item);
                                            //if(((int)$inx!==(int)count($arrys_box_item-1))){
                                            //    echo ',';
                                            //}
                                        ?>
                                    <?php endif;?>
                                <?php else:?>
                                    <?php echo '...';break;?>
                                <?php endif;?>
                            <?php endforeach;?> -->
                            <input type="button" value="詳細" class="ibtn_gr3020" style=""
                            onclick="view('box',<?php echo (int)$rs_user_id;?>);void(0);" onmouseover="this.style.cursor='pointer'">
                        <?php endif;?>
                    </td>
                    <td align="center" valign="middle">
                        <?php echo (int)$revenue;?>&nbsp;$
                    </td>
                    <td align="center" valign="middle">
                        <?php echo (int)$rs_user_coin;?>&nbsp;$
                    </td>

                    <td align="center" valign="middle">
                        <input type="button" value="詳細" class="ibtn_gr3020" style=""
                        onclick="view('tx',<?php echo (int)$rs_user_id;?>);void(0);" onmouseover="this.style.cursor='pointer'">
                    </td>
                    <td align="center" valign="middle" style="border:1px solid #c6c6c6;">
                        <select id="comment_coin_<?php echo (int)$rs_user_id;?>" name="comment_coin" class="form_select comment_coin"
                        style="width:80px;" user_id="<?php echo (int)$rs_user_id;?>">
                            <option value="50" >+50葵幣
                            <option value="100">+100葵幣
                            <option value="150">+150葵幣
                            <option value="200">+200葵幣
                            <option value="300">+300葵幣

                            <option value="-50" >-50葵幣
                            <option value="-100">-100葵幣
                            <option value="-150">-150葵幣
                            <option value="-200">-200葵幣
                            <option value="-300">-300葵幣
                        </select>

                        <input type="text" id="note_<?php echo (int)$rs_user_id;?>" name="note" value=""
                        style="width:150px;" placeholder="可填寫理由或備註">

                        <input type="button" value="送出" class="ibtn_gr3020" style=""
                        onclick="incentive(<?php echo (int)$rs_user_id;?>);void(0);" onmouseover="this.style.cursor='pointer'">
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

    function incentive(user_id){
        var ocomment_coin=document.getElementById('comment_coin_'+user_id);
        var onote        =document.getElementById('note_'+user_id);
        comment_coin=ocomment_coin.value;
        note        =trim(onote.value);

        var url ='';
        var page=str_repeat('../',0)+'incentive/incentiveA.php';
        var arg ={
            'user_id':user_id,
            'comment_coin':comment_coin,
            'note':note,
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

        if(confirm('你確定要獎懲小朋友嗎?')){
            go(url,'self');
        }else{
            return false;
        }
    }

    function view(mode,user_id){
    //瀏覽
        var url ='';
        var page=str_repeat('../',0)+'view/viewF.php';
        var arg ={
            'mode':mode,
            'user_id':user_id,
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

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $config_arrys;

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
                        目前尚未有交易紀錄!<br/>
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

</script>

<?php
//-------------------------------------------------------
//page_nrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_user=NULL;
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