<?php
function verify_code($field){
//-------------------------------------------------------
//�ˮ�����
//-------------------------------------------------------
//$field                ���W��    �ϥΪ̿�J
//$_SESSION['vcode']	�Ϥ�����    �t�β���
//-------------------------------------------------------

	//�ҥ�SESSION
	if(!session_id()){
		session_start();
	}

	//�P�_�Ѽ�
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
