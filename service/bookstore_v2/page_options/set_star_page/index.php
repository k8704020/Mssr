<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 設定頁面 > 招呼語設定
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
                    APP_ROOT.'inc/code'
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

    ///---------------------------------------------------
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


    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

	//---------------------------------------------------
	//SQL
	//---------------------------------------------------

?>
<!DOCTYPE HTML>
<Html>
<script type="text/javascript" src="../../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
<script type="text/javascript" src="../js/check.js"></script>
<Head>

    <!-- 專屬 -->


    <style>
		@-webkit-keyframes rotate{
		0%{
			top:8px;
			left:12px;
			-moz-transform:scale(1);
			-webkit-transform:scale(1);
			-o-transform:scale(1);
			-ms-transform:scale(1);
			transform:scale(1);
		}
		40%{
			top:8px;
			left:12px;
			-moz-transform:scale(1);
			-webkit-transform:scale(1);
			-o-transform:scale(1);
			-ms-transform:scale(1);
			transform:scale(1);}
		50%{
			top:8px;
			left:12px;
			-moz-transform:scale(1.1);
			-webkit-transform:scale(1.1);
			-o-transform:scale(1.1);
			-ms-transform:scale(1.1);
			transform:scale(1.1);}
		60%{
			top:8px;
			left:12px;
			-moz-transform:scale(1);
			-webkit-transform:scale(1);
			-o-transform:scale(1);
			-ms-transform:scale(1);
			transform:scale(1);}
		100%{
			top:8px;
			left:12px;
			-moz-transform:scale(1);
			-webkit-transform:scale(1);
			-o-transform:scale(1);
			-ms-transform:scale(1);
			transform:scale(1);}
		}
		.lit{
		background-repeat: no-repeat;
		animation: 1s linear 0s normal none infinite rotate;
		-webkit-animation:1s linear 0s normal none infinite rotate;

		position: absolute;
		top: 74px;

		}
		/*中文特效用*/
		 .world_bar2
            {
            text-shadow:1px 0px 1px rgba(128,23,15,1),
                        0px -1px 1px rgba(128,23,15,1),
                        -1px 0px 1px rgba(128,23,15,1),
                        0px 1px 1px rgba(128,23,15,1),
                        1px 1px 1px rgba(128,23,15,1),
                        1px -1px 1px rgba(128,23,15,1),
                        -1px 1px 1px rgba(128,23,15,1),
                        -2px -1px 1px rgba(128,23,15,1)
						,1px 0px 1px rgba(128,23,15,1),
                        0px -1px 1px rgba(128,23,15,1),
                        -1px 0px 1px rgba(128,23,15,1),
                        0px 1px 1px rgba(128,23,15,1),
                        1px 1px 1px rgba(128,23,15,1),
                        1px -1px 1px rgba(128,23,15,1),
                        -1px 1px 1px rgba(128,23,15,1),
                        -1px -1px 1px rgba(128,23,15,1);


            font-weight:bold;
            color:#FCFFFF;
            letter-spacing:0pt;

            font-size:22px;
            text-align:left;
            font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif;
            }
        /* 微調 */
      .box1{
			background: rgb(232,239,174); /* Old browsers */
			background: -moz-linear-gradient(top,  rgba(232,239,174,1) 1%, rgba(176,229,43,1) 49%, rgba(130,193,48,1) 50%, rgba(130,193,48,1) 50%, rgba(130,193,48,1) 52%, rgba(182,209,77,1) 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,rgba(232,239,174,1)), color-stop(49%,rgba(176,229,43,1)), color-stop(50%,rgba(130,193,48,1)), color-stop(50%,rgba(130,193,48,1)), color-stop(52%,rgba(130,193,48,1)), color-stop(100%,rgba(182,209,77,1))); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  rgba(232,239,174,1) 1%,rgba(176,229,43,1) 49%,rgba(130,193,48,1) 50%,rgba(130,193,48,1) 50%,rgba(130,193,48,1) 52%,rgba(182,209,77,1) 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  rgba(232,239,174,1) 1%,rgba(176,229,43,1) 49%,rgba(130,193,48,1) 50%,rgba(130,193,48,1) 50%,rgba(130,193,48,1) 52%,rgba(182,209,77,1) 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  rgba(232,239,174,1) 1%,rgba(176,229,43,1) 49%,rgba(130,193,48,1) 50%,rgba(130,193,48,1) 50%,rgba(130,193,48,1) 52%,rgba(182,209,77,1) 100%); /* IE10+ */
			background: linear-gradient(to bottom,  rgba(232,239,174,1) 1%,rgba(176,229,43,1) 49%,rgba(130,193,48,1) 50%,rgba(130,193,48,1) 50%,rgba(130,193,48,1) 52%,rgba(182,209,77,1) 100%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e8efae', endColorstr='#b6d14d',GradientType=0 ); /* IE6-9 */
			box-shadow: 1px 1px 1px #222;
		}

		/*  ----------------------------------小型按鈕列------------------------------------------- */
		.sky_boll_green_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('../../bookstore_space/img/space_btn_list_s.png') 0 0;
		}
		.sky_boll_green_s:hover {
    		background: url('../../bookstore_space/img/space_btn_list_s.png') 0 -68px;
		}
		.sky_class_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('../../bookstore_space/img/space_btn_list_s.png') -72px 0;
		}
		.sky_class_s:hover {
    		background: url('../../bookstore_space/img/space_btn_list_s.png') -72px -68px;
		}
		.sky_boll_blue_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('../../bookstore_space/img/space_btn_list_s.png') -144px 0;
		}
		.sky_boll_blue_s:hover {
    		background: url('../../bookstore_space/img/space_btn_list_s.png') -144px -68px;
		}
		.sky_boll_pink_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('../../bookstore_space/img/space_btn_list_s.png') -216px 0;
		}
		.sky_boll_pink_s:hover {
    		background: url('../../bookstore_space/img/space_btn_list_s.png') -216px -68px;
		}
		.sky_boll_brown_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('../../bookstore_space/img/space_btn_list_s.png') -288px 0;
		}
		.sky_boll_brown_s:hover {
    		background: url('../../bookstore_space/img/space_btn_list_s.png') -288px -68px;
		}
		.sky_boll_purple_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('../../bookstore_space/img/space_btn_list_s.png') -360px 0;
		}
		.sky_boll_purple_s:hover {
    		background: url('../../bookstore_space/img/space_btn_list_s.png') -360px -68px;
		}
		.sky_group_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('../../bookstore_space/img/space_btn_list_s.png') -432px 0;
		}
		.sky_group_s:hover {
    		background: url('../../bookstore_space/img/space_btn_list_s.png') -432px -68px;
		}
		.sky_grade_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('../../bookstore_space/img/space_btn_list_s.png') -504px 0;
		}
		.sky_grade_s:hover {
    		background: url('../../bookstore_space/img/space_btn_list_s.png') -504px -68px;
		}
		.sky_boll_yellow_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('../../bookstore_space/img/space_btn_list_s.png') -576px 0;
		}
		.sky_boll_yellow_s:hover {
    		background: url('../../bookstore_space/img/space_btn_list_s.png') -576px -68px;
		}
		.sky_school_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('../../bookstore_space/img/space_btn_list_s.png') -648px 0;
		}
		.sky_school_s:hover {
    		background: url('../../bookstore_space/img/space_btn_list_s.png') -648px -68px;
		}
		.sky_boll_s{
			position:absolute;
			width:72px;
			height:68px;
			background: url('../../bookstore_space/img/space_btn_list_s.png') -720px 0;
		}
		.sky_boll_s:hover {
    		background: url('../../bookstore_space/img/space_btn_list_s.png') -720px -68px;
		}
    </style>
