<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 推薦框 -> 繪圖推薦
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
		require_once("functions.php");


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
		//SESSION
        $sess_uid   =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$permission = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
		$book_sid    =(isset($_GET['book_sid']))?mysql_prep($_GET['book_sid']):0;
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
		if($permission =='0' || $sess_uid=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");
    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
		$ftp_root="http://".$arry_ftp1_info['host']."/mssr/info/";

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

?>

<!DOCTYPE html>
<html onselectstart="return false" ondragstart="return false">
<head>
<meta charset="utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

<title>DrawStory Draw</title>
<link rel="stylesheet" href="css/font-awesome.min.css">
<link href="css/main2.css" rel="stylesheet" type="text/css">
<link href="js/jquery-ui-1.8.18.custom/css/ui-lightness/jquery-ui-1.8.21.custom.css" rel="stylesheet" type="text/css">


<script src="js/jquery-1.7.1.min.js"></script>
<script src="js/jquery-ui-1.8.18.custom.min.js"></script>
<script src="js/jquery.tabSlideOut.v1.3.js"></script>
<script src="js/jquery.blockUI.js"></script>
<script src="js/jquery.ui.touch-punch.min.js"></script>
<link rel="stylesheet" href="../../css/btn.css">

<script>
var open_page_on = 0;
var rec_draw_score = 0;
var storyId = 0;
var page_num = 0;
var now_page = 0;
var drawPage = 1;
var time_lock = false ;
var time = 0 ;
var auth_coin_open =  window.parent.parent.auth_coin_open;
var uid =<?php echo (int)$sess_uid;?>;
var book_id = '<?php echo trim($book_sid);?>';
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
					window.parent.parent.cover_click = -1;
					window.parent.parent.cover_level = 0;
					window.clearInterval(hnd);
					proc2();
					echo("COVER點選取消");
				}
			}, x);
		}
		//cover
		function cover(text,type,proc,proc2)
		{
		//	echo(proc2);
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
</script>

<script src="js/drawDefine_v2.js"></script>
<script src="js/drawFunction_v2.js"></script>
<style>
	/*改過*/
	.pic_list_class_df
	{
		background-color:#333;
		position:relative;
		margin:5px 7px;
		width:25px;
		height:25px;
		float:left;
		top: 3px;

		font-size:24px;
		font-weight:bolder;
		color:#FFFFFF;
		text-align:center;
		cursor:pointer;
		border-radius: 6px;
	}
	/*改過*/
	.pic_list_class_on
	{
		background-color:#6386ED;
		position:relative;
		margin:5px 7px;
		width:25px;
		height:25px;
		float:left;
		top: 3px;
		font-size:24px;
		font-weight:bolder;
		color:#fff;
		text-align:center;
		cursor:pointer;
		border-radius: 6px;
	}
	/*改過*/
	.pic_list_class_df:hover
	{
		background-color:#aaa;
		position:relative;
		margin:5px 7px;
		width:25px;
		height:25px;
		float:left;
		top: 3px;

		font-size:24px;
		font-weight:bolder;
		color:#111;
		text-align:center;
		cursor:pointer;
		border-radius: 6px;
	}
	/*改過*/
	#pic_chang_list span{
		float: left;
		font-weight: 600;
		font-size: 20px;
		position: relative;
		top: 7px;
		margin-left: 5px;
	}
	

	.uploadImg{
		
			float: right;
			right: 5px;
			top: 9px;
			position:absolute;
			
	}
	/* .uploadPlus:hover {
    		background: url('./img/fupload_icon.png') -13px 30px;
	} */
	</style>
