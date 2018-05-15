<!DOCTYPE html>
<? session_start(); ?>
	<head>

		<script src="../../../../lib/jquery/basic/code.js"></script>
		<script src="../../../../lib/jquery/plugin/code.js"></script>
		<?php
		//---------------------------------------------------
		//設定與引用
		//---------------------------------------------------
		
			//SESSION
			@session_start();
		
			//啟用BUFFER
			@ob_start();
		
			//外掛設定檔
			require_once(str_repeat("../",4)."/config/config.php");
			 //外掛函式檔
			$funcs=array(
						APP_ROOT.'inc/code',
						APP_ROOT.'lib/php/db/code',
						APP_ROOT.'lib/php/array/code'
						);
			func_load($funcs,true);
			
		
			//清除並停用BUFFER
			@ob_end_clean();
			
			//建立連線 user
			//$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
		//---------------------------------------------------
		//END   設定與引用
		//---------------------------------------------------		 
		
		//---------------------------------------------------
		//CSS 設定
		//---------------------------------------------------
		?>
		<style>
		.up_show_draw_info_css
		{
			border: 1px solid gray;

			left:150px;
			max-width:400px;
			max-height:500px;
			position : absolute;
			top: -1580px;
			left:550px;
		}
            body
            {
				overflow:hidden;
            position:relative;
				}
			/*灰階特效*/
			.grays { 
				
            }
			table {
			table-layout: fixed;
			word-break: break-all;
			overflow:hidden;
			style="table-layout:fixed;
			word-wrap:break-word;
			word-break;
			break-all;
			overflow:hidden;
			white-space: nowrap;
			}
        </style>
	</head>

<body>
		
		
        
        
       <div id="debug" style="position:absolute; top:505px; left:8px;"></div>
        <?
        //預設讀入的資料
		
		//GET
			$user_id = (int)$_GET["user_id"];
			$book_sid = $_GET["book_sid"];
			$data_array = array();
	 	
		?>
        
        
		<!--========================================================
		//DEBUG文字列
		//========================================================-->
		<div style="left:0px; top:480px; position: absolute;" id="debug"></div>
		<script language="javascript">
		
		//事件 :  遮罩開關
		function set_hide(open_on,text)
		{
			if(open_on==false)
			{
				//$.unblockUI();  
			}else if(open_on==true)
			{
				//$.blockUI({ message: '<div style="z-index: 2000;">'+text+'</div>'});
			}
		}
		function add_debug(value)
		{
			if(0)
			{
				window.document.getElementById("debug").innerHTML = value+"<br>"+window.document.getElementById("debug").innerHTML;
			}
		}
		//set_hide(1,"讀取中");
		//========================================================
		//建立
		//========================================================
		var user_id = <? echo $user_id; ?>;
		var book_sid = '<? echo $book_sid;?>';
		
		
		//========================================================
		//圖片預載
		//========================================================
		
		//===========設定圖片預載陣列==============
		
		</script>
        
        <!--======================================================
		//Html 內文
		========================================================-->
        
        <div style="position:absolute; top:0px; left:0px; background-color:#333333; opacity:0.8; height:530px; width:1050px;"></div>
        <img src="img/book_page_all.png" width="1224" height="480" style="position:absolute; top:0px; left:0px; width: 1300px; height: 462px;">
        <!-- 標題欄位 -->
		<div id="book_name" style="position:absolute; top:20px; left:37px; overflow:hidden;white-space: nowrap; font-size:36px; color:#673616; font-weight: bold; width: 428px;">我喔喔喔喔喔喔</div>
        <!-- 評星欄位 -->
<div id="rec_star" style="position:absolute; top:124px; left:162px; font-size:24px; color:#673616; font-weight: bold;">我喔喔喔喔喔喔</div>
       	<img src="img/star_pot.png" style="position:absolute; top:110px; left:4px;">
        <img src="img/stars_pot.png" style="position:absolute; top:57px; left:1px;">
        
        <img id="rec_star1" src="img/r_1n.png" style="position:absolute; top:58px; left:156px;">
        <img id="rec_star2" src="img/r_2n.png" style="position:absolute; top:60px; left:234px;">
        <img id="rec_star3" src="img/r_3n.png" style="position:absolute; top:62px; left:314px;">
        <!-- 文字欄位 -->
        <div id="up_show_text"  style="position : absolute; top: 233px; left:34px;">
        <textarea cols=34 rows=9 style="font-size:20px;resize: none; " id="up_show_text_info" readOnly></textarea>
        </div>
        <img src="img/text_pot.png" style="position:absolute; top:181px; left:6px;">
        
        <!-- 繪圖欄位 -->
        <canvas id="up_show_draw_info" class="up_show_draw_info_css" width="700" height="400" style="position:absolute; top:68px; left:538px;" ></canvas>
        <img src="img/draw_pot.png" style="position:absolute; top:12px; left:505px;">
        <!-- 錄音欄位 -->
        
		<div id="recode_bar" style="position : absolute; top: 382px; left:547px;">
            <div id="player" style=" position:absolute; top:5px; left:35px">
                              <div class="ctrl">
                                  <div class="control">
                                      <div class="left">
                                          <div class="playback icon">
                                          </div>
                                      </div>
                                      <div class="volume right">
                                          <div class="mute icon left">
                                          </div>
                                          <div class="slider left">
                                              <div class="pace">
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              <div class="progress">
                                  <div class="slider">
                                      <div class="loaded">
                                      </div>
                                  <div class="pace">
                                  </div>
                              </div>
                              <div class="timer left">0:00</div>
                              </div>
    
                          </div>
          	</div>
        </div>
        <img src="img/recode_pot.png" style="position:absolute; top:291px; left:506px;">
        
        <!-- 離開紐 -->
        <img src="img/out.png" style="position:absolute; top:358px; left:884px;" onClick="close_page()">
        
       
        
        
