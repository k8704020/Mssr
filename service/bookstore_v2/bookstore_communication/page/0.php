<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,觀測所 -> 推薦內容率
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
		if($month<8 && $month >= 2)
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
	}else if($ramge == "school_all")
	{
		$ch = "AND `mssr`.`mssr_score_star_".$time."`.`school_code` = '".$school_code."'";
	}else if($ramge == "school_grade")
	{
		$ch = "AND `mssr`.`mssr_score_star_".$time."`.`school_code` = '".$school_code."' AND `mssr`.`mssr_score_star_".$time."`.`grade_code` = '".$grade_code."'";
	}else if($ramge == "school_class")
	{
		$ch = "AND `mssr`.`mssr_score_star_".$time."`.`class_code` = '".$class_code."'";
	}
	$sql = "";
	if($time == 'total')
	{
		$sql = "SELECT `user_id`,`name`,`class_name`,`grade_code`,`school_name`,`score`
			FROM `mssr`.`mssr_score_star_".$time."`
			LEFT JOIN `user`.`member`
			ON `mssr`.`mssr_score_star_".$time."`.`user_id` = `user`.`member`.`uid`
			LEFT JOIN `user`.`class`
			ON `user`.`class`.`class_code` = `mssr`.`mssr_score_star_".$time."`.`class_code`
			LEFT JOIN `user`.`class_name`
			ON `user`.`class_name`.`classroom` = `user`.`class`.`classroom`
			AND `user`.`class_name`.`class_category` = `user`.`class`.`class_category`
			LEFT JOIN `user`.`school`
			ON `user`.`school`.`school_code` = `mssr`.`mssr_score_star_".$time."`.`school_code`
			WHERE 1 = 1
			".$ch."
			ORDER BY `score` DESC";	
	}else
	{
		$sql = "SELECT `user_id`,`name`,`class_name`,`grade_code`,`school_name`,`score`
			FROM `mssr`.`mssr_score_star_".$time."`
			LEFT JOIN `user`.`member`
			ON `mssr`.`mssr_score_star_".$time."`.`user_id` = `user`.`member`.`uid`
			LEFT JOIN `user`.`class`
			ON `user`.`class`.`class_code` = `mssr`.`mssr_score_star_".$time."`.`class_code`
			LEFT JOIN `user`.`class_name`
			ON `user`.`class_name`.`classroom` = `user`.`class`.`classroom`
			AND `user`.`class_name`.`class_category` = `user`.`class`.`class_category`
			LEFT JOIN `user`.`school`
			ON `user`.`school`.`school_code` = `mssr`.`mssr_score_star_".$time."`.`school_code`
			WHERE  1 = 1
			AND `start_date` = '".$star_time[$time]."'
			".$ch."
			ORDER BY `score` DESC";		
	}
	
	$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,100),$arry_conn_mssr);

	
	
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
    	<table>
        	<tr>
            	<td width="50" style=" text-align:left;">
                	排名
                </td>
                <td width="80" style=" text-align: left;">
                	分數
                </td>
                <td width="80" style=" text-align:left;">
                	姓名
                </td>
                <td width="80" style=" text-align:left;">
                	學校
                </td>
                <td width="50" style=" text-align:left;">
                	年級
                </td>
                <td width="50" style=" text-align:left;">
                	班級
                </td>
            </tr>
        </table></div>
    <div style="background-color:#FFF; height:300px; width:720px;overflow-x:hidden;overflow-y:auto;" class="text_1">
    	<table>
        	<?php foreach($result as $key => $val){?>
        	<tr>
            	<td width="50" style=" text-align:left;">
                	<?php echo $key+1;?>
                </td>
            	<td width="80" style=" text-align:left;">
                	<?php echo $val["score"];?>
                </td>
                <td width="80" style=" text-align:left;">
                	<?php echo $val["name"];?>
                </td>
                <td width="80" style=" text-align:left;">
                	<?php echo $val["school_name"];?>
                </td>
                <td width="50" style=" text-align:left;">
                	<?php echo $val["grade_code"]."年";?>
                </td>
                <td width="50" style=" text-align:left;">
                	<?php echo $val["class_name"]."班";?>
                </td>
            </tr>
            <?php }?>
        </table>
    
    </div>
    



    <script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	
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
	
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        $(function(){
            //初始化, 禁止滑鼠事件
            $(document).on("mousewheel DOMMouseScroll", function(e){
                e.preventDefault();
                return false;
            }).dblclick(function(e){
                e.preventDefault();
                return false;
            });
        });

    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    