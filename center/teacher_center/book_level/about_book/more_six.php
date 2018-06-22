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
    

    if(!isset($sess_user_id)&&!isset($sess_permission)&&!isset($sess_name)){


        echo '<span style="font-size:40px; color:red;">請先登入!!</span>';

        header('Location:http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/index.php');


        die();


        
     }

     if($sess_permission!="3"){

            echo '<span style="font-size:40px; color:red;">你沒有權限進入!!</span>';

            die();
    }

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

        $array_output=array();
        $topic_options=array();
        $array_book_sid=array();


        //======================
        //第二題題目選項(書的語言)
        //======================

        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                     WHERE topic_id ='2'

                ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic2_options']=unserialize($value['topic_options']);
            }
            
            // print_r($topic_options[$key]['topic3_options'][0]);
         }

        //======================
        //第三題題目選項(是否有注音)
        //======================
        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                    WHERE topic_id ='3'

                ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic3_options']=unserialize($value['topic_options']);
            }
            
            // print_r($topic_options[$key]['topic3_options'][0]);
         }

        //======================
        //第四題題目選項(大主題)
        //======================

        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                    WHERE topic_id ='4'

                 ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic4_options']=unserialize($value['topic_options']);
            }
            
           
         }

        //======================
        //第五題題目選項(中主題)
        //======================

        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                    WHERE topic_id ='5'

                 ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic5_options']=unserialize($value['topic_options']);
            }
            
           
        }
        //======================
        //第六題題目選項(小主題)
        //======================

        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                    WHERE topic_id ='6'

                 ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic6_options']=unserialize($value['topic_options']);
            }
            
           
        }

        //======================
        //第十四題題目選項(難度等級)
        //======================

        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                    WHERE topic_id ='14'

                 ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic14_options']=unserialize($value['topic_options']);
                
            }
            
           
         }



