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
//        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",2).'config/config.php');

        //外掛頁面檔
        require_once(str_repeat("../",0).'inc/require_page/code.php');
        require_once('filter_func.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code'
       	);
        func_load($funcs,true);

        //清除並停用BUFFER
//        @ob_end_clean();

	//---------------------------------------------------
    //SESSION
    //---------------------------------------------------


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
//					$sql="
//						SELECT*
//						FROM
//							(SELECT
//								`student`.`class_code`, `student`.`uid`
//							FROM
//								`student`
//
//							UNION
//
//							SELECT
//								`teacher`.`class_code`, `teacher`.`uid`
//							FROM
//								`teacher`)tmp
//						WHERE 1=1
//							AND	`uid` = $user_id
//						ORDER BY
//							`class_code` DESC
//					";
//					$arrys_result_userclasscode=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
//					$numrow_userclasscode=count($arrys_result_userclasscode);
				//-----------------------------------------------
	        	//SQL-usergrade(用class_code找學生年級資訊)
	        	//-----------------------------------------------
					if($sess_uid==$user_id){
                        $class_code = trim($_SESSION['class'][0][1]);
                    }else{
                        $date = date("Y-m-d H:i:s");
                        $sql="
                                SELECT
                                    `student`.`class_code`, `student`.`uid` ,`student`.`start`,`student`.`end`
                                FROM
                                    student

                            WHERE 1=1
                                AND	 uid = $user_id
                                and  DATE(start)       <= '$date'
                                and  DATE(end)         >= '$date'

                            ORDER BY
                                `class_code` DESC
					    ";
					   $arrys_result_userclasscode=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
                       $class_code = trim($arrys_result_userclasscode[0]['class_code']);
                            if(empty($arrys_result_userclasscode)){
                                 $sql="
                                    SELECT
                                        `teacher`.`class_code`, `teacher`.`uid` ,`teacher`.`start`,`teacher`.`end`
                                    FROM
                                        teacher
                                WHERE 1=1
                                    AND	 uid = $user_id
                                    and  DATE(start)       <= '$date'
                                    and  DATE(end)         >= '$date'

                                ORDER BY
                                    `class_code` DESC
                            ";
                               $arrys_result_userclasscode=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
                               $class_code = trim($arrys_result_userclasscode[0]['class_code']);
                            }

                    }




					$sql="
						SELECT
							`class`.`grade`, `class`.`classroom`, `class`.`class_code`, `semester`.`school_code`
						FROM
							`class` inner join `semester`
							on `class`.`semester_code` = `semester`.`semester_code`
						WHERE
							`class`.`class_code` = '$class_code'
					";


					$arrys_result_usergrade=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);

					$numrow_usergrade=count($arrys_result_usergrade);

				//-----------------------------------------------
	        	//SQL-arrys_result_user_school(用class_code找學生學校資訊)
	        	//-----------------------------------------------
					$user_school = $arrys_result_usergrade[0]["school_code"];

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
	        	//SQL-shelf(書櫃)
	        	//-----------------------------------------------
					$sql="
						SELECT
							`book_sid`,
                            `borrow_sdate`
						FROM
							`mssr_book_borrow_log`
						WHERE 1=1
							 and `user_id` = $user_id
                             and  borrow_sdate >='2014-08-01'
                             order by borrow_sdate desc
                        limit 5
					";
					$arrys_result_shelf=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_shelf=count($arrys_result_shelf);

                    //-----------------------------------------------
	        	//SQL-shelf(書櫃)
	        	//-----------------------------------------------
					$sql="
						SELECT
							`book_sid`,
                            `borrow_sdate`
						FROM
							`mssr_book_borrow_log`
						WHERE 1=1
							 and `user_id` = $user_id
                             and  borrow_sdate >='2014-08-01'
                             order by borrow_sdate desc
					";
					$arrys_shelf=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$numrow_shelfall=count($arrys_shelf);
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
	        	//SQL-shelf(我的朋友)(計算分頁)
	        	//-----------------------------------------------
					$query_sql="
						SELECT
							`user_id`,`friend_id`
						FROM
							`mssr_forum_friend`
						WHERE 1=1
							AND (
								`user_id` = $user_id
									OR
								`friend_id` = $user_id
							)
							AND `friend_state` = '成功'
                            limit 5
					";
					$arrys_result_friend=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
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
                                    `mssr_forum_article`.`article_title`,


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

                                    '0' AS article_title,

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
                                    `mssr_forum_article`.`article_title`,

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

                                    '0' AS article_title,

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
                        limit 5
					";


					$arrys_result_myreply=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);

					$arrys_result_myreply_con = count($arrys_result_myreply);


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
    //分頁處理
    //---------------------------------------------------

        $numrow=$arrys_result_myreply_con;    //資料總筆數
        $psize =10;                 //單頁筆數,預設10筆
        $pnos  =0;                  //分頁筆數
        $pinx  =1;                  //目前分頁索引,預設1
        $sinx  =0;                  //值域起始值
        $einx  =0;                  //值域終止值

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

        $pnos  =ceil($numrow/$psize);
        $pinx  =($pinx>$pnos)?$pnos:$pinx;

        $sinx  =(($pinx-1)*$psize)+1;
        $einx  =(($pinx)*$psize);
        $einx  =($einx>$numrow)?$numrow:$einx;
        //echo $numrow."<br/>";

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        if($numrow!==0){
            $arrys_chunk =array_chunk($arrys_result_myreply,$psize);
            $arrys_result=$arrys_chunk[$pinx-1];
        }else{

        }

        //加入好友顯示隱藏




        if($sess_uid == $user_id){
            $firShow = 'display:none';
        }else{
            $firShow = 'display:';
        }




                //---------------------------------------------------
                //好友列表
                //---------------------------------------------------

                $sql = "
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

                $check_friend = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);






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
?>






