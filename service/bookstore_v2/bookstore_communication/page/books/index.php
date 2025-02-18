<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,閱讀登記 -> 登記書本
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
        require_once(str_repeat("../",5).'config/config.php');
		require_once(str_repeat("../",5)."inc/get_book_info/code.php");

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

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
        //GET
		//GET
        $user_id     =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:die("嗯?");
        $ramge    =(isset($_GET['ramge']))?mysql_prep($_GET['ramge']):die("嗯?");
        $time   =(isset($_GET['time']))?mysql_prep($_GET['time']):die("嗯?");

		$class_code   =(isset($_GET['class_code']))?mysql_prep($_GET['class_code']):die("嗯?");
		$school_code  =(isset($_GET['school_code']))?mysql_prep($_GET['school_code']):die("嗯?");
		$grade_code   =(isset($_GET['grade_code']))?mysql_prep($_GET['grade_code']):die("嗯?");
		//外掛時間參數檔 $star_time array
		include_once("../../inc/date.php");

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

	//---------------------------------------------------
	//SQL
	//---------------------------------------------------

		//條件
	$class_code_array = array();
	//$user_id = '154912';//新營國小 陳德輪
	//$class_code   = 'dxi_2017_2_4_8_2';
	//$class_code_array = explode("_",$class_code);
	//echo "<Pre>";print_r($class_code_array[4]);echo "</Pre>";
	//$school_code  = 'dxi';
	//$grade_code   = '4';
	$where_text = '';
	switch ($ramge) {//自己學校
		case 'all':
			$where_text = "";
		break;
		case 'all_grade'://所有學校同年級
			$where_text = 
			" AND grade_id = '".$grade_code."'";
			 //".$school_code."
		break;
		case 'school_grade'://同校同年級
			$where_text = 
			" AND grade_id = '".$grade_code."' 
			  AND school_code = '".$school_code."'";
			 //".$grade_code." js_2017_2_4_3_2
		break;
		case 'school_class'://同班
			$where_text = 
			" AND grade_id = '".$grade_code."' 
			  AND school_code = '".$school_code."'
			  AND classroom_id = '".$class_code_array[4]."'";
			 //".$grade_code." js_2017_2_4_3_2
		break;
	}
	
	//時間判斷
	$where_time = "";
	switch ($time) {
		case 'now_week':
			$where_time = "AND borrow_edate >= '".$star_time['now_week']."'";
		break;
		case 'last_week':
			$where_time = "AND borrow_edate >= '".$star_time['last_week']."' AND borrow_edate < '".$star_time['now_week']."'";
		break;
		case 'now_month':
			$where_time = "AND borrow_edate >= '".$star_time['now_month']."'";
		break;
		case 'last_month':
			$where_time = "AND borrow_edate >= '".$star_time['last_month']."' AND borrow_edate < '".$star_time['now_month']."'";
		break;
		case 'now_semester':
			$where_time = "AND borrow_edate >= '".$star_time['now_semester']."'";
		break;
		case 'last_semester':
			$where_time = "AND borrow_edate >= '".$star_time['last_semester']."' AND borrow_edate < '".$star_time['now_semester']."'";
		break;
		default:
			$where_time = "AND borrow_edate >= '".$star_time['now_week']."'";
		break;
		
	}
	
	
	$sql = "SELECT book_sid, count(book_sid) book_sid_count
			FROM mssr.mssr_book_borrow_log 
			where 1=1
			AND grade_id != 0
			AND classroom_id != 0
			".$where_text."
			".$where_time."
			group by book_sid
			order by book_sid_count desc";
	

	

