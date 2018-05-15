<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 上架列表
//(內頁)  //主頁面 or 內頁
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",3).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();
    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

    ///---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        $user_id        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
		if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
        //GET


    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

	//---------------------------------------------------
	//SQL
	//---------------------------------------------------

?>
<!DOCTYPE HTML>
<Html>
<Head>
	<Title>上架</Title>
    <!-- 掛載 -->
    <script type="text/javascript" src="../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
    <script src="../js/select_thing.js" type="text/javascript"></script>
    <script src="../js/set_bookstore_action_log.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../css/btn.css">
	<style>

		.b0{
			position:absolute;
			width:76px;
			height:95px;
			background: url('./img/book.png') 0 0;
		}
		.b0:hover {
    		background: url('./img/book.png') 0 -190px;
		}
		.b1{
			position:absolute;
			width:76px;
			height:95px;
			background: url('./img/book.png') -76px 0;
		}
		.b1:hover {
    		background: url('./img/book.png') -76px -95px;
		}
		.b2{
			position:absolute;
			width:76px;
			height:95px;
			background: url('./img/book.png') -152px 0;
		}
		.b2:hover {
    		background: url('./img/book.png') -152px -190px;
		}
		.b3{
			position:absolute;
			width:76px;
			height:95px;
			background: url('./img/book.png') -228px 0;
		}
		.b3:hover {
    		background: url('./img/book.png') -228px -190px;
		}
		.s0{
			position:absolute;
			width:35px;
			height:34px;
			background: url('./img/star_line_big.png') 0 0;
		}
		.s1{
			position:absolute;
			width:35px;
			height:34px;
			background: url('./img/star_line_big.png') -35px 0;
		}
		.s2{
			position:absolute;
			width:35px;
			height:34px;
			background: url('./img/star_line_big.png') -70px 0;
		}
		.s3{
			position:absolute;
			width:35px;
			height:34px;
			background: url('./img/star_line_big.png') -105px 0;
		}
		.b0s{
			position:absolute;
			width:76px;
			height:95px;
			background: url('./img/book.png') 0 -95px;
		}
		.b1s{
			position:absolute;
			width:76px;
			height:95px;
			background: url('./img/book.png') -76px -95px;
		}
		.b2s{
			position:absolute;
			width:76px;
			height:95px;
			background: url('./img/book.png') -152px -95px;
		}
		.b3s{
			position:absolute;
			width:76px;
			height:95px;
			background: url('./img/book.png') -228px -95px;
		}

		.gray{
			filter: grayscale(100%);
			-webkit-filter: grayscale(100%);
			-moz-filter: grayscale(100%);
			-ms-filter: grayscale(100%);
			-o-filter: grayscale(100%);
		 }

		.nonono{
			position:absolute;
			width:37px;
			height:37px;
			background: url('img/no.png') 0 0;
		}
		.nonono:hover{
    		background: url('img/no.png') 0 -37px;
		}
		.text_1{

			font-size: 18px;
			padding: 1px 4px;
			color:#4F6279;
			font-weight:bold;
			font-family:Microsoft JhengHei;
		}
		#book_rate{
			position: relative;
			top:135px;
			left: 150px;
			width: 75px;
			height: 20px;
			/* background-color: #d19348;
			opacity: 0.5;
			border-radius: 10px;
 */

		}
		#click_through_rate1{
			position: relative;
			top:150px;
			left: 243px;
			width: 75px;
			height: 20px;
			/* background-color: #d19348;
			opacity: 0.5;
			border-radius: 10px;
 */

		}
		#click_through_rate2{
			position: relative;
			top:168px;
			left: 336px;
			width: 75px;
			height: 20px;
			/* background-color: #d19348;
			opacity: 0.5;
			border-radius: 10px;
 */

		}
		#click_through_rate3{
			position: relative;
			top:135px;
			left: 429px;
			width: 75px;
			height: 20px;
			/* background-color: #d19348;
			opacity: 0.5;
			border-radius: 10px;
 */

		}
		#click_through_rate4{
			position: relative;
			top:245px;
			left: 150px;
			width: 75px;
			height: 20px;
			/* background-color: #d19348;
			opacity: 0.5;
			border-radius: 10px;
 */

		}


		.eye_img{
			position: absolute;
			left: 3px;
		}
		.rate_number{
			position: absolute;
			left: 27px;
			top: -1px;

		}
	
		.rate_number , #total{
			font-size: 13px;
			color:#5B240C;
			font-weight: 500;
		}
		a{
			cursor:pointer;
		}
		#visitCount{
			display:block;
			width: 100%;
			text-align: left ;

		}
		.scoreWordArea{
			position:relative;
			color:#fff; 
			font-size:18px; 
			letter-spacing: 1.5px;
			font-weight: 600;

		}
		.scoreWords{
			position: absolute; 
			left:50%; 
			top:50%; 
			transform:translateX(-50%) translateY(-50%);
		}
		.scoreImg{
			padding-left: 60px;
		}
		
		
	</style>
