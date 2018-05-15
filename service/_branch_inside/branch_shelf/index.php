<!DOCTYPE html>
<? session_start(); ?>
	<head>

		<script src="../../../lib/jquery/basic/code.js"></script>
		<script src="../../../lib/jquery/plugin/code.js"></script>
        <script type="text/javascript" src="../../branch/inc/add_action_branch_log/code.js"></script>
		<?php
		//---------------------------------------------------
		//設定與引用
		//---------------------------------------------------

			//SESSION
			@session_start();

			//啟用BUFFER
			@ob_start();

			//外掛設定檔
			require_once(str_repeat("../",3)."/config/config.php");

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
			//$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
		//---------------------------------------------------
		//END   設定與引用
		//---------------------------------------------------

        //---------------------------------------------------
        //撈取, 學校資訊
        //---------------------------------------------------

            $sess_uid        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:0;
            $sess_class_code =(isset($_SESSION['class'][0][1]))?trim($_SESSION['class'][0][1]):'';
            $sess_school_code=(isset($_SESSION['school_code']))?trim($_SESSION['school_code']):'';
            $sess_sem_year   =(isset($_SESSION['sem_year']))?(int)$_SESSION['sem_year']:0;
            $sess_sem_term   =(isset($_SESSION['sem_term']))?(int)$_SESSION['sem_term']:0;
            $sess_grade      =(isset($_SESSION['grade']))?(int)$_SESSION['grade']:0;

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

		//---------------------------------------------------
		//CSS 設定
		//---------------------------------------------------
		?>
		<style>
            body
            {overflow:hidden;
            position:relative;}
			/*灰階特效*/
			.grays {

            }
        </style>
	</head>

<body>




        <div id="debug" style="position:absolute; top:505px; left:8px;">fffffffffff</div>
        <?
        //預設讀入的資料

		//GET
		$user_id = (int)$_GET["user_id"];
		$branch_id = (int)$_GET["branch_id"];

        //-----------------------------------------------
        //撈取是否為朋友
        //-----------------------------------------------

            $friend_ident=0;
            if($sess_uid!==$user_id){
                $sql="
                    SELECT
                        `track_id`
                    FROM `mssr_track_user`
                    WHERE 1=1
                        AND `track_from` IN ({$sess_uid},{$user_id})
                        AND `track_to` IN ({$sess_uid},{$user_id})
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(!empty($arrys_result)){
                    $friend_ident=1;
                }
            }
		?>


		<!--========================================================
		//DEBUG文字列
		//========================================================-->
		<div style="left:0px; top:480px; position: absolute;" id="debug"></div>
		<script language="javascript">
        var json_category_info=<?php echo $json_category_info;?>;
		//事件 :  遮罩開關
		function set_hide(open_on,text)
		{
			if(open_on==false)
			{
				//$.unblockUI();
			}else if(open_on==true)
			{
				//$.blockUI({ message: '<div style="z-index: 2000;">'+text+'</div>'});
			}
		}
		function add_debug(value)
		{
			if(1)
			{
				window.document.getElementById("debug").innerHTML = value+"<br>"+window.document.getElementById("debug").innerHTML;
			}
		}
		//set_hide(1,"讀取中");
		//========================================================
		//建立
		//========================================================
		var user_id = <? echo $user_id; ?>;
		var branch_id = <? echo $branch_id;?>;
		var visit = <?php if($user_id != $_SESSION['uid']){echo 1;}else{echo 0;}?>;
		var up_book_array = new Array();
		var click_book_list_number = -1;//選擇的書籍列
		var click_book_list_sid = "";//選擇的書籍列BOOK_SID
		//========================================================
		//圖片預載
		//========================================================

		//===========設定圖片預載陣列==============
		add_debug("圖片預載:開始");
		var images = new Array();

		</script>
        <!--======================================================
		//Html 內文
		========================================================-->

        <!-- 背景 -->
        <div style="position:absolute; top:-10px; left:-10px; background-color:#333333; opacity:0.8; height:530px; width:1050px;"></div>

        <!-- ====================書架======================== -->
		<div style="position:absolute; top:-9px; left:80px;">

            <img src="img/up_box.png" style="position:absolute; top:-9px; left:172px;"><img src="img/out.png" onClick="close_page()" style="position:absolute; cursor: pointer; top:408px; left:625px; width: 81px; height: 78px;">

            <!-- 架上書籍 -->
            <?php for($i = 0 ; $i < 5; $i++){ ?>
                <div id="up_books_<?php echo $i?>" onClick="click_book('<?php echo $i?>')"  style="cursor: pointer; position:absolute; top:<? echo (50+((int)($i/4))*122);?>px; left:<? echo (220+((int)($i%4))*120);?>px;">
                    <img id="up_books_img_<?php echo $i?>" src="img/a_book_img.png" style="position:absolute; top:0px; left:0px; width: 83px; height: 102px;">
                    <div id="up_books_text_<?php echo $i?>"style="position:absolute; top:13px; left:2px; width: 83px; height: 41px; overflow:hidden">我妳爸我妳爸我妳爸我妳爸我妳爸拉</div>
                </div>
                <img id="up_books_del_<?php echo $i?>" onClick="set_down_book(<?php echo $i?>)" src="img/no.png" style=" display:none;position:absolute; cursor: pointer; top:<? echo (36+((int)($i/4))*122);?>px; left:<? echo (278+((int)($i%4))*120);?>px; width: 38px; height: 38px;">
           <?php } ?>



            <!-- 按鈕 -->
            <img id="read_btn" src="img/read.png" onClick="go_read_page()" style="position:absolute; top:387px; left:547px; display:none">
            <img id="go_shelf_btn" onClick="set_up_book_page()" src="img/on_shelf.png" style="position:absolute; top:288px; left:462px; cursor: pointer;">



		</div>

        <!-- ====================上架選書======================== -->
		<div id="set_up_page" style="position:absolute; top:-9px; left:172px; display:none;">
        	<div style="background-color:#111; opacity:0.8; width:1050px; height:530px; position:absolute; left:-174px; top:0px;"></div>
            <img src="img/page_back.png" style="position:absolute; top:0px;">
            <table width="424" border="0" cellpadding="0" cellspacing="0" bgcolor="#DDFFDD"style="position:absolute; top:89px; left:111px; width: 452px; font-size:24px">
            <tr><td width="291">書籍名稱</td><td width="140">老師許可</td></tr>
          </table>
          	<iframe id="select_book_page" src="up_book_page.php?user_id=<? echo $user_id; ?>&branch_id=<? echo $branch_id; ?>" width="300" height="300" style="position:absolute; top:116px; left:110px; height: 238px; width: 451px;"></iframe>
		    <img id="set_up_book_btn" src="img/yes.png" style="position:absolute;cursor: pointer; top:355px; left:129px; width: 79px; height: 77px; display:none" onClick="set_up_book()">
            <img src="img/no.png" style="position:absolute;cursor: pointer; top:355px; left:449px; width: 79px; height: 77px;" onClick="close_up_book_page()">
            <div id="book_name" style="position:absolute; top:45px; left:197px; overflow:hidden; white-space: nowrap; font-size:36px; color:#673616; font-weight: bold; width: 292px;">選取書籍並上架</div>
