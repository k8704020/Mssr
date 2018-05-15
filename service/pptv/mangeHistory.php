<?php


//-------------------------------------------------------

//設定頁面語系
    header("Content-Type: text/html; charset=UTF-8");

//設定文字內部編碼
    mb_internal_encoding("UTF-8");

//設定台灣時區
    date_default_timezone_set('Asia/Taipei');

//--------------------------------------------------------




//外掛設定檔
    require_once(str_repeat("../",2)."/config/config.php");

//外掛函式檔
    $funcs=array(
                APP_ROOT.'inc/code',
                APP_ROOT.'lib/php/db/code',
                APP_ROOT.'lib/php/array/code'
                );
    func_load($funcs,true);

//接參數
    $uid = addslashes($_GET['uid']);



//建立連線 user
    $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
    $conn_user=conn($db_type='mysql',$arry_conn_user);


//預設每頁筆數
	$pageRow_records = 5;
	//預設頁數
	$num_pages = 1;
	//若已經有翻頁，將頁數更新
	if (isset($_GET['page'])) {
	  $num_pages = $_GET['page'];
	}
	//本頁開始記錄筆數 = (頁數-1)*每頁記錄筆數
	$startRow_records = ($num_pages -1) * $pageRow_records;
	//未加限制顯示筆數的SQL敘述句
	$sql_query = "
             select name,
                    school_name,
                    score,
                    first_time,
                    latest_time,
                    class.class_code,
                    grade,
                    class_name,
                    is_show,
                    number



           from   user.student
                      join user.member                 on      user.member.uid                  = user.student.uid
                      join user.class                  on      user.student.class_code          = user.class.class_code
                      join user.semester               on      user.class.semester_code         = user.semester.semester_code
                      join user.school                 on      user.semester.school_code        = user.school.school_code
                      join user.class_name             on      class_name.class_category        = user.class.class_category
                 left join mssr.pptv_score             on      user.member.uid                  = mssr.pptv_score.unique_id


            where 1 = 1
                   and  DATE(user.semester.start)      <= CURDATE()
                   and  DATE(user.semester.end)        >= CURDATE()
                   and  DATE(user.student.start)       <= CURDATE()
                   and  DATE(user.student.end)         >= CURDATE()
                   and  user.class.classroom            = user.class_name.classroom
                   and  member.uid                      = '$uid'
                   and  (
                        mssr.pptv_score.is_show         IN(1)
                            OR
                        mssr.pptv_score.is_show IS NULL
                        )
            order by mssr.pptv_score.id desc
          ";


// echo "<pre>";          
// print_r($sql_query);

 
	


  //加上限制顯示筆數的SQL敘述句，由本頁開始記錄筆數開始，每頁顯示預設筆數
	$sql_query_limit = $sql_query." LIMIT ".$startRow_records.", ".$pageRow_records;
	//以加上限制顯示筆數的SQL敘述句查詢資料到 $result 中
	$result = $conn_mssr->query($sql_query_limit);
	//以未加上限制顯示筆數的SQL敘述句查詢資料到 $all_result 中
	$all_result = $conn_mssr->query($sql_query);
	//計算總筆數
	$total_records = $all_result->rowCount();
	//計算總頁數=(總筆數/每頁筆數)後無條件進位。
	$total_pages = ceil($total_records/$pageRow_records);


    $rs = array();
    while($row = $result->fetch(PDO::FETCH_ASSOC)){
        $rs[] = $row;
    }





















//html
?>
            <!DOCTYPE HTML>
            <Html>
            <Head>
                <Title></Title>
                <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
                <meta http-equiv="Content-Language" content="UTF-8">

                <script type="text/javascript" src=""></script>
                <link rel=stylesheet type="text/css" href="css/mange.css">
                <link rel="stylesheet" href=""/>

                <style>
                    #close{

                        float:  right;
                        margin-bottom: 10px;

                    }
                </style>

            </head>

            <body>
                <button id = 'close' onclick = 'parent.close()'>關閉</button>

                 <table align ='' id = 'talbe_title'>
                   <tr>
                        <th><?php echo  htmlspecialchars($rs[0]['school_name']); ?></th>
                        <!-- <th><?php echo  htmlspecialchars($rs[0]['grade']);?>年<?php echo  htmlspecialchars($rs[0]['class_name']);?>班</th> -->
                        <th><?php echo  htmlspecialchars($rs[0]['name']);?></th>
                        <th><?php echo  htmlspecialchars($rs[0]['number']);?>號</th>
                    <tr>
                </table>

                <table cellpadding="0" cellspacing="0" border="1" width="100%" id = 'mange'/>

                    <tr>
                        <!-- <td>座號    </td> -->
                        <!-- <td>姓名    </td> -->
                        <td>分數    </td>
                        <td>開始時間</td>
                        <td>結束時間</td>
                        <!-- <td>測驗次數</td> -->

                    </tr>
           <?php foreach($rs as $key => $v){?>


<?php


    $number        = $v['number'];
    $name          = $v['name'];
    $score         = $v['score'];
    $first_time    = $v['first_time'];
    $latest_time   = $v['latest_time'];
    $is_show       = $v['is_show']
?>




                   <?php if($latest_time=='0000-00-00 00:00:00'){
                            $latest_time = $first_time;
                         }?>

                    <?php if($score ==-1){
                             $score = '未完成';
                          }?>
                    <?php if($first_time  == null && $latest_time == null && $score == null ){
                             $first_time  ='未測驗';
                             $latest_time ='未測驗';
                             $score       ='未測驗';
                          }?>
                    <?php if($v['score'] == '未測驗'){
                             $value = '未測驗';
                          }else{
                             if($num_pages!=1){
                                 $value = '第';
                                 $value.= ($key+1)+(5*($num_pages-1));
                                 $value.='次測驗';
                             }else{
                             $value = '第';
                             $value.= ($key+1);
                             $value.='次測驗';
                             }
                          }

                    ?>
                          <?php if($is_show == 2){ ?>

                          <?php } else{?>


                    <tr>
                        <!-- <td><?php echo htmlspecialchars($number     );  ?></td> -->
                        <!-- <td><?php echo htmlspecialchars($name       );  ?></td> -->
                        <td><?php echo htmlspecialchars($score      );  ?></td>
                        <td><?php echo htmlspecialchars($first_time );  ?></td>
                        <td><?php echo htmlspecialchars($latest_time);  ?></td>
                        <!-- <td><?php echo htmlspecialchars($value); ?></td> -->
                    </tr>
                         <?php } ?>
            <?php } ?>
            </table>






            <table  border="0" align="center" id = 'mangeHistory' >
              <tr   border="0">
                <td border="0">
                  頁數：
                  <?php
                  for($i=1;$i<=$total_pages;$i++){
                      if($i==$num_pages){
                          echo $i." ";
                      }else{
                          echo "<a href=\"mangeHistory.php?page=$i&uid=$uid\">$i</a> ";
                      }
                  }
                  ?>
                </td>
              </tr>
            </table>


            </body>
            </Html>

