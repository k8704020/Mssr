<?php
    //connect to databaes begin
    if( !isset($con) )
    {
        $con = mysql_connect("pot-db.lst.ncu.edu.tw","potweb","M2etndstdrefSLX5");
        if (!$con)
        {
            echo "ERROR";
        }
        $sel_db=mysql_select_db("coach", $con);
        if(!$sel_db)
        {
            echo "ERROR";
        }
    }
//connect to database end
    if( isset($_GET["uid"]) ){
        $sql = mysql_query("SELECT * FROM `pptv_answer_list` WHERE (`unique_id` =".$_GET["uid"].") AND (`question_id` <= 125)");
        include ("grade.html");
    }else{
        header("Location: http://ncu.cot.org.tw/ac/index.php");
        exit;
    }
?>