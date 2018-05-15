<?php


//---------------------------------------------------
//設定與引用
//---------------------------------------------------

	//SESSION
	@session_start();

	//啟用BUFFER
	@ob_start();

	//外掛設定檔
	require_once(str_repeat("../",2)."/config/config.php");
	//外掛函式檔
	$funcs=array(
				APP_ROOT.'inc/code',
				APP_ROOT.'lib/php/db/code',
				APP_ROOT.'lib/php/array/code'
				);
	func_load($funcs,true);


	//清除並停用BUFFER
	@ob_end_clean();

	//建立連線 user
	$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
	$conn_user=conn($db_type='mysql',$arry_conn_user);
	?>
	<script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript" src="../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
    <script src="../../../ac/js/user_log.js"></script>
    <?php

//---------------------------------------------------
//END   設定與引用
//---------------------------------------------------

	//資料初始化
	 $uid= "";
	 $grade = "";
     $school = $school="";
     $nickname = $nickname="";
	 $class ="";
	 $class_code = "";
	 $class_category="";
	 $class_name = "";
	 $stat = "";
     //$_SESSION["uid"] = '5030';
	 //---------------------------------------------------
	//判斷是否已登入
	//---------------------------------------------------
	if( isset($_SESSION["uid"]) )
    {

		$uid = $_SESSION["uid"];


        $sql = "
                select
                     mssr.mssr_auth_user.auth
                from
                     mssr.mssr_auth_user

                where 1 = 1
                    and user_id = '$uid'
               ";

        $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr); //執行sql反回數組

        $auth = $retrun[0]['auth'];
        $auth =  unserialize($auth);


        



        if(empty($auth['pptv_coda']) or $auth['pptv_coda'] == 0){

            call_js_function('noTest()');
            exit;
        }





		//搜尋老師或班級的class_code
		$sql = "
                select
                    class_code
				from
                    student
                where 1 = 1
                    and  uid   ='$uid'
                    and  start <= NOW()
                    and  end   >= NOW()
				";

		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user); //執行sql反回數組



        if(count($retrun) >0 )
		{
			$stat = "S";
			$class = $class_code = $retrun[0]['class_code'];//test_2014_1_1_1
		}
		$sql = "
                select
                    class_code
				from
                    teacher
                where 1 = 1
                    and  uid    ='$uid'
                    and  start <= NOW()
                    and  end   >= NOW()
			   ";
		$retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);

        if(count($retrun) >0 )
		{
			$stat = "T";
			$class = $class_code = $retrun[0]['class_code']; //test_2014_1_1_1
		}

		//如有班級權限則搜尋該班年籍資料
		if($stat!="")
		{
			$sql = "select * from  class where class_code = '$class_code'";
            $retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);




			$class_category = $retrun[0]["class_category"];
			$grade = $retrun[0]["grade"];
			$classroom = $retrun[0]["classroom"];

            $sql = "
                    select
                           class_name
					from
                           class_name
					where  1=1
                           and class_category = '$class_category'
                           and classroom = '$classroom'
                   ";

            $retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
            $class_name = $retrun[0]["class_name"];


          }



		//搜尋該身分
		$sql = "select * from  member where uid ='$uid'";
        $retrun = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
        $nickname = $retrun[0]['name'];



		$tmp = explode("_",$class); //拆成陣列
		$school = $tmp[0]; //取出學校




	}

	//---------------------------------------------------
	//---------------------------------------------------

	 //---------------------------------------------------
	//personal data init
	//---------------------------------------------------
    //echo  '上一題題目= ',$_POST["question"],'<br/>';

    if( isset($_POST["question"]) ) //如果post存在
    {
        $first_question  =  $_POST["first_question"];
        $latest_question =  $_POST["latest_question"];
        $high_level      =  $_POST["high_level"];
        $low_level       =  $_POST["low_level"];
        $score           =  $_POST["score"];
    }




    if( !isset($first_question)){ //如果第一題不存在

        $sql = "select * from pptv_score where unique_id = $uid order by first_time desc";
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);






        // first test because there is not data in DB
        if( $retrun[0]["score"] != -1 ||  $retrun[0]["is_show"] == 2){   //count($retrun) == 0

            call_js_function('first_test()');



            $datetime = date ("Y-m-d H:i:s");
            $sql = "
                    insert into
                            pptv_score
                        (
                            unique_id,
                            first_question,
                            latest_question,
                            high_level,
                            low_level,
                            score,
                            first_time
                        )
                     VALUES ('$uid',126,-1,-1,-1,-1,now())";


			db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);


            $first_question    = 126;
            $latest_question   = -1;
            $high_level        = -1;
            $low_level         = -1;
            $score             = -1;




        }else
        {



            call_js_function('latest_qu('.$retrun[0]["latest_question"].')');
            $first_question = $retrun[0]["first_question"];
            $latest_question= $retrun[0]["latest_question"];
            $high_level = $retrun[0]["high_level"];
            $low_level = $retrun[0]["low_level"];
            $score = $retrun[0]["score"];
            $question = $latest_question;

        }

    }


	//---------------------------------------------------
	//---------------------------------------------------

	//---------------------------------------------------
	//personal data init end
	//set ans
	//---------------------------------------------------
	if ( !isset($correct_ans)){
		$correct_ans = Array(4,3,3,1,2, 4,4,3,4,2, 4,3,4,2,4, 1,1,2,4,2,
							 4,2,4,1,2, 3,4,1,1,4, 4,3,2,2,4, 3,1,2,2,2,
							 1,3,3,2,1, 1,1,1,4,2, 2,4,2,4,1, 1,4,4,3,3,
							 4,1,1,1,3, 3,1,2,3,1, 4,1,1,2,2, 2,1,3,2,4,
							 3,2,4,4,2, 4,2,2,3,4, 3,2,3,3,3, 1,1,3,3,2,
							 4,3,4,3,3, 4,2,3,1,3, 2,4,1,2,2, 1,4,2,1,3,
							 1,3,4,4,1, 4,2,3,4,1);

        $user_first_qusetion= Array(0,30,40,55,65,70,75,80); //起始題目
	}
	//---------------------------------------------------



	//set ans end
	//--------------------------------------------------
    //echo '上一題答案= ',$_POST["answer"],'<br/>'; //126

    if( isset($_POST["answer"]) ) //4
    {
        $sql = "select * from pptv_score where unique_id = $uid order by first_time desc";
        $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

        $first_time = $retrun[0]['first_time'];

        $question = $_POST["question"];
        $latest_question = $question;
        $sql = "
                select
                        *
                from
                        pptv_answer_list
                where   1=1
                        and  unique_id   = $uid
                        and  question_id = $question
                        and  time        > '$first_time'

                ";

		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

//echo $sql;


        foreach($retrun as $key1=>$val1)
        {
           header("Location: index.php");
           exit;
        }

        if($question == 130){
            $first_question = $user_first_qusetion[$grade];
        }

        //update pptv_answer_list
        //4    == 29
        if( $_POST["answer"] == $correct_ans[$question-1]){ //如果答案一樣
            $result = 1;
        }else{
            $result = 0;
        }
        $sql = "INSERT INTO `pptv_answer_list`(`unique_id`, `question_id`,`correct`)
                     VALUES (".$uid.",".$question.",".$result.")";


		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);


        //check low_level
        if($latest_question == 1){ //如果錯回去第一題
            $low_level = 1;
        }else{
                //check_low_level = 1答對八題
            if( check_low_level($uid,$latest_question,$arry_conn_mssr,$conn_mssr) == 1 && ($low_level ==-1 || $low_level<$latest_question)){
                $low_level = $latest_question;
            }
        }
		//check high_level
        if($question == 125){
            $high_level = 125;
        }else{
            if( check_high_level($uid,$question,$arry_conn_mssr,$conn_mssr) == 1 && ($high_level ==-1 || $high_level>$latest_question)){

                $high_level = $latest_question;

                if($latest_question < $first_question)
                {
                    $high_level+=7;
                }
            }
        }
        //check end
        if( ($low_level != -1) && ($high_level != -1) ){ //已經答錯 //且完成作答


            //updata 權限;

            $auth['pptv_coda']=(int)$auth['pptv_coda']-(int)1;
            $auth=serialize($auth);
            $sql = "update mssr.mssr_auth_user set mssr.mssr_auth_user.auth = '$auth' where user_id = $uid";

            db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);







               $sql = "select * from pptv_score where unique_id = $uid order by first_time desc";
               $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

               $first_time = $retrun[0]['first_time'];



              $sql = "SELECT count(1) AS count FROM `pptv_answer_list` WHERE (`unique_id` = ".$uid.") and
                                                                (`question_id`>=".$low_level.") AND
                                                                (`question_id`<=".$high_level.") AND
                                                                (`correct` = 0) and (time > '$first_time')";
