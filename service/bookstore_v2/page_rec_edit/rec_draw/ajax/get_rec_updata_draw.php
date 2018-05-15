<?
//-------------------------------------------------------
//版本編號 1.0
//讀繪圖書籍資訊
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
		$url = $_POST["pic_url"];
        
	//-------------------------------------------
	//SQL
	//-------------------------------------------
	
		$user = $arry_ftp1_info['account']; 
		$pass = $arry_ftp1_info['password']; 
		$host = $arry_ftp1_info['host']; 


		

		//重新連接 | 重新登入 FTP
		$ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
		$ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
	
		
		
		$hostname = "ftp://".$user . ":" . $pass . "@" . $url; 
		$img_text='data:image/jpg;base64,'.base64_encode(file_get_contents($url,'r'));
		//$idata = base64_encode($hostname->data);
		$array["data"] = $img_text;
		
		
		ftp_close($ftp_conn);
		
		echo json_encode($array,1);
		?>