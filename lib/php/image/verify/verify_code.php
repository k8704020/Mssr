<?php
function verify_code($field){
//-------------------------------------------------------
//檢核驗證
//-------------------------------------------------------
//$field                表單名稱    使用者輸入
//$_SESSION['vcode']	圖片驗證    系統產生
//-------------------------------------------------------

	//啟用SESSION
	if(!session_id()){
		session_start();
	}

	//判斷參數
	if(!isset($_POST[$field])){
		return false;
	}
	if(!isset($_SESSION['vcode'])){
		return false;
	}

	$vcode=explode("|",$_SESSION['vcode']);
	if($vcode[0]==trim($_POST[$field])){
		return true;
	}else{
		return false;
	}
}
?>