<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
<title><?php //echo $title?></title>
</head>
    <!-- 通用js  -->
    <script type="text/javascript" src="../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../inc/code.js"></script>
    <script type="text/javascript" src="../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../lib/js/table/code.js"></script>
    <script type="text/javascript" src="inc/add_action_forum_log/code.js"></script>

    <!-- 專屬js  -->
    <script type="text/javascript" src="js/bootstrap.min.js"></script>

    <link type="text/css" rel="stylesheet" href="css/bootstrap.css">
    <link type="text/css" rel="stylesheet" href="css/mssr_forum.css">
    <link type="text/css" rel="stylesheet" href="../../inc/code.css">

<script>

//	var psize=<?php echo (int)$psize;?>;
//    var pinx =<?php echo (int)$pinx;?>;
//
//    window.onload=function(){
//
//        //分頁列
//        var cid         ="page";                        //容器id
//        var numrow      =<?php echo (int)$numrow;?>;    //資料總筆數
//        var psize       =<?php echo (int)$psize ;?>;    //單頁筆數,預設10筆
//        var pnos        =<?php echo (int)$pnos  ;?>;    //分頁筆數
//        var pinx        =<?php echo (int)$pinx  ;?>;    //目前分頁索引,預設1
//        var sinx        =<?php echo (int)$sinx  ;?>;    //值域起始值
//        var einx        =<?php echo (int)$einx  ;?>;    //值域終止值
//        var list_size   =5;                             //分頁列顯示筆數,5
//        var url_args    ={};                            //連結資訊
//        url_args={
//            'pinx_name' :'pinx',
//            'psize_name':'psize',
//            'page_name' :'mssr_forum_people_myreply.php',
//            'page_args' :{
//                'user_id' :<?php echo (int)$user_id;?>
//            }
//        }
//        var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
//    }
//
//
//<script>
//
//	var psize=<?php echo (int)$psize;?>;
//    var pinx =<?php echo (int)$pinx;?>;
//
//    window.onload=function(){
//
//        //分頁列
//        var cid         ="page";                        //容器id
//        var numrow      =<?php echo (int)$numrow;?>;    //資料總筆數
//        var psize       =<?php echo (int)$psize ;?>;    //單頁筆數,預設10筆
//        var pnos        =<?php echo (int)$pnos  ;?>;    //分頁筆數
//        var pinx        =<?php echo (int)$pinx  ;?>;    //目前分頁索引,預設1
//        var sinx        =<?php echo (int)$sinx  ;?>;    //值域起始值
//        var einx        =<?php echo (int)$einx  ;?>;    //值域終止值
//        var list_size   =5;                             //分頁列顯示筆數,5
//        var url_args    ={};                            //連結資訊
//        url_args={
//            'pinx_name' :'pinx',
//            'psize_name':'psize',
//            'page_name' :'mssr_forum_people_myreply.php',
//            'page_args' :{
//                'user_id' :<?php echo (int)$user_id;?>
//            }
//        }
//        var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
//    }




    $(document).ready(function() {
        $('.forum_sessId').click(function(){
            $('.elseFriends').show();

                $('#close').click(function(){
                    $('.elseFriends').hide();
                    $('.remove').remove();
                })
        });
    });

    function elsefriends(forumId){
        var forum_name = $("[class='forum_span"+forumId+"']").val();
        var friends = $("[class='all_friends"+forumId+"']").val();
        var fail = $("[class='fail_friends"+forumId+"']").val();
        $('.remove').remove();
         $.ajax
           ({
                    type:"get", //提交类型
                    url:"mssr_forum_elsefirends.php", //提交页面
                    data:{ forum_span: forum_name , all_friends: friends , fail_friends: fail },
                    async: false,
                    contentType: "application/json; charset=utf-8",
                    dataType: 'json',
                    success:function(respones) {
                        var res  = eval(respones);

                        for(var i=0;i<res.length;i++){

                           $('.insetFriends').append("<figure class = 'remove' style='margin-top:35px;margin-left:30px'><img src='image/boy.jpg'  width='80px' height='80px' /><figcaption><div  class='checkbox disabled' style='width:110px;margin-left:-15px'><span style='margin-left:-10px'><a href='mssr_forum_people_index.php?user_id="+res[i].user_id+"'>"+res[i].name+"</a></span></div></figcaption></figure>");

                        }
                    },
                    error       :function(xhr, ajaxoptions, thrownerror){
                    //失敗處理
                        if(ajaxoptions==='timeout'){
                            alert('timeout');
                            return false;
                        }else{
                            alert('error');
                            return false;
                        }
                    },
                    complete    :function(){
                    //傳送後處理
                    }
            });

    }

        function bookelsefriends(forumId){
            var book_sid     = $("[class='book_sid"+forumId+"']").val();
            var all_friends  = $("[class='all_friends"+forumId+"']").val();
            var user_id      = $("[class='user_id"+forumId+"']").val();
            $('.remove').remove();
//            alert(book_sid);
//            alert(all_friends);
//            alert(user_id);



         $.ajax
           ({
                    type:"get", //提交类型
                    url:"mssr_forum_bookelsefirends.php", //提交页面
                    data:{ book_sid: book_sid , all_friends: all_friends , user_id: user_id },
                    async: false,
                    contentType: "application/json; charset=utf-8",
                    dataType: 'json',
                    success:function(respones) {
                        var res  = eval(respones);

                        for(var i=0;i<res.length;i++){
                                $('.insetFriends').append("<figure class = 'remove' style='margin-top:35px;margin-left:30px'><img src='image/boy.jpg'  width='80px' height='80px' /><figcaption><div  class='checkbox disabled' style='width:110px;margin-left:-15px'><span style='margin-left:-10px'><a href='mssr_forum_people_index.php?user_id="+res[i].user_id+"'>"+res[i].name+"</a></span></div></figcaption></figure>");
                        }
                    },
                    error       :function(xhr, ajaxoptions, thrownerror){
                    //失敗處理
                        if(ajaxoptions==='timeout'){
                            alert('timeout');
                            return false;
                        }else{
                            alert('error');
                            return false;
                        }
                    },
                    complete    :function(){
                    //傳送後處理
                    }
            });

    }

        function samebookelsefriends(forumId){
            var book_sid     = $("[class='book_sid"+forumId+"']").val();
            var all_friends  = $("[class='all_friends"+forumId+"']").val();
            var user_id      = $("[class='user_id"+forumId+"']").val();
            var asc         = $("[class='user"+forumId+"']").val();
            $('.remove').remove();
//            alert(book_sid);
//            alert(all_friends);
//            alert(user_id);
//            alert(asc);





         $.ajax
           ({
                    type:"get", //提交类型
                    url:"mssr_forum_samebookelsefirends.php", //提交页面
                    data:{ user: asc , book_sid: book_sid , all_friends: all_friends , user_id: user_id },
                    async: false,
                    contentType: "application/json; charset=utf-8",
                    dataType: 'json',
                    success:function(respones) {
                        var res  = eval(respones);

                        for(var i=0;i<res.length;i++){
                                $('.insetFriends').append("<figure class = 'remove' style='margin-top:35px;margin-left:30px'><img src='image/boy.jpg'  width='80px' height='80px' /><figcaption><div  class='checkbox disabled' style='width:110px;margin-left:-15px'><span style='margin-left:-10px'><a href='mssr_forum_people_index.php?user_id="+res[i].user_id+"'>"+res[i].name+"</a></span></div></figcaption></figure>");
                        }
                    },
                    error       :function(xhr, ajaxoptions, thrownerror){
                    //失敗處理
                        if(ajaxoptions==='timeout'){
                            alert('timeout');
                            return false;
                        }else{
                            alert('error');
                            return false;
                        }
                    },
                    complete    :function(){
                    //傳送後處理
                    }
            });

    }

