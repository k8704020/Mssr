<?php
//-------------------------------------------------------
//明日星球, 聊書
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

        //外掛頁面檔
        require_once(str_repeat("../",0).'inc/require_page/code.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code'
       	);
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------
    //$_SESSION["uid"]=5030;

        if(!isset($_SESSION["uid"])||(int)$_SESSION["uid"]===0){
            die();
        }

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        $user_id=(int)$_SESSION["uid"];

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //search_type_val   搜尋類型
    //search_text_val   搜尋條件

        //查詢欄位,顯示用
        $query_fields=array();

        //查詢欄位,串接用
        $arr=array();

            //search_type_val   搜尋類型
            if(isset($_GET['search_type_val'])&&(trim($_GET['search_type_val']!==''))){
                $search_type_val=trim($_GET['search_type_val']);
            }else{
                $search_type_val="";
                die();
            }

            //search_text_val   搜尋條件
            if(isset($_GET['search_text_val'])&&(trim($_GET['search_text_val']!==''))){
                $search_text_val=trim($_GET['search_text_val']);
            }else{
                $search_text_val="";
                die();
            }
            $arr['search_text_val']=array(
                'n'=>'搜尋條件',        //名稱
                'v'=>$search_text_val,  //值
                'c'=>'like'             //類型
            );

        if(1==2){//除錯用
            echo "<pre>";
            print_r($arr);
            echo "</pre>";
        }

    //---------------------------------------------------
    //串接查詢欄位
    //---------------------------------------------------

        $arry_query  =mutiple_query($arr);
        $query_fields=$arry_query['query_fields'];
        $query_sql   =$arry_query['query_sql'];

	//---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //SQL-userinfo(學生|老師資訊-姓名)
        //-----------------------------------------------

            $sql="
                SELECT `name`
                FROM `member`
                WHERE `uid` = $user_id
            ";
            $arrys_result_userinfo=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);


        //-----------------------------------------------
        //SQL-usergrade(用class_code找學生年級資訊)
        //-----------------------------------------------

			$class_code = trim($_SESSION['class'][0][1]);

            $sql="
                SELECT
                    `grade`,
                    `classroom`,
                    `class_code`,
                    `school_code`
                FROM `class`
                    INNER JOIN `semester` ON
                    `class`.`semester_code`=`semester`.`semester_code`
                WHERE `class_code` = '$class_code'
            ";
            $arrys_result_usergrade=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);

        //-----------------------------------------------
        //SQL-arrys_result_user_school(用class_code找學生學校資訊)
        //-----------------------------------------------

            $user_school=trim($arrys_result_usergrade[0]['school_code']);

            $sql="
                SELECT
                    `school_name`, `region_name`
                FROM `school`
                WHERE `school_code` = '{$user_school}'
            ";
            $arrys_result_user_school=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);

        //-----------------------------------------------
        //SQL-arrys_result_top_book(書籍排行榜)
        //-----------------------------------------------

            $sql="
                SELECT
                    `book_sid`
                FROM
                    `mssr_book_borrow_semester`
                WHERE 1=1
                    AND	`school_code` 	= '{$user_school}'
                    AND	`grade_id` 		= 3
            ";
            $arrys_result_top_book=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,100),$arry_conn_mssr);
            $arry_book_sid_list=array();
            if(!empty($arrys_result_top_book)){
                $arry_list=array();
                foreach($arrys_result_top_book as  $arry_result_recommend){
                    $rs_book_sid=trim($arry_result_recommend['book_sid']);
                    if(!array_key_exists($rs_book_sid,$arry_book_sid_list)){
                        $arry_book_sid_list[$rs_book_sid]=1;
                    }else{
                        $arry_book_sid_list[$rs_book_sid]=$arry_book_sid_list[$rs_book_sid]+1;
                    }
                }
                //排序
                arsort($arry_book_sid_list);
                //篩選
                foreach($arry_book_sid_list as $book_sid=>$cno){
                    if(count($arry_list)<5){
                        $arry_list[]=trim($book_sid);
                    }else{
                        break;
                    }
                }
            }

        //-----------------------------------------------
        //SQL-arrys_result_top_group(熱門聊書小組)
        //-----------------------------------------------

            $sql="
                SELECT
                    `forum_id`
                FROM
                    `mssr_article_forum_rev`
                WHERE
                    1=1
            ";
            $arrys_result_top_group=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,100),$arry_conn_mssr);
            //$numrow_top_book=count($arrys_result_top_book);

            $arry_book_sid_list=array();
            if(!empty($arrys_result_top_group)){
                $arry_list_group=array();
                foreach($arrys_result_top_group as  $arry_result_recommend){

                    $rs_book_sid=trim($arry_result_recommend['forum_id']);

                    if(!array_key_exists($rs_book_sid,$arry_book_sid_list)){
                        $arry_book_sid_list[$rs_book_sid]=1;
                    }else{
                        $arry_book_sid_list[$rs_book_sid]=$arry_book_sid_list[$rs_book_sid]+1;
                    }
                }

                //排序
                arsort($arry_book_sid_list);

                //篩選
                foreach($arry_book_sid_list as $book_sid=>$cno){
                    if(count($arry_list_group)<3){
                        $arry_list_group[]=trim($book_sid);
                    }else{
                        break;
                    }
                }
            }

        //-----------------------------------------------
        //SQL-arrys_result_top_people(人排行榜)
        //-----------------------------------------------

            $sql="
                SELECT*
                FROM
                        (SELECT
                            `user_id`, `keyin_cdate`
                        FROM
                            `mssr_forum_article`

                    UNION ALL

                        SELECT
                            `user_id`, `keyin_cdate`
                        FROM
                            `mssr_forum_article_reply`)tmp
                WHERE 1=1
                ORDER BY
                    `keyin_cdate` DESC

            ";
            $arrys_result_top_people=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,300),$arry_conn_mssr);
            //$numrow_top_people=count($arrys_result_top_people);

            $arry_book_sid_list=array();
            if(!empty($arrys_result_top_people)){
                $arry_list_people=array();
                foreach($arrys_result_top_people as  $arry_result_recommend){

                    $rs_book_sid=trim($arry_result_recommend['user_id']);

                    if(!array_key_exists($rs_book_sid,$arry_book_sid_list)){
                        $arry_book_sid_list[$rs_book_sid]=1;
                    }else{
                        $arry_book_sid_list[$rs_book_sid]=$arry_book_sid_list[$rs_book_sid]+1;
                    }
                }

                //排序
                arsort($arry_book_sid_list);

                //篩選
                foreach($arry_book_sid_list as $book_sid=>$cno){
                    if(count($arry_list_people)<5){
                        $arry_list_people[]=trim($book_sid);
                    }else{
                        break;
                    }
                }
            }

        //-----------------------------------------------
        //撈取我的好友
        //-----------------------------------------------

            $arry_friend=[];
            $friend_list="0";

            //是否成為朋友
            $sql="
                SELECT
                    `user_id`,
                    `friend_id`
                FROM `mssr_forum_friend`
                WHERE 1=1
                    AND (
                        `user_id`  ={$user_id}
                            OR
                        `friend_id`={$user_id}
                    )
                    AND `friend_state` IN ('成功')
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($arrys_result)){
                foreach($arrys_result as $arry_result){
                    extract($arry_result, EXTR_PREFIX_ALL, "rs");
                    if((int)$rs_user_id===(int)$user_id){
                        $arry_friend[]=(int)$rs_friend_id;
                    }
                    if((int)$rs_friend_id===(int)$user_id){
                        $arry_friend[]=(int)$rs_user_id;
                    }
                }
                $friend_list=implode(",",$arry_friend);
            }

        //-----------------------------------------------
        //主SQL處理
        //-----------------------------------------------