//echo "<Pre>";print_r($sql);echo "</Pre>";
$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,100),$arry_conn_mssr);
$data_array = array();
foreach($result as $key => $val)
{
	//找書本資訊Start
	//mbc = mssr_book_class
	//mbg = mssr_book_global
	//mbl = mssr_book_library
	//mbu = mssr_book_unverified
	$ch = '';
	switch (substr($val['book_sid'],0,3)) {
		case 'mbc':
			$ch = 'mssr_book_class';
		break;
		case 'mbg':
			$ch = 'mssr_book_global';
		break;
		case 'mbl':
			$ch = 'mssr_book_library';
		break;
		case 'mbu':
			$ch = 'mssr_book_unverified';
		break;
		
	}
	$sql_book_sid = "SELECT book_name,  book_author, book_publisher FROM mssr.".$ch." where book_sid = '".$val['book_sid']."' limit 1;";
	//echo "<pre>";print_r($sql2s);echo "</pre>";
	$result_book_name = db_result($conn_type='pdo',$conn_mssr,$sql_book_sid,$arry_limit=array(),$arry_conn_mssr);
	
	if($type ==0){//暫時不包含老師
		//玩家ID
		$data_array[$key]['user_id'] = $val["user_id"];
		//書本UID
		$data_array[$key]['book_sid'] = $val["book_sid"];
		//按讚數
		$data_array[$key]['book_sid_count'] = $val["book_sid_count"];
		//書名
		$data_array[$key]['book_name'] = $result_book_name[0]["book_name"];
		//作者
		$data_array[$key]['book_author'] = $result_book_name[0]["book_author"];
		//出版社
		$data_array[$key]['book_publisher'] = $result_book_name[0]["book_publisher"];
	}
	//找書本資訊End
}

echo "<Pre>";print_r($data_array);echo "</Pre>";


?>
<!DOCTYPE HTML>
<Html >
<Head>
	<Title></Title>
    <!-- 掛載 -->



    <style>
       .text_1 {
		    display: block;
			width: 100%;
			height: 34px;
			padding: 3px 6px;
			font-size: 18px;
			line-height: 1.42857143;
			color: #555;
			background-color: #fff;
			background-image: none;
			border: 1px solid #ccc;
			border-radius: 4px;


	}

	</style>
