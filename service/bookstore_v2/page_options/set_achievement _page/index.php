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
<Head>
 
    <!-- 專屬 -->


    <style>
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
    </style>
</Head>

<Body>
	<div style="position:absolute; left: 171px; top:-1px; width:452px; height:70px;font-size:24px; font-weight:bold; color: #B66923">你可以在這邊訓練店員<br>讓他店員用你的方式招呼客人</div>
        
      <div style="position:absolute; left: 11px; top:76px; width:529px; height:54px;" class="box1">
            <div style="position:absolute; left: 14px; top:7px; width:112px; height:40px; font-size:24px; font-weight:bold;" class="world_bar2">招呼語１</div>
            <input id="t1" type="text" onkeydown="window.setTimeout( function(){ key_d('t1')}, 30)" style="position:absolute; left: 131px; top:9px; width: 388px; height: 36px; font-size:24px">
            <div id="t1_t" style="position:absolute; left: 534px; top:7px; width:112px; height:40px; font-size:24px; font-weight:bold;" ></div>
      </div>
        <div style="position:absolute; left: 11px; top:131px; width:529px; height:54px;" class="box1">
            <div style="position:absolute; left: 14px; top:7px; width:112px; height:40px; font-size:24px; font-weight:bold;" class="world_bar2">招呼語２</div>
            <input id="t2" type="text" onkeydown="window.setTimeout( function(){ key_d('t2')}, 30)" style="position:absolute; left: 131px; top:9px; width: 388px; height: 36px; font-size:24px">
            <div id="t2_t" style="position:absolute; left: 534px; top:7px; width:112px; height:40px; font-size:24px; font-weight:bold;" ></div>
        </div>
        <div style="position:absolute; left: 11px; top:186px; width:529px; height:54px;" class="box1">
          <div style="position:absolute; left: 14px; top:7px; width:112px; height:40px; font-size:24px; font-weight:bold;" class="world_bar2">招呼語３</div>
           <input id="t3" type="text" onkeydown="window.setTimeout( function(){ key_d('t3')}, 30)" style="position:absolute; left: 131px; top:9px; width: 388px; height: 36px; font-size:24px">
           <div id="t3_t" style="position:absolute; left: 534px; top:7px; width:112px; height:40px; font-size:24px; font-weight:bold;" ></div>
      </div>
        <div style="position:absolute; left: 11px; top:241px; width:529px; height:54px;" class="box1">
            <div style="position:absolute; left: 14px; top:7px; width:112px; height:40px; font-size:24px; font-weight:bold;" class="world_bar2">招呼語４</div>
            <input id="t4" type="text" onkeydown="window.setTimeout( function(){ key_d('t4')}, 30)" style="position:absolute; left: 131px; top:9px; width: 388px; height: 36px; font-size:24px">
            <div id="t4_t" style="position:absolute; left: 534px; top:7px; width:112px; height:40px; font-size:24px; font-weight:bold;" ></div>
      </div>
      <div style="position:absolute; left: 11px; top:296px; width:529px; height:54px;" class="box1">
            <div style="position:absolute; left: 14px; top:7px; width:112px; height:40px; font-size:24px; font-weight:bold;" class="world_bar2">招呼語５</div>
            <input id="t5" type="text" onkeydown="window.setTimeout( function(){ key_d('t5')}, 30)" style="position:absolute; left: 131px; top:9px; width: 388px; height: 36px; font-size:24px">
            <div id="t5_t" style="position:absolute; left: 534px; top:7px; width:112px; height:40px; font-size:24px; font-weight:bold;" ></div>
      </div>
  

</Body>
<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	var on_page = 1;
	var on_chick = -1;

	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	function out()
	{
		window.parent.set_page("");	
	}
	
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
		if(window.document.getElementById(value).value.length > 20)
		{
			window.document.getElementById(value+"_t").innerHTML= "太多字了";
			window.document.getElementById(value+"_t").style.color = "#F00";
		}
		else
		{
			window.document.getElementById(value+"_t").innerHTML= window.document.getElementById(value).value.length+"/"+20+"字";
			window.document.getElementById(value+"_t").style.color = "#000";
		}
		
	}
	//=========MAIN=============
	function main()
	{
		echo("get_rec_count:初始開始:讀取說會內容");
		cover("讀取中");
		var url = "./ajax/get_clerk_talk.php";
		$.post(url, {
					user_id:window.parent.parent.user_id,
					user_permission:window.parent.parent.user_permission
			}).success(function (data) 
			{
				echo("AJAX:success:get_rec_count():讀取說會內容:已讀出:"+data);
				if(data[0]!="{")
				{
					echo("AJAX:success:get_rec_count():讀取推薦筆數:資料庫發生問題");
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
					
					window.document.getElementById("t1").value = data_array["clerk_talk"][0];
					window.document.getElementById("t2").value = data_array["clerk_talk"][1];
					window.document.getElementById("t3").value = data_array["clerk_talk"][2];
					window.document.getElementById("t4").value = data_array["clerk_talk"][3];
					window.document.getElementById("t5").value = data_array["clerk_talk"][4];
					
					cover("");
				}
			}).error(function(e){
				echo("AJAX:error:get_rec_count():讀取說會內容:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){main();});
			}).complete(function(e){
				echo("AJAX:complete:main():讀取說會內容:");
			});
	}
	function go_save(fun,value)
	{
		for( var i = 1; i <= 5 ; i++)
		{
			if(window.document.getElementById("t"+i).value.length > 20)
			{
				cover("招呼語"+i+"輸入太多字了喔!!",1);
				return false;
			}
		}
		echo("get_rec_count:初始開始:儲存對話內容");
		cover("存檔中");
		var url = "./ajax/set_clerk_talk.php";
		$.post(url, {
					user_id:window.parent.parent.user_id,
					user_permission:window.parent.parent.user_permission,
					t1:window.document.getElementById("t1").value,
					t2:window.document.getElementById("t2").value,
					t3:window.document.getElementById("t3").value,
					t4:window.document.getElementById("t4").value,
					t5:window.document.getElementById("t5").value
			}).success(function (data) 
			{
				echo("AJAX:success:get_rec_count():儲存對話內容:已讀出:"+data);
				if(data[0]!="{")
				{
					echo("AJAX:success:get_rec_count():儲存對話內容:資料庫發生問題");
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
					if(window.document.getElementById("t1").value == "" && window.document.getElementById("t2").value == "" && window.document.getElementById("t3").value == "" && window.document.getElementById("t4").value == "" && window.document.getElementById("t5").value == "")
					{}else
					{
						parent.parent.clerk_talk = new Array();
						if(window.document.getElementById("t1").value!="")parent.parent.clerk_talk.push(window.document.getElementById("t1").value);
						if(window.document.getElementById("t2").value!="")parent.parent.clerk_talk.push(window.document.getElementById("t2").value);
						if(window.document.getElementById("t3").value!="")parent.parent.clerk_talk.push(window.document.getElementById("t3").value);
						if(window.document.getElementById("t4").value!="")parent.parent.clerk_talk.push(window.document.getElementById("t4").value);
						if(window.document.getElementById("t5").value!="")parent.parent.clerk_talk.push(window.document.getElementById("t5").value);
					}
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
				echo("AJAX:error:get_rec_count():儲存對話內容:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){go_save();});
			}).complete(function(e){
				echo("AJAX:complete:main():儲存對話內容:");
			});
		
	}
	//---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	//get_shelf_count();
	main();


</script>

</Html>
