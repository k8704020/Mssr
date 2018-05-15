<?php
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    //設定頁面語系
    header("Content-Type: text/html; charset=UTF-8");

    //設定文字內部編碼
    mb_internal_encoding("UTF-8");

    //設定台灣時區
    date_default_timezone_set('Asia/Taipei');

    //---------------------------------------------------
    //函式: search_book_info_online()
    //用途: 查找線上書籍資訊
    //---------------------------------------------------
    //$book_code    書籍代碼
    //
    //---------------------------------------------------

        //外掛設定檔
        require_once(str_repeat("../",3)."config/config.php");
        require_once(str_repeat("../",1)."code.php");
        require_once(str_repeat("../",3)."inc/code.php");
        require_once(str_repeat("../",3)."lib/php/db/code.php");

        //調配
        set_time_limit(0);

// 		//輸入識別碼
//        $book_code='9789570302134';
//        //$book_code='9789573321742';
//
//        $search_book_info_online=search_book_info_online($book_code);
//
//        echo "<Pre>";
//        print_r($search_book_info_online);
//        echo "</Pre>";

        $sql="
            SELECT COUNT(*)
            FROM `mssr_book_library`
            WHERE 1=1
                AND (
                    `book_isbn_13`<>''
                    OR
                    `book_isbn_10`<>''
                )
            UNION
            SELECT COUNT(*)
            FROM `mssr_book_class`
            WHERE 1=1
                AND (
                    `book_isbn_13`<>''
                    OR
                    `book_isbn_10`<>''
                )
            UNION
            SELECT COUNT(*)
            FROM `mssr_book_global`
            WHERE 1=1
                AND (
                    `book_isbn_13`<>''
                    OR
                    `book_isbn_10`<>''
                )
            UNION
            SELECT COUNT(*)
            FROM `mssr_book_unverified`
            WHERE 1=1
                AND (
                    `book_isbn_13`<>''
                    OR
                    `book_isbn_10`<>''
                )
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr='',$sql,array(),$arry_conn_mssr);
        $numrow=0;
        foreach($db_results as $db_result){
            $numrow+=(int)$db_result['COUNT(*)'];
        }
        //$_GET['psize']=50000;
        //$_GET['pinx'] =1;
        $psize=(int)50000;
        if(isset($_GET['pinx'])){
            $pinx=(int)$_GET['pinx'];
            if($pinx===0){
                $pinx=1;
            }
        }else{
            $pinx=1;
        }
        $pnos  =ceil($numrow/$psize);
        $pinx  =($pinx>$pnos)?die('ok'):$pinx;
        $sinx  =(($pinx-1)*$psize)+1;
        $einx  =(($pinx)*$psize);
        $einx  =($einx>$numrow)?$numrow:$einx;

        $sql="
            SELECT `book_isbn_10`,`book_isbn_13`
            FROM `mssr_book_library`
            WHERE 1=1
                AND (
                    `book_isbn_13`<>''
                    OR
                    `book_isbn_10`<>''
                )
            UNION
            SELECT `book_isbn_10`,`book_isbn_13`
            FROM `mssr_book_class`
            WHERE 1=1
                AND (
                    `book_isbn_13`<>''
                    OR
                    `book_isbn_10`<>''
                )
            UNION
            SELECT `book_isbn_10`,`book_isbn_13`
            FROM `mssr_book_global`
            WHERE 1=1
                AND (
                    `book_isbn_13`<>''
                    OR
                    `book_isbn_10`<>''
                )
            UNION
            SELECT `book_isbn_10`,`book_isbn_13`
            FROM `mssr_book_unverified`
            WHERE 1=1
                AND (
                    `book_isbn_13`<>''
                    OR
                    `book_isbn_10`<>''
                )
            LIMIT {$sinx}, {$einx}
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr='',$sql,array(),$arry_conn_mssr);
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
        foreach($db_results as $db_result){
            $rs_book_isbn_10=trim($db_result['book_isbn_10']);
            $rs_book_isbn_13=trim($db_result['book_isbn_13']);
            if(($rs_book_isbn_10!=='')&&($rs_book_isbn_13!=='')){
                $book_code=$rs_book_isbn_13;
            }
            if(($rs_book_isbn_10!=='')){
                $book_code=$rs_book_isbn_10;
            }
            if(($rs_book_isbn_13!=='')){
                $book_code=$rs_book_isbn_13;
            }

            $search_book_info_online=search_book_info_online($book_code);
            echo "<Pre>";
            print_r($book_code);
            echo "</Pre>";
            echo "<Pre>";
            print_r($search_book_info_online);
            echo "</Pre>";
            die();

            //$sql="
            //    INSERT INTO `mssr_forum_book_ch_no_rev` SET
            //        `book_sid`  =  '{$book_code }' ,
            //        `book_ch_no`=  '{$book_code }' ;
            //";
            //$conn_mssr->exec($sql);
        }
?>