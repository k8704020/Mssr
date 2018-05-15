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
        //POSTi

		$book_sid = (isset($_POST['book_sid']))?mysql_prep($_POST['book_sid']):0;
		$auth_coin_open    =(isset($_POST['auth_coin_open']))?$_POST['auth_coin_open']:0;
        $ftp_root="http://".$arry_ftp1_info['host']."/mssr/info/user/";
		//if($book_sid == 0)die("錯誤--請重新載入");
		//echo "book_sid:".$book_sid;
		//echo "<BR>";
		//echo "auth_coin_open:".$auth_coin_open;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,總店";




?>
<!--
> Muaz Khan     - www.MuazKhan.com
> MIT License   - www.WebRTC-Experiment.com/licence
> Documentation - github.com/muaz-khan/RecordRTC
> and           - RecordRTC.org
-->
<!DOCTYPE html>
<html lang="en">

<head>
    <title>RecordRTC to PHP ® Muaz Khan</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link rel="author" type="text/html" href="https://plus.google.com/+MuazKhan">
    <meta name="author" content="Muaz Khan">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <link rel="stylesheet" href="css/style.css">

    <style>
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
    </style>
    <script type="text/javascript" src="../../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
    <script src="js/RecordRTC.js"></script>
    <script src="js/gif-recorder.js"></script>
    <script src="js/getScreenId.js"></script>

    <!-- for Edige/FF/Chrome/Opera/etc. getUserMedia support -->
    <script src="js/gumadapter.js"></script>
</head>

