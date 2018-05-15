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
		$sql = "SELECT `booking_from`,`booking_to`,`book_sid`,`booking_state`,times,e_time,get_coin
				FROM
				(
					SELECT booking_from,
						   booking_to,
						   book_sid,
						   booking_state,
						   LEFT(booking_sdate,10) AS times ,
						   booking_state AS e_time,
						   '100' AS get_coin
					FROM   `mssr_book_booking_log` 
					WHERE  booking_from = {$home_id}
					AND    booking_state != '取消訂閱' 
					
					UNION ALL
				
					SELECT booking_from,
						   booking_to,
						   book_sid,
						   '交易中'AS booking_state,
						   LEFT(keyin_cdate,10) AS times,
						   '交易中' AS e_time,
						   '0' AS get_coin
					FROM   `mssr_book_booking` 
					WHERE  booking_from = {$home_id}
				
				) V1
				ORDER BY `V1`.`times` DESC";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array($page_limit,10),$arry_conn_mssr);


		foreach($retrun as $key1=>$val1)
		{
			$sql = "SELECT name
					FROM `member` 
					WHERE uid = '".$val1["booking_to"]."'";
			$retrun2 = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);	
			
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
			AND `uid` = '".$val1["booking_to"]."' 

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
			AND `uid` = '".$val1["booking_to"]."'

			ORDER BY `grade_name` DESC
			LIMIT 1

			";

				// echo "<pre>".$sql."</pre>";
				// die();


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
			$array[$key1]["name"] = $retrun2[0]["name"];
			$array[$key1]["id"]   = $val1["booking_to"];
			$array[$key1]["times"] = $val1["times"];
			$array[$key1]["booking_state"] = $val1["booking_state"];
			$array[$key1]["get_coin"] = $val1["get_coin"]; //還要書本名稱 & 300收入
			$array[$key1]["booking_state_class"]='';
			if($val1["booking_state"]=="交易中")$array[$key1]["booking_state_class"]='bule';
			//搜尋書籍名稱
			$array_select = array("book_name");
			$get_book_info=get_book_info($conn='',$val1['book_sid'],$array_select,$arry_conn_mssr);
			$array[$key1]["book_name"]=trim($get_book_info[0]['book_name']);
			$array[$key1]["book_sid"]=$val1['book_sid'];
		}


			

?>
<!DOCTYPE HTML>
<Html>
<Head>
    <link href="../css/manu.css?20170324" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
    <script src="../js/select_thing.js" type="text/javascript"></script>
    <style type="text/css">
    	
    </style>
