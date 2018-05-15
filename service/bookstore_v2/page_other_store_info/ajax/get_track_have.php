<?php
//-------------------------------------------------------
//版本編號 1.0
//獲取朋友狀態
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

       //外掛設定檔
        require_once(str_repeat("../",4).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
					APP_ROOT.'lib/php/db/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

	//-------------------------------------------
	//初始化, curl設定
	//-------------------------------------------
		$array =array();
		$array["error"] = "";
		$array["echo"] = "";
		$array["have_track"] = 0;
		$array['have_black'] = 0;
		$array['have_good'] = 0;
   	//---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
		$user_id        = (isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
		$home_id 		= (isset($_POST['home_id']))?$_POST['home_id']:'0';
 		//$type 		= (isset($_POST['type']))?mysql_prep($_POST["type"]):'0';

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
		if($permission =='0' || $user_id=='0' || $home_id=='0' || $type = '0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");
	//---------------------------------------------------
	//SQL
	//---------------------------------------------------

		$sql = "SELECT `star_score`
			FROM `mssr_score_star_log`
			WHERE take_from = ".$user_id."
			AND take_to = ".$home_id."
			";
		$retrun = db_result($conn_type='pdo','',$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		if(count($retrun) > 0 && $retrun[0]["star_score"])$array['have_good'] = $retrun[0]["star_score"];
		else $array['have_good'] = 0;

		$sql = "SELECT count(1) AS count
			FROM `mssr_track_user`
			WHERE track_from = ".$user_id."
			AND track_to = ".$home_id."
			";
		$retrun = db_result($conn_type='pdo','',$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		if($retrun[0]["count"]>0)$array['have_track'] = 1;
		else $array['have_track'] = 0;

		echo json_encode($array,1)
?>