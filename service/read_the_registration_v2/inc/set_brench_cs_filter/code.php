<?
//-------------------------------------------------------
//函式: set_brench_cs_filter()
//用途: 增加人氣值
//日期: 2013年08月20日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------



function set_brench_cs_filter($conn,$book_sid,$array_filter,$arry_conn,$user_id)
{

	//---------------------------------------------------
	//接收,設定參數
	//---------------------------------------------------
	$arrays_brench_cs_filter = array(
        'gcp'=>array(
            '1' =>25,
            '2' =>25,
			'3' =>25,
            '4' =>25,
			'5' =>25,
            '6' =>25
        ),
        'normal_school' => array(
            '1' =>25,
            '2' =>25,
			'3' =>25,
            '4' =>25,
			'5' =>25,
            '6' =>25
        )
    );
	
	$arrays_brench_bound = array(
        'bound'=>array(
            '1' => 1.03,
            '2' => 1.06,
			'3' => 1.09
        )
    );
	
	

	//---------------------------------------------------
	//檢驗參數
	//---------------------------------------------------	
	$school_code ='gcp';
	$gread =3;
	
	$dadad = date("Y-m-d  H:i:s");
	
	//以SID 搜尋該書籍分類為何 
	$sql = "SELECT branch_id 
			FROM  `mssr_book_category_rev` 
			LEFT JOIN mssr_book_category
			ON mssr_book_category_rev.cat_code = mssr_book_category.cat_code
			LEFT JOIN mssr_branch
			ON mssr_branch.branch_name  = `mssr_book_category`.`cat_name`
			WHERE mssr_book_category_rev.book_sid = '$book_sid'
			AND mssr_book_category.cat_state LIKE '啟用'
			AND mssr_branch.branch_state LIKE '啟用'
			GROUP BY branch_id;  ";
	$retrun = db_result($conn='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn);
	foreach($retrun as $key1=>$val1)
	{
		$get_sc = $arrays_brench_cs_filter['gcp']['3'];
		$branch_id = $val1['branch_id'];
		
		$sql = "SELECT count(1) AS count
				FROM  `mssr_user_branch` 
				WHERE user_id = $user_id
				AND branch_id = $branch_id
				AND branch_state LIKE '啟用';";
		$retrun2 = db_result($conn='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn);
		if(@$retrun2[0]['count'])
		{
		
				
			//=====增加滿意度至學生資料
			$sql = "UPDATE `mssr_user_branch`
					SET branch_cs = branch_cs + $get_sc
					WHERE user_id = $user_id
					AND branch_id = $branch_id;";
			//=====增加滿意度交易紀錄
			$sql = $sql."INSERT INTO `mssr_branch_cs_log`
							(`user_id`,
							 `branch_id`,
							 `branch_cs`,
							 `keyin_cdate`)
						VALUES
							($user_id,
							 $branch_id,
							 $get_sc,
							 '".$dadad."',);";
			db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn);
		}
	}

}
?>
