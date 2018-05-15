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
    //SESSION
    //---------------------------------------------------

        //$_SESSION['uid']=11111;
        $sess_uid=(int)$_SESSION["uid"];
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
					$user_id = (int)$_GET["user_id"];
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
					$numrow_userinfo=count($arrys_result_userinfo);
				//-----------------------------------------------
	        	//SQL-userclasscode
	        	//-----------------------------------------------
					$sql="
						SELECT*
						FROM
							(SELECT
								`student`.`class_code`, `student`.`uid`
							FROM
								`student`

							UNION

							SELECT
								`teacher`.`class_code`, `teacher`.`uid`
							FROM
								`teacher`)tmp
						WHERE 1=1
							AND	`uid` = $user_id
						ORDER BY
							`class_code` DESC
					";
					$arrys_result_userclasscode=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
					$numrow_userclasscode=count($arrys_result_userclasscode);
				//-----------------------------------------------
	        	//SQL-arrys_result_user_school(用class_code找學生學校資訊)
	        	//-----------------------------------------------
					$user_school = mb_substr($arrys_result_userclasscode[0]['class_code'],0,3);
					$sql="
						SELECT
							`school_name`, `region_name`
						FROM
							`school`
						WHERE
							`school_code` = '$user_school'
					";
					$arrys_result_user_school=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
					$numrow_user_school=count($arrys_result_user_school);
				//-----------------------------------------------
	        	//SQL-usergrade(學生年級資訊)
	        	//-----------------------------------------------
					$class_code = $arrys_result_userclasscode[0]['class_code'];
					$sql="
						SELECT
							`grade`, `classroom`, `class_code`
						FROM
							`class`
						WHERE
							`class_code` = '$class_code'
					";
					$arrys_result_usergrade=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
					$numrow_usergrade=count($arrys_result_usergrade);
				//-----------------------------------------------
	        	//SQL-shelf(書櫃)
	        	//-----------------------------------------------
					$sql="
						SELECT
							`book_sid`
						FROM
							`mssr_book_borrow_semester`
						WHERE
							`user_id` = $user_id
					";
					$arrys_result_shelf=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_shelf=count($arrys_result_shelf);
				//-----------------------------------------------
	        	//SQL-學生發文數量
	        	//-----------------------------------------------
					$sql="
						SELECT
							`user_id`
						FROM
							`mssr_forum_article`
						WHERE
							`user_id` = $user_id

					";
					$arrys_result_articlenum=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_articlenum=count($arrys_result_articlenum);
				//-----------------------------------------------
	        	//SQL-學生回復數量
	        	//-----------------------------------------------
					$sql="
						SELECT
							`user_id`
						FROM
							`mssr_forum_article_reply`
						WHERE
							`user_id` = $user_id

					";
					$arrys_result_replynum=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_replynum=count($arrys_result_replynum);
				//-----------------------------------------------
	        	//SQL-我的留言(計算分頁)
	        	//-----------------------------------------------
					$query_sql="
						SELECT*
						FROM (
								SELECT
									`mssr_forum_article`.`user_id`,
									`mssr_forum_article`.`article_id`,
									`mssr_forum_article`.`article_content`,
									`mssr_forum_article`.`keyin_cdate`,
									`mssr_forum_article`.`article_state`,

									`mssr_article_book_rev`.`book_sid`,
									 'book_article' AS article_from,
									'NO' AS reply_id
								FROM
									`mssr_forum_article`
								INNER JOIN
									`mssr_article_book_rev`
								ON
									`mssr_forum_article`.`article_id`=`mssr_article_book_rev`.`article_id`

							UNION ALL

								SELECT
									`mssr_forum_article_reply`.`user_id`,
									`mssr_forum_article_reply`.`article_id`,
									`mssr_forum_article_reply`.`reply_content`,
									`mssr_forum_article_reply`.`keyin_cdate`,
									`mssr_forum_article_reply`.`reply_state`,

									`mssr_article_reply_book_rev`.`book_sid`,
									'book_article_reply' AS article_from,
									`mssr_forum_article_reply`.`reply_id`
								FROM
									`mssr_forum_article_reply`
								INNER JOIN
									`mssr_article_reply_book_rev`
								ON
									`mssr_forum_article_reply`.`reply_id`=`mssr_article_reply_book_rev`.`reply_id`

							UNION ALL

								SELECT
									`mssr_forum_article`.`user_id`,
									`mssr_forum_article`.`article_id`,
									`mssr_forum_article`.`article_content`,
									`mssr_forum_article`.`keyin_cdate`,
									`mssr_forum_article`.`article_state`,

									`mssr_article_forum_rev`.`forum_id`,
									'forum_article' AS article_from,
									'NO' AS reply_id
								FROM
									`mssr_forum_article`
								INNER JOIN
									`mssr_article_forum_rev`
								ON
									`mssr_forum_article`.`article_id`=`mssr_article_forum_rev`.`article_id`

							UNION ALL

								SELECT
									`mssr_forum_article_reply`.`user_id`,
									`mssr_forum_article_reply`.`article_id`,
									`mssr_forum_article_reply`.`reply_content`,
									`mssr_forum_article_reply`.`keyin_cdate`,
									`mssr_forum_article_reply`.`reply_state`,

									`mssr_article_reply_forum_rev`.`forum_id`,
									'forum_article_reply' AS article_from ,
									`mssr_forum_article_reply`.`reply_id`

								FROM
									`mssr_forum_article_reply`
								INNER JOIN
									`mssr_article_reply_forum_rev`
								ON
									`mssr_forum_article_reply`.`reply_id`=`mssr_article_reply_forum_rev`.`reply_id`
							)tmp
						WHERE 1=1
							AND `user_id`= $user_id
							AND `article_state` LIKE '%正常%'
							
						ORDER BY
							`keyin_cdate` DESC
					";

					$arrys_result_myreply=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);

				//echo "<pre>";
