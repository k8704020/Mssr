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
    //Array
    //(
    //    [uid] => 5029
    //    [name] => 老師A
    //    [permission] => test_t_mssr
    //    [class] => Array
    //        (
    //            [0] => Array
    //                (
    //                    [0] => i_t
    //                    [1] => test_2013_1_1_1
    //                )
    //
    //        )
    //
    //    [school] => 明日學校
    //    [class_code] => test_2013_1_1_1
    //    [school_code] => test
    //    [sem_year] => 2013
    //    [sem_term] => 1
    //    [grade] => 1
    //    [ta_class] => 1
    //    [identity] => T
    //    [permission_id] => 1002
    //)
//$_SESSION['uid']=1258;
//$_SESSION['school_code']='gcp';
//$_SESSION['grade']=3;
//$_SESSION['class'][0][1]='gcp_2013_2_3_6';
//echo "<Pre>";
//print_r($_SESSION);
//echo "</Pre>";
//die();
//
//$_GET[trim('uid')]=1238;
        $sess_uid        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:0;
        $sess_class_code =(isset($_SESSION['class'][0][1]))?trim($_SESSION['class'][0][1]):'';
        $sess_school_code=(isset($_SESSION['school_code']))?trim($_SESSION['school_code']):'';
        $sess_sem_year   =(isset($_SESSION['sem_year']))?(int)$_SESSION['sem_year']:0;
        $sess_sem_term   =(isset($_SESSION['sem_term']))?(int)$_SESSION['sem_term']:0;
        $sess_grade      =(isset($_SESSION['grade']))?(int)$_SESSION['grade']:0;

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
    //branch_id     分店主索引

        //GET
        $uid          =(isset($_GET[trim('uid')]))?(int)$_GET[trim('uid')]:$sess_uid;
        $branch_id    =(isset($_GET[trim('branch_id')]))?$_GET[trim('branch_id')]:0;
        $zoom         =(isset($_GET[trim('zoom')]))?(int)$_GET[trim('zoom')]:0;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //branch_id     分店主索引

        $arry_err=array();

        if($branch_id===''){
           $arry_err[]='分店主索引,未輸入!';
        }else{
            $branch_id=(int)$branch_id;
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
                    if($sess_grade===0){
                        $sess_grade=(int)$arrys_result[0]['grade'];
                    }
                }
            }

        //-----------------------------------------------
        //更新使用者分店
        //-----------------------------------------------

            update_user_branch($conn_mssr,$uid,$arry_conn_mssr);

        //-----------------------------------------------
        //更新使用者任務清單
        //-----------------------------------------------

            update_user_task_inventory($conn_user,$conn_mssr,$uid,$APP_ROOT,$arry_conn_user,$arry_conn_mssr);

        //-----------------------------------------------
        //更新使用者分店營收紅利報表
        //-----------------------------------------------

            update_user_branch_revenue_bonus_log($conn_mssr,$uid,$sess_school_code,$arry_conn_mssr);

        //-----------------------------------------------
        //撈取是否為朋友
        //-----------------------------------------------

            $friend_ident=0;
            if($sess_uid!==$uid){
                $sql="
                    SELECT
                        `track_id`
                    FROM `mssr_track_user`
                    WHERE 1=1
                        AND `track_from` IN ({$sess_uid},{$uid})
                        AND `track_to` IN ({$sess_uid},{$uid})
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(!empty($arrys_result)){
                    $friend_ident=1;
                }
            }

        //-----------------------------------------------
        //撈取分店類型相關
        //-----------------------------------------------

            $sess_school_code   =mysql_prep($sess_school_code);

            //初始化, 階層陣列
            $arrys_category_info=array();
            $json_category_info =json_encode($arrys_category_info,true);

            $sql="
                SELECT
                    *
                FROM `mssr_book_category`
                WHERE 1=1
                    AND `mssr_book_category`.`cat1_id`<>1
                    AND `mssr_book_category`.`school_code`='{$sess_school_code}'
                    AND `mssr_book_category`.`cat_state`  ='啟用'
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($arrys_result)){
            //回填各階層相關陣列
                foreach($arrys_result as $inx=>$arry_result){
                    $cat1_id=(int)$arry_result['cat1_id'];
                    $cat2_id=(int)$arry_result['cat2_id'];
                    $cat3_id=(int)$arry_result['cat3_id'];

                    if(($cat2_id===1)&&($cat3_id===1)){
                        $lv_flag=1;
                        $arrys_category_info['lv1'][]=$arry_result;
                    }else if(($cat2_id!==1)&&($cat3_id===1)){
                        $lv_flag=2;
                        $arrys_category_info['lv2'][$cat1_id][]=$arry_result;
                    }else if(($cat2_id!==1)&&($cat3_id!==1)){
                        $lv_flag=3;
                        $arrys_category_info['lv3'][$cat2_id][]=$arry_result;
                    }else{
                        $lv_flag=0;
                        die("發生嚴重錯誤，請洽詢明日星球團隊人員!");
                    }
                }
                $json_category_info =json_encode($arrys_category_info,true);
            }