</script>

<style>
    .elseFriends{
        padding:20px;
        height:auto;
        display:none;
        background-color:#FFFFFF;
        border: 1px solid red;
        width: 500px;
        position: fixed;
        left:50%;
        top:30%;
        margin-left:-250px;
    }
    a{
        cursor: pointer;
    }

</style>





<body>

<!-- navbar start -->
    <?php r_p_navbar((int)$_SESSION["uid"],$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);?>
<!-- navbar end -->

<!--========================root=====================================-->
  <div class="root" >

      <ul class="breadcrumb">
    目前位置：
		    <li>
            <a href="index.php">首頁</a> <span class="divider"></span>
          </li>
          <li>
            <a href="mssr_forum_people_index.php?user_id=<?php echo $user_id; ?>"><?php echo $arrys_result_userinfo[0]['name']?>個人頁面</a> <span class="divider"></span>
          </li>


        </ul>
  </div>

<!--========================group header=====================================-->
  <div class="group_header">
        <div class="group_image" >
          <?php
			$get_user_info=get_user_info($conn_user,$user_id,$array_filter=array('sex'),$arry_conn_user);
			if($get_user_info[0]['sex']==1){?>
            	<img src="image/boy.jpg" width="100px" height="100px" />
            <?php }else{?>
            	<img src="image/girl.jpg" width="100px" height="100px" />
            <?php }?>
        </div>

        <div class="group_info1">

			<?php echo $arrys_result_userinfo[0]['name']?><BR>
            <?php echo $arrys_result_user_school[0]['school_name']?><?php echo $arrys_result_usergrade[0]['grade']?>年<?php echo $arrys_result_usergrade[0]['classroom']?>班
			<BR><BR>

          <?php if(!empty($check_friend)){ ?>

                    <a style="float:left" class="btn" type="button">已是好友</a>
                <?php }else{ ?>

                    <a style="float:left;<?php echo $firShow ?>;" onclick="logFuc('inc/add_action_forum_log/code.php','p12',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'add/add_friendA.php?user_id=<?php echo $user_id; ?>&sess_uid=<?php echo $sess_uid; ?>')"  class="btn" type="button">加為好友</a>
                <?php } ?>


        </div>

        <div class="group_info2">

          <?php echo $arrys_result_userinfo[0]['name']?>的閱讀資訊:<BR>
			  發表了<?php echo $numrow_articlenum?>篇文章<BR>
			  已經讀了<?php echo $numrow_shelfall?>本書<BR>
			  回覆<?php echo $numrow_replynum?>篇文章<BR>

        </div>

 </div>


