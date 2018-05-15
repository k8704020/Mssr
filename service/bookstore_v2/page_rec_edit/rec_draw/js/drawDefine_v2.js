
var drawColor;
var drawWidth;
var canvas;
var context;
var drawMode;
var undoStack;
var isSaved;

var $canvasOffset;
var $rootOffset;
var $lwshow;
var $colors;
var $dbutton;
var $csCanvas;
//var $papers;
var $dbutton;

var arriveTime = new Date().getTime();
var deltaTime = 0;
var isModify = false;

var color_array = ['FF0000', 'FF4500', 'FFBB66', 'FFFF00', 'BBFF00', '006400', '00FFFF', '0088A8', '0000FF', '4B0082', '9900FF', 'FF0088'];

$(function () {
    canvas = document.getElementById('sketchpad');
    context = canvas.getContext('2d');
    $canvasOffset = $(canvas).offset(); //get screen offset
    $rootOffset = $("html").offset();
    
	//create color panel
    for (var i = 0; i < 12; i++) { 
        $('#cp_container').append("<div class=\"jc oc\" id=\"#%a\" style=\"background-color: #%a;\"><img src=\"img/mask.png\"></div>".replace(/%a/g, color_array[i]));
		//$('#papers').append("<div class='pc' style=\"background-color: #%a;\"></div>".replace(/%a/g, color_array[i]));
    }
	$('#cp_container').append("<div class=\"jc oc\" id=\"#FFFFFF\" style=\"background-color: black;\"><img src=\"img/mask.png\"></div>");
	$('#cp_container').append("<div class=\"spc oc\" id=\"spc\" style=\"background-color: white;\"><img src=\"img/mask.png\"></div>");
    $colors = $("div.jc");
    $lwshow = $("#lw_show div"); //line width
    $dbutton = $(".dbutton"); //draw button
    $csCanvas = $('#cStamp'); //
	$dbutton = $(".dbutton"); 
	checkLogin();
    
	init();
});

function init(){	
	
	drawColor = 'black';
    drawWidth = 5;
	isSaved = true;
	//drawMode = 'pen';    
	echo("繪圖:init:uid:"+uid+">>> book_id:"+book_id);
    modeChange('pen');
   /* var nonSaved = window.localStorage.getItem("draw"+storyId+"story"+drawPage+'data');
	var bc = window.localStorage.getItem("draw"+storyId+"story"+drawPage+'bc');
	if(nonSaved !== null){
		//load Img from localhost
		updatePanel(nonSaved,bc);
		undoStack = new Array(nonSaved);
		saveImg(nonSaved,bc,drawPage,uid,book_id);
		window.localStorage.removeItem("draw"+storyId+"story"+drawPage+'data');
		window.localStorage.removeItem("draw"+storyId+"story"+drawPage+'bc');
	}
	else{*/
		//load Img from server
	//window.alert(uid);
		
		if(uid!=0)loadImg(drawPage,uid,book_id);
        $('div#d'+drawPage).addClass("pactive");
	//}
	
	//canvas for paperColor
	var img = new Image();	
    img.onload = function () {
      $("#paperColor")[0].getContext("2d").drawImage(img, 0, 0);
    };
    img.src = "img/color_picker.png";
}
function return_book(){
	return book_id+"_"+uid;
}
var tpkptk = 1;
function saveImg(idata,bc,page,uid,book_id,fun,value,state){	
	
		// parent.set_hide_rec_page_all_btn(true);隱藏按鈕甚麼的!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	if(tpkptk !=1)return false;
	tpkptk = 0;
	time_lock = false;
	cover("存檔中，請稍後");
 	window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e16',window.parent.parent.action_on);
	time_lock = false;
    //window.localStorage.setItem("draw"+storyId+"story"+drawPage+'data',idata);
    //window.localStorage.setItem("draw"+storyId+"story"+drawPage+'bc',bc);
	echo("繪圖:資料上傳中");
	$.post("ajax.php", {
        preAjax: 1,
		saveImg: 1,
		//filename: filename,
		imgData: idata,
		bcColor: bc,
        page: page,
		unique_id: uid,
		book_id: book_id,
        page_num: page_num,
		time: time,
		auth_coin_open:auth_coin_open
	}).success(function (data) {
		
		if(data[0]!="{")
		{
			cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",1);
			echo("AJAX:success:set_rec_star():儲存繪圖資料:資料庫發生問題");
			return false;
		}
		
		data_array = JSON.parse(data);
		echo("AJAX:success:set_rec_star():儲存繪圖資料:已讀出:"+data);
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
			echo(fun+"繪圖:AJAX:回傳:"+data);
			var obj =  JSON.parse(data);
			setTimeout(function(){
			//回傳並增加金錢
			if(fun!= null)
				{
						if(data_array["text"]!="")cover(data_array["text"],1);
						if(data_array["coin"]>0)window.parent.parent.set_coin(data_array["coin"]);
						if(data_array["exp_score"]>0)window.parent.parent.set_score_exp(data_array["exp_score"]);
						window.parent.save_lock = 0;
						window.document.getElementById("save_btn").style.display = "none";
						fun(value,state);
						
					
				}
				else
				{
					  
						cover("");
						
						window.parent.save_lock = 0;
						window.document.getElementById("save_btn").style.display = "none";
						if(data_array["text"]!="")cover(data_array["text"],1);
						if(data_array["coin"]>0)window.parent.parent.set_coin(data_array["coin"]);
						if(data_array["exp_score"]>0)window.parent.parent.set_score_exp(data_array["exp_score"]);
					
				}
			}, 1500); 
			
			//window.localStorage.removeItem("draw"+storyId+"story"+drawPage+'data');
			//window.localStorage.removeItem("draw"+storyId+"story"+drawPage+'bc');
		}
				
		
	}).error(function(e){
		if(e.status === 404){
			
			cover("存檔失敗，<br>請確認網路連線是否正常，<br>連上網路後再點選存檔",1);
		}
		else if(e.status === 0){
			cover("存檔失敗，<br>請確認網路連線是否正常，<br>連上網路後再點選存檔",1);	
		}
	}).complete(function(e){
		isSaved = true;
		tpkptk = 1;
	});
}

