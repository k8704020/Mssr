<?php
//-------------------------------------------------------
//mssr_forum
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------
        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",2).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code'
       	);
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();
    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 user
            //$conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
			//-----------------------------------------------
	        //下載資料庫
	        //-----------------------------------------------
        		//-----------------------------------------------
            	//檢核
            	//-----------------------------------------------
					$forum_id = (int)$_GET["forum_id"];

				//-----------------------------------------------
	        	//SQL-討論區書櫃(分頁)
	        	//-----------------------------------------------
					$query_sql="
						SELECT
							`book_sid`
						FROM
							`mssr_forum_booklist`
						WHERE
							`forum_id` = $forum_id
					";
					$arrys_result_shelf=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
					//$numrow=count($arrys_result_shelf);

				//-----------------------------------------------
	        	//SQL-討論區資訊
	        	//-----------------------------------------------
					$sql="
						SELECT
							`forum_name`, `forum_content`, `forum_state`
						FROM
							`mssr_forum`
						WHERE
							`forum_id` = $forum_id
					";
					$arrys_result_2=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_2=count($arrys_result_2);
				//-----------------------------------------------
	        	//SQL-這個討論區有多少發文
	        	//-----------------------------------------------
					$sql="
						SELECT
							`article_id`
						FROM
							`mssr_article_reply_forum_rev`
						WHERE
							`forum_id` = '$forum_id'
					";
					$arrys_result_forum_article=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_forum_article=count($arrys_result_forum_article);
				//-----------------------------------------------
	        	//SQL-這個討論區有多少回覆
	        	//-----------------------------------------------
					$sql="
						SELECT
							`reply_id`
						FROM
							`mssr_article_reply_forum_rev`
						WHERE
							`forum_id` = '$forum_id'
					";
					$arrys_result_forum_reply=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_forum_reply=count($arrys_result_forum_reply);
				//-----------------------------------------------
	        	//SQL-誰也在這個聊書小組
	        	//-----------------------------------------------
					$sql="
						SELECT
							`forum_id`, `user_id`
						FROM
							`mssr_user_forum`
						WHERE
							`forum_id` = $forum_id
					";
					$arrys_result_aside=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_aside=count($arrys_result_aside);
				//-----------------------------------------------
	        	//SQL-找這個社團的創始人
	        	//-----------------------------------------------
				$sees_uid = (int)$_SESSION["uid"];
					$sql="
						SELECT
							`user_id`, `forum_id`, `user_type`
						FROM
							`mssr_user_forum`
						WHERE 1=1
							AND `forum_id` = $forum_id
							AND `user_type` LIKE '%一般版主%'
							AND `user_id` = $sees_uid
					";
					$arrys_group_admin=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					//$numrow_aside=count($arrys_result_aside);


				//echo "<pre>";
//				print_r($arrys_group_admin);
//				echo "</pre>";
//				die();
	//---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球-聊書小組書櫃";

	//---------------------------------------------------
    //分頁處理
    //---------------------------------------------------
		$numrow=0;  //資料總筆數
		$psize =15;  //單頁筆數,預設5筆
		$pnos  =0;  //分頁筆數
		$pinx  =1;  //目前分頁索引,預設1
		$sinx  =0;  //值域起始值
		$einx  =0;  //值域終止值
		if(count($arrys_result_shelf)!==0){
			

			if(isset($_GET['psize'])){
				$psize=(int)$_GET['psize'];
				if($psize===0){
					$psize=10;
				}
			}
			if(isset($_GET['pinx'])){
				$pinx=(int)$_GET['pinx'];
				if($pinx===0){
					$pinx=1;
				}
			}

			//$numrow=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
			$numrow=count($arrys_result_shelf);

			$pnos  =ceil($numrow/$psize);
			$pinx  =($pinx>$pnos)?$pnos:$pinx;

			$sinx  =(($pinx-1)*$psize)+1;
			$einx  =(($pinx)*$psize);
			$einx  =($einx>$numrow)?$numrow:$einx;
			//echo $numrow."<br/>";

			$arrys_result_shelf=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx-1,$psize),$arry_conn_mssr);
		}else{}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title ?></title>