//echo "<Pre>";
//print_r($friend_list);
//echo "</Pre>";
//die();
            switch($search_type_val){
                case '人':
                    $query_sql=str_replace('search_text_val','`user`.`member`.`name`',$query_sql);
                    $sql="
                        SELECT *
                        FROM(
                            SELECT
                                IFNULL((
                                    SELECT
                                        COUNT(*)
                                    FROM `mssr`.`mssr_forum_friend`
                                    WHERE 1=1
                                        AND (
                                            `user_id`  IN ({$friend_list})
                                                OR
                                            `friend_id`IN ({$friend_list})
                                        )
                                        AND (
                                            `user_id`   =`user`.`member`.`uid`
                                                OR
                                            `friend_id` =`user`.`member`.`uid`
                                        )
                                        AND (
                                            `user_id`   <>{$user_id}
                                                OR
                                            `friend_id` <>{$user_id}
                                        )
                                        AND `friend_state` IN ('成功')
                                ),0) AS `same_friend_cno`,

                                `user`.`member`.`uid`,
                                `user`.`member`.`name`,

                                `user`.`class_name`.`class_name`,
                                `user`.`class`.`grade`
                            FROM `user`.`member`
                                LEFT JOIN `user`.`student` ON
                                `user`.`member`.`uid`=`user`.`student`.`uid`

                                INNER JOIN `user`.`class` ON
                                `user`.`student`.`class_code`=`user`.`class`.`class_code`

                                INNER JOIN `user`.`class_name` ON
                                `user`.`class`.`class_category`=`user`.`class_name`.`class_category`
                            WHERE 1=1
                                AND `user`.`member`.`uid`     <>{$user_id}
                                AND `user`.`class`.`classroom`=`user`.`class_name`.`classroom`
                                AND (
                                    `user`.`student`.`class_code` =(
                                        SELECT
                                            `user`.`student`.`class_code`
                                        FROM `user`.`student`
                                        WHERE 1=1
                                            AND `user`.`student`.`uid`   ={$user_id}
                                            AND `user`.`student`.`start`<= CURDATE()
                                            AND `user`.`student`.`end`  >= CURDATE()
                                        ORDER BY `user`.`student`.`start`,
                                                 `user`.`student`.`end`
                                        LIMIT 1
                                    )
                                    OR
                                    `user`.`student`.`class_code` =(
                                        SELECT
                                            `user`.`teacher`.`class_code`
                                        FROM `user`.`teacher`
                                        WHERE 1=1
                                            AND `user`.`teacher`.`uid`   ={$user_id}
                                            AND `user`.`teacher`.`start`<= CURDATE()
                                            AND `user`.`teacher`.`end`  >= CURDATE()
                                        ORDER BY `user`.`teacher`.`start`,
                                                 `user`.`teacher`.`end`
                                        LIMIT 1
                                    )
                                    OR
                                    `user`.`student`.`class_code` IN ('gcp_2014_2_5_2','gcp_2014_2_5_5')
                                )

                                -- filter 在此
                                {$query_sql}

                        UNION ALL

                            SELECT
                                IFNULL((
                                    SELECT
                                        COUNT(*)
                                    FROM `mssr`.`mssr_forum_friend`
                                    WHERE 1=1
                                        AND (
                                            `user_id`  IN ({$friend_list})
                                                OR
                                            `friend_id`IN ({$friend_list})
                                        )
                                        AND (
                                            `user_id`   =`user`.`member`.`uid`
                                                OR
                                            `friend_id` =`user`.`member`.`uid`
                                        )
                                        AND (
                                            `user_id`   <>{$user_id}
                                                OR
                                            `friend_id` <>{$user_id}
                                        )
                                        AND `friend_state` IN ('成功')
                                ),0) AS `same_friend_cno`,

                                `user`.`member`.`uid`,
                                `user`.`member`.`name`,

                                `user`.`class_name`.`class_name`,
                                `user`.`class`.`grade`
                            FROM `user`.`member`
                                LEFT JOIN `user`.`teacher` ON
                                `user`.`member`.`uid`=`user`.`teacher`.`uid`

                                INNER JOIN `user`.`class` ON
                                `user`.`teacher`.`class_code`=`user`.`class`.`class_code`

                                INNER JOIN `user`.`class_name` ON
                                `user`.`class`.`class_category`=`user`.`class_name`.`class_category`
                            WHERE 1=1
                                AND `user`.`member`.`uid`     <>{$user_id}
                                AND `user`.`class`.`classroom`=`user`.`class_name`.`classroom`
                                AND (
                                    `user`.`teacher`.`class_code` =(
                                        SELECT
                                            `user`.`student`.`class_code`
                                        FROM `user`.`student`
                                        WHERE 1=1
                                            AND `user`.`student`.`uid`   ={$user_id}
                                            AND `user`.`student`.`start`<= CURDATE()
                                            AND `user`.`student`.`end`  >= CURDATE()
                                        ORDER BY `user`.`student`.`start`,
                                                 `user`.`student`.`end`
                                        LIMIT 1
                                    )
                                    OR
                                    `user`.`teacher`.`class_code` =(
                                        SELECT
                                            `user`.`teacher`.`class_code`
                                        FROM `user`.`teacher`
                                        WHERE 1=1
                                            AND `user`.`teacher`.`uid`   ={$user_id}
                                            AND `user`.`teacher`.`start`<= CURDATE()
                                            AND `user`.`teacher`.`end`  >= CURDATE()
                                        ORDER BY `user`.`teacher`.`start`,
                                                 `user`.`teacher`.`end`
                                        LIMIT 1
                                    )
                                    OR
                                    `user`.`teacher`.`class_code` IN ('gcp_2014_2_5_2','gcp_2014_2_5_5')
                                )

                                -- filter 在此
                                {$query_sql}
                        ) AS `sqry`
                        WHERE 1=1
                        GROUP BY `uid`
                        ORDER BY `grade`
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
                break;

                case '書':
                    $query_sql=str_replace('search_text_val','`book_name`',$query_sql);
                    $sql="
                        SELECT *
                        FROM(
                            SELECT
                                #IFNULL((
                                #    SELECT
                                #        COUNT(*)
                                #    FROM `mssr`.`mssr_book_borrow_log`
                                #    WHERE 1=1
                                #        AND `book_sid`=`sqry`.`book_sid`
                                #        AND `user_id` IN ({$friend_list})
                                #    GROUP BY `user_id`
                                #),0) AS `same_friend_borrow_cno`,
                                `sqry`.`book_type`,
                                `sqry`.`book_name`,
                                `sqry`.`book_sid`,
                                `sqry`.`book_author`,
                                `sqry`.`book_publisher`,
                                `sqry`.`keyin_mdate`
                            FROM(

                                SELECT
                                    'mssr_book_class' AS `book_type`,
                                    `book_sid`,
                                    `book_name`,
                                    `book_author`,
                                    `book_publisher`,
                                    `keyin_mdate`
                                FROM `mssr_book_class` AS `m`
                                    INNER JOIN (
                                        SELECT
                                            `book_id`
                                        FROM `mssr_book_class`
                                        WHERE 1=1
                                            #AND `school_code`='{$user_school}'
                                            -- FILTER在此
                                            {$query_sql}
                                    ORDER BY `keyin_mdate` DESC
                                )  AS `s`
                                ON `m`.`book_id`=`s`.`book_id`

                            UNION ALL

                                SELECT
                                    'mssr_book_library' AS `book_type`,
                                    `book_sid`,
                                    `book_name`,
                                    `book_author`,
                                    `book_publisher`,
                                    `keyin_mdate`
                                FROM `mssr_book_library` AS `m`
                                    INNER JOIN (
                                        SELECT
                                            `book_id`
                                        FROM `mssr_book_library`
                                        WHERE 1=1
                                            #AND `school_code`='{$user_school}'
                                            -- FILTER在此
                                            {$query_sql}
                                        ORDER BY `keyin_mdate` DESC
                                    )  AS `s`
                                ON `m`.`book_id`=`s`.`book_id`

                            ) AS `sqry`
                            WHERE 1=1
                                -- FILTER在此
                                {$query_sql}
                        ) AS `mqry`
                        WHERE 1=1
                        ORDER BY `mqry`.`keyin_mdate` DESC
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    if(!empty($arrys_result)){
                        $book_sid_list=[];
                        foreach($arrys_result as $arry_result){
                            $rs_book_sid    =trim($arry_result['book_sid']);
                            $book_sid_list[]=$rs_book_sid;
                        }
                        if(!empty($book_sid_list))$book_sid_list=implode("','",$book_sid_list);
                        $sql="
                            SELECT
                                `user_id`
                            FROM `mssr_book_borrow_log`
                            WHERE 1=1
                                AND `user_id`={$user_id}
                                AND `book_sid` IN ('{$book_sid_list}')
                        ";
                        $has_borrow_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        $has_borrow_array=[];
                        if(!empty($has_borrow_results)){
                            foreach($has_borrow_results as $has_borrow_result){
                                $rs_book_sid    =trim($has_borrow_result['book_sid']);
                                $has_borrow_array[]=$rs_book_sid;
                            }
                        }
                    }
                break;

                case '群':
                    $query_sql=str_replace('search_text_val','`forum_name`',$query_sql);
                    $sql="
                        SELECT
                            IFNULL((
                                SELECT
                                    COUNT(*)
                                FROM `mssr_user_forum`
                                WHERE 1=1
                                    AND `mssr_user_forum`.`forum_id`=`mssr_forum`.`forum_id`
                                    AND (
                                        `user_id`  IN ({$friend_list})
                                    )
                            ),0) AS `forum_friend_cno`,
                            IFNULL((
                                SELECT
                                    COUNT(*)
                                FROM `mssr_user_forum`
                                WHERE 1=1
                                    AND `mssr_user_forum`.`forum_id`=`mssr_forum`.`forum_id`
                            ),0) AS `forum_user_cno`,
                            `forum_id`,
                            `forum_name`,
                            `forum_content`
                        FROM  `mssr_forum`
                        WHERE 1=1
                            -- FILTER在此
                            {$query_sql}
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                break;
            }
