<?php
//-------------------------------------------------------
//教師中心
//-------------------------------------------------------

	//---------------------------------------------------
	//設定與引用
	//---------------------------------------------------

		//SESSION
		@session_start();
		// $_SESSION['uid']=5029;

		//啟用BUFFER
		@ob_start();

		//外掛設定檔
		require_once(str_repeat("../",4).'config/config.php');
		require_once(str_repeat("../",4)."/inc/get_black_book_info/code.php");

		//外掛函式檔
		$funcs=array(
					APP_ROOT.'inc/code',
					APP_ROOT.'center/teacher_center/inc/code',

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
	$sess_user_id=$_SESSION['book_level_user_id'];
	$sess_permission=$_SESSION['book_level_permission'];
	$sess_name=$_SESSION['book_level_name'];
	
	if(!isset($sess_user_id)&&!isset($sess_permission)&&!isset($sess_name)){


		echo '<span style="font-size:40px; color:red;">請先登入!!</span>';

		header('Location:http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/index.php');


		die();


		
	 }

	 if($sess_permission!="3"){

			echo '<span style="font-size:40px; color:red;">你沒有權限進入!!</span>';

			die();
	}

	//---------------------------------------------------
	//權限,與判斷
	//---------------------------------------------------

  

	//---------------------------------------------------
	//管理者判斷
	//---------------------------------------------------



	//---------------------------------------------------
	//系統權限判斷
	//---------------------------------------------------
	//1     校長
	//3     主任
	//5     帶班老師
	//12    行政老師
	//14    主任帶一個班
	//16    主任帶多個班
	//22    老師帶多個班
	//99    管理者


	//---------------------------------------------------
	//接收參數
	//---------------------------------------------------



	//---------------------------------------------------
	//設定參數
	//---------------------------------------------------



	//---------------------------------------------------
	//串接SQL
	//---------------------------------------------------

		//-----------------------------------------------
		//資料庫
		//-----------------------------------------------

			//建立連線 user
			$conn_user=conn($db_type='mysql',$arry_conn_user);

			//建立連線 mssr
			$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

		//-----------------------------------------------
		//檢核借閱書學校關聯
		//-----------------------------------------------


		//---------------------------------------------------
		//SQL 筆數查詢
		//---------------------------------------------------

 

	//---------------------------------------------------
	//分頁處理
	//---------------------------------------------------

 
		//echo $numrow."<br/>";

	//---------------------------------------------------
	//SQL 查詢
	//---------------------------------------------------


//echo "<Pre>";
//print_r($arrys_result);
//echo "</Pre>";
//die();
	//---------------------------------------------------
	//資料,設定
	//---------------------------------------------------

		//網頁標題
		$title="明日星球,教師中心";

		//-----------------------------------------------
		//教師中心路徑選單
		//-----------------------------------------------

?>
<?php
		  //-----------------------------------------------
		//處理
		//-----------------------------------------------

		$array_output=array();
		$topic_options=array();
		$array_book_sid=array();


		//======================
		//第二題題目選項(書的語言)
		//======================

		$qa_sql ="
					SELECT topic_id ,topic_options
					FROM `mssr_idc_reading_log_topic`
					 WHERE topic_id ='2'

				";

		$qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

		if(!empty($qa_result)){
			foreach ($qa_result as $key => $value) {
				$topic_options[$key]['topic2_options']=unserialize($value['topic_options']);
			}
			
			// print_r($topic_options[$key]['topic3_options'][0]);
		 }

		//======================
		//第三題題目選項(是否有注音)
		//======================
		$qa_sql ="
					SELECT topic_id ,topic_options
					FROM `mssr_idc_reading_log_topic`
					WHERE topic_id ='3'

				";

		$qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

		if(!empty($qa_result)){
			foreach ($qa_result as $key => $value) {
				$topic_options[$key]['topic3_options']=unserialize($value['topic_options']);
			}
			
			// print_r($topic_options[$key]['topic3_options'][0]);
		 }

		//======================
		//第四題題目選項(大主題)
		//======================

		$qa_sql ="
					SELECT topic_id ,topic_options
					FROM `mssr_idc_reading_log_topic`
					WHERE topic_id ='4'

				 ";

		$qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

		if(!empty($qa_result)){
			foreach ($qa_result as $key => $value) {
				$topic_options[$key]['topic4_options']=unserialize($value['topic_options']);
			}
			
		   
		 }

		//======================
		//第五題題目選項(中主題)
		//======================

		$qa_sql ="
					SELECT topic_id ,topic_options
					FROM `mssr_idc_reading_log_topic`
					WHERE topic_id ='5'

				 ";

		$qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

		if(!empty($qa_result)){
			foreach ($qa_result as $key => $value) {
				$topic_options[$key]['topic5_options']=unserialize($value['topic_options']);
			}
			
		   
		}
		//======================
		//第六題題目選項(小主題)
		//======================

		$qa_sql ="
					SELECT topic_id ,topic_options
					FROM `mssr_idc_reading_log_topic`
					WHERE topic_id ='6'

				 ";

		$qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

		if(!empty($qa_result)){
			foreach ($qa_result as $key => $value) {
				$topic_options[$key]['topic6_options']=unserialize($value['topic_options']);
			}
			
		   
		}

		//======================
		//第十四題題目選項(難度等級)
		//======================

		$qa_sql ="
					SELECT topic_id ,topic_options
					FROM `mssr_idc_reading_log_topic`
					WHERE topic_id ='14'

				 ";

		$qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

		if(!empty($qa_result)){
			foreach ($qa_result as $key => $value) {
				$topic_options[$key]['topic14_options']=unserialize($value['topic_options']);
				
			}
			
		   
		 }



//----------------------------------------------------------

		//======================
		//表單登記的書
		//======================



			  //===================
			  //尋找書名
			  //===================
			   $sql="
					SELECT *

					FROM (

							SELECT
								book_sid,
								IFNULL(`book_name`,0) as `book_name`,
								IFNULL(`book_isbn_10`,0) as `book_isbn_10`,
								IFNULL(`book_isbn_13`,0) as `book_isbn_13`,
								0 as book_library_code,
								keyin_cdate
								 
							FROM `mssr_book_class`
							WHERE school_code='idc'



							union 
							
							SELECT 
							
								book_sid,
								IFNULL(`book_name`,0) as `book_name`,
								IFNULL(`book_isbn_10`,0) as `book_isbn_10`,
								IFNULL(`book_isbn_13`,0) as `book_isbn_13`,
								book_library_code,
								keyin_cdate
							
								 
							FROM `mssr_book_library`
							WHERE school_code='idc'

					 ) as book_db
					 order by book_db.keyin_cdate desc


					
				";
				
				$rows=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				
				//---- Pager ---------------------
	
			    $pagesize = 30;
			    //總筆數
				$total = count($rows);
				
				//是否要顯示分頁
				if($pagesize < $total) $page_open = TRUE;
				
				//總頁数
				$totalpage = ceil($total/$pagesize);
				//echo "<pre>";print_r($total);echo "</pre>";
				//目前頁數
				$page = $_GET["page"];
				if ($page == "" || $page == "0") $page = 1;  
				//目前頁面起始序號
				
				if($page==1) $go_to_one = 1;
				if($page!=1) $go_to_one = $page*$pagesize-$pagesize+1;
				
				//語句
				$sql .= " LIMIT ".($pagesize * ($page-1)).", ".$pagesize;   // LIMIT 21,40
				
				//---- Pager ---------------------    

				$result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				if(!empty($result)){

						foreach ($result as $key => $value) {

							  $array_output[$key]['book_sid']          =trim($value['book_sid']);
							  $array_output[$key]['book_name']          =trim($value['book_name']);
							  $array_output[$key]['book_isbn_13']        =trim($value['book_isbn_13']);
							  $array_output[$key]['book_isbn_10']        =trim($value['book_isbn_10']);
							  $array_output[$key]['book_library_code']   =trim($value['book_library_code']);



							  $book_sql="
								   SELECT *         
								   FROM `mssr_idc_book_sticker_level_info` 
								   WHERE  `book_sid`='{$array_output[$key]['book_sid']}' 
											
							";


							$book_sql_result=db_result($conn_type='pdo',$conn_mssr,$book_sql,array(),$arry_conn_mssr);


							// print_r($book_sql_result);

							// die();

							if(!empty($book_sql_result)){
								 
								 $array_output[$key]['administrator_level']="{$book_sql_result[0]['administrator_level']}";
								 $array_output[$key]['language']=trim($book_sql_result[0]['language']);
								 $array_output[$key]['bopomofo']=trim($book_sql_result[0]['bopomofo']);
								 $array_output[$key]['pages']=trim($book_sql_result[0]['pages']);
								 $array_output[$key]['words']=trim($book_sql_result[0]['words']);
								$array_output[$key]['language']=trim($book_sql_result[0]['language']);
								 






									// // //======================
									// // //尋找貼紙編號及貼紙顏色
									// // //======================

									$sticker_sql="
												SELECT `sticker_color`,`sticker_number` 
												FROM `mssr_idc_book_sticker_level_info`
												WHERE book_sid='{$array_output[$key]['book_sid']}'

									";

									$sticker_result=db_result($conn_type='pdo',$conn_mssr,$sticker_sql,array(),$arry_conn_mssr);

									if(!empty($sticker_result)){
										  

												$array_output[$key]['sticker_color']=trim($sticker_result[0]['sticker_color']);
												$array_output[$key]['sticker_number']=trim($sticker_result[0]['sticker_number']);


												// $sticker_color_sql="
												// 	SELECT color
												// 	FROM  `mssr_idc_book_sticker_color`
												// 	WHERE  color_id='{$sticker_result[0]['sticker_color']}'   
												// " ; 

												// $sticker_color_result=db_result($conn_type='pdo',$conn_mssr,$sticker_color_sql,array(),$arry_conn_mssr);
												  
												// if(!empty($sticker_color_result)){

												// 		$array_output[$key]['sticker_color'] =trim($sticker_color_result[0]['color']);
												// }else{
												// 	$array_output[$key]['sticker_color'] ="0";
												// }
									}else{
											$array_output[$key]['sticker_color']="0";
											$array_output[$key]['sticker_number']="0";
									}




							}else{

								$read_sql="
											   INSERT INTO `mssr_idc_book_sticker_level_info`(
											   `edit_by`, 
											   `user_id`, 
											   `book_sid`,  
											   `sticker_color`, 
											   `sticker_number`,
											   administrator_level, 
											   `keyin_cdate`, 
											   `keyin_mdate`
												) 
												VALUES (
												'{$sess_user_id}',
												'{$sess_user_id}',
												'{$array_output[$key]['book_sid']}',
												'0',
												'0',
												'0',
												now(),
												now()
												
												)

									";

								 $result=db_result($conn_type='pdo',$conn_mssr,$read_sql,array(),$arry_conn_mssr);





							}


						}
				}

//取得貼子的顏色
$sql = "
	SELECT *
	FROM `mssr_idc_book_sticker_color`;
";

$color_result = db_result($conn_type='pdo', $conn_mssr, $sql, array(), $arry_conn_mssr);

function get_sticker_color($color_number, $color_result) {
	if (!empty($color_result)) {
		foreach ($color_result as $value) {
			if ($color_number == $value['color_id']) {
				$color = $value['color'];
			}
		}
	} else {
		$color = "";
	}

	return $color;
}

?>
<!DOCTYPE HTML>
<Html>
<Head>
	<Title><?php echo $title;?></Title>
	<meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
	<meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
	<?php echo meta_keywords($key='mssr');?>
	<?php echo meta_description($key='mssr');?>
	<?php echo bing_analysis($allow=false);?>
	<?php echo robots($allow=false);?>

	<!-- 通用 -->
	<!-- <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" /> -->
	<script type="text/javascript" src="../../../../inc/code.js"></script>

	<script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>
	<script type="text/javascript" src="../../../../lib/jquery/plugin/code.js"></script>

	<script type="text/javascript" src="../../../../lib/js/vaildate/code.js"></script>
	<script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
	<script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
	<script type="text/javascript" src="../../../../lib/js/table/code.js"></script>

	<!-- 專屬 -->
	<link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
	<script type="text/javascript" src="../../inc/code.js"></script>

	<!-- <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" /> -->
	<link rel="stylesheet" type="text/css" href="../css/book_level.css" />

	<style>
	#more_six{
		font-size: 13px;
		font-family: 微軟正黑體;
	}

	#level_table_over6 table{
		text-align: center;
		margin: 30px auto;

	}
	.buttons{
		text-align: center;
		margin: 20px auto;
	}
	td{
		max-width: 110px;
		max-height: 150px;
		overflow: hidden;
		color: #444;
	}
	.input_select{
		display: none;
	}
		.input_text {
			display: none;
			width: 50px;
	}
	.show_pages, .show_words, .show_language, .show_sticker_color, .show_sticker_number {
		letter-spacing: 0px;
	}
	
	.botton1{
		border: 1px solid;
	    text-decoration: none;
	    border-radius: 5px;
	    background-color: #3399CC;
	    color: #ffffff;
	    padding: 5px;
	}
	.botton2{
	    border: 1px solid;
	    text-decoration: none;
	    border-radius: 5px;
	    background-color: #CD0A0A;
	    color: #ffffff;
	    padding: 5px;
	}

