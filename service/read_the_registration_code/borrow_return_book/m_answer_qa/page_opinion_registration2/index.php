<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,閱讀登記 -> 回答問題
//(內頁)  //主頁面 or 內頁
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",5).'config/config.php');

       //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'service/read_the_registration_code/inc/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'center/teacher_center/inc/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['service']['read_the_registration_code']){
            $url=str_repeat("../",6).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(!login_check(array('t'))){
            die();
        }

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        //初始化，承接變數
        $_sess_t=$_SESSION['t'];
        foreach($_sess_t as $field_name=>$field_value){
            $$field_name=$field_value;
        }

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        if((!isset($_SESSION['_read_the_registration_code']['_login']))||(empty($_SESSION['_read_the_registration_code']['_login']))){
            $page=str_repeat("../",2)."login/loginF.php";

            $jscript_back="
                <script>
                    parent.location.href='{$page}';
                </script>
            ";

            die($jscript_back);
        }else{
            //借書人資訊
            $_user_id    =(int)$_SESSION['_read_the_registration_code']['_login']['_user_id'];
            $_user_name  =trim($_SESSION['_read_the_registration_code']['_login']['_user_name']);
            $_user_number=(int)$_SESSION['_read_the_registration_code']['_login']['_user_number'];

            // echo $_user_id;
        }

        //是否第一次借書
     $t_permission=$_SESSION['t']['permission'];

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //初始化, isbn碼輸入提醒
        $_isbn_code_remind='yes';

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //串接SQL
    //---------------------------------------------------

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //---------------------------------------------------
        //學生陣列
        //---------------------------------------------------

            $users=arrys_users($conn_user,addslashes(trim($_sess_t['arrys_class_code'][0]['class_code'])),$date=date("Y-m-d"),$arry_conn_user);

            // print_r($users);

        //---------------------------------------------------
        //提取圖書館的書籍資訊
        //---------------------------------------------------

            $arrys_book_library_isbn_10=array();
            $arrys_book_library_isbn_13=array();
            $arrys_book_library=arrys_book_library($conn_mssr,mysql_prep(trim($_sess_t['school_code'])),$arry_conn_mssr);
            if(!empty($arrys_book_library)){
                foreach($arrys_book_library as $inx=>$arry_book_library){
                    $book_isbn_10=trim($arry_book_library['book_isbn_10']);
                    $book_isbn_13=trim($arry_book_library['book_isbn_13']);
                    $arrys_book_library_isbn_10[]=$book_isbn_10;
                    $arrys_book_library_isbn_13[]=$book_isbn_13;
                }
            }

        //---------------------------------------------------
        //提取所有使用者的借書證資訊
        //---------------------------------------------------

            $get_user_library_card_info=get_user_library_card_info($conn_user,$users,$array_filter=array('card_number'),$arry_conn_user);

        //---------------------------------------------------
        //isbn碼輸入提醒查詢
        //---------------------------------------------------

            $isbn_code_remind=isbn_code_remind($db_type='mysql',$arry_conn_mssr,(int)$_sess_t['uid']);

            if(!$isbn_code_remind){
                $_isbn_code_remind='no';
            }

        //---------------------------------------------------
        //借閱的書本資訊
        //---------------------------------------------------

            $query_sql="
                SELECT * FROM (
                    SELECT
                        'mssr_book_class' AS `book_type`,
                        `mssr_book_borrow_tmp`.`keyin_cdate`,
                        `mssr_book_borrow_tmp`.`book_sid`,

                        `mssr_book_class`.`book_isbn_10`,
                        `mssr_book_class`.`book_isbn_13`,
                        '' AS `book_library_code`,
                        `mssr_book_class`.`book_name`,
                        `mssr_book_class`.`book_no`,
                        `mssr_book_class`.`book_donor`

                    FROM `mssr_book_borrow_tmp`
                        INNER JOIN `mssr_book_class` ON
                        `mssr_book_borrow_tmp`.`book_sid`=`mssr_book_class`.`book_sid`
                    WHERE 1=1
                        AND `mssr_book_borrow_tmp`.`user_id`='{$_user_id}'

                        UNION

                    SELECT
                        'mssr_book_library' AS `book_type`,
                        `mssr_book_borrow_tmp`.`keyin_cdate`,
                        `mssr_book_borrow_tmp`.`book_sid`,

                        `mssr_book_library`.`book_isbn_10`,
                        `mssr_book_library`.`book_isbn_13`,
                        `mssr_book_library`.`book_library_code`,
                        `mssr_book_library`.`book_name`,
                        `mssr_book_library`.`book_no`,
                        '' AS `book_donor`

                    FROM `mssr_book_borrow_tmp`
                        INNER JOIN `mssr_book_library` ON
                        `mssr_book_borrow_tmp`.`book_sid`=`mssr_book_library`.`book_sid`
                    WHERE 1=1
                        AND `mssr_book_borrow_tmp`.`user_id`='{$_user_id}'
                ) AS `sqry`

                WHERE 1=1
                ORDER BY `sqry`.`keyin_cdate` DESC
            ";
            //echo $query_sql;

    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------
          

        $numrow=0;  //資料總筆數
        $psize =10; //單頁筆數,預設10筆
        $pnos  =0;  //分頁筆數
        $pinx  =1;  //目前分頁索引,預設1
        $sinx  =0;  //值域起始值
        $einx  =0;  //值域終止值

        if(isset($_GET['psize'])){
            $psize=(int)$_GET['psize'];
            if($psize===0){
                $psize=10;
            }
        }
        if(isset($_GET['pinx'])){
            $pinx=(int)$_GET['pinx'];
            if($pinx===0){
                $pinx=1;
            }
        }

        $numrow=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
        $numrow=count($numrow);

        $pnos  =ceil($numrow/$psize);
        $pinx  =($pinx>$pnos)?$pnos:$pinx;

        $sinx  =(($pinx-1)*$psize)+1;
        $einx  =(($pinx)*$psize);
        $einx  =($einx>$numrow)?$numrow:$einx;
        //echo $numrow."<br/>";

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------
      

        //網頁標題
        $title="明日星球,閱讀登記條碼版";

        // if($numrow!==0){
        //     $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx-1,$psize),$arry_conn_mssr);
        //     page_hrs($title);
            
        //     die();
        // }else{
        //     page_nrs($title);
        //    e
        //     die();
        // }

        // print_r($_SESSION);

         
