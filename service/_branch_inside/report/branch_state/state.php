<head>
<script src="../../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript" src="../../../branch/inc/add_action_branch_log/code.js"></script>
<style>
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

</head>

<?php
//---------------------------------------------------
// 書籍報表 : 書籍 : 本周藏書
//---------------------------------------------------

//---------------------------------------------------
//設定與引用
//---------------------------------------------------

	//SESSION
	@session_start();

	//啟用BUFFER
	@ob_start();

	//外掛設定檔
	require_once(str_repeat("../",4)."/config/config.php");
	require_once(str_repeat("../",4)."/inc/get_book_info/code.php");

	 //外掛函式檔
	$funcs=array(
				APP_ROOT.'inc/code',
				APP_ROOT.'lib/php/db/code',
				APP_ROOT.'lib/php/array/code'
				);
	func_load($funcs,true);


	//清除並停用BUFFER
	@ob_end_clean();

	//建立連線 user
	$conn_user=conn($db_type='mysql',$arry_conn_user);
	$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

	?>
    <script type="text/javascript" src="../../../../lib/js/plugin/func/canvas/chart/code.js"></script>
	<?php
//---------------------------------------------------
//END   設定與引用
//---------------------------------------------------

    $sess_uid        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:0;
    $sess_class_code =(isset($_SESSION['class'][0][1]))?trim($_SESSION['class'][0][1]):'';
    $sess_school_code=(isset($_SESSION['school_code']))?trim($_SESSION['school_code']):'';
    $sess_sem_year   =(isset($_SESSION['sem_year']))?(int)$_SESSION['sem_year']:0;
    $sess_sem_term   =(isset($_SESSION['sem_term']))?(int)$_SESSION['sem_term']:0;
    $sess_grade      =(isset($_SESSION['grade']))?(int)$_SESSION['grade']:0;

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
//接收,設定參數
//---------------------------------------------------
    $user_id=(isset($_GET[trim('user_id')]))?(int)$_GET[trim('user_id')]:$sess_uid;
    $branch_id = $_GET["branch_id"] ;
//---------------------------------------------------
//檢驗參數
//---------------------------------------------------

//---------------------------------------------------
//撈取, 學校資訊
//---------------------------------------------------

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
//主SQL撈取
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

//---------------------------------------------------
//SQL
//---------------------------------------------------
$data = array();
//閱讀

		$sql = "
					SELECT
						b.book_sid
					FROM
					(
						SELECT a.mintime,
						       a.book_sid,
							   a.user_id
						FROM
						(
							SELECT   MIN(mssr_book_read_opinion_log.keyin_cdate) AS mintime,
									`book_sid`,
									`user_id`
							FROM `mssr_book_read_opinion_log`
							WHERE `user_id` ='{$user_id}'
							GROUP BY mssr_book_read_opinion_log.`book_sid`
						)AS a
						WHERE a.mintime   >=  '2013-09-01'
					)AS b

					LEFT JOIN mssr_book_category_rev
					ON mssr_book_category_rev.book_sid = b.book_sid

					LEFT JOIN mssr_book_category
					ON mssr_book_category_rev.cat_code = mssr_book_category.cat_code
					AND mssr_book_category.school_code = 'gcp'

					LEFT JOIN mssr_branch
					ON mssr_branch.branch_name = mssr_book_category.cat_name
					WHERE mssr_branch.branch_id = '{$branch_id}'

					GROUP BY b.book_sid";

		$retrun_2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
	 	$data['read'] = sizeof($retrun_2);