</Head>
<body style="overflow:hidden;">

	<!--==================================================
    html內容
    ====================================================== -->
	<table  width="545"  border="0" cellpadding="0" cellspacing="0">
    <tr style="height:40px;"> 
    <td class="td_line_l_t" style=" width:25%;">將閱讀的書籍</td>
    <td class="td_line_t"  style=" width:10%;">販賣人</td>
    <td class="td_line_t"  style=" width:12%;">預定日期</td>
    <td class="td_line_t"  style=" width:12%;">交易狀態</td>
    <td class="td_line_t"  style=" width:8%;">收入</td>
    </tr>
    <? for($i = 0 ;$i < sizeof($array) ; $i++)
	{ 	
		echo "<tr id='br_".$i."' class='click_br_1' onClick='click_bar(".$i.")' onMouseOver='over_bar(".$i.")'>";
		if( $i+1 == sizeof($array))
		{
			echo "<td class='td_line_l_d' style='color:#2119b9;' onClick='go_book(\"".$array[$i]['school_name']." ".$array[$i]['grade_name']."年 ".$array[$i]['class_name']."班<BR>".$array[$i]["name"]."\",".$array[$i]["id"].",\"".$array[$i]["book_sid"]."\",\"".$array[$i]["book_name"]."\")'>";
			echo $array[$i]["book_name"];
			echo "</td>";
			if($array[$i]["name"]!="")
			echo "<td class='td_line_d_post' style='color:#2119b9;' onClick='go_home(\"".$array[$i]["name"]."\",\"".$array[$i]['school_name']." ".$array[$i]['grade_name']."年 ".$array[$i]['class_name']."班<BR>"."\",".$array[$i]["id"].")'>";
			else
			echo "<td class='td_line_d_post' style='color:#2119b9;' onClick='go_home(\"".$array[$i]["name"]."\",".$array[$i]["id"].")'>";
	
			echo $array[$i]["name"];
			echo "</td>";
			echo "<td class='td_line_d_post'>";
			echo $array[$i]["times"];
			echo "</td>";
			echo "<td class='td_line_d_post'>";
			echo "<a class='".$array[$i]["booking_state_class"]."' onClick=chick_state_help('".$array[$i]["book_name"]."','".$array[$i]["booking_state"]."')>".$array[$i]["booking_state"]."</a>";
			echo "</td>";
			echo "<td class='td_line_r_d_post'>";
			echo $array[$i]["get_coin"];
			echo "</td>";
		}
		else
		{
			echo "<td class='td_line_l' style='color:#2119b9;' onClick='go_book(\"".$array[$i]['school_name']." ".$array[$i]['grade_name']."年 ".$array[$i]['class_name']."班<BR>".$array[$i]["name"]."\",".$array[$i]["id"].",\"".$array[$i]["book_sid"]."\",\"".$array[$i]["book_name"]."\")'>";
			echo $array[$i]["book_name"];
			echo "</td>";
			
			if($array[$i]["name"]!="")
			echo "<td class='td_line_post' style='color:#2119b9;' onClick='go_home(\"".$array[$i]["name"]."\",\"".$array[$i]['school_name']." ".$array[$i]['grade_name']."年 ".$array[$i]['class_name']."班<BR>"."\",".$array[$i]["id"].")'>";
			else
			echo "<td class='td_line_post' style='color:#2119b9;' onClick='go_home(\"".$array[$i]["name"]."\",".$array[$i]["id"].")'>";
			
			echo $array[$i]["name"];
			echo "</td>";
			echo "<td class='td_line_post'>";
			echo $array[$i]["times"];
			echo "</td>";
			echo "<td class='td_line_post'>";
			echo "<a class='".$array[$i]["booking_state_class"]."' onClick=chick_state_help('".$array[$i]["book_name"]."','".$array[$i]["booking_state"]."')>".$array[$i]["booking_state"]."</a>";
			echo "</td>";
			echo "<td class='td_line_r_post'>";
			echo $array[$i]["get_coin"];
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
	function go_book(name,id,bookid,bookname)
	{
		
		var bookstoreRecBtn="<a id='bookstoreRecBtn'>書店推薦</a>";
		var forumBookBtn="<a id='forumBookBtn' style='' >聊書討論</a>";
		var cancel="<a id='cancelBtn' style='position:absolute;right:-15px; top:-15px; cursor:pointer;'><img src='./img/cancel.png'  /></a>"

		console.log(bookname);

		// console.log(window.parent.parent.forum_flag);

		//有聊書權限則也顯示聊書按鈕

		if(window.parent.parent.forum_flag == "y"){
			cover("<span style='font-size:22px;letter-spacing: 1.5px;color:#e13c61;display:block;text-align:center;padding:15px;'>請選擇要前往的位置</span>"+"<span style='font-size:24px;letter-spacing: 1.5px; display:block;text-align:center;padding:5px;padding-bottom:20px;'>"+bookname+"</span>"+bookstoreRecBtn+forumBookBtn+cancel,0);
		}else{
			cover("<span style='font-size:24px;letter-spacing: 1.5px; display:block;text-align:center;padding:5px;padding-bottom:20px;'>"+bookname+"</span>"+bookstoreRecBtn+cancel,0);
		}

		//按下叉叉按鈕跳窗取消
		window.parent.parent.$("#cancelBtn").on('click','',function(){		
			cover("");
		});
		
		//按下書店推薦按鈕.視窗轉跳
		window.parent.parent.$("#bookstoreRecBtn").on('click','',function(){
			window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e44',window.parent.parent.action_on);
			window.parent.parent.location.href="index.php?uid="+id+"&book_sid="+bookid;
		});

		//按下聊書書頁按鈕.跳出聊書視窗.原本跳窗消失

		window.parent.parent.$("#forumBookBtn").on('click','',function(){
			window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e42',window.parent.parent.action_on);
			// window.open("../forum/view/article.php?get_from=1&book_sid="+bookid);
			window.parent.parent.parent.location.href="../forum/view/article.php?get_from=1&book_sid="+bookid;
			cover("");
						
		});

		// cover("你要前往<BR>"+name+"的書店觀看推薦嗎?",2,function(){window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e44',window.parent.parent.action_on);window.parent.parent.location.href="../index.php?uid="+id+"&book_sid="+bookid;});
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
		//click_br_1 : 底色 (黃色) click_br_3 :滑過去的淺藍色 click_br_2 :選取到的顏色(亮藍)
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
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    