<?php
//-------------------------------------------------------
//明日聊書
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",3).'config/config.php');
        require_once(str_repeat("../",1).'config/config.php');

        //外掛頁面檔
        require_once(str_repeat("../",1).'pages/code.php');

        //外掛函式檔
        $funcs=array(
            APP_ROOT.'inc/code',
            APP_ROOT.'service/forum_global/inc/code'
        );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

    //---------------------------------------------------
    //有無登入,SESSION
    //---------------------------------------------------

        if(isset($_SESSION['mssr_forum_global']['uid'])&&isset($_SESSION['mssr_forum_global']['country_code'])){
            $sess_country_code    =trim($_SESSION['mssr_forum_global']['country_code']);
            $arry_conn_user       ="arry_conn_user_{$sess_country_code}";
            $arry_conn_user       =$$arry_conn_user;
            $arrys_sess_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
            if(empty($arrys_sess_login_info)){
                $msg="請先登入!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        location.href='login.php';
                    </script>
                ";
                die($jscript_back);
            }
        }else{
            $msg="請先登入!";
            $jscript_back="
                <script>
                    alert('{$msg}');
                    location.href='login.php';
                </script>
            ";
            die($jscript_back);
        }

        if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
        if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
        if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
        if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
        if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
        if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
        if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
        if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
        if(isset($_SESSION['mssr_forum_global']['country_code']))$sess_country_code=trim($_SESSION['mssr_forum_global']['country_code']);
        if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
        if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
            $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
            foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
            }
        }

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------

        $user_id=(isset($_GET['user_id']))?(int)$_GET['user_id']:(int)$sess_user_id;
        $account=(isset($_GET['account']))?trim($_GET['account']):trim($sess_account);
        $tab    =(isset($_GET['tab']))?(int)$_GET['tab']:1;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

        $arry_err=array();

        if($tab===0){
            $tab=1;
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
        //連線物件
        //-----------------------------------------------

            //$arry_conn_mssr=get_conn_country($user_id,$account);
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
            $arry_conn_mssr_country=get_conn_country($user_id,$account);
            $conn_mssr_country=conn($db_type='mysql',$arry_conn_mssr_country);
            //$conn_country_code=trim($conn_host_country_code[$arry_conn_mssr_country['db_host']]);
            $conn_country_code=trim($arry_conn_mssr_country['db_country']);

        //-----------------------------------------------
        //page_info SQL
        //-----------------------------------------------

            $sql="
                SELECT `name`,`sex`
                FROM `user`.`member`
                WHERE 1=1
                    AND `user`.`member`.`uid`={$user_id}
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr_country,$sql,array(0,1),$arry_conn_mssr_country);
            $user_img  ='';
            $user_name ='';
            $user_sex  =1;
            if(!empty($db_results)){
                $user_name=trim($db_results[0]['name']);
                $user_sex =(int)$db_results[0]['sex'];
                if($user_sex===1)$user_img='../img/default/user_boy.png';
                if($user_sex===2)$user_img='../img/default/user_girl.png';
            }else{die();}


            //$sql="
            //    SELECT
            //        COUNT(*) AS `cno`
            //    FROM `mssr_forum_global`.`mssr_forum_article`
            //    WHERE 1=1
            //        AND `mssr_forum_global`.`mssr_forum_article`.`user_id`={$user_id}
            //";
            //$add_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            //$add_article_cno=(int)($add_article_results[0]['cno']);
            //
            //
            //$sql="
            //    SELECT
            //        COUNT(*) AS `cno`
            //    FROM `mssr_forum_global`.`mssr_forum_reply`
            //    WHERE 1=1
            //        AND `mssr_forum_global`.`mssr_forum_reply`.`user_id`={$user_id}
            //";
            //$reply_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            //$reply_article_cno=(int)($reply_article_results[0]['cno']);
            //
            //$sql="
            //        SELECT `user`.`school`.`school_code`, `user`.`school`.`school_name`, `user`.`class`.`grade`, `user`.`class_name`.`class_name`
            //        FROM `user`.`class`
            //            INNER JOIN `user`.`class_name` ON
            //            `user`.`class`.`class_category`=`user`.`class_name`.`class_category`
            //            INNER JOIN `user`.`student` ON
            //            `user`.`class`.`class_code`=`user`.`student`.`class_code`
            //            INNER JOIN `user`.`semester` ON
            //            `user`.`class`.`semester_code`=`user`.`semester`.`semester_code`
            //            INNER JOIN `user`.`school` ON
            //            `user`.`semester`.`school_code`=`user`.`school`.`school_code`
            //        WHERE 1=1
            //            AND `user`.`student`.`uid`={$user_id}
            //            AND `user`.`class`.`classroom` =`user`.`class_name`.`classroom`
            //            AND `user`.`student`.`start`<=NOW()
            //            AND `user`.`student`.`end`  >=NOW()
            //        GROUP BY `user`.`class`.`class_code`
            //
            //    UNION
            //
            //        SELECT `user`.`school`.`school_code`, `user`.`school`.`school_name`, `user`.`class`.`grade`, `user`.`class_name`.`class_name`
            //        FROM `user`.`class`
            //            INNER JOIN `user`.`class_name` ON
            //            `user`.`class`.`class_category`=`user`.`class_name`.`class_category`
            //            INNER JOIN `user`.`teacher` ON
            //            `user`.`class`.`class_code`=`user`.`teacher`.`class_code`
            //            INNER JOIN `user`.`semester` ON
            //            `user`.`class`.`semester_code`=`user`.`semester`.`semester_code`
            //            INNER JOIN `user`.`school` ON
            //            `user`.`semester`.`school_code`=`user`.`school`.`school_code`
            //        WHERE 1=1
            //            AND `user`.`teacher`.`uid`={$user_id}
            //            AND `user`.`class`.`classroom` =`user`.`class_name`.`classroom`
            //            AND `user`.`teacher`.`start`<=NOW()
            //            AND `user`.`teacher`.`end`  >=NOW()
            //        GROUP BY `user`.`class`.`class_code`
            //";
            //$arry_user_school_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            //$user_school_code='';
            //if(!empty($arry_user_school_results))$user_school_code=trim($arry_user_school_results[0]['school_code']);

        //-----------------------------------------------
        //討論 SQL
        //-----------------------------------------------

            $sql="
                    SELECT
                        `user`.`member`.`name`,

                        `mssr_forum_global`.`mssr_forum_article_book_rev`.`book_sid`,

                        `mssr_forum_global`.`mssr_forum_article`.`user_id`,
                        `mssr_forum_global`.`mssr_forum_article`.`group_id`,
                        `mssr_forum_global`.`mssr_forum_article`.`article_id`,
                        0 AS `reply_id`,
                        `mssr_forum_global`.`mssr_forum_article`.`keyin_cdate`,
                        `mssr_forum_global`.`mssr_forum_article`.`article_like_cno` AS `like_cno`,

                        `mssr_forum_global`.`mssr_forum_article_detail`.`article_title`,
                        `mssr_forum_global`.`mssr_forum_article_detail`.`article_content`,
                        '' AS `reply_content`,
                        'article' AS `type`
                    FROM `mssr_forum_global`.`mssr_forum_article_book_rev`
                        INNER JOIN `mssr_forum_global`.`mssr_forum_article` ON
                        `mssr_forum_global`.`mssr_forum_article_book_rev`.`article_id`=`mssr_forum_global`.`mssr_forum_article`.`article_id`

                        INNER JOIN `mssr_forum_global`.`mssr_forum_article_detail` ON
                        `mssr_forum_global`.`mssr_forum_article`.`article_id`=`mssr_forum_global`.`mssr_forum_article_detail`.`article_id`

                        INNER JOIN `user`.`member` ON
                        `mssr_forum_global`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                    WHERE 1=1
                        AND `mssr_forum_global`.`mssr_forum_article`.`article_from` IN (1,2) -- 文章來源
                        AND `mssr_forum_global`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                        AND `mssr_forum_global`.`mssr_forum_article`.`user_id`      ={$user_id}
                        AND `mssr_forum_global`.`mssr_forum_article`.`user_country_code`='{$conn_country_code}'

                UNION ALL

                    SELECT
                        `user`.`member`.`name`,

                        `mssr_forum_global`.`mssr_forum_reply_book_rev`.`book_sid`,

                        `mssr_forum_global`.`mssr_forum_reply`.`user_id`,
                        `mssr_forum_global`.`mssr_forum_reply`.`group_id`,
                        `mssr_forum_global`.`mssr_forum_reply`.`article_id`,
                        `mssr_forum_global`.`mssr_forum_reply`.`reply_id`,
                        `mssr_forum_global`.`mssr_forum_reply`.`keyin_cdate`,
                        `mssr_forum_global`.`mssr_forum_reply`.`reply_like_cno` AS `like_cno`,

                        `mssr_forum_global`.`mssr_forum_article_detail`.`article_title`,
                        `mssr_forum_global`.`mssr_forum_article_detail`.`article_content`,
                        `mssr_forum_global`.`mssr_forum_reply_detail`.`reply_content`,

                        'reply' AS `type`
                    FROM `mssr_forum_global`.`mssr_forum_reply_book_rev`
                        INNER JOIN `mssr_forum_global`.`mssr_forum_reply` ON
                        `mssr_forum_global`.`mssr_forum_reply_book_rev`.`reply_id`=`mssr_forum_global`.`mssr_forum_reply`.`reply_id`

                        INNER JOIN `mssr_forum_global`.`mssr_forum_reply_detail` ON
                        `mssr_forum_global`.`mssr_forum_reply`.`reply_id`=`mssr_forum_global`.`mssr_forum_reply_detail`.`reply_id`

                        INNER JOIN `user`.`member` ON
                        `mssr_forum_global`.`mssr_forum_reply`.`user_id`=`user`.`member`.`uid`

                        INNER JOIN `mssr_forum_global`.`mssr_forum_article` ON
                        `mssr_forum_global`.`mssr_forum_reply`.`article_id`=`mssr_forum_global`.`mssr_forum_article`.`article_id`

                        INNER JOIN `mssr_forum_global`.`mssr_forum_article_detail` ON
                        `mssr_forum_global`.`mssr_forum_article`.`article_id`=`mssr_forum_global`.`mssr_forum_article_detail`.`article_id`
                    WHERE 1=1
                        AND `mssr_forum_global`.`mssr_forum_article`.`article_from` IN (1,2) -- 文章來源
                        AND `mssr_forum_global`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                        AND `mssr_forum_global`.`mssr_forum_reply`.`reply_state`    =1 -- 回文狀態
                        AND `mssr_forum_global`.`mssr_forum_reply`.`user_id`        ={$user_id}
                        AND `mssr_forum_global`.`mssr_forum_reply`.`user_country_code`='{$conn_country_code}'

                    ORDER BY `keyin_cdate` DESC
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);
            $article_reply_results=array();
            if(!empty($db_results)){
                foreach($db_results as $db_result){

                    extract($db_result, EXTR_PREFIX_ALL, "rs");

                    $rs_user_id         =(int)$rs_user_id;
                    $rs_group_id        =(int)$rs_group_id;
                    $rs_article_id      =(int)$rs_article_id;
                    $rs_reply_id        =(int)$rs_reply_id;
                    $rs_like_cno        =(int)$rs_like_cno;

                    $rs_name            =trim($rs_name);
                    $rs_book_sid        =trim($rs_book_sid);
                    $rs_keyin_cdate     =trim($rs_keyin_cdate);
                    $rs_article_title   =trim($rs_article_title);
                    $rs_article_content =trim($rs_article_content);
                    $rs_reply_content   =trim($rs_reply_content);
                    $rs_type            =trim($rs_type);
                    $rs_keyin_time      =strtotime($rs_keyin_cdate);

                    if($rs_group_id!==0){
                        $sql="
                            SELECT `mssr_forum_global`.`mssr_forum_group`.`group_id`
                            FROM `mssr_forum_global`.`mssr_forum_group`
                            WHERE 1=1
                                AND `mssr_forum_global`.`mssr_forum_group`.`group_id`={$rs_group_id}
                                AND `mssr_forum_global`.`mssr_forum_group`.`group_state`=1
                        ";
                        $tmp_group_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(empty($tmp_group_results))continue;
                    }

                    $article_reply_results[$rs_keyin_time][trim('rs_name        ')]=$rs_name;
                    $article_reply_results[$rs_keyin_time][trim('book_sid       ')]=$rs_book_sid;
                    $article_reply_results[$rs_keyin_time][trim('user_id        ')]=$rs_user_id;
                    $article_reply_results[$rs_keyin_time][trim('group_id       ')]=$rs_group_id;
                    $article_reply_results[$rs_keyin_time][trim('article_id     ')]=$rs_article_id;
                    $article_reply_results[$rs_keyin_time][trim('reply_id       ')]=$rs_reply_id;
                    $article_reply_results[$rs_keyin_time][trim('like_cno       ')]=$rs_like_cno;
                    $article_reply_results[$rs_keyin_time][trim('keyin_mdate    ')]=$rs_keyin_cdate;
                    $article_reply_results[$rs_keyin_time][trim('article_title  ')]=$rs_article_title;
                    $article_reply_results[$rs_keyin_time][trim('article_content')]=$rs_article_content;
                    $article_reply_results[$rs_keyin_time][trim('reply_content  ')]=$rs_reply_content;
                    $article_reply_results[$rs_keyin_time][trim('type           ')]=$rs_type;
                }
                //時間排序
                krsort($article_reply_results);
            }

        //-----------------------------------------------
        //書櫃 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr`.`mssr_book_borrow_log`.`book_sid`
                FROM `mssr`.`mssr_book_borrow_log`
                WHERE 1=1
                    AND `mssr`.`mssr_book_borrow_log`.`user_id`={$user_id}
                GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`,
                         `mssr`.`mssr_book_borrow_log`.`book_sid`
                ORDER BY `mssr`.`mssr_book_borrow_log`.`borrow_sdate` DESC
            ";
            $book_results=db_result($conn_type='pdo',$conn_mssr_country,$sql,array(0,5),$arry_conn_mssr_country);

        //-----------------------------------------------
        //個人資訊 SQL
        //-----------------------------------------------

            //$user_content='無';
            //$sql="
            //    SELECT *
            //    FROM `mssr_forum_global`.`mssr_forum_user_info`
            //    WHERE 1=1
            //        AND `mssr_forum_global`.`mssr_forum_user_info`.`user_id`={$user_id}
            //";
            //$user_info_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            //if(!empty($user_info_results)){
            //    $user_content=trim($user_info_results[0]['user_content']);
            //}

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,明日聊書";

        //標籤
        $meta=meta($rd=1);

        //導覽列
        $navbar=navbar($rd=1);

        //廣告牆
        $carousel=carousel($rd=1);

        //側邊欄


        //註腳列
        $footbar=footbar($rd=1);

        $send_url=trim('http://').trim($_SERVER['HTTP_HOST']).trim($_SERVER['REQUEST_URI']);
//echo "<Pre>";
//print_r($arrys_sess_login_info);
//echo "</Pre>";
//echo "<Pre>";
//print_r($sess_country_code);
//echo "</Pre>";
//echo "<Pre>";
//print_r($arry_conn_mssr);
//echo "</Pre>";
////echo "<Pre>";
////print_r($conn_mssr);
////echo "</Pre>";
//echo "<Pre>";
//print_r($user_img);
//echo "</Pre>";
//echo "<Pre>";
//print_r($db_results);
//echo "</Pre>";
//die();
?>
<!DOCTYPE html>
<html lang='zh-TW' prefix='og: http://ogp.me/ns#'>
<head>
    <title><?php echo $title;?></title>

    <!-- 標籤,start -->
    <?php echo $meta;?>
    <!-- 標籤,end -->

    <!-- icon -->
    <link rel="shortcut icon" href="">

    <!-- 通用 -->
    <link href="../../../lib/jquery/ui/code.css" rel="stylesheet" type="text/css">
    <link href="../../../lib/framework/bootstrap/css/code.css" rel="stylesheet" type="text/css">

    <!-- 專屬 -->
    <link href="../css/site.css" rel="stylesheet" type="text/css">

    <!--[if (gte IE 6)&(lte IE 8)]>
        <script>self.location.href='../pages/browser_update/index.php'</script>
    <![endif]-->

    <!--[if lt IE 9]>
        <script src="../../../lib/js/html5/code.js"></script>
        <script src="../../../lib/js/css/code.js"></script>
    <![endif]-->
</head>
<body>

    <!-- 導覽列,容器,start -->
    <?php echo $navbar;?>
    <!-- 導覽列,容器,end -->

    <!-- 頁面,容器,start -->
    <div class="container">

        <!-- 內容,start -->
        <div class="row">

            <!-- jumbotron,start -->
            <div class="jumbotron hidden-xs" style="background-image:url('../img/default/front_cover_user.jpg');background-position:center top;background-size:100% auto;">

                <!-- 大頭貼,大解析度,start -->
                <img class="jumbotron_img hidden-xs"
                src="<?php echo $user_img;?>"
                width="160" height="160" border="0" alt="user_img"
                onclick=""
                onmouseover='this.style.cursor="pointer";'/>
                <!-- 大頭貼,大解析度,end -->

                <!-- jumbotron_name,start -->
                <span class="jumbotron_name">
                    <?php echo htmlspecialchars($user_name);?>
                </span>
                <!-- jumbotron_name,end -->

            </div>
            <!-- jumbotron,end -->

            <!-- jumbotron-xs,start -->
            <div class="jumbotron jumbotron-xs hidden-sm hidden-md hidden-lg" style="background-image:url('../img/default/front_cover_user.jpg');background-position:center top;background-size:100% auto;">

                <!-- 大頭貼,小解析度,start -->
                <img class="jumbotron-xs_img hidden-sm hidden-md hidden-lg"
                src="<?php echo $user_img;?>"
                width="100" height="100" border="0" alt="user_img"
                onclick=""
                onmouseover='this.style.cursor="pointer";'/>
                <!-- 大頭貼,小解析度,end -->

                <!-- jumbotron-xs_name,start -->
                <span class="jumbotron-xs_name"><?php echo htmlspecialchars($user_name);?></span>
                <!-- jumbotron-xs_name,end -->

            </div>
            <!-- jumbotron-xs,end -->

            <!-- page_info,大解析度,start -->
            <div class="page_info hidden-xs">
                <table class="table hidden-xs" border="1">
                    <tbody><tr>
                        <td width="215px">&nbsp;</td>
                        <td width="250px" align="center">

                        </td>
                        <td align="center"></td>
                        <td align="center"></td>
                        <td align="center"></td>
                    </tr></tbody>
                </table>
            </div>
            <!-- page_info,大解析度,end -->

            <!-- page_info,小解析度,start -->
            <?php if($sess_user_id!==$user_id):?>
            <div class="page_info hidden-sm hidden-md hidden-lg">
                <table class="table hidden-sm hidden-md hidden-lg" border="1">
                    <tbody><tr>
                        <td align="center">
                        </td>
                    </tr></tbody>
                </table>
            </div>
            <?php endif;?>
            <!-- page_info,小解析度,end -->

            <!-- user_lefe_side,start -->
            <div class="user_lefe_side col-xs-12 col-sm-10 col-md-10 col-lg-10">
                <ul id="myTab" class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="<?php if($tab===1)echo 'active';?>"><a href="#home" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">
                        首頁
                    </a></li>
                    <li role="presentation" class="<?php if($tab===2)echo 'active';?>"><a href="#book" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile"
                    onclick="user_blade(2);">
                        書櫃
                    </a></li>
                    <li role="presentation" class="<?php if($tab===3)echo 'active';?>"><a href="#article" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile"
                    onclick="user_blade(3);">
                        討論
                    </a></li>
                    <li role="presentation" class="<?php if($tab===4)echo 'active';?> hidden-xs"><a href="#group" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile"
                    onclick="user_blade(4);">
                        小組
                    </a></li>
                    <li role="presentation" class="dropdown hidden-sm hidden-md hidden-lg">
                        <a href="javascript:void(0);" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown" aria-controls="myTabDrop1-contents">更多&nbsp;<span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="myTabDrop1" id="myTabDrop1-contents">
                            <li role="presentation" class="<?php if($tab===4)echo 'active';?>"><a href="#group" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile"
                            onclick="user_blade(4);">
                                小組
                            </a></li>
                        </ul>
                    </li>
                </ul>
                <div id="myTabContent" class="tab-content">

                    <!-- 首頁 -->
                    <div role="tabpanel" class="tab-pane fade <?php if($tab===1)echo 'in active';?>" id="home" aria-labelledBy="home-tab">

                        <!-- 近期發文 -->
                        <div class="tab_title"><?php echo htmlspecialchars($user_name);?>&nbsp;最近討論發文、回文<!-- &nbsp;<?php for($i=0;$i<15;$i++):?>‧<?php endfor;?> --></div>
                        <table class="user_lefe_side_tab1 table table-striped">
                            <thead><tr class="second_tr" align="left">
                                <td width="225px" class="hidden-xs"><span>討論類型(書籍/小組)  </span></td>
                                <td><span>發表的內容(發文/回覆)</span></td>
                                <td width="100px"><span>發表時間             </span></td>
                                <td width="20px"class="hidden-xs"><span>讚 </span></td>
                            </tr></thead>
                            <tbody>
                                <?php
                                if(!empty($article_reply_results)){
                                    $cno=0;
                                    foreach($article_reply_results as $article_reply_result):
                                        extract($article_reply_result, EXTR_PREFIX_ALL, "rs");
                                    //筆數控制
                                    if($cno<5){
                                        $rs_user_id         =(int)$rs_user_id;
                                        $rs_group_id        =(int)$rs_group_id;
                                        $rs_article_id      =(int)$rs_article_id;
                                        $rs_reply_id        =(int)$rs_reply_id;
                                        $rs_like_cno        =(int)$rs_like_cno;
                                        $rs_book_sid        =trim($rs_book_sid);
                                        $rs_keyin_mdate     =trim($rs_keyin_mdate);
                                        $rs_article_title   =trim($rs_article_title);
                                        $rs_article_content =trim($rs_article_content);
                                        $rs_reply_content   =trim($rs_reply_content);
                                        $rs_type            =trim($rs_type);

                                        //if($rs_group_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
                                        //    $arry_blacklist_group_school=get_blacklist_group_school($sess_school_code,$rs_group_id,$arry_conn_mssr);
                                        //    if(!empty($arry_blacklist_group_school))continue;
                                        //}
                                        //
                                        //if($rs_article_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
                                        //    $arry_blacklist_article_school=get_blacklist_article_school($sess_school_code,$rs_article_id,$arry_conn_mssr);
                                        //    if(!empty($arry_blacklist_article_school))continue;
                                        //}
                                        //
                                        //if($rs_reply_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
                                        //    $arry_blacklist_reply_school=get_blacklist_reply_school($sess_school_code,$rs_reply_id,$arry_conn_mssr);
                                        //    if(!empty($arry_blacklist_reply_school))continue;
                                        //}

                                        if($rs_group_id===0)$get_from=1;
                                        if($rs_group_id!==0)$get_from=2;

                                        if(mb_strlen($rs_article_content)>100){
                                            $rs_article_content=mb_substr($rs_article_content,0,100)."..";
                                        }

                                        if(mb_strlen($rs_reply_content)>100){
                                            $rs_reply_content=mb_substr($rs_reply_content,0,100)."..";
                                        }

                                        //特殊處理
                                        if($get_from===1){
                                            $rs_book_name='';
                                            $arry_book_infos=get_book_info($conn_mssr_country,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr_country);
                                            if(!empty($arry_book_infos)){$rs_book_name=trim($arry_book_infos[0]['book_name']);}else{continue;}
                                        }else{
                                            $rs_group_name='';
                                            $sql="
                                                SELECT
                                                    `mssr_forum_global`.`mssr_forum_group`.`group_name`
                                                FROM `mssr_forum_global`.`mssr_forum_group`
                                                WHERE 1=1
                                                    AND `mssr_forum_global`.`mssr_forum_group`.`group_id`={$rs_group_id}
                                            ";
                                            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                            $rs_group_name=trim(($db_results[0]['group_name']));
                                        }
                                ?>
                                <tr align="left">
                                    <td class="hidden-xs" style="border:0px;word-break:break-all;overflow:hidden;">
                                        <?php if($rs_group_id===0):?>
                                            <a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_book_sid);?>">
                                                書籍：<?php echo htmlspecialchars($rs_book_name);?>
                                            </a>
                                        <?php else:?>
                                            小組：<?php echo htmlspecialchars($rs_group_name);?>
                                        <?php endif;?>
                                    </td>
                                    <td style="border:0px;word-break:break-all;overflow:hidden;">
                                        <a target="_blank" href="reply.php?get_from=<?php echo $get_from;?>&article_id=<?php echo $rs_article_id;?>">
                                            標題：<?php echo htmlspecialchars($rs_article_title);?><br>
                                            <?php if($rs_type==='article'):?>
                                                發文：<?php echo (htmlspecialchars($rs_article_content));?>
                                            <?php else:?>
                                                回覆：<?php echo (htmlspecialchars($rs_reply_content));?>
                                            <?php endif;?>......more
                                        </a>
                                    </td>
                                    <td style="border:0px;"><?php echo htmlspecialchars($rs_keyin_mdate);?></td>
                                    <td class="hidden-xs" style="border:0px;"><?php echo htmlspecialchars($rs_like_cno);?></td>
                                </tr>
                                <?php $cno++;}endforeach;}else{?>
                                    <tr align="center"><td colspan="4" style="border:0px;font-size:16px;">查無文章資訊。</td></tr>
                                <?php }?>
                            </tbody>
                        </table>

                        <!-- 近期書籍 -->
                        <div class="tab_title"><?php echo htmlspecialchars($user_name);?>&nbsp;最近閱讀書籍<!-- &nbsp;<?php for($i=0;$i<15;$i++):?>‧<?php endfor;?> --></div>
                        <?php if(!empty($book_results)){ ?>
                            <div class="user_lefe_side_tab2 row">
                                <?php
                                    $cno=0;
                                    $arry_conn_mssr_country=get_conn_country($user_id,$account);
                                    $conn_mssr_country=conn($db_type='mysql',$arry_conn_mssr_country);
                                    foreach($book_results as $inx=>$book_result):
                                    //本數控制
                                    if($cno<6){
                                        $rs_book_sid=trim($book_result['book_sid']);
                                        if($rs_book_sid!==''){
                                            $arry_book_infos=get_book_info($conn_mssr_country,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr_country);
                                            if(empty($arry_book_infos))continue;
                                            $rs_book_name=trim($arry_book_infos[0]['book_name']);
                                            if(mb_strlen($rs_book_name)>20){
                                                $rs_book_name=mb_substr($rs_book_name,0,20)."..";
                                            }
                                            if(trim($rs_book_name)==='')continue;
                                            $rs_book_img    ='../img/default/book.png';
                                            if(file_exists("../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg")){
                                                $rs_book_img="../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg";
                                            }
                                            if(preg_match("/^mbu/i",$rs_book_sid)){
                                                $get_book_info=get_book_info($conn_mssr_country,trim($rs_book_sid),$array_filter=array('book_verified'),$arry_conn_mssr_country);
                                                if(!empty($get_book_info)){
                                                    $rs_book_verified=(int)$get_book_info[0]['book_verified'];
                                                    if($rs_book_verified===2)continue;
                                                }else{continue;}
                                            }
                                        }
                                ?>
                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                                    <div class="thumbnail">
                                        <!-- <a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_book_sid);?>"> -->
                                            <img width="80" height="80" style="weight:80px;height:80px;" src="<?php echo $rs_book_img;?>" alt="Generic placeholder thumbnail">
                                            <div class="caption"><?php echo htmlspecialchars($rs_book_name);?></div>
                                        <!-- </a> -->
                                    </div>
                                </div>
                                <?php $cno++;}endforeach;?>
                            </div>
                        <?php }else{?>
                            <table class="group_lefe_side_tab1 table table-striped">
                                <thead><tr class="second_tr" align="left">
                                    <td style="height:30px;"><span></span></td>
                                </tr></thead>
                                <tbody>
                                    <tr align="center"><td style="border:0px;font-size:16px;">查無書單資訊。</td></tr>
                                </tbody>
                            </table>
                        <?php }?>

                    </div>

                    <!-- 書櫃 -->
                    <div role="tabpanel" class="tab-pane fade <?php if($tab===2)echo 'in active';?>" id="book" aria-labelledBy="profile-tab">

                    </div>

                    <!-- 討論 -->
                    <div role="tabpanel" class="tab-pane fade <?php if($tab===3)echo 'in active';?>" id="article" aria-labelledBy="profile-tab">

                    </div>

                    <!-- 小組 -->
                    <div role="tabpanel" class="tab-pane fade <?php if($tab===4)echo 'in active';?>" id="group" aria-labelledBy="profile-tab">

                    </div>

                </div>
            </div>
            <!-- user_lefe_side,end -->

            <!-- right_side,start -->
            <div class="right_side col-xs-12 col-sm-2 col-md-2 col-lg-2"></div>
            <!-- right_side,end -->

        </div>
        <!-- 內容,end -->

    </div>
    <!-- 頁面,容器,end -->

    <!-- 註腳列,容器,start -->
    <?php echo $footbar;?>
    <!-- 註腳列,容器,end -->

    <!-- 頁面至頂,start -->
    <div class="scroll_to_top hidden-xs"></div>
    <!-- 頁面至頂,end -->

</body>

<!-- 通用 -->
<script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/func/block_ui/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/ui/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/func/flot/excanvas.min.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/func/flot/jquery.flot.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/func/flot/jquery.flot.pie.js"></script>
<script type="text/javascript" src="../../../lib/js/string/code.js"></script>
<script type="text/javascript" src="../../../lib/js/array/code.js"></script>
<script type="text/javascript" src="../../../lib/js/fso/code.js"></script>
<script type="text/javascript" src="../../../lib/js/form/code.js"></script>
<script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/3.51/jquery.form.min.js"></script>

<!-- 專屬 -->
<script type="text/javascript" src="../inc/code.js"></script>

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var send_url=document.URL;
    var tab=<?php echo $tab;?>;
    var sess_user_id=parseInt(<?php echo $sess_user_id;?>);
    var user_id=parseInt(<?php echo $user_id;?>);
    var account=$.trim(('<?php echo $account;?>'));


    //OBJ
    var notification=new notification();


    //FUNCTION
    function load_right_side(fun){
    //讀取側邊欄

        var fun=trim(fun);

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/load.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                sess_user_id:encodeURI(trim(sess_user_id        )),
                user_id     :encodeURI(trim(user_id             )),
                fun         :encodeURI(trim(fun                 )),
                method      :encodeURI(trim('load_right_side'   ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
                $.blockUI({
                    message:'<h3>網頁讀取中...</h3>',
                    css:{
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .6,
                        color: '#fff'
                    }
                });
            },
            success     :function(respones){
            //成功處理
                var respones=jQuery.parseJSON(respones);
                if($.trim(respones)!==''){
                    $('.right_side').append(respones);
                }
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                $.unblockUI();
                return false;
            },
            complete    :function(){
            //傳送後處理
                $.unblockUI();
            }
        });
    }

    function user_blade(tab){
        switch(parseInt(tab)){
            case 2:
                var user_blade_path='blade/user.book.php';
                $.ajax({
                //參數設置
                    async      :true,
                    cache      :false,
                    global     :true,
                    timeout    :50000,
                    contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                    url        :user_blade_path,
                    type       :"GET",
                    data       :{
                        user_id:user_id,
                        account:account
                    },

                //事件
                    beforeSend  :function(){
                    //傳送前處理
                        block_ui('網頁讀取中...');
                    },
                    success     :function(respones){
                    //成功處理
                        $('#book').empty();
                        $('#book').append(respones);
                        $.unblockUI();
                    }
                });
            break;

            case 3:
                var user_blade_path='blade/user.article.php';
                $.ajax({
                //參數設置
                    async      :true,
                    cache      :false,
                    global     :true,
                    timeout    :50000,
                    contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                    url        :user_blade_path,
                    type       :"GET",
                    data       :{
                        user_id:user_id,
                        account:account
                    },

                //事件
                    beforeSend  :function(){
                    //傳送前處理
                        block_ui('網頁讀取中...');
                    },
                    success     :function(respones){
                    //成功處理
                        $('#article').empty();
                        $('#article').append(respones);
                        $.unblockUI();
                    }
                });
            break;

            case 4:
                var user_blade_path='blade/user.group.php';
                $.ajax({
                //參數設置
                    async      :true,
                    cache      :false,
                    global     :true,
                    timeout    :50000,
                    contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                    url        :user_blade_path,
                    type       :"GET",
                    data       :{
                        user_id:user_id,
                        account:account
                    },

                //事件
                    beforeSend  :function(){
                    //傳送前處理
                        block_ui('網頁讀取中...');
                    },
                    success     :function(respones){
                    //成功處理
                        $('#group').empty();
                        $('#group').append(respones);
                        $.unblockUI();
                    }
                });
            break;

            case 5:
                var user_blade_path='blade/user.friend.php';
                $.ajax({
                //參數設置
                    async      :true,
                    cache      :false,
                    global     :true,
                    timeout    :50000,
                    contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                    url        :user_blade_path,
                    type       :"GET",
                    data       :{
                        user_id:user_id,
                        account:account
                    },

                //事件
                    beforeSend  :function(){
                    //傳送前處理
                        block_ui('網頁讀取中...');
                    },
                    success     :function(respones){
                    //成功處理
                        $('#friend').empty();
                        $('#friend').append(respones);
                        $.unblockUI();
                    }
                });
            break;

            case 7:
                var user_blade_path='blade/user.track.php';
                $.ajax({
                //參數設置
                    async      :true,
                    cache      :false,
                    global     :true,
                    timeout    :50000,
                    contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                    url        :user_blade_path,
                    type       :"GET",
                    data       :{
                        user_id:user_id,
                        account:account
                    },

                //事件
                    beforeSend  :function(){
                    //傳送前處理
                        block_ui('網頁讀取中...');
                    },
                    success     :function(respones){
                    //成功處理
                        $('#track').empty();
                        $('#track').append(respones);
                        $.unblockUI();
                    }
                });
            break;
        }
    }


    //ONLOAD
    $(function(){
        ////讀取側邊欄
        //if(sess_user_id===user_id){
        //    load_right_side(trim('member_self'));
        //}else{
        //    load_right_side(trim('member_other'));
        //}

        //滾動監聽
        $(window).scroll(function(){
            //if(user_article_cno>0){
            //    //偵測行動裝置
            //    if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
            //        if($(window).scrollTop()>=($(document).height()-$(window).height())%2){
            //            //讀取使用者文章
            //            load_user_article();
            //        }
            //    }else{
            //        if($(window).scrollTop()==$(document).height()-$(window).height()){
            //            //讀取使用者文章
            //            load_user_article();
            //        }
            //    }
            //}
            //偵測行動裝置
            if(/Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){

            }else{
                if(parseInt($(window).scrollTop())>0){
                    $('.scroll_to_top').show();
                }else{
                    $('.scroll_to_top').hide();
                }
            }
        });

        user_blade(tab);
    })

</script>
<script type="text/javascript" src="../../../inc/external/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//呼叫使用者頁面紀錄
//-------------------------------------------------------
    //user_page_log(rd=3);
</script>
</html>
<?php
    $conn_user=NULL;
    $conn_mssr=NULL;
    //ftp_close($ftp_conn);
?>