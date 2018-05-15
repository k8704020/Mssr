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
					$book_sid = mysql_prep(trim($_GET["book_sid"]));

				//-----------------------------------------------
	        	//SQL-發文列表(分頁)
	        	//-----------------------------------------------
					$query_sql="
						SELECT
							`mssr_forum_article`.`cat_id`,
							`mssr_forum_article`.`user_id`,
							`mssr_forum_article`.`article_content`,
							`mssr_forum_article`.`keyin_cdate`,
							`mssr_forum_article`.`article_title`,
							`mssr_forum_article`.`article_like_cno`,
							`mssr_forum_article`.`article_id`,
							`mssr_forum_article`.`article_state`,

							`mssr_article_book_rev`.`article_cdate`,
							`mssr_article_book_rev`.`reply_cdate`,
							`mssr_article_book_rev`.`book_sid`,

							(
								SELECT CASE

									WHEN `mssr_forum_article`.`keyin_cdate` > `mssr_article_book_rev`.`reply_cdate`
										THEN `mssr_forum_article`.`keyin_cdate`

									WHEN `mssr_forum_article`.`keyin_cdate` < `mssr_article_book_rev`.`reply_cdate`
										THEN `mssr_article_book_rev`.`reply_cdate`
								END

							) AS `order_filter`

						FROM `mssr_forum_article`
							INNER JOIN `mssr_article_book_rev` ON
							`mssr_forum_article`.`article_id`=`mssr_article_book_rev`.`article_id`
						WHERE 1=1
							AND `mssr_article_book_rev`.`book_sid` = '$book_sid'
							AND `article_state` LIKE '%正常%'
						ORDER BY `order_filter` DESC
					";
					$arrys_result_article=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);

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
					$sql="
						SELECT
							`user_id`, `book_sid`
						FROM
							`mssr_book_borrow_semester`
						WHERE 1=1
							AND `book_sid` = '$book_sid'
						GROUP BY
							`user_id`
						ORDER BY
							`borrow_sdate` DESC

					";
					$arrys_result_aside=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);
					$numrow_aside=count($arrys_result_aside);
					
					
				//-----------------------------------------------
	        	//SQL-誰也看過這本書
	        	//-----------------------------------------------
					$query_sql="
						SELECT
							`user_id`, `book_sid`
						FROM
							`mssr_book_borrow_semester`
						WHERE 1=1
							AND `book_sid` = '$book_sid'
						GROUP BY
							`user_id`
						ORDER BY
							`borrow_sdate` DESC

					";
					$arrys_result_member2=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					//$numrow_aside=count($arrys_result_aside);

				//-----------------------------------------------
	        	//SQL-幾位朋友看過這本書
	        	//-----------------------------------------------
					//先找出朋友名單
					$sql="
						SELECT
							`user_id`,`friend_id`
						FROM
							`mssr_forum_friend`
						WHERE 1=1
							AND (
								`user_id` = {$_SESSION['uid']}
									OR
								`friend_id` = {$_SESSION['uid']}
							)
							AND `friend_state` = '成功'
					";
					$arrys_result_friend=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_friend=count($arrys_result_friend);

					//mssr_forum_friend：user|friend欄位判斷
					$rs_arrys_result_friend=array();
					for($i=0; $i<$numrow_friend;$i++){
						if($arrys_result_friend[$i]['user_id']<>$_SESSION['uid']){
							$rs_arrys_result_friend[$i]['user_id']   = $arrys_result_friend[$i]['friend_id'];
							$rs_arrys_result_friend[$i]['friend_id'] = $arrys_result_friend[$i]['user_id'];
						}else{
							$rs_arrys_result_friend[$i]['user_id']   = $arrys_result_friend[$i]['user_id'];
							$rs_arrys_result_friend[$i]['friend_id'] = $arrys_result_friend[$i]['friend_id'];
						}
					}

					//判斷朋友是否看過這本書
					$rs_arrys_result_friend_also_look = array();
					for($i=0; $i<count($rs_arrys_result_friend); $i++){
						$sql="
							SELECT
								`book_sid`, `user_id`
							FROM
								`mssr_book_borrow_semester`
							WHERE 1=1
								AND `book_sid` = '{$book_sid}'
								AND `user_id`  = '{$rs_arrys_result_friend[$i]['friend_id']}'


						";
						$arrys_result_friend_also_look=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
						$rs_arrys_result_friend_also_look += $arrys_result_friend_also_look;
					}

					//剔除重複資料
					$rs_arrys_result_friend_also_look = array_unique($rs_arrys_result_friend_also_look);

				//-----------------------------------------------
	        	//SQL-看過這本書的人，他們也看了?
	        	//-----------------------------------------------
					//找最近看過這本書的3個人
					$rs_uid = $_SESSION['uid'];
					$sql="
						SELECT
							`user_id`
						FROM
							`mssr_book_borrow_semester`
						WHERE 1=1
							AND `book_sid` = '$book_sid'
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
							WHERE 1=1
								AND `user_id` = $user_id
								AND `book_sid` <> '$book_sid'

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
							`book_sid` = '{$book_sid}'
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

	//---------------------------------------------------
    //檢驗
    //---------------------------------------------------
		//echo "<pre>";
		//print_r($sql);
		//echo "</pre>";
		//die();

	//---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球-書籍討論";

	//---------------------------------------------------
    //分頁處理
    //---------------------------------------------------
		$numrow=0;  //資料總筆數
		$psize =15;  //單頁筆數,預設8筆
		$pnos  =0;  //分頁筆數
		$pinx  =1;  //目前分頁索引,預設1
		$sinx  =0;  //值域起始值
		$einx  =0;  //值域終止值

		if(count($arrys_result_member2)!==0){
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
			$numrow=count($arrys_result_member2);

			$pnos  =ceil($numrow/$psize);
			$pinx  =($pinx>$pnos)?$pnos:$pinx;
			$sinx  =(($pinx-1)*$psize)+1;
			$einx  =(($pinx)*$psize);
			$einx  =($einx>$numrow)?$numrow:$einx;
			//echo $numrow."<br/>";

			$arrys_result_member=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx-1,$psize),$arry_conn_mssr);
		}else{}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title ?></title>
