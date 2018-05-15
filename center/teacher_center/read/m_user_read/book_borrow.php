<?php
//-------------------------------------------------------
//教師中心
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",4).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/db/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['center']['teacher_center']){
            $url=str_repeat("../",5).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        $arrys_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
        if(empty($arrys_login_info)){
            die();
        }

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

        if(in_array('read_the_registration_code',$config_arrys['user_area'])){
        //清空閱讀登記條碼版登入資訊

            $_SESSION['config']['user_tbl']=array();
            $_SESSION['config']['user_type']='';
            $_SESSION['config']['user_lv']=0;
            if(in_array('read_the_registration_code',$_SESSION['config']['user_area'])){
                foreach($_SESSION['config']['user_area'] as $inx=>$area){
                    if(trim($area)==='read_the_registration_code'){
                        unset($_SESSION['config']['user_area'][$inx]);
                    }
                }
            }
        }

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        $sess_login_info=(isset($_SESSION['tc']['t|dt']))?$_SESSION['tc']['t|dt']:array();

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

        if(!empty($sess_login_info)){
            if(!auth_check($db_type='mysql',$arry_conn_user,$sess_login_info['permission'],$auth_type='mssr_tc')){
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }
        }else{
            //權限指標
            $auth_flag=false;
            foreach($arrys_login_info as $inx=>$arry_login_info){
                if(auth_check($db_type='mysql',$arry_conn_user,$arry_login_info['permission'],$auth_type='mssr_tc'))$auth_flag=true;
            }
            if(!$auth_flag){
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }
        }

    //---------------------------------------------------
    //管理者判斷
    //---------------------------------------------------

        if(!empty($sess_login_info)){
            $is_admin=is_admin(trim($sess_login_info['permission']));
            if($is_admin){
                $sess_login_info['responsibilities']=99;
            }
        }

    //---------------------------------------------------
    //系統權限判斷
    //---------------------------------------------------
    //1     校長
    //3     主任
    //5     帶班老師
    //12    行政老師
    //14    主任帶一個班
    //16    主任帶多個班
    //22    老師帶多個班
    //99    管理者

        if(!empty($sess_login_info)){
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_read');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //GET
        $prompt=(isset($_GET['prompt']))?trim($_GET['prompt']):'no';
        $get_filter_semester_start=(isset($_GET['filter_semester_start']))?trim($_GET['filter_semester_start']):'';
        $get_filter_semester_end  =(isset($_GET['filter_semester_end']))?trim($_GET['filter_semester_end']):'';

        //SESSION
        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用

        if(isset($_SESSION['m_user_read']['filter'])){
            $filter=$_SESSION['m_user_read']['filter'];

            if(isset($_SESSION['m_user_read']['class_code'])&&(trim($_SESSION['m_user_read']['class_code'])!=='')){
                $q_class_code=mysql_prep(trim($_SESSION['m_user_read']['class_code']));

                $sql="
                    SELECT
                        `class`.`grade`,
                        `class`.`classroom`,
                        `class`.`class_category`
                    FROM `class`
                    WHERE 1=1
                        AND `class`.`class_code`='{$q_class_code}'
                ";
                $arrys_result=db_result($conn_type='pdo','',$sql,array(0,1),$arry_conn_user);
                if(!empty($arrys_result)){
                    $q_grade=(int)$arrys_result[0]['grade'];
                    $q_classroom=(int)$arrys_result[0]['classroom'];
                    $q_class_category=(int)$arrys_result[0]['class_category'];

                    //置換班級名稱
                    $get_class_code_info_single=get_class_code_info_single('',mysql_prep($sess_login_info['school_code']),(int)$q_grade,$q_classroom,$compile_flag=true,$arry_conn_user);
                    if(!empty($get_class_code_info_single)){
                        foreach($get_class_code_info_single as $inx=>$class_code_info_single){
                            if($q_class_category===(int)$class_code_info_single['class_category'])$new_q_classroom=trim($get_class_code_info_single[$inx]['classroom']);
                        }
                    }
                }
            }
        }
        if(isset($_SESSION['m_user_read']['query_fields'])){
            $query_fields=$_SESSION['m_user_read']['query_fields'];
        }

        if(isset($sess_login_info['uid'])){$sess_user_id=(int)$sess_login_info['uid'];}
        if(isset($sess_login_info['permission'])){$sess_permission=trim($sess_login_info['permission']);}
        if(isset($sess_login_info['school_code'])){$sess_school_code=trim($sess_login_info['school_code']);}
        if(isset($sess_login_info['responsibilities'])){
            $sess_responsibilities=(int)$sess_login_info['responsibilities'];
            if(in_array($auth_sys_check_lv,array(5,14,16,22))){
                $sess_class_code=trim($sess_login_info['arrys_class_code'][0]['class_code']);
                $sess_grade=(int)$sess_login_info['arrys_class_code'][0]['grade'];
                $sess_classroom=(int)$sess_login_info['arrys_class_code'][0]['classroom'];

                //置換班級名稱
                $get_class_code_info_single=get_class_code_info_single('',mysql_prep($sess_school_code),(int)$sess_grade,$sess_classroom,$compile_flag=true,$arry_conn_user);
                $new_sess_classroom=trim($get_class_code_info_single[0]['classroom']);
            }
        }

        if(isset($_SESSION['m_user_read']['class_code'])&&trim($_SESSION['m_user_read']['class_code'])!==''){
            $_SESSION['teacher_center']['class_code']=trim($_SESSION['m_user_read']['class_code']);
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //初始化, 選擇身份指標
        $choose_identity_flag=false;
        if(isset($sess_responsibilities)){
            $choose_identity_flag=true;
        }

        //目標年級
        $grade_goal=0;
        if(isset($sess_grade)){
            $grade_goal=$sess_grade;
        }
        if(isset($q_grade)){
            $grade_goal=$q_grade;
        }

        //目標班級
        $classroom_goal=0;
        if(isset($sess_classroom)){
            $classroom_goal=$sess_classroom;
        }
        if(isset($q_classroom)){
            $classroom_goal=$q_classroom;
        }

        //目標班級(轉換)
        $new_classroom_goal='';
        if(isset($new_sess_classroom)){
            $new_classroom_goal=$new_sess_classroom;
        }
        if(isset($new_q_classroom)){
            $new_classroom_goal=$new_q_classroom;
        }

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //串接SQL
    //---------------------------------------------------

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);


        //---------------------------------------------------
        //SQL查詢
        //---------------------------------------------------

//        $book_info=array(
//                             // array('book_sid' => 'mbc2142522018011211380805', 'borrow_sdate' => '2018-01-12 14:56:30'),
//                             array('isbn' => '9789577625489', 'borrow_sdate' => '2017-09-30 14:56:30'),
//                             array('isbn' => '9789866407093', 'borrow_sdate' => '2017-09-30 14:56:30'),
//                             array('isbn' => '9789862435984', 'borrow_sdate' => '2017-09-30 14:56:30'),
//                             array('isbn' => '9789862483497 ', 'borrow_sdate' => '2017-09-30 14:56:30'),
//                             array('isbn' => '9789866310942 ', 'borrow_sdate' => '2017-09-30 14:56:30'),
//                             array('isbn' => '9789573275473', 'borrow_sdate' => '2017-10-31 14:56:30'),
//                             array('isbn' => '4715443034135', 'borrow_sdate' => '2017-10-31 14:56:30'),
//                             array('isbn' => '9789864138166', 'borrow_sdate' => '2017-10-31 14:56:30'),
//                             array('isbn' => '9575501462', 'borrow_sdate' => '2017-10-31 14:56:30'),
//                             array('isbn' => '9789576427152', 'borrow_sdate' => '2017-10-31 14:56:30'),
                  
//                             array('isbn' => '9789866735684', 'borrow_sdate' => '2017-11-30 14:56:30'),
//                             array('isbn' => '9789869209182', 'borrow_sdate' => '2017-11-30 14:56:30'),
//                             array('isbn' => '9789865646073', 'borrow_sdate' => '2017-11-30 14:56:30'),
//                             array('isbn' => '9789864291977', 'borrow_sdate' => '2017-11-30 14:56:30'),
                   
//                             array('isbn' => '9789862169032', 'borrow_sdate' => '2017-12-31 14:56:30'),
//                             array('isbn' => '9789869304634 
// ', 'borrow_sdate' => '2017-12-31 14:56:30'),
//                             array('isbn' => '9789865806811', 'borrow_sdate' => '2017-12-31 14:56:30'),
                           



//                    );

              $book_info=array(
                            // array('book_sid' => 'mbc2142522018011211380805', 'borrow_sdate' => '2018-01-12 14:56:30'),
                            array('isbn' => '9789575884192', 'borrow_sdate' => '2017-08-30 14:56:30'),
                            array('isbn' => '9787544821759', 'borrow_sdate' => '2017-08-30 14:56:30'),
                            array('isbn' => '9789867116574 ', 'borrow_sdate' => '2017-08-30 14:56:30'),
                            array('isbn' => '9789867116376 ', 'borrow_sdate' => '2017-08-30 14:56:30'),
                            array('isbn' => '9789570835014', 'borrow_sdate' => '2017-09-30 14:56:30'),
                            array('isbn' => '9781132386484', 'borrow_sdate' => '2017-09-30 14:56:30'),
                            array('isbn' => '9780062104182', 'borrow_sdate' => '2017-09-30 14:56:30'),
                            array('isbn' => '9789864138999 ', 'borrow_sdate' => '2017-09-30 14:56:30'),
                            array('isbn' => '9789862035177 ', 'borrow_sdate' => '2017-10-31 14:56:30'),
                            array('isbn' => '9789862162460', 'borrow_sdate' => '2017-10-31 14:56:30'),
                            array('isbn' => '9570339950', 'borrow_sdate' => '2017-10-31 14:56:30'),
                            array('isbn' => '9781607185284', 'borrow_sdate' => '2017-11-30 14:56:30'),
                            array('isbn' => '9781862306950', 'borrow_sdate' => '2017-11-30 14:56:30'),
                            array('isbn' => '9780823423170', 'borrow_sdate' => '2017-11-30 14:56:30'),
                            array('isbn' => '9780983404613', 'borrow_sdate' => '2017-12-31 14:56:30'),
                            array('isbn' => '9780545392563 ', 'borrow_sdate' => '2017-12-31 14:56:30'),
                            array('isbn' => '9789861891781 ', 'borrow_sdate' => '2017-12-31 14:56:30'),
                            array('isbn' => '9789862292891 ', 'borrow_sdate' => '2017-12-31 14:56:30')
                           
                   );


            $no_data=array();


            foreach ($book_info as $key => $b_value) {

  

                    $isbn=$b_value['isbn'];

                    // print_r($book_id) ;

                     $borrow=$b_value['borrow_sdate'];

                     // print_r($borrow);

                     // die();


                      $query_sql="
                                             
                              SELECT book_sid
                              FROM mssr.mssr_book_global 
                              WHERE book_isbn_10='{$isbn}'
                              OR  book_isbn_13='{$isbn}'
                              LIMIT 1 

                      ";


                             
                       $db_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);

                       if(!empty($db_results)){



                                        foreach ($db_results as $key => $value) {

                                            // $student=$value['student'];

                                             $book_id=$value['book_sid'];

                                            $log_sql="
                                                           INSERT INTO `mssr_book_borrow_log`(
                                                           `user_id`, 
                                                           `book_sid`, 
                                                           `school_code`, 
                                                           `school_category`, 
                                                           `grade_id`, 
                                                           `classroom_id`, 
                                                           `borrow_sdate`, 
                                                           `borrow_edate`, 
                                                           `keyin_ip`
                                                           ) VALUES (
                                                           '214236',
                                                           '{$book_id}',
                                                           'idc',
                                                           '1',
                                                           '1',
                                                           '1',
                                                           '{$borrow}',
                                                           '{$borrow}',
                                                           ''
                                                       
                                                            ) 
                                                        
                                                        ";



                                                        // die();
                                                       $result=db_result($conn_type='pdo',$conn_mssr,$log_sql,array(),$arry_conn_mssr);




                                                       $select_sql="
                                                           SELECT log_id
                                                           FROM `mssr_book_borrow_log`
                                                           WHERE `user_id`= '214236'
                                                           AND `book_sid`='{$book_id}'
                                                           AND `school_code`='idc'
                                                           AND `borrow_sdate`='{$borrow}'
                                                           AND `borrow_edate`='{$borrow}'

                                                        
                                                        ";


                                                        $log_result=db_result($conn_type='pdo',$conn_mssr,$select_sql,array(),$arry_conn_mssr);

                                                        $log_id=$log_result[0]['log_id'];


                                              
                                                    $update_sql="

                                                          UPDATE `mssr_book_borrow_log` SET `borrow_sid`='{$log_id}'
                                                          WHERE log_id='{$log_id}'
                                                          AND  `user_id`= '214236'
                                                          AND `book_sid`='{$book_id}'
                                                          AND `school_code`='idc'
                                                          AND `borrow_sdate`='{$borrow}'
                                                          AND `borrow_edate`='{$borrow}'

                                                        
                                                        ";

                                                      

                                                        $update_result=db_result($conn_type='pdo',$conn_mssr,$update_sql,array(),$arry_conn_mssr);

                                                        


                                                    $semester_sql="
                                                           INSERT INTO `mssr_book_borrow_semester`(
                                                           `user_id`, 
                                                           `book_sid`, 
                                                           `school_code`, 
                                                           `school_category`, 
                                                           `grade_id`, 
                                                           `classroom_id`,
                                                           `borrow_sid`, 
                                                           `borrow_sdate`, 
                                                           `borrow_edate`, 
                                                           `keyin_ip`
                                                           ) VALUES (
                                                           '214236',
                                                           '{$book_id}',
                                                           'idc',
                                                           '1',
                                                           '1',
                                                           '1',
                                                           '{$log_id}',
                                                           '{$borrow}',
                                                           '{$borrow}',
                                                           ''
                                                           
                                                           
                                                           ) 
                                                        
                                                        ";

                                                    //     echo $semester_sql;

                                                    //     // die();
                                                       $result=db_result($conn_type='pdo',$conn_mssr,$semester_sql,array(),$arry_conn_mssr);



                                                 
                                                    



                                       

                                 




                                       }


                                                        


                       }else{



                            array_push($no_data, $isbn);


                       }

                 



            }

            print_r($no_data);
            
?>