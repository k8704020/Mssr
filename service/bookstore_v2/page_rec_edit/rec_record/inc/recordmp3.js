(function(window){

  var WORKER_PATH = 'inc/recorderWorker.js';
  var encoderWorker = new Worker('inc/mp3Worker.js');

  var Recorder = function(source, cfg){
    var config = cfg || {};
    var bufferLen = config.bufferLen || 4096;
    this.context = source.context;
    this.node = (this.context.createScriptProcessor ||
                 this.context.createJavaScriptNode).call(this.context,
                                                         bufferLen, 2, 2);
    var worker = new Worker(config.workerPath || WORKER_PATH);
    worker.postMessage({
      command: 'init',
      config: {
        sampleRate: this.context.sampleRate
      }
    });
    var recording = false,
      currCallback;

    this.node.onaudioprocess = function(e){
      if (!recording) return;
      worker.postMessage({
        command: 'record',
        buffer: [
          e.inputBuffer.getChannelData(0)
        ]
      });
    }

    this.configure = function(cfg){
      for (var prop in cfg){
        if (cfg.hasOwnProperty(prop)){
          config[prop] = cfg[prop];
        }
      }
    }

    this.record = function(){
      recording = true;
    }

    this.stop = function(){
      recording = false;
    }

    this.clear = function(){
      worker.postMessage({ command: 'clear' });
    }

    this.getBuffer = function(cb) {
      currCallback = cb || config.callback;
      worker.postMessage({ command: 'getBuffer' })
    }

    this.exportWAV = function(cb, type){
      currCallback = cb || config.callback;
      type = type || config.type || 'audio/wav';
      if (!currCallback){
        throw new Error('Callback not set');
      }else{
        
      }
      worker.postMessage({
        command: 'exportWAV',
        type: type
      });
    }
	
	//Mp3 conversion
    worker.onmessage=function(e){
      var blob = e.data;
	  
	  var arrayBuffer;
	  var fileReader = new FileReader();
	  
	  fileReader.onload = function(){
		arrayBuffer = this.result;
		var buffer = new Uint8Array(arrayBuffer),
        data = parseWav(buffer);

        encoderWorker.postMessage({ cmd: 'init', config:{
            mode : 3,
			channels:1,
			samplerate: data.sampleRate,
			bitrate: data.bitsPerSample
        }});

        encoderWorker.postMessage({ cmd: 'encode', buf: Uint8ArrayToFloat32Array(data.samples) });
        encoderWorker.postMessage({ cmd: 'finish'});
        encoderWorker.onmessage = function(e) {
            if (e.data.cmd == 'data') {

				var mp3Blob = new Blob([new Uint8Array(e.data.buf)], {type: 'audio/mp3'});

                window.document.getElementById("record_time").innerHTML = '';
                if(oupdate_flag.value==='true'){
                    uploadAudio(mp3Blob);
                }else{
					
					orecord_state.style.color = '#ffffff';
                    orecord_state.innerHTML='你確定要上傳嗎，想要請按這<BR>  　　　↓  ';
       
					$(oupload).show();   
					$(block).show();
					$(close_p).show();
                }                
            }
        };
	  };
	  
	  fileReader.readAsArrayBuffer(blob);
	  
      currCallback(blob);
    }
	
	
	function encode64(buffer) {
		var binary = '',
			bytes = new Uint8Array( buffer ),
			len = bytes.byteLength;

		for (var i = 0; i < len; i++) {
			binary += String.fromCharCode( bytes[ i ] );
		}
		return window.btoa( binary );
	}

	function parseWav(wav) {
		function readInt(i, bytes) {
			var ret = 0,
				shft = 0;

			while (bytes) {
				ret += wav[i] << shft;
				shft += 8;
				i++;
				bytes--;
			}
			return ret;
		}
		if (readInt(20, 2) != 1) throw 'Invalid compression code, not PCM';
		if (readInt(22, 2) != 1) throw 'Invalid number of channels, not 1';
		return {
			sampleRate: readInt(24, 4),
			bitsPerSample: readInt(34, 2),
			samples: wav.subarray(44)
		};
	}

	function Uint8ArrayToFloat32Array(u8a){
		var f32Buffer = new Float32Array(u8a.length);
		for (var i = 0; i < u8a.length; i++) {
			var value = u8a[i<<1] + (u8a[(i<<1)+1]<<8);
			if (value >= 0x8000) value |= ~0x7FFF;
			f32Buffer[i] = value / 0x8000;
		}
		return f32Buffer;
	}
	
	function uploadAudio(mp3Data){
		var reader = new FileReader();
		reader.onload = function(event){
			var fd = new FormData();
            var mp3Name = encodeURIComponent(1+'.mp3');
			console.log("mp3name = " + mp3Name);
            fd.append('user_id', user_id);
            fd.append('book_sid', book_sid);
			fd.append('fname', mp3Name);
			fd.append('time', time);
			fd.append('data', event.target.result);
			fd.append('auth_coin_open',auth_coin_open);
			$.ajax({
				type: 'POST',
				url: 'uploadA.php',
				data: fd,
				processData: false,
				contentType: false
			}).done(function(data){
				echo("錄音上傳SQL回傳:"+data);
				var data_array = JSON.parse(data);
				if(data_array['error'] != '')window.alert(data_array['error']);
				if(data_array['coin']>0)window.parent.parent.set_coin(data_array["coin"]);
				if(data_array['exp_score']>0)window.parent.parent.set_score_exp(data_array["exp_score"]);
				if(data_array["text"]!='')cover(data_array["text"],1);

				orecord_state.style.color = '#000000';
                orecord_state.innerHTML='上傳成功喔!!';
				$(block).hide();
				$(close_p).hide();
                $(ostart).show();
                $(ostop).hide();
                $(oupload).hide();
				set_player();
                $(orecordingslist).show();
				has_record = 1;
				//這邊要把螢幕解開
				window.parent.set_top_btn("block");
			}).error(function(e){
				echo("AJAX:error:uploadAudio:");
				cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){uploadAudio(mp3Data);});
			}).complete(function(e){
				echo("AJAX:complete:uploadAudio:");
			});
		};      
		reader.readAsDataURL(mp3Data);
	}
	
    source.connect(this.node);
    this.node.connect(this.context.destination); 
  };

  window.Recorder = Recorder;

})(window);
