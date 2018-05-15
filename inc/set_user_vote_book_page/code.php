<?php
//-------------------------------------------------------
//函式: set_user_vote_book_page()
//用途: 寫入學生填寫的書籍頁數 含計算填寫公式、認可頁數填入各書庫、發送獎勵給認可的使用者
//日期: 2013年09月20日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function SET_USER_VOTE_BOOK_PAGE($conn='',$user_id,$book_sid,$book_page,$arry_conn){
    //---------------------------------------------------
    //函式: set_user_vote_book_page()
    //用途: 提取推薦資訊
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$user_id          使用者
    //$book_sid         書籍識別碼
    //$book_page         填寫的頁數
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //檢核參數
        if(!isset($user_id)||(trim($user_id)==='')){
            $err='SET_USER_VOTE_BOOK_PAGE:NO USER_ID';
            die($err);
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $err='SET_USER_VOTE_BOOK_PAGE:USER_ID IS INVAILD!?';
                die($err);
            }
        }

        if(!isset($book_sid)||(trim($book_sid)==='')){
            $err='SET_USER_VOTE_BOOK_PAGE:NO BOOK_SID';
            die($err);
        }else{
            $book_sid=trim($book_sid);
            if(!preg_match("/^mbc|^mbl|^mbg|^mbu/i",$book_sid)){
                $err='SET_USER_VOTE_BOOK_PAGE:BOOK_SID IS INVAILD';
                die($err);
            }else{
                $book_sid=addslashes($book_sid);
            }
        }

        if(!isset($book_page)||(int)$book_page==0){
            $err='SET_USER_VOTE_BOOK_PAGE:NO BOOK PAGE';
            die($err);
        }else{
            $rec_type=(int)$book_page;
        }

        //資料庫資訊
        $db_host  =$arry_conn['db_host'];
        $db_user  =$arry_conn['db_user'];
        $db_pass  =$arry_conn['db_pass'];
        $db_name  =$arry_conn['db_name'];
        $db_encode=$arry_conn['db_encode'];


        //連結物件判斷
        $has_conn=false;

        if(!$conn){
            $has_conn=true;

            $conn_info='mysql'.":host={$db_host}".";dbname={$db_name}";
            $options = array(
                PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,           //設置錯誤提示，只獲取代碼
                PDO::ATTR_CASE,              PDO::CASE_NATURAL,             //列名按照原始的方式
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode}"    //設置語系
            );

            try{
                $conn=@new PDO($conn_info, $db_user, $db_pass,$options);
            }catch(PDOException $e){
                $err ='SET_USER_VOTE_BOOK_PAGE:CONNECT FAIL';
                die($err);
            }
        }else{
            $has_conn=false;
        }
		
		
		/*===================================================================================
		運作流程
			判斷該書籍有無超過10人填寫
			(無)
			 └─ 寫入user_vote_book_page並標記'未確認'
			(有)
			 └─ 判斷有無最佳頁數(*公式 註1*)
				 (無)
				  └─ 寫入user_vote_book_page並標記'未確認'
				 (有)
				  └─ 判斷所有填寫值是否在公式標準內
				  	  (無)
					   └─ (寫入/修改)user_vote_book_page並標'差'
					  (有)
					   └─ (寫入/修改)user_vote_book_page並標'良' 
					   		'未確認'改為'良'的使用者發放100$禮物
							
		*公式 註1*
			統計每一本書計算填寫頁數的數值
			填寫最多的頁數作為"最佳數值"
			給予"誤差值" 只要最佳數值+-誤差值內算"良"
			一本書內 "良"的數量站統計的1/2以上 此最佳數值將獲准填入書籍
		*///=================================================================================

		
        //---------------------------------------------------
		//SQL 讀取學生填寫頁數的資料
		//---------------------------------------------------
		$sql = "SELECT  `edit_by`,
						`log_id`,
						`book_sid`,
						`book_page`,
						`vote_state`
				FROM `mssr_user_vote_book_page`
				WHERE `book_sid` = '{$book_sid}'";		

				
		
        //資料庫
        $err='SET_USER_VOTE_BOOK_PAGE:QUERY FAIL';
        $result=$conn->prepare($sql);
        $result->execute() or
        die($err);
		
		//建立資料集陣列
        $data=array();

        if(($result->rowCount())!==0){
        //有資料存在
            while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                $result_array[]=$arry_row;
            }
        }
		
		//---------------------------------------------------
		//格式
		//$data[使用者ID]["page"] = 填寫的頁數;
		//$data[使用者ID]["log_id"] = 編寫的KEY;
		//---------------------------------------------------
		foreach($result_array as $key2 => $vul2 )
		{
			$data[$vul2["edit_by"]]["page"] = $vul2["book_page"];
			$data[$vul2["edit_by"]]["log_id"] = $vul2["log_id"];
			$data[$vul2["edit_by"]]["vote_state"] = $vul2["vote_state"];
			if($vul2["edit_by"] == $user_id) die("重複不可");
		}
		$data[$user_id]["page"] = $book_page;
		$data[$user_id]["log_id"] = "";
		$data[$user_id]["vote_state"] = "未確認";
		
		//---------------------------------------------------
		//PHP 判斷填寫的數量是否大於10本
		//---------------------------------------------------
		if( $result->rowCount() >= 9 )  //* 新填入的尚未寫入資料庫  所以要算進去*
		{
			//---------------------------------------------------
			//PHP 獲取最佳數值
			//---------------------------------------------------
			$sid = array ();
			$bad = 5;//誤差值
			$tm_1 = 0;//成功取樣數
			$tm_2 = 0;//取樣總數
			$tms_1 = 0;//成功取樣數(含偏差)
			$book_page_range = array();
			
			/*----統計每個填寫頁數的數量----
			//公式
			// $top[填寫的數值] = 填寫數值的數量;
			---------------------------*/
			$top = array();
			foreach($data as $key2 => $vul2 )
			{
				if(!$top[$vul2["page"]])
				{
					$top[$vul2["page"]] = 1;
				}else
				{
					$top[$vul2["page"]]++;
				}
			}
			/*----統計每個填寫頁數的數量----
			//公式
			// $bast = 最佳填寫數值;
			// $tmp = 完全相同的數量;
			---------------------------*/
			$bast = 0;
			$tmp = 0;
			foreach($top as $key2 => $vul2 )
			{//獲取最高
				if($vul2 > $tmp)
				{
					$bast = $key2;
					$tmp = $vul2;
				}
			}

			/*----紀錄書籍  的上下誤差函數----
			//格式 
			//$book_page_range["top"]  上限值
			//$book_page_range["bottom"] 下限值
			//$book_page_range["bast"] 最佳值
			//$book_page_range["ok"] 是否採用 0 / 1
			---------------------------*/
			$book_page_range["top"] = (int)$bast+5;
			$book_page_range["bottom"] = (int)$bast-5;
			$book_page_range["ok"] = 0;
			$book_page_range["bast"] = (int)$bast;
			/*----計算書籍頁數採納率----
			//公式
			// $tm_1 = 成功取樣數(含誤差);
			// $tm_2 = 取樣的總數; 
			---------------------------*/
			if($tmp >= count($data)/2)$tm_1 ++;//成功取樣數
			$tm_2++;//取樣總數
			
			/*----計算含誤差值得採納率----
			//公式
			// $a1 = 成功取樣數(含誤差);
			---------------------------*/
			$a1=0;
			
			foreach($data as $key2 => $vul2 )
			{
				if($vul2["page"] == $bast)
				{
					//"相同";
					$a1++;
				}
				else if($vul2["page"] >= $bast-$bad &&  $vul2["page"] <= $bast+$bad)
				{
					//"差一些";
					$a1++;
				}
				else
				{
					//"亂填!!!";	
				}
			}
			
			//計算最佳數值超過1/2總數的採納比(包含差一些的)
			if($a1 >= count($data)/2)
			$book_page_range["ok"] = 1;
			
			
			if($book_page_range["ok"]==1)
			{
				
				/*----將頁數寫入書籍資料庫----
				//先判斷 第3個文字
				// 再依據寫入相對應的書籍資料庫
				---------------------------*/
				$sql_no = "";
				if($book_sid[2]=="g"){$sql_no = "`mssr_book_global`";}
				else if($book_sid[2]=="l"){$sql_no = "`mssr_book_library`";}
				else if($book_sid[2]=="c"){$sql_no = "`mssr_book_class`";}
				else if($book_sid[2]=="u"){$sql_no = "`mssr_book_unverified`";}
				if($sql_no!="")
				{
					$sql = "UPDATE ".$sql_no."
							SET `book_page_count` = ".$book_page_range["bast"]."
							WHERE `book_sid` = '".$book_sid."';";	
					//資料庫
					$err='SET_USER_VOTE_BOOK_PAGE:QUERY FAIL';
					$result=$conn->prepare($sql);
					$result->execute() or
					die($err);					
				}else die('SET_USER_VOTE_BOOK_PAGE:IS IT BOOKSIS?');
				
				
				/*----判斷學生填寫的頁數是否優良----
				//判斷優良vote_state寫回"良"
				//判斷超標vote_state寫回"差"
				---------------------------*/
				foreach($data as $uid =>  $vul_2)
				{
					$sql = "";
					if($vul_2["page"] <= $book_page_range["top"] && $vul_2["page"] >= $book_page_range["bottom"] )
					{//"良";
						
						if($uid != $user_id)
						{
							$sql = "UPDATE `mssr_user_vote_book_page`
									SET `vote_state` ='良'
									WHERE `log_id` = ".$vul_2["log_id"].";";
						}
						else
						{
							$datetime = date("Y-m-d  H:i:s");
							$sql="INSERT INTO  `mssr`.`mssr_user_vote_book_page` (
										`create_by` ,
										`edit_by` ,
										`book_sid` ,
										`book_page` ,
										`vote_state` ,
										`keyin_cdate` ,
										`keyin_ip`
									)VALUES (
										$user_id,
										$user_id,  
										'$book_sid',  
										$book_page,  
										'良',  
										'".$datetime."', 
										'".$_SERVER["REMOTE_ADDR"]."'
										);
										";
						}
						
						if( $data[$uid]["vote_state"]== "未確認")
						{
							if(!$user_get[$uid])
							{
								$user_get[$uid] = 100;	
							}else
							{
								$user_get[$uid] = $user_get[$uid] + 100;
							}
						}
						
					}
					else
					{//"差";
						if($uid != $user_id)
						{
							$sql = "UPDATE `mssr_user_vote_book_page`
									SET `vote_state` ='差'
									WHERE `log_id` = ".$vul_2["log_id"].";";
						}else
						{
							$datetime = date("Y-m-d  H:i:s");
							$sql="INSERT INTO  `mssr`.`mssr_user_vote_book_page` (
										`create_by` ,
										`edit_by` ,
										`book_sid` ,
										`book_page` ,
										`vote_state` ,
										`keyin_cdate` ,
										`keyin_ip`
									)VALUES (
										$user_id,
										$user_id,  
										'$book_sid',  
										$book_page,  
										'差',  
										'".$datetime."', 
										'".$_SERVER["REMOTE_ADDR"]."'
										);
										";
						}
					}	
					//資料庫
					$err='SET_USER_VOTE_BOOK_PAGE:QUERY FAIL';
					$result=$conn->prepare($sql);
					$result->execute() or
					die($err);			
				}//----END 判斷學生填寫的頁數是否優良----
				
				//---------------------------------------------------
				//PHP + SQL 
				// 將整理好的金錢發送至各個小PP們
				//---------------------------------------------------
				$tmp = 100;
				foreach($user_get as $uid => $coin )
				{	$tmp++;
					if($tmp >= 999)$tmp = 100;
					
					$datetime = date("Y-m-d  H:i:s");
					
					//給予對方金錢訊息
					$sql = "INSERT INTO `mssr_msg_log`
							(
								`user_id`,
								`from_id`,
								`log_text`,
								`log_state`,
								`keyin_cdate`,
								`keyin_mdate`
							) VALUES (
								".$uid.",
								1,
								'感謝您協助填寫書籍頁數，獲得認可獲取獎勵',
								'1',
								'".$datetime."',
								'".$datetime."'
							)";
					//資料庫
					$err='SET_USER_VOTE_BOOK_PAGE:QUERY FAIL';
					$result=$conn->prepare($sql);
					$result->execute() or
					die($err);			
					
					//寫入禮物籃
					$sql = "SELECT log_id
							FROM  `mssr_msg_log` 
							WHERE  `user_id` = '".$uid."'
							ORDER BY  `mssr_msg_log`.`keyin_cdate` DESC 
							LIMIT 0,1";
					//資料庫
					$err='SET_USER_VOTE_BOOK_PAGE:QUERY FAIL';
					$result=$conn->prepare($sql);
					$result->execute() or
					die($err);	
					
					//建立資料集陣列
					$retrun=array();
			
					if(($result->rowCount())!==0){
					//有資料存在
						while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
							$retrun[]=$arry_row;
						}
					}
					
					$log_id = $retrun[0]['log_id'];
					$tx_gift_sid = substr(tx_gift_sid(1,mb_internal_encoding()),0,22).$tmp;
					
					$sql = "INSERT INTO `mssr_tx_gift_log`
							(
								 `edit_by`,
								 `msg_id`,
								 `tx_from`,
								 `tx_to`,
								 `tx_sid`,
								 `tx_coin`,
								 `tx_state`,
								 `keyin_cdate`,
								 `keyin_mdate`,
								 `keyin_ip`
							) VALUES (
									'1',
									'$log_id',
									'1',
									".$uid.",
									'$tx_gift_sid',
									$coin,
									'未領取',
									'".$datetime."',
									'".$datetime."',
									'".$_SERVER["REMOTE_ADDR"]."'
							);";
					  $sql = $sql . "UPDATE mssr_msg_log
									 SET log_state = '1'
									 WHERE log_id = '".$log_id."' ;
								";
					//資料庫
					$err='SET_USER_VOTE_BOOK_PAGE:QUERY FAIL';
					$result=$conn->prepare($sql);
					$result->execute() or
					die($err);	
					
				}
				
			}else
			{
				//---------------------------------------------------
				//SQL 判斷不出有效數值
				//    寫入使用者填寫的頁數  並加註  '未確認'
				//  **不全部改成未確認**
				//---------------------------------------------------
				$datetime = date("Y-m-d  H:i:s");
				$sql="INSERT INTO  `mssr`.`mssr_user_vote_book_page` (
							`create_by` ,
							`edit_by` ,
							`book_sid` ,
							`book_page` ,
							`vote_state` ,
							`keyin_cdate` ,
							`keyin_ip`
						)VALUES (
							$user_id,
							$user_id,  
							'$book_sid',  
							$book_page,  
							'未確認',  
							'".$datetime."', 
							'".$_SERVER["REMOTE_ADDR"]."'
							);
							";
				//資料庫
				$err='SET_USER_VOTE_BOOK_PAGE:QUERY FAIL';
				$result=$conn->prepare($sql);
				$result->execute() or
				die($err);
			}
			
			
		}else
		{
			//---------------------------------------------------
			//SQL 未大於10個人填寫數值
			//    寫入使用者填寫的頁數  並加註  '未確認'
			//---------------------------------------------------
			$datetime = date("Y-m-d  H:i:s");
			$sql="INSERT INTO  `mssr`.`mssr_user_vote_book_page` (
						`create_by` ,
						`edit_by` ,
						`book_sid` ,
						`book_page` ,
						`vote_state` ,
						`keyin_cdate` ,
						`keyin_ip`
					)VALUES (
						$user_id,
						$user_id,  
						'$book_sid',  
						$book_page,  
						'未確認',  
						'".$datetime."', 
						'".$_SERVER["REMOTE_ADDR"]."'
						);
						";
			//資料庫
			$err='SET_USER_VOTE_BOOK_PAGE:QUERY FAIL';
			$result=$conn->prepare($sql);
			$result->execute() or
			die($err);
		}
		
		
		
		
		
        //建立資料集陣列
        $arrys_result=array();

        if(($result->rowCount())!==0){
        //有資料存在
            while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                $arrys_result[]=$arry_row;
            }
        }
		
        //傳回資料
        return true;
        if($has_conn==true){
            $conn=NULL;
        }
    }
?>