
var keyLock;
var keyReset;
var secKey = 0;

var arriveTime = new Date().getTime();
var deltaTime = 0;
var isSaved = true;
var isModify = false;

$(function () {
    $("input[type=text]").keyup(function (e) {
        isSaved = false;
        isModify = true;
    });
    
    //word count function
    $("textarea").keyup(function (e) {
        wordCount(this);
        isSaved = false;
        isModify = true;
    });
    
    function lockKeys(){
        keyLock = setTimeout(function(){
            clearTimeout(keyLock);
            keyLock = null;
            secKey = 0;
            $.unblockUI();
            $(".blockUI").fadeOut("slow");
        },3000);
    }
    
    //reset key count every second
    function resetKeys(){
        keyReset = setTimeout(function(){
            secKey = 0;
            clearTimeout(keyReset);
            keyReset = null;
            resetKeys();
        },1000);
    }
    resetKeys();
    
    //lock key control
    $("textarea").keydown(function (e) {
        if(e.keyCode ==  8 || e.keyCode == 46 || //delete backspace up down left right
           e.keyCode == 37 || e.keyCode == 38 || //will not be prevent
           e.keyCode == 39 || e.keyCode == 40){
            return;
        }else if(e.keyCode == 9 || window.event.ctrlKey){ //tab ctrl prevented
            e.preventDefault();  
        }else{
            if(keyLock || secKey > 8){
                e.preventDefault();
                if(!keyLock){
                    $.blockUI({
                        message: '<h1>不可以亂打喔!想清楚再打~</h1>',  
                        css: { 
                            border: 'none', 
                            padding: '15px', 
                            backgroundColor: '#000', 
                            '-webkit-border-radius': '10px', 
                            '-moz-border-radius': '10px', 
                            opacity: .5, 
                            color: '#fff'}
                    }); 
                    lockKeys();
                }
            }
            secKey += 1;      
        }          
    });
    
    $("div#home").click(function () {        
        if(!isSaved){
            $.ajaxSetup({
                async: false
            });
            $("div#save").click();
        }
        document.location.href = "storyShell.php?fromtext=1&story=" + sid;
    });

    $("div#save").click(function () {
        $.blockUI({
            message: '<h1>存檔中，請稍候...</h1>',  
            css: { 
                border: 'none', 
                padding: '15px', 
                backgroundColor: '#000', 
                '-webkit-border-radius': '10px', 
                '-moz-border-radius': '10px', 
                opacity: .5, 
                color: '#fff'}
        }); 
        window.localStorage.setItem("write"+sid+"story"+nowSection,$('textarea').val());
        $.post("ajax.php", {
            //preAjax: 1,
            SaveStory: 1,
            page: nowSection,
            content: $('textarea').val().trim(),
            sid: sid,
            page_num: page_num
        }).success(function (data) {
            //console.log(data);
            //var obj = $.parseJSON(data);
            //console.log(obj);
            //alert(obj.messege);
            window.localStorage.removeItem("write"+sid+"story"+nowSection);
            setTimeout(function(){
                $.unblockUI();
                $(".blockUI").fadeOut("slow");
            }, 3000); 
            isSaved = true;
        }).error(function (e) {
            if(e.status === 404){
                //alert(404);
                $.blockUI({ 
                    message: $('<div>您的網路已經斷線，請確認網路是否連線後再行儲存</div>'), 
                        fadeIn: 700, 
                        fadeOut: 700, 
                        timeout: 10000, 
                        showOverlay: false, 
                        centerY: false, 
                        css: { 
                            width: '350px', 
                            top: '10px', 
                            left: '', 
                            right: '10px', 
                            border: 'none', 
                            padding: '5px', 
                            backgroundColor: '#000', 
                            '-webkit-border-radius': '10px', 
                            '-moz-border-radius': '10px', 
                            opacity: .6, 
                            color: '#fff',
                            'text-align': "left",
                            'font-size': "25px"                         
                        } 
                });
                //window.localStorage.setItem("write"+sid+"story"+nowSection,$('textarea').val());
            }
            else if(e.status === 0){
                //alert('save error: no Internet.');
                $.blockUI({ 
                    message: $('<div>您的網路已經斷線，請確認網路是否連線後再行儲存</div>'), 
                        fadeIn: 700, 
                        fadeOut: 700, 
                        timeout: 10000, 
                        showOverlay: false, 
                        centerY: false, 
                        css: { 
                            width: '350px', 
                            top: '10px', 
                            left: '', 
                            right: '10px', 
                            border: 'none', 
                            padding: '5px', 
                            backgroundColor: '#000', 
                            '-webkit-border-radius': '10px', 
                            '-moz-border-radius': '10px', 
                            opacity: .6, 
                            color: '#fff',
                            'text-align': "left",
                            'font-size': "25px"                         
                        } 
                });
                //window.localStorage.setItem("write"+sid+"story"+nowSection,$('textarea').val());
            }
        }).complete(function (e) {
        	setTimeout(function(){
                $.unblockUI();
                $(".blockUI").fadeOut("slow");
            }, 3000); 
            isSaved = true;
        });
    });
    
    $("div#toDraw").click(function () {
        
        if(!isSaved){
            $.ajaxSetup({
                async: false
            });
            $("div#save").click();
        }
        //var page = parseInt(document.location.hash.substring(2),10);
        document.location.href = 'storyDraw.php?page=' + nowSection;
    });
    
    window.onbeforeunload = function(e){   
      $.ajaxSetup({async: false});
      if(!isSaved){          
          $("div#save").click();
      }
      if(isModify){
        var nowTime = new Date().getTime();
        deltaTime = nowTime - arriveTime;
        //console.dir(deltaTime);      
        $.post("ajax.php", {
          logWrite: 1,
          sid: sid,
          page_num: page_num,
          interval: deltaTime
        }).success(function(data){
          console.dir(data);
        });
      }
    
	}
   
    //check whether there are unsaved data
    var nonSaved = window.localStorage.getItem("write"+sid+"story"+nowSection);
    if(nonSaved !== null){       
        $('textarea').val(nonSaved);
		window.localStorage.removeItem("write"+sid+"story"+nowSection);
        $("div#save").click();
	}
    
    
    wordCount($('textarea')[0]);
    
    document.oncontextmenu=function(){
        return false;
    }
    document.body.addEventListener('touchmove', function (event) {
        event.preventDefault();
    }, false); // end body:touchmove
    $("textarea")[0].ondrop = function(e){
        e.preventDefault();  
    }
});

String.prototype.trim = function(){
    return this.replace(/(^[\s]*)|([\s]*$)/g, "");
}

function wordCount(textObj) {
    var count = textObj.value.split(/[ \n]/).join("").length;
    $("#wordCount").html("字數統計: " + count);
}

function inputComma(btn) {
    var obj = $('textarea')[0];
    //var obj = btn.form.text1;
    if (document.selection) //for ie 
    {
        obj.focus();
        var sel = document.selection.createRange();
        sel.text = btn.value;
    } else //for firefox 
    {
        var prefix = obj.value.substring(0, obj.selectionStart);
        var suffix = obj.value.substring(obj.selectionEnd);
        obj.value = prefix + btn.value + suffix;
        obj.selectionEnd = obj.selectionStart; 
        obj.selectionStart += 1;
    }
    isSaved = false;
    isModify = true;
    wordCount(obj);
}