</head>
<body style=" position:absolute; top:-20px;left:-10px; overflow:hidden;" >

	<img style="position:absolute; top:13px; left:120px;" src="img/tittle_2.png" />
	<div class="wrap"  >



	  </div>






        <div class="toolbar" id="ftool">
            <div id="undo" class="fbutton">
                <img id="on_2" src="img/trans.png" alt="undo" height="32px" title="回復筆畫"/>
            </div>
            <div id="newPage" class="fbutton">
                <img id="on_3" src="img/1330996603_Default_Document-64.png" alt="new"
                    height="32px" title="清空畫布"/>
            </div>
            <div id="chBcColor" class="fbutton">
                <img id="on_4" src="img/1331629987_22.png" alt="save" height="32px" title="更換底色"/>
            </div>
          <!--   <div id="" class="fbutton">
                <img id="on_0" src="img/1330996603_Default_Document-TT.png" onClick="open_page_s(1)" alt="save" style=" opacity:1" height="32px" title="上傳"/>
            </div> -->
            <div id="" class="fbutton">
                <img id="on_1" src="img/13309R.png" onClick="open_page(0)" alt="save" style=" opacity:0.2" height="32px" title="畫圖"/>
            </div>

            <div id="pic_chang_list" style="background-color:#f9dacf; position:absolute; left:260px; top:2px; width:285px; height:38px; display:none;border-radius: 8px;float:left; display:none; ">
            	<span >畫板:</span>
            	<div id="pic_list_0" onClick="setpic(0)"  class="pic_list_class_on" style="display:block;">1</div>
				<div id="pic_list_1" onClick="setpic(1)" class="pic_list_class_df" style="display:none;">2</div>
            	<div id="pic_list_2" onClick="setpic(2)" class="pic_list_class_df" style="display:none;">3</div>
				<div id="pic_list_3" onClick="setpic(3)" class="pic_list_class_df" style="display:none;">4</div>
				<!--上傳圖片-->
				<div id="on_0" class="uploadImg" onClick="open_page_s(1)"   style="display:block;" ><input type="button" name="上傳圖片"value="上傳圖片"></div>
				<!-- <div id="on_1" src="img/13309R.png" onClick="open_page(0)" ><i class="fa fa-times-circle-o fa-2x"></i></div> -->
            </div>


            <!--<div id="addPic" class="fbutton">
                <img src="img/1342428344_Folder - Default.png" alt="save" height="30px" />
            </div>-->
            <canvas id="paperColor" class="bordered" width="256xpx" height="256px">
                Sorry, your browser is not supported.</canvas>
        </div>
        <div id="btn_cover" style="position:absolute; width:180px; height:46px; top:6px; left:305px; display:none; cursor:no-drop"> </div>

		<div style="position:absolute; top:12px; left:0px;">

                <img src="../../img/UI_savef.png" alt="save" style="position:absolute; top:286px; left:879px; cursor: no-drop; opacity:0.4" border="0">
    	</div>
    	<!-- 改過 -->
        <div id="saveImg" style="position:absolute; top:12px; left:0px;">
        		<a id="save_btn" class="btn_save" alt="save" style="position:absolute; top:286px; left:879px; cursor:pointer; display:none"></a>
        </div>
        <!-- 改過 -->

        	<canvas id="sketchpad" class="bordered" width="700" height="400" style="position: absolute; left: 113px; top:50px;">
            Sorry, your browser is not supported.</canvas>

		<!-- 改過 -->
        <div class="toolbar" id="dtool">
            <a class="handle" href="javascript:void(0);" style=" font-size:34px; top:-40px;">調色盤</a>
            <div id="lw_container">
                <div id="lw_slider"></div>
                <div id="lw_show">
                    <div></div>
                </div>
            </div>
            <div id="pen" class="dbutton">
                <img src="img/1331014433_brush.png" />
            </div>
            <div id="eraser" class="dbutton">
                <img src="img/1331023433_clear.png" width="80" />
            </div>

            <div id="cp_container">


                <canvas id="cStamp" class="bordered" width="182px" height="182px">
                    Sorry, your browser is not supported.</canvas>
            </div>

            <div id="toolLock"></div>
        </div>
        <input style="display:none;" id="upload" type="file" multiple/>

	</div>
    <iframe id="open_page"   scr="" frameborder="0" style="position:absolute; left:100px; top:40px; width:720px; height:450px; display:none;"></iframe>
    <div style="display:none;" id="wait">123</div>


