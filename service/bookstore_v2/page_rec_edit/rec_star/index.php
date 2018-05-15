<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 推薦框 -> 評星推薦
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
        require_once(str_repeat("../",4).'config/config.php');

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

        $user_id        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
		if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
        //GET
        $uid          =(isset($_GET['uid']))?(int)$_GET['uid']:$user_id;
        $book_sid    =(isset($_GET['book_sid']))?mysql_prep($_GET['book_sid']):0;
        $book_name         =(isset($_GET['book_name']))?mysql_prep($_GET['book_name']):"？";

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
	<script src="../../js/select_thing.js" type="text/javascript"></script>
    <!-- 掛載 -->
    <script type="text/javascript" src="../../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
    <link rel="stylesheet" href="../../css/btn.css">


    <style type="text/css">
	.star{
			position:absolute;
			width:31px;
			height:30px;
			background: url('./img/star.png') -31px 0;
		}
	.star_n{
			position:absolute;
			width:31px;
			height:30px;
			background: url('./img/star.png') 0 0;
		}
	.tillt {
		font-size: 36px;
		font-family: Verdana, Geneva, sans-serif;
		font-weight: bold;
		color: #000;
	}
	.u9ipup {
		text-align: center;
	}
	.tillt_2 {
		font-weight: bold;
		font-size: 24px;
		color: #000;
	}
	.tillt_1 {
		font-size: 24px;
		color: #000;
	}
	/*改過*/
	.r_btn{
		position:absolute;
		width:39px;
		height:39px;
		left: 350px;
		background: url('./img/ong_btn.png') 0 0;
	}
	.r_btn:hover {
    	background: url('./img/ong_btn.png') 0 -39px;
	}
	/*改過*/
	.r_btn_n{
		position:absolute;
		width:39px;
		height:39px;
		left: 350px;
		background: url('./img/ong_btn.png') -39px 0;
	}
	.r_btn_n:hover {
    	background: url('./img/ong_btn.png') -39px -39px;
	}

	.r_1{
		position:absolute;
		width:65px;
		height:60px;
		background: url('./img/str2_btn.png') 0 0;
	}
	.r_1:hover {
    	background: url('./img/str2_btn.png') 0 -60px;
	}
	.r_1n{
	position:absolute;
	width:65px;
	height:60px;
	background: url('./img/str2_btn.png') -65px 0;

	}
	.r_1n:hover {
    	background: url('./img/str2_btn.png') -65px -60px;
	}

	.r_2{
		position:absolute;
		width:65px;
		height:60px;
		background: url('./img/str2_btn.png') -130px 0;
	}
	.r_2:hover {
    	background: url('./img/str2_btn.png') -130px -60px;
	}
	.r_2n{
	position:absolute;
	width:65px;
	height:60px;
	background: url('./img/str2_btn.png') -195px 0;

	}
	.r_2n:hover {
    	background: url('./img/str2_btn.png') -195px -60px;
	}

	.r_3{
		position:absolute;
		width:65px;
		height:60px;
		background: url('./img/str2_btn.png') -260px 0;
	}
	.r_3:hover {
    	background: url('./img/str2_btn.png') -260px -60px;
	}
	.r_3n{
	position:absolute;
	width:65px;
	height:60px;
	background: url('./img/str2_btn.png') -325px 0;

	}
	.r_3n:hover {
    	background: url('./img/str2_btn.png') -325px -60px;
	}

	.r_4{
		position:absolute;
		width:65px;
		height:60px;
		background: url('./img/str2_btn.png') -385px 0;
	}
	.r_4:hover {
    	background: url('./img/str2_btn.png') -385px -60px;
	}
	.r_4n{
	position:absolute;
	width:65px;
	height:60px;
	background: url('./img/str2_btn.png') -456px 0;

	}
	.r_4n:hover {
    	background: url('./img/str2_btn.png') -456px -60px;
	}

	.r_5{
		position:absolute;
		width:77px;
		height:60px;
		background: url('./img/str2_btn.png') -531px 0;
	}
	.r_5:hover {
    	background: url('./img/str2_btn.png') -531px -60px;
	}
	.r_5n{
	position:absolute;
	width:77px;
	height:60px;
	background: url('./img/str2_btn.png') -617px 0;

	}
	.r_5n:hover {
    	background: url('./img/str2_btn.png') -617px -60px;
	}

	</style>
