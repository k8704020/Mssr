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
		$shop  = array();
		$sql = "SELECT `item_name`,
					   `item_coin`,
					   `item_id`
				FROM `mssr_item`
				";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($retrun as $key1=>$val1)
		{
			$shop[$val1['item_id']]['name'] = $val1['item_name'];
			$shop[$val1['item_id']]['coin'] = $val1['item_coin'];
		}
		
		
		$array = array();
		$sql = "SELECT `tx_item`,
					   `tx_coin`,
					   LEFT(`keyin_cdate`,10) AS keyin_cdate,
					   tx_type
				FROM  
				(
					SELECT `tx_sid`,tx_type
					FROM  `mssr_user_item_log` 
					WHERE  `user_id` = $home_id
					AND  `tx_type` 
					IN 
					(
						'sell',
						'buy'
					)
					ORDER BY  `mssr_user_item_log`.`keyin_cdate` DESC
				)AS A
				LEFT JOIN mssr_tx_sys_log 
				ON mssr_tx_sys_log.tx_sid = A.tx_sid";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array($page_limit,10),$arry_conn_mssr);
		foreach($retrun as $key1=>$val1)
		{
			$array[$key1]["name"] = $shop[abs($val1["tx_item"])]['name'];
			$array[$key1]["id"] = abs($val1["tx_item"]);
			
			$array[$key1]["keyin_cdate"] = $val1["keyin_cdate"];
			if($val1["tx_type"] == "buy")
			{
				$array[$key1]["coin"] = (int)$shop[abs($val1["tx_item"])]['coin'];
				$array[$key1]["coin"] = "花費".$array[$key1]["coin"];
				$array[$key1]["tx_type"] = "購買";
				
			}else if($val1["tx_type"] == "sell")
			{
				$array[$key1]["coin"] = (int)$shop[abs($val1["tx_item"])]['coin']/5;
				$array[$key1]["coin"] = "回收".$array[$key1]["coin"];
				$array[$key1]["tx_type"] = "販賣";
				
			}
		}

?>
<!DOCTYPE HTML>
<Html>
<Head>
    <link href="../css/manu.css?20170323" rel="stylesheet" type="text/css">
    <script src="../js/select_thing.js" type="text/javascript"></script>
</Head>
<body style="overflow:hidden;">

	<!--==================================================
    html內容
    ====================================================== -->
	<table  width="545"  border="0" cellpadding="0" cellspacing="0">
    <tr style="height:40px;"> 
    <td class="td_line_l_t" style=" width:10%;">類型</td>
    <td class="td_line_t"  style=" width:50%;">物品名稱</td>
    <td class="td_line_t"  style=" width:20%;">交易狀態</td>
    <td class="td_line_t"  style=" width:20%;">交易日期</td>
    </tr>
    <? for($i = 0 ;$i < sizeof($array) ; $i++)
	{ 	
		echo "<tr id='br_".$i."' class='click_br_1' onClick='click_bar(".$i.")' onMouseOver='over_bar(".$i.")'>";
		if( $i+1 == sizeof($array))
		{
			echo "<td class='td_line_l_d'>";
			echo $array[$i]["tx_type"];
			echo "</td>";
			echo "<td class='td_line_d' style='color:#2119b9;' onClick=\"cover('<img src=\'./bookstore_courtyard/img/".$array[$i]["id"].".png\' style=\'max-height:80px; max-width=80px;\'>".$array[$i]["name"]."',1)\">";
			echo $array[$i]["name"];
			echo "</td>";
			echo "<td class='td_line_d'>";
			echo $array[$i]["coin"];
			echo "</td>";
			echo "<td class='td_line_r_d'>";
			echo $array[$i]["keyin_cdate"];
			echo "</td>";
		}
		else
		{
			echo "<td class='td_line_l'>";
			echo $array[$i]["tx_type"];
			echo "</td>";
			echo "<td class='td_line' style='color:#2119b9;' onClick=\"cover('<img src=\'./bookstore_courtyard/img/".$array[$i]["id"].".png\' style=\'max-height:80px; max-width=80px;\'>".$array[$i]["name"]."',1)\">";
			echo $array[$i]["name"];
			echo "</td>";
			echo "<td class='td_line'>";
			echo $array[$i]["coin"];
			echo "</td>";
			echo "<td class='td_line_r'>";
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
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    