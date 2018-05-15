//-------------------------------------------------------
//函式: scaffolding()
//用途: 鷹架
//-------------------------------------------------------
	//小說類(學)
	//11
	var s11a = '這本書的情節在說……';
	var s11b = '這是……的故事，主角是……，一開始……，後來……';
	var s11c = '這本書的情節背景是……';
	//12
	var s12a = '這本書提到……，讓我想到……';
	var s12b = '這本書故事講的……角色，讓我想到……';
	var s12c = '我以前發生……的情況，跟書裡的情節很類似';
	//13
	var s13a = '我現在心情很……，因為……(情節、角色)';
	var s13b = '看完這本書的結局，我感覺很……，因為……';
	var s13c = '我對這本書……的部分(情節、角色)很感動，因為……';
	//14
	var s14a = '這本書的情節內容，在……寫得很(好/不好)，因為……';
	var s14b = '我覺得這本書的情節沒有特色，因為……';
	var s14c = '我很喜歡(或不喜歡)……角色，因為……';
	
	//小說類(問)
	//21
	var s21a = '我對書中情節提到……的地方不太明白，有人知道嗎？';
	var s21b = '為什麼……(角色)要……這樣，有人知道嗎？';
	var s21c = '為什麼結局最後會……，有人知道嗎？';
	//22
	var s22a = '這本書情節提到……，有什麼相關的書籍嗎？';
	var s22b = '我最喜歡書中……的角色，因為……，大家最喜歡哪個角色呢？';
	var s22c = '我最喜歡書中……的情節內容，因為……，大家最喜歡哪個部分呢？';
	
	//非小說類(學)
	//31
	var s31a = '這本書在說跟……有關的知識，例如……';
	var s31b = '我覺得這本書的重點概念是……，因為……';
	var s31c = '這本書提到……的知識，重點是……';
	//32
	var s32a = '這本書講到……的內容，讓我想到……';
	var s32b = '我看過其他……的書，也有類似……的內容';
	var s32c = '我以前也有……的情況，跟書中……的內容一樣';
	//33
	var s33a = '書中提到……的內容，跟我想法不一樣，因為……';
	var s33b = '我覺得這本書在……的內容，寫的(好/不好)，因為……';
	var s33c = '這本書在……的內容，寫的不清楚，應該補充……';
	
	//非小說類(問)
	//21
	var s41a = '我對書中提到……的知識不太明白，因為……，有人知道嗎？';
	var s41b = '為什麼書中提到……會這樣，有人知道嗎？';
	var s41c = '關於書本所提到……的知識，我覺得是……，有沒有人想法跟我一樣啊？';
	//22
	var s42a = '我想對……的相關知識多了解一點，有什麼推薦的書籍嗎？';
	var s42b = '看完這本書後，我學到了……的知識，大家有學到什麼嗎？';
	var s42c = '書中……的概念，在你的生活中有經歷過嗎？為什麼？';
		
		function mssr_article_input_box_1_F(){
			$.blockUI({
					message: $('#mssr_article_input_box_1_F'),

					css:{
						top:  ($(window).height() - 400) /2 + 'px',
						left: ($(window).width() - 600) /2 + 'px',
						textAlign:	'left',
						width: '600'

					}
			});
		}
		
		function mssr_article_input_box_1_NF(){
			$.blockUI({
					message: $('#mssr_article_input_box_1_NF'),

					css:{
						top:  ($(window).height() - 400) /2 + 'px',
						left: ($(window).width() - 600) /2 + 'px',
						textAlign:	'left',
						width: '600'

					}
			});
		}
		
		function choose_article_type(){
			$.blockUI({
					message: $('#choose_article_type'),

					css:{
						top:  ($(window).height() - 400) /2 + 'px',
						left: ($(window).width() - 600) /2 + 'px',
						textAlign:	'left',
						width: '600'

					}
			});
		}
		
		function mssr_article_input_box_3(){
			$.blockUI({
					message: $('#mssr_article_input_box_3'),

					css:{
						top:  ($(window).height() - 400) /2 + 'px',
						left: ($(window).width() - 600) /2 + 'px',
						textAlign:	'left',
						width: '600'

					}
			});
		}
		
		function mssr_article_input_box_4(){
			$.blockUI({
					message: $('#mssr_article_input_box_4'),

					css:{
						top:  ($(window).height() - 400) /2 + 'px',
						left: ($(window).width() - 750) /2 + 'px',
						textAlign:	'left',
						width: '750'

					}
			});
		}
		
		function mssr_article_input_box_5(){
			$.blockUI({
					message: $('#mssr_article_input_box_5'),

					css:{
						top:  ($(window).height() - 400) /2 + 'px',
						left: ($(window).width() - 600) /2 + 'px',
						textAlign:	'left',
						width: '600'

					}
			});
		}
		
		function close_blockui(){
			$.unblockUI();
			return false;
		}
		
		
	
	
		//發表文章 填寫文章內容block-ui
		function input_article()	{
			
			//取得選擇的書籍book_sid

			var select_book_sid =  $('input[name=select_book_sid]:checked').val();
			var select_book_ch_no	= $('input[name=select_book_sid]:checked').attr("data-book_ch_no");
			select_book_ch_no = select_book_ch_no.substr(0,1);
			
			if((select_book_sid=="")||(select_book_sid==undefined)){
				alert('請選擇書籍');
				return false;
			}

			//若有則給定input_article_book_sid的值
			document.getElementById('input_article_book_sid').value = select_book_sid;
			
			
			if(select_book_ch_no=='0'){
				choose_article_type();
			}else if(select_book_ch_no=='8'){
				mssr_article_input_box_1_F();
			}else{
				mssr_article_input_box_1_NF();
			}




		}
		
		
		function choose_type(type){
			var type = trim(type);
			if(type=='F'){
				mssr_article_input_box_1_F();
			}else{
				mssr_article_input_box_1_NF();
			}	
		}
	
	
	
		function input_step_2(){
			//先抓上一步驟的選組的值
			var step1_choose = $('input[name=scaffolding_step1_choose]:checked').val();
			step1_choose = trim(step1_choose);
			scaffolding_step2(step1_choose);
			mssr_article_input_box_3();

			
			
		}
		
		function input_step_3(){
			//先抓上一步驟的選組的值
			var step2_choose = $('input[name=scaffolding_step2_choose]:checked').val();
			step2_choose = trim(step2_choose);
			scaffolding_step3(step2_choose);
			mssr_article_input_box_4()
			
		
			
		}
		
		function input_step_4(){
			//先抓上一步驟的選組的值
			var step3_choose = $('input[name=scaffolding_step3_choose]:checked').val();
			step3_choose = trim(step3_choose);
			article_input_text(step3_choose);
			mssr_article_input_box_5();
		}
		
		
		

		function scaffolding_step2(type){
			
			var scaffolding_type;
			
			
			switch(type) {
			
				case 'FL':
					scaffolding_type ='<div class="radio"><label><input type="radio" name="scaffolding_step2_choose" id="optionsRadios1" value="11">你覺得這個故事在說什麼？</label></div>';	
					scaffolding_type +='<div class="radio"><label><input type="radio" name="scaffolding_step2_choose" id="optionsRadios1" value="12">你有跟故事有類似的經驗嗎？</label></div>';	
					scaffolding_type +='<div class="radio"><label><input type="radio" name="scaffolding_step2_choose" id="optionsRadios1" value="13">你看完這個故事的感覺是什麼？</label></div>';	
					scaffolding_type +='<div class="radio"><label><input type="radio" name="scaffolding_step2_choose" id="optionsRadios1" value="14">你對這個故事內容有什麼看法？</label></div>';	
					break;
					
				case 'FA':
					scaffolding_type ='<div class="radio"><label><input type="radio" name="scaffolding_step2_choose" id="optionsRadios1" value="21">看完這本書後，對情節中有什麼不了解？</label></div>';	
					scaffolding_type +='<div class="radio"><label><input type="radio" name="scaffolding_step2_choose" id="optionsRadios1" value="22">看完這本書後，還想知道什麼？</label></div>';	
					break;
					
				case 'NFL':
					scaffolding_type ='<div class="radio"><label><input type="radio" name="scaffolding_step2_choose" id="optionsRadios1" value="31">你覺得這本書內容的重點是什麼？</label></div>';	
					scaffolding_type +='<div class="radio"><label><input type="radio" name="scaffolding_step2_choose" id="optionsRadios1" value="32">你有書中相關的知識想要分享嗎？</label></div>';	
					scaffolding_type +='<div class="radio"><label><input type="radio" name="scaffolding_step2_choose" id="optionsRadios1" value="33">關於書中的內容你有什麼想要評論？</label></div>';	
					break;
					
				case 'NFA':
					scaffolding_type ='<div class="radio"><label><input type="radio" name="scaffolding_step2_choose" id="optionsRadios1" value="41">看完這本書後，對內容中有什麼不了解？</label></div>';						
					scaffolding_type +='<div class="radio"><label><input type="radio" name="scaffolding_step2_choose" id="optionsRadios1" value="42">看完這本書後，還想知道什麼？</label></div>';	
					break;

			
			}
			
			document.getElementById("scaffolding_step2_content").innerHTML = scaffolding_type;
			
			
			
		}
		
		
		
		function scaffolding_step3(step2_choose){
			
			var scaffolding_text;
			
			
			
			switch(step2_choose) {
			//小說類 「學」鷹架
				case '11':
					scaffolding_text = '<div class="radio" onclick="article_input_text(111)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="11a">'+s11a+'</label></div>';	
					scaffolding_text += '<div class="radio" onclick="article_input_text(112)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="11b">'+s11b+'</label></div>';
					scaffolding_text += '<div class="radio" onclick="article_input_text(113)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="11c">'+s11c+'</label></div>';
					break;

				case '12':
					scaffolding_text = '<div class="radio" onclick="article_input_text(121)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="12a">'+s12a+'</label></div>';	
					scaffolding_text += '<div class="radio" onclick="article_input_text(122)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="12b">'+s12b+'</label></div>';
					scaffolding_text += '<div class="radio" onclick="article_input_text(123)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="12c">'+s12c+'</label></div>';
					break;

				case '13':
					
					scaffolding_text = '<div class="radio" onclick="article_input_text(131)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="13a">'+s13a+'</label></div>';	
					scaffolding_text += '<div class="radio" onclick="article_input_text(132)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="13b">'+s13b+'</label></div>';
					scaffolding_text += '<div class="radio" onclick="article_input_text(133)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="13c">'+s13c+'</label></div>';
					break;

				case '14':
					scaffolding_text = '<div class="radio" onclick="article_input_text(141)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="14a">'+s14a+'</label></div>';	
					scaffolding_text += '<div class="radio" onclick="article_input_text(142)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="14b">'+s14b+'</label></div>';
					scaffolding_text += '<div class="radio" onclick="article_input_text(143)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="14c">'+s14c+'</label></div>';
					break;
					
			//小說類 「問」鷹架
				case '21':
					scaffolding_text = '<div class="radio" onclick="article_input_text(211)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="21a">'+s21a+'</label></div>';	
					scaffolding_text += '<div class="radio" onclick="article_input_text(212)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="21b">'+s21b+'</label></div>';
					scaffolding_text += '<div class="radio" onclick="article_input_text(213)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="21c">'+s21c+'</label></div>';
					break;
					
				case '22':
					scaffolding_text = '<div class="radio" onclick="article_input_text(221)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="22a">'+s22a+'</label></div>';	
					scaffolding_text += '<div class="radio" onclick="article_input_text(222)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="22b">'+s22b+'</label></div>';
					scaffolding_text += '<div class="radio" onclick="article_input_text(223)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="22c">'+s22c+'</label></div>';
					break;
					
			//非小說類 「學」鷹架
				case '31':
					scaffolding_text = '<div class="radio" onclick="article_input_text(311)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="31a">'+s31a+'</label></div>';	
					scaffolding_text += '<div class="radio" onclick="article_input_text(312)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="31b">'+s31b+'</label></div>';
					scaffolding_text += '<div class="radio" onclick="article_input_text(313)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="31c">'+s31c+'</label></div>';
					break;
					
				case '32':
					scaffolding_text = '<div class="radio" onclick="article_input_text(321)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="32a">'+s32a+'</label></div>';	
					scaffolding_text += '<div class="radio" onclick="article_input_text(322)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="32b">'+s32b+'</label></div>';
					scaffolding_text += '<div class="radio" onclick="article_input_text(323)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="32c">'+s32c+'</label></div>';
					break;
					
				case '33':
					scaffolding_text = '<div class="radio" onclick="article_input_text(331)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="33a">'+s33a+'</label></div>';	
					scaffolding_text += '<div class="radio" onclick="article_input_text(332)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="33b">'+s33b+'</label></div>';
					scaffolding_text += '<div class="radio" onclick="article_input_text(333)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="33c">'+s33c+'</label></div>';
					break;
					
			//非小說類 「問」鷹架
				
				case '41':
					scaffolding_text = '<div class="radio" onclick="article_input_text(411)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="41a">'+s41a+'</label></div>';	
					scaffolding_text += '<div class="radio" onclick="article_input_text(412)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="41b">'+s41b+'</label></div>';
					scaffolding_text += '<div class="radio" onclick="article_input_text(413)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="41c">'+s41c+'</label></div>';
					break;
					
				case '42':
					scaffolding_text = '<div class="radio" onclick="article_input_text(421)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="42a">'+s42a+'</label></div>';	
					scaffolding_text += '<div class="radio" onclick="article_input_text(422)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="42b">'+s42b+'</label></div>';
					scaffolding_text += '<div class="radio" onclick="article_input_text(423)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="42c">'+s42c+'</label></div>';
					break;
			
			} 
				scaffolding_text +='<div class="radio" onclick="article_input_text(0)"><label><input type="radio" name="scaffolding_step3_choose" id="optionsRadios1" value="0">其他</label></div>';
				document.getElementById("scaffolding_step3_content").innerHTML = scaffolding_text;
		}
		
		
		
	


        function article_input_text(step3_choose){
			alert("OK");
			
			var type		= $('input[name=scaffolding_step2_choose]:checked').val();
			var refer_code 	= $('input[name=scaffolding_step3_choose]:checked').val();
		
			
			document.getElementById('select_input_type').value= type;
			document.getElementById('article_refer_code').value= refer_code;
		
		
			var step3_choose = trim(step3_choose);
			var text;
			
			switch(step3_choose) {
				case '111':
					text = s11a;
					break;
				case '112':
					text = s11b;
					break;
				case '113':
					text = s11c;
					break;
					
				case '121':
					text = s12a;
					break;
				case '122':
					text = s12b;
					break;
				case '123':
					text = s12c;
					break;
				
				case '131':
					text = s13a;
					break;
				case '132':
					text = s13b;
					break;
				case '133':
					text = s13c;
					break;
					
				case '141':
					text = s14a;
					break;
				case '142':
					text = s14b;
					break;
				case '143':
					text = s14c;
					break;
				
				case '211':
					text = s21a;
					break;
				case '212':
					text = s21b;
					break;
				case '213':
					text = s21c;
					break;
				
				case '221':
					text = s22a;
					break;
				case '222':
					text = s22b;
					break;
				case '223':
					text = s22c;
					break;
				
				case '311':
					text = s31a;
					break;
				case '312':
					text = s31b;
					break;
				case '313':
					text = s31c;
					break;
				
				case '321':
					text = s32a;
					break;
				case '322':
					text = s32b;
					break;
				case '323':
					text = s32c;
					break;
				
				case '331':
					text = s33a;
					break;
				case '332':
					text = s33b;
					break;
				case '333':
					text = s33c;
					break;
				
				case '411':
					text = s41a;
					break;
				case '412':
					text = s41b;
					break;
				case '413':
					text = s41c;
					break;
				
				case '421':
					text = s42a;
					break;
				case '422':
					text = s42b;
					break;
				case '423':
					text = s42c;
					break;
					
				case '0':
					document.getElementById("scaffolding_step4_content").innerHTML = '<textarea name="mssr_input_box_name_content[]" cols="40" rows="8" style="resize:none;"></textarea><BR>';
					return false;
					break;
			}
			
			
			
			
		

			
			if(text!=""){
			document.getElementById("scaffolding_step4_content").innerHTML = '<textarea name="mssr_input_box_name_content[]" cols="40" rows="8" style="resize:none;">'+text+'</textarea><BR>';

			}else{
				alert("請回上一步，重新選擇例句");
			}
			
        }

      

	
//滑鼠移入
function mouse_over(obj){
	obj.style.cursor='pointer';
}














