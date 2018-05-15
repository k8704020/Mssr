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

        if(isset($_SESSION['a']['query']['m_book_borrow_rank_info']['filter'])){
            $filter=trim($_SESSION['a']['query']['m_book_borrow_rank_info']['filter']);
        }
        if(isset($_SESSION['a']['query']['m_book_borrow_rank_info']['query_fields'])){
            $query_fields=$_SESSION['a']['query']['m_book_borrow_rank_info']['query_fields'];
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
        //學期時間 SQL
        //-----------------------------------------------

            $sql="
                SELECT `user`.`semester`.`start`,`user`.`semester`.`end`
                FROM `user`.`semester`
                WHERE 1=1
                    AND `user`.`semester`.`start` <= CURDATE()
                    AND `user`.`semester`.`end`   >= CURDATE()
            ";
            $db_results    =db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
            $semester_start=trim($db_results[0]['start']);
            $semester_end  =trim($db_results[0]['end']);
            $grade         =1;

            if(trim($filter)!==''){
                $filter=str_replace("AND grade_id","AND `user`.`class`.`grade`",$filter);
            }else{
                $filter='AND `user`.`class`.`grade`=1';
            }
            //echo "<Pre>";
            //print_r($filter);
            //echo "</Pre>";

        //-----------------------------------------------
        //學生名單 SQL
        //-----------------------------------------------

            $sql="
                SELECT `user`.`student`.`uid`
                FROM `user`.`student`
                    INNER JOIN `user`.`class` ON
                    `user`.`student`.`class_code`=`user`.`class`.`class_code`
                WHERE 1=1
                    AND `user`.`student`.`start` >='{$semester_start}'
                    {$filter}
                GROUP BY `user`.`student`.`uid`
            ";
            $db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
            if(!empty($db_results)){
                $arry_uid=[];
                foreach($db_results as $db_resul){
                    $rs_uid=(int)$db_resul['uid'];
                    $arry_uid[]=$rs_uid;
                }
                $list_uid=implode(",",$arry_uid);
            }

        //-----------------------------------------------
        //主 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    COUNT(`mssr`.`mssr_book_borrow_log`.`book_sid`) AS `cno`,
                    `mssr`.`mssr_book_library`.`book_name`          AS `library_name`,
                    `mssr`.`mssr_book_class`.`book_name`            AS `class_name`,
                    `mssr`.`mssr_book_global`.`book_name`           AS `global_name`,
                    `mssr`.`mssr_book_unverified`.`book_name`       AS `unverified_name`,

                    #`mssr`.`mssr_book_library`.`book_author`        AS `library_author`,
                    #`mssr`.`mssr_book_class`.`book_author`          AS `class_author`,
                    #`mssr`.`mssr_book_global`.`book_author`         AS `global_author`,
                    #`mssr`.`mssr_book_unverified`.`book_author`     AS `unverified_author`,

                    `mssr`.`mssr_book_library`.`book_publisher`     AS `library_publisher`,
                    `mssr`.`mssr_book_class`.`book_publisher`       AS `class_publisher`,
                    `mssr`.`mssr_book_global`.`book_publisher`      AS `global_publisher`,
                    `mssr`.`mssr_book_unverified`.`book_publisher`  AS `unverified_publisher`,

                    `mssr`.`mssr_book_library`.`book_isbn_10`       AS `library_isbn_10`,
                    `mssr`.`mssr_book_library`.`book_isbn_13`       AS `library_isbn_13`,
                    `mssr`.`mssr_book_class`.`book_isbn_10`         AS `class_isbn_10`,
                    `mssr`.`mssr_book_class`.`book_isbn_13`         AS `class_isbn_13`,
                    `mssr`.`mssr_book_global`.`book_isbn_10`        AS `global_isbn_10`,
                    `mssr`.`mssr_book_global`.`book_isbn_13`        AS `global_isbn_13`,
                    `mssr`.`mssr_book_unverified`.`book_isbn_10`    AS `unverified_isbn_10`,
                    `mssr`.`mssr_book_unverified`.`book_isbn_13`    AS `unverified_isbn_13`
                FROM `mssr`.`mssr_book_borrow_log`
                    LEFT JOIN `mssr`.`mssr_book_library` ON
                    `mssr`.`mssr_book_borrow_log`.`book_sid`=`mssr`.`mssr_book_library`.`book_sid`
                    LEFT JOIN `mssr`.`mssr_book_class` ON
                    `mssr`.`mssr_book_borrow_log`.`book_sid`=`mssr`.`mssr_book_class`.`book_sid`
                    LEFT JOIN `mssr`.`mssr_book_global` ON
                    `mssr`.`mssr_book_borrow_log`.`book_sid`=`mssr`.`mssr_book_global`.`book_sid`
                    LEFT JOIN `mssr`.`mssr_book_unverified` ON
                    `mssr`.`mssr_book_borrow_log`.`book_sid`=`mssr`.`mssr_book_unverified`.`book_sid`
                WHERE 1=1
                    AND `mssr`.`mssr_book_borrow_log`.`user_id` IN ({$list_uid})
                    AND `mssr`.`mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$semester_start}' AND '{$semester_end}'
                GROUP BY `mssr`.`mssr_book_borrow_log`.`book_sid`
                ORDER BY COUNT(`mssr`.`mssr_book_borrow_log`.`book_sid`) DESC
                LIMIT 150
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $db_results_cno=count($db_results);

    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=$db_results_cno;    //資料總筆數
        $psize =$numrow;            //單頁筆數,預設10筆
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
            die('查無資料');
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
                    <td width="">借閱次數       </td>
                    <td width="">書名           </td>
                    <td width="150px">出版社    </td>
                    <td width="130px">ISBN      </td>
                </tr>
                <?php foreach($arrys_result as $inx=>$arry_result):?>
                <?php
                    $cno                =(int)$arry_result['cno'];
                    $library_name       =trim($arry_result['library_name']);
                    $class_name         =trim($arry_result['class_name']);
                    $global_name        =trim($arry_result['global_name']);
                    $unverified_name    =trim($arry_result['unverified_name']);

                    //$library_author     =trim($arry_result['library_author']);
                    //$class_author       =trim($arry_result['class_author']);
                    //$global_author      =trim($arry_result['global_author']);
                    //$unverified_author  =trim($arry_result['unverified_author']);

                    $library_publisher  =trim($arry_result['library_publisher']);
                    $class_publisher    =trim($arry_result['class_publisher']);
                    $global_publisher   =trim($arry_result['global_publisher']);
                    $unverified_publisher=trim($arry_result['unverified_publisher']);

                    $library_isbn_10    =trim($arry_result['library_isbn_10']);
                    $class_isbn_10      =trim($arry_result['class_isbn_10']);
                    $global_isbn_10     =trim($arry_result['global_isbn_10']);
                    $unverified_isbn_10 =trim($arry_result['unverified_isbn_10']);

                    $library_isbn_13    =trim($arry_result['library_isbn_13']);
                    $class_isbn_13      =trim($arry_result['class_isbn_13']);
                    $global_isbn_13     =trim($arry_result['global_isbn_13']);
                    $unverified_isbn_13 =trim($arry_result['unverified_isbn_13']);

                    if($library_name==='' && $class_name==='' && $global_name==='' && $unverified_name==='')continue;

                    //echo "<Pre>";
                    //var_dump($inx);
                    //echo "</Pre>";
                ?>
                    <tr align="center" height="25px">
                        <td><?php echo $cno;?></td>
                        <td><?php echo $library_name.$class_name.$global_name.$unverified_name;?></td>
                        <td><?php echo $library_publisher.$class_publisher.$global_publisher;?></td>
                        <td>
                            <?php echo $library_isbn_10.$class_isbn_10.$global_isbn_10.$unverified_isbn_10;?>
                            <?php if($library_isbn_10!=='' || $class_isbn_10!=='' || $global_isbn_10!=='' || $unverified_isbn_10!=='')echo ', ';?>
                            <?php echo $library_isbn_13.$class_isbn_13.$global_isbn_13.$unverified_isbn_13;?>
                        </td>
                    </tr>
                <?php endforeach;?>
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