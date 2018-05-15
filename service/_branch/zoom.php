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
        //獲取開分店的數量
        //-----------------------------------------------
			$branch_count = array();
			$branch = array();
			$branch_lv = array();
			$branch_read = array();
			$branch_rec = array();
			$branch_comment_score = array();
			for($i = 1 ; $i <= 6 ; $i++)//初始化
			{
				$branch_rec[$i]=0;
				$branch_lv[$i]=0;
				$branch_count[$i]["branch_count_max"] = 0;
				$branch_count[$i]["branch_count"] = 0;
				$branch_comment_score[$i]["branch_count"] = 0;
				$branch_comment_score[$i]["branch_score"] = 0;
				$branch_comment_score[$i]["branch_score_average"] = '';
			}
			
            $sql="
				SELECT *
				FROM
				(
					SELECT cat_id,cat1_id
					FROM  `mssr_book_category` 
					LEFT JOIN mssr_branch
					ON mssr_book_category.cat_name = mssr_branch.branch_name
					WHERE  `school_code` LIKE  'gcp'
					AND mssr_branch.branch_state  = '啟用'
					GROUP BY cat1_id,branch_name
				)AS A
				LEFT JOIN
				(
					SELECT branch_id  
					FROM  `mssr_user_branch` 
					WHERE  `user_id` ={$uid}
					AND  `branch_state` LIKE  '啟用'
				)AS B
				ON B.branch_id = A.cat_id
            ";
            
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
           	foreach($arrys_result as $inx => $value)
			{
				$branch_count[$value["cat1_id"]]["branch_count_max"]++;
				if($value["branch_id"]!=NULL)
				{//有開分店狀況
					$branch_count[$value["cat1_id"]]["branch_count"]++;
				 	$branch.array_push($value["branch_id"]);
				}
			}
			
			//計算發展度
			for($i = 2; $i <= 6 ;$i++)
			{
				$tmp = ($branch_count[$i]["branch_count"] / $branch_count[$i]["branch_count_max"]);
				if($tmp < 0.333){$branch_lv[$i]=1;}
				else if($tmp < 0.666){$branch_lv[$i]=2;}
				else{$branch_lv[$i]=3;}
				
			}
		//-----------------------------------------------
        //獲取總閱讀數目
        //-----------------------------------------------
			for($i = 2; $i <= 6;$i++)
			{
				$sql = "
						SELECT
							b.book_sid,
							c.comment_score
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
								WHERE `user_id` ='{$uid}'
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
						
	
						LEFT JOIN
						(SELECT y.comment_score,
								   y.book_sid
							FROM
							(
								SELECT
										comment_score,
								  		book_sid 
								FROM `mssr_rec_comment_log` 
								WHERE `comment_to` = '{$uid}'
								ORDER BY `mssr_rec_comment_log`.`keyin_cdate` DESC
							)AS y
							GROUP BY y.`book_sid`
						)AS c
						ON c.book_sid = b.book_sid
						
						WHERE mssr_branch.branch_id = ".$i."
						GROUP BY b.book_sid";
	
				$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
				$branch_read[$i] = sizeof($retrun);
				foreach($retrun as $inx => $value)
				{
					if($value['comment_score']!= NULL)
					{
						$branch_comment_score[$i]["branch_count"]++;
						$branch_comment_score[$i]["branch_score"] += $value['comment_score'];
					}
				}
				if($branch_comment_score[$i]["branch_count"]!=0)$branch_comment_score[$i]["branch_score_average"]=number_format(floor($branch_comment_score[$i]["branch_score"]/$branch_comment_score[$i]["branch_count"]*10)/10,2);
				
				
				//推薦UP
				$sql = "SELECT 
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
								WHERE `user_id` ='{$uid}'
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
						WHERE mssr_branch.branch_id = '{$i}'
				
						GROUP BY b.book_sid";
				$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);	
				foreach($retrun as $key2=>$val2)
				{
					$tmp=0;
					if($val2["rec_stat_cno"]>0)$tmp++;
					if($val2["rec_draw_cno"]>0)$tmp++;
					if($val2["rec_text_cno"]>0)$tmp++;
					if($val2["rec_record_cno"]>0)$tmp++;
					if($tmp > 0)$branch_rec[$i] ++ ;
				}
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
                    `mssr_branch`.`branch_coordinate`,
                    `mssr_branch`.`branch_coordinate_small`
                FROM `mssr_user_branch`
                    INNER JOIN `mssr_branch` ON
                    `mssr_user_branch`.`branch_id`=`mssr_branch`.`branch_id`
                WHERE 1=1
                    AND `mssr_user_branch`.`user_id`={$uid}
                    AND `mssr_branch`.`branch_state`='啟用'
            ";
            //echo "<Pre>";
            //print_r($sql);
            //echo "</Pre>";
            //die();

            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($arrys_result)){
                $arrys_branch=$arrys_result;
                $json_branch=json_encode($arrys_branch,true);
            }else{
                die("DB_RESULT: QUERY FAIL!");
            }
			
			
			/*branch_score_average*/
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
    <script type="text/javascript" src="../../inc/code.js"></script>

    <script type="text/javascript" src="../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../lib/jquery/plugin/func/block_ui/code.js"></script>
    <script type="text/javascript" src="../../lib/jquery/ui/code.js"></script>

    <script type="text/javascript" src="../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../lib/js/array/code.js"></script>

    <!-- 專屬 -->
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
		.text_color1
		{
			color:#FF0000;		
		}
		.text_color2
		{
			color:#F90;	
		}
		.text_color3
		{
			color:#00CC00;
		}
		.ff_color
		{
			border: 1px solid #666;
			background: rgb(242,246,248); /* Old browsers */
			background: -moz-linear-gradient(top,  rgba(242,246,248,1) 0%, rgba(216,225,231,1) 9%, rgba(181,198,208,1) 16%, rgba(224,239,249,1) 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(242,246,248,1)), color-stop(9%,rgba(216,225,231,1)), color-stop(16%,rgba(181,198,208,1)), color-stop(100%,rgba(224,239,249,1))); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  rgba(242,246,248,1) 0%,rgba(216,225,231,1) 9%,rgba(181,198,208,1) 16%,rgba(224,239,249,1) 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  rgba(242,246,248,1) 0%,rgba(216,225,231,1) 9%,rgba(181,198,208,1) 16%,rgba(224,239,249,1) 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  rgba(242,246,248,1) 0%,rgba(216,225,231,1) 9%,rgba(181,198,208,1) 16%,rgba(224,239,249,1) 100%); /* IE10+ */
			background: linear-gradient(to bottom,  rgba(242,246,248,1) 0%,rgba(216,225,231,1) 9%,rgba(181,198,208,1) 16%,rgba(224,239,249,1) 100%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f2f6f8', endColorstr='#e0eff9',GradientType=0 ); /* IE6-9 */
			
			-webkit-border-radius: 8px;     /*圓角for Google Chrome、Safari*/
			-moz-border-radius: 8px;     /*圓角for Firefox*/
			border-radius: 8px;     /*圓角for IE*/
			
			-webkit-box-shadow: #666 0px 2px 3px;     /*陰影for Google Chrome、Safari*/
			-moz-box-shadow: #666 0px 2px 3px;     /*陰影for Firefox*/
			box-shadow: #666 0px 2px 3px;     /*陰影for IE*/
		}
		.box
		{
			-webkit-border-radius: 8px;     /*圓角for Google Chrome、Safari*/
			-moz-border-radius: 8px;     /*圓角for Firefox*/
			border-radius: 8px;     /*圓角for IE*/
			
			-webkit-box-shadow: #666 0px 2px 3px;     /*陰影for Google Chrome、Safari*/
			-moz-box-shadow: #666 0px 2px 3px;     /*陰影for Firefox*/
			box-shadow: #666 0px 2px 3px;     /*陰影for IE*/
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

        /*#light_box_branch_lv1{
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
        }*/
    </style>
    
    <?php 
	//====================================================
	//SQL
	//====================================================
	
	
	
	
	
	//====================================================
	//
	//====================================================
	?>
</Head>

<Body bgcolor="#333333">
	 <!--==================================================
    html內容
    ====================================================== -->
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
        <input type="button" value="關閉全景圖" style='position:absolute;z-index:999;' onclick='zoom();void(0);'>
        <input type="button" value="開啟簡圖" style='position:absolute; z-index:999; top: 0px; left: 95px;' onclick='zoom_the_ohhhhhh("block");void(0);'>
        <!-- <img id='background' src="img/obj/word.jpg" width="150px" height="100px" border="0"
        style='position:absolute;right:0;z-index:999;'/> -->
        <!-- 內容區塊 開始 -->
        <div id="box">
        <div id="content">
            <img id='background' src="img/obj/background_1.jpg" width="1000px" height="500px" border="0"
            style='position:absolute;z-index:4;top:310px;left:740px;'/>

            <?php if(($arry_branch_lv_ready_flag[1]==='false')&&($arry_branch_lv_ready_flag[2]==='false')):?>
                <!-- <div id="light_box_branch_lv1"></div> -->
            <?php endif;?>

            <?php if(($arry_branch_lv_ready_flag[1]==='true')&&($arry_branch_lv_ready_flag[2]==='false')):?>
                <!-- <div id="light_box_branch_lv2"></div> -->
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
                $rs_branch_coordinate       =trim($arry_branch['branch_coordinate_small']);

            //-------------------------------------------
            //定位
            //-------------------------------------------

                $rs_branch_position         =explode(",",$rs_branch_coordinate);
                $rs_branch_position_x       =(isset($rs_branch_position[0]))?(int)$rs_branch_position[0]:$sess_uid;
                $rs_branch_position_y       =(isset($rs_branch_position[1]))?(int)$rs_branch_position[1]+25:$sess_uid;

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
                                        $rs_branch_position_x=$rs_branch_position_x+55;
                                        $rs_branch_position_y=$rs_branch_position_y+105;
                                    }
                                    if($rs_branch_id===4){
                                        $rs_branch_position_x=$rs_branch_position_x+50;
                                        $rs_branch_position_y=$rs_branch_position_y+70;
                                    }
                                    if($rs_branch_id===5){
                                        $rs_branch_position_x=$rs_branch_position_x+50;
                                        $rs_branch_position_y=$rs_branch_position_y+60;
                                    }
                                    if($rs_branch_id===6){
                                        $rs_branch_position_x=$rs_branch_position_x+50;
                                        $rs_branch_position_y=$rs_branch_position_y+70;
                                    }
                                }
                            break;
                        }
                    }else{
                        $rs_branch_position_x=$rs_branch_position_x+50;
                        if($rs_branch_id===2){
                            $rs_branch_position_x=$rs_branch_position_x-50;
                            $rs_branch_position_y=$rs_branch_position_y-70;
                        }
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
                    //$has_task_inventory='false';
                    //if($rs_branch_state==='啟用'){
                    //    $sql="
                    //        SELECT
                    //            `mssr_user_task_inventory`.`task_coin_bonus`,
                    //            COUNT(`mssr_user_task_inventory`.`user_id`) AS `task_cno`
                    //        FROM `mssr_user_task_inventory`
                    //            INNER JOIN `mssr_task_period` ON
                    //            `mssr_user_task_inventory`.`task_sid`=`mssr_task_period`.`task_sid`
                    //        WHERE 1=1
                    //            AND `mssr_user_task_inventory`.`user_id`    ={$rs_user_id  }
                    //            AND `mssr_user_task_inventory`.`branch_id`  ={$rs_branch_id}
                    //
                    //            AND `mssr_task_period`.`task_state`         ='啟用'
                    //        GROUP BY `task_coin_bonus`
                    //    ";
                    //    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    //    if(!empty($arrys_result)){
                    //        $has_task_inventory='true';
                    //        $task_cno=0;
                    //        foreach($arrys_result as $inx=>$arry_result){
                    //            $rs_task_coin_bonus =(int)$arry_result['task_coin_bonus'];
                    //            $rs_task_cno        =(int)$arry_result['task_cno'];
                    //            $task_cno=$task_cno+$rs_task_cno;
                    //        }
                    //        if($rs_task_coin_bonus===3){
                    //            $task_img='img/obj/task_2.png';
                    //        }elseif($rs_task_coin_bonus===1){
                    //            $task_img='img/obj/task_2.png';
                    //        }else{
                    //            $task_img='';
                    //        }
                    //    }
                    //}
            ?>

            <!-- 書店名稱 -->
            <table cellpadding="0" cellspacing="0" border="0"
            style="position:absolute;left:<?php echo ($rs_branch_position_x);?>px;top:<?php echo ($rs_branch_position_y+20);?>px;z-index:99;"/>
                <tr>
                    <td align="center" class="number_bar" style='<?php if($rs_branch_state!=='啟用'){echo 'color:#838383;';}?>font-size:14px;"'>
                        <?php echo htmlspecialchars($rs_branch_name);?>
                    </td>
                </tr>
            </table>

            <!-- 書店任務 -->
            <?php if($task_img!==''):?>
                <!-- <div style='position:absolute;left:<?php echo ($rs_branch_position_x)+50;?>px;top:<?php echo ($rs_branch_position_y+20);?>px;z-index:99;'>
                    <img src="<?php echo $task_img;?>" width="19" height="41" border="0" alt=""/>
                </div> -->
            <?php endif;?>

            <!-- 書店圖案 -->
            <table name='tbl_branch' cellpadding="0" cellspacing="0" border="0" width='auto' height='auto'
            style="position:absolute;left:<?php echo ($rs_branch_position_x);?>px;top:<?php echo ($rs_branch_position_y);?>px;z-index:98;"
            <?php if($rs_branch_open):?>
            <?php endif;?>
            />
                <tr>
                    <td>
                        <img src="<?php echo htmlspecialchars($rs_branch_img_src);?>" width="<?php if($rs_branch_lv===1){echo '80';}else{echo '50';}?>px" height="" border="0"
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
    <!--==================================================
    html2內容
    ====================================================== -->
    
    <div id="container2" style=" position:absolute;top:0px;z-index:20000;">
    	<!--  關閉紐 -->
        <input type="button" value="關閉簡圖" style='position:absolute; z-index:999; top: 0px; left: 95px;' onclick='zoom_the_ohhhhhh("none");void(0);'>
        <div style="position:absolute; opacity:0.9; top:0px; left:0px;">
            <!--  背景 -->
            <img src="img\branch_post\back.png" style="position:absolute; top:0px; left:0px;" class="box">
            
            <!--  村落 -->
            <img id="branch2_img" src="img\branch_post\branch2_<? echo $branch_lv[2];?>.png" style="position:absolute; top:41px; left:283px;">
            <img id="branch3_img" src="img\branch_post\branch3_<? echo $branch_lv[3];?>.png" style="position:absolute; top:37px; left:528px;">
            <img id="branch4_img" src="img\branch_post\branch4_<? echo $branch_lv[4];?>.png" style="position:absolute; top:207px; left:631px;">
            <img id="branch5_img" src="img\branch_post\branch5_<? echo $branch_lv[5];?>.png" style="position:absolute; top:301px; left:371px;">
            <img id="branch6_img" src="img\branch_post\branch6_<? echo $branch_lv[6];?>.png" style="position:absolute; top:210px; left:182px;">
        </div>
        <!--  上層文字 -->
        <img src="img\branch_post\text.png" style="position:absolute; top:140px; left:311px;">
        
        <!--  數據介面 -->
        <div style="position:absolute; top:30px; left:21px; width:244px; height:112px;" class="ff_color">
            <div  style="position:absolute; top:6px; left:4px; font-size: 16pt; width: 237px;">閱讀量 : <? echo $branch_read[2];?>本</div>
            <div  style="position:absolute; top:33px; left:4px; font-size: 16pt; width: 237px;">推薦量 : <? echo $branch_rec[2];?>本</div>
            <div  style="position:absolute; top:60px; left:4px; font-size: 16pt; width:<? if($branch_comment_score[2]["branch_score_average"]!=''){echo (int)(100+(90/5*$branch_comment_score[2]["branch_score_average"]));}else{echo 184;};?>px; height:35px; overflow:hidden; white-space:nowrap;"><? if($branch_comment_score[2]["branch_score_average"]!=''){echo "推薦品質 : ★★★★★";}else{echo "推薦品質 : --";};?></div>
        	<div  style="position:absolute; top:87px; left:4px; font-size: 16pt; width: 237px;">店面數 : <a class="text_color<? echo $branch_lv[2];?>"><? echo $branch_count[2]["branch_count"];?></a><? echo " / ".$branch_count[2]["branch_count_max"];?>間</div>
        </div>
        
        <div style="position:absolute; top:29px; left:686px; width:219px; height:112px;" class="ff_color">
   		  <div  style="position:absolute; top:6px; left:4px; font-size: 16pt; width: 237px;">閱讀量 : <? echo $branch_read[3];?>本</div>
        	<div  style="position:absolute; top:33px; left:4px; font-size: 16pt; width: 237px;">推薦量 : <? echo $branch_rec[3];?>本</div>
            <div  style="position:absolute; top:60px; left:4px; font-size: 16pt; width:<? if($branch_comment_score[3]["branch_score_average"]!=''){echo (int)(100+(90/5*$branch_comment_score[3]["branch_score_average"]));}else{echo 184;};?>px; height:35px; overflow:hidden; white-space:nowrap;"><? if($branch_comment_score[3]["branch_score_average"]!=''){echo "推薦品質 : ★★★★★";}else{echo "推薦品質 : --";};?></div>
        	<div  style="position:absolute; top:87px; left:4px; font-size: 16pt; width: 237px;">店面數 : <a class="text_color<? echo $branch_lv[3];?>"><? echo $branch_count[3]["branch_count"];?></a><? echo " / ".$branch_count[3]["branch_count_max"];?>間</div>
        </div>
        
        <div style="position:absolute; top:237px; left:759px; width:219px; height:112px;" class="ff_color">
   		  <div  style="position:absolute; top:6px; left:4px; font-size: 16pt; width: 237px;">閱讀量 : <? echo $branch_read[4];?>本</div>
        	<div  style="position:absolute; top:33px; left:4px; font-size: 16pt; width: 237px;">推薦量 : <? echo $branch_rec[4];?>本</div>
            <div  style="position:absolute; top:60px; left:4px; font-size: 16pt; width:<? if($branch_comment_score[4]["branch_score_average"]!=''){echo (int)(100+(90/5*$branch_comment_score[4]["branch_score_average"]));}else{echo 184;};?>px; height:35px; overflow:hidden; white-space:nowrap;"><? if($branch_comment_score[4]["branch_score_average"]!=''){echo "推薦品質 : ★★★★★";}else{echo "推薦品質 : --";};?></div>
       		<div  style="position:absolute; top:87px; left:4px; font-size: 16pt; width: 237px;">店面數 : <a class="text_color<? echo $branch_lv[4];?>"><? echo $branch_count[4]["branch_count"];?></a><? echo " / ".$branch_count[4]["branch_count_max"];?>間</div>
        </div>
        
        <div style="position:absolute; top:341px; left:525px; width:219px; height:112px;" class="ff_color">
            <div  style="position:absolute; top:6px; left:4px; font-size: 16pt; width: 237px;">閱讀量 : <? echo $branch_read[5];?>本</div>
            <div  style="position:absolute; top:33px; left:4px; font-size: 16pt; width: 237px;">推薦量 : <? echo $branch_rec[5];?>本</div>
            <div  style="position:absolute; top:60px; left:4px; font-size: 16pt; width:<? if($branch_comment_score[5]["branch_score_average"]!=''){echo (int)(100+(90/5*$branch_comment_score[5]["branch_score_average"]));}else{echo 184;};?>px; height:35px; overflow:hidden; white-space:nowrap;"><? if($branch_comment_score[5]["branch_score_average"]!=''){echo "推薦品質 : ★★★★★";}else{echo "推薦品質 : --";};?></div>
        	<div  style="position:absolute; top:87px; left:4px; font-size: 16pt; width: 237px;">店面數 : <a class="text_color<? echo $branch_lv[5];?>"><? echo $branch_count[5]["branch_count"];?></a><? echo " / ".$branch_count[5]["branch_count_max"];?>間</div>
        </div>
        
        <div style="position:absolute; top:239px; left:6px; width:219px; height:112px;" class="ff_color">
   		  <div  style="position:absolute; top:6px; left:4px; font-size: 16pt; width: 237px;">閱讀量 : <? echo $branch_read[6];?>本</div>
        	<div  style="position:absolute; top:33px; left:4px; font-size: 16pt; width: 237px;">推薦量 : <? echo $branch_rec[6];?>本</div>
            <div  style="position:absolute; top:60px; left:4px; font-size: 16pt; width:<? if($branch_comment_score[6]["branch_score_average"]!=''){echo (int)(100+(90/5*$branch_comment_score[6]["branch_score_average"]));}else{echo 184;};?>px; height:35px; overflow:hidden; white-space:nowrap;"><? if($branch_comment_score[6]["branch_score_average"]!=''){echo "推薦品質 : ★★★★★";}else{echo "推薦品質 : --";};?></div>
        	<div  style="position:absolute; top:87px; left:4px; font-size: 16pt; width: 237px;">店面數 : <a class="text_color<? echo $branch_lv[6];?>"><? echo $branch_count[6]["branch_count"];?></a><? echo " / ".$branch_count[6]["branch_count_max"];?>間</div>
        </div>
    </div>
    
    
    
    
</Body>
<script>
function zoom(){
            //榨乾這個頁面!!!!!!!!!!!!!!!!!
            parent.$.unblockUI();
            $(otd_ifc).empty();
        }
function zoom_the_ohhhhhh(value)
{
	window.document.getElementById("container2").style.display = value;
}
</script>

</Html>