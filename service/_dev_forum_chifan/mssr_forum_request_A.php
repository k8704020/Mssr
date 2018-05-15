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
//接參數

        $uid= $_SESSION['uid'];
        if(isset($_GET['question'])){
            $question = $_GET['question'];
        }else{
            $question = '';
        }


        $firnd_arr = array();
        if(isset($_GET['friend'])){
             $firnd_arr = $_GET['friend'];
        }


//        if(empty($firnd_arr)){
//            $msg= "至少選一個朋友";
//                    $jscript_back="
//                        <script>
//                            alert('{$msg}');
//                            history.back(-1);
//                        </script>
//                    ";
//             die($jscript_back);
//        }





//找朋友

foreach($firnd_arr as $k => $v){

        $sess_uid = $v;




        $sql = "
                    select
                        mssr_user_request.request_id
                    from
                        mssr_user_request
                        inner join mssr_user_request_book_rev  on mssr_user_request.request_id = mssr_user_request_book_rev.request_id
                    where  1=1
                        and mssr_user_request.request_to    = $sess_uid
                        and mssr_user_request.request_from  = $uid
                        and mssr_user_request.request_state = 1

               ";
        $firend_ck = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
        //$fName = get_user_info($conn='',$sess_uid,$array_filter=array(),$arry_conn);



        if(!empty($firend_ck)){


             $msg= "等待對方同意中!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
             die($jscript_back);



        }



}



//新增請求


                foreach($firnd_arr as $v){


                     $sql = "
                            insert into
                                                mssr_user_request
                                            (
                                                request_id,
                                                request_from,
                                                request_to,
                                                request_question,

                                                request_state,
                                                keyin_cdate

                                            )
                                         VALUES (null,$uid,$v,'$question',1,now())

                            ";


                            db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


                            $sql = "select request_id from mssr_user_request order by request_id desc";
                            $request_insert =  db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

                            $request_id = $request_insert[0]['request_id'];


                            $sql = "
                            insert into
                                                mssr_user_request_book_rev
                                            (
                                                `request_id`
                                            )
                                         VALUES ($request_id);



                            ";


                            db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);



                   }


                    $msg= "申請成功";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    echo $jscript_back;


?>