</style>
</Head>

<Body>
		<!-- 內容區塊 開始 -->
	<div id="more_six">
			<div>
				<?php
					require_once('../user/index.php');
					if($sess_permission==="3"){require_once('../user/super_use_index.php');};
				?>
			</div>
			<div class="about_title_div">
				<h1 id="title">管理員等級表</h1>
			</div>
			<div class="buttons">
				
				
				<a href="administrator_level.php"><input type="button" id="pages" value="管理員等級表"></a>
				 <!-- <a href="need_read_again.php"><input type="button" id="read_again" value="需重看書籍表"></a> -->
				<a href="pt_work.php"><input type="button" id="pt" value="工讀生工作表" ></a>
				 <!-- <a href="read.php"><input type="button" id="read" value="已閱表"></a> -->
				 <a href="topic.php"><input type="button" id="topic" value="書本主題標籤表"></a>
				<!--  <a href="more_six.php"><input type="button" id="level_6" value="等級大於6書表"></a>
				 <a href="less_six.php"><input type="button" id="level_0" value="等級小於6書表"></a> -->
				<br>
				<!--page -->
				<p style="text-align: center;margin-top: 10px">
					<b>共有:<?php echo $total?>本書</b>
					<span>當前顯示:
						<?php 
						echo $go_to_one;
						if( $total < ($page*$pagesize)){
							echo "~".$total;
						}
						else {
							echo "~".($page*$pagesize);
							
						}
						
						?>
					</span>
					<br>
				</p>
				<p style="text-align: center;margin-top: 10px">
				<?php 
					$url = '';
					$php_url = 'administrator_level.php';
					$z = 0;
					foreach ($_GET as $key => $value) {
						if($key != 'page'){
							if($z!=0) $url.= "&"; 
							$url .= $key.'='.$value;
							$z++;
						}
						
					}
					if($z != 0) $url .="&";
					
					if($page != 1 ){
						echo '<a class="botton1" href="'.$php_url.'?'.$url.'page=1">|&lt;&nbsp;</a> ';
						echo '<a class="botton1" href="'.$php_url.'?'.$url.'page='.($page-1).'">&nbsp;&lt;&nbsp;</a> ';
					}
					if($page <= 5){
						for ($i = 1; $i <= $totalpage; $i++) {
							if ($page == $i) {
						    	echo "&nbsp;<span class='botton2'>".$i."</span>&nbsp;";	
							} else {
							 	 	if($i <= 10){
							 	 		echo "&nbsp;<a class='botton1' href='".$php_url."?".$url."page=".$i."'>".$i."</a>&nbsp;";
							 	 	}
							}
						
						}
					}else{
						//$post = $pos + 10;
						for ($i = $page -5; $i <= $totalpage; $i++) {
							if ($page == $i) {
						    	echo "&nbsp;<span class=\"botton2\">".$i."</span>&nbsp;";	
							} elseif($i <= $page + 5) {
							 	 	echo "&nbsp;<a class=\"botton1\" href='".$php_url."?".$url."page=".$i."'>".$i."</a>&nbsp;";
							}
						
						}
					}
					if ( $page != $totalpage && $totalpage!=0) echo "<a class=\"botton1\" href='".$php_url."?".$url."page=".($page+1)."'> &gt;</a>";
					if ( $page != $totalpage )echo "<a class=\"botton1\" href='".$php_url."?".$url."page=".$totalpage."'> &gt;|</a>";
				?>
				
				</p>
				<!--page end-->
			</div>
			
			<div id="level_table_over6">
				<table id="level" border="1" cellpadding="5" style="border:1px #aaa solid; text-align:center;margin: 20px auto;">
							<tr>
							   <!--  <th>已閱</th> -->
								<th>序號</th>
								<th>書名</th> 
								<th>isbn10碼</th>
								<th>isbn13碼</th>
								<th>圖書館編號</th>
								<th>語言</th>
								<th>貼紙顏色</th>
								<th>貼紙編號</th>
								<th>最終難度等級</th>
								<th>最終頁數</th>
								<th>最終字數</th>
								<th>工讀生姓名</th>
								<th>工讀生等級</th>
								<th>工讀生頁數</th>
								<th>工讀生字數</th>
							   <!--  <th>需要重看</th> -->
								
								
							</tr>   
					   
				<?php foreach ($array_output as $key => $value) { ?>
						
							<tr class="data_content" id="data_one_<?php echo $key?>"> 
										<input type="hidden" class="book_sid" value="<?php echo $value['book_sid']?>" name="<?php echo $value['book_sid']?>">
									<!--     <td class="read_checkbox" style="height:40px;">
											<input type="checkbox" id="read_checkbox_<?php echo $key?>">
										</td> -->
										<td rowspan="2" class="number" style="height:40px;">
											<?php echo ($key+$go_to_one)?>
										</td>
										<td rowspan="2" id="book_name" style="height: 40px;" title="<?php echo trim($value['book_name']) ?> ">            
											<?php echo $value['book_name']?>
										 
<!-- 
														$string=trim($value['book_name']); 
														$strLength = mb_strlen($string,"utf-8");
														if($strLength<=7){
															$textAns = $string;
															echo $textAns;
														}else{
															$textAns = mb_substr($string,0,8,'utf-8');
															echo $textAns,"...";
															
														}
														 -->
											
										</td>
										<td  rowspan="2" id="isbn_10" style="height: 40px;"><?php echo $value['book_isbn_10']?></td>
										<td  rowspan="2" id="isbn_13" style="height: 40px;"><?php echo $value['book_isbn_13']?></td>
										<td  rowspan="2" id="book_library_code" style="height: 40px;"><?php echo ($value['book_library_code']!='0'?$value['book_library_code']:'' )?></td>
										<td rowspan="2" id="language" style="height: 40px;">
											<div class="language_area">
												<span class="show_language" id="language_<?php echo ($key+$go_to_one-1) ?>">
													<?php 
														if ($value['language'] == 0) {
															echo "";
														} elseif ($value['language'] == 1) {
															echo "中文";
														} elseif ($value['language'] == 2) {
															echo "英文";
														} elseif ($value['language'] == 3) {
															echo "中英混和";
														}
													 ?>
												</span>
												<br>
												<select class="input_select" id="change_language_text_<?php echo ($key+$go_to_one-1) ?>">
													<option value="1" <?php if ($value['language'] == 1) echo "selected"; ?>>中文</option>
													<option value="2" <?php if ($value['language'] == 2) echo "selected"; ?>>英文</option>
													<option value="3" <?php if ($value['language'] == 3) echo "selected"; ?>>中英混和</option>
												</select>
												<input type="button" value="修改" id="change_btn_<?php echo ($key+$go_to_one-1) ?>">
											</div>
										</td>
										<td rowspan="2" id="sticker_color" style="height: 40px;">
											<div class="sticker_color_area">
												<span class="show_sticker_color" id="sticker_color_<?php echo ($key+$go_to_one-1)?>" style="letter-spacing:0px;">
													<?php echo get_sticker_color($value['sticker_color'], $color_result); ?>
												</span>
												<br>
												<select class="input_select" id="change_sticker_color_<?php echo ($key+$go_to_one-1)?>">
													<?php foreach ($color_result as $color_value) { ?>
														<option value="<?php echo $color_value['color_id']; ?>" <?php if ($value['sticker_color'] == $color_value['color_id']) echo "selected"; ?>><?php echo $color_value['color']; ?></option>
													<?php } ?>
												</select>
												<input type="button" value="修改"  id="change_btn_<?php echo ($key+$go_to_one-1)?>">
											</div>
										</td>
										<td rowspan="2" id="sticker_number" style="height:40px;">
											<div class="sticker_number_area">
												<span class="show_sticker_number" id="sticker_number_<?php echo ($key+$go_to_one-1) ?>">
													<?php if (!empty($value['sticker_number'])) echo $value['sticker_number']; ?>
												</span>
												<br>
												<input type="text" value="<?php echo $value['sticker_number']; ?>" class="input_text" id="change_sticker_number_<?php echo ($key+$go_to_one-1) ?>" maxlength="6">
												<input type="button" value="修改" id="change_btn_<?php echo ($key+$go_to_one-1) ?>">
											</div>
										</td>
										<td rowspan="2" id="change_<?php echo ($key+$go_to_one-1)?>" style="height: 40px;">    
											
											<div class="avg">
												<span class="avg_level" id="avg_level_<?php echo ($key+$go_to_one-1)?>" style="letter-spacing:0px;">
													<?php
														if(!empty($value['administrator_level'])){
															if ($value['administrator_level'] == 1) {
																echo "繪本∕初階";
															} elseif ($value['administrator_level'] == 2) {
																echo "繪本∕進階";
															} elseif ($value['administrator_level'] == 3) {
																echo "橋梁書∕初階";
															} elseif ($value['administrator_level'] == 4) {
																echo "橋梁書∕進階";
															} elseif ($value['administrator_level'] == 5) {
																echo "文字書";
															}
														}
													 ?>
												</span>
												<br>
												<select class="input_select" id="change_level_select_<?php echo ($key+$go_to_one-1)?>">
													<option value="1" <?php if ($value['administrator_level'] == 1) echo "selected"; ?>>繪本∕初階</option>
													<option value="2" <?php if ($value['administrator_level'] == 2) echo "selected"; ?>>繪本∕進階</option>
													<option value="3" <?php if ($value['administrator_level'] == 3) echo "selected"; ?>>橋梁書∕初階</option>
													<option value="4" <?php if ($value['administrator_level'] == 4) echo "selected"; ?>>橋梁書∕進階</option>
													<option value="5" <?php if ($value['administrator_level'] == 5) echo "selected"; ?>>文字書</option>
												</select>
												<!-- <input type="text" value="<?php echo $value['administrator_level'];?>" class="input_text" id="change_text_<?php echo ($key+$go_to_one-1)?>" style="display: none;width: 30px;" size="3" maxlength="3"  > -->
												<input type="button" value="修改"  id="change_btn_<?php echo ($key+$go_to_one-1)?>">
											</div>

										</td>
										<td rowspan="2" style="height:20px;">
											<div class="pages_area">
												<span class="show_pages" id="pages_<?php echo ($key+$go_to_one-1) ?>">
													<?php if (!empty($value['pages'])) echo $value['pages']; ?>
												</span>
												<br>
												<input type="text" value="<?php echo $value['pages']; ?>" class="input_text" id="change_pages_text_<?php echo ($key+$go_to_one-1) ?>" maxlength="6">
												<input type="button" value="修改" id="change_btn_<?php echo ($key+$go_to_one-1) ?>">
											</div>
										</td>
										<td  rowspan="2" style="height:20px;">
											<div class="words_area">
												<span class="show_words" id="words_<?php echo ($key+$go_to_one-1) ?>">
													<?php if (!empty($value['words'])) echo $value['words']; ?>
												</span>
												<br>
												<input type="text" value="<?php echo $value['words']; ?>" class="input_text" id="change_words_text_<?php echo ($key+$go_to_one-1) ?>" maxlength="6">
												<input type="button" value="修改" id="change_btn_<?php echo ($key+$go_to_one-1) ?>">
											</div>
										</td>
										<td style="height: 20px;"> 
										   
											
										</td>
										<td style="height: 20px;"> 
										   
											
										</td>
										<td style="height: 20px;"> 
										   
											
										</td>
										<td style="height: 20px;"> 
										   
											
										</td>

									 <!--    <td class="read_again_checkbox"  style="height: 40px;">  <input type="checkbox" id="read_again_checkbox_<?php echo $key?>"></td> -->
								
							</tr>
							 <tr class="data_content" id="data_two_<?php echo ($key+$go_to_one-1)?>">
									<td style="height: 20px;"> 
										   
											
									</td>
									<td style="height: 20px;"> 
									   
												
									</td>
									<td style="height:20px;">
												 
													
									</td>
									<td style="height:20px;">
												
													
									</td>
								 

							</tr>
							

				<?php } ?>
					   
				</table>
			
			</div>