</Head>

<Body>
<div style="position:absolute; left: 171px; top:-1px; width:452px; height:70px;font-size:24px; font-weight:bold; color: #B66923">你可以在這裡設定星球的招呼語</div>
	<div style="position:absolute; left: 171px; top:119px; width:452px; height:70px; font-size:24px; font-weight:bold; color: #B66923">你可以在這裡選擇星球的樣式</div>
<div style="position:absolute; left: 11px; top:46px; width:529px; height:54px;" class="box1">
      	<div style="position:absolute; left: 14px; top:7px; width:112px; height:40px; font-size:24px; font-weight:bold;" class="world_bar2">星球宣言</div>
        <input id="t1" type="text" onChange="key_d('t1')"  onFocus="key_d('t1')" onkeydown="window.setTimeout( function(){ key_d('t1')}, 30)" onkeyup="checkBadWords('t1')" style="position:absolute; left: 131px; top:9px; width: 388px; height: 36px; font-size:24px">
        <div id="t1_t" style="position:absolute; left: 534px; top:7px; width:112px; height:40px; font-size:24px; font-weight:bold;" ></div>
	</div>
    <div style="position:absolute; left: 11px; top:144px; width:638px; height:131px;" class="box1">
      	<img src="../../bookstore_space/img/sky_back.png"  style="position:absolute; left: 15px; top:13px; height:104px; width: 608px;">
        <!-- 選取的動畫 -->
        <div id="linting" style="position:absolute; left: 0px; top:0px;">
       		<img src="./img/shing.png"  style="position:absolute; left: 12px; top:8px;" class="lit">
        </div>
        <!-- 五顆星球體  痾痾-->
        <a onClick="chick_star('green',1)" class="sky_boll_green_s"  style="position:absolute; left: 35px; top:32px;"></a>
        <a onClick="chick_star('blue',1)" class="sky_boll_blue_s"  style="position:absolute; left: 135px; top:32px;"></a>
        <a onClick="chick_star('pink',1)" class="sky_boll_pink_s"  style="position:absolute; left: 232px; top:32px;"></a>
        <a onClick="chick_star('yellow',1)" class="sky_boll_yellow_s"  style="position:absolute; left: 332px; top:32px;"></a>
        <a onClick="chick_star('brown',1)" class="sky_boll_brown_s"  style="position:absolute; left: 430px; top:32px;"></a>
        <a onClick="chick_star('purple',1)" class="sky_boll_purple_s"  style="position:absolute; left: 530px; top:32px;"></a>
    </div>