</Head>
<body >

	<!--==================================================
    html內容
    ====================================================== -->
	<div style="background-color:#D8E2F1; width:720px; font-weight:bold;" class="text_1">
    	<table width="710" >
        	<tr>
            	<td width="50" style=" text-align:left;">
                	排名
                </td>
                <td width="110" style=" text-align: left;">
                	借閱次數
                </td>
                <td width="300" style=" text-align:left;">
                	書籍名稱
                </td>
                <td width="80" style=" text-align:left;">
                	作者
                </td>
                <td width="100" style=" text-align:left;">
                	出版社
                </td>
          </tr>
        </table></div>
    <div style=" background-color:#FFF; height:300px; width:720px;overflow-x:hidden;overflow-y:auto;" class="text_1">
    	<table width="710" style="table-layout:fixed;word-wrap:break-word;">
        	<?php foreach($data_array as $key => $val){?>
            <?php
                //echo "<Pre>";print_r($key);echo "</Pre>";
            ?>
        	<tr onmouseover="this.style.cursor='pointer';this.style.backgroundColor='#d8e2f1';" onmouseout="this.style.backgroundColor='#fff';"
            onclick="show_jump_bar('<?php echo $val["book_sid"];?>')">
            	<td   valign="top" style=" width:50px; text-align:left; ">
                	<?php echo $key+1;?>
                </td>
            	<td   valign="top" style=" width:110px;  text-align:left;">
                	<?php echo $val["book_sid_count"];?>
                </td>
                <td   valign="top" style=" width:300px;  text-align:left; overflow:hidden; table-layout:fixed;word-wrap:break-word;">
                	<?php echo $val["book_name"];?>
                </td>
                <td   valign="top" style=" width:80px;  text-align:left; overflow:hidden; table-layout:fixed;word-wrap:break-word;">
                	<?php echo $val["book_author"];?>
                </td>
                <td   valign="top" style=" width:100px;  text-align:left; overflow:hidden; table-layout:fixed;word-wrap:break-word;">
                	<?php echo $val["book_publisher"];?>
                </td>
            </tr>
            <?php }?>
        </table>

    </div>

    <div id="jump_bar" style="width:0px; height:0px; position:absolute; top:0px; left:0px; display:none;">
		<div class="border_" style="background-color:#2f1308; opacity:0.85; width:730px; min-height:356px; top:8px; left:13px; position:absolute;"></div>
		<textarea id="jump_text" readonly
        style="BORDER-BOTTOM: 0px solid; BORDER-LEFT: 0px solid; BORDER-RIGHT: 0px solid; BORDER-TOP: 0px solid; min-height: 282px; resize: none; color:#FFF; font-size:20px; position:absolute; top:24px; width: 690px; left: 37px; background-color:transparent;"
        ></textarea>
   		<button onClick="window.document.getElementById('jump_bar').style.display='none';"
        onmouseover="this.style.cursor='pointer'"
        style="position:absolute; top:325px; left:335px; width:100px; font-size:18px;">關閉</button>
	</div>



    <script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------

        var json_book_info=<?php echo json_encode($arrys_book_info);?>;
        var ojump_bar=document.getElementById('jump_bar');
        var ojump_text=document.getElementById('jump_text');

	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------

	//cover
	function cover(text,type,proc)
	{

		window.parent.cover(text,type);
		if(type == 2)
		{
			delayExecute(proc);
		}
	}
	/*cover 啟用器的用法
	 cover("這嘎");
	 cover("這嘎",1);
	 cover("這嘎",2,function(){echo("哈哈");});
	*/
	//cover 點選器
	function delayExecute(proc) {
		var x = 100;
		var hnd = window.setInterval(function () {
			if(window.parent.parent.cover_click ==1 )
			{//點選確定的狀況
				window.parent.parent.cover_click = -1;
				window.parent.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選確定");
				proc();
				cover("");
			}
			else if(window.parent.parent.cover_click ==0 )
			{//點選取消的狀況
				window.parent.parent.cover_click = -1;
				window.parent.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選取消");
				cover("");
			}
		}, x);
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}
	//=========MAIN=============
	function main()
	{
		echo("Main:初始開始:讀取使用者資料");
		cover("讀取使用者資料中")
		var url = "../ajax/get_user_info.php";
		$.post(url, {
					user_id:window.parent.user_id,
					user_permission:window.parent.user_permission

			}).success(function (data)
			{

				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",1);
					echo("AJAX:success:main():讀取使用者資料:資料庫發生問題");
					return false;
				}

				data_array = JSON.parse(data);
				echo("AJAX:success:main():讀取使用者資料:已讀出:"+data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					cover(data_array["echo"],1);

				}else
				{
					cover("");
				}

			}).error(function(e){
				echo("AJAX:error:main():讀取使用者資料:");
				window.alert("喔喔?! 讀取失敗了喔  請確認網路連");
				main();
			}).complete(function(e){
				echo("AJAX:complete:main():讀取使用者資料:");
			});
	}

    function show_jump_bar(book_sid){
        var book_name       =(json_book_info[book_sid]['book_name']);
        var book_author     =(json_book_info[book_sid]['book_author']);
        var book_publisher  =(json_book_info[book_sid]['book_publisher']);
        var book_note       =(json_book_info[book_sid]['book_note']);
        //console.log(book_name);
        //console.log(book_author);
        //console.log(book_publisher);
        //console.log(book_note);

        ojump_text.value=book_name+"\n"+"─".repeat(60)+"\n";
        ojump_text.value+="【作者】"+book_author+"\n\n";
        ojump_text.value+="【出版社】"+book_publisher+"\n\n";
        ojump_text.value+="【簡介】\n"+book_note+"\n\n";
        ojump_bar.style.display='';
        return false;
    }

	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------



    </script>
</Html>














