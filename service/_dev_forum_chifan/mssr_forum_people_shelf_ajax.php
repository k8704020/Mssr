<?php



//-------------------------------------------------------
//mssr_fourm
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


        //接參數
        $request_book_friend_name = $_GET['request_book_friend_name'];
        $uid = $_SESSION['uid'];


            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
			//-----------------------------------------------



           $query_sql ="
						 SELECT
							`user_id`, `friend_id`,name,keyin_cdate
						FROM
							`mssr_forum_friend`
                            join user.member on mssr_forum_friend.friend_id =  user.member.uid
						WHERE 1=1
							AND (
								`user_id` = $uid
									OR
								`friend_id` = $uid
							)
							AND `friend_state` = '成功'
                            AND  user.member.name like '%$request_book_friend_name%'


					";
	      $arrys_result_friend=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);





foreach($arrys_result_friend as $v){

				$userid 			= (int)$v['user_id'];
				$friend_id 			= (int)$v['friend_id'];

                if($userid==$uid){
					$rs_user_id     	= $userid;
					$rs_friend_id		= $friend_id;

				}else{
					$rs_user_id     	= $friend_id;
					$rs_friend_id		= $userid;
				}


                $get_user_info=get_user_info($conn_user,$rs_friend_id,$array_filter=array('name','sex'),$arry_conn_user);
                $friend_name 			= trim($get_user_info[0]['name']);




         $fid = $v['friend_id'];

         $sql =   "
                    select *

                    from
                        mssr_user_request

                    where  1=1
                        and request_to    = $fid
                        and request_from  = $uid
                        and request_state = 1

                   ";
           $request_check  = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



            if(!empty($request_check)){
                 $ck =  "disabled" ;
            }else{
                 $ck =  "";
            }?>



<!DOCTYPE HTML>
<Html>
<Head>
    <Title></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="Content-Language" content="UTF-8">
    <script	type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src=""></script>
    <link rel="stylesheet" href=""/>
    <script>
        var a  = $("#name").attr("checked")
    </script>
</head>

<body>




               <!-- <figure style="margin-top:35px;margin-left:30px">
					<img src="image/boy.jpg"  width="80px" height="80px" />
					<figcaption>
                        <div  class="checkbox disabled" style="width:110px;margin-left:-15px">

                                <input style="margin-left:0px;padding:0px;" name="friend[]" type="checkbox" value="<?php echo $rs_friend_id;?>"<?php echo $ck ?> ><?php echo $friend_name; ?>

                        </div>
                    </figcaption>
				</figure>
 -->






</body>
</Html>


<?php
}
?>