//                print_r($arrys_result_myreply);
//                echo "</pre>";
//				die();
				//-----------------------------------------------
	        	//SQL-檢查是否為好友
	        	//-----------------------------------------------

					$sql="
						SELECT
							`user_id`, `friend_id`
						FROM
							`mssr_forum_friend`
						WHERE 1=1
							AND `friend_state` 	= '成功'
							AND ((`user_id`		= $user_id
								AND
								 `friend_id`	= $sess_uid)
									OR
								 (`user_id`		= $sess_uid
								 AND
								 `friend_id`	=$user_id))

					";
					$arrys_result_friend_check=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
				//-----------------------------------------------
	        	//SQL-查登入uid是否為老師
	        	//-----------------------------------------------
				
					$sql="
						SELECT
							`uid`
						FROM
							`teacher`
						
						WHERE
							`uid` = $sess_uid
					";
					
					
					$arrys_teacher=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
					$numrow_arrys_teacher=count($arrys_teacher);
					
					

	//---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球-我的討論";

	//---------------------------------------------------
    //分頁處理
    //---------------------------------------------------
		$numrow=0;  //資料總筆數
		$psize =12;  //單頁筆數,預設5筆
		$pnos  =0;  //分頁筆數
		$pinx  =1;  //目前分頁索引,預設1
		$sinx  =0;  //值域起始值
		$einx  =0;  //值域終止值
		if(count($arrys_result_myreply)!==0){
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
			$numrow=count($arrys_result_myreply);

			$pnos  =ceil($numrow/$psize);
			$pinx  =($pinx>$pnos)?$pnos:$pinx;

			$sinx  =(($pinx-1)*$psize)+1;
			$einx  =(($pinx)*$psize);
			$einx  =($einx>$numrow)?$numrow:$einx;
			//echo $numrow."<br/>";

			$arrys_result_myreply=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx-1,$psize),$arry_conn_mssr);
		}else{}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title?></title>
