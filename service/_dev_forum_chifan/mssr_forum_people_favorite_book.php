<?php
//-------------------------------------------------------
//mssr_forum
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------
//        //SESSION
          @session_start();
//
//        //啟用BUFFER
//        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",0).'inc/require_page/code.php');

        //外掛設定檔
        require_once(str_repeat("../",2).'config/config.php');
        require_once('filter_func.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code'
       	);
        func_load($funcs,true);

//        //清除並停用BUFFER
//        @ob_end_clean();

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------


        $sess_uid=(int)$_SESSION["uid"];//登入者的uid

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
					$arrys_result_userinfo = db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
					$numrow_userinfo = count($arrys_result_userinfo);



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
//
//
//					$numrow_userclasscode=count($arrys_result_userclasscode);


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
	        	//SQL-shelf(書櫃)(計算分頁)
	        	//-----------------------------------------------





					$query_sql="
						SELECT
							*
						FROM
							`mssr_book_favorite` 
						WHERE 1=1
							 and `user_id` = $user_id                     
                             order by keyin_cdate desc
					";
					$arrys_result_shelf=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
					$arrys_result_shelf_con=count($arrys_result_shelf);
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
					$arrys_result_friend_check = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

                //-----------------------------------------------
	        	//SQL-shelf(我的朋友)(計算分頁)
	        	//-----------------------------------------------
					$query_sql ="
						SELECT
							`user_id`,`friend_id`,name,keyin_cdate
						FROM
							`mssr_forum_friend`
                            join user.member on mssr_forum_friend.friend_id =  user.member.uid
						WHERE 1=1
							AND
                                (`user_id`  = $user_id
									or
								`friend_id` = $user_id)

							AND `friend_state` = '成功'

                            order by friend_id asc


					";
					$arrys_result_friend = db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);










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



	//---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球-我的書櫃";

	//---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=$arrys_result_shelf_con;    //資料總筆數
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
            $arrys_chunk =array_chunk($arrys_result_shelf,$psize);
            $arrys_result=$arrys_chunk[$pinx-1];
        }else{

        }


        //加入好友顯示隱藏




        if($sess_uid == $user_id){
            $firShow = 'display:none';
        }else{
            $firShow = 'display:';
        }

        if($sess_uid == $user_id  or !empty($check_friend)){
          $requestShow = 'display:';
        }else{
          $requestShow = 'display:none';
        }
?>

<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
<title><?php echo $title?></title>
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
  <style>

  #open_request_book_form{

           border: 1px solid #000;
           background-color:#FFF;
           position: fixed;
           z-index:100;

        }
   </style>


<!--<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" />
<script type="text/javascript" src="jquery.autocomplete.js"></script>
<script type="text/javascript" src="jquery.autocomplete.pack.js"></script>-->

<script>

	var psize=<?php echo (int)$psize;?>;
    var pinx =<?php echo (int)$pinx;?>;

    window.onload=function(){

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
            'page_name' :'mssr_forum_people_favorite_book.php',
            'page_args' :{
                'user_id' :<?php echo (int)$user_id;?>
            }
        }
        var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
    }

$(document).ready(Block);

function Block(){



	$('#request_book_button').click(function() {
			//alert("OK!!");
            logFuc('inc/add_action_forum_log/code.php','p13',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'');

            var all_Inputs = $("input[id=request_book_friend_name]");
            all_Inputs.val("");
            $("input[type='checkbox']").attr("checked", false);


            $('#open_request_book_form').show();
            $('#open_request_book_form').css({
					top:  ($(window).height() - 400) /2 + 'px',
					left: ($(window).width() - 700) /2 + 'px',
					textAlign:	'left',
					width: '700px'
				});


                $('#input_leave').click(function() {
                     $('#open_request_book_form').hide();
                     return false
		        });

		});


	}




