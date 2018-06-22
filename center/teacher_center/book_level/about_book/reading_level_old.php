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
        require_once(str_repeat("../",3).'config/config.php');
        require_once(str_repeat("../",3)."/inc/get_black_book_info/code.php");

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

            $other_school_code=book_borrow_school_rev($db_type='mysql',$arry_conn_mssr,$sess_school_code);

        //---------------------------------------------------
        //SQL 筆數查詢
        //---------------------------------------------------

          
    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

      

    //---------------------------------------------------
    //SQL 查詢
    //---------------------------------------------------

      
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
    <script type="text/javascript" src="../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../lib/js/table/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../inc/code.css" media="all" />
    <script type="text/javascript" src="../inc/code.js"></script>

    <!-- <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" /> -->
    <link rel="stylesheet" type="text/css" href="css/reading_book.css" />

    <style>
    .buttons{
        text-align: center;
    }

    </style>
</Head>

<Body>
        <!-- 內容區塊 開始 -->
    <div id="content">
            <div class="form_div">
                <h1>書籍表單</h1>
            </div>
            <div class="buttons">
                
                
                <input type="button" id="level_6" value="等級大於6書表">
                <input type="button" id="level_0" value="等級小於6書表">
                <input type="button" id="read_again" value="需重看書籍表">
                <input type="button" id="read_again" value="工讀生工作表">
                <input type="button" id="read_again" value="已閱表">
                <input type="button" id="topic_tag" value="書本主題表">
                <input type="button" id="topic_tag" value="書本標籤表">
                <input type="button" id="pages" value="書本頁數表" >

            </div>
            <div id="level_over6_div">
                <table id="level_over6_table" border="1" cellpadding="5" style="border:2px #FFB326 solid;text-align:center;margin: 20px auto;">
                            <tr>
                                <td>已閱</td>
                                <td>書名</td> 
                                <td>isbn10碼</td>
                                <td>isbn13碼</td>
                                <td>圖書館編號</td>
                                <td>實驗班貼紙顏色</td>
                                <td>實驗班貼紙編號</td>
                                <td>難度等級</td>
                                <td>等級平均</td>
                                <td>學生姓名</td>
                                <td>需要重看</td>
                                
                            </tr>   
                       
               
                       
                </table>
            
            </div>
       

</div>
</Body>
<script type="text/javascript">

//點擊上方按鈕表單切換
// $( "#level" ).click(function() {
//         $( "#level_table" ).css("display", "block");
//         $( "#topic_tag_table" ).css("display", "none");
//         $( "#pages_table" ).css("display", "none");
//         $( "#read_again_table" ).css("display", "none");

// });
// $( "#topic_tag" ).click(function() {
//         $( "#level_table" ).css("display", "none");
//         $( "#topic_tag_table" ).css("display", "block");
//         $( "#pages_table" ).css("display", "none");
//         $( "#read_again_table" ).css("display", "none");
        

// });
// $( "#pages" ).click(function() {
//         $( "#level_table" ).css("display", "none");
//         $( "#topic_tag_table" ).css("display", "none");
//         $( "#pages_table" ).css("display", "block");
//         $( "#read_again_table" ).css("display", "none");

// });
// $( "#read_again" ).click(function() {
//         $( "#level_table" ).css("display", "none");
//         $( "#topic_tag_table" ).css("display", "none");
//         $( "#pages_table" ).css("display", "none");
//         $( "#read_again_table" ).css("display", "block");
        

// });


//--------------
///畫面load進///
//-------------

function main(){


        var url = "./ajax/get_reading_log_info.php";
        var dataVal = {
            user_id:              <?php echo $sess_login_info['uid']?>,
            permission:           <?php echo $sess_login_info['permission']?>,
            school_code:          "<?php echo $sess_login_info['school_code']?>",
            responsibilities:     <?php echo $sess_responsibilities?>,
            class_code:           "<?php echo $sess_class_code?>",
            grade:                "<?php echo $sess_grade?>",
            classroom:            "<?php echo $sess_classroom?>"};
                   

        $.ajax({
                   url: url,
                   type: "POST",
                   datatype: "json",
                   data: dataVal,
                   // contentType: "application/json; charset=utf-8",
                   async: false,
                   success: function(data) {
  
                    data_array = JSON.parse(data);
                    console.log(data_array);

                    for(var key in data_array){

                        var book_name=data_array[key]['book_name'];
                        var book_isbn_13=data_array[key]['book_isbn_13'];
                        var book_isbn_10=data_array[key]['book_isbn_10'];
                        var book_library_code=data_array[key]['book_library_code'];
                        var book_language=data_array[key]['book_language'];
                        for(var index_key in book_language){

                            console.log(book_language);
                            var language=book_language[index_key]['book_language'];
                    

                        }
                        var book_bopomofo==data_array[key]['book_bopomofo'];
                        for(var index_key in book_bopomofo ){
                            
                        }
                     


                    }
                   
                   

                   },
                   error: function(jqXHR) {
                   alert("發生錯誤: " + jqXHR.status);
                  }

        });


} 
main();




//978986317693
</script>
</Html>