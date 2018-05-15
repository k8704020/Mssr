
$(function () {
    $('a.handle').click(function(){
        if ($("#dtool").hasClass('open')) {
            $("#dtool").animate({
                top: "490px"
            }).removeClass("open");
                $csCanvas.fadeOut("fast");
			window.parent.document.getElementById("bb-nav-out").style.display = "block";
			if(rec_draw_score)window.parent.document.getElementById("rec_star_comment").style.display = "block";
			if(rec_draw_score)window.parent.document.getElementById("rec_star_badge").style.display = "block";

        } else {
            $("#dtool").animate({
                top: "339px"
            }).addClass("open");
			window.parent.document.getElementById("bb-nav-out").style.display = "none";
			if(rec_draw_score)window.parent.document.getElementById("rec_star_comment").style.display = "none";
			if(rec_draw_score)window.parent.document.getElementById("rec_star_badge").style.display = "none";
        }
    });
    
    var dtoolLock = false;
    var timeoutHandle;
    $("#dtool").mouseleave(function(){
        if(!dtoolLock){
            timeoutHandle = setTimeout(function(){
                $("#dtool").animate({
                    top: "490px"
                }).removeClass("open");
                $csCanvas.fadeOut("fast");
				window.parent.document.getElementById("bb-nav-out").style.display = "block";
				if(rec_draw_score)window.parent.document.getElementById("rec_star_comment").style.display = "block";
				if(rec_draw_score)window.parent.document.getElementById("rec_star_badge").style.display = "block";
            },1000);
        }
    });
    $("#dtool").mouseenter(function(){
        if(timeoutHandle){
            clearTimeout(timeoutHandle);
        }
    });
    $("#toolLock").click(function(){
        dtoolLock = !dtoolLock;
        if(dtoolLock){
            //$(this).html("Yes");
            $(this).css({"background-position":"0 0",
                "border-color":"red"});
        }else{
            //$(this).html("No");
            $(this).css({"background-position":"0 -32px",
                "border-color":"white"});
        }
    });
    

    //linewidth slide bar
    $("#lw_slider").slider({
        range: "min",
        value: 5,
        min: 2,
        max: 70,
        slide: function (event, ui) {
            $lwshow.css({
                'width': ui.value,
                'height': ui.value,
                'margin-top': (100 - ui.value) / 2
            });
            drawWidth = $lwshow.css("width").replace("px", "");
        }
    });
    $("#lw_show div").css({
        'width': 5,
        'height': 5,
        'margin-top': 45
    });
    $(".ui-slider .ui-slider-handle").css({
        'width': '30px',
        'height': '30px',
        'top': '-10px'
    });



    $colors.click(function () { 
        if ($(this).hasClass("cactive")) {
            var ctx = $csCanvas[0].getContext("2d");
            var grd = ctx.createLinearGradient(0, 0, 182, 0);
            grd.addColorStop(0, "#FFFFFF");
            grd.addColorStop(1, $(this).attr("id"));
            ctx.fillStyle = grd;
            ctx.fillRect(0, 0, 182, 182);

            var grd = ctx.createLinearGradient(0, 0, 0, 182);
            grd.addColorStop(0, "transparent");
            grd.addColorStop(1, "black");
            ctx.fillStyle = grd;
            ctx.fillRect(0, 0, 182, 182);
            $csCanvas.fadeIn("fast");
         }
		$("div.oc").removeClass("cactive");
        $(this).addClass("cactive");
		
        colorChange();
        modeChange('pen'); 

    });
   
	$("div.spc").click(function () {
        if ($(this).hasClass("cactive")) {
            var ctx = $csCanvas[0].getContext("2d");
            var img = new Image();	
			img.onload = function () {
				ctx.drawImage(img, 0, 0, 182, 182);
				$csCanvas.fadeIn("fast");
			};
			img.src = "img/color_picker.png";
        }
        $("div.oc").removeClass("cactive");
        $(this).addClass("cactive");
        colorChange();
        modeChange('pen');
    });
	
	$("div#home").click(function(){
		/*if(!isSaved){
			$.ajaxSetup({async:false}); 
			saveImg(canvas.toDataURL(),$(canvas).css("background-color"),drawPage);
			//window.event.returnValue = "0";
			//return "You will lose unsave data.";			
		}
		document.location.href = "storyShell.php";
		//console.dir(123);*/
	});

    //create sketchpad functions
    context.lineJoin = "round";
    context.lineCap = "round";
    var what_now_type="";
	var what_now_x="";
	var what_now_y="";
	//create a drawer which tracks touch movements
    //for pad touch event
    var toucher = {
        isDrawing: false,
        touchstart: function (coors) {
            $(document).click();
            context.beginPath();
            context.strokeStyle = drawColor;
            context.lineWidth = drawWidth;
            context.moveTo(coors.x, coors.y);
			
            this.isDrawing = true;
        isSaved = false;
        isModify = true;
        set_rec_on_edit(true);
        },
        touchmove: function (coors) {
            if (this.isDrawing) {
                context.lineTo(coors.x, coors.y);
                context.stroke();
            }
        },
        touchend: function () {
            if (this.isDrawing) {
                //this.touchmove(coors);
                this.isDrawing = false;
                undoStack.push(canvas.toDataURL()); //saveAction to undo list	
                $("#undo").children("img").css("background-position", "0 0"); //set undo icon			
            }
        }
    };

    //create a function to pass touch events and coordinates to drawer
    function touch(event) {
	 
	 //判斷單點
	 if(event.type=="touchstart")
	 {
		what_now_type="point";
	 }
	 if(event.type=="touchmove")
	 {
		what_now_type="";
	 }
	 
	 	if(event.type!="touchend"){ 
			// get the touch coordinates
			var coors = {
				x: (event.targetTouches[0].clientX - $canvasOffset.left),
				y: (event.targetTouches[0].clientY - $canvasOffset.top)
			};
			
			what_now_x=coors.x;
			what_now_y=coors.y;
			// pass the coordinates to the appropriate handler
			toucher[event.type](coors);
			//$("title").html(event.type);
		}
		else{
			toucher[event.type]();
		
		}
    }

    // attach the touchstart, touchmove, touchend event listeners.
	

    //prevent elastic scrolling
    document.body.addEventListener('touchmove', function (event) {
        event.preventDefault();
    }, false); // end body:touchmove
    //for PC mouse event
    var drawer = {
        isDrawing: false,
        oldX: 0,
        oldY: 0,
		
        mousedown: function (coors) {
			/////////////////////////////////////parent.set_rec_on_edit(true,"draw");//開啟計時器2133333333333333333333333333333333333335222222222222221431431431431431431431431431431431431431431431431431431431431431431431431431431431431431
		  	set_rec_on_edit(true);
		    $(document).click();
            context.beginPath();
			echo(coors.x+"__mousedown_"+coors.y);
            context.moveTo(coors.x, coors.y);
            context.strokeStyle = drawColor;
            context.lineWidth = drawWidth;

            this.oldX = coors.x;
            this.oldY = coors.y;
            this.isDrawing = true;
        isSaved = false;
        isModify = true;
        },
        mousemove: function (coors) {
            if (this.isDrawing) {
                context.lineTo(coors.x, coors.y);
                context.stroke();
            }
        },
        mouseup: function (coors) {
            if (this.isDrawing) {
                //this.touchmove(coors);  //touch only
                this.isDrawing = false;
                
                if(coors.x == this.oldX && coors.y == this.oldY){
                    context.beginPath();
                    context.moveTo(this.oldX,this.oldY);     
                    context.arc(coors.x,coors.y,drawWidth/2,0,Math.PI*2,false);
                    context.closePath();
                    context.fillStyle = drawColor;
                    context.fill();
                    //console.log(this.oldX+"test"+this.oldY);
                }
                $("#undo").children("img").css("background-position", "0 0"); //set undo icon	
                undoStack.push(canvas.toDataURL()); //saveAction to undo list
                
                //max undo step 30
                while(undoStack.length > 30){
                    undoStack.shift();
                }
            }
        }      
    };

    function draw(ev) {
		
		
		
        var coors = {
            x: ev.clientX - $rootOffset.left - $canvasOffset.left,
            y: ev.clientY - $rootOffset.top - $canvasOffset.top
        };
		if(what_now_type == "point")
		{
			coors.x = what_now_x;
			coors.y = what_now_y;
		}
		if(ev.type=="mouseup")
		{
			what_now_type = "";	
		}
		echo(ev.type);
        drawer[ev.type](coors);
    }
	
    canvas.addEventListener('mousedown', draw, false);
    document.addEventListener('mousemove', draw, false);
    document.addEventListener('mouseup', draw, false);
    //canvas.addEventListener('mouseout', draw, false);
	canvas.addEventListener('MSPointerDown', touch, false);
    document.addEventListener("MSPointerMove", touch, false);
    document.addEventListener('MSPointerUp',   touch, false);
	
    canvas.addEventListener('touchstart', touch, false);
    document.addEventListener('touchmove', touch, false);
    document.addEventListener('touchend', touch, false);


    document.body.addEventListener('mousemove', function (event) {
        event.preventDefault();
    }, false); // end body:touchmove
    
    //effect of selection of tools
    $dbutton.click(function () {
        modeChange($(this).attr("id"));
    }).first().click();

    //create color stamp obj
    $csCanvas.css({
        "width": "182px",
        "height": "182px",
        "position": "absolute",
        "top": "-183px",
        "right": "160px",
        //"float" : "left",
        "display": "none",
        "z-index": "999"
    }).mousemove(function (e) {
        var csOffset = $csCanvas.offset();
        var canvasX = Math.floor(e.pageX - csOffset.left);
        var canvasY = Math.floor(e.pageY - csOffset.top);

        var imageData = $(this)[0].getContext('2d').getImageData(canvasX, canvasY, 1, 1);
        var pixel = imageData.data;
        var pixelColor = "rgba(" + pixel[0] + ", " + pixel[1] + ", " + pixel[2] + ", " + pixel[3] + ")";
        $('div.cactive').css('backgroundColor', pixelColor);
        $lwshow.css('backgroundColor', pixelColor);
    }).click(function (e) {
        var csOffset = $csCanvas.offset();
        var canvasX = Math.floor(e.pageX - csOffset.left);
        var canvasY = Math.floor(e.pageY - csOffset.top);

        var imageData = $(this)[0].getContext('2d').getImageData(canvasX, canvasY, 1, 1);
        var pixel = imageData.data;
        var pixelColor = "rgba(" + pixel[0] + ", " + pixel[1] + ", " + pixel[2] + ", " + pixel[3] + ")";
        $('div.cactive').css('backgroundColor', pixelColor);
        $(this).css("display", "none");
        //$(this).fadeOut("fast");
        colorChange();
    }).mouseout(function(){
        $csCanvas.fadeOut("fast");
		colorChange();
    });
	window.document.getElementById("cStamp").addEventListener("touchstart",cs_touchStart,false);
	function cs_touchStart(event)
	{
		/*var csOffset = $("#paperColor").offset();
        var canvasX = Math.floor(event.targetTouches[0].clientX - csOffset.left - 1); //-1 for bordered
        var canvasY = Math.floor(event.targetTouches[0].clientY - csOffset.top - 1); //-1 for bordered

        var imageData = $(this)[0].getContext('2d').getImageData(canvasX, canvasY, 1, 1);
        var pixel = imageData.data;
        var pixelColor = "rgba(" + pixel[0] + ", " + pixel[1] + ", " + pixel[2] + ", " + pixel[3] + ")";
        $('#sketchpad').css('backgroundColor', pixelColor);
        $(this).css("display", "none"); 
		isSaved = false;
		isModify = true;*/
		 var csOffset = $csCanvas.offset();
        var canvasX = Math.floor(event.targetTouches[0].clientX - csOffset.left);
        var canvasY = Math.floor(event.targetTouches[0].clientY - csOffset.top);

        var imageData = $(this)[0].getContext('2d').getImageData(canvasX, canvasY, 1, 1);
        var pixel = imageData.data;
        var pixelColor = "rgba(" + pixel[0] + ", " + pixel[1] + ", " + pixel[2] + ", " + pixel[3] + ")";
        $('div.cactive').css('backgroundColor', pixelColor);
        $(this).css("display", "none");
        //$(this).fadeOut("fast");
        colorChange();
		
		
	}


    //function of undo
    $("#undo").click(function () {
        if (undoStack.length != 1) {			
			
            undoStack.pop();
            var data = undoStack.pop();
			
            updatePanel(data);
			
            undoStack.push(data); //save the moment of undo
        isSaved = false;		
        isModify = true;        
        }
        if (undoStack.length === 1) {
            $(this).children("img").css("background-position", "0 -30px");
        }
		
    });

    $("#undo img").css({
        "background-image": "url(img/undos.png)",
        "background-size": "30px 60px",
        "background-repeat": "no-repeat",
        "background-position": "0 -30px"
    });

    $("div#saveImg").click(function () {
       
		saveImg(canvas.toDataURL(),$(canvas).css("background-color"),drawPage,uid,book_id);
		isSaved = true;
    });
	
    $("#newPage").click(function () {		
        
        $("<div title=\"清空畫面\">這麼做會清空畫面<br />確定要這麼做嗎?</div>").dialog({            
            height: 200,
            width: 350,
            resizable: false,
            modal: true,
            buttons: {
                "確定": function() {
                
                    clearPage();  
                    undoStack.push(canvas.toDataURL()); //saveAction to undo list
                    $("#undo").children("img").css("background-position", "0 0"); //set undo icon
                    isSaved = false; 
                    isModify = true;

                    $( this ).dialog( "close" );
                },
                "取消": function() {

                    $( this ).dialog( "close" );
                }
            }
        });
    });
	
	/*window.onbeforeunload = function(e){
    $.ajaxSetup({async:false}); 
		if(!isSaved){	     
			saveImg(canvas.toDataURL(),$(canvas).css("background-color"),drawPage,uid,book_id);
			//window.event.returnValue = "0";
			//return "You will lose unsave data.";			
		}		
    if(isModify){
      var nowTime = new Date().getTime();
      deltaTime = nowTime - arriveTime;
      //console.dir(deltaTime);      
      $.post("ajax.php", {
          logDraw: 1,
          sid: storyId,
          page_num: page_num,
          interval: deltaTime,
		  time: time,
		  auth_coin_open:auth_coin_open
		  
      }).success(function(data){
        console.dir(data);
      });
    }
	}*/
    
    $("#toWrite").click(function(e){
        
      /*  if(!isSaved){
			$.ajaxSetup({async:false}); 
			saveImg(canvas.toDataURL(),$(canvas).css("background-color"),drawPage);
			//window.event.returnValue = "0";
			//return "You will lose unsave data.";			
		}
        document.location.href = "storyWrite.php?page=" + drawPage;*/
    });
	
	$("#chBcColor").click(function(e){
		$("#paperColor").slideToggle("fast");
		set_rec_on_edit(true);
		
	});
	
	$("#paperColor").css({
        "position": "absolute",
        "top": "40px",
        "left": "-150px",
        //"float" : "left",
        "display": "none",
        "z-index": "999"
    }).mousemove(function (e) {
        var csOffset = $("#paperColor").offset();
        var canvasX = Math.floor(e.pageX - csOffset.left - 1); //-1 for bordered
        var canvasY = Math.floor(e.pageY - csOffset.top - 1); //-1 for bordered
        var imageData = $(this)[0].getContext('2d').getImageData(canvasX, canvasY, 1, 1);
        var pixel = imageData.data;
        var pixelColor = "rgba(" + pixel[0] + ", " + pixel[1] + ", " + pixel[2] + ", " + pixel[3] + ")";
        $('#sketchpad').css('backgroundColor', pixelColor);	
    }).mousedown(function (e) {
	
        var csOffset = $("#paperColor").offset();
        var canvasX = Math.floor(e.pageX - csOffset.left - 1); //-1 for bordered
        var canvasY = Math.floor(e.pageY - csOffset.top - 1); //-1 for bordered

        var imageData = $(this)[0].getContext('2d').getImageData(canvasX, canvasY, 1, 1);
        var pixel = imageData.data;
        var pixelColor = "rgba(" + pixel[0] + ", " + pixel[1] + ", " + pixel[2] + ", " + pixel[3] + ")";
        $('#sketchpad').css('backgroundColor', pixelColor);
        $(this).css("display", "none"); 
		isSaved = false;
    	isModify = true;
		
    });
	window.document.getElementById("paperColor").addEventListener("touchstart",paperColor_touchStart,false);
	function paperColor_touchStart(event)
	{
		var csOffset = $("#paperColor").offset();
        var canvasX = Math.floor(event.targetTouches[0].clientX - csOffset.left - 1); //-1 for bordered
        var canvasY = Math.floor(event.targetTouches[0].clientY - csOffset.top - 1); //-1 for bordered

        var imageData = $(this)[0].getContext('2d').getImageData(canvasX, canvasY, 1, 1);
        var pixel = imageData.data;
        var pixelColor = "rgba(" + pixel[0] + ", " + pixel[1] + ", " + pixel[2] + ", " + pixel[3] + ")";
        $('#sketchpad').css('backgroundColor', pixelColor);
        $(this).css("display", "none"); 
		isSaved = false;
		isModify = true;
	}
    
    $("div.switch").click(function(){
         $.blockUI({
            message: '<h1>讀取中，請稍候...</h1>',  
            css: { 
                border: 'none', 
                padding: '15px', 
                backgroundColor: '#000', 
                '-webkit-border-radius': '10px', 
                '-moz-border-radius': '10px', 
                opacity: .5,
                color: '#fff'}
        });  
        //setTimeout($.unblockUI, 5000); 
        
        //save and load
        if(!isSaved){
            $.ajaxSetup({async:false}); 
            saveImg(canvas.toDataURL(),$(canvas).css("background-color"),drawPage,uid,book_id);
            $.ajaxSetup({async:true}); 
            isSaved = true;
        }
    
        setTimeout(function(){
            $.unblockUI();
            $(".blockUI").fadeOut("slow");
        }, 3000); 
        drawPage = $(this).html();
        init(); 
        
        $("div.switch").removeClass("pactive");
        $(this).addClass("pactive");
    });
    window.onscroll = function(e){
        $rootOffset = $("html").offset();
    }
    
    $(window).resize(function(){
        $canvasOffset = $(canvas).offset();
    });
    
    $("#addPic").click(function(){
        $("#upload").click();
    });

    $('#upload').on('change', function() {
        //retrieve selected uploaded files data
        var files = $(this)[0].files;
        readFile(files[0]);
        //console.dir(123);
        return false;
    });
    
    var readFile = function(file) {
        if( (/image/i).test(file.type) ) {
            //define FileReader object
            var reader = new FileReader();
            
            
            //init reader onload event handlers
            reader.onload = function(e) {	
                
                var img = new Image();
                
                img.onload = function () {
                    context.drawImage(img, 0, 0);
                    $("#undo").children("img").css("background-position", "0 0");
                    undoStack.push(canvas.toDataURL());
                    isSaved = false; 
                    isModify = true;
                };
                img.src = e.target.result;                
            };
            
            //begin reader read operation
            
            reader.readAsDataURL(file);
            
            
        } else {
            //some message for wrong file format
            console.dir('*Selected file format not supported!');
        }
    }
});