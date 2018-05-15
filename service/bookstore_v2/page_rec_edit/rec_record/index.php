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
			$user = $arry_ftp1_info['account']; 
			$pass = $arry_ftp1_info['password']; 
			$host = $arry_ftp1_info['host']; 

            $has_record         =false;
            $root               =str_repeat("../",4)."info/user/".(int)$sess_uid."/book";

            $record_path_mp3    ="ftp://".$user . ":" . $pass . "@" . $host . "/public_html/mssr/info/user/{$sess_uid}/book/".trim($book_sid)."/record/1.mp3";
           	//$record_path_mp3_enc=mb_convert_encoding($record_path_mp3,$fso_enc,$page_enc);

            $record_path_wav    ="ftp://".$user . ":" . $pass . "@" . $host . "/public_html/mssr/info/user/{$sess_uid}/book/".trim($book_sid)."/record/1.wav";
            //$record_path_wav_enc=mb_convert_encoding($record_path_wav,$fso_enc,$page_enc);

            $record_path        ='';

            if(file_exists($record_path_mp3)){
                $has_record =true;
                $record_path="http://" . $host . "/mssr/info/user/{$sess_uid}/book/{$book_sid}/record/1.mp3";
            }
            if(file_exists($record_path_wav)){
                $has_record =true;
                $record_path="http://" . $host . "/mssr/info/user/{$sess_uid}/book/{$book_sid}/record/1.wav";
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
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <link rel="stylesheet" href="css/code.css">
  
    <!-- 通用 -->
    <link rel="stylesheet" href="../../css/btn.css">
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
	<script src="../../../../lib/jquery/ui/code.js"></script>
    <script src="../../js/select_thing.js" type="text/javascript"></script>
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
		#start{
			position:absolute;
			width:56px;
			height:56px;
			background: url('img/record_btn.png') -112px 0;
		}
		#start:hover{
    		background: url('img/record_btn.png') -112px -56px;
		}
		#stop{
			position:absolute;
			width:56px;
			height:56px;
			background: url('img/record_btn.png') 0 0;
		}
		#stop:hover{
    		background: url('img/record_btn.png') 0 -56px;
		}
		#upload{
			position:absolute;
			width:150px;
			height:50px;
			background: url('img/upload_btn.png') 0 0;
		}
		#upload:hover{
    		background: url('img/upload_btn.png') 0 -50px;
		}
		#close{
			position:absolute;
			width:150px;
			height:50px;
			background: url('img/upload_btn.png') -150px 0;
		}
		#close:hover{
    		background: url('img/upload_btn.png') -150px -50px;
		}
	
		
    </style>
</head>
<body>

	<img style="position:absolute; top:5px; left:146px;" src="img/tittle_2.png" />
    <img src="./img/recode_2.png" style="position:absolute; top:53px; left:124px;">
    <img src="./img/recode_3.png" style="position:absolute; top:59px; left:523px;">
    <img src="./img/recode_4.png" style="position:absolute; top:239px; left:526px;">
<div id='block' style="position:absolute; display:none; top:0px; left:0px; width:1000px; height:480px; background:#222222; opacity:0.8"></div>
    <table cellpadding="1" cellspacing="1" border="0" width="320px" height='130px' style='position: absolute; top:302px; left:532px;'/>
        <tr align='left'>
            <td colspan='2' valign='middle'>
              <span id='record_state'  style="position:absolute;left:0px; top:-30px;"><div style="color: #F00">請點選上方【允許】使用麥克風</div><br>
              　<a onClick="window.open(' ./img/help.png ', '錄音開啟教學', config='height=600,width=700');" href="#" >操作圖解</a ></span>
                <span id='record_time'  style="position:absolute;left:120px; top:30px; color:#FFFFFF"></span>
          </td>
</tr>
        <tr align='center'>
            <td width='45px' align='center' valign='middle'>

                <a id='start' border="0" alt="開始錄音" style='display:none; z-index:99999;position:absolute;left:0px; top:20px;' onclick='startRecording(this);void(0);' onmouseover='this.style.cursor="pointer" '/></a>
                <a id='stop' border="0" alt="停止錄音" style='display:none;position:absolute;left:50px; top:20px;'
                onclick='stopRecording(this);void(0);' onmouseover='this.style.cursor="pointer"'/></a>

            </td>
            <td valign='middle' align='left'>
                <a id='upload' alt="上傳檔案" style='display:none;position:absolute;left:0px; top:20px;' onclick='void(0);' onmouseover='this.style.cursor="pointer"'/></a>
            </td>
            <td valign='middle' align='left'>
                <a id='close'   style='display:none; position:absolute;left:160px; top:20px;' onclick='closeUpdata();void(0);' onmouseover='this.style.cursor="pointer"'/></a>
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
    <!--<img onClick="go_to_take()" src="../img/to_toke.png" style="cursor: pointer;"> -->