//----------------------------------------------------------

        //======================
        //表單登記的書
        //======================


        $book_sql="
              SELECT 
                    book_sid,
                    book_isbn_10,
                    book_isbn_13,
                    book_library_code
                                      
               FROM `mssr_idc_reading_log` 
               GROUP BY  `book_sid` 
               ORDER BY book_isbn_13 DESC 
                        
        ";


        $book_sql_result=db_result($conn_type='pdo',$conn_mssr,$book_sql,array(),$arry_conn_mssr);


        foreach ($book_sql_result as $key => $value) {
            // echo "key";
            // print_r($key);
            // echo "<br>";

                  $array_output[$key]['book_sid']          =trim($value['book_sid']);
                  $array_output[$key]['book_isbn_13']        =trim($value['book_isbn_13']);
                  $array_output[$key]['book_isbn_10']        =trim($value['book_isbn_10']);
                  $array_output[$key]['book_library_code']   =trim($value['book_library_code']);
               
                  //===================
                  //尋找書名
                  //===================
                   $sql="
                        SELECT 
                     
                             IFNULL(`book_name`,0) as `book_name` 
                             
                        FROM `mssr_book_class`
                        WHERE `book_sid` = '{$array_output[$key]['book_sid']  }'
                        AND school_code='idc'

                        union 
                        
                        SELECT 
                        
                       
                            IFNULL(`book_name`,0) as `book_name`
                        
                             
                        FROM `mssr_book_library`
                         WHERE `book_sid` = '{$array_output[$key]['book_sid']  }'
                        AND school_code='idc'


                        
                    ";



                    $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


                    if(!empty($result)){
                              
                             $array_output[$key]['book_name'] =trim($result[0]['book_name']);                    
                                    
                     }

                  $log_sql="

                        SELECT 
                                user_id,
                                book_language,
                                bopomofo, 
                                major_topic,
                                sub_topic,
                                minor_topic,
                                tag,
                                pages,
                                words,
                                one_person_level,
                                lexile_level
                                      
                        FROM `mssr_idc_reading_log`
                        WHERE `book_sid`='{$value['book_sid']}'             
                        order by book_sid desc

                    ";
 
                    $log_result=db_result($conn_type='pdo',$conn_mssr,$log_sql,array(),$arry_conn_mssr);

                    // print_r($log_result);




                    if(!empty($log_result)){
                            
                            foreach ($log_result as $index_key => $array_log) {
                                      //   echo "<br>";
                                      //  echo "index_key:";
                                      // print_r($array_log);
                                      //   echo "<br>";
                                    $array_output[$key]['book_language'][$index_key] =trim($array_log['book_language']);
                                    $array_output[$key]['bopomofo'][$index_key] =trim($array_log['bopomofo']);
                                    $array_output[$key]['tag'][$index_key] =trim($array_log['tag']);
                                    $array_output[$key]['pages'][$index_key]=trim($array_log['pages']);
                                    $array_output[$key]['words'][$index_key]=trim($array_log['words']);
                                     $array_output[$key]['one_person_level'][$index_key] =trim($array_log['one_person_level']);
                                    $array_output[$key]['lexile_level'][$index_key] =trim($array_log['lexile_level']);
                                       
                                        // ======================
                                        // 尋找誰寫出這筆資料 
                                        // ======================
                                         
                                    $user_sql="
                                                SELECT name
                                                FROM  `mssr_idc_member`
                                                WHERE  user_id='{$array_log['user_id']}'     
                                                " ; 
                                    $user_result=db_result($conn_type='pdo',$conn_mssr,$user_sql,array(),$arry_conn_mssr);
                                    if(!empty($user_result)){

                                            $array_output[$key]['name'][$index_key]=trim($user_result[0]['name']);
                                                                       
                                    }else{

                                            $array_output[$key]['name'][$index_key]="0";
                                                

                                    }


                                    // // //======================
                                    // // //尋找貼紙編號及貼紙顏色
                                    // // //======================

                                    $sticker_sql="
                                                SELECT `sticker_color`,`sticker_number` 
                                                FROM `mssr_idc_book_sticker_level_info`
                                                WHERE book_sid='{$array_output[$key]['book_sid']}'

                                    ";

                                    $sticker_result=db_result($conn_type='pdo',$conn_mssr,$sticker_sql,array(),$arry_conn_mssr);

                                    if(!empty($sticker_result)){
                                          

                                                $array_output[$key]['sticker_number']=trim($sticker_result[0]['sticker_number']);


                                                $sticker_color_sql="
                                                    SELECT color
                                                    FROM  `mssr_idc_book_sticker_color`
                                                    WHERE  color_id='{$sticker_result[0]['sticker_color']}'   
                                                " ; 

                                                $sticker_color_result=db_result($conn_type='pdo',$conn_mssr,$sticker_color_sql,array(),$arry_conn_mssr);
                                                  
                                                if(!empty($sticker_color_result)){

                                                        $array_output[$key]['sticker_color'] =trim($sticker_color_result[0]['color']);
                                                }else{
                                                    $array_output[$key]['sticker_color'] ="0";
                                                }
                                    }else{
                                            $array_output[$key]['sticker_color']="0";
                                            $array_output[$key]['sticker_number']="0";
                                    }


                                    // //======================
                                    // //書本的語言
                                    // //======================
                                    $array_output[$key]['book_language'][$index_key]=$topic_options[0]['topic2_options'][$array_log['book_language']];

                                    // //======================
                                    // //是否有注音
                                    // //======================
                                    $array_output[$key]['bopomofo'][$index_key]=$topic_options[0]['topic3_options'][$array_log['bopomofo']];
                                   
                                    //======================
                                    //書本的大主題
                                    //======================
                                    if($array_log['major_topic']){
                                        $major_topic=explode(",",$array_log['major_topic']);
                                        
                                        foreach ($major_topic as $index => $array_major) {

                                            $major_topic[$index]=$topic_options[0]['topic4_options'][$array_major];
                                            $array_output[$key]['major_topic'][$index_key]=$major_topic;

                                        }
                                    }

                                    //======================
                                    //書本的中主題
                                    //======================
                                    if($array_log['sub_topic']){
                                        $sub_topic=explode(",",$array_log['sub_topic']);
                                        
                                        foreach ($sub_topic as $index => $array_major) {

                                            $sub_topic[$index]=$topic_options[0]['topic5_options'][$array_major];
                                            $array_output[$key]['sub_topic'][$index_key]=$sub_topic;

                                        }
                                    }
                                    // //======================
                                    // //書本的小主題
                                    // //======================
                  
                                    if($array_log['minor_topic']){
                                            $minor_topic=explode(",",$array_log['minor_topic']);
                                            foreach ($minor_topic as $index => $array_minor) {

                                                $minor_topic[$index]=$topic_options[0]['topic6_options'][$array_minor];
                                                $array_output[$key]['minor_topic'][$index_key]=$minor_topic;

                                            }
                                     }
                                    // //======================
                                    // //單人等級
                                    // //======================

                                                 // if($array_log['one_person_level']){
                                    //     $hard_level=explode("-",$array_log['one_person_level']);
                                    //     foreach ($hard_level as $index => $array_hard) {

                                    //         $hard_level[$index]=$topic_options[0]['topic14_options'][$array_hard];
                                    //         $array_output[$key]['one_person_level'][$index_key]=$hard_level;

                                    //     }
                                    // }
                          
                                    if($array_log['one_person_level']){
                                        
                                            $array_output[$key]['one_person_level'][$index_key]=$array_log['one_person_level'];

                                        
                                    }

                                    // //======================
                                    // //平均等級
                                    // //======================

                                    if($array_output[$key]['book_sid']){

                                                    $avg_sql="

                                                        SELECT *
                                                        FROM  `mssr_idc_book_sticker_level_info` 
                                                        WHERE `book_sid`='{$array_output[$key]['book_sid']}'           
                                                        
                                                        
                                                    ";
                                                    
                                                    
                                                    $avg_result=db_result($conn_type='pdo',$conn_mssr,$avg_sql,array(),$arry_conn_mssr);

                                                    if($avg_result[0]['need_read_again']==="0"){

                                                                $array_output[$key]['avg_level']="{$avg_result[0]['avg_level']}";

                                                                $level_sql="

                                                                     SELECT one_person_level
                                                                     FROM `mssr_idc_reading_log` 
                                                                     WHERE `book_sid`='{$array_output[$key]['book_sid']}'           
                                                                
                                                                ";

                                                                $level_result=db_result($conn_type='pdo',$conn_mssr,$level_sql,array(),$arry_conn_mssr);

                                                                

                                                                if(count($level_result)<2){

                                                                    $array_output[$key]['about_level']="{$level_result[0]['one_person_level']}";

                                                                }else{

                                                                    if($level_result[0]['one_person_level']!=''){

                                                                        $one=$level_result[0]['one_person_level'];
                                                                    }else{

                                                                         $one=0;

                                                                    }

                                                                    if($level_result[1]['one_person_level']!=''){
                                                                        $two=$level_result[1]['one_person_level'];
                                                                    }else{
                                                                        $two=0;
                                                                    }

                                                                    
                                                                    

                                                                    if($one>$two){
                                                                         $about_level=$one-$two;
                                                                    }else if($two>$one){
                                                                         $about_level=$two-$one;
                                                                    }else{
                                                                        $about_level=$one;

                                                                    }


   
                                                                    $last_avg=($one+$two)/2;
                                                                    $array_output[$key]['about_level']="{$about_level}";
                                                                    $array_output[$key]['last_avg']="{$last_avg}";





                                                                }


                                                    }else{



                                                         $avg_sql="

                                                             SELECT avg_level                                  
                                                             FROM `mssr_idc_read_again_info_log` 
                                                             WHERE `book_sid`='{$array_output[$key]['book_sid']}'
                                                             AND  need_read_again='1'         
                                                        
                                                        
                                                        ";
                                                        

                                                        $avg_result=db_result($conn_type='pdo',$conn_mssr,$avg_sql,array(),$arry_conn_mssr);

                                                        

                                                        if(!empty($avg_result)){

                                                             $array_output[$key]['avg_level']=$avg_result[0]['avg_level'];
                                                        }

                                                       

                                                        $avg_sql1="

                                                             SELECT avg_level                                  
                                                             FROM `mssr_idc_read_again_info_log` 
                                                             WHERE `book_sid`='{$array_output[$key]['book_sid']}'
                                                             AND  need_read_again='2'         
                                                        
                                                        
                                                        ";

                                                        

                                                        $avg_result1=db_result($conn_type='pdo',$conn_mssr,$avg_sql1,array(),$arry_conn_mssr);

                                                        

                                                        if(!empty($avg_result1)){
                                                            $array_output[$key]['avg_level_1']=$avg_result1[0]['avg_level'];
                                                        }

                                                        

                                                    }

                                    }

                                    

                                    // //======================
                                    // //等級小於6或大於6
                                    // //======================


                                    // // //======================
                                    // // //尋找是否已閱
                                    // // //======================

                                    $read_sql="
                                                SELECT `avg_status`,`need_read_again`
                                                FROM `mssr_idc_book_sticker_level_info`
                                                WHERE book_sid='{$array_output[$key]['book_sid']}'

                                    ";

                                    $result=db_result($conn_type='pdo',$conn_mssr,$read_sql,array(),$arry_conn_mssr);

                                    if(!empty($result)){
                                          
                                            $array_output[$key]['avg_status']=trim($result[0]['avg_status']);
                                            $array_output[$key]['need_read_again'] =trim($result[0]['need_read_again']);
                                                
                                    }else{
                                            $array_output[$key]['avg_status']="0";
                                            $array_output[$key]['need_read_again']="0";
                                    }



                            }
                              
                                
                    }

                
           
        }

