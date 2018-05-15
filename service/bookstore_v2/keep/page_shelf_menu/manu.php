<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 推薦狀況表
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
		require_once(str_repeat("../",3)."/inc/get_black_book_info/code.php");
	
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
		$forum_flag = $_SESSION["forum_flag"];
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
   
       // $user_id        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$user_id        =(isset($_GET['uid']))?(int)$_GET['uid']:0;
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
		$i_s = (int)$_GET['i_s'];
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
		if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");
		if($i_s==0)$auth_open_publish=1;//非學生設為預設條件
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
		
		$auth_open_publish=(isset($_GET['auth_open_publish']))?$_GET['auth_open_publish']:0;
		$page = (int)$_GET["page"];
		$auth_read_opinion_limit_day = (int)$_GET["auth_read_opinion_limit_day"];
		$page_limit = ($page-1 )*10;
		
		$p[1] = "<img src='img/p1.png' style='position:absolute; top:0px;'>";
		$p[2] = "<img src='img/p2.png' style='position:absolute; top:0px;'>";
		$p[3] = "<img src='img/p3.png' style='position:absolute; top:0px;'>";
		$p[4] = "<img src='img/p4.png' style='position:absolute; top:0px;'>";
	//---------------------------------------------------
	//SQL
	//---------------------------------------------------
		$array = array();
		//搜尋推薦資訊
		$sql ="";
	//==================================上架條件1:  兩項推薦才可上架==============================================================		
	if($auth_open_publish <= 1)
	{	
		$count=0;
		$sql = "SELECT 
					`book_sid`,
					`rec_stat_cno`,
					`rec_draw_cno`,
					`rec_text_cno`,
					`rec_record_cno`
				FROM mssr_rec_book_cno
				WHERE `user_id` ='".$user_id."'
				AND
				book_on_shelf_state != '上架'
				ORDER BY  `mssr_rec_book_cno`.`keyin_cdate` DESC ";
	}
	//==================================上架條件2:  老師同意才可上架==============================================================		
	else if($auth_open_publish == 2)
	{
		
		$sql = "SELECT 
					`book_sid`,
					`rec_stat_cno`,
					`rec_draw_cno`,
					`rec_text_cno`,
					`rec_record_cno`
				FROM mssr_rec_book_cno
				WHERE `user_id` ='".$user_id."'
				AND	book_on_shelf_state != '上架'
				AND has_publish = '可' 
				ORDER BY  `mssr_rec_book_cno`.`keyin_cdate` DESC ";
	}
	//==================================上架條件3:  老師評分4以上才可上架==============================================================
	else if($auth_open_publish == 3)
	{
		$sql = "SELECT 
					`book_sid`,
					`rec_stat_cno`,
					`rec_draw_cno`,
					`rec_text_cno`,
					`rec_record_cno`
				FROM mssr_rec_book_cno
				WHERE `user_id` ='".$user_id."'
				AND
				book_on_shelf_state != '上架'
				ORDER BY  `mssr_rec_book_cno`.`keyin_cdate` DESC ";
	}



		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array($page_limit , 10),$arry_conn_mssr);
		foreach($retrun as $key1=>$val1)
		{
			$array[$key1]["stat_txt"] = "";
			$array[$key1]["text_txt"] = "";
			$array[$key1]["draw_txt"] = "";
			$array[$key1]["record_txt"] = "";
			$array[$key1]["retrun_forum"] = "";
			$array[$key1]["stat_s"] = 0;
			$array[$key1]["text_s"] = 0;
			$array[$key1]["draw_s"] = 0;
			$array[$key1]["record_s"] = 0;
			$array[$key1]["has_black"] = false;
			
			//搜尋書籍名稱
			$array_select = array("book_name","book_isbn_10","book_isbn_13","book_sid");
			$get_book_info=get_book_info($conn='',$val1['book_sid'],$array_select,$arry_conn_mssr);
			if($auth_open_publish == 3 )$array[$key1]["open"] = 0;
			
			//檢查是否禁用書
			$rs_book_sid=$get_book_info[0]["book_sid"];
			$rs_book_isbn_10=$get_book_info[0]["book_isbn_10"];
			$rs_book_isbn_13=$get_book_info[0]["book_isbn_13"];
			
			
			$book_nonumbering="'{$rs_book_sid}'";
			if($rs_book_isbn_10!=='')$book_nonumbering.=",'{$rs_book_isbn_10}'";
			if($rs_book_isbn_13!=='')$book_nonumbering.=",'{$rs_book_isbn_13}'";
	
			$get_black_book_info=get_black_book_info($conn_mssr,$book_nonumbering,$arry_conn_mssr);
			
			if(!count($get_black_book_info))
			{					
				//$has_find=true;
			}else
			{
				$array[$key1]["has_black"]=true;	
			}
			
			
			//搜尋書籍審核
				$book_verified = 1;
				if($val1['book_sid'][2] == "u")
				{
					$sql =	"SELECT `book_verified`
							FROM `mssr_book_unverified`
							WHERE `book_sid` = '".$val1['book_sid']."'";
					$book_tmpmp = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);	
					$book_verified = $book_tmpmp[0]["book_verified"];
				}
				
			//教師評論TX
			$sql = "
					SELECT  `book_sid`,
							`comment_type`,
							MAX(keyin_cdate),
							`comment_score`,
							`comment_content`,
							`has_del_rec`,
							`rec_sid`
					FROM `mssr_rec_comment_log`
					WHERE comment_to = ".$user_id."
					AND book_sid = '".$val1['book_sid']."'
					AND comment_type ='text'
						
					GROUP BY `book_sid`,`comment_type`";
			$retrun2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);	
			foreach($retrun2 as $key2=>$val2)
			{
				if($val2["has_del_rec"] == "有"&&$val2["comment_type"] == "text")
				{
					$sql = "SELECT rec_state 
							FROM  `mssr_rec_book_text_log`
							where user_id = ".$user_id."
							AND   book_sid = '".$val1['book_sid']."'
							ORDER BY keyin_cdate DESC
							";
					$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);	
					if($retrun3[0]["rec_state"] == "隱藏")$array[$key1]["text_txt"] .= "刪除";
					else if($val2["comment_type"] == "text")$array[$key1]["text_txt"] .= "！";
				}else if($val2["comment_type"] == "text")$array[$key1]["text_txt"] .= "！";
				if($auth_open_publish == 3 && $val2["comment_score"]>=4)$array[$key1]["open"] = 1;
				$array[$key1]["text_s"] = $val2["comment_score"];
			}
			
			//教師評論DR
			$sql = "
					SELECT  `book_sid`,
							`comment_type`,
							MAX(keyin_cdate),
							`comment_score`,
							`comment_content`,
							`has_del_rec`,
							`rec_sid`
					FROM `mssr_rec_comment_log`
					WHERE comment_to = ".$user_id."
					AND book_sid = '".$val1['book_sid']."'
					AND comment_type ='draw'
					
					GROUP BY `book_sid`,`comment_type`";
			$retrun2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);	
			foreach($retrun2 as $key2=>$val2)
			{
				if($val2["has_del_rec"] == "有"&&$val2["comment_type"] == "draw")
				{
					
					$sql = "SELECT rec_state 
							FROM  `mssr_rec_book_draw_log`
							where user_id = ".$user_id."
							AND   book_sid = '".$val1['book_sid']."'
							ORDER BY keyin_cdate DESC
							";
					$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);	
					if($retrun3[0]["rec_state"] == "隱藏")$array[$key1]["draw_txt"] .= "刪除";
					else if($val2["comment_type"] == "draw")$array[$key1]["draw_txt"] .= "！";
					
				}else if($val2["comment_type"] == "draw")$array[$key1]["draw_txt"] .= "！";
			
				if($auth_open_publish == 3 && $val2["comment_score"]>=4)$array[$key1]["open"] = 1;
				$array[$key1]["draw_s"] = $val2["comment_score"];
			}
			
			//教師評論RE
			$sql = "SELECT  `book_sid`,
							`comment_type`,
							MAX(keyin_cdate),
							`comment_score`,
							`comment_content`,
							`has_del_rec`,
							`rec_sid`
					FROM `mssr_rec_comment_log`
					WHERE comment_to = ".$user_id."
					AND book_sid = '".$val1['book_sid']."'
					AND comment_type ='record'
					GROUP BY `book_sid`,`comment_type`";
			$retrun2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);	
			foreach($retrun2 as $key2=>$val2)
			{
				if($val2["has_del_rec"] == "有"&&$val2["comment_type"] == "record")
				{
					
					$sql = "SELECT rec_state 
							FROM  `mssr_rec_book_record_log`
							where user_id = ".$user_id."
							AND   book_sid = '".$val1['book_sid']."'
							ORDER BY keyin_cdate DESC
							";
					$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);	
					if($retrun3[0]["rec_state"] == "隱藏")$array[$key1]["record_txt"] .= "刪除";
					else if($val2["comment_type"] == "record")$array[$key1]["record_txt"] .= "！";
					
				}else if($val2["comment_type"] == "record")$array[$key1]["record_txt"] .= "！";	
				
				if($auth_open_publish == 3 && $val2["comment_score"]>=4)$array[$key1]["open"] = 1;
				$array[$key1]["record_s"] = $val2["comment_score"];
			}
			
			$count = 0 ; 
			if($val1["rec_stat_cno"] >= 1 && $array[$key1]["stat_txt"]!="刪除")
			{
				$array[$key1]["stat_txt"] .= "○";
				$count++;
			}
			if($val1["rec_text_cno"] >= 1 && $array[$key1]["text_txt"]!="刪除")
			{
				$array[$key1]["text_txt"] .= "○";
				$count++;
			}
			if($val1["rec_draw_cno"] >= 1 && $array[$key1]["draw_txt"]!="刪除")
			{
				$array[$key1]["draw_txt"] .= "○";
				$count++;
			}
			if($val1["rec_record_cno"] >= 1 && $array[$key1]["record_txt"]!="刪除")
			{
				$array[$key1]["record_txt"] .= "○";
				$count++;
			}
			
			//聊書的部分
			$sql_forum = "
			SELECT  count(1) AS count
			FROM `mssr_forum`.`mssr_forum_article`
			
			LEFT JOIN `mssr_forum`.`mssr_forum_article_book_rev`
			ON `mssr_forum_article_book_rev`.`article_id` = `mssr_forum_article`.`article_id`
			
			WHERE `mssr_forum_article`.`user_id`={$user_id}
			AND `mssr_forum_article_book_rev`.`book_sid` = '".$val1['book_sid']."'";
			
			$retrun_forum = db_result($conn_type='pdo',$conn_mssr,$sql_forum,$arry_limit=array(0,1),$arry_conn_mssr);
			
			if($retrun_forum[0]["count"] >=1)
			{
				$array[$key1]["retrun_forum"] .= "○";
				$count++;
			}
			
			//計算該筆是否可選擇
			if($auth_open_publish != 3)
			{
				if($count >= 2 || $auth_open_publish == 2)
				{$array[$key1]["open"] = 1;}else{$array[$key1]["open"] = 0;}
			}
			
			//書名異常處理
			if($book_verified == 2 || $array[$key1]["has_black"])
			{
				$array[$key1]["open"] = 0;
				$get_book_info[0]['book_name']="問題書籍! 已鎖定不可使用";
			}
			
			$array[$key1]["book_name"]=$get_book_info[0]['book_name'];
			$array[$key1]["book_sid"]=$val1['book_sid'];
			$array[$key1]["user_id"]=$val1['user_id'];
			$array[$key1]["keyin_cdate"]=$val1['keyin_cdate'];
			$array[$key1]["time"]=$val1['time'];
			
		}

