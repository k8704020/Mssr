<?php
//-------------------------------------------------------
//版本編號 1.0
//給星球讚
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
		$array["type"] = "";
   	//---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
		$sid        = (isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
		$home_id 		= (isset($_POST['home_id']))?$_POST['home_id']:'0';
		
 		$type 		= (isset($_POST['type']))?mysql_prep($_POST["type"]):'';

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
		if($permission =='0' || $user_id=='0' || $home_id=='0' || $type == '0' || $home_id == $sid) 
		{	
			$array["error"] ="喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入";
			die(json_encode($array,1));
		}
	//---------------------------------------------------
	//SQL
	//---------------------------------------------------	 

		$sql = "";
		
		if($type=="add")
		{
			$sql = "SELECT COUNT(1) AS `count`
					FROM `mssr_score_star_log`
					WHERE `take_from` ='".$sid ."' 
					AND `take_to` = '".$home_id ."';";
			$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
			if($retrun[0]['count']>0 )
			{
			
				$sql = "UPDATE `mssr_score_star_log`
						SET `star_score` = 1,
							`keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
						WHERE `take_from` ='".$sid ."' 
						AND `take_to` = '".$home_id ."';";
			}else
			{
				$sql = "INSERT INTO `mssr_score_star_log`
								(
									`take_from`,
									`take_to`, 
									`star_score`,
									`keyin_cdate`,
									`keyin_ip`
								) VALUES (
									'".$sid ."',
									'".$home_id ."',
									1,
									'".date("Y-m-d  H:i:s")."',
									'".$_SERVER["REMOTE_ADDR"]."')";
			}
			$array["type"]=1;
		}	
		if($type=="del")
		{
			$sql = "UPDATE `mssr_score_star_log`
					SET `star_score` = 0,
						`keyin_ip` = '".$_SERVER["REMOTE_ADDR"]."'
					WHERE `take_from` ='".$sid ."' 
					AND `take_to` = '".$home_id ."';";
			$array["type"]=0;
		}		
	
		
		if($type!="")db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
		
		
		echo json_encode($array,1)
?>