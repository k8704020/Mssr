<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 推薦框 -> 朗讀推薦
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

		//建立連線 user
		$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

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
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
		if($permission =='0' || $sess_uid=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");
    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
        //GET
		$book_sid    =(isset($_GET['book_sid']))?mysql_prep($_GET['book_sid']):0;
        $book_name         =(isset($_GET['book_name']))?mysql_prep($_GET['book_name']):"？";
		$ftp_root="http://".$arry_ftp1_info['host']."/mssr/info/user/";
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,總店";

        //-----------------------------------------------
        //查找, 錄音資訊
        //-----------------------------------------------

            $has_record         =false;
            $root               =str_repeat("../",4)."info/user/".(int)$sess_uid."/book";

            $record_path_mp3    ="{$root}/".trim($book_sid)."/record/1.mp3";


            $record_path_wav    ="{$root}/".trim($book_sid)."/record/1.wav";


            $record_path        ='';
			$record_file       ='';
            if(file_exists($record_path_mp3)){
                $has_record =true;
                $record_path=$record_path_wav;
				$record_file       ='mp3';
            }
            if(file_exists($record_path_wav)){
                $has_record =true;
                $record_path=$record_path_wav;
				$record_file       ='wav';
            }

		//-----------------------------------------------
        //搜尋評分
        //-----------------------------------------------
		$array = array();
		// $array['rec_record_content'] = "";
		// $array['rec_record_score'] = "";
		$sql = "SELECT rec_state
			FROM  `mssr_rec_book_record_log`
			WHERE  `mssr_rec_book_record_log`.`user_id` =  '".$sess_uid."'
			AND  `mssr_rec_book_record_log`.`book_sid` =  '".$book_sid."'
			ORDER BY  `mssr_rec_book_record_log`.`keyin_cdate` DESC ";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		foreach($retrun as $key1=>$val1)
		{

			  //搜尋有無評分
			  $sql_tmp = "SELECT comment_content,comment_score,keyin_cdate
						FROM  mssr_rec_comment_log
						WHERE mssr_rec_comment_log.book_sid = '".$book_sid."'
						AND mssr_rec_comment_log.comment_to = '".$sess_uid."'
						AND comment_type='record'
						ORDER BY  `mssr_rec_comment_log`.`keyin_cdate` DESC 
                        limit 5;
                        ";
			  $retrun_tmp = db_result($conn_type='pdo',$conn_mssr,$sql_tmp,$arry_limit=array(0,1),$arry_conn_mssr);
			  if(count($retrun_tmp)>0)
			  {   
                foreach($retrun_tmp as $key2=>$val2){
				  $array[$key2]['rec_record_content'] = mysql_prep($val2['comment_content']);
				  $array[$key2]['rec_record_score'] = mysql_prep($val2['comment_score']);
                  $array[$key2]['keyin_cdate'] = mysql_prep($val2['keyin_cdate']);
                }

			  }

		}


?>
<html lang="en">

<head>
    <Title><?php echo $title;?></Title>
        <link rel="stylesheet" type="text/css" href="../rec_draw/page_up_image/css/code.css" media="all" />
        <script type="text/javascript" src="../../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>

    <link rel="stylesheet" href="css/code.css">


    <!-- 通用 -->
    <link rel="stylesheet" href="../../css/btn.css">
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
	<script src="../../../../lib/jquery/ui/code.js"></script>
	<script src="../../js/select_thing.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/array/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/fso/code.js"></script>
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link rel="author" type="text/html" href="https://plus.google.com/+MuazKhan">
    <meta name="author" content="Muaz Khan">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">


    <style>
	.cord{
            box-shadow:
                        2px 2px 0px rgba(0,0,83,1),
                        2px 0px 0px rgba(0,0,83,1),
                        0px 2px 0px rgba(0,0,83,1);
            font-weight:bold;
            color:#000;
            letter-spacing:0pt;



            font-family:Microsoft JhengHei,comic sans, comic sans ms, cursive, verdana, arial, sans-serif;
        }
	/*蘭標題特效效用*/
        .blue_bar
        {

            font-weight:bold;
            color:#6d94fd;
            letter-spacing:0pt;

            font-size:40px;
            text-align:right;

            font-family:Microsoft JhengHei,comic sans, comic sans ms, cursive, verdana, arial, sans-serif;
        }
    audio {
        vertical-align: bottom;
        width: 10em;
    }
    video {
        max-width: 100%;
        vertical-align: top;
    }
    input {
        border: 1px solid #d9d9d9;
        border-radius: 1px;
        font-size: 2em;
        margin: .2em;
        width: 30%;
    }
    p,
    .inner {
        padding: 1em;
    }
    li {
        border-bottom: 1px solid rgb(189, 189, 189);
        border-left: 1px solid rgb(189, 189, 189);
        padding: .5em;
    }
    label {
        display: inline-block;
        width: 8em;
    }
    </style>

    <style>
        .recordrtc button {
            font-size: inherit;
        }

        .recordrtc button, .recordrtc select {
            vertical-align: middle;
            line-height: 1;
            padding: 2px 5px;
            height: auto;
            font-size: inherit;
            margin: 0;
        }

        .recordrtc, .recordrtc .header {
            display: block;
            text-align: center;
            padding-top: 0;
        }

        .recordrtc video {
            width: 70%;
        }

        .recordrtc option[disabled] {
            display: none;
        }
		button, input[type=button] {
			-moz-border-radius: 3px;
			-moz-transition: none;
			-webkit-transition: none;
			background: #0370ea;
			background: -moz-linear-gradient(top, #008dfd 0, #0370ea 100%);
			background: -webkit-linear-gradient(top, #008dfd 0, #0370ea 100%);
			border: 1px solid #076bd2;
			border-radius: 3px;
			color: #fff;
			display: inline-block;
			font-family: inherit;
			font-size: .8em;
			line-height: 1.3;
			padding: 5px 12px;
			text-align: center;
			text-shadow: 1px 1px 1px #076bd2;
			font-size: 1.5em;
		}

		button:hover, input[type=button]:hover {
			background: rgb(9, 147, 240);
		}

		button:active, input[type=button]:active {
			background: rgb(10, 118, 190);
		}

		button[disabled], input[type=button][disabled] {
			background: none;
			border: 1px solid rgb(187, 181, 181);
			color: gray;
			text-shadow: none;
		}
		.aa{}
    </style>

    <script src="./js/RecordRTC.js"></script>
    <script src="./js/gif-recorder.js"></script>
    <script src="./js/getScreenId.js"></script>

    <!-- for Edige/FF/Chrome/Opera/etc. getUserMedia support -->
    <script src="./js/adapter.js"></script>
</head>

<body class="aa">
	<img style="position:absolute; top:1px; left:120px;" src="img/tittle_2.png" />
    <!-- 改過 -->
    <img src="./img/recode_2v2.png?vo" style="position:absolute; top:53px; left:100px;">
    <!-- 改過 -->
    <img src="./img/recode_3.png" style="position:absolute; top:59px; left:490px;">
   <!--  <img src="./img/recode_4.png" style="position:absolute; top:239px; left:526px;"> -->
    <div id='block' style="position:absolute; display:none; top:0px; left:0px; width:1000px; height:480px; background:#222222; opacity:0.8"></div>
    <span id="go_recode" class='btn btn-success' onClick="window.document.getElementById('updata_page').style.display='block';window.parent.set_top_btn('none');" style=" display:none;position:absolute; top:303px; left:533px;">上傳</span>
    <!-- 改過 -->
    <span id="" class='btn btn-success' onClick="$('#updata_page').show();" style="position:absolute; top:250px; left:525px;">上傳錄音檔</span>
    <!-- 改過 -->
    <span id="up_data" class='btn btn-success' onClick="go_recode()" style="position:absolute; top:250px; left:670px;">開啟線上錄音裝置</span>
    <!-- 改過 -->
    <span id="up_load" class='btn btn-success' onClick="up_load()" style=" display:none;position:absolute; top:340px; left:720px;z-index:2;">錄音後重整</span>
    <!-- 改過 -->
    <div id="player_con"  style="position:absolute; top:300px; left:515px; width:300px; height:76px;">尚未錄音</div>
    <!--<span id="donw_data" class='btn btn-success' onClick="cover('開始下載檔案',1)" style="position:absolute; top:250px; left:927px;">下載</span>
-->


<? if(($_SESSION['class'][0][1] == 'gcp_2013_2_3_9' || $_SESSION['class'][0][1] == 'gcp_2013_2_3_4' || $_SESSION['class'][0][1] == 'gcp_2013_2_3_1' ))//聊書判斷入口
		{?>
<div  style="position:absolute; top:187px; left:873px; color:#FFFFFF; width:120px; font-size:24px">
    前往聊書
    <BR>



    <!--<img onClick="go_to_take()" src="../img/to_toke.png" style="cursor: pointer;"> -->
</div><? }?>

<!-- <? echo $array[$i]['rec_record_score'];?> -->
<!-- 上傳頁面  -->
    <div id = "updata_page" style=" position:absolute; display:none; top:0px; left:0px;">
      <div style="position:absolute; display:; top:0px; left:0px; width:1000px; height:480px; background:#111; opacity:0.8"></div>
      <div style="background-color:#f6e5bc; position:absolute; border-radius:10px; top:108px; left:267px; width:454px; height:208px;"></div>
      <div class="cord" style="background-color:#fff; padding-top:30px; padding-left:40px; border-radius:5px; position:absolute; top:119px; left:284px; width:386px; height:108px; font-size:20px; line-height: 24px;"><BR>1.先點選[選擇檔案]<BR>2.選擇MP3或WAV的檔案<BR>3.點選[上傳]將檔案上傳伺服器</div>
        <div class="blue_bar" style="position:absolute; top:123px; left:237px; width:183px; color:#6d94fd; font-family: Microsoft JhengHei; font-size:30px; height: 49px;">上傳功能</div>

      <form name="Form1" id="Form1" action="" method="post" enctype="multipart/form-data" onSubmit="return false">
            <input type="hidden" id="book_sid" name="book_sid" value="<?php echo addslashes($book_sid);?>">
            <input type="hidden" id="auth_coin_open" name="auth_coin_open" value="">
            <input id="file" name="file" type="file" class="form_text" style='width:323px; position:absolute; left:284px; top:267px; font-size:22px'/>
        <span id="BtnU" class='btn btn-success' style="position:absolute; top:275px; left:629px; width: 63px;">上傳</span>
		</form>
   <span id="close_uppage" onClick="window.document.getElementById('updata_page').style.display='none';window.parent.set_top_btn('block');"  class='btn btn-info' style="position:absolute; top:325px; left:455px; width: 63px;">關閉</span>
    </div>


    <article>


        <script>
		var u = navigator.userAgent, app = navigator.appVersion;

		var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1; //android终端或者uc浏览器
		var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端




		var time_lock = 0;
		var save_lock = 0;
		var record_file = '<?php echo $record_file;?>';
		var time = 0;
		var auth_coin_open =  window.parent.parent.auth_coin_open;
		var user_id         =<?php echo (int)$sess_uid;?>;
        var book_sid        ='<?php echo trim($book_sid);?>';
		var record_path = '<? echo $record_path; ?>';
		var has_record = '<? if($has_record){echo 1;}else{echo 0;} ?>';
		var ftp_root ="<? echo $ftp_root; ?>"+user_id+"/book/"+book_sid+"/record/";
		window.document.getElementById("auth_coin_open").value = window.parent.parent.auth_coin_open;
		var oForm1          =document.getElementById("Form1");      //表單
		var ofile           =document.getElementById('file');       //檔案,按鈕
		var oBtnU           =document.getElementById("BtnU");       //上傳,按鈕
		oBtnU.onclick=function(){
				//上傳

					var arry_type=[
						'mp3',
						'wav'
					]

					var file=trim(ofile.value);
					var info=pathinfo(file);

					var filename =info['filename'];
					var extension=info['extension'];

					if(trim(file)==''){
						window.alert('請選擇欲上傳的圖片!',1);
						return false;
					}

					if(!in_array(extension.toLowerCase(),arry_type,false)){
						window.alert('請選擇MP3、WAV檔案!',1);
						return false;
					}

					go_uploadA();

				}


		function up_load()
		{
			//window.parent.parent.main();
			main();
		}


		function go_recode()
		{
			window.document.getElementById("up_load").style.display = 'block';
			//window.open('https://'+location.hostname+'/mssr/service/bookstore_v2/page_rec_edit/rec_new_record/index.php','_blank','width=560,height=397');
			post('https://'+location.hostname+'/mssr/service/bookstore_v2/page_rec_edit/rec_new_record/index.php', {book_sid: book_sid,auth_coin_open : auth_coin_open});
		}

		function post(path, params, method)
		{
			method = method || "post"; // Set method to post by default if not specified.

			// The rest of this code assumes you are not using a library.
			// It can be made less wordy if you use one.
			var form =window.parent.parent.document.createElement("form");
			form.setAttribute("method", method);
			form.setAttribute("action", path);
			form.setAttribute("target", "formresult");

			for(var key in params) {
				if(params.hasOwnProperty(key)) {
					var hiddenField = window.parent.parent.document.createElement("input");
					hiddenField.setAttribute("type", "hidden");
					hiddenField.setAttribute("name", key);
					hiddenField.setAttribute("value", params[key]);

					form.appendChild(hiddenField);
				 }
			}

			window.parent.parent.document.body.appendChild(form);
			window.open(path,'formresult',"method=post,height=400, width=400, top=0, left=0, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=yes, status=yes");
			form.submit();
		}


		function go_uploadA()
		{
			oForm1.action="uploadA.php";
			oForm1.submit();
		}
	//---------------------------------------------------
    //函式
    //---------------------------------------------------
		//LOG
		window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e13',window.parent.parent.action_on);

		//老師評論

        <?php foreach ($array as $key => $value) {?>
       
        	<? if($array[$key]['rec_record_score'] != ""){?>

        		 window.parent.set_content('<? echo $array[$key]['rec_record_content']; ?>','<? echo $array[$key]['rec_record_score']; ?>','<? echo $array[$key]['keyin_cdate']; ?>');
        	 <? }?>

        <?php } ?>

                    //  for(var key in data_array){
                    //     if(typeof(data_array[key]) == 'object' ){
                        
                    //          window.parent.set_content(data_array[key]["rec_draw_content"],data_array[key]["rec_draw_score"],data_array[key]["keyin_cdate"],key);
                           

                    //     }

                    // }


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






		function main()
		{
			//echo("Main:初始開始:讀取朗讀資料>"+book_sid+"_"+user_id);
			cover("讀取朗讀資料");

			var url = "./ajax/get_rec_recode.php";
			$.post(url, {
					user_id:user_id,
					book_sid:book_sid,

					user_permission:'<? echo $permission;?>'
			}).success(function (data)
			{ console.log(data);
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
					//ftp_root
					//player_con
					//window.alert(data_array["recode_name"]);
                    //console.log(ftp_root+data_array["recode_name"]+"?time="+Math.random());
                    //console.log(data_array);
					if(data_array["recode_name"])
					{
						var audio = new Audio();
						audio.src = ftp_root+data_array["recode_name"]+"?time="+Math.random();
						audio.controls = true;
						var tttmp= document.querySelector('#player_con');
						tttmp.innerHTML = "";
						tttmp.appendChild(document.createElement('hr'));
						tttmp.appendChild(audio);

						//if(audio.paused) audio.play();

						audio.onended = function() {
							audio.pause();
							audio.src = audio.src;
						};
					}

					cover("");
				}

			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){
				echo("AJAX:complete:main():讀取朗讀資料:");
			});
		}

		main();



        </script>

    <!-- commits.js is useless for you! -->


</body>

</html>

