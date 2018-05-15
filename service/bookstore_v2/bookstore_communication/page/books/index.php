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

		$star_time = array();
		//============周============
		$first_day = 0;
		$getdate = date("Y-m-d");
		//取得一周的第幾天,星期天開始0-6
		$weekday = date("w", strtotime($getdate));
		//要減去的天數
		$del_day = $weekday - $first_day;
		//本週開始日期
		$week_s = date("Y-m-d", strtotime("$getdate -".$del_day." days"));
		//上週開始日期
		$star_time["week"] = date('Y-m-d',strtotime("$week_s - 7 days"));


		//============月============
		//本月開始日期
		$month_s = date('Y-m-01');
		//上月開始日期
		$_tmptime = strtotime($month_s);
		$_tmptime = strtotime('-1 month', $_tmptime);
		$star_time["month"] = date("Y-m-d", $_tmptime);



		//============年============
		$month = (int)date('m');

		$year = date('Y');
		if($month < 2)
		{
			$flag = 2;
			$year_s = date('Y-08-01',strtotime("-1 year"));
		}
		else if($month<8)
		{
			$flag = 1;
			$year_s = date('Y-02-01');
		}
		else
		{
			$flag = 2;
			$year_s = date('Y-08-01');
		}


		if($flag == 2)
		{
			$flag = 1;
			$_tmptime = strtotime($year_s);
			$_tmptime = strtotime('-6 month', $_tmptime);
			$star_time["semester"] = date("Y-m-d", $_tmptime);
		}
		else
		{
			$flag = 1;
			$_tmptime = strtotime($year_s);
			$_tmptime = strtotime('-6 month', $_tmptime);
			$star_time["semester"] = date("Y-m-d", $_tmptime);
		}

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

	//---------------------------------------------------
	//SQL
	//---------------------------------------------------


	if($ramge == "all")
	{
		$ch = "";
	}else if($ramge == "all_school_grade")
	{
		$ch = "AND `mssr`.`mssr_score_books_".$time."`.`grade_code` = '".$grade_code."'";
	}else if($ramge == "school_grade")
	{
		$ch = "AND `mssr`.`mssr_score_books_".$time."`.`school_code` = '".$school_code."' AND `mssr`.`mssr_score_books_".$time."`.`grade_code` = '".$grade_code."'";
	}else if($ramge == "school_class")
	{
		$ch = "AND `mssr`.`mssr_score_books_".$time."`.`class_code` = '".$class_code."'";
	}
	$sql = "";
	if($time == 'total')
	{
		$sql = "SELECT SUM(`score`) AS `book_score`,`book_sid`
				FROM `mssr`.`mssr_score_books_".$time."`
				WHERE 1 = 1
				".$ch."
				GROUP BY `book_sid`
				ORDER BY `book_score` DESC";
	}else
	{
		$sql = "SELECT SUM(`score`) AS `book_score`,`book_sid`
				FROM `mssr`.`mssr_score_books_".$time."`
				WHERE 1 = 1
				AND `start_date` = '".$star_time[$time]."'
				".$ch."
				GROUP BY `book_sid`
				ORDER BY `book_score` DESC";
	}

    $arrys_book_info=[];
	$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,100),$arry_conn_mssr);
	foreach($result as $key => $val)
	{
		$array_select = array("book_name","book_author","book_publisher","book_note");
		$get_book_info=get_book_info($conn='',$val['book_sid'],$array_select,$arry_conn_mssr);
        if(!empty($get_book_info)){
            $result[$key]["book_sid"]       = $val['book_sid'];
            $result[$key]["book_name"]      = htmlspecialchars(trim($get_book_info[0]['book_name']));
            $result[$key]["book_author"]    = htmlspecialchars(trim($get_book_info[0]['book_author']));
            $result[$key]["book_publisher"] = htmlspecialchars(trim($get_book_info[0]['book_publisher']));
            $result[$key]["book_note"]      = htmlspecialchars(trim($get_book_info[0]['book_note']));

            $arrys_book_info[$val['book_sid']]['book_name']     =htmlspecialchars(trim($get_book_info[0]['book_name']));
            $arrys_book_info[$val['book_sid']]['book_author']   =htmlspecialchars(trim($get_book_info[0]['book_author']));
            $arrys_book_info[$val['book_sid']]['book_publisher']=htmlspecialchars(trim($get_book_info[0]['book_publisher']));
            $arrys_book_info[$val['book_sid']]['book_note']     =htmlspecialchars(trim($get_book_info[0]['book_note']));
        }
	}

//echo "<Pre>";print_r($arrys_book_info);echo "</Pre>";
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
                	獲得讚數
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
        	<?php foreach($result as $key => $val){?>
            <?php
                //echo "<Pre>";print_r($key);echo "</Pre>";
            ?>
        	<tr onmouseover="this.style.cursor='pointer';this.style.backgroundColor='#d8e2f1';" onmouseout="this.style.backgroundColor='#fff';"
            onclick="show_jump_bar('<?php echo $val["book_sid"];?>')">
            	<td   valign="top" style=" width:50px; text-align:left; ">
                	<?php echo $key+1;?>
                </td>
            	<td   valign="top" style=" width:110px;  text-align:left;">
                	<?php echo $val["book_score"];?>
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

	<!-- 跳超大的窗窩 -->
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

	    //console.log(location.href);
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














