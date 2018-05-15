<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> MSG 訊息表
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
   
        
		$user_id       =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:0;
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
		$count        =$_GET['count'];

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
		if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

	//---------------------------------------------------
	//SQL
	//---------------------------------------------------
		$array = array();
		$wnow=date("Y-m-d");
		$sql_tmp = "";
		/*黑名單片段
		$sql = "SELECT black_to
				FROM  `mssr_black_user`
				WHERE black_from= '{$user_id}';";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		if(sizeof($retrun)>0)$sql_tmp= " AND from_id NOT IN(-1";
		foreach($retrun as $key1=>$val1)
		{
			$sql_tmp.=",";
			$sql_tmp.=$val1["black_to"];
		}
		if(sizeof($retrun)>0)$sql_tmp.= ")";
		*/
		
		$sql = "SELECT V1.log_text,
					   V1.from_id,
					   tx_coin,
					   V1.log_id
				FROM
				(
					SELECT user_id,
						   from_id,
						   log_id,
						   log_text,
						   keyin_cdate
						   
					FROM  `mssr_msg_log`
					WHERE user_id= '{$user_id}' AND log_state = '1' ".$sql_tmp."
				)AS V1
				LEFT JOIN mssr_tx_gift_log
				ON mssr_tx_gift_log.msg_id = V1.log_id
				ORDER BY  V1.`keyin_cdate` DESC ";
				
				
		if($count)$sql =$sql."	LIMIT 0 , 1";
		
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		
		
		foreach($retrun as $key1=>$val1)
		{
			$sql = "SELECT name 
					FROM  `member` 
					WHERE uid = ".$val1['from_id'].";";
			$retrun_name = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
			$array[$key1]["from_name"] = $retrun_name[0]["name"];
			$array[$key1]["from_id"] = $val1["from_id"];
			$array[$key1]["text"] = $val1["log_text"];
			
			$sql = "SELECT class.grade AS grade_name,
					   class_name.class_name,
					   school.school_name
					   
							FROM
							(
								SELECT class_code 
								FROM  `student` 
								WHERE uid = ".$val1['from_id']."
								AND '".$wnow."' BETWEEN START AND END
							)AS me
						LEFT JOIN class
						ON class.class_code = me.class_code
						
						LEFT JOIN class_name
						ON class_name.class_category = class.class_category
						AND class_name.classroom = class.classroom
						
						LEFT JOIN semester
						ON semester.semester_code = class.semester_code
						
						LEFT JOIN school
						ON school.school_code = semester.school_code
						
						UNION ALL				
								
						SELECT class.grade AS grade_name,
					    class_name.class_name,
					   school.school_name
							   
						FROM
						(
							SELECT class_code 
							FROM  `teacher` 
							WHERE uid = ".$val1['from_id']."
							AND '".$wnow."' BETWEEN START AND END
						)AS me
						LEFT JOIN class
						ON class.class_code = me.class_code
						
						LEFT JOIN class_name
						ON class_name.class_category = class.class_category
						AND class_name.classroom = class.classroom
						
						LEFT JOIN semester
						ON semester.semester_code = class.semester_code
						
						LEFT JOIN school
						ON school.school_code = semester.school_code";
			$retrun_sq = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
			
			if($array[$key1]["from_id"]!=0)
			{//設定此段化為導入連結
				if(count($retrun_sq) >0)
				$array[$key1]["text"]= str_replace($array[$key1]["from_name"],"<a style='color:#4dc0fc;  cursor:pointer;' onClick='go_home(\"".$retrun_sq[0]['school_name']." ".$retrun_sq[0]['grade_name']."年 ".$retrun_sq[0]['class_name']."班<BR>  ".$array[$key1]["from_name"]."\",".$array[$key1]["from_id"].")'>".$array[$key1]["from_name"]."</a>", $array[$key1]["text"]); 
				else
				$array[$key1]["text"]= str_replace($array[$key1]["from_name"],"<a style='color:#4dc0fc;  cursor:pointer;' onClick='go_home(\"".$array[$key1]["from_name"]."\",".$array[$key1]["from_id"].")'>".$array[$key1]["from_name"]."</a>", $array[$key1]["text"]); 
			}
			$array[$key1]["coin"] = $val1["tx_coin"];
			$array[$key1]["log_id"] = $val1["log_id"];
		}

?>
<!DOCTYPE HTML>
<Html><Head>
    <script src="../js/select_thing.js" type="text/javascript"></script>
    <style>
		table {
			table-layout: fixed;
			
			word-break: break-all;
		}
		td {
			
			overflow:hidden;
			color:#FFF;
			background-color:#333;}
			
		.msg_add_coin{
			position:absolute;
			width:27px;
			height:27px;
			background: url('./img/msg_btn.png') 0 0;
		}
		.msg_add_coin:hover {
    		background: url('./img/msg_btn.png') 0 -27px;
		}
		.msg_del_coin{
			position:absolute;
			width:27px;
			height:27px;
			background: url('./img/msg_btn.png') -27px 0;
		}
		.msg_del_coin:hover {
    		background: url('./img/msg_btn.png') -27px -27px;
		}
		.msg_delet{
			position:absolute;
			width:27px;
			height:27px;
			background: url('./img/msg_btn.png') -54px 0;
		}
		.msg_delet:hover {
    		background: url('./img/msg_btn.png') -54px -27px;
		}
       
	</style>
</Head>
<body>

	<!--==================================================
    html內容
    ====================================================== -->
	<table border="0" cellpadding="0" cellspacing="0" width="457"  style="position:absolute; top:0px; font-size: 16px;">

    <? for($i = 0 ;$i < sizeof($array) ; $i++)
	{ 	
		echo "<tr id='br_".$i."' style=' height:25px;'>";
		
		echo "<td id='text_".$i."' style='width:345px;'>";
		echo $array[$i]["text"];
		echo "</td>";
		echo "<td style='width:10px;'>";
		echo "";
		echo "</td>";
		echo "<td style='width:50px;'>";
		
		if($array[$i]["coin"]>0)
		{
			echo (int)$array[$i]["coin"];
			echo "$";
		}
		echo "</td>";
		echo "<td>";
		echo '<div style="position:absolute;"><a id="btn_'.$i.'"'; 
		if($array[$i]["coin"]>0)echo ' class="msg_add_coin"';
		else echo 'class="msg_delet"';
		echo 'onClick="click_bar('.$array[$i]['log_id'].','.$i.')" style="cursor:pointer; position:absolute; top:-12px; "></a></div>';
		echo "</td>";
		
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
	function click_bar(value,id)
	{
		if(<? echo $count;?>)
		{
			echo("類型1 單一"+value);
			window.parent.set_msg_of(value,"re");
			window.document.getElementById("text_"+id).style.color = "#00FF00";
			window.document.getElementById("btn_"+id).style.display = "none";
			
		}
		else
		{
			echo("類型2 多項"+value);
			window.parent.set_msg_of(value,"none");
			window.document.getElementById("text_"+id).style.color = "#00FF00";
			window.document.getElementById("btn_"+id).style.display = "none";
		}
	
	}
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
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}	
	function go_home(name,id)
	{
		cover("你要前往<BR>"+name+"的書店嗎?",2,function(){window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e36',window.parent.parent.action_on); window.parent.parent.location.href="../bookstore_courtyard/index.php?uid="+id;});

	}
	//---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        
		cover("");
      </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    