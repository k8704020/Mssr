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

        if(isset($_SESSION['a']['query']['m_verified_credit_class']['filter'])){
            $filter=trim($_SESSION['a']['query']['m_verified_credit_class']['filter']);
        }
        if(isset($_SESSION['a']['query']['m_verified_credit_class']['query_fields'])){
            $query_fields=$_SESSION['a']['query']['m_verified_credit_class']['query_fields'];
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

            if($filter!=''){
                $filter=str_replace("grade_id","`mssr`.`mssr_credit_grade`.`grade_id`",$filter);
                $filter=str_replace("class_id","`mssr`.`mssr_credit_class`.`class_id`",$filter);
                $sql="
                    SELECT
                        `mssr`.`mssr_user_credit_group_rev`.`user_id`,
                        `user`.`member`.`name`,

                        `mssr`.`mssr_credit_grade`.`grade_id`,
                        `mssr`.`mssr_credit_grade`.`grade_name`,

                        `mssr`.`mssr_credit_class`.`class_id`,
                        `mssr`.`mssr_credit_class`.`class_name`
                    FROM `mssr`.`mssr_credit_grade`
                        INNER JOIN `mssr`.`mssr_credit_grade_class_rev` ON
                        `mssr`.`mssr_credit_grade`.`grade_id`= `mssr`.`mssr_credit_grade_class_rev`.`grade_id`

                        INNER JOIN `mssr`.`mssr_credit_class` ON
                        `mssr`.`mssr_credit_grade_class_rev`.`class_id`= `mssr`.`mssr_credit_class`.`class_id`

                        INNER JOIN `mssr`.`mssr_credit_class_group_rev` ON
                        `mssr`.`mssr_credit_grade_class_rev`.`class_id`= `mssr`.`mssr_credit_class_group_rev`.`class_id`

                        INNER JOIN `mssr`.`mssr_user_credit_group_rev` ON
                        `mssr`.`mssr_credit_class_group_rev`.`group_id`= `mssr`.`mssr_user_credit_group_rev`.`group_id`

                        INNER JOIN `user`.`member` ON
                        `mssr`.`mssr_user_credit_group_rev`.`user_id`= `user`.`member`.`uid`
                    WHERE 1=1

                    {$filter}

                    GROUP BY `mssr`.`mssr_user_credit_group_rev`.`user_id`
                    ORDER BY
                        `mssr`.`mssr_credit_grade`.`grade_id`,
                        `mssr`.`mssr_credit_class`.`class_id` DESC
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                $db_results_cno=count($db_results);
            }else{
                page_nrs($title);
                die();
            }

    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=$db_results_cno;    //資料總筆數
        $psize =15;                 //單頁筆數,預設10筆
        $pnos  =0;                  //分頁筆數
        $pinx  =1;                  //目前分頁索引,預設1
        $sinx  =0;                  //值域起始值
        $einx  =0;                  //值域終止值

        if(isset($_GET['psize'])){
            $psize=(int)$_GET['psize'];
            if($psize===0){
                $psize=15;
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

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        if($numrow!==0){
            $arrys_chunk =array_chunk($db_results,$psize);
            $arrys_result=$arrys_chunk[$pinx-1];
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

        global $arrys_result;

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
                    <td width="250px">學分班                    </td>
                    <td width="">姓名                           </td>
                    <td width="60px">推薦本數                   </td>
                    <td width="60px">上架本數                   </td>
                    <td width="60px">訂閱本數                   </td>
                    <td width="60px">聊書好友                   </td>
                    <td width="60px">發表文章                   </td>
                    <td width="60px">回應文章                   </td>
                    <td width="100px">邀請好友討論自己發表的文章</td>
                </tr>
                <?php if(!empty($arrys_result)):?>
                    <?php foreach($arrys_result as $arry_result):?>
                    <?php
                        $user_id    =(int)$arry_result['user_id'];
                        $name       =trim($arry_result['name']);
                        $grade_id   =(int)$arry_result['grade_id'];
                        $grade_name =trim($arry_result['grade_name']);
                        $class_id   =(int)$arry_result['class_id'];
                        $class_name =trim($arry_result['class_name']);

                        //推薦本數
                        $sql="
                            SELECT
                                `mssr`.`mssr_rec_book_cno`.`user_id`
                            FROM `mssr`.`mssr_rec_book_cno`
                            WHERE 1=1
                                AND `mssr`.`mssr_rec_book_cno`.`user_id` = {$user_id}
                            GROUP BY `mssr`.`mssr_rec_book_cno`.`user_id`, `mssr`.`mssr_rec_book_cno`.`book_sid`
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        $rec_cno=count($db_results);

                        //上架本數
                        $sql="
                            SELECT
                                `mssr`.`mssr_rec_book_cno`.`user_id`
                            FROM `mssr`.`mssr_rec_book_cno`
                            WHERE 1=1
                                AND `mssr`.`mssr_rec_book_cno`.`user_id` = {$user_id}
                                AND `mssr`.`mssr_rec_book_cno`.`book_on_shelf_state` = '上架'
                            GROUP BY `mssr`.`mssr_rec_book_cno`.`user_id`, `mssr`.`mssr_rec_book_cno`.`book_sid`
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        $on_shelf_cno=count($db_results);

                        //訂閱本數
                        $sql="
                                SELECT
                                    `mssr`.`mssr_book_booking_log`.`booking_from`
                                FROM `mssr`.`mssr_book_booking_log`
                                WHERE 1=1
                                    AND `mssr`.`mssr_book_booking_log`.`booking_from` = {$user_id}
                                    AND `mssr`.`mssr_book_booking_log`.`booking_state` = '完成交易'
                                GROUP BY `mssr`.`mssr_book_booking_log`.`booking_from`, `mssr`.`mssr_book_booking_log`.`book_sid`

                            UNION ALL

                                SELECT
                                    `mssr`.`mssr_book_booking`.`booking_from`
                                FROM `mssr`.`mssr_book_booking`
                                WHERE 1=1
                                    AND `mssr`.`mssr_book_booking`.`booking_from` = {$user_id}
                                GROUP BY `mssr`.`mssr_book_booking`.`booking_from`, `mssr`.`mssr_book_booking`.`book_sid`
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        $booking_cno=count($db_results);

                        //聊書好友
                        $sql="
                            SELECT `mssr_forum`.`mssr_forum_friend`.`create_by`
                            FROM `mssr_forum`.`mssr_forum_friend`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_friend`.`friend_state`=1
                                AND (
                                    `mssr_forum`.`mssr_forum_friend`.`user_id`  ={$user_id}
                                    OR
                                    `mssr_forum`.`mssr_forum_friend`.`friend_id`={$user_id}
                                )
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        $forum_friend_cno=count($db_results);

                        //發表文章
                        $sql="
                            SELECT `mssr_forum`.`mssr_forum_article`.`user_id`
                            FROM `mssr_forum`.`mssr_forum_article`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_article`.`article_state`=1
                                AND `mssr_forum`.`mssr_forum_article`.`user_id`={$user_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        $article_cno=count($db_results);

                        //回應文章
                        $sql="
                            SELECT `mssr_forum`.`mssr_forum_reply`.`user_id`
                            FROM `mssr_forum`.`mssr_forum_reply`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_reply`.`reply_state`=1
                                AND `mssr_forum`.`mssr_forum_reply`.`user_id`={$user_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        $reply_cno=count($db_results);

                        //邀請好友討論自己發表的文章
                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_user_request`.`request_from`
                            FROM `mssr_forum`.`mssr_forum_user_request`
                                INNER JOIN `mssr_forum`.`mssr_forum_user_request_article_rev` ON
                                `mssr_forum`.`mssr_forum_user_request`.`request_id`=`mssr_forum`.`mssr_forum_user_request_article_rev`.`request_id`

                                INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                                `mssr_forum`.`mssr_forum_user_request_article_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_user_request`.`request_from`={$user_id}
                                AND `mssr_forum`.`mssr_forum_article`.`user_id`={$user_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        $request_my_article_cno=count($db_results);
                    ?>
                    <tr align="center" height="25px">
                        <td><?php echo htmlspecialchars($grade_name.' '.$class_name);?></td>
                        <td><?php echo htmlspecialchars($name);?></td>
                        <td><?php echo $rec_cno;?></td>
                        <td><?php echo $on_shelf_cno;?></td>
                        <td><?php echo $booking_cno;?></td>
                        <td><?php echo $forum_friend_cno;?></td>
                        <td><?php echo $article_cno;?></td>
                        <td><?php echo $reply_cno;?></td>
                        <td><?php echo $request_my_article_cno;?></td>
                    </tr>
                    <?php endforeach;?>
                <?php endif;?>
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

        global $arrys_result;

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
            <table align="center" border="0" width="90%" cellpadding="5" cellspacing="0" style="position:relative;top:80px;" class="table_style2">
                <tr align="center" valign="middle" class="bg_gray1">
                    <td>&nbsp;</td>
                </tr>
                <tr align="center">
                    <td height="300px" align="center" valign="middle" class="font-family1 fsize_16">
                        <img src="../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
                        目前系統無資料，或查無資料!<br/><br/>
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

    //---------------------------------------------------
    //物件
    //---------------------------------------------------

    //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        window.onload=function(){

        }
</script>

</Body>
</Html>

<?php
//-------------------------------------------------------
//page_nrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_user=NULL;
    $conn_mssr=NULL;
?>
<?php };?>