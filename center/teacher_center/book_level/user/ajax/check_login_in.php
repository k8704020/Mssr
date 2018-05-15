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
            // $book_isbn="";
        

            // $sess_user_id=(int)$sess_user_id;

            // $create_by   =(int)$sess_user_id;
            // $edit_by     =(int)$sess_user_id;
            //  echo $sess_user_id;
            $account=trim($_REQUEST['account']);
            $password=trim($_REQUEST['password']);


            


//9789862167038

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

        //搜尋使用者資料

       

            $sql="
                        SELECT user_id,name,permission
                             
                        FROM `mssr_idc_member`
                        WHERE `account`='{$account}'
                        AND `password`='{$password}'


                     ";

 
            $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            $array_output=array();
    
            if(!empty($result)){
                    foreach($result as $key=>$arry_result){
                            $array_output[$key]['user_id']          =trim($arry_result['user_id']);
                            $array_output[$key]['name']             =trim($arry_result['name']);
                            $array_output[$key]['permission']       =trim($arry_result['permission']);
                            $_SESSION["user_id"]                    = trim($arry_result['user_id']);
                            $_SESSION["name"]                       = trim($arry_result['name']);
                            $_SESSION["permission"]                 = trim($arry_result['permission']);
                            
                    }
                    $array_output['msg'] ="";

            }else{
                        
   
                    $array_output['msg'] ="帳號或密碼有輸入錯誤";

             }
            




    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        echo json_encode($array_output,true);
?>