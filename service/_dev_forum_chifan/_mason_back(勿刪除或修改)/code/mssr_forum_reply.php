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

				//-----------------------------------------------
	        	//SQL-留言資訊(mssr_reply_box_frame)
	        	//-----------------------------------------------
					$sql="
						SELECT
							`user_id`, `article_title`, `article_content`, `article_like_cno`, `keyin_cdate`, `article_state`
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
				//SQL-查book_sid
	        	//-----------------------------------------------
					$sql="
						SELECT
							`book_sid`
						FROM
							`mssr_article_book_rev`
						WHERE
							`article_id`=$article_id
					";
					$arrys_result_book_sid=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$book_sid		= $arrys_result_book_sid[0]['book_sid'];

				//-----------------------------------------------
	        	//SQL-這本書有多少發文
	        	//-----------------------------------------------
					$sql="
						SELECT
							`article_id`
						FROM
							`mssr_article_book_rev`
						WHERE
							`book_sid` = '$book_sid'
					";
					$arrys_result_book_article=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_book_article=count($arrys_result_book_article);
				//-----------------------------------------------
	        	//SQL-這本書有多少回覆
	        	//-----------------------------------------------
					$sql="
						SELECT
							`article_id`
						FROM
							`mssr_article_reply_book_rev`
						WHERE
							`book_sid` = '$book_sid'
					";
					$arrys_result_book_reply=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_book_reply=count($arrys_result_book_reply);
				//-----------------------------------------------
	        	//SQL-誰也看過這本書
	        	//-----------------------------------------------
					$book_sid = $arrys_result_book_sid[0]['book_sid'];
					$sql="
						SELECT
							`book_sid`, `user_id`
						FROM
							`mssr_book_borrow_semester`
						WHERE
							`book_sid` = '$book_sid'
						ORDER BY
							`borrow_sdate` DESC

					";
					$arrys_result_aside=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);
					$numrow_aside=count($arrys_result_aside);

				//$book_sid = $arrys_result[0]['book_sid'];
				//echo "<pre>";
                //print_r($arrys_result_comment_name);
                //echo "</pre>";
				//die();
				//-----------------------------------------------
	        	//SQL-看過這本書的人，他們也看了?
	        	//-----------------------------------------------
					//找最近看過這本書的3個人
					$sql="
						SELECT
							`user_id`
						FROM
							`mssr_book_borrow_semester`
						WHERE
							`book_sid` = '$book_sid'
						ORDER BY
							`borrow_sdate` DESC

					";
					$arrys_result_who_read=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,3),$arry_conn_mssr);
					$numrow_book_who_read=count($arrys_result_who_read);

					$arry_book_sid_list=array();
					for($i=0;$i<$numrow_book_who_read;$i++){
						$user_id 			= (int)$arrys_result_who_read[$i]['user_id'];
						//看過這本書的人，他們最近也看過甚麼書，挑重複度最高的3本
						$sql="
							SELECT
								`book_sid`
							FROM
								`mssr_book_borrow_semester`
							WHERE
								`user_id` = $user_id

						";
						$arrys_result_recommend=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

						if(!empty($arrys_result_recommend)){
							$arry_list=array();
							foreach($arrys_result_recommend as  $arry_result_recommend){

								$rs_book_sid=trim($arry_result_recommend['book_sid']);

								if(!array_key_exists($rs_book_sid,$arry_book_sid_list)){
									$arry_book_sid_list[$rs_book_sid]=1;
								}else{
									$arry_book_sid_list[$rs_book_sid]=$arry_book_sid_list[$rs_book_sid]+1;
								}
							}
							//排序
							arsort($arry_book_sid_list);

							//篩選
							foreach($arry_book_sid_list as $book_sid_tmp=>$cno){
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
	        	//SQL-哪個聊書小組也在討論這本書
	        	//-----------------------------------------------
					$sql="
						SELECT
							`forum_id`
						FROM
							`mssr_forum_booklist`
						WHERE
							`book_sid` = '$book_sid'
					";
					$arrys_result_which_group=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,3),$arry_conn_mssr);
					$numrow_which_group=count($arrys_result_which_group);
				//-----------------------------------------------
	        	//SQL-追蹤書籍
	        	//-----------------------------------------------
					$sql="
						SELECT
							`user_id`
						FROM
							`mssr_book_favorite`
						WHERE 1=1
                            AND `user_id`  =  {$_SESSION['uid']}
							AND `book_sid` = '{$book_sid       }'
					";
					$arrys_result_book_check=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				//echo "<pre>";
//				//print_r($arry_book_sid_list);
//				print_r($arry_list);
//				echo "</pre>";
//				echo $arry_list[0];
				//die();
				//echo($arry_book_sid_list[0][$key]);
				//echo($rs_book_sid);
				//-----------------------------------------------
	        	//SQL-查登入uid是否為老師
	        	//-----------------------------------------------
				$see_uid = $_SESSION['uid'];
					$sql="
						SELECT
							`uid`
						FROM
							`teacher`
						
						WHERE
							`uid` = $see_uid
					";
					
					
					$arrys_teacher=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
					$numrow_arrys_teacher=count($arrys_teacher);
					
					
					//echo "<pre>";
//					print_r($arrys_teacher);
//					echo "</pre>";
//					die();
					
	//---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球-書籍討論";

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
<script type="text/javascript" src="js/mssr_forum/code.js"></script>
<script type="text/javascript" src="inc/add_action_forum_log/code.js"></script>
<script>

	var psize=<?php echo $psize;?>;
	var pinx =<?php echo $pinx;?>;
	var article_id='<?php echo addslashes($article_id);?>';

	var check =<?php echo count($arrys_result_book_check)?>;

	function book_fav(user_id, book_sid){
	//對書籍追蹤

		//參數
		var user_id   		=parseInt(user_id);
		var book_sid      	=trim(book_sid);



		if((user_id===0)){
			alert('動作失敗!');
			return false;
		}

		//頁面條件
		var url='mssr_forum_book_favorite_A.php';
		url+='?user_id='+encodeURI(user_id);
		url+='&book_sid='+encodeURI(book_sid);


		if((user_id===0)){
			alert('動作失敗!');
			return false;
		}
		if((book_sid==='')){
			alert('動作失敗!');
			return false;
		}
		if(check==0){
			alert("已經將此書籍加入追蹤。");
            action_log('inc/add_action_forum_log/code.php','ba2',<?php echo $_SESSION["uid"];?>,0,0,book_sid,'',0,0,<?php echo $article_id;?>,0,url);
		}else{
			alert("已經取消追蹤這本書。");
            action_log('inc/add_action_forum_log/code.php','ba3',<?php echo $_SESSION["uid"];?>,0,0,book_sid,'',0,0,<?php echo $article_id;?>,0,url);
		}

        return true;
	}
	
	

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
		var url='mssr_forum_like_A.php';
		url+='?type='+encodeURI(type);
		url+='&user_id='+encodeURI(user_id);
		url+='&article_id='+encodeURI(article_id);

        if(type==='article'){
            action_log('inc/add_action_forum_log/code.php','ba7',<?php echo $_SESSION["uid"];?>,0,0,'<?php echo $book_sid?>','',0,0,article_id,0,url);
        }else{
            action_log('inc/add_action_forum_log/code.php','ba8',<?php echo $_SESSION["uid"];?>,0,0,'<?php echo $book_sid?>','',0,0,<?php echo $_GET["article_id"];?>,article_id,url);
        }
        return false;
	}

	
	function go_back(){
		window.history.back();

	}
	
	//隱藏回覆按鈕
	function reply_disable_close(obj){
		var mssr_comment_input_content	=document.getElementById('mssr_comment_input_content');
		var mssr_forum_reply			=document.getElementById('mssr_forum_reply');
		if(trim(mssr_comment_input_content.value)===''){
			alert("請輸入回復！");	
			return false; 
		}
		obj.disabled=true;
		mssr_forum_reply.submit();
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
			'page_name' :'mssr_forum_reply.php',
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
<body>
<!--=======================================================================================================-->
<!--=============================================頁頭=======================================================-->
<!--=======================================================================================================-->
<section id="logopic">
	<img src="image/logopic3.jpg" alt="" width=100% height="150"/>
    <a onclick="action_log('inc/add_action_forum_log/code.php','ba0',<?php echo $_SESSION["uid"];?>,0,0,'<?php echo $book_sid;?>','',0,0,<?php echo $article_id;?>,0,'index.php');void(0);"
    href="javascript:void(0);"><img id="home" src="image/home.png" /></a>
</section>
<header>
	<img src="image/namecard_book.png" width="40%" height="150px" />
	<section class="header_left" >


    	<!----------書籍圖片---------->
        <?php
        //書籍封面處理
		$bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
		if(file_exists($bookpic_root)){
			$rs_bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
		}else{
			$rs_bookpic_root = 'image/book.jpg';
		}?>



        <div class="header_pic">
            <a href="mssr_forum_book_discussion.php?book_sid=<?php echo $book_sid?>"><img src="<?php echo $rs_bookpic_root?>" alt="<?php echo $book_sid?>" width="110" height="110"/></a>
        </div>

        <!----------書籍資訊---------->
        <div class="book_info">
            <?php
            for($i=0; $i<1; $i++){

                $arrys_book_info	= get_book_info($conn_mssr,$book_sid,$array_filter=array(),$arry_conn_mssr);
                $book_name 			= mysql_prep(trim($arrys_book_info[0]['book_name']));
                $book_author 		= mysql_prep(trim($arrys_book_info[0]['book_author']));
                $book_publisher 	= mysql_prep(trim($arrys_book_info[0]['book_publisher']));
                $book_isbn_13 		= mysql_prep(trim($arrys_book_info[0]['book_isbn_13']));?>

                <h1 id="book_info_name"><b><?php echo $book_name?></b></h1>
                <h7>作者：<?php echo $book_author?><br>出版社:<?php echo $book_publisher?></h7>
            <?php }?>
        </div>
    </section>

    <!----------書籍閱讀資訊---------->
    <div class="header_info">
        <img id="header_info_owl" src="image/owl.png"  />
        <p id="header_info_bookinfo">這本書有，<b><?php echo $numrow_book_article?></b>篇發文，<br/><b><?php echo $numrow_book_reply?></b>篇回覆，<b><?php echo $numrow_aside?></b>位朋友看<br/>過這本書。<br/>還沒看過趕快來看呦！</p>
    </div>

	<!----------發文---------->
    <img id="open_mssr_input_box" src="image/article_publish.png" alt="" width="50" height="50"
            style='position:relative;z-index:99;';
            onmouseover="mouse_over(this);void(0);"
            onclick=";void(0);" />

    <!----------追蹤書籍---------->
    <?php
	if(count($arrys_result_book_check)!==0){?>
    	<img id="mssr_forum_book_favorite" src="image/like.jpg" alt="" width="50" height="50"
            style='position:relative;z-index:99;';
            onmouseover="mouse_over(this);void(0);"
            onclick="book_fav(<?php echo (int)$_SESSION["uid"];?>,'<?php echo $book_sid;?>');void(0);" />
            <!--抓SESSION-->

	<?php }else{?>
    	<img id="mssr_forum_book_favorite" src="image/like_cancel.jpg" alt="" width="50" height="50"
            style='position:relative;z-index:99;';
            onmouseover="mouse_over(this);void(0);"
            onclick="book_fav(<?php echo (int)$_SESSION["uid"];?>,'<?php echo $book_sid;?>');void(0);" />
            <!--抓SESSION-->
    <?php }?>

    


    <!----------NAV---------->
	<nav>
    	<ul>
        	<!--<li><a href="mssr_forum_book_discussion.php?book_sid=<?php echo $book_sid?>" >書本介紹</a></li>-->
            <li><a href="mssr_forum_book_discussion.php?book_sid=<?php echo $book_sid?>" style="background-image:url(image/icon_circle.png); background-repeat:no-repeat; background-position:center">討論</a></li>
            <li><a href="mssr_forum_book_shelf.php?book_sid=<?php echo $book_sid;?>" >看過這本書的人也看過</a></li>
            <li><a href="mssr_forum_book_member.php?book_sid=<?php echo $book_sid;?>">誰也看過?</a></li>
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
	<form action="mssr_forum_book_discussion_A.php" method="post">
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
        <textarea name="book_sid"  style="display:none" cols="3" rows="8"><?php echo $book_sid;?></textarea>
        <input id="r1"  class="mssr_input_box_radio" type="radio" name="article_refer_code" value="1a" /><p id="p1" class="mssr_input_box_radio_text" style="width:200px; height:25px;">我對於這本書的情節有疑問，因為…</p>
		<input id="r2"  class="mssr_input_box_radio" type="radio" name="article_refer_code" value="1b" /><p id="p2" class="mssr_input_box_radio_text" style="width:200px; height:25px;">我對於這本書的角色有疑問，因為…</p>
        <input id="r3"  class="mssr_input_box_radio" type="radio" name="article_refer_code" value="1c" /><p id="p3" class="mssr_input_box_radio_text" style="width:200px; height:25px;">我對於這本書的背景有疑問，因為…</p>
        <input type="text" id="action_code" name="action_code" value="ba1" style='display:none;'>
        <input id="mssr_input_box_submit" type="submit" value="送出" />
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
					<a onclick="action_log('inc/add_action_forum_log/code.php','ba10',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'<?php echo $book_sid;?>','',0,0,<?php echo $article_id;?>,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                    href="javascript:void(0);"><img id="mssr_reply_box_pic" src="image/boy.jpg" alt="" width="100" height="100"/></a>
				<?php }else{?>
					<a onclick="action_log('inc/add_action_forum_log/code.php','ba10',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'<?php echo $book_sid;?>','',0,0,<?php echo $article_id;?>,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                    href="javascript:void(0);"><img id="mssr_reply_box_pic" src="image/girl.jpg" alt="" width="100" height="100"/></a>
				<?php }?>

                <h3 id="mssr_reply_box_title"><?php echo $article_title?></h3>
                <p id="mssr_reply_box_content" ><?php echo $article_content?></p>
                <p id="mssr_reply_box_time"><?php echo $keyin_cdate?>(#<?php echo $_GET["article_id"];?>)</p>

                <img id="mssr_reply_box_likepic" src="image/like.png" alt="" width="10" height="10"
                onmouseover="mouse_over(this);void(0);"
                onclick="like('article',<?php echo $_SESSION["uid"];?>,<?php echo $article_id;?>);void(0);"/>

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
			$numrow_like_reply=count($arrys_result_like_reply);
			
			//-----------------------------------------------
			//SQL-檢舉
			//-----------------------------------------------
			$sql="
				SELECT
					`report_from`
				FROM
					`mssr_forum_article_reply_report_log`
				WHERE
					`reply_id` = '$reply_id'
			";
			$arrys_result_report_reply=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
			$numrow_like_report=count($arrys_result_report_reply);
			
			
			if($numrow_like_report>=2){
				$reply_content = "這篇留言已經被檢舉！";
				
			}
			
			?>
            <figure id="mssr_comment_box_frame">

            	<?php
				//學生照片
				$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
				if($get_user_info[0]['sex']==1){?>
					<a onclick="action_log('inc/add_action_forum_log/code.php','ba11',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'<?php echo $book_sid;?>','',0,0,<?php echo $article_id;?>,<?php echo $reply_id;?>,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                    href="javascript:void(0);"><img id="mssr_comment_box_pic" src="image/boy.jpg" alt="" width="100" height="100"/></a>
				<?php }else{?>
					<a onclick="action_log('inc/add_action_forum_log/code.php','ba11',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'<?php echo $book_sid;?>','',0,0,<?php echo $article_id;?>,<?php echo $reply_id;?>,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                    href="javascript:void(0);"><img id="mssr_comment_box_pic" src="image/girl.jpg" alt="" width="100" height="100"/></a>
				<?php }?>






                <figcaption id="mssr_comment_box_name"><?php echo $arrys_result_username[0]['name'];?></figcaption>

                <?php 
				if($numrow_like_report>=7){
					$reply_content = "這篇留言已經被檢舉！";?>
					 <p id="mssr_comment_box_content" style="text-align:center; color:#FF0000"; ><?php echo $reply_content?></p>
                     
                     <p id="mssr_comment_box_time"><?php echo $keyin_cdate?>(#<?php echo $reply_id; ?>)</p>


                    <img id="mssr_comment_box_likepic" src="image/like.png" alt="" width="10" height="10"
                    onmouseover="mouse_over(this);void(0);"
                    onclick="like('reply',<?php echo $_SESSION["uid"];?>,<?php echo $reply_id;?>);void(0);"/>
    
                    <p id="mssr_comment_box_likecnt"><?php echo $numrow_like_reply?></p>
                    
                    <img id="mssr_comment_box_reportpic" src="image/report.png"  
                    onmouseover="mouse_over(this);void(0);" 
                    onclick="report('reply',<?php echo $_SESSION['uid'];?>,<?php echo $user_id;?>,<?php echo $reply_id;?>);void(0);" alt="" width="10" height="10"/>
                    <p id="mssr_comment_box_reportcnt"><?php echo $numrow_like_report; ?></p>
              	<?php }?>
				
				
                <p id="mssr_comment_box_content"><?php echo $reply_content?></p>
                <p id="mssr_comment_box_time"><?php echo $keyin_cdate?>(#<?php echo $reply_id; ?>)</p>


                <img id="mssr_comment_box_likepic" src="image/like.png" alt="" width="10" height="10"
                onmouseover="mouse_over(this);void(0);"
                onclick="like('reply',<?php echo $_SESSION["uid"];?>,<?php echo $reply_id;?>);void(0);"/>

                <p id="mssr_comment_box_likecnt"><?php echo $numrow_like_reply?></p>
                
                <img id="mssr_comment_box_reportpic" src="image/report.png"  
                onmouseover="mouse_over(this);void(0);" 
                onclick="report('reply',<?php echo $_SESSION['uid'];?>,<?php echo $user_id;?>,<?php echo $reply_id;?>);void(0);" alt="" width="10" height="10"/>
                <p id="mssr_comment_box_reportcnt"><?php echo $numrow_like_report; ?></p>
                
                
            </figure>
        <?php }?>

        <!----------回覆的發文---------->

        <form id="mssr_forum_reply" action="mssr_forum_reply_A.php" method="post">
            <figure id="mssr_comment_input_frame">
				<?php
				//學生照片
				$get_user_info=get_user_info($conn_user,$_SESSION["uid"],$array_filter=array('sex','name'),$arry_conn_user);
				if($get_user_info[0]['sex']==1){?>
					<img id="mssr_comment_input_pic" src="image/boy.jpg" alt="" width="100" height="100"/>
				<?php }else{?>
					<img id="mssr_comment_input_pic" src="image/girl.jpg" alt="" width="100" height="100"/>
				<?php }?>

                <figcaption id="mssr_comment_input_name"><?php echo $get_user_info[0]['name'];?></figcaption>
                <textarea id="mssr_comment_input_content" name="mssr_comment_input_name_content" cols="26" rows="8"></textarea>
                <textarea 	name="article_id"  	style="display:none" cols="2" rows="8"><?php echo $_GET["article_id"]?></textarea>

                <input id="mssr_comment_input_submit"  onclick="reply_disable_close(this);" type="button" value="送出"/>
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
        <p id="book_aside_people_title">誰也看過這本書</p>
    	<img id="book_aside_people_icon" src="image/icon_hot.png" alt="" width="30" height="30"/>
        <!--誰也看過這本書-->
        <?php
		if(count($arrys_result_aside)!==0){
        	for($i=0; $i<count($arrys_result_aside); $i++){
				$user_id = $arrys_result_aside[$i]['user_id'];
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
                    	<a onclick="action_log('inc/add_action_forum_log/code.php','ba4',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'<?php echo $book_sid;?>','',0,0,<?php echo $article_id;?>,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                        href="javascript:void(0);"><img id="book_aside_people_info_pic" src="image/boy.jpg" alt=""/></a>
					<?php }else{?>
						<a onclick="action_log('inc/add_action_forum_log/code.php','ba4',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'<?php echo $book_sid;?>','',0,0,<?php echo $article_id;?>,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                        href="javascript:void(0);"><img id="book_aside_people_info_pic" src="image/girl.jpg" alt=""/></a>
					<?php }?>

                    <a id="book_aside_people_info_name"
                    onclick="action_log('inc/add_action_forum_log/code.php','ba4',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'<?php echo $book_sid;?>','',0,0,<?php echo $article_id;?>,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                    href="javascript:void(0);"><?php echo $arrys_result_replyname[0]["name"]?></a>
        		</figure>
        	<?php }?>
        <?php }else{
			echo "目前暫無資料";
		}?>
        <!--<p id="book_aside_icon">共有<?php echo $numrow_aside?>個人看過此書</p>-->
       	<!--<a id="book_aside_people_see_more" href="">看更多</a>-->
	</section>

	<!----------書---------->
    <section class="book_aside_book">
    	<p id="book_aside_book_title">看過這本書的人<br/>他們也看了哪些書?</p>
    	<img id="book_aside_book_icon" src="image/icon_hot.png" alt="" width="30" height="30"/>
        <?php
		if(count($arry_book_sid_list)!==0){
			//arsort($arry_book_sid_list);
			for($i=0; $i<count($arry_list); $i++){
				$rs_book_sid 				= mysql_prep(trim($arry_list[$i]));
				$arrys_book_info		=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array(),$arry_conn_mssr);
				$book_name 				= mysql_prep(trim($arrys_book_info[0]['book_name']));

				//book_name		書名
				if(mb_strlen($book_name)>7){
					$book_name=mb_substr($book_name,0,7)."..";
				}?>


                <figure id="book_aside_book_info">
                	<?php
                    //書籍封面處理
					$bookpic_root = '../../info/book/'.$rs_book_sid.'/img/front/simg/1.jpg';
					if(file_exists($bookpic_root)){
						$rs_bookpic_root = '../../info/book/'.$rs_book_sid.'/img/front/simg/1.jpg';
					}else{
						$rs_bookpic_root = 'image/book.jpg';
					}?>
                    <a onclick="action_log('inc/add_action_forum_log/code.php','ba5',<?php echo $_SESSION["uid"];?>,0,0,'<?php echo $book_sid;?>','<?php echo $rs_book_sid;?>',0,0,<?php echo $article_id;?>,0,'mssr_forum_book_discussion.php?book_sid=<?php echo $rs_book_sid?>');void(0);"
                    href="javascript:void(0);"><img id="book_aside_book_info_pic" src="<?php echo $rs_bookpic_root?>" alt=""/></a>
                    <a id="book_aside_book_info_bookname"
                    onclick="action_log('inc/add_action_forum_log/code.php','ba5',<?php echo $_SESSION["uid"];?>,0,0,'<?php echo $book_sid;?>','<?php echo $rs_book_sid;?>',0,0,<?php echo $article_id;?>,0,'mssr_forum_book_discussion.php?book_sid=<?php echo $rs_book_sid?>');void(0);"
                    href="javascript:void(0);"><?php echo $book_name?></a>
        		</figure>
        	<?php }?>
        <?php }else{
			echo "目前暫無資料";
		}?>
    </section>

    <!----------群---------->
    <section class="book_aside_group">
    	<p id="book_aside_group_title">哪個聊書小組<br/>正在討論這本書?</p>
    	<img id="book_aside_group_icon" src="image/icon_hot.png" alt="" width="30" height="30"/>
        <?php
		if(count($arrys_result_which_group)!==0){
			for($i=0; $i<count($arrys_result_which_group); $i++){
				$forum_id 				= mysql_prep(trim($arrys_result_which_group[$i]['forum_id']));
				//-----------------------------------------------
				//SQL-討論區名稱
				//-----------------------------------------------
				$sql="
					SELECT
						`forum_name`
					FROM
						`mssr_forum`
					WHERE
						`forum_id` = $forum_id
				";
				$arrys_result_forum_name=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				$forum_name = $arrys_result_forum_name[0]['forum_name'];

				//forum_name		聊書小組名稱
				if(mb_strlen($forum_name)>7){
					$arrys_result_forum_name[0]['forum_name']=mb_substr($forum_name,0,7)."..";
				}?>


                <figure id="book_aside_group_info">
                	<?php
					//聊書小組封面處理
					$forumpic_root = 'image/forum_pic_'.$forum_id.'.jpg';
					if(file_exists($forumpic_root)){
						$rs_forumpic_root = 'image/forum_pic_'.$forum_id.'.jpg';
					}else{
						$rs_forumpic_root = 'image/forum_pic.jpg';
					}?>
                    <a onclick="action_log('inc/add_action_forum_log/code.php','ba6',<?php echo $_SESSION["uid"];?>,0,0,'<?php echo $book_sid;?>','',<?php echo $forum_id;?>,0,<?php echo $article_id;?>,0,'mssr_forum_group_discussion.php?forum_id=<?php echo $forum_id?>');void(0);"
                    href="javascript:void(0);"><img id="book_aside_group_info_pic" src="<?php echo $rs_forumpic_root?>" alt=""/></a>
                    <a id="book_aside_group_info_groupname"
                    onclick="action_log('inc/add_action_forum_log/code.php','ba6',<?php echo $_SESSION["uid"];?>,0,0,'<?php echo $book_sid;?>','',<?php echo $forum_id;?>,0,<?php echo $article_id;?>,0,'mssr_forum_group_discussion.php?forum_id=<?php echo $forum_id?>');void(0);"
                    href="javascript:void(0);"><?php echo $forum_name?></a>
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