</Head>
<body>

	<!--==================================================
    html內容
    ====================================================== -->
    <!-- 遮屏-->
	<div id="black" style="position:absolute; top:0px; left:0px; width:980px; height:500px; background-color:#000000; opacity:0.85; z-index:50;" onClick=""></div>
    <!-- 列表版面-->
	<div id="page1" style="position:absolute; top:0px; left:0px;z-index:60;">

	    <!-- 上架販售背景圖片 -->
	    	<img src="../img/back.png" style="position:absolute; left:35px; top: 52px; width: 886px; height: 431px;" border="0">
	    <!-- 書架 -->
	        <img src="./img/up_box2.png" style="position:absolute; left:75px; top: 84px;" border="0">
	   	<!-- 上架資訊及書籍資訊的圖 -->
			<img src="./img/info2.png" style="position:absolute; left:550px; top: 78px;" border="0">


	   	  	<div class="text_1" id="count_txt"  style="position:absolute; display:none; left:609px; top: 125px; width: 269px; font-size:24px;" border="0">上架量 : 10/10本</div>
		   <? for($i = 0; $i < 12  ; $i++){?>
	        <!-- 建立書籍列表-->
				<!-- 書-->
	      		<a id="book_<?php echo $i;?>" class="b0" onClick="click_book(<?php echo $i;?>)" style="position:absolute; left:<?php echo 155+($i%4*92);?>px; top:<?php echo 96+((int)($i/4)*110);?>px;  display:none; cursor:pointer;"></a>
	      		<!-- 書名 -->
	  	  		<div id="book_name_<?php echo $i;?>" onClick="click_book(<?php echo $i;?>)" style="position:absolute; height:31px; width:60px;  overflow:hidden; left:<?php echo 160+($i%4*92);?>px; top:<?php echo 102+((int)($i/4)*110);?>px; display:none; font-size:10px; cursor:pointer;word-break: break-all; "></div>
				<!-- 瀏覽率 -->
	  	  		<a id="book_rate_<?php echo $i;?>" onClick="click_book(<?php echo $i;?>);click_visit_count()" style="position: absolute;top:<?php echo 165+((int)($i/4)*110);?>px; left: <?php echo 153+($i%4*93);?>px; width: 75px;height: 20px; display:none;"> 
	            	<div class="eye_img">
	            		<img src="./img/eye.png" alt="">
	            	</div>
	            	<div class="rate_number">
						<span id="visit_people_<?php echo $i;?>">55255</span>
					</div>
	            </a>
	  	  		
	        <!-- <div id="book_star_<? echo $i;?>" onClick="click_book(<? echo $i;?>)" style="position:absolute; height:40px; width:76px; left:<? echo 296+($i%4*110);?>px; top:<? echo 96+((int)($i/4)*122);?>px; display:none; font-size:12px; cursor:pointer;">
	        	<a id="book_star_<? echo $i;?>_1" class="s0" style="position:absolute; left:0px;"></a>
	            <a id="book_star_<? echo $i;?>_2" class="s0" style="position:absolute; left:20px;"></a>
	            <a id="book_star_<? echo $i;?>_3" class="s0" style="position:absolute; left:40px;"></a>
	        </div>-->
			<? }?>
			<!-- 瀏覽率 -->
	          <!--   <a id="book_rate" onClick="click_through_rate()" > 
	            	<div class="eye_img">
	            		<img src="./img/eye.png" alt="">
	            	</div>
	            	<div class="rate_number">
						<span id="today_people">55255</span>
					</div>
	            </a> -->
	        <!-- 瀏覽率 -->
	         <!--    <a id="click_through_rate1" onClick="click_through_rate()" > 
	            	<div class="eye_img">
	            		<img src="./img/eye.png" alt="">
	            	</div>
	            	<div class="rate_number">
						<span id="today_people">52555</span>
					</div>
	            </a> -->
	        <!-- 瀏覽率 -->
	           <!--  <a id="click_through_rate2" > 
	            	<div class="eye_img">
	            		<img src="./img/eye.png" alt="">
	            	</div>
	            	<div class="rate_number">
						<span id="today_people">52555</span>
					</div>
	            </a>
	       

	              <a id="click_through_rate4" > 
	            	<div class="eye_img">
	            		<img src="./img/eye.png" alt="">
	            	</div>
	            	<div class="rate_number">
						<span id="today_people">52555</span>
					</div>
	            </a> -->
	        <img src="img/cool_cover.png?dd=12" style="position:absolute; top:362px; left:581px;">
	      	<a id="main_left_btn" class="btn_arrow_l" onClick="set_main_page(-1)" style="position:absolute; left:87px; top: 249px; cursor:pointer;display:none;"  border="0"></a>
	      	<a id="main_right_btn" class="btn_arrow_r" onClick="set_main_page(1)" style="position:absolute; left:531px; top: 249px; cursor:pointer;display:none;" border="0"></a>
	      	<a id="down_btn" class="btn_unshelf" onClick="set_down()" style="position:absolute; left:588px; top: 364px; cursor:pointer;display:none;" ></a>
	      	<a id="up_btn" class="btn_onshelf" onClick="window.parent.set_action_bookstore_log(window.parent.user_id,'e37',window.parent.action_on);set_up_page(1)" style="position:absolute; left:666px; top: 364px; cursor:pointer;display:none;" ></a>
	        <a id="out"  class="btn_close" onClick="out()" style="position:absolute; left:821px; top: 363px; cursor:pointer;" ></a>
	      	<div class="text_1" id = "chick_book_name" onClick="cover(window.document.getElementById('chick_book_name').innerHTML,1)" style="cursor:help; position:absolute; left:606px; top: 227px; overflow:hidden; width: 257px; height: 30px; line-height:40px; font-size:24px;">我是書籍名稱</div>
	      	<div class="text_1" id = "chick_book_author" onClick="cover(window.document.getElementById('chick_book_author').innerHTML,1)" style="cursor:help; position:absolute; line-height:40px; left:606px; top: 265px; overflow:hidden; width: 257px; height: 30px; line-height:40px; font-size:24px;">我是書籍作者</div>
	      	<div class="text_1" id = "chick_book_publisher" onClick="cover(window.document.getElementById('chick_book_publisher').innerHTML,1)" style="cursor:help; position:absolute; line-height:40px; left:606px; top: 303px; overflow:hidden; width: 257px; height: 30px; line-height:40px; font-size:24px;">我是書籍出版社</div>
	        <a id="book_sc_1" class="s0" style="position:absolute; top:184px; left:724px;cursor:help;"></a>
	        <a id="book_sc_2" class="s0" style="position:absolute; top:184px; left:761px;cursor:help;"></a>
	        <a id="book_sc_3" class="s0" style="position:absolute; top:184px; left:797px;cursor:help;"></a>
	        <!--瀏覽按鈕-->
	        <a id="read_rec_btn" class="btn_read" src="./img/read.png" onClick="window.parent.set_action_bookstore_log(window.parent.user_id,'e8',window.parent.action_on);set_visit_shelf_book_rate();" style="position:absolute; left:742px; top: 364px; display:none; cursor:pointer;"></a>
	</div>
    <!-- 選擇的版面-->
    <div id="page2"  style="position:absolute; top:0px; left:0px; height:500px;width: 960px;z-index:80; display:none;">
    	
	        <!-- 上架頁面左半部 -->
	        <div  style="position:absolute;  left:-10px; top: 80px;">
	        	<div style="position: relative;">
	        		<img src="./img/up_ho.png" border="0">
	       
	      			<div id="up_type" style="position:absolute;  left:50px; top: 50px; color:#000; width:280px; font-size:18px; font-weight:600;letter-spacing:1px"></div> 
	      		</div>
	      		<!--獎盃介紹-->
	      		<div style="position:relative;left:15px; top:-15px;">
					<div class="scoreWordArea" >
						<img src="./img/small5.png" class="scoreImg"  border="0">
						<span class="scoreWords" >金盃(五分)</span>

					</div>
					<div class="scoreWordArea">
						<img src="./img/small4.png" class="scoreImg"  border="0">
						<span class="scoreWords" >銀盃(四分)</span>
						
					</div>
					<div class="scoreWordArea">
						<img src="./img/small3.png" class="scoreImg"  border="0">
						<span class="scoreWords" >銅盃(三分)</span>
						
					</div>
					<div class="scoreWordArea">
						<img src="./img/small2.png" class="scoreImg"  border="0">
						<span class="scoreWords" >二分</span>
						
					</div>
					<div class="scoreWordArea">
						<img src="./img/small1.png" class="scoreImg"  border="0">
						<span class="scoreWords" >一分</span>
						
					</div>
						
				
				</div>
	      	</div>
	      	<!-- 上架頁面右半部-->
	      	<div style="position:absolute; right:10px; top:65px;">
	        	<img src="../img/back_s.png" width="595" border="0">
	        	<div id="iframe" style="position:relative;top:0px;left:0px;">
	        	</div>
	        	<div >
	         		<!-- 上一頁按鈕 -->
		        	<a id="left_btn" class="btn_arrow_l" onClick="set_page(-1)"  style="position:absolute; left:50px; bottom:12px ;display:none;"></a>
		        	<!-- 下一頁按鈕 -->
		        	<a id="right_btn" class="btn_arrow_r" onClick="set_page(1)" style="position:absolute; left:295px;bottom: 12px;display:none;"></a>
		        	<!-- 幾頁數字 -->
		      		<div id="page_text" style="position:absolute; bottom:20px; left:150px; width:150px; height:47px; font-size: 36px;  font-weight: bold; color: #406031;"></div>
		      		<!-- 確定按鈕 -->
		    		<a id="set_btn" class="btn_yes"  onClick="set_shelf_no()" style="position:absolute; right:120px; bottom: 3px; cursor:pointer; display:none;"></a>
	    		</div>
	        </div>
	         
	         
	    	<!-- 離開按鈕 -->
	        <a id="out" class="btn_close" onClick="set_up_page(0)" style="position:absolute; right:35px; top: 393px; cursor:pointer;"></a>
	        <!-- 說明按鈕 -->
	      	<a class="btn_help" onClick="open_helper(11)" style="position:absolute; top:-3px; left:899px; cursor:pointer; z-index:64;"></a>
    </div>
	<!-- 說明按鈕 -->
    <a class="btn_help" onClick="open_helper(12)" style="position:absolute; top:0px; left:895px; cursor:pointer; z-index:64;"></a>
	<div id="helper" style="position:absolute; top:-8px; left:-8px; width:1040px; height:480px; display:none; overflow:hidden; z-index:82;"></div>

    <!-- 改過 -->
	<img src="./img/tittle2.png" style="position:absolute;z-index:81; left:300px; top: -2px;" border="0">

	