<!--page -->
				
				<p style="text-align: center;margin-top: 10px">
					
				<?php 
					$url = '';
					$php_url = 'administrator_level.php';
					$z = 0;
					foreach ($_GET as $key => $value) {
						if($key != 'page'){
							if($z!=0) $url.= "&"; 
							$url .= $key.'='.$value;
							$z++;
						}
						
					}
					if($z != 0) $url .="&";
					
					if($page != 1 ){
						echo '<a class="botton1" href="'.$php_url.'?'.$url.'page=1">|&lt;&nbsp;</a> ';
						echo '<a class="botton1" href="'.$php_url.'?'.$url.'page='.($page-1).'">&nbsp;&lt;&nbsp;</a> ';
					}
					if($page <= 5){
						for ($i = 1; $i <= $totalpage; $i++) {
							if ($page == $i) {
						    	echo "&nbsp;<span class='botton2'>".$i."</span>&nbsp;";	
							} else {
							 	 	if($i <= 10){
							 	 		echo "&nbsp;<a class='botton1' href='".$php_url."?".$url."page=".$i."'>".$i."</a>&nbsp;";
							 	 	}
							}
						
						}
					}else{
						//$post = $pos + 10;
						for ($i = $page -5; $i <= $totalpage; $i++) {
							if ($page == $i) {
						    	echo "&nbsp;<span class=\"botton2\">".$i."</span>&nbsp;";	
							} elseif($i <= $page + 5) {
							 	 	echo "&nbsp;<a class=\"botton1\" href='".$php_url."?".$url."page=".$i."'>".$i."</a>&nbsp;";
							}
						
						}
					}
					if ( $page != $totalpage && $totalpage!=0) echo "<a class=\"botton1\" href='".$php_url."?".$url."page=".($page+1)."'> &gt;</a>";
					if ( $page != $totalpage )echo "<a class=\"botton1\" href='".$php_url."?".$url."page=".$totalpage."'> &gt;|</a>";
				?>
				</p>
				<p style="text-align: center;margin-top: 10px">
					<b>共有:<?php echo $total?>本書</b>
					<span>當前顯示:
						<?php 
						echo $go_to_one;
						if( $total < ($page*$pagesize)){
							echo "~".$total;
						}
						else {
							echo "~".($page*$pagesize);
							
						}
						
						?>
					</span>
					<br>
				</p>
				<!--page end-->
				<br>
				<br>
				<br>
   