</Body>
<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var on_page = 1;
	var on_chick = -1;
	var star_style = "green";
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------

	/*cover 啟用器的用法
	 cover("這嘎");
	 cover("這嘎",1);
	 cover("這嘎",2,function(){echo("哈哈");});
	 cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){get_booking_count();});
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
	//cover
	function cover(text,type,proc)
	{

		window.parent.cover(text,type);
		if(type == 2)
		{
			delayExecute(proc);
		}
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}
	//即時算字數
	function key_d(value)
	{
		window.parent.document.getElementById("save_btn").style.display = "block";
		echo(window.document.getElementById(value).value.length);
		if(window.document.getElementById(value).value.length > 14)
		{
			window.document.getElementById(value+"_t").innerHTML= "太多字了";
			window.document.getElementById(value+"_t").style.color = "#F00";
		}
		else
		{
			window.document.getElementById(value+"_t").innerHTML= window.document.getElementById(value).value.length+"/"+14+"字";
			window.document.getElementById(value+"_t").style.color = "#000";
		}

	}

		//確認不雅字眼
function checkBadWords(value){
		//console.log(value);

	
		var inputVal=document.getElementById(value);
		// var censored = regCheck(inputVal.value, constant.badWords);
  //     
        var censored = regCheck(inputVal.value, constant.badWords) === false ?  false : true ;

        // console.log("regCheck:",regCheck(inputVal.value, constant.badWords));
		return censored;

	}

	function regCheck(string, filters) {
     // "i" is to ignore case and "g" for global
     var regex = new RegExp(filters.join("|"), "gi");
     // console.log(regex);
     var checked = regex.test(string);
     
     if(checked) {
       cover("偵測到含有不適合的詞彙",1);
       return false
     } else {
       return true
     }
 }
	//選擇星球
	function chick_star(text,vlaue)
	{
		var tmp = 0;
		if(text == 'green')tmp = 0;
		else if(text == 'blue')tmp = 1;
		else if(text == 'pink')tmp = 2;
		else if(text == 'yellow')tmp = 3;
		else if(text == 'brown')tmp = 4;
		else if(text == 'purple')tmp = 5;
		if(vlaue)window.parent.document.getElementById("out").style.display = "block";
		if(vlaue)window.parent.document.getElementById("save_btn").style.display = "block";

		window.document.getElementById("linting").style.left = (tmp*100)+"px";
		star_style = text;

	}
	//=========MAIN=============
	function main()
	{
		echo("main:初始開始:讀取星球資料");
		cover("讀取中");
		var url = "./ajax/get_star_info.php";
		$.post(url, {
					user_id:window.parent.parent.user_id,
					user_permission:window.parent.parent.user_permission
			}).success(function (data)
			{
				echo("AJAX:success:main():讀取星球資料:已讀出:"+data);
				if(data[0]!="{")
				{
					echo("AJAX:success:main():讀取星球資料:資料庫發生問題");
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",2,function(){main();});
					return false;
				}

				data_array = JSON.parse(data);

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
					window.document.getElementById("t1").value=data_array["star_declaration"];
					chick_star(data_array["star_style"],0);
					cover("");
				}
			}).error(function(e){
				echo("AJAX:error:main():讀取星球資料:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){main();});
			}).complete(function(e){
				echo("AJAX:complete:main():讀取星球資料:");
			});
	}
	function go_save(fun,value)
	{
		if(window.document.getElementById("t1").value.length > 14)
		{
			cover("星球宣言輸入太多字了喔!!",1);
			return false;
		}
		if(!checkBadWords("t1")){
				// console.log(i);
				cover("偵測到含有不適合的詞彙!!",1);
				return false;

		}
		echo("get_rec_count:初始開始:儲存星球資料");
		cover("存檔中");
		window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e31',window.parent.parent.action_on);

		var url = "./ajax/set_star_info.php";
		$.post(url, {
					user_id:window.parent.parent.user_id,
					user_permission:window.parent.parent.user_permission,
					t1:window.document.getElementById("t1").value,
					star_style:star_style

			}).success(function (data)
			{
				echo("AJAX:success:get_rec_count():儲存星球資料:已讀出:"+data);
				if(data[0]!="{")
				{
					echo("AJAX:success:get_rec_count():儲存星球資料:資料庫發生問題");
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",2,function(){go_save();});
					return false;
				}

				data_array = JSON.parse(data);

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
					if(fun != null)
					{
						fun(value);
					}else
					{
						window.parent.document.getElementById("save_btn").style.display = "none";
						cover("存檔完成",1);
					}
				}
			}).error(function(e){
				echo("AJAX:error:get_rec_count():儲存星球資料:");
				cover("維修中請稍後",2,function(){go_save();});
			}).complete(function(e){
				echo("AJAX:complete:main():儲存星球資料:");
			});

	}
	//---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	//get_shelf_count();
	main();


</script>

</Html>
