<?php



            $teacher_uid = teacher_uid($class_code);
            $hotList     = hot($teacher_uid);
            // $check_book  = check_book($teacher_uid);
            // $check       = array();





            foreach ($hotList as $key => $value) {
                $book_sid                     = $value['book_sid'];
                $arrys_book_info              = get_book_info($conn_mssr,$book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                $book_name                    = trim($arrys_book_info[0]['book_name']);
                $hotList[$key]['book_name']   = $book_name;
            }



            // array_multisort($hotList,SORT_DESC);

            // echo '<pre>';
            // print_r($hotList);

?>