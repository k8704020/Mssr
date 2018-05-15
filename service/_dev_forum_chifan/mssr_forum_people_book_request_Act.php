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



        //建立連線 user
        $conn_user=conn($db_type='mysql',$arry_conn_user);

        //建立連線 mssr
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
        //-----------------------------------------------

        $book_isbn  =  '';
        if(isset($_GET['request_book_id'])){
            $book_isbn = $_GET['request_book_id'];
        }

        $selectBook ='';
        if(isset($_GET['select_book'])){
            $selectBook =  $_GET['select_book'];
        }

        if($book_isbn==''){

            $sql  = "


                         
                         
                         SELECT
                                (book_name) as name,
                                book_sid,
                                keyin_cdate
                         FROM
                                mssr_book_library
                         WHERE  book_sid =  '$selectBook'

                         UNION

                         SELECT
                                (book_name) as name,
                                  book_sid,
                                keyin_cdate
                         FROM
                                mssr_book_class
                         WHERE  book_sid    =  '$selectBook'

                         UNION

                         SELECT
                                    (book_name) as name,
                                      book_sid,
                                keyin_cdate
                                FROM
                                    `mssr_book_unverified`
                         WHERE  book_sid    =  '$selectBook'

                          UNION

                         SELECT
                                     (book_name) as name,
                                      book_sid,
                                keyin_cdate
                                FROM
                                    `mssr_book_global`
                         WHERE  book_sid    =  '$selectBook'


                        
                        GROUP BY name
                            ORDER BY
                            `keyin_cdate` DESC

                ";
//                echo "<Pre>";
//                print_r($sql);
//                echo "</Pre>";
//die;
        }else{




        $sql  = "


                         
                         SELECT
                                (book_name) as name,
                                book_sid,
                                keyin_cdate
                         FROM
                                mssr_book_library
                         WHERE  (
                                book_isbn_10    =  '$book_isbn'
                                OR book_isbn_13 =  '$book_isbn'
                                )



                         UNION

                         SELECT
                                (book_name) as name,
                                book_sid,
                                keyin_cdate
                         FROM
                                mssr_book_class
                         WHERE  (
                                book_isbn_10    =  '$book_isbn'
                                OR book_isbn_13 =  '$book_isbn'
                                )
                         UNION

                         SELECT
                                 (book_name) as name,
                                  book_sid,
                                keyin_cdate

                         FROM
                                mssr_book_unverified
                         WHERE  (
                                book_isbn_10    =  '$book_isbn'
                                OR book_isbn_13 =  '$book_isbn'
                                )
                         UNION

                         SELECT
                                (book_name) as name,
                                book_sid,
                                keyin_cdate
                         FROM
                                mssr_book_global
                         WHERE  (
                                book_isbn_10    =  '$book_isbn'
                                OR book_isbn_13 =  '$book_isbn'
                                )

                        
                        GROUP BY name
                            ORDER BY
                            `keyin_cdate` DESC

                ";

        }

              $arrys_book = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


              if(empty($arrys_book)){

                 $arrys_book[0]['flag'] = 'err';

              }

              echo json_encode($arrys_book);



?>