<script>
		var ftp_root ="<? echo $ftp_root; ?>";
		function open_page_s(value)
		{
			if(isSaved)
			{
				open_page(value);
				return false ;
			}

			cover("在離開之前<BR>是否要存檔",3,function(){
					open_page(value);
				},function(){
					saveImg(canvas.toDataURL(),$(canvas).css("background-color"),drawPage,uid,book_id,open_page(value),value,"");
				});
			return false;
		}

		function setpic(value)
		{
			if(now_page == value)return false ;
			if(isSaved)
			{
				set_pic(value);
				return false ;
			}

			cover("在離開之前<BR>是否要存檔",3,function(){
					set_pic(value);
				},function(){
					saveImg(canvas.toDataURL(),$(canvas).css("background-color"),drawPage,uid,book_id,set_pic(value),value,"");
				});
			return false;
		}
		function get_base64_fome_jpg(pic_url)
		{
			var url = "./ajax/get_rec_updata_draw.php";
			$.post(url, {
					pic_url:pic_url
			}).success(function (data)
			{

				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看",2,function(){chick_pic();});
					return false;
				}

				data_array = JSON.parse(data);
				echo("AJAX:success:main():讀取繪圖資料:已讀出:"+data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					window.alert(data_array["echo"]);
					cover(data_array["echo"],1);

				}else
				{
					context.clearRect (0, 0, $(canvas).width(), $(canvas).height());
					//$("#sketchpad").css("background","withe");

					var img = new Image(700,400);

					img.onload = function () {


						context.drawImage(img, 0, 0,700,400);
						$(canvas).css("background-color","withe");
						modeChange(drawMode);

						cover("");
						undoStack = new Array();
						undoStack.push(canvas.toDataURL()); //saveAction to undo list
						//max undo step 30１
						while(undoStack.length > 30){
							undoStack.shift();
						}

						cover("");
					};

					img.src = data_array["data"];

				}

			}).error(function(e){

				cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){

			});
		}

		function set_pic(value,aa)
		{
			modeChange ('pen');
			now_page = value;
			for(var i = 0 ; i <= 3 ; i++)
			{
				if(i == now_page)window.document.getElementById("pic_list_"+i).className = "pic_list_class_on";
				else window.document.getElementById("pic_list_"+i).className = "pic_list_class_df";
			}

			cover("讀取圖片中");
			if(value == 0)
			{
				drawPage = 1;
				if(uid!=0)loadImg(drawPage,uid,book_id);
				return false;

			}
			drawPage = "upload_"+value;
			var d = new Date();
			get_base64_fome_jpg(ftp_root+"user/"+uid+"/book/"+book_id+"/draw/bimg/upload_"+value+".jpg?t="+d.getTime());



			//$("#sketchpad").css("background","url('../../../../info/user/"+uid+"/book/"+book_id+"/draw/bimg/upload_"+value+".jpg') 0 0");
		}
		function open_page(value)
		{

			if(open_page_on == 0 && value ==1 )
			{
				window.document.getElementById("pic_chang_list").style.display = "none";
				window.document.getElementById('open_page').src = "./page_up_image/index.php?book_sid=<?php echo trim($book_sid);?>";
				window.document.getElementById('open_page').style.display = "block";
				//window.document.getElementById("titl_text").innerHTML = "上傳功能";
				window.document.getElementById("on_4").style.opacity = "0.2";
				window.document.getElementById("on_3").style.opacity = "0.2";
				window.document.getElementById("on_2").style.opacity = "0.2";
				// window.document.getElementById("on_0").style.opacity = "0.2";
				window.document.getElementById("on_1").style.opacity = "1";
				window.document.getElementById('btn_cover').style.display = "block";
				window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e11',window.parent.parent.action_on);
				open_page_on = 1;
			}
			else if(open_page_on == 1 && value ==0 )
			{
				chick_pic();
				set_pic(now_page,"");
				window.document.getElementById('open_page').src = "";
				window.document.getElementById('open_page').style.display = "none";
				//window.document.getElementById("titl_text").innerHTML = "繪圖功能";
				window.document.getElementById("on_4").style.opacity = "1";
				window.document.getElementById("on_3").style.opacity = "1";
				window.document.getElementById("on_2").style.opacity = "1";
				// window.document.getElementById("on_0").style.opacity = "1";
				window.document.getElementById("on_1").style.opacity = "0.2";
				window.document.getElementById('btn_cover').style.display = "none";
				window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e10',window.parent.parent.action_on);
				open_page_on = 0;

			}

		}

		function go_save(fun,value,state)
			{

				saveImg(canvas.toDataURL(),$(canvas).css("background-color"),drawPage,uid,book_id,fun,value,state);
				isSaved = true;
			}

		/*cover 啟用器的用法
		 cover("這嘎");
		 cover("這嘎",1);
		 cover("這嘎",2,function(){echo("哈哈");});
		*/

		//debug
		function echo(text)
		{
			window.parent.echo(text);
		}
		//計時器裝置=================

		//事件 :  操作時變更的設定
		function set_rec_on_edit(value)
		{
			time_lock = value;
			window.parent.save_lock = 1;
			window.document.getElementById("save_btn").style.display = "block";
		}

		//事件 :  設置計時器
		function timedCount()
		{
			if(time_lock)time++;
			//echo(time);
			rec_edit_setTimeout=setTimeout("timedCount()",1000);
		}
		timedCount();

	//function chick pic
	function chick_pic()
	{

		var url = "./ajax/chick_pic.php";
		$.post(url, {
					user_id:uid,
					book_sid:book_id
			}).success(function (data)
			{

				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看",2,function(){chick_pic();});
					echo("AJAX:success:main():讀取使用者資料:資料庫發生問題");
					return false;
				}

				data_array = JSON.parse(data);
				echo("AJAX:success:main():讀取繪圖資料:已讀出:"+data);
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
					for(var i = 1 ; i<=3 ; i++)
					{
						if(data_array[i] == "y")window.document.getElementById("pic_list_"+i).style.display = "block";
						else if(data_array[i] == "n")window.document.getElementById("pic_list_"+i).style.display = "none";

					}
					window.document.getElementById("pic_chang_list").style.display = "block";
				}

			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){
				echo("AJAX:complete:main():讀取繪圖資料:");

			});


	}
	//=========MAIN=============
	function main()
	{
		echo("Main:初始開始:讀取繪圖資料>"+book_id+"_"+uid);
		cover("讀取繪圖資料");
		window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e10',window.parent.parent.action_on);
		var url = "./ajax/get_rec_draw.php";
		$.post(url, {
					user_id:uid,
					book_sid:book_id,

					user_permission:'<? echo $permission;?>'
			}).success(function (data)
			{   
				console.log(data);
				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看",2,function(){main();});
					echo("AJAX:success:main():讀取使用者資料:資料庫發生問題");
					return false;
				}

				data_array = JSON.parse(data);
				echo("AJAX:success:main():讀取繪圖資料:已讀出:"+data);
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
					 for(var key in data_array){
                        if(typeof(data_array[key]) == 'object' ){
                        
                             window.parent.set_content(data_array[key]["rec_draw_content"],data_array[key]["rec_draw_score"],data_array[key]["keyin_cdate"],key);
                           

                        }

                    }


                    console.log(data_array);
					
					rec_draw_score=data_array["rec_draw_score"];
					chick_pic();



				}

			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){
				echo("AJAX:complete:main():讀取繪圖資料:");
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
</body>

</html>