?>
<!DOCTYPE HTML>
<Html>
<Head>
	<Title>閱讀登記</Title>
	 <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=true);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/effect/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../css/def.css" media="all" />


    <!-- 掛載 -->
    <script type="text/javascript" src="../../../../../lib/jquery/basic/code.js"></script>
    <script src="js/select_thing.js" type="text/javascript"></script>
    <link href="css/registration_btn.css" rel="stylesheet" type="text/css">
    <style>
		body{
            font-family: Microsoft JhengHei;
        }
		.cover_box{
			padding: 7px 14px;
			-webkit-border-radius: 14px;
			-moz-border-radius: 14px;
			border-radius: 14px;

			-webkit-box-shadow: #242 1px 1px 3px;
			-moz-box-shadow: #242 1px 1px 3px;
			box-shadow: #242 2px 1px 1px;

			border: 1px solid #111;
			font-size:24px;
			color:#333;
			font-weight: bold;

			background: rgb(242,246,248); /* Old browsers */
			background: -moz-linear-gradient(top,  rgba(242,246,248,1) 0%, rgba(181,198,208,1) 6%, rgba(181,198,208,1) 6%, rgba(216,225,231,1) 21%, rgba(224,239,249,1) 48%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(242,246,248,1)), color-stop(6%,rgba(181,198,208,1)), color-stop(6%,rgba(181,198,208,1)), color-stop(21%,rgba(216,225,231,1)), color-stop(48%,rgba(224,239,249,1))); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  rgba(242,246,248,1) 0%,rgba(181,198,208,1) 6%,rgba(181,198,208,1) 6%,rgba(216,225,231,1) 21%,rgba(224,239,249,1) 48%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  rgba(242,246,248,1) 0%,rgba(181,198,208,1) 6%,rgba(181,198,208,1) 6%,rgba(216,225,231,1) 21%,rgba(224,239,249,1) 48%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  rgba(242,246,248,1) 0%,rgba(181,198,208,1) 6%,rgba(181,198,208,1) 6%,rgba(216,225,231,1) 21%,rgba(224,239,249,1) 48%); /* IE10+ */
			background: linear-gradient(to bottom,  rgba(242,246,248,1) 0%,rgba(181,198,208,1) 6%,rgba(181,198,208,1) 6%,rgba(216,225,231,1) 21%,rgba(224,239,249,1) 48%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f2f6f8', endColorstr='#e0eff9',GradientType=0 ); /* IE6-9 */
		}

		.no_box{
			-webkit-border-radius: 8px;
			-moz-border-radius: 8px;
			border-radius: 8px;

			-webkit-box-shadow: #000 2px 2px 3px;
			-moz-box-shadow: #000 2px 2px 3px;
			box-shadow: #000 2px 2px 1px;

			border: 1px solid #111;
			font-size:24px;
			color:#333;
			font-weight: bold;

			background: rgb(242,246,248); /* Old browsers */
			background: -moz-linear-gradient(top,  rgba(242,246,248,1) 0%, rgba(216,225,231,1) 50%, rgba(181,198,208,1) 86%, rgba(224,239,249,1) 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(242,246,248,1)), color-stop(50%,rgba(216,225,231,1)), color-stop(86%,rgba(181,198,208,1)), color-stop(100%,rgba(224,239,249,1))); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  rgba(242,246,248,1) 0%,rgba(216,225,231,1) 50%,rgba(181,198,208,1) 86%,rgba(224,239,249,1) 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  rgba(242,246,248,1) 0%,rgba(216,225,231,1) 50%,rgba(181,198,208,1) 86%,rgba(224,239,249,1) 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  rgba(242,246,248,1) 0%,rgba(216,225,231,1) 50%,rgba(181,198,208,1) 86%,rgba(224,239,249,1) 100%); /* IE10+ */
			background: linear-gradient(to bottom,  rgba(242,246,248,1) 0%,rgba(216,225,231,1) 50%,rgba(181,198,208,1) 86%,rgba(224,239,249,1) 100%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f2f6f8', endColorstr='#e0eff9',GradientType=0 ); /* IE6-9 */


		}
		.ok_box{
			-webkit-border-radius: 8px;
			-moz-border-radius: 8px;
			border-radius: 8px;

			-webkit-box-shadow: #000 2px 2px 2px;
			-moz-box-shadow: #000 2px 2px 2px;
			box-shadow: #000 2px 2px 2px;

			border: 3px solid #FFF;
			font-size:26px;
			color:#FFF;
			font-weight: bold;

			background: rgb(125,185,232); /* Old browsers */
			background: -moz-linear-gradient(top,  rgba(125,185,232,1) 0%, rgba(41,137,216,1) 38%, rgba(41,137,216,1) 38%, rgba(32,124,202,1) 40%, rgba(32,124,202,1) 40%, rgba(30,87,153,1) 96%, rgba(30,87,153,1) 96%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(125,185,232,1)), color-stop(38%,rgba(41,137,216,1)), color-stop(38%,rgba(41,137,216,1)), color-stop(40%,rgba(32,124,202,1)), color-stop(40%,rgba(32,124,202,1)), color-stop(96%,rgba(30,87,153,1)), color-stop(96%,rgba(30,87,153,1))); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  rgba(125,185,232,1) 0%,rgba(41,137,216,1) 38%,rgba(41,137,216,1) 38%,rgba(32,124,202,1) 40%,rgba(32,124,202,1) 40%,rgba(30,87,153,1) 96%,rgba(30,87,153,1) 96%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  rgba(125,185,232,1) 0%,rgba(41,137,216,1) 38%,rgba(41,137,216,1) 38%,rgba(32,124,202,1) 40%,rgba(32,124,202,1) 40%,rgba(30,87,153,1) 96%,rgba(30,87,153,1) 96%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  rgba(125,185,232,1) 0%,rgba(41,137,216,1) 38%,rgba(41,137,216,1) 38%,rgba(32,124,202,1) 40%,rgba(32,124,202,1) 40%,rgba(30,87,153,1) 96%,rgba(30,87,153,1) 96%); /* IE10+ */
			background: linear-gradient(to bottom,  rgba(125,185,232,1) 0%,rgba(41,137,216,1) 38%,rgba(41,137,216,1) 38%,rgba(32,124,202,1) 40%,rgba(32,124,202,1) 40%,rgba(30,87,153,1) 96%,rgba(30,87,153,1) 96%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#7db9e8', endColorstr='#1e5799',GradientType=0 ); /* IE6-9 */


		}
		#check_vote_page_btn a.vote_1:hover {
			background: url('img/vote_page_btn.png') 0 -120px;
		}
		#check_vote_page_btn a.vote_1{
			position:absolute;
			width:629px;
			height:120px;
			background: url('img/vote_page_btn.png') 0 0;
		}
		#check_vote_page_btn a.vote_2{
			position:absolute;
			width:629px;
			height:120px;
			background: url('img/vote_page_btn.png') -629px 0;
		}
		#check_vote_page_btn a.vote_3{
			position:absolute;
			width:629px;
			height:120px;
			background: url('img/vote_page_btn.png') -1258px 0;
		}
		#check_vote_page_btn a.vote_4{
			position:absolute;
			width:629px;
			height:120px;
			background: url('img/vote_page_btn.png') -629px -120px;
		}
		#check_vote_page_btn a.vote_5{
			position:absolute;
			width:629px;
			height:120px;
			background: url('img/vote_page_btn.png') -1258px -120px;
		}
		.btn_help_page2:hover {
    		background: url('img/btn_help2.png') -0px -49px;
		}
		.btn_help_page2:active {
    		background: url('img/btn_help2.png') -0px -98px;
		}
		.btn_help_page2{
			position:absolute;
			width:148px;
			height:49px;
			background: url('img/btn_help2.png') -0px 0;
		}
		.btn_help_page2_n{
			position:absolute;
			width:148px;
			height:49px;
			background: url('img/btn_help2.png') -0px -147px;
		}
		.btn_help_page:hover {
    		background: url('img/btn_help.png') -0px -49px;
		}
		.btn_help_page:active {
    		background: url('img/btn_help.png') -0px -98px;
		}
		.btn_help_page{
			position:absolute;
			width:148px;
			height:49px;
			background: url('img/btn_help.png') -0px 0;
		}
		.btn_help_page_n{
			position:absolute;
			width:148px;
			height:49px;
			background: url('img/btn_help.png') -0px -147px;
		}

		.box_ling2{
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			border-radius: 3px;

			-webkit-box-shadow: #242 1px 1px 3px;
			-moz-box-shadow: #242 1px 1px 3px;
			box-shadow: #242 2px 1px 1px;

			background: rgb(180,227,145); /* Old browsers */
			background: -moz-linear-gradient(top,  rgba(180,227,145,1) 9%, rgba(97,196,25,1) 15%, rgba(180,227,145,1) 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(9%,rgba(180,227,145,1)), color-stop(15%,rgba(97,196,25,1)), color-stop(100%,rgba(180,227,145,1))); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  rgba(180,227,145,1) 9%,rgba(97,196,25,1) 15%,rgba(180,227,145,1) 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  rgba(180,227,145,1) 9%,rgba(97,196,25,1) 15%,rgba(180,227,145,1) 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  rgba(180,227,145,1) 9%,rgba(97,196,25,1) 15%,rgba(180,227,145,1) 100%); /* IE10+ */
			background: linear-gradient(to bottom,  rgba(180,227,145,1) 9%,rgba(97,196,25,1) 15%,rgba(180,227,145,1) 100%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b4e391', endColorstr='#b4e391',GradientType=0 ); /* IE6-9 */

			border: 1px solid #020;
		}
       .box_ling{
			-webkit-border-radius: 8px;
			-moz-border-radius: 8px;
			border-radius: 8px;

			-webkit-box-shadow: #242 1px 1px 1px;
			-moz-box-shadow: #242 1px 1px 1px;
			box-shadow: #242 1px 1px 1px;
			background-color:#aaffaa;
			border: 2px solid #3a8c3a;
			-webkit-transition-duration: 1s; /* Safari */
   			transition-duration: 1s;
		}
		.text
		{
			font-size: 16px;
			font-weight: bold;
			margin: 2px;
			
			color:#333;
		}
		.btn_off
		{
			-webkit-border-radius: 16px;
			-moz-border-radius: 16px;
			border-radius: 16px;

			-webkit-box-shadow: #242 1px 1px 1px;
			-moz-box-shadow: #242 1px 1px 1px;
			box-shadow: #242 1px 1px 1px;
			background-color:#fff;
			border: 2px solid #3a8c3a;
		}
		.btn_on
		{
			-webkit-border-radius: 16px;
			-moz-border-radius: 16px;
			border-radius: 16px;

			-webkit-box-shadow: #242 1px 1px 1px;
			-moz-box-shadow: #242 1px 1px 1px;
			box-shadow: #242 1px 1px 1px;

			background: rgb(114,170,0); /* Old browsers */
			background: -moz-radial-gradient(center, ellipse cover,  rgba(114,170,0,1) 0%, rgba(114,170,0,1) 25%, rgba(158,203,45,1) 28%, rgba(158,203,45,1) 33%, rgba(158,203,45,1) 33%, rgba(255,255,255,1) 35%); /* FF3.6+ */
			background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,rgba(114,170,0,1)), color-stop(25%,rgba(114,170,0,1)), color-stop(28%,rgba(158,203,45,1)), color-stop(33%,rgba(158,203,45,1)), color-stop(33%,rgba(158,203,45,1)), color-stop(35%,rgba(255,255,255,1))); /* Chrome,Safari4+ */
			background: -webkit-radial-gradient(center, ellipse cover,  rgba(114,170,0,1) 0%,rgba(114,170,0,1) 25%,rgba(158,203,45,1) 28%,rgba(158,203,45,1) 33%,rgba(158,203,45,1) 33%,rgba(255,255,255,1) 35%); /* Chrome10+,Safari5.1+ */
			background: -o-radial-gradient(center, ellipse cover,  rgba(114,170,0,1) 0%,rgba(114,170,0,1) 25%,rgba(158,203,45,1) 28%,rgba(158,203,45,1) 33%,rgba(158,203,45,1) 33%,rgba(255,255,255,1) 35%); /* Opera 12+ */
			background: -ms-radial-gradient(center, ellipse cover,  rgba(114,170,0,1) 0%,rgba(114,170,0,1) 25%,rgba(158,203,45,1) 28%,rgba(158,203,45,1) 33%,rgba(158,203,45,1) 33%,rgba(255,255,255,1) 35%); /* IE10+ */
			background: radial-gradient(ellipse at center,  rgba(114,170,0,1) 0%,rgba(114,170,0,1) 25%,rgba(158,203,45,1) 28%,rgba(158,203,45,1) 33%,rgba(158,203,45,1) 33%,rgba(255,255,255,1) 35%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#72aa00', endColorstr='#ffffff',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */


			border: 2px solid #3a8c3a;
		}

	</style>
     <link rel="stylesheet" href="css/btn.css">