</div>
</Body>
<script type="text/javascript">

//點擊上方按鈕表單切換


//若按下修改鍵

$(".avg input:button").click(function(){



		var $this = $(this);
		var btn_id=$this.attr("id");
		var val=$this.val();
		var td_id=$(this).parent().attr("id");

		if(val=="修改"){
			$this.attr("value", "儲存");
			$(this).siblings('.input_select').css("display","block");

		}else{
		   
			$this.attr("value", "修改");
			var input_id=$(this).siblings('.input_select').css("display","none");
			var input_text_val=$(this).siblings('.input_select').val();
			var span_id=$(this).siblings('.avg_level').attr("id");
			var book_sid=$(this).parent().parent().siblings('.book_sid').val();

			var book_level = "";
			if (input_text_val == 1) {
				book_level = "繪本∕初階";
			} else if (input_text_val == 2) {
				book_level = "繪本∕進階";
			} else if (input_text_val == 3) {
				book_level = "橋梁書∕初階";
			} else if (input_text_val == 4) {
				book_level = "橋梁書∕進階";
			} else if (input_text_val == 5) {
				book_level = "文字書";
			}
			
			$("#"+span_id).text(book_level);
			change_level(input_text_val,book_sid);

			console.log(input_text_val);
			
		};

	   
});


