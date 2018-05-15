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
              $book_info=array();


                $class_sql="
                             SELECT book_sid,book_name,book_isbn_13,book_isbn_10,book_author,book_publisher,0 as book_library_code 
                             FROM `mssr_book_class`
                             WHERE school_code='idc'

                            UNION ALL

                            SELECT book_sid,book_name,book_isbn_13,book_isbn_10,book_author,book_publisher,book_library_code     
                            FROM `mssr_book_library`
                            WHERE school_code='idc'
                         
                  ";

        echo $class_sql,"<br>";
              
        $book_arrys_results=db_result($conn_type='pdo',$conn_mssr,$class_sql,array(),$arry_conn_mssr);

      

        foreach ($book_arrys_results as $key => $value) {

                    $book_sid=$value['book_sid'];  
                    $book_name=$value['book_name']; 
                    $book_isbn_13=$value['book_isbn_13']; 
                    $book_isbn_10=$value['book_isbn_10']; 
                     $book_library_code=$value['book_library_code'];
                    $book_author=$value['book_author'];
                    $book_publisher=$value['book_publisher'];


                    $level_sql="

                                       SELECT book_sid
                                       FROM mssr.mssr_idc_book_sticker_level_info
                                       WHERE book_sid='{$book_sid}'
                                       AND administrator_level !=0

                      ";

                      // echo $level_sql;

                      $level_results=db_result($conn_type='pdo',$conn_mssr,$level_sql,array(),$arry_conn_mssr);


                  if(!empty($level_results)){

                      $a+=1;
                     
                
                  }else{

                      $b+=1;
                      array_push($d,"book_sid:".$book_sid."書名:".$book_name."isbn 13:".$book_isbn_13."isbn_10:".$book_isbn_10."圖書館編號:".$book_library_code."作者:".$book_author."出版社:".$book_publisher);




                    

                 }






              
  }



echo "count:",count($book_arrys_results),"<br>";

echo "a:",$a,"<br>";

echo "b:",$b,"<br>";

print_r($d);echo "<br>";



 ;?>