//推薦
$count = 0;
$sql = "
		SELECT
				b.book_sid,
				b.rec_stat_cno,
				b.rec_draw_cno,
				b.rec_text_cno,
			    b.rec_record_cno
		FROM
		(
			SELECT a.mintime,
			 a.book_sid,
			a.user_id,
			a.rec_stat_cno,
			a.rec_draw_cno,
			a.rec_text_cno,
			a.rec_record_cno
			FROM
			(
				SELECT   MIN(mssr_rec_book_cno_semester.keyin_cdate) AS mintime,
						rec_stat_cno,
						rec_draw_cno,
						rec_text_cno,
						rec_record_cno,
						`book_sid`,
						`user_id`
				FROM `mssr_rec_book_cno_semester`
				WHERE `user_id` ='{$user_id}'
				GROUP BY mssr_rec_book_cno_semester.`book_sid`
			)AS a
			WHERE a.mintime   >=  '2013-09-01'
		)AS b

		LEFT JOIN mssr_book_category_rev
		ON mssr_book_category_rev.book_sid = b.book_sid

		LEFT JOIN mssr_book_category
		ON mssr_book_category_rev.cat_code = mssr_book_category.cat_code
		AND mssr_book_category.school_code = 'gcp'

		LEFT JOIN mssr_branch
		ON mssr_branch.branch_name = mssr_book_category.cat_name
		WHERE mssr_branch.branch_id = '{$branch_id}'

		GROUP BY b.book_sid
		";
		$retrun_2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($retrun_2 as $key2=>$val2)
		{
			$re_count = 0;
			if($val2["rec_draw_cno"] > 0) $re_count++;
			if($val2["rec_text_cno"] > 0) $re_count++;
			if($val2["rec_record_cno"] > 0) $re_count++;

			if($re_count >= 1 )$count++;
		}
$data['rec'] = $count;
//看與讀
$sql = "
				SELECT b.book_sid
				FROM
				(
					SELECT a.mintime,
						   a.book_sid
					FROM
					(
						SELECT MIN(`mssr_book_booking_log`.booking_edate)AS mintime,
							   book_sid
						FROM  `mssr_book_booking_log`
						WHERE `mssr_book_booking_log`.`booking_from` = '{$user_id}'
						AND booking_state = '完成交易'
						GROUP BY `mssr_book_booking_log`.book_sid
					)AS a
					WHERE a.mintime >= '2013-09-01'
				)AS b

				LEFT JOIN mssr_book_category_rev
				ON mssr_book_category_rev.book_sid = b.book_sid

				LEFT JOIN mssr_book_category
				ON mssr_book_category_rev.cat_code = mssr_book_category.cat_code
				AND mssr_book_category.school_code = 'gcp'

				LEFT JOIN mssr_branch
				ON mssr_branch.branch_name = mssr_book_category.cat_name

				WHERE
				mssr_branch.branch_id = '{$branch_id}'

				GROUP BY b.book_sid


				";

$retrun_2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
$data['booking_from'] = sizeof($retrun_2);
//被看與被讀
$sql = "
				SELECT b.book_sid
				FROM
				(
					SELECT a.mintime,
						   a.book_sid
					FROM
					(
						SELECT MIN(`mssr_book_booking_log`.booking_edate)AS mintime,
							   book_sid
						FROM  `mssr_book_booking_log`
						WHERE `mssr_book_booking_log`.`booking_from` = '{$user_id}'
						AND booking_state = '完成交易'
						GROUP BY `mssr_book_booking_log`.book_sid
					)AS a
					WHERE a.mintime >= '2013-09-01'
				)AS b

				LEFT JOIN mssr_book_category_rev
				ON mssr_book_category_rev.book_sid = b.book_sid

				LEFT JOIN mssr_book_category
				ON mssr_book_category_rev.cat_code = mssr_book_category.cat_code
				AND mssr_book_category.school_code = 'gcp'

				LEFT JOIN mssr_branch
				ON mssr_branch.branch_name = mssr_book_category.cat_name

				WHERE
				mssr_branch.branch_id = '{$branch_id}'

				GROUP BY b.book_sid
				";

$retrun_2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
$data['booking_to'] =  sizeof($retrun_2);




//---------------------------------------------------
//HTML
//---------------------------------------------------

