<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 銷售狀況表
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
        require_once(str_repeat("../",3)."/config/config.php");
		require_once(str_repeat("../",3)."/inc/get_book_info/code.php");
	
		 //外掛函式檔
		$funcs=array(
					APP_ROOT.'inc/code',
					APP_ROOT.'lib/php/db/code',
					);
		func_load($funcs,true);
		
	
		//清除並停用BUFFER
		@ob_end_clean();
		
		//建立連線 user
		$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
		$conn_user=conn($db_type='mysql',$arry_conn_user);

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

    ///---------------------------------------------------
    //SESSION
    //---------------------------------------------------
   
        
		$user_id       =(isset($_GET['uid']))?(int)$_GET['uid']:$_SESSION['uid'];
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
		$auth_read_opinion_limit_day = (int)$_GET['auth_read_opinion_limit_day'];
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
		if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");
		// if($auth_read_opinion_limit_day==0)
		// {
		// 	$array["error"] ="?";
		// 	die(json_encode($array,1));
		// }
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
		$page = (int)$_GET["page"];
		$page_limit = ($page-1 )*10;
	//---------------------------------------------------
	//SQL
	//---------------------------------------------------
		$array = array();
		$sday = date("Y-m-d",strtotime("-".$auth_read_opinion_limit_day." day"));
		$sql = " SELECT book_borrow_log_B.borrow_sid,
                       book_borrow_log_B.borrow_sdate,
                       book_borrow_log_B.book_sid

                FROM (SELECT   book_borrow_log_A.`read_cno`,book_borrow_log_A.book_sid,book_borrow_log_A.borrow_sid,book_borrow_log_A.borrow_sdate
                      FROM (
                            SELECT       
                                    COUNT(`mssr_book_borrow_log`.`book_sid`) AS `read_cno`, 
                                    `mssr_book_borrow_log`.`book_sid`, 
                                    MAX(`mssr_book_borrow_log`.`borrow_sid`) as borrow_sid, 
                                    MAX(`mssr_book_borrow_log`.`borrow_sdate`)  as borrow_sdate
                             FROM `mssr_book_borrow_log` 
                             WHERE 1=1 
                             AND `mssr_book_borrow_log`.`user_id`=".$user_id."
                             GROUP BY mssr_book_borrow_log.book_sid 

                       ) AS  `book_borrow_log_A`

                	  ORDER BY book_borrow_log_A.borrow_sdate DESC

                ) AS book_borrow_log_B
                LEFT JOIN mssr_book_read_opinion_log
                ON mssr_book_read_opinion_log.borrow_sid = book_borrow_log_B.borrow_sid
                
                
                WHERE book_borrow_log_B.`borrow_sdate`  >= '".$sday." 00:00:00'

                AND mssr_book_read_opinion_log.book_sid is NULL
                
                
                ORDER BY  `book_borrow_log_B`.`borrow_sdate` DESC  ";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array($page_limit,10),$arry_conn_mssr);
		foreach($retrun as $key1=>$val1)
		{
			
			
			//搜尋書籍名稱
			$array_select = array("book_name","book_author","book_publisher","book_page_count","book_isbn_10","book_isbn_13","book_library_code");
			// print_r($val1['book_sid']);
			// //die();
			$get_book_info=get_book_info($conn='',$val1['book_sid'],$array_select,$arry_conn_mssr);



			//判斷是否有書名

			if($get_book_info[0]['book_name'] ===""){

					$bookIsbn=substr($val1['book_sid'], 0,3);

					if($bookIsbn!='mbl'){
						$array[$key1]["book_name"] = $get_book_info[0]['book_name'];
						$array[$key1]["book_name2"]="書名:??? <ISBN:".$get_book_info[0]['book_isbn_13'].">";

					}else{
						$array[$key1]["book_name"] = $get_book_info[0]['book_name'];
						$array[$key1]["book_name2"]="書名:??? <圖書館:".$get_book_info[0]['book_library_code'].">";

					}

			}else{
				$array[$key1]["book_name"] = $get_book_info[0]['book_name'];
			}

			// $array[$key1]["book_name"]=$get_book_info[0]['book_name'];
			$array[$key1]["book_author"]=$get_book_info[0]['book_author'];
			$array[$key1]["book_publisher"]=$get_book_info[0]['book_publisher'];
			$array[$key1]["book_page_count"]=$get_book_info[0]['book_page_count'];
			$array[$key1]["borrow_sdate"]=$val1['borrow_sdate'];
			$array[$key1]["borrow_sid"]=$val1['borrow_sid'];
			$array[$key1]["book_sid"]=$val1['book_sid'];

			$array[$key1]["book_isbn_10"]=$get_book_info[0]['book_isbn_10'];
			$array[$key1]["book_isbn_13"]=$get_book_info[0]['book_isbn_13'];
			$array[$key1]["book_library_code"]=$get_book_info[0]['book_library_code'];


			//蒐書圖片
			$root = str_repeat("../",3)."info/book/".$val1['book_sid']."/img/front";
			$book_b_j    ="{$root}/bimg/1.jpg";
			$book_b_p    ="{$root}/bimg/1.png";
			$book_s_j    ="{$root}/simg/1.jpg";
			$book_s_p    ="{$root}/simg/1.png";
			$pic_path        ='';

			if(file_exists("".$book_b_j)){
				$pic_path=$book_b_j;
			}
			if(file_exists("".$book_b_p)){
				$pic_path=$book_b_p;
			}
			if(file_exists("".$book_s_j)){
				$pic_path=$book_s_j;
			}
			if(file_exists("".$book_s_p)){
				$pic_path=$book_s_p;
			}

			if($pic_path=='')$pic_path = './0.png';
			$array[$key1]["src"] = $pic_path;
			
		}