<!--========================tab_bar=====================================-->
    <div class="tab_bar">

		   <div class="tabbable" id="tabs-215204">
			<ul class="nav nav-tabs">
			<li class="active">
				<a onclick="logFuc('inc/add_action_forum_log/code.php','p14',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'mssr_forum_people_index.php?user_id=<?php echo $user_id?>')">首頁</a>
			  </li>
			  <li>
				<a onclick="logFuc('inc/add_action_forum_log/code.php','p8',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'mssr_forum_people_shelf.php?user_id=<?php echo $user_id?>')">書櫃</a>
			  </li>
			  <li>
				<a onclick="logFuc('inc/add_action_forum_log/code.php','p9',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'mssr_forum_people_myreply.php?user_id=<?php echo $user_id?>')">討論</a>
			  </li>
			  <li>
				<a onclick="logFuc('inc/add_action_forum_log/code.php','p10',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'mssr_forum_people_group.php?user_id=<?php echo $user_id?>')">聊書小組</a>
			  </li>
			  <li>
				<a onclick="logFuc('inc/add_action_forum_log/code.php','p11',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'mssr_forum_people_friend.php?user_id=<?php echo $user_id?>')">朋友</a>
			  </li>
			   <li>
                <a onclick="logFuc('inc/add_action_forum_log/code.php','p11',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'mssr_forum_people_favorite_book.php?user_id=<?php echo $user_id?>')">追蹤書籍</a>
              </li>
			  </ul>
		  </div>

	</div>


