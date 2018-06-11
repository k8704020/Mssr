<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,觀測所 -> 推薦內容率
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
		require_once(str_repeat("../",5)."inc/get_book_info/code.php");

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'lib/php/db/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();
		

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
    
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
        //GET
		//GET
        $user_id     =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:die("嗯?");
        $ramge    =(isset($_GET['ramge']))?mysql_prep($_GET['ramge']):die("嗯?");
        $time   =(isset($_GET['time']))?mysql_prep($_GET['time']):die("嗯?");
		
		
		$class_code   =(isset($_GET['class_code']))?mysql_prep($_GET['class_code']):die("嗯?");
		$school_code  =(isset($_GET['school_code']))?mysql_prep($_GET['school_code']):die("嗯?");
		$grade_code   =(isset($_GET['grade_code']))?mysql_prep($_GET['grade_code']):die("嗯?");
		
		//echo "<pre>";print_r($_GET);echo '</pre>';
		//外掛時間參數檔 $star_time array
		include_once("../../inc/date.php");
		
		
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
	
	//---------------------------------------------------
	//SQL
	//---------------------------------------------------
	//條件
	$where_text = '';
	switch ($ramge) {//自己學校
		case 'all':
			$where_text = "";
		break;
		case 'school_all'://同校
			$where_text = 
			" AND ( 
				SELECT count(usc.school_code) 
			    FROM `user`.`school` usc,user.class uc
			    WHERE 1=1
			    AND uc.class_code = ( SELECT class_code FROM user.student WHERE uid = take_to ORDER BY end DESC LIMIT 1 )
			    AND usc.`school_code` = SUBSTRING_INDEX(uc.class_code, '_', 1 )
			    AND usc.`school_code` = '".$school_code."'
			 ) > 0 ";
			 //".$school_code."
		break;
		case 'school_grade'://同校同年級
			$where_text = 
			" AND ( 
				SELECT 
				count(uc.grade)
				FROM `user`.`school` usc, user.class uc
				WHERE 1 = 1  
				AND uc.class_code = ( SELECT class_code FROM user.student WHERE uid = take_to ORDER BY end DESC LIMIT 1 )
				AND usc.`school_code` = SUBSTRING_INDEX(uc.class_code, '_', 1 )
			    AND usc.`school_code` = '".$school_code."'
			    AND uc.grade = SUBSTRING_INDEX(SUBSTRING_INDEX(uc.class_code, '_', 4), '_', -1)
			 ) > 0 ";
			 //".$grade_code." js_2017_2_4_3_2
		break;
		case 'school_class'://同校班
			$where_text = 
			" AND ( 
				SELECT 
				count(uc.grade)
				FROM user.class uc
				WHERE 1 = 1  
				AND uc.class_code = ( SELECT class_code FROM user.student WHERE uid = '".$user_id."' ORDER BY end DESC LIMIT 1 )
				AND uc.class_code = ( SELECT class_code FROM user.student WHERE uid = take_to ORDER BY end DESC LIMIT 1 )
			 ) > 0 ";
			 //".$grade_code." js_2017_2_4_3_2
		break;
	}
	
	//時間判斷
	$where_time = "";
	switch ($time) {
		case 'now_week':
			$where_time = "AND keyin_mdate >= '".$star_time['now_week']."'";
		break;
		case 'last_week':
			$where_time = "AND keyin_mdate >= '".$star_time['last_week']."' AND keyin_mdate < '".$star_time['now_week']."'";
		break;
		case 'now_month':
			$where_time = "AND keyin_mdate >= '".$star_time['now_month']."'";
		break;
		case 'last_month':
			$where_time = "AND keyin_mdate >= '".$star_time['last_month']."' AND keyin_mdate < '".$star_time['now_month']."'";
		break;
		case 'now_semester':
			$where_time = "AND keyin_mdate >= '".$star_time['now_semester']."'";
		break;
		case 'last_semester':
			$where_time = "AND keyin_mdate >= '".$star_time['last_semester']."' AND keyin_mdate < '".$star_time['now_semester']."'";
		break;
		default:
			$where_time = "AND keyin_mdate >= '".$star_time['now_week']."'";
		break;
		
	}
	
	$sql = "SELECT take_to AS user_id,book_sid, count(book_sid) AS book_sid_count 
			FROM mssr.mssr_score_rec_log 
			WHERE 1=1
			AND rec_score = 1 
			AND rec_type != 4 
			".$where_time."
			".$where_text."
			AND ( SELECT count( school_code ) 
				FROM user.personnel u_p
				WHERE u_p.uid = take_to ) = 0
			GROUP BY book_sid,take_to
			ORDER BY book_sid_count DESC,keyin_cdate DESC
			";
	//echo "<pre>";print_r($sql);echo '</pre>';
	//抓前100就好
	$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,100),$arry_conn_mssr);
	//	echo "<pre>";print_r($result);echo '</pre>';
	$data_array = array();
	foreach($result as $key => $val)
	{
		//找學生&老師資訊 Start (找學生最後的學期學校年級班級)
		//姓名
		$sql_name = "SELECT name FROM user.member WHERE uid = '".$val['user_id']."'";
		$result_name = db_result($conn_type='pdo',$conn_mssr,$sql_name,$arry_limit1s=array(),$arry_conn_mssr);
		
		//echo "<pre>";print_r($result_name);echo '</pre>';
		
		
		
		//學期代碼 (學生和老師表都找)
		//type = 身分
		//0.學生
		//1.校長
		//2.主任
		//3.老師
		
		//先找學生
		$sql_u_s = "SELECT class_code, end FROM user.student u_s WHERE u_s.uid = '".$val['user_id']."' ORDER BY end DESC LIMIT 1";
		$result_u_s = db_result($conn_type='pdo',$conn_mssr,$sql_u_s,$arry_limit1s=array(),$arry_conn_mssr);
		
		
		$type = null;
		$class_code_num = '';
		if(count($result_u_s) > 0){//是學生
			$type = 0;
			$class_code_num = $result_u_s[0]['class_code'];
		}else if(count($result_u_s) <= 0){//若不是學生 找老師
			$sql_u_t = "
				SELECT class_code, end 
				FROM user.teacher u_t 
				WHERE u_t.uid = '".$val['user_id']."' 
				ORDER BY end DESC 
				LIMIT 1";
			$result_u_t = db_result($conn_type='pdo',$conn_mssr,$sql_u_t,$arry_limit1s=array(),$arry_conn_mssr);
			$type = 3;
			$class_code_num = $result_u_t[0]['class_code'];
			if(count($result_u_t) <= 0){//若不是老師應該就是主任或校長或志工
			$sql_u_p = "
				SELECT school_code as class_code, end, responsibilities 
				FROM user.personnel u_p
				WHERE u_p.uid = '".$val['user_id']."' 
				ORDER BY end DESC 
				LIMIT 1";
			$result_u_p = db_result($conn_type='pdo',$conn_mssr,$sql_u_p,$arry_limit1s=array(),$arry_conn_mssr);
			
			$type = $result_u_p[0]['responsibilities'];
			$class_code_num = $result_u_p[0]['class_code'];
			}
		}
		
		//echo "<pre>";print_r($sql_school_code);echo '</pre>';

		
		
		//學校名稱
		$school_code_array = array();
		$school_code_array = explode('_',$class_code_num);
		$school_code_num = $school_code_array[0];//學校代號
		$sql_school_name = "SELECT school_code, school_name, region_name, country_code FROM user.school WHERE school_code = '".$school_code_num."';";
		$result_school_name = db_result($conn_type='pdo',$conn_mssr,$sql_school_name,$arry_limit1s=array(),$arry_conn_mssr);
		
		
		
		
		if($data_array[$key]['type'] == 0 || $data_array[$key]['type'] ==3){
			//年級班級
			$sql_grade_code = "
			SELECT 
			uc.grade as grade_code,
			ucn.class_name
			FROM user.class uc,`user`.`class_name` ucn
			WHERE 1 = 1  
			AND uc.class_code = '".$class_code_num."'
			AND ucn.`classroom` = uc.`classroom` 
			AND ucn.`class_category` = uc.`class_category`
			";
			$result_grade_code = db_result($conn_type='pdo',$conn_mssr,$sql_grade_code,$arry_limit1s=array(),$arry_conn_mssr);
			
			//echo "<pre>";print_r($result_grade_code);echo '</pre>';
			
		}
		
		
		//找學生資訊 End
		
		//找書本資訊Start
		//mbc = mssr_book_class
		//mbg = mssr_book_global
		//mbl = mssr_book_library
		//mbu = mssr_book_unverified
		$ch = '';
		switch (substr($val['book_sid'],0,3)) {
			case 'mbc':
				$ch = 'mssr_book_class';
			break;
			case 'mbg':
				$ch = 'mssr_book_global';
			break;
			case 'mbl':
				$ch = 'mssr_book_library';
			break;
			case 'mbu':
				$ch = 'mssr_book_unverified';
			break;
			
		}
		$sql_book_sid = "SELECT book_name FROM mssr.".$ch." where book_sid = '".$val['book_sid']."' limit 1;";
		//echo "<pre>";print_r($sql2s);echo "</pre>";
		$result_book_name = db_result($conn_type='pdo',$conn_mssr,$sql_book_sid,$arry_limit=array(),$arry_conn_mssr);
		
		if($type ==0){//暫時不包含老師
			//玩家ID
			$data_array[$key]['user_id'] = $val["user_id"];
			//書本UID
			$data_array[$key]['book_sid'] = $val["book_sid"];
			//按讚數
			$data_array[$key]['book_sid_count'] = $val["book_sid_count"];
			//姓名
			$data_array[$key]['name'] = $result_name[0]['name'];
			//身分
			$data_array[$key]['type'] = $type;
			//學期代碼
			$data_array[$key]['class_code'] = $class_code_num;
			//學校名稱及代碼
			$data_array[$key]['school_code'] = $result_school_name[0]['school_code'];
			$data_array[$key]['school_name'] = $result_school_name[0]['school_name'];
			//年級_班級
			$data_array[$key]['grade_code'] = $result_grade_code[0]['grade_code'];
			$data_array[$key]['class_name'] = $result_grade_code[0]['class_name'];
			//書名
			$data_array[$key]['book_name'] = $result_book_name[0]["book_name"];
		}
		
		
		//找書本資訊End
		
		
	}
	
	//echo "<pre>";print_r($data_array);echo '</pre>';
	
