<?php   

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",6).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/date/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();


//-----------------------------------------------
//資料庫
//-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);        


            $year          =date("Y");
            $month         =date("m");
            $date_now      =(int)date('j');
            $week_cno      =(int)(ceil($date_now/7)-1);
            $arry_date_week=date_week_array($year,$month);
            $week_sdate    =trim($arry_date_week[$week_cno]['sdate']);
            $week_edate    =trim($arry_date_week[$week_cno]['edate']);
            
            
            if(isset($_GET['book_sid'])){
                $book_sid = $_GET['book_sid'];
            }else{
                        $msg='
                            <script>
                                alert("書籍不存在");
                                history.back(-1);
                            </script>
                        ';
                        die($msg);
            }
            if(isset($_GET['tid'])){
                $tid = $_GET['tid'];
            }else{
                        $msg='
                            <script>
                                alert("不是正確的uid");
                                history.back(-1);
                            </script>
                        ';
                        die($msg);
            }

            

            $week_sdate    =trim(date('Y-m-d', time()-86400*date('w')+(date('w')>0?86400:-6*86400)));
            $week_edate    =trim(date("Y-m-d",strtotime($week_sdate)+(86400*6)));
            
            $sql="      
                DELETE FROM `mssr_forum`.`mssr_forum_hot_booklist_discuss` WHERE 
                    `book_sid` ='$book_sid' and `create_by` = $tid
                AND `mssr_forum`.`mssr_forum_hot_booklist_discuss`.`keyin_cdate` BETWEEN '{$week_sdate} 00:00:00' AND '{$week_edate} 23:59:59'
            ";

            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            //echo $sql;
               
            $msg='
                    <script>
                        history.back(-1);
                    </script>
                ';
            die($msg);
        



?>