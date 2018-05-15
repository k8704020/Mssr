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
		$user_id=(isset($_GET[trim('user_id')]))?(int)$_GET[trim('user_id')]:$sess_uid;
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
                    if($sess_grade===0){
                        $sess_grade=(int)$arrys_result[0]['grade'];
                    }
                }
            }

        //---------------------------------------------------
        //撈取分店類型相關
        //---------------------------------------------------

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
        //分店類型撈取
        //-----------------------------------------------

            $sess_school_code=mysql_prep($sess_school_code);

            $sql="
                SELECT
                    `mssr_book_category`.`cat_code`
                FROM `mssr_branch`
                    INNER JOIN `mssr_book_category` ON
                    `mssr_branch`.`branch_name`=`mssr_book_category`.`cat_name`
                WHERE 1=1
                    AND `mssr_branch`.`branch_id`           = {$branch_id       }
                    AND `mssr_book_category`.`school_code`  ='{$sess_school_code}'
            ";
            $arrys_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
            if(!empty($arrys_results)){
                $rs_cat_code=trim($arrys_results[0]['cat_code']);
            }else{
                die();
            }

        //-----------------------------------------------
        //分店名稱撈取
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_branch`.`branch_name`
                FROM `mssr_user_branch`
                    INNER JOIN `mssr_branch` ON
                    `mssr_user_branch`.`branch_id`=`mssr_branch`.`branch_id`
                WHERE 1=1
                    AND `mssr_user_branch`.`user_id`={$user_id}
                    AND `mssr_branch`.`branch_state`='啟用'
                    AND `mssr_branch`.`branch_id`   ={$branch_id}
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(!empty($arrys_result)){
                $branch_name=trim($arrys_result[0]['branch_name']);
            }else{
                die("DB_RESULT: QUERY FAIL!");
            }

        //-----------------------------------------------
        //主SQL撈取
        //-----------------------------------------------

            $arrys_book_info=array();
            $rs_cat_code    =mysql_prep($rs_cat_code);

            $sql="
                SELECT
                    *
                FROM(
                    SELECT
                        `mssr_book_read_opinion_log`.`borrow_sdate`,
                        `mssr_book_read_opinion_log`.`book_sid`
                    FROM `mssr_book_read_opinion_log`
                    WHERE 1=1
                        AND `mssr_book_read_opinion_log`.`user_id`      ={$user_id}
                        AND `mssr_book_read_opinion_log`.`keyin_cdate` >='2013-09-01'
                ) AS `sqry`
                GROUP BY `sqry`.`book_sid`
                ORDER BY `sqry`.`borrow_sdate` DESC
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $inx=>$db_result){
                    $rs_book_sid=mysql_prep(trim($db_result['book_sid']));
                    $sql="
                        SELECT
                            `cat_code`
                        FROM `mssr_book_category_rev`
                        WHERE 1=1
                            AND `book_sid`='{$rs_book_sid}'
                            AND `cat_code`='{$rs_cat_code}'
                    ";
                    $arrys_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                    if(empty($arrys_results)){
                        unset($db_results[$inx]);
                    }
                }
                sort($db_results);
                $arrys_book_info=$db_results;
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
    <?php echo bing_analysis($allow=true);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>

    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/array/code.js"></script>

    <script type="text/javascript" src="../../../branch/inc/add_action_branch_log/code.js"></script>

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
		.ba_1
		{
		background: #c9dbfd; /* Old browsers */
		background: -moz-linear-gradient(top,  #c9dbfd 0%, #ffffff 21%, #fcfcff 82%, #c9dbfd 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#c9dbfd), color-stop(21%,#ffffff), color-stop(82%,#fcfcff), color-stop(100%,#c9dbfd)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #c9dbfd 0%,#ffffff 21%,#fcfcff 82%,#c9dbfd 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #c9dbfd 0%,#ffffff 21%,#fcfcff 82%,#c9dbfd 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #c9dbfd 0%,#ffffff 21%,#fcfcff 82%,#c9dbfd 100%); /* IE10+ */
		background: linear-gradient(to bottom,  #c9dbfd 0%,#ffffff 21%,#fcfcff 82%,#c9dbfd 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#c9dbfd', endColorstr='#c9dbfd',GradientType=0 ); /* IE6-9 */
		}
    </style>
</Head>

<Body>

    <!-- 容器區塊 開始 -->
    <?php if(!empty($arrys_book_info)):?>
    <table cellpadding="0" cellspacing="0" border="0" width="100%" rules="none" style='font-size:14pt;'/>
        <?php foreach($arrys_book_info as $arry_book_info):?>
        <?php
        //-----------------------------------------------
        //參數處理
        //-----------------------------------------------

            $rec_flag               =0;
            $rs_rec_stat_html       ='';
            $rs_rec_draw_html       ='';
            $rs_rec_text_html       ='';
            $rs_rec_record_html     ='';
            $rs_book_sid            =mysql_prep(trim($arry_book_info['book_sid']));
            $rs_borrow_sdate        =date("Y-m-d",strtotime(trim($arry_book_info['borrow_sdate'])));

            $rs_borrow_sdate_second =(int)strtotime(trim($arry_book_info['borrow_sdate']));
            $now_second             =(int)strtotime(date("Y-m-d H:i:s"));

        //-----------------------------------------------
        //特殊處理
        //-----------------------------------------------

            //-------------------------------------------
            //書名
            //-------------------------------------------

                if(trim($rs_book_sid)!==''){
                    $get_book_info=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                    if(!empty($get_book_info)){
                        $rs_book_name=trim($get_book_info[0]['book_name']);
                        if(mb_strlen($rs_book_name)>15){
                            $rs_book_name=mb_substr($rs_book_name,0,15)."..";
                            $rec_flag+=1;
                        }
                    }else{
                        continue;
                    }
                }else{
                    continue;
                }

            //-------------------------------------------
            //推薦資訊
            //-------------------------------------------

                $rs_rec_cno         =0;
                $rs_deadline_html   ='過期';
                $rs_deadline_day    =0;

                $sql="
                    SELECT
                        `mssr_rec_book_cno_semester`.`rec_stat_cno`,
                        `mssr_rec_book_cno_semester`.`rec_draw_cno`,
                        `mssr_rec_book_cno_semester`.`rec_text_cno`,
                        `mssr_rec_book_cno_semester`.`rec_record_cno`
                    FROM `mssr_rec_book_cno_semester`
                    WHERE 1=1
                        AND `mssr_rec_book_cno_semester`.`user_id` = {$user_id    }
                        AND `mssr_rec_book_cno_semester`.`book_sid`='{$rs_book_sid}'
                ";
                $arrys_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                if(!empty($arrys_results)){
                    $rs_rec_stat_cno    =(int)$arrys_results[0]['rec_stat_cno'];
                    $rs_rec_draw_cno    =(int)$arrys_results[0]['rec_draw_cno'];
                    $rs_rec_text_cno    =(int)$arrys_results[0]['rec_text_cno'];
                    $rs_rrec_record_cno =(int)$arrys_results[0]['rrec_record_cno'];
                    if($rs_rec_stat_cno!==0){
                        $rs_rec_stat_html='O';
                        $rs_rec_cno=$rs_rec_cno+1;
                    }
                    if($rs_rec_draw_cno!==0){
                        $rs_rec_draw_html='O';
                        $rs_rec_cno=$rs_rec_cno+1;
                    }
                    if($rs_rec_text_cno!==0){
                        $rs_rec_text_html='O';
                        $rs_rec_cno=$rs_rec_cno+1;
                    }
                    if($rs_rrec_record_cno!==0){
                        $rs_rec_record_html='O';
                        $rs_rec_cno=$rs_rec_cno+1;
                    }
                }

            //-------------------------------------------
            //期限資訊
            //-------------------------------------------

                if($rs_rec_cno>=2){
                    $rs_deadline_html='完成';
                }else{
                    if(((int)$now_second-(int)$rs_borrow_sdate_second)<=1209600){
                        $rs_deadline=(int)1209600-((int)$now_second-(int)$rs_borrow_sdate_second);
                        $rs_deadline_day=(int)(((int)$rs_deadline/86400));
                        $rs_deadline_html="剩{$rs_deadline_day}天";
                        $rec_flag+=1;
                    }else{
                        continue;
                    }
                }
        ?>
        <tr align="center" <?php //if($rs_deadline_html!=='過期')echo 'onclick="alert(123);void(0);"';?> style="overflow: hidden; height:10px;">
            <td width="" valign="middle" height="30px" bgcolor="#ffdabd" style="display:inline; overflow: hidden; height:10px;">
                <div style="display:inline;">
                    <?php echo htmlspecialchars($rs_book_name);?>
                </div><!--$rs_book_sid-->
            </td>
            <td width="50px" valign="middle" height="30px" bgcolor="#ffdabd" >
                <div style="cursor:pointer;<? if($user_id != $_SESSION['uid']) echo "display:none;";?>"  onClick="go_rec('<?php echo htmlspecialchars($rs_book_sid);?>','<?php echo htmlspecialchars($rs_book_name);?>')">
                	<img src="img/btn1.png" width="91" height="31" >
                </div>
            </td>
            <td width="70px" valign="middle" height="30px" bgcolor="#ffdabd">
                <?php echo htmlspecialchars($rs_rec_stat_html);?>
            </td>
            <td width="70px" valign="middle" height="30px" bgcolor="#ffdabd">
                <?php echo htmlspecialchars($rs_rec_draw_html);?>
            </td>
            <td width="70px" valign="middle" height="30px" bgcolor="#ffdabd">
                <?php echo htmlspecialchars($rs_rec_text_html);?>
            </td>
            <td width="70px" valign="middle" height="30px" bgcolor="#ffdabd">
                <?php echo htmlspecialchars($rs_rec_record_html);?>
            </td>
            <td width="110px" valign="middle" height="30px" bgcolor="#ffdabd">
                <?php echo htmlspecialchars($rs_borrow_sdate);?>
            </td>
            <!-- <td width="" valign="middle" height="30px" bgcolor="#ffdabd">
                <?php echo htmlspecialchars($rs_deadline_html);?>
            </td> -->
        </tr>
        <?php endforeach;?>
    </table>
    <?php else:?>
    <table cellpadding="0" cellspacing="0" border="0" width="100%" rules="rows"/>
        <tr align='center'>
            <td>
                目前尚未有推薦資料。
            </td>
        </tr>
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

        var branch_name='<?php echo trim($branch_name);?>';

    //---------------------------------------------------
    //物件
    //---------------------------------------------------

        var json_category_info=<?php echo $json_category_info;?>;

    //---------------------------------------------------
    //函式
    //---------------------------------------------------

		function go_rec(value,value2){

            //action_log
            for(key1 in json_category_info['lv1']){

                var cat_id  =parseInt(json_category_info['lv1'][key1]['cat_id']);
                var cat_name=trim(json_category_info['lv1'][key1]['cat_name']);

                if(cat_name===branch_name){
                    //console.log(cat_id);
                    //action_log
                    add_action_branch_log(
                        '../../../branch/inc/add_action_branch_log/code.php',
                        'rep04',
                        <?php echo $sess_uid;?>,
                        <?php echo $user_id;?>,
                        value,
                        0,
                        <?php echo $branch_id;?>,
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
                        //console.log(key2);
                        //action_log
                        add_action_branch_log(
                            '../../../branch/inc/add_action_branch_log/code.php',
                            'rep04',
                            <?php echo $sess_uid;?>,
                            <?php echo $user_id;?>,
                            value,
                            0,
                            <?php echo $branch_id;?>,
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
                            //console.log(cat_id);
                            add_action_branch_log(
                                '../../../branch/inc/add_action_branch_log/code.php',
                                'rep04',
                                <?php echo $sess_uid;?>,
                                <?php echo $user_id;?>,
                                value,
                                0,
                                <?php echo $branch_id;?>,
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
            setTimeout(function(){
                window.parent.parent.location.replace( "../../../bookstore/code.php?do=rec&book_sid="+value+"&book_name="+value2);
            }, 500);
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

            //初始化, 禁止滑鼠滾動事件
            $(document).on("mousewheel DOMMouseScroll", function(e){
                e.preventDefault();
                return false;
            });
        });

</script>
</Html>