</head>
<link href="css/mssr_forum(position).css" 	type="text/css" rel="stylesheet" />
<link href="css/mssr_forum(style).css" 		type="text/css" rel="stylesheet" />
<link href="../../inc/code.css" 			type="text/css" rel="stylesheet" />
<script	type="text/javascript" 	src="jquery-1.10.2.min.js"></script>
<script	type="text/javascript" 	src="jquery.blockUI.js"></script>
<script type="text/javascript" 	src="../../inc/code.js"></script>
<script type="text/javascript" 	src="../../lib/js/vaildate/code.js"></script>
<script type="text/javascript" 	src="../../lib/js/public/code.js"></script>
<script type="text/javascript" 	src="../../lib/js/string/code.js"></script>
<script type="text/javascript" 	src="../../lib/js/table/code.js"></script>
<script type="text/javascript" src="inc/add_action_forum_log/code.js"></script>
<script>
	var psize=<?php echo $psize;?>;
	var pinx =<?php echo $pinx;?>;
	var book_sid='<?php echo addslashes($book_sid);?>';
	var check =<?php echo count($arrys_result_book_check);?>;

	//對書籍追蹤
	function book_fav(user_id, book_sid){
		//參數
		var user_id   		=parseInt(user_id);
		var book_sid      	=trim(book_sid);

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
            action_log('inc/add_action_forum_log/code.php','b2',<?php echo $_SESSION["uid"];?>,0,0,book_sid,'',0,0,0,0,url);
		}else{
			alert("已經取消追蹤這本書。");
            action_log('inc/add_action_forum_log/code.php','b3',<?php echo $_SESSION["uid"];?>,0,0,book_sid,'',0,0,0,0,url);
		}

        return true;
	}

	//滑鼠移入
	function mouse_over(obj){
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
			'page_name' :'mssr_forum_book_member.php',
			'page_args' :{
				'book_sid':book_sid
			}
		}
		var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);

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
    <a onclick="action_log('inc/add_action_forum_log/code.php','b0',<?php echo $_SESSION["uid"];?>,0,0,'<?php echo $book_sid;?>','',0,0,0,0,'index.php');void(0);"
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
            <a href="mssr_forum_book_discussion.php?book_sid=<?php echo $_GET["book_sid"]?>"><img src="<?php echo $rs_bookpic_root?>" alt="<?php echo $_GET["book_sid"]?>" width="110" height="110"/></a>
        </div>

        <!----------書籍資訊---------->
        <div class="book_info">
            <?php
            for($i=0; $i<1; $i++){
                $book_sid 			= mysql_prep(trim($_GET["book_sid"]));
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
        <p id="header_info_bookinfo">這本書有，<b><?php echo $numrow_book_article?></b>篇發文，<br/><b><?php echo $numrow_book_reply?></b>篇回覆，<b><?php echo count($arrys_result_member2)?></b>個人看<br/>過這本書，其中有<?php echo count($rs_arrys_result_friend_also_look)?>位朋友。<br/>還沒看過趕快來看呦！</p>
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
            onclick="book_fav(<?php echo $_SESSION["uid"];?>,'<?php echo $_GET["book_sid"];?>');void(0);" />
            <!--抓SESSION-->

	<?php }else{?>
    	<img id="mssr_forum_book_favorite" src="image/like_cancel.jpg" alt="" width="50" height="50"
            style='position:relative;z-index:99;';
            onmouseover="mouse_over(this);void(0);"
            onclick="book_fav(<?php echo $_SESSION["uid"];?>,'<?php echo $_GET["book_sid"];?>');void(0);" />
            <!--抓SESSION-->
    <?php }?>




    

    <!----------NAV---------->
	<nav>
    	<ul>
        	<!--<li><a href="mssr_forum_book_discussion.php?book_sid=<?php echo $_GET["book_sid"]?>">書本介紹</a></li>-->
            <li><a href="mssr_forum_book_discussion.php?book_sid=<?php echo $_GET["book_sid"];?>" >討論</a></li>
            <li><a href="mssr_forum_book_shelf.php?book_sid=<?php echo $_GET["book_sid"];?>" >看過這本書的人也看過</a></li>
            <li><a href="mssr_forum_book_member.php?book_sid=<?php echo $_GET["book_sid"];?>" style="background-image:url(image/icon_circle.png); background-repeat:no-repeat; background-position:center">誰也看過?</a></li>
        </ul>
    </nav>
