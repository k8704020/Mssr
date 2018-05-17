<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,閱讀登記->搜尋資料庫的書籍資料
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
        require_once(str_repeat("../",3).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
					APP_ROOT.'lib/php/db/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

		//建立連線
		$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
        $conn_user=conn($db_type='mysql',$arry_conn_user);

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

        $user_id        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
		if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");
    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

	//---------------------------------------------------
	//SQL
	//---------------------------------------------------

		$max_borrows = 10;
		$borrows = 0;
		$borrows_lock = 0;
		//判斷登記本數限制
		$sql = "
				SELECT `auth`,count(1) AS `count`
				FROM  `user`.`student`
				LEFT JOIN `mssr_auth_class`
				ON `mssr_auth_class`.`class_code` = `user`.`student`.`class_code`
				WHERE uid = {$user_id}
				AND '".date("Y-m-d")."' BETWEEN start AND end
				";

		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($retrun as $key1=>$val1)
		{
			$borrows_lock = $val1['count'];
			if($val1['auth'])
			{
				$auth=unserialize($val1['auth']);
				if(isset($auth['borrow_limit_cno']))
				{

					$max_borrows = $auth['borrow_limit_cno'];
				}
			}
		}


		if($borrows_lock == 0)$max_borrows = 10;
		$sql = "SELECT COUNT(1) AS `count`
				FROM `mssr_book_borrow`
				WHERE `user_id` = {$user_id}
				AND `borrow_sdate` BETWEEN '".date("Y-m-d")." 00:00:00' AND '".date("Y-m-d")." 23:59:59'";
		$retrun_count = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		$borrows = $retrun_count[0]['count'];


		$sql="
			SELECT `status`,`permission`
			FROM `permissions`
			WHERE 1=1
				AND `permission`='{$permission}'
		";
		$guest = false;
		$u_mssr_bs = false;
		$db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
		if(!empty($db_results)){
			foreach($db_results as $db_result){
				$rs_status=trim($db_result['status']);
				if(trim($db_result['status'])==='u_mssr_bs'){ $u_mssr_bs = true;}
				if(trim($db_result['permission'])==='guest_s'){ $guest = true;}
				if(trim($db_result['permission'])==='guest_t'){ $guest = true;}
				if(trim($db_result['permission'])==='guest_f'){ $guest = true;}
			}
		}
		if(!$u_mssr_bs)die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");


		$sql="
			SELECT `school_code`
			FROM `member_school`
			WHERE `uid` =".$_SESSION['uid']."
		";
		$db_results=db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
		 if(!empty($db_results)){
			foreach($db_results as $db_result){
				if(trim($db_result['school_code'])==='exp'){ $guest = true;}
			}
		}
?>
<!DOCTYPE HTML>
<Html>
<Head>
	<Title>搜尋書籍</Title>
    <!-- 掛載 -->
    <link href="../css/registration_btn.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>



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
		#out_btn:hover {
    		background: url('../../bookstore_v2/bookstore_courtyard/img/btn_list_1.png') -600px -100px;
		}
		#out_btn{
			position:absolute;
			width:120px;
			height:100px;
			background: url('../../bookstore_v2/bookstore_courtyard/img/btn_list_1.png') -600px 0;
		}
		.box_ling{
			-webkit-border-radius: 8px;
			-moz-border-radius: 8px;
			border-radius: 8px;

			-webkit-box-shadow: #242 1px 1px 3px;
			-moz-box-shadow: #242 1px 1px 3px;
			box-shadow: #242 2px 1px 1px;

			border: 2px solid #3a8c3a;
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
	</style>
</Head>
<body>
	<!--==================================================
    html內容
    ====================================================== -->


	<!-- 登記頁 -->
    <?
		if($max_borrows > $borrows || $_SESSION['uid'] == 5030)
		{  ?>
    <div id="borrow_page" style="position:absolute; top:-47px; left:8px;">
    	<div id="name" style="position:absolute; top:108px; left:-3px; height:128px; width:284px; text-align: right; font-size: 42px; font-weight: bold;"></div>
        <!-- 輸入框 9789577660275  957-493-285-0   9789860111385  9788301133337-->
      <div style="position:absolute; top:249px; left:214px; width:573px; height:59px; background-color:#aaffaa;" class="box_ling"></div>
        <img src="img/opin_welcome.png" style="position:absolute; top:118px; left:205px;">
        <input id="borrow_page_input_text" type="text" value="" style="position:absolute; top:256px; left:468px; font-size:32px; resize: none; width: 270px;" autofocus />

        <!-- 輸入小鍵盤 -->
      	<div style="position:absolute; top:324px; left:314px; width:359px; height:183px; background-color:#aaffaa;" class="box_ling"></div>

        <div style="position:absolute; top:337px; left:373px;">
        	<!-- 確認取消按鈕 -->
            <a id="borrow_page_ok" onClick="find_book()" class="btn_2" style="position:absolute; top:88px; left:128px; cursor: pointer;"></a>
            <a id="borrow_page_re" onClick="re_borrow_page()" class="btn_8"  style="position:absolute; top:88px; left:-50px; cursor: pointer;"></a>

        	<!-- 數字按鈕 -->
			<?PHP for($i = 0 ; $i <= 9 ; $i++){?>
          <div onClick="input_number(<?PHP echo $i;?>)" style="position:absolute; top:<?PHP echo ((int)($i/5))*48;?>px; left:<?PHP echo ((int)($i%5))*52;?>px; width:40px; height:39px; background-color:#00ca00; font-size:36px; text-align: center; cursor: pointer;" class="box_ling2"><?PHP echo $i;?></div>
            <?PHP } ?>
      </div>

	</div>
    <? }
	?>
	 <!-- 改過 -->
	<div style="position:absolute; top:402px; left:805px; width:150px; height:46px; background-color:#F8C967"></div>
	 <!-- 改過 -->
    <a id="out_btn" onClick="go_page('menu')"  style="position:absolute; top:370px; left:820px; cursor:pointer;"></a>
</body>


	<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------

	window.parent.isbn13 = "";
	window.parent.isbn10 = "";
	window.parent.book_id = "";
	window.parent.book_sid = "";

	//書籍資料
	window.parent.book_info = new Array();
	window.parent.book_choose = -1;
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------

	//cover
	function cover(text,type)
	{
		window.parent.cover(text,type);

	}
	//debug
	function echo(text)
	{
		//	window.parent.echo(text);
		//console.log(text);
	}

	//=========輸入書號頁面=============
	//重填事件
	function re_borrow_page()
	{
		echo("re_borrow_page:按下重填鍵:");
		window.document.getElementById("borrow_page_input_text").value = "";
	}

	//確定事件  開始找書嘞阿
	function find_book()
	{
		//優先問題阻擋
		if(window.document.getElementById("borrow_page_input_text").value == "")
		{
			cover("喔喔?! 你什麼東西都沒輸入喔",1);
			return false;
		}
		if(window.document.getElementById("borrow_page_input_text").value == "0000000000"||window.document.getElementById("borrow_page_input_text").value == "1111111111"||window.document.getElementById("borrow_page_input_text").value == "2222222222"||window.document.getElementById("borrow_page_input_text").value == "3333333333"||window.document.getElementById("borrow_page_input_text").value == "4444444444"||window.document.getElementById("borrow_page_input_text").value == "5555555555"||window.document.getElementById("borrow_page_input_text").value == "6666666666"||window.document.getElementById("borrow_page_input_text").value == "7777777777"||window.document.getElementById("borrow_page_input_text").value == "8888888888"||window.document.getElementById("borrow_page_input_text").value == "9999999999")
		{
			cover("禁止亂輸入書籍<br>請重新進入網頁",1);
			return false;
		}
		window.parent.book_id = window.document.getElementById("borrow_page_input_text").value;
		echo("find_book:按下確定鍵:搜尋書籍:輸入->"+window.parent.book_id);

		cover("搜尋書籍 請稍候");

		var url = "./ajax/get_book_info.php";
		$.post(url, {
					book_id:window.parent.book_id,
					user_id:window.parent.user_id,
					user_permission:window.parent.user_permission,
					user_school:window.parent.user_school
			}).success(function (data)
			{ //console.log(data);
				echo("AJAX:success:find_book():搜尋書籍:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",1);
					echo("AJAX:success:read_the_registration:登記書籍資料中:資料庫發生問題");
					return false;
				}
				data_array = JSON.parse(data);


				if(data_array["error"]!="")
				{//錯誤的
					cover(data_array["error"]);
					return false;
				}


				if(data_array["echo"]!="")
				{//訊息
					cover(data_array["echo"],1);

				}else
				{//成功的

					if(data_array["book_area"]=="go_find_internet")
					{//===================查無資料網路搜尋====================
						if(data_array["has_black"])
						{
							cover("此書籍為禁用書籍<BR>請登記其他書籍",1);
							return false ;
						}
						echo("AJAX:success:find_book():搜尋書籍:查無資料網路搜尋:");
						find_book_online(data_array["book_isbn_10"],data_array["book_isbn_13"])
					}
					else if(data_array["book_area"]=="")
					{//===================特殊例外，須查明======================
						echo("AJAX:success:find_book():搜尋書籍:特殊例外須查明:");
						cover("奇怪<BR>壞掉了?",1);
					}
					else
					{//===================資料庫搜索成功======================
						echo("AJAX:success:find_book():搜尋書籍:資料庫搜索成功:"+data_array["book_info"][0]["book_name"]);
						window.parent.book_info = data_array["book_info"];
						//轉向選擇書籍資訊
						document.location.href="../page_book_choose/index.php";
					}


				}

			}).error(function(e){
				echo("AJAX:error:find_book():搜尋書籍中:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",1);
				//find_book();
			}).complete(function(e){
				echo("AJAX:complete:find_book():搜尋書籍:");
			});

	}

	//確定事件  開始找書嘞阿(改過)
	function find_book_online(isbn10,isbn13)
	{

		echo("find_book_online:網路搜尋書籍:輸入10->"+isbn10+":輸入13->"+isbn13);
		cover(" 正在網路上搜尋可用資源  <BR>請稍候");

		window.parent.isbn13 = isbn13;
		window.parent.isbn10 = isbn10;
		var url = "./ajax/set_book_online_info.php";
		$.post(url, {
					isbn13:isbn13,
					isbn10:isbn10,
					user_id:window.parent.user_id,
					user_permission:window.parent.user_permission,
			}).success(function (data)
			{
				echo("AJAX:success:find_book_online:網路搜尋書籍:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",1);
					echo("AJAX:success:find_book_online:網路搜尋書籍:資料庫發生問題");
					return false;
				}
				data_array = JSON.parse(data);

				if(data_array["error"]!="")
				{//錯誤的
					cover(data_array["error"]);
					echo("AJAX:success:find_book_online:網路搜尋書籍:error:"+data_array["error"]);
					return false;
				}

				if(data_array["echo"]!="")
				{//訊息
					cover(data_array["echo"],1);
					echo("AJAX:success:find_book_online:網路搜尋書籍:echo:"+data_array["echo"]);
				}else
				{//成功的
					if(data_array["has_info"] != 0)
					{//成功於線上獲取書籍
						echo("AJAX:success:find_book_online:網路搜尋書籍:成功DOWNLOAD");
						find_book();
						return false;
					}
					else
					{//甚麼都找不到  哭哭了
						<? if($guest) {?>
						cover("網路上查無此資料<BR>正式帳號才可使用自建書籍功能!",1);
						<? }else{?>
						echo("AJAX:success:find_book_online:網路搜尋書籍:失敗  無書籍");
						cover("找不到書籍請自行輸入");

							window.document.location.href="../page_book_registration/index.php";

						<? } ?>
					}
				}

			}).error(function(e){
				echo("AJAX:error:find_book_online:網路搜尋書籍:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",1);
				//find_book();
			}).complete(function(e){
				echo("AJAX:complete:find_book_online:網路搜尋書籍:");
			});

	}

	//數字按鈕事件
	function input_number(value)
	{
		echo("input_number:按下數字鍵"+value+":");
		window.document.getElementById("borrow_page_input_text").value += value;
		document.getElementById('borrow_page_input_text').focus();
	}
	function go_page(value)
	{
		if(value == "menu")window.parent.parent.location.href = "../../mssr_menu.php";
	}
	//=========MAIN=============
	function main()
	{
		if(window.parent.user_name !="")
		{
			window.document.getElementById("name").innerHTML = window.parent.user_name;
			cover("")
			return false;
		}
		echo("Main:初始開始:讀取使用者資料");
		cover("讀取使用者資料中")
		var url = "../ajax/get_user_info.php";
		$.post(url, {
					user_id:window.parent.user_id,
					user_permission:window.parent.user_permission

			}).success(function (data)
			{
				echo("AJAX:success:main():讀取使用者資料:已讀出:"+data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",1);
					echo("AJAX:success:main():讀取使用者資料:資料庫發生問題");
					return false;
				}
				data_array = JSON.parse(data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				window.parent.user_name =data_array["user_name"];
				window.parent.user_sex =data_array["user_sex"];
				window.parent.user_permission =data_array["user_permission"];
				window.parent.user_personnel =data_array["user_class_code"][0]["user_personnel"];
				window.parent.user_class_code =data_array["user_class_code"][0]["class_code"];
				window.parent.user_grade =data_array["user_class_code"][0]["grade"];
				window.parent.user_class =data_array["user_class_code"][0]["class"];
				window.parent.user_school =data_array["user_class_code"][0]["school"];
				window.parent.user_school_category =data_array["user_class_code"][0]["school_category"];
				window.parent.auth_coin_open =data_array["auth_coin_open"];
				window.document.getElementById("name").innerHTML = data_array["user_name"];
				cover("");

			}).error(function(e){
				echo("AJAX:error:main():讀取使用者資料:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",1);
				//main();
			}).complete(function(e){
				echo("AJAX:complete:main():讀取使用者資料:");
			});

	}

	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        $(function(){
			$("#borrow_page_input_text").keypress(function(event){
				if (event.keyCode == 13)find_book();
			});
            //初始化, 禁止滑鼠事件
            $(document).on("mousewheel DOMMouseScroll", function(e){
                e.preventDefault();
                return false;
            }).dblclick(function(e){
                e.preventDefault();
                return false;
            });

        });


    <?	if($max_borrows > $borrows)
		{

		?>

		main();

		<? }else{
			echo "cover('一天最多登記".$max_borrows."本書籍<BR>已經達到登記上限',1)";

		}


		?>




    </script>
</Html>














