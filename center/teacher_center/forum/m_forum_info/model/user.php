<?php


	function student ($conn_user,$arry_conn_user,$class_code) {
		//學生陣列
		$users=arrys_users($conn_user,$class_code,$date=date("Y-m-d"),$arry_conn_user);

		$sql="
		    SELECT
		        `member`.`uid`,
		        `member`.`name`,
		        `student`.`number`,
		        `student`.`start`,
		        `student`.`end`
		    FROM `member`
		        INNER JOIN `student`
		        ON `member`.`uid`=`student`.`uid`
		    WHERE 1=1
		        AND `member`.`uid` IN ($users)
		        AND `student`.`start` < CURDATE()
		        AND `student`.`end` > CURDATE()
		        AND `student`.`class_code`='$class_code'
		    GROUP BY `member`.`uid`, `student`.`number`
		    ORDER BY `student`.`number` ASC
		";

		return db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);

	}

	function po_cno($rs_uid){
		//-----------------------------------------------
	    //發文篇數
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;


	    $article_cno=0;
	    $sql="
	        SELECT
	            COUNT(*) AS `cno`
	        FROM  mssr_forum.`mssr_forum_article`
	        WHERE 1=1
	            AND `user_id`={$rs_uid}
                AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
	    ";

	    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
	    if(!empty($db_results)){
	        $article_cno=(int)$db_results[0]['cno'];
	    }
		return  $article_cno;
	}


	function repo_cno($rs_uid,$conn_mssr,$arry_conn_mssr){
		//-----------------------------------------------
        //回覆篇數
        //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;

            $reply_cno=0;
            $sql="
                SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_reply`
                WHERE 1=1
                    AND `user_id`={$rs_uid}
                    AND `mssr_forum`.`mssr_forum_reply`.`reply_state`    =1 -- 回文狀態
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(!empty($db_results)){
                $reply_cno=(int)$db_results[0]['cno'];
            }
			return $reply_cno;
    }

    function avgpo_cno($rs_uid){
		//-----------------------------------------------
	    //發文平均字數
	    //----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;

	        $sql="
                SELECT
	            		mssr_forum_article_detail.article_content

	        	FROM
	        			mssr_forum.mssr_forum_article
	        	join
	        			mssr_forum.mssr_forum_article_detail on mssr_forum_article.article_id = mssr_forum.mssr_forum_article_detail.article_id
	        	WHERE 1=1
	            AND `user_id`={$rs_uid}
            ";

	    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
	    $avg = 0;
	    foreach($db_results as $k=>$v){
	    	$avg+=mb_strlen($v['article_content'], "utf-8");
	    }

		return  $avg;
	}

	function avgrepo_cno($rs_uid){
		//-----------------------------------------------
	    //回文平均字數
	    //----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;

		    $sql="
	                SELECT
		            		mssr_forum_reply_detail.reply_content

		        	FROM
		        			mssr_forum.mssr_forum_reply
		        	join
		        			mssr_forum.mssr_forum_reply_detail on mssr_forum_reply.reply_id = mssr_forum.mssr_forum_reply_detail.reply_id
		        	WHERE 1=1
		            AND `user_id`={$rs_uid}
	            ";

	    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
	    $avg = 0;
	    foreach($db_results as $k=>$v){
	    	$avg+=mb_strlen($v['reply_content'], "utf-8");
	    }

		return  $avg;
	}

	function group_po_cno($rs_uid){
		//-----------------------------------------------
	    //小組發文家回文篇數
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;

		$article_cno=0;
	    $sql="
	         	SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_article`
                WHERE 1=1
                    AND `user_id`={$rs_uid}
                    AND `article_from`=2
                    AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
	    ";
		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


		foreach ($db_results as $key => $value) {
			$article_cno = $value['cno'];
		}

		return $article_cno;
	}

	function group_repo_cno($rs_uid){
		//-----------------------------------------------
	    //小組發文家回文篇數
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;

		$article_cno=0;
	    $sql="
	         	SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_reply`
                WHERE 1=1
                    AND `user_id`={$rs_uid}
                    AND `reply_from`=2
                    AND `mssr_forum`.`mssr_forum_reply`.`reply_state`    =1 -- 回文狀態
	    ";
		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


		foreach ($db_results as $key => $value) {
			$article_cno = $value['cno'];
		}

		return $article_cno;
	}



	function book_po_cno($rs_uid){
		//-----------------------------------------------
	    //書籍發文家回文篇數
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;

		$article_cno=0;
	    $sql="
	         	SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_article`
                WHERE 1=1
                    AND `user_id`={$rs_uid}
                    AND `article_from`=1
                    AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
	    ";
		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


		foreach ($db_results as $key => $value) {
			$article_cno = $value['cno'];
		}

		return $article_cno;
	}

	function book_repo_cno($rs_uid){
		//-----------------------------------------------
	    //書籍發文家回文篇數
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;

		$article_cno=0;
	    $sql="
	         	SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_reply`
                WHERE 1=1
                    AND `user_id`={$rs_uid}
                    AND `reply_from`=1
                    AND `mssr_forum`.`mssr_forum_reply`.`reply_state`    =1 -- 回文狀態
	    ";
		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


		foreach ($db_results as $key => $value) {
			$article_cno = $value['cno'];
		}

		return $article_cno;
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


	function request ($rs_uid) {
		//-----------------------------------------------
	    //邀請率
	    //-----------------------------------------------

		global $arry_conn_mssr;
		global $conn_mssr;

		$article_cno=0;
	    $sql="
	         	SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_user_request`
                WHERE 1=1
                    AND `request_to`={$rs_uid}
	    ";
		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

		foreach ($db_results as $key => $value) {
			$article_cno = $value['cno'];
		}

		return $article_cno;
	}

	function request_success ($rs_uid) {
		//-----------------------------------------------
	    //邀請率成功
	    //-----------------------------------------------

		global $arry_conn_mssr;
		global $conn_mssr;

		$article_cno=0;
	    $sql="
	         	SELECT
                    COUNT(*) AS `cno`
                FROM mssr_forum.`mssr_forum_user_request`
                WHERE 1=1
                    AND `request_to`={$rs_uid}
                    AND  request_state = 1
	    ";
		$db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

		foreach ($db_results as $key => $value) {
			$article_cno = $value['cno'];
		}

		return $article_cno;
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



	function like_book ($rs_uid) {
		//-----------------------------------------------
	    //喜歡的書
	    //-----------------------------------------------
		global $arry_conn_mssr;
		global $conn_mssr;



			$sql = "
				SELECT
						count(*) as cno,
						article_id,
						ifnull((
							select
								book_sid
							from
								 mssr_forum.mssr_forum_reply_book_rev
							where article_id = sqry.article_id
							limit 1
						),0) as book_sid

				FROM(
						select article_id,user_id  from
						 mssr_forum.mssr_forum_article
						where group_id = 0
                            and user_id ={$rs_uid}

						UNION ALL

						select article_id,user_id  from
						 mssr_forum.mssr_forum_reply
						where group_id = 0
                            and user_id ={$rs_uid}
					) as sqry
				where 1=1
					AND user_id={$rs_uid}
					group by article_id
					order by cno desc
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



			// $article_cno = $db_results[0]['group_name'];
			// return $article_cno;


	}

	function report ($rs_uid) {
		//-----------------------------------------------
	    //被檢舉文章
	    //-----------------------------------------------

		global $arry_conn_mssr;
		global $conn_mssr;

		$cno = 0;
	    $sql = "
						select count(*) as cno

						from(

						select article_id,user_id,article_type  from
						 mssr_forum.mssr_forum_article
						where article_type = 2

						UNION ALL

						select article_id,user_id,reply_state  from
						 mssr_forum.mssr_forum_reply
						where reply_state = 2

						) as sqry
						where 1=1
							AND user_id={$rs_uid}
	    	";

	    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



	    return $db_results[0]['cno'];

	}


?>