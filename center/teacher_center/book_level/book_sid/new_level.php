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

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------


            //建立連線 user
        $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

    

        

        $a=0;
        $b=0;
        $c=0;
        $d=array();


        $book_sql="
                SELECT *
                FROM `idc_level_book_info_no_data` 
                   
            ";
        // echo $book_sql,"<br>";
              
        $book_arrys_results=db_result($conn_type='pdo',$conn_mssr,$book_sql,array(),$arry_conn_mssr);

        foreach ($book_arrys_results as $key => $value) {

                    $id=$value['id']; 
                    $book_name=trim($value['book_name']);
                    $book_name = str_replace("'", "’", $book_name);
                    $book_author=trim($value['book_author']);
                    $book_publish=trim($value['book_publish']);
                    $book_isbn_13=trim($value['isbn_13']);
                    $book_isbn_10=trim($value['isbn_10']);
                    $library_code=trim($value['library_code']);
                    $language=$value['language'];
                    $hard=$value['hard'];
                    $bopomofo=$value['bopomofo'];
                    $words=$value['words'];
                    $pages=$value['pages'];
                    $book_pictures=$value['book_pictures'];
                    $level=$value['level'];
                    $ps=$value['ps'];


                  $class_sql="
                             SELECT `book_sid`
                             FROM `mssr_book_class`
                             WHERE ( `book_isbn_13` ='{$book_isbn_13}'
                             AND school_code='idc')

                             OR  ( `book_isbn_10` ='{$book_isbn_10}'
                             AND school_code='idc')

                            UNION ALL

                            SELECT `book_sid`               
                            FROM `mssr_book_library`
                            WHERE `book_library_code`='{$library_code}'
                            AND school_code='idc'
                         
                  ";


                    
                  $class_results=db_result($conn_type='pdo',$conn_mssr,$class_sql,array(),$arry_conn_mssr);

                  if(!empty($class_results)){

                      $a+=1;
                      foreach ($class_results as $index_key =>$val) {
                              
                           
                                $book_sid= $val['book_sid'];    


                                //==============
                                //找尋等級資料庫
                                //==============

                                $level_sql="

                                       SELECT book_sid
                                       FROM mssr.mssr_idc_book_sticker_level_info
                                       WHERE book_sid='{$book_sid}'

                                    ";

                                $level_results=db_result($conn_type='pdo',$conn_mssr,$level_sql,array(),$arry_conn_mssr);



                                if(!empty($level_results)){



                                    $update_sql="
                                       UPDATE `mssr_idc_book_sticker_level_info` 
                                       SET `edit_by`='214252',
                                           `user_id`='214252',
                                           `administrator_level`='{$level}',
                                           `hard`='{$hard}',
                                           `bopomofo`='{$bopomofo}',
                                           `language`='{$language}',
                                           `pages`='{$pages}',
                                           `words`='{$words}',
                                           `book_pictures`='{$book_pictures}',
                                           `keyin_mdate`=NOW()

                                        WHERE book_sid='{$book_sid}'

                                    ";

                                // echo "5",$update_sql,"<br>";
                                    $results=db_result($conn_type='pdo',$conn_mssr,$update_sql,array(),$arry_conn_mssr);


                                        $b+=1;

                                 }else{


                                     $insert_sql="

                                        INSERT INTO `mssr_idc_book_sticker_level_info`(
                                        `edit_by`, 
                                        `user_id`, 
                                        `book_sid`, 
                                        `sticker_color`, 
                                        `sticker_number`, 
                                        `administrator_level`, 
                                        `hard`, 
                                        `bopomofo`, 
                                        `language`, 
                                        `pages`, 
                                        `words`, 
                                        `book_pictures`, 
                                        `keyin_cdate`, 
                                        `keyin_mdate`
                                        ) VALUES (
                                        '214252',
                                        '214252',
                                        '{$book_sid}',
                                        '0',
                                        '0',
                                        '{$level}',
                                        '{$hard}',
                                        '{$bopomofo}',
                                        '{$language}',
                                        '{$pages}',
                                        '{$words}',
                                        '{$book_pictures}',
                                        NOW(),
                                        NOW()
                                        )
                                   

                                    ";

                                           echo "6",$insert_sql,"<br>";

                                    $results=db_result($conn_type='pdo',$conn_mssr,$insert_sql,array(),$arry_conn_mssr);

                                        $c+=1;

                                 }




                      }

                
                  }else{

                     array_push($d,$book_isbn_13);

                  
                  

              }






              
  }






echo $a,"<br>";

echo $b,"<br>";

echo $c,"<br>";
print_r($d);


 ;?>