<script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
    //console.log(location.href);
	var max_count = 0;
	var on_chick = -1;
	//var main_page = 1 ;

	var main_max_page = 1 ;
	//var page = 1 ;
	var max_page = 1 ;
	var book_info = new Array();
	var chick_sid='';
	
	var home_on=window.parent.home_on;
	var home_id=window.parent.home_id;
	var user_id=window.parent.user_id;
	


	if(home_id != user_id)
	{
		home_on = 'other';
	}

	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	function out()
	{
		window.parent.set_page("");
	}

	/*cover 啟用器的用法
	 cover("這嘎");
	 cover("這嘎",1);
	 cover("這嘎",2,function(){echo("哈哈");});
	 cover("喔喔?! 讀取失敗了喔  請確認網路連",2,function(){get_booking_count();});
	*/
	//cover 點選器
	function delayExecute(proc) {
		var x = 100;
		var hnd = window.setInterval(function () {
			if(window.parent.cover_click ==1 )
			{//點選確定的狀況
				window.parent.cover_click = -1;
				window.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選確定");
				proc();
				cover("");
			}
			else if(window.parent.cover_click ==0 )
			{//點選取消的狀況
				window.parent.cover_click = -1;
				window.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選取消");
				cover("");
			}
		}, x);
	}
	//cover
	function cover(text,type,proc)
	{

		window.parent.cover(text,type);
		if(type == 2)
		{
			delayExecute(proc);
		}
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}
	//點選設定圖
	function click_book(value)
	{
		window.parent.read_on = value + 12*(window.parent.page_list["shelf"]-1);
		window.parent.click_book_sid = book_info[value]["book_sid"];
		window.parent.click_book_name = book_info[value]["book_name"];

		

		window.document.getElementById("book_sc_1").className = "s"+book_info[value]["draw"];
		window.document.getElementById("book_sc_2").className = "s"+book_info[value]["text"];
		window.document.getElementById("book_sc_3").className = "s"+book_info[value]["record"];
		window.parent.click_book_star_1 = book_info[value]["draw"];
		window.parent.click_book_star_2 = book_info[value]["text"];
		window.parent.click_book_star_3 = book_info[value]["record"];

		aboutComment(value);

		


		for(var i = 0 ; i < 12 ; i++)
		{
			window.document.getElementById("book_"+i).className = "b"+book_info[i]["style"]+"";
		}
		window.document.getElementById("read_rec_btn").style.display = "block";
		if(window.parent.home_on == 'user')window.document.getElementById("down_btn").style.display = "block";
		window.document.getElementById("chick_book_name").innerHTML ="書名 : "+book_info[value]["book_name"];
		window.document.getElementById("chick_book_author").innerHTML ="作者 : "+book_info[value]["book_author"];
		window.document.getElementById("chick_book_publisher").innerHTML ="出版社 : "+book_info[value]["book_publisher"];

		window.document.getElementById("book_"+value).className = "b"+book_info[value]["style"]+"s";
	}
	//轉換至觀看頁面

	function read_rec(){
		// return false;
		cover("讀取中");
		<? if($_SESSION["forum_flag"]&&$_SESSION["forum_flag_home"]){?>
		window.parent.set_page('page_rec_read_v_forum');
		
		<? }else{ ?>
		window.parent.set_page('page_rec_read');
		
		<? } ?>

		
	}

	//若老師有公開留言,點選獎盃顯示老師的評語及時間
	function aboutComment(value){

			var drawClass= "score_"+book_info[value]["draw"];
			var textClass = "score_"+book_info[value]["text"];
			var recordClass = "score_"+book_info[value]["record"];
			//console.log(textClass);

			var bookName=book_info[value]["book_name"];

			var drawComment=book_info[value]["drawComment"];
			var textComment=book_info[value]["textComment"];
			var recordComment=book_info[value]["recordComment"];
			var drawCommentDate=book_info[value]["drawCommentDate"];
			var textCommentDate=book_info[value]["textCommentDate"];
			var recordCommentDate=book_info[value]["recordCommentDate"];
			var drawCommentPublic=book_info[value]["drawCommentPublic"];
			var textCommentPublic=book_info[value]["textCommentPublic"];
			var recordCommentPublic=book_info[value]["recordCommentPublic"];

			//日期去除時分秒
			var drawTime = new Date(drawCommentDate);
			var drawYear = drawTime.getFullYear(drawCommentDate) ;
			var drawMonth=drawTime.getMonth(drawCommentDate)+1;
			var drawDate = drawTime.getDate(drawCommentDate);
			var drawComTime=drawYear+"-"+drawMonth+"-"+drawDate;

			var textTime = new Date(textCommentDate);
			var textYear = textTime.getFullYear(textCommentDate) ;
			var textMonth=textTime.getMonth(textCommentDate)+1;
			var textDate = textTime.getDate(textCommentDate);
			var textComTime=textYear+"-"+textMonth+"-"+textDate;

			var recordTime= new Date(recordCommentDate);
			var recordYear = recordTime.getFullYear(recordCommentDate) ;
			var recordMonth=recordTime.getMonth(recordCommentDate)+1;
			var recordDate = recordTime.getDate(recordCommentDate);
			var recordComTime=recordYear+"-"+recordMonth+"-"+recordDate;
					


			$("#book_sc_1").on("click",function(){

				//若留言有公開. 判斷是否有評語

				if(drawCommentPublic==2){

					if(drawComment==0){

						cover('繪畫推薦獲得的獎盃',1);

					}else{

						cover("<span style='display:block; text-align:center;padding-top:10px;'>"+bookName+"</span>"
							+"<span style='display:block; text-align:center;padding-top:10px;font-size:18px;font-weight:600;color:#54030f;'>"+"繪畫推薦獲得的獎盃</span>"
							+"<hr><div  class='"+drawClass+"'></div>"
							+"<div style=' padding-top:10px;'>"
							+"<span style=' font-size:20px; display:block;text-align:left;padding-left:140px;'>老師評語:</span>"
							+"<span style=' font-size:18px; font-weight:500;display:block;text-align:left;padding:10px;padding-left:160px;'>"+drawComment+"</span> "
							+"<span style=' font-size:18px; font-weight:500;display:block;text-align:right;'>"+drawComTime+"</span>"
							+"</div> " ,1);
					}

				}else{
					
					cover('繪畫推薦獲得的獎盃',1);
				}

				

			});

			$("#book_sc_2").on("click",function(){

				if(textCommentPublic==2){

					if(textComment==0){

						cover('文字推薦獲得的獎盃',1);

					}else{

						cover("<span style='display:block; text-align:center;padding-top:10px;'>"+bookName+"</span>"
							+"<span style='display:block; text-align:center;padding-top:10px;font-size:18px;font-weight:600;color:#54030f;'>"+"文字推薦獲得的獎盃</span>"
							+"<hr><div  class='"+textClass+"'></div>"
							+"<div style=' padding-top:10px;'>"
							+"<span style=' font-size:20px; display:block;text-align:left;padding-left:140px;'>老師評語:</span>"
							+"<span style=' font-size:18px; font-weight:500;display:block;text-align:left;padding:10px;padding-left:160px;'>"+textComment+"</span> "
							+"<span style=' font-size:18px; font-weight:500;display:block;text-align:right;'>"+textComTime+"</span>"
							+"</div> " ,1);
					}

				}else{
					
					cover('文字推薦獲得的獎盃',1);
				}

				

			});
			
			$("#book_sc_3").on("click",function(){

				if(recordCommentPublic==2){

					if(textComment==0){

						cover('錄音推薦獲得的獎盃',1);

					}else{

							cover("<span style='display:block; text-align:center;padding-top:10px;'>"+bookName+"</span>"
							+"<span style='display:block; text-align:center;padding-top:10px;font-size:18px;font-weight:600;color:#54030f;'>"+"錄音推薦獲得的獎盃</span>"
							+"<hr><div  class='"+recordClass+"'></div>"
							+"<div style=' padding-top:10px;'>"
							+"<span style=' font-size:20px; display:block;text-align:left;padding-left:140px;'>老師評語:</span>"
							+"<span style=' font-size:18px; font-weight:500;display:block;text-align:left;padding:10px;padding-left:160px;'>"+recordComment+"</span> "
							+"<span style=' font-size:18px; font-weight:500;display:block;text-align:right;'>"+recordComTime+"</span>"
							+"</div> " ,1);
					}

				}else{
					
					cover('錄音推薦獲得的獎盃',1);
				}

				

			});

	}



	//紀錄瀏覽率

		
	function set_visit_shelf_book_rate(){

	
		if(home_on == "other"){



				echo("set_visit_shelf_book_rate:初始開始:紀錄瀏覽率");
				cover("請稍後");
				
				var url = "./ajax/set_visit_shelf_book_rate.php";

				// console.log(window.parent.user_id);
				// console.log(window.parent.home_id);
				// console.log(window.parent.user_permission);
				// console.log(window.parent.click_book_sid);
				
				// return false;


				$.post(url, {

							user_id:window.parent.user_id,
							home_id:window.parent.home_id,
							user_permission:window.parent.user_permission,
							book_sid:window.parent.click_book_sid

					}).success(function (data)
					{
						// console.log(data);


						if(data[0]!="{")
						{
							echo("AJAX:success:set_visit_shelf_book_rate():瀏覽率寫入:資料庫發生問題");
							cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員",2,function(){set_visit_shelf_book_rate();});
							return false;
						}

						data_array = JSON.parse(data);

						// console.log(data);
						echo("AJAX:success:set_visit_shelf_book_rate():瀏覽率寫入:已讀出:"+data);
						if(data_array["error"]!="")
						{
							cover(data_array["error"]);
							return false;
						}
						if(data_array["echo"]!="")
						{
							cover(data_array["echo"],1);

						}else
						{
							
						}

						read_rec();

					}).error(function(e){
						console.log('error');
						cover("");
						window.alert("連線失敗請重新讀取/存檔");
					}).complete(function(e){
						echo("AJAX:complete:set_visit_shelf_book_rate():瀏覽率寫入:");
					});


		}else{

			read_rec();

		}

	}