</header>

<!----------分頁---------->
<div class="table_page">

        <table  border="0" width="100%" style='position:relative;top:0px; left:-10px;<?php if(count($arrys_result_article)===0)echo 'display:none;'?>'>
            <tr valign="middle">
                <td align="left">
                    <!-- 分頁列 -->
                    <span id="page" style="position:relative;top:0px;"></span>
                </td>
            </tr>
        </table>

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
        <textarea name="book_sid"  style="display:none" cols="3" rows="8"><?php echo $_GET["book_sid"]?></textarea>
        <input id="r1"  class="mssr_input_box_radio" type="radio" name="article_refer_code" value="1a" /><p id="p1" class="mssr_input_box_radio_text" style="width:200px; height:25px;">我對於這本書的情節有疑問，因為…</p>
		<input id="r2"  class="mssr_input_box_radio" type="radio" name="article_refer_code" value="1b" /><p id="p2" class="mssr_input_box_radio_text" style="width:200px; height:25px;">我對於這本書的角色有疑問，因為…</p>
        <input id="r3"  class="mssr_input_box_radio" type="radio" name="article_refer_code" value="1c" /><p id="p3" class="mssr_input_box_radio_text" style="width:200px; height:25px;">我對於這本書的背景有疑問，因為…</p>
        <input type="text" id="action_code" name="action_code" value="b1" style='display:none;'>
        <input id="mssr_input_box_submit" type="submit" value="送出"/>
   	</form>
</div>
<!--=======================================================================================================-->
<!--=============================================主頁面=====================================================-->
<!--=======================================================================================================-->

<section class="course_2">

	<?php
	if(empty($arrys_result_member)){
		echo("這個討論區目前沒人喔！");
	}else{
		for($i=0;$i<count($arrys_result_member);$i++){
			$user_id = $arrys_result_member[$i]['user_id'];

			//-----------------------------------------------
			//SQL-userinfo(學生資訊)
			//-----------------------------------------------
			$sql="
				SELECT
					`name`
				FROM
					`member`
				WHERE
					`uid` = $user_id
			";
			$arrys_result_userinfo=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
			$user_name   	= $arrys_result_userinfo[0]['name'];?>
            <figure>

            <?php
			$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
			if($get_user_info[0]['sex']==1){?>
            	<a onclick="action_log('inc/add_action_forum_log/code.php','g6',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',<?php echo $forum_id;?>,0,0,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                href="javascript:void(0);"><img src="image/boy.jpg" width="110px" height="110px" /></a>
            <?php }else{?>
            	<a onclick="action_log('inc/add_action_forum_log/code.php','g6',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',<?php echo $forum_id;?>,0,0,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>');void(0);"
                href="javascript:void(0);"><img src="image/girl.jpg" width="110px" height="110px" /></a>
            <?php }?>




                <figcaption><?php echo $user_name?></figcaption>
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