?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=false);?>
    <?php echo robots($allow=false);?>

    <!-- 通用 -->
    <!-- <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" /> -->
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/table/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <!-- <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" /> -->
    <link rel="stylesheet" type="text/css" href="../css/book_level.css" />

    <style>
    #more_six{
        font-size: 13px;
        font-family: 微軟正黑體;
    }

    #level_table_over6 table{
        text-align: center;
        margin: 30px auto;

    }
    .buttons{
        text-align: center;
        margin: 20px auto;
    }
    td{
        max-width: 110px;
        max-height: 150px;
        overflow: hidden;
        color: #444;
    }
    .input_text{
        display: none;
    }






</style>
</Head>

<Body>
        <!-- 內容區塊 開始 -->
    <div id="more_six">
            <div>
                <?php
                    require_once('../user/index.php');
                    if($sess_permission==="3"){require_once('../user/super_use_index.php');};
                ?>
            </div>
            <div class="about_title_div">
                <h1 id="title">等級大於6書表</h1>
            </div>
            <div class="buttons">
                
                
                 <a href="more_six.php"><input type="button" id="level_6" value="等級大於6書表"></a>
                 <a href="less_six.php"><input type="button" id="level_0" value="等級小於6書表"></a>
                 <a href="need_read_again.php"><input type="button" id="read_again" value="需重看書籍表"></a>
                 <a href="pt_work.php"><input type="button" id="pt" value="工讀生工作表" ></a>
                 <a href="read.php"><input type="button" id="read" value="已閱表"></a>
                 <a href="topic.php"><input type="button" id="topic" value="書本主題表"></a>
                 <a href="tag.php"><input type="button" id="tag" value="書本標籤表"></a>
                 <a href="pages.php"><input type="button" id="pages" value="書本頁數字數表"></a>

            </div>
            
            <div id="level_table_over6">
                <table id="level" border="1" cellpadding="5" style="border:1px #aaa solid; text-align:center;margin: 20px auto;">
                            <tr>
                                <th>已閱</th>
                                <th>書名</th> 
                                <th>isbn10碼</th>
                                <th>isbn13碼</th>
                                <th>圖書館編號</th>
                                <th>貼紙顏色</th>
                                <th>貼紙編號</th>
                                <th>學生姓名</th>
                                <th>難度等級</th>
                                <th>等級平均</th>
                                <th>頁數</th>
                                <th>字數</th>
                                <th>需要重看</th>
                                
                                
                            </tr>   
                       
                <?php foreach ($array_output as $key => $value) { ?>
                        <?php if($value['avg_status']==="0"&&$value['need_read_again']==='0'&& $value['about_level']>6){?>
                            <tr class="data_content" id="data_one_<?php echo $key?>"> 
                                        <input type="hidden" class="book_sid" value="<?php echo $value['book_sid']?>" name="<?php echo $value['book_sid']?>">
                                        <td class="read_checkbox" rowspan="2" style="height:40px;">
                                            <input type="checkbox" id="read_checkbox_<?php echo $key?>">
                                        </td>
                                        <td rowspan="2" id="book_name" style="height: 40px;" title="<?php echo trim($value['book_name']) ?> ">            <?php echo $value['book_name']?>
                                         
<!-- 
                                                        $string=trim($value['book_name']); 
                                                        $strLength = mb_strlen($string,"utf-8");
                                                        if($strLength<=7){
                                                            $textAns = $string;
                                                            echo $textAns;
                                                        }else{
                                                            $textAns = mb_substr($string,0,8,'utf-8');
                                                            echo $textAns,"...";
                                                            
                                                        }
                                                         -->
                                            
                                        </td>
                                        <td rowspan="2" id="isbn_10" style="height: 40px;"><?php echo $value['book_isbn_10']?></td>
                                        <td rowspan="2" id="isbn_13" style="height: 40px;"><?php echo $value['book_isbn_13']?></td>
                                        <td rowspan="2" id="book_library_code" style="height: 40px;"><?php echo $value['book_library_code']?></td>
                                        <td rowspan="2" id="sticker_color" style="height: 40px;">
                                             <?php if($value['sticker_color']==="0"){echo  $value['sticker_color']="";
                                                  }else{
                                                    echo $value['sticker_color'];
                                                   } ?>
                                            
                                        </td>
                                        <td rowspan="2" id="sticker_number" style="height:40px;"> 

                                                <?php if($value['sticker_number']==="0"){
                                                    echo  $value['sticker_number']="";
                                                  }else{
                                                    echo $value['sticker_number'];
                                                   } ?>
                                                
                                        </td>
                                        <td style="height:20px;"> 
                                            <?php
                                              if(!empty($value['name'][0])){
                                                    echo $value['name'][0];
                                            }?>
                                                
                                        </td>
                                        <td style="height:20px;">
                                            <?php
                                              if(!empty($value['one_person_level'][0])){
                                                echo $value['one_person_level'][0];
                                               }
                                            ?>
                                            
                                        </td>
                                        <td rowspan="2"  id="change_<?php echo $key?>" style="height: 40px;">    
                                            
                                            <div class="avg">
                                                <span class="avg_level" id="avg_level_<?php echo $key?>"><?php echo $value['last_avg'];?></span>
                                                <input type="text" value="<?php echo $value['avg_level'];?>" class="input_text" id="change_text_<?php echo $key?>" style="display: none;width: 30px;" size="3" maxlength="3"  >
                                                <input type="button" value="修改"  id="change_btn_<?php echo $key?>">
                                            </div>

                                        </td>
                                         <td style="height:20px;">
                                            <?php
                                              if(!empty($value['pages'][0])){
                                                echo $value['pages'][0];
                                               }
                                            ?>
                                            
                                        </td>
                                        <td style="height:20px;">
                                            <?php
                                              if(!empty($value['words'][0])){
                                                echo $value['words'][0];
                                               }
                                            ?>
                                            
                                        </td>
                                           
                                      
                                       
                                        <td class="read_again_checkbox" rowspan="2" style="height: 40px;">  <input type="checkbox" id="read_again_checkbox_<?php echo $key?>"></td>
                                
                            </tr>
                            <tr class="data_content" id="data_two_<?php echo $key?>">
                                        <td style="height: 20px;"> 
                                            <?php
                                              if(!empty($value['name'][1])){
                                                echo $value['name'][1];
                                            }?>
                                            
                                        </td>
                                        <td style="height: 20px;"> 
                                            <?php
                                             if(!empty($value['one_person_level'][1])){
                                             echo $value['one_person_level'][1]; }
                                            ?>
                                                
                                        </td>
                                         <td style="height:20px;">
                                            <?php
                                              if(!empty($value['pages'][1])){
                                                echo $value['pages'][1];
                                               }
                                            ?>
                                            
                                        </td>
                                        <td style="height:20px;">
                                            <?php
                                              if(!empty($value['words'][1])){
                                                echo $value['words'][1];
                                               }
                                            ?>
                                            
                                        </td>
                                 
                                      
                            </tr>

                        <?php }?>
                <?php } ?>
                       
                </table>
            
            </div>

   
