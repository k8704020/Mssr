<?php   

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",7).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/date/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();


//-----------------------------------------------
//資料庫
//-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);        


            $year          =date("Y");
            $month         =date("m");
            $date_now      =(int)date('j');
            $week_cno      =(int)(ceil($date_now/7)-1);
            $arry_date_week=date_week_array($year,$month);
            $week_sdate    =trim($arry_date_week[$week_cno]['sdate']);
            $week_edate    =trim($arry_date_week[$week_cno]['edate']);
            
            
            if(isset($_GET['book_sid'])){
                $book_sid = $_GET['book_sid'];
            }else{
                        $msg='
                            <script>
                                alert("書籍不存在");
                                history.back(-1);
                            </script>
                        ';
                        die($msg);
            }
            if(isset($_GET['tid'])){
                $tid = $_GET['tid'];
            }else{
                        $msg='
                            <script>
                                alert("不是正確的uid");
                                history.back(-1);
                            </script>
                        ';
                        die($msg);
            }

            if ($tid =="") {
                 $msg='
                            <script>
                                alert("老師權限錯誤");
                                history.back(-1);
                            </script>
                        ';
                        die($msg);
            }

            $sql = "
                SELECT 
                    `mssr`.`mssr_book_library`.`book_isbn_10`,
                    `mssr`.`mssr_book_library`.`book_isbn_13`  
                FROM `mssr`.`mssr_book_library`   WHERE book_sid     = '$book_sid'
                union
                SELECT 
                    `mssr`.`mssr_book_class`.`book_isbn_10`,
                    `mssr`.`mssr_book_class`.`book_isbn_13`     
                FROM `mssr`.`mssr_book_class`     WHERE book_sid     = '$book_sid'
                union
                SELECT 
                     `mssr`.`mssr_book_global` .`book_isbn_10`,
                     `mssr`.`mssr_book_global` .`book_isbn_13`  
                FROM `mssr`.`mssr_book_global`  WHERE book_sid       = '$book_sid'
         
            ";
            
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            
            if(isset($db_results[0]['book_isbn_10'])){
                $book_isbn_10 = $db_results[0]['book_isbn_10'];
            }else{
                 $book_isbn_10 ='';
            }
            
            if(isset($db_results[0]['book_isbn_13'])){
                $book_isbn_13 = $db_results[0]['book_isbn_13'];
            }else{
                 $book_isbn_13 ='';
            }    
               
          
            

            
            $sql="      
                INSERT INTO 
                    `mssr_forum`.`mssr_forum_hot_booklist_discuss`(`create_by`, `book_sid`, `book_isbn_10`, `book_isbn_13`, `keyin_cdate`) 
                VALUES ($tid,'$book_sid','$book_isbn_10','$book_isbn_13',now())
            ";

            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            
               
            $msg='
                    <script>
                        history.back(-1);
                    </script>
                ';
                die($msg);
        



?>