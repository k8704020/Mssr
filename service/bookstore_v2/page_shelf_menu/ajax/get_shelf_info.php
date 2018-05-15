<?php
//-------------------------------------------------------
//版本編號 1.0
//讀上架書籍資訊
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
	require_once(str_repeat("../",4)."/config/config.php");
	require_once(str_repeat("../",4)."/inc/get_book_info/code.php");

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
		$page=(isset($_POST['page']))?(int)$_POST['page']:1;

		

		if($user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------
		$sql = "SELECT count(1) as count FROM `mssr_rec_book_cno`
				WHERE `user_id` = $user_id
				AND `book_on_shelf_state` = '上架'";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		$array["all_count"] =$retrun[0]["count"];

		//new

		//if(1){
			$ture_page = ($page-1)*12;
			$array_select = array("book_name");
			$ass = array();
			$sql ="SELECT book_sid,
							   user_id
						FROM  `mssr_rec_book_cno`
						WHERE user_id = $user_id
						AND book_on_shelf_state = '上架'
						ORDER BY  `keyin_mdate` DESC
						LIMIT ".$ture_page." , 12";
			$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);


			foreach($retrun as $key => $val)
			{
				$sql = "
						SELECT  *
						FROM
						(
							SELECT DISTINCT comment_type,
								   keyin_cdate,
								   comment_score,
								   book_sid,
								   comment_content,
								   comment_public
							FROM   mssr_rec_comment_log
							WHERE comment_to = $user_id
							AND book_sid = '".$val["book_sid"]."'
							ORDER BY  `keyin_cdate` DESC
						)AS B
						GROUP BY B.book_sid,B.comment_type;";
				$retrun2 = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
				// foreach ($retrun2 as $key => $value) {
				// 	$hello[key]["comment_content"]=$value["comment_content"];
				// }

				// echo $sql;
				// die();
				

				if(count($retrun2)>0)
				{
					foreach($retrun2 as $key2 => $val2)
					{
						array_push($ass,$val2);
					}
				}
				else
				{
					$tmp =
					array("book_sid" => $val["book_sid"],
						"comment_type" => NULL,
						"comment_score"=> NULL,
						"keyin_cdate"=> NULL

					);
					array_push($ass,$tmp);
				}
			}

			$retrun = $ass;//屁屁給retrun
			// print_r($retrun);

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


				if($val["comment_score"])
				{
					$rescore[$val["book_sid"]]["count"] ++ ;
					$rescore[$val["book_sid"]]["score"] += $val["comment_score"] ;
				}
				$rescore[$val["book_sid"]][$val["comment_type"]] = $val["comment_score"];


				
				if($val["comment_type"]=="draw"){

					$rescore[$val["book_sid"]]["drawComment"] = $val["comment_content"];
					$rescore[$val["book_sid"]]["drawCommentDate"]= $val["keyin_cdate"];
					$rescore[$val["book_sid"]]["drawCommentPublic"] = $val["comment_public"];


				}
				if($val["comment_type"]=="text"){

					$rescore[$val["book_sid"]]["textComment"] = $val["comment_content"];
					$rescore[$val["book_sid"]]["textCommentDate"]= $val["keyin_cdate"];
					$rescore[$val["book_sid"]]["textCommentPublic"] = $val["comment_public"];

				}
				if($val["comment_type"]=="record"){

					$rescore[$val["book_sid"]]["recordComment"] = $val["comment_content"];
					$rescore[$val["book_sid"]]["recordCommentDate"]= $val["keyin_cdate"];
					$rescore[$val["book_sid"]]["recordCommentPublic"] = $val["comment_public"];
				}
				// $rescore[$val["book_sid"]][$val["keyin_cdate"]] = $val["keyin_cdate"];



			
				
			}
			// print_r($rescore);
			
			$i = 0 ;
           
			foreach($rescore as $key => $val)
			{
                if((int)$val["count"]===0)$val["count"]=1;
				$array[$i]["score"] = $val["score"]/$val["count"];
				$array[$i]["count"] = $val["count"];

				

				// if($val["textComment"]==null){
				// 	$array[$i]["textComment"]=0;
				// // }else{
				// 	$array[$i]["textComment"]= $val["textComment"];
				// // }
				// // if($val["drawComment"]==null){
				// // 	$array[$i]["drawComment"]=0;
				// // }else{
				// 	$array[$i]["drawComment"]= $val["drawComment"];
				// // }
				// // if($val["recordComment"]==null){
				// // 	$array[$i]["recordComment"]=0;
				// // }else{
				// 	$array[$i]["recordComment"]=$val["recordComment"];
				// }

				
				
				// print_r($val);

				// $array[$i]["keyin_cdate"]= $val["keyin_cdate"];

				// print_r($val);

			

				$array_select = array("book_name","book_author","book_publisher","book_isbn_10","book_isbn_13","book_sid");

				// print_r($array_select);

				$get_book_info=get_book_info($conn='',$key,$array_select,$arry_conn_mssr);
				$array[$i]["book_name"]=$get_book_info[0]['book_name'];
				$array[$i]["book_author"]=$get_book_info[0]['book_author'];
				$array[$i]["book_publisher"]=$get_book_info[0]['book_publisher'];


				$array[$i]["book_sid"]=$key;//draw text record
				$array[$i]["has_black"]=false ;
				//檢查是否禁用書
				$rs_book_sid=$get_book_info[0]["book_sid"];
				$rs_book_isbn_10=$get_book_info[0]["book_isbn_10"];
				$rs_book_isbn_13=$get_book_info[0]["book_isbn_13"];


				$book_nonumbering="'{$rs_book_sid}'";
				if($rs_book_isbn_10!=='')$book_nonumbering.=",'{$rs_book_isbn_10}'";
				if($rs_book_isbn_13!=='')$book_nonumbering.=",'{$rs_book_isbn_13}'";

				$get_black_book_info=get_black_book_info($conn_mssr,$book_nonumbering,$arry_conn_mssr);

				if(!count($get_black_book_info))
				{
					//$has_find=true;
				}else
				{
					$array[$i]["has_black"]=true;
				}


				if(!isset($rescore[$array[$i]["book_sid"]]["draw"]) || $rescore[$array[$i]["book_sid"]]["draw"] <= 2)$array[$i]["draw"] = 0;
				else $array[$i]["draw"] = (int)$rescore[$array[$i]["book_sid"]]["draw"] - 2;

				if(!isset($rescore[$array[$i]["book_sid"]]["text"]) || $rescore[$array[$i]["book_sid"]]["text"] <= 2)$array[$i]["text"] = 0;
				else $array[$i]["text"] = (int)$rescore[$array[$i]["book_sid"]]["text"] - 2;

				if(!isset($rescore[$array[$i]["book_sid"]]["record"]) || $rescore[$array[$i]["book_sid"]]["record"] <= 2)$array[$i]["record"] = 0;
				else $array[$i]["record"] = (int)$rescore[$array[$i]["book_sid"]]["record"] - 2;
				

				//若老師評語為空.設為0.將值傳到前面

				if(!isset($rescore[$array[$i]["book_sid"]]["drawComment"]) || $rescore[$array[$i]["book_sid"]]["drawComment"] ==null ){
					$array[$i]["drawComment"] = 0;
				}else {
					$array[$i]["drawComment"] = $rescore[$array[$i]["book_sid"]]["drawComment"] ;
				}

				if(!isset($rescore[$array[$i]["book_sid"]]["textComment"]) || $rescore[$array[$i]["book_sid"]]["textComment"] ==null ){
					$array[$i]["textComment"] = 0;
				}else {
					$array[$i]["textComment"] = $rescore[$array[$i]["book_sid"]]["textComment"];
				}

				if(!isset($rescore[$array[$i]["book_sid"]]["recordComment"]) || $rescore[$array[$i]["book_sid"]]["recordComment"] ==null){
					$array[$i]["recordComment"] = 0;
				}else{
					$array[$i]["recordComment"] = $rescore[$array[$i]["book_sid"]]["recordComment"];
				} 


				//若老師評語時間為空.設為0.將值傳到前面

				if(!isset($rescore[$array[$i]["book_sid"]]["drawCommentDate"]) || $rescore[$array[$i]["book_sid"]]["drawCommentDate"] ==null ){
					$array[$i]["drawCommentDate"] = 0;
				}else{
					$array[$i]["drawCommentDate"] = $rescore[$array[$i]["book_sid"]]["drawCommentDate"] ;
				}

				if(!isset($rescore[$array[$i]["book_sid"]]["textCommentDate"]) || $rescore[$array[$i]["book_sid"]]["textCommentDate"] ==null ){
					$array[$i]["textCommentDate"] = 0;
				}else{
					$array[$i]["textCommentDate"] = $rescore[$array[$i]["book_sid"]]["textCommentDate"];
				}

				if(!isset($rescore[$array[$i]["book_sid"]]["recordCommentDate"]) || $rescore[$array[$i]["book_sid"]]["recordCommentDate"] ==null){
					$array[$i]["recordCommentDate"] = 0;
				}else{
					$array[$i]["recordCommentDate"] = $rescore[$array[$i]["book_sid"]]["recordCommentDate"];
				}

				//若老師評語設為不公開.設為0.將值傳到前面

				if(!isset($rescore[$array[$i]["book_sid"]]["drawCommentPublic"]) || $rescore[$array[$i]["book_sid"]]["drawCommentPublic"] ==null ){
					$array[$i]["drawCommentPublic"] = 0;
				}else{
					$array[$i]["drawCommentPublic"] = $rescore[$array[$i]["book_sid"]]["drawCommentPublic"] ;
				} 

				if(!isset($rescore[$array[$i]["book_sid"]]["textCommentPublic"]) || $rescore[$array[$i]["book_sid"]]["textCommentPublic"] ==null ){
					$array[$i]["textCommentPublic"] = 0;
				}else {
					$array[$i]["textCommentPublic"] = $rescore[$array[$i]["book_sid"]]["textCommentPublic"];
				}

				if(!isset($rescore[$array[$i]["book_sid"]]["recordCommentPublic"]) || $rescore[$array[$i]["book_sid"]]["recordCommentPublic"] ==null){
					$array[$i]["recordCommentPublic"] = 0;
				}else {
					$array[$i]["recordCommentPublic"] = $rescore[$array[$i]["book_sid"]]["recordCommentPublic"] ;
				}








				//搜尋書籍審核
				$array[$i]["book_verified"] = 1;

				if($key[2] == "u")
				{
					$sql =	"SELECT `book_verified`
							FROM `mssr_book_unverified`
							WHERE `book_sid` = '".$key."'";
					$book_tmpmp = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
					$array[$i]["book_verified"] = $book_tmpmp[0]["book_verified"];
				}




				//瀏覽率

				$array[$i]["total_visit_count"] = 0;



				$sql1 = "
						SELECT 
							IFNULL((
								sum(`visit_cno`) 
							),0 ) as `total_visit_count`
						FROM `mssr_visit_on_shelf_book`
						WHERE `visit_to` = '$user_id'
						AND  `book_sid`='".$key."'
						";

				$retrun3 = db_result($conn_type='pdo',$conn_mssr,$sql1,$arry_limit=array(),$arry_conn_mssr);

				// echo $sql1; print_r($retrun3);die();

				if(empty($retrun3)){
			
						$array[$i]["total_visit_count"]=0;

				}else{
						$array[$i]["total_visit_count"]= $retrun3[0]["total_visit_count"];
				}
	

				$i++;
			}
			$array["count"] = sizeof($rescore);

			// print_r($rescore);


			//print_r($array);
			//die();
		/*}
		else{

		//

		$ture_page = ($page-1)*12;
		$sql = "SELECT book_sid FROM `mssr_rec_book_cno`
				WHERE `user_id` = $user_id
				AND `book_on_shelf_state` = '上架'
				ORDER BY `mssr_rec_book_cno`.`keyin_mdate` DESC";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array($ture_page,12),$arry_conn_mssr);
		foreach($retrun as $key1 =>$val1)
		{
			$array_select = array("book_name");
			$get_book_info=get_book_info($conn='',$val1['book_sid'],$array_select,$arry_conn_mssr);
			$array[$key1]["book_name"]=$get_book_info[0]['book_name'];
			$array[$key1]["book_sid"]=$val1['book_sid'];
		}
		$array["count"] = sizeof($retrun);
		}*/


		echo json_encode($array,1);
		?>