//action of color changed
function colorChange() {
    drawColor = $('div.cactive').css("background-color");
    $lwshow.css("background-color", drawColor);
}

function clearPage() {
    context.clearRect (0, 0, $(canvas).width(), $(canvas).height());
}

function loadImg(page,uid,book_id) {
	
    $.post("ajax.php", {
        preAjax: 1,
        loadImg: 1,
		unique_id: uid,
		book_id: book_id,
		//filename: filename
        page: page
    }).success(function (data) {
        //console.dir(data);
		cover("");
		
        var obj = $.parseJSON(data);
        if (obj.status === 'load0') {
            updatePanel(obj.data,obj.bc);
            
			var obj = $.parseJSON(data);
			//$("#lwShow").attr("src",obj.data).css("background-color",obj.bc);
            undoStack = new Array(obj.data);
        } else if (obj.status === 'load1') {
            clearPage();
            $("#sketchpad").css("background-color","white");
            undoStack = new Array(canvas.toDataURL());
        } else {
          
            undoStack = new Array(canvas.toDataURL());
        }
    }).complete(function(){
        $('#undo').children("img").css("background-position", "0 -30px");
    });
}

function modeChange (mode) {
    //console.error(drawMode);
    if(drawMode != mode){
        $dbutton.animate({
            "padding-top": "20px"
        }, 100);
        $("#"+mode).animate({
            "padding-top": "0px"
        }, 100);
    }
    if (mode == 'pen') {
        context.globalCompositeOperation = "source-over";
        $lwshow.css("background-image", "none");
        drawMode = mode;
    } else if (mode == 'eraser') {
        context.globalCompositeOperation = "destination-out";
        $lwshow.css("background-image", "url(img/era.png)");
        drawMode = mode;
    }
}

function updatePanel(data,bc) {
	context.globalCompositeOperation = "copy";
    var img = new Image();
	
    img.onload = function () {
        context.drawImage(img, 0, 0);
		$(canvas).css("background-color",bc);
		modeChange(drawMode);
    };
    img.src = data;
}

function getCookie(c_name)
{
    if (document.cookie.length>0){
    var c_list = document.cookie.split("\; ");
            for ( i in c_list ){
            var cook = c_list[i].split("=");
            //console.dir(cook);
            if ( cook[0] === c_name ){
                //console.dir(cook);
                return unescape(cook[1]);
            }
        } 
    }
    return null;
}
 
function checkLogin(){

}