/*
audio播放器
*/

function audio_player(playlist,flag){

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

    if(flag==='reload'){
        try{
            $('.playback').removeClass('playing');
            var oaudios=document.getElementsByTagName('AUDIO');
            for(var i=0;i<=oaudios.length;i++){
                var oaudio=oaudios[i];  
                oaudio.pause();
                oaudio.currentTime = 0;
                //$(oaudio).remove();
            }
            clearInterval(updateProgress);
            isPlaying = false;
            $('#player').empty();
            loadMusic(currentTrack);

//alert(oaudios.length);

            //var oaudio=document.getElementById('audio');
            //oaudio.pause();
            //$('.playback').removeClass('playing');
            //clearInterval(updateProgress);
            //isPlaying = false;
            //oaudio.currentTime = 0;
            //
            //$('#player').find('AUDIO').remove();
            //loadMusic(currentTrack);
            ////oaudio.src=playlist[0].mp3;
            ////oaudio.load();        
        }catch(e){  
            
        }
        return true;
    }

    var play = function(){
        audio.play();
        $('.playback').addClass('playing');
        timeout = setInterval(updateProgress, 500);
        isPlaying = true;
    }

    var pause = function(){
        audio.pause();
        $('.playback').removeClass('playing');
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
        
        $('.timer').html(parseInt(value/60)+':'+currentSec);
        $('.progress .pace').css('width', ratio + '%');
        $('.progress .slider a').css('left', ratio + '%');
        
        //Time ctrl
        if(audio_duration>=15){
            if((audio.currentTime)>=(audio_duration/2)){
                audio.pause();                                
                isPlaying = false;   
                $('.playback').removeClass('playing');
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

        $('.progress .slider').slider({step: 0.1, slide: function(event, ui){
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
            $('.volume .pace').css('width', value * 100 + '%');
            $('.volume .slider a').css('left', value * 100 + '%');
        }
        
        $('.volume .slider').slider({max: 1, min: 0, step: 0.01, value: volume, slide: function(event, ui){
            setVolume(ui.value);
            $(this).addClass('enable');
            $('.mute').removeClass('enable');
        }, stop: function(){
            $(this).removeClass('enable');
        }}).children('.pace').css('width', volume * 100 + '%');

        $('.mute').click(function(){
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
        $('.progress .loaded').css('width', (100 / (this.duration || 1) * endVal) +'%');
    }

    // Fire when track loaded completely
    var afterLoad = function(){
        if (autoplay == true) play();
    }

    // Load track
    var loadMusic = function(i){
        var item = playlist[i],
            newaudio = $('<audio id="audio">').html('<source id="source" src="'+item.mp3+'"><source src="'+item.ogg+'">').appendTo('#player');

        //$('.cover').html('<img src="'+item.cover+'" alt="'+item.album+'">');
        //$('.tag').html('<strong>'+item.title+'</strong><span class="artist">'+item.artist+'</span><span class="album">'+item.album+'</span>');
        //$('#playlist li').removeClass('playing').eq(i).addClass('playing');
        audio = newaudio[0];
        audio.volume = $('.mute').hasClass('enable') ? 0 : volume;
        audio.addEventListener('progress', beforeLoad, false);
        audio.addEventListener('durationchange', beforeLoad, false);
        audio.addEventListener('canplay', afterLoad, false);
        audio.addEventListener('ended', ended, false);
    }
    
    $('.playback').on('click', function(){
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