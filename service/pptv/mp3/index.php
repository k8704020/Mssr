<?php
//connect to databaes begin
    if( !isset($con) )
    {
        $con = mysql_connect("db.dev.cot.org.tw","potweb","M2etndstdrefSLX5");
        $sel_db=mysql_select_db("coach", $con);
    }
//connect to database end

//include common.js
echo "<script type=\"text/javascript\" src=\"common.js\"></script>";

// if login?
    if( isset($_COOKIE["id"]) )
    {
        $uid = $_COOKIE["unique_id"];
        $grade = $_COOKIE["grade"];
        $school = $school=iconv("big5","UTF-8",$_COOKIE["school"]);
        $nickname = $nickname=iconv("big5","UTF-8",$_COOKIE["nickname"]);
    }else{
        echo "<a href=\"http://ncu.cot.org.tw/ac/index.php\">點此回到首頁</a><br>";
		if( $_COOKIE["identity"] == 'T' || $_COOKIE["identity"] == 'A')
			echo "<a href=\"mange.php\">進入成績檢視頁面</a>";
		
        exit;
    }
//personal data init
    if( isset($_POST["question"]) ){
        $first_question =$_POST["first_question"];
        $latest_question=$_POST["latest_question"];
        $high_level = $_POST["high_level"];
        $low_level = $_POST["low_level"];
        $score =$_POST["score"];
    }
    if( !isset($first_question)){
        $sql = mysql_query("SELECT * FROM `pptv_score` WHERE `unique_id`=".$uid);
        $result = mysql_affected_rows();
        // first test because there is not data in DB
        if( $result == 0){
            call_js_function('first_test()');
			$datetime = date ("Y-m-d H:i:s");
            mysql_query("INSERT INTO `pptv_score`(`unique_id`, `first_question`, `latest_question`, `high_level`, `low_level`, `score`,`first_time`)
                     VALUES (".$uid.",126,-1,-1,-1,-1,'".$datetime."')");
            $first_question =126;
            $latest_question=-1;
            $high_level = -1;
            $low_level = -1;
            $score =-1;
        }else
        {// not first
            $row = mysql_fetch_row($sql);
            if( $row[5] != -1){
                    call_js_function('end('.$row[5].')');
                    echo "<a href=\"http://ncu.cot.org.tw/ac/index.php\">點此回到首頁</a>";
                    exit;
            }
            call_js_function('latest_qu('.$row[2].')');
            $first_question = $row[1];
            $latest_question= $row[2];
            $high_level = $row[3];
            $low_level = $row[4];
            $score = $row[5];
            $question = $latest_question;
        }
    }
//personal data init end
//set ans
if ( !isset($correct_ans)){
    $correct_ans = Array(4,3,3,1,2, 4,4,3,4,2, 4,3,4,2,4, 1,1,2,4,2,
                         4,2,4,1,2, 3,4,1,1,4, 4,3,2,2,4, 3,1,2,2,2,
                         1,3,3,2,1, 1,1,1,4,2, 2,4,2,4,1, 1,4,4,3,3,
                         4,1,1,1,3, 3,1,2,3,1, 4,1,1,2,2, 2,1,3,2,4,
                         3,2,4,4,2, 4,2,2,3,4, 3,2,3,3,3, 1,1,3,3,2,
                         4,3,4,3,3, 4,2,3,1,3, 2,4,1,2,2, 1,4,2,1,3,
                         1,3,4,4,1, 4,2,3,4,1);
    $user_first_qusetion= Array(0,30,40,55,65,70);
}
//set ans end
//question
    if( isset($_POST["answer"]) )
    {
        $question = $_POST["question"];
        $latest_question = $question;
        $sql = mysql_query("SELECT * FROM `pptv_answer_list` WHERE (`unique_id`=".$uid.") AND (`question_id`=".$question.")");
        while( $result = mysql_fetch_array($sql))
        {
            header("Location: index.php");
            exit;
        }
        if($question == 130){
            $first_question = $user_first_qusetion[$grade];
        }
        //update pptv_answer_list
        if( $_POST["answer"] == $correct_ans[$question-1]){
            $result = 1;
        }else{
            $result = 0;
        }
        mysql_query("INSERT INTO `pptv_answer_list`(`unique_id`, `question_id`,`correct`)
                     VALUES (".$uid.",".$question.",".$result.")");
        //check low_level
        if($latest_question == 1){
            $low_level = 1;
        }else{
            if( check_low_level($uid,$latest_question) == 1 && ($low_level ==-1 || $low_level<$latest_question)){
                $low_level = $latest_question;
            }
        }
        //check high_level
        if($question == 125){
            $high_level = 125;
        }else{
            if( check_high_level($uid,$latest_question) == 1 && ($high_level ==-1 || $high_level>$latest_question)){
                $high_level = $latest_question;
                if($latest_question < $first_question)
                {
                    $high_level+=7;
                }
            }
        }
        //check end
        if( ($low_level != -1) && ($high_level != -1) ){
            mysql_query("SELECT * FROM `pptv_answer_list` WHERE (`unique_id` = ".$uid.") AND 
                                                (`question_id`>=".$low_level.") AND 
                                                (`question_id`<=".$high_level.") AND
                                                (`correct` = 0)");
			$num = mysql_affected_rows();
            $score = $high_level-$num;
        }
        //update pptv_score
        mysql_query("UPDATE `pptv_score` SET `unique_id`=".$uid.", `first_question`=".$first_question.",
                    `latest_question`=".$latest_question.", `high_level`=".$high_level.",
                    `low_level`=".$low_level.", `score`=".$score."
                    WHERE `unique_id`=".$uid);
		if($question == 130){
			call_js_function('begin()');
			header("Location: begin.html");
			exit;
		}
        if( $score != -1){
            call_js_function('end('.$score.')');
            echo "<a href=\"http://ncu.cot.org.tw/\">點此回到首頁</a>";
            exit;
        }
    }
    if($first_question==126){
            if($latest_question == -1){
                $question = 126;
            }
            else{
                $question = $latest_question +1;
            }
    }else if($latest_question == 130){
                $question = $first_question;
    }else{
        if($first_question == $latest_question){
                $sql = mysql_query("SELECT * FROM `pptv_answer_list` WHERE 
                                    (`question_id`=".$first_question.") AND (`unique_id`=".$uid.")");
                $row = mysql_fetch_row($sql);
                if($row[3]==1){
                    $question = $first_question+1;
                }else{
                    $question = $first_question-1;
                }
        }else if($first_question < $latest_question){
                if( $high_level != -1){
                    $sql = mysql_query("SELECT * FROM `pptv_answer_list` WHERE `unique_id`=".$uid." ORDER BY `question_id` ASC");
                    $row = mysql_fetch_row($sql);
                    $question = $row[2]-1;
                }else{
                    $question = $latest_question+1;
                }
        }else if($first_question > $latest_question){
                if( $low_level != -1){
                    $sql = mysql_query("SELECT * FROM `pptv_answer_list` WHERE `unique_id`=".$uid." ORDER BY `question_id` DESC");
                    $row = mysql_fetch_row($sql);
                    $question = $row[2]+1;
                }else{
                    $question = $latest_question-1;
                }
        }
    }
//function
function check_low_level($uid,$question){
    $count = 0;
    $sql = mysql_query("SELECT * FROM `pptv_answer_list` WHERE (`unique_id`=".$uid.") AND (`question_id`<=".$question."+7) AND (`question_id`>=".$question."-7)");
    while( $row = mysql_fetch_row($sql)){
        if($row[3]==1){ $count++; }
    }
    if( $count == 8) { return 1;}
    else { return 0; }
}
function check_high_level($uid,$question){
    $count = 0;
    $sql = mysql_query("SELECT * FROM `pptv_answer_list` WHERE (`unique_id`=".$uid.") AND (`question_id`<=".$question."+7) AND (`question_id`>=".$question."-7)");
    while( $row = mysql_fetch_row($sql)){
        if($row[3]==0){ $count++; }
    }
    if( $count >= 6) { return 1;}
    else { return 0; }
}
function call_js_function($function_name){
    echo '<script type="text/javascript">'
   , $function_name.';'
   , '</script>';
}
function question_number($question){
	$arr = Array("甲","乙","丙","丁","戊");
	if($question >= 126){
		echo $arr[$question-126];
	}
}
//question end
 include("index1.html");
?>