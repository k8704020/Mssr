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
		if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");
    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
        //GET
		$book_sid    =(isset($_GET['book_sid']))?mysql_prep($_GET['book_sid']):0;
        $book_name         =(isset($_GET['book_name']))?mysql_prep($_GET['book_name']):"？";

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
		$array['rec_record_content'] = "";
		$array['rec_record_score'] = "";
		$sql = "SELECT rec_state
			FROM  `mssr_rec_book_record_log`
			WHERE  `mssr_rec_book_record_log`.`user_id` =  '".$sess_uid."'
			AND  `mssr_rec_book_record_log`.`book_sid` =  '".$book_sid."'
			ORDER BY  `mssr_rec_book_record_log`.`keyin_cdate` DESC ";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
		foreach($retrun as $key1=>$val1)
		{
		  
			  //搜尋有無評分
			  $sql_tmp = "SELECT comment_content,comment_score
						FROM  mssr_rec_comment_log
						WHERE mssr_rec_comment_log.book_sid = '".$book_sid."'
						AND mssr_rec_comment_log.comment_to = '".$sess_uid."'
						AND comment_type='record'
						ORDER BY  `mssr_rec_comment_log`.`keyin_cdate` DESC ";
			  $retrun_tmp = db_result($conn_type='pdo',$conn_mssr,$sql_tmp,$arry_limit=array(0,1),$arry_conn_mssr);	
			  if(count($retrun_tmp)>0)
			  {
				  $array['rec_record_content'] = mysql_prep($retrun_tmp[0]['comment_content']);
				  $array['rec_record_score'] = mysql_prep($retrun_tmp[0]['comment_score']);
				  
			  }
		  
		}
			
			
?>
<html lang="en">

<head>
    <Title><?php echo $title;?></Title>
        <link rel="stylesheet" type="text/css" href="../rec_draw/page_up_image/css/code.css" media="all" />

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
	<img style="position:absolute; top:5px; left:146px;" src="img/tittle_2.png" />
    <img src="./img/recode_2.png" style="position:absolute; top:53px; left:124px;">
    <img src="./img/recode_3.png" style="position:absolute; top:59px; left:523px;">
    <img src="./img/recode_4.png" style="position:absolute; top:239px; left:526px;">
    <div id='block' style="position:absolute; display:none; top:0px; left:0px; width:1000px; height:480px; background:#222222; opacity:0.8"></div>
    <div width="320px" height='130px' style='position: absolute; top:302px; left:532px; width: 330px; height: 135px;'>
        
    <section class="experiment recordrtc">
            <h2 class="header">
              <div id="record_time" style="float:left; position:absolute; top:0px; left:110px;">錄音時間　00/50</div>
              <button id="sopbtn" style="position:absolute; top:0px; left:0px;width:100px;">&nbsp;● &nbsp;錄音</button>
	  </h2>
            
			<h2 class="header">
              <div id="videoaa" style="text-align: left; display:none; "> 
                  <button id="upload-to-server" style=" position:absolute; top:40px; left:0px; width:100px;">⇧ 上傳</button>
                    <button id="save-to-disk" style=" display:none; width:100px;">⇩下載</button>
                </div>
			</h2>	
          
			<div id="videobb"  style=" position:absolute;display:none; top:40px; left:110px;">試聽:
            	<video controls muted style="display:none;" ></video>
            </div>
      </section>
    </div>
    <span id="up_data" class='btn btn-success' onClick="window.document.getElementById('updata_page').style.display='block';window.parent.set_top_btn('none');" style="position:absolute; top:250px; left:873px;">上傳</span>
    <span id="donw_data" class='btn btn-success' onClick="cover('開始下載檔案',1)" style="position:absolute; top:250px; left:927px;">下載</span>

    <!-- 音樂 -->
    <img src="img/BAR4.png" style="position:absolute; top:387px; left:535px; width:327px; height:52px;">
    <h2 style="position:absolute; top:387px; left:535px; width:327px; height:52px;">尚未錄音</h2>
    <h2 class="header">
        <div id="player" style="position:absolute; top:384px; left:532px; height:58px; width:334px; background-color:#f5e4bb; overflow:hidden;display:none;">
        </div>
</h2>

