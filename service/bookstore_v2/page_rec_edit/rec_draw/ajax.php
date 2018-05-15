<?php
require_once("functions.php");
require_once("./ajax/save_mssr_rec_draw.php");


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
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/fso/code'
                    );
        func_load($funcs,true);


	//-----------------------------------------------
	//處理
	//-----------------------------------------------
//require_once("sendLog.php");
if(isset($_POST['preAjax'])){ //ready for cross domain
    unset($_POST['preAjax']); //very important can't remove, will be dead
    //$_POST['uid'] = $_COOKIE['unique_id'];
   
    if(isset($_SESSION['now'])){
        $_POST['now'] = $_SESSION['now']; 
    }
    $postdata = http_build_query($_POST);
    //echo "-------local-------\n";
    //print_r($_POST);
    session_write_close(); //if second layer need session we need to pass id to it
    
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context  = stream_context_create($opts);
    $result = file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/mssr/service/bookstore_v2/page_rec_edit/rec_draw/ajax.php', false, $context);
       
    echo $result;
    
    //special case for saveing "Img"
    if(isset($_POST['saveImg'])){

    } 
}
else if(isset($_POST['test'])){
    echo "-------remote-------\n";
    print_r((object)$_POST['now']);
    
}else if(isset($_POST['saveImg'])){
	$page = $_POST["page"];
	$book_id = $_POST["book_id"];
	$uid = $_POST["unique_id"];
	$time = $_POST["time"];
	if( 1)
	{
		/*$filename = sprintf("../../../../info/user/%s/book/%s/draw",$uid,$book_id);
		checkDir($filename); 
      	$filename = $filename."/base64_img/".$page;
		*/
  
		
        if(preg_match('/^rgb\(/',$_POST['bcColor']))
		{
            $_POST['bcColor'] = str_replace(array("rgb",')'),array("rgba",', 1)'),$_POST['bcColor']);
    	}
        
        //save format is bgcolor and imgdata in BASE64
        $imgContent = $_POST['bcColor'].$_POST['imgData'];
		$save_rec_draw_re=save_rec_draw($book_id,$uid,$time,$conn_mssr,$arry_conn_mssr,$auth_coin_open);
		
        //update File
       /* if(file_put_contents($filename,$imgContent))
		{            
            //create thumbnail
        	createThumbnail($book_id,$uid,$page);
			*/
			
			//-------------------------------------------
            //FTP DATASERVER 開設資料夾
            //-------------------------------------------

                //ftp路徑
                $ftp_root="public_html/mssr/info/user";
                $ftp_path="{$ftp_root}/{$uid}/book/{$book_id}/draw";

                //檢核資料夾
                $_arrys_path=array(
                    "{$ftp_root}"                            =>mb_convert_encoding("{$ftp_root}",$fso_enc,$page_enc),
                    "{$ftp_root}/{$uid}"                 =>mb_convert_encoding("{$ftp_root}/{$uid}",$fso_enc,$page_enc),
                    "{$ftp_root}/{$uid}/book"            =>mb_convert_encoding("{$ftp_root}/{$uid}/book",$fso_enc,$page_enc),
                    "{$ftp_root}/{$uid}/book/{$book_id}"=>mb_convert_encoding("{$ftp_root}/{$uid}/book/{$book_id}",$fso_enc,$page_enc),

                    "{$ftp_path}"                            =>mb_convert_encoding("{$ftp_path}",$fso_enc,$page_enc),
                    "{$ftp_path}/bimg"                       =>mb_convert_encoding("{$ftp_path}/bimg",$fso_enc,$page_enc),
                    "{$ftp_path}/simg"                       =>mb_convert_encoding("{$ftp_path}/simg",$fso_enc,$page_enc),
                    "{$ftp_path}/base64_img"                 =>mb_convert_encoding("{$ftp_path}/base64_img",$fso_enc,$page_enc)
                );
				
				foreach($_arrys_path as $_path=>$_path_enc){
                    //重新連接 | 重新登入 FTP
                    $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                    $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
					
					ftp_pasv($ftp_conn,TRUE);
                    
					if(false===@ftp_chdir($ftp_conn,$_path_enc)){
                        mk_dir_ftp($ftp_conn,$_path,$mode=0777,$fso_enc);
                    }
                    //關閉連線
                    ftp_close($ftp_conn);
                }
			//-------------------------------------------
            //FTP DATASERVER 上傳base64_img圖片
            //-------------------------------------------
				/* set the FTP hostname */ 
				$user = $arry_ftp1_info['account']; 
				$pass = $arry_ftp1_info['password']; 
				$host = $arry_ftp1_info['host']; 

				$hostname = "ftp://".$user . ":" . $pass . "@" . $host . "/public_html/mssr/info/user/{$uid}/book/{$book_id}/draw/base64_img/".$page; 
				
				file_put_contents($filename,$imgContent);
				
				/* create a stream context telling PHP to overwrite the file */ 
				$options = array('ftp' => array('overwrite' => true)); 
				$stream = stream_context_create($options); 
				
				/* and finally, put the contents */ 
				file_put_contents($hostname, $imgContent, 0, $stream); 

			//-------------------------------------------
            //FTP DATASERVER 上傳縮圖
            //-------------------------------------------	
				//重新連接 | 重新登入 FTP
				$ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
				$ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
				
				$user = $arry_ftp1_info['account']; 
				$pass = $arry_ftp1_info['password']; 
				$host = $arry_ftp1_info['host']; 
				
				$hostname = "ftp://".$user . ":" . $pass . "@" . $host . "/public_html/mssr/info/user/{$uid}/book/{$book_id}/draw"; 
				
				$filename = $hostname."/base64_img/".$page;
				

				$drawObj = getDrawObject($filename);
				$idata = base64_decode($drawObj->data);
				
				//file_put_contents("test.png",$a);
			
				$source = imagecreatefromstring($idata);
				$dst = imagecreatetruecolor(700,400);
				
				//background color
				eval('$color = imagecolorallocatealpha($dst,'.substr($drawObj->bc,5).';');
				
				//copy and resieze
				imagefilledrectangle($dst, 0, 0, 700, 400, $color);
				imagecopyresized($dst,$source,0,0,0,0,700,400,700,400);
				
				//save to file
				ob_start();
				imagepng($dst);
				$image = ob_get_clean();
				$hostname = "ftp://".$user . ":" . $pass . "@" . $host . "/public_html/mssr/info/user/{$uid}/book/{$book_id}/draw/bimg/".$page.".jpg"; 
				file_put_contents($hostname,$image, 0, $stream); 				
				
				imagedestroy($dst);
				
			
			
			
			
            die($save_rec_draw_re);
		/*}
        else{
            die('{"status" : "save1", "messege" : "save failed."}');        
		}   */
		//上傳至學生圖片資料庫     
	}
	else{
		die('{"status" : "save2", "messege" : "filename error."}');
	}	
}
else if(isset($_POST['loadImg'])){
	$page = $_POST["page"];
   
	$book_id = $_POST["book_id"];
	$uid = $_POST["unique_id"];
	/* set the FTP hostname */ 
	$user = $arry_ftp1_info['account']; 
	$pass = $arry_ftp1_info['password']; 
	$host = $arry_ftp1_info['host']; 
	$hostname = "ftp://".$user .":".$pass."@".$host."/public_html/mssr/info/user/{$uid}/book/{$book_id}/draw/base64_img/".$page; 
	
	$filename = sprintf('http://'.$host."/mssr/info/user/%s/book/%s/draw/base64_img/%s",$uid,$book_id,$page);
    
    
	
	
				
	//echo $filename; //for debug
	if(file_exists($hostname)){
		
		$str = file_get_contents($filename);
		//echo $str;
		
        //check format and separate bgcolor and imgdata
		if(preg_match('/^rgba?\(\d+(,\s\d+)+\)data:image\/png;base64,/',$str)){
			$c = strpos($str,")") + 1;
			$bc = substr($str,0,$c);
			$data = substr($str,$c);
			die('{"status" : "load0", "messege": "load success.","bc": "'. $bc .'", "data": "'.$data.'"}');
		}
		else{
			die('{"status" : "load2", "messege": "file format error."}'); 
		}		
	}
	else{
		die('{"status" : "load1", "messege" : "file dose not exist."}');
	}
}
else{	
    //ajax post error
	echo '{"status" : "-1", "messege" : "request error."}';
}

?>