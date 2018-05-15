<?php
//-------------------------------------------------------
//版本編號 1.0
//讀取使用者資料
//ajax
//-------------------------------------------------------

	//---------------------------------------------------
	//輸入 user_id
	//輸出
	//---------------------------------------------------

	//---------------------------------------------------
	//設定與引用
	//---------------------------------------------------

	//SESSION
	@session_start();

	//啟用BUFFER
	@ob_start();

	//外掛設定檔
	require_once(str_repeat("../",3)."/config/config.php");

	 //外掛函式檔
	$funcs=array(
				APP_ROOT.'inc/code',
				APP_ROOT.'lib/php/db/code',
				);
	func_load($funcs,true);


	//清除並停用BUFFER
	@ob_end_clean();

	//建立連線 user
	$conn_user=conn($db_type='mysql',$arry_conn_user);
	$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);




	//-----------------------------------------------
	//通用
	//-----------------------------------------------

	//-------------------------------------------
	//初始化, curl設定
	//-------------------------------------------
		$array =array();
		$array["error"] = "";
		$array["echo"] = "";
		$array["ebook"] = 0;
		$array['status'] = array();
		$array['status']["i_a"] = 0;
		$array['status']["i_f"] = 0;
		$array['status']["i_s"] = 0;
		$array['status']["i_sa"] = 0;
		$array['status']["i_t"] = 0;
		
		//預設權限表
		//a:6:{s:12:"open_publish";i:1;s:30:"read_the_registration_code_pwd";s:3:"t01";s:22:"read_opinion_limit_day";i:14;s:12:"rec_en_input";s:3:"yes";s:13:"rec_draw_open";s:3:"yes";s:9:"coin_open";s:3:"yes";}*/
		$array["auth_open_publish"] = 1;
		$array["auth_read_opinion_limit_day"] = 14;
		$array["auth_rec_en_input"] = "yes";
		$array["auth_rec_draw_open"] = "yes";
		$array["auth_coin_open"] = "yes";
		$array["auth_open_publish_cno"] = 10;
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------

        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$home_id        =(isset($_POST['home_id']))?(int)$_POST['home_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
		$forum_flag=(isset($_POST['forum_flag']))?$_POST['forum_flag']:0;


 		//trim();//去空白


		if($user_id != $_SESSION["uid"] || $user_permission != $_SESSION["permission"])
		{
			$array["error"] .="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------
		$time = date("Y-m-d");
		$array["clerk_talk"] = array("","","","","");
		//搜尋mssr使用者資料
		$sql = "SELECT  `map_item`,
						`box_item`,
						`user_coin`,
						`score_exp`,
						`clerk_talk`,
						`star_style`,
						`star_declaration`,
						`pet_declaration`
				FROM  `mssr_user_info`
				WHERE user_id = $home_id
				";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		$array['map_item'] = $retrun[0]["map_item"];
		$array['box_item'] = $retrun[0]["box_item"];
		$array['user_coin'] = $retrun[0]["user_coin"];
		$array['score_exp'] = $retrun[0]["score_exp"];
		$array['star_style'] = $retrun[0]["star_style"];
		$array['star_declaration'] = $retrun[0]["star_declaration"];
		$array['pet_declaration'] = $retrun[0]["pet_declaration"];
		if($retrun[0]["clerk_talk"] != "")
		{
			$tmp = unserialize($retrun[0]["clerk_talk"]);
			$array['clerk_talk'][0] = gzuncompress(base64_decode($tmp[0]));
			$array['clerk_talk'][1] = gzuncompress(base64_decode($tmp[1]));
			$array['clerk_talk'][2] = gzuncompress(base64_decode($tmp[2]));
			$array['clerk_talk'][3] = gzuncompress(base64_decode($tmp[3]));
			$array['clerk_talk'][4] = gzuncompress(base64_decode($tmp[4]));
		}
		//搜尋使用者基本資料
		$sql = "SELECT `permission`,
					   `sex`,
					   `name`
				FROM  `member`
				WHERE uid = {$home_id}";

		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		$array['user_name'] = $retrun[0]["name"];

		// $textLength= mb_strlen($array['user_name'],"utf-8");
		// echo $textLength<=8 ? $array['user_name'] : mb_substr($array['user_name'],0,8,"utf-8");

		$array['user_sex'] = $retrun[0]["sex"];
		$array['user_permission'] = $retrun[0]["permission"];


		//權限搜尋
		$sql = "SELECT status
				FROM  `permissions`
				WHERE permission = '".$array['user_permission']."'";
		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);



		foreach($retrun as $key1=>$val1)
		{
			if($val1['status'] == "i_f")$array['status']["i_f"] = 1;
			if($val1['status'] == "i_a")$array['status']["i_a"] = 1;
			if($val1['status'] == "i_s")$array['status']["i_s"] = 1;
			if($val1['status'] == "i_sa")$array['status']["i_sa"] = 1;
			if($val1['status'] == "i_t")$array['status']["i_t"] = 1;
			

		}
		//尋找班級資料
		$array['user_class_code'] = array();
		$class_has = false;

		//學生


		$sql = "
				SELECT class.grade,
					   class.grade AS grade_name,
					   class.class_category AS category,
				       class.classroom AS class,
					   semester.semester_code,
					   semester.school_code AS school,
					   me.class_code,
					   class_name.class_name,
					   semester.semester_year AS year,
					   school.school_category,
					   semester.semester_term AS semester

				FROM
				(
					SELECT class_code
					FROM  `student`
					WHERE uid = '$home_id'
					AND '".date("Y-m-d")."' BETWEEN start AND end
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
            UNION
				SELECT class.grade,
					   class.grade AS grade_name,
					   class.class_category AS category,
				       class.classroom AS class,
					   semester.semester_code,
					   semester.school_code AS school,
					   me.class_code,
					   class_name.class_name,
					   semester.semester_year AS year,
					   school.school_category,
					   semester.semester_term AS semester

				FROM
				(
					SELECT class_code
					FROM  `teacher`
					WHERE uid = '$home_id'
					AND '".date("Y-m-d")."' BETWEEN start AND end
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
        ";

		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);

		foreach($retrun as $key1=>$val1)
		{
			//班級資訊
			if($val1["school"] == "pmc")$array["ebook"] = 1;
			$tmp_2 = array(
			"class_code" => $val1["class_code"],
			"school" => $val1["school"],
			"year" => $val1["year"],
			"semester" => $val1["semester"],
			"grade" => $val1["grade"],
			"class" => $val1["class"],
			"school_category" => $val1["school_category"],
			"user_personnel" =>"student");
			$class_has = true;
			array_push($array['user_class_code'],$tmp_2);


			//獲取帶班教師

			$sql = "SELECT uid
					FROM  `teacher`
					WHERE  `class_code` =  '".$val1["class_code"]."'
					AND '$time' BETWEEN start AND end";
			$retrun3 = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);

			if(sizeof($retrun3)>0)
			{//獲取教師設定的權限
				$sql ="SELECT auth
					   FROM  `mssr_auth_user`
					   WHERE user_id = ".$retrun3[0]["uid"].";";
				$retrun4 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
				if(sizeof($retrun4)>0)
				{
					$auth=unserialize($retrun4[0]['auth']);
					if(isset($auth["open_publish"]))$array["auth_open_publish"] = $auth["open_publish"];
					if(isset($auth["read_opinion_limit_day"]))$array["auth_read_opinion_limit_day"] = $auth["read_opinion_limit_day"];
					if(isset($auth["rec_en_input"]))$array["auth_rec_en_input"] = $auth["rec_en_input"];
					if(isset($auth["rec_draw_open"]))$array["auth_rec_draw_open"] = $auth["rec_draw_open"];
					if(isset($auth["coin_open"]))$array["auth_coin_open"] = $auth["coin_open"];
					if(isset($auth["auth_open_publish_cno"]))$array["auth_open_publish_cno"] = $auth_class["open_publish_cno"];

					//============================
					//在這邊核對上架書籍的資訊，不符合者下架
					//============================
					if($_SESSION["uid"] == $home_id ){

						//搜尋有在架上的書籍
						//1:推薦至少兩項(判斷)
						if($array["auth_open_publish"] ==1)
						{

							$sql = "SELECT
								`book_sid`,
								`rec_stat_cno`,
								`rec_draw_cno`,
								`rec_text_cno`,
								`rec_record_cno`
							FROM mssr_rec_book_cno
							WHERE `user_id` ='".$home_id."'
							AND
							book_on_shelf_state = '上架'
							ORDER BY  `mssr_rec_book_cno`.`keyin_cdate` DESC
							";
							$retrun_up = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,10),$arry_conn_mssr);
							foreach($retrun_up as $key_up => $val_up)
							{
								$count = 0;
								// if($val_up["rec_stat_cno"]>0)$count++;

								$sql = "SELECT rec_state
								FROM  `mssr_rec_book_text_log`
								where user_id = ".$home_id."
								AND   book_sid = '".$val_up['book_sid']."'
								ORDER BY keyin_cdate DESC
								";
								$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
								if(sizeof($retrun3)>0 && $retrun3[0]["rec_state"]!= "隱藏" )$count++;

								$sql = "SELECT rec_state
								FROM  `mssr_rec_book_draw_log`
								where user_id = ".$home_id."
								AND   book_sid = '".$val_up['book_sid']."'
								ORDER BY keyin_cdate DESC
								";
								$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
								if(sizeof($retrun3)>0 && $retrun3[0]["rec_state"]!= "隱藏" )$count++;

								$sql = "SELECT rec_state
								FROM  `mssr_rec_book_record_log`
								where user_id = ".$home_id."
								AND   book_sid = '".$val_up['book_sid']."'
								ORDER BY keyin_cdate DESC
								";
								$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
								if(sizeof($retrun3)>0 && $retrun3[0]["rec_state"]!= "隱藏" )$count++;


								//判斷是否有被開放聊書權限.若有開放聊書一樣算進count


								if($forum_flag=="y") {

									//聊書的部分
									$sql_forum = "
									SELECT  count(1) AS count
									FROM `mssr_forum`.`mssr_forum_article`

									LEFT JOIN `mssr_forum`.`mssr_forum_article_book_rev`
									ON `mssr_forum_article_book_rev`.`article_id` = `mssr_forum_article`.`article_id`

									WHERE `mssr_forum_article`.`user_id`=".$home_id."
									AND `mssr_forum_article_book_rev`.`book_sid` = '".$val_up['book_sid']."'

									";

									$retrun_forum = db_result($conn_type='pdo',$conn_mssr,$sql_forum,$arry_limit=array(0,1),$arry_conn_mssr);

									if($retrun_forum[0]["count"] >=1)$count++;

								}
								//若原有架上的書不符合此上架條件.則狀態設為下架

								if($count < 2)
								{
									
									$sql = "UPDATE  `mssr`.`mssr_rec_book_cno`
											SET	`book_on_shelf_state` =  '下架'
											where user_id = ".$home_id."
											AND   book_sid = '".$val_up['book_sid']."'
											;";
									$sql = $sql."UPDATE  `mssr`.`mssr_rec_book_cno_one_week`
											SET	`book_on_shelf_state` =  '下架'
											where user_id = ".$home_id."
											AND   book_sid = '".$val_up['book_sid']."'
											;";
									$sql = $sql."UPDATE  `mssr`.`mssr_rec_book_cno_semester`
											SET	`book_on_shelf_state` =  '下架'
											where user_id = ".$home_id."
											AND   book_sid = '".$val_up['book_sid']."'
											;";
									db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
								}
							}
						}
						//2:老師同意
						else if($array["auth_open_publish"] ==2)
						{
							$sql = "SELECT
								`book_sid`,
								`rec_stat_cno`,
								`rec_draw_cno`,
								`rec_text_cno`,
								`rec_record_cno`,
								`has_publish`
							FROM mssr_rec_book_cno
							WHERE `user_id` ='".$home_id."'
							AND book_on_shelf_state = '上架'
							ORDER BY  `mssr_rec_book_cno`.`keyin_cdate` DESC
							";
							$retrun_up = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,10),$arry_conn_mssr);
							foreach($retrun_up as $key_up => $val_up)
							{
								if($val_up["has_publish"] != "可")
								{
									//設為下架
									$sql = "UPDATE  `mssr`.`mssr_rec_book_cno`
											SET	`book_on_shelf_state` =  '下架'
											where user_id = ".$home_id."
											AND   book_sid = '".$val_up['book_sid']."'
											;";
									$sql = $sql."UPDATE  `mssr`.`mssr_rec_book_cno_one_week`
											SET	`book_on_shelf_state` =  '下架'
											where user_id = ".$home_id."
											AND   book_sid = '".$val_up['book_sid']."'
											;";
									$sql = $sql."UPDATE  `mssr`.`mssr_rec_book_cno_semester`
											SET	`book_on_shelf_state` =  '下架'
											where user_id = ".$home_id."
											AND   book_sid = '".$val_up['book_sid']."'
											;";
									db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
								}
							}
						}
						//3:至少1項為4分
						else if($array["auth_open_publish"] ==3)
						{
							$sql = "SELECT
								`book_sid`
							FROM mssr_rec_book_cno
							WHERE `user_id` ='".$home_id."'
							AND	book_on_shelf_state = '上架'
							ORDER BY  `mssr_rec_book_cno`.`keyin_cdate` DESC
							";
							$retrun_up = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,10),$arry_conn_mssr);
							foreach($retrun_up as $key_up => $val_up)
							{
								$count = 0;
								//教師評論TX
								$sql = "SELECT *
										FROM
										(
											SELECT `comment_score`,`comment_type`,`book_sid`
											FROM `mssr_rec_comment_log`
											WHERE comment_to = ".$home_id."
											AND book_sid = '".$val_up['book_sid']."'
											AND comment_type ='text'
											ORDER BY  `mssr_rec_comment_log`.`keyin_cdate` DESC
										)AS A
										GROUP BY `book_sid`,`comment_type`";
								$retrun7 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
								if(@$retrun7[0]["comment_score"]>=4)$count++;


								//教師評論DR
								$sql = "SELECT *
										FROM
										(
											SELECT  `comment_score`,`comment_type`,`book_sid`
											FROM `mssr_rec_comment_log`
											WHERE comment_to = ".$home_id."
											AND book_sid = '".$val_up['book_sid']."'
											AND comment_type ='draw'
											ORDER BY  `mssr_rec_comment_log`.`keyin_cdate` DESC
										)AS A
										GROUP BY `book_sid`,`comment_type`";
								$retrun7 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
								if(@$retrun7[0]["comment_score"]>=4)$count++;

								//教師評論RE
								$sql = "SELECT *
										FROM
										(
											SELECT  `comment_score`,`comment_type`,`book_sid`
											FROM `mssr_rec_comment_log`
											WHERE comment_to = ".$home_id."
											AND book_sid = '".$val_up['book_sid']."'
											AND comment_type ='record'
											ORDER BY  `mssr_rec_comment_log`.`keyin_cdate` DESC
										)AS A
										GROUP BY `book_sid`,`comment_type`";
								$retrun7 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
								if(@$retrun7[0]["comment_score"]>=4)$count++;

								//若原有架上的書不符合此上架條件.則狀態設為下架
								if($count == 0)
								{
									//設為下架
									$sql = "UPDATE  `mssr`.`mssr_rec_book_cno`
											SET	`book_on_shelf_state` =  '下架'
											where user_id = ".$home_id."
											AND   book_sid = '".$val_up['book_sid']."'
											;";
									$sql = $sql."UPDATE  `mssr`.`mssr_rec_book_cno_one_week`
											SET	`book_on_shelf_state` =  '下架'
											where user_id = ".$home_id."
											AND   book_sid = '".$val_up['book_sid']."'
											;";
									$sql = $sql."UPDATE  `mssr`.`mssr_rec_book_cno_semester`
											SET	`book_on_shelf_state` =  '下架'
											where user_id = ".$home_id."
											AND   book_sid = '".$val_up['book_sid']."'
											;";
									db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
								}
							}
						}
						//4:不管有無聊書.推薦至少三項(不含評星判斷)
						else if($array["auth_open_publish"] ==4)
						{

							$sql = "SELECT
								`book_sid`,
								`rec_stat_cno`,
								`rec_draw_cno`,
								`rec_text_cno`,
								`rec_record_cno`
							FROM mssr_rec_book_cno
							WHERE `user_id` ='".$home_id."'
							AND
							book_on_shelf_state = '上架'
							ORDER BY  `mssr_rec_book_cno`.`keyin_cdate` DESC
							";
							$retrun_up = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,10),$arry_conn_mssr);
							foreach($retrun_up as $key_up => $val_up)
							{
								$count = 0;
								// if($val_up["rec_stat_cno"]>0)$count++;

								$sql = "SELECT rec_state
								FROM  `mssr_rec_book_text_log`
								where user_id = ".$home_id."
								AND   book_sid = '".$val_up['book_sid']."'
								ORDER BY keyin_cdate DESC
								";
								$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
								if(sizeof($retrun3)>0 && $retrun3[0]["rec_state"]!= "隱藏" )$count++;

								$sql = "SELECT rec_state
								FROM  `mssr_rec_book_draw_log`
								where user_id = ".$home_id."
								AND   book_sid = '".$val_up['book_sid']."'
								ORDER BY keyin_cdate DESC
								";
								$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
								if(sizeof($retrun3)>0 && $retrun3[0]["rec_state"]!= "隱藏" )$count++;

								$sql = "SELECT rec_state
								FROM  `mssr_rec_book_record_log`
								where user_id = ".$home_id."
								AND   book_sid = '".$val_up['book_sid']."'
								ORDER BY keyin_cdate DESC
								";
								$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
								if(sizeof($retrun3)>0 && $retrun3[0]["rec_state"]!= "隱藏" )$count++;

								//判斷是否有被開放聊書權限.若有開放聊書一樣算進count


								if($forum_flag=="y") {

									//聊書的部分
									$sql_forum = "
									SELECT  count(1) AS count
									FROM `mssr_forum`.`mssr_forum_article`

									LEFT JOIN `mssr_forum`.`mssr_forum_article_book_rev`
									ON `mssr_forum_article_book_rev`.`article_id` = `mssr_forum_article`.`article_id`

									WHERE `mssr_forum_article`.`user_id`=".$home_id."
									AND `mssr_forum_article_book_rev`.`book_sid` = '".$val_up['book_sid']."'

									";

									$retrun_forum = db_result($conn_type='pdo',$conn_mssr,$sql_forum,$arry_limit=array(0,1),$arry_conn_mssr);

									if($retrun_forum[0]["count"] >=1)$count++;

								}


								//若原有架上的書不符合此上架條件.則狀態設為下架
								
								if($count < 3)
								{
									//設為下架
									$sql = "UPDATE  `mssr`.`mssr_rec_book_cno`
											SET	`book_on_shelf_state` =  '下架'
											where user_id = ".$home_id."
											AND   book_sid = '".$val_up['book_sid']."'
											;";
									$sql = $sql."UPDATE  `mssr`.`mssr_rec_book_cno_one_week`
											SET	`book_on_shelf_state` =  '下架'
											where user_id = ".$home_id."
											AND   book_sid = '".$val_up['book_sid']."'
											;";
									$sql = $sql."UPDATE  `mssr`.`mssr_rec_book_cno_semester`
											SET	`book_on_shelf_state` =  '下架'
											where user_id = ".$home_id."
											AND   book_sid = '".$val_up['book_sid']."'
											;";
									db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
								}
							}
						}
						//5:不管有無聊書.推薦至少四項(不含評星)
						else if($array["auth_open_publish"] ==5)
						{

							$sql = "SELECT
								`book_sid`,
								`rec_stat_cno`,
								`rec_draw_cno`,
								`rec_text_cno`,
								`rec_record_cno`
							FROM mssr_rec_book_cno
							WHERE `user_id` ='".$home_id."'
							AND
							book_on_shelf_state = '上架'
							ORDER BY  `mssr_rec_book_cno`.`keyin_cdate` DESC
							";
							$retrun_up = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,10),$arry_conn_mssr);
							foreach($retrun_up as $key_up => $val_up)
							{
								$count = 0;
								// if($val_up["rec_stat_cno"]>0)$count++;

								$sql = "SELECT rec_state
								FROM  `mssr_rec_book_text_log`
								where user_id = ".$home_id."
								AND   book_sid = '".$val_up['book_sid']."'
								ORDER BY keyin_cdate DESC
								";
								$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
								if(sizeof($retrun3)>0 && $retrun3[0]["rec_state"]!= "隱藏" )$count++;

								$sql = "SELECT rec_state
								FROM  `mssr_rec_book_draw_log`
								where user_id = ".$home_id."
								AND   book_sid = '".$val_up['book_sid']."'
								ORDER BY keyin_cdate DESC
								";
								$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
								if(sizeof($retrun3)>0 && $retrun3[0]["rec_state"]!= "隱藏" )$count++;

								$sql = "SELECT rec_state
								FROM  `mssr_rec_book_record_log`
								where user_id = ".$home_id."
								AND   book_sid = '".$val_up['book_sid']."'
								ORDER BY keyin_cdate DESC
								";
								$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
								if(sizeof($retrun3)>0 && $retrun3[0]["rec_state"]!= "隱藏" )$count++;

								//判斷是否有被開放聊書權限.若有開放聊書一樣算進count

								if($forum_flag=="y") {

									//聊書的部分
									$sql_forum = "
									SELECT  count(1) AS count
									FROM `mssr_forum`.`mssr_forum_article`

									LEFT JOIN `mssr_forum`.`mssr_forum_article_book_rev`
									ON `mssr_forum_article_book_rev`.`article_id` = `mssr_forum_article`.`article_id`

									WHERE `mssr_forum_article`.`user_id`=".$home_id."
									AND `mssr_forum_article_book_rev`.`book_sid` = '".$val_up['book_sid']."'

									";

									$retrun_forum = db_result($conn_type='pdo',$conn_mssr,$sql_forum,$arry_limit=array(0,1),$arry_conn_mssr);

									if($retrun_forum[0]["count"] >=1)$count++;

								}


								//若原有架上的書不符合此上架條件.則狀態設為下架

								if($count < 4)
								{
									//設為下架
									$sql = "UPDATE  `mssr`.`mssr_rec_book_cno`
											SET	`book_on_shelf_state` =  '下架'
											where user_id = ".$home_id."
											AND   book_sid = '".$val_up['book_sid']."'
											;";
									$sql = $sql."UPDATE  `mssr`.`mssr_rec_book_cno_one_week`
											SET	`book_on_shelf_state` =  '下架'
											where user_id = ".$home_id."
											AND   book_sid = '".$val_up['book_sid']."'
											;";
									$sql = $sql."UPDATE  `mssr`.`mssr_rec_book_cno_semester`
											SET	`book_on_shelf_state` =  '下架'
											where user_id = ".$home_id."
											AND   book_sid = '".$val_up['book_sid']."'
											;";
									db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
								}
							}
						}
					}
					//============================
					//============================
				}
			}

			//搜尋班級設定(有則覆蓋老師設定)
			if($val1["school"]!="")
			{
				if($val1["class_code"]!="")
				{
					$sql = "SELECT auth
							FROM  `mssr_auth_class`
							WHERE class_code = '".$val1["class_code"]."'";
					$re_auth = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
					if(count($re_auth)>=1)
					{
						$auth=unserialize($re_auth[0]['auth']);
						if(isset($auth["open_publish"]))$array["auth_open_publish"] = $auth["open_publish"];
						if(isset($auth["read_opinion_limit_day"]))$array["auth_read_opinion_limit_day"] = $auth["read_opinion_limit_day"];
						if(isset($auth["rec_en_input"]))$array["auth_rec_en_input"] = $auth["rec_en_input"];
						if(isset($auth["rec_draw_open"]))$array["auth_rec_draw_open"] = $auth["rec_draw_open"];
						if(isset($auth["coin_open"]))$array["auth_coin_open"] = $auth["coin_open"];
						if(isset($auth["open_publish_cno"]))$array["auth_open_publish_cno"] = $auth["open_publish_cno"];

					}

				}


				//搜尋學校設定(有則覆蓋老師設定)
				$sql = "SELECT auth
						FROM  `mssr_auth_school`
						WHERE school_code = '".$val1["school"]."'";
				$re_auth = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
				if(count($re_auth)>=1)
				{
					$auth=unserialize($re_auth[0]['auth']);
					if($auth["open_publish"])$array["auth_open_publish"] = $auth["open_publish"];
					if($auth["read_opinion_limit_day"])$array["auth_read_opinion_limit_day"] = $auth["read_opinion_limit_day"];
					if($auth["rec_en_input"])$array["auth_rec_en_input"] = $auth["rec_en_input"];
					if($auth["rec_draw_open"])$array["auth_rec_draw_open"] = $auth["rec_draw_open"];
					if($auth["coin_open"])$array["auth_coin_open"] = $auth["coin_open"];
					if($auth["open_publish_cno"])$array["auth_open_publish_cno"] = $auth["open_publish_cno"];
				}
			}

		}


		//老師
		$sql = "
				SELECT class.grade,
					   class.grade AS grade_name,
					   class.class_category AS category,
				       class.classroom AS class,
					   semester.semester_code,
					   semester.school_code AS school,
					   me.class_code,
					   class_name.class_name,
					   semester.semester_year AS year,
					   school.school_category,
					   semester.semester_term AS semester

				FROM
				(
					SELECT class_code
					FROM  `teacher`
					WHERE uid = '$home_id'
					AND '".date("Y-m-d")."' BETWEEN start AND end
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

		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
		foreach($retrun as $key1=>$val1)
		{
			//班級資訊
			if($val1["school"] == "pmc")$array["ebook"] = 1;
			$tmp_2 = array(
			"class_code" => $val1["class_code"],
			"school" => $val1["school"],
			"year" => $val1["year"],
			"semester" => $val1["semester"],
			"grade" => $val1["grade"],
			"class" => $val1["class"],
			"school_category" => $val1["school_category"],
			"user_personnel" =>"teacher");
			$class_has = true;
			array_push($array['user_class_code'],$tmp_2);
		}
		//未有班級
		if(!$class_has)
		{
			$tmp_2 = array(
			"class_code" => "",
			"school" => "",
			"year" => "",
			"semester" => "",
			"grade" => "",
			"class" => "",
			"school_category" => "",
			"user_personnel" =>"");
			array_push($array['user_class_code'],$tmp_2);
		}
		if($_SESSION["permission"] == "super")$array["ebook"] = 1;

		//追加forum 經驗值時用

		$array['forum_open'] = false;
		$array['forum_exe'] = '';


		echo json_encode($array,1);
		?>