<? if(($_SESSION['class'][0][1] == 'gcp_2013_2_3_9' || $_SESSION['class'][0][1] == 'gcp_2013_2_3_4' || $_SESSION['class'][0][1] == 'gcp_2013_2_3_1' ))//聊書判斷入口
		{?>
<div  style="position:absolute; top:187px; left:873px; color:#FFFFFF; width:120px; font-size:24px">
    前往聊書
    <BR>
    
    
    
    <!--<img onClick="go_to_take()" src="../img/to_toke.png" style="cursor: pointer;"> -->
</div><? }?>
    
<? echo $array['rec_record_score'];?>
<!-- 上傳頁面  -->
    <div id = "updata_page" style=" position:absolute; display:none; top:0px; left:0px;">
      <div style="position:absolute; display:; top:0px; left:0px; width:1000px; height:480px; background:#111; opacity:0.8"></div>
      <div style="background-color:#f6e5bc; position:absolute; border-radius:10px; top:108px; left:267px; width:454px; height:208px;"></div>
      <div class="cord" style="background-color:#fff; padding-top:30px; padding-left:40px; border-radius:5px; position:absolute; top:119px; left:284px; width:386px; height:108px; font-size:20px; line-height: 24px;"><BR>1.先點選[選擇檔案]<BR>2.選擇MP3或MAV的音樂檔案<BR>3.點選[上傳]將檔案上傳伺服器</div>
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
		<? if($array['rec_record_score'] != ""){?>
		window.parent.set_content('<? echo $array['rec_record_content']; ?>','<? echo $array['rec_record_score']; ?>');
		<? }?>
		
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
		//設置錄音時間
		function record_time(value)
		{
			window.document.getElementById("record_time").innerHTML = "錄音時間　";
			var tmp = parseInt(value);

			if(tmp>49)stopRecording();//最長1分鐘
			
			if(tmp<10)tmp = "0"+tmp;
			window.document.getElementById("record_time").innerHTML += tmp;
			window.document.getElementById("record_time").innerHTML += "/50";
		}
		//計時器裝置=================
		
		//事件 :  操作時變更的設定
		function set_rec_on_edit(value)
		{
			time_lock = value;
			//window.parent.save_lock = 1;
			//window.document.getElementById("save_btn").style.display = "block";
		}

		//事件 :  設置計時器
		function timedCount()
		{
			if(time_lock)time++;
			if(time_lock)record_time(time);
			rec_edit_setTimeout=setTimeout("timedCount()",1000);
		}
		timedCount();
		//時間滿
		function stopRecording()
		{
			time_lock = 0;
			
			document.getElementById("sopbtn").click();
		}
		
		
            (function() {
                var params = {},
                    r = /([^&=]+)=?([^&]*)/g;

                function d(s) {
                    return decodeURIComponent(s.replace(/\+/g, ' '));
                }

                var match, search = window.location.search;
                while (match = r.exec(search.substring(1))) {
                    params[d(match[1])] = d(match[2]);

                    if(d(match[2]) === 'true' || d(match[2]) === 'false') {
                        params[d(match[1])] = d(match[2]) === 'true' ? true : false;
                    }
                }

                window.params = params;
            })();
        </script>

        <script>
            var recordingDIV = document.querySelector('.recordrtc');
            
			var recording_Media = 'record-audio';
            var recordingPlayer = recordingDIV.querySelector('video');
			var mediaContainer_Format = 'WAV';

            recordingDIV.querySelector('button').onclick = function() {
                var button = this;

                if(button.innerHTML === '█ 停止') {
                    button.disabled = true;
					
                    button.disableStateWaiting = true;
                    setTimeout(function() {
                        button.disabled = false;
                        button.disableStateWaiting = false;
                    }, 2 * 1000);

                    button.innerHTML = '● 錄音';
					set_rec_on_edit(0);
					
                    function stopStream() {
                        if(button.stream && button.stream.stop) {
                            button.stream.stop();
                            button.stream = null;
                        }
                    }

                    if(button.recordRTC) {
                        if(button.recordRTC.length) {
                            button.recordRTC[0].stopRecording(function(url) {
                                if(!button.recordRTC[1]) {
                                    button.recordingEndedCallback(url);
                                    stopStream();

                                    saveToDiskOrOpenNewTab(button.recordRTC[0]);
                                    return;
                                }

                                button.recordRTC[1].stopRecording(function(url) {
                                    button.recordingEndedCallback(url);
                                    stopStream();
                                });
                            });
                        }
                        else {
                            button.recordRTC.stopRecording(function(url) {
                                button.recordingEndedCallback(url);
                                stopStream();

                                saveToDiskOrOpenNewTab(button.recordRTC);
                            });
                        }
                    }

                    return;
                }

                button.disabled = true;

                var commonConfig = {
                    onMediaCaptured: function(stream) {
                        button.stream = stream;
                        if(button.mediaCapturedCallback) {
                            button.mediaCapturedCallback();
                        }

                        button.innerHTML = '█ 停止';
						time =0;
						set_rec_on_edit(1);
						recordingDIV.querySelector('#save-to-disk').parentNode.style.display = 'none';
						window.document.getElementById("videoaa").style.display = "none";
						window.document.getElementById("videobb").style.display = "none";
                        button.disabled = false;
                    },
                    onMediaStopped: function() {
                        button.innerHTML = '● 錄音';
						set_rec_on_edit(0);
						window.document.getElementById("videoaa").style.display = "block";
						if(!isAndroid)window.document.getElementById("videobb").style.display = "block";
                        if(!button.disableStateWaiting) {
                            button.disabled = false;
                        }
                    },
                    onMediaCapturingFailed: function(error) {
                        if(error.name === 'PermissionDeniedError' && !!navigator.mozGetUserMedia) {
                            InstallTrigger.install({
                                'Foo': {
                                    URL: 'https://addons.mozilla.org/en-US/firefox/addon/enable-screen-capturing/',
                                    toString: function () {
                                        return this.URL;
                                    }
                                }
                            });
                        }

                        commonConfig.onMediaStopped();
                    }
                };

             
                if(recording_Media === 'record-audio') {
                    captureAudio(commonConfig);

                    button.mediaCapturedCallback = function() {
                        button.recordRTC = RecordRTC(button.stream, {
                            type: 'audio',
                            bufferSize: typeof params.bufferSize == 'undefined' ? 0 : parseInt(params.bufferSize),
                            sampleRate: typeof params.sampleRate == 'undefined' ? 44100 : parseInt(params.sampleRate),
                            leftChannel: params.leftChannel || false,
                            disableLogs: params.disableLogs || false,
                            recorderType: webrtcDetectedBrowser === 'edge' ? StereoAudioRecorder : null
                        });

                        button.recordingEndedCallback = function(url) {
                            var audio = new Audio();
                            audio.src = url;
                            audio.controls = true;
							
							recordingPlayer.parentNode.innerHTML ='試聽:<video controls muted style="display:none;" ></video>';
                            recordingPlayer=recordingDIV.querySelector('video');
                            recordingPlayer.parentNode.appendChild(audio);

                            //if(audio.paused) audio.play();

                            audio.onended = function() {
                                audio.pause();
                                audio.src = URL.createObjectURL(button.recordRTC.blob);
                            };
                        };

                        button.recordRTC.startRecording();
                    };
                }

            };

            function captureVideo(config) {
                captureUserMedia({video: true}, function(videoStream) {
                    recordingPlayer.srcObject = videoStream;
                    recordingPlayer.play();

                    config.onMediaCaptured(videoStream);

                    videoStream.onended = function() {
                        config.onMediaStopped();
                    };
                }, function(error) {
                    config.onMediaCapturingFailed(error);
                });
            }

            function captureAudio(config) {
                captureUserMedia({audio: true}, function(audioStream) {
                    recordingPlayer.srcObject = audioStream;
                    recordingPlayer.play();

                    config.onMediaCaptured(audioStream);

                    audioStream.onended = function() {
                        config.onMediaStopped();
                    };
                }, function(error) {
                    config.onMediaCapturingFailed(error);
                });
            }

            function captureAudioPlusVideo(config) {
                captureUserMedia({video: true, audio: true}, function(audioVideoStream) {
                    recordingPlayer.srcObject = audioVideoStream;
                    recordingPlayer.play();

                    config.onMediaCaptured(audioVideoStream);

                    audioVideoStream.onended = function() {
                        config.onMediaStopped();
                    };
                }, function(error) {
                    config.onMediaCapturingFailed(error);
                });
            }

            function captureScreen(config) {
                getScreenId(function(error, sourceId, screenConstraints) {
                    if (error === 'not-installed') {
                        document.write('<h1><a target="_blank" href="https://chrome.google.com/webstore/detail/screen-capturing/ajhifddimkapgcifgcodmmfdlknahffk">Please install this chrome extension then reload the page.</a></h1>');
                    }

                    if (error === 'permission-denied') {
                        alert('Screen capturing permission is denied.');
                    }

                    if (error === 'installed-disabled') {
                        alert('Please enable chrome screen capturing extension.');
                    }

                    if(error) {
                        config.onMediaCapturingFailed(error);
                        return;
                    }

                    captureUserMedia(screenConstraints, function(screenStream) {
                        recordingPlayer.srcObject = screenStream;
                        recordingPlayer.play();

                        config.onMediaCaptured(screenStream);

                        screenStream.onended = function() {
                            config.onMediaStopped();
                        };
                    }, function(error) {
                        config.onMediaCapturingFailed(error);
                    });
                });
            }

            function captureAudioPlusScreen(config) {
                getScreenId(function(error, sourceId, screenConstraints) {
                    if (error === 'not-installed') {
                        document.write('<h1><a target="_blank" href="https://chrome.google.com/webstore/detail/screen-capturing/ajhifddimkapgcifgcodmmfdlknahffk">Please install this chrome extension then reload the page.</a></h1>');
                    }

                    if (error === 'permission-denied') {
                        alert('Screen capturing permission is denied.');
                    }

                    if (error === 'installed-disabled') {
                        alert('Please enable chrome screen capturing extension.');
                    }

                    if(error) {
                        config.onMediaCapturingFailed(error);
                        return;
                    }

                    screenConstraints.audio = true;

                    captureUserMedia(screenConstraints, function(screenStream) {
                        recordingPlayer.srcObject = screenStream;
                        recordingPlayer.play();

                        config.onMediaCaptured(screenStream);

                        screenStream.onended = function() {
                            config.onMediaStopped();
                        };
                    }, function(error) {
                        config.onMediaCapturingFailed(error);
                    });
                });
            }

            function captureUserMedia(mediaConstraints, successCallback, errorCallback) {
                navigator.mediaDevices.getUserMedia(mediaConstraints).then(successCallback).catch(errorCallback);
            }

          

            if(webrtcDetectedBrowser === 'edge') {
               
            }

            if(webrtcDetectedBrowser === 'firefox') {
              
            }

            // disabling this option because currently this demo
            // doesn't supports publishing two blobs.
            // todo: add support of uploading both WAV/WebM to server.
            if(false && webrtcDetectedBrowser === 'chrome') {
              }

            function saveToDiskOrOpenNewTab(recordRTC) {
                recordingDIV.querySelector('#save-to-disk').parentNode.style.display = 'block';
                recordingDIV.querySelector('#save-to-disk').onclick = function() {
                    if(!recordRTC) return alert('No recording found.');

                    recordRTC.save();
                };

              /*  recordingDIV.querySelector('#open-new-tab').onclick = function() {
                    if(!recordRTC) return alert('No recording found.');

                    window.open(recordRTC.toURL());
                };*/

                recordingDIV.querySelector('#upload-to-server').disabled = false;
                recordingDIV.querySelector('#upload-to-server').onclick = function() {
					if(save_lock!=0)false;
					save_lock = 1;
                    if(!recordRTC) return alert('No recording found.');
                    this.disabled = true;

                    var button = this;
                    uploadToServer(recordRTC, function(progress, fileURL) {
                        if(progress === 'ended') {
                            button.disabled = false;
                           
							/*button.innerHTML = '上傳完成';
                            button.onclick = function() {
                                window.open(fileURL);
                            };*/
                            return;
                        }
                        button.innerHTML = progress;
                    });
                };
            }

            var listOfFilesUploaded = [];

            function uploadToServer(recordRTC, callback) {
                var blob = recordRTC instanceof Blob ? recordRTC : recordRTC.blob;
                var fileType = blob.type.split('/')[0] || 'audio';
                var fileName = '1';

                if (fileType === 'audio') {
                    fileName += '.' + (!!navigator.mozGetUserMedia ? 'ogg' : 'wav');
                } else {
                    fileName += '.webm';
                }

                // create FormData
                var formData = new FormData();
                formData.append(fileType + '-filename', fileName);
                formData.append(fileType + '-blob', blob);
     			formData.append('user_id', user_id);
                formData.append('book_sid', book_sid);
				formData.append('time', time);
				formData.append('auth_coin_open', auth_coin_open);
				formData.append('filename', fileName);
				//callback('Uploading ' + fileType + ' recording to server.');
				
                makeXMLHttpRequest('save.php', formData, function(progress) {
                    if (progress !== 'upload-ended') {
                        callback(progress);
                        return;
                    }

                    var initialURL = location.href.replace(location.href.split('/').pop(), '') + 'uploads/';

                    callback('ended', initialURL + fileName);

                    // to make sure we can delete as soon as visitor leaves
                    listOfFilesUploaded.push(initialURL + fileName);
                });
            }

            function makeXMLHttpRequest(url, data, callback) {
                var request = new XMLHttpRequest();
				
				callback('upload-ended');
                request.onreadystatechange = function() {
                    if (request.readyState == 4 && request.status == 200) {
						data = request.responseText;
						if(data[0]!="{")
						{
							cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",2,function(){makeXMLHttpRequest(url, data, callback);});
							echo("AJAX:success:main():讀取文字資料:資料庫發生問題");
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
							
							
							save_lock = 0;
							cover("存檔完畢",1);
							window.document.getElementById("videoaa").style.display = "none";
							window.document.getElementById("videobb").style.display = "none";
							set_player("wav");
							if(data_array["coin"]>0)window.parent.parent.set_coin(data_array["coin"]);
							if(data_array["text"]!="")cover(data_array["text"],1);
							
						}
						
                    }
                };

                request.upload.onloadstart = function() {
                    cover('開始上傳');
                };

                request.upload.onprogress = function(event) {
                   cover("上傳中...  "+ Math.round(event.loaded / event.total * 100) +"%");
                };

                request.upload.onload = function() {
                    callback('progress-about-to-end');
                };

                request.upload.onload = function() {
                     
                };

                request.upload.onerror = function(error) {
                    cover('上傳失敗,請再次上傳',1);
                    console.error('XMLHttpRequest failed', error);
                };

                request.upload.onabort = function(error) {
                    callback('Upload aborted.');
                    console.error('XMLHttpRequest aborted', error);
                };
				
                request.open('POST', url);
                request.send(data);
            }

            window.onbeforeunload = function() {
                /*recordingDIV.querySelector('button').disabled = false;
               // recordingMedia.disabled = false;
               // mediaContainerFormat.disabled = false;

                if(!listOfFilesUploaded.length) return;

                listOfFilesUploaded.forEach(function(fileURL) {
                    var request = new XMLHttpRequest();
                    request.onreadystatechange = function() {
                        if (request.readyState == 4 && request.status == 200) {
                            if(this.responseText === ' problem deleting files.') {
                                alert('Failed to delete ' + fileURL + ' from the server.');
                                return;
                            }

                            listOfFilesUploaded = [];
                            alert('You can leave now. Your files are removed from the server.');
                        }
                    };
                    request.open('POST', 'delete.php');

                    var formData = new FormData();
                    formData.append('delete-file', fileURL.split('/').pop());
                    request.send(formData);
                });

                return 'Please wait few seconds before your recordings are deleted from the server.';*/
            };
		//設(預設)置播放裝置
		function set_default_player()
		{
			
			var record_path = "./default/1.wav";
			var audio = new Audio();
			audio.src = record_path;
			audio.controls = true;
			
			window.document.getElementById("player").innerHTML ='試聽:<video controls muted style="display:none;" ></video>';
			window.document.getElementById("player").appendChild(audio);


			audio.onended = function() {
				audio.pause();
			};
			
			
		}
		function set_player(file)
		{
			
			var record_path = "../../../../info/user/"+user_id+"/book/"+book_sid+"/record/1."+file;
			var audio = new Audio();
			audio.src = record_path;
			audio.controls = true;
			window.document.getElementById("player").style.display= "block";
			window.document.getElementById("player").innerHTML ='已完成錄音! <video controls muted style="display:none;" ></video>';
			//recordingPlayer=recordingDIV.querySelector('video');
			window.document.getElementById("player").appendChild(audio);

			//if(audio.paused) audio.play();

			audio.onended = function() {
				//window.document.getElementById("player").style.display= "block";
				audio.pause();
				//audio.src = URL.createObjectURL(button.recordRTC.blob);
			};
		}
		if(has_record == 1)set_player(record_file);
        else set_default_player();
        </script>
 
    <!-- commits.js is useless for you! -->
    <script>
        window.useThisGithubPath = 'muaz-khan/RecordRTC';
		cover("");
    </script> 
    <script src="https://cdn.webrtc-experiment.com/commits.js" async></script>
</body>

</html>