</div>
</Body>
<script type="text/javascript">

//點擊上方按鈕表單切換


//若按下修改鍵

$(".avg input:button").click(function(){

        var $this = $(this);
        var btn_id=$this.attr("id");
        var val=$this.val();
        var td_id=$(this).parent().attr("id");
        
        console.log(val);
        if(val=="修改"){
            $this.attr("value", "儲存");
            $(this).siblings('.input_text').css("display","block");

        }else{
           
            $this.attr("value", "修改");
            var input_id=$(this).siblings('.input_text').css("display","none");
            var input_text_val=$(this).siblings('.input_text').val();
            var span_id=$(this).siblings('.avg_level').attr("id");
            var book_sid=$(this).parent().parent().siblings('.book_sid').val();

            
            $("#"+span_id).text(input_text_val);
            change_level(input_text_val,book_sid);
            
        };

       
});



//修改等級

function change_level(input_text_val,book_sid){
        var url = "ajax/update_avg_level.php";
        var dataVal = {
           
            avg_level:            input_text_val,
            book_sid:             book_sid  
        };

        $.ajax({
                   url: url,
                   type: "POST",
                   datatype: "json",
                   data: dataVal,
                   // contentType: "application/json; charset=utf-8",
                   async: false,
                   success: function(data) {

                   },
                   error: function(jqXHR) {
                    alert("發生錯誤: " + jqXHR.status);
                  }

        });

}