//修改等級

function change_level(input_text_val,book_sid){
		var url = "ajax/update_avg_level.php";
		var dataVal = {
		   
			avg_level:            input_text_val,
			book_sid:             book_sid  
		};

		$.ajax({
		   url: url,
		   type: "POST",
		   datatype: "json",
		   data: dataVal,
		   // contentType: "application/json; charset=utf-8",
		   async: false,
		   success: function(respones) {
			//成功處理
			var data = JSON.parse(respones);
			if(data['type'] == 'error'){
				alert(data['error_text']);
				if(data['error_go_to_url'] != '') document.location.href=data['error_go_to_url']; 
				
			}
		   },
		   error: function(jqXHR) {
			alert("發生錯誤: " + jqXHR.status);
		  }

		});

}

//按下頁數欄位的修改
$(".pages_area input:button").click(function(){
	var btn_id = $(this).attr("id");
	var val = $(this).val();
	var td_id = $(this).parent().attr("id");

	if (val == "修改") {
		$(this).attr("value", "儲存");
		$(this).siblings('.input_text').css("display","block");
	} else {
		$(this).attr("value", "修改");
		var input_id = $(this).siblings('.input_text').css("display","none");
		var input_text_val = $(this).siblings('.input_text').val();
		var span_id = $(this).siblings('.show_pages').attr("id");
		var book_sid = $(this).parent().parent().siblings('.book_sid').val();
		
		$("#"+span_id).text(input_text_val);
		change_pages(book_sid, input_text_val);
	};
});

