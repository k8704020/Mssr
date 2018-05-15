<?php
//-------------------------------------------------------
//教師中心
//-------------------------------------------------------
// echo "hello";
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

        if($config_arrys['is_offline']['center']['teacher_center']){
            $url=str_repeat("../",4).'index.php';
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

    // //---------------------------------------------------
    // //管理者判斷
    // //---------------------------------------------------

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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_rec');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //user_id    使用者主索引(被閱讀人)
    //book_sid   書籍識別碼
    //flag       閱讀結果指標
    //ajax_cno   閱讀數指標


    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //user_id    使用者主索引(被閱讀人)
    //book_sid   書籍識別碼
    //flag       閱讀結果指標
    //ajax_cno   閱讀數指標

        //POST
        // $user_id =trim($_POST[trim('user_id ')]);
        // $book_sid=trim($_POST[trim('book_sid')]);
        // $flag    =trim($_POST[trim('flag    ')]);
        // $ajax_cno=trim($_POST[trim('ajax_cno')]);

        //SESSION
        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);
        $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
        $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
        $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];

        //分頁
        // $psize=(isset($_POST['psize']))?(int)$_POST['psize']:10;
        // $pinx =(isset($_POST['pinx']))?(int)$_POST['pinx']:1;
        // $psize=($psize===0)?10:$psize;
        // $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------


    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------
            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $sess_user_id=(int)$sess_user_id;

            $create_by   =(int)$sess_user_id;
            $edit_by     =(int)$sess_user_id;
      


        //-----------------------------------------------
        //處理
        //-----------------------------------------------

        $array_output=array();
        $topic_options=array();
        $array_book_sid=array();


        //======================
        //第二題題目選項(書的語言)
        //======================

        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                     WHERE topic_id ='2'

                ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic2_options']=unserialize($value['topic_options']);
            }
            
            // print_r($topic_options[$key]['topic3_options'][0]);
         }

        //======================
        //第三題題目選項(是否有注音)
        //======================
        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                    WHERE topic_id ='3'

                ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic3_options']=unserialize($value['topic_options']);
            }
            
            // print_r($topic_options[$key]['topic3_options'][0]);
         }

        //======================
        //第四題題目選項(大主題)
        //======================

        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                    WHERE topic_id ='4'

                 ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic4_options']=unserialize($value['topic_options']);
            }
            
           
         }

        //======================
        //第五題題目選項(中主題)
        //======================

        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                    WHERE topic_id ='5'

                 ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic5_options']=unserialize($value['topic_options']);
            }
            
           
        }
        //======================
        //第六題題目選項(小主題)
        //======================

        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                    WHERE topic_id ='6'

                 ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic6_options']=unserialize($value['topic_options']);
            }
            
           
        }

        //======================
        //第十四題題目選項(難度等級)
        //======================

        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                    WHERE topic_id ='14'

                 ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic14_options']=unserialize($value['topic_options']);
            }
            
           
         }