//已閱按鈕被按下後
$(".read_checkbox input:checkbox").change(function(){
    var $this = $(this);   
    if($this.is(":checked")){ 
        var checkbox_id=$this.attr("id");
        var book_sid=$this.parent().siblings('.book_sid').val();
        var www=$this.parent().parent('.data_content');
        var www_id=www.attr("id");
        console.log(www_id);
        var str=www_id.substr(9);
        console.log(www_id.substr(9));
        var hhh=$this.parent().parent('#'+www_id).siblings('#data_two_'+str);
 
        var hhh_id=hhh.attr("id");
        console.log(hhh_id);
        $("#"+www_id).remove();
        $("#"+hhh_id).remove();
        var status="true";
        read(book_sid,status);
    }

       
});


//已閱

function read(book_sid,status){

      var url = "./ajax/update_read.php";
        var dataVal = {
          
            status:               status,
            book_sid:             book_sid  
        };

        $.ajax({
                   url: url,
                   type: "POST",
                   datatype: "json",
                   data: dataVal,
                   // contentType: "application/json; charset=utf-8",
                   async: false,
                   success: function(data) {

                   },
                   error: function(jqXHR) {
                    alert("發生錯誤: " + jqXHR.status);
                  }

        });

}


//重看按鈕被按下後
$(".read_again_checkbox input:checkbox").change(function(){
    var $this = $(this);   
    if($this.is(":checked")){ 
        var checkbox_id=$this.attr("id");
        var book_sid=$this.parent().siblings('.book_sid').val();
        var www=$this.parent().parent('.data_content');
        var www_id=www.attr("id");
        console.log(www_id);
        var str=www_id.substr(9);
        console.log(www_id.substr(9));
        var hhh=$this.parent().parent('#'+www_id).siblings('#data_two_'+str);
 
        var hhh_id=hhh.attr("id");
        console.log(hhh_id);
        $("#"+www_id).remove();
        $("#"+hhh_id).remove();
        var status="true";
        read_again(book_sid,status);

    }



       
});