?>
<!DOCTYPE HTML>
<Html>
<Head>
    <script src="../js/select_thing.js" type="text/javascript"></script>
    <link href="../css/manu.css" rel="stylesheet" type="text/css">
</Head>
<body style="overflow:hidden;">

	<!--==================================================
    html內容
    ====================================================== -->
    
    <? if($forum_flag){?>
	<table  width="490"  border="0" cellpadding="0" cellspacing="0">
    <tr style="height:40px;"> 
    <td class="td_line_l_t" style=" width:160px; ">書籍名稱</td>
    <td class="td_line_t"  style=" width:48px;">評星</td>
    <td class="td_line_t"  style=" width:48px;">繪圖</td>
    <td class="td_line_t"  style="width:48px;">文字</td>
    <td class="td_line_t"  style="width:48px;">錄音</td>
    <td class="td_line_t"  style="width:48px;">聊書</td>
	<? }else{?>
    <table  width="490"  border="0" cellpadding="0" cellspacing="0">
    <tr style="height:40px;"> 
    <td class="td_line_l_t" style=" width:200px; ">書籍名稱</td>
    <td class="td_line_t"  style=" width:50px;">評星</td>
    <td class="td_line_t"  style=" width:50px;">繪圖</td>
    <td class="td_line_t"  style="width:50px;">文字</td>
    <td class="td_line_t"  style="width:50px;">錄音</td>
    
	<? } ?>
    </tr>
    <? for($i = 0 ;$i < sizeof($array) ; $i++)
	{ 	
		echo "<tr id='br_".$i."' class='click_br_".$array[$i]['open']."' onClick='click_bar(".$i.")' onMouseOver='over_bar(".$i.")'>";
		if( $i+1 == sizeof($array))
		{
			echo "<td class='td_line_l_d'>";
			echo $array[$i]["book_name"];
			echo "</td>";
			
			echo "<td align='center' class='td_line_d' style='position:relative'>";
			if($array[$i]["stat_txt"]=="○")echo $p[3];
			echo "</td>";
			
			if($array[$i]["draw_s"]>0) echo "<td align='left' class='td_line_d' style='position:relative'>";
			else echo"<td align='center' class='td_line_d' style='position:relative'>";
			if($array[$i]["draw_s"]>0)echo $array[$i]["draw_s"]."分";
			if($array[$i]['open']==0)
			{
				if($array[$i]["draw_txt"]=="○")echo $p[3];
				else if($array[$i]["draw_txt"]=="！○")echo $p[2];
				else if($array[$i]["draw_txt"]=="刪除")echo $p[4];
			}else
			{
				if($array[$i]["draw_txt"]=="○")echo $p[3];
				else if($array[$i]["draw_txt"]=="！○")echo $p[2];
				else if($array[$i]["draw_txt"]=="刪除")echo $p[4];
			}
			echo "</td>";
			
			if($array[$i]["text_s"]>0) echo "<td align='left' class='td_line_d' style='position:relative'>";
			else echo"<td align='center' class='td_line_d' style='position:relative'>";
			if($array[$i]["text_s"]>0)echo $array[$i]["text_s"]."分";
			if($array[$i]['open']==0)
			{
				if($array[$i]["text_txt"]=="○")echo $p[3];
				else if($array[$i]["text_txt"]=="！○")echo $p[2];
				else if($array[$i]["text_txt"]=="刪除")echo $p[4];
			}else
			{
				if($array[$i]["text_txt"]=="○")echo $p[3];
				else if($array[$i]["text_txt"]=="！○")echo $p[2];
				else if($array[$i]["text_txt"]=="刪除")echo $p[4];
			}
			echo "</td>";
			
			if($array[$i]["record_s"]>0) echo "<td align='left' class='td_line_d' style='position:relative'>";
			else echo "<td align='center' class='td_line_d' style='position:relative'>";
			if($array[$i]["record_s"]>0)echo $array[$i]["record_s"]."分";
			if($array[$i]['open']==0)
			{
				if($array[$i]["record_txt"]=="○")echo $p[3];
				else if($array[$i]["record_txt"]=="！○")echo $p[2];
				else if($array[$i]["record_txt"]=="刪除")echo $p[4];
			}else
			{
				if($array[$i]["record_txt"]=="○")echo $p[3];
				else if($array[$i]["record_txt"]=="！○")echo $p[2];
				else if($array[$i]["record_txt"]=="刪除")echo $p[4];
			}
			if($forum_flag)
			{ 
				echo "<td align='center' class='td_line_d' style='position:relative'>";
				if($array[$i]["retrun_forum"]=="○")echo $p[3];
				echo "</td>";
				
			}
			
			
			echo "</td>";
		}
		else
		{
			echo "<td class='td_line_l'>";
			echo $array[$i]["book_name"];
			echo "</td>";
			
			echo "<td align='center' class='td_line' style='position:relative'>";
			if($array[$i]["stat_txt"]=="○")echo $p[3];
			echo "</td>";
			
			if($array[$i]["draw_s"]>0) echo "<td align='left' class='td_line' style='position:relative'>";
			else echo"<td align='center' class='td_line' style='position:relative'>";
			if($array[$i]["draw_s"]>0)echo $array[$i]["draw_s"]."分";
			if($array[$i]['open']==0)
			{
				if($array[$i]["draw_txt"]=="○")echo $p[3];
				else if($array[$i]["draw_txt"]=="！○")echo $p[2];
				else if($array[$i]["draw_txt"]=="刪除")echo $p[4];
			}else
			{
				if($array[$i]["draw_txt"]=="○")echo $p[3];
				else if($array[$i]["draw_txt"]=="！○")echo $p[2];
				else if($array[$i]["draw_txt"]=="刪除")echo $p[4];
			}
			echo "</td>";
			
			if($array[$i]["text_s"]>0) echo "<td align='left' class='td_line' style='position:relative'>";
			else echo"<td align='center' class='td_line' style='position:relative'>";
			if($array[$i]["text_s"]>0)echo $array[$i]["text_s"]."分";
			if($array[$i]['open']==0)
			{
				if($array[$i]["text_txt"]=="○")echo $p[3];
				else if($array[$i]["text_txt"]=="！○")echo $p[2];
				else if($array[$i]["text_txt"]=="刪除")echo $p[4];
			}else
			{
				if($array[$i]["text_txt"]=="○")echo $p[3];
				else if($array[$i]["text_txt"]=="！○")echo $p[2];
				else if($array[$i]["text_txt"]=="刪除")echo $p[4];
			}
			echo "</td>";
			
			if($array[$i]["record_s"]>0) echo "<td align='left' class='td_line' style='position:relative'>";
			else echo"<td align='center' class='td_line' style='position:relative'>";
			if($array[$i]["record_s"]>0)echo $array[$i]["record_s"]."分";
			if($array[$i]['open']==0)
			{
				if($array[$i]["record_txt"]=="○")echo $p[3];
				else if($array[$i]["record_txt"]=="！○")echo $p[2];
				else if($array[$i]["record_txt"]=="刪除")echo $p[4];
			}else
			{
				if($array[$i]["record_txt"]=="○")echo $p[3];
				else if($array[$i]["record_txt"]=="！○")echo $p[2];
				else if($array[$i]["record_txt"]=="刪除")echo $p[4];
			}
			
			
			if($forum_flag)
			{ 
				echo "<td align='center' class='td_line' style='position:relative'>";
				if($array[$i]["retrun_forum"]=="○")echo $p[3];
				echo "</td>";
				
			}
			
			echo "</td>";
		}
	
		echo "</tr>";
	 } ?>
    
    </table>



    <script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var on_chick = -1;
	var book_sid = new Array();
	var btn_open = new Array();
	
	var array_data = <? echo json_encode($array,1); ?>;
	
	for(var key in array_data)
	{
		btn_open[key] = array_data[key].open;
		book_sid[key] = array_data[key].book_sid;
	}
	
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	
	//點擊欄位事件ceffb7
	function click_bar(value)
	{
		if(btn_open[value]==0)return false;
		for(var i =0 ; i < <? echo sizeof($array);?> ; i++)
		{
			window.document.getElementById("br_"+i).className = 'click_br_'+btn_open[i];
		}
		window.document.getElementById("br_"+value).className = 'click_br_2';
		on_chick = value;
		window.parent.document.getElementById("set_btn").style.display = "block";
		echo("選擇到的書籍SID 為->"+book_sid[on_chick]);
		window.parent.chick_sid = book_sid[on_chick];
	}
	function over_bar(value)
	{
		if(btn_open[value]==0)return false;
		for(var i =0 ; i < <? echo sizeof($array);?> ; i++)
		{
			if(window.document.getElementById("br_"+i).className != 'click_br_2')window.document.getElementById("br_"+i).className = 'click_br_'+btn_open[i];
		}
		if(window.document.getElementById("br_"+value).className != 'click_br_2' && window.document.getElementById("br_"+value).className != 'click_br_0')window.document.getElementById("br_"+value).className = 'click_br_3';
	}
	//cover
	function cover(text,type)
	{
		window.parent.cover(text,type);
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}	
	//---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

       
		
	cover("");
    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    