//修改頁數
function change_pages(book_sid, pages) {
	$.ajax({
		async: true,
		cache: false,
		global: true,
		timeout: 50000,
		contentType: "application/x-www-form-urlencoded; charset=UTF-8",
		url: "ajax/update_pages_and_words.php",
		type: "POST",
		datatype: "json",
		data: {
			book_sid: book_sid,
			pages: pages,
			action: "update_pages"
		},
		success: function(respones) {
			//成功處理
			var data = JSON.parse(respones);
			if(data['type'] == 'error'){
				alert(data['error_text']);
				if(data['error_go_to_url'] != '') document.location.href=data['error_go_to_url']; 
				
			}
		},
		error: function(xhr, ajaxoptions, thrownerror) {
			//失敗處理
			alert(xhr.responseText);
			return false;
		}
	});
}

//按下字數欄位的修改
$(".words_area input:button").click(function(){
	var btn_id = $(this).attr("id");
	var val = $(this).val();
	var td_id = $(this).parent().attr("id");

	if (val == "修改") {
		$(this).attr("value", "儲存");
		$(this).siblings('.input_text').css("display","block");
	} else {
		$(this).attr("value", "修改");
		var input_id = $(this).siblings('.input_text').css("display","none");
		var input_text_val = $(this).siblings('.input_text').val();
		var span_id = $(this).siblings('.show_words').attr("id");
		var book_sid = $(this).parent().parent().siblings('.book_sid').val();
		
		$("#"+span_id).text(input_text_val);
		change_words(book_sid, input_text_val);
	};
});

