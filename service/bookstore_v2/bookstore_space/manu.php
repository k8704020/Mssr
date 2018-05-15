<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,太空社群 -> 搜尋星球列表
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

		$text =  trim(mysql_prep($_GET["select"]));
		$school =  mysql_prep($_GET["school"]);


		if($text != "")
		{
			/*$sql = "SELECT me.`uid` ,
						  me.`name` ,
						  me.`class_name` ,
						  me.`grade` ,
						  star_style,
						  star_declaration

					FROM
					(
						SELECT me.`uid` ,
							   me.`name` ,
							   `class_name` ,
							   `class`.`grade`
						FROM
						(
							SELECT me.`uid` ,
								   me.`name` ,
								   `class_code`
							FROM
							(

								SELECT me.`uid` ,
									   me.`name`
								FROM
								(

									SELECT  `uid` ,  `name`
									FROM  `user`.`member`
									WHERE  `name` LIKE  '%".$text."%'
								) AS me
								LEFT JOIN user.member_school
								ON me.`uid` = member_school.`uid`
								WHERE  `school_code` LIKE '".$school."%'
							) AS me
							LEFT JOIN user.student ON student.uid = me.uid
							WHERE NOW( )
							BETWEEN START AND END

							UNION ALL

							SELECT me.`uid` , me.`name` ,  `class_code`
							FROM
							(

								SELECT me.`uid` , me.`name`
								FROM
								(

									SELECT  `uid` ,  `name`
									FROM  `user`.`member`
									WHERE  `name` LIKE  '%".$text."%'
								) AS me
								LEFT JOIN `user`.member_school ON me.`uid` = member_school.`uid`
								WHERE  `school_code` LIKE '".$school."%'
							) AS me
							LEFT JOIN `user`.teacher ON teacher.uid = me.uid
							WHERE NOW( )
							BETWEEN START AND END
						) AS me
						LEFT JOIN `user`.class ON class.class_code = me.class_code
						LEFT JOIN `user`.class_name ON class.class_category = class_name.class_category
						AND class.classroom = class_name.classroom
					)AS me
					LEFT JOIN `mssr`.mssr_user_info ON
					mssr_user_info.user_id = me.uid

					GROUP BY me.uid";*/
			//挑出學生+是否在學校中
			$i = 0;
			$sql = "SELECT  `uid` ,  `name`
					FROM  `user`.`member`
					WHERE  `name` LIKE  '".$text."%'";
			$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
			foreach($retrun as $key => $val)
			{
				$sql = "SELECT count(1) as count
						FROM `user`.member_school
						WHERE `uid` = '".$val['uid']."'
						AND `school_code` LIKE '".$school."'";
				$sre = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
				if($sre[0]['count'] > 0)
				{

					$data[$i]['user_id'] = $val['uid'];
					$data[$i]['name'] = $val['name'];
					$i++;
				}
			}

			//挑出各位學生的所在班級資料
			foreach($data as $key => $val)
			{
				//查學生
				$sql = "SELECT  `class_code`
						FROM `user`.`student`
						WHERE `uid` = '".$val['user_id']."'
						AND NOW( ) BETWEEN `START` AND `END`";
				$sre = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
				foreach($sre as $sre_key => $sre_val)
				{
					$data[$key]["class_code"] = $sre_val["class_code"];
				}
				//查老師
				$sql = "SELECT  `class_code`
						FROM `user`.`teacher`
						WHERE `uid` = '".$val['user_id']."'
						AND NOW( ) BETWEEN `START` AND `END`";
				$sre = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
				foreach($sre as $sre_key => $sre_val)
				{
					$data[$key]["class_code"] = $sre_val["class_code"];

				}

				//查詢class名稱
				if($data[$key]["class_code"])
				{

					//查詢class_code
					$sql = "SELECT  `class_code`,
									`grade`,
									`class_category`,
									`classroom`,
									`grade`
							FROM `user`.class
							WHERE `class_code` = '".$data[$key]["class_code"]."'
							";
					$class_code = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
					foreach($class_code as $class_code_key => $class_code_val)
					{
						//查詢class_code VER 名稱
						$sql = "SELECT  `class_name`
								FROM `user`.`class_name`
								WHERE `classroom` = '".$class_code_val["classroom"]."'
								AND `class_category` = '".$class_code_val["class_category"]."'
								";
						$class_name = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
						foreach($class_name as $class_name_key => $class_name_val)
						{

							$data[$key]['grade'] = $class_code_val['grade']."年";
							$data[$key]['class'] = $class_name_val['class_name']."班";
						}
					}


				}

				$sql = "SELECT `star_style`,
						  	   `star_declaration`
						FROM `mssr`.`mssr_user_info`
						WHERE `user_id` = '".$val['user_id']."'
						";
				$mssr_user_info = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
				foreach($mssr_user_info as $mssr_user_info_key => $mssr_user_info_val)
				{

					$data[$key]['star_style'] = $mssr_user_info_val['star_style'];
					$data[$key]['star_declaration'] = $mssr_user_info_val['star_declaration'];
				}

			}





		/*
			$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
			$data = array();
			foreach($retrun as $key => $val)
			{
				$data[$key]['user_id'] = $val['uid'];
				$data[$key]['name'] = $val['name'];
				$data[$key]['grade'] = $val['grade']."年";
				$data[$key]['class'] = $val['class_name']."班";
				$data[$key]['star_style'] = $val['star_style'];
				$data[$key]['star_declaration'] = $val['star_declaration'];
			}*/
		}
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

       // $user_id        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		//$home_id        =(isset($_GET['uid']))?(int)$_GET['uid']:$_SESSION['uid'];
		//$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
		//if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

	//---------------------------------------------------
	//SQL
	//---------------------------------------------------

