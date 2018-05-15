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
        require_once(str_repeat("../",4).'config/config.php');

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

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
    //user_id       使用者主索引
    //branch_id     分店主索引

        //GET
		$user_id  =(isset($_GET[trim('user_id')]))?$_GET[trim('user_id')]:0;
        $branch_id=(isset($_GET[trim('branch_id')]))?$_GET[trim('branch_id')]:0;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //user_id       使用者主索引
    //branch_id     分店主索引

        $arry_err=array();

        if($user_id===''){
           $arry_err[]='使用者主索引,未輸入!';
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $arry_err[]='使用者主索引,錯誤!';
            }
        }

        if($branch_id===''){
           $arry_err[]='分店主索引,未輸入!';
        }else{
            $branch_id=(int)$branch_id;
            if($branch_id===0){
                $arry_err[]='分店主索引,錯誤!';
            }
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

        //建立連線 mssr
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //建立連線 user
        $conn_user=conn($db_type='mysql',$arry_conn_user);

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
        //分店類型撈取
        //-----------------------------------------------

            $sess_school_code=mysql_prep($sess_school_code);

            $sql="
                SELECT
                    `sqry`.`branch_name`,
                    `mssr_book_category`.`cat_code`
                FROM(
                    SELECT
                        `mssr_branch`.`branch_name`
                    FROM `mssr_branch`
                    WHERE 1=1
                        AND `mssr_branch`.`branch_id`={$branch_id}
                    LIMIT 1
                ) AS `sqry`
                    INNER JOIN `mssr_book_category` ON
                    `sqry`.`branch_name`=`mssr_book_category`.`cat_name`
                WHERE 1=1
                    AND `mssr_book_category`.`school_code`='{$sess_school_code}'
            ";
            $arrys_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
            if(!empty($arrys_results)){
                $rs_cat_code=trim($arrys_results[0]['cat_code']);
            }else{
                die();
            }

        //-----------------------------------------------
        //主SQL撈取
        //-----------------------------------------------

            $sql="
                SELECT
                    *
                FROM `mssr_user_branch_revenue_log`
                WHERE 1=1
                    AND `mssr_user_branch_revenue_log`.`user_id`      ={$user_id  }
                    AND `mssr_user_branch_revenue_log`.`branch_id`    ={$branch_id}
                ORDER BY `mssr_user_branch_revenue_log`.`keyin_cdate` DESC
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

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
    <?php echo bing_analysis($allow=true);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>

    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/array/code.js"></script>

    <!-- 專屬 -->

    <style>
        /* 微調 */
        body{
            overflow:hidden;
            position:relative;
            margin:0px;
            padding:0px;
            margin:0 auto;

            border:0px solid #ff0000;
        }
    </style>
</Head>

<Body>

    <!-- 容器區塊 開始 -->
    <?php if(!empty($db_results)):?>
    <table cellpadding="0" cellspacing="0" border="0" width="100%" rules="none" style="color:#000099;font-size:14pt;"/>
        <?php foreach($db_results as $db_result):?>
        <?php
        //-----------------------------------------------
        //參數處理
        //-----------------------------------------------

            $rs_user_id         =(int)$db_result[trim('user_id      ')] ;
            $rs_branch_id       =(int)$db_result[trim('branch_id    ')] ;
            $rs_log_id          =(int)$db_result[trim('log_id       ')] ;
            $rs_revenue_sdate   =trim($db_result[trim('revenue_sdate')]);
            $rs_revenue_edate   =trim($db_result[trim('revenue_edate')]);
            $rs_revenue_coin    =(int)$db_result[trim('revenue_coin ')] ;
            $rs_keyin_cdate     =trim($db_result[trim('keyin_cdate  ')]);

        //-----------------------------------------------
        //特殊處理
        //-----------------------------------------------

            //-------------------------------------------
            //撈取該週銷售成功數
            //-------------------------------------------

                $query_sql="
                    SELECT
                        `mssr_book_booking_log`.`booking_to`
                    FROM `mssr_book_booking_log`
                        INNER JOIN `mssr_book_category_rev` ON
                        `mssr_book_booking_log`.`book_sid`=`mssr_book_category_rev`.`book_sid`
                        INNER JOIN `mssr_book_category` ON
                        `mssr_book_category_rev`.`cat_code`=`mssr_book_category`.`cat_code`
                    WHERE 1=1
                        AND `mssr_book_booking_log`.`booking_to`    = {$rs_user_id     }
                        AND `mssr_book_booking_log`.`booking_state` ='完成交易'
                        AND (
                            `mssr_book_booking_log`.`booking_edate` BETWEEN '{$rs_revenue_sdate}' AND '{$rs_revenue_edate}'
                        )

                        AND `mssr_book_category_rev`.`school_code`  ='{$sess_school_code }'
                        AND `mssr_book_category_rev`.`cat_code`     ='{$rs_cat_code }'

                        AND `mssr_book_category`.`cat_state`        ='啟用'

                    GROUP BY `mssr_book_booking_log`.`book_sid`
                ";
                $err='QUERY FAIL';
                $result=$conn_mssr->query($query_sql) or die($err);

                //取得筆數
                $rs_book_booking_cno=$result->rowCount();

            //-------------------------------------------
            //完成任務數
            //-------------------------------------------

                $query_sql="
                    SELECT
                        `user_id`
                    FROM `mssr_user_task_log`
                    WHERE 1=1
                        AND `user_id`   =  {$rs_user_id     }
                        AND `branch_id` =  {$branch_id      }
                        AND `task_sdate`>='{$rs_revenue_sdate }'
                        AND `task_edate`<='{$rs_revenue_edate }'
                        AND `task_state`= '成功'
                ";
//echo "<Pre>";
//print_r($query_sql);
//echo "</Pre>";
                $err='QUERY FAIL';
                $result=$conn_mssr->query($query_sql) or die($err);

                //取得筆數
                $rs_task_cno=$result->rowCount();


                $rs_revenue_sdate =date("m/d",strtotime($rs_revenue_sdate));
                $rs_revenue_edate =date("m/d",strtotime($rs_revenue_edate));
        ?>
        <tr align="center">
            <td width="" valign="middle" height="30px" bgcolor="#caf3ff" style='color:#000000;'>
                <?php echo htmlspecialchars($rs_revenue_sdate);?> ~
                <?php echo htmlspecialchars($rs_revenue_edate);?>
            </td>
            <td width="185px" valign="middle" height="30px" bgcolor="#caf3ff" style='color:#000000;'>
                <?php echo (int)($rs_book_booking_cno);?>
            </td>
            <td width="235px" valign="middle" height="30px" bgcolor="#caf3ff" style='color:#000000;'>
                <?php echo (int)($rs_task_cno);?>
            </td>
            <td width="190px" valign="middle" height="30px" bgcolor="#91C0E3" style='color:#000000;'>
                <img src="../../img/bar/coin.png" width="15px" height="15px" border="0" alt="葵幣"/>
                <?php echo (int)($rs_revenue_coin);?>
            </td>
        </tr>
        <?php endforeach;?>
    </table>
    <?php endif;?>
    <!-- 容器區塊 結束 -->

</Body>
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
    //函式
    //---------------------------------------------------

        function mouse_over(obj){
        //初始化, 滑鼠移入函式
            obj.style.cursor='pointer';
            return false;
        }

    //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        $(function(){

            //初始化, 禁止滑鼠滾動事件
            $(document).on("mousewheel DOMMouseScroll", function(e){
                e.preventDefault();
                return false;
            });
        });

</script>
</Html>