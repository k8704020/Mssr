<?
//-------------------------------------------------------
//版本編號 1.0
//登記書籍  寫入書籍分類
//ajax
//-------------------------------------------------------
	
	//---------------------------------------------------
	//輸入 
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

	//外掛函式檔
	$funcs=array(
				APP_ROOT.'inc/conn/code',
				APP_ROOT.'lib/php/db/code'
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
		$array["echo"] ="";	
        //POST

		
	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------
		
		//POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;
		$book_sid   =(isset($_POST['book_sid']))?$_POST['book_sid']:0;
		$ver_key   =(isset($_POST['ver_key']))?$_POST['ver_key']:array();
		$ver_val   =(isset($_POST['ver_val']))?$_POST['ver_val']:array();	
		$user_school   =(isset($_POST['user_school']))?$_POST['user_school']:0;
		if($user_id != $_SESSION["uid"] || $user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------

		$key = json_decode($ver_key);
		$val = json_decode($ver_val);

		foreach($key as $k => $v)
		{

			if(1 == $val[$k])
			{//建立軟標籤
				$sql = "SELECT count(1) AS count
				FROM `mssr_book_category_user_rev`
				WHERE `book_sid` = '$book_sid' 
				AND `create_by` ='$user_id'
				AND `rev_id` = '".$key[$k]."'";
				$are_you_crycry = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
				if($are_you_crycry[0]['count'] == 0)
				{
					 $sql = "SELECT `cat_code`
							FROM `mssr_book_category`
							WHERE `cat_id` = '".$key[$k]."'";
					$dasdfsd = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

					
					echo $sql = "INSERT INTO `mssr_book_category_user_rev`
							(
								`create_by`, 
								`cat_code`,
								`book_sid`,
								`rev_id`,
								`cat_group`,
								`keyin_cdate`
							)VALUES
							(
								'$user_id',
								'".$dasdfsd [0]["cat_code"]."',
								'$book_sid',
								'".$key[$k]."',
								1,
								'".$_SERVER["REMOTE_ADDR"]."'
							
							)";
					db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
				}
				
			}
			else
			{//移除軟標籤
				$sql = "DELETE FROM `mssr_book_category_user_rev`
						WHERE `book_sid` = '$book_sid' 
						AND `create_by` ='$user_id'
						AND `rev_id` = '".$key[$k]."'";
				db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
				
			}
		}
		
		
		echo json_encode($array,1);
		
		
		?>