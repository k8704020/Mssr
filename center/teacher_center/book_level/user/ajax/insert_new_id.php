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

   

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

 
            $name=trim($_REQUEST['name']);
            $sex=trim($_REQUEST['sex']);
            $permission=trim($_REQUEST['permission']);
            $new_account=trim($_REQUEST['new_account']);
            $new_password=trim($_REQUEST['new_password']);


            


//9789862167038

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

        //搜尋使用者資料

       

            $sql="
                        INSERT INTO `mssr_idc_member`
                        (
                        `name`, 
                        `sex`, 
                        `account`, 
                        `password`, 
                        `permission`, 
                        `build_time`
                        ) 
                        VALUES (
                        '{$name}',
                        '{$sex}',
                        '{$new_account}',
                        '{$new_password}',
                        '{$permission}',
                        now()
                        )



                     ";

 
            $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


            $sql1="
                        SELECT user_id,name,permission
                             
                        FROM `mssr_idc_member`
                        WHERE `account`='{$new_account}'
                        


                     ";

 
            $result1=db_result($conn_type='pdo',$conn_mssr,$sql1,array(),$arry_conn_mssr);

            $array_output=array();
    
            if(!empty($result1)){

              
                    foreach($result as $key=>$arry_result){
                            $array_output[$key]['user_id']         =trim($arry_result['user_id']);
                            $array_output[$key]['name']      =trim($arry_result['name']);
                            $array_output[$key]['permission']       =trim($arry_result['permission']);
                            
                    }
                    
       
                 

            }



           



    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------


    echo json_encode($array_output,true);

   
?>