</head>
<link href="css/mssr_forum(position).css" type="text/css" rel="stylesheet" />
<link href="css/mssr_forum(style).css" type="text/css" rel="stylesheet" />
<link href="../../inc/code.css" type="text/css" rel="stylesheet" />
<script	type="text/javascript" src="jquery-1.10.2.min.js"></script>
<script	type="text/javascript" src="jquery.blockUI.js"></script>
<script type="text/javascript" src="../../inc/code.js"></script>
<script type="text/javascript" src="../../lib/js/vaildate/code.js"></script>
<script type="text/javascript" src="../../lib/js/public/code.js"></script>
<script type="text/javascript" src="../../lib/js/string/code.js"></script>
<script type="text/javascript" src="../../lib/js/table/code.js"></script>
<script type="text/javascript" src="inc/add_action_forum_log/code.js"></script>
<script>

	var psize=<?php echo $psize;?>;
	var pinx =<?php echo $pinx;?>;
	var forum_id=<?php echo addslashes($forum_id);?>;


	function mouse_over(obj){
	//滑鼠移入
		obj.style.cursor='pointer';
	}

	function action_log(
        process_url,
        action_code,
        action_from,
        user_id_1,
        user_id_2,
        book_sid_1,
        book_sid_2,
        forum_id_1,
        forum_id_2,
        article_id,
        reply_id,
        go_url
    ){
        add_action_forum_log(
            process_url,
            action_code,
            action_from,
            user_id_1,
            user_id_2,
            book_sid_1,
            book_sid_2,
            forum_id_1,
            forum_id_2,
            article_id,
            reply_id,
            go_url
        );
    }

	$(document).ready(function() {

		//分頁列
		var cid         ="page";                        //容器id
		var numrow      =<?php echo (int)$numrow;?>;    //資料總筆數
		var psize       =<?php echo (int)$psize ;?>;    //單頁筆數,預設10筆
		var pnos        =<?php echo (int)$pnos  ;?>;    //分頁筆數
		var pinx        =<?php echo (int)$pinx  ;?>;    //目前分頁索引,預設1
		var sinx        =<?php echo (int)$sinx  ;?>;    //值域起始值
		var einx        =<?php echo (int)$einx  ;?>;    //值域終止值
		var list_size   =5;                             //分頁列顯示筆數,5
		var url_args    ={};                            //連結資訊
		url_args={
			'pinx_name' :'pinx',
			'psize_name':'psize',
			'page_name' :'mssr_forum_group_shelf.php',
			'page_args' :{
				'forum_id':forum_id
			}
		}
		<?php if($numrow!==0):?>
            var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
        <?php endif;?>

		//block ui
		$('#open_mssr_input_box').click(function() {
			//alert("OK!!");
			$.blockUI({
				message: $('#mssr_input_box'),
				css:{
					top:  ($(window).height() - 400) /2 + 'px',
					left: ($(window).width() - 700) /2 + 'px',
					textAlign:	'left',
					width: '700px'
				}
			});
		});
		
		
		$('#open_mssr_forum_group_add_book').click(function() {
			//alert("OK!!");
			$.blockUI({
				message: $('#mssr_add_book'),
				css:{
					top:  ($(window).height() - 400) /2 + 'px',
					left: ($(window).width() - 700) /2 + 'px',
					textAlign:	'left',
					width: '700px'
				}
			});
		});
		$('#mssr_input_box_leave').click(function() {
			$.unblockUI();
			return false;
		});
		$('#mssr_group_add_book_leave').click(function() {
			$.unblockUI();
			return false;
		});
		$('#mssr_reply_box_leave').click(function() {
			$.unblockUI();
			return false;
		});
		$('#mssr_input_box_submit').click(function() {
			//alert($("#haha").val());mssr_group_add_book_leave
			//alert($("#mssr_input_article_content").val());
			$.unblockUI();
		});
	});