?>
<!DOCTYPE HTML>
<Html >
<Head>
	<Title></Title>
    <!-- 掛載 -->

    
    
    <style>
table {
table-layout: fixed;
word-break: break-all;
}
       .text_1 {
		    display: block;
			width: 100%;
			height: 34px;
			padding: 3px 6px;
			font-size: 18px;
			line-height: 1.42857143;
			color: #555;
			background-color: #fff;
			background-image: none;
			border: 1px solid #ccc;
			border-radius: 4px;
			
		
	}
	</style>
</Head>
<body >

	<!--==================================================
    html內容
    ====================================================== -->
	<div style="background-color:#D8E2F1; width:720px; font-weight:bold;" class="text_1">
    	<table>
        	<tr>
            	<td width="50" style=" text-align:left;">
                	排名
                </td>
                <td width="110" style=" text-align: left;">
                	獲得讚數
                </td>
                <td width="200" style=" text-align:left;">
                	書籍名稱
                </td>
                <td width="80" style=" text-align:left;">
                	姓名
                </td>
                <td width="80" style=" text-align:left;">
                	學校
                </td>
                <td width="50" style=" text-align:left;">
                	年級
                </td>
                <td width="50" style=" text-align:left;">
                	班級
                </td>
            </tr>
        </table></div>
    <div style="background-color:#FFF; height:300px; width:720px;overflow-x:hidden;overflow-y:auto;" class="text_1">
    	<table>
        	<?php foreach($data_array as $key => $val){?>
        	<tr>
            	<td width="50" style=" text-align:left;">
                	<?php echo $key+1;?>
                </td>
            	<td width="110" style=" text-align:left;">
                	<?php echo $val["book_sid_count"];?>
                </td>
                
                <td   valign="top" style=" width:200px;  text-align:left; overflow:hidden; table-layout:fixed;word-wrap:break-word;">
                	<a href="#" onClick="gogo('<?php echo $val["user_id"];?>','<?php echo $val["name"];?>','<?php echo $val["book_sid"];?>')" ><?php echo $val["book_name"];?></a>
                </td>
                <td width="80" style=" text-align:left;">
                	<?php echo $val["name"];?></a>
                </td>
                <td width="80" style=" text-align:left;">
                	<?php echo $val["school_name"];?>
                </td>
                <?php if($val['type'] == 0 || strlen($val["grade_code"]) > 0): ?>
                	<td width="50" style=" text-align:right;">
	                	<?php echo $val["grade_code"]."年";?>
	                </td>
	                <td width="50" style=" text-align:right;">
	                	<?php echo $val["class_name"]."班";?>
	                </td>
	            <?php else:?>
	            	<td width="100" colspan="2" style=" text-align:right;">
	                	<?php switch ($val['type']) {
							case '1':
								echo "校長";
							break;
							case '2':
								echo "主任";
							break;
							case '3':
								echo "老師";
							break;
						};?>
	                </td>
	            <?php endif;?>
            </tr>
            <?php }?>
        </table>
    
    </div>
    



    <script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	function gogo(value,name,book_sid)
	{
		window.parent.cover("您要前往"+name+"的星球觀看推薦嗎?",2,function(){window.parent.location.href="../index.php?uid="+value+"&book_sid="+book_sid;});
		
		
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
	//=========MAIN=============
	function main()
	{
		echo("Main:初始開始:讀取使用者資料");
		cover("讀取使用者資料中")
		var url = "../ajax/get_user_info.php";
		$.post(url, {
					user_id:window.parent.user_id,
					user_permission:window.parent.user_permission
					
			}).success(function (data) 
			{
				
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",1);
					echo("AJAX:success:main():讀取使用者資料:資料庫發生問題");
					return false;
				}
				
				data_array = JSON.parse(data);
				echo("AJAX:success:main():讀取使用者資料:已讀出:"+data);
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
					cover("");
				}
				
			}).error(function(e){
				echo("AJAX:error:main():讀取使用者資料:");
				window.alert("喔喔?! 讀取失敗了喔  請確認網路連");
				main();
			}).complete(function(e){
				echo("AJAX:complete:main():讀取使用者資料:");
			});
	}
	
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------


    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    