<!--========================content=====================================-->
<div class="content">

    <div class="left_content">




<!--========================最新討論回覆文章=====================================-->

		<H4 style="background-color:#CCC;"><B>最近討論發文、回文</B></h4>
		<?php //echo trim($arrys_result_2[0]['forum_content']);?>




      <table class="table table-hover table-striped">
         <thead>
                    <tr>
                        <th style="width:25%">討論名稱</th>
                        <th style="width:40%">發表的內容(發文/回覆)</th>
                        <th style="width:15%">發表時間</th>
                        <th style="width:3%">讚</th>
                        <!-- <th style="width:7%">回應</th> -->

                    </tr>
                </thead>
                <tbody>

				<?php
			if(empty($arrys_result_myreply)){
				//echo("這本書目前還沒有留言喔，趕快來留言吧！");
			}else{

			foreach($arrys_result as $inx=>$arrys_result_myreply):
				$book_sid 			= trim($arrys_result_myreply['book_sid']);
				$keyin_cdate 		= trim($arrys_result_myreply['keyin_cdate']);
				$article_content 	= trim($arrys_result_myreply['article_content']);
				$article_from 		= trim($arrys_result_myreply['article_from']);
				$article_id 		= trim($arrys_result_myreply['article_id']);


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
                        limit 5
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





					$arrys_result_replynum=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);
					$numrow_replynum=count($arrys_result_replynum);


                   $sql="
						SELECT
							article_title
						FROM
							mssr_forum_article
						WHERE
							`article_id` = '$article_id'
                        limit 5
					";


                   $arrys_result_title =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

			?>

                     <tr class="even">
                        <td><?php if($numrow_arrys_teacher!==0){?>
                                <img id="mssr_comment_box_delete" src="image/delete.png" alt="" width="10" height="10"
                                onmouseover="mouse_over(this);void(0);"
                                onclick="delete_article('article',<?php echo $user_id;?>,<?php echo $article_id;?>);void(0);"/>
                			<?php }?>
                            書籍：<a href='mssr_forum_book_reply.php?article_id=<?php echo $article_id?>'><?php echo $book_name?></a></td>
						<td>
                            <a onclick="logFuc('inc/add_action_forum_log/code.php','p16',<?php echo $sess_uid?>,<?php echo $user_id?>,0,'<?php echo $book_sid?>',0,0,0,<?php echo $article_id;?>,0,'mssr_forum_book_reply.php?article_id=<?php echo $article_id?>')">標題：<?php echo filter($arrys_result_title[0]['article_title'])?></a><br/>
                            <a onclick="logFuc('inc/add_action_forum_log/code.php','p16',<?php echo $sess_uid?>,<?php echo $user_id?>,0,'<?php echo $book_sid?>',0,0,0,<?php echo $article_id;?>,0,'mssr_forum_book_reply.php?article_id=<?php echo $article_id?>')">發文：<?php echo filter($article_content);?></a>

                        </td>
						<td><?php echo $keyin_cdate?></td>

						<td><?php echo $numrow_like?></td>
						<!-- <td><?php echo $numrow_replynum?></td> -->
                    </tr>

					<?php
				//-----------------------------------------------
				//書籍回復
				//-----------------------------------------------
				}else if($article_from =="book_article_reply"){



					$arrys_book_info=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
					$book_name 			= trim($arrys_book_info[0]['book_name']);
					$reply_id 			= trim($arrys_result_myreply['reply_id']);


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
					$arrys_result_replynum=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);
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
					$arrys_result_like=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);
					$numrow_like=count($arrys_result_like);



