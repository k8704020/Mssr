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
        $title="書籍表單";

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
/*    *{
        font-size: 13px;
        font-family: 微軟正黑體;
    }*/
    #about_book{
        font-family: 微軟正黑體;
        font-size: 13px;
    }
    .buttons{
        text-align: center;
        margin: 20px auto;
    }



</style>
</Head>

<Body>
        <!-- 內容區塊 開始 -->
    <div id="about_book">
            <div>
                <?php
                    require_once('../user/index.php');
                    if($sess_permission==="3"){require_once('../user/super_use_index.php');};
                ?>
            </div>
            <div class="about_title_div">
                <h1 id="title">書籍相關總覽</h1>
            </div>
            <div class="buttons">
                
                 <a href="administrator_level.php"><input type="button" id="pages" value="管理員等級表"></a>
                 <!-- <a href="need_read_again.php"><input type="button" id="read_again" value="需重看書籍表"></a> -->
                 <a href="pt_work.php"><input type="button" id="pt" value="工讀生工作表" ></a>
                 <!-- <a href="read.php"><input type="button" id="read" value="已閱表"></a> -->
                 <a href="topic.php"><input type="button" id="topic" value="書本主題標籤表"></a>
                <!--  <a href="more_six.php"><input type="button" id="level_6" value="等級大於6書表"></a>
                 <a href="less_six.php"><input type="button" id="level_0" value="等級小於6書表"></a> -->

            </div>
            
            
    </div>
            

</Body>
<script type="text/javascript">


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
        var dataVal = {

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