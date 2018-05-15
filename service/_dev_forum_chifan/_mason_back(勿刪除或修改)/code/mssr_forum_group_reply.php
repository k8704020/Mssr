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
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
			//-----------------------------------------------
	        //下載資料庫
	        //-----------------------------------------------
				//-----------------------------------------------
            	//檢核
            	//-----------------------------------------------
					$article_id = (int)$_GET["article_id"];
					$sess_uid   = (int)$_SESSION["uid"];

				//-----------------------------------------------
	        	//SQL-留言資訊(mssr_reply_box_frame)
	        	//-----------------------------------------------
					$sql="
						SELECT
							`article_title`, `article_content`, `article_like_cno`, `keyin_cdate`, `user_id`, `article_state`
						FROM
							`mssr_forum_article`
						WHERE 1=1
							AND `article_id` = $article_id
							AND `article_state` LIKE '%正常%'
					";
					$arrys_result_reply_box=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
					$numrow_reply_box=count($arrys_result_reply_box);

				//-----------------------------------------------
	        	//SQL-mssr_reply_box_frame-名子
	        	//-----------------------------------------------
					for($i=0;$i<$numrow_reply_box;$i++){
						$user_id = $arrys_result_reply_box[$i]['user_id'];
						$sql="
							SELECT
								`name`
							FROM
								`member`
							WHERE
								`uid` = $user_id
						";
					}
					$arrys_result_reply_name=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
					$numrow_reply_name=count($arrys_result_reply_name);


				//-----------------------------------------------
	        	//SQL-回復資訊(分頁)(mssr_comment_box_frame)
	        	//-----------------------------------------------
					$query_sql="
						SELECT
							`user_id`, `reply_content`, `reply_like_cno`, `keyin_cdate`, `reply_id`, `reply_state`
						FROM
							`mssr_forum_article_reply`
						WHERE 1=1
							AND `article_id` = $article_id
							AND `reply_state` LIKE '%正常%'
						ORDER BY
							`mssr_forum_article_reply`.`reply_id` ASC

					";
					$arrys_result_comment=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
					$numrow=count($arrys_result_comment);


				//-----------------------------------------------
	        	//SQL-mssr_comment_box_frame-名子
	        	//-----------------------------------------------

					if($numrow!==0){
						for($i=0;$i<$numrow;$i++){
							$user_id = $arrys_result_comment[$i]['user_id'];
							$sql="
								SELECT
									`name`
								FROM
									`member`
								WHERE
									`id` = $user_id
							";
						}
						$arrys_result_comment_name=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
						$numrow_comment_name=count($arrys_result_comment_name);
					}


				//-----------------------------------------------
				//SQL-查forum_id
	        	//-----------------------------------------------
					$sql="
						SELECT
							`forum_id`
						FROM
							`mssr_article_forum_rev`
						WHERE
							`article_id`=$article_id
					";
					$arrys_result_forum_id=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

				//-----------------------------------------------
	        	//SQL-討論區資訊
	        	//-----------------------------------------------

					$forum_id = $arrys_result_forum_id[0]['forum_id'];
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
						WHERE 1=1
							AND `forum_id` = $forum_id
							AND `user_state` LIKE '%啟用%'

					";
					$arrys_result_aside=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_aside=count($arrys_result_aside);
				//-----------------------------------------------
	        	//SQL-誰也參加這個聊書小組
	        	//-----------------------------------------------
					$sql="
						SELECT
							`forum_id`, `user_id`
						FROM
							`mssr_user_forum`
						WHERE 1=1
							AND `forum_id` = $forum_id
							AND `user_state` LIKE '%啟用%'
					";
					$arrys_result_forum_member=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);
					$numrow_forum_member=count($arrys_result_forum_member);
				//-----------------------------------------------
	        	//SQL-討論區書籍
	        	//-----------------------------------------------
					$sql="
						SELECT
							`book_sid`
						FROM
							`mssr_forum_booklist`
						WHERE
							`forum_id` = $forum_id
					";
					$arrys_result_forum_booklist=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,3),$arry_conn_mssr);
					$numrow_forum_booklist=count($arrys_result_forum_booklist);
				//-----------------------------------------------
	        	//SQL-參加這個聊書小組的人，他們也參加了?
	        	//-----------------------------------------------
					//找最近參加此聊書小組的3個人
					$sql="
						SELECT
							`user_id`
						FROM
							`mssr_user_forum`
						WHERE
							`forum_id` = {$forum_id}
						ORDER BY
							`keyin_cdate` DESC

					";
					$arrys_result_who_join=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,3),$arry_conn_mssr);
					$numrow_group_who_join=count($arrys_result_who_join);

					$arry_group_id_list=array();
					for($i=0;$i<$numrow_group_who_join;$i++){
						$user_id 			= (int)$arrys_result_who_join[$i]['user_id'];
						//找參加過這個聊書小組的人，他們也參加甚麼聊書小組，挑重複度最高的三位
						$sql="
							SELECT
								`forum_id`
							FROM
								`mssr_user_forum`
							WHERE
								`user_id` = $user_id
                                AND `forum_id`<>{$forum_id}

						";
						$arrys_result_recommend=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

						if(!empty($arrys_result_recommend)){
							$arry_list=array();
							foreach($arrys_result_recommend as  $arry_result_recommend){

								$rs_group_id=trim($arry_result_recommend['forum_id']);

								if(!array_key_exists($rs_group_id,$arry_group_id_list)){
									$arry_group_id_list[$rs_group_id]=1;
								}else{
									$arry_group_id_list[$rs_group_id]=$arry_group_id_list[$rs_group_id]+1;
								}
							}
							//排序
							arsort($arry_group_id_list);

							//篩選
							foreach($arry_group_id_list as $book_sid_tmp=>$cno){
								if(count($arry_list)<3){
									$arry_list[]=trim($book_sid_tmp);
								}else{
									break;
								}
							}
						}
						//$numrow_recommend=count($arrys_result_recommend);
					}
					
				//-----------------------------------------------
	        	//SQL-是否是板主
	        	//-----------------------------------------------
					$sql="
						SELECT
							`forum_id`, `user_id`, `user_type`
						FROM
							`mssr_user_forum`
						WHERE 1=1
							ANd `forum_id` = $forum_id
							AND `user_id` = $sess_uid
							AND `user_type` LIKE '%一般版主%'
					";
					$arrys_admin=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_arrys_admin=count($arrys_admin);
					
					
					
				
				//echo "<pre>";
