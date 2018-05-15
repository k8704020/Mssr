//-------------------------------------------------------
//string
//-------------------------------------------------------
//report()                  addslashes



//-------------------------------------------------------
//函式: report()
//用途: 檢舉文章
//-------------------------------------------------------
function report(type, report_from, report_to, article_id){
	//參數
	var type      		=trim(type);
	var report_from   	=parseInt(report_from);
	var report_to		=parseInt(report_to);
	var article_id		=parseInt(article_id);
	
	if(confirm("您確定要檢舉此篇文章嗎?")){
		if((report_from===0)||(report_to===0)||(article_id===0)){
			alert('動作失敗!');
			return false;
		}
		//頁面條件
		var url='mssr_forum_report_A.php';
		url+='?type='+encodeURI(type);
		url+='&report_from='+encodeURI(report_from);
		url+='&report_to='+encodeURI(report_to);
		url+='&article_id='+encodeURI(article_id);
		
		location.href=url;
	}	
}


//-------------------------------------------------------
//函式: mouse_over()
//用途: 滑鼠移入
//-------------------------------------------------------
function mouse_over(obj){
	obj.style.cursor='pointer';
}












