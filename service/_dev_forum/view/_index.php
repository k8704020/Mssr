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

        //外掛頁面檔
        require_once(str_repeat("../",1).'pages/code.php');

        //外掛函式檔
        $funcs=array(
            APP_ROOT.'inc/code',
            APP_ROOT.'service/dev_forum/inc/code'
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
    //$_SESSION['uid']=5029;
    //$_SESSION['uid']=5030;
    //$_SESSION['uid']=5031;
    //$_SESSION['uid']=5032;
    //$_SESSION['uid']=5033;
    //$_SESSION['uid']=5034;
    //$_SESSION['uid']=5035;
    //$_SESSION['uid']=35526;

        $arrys_sess_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);

        if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
        if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
        if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
        if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
        if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
        if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
        if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
        if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
        if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
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

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //連線物件
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //動態牆 SQL
        //-----------------------------------------------
        //好友動態      SQL
        //追蹤書籍動態  SQL
        //加入小組動態  SQL

            //取得好友名單
            $arry_forum_friend=array();
            $forum_friend_list='';
            $friend_results   =get_forum_friend($sess_user_id,$friend_id=0,$arry_conn_mssr);
            if(!empty($friend_results)){
                foreach($friend_results as $friend_result){
                    if((int)$friend_result['friend_state']===1){
                        if((int)$friend_result['user_id']!==$sess_user_id)$arry_forum_friend[]=$friend_result['user_id'];
                        if((int)$friend_result['friend_id']!==$sess_user_id)$arry_forum_friend[]=$friend_result['friend_id'];
                    }
                }
                $forum_friend_list=implode(',',$arry_forum_friend);
            }

            //取得追蹤書籍名單
            $arry_forum_track_book=array();
            $forum_track_book_list='';
            $sql="
                SELECT `mssr_forum`.`mssr_forum_track_book`.`book_sid`
                FROM `mssr_forum`.`mssr_forum_track_book`
                WHERE `mssr_forum`.`mssr_forum_track_book`.`user_id`={$sess_user_id}
                GROUP BY `mssr_forum`.`mssr_forum_track_book`.`user_id`, `mssr_forum`.`mssr_forum_track_book`.`book_sid`
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $db_result){
                    $rs_book_sid=trim($db_result['book_sid']);
                    $arry_forum_track_book[]=$rs_book_sid;
                }
                $forum_track_book_list="'".implode("','",$arry_forum_track_book)."'";
            }

            //取得加入小組名單
            $arry_forum_group_user=array();
            $forum_group_user_list='';
            $sql="
                SELECT `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                FROM `mssr_forum`.`mssr_forum_group_user_rev`
                WHERE `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`={$sess_user_id}
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`=1
                GROUP BY `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`, `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $db_result){
                    $rs_group_id=trim($db_result['group_id']);
                    $arry_forum_group_user[]=$rs_group_id;
                }
                $forum_group_user_list=implode(',',$arry_forum_group_user);
            }

            //動態牆SQL
            $wall_results=array();
            $wall_sql="";

            if(!empty($arry_forum_friend)){
                $wall_sql.="
                    SELECT
                        `mssr_forum`.`mssr_forum_group`.`group_name`,
                        `mssr_forum`.`mssr_forum_group`.`group_content`,
                        `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,
                        `user`.`member`.`name`,
                        `user`.`member`.`sex`,
                        `mssr_forum`.`mssr_forum_article`.`user_id`,
                        `mssr_forum`.`mssr_forum_article`.`group_id`,
                        `mssr_forum`.`mssr_forum_article`.`article_id`,
                        `mssr_forum`.`mssr_forum_article`.`keyin_cdate`,
                        `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                        `mssr_forum`.`mssr_forum_article_detail`.`article_content`
                    FROM `mssr_forum`.`mssr_forum_article`
                        INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                        `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                        INNER JOIN `user`.`member` ON
                        `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

                        LEFT JOIN `mssr_forum`.`mssr_forum_article_book_rev`  ON
                        `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_book_rev`.`article_id`

                        LEFT JOIN `mssr_forum`.`mssr_forum_group`  ON
                        `mssr_forum`.`mssr_forum_article`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_article`.`user_id` IN ($forum_friend_list)
                        AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                ";
                if(!empty($arry_forum_track_book)||!empty($arry_forum_group_user)){
                    $wall_sql.="UNION";
                }
            }
            if(!empty($arry_forum_track_book)){
                $wall_sql.="
                    SELECT
                        `mssr_forum`.`mssr_forum_group`.`group_name`,
                        `mssr_forum`.`mssr_forum_group`.`group_content`,
                        `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,
                        `user`.`member`.`name`,
                        `user`.`member`.`sex`,
                        `mssr_forum`.`mssr_forum_article`.`user_id`,
                        `mssr_forum`.`mssr_forum_article`.`group_id`,
                        `mssr_forum`.`mssr_forum_article`.`article_id`,
                        `mssr_forum`.`mssr_forum_article`.`keyin_cdate`,
                        `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                        `mssr_forum`.`mssr_forum_article_detail`.`article_content`
                    FROM `mssr_forum`.`mssr_forum_article`
                        INNER JOIN `mssr_forum`.`mssr_forum_article_book_rev` ON
                        `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_book_rev`.`article_id`

                        INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                        `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                        INNER JOIN `user`.`member` ON
                        `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

                        LEFT JOIN `mssr_forum`.`mssr_forum_group`  ON
                        `mssr_forum`.`mssr_forum_article`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid` IN ($forum_track_book_list)
                        AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                ";
                if(!empty($arry_forum_group_user)){
                    $wall_sql.="UNION";
                }
            }
            if(!empty($arry_forum_group_user)){
                $wall_sql.="
                    SELECT
                        `mssr_forum`.`mssr_forum_group`.`group_name`,
                        `mssr_forum`.`mssr_forum_group`.`group_content`,
                        `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,
                        `user`.`member`.`name`,
                        `user`.`member`.`sex`,
                        `mssr_forum`.`mssr_forum_article`.`user_id`,
                        `mssr_forum`.`mssr_forum_article`.`group_id`,
                        `mssr_forum`.`mssr_forum_article`.`article_id`,
                        `mssr_forum`.`mssr_forum_article`.`keyin_cdate`,
                        `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                        `mssr_forum`.`mssr_forum_article_detail`.`article_content`
                    FROM `mssr_forum`.`mssr_forum_article`
                        INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                        `mssr_forum`.`mssr_forum_article`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`

                        INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                        `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                        INNER JOIN `user`.`member` ON
                        `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

                        LEFT JOIN `mssr_forum`.`mssr_forum_article_book_rev`  ON
                        `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_book_rev`.`article_id`

                        LEFT JOIN `mssr_forum`.`mssr_forum_group`  ON
                        `mssr_forum`.`mssr_forum_article`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_article`.`group_id` IN ($forum_group_user_list)
                        AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                ";
            }

            //彙整
            if($wall_sql!==''){
                $db_results=db_result($conn_type='pdo',$conn_mssr,$wall_sql,array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $db_result){
                        extract($db_result, EXTR_PREFIX_ALL, "rs");
                        $rs_group_name     =trim($rs_group_name);
                        $rs_group_content  =trim($rs_group_content);
                        $rs_book_sid       =trim($rs_book_sid);
                        $rs_user_sex       =(int)$rs_sex;
                        $rs_user_name      =trim($rs_name);
                        $rs_user_id        =(int)$rs_user_id;
                        $rs_group_id       =(int)$rs_group_id;
                        $rs_article_id     =(int)$rs_article_id;
                        $rs_keyin_cdate    =trim($rs_keyin_cdate);
                        $rs_article_title  =trim($rs_article_title);
                        $rs_article_content=trim($rs_article_content);
                        $rs_time           =trim(strtotime($rs_keyin_cdate));

                        $wall_results[$rs_time][trim('group_name     ')]=$rs_group_name;
                        $wall_results[$rs_time][trim('group_content  ')]=$rs_group_content;
                        $wall_results[$rs_time][trim('book_sid       ')]=$rs_book_sid;
                        $wall_results[$rs_time][trim('user_sex       ')]=$rs_user_sex;
                        $wall_results[$rs_time][trim('user_name      ')]=$rs_user_name;
                        $wall_results[$rs_time][trim('user_id        ')]=$rs_user_id;
                        $wall_results[$rs_time][trim('group_id       ')]=$rs_group_id;
                        $wall_results[$rs_time][trim('article_id     ')]=$rs_article_id;
                        $wall_results[$rs_time][trim('keyin_cdate    ')]=$rs_keyin_cdate;
                        $wall_results[$rs_time][trim('article_title  ')]=$rs_article_title;
                        $wall_results[$rs_time][trim('article_content')]=$rs_article_content;
                        $wall_results[$rs_time][trim('time           ')]=$rs_time;
                    }
                    krsort($wall_results);
                }
            }

        //-----------------------------------------------
        //熱門書籍 SQL
        //-----------------------------------------------

            $hot_book_results=array();
            $sql="
                    SELECT
                        `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,
                        `mssr_forum`.`mssr_forum_article`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_article_book_rev`
                        INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                        `mssr_forum`.`mssr_forum_article_book_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_article`.`group_id`=0

                UNION ALL

                    SELECT
                        `mssr_forum`.`mssr_forum_reply_book_rev`.`book_sid`,
                        `mssr_forum`.`mssr_forum_reply`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_reply_book_rev`
                        INNER JOIN `mssr_forum`.`mssr_forum_reply` ON
                        `mssr_forum`.`mssr_forum_reply`.`article_id`=`mssr_forum`.`mssr_forum_reply`.`article_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_reply`.`reply_id`=`mssr_forum`.`mssr_forum_reply`.`reply_id`
                        AND `mssr_forum`.`mssr_forum_reply`.`group_id`=0

                ORDER BY `keyin_cdate` DESC
                LIMIT 100;
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $db_result){
                    $rs_book_sid=trim($db_result['book_sid']);
                    $hot_book_results[]=$rs_book_sid;
                }
                $hot_book_results=array_count_values($hot_book_results);
                arsort($hot_book_results);
            }

        //-----------------------------------------------
        //熱門小組 SQL
        //-----------------------------------------------

            $hot_group_results=array();
            $sql="
                    SELECT
                        `mssr_forum`.`mssr_forum_group`.`group_id`,
                        `mssr_forum`.`mssr_forum_article`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_article`
                        INNER JOIN `mssr_forum`.`mssr_forum_group` ON
                        `mssr_forum`.`mssr_forum_article`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_article`.`group_id`<>0

                UNION ALL

                    SELECT
                        `mssr_forum`.`mssr_forum_group`.`group_id`,
                        `mssr_forum`.`mssr_forum_reply`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_reply`
                        INNER JOIN `mssr_forum`.`mssr_forum_group` ON
                        `mssr_forum`.`mssr_forum_reply`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_reply`.`group_id`<>0

                ORDER BY `keyin_cdate` DESC
                LIMIT 100;
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $db_result){
                    $rs_group_id=(int)$db_result['group_id'];
                    $hot_group_results[]=$rs_group_id;
                }
                $hot_group_results=array_count_values($hot_group_results);
                arsort($hot_group_results);
            }

        //-----------------------------------------------
        //名人區 SQL
        //-----------------------------------------------

            $hot_member_results=array();
            $sql="
                    SELECT
                        `mssr_forum`.`mssr_forum_article`.`user_id`,
                        `mssr_forum`.`mssr_forum_article`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_article`
                    WHERE 1=1

                UNION ALL

                    SELECT
                        `mssr_forum`.`mssr_forum_reply`.`user_id`,
                        `mssr_forum`.`mssr_forum_reply`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_reply`
                    WHERE 1=1

                ORDER BY `keyin_cdate` DESC
                LIMIT 100;
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $db_result){
                    $rs_user_id=(int)$db_result['user_id'];
                    $hot_member_results[]=$rs_user_id;
                }
                $hot_member_results=array_count_values($hot_member_results);
                arsort($hot_member_results);
            }

        //-----------------------------------------------
        //推薦好友 SQL
        //-----------------------------------------------

            $rec_friend_results=array();
            if(isset($sess_arry_class_info)&&!empty($sess_arry_class_info)&&count($arry_forum_friend)<10){
                $sess_class_code=addslashes(trim($sess_arry_class_info['class_code']));
                $sql="
                    SELECT
                        `user`.`member`.`uid`,
                        `user`.`member`.`name`,
                        `user`.`member`.`sex`
                    FROM `user`.`member`
                        INNER JOIN `user`.`student` ON
                        `user`.`member`.`uid`=`user`.`student`.`uid`
                    WHERE 1=1
                        AND `user`.`student`.`class_code` = '{$sess_class_code}'
                        AND CURDATE() BETWEEN `user`.`student`.`start` AND `user`.`student`.`end`
                ";
                $rec_friend_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($rec_friend_results)){
                    foreach($rec_friend_results as $inx=>$rec_friend_result){
                        if(in_array((int)$rec_friend_result['uid'],$arry_forum_friend))unset($rec_friend_results[$inx]);
                        if((int)$rec_friend_result['uid']===$sess_user_id)unset($rec_friend_results[$inx]);
                    }
                    Shuffle($rec_friend_results);
                }
            }

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

        //註腳列
        $footbar=footbar($rd=1);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title;?></title>

    <!-- 標籤,start -->
    <?php echo $meta;?>
    <!-- 標籤,end -->

    <!-- icon -->
    <link rel="shortcut icon" href="">

    <!-- 通用 -->
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

        <!-- carousel,容器,start -->
        <?php echo $carousel;?>
        <!-- carousel,容器,end -->

        <!-- 推薦好友,start -->
        <?php if(!empty($rec_friend_results)):?>
        <div class="row" style="position:relative;margin-top:-20px;">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><h3 style='color:#ff0000;'>你可能認識的人......</h3></div>
            <?php
                $cno=0;
                foreach($rec_friend_results as $rec_friend_result):
                    if($cno<4){
                        $rs_user_id  =(int)$rec_friend_result['uid'];
                        $rs_user_sex =(int)$rec_friend_result['sex'];
                        $rs_user_name=trim($rec_friend_result['name']);
                        if($rs_user_sex===1)$rs_user_img='../img/default/user_boy.png';
                        if($rs_user_sex===2)$rs_user_img='../img/default/user_girl.png';
            ?>
                <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
                    <div class="thumbnail">
                        <img class='img-responsive' src="<?php echo $rs_user_img;?>" alt="thumbnail" style="height:120px;">
                        <div class="caption" style="text-align:center;">
                            <h3><?php echo htmlspecialchars($rs_user_name);?></h3>
                            <p><a href="javascript:void(0);" class="btn_add_friend btn btn-primary" role="button"
                            user_id=<?php echo $sess_user_id;?>
                            friend_id=<?php echo $rs_user_id;?>
                            >加為好友</a></p>
                        </div>
                    </div>
                </div>
            <?php $cno++;}endforeach;?>
        </div>
        <?php endif;?>
        <!-- 推薦好友,容器,end -->

        <!-- 內容,start -->
        <div class="row">

            <!-- index_left_side,start -->
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 hidden-xs">
                <div class="row">
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12" style="color:#9197a3;">
                        <b>常用</b>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <a href="user.php?user_id=<?php echo $sess_user_id;?>&tab=6">
                            <em class="glyphicon glyphicon-comment"></em> 我的訊息 <span>Messages</span>
                        </a>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <a href="user.php?user_id=<?php echo $sess_user_id;?>&tab=2">
                            <em class="glyphicon glyphicon-book"></em> 我的書櫃 <span>Bookcases</span>
                        </a>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <a href="user.php?user_id=<?php echo $sess_user_id;?>&tab=3">
                            <em class="glyphicon glyphicon-pencil"></em> 我的討論 <span>Articles</span>
                        </a>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <a href="user.php?user_id=<?php echo $sess_user_id;?>&tab=5">
                            <em class="glyphicon glyphicon-user"></em> 我的好友 <span>Friends</span>
                        </a>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <a href="user.php?user_id=<?php echo $sess_user_id;?>&tab=4">
                            <em class="glyphicon glyphicon-star"></em> 我的小組 <span>Groups</span>
                        </a>
                    </div>

                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12" style="color:#9197a3;">
                        <b>本週熱門</b>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <a href="forum.php?method=view_hot_booklist">
                            <em class="glyphicon glyphicon glyphicon-list-alt"></em> 熱門書單 <span>Hot Booklist</span>
                        </a>
                    </div>
                </div>
            </div>
            <!-- index_left_side,end -->

            <!-- 動態牆,start -->
            <div class="wall col-xs-12 col-sm-8 col-md-8 col-lg-8">
                <?php if(!empty($wall_results)){
                    foreach($wall_results as $rs_time=>$wall_result){
                        $rs_group_name      =trim($wall_result[trim('group_name     ')]);
                        $rs_group_content   =trim($wall_result[trim('group_content  ')]);
                        $rs_book_sid        =trim($wall_result[trim('book_sid       ')]);
                        $rs_user_sex        =(int)$wall_result[trim('user_sex       ')];
                        $rs_user_name       =trim($wall_result[trim('user_name      ')]);
                        $rs_user_id         =(int)$wall_result[trim('user_id        ')];
                        $rs_group_id        =(int)$wall_result[trim('group_id       ')];
                        $rs_article_id      =(int)$wall_result[trim('article_id     ')];
                        $rs_keyin_cdate     =trim($wall_result[trim('keyin_cdate    ')]);
                        $rs_article_title   =trim($wall_result[trim('article_title  ')]);
                        $rs_article_content =trim($wall_result[trim('article_content')]);
                        $rs_user_img        ='../img/default/user_boy.png';

                        if($rs_user_sex===2)$rs_user_img ='../img/default/user_girl.png';

                        if(mb_strlen($rs_article_content)>100){
                            $rs_article_content=mb_substr($rs_article_content,0,100)."..";
                        }

                        if(trim($rs_book_sid)!==''){
                            $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name','book_author','book_publisher'),$arry_conn_mssr);
                            if(empty($arry_book_infos))continue;
                            $rs_book_name     =trim($arry_book_infos[0]['book_name']);
                            $rs_book_author   =trim($arry_book_infos[0]['book_author']);
                            $rs_book_publisher=trim($arry_book_infos[0]['book_publisher']);
                        }

                        if($rs_group_id===0){
                            $get_from    =1;
                            $rs_img_1    =trim('../img/default/book.png');
                            $rs_href_1   =trim("article.php?get_from=1&book_sid={$rs_book_sid}");
                            $rs_href_2   =trim("article.php?get_from=1&book_sid={$rs_book_sid}");
                            $rs_content_1=trim("【<a href='article.php?get_from=1&book_sid={$rs_book_sid}'>{$rs_book_name}</a>】 說");
                            $rs_content_2=trim("{$rs_book_name}");
                            $rs_content_3=trim("作者：{$rs_book_author}</br/>出版社：{$rs_book_publisher}");
                        }else{
                            $get_from    =2;
                            $rs_img_1    =trim('../img/default/group.png');
                            $rs_href_1   =trim("article.php?get_from=2&group_id={$rs_group_id}");
                            $rs_href_2   =trim("article.php?get_from=2&group_id={$rs_group_id}");
                            $rs_content_1=trim("【<a href='article.php?get_from=2&group_id={$rs_group_id}'>{$rs_group_name}</a>】 說");
                            $rs_content_2=trim("{$rs_group_name}");
                            $rs_content_3=trim("小組簡介：{$rs_group_content}");
                        }

                        //動態消息是否為好友
                        $is_forum_friend    =false;
                        if($rs_user_id!==$sess_user_id){
                            $sql="
                                SELECT
                                    `mssr_forum`.`mssr_forum_friend`.`user_id`,
                                    `mssr_forum`.`mssr_forum_friend`.`friend_id`
                                FROM `mssr_forum`.`mssr_forum_friend`
                                WHERE 1=1
                                    AND (
                                        `mssr_forum`.`mssr_forum_friend`.`user_id`  ={$sess_user_id}
                                            OR
                                        `mssr_forum`.`mssr_forum_friend`.`friend_id`={$sess_user_id}
                                    )
                                    AND (
                                        `mssr_forum`.`mssr_forum_friend`.`user_id`  ={$rs_user_id}
                                            OR
                                        `mssr_forum`.`mssr_forum_friend`.`friend_id`={$rs_user_id}
                                    )
                                    AND `mssr_forum`.`mssr_forum_friend`.`friend_state`=1
                            ";
                            $friend_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                            if(!empty($friend_results))$is_forum_friend=true;
                        }
                ?>
                    <?php if(!$is_forum_friend):?>
                        <div class="media">
                            <a class="pull-left" href="<?php echo $rs_href_1;?>">
                                <img class="media-object" src="<?php echo htmlspecialchars($rs_img_1);?>" width="64" height="64" alt="Media">
                            </a>
                            <div class="media-body">
                                <h5 class="media-heading" style="position:relative;left:0px">
                                    【<a href="user.php?user_id=<?php echo $rs_user_id;?>&tab=1"><?php echo htmlspecialchars($rs_user_name);?></a>】
                                    在 <?php echo ($rs_content_1);?>
                                </h5>
                                <p style="position:relative;top:5px;">
                                    <a target="_blank" href="reply.php?get_from=<?php echo (int)$get_from;?>&article_id=<?php echo (int)$rs_article_id;?>">
                                        <?php echo htmlspecialchars($rs_article_content);?>
                                    </a>
                                </p>
                            </div>
                        </div>
                    <?php else:?>
                        <div class="media">
                            <a class="pull-left" href="user.php?user_id=<?php echo $rs_user_id;?>&tab=1">
                                <img class="media-object" src="<?php echo $rs_user_img;?>" width="64" height="64" alt="Media">
                            </a>
                            <div class="media-body">
                                <h5 class="media-heading" style="position:relative;left:0px">
                                    你的朋友【<a href="user.php?user_id=<?php echo $rs_user_id;?>&tab=1"><?php echo htmlspecialchars($rs_user_name);?></a>】
                                    在 <?php echo ($rs_content_1);?>
                                </h5>
                                <p style="position:relative;top:5px;">
                                    <a target="_blank" href="reply.php?get_from=<?php echo (int)$get_from;?>&article_id=<?php echo (int)$rs_article_id;?>">
                                        <?php echo htmlspecialchars($rs_article_content);?>
                                    </a>
                                </p>
                                    <div class="submedia">
                                        <a class="pull-left" href="<?php echo $rs_href_2;?>">
                                            <img class="media-object" src="<?php echo htmlspecialchars($rs_img_1);?>" width="64" height="64" alt="Media">
                                        </a>
                                        <div class="media-body">
                                            <h5 class="media-heading submedia-heading"><?php echo $rs_content_2;?></h5>
                                            <p class="submedia-heading"><?php echo $rs_content_3;?></p>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    <?php endif;?>
                <?php }}else{?>
                    <div class="media">
                        <a class="pull-left" href="javascript:void(0);"></a>
                        <div class="media-body">
                            <h4 class="media-heading">歡迎蒞臨明日聊書！ 您目前無任何動態消息...</h4>
                        </div>
                    </div>
                <?php }?>
            </div>
            <!-- 動態牆,end -->

            <!-- right_side,start -->
            <div class="right_side col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <table class="table">
                    <thead>
                        <tr><td>熱門書籍</td></tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($hot_book_results)){
                            $cno=0;
                            foreach($hot_book_results as $rs_book_sid=>$hot_book_cno){
                                $rs_book_sid=trim($rs_book_sid);
                                if($cno<5){
                                    $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                    if(empty($arry_book_infos))continue;
                                    $rs_book_name=trim($arry_book_infos[0]['book_name']);
                        ?>
                        <tr><td><a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_book_sid);?>">
                            <?php echo htmlspecialchars($rs_book_name);?>
                        </a></td></tr>
                        <?php $cno++;}}}else{?>
                        <tr><td>
                            目前無任何書籍資訊...
                        </td></tr>
                        <?php }?>
                    </tbody>
                </table>

                <table class="table">
                    <thead>
                        <tr><td>熱門小組</td></tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($hot_group_results)){
                            $cno=0;
                            foreach($hot_group_results as $rs_group_id=>$hot_group_cno){
                                $rs_group_id=(int)$rs_group_id;
                                if($cno<5){
                                    $sql="
                                        SELECT
                                            `mssr_forum`.`mssr_forum_group`.`group_name`
                                        FROM `mssr_forum`.`mssr_forum_group`
                                        WHERE 1=1
                                            AND `mssr_forum`.`mssr_forum_group`.`group_id`={$rs_group_id}
                                    ";
                                    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                    if(empty($db_results))continue;
                                    $rs_group_name=trim($db_results[0]['group_name']);
                        ?>
                        <tr><td><a href="article.php?get_from=2&group_id=<?php echo addslashes($rs_group_id);?>">
                            <?php echo htmlspecialchars($rs_group_name);?>
                        </a></td></tr>
                        <?php $cno++;}}}else{?>
                        <tr><td>
                            目前無任何小組資訊...
                        </td></tr>
                        <?php }?>
                    </tbody>
                </table>

                <table class="table">
                    <thead>
                        <tr><td>名人區</td></tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($hot_member_results)){
                            $cno=0;
                            foreach($hot_member_results as $rs_user_id=>$hot_member_cno){
                                $rs_user_id=(int)$rs_user_id;
                                if($cno<5){
                                    $sql="
                                        SELECT
                                            `user`.`member`.`name`
                                        FROM `user`.`member`
                                        WHERE 1=1
                                            AND `user`.`member`.`uid`={$rs_user_id}
                                    ";
                                    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                    if(empty($db_results))continue;
                                    $rs_user_name=trim($db_results[0]['name']);
                        ?>
                        <tr><td><a href="user.php?user_id=<?php echo $rs_user_id;?>&tab=1">
                            <?php echo htmlspecialchars($rs_user_name);?>
                        </a></td></tr>
                        <?php $cno++;}}}else{?>
                        <tr><td>
                            目前無任何小組資訊...
                        </td></tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
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
<script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>
<script type="text/javascript" src="../../../lib/js/string/code.js"></script>

<!-- 專屬 -->
<script type="text/javascript" src="../inc/code.js"></script>

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var send_url=document.URL;


    //OBJ


    //FUNCTION
    $('.btn_add_friend').click(function(){
    //加為好友

        var user_id  =parseInt($(this).attr('user_id'));
        var friend_id=parseInt($(this).attr('friend_id'));

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/add.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                user_id     :encodeURI(trim(user_id         )),
                friend_id   :encodeURI(trim(friend_id       )),
                method      :encodeURI(trim('add_friend'    )),
                send_url    :encodeURI(trim(send_url        ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                location.reload();
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    });


    //ONLOAD
    $(function(){
        //滾動監聽
        $(window).scroll(function(){
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
    })


</script>
</html>
<?php
    $conn_user=NULL;
    $conn_mssr=NULL;
?>