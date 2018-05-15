<?php


           $hotList      = hot();
           $teacher_uid  = teacher_uid($class_code);
           // $check_book   = check_book($teacher_uid);
           // $check 		   = array();






           foreach ($hotList as $key => $value) {
                $book_sid                     = $value['book_sid'];
                $hotList[$key]['count']       = countBook ($book_sid,$teacher_uid);
                $arrys_book_info              = get_book_info($conn_mssr,$book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                $book_name                    = trim($arrys_book_info[0]['book_name']);
                $hotList[$key]['book_name']   = $book_name;

           }




?>