//                print_r($arrys_admin);
//                echo "</pre>";
//				die();
	//---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球-聊書小組討論區";


	//---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

		$numrow=0;  //資料總筆數
		$psize =6;  //單頁筆數,預設8筆
		$pnos  =0;  //分頁筆數
		$pinx  =1;  //目前分頁索引,預設1
		$sinx  =0;  //值域起始值
		$einx  =0;  //值域終止值

		if(count($arrys_result_comment)!==0){
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
			$numrow=count($arrys_result_comment);

			$pnos  =ceil($numrow/$psize);
			$pinx  =($pinx>$pnos)?$pnos:$pinx;

			$sinx  =(($pinx-1)*$psize)+1;
			$einx  =(($pinx)*$psize);
			$einx  =($einx>$numrow)?$numrow:$einx;
			//echo $numrow."<br/>";

			$arrys_result_comment=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx-1,$psize),$arry_conn_mssr);
		}else{}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title?></title>
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
	var article_id='<?php echo addslashes($article_id);?>';

	function like(type,user_id,article_id){
	//按讚

		//參數
		var type      =trim(type);
		var user_id   =parseInt(user_id);
		var article_id=parseInt(article_id);

		if((user_id===0)||(article_id===0)){
			alert('動作失敗!');
			return false;
		}

		//頁面條件
		var url='mssr_forum_group_like_A.php';
		url+='?type='+encodeURI(type);
		url+='&user_id='+encodeURI(user_id);
		url+='&article_id='+encodeURI(article_id);

        if(type==='article'){
            action_log('inc/add_action_forum_log/code.php','ga12',<?php echo $_SESSION["uid"];?>,0,0,'','',<?php echo $forum_id;?>,0,article_id,0,url);
        }else{
            action_log('inc/add_action_forum_log/code.php','ga13',<?php echo $_SESSION["uid"];?>,0,0,'','',<?php echo $forum_id;?>,0,<?php echo $_GET["article_id"];?>,article_id,url);
        }
        return false;
	}

	function mouse_over(obj){
	//滑鼠移入
		obj.style.cursor='pointer';
	}
	
	//隱藏文章
	function delete_article(type, user_id, article_id){
		//參數
		var type      	=trim(type);
		var user_id   	=parseInt(user_id);
		var article_id 	=parseInt(article_id);
		
		
		
		if(confirm("您確定要隱藏此篇文章嗎?")){
			if((user_id===0)||(article_id===0)){
				alert('動作失敗!');
				return false;
			}

			//頁面條件
			var url='mssr_forum_delete_A.php';
			url+='?type='+encodeURI(type);
			url+='&user_id='+encodeURI(user_id);
			url+='&article_id='+encodeURI(article_id);
			
			
			location.href=url;
		}	
	}
	
	function go_back(){
		window.history.back();

	}
	
	//隱藏回覆按鈕
	function reply_disable_close(obj){
		var mssr_comment_input_content	=document.getElementById('mssr_comment_input_content');
		var mssr_forum_group_reply		=document.getElementById('mssr_forum_group_reply');
		if(trim(mssr_comment_input_content.value)===''){
			alert("請輸入回復！");	
			return false; 
		}
		obj.disabled=true;
		mssr_forum_group_reply.submit();
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
			'page_name' :'mssr_forum_group_reply.php',
			'page_args' :{
				'article_id':article_id
			}
		}
        <?php if($numrow!==0):?>
		var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
        <?php endif;?>

		//block ui
		$('#open_mssr_input_box').click(function() {
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
		$('#mssr_input_box_leave').click(function() {
			$.unblockUI();
			return false;
		});
		$('#mssr_reply_box_leave').click(function() {
			$.unblockUI();
			return false;
		});
		$('#mssr_input_box_submit').click(function() {
			//alert($("#haha").val());
			//alert($("#mssr_input_article_content").val());
			$.unblockUI();
		});
	});
