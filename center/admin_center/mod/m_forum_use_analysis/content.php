<?php
//-------------------------------------------------------
//明日書店網管中心
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
                    APP_ROOT.'center/admin_center/inc/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'inc/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(!login_check(array('a'))){
            $url=str_repeat("../",2).'mod/m_login/loginF.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        //初始化，承接變數
        $_sess_a=$_SESSION['a'];
        foreach($_sess_a as $field_name=>$field_value){
            if(!is_array($field_value))$$field_name=trim($field_value);
        }
        //echo "<Pre>";
        //print_r($_SESSION['a']);
        //echo "</Pre>";

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:15;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?15:$psize;
        $pinx =($pinx===0)?1:$pinx;

        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用

        if(isset($_SESSION['a']['query']['m_forum_use_analysis']['filter'])){
            $filter=trim($_SESSION['a']['query']['m_forum_use_analysis']['filter']);
        }
        if(isset($_SESSION['a']['query']['m_forum_use_analysis']['query_fields'])){
            $query_fields=$_SESSION['a']['query']['m_forum_use_analysis']['query_fields'];
        }
        //echo "<Pre>";
        //print_r($filter);
        //echo "</Pre>";

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,明日書店網管中心";

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

        //-----------------------------------------------
        //主SQL
        //-----------------------------------------------

    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        page_hrs($title);
        die();
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
        global $arry_conn_user;
        global $arry_conn_mssr;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

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

        $sql="
                SELECT * FROM (
                    SELECT
                        `mssr_forum`.`mssr_forum_article`.`user_id`,
                        COUNT(`mssr_forum`.`mssr_forum_article`.`user_id`) AS `cno`
                    FROM `mssr_forum`.`mssr_forum_article`
                    WHERE 1=1
                    GROUP BY `mssr_forum`.`mssr_forum_article`.`user_id`
                ) AS `sqry1`
                WHERE 1=1
                    AND `sqry1`.`cno`>1
            UNION ALL
                SELECT * FROM (
                    SELECT
                        `mssr_forum`.`mssr_forum_reply`.`user_id`,
                        COUNT(`mssr_forum`.`mssr_forum_reply`.`user_id`) AS `cno`
                    FROM `mssr_forum`.`mssr_forum_reply`
                    WHERE 1=1
                    GROUP BY `mssr_forum`.`mssr_forum_reply`.`user_id`
                ) AS `sqry2`
                WHERE 1=1
                    AND `sqry2`.`cno`>1
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
        $live_user_arry       =[];
        $live_user_school_arry=[];
        if(!empty($db_results)){
            foreach($db_results as $db_result){
                $rs_user_id=(int)$db_result['user_id'];
                $live_user_arry[]=$rs_user_id;
            }
            $live_user_list=implode(",",$live_user_arry);
            $sql="
                    SELECT
                        `user`.`personnel`.`school_code`
                    FROM `user`.`personnel`
                    WHERE 1=1
                        AND `user`.`personnel`.`uid` IN ($live_user_list)
                UNION
                    SELECT
                        `user`.`member_school`.`school_code`
                    FROM `user`.`member_school`
                    WHERE 1=1
                        AND `user`.`member_school`.`uid` IN ($live_user_list)
                UNION
                    SELECT
                        `user`.`semester`.`school_code`
                    FROM `user`.`student`
                        INNER JOIN `user`.`class` ON
                        `user`.`student`.`class_code`=`user`.`class`.`class_code`
                        INNER JOIN `user`.`semester` ON
                        `user`.`class`.`semester_code`=`user`.`semester`.`semester_code`
                    WHERE 1=1
                        AND `user`.`student`.`uid` IN ($live_user_list)
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $db_result){
                    $rs_school_code=trim($db_result['school_code']);
                    $live_user_school_arry[]=$rs_school_code;
                }
            }
        }


        $sql="
            SELECT `user`.`member`.`uid`
            FROM  `user`.`member`
                INNER JOIN `user`.`permissions` ON
                `user`.`member`.`permission`=`user`.`permissions`.`permission`
                INNER JOIN `user`.`status_info` ON
                `user`.`permissions`.`status`=`user`.`status_info`.`status`
            WHERE 1=1
                AND `user`.`status_info`.`status` = 'u_mssr_forum'
            GROUP BY `user`.`member`.`uid`
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
        $auth_user_arry=[];
        if(!empty($db_results)){
            foreach($db_results as $db_result){
                $rs_uid=(int)$db_result['uid'];
                $auth_user_arry[]=$rs_uid;
            }
        }


        $sql="
            SELECT COUNT(*) AS `cno`
            FROM `mssr_forum`.`mssr_forum_article`
            WHERE 1=1
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
        $article_cno=(int)$db_results[0]['cno'];

        $sql="
            SELECT `mssr_forum`.`mssr_forum_article`.`user_id`
            FROM `mssr_forum`.`mssr_forum_article`
            WHERE 1=1
            GROUP BY `mssr_forum`.`mssr_forum_article`.`user_id`
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
        $article_group_user_cno=(int)count($db_results);

        $sql="
            SELECT COUNT(*) AS `cno`
            FROM `mssr_forum`.`mssr_forum_reply`
            WHERE 1=1
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
        $reply_cno=(int)$db_results[0]['cno'];

        $sql="
            SELECT `mssr_forum`.`mssr_forum_reply`.`user_id`
            FROM `mssr_forum`.`mssr_forum_reply`
            WHERE 1=1
            GROUP BY `mssr_forum`.`mssr_forum_reply`.`user_id`
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
        $reply_group_user_cno=(int)count($db_results);


        $sql="
            SELECT `mssr_forum`.`mssr_forum_article_detail_log`.`article_content`
            FROM `mssr_forum`.`mssr_forum_article_detail_log`
            WHERE 1=1
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
        $article_contents='';
        foreach($db_results as $db_result){
            $rs_article_content=trim($db_result['article_content']);
            $article_contents.=$rs_article_content;
        }
        $article_contents=preg_replace('/[0-9]|[A-Z]|[a-z]/','',$article_contents);
        $article_contents=preg_replace('/\s+/','',$article_contents);

        $sql="
            SELECT `mssr_forum`.`mssr_forum_reply_detail_log`.`reply_content`
            FROM `mssr_forum`.`mssr_forum_reply_detail_log`
            WHERE 1=1
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
        $reply_contents='';
        foreach($db_results as $db_result){
            $rs_reply_content=trim($db_result['reply_content']);
            $reply_contents.=$rs_reply_content;
        }
        $reply_contents=preg_replace('/[0-9]|[A-Z]|[a-z]/','',$reply_contents);
        $reply_contents=preg_replace('/\s+/','',$reply_contents);
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
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../lib/php/image/verify/verify_image.js"></script>
    <script type="text/javascript" src="../../../../inc/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/flash/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" />
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>
</Head>