</script>
<script>
	function functionname(s){
		switch(s) {
			case '1':
				document.getElementById('p1').innerHTML = "我想對聊書小組某本書的情節有疑問，因為…";
				document.getElementById('r1').value= "我想對聊書小組某本書的情節有疑問，因為…";
				document.getElementById('p2').innerHTML = "我想對聊書小組某本書的角色有疑問，因為…";
				document.getElementById('r2').value= "我想對聊書小組某本書的角色有疑問，因為…";
				document.getElementById('p3').innerHTML = "我想對聊書小組某本書的背景有疑問，因為…";
				document.getElementById('r3').value= "我想對聊書小組某本書的背景有疑問，因為…";
				break;

			case '2':
				document.getElementById('p1').innerHTML = "聊書小組某本書的封面很好看，因為…";
				document.getElementById('r1').value= "聊書小組某本書的封面很好看，因為…";
				document.getElementById('p2').innerHTML = "聊書小組某本書的劇情很讚，因為…";
				document.getElementById('r2').value= "聊書小組某本書的劇情很讚，因為…";
				document.getElementById('p3').innerHTML = "聊書小組某本書的角色很(帥，美麗)，因為…";
				document.getElementById('r3').value= "聊書小組某本書的角色很(帥，美麗)，因為…";
				break;

			case '3':
				document.getElementById('p1').innerHTML = "在聊書小組看完哪一本書，我覺得我很開心，因為…";
				document.getElementById('r1').value= "在聊書小組看完哪一本書，我覺得我很開心，因為…";
				document.getElementById('p2').innerHTML = "在聊書小組看完哪一本書，我覺得我很難過，因為…";
				document.getElementById('r2').value= "在聊書小組看完哪一本書，我覺得我很難過，因為…";
				document.getElementById('p3').innerHTML = "在聊書小組看完哪一本書，我覺得我很生氣，因為…";
				document.getElementById('r3').value= "在聊書小組看完哪一本書，我覺得我很生氣，因為…";
				break;

			case '4':
				document.getElementById('p1').innerHTML = "我想要跟聊書小組的朋友分享，回想到我以前，我也有過像書中類似的情況…";
				document.getElementById('r1').value= "我想要跟聊書小組的朋友分享，回想到我以前，我也有過像書中類似的情況…";
				document.getElementById('p2').innerHTML = "我想要跟聊書小組的朋友分享，我的家人或朋友，他們跟我說過…";
				document.getElementById('r2').value= "我想要跟聊書小組的朋友分享，我的家人或朋友，他們跟我說過…";
				document.getElementById('p3').innerHTML = "我想要跟聊書小組的朋友分享，我看過其他書，也有類似的事情…";
				document.getElementById('r3').value= "我想要跟聊書小組的朋友分享，我看過其他書，也有類似的事情…";
				break;

			case '5':
				document.getElementById('p1').innerHTML = "我覺得聊書小組的某本書，在什麼地方寫的很好，為什麼?";
				document.getElementById('r1').value= "我覺得聊書小組的某本書，在什麼地方寫的很好，為什麼?";
				document.getElementById('p2').innerHTML = "我覺得聊書小組的某本書，在什麼地方寫的不好，為什麼?";
				document.getElementById('r2').value= "我覺得聊書小組的某本書，在什麼地方寫的不好，為什麼?";
				document.getElementById('p3').innerHTML = "我很(喜歡，不喜歡)聊書小組的某本書的作者，為什麼?";
				document.getElementById('r3').value= "我很(喜歡，不喜歡)聊書小組的某本書的作者，為什麼?";
				break;

		}
	}
</script>
<body>

<!--=======================================================================================================-->
<!--=============================================頁頭=======================================================-->
<!--=======================================================================================================-->
<section id="logopic">
	<img src="image/logopic4.jpg" alt="" width=100% height="150"/>
    <a onclick="action_log('inc/add_action_forum_log/code.php','g0',<?php echo $_SESSION["uid"];?>,0,0,'','',<?php echo $forum_id;?>,0,0,0,'index.php');void(0);"
    href="javascript:void(0);"><img id="home" src="image/home.png" /></a>