//echo "<Pre>";
//print_r($json_category_info);
//echo "</Pre>";
//die();
        //-----------------------------------------------
        //撈取分店開啟條件
        //-----------------------------------------------

            $sess_school_code=mysql_prep($sess_school_code);

            $sql="
                SELECT
                    `read_filter`,
                    `rec_filter`
                FROM `mssr_branch_open_filter`
                WHERE 1=1
                    AND `school_code`='{$sess_school_code}'
                    AND `grade`      = {$sess_grade      }
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($arrys_result)){
                $rs_read_filter=(int)$arrys_result[0]['read_filter'];
                $rs_rec_filter =(int)$arrys_result[0]['rec_filter'];
            }else{
                die();
            }

        //-----------------------------------------------
        //撈取使用者資訊
        //-----------------------------------------------

            $sql="
                SELECT
                    `user_coin`
                FROM `mssr_user_info`
                WHERE 1=1
                    AND `user_id`={$uid}
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(!empty($arrys_result)){
                $rs_user_coin=(int)$arrys_result[0]['user_coin'];
            }else{
                die();
            }

        //-----------------------------------------------
        //撈取分店資訊
        //-----------------------------------------------

            $arry_branch_lv_info=array();
            $json_branch_lv_info=json_encode($arry_branch_lv_info,true);

            $sql="
                SELECT
                    `branch_id`,
                    `branch_lv`
                FROM `mssr_branch`
                WHERE 1=1
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($arrys_result)){
                foreach($arrys_result as $arry_result){
                    $rs_branch_id=(int)$arry_result['branch_id'];
                    $rs_branch_lv=(int)$arry_result['branch_lv'];
                    $arry_branch_lv_info[$rs_branch_id]=$rs_branch_lv;
                }
                $json_branch_lv_info=json_encode($arry_branch_lv_info,true);
            }else{
                die();
            }

        //-----------------------------------------------
        //撈取分店階層開啟狀態
        //-----------------------------------------------

            $arry_branch_lv_ready_flag=array(
                1=>'true',
                2=>'true',
                3=>'true'
            );
            $json_branch_lv_ready_flag=json_encode($arry_branch_lv_ready_flag,true);

            $sql="
                SELECT
                    `mssr_branch`.`branch_lv`,
                    `mssr_user_branch`.`branch_state`
                FROM `mssr_user_branch`
                    INNER JOIN `mssr_branch` ON
                    `mssr_user_branch`.`branch_id`=`mssr_branch`.`branch_id`
                WHERE 1=1
                    AND `mssr_user_branch`.`user_id`={$uid}
                    AND `mssr_branch`.`branch_state`='啟用'
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($arrys_result)){
                foreach($arrys_result as $arry_result){
                    $rs_branch_lv   =(int)$arry_result['branch_lv'];
                    $rs_branch_state=trim($arry_result['branch_state']);
                    if($rs_branch_state!=='啟用'){
                        $arry_branch_lv_ready_flag[$rs_branch_lv]='false';
                        continue;
                    }
                }
                $json_branch_lv_ready_flag=json_encode($arry_branch_lv_ready_flag,true);
            }else{
                die();
            }

        //-----------------------------------------------
        //主SQL撈取
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_user_branch`.`user_id`,
                    `mssr_user_branch`.`branch_id`,
                    `mssr_user_branch`.`branch_rank`,
                    `mssr_user_branch`.`branch_cs`,
                    `mssr_user_branch`.`branch_nickname`,
                    `mssr_user_branch`.`branch_state`,

                    `mssr_branch`.`branch_lv`,
                    `mssr_branch`.`branch_name`,
                    `mssr_branch`.`branch_coordinate`
                FROM `mssr_user_branch`
                    INNER JOIN `mssr_branch` ON
                    `mssr_user_branch`.`branch_id`=`mssr_branch`.`branch_id`
                WHERE 1=1
                    AND `mssr_user_branch`.`user_id`={$uid}
                    AND `mssr_branch`.`branch_state`='啟用'
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($arrys_result)){
                $arrys_branch=$arrys_result;
                $json_branch=json_encode($arrys_branch,true);
            }else{
                die("DB_RESULT: QUERY FAIL!");
            }

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------
//echo "<Pre>";
//print_r($arrys_category_info);
//echo "</Pre>";
//die();
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
    <?php echo bing_analysis($allow=true);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <script type="text/javascript" src="../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../lib/jquery/plugin/func/block_ui/code.js"></script>
    <script type="text/javascript" src="../../lib/jquery/ui/code.js"></script>

    <script type="text/javascript" src="../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../lib/js/array/code.js"></script>

    <!-- 專屬 -->
    <!-- <link rel="stylesheet" type="text/css" href="inc/code.css" media="all" /> -->
    <script type="text/javascript" src="inc/code.js"></script>
    <script type="text/javascript" src="inc/add_action_branch_log/code.js"></script>

    <link rel="stylesheet" type="text/css" href="css/def.css" media="all" />

    <style>
        /* 微調 */
        body{
            overflow:hidden;
            position:relative;

            z-index:1;
        }

        #container{
            overflow:hidden;
            position:relative;
            background:#e2b563;

            width:1000px;
            height:480px;

            /*width:3872px;
            height:1855px;*/

            z-index:2;
        }

        #box{
            overflow:hidden;
            position:relative;

            top:-640px;
            left:-1480px;

            width:3872px;
            height:1855px;

            z-index:3;

            border:0px solid #ff0000;
        }

        #content{
            overflow:hidden;
            position:absolute;

            top:310px;
            left:740px;

            width:2536px;
            height:1212px;

            /*background:#e2b563 url('img/obj/background_1.jpg') no-repeat;*/

            border:0px solid #ff0000;

            z-index:4;
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

            font-size:22px;
            text-align:center;
            font-family:"微軟正黑體","標楷體","新細明體";
        }

        #light_box_branch_lv1{
            overflow:hidden;
            position:absolute;

            top:0px;
            left:0px;

            width:2536px;
            height:1212px;

            background:url('img/obj/branch_lv1.png') no-repeat;

            z-index:5;
        }

        #light_box_branch_lv2{
            overflow:hidden;
            position:absolute;

            top:0px;
            left:0px;

            width:2536px;
            height:1212px;

            background:url('img/obj/branch_lv2.png') no-repeat;

            z-index:5;
        }
    </style>
</Head>

<Body>
<?php
//echo "<Pre>";
//print_r($arry_branch_lv_ready_flag[1]);
//echo "</Pre>";
//echo "<Pre>";
//print_r($arry_branch_lv_ready_flag[2]);
//echo "</Pre>";
//echo "<Pre>";
//print_r($arry_branch_lv_ready_flag[3]);
//echo "</Pre>";
//echo "<Pre>";
//print_r($arrys_category_info);
//echo "</Pre>";
//die();
?>
    <!-- 容器區塊 開始 -->
    <div id="container">
        <input type="button" value="全景圖" style='position:absolute;z-index:999;' onclick='zoom("mt");void(0);'>
        <!-- <img id='background' src="img/obj/word.jpg" width="150px" height="100px" border="0"
        style='position:absolute;right:0;z-index:999;'/> -->
        <!-- 內容區塊 開始 -->
        <div id="box">
        <div id="content">
            <img id='background' src="img/obj/background_1.jpg" width="2536px" height="1212px" border="0"
            style='position:absolute;z-index:4;top:0;left:0;'/>

            <?php if(($arry_branch_lv_ready_flag[1]==='false')&&($arry_branch_lv_ready_flag[2]==='false')):?>
                <div id="light_box_branch_lv1"></div>
            <?php endif;?>

            <?php if(($arry_branch_lv_ready_flag[1]==='true')&&($arry_branch_lv_ready_flag[2]==='false')):?>
                <div id="light_box_branch_lv2"></div>
            <?php endif;?>

            <?php foreach($arrys_branch as $arry_branch):?>
            <?php
            //-------------------------------------------
            //參數
            //-------------------------------------------

                $rs_user_id                 =(int)$arry_branch['user_id'];
                $rs_branch_id               =(int)$arry_branch['branch_id'];
                $rs_branch_rank             =(int)$arry_branch['branch_rank'];
                $rs_branch_cs               =(int)$arry_branch['branch_cs'];
                $rs_branch_nickname         =trim($arry_branch['branch_nickname']);
                $rs_branch_state            =trim($arry_branch['branch_state']);
                $rs_branch_lv               =(int)$arry_branch['branch_lv'];
                $rs_branch_name             =trim($arry_branch['branch_name']);
                $rs_branch_coordinate       =trim($arry_branch['branch_coordinate']);

            //-------------------------------------------
            //定位
            //-------------------------------------------

                $rs_branch_position         =explode(",",$rs_branch_coordinate);
                $rs_branch_position_x       =(int)$rs_branch_position[0];
                $rs_branch_position_y       =(int)$rs_branch_position[1]+25;

                $rs_read_filter             =(int)$rs_read_filter;
                $rs_rec_filter              =(int)$rs_rec_filter;
                $rs_branch_open             =true;

            //-------------------------------------------
            //圖片
            //-------------------------------------------

                $rs_branch_img_src="";
                if(in_array($rs_branch_id,array(1,2,3,4,5,6))){
                //主題分店
                    if($rs_branch_state==='啟用'){
                        switch($rs_branch_rank){
                            case 1:
                                $rs_branch_img_src="img/branch/{$rs_branch_id}/branch_1.png";
                            break;
                            case 2:
                                $rs_branch_img_src="img/branch/{$rs_branch_id}/branch_2.png";
                            break;
                            case 3:
                                $rs_branch_img_src="img/branch/{$rs_branch_id}/branch_3.png";
                                if($rs_branch_id===1){
                                    $rs_branch_position_y=$rs_branch_position_y-150;
                                }else{
                                    $rs_branch_position_y=$rs_branch_position_y-50;
                                    if($rs_branch_id===3){
                                        $rs_branch_position_y=$rs_branch_position_y+20;
                                    }
                                    if($rs_branch_id===5){
                                        $rs_branch_position_y=$rs_branch_position_y-25;
                                    }
                                }
                            break;
                        }
                    }else{
                        $rs_branch_position_x=$rs_branch_position_x+50;
                        if($rs_branch_id===3){
                            $rs_branch_position_y=$rs_branch_position_y+50;
                        }
                        switch($rs_branch_lv){
                            case 2:
                                if($arry_branch_lv_ready_flag[1]==='true'){
                                    $rs_branch_img_src="img/branch/{$rs_branch_id}/car_999.png";
                                }else{
                                    $rs_branch_img_src="img/branch/{$rs_branch_id}/car_999_gray.png";
                                    $rs_branch_open=false;
                                }
                            break;

                            case 3:
                                if($arry_branch_lv_ready_flag[2]==='true'){
                                    $rs_branch_img_src="img/branch/{$rs_branch_id}/car_999.png";
                                }else{
                                    $rs_branch_img_src="img/branch/{$rs_branch_id}/car_999_gray.png";
                                    $rs_branch_open=false;
                                }
                            break;

                            default:
                                $rs_branch_img_src="img/branch/{$rs_branch_id}/car_999.png";
                            break;
                        }
                    }
                }else{
                    $rs_read_filter=(int)2;
                    if($rs_branch_state==='啟用'){
                        switch($rs_branch_rank){
                            case 1:
                                $rs_branch_img_src="img/branch/1/car_1.png";
                            break;
                            case 2:
                                $rs_branch_img_src="img/branch/2/car_2.png";
                            break;
                            case 3:
                                $rs_branch_img_src="img/branch/3/car_3.png";
                            break;
                        }
                    }else{
                        switch($rs_branch_lv){
                            case 2:
                                if($arry_branch_lv_ready_flag[1]==='true'){
                                    $rs_branch_img_src="img/branch/4/car_999.png";
                                }else{
                                    $rs_branch_img_src="img/branch/4/car_999_gray.png";
                                    $rs_branch_open=false;
                                }
                            break;

                            case 3:
                                if($arry_branch_lv_ready_flag[2]==='true'){
                                    $rs_branch_img_src="img/branch/4/car_999.png";
                                }else{
                                    $rs_branch_img_src="img/branch/4/car_999_gray.png";
                                    $rs_branch_open=false;
                                }
                            break;

                            default:
                                $rs_branch_img_src="img/branch/4/car_999.png";
                            break;
                        }
                    }
                }

            //-------------------------------------------
            //任務狀態
            //-------------------------------------------

                //---------------------------------------
                //進行中任務
                //---------------------------------------

                    $has_task_tmp='false';

                    if($rs_branch_state==='啟用'){
                        $sql="
                            SELECT
                                `user_id`
                            FROM `mssr_user_task_tmp`
                                INNER JOIN `mssr_task_period` ON
                                `mssr_user_task_tmp`.`task_sid`=`mssr_task_period`.`task_sid`
                            WHERE 1=1
                                AND `mssr_user_task_tmp`.`user_id`    ={$rs_user_id  }
                                AND `mssr_user_task_tmp`.`branch_id`  ={$rs_branch_id}

                                AND `mssr_task_period`.`task_state`   ='啟用'
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(!empty($arrys_result)){
                            $has_task_tmp='true';
                        }
                    }

                //---------------------------------------
                //可接任務
                //---------------------------------------

                    $task_img='';
                    $has_task_inventory='false';
                    if($rs_branch_state==='啟用'){
                        $sql="
                            SELECT
                                `mssr_user_task_inventory`.`task_coin_bonus`,
                                COUNT(`mssr_user_task_inventory`.`user_id`) AS `task_cno`
                            FROM `mssr_user_task_inventory`
                                INNER JOIN `mssr_task_period` ON
                                `mssr_user_task_inventory`.`task_sid`=`mssr_task_period`.`task_sid`
                            WHERE 1=1
                                AND `mssr_user_task_inventory`.`user_id`    ={$rs_user_id  }
                                AND `mssr_user_task_inventory`.`branch_id`  ={$rs_branch_id}

                                AND `mssr_task_period`.`task_state`         ='啟用'
                            GROUP BY `task_coin_bonus`
                        ";
                        $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        if(!empty($arrys_result)){
                            $has_task_inventory='true';
                            $task_cno=0;
                            foreach($arrys_result as $inx=>$arry_result){
                                $rs_task_coin_bonus =(int)$arry_result['task_coin_bonus'];
                                $rs_task_cno        =(int)$arry_result['task_cno'];
                                $task_cno=$task_cno+$rs_task_cno;
                            }
                            if($rs_task_coin_bonus===3){
                                $task_img='img/obj/task_2.png';
                            }elseif($rs_task_coin_bonus===1){
                                $task_img='img/obj/task_2.png';
                            }else{
                                $task_img='';
                            }
                        }
                    }
            ?>

            <!-- 書店名稱 -->
            <table cellpadding="0" cellspacing="0" border="0"
            style="position:absolute;left:<?php echo ($rs_branch_position_x);?>px;top:<?php echo ($rs_branch_position_y+20);?>px;z-index:99;"/>
                <tr>
                    <td align="center" class="number_bar" style='<?php if($rs_branch_state!=='啟用'){echo 'color:#838383;';}?>"'>
                        <?php echo htmlspecialchars($rs_branch_name);?>
                    </td>
                </tr>
            </table>

            <!-- 書店任務 -->
            <?php if($task_img!==''):?>
                <div style='position:absolute;left:<?php echo ($rs_branch_position_x)+50;?>px;top:<?php echo ($rs_branch_position_y+20);?>px;z-index:99;'>
                    <img src="<?php echo $task_img;?>" width="19" height="41" border="0" alt=""/>
                </div>
            <?php endif;?>

            <!-- 書店圖案 -->
            <table name='tbl_branch' cellpadding="0" cellspacing="0" border="0"
            style="position:absolute;left:<?php echo ($rs_branch_position_x);?>px;top:<?php echo ($rs_branch_position_y);?>px;z-index:98;"
            <?php if($rs_branch_open):?>
                onmouseover="mouse_over(this);void(0);"
                onclick="branch_ctrl(this,<?php echo $rs_user_id;?>,<?php echo $rs_branch_id;?>,
                '<?php echo $rs_branch_name;?>','<?php echo $rs_branch_state;?>'
                ,<?php echo $rs_read_filter;?>,<?php echo $rs_rec_filter;?>,'<?php echo $has_task_tmp;?>','<?php echo $has_task_inventory;?>'
                ,<?php echo $rs_branch_rank;?>,<?php echo $rs_branch_lv;?>);void(0);"
            <?php endif;?>
            />
                <tr>
                    <td>
                        <img src="<?php echo htmlspecialchars($rs_branch_img_src);?>" width="" height="" border="0"
                        alt="<?php echo htmlspecialchars($rs_branch_name);?>"/>
                    </td>
                </tr>
            </table>

            <?php endforeach;?>
        </div>
        </div>
        <!-- 內容區塊 結束 -->

        <!-- 開啟區塊 開始 -->
        <table id="tbl_block_view" align='center' cellpadding="0" cellspacing="0" border="0" width="100%" style="position:relative;display:none;"/>
            <tr>
                <td align="center" colspan="2"><span id="span_branch_name" class='number_bar'></span></td>
                <td align="center" class='number_bar'>書本推薦</td>
            </tr>
            <tr>
                <td align="center" colspan="2">&nbsp;</td>
                <td align="center" rowspan='7' style='' width='350px' height='250px'>
                    <iframe id="IFC" name="IFC" src="" frameborder="0"
                    style="width:90%;height:90%;overflow:hidden;overflow-y:auto"></iframe>
                </td>
            </tr>
            <tr>
                <td align='right' width='100px' class='number_bar' style='font-size:12pt;'>
                    閱讀本數
                    <img src="img/obj/open_bar_read.png" width="21px" height="17px" border="0" alt="閱讀本數"/>
                </td>
                <td align='left' style="position:relative;">
                    <span style="position:relative;display:block;top:7px;">
                        <img src="img/obj/open_bar.png" width="70px" height="17px" border="0" alt="能量條bar"
                        style="position:relative;z-index:3;top:13px;"/>

                        <img src="img/obj/open_bar_background.png" width="60px" height="10px" border="0" alt="能量條背景"
                        style="position:relative;left:4px;bottom:2px;z-index:1;"/>
                        <img src="img/obj/open_bar_energy.png" width="60px" height="10px" border="0" alt="能量條值"
                        style="position:relative;left:4px;bottom:15px;z-index:2;"/>
                    </span>

                    <span class="number_bar" id="read_cno" style="position:relative;left:13px;bottom:23px;font-size:14px;z-index:4;">
                        <img src="../../img/icon/loader.gif" width="15" height="15" border="0" alt="讀取中"/>
                    </span>
                    <span class="number_bar" id="read_total" style="position:relative;left:13px;bottom:23px;font-size:14px;z-index:4;">/0</span>

                    <input type="hidden" id="in_read_cno" name="" value="" size="" maxlength="">
                    <input type="hidden" id="in_read_total" name="" value="" size="" maxlength="">
                </td>
            </tr>
            <tr>
                <td align='right' width='100px' class='number_bar' style='font-size:12pt;'>
                    推薦本數
                    <img src="img/obj/open_bar_rec.png" width="21px" height="17px" border="0" alt="推薦本數"/>
                </td>
                <td align='left' style="position:relative;">
                    <span style="position:relative;display:block;top:7px">
                        <img src="img/obj/open_bar.png" width="70px" height="17px" border="0" alt="能量條bar"
                        style="position:relative;z-index:3;top:13px;"/>

                        <img src="img/obj/open_bar_background.png" width="60px" height="10px" border="0" alt="能量條背景"
                        style="position:relative;left:4px;bottom:2px;z-index:1;"/>
                        <img src="img/obj/open_bar_energy.png" width="60px" height="10px" border="0" alt="能量條值"
                        style="position:relative;left:4px;bottom:15px;z-index:2;"/>
                    </span>

                    <span class="number_bar" id="rec_cno" style="position:relative;left:13px;bottom:23px;font-size:14px;z-index:4;">
                        <img src="../../img/icon/loader.gif" width="15" height="15" border="0" alt="讀取中"/>
                    </span>
                    <span class="number_bar" id="rec_total" style="position:relative;left:13px;bottom:23px;font-size:14px;z-index:4;">/0</span>

                    <input type="hidden" id="in_rec_cno" name="" value="" size="" maxlength="">
                    <input type="hidden" id="in_rec_total" name="" value="" size="" maxlength="">
                </td>
            </tr>
            <tr>
                <td align="center" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td align="center" class='number_bar' style='font-size:12pt;'>
                    開店金額&nbsp;
                    <img src="img/obj/coin.png" width="11px" height="14px" border="0" alt="錢幣"/>
                </td>
                <td align="left" class='number_bar' style='font-size:12pt;'>
                    <span id='span_spent_coin'>
                        <img src="../../img/icon/loader.gif" width="15" height="15" border="0" alt="讀取中"/>
                    </span>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td align="center" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td align="center" colspan="3">
                    <input type="hidden" id="uid" name="uid" value="0" size="" maxlength="" tabindex="1">
                    <input type="hidden" id="branch_id" name="branch_id" value="0" size="" maxlength="" tabindex="2">
                    <input type="hidden" id="branch_name" name="branch_name" value="" size="" maxlength="" tabindex="14">
                    <input type="button" id="open_branch" value="開分店" tabindex="3" onclick="void(0);"
                    style="display:none;">
                    <input type="button" value="關閉" tabindex="4" onclick="$.unblockUI();void(0);">
                </td>
            </tr>
        </table>
        <!-- 開啟區塊 結束 -->

        <!-- 進入分店 開始 -->
        <table id="tbl_block_go" cellpadding="0" cellspacing="0" border="0" width="100%"/>
            <tr>
                <td><span class="span_branch_name number_bar"></span></td>
            </tr>
            <tr><td align="center">&nbsp;</td></tr>
            <tr>
                <td>
                    <input type="hidden" class='uid' value="0" size="" maxlength="" tabindex="5">
                    <input type="hidden" class='branch_id' value="0" size="" maxlength="" tabindex="6">
                    <input type="button" value="進入" tabindex="7" onclick="go_branch();void(0);">
                    <input type="button" value="取消" tabindex="8" onclick="$.unblockUI();void(0);">
                    <input type="hidden" class='has_task_tmp' value="false" size="" maxlength="" tabindex="9">
                    <input type="hidden" class='has_task_inventory' value="false" size="" maxlength="" tabindex="10">

                    <input type="hidden" class='branch_rank' value="0" size="" maxlength="" tabindex="11">
                    <input type="hidden" class='branch_lv' value="0" size="" maxlength="" tabindex="12">
                    <input type="hidden" class='branch_name' value="" size="" maxlength="" tabindex="13">
                </td>
            </tr>
        </table>
        <!-- 進入分店 結束 -->

    </div>
    <!-- 容器區塊 結束 -->

<input type="text" id="draggable_flag" value='0' style='display:none;'>
<table id='tbl_ifc' cellpadding="0" cellspacing="0" border="1" width="1000px" height='480px' style='display:none;'/>
    <tr>
        <td id='td_ifc'>

        </td>
    </tr>
</table>

</Body>
<?php
//echo "<Pre>";
//print_r($arrys_category_info);
//echo "</Pre>";
?>
<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //---------------------------------------------------
    //參數
    //---------------------------------------------------

        var uid                      =<?php echo (int)$uid;?>;
        var branch_id                =<?php echo (int)$branch_id;?>;
        var zoom_flag                =<?php echo (int)$zoom;?>;
        var sess_school_code         ='<?php echo trim($sess_school_code);?>';
        var rs_user_coin             =<?php echo (int)($rs_user_coin);?>;
        var json_branch_lv_info      =<?php echo $json_branch_lv_info;?>;
        var json_branch_lv_ready_flag=<?php echo $json_branch_lv_ready_flag;?>;
        var json_category_info       =<?php echo $json_category_info;?>;

    //---------------------------------------------------
    //物件
    //---------------------------------------------------

        var otbl_ifc            =document.getElementById('tbl_ifc');
        var otd_ifc             =document.getElementById('td_ifc');

        var ocontainer          =document.getElementById('container');
        var ocontent            =document.getElementById('content');
        var otbl_block_view     =document.getElementById('tbl_block_view');
        var otbl_block_go       =document.getElementById('tbl_block_go');
        var ospan_branch_name   =document.getElementById('span_branch_name');

        var ouid                =document.getElementById('uid');
        var obranch_id          =document.getElementById('branch_id');
        var obranch_name        =document.getElementById('branch_name');

        var oread_cno           =document.getElementById('read_cno');
        var orec_cno            =document.getElementById('rec_cno');
        var oread_total         =document.getElementById('read_total');
        var orec_total          =document.getElementById('rec_total');

        var oin_read_cno        =document.getElementById('in_read_cno');
        var oin_rec_cno         =document.getElementById('in_rec_cno');
        var oin_read_total      =document.getElementById('in_read_total');
        var oin_rec_total       =document.getElementById('in_rec_total');

        var Oopen_branch        =document.getElementById('open_branch');

        var ospan_spent_coin    =document.getElementById('span_spent_coin');

        var otbl_branchs        =document.getElementsByName('tbl_branch');

        var odraggable_flag     =document.getElementById('draggable_flag');

        var oIFC                =document.getElementById('IFC');

    //---------------------------------------------------
    //函式
    //---------------------------------------------------

        function zoom(flag){
            //動作
            $.blockUI({
                message:$(otbl_ifc),
                css: {
                    border: 'none',
                    padding: '0px',
                    backgroundColor: '#ffffff',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: 1,
                    color: '#000000',
                    top:'0',
                    left:'0',
                    width: '1000px',
                    height: '480px'
                },
                overlayCSS:{
                    backgroundColor:'#000000',
                    opacity:0.9,
                    cursor:'default'
                }
            });
            var _html='';
            _html+='<iframe id="IFC" name="IFC" src="zoom.php?uid=<?php echo $uid;?>" frameborder="0"';
            _html+='style="width:1000px;height:480px;overflow:hidden;"></iframe>';
            $(otd_ifc).html(_html);

            if(trim(flag)==='mt'){
                <?php if($sess_uid!==$uid):?>
                    //action_log
                    add_action_branch_log(
                        'inc/add_action_branch_log/code.php',
                        'la03',
                        <?php echo $sess_uid;?>,
                        <?php echo $uid;?>,
                        '',
                        <?php echo $friend_ident;?>,
                        0,
                        '',
                        0,
                        0,
                        0,
                        0,
                        0,
                        '',
                        ''
                    );
                <?php else:?>
                    //action_log
                    add_action_branch_log(
                        'inc/add_action_branch_log/code.php',
                        'r02',
                        <?php echo $sess_uid;?>,
                        <?php echo $uid;?>,
                        '',
                        0,
                        0,
                        '',
                        0,
                        0,
                        0,
                        0,
                        0,
                        '',
                        ''
                    );
                <?php endif;?>
            }
        }

        function go_branch(){
        //進入分店
            var user_id             =parseInt($('.uid')[0].value);
            var branch_id           =parseInt($('.branch_id')[0].value);
            var has_task_tmp        =trim($('.has_task_tmp')[0].value);
            var has_task_inventory  =trim($('.has_task_inventory')[0].value);
            var branch_rank         =trim($('.branch_rank')[0].value);
            var branch_lv           =trim($('.branch_lv')[0].value);
            var branch_name         =trim($('.branch_name')[0].value);

            if(has_task_inventory==='true'){
                var task_ident=1;
            }else{
                var task_ident=0;
            }
            if(has_task_tmp==='true'){
                var task_state=1;
            }else{
                var task_state=0;
            }

            if(branch_id===1){
                <?php if($sess_uid===$uid):?>
                    var branch_path='../bookstore/bookstory_outside/code.php';
                <?php else:?>
                    var branch_path='../bookstore/bookstory_outside/code.php?id=<?php echo $uid;?>';
                <?php endif;?>
                //action_log
                add_action_branch_log(
                    'inc/add_action_branch_log/code.php',
                    'b01',
                    <?php echo $sess_uid;?>,
                    <?php echo $uid;?>,
                    '',
                    0,
                    1,
                    '啟用',
                    3,
                    1,
                    0,
                    0,
                    0,
                    '',
                    branch_path
                );
                return true;
            }else{
                var branch_path='../branch_inside/index.php?user_id='+user_id+'&branch_id='+branch_id;
            }

            for(key1 in json_category_info['lv1']){

                var cat_id  =parseInt(json_category_info['lv1'][key1]['cat_id']);
                var cat_name=trim(json_category_info['lv1'][key1]['cat_name']);

                if(cat_name===branch_name){
                    //action_log
                    add_action_branch_log(
                        'inc/add_action_branch_log/code.php',
                        'b01',
                        <?php echo $sess_uid;?>,
                        <?php echo $uid;?>,
                        '',
                        0,
                        branch_id,
                        '啟用',
                        branch_rank,
                        branch_lv,
                        task_ident,
                        task_state,
                        cat_id,
                        '',
                        branch_path
                    );
                }
            }

            var lv2_in_flag=false;
            for(key2 in json_category_info['lv2']){
                var key2=parseInt(key2);
                for(key3 in json_category_info['lv2'][key2]){
                    var cat_id  =parseInt(json_category_info['lv2'][key2][key3]['cat_id']);
                    var cat_name=trim(json_category_info['lv2'][key2][key3]['cat_name']);

                    if(cat_name===branch_name){
                        //action_log
                        add_action_branch_log(
                            'inc/add_action_branch_log/code.php',
                            'b01',
                            <?php echo $sess_uid;?>,
                            <?php echo $uid;?>,
                            '',
                            0,
                            branch_id,
                            '啟用',
                            branch_rank,
                            branch_lv,
                            0,
                            0,
                            key2,
                            '',
                            ''
                        );

                        if(!lv2_in_flag){
                            add_action_branch_log(
                                'inc/add_action_branch_log/code.php',
                                'b01',
                                <?php echo $sess_uid;?>,
                                <?php echo $uid;?>,
                                '',
                                0,
                                branch_id,
                                '啟用',
                                branch_rank,
                                branch_lv,
                                task_ident,
                                task_state,
                                cat_id,
                                '',
                                branch_path
                            );
                            lv2_in_flag=true;
                        }
                    }
                }
            }
            return true;
        }

        Oopen_branch.onclick=function(){
        //開分店
            var user_id     =parseInt(this.getAttribute('user_id'));
            var branch_id   =parseInt(this.getAttribute('branch_id'));
            var branch_name =trim(this.getAttribute('branch_name'));
            var branch_lv   =parseInt(json_branch_lv_info[branch_id]);
            var branch_path ='open_branch.php?user_id='+user_id+'&branch_id='+branch_id;

            if(branch_lv>1){
                var lv_ready_flag=trim(json_branch_lv_ready_flag[branch_lv-1]);
                if(lv_ready_flag!=='true'){
                    alert('上一環的分店尚未全部開啟喔!');
                    return false;
                }
            }

            for(key1 in json_category_info['lv1']){

                var cat_id  =parseInt(json_category_info['lv1'][key1]['cat_id']);
                var cat_name=trim(json_category_info['lv1'][key1]['cat_name']);

                if(cat_name===branch_name){
                    //action_log
                    add_action_branch_log(
                        'inc/add_action_branch_log/code.php',
                        'b03',
                        <?php echo $sess_uid;?>,
                        <?php echo $uid;?>,
                        '',
                        0,
                        branch_id,
                        '',
                        0,
                        0,
                        0,
                        0,
                        cat_id,
                        '',
                        branch_path
                    );
                }
            }

            var lv2_in_flag=false;
            for(key2 in json_category_info['lv2']){
                var key2=parseInt(key2);
                for(key3 in json_category_info['lv2'][key2]){
                    var cat_id  =parseInt(json_category_info['lv2'][key2][key3]['cat_id']);
                    var cat_name=trim(json_category_info['lv2'][key2][key3]['cat_name']);

                    if(cat_name===branch_name){
                        //action_log
                        add_action_branch_log(
                            'inc/add_action_branch_log/code.php',
                            'b03',
                            <?php echo $sess_uid;?>,
                            <?php echo $uid;?>,
                            '',
                            0,
                            branch_id,
                            '',
                            0,
                            0,
                            0,
                            0,
                            key2,
                            '',
                            ''
                        );

                        if(!lv2_in_flag){
                            add_action_branch_log(
                                'inc/add_action_branch_log/code.php',
                                'b03',
                                <?php echo $sess_uid;?>,
                                <?php echo $uid;?>,
                                '',
                                0,
                                branch_id,
                                '',
                                0,
                                0,
                                0,
                                0,
                                cat_id,
                                '',
                                branch_path
                            );
                            lv2_in_flag=true;
                        }
                    }
                }
            }
        }

        function block_go(user_id,branch_id,branch_name,has_task_tmp,has_task_inventory,branch_rank,branch_lv){
        //進入分店

            var user_id             =parseInt(user_id);
            var branch_id           =parseInt(branch_id);
            var branch_name         =trim(branch_name);
            var has_task_tmp        =trim(has_task_tmp);
            var has_task_inventory  =trim(has_task_inventory);
            var branch_rank         =parseInt(branch_rank);
            var branch_lv           =parseInt(branch_lv);

            //物件
            if(branch_id!==1){
                $('.span_branch_name').html(branch_name+'分店');
            }else{
                $('.span_branch_name').html(branch_name);
            }
            $('.uid')[0].value=user_id;
            $('.branch_id')[0].value=branch_id;
            $('.has_task_tmp')[0].value=has_task_tmp;
            $('.has_task_inventory')[0].value=has_task_inventory;
            $('.branch_rank')[0].value=branch_rank;
            $('.branch_lv')[0].value=branch_lv;
            $('.branch_name')[0].value=branch_name;

            //動作
            $.blockUI({
                message:$(otbl_block_go),
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#ffffff',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: 1,
                    color: '#ffffff',
                    top:'15%',
                    left:($(ocontainer).width() - 230) /2 + 'px',
                    width: '200px'
                },
                overlayCSS:{
                    backgroundColor:'#000000',
                    opacity:0.6,
                    cursor:'default'
                }
            });
        }

        function block_ui(user_id,branch_id,branch_name,branch_state,branch_open_read_filter,branch_open_rec_filter){
        //遮罩

            //參數
            var user_id                 =parseInt(user_id);
            var branch_id               =parseInt(branch_id);
            var branch_name             =trim(branch_name);
            var branch_state            =trim(branch_state);
            var branch_open_read_filter =parseInt(branch_open_read_filter);
            var branch_open_rec_filter  =parseInt(branch_open_rec_filter);

            //物件
            ospan_branch_name.innerHTML=branch_name+'分店';
            ouid.value=user_id;
            obranch_id.value=branch_id;
            obranch_name.value=branch_name;

            oread_total.innerHTML='/'+branch_open_read_filter;
            orec_total.innerHTML ='/'+branch_open_rec_filter;

            oin_read_total.value=branch_open_read_filter;
            oin_rec_total.value=branch_open_rec_filter;
            oIFC.src='book_list.php?branch_name='+trim(branch_name);

            //查詢分店資訊
            branch_chk();

            //動作
            $.blockUI({
                message:$(otbl_block_view),
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#ffffff',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: 1,
                    color: '#ffffff',
                    top:'10%',
                    left:($(ocontainer).width() - 590) /2 + 'px',
                    width: '510px'
                },
                overlayCSS:{
                    backgroundColor:'#000000',
                    opacity:0.6,
                    cursor:'default'
                }
            });
        }

        function branch_chk(){
        //查詢分店資訊

            var user_id     =parseInt(ouid.value);
            var branch_id   =parseInt(obranch_id.value);
            var branch_name =trim(obranch_name.value);

            //ajax參數
            var $url        ="ajax/get_branch_info.php";
            var $type       ="POST";
            var $datatype   ="json";

            $.ajax({
            //參數設置
                async      :true,
                cache      :false,
                global     :true,
                timeout    :500000,
                contentType:"application/x-www-form-urlencoded; charset=UTF-8",

                url        :$url,
                type       :$type,
                datatype   :$datatype,
                data       :{
                    school_code :encodeURI(trim(sess_school_code)),
                    user_id     :encodeURI(parseInt(user_id     )),
                    branch_id   :encodeURI(parseInt(branch_id   ))
                },

            //事件
                beforeSend  :function(){
                //傳送前處理
                    $(Oopen_branch).hide();
                    oread_cno.innerHTML ='<img src="../../img/icon/loader.gif" width="15" height="15" border="0" alt="讀取中"/>';
                    orec_cno.innerHTML  ='<img src="../../img/icon/loader.gif" width="15" height="15" border="0" alt="讀取中"/>';
                    ospan_spent_coin.innerHTML='<img src="../../img/icon/loader.gif" width="15" height="15" border="0" alt="讀取中"/>';
                },
                success     :function(respones){
                //成功處理

                    var branch_lv       =parseInt(json_branch_lv_info[branch_id]);
                    var respones        =jQuery.parseJSON(respones);
                    var json_read_cno   =parseInt(respones['read_cno']);
                    var json_rec_cno    =parseInt(respones['rec_cno']);
                    var branch_show_flag=true;

                    oread_cno.innerHTML =json_read_cno;
                    orec_cno.innerHTML  =json_rec_cno;
                    oin_read_cno.value  =json_read_cno;
                    oin_rec_cno.value   =json_rec_cno;

                    var in_read_total   =parseInt(oin_read_total.value);
                    var in_rec_total    =parseInt(oin_rec_total.value);

                    if(branch_lv===1){
                        ospan_spent_coin.innerHTML=0+' 元';
                    }else if(branch_lv===2){
                        ospan_spent_coin.innerHTML=100+' 元';
                        if(parseInt(rs_user_coin)<100){
                            ospan_spent_coin.innerHTML='<span class="fc_red0">'+100+' 元'+'</span>';
                            branch_show_flag=false;
                        }
                    }else if(branch_lv===3){
                        ospan_spent_coin.innerHTML=100+' 元';
                        if(parseInt(rs_user_coin)<100){
                            ospan_spent_coin.innerHTML='<span class="fc_red0">'+100+' 元'+'</span>';
                            branch_show_flag=false;
                        }
                    }else{
                        branch_show_flag=false;
                    }

                    if((json_read_cno<in_read_total)||(json_rec_cno<in_rec_total)){
                        branch_show_flag=false;
                    }

                    //顯示開店按鈕與否
                    if(!branch_show_flag){
                        $(Oopen_branch).hide();
                    }else{
                        <?php if($sess_uid===$uid):?>
                            $(Oopen_branch).show();
                        <?php endif;?>
                        Oopen_branch.setAttribute('user_id',user_id);
                        Oopen_branch.setAttribute('branch_id',branch_id);
                        Oopen_branch.setAttribute('branch_name',branch_name);
                    }
                },
                error       :function(xhr, ajaxoptions, thrownerror){
                //失敗處理
                    if(ajaxoptions==='timeout'){

                    }else{

                    }
                },
                complete    :function(){
                //傳送後處理
                }
            });
        }

        function branch_ctrl(obj,user_id,branch_id,branch_name,branch_state,branch_open_read_filter,branch_open_rec_filter,has_task_tmp,has_task_inventory,branch_rank,branch_lv){
        //分店操作

            odraggable_flag.value=parseInt(odraggable_flag.value)+3;

            var user_id                 =parseInt(user_id);
            var branch_id               =parseInt(branch_id);
            var branch_name             =trim(branch_name);
            var branch_state            =trim(branch_state);
            var branch_open_read_filter =parseInt(branch_open_read_filter);
            var branch_open_rec_filter  =parseInt(branch_open_rec_filter);
            var has_task_tmp            =trim(has_task_tmp);
            var has_task_inventory      =trim(has_task_inventory);
            var branch_rank             =parseInt(branch_rank);
            var branch_lv               =parseInt(branch_lv);

            if(parseInt(odraggable_flag.value)===3){
                switch(branch_state){

                    case '啟用':
                        block_go(user_id,branch_id,branch_name,has_task_tmp,has_task_inventory,branch_rank,branch_lv);
                    break;

                    default:
                        block_ui(user_id,branch_id,branch_name,branch_state,branch_open_read_filter,branch_open_rec_filter);

                        //action_log
                        for(key1 in json_category_info['lv1']){

                            var cat_id  =parseInt(json_category_info['lv1'][key1]['cat_id']);
                            var cat_name=trim(json_category_info['lv1'][key1]['cat_name']);

                            if(cat_name===branch_name){
                                //action_log
                                add_action_branch_log(
                                    'inc/add_action_branch_log/code.php',
                                    'b04',
                                    <?php echo $sess_uid;?>,
                                    <?php echo $uid;?>,
                                    '',
                                    0,
                                    branch_id,
                                    '',
                                    0,
                                    0,
                                    0,
                                    0,
                                    cat_id,
                                    '',
                                    ''
                                );
                            }
                        }

                        var lv2_in_flag=false;
                        for(key2 in json_category_info['lv2']){
                            var key2=parseInt(key2);
                            for(key3 in json_category_info['lv2'][key2]){
                                var cat_id  =parseInt(json_category_info['lv2'][key2][key3]['cat_id']);
                                var cat_name=trim(json_category_info['lv2'][key2][key3]['cat_name']);

                                if(cat_name===branch_name){
                                    //action_log
                                    add_action_branch_log(
                                        'inc/add_action_branch_log/code.php',
                                        'b04',
                                        <?php echo $sess_uid;?>,
                                        <?php echo $uid;?>,
                                        '',
                                        0,
                                        branch_id,
                                        '',
                                        0,
                                        0,
                                        0,
                                        0,
                                        key2,
                                        '',
                                        ''
                                    );

                                    if(!lv2_in_flag){
                                        add_action_branch_log(
                                            'inc/add_action_branch_log/code.php',
                                            'b04',
                                            <?php echo $sess_uid;?>,
                                            <?php echo $uid;?>,
                                            '',
                                            0,
                                            branch_id,
                                            '',
                                            0,
                                            0,
                                            0,
                                            0,
                                            cat_id,
                                            '',
                                            ''
                                        );
                                        lv2_in_flag=true;
                                    }
                                }
                            }
                        }
                    break;
                }
            }
            odraggable_flag.value=parseInt(0);
        }

        function initialize_move(obj){
        //初始化, 畫面起始位置函式

            $(obj).animate({
                //top:"-320px",
                //left:"-740px"
            },0);

            return false;
        }

        function mouse_over(obj){
        //初始化, 滑鼠移入函式
            obj.style.cursor='pointer';
            return false;
        }

    //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        $(function(){

            //action_log
            add_action_branch_log(
                'inc/add_action_branch_log/code.php',
                'r01',
                <?php echo $sess_uid;?>,
                <?php echo $uid;?>,
                '',
                0,
                0,
                '',
                0,
                0,
                0,
                0,
                0,
                '',
                ''
            );

            if(parseInt(zoom_flag)===1){
                zoom("at");
            }

            ////初始化, 畫面起始位置函式
            //initialize_move(ocontent);

            //初始化, 禁止滑鼠事件
            $(document).on("mousewheel DOMMouseScroll", function(e){
                e.preventDefault();
                return false;
            }).dblclick(function(e){
                e.preventDefault();
                return false;
            });

            //初始化, 拖曳設置
            $(ocontent).draggable({
                containment: "#box",
                stack: ".drag",
                start: function(e){
                },
                stop: function(e){
                    //alert('stop');
                    //e.preventDefault();
                    //return false;
                    //for(var i=0;i<otbl_branchs.length;i++){
                    //    var otbl_branch=otbl_branchs[i];
                    //    otbl_branch.onclick=function(){
                    //        alert(123);
                    //    }
                    //}
                    odraggable_flag.value=parseInt(odraggable_flag.value)+1;
                }
            });
            $(ocontent).mouseup(function(){
                //alert(123);
                odraggable_flag.value=parseInt(0);
            });

            $(Oopen_branch).hide();
        });

</script>

<Html>