<Body>

<!-- 資料列表 開始 -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <!-- 在此設定寬高 -->
        <td width="80%" height="300px" align="center" valign="top">
            <!-- 內容 -->
            <table align="center" border="1" width="100%" class="table_style9" style="position:relative;margin-top:30px;">
                <tr align="center" class="bg_gray1" height="35px">
                    <td width="">活著使用人數 / 在多少間學校內  </td>
                    <td width="">被授權可以使用聊書功能的人數   </td>
                    <td width="">平均發文或回文的篇數           </td>
                    <td width="">全天(使用小時數/天)            </td>
                    <td width="">上學時間(使用小時數/天)        </td>
                    <td width="">放學時間(使用小時數/天)        </td>
                    <td width="90">發文平均字數                 </td>
                    <td width="90">回文平均字數                 </td>
                </tr>
                    <tr align="center" height="25px">
                        <td><?php echo count($live_user_arry);?> / <?php echo count($live_user_school_arry);?></td>
                        <td><?php echo count($auth_user_arry);?></td>
                        <td><?php echo round(($article_cno+$reply_cno)/($article_group_user_cno+$reply_group_user_cno),1);?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?php echo round((mb_strlen($article_contents)/$article_group_user_cno),1);?><br>(含標點符號)</td>
                        <td><?php echo round((mb_strlen($reply_contents)/$reply_group_user_cno),1);?><br>(含標點符號)</td>
                    </tr>
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

    //---------------------------------------------------
    //參數
    //---------------------------------------------------

        var psize=<?php echo $psize;?>;
        var pinx =<?php echo $pinx;?>;

    //---------------------------------------------------
    //物件
    //---------------------------------------------------

    //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        window.onload=function(){
            ////套表格列奇偶色
            //table_hover(tbl_id='mod_data_tbl',c_odd='#fff',c_even='#fff',c_on='#e6faff');

            ////分頁列
            //var cid         ="page";                        //容器id
            //var numrow      =<?php echo (int)$numrow;?>;    //資料總筆數
            //var psize       =<?php echo (int)$psize ;?>;    //單頁筆數,預設10筆
            //var pnos        =<?php echo (int)$pnos  ;?>;    //分頁筆數
            //var pinx        =<?php echo (int)$pinx  ;?>;    //目前分頁索引,預設1
            //var sinx        =<?php echo (int)$sinx  ;?>;    //值域起始值
            //var einx        =<?php echo (int)$einx  ;?>;    //值域終止值
            //var list_size   =5;                             //分頁列顯示筆數,5
            //var url_args    ={};                            //連結資訊
            //url_args={
            //    'pinx_name' :'pinx',
            //    'psize_name':'psize',
            //    'page_name' :'content.php',
            //    'page_args' :{}
            //}
            //var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
        }
</script>

</Body>
</Html>

<?php
//-------------------------------------------------------
//page_hrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_user=NULL;
    $conn_mssr=NULL;
?>
<?php };?>