// 跳窗顯示的瀏覽率

	function click_visit_count(){

	
		if(home_on == "user"){



				echo("click_visit_count:初始開始:撈出瀏覽率");
				cover("請稍後");
				
				var url = "./ajax/get_visit_shelf_book_rate.php";
				
				// console.log(window.parent.user_id);
				// console.log(window.parent.home_id);
				// console.log(window.parent.user_permission);
				// console.log(window.parent.click_book_sid);
				// console.log(window.parent.click_book_name);
				// return false;


				$.post(url, {

							user_id:window.parent.user_id,
							home_id:window.parent.home_id,
							user_permission:window.parent.user_permission,
							book_sid:window.parent.click_book_sid,
							book_name:window.parent.click_book_name

							

					}).success(function (data)
					{
						// console.log(data);


						if(data[0]!="{")
						{
							echo("AJAX:success:click_visit_count():瀏覽率寫入:資料庫發生問題");
							cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員",2,function(){click_visit_count();});
							return false;
						}

						data_array = JSON.parse(data);

						// console.log(data);

						echo("AJAX:success:click_visit_count():瀏覽率:已讀出:"+data);
						if(data_array["error"]!="")
						{
							cover(data_array["error"]);
							return false;
						}
						if(data_array["echo"]!="")
						{
							cover(data_array["echo"],1);

						}else
						{
							on_shelf_date = data_array["on_shelf_date"];

							var time = new Date(on_shelf_date);
							// console.log(time);
							var year = time.getFullYear(on_shelf_date) ;
							var month=time.getMonth(on_shelf_date)+1;
							var date = time.getDate(on_shelf_date);
							// console.log(year);
							// console.log(month);console.log(date);

							this_time_visit_count= data_array["this_time_visit_count"];
							total_visit_count = data_array["total_visit_count"];
							avg_visit_count= data_array["avg_visit_count"];
							avg_every_count= data_array["avg_every_count"];
							click_book_name= data_array["book_name"];

							// console.log(click_book_name);



							// console.log(on_shelf_date);
							cover('<u ><span>'+ click_book_name +'</span></u>'
								+'<span style="display:block;text-align:left;padding:3px 80px; padding-top:10px;">上架時間：'+ year+'-'+month+'-'+date+'</span>'
								+'<span style="display:block;text-align:left;padding:3px 80px;">觀看次數：'+this_time_visit_count+'次(一天平均'+avg_visit_count+'次)</span>'
								+'<span style="display:block;text-align:left;padding:3px 80px;">累積觀看次數：'+total_visit_count+'次(一天平均'+avg_every_count+'次)</span>',1);
						}

						

					}).error(function(e){
						console.log('error');
						cover("");
						window.alert("連線失敗請重新讀取/存檔");
					}).complete(function(e){
						echo("AJAX:complete:click_visit_count():撈出瀏覽率:");
					});


		}else{


		}

	}




	//=========MAIN=============
	function main()
	{

		if(window.parent.auth_open_publish == 1)window.document.getElementById("up_type").innerHTML = "至少做好兩項以上的推薦才可以上架, 不包括評星";
		if(window.parent.auth_open_publish == 2)window.document.getElementById("up_type").innerHTML = "老師同意才可以上架";
		if(window.parent.auth_open_publish == 3)window.document.getElementById("up_type").innerHTML = "老師指導4分以上才可以上架";
		if(window.parent.auth_open_publish == 4)window.document.getElementById("up_type").innerHTML = "至少做好三項推薦才可以上架, 不包括評星";
		if(window.parent.auth_open_publish == 5)window.document.getElementById("up_type").innerHTML = "至少做好四項推薦以上才可以上架,不包括評星";

		echo("main:初始開始:讀取架上列表");
		cover("讀取架上列表")
		var url = "./ajax/get_shelf_info.php";

        // console.log(window.parent.home_id);
        //console.log(window.parent.user_permission);
        //console.log(window.parent.page_list["shelf"]);

		$.post(url, {
					user_id:window.parent.home_id,
					user_permission:window.parent.user_permission,
					page:window.parent.page_list["shelf"],
					
			}).success(function (data)
			{
				// console.log(data);

				if(data[0]!="{")
				{
					echo("AJAX:success:main():讀取架上列表:資料庫發生問題");
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員",2,function(){main();});
					return false;
				}

				data_array = JSON.parse(data);
				echo("AJAX:success:main():讀取架上列表:已讀出:"+data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					cover(data_array["echo"],1);

				}else
				{
					//教師+學生開啟分頁
					//if(!window.parent.auth_i_s)
					//{
						echo("最大頁數"+data_array["all_count"]);
						window.parent.read_max_count = data_array["all_count"];
						if(data_array["all_count"]!=0)main_max_page=((Math.floor(data_array["all_count"]))/12);
						else main_max_page=0;
						set_main_page_btn();
					//}

					for(var i = 0 ; i < 12;i++)
					{
						book_info[i] = new Array();

						if(i < data_array["count"])
						{
							//檢核問題書籍  並自動下架
							if(data_array[i]["book_verified"] == 2 || data_array[i]["has_black"])
							{

								window.parent.click_book_sid= data_array[i]["book_sid"];
								set_shelf_out(0);
								return false;

							}

							book_info[i]["book_sid"]= data_array[i]["book_sid"];
							book_info[i]["book_name"]= data_array[i]["book_name"];
							book_info[i]["draw"]= data_array[i]["draw"];
							book_info[i]["text"]= data_array[i]["text"];
							book_info[i]["record"]= data_array[i]["record"];
							book_info[i]["score"]= data_array[i]["score"];
							book_info[i]["count"]= data_array[i]["count"];
							book_info[i]["book_author"]= data_array[i]["book_author"];
							book_info[i]["book_publisher"]= data_array[i]["book_publisher"];
							book_info[i]["total_visit_count"]=data_array[i]["total_visit_count"];
							book_info[i]["textComment"]= data_array[i]["textComment"];
							book_info[i]["drawComment"]= data_array[i]["drawComment"];
							book_info[i]["recordComment"]= data_array[i]["recordComment"];
							book_info[i]["drawCommentDate"]=data_array[i]["drawCommentDate"];
							book_info[i]["textCommentDate"]=data_array[i]["textCommentDate"];
							book_info[i]["recordCommentDate"]=data_array[i]["recordCommentDate"];
							book_info[i]["drawCommentPublic"]=data_array[i]["drawCommentPublic"];
							book_info[i]["textCommentPublic"]=data_array[i]["textCommentPublic"];
							book_info[i]["recordCommentPublic"]=data_array[i]["recordCommentPublic"];


							score = Math.round(data_array[i]["score"])-1;
							if(score <= 1) score = 1;
							if(score >=  Math.round(data_array[i]["count"])+2) score = Math.round(data_array[i]["count"])+1;
							book_info[i]["style"] = score - 1;

							window.document.getElementById("book_"+i).style.display = "block";
							window.document.getElementById("book_name_"+i).style.display = "block";
							window.document.getElementById("book_rate_"+i).style.display = "block";
							window.document.getElementById("visit_people_"+i).innerHTML= data_array[i]["total_visit_count"];
						//	window.document.getElementById("book_star_"+i).style.display = "block";
						//	window.document.getElementById("book_star_"+i+"_1").className = "s"+book_info[i]["draw"];
						//	window.document.getElementById("book_star_"+i+"_2").className = "s"+book_info[i]["text"];
						//	window.document.getElementById("book_star_"+i+"_3").className = "s"+book_info[i]["record"];
							window.document.getElementById("book_name_"+i).innerHTML = data_array[i]["book_name"];

							// console.log("bookid:"+book_info[i]["book_sid"]);
							// console.log("文字:"+book_info[i]["textComment"]);
							// console.log("畫圖:"+book_info[i]["drawComment"]);
							// console.log("錄音:"+book_info[i]["recordComment"]);
							// console.log("文字時間:"+book_info[i]["textCommentDate"]);
							// console.log("畫圖時間:"+book_info[i]["drawCommentDate"]);
							// console.log("錄音時間:"+book_info[i]["recordCommentDate"]);
							// console.log("畫圖公開:"+ book_info[i]["drawCommentPublic"]);
							// console.log("文字公開:"+ book_info[i]["textCommentPublic"]);
							// console.log("錄音公開:"+ book_info[i]["recordCommentPublic"]);

							

						}else
						{
							book_info[i]["book_sid"]= "";
							book_info[i]["book_name"]= "";
							window.document.getElementById("book_"+i).style.display = "none";
						//	window.document.getElementById("book_star_"+i).style.display = "none";
							window.document.getElementById("book_name_"+i).style.display = "none";
							window.document.getElementById("book_rate_"+i).style.display = "none";
						}
					}

					for(var i = 0 ; i < 12 ; i++)
					{
						window.document.getElementById("book_"+i).className = "b"+book_info[i]["style"]+"";
					}
					if(window.parent.auth_i_s !=1)//非學生可以無限上架 ((爽
					{
						window.document.getElementById("count_txt").style.display = "block";
						window.document.getElementById("count_txt").innerHTML = "上架量 : "+data_array["all_count"]+'本 / (無上限)';
						if(window.parent.home_on!="user")
						{
							window.document.getElementById("up_btn").style.display = "none";
						}
						else
						{
							window.document.getElementById("up_btn").style.display = "block";
						}
					}
					else
					{
						window.document.getElementById("count_txt").style.display = "block";
						window.document.getElementById("count_txt").innerHTML = "上架量 : "+data_array["all_count"]+'本 '+"/"+window.parent.auth_open_publish_cno+"本 ";
						if(window.parent.auth_open_publish_cno <= data_array["all_count"] || window.parent.home_on!="user")
						{
							window.document.getElementById("up_btn").style.display = "none";
						}
						else
						{
							window.document.getElementById("up_btn").style.display = "block";
						}

					}

					//window.document.getElementById("up_btn").style.top = (46+(Math.floor(data_array["count"]/4)*122))+"px";
					//window.document.getElementById("up_btn").style.left = Math.floor(290+(data_array["count"]%4*110))+"px";
					if( !window.parent.auth_i_s)
					{

						if(window.parent.parent.help_cover["bookstore_shelf_help"])
						{
							//cover("現在老師可以無限制<BR>上架書籍了喔！",1);
							window.parent.parent.help_cover["bookstore_shelf_help"] = false;
						}
					}

					window.document.getElementById("chick_book_name").innerHTML = "";
					window.document.getElementById("chick_book_author").innerHTML = "";
					window.document.getElementById("chick_book_publisher").innerHTML = "";
					window.document.getElementById("book_sc_1").className = "";
					window.document.getElementById("book_sc_2").className = "";
					window.document.getElementById("book_sc_3").className = "";
					window.document.getElementById("down_btn").style.display = "none";
					window.document.getElementById("read_rec_btn").style.display = "none";



					if(window.parent.book_sid)
					{

						for(var i = 0 ; i < 12 ;i++)
						{
							if(i < data_array["count"])
							{
								if(window.parent.book_sid == book_info[i]["book_sid"])
								{
									click_book(i);
									window.parent.book_sid = "";
									read_rec()
									
									return false ;
								}


							}
						}

						if(window.parent.page_list["shelf"] >= main_max_page)
						{//找不到書
							cover("此書籍已經下架囉!",1);
							window.parent.book_sid = "";


						}else
						{
							set_main_page(1);


						}

						return false ;

					}

					cover("");

				}
				



			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");

			}).complete(function(e){
				echo("AJAX:complete:main():讀取架上列表:");
			});
	}
	//設定頁面模式
	function set_up_page(value)
	{
		cover("讀取中");
		if(value ==1)
		{
			window.document.getElementById("black").style.zIndex = "70";
			window.document.getElementById("page1").style.left = "-221px";
			window.document.getElementById("page2").style.display = "block";
			window.document.getElementById("set_btn").style.display = "none";
			get_shelf_count();
		}else if(value ==0)
		{
			window.document.getElementById("black").style.zIndex = "50";
			window.document.getElementById("page1").style.left = "0px";
			window.document.getElementById("page2").style.display = "none";
			main(window.parent.page_list["shelf"]);
		}

	}
	//設定上架
	function set_shelf_no()
	{
		echo("set_shelf:初始開始:開始上架書籍+->"+chick_sid);
		cover("上架中請稍後");
		window.parent.set_action_bookstore_log(window.parent.user_id,'e6',window.parent.action_on);

		var url = "./ajax/set_shelf_on.php";

		// console.log(chick_sid);

		$.post(url, {
					user_id:window.parent.home_id,
					user_permission:window.parent.user_permission,
					book_sid:chick_sid
			}).success(function (data)
			{

				if(data[0]!="{")
				{
					echo("AJAX:success:set_shelf_no():上架寫入:資料庫發生問題");
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員",2,function(){set_shelf_no();});
					return false;
				}

				data_array = JSON.parse(data);
				echo("AJAX:success:set_shelf_no():上架寫入:已讀出:"+data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					cover(data_array["echo"],1);

				}else
				{

					window.document.getElementById("chick_book_name").innerHTML = "";
					window.document.getElementById("chick_book_author").innerHTML = "";
					window.document.getElementById("chick_book_publisher").innerHTML = "";

					window.document.getElementById("down_btn").style.display = "none";
					window.document.getElementById("read_rec_btn").style.display = "none";
					on_chick = -1;
					set_up_page(0);
					for(var i = 0 ; i < 12 ; i++)
					{
						window.document.getElementById("book_"+i).className = "b"+book_info[i]["style"]+"";
					}
				}


			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){
				echo("AJAX:complete:set_shelf_no():上架寫入:");
			});

	}
	function set_down()
	{
		cover("你確定要下架這本書籍嗎?<BR><a style='color:#903;'>"+window.document.getElementById('chick_book_name').innerHTML+"</a>",2,function(){set_shelf_out(on_chick)});
		;
	}
	//設定下架
	function set_shelf_out(value)
	{
		echo("set_shelf:初始開始:開始上架書籍");
		cover("下架中請稍後");
		window.parent.set_action_bookstore_log(window.parent.user_id,'e7',window.parent.action_on);

		var url = "./ajax/set_shelf_out.php";
		$.post(url, {
					user_id:window.parent.home_id,
					user_permission:window.parent.user_permission,
					book_sid:window.parent.click_book_sid
			}).success(function (data)
			{
				// console.log(data);
				if(data[0]!="{")
				{
					echo("AJAX:success:set_shelf_out():下架寫入:資料庫發生問題");
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員",2,function(){set_shelf_out(value);});
					return false;
				}

				data_array = JSON.parse(data);
				echo("AJAX:success:set_shelf_out():下架寫入:已讀出:"+data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					cover(data_array["echo"],1);

				}else
				{
					window.document.getElementById("chick_book_name").innerHTML = "";
					window.document.getElementById("chick_book_author").innerHTML = "";
					window.document.getElementById("chick_book_publisher").innerHTML = "";
					window.document.getElementById("down_btn").style.display = "none";
					window.document.getElementById("read_rec_btn").style.display = "none";
					on_chick = -1;
					set_up_page(0);
					for(var i = 0 ; i < 12 ; i++)
					{

						window.document.getElementById("book_"+i).className = "b"+book_info[i]["style"]+"";
					}

				}

			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){
				echo("AJAX:complete:set_shelf_out():下架寫入:");
			});

	}
	//讀取可上架數量
	function get_shelf_count()
	{
		echo("get_shelf_count:初始開始:讀取可上架筆數 教師的決則->"+window.parent.auth_open_publish);
		cover("讀可上架頁面")
		var url = "./ajax/get_shelf_count.php";
		$.post(url, {
					user_id:window.parent.home_id,
					user_permission:window.parent.user_permission,
					auth_open_publish:window.parent.auth_open_publish,
					i_s:window.parent.auth_i_s
			}).success(function (data)
			{

				if(data[0]!="{")
				{
					echo("AJAX:success:get_shelf_count():讀取可上架筆數:資料庫發生問題");
					cover("資料庫好像有點問題呢，請再試試看<BR>或著通知系統人員",2,function(){get_shelf_count();});
					return false;
				}

				data_array = JSON.parse(data);
				echo("AJAX:success:get_shelf_count():讀取可上架筆數:已讀出:"+data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					cover(data_array["echo"],1);

				}else
				{
					max_count = data_array["count"];
					if(max_count == 0)
					{max_page = 1;}
					else
					{max_page = Math.floor((Math.floor(max_count)-1)/12)+1;}

					set_page(0);
					//cover("");
				}

			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");;
			}).complete(function(e){
				echo("AJAX:complete:get_shelf_count():讀取可上架筆數:");
			});
	}
	//換分業
	function set_page(value)
	{
		echo("set_page");
		window.parent.page_list["select_shelf"] = window.parent.page_list["select_shelf"]+value;
		window.document.getElementById("set_btn").style.display = "none";
		if(window.parent.page_list["select_shelf"] == 1)
		{window.document.getElementById("left_btn").style.display = "none";}
		else
		{window.document.getElementById("left_btn").style.display = "block";}

		if(window.parent.page_list["select_shelf"] == max_page)
		{window.document.getElementById("right_btn").style.display = "none";}
		else
		{window.document.getElementById("right_btn").style.display = "block";}
		window.document.getElementById("page_text").innerHTML = window.parent.page_list["select_shelf"]+" / "+max_page+" 頁";
		//改過
		window.document.getElementById("iframe").innerHTML ='<iframe src="./manu.php?page='+window.parent.page_list["select_shelf"]+'&uid='+window.parent.home_id+'&auth_open_publish='+window.parent.auth_open_publish+'&i_s='+window.parent.auth_i_s+'&auth_read_opinion_limit_day='+window.parent.auth_read_opinion_limit_day+'" frameborder="0" width="505" height="310" style="position:absolute; bottom:60px; left:45px; " ></iframe>';
		cover("");
	}
	//主頁面換分頁
	function set_main_page(value)
	{

		window.parent.page_list["shelf"]+=value;
		echo("現在頁面=?"+window.parent.page_list["shelf"] );
		main();
	}
	function set_main_page_btn()
	{echo("現在頁面=?"+main_max_page );
		if(window.parent.page_list["shelf"] <= 1)
		window.document.getElementById("main_left_btn").style.display= "none";
		else window.document.getElementById("main_left_btn").style.display= "block";

		if(window.parent.page_list["shelf"] >= main_max_page)
		window.document.getElementById("main_right_btn").style.display= "none";
		else window.document.getElementById("main_right_btn").style.display= "block";

	}




	//---------------------------------------------------
    //helper
    //---------------------------------------------------
	function open_helper(value)
	{
		window.document.getElementById("helper").innerHTML="<iframe src='../page_helper/index.php?id="+value+"' style='position:absolute; top:0px; left:0px; width:1040px; height:590px;  overflow:hidden;' frameborder='0'></iframe>";
		window.document.getElementById("helper").style.display = "block";
	}
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	//get_shelf_count();
	main();


    </script>
</Html>














