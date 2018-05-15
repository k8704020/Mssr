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


        $array_output=array();


            $sql="
                    SELECT book_sid
                    FROM mssr_idc_reading_log
                    

            ";

            $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            foreach ($result as $key => $value) {

                    $array_output[$key]['book_sid'] =$value['book_sid'];

                    $class_sql="
                            SELECT 
                                    * 
                            FROM `mssr_idc_book_sticker_level_info`
                            WHERE `book_sid` ='{$book_sid}'
                            
                            ORDER BY keyin_cdate
                       

                    ";
  
                    $class_result=db_result($conn_type='pdo',$conn_mssr,$class_sql,array(),$arry_conn_mssr);
                    
                    if(!empty($class_result)){

                        $class_sql="

                            SELECT 
                                    *    
                            FROM `mssr_idc_reading_log`
                            WHERE `book_sid` ='{$array_output[$key]['book_sid']}'
                            
                            ORDER BY keyin_cdate
                       

                        ";

                        $class_result=db_result($conn_type='pdo',$conn_mssr,$class_sql,array(),$arry_conn_mssr);

                        foreach ($class_result as $index_key => $book) {

                            $array_output[$key]['one_person_level'][$index_key]=$book['one_person_level'];

                                 $one=$array_output[$key]['one_person_level'][0];
                                 $two=$array_output[$key]['one_person_level'][1];
                                 $three=$array_output[$key]['one_person_level'][2];
                                 $four=$array_output[$key]['one_person_level'][3];

                                if(count($class_result)=="1"){

                                     $one=$array_output[$key]['one_person_level'][0];
                                     $array_output[$key]['avg_level']=$one;

                                    echo "1","<br>";


                                }else if (count($class_result)=="2"){

                                    if($one>$two){
                                            $about_level=$one-$two;
                                           
                                    }else{

                                            $about_level=$two-$one;
                                            
                                    }

                             

                                     $array_output[$key]['about_level']="{$about_level}";


                                     $array_output[$key]['avg_level']=($one+$two)/2;

                                    echo "2","<br>";


                                }else if(count($class_result)=="3"){

                                    echo "3","<br>";

                                        if($one>$two){
                                                $about_level=$one-$two;
                                               
                                        }else{

                                                $about_level=$two-$one;
                                                
                                        }

                                      

                                            $array_output[$key]['avg_level']=($one+$two)/2;
                                             $array_output[$key]['about_level']="{$about_level}";

                                    

                                }else{
                                        echo "4","<br>";
                                             
                                            if($one>$two){
                                                $about_level=$one-$two;

                                            }else{

                                                $about_level=$two-$one;
                                                
                                            }

                                            $array_output[$key]['about_level']="{$about_level}";

                                            if($about_level>6){
                                                $array_output[$key]['avg_level']=($three_and_four)/2;

                                            }else{

                                                $one_and_two=($one+$two)/2;
                                                $three_and_four=($three+$four)/2;
                                                $array_output[$key]['avg_level']=($one_and_two + $three_and_four)/2;

                                            }

                                             $array_output[$key]['avg_level']=($one+$two)/2;
                                            


                                }

                                if($about_level<6){



                                        $hello_sql="
                                                SELECT 
                                                        *    
                                                FROM `mssr_idc_book_sticker_level_info`
                                                WHERE `book_sid` ='{$array_output[$key]['book_sid']}'
                                                
                                                ORDER BY keyin_cdate
                                           

                                        ";

                                         $hello_result=db_result($conn_type='pdo',$conn_mssr,$hello_sql,array(),$arry_conn_mssr);
                                         foreach ($hello_result as $key => $value) {

                                             if($value['avg_level']==="0.00"){

                                                echo $value['book_sid'],"沒有等級";
                                                $book_sticker_sql="

                                                             UPDATE `mssr_idc_book_sticker_level_info` 
                                                             SET 
                                                                 `edit_by`='1',               
                                                                 `avg_level`='{$array_output[$key]['avg_level']}',
                                                                 `avg_status`='0',
                                                                 `keyin_mdate`=now()

                                                             WHERE book_sid='{$value['book_sid']}'
                                                                      
                                                ";

                                                
                                                $book_sticker_result=db_result($conn_type='pdo',$conn_mssr,$book_sticker_sql,array(),$arry_conn_mssr);

                                             }
                                             
                                         }

                            }

                                
                            } 


                        


                    }else{

                        echo "沒有";
                    }
                   
                    



            }



echo json_encode($array_output,true);
        
?>
