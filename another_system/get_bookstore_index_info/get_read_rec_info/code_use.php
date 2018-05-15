<? 

function test()
{

	require_once($_SERVER['DOCUMENT_ROOT']."/mssr/inc/get_bookstore_index_info/get_read_rec_info/code.php");
	
	/*
	一、	函數功能：學生最新作品列表(歷程首頁專用)  ORDER BY 登記日期 DESC
	二、	函數名稱：系統名稱_function名稱  LHAS_read_list_limit
	三、	參數：(學生uid,作品數量)  ex: ps_abc(105,
	$functionReturn = LHAS_read_list_limit(1251,1);
	print_r($functionReturn);
	echo "<BR>";4)          */
	/*輸出
	Array ( [0] => Array (
				    [book_name] => 生活小學堂                      								  //書籍名稱
					[book_scr] => mssr/info/book/mbc1258201310161539187428/img/front/simg/1.jpg   //書籍圖片網址 無圖片時為0
					[book_sid] => mbc1258201310161543073284                                       //書籍ID
					[time] => 2013-10-16 15:58:55												  //登記時間
				) )                                             
	*/
	//=======================================================================================================================
	/*
	一、	函數功能：作品列表(學年列表)
	二、	函數名稱：系統名稱_function名稱  LHAS_get_read_semester
	三、	參數：(學生uid)  ex: ps_abc(1
	$functionReturn = LHAS_get_read_semester(111);
	print_r($functionReturn);
	echo "<BR>";05)*/
	/*輸出
	Array ( [2012-2] => 101年 第2學期 
			[2012-1] => 101年 第1學期 
			[2011-2] => 100年 第2學期
			[2011-1] => 100年 第1學期
			[2010-2] => 99年 第2學期 )    //[value] => 顯示名稱 
	*/
	//=======================================================================================================================
	/*
	一、	函數功能：作品列表(所有作品)
	二、	函數名稱：系統名稱_function名稱  ex:ps_abc()
	三、	參數：(學生uid,學期)  ex: ps_abc(105,1012);
	四、	回傳方式：多維陣列
	五、	備註:評星，繪圖，文字，錄音 回傳 0(未完成) or 1(已完成)
	$functionReturn = LHAS_read_info_semester(1251,'2013-1');
	print_r($functionReturn);
	echo "<BR>"; */
	/*輸出
	Array ( [0] => Array (
					 [star] => 0 																	//評星  1/0(有推薦/無推薦)
					 [draw] => 0 																	//繪圖  1/0(有推薦/無推薦)
					 [text] => 0 																	//文字  1/0(有推薦/無推薦)
					 [record] => 0 																	//錄音  1/0(有推薦/無推薦)
					 [book_name] => 海洋生物總動員：認識海豚、鯊魚、企鵝、海								//書籍名稱
					 [book_scr] => mssr/info/book/mbc1258201310161539187428/img/front/simg/1.jpg 	//書籍圖片網址 無圖片時為0
					 [book_sid] => mbc1258201310161539187428 										//書籍ID
					 [time] => 2013-10-16 15:58:11 )												//登記時間
		    [1] => Array ( 
					 [star] => 1 
					 [draw] => 1 
					 [text] => 0 
					 [record] => 0 
					 [book_name] => 生活小學堂 
					 [book_scr] => 																	//書籍圖片網址  EX :無圖片時
					 [book_sid] => mbc1258201310161543073284 
					 [time] => 2013-10-16 15:58:55
		) ) 
	*/
	
	$user_id = 35902;
	$sdate = '2015-09-01';
	$edate = '2016-01-14';
	$class_id = array("gcp_2015_2_2_3_1","gcp_2015_2_2_5_1","gcp_2015_2_2_7_1","gcp_2015_2_2_8_1");
	
	//print_r(LHAS_rec_info_by_local_multi($class_id,$sdate,$edate)); //  評分  班級  總平均分數/評星(平均分數)/繪圖(平均分數)/文字(平均分數)/錄音(平均分數)  不重複   區間
	//print_r(LHAS_rec_info_by_day_multi($class_id,$sdate,$edate));
	echo "<table>";
	foreach($class_id as $kk => $vv )
	{
		$sql = "SELECT `student`.`uid`,`number`,`name`
				FROM  `student`
				LEFT JOIN `member`
				ON `member`.`uid` = `student`.`uid`
				WHERE  `class_code` =  '{$vv}'
				ORDER BY  `student`.`number` ASC
				";
		$result = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
		echo "<tr>";
		echo "<td>";
		echo $vv;
		echo "</td>";
		echo "</tr>";
		
		foreach($result as $key => $vul)
		{
			echo "<tr>";
			
			echo "<td>";
			echo $vul['name'];
			echo "</td>";
			
			echo "<td>";
			echo $vul['number'];
			echo "<td>";
			
			echo "<td>";
			$all = LHAS_rec_comment_by_local_single($vul['uid'],$sdate,$edate);
			print_r($all);
			echo "<td>";
			
			
			echo "</tr>";	
		}
	}
	echo "</table>";
	//print_r(LHAS_rec_comment_by_local_single($user_id,$sdate,$edate));//  評分  單人  總平均分數/繪圖(平均分數)/文字(平均分數)/錄音(平均分數)  不重複   區間
	echo "<BR>"; 
	//print_r(LHAS_rec_info_buot_by_day_single($user_id,$sdate,$edate));  // 推薦  單人  總次數/評星(次)/繪圖(次)/文字(次)/錄音(次)  不重複   每日
	echo "<BR>"; 
	//print_r(LHAS_rec_info_buot_by_local_single($user_id,$sdate,$edate)); // 推薦  單人  總次數/評星(次)/繪圖(次)/文字(次)/錄音(次)  不重複   區間
	
	
}
test();

?>
<table>
<tr>
<td>


