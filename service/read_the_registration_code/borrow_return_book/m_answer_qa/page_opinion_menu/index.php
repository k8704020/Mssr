<?php
//-------------------------------------------------------
//閱讀登記條碼版
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
            $page=str_repeat("../",1)."login/loginF.php";

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
        $first_borrow='yes';
        if((isset($_SESSION['_read_the_registration_code']['_login']['first_borrow']))&&($_SESSION['_read_the_registration_code']['_login']['first_borrow']==='no')){
            $first_borrow='no';
        }

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
        //isbn碼輸入提醒查詢
        //---------------------------------------------------

              $sql="
                    SELECT auth 
                    FROM  mssr.`mssr_auth_user` 
                    WHERE  create_by='{$_SESSION['t']['uid']}'

            ";


        $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

        $auth                               =unserialize($result[0]['auth']);

        $_SESSION['t']['read_opinion_limit_day']=$auth['read_opinion_limit_day'];






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
    
    <script type="text/javascript" src="../../../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
    <script src="js/select_thing.js" type="text/javascript"></script>
    <link rel="stylesheet" href="css/btn.css">
	<style>

	*{
		outline: 1px solid #fac;
	}
     
		#popopo{
			position:absolute;
			width:223px;
			height:284px;
			background: url('./img/pagge_2.png') 0px 0;
		}
		#popopo:hover {
    		background: url('./img/pagge_2.png') 0px -284px;
		}
		
	</style>
</Head>
<body>

<!-- 
	<span>hello</span> -->

	<!--==================================================
    html內容
    ====================================================== -->
    <!-- 遮屏-->
	

	<!-- <div style="position:absolute; top:20px; left:0px; width:750px; height:500px; background-color:#000000; opacity:0.8;" onClick=""></div> -->

	<div style="position:relative; text-align: center;">
        
        <img src="../img/back_s.png" style="position:absolute; left:89px; top: 80px;" border="0">
        <span style="font-family: 微軟正黑體; font-size: 40px; font-weight:600 ;position:relative;  top: 20px; "><?php echo $_user_name;?>登記的書籍</span>
        <!-- <img src="./img/reg_tittle.png" style="position:absolute; left:280px; top: 20px;" border="0"> -->
        <!-- <a id="out" class="btn_close" onClick="out()" style="position:absolute; left:698px; top: 383px; cursor:pointer;"></a> -->
      <div id="iframe" style="position:absolute;top:0px;left:-100px;"><iframe src="manu.php" frameborder="0" width="600" height="287" style="position:absolute; top:145px; left:250px; " ></iframe></div>
        <a id="left_btn" class="btn_arrow_l" onClick="set_page(-1)" style="position:absolute; cursor:pointer; left:249px; top: 399px;display:none;"></a>
        <a id="right_btn" class="btn_arrow_r" onClick="set_page(1)" style="position:absolute; cursor:pointer;left:500px; top: 399px; display:none;" ></a>
      <div id="page_text" style="position:absolute; top:402px; left:322px; width:177px; height:47px; font-size: 36px; text-align: center; font-weight: bold; color: #406031;"></div>
    </div>
    <!--按鈕列-->
    <!-- <img src="./img/pagge_1.png" style="position:absolute; left:680px; top: 379px; width: 295px; height: 74px; " border="0"> -->
	<a id="popopo" onClick="goooooooo()" style="position:absolute; left:708px; top: 82px; display:none;" ></a>
	<!-- 說明-->
   <!--  <a class=" btn_help" onClick="open_helper(9)" style="position:absolute; top:0px; left:896px; cursor:pointer;"></a> -->
	<div id="helper" style="position:absolute; top:-8px; left:-8px; width:1040px; height:480px; display:none; overflow:hidden;"></div>
	