//重看按鈕

function read_again(book_sid,status){

    console.log(book_sid);

      var url = "./ajax/update_read_again.php";
        var

            book_sid:             book_sid  
        };

        $.ajax({
                   url: url,
                   type: "POST",
                   datatype: "json",
                   data: dataVal,
                   // contentType: "application/json; charset=utf-8",
                   async: false,
                   success: function(data) {

                   },
                   error: function(jqXHR) {
                    alert("發生錯誤: " + jqXHR.status);
                  }

        });

}

//最終等級按鈕被按下後
$(".save input:button").click(function(){
   
        var $this = $(this);
        var btn_id=$this.attr("id");
        
        var txt_id=$(this).siblings('.input_text').attr("id");
        var txt_val=$("#"+txt_id).val();
        var book_sid=$(this).parent().parent().siblings('.book_sid').val();

        // last_avg(book_sid,txt_val);

        var tr_one=$this.parent().parent().parent('.data_content');
        var tr_one_id=tr_one.attr("id");
        var str=tr_one_id.substr(-1);

        var tr_two=$this.parent().parent().parent('#'+tr_one_id).siblings('#data_two_'+str);
        var tr_two_id=tr_two.attr("id");
        var tr_three=$this.parent().parent().parent('#'+tr_one_id).siblings('#data_three_'+str);
        var tr_three_id=tr_three.attr("id");

        var tr_four=$this.parent().parent().parent('#'+tr_one_id).siblings('#data_four_'+str);
        var tr_four_id=tr_four.attr("id");


        $("#"+tr_one_id).remove();
        $("#"+tr_two_id).remove();
        $("#"+tr_three_id).remove();
        $("#"+tr_four_id).remove();

        last_avg(book_sid,txt_val);
       
       
});

