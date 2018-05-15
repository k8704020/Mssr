//-------------------------------------------------------
//函式: book_track()
//用途: 檢舉文章

//-------------------------------------------------------
function book_track(action_code, user_id, book_sid, check){

		
		//參數
		var action_code		=trim(action_code);//代號
		var user_id   		=parseInt(user_id);
		var book_sid      	=trim(book_sid);
		var check 			=parseInt(check);
		

		
	
	
		
	
		
		//頁面條件
		var url='mssr_forum_book_favorite_A.php';
		url+='?user_id='+encodeURI(user_id);
		url+='&book_sid='+encodeURI(book_sid);
		
		
		if((user_id===0)){
			alert('無使用者編號，請重新追蹤!');
			return false;
		}
		if((book_sid==='')){
			alert('無書籍編號，請重新追蹤!');
			return false;
		}
		if(check==0){
			alert("已經將此書籍加入追蹤。");
			action_log('inc/add_action_forum_log/code.php',action_code,user_id,0,0,book_sid,0,0,0,0,0,url);
            
		}else{
			alert("已經取消追蹤這本書。");
			action_log('inc/add_action_forum_log/code.php',action_code,user_id,0,0,book_sid,0,0,0,0,0,url);
            
		}
        return true;
	}
	
	
//滑鼠移入
function mouse_over(obj){
	obj.style.cursor='pointer';
}