</div>

        <!-- ====================觀看頁面======================== -->
		<div id="read_page" style="position:absolute; top:-10px; left:-10px;">

        </div>


<script>
		//========================================================
		//JS Function
		//========================================================


		//點選上架的書籍
		function click_book(value)
		{
			add_debug("click_book:開始:點選的書籍為:"+value);
			for(i = 0 ; i < 5; i++)
			{
				window.document.getElementById("up_books_img_"+i).src = "img/a_book_img.png";
			}
			window.document.getElementById("up_books_img_"+value).src = "img/a_book_img_l.png";
			click_book_list_number = value;
			window.document.getElementById("read_btn").style.display = "block";
		}

		//設定上架的書籍
		function set_up_books()
		{
			add_debug("set_up_books:開始:設定上架筆數"+up_book_array.length);
			for(i = 0 ; i < 5; i++)
			{
				if( i < up_book_array.length)
				{//有書籍存在 設定書籍
					window.document.getElementById("up_books_"+i).style.display = "block";
					if(!visit)window.document.getElementById("up_books_del_"+i).style.display = "block";
					window.document.getElementById("up_books_text_"+i).innerHTML = up_book_array[i]["book_name"];


				}
				else
				{//無書籍則隱藏

					window.document.getElementById("up_books_"+i).style.display = "none";
					window.document.getElementById("up_books_del_"+i).style.display = "none";
				}


			}
			if(up_book_array.length >= 5 || visit)
			{
				window.document.getElementById("go_shelf_btn").style.display = "none";
			}else
			{
				window.document.getElementById("go_shelf_btn").style.display = "block";
				window.document.getElementById("go_shelf_btn").style.top = window.document.getElementById("up_books_"+up_book_array.length).style.top ;
				window.document.getElementById("go_shelf_btn").style.left = window.document.getElementById("up_books_"+up_book_array.length).style.left ;
			}
		}

		//搜尋已上架的書籍
		function get_branch_shelf()
		{
			add_debug("get_branch_shelf:post:開始");
			//set_hide(1,"讀取中");
			var url = "./ajax/get_branch_shelf.php";
			$.post(url, {
					user_id:user_id,
					branch_id:branch_id
				}).success(function (data)
				{
					add_debug("get_branch_shelf:post:get_data -> "+data);
					data_array = JSON.parse(data);
					if(data_array["error"])
					{
						window.alert("錯誤資訊:"+data_array["state"]+"請重新整理頁面");
						window.location.reload() ;
					}
					up_book_array = data_array;
					add_debug("set_up_books:開始:設定上架筆數"+up_book_array);
				}).error(function(e){
					add_debug("get_branch_shelf:post:error -> "+e );
				}).complete(function(e){
					add_debug("get_branch_shelf:post:complete");
					set_up_books();
				});

		}
		//換至上架頁面
		function set_up_book_page()
		{
			add_debug("set_up_book:開啟上架書籍頁面:開始:");
			window.document.getElementById('select_book_page').src = 'up_book_page.php?user_id=<? echo $user_id; ?>&branch_id=<? echo $branch_id; ?>';

			window.document.getElementById('set_up_page').style.display='block';
		}
		//關閉上架頁面
		function close_up_book_page()
		{
			window.document.getElementById("set_up_book_btn").style.display = "none";
			add_debug("close_up_book_page:關閉上架書籍頁面:開始:");
			window.document.getElementById('set_up_page').style.display='none';
			get_branch_shelf();
		}
		//設定上架
		function set_up_book()
		{
			window.document.getElementById("set_up_book_btn").style.display = "none";
			add_debug("set_up_book:上架書籍:開始:"+click_book_list_sid);
			//set_hide(1,"讀取中");
			var url = "./ajax/set_up_book.php";
			$.post(url, {
					user_id:user_id,
					branch_id:branch_id,
					book_sid:click_book_list_sid
				}).success(function (data)
				{
					add_debug("set_up_book:post:get_data -> "+data);

                    //console.log(parent.data_array["branch_name"]);

                    //action_log
                    for(key1 in json_category_info['lv1']){

                        var cat_id  =parseInt(json_category_info['lv1'][key1]['cat_id']);
                        var cat_name=(json_category_info['lv1'][key1]['cat_name']);

                        if(cat_name===parent.data_array["branch_name"]){
                            //action_log
                            add_action_branch_log(
                                '../../branch/inc/add_action_branch_log/code.php',
                                'rep05',
                                user_id,
                                user_id,
                                click_book_list_sid,
                                0,
                                branch_id,
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

                            if(cat_name===parent.data_array["branch_name"]){
                                //action_log
                                add_action_branch_log(
                                    '../../branch/inc/add_action_branch_log/code.php',
                                    'rep05',
                                    user_id,
                                    user_id,
                                    click_book_list_sid,
                                    0,
                                    branch_id,
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
                                        '../../branch/inc/add_action_branch_log/code.php',
                                        'rep05',
                                        user_id,
                                        user_id,
                                        click_book_list_sid,
                                        0,
                                        branch_id,
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
				}).error(function(e){
					add_debug("set_up_book:post:error -> "+e );
				}).complete(function(e){

					window.document.getElementById('set_up_page').style.display='none';
					get_branch_shelf();
				});
		}

		//將現有書籍下架
		function set_down_book(value)
		{

			add_debug("set_down_book:下架書籍:開始:"+up_book_array[value]['book_sid']);
			var url = "./ajax/set_down_book.php";
			$.post(url, {
					user_id:user_id,
					branch_id:branch_id,
					book_sid:up_book_array[value]['book_sid']
				}).success(function (data)
				{
					add_debug("set_down_book:post:get_data -> "+data);

				}).error(function(e){
					add_debug("set_down_book:post:error -> "+e );
				}).complete(function(e){
					get_branch_shelf();
				});

		}

		//前往觀看頁面
		function go_read_page()
		{
			add_debug("go_read_page:開始前往觀看頁面:"+up_book_array[click_book_list_number]['book_sid']);
			window.document.getElementById("read_page").innerHTML='<iframe src="./read_book_page/index.php?user_id='+user_id+'&book_sid='+up_book_array[click_book_list_number]['book_sid']+'" width="1050" height="530" style="position:absolute; top:0px; left:0px;"></iframe>';

            //action_log
            add_action_branch_log(
                '../../branch/inc/add_action_branch_log/code.php',
                'la05',
                <?php echo $sess_uid;?>,
                <?php echo $user_id;?>,
                up_book_array[click_book_list_number]['book_sid'],
                <?php echo $friend_ident;?>,
                <?php echo $branch_id;?>,
                '',
                0,
                0,
                0,
                0,
                0,
                '',
                ''
            );
		}

		//關閉此頁
		function close_page()
		{
			window.parent.document.getElementById('up_page').innerHTML = "";
		}
		get_branch_shelf();
		</script>
</body>