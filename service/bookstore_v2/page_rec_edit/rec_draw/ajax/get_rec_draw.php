<?
//-------------------------------------------------------
//版本編號 1.0
//讀繪圖書籍資訊
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
	require_once(str_repeat("../",5)."/config/config.php");
	require_once(str_repeat("../",5)."/inc/get_book_info/code.php");

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




	//-----------------------------------------------
	//通用
	//-----------------------------------------------

	//-------------------------------------------
	//初始化, curl設定
	//-------------------------------------------
		$array =array();
		//繪圖
		$array['rec_draw']="-1";
		$array['rec_draw_content'] = "";
		$array['rec_draw_score'] = 0;

		$array["error"] = "";
		$array["echo"] = "";
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------

        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
		$book_sid = addslashes(trim($_POST['book_sid']));
 		//trim();//去空白

		if($user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------

		//繪圖
		$sql = "SELECT rec_state
				FROM  `mssr_rec_book_draw_log`
				WHERE  `mssr_rec_book_draw_log`.`user_id` =  '".$user_id."'
				AND  `mssr_rec_book_draw_log`.`book_sid` =  '".$book_sid."'
				ORDER BY  `mssr_rec_book_draw_log`.`keyin_cdate` DESC ";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		foreach($retrun as $key1=>$val1)
		{
			if($val1['rec_state']== "顯示")
			{
				$array['rec_draw']="1";
			}
				//搜尋有無評分
				$sql_tmp = "SELECT comment_content,comment_score,keyin_cdate
						FROM  mssr_rec_comment_log
						WHERE mssr_rec_comment_log.book_sid = '".$book_sid."'
						AND mssr_rec_comment_log.comment_to = '".$user_id."'
						AND comment_type='draw'
						ORDER BY  `mssr_rec_comment_log`.`keyin_cdate` DESC
						limit 5;
						 ";
				$retrun_tmp = db_result($conn_type='pdo',$conn_mssr,$sql_tmp,$arry_limit=array(0,1),$arry_conn_mssr);
				if(count($retrun_tmp)>0)
				{	foreach($retrun_tmp as $key2=>$val2)
			        {
					$array[$key2]['rec_draw_content'] = addslashes($val2['comment_content']);
					$array[$key2]['rec_draw_score'] = addslashes($val2['comment_score']);
					$array[$key2]['keyin_cdate'] = addslashes($val2['keyin_cdate']);
					}
				}

		}
		echo json_encode($array,1);
		?>