//修改字數
function change_words(book_sid, words) {
	$.ajax({
		async: true,
		cache: false,
		global: true,
		timeout: 50000,
		contentType: "application/x-www-form-urlencoded; charset=UTF-8",
		url: "ajax/update_pages_and_words.php",
		type: "POST",
		datatype: "json",
		data: {
			book_sid: book_sid,
			words: words,
			action: "update_words"
		},
		success: function(respones) {
			//成功處理
			var data = JSON.parse(respones);
			if(data['type'] == 'error'){
				alert(data['error_text']);
				if(data['error_go_to_url'] != '') document.location.href=data['error_go_to_url']; 
				
			}
		},
		error: function(xhr, ajaxoptions, thrownerror) {
			//失敗處理
			alert(xhr.responseText);
			return false;
		}
	});
}

//按下語言欄位的修改
$(".language_area input:button").click(function(){
	var btn_id = $(this).attr("id");
	var val = $(this).val();
	var td_id = $(this).parent().attr("id");

	if (val == "修改") {
		$(this).attr("value", "儲存");
		$(this).siblings('.input_select').css("display","block");
	} else {
		$(this).attr("value", "修改");
		var input_id = $(this).siblings('.input_select').css("display","none");
		var input_text_val = $(this).siblings('.input_select').val();
		var span_id = $(this).siblings('.show_language').attr("id");
		var book_sid = $(this).parent().parent().siblings('.book_sid').val();
		
		var book_language = "";
		if (input_text_val == 1) {
			book_language = "中文";
		} else if (input_text_val == 2) {
			book_language = "英文";
		} else if (input_text_val == 3) {
			book_language = "中英混合";
		}
		
		$("#"+span_id).text(book_language);
		change_language(book_sid, input_text_val);
	};
});

