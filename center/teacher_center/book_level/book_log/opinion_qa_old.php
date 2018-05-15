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

        if($config_arrys['is_offline']['center']['teacher_center']){
            $url=str_repeat("../",5).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        $arrys_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
        if(empty($arrys_login_info)){
            die();
        }

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------


    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        $sess_login_info=(isset($_SESSION['tc']['t|dt']))?$_SESSION['tc']['t|dt']:array();
        unset($_SESSION['tc']['t|dt']['add_book_tip']);

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

      
    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="";

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
        <!-- 內容區塊 開始 -->
    <div id="content">
            <div class="form_div">
                <h1>登記書籍表單</h1>
            </div>
            <div id="about_qa">
         
                    <div class="qa_div" id="isbn_input" >
                            <div class="title_div">
                                <span>1.請填此書的ISBN或圖書館編號:</span><span class="star"  >*</span>
                            </div>
                            <div class="options_div" id="isbn_option">
                                <input type="text" name="isbn" id="isbn" onblur="find_book()">
                            </div>
                    </div>


                    
                    <div class="qa_div">
                            <div class="title_div">
                                <span>2.此本書為</span><span class="star">*</span>
                            </div>
                            <div class="language_options">
                                <input type="radio" id="ch_book" name="language" value="1" > <label for="ch_book">中文書</label>
                                <input type="radio" id="eng_book" name="language" value="2" > <label for="eng_book">英文書</label>
                                <input type="radio" id="ch_eng_book" name="language" value="3" > <label for="ch_eng_book">中英文混和書</label>
                            </div>
                    </div>
                    <div class="qa_div">
                            <div class="title_div">
                                <span>3.是否有注音</span><span class="star">*</span>
                            </div>
                             <div class="bopomofo_options ">
                                <input type="radio"  name="bopomofo" id="no" value="0" ><label for="no">無</label>
                                <input type="radio"  name="bopomofo" id="yes" value="1" > <label for="yes">有</label>
                            </div>
                    </div>
                    <div class="qa_div" id="major_topic">
                        <div class="title_div">
                            <span>4.請選擇適合此書的大主題</span><span class="star">*</span>
                        </div>
                        <div class="major_topic_options">
                            <input type="checkbox" class="major_topic_ckb" name="life" value="1"> <label for="life">生活</label>
                            <input type="checkbox" class="major_topic_ckb" name="science" value="2"> <label for="science">科學</label>  
                            <input type="checkbox" class="major_topic_ckb" name="history" value="3"> <label for="history">史地</label>
                            <input type="checkbox" class="major_topic_ckb" name="literature" value="4"> <label for="literature">文學</label> 
                            <input type="checkbox" class="major_topic_ckb" name="art" value="5"> <label for="art">藝術</label>     
                            <input type="checkbox" class="major_topic_ckb" id="other1" name="other1" value="6"> <label for="other1">其他</label><input type="text" name="other1" id="other_input1"><br>
                        </div>
                    </div>
                    <div class="qa_div" id="sub_topic">
                        <div class="title_div">  
                            <span>5.請選擇適合此書的中主題</span><span class="star">*</span>
                        </div>
                        <div class="sub_topic_options">
                            <input type="checkbox" id="character" name="character" value="1"> <label for="character">品格</label>
                            <input type="checkbox" id="health" name="health" value="2"><label for="health"> 健康</label>
                            <input type="checkbox" id="financial" name="financial" value="3"><label for="financial">理財</label>          
                            <input type="checkbox" id="energy" name="energy" value="4"> <label for="energy">能源</label>
                            <input type="checkbox" id="technology" name="technology" value="5"> <label for="technology">科技</label>
                            <input type="checkbox" id="mathematics" name="mathematics" value="6"> <label for="mathematics">數學</label>       
                            <input type="checkbox" id="biological" name="biological" value="7"> <label for="biological">生物</label>   
                            <input type="checkbox" id="earth_science" name="earth_science" value="8"> <label for="earth_science">地科</label>
                            <input type="checkbox" id="geography" name="geography" value="9"> <label for="geography">地理</label>   
                            <input type="checkbox" id="history" name="energy" value="10"> <label for="energy">歷史</label>   
                            <input type="checkbox" id="proverb" name="proverb" value="11"> <label for="proverb">成語</label>
                            <input type="checkbox" id="chinese_studies" name="chinese_studies" value="12"> <label for="chinese_studies">國學</label> 
                            <input type="checkbox" id="novel" name="novel" value="13"><label for="novel">小說</label>
                            <input type="checkbox" id="prose" name="prose" value="14"> <label for="prose">散文</label>
                            <input type="checkbox" id="poetry" name="poetry" value="15"> <label for="poetry">詩賦</label>
                            <input type="checkbox" id="visual_arts" name="visual_arts" value="16"> <label for="visual_arts">視覺藝術</label>
                            <input type="checkbox" id="music" name="music" value="17"> <label for="music">音樂</label>
                            <input type="checkbox" id="performing_arts" name="performing_arts" value="18"> <label for="performing_arts">表演藝術</label>
                            <input type="checkbox" id="fairy_tales" name="fairy_tales" value="19"> <label for="fairy_tales"> 法律</label>
                            <input type="checkbox" id="other2" name="other2" value="20"><label for="other2">其他 </label><input type="text" name="other_input2" id="other_input2"br>
                        </div>
                    </div>
                    <div class="qa_div" id="minor_topic">
                            <div class="title_div"> 
                                <span>6.請選擇適合此書的小主題 </span><span class="star">*</span><br>
                            </div>
                            <div class="minor_topic_options">
                                <input type="checkbox" id="physiology" name="physiology" value="1"><label for="physiology"> 生理 </label>
                                <input type="checkbox" id="psychology" name="psychology" value="2"><label for="psychology">  心理 </label>
                                <input type="checkbox" id="sports" name="sports" value="3"> <label for="sports">體育</label>          
                                <input type="checkbox" id="chemistry" name="chemistry" value="4"> <label for="chemistry">理化</label>
                                <input type="checkbox" id="computer_technology" name="computer_technology" value="5" ><label for="computer_technology"> 電腦科技</label>
                                <input type="checkbox" id="airplane" name="airplane" value="6" ><label for="airplane"> 航太科技</label>       
                                <input type="checkbox" id="animal" name="animal" value="7" ><label for="animal">動物 </label>   
                                <input type="checkbox" id="plant" name="plant" value="8" ><label for="plant"> 植物 </label>    
                                <input type="checkbox" id="astronomy" name="astronomy" value="9" ><label for="astronomy"> 天文 </label>  
                                <input type="checkbox" id="atmospheric_science" name="atmospheric_science" value="10" ><label for="atmospheric_science"> 大氣科學</label>    
                                <input type="checkbox" id="oceanography" name="oceanography" value="11" ><label for="oceanography">海洋學</label>
                                <input type="checkbox" id="world" name="world" value="12 " > <label for="world"> 世界</label>    
                                <input type="checkbox" id="taiwan" name="taiwan" value="13" > <label for="taiwan"> 台灣 </label>    
                                <input type="checkbox" id="ancient_history" name="ancient_history" value="14" > <label for="ancient_history">遠古史</label>
                                <input type="checkbox" id="modern_history" name="modern_history" value="15" ><label for="modern_history">  近代史</label>
                                <input type="checkbox" id="myth" name="myth" value="16" > <label for="myth"> 神話</label>
                                <input type="checkbox" id="biography" name="biography" value="17" > <label for="biography"> 傳記</label>
                                <input type="checkbox" id="fairy_tales" name="fairy_tales" value="18" > <label for="fairy_tales"> 童話</label>
                                <input type="checkbox" id="fable" name="fable" value="19" > <label for="fable"> 寓言</label>
                                <input type="checkbox" id="calligraphy" name="calligraphy" value="20" > <label for="calligraphy"> 書法</label>
                                <input type="checkbox" id="painting" name="painting" value="21" > <label for="painting"> 繪畫</label>
                                <input type="checkbox" id="photography" name="photography" value="22" > <label for="photography"> 攝影</label>
                                <input type="checkbox" id="dance" name="dance" value="23" > <label for="dance">舞蹈</label>
                                <input type="checkbox" id="drama" name="drama" value="24" > <label for="drama">戲劇</label>
                                <input type="checkbox" id="other3" name="other3" value="25" ><label for="other3">其他 </label><input type="text" name="other_input3" id="other_input3">
                            </div>
                    </div>
                    <div class="qa_div">
                            <div class="title_div"> 
                                <span>7.請填此書相關的標籤一: </span><span class="star">*</span>
                            </div>
                            <div class="options_div">
                                <input type="text" name="tag1" class="input_val" id="tag1">
                            </div>
                    </div>
                    <div class="qa_div">
                            <div class="title_div">   
                                <span>8.請填此書相關的標籤二: </span><span class="star">*</span>
                            </div>
                            <div class="options_div">
                                <input type="text" name="tag2" class="input_val" id="tag2">
                            </div>
                    </div>
                    <div class="qa_div "> 
                            <div class="title_div">  
                                <span>9.請填此書相關的標籤三: </span><span class="star">*</span>
                            </div>
                            <div class="options_div">
                                <input type="text" name="tag3" class="input_val" id="tag3">
                            </div>
                    </div>
                    <div class="qa_div">
                            <div class="title_div">
                                <span>10.請填此書相關的標籤四: </span>
                            </div>
                            <div class="options_div">
                                <input type="text" name="tag4" class="input_val" id="tag4">
                            </div>
                    </div>
                    <div class="qa_div">
                            <div class="title_div">
                                <span>11.請填此書相關的標籤五: </span>
                            </div>
                            <div class="options_div">
                                <input type="text" name="tag5" class="input_val" id="tag5">
                            </div>
                    </div>
                    <div class="qa_div">
                            <div class="title_div">   
                                <span>12.請填此書的頁數: </span><span class="star">*</span>
                            </div>
                            <div class="options_div">
                                <input type="text" name="pages" class="input_val" id="pages">
                            </div>
                    </div>
                    <div class="qa_div"> 
                            <div class="title_div">   
                                <span>13.請填此書的字數(必填): </span><span class="star">*</span>
                            </div>
                             <div class="options_div">
                                <input type="text" name="words" class="input_val"  id="words" >
                            </div>
                    </div>
                    <div class="qa_div">
                            <div class="options_div">
                                <span>14.請填此書的難度等級(填A-Z) (也可以填G-H)(必填): </span><span class="star">*</span>
                            </div>
                            <div>
                                 <select id="level_one" name="level_one" ">
                                        <option value="0">請選擇</option>
                                        <option value="1">A</option>
                                        <option value="2">B</option>
                                        <option value="3">C</option>
                                        <option value="4">D</option>
                                        <option value="5">E</option>
                                        <option value="6">F</option>
                                        <option value="7">G</option>
                                        <option value="8">H</option>
                                        <option value="9">I</option>
                                        <option value="10">J</option>
                                        <option value="11">K</option>
                                        <option value="12">L</option>
                                        <option value="13">M</option>
                                        <option value="14">N</option>
                                        <option value="15">O</option>
                                        <option value="16">P</option>
                                        <option value="17">Q</option>
                                        <option value="18">R</option>
                                        <option value="19">S</option>
                                        <option value="20">T</option>
                                        <option value="21">U</option>
                                        <option value="22">V</option>
                                        <option value="23">W</option>
                                        <option value="24">X</option>
                                        <option value="25">Y</option>
                                        <option value="26">Z</option>
                                 </select>
                                 <span>-</span>
                                <select id="level_two" name="level_two">
                                        <option value="0">請選擇</option>
                                        <option value="1">A</option>
                                        <option value="2">B</option>
                                        <option value="3">C</option>
                                        <option value="4">D</option>
                                        <option value="5">E</option>
                                        <option value="6">F</option>
                                        <option value="7">G</option>
                                        <option value="8">H</option>
                                        <option value="9">I</option>
                                        <option value="10">J</option>
                                        <option value="11">K</option>
                                        <option value="12">L</option>
                                        <option value="13">M</option>
                                        <option value="14">N</option>
                                        <option value="15">O</option>
                                        <option value="16">P</option>
                                        <option value="17">Q</option>
                                        <option value="18">R</option>
                                        <option value="19">S</option>
                                        <option value="20">T</option>
                                        <option value="21">U</option>
                                        <option value="22">V</option>
                                        <option value="23">W</option>
                                        <option value="24">X</option>
                                        <option value="25">Y</option>
                                        <option value="26">Z</option>
                                 </select>
                            </div>
                    </div>
                    <div class="qa_div">
                            <div class="options_div">
                                <span>15.英文書的LEXILE分數（含AD, BR....）查不到寫0，中文書也寫0 (https://www.lexile.com)(必填) </span><span class="star">*</span><br>
                            </div>
                            <div>
                                <input type="text" name="eng_level" class="input_val" id="eng_level">
                            </div>
                    </div> 
            </div>
        
            <input type="submit" name="送出" value="送出" id="submit" onclick="check_all_value();">
        </div>
         <!-- 內容區塊 開始 -->
    </div>
    <!-- 快速切換區塊 開始 -->
</Body>
<script type="text/javascript">

    

//從資料庫尋找書籍顯示書籍資料
function find_book(){

    var isbn_val=$("#isbn").val();
    console.log(isbn_val);

    if(isbn_val){

            var url = "./ajax/get_book_info.php";
            var dataVal = {
                
                user_id:              <?php echo $sess_login_info['uid']?>,
                permission:           <?php echo $sess_login_info['permission']?>,
                school_code:          "<?php echo $sess_login_info['school_code']?>",
                responsibilities:     <?php echo $sess_responsibilities?>,
                class_code:           "<?php echo $sess_class_code?>",
                grade:                "<?php echo $sess_grade?>",
                classroom:            "<?php echo $sess_classroom?>",
                book_isbn:            isbn_val
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
                        console.log(data_array);
                        if(data!="[]"){

                                var book_sid= data_array[0]['book_sid'];
                                var book_isbn_10=data_array[0]['book_isbn_10'];
                                var book_isbn_13=data_array[0]['book_isbn_13'];
                                var book_name=data_array[0]['book_name'];
                                var book_library_code=data_array[0]['book_library_code'];
                                var have_log=data_array[0]['have_log'];
                                var can_log=data_array[0]['can_log'];

                                if(book_isbn_10==="")book_isbn_10=0;
                                if(book_isbn_13==="")book_isbn_13=0;
                                if(book_name==="")book_name=0;
                                if(book_library_code==="")book_library_code=0;
                                if(have_log==="yes" && can_log==="no"){
                                    
                                    $('#isbn_option').append("<span id='ps' style='color:red;'>此書你登記過了,請換書本登記</span>");

                                    $("#isbn").on("focus", function() {
                                        $("#isbn").val("");
                                        $("#ps").remove();
                                    });


                                                                
                                }else if(have_log==="no" && can_log==="no"){
                                    
                                    $('#isbn_option').append("<span id='ps' style='color:red;'>此書已被登記兩次了,請換書本登記</span>");
                                    $("#isbn").on("focus", function() {
                                        $("#isbn").val("");
                                        $("#ps").remove();
                                    });
                                   
                                }
                                else if(have_log==="yes" &&can_log==="yes"){
                                 
                                    $('#isbn_option').append("<span id='ps' style='color:red;'>系統異常,請告知工程師</span>");
                                    
                                }else{
                                   
                                    $('#isbn_option').append("<span id='ps'> 書名:"+book_name+"</span>");
                                    
                                    $('#isbn_option').append('<input type="hidden" id="book_sid"  name="book_sid" value="'+book_sid+'">');
                                    $('#isbn_option').append('<input type="hidden" id="book_isbn_10" name="book_isbn_10" value="'+book_isbn_10+' ">');
                                    $('#isbn_option').append('<input type="hidden" id="book_isbn_13" name="book_isbn_13" value="'+book_isbn_13+' ">');
                                    $('#isbn_option').append('<input type="hidden" id="book_name" name="book_name" value="'+book_name+' ">');
                                    $('#isbn_option').append('<input type="hidden" id="book_library_code" name="book_library_code" value="'+book_library_code+'">');
                                }
                               

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

//確認那些值沒有填寫

function check_all_value(){



        var isbn_val=$("#isbn").val();
        var txt=$("#ps").text();
        var language=$('input[name=language]:checked').val();

        var bopomofo=$('input[name=bopomofo]:checked').val();

        
        var major_topic_val = [];
        var sub_topic_val=[];
        var minor_topic_val=[];

        $('.major_topic_options :checkbox:checked').each(function(i){
            major_topic_val[i] = $(this).val();
                      
        });
        $('.sub_topic_options :checkbox:checked').each(function(i){
            sub_topic_val[i] = $(this).val();
                      
        });
        $('.minor_topic_options :checkbox:checked').each(function(i){
            minor_topic_val[i] = $(this).val();
                      
        });

        var tag1=$("#tag1").val();
        var tag2=$("#tag2").val();
        var tag3=$("#tag3").val();
        var pages=$("#pages").val();
        var words=$("#words").val();
        var eng_level=$("#eng_level").val();
       
        var level_one_val=$('select[name=level_one]').val();
        var level_two_val=$('select[name=level_two]').val();
        if(level_two_val>level_one_val){
            var level_val=level_two_val-level_one_val;
        }else if(level_two_val<level_one_val){
            var level_val=level_one_val-level_two_val;
        }else{
            var level_val=level_one_val-level_two_val;
        }

        var other_input1=$("#other_input1").val();
        var other_input2=$("#other_input2").val();
        var other_input3=$("#other_input3").val();

        console.log(other_input1);
        console.log(other_input2);
        console.log(other_input3);


        if(isbn_val===""){
            alert("請填第1題此書的ISBN");
         
            
        }else if(txt==="此書你登記過了,請換書本登記"){
            alert("此書你登記過了,請換書本登記");
          
        }else if(txt==="此書已被登記兩次了,請換書本登記"){

            alert("此書已被登記兩次了,請換書本登記");
          

        }else if(language===undefined){
            alert("請填第2題填此書的語言");
           
            
        }else if(bopomofo===undefined){
            alert("請填第3題填此書有無注音");
           
            
        }else if(major_topic_val.length ===0){

            alert("請勾選第4題大主題");
            
           
        }else if ($('#other1').is(':checked')&&other_input1==="") {

            alert("請填寫第4題其他主題的內容");
        

        }else if(sub_topic_val.length ===0){
            alert("請勾選第5題中主題");
            
        }else if ($('#other2').is(':checked')&&other_input2==="") {

            alert("請填寫第5題其他主題的內容");

        }else if(minor_topic_val.length ===0){
            alert("請勾選第6題小主題");
                        
        }else if ($('#other3').is(':checked')&&other_input3==="") {

            alert("請填寫第6題其他主題的內容");
         
        }else if(tag1===""){
             alert("請填第7題填此書相關的標籤一");
             
             
        }else if(tag2===""){
             alert("請填第8題填此書相關的標籤二");
            
             
        }else if(tag3===""){
            alert("請填第9題填此書相關的標籤三");
           
            
        }else if(pages===""){
            alert("請填第12題填此書的頁數");
     
            
        }else if(words===""){
            alert("請填第13題填此書的字數");
          
            
        }else if(level_one_val==="0"){
            alert("請填第14題填此書的難度");

        }else if(level_two_val==="0"){
             
             alert("請填第14題填此書的難度");

        }else if(level_val>1){

            alert("請檢查第14題此書的難度等級");

        }else if(eng_level===""){
            alert("請填第15題填此英文書LEXILE分數");

        }else{

            level_info();
        }

    }


    //資料輸入至資料庫
    function level_info(){

        var book_sid=$("#book_sid").val();
        var book_isbn_10=$("#book_isbn_10").val();
        var book_isbn_13=$("#book_isbn_13").val();
        var book_library_code=$("#book_library_code").val();
        var language=$('input[name=language]:checked').val();
        var bopomofo=$('input[name=bopomofo]:checked').val();
        var major_topic_val = [];
        var sub_topic_val=[];
        var minor_topic_val=[];

        $('.major_topic_options :checkbox:checked').each(function(i){
            major_topic_val[i] = $(this).val();        
        });
        $('.sub_topic_options :checkbox:checked').each(function(i){
            sub_topic_val[i] = $(this).val();           
        });
        $('.minor_topic_options :checkbox:checked').each(function(i){
            minor_topic_val[i] = $(this).val();                  
        });
        var other_input1=$("#other_input1").val();
        var other_input2=$("#other_input2").val();
        var other_input3=$("#other_input3").val();

        console.log(other_input1);
        console.log(other_input2);
        console.log(other_input3);
        var tag1=$("#tag1").val();
        var tag2=$("#tag2").val();
        var tag3=$("#tag3").val();
        var tag4=$("#tag4").val();
        var tag5=$("#tag5").val();
        var pages=$("#pages").val();
        var words=$("#words").val();
        var level_one_val=$('select[name=level_one]').val();
        var level_two_val=$('select[name=level_two]').val();
        var eng_level=$('#eng_level').val();


      var url = "./ajax/insert_book_level.php";
      var dataVal = {
            user_id:              <?php echo $sess_login_info['uid']?>,
            permission:           <?php echo $sess_login_info['permission']?>,
            school_code:          "<?php echo $sess_login_info['school_code']?>",
            responsibilities:     <?php echo $sess_responsibilities?>,
            class_code:           "<?php echo $sess_class_code?>",
            grade:                "<?php echo $sess_grade?>",
            classroom:            "<?php echo $sess_classroom?>",
            book_sid:             book_sid,
            book_isbn_10:         book_isbn_10,
            book_isbn_13:         book_isbn_13,
            book_library_code:    book_library_code,
            language:             language,
            bopomofo:             bopomofo,
            major_topic:          major_topic_val,
            sub_topic:            sub_topic_val,
            minor_topic:          minor_topic_val,
            other_input1:         other_input1,
            other_input2:         other_input2,
            other_input3:         other_input3,
            tag1:                 tag1,
            tag2:                 tag2,
            tag3:                 tag3,
            tag4:                 tag4,
            tag5:                 tag5,
            pages:                pages,
            words:                words,
            level_one_val:        level_one_val,
            level_two_val:        level_two_val,
            eng_level:            eng_level,

        };

        $.ajax({
                   url: url,
                   type: "POST",
                   datatype: "json",
                   data: dataVal,
                   // contentType: "application/json; charset=utf-8",
                   async: false,  
                   success: function(data) {
                    window.location.href="finish.php";
                   },
                   error: function(jqXHR) {
                    alert("發生錯誤: " + jqXHR.status);
                  }

        });





    }


//978986317693
</script>
</Html>