//echo $sql;


			$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

            $score = $high_level-$retrun[0]["count"]; //計算分數











        }
        //update pptv_score
                $sql = "UPDATE `pptv_score` SET `unique_id`=".$uid.", `first_question`=".$first_question.",
                        `latest_question`=".$latest_question.", `high_level`=".$high_level.",
                        `low_level`=".$low_level.", `score`=".$score."
                         WHERE `unique_id`=".$uid." and  `score`=-1";

		db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);






        if($question == 130){ //戊題答完 開始正式測驗
			call_js_function('begin()');
			header("Location: view/begin.html");
			exit;
		}
        if( $score != -1){
            call_js_function('end('.$score.')');
            exit;
        }

    }


	if($first_question==126){

            if($latest_question == -1){
                $question = 126; //第一題
            }
            else{

                $question = $latest_question +1; //完成第一題後 126+1
            }

    }else if($latest_question == 130){ //如果題目到戊題

                $question = $first_question;


    }else{

       if($first_question == $latest_question){ //30 題等於30題

               $sql = "select * from pptv_score where unique_id = $uid order by first_time desc";
               $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

               $first_time = $retrun[0]['first_time'];


                $sql = "
                        select
                                *
                        from
                                pptv_answer_list
                        where 1 = 1
                              and question_id   = '$first_question'
                              and unique_id     = '$uid'
                              and time          > '$first_time'

                        ";

				$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);



//echo $sql,'<br/>';




                if($retrun[0]["correct"]==1){ //$retrun[0]["correct"]必等於0
                    $question = $first_question+1;
                }else{
                    $question = $first_question-1;
                }

        }else if($first_question > $latest_question){ // 到回前面作答時



                if( $low_level != -1) //連續答對八題 或 不連續答對15題
                {
                    $sql = "select * from pptv_score where unique_id = $uid order by first_time desc";
                    $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

                    $first_time = $retrun[0]['first_time'];


                    $sql = "
                            select
                                    *
                            from
                                    pptv_answer_list

                            where   1=1
                                    and unique_id    =  '$uid'
                                    and question_id  <  126
                                    and time         >  '$first_time'

                            order by
                                    question_id desc
                           ";

                    $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);


                    $question = $retrun[0]["question_id"]+1;
                }else{
                    $question = $latest_question-1;


                }

        }else if($first_question < $latest_question){ //正常作答時

                if( $high_level != -1){ //最高水準出現時
                    $sql = "select * from pptv_score where unique_id = $uid order by first_time desc";
                    $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

                    $first_time = $retrun[0]['first_time'];
                    $sql = "
                            select
                                    *
                            from
                                    pptv_answer_list

                            where 1=1
                                  and unique_id = '$uid'
                                  and time      > '$first_time'

                            order by question_id ASC
                           ";

                    $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

//echo '最高水準',$sql,'<br/>';


                    $question = $retrun[0]["question_id"]-1;
                }else{

                    $question = $latest_question+1;

                }
        }
    }

	//--------------------------------------------------
	//--------------------------------------------------



	//---------------------------------------------------
	//function
	//---------------------------------------------------
