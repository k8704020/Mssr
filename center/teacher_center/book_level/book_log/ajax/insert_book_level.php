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

        

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

       
    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------
    $sess_user_id=$_SESSION['book_level_user_id'];
    $sess_permission=$_SESSION['book_level_permission'];
    $sess_name=$_SESSION['book_level_name'];
    

    if(!isset($sess_user_id)&&!isset($sess_permission)&&!isset($sess_name)){


        echo '<span style="font-size:40px; color:red;">請先登入!!</span>';

        header('Location:http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/index.php');


        die();


        
     }

     if($sess_permission==="2"){

            echo '<span style="font-size:40px; color:red;">你沒有權限進入!!</span>';

            die();
    }

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

        
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
            
            $sess_user_id=$_SESSION['book_level_user_id'];

            $sess_user_id=(int)$sess_user_id;
            $create_by   =(int)$sess_user_id;
            $edit_by     =(int)$sess_user_id;

            $book_sid =$_REQUEST['book_sid'];
            $book_isbn_10 =$_REQUEST['book_isbn_10'];
            $book_isbn_13 =$_REQUEST['book_isbn_13'];
            $book_name =$_REQUEST['book_name'];
            $book_library_code =$_REQUEST['book_library_code'];


            $language =$_REQUEST['language'];
            $bopomofo=$_REQUEST['bopomofo'];
            $major_topic_val=$_REQUEST['major_topic'];
            $major_topic=implode(",",$major_topic_val);

            $sub_topic_val= $_REQUEST['sub_topic'];
            $sub_topic=implode(",",$sub_topic_val);
    


            $minor_topic_val= $_REQUEST['minor_topic'];
            $minor_topic=implode(",",$minor_topic_val);

            $other_input1=$_REQUEST['other_input1'];
            $other_input2=$_REQUEST['other_input2'];
            $other_input3=$_REQUEST['other_input3'];        

            $tag1= $_REQUEST['tag1'];
            $tag2= $_REQUEST['tag2'];
            $tag3= $_REQUEST['tag3'];
            $tag4= $_REQUEST['tag4'];
            $tag5= $_REQUEST['tag5'];
            $pages= $_REQUEST['pages'];
            $words= $_REQUEST['words'];
            $level_one= $_REQUEST['level_one_val'];
            $level_two= $_REQUEST['level_one_val'];
            $tag="{$tag1}".","."{$tag2}".","."{$tag3}";
            if($tag4){
                $tag.=","."$tag4";
            }
            if($tag5){
                 $tag.=","."$tag5";

            }

            $qq=array($level_one,$level_two);
            $hard=implode("-",$qq);
            $one_person_avg_level=($level_one+$level_two)/2;


            $eng_level= $_REQUEST['eng_level'];
            $keyin_cdate        ="NOW()";
            $keyin_mdate        ="NOW()";
            $keyin_ip           =get_ip();




        //-----------------------------------------------

        //-----------------------------------------------


            $sql="
                        

                        INSERT INTO `mssr_idc_reading_log_spreadsheet`( 
	                        `user_id`, 
	                        `book_sid`, 
	                        `book_isbn_10`, 
	                        `book_isbn_13`, 
	                        `book_library_code`, 
	                        `book_language`, 
	                        `bopomofo`, 
	                        `major_topic`, 
	                        `sub_topic`, 
	                        `minor_topic`, 
	                        `tag`, 
	                        `pages`, 
	                        `words`, 
                            `one_person_level`,
	                        `lexile_level`, 
	                        `keyin_cdate`, 
	                        `keyin_mdate`, 
	                        `keyin_ip`
                        ) 
                        VALUES 
                        (
	                        {$create_by},
	                        '{$book_sid}',
	                        '{$book_isbn_10}',
	                        '{$book_isbn_13}',
	                        '{$book_library_code}',
	                        {$language},
	                        '{$bopomofo}',
	                        '".$major_topic."',
	                        '".$sub_topic."',
	                        '".$minor_topic."',
	                        '{$tag}',
	                        '{$pages}',
	                        '{$words}',
                            '{$one_person_avg_level}',
	                        '{$eng_level}',
	                        {$keyin_cdate},
	                        {$keyin_mdate},
	                        '{$keyin_ip}'
                    	)

        ";




       