//echo "<Pre>";
//print_r($arrys_result);
//echo "</Pre>";
//die();
	//---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

	//---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球, 聊書首頁";

//echo "<Pre>";
//print_r($sql);
//echo "</Pre>";
//die();
?>
<!DOCTYPE HTML>
<html>
<head>
    <title><?php echo $title?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="Content-Language" content="UTF-8">

    <!-- 通用js  -->
    <script type="text/javascript" src="../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../inc/code.js"></script>
    <script type="text/javascript" src="../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../lib/js/table/code.js"></script>

    <!-- 專屬js  -->
    <script type="text/javascript" src="js/bootstrap.min.js"></script>

    <link type="text/css" rel="stylesheet" href="css/bootstrap.css">
    <link type="text/css" rel="stylesheet" href="css/mssr_forum.css">
    <link type="text/css" rel="stylesheet" href="../../inc/code.css">

    <style>
        /* 頁面微調 */
        .content{
            top:80px;
        }
    </style>
</head>
<body>

    <!-- navbar start -->
    <?php r_p_navbar((int)$_SESSION["uid"],$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);?>
    <!-- navbar end -->

    <!-- content start -->
    <div class="content">

        <div style="width:100%;position:relative;left:10px;font-size:16px;color:#660000;">
            <b><i><u>以下為你搜尋【<?php echo htmlspecialchars($search_type_val);?>】的結果</u>：</i></b>
            <span style='position:relative;left:10px;font-size:18px;color:#660000;'>
                <i><?php echo htmlspecialchars($search_text_val);?></i>
            </span>
        </div>

        <!-- left_content start -->
        <?php if(!empty($arrys_result)):?>
            <?php if(trim($search_type_val)==='人'):?>

                <?php
                    foreach($arrys_result as $arry_result):
                        $rs_user_id        =trim($arry_result['uid']);
                        $rs_name           =trim($arry_result['name']);
                        $rs_grade          =trim($arry_result['grade']);
                        $rs_class_name     =trim($arry_result['class_name']);
                        $rs_same_friend_cno=trim($arry_result['same_friend_cno']);
                ?>
                <div class="left_content" style="border-right:1px solid #ebebeb;">
                    <div class="media" style="border:1px solid #ebebeb; padding:15px;background-color:#ffffff;">
                        <a class="pull-left" href="mssr_forum_people_index.php?user_id=<?php echo urlencode(addslashes($rs_user_id));?>">
                            <img src="image/boy.jpg" alt="示意圖"  width="64" height="64" border='0'/>
                        </a>
                        <div class="media-body">
                            <br/>
                            <h4 class="media-heading" style='color:#ff0000;'><?php echo htmlspecialchars($rs_name);?></h4>
                            <p style='color:#777777;'>
                                <?php echo htmlspecialchars($rs_grade);?>年<?php echo htmlspecialchars($rs_class_name);?>班 , &nbsp;&nbsp;
                                (你與他有【<?php echo htmlspecialchars($rs_same_friend_cno);?>】位共同好友)
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>

            <?php elseif(trim($search_type_val)==='書'):?>

                <?php
                    foreach($arrys_result as $arry_result):
                        $rs_book_type               =trim($arry_result['book_type']);
                        $rs_book_name               =trim($arry_result['book_name']);
                        $rs_book_sid                =trim($arry_result['book_sid']);
                        $rs_book_author             =trim($arry_result['book_author']);
                        $rs_book_publisher          =trim($arry_result['book_publisher']);
                        $rs_same_friend_borrow_cno  =0;
                        $has_borrow                 =false;
                        if(in_array($rs_book_sid,$has_borrow_array))$has_borrow=true;

                        $sql="
                            SELECT
                                COUNT(*)
                            FROM `mssr`.`mssr_book_borrow_log`
                            WHERE 1=1
                                AND `book_sid`='{$rs_book_sid}'
                                AND `user_id` IN ({$friend_list})
                            GROUP BY `user_id`
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        $rs_same_friend_borrow_cno=count($db_results);

                        if($rs_book_author==='')$rs_book_author='無';
                        if($rs_book_publisher==='')$rs_book_publisher='無';
                ?>
                <div class="left_content" style="border-right:1px solid #ebebeb;">
                    <div class="media" style="border:1px solid #ebebeb; padding:15px;background-color:#ffffff;">
                        <a class="pull-left" href="mssr_forum_book_discussion.php?book_sid=<?php echo urlencode(addslashes($rs_book_sid));?>">
                            <img src="image/book.jpg" alt="示意圖"  width="64" height="64" border='0'/>
                        </a>
                        <div class="media-body">
                            <h4 class="media-heading" style='color:#ff0000;'><?php echo htmlspecialchars($rs_book_name);?><?php if($has_borrow)echo '- (已借閱)';?></h4>
                            <p style='color:#777777;'>
                                作者：<?php echo htmlspecialchars($rs_book_author);?> , 出版社：<?php echo htmlspecialchars($rs_book_publisher);?>
                            </p>
                            <p style='color:#777777;'>( <?php echo htmlspecialchars($rs_same_friend_borrow_cno);?> 位好友看過)</p>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>

            <?php elseif(trim($search_type_val)==='群'):?>

                <?php
                    foreach($arrys_result as $arry_result):
                        $rs_forum_friend_cno    =trim($arry_result['forum_friend_cno']);
                        $rs_forum_user_cno      =trim($arry_result['forum_user_cno']);
                        $rs_forum_id            =trim($arry_result['forum_id']);
                        $rs_forum_name          =trim($arry_result['forum_name']);
                        $rs_forum_content       =trim($arry_result['forum_content']);

                        if($rs_forum_content==='')$rs_forum_content='無';
                ?>
                <div class="left_content" style="border-right:1px solid #ebebeb;">
                    <div class="media" style="border:1px solid #ebebeb; padding:15px;background-color:#ffffff;">
                        <a class="pull-left" href="mssr_forum_group_index.php?forum_id=<?php echo $rs_forum_id;?>">
                            <img src="image/group.png" alt="示意圖"  width="64" height="64" border='0'/>
                        </a>
                        <div class="media-body">
                            <h4 class="media-heading" style='color:#ff0000;'><?php echo htmlspecialchars($rs_forum_name);?></h4>
                            <p style='color:#777777;'>
                                簡介：<?php echo htmlspecialchars($rs_forum_content);?>
                            </p>
                            <p style='color:#777777;'><?php echo htmlspecialchars($rs_forum_user_cno);?> 位成員 ( <?php echo htmlspecialchars($rs_forum_friend_cno);?> 位好友加入)</p>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>

            <?php endif;?>
        <?php else:?>
            <div class="left_content" style="border-right:1px solid #ebebeb;">
                <div class="media" style="border:1px solid #ebebeb; padding:15px;background-color:#ffffff;">
                    <div class="media-body">
                        <h4 class="media-heading" style='color:#ff0000;'>查無資料。</h4><br/>
                    </div>
                </div>
            </div>
        <?php endif;?>
        <!-- left_content end -->

        <!-- aside start -->
        <div class="aside">

            <!-- 個人資訊 -->
            <div class="thumbnail" style="height:120px;">
                <div class="caption">

					<?php
					//學生照片
					$get_user_info=get_user_info($conn_user,$_SESSION["uid"],$array_filter=array('sex'),$arry_conn_user);
					if($get_user_info[0]['sex']==1){?>
						<a href="javascript:void(0);">
							<img id="index_user_pic" src="image/boy.jpg" alt="<?php echo $user_id?>" width="60" height="60"/>
						</a>
					<?php }else{?>
						<a href="javascript:void(0);">
							<img id="index_user_pic" src="image/girl.jpg" alt="<?php echo $user_id?>" width="60" height="60"/>
						</a>



					<?php }?>

					<div id="index_user_name">
						<h4><font color=blue><B><?php echo $arrys_result_userinfo[0]['name'];?></B></font></h4>
						<p>	<?php echo $arrys_result_user_school[0]['region_name']?>
							<?php echo $arrys_result_user_school[0]['school_name']?><BR>
							<?php echo $arrys_result_usergrade[0]['grade']?>年
							<?php echo $arrys_result_usergrade[0]['classroom']?>班
						</p>
					</div>

                </div>
            </div>

            <!-- 熱門書籍 -->
            <table class="table">
                <thead>
                    <tr align="center"><td colspan=2>熱門書籍</td></tr>
                </thead>
                <tbody>

                    <?php if(!empty($arry_list)):?>
                        <?php foreach($arry_list as $inx=>$rs_book_sid):?>
                        <?php
                            $rs_book_sid=trim($rs_book_sid);

                            //書名
                            $arry_book_info=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                            if(!empty($arry_book_info)){
                                $rs_book_name=trim($arry_book_info[0]['book_name']);
                            }else{
                                continue;
                            }

                            //書籍圖片
                            $bookpic_root    = '../../info/book/'.$rs_book_sid.'/img/front/simg/1.jpg';
                            $bookpic_root_enc=mb_convert_encoding($bookpic_root,$fso_enc,$page_enc);
                            if(file_exists($bookpic_root_enc)){
                                $rs_bookpic_root = '../../info/book/'.$rs_book_sid.'/img/front/simg/1.jpg';
                            }else{
                                $rs_bookpic_root = 'image/book.jpg';
                            }
                        ?>
                        <tr <?php if($inx%2)echo 'class=active';?>>
                            <td width='40px'>
                                <a href="mssr_forum_book_discussion.php?book_sid=<?php echo urlencode(addslashes($rs_book_sid));?>">
                                    <img src="<?php echo $rs_bookpic_root;?>" alt=""  width="40" height="40" border='0'/>
                                </a>
                            </td>
                            <td>
                                <a href="mssr_forum_book_discussion.php?book_sid=<?php echo urlencode(addslashes($rs_book_sid));?>">
                                    <?php echo htmlspecialchars($rs_book_name);?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    <?php else:?>
                        <tr><td align='center'>目前暫無資料</td></tr>
                    <?php endif;?>

                </tbody>
            </table>

            <!-- 熱門聊書小組 -->
            <table class="table">
                <thead>
                    <tr align="center"><td colspan=2>熱門聊書小組</td></tr>
                </thead>
                <tbody>

                    <?php if(!empty($arry_list_group)):?>
                        <?php foreach($arry_list_group as $inx=>$rs_forum_id):?>
                        <?php
                            $rs_forum_id=(int)$rs_forum_id;

                            //小組名稱
                            $sql="
                                SELECT `forum_name`
                                FROM `mssr_forum`
                                WHERE `forum_id` = $rs_forum_id
                            ";
                            $arry_forum_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                            if(!empty($arry_forum_result)){
                                $rs_forum_name=trim($arry_forum_result[0]['forum_name']);
                            }else{
                                continue;
                            }

                            //聊書小組圖片
                            $forumpic_root = 'image/forum_pic_'.$rs_forum_id.'.jpg';
                            $forumpic_root_enc=mb_convert_encoding($forumpic_root,$fso_enc,$page_enc);
                            if(file_exists($forumpic_root_enc)){
                                $rs_forumpic_root = 'image/forum_pic_'.$rs_forum_id.'.jpg';
                            }else{
                                $rs_forumpic_root = 'image/group.png';
                            }
                        ?>
                        <tr <?php if($inx%2)echo 'class=active';?>>
                            <td width='40px'>
                                <a href="mssr_forum_group_discussion.php?forum_id=<?php echo urlencode(addslashes($rs_forum_id));?>">
                                    <img src="<?php echo $rs_forumpic_root;?>" alt=""  width="40" height="40" border='0'/>
                                </a>
                            </td>
                            <td>
                                <a href="mssr_forum_group_discussion.php?forum_id=<?php echo urlencode(addslashes($rs_forum_id));?>">
                                    <?php echo htmlspecialchars($rs_forum_name);?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    <?php else:?>
                        <tr><td align='center'>目前暫無資料</td></tr>
                    <?php endif;?>

                </tbody>
            </table>

            <!-- 名人區 -->
            <table class="table">
                <thead>
                    <tr align="center"><td colspan=2>名人區</td></tr>
                </thead>
                <tbody>

                    <?php if(!empty($arry_list_people)):?>
                        <?php foreach($arry_list_people as $inx=>$rs_user_id):?>
                        <?php
                            $rs_user_id=(int)$rs_user_id;

                            //人名, 性別
                            $arry_user_info=get_user_info($conn_user,$rs_user_id,$array_filter=array('name','sex'),$arry_conn_user);
                            if(!empty($arry_user_info)){
                                $rs_user_name=trim($arry_user_info[0]['name']);
                                $rs_user_sex =(int)($arry_user_info[0]['sex']);
                            }else{
                                continue;
                            }

                            //大頭貼
                            if($rs_user_sex===1){
                                $rs_userpic_root="image/boy.jpg";
                            }else{
                                $rs_userpic_root="image/girl.jpg";
                            }
                        ?>
                        <tr <?php if($inx%2)echo 'class=active';?>>
                            <td width='40px'>
                                <a href="mssr_forum_people_index.php?user_id=<?php echo urlencode(addslashes($rs_user_id));?>">
                                    <img src="<?php echo $rs_userpic_root;?>" alt=""  width="40" height="40" border='0'/>
                                </a>
                            </td>
                            <td>
                                <a href="mssr_forum_people_index.php?user_id=<?php echo urlencode(addslashes($rs_user_id));?>">
                                    <?php echo htmlspecialchars($rs_user_name);?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    <?php else:?>
                        <tr><td align='center'>目前暫無資料</td></tr>
                    <?php endif;?>

                </tbody>
            </table>
        </div>
        <!-- aside end -->

    </div>
    <!-- content end -->

<!-- 頁面js  -->
<script type="text/javascript">
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    //FUNCTION

    //ONLOAD
    $(document).ready(function(){


    });

</script>
</body>
</Html>