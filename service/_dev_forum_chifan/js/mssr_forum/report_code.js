
//-------------------------------------------------------
//函式: report()
//用途: 檢舉文章
//-------------------------------------------------------
function report(type, report_from, report_to, id, con_report){

	//參數
	var type      		=trim(type);
	var report_from   	=parseInt(report_from);
	var report_to		=parseInt(report_to);
	var id				=parseInt(id);
	var con_report		=parseInt(con_report);
	
	
	if(confirm("您確定要檢舉此篇文章嗎?")){
		if((report_from===0)||(report_to===0)||(id===0)){
			alert('動作失敗!');
			return false;
		}
		//頁面條件
		var URLs='mssr_forum_report_A.php';

		
		$.ajax({
                url: URLs,
				
                data: "type="+encodeURI(type)+"&report_from="+encodeURI(report_from)
					+"&report_to="+encodeURI(report_to)+"&id="+encodeURI(id),
                type:"GET",
                dataType:'text',

                success: function(msg){
					
					//是否有錯誤訊息
					if(msg=="user_error"){
						alert("使用者不存在, 請重新輸入!");
						return false;
					}else if(msg=="article_error"){
						alert("文章不存在, 請重新輸入！");
						return false;
					}else if(msg=="reply_error"){
						alert("回覆不存在, 請重新輸入！");
						return false;
					}else if(msg=="has_report"){
						alert("你已經按過檢舉！");
						return false;
					}
					
					if(type=="article"){
						$("#mssr_reply_box_reportcnt-" + id).html(con_report+1);
					}else{
						$("#mssr_comment_box_reportcnt-" + id).html(con_report+1);
					}
                   
                },
				
			
                 error:function(xhr, ajaxOptions, thrownError){ 
                    alert(xhr.status); 
                    alert(thrownError); 
                 }
          }); 
		
	}
	return false;
}


//-------------------------------------------------------
//函式: mouse_over()
//用途: 滑鼠移入
//-------------------------------------------------------
function mouse_over(obj){
	obj.style.cursor='pointer';
}