<script>
		//========================================================
		//JS Function
		//========================================================
		function close_page()
		{
			window.parent.document.getElementById('read_page').innerHTML = "";
		}
		
		
		function go_read_page()
		{window.parent
			add_debug("go_read_page:觀看頁面:"+book_sid);
			add_debug("go_read_page:觀看頁面:"+user_id);
			var url = "./ajax/get_mssr_up_show_info.php";
			$.post(url, {
					sid:user_id,
					book_sid:book_sid
				}).success(function (data) 
				{
					add_debug("set_down_book:post:get_data -> "+data);

					data_array = JSON.parse(data);
		
					//==========SET有無上架========================================
					
					//==========SET書籍名稱========================================
					window.document.getElementById('book_name').innerHTML= data_array["book_name"];
					//==========SET評星內容========================================
					window.document.getElementById('rec_star').innerHTML = data_array['rec_reason_1'];
					window.document.getElementById('rec_star').innerHTML = window.document.getElementById('rec_star').innerHTML +"<BR>"+data_array['rec_reason_2'];
					
					window.document.getElementById('rec_star1').src = "img/r_1n.png";
					window.document.getElementById('rec_star2').src = "img/r_2n.png";
					window.document.getElementById('rec_star3').src = "img/r_3n.png";
					if(data_array["rec_rank"]!="" && data_array["rec_rank"]!=null)
					{
						
						window.document.getElementById('rec_star'+data_array["rec_rank"]).src = "img/r_"+data_array["rec_rank"]+".png";
					}

					//==========SET繪圖內容========================================
					if(data_array['rec_draw_type'] == "base64")
					{
						var canvas = document.getElementById('up_show_draw_info');
						document.getElementById('up_show_draw_info').style.backgroundColor = data_array['rec_draw_bc'];
						var context = canvas.getContext('2d');
						context.globalCompositeOperation = "copy";
		
						var img = new Image();
		
						img.onload = function () {
							context.drawImage(img, 0, 0);
						};
						img.src = data_array['rec_draw_data'];
					}
					else if(data_array['rec_draw_type'] == "bimg")
					{
						var canvas = document.getElementById('up_show_draw_info');
						var context = canvas.getContext('2d');
						context.clearRect(0,0,canvas.width,canvas.height);
						document.getElementById('up_show_draw_info').style.backgroundColor = "rgba(255, 255, 255, 0)";
						context.globalCompositeOperation = "copy";
						canvas.toDataURL(data_array['rec_draw_link']);
		
						var img = new Image();
		
						img.onload = function () {
							context.drawImage(img, 0, 0);
						};
						img.src = data_array['rec_draw_link'];
						echo_add("接收瀏覽-畫圖的連結 : "+data_array['rec_draw_link']);
					}
					else
					{
						var canvas = document.getElementById('up_show_draw_info');
						var context = canvas.getContext('2d');
						context.clearRect(0,0,canvas.width,canvas.height);
						document.getElementById('up_show_draw_info').style.backgroundColor = "rgba(255, 255, 255, 1)";
					}
					//==========SET文字內容========================================
					 document.getElementById('up_show_text_info').value ="一句話:"+data_array['rec_content_1']+"\n內容:"+data_array['rec_content_2']+"\n學到的事:"+data_array['rec_content_3'];
				/*
					//==========SET錄音內容========================================
					if(data_array["rec_record_book_sid"] != "" )
					{
						document.getElementById("recode_bar").style.display = "block";
						set_player(data_array["rec_record_book_sid"]);
					}else
					{
						document.getElementById("recode_bar").style.display = "none";
					}*/
				
				}).error(function(e){
					add_debug("set_down_book:post:error -> "+e );
				}).complete(function(e){
					
				});
				
				
		}
		go_read_page()
		</script>
</body>