//-----------------------------------------------
//標題
//-----------------------------------------------

                    $sql="
						SELECT
							article_title
						FROM
							mssr_forum_article
						WHERE
							`article_id` = '$article_id'
                        limit 5
					";
					$arrys_result_title =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

					?>
					<tr class="even">
						<td><?php if($numrow_arrys_teacher!==0){?>
                                <img id="mssr_comment_box_delete" src="image/delete.png" alt="" width="10" height="10"
                                onmouseover="mouse_over(this);void(0);"
                                onclick="delete_article('reply',<?php echo $user_id;?>,<?php echo $reply_id;?>);void(0);"/>
                			<?php }?>

                        書籍：<a href='mssr_forum_book_reply.php?article_id=<?php echo $article_id?>'><?php echo $book_name?><a></td>
						<td>
                             <a onclick="logFuc('inc/add_action_forum_log/code.php','p17',<?php echo $sess_uid?>,<?php echo $user_id?>,0,'<?php echo $book_sid?>',0,0,0,<?php echo $article_id;?>,<?php echo $reply_id;?>,'mssr_forum_book_reply.php?article_id=<?php echo $article_id?>')">標題：<?php echo filter($arrys_result_title[0]['article_title']);?></a><br/>
                             <a onclick="logFuc('inc/add_action_forum_log/code.php','p17',<?php echo $sess_uid?>,<?php echo $user_id?>,0,'<?php echo $book_sid?>',0,0,0,<?php echo $article_id;?>,<?php echo $reply_id;?>,'mssr_forum_book_reply.php?article_id=<?php echo $article_id?>')">#RE：<?php  echo filter($article_content);?></a>




                            <!-- <a href="mssr_forum_book_reply.php?article_id=<?php echo $article_id?>">標題：<?php echo $arrys_result_title[0]['article_title']?></a><br/>
                            <a href="mssr_forum_book_reply.php?article_id=<?php echo $article_id?>">回覆：<?php echo $article_content?></a> -->
                        </td>
						<td><?php echo $keyin_cdate?></td>

						<td><?php echo $numrow_like?></td>
						<!-- <td><?php echo $numrow_replynum?></td> -->
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
					$arrys_result_forum_name=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);

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
					$arrys_result_like=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);
					$numrow_like=count($arrys_result_like);

                    //-----------------------------------------------
					//標題
					//-----------------------------------------------

                    $sql="
						SELECT
							article_title
						FROM
							mssr_forum_article
						WHERE
							`article_id` = '$article_id'
                        limit 5
					";
					$arrys_result_title =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
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
					$arrys_result_replynum=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);
					$numrow_replynum=count($arrys_result_replynum);?>



					<tr class="even">
						<td><?php if($numrow_arrys_teacher!==0){?>
                                <img id="mssr_comment_box_delete" src="image/delete.png" alt="" width="10" height="10"
                                onmouseover="mouse_over(this);void(0);"
                                onclick="delete_article('article',<?php echo $user_id;?>,<?php echo $article_id;?>);void(0);"/>
                			<?php }?>
                        聊書小組：<a href='mssr_forum_group_reply.php?article_id=<?php echo $article_id?>'><?php echo $arrys_result_forum_name[0]['forum_name']?></a></td>
						<td>
                            <a onclick="logFuc('inc/add_action_forum_log/code.php','p18',<?php echo $sess_uid?>,<?php echo $user_id?>,0,0,0,<?php echo $book_sid; ?>,0,<?php echo $article_id;?>,0,'mssr_forum_group_reply.php?article_id=<?php echo $article_id?>')">標題：<?php echo filter($arrys_result_title[0]['article_title']);?></a><br/>
                            <a onclick="logFuc('inc/add_action_forum_log/code.php','p18',<?php echo $sess_uid?>,<?php echo $user_id?>,0,0,0,<?php echo $book_sid; ?>,0,<?php echo $article_id;?>,0,'mssr_forum_group_reply.php?article_id=<?php echo $article_id?>')">發文：<?php echo filter($article_content);?></a>

                        </td>
						<td><?php echo $keyin_cdate?></td>

						<td><?php echo $numrow_like?></td>
						<!-- <td><?php echo $numrow_replynum?></td> -->
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
					$arrys_result_forum_name=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);

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
					$reply_id 			= trim($arrys_result_myreply['reply_id']);

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

					$arrys_result_like=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);
					$numrow_like=count($arrys_result_like);


					$arrys_result_replynum=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);
					$numrow_replynum=count($arrys_result_replynum);

                    //-----------------------------------------------
					//標題
					//-----------------------------------------------

                    $sql="
						SELECT
							article_title
						FROM
							mssr_forum_article
						WHERE
							`article_id` = '$article_id'
                        limit 5
					";
					$arrys_result_title =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

                    ?>





					<tr class="even">
						<td><?php if($numrow_arrys_teacher!==0){?>
                                <img id="mssr_comment_box_delete" src="image/delete.png" alt="" width="10" height="10"
                                onmouseover="mouse_over(this);void(0);"
                                onclick="delete_article('reply',<?php echo $user_id;?>,<?php echo $reply_id;?>);void(0);"/>
                			<?php }?>
                        聊書小組：<a href='mssr_forum_group_reply.php?article_id=<?php echo $article_id?>'><?php echo $arrys_result_forum_name[0]['forum_name']?></a></td>
						<td>
                            <a onclick="logFuc('inc/add_action_forum_log/code.php','p19',<?php echo $sess_uid?>,<?php echo $user_id?>,0,0,0,<?php echo $book_sid; ?>,0,<?php echo $article_id;?>,<?php echo $reply_id;?>,'mssr_forum_group_reply.php?article_id=<?php echo $article_id?>')">標題：<?php echo filter($arrys_result_title[0]['article_title']);?></a><br/>
                            <a onclick="logFuc('inc/add_action_forum_log/code.php','p19',<?php echo $sess_uid?>,<?php echo $user_id?>,0,0,0,<?php echo $book_sid; ?>,0,<?php echo $article_id;?>,<?php echo $reply_id;?>,'mssr_forum_group_reply.php?article_id=<?php echo $article_id?>')">#RE：<?php  echo filter($article_content);?></a>

                        </td>
						<td><?php echo $keyin_cdate?></td>

						<td><?php echo $numrow_like?></td>
						<!-- <td><?php echo $numrow_replynum?></td> -->
					</tr>
			<?php
					}
				endforeach ;
				}

			?>

          </tbody>
      </table>


