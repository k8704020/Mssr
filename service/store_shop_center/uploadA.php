<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店
//(內頁)  //主頁面 or 內頁
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",2).'config/config.php');


        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/fso/code',
                    APP_ROOT.'lib/php/upload/file_upload_save/code',
                    APP_ROOT.'lib/php/image/phpthumb/thumb_imagebysize'
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

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收與檢驗
    //---------------------------------------------------

		$error = "";
		
		
		$item_name = trim(addslashes($_POST["item_name"]));
		$item_info = trim(addslashes($_POST["item_info"]));
		$item_coin = (int)($_POST["item_coin"]);
		$item_state = trim(addslashes($_POST["item_state"]));
		$item_note = trim(addslashes($_POST["item_note"]));
		$user_id = "1";
		
		
	
		if($item_name == "")$error="名稱空白";//名稱空白
		if(!($item_state == "上架" || $item_state == "下架"))$error="上下架選項錯誤";//名稱空白
		if($item_coin == 0)$error="金錢錯誤";//ID遺失
		if(!(isset($_FILES["file"])&&!empty($_FILES["file"])&&$_FILES["file"]['error']===0))$error="無上傳檔案";//無上傳檔案
		
		if($error!="")
		{
			$jscript_back="
                    <script>
                        //var err_msg='{$err_msg}'.split('~');
                        alert('".$error."');
						self.location.href='./uppage.php?ps=tdd';
                    </script>
                ";

                die($jscript_back);

		}


    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);      
				
    //---------------------------------------------------
    //上傳處理
    //---------------------------------------------------
		$dadad = date("Y-m-d  H:i:s");
        $allow_exts ="";  //類型清單陣列
        $allow_mimes="";  //mime清單陣列
        $allow_size ="";  //檔案容量上限

        $allow_exts=array(
            trim("png")
        );
        $allow_mimes=array(
            trim("image/png")
        );
        $allow_size=array('kb'=>5000);

        //變數設定
        $File=$_FILES["file"];
        $path = $root=str_repeat("../",1)."bookstore_v2/bookstore_courtyard/img/";


            //檢核資料夾
            $_arrys_path=array(
                "{$path}"       =>mb_convert_encoding("{$path}",$fso_enc,$page_enc)
            );
            foreach($_arrys_path as $_path=>$_path_enc){
                if(!file_exists($_path_enc)){
                    mk_dir($_path,$mode=0777,$recursive=true,$fso_enc);
                }
            }
			
			
			 $upload_file=file_upload_save($File,$path,$fso_enc,$allow_exts,$allow_mimes,$allow_size,true);
 			if($upload_file!==false)
			{
				//ftp路徑
                
				$info     =pathinfo($upload_file);
				trim($info['basename']);
				
				$sql = "INSERT INTO `mssr`.`mssr_item` 
									(`create_by`,
									 `edit_by`,
									 `item_name`, 
									 `item_info`, 
									 `item_coin`, 
									 `item_state`, 
									 `item_note`, 
									 `keyin_cdate`
									 ) VALUES (
									 '".$user_id."',
									 '".$user_id."',
									 '".$item_name."',
									 '".$item_info."',
									 '".$item_coin."',
									 '".$item_state."',
									 '".$item_note."',
									 '".$dadad."');";
				db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
				$sql = "SELECT item_id 
						FROM `mssr_item` 
						WHERE item_name = '".$item_name."'
						ORDER BY `mssr_item`.`item_id` DESC";
				$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
				if(count($result)>0)rename($path.$info['basename'],$path.$result[0]["item_id"].".png");
				
				
				
				
				
				$jscript_back="
                    <script>
                        alert('輸入成功');
						window.parent.main();
                        history.back(-1);
                    </script>
                ";die($jscript_back);
				
			}else
			{
				$jscript_back="
                    <script>
                        alert('傳錯檔案類型');
                        history.back(-1);
                    </script>
                ";die($jscript_back);
			}
			
?>