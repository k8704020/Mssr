//-------------------------------------------------------
//inc
//-------------------------------------------------------
//root      根單元
//rec       推薦
//
//-------------------------------------------------------
//root
//-------------------------------------------------------
//  root/fast_area_config()     快速切換設置
//  root/sel_identity()         選擇身分
//  root/sel_class_code()       選擇班級
//
//-------------------------------------------------------
//rec
//-------------------------------------------------------
//  rec/audio_player()          audio播放器
//
//-------------------------------------------------------


//-------------------------------------------------------
//root
//-------------------------------------------------------
//  root/fast_area_config()     快速切換設置
//  root/sel_identity()         選擇身分
//  root/sel_class_code()       選擇班級

    function fast_area_config(obj,_top,_right){    
    //---------------------------------------------------
    //快速切換設置
    //---------------------------------------------------
    //obj       容器
    //_top      距離上方邊距
    //_right    距離右方邊距
    //---------------------------------------------------
    //回傳值
    //---------------------------------------------------

        //-----------------------------------------------
        //參數設置
        //-----------------------------------------------

            var $win         = $(window),
                $fast_area   = $(obj).show(),
                $fast_content=$('#fast_content').hide(),
                
                _top         = _top,    //距離上方之邊距
                _right       = _right   //距離右方之邊距
    
                _movespeed   = 1;       //移動的速度

        //-----------------------------------------------
        //移動到定點
        //-----------------------------------------------

            $fast_area.css({
                top:_top,
                right:_right
            });

            $fast_content.css({
                top:_top,
                right:-$(this).width()
            });

        //-----------------------------------------------
        //事件處理
        //-----------------------------------------------
            
            //滑鼠移入移出
            $fast_area.mouseover(function(){
                $fast_content.stop().animate({
                    top:_top,
                    right:_right+15,
                    opacity:1 
                }, _movespeed,function(){$fast_content.show()});      
            }).mouseout(function(){
                $fast_content.stop().animate({
                    top:_top,
                    right:-$(this).width(),
                    opacity:0
                }, _movespeed,function(){$fast_content.hide()});          
            });

            $fast_content.mouseover(function(){
                $(this).stop().animate({
                    top:_top,
                    right:_right+15,
                    opacity:1 
                }, _movespeed,function(){$fast_content.show()});      
            }).mouseout(function(){
                $(this).stop().animate({
                    top:_top,
                    right:-$(this).width(),
                    opacity:0
                }, _movespeed,function(){$fast_content.hide()});           
            });

            ////scroll, resize 事件
            //$win.bind('scroll resize', function(){
            //    var $this = $(this);
            //    //控制移動
            //    $change_identity.stop().animate({
            //        top: $this.scrollTop() + $this.height() - _height - _diffY,
            //        left: $this.scrollLeft() + $this.width() - _width - _diffX
            //    }, _moveSpeed);
            //}).scroll()
    }

    function sel_identity(rd,identity){
    //---------------------------------------------------
    //選擇身分
    //---------------------------------------------------
    //rd            層級指標,預設0,表示在目前目錄下
    //identity      身分代號
    //---------------------------------------------------
    //回傳值
    //---------------------------------------------------
    //
    //---------------------------------------------------

        //參數
        if(rd===0){
            rd='';
        }else{
            rd=str_repeat('../',rd);
        }

        //ajax設定
        var $_url           =rd+'user_info/sel_identity/sel_identityA.php';
        var $_type          ="GET";
        var $_datatype      ="json";

        //送出
        _ajax($_url,$_type,$_datatype,identity);

        function _ajax($_url,$_type,$_datatype,identity){
        //ajax設置
            $.ajax({
            //參數設置
                async      :true,      //瀏覽器在發送的時候，停止任何其他的呼叫。
                cache      :false,     //快取的回應。
                global     :true,      //是否使用全局 AJAX 事件。
                timeout    :10000,
                contentType:"application/x-www-form-urlencoded; charset=UTF-8",

                url        :$_url,     //請求的頁面
                type       :$_type,    //GET or POST
                datatype   :$_datatype,
                data       :{
                    identity:encodeURI(trim(identity))
                },

            //事件
                beforeSend  :function(){//傳送前處理
                },
                success     :function(respones){
                //成功處理

                    respones=jQuery.parseJSON(respones);
                    var msg=respones.msg;   //訊息

                    alert(msg);

                    //重整當前頁面
                    location.reload();
                    return false;
                },
                error       :function(xhr, ajaxoptions, thrownerror){
                //失敗處理
                    if(ajaxoptions==='timeout'){
                        alert('身分更換失敗!');
                        //重整當前頁面
                        location.reload();
                        return false;
                    }else{
                        alert('身分更換失敗!');
                        //重整當前頁面
                        location.reload();
                        return false;
                    }
                },
                complete    :function(){//傳送後處理
                }
            });
        }
    }

    function sel_class_code(rd,class_code,class_category,grade,classroom,semester_code){
    //---------------------------------------------------
    //選擇班級
    //---------------------------------------------------
    //rd                層級指標,預設0,表示在目前目錄下
    //class_code        班級代號
    //class_category    班級類別
    //grade             年級
    //classroom         班級
    //semester_code     學期代號
    //---------------------------------------------------
    //回傳值
    //---------------------------------------------------
    //
    //---------------------------------------------------

        //參數
        if(rd===0){
            rd='';
        }else{
            rd=str_repeat('../',rd);
        }

        //ajax設定
        var $_url           =rd+'user_info/sel_class_code/sel_class_codeA.php';
        var $_type          ="GET";
        var $_datatype      ="json";

        //送出
        _ajax($_url,$_type,$_datatype,class_code,class_category,grade,classroom,semester_code);

        function _ajax($_url,$_type,$_datatype,class_code,class_category,grade,classroom,semester_code){
        //ajax設置
            $.ajax({
            //參數設置
                async      :true,      //瀏覽器在發送的時候，停止任何其他的呼叫。
                cache      :false,     //快取的回應。
                global     :true,      //是否使用全局 AJAX 事件。
                timeout    :10000,
                contentType:"application/x-www-form-urlencoded; charset=UTF-8",

                url        :$_url,     //請求的頁面
                type       :$_type,    //GET or POST
                datatype   :$_datatype,
                data       :{
                    class_code:encodeURI(trim(class_code)),
                    class_category:encodeURI(trim(class_category)),
                    grade:encodeURI(trim(grade)),
                    classroom:encodeURI(trim(classroom)),
                    semester_code:encodeURI(trim(semester_code))
                },

            //事件
                beforeSend  :function(){//傳送前處理
                },
                success     :function(respones){
                //成功處理

                    respones=jQuery.parseJSON(respones);
                    var msg=respones.msg;   //訊息

                    alert(msg);

                    //重整當前頁面
                    location.reload();
                    return false;
                },
                error       :function(xhr, ajaxoptions, thrownerror){
                //失敗處理
                    if(ajaxoptions==='timeout'){
                        alert('班級更換失敗!');
                        //重整當前頁面
                        location.reload();
                        return false;
                    }else{
                        alert('班級更換失敗!');
                        //重整當前頁面
                        location.reload();
                        return false;
                    }
                },
                complete    :function(){//傳送後處理
                }
            });
        }
    }