</Head>
<body>
	<!--==================================================
    遮罩內容
    ====================================================== -->
	<div id="cover" style="position:absolute; top:-8px; left:-8px; display:none;">
    	<div onClick="" style="position:relative; top:0px; left:0px; height:500px; width:950px; cursor:wait; background-color:#000; opacity:0.7; z-index:9999"></div>
        <table width="550"   border="0" cellspacing="0"  style="position:absolute; top:50%; left:50%; transform: translateX(-50%)translateY(-50%);   text-align: center; z-index:10000;">
        	<tr height="90">
            	<td width="500"  valign="center" id="cover_text" style="text-align: center;" class="cover_box" >正在讀取中請稍後...

                </td>
            </tr>
            <tr height="40">
            	<td>
                        <div id="cover_btn_0" onClick="close_cover(2)" style="position:absolute; left:1px; width:110px; height:38px; text-align: center; z-index:10003; display:none; cursor:pointer;" class="ok_box">存檔</div>
                        <div id="cover_btn_1" onClick="close_cover(1)" style="position:absolute; left:141px; width:110px; height:38px; text-align: center; z-index:10001; display:none; cursor:pointer;" class="ok_box">確定</div>
                        <div id="cover_btn_2" onClick="close_cover(0)" style="position:absolute; left:286px; width:110px; height:38px; text-align: center; z-index:10002; display:none; cursor:pointer;" class="no_box">取消</div>
                        

                 </td>
            </tr>
        </table>

	</div>


	<!--==================================================
    html內容
    ====================================================== -->

    <div style="position:absolute; top:10px; left:0px; width:880px; height:500px; background-color:#56ffd6; z-index:;"></div>
    <!--書籍資料顯示-->
    <!--標題開頭-->
    <p id="tittle"  style="position:absolute; top:0px; left:31%;font-size: 36px; font-weight: 600; padding: 2px;" class="box_ling">
    &nbsp;&nbsp;&nbsp;&nbsp;回答閱讀書籍相關問題&nbsp;&nbsp;&nbsp;&nbsp;</p>
    <div style="position:absolute; width:200px; height:400px; top:25px; left:17px;" class="box_ling"> 
            <img id="book_pic" src="./0.png" width="133" height="166" style="position:relative; top:10px; left:33px; display:none;">
            <div id="text_name" style="position:relative; height:30px; width:160px; top:15px; left:10px;" class="text">書名:</div>
            <div id="text_author" style="position:relative; height:30px; width:160px; top:45px; left:10px;" class="text">作者:</div>
            <div id="text_publisher" style="position:relative; height:30px; width:160px; top:75px; left:10px;" class="text">出版社:</div>
            <div id="text_donor" style="position:relative; height:30px; width:160px; top:100px; left:10px;" class="text">捐贈者:</div>
    </div>
	
    <img id="book_pic" src="./0.png" width="133" height="166" style="position:absolute; top:122px; left:33px; display:none;">
    
    

    <div id="out_b" style="position:absolute; top:15px; right:20px; width:100px; height:46px; background-color:#F8C967"></div>
    <!-- 改過 -->
    <a id="out" class="btn_close" onClick="go_out()" style="position:absolute; top:12px; left:800px; cursor:pointer; display:none"></a>

    <!--回答頁框-->
      <!-- 改過 -->
	<div id="quset_box" style="position:absolute; top:92px; left:230px;   width:620px; " class="box_ling">


    	<!-- 新版特別框 -->
        <div>
        	<div id="show1">
                
                    <div id="quest" style="  margin-left:20px;margin-right:20px;font-size: 36px; font-weight: 600;top:15px;position:relative;">
                    </div>
                
               
                    <div id="answer" style=" margin-left:40px; margin-right:40px; margin-top:10px; margin-bottom:10px;font-size: 32px; font-weight: 600;top:15px;position:relative; ">


                    </div>
              
            </div>
            <div id="show2" style="display:none;">
            	
                    <div id="reanswer" style=" font-size: 28px;font-weight: 600;top:15px;margin-left:20px;position:relative;line-height:1.2; padding: 10px;">


                    </div>
                
            </div>
            <div style="position:relative; top:0px; left:0px; height:80px;">
            	<a id="btn_up" onClick="page_v2(-1)" class="btn_6" style="position:absolute; left:30px;" > </a>
            	<a id="btn_down" onClick="page_v2(1)" class="btn_5"  style="position:absolute; right:30px;"></a>
        	</div>
        </div>
		<!--
        <img id="quset_img" src="./img/opin_text_1.png" style="position:absolute; top:3px; left:22px;">
      <div id="btn_list" style="position:absolute; top:63px; left:36px;"></div>
        <a id="yes_btn"  class="btn_5" onClick="go_next_quest()" style="position:absolute; top:137px; left:452px; cursor:pointer; "></a>
        <a id="up_btn"  class="btn_6" onClick="go_from_quest()" style="position:absolute; top:137px; left:51px; cursor:pointer; display:none"></a>
  	--><!--特殊輸入框-->
    	<!--<div id="sp" style="position:absolute; top:43px; left:29px; display:none;">
            <img id="sp1" src="img/11.png" onClick="sp_btn(1)" style="position:absolute; top:10px; left:-3px; opacity:0.4;">
            <img id="sp2" src="img/22.png" onClick="sp_btn(2)" style="position:absolute; top:10px; left:120px; opacity:0.4;">
            <img id="sp3" src="img/33.png" onClick="sp_btn(3)" style="position:absolute; top:10px; left:240px; opacity:0.4;">
            <img id="sp4" src="img/44.png" onClick="sp_btn(4)" style="position:absolute; top:10px; left:360px; opacity:0.4;">
            <img id="sp5" src="img/55.png" onClick="sp_btn(5)" style="position:absolute; top:10px; left:480px; opacity:0.4;">

      </div>-->
	</div>
	<!--確認頁框--><a id="btn_help_page_n" class="btn_help_page_n"  style="position:absolute; top:429px; left:18px; display:none;"></a>
    <!-- <a id="btn_help_page" class="btn_help_page" onClick="open_vate_page()" style="position:absolute; top:429px; left:18px; cursor:pointer;display:none;"></a> -->
    <!--a id="btn_help_page2_n" class="btn_help_page2_n" onClick="open_books_ver_page_page()" style="position:absolute; top:429px; left:168px;display:none;"></a>
    <!--a id="btn_help_page2" class="btn_help_page2" onClick="open_books_ver_page_page()" style="position:absolute; top:429px; left:168px; cursor:pointer;display:none;"></a>
    <!--================================================第六頁特殊輸入頁數功能=============================================================-->
    <!--頁數填寫數值班-->


    <!--填寫頁數的按鈕-->
