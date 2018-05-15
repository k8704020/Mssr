<?php
//-------------------------------------------------------
//版本編號 1.0
//確認是否有這些上傳的圖
//ajax
//-------------------------------------------------------
	//---------------------------------------------------
	//設定與引用
	//---------------------------------------------------

	//SESSION
	@session_start();

	//啟用BUFFER
	@ob_start();

	//外掛設定檔
	require_once(str_repeat("../",5)."/config/config.php");
	require_once(str_repeat("../",5)."/inc/get_book_info/code.php");

	 //外掛函式檔
	$funcs=array(
				APP_ROOT.'inc/code',
				APP_ROOT.'lib/php/db/code',
				);
	func_load($funcs,true);


	//清除並停用BUFFER
	@ob_end_clean();

	//建立連線 user
	//---------------------------------------------------
	//輸入 user_id
	//輸出
	//---------------------------------------------------
	$book_sid = $_POST["book_sid"];
	$user_id = $_POST["user_id"];

	$array = array();
	$array["echo"] ="";
	$array["error"] ="";
	//FTP 路徑
	$ftp_root="public_html/mssr/info/user";
	$ftp_path_bimg="{$ftp_root}/{$user_id}/book/{$book_sid}/draw/bimg";
	$file_list = array();
	//連接 | 登入 FTP
	$ftp_conn  =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
	$ftp_login =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
	ftp_pasv($ftp_conn,TRUE);
	$arry_ftp_file=ftp_nlist($ftp_conn,$ftp_path_bimg);
	if(in_array($ftp_path_bimg."/upload_1.jpg",$arry_ftp_file))$file_list["du_1"]="1";
	if(in_array($ftp_path_bimg."/upload_2.jpg",$arry_ftp_file))$file_list["du_2"]="1";
	if(in_array($ftp_path_bimg."/upload_3.jpg",$arry_ftp_file))$file_list["du_3"]="1";
	ftp_close($ftp_conn);


        for($i=1;$i<=3;$i++){
            if(isset($file_list["du_".$i])){
                if($file_list["du_".$i]){

                    $array[$i] = "y";
                }else
                {
                    $array[$i] = "n";
                }
            }
        }


		echo json_encode($array,1);
		?>