//修改語言
function change_language(book_sid, language) {
	$.ajax({
		async: true,
		cache: false,
		global: true,
		timeout: 50000,
		contentType: "application/x-www-form-urlencoded; charset=UTF-8",
		url: "ajax/update_language.php",
		type: "POST",
		datatype: "json",
		data: {
			book_sid: book_sid,
			language: language
		},
		success: function(respones) {
			//成功處理
			var data = JSON.parse(respones);
			if(data['type'] == 'error'){
				alert(data['error_text']);
				if(data['error_go_to_url'] != '') document.location.href=data['error_go_to_url']; 
				
			}
		},
		error: function(xhr, ajaxoptions, thrownerror) {
			//失敗處理
			alert(xhr.responseText);
			return false;
		}
	});
}

//按下貼子顏色欄位的修改
$(".sticker_color_area input:button").click(function(){
	var btn_id = $(this).attr("id");
	var val = $(this).val();
	var td_id = $(this).parent().attr("id");

	if (val == "修改") {
		$(this).attr("value", "儲存");
		$(this).siblings('.input_select').css("display","block");
	} else {
		$(this).attr("value", "修改");
		var input_id = $(this).siblings('.input_select').css("display","none");
		var input_text_val = $(this).siblings('.input_select').val();
		var span_id = $(this).siblings('.show_sticker_color').attr("id");
		var book_sid = $(this).parent().parent().siblings('.book_sid').val();
		var color = $(this).siblings('.input_select').find("option:selected").text();
		
		$("#"+span_id).text(color);
		change_sticker_color(book_sid, input_text_val);
	};
});

//修改貼子顏色
function change_sticker_color(book_sid, sticker_color) {
	$.ajax({
		async: true,
		cache: false,
		global: true,
		timeout: 50000,
		contentType: "application/x-www-form-urlencoded; charset=UTF-8",
		url: "ajax/update_sticker_color_and_number.php",
		type: "POST",
		datatype: "json",
		data: {
			book_sid: book_sid,
			sticker_color: sticker_color,
			action: "update_color"
		},
		success: function(respones) {
			//成功處理
			var data = JSON.parse(respones);
			if(data['type'] == 'error'){
				alert(data['error_text']);
				if(data['error_go_to_url'] != '') document.location.href=data['error_go_to_url']; 
				
			}
		},
		error: function(xhr, ajaxoptions, thrownerror) {
			//失敗處理
			alert(xhr.responseText);
			return false;
		}
	});
}

//按下貼子編號欄位的修改
$(".sticker_number_area input:button").click(function(){
	var btn_id = $(this).attr("id");
	var val = $(this).val();
	var td_id = $(this).parent().attr("id");

	if (val == "修改") {
		$(this).attr("value", "儲存");
		$(this).siblings('.input_text').css("display","block");
	} else {
		$(this).attr("value", "修改");
		var input_id = $(this).siblings('.input_text').css("display","none");
		var input_text_val = $(this).siblings('.input_text').val();
		var span_id = $(this).siblings('.show_sticker_number').attr("id");
		var book_sid = $(this).parent().parent().siblings('.book_sid').val();
		
		$("#"+span_id).text(input_text_val);
		change_sticker_number(book_sid, input_text_val);
	};
});

//修改貼子編號
function change_sticker_number(book_sid, sticker_number) {
	$.ajax({
		async: true,
		cache: false,
		global: true,
		timeout: 50000,
		contentType: "application/x-www-form-urlencoded; charset=UTF-8",
		url: "ajax/update_sticker_color_and_number.php",
		type: "POST",
		datatype: "json",
		data: {
			book_sid: book_sid,
			sticker_number: sticker_number,
			action: "update_number"
		},
		success: function(respones) {
			//成功處理
			var data = JSON.parse(respones);
			if(data['type'] == 'error'){
				alert(data['error_text']);
				if(data['error_go_to_url'] != '') document.location.href=data['error_go_to_url']; 
				
			}
		},
		error: function(xhr, ajaxoptions, thrownerror) {
			//失敗處理
			alert(xhr.responseText);
			return false;
		}
	});
}

</script>
</Html>