<div class="group_index">

	<H4 style="background-color:#CCC;"><B>最近閱讀書籍</B></h4>






  <table class="table table-hover table-striped">

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


						<figure class="figure_book">
							<a onclick="logFuc('inc/add_action_forum_log/code.php','20',<?php echo $sess_uid?>,<?php echo $user_id?>,0,'<?php echo $book_sid?>','0',0,0,0,0,'mssr_forum_book_discussion.php?book_sid=<?php echo $book_sid?>')"><img src="<?php echo $rs_bookpic_root ?>" alt="<?php echo filter($book_name)?>" width="100px" height="100px" /></a>

							<figcaption class="figcaption_book"><a onclick="logFuc('inc/add_action_forum_log/code.php','p20',<?php echo $sess_uid?>,<?php echo $user_id?>,0,'<?php echo $book_sid?>','0',0,0,0,0,'mssr_forum_book_discussion.php?book_sid=<?php echo $book_sid?>')"><?php echo filter($book_name)?></a></figcaption>
						</figure>
					<?php }?>
				<?php }?>

      </table>


</div>




<div class="group_index">
    <H4 style="background-color:#CCC;"><B>最近新增好友</B></h4>
	<?php
		if(empty($arrys_result_friend)){
			echo("現在還沒有朋友喔，趕快去找朋友吧！");
		}else{
		foreach($arrys_result_friend as $inx=>$arrys_result_friend):


				$userid 			= (int)$arrys_result_friend['user_id'];
				$friend_id 			= (int)$arrys_result_friend['friend_id'];
				if($userid==$_GET["user_id"]){
					$rs_user_id     	= $userid;
					$rs_friend_id		= $friend_id;

				}else{
					$rs_user_id     	= $friend_id;
					$rs_friend_id		= $userid;
				}
				$get_user_info=get_user_info($conn_user,$rs_friend_id,$array_filter=array('name','sex'),$arry_conn_user);


				$friend_name 			= trim($get_user_info[0]['name']);
				$friend_sex 			= trim($get_user_info[0]['sex']);?>



				<figure class = 'figure_book'>
                <?php
                	//處理
					if($friend_sex==1){?>
                        <a href="mssr_forum_people_index.php?user_id=<?php echo $rs_friend_id?>"><img src="image/boy.jpg" alt="<?php echo $friend_name?>" width="100px" height="100px" /></a>
					<?php }else{?>
						<a href="mssr_forum_people_index.php?user_id=<?php echo $rs_friend_id?>"><img src="image/girl.jpg" alt="<?php echo $friend_name?>" width="100px" height="100px" /></a>
					<?php }?>

					<figcaption><a onclick="logFuc('inc/add_action_forum_log/code.php','p21',<?php echo $sess_uid?>,<?php echo $user_id;?>,<?php echo $rs_friend_id?>,0,0,0,0,0,0,'mssr_forum_people_index.php?user_id=<?php echo $rs_friend_id?>')"><?php echo $friend_name?></a></figcaption>
				</figure>
   			<?php
				endforeach ;
				}
			?>