<link href="css/mssr_forum(position).css" 	type="text/css" rel="stylesheet" />
<link href="css/mssr_forum(style).css" 		type="text/css" rel="stylesheet" />
<link href="../../inc/code.css" 			type="text/css" rel="stylesheet" />
<script	type="text/javascript" src="jquery-1.10.2.min.js"></script>
<script	type="text/javascript" src="mssr_forum_people.js"></script>
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
	var user_id=<?php echo addslashes($user_id);?>;
	function mouse_over(obj){
	//滑鼠移入
		obj.style.cursor='pointer';
	}
	
	//隱藏文章
	function delete_article(type, user_id, article_id){
		//參數
		var type      =trim(type);
		var user_id   =parseInt(user_id);
		var article_id=parseInt(article_id);
		
		
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

	function add_friend(){
    //加為好友
        var url ='';
        var page=str_repeat('../',0)+'add/add_friendA.php';
        var arg ={
            'sess_uid':sess_uid,
            'user_id' :user_id,
            'psize'   :psize,
            'pinx'    :pinx
        };
        var _arg=[];
        for(var key in arg){
            _arg.push(key+"="+encodeURI(arg[key]));
        }
        arg=_arg.join("&");

        if(arg.length!=0){
            url+=page+"?"+arg;
        }else{
            url+=page;
        }

        if(confirm('你確定要加此人為好友嗎?')){
            action_log('inc/add_action_forum_log/code.php','p12',sess_uid,user_id,0,'','',0,0,0,0,url);
            //go(url,'self');
        }else{
            return false;
        }
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
			'page_name' :'mssr_forum_people_myreply.php',
			'page_args' :{
				'user_id':user_id
			}
		}
		var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
	});
	
	
</script>
</head>

<body>
<!--=======================================================================================================-->
<!--=============================================頁頭=======================================================-->
<!--=======================================================================================================-->
<section id="logopic">
	<img src="image/logopic2.jpg" alt="" width=100% height="150"/>
    <a onclick="action_log('inc/add_action_forum_log/code.php','p0',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',0,0,0,0,'index.php');void(0);"
    href="javascript:void(0);"><img id="home" src="image/home.png" /></a>
</section>
<header>
	<img src="image/namecard.png" width="40%" height="150px" />
    <section class="header_left" >

        <!----------個人資訊---------->
        <div class="header_pic">
        	<?php
			$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
			if($get_user_info[0]['sex']==1){?>
            	<img src="image/boy.jpg" width="110px" height="110px" />
            <?php }else{?>
            	<img src="image/girl.jpg" width="110px" height="110px" />
            <?php }?>
        </div>

        <div class="stud_info">
            <h1 id="stud_info_name">
                <b><?php echo $arrys_result_userinfo[0]['name']?></b>
                <span style='position:relative;float:right;clear:right;'>
                    <?php if($sess_uid!==$user_id):?>
                    	<?php if(count($arrys_result_friend_check)==0){?>
                        	<input id="friend_check" type="button" value="加為好友" onclick="add_friend();void(0);">
                        <?php }else{?>
                        	<input id="friend_check" type="button" value="加為好友" onclick="add_friend();void(0);" style="visibility:hidden">
                        <?php }?>
                    <?php endif;?>
                </span>
            </h1>
            <h3><br/><?php echo $arrys_result_user_school[0]['school_name']?><?php echo $arrys_result_usergrade[0]['grade']?>年<?php echo $arrys_result_usergrade[0]['classroom']?>班</h3>
        </div>
    </section>

    <!----------個人閱讀狀況---------->
    <div class="header_info">
        <img id="reading_info" src="image/readingboy.png"  />
        <p id="p_book_red"><?php echo $numrow_shelf?></p>
        <p id="p_book_green"><?php echo $numrow_articlenum?></p>
        <p id="p_book_purple"><?php echo $numrow_replynum?></p>
        <img id="book_red" 		src="image/book_red.png"  />
        <img id="book_green" 	src="image/book_green.png"  />
        <img id="book_purple" 	src="image/book_purple.png"  />
    </div>

    <!----------NAV---------->
	<nav>
    	<ul>
        	<li><a onclick="action_log('inc/add_action_forum_log/code.php','p8',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',0,0,0,0,'mssr_forum_people_shelf.php?user_id=<?php echo $_GET["user_id"]?>');void(0);"
            href="javascript:void(0);">書櫃</a></li>

            <li><a onclick="action_log('inc/add_action_forum_log/code.php','p9',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',0,0,0,0,'mssr_forum_people_myreply.php?user_id=<?php echo $_GET["user_id"]?>');void(0);"
            href="javascript:void(0);" style="background-image:url(image/icon_circle.png); background-repeat:no-repeat; background-position:center">討論</a></li>

            <li><a onclick="action_log('inc/add_action_forum_log/code.php','p10',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',0,0,0,0,'mssr_forum_people_group.php?user_id=<?php echo $_GET["user_id"]?>');void(0);"
            href="javascript:void(0);">聊書小組</a></li>

            <li><a onclick="action_log('inc/add_action_forum_log/code.php','p11',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',0,0,0,0,'mssr_forum_people_friend.php?user_id=<?php echo $_GET["user_id"]?>');void(0);"
            href="javascript:void(0);">朋友</a></li>
        </ul>
    </nav>