//最終等級
function last_avg(book_sid,txt_val){

  

      var url = "./ajax/update_last_avg.php";
        var dataVal = {
           
            txt_val:              txt_val,
            book_sid:             book_sid  
        };

        $.ajax({
                   url: url,
                   type: "POST",
                   datatype: "json",
                   data: dataVal,
                   // contentType: "application/json; charset=utf-8",
                   async: false,
                   success: function(data) {

                   },
                   error: function(jqXHR) {
                    alert("發生錯誤: " + jqXHR.status);
                  }

        });

}
//找尋時間內工讀生登記本數
function search(){
        var date_start=$("#date_start").val();
        var date_end=$("#date_end").val();
        var url = "./ajax/search_pt_data.php";
        var dataVal = {
 
            date_start:           date_start,
            date_end:             date_end 

        };

        $.ajax({
                   url: url,
                   type: "POST",
                   datatype: "json",
                   data: dataVal,
                   // contentType: "application/json; charset=utf-8",
                   async: false,
                   success: function(data) {

                    data_array = JSON.parse(data);
                  

                        for(var key in data_array){
                            var name= data_array[key]['name'];
                            var count=data_array[key]['count'];
                            $('.table_about_pt tbody').append('<tr class="data"><td>'+name+'</td><td>'+count+'</td></tr>');
                        }


                   },
                   error: function(jqXHR) {
                    alert("發生錯誤: " + jqXHR.status);
                  }

        });

}

