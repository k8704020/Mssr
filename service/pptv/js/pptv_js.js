time = false;
mp3=document.getElementById("mp3");
button=document.getElementById("left");
click=0;
function count(){
    click++;
    setTimeout("count();",333);
}
function play(){
    click=0;
    if(!time){
        setTimeout("count();",333);
    }
    time = true;
    mp3.play();
}
function go(ans,qu){
	
    var arr = new Array(5);
    arr[0] = 4;
    arr[1] = 2;
    arr[2] = 3;
    arr[3] = 4;
    arr[4] = 1;
    if( click>=(mp3.duration) ){
		if(confirm("你選擇的是("+ans+")?")){
			if(qu>125 && arr[qu-126] == ans){
				alert("恭喜答對了!!");
				right.answer.value = ans;
                
				right.submit();
                
			}else if(qu <=125){
				right.answer.value = ans;                              
				right.submit();
                
                
			}else{
				alert("答案好像怪怪的喔 要不要再想想呢?");
			}
		}
    }
}