</div>


<!--=================================add book (block)==================================-->
<div id="open_request_book_form" style="display:none">
	<form action="mssr_forum_request_A.php" method="post">

		<input type="image" id="input_leave" src="image/xlogo.png" alt="" width="30" height="30" onclick="leaveui();"/>

		<h3>請求好友推薦書籍給自己</h3>
    	<p><input  type="text"  name="request_book_friend_name" size="50" maxlength="40" placeholder="要邀請誰推薦書籍"/></p>


        <div style="position:relative; width:100%;" >
     		   <figure>
					<img src="image/boy.jpg"  width="80px" height="80px" />
					<figcaption><input name="" type="checkbox" value="">xxxx</figcaption>
				</figure>
                  <figure>
					<img src="image/boy.jpg"  width="80px" height="80px" />
					<figcaption><input name="" type="checkbox" value="">xxxx</figcaption>
				</figure>
                <figure>
					<img src="image/boy.jpg"  width="80px" height="80px" />
					<figcaption><input name="" type="checkbox" value="">xxxx</figcaption>
				</figure>
                 <figure>
					<img src="image/boy.jpg"  width="80px" height="80px" />
					<figcaption><input name="" type="checkbox" value="">xxxx</figcaption>
				</figure>
                  <figure>
					<img src="image/boy.jpg"  width="80px" height="80px" />
					<figcaption><input name="" type="checkbox" value="">xxxx</figcaption>

                   <BR>

                   <input class="btn btn-success" id="decide" type ="submit" value="確定"></input>

         </div>

</div>

<!--=================================彈出其他好友==================================-->
<div class="elseFriends">
    <input type="button" id="close" class = "close" value = "關閉">
    <div class ="insetFriends"></div>
</div>

<!--========================排行=====================================-->
    </div>


<?php require_once('mssr_forum_right_people.php');  ?>






</body>

</html>
<script>

function logFuc(process_url,action_code,action_from,user_id_1,user_id_2,book_sid_1,book_sid_2,forum_id_1,forum_id_2,article_id,reply_id,go_url){

            var process_url     = process_url;
            var action_code     = action_code;
            var action_from     = action_from;

            var user_id_1       = user_id_1;
            var user_id_2       = user_id_2;
            var book_sid_1      = book_sid_1;
            var book_sid_2      = book_sid_2;
            var forum_id_1      = forum_id_1;
            var forum_id_2      = forum_id_2;

            var article_id      = article_id;
            var reply_id        = reply_id;
            var go_url          = go_url;



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

</script>