function check_low_level($uid,$question,$arry_conn_mssr,$conn_mssr){

    $sql = "select * from pptv_score where unique_id = $uid order by first_time desc";
    $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

    $first_time = $retrun[0]['first_time'];


    $count = 0;

    if($question >= 118){
        $sql = "SELECT * FROM pptv_answer_list WHERE unique_id = '$uid'  AND question_id <= '$question' AND question_id >='$question'-7 AND time > '$first_time'";

    }else{
        $sql = "SELECT * FROM pptv_answer_list WHERE unique_id = '$uid'  AND question_id <= '$question'+7 AND question_id >='$question'-7 AND time > '$first_time'";
    }


    //echo $sql,'<br/>';

	$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);


    foreach($retrun as $key1=>$val1){
        if($val1["correct"]==1){ $count++; }



    }

        //echo 'check_low_level計數= ',$count,'<br/>';

    if( $count == 8) { return 1;}
    else { return 0; }
}
function check_high_level($uid,$question,$arry_conn_mssr,$conn_mssr){

    $sql = "select * from pptv_score where unique_id = $uid order by first_time desc";
    $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

    $first_time = $retrun[0]['first_time'];
    //echo $first_time,'<br/>';

    $count = 0;
    if($question >= 118){
        $sql = "SELECT * FROM pptv_answer_list WHERE unique_id = '$uid'  AND question_id <= '$question' AND question_id >='$question'-7 AND time > '$first_time'";
        //echo $sql,'<br/>';
    }else{
        $sql = "SELECT * FROM pptv_answer_list WHERE unique_id = '$uid'  AND question_id <= '$question'+7 AND question_id >='$question'-7 AND time > '$first_time'";
    }
    $retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

    foreach($retrun as $key1=>$val1){
        if($val1["correct"]==0){ $count++; }

    }

    //echo 'check_high_level計數= ',$count,'<br/>';

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
//question end?>
<!DOCTYPE>
<html>
<head>
<link type="text/css" rel="stylesheet" href="css/index.css">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>


<!--$first_question=    <?php echo $first_question,'<br/>';?>
$latest_question=   <?php echo $latest_question,'<br/>';?>
$low_level=         <?php echo $low_level,'<br/>';?>
$high_level=        <?php echo $high_level,'<br/>';?>
$score=             <?php echo $score,'<br/>';?>
$question=           <?php echo $question;?>--->

<div id="main" >
    <div id="left">
    <audio id="mp3">
        <source src="mp3/<?php echo $question;?>.mp3">
    </audio>
        <button id="btn" onClick="play()" type="button"></button>
        <p>  點選圖片進行回答</p>
        <p id="pro">
			<?php echo $school;?>
			<?php echo "<br>";?>
			<?php echo $grade."年".$class_name."班";?>
			<?php echo "<br>";?>
			<?php echo $nickname?>
		</p>
    </div>
    <form action="index.php" method="POST" id="right" style="background-image:url(jpg/<?php echo $question+6;?>.jpg);"> <!--送表單js-->

        <button type="button" id="a" class="opt" onClick="go(1,<?php echo $question;?>)"></button>
        <button type="button" id="b" class="opt" onClick="go(2,<?php echo $question;?>)"></button>
        <button type="button" id="c" class="opt" onClick="go(3,<?php echo $question;?>)"></button>
        <button type="button" id="d" class="opt" onClick="go(4,<?php echo $question;?>)"></button>
        <input type="hidden" name="answer" value="0"/>

        <input type="hidden" name="question" value="<?php echo $question;?>"/>
        <input type="hidden" name="first_question" value="<?php echo $first_question;?>"/>
        <input type="hidden" name="latest_question" value="<?php echo $latest_question;?>"/>
        <input type="hidden" name="low_level" value="<?php echo $low_level;?>"/>
        <input type="hidden" name="high_level" value="<?php echo $high_level;?>"/>
        <input type="hidden" name="score" value="<?php echo $score;?>"/>

    </form>

	<?php if(0) echo "<a id=\"man\" href=\"mange.php\">檢視班級成績</a>"; ?>
	<p id="qu"><?php question_number($question);?></p>
</div>
</body>
<script type="text/javascript" src="js/pptv_js.js"></script>
</html>

