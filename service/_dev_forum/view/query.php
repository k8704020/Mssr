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

        //特殊處理
        if(!isset($sess_country_code)){
            $sess_country_code='tw';
        }
        if($sess_country_code!=='tw'){
            die();
        }

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
    //search_value
    //search_type

        $search_value=(isset($_GET['search_value']))?trim($_GET['search_value']):'';
        $search_type =(isset($_GET['search_type']))?(int)$_GET['search_type']:0;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

        $arry_err=array();

        if($search_value===''){
            $arry_err[]='查詢條件未輸入';
        }
        if($search_type===''){
            $arry_err[]='查詢類型錯誤';
        }else{
            $search_type=(int)$search_type;
            if(!in_array($search_type,array(1,2,3,4))){
                $arry_err[]='查詢類型錯誤';
            }
        }
        if(count($arry_err)!==0){
            $msg=implode(', ',$arry_err);
            $jscript_back="
                <script>
                    alert('{$msg}');
                    history.back(-1);
                </script>
            ";
            die($jscript_back);
        }

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //連線物件
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //查詢牆 SQL
        //-----------------------------------------------

            $search_value=addslashes(trim($search_value));

            ////取得好友名單
            //$arry_forum_friend=array();
            //$forum_friend_list='';
            //$friend_results   =get_forum_friend($sess_user_id,$friend_id=0,$arry_conn_mssr);
            //if(!empty($friend_results)){
            //    foreach($friend_results as $friend_result){
            //        if((int)$friend_result['friend_state']===1){
            //            if((int)$friend_result['user_id']!==$sess_user_id)$arry_forum_friend[]=$friend_result['user_id'];
            //            if((int)$friend_result['friend_id']!==$sess_user_id)$arry_forum_friend[]=$friend_result['friend_id'];
            //        }
            //    }
            //    $forum_friend_list=implode(',',$arry_forum_friend);
            //}

            switch($search_type){

                case 1:
                    $search_type_html='人員';
                    $sql="
                            SELECT
                                `user`.`member`.`uid`,
                                `user`.`member`.`name`,
                                `user`.`member`.`sex`,

                                `user`.`class_name`.`class_name`,
                                `user`.`class`.`grade`,
                                `user`.`school`.`school_name`
                            FROM `user`.`member`
                                LEFT JOIN `user`.`student` ON
                                `user`.`member`.`uid`=`user`.`student`.`uid`

                                INNER JOIN `user`.`class` ON
                                `user`.`student`.`class_code`=`user`.`class`.`class_code`

                                INNER JOIN `user`.`class_name` ON
                                `user`.`class`.`class_category`=`user`.`class_name`.`class_category`

                                INNER JOIN `user`.`semester` ON
                                `user`.`class`.`semester_code`=`user`.`semester`.`semester_code`

                                INNER JOIN `user`.`school` ON
                                `user`.`semester`.`school_code`=`user`.`school`.`school_code`
                            WHERE 1=1
                                AND `user`.`member`.`uid`     <>{$sess_user_id}
                                AND `user`.`member`.`permission`<>'x'
                                AND `user`.`class`.`classroom`=`user`.`class_name`.`classroom`
                                AND `user`.`member`.`name` like '%{$search_value}%'
                                AND CURDATE() BETWEEN `user`.`student`.`start` AND `user`.`student`.`end`

                        UNION

                            SELECT
                                `user`.`member`.`uid`,
                                `user`.`member`.`name`,
                                `user`.`member`.`sex`,

                                `user`.`class_name`.`class_name`,
                                `user`.`class`.`grade`,
                                `user`.`school`.`school_name`
                            FROM `user`.`member`
                                LEFT JOIN `user`.`teacher` ON
                                `user`.`member`.`uid`=`user`.`teacher`.`uid`

                                INNER JOIN `user`.`class` ON
                                `user`.`teacher`.`class_code`=`user`.`class`.`class_code`

                                INNER JOIN `user`.`class_name` ON
                                `user`.`class`.`class_category`=`user`.`class_name`.`class_category`

                                INNER JOIN `user`.`semester` ON
                                `user`.`class`.`semester_code`=`user`.`semester`.`semester_code`

                                INNER JOIN `user`.`school` ON
                                `user`.`semester`.`school_code`=`user`.`school`.`school_code`
                            WHERE 1=1
                                AND `user`.`member`.`uid`     <>{$sess_user_id}
                                AND `user`.`member`.`permission`<>'x'
                                AND `user`.`class`.`classroom`=`user`.`class_name`.`classroom`
                                AND `user`.`member`.`name` like '%{$search_value}%'
                                AND CURDATE() BETWEEN `user`.`teacher`.`start` AND `user`.`teacher`.`end`

                        UNION

                            SELECT
                                `user`.`member`.`uid`,
                                `user`.`member`.`name`,
                                `user`.`member`.`sex`,

                                '' AS `class_name`,
                                '' AS `grade`,
                                `user`.`school`.`school_name`
                            FROM `user`.`member`
                                INNER JOIN `user`.personnel ON
                                `user`.`member`.`uid`=`user`.personnel.`uid`

                                INNER JOIN `user`.`school` ON
                                `user`.personnel.`school_code`=`user`.`school`.`school_code`
                            WHERE 1=1
                                AND `user`.`member`.`uid`     <>{$sess_user_id}
                                AND `user`.`member`.`permission`<>'x'
                                AND `user`.`member`.`name` like '%{$search_value}%'

                        GROUP BY `uid`
                        ORDER BY `grade`
                        LIMIT 200;
                    ";
                break;

                case 2:
                    $search_type_html='書籍';
                    $sql="
                            SELECT
                                `mssr`.`mssr_book_class`.`book_isbn_10`,
                                `mssr`.`mssr_book_class`.`book_sid`,
                                `mssr`.`mssr_book_class`.`book_name`
                            FROM `mssr`.`mssr_book_class`
                            WHERE 1=1
                                AND `mssr`.`mssr_book_class`.`book_name` like '%{$search_value}%'

                        UNION ALL

                            SELECT
                                `mssr`.`mssr_book_library`.`book_isbn_10`,
                                `mssr`.`mssr_book_library`.`book_sid`,
                                `mssr`.`mssr_book_library`.`book_name`
                            FROM `mssr`.`mssr_book_library`
                            WHERE 1=1
                                AND `mssr`.`mssr_book_library`.`book_name` like '%{$search_value}%'

                        UNION ALL

                            SELECT
                                `mssr`.`mssr_book_global`.`book_isbn_10`,
                                `mssr`.`mssr_book_global`.`book_sid`,
                                `mssr`.`mssr_book_global`.`book_name`
                            FROM `mssr`.`mssr_book_global`
                            WHERE 1=1
                                AND `mssr`.`mssr_book_global`.`book_name` like '%{$search_value}%'
                        GROUP BY `book_isbn_10`
                        LIMIT 200;
                    ";
                    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    $rs_arry_book_sid=array();
                    $rs_book_sid_list='';
                    if(!empty($db_results)){
                        foreach($db_results as $db_result){
                            $rs_book_sid=trim($db_result['book_sid']);
                            if($rs_book_sid!=='')$rs_arry_book_sid[]=$rs_book_sid;
                        }
                        $rs_book_sid_list="'".implode("','",$rs_arry_book_sid)."'";
                    }
                    if(trim($rs_book_sid_list)!==''){
                        $sql="
                                SELECT
                                    `mssr`.`mssr_book_class`.`book_isbn_13`,
                                    `mssr`.`mssr_book_class`.`book_sid`,
                                    `mssr`.`mssr_book_class`.`book_name`
                                FROM `mssr`.`mssr_book_class`
                                WHERE 1=1
                                    AND `mssr`.`mssr_book_class`.`book_sid` IN ({$rs_book_sid_list})
                                    AND `mssr`.`mssr_book_class`.`book_name` like '%{$search_value}%'

                            UNION ALL

                                SELECT
                                    `mssr`.`mssr_book_library`.`book_isbn_13`,
                                    `mssr`.`mssr_book_library`.`book_sid`,
                                    `mssr`.`mssr_book_library`.`book_name`
                                FROM `mssr`.`mssr_book_library`
                                WHERE 1=1
                                    AND `mssr`.`mssr_book_library`.`book_sid` IN ({$rs_book_sid_list})
                                    AND `mssr`.`mssr_book_library`.`book_name` like '%{$search_value}%'

                            UNION ALL

                                SELECT
                                    `mssr`.`mssr_book_global`.`book_isbn_13`,
                                    `mssr`.`mssr_book_global`.`book_sid`,
                                    `mssr`.`mssr_book_global`.`book_name`
                                FROM `mssr`.`mssr_book_global`
                                WHERE 1=1
                                    AND `mssr`.`mssr_book_global`.`book_sid` IN ({$rs_book_sid_list})
                                    AND `mssr`.`mssr_book_global`.`book_name` like '%{$search_value}%'
                            GROUP BY `book_isbn_13`
                            LIMIT 200;
                        ";
                    }
                break;

                case 3:
                    $search_type_html='小組';
                    $sql="
                        SELECT
                            `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`,
                            `user`.`member`.`name`,
                            `mssr_forum`.`mssr_forum_group`.`group_id`,
                            `mssr_forum`.`mssr_forum_group`.`group_name`,
                            `mssr_forum`.`mssr_forum_group`.`group_content`
                        FROM `mssr_forum`.`mssr_forum_group`
                            INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                            `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`

                            INNER JOIN `user`.`member` ON
                            `user`.`member`.`uid`=`mssr_forum`.`mssr_forum_group_user_rev`.`user_id`
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_group`.`group_name` like '%{$search_value}%'
                            AND `mssr_forum`.`mssr_forum_group`.`group_state`=1

                            #AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_type` =2
                            AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`=1
                        GROUP BY `mssr_forum`.`mssr_forum_group`.`group_id`
                        LIMIT 200;
                    ";
                break;

                case 4:
                    $search_value=(int)$search_value;
                    $search_type_html='文章編號';
                    $sql="
                        SELECT
                            `user`.`member`.`name`,

                            `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,

                            `mssr_forum`.`mssr_forum_article`.`user_id`,
                            `mssr_forum`.`mssr_forum_article`.`article_from`,
                            `mssr_forum`.`mssr_forum_article`.`article_id`,
                            `mssr_forum`.`mssr_forum_article`.`article_like_cno`,
                            `mssr_forum`.`mssr_forum_article`.`article_report_cno`,
                            `mssr_forum`.`mssr_forum_article`.`keyin_mdate`,

                            `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                            `mssr_forum`.`mssr_forum_article_detail`.`article_content`
                        FROM `mssr_forum`.`mssr_forum_article_book_rev`
                            INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                            `mssr_forum`.`mssr_forum_article_book_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`

                            INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                            `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                            INNER JOIN `user`.`member` ON
                            `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                            AND `mssr_forum`.`mssr_forum_article`.`article_id`   ={$search_value}
                        LIMIT 1
                    ";
                break;
            }
            //echo "<Pre>";
            //print_r($sql);
            //echo "</Pre>";
            $query_wall_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

        //-----------------------------------------------
        //自建的小組 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_group`.`group_id`,
                    `mssr_forum`.`mssr_forum_group`.`group_name`
                FROM `mssr_forum`.`mssr_forum_group`
                    INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                    `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_group`.`group_state`=1
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`={$sess_user_id}
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_type` IN (2,3)
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state` IN (1)
                ORDER BY `mssr_forum`.`mssr_forum_group_user_rev`.`keyin_cdate` DESC
            ";
            $my_create_group_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

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
                        AND `mssr_forum`.`mssr_forum_article`.`article_state`=1

                UNION ALL

                    SELECT
                        `mssr_forum`.`mssr_forum_reply_book_rev`.`book_sid`,
                        `mssr_forum`.`mssr_forum_reply`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_reply_book_rev`
                        INNER JOIN `mssr_forum`.`mssr_forum_reply` ON
                        `mssr_forum`.`mssr_forum_reply_book_rev`.`article_id`=`mssr_forum`.`mssr_forum_reply`.`article_id`

                        INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                        `mssr_forum`.`mssr_forum_reply`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_reply_book_rev`.`reply_id`=`mssr_forum`.`mssr_forum_reply`.`reply_id`
                        AND `mssr_forum`.`mssr_forum_reply`.`group_id`=0
                        AND `mssr_forum`.`mssr_forum_article`.`article_state`=1
                        AND `mssr_forum`.`mssr_forum_reply`.`reply_state`=1

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
                        AND `mssr_forum`.`mssr_forum_group`.`group_state`=1

                UNION ALL

                    SELECT
                        `mssr_forum`.`mssr_forum_group`.`group_id`,
                        `mssr_forum`.`mssr_forum_reply`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_reply`
                        INNER JOIN `mssr_forum`.`mssr_forum_group` ON
                        `mssr_forum`.`mssr_forum_reply`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_reply`.`group_id`<>0
                        AND `mssr_forum`.`mssr_forum_group`.`group_state`=1

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

            $cdate_filter=date("Y-m-d H:i:s",strtotime("-15 days"));
            $hot_member_results=array();
            $sql="
                    SELECT
                        `mssr_forum`.`mssr_forum_article`.`user_id`,
                        `mssr_forum`.`mssr_forum_article`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_article`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_article`.`keyin_cdate` > '{$cdate_filter}'

                UNION ALL

                    SELECT
                        `mssr_forum`.`mssr_forum_reply`.`user_id`,
                        `mssr_forum`.`mssr_forum_reply`.`keyin_cdate`
                    FROM `mssr_forum`.`mssr_forum_reply`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_article`.`keyin_cdate` > '{$cdate_filter}'

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

        <!-- 內容,start -->
        <div class="row">

            <!-- 查詢牆,start -->
            <div class="wall col-xs-12 col-sm-10 col-md-10 col-lg-10">
                <div style="font-size:14px;color:#4e4e4e;margin-top:10px;">
                    <b>以下為搜尋【<?php echo htmlspecialchars($search_type_html);?>】的結果：</b>
                    <span><?php echo htmlspecialchars($search_value);?></span>
                </div>
                <?php if(!empty($query_wall_results)):?>

                    <?php if($search_type===1):?>
                    <?php
                        $arry_user_id=array();
                        foreach($query_wall_results as $query_wall_result):
                            $rs_user_id     =(int)$query_wall_result['uid'];
                            $rs_user_sex    =(int)$query_wall_result['sex'];
                            $rs_user_name   =trim($query_wall_result['name']);
                            $rs_grade       =(int)$query_wall_result['grade'];
                            $rs_class_name  =trim($query_wall_result['class_name']);
                            $rs_school_name =trim($query_wall_result['school_name']);

                            if(!in_array($rs_user_id,$arry_user_id)){
                                $arry_user_id[]=$rs_user_id;
                            }else{
                                continue;
                            }

                            if($rs_user_sex===1)$user_img='../img/default/user_boy.png';
                            if($rs_user_sex===2)$user_img='../img/default/user_girl.png';

                            //加為好友
                            $btn_add_friend_show=true;
                            $btn_add_friend_disabled=false;
                            $btn_add_friend_html=trim('加為好友');
                            if($sess_user_id!==$rs_user_id){
                                $get_forum_friend=get_forum_friend($sess_user_id,$rs_user_id,$arry_conn_mssr);
                                if(empty($get_forum_friend)){
                                    $btn_add_friend_show=true;
                                    $btn_add_friend_disabled=false;
                                    $btn_add_friend_html=trim('加為好友');
                                }else{
                                    if((int)$get_forum_friend[0]['friend_state']===1){$btn_add_friend_show=true;$btn_add_friend_disabled=true;$btn_add_friend_html=trim('已是好友');}
                                    if((int)$get_forum_friend[0]['friend_state']===2){$btn_add_friend_show=true;$btn_add_friend_disabled=false;$btn_add_friend_html=trim('加為好友');}
                                    if((int)$get_forum_friend[0]['friend_state']===3){$btn_add_friend_show=true;$btn_add_friend_disabled=true;$btn_add_friend_html=trim('好友確認中');}
                                }
                            }else{
                                $btn_add_friend_show=false;
                                $btn_add_friend_disabled=true;
                            }
                    ?>
                        <div class="media" style="overflow:visible;">
                            <a class="pull-left" href="user.php?user_id=<?php echo $rs_user_id;?>&tab=1">
                                <img class="media-object" src="<?php echo htmlspecialchars($user_img);?>" width="64" height="64" alt="Media">
                            </a>
                            <div class="media-body" style="overflow:visible;">
                                <h5 class="media-heading">
                                    <?php echo htmlspecialchars($rs_user_name);?>，
                                    <?php echo htmlspecialchars($rs_school_name);?>
                                    <?php if($rs_grade!==0)echo '，'.htmlspecialchars($rs_grade).'年';?>
                                    <?php if($rs_class_name!=='')echo htmlspecialchars($rs_class_name).'班';?>
                                </h5><p></p>
                                <p>
                                    <button type="button" class="btn btn-default btn-xs pull-left" style="position:relative;margin:0 1px;"
                                    onclick='location.href="user.php?user_id=<?php echo $rs_user_id;?>&tab=1";void(0);'>
                                        前往觀看
                                    </button>

                                    <?php if($btn_add_friend_show):?>
                                        <button type="button" class="btn_add_friend btn btn-default btn-xs pull-left" style="position:relative;margin:0 1px;"
                                        <?php if($btn_add_friend_disabled)echo 'disabled="disabled"';?>
                                        user_id=<?php echo $sess_user_id;?>
                                        friend_id=<?php echo $rs_user_id;?>><?php echo $btn_add_friend_html;?></button>
                                    <?php endif;?>

                                    <?php if(!empty($my_create_group_results)):?>
                                    <div class="btn-group" style="position:relative;margin:0 1px;top:-1px;">
                                        <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown">
                                            邀請加入小組 <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                        <?php foreach($my_create_group_results as $my_create_group_result):?>
                                        <?php
                                            $rs_group_id=(int)$my_create_group_result['group_id'];
                                            $rs_group_name=trim($my_create_group_result['group_name']);
                                        ?>
                                            <li onclick="add_request_join_us_group(this);void(0);"
                                            group_id="<?php echo $rs_group_id;?>"
                                            group_user_id="<?php echo $rs_user_id;?>"
                                            ><a href="javascript:void(0);"><?php echo htmlspecialchars($rs_group_name);?></a></li>
                                        <?php endforeach;?>
                                        </ul>
                                    </div>
                                    <?php else:?>
                                        <div style="clear:left;"></div>
                                    <?php endif;?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach;endif;?>

                    <?php if($search_type===2):?>
                    <?php
                        foreach($query_wall_results as $query_wall_result):
                            $rs_book_sid =trim($query_wall_result['book_sid']);
                            $rs_book_name=trim($query_wall_result['book_name']);
                            $sql="
                                SELECT `mssr`.`mssr_book_borrow_log`.`user_id`
                                FROM  `mssr`.`mssr_book_borrow_log`
                                    WHERE `mssr`.`mssr_book_borrow_log`.`user_id`= {$sess_user_id}
                                    AND `mssr`.`mssr_book_borrow_log`.`book_sid` ='{$rs_book_sid }'
                            ";
                            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    ?>
                        <div class="media">
                            <a class="pull-left" href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_book_sid);?>">
                                <img class="media-object" src="../img/default/book.png" width="64" height="64" alt="Media">
                            </a>
                            <div class="media-body">
                                <h5 class="media-heading">
                                    <?php echo htmlspecialchars($rs_book_name);?>
                                    <?php if(count($db_results)!==0)echo '(已借閱)';?>
                                </h5><p></p>
                                <p>
                                    <button type="button" class="btn btn-default btn-xs pull-left"
                                    onclick='location.href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_book_sid);?>";void(0);'>
                                        前往觀看
                                    </button>
                                </p>
                            </div>
                        </div>
                    <?php endforeach;endif;?>

                    <?php if($search_type===3):?>
                    <?php
                        foreach($query_wall_results as $query_wall_result):
                            $rs_user_id         =(int)$query_wall_result['user_id'];
                            $rs_user_name       =trim($query_wall_result['name']);
                            $rs_group_id        =(int)$query_wall_result['group_id'];
                            $rs_group_name      =trim($query_wall_result['group_name']);
                            $rs_group_content   =trim($query_wall_result['group_content']);
                    ?>
                        <div class="media">
                            <a class="pull-left" href="article.php?get_from=2&group_id=<?php echo addslashes($rs_group_id);?>">
                                <img class="media-object" src="../img/default/group.jpg" width="64" height="64" alt="Media">
                            </a>
                            <div class="media-body">
                                <h5 class="media-heading">
                                    <?php echo htmlspecialchars($rs_group_name);?>，
                                    版主：【<a href="user.php?user_id=<?php echo $rs_user_id;?>&tab=1"><?php echo htmlspecialchars($rs_user_name);?></a>】
                                </h5>
                                <h6>小組簡介：<?php echo htmlspecialchars($rs_group_content);?></h6>
                                <p>
                                    <button type="button" class="btn btn-default btn-xs pull-left"
                                    onclick='location.href="article.php?get_from=2&group_id=<?php echo addslashes($rs_group_id);?>";void(0);'>
                                        前往觀看
                                    </button>
                                    <!-- <button type="button" class="btn_join_group btn btn-default btn-xs pull-left"
                                    user_id=<?php echo $sess_user_id;?>
                                    group_id="<?php echo $rs_group_id;?>"
                                    >申請加入小組</button> -->
                                </p>
                            </div>
                        </div>
                    <?php endforeach;endif;?>

                    <?php if($search_type===4):?>
                    <?php
                        foreach($query_wall_results as $query_wall_result):
                            $rs_article_book_sid    =trim($query_wall_result['book_sid']);
                            $rs_article_title       =trim($query_wall_result['article_title']);
                            $rs_article_content     =trim($query_wall_result['article_content']);
                            $rs_article_user_name   =trim($query_wall_result['name']);
                            $rs_article_keyin_mdate =trim($query_wall_result['keyin_mdate']);
                            $rs_article_like_cno    =(int)($query_wall_result['article_like_cno']);
                            $rs_article_id          =(int)($query_wall_result['article_id']);
                            $rs_article_user_id     =(int)($query_wall_result['user_id']);
                            $rs_article_from        =(int)$query_wall_result['article_from'];

                            //特殊處理
                            $rs_article_book_name='';
                            $arry_book_infos=get_book_info($conn_mssr,$rs_article_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                            if(!empty($arry_book_infos)){$rs_article_book_name=trim($arry_book_infos[0]['book_name']);}else{}
                            $rs_article_book_img='../img/default/book.png';
                            if(file_exists("../../../info/book/{$rs_article_book_sid}/img/front/simg/1.jpg")){
                                $rs_article_book_img="../../../info/book/{$rs_article_book_sid}/img/front/simg/1.jpg";
                            }

                            $get_book_sid=mysql_prep(trim($rs_article_book_sid));

                            $rs_article_img='../img/default/user_boy.png';
                    ?>
                        <div class="media">
                            <a class="pull-left" href="reply.php?get_from=<?php echo addslashes($rs_article_from);?>&article_id=<?php echo addslashes($rs_article_id);?>">
                                <img class="media-object" src="../img/default/article.jpg" width="64" height="64" alt="Media">
                            </a>
                            <div class="media-body">
                                <h5 class="media-heading">
                                    <?php echo htmlspecialchars($rs_article_title);?>
                                </h5><p></p>
                                <p>
                                    <button type="button" class="btn btn-default btn-xs pull-left"
                                    onclick='location.href="reply.php?get_from=<?php echo addslashes($rs_article_from);?>&article_id=<?php echo addslashes($rs_article_id);?>";void(0);'>
                                        前往觀看
                                    </button>
                                </p>
                            </div>
                        </div>
                    <?php endforeach;endif;?>

                    <div class="media">
                        <a class="pull-left" href="javascript:void(0);"></a>
                        <div class="media-body">
                            <h4 class="media-heading text-center">若查無相關資訊，請使用更精確的字眼查詢</h4>
                        </div>
                    </div>

                <?php else:?>
                    <div class="media">
                        <a class="pull-left" href="javascript:void(0);"></a>
                        <div class="media-body">
                            <h4 class="media-heading">很抱歉，查無相關資訊，請使用更精確的字眼查詢...</h4>
                        </div>
                    </div>
                <?php endif;?>
            </div>
            <!-- 查詢牆,end -->

            <!-- right_side,start -->
            <div class="right_side col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <div class="row hidden-xs" style="border:0px solid #dddddd;border-radius:5px;margin-top:0px;">
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden" style="color:#9197a3;margin-top:10px;margin-left:-10px;">
                        <b>常用</b>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden">
                        <a href="user.php?user_id=<?php echo $sess_user_id;?>&tab=6">
                            <em class="glyphicon glyphicon-comment"></em> 我的訊息 <span class='hidden'>Messages</span>
                        </a>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden">
                        <a href="user.php?user_id=<?php echo $sess_user_id;?>&tab=2">
                            <em class="glyphicon glyphicon-book"></em> 我的書櫃 <span class='hidden'>Bookcases</span>
                        </a>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden">
                        <a href="user.php?user_id=<?php echo $sess_user_id;?>&tab=3">
                            <em class="glyphicon glyphicon-pencil"></em> 我的討論 <span class='hidden'>Articles</span>
                        </a>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden">
                        <a href="user.php?user_id=<?php echo $sess_user_id;?>&tab=5">
                            <em class="glyphicon glyphicon-user"></em> 我的好友 <span class='hidden'>Friends</span>
                        </a>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden">
                        <a href="user.php?user_id=<?php echo $sess_user_id;?>&tab=4">
                            <em class="glyphicon glyphicon-star"></em> 我的小組 <span class='hidden'>Groups</span>
                        </a>
                    </div>

                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12" style="color:#9197a3;margin-left:-10px;">
                        <b>本週熱門</b>
                    </div>
                    <div class="index_left_side col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <a href="forum.php?method=view_hot_booklist">
                            <em class="glyphicon glyphicon glyphicon-list-alt"></em> 熱門書單 <span class='hidden'>Hot Booklist</span>
                        </a>
                    </div>
                </div>

                <table class="table" style="margin-top:25px;">
                    <thead>
                        <tr><td class="text-center">熱門書籍</td></tr>
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
                        <tr><td class="text-center">熱門小組</td></tr>
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
                        <tr><td class="text-center">名人區</td></tr>
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
    function add_request_join_us_group(obj){
    //邀請好友加入小組

        var $group_id     =parseInt(trim($(obj).attr('group_id')));
        var $group_user_id=parseInt(trim($(obj).attr('group_user_id')));
        var arry_err=[];

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(!confirm('你確定要邀請嗎?')){
                return false;
            }else{
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
                        group_id    :encodeURI(trim($group_id                   )),
                        friend_id   :encodeURI(trim($group_user_id              )),
                        method      :encodeURI(trim('add_request_join_us_group' )),
                        send_url    :encodeURI(trim(send_url                    ))
                    },

                //事件
                    beforeSend  :function(){
                    //傳送前處理
                    },
                    success     :function(respones){
                    //成功處理
                        alert(respones);
                        return true;
                    },
                    error       :function(xhr, ajaxoptions, thrownerror){
                    //失敗處理
                    },
                    complete    :function(){
                    //傳送後處理
                    }
                });
            }
        }
    }

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