//aajx

    function search(){
          var arrFriend = new Array();
          $("input[name='friend[]']:checked").each(function(i) { arrFriend[i] = this.value;});



          $('.re').remove();
          var m = $("[id='request_book_friend_name']").val();

          $.ajax({
            type:"get", //提交类型
            url:"mssr_forum_people_shelf_test_ajax.php", //提交页面
            data:{ request_book_friend_name: m , arrFriend: arrFriend},
            async: false,
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            success:function(respones) {


                var res  = eval(respones);
                for(var i=0;i<res.length;i++){



                       if(res[i].check == 1){
                          $('#tablelist').append("<div class='re'><figure style='margin-top:35px;margin-left:30px'><img src='image/boy.jpg'  width='80px' height='80px' /><figcaption><div class='checkbox disabled inner' style='width:110px;margin-left:-15px'><input checked style='margin-left:0px;padding:0px;' name='friend[]' type='checkbox' value='"+res[i].friend_id+"'>"+res[i].name+"</div></figcaption></figure></div>");
                       }else if(res[i].ck == 1){
                          $('#tablelist').append("<div class='re'><figure style='margin-top:35px;margin-left:30px'><img src='image/boy.jpg'  width='80px' height='80px' /><figcaption><div class='checkbox disabled inner' style='width:110px;margin-left:-15px'><input style='margin-left:0px;padding:0px;' name='friend[]' disabled type='checkbox' value='"+res[i].friend_id+"'>"+res[i].name+"</div></figcaption></figure></div>");
                       }else{
                          $('#tablelist').append("<div class='re'><figure style='margin-top:35px;margin-left:30px'><img src='image/boy.jpg'  width='80px' height='80px' /><figcaption><div class='checkbox disabled inner' style='width:110px;margin-left:-15px'><input style='margin-left:0px;padding:0px;' name='friend[]' type='checkbox' value='"+res[i].friend_id+"'>"+res[i].name+"</div></figcaption></figure></div>");
                       }



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

    function rest(){


          var v = $("[id='input_leave']").val();
          $.ajax({
            type:"get", //提交类型
            url:"mssr_forum_people_shelf_ajax.php", //提交页面
            data:{ request_book_friend_name: v},
            async: false,
            contentType: "application/json; charset=utf-8",
            dataType: 'html',
            success:function(msg) {
                $("#tablelist").html(msg);

            }
        });


   }

    function fastRest(){
         logFuc('inc/add_action_forum_log/code.php','p13',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'mssr_forum_request_A.php?friend[]=<?php echo $user_id ?>');
         //location.href="mssr_forum_request_A.php?friend[]=<?php echo $user_id ?>"
    }

    function loade(){
        location.reload();
    }


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


     function checkNike(){

       var arrFriend = new Array();
          $("input[name='friend[]']:checked").each(function(i) { arrFriend[i] = this.value;});
          if(arrFriend.length == 0){
            alert('你沒有勾選任何朋友');
            return false;
          }

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

.checkbox{
     float:left;
     width:100px;
     height:150px;

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

          <li class="active">
            書櫃
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



        <?php if($sess_uid == $user_id){ ?>
		    <!-- <a style="float:left;<?php echo $requestShow?>" class="btn" id="request_book_button">請求推薦書籍</a> -->
        <?php }else{ ?>
            <!-- <a style="float:left;<?php echo $requestShow?>" class="btn" onclick = "fastRest()">請求推薦書籍</a> -->
        <?php } ?>

        </div>

        <div class="group_info2">

          <?php echo $arrys_result_userinfo[0]['name']?>的閱讀資訊:<BR>
			  發表了<?php echo $numrow_articlenum?>篇文章<BR>
			  已經讀了<?php echo $arrys_result_shelf_con?>本書<BR>
			  回覆<?php echo $numrow_replynum?>篇文章<BR>

        </div>

 </div>


<!--========================tab_bar=====================================-->
    <div class="tab_bar">

		  <div class="tabbable" id="tabs-215204">
			<ul class="nav nav-tabs">
			<li>
				<a onclick="logFuc('inc/add_action_forum_log/code.php','p14',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'mssr_forum_people_index.php?user_id=<?php echo $user_id?>')">首頁</a>
			  </li>
			  <li >
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
              <li class="active">
                <a onclick="logFuc('inc/add_action_forum_log/code.php','p11',<?php echo $sess_uid?>,<?php echo $user_id;?>,0,0,0,0,0,0,0,'mssr_forum_people_friend.php?user_id=<?php echo $user_id?>')">追蹤書籍</a>
              </li>
			  </ul>
		  </div>

	</div>



<!--========================content=====================================-->
<div class="content">

    <div class="left_content">






	      <table class="table table-hover table-striped">
       <thead>
                    <tr>
                        <th style="width:10%"></th>
                        <th style="width:40%">書籍名稱</th>
                        <!-- <th style="width:15%">書籍分類</th> -->
                        <th style="width:15%">追蹤日期</th>

                    </tr>
                </thead>
                <tbody>
	<?php
		if(empty($arrys_result_shelf)){
			echo("現在書櫃沒有追蹤任何書籍");
		}else{
			foreach($arrys_result as $inx=>$arrys_result_shelf):
				$book_sid 			= trim($arrys_result_shelf['book_sid']);
				$borrow_sdate		= trim($arrys_result_shelf['keyin_cdate']);

				$arrys_book_info	=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
				$book_name 			= trim($arrys_book_info[0]['book_name']);


				//book_name		書名
				if(mb_strlen($book_name)>15){
					$book_name=mb_substr($book_name,0,15)."..";
				}

				//書籍封面處理
				$bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
				if(file_exists($bookpic_root)){
					$rs_bookpic_root = '../../info/book/'.$book_sid.'/img/front/simg/1.jpg';
				}else{
					$rs_bookpic_root = 'image/book.jpg';

				}



	?>
                     <tr class="even">
                        <td align="center"><a onclick="logFuc('inc/add_action_forum_log/code.php','p1',<?php echo $sess_uid?>,<?php echo $user_id?>,0,'<?php echo $book_sid?>','0',0,0,0,0,'mssr_forum_book_discussion.php?book_sid=<?php echo $book_sid?>')"><img src="<?php echo $rs_bookpic_root ?>" alt="<?php echo $book_name?>"  width="50" height="50"/></a></td>
                        <td><a onclick="logFuc('inc/add_action_forum_log/code.php','p1',<?php echo $sess_uid?>,<?php echo $user_id?>,0,'<?php echo $book_sid?>','0',0,0,0,0,'mssr_forum_book_discussion.php?book_sid=<?php echo $book_sid?>')"><?php echo filter($book_name)?></a></td>
                        <!-- <td>分類</td> -->
                        <td><?php echo $borrow_sdate?></td>

                    </tr>
          </tbody>
		  <?php
			 endforeach ;
		  }
		  ?>

      </table>


<!--========================分頁=====================================-->
		  <table style="float:right;">
			<tr valign="middle">
				<td align="left">
					<!-- 分頁列 -->
					<span id="page" style="position:relative;"></span>
				</td>
			</tr>
		  </table>

    </div>
<style>
#open_request_book_form {

    width: 700px;
    height: 300px;
    overflow: scroll;
    overflow-x:hidden;
}
</style>
<!--=================================add book (block)==================================-->
<div id="open_request_book_form" style="display:none"  >
	<form action="mssr_forum_request_A.php" method="GET" onsubmit ='return checkNike()';>

		<input type="image" id="input_leave" name="input_leave" src="image/xlogo.png" alt="" width="30" height="30" onclick ="loade()"/>

		<div style="width:700px;height:50px;"><div style="width:300px;float:left;font-size:25px;">請求好友推薦書籍給自己</div>
        <!-- <div style=" float:right;width:300px;" ><input id ="request_book_friend_name"  type="text"  onkeyup="search"  size="20" maxlength="40" placeholder="輸入你想搜尋的好友" /><input style="margin-top:-5px;margin-left:5px;" class="btn btn-default btn-xs" type="button" onclick="search()" value = "搜尋"></div> --></div>
        <!-- <div style="font-size:18px;">輸入你想對他說的話:<input style="height:25px" type="text" name="question"/></div> -->



        <div id ="tablelist" style="position:relative; width:500px; margin-left:auto;margin:0 auto;" >

<?php

foreach($arrys_result_friend as $v){

   				$userid 			= (int)$v['user_id'];
				$friend_id 			= (int)$v['friend_id'];


				if($userid ==$_GET["user_id"]){
					$rs_user_id     	= $userid;
					$rs_friend_id		= $friend_id;

				}else{
					$rs_user_id     	= $friend_id;
					$rs_friend_id		= $userid;
				}

                $get_user_info=get_user_info($conn_user,$rs_friend_id,$array_filter=array('name','sex'),$arry_conn_user);
				$friend_name 			= trim($get_user_info[0]['name']);



         $sql =   "
                    select
                        mssr_user_request.request_id
                    from
                        mssr_user_request
                    inner join mssr_user_request_book_rev  on mssr_user_request.request_id = mssr_user_request_book_rev.request_id
                    where  1=1
                        and mssr_user_request.request_to    = $rs_friend_id
                        and mssr_user_request.request_from  = $rs_user_id
                        and mssr_user_request.request_state = 1
                   ";
           $request_check  = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



            if(!empty($request_check)){
                 $ck =  "disabled" ;
                 $chat= "(已邀請)";
            }else{
                 $ck =  "";
                 $chat= "";
            }
?>


                     <div style='width:500px;height:auto;' >
                        <div  class="checkbox disabled" >
					    <img src="image/boy.jpg"  width="80px" height="80px" />
                                <input style="margin-left:0px;padding:0px;" name="friend[]" type="checkbox" value="<?php echo $rs_friend_id;?>"<?php echo $ck ?>><span><?php echo $friend_name;?></span>
                                 <span><br/><?php echo $chat;?></span>
                        </div>
                    </div>


 <?php } ?>

       </div>

               <figure class="col-md-12 col-md-offset-5">

                   <input class="btn btn-success" id="decide" type ="submit" value="確定"></input>

               </figure>



</div>
<!--=================================彈出其他好友==================================-->
<div class="elseFriends">
    <input type="button" id="close" class = "close" value = "關閉">
    <div class ="insetFriends"></div>
</div>














<!--========================排行=====================================-->
<?php require_once('mssr_forum_right_people.php');  ?>



</body>

</html>

<script>

function logFuc(process_url,action_code,action_from,user_id_1,user_id_2,book_sid_1,book_sid_2,forum_id_1,forum_id_2,article_id,reply_id,go_url){

            var process_url     = process_url;
            var action_code     = action_code;
            var action_from     = action_from;

            var user_id_1       = user_id_1
            var user_id_2       = user_id_2
            var book_sid_1      = book_sid_1
            var book_sid_2      = book_sid_2
            var forum_id_1      = forum_id_1
            var forum_id_2      = forum_id_2

            var article_id      = article_id
            var reply_id        = reply_id
            var go_url          = go_url



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