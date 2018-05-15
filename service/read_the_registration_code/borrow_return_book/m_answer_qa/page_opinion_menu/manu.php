<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 銷售狀況表
//(內頁)  //主頁面 or 內頁
//-------------------------------------------------------
 @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",5).'config/config.php');

       //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'service/read_the_registration_code/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['service']['read_the_registration_code']){
            $url=str_repeat("../",6).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(!login_check(array('t'))){
            die();
        }

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        //初始化，承接變數
        $_sess_t=$_SESSION['t'];
        foreach($_sess_t as $field_name=>$field_value){
            $$field_name=$field_value;
        }

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        if((!isset($_SESSION['_read_the_registration_code']['_login']))||(empty($_SESSION['_read_the_registration_code']['_login']))){
            $page=str_repeat("../",1)."login/loginF.php";

            $jscript_back="
                <script>
                    parent.location.href='{$page}';
                </script>
            ";

            die($jscript_back);
        }else{
            //借書人資訊
            $_user_id    =(int)$_SESSION['_read_the_registration_code']['_login']['_user_id'];
            $_user_name  =trim($_SESSION['_read_the_registration_code']['_login']['_user_name']);
            $_user_number=(int)$_SESSION['_read_the_registration_code']['_login']['_user_number'];

            // echo $_user_id ;
        }

        //是否第一次借書
        $first_borrow='yes';
        if((isset($_SESSION['_read_the_registration_code']['_login']['first_borrow']))&&($_SESSION['_read_the_registration_code']['_login']['first_borrow']==='no')){
            $first_borrow='no';
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //初始化, isbn碼輸入提醒
        $_isbn_code_remind='yes';

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //串接SQL
    //---------------------------------------------------

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //---------------------------------------------------
        //學生陣列
        //---------------------------------------------------

            $users=arrys_users($conn_user,addslashes(trim($_sess_t['arrys_class_code'][0]['class_code'])),$date=date("Y-m-d"),$arry_conn_user);

            // print_r($users);

        //---------------------------------------------------
        //提取圖書館的書籍資訊
        //---------------------------------------------------

            $arrys_book_library_isbn_10=array();
            $arrys_book_library_isbn_13=array();
            $arrys_book_library=arrys_book_library($conn_mssr,mysql_prep(trim($_sess_t['school_code'])),$arry_conn_mssr);
            if(!empty($arrys_book_library)){
                foreach($arrys_book_library as $inx=>$arry_book_library){
                    $book_isbn_10=trim($arry_book_library['book_isbn_10']);
                    $book_isbn_13=trim($arry_book_library['book_isbn_13']);
                    $arrys_book_library_isbn_10[]=$book_isbn_10;
                    $arrys_book_library_isbn_13[]=$book_isbn_13;
                }
            }

        //---------------------------------------------------
        //提取所有使用者的借書證資訊
        //---------------------------------------------------

            $get_user_library_card_info=get_user_library_card_info($conn_user,$users,$array_filter=array('card_number'),$arry_conn_user);

        //---------------------------------------------------
        //isbn碼輸入提醒查詢
        //---------------------------------------------------

            $isbn_code_remind=isbn_code_remind($db_type='mysql',$arry_conn_mssr,(int)$_sess_t['uid']);

            if(!$isbn_code_remind){
                $_isbn_code_remind='no';
            }
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
		$page = (int)$_GET["page"];
		$page_limit = ($page-1 )*10;
		$auth_read_opinion_limit_day=$_SESSION['t']['read_opinion_limit_day'];

// $_SESSION['t']['read_opinion_limit_day']
// print_r($_SESSION);
	//---------------------------------------------------
	//SQL
	//---------------------------------------------------
		$array = array();
		$sday = date("Y-m-d",strtotime("-".$auth_read_opinion_limit_day." day"));
		$sql = "
                SELECT book_borrow_log_B.borrow_sid,
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
                             AND `mssr_book_borrow_log`.`user_id`=".$_user_id."
                             GROUP BY mssr_book_borrow_log.book_sid 

                       ) AS  `book_borrow_log_A`

                	  ORDER BY book_borrow_log_A.borrow_sdate DESC

                ) AS book_borrow_log_B
                LEFT JOIN mssr_book_read_opinion_log
                ON mssr_book_read_opinion_log.borrow_sid = book_borrow_log_B.borrow_sid
                
                
                WHERE book_borrow_log_B.`borrow_sdate`  >= '".$sday." 00:00:00'

                AND mssr_book_read_opinion_log.book_sid is NULL
                
                
                ORDER BY  `book_borrow_log_B`.`borrow_sdate` DESC 

                "; 



		// $retrun=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

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
			$root = str_repeat("../",5)."info/book/".$val1['book_sid']."/img/front";
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
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=true);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/effect/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../css/def.css" media="all" />
    <!-- 掛載 --> 
   <link href="css/manu.css" rel="stylesheet" type="text/css">
   <script src="js/select_thing.js" type="text/javascript"></script>
