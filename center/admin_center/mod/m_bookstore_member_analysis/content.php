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

        if(isset($_SESSION['a']['query']['m_bookstore_member_analysis']['filter'])){
            $filter=trim($_SESSION['a']['query']['m_bookstore_member_analysis']['filter']);
        }
        if(isset($_SESSION['a']['query']['m_bookstore_member_analysis']['query_fields'])){
            $query_fields=$_SESSION['a']['query']['m_bookstore_member_analysis']['query_fields'];
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

        $arry_date      =[];
        $arry_user_cno  =[];
        $arry_user_accumulation=[];
        $arry_user_accumulation_cno=[];
        $arry_class_cno =[];
        $arry_school_cno=[];
        $query_year=(isset($_SESSION['a']['query']['m_bookstore_member_analysis']['query_year']))?(int)$_SESSION['a']['query']['m_bookstore_member_analysis']['query_year']:(int)date("Y");

        //書店總活耀人數
        $sql_all="
            #************************************
            #此段SQL語法為撈取書店活耀度趨勢圖
            #經Brian指示製作此一功能
            #若slow log上榜為正常現象
            #************************************
            SELECT `mssr`.`mssr_book_borrow_log`.`user_id`
            FROM `mssr`.`mssr_book_borrow_log`
            WHERE 1=1
            GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`
        ";
        $db_all_results=db_result($conn_type='pdo',$conn_mssr,$sql_all,$arry_limit=array(),$arry_conn_mssr);
        $list_all_uid='';
        $arry_all_uid=[];
        foreach($db_all_results as $db_all_result){
            $rs_user_id=(int)$db_all_result['user_id'];
            $list_all_uid.="{$rs_user_id},";
            $arry_all_uid[]=$rs_user_id;
        }
        $list_all_uid=substr($list_all_uid,0,-1);
        //echo "<Pre>";
        //print_r(count($arry_all_uid));
        //echo "</Pre>";

        //總班數
        $arry_all_class=[];
        $sql_all_class="
            #************************************
            #此段SQL語法為撈取書店活耀度趨勢圖
            #經Brian指示製作此一功能
            #若slow log上榜為正常現象
            #************************************
            SELECT `user`.`student`.`class_code` AS `class_code`
            FROM `user`.`student`
            WHERE 1=1
                AND `user`.`student`.`uid` IN({$list_all_uid})
                UNION
            SELECT `user`.`teacher`.`class_code` AS `class_code`
            FROM `user`.`teacher`
            WHERE 1=1
                AND `user`.`teacher`.`uid` IN({$list_all_uid})
        ";
        $db_all_class_results=db_result($conn_type='pdo',$conn_mssr,$sql_all_class,$arry_limit=array(),$arry_conn_mssr);
        foreach($db_all_class_results as $db_all_class_result){
            $rs_class_code=trim($db_all_class_result['class_code']);
            $arry_all_class[]=$rs_class_code;
        }
        //echo "<Pre>";
        //print_r(count($arry_all_class));
        //echo "</Pre>";

        //總校數
        $arry_all_school=[];
        $sql_all_school="
            #************************************
            #此段SQL語法為撈取書店活耀度趨勢圖
            #經Brian指示製作此一功能
            #若slow log上榜為正常現象
            #************************************
            SELECT `user`.`semester`.`school_code`
            FROM `user`.`student`
                INNER JOIN `user`.`class` ON
                `user`.`student`.`class_code`=`user`.`class`.`class_code`

                INNER JOIN `user`.`semester` ON
                `user`.`class`.`semester_code`=`user`.`semester`.`semester_code`
            WHERE 1=1
                AND `user`.`student`.`uid` IN({$list_all_uid})

                UNION

            SELECT `user`.`semester`.`school_code`
            FROM `user`.`teacher`
                INNER JOIN `user`.`class` ON
                `user`.`teacher`.`class_code`=`user`.`class`.`class_code`

                INNER JOIN `user`.`semester` ON
                `user`.`class`.`semester_code`=`user`.`semester`.`semester_code`
            WHERE 1=1
                AND `user`.`teacher`.`uid` IN({$list_all_uid})
        ";
        $db_all_school_results=db_result($conn_type='pdo',$conn_mssr,$sql_all_school,$arry_limit=array(),$arry_conn_mssr);
        foreach($db_all_school_results as $db_all_school_result){
            $rs_school_code=trim($db_all_school_result['school_code']);
            $arry_all_school[]=$rs_school_code;
        }
        //echo "<Pre>";
        //print_r(count($arry_all_school));
        //echo "</Pre>";


        $sql="
            #************************************
            #此段SQL語法為撈取書店活耀度趨勢圖
            #經Brian指示製作此一功能
            #若slow log上榜為正常現象
            #************************************
            SELECT
                DATE_FORMAT(`mssr`.`mssr_book_borrow_log`.`borrow_sdate`, '%Y-%m') AS `borrow_sdate`
            FROM `mssr`.`mssr_book_borrow_log`
            WHERE 1=1
            GROUP BY DATE_FORMAT(`mssr`.`mssr_book_borrow_log`.`borrow_sdate`, '%Y-%m')
            ORDER BY DATE_FORMAT(`mssr`.`mssr_book_borrow_log`.`borrow_sdate`, '%Y-%m') ASC
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
        foreach($db_results as $db_result){
            $rs_borrow_sdate=trim($db_result['borrow_sdate']);
            $rs_borrow_year =date("Y",strtotime("{$rs_borrow_sdate}"));
            $daycount       =date("t",strtotime("{$rs_borrow_sdate}"));
            $rs_borrow_start=$rs_borrow_sdate."-01";
            $rs_borrow_end  =$rs_borrow_sdate."-{$daycount}";
            $list_uid       ='';
            $list_class_code='';
            if((int)$rs_borrow_year==(int)$query_year){
                //echo "<Pre>";
                //print_r($rs_borrow_start);
                //echo "</Pre>";
                $arry_date[]=$rs_borrow_sdate;
                //書店活耀人數
                $sql_1="
                    #************************************
                    #此段SQL語法為撈取書店活耀度趨勢圖
                    #經Brian指示製作此一功能
                    #若slow log上榜為正常現象
                    #************************************
                    SELECT `mssr`.`mssr_book_borrow_log`.`user_id`
                    FROM `mssr`.`mssr_book_borrow_log`
                    WHERE 1=1
                        #AND `mssr`.`mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$rs_borrow_start}' AND '{$rs_borrow_end}'
                        AND `mssr`.`mssr_book_borrow_log`.`borrow_sdate` < '{$rs_borrow_end}'
                    GROUP BY  `mssr`.`mssr_book_borrow_log`.`user_id`
                ";
                $db_1_results=db_result($conn_type='pdo',$conn_mssr,$sql_1,$arry_limit=array(),$arry_conn_mssr);
                if(!empty($db_1_results)){
                    $arry_interval_user=[];
                    $arry_user_accumulation=[];
                    foreach($db_1_results as $db_1_result){
                        $rs_user_id=(int)$db_1_result['user_id'];
                        //$rs_1_borrow_sdate=trim($db_1_result['borrow_sdate']);
                        $list_uid.="{$rs_user_id},";
                        $arry_user_accumulation[]=$rs_user_id;
                    }
                    $list_uid=substr($list_uid,0,-1);
                }
                //書店活耀班數
                if($list_uid!==''){
                    $arry_user_accumulation_cno[]=count($arry_user_accumulation);
                    $arry_interval_user          =array_diff($arry_all_uid, $arry_user_accumulation);
                    $arry_user_cno[]             =count($arry_interval_user);
                    //echo "<Pre>";
                    //print_r(count($arry_interval_user));
                    //echo "</Pre>";

                    $sql_2="
                        #************************************
                        #此段SQL語法為撈取書店活耀度趨勢圖
                        #經Brian指示製作此一功能
                        #若slow log上榜為正常現象
                        #************************************
                        SELECT `user`.`student`.`class_code` AS `class_code`
                        FROM `user`.`student`
                        WHERE 1=1
                            AND `user`.`student`.`uid` IN ({$list_uid})
                            AND `user`.`student`.`start` <= '{$rs_borrow_end}'

                            UNION

                        SELECT `user`.`teacher`.`class_code` AS `class_code`
                        FROM `user`.`teacher`
                        WHERE 1=1
                            AND `user`.`teacher`.`uid` IN ({$list_uid})
                            AND `user`.`teacher`.`start` <= '{$rs_borrow_end}'
                    ";
                    $db_2_results=db_result($conn_type='pdo',$conn_mssr,$sql_2,$arry_limit=array(),$arry_conn_mssr);
                    $arry_class_code_accumulation=[];
                    $arry_interval_class_code=[];
                    if(!empty($db_2_results)){
                        foreach($db_2_results as $db_2_result){
                            $rs_class_code=trim($db_2_result['class_code']);
                            $list_class_code.="'{$rs_class_code}',";
                            $arry_class_code_accumulation[]=$rs_class_code;
                        }
                        $list_class_code=substr($list_class_code,0,-1);
                    }
                }else{
                    $arry_user_cno[]=0;
                    $arry_user_accumulation_cno[]=0;
                }
                //書店活耀校數
                $db_3_results=[];
                if($list_class_code!==''){
                    $arry_class_code_accumulation_cno[]=count($arry_class_code_accumulation);
                    $arry_class_cno[]=count(array_diff($arry_all_class, $arry_class_code_accumulation));
                    $sql_3="
                        #************************************
                        #此段SQL語法為撈取書店活耀度趨勢圖
                        #經Brian指示製作此一功能
                        #若slow log上榜為正常現象
                        #************************************
                        SELECT `user`.`semester`.`school_code` AS `school_code`
                        FROM `user`.`class`
                            INNER JOIN `user`.`semester` ON
                            `user`.`class`.`semester_code`=`user`.`semester`.`semester_code`
                        WHERE 1=1
                            AND `user`.`class`.`class_code` IN ({$list_class_code})
                        GROUP BY `school_code`
                    ";
                    $db_3_results=db_result($conn_type='pdo',$conn_mssr,$sql_3,$arry_limit=array(),$arry_conn_mssr);
                }else{
                    $arry_class_cno[]=0;
                    $arry_class_code_accumulation_cno[]=0;
                }
                if(!empty($db_3_results)){
                    //$arry_school_cno[]=count($db_3_results);
                    $arry_school_accumulation=[];
                    foreach($db_3_results as $db_3_result){
                        $rs_school_code=trim($db_3_result['school_code']);
                        $arry_school_accumulation[]=$rs_school_code;
                    }
                    $arry_school_accumulation_cno[]=count($arry_school_accumulation);
                    $arry_school_cno[]=count(array_diff($arry_all_school, $arry_school_accumulation));
                }else{
                    $arry_school_cno[]=0;
                    $arry_school_accumulation_cno[]=0;
                }
            }
        }
        //echo "<Pre>";
        //print_r($arry_date);
        //echo "</Pre>";
        //echo "<Pre>";
        //print_r($arry_user_cno);
        //echo "</Pre>";
        //echo "<Pre>";
        //print_r($arry_user_accumulation_cno);
        //echo "</Pre>";
        //echo "<Pre>";
        //print_r($arry_class_cno);
        //echo "</Pre>";
        //echo "<Pre>";
        //print_r($arry_class_code_accumulation_cno);
        //echo "</Pre>";
        //echo "<Pre>";
        //print_r($arry_school_cno);
        //echo "</Pre>";
        //echo "<Pre>";
        //print_r($arry_school_accumulation_cno);
        //echo "</Pre>";
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
    <script type="text/javascript" src="../../../../inc/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/flash/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>
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
                        <span style="font-size:18pt;">
                            書店活耀度趨勢圖<br><br>
                        </span>
                        書店活耀人數：只要借閱過一本書就算<br>
                        書店活耀班數：只要班裡一人是活耀的就算<br>
                        書店活耀校數：只要校裡一班是活耀的就算<br>
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

        var arry_date      =[];
        var arry_user_cno  =[];
        var arry_class_cno =[];
        var arry_school_cno=[];

        <?php foreach($arry_date as $val):?>
            arry_date.push('<?php echo $val;?>');
        <?php endforeach;?>

        <?php foreach($arry_user_cno as $val):?>
            arry_user_cno.push(<?php echo $val;?>);
        <?php endforeach;?>

        <?php foreach($arry_class_cno as $val):?>
            arry_class_cno.push(<?php echo $val;?>);
        <?php endforeach;?>

        <?php foreach($arry_school_cno as $val):?>
            arry_school_cno.push(<?php echo $val;?>);
        <?php endforeach;?>

        var arry_user_accumulation_cno  =[];
        var arry_class_accumulation_cno =[];
        var arry_school_accumulation_cno=[];

        <?php foreach($arry_user_accumulation_cno as $val):?>
            arry_user_accumulation_cno.push(<?php echo $val;?>);
        <?php endforeach;?>

        <?php foreach($arry_class_code_accumulation_cno as $val):?>
            arry_class_accumulation_cno.push(<?php echo $val;?>);
        <?php endforeach;?>

        <?php foreach($arry_school_accumulation_cno as $val):?>
            arry_school_accumulation_cno.push(<?php echo $val;?>);
        <?php endforeach;?>

    //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

    $(function(){
//console.log(arry_date);
//console.log(arry_user_cno);
//console.log(arry_class_cno);
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
                    text: '書店活耀校數',
                    style: {
                        color: Highcharts.getOptions().colors[2]
                    }
                },
                opposite: true

            }, { // Secondary yAxis
                labels: {
                    format: '{value}',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                title: {
                    text: '書店活耀人數',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                opposite: true

            }, { // Tertiary yAxis
                gridLineWidth: 0,
                title: {
                    text: '書店活耀班數',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                },
                labels: {
                    format: '{value}',
                    style: {
                        color: Highcharts.getOptions().colors[1]
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
                name: '書店活耀人數',
                type: 'spline',
                yAxis: 1,
                data: arry_user_accumulation_cno,
                tooltip: {
                    valueSuffix: ''
                }

            }, {
                name: '書店活耀班數',
                type: 'spline',
                yAxis: 2,
                data: arry_class_accumulation_cno,
                dashStyle: 'shortdot',
                tooltip: {
                    valueSuffix: ''
                }

            }, {
                name: '書店活耀校數',
                type: 'spline',
                data: arry_school_accumulation_cno,
                tooltip: {
                    valueSuffix: ''
                }
            }]
        });

        //$('#container').highcharts({
        //    chart: {zoomType: 'xy'},
        //    title: {text: ''},
        //    subtitle: {},
        //    xAxis: [{
        //        categories: arry_date,
        //        crosshair: true
        //    }],
        //    yAxis: [{ // Primary yAxis
        //        labels: {
        //            format: '{value}',
        //            style: {
        //                color: Highcharts.getOptions().colors[0]
        //            }
        //        },
        //        title: {
        //            text: '書店活耀人數',
        //            style: {
        //                color: Highcharts.getOptions().colors[0]
        //            }
        //        },
        //        opposite: true
        //
        //    }, { // Secondary yAxis
        //        labels: {
        //            format: '{value}',
        //            style: {
        //                color: Highcharts.getOptions().colors[1]
        //            }
        //        },
        //        title: {
        //            text: '書店活耀班數',
        //            style: {
        //                color: Highcharts.getOptions().colors[1]
        //            }
        //        },
        //        opposite: true
        //
        //    }, { // Tertiary yAxis
        //        gridLineWidth: 0,
        //        title: {
        //            text: '書店活耀校數',
        //            style: {
        //                color: Highcharts.getOptions().colors[2]
        //            }
        //        },
        //        labels: {
        //            format: '{value}',
        //            style: {
        //                color: Highcharts.getOptions().colors[2]
        //            }
        //        },
        //        opposite: true
        //    }],
        //    tooltip: {shared: true},
        //    plotOptions: {
        //        line: {
        //            dataLabels: {
        //                enabled: true
        //            },
        //            enableMouseTracking: false
        //        }
        //    },
        //    legend: {
        //        layout: 'vertical',
        //        align: 'left',
        //        x: 80,
        //        verticalAlign: 'top',
        //        y: 55,
        //        floating: true,
        //        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        //    },
        //    series: [{
        //        name: '書店活耀人數',
        //        type: 'spline',
        //        yAxis: 1,
        //        data: arry_user_cno,
        //        tooltip: {
        //            valueSuffix: ''
        //        }
        //
        //    }, {
        //        name: '書店活耀班數',
        //        type: 'spline',
        //        yAxis: 2,
        //        data: arry_class_cno,
        //        dashStyle: 'shortdot',
        //        tooltip: {
        //            valueSuffix: ''
        //        }
        //
        //    }, {
        //        name: '書店活耀校數',
        //        type: 'spline',
        //        data: arry_school_cno,
        //        tooltip: {
        //            valueSuffix: ''
        //        }
        //    }]
        //});
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