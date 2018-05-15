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
		background: rgb(226,226,226); /* Old browsers */
		background: -moz-linear-gradient(top,  rgba(226,226,226,1) 0%, rgba(219,219,219,1) 50%, rgba(209,209,209,1) 51%, rgba(254,254,254,1) 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(226,226,226,1)), color-stop(50%,rgba(219,219,219,1)), color-stop(51%,rgba(209,209,209,1)), color-stop(100%,rgba(254,254,254,1))); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  rgba(226,226,226,1) 0%,rgba(219,219,219,1) 50%,rgba(209,209,209,1) 51%,rgba(254,254,254,1) 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  rgba(226,226,226,1) 0%,rgba(219,219,219,1) 50%,rgba(209,209,209,1) 51%,rgba(254,254,254,1) 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  rgba(226,226,226,1) 0%,rgba(219,219,219,1) 50%,rgba(209,209,209,1) 51%,rgba(254,254,254,1) 100%); /* IE10+ */
		background: linear-gradient(to bottom,  rgba(226,226,226,1) 0%,rgba(219,219,219,1) 50%,rgba(209,209,209,1) 51%,rgba(254,254,254,1) 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e2e2e2', endColorstr='#fefefe',GradientType=0 ); /* IE6-9 */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffadad', endColorstr='#ea4f4f',GradientType=0 ); /* IE6-9 */
		box-shadow: 1px 1px 1px #222;
		font-size:20px;
		/*font-weight:bold;*/
		
		}
    </style>
</Head>

<Body>
	<div style="position:absolute; left: 171px; top:-1px; width:452px; height:70px;font-size:24px;  font-weight:bold; color: #B66923">這些是被你列入黑名單的人<br>你可以點選<img src="img/no.png">取消黑名</div>
    <div id="black_list" style="position:absolute; left: 10px; top:76px; width:627px; background-color:#FFFAE6;box-shadow: -1px -1px 1px #222;">
    
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

	//=========MAIN=============
	function main()
	{
		echo("main:初始開始:讀取黑名單列表");
		cover("讀取中");
		var url = "./ajax/get_user_black.php";
		$.post(url, {
					user_id:window.parent.parent.user_id,
					user_permission:window.parent.parent.user_permission
			}).success(function (data) 
			{
				echo("AJAX:success:main():讀取黑名單列表:已讀出:"+data);
				if(data[0]!="{")
				{
					echo("AJAX:success:main():讀取黑名單列表:資料庫發生問題");
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
					if(data_array["count"] == 0)window.document.getElementById("black_list").innerHTML = "無黑名單";
					else window.document.getElementById("black_list").innerHTML = "";
					
					for(var i = 0 ; i < data_array["count"] ; i++)
					{
						window.document.getElementById("black_list").innerHTML += '<div style="position:relative; display:inline-block; height:30px;"  class="box1">　'+data_array[i]["name"]+'<img src="img/no.png" onClick="check_del('+data_array[i]["id"]+',\''+data_array[i]["name"]+'\')" style="position:absolute; top:-3px; cursor:pointer; ">　　</div>　';
					}
					
					cover("");
				}
			}).error(function(e){
				echo("AJAX:error:main():讀取黑名單列表:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){main();});
			}).complete(function(e){
				echo("AJAX:complete:main():讀取黑名單列表:");
			});
	}
	function check_del(value,name)
	{
		cover("您是否要取消<BR>對["+name+"]的黑名嗎?",2,function(){del(value)});
	}
	function del(value)
	{
		
		echo("del:初始開始:讀取黑名單列表");
		cover("讀取中");
		var url = "./ajax/set_user_black_off.php";
		$.post(url, {
					user_id:window.parent.parent.user_id,
					id:value,
					user_permission:window.parent.parent.user_permission
			}).success(function (data) 
			{
				echo("AJAX:success:del():讀取黑名單列表:已讀出:"+data);
				if(data[0]!="{")
				{
					echo("AJAX:success:del():讀取黑名單列表:資料庫發生問題");
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",2,function(){del(value);});
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
					cover("讀取中");
					main();
				}
			}).error(function(e){
				echo("AJAX:error:del():讀取黑名單列表:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){del(value);});
			}).complete(function(e){
				echo("AJAX:complete:del():讀取黑名單列表:");
			});
	}
	/*function go_home(name,id)
	{
		cover("你要前往<BR>"+name+"的書店嗎?",2,function(){window.parent.parent.location.href="../bookstore_courtyard/index.php?uid="+id;});

	}*/
	//---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	//get_shelf_count();
	main();


</script>

</Html>
