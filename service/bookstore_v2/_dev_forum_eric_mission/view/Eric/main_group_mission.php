<html>
<head>

	<meta charset="utf-8">
	<link rel="stylesheet" href="css/common.css" />
	<script src="js/jquery-2.1.1.min.js"></script>
	
	<!--進度條套件-->
	<!--<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>-->
	<script type="text/javascript" src="js/jquery-ui.js"></script>
	<link href="css/jquery-ui.css" rel="stylesheet" />	
	
	<!--Include JQM and JQ-->
	<link rel="stylesheet" href="css/themes/jqmfb.min.css" />
	<link rel="stylesheet" href="css/jquery.mobile.structure.min.css" />
	<script src="js/jquery.animate-enhanced.min.js"></script>
	
	<link rel="stylesheet" type="text/css" href="css/selection.styles.css" />
	
	<!--JQM globals you can edit or remove file entirely... note it needs to be loaded before jquerymobile js -->
	<script src="js/jqm.globals.js"></script>	
	<script src="js/jquery.mobile.min.js"></script> 

	<!--JQM SlideMenu-->
	<link rel="stylesheet" href="css/jqm.slidemenu.css" />
	<script src="js/jqm.slidemenu.js"></script>
	
	<script src="js/group_mission.js"></script>
	<script>
	$(document).ready(function(){		
		
		//var uid =36152;//使用者
		var task_topic   		= [];//存取任務的主題
		var task_id		 		= [];//存取任務的編號
		var task_number  		= 0;//存取任務的數量
		var friend_number		= 0;//存取好友的數量
		var my_friend    		= [];
		var mission_state		= []
		var get_name 	 		= [];//存取自己與好友的名字(0:自己;1~n:朋友)
		var deliver_name 		= [];//存取是傳遞任務的人的名字		
		var deliver_uid  		= [];//存取是傳遞任務的人的uid
		var sex          		= 0;//儲存使用者的性別	
		var article_title		= [];
		var article_id   		= [];
		var article_reply_cno 	= [];
		var like_cno    	 	= [];
		var step_number  		= [];
		var get_potential_score = [];
		var title_next   		= [];
		var title_name   		= [];
		var title_score  		= [];
		var progress_score		= 0;
		var get_book_name       = [];
		var deliver_cno         = [];
		var book_id             = [];
		
		var _showTab = 0; //預設顯示第一個 Tab
		<?php
			if($_GET["group_task_id"]!=0){
			$task = (int)$_GET["group_task_id"] ?>
			_showTab = <?php echo $task-1;?>;
			//alert(_showTab);
		<?php
			}
		?>

	//-----------------------------------------------------------------	
		var send_data = [];		
		$.ajax({//抓取學生的按讚數量，傳給function產生視圖
			"url":"getvalue_mission.php",
			"data":send_data,
			"type":"POST",
			"dataType":"json",
			"async": false,//改為同步執行，照正常邏輯執行
			"beforeSend": function(){},
			"success": function(data){
				console.log(data);//檢查用
				task_id 	 	   = data["group_task_id"];
				task_topic   	   = data["task_topic"];
				task_number  	   = data["task_number"];
				friend_number 	   = data["friend_number"];
				mission_state	   = data["mission_state"];
				get_name           = data["get_name"];
				deliver_name       = data["deliver_name"];
				deliver_uid		   = data["deliver_uid"];
				my_friend 		   = data["my_friend"];
				title_name		   = data["title_name"];
				title_score		   = data["title_score"];
				title_next		   = data["title_next"];
				get_score		   = data["get_score"];
				progress_score     = data["progress_score"];
				like_cno	       = data["like_cno"];
				article_reply_cno  = data["article_reply_cno"];
				article_id         = data["article_id"];
				article_title      = data["article_title"];
				step_number        = data["step_number"];
				sex                = data["sex"];
				get_potential_score= data["get_potential_score"];
				get_book_name      = data["get_book_name"];
				deliver_cno		   = data["deliver_cno"];
				book_id            = data["book_id"];
				show_mission_list();
				none_reply_mission(1);
				show_total_mission();

			},
			"error": function(data){
				//console.log(data);
				alert("error 0");	
			}	
		});
		
		<?php
			if($_GET["has_add_article"]==1){?>
				auto_submit();
		<?php	
			}
		?>
		var reload_time = 0;
		function auto_submit(){
			//alert('test');
			reload_time =1;
			var send_data = {"task_id":<?php echo $_GET["group_task_id"]; ?>};
			$.ajax({
				"url":"auto_submit_mission.php",
				"data":send_data,
				"type":"POST",
				//"dataType":"json",
				"async": false,//改為同步執行，照正常邏輯執行
				"beforeSend": function(){},
				"success": function(){
					window.location.assign('#main_page');					
					if(reload_time==1){//重新整理頁面一次
						window.location.reload();
					}
				},
				"error": function(data){
					alert("error");
				}	
			});				
		}
		//----------------------------------------------------
		//產生推播任務列表
		//----------------------------------------------------
		function show_total_mission(){	
			$("#div-total_mission").empty();
				var add ="";
				add += "<div style='margin-left:2%;margin-right:2%;margin-top:1%;margin-bottom:1%'>";
				add += "<table class='TB'>";
				add += "<tr><td style='background-Color:#FFFFBB;font-size:17px;text-align:center;'><img src='img/mission_icon/list.png'>&nbsp;【推播任務列表】</td></tr>";
				add += "<tr><td><div class='abgne_tab' style='margin:2%;width:95%;'><ul class='tabs'></ul><div class='tab_container' style='height:425px;overflow-x:hidden;overflow-y:auto;background-Color:#FDFDFD'></div></div><td></tr>";
				add += "</table>";
				add += "</div>";
				$("#div-total_mission").append(add);
			for(var x=0; x<task_number; x++){
					var i = 0;//紀錄>第幾樓的任務
					var add ="";
					add += "<li><div id='tab_div_"+x+"' style='display:'><img src='img/mission_icon/align.png' style='float:left;vertical-align:middle;margin-left:3px;margin-top:8px;margin-right:1px'><a href='#tab"+x+"' style='color:black'>"+task_topic[x]+"</a></div></li>";
					$(".tabs").append(add);
					add ="";
					add += "<div id='tab"+x+"' class='tab_content'></div>";
					$(".tab_container").append(add);
				for(var y=0; y<friend_number; y++){
					if(y==0){//自己的任務
						if(mission_state[x][y] != 4){//自己的任務>篩選掉(沒接到的任務)
							i++;
							add ="";
							add += "<div style='margin-top:3%'>";
							add += "<table class='TB' style='width:80%;margin: 1% auto;'>";
							add += "<tr>";
							add += "<td id='ms_"+x+"_"+y+"' colspan='3' class='mission_step' style='background-Color:#FFFFBB;color:black;border-bottom-style:solid;border-bottom-color:#BBBB00;' name="+x+" value="+y+"><img src='img/mission_icon/topic.png'>&nbsp;&nbsp;任務狀態：<span id='span_state_"+x+"_"+y+"' style='color:blue'></span><span style='float:right;margin-right:10px;'>#"+i+"</span>&nbsp;<span id='span_step_"+x+"_"+y+"' style='color:red'></span></td>";
							add += "</tr>";
							add += "<tr>";
							add += "<td id='td_step1_"+x+"_"+y+"' style='border-bottom-style:solid;border-left-style:solid;border-top-style:solid;border-right-style:solid;width:33.3%;background-Color:#F0F8FF;border-bottom-color:#BBBB00;'><span id='state_"+x+"_1' style='text-align:center;'>第一步：閱讀與發文</span></td>";
							add += "<td id='td_step2_"+x+"_"+y+"'  style='border-bottom-style:solid;border-left-style:solid;border-top-style:solid;border-right-style:solid;width:33.3%;background-Color:#F0F8FF;border-bottom-color:#BBBB00;'><span id='state_"+x+"_2' style='text-align:center;'>第二步：邀請討論</span></td>";
							add += "<td id='td_step3_"+x+"_"+y+"' style='border-bottom-style:solid;border-left-style:solid;border-top-style:solid;border-right-style:solid;width:33.3%;background-Color:#F0F8FF;border-bottom-color:#BBBB00;'><span id='state_"+x+"_3' style='text-align:center;'>第三步：傳遞任務</span></td>";
							add += "</tr>";
							add += "<tr>";
							add += "<td id='td1_step1_"+x+"_"+y+"' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;border-left-style:solid;border-right-style:solid;width:33.3%;'>&nbsp;&nbsp;<span id='span_step1_"+x+"_"+y+"' style='height:72px;line-height:72px;' name="+x+" class='new_button'></span><img src='img/mission_icon/write.png'></td>";
							add += "<td id='td1_step2_"+x+"_"+y+"' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;border-left-style:solid;border-right-style:solid;width:33.3%;'>&nbsp;&nbsp;<span id='span_step2_"+x+"_"+y+"' style='height:72px;line-height:72px;' name="+x+" class='new_button'></span><img src='img/mission_icon/discuss_talk.png'></td>";
							add += "<td id='td1_step3_"+x+"_"+y+"' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;border-left-style:solid;border-right-style:solid;width:33.3%;'>&nbsp;&nbsp;<span id='span_step3_"+x+"_"+y+"' style='height:72px;line-height:72px;' name="+x+" class='new_button'></span><img src='img/mission_icon/deliver_mission.png'></td>";
							add += "</tr>";
							add += "<tr>";
							add += "<td id='td2_step1_"+x+"_"+y+"' class='mission_step1' style='border-left-style:solid;border-right-style:solid;border-bottom-style:solid;border-bottom-color:#BBBB00;width:33.3%;'><img src='img/mission_icon/article.png'>&nbsp;標題：<a id='href_article"+x+"_"+y+"' href='../_dev_group_mission.php?get_from=3&group_task_id="+task_id[x]+"&article_id="+article_id[x][y]+"' target='_blank'>"+article_title[x][y]+"<a><span id='span_article"+x+"_"+y+"'></span></td>";
							add += "<td id='td2_step2_"+x+"_"+y+"' class='mission_step1' style='border-left-style:solid;border-right-style:solid;border-bottom-style:solid;border-bottom-color:#BBBB00;width:33.3%;'><img src='img/mission_icon/like.png'>&nbsp;討論串按讚數：&nbsp;"+like_cno[x][y]+"&nbsp;個</td>";
							add += "<td id='td2_step3_"+x+"_"+y+"' class='mission_step1' style='border-left-style:solid;border-right-style:solid;border-bottom-style:solid;border-bottom-color:#BBBB00;width:33.3%;'><img src='img/mission_icon/deliver.png'>&nbsp;任務已傳給：&nbsp;"+deliver_cno[x]+"&nbsp;人</td>";
							add += "</tr>";
							add += "<tr>";
							add += "<td id='td3_step1_"+x+"_"+y+"' class='mission_step1' style='border-left-style:solid;border-right-style:solid;border-bottom-style:solid;border-bottom-color:#BBBB00;width:33.3%'><img src='img/mission_icon/book.png'>&nbsp;書籍：<a id='href_book"+x+"' href='../article.php?get_from=1&book_sid="+book_id[x]+"' target='_blank'><span style='word-break: break-all;'>"+get_book_name[x]+"</span></a><span id='span_book"+x+"'></span></td>";
							add += "<td id='td3_step2_"+x+"_"+y+"' class='mission_step1' style='border-left-style:solid;border-right-style:solid;border-bottom-style:solid;border-bottom-color:#BBBB00;width:33.3%;'><img src='img/mission_icon/reply.png'>&nbsp;文章被回覆數：&nbsp;"+article_reply_cno[x][y]+"&nbsp;個</td>";
							add += "<td id='td3_step3_"+x+"_"+y+"' class='mission_step1' style='border-left-style:solid;border-right-style:solid;border-bottom-style:solid;border-bottom-color:#BBBB00;width:33.3%;'><img src='img/mission_icon/money.png'>&nbsp;&nbsp;<span id='span_reward"+x+"_"+y+"'>累積獎勵</span>：&nbsp;"+get_potential_score[x][y]+" 分&nbsp;<span id='span_explain"+x+"_"+y+"'></span></td>";
							add += "</table>";	
							add += "</div>";
							$("#tab"+x).append(add);
							$("#ms_"+x+"_"+y).css({'background-Color':'#CCFF99'});
							$(".TB").css({'border-collapse':'collapse'});
							
						}
					}else if(y!=0){//朋友的任務
						if(mission_state[x][y]!= 0 && mission_state[x][y]!= 3 && mission_state[x][y]!= 4){//朋友的任務>篩選掉(1.沒回應 2.已拒絕 3.沒接到任務)
							i++;
							add ="";
							add += "<div style='margin-top:5%'>";
							add += "<table class='TB' style='width:80%;margin: 1% auto;'>";
							add += "<tr>";
							add += "<td colspan='2' class='mission_step' style='background-Color:#FFFFBB;color:black;border-bottom-style:solid;border-bottom-color:#BBBB00;' name="+x+" value="+y+"><img src='img/mission_icon/topic.png'>&nbsp;&nbsp;朋友的任務狀態：<span id='span_state_"+x+"_"+y+"' style='color:blue'></span><span style='float:right;margin-right:10px;'>#"+i+"</span>&nbsp;<span id='span_step_"+x+"_"+y+"' style='color:red'></span></td>";
							add += "</tr>";
							add += "<tr>";
							add += "<td class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;width:50%'><img src='img/mission_icon/deliver.png'>&nbsp;&nbsp;接受任務人：&nbsp;<span style='color:blue'>「"+get_name[x][y]+"」</span></td>";
							add += "<td class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;width:50%'><img src='img/mission_icon/article.png'>&nbsp;&nbsp;文章標題：&nbsp;<a id='href_article"+x+"_"+y+"' href='../_dev_group_mission.php?get_from=3&group_task_id="+task_id[x]+"&article_id="+article_id[x][y]+"' target='_blank'>"+article_title[x][y]+"<a><span id='span_article"+x+"_"+y+"'></span></td>";
							add += "</tr>";
							add += "<tr>";
							add += "<td class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;width:50%'><img src='img/mission_icon/like.png'>&nbsp;&nbsp;您貢獻的按讚數：&nbsp;"+like_cno[x][y]+"&nbsp;個&nbsp;&nbsp;</td>";
							add += "<td class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;width:50%'><img src='img/mission_icon/reply.png'>&nbsp;&nbsp;您貢獻的回覆數：&nbsp;"+article_reply_cno[x][y]+"&nbsp;個</td>";
							add += "</tr>";
							add += "<tr>";
							add += "<td colspan='2' class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;width:50%'><img src='img/mission_icon/friend_money.png'>&nbsp;&nbsp;<span id='span_reward"+x+"_"+y+"'>累積可獲得獎勵</span>：&nbsp;"+get_potential_score[x][y]+" 分&nbsp;<span id='span_explain"+x+"_"+y+"' style='text-decoration:underline;'></span></td>";
							add += "</tr>";
							add += "</table>";	
							add += "</div>";
							$("#tab"+x).append(add);						
						}	

					}
					//----------------------------------------------------	
					//調整>顯示狀況
					//----------------------------------------------------				
					if(mission_state[x][y]==4){//當自已或朋友沒有該任務時，任務頁籤將隱藏起來	
						
						//$("#tab_div_"+x).css({"display":"none"});
						if(task_id[x]==2||task_id[x]==3||task_id[x]==4){						
							$("#tab_div_"+x).css({"display":"none"});
							$("#tab"+x).css({"display":"none"});
						}
					}  
																
					if(article_id[x][y]==0){//當有顯示的任務中，有未完成發文者
						$("#href_article"+x+"_"+y).remove();						
						$("#span_article"+x+"_"+y).text("未發表文章");
						$("#span_article"+x+"_"+y).css({"color":""});
						
					}
					if(book_id[x]==0){
						$("#href_book"+x).remove();
						$("#span_book"+x).text("還沒有選擇書籍");
					}
					//-------------------------------------
					//任務已完成
					//-------------------------------------
					if(mission_state[x][y]==1){//任務已完成
						if(y==0){
							$("#span_reward"+x+"_"+y).text("已獲得獎勵");
						}else if(y!=0){
							$("#span_reward"+x+"_"+y).text("您可獲得獎勵");
						}					
						$("#span_state_"+x+"_"+y).text("『 任務已完成 』 (點擊可查看完成狀況)");
						$("#span_step1_"+x+"_"+y).text("已完成任務");
						$("#span_step2_"+x+"_"+y).text("已完成任務");
						$("#span_step3_"+x+"_"+y).text("已完成任務");
					}
					//-------------------------------------
					//任務進行中
					//-------------------------------------
					else if(mission_state[x][y]==2){
						if(y==0){//自己的任務
							//$("#span_explain"+x+"_"+y).text("(需完成所有步驟才能取得獎勵分數)");						
							if(step_number[x][y]==0){
								//$("#span_step_"+x+"_"+y).text("(正在進行> 第一步驟)");
								$("#span_step1_"+x+"_"+y).text("進行任務");
								$("#span_step1_"+x+"_"+y).addClass("m-button");
								$("#state_"+x+"_1").css({"color":"red"});
								$("#td_step1_"+x+"_"+y).css({"border-left-color":"red"});
								$("#td_step1_"+x+"_"+y).css({"border-right-color":"red"});							
								$("#td1_step1_"+x+"_"+y).css({"border-left-color":"red"});
								$("#td1_step1_"+x+"_"+y).css({"border-right-color":"red"});
								$("#td1_step1_"+x+"_"+y).css({"background-Color":"#FFFFE0"});
								$("#td2_step1_"+x+"_"+y).css({"border-left-color":"red"});
								$("#td2_step1_"+x+"_"+y).css({"border-right-color":"red"});
								$("#td2_step1_"+x+"_"+y).css({"background-Color":"#FFFFE0"});
								$("#td3_step1_"+x+"_"+y).css({"border-left-color":"red"});
								$("#td3_step1_"+x+"_"+y).css({"border-right-color":"red"});
								$("#td3_step1_"+x+"_"+y).css({"background-Color":"#FFFFE0"});
						
								$("#span_step2_"+x+"_"+y).text("未開啟任務");
								$("#span_step3_"+x+"_"+y).text("未開啟任務");
							}
							else if(step_number[x][y]==1){
								//$("#span_step_"+x+"_"+y).text("(正在進行> 第二步驟)");
								$("#span_step2_"+x+"_"+y).text("進行任務");
								$("#span_step2_"+x+"_"+y).addClass("m-button");
								$("#state_"+x+"_2").css({"color":"red"});							
								$("#td_step1_"+x+"_"+y).css({"border-right-color":"red"});							
								$("#td1_step1_"+x+"_"+y).css({"border-right-color":"red"});							
								$("#td2_step1_"+x+"_"+y).css({"border-right-color":"red"});								
								$("#td3_step1_"+x+"_"+y).css({"border-right-color":"red"});
								$("#td_step2_"+x+"_"+y).css({"border-left-color":"red"});
								$("#td_step2_"+x+"_"+y).css({"border-right-color":"red"});							
								$("#td1_step2_"+x+"_"+y).css({"border-left-color":"red"});
								$("#td1_step2_"+x+"_"+y).css({"border-right-color":"red"});
								$("#td1_step2_"+x+"_"+y).css({"background-Color":"#FFFFE0"});
								$("#td2_step2_"+x+"_"+y).css({"border-left-color":"red"});
								$("#td2_step2_"+x+"_"+y).css({"border-right-color":"red"});
								$("#td2_step2_"+x+"_"+y).css({"background-Color":"#FFFFE0"});
								$("#td3_step2_"+x+"_"+y).css({"border-left-color":"red"});
								$("#td3_step2_"+x+"_"+y).css({"border-right-color":"red"});
								$("#td3_step2_"+x+"_"+y).css({"background-Color":"#FFFFE0"});
								
								$("#span_step1_"+x+"_"+y).text("已完成任務");
								$("#span_step3_"+x+"_"+y).text("未開啟任務");
							}
							else if(step_number[x][y]==2){
								//$("#span_step_"+x+"_"+y).text("(正在進行> 第三步驟)");
								$("#span_step3_"+x+"_"+y).text("進行任務");
								$("#span_step3_"+x+"_"+y).addClass("m-button");
								$("#state_"+x+"_3").css({"color":"red"});								
								$("#td_step2_"+x+"_"+y).css({"border-right-color":"red"});							
								$("#td1_step2_"+x+"_"+y).css({"border-right-color":"red"});								
								$("#td2_step2_"+x+"_"+y).css({"border-right-color":"red"});								
								$("#td3_step2_"+x+"_"+y).css({"border-right-color":"red"});
								$("#td_step3_"+x+"_"+y).css({"border-left-color":"red"});
								$("#td_step3_"+x+"_"+y).css({"border-right-color":"red"});							
								$("#td1_step3_"+x+"_"+y).css({"border-left-color":"red"});
								$("#td1_step3_"+x+"_"+y).css({"border-right-color":"red"});
								$("#td1_step3_"+x+"_"+y).css({"background-Color":"#FFFFE0"});
								$("#td2_step3_"+x+"_"+y).css({"border-left-color":"red"});
								$("#td2_step3_"+x+"_"+y).css({"border-right-color":"red"});
								$("#td2_step3_"+x+"_"+y).css({"background-Color":"#FFFFE0"});
								$("#td3_step3_"+x+"_"+y).css({"border-left-color":"red"});
								$("#td3_step3_"+x+"_"+y).css({"border-right-color":"red"});
								$("#td3_step3_"+x+"_"+y).css({"background-Color":"#FFFFE0"});
								
								$("#span_step1_"+x+"_"+y).text("已完成任務");
								$("#span_step2_"+x+"_"+y).text("已完成任務");
							}
							$("#span_state_"+x+"_"+y).text("『 任務進行中 』");
						}
						if(y!=0){//朋友的任務
							$("#span_reward"+x+"_"+y).text("您貢獻的任務獎勵");
							if(step_number[x][y]==0){
								$("#span_state_"+x+"_"+y).text("『 任務進行中 』 (點擊可查看狀況)");
							}else if(step_number[x][y]==1){
								$("#span_state_"+x+"_"+y).text("『 任務進行中 』 (點擊->參與朋友的討論)");
							}else if(step_number[x][y]==2){
								$("#span_state_"+x+"_"+y).text("『 任務進行中 』 (點擊可查看狀況)");
							}
						}
											
					}
					//-------------------------------------
					//任務已拒絕
					//-------------------------------------					
					else if(mission_state[x][y]==3){
						$("#span_state_"+x+"_"+y).text("『 已拒絕任務 』 (可以再次接取任務)");
						$("#span_step1_"+x+"_"+y).text("未開啟任務");
						$("#span_step2_"+x+"_"+y).text("未開啟任務");
						$("#span_step3_"+x+"_"+y).text("未開啟任務");
					//-------------------------------------
					//任務未回應
					//-------------------------------------	
					}else if(mission_state[x][y]==0||mission_state[x][y]==5){
						$("#span_state_"+x+"_"+y).text("『 未回應任務 』 (點擊接取任務)");
						$("#span_step1_"+x+"_"+y).text("未開啟任務");
						$("#span_step2_"+x+"_"+y).text("未開啟任務");
						$("#span_step3_"+x+"_"+y).text("未開啟任務");					
					}	
					/* else if(mission_state[x][y]==4){$("#span_state_"+x+"_"+y).text("『 未接到任務 』");} */
				}												
					//----------------------------------------------------
					//按下>查看任務狀況
					//----------------------------------------------------
					$(".new_button").click(function(){//新按鈕
						var x = $(this).attr("name");//存取陣列跑到第幾個任務
						var y =0;
						if($(this).text()=="進行任務"){//限制只有進行的步驟可以點擊					
							if(mission_state[x][0]!=4){//自己的任務>篩選掉(沒接到的任務)
								if(mission_state[x][y]==1){
									finish_mission(task_id[x],task_topic[x],deliver_uid[x]);
									$("#mission_topic").text("["+get_name[x][y]+"] 的任務");
								}else if(mission_state[x][y]==2){
									doing_mission(task_id[x],task_topic[x],deliver_uid[x]);
									$("#mission_topic").text("["+get_name[x][y]+"] 的任務");
								}
								window.location.assign('#another_page');
							}
						}						
					})
					
					
					$(".mission_step").click(function(){//原按鈕						
						var x = $(this).attr("name");//存取陣列跑到第幾個任務
						var y = $(this).attr("value");//第幾個朋友(0:自己；1~n:朋友)
						//換頁----------------------------------------------
						if(y==0){//自己的任務
							if(mission_state[x][0]!=4){//自己的任務>篩選掉(沒接到的任務)
								if(mission_state[x][y]==1){
									finish_mission(task_id[x],task_topic[x],deliver_uid[x]);
									$("#mission_topic").text("["+get_name[x][y]+"] 的任務");
								}else if(mission_state[x][y]==2){
									doing_mission(task_id[x],task_topic[x],deliver_uid[x]);
									$("#mission_topic").text("["+get_name[x][y]+"] 的任務");
								}else if(mission_state[x][y]==0||mission_state[x][y]==5){
									none_reply_mission(2,x);
									//$("#mission_topic").text("["+get_name[x][y]+"] 的任務");
								}else if(mission_state[x][y]==3){
									reject_mission();
									//$("#mission_topic").text("["+get_name[x][y]+"] 的任務");
								}
								window.location.assign('#another_page');
							}
							
						}else if(y!=0){//朋友的任務
							if(mission_state[x][y] != 0 && mission_state[x][y] != 3 && mission_state[x][y] != 4 && mission_state[x][y] != 5){//朋友的任務>篩選掉(1.沒回應 2.已拒絕 3.沒接到任務)
								if(mission_state[x][y]==1){
									finish_friend_mission(task_id[x],task_topic[x],my_friend[y],get_name[x][y]);
									$("#mission_topic").text("["+get_name[x][y]+"] 的任務");
								}else if(mission_state[x][y]==2){
									doing_friend_mission(task_id[x],task_topic[x],my_friend[y],get_name[x][y]);
									$("#mission_topic").text("["+get_name[x][y]+"] 的任務");
								}
								window.location.assign('#another_page');
							}	
						}
					})
					//----------------------------------------------	
			}	
			//加入成就稱號----------------------------------------------------	
				add ="";
				add += "<div style='margin-left:2%;margin-right:2%;margin-top:1%'>";
				add += "<table class='TB'>";
				add += "<tr><td colspan='3' style='background-Color:#FFFFBB;font-size:17px;text-align:center;'><img src='img/mission_icon/badge.png'>&nbsp;【成就稱號】</td></tr>";
				add += "<tr>";
				add += "<td style='width:13%;background-color:#CCEEFF;'>《"+title_name[title_next-2]+"》</td>";
				add += "<td style='border-style:inset'>晉級到下個稱號：『"+title_name[title_next-1]+"』，還需要 「"+(title_score[title_next-1]-get_score)+"」分</td>";					
				add += "<td style='width:13%;background-color:#CCEEFF;'>《"+title_name[title_next-1]+"》</td>";			
				add += "</tr>";
				add += "<tr>";
				add += "<td style='width:13%;background-Color:#F9F9F9'><img src='img/character/character"+sex+"_"+(title_next-1)+".png' width='50%' height='auto'></td>";
				add += "<td style='width:74%;' class='TB_foot'><div id='progressbar'><div class='progress-label3'>"+"Loading..."+"</div></div></td>";
				add += "<td style='width:13%;background-Color:#F9F9F9'><img src='img/character/character"+sex+"_"+title_next+".png' width='50%' height='auto'></td>";
				add += "</tr>";
				add += "</table>";
				add += "</div>";
				$("#div-total_mission").append(add);
				progress(progress_score,get_score);
			//----------------------------------------------------
				$('.abgne_tab').each(function(){
					// 目前的頁籤區塊
					var $tab = $(this);
					var $defaultLi = $('ul.tabs li', $tab).eq(_showTab).addClass('active');
					$($defaultLi.find('a').attr('href')).siblings().hide();
			 
					// 當 li 頁籤被點擊時...
					// 若要改成滑鼠移到 li 頁籤就切換時, 把 click 改成 mouseover
					$('ul.tabs li', $tab).click(function() {
						// 找出 li 中的超連結 href(#id)
						var $this = $(this),
							_clickTab = $this.find('a').attr('href');
						// 把目前點擊到的 li 頁籤加上 .active
						// 並把兄弟元素中有 .active 的都移除 class
						$this.addClass('active').siblings('.active').removeClass('active');
						// 淡入相對應的內容並隱藏兄弟元素
						$(_clickTab).stop(false, true).fadeIn().siblings().hide();
			 
						return false;
					}).find('a').focus(function(){
						this.blur();
					});
				});	
			//-----------------------------------------------------------------	
		}
		//-----------------------------------------------------------------------
		//產生Menu列表
		//-----------------------------------------------------------------------
		function show_mission_list(){
			$("#m_list").empty();
			$("#mission_content").empty();
			for(var x=0; x<task_number; x++){				
				var add ="";
				add += "<ul id='m_"+task_id[x]+"' class='mission_list'><img src='img/mission_icon/align.png'>【"+task_topic[x]+"】</ul>";
				add += "<ul class='m_"+task_id[x]+"'></ul>";
				$("#m_list").append(add);
				
				for(var y=0; y<=friend_number; y++){
					add ="";										
					if(y==0){//自己的任務
						if(mission_state[x][0]!=4){
							add += "<li id='"+task_id[x]+"_"+y+"' name='"+task_id[x]+"' value='"+x+"'><a id='"+y+"' href='#another_page'><img src='img/mission_icon/person.png'><span class='font-MJ'>["+get_name[x][0]+"] 的任務</span></a></li>";							
						}
																		
					}else if(y!=0){//朋友的任務
						if(mission_state[x][y] != 0 && mission_state[x][y] != 3 && mission_state[x][y] != 4){
							if(task_id[x]!=2||task_id[x]!=3||task_id[x]!=4){
							add += "<li id='"+task_id[x]+"_"+y+"' name='"+task_id[x]+"' value='"+x+"'><a id='"+y+"' href='#another_page'><img src='img/mission_icon/group.png'><span class='font-MJ'>["+get_name[x][y]+"] 的任務</span></a></li>";	
							}
						}
					}
					$(".m_"+task_id[x]).append(add);
				}
				if(task_id[x]==2||task_id[x]==3||task_id[x]==4){//限制看不到任務編號2、3、4
					$("#m_"+task_id[x]).hide();
					$(".m_"+task_id[x]).hide();
				}
			}			
			
			
			$(".mission_list").click(function(){//延展主任務下的子任務
				var ul_id = $(this).attr("id");
				$("."+ul_id).toggle();					
			});	
			
			$("li").click(function(){//點擊子任務事件	
				var li_task_id = $(this).attr("name");//任務的編號				
				var li_a = $(this).attr("value");//迴圈中跑到「第幾個任務」
				var li_b = $(this).find("a").attr("id");//迴圈中跑到「第幾個人」(0:自己；1~n:朋友)
				var li_name = $(this).find("span").text();//指到是「誰」的任務
				var n = parseInt(mission_state[li_a][li_b]);//任務的狀態
				$("#mission_topic").text(li_name);
				//alert(n);
				if(li_b == 0){//針對「自己」的任務									
					switch(n)
					{
						case 0://未回應該任務							
							none_reply_mission(2,li_a);
						break;
						
						case 1://已完成該任務
							finish_mission(li_task_id,task_topic[li_a],deliver_uid[li_a]);
						break;
						
						case 2://未完成該任務
							doing_mission(li_task_id,task_topic[li_a],deliver_uid[li_a]);
						break;

						case 3://已拒絕該任務
							reject_mission();					
						break;	
						
						case 5://未回應該任務(看到不理)
							none_reply_mission(2,li_a);					
						break;
					}														
				}else if(li_b != 0){//針對「朋友」的任務				
					switch(n)
					{
						case 1://已完成該任務
							finish_friend_mission(li_task_id,task_topic[li_a],my_friend[li_b],get_name[li_a][li_b]);
						break;
						
						case 2://未完成該任務
							doing_friend_mission(li_task_id,task_topic[li_a],my_friend[li_b],get_name[li_a][li_b]);
						break;						
					}
				}								
			});			

		}
		//-----------------------------------------------------------------------
		//接收到任務，且未回應任務
		//-----------------------------------------------------------------------
		function none_reply_mission(m_type,t){//學生未回應任務(m_type：類型；t：跑到第幾個任務)
		$("#mission_content").empty();
		if(m_type==1){//類型1：由「系統」主動提醒要接取任務
			var i = 0;
			for(var x=0; x<task_number; x++){//如果有接收到任務且未回應			
				if(mission_state[x][0]==0||mission_state[x][0]==5 && i==0){
					i = 1;
					var add ="";
					add +="<table class='TB' style='width:70%;margin-top:3%;border-color:#E6E6FA'>";
					add += "<tr><td colspan='3'><div id='div-alert'class='font-MJ' style='color:red;font-size:16px;'><img src='img/mission_icon/back.png'>&nbsp;<a href='#main_page'>[返回任務總覽頁面]</a></div></td></tr>";
					add +="<tr><td style='background-Color:#FFFFBB;font-size:16px;color:black;border-bottom-style:solid;border-bottom-color:#BBBB00;'>您收到來自<span style='color:blue'>『 "+deliver_name[x]+" 』</span>指派的任務</td></tr>";
					add +="<tr><td class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/topic.png'>&nbsp;&nbsp;任務內容：選擇一本<span style='color:red'>【"+task_topic[x]+"】</span>，並且寫下閱讀後的讀書心得</td></tr>";
					add +="<tr><td class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/arrow1.png'>&nbsp;&nbsp;請選擇是否接受這項任務：</td></tr>";
					add +="<tr><td><div id='accept_m' class='m-button' name="+x+">接受任務</div></td></tr>";
					add +="<tr><td><div id='reject_m' class='m-button' name="+x+">拒絕任務</div></td></tr>";
					add +="</table>";
					add +="</div>";
					$("#mission_content").append(add);	
					window.location.assign('#another_page');
					$("#mission_topic").text("收到的推播任務");
					//-----------------------------------------
					//將任務狀態改為「看到不理」(5)
					//-----------------------------------------
					var send_data = [];
					send_data = {"mode":1,"u_task_id":task_id[x],"i_deliver_uid":deliver_uid[x]};
					$.ajax({
						"url":"initial_mission.php",
						"data":send_data,
						"type":"POST",
						//"dataType":"json",
						"async": false,//改為同步執行，照正常邏輯執行
						"beforeSend": function(){},
						"success": function(data){
							console.log(data);//檢查用
						},
						"error": function(data){
							//console.log(data);
							alert("error");	
						}	
					});	
					//-----------------------------------------
				}
			}
		}else if(m_type==2){//類型2：由「學生」自行接取任務
			var add ="";
			add +="<table class='TB' style='width:70%;margin-top:3%;border-color:#E6E6FA'>";
			add += "<tr><td colspan='3'><div id='div-alert'class='font-MJ' style='color:red;font-size:16px;'><img src='img/mission_icon/back.png'>&nbsp;<a href='#main_page'>[返回任務總覽頁面]</a></div></td></tr>";
			add +="<tr><td style='background-Color:#FFFFBB;font-size:16px;color:black;border-bottom-style:solid;border-bottom-color:#BBBB00;'>您收到來自<span style='color:blue'>『 "+deliver_name[t]+" 』</span>指派的任務</td></tr>";
			add +="<tr><td class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/topic.png'>&nbsp;&nbsp;任務內容：選擇一本<span style='color:red'>【"+task_topic[t]+"】</span>，並且寫下閱讀後的讀書心得</td></tr>";
			add +="<tr><td class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/arrow1.png'>&nbsp;&nbsp;請選擇是否接受這項任務：</td></tr>";
			add +="<tr><td><div id='accept_m' class='m-button' name="+t+">接受任務</div></td></tr>";
			add +="<tr><td><div id='reject_m' class='m-button' name="+t+">拒絕任務</div></td></tr>";
			add +="</table>";
			add +="</div>";
			$("#mission_content").append(add);	
			window.location.assign('#another_page');
			$("#mission_topic").text("收到的推播任務");
			//-----------------------------------------
			//將任務狀態改為「看到不理」(5)
			//-----------------------------------------
			var send_data = [];
			send_data = {"mode":1,"u_task_id":task_id[t],"i_deliver_uid":deliver_uid[t]};
			$.ajax({
				"url":"initial_mission.php",
				"data":send_data,
				"type":"POST",
				//"dataType":"json",
				"async": false,//改為同步執行，照正常邏輯執行
				"beforeSend": function(){},
				"success": function(data){
					console.log(data);//檢查用
				},
				"error": function(data){
					//console.log(data);
					alert("error");	
				}	
			});	
			//-----------------------------------------								
		}
			//-----------------------------------------	
			//按下提交按鈕(接受/拒絕)
			//-----------------------------------------										
			$(".m-button").click(function(){				
				var selection = $(this).attr("id");//存取是按(接受/拒絕)
				var x = $(this).attr("name");//存取陣列跑到第幾個任務
				var send_data = [];
				if(selection=="accept_m"){//接受任務				
					send_data = {"mode":2,"u_task_id":task_id[x],"u_mission_state":2,"i_deliver_uid":deliver_uid[x]};									
				}else if(selection=="reject_m"){//拒絕任務				
					send_data = {"mode":2,"u_task_id":task_id[x],"u_mission_state":3,"i_deliver_uid":deliver_uid[x]};			
				}
				$.ajax({
					"url":"initial_mission.php",
					"data":send_data,
					"type":"POST",
					//"dataType":"json",
					"async": false,//改為同步執行，照正常邏輯執行
					"beforeSend": function(){},
					"success": function(data){
						console.log(data);//檢查用
						if(selection=="accept_m"){//接受任務，網頁導向任務進行中
							//alert("success");
							doing_mission(task_id[x],task_topic[x],deliver_uid[x]);
							window.location.assign('#another_page');
						}
						else if(selection=="reject_m"){//拒絕任務，網頁導向推播任務總覽頁面
							reject_mission(task_id[x],task_topic[x],deliver_uid[x]);
							window.location.assign('#main_page');
						}
					},
					"error": function(data){
						//console.log(data);
						alert("error");	
					}	
				});		
			});
			//-----------------------------------------	
		}
		//-----------------------------------------------------------------------
		//已拒絕的任務
		//-----------------------------------------------------------------------
		function reject_mission(){
		$("#mission_content").empty();
		for(var x=0; x<task_number; x++){//如果有接收到任務且未回應			
			if(mission_state[x][0]==3){
				var add ="";
				add +="<table class='TB' style='width:70%;margin-top:3%;border-color:#E6E6FA'>";
				add += "<tr><td colspan='3'><div id='div-alert'class='font-MJ' style='color:red;font-size:16px;'><img src='img/mission_icon/back.png'>&nbsp;<a href='#' onClick='javascript:history.back(1)'>[返回上一頁]</a></div></td></tr>";
				add +="<tr><td style='background-Color:#FFFFBB;font-size:16px;color:black;border-bottom-style:solid;border-bottom-color:#BBBB00;'>您收到來自<span style='color:blue'>『 "+deliver_name[x]+" 』</span>指派的任務</td></tr>";
				add +="<tr><td class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/topic.png'>&nbsp;&nbsp;任務內容：選擇一本<span style='color:red'>【"+task_topic[x]+"】</span>，並且寫下閱讀後的讀書心得</td></tr>";
				add +="<tr><td class='mission_step1' style='border-bottom-style:solid;border-bottom-color:#BBBB00;'><img src='img/mission_icon/arrow1.png'>&nbsp;&nbsp;請選擇是否接受這項任務：</td></tr>";
				add +="<tr><td><div id='accept_m' class='m-button' name="+x+">接受任務</div></td></tr>";
				add +="<tr><td><div id='reject_m' class='m-button' name="+x+">拒絕任務</div></td></tr>";
				add +="</table>";
				add +="</div>";
				$("#mission_content").append(add);	
				window.location.assign('#another_page');
				$("#mission_topic").text("收到的推播任務");
			}
		}
			
			$(".m-button").click(function(){//按下提交按鈕(接受/拒絕)
				var send_data = [];
				var selection = $(this).attr("id");//存取是按(接受/拒絕)
				var x = $(this).attr("name");//存取陣列跑到第幾個任務
				if(selection=="accept_m"){//接受任務				
					send_data = {"u_task_id":task_id[x],"u_mission_state":2,"i_deliver_uid":deliver_uid[x]};									
				}else if(selection=="reject_m"){//拒絕任務				
					send_data = {"u_task_id":task_id[x],"u_mission_state":3,"i_deliver_uid":deliver_uid[x]};			
				}
				$.ajax({
					"url":"initial_mission.php",
					"data":send_data,
					"type":"POST",
					//"dataType":"json",
					"async": false,//改為同步執行，照正常邏輯執行
					"beforeSend": function(){},
					"success": function(data){
						console.log(data);//檢查用
						if(selection=="accept_m"){//接受任務，網頁導向任務進行中
							//alert("success");
							doing_mission(task_id[x],task_topic[x],deliver_uid[x]);
							window.location.assign('#another_page');
						}
						else if(selection=="reject_m"){//拒絕任務，網頁導向推播任務總覽頁面
							//reject_mission(task_id[x],task_topic[x],deliver_uid[x]);
							window.location.assign('#main_page');
						}
					},
					"error": function(data){
						//console.log(data);
						alert("error");	
					}	
				});		
			});			
		}
		//-----------------------------------------------------------------------
		//處理進度條
		//-----------------------------------------------------------------------
		function progress(){	
			var proLabel = $(".progress-label3");//成就頭銜
			var pro = $("#progressbar");  //进度条div
			pro.progressbar({
			  value: false,  //初始化的值为0
			  change: function() {//当value值改变时，同时修改label的字			  				
				proLabel.text("稱號進度："+ get_score + "/ " + title_score[title_next-1] + " 分" );		
				
			  },
			  complete: function() {//当进度条完成时，显示complate			  	
				proLabel.text("已取得最高位階的稱號！");		
			  }
			});
			//延迟500毫秒调用修改value的函数
			if(progress_score > 0){
				setTimeout(addValue, 500);
			}
			else{pro.progressbar("value",0);}			
		 
			//动态修改value的函数
			//var nowValue = pro.progressbar("value");//抓取目前的值 
			var nowValue = progress_score;
			var start = 0;
			function addValue(){
				var pro = $("#progressbar");
				start +=1;			   
				pro.progressbar("value",start); //设置新值
				if( start >= nowValue) {return;}    //超过目前的最新進度时，返回
				setTimeout( addValue, 10); //延迟500毫秒递归调用自己
			}						
		}		
	});
	</script>