//-------------------------------------------------------
//rec
//-------------------------------------------------------
//  rec/audio_player()          audio播放器

    function audio_player(playlist,user_id,book_sid,flag){
    //---------------------------------------------------
    //audio播放器
    //---------------------------------------------------
    //obj       容器
    //---------------------------------------------------
    //回傳值
    //---------------------------------------------------

        if(flag==='load'){
            // Settings
            var playlist = playlist,
                repeat = localStorage.repeat || 0,
                shuffle = localStorage.shuffle || 'false',
                continous = false,
                autoplay = false,    
                time = new Date(),
                currentTrack = shuffle === 'true' ? time.getTime() % playlist.length : 0,
                trigger = false,
                volume = localStorage.volume || 0.8,
                audio, timeout, isPlaying, playCounts;
        }

    //    if(flag==='reload'){
    //        try{
    //            $('.playback').removeClass('playing');
    //            var oaudios=document.getElementsByTagName('AUDIO');
    //            for(var i=0;i<=oaudios.length;i++){
    //                var oaudio=oaudios[i];  
    //                oaudio.pause();
    //                oaudio.currentTime = 0;
    //                //$(oaudio).remove();
    //            }
    //            clearInterval(updateProgress);
    //            isPlaying = false;
    //            $('.player_'+user_id+'_'+book_sid).empty();
    //            loadMusic(currentTrack);
    //
    ////alert(oaudios.length);
    //
    //            //var oaudio=document.getElementById('audio');
    //            //oaudio.pause();
    //            //$('.playback').removeClass('playing');
    //            //clearInterval(updateProgress);
    //            //isPlaying = false;
    //            //oaudio.currentTime = 0;
    //            //
    //            //$('.player_'+user_id+'_'+book_sid).find('AUDIO').remove();
    //            //loadMusic(currentTrack);
    //            ////oaudio.src=playlist[0].mp3;
    //            ////oaudio.load();        
    //        }catch(e){  
    //            
    //        }
    //        return true;
    //    }

        var play = function(){
            audio.play();
            $('#playback'+'_'+user_id+'_'+book_sid).addClass('playing');
            timeout = setInterval(updateProgress, 500);
            isPlaying = true;
        }

        var pause = function(){
            audio.pause();
            $('#playback'+'_'+user_id+'_'+book_sid).removeClass('playing');
            clearInterval(updateProgress);
            isPlaying = false;
        }

        // Update progress
        var setProgress = function(value){
            var currentSec = parseInt(value%60) < 10 ? '0' + parseInt(value%60) : parseInt(value%60);
            var audio_duration = audio.duration;

            //Time ctrl
            if(audio_duration>=15){
                var ratio = (value / audio.duration * 100) * 2;
            }else{
                var ratio = (value / audio.duration * 100);
            }
            
            $('#timer'+'_'+user_id+'_'+book_sid).html(parseInt(value/60)+':'+currentSec);
            $('.progress #pace'+'_'+user_id+'_'+book_sid).css('width', ratio + '%');
            $('.progress #slider'+'_'+user_id+'_'+book_sid+' a').css('left', ratio + '%');
            
            //Time ctrl
            if(audio_duration>=15){
                if((audio.currentTime)>=(audio_duration/2)){
                    audio.pause();                                
                    audio.currentTime = 0;
                }
            }
        }

        var updateProgress = function(){
            setProgress(audio.currentTime);
        }

        //---------------------------------------------------
        //Progress slider
        //---------------------------------------------------

            $('.progress #slider'+'_'+user_id+'_'+book_sid).slider({step: 0.1, slide: function(event, ui){
                $(this).addClass('enable');
                //Time ctrl
                if(audio.duration>=15){
                    setProgress(audio.duration * ui.value / 100 / 2);
                }else{
                    setProgress(audio.duration * ui.value / 100);
                }
                clearInterval(timeout);
            }, stop: function(event, ui){
                //Time ctrl
                if(audio.duration>=15){
                    audio.currentTime = audio.duration * ui.value / 100 / 2;
                }else{
                    audio.currentTime = audio.duration * ui.value / 100;
                }
                $(this).removeClass('enable');
                timeout = setInterval(updateProgress, 500);
            }});

        //---------------------------------------------------
        //Volume
        //---------------------------------------------------

            // Volume slider
            var setVolume = function(value){
                audio.volume = localStorage.volume = value;
                $('.volume #pace'+'_'+user_id+'_'+book_sid).css('width', value * 100 + '%');
                $('.volume #slider'+'_'+user_id+'_'+book_sid+' a').css('left', value * 100 + '%');
            }
            
            $('.volume #slider'+'_'+user_id+'_'+book_sid).slider({max: 1, min: 0, step: 0.01, value: volume, slide: function(event, ui){
                setVolume(ui.value);
                $(this).addClass('enable');
                $('#mute'+'_'+user_id+'_'+book_sid).removeClass('enable');
            }, stop: function(){
                $(this).removeClass('enable');
            }}).children('#pace'+'_'+user_id+'_'+book_sid).css('width', volume * 100 + '%');

            $('#mute'+'_'+user_id+'_'+book_sid).click(function(){
                if ($(this).hasClass('enable')){
                    setVolume($(this).data('volume'));
                    $(this).removeClass('enable');
                } else {
                    $(this).data('volume', audio.volume).addClass('enable');
                    setVolume(0);
                }
            });

        // Switch track
        var switchTrack = function(i){
            if (i < 0){
                track = currentTrack = playlist.length - 1;
            } else if (i >= playlist.length){
                track = currentTrack = 0;
            } else {
                track = i;
            }

            $('audio').remove();
            loadMusic(track);
            if (isPlaying == true) play();
        }

        // Shuffle
        var shufflePlay = function(){
            var time = new Date(),
                lastTrack = currentTrack;
            currentTrack = time.getTime() % playlist.length;
            if (lastTrack == currentTrack) ++currentTrack;
            switchTrack(currentTrack);
        }

        // Fire when track ended
        var ended = function(){
            pause();
            audio.currentTime = 0;
            playCounts++;
            if (continous == true) isPlaying = true;
            if (repeat == 1){
                play();
            } else {
                if (shuffle === 'true'){
                    shufflePlay();
                } else {
                    if (repeat == 2){
                        switchTrack(++currentTrack);
                    } else {
                        if (currentTrack < playlist.length) switchTrack(++currentTrack);
                    }
                }
            }
        }

        var beforeLoad = function(){
            var endVal = this.seekable && this.seekable.length ? this.seekable.end(0) : 0;
            $('.progress #loaded'+'_'+user_id+'_'+book_sid).css('width', (100 / (this.duration || 1) * endVal) +'%');
        }

        // Fire when track loaded completely
        var afterLoad = function(){
            if (autoplay == true) play();
        }

        // Load track
        var loadMusic = function(i){
            var item = playlist[i],
                obj  ='.player'+'_'+user_id+'_'+book_sid,            
                newaudio = $('<audio id="audio'+'_'+user_id+'_'+book_sid+'">').html('<source src="'+item.mp3+'"><source src="'+item.ogg+'">').appendTo(obj);

            //$('.cover').html('<img src="'+item.cover+'" alt="'+item.album+'">');
            //$('.tag').html('<strong>'+item.title+'</strong><span class="artist">'+item.artist+'</span><span class="album">'+item.album+'</span>');
            //$('#playlist li').removeClass('playing').eq(i).addClass('playing');
            audio = newaudio[0];
            audio.volume = $('#mute'+'_'+user_id+'_'+book_sid).hasClass('enable') ? 0 : volume;
            audio.addEventListener('progress', beforeLoad, false);
            audio.addEventListener('durationchange', beforeLoad, false);
            audio.addEventListener('canplay', afterLoad, false);
            audio.addEventListener('ended', ended, false);
        }
        
        $('#playback'+'_'+user_id+'_'+book_sid).on('click', function(){
            if ($(this).hasClass('playing')){
                pause();
            } else {
                play();
            }
        });
        $('.rewind').on('click', function(){
            if (shuffle === 'true'){
                shufflePlay();
            } else {
                switchTrack(--currentTrack);
            }
        });
        $('.fastforward').on('click', function(){
            if (shuffle === 'true'){
                shufflePlay();
            } else {
                switchTrack(++currentTrack);
            }
        });
        $('#playlist li').each(function(i){
            var _i = i;
            $(this).on('click', function(){
                switchTrack(_i);
            });
        });

        if (shuffle === 'true') $('.shuffle').addClass('enable');
        if (repeat == 1){
            $('.repeat').addClass('once');
        } else if (repeat == 2){
            $('.repeat').addClass('all');
        }

        $('.repeat').on('click', function(){
            if ($(this).hasClass('once')){
                repeat = localStorage.repeat = 2;
                $(this).removeClass('once').addClass('all');
            } else if ($(this).hasClass('all')){
                repeat = localStorage.repeat = 0;
                $(this).removeClass('all');
            } else {
                repeat = localStorage.repeat = 1;
                $(this).addClass('once');
            }
        });

        $('.shuffle').on('click', function(){
            if ($(this).hasClass('enable')){
                shuffle = localStorage.shuffle = 'false';
                $(this).removeClass('enable');
            } else {
                shuffle = localStorage.shuffle = 'true';
                $(this).addClass('enable');
            }
        });    

        if(flag==='load'){
            loadMusic(currentTrack);
        }
    }