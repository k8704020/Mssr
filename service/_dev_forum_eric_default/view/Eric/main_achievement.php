<html>
<head>

	<meta charset="utf-8">
	<!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320"/>
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta http-equiv="cleartype" content="on">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">-->

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
	<script>
	$(document).ready(function(){		
		//var uid = 36152;//使用者
		var type = 0;//暫存成就類型
		var cno ;//該成就次數，Ex:按讚次數
		var level_now ;//暫存正在進行任務的等級
		var Arrlevel 		= [];//暫存個數	
		var Arrprocess 		= [];//暫存進度比例
		var level_degree 	= [];//存取成就等級
		var achieve_name 	= [];//存取成就名稱
		var achieve_content = [];//存取成就描述
		var achieve_value 	= [];//存取達成值
		var sex             = 0;//存取使用者的性別
	//---------------------------------------------------------
	//起始頁面> 更新成就總覽資料
	//---------------------------------------------------------
	
	//---------------------------------------------------------
	//起始頁面> 抓取成就總覽資料
	//---------------------------------------------------------
		var div_id =$("#div-total_achieve").attr('id');
		var send_data = {"type":type};
		$.ajax({//抓取學生的表現，傳給function產生視圖
			"url":"getvalue_achievement.php",
			"data":send_data,
			"type":"POST",
			"dataType":"json",
			//"async": false,//改為同步執行，照正常邏輯執行
			"beforeSend": function(){},
			"success": function(data){
				console.log(data);//檢查用
				//alert("suc 0");
				Arrtotal 		 = data["Arrtotal"];	
				Arrtotal_process = data["Arrtotal_process"];
				achieve_type 	 = data["achieve_type"];	
				title_name       = data["title_name"];
				title_score      = data["title_score"];
				title_next       = data["title_next"];
				get_score		 = data["get_score"];
				sex              = data["sex"];
				get_data(type,div_id);
			},
			"error": function(data){
				//console.log(data);
				alert("error 0");	
			}	
		});
	//---------------------------------------------------------
	//處理成就總體資料
	//---------------------------------------------------------
		$("#achieve_total").click(function(){
			type =0;
			var div_id =$("#div-total_achieve").attr('id');
			var send_data = {"type":type};
			$.ajax({//抓取學生的按讚數量，傳給function產生視圖
				"url":"getvalue_achievement.php",
				"data":send_data,
				"type":"POST",
				"dataType":"json",
				//"async": false,//改為同步執行，照正常邏輯執行
				"beforeSend": function(){},
				"success": function(data){
					console.log(data);//檢查用
					//alert("suc 0");
					Arrtotal 		 = data["Arrtotal"];	
					Arrtotal_process = data["Arrtotal_process"];
					achieve_name 	 = data["achieve_name"];
					title_name       = data["title_name"];
					title_score      = data["title_score"];
					title_next       = data["title_next"];
					get_score		 = data["get_score"];
					sex              = data["sex"];
					get_data(type,div_id);
				},
				"error": function(data){
					//console.log(data);
					alert("error 0");	
				}	
			});				
			
		});
	//---------------------------------------------------------	
	//處理文章『按讚』的資料
	//---------------------------------------------------------	
		$("#article_like").click(function(){
			/* $("#progressbar").progressbar({value: 100}); */			
			type =1;//選取成就的類型
			var div_id =$("#div-article_like").attr('id');
			var send_data = {"type":type};
			$.ajax({//抓取學生的按讚數量，傳給function產生視圖
				"url":"getvalue_achievement.php",
				"data":send_data,
				"type":"POST",
				"dataType":"json",
				//"async": false,//改為同步執行，照正常邏輯執行
				"beforeSend": function(){},
				"success": function(data){
					console.log(data);//檢查用
					//alert("suc 1");
					cno 			= data["cno"];
					level_now    	= data["level_now"];
					Arrlevel 		= data["Arrlevel"];	
					Arrprocess 		= data["Arrprocess"];
					level_degree 	= data["level_degree"];
					achieve_name 	= data["achieve_name"];
					achieve_content = data["achieve_content"];
					achieve_value	= data["achieve_value"];					
					get_data(type,div_id);//產生圖示					
					
				},
				"error": function(data){
					//console.log(data);
					alert("error 1");	
				}	
			});
		});
	//---------------------------------------------------------	
	//處理文章『被按讚』的資料
	//---------------------------------------------------------	
		$("#article_be_liked").click(function(){					
			type =2;//選取成就的類型
			var div_id =$("#div-article_be_liked").attr('id');
			var send_data = {"type":type};
			$.ajax({//抓取學生的按讚數量，傳給function產生視圖
				"url":"getvalue_achievement.php",
				"data":send_data,
				"type":"POST",
				"dataType":"json",
				//"async": false,//改為同步執行，照正常邏輯執行
				"beforeSend": function(){},
				"success": function(data){
					console.log(data);//檢查用
					//alert("suc 2");
					cno 			= data["cno"];
					level_now    	= data["level_now"];
					Arrlevel 		= data["Arrlevel"];	
					Arrprocess 		= data["Arrprocess"];
					level_degree 	= data["level_degree"];
					achieve_name 	= data["achieve_name"];
					achieve_content = data["achieve_content"];
					achieve_value	= data["achieve_value"];					
					get_data(type,div_id);//產生圖示
					
				},
				"error": function(data){
					//console.log(data);
					alert("error 2");	
				}	
			});
		});
	//---------------------------------------------------------	
	//處理『回覆』文章的資料
	//---------------------------------------------------------	
		$("#article_reply").click(function(){					
			type =3;//選取成就的類型
			var div_id =$("#div-article_reply").attr('id');
			var send_data = {"type":type};
			$.ajax({//抓取學生的按讚數量，傳給function產生視圖
				"url":"getvalue_achievement.php",
				"data":send_data,
				"type":"POST",
				"dataType":"json",
				//"async": false,//改為同步執行，照正常邏輯執行
				"beforeSend": function(){},
				"success": function(data){
					console.log(data);//檢查用
					//alert("suc 3");
					cno 			= data["cno"];
					level_now    	= data["level_now"];
					Arrlevel 		= data["Arrlevel"];	
					Arrprocess 		= data["Arrprocess"];
					level_degree 	= data["level_degree"];
					achieve_name 	= data["achieve_name"];
					achieve_content = data["achieve_content"];
					achieve_value	= data["achieve_value"];					
					get_data(type,div_id);//產生圖示
					
				},
				"error": function(data){
					//console.log(data);
					alert("error 3");	
				}	
			});
		});
	//---------------------------------------------------------
	//處理『回覆』文章的資料
	//---------------------------------------------------------
		$("#article_be_replied").click(function(){					
			type =4;//選取成就的類型
			var div_id =$("#div-article_be_replied").attr('id');
			var send_data = {"type":type};
			$.ajax({//抓取學生的按讚數量，傳給function產生視圖
				"url":"getvalue_achievement.php",
				"data":send_data,
				"type":"POST",
				"dataType":"json",
				//"async": false,//改為同步執行，照正常邏輯執行
				"beforeSend": function(){},
				"success": function(data){
					console.log(data);//檢查用
					//alert("suc 4");
					cno 			= data["cno"];
					level_now    	= data["level_now"];
					Arrlevel 		= data["Arrlevel"];	
					Arrprocess 		= data["Arrprocess"];
					level_degree 	= data["level_degree"];
					achieve_name 	= data["achieve_name"];
					achieve_content = data["achieve_content"];
					achieve_value	= data["achieve_value"];					
					get_data(type,div_id);//產生圖示
					
				},
				"error": function(data){
					//console.log(data);
					alert("error 4");	
				}	
			});
		});
	//---------------------------------------------------------	
	//處理『鷹架發文』文章的資料
	//---------------------------------------------------------	
		$("#article_eagle_publish").click(function(){					
			type =5;//選取成就的類型
			var div_id =$("#div-article_eagle_publish").attr('id');
			var send_data = {"type":type};
			$.ajax({//抓取學生的按讚數量，傳給function產生視圖
				"url":"getvalue_achievement.php",
				"data":send_data,
				"type":"POST",
				"dataType":"json",
				//"async": false,//改為同步執行，照正常邏輯執行
				"beforeSend": function(){},
				"success": function(data){
					console.log(data);//檢查用
					//alert("suc 5");
					cno 			= data["cno"];
					level_now    	= data["level_now"];
					Arrlevel 		= data["Arrlevel"];	
					Arrprocess 		= data["Arrprocess"];
					level_degree 	= data["level_degree"];
					achieve_name 	= data["achieve_name"];
					achieve_content = data["achieve_content"];
					achieve_value	= data["achieve_value"];					
					get_data(type,div_id);//產生圖示
					
				},
				"error": function(data){
					//console.log(data);
					alert("error 5");	
				}	
			});
		});	
	//---------------------------------------------------------
	//處理『鷹架發文』文章的資料	
	//---------------------------------------------------------
		$("#article_request").click(function(){				
			type =6;//選取成就的類型
			var div_id =$("#div-article_request").attr('id');
			var send_data = {"type":type};
			$.ajax({//抓取學生的按讚數量，傳給function產生視圖
				"url":"getvalue_achievement.php",
				"data":send_data,
				"type":"POST",
				"dataType":"json",
				//"async": false,//改為同步執行，照正常邏輯執行
				"beforeSend": function(){},
				"success": function(data){
					console.log(data);//檢查用
					//alert("suc 6");
					cno 			= data["cno"];
					level_now    	= data["level_now"];
					Arrlevel 		= data["Arrlevel"];	
					Arrprocess 		= data["Arrprocess"];
					level_degree 	= data["level_degree"];
					achieve_name 	= data["achieve_name"];
					achieve_content = data["achieve_content"];
					achieve_value	= data["achieve_value"];					
					get_data(type,div_id);//產生圖示
					
				},
				"error": function(data){
					//console.log(data);
					alert("error 6");	
				}	
			});
		});
	//---------------------------------------------------------	
	//顯示並產生圖示
	//---------------------------------------------------------			
		 function get_data(i,j){//(i:成就類型；j:div的名稱)
			$("#"+j).empty();//清空div
			if(type==0){//成就總覽
				var add ="";//產生成就總體>總個數
					add += "<div style='margin:2%;'>";
					add += "<table class='TB'>";
					add += "<tr><td rowspan='3' style='width:10%;border-style:double'><img src='img/reward/champion.png' width='90%' height='auto'></td></tr>";
					add += "<tr><td style='background-color:#FFFFBB;font-size:17px;'><img src='img/mission_icon/total.png'>&nbsp;【成就總體狀態】</td></tr>";
					add += "<tr><td class='TB_foot'><div id='progressbar0_0'><div class='progress-label2'>"+"Loading..."+"</div></div></td></tr>";					
					add += "</table>";
					add += "</div>";
					$("#"+j).append(add);
					progress(0,Arrtotal_process[6],i,Arrtotal[6]);
				for(var x=1; x<=6; x++){//產生成就總覽>各項目
					add ="";//清空html
					add += "<div class='div-achieve_total'>";
					add += "<table class='TB'>";
					add += "<tr><td rowspan='3' style='width:20%;background-color:#CCEEFF;border-style:double'><img src='img/reward/medal"+x+".png' width='100%' height='auto'></td></tr>";
					add += "<tr><td class='achieve_type' ><span id='"+x+"'><img src='img/mission_icon/champion.png'>《"+achieve_type[x-1]+"》</span></td></tr>";
					add += "<tr><td class='TB_foot'><div id='progressbar"+i+"_"+x+"' ><div class='progress-label_"+x+"'>"+"Loading..."+"</div></div></td></tr>";					
					add += "</table>";
					add += "</div>";
					$("#"+j).append(add);
					progress(x,Arrtotal_process[x-1],i,Arrtotal[x-1]);//傳值給進度條(迴圈跑到第幾個;陣列中的進度趴數;第幾種類型;陣列中的進度個數)
				}
					add = "";//清空html
					add += "<div style='margin:2%;'>";
					add += "<table class='TB'>";
					add += "<tr><td colspan='3' style='background-Color:#FFFFBB;font-size:17px;text-align:center;'><img src='img/mission_icon/badge.png'>&nbsp;【成就稱號】</td></tr>";
					add += "<tr>";
					add += "<td style='width:13%;background-color:#CCEEFF;'>《"+title_name[title_next-2]+"》</td>";
					add += "<td style='border-style:inset'>晉級到下個稱號：『"+title_name[title_next-1]+"』，還需要 「"+(title_score[title_next-1]-get_score)+"」分</td>";					
					add += "<td style='width:13%;background-color:#CCEEFF;'>《"+title_name[title_next-1]+"》</td>";			
					add += "</tr>";
					add += "<tr>";
					add += "<td style='width:13%;background-Color:#F9F9F9'><img src='img/character/character"+sex+"_"+(title_next-1)+".png' width='50%' height='auto'></td>";
					add += "<td style='width:74%;' class='TB_foot'><div id='progressbarc_title'><div class='progress-label3'>"+"Loading..."+"</div></div></td>";
					add += "<td style='width:13%;background-Color:#F9F9F9'><img src='img/character/character"+sex+"_"+title_next+".png' width='50%' height='auto'></td>";
					add += "</tr>";
					add += "</table>";
					add += "</div>";
					$("#"+j).append(add);
					progress("title",Arrtotal_process[7],"c",get_score);
					//alert(Arrtotal_process[7]);
					//alert(get_score);
			}
			else{//type!=0;代表是個別的成就類型的『等級』
					var add ="";
					add += "<div id='div-alert'class='font-MJ' style='color:red;font-size:16px;background-color:white'><img src='img/mission_icon/back.png'>&nbsp;<a href='#' class='back_achieve_total'>[返回成就總覽頁面]</a></div>";
					$("#"+j).append(add);
				for(var x=1; x<=5; x++){
					add = "";
					add += "<table id='t_special"+i+"_"+x+"' class='TB'>";
					add += "<tr>";
					add += "<td rowspan='3' style='width:20%' class='TB_side'><img src='img/reward/medal"+i+"_"+x+".png' width='70%' height='auto'></td>";
					add += "<td style='width:60%;' class='TB_head'>《 "+achieve_name[x-1]+" Lv"+level_degree[x-1]+" 》</td>";
					add += "<td rowspan='3' class='TB_side'><img src='img/reward/score_"+x+".png' width='70%' height='auto'></td>";
					add += "</tr>";
					add += "<tr>";
					add += "<td style='border-style:inset'>"+achieve_content[x-1]+"達("+"<span class='a_special'>"+Arrlevel[x-1]+"</span>/"+achieve_value[x-1]+")個"+"</td>";
					add += "</tr>";
					add += "<tr>";
					add += "<td class='TB_foot'><div id='progressbar"+i+"_"+x+"'><div class='progress-label'>"+"Loading..."+"</div></div></td>";
					add += "</tr>";
					add += "</table>";
					$("#"+j).append(add);
					if(x == level_now){
					 $("#t_special"+i+"_"+x).css({"border-style":"dashed","border-color":"red"});
					 $(".TB_side").css({"background-color":"#FFBB66"});
					 $(".TB_head").css({"background-color":"#FFBB66"});
					 $(".a_special").css({"color":"black"});
					}			
					$(".a_special").css({"color":"red"});					
					progress(x,Arrprocess[x-1],i);//傳值給進度條(迴圈跑到第幾個;陣列中的進度趴數;第幾種類型)				
				}
				
				$(".back_achieve_total").click(function(){//返回成就總覽按鈕
					window.location.assign('#main_page');
					location.reload();
				});	
				
			}
			//---------------------------------------------------------	
			//成就總覽中的『個別連結』	
			//---------------------------------------------------------	
			$(".achieve_type").click(function(){
				//alert($(this).find("span").attr("id"));
				var div_id;
				type = $(this).find("span").attr("id");
				if(type==1){
					div_id = $("#div-article_like").attr('id');
					window.location.assign('#another_page1');
				}
				else if(type==2){
					div_id = $("#div-article_be_liked").attr('id');
					window.location.assign('#another_page2');
				}
				else if(type==3){
					div_id = $("#div-article_reply").attr('id');
					window.location.assign('#another_page3');
				}
				else if(type==4){
					div_id = $("#div-article_be_replied").attr('id');
					window.location.assign('#another_page4');
				}
				else if(type==5){
					div_id = $("#div-article_eagle_publish").attr('id');
					window.location.assign('#another_page5');
				}
				else if(type==6){
					div_id = $("#div-article_request").attr('id');
					window.location.assign('#another_page6');
				}
				
				var send_data = {"type":type};
				$.ajax({//抓取學生的按讚數量，傳給function產生視圖
					"url":"getvalue_achievement.php",
					"data":send_data,
					"type":"POST",
					"dataType":"json",
					//"async": false,//改為同步執行，照正常邏輯執行
					"beforeSend": function(){},
					"success": function(data){
						console.log(data);//檢查用
						//alert("suc 6");
						cno 			= data["cno"];
						level_now    	= data["level_now"];
						Arrlevel 		= data["Arrlevel"];	
						Arrprocess 		= data["Arrprocess"];
						level_degree 	= data["level_degree"];
						achieve_name 	= data["achieve_name"];
						achieve_content = data["achieve_content"];
						achieve_value	= data["achieve_value"];						
						get_data(type,div_id);//產生圖示							
					},
					"error": function(data){
						//console.log(data);
						alert("error 7");	
					}	
				});		
				
			}); 
			//---------------------------------------------------------	
		};			 
	//---------------------------------------------------------	
	//顯示並產生進度表
	//---------------------------------------------------------		
		function progress(x,y,z,w){//x:迴圈跑到第幾個; y:陣列中的進度趴數; z:第幾種類型; w:陣列中的進度個數	
			var proLabel  = $(".progress-label"); //各項成就等級	
			var proLabel2 = $(".progress-label2");//成就總體>總個數
			var proLabel3 = $(".progress-label3");//成就頭銜
			var pro = $("#progressbar"+z+"_"+x);  //进度条div
			pro.progressbar({
			  value: false,  //初始化的值为0
			  change: function() {//当value值改变时，同时修改label的字			  
 				if(z==0 && x==0){ proLabel2.text("成就總體進度："+ w + "/30 個" ); }				
				else if(z==0 && x!=0){ $(".progress-label_"+x).text("成就進度："+ w + "/5 個" ); }			
				else if(z=="c" && x=="title"){ proLabel3.text("稱號進度："+ w + "/ " + title_score[title_next-1] + " 分" ); }						
				proLabel.text("完成進度："+ pro.progressbar( "value" ) + "%" );
			  },
			  complete: function() {//当进度条完成时，显示complate			  
				if(z==0 && x==0){ proLabel2.text("成就全部達成！");}
				else if(z==0 && x!=0){ $(".progress-label_"+x).text("成就進度："+ w + "/5 個" );}	
				else if(z=="c" && x=="title"){ proLabel3.text("已取得最高位階的稱號！");}
				proLabel.text("成就已達成！");
			  }
			});
			//延迟500毫秒调用修改value的函数
			if(y > 0){
				setTimeout(addValue, 500);
			}
			else{pro.progressbar("value",0);}			
		 
			//动态修改value的函数
			//var nowValue = pro.progressbar("value");//抓取目前的值 
			var nowValue = y;
			var start = 0;
			function addValue(){
				var pro = $("#progressbar"+z+"_"+x);
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
			<img src="img/achieve_icon/task.png">
			<div class="profile_info"><strong class="font-MJ">個人成就</strong><br><small class="font-MJ">Personal Achievement</small></div>
		</div>

		<h3>成就項目</h3>

		<ul>
			<li id="achieve_total"><a href="#main_page"><img src="img/achieve_icon/course.png"><span class="font-MJ">成就總覽</span></a></li>
			<li id="article_like"><a href="#another_page1"><img src="img/achieve_icon/like.png"><span class="font-MJ">文章按讚</span></a></li>
			<li id="article_be_liked"><a href="#another_page2"><img src="img/achieve_icon/liked.png"><span class="font-MJ">文章被按讚</span></a></li>
			<li id="article_reply"><a href="#another_page3"><img src="img/achieve_icon/reply.png"><span class="font-MJ">回覆文章</span></a></li>
			<li id="article_be_replied"><a href="#another_page4"><img src="img/achieve_icon/replied.png"><span class="font-MJ">文章被回覆</span></a></li>
			<li id="article_eagle_publish"><a href="#another_page5"><img src="img/achieve_icon/publish.png"><span class="font-MJ">鷹架發文</span></a></li>
			<li id="article_request"><a href="#another_page6"><img src="img/achieve_icon/request.png"><span class="font-MJ">請求推薦書籍</span></a></li>
			
		</ul>

	</div>
	<!--  成就總覽  -->
	<div data-role="page" id="main_page" data-theme="a" >

		<div data-role="header" data-position="fixed" data-tap-toggle="false" data-update-page-padding="false">
			<a href="#" data-slidemenu="#slidemenu" data-slideopen="false" data-icon="smico" data-corners="false" data-iconpos="notext">Menu</a>
			<h1 class="font-MJ">成就總覽</h1>
		</div>
		<div data-role="content" id="div-total_achieve"></div>
		
	</div>
	<!--  文章按讚  -->
	<div data-role="page" id="another_page1" data-theme="a">

		<div data-role="header" data-position="fixed" data-tap-toggle="false" data-update-page-padding="false">
			<a href="#" data-slidemenu="#slidemenu" data-slideopen="false" data-icon="smico" data-corners="false" data-iconpos="notext">Menu</a>
			<h1 class="font-MJ">文章按讚</h1>
		</div>
		<div data-role="content" align="center" style="overflow:visible;" id="div-article_like"></div>			

	</div>
	<!--  文章被按讚  -->
	<div data-role="page" id="another_page2" data-theme="a">

		<div data-role="header" data-position="fixed" data-tap-toggle="false" data-update-page-padding="false">
			<a href="#" data-slidemenu="#slidemenu" data-slideopen="false" data-icon="smico" data-corners="false" data-iconpos="notext">Menu</a>
			<h1 class="font-MJ">文章被按讚</h1>
		</div>
		<div data-role="content" align="center" style="overflow:visible;" id="div-article_be_liked"></div>		

	</div>
	<!--  回覆文章  -->
	<div data-role="page" id="another_page3" data-theme="a">

		<div data-role="header" data-position="fixed" data-tap-toggle="false" data-update-page-padding="false">
			<a href="#" data-slidemenu="#slidemenu" data-slideopen="false" data-icon="smico" data-corners="false" data-iconpos="notext">Menu</a>
			<h1 class="font-MJ">回覆文章</h1>
		</div>
		<div data-role="content" align="center" style="overflow:visible;" id="div-article_reply"></div>		
		
	</div>
	<!--  文章被回覆  -->
	<div data-role="page" id="another_page4" data-theme="a">

		<div data-role="header" data-position="fixed" data-tap-toggle="false" data-update-page-padding="false">
			<a href="#" data-slidemenu="#slidemenu" data-slideopen="false" data-icon="smico" data-corners="false" data-iconpos="notext">Menu</a>
			<h1 class="font-MJ">文章被回覆</h1>
		</div>
		<div data-role="content" align="center" style="overflow:visible;" id="div-article_be_replied"></div>	
		
	</div>
	<!--  鷹架發文  -->
	<div data-role="page" id="another_page5" data-theme="a">

		<div data-role="header" data-position="fixed" data-tap-toggle="false" data-update-page-padding="false">
			<a href="#" data-slidemenu="#slidemenu" data-slideopen="false" data-icon="smico" data-corners="false" data-iconpos="notext">Menu</a>
			<h1 class="font-MJ">鷹架發文</h1>
		</div>
		<div data-role="content" align="center" style="overflow:visible;" id="div-article_eagle_publish"></div>
		
	</div>
	<!--  請求推薦書籍  -->
	<div data-role="page" id="another_page6" data-theme="a">

		<div data-role="header" data-position="fixed" data-tap-toggle="false" data-update-page-padding="false">
			<a href="#" data-slidemenu="#slidemenu" data-slideopen="false" data-icon="smico" data-corners="false" data-iconpos="notext">Menu</a>
			<h1 class="font-MJ">請求推薦書籍</h1>
		</div>
		<div data-role="content" align="center" style="overflow:visible;" id="div-article_request"></div>	
		
	</div>		
</body>
</html>