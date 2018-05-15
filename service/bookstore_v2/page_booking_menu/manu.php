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
			$array[$key1]["book_name"]=$get_book_info[0]['book_name'];
		}

?>
<!DOCTYPE HTML>
<Html>
<Head>
    <link href="../css/manu.css" rel="stylesheet" type="text/css">
    <script src="../js/select_thing.js" type="text/javascript"></script>
</Head>
<body style="overflow:hidden;">

	<!--==================================================
    html內容
    ====================================================== -->
	<table  width="545"  border="0" cellpadding="0" cellspacing="0">
    <tr style="height:40px;"> 
    <td class="td_line_l_t" style=" width:10%;">販賣人</td>
    <td class="td_line_t"  style=" width:25%;">書籍名稱</td>
    <td class="td_line_t"  style=" width:12%;">預定日期</td>
    <td class="td_line_t"  style=" width:12%;">交易狀態</td>
    <td class="td_line_t"  style=" width:8%;">收入</td>
    </tr>
    <? for($i = 0 ;$i < sizeof($array) ; $i++)
	{ 	
		echo "<tr id='br_".$i."' class='click_br_1' onClick='click_bar(".$i.")' onMouseOver='over_bar(".$i.")'>";
		if( $i+1 == sizeof($array))
		{
			echo "<td class='td_line_l_d'  style='color:#2119b9;' onClick='go_home(\"".$array[$i]["name"]."\",".$array[$i]["id"].")'>";
			echo $array[$i]["name"];
			echo "</td>";
			echo "<td class='td_line_d'>";
			echo $array[$i]["book_name"];
			echo "</td>";
			echo "<td class='td_line_d'>";
			echo $array[$i]["times"];
			echo "</td>";
			echo "<td class='td_line_d'>";
			echo "<a class='".$array[$i]["booking_state_class"]."' onClick=chick_state_help('".$array[$i]["book_name"]."','".$array[$i]["booking_state"]."')>".$array[$i]["booking_state"]."</a>";
			echo "</td>";
			echo "<td class='td_line_r_d'>";
			echo $array[$i]["get_coin"];
			echo "</td>";
		}
		else
		{
			echo "<td class='td_line_l' style='color:#2119b9;' onClick='go_home(\"".$array[$i]["name"]."\",".$array[$i]["id"].")'>";
			echo $array[$i]["name"];
			echo "</td>";
			echo "<td class='td_line'>";
			echo $array[$i]["book_name"];
			echo "</td>";
			echo "<td class='td_line'>";
			echo $array[$i]["times"];
			echo "</td>";
			echo "<td class='td_line'>";
			echo "<a class='".$array[$i]["booking_state_class"]."' onClick=chick_state_help('".$array[$i]["book_name"]."','".$array[$i]["booking_state"]."')>".$array[$i]["booking_state"]."</a>";
			echo "</td>";
			echo "<td class='td_line_r'>";
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
	function go_home(name,id)
	{
		cover("你要前往<BR>"+name+"的書店嗎?",2,function(){window.parent.parent.location.href="../bookstore_courtyard/index.php?uid="+id;});
		
		
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
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    