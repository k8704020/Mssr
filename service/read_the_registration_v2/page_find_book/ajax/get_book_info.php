<?php
//-------------------------------------------------------
//版本編號 1.0
//搜尋資料
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
	require_once(str_repeat("../",4)."config/config.php");
	require_once(str_repeat("../",4)."/inc/get_black_book_info/code.php");
	//外掛函式檔
	$funcs=array(
				APP_ROOT.'inc/conn/code',
				APP_ROOT.'lib/php/vaildate/code',
				APP_ROOT.'lib/php/string/code',
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
		$array["book_area"]="";
		$array["has_black"]=false;
		$array["book_info"] = array();
		$has_find = false;

		 //book_code     書籍編號

        //POST
        $book_id = $_POST["book_id"];
		$book_id = trim($book_id);

	//---------------------------------------------------
    //設定參數 檢驗參數
    //---------------------------------------------------

        //POST
       	$user_id        =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
		$user_permission=(isset($_POST['user_permission']))?$_POST['user_permission']:0;

		$user_school=(isset($_POST['user_school']))?$_POST['user_school']:"";
		$book_id=(isset($_POST['book_id']))?mysql_prep(trim($_POST['book_id'])):"";

		//去除"-" 的手術
		$book_id = str_replace ("-","",$book_id);

		if($user_id != $_SESSION["uid"] || $user_permission != $_SESSION["permission"])
		{
			$array["error"] ="你違法進入了喔!!  請重新登入";
			die(json_encode($array,1));
		}

		//判斷輸入的ID是否正常
		if($_POST['book_id']=="")
		{
			$array["echo"] ="請輸入數字號碼";
			die(json_encode($array,1));
		}

	//-------------------------------------------
	//SQL
	//-------------------------------------------

		//-------------------------------------------
		//圖書館是否有此書籍
		//-------------------------------------------

			if(!$has_find){
				$sql="
					SELECT
						`book_sid`,
						`book_isbn_10`,
						`book_isbn_13`,
						`book_name`,
						`book_author`,
						`book_page_count`,
						`book_publisher`
					FROM `mssr_book_library`
					WHERE 1=1
						AND (
							`book_isbn_10` = '{$book_id}'
								OR
							`book_isbn_13` = '{$book_id}'
								OR
							`book_library_code`  = '{$book_id}'
						)
						AND
						`school_code` = '{$user_school}'
				";

				$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$numrow=count($arrys_result);

				if($numrow!==0){
					foreach($arrys_result as $key => $vul)
					{
						$rs_book_sid=$vul["book_sid"];
						$rs_book_isbn_10=$vul["book_isbn_10"];
						$rs_book_isbn_13=$vul["book_isbn_13"];


						$book_nonumbering="'{$rs_book_sid}'";
						if($rs_book_isbn_10!=='')$book_nonumbering.=",'{$rs_book_isbn_10}'";
						if($rs_book_isbn_13!=='')$book_nonumbering.=",'{$rs_book_isbn_13}'";

						$get_black_book_info=get_black_book_info($conn_mssr,$book_nonumbering,$arry_conn_mssr);
						if(!count($get_black_book_info))
						{
							$has_find=true;
						}
						else
						{
							$array["has_black"]=true;
						}
					}
					if($has_find==true){
						$array["book_area"]='library';
						$array["book_info"]=$arrys_result;
					}
				}
			}

		//-------------------------------------------
		//班級是否有此書籍
		//-------------------------------------------

			if(!$has_find){
				$sql="
					SELECT
						`book_sid`,
						`book_isbn_10`,
						`book_isbn_13`,
						`book_name`,
						`book_author`,
						`book_page_count`,
						`book_publisher`,
						`book_donor`
					FROM `mssr_book_class`
					WHERE 1=1
						AND (
							`book_isbn_10` = '{$book_id}'
								OR
							`book_isbn_13` = '{$book_id}'
						)
						AND
						`school_code` = '{$user_school}'
				";

				$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$numrow=count($arrys_result);

				if($numrow!==0){

					foreach($arrys_result as $key => $vul)
					{
						$rs_book_sid=$vul["book_sid"];
						$rs_book_isbn_10=$vul["book_isbn_10"];
						$rs_book_isbn_13=$vul["book_isbn_13"];


						$book_nonumbering="'{$rs_book_sid}'";
						if($rs_book_isbn_10!=='')$book_nonumbering.=",'{$rs_book_isbn_10}'";
						if($rs_book_isbn_13!=='')$book_nonumbering.=",'{$rs_book_isbn_13}'";

						$get_black_book_info=get_black_book_info($conn_mssr,$book_nonumbering,$arry_conn_mssr);
						if(!count($get_black_book_info))
						{
							$has_find=true;
						}else
						{
							$array["has_black"]=true;
						}
					}
					if($has_find==true){
						$array["book_area"]='class';
						$array["book_info"]=$arrys_result;
					}

				}
			}


		//-------------------------------------------
		//系統書庫是否有此書籍
		//-------------------------------------------

			if(!$has_find){
				$sql="
					SELECT
						`book_sid`,
						`book_isbn_10`,
						`book_isbn_13`,
						`book_name`,
						`book_author`,
						`book_page_count`,
						`book_publisher`
					FROM `mssr_book_global`
					WHERE 1=1
						AND (
							`book_isbn_10` = '{$book_id}'
								OR
							`book_isbn_13` = '{$book_id}'
						)
				";

				$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$numrow=count($arrys_result);

				if($numrow!==0){

					foreach($arrys_result as $key => $vul)
					{
						$rs_book_sid=$vul["book_sid"];
						$rs_book_isbn_10=$vul["book_isbn_10"];
						$rs_book_isbn_13=$vul["book_isbn_13"];


						$book_nonumbering="'{$rs_book_sid}'";
						if($rs_book_isbn_10!=='')$book_nonumbering.=",'{$rs_book_isbn_10}'";
						if($rs_book_isbn_13!=='')$book_nonumbering.=",'{$rs_book_isbn_13}'";

						$get_black_book_info=get_black_book_info($conn_mssr,$book_nonumbering,$arry_conn_mssr);
						if(!count($get_black_book_info))
						{
							$has_find=true;
						}else
						{
							$array["has_black"]=true;
						}
					}
					if($has_find==true){
						$array["book_area"]='global';
						$array["book_info"]=$arrys_result;
					}
				}
			}

		//-------------------------------------------
		//垃圾書庫是否有此書籍
		//-------------------------------------------

			if(!$has_find){
				$sql="
					SELECT
						`book_sid`,
						`book_isbn_10`,
						`book_isbn_13`,
						`book_name`,
						`book_author`,
						`book_page_count`,
						`book_publisher`
					FROM `mssr_book_unverified`
					WHERE 1=1
						AND (
							`book_isbn_10` = '{$book_id}'
								OR
							`book_isbn_13` = '{$book_id}'
						)
						AND
						(
							(
								`book_from` = 1
								AND
								`book_verified` = 1
							)
							OR
							`book_from` = 2
						)
				";

				$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$numrow=count($arrys_result);

				if($numrow!==0){

					foreach($arrys_result as $key => $vul)
					{
						$rs_book_sid=$vul["book_sid"];
						$rs_book_isbn_10=$vul["book_isbn_10"];
						$rs_book_isbn_13=$vul["book_isbn_13"];


						$book_nonumbering="'{$rs_book_sid}'";
						if($rs_book_isbn_10!=='')$book_nonumbering.=",'{$rs_book_isbn_10}'";
						if($rs_book_isbn_13!=='')$book_nonumbering.=",'{$rs_book_isbn_13}'";

						$get_black_book_info=get_black_book_info($conn_mssr,$book_nonumbering,$arry_conn_mssr);
						if(!count($get_black_book_info))
						{
							$has_find=true;
						}else
						{
							$array["has_black"]=true;
						}
					}
					if($has_find==true){
						$array["book_area"]='unverified';
						$array["book_info"]=$arrys_result;
					}

				}
			}
		//-------------------------------------------
		//資料庫有書籍的狀況 搜尋是否有圖片存在
		//-------------------------------------------

			if($has_find)
			{

				for($i = 0 ; $i < sizeof($array["book_info"]);$i++)
				{
					$root = str_repeat("../",3)."info/book/".$array["book_info"][$i]["book_sid"]."/img/front";

					$book_b_j    ="{$root}/bimg/1.jpg";
					$book_b_p    ="{$root}/bimg/1.png";
					$book_s_j    ="{$root}/simg/1.jpg";
					$book_s_p    ="{$root}/simg/1.png";
					$pic_path        ='';

					if(file_exists("../".$book_b_j)){
						$pic_path=$book_b_j;
					}
					if(file_exists("../".$book_b_p)){
						$pic_path=$book_b_p;
					}
					if(file_exists("../".$book_s_j)){
						$pic_path=$book_s_j;
					}
					if(file_exists("../".$book_s_p)){
						$pic_path=$book_s_p;
					}

					if($pic_path=='')$pic_path = './0.png';
					$array["book_info"][$i]["src"] = $pic_path;
				}
			}

		//-------------------------------------------
		//判定書籍是否為ISBN 並將10碼轉檔13碼
		//-------------------------------------------

            function findIsbn($str)
            {
                $regex = '/\b(?:ISBN(?:: ?| ))?((?:97[89])?\d{9}[\dx])\b/i';

                if (preg_match($regex, str_replace('-', '', $str), $matches)) {
                    return (10 === strlen($matches[1]))
                        ? 1   // ISBN-10
                        : 2;  // ISBN-13
                }
                return false; // No valid ISBN found
            }

			if(!$has_find)
			{


					if(isset($book_id)){
						$book_isbn_10='';
						$book_isbn_13='';
						$_book_code=(int)$book_id;

						$ch_isbn_10=ch_isbn_10($_book_code, $convert=false);
						$ch_isbn_13=ch_isbn_13($_book_code, $convert=false);

						$_lv=0; //錯誤指標
						if(isset($ch_isbn_10['error'])){
							$_lv=$_lv+1;
						}
						if(isset($ch_isbn_13['error'])){
							$_lv=$_lv+3;
						}

						switch($_lv){
							case 1:
							//10碼錯誤，利用13碼轉換更新
								$array["book_isbn_10"]=isbn_13_to_10($_book_code);
								$array["book_isbn_13"]=$_book_code;
								$array["book_area"]='go_find_internet';
							break;

							case 3:
							//13碼錯誤，利用10碼轉換更新
								$array["book_isbn_10"]=$_book_code;
								$array["book_isbn_13"]=isbn_10_to_13($_book_code);
								$array["book_area"]='go_find_internet';
							break;

							case 4:
								$array["book_isbn_10"]=$_book_code;
								$array["book_isbn_13"]=$_book_code;
								$array["book_area"]='go_find_internet';
							break;

							case 9:
								$array["echo"] ="錯誤的書籍編號<BR>請再次確認書籍編號是否錯誤";
							break;
						}
					}
			}

		//最後處理 處裡重複書籍的部分
		if(sizeof($array["book_info"])>0)
		{
			$tmp = array();
			$tmp_s = array();
			foreach($array["book_info"] as $key => $value)
			{
				if(@$tmp_s[$value["book_name"]]!=1)
				{
					$tmp_s[$value["book_name"]] = 1;
					array_push($tmp,$value);
				}

			}
			$array["book_info"] = $tmp;
		}






		echo json_encode($array,1);


		?>