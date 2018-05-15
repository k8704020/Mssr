<?php
//-------------------------------------------------------
//教師中心
//-------------------------------------------------------

	//---------------------------------------------------
	//設定與引用
	//---------------------------------------------------

		//SESSION
		@session_start();

		//啟用BUFFER
		@ob_start();

		//外掛設定檔
		require_once(str_repeat("../",5).'config/config.php');

		//外掛函式檔
		$funcs=array(
					APP_ROOT.'inc/code',
					APP_ROOT.'center/teacher_center/inc/code',

					APP_ROOT.'lib/php/db/code'
					);
		func_load($funcs,true);

		//清除並停用BUFFER
		@ob_end_clean();

	//---------------------------------------------------
	//有無維護
	//---------------------------------------------------

		if($config_arrys['is_offline']['center']['teacher_center']){
			$url=str_repeat("../",6).'index.php';
			header("Location: {$url}");
			die();
		}

	//---------------------------------------------------
	//有無登入
	//---------------------------------------------------

		$arrys_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
		if(empty($arrys_login_info)){
			die();
		}

	//---------------------------------------------------
	//重複登入
	//---------------------------------------------------

		if(in_array('read_the_registration_code',$config_arrys['user_area'])){
		//清空閱讀登記條碼版登入資訊

			$_SESSION['config']['user_tbl']=array();
			$_SESSION['config']['user_type']='';
			$_SESSION['config']['user_lv']=0;
			if(in_array('read_the_registration_code',$_SESSION['config']['user_area'])){
				foreach($_SESSION['config']['user_area'] as $inx=>$area){
					if(trim($area)==='read_the_registration_code'){
						unset($_SESSION['config']['user_area'][$inx]);
					}
				}
			}
		}

	//---------------------------------------------------
	//SESSION
	//---------------------------------------------------

		$sess_login_info=(isset($_SESSION['tc']['t|dt']))?$_SESSION['tc']['t|dt']:array();

	//---------------------------------------------------
	//權限,與判斷
	//---------------------------------------------------

		if(!empty($sess_login_info)){
			if(!auth_check($db_type='mysql',$arry_conn_user,$sess_login_info['permission'],$auth_type='mssr_tc')){
				$msg="您沒有權限進入，請洽詢明日星球團隊人員!";
				$jscript_back="
					<script>
						alert('{$msg}');
						history.back(-1);
					</script>
				";
				die($jscript_back);
			}
		}else{
			//權限指標
			$auth_flag=false;
			foreach($arrys_login_info as $inx=>$arry_login_info){
				if(auth_check($db_type='mysql',$arry_conn_user,$arry_login_info['permission'],$auth_type='mssr_tc'))$auth_flag=true;
			}
			if(!$auth_flag){
				$msg="您沒有權限進入，請洽詢明日星球團隊人員!";
				$jscript_back="
					<script>
						alert('{$msg}');
						history.back(-1);
					</script>
				";
				die($jscript_back);
			}
		}

	//---------------------------------------------------
	//系統權限判斷
	//---------------------------------------------------
	//1     校長
	//3     主任
	//5     帶班老師
	//12    行政老師
	//14    主任帶一個班
	//16    主任帶多個班
	//22    老師帶多個班

		if(!empty($sess_login_info)){
			$auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_forum_setting_class');
		}

	//---------------------------------------------------
	//接收參數
	//---------------------------------------------------
	//student_id        學生代號

		$get_chk=array(
			'student_id'
		);
		$get_chk=array_map("trim",$get_chk);
		foreach($get_chk as $get){
			if(!isset($_GET[$get])){
				die();
			}
		}

	//---------------------------------------------------
	//設定參數
	//---------------------------------------------------
	//student_id        學生代號

		//POST
		$student_id=(int)$_GET[trim('student_id')];

		//SESSION
		$sess_user_id=(int)$sess_login_info['uid'];

		//分頁
		$psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
		$pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
		$psize=($psize===0)?10:$psize;
		$pinx =($pinx===0)?1:$pinx;

	//---------------------------------------------------
	//檢驗參數
	//---------------------------------------------------
	//student_id        學生代號

		$arry_err=array();

		if($student_id===''){
			$arry_err[]='學生代號,未輸入!';
		}

		if(count($arry_err)!==0){
			if(1==2){//除錯用
				echo "<pre>";
				print_r($arry_err);
				echo "</pre>";
			}
			die();
		}

	//---------------------------------------------------
	//資料庫
	//---------------------------------------------------

		//-----------------------------------------------
		//通用
		//-----------------------------------------------

			//建立連線 mssr
			$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

		//-----------------------------------------------
		//檢核
		//-----------------------------------------------
		//student_id        學生代號

			$student_id=(int)$student_id;

		//-----------------------------------------------
		//預設值
		//-----------------------------------------------

			$sess_user_id = (int)$sess_user_id;
			$student_id = (int)$student_id;
			$point = 30;

		//-----------------------------------------------
		//處理
		//-----------------------------------------------

			$sql = "
				UPDATE `mssr_forum`.`mssr_forum_point` SET
					`total_point`='{$point}'
				WHERE 1=1
					AND `mssr_forum`.`mssr_forum_point`.`user_id`='{$student_id}'
				LIMIT 1
			";

			//送出
			$err = 'DB QUERY FAIL';
			$sth = $conn_mssr->prepare($sql);
			$sth->execute()or die($err);

	//---------------------------------------------------
	//重導頁面
	//---------------------------------------------------

		$url ="";
		$page=str_repeat("../",1)."index.php";
		$arg =array(
			'psize'=>$psize,
			'pinx' =>1
		);
		$arg =http_build_query($arg);

		if(!empty($arg)){
			$url="{$page}?{$arg}";
		}else{
			$url="{$page}";
		}

		header("Location: {$url}");
?>