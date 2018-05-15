<?php


	function student ($conn_user,$arry_conn_user,$class_code) {
		////學生陣列
		//$users=arrys_users($conn_user,$class_code,$date=date("Y-m-d"),$arry_conn_user);
		//if(empty($users)){
		//	$users =0;
		//}
		//$curdate=date("Y-m-d");

		$sql="
		    SELECT
		        `member`.`uid`,
		        `member`.`name`,
		        `student`.`number`,
		        `semester`.`start`,
		        `semester`.`end`
		    FROM `member`
		        INNER JOIN `student`
		        ON `member`.`uid`=`student`.`uid`

                INNER JOIN `class` ON
                `student`.`class_code`=`class`.`class_code`

                INNER JOIN `semester` ON
                `class`.`semester_code`=`semester`.`semester_code`
		    WHERE 1=1
		        AND `student`.`class_code`='{$class_code}'
                AND `student`.`start` >=`semester`.`start`
                AND `student`.`end`    =`semester`.`end`
		    GROUP BY `member`.`uid`, `student`.`number`
		    ORDER BY `student`.`number` ASC
		";

		return db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);
	}


	function forum_po_cno ($rs_uid) {
		//-----------------------------------------------
	    //小組發文篇數
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;
		global $semester_start;
		global $semester_end;


	    $article_cno=0;
	    $sql="
	        SELECT
	            COUNT(*) AS cno
	        FROM  mssr_forum.mssr_forum_article
	        WHERE 1=1
	            AND user_id={$rs_uid}
	            AND article_from = 2
	            AND `keyin_cdate` BETWEEN '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
	    ";

	    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
	    if(!empty($db_results)){
	        $article_cno=(int)$db_results[0]['cno'];
	    }

		return  $article_cno;
	}



	function forum_repo_cno($rs_uid){
		//-----------------------------------------------
	    //小組回文篇數
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;
		global $semester_start;
		global $semester_end;


	   $article_cno=0;
	   $sql="
                SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_reply`
                WHERE 1=1
                    AND `user_id`={$rs_uid}
                    AND  reply_from=2
                    AND `keyin_cdate` BETWEEN '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
            ";
	    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
	    if(!empty($db_results)){
	        $article_cno=(int)$db_results[0]['cno'];
	    }
		return  $article_cno;
	}



	function book_po_cno ($rs_uid) {
		//-----------------------------------------------
	    //書頁發文篇數
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;
		global $semester_start;
		global $semester_end;

	    $article_cno=0;
	    $sql="
	        SELECT
	            COUNT(*) AS cno
	        FROM  mssr_forum.mssr_forum_article
	        WHERE 1=1
	            AND user_id={$rs_uid}
	            AND article_from = 1
	            AND `keyin_cdate` BETWEEN '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
	    ";

	    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
	    if(!empty($db_results)){
	        $article_cno=(int)$db_results[0]['cno'];
	    }

		return  $article_cno;
	}



	function book_repo_cno($rs_uid){
		//-----------------------------------------------
	    //書頁回文篇數
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;
		global $semester_start;
		global $semester_end;

	   $article_cno=0;
	   $sql="
                SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_reply`
                WHERE 1=1
                    AND `user_id`={$rs_uid}
                    AND  reply_from=1
                    AND `keyin_cdate` BETWEEN '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
            ";
	    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
	    if(!empty($db_results)){
	        $article_cno=(int)$db_results[0]['cno'];
	    }
		return  $article_cno;
	}




    function avg_po_cno($rs_uid){
		//-----------------------------------------------
	    //發文平均字數
	    //----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;
		global $semester_start;
		global $semester_end;


	        $sql="
                SELECT
	            		mssr_forum_article_detail.article_content

	        	FROM
	        			mssr_forum.mssr_forum_article
	        	join
	        			mssr_forum.mssr_forum_article_detail on mssr_forum_article.article_id = mssr_forum.mssr_forum_article_detail.article_id
	        	WHERE 1=1
	            AND `user_id`={$rs_uid}
	            AND `keyin_cdate` BETWEEN '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'

            ";

	    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
	    $avg = 0;
	    foreach($db_results as $k=>$v){
	    	$avg+=mb_strlen($v['article_content'], "utf-8");
	    }

		return  $avg;
	}

	function avg_repo_cno($rs_uid){
		//-----------------------------------------------
	    //回文平均字數
	    //----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;
		global $semester_start;
		global $semester_end;

		    $sql="
	                SELECT
		            		mssr_forum_reply_detail.reply_content

		        	FROM
		        			mssr_forum.mssr_forum_reply
		        	join
		        			mssr_forum.mssr_forum_reply_detail on mssr_forum_reply.reply_id = mssr_forum.mssr_forum_reply_detail.reply_id
		        	WHERE 1=1
		            AND `user_id`={$rs_uid}
		            AND `keyin_cdate` BETWEEN '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'

	            ";


	    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
	    $avg = 0;
	    foreach($db_results as $k=>$v){
	    	$avg+=mb_strlen($v['reply_content'], "utf-8");
	    }

		return  $avg;
	}


	function like_po_cno($rs_uid){
		//-----------------------------------------------
	    //發文讚數
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;

		$article_cno=0;
	    $sql="
	         	SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_article_like_log`
                WHERE 1=1
                    AND `user_id`={$rs_uid}

	    ";
		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


		foreach ($db_results as $key => $value) {
			$article_cno = $value['cno'];
		}

		return $article_cno;
	}

	function like_repo_cno($rs_uid){
		//-----------------------------------------------
	    //回文讚數
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;

		$article_cno=0;
	    $sql="
	         	SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_article_like_log`
                WHERE 1=1
                    AND `user_id`={$rs_uid}
	    ";
		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


		foreach ($db_results as $key => $value) {
			$article_cno = $value['cno'];
		}

		return $article_cno;
	}

	function report_po_cno($rs_uid){
		//-----------------------------------------------
	    //發文檢舉數
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;

		$article_cno=0;
	    $sql="
	         	SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_article_report_log`
                WHERE 1=1
                    AND `user_id`={$rs_uid}

	    ";
		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


		foreach ($db_results as $key => $value) {
			$article_cno = $value['cno'];
		}

		return $article_cno;
	}


	function report_repo_cno($rs_uid){
		//-----------------------------------------------
	    //回文檢舉數
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;

		$article_cno=0;
	    $sql="
	         	SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_reply_report_log`
                WHERE 1=1
                    AND `user_id`={$rs_uid}

	    ";
		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach ($db_results as $key => $value) {
			$article_cno = $value['cno'];
		}

		return $article_cno;
	}

	function Actively($rs_uid) {
		//-----------------------------------------------
	    //活躍度
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;


		 $sql="
	         	SELECT
	         			count(*) as cno
				FROM
				 		mssr_forum.mssr_forum_user_request
				where 1=1
						and	request_from  = {$rs_uid} or request_to ={$rs_uid}
						and	request_state = 3
	    ";

	    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
	    $addOne = $db_results[0]['cno'];


	    $sql="
	         	SELECT
	         			count(*) as cno
				FROM
				 		mssr_forum.mssr_forum_user_request
				where 1=1
						and	request_from  = {$rs_uid} or request_to ={$rs_uid}
						and	request_state = 1
	    ";

	    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
	    $addTwo = $db_results[0]['cno']*2;

	    return $addOne+$addTwo;

	}


	function request ($rs_uid) {
		//-----------------------------------------------
	    //別人邀請
	    //-----------------------------------------------

		global $arry_conn_mssr;
		global $conn_mssr;
		global $semester_start;
		global $semester_end;

		$article_cno=0;
	    $sql="
	         	SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_user_request`
                WHERE 1=1
                    AND `request_to`={$rs_uid}
                    AND `keyin_cdate` BETWEEN '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
	    ";
		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

		foreach ($db_results as $key => $value) {
			$article_cno = $value['cno'];
		}

		return $article_cno;
	}


	function request_success ($rs_uid) {
		//-----------------------------------------------
	    //別人接受率
	    //-----------------------------------------------

		global $arry_conn_mssr;
		global $conn_mssr;
		global $semester_start;
		global $semester_end;

		$article_cno=0;
	    $sql="
	         	SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_user_request`
                WHERE 1=1
                    AND `request_to`={$rs_uid}
                    AND  request_state = 1
                    AND `keyin_cdate` BETWEEN '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
	    ";
		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

		foreach ($db_results as $key => $value) {
			$article_cno = $value['cno'];
		}

		return $article_cno;
	}


	function accept ($rs_uid) {
		//-----------------------------------------------
	    //別人邀請
	    //-----------------------------------------------

		global $arry_conn_mssr;
		global $conn_mssr;
		global $semester_start;
		global $semester_end;

		$article_cno=0;
	    $sql="
	         	SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_user_request`
                WHERE 1=1
                    AND `request_from`={$rs_uid}
                    AND `keyin_cdate` BETWEEN '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
	    ";
		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

		foreach ($db_results as $key => $value) {
			$article_cno = $value['cno'];
		}

		return $article_cno;
	}


	function accept_success ($rs_uid) {
		//-----------------------------------------------
	    //別人接受率
	    //-----------------------------------------------

		global $arry_conn_mssr;
		global $conn_mssr;
		global $semester_start;
		global $semester_end;

		$article_cno=0;
	    $sql="
	         	SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_user_request`
                WHERE 1=1
                    AND `request_from`={$rs_uid}
                    AND  request_state = 1
                    AND `keyin_cdate` BETWEEN '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
	    ";
		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

		foreach ($db_results as $key => $value) {
			$article_cno = $value['cno'];
		}

		return $article_cno;
	}



	function like_look ($rs_uid) {
		//-----------------------------------------------
	    //喜歡的小組書
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;


		$sql = "
			SELECT
					count(*) as cno,
					group_id,
					article_id,

					ifnull((
						select
							group_name
						from
							 mssr_forum.mssr_forum_group
						where group_id = sqry.group_id
						limit 1
					),0) as group_name,

					ifnull((
							select
								book_sid
							from
								 mssr_forum.mssr_forum_article_book_rev
							where article_id = sqry.article_id
							limit 1
						),0) as book_sid
			FROM(
					select article_id,group_id,user_id  from
					mssr_forum.mssr_forum_article
                        WHERE user_id ={$rs_uid}
					UNION ALL
					select article_id,group_id,user_id  from
					mssr_forum.mssr_forum_reply
                        WHERE user_id ={$rs_uid}
				) 	as sqry
			where 1=1
				and user_id ={$rs_uid}

				GROUP  BY  article_id
				ORDER  BY  cno desc
				limit 1
		";


		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

		if(!empty($db_results[0]['book_sid'])){
				$book_sid = $db_results[0]['book_sid'];
				$arrys_book_info	=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
				$book_name 			= trim($arrys_book_info[0]['book_name']);
				return $book_name;

		}else{
				return $db_results[0]['book_sid'] = "無";
		}
	}



	function like_group ($rs_uid) {
		//-----------------------------------------------
	    //喜歡的小組
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;

		$arr_group=array();
	    $sql="
	         	SELECT
	         			group_id
				FROM
						 mssr_forum.mssr_forum_group_user_rev
				WHERE 1=1
                    	AND user_id={$rs_uid}

	    ";



		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

		foreach ($db_results as $key => $value) {
			$arr_group[] = $value['group_id'];
		}




		$arr_group = implode(",",$arr_group);

		if(empty($arr_group)){
			$arr_group = 0;
		}


		$sql = "
			SELECT
					count(*) as cno,
					group_id,

					ifnull((
						select
							group_name
						from
							 mssr_forum.mssr_forum_group
						where group_id = sqry.group_id
						limit 1
					),0) as group_name

			FROM(

					select group_id,user_id  from
					 mssr_forum.mssr_forum_article

					UNION ALL
					select group_id,user_id  from
					 mssr_forum.mssr_forum_reply

				) as sqry
			where 1=1
				AND  group_id in ($arr_group)
				AND  user_id={$rs_uid}
			group by group_id
			order by cno desc
			limit 1
		";



		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

		if(!empty($db_results[0]['group_name'])){
			return $db_results[0]['group_name'];
		}else{
			return $db_results[0]['group_name'] = "無";
		}
		// $article_cno = $db_results[0]['group_name'];
		// return $article_cno;
	}



	// function like_book ($rs_uid) {
	// 	//-----------------------------------------------
	//     //喜歡的書
	//     //-----------------------------------------------
	// 	global $arry_conn_mssr;
	// 	global $conn_mssr;



	// 		$sql = "
	// 			SELECT
	// 					count(*) as cno,
	// 					article_id,
	// 					ifnull((
	// 						select
	// 							book_sid
	// 						from
	// 							 mssr_forum.mssr_forum_reply_book_rev
	// 						where article_id = sqry.article_id
	// 						limit 1
	// 					),0) as book_sid

	// 			FROM(
	// 					select article_id,user_id  from
	// 					 mssr_forum.mssr_forum_article
	// 					where group_id = 0

	// 					UNION ALL

	// 					select article_id,user_id  from
	// 					 mssr_forum.mssr_forum_reply
	// 					where group_id = 0
	// 				) as sqry
	// 			where 1=1
	// 				AND user_id={$rs_uid}
	// 				group by article_id
	// 				order by cno desc
	// 				limit 1
	// 		";



	// 		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

	// 		if(!empty($db_results[0]['book_sid'])){
	// 			$book_sid = $db_results[0]['book_sid'];
	// 			$arrys_book_info	=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
	// 			$book_name 			= trim($arrys_book_info[0]['book_name']);
	// 			return $book_name;

	// 		}else{
	// 			return $db_results[0]['book_sid'] = "無";
	// 		}



	// 		// $article_cno = $db_results[0]['group_name'];
	// 		// return $article_cno;


	// }

	// function report ($rs_uid) {
	// 	//-----------------------------------------------
	//     //被檢舉文章
	//     //-----------------------------------------------

	// 	global $arry_conn_mssr;
	// 	global $conn_mssr;

	// 	$cno = 0;
	//     $sql = "
	// 					select count(*) as cno

	// 					from(

	// 					select article_id,user_id,article_type  from
	// 					 mssr_forum.mssr_forum_article
	// 					where article_type = 2

	// 					UNION ALL

	// 					select article_id,user_id,reply_state  from
	// 					 mssr_forum.mssr_forum_reply
	// 					where reply_state = 2

	// 					) as sqry
	// 					where 1=1
	// 						AND user_id={$rs_uid}
	//     	";

	//     $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



	//     return $db_results[0]['cno'];

	// }


?>