</script>
<script>
	function functionname(s){
		switch(s) {
			case '1':
				document.getElementById('p1').innerHTML = "我對於這本書的情節有疑問，因為…";
				document.getElementById('r1').value= "1a";
				document.getElementById('p2').innerHTML = "我對於這本書的角色有疑問，因為…";
				document.getElementById('r2').value= "1b";
				document.getElementById('p3').innerHTML = "我對於這本書的背景有疑問，因為…";
				document.getElementById('r3').value= "1c";
				break;

			case '2':
				document.getElementById('p1').innerHTML = "這本書的封面很好看，因為…";
				document.getElementById('r1').value= "2a";
				document.getElementById('p2').innerHTML = "這本書的劇情很讚，因為…";
				document.getElementById('r2').value= "2b";
				document.getElementById('p3').innerHTML = "這本書的角色很(帥，美麗)，因為…";
				document.getElementById('r3').value= "2c";
				break;

			case '3':
				document.getElementById('p1').innerHTML = "看完這本書，我覺得我很開心，因為…";
				document.getElementById('r1').value= "3a";
				document.getElementById('p2').innerHTML = "看完這本書，我覺得我很難過，因為…";
				document.getElementById('r2').value= "3b";
				document.getElementById('p3').innerHTML = "看完這本書，我覺得我很生氣，因為…";
				document.getElementById('r3').value= "3c";
				break;

			case '4':
				document.getElementById('p1').innerHTML = "回想到我以前，我也有過像書中類似的情況…";
				document.getElementById('r1').value= "4a";
				document.getElementById('p2').innerHTML = "我的家人或朋友，他們跟我說過…";
				document.getElementById('r2').value= "4b";
				document.getElementById('p3').innerHTML = "我看過其他書，也有類似的事情…";
				document.getElementById('r3').value= "4c";
				break;

			case '5':
				document.getElementById('p1').innerHTML = "我覺得這本書，在什麼地方寫的很好，為什麼?";
				document.getElementById('r1').value= "5a";
				document.getElementById('p2').innerHTML = "我覺得這本書，在什麼地方寫的不好，為什麼?";
				document.getElementById('r2').value= "5b";
				document.getElementById('p3').innerHTML = "我很(喜歡，不喜歡)這本書的作者，為什麼?";
				document.getElementById('r3').value= "5c";
				break;

		}
	}
