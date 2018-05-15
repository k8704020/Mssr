<?php
//-------------------------------------------------------
//得取每一天的閱讀本數、字數 //不重複
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION

        //啟用BUFFER
        @ob_start();
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
        $DOCUMENT_ROOT=str_replace(DIRECTORY_SEPARATOR,'/',$_SERVER['DOCUMENT_ROOT']);

        //外掛設定檔
        require_once("{$DOCUMENT_ROOT}/mssr/config/config.php");
		require_once("{$DOCUMENT_ROOT}/mssr/inc/get_book_info/code.php");

         //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);


        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------


        //建立連線 user
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
 		$conn_user=conn($db_type='mysql',$arry_conn_user);
	//---------------------------------------------------
    //END
    //---------------------------------------------------



	/*
	一、	函數功能：學生最新作品列表(歷程首頁專用)  ORDER BY 登記日期 DESC
	二、	函數名稱：系統名稱_function名稱  ex:ps_abc()
	三、	參數：(學生uid,作品數量)  ex: ps_abc(105,4)
	四、	回傳方式：多維陣列
	*///
	function LHAS_read_list_limit($user_id,$count)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$day="";
		$array_count = -1;
		$data = array();
		$array_select = array('book_name');
		//---------------------------------------------------
		//全域變數宣告
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		$sql = "SELECT borrow_sdate,book_sid
				FROM  `mssr_book_borrow_log`
				WHERE user_id = $user_id
				ORDER BY  `mssr_book_borrow_log`.`borrow_sdate` DESC
				";
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,$count),$arry_conn_mssr);
		foreach($result as $key=>$val)
		{
			$get_book_info=get_book_info($conn='',$val["book_sid"],$array_select,$arry_conn_mssr);
			$data[$key]["book_name"]="";
			if(sizeof($get_book_info))$data[$key]["book_name"] = $get_book_info[0]["book_name"];
			$data[$key]["book_scr"] = "mssr/info/book/".$val["book_sid"]."/img/front/simg/1.jpg";
			if(!file_exists ("/home/www/public_html/".$data[$key]["book_scr"]))$data[$key]["book_scr"]="mssr/service/read_the_registration_v2/img/rec_book.png";
			$data[$key]["book_sid"] = $val["book_sid"];
			$data[$key]["time"] = $val["borrow_sdate"];
		}

		return $data;
	}

	/*
	一、	函數功能：作品列表(學年列表)
	二、	函數名稱：系統名稱_function名稱  ex:ps_abc()
	三、	參數：(學生uid)  ex: ps_abc(105)
	四、	回傳方式：多維陣列
	*/
	function LHAS_get_read_semester($user_id)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$day="";
		$max_year=0;
		$max_semester= 0;
		$max_gread = 0;
		$data = array();
		$array_select = array('book_name');
		//---------------------------------------------------
		//全域變數宣告
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		$sql = "
				SELECT class_code
				FROM  user.`student`
				WHERE uid = $user_id  and student.`start` <= NOW() 
				ORDER BY  `student`.`start` DESC
			   ";
		$result = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
		$tmp = explode("_",@$result[0]["class_code"]);
		$max_year = (int)$tmp[1];
		$max_semester = (int)$tmp[2];
		$max_gread = (int)$tmp[3];

		for($gread = (int)$max_gread,$semester = (int)$max_semester,$year = (int)$max_year ; $gread > 0 ;)
		{
			if($semester == 1)
			{//今年到跨年
				$start = $year."-09-01";
				$end = ($year+1)."-01-31";
			}
			else if($semester == 2)
			{//名年到
				$start = ($year+1)."-02-01";
				$end = ($year+1)."-08-31";
			}

		$sql = "SELECT book_sid
				FROM  `mssr_book_borrow_log`
				WHERE user_id = $user_id
				AND borrow_sdate >= '{$start}'
				AND borrow_sdate <= '{$end}'
				";
			$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);

			if(count($result)>0){
                $data[$year."-".$semester]=(($year-1911)."學年，第".$semester."學期");
            }else{
                //沒資料也回傳當前學期
                $data[$year."-".$semester]=(($year-1911)."學年，第".$semester."學期");
            }

			if($semester == 1)
			{
				$semester = 2;
				$year--;
				$gread--;
			}
			else if($semester == 2)
			{
				$semester = 1;

			}
		}
		return $data;
	}


	/*
	一、	函數功能：作品列表(所有作品)
	二、	函數名稱：系統名稱_function名稱  ex:ps_abc()
	三、	參數：(學生uid,學期)  ex: ps_abc(105,1012);
	四、	回傳方式：多維陣列
	五、	備註:評星，繪圖，文字，錄音 回傳 0(未完成) or 1(已完成)
	*/
	function LHAS_read_info_semester($user_id,$year_semester)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$data = array();
		$array_select = array('book_name');
		//---------------------------------------------------
		//全域變數宣告
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------

		//解析日期範圍
		$tmp = explode("-",$year_semester);
		if($tmp[1] == 1)
		{//今年到跨年
			$start = ((int)$tmp[0])."-09-01";
			$end = (((int)$tmp[0])+1)."-01-31";
		}
		else if($tmp[1] == 2)
		{//名年到
			$start = (((int)$tmp[0])+1)."-02-01";
			$end = (((int)$tmp[0])+1)."-08-31";
		}


	 	$sql = "SELECT rec_stat_cno,
					   rec_draw_cno,
					   rec_text_cno,
					   rec_record_cno,
					   mssr_book_borrow_log.book_sid,
					   mssr_book_borrow_log.borrow_sdate
				FROM  `mssr_book_borrow_log`
				LEFT JOIN `mssr_rec_book_cno`
				ON mssr_book_borrow_log.book_sid = mssr_rec_book_cno.book_sid
				AND mssr_book_borrow_log.user_id = mssr_rec_book_cno.user_id
				WHERE mssr_book_borrow_log.user_id = $user_id
				AND borrow_sdate >= '{$start}'
				AND borrow_sdate <= '{$end}'  ORDER BY `borrow_sdate` DESC
				";
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($result as $key=>$val)
		{

			$val["rec_stat_cno"] == NULL || $val["rec_stat_cno"] == 0 ? $data[$key]["star"] = 0:$data[$key]["star"] = 1;
			$val["rec_draw_cno"] == NULL || $val["rec_draw_cno"] == 0 ? $data[$key]["draw"] = 0:$data[$key]["draw"] = 1;
			$val["rec_text_cno"] == NULL || $val["rec_text_cno"] == 0 ? $data[$key]["text"] = 0:$data[$key]["text"] = 1;
			$val["rec_record_cno"] == NULL || $val["rec_record_cno"] == 0 ? $data[$key]["record"] = 0:$data[$key]["record"] = 1;
			$get_book_info=get_book_info($conn='',$val["book_sid"],$array_select,$arry_conn_mssr);
			$data[$key]["book_name"] = $get_book_info[0]["book_name"];
			$data[$key]["book_scr"] = "mssr/info/book/".$val["book_sid"]."/img/front/simg/1.jpg";
			if(!file_exists ("/home/www/public_html/".$data[$key]["book_scr"]))$data[$key]["book_scr"]="mssr/service/read_the_registration_v2/img/rec_book.png";
			$data[$key]["book_sid"] = $val["book_sid"];
			$data[$key]["time"] = $val["borrow_sdate"];
		}
		return $data;
	}



	function LHAS_read_info_by_day_single($user_id,$sdate,$edate)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$day="";
		$array_count = -1;
		$tmp_data = array();
		$data = array();
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
		$array_select = array('book_page_count','book_word');

		$sql = "SELECT book_sid,LEFT(borrow_sdate,10) as day
				FROM `mssr_book_borrow_log`
				WHERE `user_id` = '{$user_id}' AND `borrow_sdate`>= '{$sdate}' AND `borrow_sdate` <= '{$edate}'
				GROUP BY LEFT(borrow_sdate,10),book_sid
				ORDER BY `borrow_sdate` ASC";
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($result as $key=>$val)
		{
			//$get_book_info=get_book_info($conn='',$val["book_sid"],$array_select,$arry_conn_mssr);
			//---------------------------------------------------
			//tmp_data 紀錄
			//---------------------------------------------------
			if($val["day"]!=$day)
			{
				//換日初始化
				$array_count++;
				//$tmp_data[$array_count]['read_word']= 0;
				$tmp_data[$array_count]['read_count']= 0;
				//$tmp_data[$array_count]['read_ok_count']= 0;
				$tmp_data[$array_count]['day']= $day = $val["day"];//紀錄日
			}


			//$tmp_data[$array_count]['read_word']=$get_book_info[0]["book_word"]+$tmp_data[$array_count]['read_word']; //紀錄總字數
			$tmp_data[$array_count]['read_count']=$tmp_data[$array_count]['read_count']+1;//紀錄筆數
			//if($get_book_info[0]["book_word"]>10)$tmp_data[$array_count]['read_ok_count']=$tmp_data[$array_count]['read_ok_count']+1;//紀錄有效筆數

			//---------------------------------------------------
			//END
			//---------------------------------------------------
		}
		//---------------------------------------------------
		//轉換輸出  (指標用)
		//---------------------------------------------------
		foreach($tmp_data as $key=>$val)
		{
			$tmp = array();
			$tmp['uid'] = $user_id;
			$tmp['show_date'] = $val['day'];
			$tmp['read_count'] = $val['read_count'];

			//計算有效字數
			// if($val['read_ok_count']>0)
			// {
			// 	$tmp['read_word'] = $val['read_word']/$val['read_ok_count']*$val['read_count'];
			// }
			// else
			// {
			// 	$tmp['read_word'] = 0;
			// }

			array_push($data,$tmp);
		}

		//---------------------------------------------------
		//END
		//---------------------------------------------------
		return $data;
	}

	function LHAS_read_repeat_info_by_day_single($user_id,$sdate,$edate)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$day="";
		$array_count = -1;
		$tmp_data = array();
		$data = array();
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
		$array_select = array('book_page_count','book_word');

		$sql = "SELECT book_sid,LEFT(borrow_sdate,10) as day
				FROM `mssr_book_borrow_log`
				WHERE `user_id` = '{$user_id}' AND `borrow_sdate`>= '{$sdate}' AND `borrow_sdate` <= '{$edate}'
				ORDER BY `borrow_sdate` ASC";
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($result as $key=>$val)
		{
			$get_book_info=get_book_info($conn='',$val["book_sid"],$array_select,$arry_conn_mssr);
			//---------------------------------------------------
			//tmp_data 紀錄
			//---------------------------------------------------
			if($val["day"]!=$day)
			{
				//換日初始化
				$array_count++;
				$tmp_data[$array_count]['read_word']= 0;
				$tmp_data[$array_count]['read_count']= 0;
				$tmp_data[$array_count]['read_ok_count']= 0;
				$tmp_data[$array_count]['day']= $day = $val["day"];//紀錄日
			}

			$tmp_data[$array_count]['read_word']=$get_book_info[0]["book_word"]+$tmp_data[$array_count]['read_word']; //紀錄總字數
			$tmp_data[$array_count]['read_count']=$tmp_data[$array_count]['read_count']+1;//紀錄筆數
			if($get_book_info[0]["book_word"]>10)$tmp_data[$array_count]['read_ok_count']=$tmp_data[$array_count]['read_ok_count']+1;//紀錄有效筆數
			//---------------------------------------------------
			//END
			//---------------------------------------------------
		}
		//---------------------------------------------------
		//轉換輸出  (指標用)
		//---------------------------------------------------
		foreach($tmp_data as $key=>$val)
		{
			$tmp = array();
			$tmp['uid'] = $user_id;
			$tmp['show_date'] = $val['day'];
			$tmp['read_count'] = $val['read_count'];

			//計算有效字數
			if($val['read_ok_count']>0)
			{
				$tmp['read_word'] = $val['read_word']/$val['read_ok_count']*$val['read_count'];
			}
			else
			{
				$tmp['read_word'] = 0;
			}

			array_push($data,$tmp);
		}
		//---------------------------------------------------
		//END
		//---------------------------------------------------
		return $data;
	}

	function LHAS_read_info_by_local_single($user_id,$sdate,$edate)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$day="";
		$array_count = 0;
		$tmp_data = array();
		$data = array();
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
		$array_select = array('book_page_count','book_word');

		$sql = "SELECT book_sid as 'read_count'
		 		FROM `mssr_book_borrow_log`
		 		WHERE `user_id` = '{$user_id}' AND `borrow_sdate`>= '{$sdate}' AND `borrow_sdate` <= '{$edate}'
                GROUP BY `book_sid`
		 		ORDER BY `borrow_sdate` ASC";

		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		$tmp = array();
		$tmp['uid'] = $user_id;
		$tmp['read_count'] = count($result);
		$data[]=$tmp;
