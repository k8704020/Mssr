
//-------------------------------------------------------
//函式: like()
//用途: 按讚
//-------------------------------------------------------

function like(type,user_id,article_id,con_like){
		//按讚
	

			//參數
			var type      =trim(type);
			var user_id   =parseInt(user_id);
			var article_id=parseInt(article_id);
			var con_like  =parseInt(con_like);
			
			
			if((user_id===0)||(article_id===0)){
				alert('動作失敗!');
				return false;
			}
			
			
			//頁面條件
			var URLs='mssr_forum_like_A.php';
		
			
	 		$.ajax({
                url: URLs,
				
                data: "type="+encodeURI(type)+"&user_id="+encodeURI(user_id)+"&article_id="+encodeURI(article_id),
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
					}else if(msg=="has_like_article"){
						alert("此發文你已經按過讚！");
						return false;
					}else if(msg=="has_like_reply"){
						alert("此回覆你已經按過讚！");
						return false;
					}
					
				
					
					//判斷是否已經按過讚
					if(type=="article"){
						$("#mssr_reply_box_likecnt-" + article_id).html(con_like+1);
					}else{
						$("#mssr_comment_box_likecnt-" + article_id).html(con_like+1);
					}
					
                   
                },

                 error:function(xhr, ajaxOptions, thrownError){ 
                    alert(xhr.status); 
                    alert(thrownError); 
                 }
            }); 
			
			return false;
		}

//-------------------------------------------------------
//函式: mouse_over()
//用途: 滑鼠移入
//-------------------------------------------------------
function mouse_over(obj){
	obj.style.cursor='pointer';
}












