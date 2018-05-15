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

    $class_code = '';

    if(isset($_GET['class_code'])){
    $class_code = addslashes($_GET['class_code']);
    }


//建立連線 user
    $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
    $conn_user=conn($db_type='mysql',$arry_conn_user);
// print_r($conn_user);

     $sql = "
                   select
                    mssr.pptv_score.is_show,
                    user.member.uid,
                    user.member.name,
                    user.school.school_name,
                    user.class.class_code,
                    user.class.grade,
                    user.class_name.class_name,
                    user.student.number,

                    IFNULL((
                        select
                            mssr.pptv_score.score
                        FROM mssr.pptv_score
                        WHERE 1=1
                            AND mssr.pptv_score.is_show IN(1)
                            AND mssr.pptv_score.unique_id=user.member.uid
                        order by mssr.pptv_score.id DESC
                        LIMIT 1
                    ),'') as `score`,

                    IFNULL((
                        select
                            mssr.pptv_score.first_time
                        FROM mssr.pptv_score
                        WHERE 1=1
                            AND mssr.pptv_score.is_show IN(1)
                            AND mssr.pptv_score.unique_id=user.member.uid
                        order by mssr.pptv_score.id DESC
                        LIMIT 1
                    ),'') as first_time,

                    IFNULL((
                        select
                            mssr.pptv_score.latest_time
                        FROM mssr.pptv_score
                        WHERE 1=1
                            AND mssr.pptv_score.is_show IN(1)
                            AND mssr.pptv_score.unique_id=user.member.uid
                        order by mssr.pptv_score.id DESC
                        LIMIT 1
                    ),'') as latest_time,

                    IFNULL((
                        select
                            mssr.pptv_score.id
                        FROM mssr.pptv_score
                        WHERE 1=1
                            AND mssr.pptv_score.is_show IN(1)
                            AND mssr.pptv_score.unique_id=user.member.uid
                        order by mssr.pptv_score.id DESC
                        LIMIT 1
                    ),'') as id

            from   user.student
                    inner join user.member                 on      user.member.uid                  = user.student.uid
                    inner join user.class                  on      user.student.class_code          = user.class.class_code
                    inner join user.semester               on      user.class.semester_code         = user.semester.semester_code
                    inner join user.school                 on      user.semester.school_code        = user.school.school_code
                    inner join user.class_name             on      user.class_name.class_category   = user.class.class_category
                    left join mssr.pptv_score              on      user.member.uid                  = mssr.pptv_score.unique_id
            where 1 = 1

                   and  DATE(user.semester.start)      <= CURDATE()
                   and  DATE(user.semester.end)        >= CURDATE()
                   and  DATE(user.student.start)       <= CURDATE()
                   and  DATE(user.student.end)         >= CURDATE()
                   and  user.class.classroom            = user.class_name.classroom
                   and  user.class.class_code           = '$class_code'
                   and  user.member.permission         <> 'x'
                   and  (
                        mssr.pptv_score.is_show         IN(1,2)
                            OR
                        mssr.pptv_score.is_show IS NULL
                        )
            group by user.member.uid
            order by user.student.number




            ";


    $rs =   db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);


?>




































<!--view-->

<!DOCTYPE HTML>
<Html>
<Head>
    <Title></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="Content-Language" content="UTF-8">

    <script type="text/javascript" src=""></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.blockUI.js"></script>


    <link rel="stylesheet" href=""/>
    <link rel=stylesheet type="text/css" href="css/mange.css">
    <link rel="stylesheet" type="text/css" href="css/def.css" media="all" />
    <!-- <link rel="stylesheet" type="text/css" href="css/content.css" media="all" /> -->

</head>

<body>

    <?php if($class_code == ''){
        $show_table = 'display:none';
        $show_word  = 'display:';
    }else{
        $show_table = 'display:';
        $show_word  = 'display:none';
        $grade      = $rs[0]['grade'];
        $grade     .= '年';
        $class_name = $rs[0]['class_name'];
        $class_name.= '班';
    }?>

    <table align ='' id = 'talbe_title' style = <?php echo $show_table ?>>
       <tr>
            <th><?php echo  htmlspecialchars($rs[0]['school_name']); ?></th>
            <th><?php echo  htmlspecialchars($grade);?><?php echo  htmlspecialchars($class_name);?></th>

        <tr>
    </table>
    <table id = 'mange' cellpadding="0" cellspacing="0" border="1" width="100%" style = <?php echo $show_table ?> />

        <tr>
            <td>座號        </td>
            <td>姓名        </td>
            <td>分數        </td>
            <td>開始時間    </td>
            <td>結束時間    </td>
            <td>測驗額度    </td>
            <td>本學期已測驗次數</td>
            <td>測驗歷程    </td>
            <td>刪除本次測驗       </td>
        </tr>
