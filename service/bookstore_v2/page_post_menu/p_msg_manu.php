<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 訂閱狀況表
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
        require_once(str_repeat("../",3)."/config/config.php");
		require_once(str_repeat("../",3)."/inc/get_book_info/code.php");

		 //外掛函式檔
		$funcs=array(
					APP_ROOT.'inc/code',
					APP_ROOT.'lib/php/db/code',
					);
		func_load($funcs,true);


		//清除並停用BUFFER
		@ob_end_clean();

		//建立連線 user
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

    ///---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        $user_id        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$home_id        =(isset($_GET['uid']))?(int)$_GET['uid']:$_SESSION['uid'];
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
		if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
		$page = (int)$_GET["page"];
		$page_limit = ($page-1 )*10;
	//---------------------------------------------------
	//SQL
	//---------------------------------------------------
		$array = array();
		$wnow=date("Y-m-d");
		$sql = "SELECT `from_id`,
					   `log_text`,
					   `log_state`,
					   LEFT(`keyin_cdate`,10) AS keyin_cdate
				FROM  `mssr_msg_log`
				WHERE  `user_id` = $home_id
				ORDER BY  `mssr_msg_log`.`keyin_cdate` DESC
				";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array($page_limit,10),$arry_conn_mssr);
		foreach($retrun as $key1=>$val1)
		{
			$sql = "SELECT name
					FROM  `member`
					WHERE uid = ".$val1['from_id'].";";
			$retrun_name = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);

			$sql = "

			SELECT
			IFNULL((
				SELECT `user`.`school`.`school_name` 
				FROM `user`.`member_school` 
				INNER JOIN `user`.`school` ON 
				`user`.`member_school`.`school_code`=`user`.`school`.`school_code`
				WHERE 1=1
				AND `user`.`member_school`.`uid` = `user`.`member`.`uid`
				AND NOW()>=`user`.`member_school`.`start`
				AND `user`.`member_school`.`end` = '0000-00-00'
				LIMIT 1
			),'') AS `school_name`,

			IFNULL((

				SELECT `user`.`class`.`grade`
				FROM `user`.`student` 
				INNER JOIN `user`.`class` ON 
				`user`.`student`.`class_code`=`user`.`class`.`class_code`
				WHERE 1=1
				AND `user`.`student`.`uid` = `user`.`member`.`uid`
				AND NOW() BETWEEN `user`.`student`.`start` AND `user`.`student`.`end` 
				ORDER BY `user`.`student`.`end` DESC
				LIMIT 1
			),'') AS `grade_name`,

			IFNULL((

				SELECT `class_name`
				FROM `user`.`student` 
				INNER JOIN `user`.`class` ON 
				`user`.`student`.`class_code`=`user`.`class`.`class_code`

				INNER JOIN `user`.`class_name` ON 
				`user`.`class`.`classroom`=`user`.`class_name`.`classroom`
				WHERE 1=1
				AND `user`.`student`.`uid` = `user`.`member`.`uid`
				AND NOW() BETWEEN `user`.`student`.`start` AND `user`.`student`.`end` 
				AND `user`.`class_name`.`class_category` = `user`.`class`.`class_category`
				ORDER BY `user`.`student`.`end` DESC
				LIMIT 1
			),'') AS `class_name`

			FROM `user`.`member`
			WHERE 1=1
			AND `uid` = ".$val1['from_id']." 

			UNION 

			SELECT

			IFNULL((
				SELECT `user`.`school`.`school_name` 
				FROM `user`.`member_school` 
				INNER JOIN `user`.`school` ON 
				`user`.`member_school`.`school_code`=`user`.`school`.`school_code`
				WHERE 1=1
				AND `user`.`member_school`.`uid` = `user`.`member`.`uid`
				AND NOW()>=`user`.`member_school`.`start`
				AND `user`.`member_school`.`end` = '0000-00-00'
				LIMIT 1
			),'') AS `school_name`,

			IFNULL((

				SELECT `user`.`class`.`grade`
				FROM `user`.`teacher` 
				INNER JOIN `user`.`class` ON 
				`user`.`teacher`.`class_code`=`user`.`class`.`class_code`
				WHERE 1=1
				AND `user`.`teacher`.`uid` = `user`.`member`.`uid`
				AND NOW() BETWEEN `user`.`teacher`.`start` AND `user`.`teacher`.`end` 
				ORDER BY `user`.`teacher`.`end` DESC
				LIMIT 1
			),'') AS `grade_name`,

			IFNULL((

				SELECT `class_name`
				FROM `user`.`teacher` 
				INNER JOIN `user`.`class` ON 
				`user`.`teacher`.`class_code`=`user`.`class`.`class_code`

				INNER JOIN `user`.`class_name` ON 
				`user`.`class`.`classroom`=`user`.`class_name`.`classroom`
				WHERE 1=1
				AND `user`.`teacher`.`uid` = `user`.`member`.`uid`
				AND NOW() BETWEEN `user`.`teacher`.`start` AND `user`.`teacher`.`end` 
				AND `user`.`class_name`.`class_category` = `user`.`class`.`class_category`
				ORDER BY `user`.`teacher`.`end` DESC
				LIMIT 1
			),'') AS `class_name`

			FROM `user`.`member`
			WHERE 1=1
			AND `uid` = ".$val1['from_id']."

			ORDER BY `grade_name` DESC
			LIMIT 1

			";

			$retrun_sq = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
			if(count($retrun_sq) > 0)
			{
				$array[$key1]["class_name"] = $retrun_sq[0]["class_name"];
				$array[$key1]["grade_name"]   = $retrun_sq[0]["grade_name"];
				$array[$key1]["school_name"] = $retrun_sq[0]["school_name"];
			}else
			{
				$array[$key1]["class_name"] = "";
				$array[$key1]["grade_name"]   = "";
				$array[$key1]["school_name"] = "";
			}
			$array[$key1]["from_name"] = $retrun_name[0]["name"];
			$array[$key1]["from_id"] = $val1["from_id"];
			$array[$key1]["text"] = $val1["log_text"];
			$array[$key1]["text_"] = $val1["log_text"];

			if($array[$key1]["from_id"]!=0)
			{//設定此段化為導入連結
				if(count($retrun_sq) >0)
				$array[$key1]["text"]= str_replace($array[$key1]["from_name"],"<a style='color:#2119b9;  cursor:pointer;' onClick='go_home(\"".$array[$key1]["from_name"]."\",\"".$retrun_sq[0]['school_name']." ".$retrun_sq[0]['grade_name']."年 ".$retrun_sq[0]['class_name']."班<BR>,"."\",".$array[$key1]["from_id"].")'>".$array[$key1]["from_name"]."</a>", $array[$key1]["text"]);
				else
				$array[$key1]["text"]= str_replace($array[$key1]["from_name"],"<a style='color:#2119b9;  cursor:pointer;' onClick='go_home(\"".$array[$key1]["from_name"]."\",".$array[$key1]["from_id"].")'>".$array[$key1]["from_name"]."</a>", $array[$key1]["text"]);
			}
			if(isset($val1["log_id"]))$array[$key1]["log_id"] = $val1["log_id"];
			if($val1["log_state"] == 1)
			$array[$key1]["log_state"] = "<a style='color:#cd2525;  cursor:pointer;' onClick='cover(\"請至書店左上角讀取訊息列\",1)'>未讀</a>";
			else $array[$key1]["log_state"] = "已讀";
			$array[$key1]["keyin_cdate"] = $val1["keyin_cdate"];
		}