</section>
<header>
	<img src="image/namecard.png" width="40%" height="150px" />
	<section class="header_left" >
    	<!----------討論區圖片---------->
        <?php
		//聊書小組封面處理
		$forumpic_root = 'image/forum_pic_'.$forum_id.'.jpg';
		if(file_exists($forumpic_root)){
			$rs_forumpic_root = 'image/forum_pic_'.$forum_id.'.jpg';
		}else{
			$rs_forumpic_root = 'image/forum_pic.jpg';
		}?>
        <div class="header_pic">
            <a href="mssr_forum_group_discussion.php?forum_id=<?php echo $_GET["forum_id"]?>"><img src="<?php echo $rs_forumpic_root?>" alt="" width="110" height="110"/></a>
        </div>

        <!----------討論區資訊---------->
        <div class="stud_info">
            <?php
                for($i=0;$i<$numrow_2;$i++){
                    $forum_name 		= trim($arrys_result_2[$i]['forum_name']);
                    $forum_content 		= trim($arrys_result_2[$i]['forum_content']);
                    $forum_state 		= trim($arrys_result_2[$i]['forum_state']);?>
                    <h1 id="book_info_name"><b><?php echo $forum_name?></b></h1>
                    <h4><?php echo $forum_content?></h4>
            <?php }?>
        </div>
    </section>

    <!----------討論區聊天資訊---------->
    <div class="header_info">
    	<img id="header_info_group" src="image/talkman.png"  />
        <p id="header_info_groupinfo">這個討論群有，<b><?php echo $numrow_forum_article?></b>篇發文，<br/><b><?php echo $numrow_forum_reply?></b>篇回覆，<b><?php echo $numrow_aside?></b>位成員也參加<br/>這個聊書小組。<br/>趕快來參加吧！</p>
    </div>

    <!----------發文---------->
    <img id="open_mssr_input_box" src="image/article_publish.png" alt="" 
            style='position:relative;z-index:99;';
            onmouseover="mouse_over(this);void(0);"
            onclick=";void(0);" />
    
    
    <!----------加入討論書籍---------->
   <?php
	if(count($arrys_group_admin)!==0){?>
    	<img id="open_mssr_forum_group_add_book" src="image/add_book.png" alt="" 
            style='position:relative;z-index:99;';
            onmouseover="mouse_over(this);void(0);"
            onclick=";void(0);" />
	<?php }?>

    <!----------NAV---------->
	<nav>
    	<ul>
        	<li><a onclick="action_log('inc/add_action_forum_log/code.php','g2',<?php echo $_SESSION["uid"];?>,0,0,'','',<?php echo $forum_id?>,0,0,0,'mssr_forum_group_discussion.php?forum_id=<?php echo $forum_id?>');void(0);"
                href="javascript:void(0);">大家來聊書</a></li>

            <li><a onclick="action_log('inc/add_action_forum_log/code.php','g3',<?php echo $_SESSION["uid"];?>,0,0,'','',<?php echo $forum_id?>,0,0,0,'mssr_forum_group_shelf.php?forum_id=<?php echo $forum_id?>');void(0);"
                href="javascript:void(0);" style="background-image:url(image/icon_circle.png); background-repeat:no-repeat; background-position:center">聊書小組書櫃</a></li>

            <li><a onclick="action_log('inc/add_action_forum_log/code.php','g4',<?php echo $_SESSION["uid"];?>,0,0,'','',<?php echo $forum_id?>,0,0,0,'mssr_forum_group_member.php?forum_id=<?php echo $forum_id?>');void(0);"
                href="javascript:void(0);">聊書小組成員</a></li>
        </ul>
    </nav>
</header>

<!----------分頁---------->
<div class="table_page">
	<?php
	if(count($arrys_result_shelf)!==0):?>
        <table  border="0" width="100%" style='position:relative;top:0px; left:-10px;'>
            <tr valign="middle">
                <td align="left">
                    <!-- 分頁列 -->
                    <span id="page" style="position:relative;top:0px;"></span>
                </td>
            </tr>
        </table>
	<?php endif;?>
</div>