<body>





        <section class="experiment recordrtc" style=" max-width:400px;">
            <h2 class="header">
            	<button id="sopbtn"><?php if(trim($language)==='zh-cn'){?>点我录音<? }else{ ?>點我錄音<? }?><a style="color:#FF0000">●</a></button><BR>
                <select class="recording-media" style="display:none;" >
                    <option value="record-video">Video</option>
                    <option value="record-audio">Audio</option>
                    <option value="record-screen">Screen</option>
                </select>
                <select class="media-container-format" style=" display:none;">
                    <option>WebM</option>
                    <option disabled>Mp4</option>
                    <option disabled>WAV</option>
                    <option disabled>Ogg</option>
                    <option>Gif</option>
                </select>


            </h2>
        	<div id="record_time" style="text-align: center;">
                <?php if(trim($language)==='zh-cn'){?>最长录音时间:00/60(秒)<? }else{ ?>最長錄音時間:00/60(秒)<? }?>
            </div>
			<div style="text-align: center; display: none; width:300px;">
                <button id="save-to-disk" style=" display:none;"><?php if(trim($language)==='zh-cn'){?>下载<? }else{ ?>下載<? }?>▼</button>
                <button id="open-new-tab" style=" display:none;">Open New Tab</button>
                <button id="upload-to-server"><?php if(trim($language)==='zh-cn'){?>上传<? }else{ ?>上傳新的錄音檔<? }?>▲</button>
                <button id="out" onClick="out()" style="display:none; float:right;"><?php if(trim($language)==='zh-cn'){?>离开<? }else{ ?>離開<? }?></button>
                <BR>

            </div>
            <div id="upbar" style="display: none; position:relative;background-color:#CCC;height:18px;  text-align: center; border-radius:10px; box-shadow:-1px -1px 1px #333">
            	<div id="upbar_c" style=" position:relative; top:1px; width:10%; height:16px; background-color:#0B0; color:#FFFFFF;border-radius:10px; text-shadow: 1px 1px 1px #000;box-shadow:1px 1px 1px #FFF"></div>
                <div id="upbar_t" style=" position:absolute; top:0px;color:#FFFFFF; text-shadow: 1px 1px 1px #000; font-size:10px"><?php if(trim($language)==='zh-cn'){?><? }else{ ?><? }?>上傳進度 : 60%</div>
            </div>
            <video controls muted style="display:none;"></video>
            <div id="player_con" style="">尚未錄音</div>
            <div id="videovideo"></div>
        </section>

        <script>
			//初始化
			var user_id = "<? echo $sess_uid;?>";
			var book_sid = "<? echo $book_sid;?>";
			var auth_coin_open = "<? echo $auth_coin_open;?>";
			var time = 0;
			var time_lock = false;

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
            var recordingMedia = recordingDIV.querySelector('.recording-media');
            var recordingPlayer = recordingDIV.querySelector('video');
            var mediaContainerFormat = recordingDIV.querySelector('.media-container-format');
            var ftp_root ="<? echo $ftp_root; ?>"+user_id+"/book/"+book_sid+"/record/";

		    //cover
            function cover(text,type,proc)
            {

                window.parent.cover(text,type);
                if(type == 2)
                {
                    delayExecute(proc);
                }
            }

            recordingDIV.querySelector('button').onclick = function() {
                var button = this;

                if(button.innerHTML === '<?php if(trim($language)==='zh-cn'){?>结束录音<? }else{ ?>結束錄音<? }?><a style="color:#FF0000">■</a>') {
					close_timer();
                    button.disabled = true;
                    button.disableStateWaiting = true;
                    setTimeout(function() {
                        button.disabled = false;
                        button.disableStateWaiting = false;
                    }, 2 * 1000);

                    button.innerHTML = '<?php if(trim($language)==='zh-cn'){?>点我录音<? }else{ ?>點我錄音<? }?><a style="color:#FF0000">●</a>';

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
						//開始計時
						open_timer();
						var uploadserver = document.querySelector('#upload-to-server');
               			uploadserver.innerHTML = '<?php if(trim($language)==='zh-cn'){?>上传<? }else{ ?>上傳新的錄音檔<? }?>▲';
						uploadserver.disabled = true;
						var savedisk = document.querySelector('#save-to-disk');
						savedisk.disabled = true;
                        button.innerHTML = '<?php if(trim($language)==='zh-cn'){?>结束录音<? }else{ ?>結束錄音<? }?><a style="color:#FF0000">■</a>';
                        button.disabled = false;
                    },
                    onMediaStopped: function() {
                        button.innerHTML = '<?php if(trim($language)==='zh-cn'){?>点我录音<? }else{ ?>點我錄音<? }?><a style="color:#FF0000">●</a>';
                        var uploadserver = document.querySelector('#upload-to-server');
               			uploadserver.innerHTML = '<?php if(trim($language)==='zh-cn'){?>上传<? }else{ ?>上傳新的錄音檔<? }?>▲';
						uploadserver.disabled = false;
						var savedisk = document.querySelector('#save-to-disk');
						savedisk.disabled = false;
                        if(!button.disableStateWaiting) {
                            button.disabled = false;
                        }
                    },
                    onMediaCapturingFailed: function(error) {
                        if(error.name === 'PermissionDeniedError' && !!navigator.mozGetUserMedia) {
                            InstallTrigger.install({
                                'Foo': {
                                    // https://addons.mozilla.org/firefox/downloads/latest/655146/addon-655146-latest.xpi?src=dp-btn-primary
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

                /*if(recordingMedia.value === 'record-video') {
					console.log('record-video');
                    captureVideo(commonConfig);

                    button.mediaCapturedCallback = function() {
                        button.recordRTC = RecordRTC(button.stream, {
                            type: mediaContainerFormat.value === 'Gif' ? 'gif' : 'video',
                            disableLogs: params.disableLogs || false,
                            canvas: {
                                width: params.canvas_width || 320,
                                height: params.canvas_height || 240
                            },
                            frameInterval: typeof params.frameInterval !== 'undefined' ? parseInt(params.frameInterval) : 20 // minimum time between pushing frames to Whammy (in milliseconds)
                        });

                        button.recordingEndedCallback = function(url) {
                            recordingPlayer.src = null;
                            recordingPlayer.srcObject = null;

                            if(mediaContainerFormat.value === 'Gif') {
                                recordingPlayer.pause();
                                recordingPlayer.poster = url;

                                recordingPlayer.onended = function() {
                                    recordingPlayer.pause();
                                    recordingPlayer.poster = URL.createObjectURL(button.recordRTC.blob);
                                };
                                return;
                            }

                            recordingPlayer.src = url;
                            recordingPlayer.play();

                            recordingPlayer.onended = function() {
                                recordingPlayer.pause();
                                recordingPlayer.src = URL.createObjectURL(button.recordRTC.blob);
                            };
                        };

                        button.recordRTC.startRecording();
                    };
                }*/

                if(/*recordingMedia.value === 'record-audio'*/1) {
					console.log('record-audio');
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


						//建立新的音喔喔喔喔喔喔喔喔喔-----654654232/*
                        button.recordingEndedCallback = function(url) {
                            var audio = new Audio();
                            audio.src = url;
							audio.name = "videovideo";
                            audio.controls = true;
							var tttmp= document.querySelector('#videovideo');
							tttmp.innerHTML = "";
                            tttmp.appendChild(document.createElement('hr'));
                            tttmp.innerHTML+="新的錄音檔：";
                            tttmp.appendChild(audio);

                            //if(audio.paused) audio.play();

                            audio.onended = function() {
                                audio.pause();
                                audio.src = URL.createObjectURL(button.recordRTC.blob);
                            };
                        };

                        button.recordRTC.startRecording();
                    };
                }

               /* if(recordingMedia.value === 'record-audio-plus-video') {
					console.log('record-audio-plus-video');
                    captureAudioPlusVideo(commonConfig);

                    button.mediaCapturedCallback = function() {

                        if(webrtcDetectedBrowser !== 'firefox') { // opera or chrome etc.
                            button.recordRTC = [];

                            if(!params.bufferSize) {
                                // it fixes audio issues whilst recording 720p
                                params.bufferSize = 16384;
                            }

                            var audioRecorder = RecordRTC(button.stream, {
                                type: 'audio',
                                bufferSize: typeof params.bufferSize == 'undefined' ? 0 : parseInt(params.bufferSize),
                                sampleRate: typeof params.sampleRate == 'undefined' ? 44100 : parseInt(params.sampleRate),
                                leftChannel: params.leftChannel || false,
                                disableLogs: params.disableLogs || false,
                                recorderType: webrtcDetectedBrowser === 'edge' ? StereoAudioRecorder : null
                            });

                            var videoRecorder = RecordRTC(button.stream, {
                                type: 'video',
                                disableLogs: params.disableLogs || false,
                                canvas: {
                                    width: params.canvas_width || 320,
                                    height: params.canvas_height || 240
                                },
                                frameInterval: typeof params.frameInterval !== 'undefined' ? parseInt(params.frameInterval) : 20 // minimum time between pushing frames to Whammy (in milliseconds)
                            });

                            // to sync audio/video playbacks in browser!
                            videoRecorder.initRecorder(function() {
                                audioRecorder.initRecorder(function() {
                                    audioRecorder.startRecording();
                                    videoRecorder.startRecording();
                                });
                            });

                            button.recordRTC.push(audioRecorder, videoRecorder);

                            button.recordingEndedCallback = function() {
                                var audio = new Audio();
                                audio.src = audioRecorder.toURL();
                                audio.controls = true;
                                audio.autoplay = true;

                                audio.onloadedmetadata = function() {
                                    recordingPlayer.src = videoRecorder.toURL();
                                    recordingPlayer.play();
                                };

                                recordingPlayer.parentNode.appendChild(document.createElement('hr'));
                                recordingPlayer.parentNode.appendChild(audio);

                                if(audio.paused) audio.play();
                            };
                            return;
                        }

                        button.recordRTC = RecordRTC(button.stream, {
                            type: 'video',
                            disableLogs: params.disableLogs || false,
                            // we can't pass bitrates or framerates here
                            // Firefox MediaRecorder API lakes these features
                        });

                        button.recordingEndedCallback = function(url) {
                            recordingPlayer.srcObject = null;
                            recordingPlayer.muted = false;
                            recordingPlayer.src = url;
                            recordingPlayer.play();

                            recordingPlayer.onended = function() {
                                recordingPlayer.pause();
                                recordingPlayer.src = URL.createObjectURL(button.recordRTC.blob);
                            };
                        };

                        button.recordRTC.startRecording();
                    };
                }*/
                /*
                if(recordingMedia.value === 'record-screen') {
					console.log('record-screen');
                    captureScreen(commonConfig);

                    button.mediaCapturedCallback = function() {
                        button.recordRTC = RecordRTC(button.stream, {
                            type: mediaContainerFormat.value === 'Gif' ? 'gif' : 'video',
                            disableLogs: params.disableLogs || false,
                            canvas: {
                                width: params.canvas_width || 320,
                                height: params.canvas_height || 240
                            }
                        });

                        button.recordingEndedCallback = function(url) {
                            recordingPlayer.src = null;
                            recordingPlayer.srcObject = null;

                            if(mediaContainerFormat.value === 'Gif') {
                                recordingPlayer.pause();
                                recordingPlayer.poster = url;
                                recordingPlayer.onended = function() {
                                    recordingPlayer.pause();
                                    recordingPlayer.poster = URL.createObjectURL(button.recordRTC.blob);
                                };
                                return;
                            }

                            recordingPlayer.src = url;
                            recordingPlayer.play();
                        };

                        button.recordRTC.startRecording();
                    };
                }*/
				/*
                if(recordingMedia.value === 'record-audio-plus-screen') {
					console.log('record-audio-plus-screen');
                    captureAudioPlusScreen(commonConfig);

                    button.mediaCapturedCallback = function() {
                        button.recordRTC = RecordRTC(button.stream, {
                            type: 'video',
                            disableLogs: params.disableLogs || false,
                            // we can't pass bitrates or framerates here
                            // Firefox MediaRecorder API lakes these features
                        });

                        button.recordingEndedCallback = function(url) {
                            recordingPlayer.srcObject = null;
                            recordingPlayer.muted = false;
                            recordingPlayer.src = url;
                            recordingPlayer.play();

                            recordingPlayer.onended = function() {
                                recordingPlayer.pause();
                                recordingPlayer.src = URL.createObjectURL(button.recordRTC.blob);
                            };
                        };

                        button.recordRTC.startRecording();
                    };
                }*/
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

            function setMediaContainerFormat(arrayOfOptionsSupported) {
                var options = Array.prototype.slice.call(
                    mediaContainerFormat.querySelectorAll('option')
                );

                var selectedItem;
                options.forEach(function(option) {
                    option.disabled = true;

                    if(arrayOfOptionsSupported.indexOf(option.value) !== -1) {
                        option.disabled = false;

                        if(!selectedItem) {
                            option.selected = true;
                            selectedItem = option;
                        }
                    }
                });
            }
            /*
            recordingMedia.onchange = function() {
                if(this.value === 'record-audio') {
                    setMediaContainerFormat(['WAV', 'Ogg']);
                    return;
                }
                setMediaContainerFormat(['WebM', 'Gif']);
            };*/

            if(webrtcDetectedBrowser === 'edge') {
                // webp isn't supported in Microsoft Edge
                // neither MediaRecorder API
                // so lets disable both video/screen recording options
/*
                console.warn('Neither MediaRecorder API nor webp is supported in Microsoft Edge. You cam merely record audio.');

                recordingMedia.innerHTML = '<option value="record-audio">Audio</option>';
                setMediaContainerFormat(['WAV']);*/
            }

            if(webrtcDetectedBrowser === 'firefox') {
                // Firefox implemented both MediaRecorder API as well as WebAudio API
                // Their MediaRecorder implementation supports both audio/video recording in single container format
                // Remember, we can't currently pass bit-rates or frame-rates values over MediaRecorder API (their implementation lakes these features)

               /* recordingMedia.innerHTML = '<option value="record-audio-plus-video">Audio+Video</option>'
                                            + '<option value="record-audio-plus-screen">Audio+Screen</option>'
                                            + recordingMedia.innerHTML;*/
            }

            // disabling this option because currently this demo
            // doesn't supports publishing two blobs.
            // todo: add support of uploading both WAV/WebM to server.
            if(false && webrtcDetectedBrowser === 'chrome') {
             /*   recordingMedia.innerHTML = '<option value="record-audio-plus-video">Audio+Video</option>'
                                            + recordingMedia.innerHTML;
                console.info('This RecordRTC demo merely tries to playback recorded audio/video sync inside the browser. It still generates two separate files (WAV/WebM).');*/
            }

            function saveToDiskOrOpenNewTab(recordRTC) {
                recordingDIV.querySelector('#save-to-disk').parentNode.style.display = 'block';
                recordingDIV.querySelector('#save-to-disk').onclick = function() {
                    if(!recordRTC) return alert('No recording found.');

                    recordRTC.save();
                };
                /*
                recordingDIV.querySelector('#open-new-tab').onclick = function() {
                    if(!recordRTC) return alert('No recording found.');

                    window.open(recordRTC.toURL());
                };*/

                recordingDIV.querySelector('#upload-to-server').disabled = false;
                recordingDIV.querySelector('#upload-to-server').onclick = function() {
                    if(!recordRTC) return alert('No recording found.');
                    this.disabled = true;

                    var button = this;
                    uploadToServer(recordRTC, function(progress, fileURL) {
                        if(progress === 'ended') {
                            button.disabled = true;

							<?php if(trim($language)==='zh-cn'){?>
							button.innerHTML = '上传完成';//
							<?php }else{?>
							button.innerHTML = '上傳完成';//
							<?php }?>
							upbar(200);
							window.document.getElementById("out").style.display = "block";
                            /*button.onclick = function() {
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
                // create FormData POST UN
                var formData = new FormData();
                formData.append(fileType + '-filename', fileName);
                formData.append(fileType + '-blob', blob);

				formData.append('user_id', user_id);
                formData.append('book_sid', book_sid);
				formData.append('time', time);
				formData.append('auth_coin_open', auth_coin_open);
				formData.append('filename', fileName);
				formData.append('filetype', (!!navigator.mozGetUserMedia ? 'ogg' : 'wav'));
                callback('Uploading ' + fileType + ' recording to server.');

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
                request.onreadystatechange = function() {
                    if (request.readyState == 4 && request.status == 200) {
                        data = request.responseText;
						if(data[0]!="{")
						{

							<?php if(trim($language)==='zh-cn'){?>
							window.alert("资料库好像有点问题呢，请再试试看<BR>或通知系统人员",2,function(){makeXMLHttpRequest(url, data, callback);});
							<?php }else{?>
							window.alert("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",2,function(){makeXMLHttpRequest(url, data, callback);});
							<?php }?>
							return false;
						}

						data_array = JSON.parse(data);

						if(data_array["error"]!="")
						{
							window.alert(data_array["error"]);
							return false;
						}
						if(data_array["echo"]!="")
						{
							window.alert(data_array["echo"],1);

						}else
						{

							if(data_array["text"]!='')
							{
								window.document.getElementById("record_time").innerHTML += '<BR>'+data_array["text"];
							}

						}


						callback('upload-ended');
                    }
                };

                request.upload.onloadstart = function() {
                    callback('Upload started...');
                };

                request.upload.onprogress = function(event) {
					upbar(Math.round(event.loaded / event.total * 100));
                    callback('Upload Progress ' + Math.round(event.loaded / event.total * 100) + "%");

                };

                request.upload.onload = function() {
                    callback('progress-about-to-end');
                };

                request.upload.onload = function() {
                    callback('<?php if(trim($language)==='zh-cn'){?>上传<? }else{ ?>上傳新的錄音檔<? }?>▲');
                };

                request.upload.onerror = function(error) {
                    callback('Failed to upload to server');
                    console.error('XMLHttpRequest failed', error);
                };

                request.upload.onabort = function(error) {
                    callback('Upload aborted.');
                    console.error('XMLHttpRequest aborted', error);
                };

                request.open('POST', url);
                request.send(data);
            }
			/*
            window.onbeforeunload = function() {
                recordingDIV.querySelector('button').disabled = false;
                recordingMedia.disabled = false;
                mediaContainerFormat.disabled = false;

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

                return 'Please wait few seconds before your recordings are deleted from the server.';
            };*/

			//控制上傳TP
			function upbar(vale)
			{

				if(vale == -1)
				{
					window.document.getElementById("upbar").style.display = "none";
				}
				else if(vale <= 100)
				{
					var a = Math.floor(vale*0.6/10)+5;
					var b = 10-Math.floor(vale*0.6/10)+5;
					if(a==10)a="A";
					if(a==11)a="B";
					if(a==12)a="C";
					if(a==13)a="D";
					if(a==14)a="E";
					if(a==15)a="F";
					if(b==10)b="A";
					if(b==11)b="B";
					if(b==12)b="C";
					if(b==13)b="D";
					if(b==14)b="E";
					if(b==15)b="F";
					window.document.getElementById("upbar_c").style.backgroundColor = "#"+b+a+"0";
					window.document.getElementById("upbar").style.display = "block";
					window.document.getElementById("upbar_c").style.width = Math.floor(vale*0.6)+"%";
					<?php if(trim($language)==='zh-cn'){?>
					window.document.getElementById("upbar_t").innerHTML = "上传进度 : "+Math.floor(vale*0.6)+"%";
					<?php }else{?>
					window.document.getElementById("upbar_t").innerHTML = "上傳進度 : "+Math.floor(vale*0.6)+"%";
					<?php }?>


				}
				else if(vale < 200)
				{console.log(vale);
					var a = Math.floor((Math.floor((vale-100)*0.4)+60)/10)+5;
					var b = 10-Math.floor((Math.floor((vale-100)*0.4)+60)/10)+5;
					if(a==10)a="A";
					if(a==11)a="B";
					if(a==12)a="C";
					if(a==13)a="D";
					if(a==14)a="E";
					if(a==15)a="F";
					if(b==10)b="A";
					if(b==11)b="B";
					if(b==12)b="C";
					if(b==13)b="D";
					if(b==14)b="E";
					if(b==15)b="F";
					window.document.getElementById("upbar_c").style.backgroundColor = "#"+b+a+"0";
					window.document.getElementById("upbar").style.display = "block";
					window.document.getElementById("upbar_c").style.width = (Math.floor((vale-100)*0.4)+60)+"%";

					<?php if(trim($language)==='zh-cn'){?>
					window.document.getElementById("upbar_t").innerHTML = "上传进度 : "+(Math.floor((vale-100)*0.4)+60)+"%";
					<?php }else{?>
					window.document.getElementById("upbar_t").innerHTML = "上傳進度 : "+(Math.floor((vale-100)*0.4)+60)+"%";
					<?php }?>
				}
				else
				{
					var a = Math.floor((Math.floor((vale-100)*0.4)+60)/10)+5;
					var b = 10-Math.floor((Math.floor((vale-100)*0.4)+60)/10)+5;
					if(a==10)a="A";
					if(a==11)a="B";
					if(a==12)a="C";
					if(a==13)a="D";
					if(a==14)a="E";
					if(a==15)a="F";
					if(b==10)b="A";
					if(b==11)b="B";
					if(b==12)b="C";
					if(b==13)b="D";
					if(b==14)b="E";
					if(b==15)b="F";
					window.document.getElementById("upbar_c").style.backgroundColor = "#"+b+a+"0";
					window.document.getElementById("upbar").style.display = "block";
					window.document.getElementById("upbar_c").style.width = "100%";

					<?php if(trim($language)==='zh-cn'){?>
					window.document.getElementById("upbar_t").innerHTML = "上传完成";
					<?php }else{?>
					window.document.getElementById("upbar_t").innerHTML = "上傳完成";
					<?php }?>

				}

			}

			//開始計時
			function open_timer()
			{
				time = 0;
				time_lock = false ;
				record_time(time) ;
				rec_edit_setTimeout=setTimeout("timedCount()",1000);
			}

			//關閉計時
			function close_timer()
			{
				time_lock = true ;
			}

			//設置錄音時間
			function record_time(value)
			{

				<?php if(trim($language)==='zh-cn'){?>
				window.document.getElementById("record_time").innerHTML = "最长录音时间:　";
				<?php }else{?>
				window.document.getElementById("record_time").innerHTML = "最長錄音時間:　";
				<?php }?>
				var tmp = parseInt(value);
				if(tmp>59)stopRecording();//最長5分鐘
				if(tmp<10)tmp = "0"+tmp;
				window.document.getElementById("record_time").innerHTML += tmp;
				window.document.getElementById("record_time").innerHTML += "/60(秒)";

			}

			//事件 :  設置計時器
			function timedCount()
			{console.log(time_lock);
				if(time_lock) return false;

				time++;
				record_time(time);
				rec_edit_setTimeout=setTimeout("timedCount()",1000);
			}

			//時間滿
			function stopRecording()
			{
				document.getElementById("sopbtn").click();
			}

			function out()
			{
				window.close();
			}

		function main()
		{
			//echo("Main:初始開始:讀取朗讀資料>"+book_sid+"_"+user_id);
			//cover("讀取朗讀資料");
			var url = "../rec_god/ajax/get_rec_recode.php";
			$.post(url, {
					user_id:user_id,
					book_sid:book_sid,

					user_permission:'<? echo $permission;?>'
			}).success(function (data)
			{
                console.log(data);
				if(data[0]!="{")
				{
					//cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",2,function(){main();});
					//echo("AJAX:success:main():讀取使用者資料:資料庫發生問題");
					return false;
				}

				data_array = JSON.parse(data);
                console.log(data_array);
				//echo("AJAX:success:main():讀取使用者資料:已讀出:"+data);
				if(data_array["error"]!="")
				{
					//cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					//cover(data_array["echo"],1);

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
                        tttmp.innerHTML+="原有錄音檔：";
						tttmp.appendChild(audio);

						//if(audio.paused) audio.play();

						audio.onended = function() {
							audio.pause();
							audio.src = audio.src;
						};
					}

					//cover("");
				}

			}).error(function(e){
				//cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){
				//echo("AJAX:complete:main():讀取朗讀資料:");
			});
		}

        main();
        </script>

</body>

</html>

