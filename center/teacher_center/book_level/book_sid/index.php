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
                    FROM mssr.mssr_idc_reading_log_spreadsheet
                    

            ";

            $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            foreach ($result as $key => $value) {


                        $user_id=trim($value['user_id']);
                        $book_isbn=trim($value['book_isbn_13']);
                        $pt_book_name=trim($value['pt_book_name']);
                        $pt_book_name = str_replace("'", "’", $pt_book_name);
                        $book_language=trim($value['book_language']);
                        $bopomofo=trim($value['bopomofo']);
                        $major_topic=trim($value['major_topic']);
                        $sub_topic=trim($value['sub_topic']);
                        $minor_topic=trim($value['minor_topic']);
                        $tag=trim($value['tag']);
                        $pages=$value['pages'];
                        $words=$value['words'];
                        $one_person_level=trim($value['one_person_level']);
                        $lexile_level=trim($value['lexile_level']);
                        $keyin_cdate=trim($value['keyin_cdate']);
                        $keyin_mdate=trim($value['keyin_mdate']);





                        echo "4";



            $class_sql="

                    SELECT 
                             `book_sid`,
                             IFNULL(`book_isbn_10`,0) as `book_isbn_10`,
                             IFNULL(`book_isbn_13`,0) as `book_isbn_13` ,
                             IFNULL(`book_name`,0) as `book_name` ,
                             '0' as book_library_code,
                             `keyin_cdate`
                             
                    FROM `mssr_book_class`
                    WHERE (`book_isbn_10` ='{$book_isbn}'
                    AND school_code='idc')
                    OR  (`book_isbn_13` ='{$book_isbn}'
                    AND school_code='idc')
                    OR  (`book_name`='{$pt_book_name}'
                    AND school_code='idc')


                    ORDER BY keyin_cdate

            ";


            echo "$class_sql";
            $class_result=db_result($conn_type='pdo',$conn_mssr,$class_sql,array(),$arry_conn_mssr);

            echo "5";

            $array_output=array();
    
            if(!empty($class_result)){
                    
                    foreach($class_result as $key=>$arry_result){
                                
                                $array_output['book_sid']            =trim($arry_result['book_sid']);
                                $array_output['book_isbn_10']        =trim($arry_result['book_isbn_10']);
                                $array_output['book_isbn_13']        =trim($arry_result['book_isbn_13']);
                                $array_output['book_name']           =trim($arry_result['book_name']);


echo "6";
                            $sql="
                                INSERT INTO `mssr_idc_reading_log`(
                                `user_id`, 
                                `book_sid`, 
                                `book_isbn_10`, 
                                `book_isbn_13`, 
                                `book_library_code`, 
                                `pt_book_name`,
                                `book_name`,
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
                                `keyin_mdate`) 
                                VALUES (
                                '{$user_id}',
                                '{$array_output['book_sid']}',
                                '{$array_output['book_isbn_10']}',
                                '{$array_output['book_isbn_13']}',
                                '',
                                '{$pt_book_name}',
                                '{$arry_result['book_name']}',
                                '{$book_language}',
                                '{$bopomofo}',
                                '{$major_topic}',
                               '{$sub_topic}',
                                '{$minor_topic}',
                                '{$tag}',
                                '{$pages}',
                                '{$words}',
                               '{$one_person_level}',
                                '{$lexile_level}',
                                '{$keyin_cdate}',
                                '{$keyin_mdate}'
                               
                                )

                            ";

                      

                                echo $sql;
                            


                            $insert_c_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

                            echo "s:",$s=$s+1,"<br>";

                      




                                
                    }

             }else{

                        $library_sql="

                                SELECT 
                                        `book_sid`,
                                        IFNULL(`book_isbn_10`,0) as `book_isbn_10`,
                                        IFNULL(`book_isbn_13`,0) as `book_isbn_13`,
                                        IFNULL(`book_name`,0) as `book_name`,
                                        `book_library_code`,
                                        `keyin_cdate`
                                             
                                FROM `mssr_book_library`
                                WHERE(`book_isbn_10` = '{$book_isbn}'
                                AND school_code='idc')

                                OR  (`book_isbn_13` ='{$book_isbn}'
                                AND school_code='idc')

                                OR  (`book_library_code`='{$book_isbn}'
                                AND school_code='idc')

                                OR  (`book_name` ='{$pt_book_name}'
                                AND school_code='idc')


                                ORDER BY keyin_cdate
                                LIMIT 1

                        ";

echo"6";
             
                        $library_result=db_result($conn_type='pdo',$conn_mssr,$library_sql,array(),$arry_conn_mssr);


                
                        if(!empty($library_result)){

                                foreach($library_result as $key=>$arry_result){
                                            
                                            $array_output['book_sid']            =trim($arry_result['book_sid']);
                                            $array_output['book_isbn_10']        =trim($arry_result['book_isbn_10']);
                                            $array_output['book_isbn_13']        =trim($arry_result['book_isbn_13']);
                                            $array_output['book_name']           =trim($arry_result['book_name']);
                                            $array_output['book_library_code']   =trim($arry_result['book_library_code']);


                                            $sql="
                                                INSERT INTO `mssr_idc_reading_log`(
                                                `user_id`, 
                                                `book_sid`, 
                                                `book_isbn_10`, 
                                                `book_isbn_13`, 
                                                `book_library_code`,
                                                `book_name`,
                                                `pt_book_name`,
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
                                                `keyin_mdate`) 
                                                VALUES (
                                                '{$user_id}',
                                                '{$array_output['book_sid']}',
                                                '{$array_output['book_isbn_10']}',
                                                '{$array_output['book_isbn_13']}',
                                                '{$array_output['book_library_code']}',
                                                '{$pt_book_name}',
                                               '{$array_output['book_name']}',
                                                '{$book_language}',
                                                '{$bopomofo}',
                                                '{$major_topic}',
                                               '{$sub_topic}',
                                                '{$minor_topic}',
                                                '{$tag}',
                                                '{$pages}',
                                                '{$words}',
                                               '{$one_person_level}',
                                                '{$lexile_level}',
                                                '{$keyin_cdate}',
                                                '{$keyin_mdate}'
                                               
                                                )

                                            ";

                                             

                                                echo $sql;
                                            
                                            $insert_l_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

                                           echo "s:",$s=$s+1,"<br>";

                                         
                                }
                        }else{

                                        $sql1="
                                                INSERT INTO `mssr_idc_reading_log_no_book`(
                                                `user_id`, 
                                                `book_isbn_13`,
                                                `pt_book_name`,
                                                `book_name`,
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
                                                `keyin_mdate`) 
                                                VALUES (
                                                '{$user_id}',
                                                '{$book_isbn}',
                                                '{$pt_book_name}',
                                                '',
                                                '{$book_language}',
                                                '{$bopomofo}',
                                                '{$major_topic}',
                                               '{$sub_topic}',
                                                '{$minor_topic}',
                                                '{$tag}',
                                                '{$pages}',
                                                '{$words}',
                                               '{$one_person_level}',
                                                '{$lexile_level}',
                                                '{$keyin_cdate}',
                                                '{$keyin_mdate}'
                                               
                                                )

                                        ";

                                        echo $sql;
                                                
                                        
                                        $insert_l_result=db_result($conn_type='pdo',$conn_mssr,$sql1,array(),$arry_conn_mssr);

                                        echo "s:",$s=$s+1,"<br>";



                                           
                        }

            }



 }



echo json_encode($array_output,true);
        
?>