?>
<!DOCTYPE HTML>
<Html>
<Head>
<style>
/*  ----------------------------------小型按鈕列------------------------------------------- */
		body{
            font-family: Microsoft JhengHei;
        }
		.table_b
		{
			background-color:#003;
		}
		.table_s
		{
			background-color:#006;
		}
		.sky_boll_green_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') 0 0;
		}
		.sky_boll_green_s:hover {
    		background: url('./img/space_btn_list_s.png') 0 -68px;
		}
		.sky_class_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -72px 0;
		}
		.sky_class_s:hover {
    		background: url('./img/space_btn_list_s.png') -72px -68px;
		}
		.sky_boll_blue_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -144px 0;
		}
		.sky_boll_blue_s:hover {
    		background: url('./img/space_btn_list_s.png') -144px -68px;
		}
		.sky_boll_pink_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -216px 0;
		}
		.sky_boll_pink_s:hover {
    		background: url('./img/space_btn_list_s.png') -216px -68px;
		}
		.sky_boll_brown_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -288px 0;
		}
		.sky_boll_brown_s:hover {
    		background: url('./img/space_btn_list_s.png') -288px -68px;
		}
		.sky_boll_purple_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -360px 0;
		}
		.sky_boll_purple_s:hover {
    		background: url('./img/space_btn_list_s.png') -360px -68px;
		}
		.sky_group_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -432px 0;
		}
		.sky_group_s:hover {
    		background: url('./img/space_btn_list_s.png') -432px -68px;
		}
		.sky_grade_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -504px 0;
		}
		.sky_grade_s:hover {
    		background: url('./img/space_btn_list_s.png') -504px -68px;
		}
		.sky_boll_yellow_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -576px 0;
		}
		.sky_boll_yellow_s:hover {
    		background: url('./img/space_btn_list_s.png') -576px -68px;
		}
		.sky_school_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -648px 0;
		}
		.sky_school_s:hover {
    		background: url('./img/space_btn_list_s.png') -648px -68px;
		}
		.sky_boll_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('./img/space_btn_list_s.png') -720px 0;
		}
		.sky_boll_s:hover {
    		background: url('./img/space_btn_list_s.png') -720px -68px;
		}
		</style>
</Head>
<body style=" background-color:#003; color:#FFFFFF;font-size:24px;" >

	<!--==================================================
    html內容
    ====================================================== -->
	<?PHP if($text == "")
	{?>
		不正確搜尋...   請點[返回搜尋]
	<?
	}else if(sizeof($data) ==0)
	{?>
    	查無資料     請點[返回搜尋]
    <?
	}else{
	?>
    <table border="0" cellpadding="0" cellspacing="0" style=" width:580px; color:#FFFFFF; overflow:hidden; table-layout: fixed; word-wrap:break-word;">
        <?PHP foreach($data as $key => $val){ ?>
        <tr style="height:65px;" id="list_<? echo $key;?>" onClick="select_one('<? echo $val["user_id"];?>',this.id)" class="table_b">
            <td class="" style=" width:100px; "><? echo $val["grade"].$val["class"];?></td>
            <td class=""  style=" width:100px;"><? echo $val["name"];?></td>
            <td class=""  style=" width:80px; position:relative;"><a class="sky_boll_<? echo $val["star_style"];?>_s" style="top:-8px;"></a></td>
            <td class=""  style=" width:300px;"><? echo $val["star_declaration"];?></td>
        </tr>
		<? }?>
    </table>
	<? }?>


    <script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------

	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	function select_one(value,id)
	{
		echo(window.parent.list_id);
		if(window.parent.list_id != -1)window.document.getElementById(window.parent.list_id).className = "table_b";
		window.parent.list_id = id;
		window.parent.select_user_id = value;
		window.parent.document.getElementById("go_select").style.display = "block";
		echo(window.parent.list_id);
		window.document.getElementById(window.parent.list_id).className = "table_s";

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



	cover("");
    </script>
</Html>














