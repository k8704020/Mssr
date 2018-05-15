<?php
//-------------------------------------------------------
//明日星球,總店
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",3).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/array/code'
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

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
//$_SESSION['uid']        =5029;
//$_GET[trim('book_sid')] ='mbg1201310091801101153473';

        //SESSION
        $sess_uid   =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:0;

        //GET
        $book_sid   =(isset($_GET[trim('book_sid')]))?$_GET[trim('book_sid')]:'';

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

        $arry_err=array();

        if($sess_uid===''){
           $arry_err[]='使用者主索引,未輸入!';
        }else{
            $sess_uid=(int)$sess_uid;
        }
        if($book_sid===''){
           $arry_err[]='書籍識別碼,未輸入!';
        }

        if(count($arry_err)!==0){
            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
            die();
        }

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        ////建立連線 mssr
        //$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //主SQL撈取
        //-----------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,總店";

        //-----------------------------------------------
        //查找, 錄音資訊
        //-----------------------------------------------

            $has_record         =false;
            $root               =str_repeat("../",3)."info/user/".(int)$sess_uid."/book";

            $record_path_mp3    ="{$root}/".trim($book_sid)."/record/1.mp3";
            $record_path_mp3_enc=mb_convert_encoding($record_path_mp3,$fso_enc,$page_enc);

            $record_path_wav    ="{$root}/".trim($book_sid)."/record/1.wav";
            $record_path_wav_enc=mb_convert_encoding($record_path_wav,$fso_enc,$page_enc);

            $record_path        ='';

            if(file_exists($record_path_mp3_enc)){
                $has_record =true;
                $record_path=$record_path_mp3_enc;
            }
            if(file_exists($record_path_wav_enc)){
                $has_record =true;
                $record_path=$record_path_wav_enc;
            }
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <link rel="stylesheet" href="css/code.css">
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=true);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../inc/code.js"></script>
    <script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
	<script src="../../../lib/jquery/ui/code.js"></script>
    <!-- 專屬 -->
    <script src="inc/recordmp3.js"></script>
    <script src="inc/audio_player/code.js"></script>

    <!-- 微調 -->
    <style type='text/css'>
        body{
            padding: 0;
            margin: 0;
            /*background: #E9ECEC url(img/bg.png) repeat;*/
            font: 14pt 微軟正黑體,Arial, sans-serif;
        }
        ul{
            list-style: none;
        }
        #recordingslist audio{
            margin-bottom:0px;
        }
    </style>
</head>
<body>

	<img src="./img/recode_1.png" style="position:absolute; top:14px; left:205px;">
    <img src="./img/recode_2.png" style="position:absolute; top:53px; left:124px;">
    <img src="./img/recode_3.png" style="position:absolute; top:59px; left:523px;">
    <img src="./img/recode_4.png" style="position:absolute; top:239px; left:526px;">
    <div id='block' style="position:absolute; display:none; top:0px; left:0px; width:1000px; height:480px; background:#222222; opacity:0.8"></div>
    <table cellpadding="1" cellspacing="1" border="0" width="320px" height='130px' style='position: absolute; top:302px; left:532px;'/>
        <tr align='left'>
            <td colspan='2' valign='middle'>
                <span id='record_state'  style="position:absolute;left:0px; top:-30px;">(錄音確認中...)</span>
                <span id='record_time'  style="position:absolute;left:120px; top:30px; color:#FFFFFF"></span>
          </td>
</tr>
        <tr align='center'>
            <td width='45px' align='center' valign='middle'>

                <img id='start' src="img/start.png"  border="0" alt="開始錄音" style='display:none; z-index:99999;position:absolute;left:0px; top:20px;'
                onclick='startRecording(this);void(0);' onmouseover='this.style.cursor="pointer" '/>
                <img id='stop' src="img/stop.png"  border="0" alt="停止錄音" style='display:none;position:absolute;left:50px; top:20px;'
                onclick='stopRecording(this);void(0);' onmouseover='this.style.cursor="pointer"'/>

            </td>
            <td valign='middle' align='left'>
                <img id='upload' src="img/upload.png" width="150" height="50" border="0" alt="上傳檔案" style='display:none;position:absolute;left:0px; top:20px;'
                onclick='void(0);' onmouseover='this.style.cursor="pointer"'/>
            </td>
            <td valign='middle' align='left'>
                <img id='close' src="img/close.png" width="150" height="50" border="0" style='display:none; position:absolute;left:160px; top:20px;'
                onclick='closeUpdata();void(0);' onmouseover='this.style.cursor="pointer"'/>
            </td>
        </tr>
        <tr align='left'>
            <td align='left' colspan='2' valign='middle'>
                <span id="recordingslist" style='display:none;float:left;'>
                	<div id="player_bar">
                    <!-- 撥放器  -->
                    <img src="img/recode_5.png" style=" position:absolute;top:73px; left:15px">
                   		<div id="player" style=" position:absolute;top:80px; left:50px">
                        	<div class="ctrl">
                            	<div class="control">
                                	<div class="left">
                                    	<div class="playback icon">
                                        </div>
                                    </div>
                                	<div class="volume right">
                                    	<div class="mute icon left">
                                        </div>
                                    	<div class="slider left">
                                        	<div class="pace">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <div class="progress">
                        		<div class="slider">
                                	<div class="loaded">
                                    </div>
                                <div class="pace">
                                </div>
                            </div>
                            <div class="timer left">0:00</div>
                        	</div>

                     	</div>
                     </div>
                     <!-- 撥放END器  -->

                    </div>
                </span>
                <input type="text" id="update_flag" name="update_flag" value="false" style='display:none;'>
            </td>
        </tr>
    </table>
