	//------------------------------------------
	//自己的任務>已完成
	//------------------------------------------	
	function finish_mission(x,y,w){//x:任務編號; y:任務主題; w:傳遞人uid	
		var send_data = {"task_id":parseInt(x),"deliver_uid":parseInt(w)};
	
			$.ajax({
			"url":"finish_mission.php",
			"data":send_data,
			"type":"POST",
			"dataType":"json",
			"async": false,//改為同步執行，照正常邏輯執行
			"beforeSend": function(){},
			"success": function(data){
				console.log(data);//檢查用
				article_title 	  = data["article_title"];
				article_id 	      = data["article_id"];
				article_like_cno  = data["like_cno"];
				article_reply_cno = data["reply_cno"];
				total_score		  = data["total_score"];
				end_time          = data["finish_time"];
				step_content	  = data["step_content"];			
			},
			"error": function(data){
				//console.log(data);
				alert("任務已完成 Error");	
			}	
		});	
		$("#mission_content").empty();
		var add ="";
			add += "<table class='TB' style='width:80%;margin-top:7%;'>";
			add += "<tr><td colspan='3'><div id='div-alert'class='font-MJ' style='color:red;font-size:16px;'><img src='img/mission_icon/back.png'>&nbsp;<a href='#' id='back_achieve_total'>[返回任務總覽頁面]</a></div></td></tr>";
			add += "<tr>";
			add += "<td rowspan='7' style='width:140px;background-Color:#CCEEFF;'><div class='circle' style='top:20%'><div class='inner' >任務已完成</div></div></td>";
			add += "<td colspan='2' class='mission_step1' style='background-Color:#FFFFBB;color:black;'><img src='img/mission_icon/topic.png'>&nbsp;&nbsp;主題：『"+y+"』</td>";
			add += "</tr>";
			add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/article.png'>&nbsp;&nbsp;文章標題：「"+article_title+"」&nbsp;─&nbsp;點此前往&nbsp;[<a href='../_dev_group_mission.php?get_from=3&group_task_id="+x+"&article_id="+article_id+"' target='_blank'>文章討論頁面</a>]</td></tr>";
			add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/arrow1.png'>&nbsp;&nbsp;本次活動文章的「按讚』與「回覆」狀況：<td></tr>";
 			add += "<tr><td colspan='2' class='mission_step3' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/one.png'>&nbsp;&nbsp;"+step_content[1]+"[<span style='color:red'>"+article_like_cno+"</span>]個<td></tr>";
			add += "<tr><td colspan='2' class='mission_step3' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/two.png'>&nbsp;&nbsp;"+step_content[2]+"[<span style='color:red'>"+article_reply_cno+"</span>]個 </td></tr>";
			add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/money.png'>&nbsp;&nbsp;任務總獲得獎勵：<span style='color:red'>"+total_score+"</span> 分</td></tr>";
			add += "<tr>";
			add += "<td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/time.png'>&nbsp;&nbsp;任務完成時間："+end_time+""; 
			add += "</tr>";
			add += "</table>";
			$("#mission_content").append(add);	
			//-----------------------------------------
			//返回任務總覽
			//-----------------------------------------
			$("#back_achieve_total").click(function(){//返回成就總覽按鈕
				window.location.assign('#main_page');
				location.reload();
			});				
			
	}
	//------------------------------------------
	//自己的任務>正在進行
	//------------------------------------------	
	function doing_mission(x,y,w){//x:任務編號; y:任務主題; w:傳遞人uid
		
		var send_data = {"task_id":parseInt(x),"deliver_uid":parseInt(w)};
		$.ajax({
			"url":"getvalue_mission_step.php",
			"data":send_data,
			"type":"POST",
			"dataType":"json",
			"async": false,//改為同步執行，照正常邏輯執行
			"beforeSend": function(){},
			"success": function(data){
				console.log(data);//檢查用
				var step_number     = data["step_number"];
				var end_time 	    = data["end_time"];
				var step_content    = data["step_content"];
				var score_content   = data["score_content"];
								
				//-----------------------------------------
				if(step_number == 0){//進行第一個任務
				//-----------------------------------------
					/* if(Date.parse(new Date()).valueOf() > Date.parse(end_time).valueOf()){//時間到後，即放棄任務
						alert("時間到還尚未發表文章，已拒絕此任務！");
						submit_timeout_mission(step_number,x,y,w);
						//javaScript:window.location.reload();
					}else{ */  					
					
						var step_score = data["step_score"];
						var uid 	   = data["uid"];
						$("#mission_content").empty();
						
						var add ="";
						add += "<table class='TB' style='width:80%;margin-top:3%;'>";
						add += "<tr>";
						add += "<td rowspan='2' style='background-Color:#FFFFFF;border-bottom-style:dashed;border-top-style:dashed;border-left-style:dashed;'><img src='img/mission_icon/write.png'></td>";
						add += "<td style='background-Color:#CCFF99;color:black;border-right-style:dashed;border-top-style:dashed;'>第一步</td>";
						add += "<td rowspan='2' style='background-Color:#E6E6FA'><img src='img/mission_icon/arrow.png'></td>";
						add += "<td>第二步</td>";
						add += "<td rowspan='2' style='background-Color:#E6E6FA'><img src='img/mission_icon/arrow.png'></td>";
						add += "<td>第三步</td>";
						add += "</tr>";
						add += "<tr>";
						add += "<td style='background-Color:#FFFFBB;border-bottom-style:dashed;border-right-style:dashed;'>閱讀書籍、發表文章</td>";
						add += "<td>討論串取得按讚、回覆</td>";
						add += "<td>將任務傳遞給3位朋友</td>";
						add += "</tr>";
						add += "</table>";					
						
						add += "<table class='TB' style='width:80%;margin-top:3%;'>";
						add += "<tr><td colspan='3'><div id='div-alert'class='font-MJ' style='color:red;font-size:16px;'><img src='img/mission_icon/back.png'>&nbsp;<a href='#' id='back_achieve_total'>[返回任務總覽頁面]</a></div></td></tr>";
						add += "<tr>";
						add += "<td rowspan='6' style='width:140px;background-Color:#CCEEFF;'><div class='circle'><div class='inner'>第一步</div></div></td>";
						add += "<td colspan='2' class='mission_step1' style='background-Color:#FFFFBB;color:black;'><img src='img/mission_icon/topic.png'>&nbsp;&nbsp;主題：『"+y+"』</td>";
						add += "</tr>";
						add += "<tr><td colspan='2' class='mission_step2' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/one.png'>&nbsp;&nbsp;"+step_content+"</td></tr>";
						add += "<tr><td colspan='2' class='mission_step2' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/two.png'>&nbsp;&nbsp;若沒有相關的書籍，可以請求好友推薦書籍&nbsp;─&nbsp;點此前往&nbsp;[<a href='../user.php?user_id="+uid+"&tab=6' target='_blank'>請求推薦</a>]<td></tr>";
						add += "<tr><td colspan='2' class='mission_step2' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/three.png'>&nbsp;&nbsp;發表這本書的心得&nbsp;─&nbsp;點此前往&nbsp;[<a href='../_dev_group_mission.php?get_from=3&group_task_id="+x+"&tab=2' target='_blank'>發表文章</a>]</td></tr>";
						add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;background-Color:#FFFFBB'><img src='img/mission_icon/money.png'>&nbsp;&nbsp;完成可獲得獎勵："+step_score+" 分</td></tr>";
						add += "<tr>";
						add += "<td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;background-Color:#FFFFBB'><img src='img/mission_icon/time.png'>&nbsp;&nbsp;任務截止時間："+end_time+"<div class='m-button' style='float:right;margin-right:20px'><span name='submit'>提交任務</span></div></td>";
						add += "</tr>";
						add += "</table>";
						$("#mission_content").append(add);
						//alert(new Date().toDateString());
					/* } */
				//-----------------------------------------	
				}else if(step_number == 1){//進行第二個任務
				//-----------------------------------------	
					/* if(Date.parse(new Date()).valueOf() > Date.parse(end_time).valueOf()){//時間到後，自動提交任務
						alert("時間到自動提交，進入到下個步驟！");						
						submit_timeout_mission(step_number,x,y,w);
						//javaScript:window.location.reload();						
					}else{ 	 */				
				
						var article_like_cno   = data["like_cno"];
						var article_reply_cno  = data["article_reply_cno"];
						var dicussion_cno      = data["dicussion_cno"];
						var expect_score       = data["expect_score"];
						var article_title      = data["article_title"];
						var article_id         = data["article_id"];
						
						$("#mission_content").empty();
						var add ="";
						add += "<table class='TB' style='width:80%;margin-top:3%;'>";
						add += "<tr>";
						add += "<td rowspan='2' style='background-Color:#E6E6FA'><img src='img/mission_icon/arrow.png'></td>";
						add += "<td>第一步</td>";
						add += "<td rowspan='2' style='background-Color:#FFFFFF;border-bottom-style:dashed;border-top-style:dashed;border-left-style:dashed;'><img src='img/mission_icon/discuss_talk.png'></td>";
						add += "<td style='background-Color:#CCFF99;color:black;border-right-style:dashed;border-top-style:dashed;'>第二步</td>";
						add += "<td rowspan='2' style='background-Color:#E6E6FA'><img src='img/mission_icon/arrow.png'></td>";
						add += "<td>第三步</td>";
						add += "</tr>";
						add += "<tr>";
						add += "<td>閱讀書籍、發表文章</td>";
						add += "<td style='background-Color:#FFFFBB;border-bottom-style:dashed;border-right-style:dashed;'>討論串取得按讚、回覆</td>";
						add += "<td >將任務傳遞給3位朋友</td>";
						add += "</tr>";
						add += "</table>";
						
						add += "<table class='TB' style='width:80%;margin-top:3%;'>";
						add += "<tr><td colspan='3'><div id='div-alert'class='font-MJ' style='color:red;font-size:16px;'><img src='img/mission_icon/back.png'>&nbsp;<a href='#' id='back_achieve_total'>[返回任務總覽頁面]</a></div></td></tr>";
						add += "<tr>";
						add += "<td rowspan='8' style='width:140px;background-Color:#CCEEFF;'><div class='circle'><div class='inner'>第二步</div></div></td>";
						add += "<td colspan='2' class='mission_step1' style='background-Color:#FFFFBB;color:black;'><img src='img/mission_icon/topic.png'>&nbsp;&nbsp;主題：『"+y+"』</td>";
						add += "</tr>";
						add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/article.png'>&nbsp;&nbsp;文章標題：「"+article_title+"」&nbsp;─&nbsp;點此前往&nbsp;[<a href='../_dev_group_mission.php?get_from=3&group_task_id="+x+"&article_id="+article_id+"' target='_blank'>文章討論頁面</a>]</td></tr>";
						add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/invite.png'>&nbsp;&nbsp;邀請朋友一同參與討論&nbsp;─&nbsp;點此前往&nbsp;[<a href='../_dev_group_mission.php?get_from=3&group_task_id="+x+"&article_id="+article_id+"&request_article=1' target='_blank'>邀請朋友討論頁面</a>]</td></tr>";
						add += "<tr><td colspan='2' class='mission_step2' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/one.png'>&nbsp;&nbsp;"+step_content[0]+"[<span style='color:red'>"+article_like_cno+"</span>]個 ("+score_content[0]+")<td></tr>";
						add += "<tr><td colspan='2' class='mission_step2' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/two.png'>&nbsp;&nbsp;"+step_content[1]+"[<span style='color:red'>"+article_reply_cno+"</span>]個 ("+score_content[1]+")</td></tr>";
						add += "<tr><td colspan='2' class='mission_step2' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/three.png'>&nbsp;&nbsp;討論串參與人數[<span style='color:red'>"+dicussion_cno+"</span>]人<td></tr>";
						add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;background-Color:#FFFFBB'><img src='img/mission_icon/money.png'>&nbsp;&nbsp;目前累積獎勵："+parseInt(30+expect_score)+" 分(30<span style='color:red'>+"+expect_score+"</span>)</td></tr>";
						add += "<tr>";
						add += "<td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;background-Color:#FFFFBB'><img src='img/mission_icon/time.png'>&nbsp;&nbsp;任務截止時間："+end_time+"<div class='m-button' style='float:right;margin-right:20px'><span name='submit'>提交任務</span></div></td>";
						add += "</tr>";
						add += "</table>";
						$("#mission_content").append(add);
					/* } */
				//-----------------------------------------	
				}else if(step_number == 2){//進行第三個任務
				//-----------------------------------------	
/* 					if(Date.parse(new Date()).valueOf() > Date.parse(mission_finish_time).valueOf()){//時間到後，放棄任務
						alert("已經超過了任務的截止時間！");
						submit_timeout_mission(step_number,x,y,w); 
					}*/						
					var article_title      = data["article_title"];
					var article_id         = data["article_id"];
					var total_score 	   = data["total_score"];
					var mission_finish_time= data["mission_finish_time"];
					var friend_list        = data["friend_list"];
					var get_name           = data["get_name"];
					
					$("#mission_content").empty();
					var add ="";			
					add += "<table class='TB' style='width:80%;margin-top:3%;'>";
					add += "<tr>";
					add += "<td rowspan='2' style='background-Color:#E6E6FA'><img src='img/mission_icon/arrow.png'></td>";
					add += "<td>第一步</td>";
					add += "<td rowspan='2' style='background-Color:#E6E6FA'><img src='img/mission_icon/arrow.png'></td>";
					add += "<td>第二步</td>";
					add += "<td rowspan='2' style='background-Color:#FFFFFF;border-bottom-style:dashed;border-top-style:dashed;border-left-style:dashed;'><img src='img/mission_icon/deliver_mission.png'></td>";
					add += "<td style='background-Color:#CCFF99;color:black;border-right-style:dashed;border-top-style:dashed;'>第三步</td>";
					add += "</tr>";
					add += "<tr>";
					add += "<td>閱讀書籍、發表文章</td>";
					add += "<td>討論串取得按讚、回覆</td>";
					add += "<td style='background-Color:#FFFFBB;border-bottom-style:dashed;border-right-style:dashed;'>將任務傳遞給3位朋友</td>";
					add += "</tr>";
					add += "</table>";
					
					add += "<table class='TB' style='width:80%;margin-top:3%;'>";
					add += "<tr><td colspan='3'><div id='div-alert'class='font-MJ' style='color:red;font-size:16px;'><img src='img/mission_icon/back.png'>&nbsp;<a href='#' id='back_achieve_total'>[返回任務總覽頁面]</a></div></td></tr>";
					add += "<tr>";
					add += "<td rowspan='7' style='width:140px;background-Color:#CCEEFF;'><div class='circle'><div class='inner'>第三步</div></div></td>";
					add += "<td colspan='2' class='mission_step1' style='background-Color:#FFFFBB;color:black;'><img src='img/mission_icon/topic.png'>&nbsp;&nbsp;主題：『"+y+"』</td>";
					add += "</tr>";
					//add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/article.png'>&nbsp;&nbsp;文章標題：「"+article_title+"」&nbsp;─&nbsp;點此前往&nbsp;[<a href='../_dev_group_mission.php?get_from=3&group_task_id="+x+"&article_id="+article_id+"' target='_blank'>文章討論頁面</a>]</td></tr>";
					add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/arrow1.png'>&nbsp;&nbsp;將本次任務傳給「3」位好友們！&nbsp;("+score_content+")</td></tr>";
					add += "<tr><td colspan='2' class='mission_step3' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/one.png'>&nbsp;&nbsp;<ul class='drop-down-menu'><li style='cursor: pointer;'><a id='f1'>選擇您的好友</a><ul id='ul_firend1'></ul></li></ul></td></tr>";
					add += "<tr><td colspan='2' class='mission_step3' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/two.png'>&nbsp;&nbsp;<ul class='drop-down-menu'><li style='cursor: pointer;'><a id='f2'>選擇您的好友</a><ul id='ul_firend2'></ul></li></ul></td></tr>";
					add += "<tr><td colspan='2' class='mission_step3' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/three.png'>&nbsp;&nbsp;<ul class='drop-down-menu'><li style='cursor: pointer;'><a id='f3'>選擇您的好友</a><ul id='ul_firend3'></ul></li></ul></td></tr>";
					add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;background-Color:#FFFFBB'><img src='img/mission_icon/money.png'>&nbsp;&nbsp;目前累積獎勵："+parseInt(30+total_score)+" 分</td></tr>";
					add += "<tr>";
					add += "<td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;background-Color:#FFFFBB'><img src='img/mission_icon/time.png'>&nbsp;&nbsp;任務截止時間："+mission_finish_time+"<div class='m-button' style='float:right;margin-right:20px'><span name='submit'>提交任務</span></div></td>";
					add += "</tr>";
					add += "</table>";
					$("#mission_content").append(add);			
					for(var j=1; j<=3; j++){
						add = "";
						for(var i=0; i<friend_list.length; i++){
							add += "<li style='cursor: pointer;'>";
							add += "<a class='select_friend' id="+j+" name='f"+j+"' value='"+friend_list[i]+"'>"+get_name[i]+"</a>";
							add += "</li>";
						}
						$("#ul_firend"+j).append(add); 
					}			
				//-----------------------------------------
				//存取欲傳遞者的狀況
				//-----------------------------------------					
					var friend_selected =["","",""];
					var friend_confirm  =[];
					$(".select_friend").click(function(){
						var li_friend = $(this).attr('name');
						$("#"+li_friend).text($(this).text());
						var friend_choose = $(this).attr('value');				
						var n = parseInt($(this).attr('id'));
						friend_selected[n-1] = friend_choose;			
						
						var k =0;
						var w =0;
						for(var i=0;i<=2;i++){
							for(var j=2;j>=0;j--){
								if(i!=j){
									if(friend_selected[i]==friend_selected[j]&&friend_selected[i]!=""){
										w =1;										
									}
								}
							}
							if(friend_selected[i]!=friend_selected[j]){
								friend_confirm[k] = friend_selected[i];
								k++;
							}
						}
						if(w==1){alert("選擇到重覆的好友了！");return false;}
						if(friend_confirm.length==0){alert("尚未選擇好友！");return false;}
					})
				//-----------------------------------------	
					
				}
				//-----------------------------------------
				//返回任務總覽
				//-----------------------------------------
					$("#back_achieve_total").click(function(){//返回成就總覽按鈕
						window.location.assign('#main_page');
						location.reload();
					});				
				//-----------------------------------------
				//提交任務事件
				//-----------------------------------------
					$(".m-button").click(function(){//提交任務
						if($(this).find("span").attr("name")=='submit'){
							if(step_number==2){
								//alert(friend_confirm);
								submit_mission(step_number,x,y,w,friend_confirm);
							}else{
								submit_mission(step_number,x,y,w);
							}
						}
					})		
				//-----------------------------------------
			},
			"error": function(data){
				//console.log(data);
				alert("任務進行中error");	
			}	
		});	
		//-----------------------------------------		
	}
	//------------------------------------------
	//主動>提交任務
	//------------------------------------------	
	function submit_mission(i,x,y,w,f){//i:步驟編號; x:任務編號; y:任務主題; w:傳遞人uid; f:確認的朋友名單
	
		var send_data = [];
		if(i!=2){
			send_data = {"step_number":parseInt(i),"task_id":parseInt(x),"deliver_uid":parseInt(w)};		
		}else if(i==2){
			send_data = {"step_number":parseInt(i),"task_id":parseInt(x),"deliver_uid":parseInt(w),"friend_confirm":f};
		}
		
		$.ajax({
			"url":"submit_mission_step.php",
			"data":send_data,
			"type":"POST",
			//"dataType":"json",
			"async": false,//改為同步執行，照正常邏輯執行
			"beforeSend": function(){},
			"success": function(){
				if(i!=2){//還未完成任務
					alert("提交完成，進入下個步驟");
					doing_mission(x,y,w);
				}else if(i==2){//完成任務
					//alert(f);
					alert("完成所有任務，將獲得任務獎勵！");
					finish_mission(x,y,w);
				}
			},
			"error": function(data){
				//console.log(data);
				
				if(i==0){//第一步驟 Error
					$("#div-alert").remove();
					var add ="";
					add +="<div id='div-alert'class='font-MJ' style='margin-top:1%;color:red;font-size:18px'>您尚未發表文章 或者 沒有使用「鷹架」發表文章！</div>";
					$("#mission_content").append(add);
				}else if(i==1){//第二步驟 Error
					$("#div-alert").remove();
					var add ="";
					add += "<div id='div-alert'class='font-MJ' style='margin-top:1%;color:red;font-size:18px'>資料有誤，請洽詢明日星球團隊人員！</div>";
					$("#mission_content").append(add);					
				}
				else if(i==2){//第三步驟 Error
					$("#div-alert").remove();
					var add ="";
					add += "<div id='div-alert'class='font-MJ' style='margin-top:1%;color:red;font-size:18px'>資料有誤，請洽詢明日星球團隊人員！</div>";
					$("#mission_content").append(add);
				}	
			}	
		});						
	}
	//------------------------------------------
	//朋友的任務>正在進行
	//------------------------------------------	
	function doing_friend_mission(x,y,f,fn){//x:任務編號; y:任務主題; f:朋友的uid; fn:朋友的name
		var send_data = {"task_id":parseInt(x),"friend_uid":parseInt(f)};
		$.ajax({
			"url":"getvalue_friend_mission.php",
			"data":send_data,
			"type":"POST",
			"dataType":"json",
			"async": false,//改為同步執行，照正常邏輯執行
			"beforeSend": function(){},
			"success": function(data){
				var article_id    = data["article_id"];
				var article_title = data["article_title"];
				var like_cno      = data["like_cno"];
				var step2_like_score = data["step2_like_score"];
				var article_reply_cno = data["article_reply_cno"];
				var step2_reply_score = data["step2_reply_score"];
				var reply_be_like_cno = data["reply_be_like_cno"];
				var step2_reply_liked_score = data["step2_reply_liked_score"];
				var step_content = data["step_content"];
				var score_content= data["score_content"];
				
				$("#mission_content").empty();	
				var add ="";
				if(article_id!=0){//朋友有發表文章
					add += "<table class='TB' style='width:80%;margin-top:7%;'>";
					add += "<tr><td><div id='div-alert'class='font-MJ' style='color:red;font-size:16px;'><img src='img/mission_icon/back.png'>&nbsp;<a href='#' id='back_achieve_total' >[返回任務總覽頁面]</a></div></td></tr>";
					add += "<tr><td class='mission_step1' style='background-Color:#FFFFBB;color:black;'><img src='img/mission_icon/topic.png'>&nbsp;&nbsp;主題：『"+y+"』</td></tr>";
					add += "<tr><td class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/deliver.png'>&nbsp;&nbsp;任務接受人：「"+fn+"」</td></tr>";
					add += "<td class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/article.png'>&nbsp;&nbsp;文章標題：「"+article_title+"」&nbsp;─&nbsp;點此前往&nbsp;[<a href='../_dev_group_mission.php?get_from=3&group_task_id="+x+"&article_id="+article_id+"' target='_blank'>文章討論頁面</a>]</td>";
					add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/arrow1.png'>&nbsp;&nbsp;您在朋友討論串的貢獻狀況：</td></tr>";
					add +="<tr><td class='mission_step3' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/one.png'>&nbsp;&nbsp;"+step_content[0]+"[<span style='color:red'>"+like_cno+"</span>]個 ("+score_content[0]+")</td></tr>";
					add +="<tr><td class='mission_step3' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/two.png'>&nbsp;&nbsp;"+step_content[1]+"[<span style='color:red'>"+article_reply_cno+"</span>]個 ("+score_content[1]+")</td></tr>";
					add +="<tr><td class='mission_step3' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/three.png'>&nbsp;&nbsp;"+step_content[2]+"[<span style='color:red'>"+reply_be_like_cno+"</span>]個 ("+score_content[2]+")</td></tr>";
					add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/money.png'>&nbsp;&nbsp;您在朋友討論串貢獻的獎勵：<span style='color:red'>"+parseInt(step2_like_score+step2_reply_score+step2_reply_liked_score)+"</span>分 (需等到朋友完成任務後，才可以獲得獎勵)</td></tr>";
					add += "</table>";
					$("#mission_content").append(add);
				}else if(article_id==0){//朋友尚未發表文章
					add = "";
					add += "<table class='TB' style='width:80%;margin-top:7%;'>";
					add += "<tr><td><div id='div-alert'class='font-MJ' style='color:red;font-size:16px;'><img src='img/mission_icon/back.png'>&nbsp;<a href='#' onClick='javascript:history.back(1)'>[返回任務總覽頁面]</a></div></td></tr>";
					add += "<tr><td class='mission_step1' style='background-Color:#FFFFBB;color:black;'><img src='img/mission_icon/topic.png'>&nbsp;&nbsp;主題：『"+y+"』</td></tr>";
					add += "<tr><td class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/deliver.png'>&nbsp;&nbsp;任務接受人：「"+fn+"」</td></tr>";
					add += "<tr><td class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/article.png'>&nbsp;&nbsp;點此前往&nbsp;[<a href='../_dev_group_mission.php?get_from=3&group_task_id="+x+"' target='_blank'>活動專頁</a>]</td><tr>";
					add += "<tr><td class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/information.png'>&nbsp;&nbsp;您的朋友還沒有發表文章！</td></tr>";
					add += "</table>";
					$("#mission_content").append(add);
				}
				//-----------------------------------------
				//返回任務總覽
				//-----------------------------------------
					$("#back_achieve_total").click(function(){//返回成就總覽按鈕
						window.location.assign('#main_page');
						location.reload();
					});				
				//-----------------------------------------
			},
			"error": function(data){
				//console.log(data);
			}	
		});
		

		
	}
	//------------------------------------------
	//朋友的任務>已完成
	//------------------------------------------
	function finish_friend_mission(x,y,f,fn){//x:任務編號; y:任務主題; f:朋友的uid; fn:朋友的name
	
			var send_data = {"task_id":parseInt(x),"friend_uid":parseInt(f)};
			$.ajax({
			"url":"finish_mission.php",
			"data":send_data,
			"type":"POST",
			"dataType":"json",
			"async": false,//改為同步執行，照正常邏輯執行
			"beforeSend": function(){},
			"success": function(data){
				console.log(data);//檢查用
				article_title 	  = data["article_title"];
				article_id 	      = data["article_id"];
				article_like_cno  = data["like_cno"];
				article_reply_cno = data["reply_cno"];
				total_score		  = data["total_score"];
				end_time          = data["finish_time"];
				step_content	  = data["step_content"];			
			},
			"error": function(data){
				//console.log(data);
				alert("任務已完成 Error");	
			}	
		});	
		$("#mission_content").empty();
		var add ="";
			add += "<table class='TB' style='width:80%;margin-top:7%;'>";
			add += "<tr><td colspan='3'><div id='div-alert'class='font-MJ' style='color:red;font-size:16px;'><img src='img/mission_icon/back.png'>&nbsp;<a href='#' onClick='javascript:history.back(1)'>[返回任務總覽頁面]</a></div></td></tr>";
			add += "<tr>";
			add += "<td rowspan='7' style='width:140px;background-Color:#CCEEFF;'><div class='circle' style='top:24%'><div class='inner' >任務已完成</div></div></td>";
			add += "<td colspan='2' class='mission_step1' style='background-Color:#FFFFBB;color:black;'><img src='img/mission_icon/topic.png'>&nbsp;&nbsp;主題：『"+y+"』</td>";
			add += "</tr>";
			add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/deliver.png'>&nbsp;&nbsp;任務接受人：「"+fn+"」<td></tr>";
			add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/article.png'>&nbsp;&nbsp;文章標題：「"+article_title+"」&nbsp;─&nbsp;點此前往&nbsp;[<a href='../_dev_forum_eric/view/_dev_group_mission.php?get_from=3&group_task_id="+x+"&article_id="+article_id+"' target='_blank'>文章討論頁面</a>]</td></tr>";
 			add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/one.png'>&nbsp;&nbsp;"+step_content[1]+"[<span style='color:red'>"+article_like_cno+"</span>]個<td></tr>";
			add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/two.png'>&nbsp;&nbsp;"+step_content[2]+"[<span style='color:red'>"+article_reply_cno+"</span>]個 </td></tr>";
			add += "<tr><td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/money.png'>&nbsp;&nbsp;任務總獲得獎勵：<span style='color:red'>"+total_score+"</span> 分</td></tr>";
			add += "<tr>";
			add += "<td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/time.png'>&nbsp;&nbsp;任務完成時間："+end_time+""; 
			add += "</tr>";
			add += "</table>";
			$("#mission_content").append(add);					
	}
	//------------------------------------------
	//時間到>自動提交任務
	//------------------------------------------
	function submit_timeout_mission(i,x,y,w){//i:步驟編號; x:任務編號; y:任務主題; w:傳遞人uid;
		var send_data = {"step_number":parseInt(i),"task_id":parseInt(x),"deliver_uid":parseInt(w)};
		$.ajax({
			"url":"submit_timeout_mission_step.php",
			"data":send_data,
			"type":"POST",
			//"dataType":"json",
			"async": false,//改為同步執行，照正常邏輯執行
			"beforeSend": function(){},
			"success": function(){
				if(i==0){//第一步驟					
					window.location.assign('#main_page');
					//alert("時間到還尚未發表文章，已拒絕此任務！");
				}else if(i==1){//第二步驟	
					window.location.assign('#another_page');
					doing_mission(x,y,w);
					//alert("時間到自動提交，進入到下個步驟！");
				}/* else if(i==2){//第三步驟
					alert("時間到自動提交，將獲得任務獎勵！");
					finish_mission(x,y,w);
				} */	
			},
			"error": function(){				
			}	
		});			
	}
	//------------------------------------------

	


