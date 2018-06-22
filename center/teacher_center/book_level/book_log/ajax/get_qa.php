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
        require_once(str_repeat("../",5).'config/config.php');

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

    $sess_user_id=$_SESSION['book_level_user_id'];
    $sess_permission=$_SESSION['book_level_permission'];
    $sess_name=$_SESSION['book_level_name'];
    

    if(!isset($sess_user_id)&&!isset($sess_permission)&&!isset($sess_name)){


        echo '<span style="font-size:40px; color:red;">請先登入!!</span>';

        header('Location:http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/index.php');


        die();


        
     }

     if($sess_permission==="2"){

            echo '<span style="font-size:40px; color:red;">你沒有權限進入!!</span>';

            die();
    }


    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

       

    // //---------------------------------------------------
    // //管理者判斷
    // //---------------------------------------------------

       

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

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------
            
            $sess_user_id=$_SESSION['book_level_user_id'];

            $sess_user_id=(int)$sess_user_id;

            $create_by   =(int)$sess_user_id;
            $edit_by     =(int)$sess_user_id;

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

        

        $sql="
                        SELECT 
                             `topic_id`,
                             `topic_title`,
                             `topic_type`,
                             `topic_options`,
                             `required_field`
                        FROM `mssr_idc_reading_log_topic`
                        WHERE `topic_use`= 1;
        ";



        $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
        // print_r($result);
        $array_output=array();
    
            if(!empty($result)){
                    
                    foreach($result as $key=>$arry_result){
                        
                        $array_output[$key]['topic_id']         =trim($arry_result['topic_id']);
                        $array_output[$key]['topic_title']      =trim($arry_result['topic_title']);
                        if(trim($arry_result['topic_type'])==1){
                            $array_output[$key]['topic_type'] ="text";
                        }else if(trim($arry_result['topic_type'])==2){
                            $array_output[$key]['topic_type'] ="radio";
                        }else if (trim($arry_result['topic_type'])==3) {
                             $array_output[$key]['topic_type'] ="checkbox";
                        }else{
                             $array_output[$key]['topic_type'] ="select";
                        }
                        
                        $array_output[$key]['topic_options']     =unserialize($arry_result['topic_options']);
                        if(trim($arry_result['required_field'])==0){
                            $array_output[$key]['required_field']="false";
                        }else{
                            $array_output[$key]['required_field']="true";
                        }
                       
                    
                    } 


                    // $array_output=new StdClass;
                    // $array_output->topic_id=$topic_id;
                    // $array_output->topic_title=$topic_title; 
                    // $array_output->topic_type=$topic_type; 
                    // $array_output->topic_options=$topic_options;  
                    // $array_output->required_field=$required_field; 
            
            }

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        echo json_encode($array_output,true);
?>