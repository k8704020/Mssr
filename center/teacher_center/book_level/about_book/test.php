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
    

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------



    //---------------------------------------------------
    //管理者判斷
    //---------------------------------------------------

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

        //SESSION
      

    //---------------------------------------------------
    //設定參數
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

        //-----------------------------------------------
        //檢核借閱書學校關聯
        //-----------------------------------------------

           

        //---------------------------------------------------
        //SQL 筆數查詢
        //---------------------------------------------------

          
    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

   
        //echo $numrow."<br/>";

    //---------------------------------------------------
    //SQL 查詢
    //---------------------------------------------------

//echo "<Pre>";
//print_r($arrys_result);
//echo "</Pre>";
//die();
    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,教師中心";

        //-----------------------------------------------
        //教師中心路徑選單
        //-----------------------------------------------

          
?>
<?php
          //-----------------------------------------------
        //處理
        //-----------------------------------------------



        //======================
        //第二題題目選項(書的語言)
        //======================



        $book_sql="
              SELECT 
                   
                   
                    book_isbn_13

                                      
               FROM `mssr_idc_reading_log_spreadsheet` 
        
               ORDER BY book_isbn_13 DESC 
                        
        ";


echo $book_sql;

        $book_sql_result=db_result($conn_type='pdo',$conn_mssr,$book_sql,array(),$arry_conn_mssr);


        foreach ($book_sql_result as $key => $value) {
            // echo "key";
            // print_r($key);
            // echo "<br>";

                  
                  $array_output[$key]['book_isbn_13']        =trim($value['book_isbn_13']);
                 
                  //===================
                  //尋找書名
                  //===================
                   $sql="
                        SELECT 
                     
                             IFNULL(`book_sid`,0) as `book_sid` 
                             
                        FROM `mssr_book_class`
                        WHERE (`book_isbn_10` = '{$array_output[$key]['book_isbn_13']  }'
                        AND school_code='idc')
                        OR (`book_isbn_13` = '{$array_output[$key]['book_isbn_13']  }'
                        AND school_code='idc')

                        union 
                        
                        SELECT 
                        
                       
                            IFNULL(`book_sid`,0) as `book_sid`
                        
                             
                        FROM `mssr_book_library`
                        WHERE (`book_isbn_10` = '{$array_output[$key]['book_isbn_13']  }'
                        AND school_code='idc')
                        OR (`book_isbn_13` = '{$array_output[$key]['book_isbn_13']  }'
                        AND school_code='idc')
                        OR (`book_library_code` = '{$array_output[$key]['book_isbn_13']  }'
                        AND school_code='idc')


                        
                    ";



                    $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


                    if(!empty($result)){
                              
                             $array_output[$key]['book_sid'] =trim($result[0]['book_sid']);                    
                                    
                     }

                  $log_sql="

                        UPDATE `mssr_idc_reading_log_spreadsheet` SET `book_sid`='{$array_output[$key]['book_sid']}'
                        WHERE  book_isbn_13='{$array_output[$key]['book_isbn_13']}'
                                  
                   

                    ";
 
                    $log_result=db_result($conn_type='pdo',$conn_mssr,$log_sql,array(),$arry_conn_mssr);

                    // print_r($log_result);

                    
                
           
        }

?>