</Head>
<body >

	<!--==================================================
    html內容
    ====================================================== -->
	<table  width="490"  border="0" cellpadding="0" cellspacing="0">
    <tr style="height:40px;"> 
    <td class="td_line_l_t" style=" width:80%;">書籍名稱</td>
    <td class="td_line_t"  style=" width:20%;">登記日期</td>
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
		
		for(var i =0 ; i < <? echo sizeof($array);?> ; i++)
		{
			window.document.getElementById("br_"+i).className = 'click_br_1';
		}
		window.document.getElementById("br_"+value).className = 'click_br_2';
		// console.log(value);
		
		var about_book_sid = book_sid[value];
		var about_book_name = book_name[value];
		var about_book_author= book_author[value];
		var about_book_page_count = book_page_count[value];
		var about_book_publisher = book_publisher[value];
		var book_borrow_sid = borrow_sid[value];
		var about_book_src = src[value];


		
		window.parent.parent.parent.about_book_sid=about_book_sid;
		window.parent.parent.parent.about_book_name=about_book_name;
		window.parent.parent.parent.about_book_author=about_book_author;
		window.parent.parent.parent.about_book_page_count=about_book_page_count;
		window.parent.parent.parent.about_book_publisher=about_book_publisher;
		window.parent.parent.parent.book_borrow_sid=book_borrow_sid;
		window.parent.parent.parent.about_book_src=about_book_src;

		
		window.parent.parent.parent.change_page("page_opinion_registration2");
	}

	// function set_page(value)
	// {
	// 	// echo("set_page:開啟畫面 value>"+value);
	// 	if(value !="")
	// 	{
			
	// 		window.document.getElementById("iframe").innerHTML = '<iframe id="" src="'+value+'/index.php" width="1000" height="500" scrolling="no" frameborder="0" style=" top:0px; left:0px; overflow:hidden;"></iframe>';
	// 	}
	// 	else
	// 	{
	// 		cover("讀取中");
	// 		window.document.getElementById("other_ifame").style.display = "block";
	// 		window.document.getElementById("goout").style.display = "block";
	// 		get_shelf();
	// 		coin_coin();
	// 		window.document.getElementById("iframe").innerHTML = '';
	// 	}
	// }
	function over_bar(value)
	{
		for(var i =0 ; i < <? echo sizeof($array);?> ; i++)
		{
			if(window.document.getElementById("br_"+i).className != 'click_br_2')window.document.getElementById("br_"+i).className = 'click_br_1';
		}
		if(window.document.getElementById("br_"+value).className != 'click_br_2' && window.document.getElementById("br_"+value).className != 'click_br_0')window.document.getElementById("br_"+value).className = 'click_br_3';
	}
	//cover
	// function cover(text,type)
	// {
	// 	window.parent.cover(text,type);
	// }
	//debug
	// function echo(text)
	// {
	// 	window.parent.echo(text);
	// }	
	//---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    