<? if(($_SESSION['class'][0][1] == 'gcp_2013_2_3_9' || $_SESSION['class'][0][1] == 'gcp_2013_2_3_4' || $_SESSION['class'][0][1] == 'gcp_2013_2_3_1' ))//聊書判斷入口
		{?>
<div  style="position:absolute; top:187px; left:873px; color:#FFFFFF; width:120px; font-size:24px">
    前往聊書
    <BR>
    <img onClick="go_to_take()" src="../img/to_toke.png" style="cursor: pointer;">
</div><? }?>
    <pre id="log"></pre>

<script type="text/javascript">
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    //---------------------------------------------------
    //物件
    //---------------------------------------------------
		//紀錄
		//window.document.getElementById('player_bar').innerHTML = '<div id="player" style=" position:absolute;top:80px;"><div class="ctrl"><div class="control"><div class="left"><div class="playback icon"></div></div><div class="volume right"><div class="mute icon left"></div><div class="slider left"><div class="pace"></div></div></div></div><div class="progress"><div class="slider"><div class="loaded"></div><div class="pace"></div></div><div class="timer left">0:00</div></div></div></div>';
		var time; //
		var btn_lock = false;

		var set_record = false;
		var record_path = '<? echo $record_path_mp3_enc; ?>';
		var has_record = '<? if($has_record){echo 1;}else{echo 0;} ?>';
        //輸入
        var audio_context;
        var recorder;
		var block			=document.getElementById('block');
		var close_p			=document.getElementById('close');
        var ostart          =document.getElementById('start');
        var ostop           =document.getElementById('stop');
        var oupload         =document.getElementById('upload');
        var orecordingslist =document.getElementById('recordingslist');
        var orecord_state   =document.getElementById('record_state');
        var oupdate_flag    =document.getElementById('update_flag');

        var user_id         =<?php echo (int)$sess_uid;?>;
        var book_sid        ='<?php echo trim($book_sid);?>';



    //---------------------------------------------------
    //函式
    //---------------------------------------------------

        //輸入裝置監測
        function startusermedia(stream){
            var input= audio_context.createMediaStreamSource(stream);
            //_log('Media stream created' );
            //_log("input sample rate" +input.context.sampleRate);

            //建立裝置連線
            input.connect(audio_context.destination);
            //_log('Input connected to audio context destination.');

            //初始化, 錄音物件
            recorder=new Recorder(input);
            //_log('Recorder initialised.');

            //檢核
            if(typeof(recorder)==='object'){
				$(block).hide();
				$(close_p).hide();
                $(ostart).show();
                $(ostop).hide();
                $(oupload).hide();
                $(orecordingslist).show();
				orecord_state.style.color = '#000000';
                orecord_state.innerHTML='請點我開始錄音<BR> 　↓';
            }
			if(has_record == 0)
			{
				$(orecordingslist).hide();
				orecord_state.innerHTML='尚未錄音，請點我開始錄音<BR> 　↓';
			}
        }

        //錄音
        function startRecording(button){
            if(btn_lock)return;
			btn_lock = true;

			recorder.clear();
            recorder && recorder.record();
			$(block).show();
			$(close_p).hide();
            $(ostart).hide();
            $(ostop).show();
            $(oupload).hide();
            $(orecordingslist).hide();
			window.parent.set_hide_rec_page_all_btn(true);
			
			window.parent.set_rec_on_edit(true,"recode");
            oupdate_flag.value='false';
			orecord_state.style.color = '#ffffff';
            orecord_state.innerHTML='錄音中...<BR>想停止錄音請點我<BR><BR>→';

			btn_lock = false;
            //_log('Recording...');
        }

        //停止錄音
        function stopRecording(button){
			if(btn_lock)return;
			btn_lock = true;

            recorder && recorder.stop();
            createDownloadLink();
			$(block).show();
			$(close_p).hide();
            $(ostart).hide();
            $(ostop).hide();
            $(oupload).hide();

			time = window.parent.rec_edit_time;
			window.parent.echo_add("錄音停止時間"+time);
			window.parent.rec_edit_time = 0;
			window.parent.set_rec_on_edit(false);
			orecord_state.style.color = '#ffffff';
            orecord_state.innerHTML='停止錄音，錄音檔案製作中<BR>請稍後...';
            //$(orecordingslist).empty();
			window.parent.echo_add("MMMM");
            //_log('Stopped recording.');

			btn_lock = false;
        }
		//關閉上傳頁面
		function closeUpdata()
		{
			$(block).hide();
			$(close_p).hide();
            $(ostart).show();
            $(ostop).hide();
            $(oupload).hide();
			window.parent.set_hide_rec_page_all_btn(false);
			
			orecord_state.style.color = '#000000';
			if(has_record == 1)
			{
				$(orecordingslist).show();
			}
			else
			{
				$(orecordingslist).hide();
			}
			window.document.getElementById("record_time").innerHTML = '';
			orecord_state.innerHTML='開始錄音請點我<BR> 　↓';
		}


        //上傳
        oupload.onclick=function(){
			if(btn_lock)return;
			btn_lock = true;
           // if(confirm('你確定要上傳嗎?')){
                $(block).show();
				$(close_p).hide();
				$(ostart).hide();
				$(ostop).hide();
				$(oupload).hide();
				orecord_state.innerHTML='上傳中，請稍後...';

				oupdate_flag.value='true';
                createDownloadLink();
          /*  }else{
                return false;
            }*/
			btn_lock = false;
        }

        //建立音頻連結
        function createDownloadLink(){
            recorder && recorder.exportWAV(function(blob){

            });
        }

        //紀錄
        function _log(e, data){
            log.innerHTML += "\n" + e + " " + (data || '');
        }
		//設置錄音時間
		function record_time(value)
		{
			window.document.getElementById("record_time").innerHTML = "錄音時間　";
			var tmp = parseInt(parseInt(value)/60);
			if(tmp<10)tmp = "0"+tmp;
			if(tmp>0)stopRecording();//最長1分鐘
			//window.document.getElementById("record_time").innerHTML += tmp+":";
			tmp = parseInt(parseInt(value)%60);
			if(tmp<10)tmp = "0"+tmp;
			window.document.getElementById("record_time").innerHTML += tmp;
			window.document.getElementById("record_time").innerHTML += "/60";
		}
		//var ids = Math.parseInt(Math.random()*999999);


		//設置播放裝置
		function set_player()
		{

			window.parent.echo_add("設置播放介面:START");
			if(set_record == false)
			{
				value = 'load';
				set_record = true;
			}else
			{
				value = 'reload';

			}
			var tmp=Math.random();


			(function($){
				tmp_record_path= record_path+"?xxx="+tmp;
				window.parent.echo_add(tmp_record_path);
				var playlist=[{
						mp3: tmp_record_path
					}]

				//呼叫
				audio_player(playlist,value);


			})(jQuery);

			window.parent.echo_add("設置播放介面:END");
		}
		if(has_record == 1)set_player();

		//set_player();
    //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        window.onload=function init(){
            try{
                //webkit shim
                window.AudioContext = window.AudioContext || window.webkitAudioContext;
                navigator.getUserMedia = ( navigator.getUserMedia ||
                               navigator.webkitGetUserMedia ||
                               navigator.mozGetUserMedia ||
                               navigator.msGetUserMedia);
                window.URL = window.URL || window.webkitURL;

                audio_context = new AudioContext;

                //// webkit shim
                //window.AudioContext = window.AudioContext || window.webkitAudioContext;
                //navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia;
                //window.URL = window.URL || window.webkitURL;
                //
                //audio_context = new AudioContext;

                //_log('Audio context set up.');
                //_log('navigator.getUserMedia ' + (navigator.getUserMedia ? 'available.' : 'not present!'));
            }catch(e){
                alert('您的瀏覽器不支援錄音功能!');
            }

            navigator.getUserMedia({audio:true},startusermedia,function(e){
                //_log('No live audio input: ' + e);
            });
        };
</script>
</body>
</Html>