// echo $sql;
// die();


        $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

      

        $book_level_sql="
                            
                    SELECT *                                  
                    FROM `mssr_idc_book_sticker_level_info` 
                                                                                
                    WHERE book_sid='{$book_sid}'           
                    ORDER BY keyin_cdate 
                            
                            
        ";

        $book_level_result=db_result($conn_type='pdo',$conn_mssr,$book_level_sql,array(),$arry_conn_mssr);


        $log_sql="
                
                    SELECT * 
                    FROM `mssr_idc_reading_log_spreadsheet` 
                    WHERE book_sid='{$book_sid}'
                    ORDER BY keyin_cdate 
        ";

        $log_result=db_result($conn_type='pdo',$conn_mssr,$log_sql,array(),$arry_conn_mssr);

        $reading_log_id=$log_result[0]['reading_log_id'];



        if($other_input1!=""){

            $other_sql1="

                INSERT INTO `mssr_idc_reading_log_other_options_rev`(
                    `reading_log_id`, 
                    `title`, 
                    `other_options` 
                
                ) 
                VALUES (
                    '{$reading_log_id}',
                    'major_topic',
                    '{$other_input1}'
                
                )
            ";
            $other_sql_result1=db_result($conn_type='pdo',$conn_mssr,$other_sql1,array(),$arry_conn_mssr);

        }

        if($other_input2!=""){
            $other_sql2="

                INSERT INTO `mssr_idc_reading_log_other_options_rev`(
                    `reading_log_id`, 
                    `title`, 
                    `other_options`
                
                ) 
                VALUES (
                    '{$reading_log_id}',
                    'sub_topic',
                    '{$other_input2}'
                
                )
            ";
            $other_sql_result2=db_result($conn_type='pdo',$conn_mssr,$other_sql2,array(),$arry_conn_mssr);

        }
        if($other_input3!=""){

            $other_sql3="

                INSERT INTO `mssr_idc_reading_log_other_options_rev`(
                    `reading_log_id`, 
                    `title`, 
                    `other_options`
                
                ) 
                VALUES (
                    '{$reading_log_id}',
                    'minor_topic',
                    '{$other_input3}'
                
                )
            ";
            $other_sq1_result3=db_result($conn_type='pdo',$conn_mssr,$other_sql3,array(),$arry_conn_mssr);

        }

       

        if(empty($book_level_result)){

                if(count($log_result)<2){

                    $avg_level_sql="

                                 INSERT INTO `mssr_idc_book_sticker_level_info`
                                 (
                                 `edit_by`, 
                                 `user_id`, 
                                 `book_sid`, 
                                 `sticker_color`, 
                                 `sticker_number`, 
                                 `avg_level`, 
                                 `need_read_again`,
                                 `avg_status`, 
                                 `keyin_cdate`, 
                                 `keyin_mdate`
                                 ) 
                                 VALUES 
                                 (
                                 '0',
                                 '0',
                                 '{$book_sid}',
                                 '0',
                                 '0',
                                 '{$one_person_avg_level}',
                                 '0',
                                 '1',
                                 NOW(),
                                 NOW()
                                 )

                                
                    ";
                    
                    $avg_level_result=db_result($conn_type='pdo',$conn_mssr,$avg_level_sql,array(),$arry_conn_mssr);

                }

        }

        else{



            if($book_level_result[0]['avg_status']=="1"&&$book_level_result[0]['need_read_again']=="0"){

                
    

                $book_level_one=$log_result[0]['one_person_level'];
                $book_level_two=$log_result[1]['one_person_level'];
                $new_level=($book_level_one+ $book_level_two)/2;


                $avg_level_sql="

                                 UPDATE `mssr_idc_book_sticker_level_info` 
                                 SET `avg_level`='{$new_level}',
                                     `avg_status`='2'
                                 WHERE book_sid='{$book_sid}'   

                                
                ";
                $avg_level_result=db_result($conn_type='pdo',$conn_mssr,$avg_level_sql,array(),$arry_conn_mssr);


            }

            else if($book_level_result[0]['avg_status']==="1"&&$book_level_result[0]['need_read_again']==="1"){ 


                 if(count($log_result)<=3){



                            $need_read_again_sql="
                                    
                                    SELECT *                                  
                                    FROM `mssr_idc_read_again_info_log`                                                                                      
                                    WHERE book_sid='{$book_sid}'           
                                    ORDER BY keyin_cdate
                                            
                                    
                            ";

                            $need_read_again_result=db_result($conn_type='pdo',$conn_mssr,$need_read_again_sql,array(),$arry_conn_mssr);


                            if(!empty($need_read_again_result)){

                                     $avg_level_sql="

                                             INSERT INTO `mssr_idc_read_again_info_log`(
                                             `edit_by`, 
                                             `user_id`, 
                                             `book_sid`, 
                                             `need_read_again`, 
                                             `avg_level`,  
                                             `keyin_cdate`, 
                                             `keyin_mdate`
                                             ) 
                                             VALUES 
                                             (
                                             '0',
                                             '0',
                                             '{$book_sid}',
                                             '2',
                                             '{$one_person_avg_level}',
                                             NOW(),
                                             NOW()
                                             );

                                            
                                    ";
                                    $avg_level_result=db_result($conn_type='pdo',$conn_mssr,$avg_level_sql,array(),$arry_conn_mssr);

                            }else{

                                $one_level_sql="

                                             INSERT INTO `mssr_idc_read_again_info_log`(
                                             `edit_by`, 
                                             `user_id`, 
                                             `book_sid`, 
                                             `need_read_again`, 
                                             `avg_level`,  
                                             `keyin_cdate`, 
                                             `keyin_mdate`
                                             ) 
                                             VALUES 
                                             (
                                             '0',
                                             '0',
                                             '{$book_sid}',
                                             '1',
                                             '{$book_level_result[0]['avg_level']}',
                                             NOW(),
                                             NOW()
                                             );

                                            
                                    ";
                                    $avg_level_result=db_result($conn_type='pdo',$conn_mssr,$one_level_sql,array(),$arry_conn_mssr);

                                    $two_level_sql="

                                             INSERT INTO `mssr_idc_read_again_info_log`(
                                             `edit_by`, 
                                             `user_id`, 
                                             `book_sid`,  
                                             `need_read_again`, 
                                             `avg_level`,  
                                             `keyin_cdate`, 
                                             `keyin_mdate`
                                             ) 
                                             VALUES 
                                             (
                                             '0',
                                             '0',
                                             '{$book_sid}',
                                             '2',
                                             '{$one_person_avg_level}',
                                             NOW(),
                                             NOW()
                                             );

                                            
                                    ";
                                    $avg_level_result=db_result($conn_type='pdo',$conn_mssr,$two_level_sql,array(),$arry_conn_mssr);

                            }



                 }else if(count($log_result)>3){


 

                    $book_level_three=$log_result[2]['one_person_level'];
                    $book_level_four=$log_result[3]['one_person_level'];
                    $new_level=($book_level_three+ $book_level_four)/2;

                    $need_read_again_sql="
                                    
                                    SELECT *                                  
                                    FROM `mssr_idc_read_again_info_log`                                                                                      
                                    WHERE book_sid='{$book_sid}'
                                    AND  `need_read_again`=2         
                                    ORDER BY keyin_cdate
                                            
                                    
                    ";



                    $need_read_again_result=db_result($conn_type='pdo',$conn_mssr,$need_read_again_sql,array(),$arry_conn_mssr);



                    if(empty($need_read_again_result)){


                        $two_level_sql="

                                        INSERT INTO `mssr_idc_read_again_info_log`(
                                             `edit_by`, 
                                             `user_id`, 
                                             `book_sid`, 
                                             `need_read_again`, 
                                             `avg_level`,  
                                             `keyin_cdate`, 
                                             `keyin_mdate`
                                             ) 
                                             VALUES 
                                             (
                                             '0',
                                             '0',
                                             '{$book_sid}',
                                             '2',
                                             '{$new_level}',
                                             NOW(),
                                             NOW()
                                             );

                                            
                        ";


                        $avg_level_result=db_result($conn_type='pdo',$conn_mssr,$two_level_sql,array(),$arry_conn_mssr);

                    }else{

                        $avg_level_sql="

                                 UPDATE `mssr_idc_book_sticker_level_info` 
                                 SET `avg_level`='{$new_level}',
                                 WHERE book_sid='{$book_sid}'   

                                
                        ";

              
                        $avg_level_result=db_result($conn_type='pdo',$conn_mssr,$avg_level_sql,array(),$arry_conn_mssr);

                    }





                 }
            }

  




        }  

 

?>