<div id="check_vote_page_btn" style="position:absolute; top:150px; left:310px; display:none; cursor:pointer;" onClick="open_vate_page()">
        <a id="check_vote_page_btn_a" class="vote_1"></a>
        <div id="my_vote_page" style="position:absolute; top:14px; left:359px; font-size:32px;"></div>
    </div>
<div id="check_vote_page_btn_list"  style="position:absolute;  top:150px; left:310px; display:none;">
    	<a onClick="tk(1)" class="btn_4" style=" position:absolute; top:130px; left:80px; cursor:pointer"></a>
        <a onClick="tk(0)"  class="btn_3" style=" position:absolute; top:130px; left:380px; cursor:pointer"></a>
    </div>


	<!-- 填寫頁數的頁面 -->
	<div id="vote_bookpage_page" style="position:absolute; top:0px; left:0px; display:none;">
        <div onClick="" style="position:absolute; top:0px; left:0px; height:500px; width:880px; cursor:wait; background-color:#000; opacity:0.7;"></div>
         <!-- 改過 -->
    	<img src="img/vote_page_bar.png" style="position:absolute; top:79px; left:260px;">

        <!-- 文字藍 -->
         <!-- 改過 -->
        <input id="borrow_page_input_text" type="text" value="" style="position:absolute; top:137px; left:415px; font-size:32px; resize: none; width: 136px;"/>

        <!-- 輸入小鍵盤 -->
        <div style="position:absolute; top:214px; left:363px;">
        	<div style="position:absolute; top:-13px; left:-27px; width:303px; height:183px; background-color:#aaffaa;" class="box_ling"></div>
            <!-- 數字按鈕 -->
            <?PHP for($i = 0 ; $i <= 9 ; $i++){?>
      		<div onClick="input_number(<?PHP echo $i;?>)" style="position:absolute; top:<?PHP echo ((int)($i/5))*48;?>px; left:<?PHP echo ((int)($i%5))*52;?>px; width:40px; height:39px; background-color:#00ca00; font-size:36px; text-align: center; cursor: pointer;" class="box_ling2"><?PHP echo $i;?></div>
            <?PHP } ?>

            <!-- 確認取消按鈕 -->
            <div onClick="set_vote()" style="position:absolute; top:127px; left:-14px; width:110px; height:38px; text-align: center; cursor:pointer;" class="ok_box">確定</div>
            <div onClick="out()" style="position:absolute; top:130px; left:155px; width:110px; height:38px; text-align: center; cursor:pointer;" class="no_box">取消</div>
   	  	</div>
	</div>

    <!-- 協助書籍分類 -->
	<div id="books_ver_page" style="position:absolute; top:0px; left:0px; display:none;">
        <div onClick="" style="position:absolute; top:0px; left:0px; height:480px; width:1000px; cursor:wait; background-color:#000; opacity:0.7;"></div>

        <div id="books_ver_check_box" style="position:absolute; top:89px; left:193px; height:234px; width:671px;" class="box_ling">
  		<!-- 內容 -->
        <!-- 內容 -->
        </div>

            <!-- 確認取消按鈕 -->
  		<div onClick="set_books_ver_check_box()" style="position:absolute; top:268px; left:411px; width:110px; height:38px; text-align: center; cursor:pointer;" class="ok_box">確定</div>
        <div onClick="outt()" style="position:absolute; top:271px; left:580px; width:110px; height:38px; text-align: center; cursor:pointer;" class="no_box">取消</div>
	</div>

