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
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------

        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
 		//trim();//去空白

		if($user_permission != $_SESSION["permission"]||$user_id==0)
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------
		/*$sql = "SELECT count(1) AS count
				FROM  `mssr_rec_book_cno`
				WHERE user_id = $user_id
				AND book_on_shelf_state = '上架'";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);

		$array["shelf_count"]  = $retrun[0]["count"];

		*/
		$sql = "
					SELECT book_sid,
						   user_id
					FROM  `mssr_rec_book_cno`
					WHERE user_id = $user_id
					AND book_on_shelf_state = '上架'
				";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);


		$array["shelf_count"] = 0;
		$rescore = array();
		foreach($retrun as $key => $val)
		{
            if(!isset($rescore[$val["book_sid"]]))
            {//NEW CRAE
                $rescore[$val["book_sid"]]["count"] = 0 ;
                $rescore[$val["book_sid"]]["score"] = 0 ;
                $array["shelf_count"]++;
            }

			$sql = "SELECT B.comment_score
					FROM
					(
						SELECT comment_type,
							   keyin_cdate,
							   comment_score,
							   book_sid
						FROM
						mssr_rec_comment_log
						WHERE comment_to= $user_id
						AND book_sid = '".$val["book_sid"]."'
						ORDER BY  `mssr_rec_comment_log`.`keyin_cdate` DESC
					)AS B
					GROUP BY B.book_sid,B.comment_type";
			$retrun_2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
			foreach($retrun_2 as $key_2 => $val_2)
			{
				$rescore[$val["book_sid"]]["count"] ++ ;
				$rescore[$val["book_sid"]]["score"] += $val_2["comment_score"] ;

			}
		}
		$i = 0 ;
		foreach($rescore as $key => $val)
		{$i++;
            if((int)$val["count"]===0)$val["count"]=1;
			$array[$i]["score"] = $val["score"]/$val["count"];
			$array[$i]["count"] = $val["count"];
		}


		/*
		$sql = "SELECT mssr_rec_comment_log.keyin_cdate,
					   A.user_id,A.book_sid,
					   mssr_rec_comment_log.comment_score,
					   comment_type
				FROM
				(
					SELECT book_sid,
						   user_id
					FROM  `mssr_rec_book_cno`
					WHERE user_id = $user_id
					AND book_on_shelf_state = '上架'
				)AS A
				LEFT JOIN
				(
					SELECT *
					FROM
					(
						SELECT comment_to,
							   comment_type,
							   keyin_cdate,
							   comment_score,
							   book_sid
						FROM
						mssr_rec_comment_log
						WHERE comment_to= $user_id
						ORDER BY  `mssr_rec_comment_log`.`keyin_cdate` DESC
					)AS B
					GROUP BY B.book_sid,B.comment_type
				)AS mssr_rec_comment_log
				ON A.user_id = mssr_rec_comment_log.comment_to
				AND A.book_sid= mssr_rec_comment_log.book_sid";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);


		$array["shelf_count"] = 0;
		$rescore = array();
		foreach($retrun as $key => $val)
		{
			if(! $rescore[$val["book_sid"]])
			{//NEW CRAE
				$rescore[$val["book_sid"]]["count"] = 0 ;
				$rescore[$val["book_sid"]]["score"] = 0 ;
				$array["shelf_count"]++;
			}


			if($val["comment_score"])
			{
				$rescore[$val["book_sid"]]["count"] ++ ;
				$rescore[$val["book_sid"]]["score"] += $val["comment_score"] ;
			}
		}
		$i = 0 ;
		foreach($rescore as $key => $val)
		{$i++;
			$array[$i]["score"] = $val["score"]/$val["count"];
			$array[$i]["count"] = $val["count"];
		}
		//$array["shelf_count"]  = $retrun[0]["count"];
		*/


		echo json_encode($array,1);
		?>










