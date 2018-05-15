<?php
//-------------------------------------------------------
//教師中心
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();
        // $_SESSION['uid']=5029;

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",4).'config/config.php');
        require_once(str_repeat("../",4)."/inc/get_black_book_info/code.php");
        // require_once(str_repeat("../",1)."/user/user_log_in.php");
//外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/vaildate/code',
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

    //---------------------------------------------------

    $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------


            $a=0;
            $b=0;
            $c=0;
            $s=0;


            $sql="
                    SELECT *
                    FROM mssr_idc_reading_log
                    

            ";

            $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            foreach ($result as $key => $value) {

                    $user_id=$value['user_id'];
                    $book_sid=$value['book_sid'];
                    $book_isbn_10=$value['book_isbn_10'];
                    $book_isbn_13=$value['book_isbn_13'];
                    $book_library_code=$value['book_library_code'];
                    $book_language=$value['book_language'];
                    $bopomofo=$value['bopomofo'];
                    $major_topic=$value['major_topic'];
                    $sub_topic=$value['sub_topic'];
                    $minor_topic=$value['minor_topic'];
                    $tag=$value['tag'];
                    $pages=$value['pages'];
                    $words=$value['words'];
                    $one_person_level=$value['one_person_level'];
                    $lexile_level=$value['lexile_level'];
                    $keyin_cdate=$value['keyin_cdate'];
                    $keyin_mdate=$value['keyin_mdate'];
                    
                    
                    

                   
                        
                    $class_sql="
                            SELECT 
                                    *    
                            FROM `mssr_idc_book_sticker_level_info`
                            WHERE `book_sid` ='{$book_sid}'
                            
                            ORDER BY keyin_cdate
                       

                    ";


         
                    $class_result=db_result($conn_type='pdo',$conn_mssr,$class_sql,array(),$arry_conn_mssr);

                    $array_output=array();
            
                    if(!empty($class_result)){


                            
 

                    }else{

                                

                                    $sql="
                                        INSERT INTO `mssr_idc_book_sticker_level_info`(
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
                                        ) VALUES (
                                        '1',
                                        '1',
                                        '{$book_sid}',
                                        '',
                                        '',
                                        '',
                                        '',
                                        0,
                                        now(),
                                        now()

                                        )


                                    ";

                                     

                                        echo $sql;
                                    
                                    $insert_l_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

                                   echo "s:",$s=$s+1,"<br>";

                                                 
                                     
                    }



            }



echo json_encode($array_output,true);
        
?>