// --------old---
// 		$sql = "SELECT book_sid
// 				FROM `mssr_book_borrow_log`
// 				WHERE `user_id` = '{$user_id}' AND `borrow_sdate`>= '{$sdate}' AND `borrow_sdate` <= '{$edate}'
//                 GROUP BY `book_sid`
// 				ORDER BY `borrow_sdate` ASC";
// 		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
// 		foreach($result as $key=>$val)
// 		{
// 			$get_book_info=get_book_info($conn='',$val["book_sid"],$array_select,$arry_conn_mssr);
// 			//---------------------------------------------------
// 			//tmp_data 紀錄
// 			//---------------------------------------------------
// 			$tmp_data[$array_count]['read_word']=$get_book_info[0]["book_word"]+$tmp_data[$array_count]['read_word']; //紀錄總字數
// 			$tmp_data[$array_count]['read_count']=$tmp_data[$array_count]['read_count']+1;//紀錄筆數
// 			if($get_book_info[0]["book_word"]>10)$tmp_data[$array_count]['read_ok_count']=$tmp_data[$array_count]['read_ok_count']+1;//紀錄有效筆數
// 			//---------------------------------------------------
// 			//END
// 			//---------------------------------------------------
// 		}
// 		//---------------------------------------------------
// 		//轉換輸出  (指標用)
// 		//---------------------------------------------------
// 		foreach($tmp_data as $key=>$val)
// 		{
// 			$tmp = array();
// 			$tmp['uid'] = $user_id;
// 			$tmp['read_count'] = $val['read_count'];

		// 	//計算有效字數
		// 	if($val['read_ok_count']>0)
		// 	{
		// 		$tmp['read_word'] = $val['read_word']/$val['read_ok_count']*$val['read_count'];
		// 	}
		// 	else
		// 	{
		// 		$tmp['read_word'] = 0;
		// 	}
		// 	array_push($data,$tmp);
		// }
		//---------------------------------------------------
		//END
		//---------------------------------------------------
