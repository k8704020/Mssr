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

        if(!empty($_GET['arrFriend'])){
                $arrFriend = $_GET['arrFriend'];
        }else{
                $arrFriend = '';
        }
            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
			//-----------------------------------------------

if(!empty($arrFriend)){
        $arrFri = implode(",",$arrFriend);
}else{
         $arrFri = '';
}



            $query_sql ="
						SELECT  `user_id` ,  `friend_id` , name, keyin_cdate
                        FROM  `mssr_forum_friend`
                        JOIN user.member ON mssr_forum_friend.friend_id = user.member.uid
                        WHERE 1 =1
                        AND user.member.uid in('$arrFri')
                        group by name
                        order by friend_id asc
					";


	       $arrys_result_member=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);



           foreach($arrys_result_member as $k =>$v){
                  $arrys_result_member[$k]['check'] = 1;
           }

           $re=array();
           foreach($arrys_result_member as $key=>$value){
            $re[$key] = $value['friend_id'];
           }


           $query_sql ="
						SELECT
                            IFNULL((
                            SELECT
                                 `user`.`member`.`name`
                            FROM `user`.`member`
                            WHERE `mssr`.`mssr_forum_friend`.`user_id`=`user`.`member`.`uid`
                            LIMIT 1
                        ),'') AS `user_name`,
							`user_id`,
                            `friend_id`,
                             name,
                             keyin_cdate
						FROM
							`mssr_forum_friend`
                             join user.member on mssr_forum_friend.friend_id =  user.member.uid
						WHERE 1=1
							AND
                                (`user_id`  = $uid
									or
								`friend_id` = $uid)

							AND `friend_state` = '成功'
                            AND  user.member.name like '%$request_book_friend_name%'

                            order by friend_id asc
					";


	      $arrys_result_friend=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);

foreach($arrys_result_friend as $k =>$v){

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
				$arrys_result_friend[$k]['name'] = trim($get_user_info[0]['name']);



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
                 $arrys_result_friend[$k]['ck'] = 1;
            }else{
                 $ck =  "";
            }


}







if($request_book_friend_name != ''){
    for($i=0;$i<count($arrys_result_friend);$i++){
        if(@in_array($arrys_result_friend[$i]['friend_id'],$arrFriend)){
            unset($arrys_result_friend[$i]);
        }
    }
    $arr_res = array_merge($arrys_result_member,$arrys_result_friend);
          echo json_encode($arr_res);
}else{
        foreach($arrys_result_friend as $k =>$v){
               if(in_array($arrys_result_friend[$k]['friend_id'],$re)){
                       $arrys_result_friend[$k]['check'] = 1;
               }
        }
          echo json_encode($arrys_result_friend);
}



?>