?>
<body>
<canvas id="canvas" height="280" width="300" style="left:40px; top:10px; position: absolute;"></canvas>
<script>
function stat_no(value)
{
    var branch_name='<?php echo $branch_name;?>';
    var json_category_info=<?php echo $json_category_info;?>;
    var action_code='';

    // window.parent.parent.parent.add_debug("分店概況報表開啟:點選:"+value);
    if("rec" == value){window.parent.document.getElementById('title').innerHTML = "推薦統計";action_code='rep09';}
    if("read" == value){window.parent.document.getElementById('title').innerHTML = "近期閱讀統計";action_code='rep08';}
    if("booking_to" == value){window.parent.document.getElementById('title').innerHTML = "我的訂閱書單";action_code='rep10';}
    if("booking_from" == value){window.parent.document.getElementById('title').innerHTML = "我被訂閱的書籍";action_code='rep11';}
    window.parent.document.getElementsByName('state')[0].src="./branch_state/loading.php";
    window.parent.document.getElementsByName('state')[0].src="./branch_state/"+value+".php?user_id=<?php echo $user_id;?>&branch_id=<?php echo $branch_id;?>";

    //action_log
    for(key1 in json_category_info['lv1']){

        var cat_id  =parseInt(json_category_info['lv1'][key1]['cat_id']);
        var cat_name=(json_category_info['lv1'][key1]['cat_name']);

        if(cat_name===branch_name){
            //action_log
            add_action_branch_log(
                '../../../branch/inc/add_action_branch_log/code.php',
                action_code,
                <?php echo $sess_uid;?>,
                <?php echo $user_id;?>,
                '',
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
            var cat_name=(json_category_info['lv2'][key2][key3]['cat_name']);

            if(cat_name===branch_name){
                //action_log
                add_action_branch_log(
                    '../../../branch/inc/add_action_branch_log/code.php',
                    action_code,
                    <?php echo $sess_uid;?>,
                    <?php echo $user_id;?>,
                    '',
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
                    add_action_branch_log(
                        '../../../branch/inc/add_action_branch_log/code.php',
                        action_code,
                        <?php echo $sess_uid;?>,
                        <?php echo $user_id;?>,
                        '',
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
}

    var radarChartData = {
        labels : [
            "",
            "",
            "",
            ""
        ],
        datasets : [
            {
                fillColor       : "rgba(255,150,70,0.7)",
                strokeColor     : "rgba(205,150,70,1)",
                pointColor      : "rgba(205,150,70,1)",

                pointStrokeColor: "#fff",
                data: [
                    <?php echo $data['read'];?>,
					<?php echo $data['rec'];?>,
					<?php echo $data['booking_from'];?>,
					<?php echo $data['booking_to'];?>,
                ]
            }
        ]
    }

    //附加
	
    window.parent.parent.parent.add_debug("報表讀取:資料:"+radarChartData.datasets[0].data);
</script>

<?php
$chart_options = 'scaleShowLabels : true';
$chart_options = $chart_options.',pointLabelFontSize : 15';
$chart_options = $chart_options.',scaleLineColor : "rgba(0,0,0,0.4)"';
$chart_options = $chart_options.',pointDot : false';
$chart_options = $chart_options.',scaleFontSize : 14';
$chart_options = $chart_options.',scaleBackdropColor : "rgba(255,255,255,0)"';
$chart_options = $chart_options.',pointLabelFontSize : 22';
$chart_options = $chart_options.',angleLineColor : "rgba(0,0,0,.8)"';
$chart_options = $chart_options.',pointLabelFontColor : "#000"';
echo '<script>var myRadar = new Chart(document.getElementById("canvas").getContext("2d")).Radar(radarChartData,{'.$chart_options.'});</script>';
?>
<img src="img/btn1.png" style="position: absolute; height:31px; width:96px; height:34px; left: 288px; top: 131px;">
<img src="img/btn1.png" style="position: absolute; height:31px; width:96px; height:34px; left: 144px; top: 249px;">
<img src="img/btn1.png" style="position: absolute; height:31px; width:96px; height:34px; left: -6px; top: 130px;">
<img src="img/btn1.png" style="position: absolute; height:31px; width:96px; height:34px; left: 149px; top: 8px;">
<div align="center"  style="left:149px; top:10px; position: absolute; height:31px; width:90px; font-size:22px; cursor:pointer;" onClick="stat_no('read');">閱讀<?php echo $data['read'];?>本</div>
<div align="center" style="left:290px; top:132px; position: absolute; height:31px; width:90px; font-size:22px;cursor:pointer;" onClick="stat_no('rec');">推薦<?php echo $data['rec'];?>本</div>
<div align="center" style="left:147px; top:252px; position: absolute; height:31px; width:90px; font-size:22px;cursor:pointer;" onClick="stat_no('booking_from');">銷售<?php echo $data['booking_from'];?>本</div>
<div align="center" style="left:0px; top:131px; position: absolute; height:31px; width:90px; font-size:22px;cursor:pointer;" onClick="stat_no('booking_to');">購買<?php echo $data['booking_to'];?>本</div>

</body>