</Head>
<body style="overflow:hidden;">

	<!--==================================================
    html內容
    ====================================================== -->


    <table width="765" border="0" height="480" style="position:absolute; top:1px; left:129px;">
        <tr>
            <td width="358" align="center">

                <table width="84%" border="0" height="440" style="table-layout: fixed; word-break: break-all; position:absolute; top:23px; left: 22px; width: 336px; height: 309px;">
                    <tr>
                        <td height="76" align="center" class="tillt"><img style="position:absolute; top:-23px; left:-28px;" src="img/tittle_2.png" /></td>
                    </tr>
                       <!-- 改過 -->
                        <td  style="position: absolute; left:35px; top:80px;" ><img src="img/rec_book.png" width="196" height="240" /></td>

                    <tr>
                        <td height="2" align="center" class="tillt_1" id="rec_star_book_name" > </td>
                    </tr>
                    <tr>
                    	<!-- 改過 -->
                        <td class="tillt_2" style="position: absolute; left:95px; top:330px;" ><img src="img/text14.png" /></td>
                    </tr>
                    <tr>
                        <td align="center">
                        <!-- 改過 -->
                        <div id="rec_star_rank_ans1" onClick="set_rank(1)" name="rec_star_rank_ans1" style="cursor: pointer;position:absolute; top:375px; left:45px;" class="star_n"></div>
                        <!-- 改過 -->
                        <div id="rec_star_rank_ans2" onClick="set_rank(2)" name="rec_star_rank_ans2" style="cursor: pointer;position:absolute; top:375px; left:85px;" class="star_n"></div>
                        <!-- 改過 -->
                        <div id="rec_star_rank_ans3" onClick="set_rank(3)" name="rec_star_rank_ans3" style="cursor: pointer;position:absolute; top:375px; left:125px;" class="star_n"></div>
                        <!-- 改過 -->
                        <div id="rec_star_rank_ans4" onClick="set_rank(4)" name="rec_star_rank_ans4" style="cursor: pointer;position:absolute; top:375px; left:165px;" class="star_n"></div>
                        <!-- 改過 -->
                        <div id="rec_star_rank_ans5" onClick="set_rank(5)" name="rec_star_rank_ans5" style="cursor: pointer; position:absolute; top:375px; left:205px;" class="star_n"></div>
                    </tr>
                </table>


          </td>
            <td width="397">
                <table width="100%" border="0" height="100%">
                    <tr>
                        <td  height="21" >&nbsp;</td>
                    </tr>
                    <tr>
                    	  <!-- 改過 -->
                        <td  class="tillt_2"  style="position: absolute; left:440px; top:20px;"><img src="img/text6.png" /></td>
                    </tr>
                    <tr>
                        <td height="355"  class="tillt_1">
                            <table width="100%" border="0" height="100%">

                    	  <!-- 改過 -->
                                <tr>
                                	<td>&nbsp;&nbsp;<a class="r_btn" border="0" onClick="set_reason(0)" id="rec_star_reason_ans0" name="rec_star_reason_ans1"  style="cursor: pointer;"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="img/text7.png" style="position: absolute;left:380px; top:100px" /></td>
                                </tr>

                    	  <!-- 改過 -->
                               <tr>
                               		<td> &nbsp;&nbsp;<a class="r_btn" border="0" onClick="set_reason(1)" id="rec_star_reason_ans1" name="rec_star_reason_ans2"  style="cursor: pointer;"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="img/text8.png" style="position: absolute;left:380px; top:150px"/></td>
                               </tr>

                    	  <!-- 改過 -->
                                <tr>
                                	<td>&nbsp;&nbsp;<a class="r_btn" border="0" onClick="set_reason(2)" id="rec_star_reason_ans2" name="rec_star_reason_ans3" style="cursor: pointer;" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="img/text9.png" style="position: absolute;left:380px; top:200px" /></td>
                               </tr>

                    	  <!-- 改過 -->
                               <tr>
                               		<td> &nbsp;&nbsp;<a class="r_btn" border="0" onClick="set_reason(3)" id="rec_star_reason_ans3" name="rec_star_reason_ans4" style="cursor: pointer;" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="img/text10.png" style="position: absolute;left:380px; top:250px" /></td>
                               	</tr>

                    	  <!-- 改過 -->
                               <tr>
                               		<td> &nbsp;&nbsp;<a class="r_btn" border="0" onClick="set_reason(4)" id="rec_star_reason_ans4" name="rec_star_reason_ans5"  style="cursor: pointer;"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="img/text11.png" style="position: absolute;left:380px; top:300px"/></td>
                               </tr>

                    	  <!-- 改過 -->
                               <tr>
                               		<td> &nbsp;&nbsp;<a class="r_btn" border="0" onClick="set_reason(5)" id="rec_star_reason_ans5" name="rec_star_reason_ans6" style="cursor: pointer;" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="img/text12.png" style="position: absolute;left:380px; top:353px" /></td>
                               </tr>

                    	  <!-- 改過 -->
                               <tr>
                               		<td> &nbsp;&nbsp;<a class="r_btn" border="0" onClick="set_reason(6)" id="rec_star_reason_ans6" name="rec_star_reason_ans7" style="cursor: pointer;" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="img/text13.png" style="position: absolute;left:380px; top:405px" /></td>
                               </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td  height="30">&nbsp;</td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
    <img src="../../img/UI_savef.png" style="position:absolute; top:286px; left:879px; cursor: no-drop; opacity:0.4" border="0">
	<a id="save_btn" class="btn_save" src="../img/save.png" onClick="go_save()" style="position:absolute; top:286px; left:879px; cursor:pointer; display:none"></a>


    <script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
     var book_sid = '<? echo $book_sid;?>';
	 var book_name = '<? echo $book_name;?>';
	 var uid = '<? echo $uid;?>';
	 var reason = "xxxxxxx";
	 var rank = -1;
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------

	//cover
	function cover(text,type,proc,proc2)
	{

		window.parent.cover(text,type);
		if(type == 2 && proc != null)
		{
			delayExecute(proc);
		}
		else if(type == 3 && proc2 != null)
		{
			delayExecute(proc,proc2);
		}
	}
	/*cover 啟用器的用法
	 cover("這嘎");
	 cover("這嘎",1);
	 cover("這嘎",2,function(){echo("哈哈");});
	*/
	//cover 點選器
	function delayExecute(proc,proc2) {
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
			else if(window.parent.parent.cover_click ==2 )
			{//點選取消的狀況
				cover_click = -1;
				cover_level = 0;
				window.clearInterval(hnd);
				proc2();
				echo("COVER點選取消");
			}
		}, x);
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}
	function set_rank(value,start)
	{
		if(value != -1)
		{
			if(start != 1)window.parent.save_lock = 1;
			if(start != 1)window.document.getElementById("save_btn").style.display = "block";
			rank = value;
			for(var i = 1; i <= 5; i++)
			{
				if(value >= i)window.document.getElementById("rec_star_rank_ans"+i).className = "star";
				else window.document.getElementById("rec_star_rank_ans"+i).className = "star_n";
			}
			//window.document.getElementById("rec_star_rank_ans"+value).className = "r_"+value;
		}else
		{
			for(var i = 1; i <= 5; i++)
			{
				window.document.getElementById("rec_star_rank_ans"+i).className = "star_n";
			}
			//if(rank!= -1)window.document.getElementById("rec_star_rank_ans"+rank).className = "r_"+rank;
		}
		echo("改變"+rank);
	}
	function set_reason(value)
	{

		var tmp = reason;
		if(value != -1)
		{
			window.parent.save_lock = 1;
			window.document.getElementById("save_btn").style.display = "block";
			tmp = "";
			for(var i = 0; i < 7 ; i++)
			{
				if(value != i)
				{
					tmp = tmp + reason[i];
				}else
				{
					if(reason[value] == 'x')
					{
						tmp = tmp + 'o';
					}
					else if(reason[value] == 'o')
					{
						tmp = tmp + 'x';
					}
				}
			}
		}

		//確認選項是否超過2個
		var click_over = 0
		for(var i = 0; i < 7 ; i++)
		{
			if(tmp[i] == 'o') click_over++;
		}
		if(click_over >2)
		{
			cover("理由不能選超過2個喔",1);
		}
		else
		{
			for(var i = 0; i < 7 ; i++)
			{
				if(tmp[i] == 'o')
				window.document.getElementById("rec_star_reason_ans"+i).className = "r_btn_n";
				else
				window.document.getElementById("rec_star_reason_ans"+i).className = "r_btn";
			}
			reason = tmp;
		}

		echo("改變"+reason);
	}

	function go_save(fun,value,state)
	{
		echo("go_save:評星存檔判定");
		//確認可否存檔
		//星等要有
		if(rank == -1)
		{

			cover("'星等'還沒選擇",1);
			return false ;
		}
		//理由要有
		var click_over = 0;
		for(var i = 0; i < 7 ; i++)
		{
			if(reason[i] == 'o') click_over++;
		}
		if(click_over>2 || click_over == 0)
		{
			cover("'理由'還沒選擇",1);
			return false ;
		}
		echo("go_save:通過 進入存檔階段");

		set_rec_star(fun,value,state);
	}

	//存檔
	function set_rec_star(fun,value,state)
	{
		echo("set_rec_star:初始開始:儲存評星資料>"+book_sid+"_"+uid);
		cover("儲存評星資料中");
		window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e15',window.parent.parent.action_on);

		var url = "./ajax/set_rec_star.php";
		$.post(url, {
					user_id:uid,
					book_sid:book_sid,
					rank:rank,
					reason:reason,
					user_permission:'<? echo $permission;?>'

			}).success(function (data)
			{

				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",2,function(){set_rec_star();});
					echo("AJAX:success:set_rec_star():儲存評星資料:資料庫發生問題");
					return false;
				}

				data_array = JSON.parse(data);
				echo("AJAX:success:set_rec_star():儲存評星資料:已讀出:"+data);
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
					window.document.getElementById("save_btn").style.display = "none";
					window.parent.save_lock = 0;

					if(fun!= null)
					{

						fun(value,state);

					}
					else
					cover("存檔完畢",1);
				}

			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){
				echo("AJAX:complete:set_rec_star():儲存評星資料:");
			});
	}

	//=========MAIN=============
	function main()
	{
		echo("Main:初始開始:讀取評星資料>"+book_sid+"_"+uid);
		cover("讀取評星資料");
		window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e9',window.parent.parent.action_on);
		var url = "./ajax/get_rec_star.php";
		$.post(url, {
					user_id:uid,
					book_sid:book_sid,

					user_permission:'<? echo $permission;?>'
			}).success(function (data)
			{
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",2,function(){main();});
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
					rank = data_array["rec_star_rank"];
					reason = data_array["rec_star_reason"];

					set_reason(-1);
					set_rank(rank,1);

					cover("");
				}

			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){
				echo("AJAX:complete:main():讀取使用者資料:");
			});
	}

	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
$.ajaxSetup({
		timeout: 15*1000
	});
       main();

    </script>
</Html>