</head>
<body>
	<div id="slidemenu" style="margin-top:53px;">

		<div id="profile" >
			<img src="img/mission_icon/task.png">
			<div class="profile_info"><strong class="font-MJ">推播任務</strong><br><small class="font-MJ">Group Achievement</small></div>
		</div>
			<h3>任務項目</h3>
		<ul>
			<li><a href="#main_page"><img src="img/mission_icon/add1.png"><span class="font-MJ" style='font-size:15px'>推播任務總覽</span></a></li>
		</ul>	
		<div id="m_list"></div>				
	</div>
	<!--   -->	
		<div data-role="page" id="main_page" data-theme="a" >

			<div data-role="header" data-position="fixed" data-tap-toggle="false" data-update-page-padding="false">
				<a href="#" data-slidemenu="#slidemenu" data-slideopen="false" data-icon="smico" data-corners="false" data-iconpos="notext">Menu</a>
				<h1 class="font-MJ">推播任務總覽</h1>
			</div>
			<div data-role="content" id="div-total_mission">
			</div>
			
		</div>
	<!--    -->
		<div data-role="page" id="another_page" data-theme="a">

			<div data-role="header" data-position="fixed" data-tap-toggle="false" data-update-page-padding="false">
				<a href="#" data-slidemenu="#slidemenu" data-slideopen="false" data-icon="smico" data-corners="false" data-iconpos="notext">Menu</a>
				<h1 id="mission_topic" class="font-MJ"></h1>
			</div>
			<div data-role="content" align="center" style="overflow:visible;" id="mission_content"></div>			

		</div>
	<div id="main-div"></div>	
</body>
</html>