//--------------
///畫面load進///
//-------------

// function main(){



//         var url = "./php/get_reading_log_info.php";
//         var dataVal = {


//};
            

//         $.ajax({
//                    url: url,
//                    type: "POST",
//                    datatype: "json",
//                    data: dataVal,
//                    // contentType: "application/json; charset=utf-8",
//                    async: false,
//                    success: function(data) {
  
//                     data_array = JSON.parse(data);
//                     // console.log(data_array);


//                     data_array.forEach(function(element,index){

//                         var tr=document.createElement("tr");
//                         tr.setAttribute("id", "data"+index);
//                         // document.getElementById("level").appendChild(tr);

//                         $("#data"+index).append("<td rowspan='2' class='checkbox_"+index+1+"'><input type='checkbox' id='read_checkbox_"+index+1+"'></td>");                        
//                         $("#data"+index).append("<td rowspan='2'>"+element.book_name+"</td>");
//                         $("#data"+index).append("<td rowspan='2'>"+element.book_isbn_13+"</td>");
//                         $("#data"+index).append("<td rowspan='2'>"+element.book_library_code+"</td>");
//                         if(element.sticker_color=="undefined"){  element.sticker_color="";}
//                         $("#data"+index).append("<td rowspan='2'>"+element.sticker_color+"</td>");
//                         $("#data"+index).append("<td rowspan='2'>"+element.sticker_number+"</td>");
//                         // $("#data").append("<td>"+hard+"</td>");
//                         // $("#data").append("<td>"+element.sticker_number+"</td>");
//                         // element.book_language.forEach(function(language){
//                         //     console.log( "book_language:",element.book_language,"index:",index)

//                        // console.log(element.hard_level);
//                         element.hard_level.forEach(function(hard, i){
                              
//                               $("#data"+i).append("<td id=one_"+i+">"+hard+"</td>");                       
//                         });

//                         // for(var key in element.hard_level){
//                         //     var kk=element.hard_level[key];

//                         //      $("#data").append("<td id=one_'"+index+">"+kk+"</td>");
//                         //   // $("#data1").append("<td id=two_'"+index+"''>"+kk+"</td>");
//                         // }

//                          // $("#data").append("<td rowspan='2'>"+element.avg_level+"</td>");
//                          // $("#data").append("<td rowspan='2'>"+element.avg_level+"</td>");


//                         //  element.name.forEach(function(name){
//                         //   console.log( "name:",name);
//                         //   $("#data").append("<td id=one_'"+index+"''>"+name+"</td>");
//                         //   $("#data1").append("<td id=two_'"+index+"''>"+name+"</td>");
//                         //   // language.forEach(function(hard){
//                         //   //    console.log( "hard:",hard);
//                         //   //    // $("#data").append("<td>"+hard+"</td>");
//                         //   // });                           
//                         // });
//                     });

// // 
//                     // for(var key in data_array){

//                     //     var book_name=data_array[key]['book_name'];
// //                     //     var book_isbn_13=data_array[key]['book_isbn_13'];
// //                     //     var book_isbn_10=data_array[key]['book_isbn_10'];
// //                     //     var book_library_code=data_array[key]['book_library_code'];
// //                     //     var book_language=data_array[key]['book_language'];

                       

// //                     //     for(var index_key in book_language){

// //                     //         console.log(book_language);
// //                     //         var language=book_language[index_key]['book_language'];
                    

// //                     //     }
                     


// //                     // }
                   
                   

// //                    },
// //                    error: function(jqXHR) {
// //                     alert("發生錯誤: " + jqXHR.status);
// //                   }

// //         });


// } 
// main();




//978986317693
</script>
</Html>