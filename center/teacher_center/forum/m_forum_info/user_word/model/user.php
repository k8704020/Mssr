<?php 
	


	function po ($rs_uid) {

		global $arry_conn_mssr;
		global $conn_mssr;

		 $sql="
	        SELECT
	            	*
	        FROM  
	        		mssr_forum.`mssr_forum_article`
	        INNER JOIN  mssr_forum.mssr_forum_article_detail   ON	mssr_forum.mssr_forum_article_detail.article_id = mssr_forum.mssr_forum_article.article_id
	        INNER JOIN  mssr_forum.mssr_forum_article_book_rev ON	mssr_forum.mssr_forum_article_detail.article_id = mssr_forum.mssr_forum_article_book_rev.article_id
	        


	        WHERE 1=1
	            AND `user_id`={$rs_uid}
	    ";

	    return $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
	}






	?>