<!----------block ui div---------->
<div id="mssr_input_box" style="display:none">
	<form action="mssr_forum_group_discussion_A.php" method="post">
		<input type="image" id="mssr_input_box_leave" src="image/xlogo.png" alt="" width="30" height="30" onclick="leaveui();"/>
		<h3 id="mssr_input_box_name">輸入發文內容與標題</h3>
    	<p id="mssr_input_box_title">標題：<input  id="haha" type="text"  name="mssr_input_box_name_title" size="50" maxlength="40" /></p>
        <textarea id="mssr_input_box_content" name="mssr_input_box_name_content" cols="40" rows="12"></textarea>
        <select id="select_type" name="type" onchange="functionname(this.options[this.options.selectedIndex].value)">
        	<option value="1">我想要問</option>
            <option value="2">書本特色</option>
            <option value="3">感情抒發</option>
            <option value="4">經驗分享</option>
            <option value="5">我要評論</option>
        </select>
        <textarea name="forum_id"  style="display:none" cols="3" rows="8"><?php echo $_GET["forum_id"]?></textarea>
        <input id="r1"  class="mssr_input_box_radio" type="radio" name="help" value="aaa" /><p id="p1" class="mssr_input_box_radio_text" style="width:200px; height:25px;">我想對聊書小組某本書的情節有疑問，因為…</p>
		<input id="r2"  class="mssr_input_box_radio" type="radio" name="help" value="bbb" /><p id="p2" class="mssr_input_box_radio_text" style="width:200px; height:25px;">我想對聊書小組某本書的角色有疑問，因為…</p>
        <input id="r3"  class="mssr_input_box_radio" type="radio" name="help" value="ccc" /><p id="p3" class="mssr_input_box_radio_text" style="width:200px; height:25px;">我想對聊書小組某本書的背景有疑問，因為…</p>
        <input id="mssr_input_box_submit" type="submit" value="送出"/>
   	</form>
</div>




<div id="mssr_add_book" style="display:none">
	<form action="mssr_forum_group_add_book_A.php" method="post">
    
		<input type="image" id="mssr_group_add_book_leave" src="image/xlogo.png" alt="" width="30" height="30" onclick="leaveui();"/>
        
		<h3 id="mssr_group_add_book_name">請輸入ISBN</h3>
    	<p id="mssr_group_add_book_title">ISBN：<input  id="haha" type="text"  name="mssr_group_add_book_name_title" size="50" maxlength="40" /></p>
        
        <textarea name="forum_id"  style="display:none" cols="3" rows="8"><?php echo $_GET["forum_id"]?></textarea>
        
        <input id="mssr_group_add_book_submit" type="submit" value="送出"/>
   	</form>
</div>
<!--=======================================================================================================-->
<!--=============================================主頁面=====================================================-->
<!--=======================================================================================================-->
<section class="course">

	<?php
		if(empty($arrys_result_shelf)){
			echo("現在書櫃沒有書喔，趕快來看書吧！");
		}else{
			for($i=0; $i<count($arrys_result_shelf); $i++){
				$book_sid 			= trim($arrys_result_shelf[$i]['book_sid']);

				$arrys_book_info	=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
				$book_name 			= trim($arrys_book_info[0]['book_name']);


				//book_name		書名
				if(mb_strlen($book_name)>7){
					$book_name=mb_substr($book_name,0,7)."..";
				}

				//書籍封面處理
				$bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
				if(file_exists($bookpic_root)){
					$rs_bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
				}else{
					$rs_bookpic_root = 'image/book.jpg';
				}?>


				<figure>
					<a onclick="action_log('inc/add_action_forum_log/code.php','g5',<?php echo $_SESSION["uid"];?>,0,0,'<?php echo $book_sid;?>','',<?php echo $forum_id;?>,0,0,0,'mssr_forum_book_discussion.php?book_sid=<?php echo $book_sid?>');void(0);"
                    href="javascript:void(0);"><img src="<?php echo $rs_bookpic_root ?>" alt="<?php echo $book_name?>" width="100px" height="100px" /></a>
					<figcaption><?php echo $book_name?></figcaption>
				</figure>
   			<?php }?>
    	<?php }?>






</section>
<!--=======================================================================================================-->
<!--=============================================頁尾=======================================================-->
<!--=======================================================================================================-->
<footer class="footer_people">
	明日星球-聊書
</footer>
</body>
</html>
