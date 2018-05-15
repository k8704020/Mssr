function login(){
    alert("抱歉你尚未登入!");
}
function first_test(){
    alert("你為第一次進行測驗");
}
function latest_qu(qu){
	if(qu!= -1){
		alert("繼續上次做的題目");
	}
}
function end(){
    alert("你已經完成這個測驗了!");
	window.parent.location = "../../../ac/index.php";
}
function begin(){
	alert("測驗正式開始嚕!!");
}

function noTest(){
	alert("你的考試次數用完了哦!!");
    window.parent.location = "../../../ac/index.php";
}

function get_Test(){
	alert("你還有一次考試的次數");
}

function get_Test(){
	alert("你還有一次考試的次數");
}