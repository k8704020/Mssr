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

        if(isset($_SESSION['a']['query']['m_forum_analysis']['filter'])){
            $filter=trim($_SESSION['a']['query']['m_forum_analysis']['filter']);
        }
        if(isset($_SESSION['a']['query']['m_forum_analysis']['query_fields'])){
            $query_fields=$_SESSION['a']['query']['m_forum_analysis']['query_fields'];
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

        //調配
        set_time_limit(0);
        ini_set('memory_limit', '3072M');

        $query_year_start=(isset($_SESSION['a']['query']['m_forum_analysis']['query_year_start']))?(int)$_SESSION['a']['query']['m_forum_analysis']['query_year_start']:(int)date("Y");
        $query_month_start=(isset($_SESSION['a']['query']['m_forum_analysis']['query_month_start']))?(int)$_SESSION['a']['query']['m_forum_analysis']['query_month_start']:(int)date("m");
        $query_year_end=(isset($_SESSION['a']['query']['m_forum_analysis']['query_year_end']))?(int)$_SESSION['a']['query']['m_forum_analysis']['query_year_end']:(int)date("Y");
        $query_month_end=(isset($_SESSION['a']['query']['m_forum_analysis']['query_month_end']))?(int)$_SESSION['a']['query']['m_forum_analysis']['query_month_end']:(int)date("m");

        function date_range($first, $last, $step = '+1 month', $format = 'Y-m'){
            $dates   = array();
            $current = strtotime($first);
            $last    = strtotime($last);

            while ($current <= $last) {
                $dates[] = date($format, $current);
                $current = strtotime($step, $current);
            }

            return $dates;
        }

        $arry_date         =date_range("{$query_year_start}-{$query_month_start}", "{$query_year_end}-{$query_month_end}");
        $arry_student_cno  =[];
        $arry_teacher_cno  =[];
        $arry_school_cno   =[];
        $arry_class_cno    =[];

        foreach($arry_date as $date){

            $query_mohth = $date;

            //總學生數
            $sql_all="
                #************************************
                #此段SQL語法為撈取閱讀使用分析(非活耀)圖
                #經Brian指示製作此一功能
                #若slow log上榜為正常現象
                #************************************
                SELECT COUNT(*) AS `cno` FROM (
                    SELECT `user`.`student`.`uid`
                    FROM `user`.`student`

                        INNER JOIN (
                            SELECT `mssr_forum`.`mssr_forum_article`.`user_id`
                            FROM `mssr_forum`.`mssr_forum_article`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_article`.`keyin_cdate` <= '{$query_mohth}'
                            GROUP BY `mssr_forum`.`mssr_forum_article`.`user_id`
                        ) AS `sqry` ON
                        `user`.`student`.`uid` = `sqry`.`user_id`

                    WHERE 1=1
                    GROUP BY `user`.`student`.`uid`
                ) AS `mqry`
                WHERE 1=1
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql_all,$arry_limit=array(),$arry_conn_mssr);
            $arry_student_cno[]=$db_results[0]['cno'];

            //總老師數
            $sql_all="
                #************************************
                #此段SQL語法為撈取閱讀使用分析(非活耀)圖
                #經Brian指示製作此一功能
                #若slow log上榜為正常現象
                #************************************
                SELECT COUNT(*) AS `cno` FROM (

                    SELECT `user`.`member`.`uid`
                    FROM `user`.`member`
                        INNER JOIN `user`.`permissions` ON
                        `user`.`member`.`permission` = `user`.`permissions`.`permission`

                        INNER JOIN (
                            SELECT `mssr_forum`.`mssr_forum_article`.`user_id`
                            FROM `mssr_forum`.`mssr_forum_article`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_article`.`keyin_cdate` <= '{$query_mohth}'
                            GROUP BY `mssr_forum`.`mssr_forum_article`.`user_id`
                        ) AS `sqry` ON
                        `user`.`member`.`uid` = `sqry`.`user_id`
                    WHERE 1=1
                        #AND `user`.`member`.`build_time` <= '{$query_mohth}'
                        AND `user`.`permissions`.`status` = 'i_t'
                    GROUP BY `user`.`member`.`uid`

                ) AS `mqry`
                WHERE 1=1
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql_all,$arry_limit=array(),$arry_conn_mssr);
            $arry_teacher_cno[]=$db_results[0]['cno'];


            //總學校數
            $sql_all="
                #************************************
                #此段SQL語法為撈取閱讀使用分析(非活耀)圖
                #經Brian指示製作此一功能
                #若slow log上榜為正常現象
                #************************************

                SELECT COUNT(*) AS `cno` FROM (

                    SELECT `user`.`semester`.`school_code`
                    FROM `user`.`semester`
                        INNER JOIN `user`.`class` ON
                        `user`.`semester`.`semester_code` = `user`.`class`.`semester_code`
                    WHERE 1=1
                        AND `user`.`class`.`class_code` IN (
                            SELECT `user`.`student`.`class_code`
                            FROM `user`.`student`

                                INNER JOIN (
                                    SELECT `mssr_forum`.`mssr_forum_article`.`user_id`
                                    FROM `mssr_forum`.`mssr_forum_article`
                                    WHERE 1=1
                                        AND `mssr_forum`.`mssr_forum_article`.`keyin_cdate` <= '{$query_mohth}'
                                    GROUP BY `mssr_forum`.`mssr_forum_article`.`user_id`
                                ) AS `sqry` ON
                                `user`.`student`.`uid` = `sqry`.`user_id`

                            WHERE 1=1
                            GROUP BY `user`.`student`.`class_code`
                        )
                    GROUP BY `user`.`semester`.`school_code`

                ) AS `mqry`
                WHERE 1=1
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql_all,$arry_limit=array(),$arry_conn_mssr);
            $arry_school_cno[]=$db_results[0]['cno'];

            //總班級數
            $sql_all="
                #************************************
                #此段SQL語法為撈取閱讀使用分析(非活耀)圖
                #經Brian指示製作此一功能
                #若slow log上榜為正常現象
                #************************************

                SELECT COUNT(*) AS `cno` FROM (

                    SELECT
                        `user`.`student`.`class_code`,
                        `user`.`student`.`end`
                    FROM `user`.`student`

                        INNER JOIN (
                            SELECT `mssr_forum`.`mssr_forum_article`.`user_id`
                            FROM `mssr_forum`.`mssr_forum_article`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_article`.`keyin_cdate` <= '{$query_mohth}'
                            GROUP BY `mssr_forum`.`mssr_forum_article`.`user_id`
                        ) AS `sqry` ON
                        `user`.`student`.`uid` = `sqry`.`user_id`

                    WHERE 1=1
                    GROUP BY `user`.`student`.`class_code`
                    HAVING `user`.`student`.`end` <= '{$query_mohth}'

                ) AS `mqry`
                WHERE 1=1
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql_all,$arry_limit=array(),$arry_conn_mssr);
            $arry_class_cno[]=$db_results[0]['cno'];
        }
//echo "<Pre>";print_r($arry_date);echo "</Pre>";
//echo "<Pre>";print_r($arry_student_cno);echo "</Pre>";
//echo "<Pre>";print_r($arry_teacher_cno);echo "</Pre>";
//echo "<Pre>";print_r($arry_school_cno);echo "</Pre>";
//echo "<Pre>";print_r($arry_class_cno);echo "</Pre>";
//die();
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
    <script type="text/javascript" src="../../../../lib/js/flash/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../inc/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/func/flot/excanvas.min.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/func/flot/jquery.flot.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/func/flot/jquery.flot.pie.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>

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
                <tr align="center" class="bg_gray1" height="45px">
                    <td>
                        <span style="font-size:14pt;">
                            聊書使用分析(非活耀)
                        </span>
                    </td>
                </tr>
                <!-- <tr align="center" height="45px">
                    <td><div id="container" style="width:100%;float:left;margin-top:5px;"></div></td>
                </tr> -->
                <tr align="center" height="45px">
                    <td><div id="container_accumulation" style="width:100%;float:left;margin-top:5px;"></div></td>
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

        var arry_date = [];
        var arry_student_cno = [];
        var arry_teacher_cno = [];
        var arry_school_cno  = [];
        var arry_class_cno  = [];

        <?php foreach($arry_date as $val):?>
            arry_date.push('<?php echo $val;?>');
        <?php endforeach;?>

        <?php foreach($arry_student_cno as $val):?>
            arry_student_cno.push(<?php echo $val;?>);
        <?php endforeach;?>

        <?php foreach($arry_teacher_cno as $val):?>
            arry_teacher_cno.push(<?php echo $val;?>);
        <?php endforeach;?>

        <?php foreach($arry_school_cno as $val):?>
            arry_school_cno.push(<?php echo $val;?>);
        <?php endforeach;?>

        <?php foreach($arry_class_cno as $val):?>
            arry_class_cno.push(<?php echo $val;?>);
        <?php endforeach;?>

    //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------


    $(function(){
//console.log(arry_date);
//console.log(arry_student_cno);
//console.log(arry_teacher_cno);
//console.log(arry_school_cno);

        $('#container_accumulation').highcharts({
            chart: {zoomType: 'xy'},
            title: {text: ''},
            subtitle: {},
            xAxis: [{
                categories: arry_date,
                crosshair: true
            }],
            yAxis: [{ // Primary yAxis
                labels: {
                    format: '{value}',
                    style: {
                        color: Highcharts.getOptions().colors[2]
                    }
                },
                title: {
                    text: '學校數',
                    style: {
                        color: Highcharts.getOptions().colors[2]
                    }
                },
                opposite: true

            }, { // Secondary yAxis
                labels: {
                    format: '{value}',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                },
                title: {
                    text: '老師數',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                },
                opposite: true

            }, { // Tertiary yAxis
                gridLineWidth: 0,
                title: {
                    text: '學生數',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                labels: {
                    format: '{value}',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                opposite: true
            }, { // Tertiary yAxis
                gridLineWidth: 0,
                title: {
                    text: '班級數',
                    style: {
                        color: Highcharts.getOptions().colors[3]
                    }
                },
                labels: {
                    format: '{value}',
                    style: {
                        color: Highcharts.getOptions().colors[3]
                    }
                },
                opposite: true
            }],
            tooltip: {shared: true},
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                }
            },
            legend: {
                layout: 'vertical',
                align: 'left',
                x: 80,
                verticalAlign: 'top',
                y: 55,
                floating: true,
                backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
            },
            series: [{
                name: '學生數',
                type: 'spline',
                yAxis: 2,
                data: arry_student_cno,
                tooltip: {
                    valueSuffix: ''
                }

            }, {
                name: '老師數',
                type: 'spline',
                yAxis: 1,
                data: arry_teacher_cno,
                //dashStyle: 'shortdot',
                tooltip: {
                    valueSuffix: ''
                }

            }, {
                name: '學校數',
                type: 'spline',
                yAxis: 0,
                data: arry_school_cno,
                tooltip: {
                    valueSuffix: ''
                }
            }, {
                name: '班級數',
                type: 'spline',
                yAxis: 3,
                data: arry_class_cno,
                tooltip: {
                    valueSuffix: ''
                }
            }]
        });
    });

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