<?php foreach($rs as $key => $v){ ?>

<?php
    $id            = $v['id'];
    $uid           = $v['uid'];
    $number        = $v['number'];
    $name          = $v['name'];
    $score         = $v['score'];
    $first_time    = $v['first_time'];
    $latest_time   = $v['latest_time'];
?>


        <?php if($latest_time =='0000-00-00 00:00:00'){
                   $latest_time = $first_time;
              }?>

        <?php if($score == -1){
                 $score = '未完成';
                 $color_score="style='color:red'";
              }else{
                  $color_score="style=''";
              }?>
        <?php if($first_time   ==  null && $latest_time == null &&  $score  == null ){
                 $first_time   ='未測驗';
                 $latest_time  ='未測驗';
                 $score        ='未測驗';
                 $colorTree    ="style='color:red'";
              }else{
                 $colorTree    ="style=''";
              }?>

       <?php if($id == null){
                $show_form = 'display:none';
                $c_form    = 'display:';

             }else{
                $show_form = 'display:';
                $c_form    = 'display:none';

             }?>




      <?php


      $sql = "
            select *

            from   user.student
                    inner join user.member                 on      user.member.uid                  = user.student.uid
                    inner join user.class                  on      user.student.class_code          = user.class.class_code
                    inner join user.semester               on      user.class.semester_code         = user.semester.semester_code
                    inner join user.school                 on      user.semester.school_code        = user.school.school_code
                    inner join user.class_name             on      user.class_name.class_category   = user.class.class_category

                    inner join mssr.pptv_score             on      user.member.uid                  = mssr.pptv_score.unique_id
            where 1 = 1

                   and  DATE(user.semester.start)      <= CURDATE()
                   and  DATE(user.semester.end)        >= CURDATE()
                   and  DATE(user.student.start)       <= CURDATE()
                   and  DATE(user.student.end)         >= CURDATE()
                   and  user.class.classroom            = user.class_name.classroom
                   and  mssr.pptv_score.unique_id       = $uid
                   and  mssr.pptv_score.is_show         = 1
       ";

      $rs =  db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);

      $s_count = count($rs);






    $sql = "
            select
                 mssr.mssr_auth_user.auth
            from
                 mssr.mssr_auth_user
            where 1 = 1
               and user_id = $uid
           ";
    $rs = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr); //執行sql反回數組



    $auth = $rs[0]['auth'];
    $auth = unserialize($auth);
    $auth = $auth['pptv_coda'];



         if($auth == 1){
            $coda = '有';
            $color="style='color:red'";
         }else{
            $coda = '無';
             $color="style=''";
         }



?>







        <tr>
            <td <?php echo $color ?>><?php echo htmlspecialchars($number     ); ?>  </td>
            <td <?php echo $color ?>><?php echo htmlspecialchars($name       ); ?>  </td>
            <td <?php echo $color ?>><?php echo htmlspecialchars($score      ); ?>  </td>
            <td <?php echo $color ?>><?php echo htmlspecialchars($first_time ); ?>  </td>
            <td <?php echo $color ?>><?php echo htmlspecialchars($latest_time); ?>  </td>
            <td <?php echo $color ?>><?php echo htmlspecialchars($coda       ); ?>  </td>
            <td <?php echo $color ?>><?php echo htmlspecialchars($s_count    ); ?>次</td>

            <td><button class = 'hisBtn' onclick = "ShowSubWin(<?php echo $uid ?>)">詳細</button></td>

        <form action ="deleteMange.php" target = "" onsubmit="return confirm('你確定要刪除最新一次的考試成績嗎?');" >
                <input type = 'hidden' value = '<?php echo $id ?>'  name ='id'>
                <input type = 'hidden' value = '<?php echo $uid ?>' name ='uid'>
                <input type = 'hidden' value = '<?php echo $class_code ?>' name ='class_code'>
            <td><input type = 'submit' value = '刪除' style = <?php echo $show_form ?>></td>
        </form>


        </tr>

<?php } ?>


    </table>

 <!-- 資料列表 開始 -->
        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->

                <!-- 內容 -->
        <table style = <?php echo $show_word ?> border="0" width="100%" cellpadding="5" cellspacing="0" style="position:relative;top:50px;" class="mod_data_tbl_outline">
        <tr align="center" valign="middle">
            <td>&nbsp;</td>
        </tr>

        <tr>
            <td height="400px" align="center" valign="middle" class="font-family1 fc_green0 fsize_18">
            <img src="jpg/fail.gif" style="vertical-align:middle;margin:2px;">
                    請先選擇右上方的年級與班級!
            </td>
        </tr>
        </table>
                <!-- 內容 -->
            </td>
        </tr>
        </table>
        <!-- 資料列表 結束 -->



</body>
</Html>





<script>


    function ShowSubWin(uid){

        var frmHtml = "<iframe frameborder='0'   style='width: 100%; height:300px' src='mangeHistory.php?uid= " + uid + "'></iframe>";

        $.blockUI({

             css:({ 'height':'275px','top':'20%','width':'850px','left':'50%' ,'margin-left':'-425px','overflow-y':'hidden','border':'0px'}),
             onOverlayClick:$.unblockUI,
             message: frmHtml

        });
    }



    function close(){
        $.unblockUI();
    }






</script>