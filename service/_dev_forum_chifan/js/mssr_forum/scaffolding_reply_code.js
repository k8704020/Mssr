//-------------------------------------------------------
//函式: scaffolding_reply_code
//用途: 回覆鷹架
//-------------------------------------------------------

        function scaffolding_reply_text(cat_id){
			var text1;
			var text2;
			var input_content;
		
		
			switch(cat_id) {
				//小說類(學)
				case 11:
					text1="我覺得你說的很好，但我還想補充……";
					text2="在……的部分，我跟你的想法不一樣，因為……";
					break;
				case 12:
					text1="我很同意你說……的部分，我也有類似……的經驗";
					text2="你說的……的部分，讓我想到……，因為……";
					break;
				case 13:
					text1="我跟你有同樣的感受，因為……";
					text2="我跟你的感受不一樣，我覺得心情很……，因為……";
					break;
				case 14:
					text1="我認同你在……的看法，因為……";
					text2="我不認同你……的看法，我覺得作者想表達的是……";
					break;
					
					
				//小說類(問)
				case 21:
					text1="我認為這個故事想表達……，因為……";
					text2="因為……角色要……，所以……";
					break;
				case 22:
					text1="我看過……的書籍，裡面提到……，跟這本書很類似";
					text2="我覺得你說的很好，但我最喜歡……的部分，因為……";
					break;
				
				
				//非小說類(學)
				case 31:
					text1="我還想補充……的知識，因為……";
					text2="我跟你想的不一樣，我覺得這本書的重點在說……的概念，因為……";
					break;
				case 32:
					text1="我覺得你在……的部分說得很好，但我還想補充……的知識，因為……";
					text2="我看過……的書，也是在說有關……的知識";
					break;
				case 33:
					text1="我不認同你的……的看法，因為……";
					text2="我認同你在……的看法，因為……";
					break;
				
				
				//非小說類(問)
				case 41:
					text1="我覺得書中提到的……，應該是……";
					text2="我很同意你……的想法，但我還想補充……";
					break;
				case 42:
					text1="我有看過……的書，裡面也說到……的相關知識，主要再說……";
					text2="書中提到……的概念，讓我聯想到……，因為……";
					break;
			}
			

			
			document.getElementById('p1').innerHTML = text1;
			document.getElementById('p2').innerHTML = text2;
			document.getElementById('r1').value= "r"+cat_id+"a";
			document.getElementById('r2').value= 'r'+cat_id+'b';
			

		
		}
		
		
	


        function article_input_text(id){
			var text;
			
		
			switch(id) {
			
				case 'r1':
					text = document.getElementById('p1').innerHTML;
					break;
					
				case 'r2':
					text = document.getElementById('p2').innerHTML;
					break;
					
					
				case 'r0':
					
					document.getElementById("mssr_comment_input_content").innerHTML = '<textarea name="mssr_comment_input_name_content[]" cols="50" rows="8" style="resize:none;"></textarea><BR>';
					return false;
					break;
					
					
					
			}
			
		
			
			
			
			if(text!=""){
			
				var scaffolding_text = text.split('……');
				var input_content ='';
				
				for(var i=0;i<scaffolding_text.length;i++){
						
						if((i==scaffolding_text.length-1)&&(scaffolding_text[i]!="")){
						
							input_content +='<textarea name="mssr_comment_input_name_content[]" cols="40" rows="1" style="border:0;resize:none;" readonly>'+scaffolding_text[i]+'</textarea><BR>';
							break;
							
						}else if(scaffolding_text[i]!=""){
					
							input_content +='<textarea name="mssr_comment_input_name_content[]" cols="40" rows="1" style="border:0; resize:none;" readonly>'+scaffolding_text[i]+'</textarea><BR>';
							input_content +='<textarea name="mssr_comment_input_name_content[]" cols="40" rows="2" style="resize:none;"  placeholder="……"></textarea><BR>';

						}
					
					
				}
				
				document.getElementById("mssr_comment_input_content").innerHTML = input_content;
			}else{
				alert("請重新選擇欲輸入的內容");
			}
			
			


			
        }

      

	
//滑鼠移入
function mouse_over(obj){
	obj.style.cursor='pointer';
}