//--------old----------------
		return $data;
	}

	function LHAS_rec_info_by_day_single($user_id,$sdate,$edate)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$day="";
		$array_count = -1;
		$tmp_data = array();
		$data = array();
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
		$array_select = array('book_page_count','book_word');

		$sql = "SELECT LEFT(keyin_mdate,10) AS day,rec_stat_cno,rec_draw_cno,rec_text_cno,rec_record_cno
				FROM `mssr_rec_book_cno`
				WHERE `user_id` = '{$user_id}' AND `keyin_mdate`>= '{$sdate}' AND `keyin_mdate` <= '{$edate}'
				ORDER BY `keyin_mdate` ASC";
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($result as $key=>$val)
		{
			//---------------------------------------------------
			//tmp_data 紀錄
			//---------------------------------------------------
			if($val["day"]!=$day)
			{
				//換日初始化
				$array_count++;
				$tmp_data[$array_count]['rec_stat_cno']= 0;
				$tmp_data[$array_count]['rec_draw_cno']= 0;
				$tmp_data[$array_count]['rec_text_cno']= 0;
				$tmp_data[$array_count]['rec_record_cno']= 0;
				$tmp_data[$array_count]['rec_count']= 0;
				$tmp_data[$array_count]['day']= $day = $val["day"];//紀錄日
			}

			if($val["rec_stat_cno"]>0)$tmp_data[$array_count]['rec_stat_cno']=$tmp_data[$array_count]['rec_stat_cno']+1;
			if($val["rec_draw_cno"]>0)$tmp_data[$array_count]['rec_draw_cno']=$tmp_data[$array_count]['rec_draw_cno']+1;
			if($val["rec_text_cno"]>0)$tmp_data[$array_count]['rec_text_cno']=$tmp_data[$array_count]['rec_text_cno']+1;
			if($val["rec_record_cno"]>0)$tmp_data[$array_count]['rec_record_cno']=$tmp_data[$array_count]['rec_record_cno']+1;
			$tmp_data[$array_count]['rec_count']=$tmp_data[$array_count]['rec_count']+1;

			//---------------------------------------------------
			//END
			//---------------------------------------------------
		}
		//---------------------------------------------------
		//轉換輸出  (指標用)
		//---------------------------------------------------
		foreach($tmp_data as $key=>$val)
		{
			$tmp = array();
			$tmp['uid'] = $user_id;
			$tmp['show_date'] = $val['day'];
			$tmp['rec_count'] = $val['rec_count'];
			$tmp['rec_stat_cno'] = $val['rec_stat_cno'];
			$tmp['rec_draw_cno'] = $val['rec_draw_cno'];
			$tmp['rec_text_cno'] = $val['rec_text_cno'];
			$tmp['rec_record_cno'] = $val['rec_record_cno'];
			$tmp['cno_count'] = $val['rec_stat_cno']+$val['rec_draw_cno']+$val['rec_text_cno']+$val['rec_record_cno'];
			array_push($data,$tmp);
		}

		//---------------------------------------------------
		//END
		//---------------------------------------------------
		return $data;
	}

	//次數 - 單日
	function LHAS_rec_info_buot_by_day_single($user_id,$sdate,$edate)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$day="";
		$array_count = -1;
		$tmp_data = array();
		$data = array();
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
		$array_select = array('book_page_count','book_word');

		$sql = "SELECT LEFT(keyin_mdate,10) AS day,rec_stat_cno,rec_draw_cno,rec_text_cno,rec_record_cno
				FROM `mssr_rec_book_cno`
				WHERE `user_id` = '{$user_id}' AND `keyin_mdate`>= '{$sdate}' AND `keyin_mdate` <= '{$edate}'
				ORDER BY `keyin_mdate` ASC";
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($result as $key=>$val)
		{
			//---------------------------------------------------
			//tmp_data 紀錄
			//---------------------------------------------------
			if($val["day"]!=$day)
			{
				//換日初始化
				$array_count++;
				$tmp_data[$array_count]['rec_stat_cno']= 0;
				$tmp_data[$array_count]['rec_draw_cno']= 0;
				$tmp_data[$array_count]['rec_text_cno']= 0;
				$tmp_data[$array_count]['rec_record_cno']= 0;
				$tmp_data[$array_count]['rec_count']= 0;
				$tmp_data[$array_count]['day']= $day = $val["day"];//紀錄日
			}

			if($val["rec_stat_cno"]>0)$tmp_data[$array_count]['rec_stat_cno']=$tmp_data[$array_count]['rec_stat_cno']+$val["rec_stat_cno"];
			if($val["rec_stat_cno"]>0)$tmp_data[$array_count]['rec_draw_cno']=$tmp_data[$array_count]['rec_draw_cno']+$val["rec_stat_cno"];
			if($val["rec_text_cno"]>0)$tmp_data[$array_count]['rec_text_cno']=$tmp_data[$array_count]['rec_text_cno']+$val["rec_text_cno"];
			if($val["rec_record_cno"]>0)$tmp_data[$array_count]['rec_record_cno']=$tmp_data[$array_count]['rec_record_cno']+$val["rec_record_cno"];
			$tmp_data[$array_count]['rec_count']=$tmp_data[$array_count]['rec_count']+$val["rec_stat_cno"]+$val["rec_stat_cno"]+$val["rec_text_cno"]+$val["rec_record_cno"];

			//---------------------------------------------------
			//END
			//---------------------------------------------------
		}
		//---------------------------------------------------
		//轉換輸出  (指標用)
		//---------------------------------------------------
		foreach($tmp_data as $key=>$val)
		{
			$tmp = array();
			$tmp['uid'] = $user_id;
			$tmp['show_date'] = $val['day'];
			$tmp['rec_count'] = $val['rec_count'];
			$tmp['rec_stat_cno'] = $val['rec_stat_cno'];
			$tmp['rec_draw_cno'] = $val['rec_draw_cno'];
			$tmp['rec_text_cno'] = $val['rec_text_cno'];
			$tmp['rec_record_cno'] = $val['rec_record_cno'];

			array_push($data,$tmp);
		}

		//---------------------------------------------------
		//END
		//---------------------------------------------------
		return $data;
	}

	function LHAS_rec_info_by_local_single($user_id,$sdate,$edate)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$day="";
		$array_count = 0;
		$tmp_data = array();
		$data = array();
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
		$array_select = array('book_page_count','book_word');

		$sql = "SELECT LEFT(keyin_mdate,10) AS day,rec_stat_cno,rec_draw_cno,rec_text_cno,rec_record_cno
				FROM `mssr_rec_book_cno`
				WHERE `user_id` = '{$user_id}' AND `keyin_mdate`>= '{$sdate}' AND `keyin_mdate` <= '{$edate}'
				ORDER BY `keyin_mdate` ASC";
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($result as $key=>$val)
		{
			//---------------------------------------------------
			//tmp_data 紀錄
			//---------------------------------------------------
			if($val["rec_stat_cno"]>0)$tmp_data[$array_count]['rec_stat_cno']=$tmp_data[$array_count]['rec_stat_cno']+1;
			if($val["rec_draw_cno"]>0)$tmp_data[$array_count]['rec_draw_cno']=$tmp_data[$array_count]['rec_draw_cno']+1;
			if($val["rec_text_cno"]>0)$tmp_data[$array_count]['rec_text_cno']=$tmp_data[$array_count]['rec_text_cno']+1;
			if($val["rec_record_cno"]>0)$tmp_data[$array_count]['rec_record_cno']=$tmp_data[$array_count]['rec_record_cno']+1;
			$tmp_data[$array_count]['rec_count']=$tmp_data[$array_count]['rec_count']+1;
			//---------------------------------------------------
			//END
			//---------------------------------------------------
		}
		//---------------------------------------------------
		//轉換輸出  (指標用)
		//---------------------------------------------------
		foreach($tmp_data as $key=>$val)
		{
			$tmp = array();
			$tmp['uid'] = $user_id;
			$tmp['rec_count'] = $val['rec_count'];
			$tmp['rec_stat_cno'] = $val['rec_stat_cno'];
			$tmp['rec_draw_cno'] = $val['rec_draw_cno'];
			$tmp['rec_text_cno'] = $val['rec_text_cno'];
			$tmp['rec_record_cno'] = $val['rec_record_cno'];
			$tmp['cno_count'] = $val['rec_stat_cno']+$val['rec_draw_cno']+$val['rec_text_cno']+$val['rec_record_cno'];

			array_push($data,$tmp);
		}

		//---------------------------------------------------
		//END
		//---------------------------------------------------
		return $data;
	}

	//推薦(次)
	function LHAS_rec_info_buot_by_local_single($user_id,$sdate,$edate)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$day="";
		$array_count = 0;
		$tmp_data = array();
		$data = array();
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
		$array_select = array('book_page_count','book_word');

		$sql = "SELECT LEFT(keyin_mdate,10) AS day,rec_stat_cno,rec_draw_cno,rec_text_cno,rec_record_cno
				FROM `mssr_rec_book_cno`
				WHERE `user_id` = '{$user_id}' AND `keyin_mdate`>= '{$sdate}' AND `keyin_mdate` <= '{$edate}'
				ORDER BY `keyin_mdate` ASC";
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($result as $key=>$val)
		{
			//---------------------------------------------------
			//tmp_data 紀錄
			//---------------------------------------------------
			if($val["rec_stat_cno"]>0)$tmp_data[$array_count]['rec_stat_cno']=$tmp_data[$array_count]['rec_stat_cno']+$val["rec_stat_cno"];
			if($val["rec_draw_cno"]>0)$tmp_data[$array_count]['rec_draw_cno']=$tmp_data[$array_count]['rec_draw_cno']+$val["rec_draw_cno"];
			if($val["rec_text_cno"]>0)$tmp_data[$array_count]['rec_text_cno']=$tmp_data[$array_count]['rec_text_cno']+$val["rec_text_cno"];
			if($val["rec_record_cno"]>0)$tmp_data[$array_count]['rec_record_cno']=$tmp_data[$array_count]['rec_record_cno']+$val["rec_record_cno"];
			$tmp_data[$array_count]['rec_count']=$tmp_data[$array_count]['rec_count']+$val["rec_stat_cno"]+$val["rec_draw_cno"]+$val["rec_text_cno"]+$val["rec_record_cno"];

			//---------------------------------------------------
			//END
			//---------------------------------------------------
		}
		//---------------------------------------------------
		//轉換輸出  (指標用)
		//---------------------------------------------------
		foreach($tmp_data as $key=>$val)
		{
			$tmp = array();
			$tmp['uid'] = $user_id;
			$tmp['rec_count'] = $val['rec_count'];
			$tmp['rec_stat_cno'] = $val['rec_stat_cno'];
			$tmp['rec_draw_cno'] = $val['rec_draw_cno'];
			$tmp['rec_text_cno'] = $val['rec_text_cno'];
			$tmp['rec_record_cno'] = $val['rec_record_cno'];

			array_push($data,$tmp);
		}

		//---------------------------------------------------
		//END
		//---------------------------------------------------
		return $data;
	}

	//老師評語(本)
	function LHAS_rec_comment_by_local_single($user_id,$sdate,$edate)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$day="";
		$array_count = 0;
		$tmp_data = array();
		$data = array();
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
		//$array_select = array('book_page_count','book_word');

		$sql = "SELECT keyin_cdate,LEFT(rec_sid,4) AS rec_sid_4,comment_type,comment_score,rec_sid
				FROM `mssr_rec_comment_log`
				WHERE `comment_to` = '{$user_id}' AND `keyin_cdate`>= '{$sdate}' AND DATE_FORMAT(`keyin_cdate`,'%Y-%m-%d') <= '{$edate}'
				ORDER BY  `mssr_rec_comment_log`.`keyin_cdate` DESC
				";
		$tmp = array();
		$comment_number = 0;
		$comment_count = 0;
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($result as $key=>$val)
		{

			//填入書SID
			if($val['rec_sid_4'] == "mrbd" )
			{
				$sql = "SELECT book_sid
						FROM  `mssr_rec_book_draw_log`
						WHERE `rec_sid` = '".$val["rec_sid"]."'
						";
			}
			else if($val['rec_sid_4'] == "mrbr" )
			{
				$sql = "SELECT book_sid
						FROM  `mssr_rec_book_record_log`
						WHERE `rec_sid` = '".$val["rec_sid"]."'
						";
			}
			else if($val['rec_sid_4'] == "mrbt" )
			{
				$sql = "SELECT book_sid
						FROM  `mssr_rec_book_text_log`
						WHERE `rec_sid` = '".$val["rec_sid"]."'
						";
			}
			$result_tmp = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
			if(!isset($tmp[$result_tmp[0]["book_sid"]][$val["comment_type"]]) || $tmp[$result_tmp[0]["book_sid"]][$val["comment_type"]]==NULL ||$tmp[$result_tmp[0]["book_sid"]][$val["comment_type"]] < $val["keyin_cdate"])
			{
				$tmp[$result_tmp[0]["book_sid"]][$val["comment_type"]] = $val["keyin_cdate"];
				$comment_number = $comment_number + $val["comment_score"];
				$comment_count ++;
			}

		}

		//---------------------------------------------------
		//轉換輸出  (指標用)
		//---------------------------------------------------

			$tmp = array();
			$tmp['uid'] = $user_id;
			if($comment_count == 0)
			{
				$tmp['comment_average'] = -1;
			}
			else
			{
				$tmp['comment_average'] = $comment_number / $comment_count;
			}
			$tmp['comment_count'] = $comment_count;

			array_push($data,$tmp);


		//---------------------------------------------------
		//END
		//---------------------------------------------------
		return $data;
	}
	function by_multi($class_id,$sdate,$edate,$type)
	{
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
		$data = array();
		$sql = "SELECT uid
				FROM  `student`
				WHERE  `class_code` LIKE  '{$class_id}'
				and `end`=(SELECT a.`end`
						FROM `semester` AS a
						INNER JOIN `class` AS b ON a.`semester_code` = b.`semester_code`
						WHERE b.`class_code` = '{$class_id}')
				ORDER BY  `student`.`uid` ASC
				";
		$result = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
		foreach($result as $key=>$val)
		{
			//echo $val['uid'];
			if($type == 1)$tmp_data = LHAS_read_info_by_day_single($val['uid'],$sdate,$edate);
			if($type == 2)$tmp_data = LHAS_read_repeat_info_by_day_single($val['uid'],$sdate,$edate);
			if($type == 3)$tmp_data = LHAS_read_info_by_local_single($val['uid'],$sdate,$edate);
			if($type == 4)$tmp_data = LHAS_rec_info_by_day_single($val['uid'],$sdate,$edate);
			if($type == 5)$tmp_data = LHAS_rec_info_by_local_single($val['uid'],$sdate,$edate);
			if($type == 6)$tmp_data = LHAS_rec_comment_by_local_single($val['uid'],$sdate,$edate);
			foreach($tmp_data as $key2=>$val2)
			{
				array_push($data,$tmp_data[$key2]);
			}
		}
		return $data;
	}

	function LHAS_read_info_by_day_multi($class_id,$sdate,$edate)
	{
		return by_multi($class_id,$sdate,$edate,1);
	}
	function LHAS_read_repeat_info_by_day_multi($class_id,$sdate,$edate)
	{
		return by_multi($class_id,$sdate,$edate,2);
	}
	function LHAS_read_info_by_local_multi($class_id,$sdate,$edate)
	{
		return by_multi($class_id,$sdate,$edate,3);
	}
	function LHAS_rec_info_by_day_multi($class_id,$sdate,$edate)
	{
		return by_multi($class_id,$sdate,$edate,4);
	}
	function LHAS_rec_info_by_local_multi($class_id,$sdate,$edate)
	{
		return by_multi($class_id,$sdate,$edate,5);
	}
	function LHAS_rec_comment_by_local_multi($class_id,$sdate,$edate)
	{
		return by_multi($class_id,$sdate,$edate,6);
	}


	//------------------------------------------------------------
	//使用範例
	//------------------------------------------------------------
	/*
		$user_id = 2029;
		$sdate = '2013-11-01';
		$edate = '2013-12-23';
		$class_id = "gcp_2013_1_3_1";
	*/

	//print_r(LHAS_read_info_by_day_single($user_id,$sdate,$edate));  // 閱讀  單人 本數/字數  不重複   每日
	//print_r(LHAS_read_repeat_info_by_day_single($user_id,$sdate,$edate)); // 閱讀  單人  本數/字數  重複   每日
	//print_r(LHAS_read_info_by_local_single($user_id,$sdate,$edate)); // 閱讀  單人  本數/字數  不重複   區間

	//print_r(LHAS_rec_comment_by_local_single($user_id,$sdate,$edate));//  評分  單人  總平均分數/評星(平均分數)/繪圖(平均分數)/文字(平均分數)/錄音(平均分數)  不重複   區間
	//print_r(LHAS_rec_comment_by_local_multi($class_id,$sdate,$edate));//  評分  班級  總平均分數/評星(平均分數)/繪圖(平均分數)/文字(平均分數)/錄音(平均分數)  不重複   區間


	//print_r(LHAS_rec_info_by_day_single($user_id,$sdate,$edate));  // 推薦  單人  總本數/評星(本)/繪圖(本)/文字(本)/錄音(本)/4推薦累積  不重複   每日
	//print_r(LHAS_rec_info_by_local_single($user_id,$sdate,$edate)); // 推薦  單人  總本數/評星(本)/繪圖(本)/文字(本)/錄音(本)/4推薦累積  不重複   區間

	//print_r(LHAS_rec_info_buot_by_day_single($user_id,$sdate,$edate));  // 推薦  單人  總次數/評星(次)/繪圖(次)/文字(次)/錄音(次)  不重複   每日
	//print_r(LHAS_rec_info_buot_by_local_single($user_id,$sdate,$edate)); // 推薦  單人  總次數/評星(次)/繪圖(次)/文字(次)/錄音(次)  不重複   區間


	//print_r(LHAS_read_info_by_day_multi($class_id,$sdate,$edate));  // 閱讀  班級 本數/字數  不重複   每日
	//print_r(LHAS_read_repeat_info_by_day_multi($class_id,$sdate,$edate)); // 閱讀  班級  本數/字數  重複   每日
	//print_r(LHAS_read_info_by_local_multi($class_id,$sdate,$edate)); // 閱讀  班級  本數/字數  不重複   區間

	//print_r(LHAS_rec_info_by_day_multi($class_id,$sdate,$edate));  // 推薦  班級  總本數/評星(本)/繪圖(本)/文字(本)/錄音(本)/4推薦累積  不重複   每日
	//print_r(LHAS_rec_info_by_local_multi($class_id,$sdate,$edate)); // 推薦  班級  總本數/評星(本)/繪圖(本)/文字(本)/錄音(本)/4推薦累積  不重複   區間