</header>

<!----------分頁---------->
<div class="table_page">
	<?php if(count($arrys_result_myreply)!==0):?>
        <table border="0" width="100%" style='position:relative;top:0px; left:0px;'>
            <tr valign="middle">
                <td align="left">
                    <!-- 分頁列 -->
                    <span id="page" style="position:relative;top:0px;"></span>
                </td>
            </tr>
        </table>
	<?php endif;?>
</div>
<!--=======================================================================================================-->
<!--=============================================主頁面=====================================================-->
<!--=======================================================================================================-->
<section class="course_2">
<table>
	<thead>
		<tr>
        	<th scope="col">討論名稱(書籍/聊書小組)</th>
        	<th scope="col">發表時間</th>
        	<th scope="col">發表的內容(發文/回覆)</th>
        	<th scope="col">讚</th>
        	<th scope="col">回應</th>
      	</tr>
	</thead>
	<tbody>
        <!----------我的回應---------->
		<?php
		if(empty($arrys_result_myreply)){
			echo("你目前還沒有留過言喔，趕快去參與討論吧！");
		}else{
			for($i=0; $i<count($arrys_result_myreply); $i++){
				$book_sid 			= trim($arrys_result_myreply[$i]['book_sid']);
				$keyin_cdate 		= trim($arrys_result_myreply[$i]['keyin_cdate']);
				$article_content 	= trim($arrys_result_myreply[$i]['article_content']);
				$article_from 		= trim($arrys_result_myreply[$i]['article_from']);
				$article_id 		= trim($arrys_result_myreply[$i]['article_id']);

				//article_content		內容
				if(mb_strlen($article_content)>17){
					$article_content=mb_substr($article_content,0,17)."..";
				}


				//-----------------------------------------------
				//書籍發文
				//-----------------------------------------------
				if($article_from =="book_article"){
					$arrys_book_info=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
					$book_name 			= trim($arrys_book_info[0]['book_name']);

					//book_name		書名
					if(mb_strlen($book_name)>8){
						$book_name=mb_substr($book_name,0,8)."..";
					}
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
					//-----------------------------------------------
					//SQL-回復數量
					//-----------------------------------------------
					$sql="
						SELECT
							`reply_id`
						FROM
							`mssr_article_reply_book_rev`
						WHERE
							`article_id` = $article_id

					";
					$arrys_result_replynum=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_replynum=count($arrys_result_replynum);?>
					<tr class="even">
						<td><?php if($numrow_arrys_teacher!==0){?>
                                <img id="mssr_comment_box_delete" src="image/delete.png" alt="" width="10" height="10"
                                onmouseover="mouse_over(this);void(0);"
                                onclick="delete_article('article',<?php echo $user_id;?>,<?php echo $article_id;?>);void(0);"/>
                			<?php }?>
                            書籍：<?php echo $book_name?></td>
						<td><?php echo $keyin_cdate?></td>
						<td>
                            <a onclick="action_log('inc/add_action_forum_log/code.php','p2',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'<?php echo $book_sid;?>','',0,0,<?php echo $article_id?>,0,'mssr_forum_reply.php?article_id=<?php echo $article_id?>');void(0);"
                            href="javascript:void(0);">發文：<?php echo $article_content?></a>
                        </td>
						<td><?php echo $numrow_like?></td>
						<td><?php echo $numrow_replynum?></td>
					</tr>
             	<?php
				//-----------------------------------------------
				//書籍回復
				//-----------------------------------------------
				}else if($article_from =="book_article_reply"){
					$arrys_book_info=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
					$book_name 			= trim($arrys_book_info[0]['book_name']);
					$reply_id 			= trim($arrys_result_myreply[$i]['reply_id']);

					//article_content		內容
					if(mb_strlen($book_name)>10){
						$book_name=mb_substr($book_name,0,10)."..";
					}

					//-----------------------------------------------
					//SQL-回復數量
					//-----------------------------------------------
					$sql="
						SELECT
							`reply_id`
						FROM
							`mssr_article_reply_book_rev`
						WHERE
							`article_id` = $article_id

					";
					$arrys_result_replynum=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_replynum=count($arrys_result_replynum);
					//-----------------------------------------------
					//SQL-讚
					//-----------------------------------------------
					$sql="
						SELECT
							`user_id`
						FROM
							`mssr_forum_article_reply_like_log`
						WHERE
							`reply_id` = $reply_id
					";

					$arrys_result_like=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_like=count($arrys_result_like);


					?>
					<tr class="even">
						<td><?php if($numrow_arrys_teacher!==0){?>
                                <img id="mssr_comment_box_delete" src="image/delete.png" alt="" width="10" height="10"
                                onmouseover="mouse_over(this);void(0);"
                                onclick="delete_article('reply',<?php echo $user_id;?>,<?php echo $reply_id;?>);void(0);"/>
                			<?php }?>
                        
                        書籍：<?php echo $book_name?></td>
						<td><?php echo $keyin_cdate?></td>
						<td>
                            <a onclick="action_log('inc/add_action_forum_log/code.php','p3',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'<?php echo $book_sid;?>','',0,0,<?php echo $article_id?>,0,'mssr_forum_reply.php?article_id=<?php echo $article_id?>');void(0);"
                            href="javascript:void(0);">回覆：<?php echo $article_content?></a>
                        </td>
						<td><?php echo $numrow_like?></td>
						<td><?php echo $numrow_replynum?></td>
					</tr>
				<?php
				//-----------------------------------------------
				//社團發文
				//-----------------------------------------------
				}else if($article_from =="forum_article"){
					$sql="
						SELECT
							`forum_name`
						FROM
							`mssr_forum`
						WHERE
							`forum_id` = $book_sid
					";
					$arrys_result_forum_name=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$forum_name 	= $arrys_result_forum_name[0]['forum_name'];
					//forum_name		聊書小組名稱
					if(mb_strlen($forum_name)>10){
						$forum_name=mb_substr($forum_name,0,10)."..";
					}
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
					//-----------------------------------------------
					//SQL-回復數量
					//-----------------------------------------------
					$sql="
						SELECT
							`reply_id`
						FROM
							`mssr_article_reply_forum_rev`
						WHERE
							`article_id` = $article_id

					";
					$arrys_result_replynum=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_replynum=count($arrys_result_replynum);?>
					<tr class="even">
						<td><?php if($numrow_arrys_teacher!==0){?>
                                <img id="mssr_comment_box_delete" src="image/delete.png" alt="" width="10" height="10"
                                onmouseover="mouse_over(this);void(0);"
                                onclick="delete_article('article',<?php echo $user_id;?>,<?php echo $article_id;?>);void(0);"/>
                			<?php }?>
                        聊書小組：<?php echo $arrys_result_forum_name[0]['forum_name']?></td>
						<td><?php echo $keyin_cdate?></td>
						<td>
                            <a onclick="action_log('inc/add_action_forum_log/code.php','p4',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',<?php echo $book_sid;?>,0,<?php echo $article_id?>,0,'mssr_forum_group_reply.php?article_id=<?php echo $article_id?>');void(0);"
                            href="javascript:void(0);">發文：<?php echo $article_content?></a>
                        </td>
						<td><?php echo $numrow_like?></td>
						<td><?php echo $numrow_replynum?></td>
					</tr>
				<?php
				//-----------------------------------------------
				//社團回復
				//-----------------------------------------------
				}else if($article_from =="forum_article_reply"){
						$sql="
						SELECT
							`forum_name`
						FROM
							`mssr_forum`
						WHERE
							`forum_id` = $book_sid
					";
					$arrys_result_forum_name=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

					$forum_name 	= $arrys_result_forum_name[0]['forum_name'];
					//forum_name		聊書小組名稱
					if(mb_strlen($forum_name)>10){
						$forum_name=mb_substr($forum_name,0,10)."..";
					}
					//-----------------------------------------------
					//SQL-回復數量
					//-----------------------------------------------
					$sql="
						SELECT
							`reply_id`
						FROM
							`mssr_article_reply_book_rev`
						WHERE
							`article_id` = $article_id

					";
					$reply_id 			= trim($arrys_result_myreply[$i]['reply_id']);
					//-----------------------------------------------
					//SQL-讚
					//-----------------------------------------------
					$sql="
						SELECT
							`user_id`
						FROM
							`mssr_forum_article_reply_like_log`
						WHERE
							`reply_id` = $reply_id
					";

					$arrys_result_like=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_like=count($arrys_result_like);


					$arrys_result_replynum=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_replynum=count($arrys_result_replynum);?>
					<tr class="even">
						<td><?php if($numrow_arrys_teacher!==0){?>
                                <img id="mssr_comment_box_delete" src="image/delete.png" alt="" width="10" height="10"
                                onmouseover="mouse_over(this);void(0);"
                                onclick="delete_article('reply',<?php echo $user_id;?>,<?php echo $reply_id;?>);void(0);"/>
                			<?php }?>
                        聊書小組：<?php echo $arrys_result_forum_name[0]['forum_name']?></td>
						<td><?php echo $keyin_cdate?></td>
						<td>
                            <a onclick="action_log('inc/add_action_forum_log/code.php','p5',<?php echo $_SESSION["uid"];?>,<?php echo $user_id;?>,0,'','',<?php echo $book_sid;?>,0,<?php echo $article_id?>,0,'mssr_forum_group_reply.php?article_id=<?php echo $article_id?>');void(0);"
                            href="javascript:void(0);">回覆：<?php echo $article_content?></a>
                        </td>
						<td><?php echo $numrow_like?></td>
						<td><?php echo $numrow_replynum?></td>
					</tr>
				<?php }?>
			<?php }?>
  		<?php }?>
	</tbody>
</table>
</section>
<!--=======================================================================================================-->
<!--=============================================頁尾=======================================================-->
<!--=======================================================================================================-->
<footer class="footer_people">
	明日星球-聊書
</footer>
</body>
</html>