</script>
<!--=======================================================================================================-->
<!--=============================================頁頭=======================================================-->
<!--=======================================================================================================-->
<section id="logopic">
	<img src="image/logopic4.jpg" alt="" width=100% height="150"/>
    <a onclick="action_log('inc/add_action_forum_log/code.php','ga0',<?php echo $_SESSION["uid"];?>,0,0,'','',<?php echo $forum_id;?>,0,<?php echo $article_id;?>,0,'index.php');void(0);"
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
            <a href="mssr_forum_group_discussion.php?forum_id=<?php echo $forum_id ?>"><img src="<?php echo $rs_forumpic_root?>" alt="" width="110" height="110"/></a>
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
     <img id="open_mssr_input_box" src="image/article_publish.png" alt="" width="50" height="50"
            style='position:relative;z-index:99;';
            onmouseover="mouse_over(this);void(0);"
            onclick=";void(0);" />

    <!----------NAV---------->
	<nav>
    	<ul>
        	<li><a onclick="action_log('inc/add_action_forum_log/code.php','ga2',<?php echo $_SESSION["uid"];?>,0,0,'','',<?php echo $forum_id?>,0,<?php echo $_GET["article_id"];?>,0,'mssr_forum_group_discussion.php?forum_id=<?php echo $forum_id?>');void(0);"
                href="javascript:void(0);" style="background-image:url(image/icon_circle.png); background-repeat:no-repeat; background-position:center">大家來聊書</a></li>
            <li><a onclick="action_log('inc/add_action_forum_log/code.php','ga3',<?php echo $_SESSION["uid"];?>,0,0,'','',<?php echo $forum_id?>,0,<?php echo $_GET["article_id"];?>,0,'mssr_forum_group_shelf.php?forum_id=<?php echo $forum_id?>');void(0);"
                href="javascript:void(0);">聊書小組書櫃</a></li>
            <li><a onclick="action_log('inc/add_action_forum_log/code.php','ga4',<?php echo $_SESSION["uid"];?>,0,0,'','',<?php echo $forum_id?>,0,<?php echo $_GET["article_id"];?>,0,'mssr_forum_group_member.php?forum_id=<?php echo $forum_id?>');void(0);"
                href="javascript:void(0);">聊書小組成員</a></li>
        </ul>
    </nav>
</header>
<!----------分頁---------->
<div class="table_page">
	<?php
	if(count($arrys_result_comment)!==0):?>
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
        <textarea name="forum_id"  style="display:none" cols="3" rows="8"><?php echo $forum_id?></textarea>
        <input id="r1"  class="mssr_input_box_radio" type="radio" name="article_refer_code" value="1a" /><p id="p1" class="mssr_input_box_radio_text" style="width:200px; height:25px;">我想對聊書小組某本書的情節有疑問，因為…</p>
		<input id="r2"  class="mssr_input_box_radio" type="radio" name="article_refer_code" value="1b" /><p id="p2" class="mssr_input_box_radio_text" style="width:200px; height:25px;">我想對聊書小組某本書的角色有疑問，因為…</p>
        <input id="r3"  class="mssr_input_box_radio" type="radio" name="article_refer_code" value="1c" /><p id="p3" class="mssr_input_box_radio_text" style="width:200px; height:25px;">我想對聊書小組某本書的背景有疑問，因為…</p>
        <input type="text" id="action_code" name="action_code" value="ga1" style='display:none;'>
        <input id="mssr_input_box_submit" type="submit" value="送出"/>
   	</form>