<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------

	console.log("1");
	var quset_answer_data = new Array();
	var answer_list = new Array(0,0,0,0,0,0,0);
	var quest_on = -1;
	var ver_on_list = new Array();
	// var book_sid = window.parent.book_info[window.parent.book_choose]["book_sid"];
	// console.log(book_sid);

	var school_code='<?php echo $school_code?>';
	var user_id='<?php echo $_user_id;?>';
	var t_permission='<?php echo $t_permission?>';


	var cover_level = 0;

	
	

	var about_book_sid=window.parent.about_book_sid;    
    var about_book_name=window.parent.about_book_name;
    var about_book_author=window.parent.about_book_author;
    var about_book_page_count=window.parent.about_book_page_count;
    var about_book_publisher=window.parent.about_book_publisher;
    var book_borrow_sid=window.parent.book_borrow_sid;
    var about_book_src=window.parent.about_book_src;

	

	//------新版的存取
	questpage = 0;
	book_max_page = 0;
	quest_list = new Array();
	re_answer = new Array();

	//--------------
	//跳窗
	//----------------

	function cover(text,type,proc,proc2)
	{
		if(type == 3 && cover_level <= 3)
		{

			window.document.getElementById("cover_btn_1").style.left = "223px";
			window.document.getElementById("cover_btn_1").style.display = "block";
			window.document.getElementById("cover_btn_1").className = "no_box";
			window.document.getElementById("cover_btn_1").innerHTML = "不存檔";
			window.document.getElementById("cover_btn_2").style.left = "430px";
			window.document.getElementById("cover_btn_2").style.display = "block";
			window.document.getElementById("cover_btn_2").className = "no_box";
			window.document.getElementById("cover_btn_0").style.left = "0px";
			window.document.getElementById("cover_btn_0").style.display = "block";
			window.document.getElementById("cover_btn_0").className = "ok_box";
			window.document.getElementById("cover_btn_0").innerHTML = "存檔";
			cover_level = 3;
			window.document.getElementById("cover_text").innerHTML=text;
			window.document.getElementById("cover").style.display = "block";


		}
		else if(type == 2 && cover_level <= 2)
		{
			window.document.getElementById("cover_btn_1").style.left = "100px";
			window.document.getElementById("cover_btn_1").style.display = "block";
			window.document.getElementById("cover_btn_1").className = "ok_box";
			window.document.getElementById("cover_btn_1").innerHTML = "確定";
			window.document.getElementById("cover_btn_2").style.left = "320px";
			window.document.getElementById("cover_btn_2").style.display = "block";
			window.document.getElementById("cover_btn_2").className = "no_box";
			window.document.getElementById("cover_btn_0").style.left = "536px";
			window.document.getElementById("cover_btn_0").style.display = "none";
			window.document.getElementById("cover_btn_0").className = "no_box";
			cover_level = 2;
			window.document.getElementById("cover_text").innerHTML=text;
			window.document.getElementById("cover").style.display = "block";
		}
		else if(type==1 && cover_level <= 1 )
		{
			window.document.getElementById("cover_btn_1").style.left = "210px";
			window.document.getElementById("cover_btn_1").style.display = "block";
			window.document.getElementById("cover_btn_1").className = "ok_box";
			window.document.getElementById("cover_btn_1").innerHTML = "確定";
			window.document.getElementById("cover_btn_2").style.left = "536px";
			window.document.getElementById("cover_btn_2").style.display = "none";
			window.document.getElementById("cover_btn_2").className = "no_box";
			window.document.getElementById("cover_btn_0").style.left = "536px";
			window.document.getElementById("cover_btn_0").style.display = "none";
			window.document.getElementById("cover_btn_0").className = "no_box";
			cover_level = 1;
			window.document.getElementById("cover_text").innerHTML=text;
			window.document.getElementById("cover").style.display = "block";
		}
		else if( cover_level <= 0)
		{
			window.document.getElementById("cover_btn_1").style.display = "none";
			window.document.getElementById("cover_btn_2").style.display = "none";
			window.document.getElementById("cover_btn_0").style.display = "none";
			cover_level = 0;

			if(text!=""&&text!=null)
			{
				window.document.getElementById("cover_text").innerHTML=text;
				window.document.getElementById("cover").style.display = "block";
			}
			else
			{
				window.document.getElementById("cover").style.display = "none";
			}
		}


		if(type == 2 && proc != null)
		{
			delayExecute(proc,null);
		}
		else if(type == 3 && proc2 != null)
		{
			delayExecute(proc,proc2);
		}
	}

		//關閉跳窗
	function close_cover(value)
	{
		if(value == 0)cover_click = 0;
		if(value == 1)cover_click = 1;
		if(value == 2)cover_click = 2;
		//echo("cover_level"+cover_level);
		if(cover_level<2)
		{
			window.document.getElementById("cover").style.display = "none";
			cover_click = -1;
			cover_level = 0;
		}

	}

	//---------------------------------------------------
	//FUNCTION  2v
	//---------------------------------------------------
	function page_v2(value)
	{
		if(value == 1 )
		{
			//check it the last
			if(quest_list.length == questpage)
			{
				opinion_registration();
				return false ;
			}

			//判斷填寫狀況
			if(quest_list[questpage]["type"] == "radio")
			{


				var check = false;

				var answer = document.getElementsByName("answer");
				for (var i = 0 ; i < answer.length;  i++)
				{
					if (answer[i].checked)
					{
						check = true ;
						re_answer[questpage] = i+1;
					}
				}
			}
			else if(quest_list[questpage]["type"] == "number")
			{
				if(quest_list[questpage]["topic_id"] ==6)
				{//6 填寫頁數的特別判定




					if(Math.floor(document.getElementById("answer1").value) !=0 && !isNaN(Math.floor(document.getElementById("answer1").value)))
					{
						tmp = Math.floor(document.getElementById("answer1").value);
						if(tmp < 1)//填寫小於1
						{
							cover("沒有讀半頁?",1) ;
							return false ;
						}
						else if(tmp > 3560)//填寫過大  大於3560
						{
							cover("你填的頁數太大了",1) ;
							return false ;
						}else if(book_max_page > 0 && tmp > book_max_page)//填寫超過MAX
						{
							cover("填寫的頁數已經超過此書總頁數囉",1) ;
							return false ;
						}else
						{
							check = true ;
							re_answer[questpage] = Math.floor(document.getElementById("answer1").value);
						}
					}




				}
				else
				{
					if(Math.floor(document.getElementById("answer1").value) !=0 && !isNaN(Math.floor(document.getElementById("answer1").value)))
					{
						check = true ;
						re_answer[questpage] = Math.floor(document.getElementById("answer1").value);
					}
				}

				console.log('re_answer[questpage]1:',re_answer[questpage]);
				if(school_code==='idc'){
					check = true ;

					var ans_1=document.getElementById("answer1").value;
					if(ans_1!=''){
						re_answer[questpage]=Math.floor(ans_1);
					}else{
						re_answer[questpage] = ans_1;
					}
					

					console.log('re_answer[questpage]:',re_answer[questpage]);

				}



			}
			else if(quest_list[questpage]["type"] == "text")
			{

			}

			//無填寫擋下
			if(!check)
			{  
				cover("尚未填寫內容! <BR>請填寫完這題再前往下一步",1) ;
				return false ;
				
				
			}
		}

		//頁數計算
		questpage += value;
		//上一頁的控制
		if(questpage == 0 ) window.document.getElementById("btn_up").style.display = "none";
		else window.document.getElementById("btn_up").style.display = "block";



		console.log("quest_list.length"+quest_list.length);
		console.log("questpage"+questpage);

		console.log("4");
		

		if(quest_list.length == questpage )
		{//完成全部作答時
			window.document.getElementById("btn_down").className = "btn_9";
			window.document.getElementById("show1").style.display = 'none';
			window.document.getElementById("show2").style.display = 'block';
			fin_qa_v2();
		}else if(school_code==='idc'&&questpage=='5'){

			window.document.getElementById("show1").style.display = 'block';
			window.document.getElementById("show2").style.display = 'none';
			window.document.getElementById("btn_down").className = "btn_3";
			set_quest_v2();


		}else{//還在回答題目

			window.document.getElementById("show1").style.display = 'block';
			window.document.getElementById("show2").style.display = 'none';
			window.document.getElementById("btn_down").className = "btn_5";
			set_quest_v2();
		}

	}

	console.log("5");
	function set_quest_v2()
	{
		//設定問題
		window.document.getElementById("quest").innerHTML = (questpage+1)+"."+quest_list[questpage]["quest"]+"<BR>";
		console.log(quest_list[questpage]["quest"]);
		//設定回答
		window.document.getElementById("tittle").innerHTML= "&nbsp;&nbsp;&nbsp;&nbsp;回答閱讀書籍相關問題&nbsp;&nbsp;&nbsp;&nbsp;";
		//判斷題目類型
		window.document.getElementById("answer").innerHTML = "";
		if(quest_list[questpage]["type"] == "radio")
		{
			for(var i  = 1 ; i < quest_list[questpage]["answer"].length ; i++)
			{
				if(re_answer[questpage] != i)window.document.getElementById("answer").innerHTML += '<div style="padding:5px;"><input  type="radio" style="width:24px;height:24px;position:relative; top:4px;" name="answer" id="answer'+i+'" >'+'<label for="answer'+i+'">'+quest_list[questpage]["answer"][i]+"<label><BR>";
				else window.document.getElementById("answer").innerHTML += '<div style="padding:5px;"><input  type="radio" style="width:24px;height:24px; position:relative; top:4px;" name="answer" id="answer'+i+'" checked>'+'<label for="answer'+i+'">'+quest_list[questpage]["answer"][i]+'<label><div><BR>';
			}
		}else if(quest_list[questpage]["type"] == "number")
		{
			if(quest_list[questpage]["topic_id"] == 6)//特殊判定 topic_id = 6 ; 會讀取目前最大頁數
			{
				if(book_max_page>0)window.document.getElementById("answer").innerHTML +=  "<div style='padding:5px;'><input type='text' id='answer1' value='"+re_answer[questpage]+"' style='font-size:20px;'> / "+book_max_page+"頁數</div>";
				else window.document.getElementById("answer").innerHTML +=  "<div style='padding:5px;'><input type='text' id='answer1' value='"+re_answer[questpage]+"' style='font-size:20px;'> / ??頁數</div>";
			}else
			{
					window.document.getElementById("answer").innerHTML +=  "<input type='text' id='answer1' value='"+re_answer[questpage]+"' style='font-size:20px;'>";
			}
		}else if(quest_list[questpage]["type"] == "text")
		{

		}
	}

	//送出前顯示
	function fin_qa_v2()
	{
		window.document.getElementById("quest").innerHTML = "";
		window.document.getElementById("answer").innerHTML= "";

		window.document.getElementById("tittle").innerHTML= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;請確認填寫內容&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		window.document.getElementById("reanswer").innerHTML= "";
		for(var i  = 0 ; i < re_answer.length ; i++)
		{
			//window.document.getElementById("answer").innerHTML += "題目"+i+". ";
			if(quest_list[i]['type'] ==  "radio")
			{
				window.document.getElementById("reanswer").innerHTML += ""+quest_list[i]['quest']+"："+quest_list[i]['answer'][re_answer[i]]+"<BR>";
			}
			else if(quest_list[i]['type'] ==  "number")
			{
				window.document.getElementById("reanswer").innerHTML += ""+quest_list[i]['quest']+"："+re_answer[i]+"<BR>";
			}
			else if(quest_list[i]['type'] ==  "text")
			{

			}
		}
	}
	//送出答案
	/*function go_fin_v2()
	{
		window.document.getElementById("quest").innerHTML = "送出中";
		window.document.getElementById("answer").innerHTML= "";
	}*/
	//出指畫

	console.log("7");
	function main_v2()
	{
		for(var i  = 0 ; i < quest_list.length ; i++)
		{
			re_answer[i]='';

		}

		page_v2(0);
	}
	//出指畫
	//main_v2()
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	function open_books_ver_page_page()
	{
		window.document.getElementById("books_ver_page").style.display = "block";
	}
	function outt()
	{
		window.document.getElementById("books_ver_page").style.display = "none";
		get_books_ver();
	}

	//debug
	// function echo(text)
	// {
	// 	window.parent.echo(text);
	// }

	//初始化  回答問題
	function set_new_quset()
	{console.log("72222");
		answer_list = new Array(0,0,0,0,0,0,0);
		quest_on = -1;//現在的題目
		//echo("set_new_quset:開始");
		cover("");
		set_quset(1);
	}
	//特殊按鈕
	function sp_btn(value)
	{
		echo("點選答案=>"+value);
		answer_list[quest_on] = value;
		for(var i = 1 ; i <= 5 ; i++ )
		{
			window.document.getElementById("sp"+i).style.opacity = 0.4;
		}

		if(value != 0)
		{
			window.document.getElementById("sp"+value).style.opacity = 1;
		}
		window.document.getElementById("yes_btn").style.display = "block";

		window.document.getElementById("answer_list_"+quest_on).src = "./img/opin_text_"+quest_on+"_"+value+".png";

	}
	//按下選擇按鈕
	function chick_ans_btn(value)
	{
		//echo("點選答案=>"+value);
		answer_list[quest_on] = value;
		for(var i = 1 ; i <= quset_answer_data['answer_count'][quest_on]; i ++)
		{
			window.document.getElementById("btn_list_"+i).className = "btn_off";
		}
		window.document.getElementById("btn_list_"+value).className = "btn_on";
		window.document.getElementById("answer_list_"+quest_on).src = "./img/opin_text_"+quest_on+"_"+value+".png";
		window.document.getElementById("yes_btn").style.display = "block";
	}
	//按下上一題按鈕
	function go_from_quest()
	{
		quest_on--;

		set_quset(quest_on);
	}
	//按下下一題按鈕
	function go_next_quest()
	{

		if(quest_on == 6)
		{//第六題額外處裡
			if(window.document.getElementById("book_max_page").value != "- - ")
			{
				if(Math.floor(window.document.getElementById("read_page").value) > Math.floor(window.document.getElementById("book_max_page").value))
				{
				cover("填入的頁數錯誤了",1);
				return false;
				}
			}

			if(Math.floor(window.document.getElementById("read_page").value) <= 4000 && Math.floor(window.document.getElementById("read_page").value) >= 1 )
			{
				answer_list[6] = window.document.getElementById("read_page").value;
				window.document.getElementById("page_input").style.display = "none";
				window.document.getElementById("check_vote_page_btn").style.display = "none";
				window.document.getElementById("chick_book_page").value = window.document.getElementById("read_page").value;
			}else
			{
				cover("填入的頁數錯誤了喔!!",1);
				return false;
			}
		}

		quest_on++;


		/*if(quest_on == 7 )
		{
			window.document.getElementById("yes_btn").style.display = "none";
			window.document.getElementById("up_btn").style.display = "none";
			window.document.getElementById("quset_img").src = "./img/opin_text_7.png";
			sp_7_set_quset();

			return false;
		}*/


		if(quset_answer_data["quest_count"] < quest_on)
		{
			set_finish_page();
			return false;
		}else
		{
			set_quset(quest_on);
		}
	}

	// console.log("9");

	//設定完成頁面
	function set_finish_page()
	{
		//顯示初始化
		window.document.getElementById("yes_btn").style.display = "none";
		window.document.getElementById("up_btn").style.display = "none";
		window.document.getElementById("btn_list").style.display = "none";
		//移動顯示框
		window.document.getElementById("quset_box").style.top = "-400px";
		window.document.getElementById("check_box").style.top = "92px";
	}

	//開啟投票頁
	function open_vate_page()
	{
		//if(window.document.getElementById("check_vote_page_btn_a").className == "vote_1")
		window.document.getElementById("vote_bookpage_page").style.display = "block";
		window.document.getElementById("borrow_page_input_text").focus();
	}
	//投票箱的按鈕數字
	function input_number(value)
	{
		window.document.getElementById("borrow_page_input_text").value = window.document.getElementById("borrow_page_input_text").value+value;
		window.document.getElementById("borrow_page_input_text").focus();
	}
	//離開投票頁
	function out()
	{
		window.document.getElementById("vote_bookpage_page").style.display = "none";

	}
	//投票送出
	function set_vote()
	{//驗證
		var nuber = Math.floor(window.document.getElementById("borrow_page_input_text").value);

		if(nuber <= 0 || nuber >= 4000)
		{
			cover("亂輸入是不行的喔!!",1);
			return false;
		}

		cover("儲存中");
		var url = "./ajax/set_user_vote_book_page.php";

		$.post(url, {
					user_id:user_id,
					user_permission:t_permission,
					book_sid:about_book_sid,
					vote_page:nuber

			}).success(function (data)
			{

				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員",1);
					//echo("AJAX:success:set_vote():資料庫發生問題");
					return false;
				}

				data_array = JSON.parse(data);
				//echo("AJAX:success:set_vote():已讀出:"+data);
				if(data_array["error"]!="")
				{
					//cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					cover(data_array["echo"],1);
					//set_vote();

				}else
				{
					if(data_array["bad_count"]>=33)
					{//做壞指數大於3

						window.document.getElementById("btn_help_page").style.display = "none";

					}else if(data_array["has_done"]>=1)
					{//有做過
						if(data_array["vote_state"]=="差")
						{
							window.document.getElementById("btn_help_page").style.display = "none";

						}else if(data_array["vote_state"]=="良")
						{
							window.document.getElementById("btn_help_page").style.display = "none";
						}else if(data_array["vote_state"]=="未確認")
						{
							window.document.getElementById("btn_help_page").style.display = "none";
						}
					}
					else
					{
						window.document.getElementById("btn_help_page").style.display = "block";
					}
					window.document.getElementById("btn_help_page_n").style.display = "block";
					//echo("AJAX:success:set_vote():完成:存入書籍頁數");
					window.document.getElementById("vote_bookpage_page").style.display = "none";
					cover('');

				}

			}).error(function(e){
				//echo("AJAX:error:set_vote():");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",1);
				//get_opinion_qa();
			}).complete(function(e){
				//echo("AJAX:complete:set_vote():");
			});


	}
	//
	function tk(value)
	{
		if(value == 0)
		{
			window.document.getElementById("check_vote_page_btn_list").style.display = "none";
			window.document.getElementById("check_vote_page_btn").style.display = "none";
			set_finish_page();
		}
		else if(value == 1)
		{
			open_vate_page();
		}

	}


	//re_read_page() 清空填寫數字
	function set_read_page(value)
	{
		window.document.getElementById("read_page").value = window.document.getElementById("read_page").value+value;
		window.document.getElementById("read_page").focus();
	}
	function re_read_page()
	{
		window.document.getElementById("read_page").value = "";
		window.document.getElementById("read_page").focus();
	}
	//開啟回答頁數的功能
	function sp_7_set_quset()
	{
		//echo("sp_7_set_quset:讀取頁數填寫資訊+++"+window.parent.book_info[window.parent.book_choose]["book_sid"]);
		cover("讀取中")
		var url = "./ajax/get_user_vote_book_page.php";
		$.post(url, {
					user_id:user_id,
					user_permission:t_permission,
					book_sid:about_book_sid

			}).success(function (data)
			{

				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員",1);
					//echo("AJAX:success:sp_7_set_quset():資料庫發生問題");
					return false;
				}

				data_array = JSON.parse(data);
				//echo("AJAX:success:sp_7_set_quset():已讀出:"+data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					window.alert(data_array["echo"]);
					return false;
					//main();

				}else
				{
					if(data_array["bad_count"]>=99)
					{//做壞指數大於3

						window.document.getElementById("btn_help_page").style.display = "none";

					}else if(data_array["has_done"]>=1)
					{//有做過
						if(data_array["vote_state"]=="差")
						{
							window.document.getElementById("btn_help_page").style.display = "none";

						}else if(data_array["vote_state"]=="良")
						{
							window.document.getElementById("btn_help_page").style.display = "none";

						}else if(data_array["vote_state"]=="未確認")
						{
							window.document.getElementById("btn_help_page").style.display = "none";

						}
					}
					else
					{//沒做過
						window.document.getElementById("btn_help_page").style.display = "block";
					}
					window.document.getElementById("btn_help_page_n").style.display = "block";


					cover("");
				}

			}).error(function(e){
				//echo("AJAX:error:sp_7_set_quset():");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",1);
				//get_opinion_qa();
			}).complete(function(e){
				//echo("AJAX:complete:sp_7_set_quset():");
			});

	}

	//設定題目

	

	function set_quset(value)
	{
		


	console.log("10");//echo("set_quset:開始"+quset_answer_data['answer_count'][value]);
		quest_on = value;

		//顯示初始化
		window.document.getElementById("page_input").style.display = "none";
		window.document.getElementById("yes_btn").style.display = "none";
		window.document.getElementById("btn_list").style.display = "block";
		window.document.getElementById("quset_box").style.top = "92px";
		window.document.getElementById("check_box").style.top = "490px";
		if(quest_on == 1 ) window.document.getElementById("up_btn").style.display = "none";
		else  window.document.getElementById("up_btn").style.display = "block";

		//特殊例外
		if(value == 6)
		{	//echo("特殊題目6");
			//sp_7_set_quset();
			window.document.getElementById("page_input").style.display = "block";
			window.document.getElementById("sp").style.display = "none";
			window.document.getElementById("quset_img").src = "./img/opin_text_"+value+".png";
			var height_tmp = 55+(42*4);
			window.document.getElementById("quset_box").style.height = (height_tmp+100)+"px";
			window.document.getElementById("yes_btn").style.top = (height_tmp+15)+"px";
			window.document.getElementById("up_btn").style.top = (height_tmp+15)+"px";
			window.document.getElementById("btn_list").innerHTML = "";
			window.document.getElementById("yes_btn").style.display = "block";
			window.document.getElementById("read_page").focus();
			return false ;
		}
		if(value == 5)
		{
			window.document.getElementById("sp").style.display = "block";
			window.document.getElementById("quset_img").src = "./img/opin_text_"+value+"s.png";
			var height_tmp = 55+(42*quset_answer_data['answer_count'][value]);
			window.document.getElementById("quset_box").style.height = (height_tmp+100)+"px";

			window.document.getElementById("yes_btn").style.top = (height_tmp+15)+"px";
			window.document.getElementById("up_btn").style.top = (height_tmp+15)+"px";
			window.document.getElementById("btn_list").innerHTML = "";
			return false ;
		}



		window.document.getElementById("sp").style.display = "none";
		window.document.getElementById("quset_img").src = "./img/opin_text_"+value+".png";

		//設定框大小
		//0 55px
		//1 95px
		//2 135px
		var height_tmp = 55+(42*quset_answer_data['answer_count'][value]);
		window.document.getElementById("quset_box").style.height = (height_tmp+100)+"px";
		//設定下一步按鈕位置
		window.document.getElementById("yes_btn").style.top = (height_tmp+15)+"px";
		window.document.getElementById("up_btn").style.top = (height_tmp+15)+"px";

		//設置按鈕

		window.document.getElementById("btn_list").innerHTML = "";
		for(var i = 1 ; i <= quset_answer_data['answer_count'][quest_on]; i ++)
		{
			window.document.getElementById("btn_list").innerHTML = window.document.getElementById("btn_list").innerHTML+'<div id="btn_list_'+i+'" onClick="chick_ans_btn('+i+')" style="position:absolute; top:'+(42*i-42)+'px; left:0px; width:30px; height:30px;  cursor:pointer" class="btn_off"></div>';
		}


	}

	//送出填寫的
	function set_books_ver_check_box()
	{
		var ver_key = new Array();
		var ver_val = new Array();
		var i = 0;
		for(key in ver_on_list)
		{
			ver_key[i] = key;
			ver_val[i] = ver_on_list[key];
			i++;
		}
		//統計
		var url = "./ajax/set_books_ver.php";
		$.post(url, {
					user_id:user_id,
					book_sid:about_book_sid,
					user_permission:t_permission,
					ver_key:JSON.stringify(ver_key),
					ver_val:JSON.stringify(ver_val),

		}).success(function (data)
		{
			console.log(data);
			if(data[0]!="{")
			{
				cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員 cod2",1);
				return false;
			}


			if(data_array["error"]!="")
			{
				cover(data_array["error"]);
				return false;
			}
			if(data_array["echo"]!="")
			{

				cover(data_array["echo"],1);
			}else
			{

				outt();
				cover("");

			}
		}).error(function(e){
			//echo("AJAX:error:get_opinion_qa():");
			cover("喔喔?! 讀取失敗了喔  請確認網路連",1);
			//get_opinion_qa();
		}).complete(function(e){
			//echo("AJAX:complete:get_opinion_qa():");
		});
	}

	//讀取答題的題目與回答
	//
	function get_opinion_qa()
	{
		//echo("get_opinion_qa:初始開始");
		//cover("讀取題庫中")

		var url = "./ajax/get_opinion_qa.php";
		$.post(url, {
					user_id:user_id,
					user_permission:t_permission

			}).success(function (data)
			{
               
				// if(data[0]!="{")
				// {
				// 	cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員 cod2",1);
				// 	echo("AJAX:success:get_opinion_qa():資料庫發生問題");
				// 	return false;
				// }

				quset_answer_data = data_array = JSON.parse(data);
				//echo("AJAX:success:get_opinion_qa():已讀出:"+data);
				console.log(quset_answer_data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					window.alert(data_array["echo"]);
					get_opinion_qa();

				}else
				{
					//echo("AJAX:success:get_opinion_qa():完成:開始建立題庫");
					//設定題目囉

					quest_list = data_array["topic"];

					main_v2();
					sp_7_set_quset();
					cover();
					//set_new_quset();

				}
				//sp_7_set_quset();
			}).error(function(e){
				//echo("AJAX:error:get_opinion_qa():");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",1);
				//get_opinion_qa();
			}).complete(function(e){
				//echo("AJAX:complete:get_opinion_qa():");
			});
	}

	//讀取書籍類型
	function get_books_ver()
	{

		if(window.parent.user_school=="")return false;

		var url = "./ajax/get_books_ver.php";
		$.post(url, {
					user_id:user_id,
					book_sid:about_book_sid,
					user_permission:t_permission,
					user_school:school_code

			}).success(function (data)
			{

				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員 cod2",1);
					//echo("AJAX:success:get_opinion_qa():資料庫發生問題");
					return false;
				}

				data_array = JSON.parse(data);
				//echo("AJAX:success:get_opinion_qa():已讀出:"+data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{

					cover(data_array["echo"],1);
				}else
				{

					if(data_array["count_category"]>0)
					{

						//window.document.getElementById("btn_help_page2_n").style.display = "block";
						//if(data_array["count_category_user"]==0)window.document.getElementById("btn_help_page2").style.display = "block";
						//else window.document.getElementById("btn_help_page2").style.display = "none";
						//建立書籍選填欄位
						var bvcb = window.document.getElementById("books_ver_check_box");
						bvcb.innerHTML = "<div style='font-weight:bold; font-size:24px;text-decoration:underline;'>請選擇書籍的類型(可複選)</div><BR>";

						for(var i = 0; i < data_array["count_category"]; i++)
						{
							var has_click = 0;
							for(var j = 0; j < data_array["count_category_user"]; j++)
							{
								if(data_array["book_category"][i]['cat_id'] == data_array["category_user"][j]['rev_id'])has_click = 1
							}
							if(has_click == 0)
							{
								bvcb.innerHTML += '<div style="float:left; width:100px; height:40px; position:relative;"><div id="books_ver_'+data_array["book_category"][i]['cat_id']+'" onClick="click_books_ver('+data_array["book_category"][i]['cat_id']+')" style="width:30px; height:30px;position:absolute; left:0px;  cursor:pointer; float:left;" class="btn_off"></div><div style="width:100px; position:absolute; left:40px; font-weight:bold; font-size:24px;">'+data_array["book_category"][i]['cat_name']+'</div></div>';
								ver_on_list[data_array["book_category"][i]['cat_id']] = 0;
							}
							else
							{
								bvcb.innerHTML += '<div style="float:left; width:100px; height:40px; position:relative;"><div id="books_ver_'+data_array["book_category"][i]['cat_id']+'" onClick="click_books_ver('+data_array["book_category"][i]['cat_id']+')" style="width:30px; height:30px;position:absolute; left:0px;  cursor:pointer; float:left;" class="btn_on"></div><div style="width:100px; position:absolute; left:40px; font-weight:bold; font-size:24px;">'+data_array["book_category"][i]['cat_name']+'</div></div>';
								ver_on_list[data_array["book_category"][i]['cat_id']] = 1;
							}
						}
					}
				}

			}).error(function(e){
			}).complete(function(e){
			});
	}
	function click_books_ver(val)
	{
		if(window.document.getElementById("books_ver_"+val).className == "btn_on")
		{
			ver_on_list[val] = 0;
			window.document.getElementById("books_ver_"+val).className = "btn_off";
		}
		else
		{
			ver_on_list[val] = 1;
			window.document.getElementById("books_ver_"+val).className = "btn_on";
		}
	}

	function opinion_registration()
	{
		var answer_tmp = new Array();
		var tmp_echo = "";
		for(var i = 1 ; i<=quset_answer_data["quest_count"];i++)
		{

			answer_tmp["quest_ans_"+i] = answer_list[i];
			answer_tmp["quest_topic_id_"+i] = i;
			tmp_echo = tmp_echo+"Q_id = "+answer_tmp["quest_topic_id_"+i]+" => A:"+ answer_tmp["quest_ans_"+i]+" -- ";
		}




		cover("儲存中");
		var url = "./ajax/set_opinion_registration.php";
		
		$.post(url, {
					re_answer:JSON.stringify(re_answer),
					quest_list:JSON.stringify(quest_list),
					auth_coin_open:window.parent.auth_coin_open,
					borrow_sid:book_borrow_sid,
					book_sid:about_book_sid,
					user_id:user_id,
					user_permission:t_permission
			}).success(function (data)
			{
				console.log(data);
				//echo("AJAX:success:opinion_registration():已讀出:"+data);
				if(data[0]!="{")
				{
					cover(data+"資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員cod3",1);
					//echo("AJAX:success:opinion_registration():資料庫發生問題");
					return false;
				}

				quset_answer_data = data_array = JSON.parse(data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					cover(data_array["echo"],1);
				}else
				{
					//echo("AJAX:success:opinion_registration():完成閱讀登記");

					if(data_array["coin"]>0)
					{
						cover("完成登記了喔<BR>並獲得訂閱獎勵<BR>"+data_array["coin"]+"$",1);
						if(window.parent.tittle =="op")
						document.location.href="../page_finish_registration/index.php";
						if(window.parent.tittle =="st")
						window.parent.set_page("page_opinion_menu");
					}
					else
					{
						cover("完成登記了");
						// if(window.parent.tittle =="op")
						document.location.href="../page_opinion_menu/index.php";
						// if(window.parent.tittle =="st")
						// window.parent.set_page("page_opinion_menu");
					}



				}

			}).error(function(e){
				//echo("AJAX:error:get_opinion_qa():");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",1);
				//get_opinion_qa();
			}).complete(function(e){
				//echo("AJAX:complete:get_opinion_qa():");
			});


	}
	function go_out()
	{
		window.parent.change_page("page_opinion_menu");
	}

	//顯示書籍資訊
	function show_book()
	{
		//在書店內展開離開按鈕
		// if(window.parent.tittle =="st")
		// {
			window.document.getElementById("out_b").style.right = "20px";
			window.document.getElementById("out_b").style.width = "100px";
			window.document.getElementById("out").style.display = "block";
		// }
		//顯示書籍資訊
		window.document.getElementById("book_pic").src = about_book_src;
		window.document.getElementById("book_pic").style.display = "block";
		if(about_book_name)window.document.getElementById("text_name").innerHTML = "<span>書名:"+ about_book_name+"</span>";
		if(about_book_author)window.document.getElementById("text_author").innerHTML = "<span>作者:" + about_book_author+"</span>";
		if(about_book_publisher)window.document.getElementById("text_publisher").innerHTML = "<span>出版社:" +about_book_publisher+"</span>";
		//if(window.parent.book_info[value]["book_donor"])window.document.getElementById("text_donor").innerHTML = window.parent.book_info[value]["book_donor"];

			if(about_book_page_count>0)
			{
				book_max_page = about_book_page_count;
			}else
			{
				book_max_page = 0;
			}

		get_books_ver();
		get_opinion_qa();
	}
	//---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        $(function(){
            //初始化, 禁止滑鼠事件

            get_opinion_qa();
            show_book();
            // $(document).on("mousewheel DOMMouseScroll", function(e){
            //     e.preventDefault();

            //     console.log("2222");
            //     return false;
            // }).dblclick(function(e){
            //     e.preventDefault();
            //     console.log("1111");
            //     return false;
            // });

    //         // 判斷是否有書名
    //         var value=window.parent.book_choose;
    //         // console.log(value);
    //         if(window.parent.book_info[value]["book_name"]==""){

    //         	//console.log(window.parent.book_info[value]["book_name"]);

				// //location.href="../page_book_registration/index.php";
				// //console.log(window.document.location.href);
				// // window.parent.set_page("../read_the_registration_v2/page_book_registration");


				// window.parent.set_page("../read_the_registration_v2/page_update_bookname");

    //         }
    //         else{
    //         	show_book(window.parent.book_choose);
    //         }


        });
	
    </script>
</Html>














