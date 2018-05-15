<?php
//-------------------------------------------------------
//明日星球,分店
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",2).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'service/branch/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        $sess_uid        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:0;
        $sess_class_code =(isset($_SESSION['class'][0][1]))?trim($_SESSION['class'][0][1]):'';
        $sess_school_code=(isset($_SESSION['school_code']))?trim($_SESSION['school_code']):'';
        $sess_sem_year   =(isset($_SESSION['sem_year']))?(int)$_SESSION['sem_year']:0;
        $sess_sem_term   =(isset($_SESSION['sem_term']))?(int)$_SESSION['sem_term']:0;
        $sess_grade      =(isset($_SESSION['grade']))?(int)$_SESSION['grade']:0;
        if(!in_array($sess_class_code,array('gcp_2013_2_3_6','gcp_2013_2_3_7'))){
            die();
        }

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
    //branch_name     分店名稱

        //GET
        $branch_name=(isset($_GET[trim('branch_name')]))?$_GET[trim('branch_name')]:'';

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //branch_name     分店名稱

        $arry_err=array();

        if($branch_name===''){
           $arry_err[]='分店名稱,未輸入!';
        }else{
            $branch_name=trim($branch_name);
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

        //建立連線 user
        $conn_user=conn($db_type='mysql',$arry_conn_user);

        //建立連線 mssr
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //撈取, 學校資訊
        //-----------------------------------------------

            if((trim($sess_school_code)==='')&&(trim($sess_class_code)!=='')){
                $sess_class_code=mysql_prep($sess_class_code);
                $sql="
                    SELECT
                        `class`.`grade`,
                        `semester`.`school_code`
                    FROM `class`
                        INNER JOIN `semester` ON
                        `class`.`semester_code`=`semester`.`semester_code`
                    WHERE 1=1
                        AND `class`.`class_code`='{$sess_class_code}'
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                if(!empty($arrys_result)){
                    $sess_school_code=trim($arrys_result[0]['school_code']);
                    $sess_school_code=mysql_prep($sess_school_code);
                    if($sess_grade===0){
                        $sess_grade=(int)$arrys_result[0]['grade'];
                    }
                }
            }

        //-----------------------------------------------
        //撈取, 分店對應類型
        //-----------------------------------------------

            $cat_code   ='';
            $branch_name=mysql_prep($branch_name);
            if(trim($sess_class_code)!==''){
                $sess_class_code=mysql_prep($sess_class_code);
            }

            $sql="
                SELECT
                    `cat_code`
                FROM `mssr_book_category`
                WHERE 1=1
                    AND `cat_name`   ='{$branch_name     }'
                    AND `school_code`='{$sess_school_code}'
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(!empty($arrys_result)){
                $cat_code=trim($arrys_result[0]['cat_code']);
            }else{
                die();
            }

        //-----------------------------------------------
        //主SQL撈取
        //-----------------------------------------------

            $arry_book_list=array();
            $arry_book_list['gcp_2013_2_3_6']="
                0000270 ,
                0000875 ,
                00009629,
                00009634,
                00009645,
                00009646,
                00009648,
                00009649,
                00009653,
                00009656,
                00009664,
                00009678,
                00009690,
                00009710,
                0000996 ,
                0003251 ,
                0005676 ,
                0005689 ,
                0005692 ,
                0005693 ,
                0005695 ,
                0005697 ,
                0005810 ,
                0005960 ,
                0005963 ,
                0006520 ,
                0006521 ,
                0006523 ,
                0007935 ,
                0015712 ,
                0015713 ,
                0018569 ,
                0018570 ,
                0020195 ,
                0020196 ,
                0028008 ,
                0028607 ,
                0028610 ,
                0030801 ,
                0031055 ,
                0001748 ,
                0001749 ,
                0001750 ,
                0001751 ,
                0001752 ,
                0001783 ,
                0001784 ,
                0001785 ,
                0001786 ,
                0001787 ,
                0001788 ,
                0001789 ,
                0001790 ,
                0001791 ,
                0001792 ,
                0001793 ,
                0001794 ,
                0001795 ,
                0001796 ,
                0001810 ,
                0001897 ,
                0001898 ,
                0001903 ,
                0001904 ,
                0001905 ,
                0003728 ,
                0003740 ,
                0003753 ,
                0004117 ,
                0004118 ,
                0004119 ,
                0006454 ,
                0006615 ,
                0006616 ,
                0006856 ,
                0006857 ,
                0006858 ,
                0006859 ,
                0006860 ,
                0006863 ,
                0006881 ,
                0006885 ,
                0008117 ,
                0008118 ,
                0009055 ,
                0009349 ,
                0009351 ,
                0009390 ,
                0009592 ,
                0009794 ,
                0010360 ,
                0011317 ,
                0011318 ,
                0011319 ,
                0011321 ,
                0011322 ,
                0011361 ,
                0011362 ,
                0011363 ,
                0011507 ,
                0011804 ,
                0011805 ,
                0011806 ,
                0012564 ,
                0012567 ,
                0014545 ,
                0015638 ,
                0015707 ,
                0015796 ,
                0018945 ,
                0019326 ,
                0019500 ,
                0019505 ,
                0021259 ,
                0021314 ,
                0021316 ,
                0021460 ,
                0021464 ,
                0021504 ,
                0021542 ,
                0021934 ,
                0022738 ,
                0023397 ,
                0024390 ,
                0024456 ,
                0027839 ,
                0027870 ,
                0027873 ,
                0027951 ,
                0027955 ,
                0027956 ,
                0028276 ,
                0028277 ,
                0028278 ,
                0028279 ,
                0028316 ,
                0028317 ,
                0028318 ,
                0028319 ,
                0028320 ,
                0030407 ,
                0030431 ,
                0030488 ,
                0030493 ,
                0030494 ,
                0030495 ,
                0030496 ,
                0030941 ,
                0030948 ,
                0032940 ,
                0032941 ,
                0032942 ,
                0033282 ,
                0025063
            ";
            $arry_book_list['gcp_2013_2_3_7']="
                00002855 ,
                00002864 ,
                00002865 ,
                00002866 ,
                00002867 ,
                00002868 ,
                00002869 ,
                00002870 ,
                00002871 ,
                00005694 ,
                00005705 ,
                00005709 ,
                00005716 ,
                00005828 ,
                00005832 ,
                00005834 ,
                00005835 ,
                00005959 ,
                00006606 ,
                00006607 ,
                00010193 ,
                00010204 ,
                00011176 ,
                00014398 ,
                00017992 ,
                00018131 ,
                00019493 ,
                00019494 ,
                00019495 ,
                00019498 ,
                00019499 ,
                00019501 ,
                00019503 ,
                00019504 ,
                00019506 ,
                00019507 ,
                00019508 ,
                00019509 ,
                00019510 ,
                00019511 ,
                00019512 ,
                00020995 ,
                00020998 ,
                00021041 ,
                00000994 ,
                00000995 ,
                00001767 ,
                00001768 ,
                00001769 ,
                00001770 ,
                00001771 ,
                00001896 ,
                00006159 ,
                00006447 ,
                00006448 ,
                00006592 ,
                00009254 ,
                00009328 ,
                00009347 ,
                00009353 ,
                00009396 ,
                00009408 ,
                00009413 ,
                00009521 ,
                00009590 ,
                00009591 ,
                00009600 ,
                00010355 ,
                00010358 ,
                00010366 ,
                00010367 ,
                00010369 ,
                00011166 ,
                00011368 ,
                00011398 ,
                00011963 ,
                00012343 ,
                00012376 ,
                00012378 ,
                00014397 ,
                00014400 ,
                00014554 ,
                00016631 ,
                00016632 ,
                00016633 ,
                00016634 ,
                00016645 ,
                00016646 ,
                00016653 ,
                00018248 ,
                00018249 ,
                00018252 ,
                00018253 ,
                00018254 ,
                00018257 ,
                00018278 ,
                00018280 ,
                00018281 ,
                00018282 ,
                00018283 ,
                00018284 ,
                00018285 ,
                00018286 ,
                00018499 ,
                00018571 ,
                00019188 ,
                00019194 ,
                00019256 ,
                00019318 ,
                00019467 ,
                00019470 ,
                00021391 ,
                00021401 ,
                00021501 ,
                00021537 ,
                00021538 ,
                00023616 ,
                00024096 ,
                00024097 ,
                00025098 ,
                00027876 ,
                00027948 ,
                00027957 ,
                00027958 ,
                00027969 ,
                00028013 ,
                00028014 ,
                00028015 ,
                00030402 ,
                00030408 ,
                00030945 ,
                00031138 ,
                00031140 ,
                00031891 ,
                00032017 ,
                00034216 ,
                00034217 ,
                00044761
            ";

            $sql="
                SELECT
                    `book_library_code`,
                    `book_name`
                FROM `mssr_book_library` AS `tbl1`
                INNER JOIN (
                    SELECT
                        `book_sid`,
                        `cat_code`
                    FROM `mssr_book_category_rev`
                    WHERE 1=1
                        AND `school_code`='{$sess_school_code}'
                        AND `cat_code`   ='{$cat_code}'
                ) AS `tbl2`
                ON `tbl1`.`book_sid`=`tbl2`.`book_sid`
                WHERE 1=1
                    AND `tbl1`.`school_code`='{$sess_school_code}'
                    AND `tbl1`.`book_library_code` IN ($arry_book_list[$sess_class_code])
                    AND `tbl2`.`cat_code`='{$cat_code}'
                GROUP BY `tbl1`.`book_sid`
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(empty($arrys_result)){
                die();
            }

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,分店";
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
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <!-- 專屬 -->
    <!-- <link rel="stylesheet" type="text/css" href="inc/code.css" media="all" /> -->
    <script type="text/javascript" src="inc/code.js"></script>
    <script type="text/javascript" src="inc/add_action_branch_log/code.js"></script>

    <link rel="stylesheet" type="text/css" href="css/def.css" media="all" />

    <style>
        /* 容器微調 */
        #container, #content, #teacher_datalist_tbl{
            width:760px;
        }

        .number_bar{
             text-shadow:2px 0px 1px rgba(0,0,0,1),
                         0px -2px 1px rgba(0,0,0,1),
                         -2px 0px 1px rgba(0,0,0,1),
                         0px 2px 1px rgba(0,0,0,1),
                         2px 2px 1px rgba(0,0,0,1),
                         2px -2px 1px rgba(0,0,0,1),
                         -2px 2px 1px rgba(0,0,0,1),
                         -2px -2px 1px rgba(0,0,0,1);
            font-weight:bold;
            color:#FCFFFF;
            letter-spacing:0pt;

            font-size:12px;
            text-align:center;
            font-family:"微軟正黑體","標楷體","新細明體";
        }
    </style>
</Head>

<body>
    <?php if(!empty($arrys_result)):?>
    <table cellpadding="3" cellspacing="0" border="1" width="100%" rules='cols' style=''/>
        <tr align='center'>
            <!-- <td>
                <span style='' class='fc_red0 number_bar'>書本編號</span>
            </td> -->
            <!-- <td>
                <span style='' class='fc_red0 number_bar'>書名</span>
            </td> -->
        </tr>
        <?php foreach($arrys_result as $arry_result):?>
        <?php
            $book_library_code=trim($arry_result['book_library_code']);
            $book_name        =trim($arry_result['book_name']);
            if(mb_strlen($book_name)>20){
                $book_name=mb_substr($book_name,0,20)."..";
            }
        ?>
            <tr align='center'>
                <!-- <td>
                    <?php echo $book_library_code;?>
                </td> -->
                <td>
                    <?php echo $book_name;?>
                </td>
            </tr>
        <?php endforeach;?>
    </table>
    <?php endif;?>
</body>
</Html>