//----------------------------------------------------------

        //======================
        //表單登記的書
        //======================


        $book_sql="
              SELECT 
                    book_sid,
                    book_isbn_10,
                    book_isbn_13,
                    book_library_code
                                      
               FROM `mssr_idc_reading_log_spreadsheet` 
               GROUP BY  `book_sid` 
               ORDER BY book_sid DESC 
                        
        ";

        $book_sql_result=db_result($conn_type='pdo',$conn_mssr,$book_sql,array(),$arry_conn_mssr);


        foreach ($book_sql_result as $key => $value) {
            // echo "key";
            // print_r($key);
            // echo "<br>";

                  $array_output[$key]['book_sid']          =trim($value['book_sid']);
                  $array_output[$key]['book_isbn_13']        =trim($value['book_isbn_13']);
                  $array_output[$key]['book_isbn_10']        =trim($value['book_isbn_10']);
                  $array_output[$key]['book_library_code']   =trim($value['book_library_code']);
               
                  //===================
                  //尋找書名
                  //===================
                   $sql="
                        SELECT 
                     
                             IFNULL(`book_name`,0) as `book_name` 
                             
                        FROM `mssr_book_class`
                        WHERE `book_sid` = '{$array_output[$key]['book_sid']  }'
                        AND school_code='test'

                        union 
                        
                        SELECT 
                        
                       
                        IFNULL(`book_name`,0) as `book_name`
                        
                             
                        FROM `mssr_book_library`
                         WHERE `book_sid` = '{$array_output[$key]['book_sid']  }'
                        AND school_code='test'


                        
                    ";

                    $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


                    if(!empty($result)){
                              
                             $array_output[$key]['book_name'] =trim($result[0]['book_name']);                    
                                    
                     }

                  $log_sql="

                        SELECT 
                                user_id,
                                book_language,
                                bopomofo, 
                                major_topic,
                                sub_topic,
                                minor_topic,
                                tag1,
                                tag2,
                                tag3,
                                tag4,
                                tag5,
                                pages,
                                words,
                                hard_level,
                                lexile_level
                                      
                        FROM `mssr_idc_reading_log_spreadsheet`
                        WHERE `book_sid`='{$value['book_sid']}'             
                        order by book_sid desc

                    ";


                    $log_result=db_result($conn_type='pdo',$conn_mssr,$log_sql,array(),$arry_conn_mssr);




                    if(!empty($log_result)){
                            
                            foreach ($log_result as $index_key => $array_log) {
                                      //   echo "<br>";
                                      //  echo "index_key:";
                                      // print_r($index_key);
                                      //   echo "<br>";
                                    $array_output[$key]['book_language'][$index_key] =trim($array_log['book_language']);
                                    $array_output[$key]['bopomofo'][$index_key] =trim($array_log['bopomofo']);
                                    $array_output[$key]['tag1'][$index_key] =trim($array_log['tag1']);
                                    $array_output[$key]['tag2'][$index_key]=trim($array_log['tag2']);
                                    $array_output[$key]['tag3'][$index_key]=trim($array_log['tag3']);
                                    $array_output[$key]['tag4'][$index_key]=trim($array_log['tag4']);
                                    $array_output[$key]['tag5'][$index_key]=trim($array_log['tag5']);
                                    $array_output[$key]['pages'][$index_key]=trim($array_log['pages']);
                                    $array_output[$key]['words'][$index_key]=trim($array_log['words']);
                                    $array_output[$key]['lexile_level'][$index_key] =trim($array_log['lexile_level']);
                                       
                                        // ======================
                                        // 尋找誰寫出這筆資料 
                                        // ======================
                                         
                                    $user_sql="
                                                SELECT name
                                                FROM  `member`  
                                                WHERE  uid='{$array_log['user_id']}'   
                                                " ; 
                                    $user_result=db_result($conn_type='pdo',$conn_user,$user_sql,array(),$arry_conn_user);
                                    if(!empty($user_result)){

                                            $array_output[$key]['name'][$index_key]=trim($user_result[0]['name']);
                                                                       
                                    }else{

                                            $array_output[$key]['name'][$index_key]="0";
                                                

                                    }


                                    // // //======================
                                    // // //尋找貼紙編號及貼紙顏色
                                    // // //======================

                                    $sticker_sql="
                                                SELECT `sticker_color`,`sticker_number` 
                                                FROM `mssr_idc_book_sticker_level_info`
                                                WHERE book_sid='{$array_output[$key]['book_sid']}'

                                    ";

                                    $sticker_result=db_result($conn_type='pdo',$conn_mssr,$sticker_sql,array(),$arry_conn_mssr);
                                        
                                    if(!empty($sticker_result)){
                                          

                                                $array_output[$key]['sticker_number']=trim($sticker_result[0]['sticker_number']);
                                                $sticker_color_sql="
                                                    SELECT color
                                                    FROM  `mssr_idc_book_sticker_color`
                                                    WHERE  color_id='{$sticker_result[0]['sticker_color']}'   
                                                " ; 

                                                $sticker_color_result=db_result($conn_type='pdo',$conn_mssr,$sticker_color_sql,array(),$arry_conn_mssr);
                                                if(!empty($sticker_color_result)){

                                                        $array_output[$key]['sticker_color']=trim($sticker_color_result[0]['color']);
                                                }else{
                                                    $array_output[$key]['sticker_color']="0";
                                                }
                                    }else{
                                            $array_output[$key]['sticker_color']="0";
                                            $array_output[$key]['sticker_number']="0";
                                    }


                                    // //======================
                                    // //書本的語言
                                    // //======================
                                    $array_output[$key]['book_language'][$index_key]=$topic_options[0]['topic2_options'][$array_log['book_language']];

                                    // //======================
                                    // //是否有注音
                                    // //======================
                                    $array_output[$key]['bopomofo'][$index_key]=$topic_options[0]['topic3_options'][$array_log['bopomofo']];
                                   
                                    //======================
                                    //書本的大主題
                                    //======================
                                    if($array_log['major_topic']){
                                        $major_topic=explode(",",$array_log['major_topic']);
                                        
                                        foreach ($major_topic as $index => $array_major) {

                                            $major_topic[$index]=$topic_options[0]['topic4_options'][$array_major];
                                            $array_output[$key]['major_topic'][$index_key]=$major_topic;

                                        }
                                    }

                                    //======================
                                    //書本的中主題
                                    //======================
                                    if($array_log['sub_topic']){
                                        $sub_topic=explode(",",$array_log['sub_topic']);
                                        
                                        foreach ($sub_topic as $index => $array_major) {

                                            $sub_topic[$index]=$topic_options[0]['topic5_options'][$array_major];
                                            $array_output[$key]['sub_topic'][$index_key]=$sub_topic;

                                        }
                                    }
                                    // //======================
                                    // //書本的小主題
                                    // //======================
                  
                                    if($array_log['minor_topic']){
                                            $minor_topic=explode(",",$array_log['minor_topic']);
                                            foreach ($minor_topic as $index => $array_minor) {

                                                $minor_topic[$index]=$topic_options[0]['topic6_options'][$array_minor];
                                                $array_output[$key]['minor_topic'][$index_key]=$minor_topic;

                                            }
                                     }
                                    // //======================
                                    // //單人等級
                                    // //======================
                                    if($array_log['hard_level']){
                                        $hard_level=explode(",",$array_log['hard_level']);
                                        foreach ($hard_level as $index => $array_hard) {

                                            $hard_level[$index]=$topic_options[0]['topic14_options'][$array_hard];
                                            $array_output[$key]['hard_level'][$index_key]=$hard_level;

                                        }
                                    }

                                    // //======================
                                    // //平均等級
                                    // //======================

                                     


                                    if($array_output[$key]['book_sid']){



                                                $book_hard_sql="
                                                                 SELECT `avg_level` 
                                                                 FROM `mssr_idc_book_sticker_level_info` 
                                                                 WHERE  `book_sid` ='{$array_output[$key]['book_sid']}'           
                                                                
                                                ";
                                                $book_hard_result=db_result($conn_type='pdo',$conn_mssr,$book_hard_sql,array(),$arry_conn_mssr);

                                                if(!empty($book_hard_result)){
                                                        
                                                     $array_output[$key]['avg_level']=$book_hard_result[0]['avg_level'];

                                                }else{
                                                    

                                                       
                                                        $hard_sql="

                                                             SELECT hard_level                                  
                                                             FROM `mssr_idc_reading_log_spreadsheet` 
                                                             WHERE book_sid='{$array_output[$key]['book_sid']}'           
                                                             ORDER BY book_sid desc
                                                            
                                                        ";
                                                        $hard_result=db_result($conn_type='pdo',$conn_mssr,$hard_sql,array(),$arry_conn_mssr);
                                                     
                                                        // $people_level=0;
                                                        $level= array(); 
                                
                                                        if(count($hard_result)>1){

                                                                foreach ($hard_result as $index_key => $array_book_hard) {
                                                          
                                                                        $hard_level[$index_key]=explode(",",$array_book_hard['hard_level']);
                                                                        if($hard_level[$index_key][0]&&$hard_level[$index_key][1]){
                                                                            $level=(int)$hard_level[$index_key][0]+(int)$hard_level[$index_key][1];
                                                                            $one_person=$level/2;
                                                                            // echo $one_person;
                                                                          }    

                                                                    // $people_level+= $one_person;   
                                                                    $arr=array_push($level,$one_person);
                                                                    
                                                                }

                                                                print_r($arr);
                                                            //     $a=(float)$people_level[0];
                                                            //     $b=(float)$people_level[1];

                                                            //     echo $a;
                                                            //     echo $b;
                                                            //     if($a>$b){

                                                            //         $hw=$a-$b;

                                                            //      }else{

                                                            //         $hw=$b-$a;
                                                            //      }


                                                                       
                                                            //     $ww=$b+$a;
                                                                // $avg_level=$people_level;


                                                            // print_r($people_level);     
                                                        
                                                        }else{

                                                                foreach ($hard_result as $index_key => $array_book_hard) {
                                                              
                                                                            $hard_level[$index_key]=explode(",",$array_book_hard['hard_level']);
                                                                            if($hard_level[$index_key][0]&&$hard_level[$index_key][1]){
                                                                                $level=(int)$hard_level[$index_key][0]+(int)$hard_level[$index_key][1];
                                                                                $one_person=$level/2;
                                                                              }
                                                                $avg_level=$one_person;
                                                     

                                                                }
                                                        }



                                                        // // if( $hw >= 6){
                                                                     
                                                        // //     echo "1";

                                                        // // }else{

                                                        // //    echo "0";
                                                        // // }

                                                        $array_output[$key]['avg_level']="$avg_level";


                                                        $sql="
                                                            
                                                                SELECT * 
                                                                FROM `mssr_idc_book_sticker_level_info` 
                                                                WHERE book_sid='{$array_output[$key]['book_sid']}'
                                                        ";

                                                        $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                                     
                                                        if(!empty($result)){

                                                            $book_sticker_sql="

                                                                         UPDATE `mssr_idc_book_sticker_level_info` 
                                                                         SET `avg_level`='{$avg_level}'
                                                                         WHERE book_sid='{$array_output[$key]['book_sid']}'
                                                                                  
                                                            ";
                                                            $book_sticker_result=db_result($conn_type='pdo',$conn_mssr,$book_sticker_sql,array(),$arry_conn_mssr);


                                                         }else{

                                                            $book_sticker_sql="
                                                                         INSERT INTO `mssr_idc_book_sticker_level_info`(
                                                                         `edit_by`, 
                                                                         `user_id`, 
                                                                         `book_sid`, 
                                                                         `sticker_id`, 
                                                                         `sticker_color`, 
                                                                         `sticker_number`, 
                                                                         `avg_level`, 
                                                                         `keyin_cdate`, 
                                                                         `keyin_mdate`
                                                                         ) 
                                                                         VALUES 
                                                                         ('0',
                                                                         '0',
                                                                         '{$array_output[$key]['book_sid']}',
                                                                         '0',
                                                                         '0',
                                                                         '0',
                                                                         '{$avg_level}',
                                                                         NOW(),
                                                                         NOW()
                                                                         );

                                                                        
                                                            ";
                                                            $book_sticker_result=db_result($conn_type='pdo',$conn_mssr,$book_sticker_sql,array(),$arry_conn_mssr);


                                                        }
                                                }

                                                
                                    }

                                            
                                

                                    
                                   


                            }
                              
                                
                    
                }
                
           
        }


       


    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        echo json_encode($array_output,1);
?>