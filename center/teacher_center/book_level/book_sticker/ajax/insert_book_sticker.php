<?php
//-------------------------------------------------------
//教師中心
//-------------------------------------------------------
// echo "hello";
    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",5).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/vaildate/code',
                    APP_ROOT.'lib/php/string/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code'
                   
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

     

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------
    $sess_user_id=$_SESSION['user_id'];
    $sess_permission=$_SESSION['permission'];
    $sess_name=$_SESSION['name'];
    

    if(!isset($sess_user_id)&&!isset($sess_permission)&&!isset($sess_name)){


        echo '<span style="font-size:40px; color:red;">請先登入!!</span>';

        header('Location:http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/index.php');


        die();


        
     }

     if($sess_permission==="1"){

            echo '<span style="font-size:40px; color:red;">你沒有權限進入!!</span>';

            die();
    }
       

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

       

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

      
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

        }

    // //---------------------------------------------------
    // //管理者判斷
    // //---------------------------------------------------

    //---------------------------------------------------
    //系統權限判斷
    //---------------------------------------------------
    //1     校長
    //3     主任
    //5     帶班老師
    //12    行政老師
    //14    主任帶一個班
    //16    主任帶多個班
    //22    老師帶多個班
    //99    管理者

     
    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //user_id    使用者主索引(被閱讀人)
    //book_sid   書籍識別碼
    //flag       閱讀結果指標
    //ajax_cno   閱讀數指標


    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //user_id    使用者主索引(被閱讀人)
    //book_sid   書籍識別碼
    //flag       閱讀結果指標
    //ajax_cno   閱讀數指標

        //POST
        // $user_id =trim($_POST[trim('user_id ')]);
        
        // $flag    =trim($_POST[trim('flag    ')]);
        // $ajax_cno=trim($_POST[trim('ajax_cno')]);

        //SESSION
      
        //分頁
        // $psize=(isset($_POST['psize']))?(int)$_POST['psize']:10;
        // $pinx =(isset($_POST['pinx']))?(int)$_POST['pinx']:1;
        // $psize=($psize===0)?10:$psize;
        // $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------


    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------


            $sess_user_id=$_SESSION['user_id'];

            $sess_user_id=(int)$sess_user_id;
            $create_by   =(int)$sess_user_id;
            $edit_by     =(int)$sess_user_id;

            $book_sid =$_REQUEST['book_sid'];
            $book_isbn_10 =$_REQUEST['book_isbn_10'];
            $book_isbn_13 =$_REQUEST['book_isbn_13'];
            $book_name =$_REQUEST['book_name'];
            $book_library_code =$_REQUEST['book_library_code'];
            $sticker_color=$_REQUEST['color'];
            $sticker_number=$_REQUEST['number'];
            $keyin_cdate        ="NOW()";
            $keyin_mdate        ="NOW()";
            $keyin_ip           =get_ip();

        //-----------------------------------------------

        //-----------------------------------------------

            $book_level_sql="
                                
                        SELECT *                                  
                        FROM `mssr_idc_book_sticker_level_info`                                             
                        WHERE book_sid='{$book_sid}'           
                        ORDER BY keyin_cdate 
                                
                                
            ";

            $book_level_result=db_result($conn_type='pdo',$conn_mssr,$book_level_sql,array(),$arry_conn_mssr);

            if(!empty($book_level_result)){


                 $sticker_sql="

                                 UPDATE `mssr_idc_book_sticker_level_info` 
                                 SET `edit_by`='{$edit_by}',
                                     `user_id`='{$create_by}',
                                     `sticker_color`={$sticker_color},
                                     `sticker_number`={$sticker_number}
                                 WHERE book_sid='{$book_sid}'   

                                
                ";


                $sticker_result=db_result($conn_type='pdo',$conn_mssr,$sticker_sql,array(),$arry_conn_mssr);



            }else{


                $sql="
                        

                        INSERT INTO `mssr_idc_book_sticker_level_info`(
	                        `edit_by`, 
	                        `user_id`, 
	                        `book_sid`, 
	                        `sticker_color`, 
                            `sticker_number`, 
                            `keyin_cdate`, 
                            `keyin_mdate`

                        ) 
                        VALUES 
                        (
	                        {$edit_by},
	                        {$create_by},
	                        '{$book_sid}',
                            {$sticker_color},
                            {$sticker_number},            
	                        {$keyin_cdate},
	                        {$keyin_mdate}
	                       
                    	)

                ";



                $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            }

            header("Location: ../finish.php");



?>