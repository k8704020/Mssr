<?php
//-------------------------------------------------------
//得取每一天的閱讀本數、字數 //不重複
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION

        //啟用BUFFER
        @ob_start();
		global $conn_mssr;
		global $conn_user;
		global $arry_conn_mssr;
		global $arry_conn_user;
        //外掛設定檔
        require_once("/home/www/public_html/mssr/config/config.php");
		require_once("/home/www/public_html/mssr/inc/get_book_info/code.php");

         //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);
		

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
	 
    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

 
        //建立連線 user
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
 		$conn_user=conn($db_type='mysql',$arry_conn_user);
	//---------------------------------------------------
    //END
    //---------------------------------------------------

	$array_select = array("book_word");
	//找學年級相關人員ID
	$sql = "SELECT uid
			FROM  `student` 
			WHERE  `class_code` LIKE  '%gcp_2013_1_4%'";
	$result = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(),$arry_conn_user);

	$array = array();
	foreach($result as $key=>$val)
	{
		
		
		
		$sql_1 = " SELECT book_sid
					FROM  `mssr_book_borrow_log` 
					WHERE  borrow_sdate >= '2013-09-01'
					AND '".$val['uid']."' = user_id";
		$result_1 = db_result($conn_type='pdo',$conn_mssr,$sql_1,$arry_limit=array(),$arry_conn_mssr);
		$result_1[0]["book_sid"];
		foreach($result_1 as $key1=>$val1)
		{
			
			//L圖書館   mssr_book_library
			if($val1["book_sid"][2] == 'l')
			{
				$sql_2 = "SELECT book_isbn_10,book_isbn_13,book_name,book_library_code,book_word
						  FROM  `mssr_book_library` 
						  WHERE book_word = 0
						  AND book_sid = '".$val1["book_sid"]."'";
				$result_2 = db_result($conn_type='pdo',$conn_mssr,$sql_2,$arry_limit=array(0,1),$arry_conn_mssr);
				if(count($result_2)!=0)
				{
					$tmp["on"] = "mssr_book_library";
					$tmp["book_isbn_10"]=$result_2[0]["book_isbn_10"];
					$tmp["book_isbn_13"]=$result_2[0]["book_isbn_13"];
					$tmp["book_name"]=$result_2[0]["book_name"];
					$tmp["book_word"]=$result_2[0]["book_word"];
					$tmp["book_library_code"]=$result_2[0]["book_library_code"];
					$tmp["sid"] = $val1["book_sid"];
					array_push($array,$tmp);
				}
			}
			//G系統  mssr_book_global
			if($val1["book_sid"][2] == 'g')
			{
				$sql_2 = "SELECT book_isbn_10,book_isbn_13,book_name,book_word
						  FROM  `mssr_book_global` 
						  WHERE book_word = 0
						  AND book_sid = '".$val1["book_sid"]."'";
				$result_2 = db_result($conn_type='pdo',$conn_mssr,$sql_2,$arry_limit=array(0,1),$arry_conn_mssr);
				if(count($result_2)!=0)
				{
					$tmp["on"] = "mssr_book_global";
					$tmp["book_isbn_10"]=$result_2[0]["book_isbn_10"];
					$tmp["book_isbn_13"]=$result_2[0]["book_isbn_13"];
					$tmp["book_name"]=$result_2[0]["book_name"];
					$tmp["book_word"]=$result_2[0]["book_word"];
					$tmp["book_library_code"]=$result_2[0]["book_library_code"];
					$tmp["sid"] = $val1["book_sid"];
					array_push($array,$tmp);
				}
			}
			//C班上  mssr_book_class
			if($val1["book_sid"][2] == 'c')
			{
				$sql_2 = "SELECT book_isbn_10,book_isbn_13,book_name,book_word
						  FROM  `mssr_book_class` 
						  WHERE book_word = 0
						  AND book_sid = '".$val1["book_sid"]."'";
				$result_2 = db_result($conn_type='pdo',$conn_mssr,$sql_2,$arry_limit=array(0,1),$arry_conn_mssr);
				if(count($result_2)!=0)
				{
					$tmp["on"] = "mssr_book_class";
					$tmp["book_isbn_10"]=$result_2[0]["book_isbn_10"];
					$tmp["book_isbn_13"]=$result_2[0]["book_isbn_13"];
					$tmp["book_name"]=$result_2[0]["book_name"];
					$tmp["book_word"]=$result_2[0]["book_word"];
					$tmp["book_library_code"]=$result_2[0]["book_library_code"];
					$tmp["sid"] = $val1["book_sid"];
					array_push($array,$tmp);
				}
			}
			//U拉基  mssr_book_unverified
			if($val1["book_sid"][2] == 'u')
			{
				$sql_2 = "SELECT book_isbn_10,book_isbn_13,book_name,book_word
						  FROM  `mssr_book_unverified` 
						  WHERE book_word = 0
						  AND book_sid = '".$val1["book_sid"]."'";
				$result_2 = db_result($conn_type='pdo',$conn_mssr,$sql_2,$arry_limit=array(0,1),$arry_conn_mssr);
				if(count($result_2)!=0)
				{
					$tmp["on"] = "mssr_book_unverified";
					$tmp["book_isbn_10"]=$result_2[0]["book_isbn_10"];
					$tmp["book_isbn_13"]=$result_2[0]["book_isbn_13"];
					$tmp["book_name"]=$result_2[0]["book_name"];
					$tmp["book_word"]=$result_2[0]["book_word"];
					$tmp["book_library_code"]=$result_2[0]["book_library_code"];
					$tmp["sid"] = $val1["book_sid"];
					array_push($array,$tmp);
				}
			}
			
			
			
			//$get_book_info=get_book_info($conn='',$val1["book_sid"],$array_select,$arry_conn_mssr);
			echo $val1["book_sid"][2]."<BR>";
		}
		//搜尋閱讀資料
		
	}
	
	//	print_r($array);
		
	

?>

<table>
	<tr>
        <td>
        	來源
        </td>
        <td>
        	書名
        </td>
        <td>
        	ISBN 10碼
        </td>
        <td>
        	ISBN 13碼
        </td>
        <td>
        	圖書編號
        </td>
        <td>
        	字數
        </td>
        <td>
            SID
        </td>
    </tr>
<? for($i = 0 ; $i < sizeof($array);$i++){?>
    <tr>
        <td>
        	<? echo $array[$i]["on"];?>
        </td>
        <td>
			<? echo $array[$i]["book_name"];?>
        </td>
        <td>
       		<? echo $array[$i]["book_isbn_10"];?>
        </td>
        <td>
        	<? echo $array[$i]["book_isbn_13"];?>
        </td>
        <td>
        	<? echo $array[$i]["book_library_code"];?>
        </td>
        <td>
        	<? echo $array[$i]["book_word"];?>
        </td>
        <td>
        	<? echo $array[$i]["sid"];?>
        </td>
    </tr>
<? }?>
</table>