?>
<!DOCTYPE HTML>
<Html>
<Head>
    <link href="../css/manu.css?20170324" rel="stylesheet" type="text/css">
    <script src="../js/select_thing.js" type="text/javascript"></script>
</Head>
<body style="overflow:hidden;">

	<!--==================================================
    html內容
    ====================================================== -->
	<table  width="545"  border="0" cellpadding="0" cellspacing="0">
    <tr style="height:40px;">
    <td class="td_line_l_t" style=" width:68%;">訊息內容</td>
    <td class="td_line_t"  style=" width:12%;">狀態</td>
    <td class="td_line_t"  style=" width:20%;">訊息時間</td>
    </tr>
    <? for($i = 0 ;$i < sizeof($array) ; $i++)
	{
		echo "<tr id='br_".$i."' class='click_br_1' onClick='click_bar(".$i.")' onMouseOver='over_bar(".$i.")'>";
		if( $i+1 == sizeof($array))
		{
			echo "<td class='td_line_l_d' >";
			echo "<img src='./img/take_btn.png' style='height:18px;' onClick='cover(\"".$array[$i]["text_"]."\",1)'>";
			echo $array[$i]["text"];
			echo "</td>";
			echo "<td class='td_line_d_post'>";
			echo $array[$i]["log_state"];
			echo "</td>";
			echo "<td class='td_line_r_d_post'>";
			echo $array[$i]["keyin_cdate"];
			echo "</td>";
		}
		else
		{
			echo "<td class='td_line_l' >";
			echo "<img src='./img/take_btn.png' style='height:18px;' onClick='cover(\"".$array[$i]["text_"]."\",1)'>";
			echo $array[$i]["text"];
			echo "</td>";
			echo "<td class='td_line_post'>";
			echo $array[$i]["log_state"];
			echo "</td>";
			echo "<td class='td_line_r_post'>";
			echo $array[$i]["keyin_cdate"];
			echo "</td>";
		}

		echo "</tr>";
	 } ?>

    </table>



    <script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------

	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------

	//點擊欄位事件ceffb7
	function click_bar(value)
	{

		for(var i =0 ; i < <? echo sizeof($array);?> ; i++)
		{
			window.document.getElementById("br_"+i).className = 'click_br_1';
		}
		window.document.getElementById("br_"+value).className = 'click_br_2';
	}
	function go_home(name,school,id)
	{
		var bookStoreBtn="<a id='bookstoreBtn'>書店</a>";
		var forumBtn="<a id='forumBtn' style='' >聊書</a>";
		var cancel="<a id='cancelBtn' style='position:absolute;right:-15px; top:-15px; cursor:pointer;'><img src='./img/cancel.png'  /></a>"

		console.log(name);
		console.log(id);

		//有聊書權限則也顯示聊書按鈕
		if(window.parent.parent.forum_flag == "y"){
			cover("<span style='font-size:22px;letter-spacing: 1.5px;color:#e13c61;display:block;text-align:center;padding:15px;'>請選擇要前往的位置</span>"+
				"<span style='font-size:24px;letter-spacing: 1.5px; display:block;text-align:center;padding:5px;'>"+name+"</span>"+
				"<span style='font-size:20px;letter-spacing: 1.5px; display:block;text-align:center;padding:5px;padding-bottom:20px;'>"+school+"</span>"+bookStoreBtn+forumBtn+cancel,0);
		}else{
			cover(
				"<span style='font-size:24px;letter-spacing: 1.5px; display:block;text-align:center;padding:5px;'>"+name+"</span>"+
				"<span style='font-size:20px;letter-spacing: 1.5px; display:block;text-align:center;padding:5px;padding-bottom:20px;'>"+school+"</span>"+bookStoreBtn+cancel,0);
		}

		//按下叉叉按鈕跳窗取消

		window.parent.parent.$("#cancelBtn").on('click','',function(){		
			cover("");
		});

		
		//按下書店按鈕.視窗轉跳
		window.parent.parent.$("#bookstoreBtn").on('click','',function(){
			console.log(123);
			window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e34',window.parent.parent.action_on);
			window.parent.parent.location.href="bookstore_courtyard/index.php?uid="+id;
		});

		//按下聊書按鈕.跳出聊書視窗.原本跳窗消失

		window.parent.parent.$("#forumBtn").on('click','',function(){
			console.log(234);
			window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e41',window.parent.parent.action_on);
			// window.open("../forum/view/user.php?user_id="+id+"&tab=1");
			window.parent.parent.parent.location.href="../forum/view/user.php?user_id="+id+"&tab=1";
			cover("");					
		});

		// cover("你要前往<BR>"+name+"的書店嗎?",2,function(){
		// 	window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e34',window.parent.parent.action_on);
		// 	window.parent.parent.location.href="../bookstore_courtyard/index.php?uid="+id;}
		// 	);
		
	}
	//cover
	function cover(text,type,proc)
	{

		window.parent.cover(text,type);
		if(type == 2)
		{
			delayExecute(proc);
		}
	}
	/*cover 啟用器的用法
	 cover("這嘎");
	 cover("這嘎",1);
	 cover("這嘎",2,function(){echo("哈哈");});
	*/
	//cover 點選器
	function delayExecute(proc) {
		var x = 100;
		var hnd = window.setInterval(function () {
			if(window.parent.parent.cover_click ==1 )
			{//點選確定的狀況
				window.parent.parent.cover_click = -1;
				window.parent.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選確定");
				proc();
				cover("");
			}
			else if(window.parent.parent.cover_click ==0 )
			{//點選取消的狀況
				window.parent.parent.cover_click = -1;
				window.parent.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選取消");
				cover("");
			}
		}, x);
	}
	function over_bar(value)
	{

		for(var i =0 ; i < <? echo sizeof($array);?> ; i++)
		{
			if(window.document.getElementById("br_"+i).className != 'click_br_2')window.document.getElementById("br_"+i).className = 'click_br_1';
		}
		if(window.document.getElementById("br_"+value).className != 'click_br_2' && window.document.getElementById("br_"+value).className != 'click_br_0')window.document.getElementById("br_"+value).className = 'click_br_3';
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}
	//點交易中
	function chick_state_help(book_name,state)
	{
		if(state == "交易中")
		cover("需要實際去看並登記<BR>["+book_name+"]<BR>才可以完成交易喔",1);
	}
	//---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------


		cover("");
    </script>
</Html>