</div><? }?>
    <pre id="log"></pre>
<? echo $array['rec_record_score'];?>
<script type="text/javascript">
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    //---------------------------------------------------
    //物件
    //---------------------------------------------------
		//紀錄
		//window.document.getElementById('player_bar').innerHTML = '<div id="player" style=" position:absolute;top:80px;"><div class="ctrl"><div class="control"><div class="left"><div class="playback icon"></div></div><div class="volume right"><div class="mute icon left"></div><div class="slider left"><div class="pace"></div></div></div></div><div class="progress"><div class="slider"><div class="loaded"></div><div class="pace"></div></div><div class="timer left">0:00</div></div></div></div>';
		var time = 0; //
		var time_lock = false;
		var btn_lock = false;
		var auth_coin_open =  window.parent.parent.auth_coin_open;
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
		window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e13',window.parent.parent.action_on);
		
		<? if($array['rec_record_score'] != ""){?>
		window.parent.set_content('<? echo $array['rec_record_content']; ?>','<? echo $array['rec_record_score']; ?>');
		<? }?>//cover
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
        //輸入裝置監測
        function startusermedia(stream){
            var input= audio_context.createMediaStreamSource(stream);

            //建立裝置連線
            input.connect(audio_context.destination);

            //初始化, 錄音物件
            recorder=new Recorder(input);

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
			time_lock = true;
			recorder.clear();
            recorder && recorder.record();
			$(block).show();
			$(close_p).hide();
            $(ostart).hide();
            $(ostop).show();
            $(oupload).hide();
            $(orecordingslist).hide();
			time=0;
			//這邊要把螢幕遮住
			window.parent.set_top_btn("none");
            oupdate_flag.value='false';
			orecord_state.style.color = '#ffffff';
            orecord_state.innerHTML='錄音中...<BR>想停止錄音請點我<BR><BR>→';

			btn_lock = false;
        }

        //停止錄音
        function stopRecording(button){
			if(btn_lock)return;
			btn_lock = true;
			time_lock = false;
            recorder && recorder.stop();
            createDownloadLink();
			$(block).show();
			$(close_p).hide();
            $(ostart).hide();
            $(ostop).hide();
            $(oupload).hide();

	
			echo("錄音停止時間"+time);
			
			orecord_state.style.color = '#ffffff';
            orecord_state.innerHTML='停止錄音，錄音檔案製作中<BR>請稍後...';
  
			echo("MMMM");


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
			//這邊要把螢幕解開
			window.parent.set_top_btn("block");
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
                $(block).show();
				$(close_p).hide();
				$(ostart).hide();
				$(ostop).hide();
				$(oupload).hide();
				orecord_state.innerHTML='上傳中，請稍後...';

				oupdate_flag.value='true';
                createDownloadLink();
				window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e19',window.parent.parent.action_on);

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
			echo(time);
			if(time_lock)record_time(time);
			rec_edit_setTimeout=setTimeout("timedCount()",1000);
		}
		timedCount();


		//設置播放裝置
		function set_player()
		{

			echo("設置播放介面:START");
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
				echo(tmp_record_path);
				var playlist=[{
						mp3: tmp_record_path
					}]

				//呼叫
				audio_player(playlist,value);


			})(jQuery);

			echo("設置播放介面:END");
		}
		if(has_record == 1)set_player();

		//set_player();
		function help()
		{
			
			
		}
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
				var Sys = {};
				var ua = navigator.userAgent.toLowerCase();
				var s;
				(s = ua.match(/rv:([\d.]+)\) like gecko/)) ? Sys.ie = s[1] :
				(s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] :
				(s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] :
				(s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] :
				(s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] :
				(s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;
				cover("");
				//if (Sys.ie) document.write('IE: ' + Sys.ie);
				//if (Sys.firefox) document.write('Firefox: ' + Sys.firefox);
				if (Sys.chrome) cover('Chrome瀏覽器暫時不支援錄音功能 ',1);
			//	if (Sys.opera) document.write('Opera: ' + Sys.opera);
				//if (Sys.safari) document.write('Safari: ' + Sys.safari);
				
                //// webkit shim
                //window.AudioContext = window.AudioContext || window.webkitAudioContext;
                //navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia;
                //window.URL = window.URL || window.webkitURL;
                //
                //audio_context = new AudioContext;

                //_log('Audio context set up.');
                //_log('navigator.getUserMedia ' + (navigator.getUserMedia ? 'available.' : 'not present!'));
            }catch(e){
                cover('您的瀏覽器不支援錄音功能!',1);
				
            }

            navigator.getUserMedia({audio:true},startusermedia,function(e){
                //_log('No live audio input: ' + e);
				
            });
        };
		
		
</script>    
</body>
</Html>