</body>
	<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var opinion_max_count = 0;
	var max_page = 1 ; 
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	function out()
	{
		window.parent.set_page("");	
	}
	
	//cover
	// function cover(text,type,proc)
	// {
		
	// 	window.parent.cover(text,type);
	// 	if(type == 2)
	// 	{
	// 		delayExecute(proc);
	// 	}
	// }
	/*cover 啟用器的用法
	 cover("這嘎");
	 cover("這嘎",1);
	 cover("這嘎",2,function(){echo("哈哈");});
	*/
	//cover 點選器
	function delayExecute(proc) {
		var x = 100;
		var hnd = window.setInterval(function () {
			if(window.parent.cover_click ==1 )
			{//點選確定的狀況
				window.parent.cover_click = -1;
				window.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選確定");
				proc();
				cover("");
			}
			else if(window.parent.cover_click ==0 )
			{//點選取消的狀況
				window.parent.cover_click = -1;
				window.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選取消");
				cover("");
			}
		}, x);
	}
	//debug
	// function echo(text)
	// {
	// 	window.parent.echo(text);
	// }
	//=========MAIN=============
	function get_opinion_count()
	{
		// echo("get_opinion_count:初始開始:讀取進貨筆數");
		//cover("讀取販賣頁面")
		var url = "./ajax/get_opinion_count.php";
		$.post(url, {
					auth_read_opinion_limit_day:window.parent.auth_read_opinion_limit_day,
					user_id:window.parent.home_id,
					user_permission:window.parent.user_permission,
					auth_open_publish:window.parent.auth_open_publish
					
			}).success(function (data) 
			{
				// echo("AJAX:success:get_opinion_count():讀取進貨筆數:已讀出:"+data);
				if(data[0]!="{")
				{
					// echo("AJAX:success:get_opinion_count():讀取進貨筆數:資料庫發生問題");
					// cover("資料庫好像有點問題呢，請再試試看<BR>",2,function(){get_opinion_count();});
					return false;
				}
				
				data_array = JSON.parse(data);
				
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
					opinion_max_count = data_array["opinion_count"];
					if(opinion_max_count == 0)
					{max_page = 1;}
					else
					{max_page = Math.floor((Math.floor(opinion_max_count)-1)/10)+1;}
					
					set_page(0);
				}
				
			}).error(function(e){
				// echo("AJAX:error:get_opinion_count():讀取進貨筆數:");
				// cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){get_opinion_count();});
			}).complete(function(e){
				// echo("AJAX:complete:get_opinion_count():讀取進貨筆數:");
			});
	}

	// function set_page(value)
	// {
	// 	window.document.getElementById("popopo").style.display = "none";
	// 	echo("set_page");
	// 	window.parent.page_list["opinion"] = window.parent.page_list["opinion"]+value;
	// 	if(window.parent.page_list["opinion"] == 1 ) 
	// 	{window.document.getElementById("left_btn").style.display = "none";}
	// 	else
	// 	{window.document.getElementById("left_btn").style.display = "block";}
		
	// 	if(window.parent.page_list["opinion"] == max_page) 
	// 	{window.document.getElementById("right_btn").style.display = "none";}
	// 	else
	// 	{window.document.getElementById("right_btn").style.display = "block";}
	// 	window.document.getElementById("page_text").innerHTML = window.parent.page_list["opinion"]+" / "+max_page+" 頁";
	// 	window.document.getElementById("iframe").innerHTML ='<iframe src="manu.php" frameborder="0" width="500" height="287" style="position:absolute; top:113px; left:236px; " ></iframe>';
	// 	cover("");
	// }
	//GGGGGGGGGGGGGGGGGGGOOOOOOOOOOOOOO近進貨拉
	// function goooooooo() {
	// 	//吃土
	// 	echo("我要這本書拉WW");
	// 	window.parent.set_action_bookstore_log(window.parent.user_id,'e21',window.parent.action_on);
	// 	window.parent.set_page("../read_the_registration_v2/page_opinion_registration2");
		
	// }
	//---------------------------------------------------
    //helper
    //---------------------------------------------------
	function open_helper(value)
	{
		window.document.getElementById("helper").innerHTML="<iframe src='../page_helper/index.php?id="+value+"' style='position:absolute; top:0px; left:0px; width:1040px; height:590px;  overflow:hidden;' frameborder='0'></iframe>";
		window.document.getElementById("helper").style.display = "block";
	}
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	get_opinion_count();
 

    </script>
</Html>
    
<?php
//-------------------------------------------------------
//page_nrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>

    
    
    
    
    
    
    
    
    
    
    
    
    