//****Jose↓↓↓

	//閱讀登記紀錄(區間)
	function LHAS_read_info_record_interval($user_id,$sdate,$edate)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$data = array();
		$array_select = array('book_name');
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;

		$sql = "SELECT book_sid ,`borrow_sdate`
		 		FROM `mssr_book_borrow_log`
		 		WHERE `user_id` = '{$user_id}' AND `borrow_sdate` >= '{$sdate}' AND SUBSTR(`borrow_sdate`,1,10) <= '{$edate}'
		 		ORDER BY `borrow_sdate` DESC";
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

		foreach($result as $key=>$val)
		{
			$get_book_info=get_book_info($conn='',$val["book_sid"],$array_select,$arry_conn_mssr);
			$data[$key]["book_name"] = $get_book_info[0]["book_name"];
			$data[$key]["book_scr"] = "mssr/info/book/".$val["book_sid"]."/img/front/simg/1.jpg";
			if(!file_exists ("/home/www/public_html/".$data[$key]["book_scr"]))$data[$key]["book_scr"]="mssr/service/read_the_registration_v2/img/rec_book.png";
			$data[$key]["book_sid"] = $val["book_sid"];
			$data[$key]["time"] = $val["borrow_sdate"];
		}
		return $data;
	}
	function LHAS_read_info_record_limit($user_id,$sdate,$semesterStart,$limit)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$data = array();
		$array_select = array('book_name');
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;

		$sql = "SELECT book_sid ,`borrow_sdate`
		 		FROM `mssr_book_borrow_log`
		 		WHERE `user_id` = '{$user_id}' AND `borrow_sdate` >= '{$semesterStart}' AND SUBSTR(`borrow_sdate`,1,10) <= '{$sdate}'
		 		ORDER BY `borrow_sdate` DESC limit 0,$limit";
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

		foreach($result as $key=>$val)
		{
			$get_book_info=get_book_info($conn='',$val["book_sid"],$array_select,$arry_conn_mssr);
			$data[$key]["book_name"] = $get_book_info[0]["book_name"];
			$data[$key]["book_scr"] = "mssr/info/book/".$val["book_sid"]."/img/front/simg/1.jpg";
			if(!file_exists ("/home/www/public_html/".$data[$key]["book_scr"]))$data[$key]["book_scr"]="mssr/service/read_the_registration_v2/img/rec_book.png";
			$data[$key]["book_sid"] = $val["book_sid"];
			$data[$key]["time"] = $val["borrow_sdate"];
		}
		return $data;
	}


	//閱讀登記紀錄
	function LHAS_read_info_record($user_id,$year_semester,$school)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$data = array();
		$array_select = array('book_name');
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;

		//解析日期範圍
		$tmp = explode("-",$year_semester);
		$semester=$tmp[0];
		$term=$tmp[1];
		$data_sql = "SELECT `start`,`end`
				FROM  `semester`
				WHERE  `semester_year` = $semester
				AND  `semester_term` = $term
				AND  `school_code` =  '{$school}'";
		$d_result = db_result($conn_type='pdo',$conn_user,$data_sql,$arry_limit=array(),$arry_conn_user);
		$start = $d_result[0]['start'];
		$end = $d_result[0]['end'];

		$sql = "SELECT book_sid ,`borrow_sdate`,count(`book_sid`) as count
		 		FROM (SELECT * FROM `mssr_book_borrow_log`
		 			WHERE `user_id` = '{$user_id}' AND `borrow_sdate`>= '{$start}' AND SUBSTR(`borrow_sdate`,1,10) <= '{$end}'
		 			ORDER BY `borrow_sdate` DESC) as t1
                GROUP BY t1.`book_sid`
		 		ORDER BY t1.`borrow_sdate` DESC";
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

		foreach($result as $key=>$val)
		{
			$get_book_info=get_book_info($conn='',$val["book_sid"],$array_select,$arry_conn_mssr);
			$data[$key]["book_name"] = $get_book_info[0]["book_name"];
			$data[$key]["book_scr"] = "mssr/info/book/".$val["book_sid"]."/img/front/simg/1.jpg";
			if(!file_exists ("/home/www/public_html/".$data[$key]["book_scr"]))$data[$key]["book_scr"]="mssr/service/read_the_registration_v2/img/rec_book.png";
			$data[$key]["book_sid"] = $val["book_sid"];
			$data[$key]["time"] = $val["borrow_sdate"];
			$data[$key]["count"] = $val["count"];
		}
		return $data;
	}

	//閱讀登記紀錄
	function LHAS_read_info_record_mobileParents($user_id,$page,$limit)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$data = array();
		$array_select = array('book_name');

		$pageLimit = $limit;
		$pageStart= ($page-1) * $pageLimit;

		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;


		$sql = "SELECT book_sid ,`borrow_sdate`,count(`book_sid`) as count
		 		FROM (SELECT * FROM `mssr_book_borrow_log`
		 			WHERE `user_id` = '{$user_id}'
		 			ORDER BY `borrow_sdate` DESC) as t1
                GROUP BY t1.`book_sid`
		 		ORDER BY t1.`borrow_sdate` DESC";
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array($pageStart,$pageLimit),$arry_conn_mssr);

		foreach($result as $key=>$val)
		{
			$get_book_info=get_book_info($conn='',$val["book_sid"],$array_select,$arry_conn_mssr);
			$data[$key]["book_name"] = $get_book_info[0]["book_name"];
			$data[$key]["book_scr"] = "mssr/info/book/".$val["book_sid"]."/img/front/simg/1.jpg";
			if(!file_exists ("/home/www/public_html/".$data[$key]["book_scr"]))$data[$key]["book_scr"]="mssr/service/read_the_registration_v2/img/rec_book.png";
			$data[$key]["book_sid"] = $val["book_sid"];
			$data[$key]["time"] = $val["borrow_sdate"];
			$data[$key]["count"] = $val["count"];
		}
		return $data;
	}

	//閱讀登記數量
	function LHAS_read_info_count($user_id,$start,$end)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$data = array();
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;

		$sql = "SELECT COUNT(*) AS count
				FROM  `mssr_book_borrow_log`
				WHERE  `user_id` = '{$user_id}'
				AND DATE(`borrow_sdate`) between '{$start}' and '{$end}'";
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

		return $result[0]['count'];
	}

	//閱讀推薦紀錄
	function LHAS_read_rec_record($user_id,$year_semester,$school)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$data = array();
		$array_select = array('book_name');
		//---------------------------------------------------
		//全域變數宣告
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------

		//解析日期範圍
		$tmp = explode("-",$year_semester);
		$semester=$tmp[0];
		$term=$tmp[1];
		$data_sql = "SELECT `start`,`end`
				FROM  `semester`
				WHERE  `semester_year` = $semester
				AND  `semester_term` = $term
				AND  `school_code` =  '{$school}'";
		$d_result = db_result($conn_type='pdo',$conn_user,$data_sql,$arry_limit=array(),$arry_conn_user);
		$start = $d_result[0]['start'];
		$end = $d_result[0]['end'];

	 	$sql = "SELECT rec_stat_cno,
					   rec_draw_cno,
					   rec_text_cno,
					   rec_record_cno,
					   book_sid,
					   keyin_cdate,
					   keyin_mdate
				FROM  `mssr_rec_book_cno`
				WHERE `user_id` = $user_id
				AND `keyin_cdate` >= '{$start}'
				AND SUBSTR(`keyin_cdate`,1,10) <= '{$end}'  ORDER BY `keyin_cdate` DESC";
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		foreach($result as $key=>$val)
		{
			$val["rec_stat_cno"] == NULL || $val["rec_stat_cno"] == 0 ? $data[$key]["star"] = 0:$data[$key]["star"] = 1;
			$val["rec_draw_cno"] == NULL || $val["rec_draw_cno"] == 0 ? $data[$key]["draw"] = 0:$data[$key]["draw"] = 1;
			$val["rec_text_cno"] == NULL || $val["rec_text_cno"] == 0 ? $data[$key]["text"] = 0:$data[$key]["text"] = 1;
			$val["rec_record_cno"] == NULL || $val["rec_record_cno"] == 0 ? $data[$key]["record"] = 0:$data[$key]["record"] = 1;
			$get_book_info=get_book_info($conn='',$val["book_sid"],$array_select,$arry_conn_mssr);
			$data[$key]["book_name"] = $get_book_info[0]["book_name"];
			$data[$key]["book_scr"] = "mssr/info/book/".$val["book_sid"]."/img/front/simg/1.jpg";
			if(!file_exists ("/home/www/public_html/".$data[$key]["book_scr"]))$data[$key]["book_scr"]="mssr/service/read_the_registration_v2/img/rec_book.png";
			$data[$key]["book_sid"] = $val["book_sid"];
			$data[$key]["cdate"] = $val["keyin_cdate"];
			$data[$key]["mdate"] = $val["keyin_mdate"];
		}
		return $data;
	}

	//閱讀推薦(limit數量)
	function LHAS_read_rec_limit($user_id,$count)
	{
		//---------------------------------------------------
		//初始計算數值
		//---------------------------------------------------
		$day="";
		$array_count = -1;
		$data = array();
		$array_select = array('book_name');
		//---------------------------------------------------
		//全域變數宣告
		//---------------------------------------------------
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
		//---------------------------------------------------
		//SQL
		//---------------------------------------------------
		$sql = "SELECT book_sid,
					   keyin_cdate
				FROM  `mssr_rec_book_cno`
				WHERE `user_id` = $user_id
				ORDER BY `keyin_cdate` DESC";
		$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,$count),$arry_conn_mssr);
		foreach($result as $key=>$val)
		{
			$get_book_info=get_book_info($conn='',$val["book_sid"],$array_select,$arry_conn_mssr);
			$data[$key]["book_name"] = $get_book_info[0]["book_name"];
			$data[$key]["book_scr"] = "mssr/info/book/".$val["book_sid"]."/img/front/simg/1.jpg";
			if(!file_exists ("/home/www/public_html/".$data[$key]["book_scr"]))$data[$key]["book_scr"]="mssr/service/read_the_registration_v2/img/rec_book.png";
			$data[$key]["book_sid"] = $val["book_sid"];
			$data[$key]["time"] = $val["keyin_cdate"];
		}

		return $data;
	}

?>