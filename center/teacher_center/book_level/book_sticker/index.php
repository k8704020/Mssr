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
    $sess_user_id=$_SESSION['user_id'];
    $sess_permission=$_SESSION['permission'];
    $sess_name=$_SESSION['name'];
    

    if(!isset($sess_user_id)&&!isset($sess_permission)&&!isset($sess_name)){


        echo '<span style="font-size:40px; color:red;">請先登入!!</span>';

        header('Location:http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/index.php');


        die();


        
     }

     if($sess_permission==="1"){

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

    

        //---------------------------------------------------
        //SQL 筆數查詢
        //---------------------------------------------------


    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------


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
        /* 容器微調 */
   /*      #container, #content, #teacher_datalist_tbl{
            width:760px;
        } */
    </style>
</Head>

<Body>
<form action="ajax/insert_book_sticker.php" method="get">
        <!-- 內容區塊 開始 -->
    <div id="content">
            <div>
                <?php
                    require_once('../user/index.php');
                    if($sess_permission==="3"){require_once('../user/super_use_index.php');};
                ?>
            </div>
            <div class="form_div">
                <h1>登記貼紙表單</h1>
            </div>
            <div id="about_qa">
                    <div class="qa_div" id="isbn_input" >
                        <div class="title_div">
                            <span>1.請填此書的ISBN或圖書館編號:</span><span class="star" >*</span>
                        </div>
                        <div class="options_div" id="isbn_option">
                            <input type="text" name="isbn" id="isbn"  onblur="find_book()">
                        </div>
                    </div>

                    <div class="qa_div" id="color">
                        <div class="title_div">
                            <span>2.請先選擇貼紙顏色:</span>
                        </div>
                        <select id="color_select" name="color" ">
                           
                        </select>
                    </div>
                    <div class="qa_div" id="number_input" >
                        <div class="title_div">
                            <span>3.請填此貼紙編號:</span><span class="star"  >*</span>
                        </div>
                        <div class="options_div" id="sticker_number">
                            <input type="text" name="number" id="number" >
                            <a style="cursor: pointer;" onclick="plus()"><img src="img/plus.png" alt="Girl in a jacket"></a>
                        </div>
                    </div>
                    

   
            </div>
        
            <input type="submit" name="送出" value="送出" id="submit" onclick="check_all_value();">
        </div>
         <!-- 內容區塊 開始 -->
    </div>
    <!-- 快速切換區塊 開始 -->
</form>
</Body>
<script type="text/javascript">
function plus(){    
     var number_val=$("#number").val();
     var number_val_change= parseInt(number_val) + 1
     console.log(number_val_change); 
     var number=$("#number").val(number_val_change);
       
  

}


function find_book(){

    var isbn_val=$("#isbn").val();
    if(isbn_val){

            var url = "./ajax/get_book_sticker_info.php";
            var dataVal = {
                

                book_isbn:            $('#isbn').val()
            };
                       

            $.ajax({
                       url: url,
                       type: "POST",
                       datatype: "json",
                       data: dataVal,
                       // contentType: "application/json; charset=utf-8",
                       async: false,
                       success: function(data) {
                        console.log(data);
                        data_array = JSON.parse(data);
                        if(data!="[]"){

                                var book_sid= data_array[0]['book_sid'];
                                var book_isbn_10=data_array[0]['book_isbn_10'];
                                var book_isbn_13=data_array[0]['book_isbn_13'];
                                var book_name=data_array[0]['book_name'];
                                var book_library_code=data_array[0]['book_library_code'];
                                
                             

                                    if(book_isbn_10==="")book_isbn_10=0;
                                    if(book_isbn_13==="")book_isbn_13=0;
                                    if(book_name==="")book_name=0;
                                    if(book_library_code==="")book_library_code=0;
                             
                                    $('#isbn_option').append("<span id='ps'> 書名:"+book_name+"</span>");
                                    $('#isbn_option').append('<input type="hidden" id="book_sid"  name="book_sid" value="'+book_sid+'">');
                                    $('#isbn_option').append('<input type="hidden" id="book_isbn_10" name="book_isbn_10" value="'+book_isbn_10+' ">');
                                    $('#isbn_option').append('<input type="hidden" id="book_isbn_13" name="book_isbn_13" value="'+book_isbn_13+' ">');
                                    $('#isbn_option').append('<input type="hidden" id="book_name" name="book_name" value="'+book_name+' ">');
                                    $('#isbn_option').append('<input type="hidden" id="book_library_code" name="book_library_code" value="'+book_library_code+'">');
   
                                
                                
                        }else{
                            alert("沒有此isbn的書籍,請先登記書籍");
                            


                        }         
                       
                       },
                       error: function(jqXHR) {
                        alert("發生錯誤: " + jqXHR.status);
                      }

            });

    }
}



function check_all_value(status){

        var isbn_val=$("#isbn").val();
        if(isbn_val===""){
            alert("請填第1題此書的ISBN");
        }


        var number_val=$("#number").val();
        var color_val=$('select[name=color]').val();
        if (typeof(Storage) !== "undefined") {
                localStorage.setItem("number", number_val);
                localStorage.setItem("color", color_val);

        } else{
                console.log("sorry");
        }

        
}

function main(){

    var url = "ajax/get_sticker_info.php";
            var dataVal = {
                

            };
                       

            $.ajax({
                       url: url,
                       type: "POST",
                       datatype: "json",
                       data: dataVal,
                       // contentType: "application/json; charset=utf-8",
                       async: false,
                       success: function(data) {
                            console.log(data);
                            data_array = JSON.parse(data);
                            if(data!="[]"){

                                for(key in data_array){
                                    var color_id= data_array[key]['color_id'];
                                    var color=data_array[key]['color'];
                                    var select = document.getElementById('color_select');
                                    var opt = document.createElement('option');
                                    opt.value = color_id;
                                    opt.innerHTML = color;
                                    select.appendChild(opt);
                                    
                                } 

                            }       
                       
                       },
                       error: function(jqXHR) {
                        alert("發生錯誤: " + jqXHR.status);
                      }

            });

         
           document.getElementById("number").value = localStorage.getItem("number");
           document.getElementById("color_select").selectedIndex = localStorage.getItem("color")-1;

}

main();
</script>
</Html>