?>
<!DOCTYPE HTML>
<Html>
<Head>
   <link href="../css/manu.css" rel="stylesheet" type="text/css">
   <script src="../js/select_thing.js" type="text/javascript"></script>
</Head>
<body style="overflow:hidden;">

	<!--==================================================
    html內容
    ====================================================== -->
	<table  width="490"  border="0" cellpadding="0" cellspacing="0">
    <tr style="height:40px;"> 
    <td class="td_line_l_t" style=" width:80%;">書籍名稱</td>
    <td class="td_line_t"  style=" width:20%;">訂貨日期</td>
    </tr>
    <? for($i = 0 ;$i < sizeof($array) ; $i++)
	{ 	
		echo "<tr id='br_".$i."' class='click_br_1' onClick='click_bar(".$i.")' onMouseOver='over_bar(".$i.")'>";
		if( $i+1 == sizeof($array))
		{
			echo "<td class='td_line_l_d'>";

			
			if($array[$i]["book_name"] != ""){
				echo $array[$i]["book_name"];
			}else{
				echo $array[$i]["book_name"].$array[$i]["book_name2"];
			}
			// if($array[$i]["book_name"]!= ""){

			// 	echo $array[$i]["book_name"];
				
			// }else{
			// 	$bookIsbn=substr($array[$i]["book_sid"], 0,3);
			// 	echo $bookIsbn;

			// 	if($bookIsbn!='mbl'){

			// 		echo "書號:???ISBN:".$array[$i]["book_isbn_13"];
			// 	}else{
			// 		echo "書號:???圖書館:",$array[$i]["book_isbn_13"];

			// 	}
			// }



			// echo $array[$i]["book_name"];
			echo "</td>";
			echo "<td class='td_line_r_d'>";
			echo $array[$i]["borrow_sdate"];
			echo "</td>";
		}
		else
		{
			echo "<td class='td_line_l'>";

			if($array[$i]["book_name"] != ""){
				echo $array[$i]["book_name"];
			}else{
				echo $array[$i]["book_name"].$array[$i]["book_name2"];
			}
			// if($array[$i]["book_name"]!= ""){

			// 	echo $array[$i]["book_name"];
				
			// }else{
			// 	$bookIsbn=substr($array[$i]["book_sid"], 0,3);
			// 	echo $bookIsbn;

			// 	if($bookIsbn!='mbl'){

			// 		echo "書號:???ISBN:".$array[$i]["book_isbn_13"];
			// 	}else{
			// 		echo "書號:???圖書館:",$array[$i]["book_isbn_13"];

			// 	}
			// }
			// echo $array[$i]["book_name"];
			echo "</td>";
			echo "<td class='td_line_r'>";
			echo $array[$i]["borrow_sdate"];
			echo "</td>";
		}
	
		echo "</tr>";
	 } ?>
    
    </table>



    <script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var book_sid = new Array();
	var book_name = new Array();
	var book_author = new Array();
	var book_page_count =  new Array();
	var book_publisher = new Array();
	var borrow_sid = new Array();
	var src = new Array();
	
	var array_data = <? echo json_encode($array,1); ?>;
	
	for(var key in array_data)
	{
		book_sid[key] = array_data[key].book_sid;
		book_name[key] = array_data[key].book_name;
		book_author[key] = array_data[key].book_author;
		book_page_count[key] = array_data[key].book_page_count;
		book_publisher[key] = array_data[key].book_publisher;
		borrow_sid[key] = array_data[key].borrow_sid;
		src[key] = array_data[key].src;
	}
	
	// for(var i in array_data)
	// {
	// 	window.parent.parent.book_info[i] =   new Array();
	// 	window.parent.parent.book_info[i]["book_sid"] = book_sid[i];
	// 	window.parent.parent.book_info[i]["book_name"] = book_name[i];
	// 	window.parent.parent.book_info[i]["book_author"] = book_author[i];
	// 	window.parent.parent.book_info[i]["book_page_count"] = book_page_count[i];
	// 	window.parent.parent.book_info[i]["book_publisher"] = book_publisher[i];
	// 	window.parent.parent.book_info[i]["borrow_sid"] = borrow_sid[i];
	// 	window.parent.parent.book_info[i]["src"] = src[i];
	// }
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	
	//點擊欄位事件ceffb7
	function click_bar(value)
	{
		
		for(var i =0 ; i < <?php echo sizeof($array);?> ; i++)
		{
			window.document.getElementById("br_"+i).className = 'click_br_1';
		}
		window.document.getElementById("br_"+value).className = 'click_br_2';


		var about_book_sid = book_sid[value];
		var about_book_name = book_name[value];
		var about_book_author= book_author[value];
		var about_book_page_count = book_page_count[value];
		var about_book_publisher = book_publisher[value];
		var book_borrow_sid = borrow_sid[value];
		var about_book_src = src[value];


// console.log(about_book_sid);

		localStorage["about_book_sid"]=about_book_sid;
		localStorage["about_book_name"]=about_book_name;
		localStorage["about_book_author"]=about_book_author;
		localStorage["about_book_page_count"]=about_book_page_count;
		localStorage["about_book_publisher"]=about_book_publisher;
		localStorage["book_borrow_sid"]=book_borrow_sid;
		localStorage["about_book_src"]=about_book_src;

		// window.parent.parent.book_choose = value;
		// window.parent.parent.borrow_sid = window.parent.parent.book_info[value]["borrow_sid"];
		// if(window.parent.home_on == 'user')window.parent.document.getElementById("popopo").style.display = "block";
		window.parent.change_page("page_opinion_registration2_idc");
	}
	function over_bar(value)
	{
		for(var i =0 ; i < <? echo sizeof($array);?> ; i++)
		{
			if(window.document.getElementById("br_"+i).className != 'click_br_2')window.document.getElementById("br_"+i).className = 'click_br_1';
		}
		if(window.document.getElementById("br_"+value).className != 'click_br_2' && window.document.getElementById("br_"+value).className != 'click_br_0')window.document.getElementById("br_"+value).className = 'click_br_3';
	}
	//cover
	function cover(text,type)
	{
		window.parent.cover(text,type);
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}	
	//---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    