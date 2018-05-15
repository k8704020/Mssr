<?php        

    function hot(){

        global $arry_conn_mssr;
        global $conn_mssr;
        global $semester_start;
        global $semester_end ;



        $year          =date("Y");
        $month         =date("m");
        $date_now      =(int)date('j');
        $week_cno      =(int)(ceil($date_now/7)-1);
        $arry_date_week=date_week_array($year,$month);
        $week_sdate    =trim($arry_date_week[$week_cno]['sdate']);
        $week_edate    =trim($arry_date_week[$week_cno]['edate']);

        $week_sdate    =trim(date('Y-m-d', time()-86400*date('w')+(date('w')>0?86400:-6*86400)));
        $week_edate    =trim(date("Y-m-d",strtotime($week_sdate)+(86400*6)));

        // echo $week_sdate;
        // echo $week_edate;

        $sql="
            SELECT
                COUNT(`mssr_forum`.`mssr_forum_hot_booklist`.`book_sid`) AS `cno`,
                `mssr_forum`.`mssr_forum_hot_booklist`.`book_sid`
            
            FROM  `mssr_forum`.`mssr_forum_hot_booklist`
            WHERE 1=1
                AND `mssr_forum`.`mssr_forum_hot_booklist`.`keyin_cdate` BETWEEN '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
            GROUP BY `mssr_forum`.`mssr_forum_hot_booklist`.`book_sid`
            ORDER BY `cno` DESC
            
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

        return $db_results;
    
    }

    function teacher_uid($class_code){
        
        global $arry_conn_mssr;
        global $conn_mssr;


        $sql="
          SELECT * 
                FROM  `user`.`teacher` 
           WHERE 1=1
                AND `user`.`teacher`.`class_code`='$class_code'     
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
        if(empty($db_results)){
            return false;
        }
        
        return $db_results[0]['uid'];
         
    }

    function check_book($teacher_uid){
        
        global $arry_conn_mssr;
        global $conn_mssr;


        $sql="
          SELECT `book_sid` 
                FROM  `mssr_forum`.`mssr_forum_hot_booklist_discuss`
          WHERE `create_by` = $teacher_uid   

        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
        //echo $sql;
        return $db_results;
         
    }

  
    function countBook ($book_sid,$rs_uid){
        global $arry_conn_mssr;
        global $conn_mssr;
        global $semester_start;
        global $semester_end;

        if($rs_uid==""){
            return 0;
        }

        $sql ="
            SELECT 
                count(*)  as count
            FROM 
                `mssr_forum`.`mssr_forum_hot_booklist_discuss` 
            WHERE  
                `mssr_forum`.`mssr_forum_hot_booklist_discuss`.`book_sid` = '$book_sid' and  `mssr_forum`.`mssr_forum_hot_booklist_discuss`.`create_by`= $rs_uid
            AND `mssr_forum`.`mssr_forum_hot_booklist_discuss`.`keyin_cdate` BETWEEN '{$semester_start} 00:00:00' AND '{$semester_end} 23:59:59'
        ";
        
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
        
        if(empty($db_results)){
            return 0;
        }
             return $db_results[0]['count'];    

    }


?>      