</div>
<!--=======================================================================================================-->
<!--=============================================主頁面=====================================================-->
<!--=======================================================================================================-->
<section class="book_course">
	 <!----------發文列表---------->
    <div id="mssr_reply_box" >

        <!----------留言資訊---------->
        <figure id="mssr_reply_box_frame">
            <?php
            for($i=0; $i<$numrow_reply_name; $i++){
                $name 						= $arrys_result_reply_name[$i]['name'];?>
                <figcaption id="mssr_reply_box_name"><?php echo $name?></figcaption>
            <?php }?>
            <?php
			//-----------------------------------------------
			//SQL-讚
			//-----------------------------------------------
			$sql="
				SELECT
					`user_id`
				FROM
					`mssr_forum_article_like_log`
				WHERE
					`article_id` = '$article_id'
			";
			$arrys_result_like=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$numrow_like=count($arrys_result_like);

			?>
            <?php
            for($i=0; $i<$numrow_reply_box; $i++){
                $user_id 					= $arrys_result_reply_box[$i]['user_id'];
                $article_title 				= $arrys_result_reply_box[$i]['article_title'];
                $article_content 			= $arrys_result_reply_box[$i]['article_content'];
                $article_like_cno 			= $arrys_result_reply_box[$i]['article_like_cno'];
                $keyin_cdate 				= $arrys_result_reply_box[$i]['keyin_cdate'];

				//學生照片
				$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
				if($get_user_info[0]['sex']==1){?>
					<a onclick="action_log('inc/add_action_forum_log/code.php','ga7',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',<?php echo $forum_id?>,0,<?php echo $article_id;?>,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                    href="javascript:void(0);"><img id="mssr_reply_box_pic" src="image/boy.jpg" alt="" width="100" height="100"/></a>
				<?php }else{?>
					<a onclick="action_log('inc/add_action_forum_log/code.php','ga7',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',<?php echo $forum_id?>,0,<?php echo $article_id;?>,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                    href="javascript:void(0);"><img id="mssr_reply_box_pic" src="image/girl.jpg" alt="" width="100" height="100"/></a>
				<?php }?>

                <h3 id="mssr_reply_box_title"><?php echo $article_title?></h3>
                <p id="mssr_reply_box_content"><?php echo $article_content?></p>
                <p id="mssr_reply_box_time"><?php echo $keyin_cdate?>(#<?php echo $_GET["article_id"];?>)</p>

                <img id="mssr_reply_box_likepic" src="image/like.png" alt="" width="10" height="10"
                onmouseover="mouse_over(this);void(0);"
                onclick="like('article',<?php echo $user_id;?>,<?php echo $article_id;?>);void(0);"/>

                <p id="mssr_reply_box_likecnt"><?php echo $numrow_like?></p>
                <img id="mssr_reply_box_replypic" src="image/icon.png" alt="" width="10" height="10"/>
                <p id="mssr_reply_box_replycnt"><?php echo $numrow?></p>

                <a onmouseover="mouse_over(this);void(0);" onclick="go_back()";><p id="mssr_reply_box_back">回上一頁</p></a>
            <?php }?>
        </figure>
            <!----------回覆留言---------->
        <?php
        for($i=0; $i<count($arrys_result_comment); $i++){
            $user_id 			= $arrys_result_comment[$i]['user_id'];
            $reply_id 			= $arrys_result_comment[$i]['reply_id'];
            $reply_content 		= $arrys_result_comment[$i]['reply_content'];
            $keyin_cdate 		= $arrys_result_comment[$i]['keyin_cdate'];
            $reply_like_cno 	= $arrys_result_comment[$i]['reply_like_cno'];
            $sql="
                SELECT
                    `name`
                FROM
                    `member`
                WHERE
                    `uid` = $user_id
            ";
            $arrys_result_username=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);


			//-----------------------------------------------
			//SQL-讚
			//-----------------------------------------------
			$sql="
				SELECT
					`user_id`
				FROM
					`mssr_forum_article_reply_like_log`
				WHERE
					`reply_id` = '$reply_id'
			";
			$arrys_result_like_reply=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$numrow_like_reply=count($arrys_result_like_reply);?>
            <figure id="mssr_comment_box_frame">

            	<?php
				//學生照片
				$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
				if($get_user_info[0]['sex']==1){?>
					<a onclick="action_log('inc/add_action_forum_log/code.php','ga8',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',<?php echo $forum_id?>,0,<?php echo $article_id;?>,<?php echo $reply_id;?>,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                    href="javascript:void(0);"><img id="mssr_comment_box_pic" src="image/boy.jpg" alt="" width="100" height="100"/></a>
				<?php }else{?>
					<a onclick="action_log('inc/add_action_forum_log/code.php','ga8',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',<?php echo $forum_id?>,0,<?php echo $article_id;?>,<?php echo $reply_id;?>,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                    href="javascript:void(0);"><img id="mssr_comment_box_pic" src="image/girl.jpg" alt="" width="100" height="100"/></a>
				<?php }?>






                <figcaption id="mssr_comment_box_name"><?php echo $arrys_result_username[0]['name']?></figcaption>

                <p id="mssr_comment_box_content"><?php echo $reply_content?></p>
                <p id="mssr_comment_box_time"><?php echo $keyin_cdate?>(#<?php echo $reply_id;?>)</p>


                <img id="mssr_comment_box_likepic" src="image/like.png" alt="" width="10" height="10"
                onmouseover="mouse_over(this);void(0);"
                onclick="like('reply',<?php echo $user_id;?>,<?php echo $reply_id;?>);void(0);"/>

                <p id="mssr_comment_box_likecnt"><?php echo $numrow_like_reply?></p>
                
                <?php if($numrow_arrys_admin!==0){?>
                    <img id="mssr_comment_box_deletepic" src="image/delete.png" alt="" width="10" height="10"
                    onmouseover="mouse_over(this);void(0);"
                    onclick="delete_article('forum_reply',<?php echo $user_id;?>,<?php echo $reply_id;?>);void(0);"/>
                <?php }?>
                
                
            </figure>
        <?php }?>
            <!--回覆留言-->
            <form id="mssr_forum_group_reply" action="mssr_forum_group_reply_A.php" method="post">
                <figure id="mssr_comment_input_frame">
                	<?php
						//學生照片
						$get_user_info=get_user_info($conn_user,$_SESSION["uid"],$array_filter=array('sex','name'),$arry_conn_user);
						if($get_user_info[0]['sex']==1){?>
							<img id="mssr_comment_box_pic" src="image/boy.jpg" alt="" width="100" height="100"/>
						<?php }else{?>
							<img id="mssr_comment_box_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
					<?php }?>

                    <figcaption id="mssr_comment_input_name"><?php echo $get_user_info[0]['name']?></figcaption>
                    <textarea id="mssr_comment_input_content" name="mssr_comment_input_name_content" cols="26" rows="8"></textarea>
                    <textarea name="article_id"  style="display:none" cols="2" rows="8"><?php echo $_GET["article_id"]?></textarea>
                    <textarea name="book_sid"  style="display:none" cols="2" rows="8"><?php echo $_GET["book_sid"]?></textarea>
                    <input id="mssr_comment_input_submit" onclick="reply_disable_close(this);" type="button" value="送出"/>
                </figure>
            </form>
    	</div>

</section>
<!--=======================================================================================================-->
<!--=============================================側欄=======================================================-->
<!--=======================================================================================================-->
<aside>
	<!----------人---------->
	<section class="book_aside_people">
        <p id="book_aside_people_title">誰也參加這個聊書小組</p>
    	<img id="book_aside_people_icon" src="image/icon_hot.png" alt="" width="30" height="30"/>
        <?php
		if($numrow_forum_member!==0){
			for($i=0; $i<$numrow_forum_member; $i++){
				$user_id = $arrys_result_forum_member[$i]['user_id'];
				//-----------------------------------------------
				//SQL-回復姓名
				//-----------------------------------------------
				$sql="
					SELECT
						`name`
					FROM
						`member`
					WHERE
						`uid` = $user_id
				";
				$arrys_result_replyname=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);?>
				<figure id="book_aside_people_info">
					<?php
					//學生照片
					$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
					if($get_user_info[0]['sex']==1){?>
						<a onclick="action_log('inc/add_action_forum_log/code.php','ga9',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',<?php echo $forum_id;?>,0,<?php echo $article_id;?>,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                        href="javascript:void(0);"><img id="book_aside_people_info_pic" src="image/boy.jpg" alt=""/></a>
					<?php }else{?>
						<a onclick="action_log('inc/add_action_forum_log/code.php','ga9',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',<?php echo $forum_id;?>,0,<?php echo $article_id;?>,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                        href="javascript:void(0);"><img id="book_aside_people_info_pic" src="image/girl.jpg" alt=""/></a>
					<?php }?>


					<a onclick="action_log('inc/add_action_forum_log/code.php','ga9',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',<?php echo $forum_id;?>,0,<?php echo $article_id;?>,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                       href="javascript:void(0);" id="book_aside_people_info_name" ><?php echo $arrys_result_replyname[0]['name']?></a>
				</figure>
        	<?php }?>
       	<?php }else{
			echo "目前暫無資料";
		}?>

	</section>

	<!----------書---------->
    <section class="book_aside_book">
    	<p id="book_aside_book_title">這個聊書小組<br/>正在討論些甚麼書?</p>
    	<img id="book_aside_book_icon" src="image/icon_hot.png" alt="" width="30" height="30"/>
        <?php
		if($numrow_forum_booklist!==0){
			for($i=0; $i<$numrow_forum_booklist; $i++){
				$book_sid  				=$arrys_result_forum_booklist[$i]['book_sid'];
				$arrys_book_info		=get_book_info($conn_mssr,$book_sid,$array_filter=array(),$arry_conn_mssr);
				$book_name 				= mysql_prep(trim($arrys_book_info[0]['book_name']));

				//book_name		書名
				if(mb_strlen($book_name)>7){
					$book_name=mb_substr($book_name,0,7)."..";
				}?>
				<figure id="book_aside_book_info">

				<?php
						//書籍封面處理
						$bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
						if(file_exists($bookpic_root)){
							$rs_bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
						}else{
							$rs_bookpic_root = 'image/book.jpg';
						}?>
						<a onclick="action_log('inc/add_action_forum_log/code.php','ga10',<?php echo $_SESSION["uid"];?>,0,0,'<?php echo $book_sid;?>','',<?php echo $forum_id;?>,0,<?php echo $article_id;?>,0,'mssr_forum_book_discussion.php?book_sid=<?php echo $book_sid?>');void(0);"
                        href="javascript:void(0);"><img id="book_aside_book_info_pic" src="<?php echo $rs_bookpic_root?>" alt=""/></a>


					<a id="book_aside_book_info_bookname"
                    onclick="action_log('inc/add_action_forum_log/code.php','ga10',<?php echo $_SESSION["uid"];?>,0,0,'<?php echo $book_sid;?>','',<?php echo $forum_id;?>,0,<?php echo $article_id;?>,0,'mssr_forum_book_discussion.php?book_sid=<?php echo $book_sid?>');void(0);"
                    href="javascript:void(0);"><?php echo $book_name?></a>
				</figure>
       		<?php }?>
       	<?php }else{
			echo "目前暫無資料";
		}?>
    </section>

    <!----------群---------->
    <section class="book_aside_group">
    	<p id="book_aside_group_title">參加這個聊書小組的人，<br/>他們也參加了那些聊書小組?</p>
    	<img id="book_aside_group_icon" src="image/icon_hot.png" alt="" width="30" height="30"/>

        <?php
		if(count($arry_group_id_list)!==0){
			for($i=0; $i<count($arry_list); $i++){
				$rs_forum_id   = mysql_prep(trim($arry_list[$i]));
				//-----------------------------------------------
	        	//SQL-找討論區名稱
	        	//-----------------------------------------------
				$sql="
					SELECT
						`forum_name`
					FROM
						`mssr_forum`
					WHERE
						`forum_id` = $rs_forum_id
				";
                $arrys_result_forum_name=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$forum_name_tmp = $arrys_result_forum_name[0]['forum_name'];

				//forum_name		聊書小組名稱
				if(mb_strlen($forum_name)>7){
					$arrys_result_forum_name[0]['forum_name']=mb_substr($forum_name,0,7)."..";
				}?>
                <figure id="book_aside_group_info">
                	<?php
					//聊書小組封面處理
					$forumpic_root = 'image/forum_pic_'.$rs_forum_id.'.jpg';
					if(file_exists($forumpic_root)){
						$rs_forumpic_root = 'image/forum_pic_'.$rs_forum_id.'.jpg';
					}else{
						$rs_forumpic_root = 'image/forum_pic.jpg';
					}?>

                    <a onclick="action_log('inc/add_action_forum_log/code.php','ga11',<?php echo $_SESSION["uid"];?>,0,0,'','',<?php echo $forum_id;?>,<?php echo $rs_forum_id;?>,<?php echo $article_id;?>,0,'mssr_forum_group_discussion.php?forum_id=<?php echo $rs_forum_id?>');void(0);"
                    href="javascript:void(0);"><img id="book_aside_group_info_pic" src="<?php echo $rs_forumpic_root?>" alt=""/></a>

                    <a id="book_aside_group_info_groupname"
                    onclick="action_log('inc/add_action_forum_log/code.php','ga11',<?php echo $_SESSION["uid"];?>,0,0,'','',<?php echo $forum_id;?>,<?php echo $rs_forum_id;?>,<?php echo $article_id;?>,0,'mssr_forum_group_discussion.php?forum_id=<?php echo $rs_forum_id?>');void(0);"
                    href="javascript:void(0);"><?php echo $forum_name_tmp?></a>
                </figure>
			<?php }?>
		<?php }else{
			echo "目前暫無資料";
		}?>

    </section>
</aside>
<!--=======================================================================================================-->
<!--=============================================頁尾=======================================================-->
<!--=======================================================================================================-->
<footer>
	明日星球-聊書
</footer>
</body>
</html>